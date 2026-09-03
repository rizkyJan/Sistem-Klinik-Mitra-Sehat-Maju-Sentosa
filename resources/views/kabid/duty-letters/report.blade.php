@extends('layouts.kabid')

@section('title', 'Laporan Hasil Dinas')
@section('page-title', 'Laporan Hasil Dinas')

@section('content')

@php
$letter = $dutyAssignment->dutyLetter;
$report = $dutyAssignment->report;
$existingFiles = $report?->files ?? collect();
$isRevision = $dutyAssignment->report_status === \App\Models\DutyAssignment::REPORT_REVISION;
@endphp

<x-toast-notification />

<div class="mx-auto max-w-5xl space-y-6">

    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <a
                href="{{ route('kabid.duty-letters.show', $dutyAssignment) }}"
                class="inline-flex items-center gap-1 text-sm font-medium text-blue-600 hover:text-blue-700">
                <span aria-hidden="true">←</span>
                Kembali ke Detail Surat
            </a>

            <h1 class="mt-3 text-2xl font-bold text-slate-800">
                {{ $isRevision ? 'Perbaiki Laporan Hasil Dinas' : 'Buat Laporan Hasil Dinas' }}
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                {{ $letter->title }}
            </p>
        </div>

        <span class="inline-flex w-fit rounded-full px-3 py-1.5 text-xs font-semibold {{ $isRevision ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700' }}">
            {{ $isRevision ? 'Perlu Perbaikan' : 'Belum Dikirim' }}
        </span>
    </div>

    @if($isRevision && $dutyAssignment->revision_note)
    <div class="rounded-xl border border-red-200 bg-red-50 p-5">
        <div class="flex gap-3">
            <div class="mt-0.5 shrink-0 text-red-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 9v4m0 4h.01M10.3 4.5L2.8 18a2 2 0 001.75 3h14.9a2 2 0 001.75-3L13.7 4.5a2 2 0 00-3.4 0z" />
                </svg>
            </div>

            <div>
                <p class="text-sm font-semibold text-red-800">
                    Catatan Perbaikan dari Admin
                </p>
                <p class="mt-2 whitespace-pre-line text-sm leading-relaxed text-red-700">
                    {{ $dutyAssignment->revision_note }}
                </p>
            </div>
        </div>
    </div>
    @endif

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 px-5 py-4 sm:px-6">
            <h2 class="font-semibold text-slate-800">Informasi Kegiatan</h2>
        </div>

        <div class="grid gap-4 p-5 sm:grid-cols-2 sm:p-6 lg:grid-cols-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Tanggal</p>
                <p class="mt-1 text-sm font-semibold text-slate-700">
                    {{ $letter->event_date->translatedFormat('d F Y') }}
                </p>
            </div>

            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Waktu</p>
                <p class="mt-1 text-sm font-semibold text-slate-700">
                    {{ substr((string) $letter->start_time, 0, 5) }}
                    @if($letter->end_time)
                    - {{ substr((string) $letter->end_time, 0, 5) }}
                    @endif
                    WIB
                </p>
            </div>

            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Lokasi</p>
                <p class="mt-1 text-sm font-semibold text-slate-700">
                    {{ $letter->location_name }}
                </p>
            </div>

            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Penyelenggara</p>
                <p class="mt-1 text-sm font-semibold text-slate-700">
                    {{ $letter->organizer ?: '-' }}
                </p>
            </div>
        </div>
    </div>

    <form
        method="POST"
        action="{{ route('kabid.duty-reports.store', $dutyAssignment) }}"
        enctype="multipart/form-data"
        class="space-y-6">
        @csrf

        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-5 py-4 sm:px-6">
                <h2 class="font-semibold text-slate-800">Isi Laporan</h2>
                <p class="mt-1 text-xs text-slate-500">
                    Tuliskan informasi yang benar-benar didapat saat kegiatan berlangsung.
                </p>
            </div>

            <div class="space-y-6 p-5 sm:p-6">
                <div>
                    <label for="discussion_summary" class="block text-sm font-semibold text-slate-700">
                        Pokok Pembahasan <span class="text-red-500">*</span>
                    </label>

                    <p class="mt-1 text-xs text-slate-500">
                        Jelaskan materi atau hal-hal utama yang dibahas dalam rapat/kegiatan.
                    </p>

                    <textarea
                        id="discussion_summary"
                        name="discussion_summary"
                        rows="7"
                        required
                        maxlength="10000"
                        class="mt-2 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        placeholder="Contoh: Pembahasan mengenai perubahan alur pelayanan BPJS, jadwal implementasi, kebutuhan teknis, dan pembagian tugas...">{{ old('discussion_summary', $report?->discussion_summary) }}</textarea>

                    @error('discussion_summary')
                    <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="result_summary" class="block text-sm font-semibold text-slate-700">
                        Hasil / Kesimpulan <span class="text-red-500">*</span>
                    </label>

                    <p class="mt-1 text-xs text-slate-500">
                        Tuliskan keputusan, hasil, informasi penting, atau kesimpulan kegiatan.
                    </p>

                    <textarea
                        id="result_summary"
                        name="result_summary"
                        rows="6"
                        required
                        maxlength="10000"
                        class="mt-2 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        placeholder="Contoh: Klinik diminta menyiapkan pembaruan credential dan melakukan pengujian sistem sebelum tanggal yang ditentukan...">{{ old('result_summary', $report?->result_summary) }}</textarea>

                    @error('result_summary')
                    <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="follow_up" class="block text-sm font-semibold text-slate-700">
                        Tindak Lanjut
                    </label>

                    <p class="mt-1 text-xs text-slate-500">
                        Isi jika ada pekerjaan atau tindakan yang perlu dilakukan setelah kegiatan.
                    </p>

                    <textarea
                        id="follow_up"
                        name="follow_up"
                        rows="5"
                        maxlength="10000"
                        class="mt-2 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        placeholder="Contoh: Koordinasi dengan bagian IT dan pelayanan untuk melakukan pengujian...">{{ old('follow_up', $report?->follow_up) }}</textarea>

                    @error('follow_up')
                    <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="additional_notes" class="block text-sm font-semibold text-slate-700">
                        Catatan Tambahan
                    </label>

                    <textarea
                        id="additional_notes"
                        name="additional_notes"
                        rows="4"
                        maxlength="10000"
                        class="mt-2 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        placeholder="Catatan lain jika diperlukan...">{{ old('additional_notes', $report?->additional_notes) }}</textarea>

                    @error('additional_notes')
                    <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-5 py-4 sm:px-6">
                <h2 class="font-semibold text-slate-800">
                    Dokumentasi Kehadiran <span class="text-red-500">*</span>
                </h2>
                <p class="mt-1 text-xs text-slate-500">
                    Minimal 1 foto, maksimal 5 foto. Format JPG, PNG, atau WEBP. Maksimal 5 MB per foto.
                </p>
            </div>

            <div class="space-y-5 p-5 sm:p-6">
                @if($existingFiles->isNotEmpty())
                <div>
                    <p class="text-sm font-semibold text-slate-700">
                        Foto yang sudah tersimpan
                    </p>

                    <div class="mt-3 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach($existingFiles as $file)
                        <div class="overflow-hidden rounded-xl border border-slate-200 bg-slate-50">
                            <a
                                href="{{ route('kabid.duty-reports.file', [$dutyAssignment, $file]) }}"
                                target="_blank"
                                rel="noopener"
                                class="block aspect-[4/3] bg-slate-100">
                                <img
                                    src="{{ route('kabid.duty-reports.file', [$dutyAssignment, $file]) }}"
                                    alt="Dokumentasi {{ $loop->iteration }}"
                                    class="h-full w-full object-cover">
                            </a>

                            <div class="p-3">
                                <p class="truncate text-xs font-medium text-slate-600" title="{{ $file->original_name }}">
                                    {{ $file->original_name }}
                                </p>

                                <p class="mt-1 text-[11px] text-slate-400">
                                    @if($file->size)
                                    {{ number_format($file->size / 1024, 0, ',', '.') }} KB
                                    @else
                                    Ukuran tidak tersedia
                                    @endif
                                </p>

                                <button
                                    type="submit"
                                    form="delete-duty-report-file-{{ $file->id }}"
                                    class="mt-3 inline-flex w-full items-center justify-center rounded-lg border border-red-200 bg-white px-3 py-2 text-xs font-semibold text-red-600 transition hover:bg-red-50"
                                    onclick="return confirm('Hapus foto dokumentasi ini?')">
                                    Hapus Foto
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <div>
                    <label for="photos" class="block text-sm font-semibold text-slate-700">
                        {{ $existingFiles->isEmpty() ? 'Upload Foto' : 'Tambah Foto' }}
                    </label>

                    <input
                        id="photos"
                        type="file"
                        name="photos[]"
                        accept="image/jpeg,image/png,image/webp"
                        multiple
                        class="mt-2 block w-full rounded-lg border border-slate-300 bg-white text-sm text-slate-600 file:mr-4 file:border-0 file:bg-slate-100 file:px-4 file:py-3 file:text-sm file:font-semibold file:text-slate-700 hover:file:bg-slate-200">

                    <div id="photo-file-info" class="mt-2 text-xs text-slate-500">
                        Belum ada foto baru dipilih.
                    </div>

                    @error('photos')
                    <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                    @enderror

                    @error('photos.*')
                    <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="rounded-lg border border-amber-200 bg-amber-50 p-4">
                    <p class="text-sm font-semibold text-amber-800">
                        Sebelum mengirim laporan
                    </p>
                    <p class="mt-1 text-sm leading-relaxed text-amber-700">
                        Pastikan isi laporan sudah benar dan foto memang menunjukkan dokumentasi kegiatan.
                        Setelah dikirim, laporan akan dikunci sementara sampai Admin memverifikasi atau meminta perbaikan.
                    </p>
                </div>
            </div>
        </div>

        <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
            <a
                href="{{ route('kabid.duty-letters.show', $dutyAssignment) }}"
                class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                Batal
            </a>

            <button
                type="submit"
                class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 12l4 4L19 6" />
                </svg>
                {{ $isRevision ? 'Kirim Ulang Laporan' : 'Kirim Laporan' }}
            </button>
        </div>
    </form>

    @foreach($existingFiles as $file)
    <form
        id="delete-duty-report-file-{{ $file->id }}"
        method="POST"
        action="{{ route('kabid.duty-reports.files.destroy', [$dutyAssignment, $file]) }}"
        class="hidden">
        @csrf
        @method('DELETE')
    </form>
    @endforeach
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const input = document.getElementById('photos');
        const info = document.getElementById('photo-file-info');
        const existingCount = {
            {
                $existingFiles - > count()
            }
        };

        if (!input || !info) {
            return;
        }

        input.addEventListener('change', function() {
            const files = Array.from(input.files || []);

            if (files.length === 0) {
                info.textContent = 'Belum ada foto baru dipilih.';
                return;
            }

            const total = existingCount + files.length;
            const names = files.map(file => file.name).join(', ');

            info.textContent =
                files.length + ' foto baru dipilih. Total setelah disimpan: ' +
                total + '/5. ' + names;
        });
    });
</script>

@endsection