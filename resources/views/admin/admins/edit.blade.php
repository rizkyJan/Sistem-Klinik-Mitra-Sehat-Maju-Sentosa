@extends('layouts.admin')

@section('title', 'Edit Admin')

@section('page-title', 'Edit Admin')

@section('content')

<div class="mx-auto max-w-3xl space-y-6">

    {{-- HEADER --}}
    <div
        class="flex flex-col gap-4
               sm:flex-row sm:items-center
               sm:justify-between">

        <div>

            <h1 class="text-2xl font-bold text-slate-800">
                Edit Admin
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Perbarui informasi akun administrator.
            </p>

        </div>


        <a
            href="{{ route('admin.admins.index') }}"
            class="rounded-lg border border-slate-300
                   bg-white px-4 py-2.5
                   text-center text-sm font-medium
                   text-slate-600 hover:bg-slate-50">

            ← Kembali

        </a>

    </div>


    {{-- ERROR --}}
    @if($errors->any())

    <div
        class="rounded-xl border border-red-200
                   bg-red-50 p-4
                   text-sm text-red-700">

        <p class="font-medium">
            Terdapat data yang belum benar:
        </p>

        <ul class="mt-2 list-disc space-y-1 pl-5">

            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach

        </ul>

    </div>

    @endif


    {{-- FORM --}}
    <div
        class="overflow-hidden rounded-xl
               border border-slate-200
               bg-white shadow-sm">

        <form
            action="{{ route('admin.admins.update', $adminUser) }}"
            method="POST">

            @csrf
            @method('PUT')


            <div class="p-4 sm:p-6">

                <h2 class="font-semibold text-slate-800">
                    Data Admin
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Informasi akun dan hak akses pengguna.
                </p>


                <div
                    class="mt-6 grid grid-cols-1
                           gap-6 md:grid-cols-2">

                    {{-- NAMA --}}
                    <div>

                        <label
                            for="name"
                            class="mb-2 block
                                   text-sm font-medium">

                            Nama Lengkap *

                        </label>

                        <input
                            id="name"
                            type="text"
                            name="name"
                            value="{{ old('name', $adminUser->name) }}"
                            required
                            class="w-full rounded-lg border-slate-300">

                        @error('name')
                        <p class="mt-1 text-sm text-red-600">
                            {{ $message }}
                        </p>
                        @enderror

                    </div>


                    {{-- EMAIL --}}
                    <div>

                        <label
                            for="email"
                            class="mb-2 block
                                   text-sm font-medium">

                            Email *

                        </label>

                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email', $adminUser->email) }}"
                            required
                            class="w-full rounded-lg border-slate-300">

                        @error('email')
                        <p class="mt-1 text-sm text-red-600">
                            {{ $message }}
                        </p>
                        @enderror

                    </div>


                    {{-- ROLE --}}
                    <div>

                        <label
                            for="role"
                            class="mb-2 block
                                   text-sm font-medium">

                            Jenis Akun *

                        </label>

                        <select
                            id="role"
                            name="role"
                            required
                            class="w-full rounded-lg border-slate-300">

                            <option
                                value="admin"
                                @selected(
                                old( 'role' ,
                                $adminUser->role
                                ) === 'admin'
                                )>

                                Admin

                            </option>


                            <option
                                value="karyawan"
                                @selected(
                                old( 'role' ,
                                $adminUser->role
                                ) === 'karyawan'
                                )>

                                Karyawan

                            </option>

                        </select>

                        @if(auth()->id() === $adminUser->id)

                        <p class="mt-1 text-xs text-amber-600">
                            Akun yang sedang Anda gunakan tidak dapat diubah menjadi karyawan.
                        </p>

                        @else

                        <p class="mt-1 text-xs text-slate-400">
                            Jika diubah menjadi Karyawan, akun akan pindah dari Data Admin ke Data Karyawan.
                        </p>

                        @endif

                        @error('role')
                        <p class="mt-1 text-sm text-red-600">
                            {{ $message }}
                        </p>
                        @enderror

                    </div>


                    {{-- STATUS --}}
                    <div>

                        <label
                            for="is_active"
                            class="mb-2 block
                                   text-sm font-medium">

                            Status *

                        </label>

                        <select
                            id="is_active"
                            name="is_active"
                            required
                            class="w-full rounded-lg border-slate-300">

                            <option
                                value="1"
                                @selected(
                                (string) old( 'is_active' ,
                                $adminUser->is_active ? '1' : '0'
                                ) === '1'
                                )>

                                Aktif

                            </option>


                            <option
                                value="0"
                                @selected(
                                (string) old( 'is_active' ,
                                $adminUser->is_active ? '1' : '0'
                                ) === '0'
                                )>

                                Nonaktif

                            </option>

                        </select>

                        @error('is_active')
                        <p class="mt-1 text-sm text-red-600">
                            {{ $message }}
                        </p>
                        @enderror

                    </div>

                </div>

            </div>


            {{-- PASSWORD --}}
            <div
                class="border-t border-slate-200
                       p-4 sm:p-6">

                <h2 class="font-semibold text-slate-800">
                    Ubah Password
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Kosongkan jika password tidak ingin diubah.
                </p>


                @if($adminUser->google_id)

                <div
                    class="mt-4 rounded-xl
                               border border-amber-200
                               bg-amber-50 p-3
                               text-sm text-amber-700">

                    Akun ini pernah terhubung dengan Google.
                    Jika akun digunakan sebagai Admin, pastikan password login sudah dibuat.

                </div>

                @endif


                <div
                    class="mt-6 grid grid-cols-1
                           gap-6 md:grid-cols-2">

                    <div>

                        <label
                            for="password"
                            class="mb-2 block
                                   text-sm font-medium">

                            Password Baru

                        </label>

                        <input
                            id="password"
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

                        <label
                            for="password_confirmation"
                            class="mb-2 block
                                   text-sm font-medium">

                            Konfirmasi Password

                        </label>

                        <input
                            id="password_confirmation"
                            type="password"
                            name="password_confirmation"
                            class="w-full rounded-lg border-slate-300">

                    </div>

                </div>

            </div>


            {{-- FOOTER --}}
            <div
                class="flex flex-col-reverse gap-3
                       border-t border-slate-200
                       bg-slate-50 px-4 py-4
                       sm:flex-row sm:justify-end
                       sm:px-6">

                <a
                    href="{{ route('admin.admins.index') }}"
                    class="rounded-lg border
                           border-slate-300 bg-white
                           px-4 py-2.5 text-center
                           text-sm font-medium text-slate-600">

                    Batal

                </a>


                <button
                    type="submit"
                    class="rounded-lg bg-blue-600
                           px-5 py-2.5
                           text-sm font-medium text-white
                           hover:bg-blue-700">

                    Simpan Perubahan

                </button>

            </div>

        </form>

    </div>

</div>

@endsection