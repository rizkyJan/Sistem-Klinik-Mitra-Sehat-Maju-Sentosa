@extends('layouts.admin')

@section('title', 'Tambah Karyawan')

@section('page-title', 'Tambah Karyawan')

@section('content')

<div class="mx-auto max-w-4xl space-y-6">

    {{-- Header --}}
    <div
        class="flex flex-col gap-4
               sm:flex-row sm:items-center
               sm:justify-between">

        <div>

            <h1 class="text-2xl font-bold text-slate-800">
                Tambah Karyawan
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Tambahkan data karyawan baru
                Mitra Sehat.
            </p>

        </div>


        <a
            href="{{ route('admin.karyawan.index') }}"
            class="inline-flex items-center justify-center
                   rounded-lg border border-slate-300
                   bg-white px-4 py-2.5
                   text-sm font-medium text-slate-600
                   hover:bg-slate-50">
            ← Kembali
        </a>

    </div>


    {{-- Validation --}}
    @if($errors->any())

    <div
        class="rounded-xl border border-red-200
                   bg-red-50 p-4">

        <p class="font-medium text-red-700">
            Terdapat data yang belum benar.
        </p>

        <ul
            class="mt-2 list-disc space-y-1
                       pl-5 text-sm text-red-600">

            @foreach($errors->all() as $error)

            <li>
                {{ $error }}
            </li>

            @endforeach

        </ul>

    </div>

    @endif


    {{-- Form --}}
    <div
        class="overflow-hidden rounded-xl
               border border-slate-200
               bg-white shadow-sm">

        <form
            action="{{ route('admin.karyawan.store') }}"
            method="POST">

            @csrf


            {{-- Data pribadi --}}
            <div class="p-6">

                <div>

                    <h2 class="font-semibold text-slate-800">
                        Data Karyawan
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Informasi identitas dan kepegawaian.
                    </p>

                </div>


                <div
                    class="mt-6 grid grid-cols-1
                           gap-6 md:grid-cols-2">

                    {{-- Nama --}}
                    <div>

                        <label
                            for="name"
                            class="mb-2 block
                                   text-sm font-medium
                                   text-slate-700">
                            Nama Lengkap
                            <span class="text-red-500">*</span>
                        </label>

                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name') }}"
                            placeholder="Nama lengkap karyawan"
                            class="w-full rounded-lg
                                   border-slate-300
                                   focus:border-blue-500
                                   focus:ring-blue-500">

                        @error('name')
                        <p class="mt-1 text-sm text-red-600">
                            {{ $message }}
                        </p>
                        @enderror

                    </div>


                    {{-- NIK --}}
                    <div>

                        <label
                            for="nik"
                            class="mb-2 block
                                   text-sm font-medium
                                   text-slate-700">
                            NIK / ID Karyawan
                            <span class="text-red-500">*</span>
                        </label>

                        <input
                            type="text"
                            id="nik"
                            name="nik"
                            value="{{ old('nik') }}"
                            placeholder="Contoh: MSMS001"
                            class="w-full rounded-lg
                                   border-slate-300
                                   focus:border-blue-500
                                   focus:ring-blue-500">

                        @error('nik')
                        <p class="mt-1 text-sm text-red-600">
                            {{ $message }}
                        </p>
                        @enderror

                    </div>


                    {{-- Email --}}
                    <div>

                        <label
                            for="email"
                            class="mb-2 block
                                   text-sm font-medium
                                   text-slate-700">
                            Email
                            <span class="text-red-500">*</span>
                        </label>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="nama@email.com"
                            class="w-full rounded-lg
                                   border-slate-300
                                   focus:border-blue-500
                                   focus:ring-blue-500">

                        @error('email')
                        <p class="mt-1 text-sm text-red-600">
                            {{ $message }}
                        </p>
                        @enderror

                    </div>


                    {{-- WhatsApp --}}
                    <div>

                        <label
                            for="whatsapp"
                            class="mb-2 block
                                   text-sm font-medium
                                   text-slate-700">
                            WhatsApp
                            <span class="text-red-500">*</span>
                        </label>

                        <input
                            type="text"
                            id="whatsapp"
                            name="whatsapp"
                            value="{{ old('whatsapp') }}"
                            placeholder="08xxxxxxxxxx"
                            class="w-full rounded-lg
                                   border-slate-300
                                   focus:border-blue-500
                                   focus:ring-blue-500">

                        @error('whatsapp')
                        <p class="mt-1 text-sm text-red-600">
                            {{ $message }}
                        </p>
                        @enderror

                    </div>


                    {{-- Department --}}
                    <div>

                        <label
                            for="department_id"
                            class="mb-2 block
                                   text-sm font-medium
                                   text-slate-700">
                            Bidang
                            <span class="text-red-500">*</span>
                        </label>

                        <select
                            id="department_id"
                            name="department_id"
                            class="w-full rounded-lg
                                   border-slate-300
                                   focus:border-blue-500
                                   focus:ring-blue-500">

                            <option value="">
                                -- Pilih Bidang --
                            </option>


                            @foreach($departments as $department)

                            <option
                                value="{{ $department->id }}"
                                @selected(
                                old('department_id')==$department->id
                                )
                                >
                                {{ $department->name }}
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
                                   text-sm font-medium
                                   text-slate-700">
                            Tanggal Mulai Kerja
                            <span class="text-red-500">*</span>
                        </label>

                        <input
                            type="date"
                            id="join_date"
                            name="join_date"
                            value="{{ old('join_date') }}"
                            max="{{ now()->format('Y-m-d') }}"
                            class="w-full rounded-lg
                                   border-slate-300
                                   focus:border-blue-500
                                   focus:ring-blue-500">

                        <p class="mt-1 text-xs text-slate-400">
                            Digunakan untuk menentukan
                            hak cuti tahunan dan masa kerja.
                        </p>

                        @error('join_date')
                        <p class="mt-1 text-sm text-red-600">
                            {{ $message }}
                        </p>
                        @enderror

                    </div>


                    {{-- Status --}}
                    <div>

                        <label
                            for="is_active"
                            class="mb-2 block
                                   text-sm font-medium
                                   text-slate-700">
                            Status
                            <span class="text-red-500">*</span>
                        </label>

                        <select
                            id="is_active"
                            name="is_active"
                            class="w-full rounded-lg
                                   border-slate-300
                                   focus:border-blue-500
                                   focus:ring-blue-500">

                            <option
                                value="1"
                                @selected(
                                old('is_active', '1' )==='1'
                                )>
                                Aktif
                            </option>

                            <option
                                value="0"
                                @selected(
                                old('is_active')==='0'
                                )>
                                Nonaktif
                            </option>

                        </select>

                    </div>

                </div>

            </div>



            {{-- Login --}}
            <div class="border-t border-slate-200 p-6">

                <h2 class="font-semibold text-slate-800">
                    Login Karyawan
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Password dapat digunakan untuk login internal.
                </p>


                <div
                    class="mt-6 grid grid-cols-1
                           gap-6 md:grid-cols-2">

                    {{-- Password --}}
                    <div>

                        <label
                            for="password"
                            class="mb-2 block
                                   text-sm font-medium
                                   text-slate-700">
                            Password
                            <span class="text-red-500">*</span>
                        </label>

                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="w-full rounded-lg
                                   border-slate-300">

                        @error('password')
                        <p class="mt-1 text-sm text-red-600">
                            {{ $message }}
                        </p>
                        @enderror

                    </div>


                    {{-- Confirm Password --}}
                    <div>

                        <label
                            for="password_confirmation"
                            class="mb-2 block
                                   text-sm font-medium
                                   text-slate-700">
                            Konfirmasi Password
                            <span class="text-red-500">*</span>
                        </label>

                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            class="w-full rounded-lg
                                   border-slate-300">

                    </div>

                </div>

            </div>



            {{-- Footer --}}
            <div
                class="flex justify-end gap-3
                       border-t border-slate-200
                       bg-slate-50 px-6 py-4">

                <a
                    href="{{ route('admin.karyawan.index') }}"
                    class="rounded-lg border
                           border-slate-300
                           bg-white px-4 py-2
                           text-sm font-medium
                           text-slate-600
                           hover:bg-slate-50">
                    Batal
                </a>


                <button
                    type="submit"
                    class="rounded-lg bg-blue-600
                           px-5 py-2 text-sm
                           font-medium text-white
                           hover:bg-blue-700">
                    Simpan Karyawan
                </button>

            </div>

        </form>

    </div>

</div>

@endsection