@extends($layout)

@section('title', 'Hear You')
@section('page-title', 'Hear You')

@section('content')
@php
$periodLabel = $status['period']->copy()->locale('id')->translatedFormat('F Y');
@endphp

<div class="space-y-6">
    {{-- ============================================================
        HEADER
    ============================================================ --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <div class="flex items-center gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 4v-4z" />
                        </svg>
                    </div>

                    <div>
                        <h1 class="text-2xl font-bold text-slate-900">Hear You</h1>
                        <p class="mt-1 text-sm text-slate-500">
                            Ruang aspirasi, kritik, saran, ide, dan evaluasi tindak lanjut pegawai.
                        </p>
                    </div>
                </div>
            </div>

            <div class="rounded-xl border px-4 py-3 {{ $status['locked'] ? 'border-amber-200 bg-amber-50' : 'border-emerald-200 bg-emerald-50' }}">
                <p class="text-xs font-semibold uppercase tracking-wide {{ $status['locked'] ? 'text-amber-700' : 'text-emerald-700' }}">
                    Status {{ $periodLabel }}
                </p>
                <p class="mt-1 text-sm font-bold {{ $status['locked'] ? 'text-amber-900' : 'text-emerald-900' }}">
                    {{ $status['locked'] ? 'Kewajiban belum selesai' : 'Kewajiban selesai • fitur terbuka' }}
                </p>
            </div>
        </div>
    </div>

    {{-- ============================================================
        RINGKASAN KEWAJIBAN
    ============================================================ --}}
    <div class="grid gap-4 md:grid-cols-2">
        <div class="rounded-2xl border {{ $status['current_submitted'] ? 'border-emerald-200 bg-emerald-50' : 'border-amber-200 bg-amber-50' }} p-5 shadow-sm">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide {{ $status['current_submitted'] ? 'text-emerald-700' : 'text-amber-700' }}">
                        Hear You {{ $periodLabel }}
                    </p>
                    <p class="mt-2 text-lg font-bold {{ $status['current_submitted'] ? 'text-emerald-900' : 'text-amber-900' }}">
                        {{ $status['current_submitted'] ? 'Sudah dikirim' : 'Belum diisi' }}
                    </p>
                    <p class="mt-1 text-sm {{ $status['current_submitted'] ? 'text-emerald-700' : 'text-amber-700' }}">
                        Setiap Karyawan dan Kabid wajib mengirim satu Hear You setiap bulan.
                    </p>
                </div>

                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full {{ $status['current_submitted'] ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                    @if($status['current_submitted'])
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    @else
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M5.07 19h13.86a2 2 0 001.74-3L13.74 4a2 2 0 00-3.48 0L3.33 16a2 2 0 001.74 3z" />
                    </svg>
                    @endif
                </div>
            </div>
        </div>

        <div class="rounded-2xl border {{ $status['due_evaluation_count'] === 0 ? 'border-emerald-200 bg-emerald-50' : 'border-amber-200 bg-amber-50' }} p-5 shadow-sm">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide {{ $status['due_evaluation_count'] === 0 ? 'text-emerald-700' : 'text-amber-700' }}">
                        Evaluasi Tindak Lanjut
                    </p>
                    <p class="mt-2 text-lg font-bold {{ $status['due_evaluation_count'] === 0 ? 'text-emerald-900' : 'text-amber-900' }}">
                        {{ $status['due_evaluation_count'] === 0 ? 'Tidak ada evaluasi tertunda' : $status['due_evaluation_count'] . ' evaluasi wajib' }}
                    </p>
                    <p class="mt-1 text-sm {{ $status['due_evaluation_count'] === 0 ? 'text-emerald-700' : 'text-amber-700' }}">
                        Evaluasi muncul setelah periode berganti dan Admin sudah memberikan tanggapan.
                    </p>
                </div>

                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full {{ $status['due_evaluation_count'] === 0 ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                    <span class="text-sm font-extrabold">{{ $status['due_evaluation_count'] }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================
        FORM HEAR YOU BULAN INI
    ============================================================ --}}
    @if(!$currentFeedback)
    <div class="rounded-2xl border border-blue-200 bg-white shadow-sm">
        <div class="border-b border-blue-100 bg-blue-50 px-6 py-4">
            <h2 class="text-lg font-bold text-blue-900">Isi Hear You {{ $periodLabel }}</h2>
            <p class="mt-1 text-sm text-blue-700">
                Setelah dikirim, kewajiban Hear You bulan ini dianggap selesai dan tidak dapat dikirim dua kali.
            </p>
        </div>

        <form method="POST" action="{{ route($routePrefix . '.hear-you.store') }}" class="space-y-5 p-6">
            @csrf

            <div>
                <label for="category" class="mb-2 block text-sm font-semibold text-slate-700">
                    Jenis Aspirasi <span class="text-red-500">*</span>
                </label>
                <select
                    id="category"
                    name="category"
                    required
                    class="w-full rounded-xl border-slate-300 bg-white text-sm text-slate-800 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Pilih jenis aspirasi</option>
                    @foreach($categoryOptions as $value => $label)
                    <option value="{{ $value }}" @selected(old('category')===$value)>
                        {{ $label }}
                    </option>
                    @endforeach
                </select>
                @error('category')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <div class="mb-2 flex items-end justify-between gap-3">
                    <label for="message" class="block text-sm font-semibold text-slate-700">
                        Isi Hear You <span class="text-red-500">*</span>
                    </label>
                    <span class="text-xs text-slate-400">Minimal 10 karakter</span>
                </div>
                <textarea
                    id="message"
                    name="message"
                    rows="7"
                    maxlength="5000"
                    required
                    placeholder="Tuliskan kritik, saran, keluhan, ide, atau apresiasi Anda dengan jelas..."
                    class="w-full rounded-xl border-slate-300 text-sm text-slate-800 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('message') }}</textarea>
                @error('message')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm leading-relaxed text-slate-600">
                <strong class="text-slate-800">Catatan:</strong>
                Tuliskan masukan yang nyata dan dapat ditindaklanjuti. Hear You tersimpan atas nama akun Anda agar Admin dapat menanggapi dan tindak lanjutnya bisa dievaluasi secara terukur.
            </div>

            <div class="flex justify-end">
                <button
                    type="submit"
                    class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                    </svg>
                    Kirim Hear You
                </button>
            </div>
        </form>
    </div>
    @else
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Hear You Bulan Ini</p>
                <div class="mt-2 flex flex-wrap items-center gap-2">
                    <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
                        {{ $currentFeedback->categoryLabel() }}
                    </span>
                    <span class="rounded-full {{ $currentFeedback->admin_response ? 'bg-violet-50 text-violet-700' : 'bg-amber-50 text-amber-700' }} px-3 py-1 text-xs font-semibold">
                        {{ $currentFeedback->followUpStatusLabel() }}
                    </span>
                </div>
            </div>

            <p class="text-xs text-slate-400">
                Dikirim {{ $currentFeedback->created_at->locale('id')->translatedFormat('d F Y, H:i') }}
            </p>
        </div>

        <div class="mt-5 rounded-xl bg-slate-50 p-4">
            <p class="whitespace-pre-line text-sm leading-relaxed text-slate-700">{{ $currentFeedback->message }}</p>
        </div>

        @if($currentFeedback->admin_response)
        <div class="mt-4 rounded-xl border border-blue-100 bg-blue-50 p-4">
            <p class="text-xs font-semibold uppercase tracking-wide text-blue-700">Tanggapan Admin</p>
            <p class="mt-2 whitespace-pre-line text-sm leading-relaxed text-blue-900">{{ $currentFeedback->admin_response }}</p>
            <p class="mt-3 text-xs text-blue-600">
                Evaluasi terhadap tanggapan ini akan tersedia setelah masuk bulan berikutnya.
            </p>
        </div>
        @else
        <div class="mt-4 rounded-xl border border-amber-100 bg-amber-50 p-4 text-sm text-amber-800">
            Menunggu Admin memberikan tanggapan. Hal ini tidak menambah kewajiban evaluasi sampai tanggapan Admin tersedia.
        </div>
        @endif
    </div>
    @endif

    {{-- ============================================================
        EVALUASI YANG WAJIB DIISI
    ============================================================ --}}
    @if($dueEvaluations->isNotEmpty())
    <div class="space-y-4">
        <div>
            <h2 class="text-lg font-bold text-slate-900">Evaluasi yang Harus Diselesaikan</h2>
            <p class="mt-1 text-sm text-slate-500">
                Nilai apakah tindak lanjut dari Hear You periode sebelumnya sudah memberikan perubahan.
            </p>
        </div>

        @foreach($dueEvaluations as $feedback)
        <div class="overflow-hidden rounded-2xl border border-amber-200 bg-white shadow-sm">
            <div class="border-b border-amber-100 bg-amber-50 px-6 py-4">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-amber-700">Wajib Dievaluasi</p>
                        <h3 class="mt-1 font-bold text-amber-950">
                            Hear You {{ $feedback->period->copy()->locale('id')->translatedFormat('F Y') }}
                        </h3>
                    </div>
                    <span class="w-fit rounded-full bg-white px-3 py-1 text-xs font-semibold text-amber-700 shadow-sm">
                        {{ $feedback->categoryLabel() }}
                    </span>
                </div>
            </div>

            <div class="space-y-5 p-6">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Aspirasi Anda</p>
                    <p class="mt-2 whitespace-pre-line text-sm leading-relaxed text-slate-700">{{ $feedback->message }}</p>
                </div>

                <div class="rounded-xl border border-blue-100 bg-blue-50 p-4">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <p class="text-xs font-semibold uppercase tracking-wide text-blue-700">Tanggapan Admin</p>
                        <span class="rounded-full bg-white px-2.5 py-1 text-xs font-semibold text-blue-700">
                            {{ $feedback->followUpStatusLabel() }}
                        </span>
                    </div>
                    <p class="mt-2 whitespace-pre-line text-sm leading-relaxed text-blue-900">{{ $feedback->admin_response }}</p>
                </div>

                <form method="POST" action="{{ route($routePrefix . '.hear-you.evaluate', $feedback) }}" class="space-y-4">
                    @csrf
                    @method('PATCH')

                    <fieldset>
                        <legend class="mb-3 text-sm font-semibold text-slate-700">
                            Bagaimana hasil tindak lanjutnya? <span class="text-red-500">*</span>
                        </legend>

                        <div class="grid gap-3 lg:grid-cols-3">
                            @foreach($evaluationOptions as $value => $label)
                            <label class="flex cursor-pointer gap-3 rounded-xl border border-slate-200 p-4 transition hover:border-blue-300 hover:bg-blue-50/50">
                                <input
                                    type="radio"
                                    name="employee_evaluation"
                                    value="{{ $value }}"
                                    required
                                    class="mt-0.5 border-slate-300 text-blue-600 focus:ring-blue-500">
                                <span class="text-sm font-medium text-slate-700">{{ $label }}</span>
                            </label>
                            @endforeach
                        </div>
                    </fieldset>

                    <div>
                        <label for="evaluation_note_{{ $feedback->id }}" class="mb-2 block text-sm font-semibold text-slate-700">
                            Catatan Evaluasi <span class="font-normal text-slate-400">(opsional)</span>
                        </label>
                        <textarea
                            id="evaluation_note_{{ $feedback->id }}"
                            name="evaluation_note"
                            rows="4"
                            maxlength="3000"
                            placeholder="Contoh: AC sudah diperbaiki dan sekarang berfungsi normal..."
                            class="w-full rounded-xl border-slate-300 text-sm text-slate-800 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                    </div>

                    <div class="flex justify-end">
                        <button
                            type="submit"
                            class="rounded-xl bg-amber-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2">
                            Simpan Evaluasi
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- ============================================================
        RIWAYAT
    ============================================================ --}}
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 px-6 py-4">
            <h2 class="text-lg font-bold text-slate-900">Riwayat Hear You</h2>
            <p class="mt-1 text-sm text-slate-500">Riwayat aspirasi, tanggapan Admin, dan evaluasi Anda.</p>
        </div>

        @if($history->count())
        <div class="divide-y divide-slate-100">
            @foreach($history as $item)
            <div class="p-6">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <p class="font-bold text-slate-900">
                            {{ $item->period->copy()->locale('id')->translatedFormat('F Y') }}
                        </p>
                        <div class="mt-2 flex flex-wrap gap-2">
                            <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600">
                                {{ $item->categoryLabel() }}
                            </span>
                            <span class="rounded-full {{ $item->admin_response ? 'bg-blue-50 text-blue-700' : 'bg-amber-50 text-amber-700' }} px-2.5 py-1 text-xs font-semibold">
                                {{ $item->followUpStatusLabel() }}
                            </span>
                            @if($item->evaluated_at)
                            <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">
                                Evaluasi: {{ $item->evaluationLabel() }}
                            </span>
                            @endif
                        </div>
                    </div>
                    <p class="text-xs text-slate-400">{{ $item->created_at->format('d/m/Y H:i') }}</p>
                </div>

                <p class="mt-4 whitespace-pre-line text-sm leading-relaxed text-slate-700">{{ $item->message }}</p>

                @if($item->admin_response)
                <div class="mt-4 rounded-xl bg-blue-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-blue-700">Tanggapan Admin</p>
                    <p class="mt-2 whitespace-pre-line text-sm leading-relaxed text-blue-900">{{ $item->admin_response }}</p>
                </div>
                @endif

                @if($item->evaluated_at)
                <div class="mt-3 rounded-xl bg-emerald-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700">Evaluasi Anda</p>
                    <p class="mt-1 text-sm font-semibold text-emerald-900">{{ $item->evaluationLabel() }}</p>
                    @if($item->evaluation_note)
                    <p class="mt-2 whitespace-pre-line text-sm leading-relaxed text-emerald-800">{{ $item->evaluation_note }}</p>
                    @endif
                </div>
                @endif
            </div>
            @endforeach
        </div>

        @if($history->hasPages())
        <div class="border-t border-slate-200 px-6 py-4">
            {{ $history->links() }}
        </div>
        @endif
        @else
        <div class="px-6 py-12 text-center">
            <p class="text-sm text-slate-500">Belum ada riwayat Hear You.</p>
        </div>
        @endif
    </div>
</div>
@endsection