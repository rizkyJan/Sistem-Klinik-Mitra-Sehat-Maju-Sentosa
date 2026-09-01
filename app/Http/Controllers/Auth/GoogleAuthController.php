<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class GoogleAuthController extends Controller
{
    /**
     * Arahkan user ke halaman login Google.
     */
    public function redirect()
    {
        return Socialite::driver('google')
            /*
             * Paksa Google menampilkan pemilih akun setiap kali
             * tombol Login dengan Google ditekan.
             *
             * Ini tidak memaksa consent ulang. Pengguna hanya diminta
             * memilih akun Google yang ingin digunakan.
             */
            ->with([
                'prompt' => 'select_account',
            ])
            ->redirect();
    }


    /**
     * Callback dari Google.
     */
    public function callback(
        Request $request
    ): RedirectResponse {

        try {

            $googleUser =
                Socialite::driver('google')
                ->user();

            /*
             * Cari berdasarkan google_id terlebih dahulu.
             * Jika belum pernah terhubung, cari berdasarkan email.
             */
            $user = User::query()
                ->where(
                    'google_id',
                    $googleUser->getId()
                )
                ->orWhere(
                    'email',
                    $googleUser->getEmail()
                )
                ->first();


            /*
            |--------------------------------------------------------------------------
            | USER BARU DARI GOOGLE
            |--------------------------------------------------------------------------
            |
            | Role sementara dibuat "karyawan" karena kolom role tidak nullable.
            | Role final Karyawan / Kabid baru dipilih di halaman
            | "Lengkapi Data Pegawai".
            |
            */
            if (! $user) {

                $user = User::create([
                    'name' =>
                    $googleUser->getName()
                        ?: $googleUser->getNickname()
                        ?: 'Pengguna Google',

                    'email' =>
                    $googleUser->getEmail(),

                    'google_id' =>
                    $googleUser->getId(),

                    'google_avatar' =>
                    $googleUser->getAvatar(),

                    'password' =>
                    Hash::make(
                        Str::random(40)
                    ),

                    /*
                     * Role sementara.
                     * Akan diganti sesuai pilihan user saat onboarding.
                     */
                    'role' =>
                    'karyawan',

                    /*
                     * Semua pendaftar Google wajib diverifikasi Admin.
                     */
                    'approval_status' =>
                    'pending',

                    'approval_rejection_reason' =>
                    null,

                    'is_active' =>
                    false,

                    'profile_completed_at' =>
                    null,
                ]);
            } else {

                /*
                 * Hubungkan akun lama dengan Google jika email sama,
                 * sekaligus perbarui avatar.
                 */
                $user->forceFill([
                    'google_id' =>
                    $user->google_id
                        ?: $googleUser->getId(),

                    'google_avatar' =>
                    $googleUser->getAvatar()
                        ?: $user->google_avatar,
                ])->save();
            }


            Auth::login(
                $user,
                true
            );

            $request->session()
                ->regenerate();


            /*
            |--------------------------------------------------------------------------
            | ADMIN
            |--------------------------------------------------------------------------
            */
            if ($user->role === 'admin') {

                if (! $user->is_active) {

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
            | PROFIL PEGAWAI BELUM LENGKAP
            |--------------------------------------------------------------------------
            |
            | Berlaku untuk pendaftar Google Karyawan maupun Kabid.
            |
            */
            if (! $user->profile_completed_at) {

                return redirect()
                    ->route(
                        'employee.profile.complete'
                    );
            }


            /*
            |--------------------------------------------------------------------------
            | BELUM DISETUJUI ADMIN
            |--------------------------------------------------------------------------
            |
            | Kabid pending tidak dianggap sebagai Admin/nonaktif.
            | Ia tetap diarahkan ke halaman menunggu verifikasi.
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
            if (! $user->is_active) {

                return $this->logoutWithError(
                    $request,
                    'Akun Anda sedang nonaktif. Silakan hubungi Administrator.'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | REDIRECT BERDASARKAN JABATAN
            |--------------------------------------------------------------------------
            */
            if ($user->role === 'kabid') {

                return redirect()
                    ->route(
                        'kabid.dashboard'
                    );
            }


            if ($user->role === 'karyawan') {

                return redirect()
                    ->route(
                        'karyawan.dashboard'
                    );
            }


            /*
             * Pengaman jika role di database tidak dikenali.
             */
            return $this->logoutWithError(
                $request,
                'Role akun tidak dikenali. Silakan hubungi Administrator.'
            );
        } catch (Throwable $exception) {

            report(
                $exception
            );

            return redirect()
                ->route('login')
                ->withErrors([
                    'email' =>
                    'Login Google gagal. Silakan coba kembali.',
                ]);
        }
    }


    /**
     * Logout user lalu kembali ke halaman login dengan pesan error.
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
