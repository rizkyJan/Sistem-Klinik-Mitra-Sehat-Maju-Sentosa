@extends('layouts.admin')

@section('title', 'Data Bidang')

@section('page-title', 'Data Bidang')

@section('content')

<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

        <div>
            <h1 class="text-2xl font-bold text-slate-800">
                Data Bidang
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Kelola bidang atau unit kerja karyawan Mitra Sehat Maju Sentosa.
            </p>
        </div>

        <a
            href="{{ route('admin.departments.create') }}"
            class="inline-flex items-center justify-center gap-2
                   rounded-lg bg-blue-600 px-4 py-2.5
                   text-sm font-medium text-white
                   transition hover:bg-blue-700">
            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor">
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 4v16m8-8H4" />
            </svg>

            Tambah Bidang
        </a>

    </div>


    {{-- Success --}}
    @if(session('success'))

    <div
        class="rounded-lg border border-emerald-200
                   bg-emerald-50 px-4 py-3
                   text-sm text-emerald-700">
        {{ session('success') }}
    </div>

    @endif


    {{-- Error --}}
    @if(session('error'))

    <div
        class="rounded-lg border border-red-200
                   bg-red-50 px-4 py-3
                   text-sm text-red-700">
        {{ session('error') }}
    </div>

    @endif


    <div
        class="overflow-hidden rounded-xl
               border border-slate-200
               bg-white shadow-sm">

        {{-- Search --}}
        <div class="border-b border-slate-200 p-5">

            <form
                action="{{ route('admin.departments.index') }}"
                method="GET"
                class="flex flex-col gap-3 sm:flex-row">

                <div class="relative w-full sm:max-w-md">

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="absolute left-3 top-1/2 h-5 w-5
                               -translate-y-1/2 text-slate-400"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.5"
                            d="m21 21-4.35-4.35m2.1-5.4
                               a7.5 7.5 0 1 1-15 0
                               7.5 7.5 0 0 1 15 0Z" />
                    </svg>

                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari nama bidang..."
                        class="w-full rounded-lg border-slate-300
                               pl-10 text-sm
                               focus:border-blue-500
                               focus:ring-blue-500">

                </div>

                <button
                    type="submit"
                    class="rounded-lg bg-slate-800
                           px-4 py-2 text-sm font-medium
                           text-white hover:bg-slate-700">
                    Cari
                </button>

                @if(request('search'))

                <a
                    href="{{ route('admin.departments.index') }}"
                    class="rounded-lg border border-slate-300
                               px-4 py-2 text-center text-sm
                               font-medium text-slate-600
                               hover:bg-slate-50">
                    Reset
                </a>

                @endif

            </form>

        </div>


        {{-- Table --}}
        <div class="overflow-x-auto">

            <table class="min-w-full divide-y divide-slate-200">

                <thead class="bg-slate-50">

                    <tr>

                        <th
                            class="px-6 py-3 text-left text-xs
                                   font-semibold uppercase
                                   tracking-wider text-slate-500">
                            No
                        </th>

                        <th
                            class="px-6 py-3 text-left text-xs
                                   font-semibold uppercase
                                   tracking-wider text-slate-500">
                            Bidang
                        </th>

                        <th
                            class="px-6 py-3 text-left text-xs
                                   font-semibold uppercase
                                   tracking-wider text-slate-500">
                            Deskripsi
                        </th>

                        <th
                            class="px-6 py-3 text-left text-xs
                                   font-semibold uppercase
                                   tracking-wider text-slate-500">
                            Anggota
                        </th>

                        <th
                            class="px-6 py-3 text-left text-xs
                                   font-semibold uppercase
                                   tracking-wider text-slate-500">
                            Status
                        </th>

                        <th
                            class="px-6 py-3 text-right text-xs
                                   font-semibold uppercase
                                   tracking-wider text-slate-500">
                            Aksi
                        </th>

                    </tr>

                </thead>


                <tbody class="divide-y divide-slate-200 bg-white">

                    @forelse($departments as $department)

                    <tr class="transition hover:bg-slate-50">

                        <td
                            class="whitespace-nowrap px-6 py-4
                                       text-sm text-slate-500">
                            {{ $departments->firstItem() + $loop->index }}
                        </td>


                        <td class="px-6 py-4">

                            <div class="flex items-center gap-3">

                                <div
                                    class="flex h-10 w-10
                                               items-center justify-center
                                               rounded-lg bg-blue-50
                                               text-blue-600">

                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-5 w-5"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="1.5"
                                            d="M3 7h18M5 7v13h14V7M9 11h6" />
                                    </svg>

                                </div>

                                <div>
                                    <p class="font-semibold text-slate-800">
                                        {{ $department->name }}
                                    </p>
                                </div>

                            </div>

                        </td>


                        <td
                            class="max-w-xs px-6 py-4
                                       text-sm text-slate-500">
                            {{ $department->description ?: '-' }}
                        </td>


                        <td
                            class="whitespace-nowrap px-6 py-4
                                       text-sm text-slate-600">
                            <span
                                class="inline-flex rounded-full
                                           bg-blue-50 px-2.5 py-1
                                           text-xs font-medium text-blue-700">
                                {{ $department->users_count }} orang
                            </span>
                        </td>


                        <td class="whitespace-nowrap px-6 py-4">

                            @if($department->is_active)

                            <span
                                class="inline-flex rounded-full
                                               bg-emerald-50 px-2.5 py-1
                                               text-xs font-medium text-emerald-700">
                                Aktif
                            </span>

                            @else

                            <span
                                class="inline-flex rounded-full
                                               bg-slate-100 px-2.5 py-1
                                               text-xs font-medium text-slate-600">
                                Nonaktif
                            </span>

                            @endif

                        </td>


                        <td class="whitespace-nowrap px-6 py-4 text-right">

                            <div class="flex items-center justify-end gap-2">

                                <a
                                    href="{{ route('admin.departments.edit', $department) }}"
                                    class="rounded-lg bg-amber-50
                                               px-3 py-2 text-xs
                                               font-medium text-amber-700
                                               hover:bg-amber-100">
                                    Edit
                                </a>


                                <form
                                    action="{{ route('admin.departments.destroy', $department) }}"
                                    method="POST"
                                    onsubmit="return confirm('Yakin ingin menghapus bidang ini?')">
                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="rounded-lg bg-red-50
                                                   px-3 py-2 text-xs
                                                   font-medium text-red-700
                                                   hover:bg-red-100">
                                        Hapus
                                    </button>

                                </form>

                            </div>

                        </td>

                    </tr>

                    @empty

                    <tr>

                        <td
                            colspan="6"
                            class="px-6 py-12 text-center">

                            <p class="text-sm font-medium text-slate-600">
                                Belum ada data bidang.
                            </p>

                            <p class="mt-1 text-xs text-slate-400">
                                Tambahkan bidang pertama untuk digunakan oleh karyawan.
                            </p>

                        </td>

                    </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        @if($departments->hasPages())

        <div class="border-t border-slate-200 px-6 py-4">
            {{ $departments->links() }}
        </div>

        @endif

    </div>

</div>

@endsection