<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class EmployeeOnboardingController extends Controller
{
    /**
     * Form melengkapi / memperbaiki data pegawai setelah login Google.
     */
    public function edit(
        Request $request
    ): View|RedirectResponse {

        $user =
            $request->user();

        if (
            $user->role === 'admin'
        ) {
            return redirect()
                ->route(
                    'admin.dashboard'
                );
        }

        /*
         * Profil yang sudah lengkap hanya boleh masuk kembali
         * ke form onboarding jika statusnya REJECTED,
         * agar user bisa memperbaiki data sesuai catatan Admin.
         */
        if (
            $user->profile_completed_at
            && $user->approval_status !== 'rejected'
        ) {
            return $this
                ->redirectAfterOnboarding(
                    $user
                );
        }

        $departments =
            Department::query()
            ->where(
                'is_active',
                true
            )
            ->orderBy('name')
            ->get();

        $kabidDepartmentIds =
            User::query()
            ->where(
                'role',
                'kabid'
            )
            ->where(
                'approval_status',
                'approved'
            )
            ->whereNotNull(
                'department_id'
            )
            ->whereKeyNot(
                $user->id
            )
            ->pluck(
                'department_id'
            )
            ->map(
                fn($id) => (int) $id
            )
            ->values()
            ->all();

        return view(
            'auth.complete-employee-profile',
            compact(
                'user',
                'departments',
                'kabidDepartmentIds'
            )
        );
    }

    /**
     * Simpan data onboarding Google / perbaikan data yang ditolak Admin.
     */
    public function update(
        Request $request
    ): RedirectResponse {

        $user =
            $request->user();

        if (
            $user->role === 'admin'
        ) {
            abort(403);
        }

        /*
         * Jangan izinkan user approved mengubah data dari onboarding.
         * Update profil user aktif akan dibuat pada Step 4
         * dan wajib melalui ACC Admin.
         */
        if (
            $user->profile_completed_at
            && $user->approval_status !== 'rejected'
        ) {
            return $this
                ->redirectAfterOnboarding(
                    $user
                );
        }

        $validated =
            $request->validate(
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

                        Rule::unique(
                            'users',
                            'nip'
                        )->ignore(
                            $user->id
                        ),

                        Rule::unique(
                            'users',
                            'nik'
                        )->ignore(
                            $user->id
                        ),
                    ],

                    'nik_ktp' => [
                        'required',
                        'string',
                        'regex:/^[0-9]{16}$/',

                        Rule::unique(
                            'users',
                            'nik_ktp'
                        )->ignore(
                            $user->id
                        ),
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

                        Rule::exists(
                            'departments',
                            'id'
                        )->where(
                            fn($query) =>
                            $query->where(
                                'is_active',
                                true
                            )
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
                        Rule::requiredIf(
                            blank(
                                $user->formal_photo_path
                            )
                        ),
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
                    'Pas foto formal harus berupa file gambar.',

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

        $newPhotoPath =
            null;

        if (
            $request->hasFile(
                'formal_photo'
            )
        ) {
            $newPhotoPath =
                $request
                ->file('formal_photo')
                ->store(
                    'employee-photos',
                    'local'
                );
        }

        $oldPhotoPath =
            $user->formal_photo_path;

        try {
            DB::transaction(
                function () use (
                    $user,
                    $validated,
                    $newPhotoPath
                ) {

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
                            ->whereKeyNot(
                                $user->id
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

                    $data = [
                        'name' =>
                        $validated['name'],

                        /*
                         * Kolom nik lama tetap disinkronkan ke NIP.
                         */
                        'nik' =>
                        $validated['nip'],

                        'nip' =>
                        $validated['nip'],

                        'nik_ktp' =>
                        $validated['nik_ktp'],

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

                        'bank_name' =>
                        User::BANK_BSI,

                        'bank_account_number' =>
                        $validated['bank_account_number'],

                        'bank_account_name' =>
                        $validated['bank_account_name'],

                        /*
                         * Kirim / kirim ulang untuk verifikasi.
                         */
                        'approval_status' =>
                        'pending',

                        'approval_rejection_reason' =>
                        null,

                        'is_active' =>
                        false,

                        'profile_completed_at' =>
                        now(),
                    ];

                    if (
                        $newPhotoPath
                    ) {
                        $data['formal_photo_path'] =
                            $newPhotoPath;
                    }

                    $user->forceFill(
                        $data
                    )->save();
                }
            );
        } catch (\Throwable $exception) {
            if (
                $newPhotoPath
            ) {
                Storage::disk('local')
                    ->delete(
                        $newPhotoPath
                    );
            }

            throw $exception;
        }

        /*
         * Hapus foto lama hanya setelah database berhasil diperbarui.
         */
        if (
            $newPhotoPath
            && $oldPhotoPath
            && $oldPhotoPath !== $newPhotoPath
        ) {
            Storage::disk('local')
                ->delete(
                    $oldPhotoPath
                );
        }

        return redirect()
            ->route(
                'employee.approval.waiting'
            )
            ->with(
                'success',
                'Data berhasil dikirim. Silakan tunggu verifikasi Administrator.'
            );
    }

    /**
     * Halaman menunggu verifikasi Admin.
     */
    public function waiting(
        Request $request
    ): View|RedirectResponse {

        $user =
            $request->user();

        if (
            $user->role === 'admin'
        ) {
            return redirect()
                ->route(
                    'admin.dashboard'
                );
        }

        if (
            ! $user->profile_completed_at
        ) {
            return redirect()
                ->route(
                    'employee.profile.complete'
                );
        }

        if (
            $user->approval_status === 'approved'
            && $user->is_active
        ) {
            return $this
                ->redirectAfterOnboarding(
                    $user
                );
        }

        return view(
            'auth.waiting-approval',
            compact('user')
        );
    }

    /**
     * Redirect berdasarkan role/status.
     */
    private function redirectAfterOnboarding(
        User $user
    ): RedirectResponse {

        if (
            $user->approval_status !== 'approved'
            || ! $user->is_active
        ) {
            return redirect()
                ->route(
                    'employee.approval.waiting'
                );
        }

        if (
            $user->role === 'kabid'
        ) {
            return redirect()
                ->route(
                    'kabid.dashboard'
                );
        }

        return redirect()
            ->route(
                'karyawan.dashboard'
            );
    }
}
