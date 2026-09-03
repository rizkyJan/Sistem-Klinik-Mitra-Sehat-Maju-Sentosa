@extends('layouts.karyawan')

@section('title', 'Detail Surat Dinas')
@section('page-title', 'Detail Surat Dinas')

@section('content')

@php
$letter = $dutyAssignment->dutyLetter;
$isCancelled = $letter->status === \App\Models\DutyLetter::STATUS_CANCELLED;
$reportClass = match($dutyAssignment->report_status) {
\App\Models\DutyAssignment::REPORT_SUBMITTED => 'bg-blue-100 text-blue-700',
\App\Models\DutyAssignment::REPORT_REVISION => 'bg-red-100 text-red-700',
\App\Models\DutyAssignment::REPORT_VERIFIED => 'bg-emerald-100 text-emerald-700',
default => 'bg-amber-100 text-amber-700',
};
$feeClass = $dutyAssignment->isFeePaid()
? 'bg-emerald-100 text-emerald-700'
: 'bg-slate-100 text-slate-600';
$canWriteReport =
! $isCancelled
&& ! $letter->event_date->isFuture()
&& in_array(
$dutyAssignment->report_status,
[
\App\Models\DutyAssignment::REPORT_PENDING,
\App\Models\DutyAssignment::REPORT_REVISION,
],
true
);
@endphp

<x-toast-notification />

