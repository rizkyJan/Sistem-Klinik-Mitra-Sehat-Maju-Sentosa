@extends('layouts.kabid')

@section('title', 'Surat Dinas Saya')
@section('page-title', 'Surat Dinas Saya')

@section('content')

<x-toast-notification />

<div class="space-y-6">

    {{-- PAGE HEADER --}}
    <div>
        <h1 class="text-2xl font-bold text-slate-800">
            Surat Dinas Saya
        </h1>

        <p class="mt-1 text-sm text-slate-500">
            Lihat surat tugas, jadwal kegiatan, lokasi, dan status laporan dinas Anda.
        </p>
    </div>


    {{-- STATISTICS --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">

        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-sm text-slate-500">Total Surat</p>
                    <p class="mt-2 text-3xl font-bold text-slate-800">{{ $totalCount }}</p>
                    <p class="mt-1 text-xs text-slate-400">Semua penugasan Anda</p>
                </div>
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 3h7l5 5v13H7V3zM14 3v5h5M10 13h6M10 17h6" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-sm text-slate-500">Akan Datang</p>
                    <p class="mt-2 text-3xl font-bold text-blue-600">{{ $upcomingCount }}</p>
                    <p class="mt-1 text-xs text-slate-400">Jadwal hari ini / berikutnya</p>
                </div>
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3M5 11h14M5 5h14a2 2 0 012 2v12a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-sm text-slate-500">Belum Ada Laporan</p>
                    <p class="mt-2 text-3xl font-bold text-amber-600">{{ $pendingReportCount }}</p>
                    <p class="mt-1 text-xs text-slate-400">Menunggu laporan hasil dinas</p>
                </div>
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6M9 16h6M9 8h3M5 4h14v16H5V4z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-sm text-slate-500">Laporan Selesai</p>
                    <p class="mt-2 text-3xl font-bold text-emerald-600">{{ $verifiedReportCount }}</p>
                    <p class="mt-1 text-xs text-slate-400">Sudah diverifikasi Admin</p>
                </div>
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>


    {{-- FILTER & LIST --}}
    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 p-5">
            <form method="GET" action="{{ route('kabid.duty-letters.index') }}" class="grid gap-3 lg:grid-cols-[1fr_220px_auto] lg:items-end">
                <div>
                    <label for="search" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                        Cari Surat
                    </label>
                    <input
                        id="search"
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Nomor surat, kegiatan, penyelenggara, lokasi..."
                        class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label for="filter" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                        Filter
                    </label>
                    <select id="filter" name="filter" class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Semua Surat</option>
                        <option value="upcoming" @selected(request('filter')==='upcoming' )>Akan Datang</option>
                        <option value="past" @selected(request('filter')==='past' )>Sudah Lewat</option>
                        <option value="cancelled" @selected(request('filter')==='cancelled' )>Dibatalkan</option>
                        <option value="report_pending" @selected(request('filter')==='report_pending' )>Belum Ada Laporan</option>
                        <option value="report_revision" @selected(request('filter')==='report_revision' )>Perlu Perbaikan</option>
                        <option value="report_verified" @selected(request('filter')==='report_verified' )>Laporan Diverifikasi</option>
                    </select>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="rounded-lg bg-slate-800 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-slate-700">
                        Tampilkan
                    </button>

                    @if(request()->filled('search') || request()->filled('filter'))
                    <a href="{{ route('kabid.duty-letters.index') }}" class="rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-slate-50">
                        Reset
                    </a>
                    @endif
                </div>
            </form>
        </div>

        @if($assignments->count())
        <div class="divide-y divide-slate-200">
            @foreach($assignments as $assignment)
            @php
            $letter = $assignment->dutyLetter;
            $isCancelled = $letter->status === \App\Models\DutyLetter::STATUS_CANCELLED;
            $isUpcoming = ! $isCancelled && ($letter->event_date->isToday() || $letter->event_date->isFuture());
            @endphp

            <div class="p-5 sm:p-6">
                <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            @if($isCancelled)
                            <span class="inline-flex rounded-full bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-700">
                                Dibatalkan
                            </span>
                            @elseif($isUpcoming)
                            <span class="inline-flex rounded-full bg-blue-100 px-2.5 py-1 text-xs font-semibold text-blue-700">
                                Akan Datang
                            </span>
                            @else
                            <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600">
                                Sudah Lewat
                            </span>
                            @endif

                            @php
                            $reportClass = match($assignment->report_status) {
                            \App\Models\DutyAssignment::REPORT_SUBMITTED => 'bg-blue-100 text-blue-700',
                            \App\Models\DutyAssignment::REPORT_REVISION => 'bg-red-100 text-red-700',
                            \App\Models\DutyAssignment::REPORT_VERIFIED => 'bg-emerald-100 text-emerald-700',
                            default => 'bg-amber-100 text-amber-700',
                            };
                            @endphp

                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $reportClass }}">
                                {{ $assignment->report_status_label }}
                            </span>
                        </div>

                        <h2 class="mt-3 break-words text-lg font-bold text-slate-800">
                            {{ $letter->title }}
                        </h2>

                        <p class="mt-1 text-sm text-slate-500">
                            {{ $letter->letter_number ?: 'Nomor surat tidak dicantumkan' }}
                        </p>

                        <div class="mt-4 grid gap-3 text-sm text-slate-600 sm:grid-cols-2 xl:grid-cols-3">
                            <div class="flex items-start gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="mt-0.5 h-4 w-4 shrink-0 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3M5 11h14M5 5h14a2 2 0 012 2v12a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2z" />
                                </svg>
                                <span>{{ $letter->event_date->format('d/m/Y') }} • {{ substr((string) $letter->start_time, 0, 5) }} WIB</span>
                            </div>

                            <div class="flex items-start gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="mt-0.5 h-4 w-4 shrink-0 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 21s7-4.35 7-11a7 7 0 10-14 0c0 6.65 7 11 7 11zM12 12a2 2 0 100-4 2 2 0 000 4z" />
                                </svg>
                                <span class="break-words">{{ $letter->location_name }}</span>
                            </div>

                            <div class="flex items-start gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="mt-0.5 h-4 w-4 shrink-0 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 4h14v16H5zM8 8h8M8 12h8" />
                                </svg>
                                <span class="break-words">{{ $letter->organizer ?: '-' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex shrink-0 flex-col gap-2 sm:flex-row xl:flex-col">
                        <a href="{{ route('kabid.duty-letters.show', $assignment) }}" class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700">
                            Lihat Detail
                        </a>

                        <a href="{{ route('kabid.duty-letters.pdf', $assignment) }}" target="_blank" rel="noopener" class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                            Lihat PDF
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="border-t border-slate-200 px-5 py-4">
            {{ $assignments->links() }}
        </div>
        @else
        <div class="px-6 py-14 text-center">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-slate-100 text-slate-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 3h7l5 5v13H7V3zM14 3v5h5" />
                </svg>
            </div>

            <h3 class="mt-4 font-semibold text-slate-800">
                Belum ada Surat Dinas
            </h3>

            <p class="mx-auto mt-1 max-w-md text-sm text-slate-500">
                Surat yang ditugaskan Admin kepada Anda akan otomatis muncul di halaman ini.
            </p>
        </div>
        @endif
    </div>
</div>

@endsection