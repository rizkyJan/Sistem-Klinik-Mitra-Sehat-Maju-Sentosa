@extends('layouts.admin')

@section('title', 'Edit Admin')
@section('page-title', 'Edit Admin')

@section('content')

<div class="mx-auto max-w-6xl space-y-6">

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Edit Admin</h1>
            <p class="mt-1 text-sm text-slate-500">
                Kelola identitas, biodata, SIP, pas foto formal, rekening BSI, dan akun Administrator.
            </p>
        </div>

        <a
            href="{{ route('admin.admins.index') }}"
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

    <div class="rounded-xl border border-blue-200 bg-blue-50 px-4 py-3 text-sm leading-6 text-blue-800">
        Untuk akun Admin/Super Admin, NIP, NIK KTP, WhatsApp, Bidang, tanggal mulai kerja, biodata, SIP, foto, dan rekening bersifat opsional. Yang wajib hanya nama, email, status akun, serta password saat membuat akun baru.
    </div>

    <form
        action="{{ route('admin.admins.update', $adminUser) }}"
        method="POST"
        enctype="multipart/form-data"
        class="space-y-6">

        @csrf
        @method('PUT')

        <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-5 py-4 sm:px-6">
                <h2 class="font-semibold text-slate-800">Identitas & Kepegawaian</h2>
                <p class="mt-1 text-sm text-slate-500">
                    Nama dan email adalah identitas utama akun. Data kepegawaian di bawah ini bersifat opsional untuk Admin/Super Admin.
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
                        value="{{ old('name', $adminUser->name) }}"
                        required
                        class="w-full rounded-lg border-slate-300">
                    @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="nip" class="mb-2 block text-sm font-medium text-slate-700">
                        NIP / ID Pegawai
                    </label>
                    <input
                        id="nip"
                        type="text"
                        name="nip"
                        value="{{ old('nip', $adminUser->nip ?? $adminUser->nik) }}"
                        maxlength="50"
                        placeholder="Contoh: MSMS001"
                        class="w-full rounded-lg border-slate-300">
                    <p class="mt-1 text-xs text-slate-400">Tetap disinkronkan ke kolom ID lama agar fitur lama tetap kompatibel.</p>
                    @error('nip')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="nik_ktp" class="mb-2 block text-sm font-medium text-slate-700">
                        NIK KTP
                    </label>
                    <input
                        id="nik_ktp"
                        type="text"
                        inputmode="numeric"
                        name="nik_ktp"
                        value="{{ old('nik_ktp', $adminUser->nik_ktp) }}"
                        minlength="16"
                        maxlength="16"
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
                        value="{{ old('email', $adminUser->email) }}"
                        required
                        class="w-full rounded-lg border-slate-300">
                    @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="whatsapp" class="mb-2 block text-sm font-medium text-slate-700">
                        WhatsApp
                    </label>
                    <input
                        id="whatsapp"
                        type="text"
                        name="whatsapp"
                        value="{{ old('whatsapp', $adminUser->whatsapp) }}"
                        placeholder="Contoh: 081234567890"
                        class="w-full rounded-lg border-slate-300">
                    @error('whatsapp')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="department_id" class="mb-2 block text-sm font-medium text-slate-700">
                        Bidang
                    </label>
                    <select
                        id="department_id"
                        name="department_id"
                        class="w-full rounded-lg border-slate-300">
                        <option value="">-- Pilih Bidang --</option>
                        @foreach($departments as $department)
                        <option
                            value="{{ $department->id }}"
                            @selected((string) old('department_id', $adminUser->department_id) === (string) $department->id)>
                            {{ $department->name }}
                            @if(! $department->is_active) - Nonaktif @endif
                        </option>
                        @endforeach
                    </select>
                    @error('department_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="join_date" class="mb-2 block text-sm font-medium text-slate-700">
                        Tanggal Mulai Kerja
                    </label>
                    <input
                        id="join_date"
                        type="date"
                        name="join_date"
                        max="{{ now()->format('Y-m-d') }}"
                        value="{{ old('join_date', $adminUser->join_date?->format('Y-m-d')) }}"
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
                        <option value="1" @selected((string) old('is_active', $adminUser->is_active ? '1' : '0') === '1')>Aktif</option>
                        <option value="0" @selected((string) old('is_active', $adminUser->is_active ? '1' : '0') === '0')>Nonaktif</option>
                    </select>
                </div>
            </div>
        </section>


        <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-5 py-4 sm:px-6">
                <h2 class="font-semibold text-slate-800">Biodata Pribadi</h2>
                <p class="mt-1 text-sm text-slate-500">
                    Opsional. Isi hanya jika data pribadi Administrator memang perlu dicatat.
                </p>
            </div>

            <div class="grid grid-cols-1 gap-5 p-5 sm:p-6 md:grid-cols-2">
                <div>
                    <label for="birth_place" class="mb-2 block text-sm font-medium text-slate-700">
                        Tempat Lahir
                    </label>
                    <input
                        id="birth_place"
                        type="text"
                        name="birth_place"
                        value="{{ old('birth_place', $adminUser->birth_place) }}"
                        class="w-full rounded-lg border-slate-300">
                    @error('birth_place')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="birth_date" class="mb-2 block text-sm font-medium text-slate-700">
                        Tanggal Lahir
                    </label>
                    <input
                        id="birth_date"
                        type="date"
                        name="birth_date"
                        max="{{ now()->subDay()->format('Y-m-d') }}"
                        value="{{ old('birth_date', $adminUser->birth_date?->format('Y-m-d')) }}"
                        class="w-full rounded-lg border-slate-300">
                    @error('birth_date')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="blood_type" class="mb-2 block text-sm font-medium text-slate-700">
                        Golongan Darah
                    </label>
                    <select
                        id="blood_type"
                        name="blood_type"
                        class="w-full rounded-lg border-slate-300">
                        <option value="">-- Pilih --</option>
                        @foreach(['A', 'B', 'AB', 'O'] as $bloodType)
                        <option value="{{ $bloodType }}" @selected(old('blood_type', $adminUser->blood_type) === $bloodType)>
                            {{ $bloodType }}
                        </option>
                        @endforeach
                    </select>
                    @error('blood_type')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="religion" class="mb-2 block text-sm font-medium text-slate-700">
                        Agama
                    </label>
                    <select
                        id="religion"
                        name="religion"
                        class="w-full rounded-lg border-slate-300">
                        <option value="">-- Pilih Agama --</option>
                        @foreach(['Islam', 'Kristen Protestan', 'Katolik', 'Hindu', 'Buddha', 'Konghucu', 'Kepercayaan'] as $religion)
                        <option value="{{ $religion }}" @selected(old('religion', $adminUser->religion) === $religion)>
                            {{ $religion }}
                        </option>
                        @endforeach
                    </select>
                    @error('religion')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="md:col-span-2">
                    <label for="ktp_address" class="mb-2 block text-sm font-medium text-slate-700">
                        Alamat KTP
                    </label>
                    <textarea
                        id="ktp_address"
                        name="ktp_address"
                        rows="3"
                        class="w-full rounded-lg border-slate-300"
                        placeholder="Alamat lengkap sesuai KTP">{{ old('ktp_address', $adminUser->ktp_address) }}</textarea>
                    @error('ktp_address')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="md:col-span-2">
                    <div class="mb-2 flex flex-wrap items-center justify-between gap-2">
                        <label for="domicile_address" class="block text-sm font-medium text-slate-700">
                            Alamat Domisili
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
                        class="w-full rounded-lg border-slate-300"
                        placeholder="Alamat tempat tinggal saat ini">{{ old('domicile_address', $adminUser->domicile_address) }}</textarea>
                    @error('domicile_address')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
        </section>
        <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-5 py-4 sm:px-6">
                <h2 class="font-semibold text-slate-800">Surat Izin Praktik (SIP)</h2>
                <p class="mt-1 text-sm text-slate-500">
                    Opsional. Isi hanya jika Administrator mempunyai SIP.
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
                        value="{{ old('sip_number', $adminUser->sip_number) }}"
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
                        value="{{ old('sip_valid_from', $adminUser->sip_valid_from?->format('Y-m-d')) }}"
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
                        value="{{ old('sip_valid_until', $adminUser->sip_valid_until?->format('Y-m-d')) }}"
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
                    Pas foto disimpan private. Rekening Administrator wajib Bank Syariah Indonesia (BSI).
                </p>
            </div>

            <div class="grid grid-cols-1 gap-6 p-5 sm:p-6 lg:grid-cols-2">
                <div>
                    <h3 class="mb-3 text-sm font-semibold text-slate-700">Pas Foto Formal (Opsional)</h3>

                    @if($adminUser->formal_photo_path)
                    <div class="mb-4 flex items-center gap-4 rounded-xl border border-slate-200 bg-slate-50 p-4">
                        <img
                            src="{{ route('admin.admins.photo', $adminUser) }}"
                            alt="Pas foto {{ $adminUser->name }}"
                            class="h-24 w-24 rounded-xl border border-slate-200 bg-white object-cover">
                        <div>
                            <p class="text-sm font-semibold text-slate-700">Pas foto saat ini</p>
                            <p class="mt-1 text-xs text-slate-500">
                                Upload foto baru hanya jika ingin mengganti foto yang tersimpan.
                            </p>
                            <a
                                href="{{ route('admin.admins.photo', $adminUser) }}"
                                target="_blank"
                                class="mt-2 inline-flex text-xs font-semibold text-blue-600 hover:text-blue-700">
                                Lihat foto penuh
                            </a>
                        </div>
                    </div>
                    @else
                    <div class="mb-4 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-700">
                        Data lama belum memiliki pas foto. Pas foto wajib diunggah pada saat menyimpan perubahan ini.
                    </div>
                    @endif


                    <x-profile-photo-cropper
                        input-id="formal_photo"
                        input-name="formal_photo"
                        :required="false"
                        label="{{ $adminUser->formal_photo_path ? 'Ganti Pas Foto' : 'Upload Pas Foto' }}"
                        help="Pilih foto baru bila ingin mengganti. Geser dan zoom sampai bagian yang diinginkan pas di kotak 1:1." />
                </div>

                <div>
                    <h3 class="mb-3 text-sm font-semibold text-slate-700">Rekening Fee/Reimburse (Opsional)</h3>

                    <div class="space-y-4">
                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-700">Bank</label>
                            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3">
                                <p class="font-semibold text-emerald-800">Bank Syariah Indonesia (BSI)</p>
                                <p class="mt-1 text-xs text-emerald-600">Jika rekening diisi, bank menggunakan Bank Syariah Indonesia (BSI).</p>
                            </div>
                        </div>

                        <div>
                            <label for="bank_account_number" class="mb-2 block text-sm font-medium text-slate-700">
                                Nomor Rekening BSI
                            </label>
                            <input
                                id="bank_account_number"
                                type="text"
                                inputmode="numeric"
                                name="bank_account_number"
                                value="{{ old('bank_account_number', $adminUser->bank_account_number) }}"
                                minlength="8"
                                maxlength="20"
                                placeholder="Nomor rekening BSI"
                                class="w-full rounded-lg border-slate-300">
                            @error('bank_account_number')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="bank_account_name" class="mb-2 block text-sm font-medium text-slate-700">
                                Nama Pemilik Rekening
                            </label>
                            <input
                                id="bank_account_name"
                                type="text"
                                name="bank_account_name"
                                value="{{ old('bank_account_name', $adminUser->bank_account_name) }}"
                                maxlength="150"
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
                <p class="mt-1 text-sm text-slate-500">Kosongkan jika password tidak ingin diganti.</p>
            </div>

            <div class="grid grid-cols-1 gap-5 p-5 sm:p-6 md:grid-cols-2">
                <div>
                    <label for="password" class="mb-2 block text-sm font-medium text-slate-700">
                        Password
                    </label>
                    <input
                        id="password"
                        type="password"
                        name="password"

                        autocomplete="new-password"
                        class="w-full rounded-lg border-slate-300">
                    @error('password')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="password_confirmation" class="mb-2 block text-sm font-medium text-slate-700">
                        Konfirmasi Password
                    </label>
                    <input
                        id="password_confirmation"
                        type="password"
                        name="password_confirmation"

                        autocomplete="new-password"
                        class="w-full rounded-lg border-slate-300">
                </div>
            </div>
        </section>

        <div class="sticky bottom-0 z-10 flex flex-col-reverse gap-3 rounded-xl border border-slate-200 bg-white/95 p-4 shadow-lg backdrop-blur sm:flex-row sm:justify-end">
            <a
                href="{{ route('admin.admins.index') }}"
                class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-5 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50">
                Batal
            </a>
            <button
                type="submit"
                class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">
                Simpan Perubahan
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