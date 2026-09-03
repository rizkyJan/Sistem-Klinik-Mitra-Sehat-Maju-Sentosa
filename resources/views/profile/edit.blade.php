@extends(
auth()->user()->role === 'kabid'
? 'layouts.kabid'
: (
auth()->user()->role === 'karyawan'
? 'layouts.karyawan'
: 'layouts.admin'
)
)

@section('title', 'Profil Saya')
@section('page-title', 'Profil Saya')

@section('content')

@php
$isAdmin = $user->role === 'admin';
$hasPending = filled($pendingProfileUpdateRequest);

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

$formatProfileValue = function ($field, $value) use (
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

    {{-- ============================================================
         FLASH MESSAGE
    ============================================================ --}}
    @foreach([
    'success' => ['emerald', 'Berhasil'],
    'error' => ['red', 'Perhatian'],
    'info' => ['blue', 'Informasi'],
    ] as $flashKey => [$tone, $title])
    @if(session($flashKey))
    <div class="rounded-xl border
                    {{ $tone === 'emerald' ? 'border-emerald-200 bg-emerald-50 text-emerald-800' : '' }}
                    {{ $tone === 'red' ? 'border-red-200 bg-red-50 text-red-800' : '' }}
                    {{ $tone === 'blue' ? 'border-blue-200 bg-blue-50 text-blue-800' : '' }}
                    px-4 py-3 text-sm">
        <strong>{{ $title }}:</strong>
        {{ session($flashKey) }}
    </div>
    @endif
    @endforeach

    @if($errors->any())
    <div class="rounded-xl border border-red-200 bg-red-50 p-4">
        <p class="font-semibold text-red-700">
            Data belum dapat diproses.
        </p>

        <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-red-600">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif


    {{-- ============================================================
         ADMIN PROFILE
    ============================================================ --}}
    @if($isAdmin)
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 px-5 py-4 sm:px-6">
            <h2 class="font-semibold text-slate-800">
                Profil Administrator
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Data akun Administrator diperbarui langsung dan tidak memerlukan persetujuan.
            </p>
        </div>

        <form
            method="POST"
            action="{{ route('profile.update') }}"
            class="grid gap-5 p-5 sm:p-6 md:grid-cols-2">

            @csrf
            @method('PATCH')

            <div>
                <label for="name" class="block text-sm font-semibold text-slate-700">
                    Nama
                </label>

                <input
                    id="name"
                    name="name"
                    type="text"
                    required
                    value="{{ old('name', $user->name) }}"
                    class="mt-2 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label for="email" class="block text-sm font-semibold text-slate-700">
                    Email
                </label>

                <input
                    id="email"
                    name="email"
                    type="email"
                    required
                    value="{{ old('email', $user->email) }}"
                    class="mt-2 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div class="md:col-span-2 flex justify-end">
                <button
                    type="submit"
                    class="rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">
                    Simpan Profil
                </button>
            </div>
        </form>
    </div>

    @else

    {{-- ============================================================
         HEADER PEGAWAI
    ============================================================ --}}
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex flex-col gap-5 p-5 sm:flex-row sm:items-center sm:p-6">
            <div class="shrink-0">
                @if($user->formal_photo_path)
                <img
                    src="{{ route('profile.photo') }}"
                    alt="Pas foto {{ $user->name }}"
                    class="h-36 w-28 rounded-xl border border-slate-200 object-cover shadow-sm">
                @else
                <div class="flex h-36 w-28 items-center justify-center rounded-xl bg-slate-100 text-3xl font-bold text-slate-500">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                @endif
            </div>

            <div class="min-w-0 flex-1">
                <p class="text-sm font-semibold text-blue-600">
                    {{ $user->role === 'kabid' ? 'Kabid' : 'Karyawan' }}
                    •
                    {{ $user->department?->name ?? 'Bidang belum diatur' }}
                </p>

                <h1 class="mt-1 break-words text-2xl font-bold text-slate-900">
                    {{ $user->name }}
                </h1>

                <div class="mt-3 grid gap-2 text-sm text-slate-600 sm:grid-cols-2">
                    <p>
                        <span class="font-medium">NIP:</span>
                        {{ $user->nip ?? $user->nik ?? '-' }}
                    </p>

                    <p>
                        <span class="font-medium">NIK KTP:</span>
                        {{ $user->nik_ktp ?: '-' }}
                    </p>

                    <p>
                        <span class="font-medium">Email:</span>
                        {{ $user->email }}
                    </p>

                    <p>
                        <span class="font-medium">Bank:</span>
                        Bank Syariah Indonesia (BSI)
                    </p>
                </div>
            </div>

            <div class="shrink-0">
                @if($hasPending)
                <span class="inline-flex rounded-full bg-amber-100 px-3 py-1.5 text-xs font-semibold text-amber-700">
                    Menunggu ACC Admin
                </span>
                @else
                <span class="inline-flex rounded-full bg-emerald-100 px-3 py-1.5 text-xs font-semibold text-emerald-700">
                    Data Aktif
                </span>
                @endif
            </div>
        </div>
    </div>


    {{-- ============================================================
         PENDING / REJECTED REQUEST
    ============================================================ --}}
    @if($pendingProfileUpdateRequest)
    <section class="overflow-hidden rounded-2xl border border-amber-200 bg-white shadow-sm">
        <div class="border-b border-amber-200 bg-amber-50 px-5 py-4 sm:px-6">
            <h2 class="font-semibold text-amber-900">
                Perubahan Profil Menunggu Persetujuan Admin
            </h2>

            <p class="mt-1 text-sm leading-6 text-amber-700">
                Data utama Anda belum berubah. SIMI-MS tetap memakai data aktif sampai Admin menyetujui pengajuan ini.
            </p>
        </div>

        <div class="p-5 sm:p-6">
            @if(! empty($pendingProfileUpdateRequest->new_data))
            <div class="overflow-hidden rounded-xl border border-slate-200">
                <div class="hidden grid-cols-[1fr_1fr_1fr] gap-3 bg-slate-50 px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500 md:grid">
                    <div>Data</div>
                    <div>Sebelum</div>
                    <div>Diajukan</div>
                </div>

                <div class="divide-y divide-slate-200">
                    @foreach($pendingProfileUpdateRequest->new_data as $field => $newValue)
                    <div class="grid gap-3 px-4 py-4 md:grid-cols-[1fr_1fr_1fr]">
                        <div class="font-semibold text-slate-700">
                            {{ $fieldLabels[$field] ?? $field }}
                        </div>

                        <div>
                            <p class="mb-1 text-xs font-medium text-slate-400 md:hidden">
                                Data Aktif
                            </p>
                            <p class="break-words text-sm text-slate-600">
                                {{ $formatProfileValue(
                                    $field,
                                    $pendingProfileUpdateRequest->old_data[$field] ?? null
                                ) }}
                            </p>
                        </div>

                        <div>
                            <p class="mb-1 text-xs font-medium text-blue-500 md:hidden">
                                Data Diajukan
                            </p>
                            <p class="break-words text-sm font-medium text-blue-700">
                                {{ $formatProfileValue($field, $newValue) }}
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            @if($pendingProfileUpdateRequest->new_photo_path)
            <div class="mt-5">
                <p class="mb-2 text-sm font-semibold text-slate-700">
                    Pas Foto Baru yang Diajukan
                </p>

                <img
                    src="{{ route(
                        'profile.pending-photo',
                        $pendingProfileUpdateRequest
                    ) }}"
                    alt="Pas foto baru"
                    class="h-44 w-36 rounded-xl border border-blue-200 object-cover shadow-sm">
            </div>
            @endif

            <p class="mt-5 text-xs text-slate-500">
                Diajukan:
                {{ $pendingProfileUpdateRequest->created_at?->translatedFormat('d F Y H:i') }}
            </p>
        </div>
    </section>

    @elseif(
    $latestProfileUpdateRequest
    && $latestProfileUpdateRequest->status === 'rejected'
    )
    <section class="rounded-2xl border border-red-200 bg-red-50 p-5 sm:p-6">
        <h2 class="font-semibold text-red-800">
            Pengajuan Perubahan Terakhir Ditolak
        </h2>

        <p class="mt-2 text-sm leading-6 text-red-700">
            {{ $latestProfileUpdateRequest->rejection_reason
                ?: 'Admin menolak perubahan profil terakhir.' }}
        </p>

        <p class="mt-2 text-xs text-red-500">
            Anda dapat memperbaiki data pada form di bawah dan mengajukannya kembali.
        </p>
    </section>
    @endif


    {{-- ============================================================
         FORM PENGAJUAN PERUBAHAN
    ============================================================ --}}
    <form
        method="POST"
        action="{{ route('profile.update') }}"
        enctype="multipart/form-data"
        class="space-y-6">

        @csrf
        @method('PATCH')

        <fieldset
            @disabled($hasPending)
            class="space-y-6 {{ $hasPending ? 'opacity-60' : '' }}">

            {{-- IDENTITAS --}}
            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 bg-slate-50/70 px-5 py-4 sm:px-6">
                    <h2 class="font-semibold text-slate-800">
                        Identitas & Kepegawaian
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Semua perubahan di bagian ini baru aktif setelah disetujui Admin.
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-5 p-5 sm:p-6 md:grid-cols-2">
                    <div class="md:col-span-2">
                        <label for="name" class="block text-sm font-semibold text-slate-700">
                            Nama Lengkap <span class="text-red-500">*</span>
                        </label>

                        <input
                            id="name"
                            name="name"
                            type="text"
                            required
                            value="{{ old('name', $user->name) }}"
                            class="mt-2 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="nip" class="block text-sm font-semibold text-slate-700">
                            NIP / ID Pegawai <span class="text-red-500">*</span>
                        </label>

                        <input
                            id="nip"
                            name="nip"
                            type="text"
                            required
                            maxlength="50"
                            value="{{ old('nip', $user->nip ?? $user->nik) }}"
                            class="mt-2 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="nik_ktp" class="block text-sm font-semibold text-slate-700">
                            NIK KTP <span class="text-red-500">*</span>
                        </label>

                        <input
                            id="nik_ktp"
                            name="nik_ktp"
                            type="text"
                            inputmode="numeric"
                            minlength="16"
                            maxlength="16"
                            required
                            value="{{ old('nik_ktp', $user->nik_ktp) }}"
                            class="mt-2 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-semibold text-slate-700">
                            Email <span class="text-red-500">*</span>
                        </label>

                        <input
                            id="email"
                            name="email"
                            type="email"
                            required
                            value="{{ old('email', $user->email) }}"
                            class="mt-2 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="whatsapp" class="block text-sm font-semibold text-slate-700">
                            WhatsApp <span class="text-red-500">*</span>
                        </label>

                        <input
                            id="whatsapp"
                            name="whatsapp"
                            type="text"
                            required
                            value="{{ old('whatsapp', $user->whatsapp) }}"
                            class="mt-2 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="join_date" class="block text-sm font-semibold text-slate-700">
                            Tanggal Mulai Kerja <span class="text-red-500">*</span>
                        </label>

                        <input
                            id="join_date"
                            name="join_date"
                            type="date"
                            max="{{ now()->format('Y-m-d') }}"
                            required
                            value="{{ old(
                                'join_date',
                                optional($user->join_date)->format('Y-m-d')
                            ) }}"
                            class="mt-2 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="department_id" class="block text-sm font-semibold text-slate-700">
                            Bidang <span class="text-red-500">*</span>
                        </label>

                        <select
                            id="department_id"
                            name="department_id"
                            required
                            class="mt-2 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">
                                -- Pilih Bidang --
                            </option>

                            @foreach($departments as $department)
                            <option
                                value="{{ $department->id }}"
                                @selected(
                                (string) old( 'department_id' ,
                                $user->department_id
                                )
                                === (string) $department->id
                                )>
                                {{ $department->name }}
                            </option>
                            @endforeach
                        </select>

                        @if($user->role === 'kabid')
                        <p class="mt-1.5 text-xs text-amber-600">
                            Perubahan bidang Kabid akan diperiksa Admin agar tidak terjadi dua Kabid aktif pada bidang yang sama.
                        </p>
                        @endif
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-slate-700">
                            Role / Hak Akses
                        </label>

                        <div class="mt-2 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700">
                            {{ $user->role === 'kabid' ? 'Kabid' : 'Karyawan' }}
                        </div>

                        <p class="mt-1.5 text-xs text-slate-500">
                            Role tidak dapat diubah dari Profil Saya. Perubahan role hanya dilakukan Administrator.
                        </p>
                    </div>
                </div>
            </section>


            {{-- BIODATA --}}
            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 bg-slate-50/70 px-5 py-4 sm:px-6">
                    <h2 class="font-semibold text-slate-800">
                        Biodata Pribadi
                    </h2>
                </div>

                <div class="grid grid-cols-1 gap-5 p-5 sm:p-6 md:grid-cols-2">
                    <div>
                        <label for="birth_place" class="block text-sm font-semibold text-slate-700">
                            Tempat Lahir <span class="text-red-500">*</span>
                        </label>

                        <input
                            id="birth_place"
                            name="birth_place"
                            type="text"
                            required
                            value="{{ old('birth_place', $user->birth_place) }}"
                            class="mt-2 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="birth_date" class="block text-sm font-semibold text-slate-700">
                            Tanggal Lahir <span class="text-red-500">*</span>
                        </label>

                        <input
                            id="birth_date"
                            name="birth_date"
                            type="date"
                            required
                            max="{{ now()->subDay()->format('Y-m-d') }}"
                            value="{{ old(
                                'birth_date',
                                optional($user->birth_date)->format('Y-m-d')
                            ) }}"
                            class="mt-2 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="blood_type" class="block text-sm font-semibold text-slate-700">
                            Golongan Darah <span class="text-red-500">*</span>
                        </label>

                        <select
                            id="blood_type"
                            name="blood_type"
                            required
                            class="mt-2 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500">
                            @foreach(['A', 'B', 'AB', 'O'] as $bloodType)
                            <option
                                value="{{ $bloodType }}"
                                @selected(
                                old('blood_type', $user->blood_type)
                                === $bloodType
                                )>
                                {{ $bloodType }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="religion" class="block text-sm font-semibold text-slate-700">
                            Agama <span class="text-red-500">*</span>
                        </label>

                        <select
                            id="religion"
                            name="religion"
                            required
                            class="mt-2 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500">
                            @foreach([
                            'Islam',
                            'Kristen Protestan',
                            'Katolik',
                            'Hindu',
                            'Buddha',
                            'Konghucu',
                            'Kepercayaan',
                            ] as $religion)
                            <option
                                value="{{ $religion }}"
                                @selected(
                                old('religion', $user->religion)
                                === $religion
                                )>
                                {{ $religion }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label for="ktp_address" class="block text-sm font-semibold text-slate-700">
                            Alamat KTP <span class="text-red-500">*</span>
                        </label>

                        <textarea
                            id="ktp_address"
                            name="ktp_address"
                            rows="3"
                            required
                            class="mt-2 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500">{{ old('ktp_address', $user->ktp_address) }}</textarea>
                    </div>

                    <div class="md:col-span-2">
                        <label for="domicile_address" class="block text-sm font-semibold text-slate-700">
                            Alamat Domisili <span class="text-red-500">*</span>
                        </label>

                        <textarea
                            id="domicile_address"
                            name="domicile_address"
                            rows="3"
                            required
                            class="mt-2 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500">{{ old('domicile_address', $user->domicile_address) }}</textarea>

                        <button
                            type="button"
                            id="copy-address"
                            class="mt-2 text-xs font-medium text-blue-600 hover:text-blue-700">
                            Gunakan alamat KTP
                        </button>
                    </div>
                </div>
            </section>


            {{-- SIP --}}
            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 bg-slate-50/70 px-5 py-4 sm:px-6">
                    <h2 class="font-semibold text-slate-800">
                        Surat Izin Praktik (SIP)
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Opsional. Jika Nomor SIP diisi, masa berlaku harus dilengkapi.
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-5 p-5 sm:p-6 md:grid-cols-3">
                    <div class="md:col-span-3">
                        <label for="sip_number" class="block text-sm font-semibold text-slate-700">
                            Nomor SIP
                        </label>

                        <input
                            id="sip_number"
                            name="sip_number"
                            type="text"
                            maxlength="100"
                            value="{{ old('sip_number', $user->sip_number) }}"
                            class="mt-2 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="sip_valid_from" class="block text-sm font-semibold text-slate-700">
                            Berlaku Mulai
                        </label>

                        <input
                            id="sip_valid_from"
                            name="sip_valid_from"
                            type="date"
                            value="{{ old(
                                'sip_valid_from',
                                optional($user->sip_valid_from)->format('Y-m-d')
                            ) }}"
                            class="mt-2 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="sip_valid_until" class="block text-sm font-semibold text-slate-700">
                            Berlaku Sampai
                        </label>

                        <input
                            id="sip_valid_until"
                            name="sip_valid_until"
                            type="date"
                            value="{{ old(
                                'sip_valid_until',
                                optional($user->sip_valid_until)->format('Y-m-d')
                            ) }}"
                            class="mt-2 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>
            </section>


            {{-- PAS FOTO --}}
            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 bg-slate-50/70 px-5 py-4 sm:px-6">
                    <h2 class="font-semibold text-slate-800">
                        Ganti Pas Foto Formal
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Kosongkan jika tidak ingin mengganti foto. Foto aktif baru berubah setelah ACC Admin.
                    </p>
                </div>

                <div class="p-5 sm:p-6">
                    <input
                        id="formal_photo"
                        type="file"
                        name="formal_photo"
                        accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                        class="block w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm file:mr-4 file:rounded-lg file:border-0 file:bg-blue-50 file:px-4 file:py-2 file:text-sm file:font-medium file:text-blue-700 hover:file:bg-blue-100">

                    <p class="mt-2 text-xs text-slate-500">
                        JPG/JPEG/PNG/WEBP, maksimal 2 MB.
                    </p>

                    <div
                        id="photo-preview-wrap"
                        class="mt-4 hidden">
                        <p class="mb-2 text-xs font-medium text-slate-500">
                            Preview Foto Baru
                        </p>

                        <img
                            id="photo-preview"
                            class="h-44 w-36 rounded-xl border border-blue-200 object-cover shadow-sm"
                            alt="Preview pas foto">
                    </div>
                </div>
            </section>


            {{-- REKENING --}}
            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 bg-slate-50/70 px-5 py-4 sm:px-6">
                    <h2 class="font-semibold text-slate-800">
                        Rekening Pegawai
                    </h2>
                </div>

                <div class="grid grid-cols-1 gap-5 p-5 sm:p-6 md:grid-cols-2">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-slate-700">
                            Bank
                        </label>

                        <div class="mt-2 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">
                            🔒 Bank Syariah Indonesia (BSI)
                        </div>
                    </div>

                    <div>
                        <label for="bank_account_number" class="block text-sm font-semibold text-slate-700">
                            Nomor Rekening BSI <span class="text-red-500">*</span>
                        </label>

                        <input
                            id="bank_account_number"
                            name="bank_account_number"
                            type="text"
                            inputmode="numeric"
                            minlength="8"
                            maxlength="20"
                            required
                            value="{{ old(
                                'bank_account_number',
                                $user->bank_account_number
                            ) }}"
                            class="mt-2 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="bank_account_name" class="block text-sm font-semibold text-slate-700">
                            Nama Pemilik Rekening <span class="text-red-500">*</span>
                        </label>

                        <input
                            id="bank_account_name"
                            name="bank_account_name"
                            type="text"
                            maxlength="150"
                            required
                            value="{{ old(
                                'bank_account_name',
                                $user->bank_account_name
                            ) }}"
                            class="mt-2 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>
            </section>


            <div class="rounded-xl border border-blue-200 bg-blue-50 p-4 text-sm leading-6 text-blue-800">
                <strong>Penting:</strong>
                menekan tombol Ajukan Perubahan tidak langsung mengganti data aktif.
                Admin akan memeriksa perubahannya terlebih dahulu.
            </div>

            <div class="flex justify-end">
                <button
                    type="submit"
                    class="rounded-xl bg-blue-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 disabled:cursor-not-allowed disabled:bg-slate-300">
                    {{ $hasPending
                        ? 'Menunggu Persetujuan Admin'
                        : 'Ajukan Perubahan Profil' }}
                </button>
            </div>
        </fieldset>
    </form>
    @endif


    {{-- ============================================================
         PASSWORD — LANGSUNG, TANPA ACC ADMIN
    ============================================================ --}}
    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 bg-slate-50/70 px-5 py-4 sm:px-6">
            <h2 class="font-semibold text-slate-800">
                Ubah Password
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Password adalah data keamanan pribadi dan tidak memerlukan persetujuan Admin.
            </p>
        </div>

        <form
            method="POST"
            action="{{ route('password.update') }}"
            class="grid gap-5 p-5 sm:p-6 md:grid-cols-2">

            @csrf
            @method('PUT')

            <div class="md:col-span-2">
                <label for="update_password_current_password" class="block text-sm font-semibold text-slate-700">
                    Password Saat Ini
                </label>

                <input
                    id="update_password_current_password"
                    name="current_password"
                    type="password"
                    autocomplete="current-password"
                    required
                    class="mt-2 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500">

                @error('current_password', 'updatePassword')
                <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="update_password_password" class="block text-sm font-semibold text-slate-700">
                    Password Baru
                </label>

                <input
                    id="update_password_password"
                    name="password"
                    type="password"
                    autocomplete="new-password"
                    required
                    class="mt-2 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500">

                @error('password', 'updatePassword')
                <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="update_password_password_confirmation" class="block text-sm font-semibold text-slate-700">
                    Konfirmasi Password Baru
                </label>

                <input
                    id="update_password_password_confirmation"
                    name="password_confirmation"
                    type="password"
                    autocomplete="new-password"
                    required
                    class="mt-2 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div class="md:col-span-2 flex items-center justify-end gap-3">
                @if(session('status') === 'password-updated')
                <span class="text-sm font-medium text-emerald-600">
                    Password berhasil diperbarui.
                </span>
                @endif

                <button
                    type="submit"
                    class="rounded-xl bg-slate-800 px-5 py-2.5 text-sm font-semibold text-white hover:bg-slate-900">
                    Ubah Password
                </button>
            </div>
        </form>
    </section>
</div>

@if(! $isAdmin && ! $hasPending)
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const copyButton = document.getElementById('copy-address');
        const ktpAddress = document.getElementById('ktp_address');
        const domicileAddress = document.getElementById('domicile_address');

        copyButton?.addEventListener('click', function() {
            if (!ktpAddress || !domicileAddress) {
                return;
            }

            domicileAddress.value = ktpAddress.value;
            domicileAddress.focus();
        });

        const photoInput = document.getElementById('formal_photo');
        const photoPreviewWrap = document.getElementById('photo-preview-wrap');
        const photoPreview = document.getElementById('photo-preview');

        photoInput?.addEventListener('change', function() {
            const file = this.files?.[0];

            if (!file) {
                photoPreviewWrap?.classList.add('hidden');
                photoPreview?.removeAttribute('src');
                return;
            }

            const reader = new FileReader();

            reader.onload = function(event) {
                if (!photoPreview || !photoPreviewWrap) {
                    return;
                }

                photoPreview.src = event.target.result;
                photoPreviewWrap.classList.remove('hidden');
            };

            reader.readAsDataURL(file);
        });
    });
</script>
@endif

@endsection