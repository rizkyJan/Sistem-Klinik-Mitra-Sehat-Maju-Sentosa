<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeaveBalance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LeaveBalanceController extends Controller
{
    public function index(Request $request): View
    {
        $this->ensureApprover();

        $currentYear = now()->year;
        $year = (int) $request->input('year', $currentYear);
        $search = trim((string) $request->input('search', ''));

        if ($year > $currentYear + 1) {
            $year = $currentYear;
        }

        $eligibilityCutoff = $this->getEligibilityCutoff($year);

        $allActiveEmployees = User::query()
            ->whereIn('role', ['karyawan', 'kabid'])
            ->where('is_active', true)
            ->with([
                'department',
                'leaveBalances' => function ($query) use ($year) {
                    $query->where('year', $year);
                },
            ])
            ->orderBy('name')
            ->get();

        $allActiveEmployees->each(function (User $employee) use ($eligibilityCutoff) {
            $employee->selected_leave_balance =
                $employee->leaveBalances->first();

            $employee->annual_leave_eligible_date =
                $this->getAnnualLeaveEligibleDate($employee);

            $employee->is_annual_leave_eligible =
                $this->isEmployeeEligibleOn(
                    $employee,
                    $eligibilityCutoff
                );
        });

        $totalKaryawan = $allActiveEmployees->count();

        $sudahDiberi = $allActiveEmployees
            ->filter(
                fn(User $employee) =>
                $employee->selected_leave_balance !== null
            )
            ->count();

        $belumDiberi = $allActiveEmployees
            ->filter(
                fn(User $employee) =>
                $employee->is_annual_leave_eligible
                    && $employee->selected_leave_balance === null
            )
            ->count();

        $belumBerhak = $allActiveEmployees
            ->filter(
                fn(User $employee) =>
                ! $employee->is_annual_leave_eligible
                    && $employee->selected_leave_balance === null
            )
            ->count();

        $employees = $allActiveEmployees;

        if ($search !== '') {
            $needle = mb_strtolower($search);

            $employees = $employees
                ->filter(function (User $employee) use ($needle) {
                    $values = [
                        $employee->name,
                        $employee->nik,
                        $employee->email,
                        $employee->department?->name,
                    ];

                    foreach ($values as $value) {
                        if (
                            $value !== null
                            && str_contains(
                                mb_strtolower((string) $value),
                                $needle
                            )
                        ) {
                            return true;
                        }
                    }

                    return false;
                })
                ->values();
        }

        $oldestJoinDate = User::query()
            ->whereIn('role', ['karyawan', 'kabid'])
            ->whereNotNull('join_date')
            ->min('join_date');

        $startYear = $oldestJoinDate
            ? Carbon::parse($oldestJoinDate)->year
            : $currentYear;

        $startYear = min(
            $startYear,
            $currentYear - 4
        );

        $years = collect(
            range(
                $currentYear,
                $startYear
            )
        );

        if (! $years->contains($year)) {
            $years->push($year);
        }

        $years = $years
            ->unique()
            ->sortDesc()
            ->values();

        return view(
            'admin.leave-balances.index',
            compact(
                'employees',
                'year',
                'years',
                'search',
                'totalKaryawan',
                'sudahDiberi',
                'belumDiberi',
                'belumBerhak'
            )
        );
    }

    public function generate(Request $request): RedirectResponse
    {
        $this->ensureApprover();

        $validated = $request->validate([
            'year' => [
                'required',
                'integer',
                'min:2000',
                'max:' . now()->year,
            ],
        ]);

        $year = (int) $validated['year'];
        $eligibilityCutoff = $this->getEligibilityCutoff($year);

        $employees = User::query()
            ->whereIn('role', ['karyawan', 'kabid'])
            ->where('is_active', true)
            ->whereNotNull('join_date')
            ->orderBy('name')
            ->get();

        $generated = 0;

        foreach ($employees as $employee) {
            if (
                ! $this->isEmployeeEligibleOn(
                    $employee,
                    $eligibilityCutoff
                )
            ) {
                continue;
            }

            $balance = LeaveBalance::firstOrCreate(
                [
                    'user_id' => $employee->id,
                    'year' => $year,
                ],
                [
                    'quota_days' => 9,
                    'used_days' => 0,
                ]
            );

            if ($balance->wasRecentlyCreated) {
                $generated++;
            }
        }

        if ($generated === 0) {
            return redirect()
                ->route(
                    'admin.leave-balances.index',
                    ['year' => $year]
                )
                ->with(
                    'success',
                    'Tidak ada jatah cuti baru yang perlu dibuat. Semua pegawai yang sudah berhak telah memiliki jatah.'
                );
        }

        return redirect()
            ->route(
                'admin.leave-balances.index',
                ['year' => $year]
            )
            ->with(
                'success',
                "{$generated} pegawai berhasil diberikan jatah cuti {$year} sebanyak 9 hari."
            );
    }

    public function update(
        Request $request,
        LeaveBalance $leaveBalance
    ): RedirectResponse {
        $this->ensureApprover();

        $validated = $request->validate([
            'quota_days' => [
                'required',
                'integer',
                'min:0',
                'max:365',
            ],
        ]);

        $quotaDays = (int) $validated['quota_days'];

        if ($quotaDays < $leaveBalance->used_days) {
            throw ValidationException::withMessages([
                'quota_days' =>
                "Jatah cuti tidak boleh lebih kecil dari cuti yang sudah terpakai ({$leaveBalance->used_days} hari).",
            ]);
        }

        $leaveBalance->update([
            'quota_days' => $quotaDays,
        ]);

        return back()->with(
            'success',
            'Jatah cuti berhasil diperbarui.'
        );
    }

    private function getAnnualLeaveEligibleDate(
        User $employee
    ): ?Carbon {
        if (! $employee->join_date) {
            return null;
        }

        return Carbon::parse($employee->join_date)
            ->startOfDay()
            ->addYear();
    }

    private function isEmployeeEligibleOn(
        User $employee,
        Carbon $cutoff
    ): bool {
        $eligibleDate =
            $this->getAnnualLeaveEligibleDate($employee);

        if (! $eligibleDate) {
            return false;
        }

        return $eligibleDate->lte(
            $cutoff->copy()->endOfDay()
        );
    }

    private function getEligibilityCutoff(
        int $year
    ): Carbon {
        $currentYear = now()->year;

        if ($year === $currentYear) {
            return now()->endOfDay();
        }

        if ($year < $currentYear) {
            return Carbon::create(
                $year,
                12,
                31,
                23,
                59,
                59
            );
        }

        return Carbon::create(
            $year,
            1,
            1
        )->subSecond();
    }

    private function ensureApprover(): void
    {
        $user = Auth::user();

        abort_unless(
            $user && $user->role === 'admin',
            403
        );
    }
}
