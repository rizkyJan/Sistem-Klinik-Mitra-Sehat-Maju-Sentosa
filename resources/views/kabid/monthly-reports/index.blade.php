@extends('layouts.kabid')

@section('title', 'Laporan Bulanan Bidang')
@section('page-title', 'Laporan Bulanan Bidang')

@section('content')
@php
    $periodLabel = $status['period']->copy()->locale('id')->translatedFormat('F Y');
    $canUpload = $status['window_open']
        && (!$currentReport || $currentReport->status === \App\Models\DepartmentMonthlyReport::STATUS_REVISION);
@endphp

<div class="space-y-6">
    @if(session('success'))<div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-800 shadow-sm">{{ session('success') }}</div>@endif
    @if(session('warning'))<div class="rounded-2xl border border-amber-200 bg-amber-50 px-5 py-4 text-sm font-medium text-amber-800 shadow-sm">{{ session('warning') }}</div>@endif
    @if(session('error'))<div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm font-medium text-red-800 shadow-sm">{{ session('error') }}</div>@endif
    @if(session('info'))<div class="rounded-2xl border border-blue-200 bg-blue-50 px-5 py-4 text-sm font-medium text-blue-800 shadow-sm">{{ session('info') }}</div>@endif

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <div class="flex items-center gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M9 12h6m-6 4h6M7 4h7l3 3v13H7z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-slate-900">Laporan Bulanan Bidang</h1>
                        <p class="mt-1 text-sm text-slate-500">
                            Satu laporan per bulan untuk bidang <strong>{{ $user->department?->name ?? '-' }}</strong>.
                        </p>
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-blue-100 bg-blue-50 px-4 py-3">
                <p class="text-xs font-semibold uppercase tracking-wide text-blue-700">Periode</p>
                <p class="mt-1 font-bold text-blue-900">{{ $periodLabel }}</p>
            </div>
        </div>
    </div>

    @if(!$status['has_department'])
        <div class="rounded-2xl border border-red-200 bg-red-50 p-6 shadow-sm">
            <h2 class="font-bold text-red-900">Bidang belum ditentukan</h2>
            <p class="mt-2 text-sm leading-relaxed text-red-700">
                Akun Kabid Anda belum terhubung ke bidang. Hubungi Admin agar bidang ditentukan terlebih dahulu.
            </p>
        </div>
    @elseif(!$status['window_open'])
        <div class="rounded-2xl border border-blue-200 bg-blue-50 p-6 shadow-sm">
            <h2 class="font-bold text-blue-900">Belum masuk periode wajib upload</h2>
            <p class="mt-2 text-sm leading-relaxed text-blue-700">
                Laporan {{ $periodLabel }} mulai diwajibkan pada tanggal
                <strong>{{ $status['required_from_day'] }}</strong>. Sebelum tanggal tersebut fitur operasional tetap berjalan normal.
            </p>
        </div>
    @endif

    @if($currentReport)
        @php
            $statusClasses = match($currentReport->status) {
                \App\Models\DepartmentMonthlyReport::STATUS_VERIFIED => 'border-emerald-200 bg-emerald-50 text-emerald-800',
                \App\Models\DepartmentMonthlyReport::STATUS_REVISION => 'border-red-200 bg-red-50 text-red-800',
                default => 'border-amber-200 bg-amber-50 text-amber-800',
            };
        @endphp
        <div class="rounded-2xl border p-6 shadow-sm {{ $statusClasses }}">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide">Status laporan saat ini</p>
                    <h2 class="mt-1 text-xl font-bold">{{ $currentReport->statusLabel() }}</h2>
                    <p class="mt-2 text-sm">
                        File: <strong>{{ $currentReport->original_name }}</strong>
                        @if($currentReport->submitted_at)
                            • dikirim {{ $currentReport->submitted_at->locale('id')->translatedFormat('d F Y, H:i') }}
                        @endif
                    </p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('kabid.monthly-reports.preview', $currentReport) }}" target="_blank"
                       class="rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm border border-slate-200 transition hover:bg-slate-50">
                        Preview
                    </a>
                    <a href="{{ route('kabid.monthly-reports.download', $currentReport) }}"
                       class="rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800">
                        Download
                    </a>
                </div>
            </div>

            @if($currentReport->revision_note)
                <div class="mt-5 rounded-xl bg-white/80 p-4 text-sm leading-relaxed text-red-800">
                    <p class="font-bold">Catatan revisi Admin</p>
                    <p class="mt-2 whitespace-pre-line">{{ $currentReport->revision_note }}</p>
                </div>
            @endif

            @if($currentReport->admin_note)
                <div class="mt-4 rounded-xl bg-white/80 p-4 text-sm leading-relaxed">
                    <p class="font-bold">Catatan Admin</p>
                    <p class="mt-2 whitespace-pre-line">{{ $currentReport->admin_note }}</p>
                </div>
            @endif
        </div>
    @endif

    @if($canUpload)
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-6 py-4">
                <h2 class="text-lg font-bold text-slate-900">
                    {{ $currentReport ? 'Upload Revisi Laporan' : 'Upload Laporan ' . $periodLabel }}
                </h2>
                <p class="mt-1 text-sm text-slate-500">
                    Format: {{ strtoupper(implode(', ', $allowedExtensions)) }}. Maksimum {{ number_format($maxFileSizeKb / 1024, 0) }} MB.
                </p>
            </div>

            <form method="POST" action="{{ route('kabid.monthly-reports.store') }}" enctype="multipart/form-data" class="space-y-5 p-6">
                @csrf

                <div>
                    <label for="report_file" class="mb-2 block text-sm font-semibold text-slate-700">
                        File Laporan <span class="text-red-500">*</span>
                    </label>
                    <input id="report_file" type="file" name="report_file" required
                           accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png"
                           class="block w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-700 shadow-sm file:mr-4 file:rounded-lg file:border-0 file:bg-blue-50 file:px-4 file:py-2 file:font-semibold file:text-blue-700 hover:file:bg-blue-100">
                    @error('report_file')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="employee_note" class="mb-2 block text-sm font-semibold text-slate-700">
                        Catatan Kabid <span class="font-normal text-slate-400">(opsional)</span>
                    </label>
                    <textarea id="employee_note" name="employee_note" rows="4" maxlength="3000"
                              placeholder="Catatan singkat mengenai isi laporan..."
                              class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('employee_note', $currentReport?->employee_note) }}</textarea>
                    @error('employee_note')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm leading-relaxed text-amber-800">
                    Setelah file berhasil dikirim, penguncian fitur operasional akan dilepas. Jika Admin meminta revisi, fitur akan terkunci kembali sampai file revisi dikirim.
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                            class="rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700">
                        {{ $currentReport ? 'Kirim Ulang Revisi' : 'Kirim Laporan' }}
                    </button>
                </div>
            </form>
        </div>
    @endif

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 px-6 py-4">
            <h2 class="text-lg font-bold text-slate-900">Riwayat Laporan Bulanan</h2>
            <p class="mt-1 text-sm text-slate-500">Seluruh laporan bulanan untuk bidang Anda.</p>
        </div>

        @if($history->count())
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Periode</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">File</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Status</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($history as $item)
                            <tr class="hover:bg-slate-50/70">
                                <td class="px-5 py-4 text-sm font-semibold text-slate-900">
                                    {{ $item->period->copy()->locale('id')->translatedFormat('F Y') }}
                                </td>
                                <td class="px-5 py-4">
                                    <p class="max-w-xs truncate text-sm font-medium text-slate-700">{{ $item->original_name }}</p>
                                    <p class="mt-1 text-xs text-slate-400">{{ number_format($item->size / 1024 / 1024, 2) }} MB</p>
                                </td>
                                <td class="px-5 py-4 text-sm font-semibold text-slate-700">{{ $item->statusLabel() }}</td>
                                <td class="px-5 py-4 text-right">
                                    <a href="{{ route('kabid.monthly-reports.preview', $item) }}" target="_blank" class="text-sm font-semibold text-blue-600 hover:text-blue-700">Preview</a>
                                    <span class="px-2 text-slate-300">|</span>
                                    <a href="{{ route('kabid.monthly-reports.download', $item) }}" class="text-sm font-semibold text-slate-600 hover:text-slate-800">Download</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($history->hasPages())
                <div class="border-t border-slate-200 px-6 py-4">{{ $history->links() }}</div>
            @endif
        @else
            <div class="px-6 py-12 text-center text-sm text-slate-500">Belum ada riwayat laporan bulanan.</div>
        @endif
    </div>
</div>
@endsection
