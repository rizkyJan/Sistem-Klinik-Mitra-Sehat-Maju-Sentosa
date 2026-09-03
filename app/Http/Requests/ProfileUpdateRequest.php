<?php

namespace App\Http\Requests;

use App\Models\Department;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => trim((string) $this->input('name')),
            'email' => strtolower(trim((string) $this->input('email'))),
            'nip' => trim((string) $this->input('nip')),
            'nik_ktp' => preg_replace(
                '/\D+/',
                '',
                (string) $this->input('nik_ktp')
            ),
            'whatsapp' => trim((string) $this->input('whatsapp')),
            'bank_account_number' => preg_replace(
                '/\D+/',
                '',
                (string) $this->input('bank_account_number')
            ),
            'bank_account_name' => trim(
                (string) $this->input('bank_account_name')
            ),
        ]);
    }

    public function rules(): array
    {
        $user = $this->user();

        /*
         * Admin tetap dapat mengubah data akun dasarnya secara langsung.
         * Workflow ACC perubahan profil hanya untuk Karyawan/Kabid.
         */
        if ($user?->role === 'admin') {
            return [
                'name' => [
                    'required',
                    'string',
                    'max:255',
                ],

                'email' => [
                    'required',
                    'string',
                    'lowercase',
                    'email',
                    'max:255',
                    Rule::unique('users', 'email')
                        ->ignore($user->id),
                ],
            ];
        }

        return [
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique('users', 'email')
                    ->ignore($user->id),
            ],

            'nip' => [
                'required',
                'string',
                'max:50',
                Rule::unique('users', 'nip')
                    ->ignore($user->id),

                /*
                 * Kolom `nik` legacy masih ada selama masa transisi.
                 */
                Rule::unique('users', 'nik')
                    ->ignore($user->id),
            ],

            'nik_ktp' => [
                'required',
                'string',
                'regex:/^[0-9]{16}$/',
                Rule::unique('users', 'nik_ktp')
                    ->ignore($user->id),
            ],

            'whatsapp' => [
                'required',
                'string',
                'max:20',
            ],

            /*
             * Sesuai kebutuhan "semua data profil bisa diajukan update",
             * tanggal mulai kerja dan bidang boleh diajukan perubahan,
             * tetapi TIDAK langsung aktif sebelum ACC Admin.
             */
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

            /*
             * Foto tidak wajib kalau user hanya mengubah data teks.
             */
            'formal_photo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],

            /*
             * Bank tidak pernah diterima dari request.
             * Backend selalu mempertahankan BSI.
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
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' =>
            'Nama lengkap wajib diisi.',

            'email.required' =>
            'Email wajib diisi.',

            'email.email' =>
            'Format email tidak valid.',

            'email.unique' =>
            'Email sudah digunakan akun lain.',

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
        ];
    }
}
