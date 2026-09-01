<?php

namespace App\Http\Controllers\Kabid;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LeaveApprovalController extends Controller
{
    /**
     * Daftar pengajuan milik anggota satu bidang.
     */
    public function index(Request $request): View
    {
        /** @var User $kabid */
        $kabid = $request->user();

        $this->ensureKabidReady(
            $kabid
        );

        $search = trim(
            (string) $request->input(
                'search',
                ''
            )
        );

        $kabidStatus = $request->input(
            'kabid_status'
        );

        $baseQuery = LeaveRequest::query()
            ->whereHas(
                'user',
                function ($query) use ($kabid) {
                    $query
                        ->where(
                            'role',
                            'karyawan'
                        )
                        ->where(
                            'approval_status',
                            'approved'
                        )
                        ->where(
                            'department_id',
                            $kabid->department_id
                        );
                }
            );

        /*
        |--------------------------------------------------------------------------
        | Statistik tahap Kabid
        |--------------------------------------------------------------------------
        */
        $pendingCount = (clone $baseQuery)
            ->where(
                'status',
                'pending'
            )
            ->where(
                'kabid_status',
                LeaveRequest::KABID_STATUS_PENDING
            )
            ->count();

        $approvedCount = (clone $baseQuery)
            ->where(
                'kabid_status',
                LeaveRequest::KABID_STATUS_APPROVED
            )
            ->count();

        $rejectedCount = (clone $baseQuery)
            ->where(
                'kabid_status',
                LeaveRequest::KABID_STATUS_REJECTED
            )
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Daftar pengajuan
        |--------------------------------------------------------------------------
        */
        $leaveRequests = (clone $baseQuery)
            ->with([
                'user.department',
                'permissionType',
                'kabidReviewer',
                'approver',
            ])
            ->when(
                in_array(
                    $kabidStatus,
                    [
                        LeaveRequest::KABID_STATUS_PENDING,
                        LeaveRequest::KABID_STATUS_APPROVED,
                        LeaveRequest::KABID_STATUS_REJECTED,
                    ],
                    true
                ),
                fn($query) =>
                $query->where(
                    'kabid_status',
                    $kabidStatus
                )
            )
            ->when(
                $search !== '',
                function ($query) use ($search) {
                    $query->where(
                        function ($query) use ($search) {
                            $query
                                ->whereHas(
                                    'user',
                                    function ($userQuery) use ($search) {
                                        $userQuery
                                            ->where(
                                                'name',
                                                'like',
                                                '%' . $search . '%'
                                            )
                                            ->orWhere(
                                                'nik',
                                                'like',
                                                '%' . $search . '%'
                                            )
                                            ->orWhere(
                                                'email',
                                                'like',
                                                '%' . $search . '%'
                                            );
                                    }
                                )
                                ->orWhereHas(
                                    'permissionType',
                                    fn($typeQuery) =>
                                    $typeQuery->where(
                                        'name',
                                        'like',
                                        '%' . $search . '%'
                                    )
                                )
                                ->orWhere(
                                    'reason',
                                    'like',
                                    '%' . $search . '%'
                                );
                        }
                    );
                }
            )
            /*
             * Yang menunggu Kabid diletakkan paling atas.
             */
            ->orderByRaw(
                "CASE WHEN kabid_status = 'pending' AND status = 'pending' THEN 0 ELSE 1 END"
            )
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view(
            'kabid.leave-approvals.index',
            compact(
                'kabid',
                'leaveRequests',
                'pendingCount',
                'approvedCount',
                'rejectedCount',
                'search',
                'kabidStatus'
            )
        );
    }


    /**
     * Detail pengajuan anggota.
     */
    public function show(
        Request $request,
        LeaveRequest $leaveRequest
    ): View {
        /** @var User $kabid */
        $kabid = $request->user();

        $this->ensureKabidReady(
            $kabid
        );

        $this->ensureDepartmentRequest(
            $kabid,
            $leaveRequest
        );

        $leaveRequest->load([
            'user.department',
            'permissionType',
            'leaveBalance',
            'substituteSchedules.workShift',
            'kabidReviewer',
            'approver',
        ]);

        return view(
            'kabid.leave-approvals.show',
            compact(
                'kabid',
                'leaveRequest'
            )
        );
    }


    /**
     * ACC tahap Kabid.
     *
     * Penting:
     * status final TIDAK diubah.
     * Pengajuan tetap pending agar dilanjutkan ke Admin.
     */
    public function approve(
        Request $request,
        LeaveRequest $leaveRequest
    ): RedirectResponse {
        /** @var User $kabid */
        $kabid = $request->user();

        $this->ensureKabidReady(
            $kabid
        );

        DB::transaction(
            function () use (
                $kabid,
                $leaveRequest
            ) {
                /** @var LeaveRequest $current */
                $current = LeaveRequest::query()
                    ->with(
                        'user'
                    )
                    ->lockForUpdate()
                    ->findOrFail(
                        $leaveRequest->id
                    );

                $this->ensureDepartmentRequest(
                    $kabid,
                    $current
                );

                $this->ensureCanBeReviewed(
                    $current
                );

                $current->update([
                    /*
                     * Status final tetap pending.
                     * Admin yang membuat keputusan final.
                     */
                    'status' =>
                    'pending',

                    'kabid_status' =>
                    LeaveRequest::KABID_STATUS_APPROVED,

                    'kabid_reviewed_by' =>
                    $kabid->id,

                    'kabid_reviewed_at' =>
                    now(),

                    'kabid_rejection_reason' =>
                    null,
                ]);
            }
        );

        return redirect()
            ->route(
                'kabid.leave-approvals.show',
                $leaveRequest
            )
            ->with(
                'success',
                'Pengajuan berhasil disetujui. Selanjutnya menunggu keputusan final Administrator.'
            );
    }


