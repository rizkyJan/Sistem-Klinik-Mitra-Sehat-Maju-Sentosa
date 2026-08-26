@extends('layouts.admin')

@section('title', 'Edit Bidang')

@section('page-title', 'Edit Bidang')

@section('content')

<div class="mx-auto max-w-3xl">

    <div class="mb-6">

        <h1 class="text-2xl font-bold text-slate-800">
            Edit Bidang
        </h1>

        <p class="mt-1 text-sm text-slate-500">
            Perbarui informasi bidang.
        </p>

    </div>


    <div
        class="overflow-hidden rounded-xl
               border border-slate-200
               bg-white shadow-sm">

        <form
            action="{{ route('admin.departments.update', $department) }}"
            method="POST">

            @csrf
            @method('PUT')


            <div class="space-y-6 p-6">

                {{-- Nama --}}
                <div>

                    <label
                        for="name"
                        class="mb-2 block text-sm
                               font-medium text-slate-700">
                        Nama Bidang

                        <span class="text-red-500">*</span>
                    </label>


                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name', $department->name) }}"
                        class="w-full rounded-lg border-slate-300
                               focus:border-blue-500
                               focus:ring-blue-500">


                    @error('name')

                    <p class="mt-1 text-sm text-red-600">
                        {{ $message }}
                    </p>

                    @enderror

                </div>


                {{-- Description --}}
                <div>

                    <label
                        for="description"
                        class="mb-2 block text-sm
                               font-medium text-slate-700">
                        Deskripsi
                    </label>


                    <textarea
                        id="description"
                        name="description"
                        rows="4"
                        class="w-full resize-none rounded-lg
                               border-slate-300
                               focus:border-blue-500
                               focus:ring-blue-500">{{ old('description', $department->description) }}</textarea>

                </div>


                {{-- Status --}}
                <div>

                    <label
                        for="is_active"
                        class="mb-2 block text-sm
                               font-medium text-slate-700">
                        Status
                    </label>


                    <select
                        id="is_active"
                        name="is_active"
                        class="w-full rounded-lg border-slate-300
                               focus:border-blue-500
                               focus:ring-blue-500">

                        <option
                            value="1"
                            @selected(
                            old('is_active', $department->is_active) == 1
                            )
                            >
                            Aktif
                        </option>

                        <option
                            value="0"
                            @selected(
                            old('is_active', $department->is_active) == 0
                            )
                            >
                            Nonaktif
                        </option>

                    </select>

                </div>

            </div>


            <div
                class="flex items-center justify-end gap-3
                       border-t border-slate-200
                       bg-slate-50 px-6 py-4">

                <a
                    href="{{ route('admin.departments.index') }}"
                    class="rounded-lg border border-slate-300
                           bg-white px-4 py-2
                           text-sm font-medium text-slate-600
                           hover:bg-slate-50">
                    Batal
                </a>


                <button
                    type="submit"
                    class="rounded-lg bg-blue-600
                           px-4 py-2 text-sm
                           font-medium text-white
                           hover:bg-blue-700">
                    Simpan Perubahan
                </button>

            </div>

        </form>

    </div>

</div>

@endsection