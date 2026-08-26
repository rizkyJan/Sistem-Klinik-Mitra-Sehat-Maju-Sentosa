<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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

                /*
                |--------------------------------------------------------------------------
                | Tanggal mulai kerja
                |--------------------------------------------------------------------------
                */

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
                'Nama karyawan wajib diisi.',

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
                'Status karyawan wajib dipilih.',
            ]
        );


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
            'karyawan',

            'is_active' =>
            (bool) $validated['is_active'],

            /*
             * Untuk karyawan yang dibuat langsung
             * oleh Admin, kita anggap sudah disetujui.
             */
        ]);


        return redirect()
            ->route('admin.karyawan.index')
            ->with(
                'success',
                'Data karyawan berhasil ditambahkan.'
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

        $this->ensureKaryawan(
            $karyawan
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
                        $karyawan->id
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
                        $karyawan->id
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

                /*
                 * Password kosong berarti tidak diganti.
                 */
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

                'role' => [
                    'required',
                    Rule::in([
                        'karyawan',
                        'admin',
                    ]),
                ],
            ],
            [
                'name.required' =>
                'Nama karyawan wajib diisi.',

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

                'role.required' =>
                'Jenis akun wajib dipilih.',

                'role.in' =>
                'Jenis akun hanya boleh Karyawan atau Admin.',
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

            'role' =>
            $validated['role'],
        ];


        /*
        |--------------------------------------------------------------------------
        | Password hanya diubah kalau diisi
        |--------------------------------------------------------------------------
        */

        if (
            ! empty($validated['password'])
        ) {

            $data['password'] =
                Hash::make(
                    $validated['password']
                );
        }


        $karyawan->update(
            $data
        );


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

    public function destroy(
        User $karyawan
    ): RedirectResponse {

        $this->ensureKaryawan(
            $karyawan
        );


        /*
        |--------------------------------------------------------------------------
        | Jangan hard delete jika sudah punya riwayat penting
        |--------------------------------------------------------------------------
        |
        | Data perizinan dan reimbursement adalah data histori.
        | Kalau user dihapus paksa, relasi foreign key dapat bentrok
        | atau histori operasional/keuangan ikut hilang.
        |
        | Solusi yang lebih aman:
        | - Karyawan tanpa histori -> boleh dihapus.
        | - Karyawan dengan histori -> jangan dihapus, nonaktifkan saja.
        |
        */

        $leaveRequestCount =
            $karyawan->leaveRequests()->count();

        $reimbursementCount =
            $karyawan->reimbursements()->count();


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


        /*
        |--------------------------------------------------------------------------
        | Fallback database protection
        |--------------------------------------------------------------------------
        |
        | Walaupun pengecekan di atas sudah dilakukan, foreign key lain
        | mungkin ditambahkan di masa depan. Tangkap QueryException agar
        | user tidak mendapat halaman Internal Server Error.
        |
        */

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
