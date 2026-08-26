@extends('layouts.karyawan')

@section('title', 'Detail Reimburse')

@section('page-title', 'Detail Reimburse')

@section('content')

<div class="mx-auto max-w-5xl space-y-6">

    {{-- ============================================================
        HEADER
    ============================================================ --}}
    <div
        class="flex flex-col gap-4
               sm:flex-row sm:items-center sm:justify-between">

        <div>
            <div class="flex flex-wrap items-center gap-3">

                <h1 class="text-2xl font-bold text-slate-800">
                    Detail Reimburse
                </h1>

                @if($reimbursement->isPending())
                <span
                    class="rounded-full bg-amber-50
                               px-3 py-1 text-xs font-semibold text-amber-700">
                    {{ $reimbursement->status_label }}
                </span>

                @elseif($reimbursement->isApproved())
                <span
                    class="rounded-full bg-blue-50
                               px-3 py-1 text-xs font-semibold text-blue-700">
                    {{ $reimbursement->status_label }}
                </span>

                @elseif($reimbursement->isRejected())
                <span
                    class="rounded-full bg-red-50
                               px-3 py-1 text-xs font-semibold text-red-700">
                    {{ $reimbursement->status_label }}
                </span>

                @else
                <span
                    class="rounded-full bg-emerald-50
                               px-3 py-1 text-xs font-semibold text-emerald-700">
                    {{ $reimbursement->status_label }}
                </span>
                @endif

            </div>

            <p class="mt-1 text-sm text-slate-500">
                {{ $reimbursement->code }}
            </p>
        </div>


        <div class="flex flex-col gap-2 sm:flex-row">

            @if($reimbursement->isPending())
            <a
                href="{{ route(
                        'karyawan.reimbursements.edit',
                        $reimbursement
                    ) }}"
                class="inline-flex items-center justify-center
                           rounded-lg border border-blue-300
                           bg-blue-50 px-4 py-2.5
                           text-sm font-medium text-blue-700
                           transition hover:bg-blue-100">
                Edit
            </a>
            @endif

            <a
                href="{{ route('karyawan.reimbursements.index') }}"
                class="inline-flex items-center justify-center
                       rounded-lg border border-slate-300
                       px-4 py-2.5 text-sm font-medium text-slate-600
                       transition hover:bg-slate-50">
                Kembali
            </a>
        </div>
    </div>


    {{-- ============================================================
        FLASH MESSAGE
    ============================================================ --}}
    <x-toast-notification />

    {{-- ============================================================
        STATUS INFORMATION
    ============================================================ --}}
    @if($reimbursement->isPending())
    <div
        class="rounded-xl border border-amber-200
                   bg-amber-50 p-4">

        <div class="flex gap-3">
            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="mt-0.5 h-5 w-5 shrink-0 text-amber-600"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor">
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="1.5"
                    d="M12 8v4l3 3m6-3a9 9
                           0 11-18 0 9 9 0 0118 0z" />
            </svg>

            <div>
                <p class="text-sm font-semibold text-amber-900">
                    Menunggu pemeriksaan Admin
                </p>

                <p class="mt-1 text-sm text-amber-700">
                    Selama masih pending, pengajuan dapat diedit
                    atau dibatalkan.
                </p>
            </div>
        </div>
    </div>

    @elseif($reimbursement->isApproved())
    <div
        class="rounded-xl border border-blue-200
                   bg-blue-50 p-4">

        <p class="text-sm font-semibold text-blue-900">
            Pengajuan telah disetujui.
        </p>

        <p class="mt-1 text-sm text-blue-700">
            Reimburse menunggu proses pembayaran dari Admin.
        </p>
    </div>

    @elseif($reimbursement->isRejected())
    <div
        class="rounded-xl border border-red-200
                   bg-red-50 p-4">

        <p class="text-sm font-semibold text-red-900">
            Pengajuan ditolak.
        </p>

        <p class="mt-1 text-sm text-red-700">
            Lihat catatan Admin pada bagian pemeriksaan di bawah.
        </p>
    </div>

    @elseif($reimbursement->isPaid())
    <div
        class="rounded-xl border border-emerald-200
                   bg-emerald-50 p-4">

        <p class="text-sm font-semibold text-emerald-900">
            Reimburse sudah dibayar.
        </p>

        @if($reimbursement->paid_at)
        <p class="mt-1 text-sm text-emerald-700">
            Dicatat dibayar pada
            {{ $reimbursement->paid_at->format('d/m/Y H:i') }}.
        </p>
        @endif
    </div>
    @endif


    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

        {{-- ========================================================
            MAIN DETAIL
        ======================================================== --}}
        <div class="space-y-6 lg:col-span-2">

            <div
                class="overflow-hidden rounded-xl
                       border border-slate-200 bg-white shadow-sm">

                <div class="border-b border-slate-200 px-5 py-4 sm:px-6">
                    <h2 class="font-semibold text-slate-800">
                        Informasi Pembelian
                    </h2>
                </div>


                <dl class="divide-y divide-slate-100">

                    <div
                        class="grid grid-cols-1 gap-1 px-5 py-4
                               sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm text-slate-500">
                            Kode Reimburse
                        </dt>

                        <dd
                            class="text-sm font-semibold text-slate-800
                                   sm:col-span-2">
                            {{ $reimbursement->code }}
                        </dd>
                    </div>

                    <div
                        class="grid grid-cols-1 gap-1 px-5 py-4
                               sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm text-slate-500">
                            Tanggal Pembelian
                        </dt>

                        <dd class="text-sm text-slate-800 sm:col-span-2">
                            {{ $reimbursement->purchase_date->format('d F Y') }}
                        </dd>
                    </div>

                    <div
                        class="grid grid-cols-1 gap-1 px-5 py-4
                               sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm text-slate-500">
                            Kategori
                        </dt>

                        <dd class="text-sm text-slate-800 sm:col-span-2">
                            {{ $reimbursement->category_label }}
                        </dd>
                    </div>

                    <div
                        class="grid grid-cols-1 gap-1 px-5 py-4
                               sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm text-slate-500">
                            Toko / Merchant
                        </dt>

                        <dd class="text-sm text-slate-800 sm:col-span-2">
                            {{ $reimbursement->merchant_name ?: '-' }}
                        </dd>
                    </div>

                    <div
                        class="grid grid-cols-1 gap-1 px-5 py-4
                               sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm text-slate-500">
                            Barang / Keperluan
                        </dt>

                        <dd
                            class="break-words text-sm font-medium
                                   text-slate-800 sm:col-span-2">
                            {{ $reimbursement->item_name }}
                        </dd>
                    </div>

                    <div
                        class="grid grid-cols-1 gap-1 px-5 py-4
                               sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm text-slate-500">
                            Keperluan / Alasan
                        </dt>

                        <dd
                            class="whitespace-pre-line break-words
                                   text-sm leading-relaxed text-slate-800
                                   sm:col-span-2">{{ $reimbursement->purpose }}</dd>
                    </div>

                    <div
                        class="grid grid-cols-1 gap-1 px-5 py-4
                               sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm text-slate-500">
                            Nominal
                        </dt>

                        <dd
                            class="text-lg font-bold text-slate-900
                                   sm:col-span-2">
                            Rp{{ number_format(
                                $reimbursement->amount,
                                0,
                                ',',
                                '.'
                            ) }}
                        </dd>
                    </div>

                    <div
                        class="grid grid-cols-1 gap-1 px-5 py-4
                               sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm text-slate-500">
                            Bank Tujuan
                        </dt>

                        <dd class="text-sm text-slate-800 sm:col-span-2">
                            {{ $reimbursement->bank_name ?: '-' }}
                        </dd>
                    </div>

                    <div
                        class="grid grid-cols-1 gap-1 px-5 py-4
                               sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm text-slate-500">
                            Nomor Rekening
                        </dt>

                        <dd
                            class="font-mono text-sm font-semibold
                                   text-slate-800 sm:col-span-2">
                            {{ $reimbursement->account_number ?: '-' }}
                        </dd>
                    </div>

                    <div
                        class="grid grid-cols-1 gap-1 px-5 py-4
                               sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm text-slate-500">
                            Pemilik Rekening
                        </dt>

                        <dd class="text-sm text-slate-800 sm:col-span-2">
                            {{ $reimbursement->account_holder_name ?: '-' }}
                        </dd>
                    </div>

                    <div
                        class="grid grid-cols-1 gap-1 px-5 py-4
                               sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm text-slate-500">
                            Diajukan
                        </dt>

                        <dd class="text-sm text-slate-800 sm:col-span-2">
                            {{ $reimbursement->created_at->format('d/m/Y H:i') }}
                        </dd>
                    </div>

                </dl>
            </div>


            {{-- REVIEW INFORMATION --}}
            <div
                class="overflow-hidden rounded-xl
                       border border-slate-200 bg-white shadow-sm">

                <div class="border-b border-slate-200 px-5 py-4 sm:px-6">
                    <h2 class="font-semibold text-slate-800">
                        Pemeriksaan Admin
                    </h2>
                </div>

                @if($reimbursement->reviewed_at)

                <dl class="divide-y divide-slate-100">

                    <div
                        class="grid grid-cols-1 gap-1 px-5 py-4
                                   sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm text-slate-500">
                            Diproses oleh
                        </dt>

                        <dd class="text-sm text-slate-800 sm:col-span-2">
                            {{ $reimbursement->reviewer?->name ?? 'Admin' }}
                        </dd>
                    </div>

                    <div
                        class="grid grid-cols-1 gap-1 px-5 py-4
                                   sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm text-slate-500">
                            Waktu Pemeriksaan
                        </dt>

                        <dd class="text-sm text-slate-800 sm:col-span-2">
                            {{ $reimbursement->reviewed_at->format(
                                    'd/m/Y H:i'
                                ) }}
                        </dd>
                    </div>

                    <div
                        class="grid grid-cols-1 gap-1 px-5 py-4
                                   sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm text-slate-500">
                            Catatan
                        </dt>

                        <dd
                            class="whitespace-pre-line break-words
                                       text-sm leading-relaxed text-slate-800
                                       sm:col-span-2">{{ $reimbursement->review_note ?: '-' }}</dd>
                    </div>

                </dl>

                @else

                <div class="px-5 py-8 text-center sm:px-6">
                    <p class="text-sm text-slate-500">
                        Pengajuan belum diperiksa oleh Admin.
                    </p>
                </div>

                @endif
            </div>

        </div>


        {{-- ========================================================
            RECEIPT + ACTION
        ======================================================== --}}
        <div class="space-y-6">

            <div
                class="overflow-hidden rounded-xl
                       border border-slate-200 bg-white shadow-sm">

                <div class="border-b border-slate-200 px-5 py-4">
                    <h2 class="font-semibold text-slate-800">
                        Bukti Pembelian
                    </h2>
                </div>

                <div class="p-5">

                    @if(
                    $reimbursement->receipt_mime
                    && str_starts_with(
                    $reimbursement->receipt_mime,
                    'image/'
                    )
                    )
                    <a
                        href="{{ route(
                                'karyawan.reimbursements.receipt',
                                $reimbursement
                            ) }}"
                        target="_blank"
                        rel="noopener"
                        class="block overflow-hidden rounded-lg
                                   border border-slate-200 bg-slate-50">

                        <img
                            src="{{ route(
                                    'karyawan.reimbursements.receipt',
                                    $reimbursement
                                ) }}"
                            alt="Bukti pembelian {{ $reimbursement->code }}"
                            class="max-h-72 w-full object-contain">
                    </a>
                    @else
                    <div
                        class="flex h-40 items-center justify-center
                                   rounded-lg border border-slate-200
                                   bg-slate-50 text-slate-400">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-12 w-12"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="1.5"
                                d="M7 3h7l5 5v13H7V3z
                                       M14 3v6h5
                                       M9 14h6M9 18h4" />
                        </svg>
                    </div>
                    @endif


                    <div class="mt-4">
                        <p
                            class="break-all text-sm font-medium
                                   text-slate-800">
                            {{ $reimbursement->receipt_original_name }}
                        </p>

                        <p class="mt-1 text-xs text-slate-500">
                            {{ strtoupper(
                                pathinfo(
                                    $reimbursement->receipt_original_name,
                                    PATHINFO_EXTENSION
                                )
                            ) }}

                            @if($reimbursement->receipt_size)
                            •
                            {{ number_format(
                                    $reimbursement->receipt_size / 1024,
                                    1,
                                    ',',
                                    '.'
                                ) }}
                            KB
                            @endif
                        </p>
                    </div>


                    <a
                        href="{{ route(
                            'karyawan.reimbursements.receipt',
                            $reimbursement
                        ) }}"
                        target="_blank"
                        rel="noopener"
                        class="mt-4 inline-flex w-full items-center
                               justify-center gap-2 rounded-lg
                               bg-slate-800 px-4 py-2.5
                               text-sm font-medium text-white
                               transition hover:bg-slate-700">

                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="1.5"
                                d="M15 10l4.553-4.553
                                   M19.553 5.447H15.5
                                   m4.053 0V9.5
                                   M14 5H7a2 2 0 00-2 2v10
                                   a2 2 0 002 2h10a2 2 0 002-2v-7" />
                        </svg>

                        Buka Bukti
                    </a>

                </div>
            </div>


            @if($reimbursement->isPending())

            <div
                class="rounded-xl border border-red-200
                           bg-white p-5 shadow-sm">

                <h2 class="font-semibold text-slate-800">
                    Batalkan Pengajuan
                </h2>

                <p class="mt-1 text-sm leading-relaxed text-slate-500">
                    Pengajuan pending dapat dibatalkan.
                    Data beserta bukti akan dihapus.
                </p>

                <form
                    action="{{ route(
                            'karyawan.reimbursements.destroy',
                            $reimbursement
                        ) }}"
                    method="POST"
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
                        class="w-full rounded-lg border
                                   border-red-300 bg-red-50
                                   px-4 py-2.5 text-sm font-medium
                                   text-red-700 transition hover:bg-red-100">
                        Batalkan Pengajuan
                    </button>
                </form>
            </div>

            @endif

        </div>

    </div>

</div>


<x-confirm-action-modal />

@endsection