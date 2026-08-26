<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class EmployeeOnboardingController extends Controller
{
    public function edit(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        abort_unless($user->role === 'karyawan', 403);

        /*
         * Kalau sudah approved tidak perlu
         * masuk halaman lengkapi data lagi.
         */
        if (
            $user->profile_completed_at &&
            $user->approval_status === 'approved' &&
            $user->is_active
        ) {
            return redirect()
                ->route('karyawan.dashboard');
        }

        $departments = Department::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view(
            'auth.complete-employee-profile',
            compact('user', 'departments')
        );
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        abort_unless($user->role === 'karyawan', 403);

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255'
            ],

            'nik' => [
                'required',
                'string',
                'max:50',
                Rule::unique(User::class, 'nik')
                    ->ignore($user->id),
            ],

            'whatsapp' => [
                'required',
                'string',
                'max:20'
            ],

            'join_date' => [
                'required',
                'date',
                'before_or_equal:today'
            ],

            'department_id' => [
                'required',

                Rule::exists('departments', 'id')
                    ->where(
                        fn($query) =>
                        $query->where('is_active', true)
                    ),
            ],
        ], [
            'name.required' =>
            'Nama wajib diisi.',

            'nik.required' =>
            'NIK wajib diisi.',

            'nik.unique' =>
            'NIK sudah digunakan.',

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
        ]);

        $user->update([
            ...$validated,

            'profile_completed_at' => now(),

            /*
             * Setiap submit profil harus
             * diverifikasi admin.
             */
            'approval_status' => 'pending',

            'approval_rejection_reason' => null,

            'is_active' => false,
        ]);

        return redirect()
            ->route('employee.approval.waiting')
            ->with(
                'success',
                'Data berhasil dikirim. Silakan tunggu verifikasi admin.'
            );
    }

    public function waiting(
        Request $request
    ): View|RedirectResponse {

        $user = $request->user();

        abort_unless($user->role === 'karyawan', 403);

        /*
         * Belum isi profil.
         */
        if (! $user->profile_completed_at) {
            return redirect()
                ->route('employee.profile.complete');
        }

        /*
         * Sudah ACC.
         */
        if (
            $user->approval_status === 'approved' &&
            $user->is_active
        ) {
            return redirect()
                ->route('karyawan.dashboard');
        }

        return view(
            'auth.waiting-approval',
            compact('user')
        );
    }
}
