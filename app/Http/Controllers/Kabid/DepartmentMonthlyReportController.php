<?php

namespace App\Http\Controllers\Kabid;

use App\Http\Controllers\Controller;
use App\Models\DepartmentMonthlyReport;
use App\Models\User;
use App\Services\DepartmentMonthlyReportRequirementService;
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
    public function __construct(
        private readonly DepartmentMonthlyReportRequirementService $requirements
    ) {}

    public function index(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();
        $this->ensureKabid($user);

        $user->load('department');
        $status = $this->requirements->statusFor($user);

        $history = DepartmentMonthlyReport::query()
            ->with(['verifiedBy:id,name', 'adminEditedBy:id,name'])
            ->where('department_id', $user->department_id)
            ->orderByDesc('period')
            ->paginate(12)
            ->withQueryString();

        return view('kabid.monthly-reports.index', [
            'user' => $user,
            'status' => $status,
            'currentReport' => $status['report'],
            'history' => $history,
            'allowedExtensions' => config('monthly-report.allowed_extensions', []),
            'maxFileSizeKb' => (int) config('monthly-report.max_file_size_kb', 10240),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        $this->ensureKabid($user);

        if (! $user->department_id) {
            return back()->with(
                'error',
                'Akun Kabid belum memiliki bidang. Hubungi Admin sebelum mengunggah laporan.'
            );
        }

        $requirementStatus = $this->requirements->statusFor($user);

        if (! $requirementStatus['window_open']) {
            return back()->with(
                'warning',
                'Belum ada periode laporan yang jatuh tempo. Periode pertama fitur mengikuti konfigurasi MONTHLY_REPORT_START_PERIOD.'
            );
        }

        if (! $requirementStatus['locked']) {
            return back()->with('info', 'Tidak ada laporan bulanan yang perlu dikirim saat ini.');
        }

        $maxKb = (int) config('monthly-report.max_file_size_kb', 10240);
        $extensions = config('monthly-report.allowed_extensions', [
            'pdf', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png',
        ]);

        $validated = $request->validate([
            'report_file' => [
                'required',
                File::types($extensions)->max($maxKb . 'kb'),
            ],
            'employee_note' => ['nullable', 'string', 'max:3000'],
        ], [
            'report_file.required' => 'File laporan wajib dipilih.',
            'report_file.max' => 'Ukuran file laporan terlalu besar.',
            'employee_note.max' => 'Catatan maksimal 3.000 karakter.',
        ]);

        /** @var UploadedFile $uploadedFile */
        $uploadedFile = $request->file('report_file');
        $period = $requirementStatus['period'];

        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk('local');

        $newPath = $uploadedFile->store(
            'department-monthly-reports/'
                . $user->department_id
                . '/'
                . $period->format('Y-m'),
            'local'
        );

        $oldPath = null;

        try {
            DB::transaction(function () use (
                $user,
                $period,
                $uploadedFile,
                $newPath,
                $validated,
                &$oldPath
            ): void {
                $existing = DepartmentMonthlyReport::query()
                    ->where('department_id', $user->department_id)
                    ->whereDate('period', $period->toDateString())
                    ->lockForUpdate()
                    ->first();

                if (
                    $existing
                    && $existing->status !== DepartmentMonthlyReport::STATUS_REVISION
                ) {
                    abort(
                        409,
                        'Laporan periode ini sudah dikirim dan tidak dapat diunggah ulang kecuali Admin meminta revisi.'
                    );
                }

                if ($existing) {
                    $oldPath = $existing->file_path;

                    $existing->update([
                        'submitted_by' => $user->id,
                        'file_path' => $newPath,
                        'original_name' => $uploadedFile->getClientOriginalName(),
                        'mime' => $uploadedFile->getMimeType(),
                        'size' => $uploadedFile->getSize(),
                        'employee_note' => filled($validated['employee_note'] ?? null)
                            ? trim($validated['employee_note'])
                            : null,
                        'status' => DepartmentMonthlyReport::STATUS_SUBMITTED,
                        'revision_note' => null,
                        'submitted_at' => now(config('monthly-report.timezone', 'Asia/Jakarta')),
                        'verified_at' => null,
                        'verified_by' => null,
                    ]);

                    return;
                }

                DepartmentMonthlyReport::create([
                    'department_id' => $user->department_id,
                    'period' => $period->toDateString(),
                    'submitted_by' => $user->id,
                    'file_path' => $newPath,
                    'original_name' => $uploadedFile->getClientOriginalName(),
                    'mime' => $uploadedFile->getMimeType(),
                    'size' => $uploadedFile->getSize(),
                    'employee_note' => filled($validated['employee_note'] ?? null)
                        ? trim($validated['employee_note'])
                        : null,
                    'status' => DepartmentMonthlyReport::STATUS_SUBMITTED,
                    'submitted_at' => now(config('monthly-report.timezone', 'Asia/Jakarta')),
                ]);
            });
        } catch (Throwable $exception) {
            $disk->delete($newPath);
            throw $exception;
        }

        if ($oldPath && $oldPath !== $newPath) {
            $disk->delete($oldPath);
        }

        $freshStatus = $this->requirements->statusFor($user);

        $message = $freshStatus['locked']
            ? 'Laporan periode ini berhasil dikirim. Masih ada ' . $freshStatus['obligation_count'] . ' kewajiban laporan lain yang harus diselesaikan.'
            : 'Laporan bulanan berhasil dikirim. Fitur operasional sudah dapat digunakan sambil menunggu verifikasi Admin.';

        return redirect()
            ->route('kabid.monthly-reports.index')
            ->with('success', $message);
    }

    public function preview(
        Request $request,
        DepartmentMonthlyReport $monthlyReport
    ): StreamedResponse {
        $this->ensureReportOwner($request, $monthlyReport);

        return $this->fileResponse($monthlyReport, 'inline');
    }

    public function download(
        Request $request,
        DepartmentMonthlyReport $monthlyReport
    ): StreamedResponse {
        $this->ensureReportOwner($request, $monthlyReport);

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

    private function ensureReportOwner(
        Request $request,
        DepartmentMonthlyReport $monthlyReport
    ): void {
        /** @var User $user */
        $user = $request->user();
        $this->ensureKabid($user);

        abort_unless(
            $user->department_id
                && $monthlyReport->department_id === $user->department_id,
            403
        );
    }

    private function ensureKabid(User $user): void
    {
        abort_unless(
            $user->role === 'kabid'
                && $user->approval_status === 'approved'
                && $user->is_active,
            403
        );
    }
}
