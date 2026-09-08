@extends('layouts.admin')

@section('title', 'Hear You')
@section('page-title', 'Hear You')

@section('content')
@php
$periodLabel = $period->copy()->locale('id')->translatedFormat('F Y');
@endphp

<div class="space-y-6">
    {{-- HEADER --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <div class="flex items-center gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 4v-4z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-slate-900">Hear You</h1>
                        <p class="mt-1 text-sm text-slate-500">
                            Pantau aspirasi bulanan Karyawan dan Kabid, berikan tanggapan, lalu lihat hasil evaluasinya.
                        </p>
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-blue-100 bg-blue-50 px-4 py-3">
                <p class="text-xs font-semibold uppercase tracking-wide text-blue-700">Periode aktif</p>
                <p class="mt-1 font-bold text-blue-900">{{ $periodLabel }}</p>
            </div>
        </div>
    </div>

    {{-- STATISTIK --}}
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Pegawai Wajib</p>
            <p class="mt-2 text-3xl font-bold text-slate-900">{{ $eligibleEmployeeCount }}</p>
        </div>

        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700">Sudah Mengisi</p>
            <p class="mt-2 text-3xl font-bold text-emerald-900">{{ $submittedCount }}</p>
        </div>

        <div class="rounded-2xl border border-red-200 bg-red-50 p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-red-700">Belum Mengisi</p>
            <p class="mt-2 text-3xl font-bold text-red-900">{{ $missingCount }}</p>
        </div>

        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-amber-700">Belum Ditanggapi</p>
            <p class="mt-2 text-3xl font-bold text-amber-900">{{ $pendingResponseCount }}</p>
        </div>

        <div class="rounded-2xl border border-violet-200 bg-violet-50 p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-violet-700">Sudah Dievaluasi</p>
            <p class="mt-2 text-3xl font-bold text-violet-900">{{ $evaluatedCount }}</p>
        </div>
    </div>

    {{-- FILTER --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <form method="GET" action="{{ route('admin.hear-you.index') }}" class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div>
                <label for="period" class="mb-2 block text-sm font-semibold text-slate-700">Periode</label>
                <input
                    id="period"
                    type="month"
                    name="period"
                    value="{{ $period->format('Y-m') }}"
                    class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label for="status" class="mb-2 block text-sm font-semibold text-slate-700">Status</label>
                <select
                    id="status"
                    name="status"
                    class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="all" @selected($statusFilter==='all' )>Semua Status</option>
                    <option value="waiting_response" @selected($statusFilter==='waiting_response' )>Belum Ditanggapi</option>
                    @foreach($statusOptions as $value => $label)
                    <option value="{{ $value }}" @selected($statusFilter===$value)>{{ $label }}</option>
                    @endforeach
                    <option value="evaluated" @selected($statusFilter==='evaluated' )>Sudah Dievaluasi Pegawai</option>
                    <option value="not_evaluated" @selected($statusFilter==='not_evaluated' )>Menunggu Evaluasi Pegawai</option>
                </select>
            </div>

            <div>
                <label for="search" class="mb-2 block text-sm font-semibold text-slate-700">Cari Pegawai</label>
                <input
                    id="search"
                    type="text"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Nama atau NIP..."
                    class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div class="flex items-end gap-2">
                <button
                    type="submit"
                    class="inline-flex flex-1 items-center justify-center rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700">
                    Terapkan Filter
                </button>
                <a
                    href="{{ route('admin.hear-you.index') }}"
                    class="inline-flex items-center justify-center rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- DAFTAR ASPIRASI --}}
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 px-6 py-4">
            <h2 class="text-lg font-bold text-slate-900">Aspirasi {{ $periodLabel }}</h2>
            <p class="mt-1 text-sm text-slate-500">Klik detail untuk membaca lengkap dan memberikan tanggapan.</p>
        </div>

        @if($feedbacks->count())
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Pegawai</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Jenis</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Aspirasi</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Tindak Lanjut</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Evaluasi</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @foreach($feedbacks as $feedback)
                    <tr class="align-top hover:bg-slate-50/70">
                        <td class="px-5 py-4">
                            <p class="text-sm font-semibold text-slate-900">{{ $feedback->user?->name ?? 'Pegawai dihapus' }}</p>
                            <p class="mt-1 text-xs text-slate-500">
                                {{ strtoupper($feedback->user?->role ?? '-') }}
                                @if($feedback->user?->department)
                                • {{ $feedback->user->department->name }}
                                @endif
                            </p>
                        </td>
                        <td class="px-5 py-4">
                            <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600">
                                {{ $feedback->categoryLabel() }}
                            </span>
                        </td>
                        <td class="max-w-md px-5 py-4">
                            <p class="line-clamp-3 text-sm leading-relaxed text-slate-700">{{ $feedback->message }}</p>
                        </td>
                        <td class="px-5 py-4">
                            <span class="rounded-full {{ $feedback->admin_response ? 'bg-blue-50 text-blue-700' : 'bg-amber-50 text-amber-700' }} px-2.5 py-1 text-xs font-semibold">
                                {{ $feedback->followUpStatusLabel() }}
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            @if($feedback->evaluated_at)
                            <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">
                                {{ $feedback->evaluationLabel() }}
                            </span>
                            @else
                            <span class="text-xs text-slate-400">Belum ada</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-right">
                            <a
                                href="{{ route('admin.hear-you.show', $feedback) }}"
                                class="inline-flex rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-blue-700">
                                Detail
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($feedbacks->hasPages())
        <div class="border-t border-slate-200 px-6 py-4">
            {{ $feedbacks->links() }}
        </div>
        @endif
        @else
        <div class="px-6 py-12 text-center">
            <p class="text-sm font-medium text-slate-600">Tidak ada Hear You yang cocok dengan filter.</p>
        </div>
        @endif
    </div>

    {{-- PEGAWAI BELUM MENGISI --}}
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 px-6 py-4">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Pegawai Belum Mengisi</h2>
                    <p class="mt-1 text-sm text-slate-500">Karyawan/Kabid aktif dan approved yang belum mengirim Hear You {{ $periodLabel }}.</p>
                </div>
                <span class="w-fit rounded-full {{ $missingUsers->isEmpty() ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700' }} px-3 py-1 text-xs font-bold">
                    {{ $missingUsers->count() }} orang
                </span>
            </div>
        </div>

        @if($missingUsers->isNotEmpty())
        <div class="divide-y divide-slate-100">
            @foreach($missingUsers as $employee)
            <div class="flex flex-col gap-1 px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm font-semibold text-slate-900">{{ $employee->name }}</p>
                    <p class="mt-1 text-xs text-slate-500">
                        {{ strtoupper($employee->role) }}
                        @if($employee->department)
                        • {{ $employee->department->name }}
                        @endif
                    </p>
                </div>
                <p class="text-xs text-slate-400">NIP {{ $employee->nip ?? $employee->nik ?? '-' }}</p>
            </div>
            @endforeach
        </div>
        @else
        <div class="px-6 py-10 text-center">
            <p class="text-sm font-semibold text-emerald-700">Semua pegawai wajib sudah mengisi pada periode ini.</p>
        </div>
        @endif
    </div>
</div>
@endsection