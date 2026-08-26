<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Index
    |--------------------------------------------------------------------------
    */
    public function index(Request $request): View
    {
        $search = $request->input('search');

        $admins = User::query()
            ->where('role', 'admin')
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
                                    'email',
                                    'like',
                                    '%' . $search . '%'
                                );
                        }
                    );
                }
            )
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view(
            'admin.admins.index',
            compact('admins')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Create
    |--------------------------------------------------------------------------
    */
    public function create(): View
    {
        return view('admin.admins.create');
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

                'email' => [
                    'required',
                    'email',
                    'max:255',
                    'unique:users,email',
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
                'Nama admin wajib diisi.',

                'email.required' =>
                'Email admin wajib diisi.',

                'email.email' =>
                'Format email tidak valid.',

                'email.unique' =>
                'Email sudah digunakan.',

                'password.required' =>
                'Password wajib diisi.',

                'password.min' =>
                'Password minimal 8 karakter.',

                'password.confirmed' =>
                'Konfirmasi password tidak sama.',

                'is_active.required' =>
                'Status akun wajib dipilih.',
            ]
        );

        User::create([
            'name' =>
            $validated['name'],

            'email' =>
            strtolower(
                $validated['email']
            ),

            'password' =>
            Hash::make(
                $validated['password']
            ),

            'role' =>
            'admin',

            'is_active' =>
            (bool) $validated['is_active'],

            'email_verified_at' =>
            now(),

            'approval_status' =>
            'approved',
        ]);

        return redirect()
            ->route(
                'admin.admins.index'
            )
            ->with(
                'success',
                'Admin berhasil ditambahkan.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Edit
    |--------------------------------------------------------------------------
    */
    public function edit(
        User $adminUser
    ): View {
        $this->ensureAdmin(
            $adminUser
        );

        return view(
            'admin.admins.edit',
            compact(
                'adminUser'
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
        User $adminUser
    ): RedirectResponse {
        $this->ensureAdmin(
            $adminUser
        );

        $validated = $request->validate(
            [
                'name' => [
                    'required',
                    'string',
                    'max:255',
                ],

                'email' => [
                    'required',
                    'email',
                    'max:255',

                    Rule::unique(
                        'users',
                        'email'
                    )->ignore(
                        $adminUser->id
                    ),
                ],

                'role' => [
                    'required',

                    Rule::in([
                        'admin',
                        'karyawan',
                    ]),
                ],

                'is_active' => [
                    'required',
                    'boolean',
                ],

                'password' => [
                    'nullable',
                    'string',
                    'min:8',
                    'confirmed',
                ],
            ],
            [
                'name.required' =>
                'Nama wajib diisi.',

                'email.required' =>
                'Email wajib diisi.',

                'email.email' =>
                'Format email tidak valid.',

                'email.unique' =>
                'Email sudah digunakan.',

                'role.required' =>
                'Jenis akun wajib dipilih.',

                'role.in' =>
                'Jenis akun tidak valid.',

                'is_active.required' =>
                'Status akun wajib dipilih.',

                'password.min' =>
                'Password minimal 8 karakter.',

                'password.confirmed' =>
                'Konfirmasi password tidak sama.',
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | Lindungi akun admin yang sedang digunakan
        |--------------------------------------------------------------------------
        */
        if (
            Auth::id() === $adminUser->id
            &&
            $validated['role'] !== 'admin'
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'role' =>
                    'Akun admin yang sedang digunakan tidak dapat diubah menjadi karyawan.',
                ]);
        }


        if (
            Auth::id() === $adminUser->id
            &&
            ! (bool) $validated['is_active']
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'is_active' =>
                    'Akun admin yang sedang digunakan tidak dapat dinonaktifkan.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Data Update
        |--------------------------------------------------------------------------
        */
        $data = [
            'name' =>
            $validated['name'],

            'email' =>
            strtolower(
                $validated['email']
            ),

            'role' =>
            $validated['role'],

            'is_active' =>
            (bool) $validated['is_active'],
        ];


        /*
        |--------------------------------------------------------------------------
        | Password
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


        /*
        |--------------------------------------------------------------------------
        | Jika tetap / menjadi Admin
        |--------------------------------------------------------------------------
        */
        if (
            $validated['role']
            === 'admin'
        ) {
            $data['approval_status'] =
                'approved';

            $data['approval_rejection_reason'] =
                null;

            $data['is_active'] =
                (bool) $validated['is_active'];
        }


        /*
        |--------------------------------------------------------------------------
        | Jika Admin diubah menjadi Karyawan
        |--------------------------------------------------------------------------
        */
        if (
            $validated['role']
            === 'karyawan'
        ) {
            $data['approval_status'] =
                $adminUser
                ->profile_completed_at
                ? 'approved'
                : 'pending';

            if (
                ! $adminUser
                    ->profile_completed_at
            ) {
                $data['is_active'] =
                    false;
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Simpan
        |--------------------------------------------------------------------------
        */
        $adminUser->update(
            $data
        );


        /*
        |--------------------------------------------------------------------------
        | Redirect jika menjadi Karyawan
        |--------------------------------------------------------------------------
        */
        if (
            $validated['role']
            === 'karyawan'
        ) {
            return redirect()
                ->route(
                    'admin.karyawan.index'
                )
                ->with(
                    'success',
                    'Akun ' .
                        $adminUser->name .
                        ' berhasil diubah menjadi karyawan.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Redirect Admin
        |--------------------------------------------------------------------------
        */
        return redirect()
            ->route(
                'admin.admins.index'
            )
            ->with(
                'success',
                'Data admin berhasil diperbarui.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Destroy
    |--------------------------------------------------------------------------
    */
    public function destroy(
        User $adminUser
    ): RedirectResponse {
        $this->ensureAdmin(
            $adminUser
        );


        /*
        |--------------------------------------------------------------------------
        | Tidak boleh menghapus akun sendiri
        |--------------------------------------------------------------------------
        */
        if (
            Auth::id()
            === $adminUser->id
        ) {
            return redirect()
                ->route(
                    'admin.admins.index'
                )
                ->withErrors([
                    'delete' =>
                    'Akun admin yang sedang digunakan tidak dapat dihapus.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Minimal satu admin
        |--------------------------------------------------------------------------
        */
        $totalAdmin = User::query()
            ->where(
                'role',
                'admin'
            )
            ->count();

        if (
            $totalAdmin <= 1
        ) {
            return redirect()
                ->route(
                    'admin.admins.index'
                )
                ->withErrors([
                    'delete' =>
                    'Minimal harus ada satu akun admin di sistem.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Hapus
        |--------------------------------------------------------------------------
        */
        $adminUser->delete();


        return redirect()
            ->route(
                'admin.admins.index'
            )
            ->with(
                'success',
                'Admin berhasil dihapus.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Security
    |--------------------------------------------------------------------------
    */
    private function ensureAdmin(
        User $user
    ): void {
        abort_unless(
            $user->role === 'admin',
            404
        );
    }
}
