@extends('layouts.admin')

@section('title', 'Edit Laporan Bulanan')
@section('page-title', 'Edit Laporan Bulanan')

@section('content')
<div class="mx-auto max-w-4xl space-y-6">
    <div>
        <a href="{{ route('admin.monthly-reports.show', $report) }}" class="text-sm font-semibold text-blue-600 hover:text-blue-700">← Kembali ke detail</a>
        <h1 class="mt-2 text-2xl font-bold text-slate-900">Edit Laporan Bulanan</h1>
        <p class="mt-1 text-sm text-slate-500">{{ $report->department?->name }} • {{ $report->period->copy()->locale('id')->translatedFormat('F Y') }}</p>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <form method="POST" action="{{ route('admin.monthly-reports.update', $report) }}" enctype="multipart/form-data" class="space-y-6 p-6">
            @csrf
            @method('PUT')

            <div class="rounded-xl bg-slate-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">File saat ini</p>
                <p class="mt-1 break-all text-sm font-bold text-slate-900">{{ $report->original_name }}</p>
            </div>

            <div>
                <label for="report_file" class="mb-2 block text-sm font-semibold text-slate-700">Ganti File <span class="font-normal text-slate-400">(opsional)</span></label>
                <input id="report_file" type="file" name="report_file" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png" class="block w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm shadow-sm file:mr-4 file:rounded-lg file:border-0 file:bg-blue-50 file:px-4 file:py-2 file:font-semibold file:text-blue-700">
                <p class="mt-2 text-xs text-slate-500">Format: {{ strtoupper(implode(', ', $allowedExtensions)) }}. Maksimum {{ number_format($maxFileSizeKb / 1024, 0) }} MB.</p>
                @error('report_file')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="employee_note" class="mb-2 block text-sm font-semibold text-slate-700">Catatan Kabid</label>
                <textarea id="employee_note" name="employee_note" rows="4" maxlength="3000" class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('employee_note', $report->employee_note) }}</textarea>
                @error('employee_note')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="admin_note" class="mb-2 block text-sm font-semibold text-slate-700">Catatan Admin</label>
                <textarea id="admin_note" name="admin_note" rows="5" maxlength="5000" placeholder="Catatan internal / koreksi dari Admin..." class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('admin_note', $report->admin_note) }}</textarea>
                @error('admin_note')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
                Jika Admin mengganti file, file lama pada private storage akan diganti setelah update database berhasil.
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.monthly-reports.show', $report) }}" class="rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50">Batal</a>
                <button type="submit" class="rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection
