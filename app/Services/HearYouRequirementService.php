<?php

namespace App\Services;

use App\Models\HearYouFeedback;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class HearYouRequirementService
{
    public function currentPeriod(): Carbon
    {
        return Carbon::now(config('hear-you.timezone', 'Asia/Jakarta'))
            ->startOfMonth();
    }

    // public function currentPeriod(): Carbon
    // {
    //     $timezone = config('hear-you.timezone', 'Asia/Jakarta');

    //     $testDate = config('hear-you.test_date');

    //     if (
    //         app()->environment(['local', 'testing']) &&
    //         filled($testDate)
    //     ) {
    //         return Carbon::parse($testDate, $timezone)
    //             ->startOfMonth();
    //     }

    //     return Carbon::now($timezone)
    //         ->startOfMonth();
    // }

    public function isRequiredFor(User $user): bool
    {
        return in_array(
            $user->role,
            config('hear-you.required_roles', ['karyawan', 'kabid']),
            true
        );
    }

    /**
     * Menghasilkan seluruh status kewajiban Hear You milik pegawai.
     *
     * Evaluasi menjadi wajib jika:
     * - aspirasi berasal dari bulan yang sudah lewat;
     * - Admin sudah memberikan tanggapan; dan
     * - pegawai belum pernah memberikan evaluasi.
     *
     * Dengan aturan ini, jika Admin terlambat menanggapi aspirasi lama,
     * evaluasi tetap akan diminta pada login berikutnya dan tidak hilang.
     */
    public function statusFor(User $user): array
    {
        $period = $this->currentPeriod();

        if (! $this->isRequiredFor($user)) {
            return [
                'required' => false,
                'period' => $period,
                'current_feedback' => null,
                'current_submitted' => true,
                'due_evaluations' => collect(),
                'due_evaluation_count' => 0,
                'obligation_count' => 0,
                'locked' => false,
            ];
        }

        $currentFeedback = HearYouFeedback::query()
            ->where('user_id', $user->id)
            ->whereDate('period', $period->toDateString())
            ->first();

        /** @var Collection<int, HearYouFeedback> $dueEvaluations */
        $dueEvaluations = HearYouFeedback::query()
            ->where('user_id', $user->id)
            ->whereDate('period', '<', $period->toDateString())
            ->whereNotNull('admin_response')
            ->whereNotNull('responded_at')
            ->whereNull('evaluated_at')
            ->orderBy('period')
            ->get();

        $currentSubmitted = (bool) $currentFeedback;
        $obligationCount = ($currentSubmitted ? 0 : 1) + $dueEvaluations->count();

        return [
            'required' => true,
            'period' => $period,
            'current_feedback' => $currentFeedback,
            'current_submitted' => $currentSubmitted,
            'due_evaluations' => $dueEvaluations,
            'due_evaluation_count' => $dueEvaluations->count(),
            'obligation_count' => $obligationCount,
            'locked' => $obligationCount > 0,
        ];
    }
}
