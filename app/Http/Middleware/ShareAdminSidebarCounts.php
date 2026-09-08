<?php

namespace App\Http\Middleware;

use App\Models\HearYouFeedback;
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
     * Bagikan jumlah notifikasi sidebar ke seluruh view Admin.
     */
    public function handle(
        Request $request,
        Closure $next
    ): Response {
        if (
            Auth::check()
            && Auth::user()->role === 'admin'
        ) {
            $pendingEmployeeVerificationCount = User::query()
                ->where('role', 'karyawan')
                ->where('approval_status', 'pending')
                ->count();

            $pendingKabidVerificationCount = User::query()
                ->where('role', 'kabid')
                ->where('approval_status', 'pending')
                ->count();

            $pendingLeaveRequestCount = LeaveRequest::query()
                ->where('status', 'pending')
                ->whereIn(
                    'kabid_status',
                    [
                        LeaveRequest::KABID_STATUS_APPROVED,
                        LeaveRequest::KABID_STATUS_NOT_REQUIRED,
                    ]
                )
                ->count();

            $pendingReimbursementCount = Reimbursement::query()
                ->where('status', Reimbursement::STATUS_PENDING)
                ->count();

            /*
             * Semua Hear You yang belum memperoleh tanggapan Admin.
             * Badge tidak dibatasi bulan agar aspirasi lama tidak terlupakan.
             */
            $pendingHearYouResponseCount = HearYouFeedback::query()
                ->whereNull('admin_response')
                ->count();

            view()->share([
                'pendingEmployeeVerificationCount' =>
                    $pendingEmployeeVerificationCount,

                'pendingKabidVerificationCount' =>
                    $pendingKabidVerificationCount,

                'pendingLeaveRequestCount' =>
                    $pendingLeaveRequestCount,

                'pendingReimbursementCount' =>
                    $pendingReimbursementCount,

                'pendingHearYouResponseCount' =>
                    $pendingHearYouResponseCount,
            ]);
        }

        return $next($request);
    }
}
