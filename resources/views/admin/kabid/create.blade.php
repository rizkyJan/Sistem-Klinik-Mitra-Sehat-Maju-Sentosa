@extends('layouts.admin')

@section('title', 'Tambah Kabid')
@section('page-title', 'Tambah Kabid')

@section('content')

<div class="mx-auto max-w-6xl space-y-6">

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Tambah Kabid</h1>
            <p class="mt-1 text-sm text-slate-500">
                Lengkapi identitas, biodata, SIP, pas foto formal, dan rekening BSI pegawai.
            </p>
        </div>

        <a
            href="{{ route('admin.kabid.index') }}"
            class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50">
            ← Kembali
        </a>
    </div>

    @if($errors->any())
    <div class="rounded-xl border border-red-200 bg-red-50 p-4">
        <p class="font-medium text-red-700">Terdapat data yang belum benar:</p>
        <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-red-600">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form
        action="{{ route('admin.kabid.store') }}"
        method="POST"
        enctype="multipart/form-data"
        class="space-y-6">

        @csrf


        <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-5 py-4 sm:px-6">
                <h2 class="font-semibold text-slate-800">Identitas & Kepegawaian</h2>
                <p class="mt-1 text-sm text-slate-500">
                    NIP adalah ID internal pegawai. NIK KTP adalah nomor identitas resmi 16 digit.
                </p>
            </div>

            <div class="grid grid-cols-1 gap-5 p-5 sm:p-6 md:grid-cols-2">
                <div>
                    <label for="name" class="mb-2 block text-sm font-medium text-slate-700">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input
                        id="name"
                        type="text"
                        name="name"
                        value="{{ old('name') }}"
                        required
                        class="w-full rounded-lg border-slate-300">
                    @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="nip" class="mb-2 block text-sm font-medium text-slate-700">
                        NIP / ID Pegawai <span class="text-red-500">*</span>
                    </label>
                    <input
                        id="nip"
                        type="text"
                        name="nip"
                        value="{{ old('nip') }}"
                        maxlength="50"
                        required
                        placeholder="Contoh: MSMS001"
                        class="w-full rounded-lg border-slate-300">
                    <p class="mt-1 text-xs text-slate-400">Menggantikan istilah NIK / ID Karyawan lama.</p>
                    @error('nip')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="nik_ktp" class="mb-2 block text-sm font-medium text-slate-700">
                        NIK KTP <span class="text-red-500">*</span>
                    </label>
                    <input
                        id="nik_ktp"
                        type="text"
                        inputmode="numeric"
                        name="nik_ktp"
                        value="{{ old('nik_ktp') }}"
                        minlength="16"
                        maxlength="16"
                        required
                        placeholder="16 digit NIK KTP"
                        class="w-full rounded-lg border-slate-300">
                    @error('nik_ktp')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="email" class="mb-2 block text-sm font-medium text-slate-700">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        class="w-full rounded-lg border-slate-300">
                    @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="whatsapp" class="mb-2 block text-sm font-medium text-slate-700">
                        WhatsApp <span class="text-red-500">*</span>
                    </label>
                    <input
                        id="whatsapp"
                        type="text"
                        name="whatsapp"
                        value="{{ old('whatsapp') }}"
                        required
                        placeholder="Contoh: 081234567890"
                        class="w-full rounded-lg border-slate-300">
                    @error('whatsapp')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="department_id" class="mb-2 block text-sm font-medium text-slate-700">
                        Bidang <span class="text-red-500">*</span>
                    </label>
                    <select
                        id="department_id"
                        name="department_id"
                        required
                        class="w-full rounded-lg border-slate-300">
                        <option value="">-- Pilih Bidang --</option>
                        @foreach($departments as $department)
                        <option
                            value="{{ $department->id }}"
                            @selected((string) old('department_id', '' )===(string) $department->id)>
                            {{ $department->name }}
                            @if(! $department->is_active) - Nonaktif @endif
                        </option>
                        @endforeach
                    </select>
                    @error('department_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="join_date" class="mb-2 block text-sm font-medium text-slate-700">
                        Tanggal Mulai Kerja <span class="text-red-500">*</span>
                    </label>
                    <input
                        id="join_date"
                        type="date"
                        name="join_date"
                        max="{{ now()->format('Y-m-d') }}"
                        value="{{ old('join_date', null) }}"
                        required
                        class="w-full rounded-lg border-slate-300">
                    @error('join_date')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="is_active" class="mb-2 block text-sm font-medium text-slate-700">
                        Status Akun <span class="text-red-500">*</span>
                    </label>
                    <select
                        id="is_active"
                        name="is_active"
                        required
                        class="w-full rounded-lg border-slate-300">
                        <option value="1" @selected((string) old('is_active', '1' )==='1' )>Aktif</option>
                        <option value="0" @selected((string) old('is_active', '1' )==='0' )>Nonaktif</option>
                    </select>
                </div>
            </div>
        </section>

        <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-5 py-4 sm:px-6">
                <h2 class="font-semibold text-slate-800">Biodata Pribadi</h2>
                <p class="mt-1 text-sm text-slate-500">
                    Data administratif sesuai identitas pegawai.
                </p>
            </div>

            <div class="grid grid-cols-1 gap-5 p-5 sm:p-6 md:grid-cols-2">
                <div>
                    <label for="birth_place" class="mb-2 block text-sm font-medium text-slate-700">
                        Tempat Lahir <span class="text-red-500">*</span>
                    </label>
                    <input
                        id="birth_place"
                        type="text"
                        name="birth_place"
                        value="{{ old('birth_place') }}"
                        required
                        class="w-full rounded-lg border-slate-300">
                    @error('birth_place')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="birth_date" class="mb-2 block text-sm font-medium text-slate-700">
                        Tanggal Lahir <span class="text-red-500">*</span>
                    </label>
                    <input
                        id="birth_date"
                        type="date"
                        name="birth_date"
                        max="{{ now()->subDay()->format('Y-m-d') }}"
                        value="{{ old('birth_date', null) }}"
                        required
                        class="w-full rounded-lg border-slate-300">
                    @error('birth_date')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="blood_type" class="mb-2 block text-sm font-medium text-slate-700">
                        Golongan Darah <span class="text-red-500">*</span>
                    </label>
                    <select
                        id="blood_type"
                        name="blood_type"
                        required
                        class="w-full rounded-lg border-slate-300">
                        <option value="">-- Pilih --</option>
                        @foreach(['A', 'B', 'AB', 'O'] as $bloodType)
                        <option value="{{ $bloodType }}" @selected(old('blood_type', '' )===$bloodType)>
                            {{ $bloodType }}
                        </option>
                        @endforeach
                    </select>
                    @error('blood_type')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="religion" class="mb-2 block text-sm font-medium text-slate-700">
                        Agama <span class="text-red-500">*</span>
                    </label>
                    <select
                        id="religion"
                        name="religion"
                        required
                        class="w-full rounded-lg border-slate-300">
                        <option value="">-- Pilih Agama --</option>
                        @foreach(['Islam', 'Kristen Protestan', 'Katolik', 'Hindu', 'Buddha', 'Konghucu', 'Kepercayaan'] as $religion)
                        <option value="{{ $religion }}" @selected(old('religion', '' )===$religion)>
                            {{ $religion }}
                        </option>
                        @endforeach
                    </select>
                    @error('religion')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="md:col-span-2">
                    <label for="ktp_address" class="mb-2 block text-sm font-medium text-slate-700">
                        Alamat KTP <span class="text-red-500">*</span>
                    </label>
                    <textarea
                        id="ktp_address"
                        name="ktp_address"
                        rows="3"
                        required
                        class="w-full rounded-lg border-slate-300"
                        placeholder="Alamat lengkap sesuai KTP">{{ old('ktp_address') }}</textarea>
                    @error('ktp_address')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="md:col-span-2">
                    <div class="mb-2 flex flex-wrap items-center justify-between gap-2">
                        <label for="domicile_address" class="block text-sm font-medium text-slate-700">
                            Alamat Domisili <span class="text-red-500">*</span>
                        </label>
                        <button
                            type="button"
                            id="copyKtpAddress"
                            class="text-xs font-semibold text-blue-600 hover:text-blue-700">
                            Sama dengan alamat KTP
                        </button>
                    </div>
                    <textarea
                        id="domicile_address"
                        name="domicile_address"
                        rows="3"
                        required
                        class="w-full rounded-lg border-slate-300"
                        placeholder="Alamat tempat tinggal saat ini">{{ old('domicile_address') }}</textarea>
                    @error('domicile_address')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
        </section>

        <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-5 py-4 sm:px-6">
                <h2 class="font-semibold text-slate-800">Surat Izin Praktik (SIP)</h2>
                <p class="mt-1 text-sm text-slate-500">
                    Opsional. Isi bagian ini hanya jika pegawai mempunyai SIP.
                </p>
            </div>

            <div class="grid grid-cols-1 gap-5 p-5 sm:p-6 md:grid-cols-3">
                <div class="md:col-span-3">
                    <label for="sip_number" class="mb-2 block text-sm font-medium text-slate-700">
                        Nomor SIP
                    </label>
                    <input
                        id="sip_number"
                        type="text"
                        name="sip_number"
                        value="{{ old('sip_number') }}"
                        maxlength="100"
                        placeholder="Contoh: 503/123/SIP/2026"
                        class="w-full rounded-lg border-slate-300">
                    @error('sip_number')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="sip_valid_from" class="mb-2 block text-sm font-medium text-slate-700">
                        Berlaku Mulai
                    </label>
                    <input
                        id="sip_valid_from"
                        type="date"
                        name="sip_valid_from"
                        value="{{ old('sip_valid_from', null) }}"
                        class="w-full rounded-lg border-slate-300">
                    @error('sip_valid_from')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="sip_valid_until" class="mb-2 block text-sm font-medium text-slate-700">
                        Berlaku Sampai
                    </label>
                    <input
                        id="sip_valid_until"
                        type="date"
                        name="sip_valid_until"
                        value="{{ old('sip_valid_until', null) }}"
                        class="w-full rounded-lg border-slate-300">
                    @error('sip_valid_until')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="rounded-lg border border-blue-100 bg-blue-50 p-3 text-sm text-blue-700">
                    Jika Nomor SIP diisi, tanggal mulai dan tanggal berakhir wajib diisi.
                </div>
            </div>
        </section>

        <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-5 py-4 sm:px-6">
                <h2 class="font-semibold text-slate-800">Pas Foto & Rekening</h2>
                <p class="mt-1 text-sm text-slate-500">
                    Pas foto disimpan private. Rekening pegawai wajib Bank Syariah Indonesia (BSI).
                </p>
            </div>

            <div class="grid grid-cols-1 gap-6 p-5 sm:p-6 lg:grid-cols-2">
                <div>
                    <h3 class="mb-3 text-sm font-semibold text-slate-700">Pas Foto Formal</h3>


                    <label for="formal_photo" class="mb-2 block text-sm font-medium text-slate-700">
                        Upload Pas Foto <span class="text-red-500">*</span>
                    </label>
                    <input
                        id="formal_photo"
                        type="file"
                        name="formal_photo"
                        accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                        required
                        class="block w-full rounded-lg border border-slate-300 bg-white text-sm text-slate-600 file:mr-4 file:border-0 file:bg-slate-100 file:px-4 file:py-2.5 file:text-sm file:font-medium file:text-slate-700 hover:file:bg-slate-200">
                    <p class="mt-2 text-xs text-slate-400">JPG/JPEG/PNG/WEBP, maksimal 2 MB.</p>
                    @error('formal_photo')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror

                    <div id="newPhotoPreviewWrap" class="mt-4 hidden">
                        <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-400">Preview foto baru</p>
                        <img id="newPhotoPreview" alt="Preview pas foto" class="h-32 w-28 rounded-lg border border-slate-200 object-cover">
                    </div>
                </div>

                <div>
                    <h3 class="mb-3 text-sm font-semibold text-slate-700">Rekening Fee/Reimburse</h3>

                    <div class="space-y-4">
                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-700">Bank</label>
                            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3">
                                <p class="font-semibold text-emerald-800">Bank Syariah Indonesia (BSI)</p>
                                <p class="mt-1 text-xs text-emerald-600">Bank dikunci oleh sistem dan tidak dapat dipilih.</p>
                            </div>
                        </div>

                        <div>
                            <label for="bank_account_number" class="mb-2 block text-sm font-medium text-slate-700">
                                Nomor Rekening BSI <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="bank_account_number"
                                type="text"
                                inputmode="numeric"
                                name="bank_account_number"
                                value="{{ old('bank_account_number') }}"
                                minlength="8"
                                maxlength="20"
                                required
                                placeholder="Nomor rekening BSI"
                                class="w-full rounded-lg border-slate-300">
                            @error('bank_account_number')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="bank_account_name" class="mb-2 block text-sm font-medium text-slate-700">
                                Nama Pemilik Rekening <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="bank_account_name"
                                type="text"
                                name="bank_account_name"
                                value="{{ old('bank_account_name') }}"
                                maxlength="150"
                                required
                                placeholder="Sesuai nama pada rekening BSI"
                                class="w-full rounded-lg border-slate-300">
                            @error('bank_account_name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-5 py-4 sm:px-6">
                <h2 class="font-semibold text-slate-800">Keamanan Akun</h2>
                <p class="mt-1 text-sm text-slate-500">Minimal 8 karakter.</p>
            </div>

            <div class="grid grid-cols-1 gap-5 p-5 sm:p-6 md:grid-cols-2">
                <div>
                    <label for="password" class="mb-2 block text-sm font-medium text-slate-700">
                        Password <span class="text-red-500">*</span>
                    </label>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        required
                        autocomplete="new-password"
                        class="w-full rounded-lg border-slate-300">
                    @error('password')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="password_confirmation" class="mb-2 block text-sm font-medium text-slate-700">
                        Konfirmasi Password <span class="text-red-500">*</span>
                    </label>
                    <input
                        id="password_confirmation"
                        type="password"
                        name="password_confirmation"
                        required
                        autocomplete="new-password"
                        class="w-full rounded-lg border-slate-300">
                </div>
            </div>
        </section>

        <div class="sticky bottom-0 z-10 flex flex-col-reverse gap-3 rounded-xl border border-slate-200 bg-white/95 p-4 shadow-lg backdrop-blur sm:flex-row sm:justify-end">
            <a
                href="{{ route('admin.kabid.index') }}"
                class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-5 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50">
                Batal
            </a>
            <button
                type="submit"
                class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">
                Simpan Kabid
            </button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const copyButton = document.getElementById('copyKtpAddress');
        const ktpAddress = document.getElementById('ktp_address');
        const domicileAddress = document.getElementById('domicile_address');

        copyButton?.addEventListener('click', function() {
            domicileAddress.value = ktpAddress.value;
            domicileAddress.focus();
        });

        const photoInput = document.getElementById('formal_photo');
        const previewWrap = document.getElementById('newPhotoPreviewWrap');
        const preview = document.getElementById('newPhotoPreview');

        photoInput?.addEventListener('change', function() {
            const file = this.files?.[0];

            if (!file) {
                previewWrap.classList.add('hidden');
                preview.removeAttribute('src');
                return;
            }

            preview.src = URL.createObjectURL(file);
            previewWrap.classList.remove('hidden');
        });

        const digitsOnly = function(input, maxLength) {
            input?.addEventListener('input', function() {
                this.value = this.value.replace(/\D/g, '').slice(0, maxLength);
            });
        };

        digitsOnly(document.getElementById('nik_ktp'), 16);
        digitsOnly(document.getElementById('bank_account_number'), 20);
    });
</script>

@endsection