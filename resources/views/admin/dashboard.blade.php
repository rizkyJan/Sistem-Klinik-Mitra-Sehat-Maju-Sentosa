@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('page-title', 'Dashboard')

@section('content')

{{-- ============================================================
    HEADING
============================================================ --}}
<div class="mb-8">

    <h1 class="text-2xl font-bold text-slate-800">
        Selamat datang, {{ $user->name }}
    </h1>

    <p class="mt-1 text-sm text-slate-500">
        Kelola sistem perizinan karyawan Mitra Sehat Maju Sentosa.
    </p>

</div>



{{-- ============================================================
    STATISTIC CARDS
============================================================ --}}
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-8">

    {{-- Total Karyawan --}}
    <div
        class="bg-white border border-slate-200
               rounded-xl p-5 shadow-sm">

        <div class="flex items-center justify-between">

            <div>

                <p class="text-sm text-slate-500">
                    Total Karyawan
                </p>

                <h2 class="mt-2 text-3xl font-bold text-slate-800">
                    {{ $totalKaryawan }}
                </h2>

            </div>


            <div
                class="w-11 h-11 rounded-xl
                       bg-blue-50 text-blue-600
                       flex items-center justify-center">

                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="w-6 h-6"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="1.5"
                        d="M17 20h5v-2a4 4 0 00-4-4h-1
                           M9 20H2v-2a4 4 0 014-4h1
                           m6-4a4 4 0 100-8
                           4 4 0 000 8
                           m-4 4a4 4 0 100 8
                           4 4 0 000-8z" />
                </svg>

            </div>

        </div>

    </div>



    {{-- Menunggu Persetujuan --}}
    <div
        class="bg-white border border-slate-200
               rounded-xl p-5 shadow-sm">

        <div class="flex items-center justify-between">

            <div>

                <p class="text-sm text-slate-500">
                    Menunggu Persetujuan
                </p>

                <h2 class="mt-2 text-3xl font-bold text-slate-800">
                    {{ $pendingCount }}
                </h2>

            </div>


            <div
                class="w-11 h-11 rounded-xl
                       bg-amber-50 text-amber-600
                       flex items-center justify-center">

                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="w-6 h-6"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="1.5"
                        d="M12 8v4l3 3
                           m6-3a9 9 0 11-18 0
                           9 9 0 0118 0z" />
                </svg>

            </div>

        </div>

    </div>



    {{-- Disetujui --}}
    <div
        class="bg-white border border-slate-200
               rounded-xl p-5 shadow-sm">

        <div class="flex items-center justify-between">

            <div>

                <p class="text-sm text-slate-500">
                    Cuti Disetujui
                </p>

                <h2 class="mt-2 text-3xl font-bold text-slate-800">
                    {{ $approvedCount }}
                </h2>

            </div>


            <div
                class="w-11 h-11 rounded-xl
                       bg-emerald-50 text-emerald-600
                       flex items-center justify-center">

                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="w-6 h-6"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="1.5"
                        d="M5 13l4 4L19 7" />
                </svg>

            </div>

        </div>

    </div>



    {{-- Ditolak --}}
    <div
        class="bg-white border border-slate-200
               rounded-xl p-5 shadow-sm">

        <div class="flex items-center justify-between">

            <div>

                <p class="text-sm text-slate-500">
                    Cuti Ditolak
                </p>

                <h2 class="mt-2 text-3xl font-bold text-slate-800">
                    {{ $rejectedCount }}
                </h2>

            </div>


            <div
                class="w-11 h-11 rounded-xl
                       bg-red-50 text-red-600
                       flex items-center justify-center">

                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="w-6 h-6"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="1.5"
                        d="M6 18L18 6M6 6l12 12" />
                </svg>

            </div>

        </div>

    </div>

</div>



{{-- ============================================================
    INFORMASI AKUN
============================================================ --}}
<div
    class="bg-white border border-slate-200
           rounded-xl shadow-sm mb-8">

    <div class="px-6 py-5 border-b border-slate-200">

        <h2 class="font-semibold text-slate-800">
            Sistem Perizinan Karyawan
        </h2>

        <p class="text-sm text-slate-500 mt-1">
            Informasi akun yang sedang digunakan.
        </p>

    </div>


    <div class="p-6">

        <div class="grid sm:grid-cols-3 gap-6">

            {{-- Nama --}}
            <div>

                <p
                    class="text-xs font-medium uppercase
                           tracking-wide text-slate-400">
                    Nama
                </p>

                <p class="mt-1 font-medium text-slate-700">
                    {{ $user->name }}
                </p>

            </div>


            {{-- Email --}}
            <div>

                <p
                    class="text-xs font-medium uppercase
                           tracking-wide text-slate-400">
                    Email
                </p>

                <p class="mt-1 font-medium text-slate-700">
                    {{ $user->email }}
                </p>

            </div>


            {{-- Role --}}
            <div>

                <p
                    class="text-xs font-medium uppercase
                           tracking-wide text-slate-400">
                    Hak Akses
                </p>

                <div class="mt-1">

                    <span
                        class="inline-flex px-2.5 py-1
                               rounded-full bg-blue-50
                               text-blue-700
                               text-xs font-medium capitalize">
                        {{ $user->role }}
                    </span>

                </div>

            </div>

        </div>

    </div>

