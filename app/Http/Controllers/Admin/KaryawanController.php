<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\LeaveRequest;
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
use Illuminate\View\View;

class KaryawanController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Index
    |--------------------------------------------------------------------------
    */
    public function index(Request $request): View
    {
        $search = $request->input('search');

        $karyawan = User::query()
            ->with('department')
            ->where('role', 'karyawan')
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
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view(
            'admin.karyawan.index',
            compact('karyawan')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Create
    |--------------------------------------------------------------------------
    */

    public function create(): View
    {
        $departments = Department::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();


        return view(
            'admin.karyawan.create',
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
                    /*
                 * Selama kolom `nik` legacy masih dipakai fitur lama,
                 * NIP juga tidak boleh bentrok dengan nilai legacy user lain.
                 */
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

                'whatsapp' => [
                    'required',
                    'string',
                    'max:20',
                ],

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

                'birth_place' => [
                    'required',
                    'string',
                    'max:100',
                ],

                'birth_date' => [
                    'required',
                    'date',
                    'before:today',
                ],

                'ktp_address' => [
                    'required',
                    'string',
                    'max:3000',
                ],

                'domicile_address' => [
                    'required',
                    'string',
                    'max:3000',
                ],

                'blood_type' => [
                    'required',
                    Rule::in(['A', 'B', 'AB', 'O']),
                ],

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

                /*
             * Bank tidak dikirim sebagai pilihan.
             * Sistem selalu menyimpan Bank Syariah Indonesia (BSI).
             */
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

                'is_active' => [
                    'required',
                    'boolean',
                ],
            ],
            [
                'name.required' => 'Nama karyawan wajib diisi.',
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
                'is_active.required' => 'Status karyawan wajib dipilih.',
            ]
        );

        $photoPath = $request->file('formal_photo')
            ->store('employee-photos', 'local');

        try {
            User::create([
                'name' => $validated['name'],

                /*
             * `nik` tetap disinkronkan sementara dengan NIP
             * agar fitur lama yang belum dipindahkan ke `nip`
             * tetap berjalan.
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
                'role' => 'karyawan',
                'is_active' => (bool) $validated['is_active'],

                /*
             * Dibuat langsung oleh Admin sehingga data dianggap
             * sudah diverifikasi dan profil sudah lengkap.
             */
                'approval_status' => 'approved',
                'approval_rejection_reason' => null,
                'profile_completed_at' => now(),
            ]);
        } catch (\Throwable $exception) {
            Storage::disk('local')->delete($photoPath);
            throw $exception;
        }

        return redirect()
            ->route('admin.karyawan.index')
            ->with(
                'success',
                'Data karyawan berhasil ditambahkan lengkap dengan biodata dan rekening BSI.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Edit
    |--------------------------------------------------------------------------
    */

    public function edit(
        User $karyawan
    ): View {

        $this->ensureKaryawan(
            $karyawan
        );


        $departments = Department::query()
            ->orderBy('name')
            ->get();


        return view(
            'admin.karyawan.edit',
            compact(
                'karyawan',
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
        User $karyawan
    ): RedirectResponse {
        $this->ensureKaryawan($karyawan);

        $validated = $request->validate(
            [
                'name' => ['required', 'string', 'max:255'],

                'nip' => [
                    'required',
                    'string',
                    'max:50',
                    Rule::unique('users', 'nip')->ignore($karyawan->id),
                    Rule::unique('users', 'nik')->ignore($karyawan->id),
                ],

                'nik_ktp' => [
                    'required',
                    'string',
                    'regex:/^[0-9]{16}$/',
                    Rule::unique('users', 'nik_ktp')->ignore($karyawan->id),
                ],

                'email' => [
                    'required',
                    'email',
                    'max:255',
                    Rule::unique('users', 'email')->ignore($karyawan->id),
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
                    Rule::requiredIf(blank($karyawan->formal_photo_path)),
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
                'name.required' => 'Nama karyawan wajib diisi.',
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

        $oldPhotoPath = $karyawan->formal_photo_path;
        $newPhotoPath = null;

        if ($request->hasFile('formal_photo')) {
            $newPhotoPath = $request->file('formal_photo')
                ->store('employee-photos', 'local');
        }

        $data = [
            'name' => $validated['name'],

            /*
         * Sinkronkan NIP baru ke kolom legacy `nik`
         * selama proses migrasi kode belum selesai.
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

            'bank_name' => User::BANK_BSI,
            'bank_account_number' => $validated['bank_account_number'],
            'bank_account_name' => $validated['bank_account_name'],

            'is_active' => (bool) $validated['is_active'],
            'role' => 'karyawan',
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
                    $karyawan,
                    $data,
                    $validated
                ) {
                    /** @var User $current */
                    $current = User::query()
                        ->whereKey($karyawan->id)
                        ->lockForUpdate()
                        ->firstOrFail();

                    $departmentChanged =
                        (int) $current->department_id
                        !== (int) $validated['department_id'];

                    $current->update($data);

                    if ($departmentChanged) {
                        LeaveRequest::query()
                            ->where('user_id', $current->id)
                            ->where('status', 'pending')
                            ->whereIn(
                                'kabid_status',
                                [
                                    LeaveRequest::KABID_STATUS_PENDING,
                                    LeaveRequest::KABID_STATUS_APPROVED,
                                ]
                            )
                            ->update([
                                'kabid_status' => LeaveRequest::KABID_STATUS_PENDING,
                                'kabid_reviewed_by' => null,
                                'kabid_reviewed_at' => null,
                                'kabid_rejection_reason' => null,
                            ]);
                    }
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
            ->route('admin.karyawan.index')
            ->with(
                'success',
                'Data karyawan berhasil diperbarui.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Delete
    |--------------------------------------------------------------------------
    */
    public function destroy(User $karyawan): RedirectResponse
    {
        $this->ensureKaryawan($karyawan);

        /*
    |--------------------------------------------------------------------------
    | Jangan hard delete jika sudah punya riwayat penting
    |--------------------------------------------------------------------------
    */
        $leaveRequestCount = $karyawan->leaveRequests()->count();
        $reimbursementCount = $karyawan->reimbursements()->count();

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
                ->route('admin.karyawan.index')
                ->with(
                    'error',
                    'Data karyawan "'
                        . $karyawan->name
                        . '" tidak dapat dihapus karena masih memiliki '
                        . implode(' dan ', $relatedData)
                        . '. Nonaktifkan akun melalui menu Edit agar riwayat tetap aman.'
                );
        }

        $photoPath = $karyawan->formal_photo_path;

        try {
            $karyawan->delete();
        } catch (QueryException $exception) {
            report($exception);

            return redirect()
                ->route('admin.karyawan.index')
                ->with(
                    'error',
                    'Data karyawan tidak dapat dihapus karena masih digunakan oleh data lain. '
                        . 'Silakan nonaktifkan akun karyawan melalui menu Edit.'
                );
        }

        /*
     * File foto baru dihapus hanya setelah record user berhasil dihapus.
     */
        if ($photoPath) {
            Storage::disk('local')->delete($photoPath);
        }

        return redirect()
            ->route('admin.karyawan.index')
            ->with(
                'success',
                'Data karyawan berhasil dihapus.'
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

    public function photo(User $karyawan): StreamedResponse
    {
        $this->ensureKaryawan($karyawan);

        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk('local');

        abort_unless(
            $karyawan->formal_photo_path
                && $disk->exists($karyawan->formal_photo_path),
            404
        );

        return $disk->response(
            $karyawan->formal_photo_path,
            basename($karyawan->formal_photo_path),
            [
                'Content-Disposition' => 'inline',
                'Cache-Control' => 'private, max-age=3600',
            ]
        );
    }



    private function ensureKaryawan(
        User $user
    ): void {

        abort_unless(
            $user->role === 'karyawan',
            404
        );
    }

    /*
|--------------------------------------------------------------------------
| Approve / ACC Karyawan
|--------------------------------------------------------------------------
*/

    public function approve(User $karyawan): RedirectResponse
    {
        /*
     * Pastikan user memang karyawan.
     */
        abort_unless(
            $karyawan->role === 'karyawan',
            404
        );


        /*
     * Karyawan harus sudah melengkapi profil.
     */
        if (! $karyawan->profile_completed_at) {

            return redirect()
                ->route('admin.karyawan.index')
                ->withErrors([
                    'approval' =>
                    'Data karyawan belum lengkap dan belum dapat di-ACC.'
                ]);
        }


        /*
     * Aktifkan akun.
     */
        $karyawan->update([
            'approval_status' => 'approved',
            'approval_rejection_reason' => null,
            'is_active' => true,
        ]);


        return redirect()
            ->route('admin.karyawan.index')
            ->with(
                'success',
                'Karyawan ' .
                    $karyawan->name .
                    ' berhasil di-ACC. Akun sekarang sudah aktif.'
            );
    }

    /*
|--------------------------------------------------------------------------
| Reject / Tolak Karyawan
|--------------------------------------------------------------------------
*/

    public function reject(
        Request $request,
        User $karyawan
    ): RedirectResponse {

        /*
     * Pastikan user memang karyawan.
     */
        abort_unless(
            $karyawan->role === 'karyawan',
            404
        );


        /*
     * Validasi alasan penolakan.
     */
        $validated = $request->validate(
            [
                'reason' => [
                    'required',
                    'string',
                    'max:1000'
                ],
            ],
            [
                'reason.required' =>
                'Alasan penolakan wajib diisi.',
            ]
        );


        /*
     * Tolak verifikasi.
     */
        $karyawan->update([
            'approval_status' =>
            'rejected',

            'approval_rejection_reason' =>
            $validated['reason'],

            'is_active' =>
            false,
        ]);


        return redirect()
            ->route('admin.karyawan.index')
            ->with(
                'success',
                'Data ' .
                    $karyawan->name .
                    ' ditolak dan dikembalikan untuk diperbaiki.'
            );
    }
}
