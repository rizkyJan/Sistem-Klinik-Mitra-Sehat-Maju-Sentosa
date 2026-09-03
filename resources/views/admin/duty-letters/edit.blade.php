@extends('layouts.admin')

@section('title', 'Edit Surat Dinas')
@section('page-title', 'Edit Surat Dinas')

@section('content')
<div class="mx-auto max-w-6xl space-y-6">

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <a
                href="{{ route('admin.duty-letters.index') }}"
                class="inline-flex items-center gap-1.5 text-sm font-medium text-slate-500 transition hover:text-slate-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali ke Surat Dinas
            </a>

            <h1 class="mt-2 text-2xl font-bold text-slate-800">
                Edit Surat Dinas
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Perubahan hanya diperbolehkan sebelum hari kegiatan dan langsung terlihat oleh Karyawan/Kabid yang ditugaskan.
            </p>
        </div>
    </div>


    <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
        <div class="flex gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="mt-0.5 h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
            </svg>
            <div>
                <p class="font-semibold">Edit hanya sebelum hari H</p>
                <p class="mt-1 leading-relaxed">
                    Setelah tanggal kegiatan tiba, surat otomatis dikunci. Jika penerima dihapus dari daftar sebelum hari H, surat tersebut juga tidak lagi muncul pada akun penerima itu.
                </p>
            </div>
        </div>
    </div>


    @if($errors->any())
    <div class="rounded-xl border border-red-200 bg-red-50 p-4">
        <div class="flex gap-3">
            <div class="mt-0.5 text-red-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                </svg>
            </div>

            <div>
                <p class="text-sm font-semibold text-red-800">
                    Data belum bisa disimpan.
                </p>
                <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-red-700">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif


    <form
        method="POST"
        action="{{ route('admin.duty-letters.update', $dutyLetter) }}"
        enctype="multipart/form-data"
        class="space-y-6">
        @csrf
        @method('PUT')

        {{-- ========================================================
            INFORMASI SURAT
        ======================================================== --}}
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-5 py-4 sm:px-6">
                <h2 class="text-base font-semibold text-slate-800">
                    Informasi Surat & Kegiatan
                </h2>
                <p class="mt-1 text-sm text-slate-500">
                    Isi informasi berdasarkan surat undangan atau surat tugas yang diterima.
                </p>
            </div>

            <div class="grid gap-5 p-5 sm:grid-cols-2 sm:p-6">

                <div>
                    <label for="letter_number" class="text-sm font-medium text-slate-700">
                        Nomor Surat
                    </label>
                    <input
                        id="letter_number"
                        name="letter_number"
                        type="text"
                        value="{{ old('letter_number', $dutyLetter->letter_number) }}"
                        placeholder="Contoh: 005/MSMS/IX/2026"
                        class="mt-2 w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                    <p class="mt-1 text-xs text-slate-400">Opsional jika surat tidak memiliki nomor.</p>
                </div>

                <div>
                    <label for="organizer" class="text-sm font-medium text-slate-700">
                        Penyelenggara
                    </label>
                    <input
                        id="organizer"
                        name="organizer"
                        type="text"
                        value="{{ old('organizer', $dutyLetter->organizer) }}"
                        placeholder="Contoh: Dinas Kesehatan Sukoharjo"
                        class="mt-2 w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div class="sm:col-span-2">
                    <label for="title" class="text-sm font-medium text-slate-700">
                        Judul / Nama Kegiatan <span class="text-red-500">*</span>
                    </label>
                    <input
                        id="title"
                        name="title"
                        type="text"
                        value="{{ old('title', $dutyLetter->title) }}"
                        required
                        placeholder="Contoh: Rapat Koordinasi Pelayanan BPJS"
                        class="mt-2 w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div class="sm:col-span-2">
                    <label for="description" class="text-sm font-medium text-slate-700">
                        Keterangan
                    </label>
                    <textarea
                        id="description"
                        name="description"
                        rows="4"
                        placeholder="Tambahkan informasi penting untuk pegawai yang ditugaskan..."
                        class="mt-2 w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description', $dutyLetter->description) }}</textarea>
                </div>

            </div>
        </div>


        {{-- ========================================================
            WAKTU & LOKASI
        ======================================================== --}}
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-5 py-4 sm:px-6">
                <h2 class="text-base font-semibold text-slate-800">
                    Waktu & Lokasi
                </h2>
                <p class="mt-1 text-sm text-slate-500">
                    Informasi ini akan tampil langsung pada akun pegawai yang ditugaskan.
                </p>
            </div>

            <div class="grid gap-5 p-5 sm:grid-cols-2 lg:grid-cols-3 sm:p-6">

                <div>
                    <label for="event_date" class="text-sm font-medium text-slate-700">
                        Tanggal Kegiatan <span class="text-red-500">*</span>
                    </label>
                    <input
                        id="event_date"
                        name="event_date"
                        type="date"
                        value="{{ old('event_date', $dutyLetter->event_date?->format('Y-m-d')) }}"
                        min="{{ now()->addDay()->format('Y-m-d') }}"
                        required
                        class="mt-2 w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label for="start_time" class="text-sm font-medium text-slate-700">
                        Jam Mulai <span class="text-red-500">*</span>
                    </label>
                    <input
                        id="start_time"
                        name="start_time"
                        type="time"
                        value="{{ old('start_time', substr((string) $dutyLetter->start_time, 0, 5)) }}"
                        required
                        class="mt-2 w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label for="end_time" class="text-sm font-medium text-slate-700">
                        Jam Selesai
                    </label>
                    <input
                        id="end_time"
                        name="end_time"
                        type="time"
                        value="{{ old('end_time', $dutyLetter->end_time ? substr((string) $dutyLetter->end_time, 0, 5) : '') }}"
                        class="mt-2 w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                    <p class="mt-1 text-xs text-slate-400">Opsional jika belum diketahui.</p>
                </div>

                <div class="sm:col-span-2 lg:col-span-3">
                    <label for="location_name" class="text-sm font-medium text-slate-700">
                        Nama Lokasi <span class="text-red-500">*</span>
                    </label>
                    <input
                        id="location_name"
                        name="location_name"
                        type="text"
                        value="{{ old('location_name', $dutyLetter->location_name) }}"
                        required
                        placeholder="Contoh: Aula Dinas Kesehatan Kabupaten Sukoharjo"
                        class="mt-2 w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div class="sm:col-span-2 lg:col-span-3">
                    <label for="location_address" class="text-sm font-medium text-slate-700">
                        Alamat Lengkap
                    </label>
                    <textarea
                        id="location_address"
                        name="location_address"
                        rows="3"
                        placeholder="Alamat tempat kegiatan..."
                        class="mt-2 w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">{{ old('location_address', $dutyLetter->location_address) }}</textarea>
                </div>

                <div class="sm:col-span-2 lg:col-span-3">
                    <label for="maps_url" class="text-sm font-medium text-slate-700">
                        Link Google Maps
                    </label>
                    <input
                        id="maps_url"
                        name="maps_url"
                        type="url"
                        value="{{ old('maps_url', $dutyLetter->maps_url) }}"
                        placeholder="https://maps.google.com/..."
                        class="mt-2 w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                    <p class="mt-1 text-xs text-slate-400">
                        Opsional. Nantinya pegawai bisa membuka lokasi langsung dari detail surat.
                    </p>
                </div>

            </div>
        </div>


        {{-- ========================================================
            PDF SURAT
        ======================================================== --}}
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-5 py-4 sm:px-6">
                <h2 class="text-base font-semibold text-slate-800">
                    PDF Surat Dinas
                </h2>
                <p class="mt-1 text-sm text-slate-500">
                    PDF lama tetap digunakan jika Anda tidak memilih file baru.
                </p>
            </div>

            <div class="p-5 sm:p-6">
                <div class="mb-4 flex flex-col gap-3 rounded-lg border border-slate-200 bg-slate-50 p-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="min-w-0">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">PDF Saat Ini</p>
                        <p class="mt-1 truncate text-sm font-semibold text-slate-700">
                            {{ $dutyLetter->letter_original_name ?: 'Surat dinas.pdf' }}
                        </p>
                    </div>

                    <a
                        href="{{ route('admin.duty-letters.pdf', $dutyLetter) }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex shrink-0 items-center justify-center rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-100">
                        Lihat PDF Saat Ini
                    </a>
                </div>

                <label
                    for="letter"
                    class="group flex cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed border-slate-300 bg-slate-50 px-6 py-10 text-center transition hover:border-blue-400 hover:bg-blue-50/40">

                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-white text-slate-500 shadow-sm group-hover:text-blue-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 3h7l5 5v13H7a2 2 0 01-2-2V5a2 2 0 012-2zM14 3v6h6M12 12v6m-3-3h6" />
                        </svg>
                    </div>

                    <p class="mt-3 text-sm font-semibold text-slate-700">
                        Pilih PDF baru jika ingin mengganti
                    </p>
                    <p class="mt-1 text-xs text-slate-500">
                        Opsional • Hanya PDF • maksimal 10 MB
                    </p>
                    <p id="selectedLetterName" class="mt-3 hidden rounded-full bg-blue-100 px-3 py-1 text-xs font-medium text-blue-700"></p>

                    <input
                        id="letter"
                        name="letter"
                        type="file"
                        accept="application/pdf,.pdf"
                        class="sr-only">
                </label>
            </div>
        </div>


        {{-- ========================================================
            PENERIMA SURAT
        ======================================================== --}}
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-5 py-4 sm:px-6">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-base font-semibold text-slate-800">
                            Karyawan / Kabid yang Ditugaskan
                        </h2>
                        <p class="mt-1 text-sm text-slate-500">
                            Centang penerima yang tetap/baru ditugaskan. Perubahan daftar penerima langsung berlaku tanpa proses ACC.
                        </p>
                    </div>

                    <div class="rounded-full bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700">
                        <span id="selectedAssigneeCount">0</span> dipilih
                    </div>
                </div>
            </div>

            <div class="p-5 sm:p-6">
                @if($assignees->isEmpty())
                <div class="rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
                    Belum ada Karyawan/Kabid aktif yang sudah disetujui Admin. Pastikan data pegawai sudah diverifikasi terlebih dahulu.
                </div>
                @else
                <div class="flex flex-col gap-3 sm:flex-row">
                    <div class="relative flex-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M11 18a7 7 0 100-14 7 7 0 000 14z" />
                        </svg>
                        <input
                            id="assigneeSearch"
                            type="text"
                            placeholder="Cari nama, role, atau bidang..."
                            class="w-full rounded-lg border-slate-300 pl-10 text-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <button
                        id="selectVisibleAssignees"
                        type="button"
                        class="rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                        Pilih Semua yang Tampil
                    </button>

                    <button
                        id="clearAssignees"
                        type="button"
                        class="rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                        Kosongkan
                    </button>
                </div>

                @error('assignee_ids')
                <p class="mt-3 text-sm font-medium text-red-600">{{ $message }}</p>
                @enderror

                <div id="assigneeList" class="mt-4 grid max-h-[430px] gap-3 overflow-y-auto pr-1 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach($assignees as $assignee)
                    @php
                    $searchText = strtolower(
                    $assignee->name . ' ' .
                    $assignee->role . ' ' .
                    ($assignee->department?->name ?? '')
                    );
                    @endphp

                    <label
                        data-assignee-card
                        data-search="{{ $searchText }}"
                        class="cursor-pointer rounded-xl border border-slate-200 bg-white p-4 transition hover:border-blue-300 hover:bg-blue-50/30">

                        <div class="flex items-start gap-3">
                            <input
                                type="checkbox"
                                name="assignee_ids[]"
                                value="{{ $assignee->id }}"
                                @checked(in_array($assignee->id, array_map('intval', (array) old('assignee_ids', $selectedAssigneeIds)), true))
                            class="assignee-checkbox mt-1 rounded border-slate-300 text-blue-600 focus:ring-blue-500">

                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-800">
                                    {{ $assignee->name }}
                                </p>

                                <div class="mt-2 flex flex-wrap gap-1.5">
                                    <span class="rounded-full px-2 py-0.5 text-[11px] font-semibold {{ $assignee->role === 'kabid' ? 'bg-violet-50 text-violet-700' : 'bg-blue-50 text-blue-700' }}">
                                        {{ $assignee->role === 'kabid' ? 'Kabid' : 'Karyawan' }}
                                    </span>

                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-medium text-slate-600">
                                        {{ $assignee->department?->name ?? 'Tanpa Bidang' }}
                                    </span>
                                </div>

                                @if($assignee->nik)
                                <p class="mt-2 text-xs text-slate-400">
                                    NIK: {{ $assignee->nik }}
                                </p>
                                @endif
                            </div>
                        </div>
                    </label>
                    @endforeach
                </div>

                <div id="assigneeEmptySearch" class="mt-4 hidden rounded-lg bg-slate-50 p-5 text-center text-sm text-slate-500">
                    Pegawai tidak ditemukan dari pencarian tersebut.
                </div>
                @endif
            </div>
        </div>


        {{-- ========================================================
            ACTIONS
        ======================================================== --}}
        <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
            <a
                href="{{ route('admin.duty-letters.show', $dutyLetter) }}"
                class="rounded-lg border border-slate-300 bg-white px-5 py-2.5 text-center text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                Batal
            </a>

            <button
                type="submit"
                @disabled($assignees->isEmpty())
                class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:bg-slate-300">
                Simpan Perubahan
            </button>
        </div>

    </form>
</div>
@endsection


@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const letterInput = document.getElementById('letter');
        const selectedLetterName = document.getElementById('selectedLetterName');

        if (letterInput && selectedLetterName) {
            letterInput.addEventListener('change', function() {
                const file = this.files && this.files[0] ? this.files[0] : null;

                if (!file) {
                    selectedLetterName.textContent = '';
                    selectedLetterName.classList.add('hidden');
                    return;
                }

                selectedLetterName.textContent = file.name;
                selectedLetterName.classList.remove('hidden');
            });
        }


        const searchInput = document.getElementById('assigneeSearch');
        const cards = Array.from(document.querySelectorAll('[data-assignee-card]'));
        const checkboxes = Array.from(document.querySelectorAll('.assignee-checkbox'));
        const countElement = document.getElementById('selectedAssigneeCount');
        const selectVisibleButton = document.getElementById('selectVisibleAssignees');
        const clearButton = document.getElementById('clearAssignees');
        const emptySearch = document.getElementById('assigneeEmptySearch');

        function updateCount() {
            if (!countElement) {
                return;
            }

            countElement.textContent = checkboxes.filter(function(checkbox) {
                return checkbox.checked;
            }).length;
        }

        function filterCards() {
            if (!searchInput) {
                return;
            }

            const keyword = searchInput.value.trim().toLowerCase();
            let visibleCount = 0;

            cards.forEach(function(card) {
                const matches = card.dataset.search.includes(keyword);
                card.classList.toggle('hidden', !matches);

                if (matches) {
                    visibleCount++;
                }
            });

            if (emptySearch) {
                emptySearch.classList.toggle('hidden', visibleCount !== 0);
            }
        }

        checkboxes.forEach(function(checkbox) {
            checkbox.addEventListener('change', updateCount);
        });

        if (searchInput) {
            searchInput.addEventListener('input', filterCards);
        }

        if (selectVisibleButton) {
            selectVisibleButton.addEventListener('click', function() {
                cards.forEach(function(card) {
                    if (card.classList.contains('hidden')) {
                        return;
                    }

                    const checkbox = card.querySelector('.assignee-checkbox');

                    if (checkbox) {
                        checkbox.checked = true;
                    }
                });

                updateCount();
            });
        }

        if (clearButton) {
            clearButton.addEventListener('click', function() {
                checkboxes.forEach(function(checkbox) {
                    checkbox.checked = false;
                });

                updateCount();
            });
        }

        updateCount();
    });
</script>
@endpush