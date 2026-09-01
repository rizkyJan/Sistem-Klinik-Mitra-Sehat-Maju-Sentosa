@extends('layouts.kabid')

@section('title', 'Persetujuan Izin')
@section('page-title', 'Persetujuan Izin')

@section('content')

<x-toast-notification />

<div class="space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">

        <div>
            <h1 class="text-2xl font-bold text-slate-800">
                Persetujuan Izin
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Periksa pengajuan anggota bidang
                <span class="font-semibold text-slate-700">
                    {{ $kabid->department?->name ?? '-' }}
                </span>
                sebelum diteruskan ke Administrator.
            </p>
        </div>

        <div
            class="inline-flex w-fit items-center gap-2 rounded-lg
                   border border-blue-200 bg-blue-50 px-4 py-2.5
                   text-sm font-medium text-blue-700">

            Tahap 1 • Kabid
        </div>
    </div>


    {{-- WORKFLOW --}}
    <div
        class="rounded-xl border border-slate-200
               bg-white p-5 shadow-sm">

        <div class="grid gap-3 sm:grid-cols-3">

            <div class="rounded-lg border border-blue-200 bg-blue-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-blue-500">
                    1
                </p>

                <p class="mt-1 font-semibold text-blue-800">
                    Karyawan Mengajukan
                </p>
            </div>


            <div class="rounded-lg border border-amber-200 bg-amber-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-amber-500">
                    2
                </p>

                <p class="mt-1 font-semibold text-amber-800">
                    Kabid ACC / Tolak
                </p>
            </div>


            <div class="rounded-lg border border-slate-200 bg-slate-100 p-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                    3
                </p>

                <p class="mt-1 font-semibold text-slate-600">
                    Admin Final
                </p>
            </div>
        </div>
    </div>


    {{-- STATISTICS --}}
    <div class="grid gap-4 sm:grid-cols-3">

        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">
                Menunggu Saya
            </p>

            <p class="mt-2 text-3xl font-bold text-amber-600">
                {{ $pendingCount }}
            </p>
        </div>


        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">
                Sudah Saya Setujui
            </p>

            <p class="mt-2 text-3xl font-bold text-emerald-600">
                {{ $approvedCount }}
            </p>
        </div>


        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">
                Sudah Saya Tolak
            </p>

            <p class="mt-2 text-3xl font-bold text-red-600">
                {{ $rejectedCount }}
            </p>
        </div>
    </div>


    {{-- FILTER + LIST --}}
    <div
        class="overflow-hidden rounded-xl border
               border-slate-200 bg-white shadow-sm">

        <div class="border-b border-slate-200 p-5">

            <form
                method="GET"
                action="{{ route('kabid.leave-approvals.index') }}"
                class="grid gap-3 lg:grid-cols-[1fr_240px_auto_auto]">

                <div>
                    <label
                        for="search"
                        class="mb-1 block text-xs font-semibold
                               uppercase tracking-wide text-slate-500">
                        Cari
                    </label>

                    <input
                        type="text"
                        id="search"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Nama, NIK, jenis izin, alasan..."
                        class="w-full rounded-lg border-slate-300 text-sm
                               focus:border-blue-500 focus:ring-blue-500">
                </div>


                <div>
                    <label
                        for="kabid_status"
                        class="mb-1 block text-xs font-semibold
                               uppercase tracking-wide text-slate-500">
                        Status Kabid
                    </label>

                    <select
                        id="kabid_status"
                        name="kabid_status"
                        class="w-full rounded-lg border-slate-300 text-sm
                               focus:border-blue-500 focus:ring-blue-500">

                        <option value="">
                            Semua Status
                        </option>

                        <option
                            value="pending"
                            @selected($kabidStatus==='pending' )>
                            Menunggu Saya
                        </option>

                        <option
                            value="approved"
                            @selected($kabidStatus==='approved' )>
                            Sudah Disetujui
                        </option>

                        <option
                            value="rejected"
                            @selected($kabidStatus==='rejected' )>
                            Ditolak
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

                    @if($search !== '' || $kabidStatus)

                    <a
                        href="{{ route('kabid.leave-approvals.index') }}"
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


        {{-- DESKTOP --}}
        <div class="hidden overflow-x-auto md:block">

            <table class="min-w-full divide-y divide-slate-200">

                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                            Karyawan
                        </th>

                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                            Jenis
                        </th>

                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                            Periode
                        </th>

                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                            Tahap Kabid
                        </th>

                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                            Status Final
                        </th>

                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">
                            Aksi
                        </th>
                    </tr>
                </thead>


                <tbody class="divide-y divide-slate-100">

                    @forelse($leaveRequests as $item)

                    <tr
                        class="{{
                                $item->status === 'pending'
                                && $item->kabid_status === 'pending'
                                    ? 'bg-amber-50/30'
                                    : 'bg-white'
                            }}">

                        <td class="px-5 py-4">

                            <p class="font-semibold text-slate-800">
                                {{ $item->user?->name ?? '-' }}
                            </p>

                            <p class="mt-1 text-xs text-slate-500">
                                {{ $item->user?->nik ?? '-' }}
                                •
                                {{ $item->user?->department?->name ?? '-' }}
                            </p>
                        </td>


                        <td class="px-5 py-4">

                            <p class="text-sm font-medium text-slate-800">
                                {{ $item->permissionType?->name ?? '-' }}
                            </p>

                            <p class="mt-1 text-xs text-slate-500">
                                {{ $item->total_days }} hari
                            </p>
                        </td>


                        <td class="px-5 py-4 text-sm text-slate-700">
                            {{ $item->start_date?->format('d/m/Y') }}
                            <br>
                            <span class="text-xs text-slate-400">
                                s/d
                            </span>
                            <br>
                            {{ $item->end_date?->format('d/m/Y') }}
                        </td>


                        <td class="px-5 py-4">

                            @if($item->kabid_status === 'pending')

                            <span
                                class="inline-flex rounded-full
                                               bg-amber-50 px-2.5 py-1
                                               text-xs font-semibold
                                               text-amber-700">
                                Menunggu Saya
                            </span>

                            @elseif($item->kabid_status === 'approved')

                            <span
                                class="inline-flex rounded-full
                                               bg-emerald-50 px-2.5 py-1
                                               text-xs font-semibold
                                               text-emerald-700">
                                Disetujui
                            </span>

                            @elseif($item->kabid_status === 'rejected')

                            <span
                                class="inline-flex rounded-full
                                               bg-red-50 px-2.5 py-1
                                               text-xs font-semibold
                                               text-red-700">
                                Ditolak
                            </span>

                            @else

                            <span
                                class="inline-flex rounded-full
                                               bg-slate-100 px-2.5 py-1
                                               text-xs font-semibold
                                               text-slate-600">
                                Tidak Diperlukan
                            </span>

                            @endif
                        </td>


                        <td class="px-5 py-4">

                            @if($item->status === 'pending')

                            <span
                                class="inline-flex rounded-full
                                               bg-blue-50 px-2.5 py-1
                                               text-xs font-semibold
                                               text-blue-700">
                                Menunggu Admin
                            </span>

                            @elseif($item->status === 'approved')

                            <span
                                class="inline-flex rounded-full
                                               bg-emerald-50 px-2.5 py-1
                                               text-xs font-semibold
                                               text-emerald-700">
                                Disetujui
                            </span>

                            @else

                            <span
                                class="inline-flex rounded-full
                                               bg-red-50 px-2.5 py-1
                                               text-xs font-semibold
                                               text-red-700">
                                Ditolak
                            </span>

                            @endif
                        </td>


                        <td class="px-5 py-4 text-right">

                            <a
                                href="{{ route(
                                        'kabid.leave-approvals.show',
                                        $item
                                    ) }}"
                                class="{{
                                        $item->status === 'pending'
                                        && $item->kabid_status === 'pending'
                                            ? 'bg-blue-600 text-white hover:bg-blue-700'
                                            : 'bg-slate-100 text-slate-700 hover:bg-slate-200'
                                    }} inline-flex rounded-lg px-3 py-2
                                       text-xs font-semibold">
                                {{
                                        $item->status === 'pending'
                                        && $item->kabid_status === 'pending'
                                            ? 'Periksa'
                                            : 'Detail'
                                    }}
                            </a>
                        </td>
                    </tr>

                    @empty

                    <tr>
                        <td
                            colspan="6"
                            class="px-5 py-12 text-center
                                       text-sm text-slate-500">
                            Belum ada pengajuan anggota yang sesuai.
                        </td>
                    </tr>

                    @endforelse
                </tbody>
            </table>
        </div>


        {{-- MOBILE --}}
        <div class="divide-y divide-slate-100 md:hidden">

            @forelse($leaveRequests as $item)

            <div class="space-y-4 p-5">

                <div class="flex items-start justify-between gap-3">

                    <div>
                        <p class="font-semibold text-slate-800">
                            {{ $item->user?->name ?? '-' }}
                        </p>

                        <p class="mt-1 text-xs text-slate-500">
                            {{ $item->permissionType?->name ?? '-' }}
                            • {{ $item->total_days }} hari
                        </p>
                    </div>


                    @if(
                    $item->status === 'pending'
                    && $item->kabid_status === 'pending'
                    )

                    <span
                        class="shrink-0 rounded-full
                                       bg-amber-50 px-2.5 py-1
                                       text-xs font-semibold text-amber-700">
                        Menunggu Saya
                    </span>

                    @elseif($item->kabid_status === 'approved')

                    <span
                        class="shrink-0 rounded-full
                                       bg-emerald-50 px-2.5 py-1
                                       text-xs font-semibold text-emerald-700">
                        ACC Kabid
                    </span>

                    @else

                    <span
                        class="shrink-0 rounded-full
                                       bg-red-50 px-2.5 py-1
                                       text-xs font-semibold text-red-700">
                        Ditolak
                    </span>

                    @endif
                </div>


                <div class="grid grid-cols-2 gap-3 text-sm">

                    <div>
                        <p class="text-xs text-slate-400">
                            Mulai
                        </p>

                        <p class="mt-1 text-slate-700">
                            {{ $item->start_date?->format('d/m/Y') }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-slate-400">
                            Selesai
                        </p>

                        <p class="mt-1 text-slate-700">
                            {{ $item->end_date?->format('d/m/Y') }}
                        </p>
                    </div>
                </div>


                <a
                    href="{{ route(
                            'kabid.leave-approvals.show',
                            $item
                        ) }}"
                    class="block rounded-lg bg-blue-50
                               px-4 py-2.5 text-center
                               text-sm font-semibold text-blue-700
                               hover:bg-blue-100">
                    Lihat Detail
                </a>
            </div>

            @empty

            <div class="p-10 text-center text-sm text-slate-500">
                Belum ada pengajuan anggota yang sesuai.
            </div>

            @endforelse
        </div>


        @if($leaveRequests->hasPages())

        <div class="border-t border-slate-200 p-5">
            {{ $leaveRequests->links() }}
        </div>

        @endif
    </div>
</div>

@endsection