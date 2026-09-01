@extends('layouts.kabid')

@section('title', 'Ajukan Perizinan')

@section('page-title', 'Ajukan Perizinan')

@section('content')

<div class="mx-auto max-w-5xl space-y-6">

    <div>
        <h1 class="text-2xl font-bold text-slate-800">
            Ajukan Perizinan
        </h1>

        <p class="mt-1 text-sm text-slate-500">
            Lengkapi informasi perizinan sesuai kebutuhan Anda.
        </p>
    </div>


    @if($errors->any())
    <div class="rounded-xl border border-red-200 bg-red-50 p-5">
        <p class="font-semibold text-red-700">
            Terdapat data yang belum benar.
        </p>

        <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-red-600">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif


    <div class="grid grid-cols-1 gap-4 md:grid-cols-4">

        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-xs uppercase text-slate-400">Nama</p>
            <p class="mt-1 font-semibold text-slate-800">
                {{ $user->name }}
            </p>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-xs uppercase text-slate-400">NIK</p>
            <p class="mt-1 font-semibold text-slate-800">
                {{ $user->nik ?? '-' }}
            </p>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-xs uppercase text-slate-400">Bidang</p>
            <p class="mt-1 font-semibold text-slate-800">
                {{ $user->department?->name ?? '-' }}
            </p>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-xs uppercase text-slate-400">Cuti Tahunan</p>

            @if($annualLeaveEligible)
            @if($balance)
            <p class="mt-1 text-xl font-bold text-blue-600">
                {{ $annualLeaveAvailableDays }} hari
            </p>
            <p class="mt-1 text-xs text-slate-400">
                Sisa yang masih dapat diajukan.
            </p>
            @else
            <p class="mt-1 font-semibold text-amber-600">
                Jatah Belum Diberikan
            </p>
            @endif
            @else
            <p class="mt-1 font-semibold text-amber-600">
                Belum Berhak
            </p>

            @if($annualLeaveEligibleDate)
            <p class="mt-1 text-xs text-slate-400">
                Mulai {{ $annualLeaveEligibleDate->format('d/m/Y') }}
            </p>
            @else
            <p class="mt-1 text-xs text-slate-400">
                Tanggal mulai kerja belum tersedia.
            </p>
            @endif
            @endif
        </div>

    </div>


    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">

        <form
            action="{{ route('kabid.leave-requests.store') }}"
            method="POST"
            enctype="multipart/form-data"
            id="permissionForm">
            @csrf

            <div class="space-y-8 p-6">

                <section>
                    <h2 class="font-semibold text-slate-800">
                        Informasi Perizinan
                    </h2>

                    <div class="mt-6 space-y-6">

                        <div>
                            <label
                                for="permissionType"
                                class="mb-2 block text-sm font-medium text-slate-700">
                                Jenis Perizinan
                                <span class="text-red-500">*</span>
                            </label>

                            <select
                                id="permissionType"
                                name="permission_type_id"
                                class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                                <option value="">
                                    -- Pilih Jenis Perizinan --
                                </option>

                                @foreach($permissionTypes as $type)
                                @php
                                $isAnnualLeave = $type->code === 'annual_leave';
                                $disableAnnualLeave =
                                $isAnnualLeave
                                && ! $annualLeaveCanBeUsed;
                                @endphp

                                <option
                                    value="{{ $type->id }}"
                                    data-code="{{ $type->code }}"
                                    @selected(old('permission_type_id')==$type->id)
                                    @disabled($disableAnnualLeave)
                                    >
                                    {{ $type->name }}

                                    @if($isAnnualLeave && ! $annualLeaveEligible)
                                    - Belum memenuhi 12 bulan
                                    @elseif($isAnnualLeave && $annualLeaveEligible && ! $balance)
                                    - Jatah belum diberikan
                                    @elseif($isAnnualLeave && $annualLeaveAvailableDays <= 0)
                                        - Jatah habis
                                        @endif
                                        </option>
                                        @endforeach
                            </select>

                            @error('permission_type_id')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                            @enderror
                        </div>


                        <div
                            id="maternityFields"
                            class="hidden rounded-xl border border-purple-200 bg-purple-50 p-5">
                            <label
                                for="expectedDeliveryDate"
                                class="mb-2 block text-sm font-medium text-purple-900">
                                Perkiraan Tanggal Melahirkan
                                <span class="text-red-500">*</span>
                            </label>

                            <input
                                type="date"
                                id="expectedDeliveryDate"
                                name="expected_delivery_date"
                                value="{{ old('expected_delivery_date') }}"
                                class="w-full rounded-lg border-purple-200 bg-white focus:border-purple-500 focus:ring-purple-500">

                            <p class="mt-2 text-sm text-purple-700">
                                Setelah tanggal perkiraan melahirkan dipilih,
                                sistem otomatis mengisi tanggal mulai satu bulan
                                sebelumnya dan tanggal selesai satu bulan sesudahnya.
                            </p>

                            <p class="mt-1 text-xs text-purple-600">
                                Tanggal mulai dan selesai tetap dapat Anda ubah
                                secara manual setelah terisi otomatis.
                            </p>

                            @error('expected_delivery_date')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                            @enderror
                        </div>


                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <label
                                    for="startDate"
                                    class="mb-2 block text-sm font-medium text-slate-700">
                                    Tanggal Mulai
                                    <span class="text-red-500">*</span>
                                </label>

                                <input
                                    type="date"
                                    id="startDate"
                                    name="start_date"
                                    value="{{ old('start_date') }}"
                                    class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">

                                @error('start_date')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                                @enderror
                            </div>

                            <div>
                                <label
                                    for="endDate"
                                    class="mb-2 block text-sm font-medium text-slate-700">
                                    Tanggal Selesai
                                    <span class="text-red-500">*</span>
                                </label>

                                <input
                                    type="date"
                                    id="endDate"
                                    name="end_date"
                                    value="{{ old('end_date') }}"
                                    class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">

                                @error('end_date')
                                <p class="mt-1 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                                @enderror
                            </div>
                        </div>


                        <div
                            id="maternityPolicyInfo"
                            class="hidden rounded-xl border border-purple-200 bg-purple-50 p-4">
                            <p class="font-semibold text-purple-900">
                                Periode Hak Cuti Melahirkan
                            </p>

                            <p
                                id="maternityPolicyText"
                                class="mt-1 text-sm leading-relaxed text-purple-700"></p>
                        </div>


                        <div
                            id="annualLeaveValidation"
                            class="hidden rounded-xl border p-4">
                            <p
                                id="annualLeaveValidationTitle"
                                class="font-semibold"></p>

                            <p
                                id="annualLeaveValidationText"
                                class="mt-1 text-sm"></p>
                        </div>


                        <div
                            id="doctorLetterFields"
                            class="hidden">
                            <label class="mb-2 block text-sm font-medium">
                                Surat Dokter
                                <span class="text-red-500">*</span>
                            </label>

                            <input
                                type="file"
                                name="supporting_document"
                                accept=".pdf,.jpg,.jpeg,.png"
                                class="w-full rounded-lg border border-slate-300 bg-white p-2 text-sm">

                            <p class="mt-1 text-xs text-slate-500">
                                PDF, JPG, JPEG, atau PNG. Maksimal 5 MB.
                            </p>

                            @error('supporting_document')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                            @enderror
                        </div>


                        <div
                            id="excessBreakdown"
                            class="hidden rounded-xl border border-amber-200 bg-amber-50 p-5">
                            <h3 class="font-semibold text-amber-900">
                                Perhitungan Pengajuan
                            </h3>

                            <p
                                id="excessBreakdownDescription"
                                class="mt-1 text-sm leading-relaxed text-amber-700"></p>

                            <div class="mt-5 overflow-hidden rounded-lg border border-amber-200 bg-white">

                                <div class="grid grid-cols-2 border-b border-slate-100 px-4 py-3">
                                    <span class="text-sm text-slate-500">
                                        Total Pengajuan
                                    </span>
                                    <span
                                        id="previewTotalDays"
                                        class="text-right text-sm font-semibold text-slate-800">
                                        -
                                    </span>
                                </div>

                                <div class="grid grid-cols-2 border-b border-slate-100 px-4 py-3">
                                    <span
                                        id="coveredDaysLabel"
                                        class="text-sm text-slate-500">
                                        Hak dari Klinik
                                    </span>
                                    <span
                                        id="previewCoveredDays"
                                        class="text-right text-sm font-semibold text-emerald-600">
                                        -
                                    </span>
                                </div>

                                <div class="grid grid-cols-2 border-b border-slate-100 px-4 py-3">
                                    <span class="text-sm text-slate-500">
                                        Menggunakan Cuti Tahunan
                                    </span>
                                    <span
                                        id="previewAnnualDays"
                                        class="text-right text-sm font-semibold text-blue-600">
                                        -
                                    </span>
                                </div>

                                <div class="grid grid-cols-2 px-4 py-3">
                                    <span
                                        id="uncoveredDaysLabel"
                                        class="text-sm text-slate-500">
                                        Hari Tidak Dibayar
                                    </span>
                                    <span
                                        id="previewUnpaidDays"
                                        class="text-right text-sm font-semibold text-red-600">
                                        -
                                    </span>
                                </div>

                            </div>

                            <div
                                id="salaryConsentContainer"
                                class="mt-5 hidden rounded-lg border border-red-200 bg-red-50 p-4">
                                <label class="flex cursor-pointer items-start gap-3">
                                    <input
                                        type="checkbox"
                                        id="salaryDeductionConsent"
                                        name="salary_deduction_consent"
                                        value="1"
                                        @checked(old('salary_deduction_consent'))
                                        class="mt-1 rounded border-red-300 text-red-600 focus:ring-red-500">

                                    <div>
                                        <p class="text-sm font-semibold text-red-800">
                                            Persetujuan Hari Tidak Dibayar
                                        </p>

                                        <p
                                            id="salaryConsentText"
                                            class="mt-1 text-sm leading-relaxed text-red-700"></p>
                                    </div>
                                </label>
                            </div>

                            @error('salary_deduction_consent')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                            @enderror
                        </div>


                        <div
                            id="flexibleHandlingSection"
                            class="hidden rounded-xl border border-blue-200 bg-blue-50 p-5">

                            <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                                <div>
                                    <h3
                                        id="flexibleHandlingTitle"
                                        class="font-semibold text-blue-900">
                                        Pembagian Hari Perizinan
                                    </h3>

                                    <p
                                        id="flexibleHandlingDescription"
                                        class="mt-1 text-sm leading-relaxed text-blue-700">
                                        Tentukan berapa hari yang ingin menggunakan saldo cuti tahunan.
                                        Sisa hari otomatis menjadi tanggung jawab mencari pengganti
                                        dan membayar pengganti secara mandiri.
                                    </p>
                                </div>

                                <div class="rounded-lg bg-white px-3 py-2 text-right shadow-sm">
                                    <p class="text-xs text-slate-400">Saldo cuti tersedia</p>
                                    <p class="font-bold text-blue-700">
                                        {{ $annualLeaveAvailableDays }} hari
                                    </p>
                                </div>
                            </div>

                            <div class="mt-5 grid gap-4 md:grid-cols-2">
                                <div class="rounded-xl border border-blue-100 bg-white p-4">
                                    <label
                                        for="excessAnnualLeaveDays"
                                        class="block text-sm font-semibold text-slate-800">
                                        Gunakan Cuti Tahunan
                                    </label>

                                    <div class="mt-3 flex items-center gap-3">
                                        <input
                                            type="number"
                                            id="excessAnnualLeaveDays"
                                            name="excess_annual_leave_days"
                                            min="0"
                                            step="1"
                                            value="{{ old('excess_annual_leave_days', old('sick_annual_leave_days', 0)) }}"
                                            class="w-28 rounded-lg border-slate-300 text-center focus:border-blue-500 focus:ring-blue-500">

                                        <span class="text-sm text-slate-500">hari</span>
                                    </div>

                                    <p
                                        id="flexibleAnnualMaxText"
                                        class="mt-2 text-xs text-slate-500">
                                        Maksimal menyesuaikan jumlah hari yang perlu dibagi dan saldo cuti.
                                    </p>

                                    @error('excess_annual_leave_days')
                                    <p class="mt-2 text-sm text-red-600">
                                        {{ $message }}
                                    </p>
                                    @enderror

                                    @error('sick_annual_leave_days')
                                    <p class="mt-2 text-sm text-red-600">
                                        {{ $message }}
                                    </p>
                                    @enderror
                                </div>

                                <div class="rounded-xl border border-amber-200 bg-amber-50 p-4">
                                    <p class="text-sm font-semibold text-amber-900">
                                        Cari Pengganti & Biaya Mandiri
                                    </p>

                                    <p
                                        id="flexibleSelfReplacementDays"
                                        class="mt-3 text-3xl font-bold text-amber-700">
                                        0 hari
                                    </p>

                                    <p class="mt-2 text-xs leading-relaxed text-amber-700">
                                        Untuk hari ini nama pengganti tidak wajib dimasukkan ke sistem.
                                        Koordinasi pengganti dan pembayarannya menjadi tanggung jawab pemohon.
                                    </p>
                                </div>
                            </div>

                            <div class="mt-5 rounded-xl border border-blue-200 bg-white p-4">
                                <p class="text-sm font-semibold text-blue-900">
                                    Ketentuan biaya pengganti
                                </p>

                                <div class="mt-2 space-y-1 text-sm leading-relaxed text-blue-700">
                                    <p>
                                        • Hari yang menggunakan <strong>cuti tahunan</strong>:
                                        pengganti dicatat di sistem dan biaya dibayar <strong>Perusahaan</strong>.
                                    </p>
                                    <p>
                                        • Hari yang <strong>tidak menggunakan cuti tahunan</strong>:
                                        pengganti dan biaya menjadi tanggung jawab <strong>Saya / Pemohon</strong>.
                                    </p>
                                </div>
                            </div>

                            <div
                                id="selfReplacementConsentContainer"
                                class="mt-5 hidden rounded-xl border border-amber-300 bg-amber-100/70 p-4">
                                <label class="flex cursor-pointer items-start gap-3">
                                    <input
                                        type="checkbox"
                                        id="selfReplacementConsent"
                                        name="self_replacement_consent"
                                        value="1"
                                        @checked(old('self_replacement_consent'))
                                        class="mt-1 rounded border-amber-400 text-amber-600 focus:ring-amber-500">

                                    <div>
                                        <p class="text-sm font-semibold text-amber-900">
                                            Konfirmasi Pengganti Mandiri
                                        </p>
                                        <p
                                            id="selfReplacementConsentText"
                                            class="mt-1 text-sm leading-relaxed text-amber-800"></p>
                                    </div>
                                </label>
                            </div>

                            @error('self_replacement_consent')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                            @enderror
                        </div>


                        <div>
                            <label class="mb-2 block text-sm font-medium">
                                Alasan / Keperluan
                                <span class="text-red-500">*</span>
                            </label>

                            <textarea
                                name="reason"
                                rows="4"
                                placeholder="Tuliskan alasan atau keperluan..."
                                class="w-full resize-none rounded-lg border-slate-300">{{ old('reason') }}</textarea>

                            @error('reason')
                            <p class="mt-1 text-sm text-red-600">
                                {{ $message }}
                            </p>
                            @enderror
                        </div>

                    </div>
                </section>


                <div class="border-t border-slate-200"></div>


                <section id="generalSubstituteQuestion">
                    <h2 class="font-semibold text-slate-800">
                        Pengganti
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Apakah ada hari dalam periode perizinan yang membutuhkan orang pengganti?
                    </p>

                    <div class="mt-5 grid gap-4 sm:grid-cols-2">
                        <label class="cursor-pointer rounded-xl border border-slate-200 p-4">
                            <input
                                type="radio"
                                name="has_substitute"
                                value="0"
                                @checked(old('has_substitute', '0' )==='0' )>

                            <span class="ml-2 font-medium">
                                Tidak Ada Hari yang Membutuhkan Pengganti
                            </span>
                        </label>

                        <label class="cursor-pointer rounded-xl border border-slate-200 p-4">
                            <input
                                type="radio"
                                name="has_substitute"
                                value="1"
                                @checked(old('has_substitute')==='1' )>

                            <span class="ml-2 font-medium">
                                Ada Hari yang Membutuhkan Pengganti
                            </span>
                        </label>
                    </div>

                    @error('has_substitute')
                    <p class="mt-2 text-sm text-red-600">
                        {{ $message }}
                    </p>
                    @enderror
                </section>


                <section
                    id="substituteSection"
                    class="hidden space-y-5 border-t border-slate-200 pt-7">
                    <div>
                        <h2 class="font-semibold text-slate-800">
                            Pengganti per Tanggal
                        </h2>

                        <p class="mt-1 text-sm text-slate-500">
                            Centang hanya tanggal yang membutuhkan pengganti. Orang pengganti boleh berbeda pada setiap hari.
                        </p>
                    </div>


                    <div
                        id="companySubstituteRequirement"
                        class="hidden rounded-xl border border-indigo-200 bg-indigo-50 p-4">
                        <p class="text-sm font-semibold text-indigo-900">
                            Pengganti yang Dibayar Perusahaan
                        </p>

                        <p
                            id="companySubstituteRequirementText"
                            class="mt-1 text-sm leading-relaxed text-indigo-700">
                        </p>
                    </div>


                    <div class="rounded-xl border border-blue-200 bg-blue-50 p-4">
                        <p class="text-sm font-medium text-blue-800">
                            Cara cepat
                        </p>

                        <p class="mt-1 text-xs leading-relaxed text-blue-700">
                            Isi data pada hari terpilih pertama. Jika orangnya sama, gunakan tombol
                            <strong>Samakan Data Pengganti</strong>.
                            Jika jadwalnya juga sama, gunakan
                            <strong>Samakan Jadwal Penggantian</strong>.
                        </p>
                    </div>


                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-5">
                        <div class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
                            <div>
                                <h3 class="font-semibold text-slate-800">
                                    Jadwal Penggantian
                                </h3>

                                <p class="mt-1 text-sm text-slate-500">
                                    Pilih hari yang membutuhkan pengganti, lalu isi orang dan jadwalnya.
                                </p>
                            </div>

                            <div class="flex flex-wrap gap-2">
                                <button
                                    type="button"
                                    id="toggleAllSubstituteDays"
                                    class="hidden rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs font-medium text-slate-700 hover:bg-slate-100">
                                    Pilih Semua Hari
                                </button>

                                <button
                                    type="button"
                                    id="copyFirstSubstituteData"
                                    class="hidden rounded-lg border border-blue-200 bg-blue-50 px-3 py-2 text-xs font-medium text-blue-700 hover:bg-blue-100">
                                    Samakan Data Pengganti
                                </button>

                                <button
                                    type="button"
                                    id="copyFirstSchedule"
                                    class="hidden rounded-lg border border-indigo-200 bg-indigo-50 px-3 py-2 text-xs font-medium text-indigo-700 hover:bg-indigo-100">
                                    Samakan Jadwal Penggantian
                                </button>
                            </div>
                        </div>


                        <div
                            id="scheduleHint"
                            class="mt-5 rounded-lg border border-dashed border-slate-300 bg-white p-5 text-center text-sm text-slate-500">
                            Pilih tanggal perizinan terlebih dahulu.
                        </div>


                        <div
                            id="scheduleContainer"
                            class="mt-5 grid gap-4"></div>


                        @error('substitute_schedules')
                        <p class="mt-3 text-sm text-red-600">
                            {{ $message }}
                        </p>
                        @enderror
                    </div>
                </section>

            </div>


            <div class="flex justify-end gap-3 border-t border-slate-200 bg-slate-50 px-6 py-4">
                <a
                    href="{{ route('kabid.leave-requests.index') }}"
                    class="rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-600">
                    Batal
                </a>

                <button
                    type="submit"
                    id="submitPermissionButton"
                    class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:bg-slate-300">
                    Ajukan Perizinan
                </button>
            </div>

        </form>
    </div>

</div>


@php
$permissionPageData = [
'annualLeaveAvailableDays' => (int) $annualLeaveAvailableDays,
'annualLeaveEligible' => (bool) $annualLeaveEligible,
'annualLeaveEligibleDate' => $annualLeaveEligibleDate
?->format('Y-m-d'),
'workShifts' => $workShifts
->map(
fn ($shift) => [
'id' => $shift->id,
'name' => $shift->name,
]
)
->values()
->all(),
'oldSchedules' => old(
'substitute_schedules',
[]
),
];
@endphp

<textarea
    id="permissionPageData"
    class="hidden"
    aria-hidden="true"
    tabindex="-1">{!! json_encode(
    $permissionPageData,
    JSON_HEX_TAG
    | JSON_HEX_AMP
    | JSON_HEX_APOS
    | JSON_HEX_QUOT
) !!}</textarea>


<script>
    document.addEventListener('DOMContentLoaded', function() {

        const permissionPageDataElement =
            document.getElementById('permissionPageData');

        let permissionPageData = {};

        if (permissionPageDataElement) {
            try {
                permissionPageData = JSON.parse(
                    permissionPageDataElement.value
                );
            } catch (error) {
                console.error(
                    'Gagal membaca data halaman perizinan.',
                    error
                );
            }
        }

        const annualLeaveAvailableDays = Number(
            permissionPageData.annualLeaveAvailableDays ?? 0
        );

        const annualLeaveEligible = Boolean(
            permissionPageData.annualLeaveEligible
        );

        const annualLeaveEligibleDate =
            permissionPageData.annualLeaveEligibleDate || null;

        const workShifts = Array.isArray(
                permissionPageData.workShifts
            ) ?
            permissionPageData.workShifts : [];

        const oldSchedules = Array.isArray(
                permissionPageData.oldSchedules
            ) ?
            permissionPageData.oldSchedules : [];


        const permissionType = document.getElementById('permissionType');
        const maternityFields = document.getElementById('maternityFields');
        const maternityPolicyInfo = document.getElementById('maternityPolicyInfo');
        const maternityPolicyText = document.getElementById('maternityPolicyText');
        const doctorLetterFields = document.getElementById('doctorLetterFields');
        const startDate = document.getElementById('startDate');
        const endDate = document.getElementById('endDate');
        const expectedDeliveryDate = document.getElementById('expectedDeliveryDate');

        const annualLeaveValidation = document.getElementById('annualLeaveValidation');
        const annualLeaveValidationTitle = document.getElementById('annualLeaveValidationTitle');
        const annualLeaveValidationText = document.getElementById('annualLeaveValidationText');

        const excessBreakdown = document.getElementById('excessBreakdown');
        const excessBreakdownDescription = document.getElementById('excessBreakdownDescription');
        const coveredDaysLabel = document.getElementById('coveredDaysLabel');
        const previewTotalDays = document.getElementById('previewTotalDays');
        const previewCoveredDays = document.getElementById('previewCoveredDays');
        const previewAnnualDays = document.getElementById('previewAnnualDays');
        const previewUnpaidDays = document.getElementById('previewUnpaidDays');
        const uncoveredDaysLabel = document.getElementById('uncoveredDaysLabel');
        const salaryConsentContainer = document.getElementById('salaryConsentContainer');
        const salaryDeductionConsent = document.getElementById('salaryDeductionConsent');
        const salaryConsentText = document.getElementById('salaryConsentText');

        const flexibleHandlingSection = document.getElementById('flexibleHandlingSection');
        const flexibleHandlingTitle = document.getElementById('flexibleHandlingTitle');
        const flexibleHandlingDescription = document.getElementById('flexibleHandlingDescription');
        const excessAnnualLeaveDays = document.getElementById('excessAnnualLeaveDays');
        const flexibleAnnualMaxText = document.getElementById('flexibleAnnualMaxText');
        const flexibleSelfReplacementDays = document.getElementById('flexibleSelfReplacementDays');
        const selfReplacementConsentContainer = document.getElementById('selfReplacementConsentContainer');
        const selfReplacementConsent = document.getElementById('selfReplacementConsent');
        const selfReplacementConsentText = document.getElementById('selfReplacementConsentText');

        const generalSubstituteQuestion = document.getElementById('generalSubstituteQuestion');
        const companySubstituteRequirement = document.getElementById('companySubstituteRequirement');
        const companySubstituteRequirementText = document.getElementById('companySubstituteRequirementText');
        const substituteSection = document.getElementById('substituteSection');
        const scheduleContainer = document.getElementById('scheduleContainer');
        const scheduleHint = document.getElementById('scheduleHint');
        const toggleAllSubstituteDays = document.getElementById('toggleAllSubstituteDays');
        const copyFirstSubstituteData = document.getElementById('copyFirstSubstituteData');
        const copyFirstSchedule = document.getElementById('copyFirstSchedule');
        const submitPermissionButton = document.getElementById('submitPermissionButton');


        let annualRequestIsValid = true;
        let unpaidConsentRequired = false;
        let selfReplacementConsentRequired = false;
        let companyPayerIsValid = true;


        const oldScheduleMap = {};

        oldSchedules.forEach(function(item) {
            if (item.schedule_date) {
                oldScheduleMap[item.schedule_date] = item;
            }
        });


        function escapeHtml(value) {
            const div = document.createElement('div');
            div.textContent = value ?? '';
            return div.innerHTML;
        }


        function getPermissionCode() {
            const option = permissionType.options[permissionType.selectedIndex];
            return option?.dataset.code || '';
        }


        function updateSubmitState() {
            const salaryConsentValid = !unpaidConsentRequired ||
                salaryDeductionConsent.checked;

            const replacementConsentValid = !selfReplacementConsentRequired ||
                selfReplacementConsent.checked;

            submitPermissionButton.disabled = !annualRequestIsValid ||
                !salaryConsentValid ||
                !replacementConsentValid ||
                !companyPayerIsValid;
        }


        function getNormalCalendarDays() {
            if (!startDate.value || !endDate.value) {
                return 0;
            }

            const start = new Date(startDate.value + 'T00:00:00');
            const end = new Date(endDate.value + 'T00:00:00');

            if (end < start) {
                return 0;
            }

            return Math.floor((end - start) / 86400000) + 1;
        }


        function countAnnualLeaveDays(startValue, endValue) {
            if (!startValue || !endValue) {
                return 0;
            }

            const start = new Date(startValue + 'T00:00:00');
            const end = new Date(endValue + 'T00:00:00');

            if (end < start) {
                return 0;
            }

            /*
             * Semua tanggal dihitung secara inklusif.
             *
             * Contoh:
             * 28/08/2026 - 30/08/2026 = 3 hari,
             * termasuk hari Minggu.
             */
            return Math.floor((end - start) / 86400000) + 1;
        }


        function formatSimpleDate(value) {
            if (!value) {
                return '-';
            }

            const date = new Date(value + 'T00:00:00');

            return date.toLocaleDateString(
                'id-ID', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric',
                }
            );
        }


        function addMonthsNoOverflow(sourceDate, months) {
            const date = new Date(sourceDate.getTime());
            const originalDay = date.getDate();

            date.setDate(1);
            date.setMonth(date.getMonth() + months);

            const lastDay = new Date(
                date.getFullYear(),
                date.getMonth() + 1,
                0
            ).getDate();

            date.setDate(
                Math.min(originalDay, lastDay)
            );

            return date;
        }


        function toDateString(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');

            return `${year}-${month}-${day}`;
        }


        function applyMaternityDefaultDates(force = false) {
            if (
                getPermissionCode() !== 'maternity' ||
                !expectedDeliveryDate.value
            ) {
                return;
            }

            const expected = new Date(
                expectedDeliveryDate.value + 'T00:00:00'
            );

            const defaultStart = addMonthsNoOverflow(expected, -1);
            const defaultEnd = addMonthsNoOverflow(expected, 1);

            if (force || !startDate.value) {
                startDate.value = toDateString(defaultStart);
            }

            if (force || !endDate.value) {
                endDate.value = toDateString(defaultEnd);
            }
        }


        function updateMaternityPolicyInfo() {
            maternityPolicyInfo.classList.add('hidden');

            if (
                getPermissionCode() !== 'maternity' ||
                !expectedDeliveryDate.value
            ) {
                return;
            }

            const expected = new Date(
                expectedDeliveryDate.value + 'T00:00:00'
            );

            const policyStart = addMonthsNoOverflow(expected, -1);
            const policyEnd = addMonthsNoOverflow(expected, 1);

            maternityPolicyText.textContent =
                `Berdasarkan perkiraan tanggal melahirkan, periode hak cuti melahirkan adalah ${formatSimpleDate(
                toDateString(policyStart)
            )} sampai ${formatSimpleDate(
                toDateString(policyEnd)
            )}. Tanggal mulai dan selesai boleh diedit. Hari di luar periode ini akan menggunakan sisa cuti tahunan, kemudian menjadi hari tidak dibayar jika saldo tidak mencukupi.`;

            maternityPolicyInfo.classList.remove('hidden');
        }


        function showAnnualLeaveMessage(type, title, message) {
            annualLeaveValidation.classList.remove(
                'hidden',
                'border-red-200',
                'bg-red-50',
                'text-red-700',
                'border-emerald-200',
                'bg-emerald-50',
                'text-emerald-700',
                'border-blue-200',
                'bg-blue-50',
                'text-blue-700'
            );

            if (type === 'error') {
                annualLeaveValidation.classList.add(
                    'border-red-200',
                    'bg-red-50',
                    'text-red-700'
                );
            } else if (type === 'success') {
                annualLeaveValidation.classList.add(
                    'border-emerald-200',
                    'bg-emerald-50',
                    'text-emerald-700'
                );
            } else {
                annualLeaveValidation.classList.add(
                    'border-blue-200',
                    'bg-blue-50',
                    'text-blue-700'
                );
            }

            annualLeaveValidationTitle.textContent = title;
            annualLeaveValidationText.textContent = message;
        }


        function validateAnnualLeavePreview() {
            annualRequestIsValid = true;
            annualLeaveValidation.classList.add('hidden');

            if (getPermissionCode() !== 'annual_leave') {
                updateSubmitState();
                return;
            }

            if (!startDate.value || !endDate.value) {
                showAnnualLeaveMessage(
                    'info',
                    'Pilih periode cuti',
                    'Sistem akan menghitung jumlah hari cuti dan membandingkannya dengan sisa cuti tahunan Anda.'
                );

                updateSubmitState();
                return;
            }

            const start = new Date(startDate.value + 'T00:00:00');
            const end = new Date(endDate.value + 'T00:00:00');

            if (end < start) {
                annualRequestIsValid = false;

                showAnnualLeaveMessage(
                    'error',
                    'Tanggal tidak valid',
                    'Tanggal selesai tidak boleh sebelum tanggal mulai.'
                );

                updateSubmitState();
                return;
            }

            if (start.getFullYear() !== end.getFullYear()) {
                annualRequestIsValid = false;

                showAnnualLeaveMessage(
                    'error',
                    'Periode melewati pergantian tahun',
                    'Cuti tahunan harus diajukan dalam tahun yang sama.'
                );

                updateSubmitState();
                return;
            }

            if (
                annualLeaveEligibleDate &&
                startDate.value < annualLeaveEligibleDate
            ) {
                annualRequestIsValid = false;

                showAnnualLeaveMessage(
                    'error',
                    'Belum berhak menggunakan cuti tahunan',
                    `Hak cuti tahunan mulai tersedia pada ${formatSimpleDate(
                    annualLeaveEligibleDate
                )}.`
                );

                updateSubmitState();
                return;
            }

            const requestedDays = countAnnualLeaveDays(
                startDate.value,
                endDate.value
            );

            if (requestedDays <= 0) {
                annualRequestIsValid = false;

                showAnnualLeaveMessage(
                    'error',
                    'Periode tidak dapat digunakan',
                    'Periode cuti yang dipilih tidak valid.'
                );

                updateSubmitState();
                return;
            }

            if (requestedDays > 3) {
                annualRequestIsValid = false;

                showAnnualLeaveMessage(
                    'error',
                    'Maksimal 3 hari per pengajuan',
                    `Satu kali pengajuan cuti tahunan maksimal 3 hari. Semua tanggal dalam rentang ikut dihitung. Periode yang dipilih menggunakan ${requestedDays} hari cuti.`
                );

                updateSubmitState();
                return;
            }

            if (requestedDays > annualLeaveAvailableDays) {
                annualRequestIsValid = false;

                const shortage =
                    requestedDays - annualLeaveAvailableDays;

                showAnnualLeaveMessage(
                    'error',
                    'Pengajuan melebihi sisa cuti tahunan',
                    `Periode menggunakan ${requestedDays} hari cuti, sedangkan sisa yang dapat diajukan hanya ${annualLeaveAvailableDays} hari. Kurangi periode sebanyak ${shortage} hari.`
                );

                updateSubmitState();
                return;
            }

            showAnnualLeaveMessage(
                'success',
                'Jatah cuti mencukupi',
                `Periode ini menggunakan ${requestedDays} hari cuti. Jika disetujui, sisa cuti menjadi ${annualLeaveAvailableDays - requestedDays} hari.`
            );

            updateSubmitState();
        }


        function annualLeaveCanCoverFlexibleDays() {
            if (
                !annualLeaveEligible ||
                annualLeaveAvailableDays <= 0 ||
                !startDate.value ||
                !endDate.value
            ) {
                return false;
            }

            if (
                annualLeaveEligibleDate &&
                startDate.value < annualLeaveEligibleDate
            ) {
                return false;
            }

            const start = new Date(startDate.value + 'T00:00:00');
            const end = new Date(endDate.value + 'T00:00:00');

            return end >= start &&
                start.getFullYear() === end.getFullYear();
        }


        function updateFlexibleBreakdown(
            totalDays,
            coveredDays,
            permissionName,
            coveredLabel,
            description
        ) {
            const excessDays = Math.max(
                0,
                totalDays - coveredDays
            );

            uncoveredDaysLabel.textContent =
                'Cari Pengganti & Biaya Mandiri';

            flexibleHandlingSection.classList.add('hidden');
            selfReplacementConsentContainer.classList.add('hidden');
            selfReplacementConsentRequired = false;

            if (excessDays <= 0) {
                excessAnnualLeaveDays.value = '0';
                flexibleSelfReplacementDays.textContent = '0 hari';
                updateFeePayerAvailability();
                updateSubmitState();
                return;
            }

            const maxAnnualDays = annualLeaveCanCoverFlexibleDays() ?
                Math.min(
                    excessDays,
                    annualLeaveAvailableDays
                ) :
                0;

            excessAnnualLeaveDays.max = String(maxAnnualDays);

            let selectedAnnualDays = Number.parseInt(
                excessAnnualLeaveDays.value || '0',
                10
            );

            if (
                Number.isNaN(selectedAnnualDays) ||
                selectedAnnualDays < 0
            ) {
                selectedAnnualDays = 0;
            }

            if (selectedAnnualDays > maxAnnualDays) {
                selectedAnnualDays = maxAnnualDays;
                excessAnnualLeaveDays.value = String(maxAnnualDays);
            }

            const selfReplacementDays = Math.max(
                0,
                excessDays - selectedAnnualDays
            );

            coveredDaysLabel.textContent = coveredLabel;
            previewTotalDays.textContent = `${totalDays} hari`;
            previewCoveredDays.textContent = `${coveredDays} hari`;
            previewAnnualDays.textContent = `${selectedAnnualDays} hari`;
            previewUnpaidDays.textContent = `${selfReplacementDays} hari`;

            excessBreakdownDescription.textContent =
                description +
                ` Dari ${excessDays} hari yang perlu dibagi, ` +
                `${selectedAnnualDays} hari menggunakan cuti tahunan dan ` +
                `${selfReplacementDays} hari menjadi tanggung jawab pengganti serta biaya mandiri.`;

            flexibleHandlingTitle.textContent =
                `Pembagian Hari ${permissionName}`;

            flexibleHandlingDescription.textContent =
                `Tentukan berapa dari ${excessDays} hari yang ingin menggunakan saldo cuti tahunan. ` +
                `Hari yang memakai cuti tahunan harus memiliki data pengganti dan biaya dibayar Perusahaan. ` +
                `Sisa hari otomatis menjadi tanggung jawab Saya / Pemohon.`;

            flexibleAnnualMaxText.textContent =
                maxAnnualDays > 0 ?
                `Maksimal ${maxAnnualDays} hari dari ${excessDays} hari. Saldo cuti yang dapat diajukan saat ini: ${annualLeaveAvailableDays} hari.` :
                'Cuti tahunan tidak dapat digunakan untuk periode ini karena belum memenuhi masa kerja, saldo habis/belum tersedia, atau periode melewati tahun.';

            flexibleSelfReplacementDays.textContent =
                `${selfReplacementDays} hari`;

            excessBreakdown.classList.remove('hidden');
            flexibleHandlingSection.classList.remove('hidden');

            if (selfReplacementDays > 0) {
                selfReplacementConsentRequired = true;

                selfReplacementConsentText.textContent =
                    `Saya memahami bahwa ${selfReplacementDays} hari pada pengajuan ${permissionName.toLowerCase()} tidak menggunakan cuti tahunan. ` +
                    `Saya bertanggung jawab mencari pengganti sendiri dan menyelesaikan biaya pengganti secara pribadi.`;

                selfReplacementConsentContainer.classList.remove('hidden');
            }

            updateFeePayerAvailability();
            updateSubmitState();
        }


        function updateMaternityBreakdown() {
            if (
                !expectedDeliveryDate.value ||
                !startDate.value ||
                !endDate.value
            ) {
                updateSubmitState();
                return;
            }

            const requestedStart = new Date(
                startDate.value + 'T00:00:00'
            );

            const requestedEnd = new Date(
                endDate.value + 'T00:00:00'
            );

            if (requestedEnd < requestedStart) {
                updateSubmitState();
                return;
            }

            const expected = new Date(
                expectedDeliveryDate.value + 'T00:00:00'
            );

            const policyStart = addMonthsNoOverflow(expected, -1);
            const policyEnd = addMonthsNoOverflow(expected, 1);

            const totalDays =
                Math.floor(
                    (requestedEnd - requestedStart) /
                    86400000
                ) + 1;

            const coveredStart =
                requestedStart > policyStart ?
                requestedStart :
                policyStart;

            const coveredEnd =
                requestedEnd < policyEnd ?
                requestedEnd :
                policyEnd;

            let coveredDays = 0;

            if (coveredStart <= coveredEnd) {
                coveredDays =
                    Math.floor(
                        (coveredEnd - coveredStart) /
                        86400000
                    ) + 1;
            }

            updateFlexibleBreakdown(
                totalDays,
                coveredDays,
                'Cuti Melahirkan',
                'Hak Cuti Melahirkan',
                `Periode hak cuti melahirkan adalah ${formatSimpleDate(
                    toDateString(policyStart)
                )} sampai ${formatSimpleDate(
                    toDateString(policyEnd)
                )}.`
            );
        }


        function updateExcessBreakdown() {
            excessBreakdown.classList.add('hidden');
            salaryConsentContainer.classList.add('hidden');
            flexibleHandlingSection.classList.add('hidden');
            selfReplacementConsentContainer.classList.add('hidden');

            unpaidConsentRequired = false;
            selfReplacementConsentRequired = false;
            uncoveredDaysLabel.textContent =
                'Cari Pengganti & Biaya Mandiri';

            const code = getPermissionCode();

            if (!code) {
                updateFeePayerAvailability();
                updateSubmitState();
                return;
            }

            if (code === 'annual_leave') {
                excessAnnualLeaveDays.value = '0';
                updateFeePayerAvailability();
                updateSubmitState();
                return;
            }

            if (code === 'maternity') {
                updateMaternityBreakdown();
                return;
            }

            const totalDays = getNormalCalendarDays();

            if (totalDays <= 0) {
                updateFeePayerAvailability();
                updateSubmitState();
                return;
            }

            if (code === 'sick') {
                updateFlexibleBreakdown(
                    totalDays,
                    Math.min(totalDays, 1),
                    'Izin Sakit',
                    'Hak Izin Sakit',
                    'Izin sakit memiliki hak 1 hari sesuai ketentuan.'
                );
                return;
            }

            if (code === 'marriage') {
                updateFlexibleBreakdown(
                    totalDays,
                    Math.min(totalDays, 3),
                    'Izin Menikah',
                    'Hak Izin Menikah',
                    'Izin menikah memiliki hak 3 hari sesuai ketentuan.'
                );
                return;
            }

            if (code === 'miscarriage') {
                updateFlexibleBreakdown(
                    totalDays,
                    Math.min(totalDays, 7),
                    'Cuti Keguguran',
                    'Hak Cuti Keguguran',
                    'Cuti keguguran memiliki hak maksimal 7 hari sesuai ketentuan.'
                );
                return;
            }

            if (code === 'other') {
                updateFlexibleBreakdown(
                    totalDays,
                    0,
                    'Izin Lainnya',
                    'Hak Khusus Klinik',
                    'Izin lainnya tidak memiliki jatah hari khusus. Anda bebas menentukan berapa hari yang ingin menggunakan saldo cuti tahunan.'
                );
                return;
            }

            updateFeePayerAvailability();
            updateSubmitState();
        }


        function updatePermissionFields() {
            const code = getPermissionCode();

            maternityFields.classList.add('hidden');
            maternityPolicyInfo.classList.add('hidden');
            doctorLetterFields.classList.add('hidden');
            generalSubstituteQuestion.classList.remove('hidden');

            if (code === 'sick') {
                doctorLetterFields.classList.remove('hidden');
            }

            if (code === 'maternity') {
                maternityFields.classList.remove('hidden');

                /*
                 * Saat load ulang karena validation error,
                 * jangan menimpa old start/end yang sudah ada.
                 */
                applyMaternityDefaultDates(false);
                updateMaternityPolicyInfo();
            }

            validateAnnualLeavePreview();
            updateExcessBreakdown();
            renderSchedules();
        }


        function getSelectedDateRange() {
            if (!startDate.value || !endDate.value) {
                return [];
            }

            const start = new Date(
                startDate.value + 'T00:00:00'
            );

            const end = new Date(
                endDate.value + 'T00:00:00'
            );

            if (end < start) {
                return [];
            }

            const dates = [];
            const cursor = new Date(start.getTime());

            while (cursor <= end) {
                dates.push(
                    toDateString(cursor)
                );

                cursor.setDate(
                    cursor.getDate() + 1
                );
            }

            return dates;
        }


        function formatDateIndonesia(value) {
            const date = new Date(
                value + 'T00:00:00'
            );

            return date.toLocaleDateString(
                'id-ID', {
                    weekday: 'long',
                    day: '2-digit',
                    month: 'long',
                    year: 'numeric',
                }
            );
        }


        function collectCurrentSchedules() {
            const values = {};

            scheduleContainer
                .querySelectorAll('[data-schedule-card]')
                .forEach(function(card) {
                    const date = card.dataset.date;

                    values[date] = {
                        schedule_date: date,
                        selected: card.querySelector(
                            '[data-selected-input]'
                        )?.value || '0',
                        substitute_name: card.querySelector(
                            '[data-substitute-name]'
                        )?.value || '',
                        substitute_whatsapp: card.querySelector(
                            '[data-substitute-whatsapp]'
                        )?.value || '',
                        substitute_fee_payer: card.querySelector(
                            'input[data-fee-payer]:checked'
                        )?.value || 'employee',
                        substitute_bank_name: card.querySelector(
                            '[data-substitute-bank-name]'
                        )?.value || '',
                        substitute_bank_account_number: card.querySelector(
                            '[data-substitute-bank-account-number]'
                        )?.value || '',
                        substitute_bank_account_holder: card.querySelector(
                            '[data-substitute-bank-account-holder]'
                        )?.value || '',
                        schedule_type: card.querySelector(
                            'input[data-schedule-type]:checked'
                        )?.value || '',
                        work_shift_id: card.querySelector(
                            '[data-work-shift]'
                        )?.value || '',
                        start_time: card.querySelector(
                            '[data-start-time]'
                        )?.value || '',
                        end_time: card.querySelector(
                            '[data-end-time]'
                        )?.value || '',
                    };
                });

            return values;
        }


        function buildShiftOptions(selectedId) {
            let html =
                '<option value="">-- Pilih Shift --</option>';

            workShifts.forEach(function(shift) {
                const selected =
                    String(selectedId) === String(shift.id) ?
                    'selected' :
                    '';

                html += `
                    <option value="${shift.id}" ${selected}>
                        ${escapeHtml(shift.name)}
                    </option>
                `;
            });

            return html;
        }


        function isCardSelected(card) {
            return card.querySelector(
                '[data-selected-input]'
            )?.value === '1';
        }


        function getSelectedCards() {
            return Array.from(
                scheduleContainer.querySelectorAll(
                    '[data-schedule-card]'
                )
            ).filter(isCardSelected);
        }


        function getAnnualLeaveDaysForCurrentRequest() {
            const code = getPermissionCode();

            if (
                !startDate.value ||
                !endDate.value
            ) {
                return 0;
            }

            if (code === 'annual_leave') {
                if (
                    !annualLeaveEligible ||
                    annualLeaveAvailableDays <= 0
                ) {
                    return 0;
                }

                const requested = countAnnualLeaveDays(
                    startDate.value,
                    endDate.value
                );

                return Math.max(
                    0,
                    Math.min(
                        requested,
                        annualLeaveAvailableDays,
                        3
                    )
                );
            }

            if (!annualLeaveCanCoverFlexibleDays()) {
                return 0;
            }

            const selected = Number.parseInt(
                excessAnnualLeaveDays.value || '0',
                10
            );

            if (Number.isNaN(selected) || selected < 0) {
                return 0;
            }

            return selected;
        }


        function updateFeePayerFields(card) {
            const selectedPayer = card.querySelector(
                'input[data-fee-payer]:checked'
            )?.value || 'employee';

            const bankFields = card.querySelector(
                '[data-company-bank-fields]'
            );

            const bankInputs = [
                card.querySelector('[data-substitute-bank-name]'),
                card.querySelector('[data-substitute-bank-account-number]'),
                card.querySelector('[data-substitute-bank-account-holder]'),
            ].filter(Boolean);

            if (!bankFields) {
                return;
            }

            if (selectedPayer === 'company' && isCardSelected(card)) {
                bankFields.classList.remove('hidden');
                bankInputs.forEach(function(input) {
                    input.required = true;
                });
            } else {
                bankFields.classList.add('hidden');
                bankInputs.forEach(function(input) {
                    input.required = false;
                });
            }
        }


        function updateFeePayerAvailability() {
            const selectedCards = getSelectedCards();
            const companyAllowance = getAnnualLeaveDaysForCurrentRequest();

            const noSubstituteRadio = document.querySelector(
                'input[name="has_substitute"][value="0"]'
            );

            const yesSubstituteRadio = document.querySelector(
                'input[name="has_substitute"][value="1"]'
            );

            if (companyAllowance > 0) {
                /*
                 * Setiap hari yang memakai cuti tahunan wajib punya data
                 * pengganti yang dibayar Perusahaan.
                 */
                if (yesSubstituteRadio) {
                    yesSubstituteRadio.checked = true;
                }

                if (noSubstituteRadio) {
                    noSubstituteRadio.disabled = true;
                }

                substituteSection.classList.remove('hidden');
                companySubstituteRequirement.classList.remove('hidden');
                companySubstituteRequirementText.textContent =
                    `Pengajuan ini memakai ${companyAllowance} hari cuti tahunan. ` +
                    `Pilih tepat ${companyAllowance} tanggal pengganti, isi nama/WhatsApp/jadwal, ` +
                    `lalu pilih biaya "Perusahaan" dan lengkapi data rekening.`;
            } else {
                if (noSubstituteRadio) {
                    noSubstituteRadio.disabled = false;
                }

                companySubstituteRequirement.classList.add('hidden');
                companySubstituteRequirementText.textContent = '';
            }

            /*
             * Jika jumlah pilihan Perusahaan melebihi jatah cuti karena
             * tanggal / pembagian diubah, kelebihannya dikembalikan menjadi
             * Saya / Pemohon.
             */
            let remainingCompanySlots = companyAllowance;

            selectedCards.forEach(function(card) {
                const companyRadio = card.querySelector(
                    'input[data-company-fee-payer]'
                );

                const employeeRadio = card.querySelector(
                    'input[data-fee-payer][value="employee"]'
                );

                if (!companyRadio || !employeeRadio) {
                    return;
                }

                if (companyRadio.checked) {
                    if (remainingCompanySlots > 0) {
                        remainingCompanySlots--;
                    } else {
                        employeeRadio.checked = true;
                        updateFeePayerFields(card);
                    }
                }
            });

            const companySelectedCount = selectedCards.filter(function(card) {
                return card.querySelector(
                    'input[data-company-fee-payer]'
                )?.checked;
            }).length;

            /*
             * Berbeda dari revisi sebelumnya yang hanya memakai batas
             * "maksimal". Sekarang harus TEPAT sejumlah hari cuti tahunan,
             * karena hari yang memakai cuti wajib memiliki nama pengganti.
             */
            companyPayerIsValid = companyAllowance > 0 ?
                companySelectedCount === companyAllowance :
                companySelectedCount === 0;

            selectedCards.forEach(function(card) {
                const companyRadio = card.querySelector(
                    'input[data-company-fee-payer]'
                );

                const note = card.querySelector(
                    '[data-company-fee-note]'
                );

                if (!companyRadio) {
                    return;
                }

                if (companyAllowance <= 0) {
                    companyRadio.disabled = true;

                    if (note) {
                        note.textContent =
                            'Perusahaan hanya dapat dipilih pada hari yang menggunakan saldo cuti tahunan.';
                        note.className =
                            'mt-3 text-xs leading-relaxed text-amber-700';
                    }
                } else {
                    const isCurrentCompany = companyRadio.checked;

                    companyRadio.disabled = !isCurrentCompany &&
                        companySelectedCount >= companyAllowance;

                    if (note) {
                        note.textContent =
                            `Wajib ada tepat ${companyAllowance} hari dengan biaya Perusahaan karena pengajuan memakai ${companyAllowance} hari cuti tahunan. Saat ini dipilih ${companySelectedCount} hari.`;
                        note.className =
                            companySelectedCount === companyAllowance ?
                            'mt-3 text-xs leading-relaxed text-emerald-700' :
                            'mt-3 text-xs leading-relaxed text-blue-700';
                    }
                }

                updateFeePayerFields(card);
            });

            updateSubmitState();
        }


        function updateBulkButtons() {
            const cards = Array.from(
                scheduleContainer.querySelectorAll(
                    '[data-schedule-card]'
                )
            );

            const selectedCards = getSelectedCards();

            if (cards.length > 0) {
                toggleAllSubstituteDays.classList.remove('hidden');
            } else {
                toggleAllSubstituteDays.classList.add('hidden');
            }

            if (selectedCards.length > 1) {
                copyFirstSubstituteData.classList.remove('hidden');
                copyFirstSchedule.classList.remove('hidden');
            } else {
                copyFirstSubstituteData.classList.add('hidden');
                copyFirstSchedule.classList.add('hidden');
            }

            const allSelected =
                cards.length > 0 &&
                selectedCards.length === cards.length;

            toggleAllSubstituteDays.textContent = allSelected ?
                'Batalkan Pilih Semua' :
                'Pilih Semua Hari';
        }


        function updateScheduleCard(card) {
            const selected = isCardSelected(card);

            const details = card.querySelector(
                '[data-substitute-details]'
            );

            const selectedBadge = card.querySelector(
                '[data-selected-badge]'
            );

            const type = card.querySelector(
                'input[data-schedule-type]:checked'
            )?.value;

            const full = card.querySelector(
                '[data-full-shift-fields]'
            );

            const partial = card.querySelector(
                '[data-partial-fields]'
            );

            if (selected) {
                details.classList.remove('hidden');
                selectedBadge.classList.remove('hidden');
                card.classList.add('border-blue-300', 'bg-blue-50/30');
            } else {
                details.classList.add('hidden');
                selectedBadge.classList.add('hidden');
                card.classList.remove('border-blue-300', 'bg-blue-50/30');
            }

            full.classList.add('hidden');
            partial.classList.add('hidden');

            if (selected && type === 'full_shift') {
                full.classList.remove('hidden');
            }

            if (selected && type === 'partial_hours') {
                partial.classList.remove('hidden');
            }

            updateFeePayerFields(card);
            updateBulkButtons();
        }


        function renderSchedules() {
            const hasSubstitute = document.querySelector(
                'input[name="has_substitute"]:checked'
            )?.value;

            if (hasSubstitute !== '1') {
                scheduleContainer.innerHTML = '';
                scheduleHint.classList.remove('hidden');
                toggleAllSubstituteDays.classList.add('hidden');
                copyFirstSubstituteData.classList.add('hidden');
                copyFirstSchedule.classList.add('hidden');
                companyPayerIsValid = true;
                updateSubmitState();
                return;
            }

            const currentValues = collectCurrentSchedules();
            const dates = getSelectedDateRange();

            scheduleContainer.innerHTML = '';

            if (dates.length === 0) {
                scheduleHint.textContent =
                    'Pilih tanggal perizinan terlebih dahulu.';

                scheduleHint.classList.remove('hidden');
                toggleAllSubstituteDays.classList.add('hidden');
                copyFirstSubstituteData.classList.add('hidden');
                copyFirstSchedule.classList.add('hidden');
                companyPayerIsValid = true;
                updateSubmitState();
                return;
            }

            scheduleHint.classList.add('hidden');

            dates.forEach(function(date, index) {
                const saved =
                    currentValues[date] ||
                    oldScheduleMap[date] || {};

                /*
                 * Data lama belum punya field selected.
                 * Jika schedule_type sudah ada, anggap tanggal itu terpilih.
                 */
                const selected =
                    String(saved.selected ?? '') === '1' ||
                    saved.selected === true ||
                    (
                        saved.selected === undefined &&
                        Boolean(saved.schedule_type)
                    );

                const fullChecked =
                    saved.schedule_type === 'full_shift' ?
                    'checked' :
                    '';

                const partialChecked =
                    saved.schedule_type === 'partial_hours' ?
                    'checked' :
                    '';

                const feePayer =
                    saved.substitute_fee_payer === 'company' ?
                    'company' :
                    'employee';

                const employeeFeeChecked =
                    feePayer === 'employee' ? 'checked' : '';

                const companyFeeChecked =
                    feePayer === 'company' ? 'checked' : '';

                const card = document.createElement('div');

                card.dataset.scheduleCard = '1';
                card.dataset.date = date;
                card.className =
                    'rounded-xl border border-slate-200 bg-white p-5 transition';

                card.innerHTML = `
                    <input
                        type="hidden"
                        name="substitute_schedules[${index}][schedule_date]"
                        value="${date}"
                    >

                    <input
                        type="hidden"
                        data-selected-input
                        name="substitute_schedules[${index}][selected]"
                        value="${selected ? '1' : '0'}"
                    >

                    <div class="flex flex-col gap-3 border-b border-slate-100 pb-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <p class="text-sm font-semibold text-slate-800">
                                    ${escapeHtml(formatDateIndonesia(date))}
                                </p>

                                <span
                                    data-selected-badge
                                    class="${selected ? '' : 'hidden'} inline-flex rounded-full bg-blue-100 px-2.5 py-1 text-xs font-medium text-blue-700"
                                >
                                    Ada Pengganti
                                </span>
                            </div>

                            <p class="mt-1 text-xs text-slate-500">
                                Aktifkan hanya jika tanggal ini membutuhkan pengganti.
                            </p>
                        </div>

                        <label class="inline-flex cursor-pointer items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700">
                            <input
                                type="checkbox"
                                data-selected-toggle
                                ${selected ? 'checked' : ''}
                                class="rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                            >

                            Membutuhkan Pengganti
                        </label>
                    </div>

                    <div
                        data-substitute-details
                        class="${selected ? '' : 'hidden'} mt-5 space-y-6"
                    >
                        <div>
                            <h4 class="text-sm font-semibold text-slate-800">
                                Data Orang Pengganti
                            </h4>

                            <p class="mt-1 text-xs text-slate-500">
                                Data ini hanya berlaku untuk tanggal ini.
                            </p>
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="mb-2 block text-sm font-medium">
                                    Nama Pengganti *
                                </label>

                                <input
                                    type="text"
                                    data-substitute-name
                                    name="substitute_schedules[${index}][substitute_name]"
                                    value="${escapeHtml(saved.substitute_name || '')}"
                                    class="w-full rounded-lg border-slate-300"
                                >
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-medium">
                                    WhatsApp *
                                </label>

                                <input
                                    type="text"
                                    data-substitute-whatsapp
                                    name="substitute_schedules[${index}][substitute_whatsapp]"
                                    value="${escapeHtml(saved.substitute_whatsapp || '')}"
                                    class="w-full rounded-lg border-slate-300"
                                >
                            </div>
                        </div>

                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                            <p class="text-sm font-semibold text-slate-800">
                                Biaya pengganti dibayar oleh:
                            </p>

                            <div class="mt-3 grid gap-3 sm:grid-cols-2">
                                <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-slate-200 bg-white p-3">
                                    <input
                                        type="radio"
                                        data-fee-payer
                                        name="substitute_schedules[${index}][substitute_fee_payer]"
                                        value="employee"
                                        ${employeeFeeChecked}
                                        class="mt-1 border-slate-300 text-blue-600 focus:ring-blue-500"
                                    >

                                    <div>
                                        <p class="text-sm font-semibold text-slate-800">
                                            Saya / Pemohon
                                        </p>
                                        <p class="mt-1 text-xs leading-relaxed text-slate-500">
                                            Biaya pengganti diselesaikan sendiri oleh karyawan yang mengajukan izin.
                                        </p>
                                    </div>
                                </label>

                                <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-slate-200 bg-white p-3">
                                    <input
                                        type="radio"
                                        data-fee-payer
                                        data-company-fee-payer
                                        name="substitute_schedules[${index}][substitute_fee_payer]"
                                        value="company"
                                        ${companyFeeChecked}
                                        class="mt-1 border-slate-300 text-blue-600 focus:ring-blue-500"
                                    >

                                    <div>
                                        <p class="text-sm font-semibold text-slate-800">
                                            Perusahaan
                                        </p>
                                        <p class="mt-1 text-xs leading-relaxed text-slate-500">
                                            Hanya tersedia jika pengajuan memakai saldo cuti tahunan.
                                        </p>
                                    </div>
                                </label>
                            </div>

                            <p
                                data-company-fee-note
                                class="mt-3 text-xs leading-relaxed text-slate-500"
                            ></p>

                            <div
                                data-company-bank-fields
                                class="${feePayer === 'company' ? '' : 'hidden'} mt-4 rounded-lg border border-blue-200 bg-blue-50 p-4"
                            >
                                <p class="text-sm font-semibold text-blue-900">
                                    Data Rekening Pengganti
                                </p>
                                <p class="mt-1 text-xs text-blue-700">
                                    Wajib diisi karena biaya pengganti akan dibayarkan oleh perusahaan.
                                </p>

                                <div class="mt-4 grid gap-4 md:grid-cols-2">
                                    <div>
                                        <label class="mb-2 block text-sm font-medium">
                                            Nama Bank *
                                        </label>

                                        <input
                                            type="text"
                                            data-substitute-bank-name
                                            name="substitute_schedules[${index}][substitute_bank_name]"
                                            value="${escapeHtml(saved.substitute_bank_name || '')}"
                                            class="w-full rounded-lg border-slate-300"
                                        >
                                    </div>

                                    <div>
                                        <label class="mb-2 block text-sm font-medium">
                                            Nomor Rekening *
                                        </label>

                                        <input
                                            type="text"
                                            data-substitute-bank-account-number
                                            name="substitute_schedules[${index}][substitute_bank_account_number]"
                                            value="${escapeHtml(saved.substitute_bank_account_number || '')}"
                                            class="w-full rounded-lg border-slate-300"
                                        >
                                    </div>

                                    <div class="md:col-span-2">
                                        <label class="mb-2 block text-sm font-medium">
                                            Atas Nama Rekening *
                                        </label>

                                        <input
                                            type="text"
                                            data-substitute-bank-account-holder
                                            name="substitute_schedules[${index}][substitute_bank_account_holder]"
                                            value="${escapeHtml(saved.substitute_bank_account_holder || '')}"
                                            class="w-full rounded-lg border-slate-300"
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="border-t border-slate-200 pt-5">
                            <h4 class="text-sm font-semibold text-slate-800">
                                Jadwal Penggantian
                            </h4>

                            <div class="mt-4 grid gap-3 sm:grid-cols-2">
                                <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-slate-200 bg-white p-3">
                                    <input
                                        type="radio"
                                        data-schedule-type
                                        name="substitute_schedules[${index}][schedule_type]"
                                        value="full_shift"
                                        ${fullChecked}
                                    >

                                    <div>
                                        <p class="text-sm font-medium">
                                            Full Shift
                                        </p>

                                        <p class="text-xs text-slate-500">
                                            Menggantikan satu shift penuh.
                                        </p>
                                    </div>
                                </label>

                                <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-slate-200 bg-white p-3">
                                    <input
                                        type="radio"
                                        data-schedule-type
                                        name="substitute_schedules[${index}][schedule_type]"
                                        value="partial_hours"
                                        ${partialChecked}
                                    >

                                    <div>
                                        <p class="text-sm font-medium">
                                            Beberapa Jam
                                        </p>

                                        <p class="text-xs text-slate-500">
                                            Menggantikan pada jam tertentu.
                                        </p>
                                    </div>
                                </label>
                            </div>

                            <div
                                data-full-shift-fields
                                class="mt-4 hidden"
                            >
                                <label class="mb-2 block text-sm font-medium">
                                    Shift *
                                </label>

                                <select
                                    data-work-shift
                                    name="substitute_schedules[${index}][work_shift_id]"
                                    class="w-full rounded-lg border-slate-300"
                                >
                                    ${buildShiftOptions(saved.work_shift_id || '')}
                                </select>
                            </div>

                            <div
                                data-partial-fields
                                class="mt-4 hidden"
                            >
                                <div class="grid gap-4 sm:grid-cols-2">
                                    <div>
                                        <label class="mb-2 block text-sm font-medium">
                                            Jam Mulai *
                                        </label>

                                        <input
                                            data-start-time
                                            type="time"
                                            name="substitute_schedules[${index}][start_time]"
                                            value="${escapeHtml(saved.start_time || '')}"
                                            class="w-full rounded-lg border-slate-300"
                                        >
                                    </div>

                                    <div>
                                        <label class="mb-2 block text-sm font-medium">
                                            Jam Selesai *
                                        </label>

                                        <input
                                            data-end-time
                                            type="time"
                                            name="substitute_schedules[${index}][end_time]"
                                            value="${escapeHtml(saved.end_time || '')}"
                                            class="w-full rounded-lg border-slate-300"
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                scheduleContainer.appendChild(card);
                updateScheduleCard(card);
            });

            updateBulkButtons();
            updateFeePayerAvailability();
        }


        function updateSubstitute() {
            const value = document.querySelector(
                'input[name="has_substitute"]:checked'
            )?.value;

            if (value === '1') {
                substituteSection.classList.remove('hidden');
            } else {
                substituteSection.classList.add('hidden');
            }

            renderSchedules();
        }


        scheduleContainer.addEventListener(
            'change',
            function(event) {
                const card = event.target.closest(
                    '[data-schedule-card]'
                );

                if (!card) {
                    return;
                }

                if (event.target.matches('[data-selected-toggle]')) {
                    const selectedInput = card.querySelector(
                        '[data-selected-input]'
                    );

                    selectedInput.value = event.target.checked ?
                        '1' :
                        '0';

                    updateScheduleCard(card);
                    updateFeePayerAvailability();
                    return;
                }

                if (event.target.matches('input[data-fee-payer]')) {
                    updateFeePayerFields(card);
                    updateFeePayerAvailability();
                    return;
                }

                if (event.target.matches('input[data-schedule-type]')) {
                    updateScheduleCard(card);
                }
            }
        );


        toggleAllSubstituteDays.addEventListener(
            'click',
            function() {
                const cards = Array.from(
                    scheduleContainer.querySelectorAll(
                        '[data-schedule-card]'
                    )
                );

                if (cards.length === 0) {
                    return;
                }

                const selectedCards = getSelectedCards();

                const shouldSelectAll =
                    selectedCards.length !== cards.length;

                cards.forEach(function(card) {
                    const toggle = card.querySelector(
                        '[data-selected-toggle]'
                    );

                    const selectedInput = card.querySelector(
                        '[data-selected-input]'
                    );

                    toggle.checked = shouldSelectAll;
                    selectedInput.value = shouldSelectAll ? '1' : '0';

                    updateScheduleCard(card);
                });

                updateBulkButtons();
                updateFeePayerAvailability();
            }
        );


        copyFirstSubstituteData.addEventListener(
            'click',
            function() {
                const selectedCards = getSelectedCards();

                if (selectedCards.length < 2) {
                    alert(
                        'Pilih minimal dua hari yang membutuhkan pengganti.'
                    );
                    return;
                }

                const first = selectedCards[0];

                const source = {
                    name: first.querySelector(
                        '[data-substitute-name]'
                    )?.value || '',
                    whatsapp: first.querySelector(
                        '[data-substitute-whatsapp]'
                    )?.value || '',
                    feePayer: first.querySelector(
                        'input[data-fee-payer]:checked'
                    )?.value || 'employee',
                    bankName: first.querySelector(
                        '[data-substitute-bank-name]'
                    )?.value || '',
                    accountNumber: first.querySelector(
                        '[data-substitute-bank-account-number]'
                    )?.value || '',
                    accountHolder: first.querySelector(
                        '[data-substitute-bank-account-holder]'
                    )?.value || '',
                };

                if (!source.name) {
                    alert(
                        'Isi data pengganti pada hari terpilih pertama terlebih dahulu.'
                    );
                    return;
                }

                selectedCards.slice(1).forEach(function(card) {
                    card.querySelector(
                        '[data-substitute-name]'
                    ).value = source.name;

                    card.querySelector(
                        '[data-substitute-whatsapp]'
                    ).value = source.whatsapp;

                    const feePayerRadio = card.querySelector(
                        `input[data-fee-payer][value="${source.feePayer}"]`
                    );

                    if (feePayerRadio && !feePayerRadio.disabled) {
                        feePayerRadio.checked = true;
                    } else {
                        const employeeRadio = card.querySelector(
                            'input[data-fee-payer][value="employee"]'
                        );

                        if (employeeRadio) {
                            employeeRadio.checked = true;
                        }
                    }

                    card.querySelector(
                        '[data-substitute-bank-name]'
                    ).value = source.bankName;

                    card.querySelector(
                        '[data-substitute-bank-account-number]'
                    ).value = source.accountNumber;

                    card.querySelector(
                        '[data-substitute-bank-account-holder]'
                    ).value = source.accountHolder;

                    updateFeePayerFields(card);
                });

                updateFeePayerAvailability();
            }
        );


        copyFirstSchedule.addEventListener(
            'click',
            function() {
                const selectedCards = getSelectedCards();

                if (selectedCards.length < 2) {
                    alert(
                        'Pilih minimal dua hari yang membutuhkan pengganti.'
                    );
                    return;
                }

                const first = selectedCards[0];

                const firstType = first.querySelector(
                    'input[data-schedule-type]:checked'
                )?.value;

                if (!firstType) {
                    alert(
                        'Isi jadwal pada hari terpilih pertama terlebih dahulu.'
                    );
                    return;
                }

                const firstShift = first.querySelector(
                    '[data-work-shift]'
                )?.value || '';

                const firstStart = first.querySelector(
                    '[data-start-time]'
                )?.value || '';

                const firstEnd = first.querySelector(
                    '[data-end-time]'
                )?.value || '';

                selectedCards.slice(1).forEach(function(card) {
                    const radio = card.querySelector(
                        `input[data-schedule-type][value="${firstType}"]`
                    );

                    if (radio) {
                        radio.checked = true;
                    }

                    const shift = card.querySelector(
                        '[data-work-shift]'
                    );

                    const start = card.querySelector(
                        '[data-start-time]'
                    );

                    const end = card.querySelector(
                        '[data-end-time]'
                    );

                    if (shift) {
                        shift.value = firstShift;
                    }

                    if (start) {
                        start.value = firstStart;
                    }

                    if (end) {
                        end.value = firstEnd;
                    }

                    updateScheduleCard(card);
                });
            }
        );


        permissionType.addEventListener(
            'change',
            function() {
                updatePermissionFields();
            }
        );


        expectedDeliveryDate.addEventListener(
            'change',
            function() {
                /*
                 * HPL berubah:
                 * otomatis isi start = HPL - 1 bulan
                 * dan end = HPL + 1 bulan.
                 * Setelah itu user boleh mengedit kedua tanggal.
                 */
                applyMaternityDefaultDates(true);

                updateMaternityPolicyInfo();
                validateAnnualLeavePreview();
                updateExcessBreakdown();
                renderSchedules();
            }
        );


        startDate.addEventListener(
            'change',
            function() {
                validateAnnualLeavePreview();
                updateExcessBreakdown();
                renderSchedules();
            }
        );


        endDate.addEventListener(
            'change',
            function() {
                validateAnnualLeavePreview();
                updateExcessBreakdown();
                renderSchedules();
            }
        );


        salaryDeductionConsent.addEventListener(
            'change',
            function() {
                updateSubmitState();
            }
        );


        excessAnnualLeaveDays.addEventListener(
            'input',
            function() {
                updateExcessBreakdown();
                renderSchedules();
            }
        );

        selfReplacementConsent.addEventListener(
            'change',
            function() {
                updateSubmitState();
            }
        );


        document
            .querySelectorAll(
                'input[name="has_substitute"]'
            )
            .forEach(function(input) {
                input.addEventListener(
                    'change',
                    updateSubstitute
                );
            });


        updatePermissionFields();
        updateSubstitute();
        validateAnnualLeavePreview();
        updateExcessBreakdown();
        updateMaternityPolicyInfo();
        updateSubmitState();
    });
</script>

@endsection