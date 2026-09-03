<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Lengkapi Data Pegawai - SIMI-MS</title>

    @vite([
    'resources/css/app.css',
    'resources/js/app.js'
    ])
</head>

<body class="min-h-screen bg-slate-50 text-slate-800 antialiased">
    <div class="mx-auto min-h-screen max-w-5xl px-4 py-8 sm:px-6 lg:py-12">
        <div class="mb-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-col gap-4 p-5 sm:flex-row sm:items-center sm:justify-between sm:p-6">
                <div>
                    <p class="text-sm font-semibold text-blue-600">
                        Pendaftaran Pegawai via Google
                    </p>

                    <h1 class="mt-1 text-2xl font-bold text-slate-900">
                        {{ $user->approval_status === 'rejected'
                            ? 'Perbaiki Data Pegawai'
                            : 'Lengkapi Data Pegawai' }}
                    </h1>

                    <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">
                        Lengkapi NIP, NIK KTP, biodata, pas foto formal,
                        dan rekening BSI sebelum dikirim untuk verifikasi Administrator.
                    </p>
                </div>

                @if($user->google_avatar)
                <img
                    src="{{ $user->google_avatar }}"
                    alt="Foto akun Google"
                    class="h-16 w-16 shrink-0 rounded-full border border-slate-200 object-cover">
                @else
                <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-full bg-blue-100 text-xl font-bold text-blue-700">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                @endif
            </div>

            <div class="border-t border-slate-200 bg-slate-50/70 px-5 py-4 text-sm sm:px-6">
                <span class="text-slate-500">Akun Google:</span>
                <strong class="ml-1 break-all text-slate-700">
                    {{ $user->email }}
                </strong>
            </div>
        </div>

        @if(
        $user->approval_status === 'rejected'
        && $user->approval_rejection_reason
        )
        <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-4">
            <p class="font-semibold text-red-700">
                Data sebelumnya ditolak Admin.
            </p>

            <p class="mt-2 text-sm leading-6 text-red-600">
                {{ $user->approval_rejection_reason }}
            </p>
        </div>
        @endif

        @if($errors->any())
        <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-4">
            <p class="font-semibold text-red-700">
                Data belum dapat disimpan.
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
            action="{{ route('employee.profile.update') }}"
            enctype="multipart/form-data"
            class="space-y-6">

            @csrf
            @method('PUT')

            @include('auth.partials.employee-profile-fields', [
            'user' => $user,
            ])

            <div class="rounded-2xl border border-blue-200 bg-blue-50 p-4 text-sm leading-6 text-blue-800">
                Setelah dikirim, akun akan berstatus
                <strong>Menunggu Verifikasi Admin</strong>.
                Admin akan memeriksa data sebelum akun diaktifkan.
            </div>

            <div class="flex flex-col-reverse gap-3 pb-8 sm:flex-row sm:items-center sm:justify-end">
                <a
                    href="{{ route('employee.approval.waiting') }}"
                    class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    Kembali
                </a>

                <button
                    type="submit"
                    class="inline-flex items-center justify-center rounded-xl bg-blue-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-blue-700">
                    {{ $user->approval_status === 'rejected'
                        ? 'Kirim Ulang ke Admin'
                        : 'Simpan & Kirim ke Admin' }}
                </button>
            </div>
        </form>
    </div>
</body>

</html>