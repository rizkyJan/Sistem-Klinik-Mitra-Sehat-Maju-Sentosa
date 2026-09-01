<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>
        Lengkapi Data Pegawai
    </title>

    @vite([
    'resources/css/app.css',
    'resources/js/app.js'
    ])

</head>


<body
    class="
        min-h-screen
        bg-slate-50
        text-slate-800
        antialiased
    ">


    <div
        class="
            flex
            min-h-screen
            items-center
            justify-center
            px-4
            py-8
            sm:px-6
        ">

        <div
            class="
                w-full
                max-w-3xl
                overflow-hidden
                rounded-2xl
                border
                border-slate-200
                bg-white
                shadow-sm
            ">


            {{-- ====================================================
                HEADER
            ==================================================== --}}
            <div
                class="
                    border-b
                    border-slate-200
                    px-6
                    py-6
                    sm:px-8
                ">

                <div
                    class="
                        flex
                        flex-col
                        gap-4
                        sm:flex-row
                        sm:items-center
                        sm:justify-between
                    ">

                    <div>

                        <p
                            class="
                                text-sm
                                font-medium
                                text-blue-600
                            ">
                            Pendaftaran Pegawai
                        </p>

                        <h1
                            class="
                                mt-1
                                text-2xl
                                font-bold
                                text-slate-800
                            ">
                            Lengkapi Data Pegawai
                        </h1>

                        <p
                            class="
                                mt-2
                                text-sm
                                leading-6
                                text-slate-500
                            ">
                            Lengkapi identitas pegawai, pilih bidang,
                            dan tentukan jabatan sebelum dikirim
                            untuk verifikasi Administrator.
                        </p>

                    </div>


                    @if ($user->google_avatar)

                    <img
                        src="{{ $user->google_avatar }}"
                        alt="Foto akun Google"
                        class="
                                h-14
                                w-14
                                rounded-full
                                border
                                border-slate-200
                                object-cover
                            ">

                    @else

                    <div
                        class="
                                flex
                                h-14
                                w-14
                                shrink-0
                                items-center
                                justify-center
                                rounded-full
                                bg-blue-100
                                text-lg
                                font-semibold
                                text-blue-700
                            ">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>

                    @endif

                </div>

            </div>


            {{-- ====================================================
                GOOGLE ACCOUNT INFO
            ==================================================== --}}
            <div
                class="
                    border-b
                    border-slate-200
                    bg-slate-50/70
                    px-6
                    py-4
                    sm:px-8
                ">

                <div
                    class="
                        flex
                        flex-col
                        gap-1
                    ">

                    <p
                        class="
                            text-xs
                            font-semibold
                            uppercase
                            tracking-wider
                            text-slate-400
                        ">
                        Akun Google yang digunakan
                    </p>

                    <p
                        class="
                            break-all
                            text-sm
                            font-medium
                            text-slate-700
                        ">
                        {{ $user->email }}
                    </p>

                </div>

            </div>


            {{-- ====================================================
                FORM
            ==================================================== --}}
            <form
                method="POST"
                action="{{ route('employee.profile.update') }}"
                class="
                    space-y-6
                    px-6
                    py-6
                    sm:px-8
                    sm:py-8
                ">

                @csrf
                @method('PUT')


                @if ($errors->any())

                <div
                    class="
                            rounded-xl
                            border
                            border-red-200
                            bg-red-50
                            px-4
                            py-3
                            text-sm
                            text-red-700
                        ">

                    <p class="font-semibold">
                        Data belum dapat disimpan.
                    </p>

                    <ul
                        class="
                                mt-2
                                list-disc
                                space-y-1
                                pl-5
                            ">
                        @foreach ($errors->all() as $error)
                        <li>
                            {{ $error }}
                        </li>
                        @endforeach
                    </ul>

                </div>

                @endif


                <div
                    class="
                        grid
                        grid-cols-1
                        gap-5
                        md:grid-cols-2
                    ">


                    {{-- NAMA LENGKAP --}}
                    <div class="md:col-span-2">

                        <label
                            for="name"
                            class="
                                block
                                text-sm
                                font-semibold
                                text-slate-700
                            ">
                            Nama Lengkap
                            <span class="text-red-500">*</span>
                        </label>

                        <input
                            id="name"
                            type="text"
                            name="name"
                            value="{{ old('name', $user->name) }}"
                            required
                            autocomplete="name"
                            placeholder="Masukkan nama lengkap pegawai"
                            class="
                                mt-2
                                w-full
                                rounded-lg
                                border
                                border-slate-300
                                bg-white
                                px-3.5
                                py-2.5
                                text-sm
                                outline-none
                                transition
                                focus:border-blue-500
                                focus:ring-2
                                focus:ring-blue-100
                            ">

                        <p
                            class="
                                mt-1.5
                                text-xs
                                leading-5
                                text-slate-500
                            ">
                            Otomatis diisi dari nama akun Google,
                            tetapi boleh diperbaiki sesuai nama pegawai.
                        </p>

                        @error('name')
                        <p class="mt-1.5 text-xs text-red-600">
                            {{ $message }}
                        </p>
                        @enderror

                    </div>


                    {{-- NIK --}}
                    <div>

                        <label
                            for="nik"
                            class="
                                block
                                text-sm
                                font-semibold
                                text-slate-700
                            ">
                            NIK / ID Pegawai
                            <span class="text-red-500">*</span>
                        </label>

                        <input
                            id="nik"
                            type="text"
                            name="nik"
                            value="{{ old('nik', $user->nik) }}"
                            required
                            autocomplete="off"
                            placeholder="Contoh: MSMS001"
                            class="
                                mt-2
                                w-full
                                rounded-lg
                                border
                                border-slate-300
                                bg-white
                                px-3.5
                                py-2.5
                                text-sm
                                outline-none
                                transition
                                focus:border-blue-500
                                focus:ring-2
                                focus:ring-blue-100
                            ">

                        @error('nik')
                        <p class="mt-1.5 text-xs text-red-600">
                            {{ $message }}
                        </p>
                        @enderror

                    </div>


                    {{-- WHATSAPP --}}
                    <div>

                        <label
                            for="whatsapp"
                            class="
                                block
                                text-sm
                                font-semibold
                                text-slate-700
                            ">
                            WhatsApp
                            <span class="text-red-500">*</span>
                        </label>

                        <input
                            id="whatsapp"
                            type="text"
                            name="whatsapp"
                            value="{{ old('whatsapp', $user->whatsapp) }}"
                            required
                            autocomplete="off"
                            placeholder="08xxxxxxxxxx"
                            class="
                                mt-2
                                w-full
                                rounded-lg
                                border
                                border-slate-300
                                bg-white
                                px-3.5
                                py-2.5
                                text-sm
                                outline-none
                                transition
                                focus:border-blue-500
                                focus:ring-2
                                focus:ring-blue-100
                            ">

                        @error('whatsapp')
                        <p class="mt-1.5 text-xs text-red-600">
                            {{ $message }}
                        </p>
                        @enderror

                    </div>


                    {{-- TANGGAL MULAI KERJA --}}
                    <div>

                        <label
                            for="join_date"
                            class="
                                block
                                text-sm
                                font-semibold
                                text-slate-700
                            ">
                            Tanggal Mulai Kerja
                            <span class="text-red-500">*</span>
                        </label>

                        <input
                            id="join_date"
                            type="date"
                            name="join_date"
                            value="{{ old('join_date', $user->join_date?->format('Y-m-d')) }}"
                            max="{{ now()->format('Y-m-d') }}"
                            required
                            class="
                                mt-2
                                w-full
                                rounded-lg
                                border
                                border-slate-300
                                bg-white
                                px-3.5
                                py-2.5
                                text-sm
                                outline-none
                                transition
                                focus:border-blue-500
                                focus:ring-2
                                focus:ring-blue-100
                            ">

                        @error('join_date')
                        <p class="mt-1.5 text-xs text-red-600">
                            {{ $message }}
                        </p>
                        @enderror

                    </div>


                    {{-- JABATAN --}}
                    <div>

                        <label
                            for="role"
                            class="
                                block
                                text-sm
                                font-semibold
                                text-slate-700
                            ">
                            Jabatan
                            <span class="text-red-500">*</span>
                        </label>

                        <select
                            id="role"
                            name="role"
                            required
                            class="
                                mt-2
                                w-full
                                rounded-lg
                                border
                                border-slate-300
                                bg-white
                                px-3.5
                                py-2.5
                                text-sm
                                outline-none
                                transition
                                focus:border-blue-500
                                focus:ring-2
                                focus:ring-blue-100
                            ">

                            <option value="">
                                -- Pilih Jabatan --
                            </option>

                            <option
                                value="karyawan"
                                @selected(old('role')==='karyawan' )>
                                Karyawan
                            </option>

                            <option
                                value="kabid"
                                @selected(old('role')==='kabid' )>
                                Kabid
                            </option>

                        </select>

                        <p
                            class="
                                mt-1.5
                                text-xs
                                leading-5
                                text-slate-500
                            ">
                            Pendaftaran Google hanya dapat memilih
                            Karyawan atau Kabid.
                        </p>

                        @error('role')
                        <p class="mt-1.5 text-xs text-red-600">
                            {{ $message }}
                        </p>
                        @enderror

                    </div>


                    {{-- BIDANG --}}
                    <div class="md:col-span-2">

                        <label
                            for="department_id"
                            class="
                                block
                                text-sm
                                font-semibold
                                text-slate-700
                            ">
                            Bidang
                            <span class="text-red-500">*</span>
                        </label>

                        <select
                            id="department_id"
                            name="department_id"
                            required
                            class="
                                mt-2
                                w-full
                                rounded-lg
                                border
                                border-slate-300
                                bg-white
                                px-3.5
                                py-2.5
                                text-sm
                                outline-none
                                transition
                                focus:border-blue-500
                                focus:ring-2
                                focus:ring-blue-100
                            ">

                            <option value="">
                                -- Pilih Bidang --
                            </option>

                            @foreach ($departments as $department)

                            @php
                            $hasKabid =
                            in_array(
                            (int) $department->id,
                            $kabidDepartmentIds,
                            true
                            );
                            @endphp

                            <option
                                value="{{ $department->id }}"
                                data-has-kabid="{{ $hasKabid ? '1' : '0' }}"
                                data-name="{{ $department->name }}"
                                @selected(
                                (string) old( 'department_id' ,
                                $user->department_id
                                )
                                ===
                                (string) $department->id
                                )
                                >
                                {{ $department->name }}
                                {{ $hasKabid ? ' — sudah ada Kabid' : '' }}
                            </option>

                            @endforeach

                        </select>

                        <p
                            id="departmentHelp"
                            class="
                                mt-1.5
                                text-xs
                                leading-5
                                text-slate-500
                            ">
                            Pilih bidang tempat Anda bekerja.
                        </p>

                        @error('department_id')
                        <p class="mt-1.5 text-xs text-red-600">
                            {{ $message }}
                        </p>
                        @enderror

                    </div>

                </div>


                {{-- INFO APPROVAL --}}
                <div
                    class="
                        rounded-xl
                        border
                        border-amber-200
                        bg-amber-50
                        px-4
                        py-4
                    ">

                    <div class="flex gap-3">

                        <div
                            class="
                                mt-0.5
                                flex
                                h-8
                                w-8
                                shrink-0
                                items-center
                                justify-center
                                rounded-lg
                                bg-amber-100
                                text-amber-700
                            ">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-4 w-4"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 8v4m0 4h.01M12 3l9 18H3L12 3z" />
                            </svg>
                        </div>

                        <div>

                            <p
                                class="
                                    text-sm
                                    font-semibold
                                    text-amber-800
                                ">
                                Memerlukan Verifikasi Administrator
                            </p>

                            <p
                                class="
                                    mt-1
                                    text-xs
                                    leading-5
                                    text-amber-700
                                ">
                                Setelah data dikirim, akun belum dapat
                                menggunakan sistem sampai disetujui Admin.
                                Pendaftar Kabid akan masuk ke menu Kelola Kabid,
                                sedangkan Karyawan masuk ke menu Kelola Karyawan.
                            </p>

                        </div>

                    </div>

                </div>


                {{-- ACTION --}}
                <div
                    class="
                        flex
                        flex-col-reverse
                        gap-3
                        border-t
                        border-slate-200
                        pt-6
                        sm:flex-row
                        sm:items-center
                        sm:justify-end
                    ">

                    <button
                        id="submitButton"
                        type="submit"
                        class="
                            inline-flex
                            items-center
                            justify-center
                            rounded-lg
                            bg-blue-600
                            px-5
                            py-2.5
                            text-sm
                            font-semibold
                            text-white
                            transition
                            hover:bg-blue-700
                            focus:outline-none
                            focus:ring-2
                            focus:ring-blue-200
                            disabled:cursor-not-allowed
                            disabled:opacity-60
                        ">
                        Kirim untuk Verifikasi
                    </button>

                </div>

            </form>

        </div>

    </div>


    {{-- ============================================================
        ROLE / DEPARTMENT GUARD
    ============================================================ --}}
    <script>
        document.addEventListener(
            'DOMContentLoaded',
            function() {

                const roleSelect =
                    document.getElementById('role');

                const departmentSelect =
                    document.getElementById('department_id');

                const departmentHelp =
                    document.getElementById('departmentHelp');


                if (
                    !roleSelect ||
                    !departmentSelect
                ) {
                    return;
                }


                function refreshDepartmentOptions() {
                    const isKabid =
                        roleSelect.value === 'kabid';


                    Array.from(
                        departmentSelect.options
                    ).forEach(
                        function(option, index) {

                            if (index === 0) {
                                return;
                            }


                            const hasKabid =
                                option.dataset.hasKabid === '1';


                            option.disabled =
                                isKabid && hasKabid;
                        }
                    );


                    const selectedOption =
                        departmentSelect.options[
                            departmentSelect.selectedIndex
                        ];


                    if (
                        isKabid &&
                        selectedOption &&
                        selectedOption.dataset.hasKabid === '1'
                    ) {
                        departmentSelect.value = '';
                    }


                    if (departmentHelp) {

                        if (isKabid) {

                            departmentHelp.textContent =
                                'Untuk jabatan Kabid, bidang yang sudah memiliki Kabid tidak dapat dipilih.';

                        } else {

                            departmentHelp.textContent =
                                'Pilih bidang tempat Anda bekerja.';
                        }
                    }
                }


                roleSelect.addEventListener(
                    'change',
                    refreshDepartmentOptions
                );


                refreshDepartmentOptions();

            }
        );
    </script>

</body>

</html>