<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Daftar Pegawai - SIMI-MS</title>

    @vite([
    'resources/css/app.css',
    'resources/js/app.js'
    ])
</head>

<body class="min-h-screen bg-slate-50 text-slate-800 antialiased">
    <div class="mx-auto min-h-screen max-w-5xl px-4 py-8 sm:px-6 lg:py-12">
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-semibold text-blue-600">
                    SIMI-MS
                </p>

                <h1 class="mt-1 text-2xl font-bold text-slate-900 sm:text-3xl">
                    Pendaftaran Pegawai
                </h1>

                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">
                    Lengkapi seluruh data dari awal. Setelah dikirim,
                    akun Karyawan/Kabid wajib diverifikasi Administrator
                    sebelum dapat menggunakan aplikasi.
                </p>
            </div>

            <a
                href="{{ route('login') }}"
                class="text-sm font-medium text-blue-600 hover:text-blue-700">
                Sudah punya akun? Masuk
            </a>
        </div>

        @if($errors->any())
        <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-4">
            <p class="font-semibold text-red-700">
                Data belum dapat dikirim.
            </p>

            <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-red-600">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form
            method="POST"
            action="{{ route('register') }}"
            enctype="multipart/form-data"
            class="space-y-6">

            @csrf

            @include('auth.partials.employee-profile-fields')

            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 bg-slate-50/70 px-5 py-4 sm:px-6">
                    <h2 class="font-semibold text-slate-800">
                        Akun SIMI-MS
                    </h2>
                    <p class="mt-1 text-sm text-slate-500">
                        Email dan password digunakan untuk login manual.
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-5 p-5 sm:p-6 md:grid-cols-2">
                    <div class="md:col-span-2">
                        <label for="email" class="block text-sm font-semibold text-slate-700">
                            Email <span class="text-red-500">*</span>
                        </label>

                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autocomplete="username"
                            class="mt-2 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500">

                        @error('email')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-semibold text-slate-700">
                            Password <span class="text-red-500">*</span>
                        </label>

                        <input
                            id="password"
                            type="password"
                            name="password"
                            required
                            autocomplete="new-password"
                            class="mt-2 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500">

                        @error('password')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-semibold text-slate-700">
                            Konfirmasi Password <span class="text-red-500">*</span>
                        </label>

                        <input
                            id="password_confirmation"
                            type="password"
                            name="password_confirmation"
                            required
                            autocomplete="new-password"
                            class="mt-2 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>
            </section>

            <div class="rounded-2xl border border-blue-200 bg-blue-50 p-4 text-sm leading-6 text-blue-800">
                <strong>Setelah klik Daftar:</strong>
                data akan berstatus <strong>Menunggu Verifikasi Admin</strong>.
                Anda belum dapat masuk dashboard sampai Admin menyetujui pendaftaran.
            </div>

            <div class="flex flex-col-reverse gap-3 pb-8 sm:flex-row sm:items-center sm:justify-end">
                <a
                    href="{{ route('login') }}"
                    class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    Batal
                </a>

                <button
                    type="submit"
                    class="inline-flex items-center justify-center rounded-xl bg-blue-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-blue-700">
                    Daftar & Kirim ke Admin
                </button>
            </div>
        </form>
    </div>
</body>

</html>