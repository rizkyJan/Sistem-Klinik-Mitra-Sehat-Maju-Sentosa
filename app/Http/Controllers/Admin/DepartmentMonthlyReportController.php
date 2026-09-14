<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\DepartmentMonthlyReport;
use Carbon\Carbon;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\File;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class DepartmentMonthlyReportController extends Controller
{
    public function index(Request $request): View
    {
        $period = $this->resolvePeriod($request->query('period'));
        $periodDate = $period->toDateString();
        $search = trim((string) $request->query('search', ''));
        $statusFilter = (string) $request->query('status', 'all');

        $departments = Department::query()
            ->where('is_active', true)
            ->with([
                'users' => function ($query): void {
                    $query
                        ->where('role', 'kabid')
                        ->where('approval_status', 'approved')
                        ->where('is_active', true)
                        ->select(['id', 'name', 'nip', 'department_id']);
                },
                'monthlyReports' => function ($query) use ($periodDate): void {
                    $query
                        ->whereDate('period', $periodDate)
                        ->with([
                            'submittedBy:id,name',
                            'verifiedBy:id,name',
                        ]);
                },
            ])
            ->orderBy('name')
            ->get();

        $totalDepartmentCount = $departments->count();
        $submittedCount = $departments->filter(fn ($department) => $department->monthlyReports->isNotEmpty())->count();
        $missingCount = max(0, $totalDepartmentCount - $submittedCount);
        $pendingVerificationCount = $departments->filter(
            fn ($department) => $department->monthlyReports->first()?->status === DepartmentMonthlyReport::STATUS_SUBMITTED
        )->count();
        $revisionCount = $departments->filter(
            fn ($department) => $department->monthlyReports->first()?->status === DepartmentMonthlyReport::STATUS_REVISION
        )->count();
        $verifiedCount = $departments->filter(
            fn ($department) => $department->monthlyReports->first()?->status === DepartmentMonthlyReport::STATUS_VERIFIED
        )->count();

        $departments = $departments
            ->filter(function ($department) use ($search, $statusFilter): bool {
                $report = $department->monthlyReports->first();
                $kabid = $department->users->first();

                if ($search !== '') {
                    $haystack = strtolower(
                        $department->name . ' ' . ($kabid?->name ?? '') . ' ' . ($kabid?->nip ?? '')
                    );

                    if (! str_contains($haystack, strtolower($search))) {
                        return false;
                    }
                }

                return match ($statusFilter) {
                    'missing' => $report === null,
                    DepartmentMonthlyReport::STATUS_SUBMITTED => $report?->status === DepartmentMonthlyReport::STATUS_SUBMITTED,
                    DepartmentMonthlyReport::STATUS_REVISION => $report?->status === DepartmentMonthlyReport::STATUS_REVISION,
                    DepartmentMonthlyReport::STATUS_VERIFIED => $report?->status === DepartmentMonthlyReport::STATUS_VERIFIED,
                    default => true,
                };
            })
            ->values();

        return view('admin.monthly-reports.index', [
            'period' => $period,
            'departments' => $departments,
            'totalDepartmentCount' => $totalDepartmentCount,
            'submittedCount' => $submittedCount,
            'missingCount' => $missingCount,
            'pendingVerificationCount' => $pendingVerificationCount,
            'revisionCount' => $revisionCount,
            'verifiedCount' => $verifiedCount,
            'search' => $search,
            'statusFilter' => $statusFilter,
        ]);
    }

    public function show(DepartmentMonthlyReport $monthlyReport): View
    {
        $monthlyReport->load([
            'department',
            'submittedBy:id,name,nip',
            'verifiedBy:id,name',
            'adminEditedBy:id,name',
        ]);

        return view('admin.monthly-reports.show', [
            'report' => $monthlyReport,
        ]);
    }

    public function edit(DepartmentMonthlyReport $monthlyReport): View
    {
        $monthlyReport->load(['department', 'submittedBy:id,name']);

        return view('admin.monthly-reports.edit', [
            'report' => $monthlyReport,
            'allowedExtensions' => config('monthly-report.allowed_extensions', []),
            'maxFileSizeKb' => (int) config('monthly-report.max_file_size_kb', 10240),
        ]);
    }

    public function update(
        Request $request,
        DepartmentMonthlyReport $monthlyReport
    ): RedirectResponse {
        $maxKb = (int) config('monthly-report.max_file_size_kb', 10240);
        $extensions = config('monthly-report.allowed_extensions', [
            'pdf', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png',
        ]);

        $validated = $request->validate([
            'report_file' => [
                'nullable',
                File::types($extensions)->max($maxKb . 'kb'),
            ],
            'employee_note' => ['nullable', 'string', 'max:3000'],
            'admin_note' => ['nullable', 'string', 'max:5000'],
        ], [
            'employee_note.max' => 'Catatan Kabid maksimal 3.000 karakter.',
            'admin_note.max' => 'Catatan Admin maksimal 5.000 karakter.',
        ]);

        /** @var UploadedFile|null $uploadedFile */
        $uploadedFile = $request->file('report_file');

        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk('local');

        $newPath = null;
        $oldPath = null;

        if ($uploadedFile) {
            $newPath = $uploadedFile->store(
                'department-monthly-reports/'
                    . $monthlyReport->department_id
                    . '/'
                    . $monthlyReport->period->format('Y-m'),
                'local'
            );
        }

        try {
            DB::transaction(function () use (
                $request,
                $monthlyReport,
                $validated,
                $uploadedFile,
                $newPath,
                &$oldPath
            ): void {
                $locked = DepartmentMonthlyReport::query()
                    ->lockForUpdate()
                    ->findOrFail($monthlyReport->id);

                $updates = [
                    'employee_note' => filled($validated['employee_note'] ?? null)
                        ? trim($validated['employee_note'])
                        : null,
                    'admin_note' => filled($validated['admin_note'] ?? null)
                        ? trim($validated['admin_note'])
                        : null,
                    'admin_edited_at' => now(config('monthly-report.timezone', 'Asia/Jakarta')),
                    'admin_edited_by' => $request->user()->id,
                ];

                if ($uploadedFile && $newPath) {
                    $oldPath = $locked->file_path;
                    $updates = [
                        ...$updates,
                        'file_path' => $newPath,
                        'original_name' => $uploadedFile->getClientOriginalName(),
                        'mime' => $uploadedFile->getMimeType(),
                        'size' => $uploadedFile->getSize(),
                    ];
                }

                $locked->update($updates);
            });
        } catch (Throwable $exception) {
            if ($newPath) {
                $disk->delete($newPath);
            }

            throw $exception;
        }

        if ($oldPath && $oldPath !== $newPath) {
            $disk->delete($oldPath);
        }

        return redirect()
            ->route('admin.monthly-reports.show', $monthlyReport)
            ->with('success', 'Data laporan bulanan berhasil diperbarui oleh Admin.');
    }

    public function verify(
        Request $request,
        DepartmentMonthlyReport $monthlyReport
    ): RedirectResponse {
        DB::transaction(function () use ($request, $monthlyReport): void {
            $locked = DepartmentMonthlyReport::query()
                ->lockForUpdate()
                ->findOrFail($monthlyReport->id);

            if ($locked->status === DepartmentMonthlyReport::STATUS_VERIFIED) {
                return;
            }

            $locked->update([
                'status' => DepartmentMonthlyReport::STATUS_VERIFIED,
                'revision_note' => null,
                'verified_at' => now(config('monthly-report.timezone', 'Asia/Jakarta')),
                'verified_by' => $request->user()->id,
            ]);
        });

        return back()->with('success', 'Laporan bulanan berhasil diverifikasi.');
    }

    public function requestRevision(
        Request $request,
        DepartmentMonthlyReport $monthlyReport
    ): RedirectResponse {
        $validated = $request->validate([
            'revision_note' => ['required', 'string', 'min:3', 'max:5000'],
        ], [
            'revision_note.required' => 'Catatan revisi wajib diisi.',
            'revision_note.min' => 'Catatan revisi minimal 3 karakter.',
            'revision_note.max' => 'Catatan revisi maksimal 5.000 karakter.',
        ]);

        DB::transaction(function () use ($request, $monthlyReport, $validated): void {
            $locked = DepartmentMonthlyReport::query()
                ->lockForUpdate()
                ->findOrFail($monthlyReport->id);

            $locked->update([
                'status' => DepartmentMonthlyReport::STATUS_REVISION,
                'revision_note' => trim($validated['revision_note']),
                'verified_at' => null,
                'verified_by' => null,
                'admin_note' => $locked->admin_note,
                'admin_edited_at' => now(config('monthly-report.timezone', 'Asia/Jakarta')),
                'admin_edited_by' => $request->user()->id,
            ]);
        });

        return back()->with(
            'success',
            'Permintaan revisi berhasil dikirim. Fitur operasional Kabid bidang tersebut akan terkunci sampai laporan dikirim ulang.'
        );
    }

    public function preview(DepartmentMonthlyReport $monthlyReport): StreamedResponse
    {
        return $this->fileResponse($monthlyReport, 'inline');
    }

    public function download(DepartmentMonthlyReport $monthlyReport): StreamedResponse
    {
        return $this->fileResponse($monthlyReport, 'attachment');
    }

    private function fileResponse(
        DepartmentMonthlyReport $monthlyReport,
        string $disposition
    ): StreamedResponse {
        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk('local');

        abort_unless(
            filled($monthlyReport->file_path)
                && $disk->exists($monthlyReport->file_path),
            404
        );

        return $disk->response(
            $monthlyReport->file_path,
            $monthlyReport->original_name,
            [
                'Content-Type' => $monthlyReport->mime ?: 'application/octet-stream',
                'X-Content-Type-Options' => 'nosniff',
                'Cache-Control' => 'private, no-store, max-age=0',
            ],
            $disposition
        );
    }

    private function resolvePeriod(mixed $value): Carbon
    {
        if (is_string($value) && preg_match('/^\d{4}-\d{2}$/', $value)) {
            try {
                return Carbon::createFromFormat(
                    'Y-m-d',
                    $value . '-01',
                    config('monthly-report.timezone', 'Asia/Jakarta')
                )->startOfMonth();
            } catch (Throwable) {
                // Jatuh ke periode bulan berjalan.
            }
        }

        return Carbon::now(config('monthly-report.timezone', 'Asia/Jakarta'))
            ->startOfMonth();
    }
}
