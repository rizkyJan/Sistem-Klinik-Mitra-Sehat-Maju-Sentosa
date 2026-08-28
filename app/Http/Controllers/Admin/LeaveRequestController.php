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


        $status =
            $request->input('status');


        $search =
            $request->input('search');


        /*
        |--------------------------------------------------------------------------
        | Pengajuan
        |--------------------------------------------------------------------------
        |
        | Jadwal pengganti sekarang berasal dari:
        |
        | leave_request_substitute_schedules
        |
        | sehingga eager load:
        |
        | substituteSchedules.workShift
        |
        */

        $leaveRequests =
            LeaveRequest::query()

            ->with([
                'user.department',
                'permissionType',
                'substituteSchedules.workShift',
                'approver',
            ])

            /*
                |--------------------------------------------------------------------------
                | Filter Status
                |--------------------------------------------------------------------------
                */

            ->when(
                $status,
                function (
                    $query,
                    $status
                ) {

                    $query->where(
                        'status',
                        $status
                    );
                }
            )

            /*
                |--------------------------------------------------------------------------
                | Search
                |--------------------------------------------------------------------------
                */

            ->when(
                $search,
                function (
                    $query,
                    $search
                ) {

                    $query->where(
                        function ($query) use ($search) {

                            /*
                                 * Cari berdasarkan karyawan.
                                 */
                            $query->whereHas(
                                'user',
                                function ($query) use ($search) {

                                    $query->where(
                                        function ($query) use ($search) {

                                            $query
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

                                /*
                                 * Atau berdasarkan jenis perizinan.
                                 */
                                ->orWhereHas(
                                    'permissionType',
                                    function ($query) use ($search) {

                                        $query->where(
                                            'name',
                                            'like',
                                            '%' . $search . '%'
                                        );
                                    }
                                );
                        }
                    );
                }
            )

            ->latest()

            ->paginate(10)

            ->withQueryString();


        /*
        |--------------------------------------------------------------------------
        | Statistik
        |--------------------------------------------------------------------------
        */

        $pendingCount =
            LeaveRequest::query()
            ->where(
                'status',
                'pending'
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
                'pendingCount',
                'approvedCount',
                'rejectedCount',
                'status',
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
    | ACCESS
    |--------------------------------------------------------------------------
    */

    private function ensureApprover(): void
    {
        $user =
            Auth::user();


        abort_unless(
            $user
                &&
                in_array(
                    $user->role,
                    [
                        'admin',
                        'kabid',
                    ],
                    true
                ),
            403
        );
    }
}
