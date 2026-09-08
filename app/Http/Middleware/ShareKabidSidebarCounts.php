<?php

namespace App\Http\Middleware;

use App\Models\LeaveRequest;
use App\Services\HearYouRequirementService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class ShareKabidSidebarCounts
{
    public function __construct(
        private readonly HearYouRequirementService $hearYouRequirements
    ) {}

    public function handle(
        Request $request,
        Closure $next
    ): Response {
        $user = $request->user();

        $pendingKabidLeaveApprovalCount = 0;
        $hearYouObligationCount = 0;
        $hearYouLocked = false;
        $hearYouCurrentSubmitted = true;
        $hearYouDueEvaluationCount = 0;

        if (
            $user
            && $user->role === 'kabid'
        ) {
            if ($user->department_id) {
                $pendingKabidLeaveApprovalCount = LeaveRequest::query()
                    ->where('status', 'pending')
                    ->where(
                        'kabid_status',
                        LeaveRequest::KABID_STATUS_PENDING
                    )
                    ->whereHas(
                        'user',
                        function ($query) use ($user): void {
                            $query
                                ->where('role', 'karyawan')
                                ->where('approval_status', 'approved')
                                ->where('department_id', $user->department_id);
                        }
                    )
                    ->count();
            }

            $hearYouStatus = $this->hearYouRequirements->statusFor($user);

            $hearYouObligationCount = $hearYouStatus['obligation_count'];
            $hearYouLocked = $hearYouStatus['locked'];
            $hearYouCurrentSubmitted = $hearYouStatus['current_submitted'];
            $hearYouDueEvaluationCount = $hearYouStatus['due_evaluation_count'];
        }

        View::share([
            'pendingKabidLeaveApprovalCount' => $pendingKabidLeaveApprovalCount,
            'hearYouObligationCount' => $hearYouObligationCount,
            'hearYouLocked' => $hearYouLocked,
            'hearYouCurrentSubmitted' => $hearYouCurrentSubmitted,
            'hearYouDueEvaluationCount' => $hearYouDueEvaluationCount,
        ]);

        return $next($request);
    }
}
