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
<style>
    .simi-profile-page {
        width: 100%;
        max-width: 100%;
        min-width: 0;
    }

    .simi-profile-page section,
    .simi-profile-page form,
    .simi-profile-page fieldset,
    .simi-profile-page input,
    .simi-profile-page select,
    .simi-profile-page textarea {
        max-width: 100%;
        min-width: 0;
    }

    .simi-profile-hero {
        display: grid;
        grid-template-columns: 128px minmax(0, 1fr) auto;
        align-items: center;
        gap: 24px;
        padding: 24px;
    }

    .simi-profile-hero-photo,
    .simi-profile-hero-placeholder {
        width: 128px !important;
        height: 128px !important;
        max-width: 128px !important;
        min-width: 128px !important;
        aspect-ratio: 1 / 1;
        border-radius: 16px;
        flex: 0 0 128px;
    }

    .simi-profile-hero-photo {
        display: block;
        object-fit: cover;
        object-position: center;
    }

    .simi-profile-hero-placeholder {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .simi-profile-hero-main {
        min-width: 0;
    }

    .simi-profile-hero-name {
        overflow-wrap: anywhere;
        word-break: break-word;
    }

    .simi-profile-meta {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 8px 24px;
        margin-top: 12px;
    }

    .simi-profile-meta p {
        min-width: 0;
        margin: 0;
        overflow-wrap: anywhere;
        word-break: break-word;
    }

    .simi-profile-status {
        justify-self: end;
        white-space: nowrap;
    }

    .simi-current-profile-photo {
        width: 96px !important;
        height: 96px !important;
        max-width: 96px !important;
        min-width: 96px !important;
        aspect-ratio: 1 / 1;
        object-fit: cover;
        object-position: center;
    }

    .simi-pending-profile-photo {
        width: 144px !important;
        height: 144px !important;
        max-width: 144px !important;
        min-width: 144px !important;
        aspect-ratio: 1 / 1;
        object-fit: cover;
        object-position: center;
    }

    @media (max-width: 960px) {
        .simi-profile-hero {
            grid-template-columns: 112px minmax(0, 1fr);
            gap: 20px;
        }

        .simi-profile-hero-photo,
        .simi-profile-hero-placeholder {
            width: 112px !important;
            height: 112px !important;
            max-width: 112px !important;
            min-width: 112px !important;
            flex-basis: 112px;
        }

        .simi-profile-status {
            grid-column: 2;
            justify-self: start;
        }
    }

    @media (max-width: 640px) {
        .simi-profile-hero {
            grid-template-columns: minmax(0, 1fr);
            align-items: start;
            gap: 16px;
            padding: 20px;
        }

        .simi-profile-hero-photo,
        .simi-profile-hero-placeholder {
            width: 96px !important;
            height: 96px !important;
            max-width: 96px !important;
            min-width: 96px !important;
            flex-basis: 96px;
        }

        .simi-profile-meta {
            grid-template-columns: minmax(0, 1fr);
            gap: 8px;
        }

        .simi-profile-status {
            grid-column: 1;
            justify-self: start;
        }

        .simi-current-profile-photo {
            width: 80px !important;
            height: 80px !important;
            max-width: 80px !important;
            min-width: 80px !important;
        }
    }

    @media (max-width: 420px) {
        .simi-profile-hero {
            padding: 16px;
        }
    }
</style>

<div class="space-y-6 simi-profile-page">


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
            enctype="multipart/form-data"
            class="grid gap-5 p-5 sm:p-6 md:grid-cols-2">

            @csrf
            @method('PATCH')

            <div class="md:col-span-2 space-y-4">
                @if($user->formal_photo_path)
                <div class="flex items-center gap-4 rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <img
                        src="{{ route('profile.photo') }}"
                        alt="Foto profil {{ $user->name }}"
                        class="simi-current-profile-photo shrink-0 rounded-xl border border-slate-200 bg-white shadow-sm">

                    <div>
                        <p class="text-sm font-semibold text-slate-800">
                            Foto Profil Saat Ini
                        </p>
                        <p class="mt-1 text-xs leading-5 text-slate-500">
                            Pilih foto baru di bawah jika ingin menggantinya.
                        </p>
                    </div>
                </div>
                @endif

                <x-profile-photo-cropper
                    input-id="formal_photo"
                    input-name="formal_photo"
                    :required="false"
                    label="{{ $user->formal_photo_path ? 'Ganti Foto Profil' : 'Tambah Foto Profil' }}"
                    help="Geser dan zoom foto sampai bagian yang diinginkan pas di kotak 1:1. Foto hanya berubah setelah tombol Simpan Profil ditekan." />
            </div>

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

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="simi-profile-hero">
            <div>
                @if($user->formal_photo_path)
                <img
                    src="{{ route('profile.photo') }}"
                    alt="Pas foto {{ $user->name }}"
                    class="simi-profile-hero-photo border border-slate-200 bg-white shadow-sm">
                @else
                <div class="simi-profile-hero-placeholder bg-slate-100 text-3xl font-bold text-slate-500">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                @endif
            </div>

            <div class="simi-profile-hero-main">
                <p class="text-sm font-semibold text-blue-600">
                    {{ $user->role === 'kabid' ? 'Kabid' : 'Karyawan' }}
                    •
                    {{ $user->department?->name ?? 'Bidang belum diatur' }}
                </p>

                <h1 class="simi-profile-hero-name mt-1 text-2xl font-bold text-slate-900">
                    {{ $user->name }}
                </h1>

                <div class="simi-profile-meta text-sm text-slate-600">
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

            <div class="simi-profile-status">
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
                    class="simi-pending-profile-photo rounded-xl border border-blue-200 shadow-sm">
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
                    <x-profile-photo-cropper
                        input-id="formal_photo"
                        input-name="formal_photo"
                        :required="false"
                        label="Pilih Foto Baru"
                        help="Kosongkan jika tidak ingin mengganti foto. Jika memilih foto, geser dan zoom sampai wajah pas di kotak 1:1. Foto baru tetap menunggu ACC Admin." />
                </div>
            </section>



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

    });
</script>
@endif

@endsection