<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Form pendaftaran mandiri Karyawan/Kabid.
     */
    public function create(): View
    {
        $departments = Department::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        /*
         * Hanya Kabid yang SUDAH approved yang dianggap
         * resmi mengunci sebuah bidang.
         */
        $kabidDepartmentIds = User::query()
            ->where('role', 'kabid')
            ->where('approval_status', 'approved')
            ->whereNotNull('department_id')
            ->pluck('department_id')
            ->map(fn($id) => (int) $id)
            ->values()
            ->all();

        return view(
            'auth.register',
            compact(
                'departments',
                'kabidDepartmentIds'
            )
        );
    }

    /**
     * Simpan pendaftaran mandiri.
     *
     * Semua pendaftar Karyawan/Kabid tetap menunggu ACC Admin.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate(
            [
                'name' => [
                    'required',
                    'string',
                    'max:255',
                ],

                'nip' => [
                    'required',
                    'string',
                    'max:50',
                    'unique:users,nip',

                    /*
                     * Kolom nik lama masih hidup sementara.
                     * NIP juga tidak boleh bentrok dengan legacy nik.
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
                    'string',
                    'lowercase',
                    'email',
                    'max:255',
                    'unique:users,email',
                ],

                'password' => [
                    'required',
                    'confirmed',
                    Rules\Password::defaults(),
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
                        ->where(
                            fn($query) =>
                            $query->where('is_active', true)
                        ),
                ],

                'role' => [
                    'required',
                    Rule::in([
                        'karyawan',
                        'kabid',
                    ]),
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
                    Rule::in([
                        'A',
                        'B',
                        'AB',
                        'O',
                    ]),
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

                /*
                 * SIP opsional.
                 * Kalau salah satu bagian SIP diisi,
                 * data SIP harus lengkap.
                 */
                'sip_number' => [
                    'nullable',
                    'required_with:sip_valid_from,sip_valid_until',
                    'string',
                    'max:100',
                ],

                'sip_valid_from' => [
                    'nullable',
                    'required_with:sip_number,sip_valid_until',
                    'date',
                ],

                'sip_valid_until' => [
                    'nullable',
                    'required_with:sip_number,sip_valid_from',
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
                 * Bank tidak dipilih.
                 * Selalu BSI.
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
            ],
            [
                'name.required' =>
                'Nama lengkap wajib diisi.',

                'nip.required' =>
                'NIP / ID Pegawai wajib diisi.',

                'nip.unique' =>
                'NIP / ID Pegawai sudah digunakan.',

                'nik_ktp.required' =>
                'NIK KTP wajib diisi.',

                'nik_ktp.regex' =>
                'NIK KTP harus tepat 16 digit angka.',

                'nik_ktp.unique' =>
                'NIK KTP sudah digunakan.',

                'email.required' =>
                'Email wajib diisi.',

                'email.email' =>
                'Format email tidak valid.',

                'email.unique' =>
                'Email sudah digunakan.',

                'password.required' =>
                'Password wajib diisi.',

                'password.confirmed' =>
                'Konfirmasi password tidak sama.',

                'whatsapp.required' =>
                'Nomor WhatsApp wajib diisi.',

                'join_date.required' =>
                'Tanggal mulai kerja wajib diisi.',

                'join_date.before_or_equal' =>
                'Tanggal mulai kerja tidak boleh melewati hari ini.',

                'department_id.required' =>
                'Bidang wajib dipilih.',

                'department_id.exists' =>
                'Bidang yang dipilih tidak tersedia.',

                'role.required' =>
                'Jabatan wajib dipilih.',

                'role.in' =>
                'Jabatan hanya boleh Karyawan atau Kabid.',

                'birth_place.required' =>
                'Tempat lahir wajib diisi.',

                'birth_date.required' =>
                'Tanggal lahir wajib diisi.',

                'birth_date.before' =>
                'Tanggal lahir harus sebelum hari ini.',

                'ktp_address.required' =>
                'Alamat KTP wajib diisi.',

                'domicile_address.required' =>
                'Alamat domisili wajib diisi.',

                'blood_type.required' =>
                'Golongan darah wajib dipilih.',

                'religion.required' =>
                'Agama wajib dipilih.',

                'sip_number.required_with' =>
                'Nomor SIP wajib diisi jika masa berlaku SIP diisi.',

                'sip_valid_from.required_with' =>
                'Tanggal mulai SIP wajib diisi jika data SIP digunakan.',

                'sip_valid_until.required_with' =>
                'Tanggal berakhir SIP wajib diisi jika data SIP digunakan.',

                'sip_valid_until.after_or_equal' =>
                'Tanggal berakhir SIP tidak boleh sebelum tanggal mulai SIP.',

                'formal_photo.required' =>
                'Pas foto formal wajib diunggah.',

                'formal_photo.image' =>
                'Pas foto formal harus berupa gambar.',

                'formal_photo.mimes' =>
                'Pas foto formal harus JPG, JPEG, PNG, atau WEBP.',

                'formal_photo.max' =>
                'Ukuran pas foto formal maksimal 2 MB.',

                'bank_account_number.required' =>
                'Nomor rekening BSI wajib diisi.',

                'bank_account_number.regex' =>
                'Nomor rekening BSI harus berupa 8-20 digit angka.',

                'bank_account_name.required' =>
                'Nama pemilik rekening BSI wajib diisi.',
            ]
        );

        $photoPath = $request
            ->file('formal_photo')
            ->store(
                'employee-photos',
                'local'
            );

        try {
            $user = DB::transaction(
                function () use (
                    $validated,
                    $photoPath
                ): User {

                    /*
                     * Jika mendaftar sebagai Kabid,
                     * lock bidang dan pastikan bidang tersebut
                     * belum mempunyai Kabid approved.
                     */
                    if (
                        $validated['role']
                        === 'kabid'
                    ) {
                        Department::query()
                            ->whereKey(
                                $validated['department_id']
                            )
                            ->lockForUpdate()
                            ->firstOrFail();

                        $departmentAlreadyHasKabid =
                            User::query()
                            ->where(
                                'role',
                                'kabid'
                            )
                            ->where(
                                'department_id',
                                $validated['department_id']
                            )
                            ->where(
                                'approval_status',
                                'approved'
                            )
                            ->exists();

                        if (
                            $departmentAlreadyHasKabid
                        ) {
                            throw ValidationException::withMessages([
                                'department_id' =>
                                'Bidang yang dipilih sudah memiliki Kabid yang disetujui Admin. '
                                    . 'Silakan pilih Karyawan atau hubungi Administrator.',
                            ]);
                        }
                    }

                    return User::create([
                        'name' =>
                        $validated['name'],

                        /*
                         * Selama masa transisi:
                         * nik legacy = nip
                         */
                        'nik' =>
                        $validated['nip'],

                        'nip' =>
                        $validated['nip'],

                        'nik_ktp' =>
                        $validated['nik_ktp'],

                        'email' =>
                        $validated['email'],

                        'password' =>
                        Hash::make(
                            $validated['password']
                        ),

                        'whatsapp' =>
                        $validated['whatsapp'],

                        'join_date' =>
                        $validated['join_date'],

                        'department_id' =>
                        $validated['department_id'],

                        'role' =>
                        $validated['role'],

                        'birth_place' =>
                        $validated['birth_place'],

                        'birth_date' =>
                        $validated['birth_date'],

                        'ktp_address' =>
                        $validated['ktp_address'],

                        'domicile_address' =>
                        $validated['domicile_address'],

                        'blood_type' =>
                        $validated['blood_type'],

                        'religion' =>
                        $validated['religion'],

                        'sip_number' =>
                        $validated['sip_number']
                            ?? null,

                        'sip_valid_from' =>
                        $validated['sip_valid_from']
                            ?? null,

                        'sip_valid_until' =>
                        $validated['sip_valid_until']
                            ?? null,

                        'formal_photo_path' =>
                        $photoPath,

                        'bank_name' =>
                        User::BANK_BSI,

                        'bank_account_number' =>
                        $validated['bank_account_number'],

                        'bank_account_name' =>
                        $validated['bank_account_name'],

                        /*
                         * Pendaftaran sendiri selalu perlu ACC Admin.
                         */
                        'approval_status' =>
                        'pending',

                        'approval_rejection_reason' =>
                        null,

                        'is_active' =>
                        false,

                        'profile_completed_at' =>
                        now(),
                    ]);
                }
            );
        } catch (\Throwable $exception) {
            Storage::disk('local')
                ->delete($photoPath);

            throw $exception;
        }

        event(
            new Registered($user)
        );

        /*
         * Login sementara agar user bisa melihat
         * halaman Menunggu Verifikasi.
         */
        Auth::login($user);

        $request->session()
            ->regenerate();

        return redirect()
            ->route(
                'employee.approval.waiting'
            )
            ->with(
                'success',
                'Pendaftaran berhasil dikirim. Silakan tunggu verifikasi Administrator.'
            );
    }
}
