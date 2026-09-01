@extends('layouts.admin')

@section('title', 'Edit Kabid')

@section('page-title', 'Edit Kabid')

@section('content')

<div class="mx-auto max-w-4xl space-y-6">

    {{-- Header --}}
    <div
        class="flex flex-col gap-4
               sm:flex-row sm:items-center
               sm:justify-between">

        <div>

            <h1 class="text-2xl font-bold text-slate-800">
                Edit Kabid
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Perbarui informasi Kabid.
            </p>

        </div>


        <a
            href="{{ route('admin.kabid.index') }}"
            class="rounded-lg border
                   border-slate-300 bg-white
                   px-4 py-2.5 text-sm
                   font-medium text-slate-600
                   hover:bg-slate-50">
            ← Kembali
        </a>

    </div>



    @if($errors->any())

    <div
        class="rounded-xl border
                   border-red-200 bg-red-50
                   p-4 text-sm text-red-700">

        <p class="font-medium">
            Terdapat data yang belum benar:
        </p>

        <ul class="mt-2 list-disc space-y-1 pl-5">

            @foreach($errors->all() as $error)

            <li>
                {{ $error }}
            </li>

            @endforeach

        </ul>

    </div>

    @endif



    <div
        class="overflow-hidden rounded-xl
               border border-slate-200
               bg-white shadow-sm">

        <form
            action="{{ route(
                'admin.kabid.update',
                $kabid
            ) }}"
            method="POST">

            @csrf
            @method('PUT')


            <div class="p-6">

                <h2 class="font-semibold text-slate-800">
                    Data Kabid
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Informasi identitas dan kepegawaian.
                </p>


                <div
                    class="mt-6 grid grid-cols-1
                           gap-6 md:grid-cols-2">

                    {{-- Nama --}}
                    <div>

                        <label class="mb-2 block text-sm font-medium">
                            Nama Lengkap *
                        </label>

                        <input
                            type="text"
                            name="name"
                            value="{{ old(
                                'name',
                                $kabid->name
                            ) }}"
                            class="w-full rounded-lg border-slate-300">

                        @error('name')
                        <p class="mt-1 text-sm text-red-600">
                            {{ $message }}
                        </p>
                        @enderror

                    </div>


                    {{-- NIK --}}
                    <div>

                        <label class="mb-2 block text-sm font-medium">
                            NIK / ID Kabid *
                        </label>

                        <input
                            type="text"
                            name="nik"
                            value="{{ old(
                                'nik',
                                $kabid->nik
                            ) }}"
                            class="w-full rounded-lg border-slate-300">

                        @error('nik')
                        <p class="mt-1 text-sm text-red-600">
                            {{ $message }}
                        </p>
                        @enderror

                    </div>


                    {{-- Email --}}
                    <div>

                        <label class="mb-2 block text-sm font-medium">
                            Email *
                        </label>

                        <input
                            type="email"
                            name="email"
                            value="{{ old(
                                'email',
                                $kabid->email
                            ) }}"
                            class="w-full rounded-lg border-slate-300">

                        @error('email')
                        <p class="mt-1 text-sm text-red-600">
                            {{ $message }}
                        </p>
                        @enderror

                    </div>


                    {{-- WA --}}
                    <div>

                        <label class="mb-2 block text-sm font-medium">
                            WhatsApp *
                        </label>

                        <input
                            type="text"
                            name="whatsapp"
                            value="{{ old(
                                'whatsapp',
                                $kabid->whatsapp
                            ) }}"
                            class="w-full rounded-lg border-slate-300">

                        @error('whatsapp')
                        <p class="mt-1 text-sm text-red-600">
                            {{ $message }}
                        </p>
                        @enderror

                    </div>


                    {{-- Bidang --}}
                    <div>

                        <label class="mb-2 block text-sm font-medium">
                            Bidang *
                        </label>

                        <select
                            name="department_id"
                            class="w-full rounded-lg border-slate-300">

                            <option value="">
                                -- Pilih Bidang --
                            </option>

                            @foreach($departments as $department)

                            <option
                                value="{{ $department->id }}"
                                @selected(
                                old( 'department_id' ,
                                $kabid->department_id
                                )
                                == $department->id
                                )
                                >
                                {{ $department->name }}

                                @if(! $department->is_active)
                                - Nonaktif
                                @endif

                            </option>

                            @endforeach

                        </select>

                        @error('department_id')
                        <p class="mt-1 text-sm text-red-600">
                            {{ $message }}
                        </p>
                        @enderror

                    </div>


                    {{-- JOIN DATE --}}
                    <div>

                        <label
                            for="join_date"
                            class="mb-2 block
                                   text-sm font-medium">
                            Tanggal Mulai Kerja *
                        </label>

                        <input
                            type="date"
                            id="join_date"
                            name="join_date"
                            max="{{ now()->format('Y-m-d') }}"
                            value="{{ old(
                                'join_date',
                                $kabid->join_date
                                    ?->format('Y-m-d')
                            ) }}"
                            class="w-full rounded-lg
                                   border-slate-300">

                        <p class="mt-1 text-xs text-slate-400">
                            Menentukan masa kerja dan
                            kelayakan cuti tahunan.
                        </p>

                        @error('join_date')
                        <p class="mt-1 text-sm text-red-600">
                            {{ $message }}
                        </p>
                        @enderror

                    </div>


                    {{-- Status --}}
                    <div>

                        <label class="mb-2 block text-sm font-medium">
                            Status *
                        </label>

                        <select
                            name="is_active"
                            class="w-full rounded-lg border-slate-300">

                            <option
                                value="1"
                                @selected(
                                (string) old( 'is_active' ,
                                $kabid->is_active ? '1' : '0'
                                ) === '1'
                                )
                                >
                                Aktif
                            </option>

                            <option
                                value="0"
                                @selected(
                                (string) old( 'is_active' ,
                                $kabid->is_active ? '1' : '0'
                                ) === '0'
                                )
                                >
                                Nonaktif
                            </option>

                        </select>

                    </div>

                    {{-- Jabatan --}}
                    <div>

                        <label class="mb-2 block text-sm font-medium">
                            Jabatan
                        </label>

                        <input
                            type="text"
                            value="Kabid"
                            disabled
                            class="w-full cursor-not-allowed
                                   rounded-lg border-slate-300
                                   bg-slate-100 text-slate-500">

                        <p class="mt-1 text-xs text-slate-500">
                            Jabatan pada menu Kelola Kabid
                            selalu disimpan sebagai Kabid.
                        </p>

                    </div>

                </div>

            </div>



            {{-- Password --}}
            <div class="border-t border-slate-200 p-6">

                <h2 class="font-semibold text-slate-800">
                    Ubah Password
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Kosongkan jika password tidak ingin diubah.
                </p>


                <div
                    class="mt-6 grid grid-cols-1
                           gap-6 md:grid-cols-2">

                    <div>

                        <label class="mb-2 block text-sm font-medium">
                            Password Baru
                        </label>

                        <input
                            type="password"
                            name="password"
                            class="w-full rounded-lg border-slate-300">

                        @error('password')
                        <p class="mt-1 text-sm text-red-600">
                            {{ $message }}
                        </p>
                        @enderror

                    </div>


                    <div>

                        <label class="mb-2 block text-sm font-medium">
                            Konfirmasi Password
                        </label>

                        <input
                            type="password"
                            name="password_confirmation"
                            class="w-full rounded-lg border-slate-300">

                    </div>

                </div>

            </div>



            {{-- Footer --}}
            <div
                class="flex justify-end gap-3
                       border-t border-slate-200
                       bg-slate-50 px-6 py-4">

                <a
                    href="{{ route('admin.kabid.index') }}"
                    class="rounded-lg border
                           border-slate-300 bg-white
                           px-4 py-2 text-sm
                           font-medium text-slate-600">
                    Batal
                </a>


                <button
                    type="submit"
                    class="rounded-lg bg-blue-600
                           px-5 py-2 text-sm
                           font-medium text-white
                           hover:bg-blue-700">
                    Simpan Perubahan
                </button>

            </div>

        </form>

    </div>

</div>

@endsection