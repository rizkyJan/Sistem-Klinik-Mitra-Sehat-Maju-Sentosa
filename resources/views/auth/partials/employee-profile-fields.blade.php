@php
$profileUser = $user ?? null;

$selectedRole = old(
'role',
$profileUser?->role === 'kabid'
? 'kabid'
: 'karyawan'
);

$selectedDepartment = old(
'department_id',
$profileUser?->department_id
);

$hasExistingPhoto = filled(
$profileUser?->formal_photo_path
);
@endphp

{{-- ============================================================
     IDENTITAS & KEPEGAWAIAN
============================================================ --}}
<section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div class="border-b border-slate-200 bg-slate-50/70 px-5 py-4 sm:px-6">
        <h2 class="font-semibold text-slate-800">
            Identitas & Kepegawaian
        </h2>
        <p class="mt-1 text-sm text-slate-500">
            NIP adalah ID internal pegawai. NIK KTP adalah nomor identitas resmi 16 digit.
        </p>
    </div>

    <div class="grid grid-cols-1 gap-5 p-5 sm:p-6 md:grid-cols-2">
        <div class="md:col-span-2">
            <label for="name" class="block text-sm font-semibold text-slate-700">
                Nama Lengkap <span class="text-red-500">*</span>
            </label>

            <input
                id="name"
                type="text"
                name="name"
                value="{{ old('name', $profileUser?->name) }}"
                required
                autocomplete="name"
                class="mt-2 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500">

            @error('name')
            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="nip" class="block text-sm font-semibold text-slate-700">
                NIP / ID Pegawai <span class="text-red-500">*</span>
            </label>

            <input
                id="nip"
                type="text"
                name="nip"
                value="{{ old('nip', $profileUser?->nip ?? $profileUser?->nik) }}"
                maxlength="50"
                required
                placeholder="Contoh: MSMS001"
                class="mt-2 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500">

            @error('nip')
            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="nik_ktp" class="block text-sm font-semibold text-slate-700">
                NIK KTP <span class="text-red-500">*</span>
            </label>

            <input
                id="nik_ktp"
                type="text"
                inputmode="numeric"
                name="nik_ktp"
                value="{{ old('nik_ktp', $profileUser?->nik_ktp) }}"
                minlength="16"
                maxlength="16"
                required
                placeholder="16 digit NIK KTP"
                class="mt-2 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500">

            @error('nik_ktp')
            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="whatsapp" class="block text-sm font-semibold text-slate-700">
                WhatsApp <span class="text-red-500">*</span>
            </label>

            <input
                id="whatsapp"
                type="text"
                name="whatsapp"
                value="{{ old('whatsapp', $profileUser?->whatsapp) }}"
                required
                placeholder="Contoh: 081234567890"
                class="mt-2 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500">

            @error('whatsapp')
            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="join_date" class="block text-sm font-semibold text-slate-700">
                Tanggal Mulai Kerja <span class="text-red-500">*</span>
            </label>

            <input
                id="join_date"
                type="date"
                name="join_date"
                max="{{ now()->format('Y-m-d') }}"
                value="{{ old('join_date', optional($profileUser?->join_date)->format('Y-m-d')) }}"
                required
                class="mt-2 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500">

            @error('join_date')
            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
            @enderror
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
                <option value="">-- Pilih Bidang --</option>

                @foreach($departments as $department)
                <option
                    value="{{ $department->id }}"
                    data-has-kabid="{{ in_array((int) $department->id, $kabidDepartmentIds ?? [], true) ? '1' : '0' }}"
                    @selected((string) $selectedDepartment===(string) $department->id)>
                    {{ $department->name }}
                    @if(in_array((int) $department->id, $kabidDepartmentIds ?? [], true))
                    — sudah memiliki Kabid
                    @endif
                </option>
                @endforeach
            </select>

            @error('department_id')
            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="role" class="block text-sm font-semibold text-slate-700">
                Jabatan / Role <span class="text-red-500">*</span>
            </label>

            <select
                id="role"
                name="role"
                required
                class="mt-2 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="karyawan" @selected($selectedRole==='karyawan' )>
                    Karyawan
                </option>
                <option value="kabid" @selected($selectedRole==='kabid' )>
                    Kabid
                </option>
            </select>

            <p id="kabid-warning" class="mt-2 hidden rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-xs text-amber-700">
                Bidang yang dipilih sudah memiliki Kabid yang disetujui. Anda tidak dapat mendaftar sebagai Kabid pada bidang ini.
            </p>

            @error('role')
            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>
</section>

{{-- ============================================================
     BIODATA PRIBADI
============================================================ --}}
<section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div class="border-b border-slate-200 bg-slate-50/70 px-5 py-4 sm:px-6">
        <h2 class="font-semibold text-slate-800">
            Biodata Pribadi
        </h2>
        <p class="mt-1 text-sm text-slate-500">
            Isi sesuai identitas dan data administratif pegawai.
        </p>
    </div>

    <div class="grid grid-cols-1 gap-5 p-5 sm:p-6 md:grid-cols-2">
        <div>
            <label for="birth_place" class="block text-sm font-semibold text-slate-700">
                Tempat Lahir <span class="text-red-500">*</span>
            </label>

            <input
                id="birth_place"
                type="text"
                name="birth_place"
                value="{{ old('birth_place', $profileUser?->birth_place) }}"
                required
                class="mt-2 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500">

            @error('birth_place')
            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="birth_date" class="block text-sm font-semibold text-slate-700">
                Tanggal Lahir <span class="text-red-500">*</span>
            </label>

            <input
                id="birth_date"
                type="date"
                name="birth_date"
                max="{{ now()->subDay()->format('Y-m-d') }}"
                value="{{ old('birth_date', optional($profileUser?->birth_date)->format('Y-m-d')) }}"
                required
                class="mt-2 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500">

            @error('birth_date')
            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
            @enderror
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
                <option value="">-- Pilih --</option>
                @foreach(['A', 'B', 'AB', 'O'] as $bloodType)
                <option
                    value="{{ $bloodType }}"
                    @selected(old('blood_type', $profileUser?->blood_type) === $bloodType)>
                    {{ $bloodType }}
                </option>
                @endforeach
            </select>

            @error('blood_type')
            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
            @enderror
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
                <option value="">-- Pilih Agama --</option>

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
                    @selected(old('religion', $profileUser?->religion) === $religion)>
                    {{ $religion }}
                </option>
                @endforeach
            </select>

            @error('religion')
            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
            @enderror
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
                class="mt-2 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500"
                placeholder="Alamat lengkap sesuai KTP">{{ old('ktp_address', $profileUser?->ktp_address) }}</textarea>

            @error('ktp_address')
            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
            @enderror
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
                class="mt-2 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500"
                placeholder="Alamat tempat tinggal saat ini">{{ old('domicile_address', $profileUser?->domicile_address) }}</textarea>

            <button
                type="button"
                id="copy-ktp-address"
                class="mt-2 text-xs font-medium text-blue-600 hover:text-blue-700">
                Gunakan alamat KTP sebagai alamat domisili
            </button>

            @error('domicile_address')
            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>
</section>

{{-- ============================================================
     SIP
============================================================ --}}
<section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div class="border-b border-slate-200 bg-slate-50/70 px-5 py-4 sm:px-6">
        <h2 class="font-semibold text-slate-800">
            Surat Izin Praktik (SIP)
        </h2>
        <p class="mt-1 text-sm text-slate-500">
            Opsional. Isi jika pegawai memiliki SIP.
        </p>
    </div>

    <div class="grid grid-cols-1 gap-5 p-5 sm:p-6 md:grid-cols-3">
        <div class="md:col-span-3">
            <label for="sip_number" class="block text-sm font-semibold text-slate-700">
                Nomor SIP
            </label>

            <input
                id="sip_number"
                type="text"
                name="sip_number"
                value="{{ old('sip_number', $profileUser?->sip_number) }}"
                maxlength="100"
                placeholder="Kosongkan jika tidak memiliki SIP"
                class="mt-2 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500">

            @error('sip_number')
            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="sip_valid_from" class="block text-sm font-semibold text-slate-700">
                Berlaku Mulai
            </label>

            <input
                id="sip_valid_from"
                type="date"
                name="sip_valid_from"
                value="{{ old('sip_valid_from', optional($profileUser?->sip_valid_from)->format('Y-m-d')) }}"
                class="mt-2 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500">

            @error('sip_valid_from')
            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="sip_valid_until" class="block text-sm font-semibold text-slate-700">
                Berlaku Sampai
            </label>

            <input
                id="sip_valid_until"
                type="date"
                name="sip_valid_until"
                value="{{ old('sip_valid_until', optional($profileUser?->sip_valid_until)->format('Y-m-d')) }}"
                class="mt-2 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500">

            @error('sip_valid_until')
            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-end">
            <p class="rounded-xl bg-blue-50 px-4 py-3 text-xs leading-5 text-blue-700">
                Jika Nomor SIP diisi, tanggal mulai dan berakhir wajib dilengkapi.
            </p>
        </div>
    </div>
</section>

