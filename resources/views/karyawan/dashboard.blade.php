@extends('layouts.karyawan')

@section('title', 'Dashboard Karyawan')

@section('page-title', 'Dashboard')

@section('content')

<div class="space-y-7">

    {{-- ============================================================
        WELCOME
    ============================================================ --}}
    <div
        class="flex flex-col gap-4
               sm:flex-row sm:items-center sm:justify-between">

        <div>

            <h1
                class="text-2xl lg:text-3xl
                       font-bold text-slate-800">
                Selamat datang, {{ $user->name }}
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Kelola pengajuan cuti dan perizinan Anda melalui sistem.
            </p>

        </div>


        <a
            href="{{ route('karyawan.leave-requests.create') }}"
            class="inline-flex items-center justify-center
                   gap-2 rounded-lg bg-blue-600
                   px-4 py-2.5 text-sm font-medium
                   text-white transition hover:bg-blue-700">

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

            Ajukan Cuti

        </a>

    </div>



    {{-- ============================================================
        STATISTIC CARDS
    ============================================================ --}}
    <div
        class="grid grid-cols-1
               gap-5 sm:grid-cols-2
               xl:grid-cols-4">

        {{-- Sisa Cuti --}}
        <div
            class="rounded-xl border
                   border-slate-200
                   bg-white p-5 shadow-sm">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-sm text-slate-500">
                        Sisa Cuti
                    </p>


                    <h2
                        class="mt-2 text-3xl
                               font-bold text-slate-800">
                        {{ $remainingLeave }}
                    </h2>


                    <p class="mt-1 text-xs text-slate-400">
                        Hari • {{ $year }}
                    </p>

                </div>


                <div
                    class="flex h-12 w-12
                           items-center justify-center
                           rounded-xl bg-blue-50
                           text-blue-600">

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-6 w-6"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.5"
                            d="M8 7V3m8 4V3M5 11h14
                               M5 5h14a2 2 0 012 2v12
                               a2 2 0 01-2 2H5
                               a2 2 0 01-2-2V7
                               a2 2 0 012-2z" />
                    </svg>

                </div>

            </div>

        </div>



        {{-- Pending --}}
        <div
            class="rounded-xl border
                   border-slate-200
                   bg-white p-5 shadow-sm">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-sm text-slate-500">
                        Menunggu Persetujuan
                    </p>

                    <h2
                        class="mt-2 text-3xl
                               font-bold text-slate-800">
                        {{ $pendingCount }}
                    </h2>

                    <p class="mt-1 text-xs text-slate-400">
                        Pengajuan
                    </p>

                </div>


                <div
                    class="flex h-12 w-12
                           items-center justify-center
                           rounded-xl bg-amber-50
                           text-amber-600">

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-6 w-6"
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



        {{-- Approved --}}
        <div
            class="rounded-xl border
                   border-slate-200
                   bg-white p-5 shadow-sm">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-sm text-slate-500">
                        Cuti Disetujui
                    </p>

                    <h2
                        class="mt-2 text-3xl
                               font-bold text-slate-800">
                        {{ $approvedCount }}
                    </h2>

                    <p class="mt-1 text-xs text-slate-400">
                        Pengajuan
                    </p>

                </div>


                <div
                    class="flex h-12 w-12
                           items-center justify-center
                           rounded-xl bg-emerald-50
                           text-emerald-600">

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-6 w-6"
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



        {{-- Rejected --}}
        <div
            class="rounded-xl border
                   border-slate-200
                   bg-white p-5 shadow-sm">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-sm text-slate-500">
                        Cuti Ditolak
                    </p>

                    <h2
                        class="mt-2 text-3xl
                               font-bold text-slate-800">
                        {{ $rejectedCount }}
                    </h2>

                    <p class="mt-1 text-xs text-slate-400">
                        Pengajuan
                    </p>

                </div>


                <div
                    class="flex h-12 w-12
                           items-center justify-center
                           rounded-xl bg-red-50
                           text-red-600">

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-6 w-6"
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
        INFORMASI KARYAWAN
    ============================================================ --}}
    <div
        class="overflow-hidden rounded-xl
               border border-slate-200
               bg-white shadow-sm">

        <div
            class="border-b border-slate-200
                   px-6 py-5">

            <h2 class="font-semibold text-slate-800">
                Informasi Karyawan
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Informasi akun dan bidang Anda.
            </p>

        </div>


        <div class="p-6">

            <div
                class="grid grid-cols-1
                       gap-6 sm:grid-cols-2
                       xl:grid-cols-4">

                {{-- Nama --}}
                <div>

                    <p
                        class="text-xs font-medium
                               uppercase tracking-wide
                               text-slate-400">
                        Nama
                    </p>

                    <p class="mt-1 font-medium text-slate-700">
                        {{ $user->name }}
                    </p>

                </div>


                {{-- NIK --}}
                <div>

                    <p
                        class="text-xs font-medium
                               uppercase tracking-wide
                               text-slate-400">
                        NIK
                    </p>

                    <p class="mt-1 font-medium text-slate-700">
                        {{ $user->nik ?? '-' }}
                    </p>

                </div>


                {{-- Bidang --}}
                <div>

                    <p
                        class="text-xs font-medium
                               uppercase tracking-wide
                               text-slate-400">
                        Bidang
                    </p>

                    <p class="mt-1 font-medium text-slate-700">
                        {{ $user->department?->name ?? '-' }}
                    </p>

                </div>


                {{-- Whatsapp --}}
                <div>

                    <p
                        class="text-xs font-medium
                               uppercase tracking-wide
                               text-slate-400">
                        WhatsApp
                    </p>

                    <p class="mt-1 font-medium text-slate-700">
                        {{ $user->whatsapp ?? '-' }}
                    </p>

                </div>

            </div>

        </div>

    </div>



    {{-- ============================================================
        PENGAJUAN TERAKHIR
    ============================================================ --}}
    <div
        class="overflow-hidden rounded-xl
               border border-slate-200
               bg-white shadow-sm">

        <div
            class="flex items-center justify-between
                   border-b border-slate-200
                   px-6 py-5">

            <div>

                <h2 class="font-semibold text-slate-800">
                    Pengajuan Terakhir
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Lima pengajuan cuti terbaru Anda.
                </p>

            </div>


            <a
                href="{{ route('karyawan.leave-requests.index') }}"
                class="text-sm font-medium
                       text-blue-600 hover:text-blue-700">
                Lihat Semua
            </a>

        </div>


        <div class="overflow-x-auto">

            <table class="min-w-full divide-y divide-slate-200">

                <thead class="bg-slate-50">

                    <tr>

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
                            Jumlah
                        </th>

                        <th
                            class="px-6 py-3 text-left
                                   text-xs font-semibold
                                   uppercase tracking-wider
                                   text-slate-500">
                            Pengganti
                        </th>

                        <th
                            class="px-6 py-3 text-left
                                   text-xs font-semibold
                                   uppercase tracking-wider
                                   text-slate-500">
                            Status
                        </th>

                    </tr>

                </thead>


                <tbody class="divide-y divide-slate-200">

                    @forelse($recentLeaveRequests as $leave)

                    <tr class="hover:bg-slate-50">

                        {{-- Date --}}
                        <td
                            class="whitespace-nowrap
                                       px-6 py-4 text-sm
                                       text-slate-700">

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


                        {{-- Days --}}
                        <td
                            class="whitespace-nowrap
                                       px-6 py-4 text-sm
                                       text-slate-700">
                            {{ $leave->total_days }} hari
                        </td>


                        {{-- Substitute --}}
                        <td
                            class="whitespace-nowrap
                                       px-6 py-4 text-sm
                                       text-slate-700">

                            {{ $leave->substitute_name }}

                            <p class="text-xs text-slate-400">
                                {{ $leave->substitute_whatsapp }}
                            </p>

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

                    </tr>

                    @empty

                    <tr>

                        <td
                            colspan="4"
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

</div>

@endsection