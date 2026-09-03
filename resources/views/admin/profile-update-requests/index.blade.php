@extends('layouts.admin')

@section('title', 'Perubahan Profil')
@section('page-title', 'Perubahan Profil')

@section('content')
<div class="space-y-6">

    @if(session('success'))
    <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
        {{ session('success') }}
    </div>
    @endif

    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-900 sm:text-2xl">
                Pengajuan Perubahan Profil
            </h1>

            <p class="mt-1 text-sm leading-6 text-slate-500">
                Periksa perubahan biodata Karyawan/Kabid sebelum data aktif diperbarui.
            </p>
        </div>
    </div>


    {{-- STATISTIK --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <a
            href="{{ route('admin.profile-updates.index', ['status' => 'pending']) }}"
            class="rounded-2xl border border-amber-200 bg-white p-5 shadow-sm transition hover:border-amber-300">
            <p class="text-sm font-medium text-slate-500">
                Menunggu
            </p>
            <p class="mt-2 text-3xl font-bold text-amber-600">
                {{ $counts['pending'] }}
            </p>
        </a>

        <a
            href="{{ route('admin.profile-updates.index', ['status' => 'approved']) }}"
            class="rounded-2xl border border-emerald-200 bg-white p-5 shadow-sm transition hover:border-emerald-300">
            <p class="text-sm font-medium text-slate-500">
                Disetujui
            </p>
            <p class="mt-2 text-3xl font-bold text-emerald-600">
                {{ $counts['approved'] }}
            </p>
        </a>

        <a
            href="{{ route('admin.profile-updates.index', ['status' => 'rejected']) }}"
            class="rounded-2xl border border-red-200 bg-white p-5 shadow-sm transition hover:border-red-300">
            <p class="text-sm font-medium text-slate-500">
                Ditolak
            </p>
            <p class="mt-2 text-3xl font-bold text-red-600">
                {{ $counts['rejected'] }}
            </p>
        </a>
    </div>


    {{-- FILTER --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
        <form
            method="GET"
            action="{{ route('admin.profile-updates.index') }}"
            class="grid gap-3 md:grid-cols-[1fr_190px_auto]">

            <div>
                <label for="search" class="sr-only">
                    Cari pegawai
                </label>

                <input
                    id="search"
                    name="search"
                    type="search"
                    value="{{ $search }}"
                    placeholder="Cari nama, NIP, NIK KTP, atau email..."
                    class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <select
                    name="status"
                    class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="pending" @selected($status==='pending' )>
                        Menunggu
                    </option>
                    <option value="approved" @selected($status==='approved' )>
                        Disetujui
                    </option>
                    <option value="rejected" @selected($status==='rejected' )>
                        Ditolak
                    </option>
                </select>
            </div>

            <button
                type="submit"
                class="rounded-xl bg-slate-800 px-5 py-2.5 text-sm font-semibold text-white hover:bg-slate-900">
                Terapkan
            </button>
        </form>
    </div>


    {{-- DESKTOP TABLE --}}
    <div class="hidden overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm md:block">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr class="text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <th class="px-5 py-3">Pegawai</th>
                        <th class="px-5 py-3">Role / Bidang</th>
                        <th class="px-5 py-3">Perubahan</th>
                        <th class="px-5 py-3">Diajukan</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse($requests as $profileRequest)
                    <tr class="align-top">
                        <td class="px-5 py-4">
                            <p class="font-semibold text-slate-800">
                                {{ $profileRequest->user?->name ?? 'User tidak tersedia' }}
                            </p>

                            <p class="mt-1 text-xs text-slate-500">
                                NIP:
                                {{ $profileRequest->user?->nip
                                    ?? $profileRequest->user?->nik
                                    ?? '-' }}
                            </p>
                        </td>

                        <td class="px-5 py-4 text-sm text-slate-600">
                            <p class="font-medium">
                                {{ $profileRequest->user?->role === 'kabid'
                                    ? 'Kabid'
                                    : 'Karyawan' }}
                            </p>

                            <p class="mt-1 text-xs text-slate-500">
                                {{ $profileRequest->user?->department?->name ?? '-' }}
                            </p>
                        </td>

                        <td class="px-5 py-4">
                            <span class="inline-flex rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700">
                                {{ count($profileRequest->new_data ?? []) }}
                                field
                            </span>

                            @if($profileRequest->new_photo_path)
                            <span class="ml-1 inline-flex rounded-full bg-violet-50 px-2.5 py-1 text-xs font-semibold text-violet-700">
                                + foto
                            </span>
                            @endif
                        </td>

                        <td class="px-5 py-4 text-sm text-slate-600">
                            {{ $profileRequest->created_at?->translatedFormat('d M Y H:i') }}
                        </td>

                        <td class="px-5 py-4">
                            @if($profileRequest->status === 'pending')
                            <span class="inline-flex rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-700">
                                Menunggu
                            </span>
                            @elseif($profileRequest->status === 'approved')
                            <span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700">
                                Disetujui
                            </span>
                            @else
                            <span class="inline-flex rounded-full bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-700">
                                Ditolak
                            </span>
                            @endif
                        </td>

                        <td class="px-5 py-4 text-right">
                            <a
                                href="{{ route(
                                    'admin.profile-updates.show',
                                    $profileRequest
                                ) }}"
                                class="inline-flex rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                                Periksa
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td
                            colspan="6"
                            class="px-6 py-12 text-center text-sm text-slate-500">
                            Tidak ada pengajuan pada filter ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>


    {{-- MOBILE CARDS --}}
    <div class="space-y-3 md:hidden">
        @forelse($requests as $profileRequest)
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <p class="break-words font-semibold text-slate-800">
                        {{ $profileRequest->user?->name ?? 'User tidak tersedia' }}
                    </p>

                    <p class="mt-1 text-xs text-slate-500">
                        {{ $profileRequest->user?->role === 'kabid'
                            ? 'Kabid'
                            : 'Karyawan' }}
                        •
                        {{ $profileRequest->user?->department?->name ?? '-' }}
                    </p>
                </div>

                @if($profileRequest->status === 'pending')
                <span class="shrink-0 rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-700">
                    Menunggu
                </span>
                @elseif($profileRequest->status === 'approved')
                <span class="shrink-0 rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700">
                    Disetujui
                </span>
                @else
                <span class="shrink-0 rounded-full bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-700">
                    Ditolak
                </span>
                @endif
            </div>

            <div class="mt-4 grid grid-cols-2 gap-3 text-sm">
                <div class="rounded-xl bg-slate-50 p-3">
                    <p class="text-xs text-slate-500">
                        NIP
                    </p>
                    <p class="mt-1 break-all font-medium text-slate-700">
                        {{ $profileRequest->user?->nip
                            ?? $profileRequest->user?->nik
                            ?? '-' }}
                    </p>
                </div>

                <div class="rounded-xl bg-slate-50 p-3">
                    <p class="text-xs text-slate-500">
                        Perubahan
                    </p>
                    <p class="mt-1 font-medium text-slate-700">
                        {{ count($profileRequest->new_data ?? []) }}
                        field
                        {{ $profileRequest->new_photo_path ? '+ foto' : '' }}
                    </p>
                </div>
            </div>

            <p class="mt-3 text-xs text-slate-500">
                Diajukan
                {{ $profileRequest->created_at?->translatedFormat('d M Y H:i') }}
            </p>

            <a
                href="{{ route(
                    'admin.profile-updates.show',
                    $profileRequest
                ) }}"
                class="mt-4 inline-flex w-full items-center justify-center rounded-xl bg-slate-800 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-900">
                Periksa Pengajuan
            </a>
        </div>
        @empty
        <div class="rounded-2xl border border-slate-200 bg-white px-5 py-12 text-center text-sm text-slate-500 shadow-sm">
            Tidak ada pengajuan pada filter ini.
        </div>
        @endforelse
    </div>


    @if($requests->hasPages())
    <div>
        {{ $requests->links() }}
    </div>
    @endif
</div>
@endsection