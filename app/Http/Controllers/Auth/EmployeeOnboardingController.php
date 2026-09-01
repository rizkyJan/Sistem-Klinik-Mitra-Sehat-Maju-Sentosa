<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class EmployeeOnboardingController extends Controller
{
    /**
     * Form melengkapi data pegawai setelah login Google.
     */
    public function edit(
        Request $request
    ): View|RedirectResponse {

        $user =
            $request->user();


        /*
         * Admin tidak menggunakan onboarding pegawai.
         */
        if ($user->role === 'admin') {

            return redirect()
                ->route(
                    'admin.dashboard'
                );
        }


        /*
         * Jika profil sudah pernah dilengkapi,
         * jangan izinkan user mengulang pemilihan jabatan dari sini.
         */
        if ($user->profile_completed_at) {

            return $this->redirectAfterOnboarding(
                $user
            );
        }


        $departments = Department::query()
            ->where(
                'is_active',
                true
            )
            ->orderBy('name')
            ->get();


        /*
         * Hanya Kabid yang SUDAH DISETUJUI Admin yang
         * dianggap resmi menempati suatu bidang.
         *
         * Kabid dengan status pending masih merupakan calon Kabid
         * sehingga belum mengunci bidang tersebut.
         */
        $kabidDepartmentIds =
            User::query()
            ->where(
                'role',
                'kabid'
            )
            ->whereNotNull(
                'department_id'
            )
            ->whereKeyNot(
                $user->id
            )
            ->where(
                'approval_status',
                'approved'
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
     * Simpan data onboarding Google.
     */
    public function update(
        Request $request
    ): RedirectResponse {

        $user =
            $request->user();


        if ($user->role === 'admin') {

            abort(403);
        }


        /*
         * Onboarding hanya boleh dilakukan sekali.
         */
        if ($user->profile_completed_at) {

            return $this->redirectAfterOnboarding(
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

                    'nik' => [
                        'required',
                        'string',
                        'max:50',

                        Rule::unique(
                            'users',
                            'nik'
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

                    /*
                     * Dari Google hanya boleh memilih:
                     * - karyawan
                     * - kabid
                     *
                     * Admin tidak pernah boleh dikirim dari form onboarding.
                     */
                    'role' => [
                        'required',

                        Rule::in([
                            'karyawan',
                            'kabid',
                        ]),
                    ],
                ],
                [
                    'name.required' =>
                    'Nama lengkap wajib diisi.',

                    'name.max' =>
                    'Nama lengkap maksimal 255 karakter.',

                    'nik.required' =>
                    'NIK / ID Pegawai wajib diisi.',

                    'nik.unique' =>
                    'NIK / ID Pegawai sudah digunakan.',

                    'whatsapp.required' =>
                    'Nomor WhatsApp wajib diisi.',

                    'join_date.required' =>
                    'Tanggal mulai kerja wajib diisi.',

                    'join_date.date' =>
                    'Tanggal mulai kerja tidak valid.',

                    'join_date.before_or_equal' =>
                    'Tanggal mulai kerja tidak boleh melewati hari ini.',

                    'department_id.required' =>
                    'Bidang wajib dipilih.',

                    'department_id.exists' =>
                    'Bidang yang dipilih tidak tersedia.',

                    'role.required' =>
                    'Jabatan wajib dipilih.',

                    'role.in' =>
                    'Jabatan yang dipilih tidak valid.',
                ]
            );


        DB::transaction(
            function () use (
                $user,
                $validated
            ) {

                /*
                 * Jika memilih Kabid, lock bidang tersebut
                 * dan cek sekali lagi di backend.
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
                            'Bidang yang dipilih sudah memiliki Kabid yang telah disetujui Admin. '
                                . 'Satu bidang hanya boleh memiliki satu Kabid resmi.',
                        ]);
                    }
                }


                $user->forceFill([
                    'name' =>
                    $validated['name'],

                    'nik' =>
                    $validated['nik'],

                    'whatsapp' =>
                    $validated['whatsapp'],

                    'join_date' =>
                    $validated['join_date'],

                    'department_id' =>
                    $validated['department_id'],

                    /*
                     * Jabatan final dipilih di sini.
                     */
                    'role' =>
                    $validated['role'],

                    /*
                     * Semua pendaftar Google tetap menunggu Admin.
                     */
                    'approval_status' =>
                    'pending',

                    'approval_rejection_reason' =>
                    null,

                    'is_active' =>
                    false,

                    'profile_completed_at' =>
                    now(),
                ])->save();
            }
        );


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


        if ($user->role === 'admin') {

            return redirect()
                ->route(
                    'admin.dashboard'
                );
        }


        if (! $user->profile_completed_at) {

            return redirect()
                ->route(
                    'employee.profile.complete'
                );
        }


        /*
         * Jika Admin sudah menyetujui akun,
         * jangan biarkan user tetap berada di halaman menunggu.
         */
        if (
            $user->approval_status === 'approved'
            && $user->is_active
        ) {

            return $this->redirectAfterOnboarding(
                $user
            );
        }


        return view(
            'auth.waiting-approval',
            compact('user')
        );
    }


    /**
     * Redirect berdasarkan role/status setelah onboarding.
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


        if ($user->role === 'kabid') {

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
