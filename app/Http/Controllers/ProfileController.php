<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Department;
use App\Models\EmployeeProfileUpdateRequest;
use App\Models\User;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ProfileController extends Controller
{
    /**
     * Halaman Profil.
     *
     * Admin:
     * - update nama/email/foto langsung.
     *
     * Karyawan/Kabid:
     * - semua perubahan profil masuk sebagai pengajuan
     *   dan menunggu ACC Admin.
     */
    public function edit(
        Request $request
    ): View|RedirectResponse {

        $user = $request->user()
            ->load('department');

        if (
            $user->role !== 'admin'
            && (
                $user->approval_status !== 'approved'
                || ! $user->is_active
            )
        ) {
            return redirect()
                ->route(
                    'employee.approval.waiting'
                );
        }

        $departments = collect();
        $pendingProfileUpdateRequest = null;
        $latestProfileUpdateRequest = null;
        $departmentLookup = collect();

        if (
            in_array(
                $user->role,
                ['karyawan', 'kabid'],
                true
            )
        ) {
            $departments = Department::query()
                ->where('is_active', true)
                ->orderBy('name')
                ->get();

            /*
             * Lookup semua bidang, termasuk bidang lama yang mungkin
             * sudah nonaktif, untuk menampilkan riwayat pengajuan.
             */
            $departmentLookup = Department::query()
                ->orderBy('name')
                ->pluck('name', 'id');

            $pendingProfileUpdateRequest =
                $user->profileUpdateRequests()
                ->where(
                    'status',
                    EmployeeProfileUpdateRequest::STATUS_PENDING
                )
                ->latest()
                ->first();

            $latestProfileUpdateRequest =
                $user->profileUpdateRequests()
                ->latest()
                ->first();
        }

        return view(
            'profile.edit',
            compact(
                'user',
                'departments',
                'departmentLookup',
                'pendingProfileUpdateRequest',
                'latestProfileUpdateRequest'
            )
        );
    }

    /**
     * Update Profil.
     *
     * Pegawai TIDAK langsung mengubah tabel users.
     */
    public function update(
        ProfileUpdateRequest $request
    ): RedirectResponse {

        $user = $request->user();

        /*
        |--------------------------------------------------------------------------
        | ADMIN: update profil dasar secara langsung
        |--------------------------------------------------------------------------
        */
        if (
            $user->role === 'admin'
        ) {
            $validated = $request->validated();

            $emailChanged =
                $validated['email']
                !== $user->email;

            $oldPhotoPath = $user->formal_photo_path;
            $newPhotoPath = null;

            if ($request->hasFile('formal_photo')) {
                $newPhotoPath = $request->file('formal_photo')
                    ->store('admin-photos', 'local');
            }

            try {
                $user->forceFill([
                    'name' =>
                    $validated['name'],

                    'email' =>
                    $validated['email'],
                ]);

                if ($newPhotoPath) {
                    $user->formal_photo_path = $newPhotoPath;
                }

                if ($emailChanged) {
                    $user->email_verified_at = null;
                }

                $user->save();
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
                ->route('profile.edit')
                ->with(
                    'success',
                    'Profil Administrator berhasil diperbarui.'
                );
        }

        $this->ensureEmployeeCanUseProfile(
            $user
        );

        /*
         * Satu pegawai hanya boleh mempunyai satu
         * pengajuan PENDING dalam satu waktu.
         */
        $alreadyPending =
            $user->profileUpdateRequests()
            ->where(
                'status',
                EmployeeProfileUpdateRequest::STATUS_PENDING
            )
            ->exists();

        if ($alreadyPending) {
            return redirect()
                ->route('profile.edit')
                ->with(
                    'error',
                    'Masih ada perubahan profil yang menunggu persetujuan Admin. '
                        . 'Tunggu pengajuan tersebut diproses terlebih dahulu.'
                );
        }

        $validated =
            $request->validated();

        /*
         * Data yang boleh diajukan pegawai.
         *
         * Role, is_active, approval_status, dan field hak akses
         * TIDAK pernah bisa diajukan dari halaman Profil.
         */
        $proposed = [
            'name' =>
            $validated['name'],

            'email' =>
            $validated['email'],

            'nip' =>
            $validated['nip'],

            'nik_ktp' =>
            $validated['nik_ktp'],

            'whatsapp' =>
            $validated['whatsapp'],

            'join_date' =>
            $validated['join_date'],

            'department_id' =>
            (int) $validated['department_id'],

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
            $this->nullableString(
                $validated['sip_number']
                    ?? null
            ),

            'sip_valid_from' =>
            $this->nullableString(
                $validated['sip_valid_from']
                    ?? null
            ),

            'sip_valid_until' =>
            $this->nullableString(
                $validated['sip_valid_until']
                    ?? null
            ),

            /*
             * Bank selalu BSI.
             * Hanya nomor + nama rekening yang bisa diajukan.
             */
            'bank_account_number' =>
            $validated['bank_account_number'],

            'bank_account_name' =>
            $validated['bank_account_name'],
        ];

        $current = [
            'name' =>
            $user->name,

            'email' =>
            $user->email,

            'nip' =>
            $user->nip
                ?? $user->nik,

            'nik_ktp' =>
            $user->nik_ktp,

            'whatsapp' =>
            $user->whatsapp,

            'join_date' =>
            optional(
                $user->join_date
            )->format('Y-m-d'),

            'department_id' =>
            $user->department_id
                ? (int) $user->department_id
                : null,

            'birth_place' =>
            $user->birth_place,

            'birth_date' =>
            optional(
                $user->birth_date
            )->format('Y-m-d'),

            'ktp_address' =>
            $user->ktp_address,

            'domicile_address' =>
            $user->domicile_address,

            'blood_type' =>
            $user->blood_type,

            'religion' =>
            $user->religion,

            'sip_number' =>
            $this->nullableString(
                $user->sip_number
            ),

            'sip_valid_from' =>
            optional(
                $user->sip_valid_from
            )->format('Y-m-d'),

            'sip_valid_until' =>
            optional(
                $user->sip_valid_until
            )->format('Y-m-d'),

            'bank_account_number' =>
            $user->bank_account_number,

            'bank_account_name' =>
            $user->bank_account_name,
        ];

        $oldData = [];
        $newData = [];

        foreach (
            $proposed
            as $field => $newValue
        ) {
            $oldValue =
                $current[$field]
                ?? null;

            /*
             * Normalisasi supaya null dan string kosong
             * tidak dianggap sebagai perubahan berbeda.
             */
            $oldComparable =
                $this->normalizeComparable(
                    $oldValue
                );

            $newComparable =
                $this->normalizeComparable(
                    $newValue
                );

            if (
                $oldComparable
                === $newComparable
            ) {
                continue;
            }

            $oldData[$field] =
                $oldValue;

            $newData[$field] =
                $newValue;
        }

        $newPhotoPath = null;

        if (
            $request->hasFile(
                'formal_photo'
            )
        ) {
            $newPhotoPath =
                $request
                ->file('formal_photo')
                ->store(
                    'employee-profile-update-requests/'
                        . $user->id,
                    'local'
                );
        }

        if (
            empty($newData)
            && ! $newPhotoPath
        ) {
            return redirect()
                ->route('profile.edit')
                ->with(
                    'info',
                    'Tidak ada perubahan data yang perlu diajukan.'
                );
        }

        try {
            DB::transaction(
                function () use (
                    $user,
                    $oldData,
                    $newData,
                    $newPhotoPath
                ) {
                    /*
                     * Cek ulang dengan lock agar dua tab browser
                     * tidak dapat membuat dua request pending sekaligus.
                     */
                    $hasPending =
                        EmployeeProfileUpdateRequest::query()
                        ->where(
                            'user_id',
                            $user->id
                        )
                        ->where(
                            'status',
                            EmployeeProfileUpdateRequest::STATUS_PENDING
                        )
                        ->lockForUpdate()
                        ->exists();

                    if ($hasPending) {
                        throw new \RuntimeException(
                            'PENDING_PROFILE_UPDATE_EXISTS'
                        );
                    }

                    EmployeeProfileUpdateRequest::create([
                        'user_id' =>
                        $user->id,

                        'old_data' =>
                        $oldData,

                        'new_data' =>
                        $newData,

                        'new_photo_path' =>
                        $newPhotoPath,

                        'status' =>
                        EmployeeProfileUpdateRequest::STATUS_PENDING,

                        'rejection_reason' =>
                        null,

                        'reviewed_by' =>
                        null,

                        'reviewed_at' =>
                        null,
                    ]);
                }
            );
        } catch (\Throwable $exception) {
            if ($newPhotoPath) {
                Storage::disk('local')
                    ->delete(
                        $newPhotoPath
                    );
            }

            if (
                $exception->getMessage()
                === 'PENDING_PROFILE_UPDATE_EXISTS'
            ) {
                return redirect()
                    ->route('profile.edit')
                    ->with(
                        'error',
                        'Masih ada perubahan profil yang menunggu persetujuan Admin.'
                    );
            }

            throw $exception;
        }

        return redirect()
            ->route('profile.edit')
            ->with(
                'success',
                'Perubahan profil berhasil diajukan. '
                    . 'Data aktif belum berubah sampai disetujui Administrator.'
            );
    }

    /**
     * Tampilkan pas foto AKTIF milik user login.
     */
    public function photo(
        Request $request
    ): BinaryFileResponse {

        /*
         * Route ini berada di dalam middleware auth.
         *
         * Foto yang ditampilkan selalu foto milik user yang sedang login,
         * jadi aman dipakai bersama oleh header/sidebar:
         * - Admin
         * - Kabid
         * - Karyawan
         */
        $user =
            $request->user();

        abort_if(
            blank(
                $user->formal_photo_path
            ),
            404
        );

        /** @var FilesystemAdapter $disk */
        $disk =
            Storage::disk('local');

        abort_unless(
            $disk->exists(
                $user->formal_photo_path
            ),
            404
        );

        return response()->file(
            $disk->path(
                $user->formal_photo_path
            ),
            [
                'Content-Type' =>
                $disk->mimeType(
                    $user->formal_photo_path
                )
                    ?: 'application/octet-stream',

                'Cache-Control' =>
                'private, max-age=300',
            ]
        );
    }

    /**
     * Preview foto BARU pada pengajuan milik user login.
     */
    public function pendingPhoto(
        Request $request,
        EmployeeProfileUpdateRequest $profileUpdateRequest
    ): BinaryFileResponse {

        $user =
            $request->user();

        $this->ensureEmployeeCanUseProfile(
            $user
        );

        abort_unless(
            (int) $profileUpdateRequest->user_id
                === (int) $user->id,
            403
        );

        abort_if(
            blank(
                $profileUpdateRequest->new_photo_path
            ),
            404
        );

        /** @var FilesystemAdapter $disk */
        $disk =
            Storage::disk('local');

        abort_unless(
            $disk->exists(
                $profileUpdateRequest->new_photo_path
            ),
            404
        );

        return response()->file(
            $disk->path(
                $profileUpdateRequest->new_photo_path
            ),
            [
                'Content-Type' =>
                $disk->mimeType(
                    $profileUpdateRequest->new_photo_path
                )
                    ?: 'application/octet-stream',

                'Cache-Control' =>
                'private, max-age=300',
            ]
        );
    }

    /**
     * Dipertahankan untuk kompatibilitas route lama.
     *
     * Self-delete dinonaktifkan karena SIMI-MS memiliki data relasional
     * (cuti, reimburse, surat dinas, dll.) dan akun sebaiknya dikelola Admin.
     */
    public function destroy(
        Request $request
    ): RedirectResponse {

        return redirect()
            ->route('profile.edit')
            ->with(
                'error',
                'Penghapusan akun sendiri dinonaktifkan. '
                    . 'Silakan hubungi Administrator jika akun perlu dinonaktifkan.'
            );
    }

    private function ensureEmployeeCanUseProfile(
        User $user
    ): void {

        abort_unless(
            in_array(
                $user->role,
                ['karyawan', 'kabid'],
                true
            ),
            403
        );

        abort_unless(
            $user->approval_status
                === 'approved'
                && $user->is_active,
            403
        );
    }

    private function nullableString(
        mixed $value
    ): ?string {

        if (
            $value === null
            || trim(
                (string) $value
            ) === ''
        ) {
            return null;
        }

        return trim(
            (string) $value
        );
    }

    private function normalizeComparable(
        mixed $value
    ): mixed {

        if ($value === '') {
            return null;
        }

        if (
            is_string($value)
        ) {
            return trim($value);
        }

        return $value;
    }
}
