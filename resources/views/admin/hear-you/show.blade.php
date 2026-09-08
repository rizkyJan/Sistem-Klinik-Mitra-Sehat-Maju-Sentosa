@extends('layouts.admin')

@section('title', 'Detail Hear You')
@section('page-title', 'Detail Hear You')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <a href="{{ route('admin.hear-you.index', ['period' => $feedback->period->format('Y-m')]) }}" class="text-sm font-semibold text-blue-600 hover:text-blue-700">
                ← Kembali ke Hear You
            </a>
            <h1 class="mt-2 text-2xl font-bold text-slate-900">Detail Hear You</h1>
            <p class="mt-1 text-sm text-slate-500">
                Periode {{ $feedback->period->copy()->locale('id')->translatedFormat('F Y') }}
            </p>
        </div>

        <span class="w-fit rounded-full {{ $feedback->admin_response ? 'bg-blue-50 text-blue-700' : 'bg-amber-50 text-amber-700' }} px-3 py-1.5 text-xs font-bold">
            {{ $feedback->followUpStatusLabel() }}
        </span>
    </div>

    <div class="grid gap-6 xl:grid-cols-3">
        {{-- DETAIL PEGAWAI + ASPIRASI --}}
        <div class="space-y-6 xl:col-span-2">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Nama Pegawai</p>
                        <p class="mt-1 font-bold text-slate-900">{{ $feedback->user?->name ?? 'Pegawai dihapus' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Role</p>
                        <p class="mt-1 font-semibold text-slate-800">{{ strtoupper($feedback->user?->role ?? '-') }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Bidang</p>
                        <p class="mt-1 font-semibold text-slate-800">{{ $feedback->user?->department?->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">NIP</p>
                        <p class="mt-1 font-semibold text-slate-800">{{ $feedback->user?->nip ?? $feedback->user?->nik ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Aspirasi Pegawai</p>
                        <p class="mt-1 text-sm font-semibold text-slate-800">{{ $feedback->categoryLabel() }}</p>
                    </div>
                    <p class="text-xs text-slate-400">
                        Dikirim {{ $feedback->created_at->locale('id')->translatedFormat('d F Y, H:i') }}
                    </p>
                </div>

                <div class="mt-5 rounded-xl bg-slate-50 p-5">
                    <p class="whitespace-pre-line text-sm leading-relaxed text-slate-700">{{ $feedback->message }}</p>
                </div>
            </div>

            @if($feedback->evaluated_at)
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-6 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700">Evaluasi dari Pegawai</p>
                <p class="mt-2 text-lg font-bold text-emerald-900">{{ $feedback->evaluationLabel() }}</p>
                @if($feedback->evaluation_note)
                <p class="mt-3 whitespace-pre-line text-sm leading-relaxed text-emerald-800">{{ $feedback->evaluation_note }}</p>
                @endif
                <p class="mt-3 text-xs text-emerald-600">
                    Dinilai {{ $feedback->evaluated_at->locale('id')->translatedFormat('d F Y, H:i') }}
                </p>
            </div>
            @elseif($feedback->admin_response)
            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 shadow-sm">
                <p class="text-sm font-semibold text-amber-900">Belum ada evaluasi dari pegawai.</p>
                <p class="mt-1 text-sm text-amber-700">
                    Evaluasi baru diwajibkan setelah periode aspirasi tersebut telah berganti bulan.
                </p>
            </div>
            @endif
        </div>

        {{-- FORM TANGGAPAN ADMIN --}}
        <div>
            <div class="sticky top-6 rounded-2xl border border-blue-200 bg-white shadow-sm">
                <div class="border-b border-blue-100 bg-blue-50 px-5 py-4">
                    <h2 class="font-bold text-blue-900">Tanggapan Admin</h2>
                    <p class="mt-1 text-xs leading-relaxed text-blue-700">
                        Tanggapan dapat diperbarui apabila progres tindak lanjut berubah.
                    </p>
                </div>

                <form method="POST" action="{{ route('admin.hear-you.respond', $feedback) }}" class="space-y-5 p-5">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label for="follow_up_status" class="mb-2 block text-sm font-semibold text-slate-700">
                            Status Tindak Lanjut <span class="text-red-500">*</span>
                        </label>
                        <select
                            id="follow_up_status"
                            name="follow_up_status"
                            required
                            class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Pilih status</option>
                            @foreach($statusOptions as $value => $label)
                            <option
                                value="{{ $value }}"
                                @selected(old('follow_up_status', $feedback->admin_response ? $feedback->follow_up_status : '') === $value)>
                                {{ $label }}
                            </option>
                            @endforeach
                        </select>
                        @error('follow_up_status')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="admin_response" class="mb-2 block text-sm font-semibold text-slate-700">
                            Isi Tanggapan <span class="text-red-500">*</span>
                        </label>
                        <textarea
                            id="admin_response"
                            name="admin_response"
                            rows="9"
                            maxlength="5000"
                            required
                            placeholder="Jelaskan tanggapan dan tindak lanjut yang dilakukan..."
                            class="w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('admin_response', $feedback->admin_response) }}</textarea>
                        @error('admin_response')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    @if($feedback->responded_at)
                    <div class="rounded-xl bg-slate-50 p-3 text-xs leading-relaxed text-slate-500">
                        Terakhir diperbarui oleh
                        <strong class="text-slate-700">{{ $feedback->respondedBy?->name ?? 'Admin' }}</strong>
                        pada {{ $feedback->responded_at->locale('id')->translatedFormat('d F Y, H:i') }}.
                    </div>
                    @endif

                    <button
                        type="submit"
                        class="inline-flex w-full items-center justify-center rounded-xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        {{ $feedback->admin_response ? 'Perbarui Tanggapan' : 'Kirim Tanggapan' }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection