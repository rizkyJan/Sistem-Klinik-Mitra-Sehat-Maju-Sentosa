@extends('layouts.admin')

@section('title', 'Reimbursement')

@section('page-title', 'Reimbursement')

@section('content')

<div class="space-y-6">

    {{-- ============================================================
        HEADER
    ============================================================ --}}
    <div
        class="flex flex-col gap-4
               lg:flex-row lg:items-center lg:justify-between">

        <div>
            <h1 class="text-2xl font-bold text-slate-800">
                Pengajuan Reimbursement
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Periksa pengajuan biaya dari karyawan dan kelola status pembayarannya.
            </p>
        </div>
    </div>

    <x-toast-notification />

    {{-- ============================================================
        STATISTIC CARDS
    ============================================================ --}}
    <div
        class="grid grid-cols-1 gap-4
               sm:grid-cols-2 xl:grid-cols-4">

        {{-- Pending --}}
        <div
            class="rounded-xl border border-slate-200
                   bg-white p-5 shadow-sm">

            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-sm text-slate-500">
                        Menunggu
                    </p>

                    <p class="mt-2 text-3xl font-bold text-slate-800">
                        {{ $pendingCount }}
                    </p>

                    <p class="mt-1 text-xs text-slate-400">
                        Perlu diperiksa
                    </p>
                </div>

                <div
                    class="flex h-12 w-12 shrink-0 items-center justify-center
                           rounded-xl bg-amber-50 text-amber-600">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-6 w-6"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.5"
                            d="M12 8v4l3 3m6-3a9 9 0
                               11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>


        {{-- Approved --}}
        <div
            class="rounded-xl border border-slate-200
                   bg-white p-5 shadow-sm">

            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-sm text-slate-500">
                        Disetujui
                    </p>

                    <p class="mt-2 text-3xl font-bold text-slate-800">
                        {{ $approvedCount }}
                    </p>

                    <p class="mt-1 text-xs text-slate-400">
                        Menunggu dibayar
                    </p>
                </div>

                <div
                    class="flex h-12 w-12 shrink-0 items-center justify-center
                           rounded-xl bg-blue-50 text-blue-600">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-6 w-6"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.5"
                            d="M9 12l2 2 4-4m6 2a9 9 0
                               11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>


        {{-- Rejected --}}
        <div
            class="rounded-xl border border-slate-200
                   bg-white p-5 shadow-sm">

            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-sm text-slate-500">
                        Ditolak
                    </p>

                    <p class="mt-2 text-3xl font-bold text-slate-800">
                        {{ $rejectedCount }}
                    </p>

                    <p class="mt-1 text-xs text-slate-400">
                        Tidak disetujui
                    </p>
                </div>

                <div
                    class="flex h-12 w-12 shrink-0 items-center justify-center
                           rounded-xl bg-red-50 text-red-600">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-6 w-6"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.5"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
            </div>
        </div>


        {{-- Paid this month --}}
        <div
            class="rounded-xl border border-slate-200
                   bg-white p-5 shadow-sm">

            <div class="flex items-center justify-between gap-4">
                <div class="min-w-0">
                    <p class="text-sm text-slate-500">
                        Dibayar Bulan Ini
                    </p>

                    <p
                        class="mt-2 truncate text-2xl font-bold text-slate-800"
                        title="Rp{{ number_format($paidThisMonth, 0, ',', '.') }}">
                        Rp{{ number_format($paidThisMonth, 0, ',', '.') }}
                    </p>

                    <p class="mt-1 text-xs text-slate-400">
                        Total status paid
                    </p>
                </div>

                <div
                    class="flex h-12 w-12 shrink-0 items-center justify-center
                           rounded-xl bg-emerald-50 text-emerald-600">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-6 w-6"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.5"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343
                               2 3 2 3 .895 3 2-1.343 2-3
                               2m0-8c1.11 0 2.08.402 2.599
                               1M12 8V7m0 1v8m0 0v1m0-1
                               c-1.11 0-2.08-.402-2.599-1
                               M21 12a9 9 0 11-18 0 9 9
                               0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>


    {{-- ============================================================
        FILTER
    ============================================================ --}}
    <div
        class="rounded-xl border border-slate-200
               bg-white p-5 shadow-sm">

        <form
            method="GET"
            action="{{ route('admin.reimbursements.index') }}"
            class="grid grid-cols-1 gap-4
                   md:grid-cols-2 xl:grid-cols-12">

            <div class="xl:col-span-6">
                <label
                    for="search"
                    class="mb-2 block text-sm font-medium text-slate-700">
                    Pencarian
                </label>

                <input
                    type="text"
                    id="search"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Kode, nama karyawan, NIK, barang, toko..."
                    class="w-full rounded-lg border-slate-300
                           focus:border-blue-500 focus:ring-blue-500">
            </div>


            <div class="xl:col-span-3">
                <label
                    for="status"
                    class="mb-2 block text-sm font-medium text-slate-700">
                    Status
                </label>

                <select
                    id="status"
                    name="status"
                    class="w-full rounded-lg border-slate-300
                           focus:border-blue-500 focus:ring-blue-500">

                    <option value="">
                        Semua Status
                    </option>

                    <option
                        value="pending"
                        @selected(request('status')==='pending' )>
                        Menunggu
                    </option>

                    <option
                        value="approved"
                        @selected(request('status')==='approved' )>
                        Disetujui
                    </option>

                    <option
                        value="rejected"
                        @selected(request('status')==='rejected' )>
                        Ditolak
                    </option>

                    <option
                        value="paid"
                        @selected(request('status')==='paid' )>
                        Sudah Dibayar
                    </option>
                </select>
            </div>


            <div
                class="flex items-end gap-2
                       md:col-span-2 xl:col-span-3">

                <button
                    type="submit"
                    class="inline-flex flex-1 items-center
                           justify-center rounded-lg bg-slate-800
                           px-4 py-2.5 text-sm font-medium text-white
                           transition hover:bg-slate-700">
                    Cari
                </button>

                @if(request()->filled('search') || request()->filled('status'))
                <a
                    href="{{ route('admin.reimbursements.index') }}"
                    class="inline-flex items-center justify-center
                               rounded-lg border border-slate-300
                               px-4 py-2.5 text-sm font-medium
                               text-slate-600 transition hover:bg-slate-50">
                    Reset
                </a>
                @endif
            </div>

        </form>
    </div>


    {{-- ============================================================
        TABLE
    ============================================================ --}}
    <div
        class="overflow-hidden rounded-xl
               border border-slate-200 bg-white shadow-sm">

        <div class="border-b border-slate-200 px-5 py-4">
            <div>
                <h2 class="font-semibold text-slate-800">
                    Daftar Pengajuan
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Buka detail untuk memeriksa bukti dan memproses pengajuan.
                </p>
            </div>
        </div>


        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">

                <thead class="bg-slate-50">
                    <tr>
                        <th
                            class="whitespace-nowrap px-5 py-3 text-left
                                   text-xs font-semibold uppercase
                                   tracking-wide text-slate-500">
                            Kode
                        </th>

                        <th
                            class="whitespace-nowrap px-5 py-3 text-left
                                   text-xs font-semibold uppercase
                                   tracking-wide text-slate-500">
                            Karyawan
                        </th>

                        <th
                            class="px-5 py-3 text-left
                                   text-xs font-semibold uppercase
                                   tracking-wide text-slate-500">
                            Pembelian
                        </th>

                        <th
                            class="whitespace-nowrap px-5 py-3 text-right
                                   text-xs font-semibold uppercase
                                   tracking-wide text-slate-500">
                            Nominal
                        </th>

                        <th
                            class="whitespace-nowrap px-5 py-3 text-left
                                   text-xs font-semibold uppercase
                                   tracking-wide text-slate-500">
                            Status
                        </th>

                        <th
                            class="whitespace-nowrap px-5 py-3 text-right
                                   text-xs font-semibold uppercase
                                   tracking-wide text-slate-500">
                            Aksi
                        </th>
                    </tr>
                </thead>


                <tbody class="divide-y divide-slate-200 bg-white">

                    @forelse($reimbursements as $reimbursement)

                    <tr class="transition hover:bg-slate-50">

                        <td class="whitespace-nowrap px-5 py-4">
                            <p class="text-sm font-semibold text-slate-800">
                                {{ $reimbursement->code }}
                            </p>

                            <p class="mt-1 text-xs text-slate-400">
                                {{ $reimbursement->created_at->format('d/m/Y H:i') }}
                            </p>
                        </td>


                        <td class="whitespace-nowrap px-5 py-4">
                            <p class="text-sm font-medium text-slate-800">
                                {{ $reimbursement->user?->name ?? '-' }}
                            </p>

                            <p class="mt-1 text-xs text-slate-500">
                                {{ $reimbursement->user?->department?->name ?? 'Tanpa departemen' }}
                            </p>

                            @if($reimbursement->user?->nik)
                            <p class="mt-1 text-xs text-slate-400">
                                NIK: {{ $reimbursement->user->nik }}
                            </p>
                            @endif
                        </td>


                        <td class="max-w-xs px-5 py-4">
                            <p
                                class="truncate text-sm font-medium text-slate-800"
                                title="{{ $reimbursement->item_name }}">
                                {{ $reimbursement->item_name }}
                            </p>

                            <p class="mt-1 text-xs text-slate-500">
                                {{ $reimbursement->purchase_date->format('d/m/Y') }}
                                • {{ $reimbursement->category_label }}
                            </p>

                            <p
                                class="mt-1 truncate text-xs text-slate-400"
                                title="{{ $reimbursement->merchant_name }}">
                                {{ $reimbursement->merchant_name ?: 'Toko tidak diisi' }}
                            </p>
                        </td>


                        <td
                            class="whitespace-nowrap px-5 py-4 text-right
                                       text-sm font-semibold text-slate-800">
                            Rp{{ number_format(
                                    $reimbursement->amount,
                                    0,
                                    ',',
                                    '.'
                                ) }}
                        </td>


                        <td class="whitespace-nowrap px-5 py-4">

                            @if($reimbursement->isPending())
                            <span
                                class="inline-flex rounded-full
                                               bg-amber-50 px-3 py-1
                                               text-xs font-semibold
                                               text-amber-700">
                                {{ $reimbursement->status_label }}
                            </span>

                            @elseif($reimbursement->isApproved())
                            <span
                                class="inline-flex rounded-full
                                               bg-blue-50 px-3 py-1
                                               text-xs font-semibold
                                               text-blue-700">
                                {{ $reimbursement->status_label }}
                            </span>

                            @elseif($reimbursement->isRejected())
                            <span
                                class="inline-flex rounded-full
                                               bg-red-50 px-3 py-1
                                               text-xs font-semibold
                                               text-red-700">
                                {{ $reimbursement->status_label }}
                            </span>

                            @else
                            <span
                                class="inline-flex rounded-full
                                               bg-emerald-50 px-3 py-1
                                               text-xs font-semibold
                                               text-emerald-700">
                                {{ $reimbursement->status_label }}
                            </span>
                            @endif

                        </td>


                        <td class="whitespace-nowrap px-5 py-4 text-right">
                            <a
                                href="{{ route(
                                        'admin.reimbursements.show',
                                        $reimbursement
                                    ) }}"
                                class="inline-flex items-center rounded-lg
                                           border border-slate-300
                                           px-3 py-2 text-sm font-medium
                                           text-slate-700 transition
                                           hover:border-blue-300
                                           hover:bg-blue-50 hover:text-blue-700">
                                Periksa
                            </a>
                        </td>

                    </tr>

                    @empty

                    <tr>
                        <td colspan="6" class="px-6 py-14 text-center">

                            <div
                                class="mx-auto flex h-12 w-12
                                           items-center justify-center
                                           rounded-full bg-slate-100
                                           text-slate-400">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-6 w-6"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="1.5"
                                        d="M9 12h6m-6 4h6M9 8h6
                                               M5 4h14v16H5z" />
                                </svg>
                            </div>

                            <h3
                                class="mt-4 text-sm font-semibold
                                           text-slate-700">
                                Tidak ada pengajuan
                            </h3>

                            <p class="mt-1 text-sm text-slate-500">
                                Belum ada data yang sesuai dengan filter.
                            </p>
                        </td>
                    </tr>

                    @endforelse

                </tbody>
            </table>
        </div>


        @if($reimbursements->hasPages())
        <div class="border-t border-slate-200 px-5 py-4">
            {{ $reimbursements->links() }}
        </div>
        @endif
    </div>

</div>

@endsection