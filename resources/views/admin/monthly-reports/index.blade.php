@extends('layouts.admin')

@section('title', 'Laporan Bulanan Bidang')
@section('page-title', 'Laporan Bulanan Bidang')

@section('content')
@php
    $periodLabel = $period->copy()->locale('id')->translatedFormat('F Y');
@endphp

<div class="space-y-6">
    @if(session('success'))<div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-800 shadow-sm">{{ session('success') }}</div>@endif
    @if(session('warning'))<div class="rounded-2xl border border-amber-200 bg-amber-50 px-5 py-4 text-sm font-medium text-amber-800 shadow-sm">{{ session('warning') }}</div>@endif
    @if(session('error'))<div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm font-medium text-red-800 shadow-sm">{{ session('error') }}</div>@endif

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex items-center gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M9 12h6m-6 4h6M7 4h7l3 3v13H7z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-slate-900">Laporan Bulanan per Bidang</h1>
                    <p class="mt-1 text-sm text-slate-500">Pantau kelengkapan, periksa file, edit data/file, verifikasi, atau minta revisi.</p>
                </div>
            </div>
            <div class="rounded-xl border border-blue-100 bg-blue-50 px-4 py-3">
                <p class="text-xs font-semibold uppercase tracking-wide text-blue-700">Periode</p>
                <p class="mt-1 font-bold text-blue-900">{{ $periodLabel }}</p>
            </div>
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-xs font-semibold uppercase text-slate-500">Bidang Aktif</p><p class="mt-2 text-3xl font-bold text-slate-900">{{ $totalDepartmentCount }}</p></div>
        <div class="rounded-2xl border border-blue-200 bg-blue-50 p-5 shadow-sm"><p class="text-xs font-semibold uppercase text-blue-700">Sudah Upload</p><p class="mt-2 text-3xl font-bold text-blue-900">{{ $submittedCount }}</p></div>
        <div class="rounded-2xl border border-red-200 bg-red-50 p-5 shadow-sm"><p class="text-xs font-semibold uppercase text-red-700">Belum Upload</p><p class="mt-2 text-3xl font-bold text-red-900">{{ $missingCount }}</p></div>
        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 shadow-sm"><p class="text-xs font-semibold uppercase text-amber-700">Menunggu</p><p class="mt-2 text-3xl font-bold text-amber-900">{{ $pendingVerificationCount }}</p></div>
        <div class="rounded-2xl border border-violet-200 bg-violet-50 p-5 shadow-sm"><p class="text-xs font-semibold uppercase text-violet-700">Revisi</p><p class="mt-2 text-3xl font-bold text-violet-900">{{ $revisionCount }}</p></div>
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5 shadow-sm"><p class="text-xs font-semibold uppercase text-emerald-700">Terverifikasi</p><p class="mt-2 text-3xl font-bold text-emerald-900">{{ $verifiedCount }}</p></div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <form method="GET" action="{{ route('admin.monthly-reports.index') }}" class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div>
                <label for="period" class="mb-2 block text-sm font-semibold text-slate-700">Periode</label>
                <input id="period" type="month" name="period" value="{{ $period->format('Y-m') }}" class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <label for="status" class="mb-2 block text-sm font-semibold text-slate-700">Status</label>
                <select id="status" name="status" class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="all" @selected($statusFilter === 'all')>Semua Status</option>
                    <option value="missing" @selected($statusFilter === 'missing')>Belum Upload</option>
                    <option value="submitted" @selected($statusFilter === 'submitted')>Menunggu Verifikasi</option>
                    <option value="revision" @selected($statusFilter === 'revision')>Perlu Revisi</option>
                    <option value="verified" @selected($statusFilter === 'verified')>Diverifikasi</option>
                </select>
            </div>
            <div>
                <label for="search" class="mb-2 block text-sm font-semibold text-slate-700">Cari Bidang / Kabid</label>
                <input id="search" type="text" name="search" value="{{ $search }}" placeholder="Nama bidang, Kabid, NIP..." class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">Terapkan</button>
                <a href="{{ route('admin.monthly-reports.index') }}" class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50">Reset</a>
            </div>
        </form>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 px-6 py-4">
            <h2 class="text-lg font-bold text-slate-900">Status Bidang — {{ $periodLabel }}</h2>
            <p class="mt-1 text-sm text-slate-500">Bidang tanpa laporan tetap ditampilkan agar kelengkapan mudah dipantau.</p>
        </div>

        @if($departments->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Bidang</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Kabid</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">File</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Status</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($departments as $department)
                            @php
                                $report = $department->monthlyReports->first();
                                $kabid = $department->users->first();
                            @endphp
                            <tr class="align-top hover:bg-slate-50/70">
                                <td class="px-5 py-4"><p class="text-sm font-bold text-slate-900">{{ $department->name }}</p></td>
                                <td class="px-5 py-4">
                                    <p class="text-sm font-semibold text-slate-800">{{ $kabid?->name ?? 'Belum ada Kabid aktif' }}</p>
                                    @if($kabid?->nip)<p class="mt-1 text-xs text-slate-500">NIP {{ $kabid->nip }}</p>@endif
                                </td>
                                <td class="px-5 py-4">
                                    @if($report)
                                        <p class="max-w-xs truncate text-sm text-slate-700">{{ $report->original_name }}</p>
                                        <p class="mt-1 text-xs text-slate-400">{{ $report->submitted_at?->format('d/m/Y H:i') }}</p>
                                    @else
                                        <span class="text-sm text-slate-400">Belum ada file</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4">
                                    @if(!$report)
                                        <span class="rounded-full bg-red-50 px-2.5 py-1 text-xs font-bold text-red-700">Belum Upload</span>
                                    @elseif($report->status === 'verified')
                                        <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-700">Diverifikasi</span>
                                    @elseif($report->status === 'revision')
                                        <span class="rounded-full bg-violet-50 px-2.5 py-1 text-xs font-bold text-violet-700">Perlu Revisi</span>
                                    @else
                                        <span class="rounded-full bg-amber-50 px-2.5 py-1 text-xs font-bold text-amber-700">Menunggu Verifikasi</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-right">
                                    @if($report)
                                        <a href="{{ route('admin.monthly-reports.show', $report) }}" class="text-sm font-semibold text-blue-600 hover:text-blue-700">Detail</a>
                                    @else
                                        <span class="text-xs text-slate-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="px-6 py-12 text-center text-sm text-slate-500">Tidak ada data bidang sesuai filter.</div>
        @endif
    </div>
</div>
@endsection
