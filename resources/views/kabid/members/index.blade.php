@extends('layouts.kabid')

@section('title', 'Anggota Saya')
@section('page-title', 'Anggota Saya')

@section('content')

<div class="space-y-6">

    {{-- ============================================================
        PAGE HEADER
    ============================================================ --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">

        <div>
            <h1 class="text-2xl font-bold text-slate-800">
                Anggota Saya
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Daftar karyawan yang berada dalam bidang yang sama dengan Anda.
            </p>
        </div>


        <div
            class="inline-flex w-fit items-center gap-2 rounded-lg
                   border border-blue-200 bg-blue-50 px-4 py-2.5
                   text-sm font-medium text-blue-700">

            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-4 w-4 shrink-0"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor">

                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M3 21h18M5 21V7l7-4 7 4v14M9 9h6m-6 4h6m-6 4h6" />
            </svg>

            <span>
                {{ $kabid->department?->name ?? 'Bidang belum ditentukan' }}
            </span>
        </div>
    </div>


    {{-- ============================================================
        WARNING IF NO DEPARTMENT
    ============================================================ --}}
    @if(! $kabid->department_id)

    <div
        class="rounded-xl border border-amber-200
                   bg-amber-50 p-5 text-amber-800">

        <p class="font-semibold">
            Bidang Kabid belum ditentukan.
        </p>

        <p class="mt-1 text-sm leading-6 text-amber-700">
            Daftar anggota belum dapat ditampilkan.
            Hubungi Administrator untuk menentukan bidang Kabid terlebih dahulu.
        </p>
    </div>

    @else

    {{-- ============================================================
            INFO
        ============================================================ --}}
    <div
        class="rounded-xl border border-slate-200
                   bg-white p-5 shadow-sm">

        <div class="flex items-start gap-3">

            <div
                class="flex h-9 w-9 shrink-0 items-center
                           justify-center rounded-lg bg-slate-100
                           text-slate-600">

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
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>


            <div>
                <p class="font-semibold text-slate-800">
                    Anggota bidang {{ $kabid->department?->name }}
                </p>

                <p class="mt-1 text-sm leading-6 text-slate-500">
                    Halaman ini hanya menampilkan akun Karyawan yang sudah
                    diverifikasi Administrator dan memiliki bidang yang sama
                    dengan Kabid.
                </p>
            </div>
        </div>
    </div>


    {{-- ============================================================
            STATISTICS
        ============================================================ --}}
    <div class="grid gap-4 sm:grid-cols-3">

        <div
            class="rounded-xl border border-slate-200
                       bg-white p-5 shadow-sm">

            <p class="text-sm text-slate-500">
                Total Anggota
            </p>

            <p class="mt-2 text-3xl font-bold text-slate-800">
                {{ $totalMembers }}
            </p>
        </div>


        <div
            class="rounded-xl border border-slate-200
                       bg-white p-5 shadow-sm">

            <p class="text-sm text-slate-500">
                Aktif
            </p>

            <p class="mt-2 text-3xl font-bold text-emerald-600">
                {{ $activeMembers }}
            </p>
        </div>


        <div
            class="rounded-xl border border-slate-200
                       bg-white p-5 shadow-sm">

            <p class="text-sm text-slate-500">
                Nonaktif
            </p>

            <p class="mt-2 text-3xl font-bold text-slate-500">
                {{ $inactiveMembers }}
            </p>
        </div>
    </div>


    {{-- ============================================================
            FILTER + TABLE
        ============================================================ --}}
    <div
        class="overflow-hidden rounded-xl border
                   border-slate-200 bg-white shadow-sm">

        {{-- FILTER --}}
        <div class="border-b border-slate-200 p-5">

            <form
                method="GET"
                action="{{ route('kabid.members.index') }}"
                class="grid gap-3 md:grid-cols-[1fr_220px_auto_auto]">

                <div>
                    <label
                        for="search"
                        class="mb-1 block text-xs font-semibold
                                   uppercase tracking-wide text-slate-500">
                        Cari Anggota
                    </label>

                    <input
                        type="text"
                        id="search"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Nama, NIK, email, atau WhatsApp..."
                        class="w-full rounded-lg border-slate-300 text-sm
                                   focus:border-blue-500 focus:ring-blue-500">
                </div>


                <div>
                    <label
                        for="status"
                        class="mb-1 block text-xs font-semibold
                                   uppercase tracking-wide text-slate-500">
                        Status Akun
                    </label>

                    <select
                        id="status"
                        name="status"
                        class="w-full rounded-lg border-slate-300 text-sm
                                   focus:border-blue-500 focus:ring-blue-500">

                        <option value="">
                            Semua Status
                        </option>

                        <option
                            value="active"
                            @selected($status==='active' )>
                            Aktif
                        </option>

                        <option
                            value="inactive"
                            @selected($status==='inactive' )>
                            Nonaktif
                        </option>
                    </select>
                </div>


                <div class="flex items-end">

                    <button
                        type="submit"
                        class="w-full rounded-lg bg-slate-800
                                   px-4 py-2.5 text-sm font-semibold
                                   text-white hover:bg-slate-700">
                        Tampilkan
                    </button>
                </div>


                <div class="flex items-end">

                    @if($search !== '' || $status)

                    <a
                        href="{{ route('kabid.members.index') }}"
                        class="w-full rounded-lg border border-slate-300
                                       bg-white px-4 py-2.5 text-center
                                       text-sm font-medium text-slate-600
                                       hover:bg-slate-50">
                        Reset
                    </a>

                    @endif
                </div>
            </form>
        </div>


        {{-- DESKTOP TABLE --}}
        <div class="hidden overflow-x-auto md:block">

            <table class="min-w-full divide-y divide-slate-200">

                <thead class="bg-slate-50">
                    <tr>
                        <th
                            class="px-5 py-3 text-left text-xs font-semibold
                                       uppercase tracking-wide text-slate-500">
                            Karyawan
                        </th>

                        <th
                            class="px-5 py-3 text-left text-xs font-semibold
                                       uppercase tracking-wide text-slate-500">
                            NIK
                        </th>

                        <th
                            class="px-5 py-3 text-left text-xs font-semibold
                                       uppercase tracking-wide text-slate-500">
                            Kontak
                        </th>

                        <th
                            class="px-5 py-3 text-left text-xs font-semibold
                                       uppercase tracking-wide text-slate-500">
                            Mulai Kerja
                        </th>

                        <th
                            class="px-5 py-3 text-left text-xs font-semibold
                                       uppercase tracking-wide text-slate-500">
                            Status
                        </th>
                    </tr>
                </thead>


                <tbody class="divide-y divide-slate-100 bg-white">

                    @forelse($members as $member)

                    <tr class="hover:bg-slate-50/70">

                        <td class="px-5 py-4">

                            <div class="flex items-center gap-3">

                                <div
                                    class="flex h-10 w-10 shrink-0
                                                   items-center justify-center
                                                   rounded-full bg-blue-50
                                                   font-semibold text-blue-700">

                                    {{ strtoupper(
                                                mb_substr(
                                                    $member->name,
                                                    0,
                                                    1
                                                )
                                            ) }}
                                </div>


                                <div class="min-w-0">

                                    <p class="font-semibold text-slate-800">
                                        {{ $member->name }}
                                    </p>

                                    <p class="mt-0.5 truncate text-xs text-slate-500">
                                        {{ $member->department?->name ?? '-' }}
                                    </p>
                                </div>
                            </div>
                        </td>


                        <td class="px-5 py-4 text-sm text-slate-700">
                            {{ $member->nik ?: '-' }}
                        </td>


                        <td class="px-5 py-4">

                            <p class="text-sm text-slate-700">
                                {{ $member->email }}
                            </p>

                            <p class="mt-1 text-xs text-slate-500">
                                {{ $member->whatsapp ?: '-' }}
                            </p>
                        </td>


                        <td class="px-5 py-4 text-sm text-slate-700">
                            {{ $member->join_date?->format('d/m/Y') ?? '-' }}
                        </td>


                        <td class="px-5 py-4">

                            @if($member->is_active)

                            <span
                                class="inline-flex rounded-full
                                                   bg-emerald-50 px-2.5 py-1
                                                   text-xs font-semibold
                                                   text-emerald-700">
                                Aktif
                            </span>

                            @else

                            <span
                                class="inline-flex rounded-full
                                                   bg-slate-100 px-2.5 py-1
                                                   text-xs font-semibold
                                                   text-slate-600">
                                Nonaktif
                            </span>

                            @endif
                        </td>
                    </tr>

                    @empty

                    <tr>
                        <td
                            colspan="5"
                            class="px-5 py-12 text-center text-sm
                                           text-slate-500">
                            Tidak ada anggota yang sesuai dengan filter.
                        </td>
                    </tr>

                    @endforelse
                </tbody>
            </table>
        </div>


        {{-- MOBILE CARDS --}}
        <div class="divide-y divide-slate-100 md:hidden">

            @forelse($members as $member)

            <div class="p-5">

                <div class="flex items-start gap-3">

                    <div
                        class="flex h-11 w-11 shrink-0 items-center
                                       justify-center rounded-full bg-blue-50
                                       font-semibold text-blue-700">

                        {{ strtoupper(
                                    mb_substr(
                                        $member->name,
                                        0,
                                        1
                                    )
                                ) }}
                    </div>


                    <div class="min-w-0 flex-1">

                        <div
                            class="flex flex-wrap items-start
                                           justify-between gap-2">

                            <div class="min-w-0">

                                <p class="font-semibold text-slate-800">
                                    {{ $member->name }}
                                </p>

                                <p class="mt-0.5 text-xs text-slate-500">
                                    NIK: {{ $member->nik ?: '-' }}
                                </p>
                            </div>


                            @if($member->is_active)

                            <span
                                class="rounded-full bg-emerald-50
                                                   px-2.5 py-1 text-xs
                                                   font-semibold text-emerald-700">
                                Aktif
                            </span>

                            @else

                            <span
                                class="rounded-full bg-slate-100
                                                   px-2.5 py-1 text-xs
                                                   font-semibold text-slate-600">
                                Nonaktif
                            </span>

                            @endif
                        </div>


                        <div class="mt-4 space-y-2 text-sm">

                            <div>
                                <p class="text-xs text-slate-400">
                                    Email
                                </p>

                                <p class="break-all text-slate-700">
                                    {{ $member->email }}
                                </p>
                            </div>


                            <div>
                                <p class="text-xs text-slate-400">
                                    WhatsApp
                                </p>

                                <p class="text-slate-700">
                                    {{ $member->whatsapp ?: '-' }}
                                </p>
                            </div>


                            <div>
                                <p class="text-xs text-slate-400">
                                    Mulai Kerja
                                </p>

                                <p class="text-slate-700">
                                    {{ $member->join_date?->format('d/m/Y') ?? '-' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @empty

            <div class="p-10 text-center text-sm text-slate-500">
                Tidak ada anggota yang sesuai dengan filter.
            </div>

            @endforelse
        </div>


        @if($members->hasPages())

        <div class="border-t border-slate-200 p-5">
            {{ $members->links() }}
        </div>

        @endif
    </div>
    @endif
</div>

@endsection