@extends('layouts.admin')

@section('title', 'Detail Perubahan Profil')
@section('page-title', 'Detail Perubahan Profil')

@section('content')

@php
$profileUser = $profileUpdateRequest->user;

$fieldLabels = [
'name' => 'Nama Lengkap',
'email' => 'Email',
'nip' => 'NIP / ID Pegawai',
'nik_ktp' => 'NIK KTP',
'whatsapp' => 'WhatsApp',
'join_date' => 'Tanggal Mulai Kerja',
'department_id' => 'Bidang',
'birth_place' => 'Tempat Lahir',
'birth_date' => 'Tanggal Lahir',
'ktp_address' => 'Alamat KTP',
'domicile_address' => 'Alamat Domisili',
'blood_type' => 'Golongan Darah',
'religion' => 'Agama',
'sip_number' => 'Nomor SIP',
'sip_valid_from' => 'SIP Berlaku Mulai',
'sip_valid_until' => 'SIP Berlaku Sampai',
'bank_account_number' => 'Nomor Rekening BSI',
'bank_account_name' => 'Nama Pemilik Rekening',
];

$dateFields = [
'join_date',
'birth_date',
'sip_valid_from',
'sip_valid_until',
];

$formatValue = function ($field, $value) use (
$departmentLookup,
$dateFields
) {
if ($value === null || $value === '') {
return '-';
}

if ($field === 'department_id') {
return $departmentLookup[$value]
?? ('Bidang #' . $value);
}

if (in_array($field, $dateFields, true)) {
try {
return \Carbon\Carbon::parse($value)
->translatedFormat('d F Y');
} catch (\Throwable $e) {
return $value;
}
}

return $value;
};
@endphp