</div>



{{-- ============================================================
    PENGAJUAN CUTI TERBARU
============================================================ --}}
<div
    class="overflow-hidden rounded-xl
           border border-slate-200
           bg-white shadow-sm">

    {{-- Header --}}
    <div
        class="flex flex-col gap-3
               sm:flex-row sm:items-center
               sm:justify-between
               border-b border-slate-200
               px-6 py-5">

        <div>

            <h2 class="font-semibold text-slate-800">
                Pengajuan Cuti Terbaru
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Lima pengajuan cuti terbaru dari karyawan.
            </p>

        </div>


        <a
            href="{{ route('admin.leave-requests.index') }}"
            class="text-sm font-medium
                   text-blue-600 hover:text-blue-700">
            Lihat Semua
        </a>

    </div>



    {{-- Table --}}
    <div class="overflow-x-auto">

        <table class="min-w-full divide-y divide-slate-200">

            <thead class="bg-slate-50">

                <tr>

                    <th
                        class="px-6 py-3 text-left
                               text-xs font-semibold
                               uppercase tracking-wider
                               text-slate-500">
                        Karyawan
                    </th>

                    <th
                        class="px-6 py-3 text-left
                               text-xs font-semibold
                               uppercase tracking-wider
                               text-slate-500">
                        Bidang
                    </th>

                    <th
                        class="px-6 py-3 text-left
                               text-xs font-semibold
                               uppercase tracking-wider
                               text-slate-500">
                        Tanggal
                    </th>

                    <th
                        class="px-6 py-3 text-left
                               text-xs font-semibold
                               uppercase tracking-wider
                               text-slate-500">
                        Hari
                    </th>

                    <th
                        class="px-6 py-3 text-left
                               text-xs font-semibold
                               uppercase tracking-wider
                               text-slate-500">
                        Status
                    </th>

                    <th
                        class="px-6 py-3 text-right
                               text-xs font-semibold
                               uppercase tracking-wider
                               text-slate-500">
                        Aksi
                    </th>

                </tr>

            </thead>


            <tbody class="divide-y divide-slate-200">

                @forelse($recentLeaveRequests as $leave)

                <tr class="transition hover:bg-slate-50">

                    {{-- Karyawan --}}
                    <td class="px-6 py-4">

                        <div class="flex items-center gap-3">

                            <div
                                class="flex h-9 w-9
                                           items-center justify-center
                                           rounded-full bg-blue-100
                                           text-sm font-semibold
                                           text-blue-700">
                                {{ strtoupper(substr($leave->user->name, 0, 1)) }}
                            </div>


                            <div>

                                <p
                                    class="text-sm font-semibold
                                               text-slate-800">
                                    {{ $leave->user->name }}
                                </p>

                                <p class="text-xs text-slate-400">
                                    {{ $leave->user->nik ?? '-' }}
                                </p>

                            </div>

                        </div>

                    </td>


                    {{-- Bidang --}}
                    <td
                        class="whitespace-nowrap
                                   px-6 py-4 text-sm
                                   text-slate-600">
                        {{ $leave->user->department?->name ?? '-' }}
                    </td>


                    {{-- Tanggal --}}
                    <td
                        class="whitespace-nowrap
                                   px-6 py-4 text-sm
                                   text-slate-600">

                        {{ $leave->start_date->format('d/m/Y') }}

                        @if(
                        $leave->start_date->format('Y-m-d')
                        !==
                        $leave->end_date->format('Y-m-d')
                        )

                        -

                        {{ $leave->end_date->format('d/m/Y') }}

                        @endif

                    </td>


                    {{-- Total Hari --}}
                    <td
                        class="whitespace-nowrap
                                   px-6 py-4 text-sm
                                   text-slate-600">
                        {{ $leave->total_days }} hari
                    </td>


                    {{-- Status --}}
                    <td class="whitespace-nowrap px-6 py-4">

                        @if($leave->status === 'pending')

                        <span
                            class="inline-flex rounded-full
                                           bg-amber-50 px-2.5 py-1
                                           text-xs font-medium
                                           text-amber-700">
                            Menunggu
                        </span>


                        @elseif($leave->status === 'approved')

                        <span
                            class="inline-flex rounded-full
                                           bg-emerald-50 px-2.5 py-1
                                           text-xs font-medium
                                           text-emerald-700">
                            Disetujui
                        </span>


                        @else

                        <span
                            class="inline-flex rounded-full
                                           bg-red-50 px-2.5 py-1
                                           text-xs font-medium
                                           text-red-700">
                            Ditolak
                        </span>

                        @endif

                    </td>


                    {{-- Detail --}}
                    <td
                        class="whitespace-nowrap
                                   px-6 py-4 text-right">

                        <a
                            href="{{ route(
                                    'admin.leave-requests.show',
                                    $leave
                                ) }}"
                            class="inline-flex rounded-lg
                                       bg-blue-50 px-3 py-2
                                       text-xs font-medium
                                       text-blue-700
                                       hover:bg-blue-100">
                            Detail
                        </a>

                    </td>

                </tr>


                @empty

                <tr>

                    <td
                        colspan="6"
                        class="px-6 py-12
                                   text-center text-sm
                                   text-slate-500">
                        Belum ada pengajuan cuti.
                    </td>

                </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>

@endsection