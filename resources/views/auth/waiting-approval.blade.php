<x-guest-layout>
    <div class="text-center">
        @if(session('success'))
        <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
            {{ session('success') }}
        </div>
        @endif

        @if($user->approval_status === 'rejected')
        <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-red-100 text-2xl">
            !
        </div>

        <h1 class="text-xl font-semibold text-gray-900">
            Data Perlu Diperbaiki
        </h1>

        <p class="mt-2 text-sm text-gray-600">
            Admin meminta Anda memperbaiki data sebelum akun dapat digunakan.
        </p>

        @if($user->approval_rejection_reason)
        <div class="mt-5 rounded-xl border border-red-200 bg-red-50 p-4 text-left text-sm text-red-700">
            <strong>Catatan Admin:</strong><br>
            {{ $user->approval_rejection_reason }}
        </div>
        @endif

        <a
            href="{{ route('employee.profile.complete') }}"
            class="mt-6 inline-flex items-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-500">
            Perbaiki Data
        </a>
        @else
        <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-amber-100 text-2xl">
            ⏳
        </div>

        <h1 class="text-xl font-semibold text-gray-900">
            Menunggu Verifikasi Admin
        </h1>

        <p class="mt-2 text-sm leading-6 text-gray-600">
            Data Anda sudah dikirim. Setelah Admin menyetujui,
            akun akan aktif dan dapat digunakan sesuai role.
        </p>
        @endif

        <div class="mt-6 rounded-xl bg-gray-50 p-4 text-left text-sm text-gray-600">
            <div>
                <span class="font-medium">Nama:</span>
                {{ $user->name }}
            </div>

            <div class="mt-1">
                <span class="font-medium">Email:</span>
                {{ $user->email }}
            </div>

            <div class="mt-1">
                <span class="font-medium">NIP / ID Pegawai:</span>
                {{ $user->nip ?? $user->nik ?? '-' }}
            </div>

            <div class="mt-1">
                <span class="font-medium">NIK KTP:</span>
                {{ $user->nik_ktp ?: '-' }}
            </div>

            <div class="mt-1">
                <span class="font-medium">Role:</span>
                {{ $user->role === 'kabid' ? 'Kabid' : 'Karyawan' }}
            </div>

            <div class="mt-1">
                <span class="font-medium">Bidang:</span>
                {{ $user->department?->name ?? '-' }}
            </div>

            <div class="mt-1">
                <span class="font-medium">Bank:</span>
                Bank Syariah Indonesia (BSI)
            </div>

            <div class="mt-1">
                <span class="font-medium">Rekening:</span>
                {{ $user->bank_account_number ?: '-' }}
            </div>
        </div>

        <form
            method="POST"
            action="{{ route('logout') }}"
            class="mt-6">
            @csrf

            <button
                type="submit"
                class="text-sm text-gray-500 underline hover:text-gray-700">
                Keluar
            </button>
        </form>
    </div>
</x-guest-layout>