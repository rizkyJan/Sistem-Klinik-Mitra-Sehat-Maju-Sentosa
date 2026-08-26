@extends('layouts.karyawan')

@section('title', 'Reimburse Karyawan')

@section('page-title', 'Reimburse')

@section('content')

<div class="space-y-6">

    {{-- ============================================================
        PAGE HEADER
    ============================================================ --}}
    <div
        class="flex flex-col gap-4
               sm:flex-row sm:items-center sm:justify-between">

        <div>
            <h1 class="text-2xl font-bold text-slate-800">
                Reimburse Saya
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Ajukan penggantian biaya pembelian dan pantau statusnya.
            </p>
        </div>

        <a
            href="{{ route('karyawan.reimbursements.create') }}"
            class="inline-flex items-center justify-center gap-2
                   rounded-lg bg-blue-600 px-4 py-2.5
                   text-sm font-medium text-white
                   transition hover:bg-blue-700">

            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor">
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 4v16m8-8H4" />
            </svg>

            Ajukan Reimburse
        </a>
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
                        Belum diperiksa admin
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
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0
                               9 9 0 0118 0z" />
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
                        Menunggu pembayaran
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
                        Tidak disetujui admin
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

        {{-- Paid total --}}
        <div
            class="rounded-xl border border-slate-200
                   bg-white p-5 shadow-sm">

            <div class="flex items-center justify-between gap-4">
                <div class="min-w-0">
                    <p class="text-sm text-slate-500">
                        Total Dibayar
                    </p>

                    <p
                        class="mt-2 truncate text-2xl font-bold text-slate-800"
                        title="Rp{{ number_format($paidTotal, 0, ',', '.') }}">
                        Rp{{ number_format($paidTotal, 0, ',', '.') }}
                    </p>

                    <p class="mt-1 text-xs text-slate-400">
                        Seluruh reimburse paid
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
        FILTER + TABLE
    ============================================================ --}}
    <div
        class="overflow-hidden rounded-xl
               border border-slate-200 bg-white shadow-sm">

        <div
            class="flex flex-col gap-4 border-b border-slate-200 p-5
                   sm:flex-row sm:items-end sm:justify-between">

            <div>
                <h2 class="font-semibold text-slate-800">
                    Riwayat Pengajuan
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Klik detail untuk melihat bukti dan proses pengajuan.
                </p>
            </div>

            <form
                method="GET"
                action="{{ route('karyawan.reimbursements.index') }}"
                class="flex flex-col gap-2 sm:flex-row">

                <select
                    name="status"
                    class="rounded-lg border-slate-300 text-sm
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

                <button
                    type="submit"
                    class="rounded-lg bg-slate-800
                           px-4 py-2 text-sm font-medium text-white
                           transition hover:bg-slate-700">
                    Tampilkan
                </button>

                @if(request()->filled('status'))
                <a
                    href="{{ route('karyawan.reimbursements.index') }}"
                    class="rounded-lg border border-slate-300
                               px-4 py-2 text-center text-sm font-medium
                               text-slate-600 transition hover:bg-slate-50">
                    Reset
                </a>
                @endif
            </form>
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
                            Pembelian
                        </th>

                        <th
                            class="px-5 py-3 text-left
                                   text-xs font-semibold uppercase
                                   tracking-wide text-slate-500">
                            Barang / Keperluan
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
                            <p class="text-sm text-slate-700">
                                {{ $reimbursement->purchase_date->format('d/m/Y') }}
                            </p>

                            <p class="mt-1 text-xs text-slate-500">
                                {{ $reimbursement->category_label }}
                            </p>
                        </td>

                        <td class="max-w-xs px-5 py-4">
                            <p
                                class="truncate text-sm font-medium text-slate-800"
                                title="{{ $reimbursement->item_name }}">
                                {{ $reimbursement->item_name }}
                            </p>

                            <p
                                class="mt-1 truncate text-xs text-slate-500"
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
                                        'karyawan.reimbursements.show',
                                        $reimbursement
                                    ) }}"
                                class="inline-flex items-center rounded-lg
                                           border border-slate-300
                                           px-3 py-2 text-sm font-medium
                                           text-slate-700 transition
                                           hover:border-blue-300
                                           hover:bg-blue-50 hover:text-blue-700">
                                Detail
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
                                Belum ada pengajuan reimburse
                            </h3>

                            <p class="mt-1 text-sm text-slate-500">
                                Ajukan penggantian biaya pembelian pertama Anda.
                            </p>

                            <a
                                href="{{ route(
                                        'karyawan.reimbursements.create'
                                    ) }}"
                                class="mt-4 inline-flex rounded-lg
                                           bg-blue-600 px-4 py-2
                                           text-sm font-medium text-white
                                           hover:bg-blue-700">
                                Ajukan Reimburse
                            </a>
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