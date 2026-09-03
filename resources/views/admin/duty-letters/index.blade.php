@extends('layouts.admin')

@section('title', 'Surat Dinas')
@section('page-title', 'Surat Dinas')

@section('content')
<div class="space-y-6">

    <x-toast-notification />

    {{-- ============================================================
        HEADER
    ============================================================ --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">
                Surat Dinas
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Kelola surat dinas, penerima tugas, laporan kegiatan, dan status pembayaran fee.
            </p>
        </div>

        <a
            href="{{ route('admin.duty-letters.create') }}"
            class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Buat Surat Dinas
        </a>
    </div>


    {{-- ============================================================
        STATISTIC CARDS
    ============================================================ --}}
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">

        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-sm font-medium text-slate-500">Surat Aktif</p>
                    <p class="mt-2 text-3xl font-bold text-slate-900">{{ $publishedCount }}</p>
                </div>

                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-50 text-blue-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 3h7l5 5v13H7a2 2 0 01-2-2V5a2 2 0 012-2zM14 3v6h6" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-sm font-medium text-slate-500">Kegiatan Mendatang</p>
                    <p class="mt-2 text-3xl font-bold text-slate-900">{{ $upcomingCount }}</p>
                </div>

                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3M5 11h14M5 5h14a2 2 0 012 2v12a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-sm font-medium text-slate-500">Belum Ada Laporan</p>
                    <p class="mt-2 text-3xl font-bold text-slate-900">{{ $waitingReportCount }}</p>
                </div>

                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-50 text-amber-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6M9 16h6M9 8h6M5 4h14a2 2 0 012 2v14H3V6a2 2 0 012-2z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-sm font-medium text-slate-500">Dibatalkan</p>
                    <p class="mt-2 text-3xl font-bold text-slate-900">{{ $cancelledCount }}</p>
                </div>

                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-red-50 text-red-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
            </div>
        </div>

    </div>


    {{-- ============================================================
        LIST CARD
    ============================================================ --}}
    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">

        {{-- Search / Filter --}}
        <div class="border-b border-slate-200 p-5">
            <form method="GET" class="flex flex-col gap-3 lg:flex-row lg:items-center">
                <div class="relative flex-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M11 18a7 7 0 100-14 7 7 0 000 14z" />
                    </svg>

                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari nomor surat, kegiatan, lokasi, penyelenggara, atau penerima..."
                        class="w-full rounded-lg border-slate-300 pl-10 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <select
                    name="status"
                    class="rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500 lg:w-52">
                    <option value="">Semua Status Surat</option>
                    <option value="published" @selected(request('status')==='published' )>
                        Diterbitkan
                    </option>
                    <option value="cancelled" @selected(request('status')==='cancelled' )>
                        Dibatalkan
                    </option>
                </select>

                <button
                    type="submit"
                    class="rounded-lg bg-slate-800 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-900">
                    Cari
                </button>

                @if(request('search') || request('status'))
                <a
                    href="{{ route('admin.duty-letters.index') }}"
                    class="rounded-lg border border-slate-300 px-5 py-2.5 text-center text-sm font-medium text-slate-600 transition hover:bg-slate-50">
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
                        <th class="whitespace-nowrap px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                            Surat / Kegiatan
                        </th>
                        <th class="whitespace-nowrap px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                            Tanggal & Waktu
                        </th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                            Lokasi
                        </th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                            Ditugaskan
                        </th>
                        <th class="whitespace-nowrap px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                            Status
                        </th>
                        <th class="whitespace-nowrap px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">
                            Aksi
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-200 bg-white">
                    @forelse($dutyLetters as $dutyLetter)
                    <tr class="align-top transition hover:bg-slate-50/70">

                        <td class="min-w-[260px] px-5 py-4">
                            <p class="text-sm font-semibold text-slate-800">
                                {{ $dutyLetter->title }}
                            </p>

                            <div class="mt-1 space-y-0.5 text-xs text-slate-500">
                                <p>
                                    No. Surat:
                                    <span class="font-medium text-slate-600">
                                        {{ $dutyLetter->letter_number ?: '-' }}
                                    </span>
                                </p>

                                @if($dutyLetter->organizer)
                                <p>
                                    Penyelenggara: {{ $dutyLetter->organizer }}
                                </p>
                                @endif
                            </div>
                        </td>

                        <td class="whitespace-nowrap px-5 py-4">
                            <p class="text-sm font-medium text-slate-700">
                                {{ $dutyLetter->event_date?->format('d/m/Y') }}
                            </p>

                            <p class="mt-1 text-xs text-slate-500">
                                {{ substr((string) $dutyLetter->start_time, 0, 5) }}
                                @if($dutyLetter->end_time)
                                - {{ substr((string) $dutyLetter->end_time, 0, 5) }}
                                @endif
                                WIB
                            </p>
                        </td>

                        <td class="min-w-[220px] px-5 py-4">
                            <p class="text-sm font-medium text-slate-700">
                                {{ $dutyLetter->location_name }}
                            </p>

                            @if($dutyLetter->location_address)
                            <p class="mt-1 line-clamp-2 text-xs leading-relaxed text-slate-500">
                                {{ $dutyLetter->location_address }}
                            </p>
                            @endif
                        </td>

                        <td class="min-w-[250px] px-5 py-4">
                            <div class="flex items-center gap-2">
                                <span class="rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700">
                                    {{ $dutyLetter->assignments_count }} orang
                                </span>
                            </div>

                            <div class="mt-2 flex flex-wrap gap-1.5">
                                @foreach($dutyLetter->assignments->take(3) as $assignment)
                                <span class="rounded-full border border-slate-200 bg-slate-50 px-2.5 py-1 text-xs text-slate-600">
                                    {{ $assignment->assignee_name }}
                                </span>
                                @endforeach

                                @if($dutyLetter->assignments_count > 3)
                                <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-500">
                                    +{{ $dutyLetter->assignments_count - 3 }} lainnya
                                </span>
                                @endif
                            </div>
                        </td>

                        <td class="whitespace-nowrap px-5 py-4">
                            @if($dutyLetter->isCancelled())
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-red-50 px-2.5 py-1 text-xs font-semibold text-red-700">
                                <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>
                                Dibatalkan
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">
                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                Diterbitkan
                            </span>
                            @endif
                        </td>

                        <td class="whitespace-nowrap px-5 py-4 text-right">
                            <div class="inline-flex items-center gap-2">
                                @if($dutyLetter->isPublished() && $dutyLetter->event_date?->isAfter(today()))
                                <a
                                    href="{{ route('admin.duty-letters.edit', $dutyLetter) }}"
                                    class="inline-flex items-center justify-center rounded-lg bg-blue-50 px-3 py-2 text-xs font-semibold text-blue-700 transition hover:bg-blue-100">
                                    Edit
                                </a>
                                @endif

                                <a
                                    href="{{ route('admin.duty-letters.show', $dutyLetter) }}"
                                    class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-50">
                                    Detail
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-14 text-center">
                            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M7 3h7l5 5v13H7a2 2 0 01-2-2V5a2 2 0 012-2zM14 3v6h6" />
                                </svg>
                            </div>

                            <h3 class="mt-4 text-sm font-semibold text-slate-700">
                                Belum ada surat dinas
                            </h3>

                            <p class="mt-1 text-sm text-slate-500">
                                Buat surat dinas pertama dan pilih pegawai yang ditugaskan.
                            </p>

                            <a
                                href="{{ route('admin.duty-letters.create') }}"
                                class="mt-4 inline-flex rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700">
                                Buat Surat Dinas
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($dutyLetters->hasPages())
        <div class="border-t border-slate-200 px-5 py-4">
            {{ $dutyLetters->links() }}
        </div>
        @endif

    </div>
</div>
@endsection