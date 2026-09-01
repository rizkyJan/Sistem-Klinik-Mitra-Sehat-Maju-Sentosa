<?php

namespace App\Http\Middleware;

use App\Models\LeaveRequest;
use App\Models\Reimbursement;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ShareAdminSidebarCounts
{
    /**
     * Bagikan jumlah notifikasi sidebar ke seluruh view admin.
     *
     * Badge yang disediakan:
     * - $pendingEmployeeVerificationCount
     * - $pendingKabidVerificationCount
     * - $pendingLeaveRequestCount
     * - $pendingReimbursementCount
     */
    public function handle(
        Request $request,
        Closure $next
    ): Response {
        /*
        |--------------------------------------------------------------------------
        | Hanya hitung untuk Admin yang sedang login
        |--------------------------------------------------------------------------
        */
        if (
            Auth::check()
            &&
            Auth::user()->role === 'admin'
        ) {
            /*
            |--------------------------------------------------------------------------
            | Karyawan yang masih menunggu verifikasi
            |--------------------------------------------------------------------------
            |
            | Contoh:
            | approval_status = pending
            |
            | Jika ada 1 orang -> badge 1
            | Jika ada 2 orang -> badge 2
            |
            */
            $pendingEmployeeVerificationCount = User::query()
                ->where('role', 'karyawan')
                ->where('approval_status', 'pending')
                ->count();

            /*
            |--------------------------------------------------------------------------
            | Kabid yang masih menunggu verifikasi
            |--------------------------------------------------------------------------
            */
            $pendingKabidVerificationCount = User::query()
                ->where('role', 'kabid')
                ->where('approval_status', 'pending')
                ->count();

            /*
            |--------------------------------------------------------------------------
            | Pengajuan perizinan yang masih menunggu keputusan
            |--------------------------------------------------------------------------
            */
            /*
            |--------------------------------------------------------------------------
            | Pengajuan cuti yang SUDAH SIAP diproses Admin
            |--------------------------------------------------------------------------
            |
            | Badge Admin tidak lagi menghitung seluruh status pending.
            |
            | Yang dihitung hanya:
            | - Karyawan yang sudah ACC Kabid
            | - Cuti Kabid sendiri / data legacy (not_required)
            |
            | Pengajuan yang masih menunggu Kabid tidak menjadi pekerjaan
            | Admin sehingga tidak perlu menambah badge Admin.
            |
            */
            $pendingLeaveRequestCount = LeaveRequest::query()
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

            /*
            |--------------------------------------------------------------------------
            | Reimburse pending
            |--------------------------------------------------------------------------
            |
            | Modul reimburse diproses oleh role admin.
            */
            $pendingReimbursementCount = Reimbursement::query()
                ->where('status', Reimbursement::STATUS_PENDING)
                ->count();

            /*
            |--------------------------------------------------------------------------
            | Share ke seluruh Blade pada request admin ini
            |--------------------------------------------------------------------------
            */
            view()->share([
                'pendingEmployeeVerificationCount' =>
                $pendingEmployeeVerificationCount,

                'pendingKabidVerificationCount' =>
                $pendingKabidVerificationCount,

                'pendingLeaveRequestCount' =>
                $pendingLeaveRequestCount,

                'pendingReimbursementCount' =>
                $pendingReimbursementCount,
            ]);
        }

        return $next($request);
    }
}
