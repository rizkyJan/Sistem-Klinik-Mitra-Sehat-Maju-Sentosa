<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LeaveRequestController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

    public function index(Request $request): View
    {
        $this->ensureApprover();

        $workflow = $request->input(
            'workflow'
        );

        $search = trim(
            (string) $request->input(
                'search',
                ''
            )
        );

        /*
        |--------------------------------------------------------------------------
        | Daftar Pengajuan
        |--------------------------------------------------------------------------
        |
        | Admin tetap boleh melihat semua histori.
        | Untuk status pending, urutkan:
        |
        | 1. Siap diproses Admin
        | 2. Masih menunggu Kabid
        |
        */
        $leaveRequests =
            LeaveRequest::query()
            ->with([
                'user.department',
                'permissionType',
                'substituteSchedules.workShift',
                'kabidReviewer',
                'approver',
            ])

            /*
                |--------------------------------------------------------------------------
                | Filter Workflow
                |--------------------------------------------------------------------------
                */
            ->when(
                $workflow === 'ready_admin',
                function ($query) {
                    $query
                        ->where(
                            'status',
                            'pending'
                        )
                        ->whereIn(
                            'kabid_status',
                            [
                                LeaveRequest::KABID_STATUS_APPROVED,
                                LeaveRequest::KABID_STATUS_NOT_REQUIRED,
                            ]
                        );
                }
            )

            ->when(
                $workflow === 'waiting_kabid',
                function ($query) {
                    $query
                        ->where(
                            'status',
                            'pending'
                        )
                        ->where(
                            'kabid_status',
                            LeaveRequest::KABID_STATUS_PENDING
                        )
                        ->whereHas(
                            'user',
                            fn($userQuery) =>
                            $userQuery->where(
                                'role',
                                'karyawan'
                            )
                        );
                }
            )

            ->when(
                $workflow === 'approved',
                fn($query) =>
                $query->where(
                    'status',
                    'approved'
                )
            )

            ->when(
                $workflow === 'rejected',
                fn($query) =>
                $query->where(
                    'status',
                    'rejected'
                )
            )

            /*
                |--------------------------------------------------------------------------
                | Search
                |--------------------------------------------------------------------------
                */
            ->when(
                $search !== '',
                function ($query) use ($search) {
                    $query->where(
                        function ($query) use ($search) {
                            $query
                                ->whereHas(
                                    'user',
                                    function ($userQuery) use ($search) {
                                        $userQuery->where(
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
                 * Prioritas:
                 * 0 = siap Admin
                 * 1 = menunggu Kabid
                 * 2 = histori selesai
                 */
            ->orderByRaw(
                "CASE
                        WHEN status = 'pending'
                             AND kabid_status IN ('approved', 'not_required')
                            THEN 0
                        WHEN status = 'pending'
                             AND kabid_status = 'pending'
                            THEN 1
                        ELSE 2
                    END"
            )
            ->latest()
            ->paginate(10)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | Statistik Workflow
        |--------------------------------------------------------------------------
        */
        $readyAdminCount =
            LeaveRequest::query()
            ->where(
                'status',
                'pending'
            )
            ->whereIn(
                'kabid_status',
                [
                    LeaveRequest::KABID_STATUS_APPROVED,
                    LeaveRequest::KABID_STATUS_NOT_REQUIRED,
                ]
            )
            ->count();

        $waitingKabidCount =
            LeaveRequest::query()
            ->where(
                'status',
                'pending'
            )
            ->where(
                'kabid_status',
                LeaveRequest::KABID_STATUS_PENDING
            )
            ->whereHas(
                'user',
                fn($query) =>
                $query->where(
                    'role',
                    'karyawan'
                )
            )
            ->count();

        $approvedCount =
            LeaveRequest::query()
            ->where(
                'status',
                'approved'
            )
            ->count();

        $rejectedCount =
            LeaveRequest::query()
            ->where(
                'status',
                'rejected'
            )
            ->count();

        return view(
            'admin.leave-requests.index',
            compact(
                'leaveRequests',
                'readyAdminCount',
                'waitingKabidCount',
                'approvedCount',
                'rejectedCount',
                'workflow',
                'search'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | SHOW
    |--------------------------------------------------------------------------
    */

    public function show(
        LeaveRequest $leaveRequest
    ): View {

        $this->ensureApprover();


        /*
        |--------------------------------------------------------------------------
        | Load Semua Data Detail
        |--------------------------------------------------------------------------
        */

        $leaveRequest->load([
            'user.department',
            'permissionType',
            'leaveBalance',

            /*
             * BARU:
             *
             * Satu pengajuan memiliki banyak
             * jadwal pengganti.
             */
            'substituteSchedules.workShift',

            /*
             * Tahap 1 Kabid.
             */
            'kabidReviewer.department',

            /*
             * Tahap 2 / keputusan final Admin.
             */
            'approver',
        ]);


        return view(
            'admin.leave-requests.show',
            compact(
                'leaveRequest'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | APPROVE
    |--------------------------------------------------------------------------
    */

    public function approve(
        LeaveRequest $leaveRequest
    ): RedirectResponse {

        $this->ensureApprover();


        /*
        |--------------------------------------------------------------------------
        | Cek awal
        |--------------------------------------------------------------------------
        */

        if (
            $leaveRequest->status
            !== 'pending'
        ) {

            return back()->with(
                'error',
                'Pengajuan ini sudah diproses sebelumnya.'
            );
        }


        try {

            DB::transaction(
                function () use (
                    $leaveRequest
                ) {

                    /*
                    |--------------------------------------------------------------------------
                    | Lock Pengajuan
                    |--------------------------------------------------------------------------
                    */

                    $request =
                        LeaveRequest::query()

                        ->whereKey(
                            $leaveRequest->id
                        )

                        ->lockForUpdate()

                        ->firstOrFail();


                    /*
                    |--------------------------------------------------------------------------
                    | Cek Lagi Setelah Lock
                    |--------------------------------------------------------------------------
                    */

                    if (
                        $request->status
                        !== 'pending'
                    ) {

                        throw new \RuntimeException(
                            'Pengajuan ini sudah diproses sebelumnya.'
                        );
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Wajib Lolos Tahap Kabid
                    |--------------------------------------------------------------------------
                    */
                    $this->ensureKabidStageReady(
                        $request
                    );


                    /*
                    |--------------------------------------------------------------------------
                    | Validasi Persetujuan Hari Tidak Dibayar
                    |--------------------------------------------------------------------------
                    |
                    | Jika pengajuan memiliki unpaid_days, karyawan harus
                    | sudah memberikan persetujuan saat mengajukan.
                    |
                    */

                    if (
                        $request->unpaid_days > 0
                        &&
                        ! $request->salary_deduction_consent
                    ) {

                        throw new \RuntimeException(
                            'Pengajuan memiliki hari tidak dibayar, tetapi persetujuan karyawan belum tercatat. Pengajuan belum dapat disetujui.'
                        );
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Validasi Konfirmasi Pengganti Mandiri untuk Izin Sakit
                    |--------------------------------------------------------------------------
                    */
                    if (
                        $request->self_replacement_days > 0
                        &&
                        ! $request->self_replacement_consent
                    ) {
                        throw new \RuntimeException(
                            'Pengajuan memiliki hari pengganti mandiri, tetapi konfirmasi karyawan belum tercatat. Pengajuan belum dapat disetujui.'
                        );
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Potong Saldo Cuti Tahunan
                    |--------------------------------------------------------------------------
                    |
                    | PENTING:
                    |
                    | Bukan total_days.
                    |
                    | Yang dikurangi adalah:
                    |
                    | annual_leave_deducted_days
                    |
                    | Contoh:
                    |
                    | sakit 3 hari
                    | hak sakit = 1
                    | excess = 2
                    |
                    | annual_leave_deducted_days = 2
                    |
                    */

                    if (
                        $request
                        ->annual_leave_deducted_days
                        > 0
                    ) {

                        /*
                         * Harus punya saldo.
                         */
                        if (
                            ! $request
                                ->leave_balance_id
                        ) {

                            throw new \RuntimeException(
                                'Saldo cuti tahunan untuk pengajuan ini tidak ditemukan.'
                            );
                        }


                        /*
                        |--------------------------------------------------------------------------
                        | Lock Saldo
                        |--------------------------------------------------------------------------
                        */

                        $balance =
                            LeaveBalance::query()

                            ->whereKey(
                                $request
                                    ->leave_balance_id
                            )

                            ->lockForUpdate()

                            ->firstOrFail();


                        /*
                        |--------------------------------------------------------------------------
                        | Sisa Aktual
                        |--------------------------------------------------------------------------
                        */

                        $remainingDays =
                            $balance->quota_days
                            -
                            $balance->used_days;


                        /*
                        |--------------------------------------------------------------------------
                        | Pastikan Masih Cukup
                        |--------------------------------------------------------------------------
                        */

                        if (
                            $request
                            ->annual_leave_deducted_days
                            >
                            $remainingDays
                        ) {

                            throw new \RuntimeException(
                                'Sisa cuti tahunan karyawan tidak mencukupi untuk menyetujui pengajuan ini.'
                            );
                        }


                        /*
                        |--------------------------------------------------------------------------
                        | Tambah Used Days
                        |--------------------------------------------------------------------------
                        */

                        $balance->increment(
                            'used_days',
                            $request
                                ->annual_leave_deducted_days
                        );
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Approve
                    |--------------------------------------------------------------------------
                    */

                    $request->update([

                        'status' =>
                        'approved',

                        'approved_by' =>
                        Auth::id(),

                        'approved_at' =>
                        now(),

                        'rejected_at' =>
                        null,

                        'rejection_reason' =>
                        null,
                    ]);
                }
            );
        } catch (
            \RuntimeException $exception
        ) {

            return back()->with(
                'error',
                $exception->getMessage()
            );
        }


        return back()->with(
            'success',
            'Pengajuan perizinan berhasil disetujui.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | REJECT
    |--------------------------------------------------------------------------
    */

    public function reject(
        Request $request,
        LeaveRequest $leaveRequest
    ): RedirectResponse {

        $this->ensureApprover();


        /*
        |--------------------------------------------------------------------------
        | Hanya Pending
        |--------------------------------------------------------------------------
        */

        if (
            $leaveRequest->status
            !== 'pending'
        ) {

            return back()->with(
                'error',
                'Pengajuan ini sudah diproses sebelumnya.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Validasi Alasan
        |--------------------------------------------------------------------------
        */

        $validated =
            $request->validate([
                'rejection_reason' => [
                    'required',
                    'string',
                    'max:1000',
                ],
            ], [
                'rejection_reason.required' =>
                'Alasan penolakan wajib diisi.',

                'rejection_reason.max' =>
                'Alasan penolakan maksimal 1000 karakter.',
            ]);


        /*
        |--------------------------------------------------------------------------
        | Transaction
        |--------------------------------------------------------------------------
        */

        try {

            DB::transaction(
                function () use (
                    $leaveRequest,
                    $validated
                ) {

                    /*
                    |--------------------------------------------------------------------------
                    | Lock Request
                    |--------------------------------------------------------------------------
                    */

                    $currentRequest =
                        LeaveRequest::query()

                        ->whereKey(
                            $leaveRequest->id
                        )

                        ->lockForUpdate()

                        ->firstOrFail();


                    if (
                        $currentRequest->status
                        !== 'pending'
                    ) {

                        throw new \RuntimeException(
                            'Pengajuan ini sudah diproses sebelumnya.'
                        );
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Wajib Lolos Tahap Kabid
                    |--------------------------------------------------------------------------
                    */
                    $this->ensureKabidStageReady(
                        $currentRequest
                    );


                    /*
                    |--------------------------------------------------------------------------
                    | Reject
                    |--------------------------------------------------------------------------
                    |
                    | Reject TIDAK mengubah used_days.
                    |
                    */

                    $currentRequest->update([

                        'status' =>
                        'rejected',

                        /*
                         * Kita tetap simpan siapa
                         * yang memproses.
                         */
                        'approved_by' =>
                        Auth::id(),

                        'approved_at' =>
                        null,

                        'rejected_at' =>
                        now(),

                        'rejection_reason' =>
                        $validated['rejection_reason'],
                    ]);
                }
            );
        } catch (
            \RuntimeException $exception
        ) {

            return back()->with(
                'error',
                $exception->getMessage()
            );
        }


        return back()->with(
            'success',
            'Pengajuan perizinan berhasil ditolak.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | VALIDASI TAHAP KABID
    |--------------------------------------------------------------------------
    */

    private function ensureKabidStageReady(
        LeaveRequest $leaveRequest
    ): void {
        $leaveRequest->loadMissing(
            'user'
        );

        /*
         * Cuti Kabid sendiri dan data legacy tidak membutuhkan
         * approval tahap Kabid.
         */
        if (
            $leaveRequest->kabid_status
            === LeaveRequest::KABID_STATUS_NOT_REQUIRED
        ) {
            return;
        }


        /*
         * Workflow dua tahap diwajibkan untuk pengaju Karyawan.
         */
        if (
            $leaveRequest->user?->role
            !== 'karyawan'
        ) {
            return;
        }


        if (
            $leaveRequest->kabid_status
            === LeaveRequest::KABID_STATUS_APPROVED
        ) {
            return;
        }


        if (
            $leaveRequest->kabid_status
            === LeaveRequest::KABID_STATUS_REJECTED
        ) {
            throw new \RuntimeException(
                'Pengajuan ini sudah ditolak Kabid dan tidak dapat diproses Admin.'
            );
        }


        throw new \RuntimeException(
            'Pengajuan masih menunggu persetujuan Kabid. Admin belum dapat memproses pengajuan ini.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | ACCESS ADMIN
    |--------------------------------------------------------------------------
    */

    private function ensureApprover(): void
    {
        $user =
            Auth::user();


        /*
         * Controller ini khusus Admin.
         * Approval Kabid akan memakai controller terpisah pada STEP 15.
         */
        abort_unless(
            $user
                &&
                $user->role === 'admin',
            403
        );
    }
}
