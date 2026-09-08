<?php

namespace App\Http\Middleware;

use App\Services\HearYouRequirementService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class ShareKaryawanSidebarCounts
{
    public function __construct(
        private readonly HearYouRequirementService $hearYouRequirements
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        $unreadDutyNotificationCount = 0;
        $hearYouObligationCount = 0;
        $hearYouLocked = false;
        $hearYouCurrentSubmitted = true;
        $hearYouDueEvaluationCount = 0;

        if ($user && $user->role === 'karyawan') {
            $unreadDutyNotificationCount = $user->unreadNotifications()
                ->get()
                ->filter(
                    fn($notification) => ($notification->data['module'] ?? null) === 'duty'
                )
                ->count();

            $hearYouStatus = $this->hearYouRequirements->statusFor($user);

            $hearYouObligationCount = $hearYouStatus['obligation_count'];
            $hearYouLocked = $hearYouStatus['locked'];
            $hearYouCurrentSubmitted = $hearYouStatus['current_submitted'];
            $hearYouDueEvaluationCount = $hearYouStatus['due_evaluation_count'];
        }

        View::share([
            'unreadDutyNotificationCount' => $unreadDutyNotificationCount,
            'hearYouObligationCount' => $hearYouObligationCount,
            'hearYouLocked' => $hearYouLocked,
            'hearYouCurrentSubmitted' => $hearYouCurrentSubmitted,
            'hearYouDueEvaluationCount' => $hearYouDueEvaluationCount,
        ]);

        return $next($request);
    }
}
