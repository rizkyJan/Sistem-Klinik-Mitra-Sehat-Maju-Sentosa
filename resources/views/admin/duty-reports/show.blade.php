@extends('layouts.admin')

@section('title', 'Verifikasi Laporan Dinas')
@section('page-title', 'Verifikasi Laporan Dinas')

@section('content')
@php
$report = $dutyAssignment->report;
$status = $dutyAssignment->report_status;
@endphp

<div class="mx-auto max-w-7xl space-y-6">
    <x-toast-notification />

    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div>
            <a
                href="{{ route('admin.duty-letters.show', $dutyLetter) }}"
                class="inline-flex items-center gap-1.5 text-sm font-medium text-slate-500 transition hover:text-slate-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali ke Detail Surat
            </a>

            <h1 class="mt-3 text-2xl font-bold text-slate-800">
                Laporan {{ $dutyAssignment->assignee_name }}
            </h1>
            <p class="mt-1 text-sm text-slate-500">
                {{ $dutyLetter->title }}
            </p>
        </div>

        <div>
            @if($status === \App\Models\DutyAssignment::REPORT_SUBMITTED)
            <span class="inline-flex items-center gap-1.5 rounded-full bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700">
                <span class="h-1.5 w-1.5 rounded-full bg-blue-500"></span>
                Menunggu Verifikasi
            </span>
            @elseif($status === \App\Models\DutyAssignment::REPORT_REVISION)
            <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-3 py-1.5 text-xs font-semibold text-amber-700">
                <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                Perlu Perbaikan
            </span>
            @elseif($status === \App\Models\DutyAssignment::REPORT_VERIFIED)
            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700">
                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                Diverifikasi
            </span>
            @endif
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-[1.35fr_0.65fr]">
        <div class="space-y-6">
            <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-5 py-4 sm:px-6">
                    <h2 class="text-base font-semibold text-slate-800">Laporan Hasil Dinas</h2>
                    <p class="mt-1 text-sm text-slate-500">
                        Isi laporan yang dikirim oleh pegawai.
                    </p>
                </div>

                <div class="divide-y divide-slate-100">
                    <div class="p-5 sm:p-6">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Pokok Pembahasan</p>
                        <p class="mt-2 whitespace-pre-line text-sm leading-7 text-slate-700">{{ $report->discussion_summary }}</p>
                    </div>

                    <div class="p-5 sm:p-6">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Hasil / Kesimpulan</p>
                        <p class="mt-2 whitespace-pre-line text-sm leading-7 text-slate-700">{{ $report->result_summary }}</p>
                    </div>

                    <div class="p-5 sm:p-6">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Tindak Lanjut</p>
                        <p class="mt-2 whitespace-pre-line text-sm leading-7 text-slate-700">{{ $report->follow_up ?: '-' }}</p>
                    </div>

                    <div class="p-5 sm:p-6">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Catatan Tambahan</p>
                        <p class="mt-2 whitespace-pre-line text-sm leading-7 text-slate-700">{{ $report->additional_notes ?: '-' }}</p>
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-5 py-4 sm:px-6">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <h2 class="text-base font-semibold text-slate-800">Dokumentasi Kehadiran</h2>
                            <p class="mt-1 text-sm text-slate-500">Klik foto untuk melihat ukuran penuh.</p>
                        </div>
                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                            {{ $report->files->count() }} foto
                        </span>
                    </div>
                </div>

                <div class="p-5 sm:p-6">
                    @if($report->files->isNotEmpty())
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach($report->files as $file)
                        <a
                            href="{{ route('admin.duty-reports.file', [$dutyLetter, $dutyAssignment, $file]) }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="group overflow-hidden rounded-xl border border-slate-200 bg-slate-50 transition hover:border-blue-300 hover:shadow-sm">
                            <div class="aspect-[4/3] overflow-hidden bg-slate-100">
                                <img
                                    src="{{ route('admin.duty-reports.file', [$dutyLetter, $dutyAssignment, $file]) }}"
                                    alt="Dokumentasi {{ $loop->iteration }}"
                                    class="h-full w-full object-cover transition duration-300 group-hover:scale-[1.02]">
                            </div>
                            <div class="p-3">
                                <p class="truncate text-xs font-medium text-slate-700">{{ $file->original_name }}</p>
                            </div>
                        </a>
                        @endforeach
                    </div>
                    @else
                    <div class="rounded-lg bg-slate-50 p-5 text-center text-sm text-slate-500">
                        Dokumentasi tidak ditemukan.
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-5 py-4">
                    <h2 class="text-base font-semibold text-slate-800">Pegawai & Kegiatan</h2>
                </div>

                <div class="space-y-4 p-5">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Pegawai</p>
                        <p class="mt-1 text-sm font-semibold text-slate-800">{{ $dutyAssignment->assignee_name }}</p>
                        <p class="mt-0.5 text-xs text-slate-500">
                            {{ $dutyAssignment->assignee_role === 'kabid' ? 'Kabid' : 'Karyawan' }}
                            @if($dutyAssignment->assignee_department)
                            • {{ $dutyAssignment->assignee_department }}
                            @endif
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Tanggal Kegiatan</p>
                        <p class="mt-1 text-sm text-slate-700">{{ $dutyLetter->event_date?->translatedFormat('d F Y') }}</p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Lokasi</p>
                        <p class="mt-1 text-sm text-slate-700">{{ $dutyLetter->location_name }}</p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Dikirim</p>
                        <p class="mt-1 text-sm text-slate-700">
                            {{ $dutyAssignment->report_submitted_at?->format('d/m/Y H:i') ?: '-' }}
                        </p>
                    </div>

                    <a
                        href="{{ route('admin.duty-letters.pdf', $dutyLetter) }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex w-full items-center justify-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                        Lihat PDF Surat
                    </a>
                </div>
            </div>

            @if($status === \App\Models\DutyAssignment::REPORT_SUBMITTED)
            <div class="rounded-xl border border-blue-200 bg-white shadow-sm">
                <div class="border-b border-blue-100 bg-blue-50/70 px-5 py-4">
                    <h2 class="text-base font-semibold text-slate-800">Keputusan Admin</h2>
                    <p class="mt-1 text-sm text-slate-500">
                        Periksa isi laporan dan dokumentasi terlebih dahulu.
                    </p>
                </div>

                <div class="space-y-5 p-5">
                    <form
                        method="POST"
                        action="{{ route('admin.duty-reports.verify', [$dutyLetter, $dutyAssignment]) }}"
                        data-confirm
                        data-confirm-title="Verifikasi Laporan?"
                        data-confirm-message="Pastikan isi laporan dan bukti kehadiran sudah benar. Setelah diverifikasi, laporan menjadi final."
                        data-confirm-button="Ya, Verifikasi"
                        data-confirm-tone="primary">
                        @csrf
                        @method('PATCH')

                        <button
                            type="submit"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Verifikasi Laporan
                        </button>
                    </form>

                    <div class="border-t border-slate-200 pt-5">
                        <form
                            method="POST"
                            action="{{ route('admin.duty-reports.revision', [$dutyLetter, $dutyAssignment]) }}">
                            @csrf
                            @method('PATCH')

                            <label for="revision_note" class="text-sm font-semibold text-slate-700">
                                Minta Perbaikan
                            </label>
                            <p class="mt-1 text-xs leading-relaxed text-slate-500">
                                Jelaskan bagian yang harus diperbaiki. Catatan ini akan terlihat oleh pegawai.
                            </p>

                            <textarea
                                id="revision_note"
                                name="revision_note"
                                rows="5"
                                required
                                maxlength="2000"
                                class="mt-3 w-full rounded-lg border-slate-300 text-sm text-slate-700 shadow-sm focus:border-amber-500 focus:ring-amber-500"
                                placeholder="Contoh: Mohon lengkapi hasil keputusan rapat dan jelaskan tindak lanjut yang harus dilakukan klinik.">{{ old('revision_note') }}</textarea>

                            @error('revision_note')
                            <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
                            @enderror

                            <button
                                type="submit"
                                class="mt-3 inline-flex w-full items-center justify-center gap-2 rounded-lg bg-amber-500 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-amber-600">
                                Minta Perbaikan
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @elseif($status === \App\Models\DutyAssignment::REPORT_REVISION)
            <div class="rounded-xl border border-amber-200 bg-amber-50 p-5">
                <h2 class="text-sm font-semibold text-amber-900">Menunggu Perbaikan Pegawai</h2>
                <p class="mt-2 whitespace-pre-line text-sm leading-6 text-amber-800">{{ $dutyAssignment->revision_note }}</p>
                <p class="mt-3 text-xs text-amber-700">
                    Setelah pegawai memperbaiki dan mengirim ulang, status akan kembali menjadi Menunggu Verifikasi.
                </p>
            </div>
            @elseif($status === \App\Models\DutyAssignment::REPORT_VERIFIED)
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-5">
                <div class="flex items-start gap-3">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-semibold text-emerald-900">Laporan Sudah Diverifikasi</h2>
                        <p class="mt-1 text-sm text-emerald-800">
                            {{ $dutyAssignment->reportVerifier?->name ?: 'Admin' }}
                        </p>
                        <p class="mt-1 text-xs text-emerald-700">
                            {{ $dutyAssignment->report_verified_at?->format('d/m/Y H:i') ?: '-' }}
                        </p>
                    </div>
                </div>
            </div>
            @endif

            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-5 py-4">
                    <h2 class="text-base font-semibold text-slate-800">Pembayaran Fee Dinas</h2>
                    <p class="mt-1 text-sm text-slate-500">
                        Sistem tidak mencatat nominal. Admin hanya mengonfirmasi setelah fee benar-benar dibayarkan.
                    </p>
                </div>

                <div class="p-5">
                    @if($dutyAssignment->fee_status === \App\Models\DutyAssignment::FEE_PAID)
                    <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4">
                        <div class="flex items-start gap-3">
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>

                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-emerald-900">Fee Sudah Dibayar</p>
                                <p class="mt-1 text-sm text-emerald-800">
                                    Dikonfirmasi oleh {{ $dutyAssignment->feeConfirmer?->name ?: 'Admin' }}
                                </p>
                                <p class="mt-1 text-xs text-emerald-700">
                                    {{ $dutyAssignment->fee_paid_at?->format('d/m/Y H:i') ?: '-' }}
                                </p>

                                @if($dutyAssignment->fee_payment_note)
                                <div class="mt-3 rounded-lg bg-white/70 p-3">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700">
                                        Catatan Pembayaran
                                    </p>
                                    <p class="mt-1 whitespace-pre-line text-sm leading-6 text-emerald-900">{{ $dutyAssignment->fee_payment_note }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @elseif($status !== \App\Models\DutyAssignment::REPORT_VERIFIED)
                    <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600">
                        Belum Dibayar
                    </span>

                    <div class="mt-4 rounded-lg border border-amber-200 bg-amber-50 p-4">
                        <p class="text-sm font-semibold text-amber-900">
                            Laporan belum selesai diverifikasi
                        </p>
                        <p class="mt-1 text-sm leading-6 text-amber-800">
                            Tombol konfirmasi pembayaran baru tersedia setelah laporan berstatus
                            <strong>Diverifikasi</strong>.
                        </p>
                    </div>
                    @else
                    <div class="mb-4 flex items-center justify-between gap-3">
                        <span class="inline-flex rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700">
                            Belum Dibayar
                        </span>
                        <span class="text-xs font-medium text-emerald-700">
                            Laporan sudah diverifikasi
                        </span>
                    </div>

                    <form
                        method="POST"
                        action="{{ route('admin.duty-fees.paid', [$dutyLetter, $dutyAssignment]) }}"
                        data-confirm
                        data-confirm-title="Konfirmasi Fee Sudah Dibayar?"
                        data-confirm-message="Pastikan fee untuk pegawai ini benar-benar sudah dibayarkan. Status pembayaran akan tercatat di akun pegawai."
                        data-confirm-button="Ya, Sudah Dibayar"
                        data-confirm-tone="primary">
                        @csrf
                        @method('PATCH')

                        <label for="fee_payment_note" class="text-sm font-semibold text-slate-700">
                            Catatan Pembayaran
                            <span class="font-normal text-slate-400">(opsional)</span>
                        </label>

                        <p class="mt-1 text-xs leading-relaxed text-slate-500">
                            Contoh: Transfer, tunai, atau keterangan singkat lain. Catatan ini dapat dilihat pegawai.
                        </p>

                        <textarea
                            id="fee_payment_note"
                            name="fee_payment_note"
                            rows="3"
                            maxlength="1000"
                            class="mt-3 w-full rounded-lg border-slate-300 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            placeholder="Contoh: Dibayarkan melalui transfer.">{{ old('fee_payment_note') }}</textarea>

                        @error('fee_payment_note')
                        <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
                        @enderror

                        <button
                            type="submit"
                            class="mt-3 inline-flex w-full items-center justify-center gap-2 rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Konfirmasi Fee Sudah Dibayar
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection