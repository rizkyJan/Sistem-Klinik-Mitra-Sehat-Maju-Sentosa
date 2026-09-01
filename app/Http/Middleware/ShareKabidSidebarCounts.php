<?php

namespace App\Http\Middleware;

use App\Models\LeaveRequest;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class ShareKabidSidebarCounts
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {
        $user =
            $request->user();

        $pendingKabidLeaveApprovalCount = 0;

        if (
            $user
            && $user->role === 'kabid'
            && $user->department_id
        ) {
            $pendingKabidLeaveApprovalCount =
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
                    function ($query) use ($user) {
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
                                $user->department_id
                            );
                    }
                )
                ->count();
        }

        View::share(
            'pendingKabidLeaveApprovalCount',
            $pendingKabidLeaveApprovalCount
        );

        return $next(
            $request
        );
    }
}