    /**
     * Tolak pada tahap Kabid.
     *
     * Penolakan Kabid adalah penolakan final.
     * status dibuat rejected agar:
     * - Karyawan langsung melihat Ditolak;
     * - saldo pending tidak lagi ter-reserve;
     * - Admin tidak perlu memproses pengajuan tersebut.
     */
    public function reject(
        Request $request,
        LeaveRequest $leaveRequest
    ): RedirectResponse {
        /** @var User $kabid */
        $kabid = $request->user();

        $this->ensureKabidReady(
            $kabid
        );

        $validated = $request->validate(
            [
                'kabid_rejection_reason' => [
                    'required',
                    'string',
                    'min:5',
                    'max:2000',
                ],
            ],
            [
                'kabid_rejection_reason.required' =>
                'Alasan penolakan wajib diisi.',

                'kabid_rejection_reason.min' =>
                'Alasan penolakan minimal 5 karakter.',

                'kabid_rejection_reason.max' =>
                'Alasan penolakan maksimal 2000 karakter.',
            ]
        );

        DB::transaction(
            function () use (
                $kabid,
                $leaveRequest,
                $validated
            ) {
                /** @var LeaveRequest $current */
                $current = LeaveRequest::query()
                    ->with(
                        'user'
                    )
                    ->lockForUpdate()
                    ->findOrFail(
                        $leaveRequest->id
                    );

                $this->ensureDepartmentRequest(
                    $kabid,
                    $current
                );

                $this->ensureCanBeReviewed(
                    $current
                );

                $reason = trim(
                    $validated['kabid_rejection_reason']
                );

                $current->update([
                    /*
                     * Kabid menolak = pengajuan selesai sebagai rejected.
                     */
                    'status' =>
                    'rejected',

                    'kabid_status' =>
                    LeaveRequest::KABID_STATUS_REJECTED,

                    'kabid_reviewed_by' =>
                    $kabid->id,

                    'kabid_reviewed_at' =>
                    now(),

                    'kabid_rejection_reason' =>
                    $reason,

                    /*
                     * Isi field final lama juga agar halaman riwayat
                     * Karyawan yang sudah ada langsung membaca alasan
                     * penolakan tanpa harus menunggu perubahan view lain.
                     */
                    'rejected_at' =>
                    now(),

                    'rejection_reason' =>
                    $reason,
                ]);
            }
        );

        return redirect()
            ->route(
                'kabid.leave-approvals.show',
                $leaveRequest
            )
            ->with(
                'success',
                'Pengajuan berhasil ditolak dan tidak dilanjutkan ke Administrator.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | SECURITY
    |--------------------------------------------------------------------------
    */

    private function ensureKabidReady(
        User $kabid
    ): void {
        abort_unless(
            $kabid->role === 'kabid'
                && $kabid->approval_status === 'approved'
                && $kabid->is_active,
            403
        );

        if (! $kabid->department_id) {
            abort(
                403,
                'Kabid belum memiliki bidang.'
            );
        }
    }


    /**
     * Mencegah Kabid mengakses pengajuan:
     * - milik bidang lain;
     * - milik Kabid lain;
     * - milik user non-Karyawan;
     * - milik akun yang belum approved.
     */
    private function ensureDepartmentRequest(
        User $kabid,
        LeaveRequest $leaveRequest
    ): void {
        $leaveRequest->loadMissing(
            'user'
        );

        $employee =
            $leaveRequest->user;

        abort_unless(
            $employee
                && $employee->role === 'karyawan'
                && $employee->approval_status === 'approved'
                && (int) $employee->department_id
                === (int) $kabid->department_id,
            403
        );
    }


    /**
     * Hanya request yang masih pending di kedua tahap
     * yang boleh diproses Kabid.
     */
    private function ensureCanBeReviewed(
        LeaveRequest $leaveRequest
    ): void {
        if (
            $leaveRequest->status !== 'pending'
        ) {
            throw ValidationException::withMessages([
                'approval' =>
                'Pengajuan ini sudah selesai diproses.',
            ]);
        }

        if (
            $leaveRequest->kabid_status
            !== LeaveRequest::KABID_STATUS_PENDING
        ) {
            throw ValidationException::withMessages([
                'approval' =>
                'Pengajuan ini sudah diproses oleh Kabid sebelumnya.',
            ]);
        }
    }
}
