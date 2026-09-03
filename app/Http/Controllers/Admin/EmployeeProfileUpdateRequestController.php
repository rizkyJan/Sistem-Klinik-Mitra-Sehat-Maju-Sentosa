<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\EmployeeProfileUpdateRequest;
use App\Models\User;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class EmployeeProfileUpdateRequestController extends Controller
{
    /**
     * Daftar pengajuan perubahan profil pegawai.
     */
    public function index(
        Request $request
    ): View {

        $status = $request->string('status')
            ->lower()
            ->value();

        if (
            ! in_array(
                $status,
                ['pending', 'approved', 'rejected'],
                true
            )
        ) {
            $status = 'pending';
        }

        $search = trim(
            (string) $request->input('search', '')
        );

        $query = EmployeeProfileUpdateRequest::query()
            ->with([
                'user.department',
                'reviewer',
            ])
            ->where('status', $status)
            ->latest();

        if ($search !== '') {
            $query->whereHas(
                'user',
                function ($userQuery) use ($search) {
                    $userQuery->where(
                        function ($inner) use ($search) {
                            $inner
                                ->where(
                                    'name',
                                    'like',
                                    '%' . $search . '%'
                                )
                                ->orWhere(
                                    'nip',
                                    'like',
                                    '%' . $search . '%'
                                )
                                ->orWhere(
                                    'nik',
                                    'like',
                                    '%' . $search . '%'
                                )
                                ->orWhere(
                                    'nik_ktp',
                                    'like',
                                    '%' . $search . '%'
                                )
                                ->orWhere(
                                    'email',
                                    'like',
                                    '%' . $search . '%'
                                );
                        }
                    );
                }
            );
        }

        $requests = $query
            ->paginate(15)
            ->withQueryString();

        $counts = [
            'pending' =>
            EmployeeProfileUpdateRequest::query()
                ->where('status', 'pending')
                ->count(),

            'approved' =>
            EmployeeProfileUpdateRequest::query()
                ->where('status', 'approved')
                ->count(),

            'rejected' =>
            EmployeeProfileUpdateRequest::query()
                ->where('status', 'rejected')
                ->count(),
        ];

        return view(
            'admin.profile-update-requests.index',
            compact(
                'requests',
                'status',
                'search',
                'counts'
            )
        );
    }

    /**
     * Detail perbandingan perubahan.
     */
    public function show(
        EmployeeProfileUpdateRequest $profileUpdateRequest
    ): View {

        $profileUpdateRequest->load([
            'user.department',
            'reviewer',
        ]);

        $departmentLookup = Department::query()
            ->orderBy('name')
            ->pluck('name', 'id');

        return view(
            'admin.profile-update-requests.show',
            compact(
                'profileUpdateRequest',
                'departmentLookup'
            )
        );
    }

    /**
     * Setujui perubahan profil.
     *
     * Baru di method ini data tabel users benar-benar berubah.
     */
    public function approve(
        Request $request,
        EmployeeProfileUpdateRequest $profileUpdateRequest
    ): RedirectResponse {

        $admin = $request->user();

        $oldActivePhoto = null;

        DB::transaction(
            function () use (
                $admin,
                $profileUpdateRequest,
                &$oldActivePhoto
            ) {
                /*
                 * Lock pengajuan agar tidak bisa di-ACC dua kali.
                 */
                $lockedRequest =
                    EmployeeProfileUpdateRequest::query()
                    ->whereKey(
                        $profileUpdateRequest->id
                    )
                    ->lockForUpdate()
                    ->firstOrFail();

                if (
                    $lockedRequest->status
                    !== EmployeeProfileUpdateRequest::STATUS_PENDING
                ) {
                    throw ValidationException::withMessages([
                        'profile_update' =>
                        'Pengajuan ini sudah diproses sebelumnya.',
                    ]);
                }

                $user = User::query()
                    ->whereKey(
                        $lockedRequest->user_id
                    )
                    ->lockForUpdate()
                    ->firstOrFail();

                if (
                    ! in_array(
                        $user->role,
                        ['karyawan', 'kabid'],
                        true
                    )
                ) {
                    throw ValidationException::withMessages([
                        'profile_update' =>
                        'Pengajuan hanya boleh berasal dari Karyawan atau Kabid.',
                    ]);
                }

                $newData =
                    is_array(
                        $lockedRequest->new_data
                    )
                    ? $lockedRequest->new_data
                    : [];

                /*
                 * Hanya field ini yang boleh diterapkan.
                 * Field role / is_active / approval_status tidak akan
                 * pernah diproses meskipun JSON dimanipulasi.
                 */
                $allowedFields = [
                    'name',
                    'email',
                    'nip',
                    'nik_ktp',
                    'whatsapp',
                    'join_date',
                    'department_id',
                    'birth_place',
                    'birth_date',
                    'ktp_address',
                    'domicile_address',
                    'blood_type',
                    'religion',
                    'sip_number',
                    'sip_valid_from',
                    'sip_valid_until',
                    'bank_account_number',
                    'bank_account_name',
                ];

                $newData =
                    array_intersect_key(
                        $newData,
                        array_flip(
                            $allowedFields
                        )
                    );

                /*
                 * Validasi ULANG saat ACC.
                 *
                 * Ini penting karena antara waktu pegawai mengajukan
                 * dan waktu Admin meng-ACC, data pegawai lain bisa berubah.
                 */
                $validator = Validator::make(
                    $newData,
                    $this->approvalRules(
                        $user,
                        $newData
                    ),
                    $this->approvalMessages()
                );

                $validator->validate();

                /*
                 * Khusus Kabid:
                 * bidang tujuan tidak boleh sudah mempunyai Kabid aktif lain.
                 */
                $targetDepartmentId =
                    array_key_exists(
                        'department_id',
                        $newData
                    )
                    ? (int) $newData['department_id']
                    : (int) $user->department_id;

                if (
                    $user->role === 'kabid'
                    && $targetDepartmentId > 0
                ) {
                    Department::query()
                        ->whereKey(
                            $targetDepartmentId
                        )
                        ->lockForUpdate()
                        ->firstOrFail();

                    $alreadyHasAnotherKabid =
                        User::query()
                        ->where(
                            'role',
                            'kabid'
                        )
                        ->where(
                            'department_id',
                            $targetDepartmentId
                        )
                        ->whereKeyNot(
                            $user->id
                        )
                        ->where(
                            'approval_status',
                            'approved'
                        )
                        ->where(
                            'is_active',
                            true
                        )
                        ->exists();

                    if ($alreadyHasAnotherKabid) {
                        throw ValidationException::withMessages([
                            'department_id' =>
                            'Bidang tujuan sudah mempunyai Kabid aktif. '
                                . 'Pengajuan belum dapat disetujui.',
                        ]);
                    }
                }

                /*
                 * Terapkan data baru.
                 */
                $updateData = [];

                foreach (
                    $newData
                    as $field => $value
                ) {
                    $updateData[$field] =
                        $value === ''
                        ? null
                        : $value;
                }

                /*
                 * Selama masa transisi:
                 * NIP baru juga harus menyinkronkan kolom `nik` legacy.
                 */
                if (
                    array_key_exists(
                        'nip',
                        $updateData
                    )
                ) {
                    $updateData['nik'] =
                        $updateData['nip'];
                }

                /*
                 * Bank tidak pernah bisa berubah dari BSI.
                 */
                if (
                    array_key_exists(
                        'bank_account_number',
                        $updateData
                    )
                    || array_key_exists(
                        'bank_account_name',
                        $updateData
                    )
                ) {
                    $updateData['bank_name'] =
                        User::BANK_BSI;
                }

                /*
                 * Jika email berubah, status verifikasi email direset.
                 */
                if (
                    array_key_exists(
                        'email',
                        $updateData
                    )
                    && $updateData['email']
                    !== $user->email
                ) {
                    $updateData['email_verified_at'] =
                        null;
                }

                /*
                 * Pas foto.
                 *
                 * Foto calon dipindahkan ke folder foto aktif.
                 */
                if (
                    filled(
                        $lockedRequest->new_photo_path
                    )
                ) {
                    /** @var FilesystemAdapter $disk */
                    $disk =
                        Storage::disk('local');

                    if (
                        ! $disk->exists(
                            $lockedRequest->new_photo_path
                        )
                    ) {
                        throw ValidationException::withMessages([
                            'formal_photo' =>
                            'File pas foto baru tidak ditemukan. '
                                . 'Minta pegawai mengajukan ulang perubahan foto.',
                        ]);
                    }

                    $extension =
                        pathinfo(
                            $lockedRequest->new_photo_path,
                            PATHINFO_EXTENSION
                        );

                    $extension =
                        $extension !== ''
                        ? strtolower($extension)
                        : 'jpg';

                    $newPermanentPath =
                        'employee-photos/'
                        . $user->id
                        . '-'
                        . Str::uuid()
                        . '.'
                        . $extension;

                    $disk->makeDirectory(
                        'employee-photos'
                    );

                    $moved =
                        $disk->move(
                            $lockedRequest->new_photo_path,
                            $newPermanentPath
                        );

                    if (! $moved) {
                        throw ValidationException::withMessages([
                            'formal_photo' =>
                            'Pas foto baru gagal dipindahkan ke penyimpanan aktif.',
                        ]);
                    }

                    $oldActivePhoto =
                        $user->formal_photo_path;

                    $updateData['formal_photo_path'] =
                        $newPermanentPath;

                    /*
                     * Path calon sudah dipindahkan, maka kosongkan.
                     */
                    $lockedRequest->new_photo_path =
                        null;
                }

                $user->forceFill(
                    $updateData
                )->save();

                $lockedRequest->forceFill([
                    'status' =>
                    EmployeeProfileUpdateRequest::STATUS_APPROVED,

                    'rejection_reason' =>
                    null,

                    'reviewed_by' =>
                    $admin->id,

                    'reviewed_at' =>
                    now(),
                ])->save();
            }
        );

        /*
         * Hapus foto aktif lama SETELAH transaction sukses.
         */
        if (
            $oldActivePhoto
            && Storage::disk('local')
            ->exists(
                $oldActivePhoto
            )
        ) {
            Storage::disk('local')
                ->delete(
                    $oldActivePhoto
                );
        }

        return redirect()
            ->route(
                'admin.profile-updates.show',
                $profileUpdateRequest
            )
            ->with(
                'success',
                'Perubahan profil berhasil disetujui dan data aktif pegawai telah diperbarui.'
            );
    }

    /**
     * Tolak perubahan profil.
     */
    public function reject(
        Request $request,
        EmployeeProfileUpdateRequest $profileUpdateRequest
    ): RedirectResponse {

        $validated = $request->validate(
            [
                'rejection_reason' => [
                    'required',
                    'string',
                    'min:5',
                    'max:2000',
                ],
            ],
            [
                'rejection_reason.required' =>
                'Alasan penolakan wajib diisi.',

                'rejection_reason.min' =>
                'Alasan penolakan minimal 5 karakter.',

                'rejection_reason.max' =>
                'Alasan penolakan maksimal 2000 karakter.',
            ]
        );

        $photoToDelete = null;

        DB::transaction(
            function () use (
                $request,
                $profileUpdateRequest,
                $validated,
                &$photoToDelete
            ) {
                $lockedRequest =
                    EmployeeProfileUpdateRequest::query()
                    ->whereKey(
                        $profileUpdateRequest->id
                    )
                    ->lockForUpdate()
                    ->firstOrFail();

                if (
                    $lockedRequest->status
                    !== EmployeeProfileUpdateRequest::STATUS_PENDING
                ) {
                    throw ValidationException::withMessages([
                        'profile_update' =>
                        'Pengajuan ini sudah diproses sebelumnya.',
                    ]);
                }

                $photoToDelete =
                    $lockedRequest->new_photo_path;

                $lockedRequest->forceFill([
                    'status' =>
                    EmployeeProfileUpdateRequest::STATUS_REJECTED,

                    'rejection_reason' =>
                    $validated['rejection_reason'],

                    'reviewed_by' =>
                    $request->user()->id,

                    'reviewed_at' =>
                    now(),

                    /*
                     * Foto calon akan dihapus setelah transaksi sukses.
                     */
                    'new_photo_path' =>
                    null,
                ])->save();
            }
        );

        if (
            $photoToDelete
            && Storage::disk('local')
            ->exists(
                $photoToDelete
            )
        ) {
            Storage::disk('local')
                ->delete(
                    $photoToDelete
                );
        }

        return redirect()
            ->route(
                'admin.profile-updates.show',
                $profileUpdateRequest
            )
            ->with(
                'success',
                'Pengajuan perubahan profil telah ditolak. Data aktif pegawai tidak berubah.'
            );
    }

    /**
     * Foto AKTIF pegawai.
     */
    public function currentPhoto(
        EmployeeProfileUpdateRequest $profileUpdateRequest
    ): BinaryFileResponse {

        $profileUpdateRequest->loadMissing(
            'user'
        );

        $path =
            $profileUpdateRequest
            ->user
            ?->formal_photo_path;

        abort_if(
            blank($path),
            404
        );

        return $this->privateFile(
            $path
        );
    }

    /**
     * Foto BARU yang masih menjadi calon.
     */
    public function proposedPhoto(
        EmployeeProfileUpdateRequest $profileUpdateRequest
    ): BinaryFileResponse {

        abort_if(
            blank(
                $profileUpdateRequest->new_photo_path
            ),
            404
        );

        return $this->privateFile(
            $profileUpdateRequest->new_photo_path
        );
    }

    /**
     * Rules yang hanya memvalidasi field yang benar-benar
     * terdapat dalam new_data.
     */
    private function approvalRules(
        User $user,
        array $newData
    ): array {

        $rules = [];

        if (array_key_exists('name', $newData)) {
            $rules['name'] = [
                'required',
                'string',
                'max:255',
            ];
        }

        if (array_key_exists('email', $newData)) {
            $rules['email'] = [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique('users', 'email')
                    ->ignore($user->id),
            ];
        }

        if (array_key_exists('nip', $newData)) {
            $rules['nip'] = [
                'required',
                'string',
                'max:50',
                Rule::unique('users', 'nip')
                    ->ignore($user->id),
                Rule::unique('users', 'nik')
                    ->ignore($user->id),
            ];
        }

        if (array_key_exists('nik_ktp', $newData)) {
            $rules['nik_ktp'] = [
                'required',
                'string',
                'regex:/^[0-9]{16}$/',
                Rule::unique('users', 'nik_ktp')
                    ->ignore($user->id),
            ];
        }

        if (array_key_exists('whatsapp', $newData)) {
            $rules['whatsapp'] = [
                'required',
                'string',
                'max:20',
            ];
        }

        if (array_key_exists('join_date', $newData)) {
            $rules['join_date'] = [
                'required',
                'date',
                'before_or_equal:today',
            ];
        }

        if (array_key_exists('department_id', $newData)) {
            $rules['department_id'] = [
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
            ];
        }

        if (array_key_exists('birth_place', $newData)) {
            $rules['birth_place'] = [
                'required',
                'string',
                'max:100',
            ];
        }

        if (array_key_exists('birth_date', $newData)) {
            $rules['birth_date'] = [
                'required',
                'date',
                'before:today',
            ];
        }

        if (array_key_exists('ktp_address', $newData)) {
            $rules['ktp_address'] = [
                'required',
                'string',
                'max:3000',
            ];
        }

        if (array_key_exists('domicile_address', $newData)) {
            $rules['domicile_address'] = [
                'required',
                'string',
                'max:3000',
            ];
        }

        if (array_key_exists('blood_type', $newData)) {
            $rules['blood_type'] = [
                'required',
                Rule::in([
                    'A',
                    'B',
                    'AB',
                    'O',
                ]),
            ];
        }

        if (array_key_exists('religion', $newData)) {
            $rules['religion'] = [
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
            ];
        }

        /*
         * Untuk SIP, validasi kombinasinya dilakukan dari nilai akhir
         * (data aktif + new_data), bukan hanya field yang berubah.
         */
        if (
            array_key_exists('sip_number', $newData)
            || array_key_exists('sip_valid_from', $newData)
            || array_key_exists('sip_valid_until', $newData)
        ) {
            $finalSipNumber =
                array_key_exists('sip_number', $newData)
                ? $newData['sip_number']
                : $user->sip_number;

            $finalSipFrom =
                array_key_exists('sip_valid_from', $newData)
                ? $newData['sip_valid_from']
                : optional(
                    $user->sip_valid_from
                )->format('Y-m-d');

            $finalSipUntil =
                array_key_exists('sip_valid_until', $newData)
                ? $newData['sip_valid_until']
                : optional(
                    $user->sip_valid_until
                )->format('Y-m-d');

            Validator::make(
                [
                    'sip_number' =>
                    $finalSipNumber,

                    'sip_valid_from' =>
                    $finalSipFrom,

                    'sip_valid_until' =>
                    $finalSipUntil,
                ],
                [
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
                ],
                $this->approvalMessages()
            )->validate();

            if (array_key_exists('sip_number', $newData)) {
                $rules['sip_number'] = [
                    'nullable',
                    'string',
                    'max:100',
                ];
            }

            if (array_key_exists('sip_valid_from', $newData)) {
                $rules['sip_valid_from'] = [
                    'nullable',
                    'date',
                ];
            }

            if (array_key_exists('sip_valid_until', $newData)) {
                $rules['sip_valid_until'] = [
                    'nullable',
                    'date',
                ];
            }
        }

        if (array_key_exists('bank_account_number', $newData)) {
            $rules['bank_account_number'] = [
                'required',
                'string',
                'regex:/^[0-9]{8,20}$/',
            ];
        }

        if (array_key_exists('bank_account_name', $newData)) {
            $rules['bank_account_name'] = [
                'required',
                'string',
                'max:150',
            ];
        }

        return $rules;
    }

    private function approvalMessages(): array
    {
        return [
            'email.unique' =>
            'Email baru sudah digunakan akun lain.',

            'nip.unique' =>
            'NIP baru sudah digunakan pegawai lain.',

            'nik_ktp.regex' =>
            'NIK KTP baru harus tepat 16 digit angka.',

            'nik_ktp.unique' =>
            'NIK KTP baru sudah digunakan pegawai lain.',

            'department_id.exists' =>
            'Bidang tujuan tidak tersedia atau sudah nonaktif.',

            'sip_number.required_with' =>
            'Nomor SIP harus dilengkapi jika masa berlaku SIP digunakan.',

            'sip_valid_from.required_with' =>
            'Tanggal mulai SIP harus dilengkapi.',

            'sip_valid_until.required_with' =>
            'Tanggal berakhir SIP harus dilengkapi.',

            'sip_valid_until.after_or_equal' =>
            'Tanggal berakhir SIP tidak boleh sebelum tanggal mulai SIP.',

            'bank_account_number.regex' =>
            'Nomor rekening BSI harus berupa 8-20 digit angka.',
        ];
    }

    private function privateFile(
        string $path
    ): BinaryFileResponse {

        /** @var FilesystemAdapter $disk */
        $disk =
            Storage::disk('local');

        abort_unless(
            $disk->exists($path),
            404
        );

        return response()->file(
            $disk->path($path),
            [
                'Content-Type' =>
                $disk->mimeType($path)
                    ?: 'application/octet-stream',

                'Cache-Control' =>
                'private, max-age=300',
            ]
        );
    }
}