<div class="space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div class="min-w-0">
            <a href="{{ route('karyawan.duty-letters.index') }}" class="inline-flex items-center gap-1 text-sm font-medium text-blue-600 hover:text-blue-700">
                <span aria-hidden="true">←</span>
                Kembali ke Surat Dinas Saya
            </a>

            <div class="mt-3 flex flex-wrap items-center gap-2">
                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $isCancelled ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700' }}">
                    {{ $isCancelled ? 'Dibatalkan' : 'Ditugaskan' }}
                </span>

                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $reportClass }}">
                    {{ $dutyAssignment->report_status_label }}
                </span>
            </div>

            <h1 class="mt-3 break-words text-2xl font-bold text-slate-800">
                {{ $letter->title }}
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                {{ $letter->letter_number ?: 'Nomor surat tidak dicantumkan' }}
            </p>
        </div>

        <a href="{{ route('karyawan.duty-letters.pdf', $dutyAssignment) }}" target="_blank" rel="noopener" class="inline-flex shrink-0 items-center justify-center gap-2 rounded-lg bg-red-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-red-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 3h7l5 5v13H7V3zM14 3v5h5M10 13h6M10 17h6" />
            </svg>
            Lihat PDF Surat
        </a>
    </div>


    @if($isCancelled)
    <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
        Surat dinas ini telah dibatalkan oleh Admin. Informasi tetap ditampilkan sebagai riwayat.
    </div>
    @endif


    <div class="grid gap-6 xl:grid-cols-[minmax(0,1.5fr)_minmax(320px,0.8fr)]">

        <div class="space-y-6">
            {{-- DETAIL KEGIATAN --}}
            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-5 py-4 sm:px-6">
                    <h2 class="font-semibold text-slate-800">Informasi Kegiatan</h2>
                </div>

                <dl class="divide-y divide-slate-100">
                    <div class="grid gap-1 px-5 py-4 sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm text-slate-500">Penyelenggara</dt>
                        <dd class="break-words text-sm font-medium text-slate-800 sm:col-span-2">{{ $letter->organizer ?: '-' }}</dd>
                    </div>

                    <div class="grid gap-1 px-5 py-4 sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm text-slate-500">Tanggal</dt>
                        <dd class="text-sm font-medium text-slate-800 sm:col-span-2">{{ $letter->event_date->translatedFormat('l, d F Y') }}</dd>
                    </div>

                    <div class="grid gap-1 px-5 py-4 sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm text-slate-500">Waktu</dt>
                        <dd class="text-sm font-medium text-slate-800 sm:col-span-2">
                            {{ substr((string) $letter->start_time, 0, 5) }}
                            @if($letter->end_time)
                            - {{ substr((string) $letter->end_time, 0, 5) }}
                            @endif
                            WIB
                        </dd>
                    </div>

                    <div class="grid gap-1 px-5 py-4 sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm text-slate-500">Lokasi</dt>
                        <dd class="break-words text-sm font-medium text-slate-800 sm:col-span-2">{{ $letter->location_name }}</dd>
                    </div>

                    <div class="grid gap-1 px-5 py-4 sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm text-slate-500">Alamat</dt>
                        <dd class="whitespace-pre-line break-words text-sm leading-relaxed text-slate-800 sm:col-span-2">{{ $letter->location_address ?: '-' }}</dd>
                    </div>

                    @if($letter->maps_url)
                    <div class="grid gap-1 px-5 py-4 sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm text-slate-500">Google Maps</dt>
                        <dd class="sm:col-span-2">
                            <a href="{{ $letter->maps_url }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 rounded-lg border border-blue-200 bg-blue-50 px-3 py-2 text-sm font-semibold text-blue-700 transition hover:bg-blue-100">
                                Buka Lokasi di Maps
                                <span aria-hidden="true">↗</span>
                            </a>
                        </dd>
                    </div>
                    @endif

                    <div class="grid gap-1 px-5 py-4 sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm text-slate-500">Keterangan</dt>
                        <dd class="whitespace-pre-line break-words text-sm leading-relaxed text-slate-800 sm:col-span-2">{{ $letter->description ?: '-' }}</dd>
                    </div>
                </dl>
            </div>


            {{-- LAPORAN --}}
            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                <div class="flex flex-col gap-2 border-b border-slate-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                    <div>
                        <h2 class="font-semibold text-slate-800">Laporan Hasil Dinas</h2>
                        <p class="mt-1 text-xs text-slate-500">Ringkasan hasil kegiatan, tindak lanjut, dan dokumentasi kehadiran.</p>
                    </div>

                    <span class="inline-flex w-fit rounded-full px-2.5 py-1 text-xs font-semibold {{ $reportClass }}">
                        {{ $dutyAssignment->report_status_label }}
                    </span>
                </div>

                @if($dutyAssignment->report_status === \App\Models\DutyAssignment::REPORT_REVISION && $dutyAssignment->revision_note)
                <div class="border-b border-red-100 bg-red-50 px-5 py-4 sm:px-6">
                    <p class="text-sm font-semibold text-red-800">Catatan Perbaikan dari Admin</p>
                    <p class="mt-1 whitespace-pre-line text-sm leading-relaxed text-red-700">
                        {{ $dutyAssignment->revision_note }}
                    </p>
                </div>
                @endif

                @if($dutyAssignment->report)
                <dl class="divide-y divide-slate-100">
                    <div class="px-5 py-4 sm:px-6">
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Pokok Pembahasan</dt>
                        <dd class="mt-2 whitespace-pre-line break-words text-sm leading-relaxed text-slate-800">{{ $dutyAssignment->report->discussion_summary }}</dd>
                    </div>

                    <div class="px-5 py-4 sm:px-6">
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Hasil / Kesimpulan</dt>
                        <dd class="mt-2 whitespace-pre-line break-words text-sm leading-relaxed text-slate-800">{{ $dutyAssignment->report->result_summary }}</dd>
                    </div>

                    <div class="px-5 py-4 sm:px-6">
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Tindak Lanjut</dt>
                        <dd class="mt-2 whitespace-pre-line break-words text-sm leading-relaxed text-slate-800">{{ $dutyAssignment->report->follow_up ?: '-' }}</dd>
                    </div>

                    @if($dutyAssignment->report->additional_notes)
                    <div class="px-5 py-4 sm:px-6">
                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Catatan Tambahan</dt>
                        <dd class="mt-2 whitespace-pre-line break-words text-sm leading-relaxed text-slate-800">{{ $dutyAssignment->report->additional_notes }}</dd>
                    </div>
                    @endif
                </dl>

                <div class="border-t border-slate-100 px-5 py-5 sm:px-6">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                        Dokumentasi Kehadiran
                    </p>

                    @if($dutyAssignment->report->files->isNotEmpty())
                    <div class="mt-3 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach($dutyAssignment->report->files as $file)
                        <a
                            href="{{ route('karyawan.duty-reports.file', [$dutyAssignment, $file]) }}"
                            target="_blank"
                            rel="noopener"
                            class="group overflow-hidden rounded-lg border border-slate-200 bg-slate-100">
                            <div class="aspect-[4/3] overflow-hidden">
                                <img
                                    src="{{ route('karyawan.duty-reports.file', [$dutyAssignment, $file]) }}"
                                    alt="Dokumentasi {{ $loop->iteration }}"
                                    class="h-full w-full object-cover transition duration-200 group-hover:scale-[1.02]">
                            </div>
                            <div class="truncate bg-white px-3 py-2 text-xs font-medium text-slate-600">
                                {{ $file->original_name }}
                            </div>
                        </a>
                        @endforeach
                    </div>
                    @else
                    <p class="mt-2 text-sm text-slate-500">
                        Belum ada foto dokumentasi.
                    </p>
                    @endif
                </div>

                <div class="border-t border-slate-100 bg-slate-50 px-5 py-4 sm:px-6">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            @if($dutyAssignment->report_status === \App\Models\DutyAssignment::REPORT_SUBMITTED)
                            <p class="text-sm font-semibold text-blue-700">Laporan sudah dikirim</p>
                            <p class="mt-1 text-xs text-slate-500">
                                Menunggu Admin melakukan verifikasi.
                            </p>
                            @elseif($dutyAssignment->report_status === \App\Models\DutyAssignment::REPORT_VERIFIED)
                            <p class="text-sm font-semibold text-emerald-700">Laporan sudah diverifikasi</p>
                            <p class="mt-1 text-xs text-slate-500">
                                Laporan sudah final dan tidak dapat diedit.
                            </p>
                            @elseif($dutyAssignment->report_status === \App\Models\DutyAssignment::REPORT_REVISION)
                            <p class="text-sm font-semibold text-red-700">Laporan perlu diperbaiki</p>
                            <p class="mt-1 text-xs text-slate-500">
                                Perbaiki sesuai catatan Admin lalu kirim ulang.
                            </p>
                            @endif
                        </div>

                        @if($canWriteReport)
                        <a
                            href="{{ route('karyawan.duty-reports.edit', $dutyAssignment) }}"
                            class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700">
                            {{ $dutyAssignment->report_status === \App\Models\DutyAssignment::REPORT_REVISION ? 'Perbaiki Laporan' : 'Edit Laporan' }}
                        </a>
                        @endif
                    </div>
                </div>
                @else
                <div class="px-5 py-10 text-center sm:px-6">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-amber-50 text-amber-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6M9 16h6M5 4h14v16H5z" />
                        </svg>
                    </div>

                    <p class="mt-3 font-semibold text-slate-800">Belum ada laporan hasil dinas</p>

                    @if($isCancelled)
                    <p class="mx-auto mt-1 max-w-md text-sm text-slate-500">
                        Laporan tidak dapat dibuat karena surat dinas sudah dibatalkan.
                    </p>
                    @elseif($letter->event_date->isFuture())
                    <p class="mx-auto mt-1 max-w-md text-sm text-slate-500">
                        Laporan dapat dibuat mulai tanggal kegiatan,
                        <strong>{{ $letter->event_date->translatedFormat('d F Y') }}</strong>.
                    </p>
                    @else
                    <p class="mx-auto mt-1 max-w-md text-sm text-slate-500">
                        Setelah kegiatan selesai, isi pokok pembahasan, hasil, tindak lanjut, dan upload bukti kehadiran.
                    </p>

                    <a
                        href="{{ route('karyawan.duty-reports.edit', $dutyAssignment) }}"
                        class="mt-4 inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700">
                        Buat Laporan Dinas
                    </a>
                    @endif
                </div>
                @endif
            </div>
        </div>


        <div class="space-y-6">
            {{-- PENUGASAN --}}
            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-5 py-4">
                    <h2 class="font-semibold text-slate-800">Penugasan Anda</h2>
                </div>

                <div class="space-y-4 p-5 text-sm">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Nama</p>
                        <p class="mt-1 font-semibold text-slate-800">{{ $dutyAssignment->assignee_name }}</p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Role</p>
                        <p class="mt-1 capitalize text-slate-700">{{ $dutyAssignment->assignee_role }}</p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Bagian</p>
                        <p class="mt-1 text-slate-700">{{ $dutyAssignment->assignee_department ?: '-' }}</p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Ditugaskan</p>
                        <p class="mt-1 text-slate-700">{{ $dutyAssignment->assigned_at?->format('d/m/Y H:i') ?: '-' }}</p>
                    </div>
                </div>
            </div>


            {{-- FEE --}}
            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-5 py-4">
                    <h2 class="font-semibold text-slate-800">Status Fee Dinas</h2>
                </div>

                <div class="p-5">
                    <span class="inline-flex rounded-full px-3 py-1.5 text-sm font-semibold {{ $feeClass }}">
                        {{ $dutyAssignment->fee_status_label }}
                    </span>

                    @if($dutyAssignment->fee_status === \App\Models\DutyAssignment::FEE_PAID)
                    <div class="mt-4 rounded-lg border border-emerald-200 bg-emerald-50 p-4">
                        <p class="text-sm font-semibold text-emerald-900">
                            Pembayaran telah dikonfirmasi Admin
                        </p>

                        <p class="mt-1 text-sm text-emerald-800">
                            {{ $dutyAssignment->fee_paid_at?->format('d/m/Y H:i') ?: '-' }}
                            @if($dutyAssignment->feeConfirmer)
                            • {{ $dutyAssignment->feeConfirmer->name }}
                            @endif
                        </p>

                        @if($dutyAssignment->fee_payment_note)
                        <div class="mt-3 border-t border-emerald-200 pt-3">
                            <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700">
                                Catatan Pembayaran
                            </p>
                            <p class="mt-1 whitespace-pre-line text-sm leading-6 text-emerald-900">{{ $dutyAssignment->fee_payment_note }}</p>
                        </div>
                        @endif
                    </div>
                    @else
                    <p class="mt-3 text-sm leading-relaxed text-slate-500">
                        Belum ada konfirmasi pembayaran dari Admin. Nominal fee tidak ditampilkan di sistem.
                    </p>
                    @endif
                </div>
            </div>


            {{-- FILE SURAT --}}
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">File Surat</p>
                <p class="mt-2 break-all text-sm font-medium text-slate-700">{{ $letter->letter_original_name }}</p>

                @if($letter->letter_size)
                <p class="mt-1 text-xs text-slate-400">
                    {{ number_format($letter->letter_size / 1024, 0, ',', '.') }} KB
                </p>
                @endif

                <a href="{{ route('karyawan.duty-letters.pdf', $dutyAssignment) }}" target="_blank" rel="noopener" class="mt-4 inline-flex w-full items-center justify-center rounded-lg border border-red-200 bg-red-50 px-4 py-2.5 text-sm font-semibold text-red-700 transition hover:bg-red-100">
                    Buka PDF Surat Dinas
                </a>
            </div>
        </div>
    </div>
</div>

@endsection