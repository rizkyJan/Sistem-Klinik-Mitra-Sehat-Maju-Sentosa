<?php

namespace App\Http\Middleware;

use App\Models\DutyAssignment;
use App\Models\DutyLetter;
use App\Models\LeaveRequest;
use App\Services\DepartmentMonthlyReportRequirementService;
use App\Services\HearYouRequirementService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class ShareKabidSidebarCounts
{
    public function __construct(
        private readonly HearYouRequirementService $hearYouRequirements,
        private readonly DepartmentMonthlyReportRequirementService $monthlyReportRequirements
    ) {}

    public function handle(
        Request $request,
        Closure $next
    ): Response {
        $user = $request->user();

        $pendingKabidLeaveApprovalCount = 0;
        $unreadDutyNotificationCount = 0;
        $dutyAttentionCount = 0;
        $dutySidebarCount = 0;
        $hearYouObligationCount = 0;
        $hearYouLocked = false;
        $hearYouCurrentSubmitted = true;
        $hearYouDueEvaluationCount = 0;
        $monthlyReportObligationCount = 0;
        $monthlyReportLocked = false;
        $monthlyReportNeedsRevision = false;
        $monthlyReportCurrentSubmitted = true;
        $monthlyReportWindowOpen = false;

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

            $unreadDutyNotificationCount = $user->unreadNotifications()
                ->get()
                ->filter(
                    fn($notification) =>
                        ($notification->data['module'] ?? null) === 'duty'
                )
                ->count();

            $dutyAttentionCount = DutyAssignment::query()
                ->where('user_id', $user->id)
                ->whereIn(
                    'report_status',
                    [
                        DutyAssignment::REPORT_PENDING,
                        DutyAssignment::REPORT_REVISION,
                    ]
                )
                ->whereHas(
                    'dutyLetter',
                    fn($query) => $query->where(
                        'status',
                        DutyLetter::STATUS_PUBLISHED
                    )
                )
                ->count();

            $dutySidebarCount = max(
                $unreadDutyNotificationCount,
                $dutyAttentionCount
            );

            $hearYouStatus = $this->hearYouRequirements->statusFor($user);

            $hearYouObligationCount = $hearYouStatus['obligation_count'];
            $hearYouLocked = $hearYouStatus['locked'];
            $hearYouCurrentSubmitted = $hearYouStatus['current_submitted'];
            $hearYouDueEvaluationCount = $hearYouStatus['due_evaluation_count'];

            $monthlyReportStatus = $this->monthlyReportRequirements->statusFor($user);
            $monthlyReportObligationCount = $monthlyReportStatus['obligation_count'];
            $monthlyReportLocked = $monthlyReportStatus['locked'];
            $monthlyReportNeedsRevision = $monthlyReportStatus['needs_revision'];
            $monthlyReportCurrentSubmitted = $monthlyReportStatus['current_submitted'];
            $monthlyReportWindowOpen = $monthlyReportStatus['window_open'];
        }

        View::share([
            'pendingKabidLeaveApprovalCount' => $pendingKabidLeaveApprovalCount,
            'unreadDutyNotificationCount' => $unreadDutyNotificationCount,
            'dutyAttentionCount' => $dutyAttentionCount,
            'dutySidebarCount' => $dutySidebarCount,
            'hearYouObligationCount' => $hearYouObligationCount,
            'hearYouLocked' => $hearYouLocked,
            'hearYouCurrentSubmitted' => $hearYouCurrentSubmitted,
            'hearYouDueEvaluationCount' => $hearYouDueEvaluationCount,
            'monthlyReportObligationCount' => $monthlyReportObligationCount,
            'monthlyReportLocked' => $monthlyReportLocked,
            'monthlyReportNeedsRevision' => $monthlyReportNeedsRevision,
            'monthlyReportCurrentSubmitted' => $monthlyReportCurrentSubmitted,
            'monthlyReportWindowOpen' => $monthlyReportWindowOpen,
        ]);

        return $next($request);
    }
}
