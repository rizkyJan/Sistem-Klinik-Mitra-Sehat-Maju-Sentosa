<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Menampilkan halaman login.
     */
    public function create(): View
    {
        return view(
            'auth.login'
        );
    }

    /**
     * Memproses login user.
     */
    public function store(
        LoginRequest $request
    ): RedirectResponse {

        $request->authenticate();

        $request->session()
            ->regenerate();

        $user =
            $request->user();

        /*
        |--------------------------------------------------------------------------
        | ADMIN
        |--------------------------------------------------------------------------
        */
        if (
            $user->role === 'admin'
        ) {
            if (
                ! $user->is_active
            ) {
                return $this->logoutWithError(
                    $request,
                    'Akun Admin sedang nonaktif.'
                );
            }

            return redirect()
                ->route(
                    'admin.dashboard'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | PROFIL BELUM LENGKAP
        |--------------------------------------------------------------------------
        |
        | Umumnya terjadi pada akun baru dari Google.
        |
        */
        if (
            ! $user->profile_completed_at
        ) {
            return redirect()
                ->route(
                    'employee.profile.complete'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | MENUNGGU / DITOLAK ADMIN
        |--------------------------------------------------------------------------
        |
        | Pendaftar manual maupun Google tidak boleh langsung masuk
        | dashboard sebelum approval_status = approved.
        |
        */
        if (
            $user->approval_status !== 'approved'
        ) {
            return redirect()
                ->route(
                    'employee.approval.waiting'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | SUDAH APPROVED TAPI DINONAKTIFKAN
        |--------------------------------------------------------------------------
        */
        if (
            ! $user->is_active
        ) {
            return $this->logoutWithError(
                $request,
                'Akun Anda sedang nonaktif. Silakan hubungi Administrator.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | REDIRECT BERDASARKAN ROLE
        |--------------------------------------------------------------------------
        */
        if (
            $user->role === 'kabid'
        ) {
            return redirect()
                ->route(
                    'kabid.dashboard'
                );
        }

        if (
            $user->role === 'karyawan'
        ) {
            return redirect()
                ->route(
                    'karyawan.dashboard'
                );
        }

        return $this->logoutWithError(
            $request,
            'Role akun tidak dikenali. Silakan hubungi Administrator.'
        );
    }

    /**
     * Logout user.
     */
    public function destroy(
        Request $request
    ): RedirectResponse {

        Auth::guard('web')
            ->logout();

        $request->session()
            ->invalidate();

        $request->session()
            ->regenerateToken();

        return redirect('/');
    }

    /**
     * Logout + pesan error.
     */
    private function logoutWithError(
        Request $request,
        string $message
    ): RedirectResponse {

        Auth::guard('web')
            ->logout();

        $request->session()
            ->invalidate();

        $request->session()
            ->regenerateToken();

        return redirect()
            ->route('login')
            ->withErrors([
                'email' =>
                $message,
            ]);
    }
}
