@extends('layouts.admin')

@section('title', 'Detail Surat Dinas')
@section('page-title', 'Detail Surat Dinas')

@section('content')
@php
$assignmentCount = $dutyLetter->assignments->count();
$verifiedCount = $dutyLetter->assignments
->where('report_status', \App\Models\DutyAssignment::REPORT_VERIFIED)
->count();
$paidCount = $dutyLetter->assignments
->where('fee_status', \App\Models\DutyAssignment::FEE_PAID)
->count();
$completedCount = $dutyLetter->assignments
->filter(
fn($assignment) =>
$assignment->report_status === \App\Models\DutyAssignment::REPORT_VERIFIED
&& $assignment->fee_status === \App\Models\DutyAssignment::FEE_PAID
)
->count();
$canEdit = $dutyLetter->isPublished()
&& $dutyLetter->event_date?->isAfter(today())
&& $dutyLetter->assignments->every(
fn($assignment) =>
$assignment->report_status === \App\Models\DutyAssignment::REPORT_PENDING
&& $assignment->fee_status === \App\Models\DutyAssignment::FEE_UNPAID
&& $assignment->report === null
);
@endphp
<div class="mx-auto max-w-7xl space-y-6">

    <x-toast-notification />

    {{-- ============================================================
        HEADER
    ============================================================ --}}
    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div>
            <a
                href="{{ route('admin.duty-letters.index') }}"
                class="inline-flex items-center gap-1.5 text-sm font-medium text-slate-500 transition hover:text-slate-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali ke Surat Dinas
            </a>

            <div class="mt-3 flex flex-wrap items-center gap-3">
                <h1 class="break-words text-xl font-bold text-slate-800 sm:text-2xl">
                    {{ $dutyLetter->title }}
                </h1>

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
            </div>

            <p class="mt-2 text-sm text-slate-500">
                No. Surat: <span class="font-medium text-slate-700">{{ $dutyLetter->letter_number ?: '-' }}</span>
            </p>
        </div>

        <div class="grid w-full gap-2 sm:w-auto sm:grid-cols-2 lg:flex lg:flex-row">
            @if($canEdit)
            <a
                href="{{ route('admin.duty-letters.edit', $dutyLetter) }}"
                class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 20h9M16.5 3.5a2.12 2.12 0 013 3L8 18l-4 1 1-4L16.5 3.5z" />
                </svg>
                Edit Surat
            </a>
            @endif

            <a
                href="{{ route('admin.duty-letters.pdf', $dutyLetter) }}"
                target="_blank"
                rel="noopener noreferrer"
                class="inline-flex items-center justify-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 3h7l5 5v13H7a2 2 0 01-2-2V5a2 2 0 012-2zM14 3v6h6" />
                </svg>
                Lihat PDF
            </a>

            @if($dutyLetter->isPublished())
            <form
                method="POST"
                action="{{ route('admin.duty-letters.cancel', $dutyLetter) }}"
                data-confirm
                data-confirm-title="Batalkan Surat Dinas?"
                data-confirm-message="Surat akan ditandai dibatalkan dan tidak dihapus dari histori. Pembatalan hanya bisa dilakukan jika belum ada laporan yang diproses."
                data-confirm-button="Ya, Batalkan Surat"
                data-confirm-tone="danger">
                @csrf
                @method('PATCH')

                <button
                    type="submit"
                    class="inline-flex w-full items-center justify-center gap-2 rounded-lg border border-red-200 bg-red-50 px-4 py-2.5 text-sm font-semibold text-red-700 transition hover:bg-red-100 sm:w-auto">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Batalkan Surat
                </button>
            </form>
            @endif
        </div>
    </div>

    @if($dutyLetter->isPublished() && ! $canEdit)
    <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600">
        <span class="font-semibold text-slate-700">Edit dikunci.</span>
        Surat hanya dapat diubah sebelum hari kegiatan dan selama belum ada laporan/pembayaran yang diproses.
    </div>
    @endif


    {{-- ============================================================
        INFORMATION GRID
    ============================================================ --}}
    <div class="grid gap-6 xl:grid-cols-[1.35fr_0.65fr]">

        <div class="space-y-6">

            {{-- Informasi kegiatan --}}
            <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-5 py-4 sm:px-6">
                    <h2 class="text-base font-semibold text-slate-800">
                        Informasi Kegiatan
                    </h2>
                </div>

                <div class="grid gap-x-8 gap-y-5 p-5 sm:grid-cols-2 sm:p-6">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Penyelenggara</p>
                        <p class="mt-1.5 text-sm font-medium text-slate-700">
                            {{ $dutyLetter->organizer ?: '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Tanggal</p>
                        <p class="mt-1.5 text-sm font-medium text-slate-700">
                            {{ $dutyLetter->event_date?->translatedFormat('d F Y') }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Waktu</p>
                        <p class="mt-1.5 text-sm font-medium text-slate-700">
                            {{ substr((string) $dutyLetter->start_time, 0, 5) }}
                            @if($dutyLetter->end_time)
                            - {{ substr((string) $dutyLetter->end_time, 0, 5) }}
                            @endif
                            WIB
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Lokasi</p>
                        <p class="mt-1.5 text-sm font-medium text-slate-700">
                            {{ $dutyLetter->location_name }}
                        </p>
                    </div>

                    @if($dutyLetter->location_address)
                    <div class="sm:col-span-2">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Alamat</p>
                        <p class="mt-1.5 text-sm leading-relaxed text-slate-700">
                            {{ $dutyLetter->location_address }}
                        </p>
                    </div>
                    @endif

                    @if($dutyLetter->description)
                    <div class="sm:col-span-2">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Keterangan</p>
                        <p class="mt-1.5 whitespace-pre-line text-sm leading-relaxed text-slate-700">{{ $dutyLetter->description }}</p>
                    </div>
                    @endif

                    @if($dutyLetter->maps_url)
                    <div class="sm:col-span-2">
                        <a
                            href="{{ $dutyLetter->maps_url }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex items-center gap-2 rounded-lg bg-blue-50 px-3 py-2 text-sm font-semibold text-blue-700 transition hover:bg-blue-100">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 21s6-5.2 6-11a6 6 0 10-12 0c0 5.8 6 11 6 11zM12 12a2 2 0 100-4 2 2 0 000 4z" />
                            </svg>
                            Buka Google Maps
                        </a>
                    </div>
                    @endif
                </div>
            </div>


            {{-- Penerima --}}
            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                <div class="flex flex-col gap-2 border-b border-slate-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                    <div>
                        <h2 class="text-base font-semibold text-slate-800">
                            Pegawai yang Ditugaskan
                        </h2>
                        <p class="mt-1 text-sm text-slate-500">
                            Status laporan dan pembayaran dicatat per pegawai.
                        </p>
                    </div>

                    <span class="rounded-full bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700">
                        {{ $dutyLetter->assignments->count() }} orang
                    </span>
                </div>

                {{-- MOBILE: kartu per pegawai, tidak memaksa tabel melebar --}}
                <div class="divide-y divide-slate-100 md:hidden">
                    @forelse($dutyLetter->assignments as $assignment)
                    @php
                    [$reportLabel, $reportClass] = match ($assignment->report_status) {
                    'submitted' => ['Menunggu Verifikasi', 'bg-blue-50 text-blue-700'],
                    'revision' => ['Perlu Perbaikan', 'bg-amber-50 text-amber-700'],
                    'verified' => ['Diverifikasi', 'bg-emerald-50 text-emerald-700'],
                    default => ['Belum Ada Laporan', 'bg-slate-100 text-slate-600'],
                    };
                    @endphp

                    <div class="p-4 sm:p-5">
                        <div class="flex items-start gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-slate-100 text-sm font-bold text-slate-600">
                                {{ strtoupper(substr($assignment->assignee_name, 0, 1)) }}
                            </div>

                            <div class="min-w-0 flex-1">
                                <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                                    <div class="min-w-0">
                                        <p class="break-words text-sm font-semibold text-slate-800">
                                            {{ $assignment->assignee_name }}
                                        </p>
                                        <div class="mt-1 flex flex-wrap items-center gap-2">
                                            <span class="inline-flex rounded-full px-2 py-0.5 text-[10px] font-semibold {{ $assignment->assignee_role === 'kabid' ? 'bg-violet-50 text-violet-700' : 'bg-blue-50 text-blue-700' }}">
                                                {{ $assignment->assignee_role === 'kabid' ? 'Kabid' : 'Karyawan' }}
                                            </span>
                                            <span class="break-words text-xs text-slate-500">
                                                {{ $assignment->assignee_department ?: 'Bidang belum diatur' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4 grid gap-3 sm:grid-cols-2">
                                    <div class="rounded-lg bg-slate-50 p-3">
                                        <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Laporan</p>
                                        <span class="mt-1.5 inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $reportClass }}">
                                            {{ $reportLabel }}
                                        </span>
                                        @if($assignment->report_submitted_at)
                                        <p class="mt-1 text-[11px] text-slate-400">{{ $assignment->report_submitted_at->format('d/m/Y H:i') }}</p>
                                        @endif
                                    </div>

                                    <div class="rounded-lg bg-slate-50 p-3">
                                        <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">Fee</p>
                                        @if($assignment->fee_status === 'paid')
                                        <span class="mt-1.5 inline-flex rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">Sudah Dibayar</span>
                                        @if($assignment->fee_paid_at)
                                        <p class="mt-1 text-[11px] text-slate-400">{{ $assignment->fee_paid_at->format('d/m/Y H:i') }}</p>
                                        @endif
                                        @else
                                        <span class="mt-1.5 inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600">Belum Dibayar</span>
                                        @endif
                                    </div>
                                </div>

                                @if($assignment->report)
                                <a
                                    href="{{ route('admin.duty-reports.show', [$dutyLetter, $assignment]) }}"
                                    class="mt-3 inline-flex w-full items-center justify-center rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-xs font-semibold text-slate-700 transition hover:border-blue-300 hover:bg-blue-50 hover:text-blue-700 sm:w-auto">
                                    {{ $assignment->report_status === 'submitted' ? 'Periksa Laporan' : 'Lihat Laporan' }}
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="p-8 text-center text-sm text-slate-500">Tidak ada penerima surat dinas.</div>
                    @endforelse
                </div>

                {{-- TABLET/DESKTOP: tabel tetap tersedia --}}
                <div class="hidden overflow-x-auto md:block">
                    <table class="min-w-[900px] divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Pegawai</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Bidang</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Laporan</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Fee</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Aksi</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-200 bg-white">
                            @forelse($dutyLetter->assignments as $assignment)
                            <tr class="align-top hover:bg-slate-50/70">
                                <td class="min-w-[230px] px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-slate-100 text-sm font-bold text-slate-600">
                                            {{ strtoupper(substr($assignment->assignee_name, 0, 1)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="break-words text-sm font-semibold text-slate-800">{{ $assignment->assignee_name }}</p>
                                            <span class="mt-1 inline-flex rounded-full px-2 py-0.5 text-[10px] font-semibold {{ $assignment->assignee_role === 'kabid' ? 'bg-violet-50 text-violet-700' : 'bg-blue-50 text-blue-700' }}">
                                                {{ $assignment->assignee_role === 'kabid' ? 'Kabid' : 'Karyawan' }}
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-5 py-4 text-sm text-slate-600">{{ $assignment->assignee_department ?: '-' }}</td>

                                <td class="px-5 py-4">
                                    @switch($assignment->report_status)
                                    @case('submitted')
                                    <span class="inline-flex rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700">Menunggu Verifikasi</span>
                                    @break
                                    @case('revision')
                                    <span class="inline-flex rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700">Perlu Perbaikan</span>
                                    @break
                                    @case('verified')
                                    <span class="inline-flex rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">Diverifikasi</span>
                                    @break
                                    @default
                                    <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600">Belum Ada Laporan</span>
                                    @endswitch
                                    @if($assignment->report_submitted_at)
                                    <p class="mt-1 text-xs text-slate-400">{{ $assignment->report_submitted_at->format('d/m/Y H:i') }}</p>
                                    @endif
                                </td>

                                <td class="px-5 py-4">
                                    @if($assignment->fee_status === 'paid')
                                    <span class="inline-flex rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">Sudah Dibayar</span>
                                    @if($assignment->fee_paid_at)
                                    <p class="mt-1 text-xs text-slate-400">{{ $assignment->fee_paid_at->format('d/m/Y H:i') }}</p>
                                    @endif
                                    @else
                                    <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600">Belum Dibayar</span>
                                    @endif
                                </td>

                                <td class="px-5 py-4">
                                    @if($assignment->report)
                                    <a href="{{ route('admin.duty-reports.show', [$dutyLetter, $assignment]) }}" class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-700 transition hover:border-blue-300 hover:bg-blue-50 hover:text-blue-700">
                                        {{ $assignment->report_status === 'submitted' ? 'Periksa Laporan' : 'Lihat Laporan' }}
                                    </a>
                                    @else
                                    <span class="text-xs text-slate-400">-</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-5 py-10 text-center text-sm text-slate-500">Tidak ada penerima surat dinas.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>


        {{-- ========================================================
            RIGHT COLUMN
        ======================================================== --}}
        <div class="space-y-6">

            <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-5 py-4">
                    <h2 class="text-base font-semibold text-slate-800">
                        Dokumen Surat
                    </h2>
                </div>

                <div class="p-5">
                    <div class="flex items-start gap-3 rounded-lg bg-slate-50 p-4">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-red-50 text-red-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 3h7l5 5v13H7a2 2 0 01-2-2V5a2 2 0 012-2zM14 3v6h6" />
                            </svg>
                        </div>

                        <div class="min-w-0 flex-1">
                            <p class="break-all text-sm font-semibold text-slate-700">
                                {{ $dutyLetter->letter_original_name }}
                            </p>
                            <p class="mt-1 text-xs text-slate-400">
                                PDF
                                @if($dutyLetter->letter_size)
                                • {{ number_format($dutyLetter->letter_size / 1024, 0, ',', '.') }} KB
                                @endif
                            </p>
                        </div>
                    </div>

                    <a
                        href="{{ route('admin.duty-letters.pdf', $dutyLetter) }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-lg bg-slate-800 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-900">
                        Lihat Surat PDF
                    </a>
                </div>
            </div>


            <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-5 py-4">
                    <h2 class="text-base font-semibold text-slate-800">
                        Informasi Penerbitan
                    </h2>
                </div>

                <div class="space-y-4 p-5">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Dibuat Oleh</p>
                        <p class="mt-1 text-sm font-medium text-slate-700">
                            {{ $dutyLetter->creator?->name ?? 'Admin' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Diterbitkan</p>
                        <p class="mt-1 text-sm font-medium text-slate-700">
                            {{ $dutyLetter->published_at?->format('d/m/Y H:i') ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Jumlah Penerima</p>
                        <p class="mt-1 text-sm font-medium text-slate-700">
                            {{ $dutyLetter->assignments->count() }} pegawai
                        </p>
                    </div>
                </div>
            </div>


            <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-5 py-4">
                    <h2 class="text-base font-semibold text-slate-800">
                        Progress Surat Dinas
                    </h2>
                </div>

                <div class="grid grid-cols-2 gap-3 p-5">
                    <div class="rounded-lg bg-slate-50 p-3">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Penerima</p>
                        <p class="mt-1 text-lg font-bold text-slate-800">{{ $assignmentCount }}</p>
                    </div>

                    <div class="rounded-lg bg-emerald-50 p-3">
                        <p class="text-xs font-semibold uppercase tracking-wide text-emerald-600">Laporan Valid</p>
                        <p class="mt-1 text-lg font-bold text-emerald-800">{{ $verifiedCount }}/{{ $assignmentCount }}</p>
                    </div>

                    <div class="rounded-lg bg-blue-50 p-3">
                        <p class="text-xs font-semibold uppercase tracking-wide text-blue-600">Fee Dibayar</p>
                        <p class="mt-1 text-lg font-bold text-blue-800">{{ $paidCount }}/{{ $assignmentCount }}</p>
                    </div>

                    <div class="rounded-lg bg-violet-50 p-3">
                        <p class="text-xs font-semibold uppercase tracking-wide text-violet-600">Selesai</p>
                        <p class="mt-1 text-lg font-bold text-violet-800">{{ $completedCount }}/{{ $assignmentCount }}</p>
                    </div>
                </div>

                <div class="border-t border-slate-100 px-5 py-4">
                    @if($assignmentCount > 0 && $completedCount === $assignmentCount)
                    <p class="text-sm font-semibold text-emerald-700">
                        Semua penerima telah menyelesaikan laporan dan pembayaran fee sudah dikonfirmasi.
                    </p>
                    @else
                    <p class="text-sm leading-relaxed text-slate-500">
                        Satu penerima dianggap selesai setelah laporannya diverifikasi dan fee dinasnya sudah dikonfirmasi dibayar.
                    </p>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>
@endsection