<div class="space-y-6">

    @if(session('success'))
    <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
        {{ session('success') }}
    </div>
    @endif

    @if($errors->any())
    <div class="rounded-xl border border-red-200 bg-red-50 p-4">
        <p class="font-semibold text-red-700">
            Pengajuan belum dapat diproses.
        </p>

        <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-red-600">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif


    {{-- HEADER --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <a
                href="{{ route('admin.profile-updates.index') }}"
                class="text-sm font-medium text-blue-600 hover:text-blue-700">
                ← Kembali ke Perubahan Profil
            </a>

            <h1 class="mt-2 text-xl font-bold text-slate-900 sm:text-2xl">
                {{ $profileUser?->name ?? 'Pegawai tidak tersedia' }}
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                {{ $profileUser?->role === 'kabid' ? 'Kabid' : 'Karyawan' }}
                •
                {{ $profileUser?->department?->name ?? '-' }}
                •
                NIP {{ $profileUser?->nip ?? $profileUser?->nik ?? '-' }}
            </p>
        </div>

        <div>
            @if($profileUpdateRequest->status === 'pending')
            <span class="inline-flex rounded-full bg-amber-100 px-3 py-1.5 text-xs font-semibold text-amber-700">
                Menunggu Persetujuan
            </span>
            @elseif($profileUpdateRequest->status === 'approved')
            <span class="inline-flex rounded-full bg-emerald-100 px-3 py-1.5 text-xs font-semibold text-emerald-700">
                Disetujui
            </span>
            @else
            <span class="inline-flex rounded-full bg-red-100 px-3 py-1.5 text-xs font-semibold text-red-700">
                Ditolak
            </span>
            @endif
        </div>
    </div>


    {{-- META --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                Diajukan
            </p>
            <p class="mt-2 text-sm font-semibold text-slate-700">
                {{ $profileUpdateRequest->created_at?->translatedFormat('d F Y H:i') }}
            </p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                Jumlah Perubahan
            </p>
            <p class="mt-2 text-sm font-semibold text-slate-700">
                {{ count($profileUpdateRequest->new_data ?? []) }}
                field
                {{ $profileUpdateRequest->new_photo_path ? '+ pas foto' : '' }}
            </p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                Direview
            </p>
            <p class="mt-2 text-sm font-semibold text-slate-700">
                @if($profileUpdateRequest->reviewed_at)
                {{ $profileUpdateRequest->reviewed_at->translatedFormat('d F Y H:i') }}
                <span class="font-normal text-slate-500">
                    oleh {{ $profileUpdateRequest->reviewer?->name ?? 'Admin' }}
                </span>
                @else
                Belum direview
                @endif
            </p>
        </div>
    </div>


    {{-- PERBANDINGAN DATA --}}
    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 bg-slate-50/70 px-5 py-4 sm:px-6">
            <h2 class="font-semibold text-slate-800">
                Perbandingan Data
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Admin hanya menerapkan field yang memang diajukan berubah.
            </p>
        </div>

        @if(! empty($profileUpdateRequest->new_data))
        <div class="hidden grid-cols-[1fr_1fr_1fr] gap-4 border-b border-slate-200 bg-slate-50 px-5 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500 md:grid">
            <div>Data</div>
            <div>Data Aktif Saat Pengajuan</div>
            <div>Data Baru</div>
        </div>

        <div class="divide-y divide-slate-200">
            @foreach($profileUpdateRequest->new_data as $field => $newValue)
            <div class="grid gap-3 px-5 py-5 md:grid-cols-[1fr_1fr_1fr]">
                <div>
                    <p class="font-semibold text-slate-800">
                        {{ $fieldLabels[$field] ?? $field }}
                    </p>
                </div>

                <div class="min-w-0">
                    <p class="mb-1 text-xs font-semibold uppercase tracking-wide text-slate-400 md:hidden">
                        Sebelum
                    </p>

                    <p class="break-words whitespace-pre-line text-sm text-slate-600">
                        {{ $formatValue(
                            $field,
                            $profileUpdateRequest->old_data[$field] ?? null
                        ) }}
                    </p>
                </div>

                <div class="min-w-0 rounded-xl bg-blue-50 px-3 py-2 md:rounded-none md:bg-transparent md:px-0 md:py-0">
                    <p class="mb-1 text-xs font-semibold uppercase tracking-wide text-blue-400 md:hidden">
                        Data Baru
                    </p>

                    <p class="break-words whitespace-pre-line text-sm font-semibold text-blue-700">
                        {{ $formatValue(
                            $field,
                            $newValue
                        ) }}
                    </p>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="px-5 py-8 text-center text-sm text-slate-500">
            Tidak ada perubahan data teks. Pengajuan ini hanya mengganti pas foto.
        </div>
        @endif
    </section>


    {{-- FOTO --}}
    @if(
    $profileUpdateRequest->new_photo_path
    || $profileUser?->formal_photo_path
    )
    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 bg-slate-50/70 px-5 py-4 sm:px-6">
            <h2 class="font-semibold text-slate-800">
                Perbandingan Pas Foto
            </h2>
        </div>

        <div class="grid gap-6 p-5 sm:grid-cols-2 sm:p-6">
            <div>
                <p class="mb-3 text-sm font-semibold text-slate-600">
                    Foto Aktif
                </p>

                @if($profileUser?->formal_photo_path)
                <img
                    src="{{ route(
                        'admin.profile-updates.current-photo',
                        $profileUpdateRequest
                    ) }}"
                    alt="Foto aktif"
                    class="h-56 w-44 rounded-xl border border-slate-200 object-cover shadow-sm">
                @else
                <div class="flex h-56 w-44 items-center justify-center rounded-xl bg-slate-100 text-sm text-slate-400">
                    Belum ada foto
                </div>
                @endif
            </div>

            <div>
                <p class="mb-3 text-sm font-semibold text-blue-700">
                    Foto Baru yang Diajukan
                </p>

                @if($profileUpdateRequest->new_photo_path)
                <img
                    src="{{ route(
                        'admin.profile-updates.proposed-photo',
                        $profileUpdateRequest
                    ) }}"
                    alt="Foto baru"
                    class="h-56 w-44 rounded-xl border border-blue-200 object-cover shadow-sm">
                @else
                <div class="flex h-56 w-44 items-center justify-center rounded-xl bg-slate-100 text-sm text-slate-400">
                    Tidak diganti
                </div>
                @endif
            </div>
        </div>
    </section>
    @endif


    {{-- HASIL REVIEW --}}
    @if($profileUpdateRequest->status === 'rejected')
    <section class="rounded-2xl border border-red-200 bg-red-50 p-5 sm:p-6">
        <h2 class="font-semibold text-red-800">
            Alasan Penolakan
        </h2>

        <p class="mt-2 whitespace-pre-line text-sm leading-6 text-red-700">
            {{ $profileUpdateRequest->rejection_reason ?: '-' }}
        </p>
    </section>
    @endif


    {{-- AKSI ADMIN --}}
    @if($profileUpdateRequest->status === 'pending')
    <div class="grid gap-5 lg:grid-cols-2">
        <section class="rounded-2xl border border-red-200 bg-white p-5 shadow-sm sm:p-6">
            <h2 class="font-semibold text-slate-800">
                Tolak Perubahan
            </h2>

            <p class="mt-1 text-sm leading-6 text-slate-500">
                Data aktif pegawai tidak akan berubah. Berikan alasan agar pegawai tahu apa yang harus diperbaiki.
            </p>

            <form
                method="POST"
                action="{{ route(
                    'admin.profile-updates.reject',
                    $profileUpdateRequest
                ) }}"
                class="mt-4">

                @csrf
                @method('PATCH')

                <label for="rejection_reason" class="block text-sm font-semibold text-slate-700">
                    Alasan Penolakan <span class="text-red-500">*</span>
                </label>

                <textarea
                    id="rejection_reason"
                    name="rejection_reason"
                    rows="4"
                    minlength="5"
                    maxlength="2000"
                    required
                    placeholder="Contoh: NIK KTP belum sesuai. Mohon cek kembali..."
                    class="mt-2 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-red-500 focus:ring-red-500">{{ old('rejection_reason') }}</textarea>

                <button
                    type="submit"
                    onclick="return confirm('Tolak pengajuan perubahan profil ini?')"
                    class="mt-4 w-full rounded-xl border border-red-300 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700 hover:bg-red-100">
                    Tolak & Kirim Alasan
                </button>
            </form>
        </section>


        <section class="rounded-2xl border border-emerald-200 bg-white p-5 shadow-sm sm:p-6">
            <h2 class="font-semibold text-slate-800">
                Setujui Perubahan
            </h2>

            <p class="mt-1 text-sm leading-6 text-slate-500">
                Setelah disetujui, data baru langsung menjadi data aktif pegawai di seluruh SIMI-MS.
            </p>

            <div class="mt-4 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm leading-6 text-amber-800">
                Pastikan NIP, NIK KTP, bidang, rekening BSI, dan pas foto sudah benar sebelum menyetujui.
            </div>

            <form
                method="POST"
                action="{{ route(
                    'admin.profile-updates.approve',
                    $profileUpdateRequest
                ) }}"
                class="mt-4">

                @csrf
                @method('PATCH')

                <button
                    type="submit"
                    onclick="return confirm('Setujui perubahan profil ini? Data aktif pegawai akan diperbarui.')"
                    class="w-full rounded-xl bg-emerald-600 px-4 py-3 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700">
                    ✓ Setujui Perubahan Profil
                </button>
            </form>
        </section>
    </div>
    @endif
</div>
@endsection