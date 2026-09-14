@extends('layouts.admin')

@section('title', 'Detail Laporan Bulanan')
@section('page-title', 'Detail Laporan Bulanan')

@section('content')
<div class="space-y-6">
    @if(session('success'))<div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-800 shadow-sm">{{ session('success') }}</div>@endif
    @if(session('warning'))<div class="rounded-2xl border border-amber-200 bg-amber-50 px-5 py-4 text-sm font-medium text-amber-800 shadow-sm">{{ session('warning') }}</div>@endif
    @if(session('error'))<div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm font-medium text-red-800 shadow-sm">{{ session('error') }}</div>@endif

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <a href="{{ route('admin.monthly-reports.index', ['period' => $report->period->format('Y-m')]) }}" class="text-sm font-semibold text-blue-600 hover:text-blue-700">← Kembali ke daftar</a>
            <h1 class="mt-2 text-2xl font-bold text-slate-900">Laporan {{ $report->department?->name }}</h1>
            <p class="mt-1 text-sm text-slate-500">Periode {{ $report->period->copy()->locale('id')->translatedFormat('F Y') }}</p>
        </div>
        <span class="w-fit rounded-full px-3 py-1.5 text-xs font-bold {{ $report->status === 'verified' ? 'bg-emerald-50 text-emerald-700' : ($report->status === 'revision' ? 'bg-violet-50 text-violet-700' : 'bg-amber-50 text-amber-700') }}">
            {{ $report->statusLabel() }}
        </span>
    </div>

    <div class="grid gap-6 xl:grid-cols-3">
        <div class="space-y-6 xl:col-span-2">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <p class="text-xs font-semibold uppercase text-slate-500">Bidang</p>
                        <p class="mt-1 font-bold text-slate-900">{{ $report->department?->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase text-slate-500">Dikirim oleh</p>
                        <p class="mt-1 font-semibold text-slate-800">{{ $report->submittedBy?->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase text-slate-500">Tanggal upload</p>
                        <p class="mt-1 font-semibold text-slate-800">{{ $report->submitted_at?->locale('id')->translatedFormat('d F Y, H:i') ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase text-slate-500">Ukuran file</p>
                        <p class="mt-1 font-semibold text-slate-800">{{ number_format($report->size / 1024 / 1024, 2) }} MB</p>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="min-w-0">
                        <p class="text-xs font-semibold uppercase text-slate-500">File Laporan</p>
                        <p class="mt-1 break-all font-bold text-slate-900">{{ $report->original_name }}</p>
                    </div>
                    <div class="flex shrink-0 flex-wrap gap-2">
                        <a href="{{ route('admin.monthly-reports.preview', $report) }}" target="_blank" class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Preview</a>
                        <a href="{{ route('admin.monthly-reports.download', $report) }}" class="rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">Download</a>
                        <a href="{{ route('admin.monthly-reports.edit', $report) }}" class="rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">Edit</a>
                    </div>
                </div>

                @if($report->employee_note)
                <div class="mt-5 rounded-xl bg-slate-50 p-4">
                    <p class="text-xs font-semibold uppercase text-slate-500">Catatan Kabid</p>
                    <p class="mt-2 whitespace-pre-line text-sm text-slate-700">{{ $report->employee_note }}</p>
                </div>
                @endif
                @if($report->admin_note)
                <div class="mt-4 rounded-xl bg-blue-50 p-4">
                    <p class="text-xs font-semibold uppercase text-blue-700">Catatan Admin</p>
                    <p class="mt-2 whitespace-pre-line text-sm text-blue-900">{{ $report->admin_note }}</p>
                </div>
                @endif
                @if($report->revision_note)
                <div class="mt-4 rounded-xl bg-red-50 p-4">
                    <p class="text-xs font-semibold uppercase text-red-700">Catatan Revisi</p>
                    <p class="mt-2 whitespace-pre-line text-sm text-red-900">{{ $report->revision_note }}</p>
                </div>
                @endif
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-2xl border border-emerald-200 bg-white shadow-sm">
                <div class="border-b border-emerald-100 bg-emerald-50 px-5 py-4">
                    <h2 class="font-bold text-emerald-900">Konfirmasi Admin</h2>
                </div>
                <div class="p-5">
                    <form
                        method="POST"
                        action="{{ route('admin.monthly-reports.verify', $report) }}"
                        data-confirm
                        data-confirm-title="Verifikasi Laporan Bulanan?"
                        data-confirm-message="Pastikan file laporan bidang {{ $report->department?->name ?? '-' }} periode {{ $report->period->copy()->locale('id')->translatedFormat('F Y') }} sudah diperiksa dan benar. Setelah disetujui, status laporan akan menjadi Diverifikasi."
                        data-confirm-button="Ya, Verifikasi"
                        data-confirm-tone="success">
                        @csrf
                        @method('PATCH')

                        <button
                            type="submit"
                            class="w-full rounded-xl bg-emerald-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-emerald-700">
                            Verifikasi Laporan
                        </button>
                    </form>
                    @if($report->verified_at)
                    <p class="mt-3 text-xs leading-relaxed text-slate-500">Terakhir diverifikasi {{ $report->verified_at->locale('id')->translatedFormat('d F Y, H:i') }} oleh {{ $report->verifiedBy?->name ?? 'Admin' }}.</p>
                    @endif
                </div>
            </div>

            <div class="rounded-2xl border border-red-200 bg-white shadow-sm">
                <div class="border-b border-red-100 bg-red-50 px-5 py-4">
                    <h2 class="font-bold text-red-900">Minta Revisi</h2>
                    <p class="mt-1 text-xs text-red-700">Saat revisi diminta, fitur operasional Kabid bidang ini akan terkunci lagi.</p>
                </div>
                <form
                    method="POST"
                    action="{{ route('admin.monthly-reports.revision', $report) }}"
                    class="space-y-4 p-5"
                    data-confirm
                    data-confirm-title="Kirim Permintaan Revisi?"
                    data-confirm-message="Kabid akan diminta memperbaiki laporan ini dan fitur operasional bidang tersebut akan terkunci kembali sampai laporan diperbaiki dan dikirim ulang."
                    data-confirm-button="Ya, Minta Revisi"
                    data-confirm-tone="warning">
                    @csrf
                    @method('PATCH')
                    <textarea name="revision_note" rows="5" required maxlength="5000" placeholder="Tuliskan bagian yang harus diperbaiki..." class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-red-500 focus:ring-red-500">{{ old('revision_note', $report->revision_note) }}</textarea>
                    @error('revision_note')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
                    <button type="submit" class="w-full rounded-xl bg-red-600 px-4 py-3 text-sm font-semibold text-white hover:bg-red-700">Kirim Permintaan Revisi</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection