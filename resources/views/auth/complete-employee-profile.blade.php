<x-guest-layout>

    <div class="mb-6">
        <h1 class="text-xl font-semibold text-gray-900">
            Lengkapi Data Karyawan
        </h1>

        <p class="mt-2 text-sm text-gray-600">
            Anda sudah masuk menggunakan Google.
            Lengkapi data berikut untuk dikirim ke admin.
        </p>
    </div>


    <form
        method="POST"
        action="{{ route('employee.profile.update') }}"
        class="space-y-4"
        autocomplete="off">

        @csrf
        @method('PUT')


        {{-- =========================================================
            NAMA LENGKAP
        ========================================================== --}}
        <div>

            <x-input-label
                for="name"
                value="Nama Lengkap" />

            <x-text-input
                id="name"
                name="name"
                type="text"
                class="mt-1 block w-full"
                :value="old(
                    'name',
                    $user->profile_completed_at ? $user->name : ''
                )"
                placeholder="Masukkan nama lengkap"
                autocomplete="off"
                required
                autofocus />

            <x-input-error
                :messages="$errors->get('name')"
                class="mt-2" />

        </div>


        {{-- =========================================================
            EMAIL GOOGLE
            HANYA EMAIL YANG OTOMATIS TERISI
        ========================================================== --}}
        <div>

            <x-input-label
                for="email"
                value="Email Google" />

            <x-text-input
                id="email"
                type="email"
                class="mt-1 block w-full bg-gray-100 cursor-not-allowed"
                :value="$user->email"
                disabled />

            <p class="mt-1 text-xs text-gray-500">
                Email diambil otomatis dari akun Google dan tidak dapat diubah.
            </p>

        </div>


        {{-- =========================================================
            NIK KARYAWAN
        ========================================================== --}}
        <div>

            <x-input-label
                for="nik"
                value="NIK Karyawan" />

            <x-text-input
                id="nik"
                name="nik"
                type="text"
                class="mt-1 block w-full"
                :value="old(
                    'nik',
                    $user->profile_completed_at ? $user->nik : ''
                )"
                placeholder="Masukkan NIK karyawan"
                autocomplete="off"
                required />

            <x-input-error
                :messages="$errors->get('nik')"
                class="mt-2" />

        </div>


        {{-- =========================================================
            NOMOR WHATSAPP
        ========================================================== --}}
        <div>

            <x-input-label
                for="whatsapp"
                value="Nomor WhatsApp" />

            <x-text-input
                id="whatsapp"
                name="whatsapp"
                type="text"
                inputmode="numeric"
                class="mt-1 block w-full"
                :value="old(
                    'whatsapp',
                    $user->profile_completed_at ? $user->whatsapp : ''
                )"
                placeholder="Contoh: 081234567890"
                autocomplete="off"
                required />

            <x-input-error
                :messages="$errors->get('whatsapp')"
                class="mt-2" />

        </div>


        {{-- =========================================================
            TANGGAL MULAI KERJA
        ========================================================== --}}
        <div>

            <x-input-label
                for="join_date"
                value="Tanggal Mulai Kerja" />

            <x-text-input
                id="join_date"
                name="join_date"
                type="date"
                class="mt-1 block w-full"
                :value="old(
                    'join_date',
                    $user->profile_completed_at && $user->join_date
                        ? $user->join_date->format('Y-m-d')
                        : ''
                )"
                autocomplete="off"
                required />

            <x-input-error
                :messages="$errors->get('join_date')"
                class="mt-2" />

        </div>


        {{-- =========================================================
            BIDANG / DEPARTEMEN
        ========================================================== --}}
        <div>

            <x-input-label
                for="department_id"
                value="Bidang / Departemen" />

            <select
                id="department_id"
                name="department_id"
                class="
                    mt-1 block w-full
                    rounded-md
                    border-gray-300
                    shadow-sm
                    focus:border-indigo-500
                    focus:ring-indigo-500
                "
                autocomplete="off"
                required>

                <option value="">
                    -- Pilih Bidang / Departemen --
                </option>


                @foreach($departments as $department)

                <option
                    value="{{ $department->id }}"

                    @selected(
                    (string) old( 'department_id' ,

                    $user->profile_completed_at
                    ? $user->department_id
                    : ''
                    )

                    ===

                    (string) $department->id
                    )
                    >

                    {{ $department->name }}

                </option>

                @endforeach

            </select>


            <x-input-error
                :messages="$errors->get('department_id')"
                class="mt-2" />

        </div>


        {{-- =========================================================
            JIKA SEBELUMNYA DITOLAK ADMIN
        ========================================================== --}}
        @if(
        $user->approval_status === 'rejected'
        &&
        $user->approval_rejection_reason
        )

        <div
            class="
                    rounded-md
                    border border-red-200
                    bg-red-50
                    p-4
                    text-sm
                    text-red-700
                ">

            <div class="font-semibold mb-1">
                Data Anda perlu diperbaiki
            </div>

            <div>
                <strong>Catatan Admin:</strong>

                {{ $user->approval_rejection_reason }}
            </div>

        </div>

        @endif


        {{-- =========================================================
            BUTTON SUBMIT
        ========================================================== --}}
        <div class="pt-4">

            <x-primary-button
                class="w-full justify-center">

                Kirim untuk Verifikasi

            </x-primary-button>

        </div>

    </form>


    {{-- =============================================================
        LOGOUT
    ============================================================== --}}
    <form
        method="POST"
        action="{{ route('logout') }}"
        class="mt-5 text-center">

        @csrf

        <button
            type="submit"
            class="
                text-sm
                text-gray-500
                underline
                hover:text-gray-700
            ">
            Keluar
        </button>

    </form>

</x-guest-layout>