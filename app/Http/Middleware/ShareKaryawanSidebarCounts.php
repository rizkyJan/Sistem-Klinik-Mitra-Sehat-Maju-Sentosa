<?php

namespace App\Http\Middleware;

use App\Models\DutyAssignment;
use App\Models\DutyLetter;
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
        $dutyAttentionCount = 0;
        $dutySidebarCount = 0;
        $hearYouObligationCount = 0;
        $hearYouLocked = false;
        $hearYouCurrentSubmitted = true;
        $hearYouDueEvaluationCount = 0;

        if ($user && $user->role === 'karyawan') {
            $unreadDutyNotificationCount = $user->unreadNotifications()
                ->get()
                ->filter(
                    fn($notification) =>
                        ($notification->data['module'] ?? null) === 'duty'
                )
                ->count();

            /*
             * Tugas yang masih perlu perhatian pegawai:
             * - Surat masih aktif/published;
             * - laporan belum dikirim, atau Admin meminta revisi.
             *
             * Query ini membuat assignment lama sebelum patch tetap bisa
             * memunculkan badge walaupun belum mempunyai record notifikasi.
             */
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
        }

        View::share([
            'unreadDutyNotificationCount' => $unreadDutyNotificationCount,
            'dutyAttentionCount' => $dutyAttentionCount,
            'dutySidebarCount' => $dutySidebarCount,
            'hearYouObligationCount' => $hearYouObligationCount,
            'hearYouLocked' => $hearYouLocked,
            'hearYouCurrentSubmitted' => $hearYouCurrentSubmitted,
            'hearYouDueEvaluationCount' => $hearYouDueEvaluationCount,
        ]);

        return $next($request);
    }
}
