@extends('layouts.kabid')

@section('title', 'Ajukan Reimburse')

@section('page-title', 'Ajukan Reimburse')

@section('content')

<div class="mx-auto max-w-4xl space-y-6">

    {{-- ============================================================
        PAGE HEADER
    ============================================================ --}}
    <div
        class="flex flex-col gap-4
               sm:flex-row sm:items-center sm:justify-between">

        <div>
            <h1 class="text-2xl font-bold text-slate-800">
                Ajukan Reimburse
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Masukkan data pembelian dan unggah bukti transaksi.
            </p>
        </div>

        <a
            href="{{ route('kabid.reimbursements.index') }}"
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
        FORM CARD
    ============================================================ --}}
    <div
        class="overflow-hidden rounded-xl
               border border-slate-200 bg-white shadow-sm">

        <div class="border-b border-slate-200 px-5 py-4 sm:px-6">
            <h2 class="font-semibold text-slate-800">
                Data Reimburse
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Kolom bertanda
                <span class="text-red-500">*</span>
                wajib diisi.
            </p>
        </div>

        <form
            id="reimbursementForm"
            action="{{ route('kabid.reimbursements.store') }}"
            method="POST"
            enctype="multipart/form-data">

            @csrf

            <div class="p-5 sm:p-6">
                @include('kabid.reimbursements.partials.form')
            </div>

            <div
                class="flex flex-col-reverse gap-3
                       border-t border-slate-200 bg-slate-50
                       px-5 py-4 sm:flex-row sm:justify-end sm:px-6">

                <a
                    href="{{ route('kabid.reimbursements.index') }}"
                    class="rounded-lg border border-slate-300
                           bg-white px-5 py-2.5 text-center
                           text-sm font-medium text-slate-700
                           transition hover:bg-slate-50">
                    Batal
                </a>

                <button
                    type="submit"
                    id="submitReimbursementButton"
                    class="inline-flex items-center justify-center gap-2
                           rounded-lg bg-blue-600 px-5 py-2.5
                           text-sm font-medium text-white
                           transition hover:bg-blue-700
                           disabled:cursor-not-allowed disabled:opacity-60">

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
                            d="M5 13l4 4L19 7" />
                    </svg>

                    <span id="submitReimbursementText">
                        Kirim Pengajuan
                    </span>
                </button>
            </div>

        </form>
    </div>

</div>

@endsection


@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form =
            document.getElementById('reimbursementForm');

        const submitButton =
            document.getElementById('submitReimbursementButton');

        const submitText =
            document.getElementById('submitReimbursementText');

        const receiptInput =
            document.getElementById('receipt');

        const selectedReceipt =
            document.getElementById('selectedReceipt');

        const amountInput =
            document.getElementById('amount');

        const amountPreview =
            document.getElementById('amountPreview');

        const purposeInput =
            document.getElementById('purpose');

        const purposeCounter =
            document.getElementById('purposeCounter');


        function updateReceiptName() {
            if (!receiptInput || !selectedReceipt) {
                return;
            }

            const file = receiptInput.files?.[0];

            selectedReceipt.textContent = file ?
                'File dipilih: ' + file.name :
                '';
        }


        function getAmountDigits() {
            if (!amountInput) {
                return '';
            }

            return amountInput.value.replace(/\D/g, '');
        }


        function updateAmountPreview() {
            if (!amountInput || !amountPreview) {
                return;
            }

            const digits = getAmountDigits();

            if (!digits) {
                amountInput.value = '';
                amountPreview.textContent = '';
                return;
            }

            const value = Number(digits);
            const formatted =
                new Intl.NumberFormat('id-ID').format(value);

            amountInput.value = formatted;
            amountPreview.textContent = 'Rp' + formatted;
        }


        function updatePurposeCounter() {
            if (!purposeInput || !purposeCounter) {
                return;
            }

            purposeCounter.textContent =
                purposeInput.value.length + ' / 2000';
        }


        receiptInput?.addEventListener(
            'change',
            updateReceiptName
        );

        amountInput?.addEventListener(
            'input',
            updateAmountPreview
        );

        purposeInput?.addEventListener(
            'input',
            updatePurposeCounter
        );


        updateReceiptName();
        updateAmountPreview();
        updatePurposeCounter();


        form?.addEventListener('submit', function() {
            if (amountInput) {
                amountInput.value = getAmountDigits();
            }

            if (submitButton) {
                submitButton.disabled = true;
            }

            if (submitText) {
                submitText.textContent = 'Mengirim...';
            }
        });
    });
</script>
@endpush