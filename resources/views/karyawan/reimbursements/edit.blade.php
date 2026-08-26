@extends('layouts.karyawan')

@section('title', 'Edit Reimburse')

@section('page-title', 'Edit Reimburse')

@section('content')

<div class="mx-auto max-w-4xl space-y-6">

    {{-- ============================================================
        PAGE HEADER
    ============================================================ --}}
    <div
        class="flex flex-col gap-4
               sm:flex-row sm:items-center sm:justify-between">

        <div>
            <div class="flex flex-wrap items-center gap-2">
                <h1 class="text-2xl font-bold text-slate-800">
                    Edit Reimburse
                </h1>

                <span
                    class="rounded-full bg-amber-50
                           px-3 py-1 text-xs font-semibold text-amber-700">
                    {{ $reimbursement->status_label }}
                </span>
            </div>

            <p class="mt-1 text-sm text-slate-500">
                {{ $reimbursement->code }}
                • hanya pengajuan pending yang dapat diedit.
            </p>
        </div>

        <a
            href="{{ route(
                'karyawan.reimbursements.show',
                $reimbursement
            ) }}"
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
                Perbarui Data Reimburse
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Kosongkan file bukti jika tidak ingin mengganti bukti lama.
            </p>
        </div>

        <form
            id="editReimbursementForm"
            action="{{ route(
                'karyawan.reimbursements.update',
                $reimbursement
            ) }}"
            method="POST"
            enctype="multipart/form-data">

            @csrf
            @method('PUT')

            <div class="p-5 sm:p-6">
                @include('karyawan.reimbursements.partials.form')
            </div>

            <div
                class="flex flex-col-reverse gap-3
                       border-t border-slate-200 bg-slate-50
                       px-5 py-4 sm:flex-row sm:justify-end sm:px-6">

                <a
                    href="{{ route(
                        'karyawan.reimbursements.show',
                        $reimbursement
                    ) }}"
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
                        Simpan Perubahan
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
            document.getElementById('editReimbursementForm');

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

            selectedReceipt.textContent =
                file ?
                'Bukti baru: ' + file.name :
                '';
        }


        function updateAmountPreview() {
            if (!amountInput || !amountPreview) {
                return;
            }

            const value = Number(amountInput.value || 0);

            amountPreview.textContent =
                value > 0 ?
                'Rp' + new Intl.NumberFormat('id-ID').format(value) :
                '';
        }


        function updatePurposeCounter() {
            if (!purposeInput || !purposeCounter) {
                return;
            }

            purposeCounter.textContent =
                purposeInput.value.length + ' / 2000';
        }


        receiptInput?.addEventListener('change', updateReceiptName);
        amountInput?.addEventListener('input', updateAmountPreview);
        purposeInput?.addEventListener('input', updatePurposeCounter);

        updateReceiptName();
        updateAmountPreview();
        updatePurposeCounter();


        form?.addEventListener('submit', function() {
            if (submitButton) {
                submitButton.disabled = true;
            }

            if (submitText) {
                submitText.textContent = 'Menyimpan...';
            }
        });
    });
</script>
@endpush