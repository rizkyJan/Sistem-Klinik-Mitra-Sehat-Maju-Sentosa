<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DutyAssignment;
use App\Models\DutyLetter;
use App\Models\DutyReportFile;
use App\Models\User;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DutyReportController extends Controller
{
    /**
     * Menampilkan laporan hasil dinas milik satu penerima.
     */
    public function show(
        DutyLetter $dutyLetter,
        DutyAssignment $dutyAssignment
    ): View {
        $this->ensureAdmin();
        $this->ensureAssignmentBelongsToLetter(
            $dutyLetter,
            $dutyAssignment
        );

        $dutyAssignment->load([
            'dutyLetter.creator',
            'user.department',
            'report.files',
            'reportVerifier',
            'feeConfirmer',
        ]);

        abort_if($dutyAssignment->report === null, 404);

        return view(
            'admin.duty-reports.show',
            compact('dutyLetter', 'dutyAssignment')
        );
    }

    /**
     * Menampilkan foto bukti kehadiran dari storage private.
     */
    public function file(
        DutyLetter $dutyLetter,
        DutyAssignment $dutyAssignment,
        DutyReportFile $dutyReportFile
    ): StreamedResponse {
        $this->ensureAdmin();
        $this->ensureAssignmentBelongsToLetter(
            $dutyLetter,
            $dutyAssignment
        );

        $dutyAssignment->loadMissing('report');

        abort_unless(
            $dutyAssignment->report !== null
                && $dutyReportFile->duty_report_id
                === $dutyAssignment->report->id,
            404
        );

        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk('local');

        abort_unless(
            ! empty($dutyReportFile->file_path)
                && $disk->exists($dutyReportFile->file_path),
            404
        );

        return $disk->response(
            $dutyReportFile->file_path,
            $dutyReportFile->original_name,
            [
                'Content-Type' =>
                $dutyReportFile->mime
                    ?: 'application/octet-stream',
                'X-Content-Type-Options' => 'nosniff',
                'Cache-Control' =>
                'private, no-store, max-age=0',
            ],
            'inline'
        );
    }

    /**
     * Admin menyetujui laporan yang sudah dikirim pegawai.
     */
    public function verify(
        DutyLetter $dutyLetter,
        DutyAssignment $dutyAssignment
    ): RedirectResponse {
        $this->ensureAdmin();
        $this->ensureAssignmentBelongsToLetter(
            $dutyLetter,
            $dutyAssignment
        );

        DB::transaction(function () use ($dutyAssignment) {
            $locked = DutyAssignment::query()
                ->lockForUpdate()
                ->with('report')
                ->findOrFail($dutyAssignment->id);

            if ($locked->report === null) {
                abort(404);
            }

            if (
                $locked->report_status
                !== DutyAssignment::REPORT_SUBMITTED
            ) {
                abort(409, 'Laporan sudah tidak menunggu verifikasi.');
            }

            $locked->update([
                'report_status' =>
                DutyAssignment::REPORT_VERIFIED,
                'report_verified_at' => now(),
                'report_verified_by' => Auth::id(),
                'revision_note' => null,
            ]);
        });

        return redirect()
            ->route(
                'admin.duty-reports.show',
                [$dutyLetter, $dutyAssignment]
            )
            ->with(
                'success',
                'Laporan hasil dinas berhasil diverifikasi.'
            );
    }

    /**
     * Admin meminta pegawai memperbaiki laporan.
     */
    public function requestRevision(
        Request $request,
        DutyLetter $dutyLetter,
        DutyAssignment $dutyAssignment
    ): RedirectResponse {
        $this->ensureAdmin();
        $this->ensureAssignmentBelongsToLetter(
            $dutyLetter,
            $dutyAssignment
        );

        $validated = $request->validate(
            [
                'revision_note' => [
                    'required',
                    'string',
                    'min:5',
                    'max:2000',
                ],
            ],
            [
                'revision_note.required' =>
                'Catatan perbaikan wajib diisi.',
                'revision_note.min' =>
                'Catatan perbaikan minimal 5 karakter.',
                'revision_note.max' =>
                'Catatan perbaikan maksimal 2.000 karakter.',
            ]
        );

        DB::transaction(function () use (
            $dutyAssignment,
            $validated
        ) {
            $locked = DutyAssignment::query()
                ->lockForUpdate()
                ->with('report')
                ->findOrFail($dutyAssignment->id);

            if ($locked->report === null) {
                abort(404);
            }

            if (
                $locked->report_status
                !== DutyAssignment::REPORT_SUBMITTED
            ) {
                abort(409, 'Laporan sudah tidak menunggu verifikasi.');
            }

            $locked->update([
                'report_status' =>
                DutyAssignment::REPORT_REVISION,
                'revision_note' =>
                $validated['revision_note'],
                'report_verified_at' => null,
                'report_verified_by' => null,
            ]);
        });

        return redirect()
            ->route(
                'admin.duty-reports.show',
                [$dutyLetter, $dutyAssignment]
            )
            ->with(
                'warning',
                'Laporan dikembalikan kepada pegawai untuk diperbaiki.'
            );
    }

    /**
     * Memastikan assignment memang berasal dari surat pada URL.
     */
    private function ensureAssignmentBelongsToLetter(
        DutyLetter $dutyLetter,
        DutyAssignment $dutyAssignment
    ): void {
        abort_unless(
            $dutyAssignment->duty_letter_id === $dutyLetter->id,
            404
        );
    }

    /**
     * Pertahanan tambahan selain middleware role:admin.
     */
    private function ensureAdmin(): void
    {
        /** @var User|null $user */
        $user = Auth::user();

        abort_unless(
            $user !== null && $user->role === 'admin',
            403
        );
    }
}
