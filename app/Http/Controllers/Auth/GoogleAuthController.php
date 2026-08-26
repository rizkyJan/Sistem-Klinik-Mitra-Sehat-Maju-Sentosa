<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirectResponse;
use Throwable;
use Laravel\Socialite\Two\GoogleProvider;

class GoogleAuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Redirect ke Google
    |--------------------------------------------------------------------------
    */
    public function redirect(): SymfonyRedirectResponse
    {
        /** @var GoogleProvider $provider */
        $provider = Socialite::driver('google');

        return $provider
            ->with([
                'prompt' => 'select_account',
            ])
            ->redirect();
    }


    /*
    |--------------------------------------------------------------------------
    | Callback Google
    |--------------------------------------------------------------------------
    */
    public function callback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (Throwable $e) {
            report($e);

            return redirect()
                ->route('login')
                ->withErrors([
                    'google' =>
                    'Login Google gagal. Silakan coba lagi.',
                ]);
        }


        $email = strtolower(
            (string) $googleUser->getEmail()
        );


        if ($email === '') {
            return redirect()
                ->route('login')
                ->withErrors([
                    'google' =>
                    'Akun Google tidak memberikan alamat email.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Cari akun berdasarkan Google ID
        |--------------------------------------------------------------------------
        */
        $user = User::query()
            ->where(
                'google_id',
                $googleUser->getId()
            )
            ->first();


        /*
        |--------------------------------------------------------------------------
        | Jika belum terhubung Google, cari berdasarkan email
        |--------------------------------------------------------------------------
        |
        | Contoh:
        | admin@mitrasehat.com sebelumnya dibuat dengan email/password.
        | Saat login Google menggunakan email yang sama,
        | akun tersebut akan dihubungkan ke Google.
        |--------------------------------------------------------------------------
        */
        if (! $user) {
            $user = User::query()
                ->whereRaw(
                    'LOWER(email) = ?',
                    [$email]
                )
                ->first();
        }


        /*
        |--------------------------------------------------------------------------
        | Cegah email terhubung ke Google ID berbeda
        |--------------------------------------------------------------------------
        */
        if (
            $user
            &&
            $user->google_id
            &&
            $user->google_id !== $googleUser->getId()
        ) {
            return redirect()
                ->route('login')
                ->withErrors([
                    'google' =>
                    'Email ini sudah terhubung dengan akun Google lain. Hubungi admin.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | AKUN BARU DARI GOOGLE
        |--------------------------------------------------------------------------
        |
        | User baru SELALU dibuat sebagai Karyawan.
        | Login Google tidak boleh otomatis membuat seseorang menjadi Admin.
        |--------------------------------------------------------------------------
        */
        if (! $user) {

            $user = User::create([
                'name' =>
                $googleUser->getName() ?: $email,

                'email' =>
                $email,

                /*
                 * Password acak karena akun baru masuk lewat Google.
                 */
                'password' =>
                Hash::make(
                    Str::random(64)
                ),

                'role' =>
                'karyawan',

                /*
                 * Karyawan baru harus mengisi data dan menunggu ACC.
                 */
                'is_active' =>
                false,

                'google_id' =>
                $googleUser->getId(),

                'google_avatar' =>
                $googleUser->getAvatar(),

                'approval_status' =>
                'pending',

                'email_verified_at' =>
                now(),
            ]);
        } else {

            /*
            |--------------------------------------------------------------------------
            | AKUN SUDAH ADA
            |--------------------------------------------------------------------------
            |
            | Bisa Admin, Kabid, atau Karyawan.
            | Hubungkan akun tersebut dengan Google.
            |--------------------------------------------------------------------------
            */
            $user->forceFill([
                'google_id' =>
                $googleUser->getId(),

                'google_avatar' =>
                $googleUser->getAvatar(),

                'email_verified_at' =>
                $user->email_verified_at ?: now(),
            ])->save();
        }


        /*
        |--------------------------------------------------------------------------
        | ADMIN / KABID NONAKTIF
        |--------------------------------------------------------------------------
        |
        | Karyawan pending memang is_active=false dan tetap harus dapat masuk
        | untuk melihat halaman menunggu verifikasi.
        | Karena itu pengecekan nonaktif ini hanya untuk Admin/Kabid.
        |--------------------------------------------------------------------------
        */
        if (
            in_array(
                $user->role,
                ['admin', 'kabid'],
                true
            )
            &&
            ! $user->is_active
        ) {
            return redirect()
                ->route('login')
                ->withErrors([
                    'google' =>
                    'Akun Anda sedang nonaktif. Hubungi administrator.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Login
        |--------------------------------------------------------------------------
        */
        Auth::login(
            $user,
            true
        );

        request()
            ->session()
            ->regenerate();


        /*
        |--------------------------------------------------------------------------
        | ADMIN / KABID
        |--------------------------------------------------------------------------
        */
        if (
            in_array(
                $user->role,
                ['admin', 'kabid'],
                true
            )
        ) {
            return redirect()
                ->route('admin.dashboard');
        }


        /*
        |--------------------------------------------------------------------------
        | KARYAWAN - BELUM LENGKAP DATA
        |--------------------------------------------------------------------------
        */
        if (! $user->profile_completed_at) {
            return redirect()
                ->route(
                    'employee.profile.complete'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | KARYAWAN - BELUM DISETUJUI
        |--------------------------------------------------------------------------
        */
        if (
            $user->approval_status !== 'approved'
            ||
            ! $user->is_active
        ) {
            return redirect()
                ->route(
                    'employee.approval.waiting'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | KARYAWAN - SUDAH AKTIF
        |--------------------------------------------------------------------------
        */
        return redirect()
            ->route('karyawan.dashboard');
    }
}
