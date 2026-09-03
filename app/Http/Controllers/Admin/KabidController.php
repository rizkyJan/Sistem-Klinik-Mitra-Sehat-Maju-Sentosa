<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class KabidController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Index
    |--------------------------------------------------------------------------
    */
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $approvalStatus = $request->input('approval_status');

        $kabids = User::query()
            ->with('department')
            ->where('role', 'kabid')
            ->when(
                $search,
                function ($query, $search) {
                    $query->where(function ($query) use ($search) {
                        $query
                            ->where('name', 'like', '%' . $search . '%')
                            ->orWhere('nip', 'like', '%' . $search . '%')
                            ->orWhere('nik_ktp', 'like', '%' . $search . '%')
                            ->orWhere('nik', 'like', '%' . $search . '%') // legacy sementara
                            ->orWhere('email', 'like', '%' . $search . '%')
                            ->orWhere('whatsapp', 'like', '%' . $search . '%')
                            ->orWhere('bank_account_number', 'like', '%' . $search . '%')
                            ->orWhereHas(
                                'department',
                                fn($query) => $query->where(
                                    'name',
                                    'like',
                                    '%' . $search . '%'
                                )
                            );
                    });
                }
            )
            ->when(
                $approvalStatus,
                fn($query, $approvalStatus) =>
                $query->where('approval_status', $approvalStatus)
            )
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view(
            'admin.kabid.index',
            compact(
                'kabids',
                'search',
                'approvalStatus'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Create
    |--------------------------------------------------------------------------
    */

    public function create(): View
    {
        /*
         * Satu bidang hanya boleh memiliki satu Kabid.
         *
         * Ambil ID bidang yang sudah dipakai oleh Kabid,
         * lalu sembunyikan dari pilihan Tambah Kabid.
         */
        $usedDepartmentIds = User::query()
            ->where('role', 'kabid')
            ->where('approval_status', 'approved')
            ->whereNotNull('department_id')
            ->pluck('department_id');

        $departments = Department::query()
            ->where('is_active', true)
            ->whereNotIn(
                'id',
                $usedDepartmentIds
            )
            ->orderBy('name')
            ->get();

        return view(
            'admin.kabid.create',
            compact('departments')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Store
    |--------------------------------------------------------------------------
    */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate(
            [
                'name' => ['required', 'string', 'max:255'],

                'nip' => [
                    'required',
                    'string',
                    'max:50',
                    'unique:users,nip',
                    Rule::unique('users', 'nik'),
                ],

                'nik_ktp' => [
                    'required',
                    'string',
                    'regex:/^[0-9]{16}$/',
                    'unique:users,nik_ktp',
                ],

                'email' => [
                    'required',
                    'email',
                    'max:255',
                    'unique:users,email',
                ],

                'whatsapp' => ['required', 'string', 'max:20'],

                'join_date' => [
                    'required',
                    'date',
                    'before_or_equal:today',
                ],

                'department_id' => [
                    'required',
                    Rule::exists('departments', 'id')
                        ->where(fn($query) => $query->where('is_active', true)),
                ],

                'birth_place' => ['required', 'string', 'max:100'],
                'birth_date' => ['required', 'date', 'before:today'],
                'ktp_address' => ['required', 'string', 'max:3000'],
                'domicile_address' => ['required', 'string', 'max:3000'],
                'blood_type' => ['required', Rule::in(['A', 'B', 'AB', 'O'])],
                'religion' => [
                    'required',
                    Rule::in([
                        'Islam',
                        'Kristen Protestan',
                        'Katolik',
                        'Hindu',
                        'Buddha',
                        'Konghucu',
                        'Kepercayaan',
                    ]),
                ],

                'sip_number' => [
                    'nullable',
                    'required_with:sip_valid_from,sip_valid_until',
                    'string',
                    'max:100',
                ],
                'sip_valid_from' => [
                    'nullable',
                    'required_with:sip_number',
                    'date',
                ],
                'sip_valid_until' => [
                    'nullable',
                    'required_with:sip_number',
                    'date',
                    'after_or_equal:sip_valid_from',
                ],

                'formal_photo' => [
                    'required',
                    'image',
                    'mimes:jpg,jpeg,png,webp',
                    'max:2048',
                ],

                'bank_account_number' => [
                    'required',
                    'string',
                    'regex:/^[0-9]{8,20}$/',
                ],
                'bank_account_name' => [
                    'required',
                    'string',
                    'max:150',
                ],

                'password' => [
                    'required',
                    'string',
                    'min:8',
                    'confirmed',
                ],

                'is_active' => ['required', 'boolean'],
            ],
            [
                'name.required' => 'Nama Kabid wajib diisi.',
                'nip.required' => 'NIP / ID Pegawai wajib diisi.',
                'nip.unique' => 'NIP / ID Pegawai sudah digunakan.',
                'nik_ktp.required' => 'NIK KTP wajib diisi.',
                'nik_ktp.regex' => 'NIK KTP harus tepat 16 digit angka.',
                'nik_ktp.unique' => 'NIK KTP sudah digunakan.',
                'email.required' => 'Email wajib diisi.',
                'email.email' => 'Format email tidak valid.',
                'email.unique' => 'Email sudah digunakan.',
                'whatsapp.required' => 'Nomor WhatsApp wajib diisi.',
                'join_date.required' => 'Tanggal mulai kerja wajib diisi.',
                'join_date.before_or_equal' => 'Tanggal mulai kerja tidak boleh melewati hari ini.',
                'department_id.required' => 'Bidang wajib dipilih.',
                'department_id.exists' => 'Bidang yang dipilih tidak tersedia.',
                'birth_place.required' => 'Tempat lahir wajib diisi.',
                'birth_date.required' => 'Tanggal lahir wajib diisi.',
                'birth_date.before' => 'Tanggal lahir harus sebelum hari ini.',
                'ktp_address.required' => 'Alamat KTP wajib diisi.',
                'domicile_address.required' => 'Alamat domisili wajib diisi.',
                'blood_type.required' => 'Golongan darah wajib dipilih.',
                'religion.required' => 'Agama wajib dipilih.',
                'sip_number.required_with' => 'Nomor SIP wajib diisi jika masa berlaku SIP diisi.',
                'sip_valid_from.required_with' => 'Tanggal mulai SIP wajib diisi jika Nomor SIP diisi.',
                'sip_valid_until.required_with' => 'Tanggal berakhir SIP wajib diisi jika Nomor SIP diisi.',
                'sip_valid_until.after_or_equal' => 'Tanggal berakhir SIP tidak boleh sebelum tanggal mulai SIP.',
                'formal_photo.required' => 'Pas foto formal wajib diunggah.',
                'formal_photo.image' => 'Pas foto harus berupa file gambar.',
                'formal_photo.mimes' => 'Pas foto harus JPG, JPEG, PNG, atau WEBP.',
                'formal_photo.max' => 'Ukuran pas foto maksimal 2 MB.',
                'bank_account_number.required' => 'Nomor rekening BSI wajib diisi.',
                'bank_account_number.regex' => 'Nomor rekening BSI harus berupa 8-20 digit angka.',
                'bank_account_name.required' => 'Nama pemilik rekening BSI wajib diisi.',
                'password.required' => 'Password wajib diisi.',
                'password.min' => 'Password minimal 8 karakter.',
                'password.confirmed' => 'Konfirmasi password tidak sama.',
                'is_active.required' => 'Status Kabid wajib dipilih.',
            ]
        );

        $photoPath = $request->file('formal_photo')
            ->store('employee-photos', 'local');

        try {
            DB::transaction(
                function () use ($validated, $photoPath) {
                    Department::query()
                        ->whereKey($validated['department_id'])
                        ->lockForUpdate()
                        ->firstOrFail();

                    $departmentAlreadyHasKabid =
                        User::query()
                        ->where('role', 'kabid')
                        ->where('approval_status', 'approved')
                        ->where('department_id', $validated['department_id'])
                        ->exists();

                    if ($departmentAlreadyHasKabid) {
                        throw ValidationException::withMessages([
                            'department_id' =>
                            'Bidang yang dipilih sudah memiliki Kabid. '
                                . 'Satu bidang hanya boleh memiliki satu Kabid.',
                        ]);
                    }

                    User::create([
                        'name' => $validated['name'],

                        /*
                     * Kolom legacy `nik` disinkronkan dengan NIP
                     * sampai seluruh fitur selesai dipindah ke `nip`.
                     */
                        'nik' => $validated['nip'],
                        'nip' => $validated['nip'],
                        'nik_ktp' => $validated['nik_ktp'],

                        'email' => $validated['email'],
                        'whatsapp' => $validated['whatsapp'],
                        'join_date' => $validated['join_date'],
                        'department_id' => $validated['department_id'],

                        'birth_place' => $validated['birth_place'],
                        'birth_date' => $validated['birth_date'],
                        'ktp_address' => $validated['ktp_address'],
                        'domicile_address' => $validated['domicile_address'],
                        'blood_type' => $validated['blood_type'],
                        'religion' => $validated['religion'],

                        'sip_number' => $validated['sip_number'] ?? null,
                        'sip_valid_from' => $validated['sip_valid_from'] ?? null,
                        'sip_valid_until' => $validated['sip_valid_until'] ?? null,

                        'formal_photo_path' => $photoPath,

                        'bank_name' => User::BANK_BSI,
                        'bank_account_number' => $validated['bank_account_number'],
                        'bank_account_name' => $validated['bank_account_name'],

                        'password' => Hash::make($validated['password']),
                        'role' => 'kabid',
                        'is_active' => (bool) $validated['is_active'],
                        'approval_status' => 'approved',
                        'approval_rejection_reason' => null,
                        'profile_completed_at' => now(),
                    ]);
                }
            );
        } catch (\Throwable $exception) {
            Storage::disk('local')->delete($photoPath);
            throw $exception;
        }

        return redirect()
            ->route('admin.kabid.index')
            ->with(
                'success',
                'Data Kabid berhasil ditambahkan lengkap dengan biodata dan rekening BSI.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Edit
    |--------------------------------------------------------------------------
    */

    public function edit(
        User $kabid
    ): View {

        $this->ensureKabid(
            $kabid
        );

        /*
         * Saat edit:
         * - bidang milik Kabid ini sendiri tetap boleh dipilih;
         * - bidang yang sudah dipakai Kabid lain tidak ditampilkan.
         */
        $usedDepartmentIds = User::query()
            ->where('role', 'kabid')
            ->where('approval_status', 'approved')
            ->whereKeyNot($kabid->id)
            ->whereNotNull('department_id')
            ->pluck('department_id');

        $departments = Department::query()
            ->whereNotIn(
                'id',
                $usedDepartmentIds
            )
            ->orderBy('name')
            ->get();

        return view(
            'admin.kabid.edit',
            compact(
                'kabid',
                'departments'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */
    public function update(
        Request $request,
        User $kabid
    ): RedirectResponse {
        $this->ensureKabid($kabid);

        $validated = $request->validate(
            [
                'name' => ['required', 'string', 'max:255'],

                'nip' => [
                    'required',
                    'string',
                    'max:50',
                    Rule::unique('users', 'nip')->ignore($kabid->id),
                    Rule::unique('users', 'nik')->ignore($kabid->id),
                ],

                'nik_ktp' => [
                    'required',
                    'string',
                    'regex:/^[0-9]{16}$/',
                    Rule::unique('users', 'nik_ktp')->ignore($kabid->id),
                ],

                'email' => [
                    'required',
                    'email',
                    'max:255',
                    Rule::unique('users', 'email')->ignore($kabid->id),
                ],

                'whatsapp' => ['required', 'string', 'max:20'],
                'join_date' => ['required', 'date', 'before_or_equal:today'],
                'department_id' => ['required', 'exists:departments,id'],

                'birth_place' => ['required', 'string', 'max:100'],
                'birth_date' => ['required', 'date', 'before:today'],
                'ktp_address' => ['required', 'string', 'max:3000'],
                'domicile_address' => ['required', 'string', 'max:3000'],
                'blood_type' => ['required', Rule::in(['A', 'B', 'AB', 'O'])],
                'religion' => [
                    'required',
                    Rule::in([
                        'Islam',
                        'Kristen Protestan',
                        'Katolik',
                        'Hindu',
                        'Buddha',
                        'Konghucu',
                        'Kepercayaan',
                    ]),
                ],

                'sip_number' => [
                    'nullable',
                    'required_with:sip_valid_from,sip_valid_until',
                    'string',
                    'max:100',
                ],
                'sip_valid_from' => [
                    'nullable',
                    'required_with:sip_number',
                    'date',
                ],
                'sip_valid_until' => [
                    'nullable',
                    'required_with:sip_number',
                    'date',
                    'after_or_equal:sip_valid_from',
                ],

                'formal_photo' => [
                    Rule::requiredIf(blank($kabid->formal_photo_path)),
                    'nullable',
                    'image',
                    'mimes:jpg,jpeg,png,webp',
                    'max:2048',
                ],

                'bank_account_number' => [
                    'required',
                    'string',
                    'regex:/^[0-9]{8,20}$/',
                ],
                'bank_account_name' => [
                    'required',
                    'string',
                    'max:150',
                ],

                'password' => [
                    'nullable',
                    'string',
                    'min:8',
                    'confirmed',
                ],

                'is_active' => ['required', 'boolean'],
            ],
            [
                'name.required' => 'Nama Kabid wajib diisi.',
                'nip.required' => 'NIP / ID Pegawai wajib diisi.',
                'nip.unique' => 'NIP / ID Pegawai sudah digunakan.',
                'nik_ktp.required' => 'NIK KTP wajib diisi.',
                'nik_ktp.regex' => 'NIK KTP harus tepat 16 digit angka.',
                'nik_ktp.unique' => 'NIK KTP sudah digunakan.',
                'email.required' => 'Email wajib diisi.',
                'email.email' => 'Format email tidak valid.',
                'email.unique' => 'Email sudah digunakan.',
                'whatsapp.required' => 'Nomor WhatsApp wajib diisi.',
                'join_date.required' => 'Tanggal mulai kerja wajib diisi.',
                'join_date.before_or_equal' => 'Tanggal mulai kerja tidak boleh melewati hari ini.',
                'department_id.required' => 'Bidang wajib dipilih.',
                'birth_place.required' => 'Tempat lahir wajib diisi.',
                'birth_date.required' => 'Tanggal lahir wajib diisi.',
                'birth_date.before' => 'Tanggal lahir harus sebelum hari ini.',
                'ktp_address.required' => 'Alamat KTP wajib diisi.',
                'domicile_address.required' => 'Alamat domisili wajib diisi.',
                'blood_type.required' => 'Golongan darah wajib dipilih.',
                'religion.required' => 'Agama wajib dipilih.',
                'sip_number.required_with' => 'Nomor SIP wajib diisi jika masa berlaku SIP diisi.',
                'sip_valid_from.required_with' => 'Tanggal mulai SIP wajib diisi jika Nomor SIP diisi.',
                'sip_valid_until.required_with' => 'Tanggal berakhir SIP wajib diisi jika Nomor SIP diisi.',
                'sip_valid_until.after_or_equal' => 'Tanggal berakhir SIP tidak boleh sebelum tanggal mulai SIP.',
                'formal_photo.required' => 'Pas foto formal wajib diunggah karena data lama belum memiliki foto.',
                'formal_photo.image' => 'Pas foto harus berupa file gambar.',
                'formal_photo.mimes' => 'Pas foto harus JPG, JPEG, PNG, atau WEBP.',
                'formal_photo.max' => 'Ukuran pas foto maksimal 2 MB.',
                'bank_account_number.required' => 'Nomor rekening BSI wajib diisi.',
                'bank_account_number.regex' => 'Nomor rekening BSI harus berupa 8-20 digit angka.',
                'bank_account_name.required' => 'Nama pemilik rekening BSI wajib diisi.',
                'password.min' => 'Password minimal 8 karakter.',
                'password.confirmed' => 'Konfirmasi password tidak sama.',
            ]
        );

        $oldPhotoPath = $kabid->formal_photo_path;
        $newPhotoPath = null;

        if ($request->hasFile('formal_photo')) {
            $newPhotoPath = $request->file('formal_photo')
                ->store('employee-photos', 'local');
        }

        $data = [
            'name' => $validated['name'],
            'nik' => $validated['nip'],
            'nip' => $validated['nip'],
            'nik_ktp' => $validated['nik_ktp'],

            'email' => $validated['email'],
            'whatsapp' => $validated['whatsapp'],
            'join_date' => $validated['join_date'],
            'department_id' => $validated['department_id'],

            'birth_place' => $validated['birth_place'],
            'birth_date' => $validated['birth_date'],
            'ktp_address' => $validated['ktp_address'],
            'domicile_address' => $validated['domicile_address'],
            'blood_type' => $validated['blood_type'],
            'religion' => $validated['religion'],

            'sip_number' => $validated['sip_number'] ?? null,
            'sip_valid_from' => $validated['sip_valid_from'] ?? null,
            'sip_valid_until' => $validated['sip_valid_until'] ?? null,

            'bank_name' => User::BANK_BSI,
            'bank_account_number' => $validated['bank_account_number'],
            'bank_account_name' => $validated['bank_account_name'],

            'is_active' => (bool) $validated['is_active'],
            'role' => 'kabid',
        ];

        if ($newPhotoPath) {
            $data['formal_photo_path'] = $newPhotoPath;
        }

        if (! empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        try {
            DB::transaction(
                function () use (
                    $validated,
                    $data,
                    $kabid
                ) {
                    Department::query()
                        ->whereKey($validated['department_id'])
                        ->lockForUpdate()
                        ->firstOrFail();

                    $departmentAlreadyHasKabid =
                        User::query()
                        ->where('role', 'kabid')
                        ->where('approval_status', 'approved')
                        ->where('department_id', $validated['department_id'])
                        ->whereKeyNot($kabid->id)
                        ->exists();

                    if ($departmentAlreadyHasKabid) {
                        throw ValidationException::withMessages([
                            'department_id' =>
                            'Bidang yang dipilih sudah memiliki Kabid lain. '
                                . 'Satu bidang hanya boleh memiliki satu Kabid.',
                        ]);
                    }

                    $kabid->update($data);
                }
            );
        } catch (\Throwable $exception) {
            if ($newPhotoPath) {
                Storage::disk('local')->delete($newPhotoPath);
            }

            throw $exception;
        }

        if (
            $newPhotoPath
            && $oldPhotoPath
            && $oldPhotoPath !== $newPhotoPath
        ) {
            Storage::disk('local')->delete($oldPhotoPath);
        }

        return redirect()
            ->route('admin.kabid.index')
            ->with(
                'success',
                'Data Kabid berhasil diperbarui.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Delete
    |--------------------------------------------------------------------------
    */
    public function destroy(User $kabid): RedirectResponse
    {
        $this->ensureKabid($kabid);

        $leaveRequestCount = $kabid->leaveRequests()->count();
        $reimbursementCount = $kabid->reimbursements()->count();

        if (
            $leaveRequestCount > 0
            || $reimbursementCount > 0
        ) {
            $relatedData = [];

            if ($leaveRequestCount > 0) {
                $relatedData[] =
                    $leaveRequestCount . ' riwayat perizinan/cuti';
            }

            if ($reimbursementCount > 0) {
                $relatedData[] =
                    $reimbursementCount . ' riwayat reimbursement';
            }

            return redirect()
                ->route('admin.kabid.index')
                ->with(
                    'error',
                    'Data Kabid "'
                        . $kabid->name
                        . '" tidak dapat dihapus karena masih memiliki '
                        . implode(' dan ', $relatedData)
                        . '. Nonaktifkan akun melalui menu Edit agar riwayat tetap aman.'
                );
        }

        $photoPath = $kabid->formal_photo_path;

        try {
            $kabid->delete();
        } catch (QueryException $exception) {
            report($exception);

            return redirect()
                ->route('admin.kabid.index')
                ->with(
                    'error',
                    'Data Kabid tidak dapat dihapus karena masih digunakan oleh data lain. '
                        . 'Silakan nonaktifkan akun melalui menu Edit.'
                );
        }

        if ($photoPath) {
            Storage::disk('local')->delete($photoPath);
        }

        return redirect()
            ->route('admin.kabid.index')
            ->with(
                'success',
                'Data Kabid berhasil dihapus.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Approve / ACC Kabid
    |--------------------------------------------------------------------------
    */

    public function approve(
        User $kabid
    ): RedirectResponse {

        $this->ensureKabid(
            $kabid
        );

        if (! $kabid->profile_completed_at) {
            return redirect()
                ->route('admin.kabid.index')
                ->withErrors([
                    'approval' =>
                    'Data Kabid belum lengkap dan belum dapat di-ACC.'
                ]);
        }

        if (! $kabid->department_id) {
            return redirect()
                ->route('admin.kabid.index')
                ->withErrors([
                    'approval' =>
                    'Kabid belum memiliki bidang dan belum dapat di-ACC.'
                ]);
        }

        DB::transaction(
            function () use ($kabid) {

                /*
                 * Lock bidang saat proses ACC agar dua calon Kabid
                 * pada bidang yang sama tidak bisa disetujui bersamaan.
                 */
                Department::query()
                    ->whereKey(
                        $kabid->department_id
                    )
                    ->lockForUpdate()
                    ->firstOrFail();

                /*
                 * Pending tidak dihitung.
                 * Yang mengunci bidang hanya Kabid yang SUDAH approved.
                 */
                $departmentAlreadyHasApprovedKabid =
                    User::query()
                    ->where('role', 'kabid')
                    ->where('approval_status', 'approved')
                    ->where(
                        'department_id',
                        $kabid->department_id
                    )
                    ->whereKeyNot(
                        $kabid->id
                    )
                    ->exists();

                if (
                    $departmentAlreadyHasApprovedKabid
                ) {
                    throw ValidationException::withMessages([
                        'approval' =>
                        'Bidang '
                            . ($kabid->department?->name ?? 'yang dipilih')
                            . ' sudah memiliki Kabid yang telah disetujui. '
                            . 'Satu bidang hanya boleh memiliki satu Kabid resmi.',
                    ]);
                }

                $kabid->update([
                    'approval_status' =>
                    'approved',

                    'approval_rejection_reason' =>
                    null,

                    'is_active' =>
                    true,
                ]);
            }
        );

        return redirect()
            ->route('admin.kabid.index')
            ->with(
                'success',
                'Kabid '
                    . $kabid->name
                    . ' berhasil di-ACC. Akun sekarang sudah aktif.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Reject / Tolak Kabid
    |--------------------------------------------------------------------------
    */

    public function reject(
        Request $request,
        User $kabid
    ): RedirectResponse {

        $this->ensureKabid(
            $kabid
        );

        $validated = $request->validate(
            [
                'reason' => [
                    'required',
                    'string',
                    'max:1000',
                ],
            ],
            [
                'reason.required' =>
                'Alasan penolakan wajib diisi.',
            ]
        );

        $kabid->update([
            'approval_status' =>
            'rejected',

            'approval_rejection_reason' =>
            $validated['reason'],

            'is_active' =>
            false,
        ]);

        return redirect()
            ->route('admin.kabid.index')
            ->with(
                'success',
                'Data Kabid '
                    . $kabid->name
                    . ' ditolak dan dikembalikan untuk diperbaiki.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Security
    |--------------------------------------------------------------------------
    */
    /*
|--------------------------------------------------------------------------
| Pas Foto Formal (Private)
|--------------------------------------------------------------------------
*/

    public function photo(User $kabid): StreamedResponse
    {
        $this->ensureKabid($kabid);

        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk('local');

        abort_unless(
            $kabid->formal_photo_path
                && $disk->exists($kabid->formal_photo_path),
            404
        );

        return $disk->response(
            $kabid->formal_photo_path,
            basename($kabid->formal_photo_path),
            [
                'Content-Disposition' => 'inline',
                'Cache-Control' => 'private, max-age=3600',
            ]
        );
    }



    private function ensureKabid(
        User $user
    ): void {

        abort_unless(
            $user->role === 'kabid',
            404
        );
    }
}