{{-- ============================================================
     PAS FOTO
============================================================ --}}
<section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div class="border-b border-slate-200 bg-slate-50/70 px-5 py-4 sm:px-6">
        <h2 class="font-semibold text-slate-800">
            Pas Foto Formal
        </h2>
        <p class="mt-1 text-sm text-slate-500">
            JPG/JPEG/PNG/WEBP, maksimal 2 MB. File disimpan secara private.
        </p>
    </div>

    <div class="p-5 sm:p-6">
        @if($hasExistingPhoto)
        <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
            Pas foto sebelumnya sudah tersimpan.
            Jika sedang memperbaiki data yang ditolak Admin, upload foto baru hanya jika ingin menggantinya.
        </div>
        @endif

        <label for="formal_photo" class="block text-sm font-semibold text-slate-700">
            Pas Foto Formal
            @if(! $hasExistingPhoto)
            <span class="text-red-500">*</span>
            @endif
        </label>

        <input
            id="formal_photo"
            type="file"
            name="formal_photo"
            accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
            @required(! $hasExistingPhoto)
            class="mt-2 block w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm file:mr-4 file:rounded-lg file:border-0 file:bg-blue-50 file:px-4 file:py-2 file:text-sm file:font-medium file:text-blue-700 hover:file:bg-blue-100">

        <div id="photo-preview-wrap" class="mt-4 hidden">
            <img
                id="photo-preview"
                alt="Preview pas foto"
                class="h-44 w-36 rounded-xl border border-slate-200 object-cover shadow-sm">
        </div>

        @error('formal_photo')
        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>
</section>

{{-- ============================================================
     REKENING BSI
============================================================ --}}
<section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div class="border-b border-slate-200 bg-slate-50/70 px-5 py-4 sm:px-6">
        <h2 class="font-semibold text-slate-800">
            Rekening Pegawai
        </h2>
        <p class="mt-1 text-sm text-slate-500">
            Rekening pegawai wajib menggunakan Bank Syariah Indonesia (BSI).
        </p>
    </div>

    <div class="grid grid-cols-1 gap-5 p-5 sm:p-6 md:grid-cols-2">
        <div class="md:col-span-2">
            <label class="block text-sm font-semibold text-slate-700">
                Bank
            </label>

            <div class="mt-2 flex items-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">
                <span>🔒</span>
                <span>Bank Syariah Indonesia (BSI)</span>
            </div>

            <p class="mt-1.5 text-xs text-slate-500">
                Bank ditetapkan oleh sistem dan tidak dapat diganti.
            </p>
        </div>

        <div>
            <label for="bank_account_number" class="block text-sm font-semibold text-slate-700">
                Nomor Rekening BSI <span class="text-red-500">*</span>
            </label>

            <input
                id="bank_account_number"
                type="text"
                inputmode="numeric"
                name="bank_account_number"
                value="{{ old('bank_account_number', $profileUser?->bank_account_number) }}"
                minlength="8"
                maxlength="20"
                required
                placeholder="Nomor rekening BSI"
                class="mt-2 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500">

            @error('bank_account_number')
            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="bank_account_name" class="block text-sm font-semibold text-slate-700">
                Nama Pemilik Rekening <span class="text-red-500">*</span>
            </label>

            <input
                id="bank_account_name"
                type="text"
                name="bank_account_name"
                value="{{ old('bank_account_name', $profileUser?->bank_account_name ?? $profileUser?->name) }}"
                maxlength="150"
                required
                placeholder="Sesuai nama pada rekening BSI"
                class="mt-2 w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500">

            @error('bank_account_name')
            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ktpAddress = document.getElementById('ktp_address');
        const domicileAddress = document.getElementById('domicile_address');
        const copyAddressButton = document.getElementById('copy-ktp-address');

        if (copyAddressButton && ktpAddress && domicileAddress) {
            copyAddressButton.addEventListener('click', function() {
                domicileAddress.value = ktpAddress.value;
                domicileAddress.focus();
            });
        }

        const photoInput = document.getElementById('formal_photo');
        const photoPreviewWrap = document.getElementById('photo-preview-wrap');
        const photoPreview = document.getElementById('photo-preview');

        if (photoInput && photoPreviewWrap && photoPreview) {
            photoInput.addEventListener('change', function() {
                const file = this.files?.[0];

                if (!file) {
                    photoPreviewWrap.classList.add('hidden');
                    photoPreview.removeAttribute('src');
                    return;
                }

                const reader = new FileReader();

                reader.onload = function(event) {
                    photoPreview.src = event.target.result;
                    photoPreviewWrap.classList.remove('hidden');
                };

                reader.readAsDataURL(file);
            });
        }

        const role = document.getElementById('role');
        const department = document.getElementById('department_id');
        const kabidWarning = document.getElementById('kabid-warning');

        function updateKabidWarning() {
            if (!role || !department || !kabidWarning) {
                return;
            }

            const selectedOption =
                department.options[department.selectedIndex];

            const departmentHasKabid =
                selectedOption?.dataset?.hasKabid === '1';

            if (
                role.value === 'kabid' &&
                departmentHasKabid
            ) {
                kabidWarning.classList.remove('hidden');
            } else {
                kabidWarning.classList.add('hidden');
            }
        }

        role?.addEventListener(
            'change',
            updateKabidWarning
        );

        department?.addEventListener(
            'change',
            updateKabidWarning
        );

        updateKabidWarning();
    });
</script>