@extends('layouts.admin')

@section('title', 'Tambah Admin')

@section('page-title', 'Tambah Admin')

@section('content')

<div class="mx-auto max-w-3xl space-y-6">

    {{-- HEADER --}}
    <div
        class="flex flex-col gap-4
               sm:flex-row sm:items-center
               sm:justify-between">

        <div>

            <h1 class="text-2xl font-bold text-slate-800">
                Tambah Admin
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Buat akun administrator baru.
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
            action="{{ route('admin.admins.store') }}"
            method="POST">

            @csrf


            <div class="p-4 sm:p-6">

                <h2 class="font-semibold text-slate-800">
                    Data Akun Admin
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Admin login menggunakan email dan password.
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
                            value="{{ old('name') }}"
                            required
                            autofocus
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
                            value="{{ old('email') }}"
                            required
                            class="w-full rounded-lg border-slate-300">

                        @error('email')
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
                                @selected(old('is_active', '1' )==='1' )>
                                Aktif
                            </option>

                            <option
                                value="0"
                                @selected(old('is_active')==='0' )>
                                Nonaktif
                            </option>

                        </select>

                    </div>


                    {{-- ROLE --}}
                    <div>

                        <label
                            class="mb-2 block
                                   text-sm font-medium">

                            Jenis Akun

                        </label>

                        <div
                            class="flex h-[42px]
                                   items-center rounded-lg
                                   border border-slate-200
                                   bg-slate-50 px-3">

                            <span
                                class="rounded-full
                                       bg-violet-100 px-2.5 py-1
                                       text-xs font-semibold
                                       text-violet-700">

                                Admin

                            </span>

                        </div>

                    </div>


                    {{-- PASSWORD --}}
                    <div>

                        <label
                            for="password"
                            class="mb-2 block
                                   text-sm font-medium">

                            Password *

                        </label>

                        <input
                            id="password"
                            type="password"
                            name="password"
                            required
                            class="w-full rounded-lg border-slate-300">

                        @error('password')
                        <p class="mt-1 text-sm text-red-600">
                            {{ $message }}
                        </p>
                        @enderror

                    </div>


                    {{-- CONFIRM PASSWORD --}}
                    <div>

                        <label
                            for="password_confirmation"
                            class="mb-2 block
                                   text-sm font-medium">

                            Konfirmasi Password *

                        </label>

                        <input
                            id="password_confirmation"
                            type="password"
                            name="password_confirmation"
                            required
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

                    Simpan Admin

                </button>

            </div>

        </form>

    </div>

</div>

@endsection