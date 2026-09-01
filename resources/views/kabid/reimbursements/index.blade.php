@extends('layouts.kabid')

@section('title', 'Riwayat Reimburse')
@section('page-title', 'Riwayat Reimburse')

@section('content')

<x-toast-notification />

<div class="space-y-6">

    {{-- ============================================================
        PAGE HEADER
    ============================================================ --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

        <div>
            <h1 class="text-2xl font-bold text-slate-800">
                Riwayat Reimburse
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Pantau status penggantian biaya yang pernah Anda ajukan.
            </p>
        </div>

        <a
            href="{{ route('kabid.reimbursements.create') }}"
            class="inline-flex items-center justify-center gap-2 rounded-lg
                   bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white
                   transition hover:bg-blue-700">

            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-4 w-4"
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


    {{-- ============================================================
        STATISTICS
    ============================================================ --}}
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">

        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">
                Menunggu
            </p>

            <p class="mt-2 text-3xl font-bold text-amber-600">
                {{ $pendingCount }}
            </p>
        </div>


        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">
                Disetujui
            </p>

            <p class="mt-2 text-3xl font-bold text-emerald-600">
                {{ $approvedCount }}
            </p>
        </div>


        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">
                Ditolak
            </p>

            <p class="mt-2 text-3xl font-bold text-red-600">
                {{ $rejectedCount }}
            </p>
        </div>


        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">
                Total Sudah Dibayar
            </p>

            <p class="mt-2 text-xl font-bold text-blue-700">
                Rp{{ number_format($paidTotal, 0, ',', '.') }}
            </p>
        </div>
    </div>


    {{-- ============================================================
        TABLE
    ============================================================ --}}
    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">

        {{-- FILTER --}}
        <div class="border-b border-slate-200 p-5">

            <form
                method="GET"
                action="{{ route('kabid.reimbursements.index') }}"
                class="flex flex-col gap-3 sm:flex-row sm:items-end">

                <div class="w-full sm:max-w-xs">

                    <label
                        for="status"
                        class="mb-1 block text-xs font-semibold uppercase
                               tracking-wide text-slate-500">
                        Status
                    </label>

                    <select
                        id="status"
                        name="status"
                        class="w-full rounded-lg border-slate-300 text-sm">

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


                <button
                    type="submit"
                    class="rounded-lg bg-slate-800 px-4 py-2.5
                           text-sm font-medium text-white hover:bg-slate-700">
                    Tampilkan
                </button>


                @if(request()->filled('status'))

                <a
                    href="{{ route('kabid.reimbursements.index') }}"
                    class="rounded-lg border border-slate-300 bg-white
                               px-4 py-2.5 text-center text-sm font-medium
                               text-slate-600 hover:bg-slate-50">
                    Reset
                </a>

                @endif

            </form>
        </div>


        <div class="overflow-x-auto">

            <table class="min-w-full divide-y divide-slate-200">

                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                            Kode / Tanggal
                        </th>

                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                            Keperluan
                        </th>

                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                            Nominal
                        </th>

                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                            Status
                        </th>

                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">
                            Aksi
                        </th>
                    </tr>
                </thead>


                <tbody class="divide-y divide-slate-100 bg-white">

                    @forelse($reimbursements as $reimbursement)

                    <tr class="align-top">

                        <td class="px-5 py-4">

                            <p class="font-semibold text-slate-800">
                                {{ $reimbursement->code }}
                            </p>

                            <p class="mt-1 text-xs text-slate-500">
                                Beli:
                                {{ $reimbursement->purchase_date->format('d/m/Y') }}
                            </p>

                            <p class="text-xs text-slate-400">
                                Diajukan:
                                {{ $reimbursement->created_at->format('d/m/Y H:i') }}
                            </p>
                        </td>


                        <td class="px-5 py-4 text-sm">

                            <p class="font-medium text-slate-800">
                                {{ $reimbursement->item_name }}
                            </p>

                            <p class="mt-1 text-xs text-slate-500">
                                {{ $reimbursement->category_label }}

                                @if($reimbursement->merchant_name)
                                • {{ $reimbursement->merchant_name }}
                                @endif
                            </p>
                        </td>


                        <td class="px-5 py-4 text-sm font-semibold text-slate-800">
                            Rp{{ number_format($reimbursement->amount, 0, ',', '.') }}
                        </td>


                        <td class="px-5 py-4">

                            @php
                            $statusClass = match($reimbursement->status) {
                            'pending' =>
                            'bg-amber-50 text-amber-700',

                            'approved' =>
                            'bg-emerald-50 text-emerald-700',

                            'rejected' =>
                            'bg-red-50 text-red-700',

                            'paid' =>
                            'bg-blue-50 text-blue-700',

                            default =>
                            'bg-slate-100 text-slate-700',
                            };
                            @endphp

                            <span
                                class="inline-flex rounded-full px-2.5 py-1
                                           text-xs font-semibold {{ $statusClass }}">
                                {{ $reimbursement->status_label }}
                            </span>
                        </td>


                        <td class="px-5 py-4 text-right">

                            <div class="flex flex-wrap justify-end gap-2">

                                <a
                                    href="{{ route(
                                            'kabid.reimbursements.show',
                                            $reimbursement
                                        ) }}"
                                    class="rounded-lg bg-blue-50 px-3 py-2
                                               text-xs font-medium text-blue-700
                                               hover:bg-blue-100">
                                    Detail
                                </a>


                                @if($reimbursement->isPending())

                                <a
                                    href="{{ route(
                                                'kabid.reimbursements.edit',
                                                $reimbursement
                                            ) }}"
                                    class="rounded-lg bg-slate-100 px-3 py-2
                                                   text-xs font-medium text-slate-700
                                                   hover:bg-slate-200">
                                    Edit
                                </a>

                                @endif

                            </div>
                        </td>
                    </tr>

                    @empty

                    <tr>
                        <td
                            colspan="5"
                            class="px-5 py-12 text-center text-sm text-slate-500">

                            Belum ada pengajuan reimburse.

                            <div class="mt-4">
                                <a
                                    href="{{ route('kabid.reimbursements.create') }}"
                                    class="font-semibold text-blue-600 hover:text-blue-700">
                                    Ajukan reimburse pertama
                                </a>
                            </div>
                        </td>
                    </tr>

                    @endforelse

                </tbody>
            </table>
        </div>


        @if($reimbursements->hasPages())

        <div class="border-t border-slate-200 p-5">
            {{ $reimbursements->links() }}
        </div>

        @endif

    </div>
</div>

@endsection