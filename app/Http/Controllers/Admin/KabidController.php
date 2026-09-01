<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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
                    $query->where(
                        function ($query) use ($search) {
                            $query
                                ->where(
                                    'name',
                                    'like',
                                    '%' . $search . '%'
                                )
                                ->orWhere(
                                    'nik',
                                    'like',
                                    '%' . $search . '%'
                                )
                                ->orWhere(
                                    'email',
                                    'like',
                                    '%' . $search . '%'
                                )
                                ->orWhere(
                                    'whatsapp',
                                    'like',
                                    '%' . $search . '%'
                                )
                                ->orWhereHas(
                                    'department',
                                    function ($query) use ($search) {
                                        $query->where(
                                            'name',
                                            'like',
                                            '%' . $search . '%'
                                        );
                                    }
                                );
                        }
                    );
                }
            )

            ->when(
                $approvalStatus,
                fn($query, $approvalStatus) =>
                $query->where(
                    'approval_status',
                    $approvalStatus
                )
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

    public function store(
        Request $request
    ): RedirectResponse {

        $validated = $request->validate(
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
                    'unique:users,nik',
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
                'name.required' =>
                'Nama Kabid wajib diisi.',

                'nik.required' =>
                'NIK wajib diisi.',

                'nik.unique' =>
                'NIK sudah digunakan.',

                'email.required' =>
                'Email wajib diisi.',

                'email.email' =>
                'Format email tidak valid.',

                'email.unique' =>
                'Email sudah digunakan.',

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

                'password.required' =>
                'Password wajib diisi.',

                'password.min' =>
                'Password minimal 8 karakter.',

                'password.confirmed' =>
                'Konfirmasi password tidak sama.',

                'is_active.required' =>
                'Status Kabid wajib dipilih.',
            ]
        );

        DB::transaction(
            function () use ($validated) {

                /*
                 * Lock bidang yang dipilih agar dua proses tidak
                 * dapat membuat dua Kabid untuk bidang yang sama
                 * pada waktu yang bersamaan.
                 */
                Department::query()
                    ->whereKey(
                        $validated['department_id']
                    )
                    ->lockForUpdate()
                    ->firstOrFail();

                $departmentAlreadyHasKabid =
                    User::query()
                    ->where('role', 'kabid')
                    ->where('approval_status', 'approved')
                    ->where(
                        'department_id',
                        $validated['department_id']
                    )
                    ->exists();

                if ($departmentAlreadyHasKabid) {
                    throw ValidationException::withMessages([
                        'department_id' =>
                        'Bidang yang dipilih sudah memiliki Kabid. '
                            . 'Satu bidang hanya boleh memiliki satu Kabid.',
                    ]);
                }

                User::create([
                    'name' =>
                    $validated['name'],

                    'nik' =>
                    $validated['nik'],

                    'email' =>
                    $validated['email'],

                    'whatsapp' =>
                    $validated['whatsapp'],

                    'join_date' =>
                    $validated['join_date'],

                    'department_id' =>
                    $validated['department_id'],

                    'password' =>
                    Hash::make(
                        $validated['password']
                    ),

                    'role' =>
                    'kabid',

                    'is_active' =>
                    (bool) $validated['is_active'],

                    /*
                     * Kabid yang dibuat langsung oleh Admin
                     * tidak perlu melalui proses verifikasi ulang.
                     */
                    'approval_status' =>
                    'approved',

                    'approval_rejection_reason' =>
                    null,

                    'profile_completed_at' =>
                    now(),
                ]);
            }
        );

        return redirect()
            ->route('admin.kabid.index')
            ->with(
                'success',
                'Data Kabid berhasil ditambahkan.'
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

        $this->ensureKabid(
            $kabid
        );

        $validated = $request->validate(
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
                        $kabid->id
                    ),
                ],

                'email' => [
                    'required',
                    'email',
                    'max:255',

                    Rule::unique(
                        'users',
                        'email'
                    )->ignore(
                        $kabid->id
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
                    'exists:departments,id',
                ],

                'password' => [
                    'nullable',
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
                'name.required' =>
                'Nama Kabid wajib diisi.',

                'nik.required' =>
                'NIK wajib diisi.',

                'nik.unique' =>
                'NIK sudah digunakan.',

                'email.required' =>
                'Email wajib diisi.',

                'email.email' =>
                'Format email tidak valid.',

                'email.unique' =>
                'Email sudah digunakan.',

                'whatsapp.required' =>
                'Nomor WhatsApp wajib diisi.',

                'join_date.required' =>
                'Tanggal mulai kerja wajib diisi.',

                'join_date.before_or_equal' =>
                'Tanggal mulai kerja tidak boleh melewati hari ini.',

                'department_id.required' =>
                'Bidang wajib dipilih.',

                'password.min' =>
                'Password minimal 8 karakter.',

                'password.confirmed' =>
                'Konfirmasi password tidak sama.',
            ]
        );

        $data = [
            'name' =>
            $validated['name'],

            'nik' =>
            $validated['nik'],

            'email' =>
            $validated['email'],

            'whatsapp' =>
            $validated['whatsapp'],

            'join_date' =>
            $validated['join_date'],

            'department_id' =>
            $validated['department_id'],

            'is_active' =>
            (bool) $validated['is_active'],

            /*
             * Role tidak diambil dari request.
             * Halaman Kelola Kabid selalu mempertahankan role Kabid.
             */
            'role' =>
            'kabid',
        ];

        if (
            ! empty($validated['password'])
        ) {
            $data['password'] =
                Hash::make(
                    $validated['password']
                );
        }

        DB::transaction(
            function () use (
                $validated,
                $data,
                $kabid
            ) {

                Department::query()
                    ->whereKey(
                        $validated['department_id']
                    )
                    ->lockForUpdate()
                    ->firstOrFail();

                $departmentAlreadyHasKabid =
                    User::query()
                    ->where('role', 'kabid')
                    ->where('approval_status', 'approved')
                    ->where(
                        'department_id',
                        $validated['department_id']
                    )
                    ->whereKeyNot(
                        $kabid->id
                    )
                    ->exists();

                if ($departmentAlreadyHasKabid) {
                    throw ValidationException::withMessages([
                        'department_id' =>
                        'Bidang yang dipilih sudah memiliki Kabid lain. '
                            . 'Satu bidang hanya boleh memiliki satu Kabid.',
                    ]);
                }

                $kabid->update(
                    $data
                );
            }
        );

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

    public function destroy(
        User $kabid
    ): RedirectResponse {

        $this->ensureKabid(
            $kabid
        );

        $leaveRequestCount =
            $kabid->leaveRequests()->count();

        $reimbursementCount =
            $kabid->reimbursements()->count();

        if (
            $leaveRequestCount > 0
            || $reimbursementCount > 0
        ) {
            $relatedData = [];

            if ($leaveRequestCount > 0) {
                $relatedData[] =
                    $leaveRequestCount
                    . ' riwayat perizinan/cuti';
            }

            if ($reimbursementCount > 0) {
                $relatedData[] =
                    $reimbursementCount
                    . ' riwayat reimbursement';
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

    private function ensureKabid(
        User $user
    ): void {

        abort_unless(
            $user->role === 'kabid',
            404
        );
    }
}
