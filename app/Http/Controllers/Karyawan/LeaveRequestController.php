<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Models\PermissionType;
use App\Models\User;
use App\Models\WorkShift;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LeaveRequestController extends Controller
{
    public function index(Request $request): View
    {
        $this->ensureKaryawan();

        $year = (int) $request->input('year', now()->year);

        $leaveRequests = LeaveRequest::query()
            ->with([
                'permissionType',
                'substituteSchedules.workShift',
                'approver',
            ])
            ->where('user_id', Auth::id())
            ->whereYear('start_date', $year)
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $years = LeaveRequest::query()
            ->where('user_id', Auth::id())
            ->selectRaw('YEAR(start_date) AS year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');

        $years->push(now()->year);

        $years = $years
            ->unique()
            ->sortDesc()
            ->values();

        return view(
            'karyawan.leave-requests.index',
            compact('leaveRequests', 'year', 'years')
        );
    }

    public function create(): View
    {
        $this->ensureKaryawan();

        /** @var User $user */
        $user = Auth::user();
        $user->load('department');

        $balance = $user
            ->leaveBalances()
            ->where('year', now()->year)
            ->first();

        $annualLeaveEligibleDate = null;
        $annualLeaveEligible = false;

        if ($user->join_date) {
            $annualLeaveEligibleDate = $user
                ->join_date
                ->copy()
                ->addYear();

            $annualLeaveEligible = now()
                ->startOfDay()
                ->gte(
                    $annualLeaveEligibleDate
                        ->copy()
                        ->startOfDay()
                );
        }

        $pendingAnnualDays = 0;

        if ($balance) {
            $pendingAnnualDays = LeaveRequest::query()
                ->where('user_id', $user->id)
                ->where('leave_balance_id', $balance->id)
                ->where('status', 'pending')
                ->sum('annual_leave_deducted_days');
        }

        $annualLeaveAvailableDays = 0;

        if ($balance) {
            $annualLeaveAvailableDays = max(
                0,
                $balance->quota_days
                    - $balance->used_days
                    - $pendingAnnualDays
            );
        }

        $annualLeaveCanBeUsed =
            $annualLeaveEligible
            && $balance !== null
            && $annualLeaveAvailableDays > 0;

        $permissionTypes = PermissionType::query()
            ->where('is_active', true)
            ->orderBy('id')
            ->get();

        $workShifts = WorkShift::query()
            ->where('is_active', true)
            ->orderBy('id')
            ->get();

        return view(
            'karyawan.leave-requests.create',
            compact(
                'user',
                'balance',
                'permissionTypes',
                'workShifts',
                'annualLeaveEligible',
                'annualLeaveEligibleDate',
                'annualLeaveAvailableDays',
                'annualLeaveCanBeUsed'
            )
        );
    }

    public function store(Request $request): RedirectResponse
    {
        $this->ensureKaryawan();

        $validated = $request->validate([
            'permission_type_id' => [
                'required',
                Rule::exists('permission_types', 'id')
                    ->where(fn($query) => $query->where('is_active', true)),
            ],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'expected_delivery_date' => ['nullable', 'date'],
            'reason' => ['required', 'string', 'max:1000'],
            'salary_deduction_consent' => ['nullable', 'boolean'],
            'supporting_document' => [
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:5120',
            ],
            /*
            |------------------------------------------------------------------
            | PENGGANTI DINAMIS PER TANGGAL
            |------------------------------------------------------------------
            |
            | has_substitute hanya berarti: ada MINIMAL satu hari yang
            | membutuhkan pengganti.
            |
            | Detail orang pengganti sekarang disimpan pada setiap item
            | substitute_schedules, sehingga orangnya boleh berbeda per hari.
            |
            */
            'has_substitute' => ['required', 'boolean'],

            'substitute_schedules' => [
                'required_if:has_substitute,1',
                'nullable',
                'array',
                'min:1',
            ],

            'substitute_schedules.*.schedule_date' => [
                'required',
                'date',
            ],

            'substitute_schedules.*.selected' => [
                'nullable',
                'boolean',
            ],

            'substitute_schedules.*.substitute_name' => [
                'nullable',
                'string',
                'max:255',
            ],

            'substitute_schedules.*.substitute_whatsapp' => [
                'nullable',
                'string',
                'max:20',
            ],

            'substitute_schedules.*.substitute_address' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'substitute_schedules.*.substitute_bank_name' => [
                'nullable',
                'string',
                'max:100',
            ],

            'substitute_schedules.*.substitute_bank_account_number' => [
                'nullable',
                'string',
                'max:100',
            ],

            'substitute_schedules.*.substitute_bank_account_holder' => [
                'nullable',
                'string',
                'max:255',
            ],

            'substitute_schedules.*.schedule_type' => [
                'nullable',
                Rule::in(['full_shift', 'partial_hours']),
            ],

            'substitute_schedules.*.work_shift_id' => [
                'nullable',
                Rule::exists('work_shifts', 'id')
                    ->where(fn($query) => $query->where('is_active', true)),
            ],

            'substitute_schedules.*.start_time' => [
                'nullable',
                'date_format:H:i',
            ],

            'substitute_schedules.*.end_time' => [
                'nullable',
                'date_format:H:i',
            ],
        ], [
            'permission_type_id.required' => 'Jenis perizinan wajib dipilih.',
            'end_date.after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
            'reason.required' => 'Alasan / keperluan wajib diisi.',
        ]);

        /** @var User $user */
        $user = Auth::user();

        $permissionType = PermissionType::findOrFail(
            $validated['permission_type_id']
        );

        if (
            empty($validated['start_date'])
            || empty($validated['end_date'])
        ) {
            throw ValidationException::withMessages([
                'start_date' => 'Tanggal mulai dan tanggal selesai wajib diisi.',
            ]);
        }

        $startDate = Carbon::parse($validated['start_date']);
        $endDate = Carbon::parse($validated['end_date']);

        if (
            $permissionType->code === 'maternity'
            && empty($validated['expected_delivery_date'])
        ) {
            throw ValidationException::withMessages([
                'expected_delivery_date' => 'Tanggal perkiraan melahirkan wajib diisi.',
            ]);
        }

        $totalDays = $startDate->diffInDays($endDate) + 1;

        $policyCoveredDays = null;
        $excessDays = 0;
        $excessHandling = 'none';
        $annualLeaveDeductedDays = 0;
        $unpaidDays = 0;
        $leaveBalanceId = null;
        $maternitySalaryStatus = null;

        if ($permissionType->code === 'annual_leave') {
            $this->ensureAnnualLeaveEligible(
                $user,
                $startDate,
                'permission_type_id'
            );

            if ($startDate->year !== $endDate->year) {
                throw ValidationException::withMessages([
                    'end_date' => 'Cuti tahunan tidak boleh melewati pergantian tahun.',
                ]);
            }

            $totalDays = $this->countAnnualWorkDays(
                $startDate,
                $endDate
            );

            if ($totalDays <= 0) {
                throw ValidationException::withMessages([
                    'start_date' => 'Periode yang dipilih tidak memiliki hari kerja.',
                ]);
            }

            $balance = $this->resolveAnnualBalance(
                $user->id,
                $startDate->year,
                $totalDays,
                'permission_type_id'
            );

            $leaveBalanceId = $balance->id;
            $policyCoveredDays = $totalDays;
            $annualLeaveDeductedDays = $totalDays;
            $unpaidDays = 0;
            $excessHandling = 'annual_leave';
        }

        if ($permissionType->code === 'sick') {
            if (! $request->hasFile('supporting_document')) {
                throw ValidationException::withMessages([
                    'supporting_document' => 'Izin sakit wajib dilengkapi surat dokter.',
                ]);
            }

            $policyCoveredDays = min($totalDays, 1);
            $excessDays = max(0, $totalDays - 1);

            if ($excessDays > 0) {
                $allocation = $this->allocateExcessDays(
                    user: $user,
                    startDate: $startDate,
                    endDate: $endDate,
                    excessDays: $excessDays
                );

                $annualLeaveDeductedDays = $allocation['annual_leave_days'];
                $unpaidDays = $allocation['unpaid_days'];
                $leaveBalanceId = $allocation['leave_balance_id'];
                $excessHandling = $this->resolveExcessHandling(
                    $annualLeaveDeductedDays,
                    $unpaidDays
                );

                $this->ensureSalaryDeductionConsent(
                    $request,
                    $unpaidDays,
                    'Terdapat hari izin sakit yang tidak dapat ditutup oleh hak sakit maupun cuti tahunan.'
                );
            }
        }

        if ($permissionType->code === 'marriage') {
            $policyCoveredDays = min($totalDays, 3);
            $excessDays = max(0, $totalDays - 3);

            if ($excessDays > 0) {
                $allocation = $this->allocateExcessDays(
                    user: $user,
                    startDate: $startDate,
                    endDate: $endDate,
                    excessDays: $excessDays
                );

                $annualLeaveDeductedDays = $allocation['annual_leave_days'];
                $unpaidDays = $allocation['unpaid_days'];
                $leaveBalanceId = $allocation['leave_balance_id'];
                $excessHandling = $this->resolveExcessHandling(
                    $annualLeaveDeductedDays,
                    $unpaidDays
                );

                $this->ensureSalaryDeductionConsent(
                    $request,
                    $unpaidDays,
                    'Terdapat hari izin menikah yang tidak dapat ditutup oleh hak menikah maupun cuti tahunan.'
                );
            }
        }

        if ($permissionType->code === 'miscarriage') {
            $policyCoveredDays = min($totalDays, 7);
            $excessDays = max(0, $totalDays - 7);

            if ($excessDays > 0) {
                $allocation = $this->allocateExcessDays(
                    user: $user,
                    startDate: $startDate,
                    endDate: $endDate,
                    excessDays: $excessDays
                );

                $annualLeaveDeductedDays = $allocation['annual_leave_days'];
                $unpaidDays = $allocation['unpaid_days'];
                $leaveBalanceId = $allocation['leave_balance_id'];
                $excessHandling = $this->resolveExcessHandling(
                    $annualLeaveDeductedDays,
                    $unpaidDays
                );

                $this->ensureSalaryDeductionConsent(
                    $request,
                    $unpaidDays,
                    'Terdapat hari cuti keguguran yang melebihi hak 7 hari dan tidak dapat ditutup oleh cuti tahunan.'
                );
            }
        }

        if ($permissionType->code === 'maternity') {
            $expectedDelivery = Carbon::parse(
                $validated['expected_delivery_date']
            );

            $policyStart = $expectedDelivery
                ->copy()
                ->subMonthNoOverflow();

            $policyEnd = $expectedDelivery
                ->copy()
                ->addMonthNoOverflow();

            $coveredStart = $startDate->gt($policyStart)
                ? $startDate->copy()
                : $policyStart->copy();

            $coveredEnd = $endDate->lt($policyEnd)
                ? $endDate->copy()
                : $policyEnd->copy();

            if ($coveredStart->lte($coveredEnd)) {
                $policyCoveredDays = $coveredStart->diffInDays($coveredEnd) + 1;
            } else {
                $policyCoveredDays = 0;
            }

            $excessDays = max(
                0,
                $totalDays - $policyCoveredDays
            );

            if ($excessDays > 0) {
                $allocation = $this->allocateExcessDays(
                    user: $user,
                    startDate: $startDate,
                    endDate: $endDate,
                    excessDays: $excessDays
                );

                $annualLeaveDeductedDays = $allocation['annual_leave_days'];
                $unpaidDays = $allocation['unpaid_days'];
                $leaveBalanceId = $allocation['leave_balance_id'];
                $excessHandling = $this->resolveExcessHandling(
                    $annualLeaveDeductedDays,
                    $unpaidDays
                );

                $this->ensureSalaryDeductionConsent(
                    $request,
                    $unpaidDays,
                    'Terdapat tambahan hari cuti melahirkan di luar periode hak yang tidak dapat ditutup oleh cuti tahunan.'
                );
            }

            if ($user->join_date) {
                $oneYearDate = $user
                    ->join_date
                    ->copy()
                    ->addYear();

                $maternitySalaryStatus = $startDate->gte($oneYearDate)
                    ? 'paid_base_salary'
                    : 'unpaid';
            }
        }

        if ($permissionType->code === 'other') {
            $policyCoveredDays = 0;
            $excessDays = $totalDays;

            $allocation = $this->allocateExcessDays(
                user: $user,
                startDate: $startDate,
                endDate: $endDate,
                excessDays: $excessDays
            );

            $annualLeaveDeductedDays = $allocation['annual_leave_days'];
            $unpaidDays = $allocation['unpaid_days'];
            $leaveBalanceId = $allocation['leave_balance_id'];
            $excessHandling = $this->resolveExcessHandling(
                $annualLeaveDeductedDays,
                $unpaidDays
            );

            $this->ensureSalaryDeductionConsent(
                $request,
                $unpaidDays,
                'Izin lainnya tidak memiliki jatah hari khusus. Terdapat hari yang tidak dapat ditutup oleh cuti tahunan.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | PENGGANTI DINAMIS PER TANGGAL
        |--------------------------------------------------------------------------
        |
        | User boleh memilih hanya beberapa hari yang membutuhkan pengganti.
        | Hari yang tidak dicentang tidak akan dibuat row jadwal pengganti.
        |
        */
        $requestedHasSubstitute = (bool) $validated['has_substitute'];
        $substituteSchedules = [];

        if ($requestedHasSubstitute) {
            $substituteSchedules = $this->normalizeSubstituteSchedules(
                $validated['substitute_schedules'] ?? [],
                $startDate,
                $endDate
            );
        }

        /*
         * Nilai final ditentukan dari jadwal yang benar-benar terpilih.
         */
        $hasSubstitute = count($substituteSchedules) > 0;

        $documentPath = null;

        if ($request->hasFile('supporting_document')) {
            $documentPath = $request
                ->file('supporting_document')
                ->store('permission-documents', 'public');
        }

        /*
        |--------------------------------------------------------------------------
        | DATA RINGKASAN LEGACY DI leave_requests
        |--------------------------------------------------------------------------
        |
        | Kolom lama tetap dipertahankan supaya halaman/record lama tidak rusak.
        | Untuk pengajuan baru, data global diisi dari pengganti pada hari
        | terpilih pertama. Data lengkap tetap ada pada schedule per tanggal.
        |
        */
        $substituteData = [
            'has_substitute' => false,
            'substitute_name' => null,
            'substitute_whatsapp' => null,
            'substitute_address' => null,
            'substitute_bank_name' => null,
            'substitute_bank_account_number' => null,
            'substitute_bank_account_holder' => null,
        ];

        if ($hasSubstitute) {
            $firstSubstitute = $substituteSchedules[0];

            $substituteData = [
                'has_substitute' => true,
                'substitute_name' => $firstSubstitute['substitute_name'],
                'substitute_whatsapp' => $firstSubstitute['substitute_whatsapp'],
                'substitute_address' => $firstSubstitute['substitute_address'],
                'substitute_bank_name' => $firstSubstitute['substitute_bank_name'],
                'substitute_bank_account_number' => $firstSubstitute['substitute_bank_account_number'],
                'substitute_bank_account_holder' => $firstSubstitute['substitute_bank_account_holder'],
            ];
        }

        DB::transaction(
            function () use (
                $user,
                $permissionType,
                $leaveBalanceId,
                $startDate,
                $endDate,
                $totalDays,
                $validated,
                $policyCoveredDays,
                $excessDays,
                $excessHandling,
                $annualLeaveDeductedDays,
                $unpaidDays,
                $documentPath,
                $maternitySalaryStatus,
                $substituteData,
                $substituteSchedules,
                $request
            ) {
                $leaveRequest = LeaveRequest::create([
                    'user_id' => $user->id,
                    'permission_type_id' => $permissionType->id,
                    'leave_balance_id' => $leaveBalanceId,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'total_days' => $totalDays,
                    'reason' => $validated['reason'],
                    'policy_covered_days' => $policyCoveredDays,
                    'excess_days' => $excessDays,
                    'excess_handling' => $excessHandling,
                    'annual_leave_deducted_days' => $annualLeaveDeductedDays,
                    'unpaid_days' => $unpaidDays,
                    'salary_deduction_consent' =>
                    $unpaidDays > 0
                        && $request->boolean('salary_deduction_consent'),
                    'salary_deduction_consent_at' =>
                    $unpaidDays > 0
                        && $request->boolean('salary_deduction_consent')
                        ? now()
                        : null,
                    'supporting_document' => $documentPath,
                    'expected_delivery_date' =>
                    $permissionType->code === 'maternity'
                        ? $validated['expected_delivery_date']
                        : null,
                    'maternity_salary_status' => $maternitySalaryStatus,
                    ...$substituteData,
                    'status' => 'pending',
                ]);

                foreach ($substituteSchedules as $schedule) {
                    $leaveRequest
                        ->substituteSchedules()
                        ->create($schedule);
                }
            }
        );

        return redirect()
            ->route('karyawan.leave-requests.index')
            ->with(
                'success',
                'Pengajuan perizinan berhasil dikirim dan sedang menunggu persetujuan.'
            );
    }

    private function allocateExcessDays(
        User $user,
        Carbon $startDate,
        Carbon $endDate,
        int $excessDays
    ): array {
        $result = [
            'annual_leave_days' => 0,
            'unpaid_days' => max(0, $excessDays),
            'leave_balance_id' => null,
        ];

        if ($excessDays <= 0) {
            return $result;
        }

        if (! $user->join_date) {
            return $result;
        }

        $eligibleDate = $user
            ->join_date
            ->copy()
            ->addYear();

        if ($startDate->lt($eligibleDate)) {
            return $result;
        }

        if ($startDate->year !== $endDate->year) {
            return $result;
        }

        $balance = LeaveBalance::query()
            ->where('user_id', $user->id)
            ->where('year', $startDate->year)
            ->first();

        if (! $balance) {
            return $result;
        }

        $pendingDays = LeaveRequest::query()
            ->where('user_id', $user->id)
            ->where('leave_balance_id', $balance->id)
            ->where('status', 'pending')
            ->sum('annual_leave_deducted_days');

        $availableAnnualDays = max(
            0,
            $balance->quota_days
                - $balance->used_days
                - $pendingDays
        );

        $annualLeaveDays = min(
            $excessDays,
            $availableAnnualDays
        );

        $unpaidDays = max(
            0,
            $excessDays - $annualLeaveDays
        );

        return [
            'annual_leave_days' => $annualLeaveDays,
            'unpaid_days' => $unpaidDays,
            'leave_balance_id' =>
            $annualLeaveDays > 0
                ? $balance->id
                : null,
        ];
    }

    private function resolveExcessHandling(
        int $annualLeaveDays,
        int $unpaidDays
    ): string {
        if ($unpaidDays > 0) {
            return 'unpaid';
        }

        if ($annualLeaveDays > 0) {
            return 'annual_leave';
        }

        return 'none';
    }

    private function ensureSalaryDeductionConsent(
        Request $request,
        int $unpaidDays,
        string $context
    ): void {
        if (
            $unpaidDays > 0
            && ! $request->boolean('salary_deduction_consent')
        ) {
            throw ValidationException::withMessages([
                'salary_deduction_consent' =>
                "{$context} Terdapat {$unpaidDays} hari yang diajukan sebagai hari tidak dibayar / potong gaji. Silakan centang persetujuan.",
            ]);
        }
    }

    private function normalizeSubstituteSchedules(
        array $schedules,
        Carbon $startDate,
        Carbon $endDate
    ): array {
        $allowedDates = $this->getDateRange(
            $startDate,
            $endDate
        );

        $submittedDates = [];
        $normalized = [];

        foreach ($schedules as $index => $schedule) {
            $date = $schedule['schedule_date'] ?? null;

            if (! $date) {
                throw ValidationException::withMessages([
                    "substitute_schedules.{$index}.schedule_date" =>
                    'Tanggal jadwal pengganti wajib diisi.',
                ]);
            }

            if (! in_array($date, $allowedDates, true)) {
                throw ValidationException::withMessages([
                    'substitute_schedules' =>
                    "Tanggal jadwal {$date} berada di luar periode perizinan.",
                ]);
            }

            if (isset($submittedDates[$date])) {
                throw ValidationException::withMessages([
                    'substitute_schedules' =>
                    "Tanggal {$date} terduplikasi pada jadwal pengganti.",
                ]);
            }

            $submittedDates[$date] = true;

            /*
             * Hari tidak dicentang = tidak membutuhkan pengganti.
             * Tidak dibuat row pada database.
             */
            $selected = filter_var(
                $schedule['selected'] ?? false,
                FILTER_VALIDATE_BOOLEAN
            );

            if (! $selected) {
                continue;
            }

            $name = trim((string) ($schedule['substitute_name'] ?? ''));
            $whatsapp = trim((string) ($schedule['substitute_whatsapp'] ?? ''));
            $address = trim((string) ($schedule['substitute_address'] ?? ''));
            $bankName = trim((string) ($schedule['substitute_bank_name'] ?? ''));
            $accountNumber = trim((string) ($schedule['substitute_bank_account_number'] ?? ''));
            $accountHolder = trim((string) ($schedule['substitute_bank_account_holder'] ?? ''));
            $scheduleType = $schedule['schedule_type'] ?? null;

            if ($name === '') {
                throw ValidationException::withMessages([
                    'substitute_schedules' =>
                    "Nama pengganti untuk tanggal {$date} wajib diisi.",
                ]);
            }

            if ($whatsapp === '') {
                throw ValidationException::withMessages([
                    'substitute_schedules' =>
                    "WhatsApp pengganti untuk tanggal {$date} wajib diisi.",
                ]);
            }

            if ($address === '') {
                throw ValidationException::withMessages([
                    'substitute_schedules' =>
                    "Alamat pengganti untuk tanggal {$date} wajib diisi.",
                ]);
            }

            if ($bankName === '') {
                throw ValidationException::withMessages([
                    'substitute_schedules' =>
                    "Nama bank pengganti untuk tanggal {$date} wajib diisi.",
                ]);
            }

            if ($accountNumber === '') {
                throw ValidationException::withMessages([
                    'substitute_schedules' =>
                    "Nomor rekening pengganti untuk tanggal {$date} wajib diisi.",
                ]);
            }

            if ($accountHolder === '') {
                throw ValidationException::withMessages([
                    'substitute_schedules' =>
                    "Atas nama rekening untuk tanggal {$date} wajib diisi.",
                ]);
            }

            if (! in_array(
                $scheduleType,
                ['full_shift', 'partial_hours'],
                true
            )) {
                throw ValidationException::withMessages([
                    'substitute_schedules' =>
                    "Jenis jadwal pengganti tanggal {$date} belum dipilih.",
                ]);
            }

            if ($scheduleType === 'full_shift') {
                if (empty($schedule['work_shift_id'])) {
                    throw ValidationException::withMessages([
                        'substitute_schedules' =>
                        "Pilih shift pengganti untuk tanggal {$date}.",
                    ]);
                }

                $normalized[] = [
                    'schedule_date' => $date,
                    'substitute_name' => $name,
                    'substitute_whatsapp' => $whatsapp,
                    'substitute_address' => $address,
                    'substitute_bank_name' => $bankName,
                    'substitute_bank_account_number' => $accountNumber,
                    'substitute_bank_account_holder' => $accountHolder,
                    'schedule_type' => 'full_shift',
                    'work_shift_id' => $schedule['work_shift_id'],
                    'start_time' => null,
                    'end_time' => null,
                ];

                continue;
            }

            $startTime = $schedule['start_time'] ?? null;
            $endTime = $schedule['end_time'] ?? null;

            if (! $startTime || ! $endTime) {
                throw ValidationException::withMessages([
                    'substitute_schedules' =>
                    "Jam mulai dan selesai tanggal {$date} wajib diisi.",
                ]);
            }

            if ($endTime <= $startTime) {
                throw ValidationException::withMessages([
                    'substitute_schedules' =>
                    "Jam selesai tanggal {$date} harus setelah jam mulai.",
                ]);
            }

            $normalized[] = [
                'schedule_date' => $date,
                'substitute_name' => $name,
                'substitute_whatsapp' => $whatsapp,
                'substitute_address' => $address,
                'substitute_bank_name' => $bankName,
                'substitute_bank_account_number' => $accountNumber,
                'substitute_bank_account_holder' => $accountHolder,
                'schedule_type' => 'partial_hours',
                'work_shift_id' => null,
                'start_time' => $startTime,
                'end_time' => $endTime,
            ];
        }

        if (count($normalized) === 0) {
            throw ValidationException::withMessages([
                'substitute_schedules' =>
                'Pilih minimal satu hari yang membutuhkan pengganti.',
            ]);
        }

        usort(
            $normalized,
            fn(array $a, array $b) =>
            strcmp($a['schedule_date'], $b['schedule_date'])
        );

        return $normalized;
    }

    private function getDateRange(
        Carbon $startDate,
        Carbon $endDate
    ): array {
        $dates = [];
        $cursor = $startDate->copy();

        while ($cursor->lte($endDate)) {
            $dates[] = $cursor->format('Y-m-d');
            $cursor->addDay();
        }

        return $dates;
    }

    private function ensureAnnualLeaveEligible(
        User $user,
        Carbon $date,
        string $errorField = 'permission_type_id'
    ): void {
        if (! $user->join_date) {
            throw ValidationException::withMessages([
                $errorField =>
                'Tanggal mulai kerja belum tersedia sehingga hak cuti tahunan belum dapat digunakan.',
            ]);
        }

        $eligibleDate = $user
            ->join_date
            ->copy()
            ->addYear();

        if ($date->lt($eligibleDate)) {
            throw ValidationException::withMessages([
                $errorField =>
                'Anda belum memenuhi masa kerja 12 bulan sehingga belum dapat menggunakan cuti tahunan. Hak cuti tahunan mulai tersedia pada '
                    . $eligibleDate->format('d/m/Y')
                    . '.',
            ]);
        }
    }

    private function resolveAnnualBalance(
        int $userId,
        int $year,
        int $requestedDays,
        string $errorField = 'permission_type_id'
    ): LeaveBalance {
        $balance = LeaveBalance::query()
            ->where('user_id', $userId)
            ->where('year', $year)
            ->first();

        if (! $balance) {
            throw ValidationException::withMessages([
                $errorField =>
                "Jatah cuti tahunan {$year} belum tersedia.",
            ]);
        }

        $pendingDays = LeaveRequest::query()
            ->where('user_id', $userId)
            ->where('leave_balance_id', $balance->id)
            ->where('status', 'pending')
            ->sum('annual_leave_deducted_days');

        $availableDays =
            $balance->quota_days
            - $balance->used_days
            - $pendingDays;

        if ($requestedDays > $availableDays) {
            throw ValidationException::withMessages([
                $errorField =>
                "Membutuhkan {$requestedDays} hari cuti tahunan, sedangkan sisa yang dapat diajukan hanya {$availableDays} hari.",
            ]);
        }

        return $balance;
    }

    private function countAnnualWorkDays(
        Carbon $startDate,
        Carbon $endDate
    ): int {
        $cursor = $startDate->copy();
        $total = 0;

        while ($cursor->lte($endDate)) {
            if (! $cursor->isSunday()) {
                $total++;
            }

            $cursor->addDay();
        }

        return $total;
    }

    private function ensureKaryawan(): void
    {
        /** @var User|null $user */
        $user = Auth::user();

        abort_unless(
            $user !== null
                && $user->role === 'karyawan',
            403
        );
    }
}
