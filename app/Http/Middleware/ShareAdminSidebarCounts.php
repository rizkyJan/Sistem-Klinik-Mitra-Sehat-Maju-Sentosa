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
            | Pengajuan perizinan yang masih menunggu keputusan
            |--------------------------------------------------------------------------
            */
            $pendingLeaveRequestCount = LeaveRequest::query()
                ->where('status', 'pending')
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

                'pendingLeaveRequestCount' =>
                $pendingLeaveRequestCount,

                'pendingReimbursementCount' =>
                $pendingReimbursementCount,
            ]);
        }

        return $next($request);
    }
}
