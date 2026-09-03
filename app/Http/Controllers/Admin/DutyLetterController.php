<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DutyAssignment;
use App\Models\DutyLetter;
use App\Models\User;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DutyLetterController extends Controller
{
    /**
     * Menampilkan seluruh surat dinas yang dibuat Admin.
     *
     * View akan dibuat pada STEP 3.
     */
    public function index(Request $request): View
    {
        $this->ensureAdmin();

        $search = trim((string) $request->input('search', ''));
        $status = $request->input('status');

        if (! in_array(
            $status,
            [
                DutyLetter::STATUS_PUBLISHED,
                DutyLetter::STATUS_CANCELLED,
            ],
            true
        )) {
            $status = null;
        }

        $dutyLetters = DutyLetter::query()
            ->with([
                'creator',
                'assignments.user.department',
            ])
            ->withCount('assignments')
            ->when(
                $status,
                fn($query, $status) =>
                $query->where('status', $status)
            )
            ->when(
                $search !== '',
                function ($query) use ($search) {
                    $query->where(
                        function ($query) use ($search) {
                            $query
                                ->where(
                                    'letter_number',
                                    'like',
                                    '%' . $search . '%'
                                )
                                ->orWhere(
                                    'title',
                                    'like',
                                    '%' . $search . '%'
                                )
                                ->orWhere(
                                    'organizer',
                                    'like',
                                    '%' . $search . '%'
                                )
                                ->orWhere(
                                    'location_name',
                                    'like',
                                    '%' . $search . '%'
                                )
                                ->orWhereHas(
                                    'assignments',
                                    function ($assignmentQuery) use ($search) {
                                        $assignmentQuery->where(
                                            'assignee_name',
                                            'like',
                                            '%' . $search . '%'
                                        );
                                    }
                                );
                        }
                    );
                }
            )
            ->orderByDesc('event_date')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        $publishedCount = DutyLetter::query()
            ->where(
                'status',
                DutyLetter::STATUS_PUBLISHED
            )
            ->count();

        $upcomingCount = DutyLetter::query()
            ->where(
                'status',
                DutyLetter::STATUS_PUBLISHED
            )
            ->whereDate(
                'event_date',
                '>=',
                today()
            )
            ->count();

        $waitingReportCount = DutyAssignment::query()
            ->whereHas(
                'dutyLetter',
                fn($query) =>
                $query->where(
                    'status',
                    DutyLetter::STATUS_PUBLISHED
                )
            )
            ->where(
                'report_status',
                DutyAssignment::REPORT_PENDING
            )
            ->count();

        $cancelledCount = DutyLetter::query()
            ->where(
                'status',
                DutyLetter::STATUS_CANCELLED
            )
            ->count();

        return view(
            'admin.duty-letters.index',
            compact(
                'dutyLetters',
                'publishedCount',
                'upcomingCount',
                'waitingReportCount',
                'cancelledCount'
            )
        );
    }


    /**
     * Menampilkan form pembuatan Surat Dinas.
     *
     * Hanya Karyawan/Kabid aktif dan sudah disetujui Admin
     * yang dapat dipilih sebagai penerima.
     *
     * View akan dibuat pada STEP 3.
     */
    public function create(): View
    {
        $this->ensureAdmin();

        $assignees = User::query()
            ->with('department')
            ->whereIn(
                'role',
                [
                    'karyawan',
                    'kabid',
                ]
            )
            ->where('is_active', true)
            ->where('approval_status', 'approved')
            ->orderBy('role')
            ->orderBy('name')
            ->get();

        return view(
            'admin.duty-letters.create',
            compact('assignees')
        );
    }


    /**
     * Menyimpan Surat Dinas baru beserta seluruh penerimanya.
     *
     * Tidak ada proses ACC dari Karyawan/Kabid.
     * Setelah Admin menyimpan surat, assignment langsung dibuat.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->ensureAdmin();

        $validated = $request->validate(
            [
                'letter_number' => [
                    'nullable',
                    'string',
                    'max:150',
                ],

                'title' => [
                    'required',
                    'string',
                    'max:255',
                ],

                'organizer' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'description' => [
                    'nullable',
                    'string',
                    'max:5000',
                ],

                'event_date' => [
                    'required',
                    'date',
                ],

                'start_time' => [
                    'required',
                    'date_format:H:i',
                ],

                'end_time' => [
                    'nullable',
                    'date_format:H:i',
                    'after:start_time',
                ],

                'location_name' => [
                    'required',
                    'string',
                    'max:255',
                ],

                'location_address' => [
                    'nullable',
                    'string',
                    'max:2000',
                ],

                'maps_url' => [
                    'nullable',
                    'url:http,https',
                    'max:2000',
                ],

                'letter' => [
                    'required',
                    'file',
                    'mimes:pdf',
                    'max:10240',
                ],

                'assignee_ids' => [
                    'required',
                    'array',
                    'min:1',
                ],

                'assignee_ids.*' => [
                    'required',
                    'integer',
                    'distinct',
                    Rule::exists(
                        'users',
                        'id'
                    )->where(
                        fn($query) =>
                        $query
                            ->whereIn(
                                'role',
                                [
                                    'karyawan',
                                    'kabid',
                                ]
                            )
                            ->where(
                                'is_active',
                                true
                            )
                            ->where(
                                'approval_status',
                                'approved'
                            )
                    ),
                ],
            ],
            [
                'title.required' =>
                'Judul kegiatan wajib diisi.',

                'event_date.required' =>
                'Tanggal kegiatan wajib diisi.',

                'event_date.date' =>
                'Tanggal kegiatan tidak valid.',

                'start_time.required' =>
                'Jam mulai wajib diisi.',

                'start_time.date_format' =>
                'Format jam mulai tidak valid.',

                'end_time.date_format' =>
                'Format jam selesai tidak valid.',

                'end_time.after' =>
                'Jam selesai harus setelah jam mulai.',

                'location_name.required' =>
                'Lokasi kegiatan wajib diisi.',

                'maps_url.url' =>
                'Link Google Maps/lokasi tidak valid.',

                'letter.required' =>
                'PDF surat dinas wajib diunggah.',

                'letter.file' =>
                'File surat dinas tidak valid.',

                'letter.mimes' =>
                'Surat dinas wajib berupa file PDF.',

                'letter.max' =>
                'Ukuran PDF surat dinas maksimal 10 MB.',

                'assignee_ids.required' =>
                'Pilih minimal satu Karyawan atau Kabid yang ditugaskan.',

                'assignee_ids.array' =>
                'Data penerima surat dinas tidak valid.',

                'assignee_ids.min' =>
                'Pilih minimal satu Karyawan atau Kabid yang ditugaskan.',

                'assignee_ids.*.distinct' =>
                'Penerima surat dinas tidak boleh sama.',

                'assignee_ids.*.exists' =>
                'Ada penerima yang tidak valid, tidak aktif, atau belum disetujui Admin.',
            ]
        );

        $pdf = $request->file('letter');

        if (! $pdf instanceof UploadedFile) {
            return back()
                ->withInput()
                ->withErrors([
                    'letter' =>
                    'PDF surat dinas wajib diunggah.',
                ]);
        }

        $assigneeIds = array_values(
            array_unique(
                array_map(
                    'intval',
                    $validated['assignee_ids']
                )
            )
        );

        $assignees = User::query()
            ->with('department')
            ->whereIn('id', $assigneeIds)
            ->whereIn(
                'role',
                [
                    'karyawan',
                    'kabid',
                ]
            )
            ->where('is_active', true)
            ->where('approval_status', 'approved')
            ->get();

        if ($assignees->count() !== count($assigneeIds)) {
            return back()
                ->withInput()
                ->withErrors([
                    'assignee_ids' =>
                    'Ada penerima yang sudah tidak aktif atau tidak dapat ditugaskan. Silakan pilih ulang.',
                ]);
        }

        $letterPath = $this->storeLetterPdf($pdf);

        try {
            $dutyLetter = DB::transaction(
                function () use (
                    $validated,
                    $pdf,
                    $letterPath,
                    $assignees
                ) {
                    $dutyLetter = DutyLetter::create([
                        'letter_number' =>
                        $validated['letter_number'] ?? null,

                        'title' =>
                        $validated['title'],

                        'organizer' =>
                        $validated['organizer'] ?? null,

                        'description' =>
                        $validated['description'] ?? null,

                        'event_date' =>
                        $validated['event_date'],

                        'start_time' =>
                        $validated['start_time'],

                        'end_time' =>
                        $validated['end_time'] ?? null,

                        'location_name' =>
                        $validated['location_name'],

                        'location_address' =>
                        $validated['location_address'] ?? null,

                        'maps_url' =>
                        $validated['maps_url'] ?? null,

                        'letter_path' =>
                        $letterPath,

                        'letter_original_name' =>
                        $pdf->getClientOriginalName(),

                        'letter_mime' =>
                        $pdf->getMimeType(),

                        'letter_size' =>
                        $pdf->getSize(),

                        'status' =>
                        DutyLetter::STATUS_PUBLISHED,

                        'published_at' =>
                        now(),

                        'created_by' =>
                        Auth::id(),
                    ]);

                    foreach ($assignees as $assignee) {
                        $dutyLetter->assignments()->create([
                            'user_id' =>
                            $assignee->id,

                            'assignee_name' =>
                            $assignee->name,

                            'assignee_role' =>
                            $assignee->role,

                            'assignee_department' =>
                            $assignee->department?->name,

                            'assigned_at' =>
                            now(),

                            'report_status' =>
                            DutyAssignment::REPORT_PENDING,

                            'fee_status' =>
                            DutyAssignment::FEE_UNPAID,
                        ]);
                    }

                    return $dutyLetter;
                }
            );
        } catch (\Throwable $exception) {
            Storage::disk('local')->delete($letterPath);

            throw $exception;
        }

        return redirect()
            ->route(
                'admin.duty-letters.show',
                $dutyLetter
            )
            ->with(
                'success',
                'Surat dinas berhasil diterbitkan dan langsung diberikan kepada pegawai yang dipilih.'
            );
    }


    /**
     * Menampilkan form edit Surat Dinas.
     *
     * Surat hanya dapat diedit SEBELUM hari kegiatan.
     * Begitu tanggal kegiatan tiba, data dikunci agar histori tugas
     * tidak berubah setelah pelaksanaan dimulai.
     */
    public function edit(
        DutyLetter $dutyLetter
    ): View|RedirectResponse {
        $this->ensureAdmin();

        try {
            $this->ensureEditable($dutyLetter);
        } catch (\RuntimeException $exception) {
            return redirect()
                ->route(
                    'admin.duty-letters.show',
                    $dutyLetter
                )
                ->with(
                    'error',
                    $exception->getMessage()
                );
        }

        $dutyLetter->load([
            'assignments.user.department',
        ]);

        $assignees = User::query()
            ->with('department')
            ->whereIn(
                'role',
                [
                    'karyawan',
                    'kabid',
                ]
            )
            ->where('is_active', true)
            ->where('approval_status', 'approved')
            ->orderBy('role')
            ->orderBy('name')
            ->get();

        $selectedAssigneeIds = $dutyLetter->assignments
            ->pluck('user_id')
            ->filter()
            ->map(fn($id) => (int) $id)
            ->values()
            ->all();

        return view(
            'admin.duty-letters.edit',
            compact(
                'dutyLetter',
                'assignees',
                'selectedAssigneeIds'
            )
        );
    }


    /**
     * Memperbarui Surat Dinas sebelum hari H.
     *
     * Yang dapat diubah:
     * - nomor surat
     * - judul / penyelenggara / keterangan
     * - tanggal & waktu
     * - lokasi & Google Maps
     * - PDF (opsional, hanya jika ingin mengganti)
     * - daftar Karyawan/Kabid penerima
     *
     * Penerima yang dihapus akan kehilangan assignment surat tersebut.
     * Penerima baru langsung mendapatkan assignment tanpa ACC.
     */
    public function update(
        Request $request,
        DutyLetter $dutyLetter
    ): RedirectResponse {
        $this->ensureAdmin();

        try {
            $this->ensureEditable($dutyLetter);
        } catch (\RuntimeException $exception) {
            return redirect()
                ->route(
                    'admin.duty-letters.show',
                    $dutyLetter
                )
                ->with(
                    'error',
                    $exception->getMessage()
                );
        }

        $validated = $request->validate(
            [
                'letter_number' => [
                    'nullable',
                    'string',
                    'max:150',
                ],

                'title' => [
                    'required',
                    'string',
                    'max:255',
                ],

                'organizer' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'description' => [
                    'nullable',
                    'string',
                    'max:5000',
                ],

                // Karena edit hanya boleh sebelum hari H,
                // tanggal baru juga harus masih setelah hari ini.
                'event_date' => [
                    'required',
                    'date',
                    'after:today',
                ],

                'start_time' => [
                    'required',
                    'date_format:H:i',
                ],

                'end_time' => [
                    'nullable',
                    'date_format:H:i',
                    'after:start_time',
                ],

                'location_name' => [
                    'required',
                    'string',
                    'max:255',
                ],

                'location_address' => [
                    'nullable',
                    'string',
                    'max:2000',
                ],

                'maps_url' => [
                    'nullable',
                    'url:http,https',
                    'max:2000',
                ],

                // Saat edit PDF tidak wajib. Jika kosong,
                // PDF lama tetap digunakan.
                'letter' => [
                    'nullable',
                    'file',
                    'mimes:pdf',
                    'max:10240',
                ],

                'assignee_ids' => [
                    'required',
                    'array',
                    'min:1',
                ],

                'assignee_ids.*' => [
                    'required',
                    'integer',
                    'distinct',
                    Rule::exists(
                        'users',
                        'id'
                    )->where(
                        fn($query) =>
                        $query
                            ->whereIn(
                                'role',
                                [
                                    'karyawan',
                                    'kabid',
                                ]
                            )
                            ->where(
                                'is_active',
                                true
                            )
                            ->where(
                                'approval_status',
                                'approved'
                            )
                    ),
                ],
            ],
            [
                'title.required' =>
                'Judul kegiatan wajib diisi.',

                'event_date.required' =>
                'Tanggal kegiatan wajib diisi.',

                'event_date.date' =>
                'Tanggal kegiatan tidak valid.',

                'event_date.after' =>
                'Tanggal kegiatan setelah diedit harus setelah hari ini.',

                'start_time.required' =>
                'Jam mulai wajib diisi.',

                'start_time.date_format' =>
                'Format jam mulai tidak valid.',

                'end_time.date_format' =>
                'Format jam selesai tidak valid.',

                'end_time.after' =>
                'Jam selesai harus setelah jam mulai.',

                'location_name.required' =>
                'Lokasi kegiatan wajib diisi.',

                'maps_url.url' =>
                'Link Google Maps/lokasi tidak valid.',

                'letter.file' =>
                'File surat dinas tidak valid.',

                'letter.mimes' =>
                'Surat dinas wajib berupa file PDF.',

                'letter.max' =>
                'Ukuran PDF surat dinas maksimal 10 MB.',

                'assignee_ids.required' =>
                'Pilih minimal satu Karyawan atau Kabid yang ditugaskan.',

                'assignee_ids.array' =>
                'Data penerima surat dinas tidak valid.',

                'assignee_ids.min' =>
                'Pilih minimal satu Karyawan atau Kabid yang ditugaskan.',

                'assignee_ids.*.distinct' =>
                'Penerima surat dinas tidak boleh sama.',

                'assignee_ids.*.exists' =>
                'Ada penerima yang tidak valid, tidak aktif, atau belum disetujui Admin.',
            ]
        );

        $assigneeIds = array_values(
            array_unique(
                array_map(
                    'intval',
                    $validated['assignee_ids']
                )
            )
        );

        $assignees = User::query()
            ->with('department')
            ->whereIn('id', $assigneeIds)
            ->whereIn(
                'role',
                [
                    'karyawan',
                    'kabid',
                ]
            )
            ->where('is_active', true)
            ->where('approval_status', 'approved')
            ->get();

        if ($assignees->count() !== count($assigneeIds)) {
            return back()
                ->withInput()
                ->withErrors([
                    'assignee_ids' =>
                    'Ada penerima yang sudah tidak aktif atau tidak dapat ditugaskan. Silakan pilih ulang.',
                ]);
        }

        $newPdf = $request->file('letter');
        $newLetterPath = null;

        if ($newPdf !== null) {
            if (! $newPdf instanceof UploadedFile) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'letter' =>
                        'PDF surat dinas tidak valid.',
                    ]);
            }

            $newLetterPath = $this->storeLetterPdf($newPdf);
        }

        $oldLetterPath = $dutyLetter->letter_path;

        try {
            DB::transaction(
                function () use (
                    $dutyLetter,
                    $validated,
                    $assigneeIds,
                    $assignees,
                    $newPdf,
                    $newLetterPath
                ) {
                    $locked = DutyLetter::query()
                        ->lockForUpdate()
                        ->findOrFail($dutyLetter->id);

                    // Cek ulang setelah row terkunci agar update tetap aman.
                    $this->ensureEditable($locked);

                    $updateData = [
                        'letter_number' =>
                        $validated['letter_number'] ?? null,

                        'title' =>
                        $validated['title'],

                        'organizer' =>
                        $validated['organizer'] ?? null,

                        'description' =>
                        $validated['description'] ?? null,

                        'event_date' =>
                        $validated['event_date'],

                        'start_time' =>
                        $validated['start_time'],

                        'end_time' =>
                        $validated['end_time'] ?? null,

                        'location_name' =>
                        $validated['location_name'],

                        'location_address' =>
                        $validated['location_address'] ?? null,

                        'maps_url' =>
                        $validated['maps_url'] ?? null,
                    ];

                    if (
                        $newPdf instanceof UploadedFile
                        && is_string($newLetterPath)
                    ) {
                        $updateData['letter_path'] =
                            $newLetterPath;

                        $updateData['letter_original_name'] =
                            $newPdf->getClientOriginalName();

                        $updateData['letter_mime'] =
                            $newPdf->getMimeType();

                        $updateData['letter_size'] =
                            $newPdf->getSize();
                    }

                    $locked->update($updateData);

                    $existingAssignments = $locked
                        ->assignments()
                        ->get()
                        ->keyBy('user_id');

                    foreach ($assignees as $assignee) {
                        $existing = $existingAssignments->get(
                            $assignee->id
                        );

                        if ($existing) {
                            // Refresh snapshot identitas supaya informasi
                            // penerima tetap mengikuti data terbaru.
                            $existing->update([
                                'assignee_name' =>
                                $assignee->name,

                                'assignee_role' =>
                                $assignee->role,

                                'assignee_department' =>
                                $assignee->department?->name,
                            ]);

                            continue;
                        }

                        $locked->assignments()->create([
                            'user_id' =>
                            $assignee->id,

                            'assignee_name' =>
                            $assignee->name,

                            'assignee_role' =>
                            $assignee->role,

                            'assignee_department' =>
                            $assignee->department?->name,

                            'assigned_at' =>
                            now(),

                            'report_status' =>
                            DutyAssignment::REPORT_PENDING,

                            'fee_status' =>
                            DutyAssignment::FEE_UNPAID,
                        ]);
                    }

                    // Penerima yang tidak lagi dicentang dihapus dari surat.
                    // Ini aman karena ensureEditable() memastikan belum hari H
                    // dan belum ada laporan/fee yang diproses.
                    $locked->assignments()
                        ->where(
                            function ($query) use ($assigneeIds) {
                                $query
                                    ->whereNull('user_id')
                                    ->orWhereNotIn(
                                        'user_id',
                                        $assigneeIds
                                    );
                            }
                        )
                        ->delete();
                }
            );
        } catch (\Throwable $exception) {
            if (is_string($newLetterPath)) {
                Storage::disk('local')->delete(
                    $newLetterPath
                );
            }

            if ($exception instanceof \RuntimeException) {
                return redirect()
                    ->route(
                        'admin.duty-letters.show',
                        $dutyLetter
                    )
                    ->with(
                        'error',
                        $exception->getMessage()
                    );
            }

            throw $exception;
        }

        if (
            is_string($newLetterPath)
            && is_string($oldLetterPath)
            && $oldLetterPath !== ''
            && $oldLetterPath !== $newLetterPath
        ) {
            Storage::disk('local')->delete(
                $oldLetterPath
            );
        }

        return redirect()
            ->route(
                'admin.duty-letters.show',
                $dutyLetter
            )
            ->with(
                'success',
                'Surat dinas berhasil diperbarui. Perubahan langsung tampil pada akun penerima.'
            );
    }


    /**
     * Menampilkan detail Surat Dinas beserta semua penerima.
     *
     * View akan dibuat pada STEP 3.
     */
    public function show(DutyLetter $dutyLetter): View
    {
        $this->ensureAdmin();

        $dutyLetter->load([
            'creator',
            'assignments' =>
            fn($query) =>
            $query->orderBy('assignee_name'),
            'assignments.user.department',
            'assignments.report.files',
            'assignments.reportVerifier',
            'assignments.feeConfirmer',
        ]);

        return view(
            'admin.duty-letters.show',
            compact('dutyLetter')
        );
    }


    /**
     * Menampilkan PDF surat secara private.
     *
     * File berada di storage/app/private sehingga tidak perlu
     * membuat URL publik atau storage:link untuk surat dinas.
     */
    public function pdf(
        DutyLetter $dutyLetter
    ): StreamedResponse {
        $this->ensureAdmin();

        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk('local');

        abort_unless(
            ! empty($dutyLetter->letter_path)
                && $disk->exists(
                    $dutyLetter->letter_path
                ),
            404
        );

        return $disk->response(
            $dutyLetter->letter_path,
            $dutyLetter->letter_original_name,
            [
                'Content-Type' =>
                $dutyLetter->letter_mime
                    ?: 'application/pdf',

                'X-Content-Type-Options' =>
                'nosniff',
            ],
            'inline'
        );
    }


    /**
     * Membatalkan Surat Dinas.
     *
     * Data tidak dihapus agar histori tetap tersimpan.
     * Surat hanya dapat dibatalkan selama belum ada penerima
     * yang mengirim / merevisi / memiliki laporan terverifikasi.
     */
    public function cancel(
        DutyLetter $dutyLetter
    ): RedirectResponse {
        $this->ensureAdmin();

        try {
            DB::transaction(
                function () use ($dutyLetter) {
                    $locked = DutyLetter::query()
                        ->lockForUpdate()
                        ->findOrFail($dutyLetter->id);

                    if ($locked->isCancelled()) {
                        throw new \RuntimeException(
                            'Surat dinas ini sudah dibatalkan sebelumnya.'
                        );
                    }

                    $hasProcessedReport =
                        $locked->assignments()
                        ->where(
                            'report_status',
                            '!=',
                            DutyAssignment::REPORT_PENDING
                        )
                        ->exists();

                    if ($hasProcessedReport) {
                        throw new \RuntimeException(
                            'Surat dinas tidak dapat dibatalkan karena sudah ada laporan yang diproses.'
                        );
                    }

                    $locked->update([
                        'status' =>
                        DutyLetter::STATUS_CANCELLED,
                    ]);
                }
            );
        } catch (\RuntimeException $exception) {
            return back()->with(
                'error',
                $exception->getMessage()
            );
        }

        return redirect()
            ->route(
                'admin.duty-letters.show',
                $dutyLetter
            )
            ->with(
                'success',
                'Surat dinas berhasil dibatalkan. Histori surat tetap tersimpan.'
            );
    }


    /**
     * Memastikan Surat Dinas masih boleh diedit.
     *
     * Aturan:
     * - status masih published
     * - tanggal kegiatan HARUS lebih besar dari hari ini
     * - belum ada laporan yang dibuat/diproses
     * - belum ada fee yang dibayar
     */
    private function ensureEditable(
        DutyLetter $dutyLetter
    ): void {
        if ($dutyLetter->isCancelled()) {
            throw new \RuntimeException(
                'Surat dinas yang sudah dibatalkan tidak dapat diedit.'
            );
        }

        if (
            $dutyLetter->event_date === null
            || ! $dutyLetter->event_date->isAfter(today())
        ) {
            throw new \RuntimeException(
                'Surat dinas hanya dapat diedit sebelum hari kegiatan. Karena hari H sudah tiba atau lewat, data telah dikunci.'
            );
        }

        $hasProgress = $dutyLetter
            ->assignments()
            ->where(
                function ($query) {
                    $query
                        ->where(
                            'report_status',
                            '!=',
                            DutyAssignment::REPORT_PENDING
                        )
                        ->orWhere(
                            'fee_status',
                            '!=',
                            DutyAssignment::FEE_UNPAID
                        )
                        ->orWhereHas('report');
                }
            )
            ->exists();

        if ($hasProgress) {
            throw new \RuntimeException(
                'Surat dinas tidak dapat diedit karena sudah ada laporan atau proses pembayaran yang tercatat.'
            );
        }
    }


    /**
     * Menyimpan PDF dengan nama acak ke disk private/local.
     */
    private function storeLetterPdf(
        UploadedFile $pdf
    ): string {
        $directory =
            'duty-letters/' . now()->format('Y/m');

        $filename =
            Str::uuid()->toString() . '.pdf';

        $path = $pdf->storeAs(
            $directory,
            $filename,
            'local'
        );

        if (! is_string($path) || $path === '') {
            throw new \RuntimeException(
                'PDF surat dinas gagal disimpan.'
            );
        }

        return $path;
    }


    /**
     * Pertahanan tambahan meskipun route sudah memakai role:admin.
     */
    private function ensureAdmin(): void
    {
        /** @var User|null $user */
        $user = Auth::user();

        abort_unless(
            $user !== null
                && $user->role === 'admin',
            403
        );
    }
}
