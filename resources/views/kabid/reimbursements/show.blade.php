@extends('layouts.kabid')

@section('title', 'Detail Reimburse')
@section('page-title', 'Detail Reimburse')

@section('content')

<x-toast-notification />
<x-confirm-action-modal />

<div class="mx-auto max-w-6xl space-y-6">

    {{-- ============================================================
        PAGE HEADER
    ============================================================ --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

        <div>
            <h1 class="text-2xl font-bold text-slate-800">
                Detail Reimburse
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                {{ $reimbursement->code }}
            </p>
        </div>


        <div class="flex flex-wrap gap-2">

            @if($reimbursement->isPending())

            <a
                href="{{ route(
                        'kabid.reimbursements.edit',
                        $reimbursement
                    ) }}"
                class="inline-flex items-center justify-center
                           rounded-lg bg-blue-600 px-4 py-2.5
                           text-sm font-semibold text-white
                           hover:bg-blue-700">
                Edit Pengajuan
            </a>

            @endif


            <a
                href="{{ route('kabid.reimbursements.index') }}"
                class="inline-flex items-center justify-center
                       rounded-lg border border-slate-300 bg-white
                       px-4 py-2.5 text-sm font-medium text-slate-600
                       hover:bg-slate-50">
                ← Kembali
            </a>
        </div>
    </div>


    {{-- ============================================================
        STATUS
    ============================================================ --}}
    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">

        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

            <div>

                <p class="text-sm text-slate-500">
                    Status Pengajuan
                </p>

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
                    class="mt-2 inline-flex rounded-full px-3 py-1.5
                           text-sm font-semibold {{ $statusClass }}">
                    {{ $reimbursement->status_label }}
                </span>

                @if($reimbursement->isPending())
                <p class="mt-2 text-xs text-slate-500">
                    Menunggu pemeriksaan Administrator.
                </p>
                @endif
            </div>


            <div class="text-sm text-slate-500 sm:text-right">

                <p>
                    Diajukan pada
                </p>

                <p class="mt-1 font-medium text-slate-700">
                    {{ $reimbursement->created_at->format('d/m/Y H:i') }}
                </p>
            </div>
        </div>
    </div>


    <div class="grid gap-6 lg:grid-cols-5">

        {{-- LEFT COLUMN --}}
        <div class="space-y-6 lg:col-span-3">

            {{-- DATA PEMBELIAN --}}
            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">

                <div class="border-b border-slate-200 px-6 py-5">

                    <h2 class="font-semibold text-slate-800">
                        Data Pembelian
                    </h2>
                </div>


                <div class="grid gap-6 p-6 sm:grid-cols-2">

                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Tanggal Pembelian
                        </p>

                        <p class="mt-1 font-medium text-slate-800">
                            {{ $reimbursement->purchase_date->format('d/m/Y') }}
                        </p>
                    </div>


                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Kategori
                        </p>

                        <p class="mt-1 font-medium text-slate-800">
                            {{ $reimbursement->category_label }}
                        </p>
                    </div>


                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Toko / Merchant
                        </p>

                        <p class="mt-1 font-medium text-slate-800">
                            {{ $reimbursement->merchant_name ?: '-' }}
                        </p>
                    </div>


                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Barang / Keperluan
                        </p>

                        <p class="mt-1 font-medium text-slate-800">
                            {{ $reimbursement->item_name }}
                        </p>
                    </div>


                    <div class="sm:col-span-2">

                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Keterangan Penggunaan
                        </p>

                        <p class="mt-1 whitespace-pre-line text-sm leading-6 text-slate-700">
                            {{ $reimbursement->purpose }}
                        </p>
                    </div>


                    <div class="sm:col-span-2 rounded-xl bg-slate-50 p-5">

                        <p class="text-sm text-slate-500">
                            Nominal Diajukan
                        </p>

                        <p class="mt-1 text-2xl font-bold text-slate-900">
                            Rp{{ number_format($reimbursement->amount, 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>


            {{-- REKENING TUJUAN --}}
            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">

                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-blue-600">
                        Rekening Tujuan Transfer
                    </p>

                    <h2 class="mt-1 font-semibold text-slate-800">
                        Data Rekening Reimburse
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Jika pengajuan disetujui, pembayaran akan ditransfer ke rekening ini.
                    </p>
                </div>


                <div class="mt-5 grid gap-5 sm:grid-cols-2">

                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Bank
                        </p>

                        <p class="mt-1 font-medium text-slate-800">
                            {{ $reimbursement->bank_name ?: '-' }}
                        </p>
                    </div>


                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Nomor Rekening
                        </p>

                        <p class="mt-1 break-all font-medium text-slate-800">
                            {{ $reimbursement->account_number ?: '-' }}
                        </p>
                    </div>


                    <div class="sm:col-span-2">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Atas Nama
                        </p>

                        <p class="mt-1 font-medium text-slate-800">
                            {{ $reimbursement->account_holder_name ?: '-' }}
                        </p>
                    </div>
                </div>
            </div>


            {{-- HASIL ADMIN --}}
            @if($reimbursement->reviewed_at)

            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">

                <h2 class="font-semibold text-slate-800">
                    Hasil Pemeriksaan Admin
                </h2>


                <div class="mt-4 grid gap-5 sm:grid-cols-2">

                    <div>

                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Diproses Oleh
                        </p>

                        <p class="mt-1 text-sm font-medium text-slate-800">
                            {{ $reimbursement->reviewer?->name ?: '-' }}
                        </p>
                    </div>


                    <div>

                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Waktu Pemeriksaan
                        </p>

                        <p class="mt-1 text-sm font-medium text-slate-800">
                            {{ $reimbursement->reviewed_at->format('d/m/Y H:i') }}
                        </p>
                    </div>


                    <div class="sm:col-span-2">

                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Catatan Admin
                        </p>

                        <p class="mt-1 whitespace-pre-line text-sm leading-6 text-slate-700">
                            {{ $reimbursement->review_note ?: '-' }}
                        </p>
                    </div>


                    @if($reimbursement->paid_at)

                    <div class="sm:col-span-2">

                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Dibayar Pada
                        </p>

                        <p class="mt-1 text-sm font-medium text-slate-800">
                            {{ $reimbursement->paid_at->format('d/m/Y H:i') }}
                        </p>
                    </div>

                    @endif
                </div>
            </div>

            @endif

        </div>


        {{-- RIGHT COLUMN --}}
        <div class="space-y-6 lg:col-span-2">

            {{-- BUKTI PEMBELIAN --}}
            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">

                <div class="flex items-center justify-between gap-3
                            border-b border-slate-200 px-5 py-4">

                    <div class="min-w-0">

                        <h2 class="font-semibold text-slate-800">
                            Bukti Pembelian
                        </h2>

                        <p class="mt-1 break-all text-xs text-slate-500">
                            {{ $reimbursement->receipt_original_name }}
                        </p>
                    </div>


                    <a
                        href="{{ route(
                            'kabid.reimbursements.receipt',
                            $reimbursement
                        ) }}"
                        target="_blank"
                        rel="noopener"
                        class="shrink-0 rounded-lg bg-slate-100
                               px-3 py-2 text-xs font-medium text-slate-700
                               hover:bg-slate-200">
                        Buka
                    </a>
                </div>


                <div class="bg-slate-50 p-4">

                    @if(str_starts_with(
                    (string) $reimbursement->receipt_mime,
                    'image/'
                    ))

                    <img
                        src="{{ route(
                                'kabid.reimbursements.receipt',
                                $reimbursement
                            ) }}"
                        alt="Bukti pembelian"
                        class="max-h-[650px] w-full rounded-lg object-contain">

                    @elseif(
                    $reimbursement->receipt_mime
                    === 'application/pdf'
                    )

                    <iframe
                        src="{{ route(
                                'kabid.reimbursements.receipt',
                                $reimbursement
                            ) }}"
                        class="h-[650px] w-full rounded-lg
                                   border border-slate-200 bg-white"
                        title="Bukti PDF">
                    </iframe>

                    @else

                    <div
                        class="rounded-lg border border-dashed border-slate-300
                                   bg-white p-8 text-center text-sm text-slate-500">

                        Preview tidak tersedia.
                        Klik tombol Buka untuk melihat file.
                    </div>

                    @endif
                </div>
            </div>


            {{-- CANCEL PENDING --}}
            @if($reimbursement->isPending())

            <div class="rounded-xl border border-red-200 bg-red-50 p-5">

                <h2 class="font-semibold text-red-800">
                    Batalkan Pengajuan
                </h2>

                <p class="mt-1 text-sm leading-6 text-red-700">
                    Selama belum diproses Admin, pengajuan masih dapat dibatalkan.
                </p>


                <form
                    method="POST"
                    action="{{ route(
                            'kabid.reimbursements.destroy',
                            $reimbursement
                        ) }}"
                    class="mt-4"
                    data-confirm
                    data-confirm-tone="danger"
                    data-confirm-title="Batalkan Pengajuan?"
                    data-confirm-message="Pengajuan dan bukti pembelian akan dihapus. Tindakan ini tidak dapat dibatalkan."
                    data-confirm-button="Ya, Batalkan">

                    @csrf
                    @method('DELETE')


                    <button
                        type="submit"
                        class="rounded-lg bg-red-600 px-4 py-2.5
                                   text-sm font-semibold text-white
                                   hover:bg-red-700">
                        Batalkan Pengajuan
                    </button>
                </form>
            </div>

            @endif
        </div>
    </div>
</div>

@endsection