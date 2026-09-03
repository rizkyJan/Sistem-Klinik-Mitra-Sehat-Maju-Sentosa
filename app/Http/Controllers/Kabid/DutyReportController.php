<?php

namespace App\Http\Controllers\Kabid;

use App\Http\Controllers\Controller;
use App\Models\DutyAssignment;
use App\Models\DutyLetter;
use App\Models\DutyReport;
use App\Models\DutyReportFile;
use App\Models\User;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class DutyReportController extends Controller
{
    /**
     * Form membuat / memperbaiki laporan hasil dinas.
     */
    public function edit(DutyAssignment $dutyAssignment): View
    {
        $this->ensureOwner($dutyAssignment);

        $dutyAssignment->load([
            'dutyLetter.creator',
            'report.files',
        ]);

        $this->ensureReportCanBeWritten($dutyAssignment);

        return view(
            'kabid.duty-letters.report',
            compact('dutyAssignment')
        );
    }


    /**
     * Menyimpan laporan lalu langsung mengirimkannya ke Admin.
     *
     * Tidak ada status draft pada workflow Surat Dinas.
     * Saat tombol "Kirim Laporan" ditekan, status assignment berubah
     * menjadi submitted / Menunggu Verifikasi.
     */
    public function store(
        Request $request,
        DutyAssignment $dutyAssignment
    ): RedirectResponse {
        $this->ensureOwner($dutyAssignment);

        $dutyAssignment->load([
            'dutyLetter',
            'report.files',
        ]);

        $this->ensureReportCanBeWritten($dutyAssignment);

        $existingPhotoCount = $dutyAssignment->report?->files->count() ?? 0;

        $newPhotos = $request->file('photos', []);

        if (! is_array($newPhotos)) {
            $newPhotos = [];
        }

        $validator = Validator::make(
            $request->all(),
            [
                'discussion_summary' => [
                    'required',
                    'string',
                    'min:20',
                    'max:10000',
                ],
                'result_summary' => [
                    'required',
                    'string',
                    'min:20',
                    'max:10000',
                ],
                'follow_up' => [
                    'nullable',
                    'string',
                    'max:10000',
                ],
                'additional_notes' => [
                    'nullable',
                    'string',
                    'max:10000',
                ],
                'photos' => [
                    'nullable',
                    'array',
                    'max:5',
                ],
                'photos.*' => [
                    'file',
                    'image',
                    'mimes:jpg,jpeg,png,webp',
                    'max:5120',
                ],
            ],
            [
                'discussion_summary.required' =>
                'Pokok pembahasan wajib diisi.',
                'discussion_summary.min' =>
                'Pokok pembahasan minimal 20 karakter.',
                'result_summary.required' =>
                'Hasil / kesimpulan wajib diisi.',
                'result_summary.min' =>
                'Hasil / kesimpulan minimal 20 karakter.',
                'photos.array' =>
                'Format dokumentasi tidak valid.',
                'photos.max' =>
                'Maksimal 5 foto dapat diunggah.',
                'photos.*.image' =>
                'Dokumentasi harus berupa file gambar.',
                'photos.*.mimes' =>
                'Foto harus berformat JPG, JPEG, PNG, atau WEBP.',
                'photos.*.max' =>
                'Ukuran setiap foto maksimal 5 MB.',
            ]
        );

        $validator->after(
            function ($validator) use (
                $existingPhotoCount,
                $newPhotos
            ) {
                $totalPhotos =
                    $existingPhotoCount + count($newPhotos);

                if ($totalPhotos < 1) {
                    $validator->errors()->add(
                        'photos',
                        'Minimal 1 foto bukti kehadiran wajib tersedia.'
                    );
                }

                if ($totalPhotos > 5) {
                    $validator->errors()->add(
                        'photos',
                        'Total dokumentasi maksimal 5 foto.'
                    );
                }
            }
        );

        $validated = $validator->validate();

        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk('local');

        $storedPaths = [];

        DB::beginTransaction();

        try {
            $report = DutyReport::query()->updateOrCreate(
                [
                    'duty_assignment_id' =>
                    $dutyAssignment->id,
                ],
                [
                    'discussion_summary' =>
                    $validated['discussion_summary'],
                    'result_summary' =>
                    $validated['result_summary'],
                    'follow_up' =>
                    $validated['follow_up'] ?? null,
                    'additional_notes' =>
                    $validated['additional_notes'] ?? null,
                ]
            );

            /** @var UploadedFile $photo */
            foreach ($newPhotos as $photo) {
                $path = $photo->store(
                    'duty-reports/' . $report->id,
                    'local'
                );

                $storedPaths[] = $path;

                $report->files()->create([
                    'file_path' => $path,
                    'original_name' =>
                    $photo->getClientOriginalName(),
                    'mime' =>
                    $photo->getMimeType(),
                    'size' =>
                    $photo->getSize(),
                    'caption' => null,
                ]);
            }

            $dutyAssignment->update([
                'report_status' =>
                DutyAssignment::REPORT_SUBMITTED,

                'report_submitted_at' => now(),

                // Reset data verifikasi lama ketika laporan
                // dikirim ulang setelah revisi.
                'report_verified_at' => null,
                'report_verified_by' => null,
                'revision_note' => null,
            ]);

            DB::commit();
        } catch (Throwable $exception) {
            DB::rollBack();

            foreach ($storedPaths as $path) {
                $disk->delete($path);
            }

            throw $exception;
        }

        return redirect()
            ->route(
                'kabid.duty-letters.show',
                $dutyAssignment
            )
            ->with(
                'success',
                'Laporan hasil dinas berhasil dikirim dan menunggu verifikasi Admin.'
            );
    }


    /**
     * Menampilkan foto bukti kehadiran dari storage private.
     */
    public function file(
        DutyAssignment $dutyAssignment,
        DutyReportFile $dutyReportFile
    ): StreamedResponse {
        $this->ensureOwner($dutyAssignment);

        $this->ensureFileBelongsToAssignment(
            $dutyAssignment,
            $dutyReportFile
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
     * Menghapus foto saat laporan masih dapat diedit.
     *
     * Setelah laporan dikirim (submitted) atau diverifikasi,
     * foto tidak dapat dihapus.
     */
    public function destroyFile(
        DutyAssignment $dutyAssignment,
        DutyReportFile $dutyReportFile
    ): RedirectResponse {
        $this->ensureOwner($dutyAssignment);

        $dutyAssignment->loadMissing([
            'dutyLetter',
            'report.files',
        ]);

        $this->ensureReportCanBeWritten($dutyAssignment);

        $this->ensureFileBelongsToAssignment(
            $dutyAssignment,
            $dutyReportFile
        );

        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk('local');

        $path = $dutyReportFile->file_path;

        $dutyReportFile->delete();

        if (! empty($path)) {
            $disk->delete($path);
        }

        return back()->with(
            'success',
            'Foto dokumentasi berhasil dihapus.'
        );
    }


    /**
     * Memastikan assignment benar-benar milik user login.
     */
    private function ensureOwner(
        DutyAssignment $dutyAssignment
    ): void {
        $this->ensureRole();

        abort_unless(
            $dutyAssignment->user_id === Auth::id(),
            403
        );
    }


    /**
     * Aturan kapan laporan boleh dibuat / diedit.
     */
    private function ensureReportCanBeWritten(
        DutyAssignment $dutyAssignment
    ): void {
        $dutyAssignment->loadMissing('dutyLetter');

        $letter = $dutyAssignment->dutyLetter;

        abort_if($letter === null, 404);

        // Surat yang dibatalkan tidak boleh dibuatkan laporan.
        abort_unless(
            $letter->status === DutyLetter::STATUS_PUBLISHED,
            403
        );

        // Laporan baru dapat dikirim mulai tanggal kegiatan.
        abort_if(
            $letter->event_date !== null
                && $letter->event_date->isFuture(),
            403
        );

        // Saat submitted, tunggu keputusan Admin.
        // Saat verified, laporan sudah final.
        abort_unless(
            in_array(
                $dutyAssignment->report_status,
                [
                    DutyAssignment::REPORT_PENDING,
                    DutyAssignment::REPORT_REVISION,
                ],
                true
            ),
            403
        );
    }


    /**
     * Mencegah user mengakses file laporan assignment orang lain.
     */
    private function ensureFileBelongsToAssignment(
        DutyAssignment $dutyAssignment,
        DutyReportFile $dutyReportFile
    ): void {
        $dutyAssignment->loadMissing('report');

        abort_unless(
            $dutyAssignment->report !== null
                && $dutyReportFile->duty_report_id
                === $dutyAssignment->report->id,
            404
        );
    }


    /**
     * Pertahanan tambahan di luar middleware route.
     */
    private function ensureRole(): void
    {
        /** @var User|null $user */
        $user = Auth::user();

        abort_unless(
            $user !== null
                && $user->role === 'kabid',
            403
        );
    }
}
