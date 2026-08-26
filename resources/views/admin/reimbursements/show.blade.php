@extends('layouts.admin')

@section('title', 'Detail Reimbursement')

@section('page-title', 'Detail Reimbursement')

@section('content')

<div class="mx-auto max-w-6xl space-y-6">

    {{-- ============================================================
        HEADER
    ============================================================ --}}
    <div
        class="flex flex-col gap-4
               lg:flex-row lg:items-center lg:justify-between">

        <div>
            <div class="flex flex-wrap items-center gap-3">

                <h1 class="text-2xl font-bold text-slate-800">
                    Detail Reimbursement
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


        <a
            href="{{ route('admin.reimbursements.index') }}"
            class="inline-flex items-center justify-center gap-2
                   rounded-lg border border-slate-300
                   px-4 py-2.5 text-sm font-medium text-slate-600
                   transition hover:bg-slate-50">

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
                    d="M15 19l-7-7 7-7" />
            </svg>

            Kembali
        </a>
    </div>


    {{-- ============================================================
        FLASH / VALIDATION MESSAGE
    ============================================================ --}}
    <x-toast-notification />

    @if($errors->any())
    <div
        class="rounded-lg border border-red-200
                   bg-red-50 px-4 py-3 text-sm text-red-700">

        <p class="font-semibold">
            Ada data yang perlu diperbaiki:
        </p>

        <ul class="mt-2 list-disc space-y-1 pl-5">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif


    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">

        {{-- ========================================================
            LEFT / MAIN
        ======================================================== --}}
        <div class="space-y-6 xl:col-span-2">

            {{-- Karyawan --}}
            <div
                class="overflow-hidden rounded-xl
                       border border-slate-200 bg-white shadow-sm">

                <div class="border-b border-slate-200 px-5 py-4 sm:px-6">
                    <h2 class="font-semibold text-slate-800">
                        Data Karyawan
                    </h2>
                </div>

                <dl class="divide-y divide-slate-100">

                    <div
                        class="grid grid-cols-1 gap-1 px-5 py-4
                               sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm text-slate-500">
                            Nama
                        </dt>

                        <dd
                            class="text-sm font-semibold text-slate-800
                                   sm:col-span-2">
                            {{ $reimbursement->user?->name ?? '-' }}
                        </dd>
                    </div>

                    <div
                        class="grid grid-cols-1 gap-1 px-5 py-4
                               sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm text-slate-500">
                            NIK
                        </dt>

                        <dd class="text-sm text-slate-800 sm:col-span-2">
                            {{ $reimbursement->user?->nik ?: '-' }}
                        </dd>
                    </div>

                    <div
                        class="grid grid-cols-1 gap-1 px-5 py-4
                               sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm text-slate-500">
                            Email
                        </dt>

                        <dd class="break-all text-sm text-slate-800 sm:col-span-2">
                            {{ $reimbursement->user?->email ?: '-' }}
                        </dd>
                    </div>

                    <div
                        class="grid grid-cols-1 gap-1 px-5 py-4
                               sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm text-slate-500">
                            Departemen
                        </dt>

                        <dd class="text-sm text-slate-800 sm:col-span-2">
                            {{ $reimbursement->user?->department?->name ?? '-' }}
                        </dd>
                    </div>

                </dl>
            </div>


            {{-- Purchase --}}
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
                            Kode Reimbursement
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
                            Diajukan
                        </dt>

                        <dd class="text-sm text-slate-800 sm:col-span-2">
                            {{ $reimbursement->created_at->format('d/m/Y H:i') }}
                        </dd>
                    </div>

                </dl>
            </div>


            {{-- Transfer Destination --}}
            <div
                class="overflow-hidden rounded-xl
                       border border-blue-200 bg-white shadow-sm">

                <div class="border-b border-blue-100 bg-blue-50 px-5 py-4 sm:px-6">
                    <h2 class="font-semibold text-blue-900">
                        Rekening Tujuan Transfer
                    </h2>

                    <p class="mt-1 text-sm text-blue-700">
                        Gunakan data ini saat melakukan pembayaran reimbursement.
                    </p>
                </div>

                <dl class="divide-y divide-slate-100">

                    <div
                        class="grid grid-cols-1 gap-1 px-5 py-4
                               sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm text-slate-500">
                            Bank
                        </dt>

                        <dd
                            class="text-sm font-semibold text-slate-800
                                   sm:col-span-2">
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
                            class="font-mono text-base font-bold
                                   tracking-wide text-slate-900 sm:col-span-2">
                            {{ $reimbursement->account_number ?: '-' }}
                        </dd>
                    </div>

                    <div
                        class="grid grid-cols-1 gap-1 px-5 py-4
                               sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm text-slate-500">
                            Atas Nama
                        </dt>

                        <dd
                            class="text-sm font-semibold text-slate-800
                                   sm:col-span-2">
                            {{ $reimbursement->account_holder_name ?: '-' }}
                        </dd>
                    </div>

                </dl>
            </div>


            {{-- Review History --}}
            <div
                class="overflow-hidden rounded-xl
                       border border-slate-200 bg-white shadow-sm">

                <div class="border-b border-slate-200 px-5 py-4 sm:px-6">
                    <h2 class="font-semibold text-slate-800">
                        Riwayat Pemeriksaan
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

                    @if($reimbursement->paid_at)
                    <div
                        class="grid grid-cols-1 gap-1 px-5 py-4
                                       sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm text-slate-500">
                            Waktu Dibayar
                        </dt>

                        <dd class="text-sm text-slate-800 sm:col-span-2">
                            {{ $reimbursement->paid_at->format(
                                        'd/m/Y H:i'
                                    ) }}
                        </dd>
                    </div>
                    @endif

                </dl>

                @else

                <div class="px-5 py-8 text-center sm:px-6">
                    <p class="text-sm text-slate-500">
                        Belum pernah diproses oleh Admin.
                    </p>
                </div>

                @endif
            </div>

        </div>


        {{-- ========================================================
            RIGHT / RECEIPT + ACTION
        ======================================================== --}}
        <div class="space-y-6">

            {{-- Receipt --}}
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
                                'admin.reimbursements.receipt',
                                $reimbursement
                            ) }}"
                        target="_blank"
                        rel="noopener"
                        class="block overflow-hidden rounded-lg
                                   border border-slate-200 bg-slate-50">

                        <img
                            src="{{ route(
                                    'admin.reimbursements.receipt',
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
                            'admin.reimbursements.receipt',
                            $reimbursement
                        ) }}"
                        target="_blank"
                        rel="noopener"
                        class="mt-4 inline-flex w-full items-center
                               justify-center gap-2 rounded-lg
                               bg-slate-800 px-4 py-2.5
                               text-sm font-medium text-white
                               transition hover:bg-slate-700">
                        Buka Bukti
                    </a>
                </div>
            </div>


            {{-- ====================================================
                ACTION: PENDING
            ==================================================== --}}
            @if($reimbursement->isPending())

            <div
                class="overflow-hidden rounded-xl
                           border border-slate-200 bg-white shadow-sm">

                <div class="border-b border-slate-200 px-5 py-4">
                    <h2 class="font-semibold text-slate-800">
                        Proses Pengajuan
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Periksa data dan bukti sebelum menentukan keputusan.
                    </p>
                </div>


                <div class="space-y-5 p-5">

                    {{-- Approve --}}
                    <form
                        action="{{ route(
                                'admin.reimbursements.approve',
                                $reimbursement
                            ) }}"
                        method="POST"
                        data-confirm
                        data-confirm-tone="success"
                        data-confirm-title="Setujui Reimbursement?"
                        data-confirm-message="Pastikan data pembelian dan bukti sudah sesuai. Pengajuan akan berubah menjadi Disetujui."
                        data-confirm-button="Ya, Setujui">

                        @csrf
                        @method('PATCH')

                        <label
                            for="approve_review_note"
                            class="mb-2 block text-sm font-medium
                                       text-slate-700">
                            Catatan Persetujuan
                            <span class="text-xs font-normal text-slate-400">
                                (opsional)
                            </span>
                        </label>

                        <textarea
                            id="approve_review_note"
                            name="review_note"
                            rows="3"
                            maxlength="2000"
                            placeholder="Contoh: Bukti sesuai dan dapat diproses."
                            class="w-full rounded-lg border-slate-300
                                       text-sm focus:border-blue-500
                                       focus:ring-blue-500"></textarea>

                        <button
                            type="submit"
                            class="mt-3 w-full rounded-lg
                                       bg-emerald-600 px-4 py-2.5
                                       text-sm font-medium text-white
                                       transition hover:bg-emerald-700">
                            Setujui Pengajuan
                        </button>
                    </form>


                    <div class="border-t border-slate-200"></div>


                    {{-- Reject --}}
                    <form
                        action="{{ route(
                                'admin.reimbursements.reject',
                                $reimbursement
                            ) }}"
                        method="POST"
                        data-confirm
                        data-confirm-tone="danger"
                        data-confirm-title="Tolak Reimbursement?"
                        data-confirm-message="Pengajuan akan ditolak dan alasan penolakan akan dapat dilihat oleh karyawan."
                        data-confirm-button="Ya, Tolak">

                        @csrf
                        @method('PATCH')

                        <label
                            for="reject_review_note"
                            class="mb-2 block text-sm font-medium
                                       text-slate-700">
                            Alasan Penolakan
                            <span class="text-red-500">*</span>
                        </label>

                        <textarea
                            id="reject_review_note"
                            name="review_note"
                            rows="3"
                            maxlength="2000"
                            required
                            placeholder="Contoh: Nominal pada bukti tidak sesuai."
                            class="w-full rounded-lg border-slate-300
                                       text-sm focus:border-red-500
                                       focus:ring-red-500">{{ old('review_note') }}</textarea>

                        <button
                            type="submit"
                            class="mt-3 w-full rounded-lg
                                       border border-red-300 bg-red-50
                                       px-4 py-2.5 text-sm font-medium
                                       text-red-700 transition hover:bg-red-100">
                            Tolak Pengajuan
                        </button>
                    </form>

                </div>
            </div>


            {{-- ====================================================
                ACTION: APPROVED
            ==================================================== --}}
            @elseif($reimbursement->isApproved())

            <div
                class="rounded-xl border border-blue-200
                           bg-white p-5 shadow-sm">

                <h2 class="font-semibold text-slate-800">
                    Pembayaran Reimbursement
                </h2>

                <p class="mt-2 text-sm leading-relaxed text-slate-500">
                    Pengajuan sudah disetujui.
                    Setelah pembayaran benar-benar dilakukan,
                    tandai sebagai sudah dibayar.
                </p>

                <div
                    class="mt-4 rounded-lg border border-blue-100
                               bg-blue-50 px-4 py-3">

                    <p class="text-xs uppercase tracking-wide text-blue-500">
                        Transfer Ke
                    </p>

                    <p class="mt-1 font-semibold text-blue-900">
                        {{ $reimbursement->bank_name ?: '-' }}
                        • {{ $reimbursement->account_number ?: '-' }}
                    </p>

                    <p class="mt-1 text-sm text-blue-700">
                        a.n. {{ $reimbursement->account_holder_name ?: '-' }}
                    </p>
                </div>

                <div
                    class="mt-3 rounded-lg bg-blue-50
                               px-4 py-3">

                    <p class="text-xs uppercase tracking-wide text-blue-500">
                        Nominal Dibayar
                    </p>

                    <p class="mt-1 text-xl font-bold text-blue-900">
                        Rp{{ number_format(
                                $reimbursement->amount,
                                0,
                                ',',
                                '.'
                            ) }}
                    </p>
                </div>

                <form
                    action="{{ route(
                            'admin.reimbursements.paid',
                            $reimbursement
                        ) }}"
                    method="POST"
                    class="mt-4"
                    data-confirm
                    data-confirm-tone="warning"
                    data-confirm-title="Tandai Sudah Dibayar?"
                    data-confirm-message="Pastikan transfer benar-benar sudah dilakukan ke rekening tujuan. Status akan berubah menjadi Sudah Dibayar."
                    data-confirm-button="Ya, Sudah Dibayar">

                    @csrf
                    @method('PATCH')

                    <button
                        type="submit"
                        class="w-full rounded-lg
                                   bg-blue-600 px-4 py-2.5
                                   text-sm font-medium text-white
                                   transition hover:bg-blue-700">
                        Tandai Sudah Dibayar
                    </button>
                </form>
            </div>


            {{-- ====================================================
                ACTION: REJECTED
            ==================================================== --}}
            @elseif($reimbursement->isRejected())

            <div
                class="rounded-xl border border-red-200
                           bg-red-50 p-5">

                <h2 class="font-semibold text-red-900">
                    Pengajuan Ditolak
                </h2>

                <p class="mt-2 text-sm leading-relaxed text-red-700">
                    Pengajuan ini telah selesai diproses sebagai ditolak
                    dan tidak dapat ditandai dibayar.
                </p>
            </div>


            {{-- ====================================================
                ACTION: PAID
            ==================================================== --}}
            @elseif($reimbursement->isPaid())

            <div
                class="rounded-xl border border-emerald-200
                           bg-emerald-50 p-5">

                <h2 class="font-semibold text-emerald-900">
                    Reimbursement Selesai
                </h2>

                <p class="mt-2 text-sm leading-relaxed text-emerald-700">
                    Reimbursement ini sudah dicatat sebagai dibayar.
                </p>

                @if($reimbursement->paid_at)
                <p class="mt-3 text-sm font-medium text-emerald-800">
                    {{ $reimbursement->paid_at->format('d/m/Y H:i') }}
                </p>
                @endif
            </div>

            @endif

        </div>

    </div>

</div>


<x-confirm-action-modal />

@endsection