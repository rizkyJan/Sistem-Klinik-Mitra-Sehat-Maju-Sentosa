<?php

namespace App\Services;

use App\Models\DepartmentMonthlyReport;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class DepartmentMonthlyReportRequirementService
{
    public function now(): Carbon
    {
        return Carbon::now(config('monthly-report.timezone', 'Asia/Jakarta'));
    }

    public function currentPeriod(): Carbon
    {
        return $this->now()->copy()->startOfMonth();
    }

    public function startPeriod(): Carbon
    {
        $timezone = config('monthly-report.timezone', 'Asia/Jakarta');
        $configured = (string) config('monthly-report.start_period', '2026-09');

        try {
            return Carbon::createFromFormat('Y-m-d', $configured . '-01', $timezone)
                ->startOfMonth();
        } catch (\Throwable) {
            return $this->currentPeriod();
        }
    }

    public function requiredFromDay(): int
    {
        return max(1, min(31, (int) config('monthly-report.required_from_day', 25)));
    }

    /**
     * Periode terbaru yang sudah jatuh tempo.
     * - tanggal >= required_from_day: bulan berjalan sudah wajib;
     * - tanggal < required_from_day: kewajiban terbaru adalah bulan sebelumnya.
     */
    public function latestDuePeriod(): ?Carbon
    {
        $now = $this->now();

        $latest = $now->day >= $this->requiredFromDay()
            ? $now->copy()->startOfMonth()
            : $now->copy()->subMonthNoOverflow()->startOfMonth();

        return $latest->lt($this->startPeriod()) ? null : $latest;
    }

    public function isWindowOpen(): bool
    {
        return $this->latestDuePeriod() !== null;
    }

    public function isRequiredFor(User $user): bool
    {
        return $user->role === 'kabid'
            && $user->approval_status === 'approved'
            && (bool) $user->is_active;
    }

    public function statusFor(User $user): array
    {
        $latestDue = $this->latestDuePeriod();
        $fallbackPeriod = $this->currentPeriod();

        if (! $this->isRequiredFor($user)) {
            return $this->emptyStatus($fallbackPeriod, $latestDue, false);
        }

        if (! $user->department_id) {
            return [
                ...$this->emptyStatus($fallbackPeriod, $latestDue, true),
                'has_department' => false,
                'locked' => false,
            ];
        }

        if (! $latestDue) {
            return $this->emptyStatus($fallbackPeriod, null, true);
        }

        $start = $this->startPeriod();

        /** @var Collection<string, DepartmentMonthlyReport> $reports */
        $reports = DepartmentMonthlyReport::query()
            ->where('department_id', $user->department_id)
            ->whereDate('period', '>=', $start->toDateString())
            ->whereDate('period', '<=', $latestDue->toDateString())
            ->get()
            ->keyBy(fn (DepartmentMonthlyReport $report) => $report->period->format('Y-m'));

        $obligations = collect();
        $cursor = $start->copy();

        while ($cursor->lte($latestDue)) {
            $key = $cursor->format('Y-m');
            $report = $reports->get($key);

            if (! $report || $report->status === DepartmentMonthlyReport::STATUS_REVISION) {
                $obligations->push([
                    'period' => $cursor->copy(),
                    'report' => $report,
                    'type' => $report ? 'revision' : 'missing',
                ]);
            }

            $cursor->addMonthNoOverflow()->startOfMonth();
        }

        $target = $obligations->first();
        $targetPeriod = $target['period'] ?? $latestDue->copy();
        $targetReport = $target['report'] ?? $reports->get($targetPeriod->format('Y-m'));
        $needsRevision = $targetReport?->status === DepartmentMonthlyReport::STATUS_REVISION;

        return [
            'required' => true,
            'has_department' => true,
            'window_open' => true,
            'required_from_day' => $this->requiredFromDay(),
            'start_period' => $start,
            'latest_due_period' => $latestDue,
            'period' => $targetPeriod,
            'report' => $targetReport,
            'current_submitted' => (bool) $targetReport,
            'needs_revision' => $needsRevision,
            'obligations' => $obligations,
            'obligation_count' => $obligations->count(),
            'locked' => $obligations->isNotEmpty(),
        ];
    }

    private function emptyStatus(
        Carbon $period,
        ?Carbon $latestDue,
        bool $required
    ): array {
        return [
            'required' => $required,
            'has_department' => true,
            'window_open' => $latestDue !== null,
            'required_from_day' => $this->requiredFromDay(),
            'start_period' => $this->startPeriod(),
            'latest_due_period' => $latestDue,
            'period' => $period,
            'report' => null,
            'current_submitted' => true,
            'needs_revision' => false,
            'obligations' => collect(),
            'obligation_count' => 0,
            'locked' => false,
        ];
    }
}
