{{-- ================================================================
    REUSABLE FORM
    Digunakan oleh create.blade.php dan edit.blade.php
================================================================ --}}

@php
$isEdit = isset($reimbursement);

$purchaseDateValue = old(
'purchase_date',
$isEdit
? $reimbursement->purchase_date?->format('Y-m-d')
: now()->format('Y-m-d')
);

$categoryValue = old(
'category',
$isEdit ? $reimbursement->category : ''
);

$merchantValue = old(
'merchant_name',
$isEdit ? $reimbursement->merchant_name : ''
);

$itemValue = old(
'item_name',
$isEdit ? $reimbursement->item_name : ''
);

$purposeValue = old(
'purpose',
$isEdit ? $reimbursement->purpose : ''
);

$amountValue = old(
'amount',
$isEdit ? $reimbursement->amount : ''
);

$bankNameValue = old(
'bank_name',
$isEdit ? $reimbursement->bank_name : ''
);

$accountNumberValue = old(
'account_number',
$isEdit ? $reimbursement->account_number : ''
);

$accountHolderNameValue = old(
'account_holder_name',
$isEdit ? $reimbursement->account_holder_name : ''
);
@endphp


<div class="space-y-6">

    {{-- PURCHASE + CATEGORY --}}
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">

        <div>
            <label
                for="purchase_date"
                class="mb-2 block text-sm font-medium text-slate-700">
                Tanggal Pembelian
                <span class="text-red-500">*</span>
            </label>

            <input
                type="date"
                id="purchase_date"
                name="purchase_date"
                value="{{ $purchaseDateValue }}"
                max="{{ now()->format('Y-m-d') }}"
                required
                class="w-full rounded-lg border-slate-300
                       focus:border-blue-500 focus:ring-blue-500">

            @error('purchase_date')
            <p class="mt-1 text-sm text-red-600">
                {{ $message }}
            </p>
            @enderror
        </div>


        <div>
            <label
                for="category"
                class="mb-2 block text-sm font-medium text-slate-700">
                Kategori
                <span class="text-red-500">*</span>
            </label>

            <select
                id="category"
                name="category"
                required
                class="w-full rounded-lg border-slate-300
                       focus:border-blue-500 focus:ring-blue-500">

                <option value="">
                    -- Pilih Kategori --
                </option>

                @foreach($categories as $value => $label)
                <option
                    value="{{ $value }}"
                    @selected($categoryValue===$value)>
                    {{ $label }}
                </option>
                @endforeach
            </select>

            @error('category')
            <p class="mt-1 text-sm text-red-600">
                {{ $message }}
            </p>
            @enderror
        </div>

    </div>


    {{-- MERCHANT --}}
    <div>
        <label
            for="merchant_name"
            class="mb-2 block text-sm font-medium text-slate-700">
            Nama Toko / Merchant
            <span class="text-xs font-normal text-slate-400">
                (opsional)
            </span>
        </label>

        <input
            type="text"
            id="merchant_name"
            name="merchant_name"
            value="{{ $merchantValue }}"
            maxlength="255"
            placeholder="Contoh: Apotek Sehat, Tokopedia, Indomaret"
            class="w-full rounded-lg border-slate-300
                   focus:border-blue-500 focus:ring-blue-500">

        @error('merchant_name')
        <p class="mt-1 text-sm text-red-600">
            {{ $message }}
        </p>
        @enderror
    </div>


    {{-- ITEM --}}
    <div>
        <label
            for="item_name"
            class="mb-2 block text-sm font-medium text-slate-700">
            Barang / Keperluan
            <span class="text-red-500">*</span>
        </label>

        <input
            type="text"
            id="item_name"
            name="item_name"
            value="{{ $itemValue }}"
            maxlength="255"
            required
            placeholder="Contoh: Masker medis 5 box"
            class="w-full rounded-lg border-slate-300
                   focus:border-blue-500 focus:ring-blue-500">

        @error('item_name')
        <p class="mt-1 text-sm text-red-600">
            {{ $message }}
        </p>
        @enderror
    </div>


    {{-- PURPOSE --}}
    <div>
        <label
            for="purpose"
            class="mb-2 block text-sm font-medium text-slate-700">
            Keperluan / Alasan Pembelian
            <span class="text-red-500">*</span>
        </label>

        <textarea
            id="purpose"
            name="purpose"
            rows="4"
            maxlength="2000"
            required
            placeholder="Jelaskan barang tersebut digunakan untuk apa..."
            class="w-full rounded-lg border-slate-300
                   focus:border-blue-500 focus:ring-blue-500">{{ $purposeValue }}</textarea>

        <div class="mt-1 flex items-center justify-between gap-4">
            @error('purpose')
            <p class="text-sm text-red-600">
                {{ $message }}
            </p>
            @else
            <p class="text-xs text-slate-400">
                Maksimal 2.000 karakter.
            </p>
            @enderror

            <p
                id="purposeCounter"
                class="shrink-0 text-xs text-slate-400">
                0 / 2000
            </p>
        </div>
    </div>


    {{-- AMOUNT --}}
    <div>
        <label
            for="amount"
            class="mb-2 block text-sm font-medium text-slate-700">
            Nominal Reimburse
            <span class="text-red-500">*</span>
        </label>

        <div class="relative">
            <span
                class="pointer-events-none absolute inset-y-0 left-0
                       flex items-center pl-3 text-sm font-medium
                       text-slate-500">
                Rp
            </span>

            <input
                type="text"
                id="amount"
                name="amount"
                value="{{ $amountValue }}"
                required
                inputmode="numeric"
                autocomplete="off"
                placeholder="15.000"
                class="w-full rounded-lg border-slate-300
                       pl-10 focus:border-blue-500 focus:ring-blue-500">
        </div>

        <div class="mt-1 flex flex-col gap-1 sm:flex-row sm:justify-between">
            @error('amount')
            <p class="text-sm text-red-600">
                {{ $message }}
            </p>
            @else
            <p class="text-xs text-slate-400">
                Boleh ketik 15000 atau 15.000. Sistem akan membaca Rp15.000.
            </p>
            @enderror

            <p
                id="amountPreview"
                class="text-xs font-medium text-blue-600">
            </p>
        </div>
    </div>


    {{-- ============================================================
        TRANSFER DESTINATION
    ============================================================ --}}
    <div
        class="rounded-xl border border-slate-200
               bg-slate-50 p-5">

        <div class="mb-5">
            <h3 class="font-semibold text-slate-800">
                Rekening Tujuan Transfer
            </h3>

            <p class="mt-1 text-sm text-slate-500">
                Reimburse yang disetujui akan ditransfer ke rekening ini.
            </p>
        </div>


        <div class="space-y-5">

            <div>
                <label
                    for="bank_name"
                    class="mb-2 block text-sm font-medium text-slate-700">
                    Nama Bank
                    <span class="text-red-500">*</span>
                </label>

                <input
                    type="text"
                    id="bank_name"
                    name="bank_name"
                    value="{{ $bankNameValue }}"
                    maxlength="100"
                    required
                    autocomplete="organization"
                    placeholder="Contoh: BCA, BRI, BNI, Mandiri"
                    class="w-full rounded-lg border-slate-300 bg-white
                           focus:border-blue-500 focus:ring-blue-500">

                @error('bank_name')
                <p class="mt-1 text-sm text-red-600">
                    {{ $message }}
                </p>
                @enderror
            </div>


            <div>
                <label
                    for="account_number"
                    class="mb-2 block text-sm font-medium text-slate-700">
                    Nomor Rekening
                    <span class="text-red-500">*</span>
                </label>

                <input
                    type="text"
                    id="account_number"
                    name="account_number"
                    value="{{ $accountNumberValue }}"
                    maxlength="40"
                    required
                    inputmode="numeric"
                    autocomplete="off"
                    placeholder="Contoh: 1234567890"
                    class="w-full rounded-lg border-slate-300 bg-white
                           focus:border-blue-500 focus:ring-blue-500">

                <p class="mt-1 text-xs text-slate-400">
                    Boleh menggunakan spasi atau tanda hubung saat mengetik.
                </p>

                @error('account_number')
                <p class="mt-1 text-sm text-red-600">
                    {{ $message }}
                </p>
                @enderror
            </div>


            <div>
                <label
                    for="account_holder_name"
                    class="mb-2 block text-sm font-medium text-slate-700">
                    Nama Pemilik Rekening
                    <span class="text-red-500">*</span>
                </label>

                <input
                    type="text"
                    id="account_holder_name"
                    name="account_holder_name"
                    value="{{ $accountHolderNameValue }}"
                    maxlength="150"
                    required
                    autocomplete="name"
                    placeholder="Sesuai nama yang terdaftar di bank"
                    class="w-full rounded-lg border-slate-300 bg-white
                           focus:border-blue-500 focus:ring-blue-500">

                @error('account_holder_name')
                <p class="mt-1 text-sm text-red-600">
                    {{ $message }}
                </p>
                @enderror
            </div>

        </div>
    </div>


    {{-- RECEIPT --}}
    <div>
        <label
            for="receipt"
            class="mb-2 block text-sm font-medium text-slate-700">
            Bukti Pembelian

            @if(!$isEdit)
            <span class="text-red-500">*</span>
            @else
            <span class="text-xs font-normal text-slate-400">
                (opsional jika tidak diganti)
            </span>
            @endif
        </label>

        <div
            class="rounded-xl border border-dashed border-slate-300
                   bg-slate-50 p-5">

            <div class="flex flex-col gap-4 sm:flex-row sm:items-center">

                <div
                    class="flex h-12 w-12 shrink-0 items-center justify-center
                           rounded-xl bg-white text-slate-500 shadow-sm">
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
                            d="M7 16a4 4 0 01-.88-7.903
                               A5.002 5.002 0 0115.9 6
                               H16a5 5 0 010 10h-1
                               m-3-4l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                </div>

                <div class="min-w-0 flex-1">
                    <input
                        type="file"
                        id="receipt"
                        name="receipt"
                        accept=".jpg,.jpeg,.png,.webp,.pdf"
                        @required(!$isEdit)
                        class="block w-full text-sm text-slate-600
                               file:mr-4 file:rounded-lg file:border-0
                               file:bg-blue-50 file:px-4 file:py-2
                               file:text-sm file:font-medium
                               file:text-blue-700
                               hover:file:bg-blue-100">

                    <p class="mt-2 text-xs text-slate-500">
                        JPG, JPEG, PNG, WEBP, atau PDF. Maksimal 5 MB.
                    </p>

                    <p
                        id="selectedReceipt"
                        class="mt-1 break-all text-xs font-medium text-slate-700">
                    </p>
                </div>

            </div>


            @if($isEdit)
            <div
                class="mt-4 rounded-lg border border-slate-200
                           bg-white p-3">

                <p class="text-xs font-medium uppercase tracking-wide
                              text-slate-400">
                    Bukti saat ini
                </p>

                <div
                    class="mt-2 flex flex-col gap-2
                               sm:flex-row sm:items-center sm:justify-between">

                    <p class="break-all text-sm text-slate-700">
                        {{ $reimbursement->receipt_original_name }}
                    </p>

                    <a
                        href="{{ route(
                                'karyawan.reimbursements.receipt',
                                $reimbursement
                            ) }}"
                        target="_blank"
                        rel="noopener"
                        class="shrink-0 text-sm font-medium
                                   text-blue-600 hover:text-blue-700">
                        Lihat Bukti
                    </a>
                </div>
            </div>
            @endif

        </div>

        @error('receipt')
        <p class="mt-1 text-sm text-red-600">
            {{ $message }}
        </p>
        @enderror
    </div>


    {{-- INFO --}}
    <div
        class="rounded-xl border border-blue-200
               bg-blue-50 p-4">

        <div class="flex gap-3">
            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="mt-0.5 h-5 w-5 shrink-0 text-blue-600"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor">
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="1.5"
                    d="M13 16h-1v-4h-1m1-4h.01
                       M21 12a9 9 0 11-18 0
                       9 9 0 0118 0z" />
            </svg>

            <div>
                <p class="text-sm font-semibold text-blue-900">
                    Pastikan data sesuai dengan bukti pembelian.
                </p>

                <p class="mt-1 text-sm leading-relaxed text-blue-700">
                    Pengajuan masih bisa diedit atau dibatalkan selama
                    statusnya masih menunggu pemeriksaan admin.
                </p>
            </div>
        </div>
    </div>

</div>