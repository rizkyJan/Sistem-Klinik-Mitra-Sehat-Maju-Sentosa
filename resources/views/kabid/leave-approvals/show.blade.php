@extends('layouts.kabid')

@section('title', 'Detail Persetujuan Izin')
@section('page-title', 'Detail Persetujuan Izin')

@section('content')

<x-toast-notification />
<x-confirm-action-modal />

@php
$canReview =
$leaveRequest->status === 'pending'
&& $leaveRequest->kabid_status
=== \App\Models\LeaveRequest::KABID_STATUS_PENDING;

$kabidApproved =
$leaveRequest->kabid_status
=== \App\Models\LeaveRequest::KABID_STATUS_APPROVED;

$kabidRejected =
$leaveRequest->kabid_status
=== \App\Models\LeaveRequest::KABID_STATUS_REJECTED;
@endphp

<div class="mx-auto max-w-6xl space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

        <div>
            <h1 class="text-2xl font-bold text-slate-800">
                Detail Persetujuan Izin
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Periksa seluruh informasi anggota sebelum memberi keputusan.
            </p>
        </div>

        <a
            href="{{ route('kabid.leave-approvals.index') }}"
            class="inline-flex items-center justify-center
                   rounded-lg border border-slate-300 bg-white
                   px-4 py-2.5 text-sm font-medium text-slate-600
                   hover:bg-slate-50">
            ← Kembali
        </a>
    </div>


    @if($errors->any())

    <div
        class="rounded-xl border border-red-200
                   bg-red-50 p-5">

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


    {{-- WORKFLOW --}}
    <div
        class="rounded-xl border border-slate-200
               bg-white p-5 shadow-sm">

        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">
            Alur Persetujuan
        </p>

        <div class="mt-4 grid gap-3 sm:grid-cols-2">

            <div
                class="
                    rounded-xl border p-4
                    {{
                        $canReview
                            ? 'border-amber-200 bg-amber-50'
                            : (
                                $kabidApproved
                                    ? 'border-emerald-200 bg-emerald-50'
                                    : 'border-red-200 bg-red-50'
                            )
                    }}
                ">

                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                    Tahap 1
                </p>

                <p class="mt-1 font-semibold text-slate-800">
                    Kabid
                </p>

                <p class="mt-1 text-sm">

                    @if($canReview)
                    <span class="font-semibold text-amber-700">
                        ● Menunggu keputusan Anda
                    </span>
                    @elseif($kabidApproved)
                    <span class="font-semibold text-emerald-700">
                        ✓ Disetujui
                    </span>
                    @elseif($kabidRejected)
                    <span class="font-semibold text-red-700">
                        ✕ Ditolak
                    </span>
                    @else
                    <span class="text-slate-500">
                        Tidak diperlukan
                    </span>
                    @endif

                </p>

                @if($leaveRequest->kabid_reviewed_at)
                <p class="mt-2 text-xs text-slate-500">
                    {{ $leaveRequest->kabidReviewer?->name ?? 'Kabid' }}
                    •
                    {{ $leaveRequest->kabid_reviewed_at->format('d/m/Y H:i') }}
                </p>
                @endif
            </div>


            <div
                class="
                    rounded-xl border p-4
                    {{
                        $kabidApproved && $leaveRequest->status === 'pending'
                            ? 'border-blue-200 bg-blue-50'
                            : (
                                $leaveRequest->status === 'approved'
                                    ? 'border-emerald-200 bg-emerald-50'
                                    : 'border-slate-200 bg-slate-100'
                            )
                    }}
                ">

                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                    Tahap 2
                </p>

                <p class="mt-1 font-semibold text-slate-800">
                    Administrator
                </p>

                <p class="mt-1 text-sm">

                    @if($kabidApproved && $leaveRequest->status === 'pending')
                    <span class="font-semibold text-blue-700">
                        ● Menunggu keputusan final
                    </span>
                    @elseif($leaveRequest->status === 'approved')
                    <span class="font-semibold text-emerald-700">
                        ✓ Disetujui Final
                    </span>
                    @elseif($kabidRejected)
                    <span class="text-slate-400">
                        Tidak dilanjutkan
                    </span>
                    @elseif($leaveRequest->status === 'rejected')
                    <span class="font-semibold text-red-700">
                        ✕ Ditolak
                    </span>
                    @else
                    <span class="text-slate-400">
                        🔒 Belum tersedia
                    </span>
                    @endif

                </p>
            </div>
        </div>
    </div>


    {{-- DATA KARYAWAN --}}
    <div
        class="overflow-hidden rounded-xl border
               border-slate-200 bg-white shadow-sm">

        <div class="border-b border-slate-200 px-6 py-5">
            <h2 class="font-semibold text-slate-800">
                Data Karyawan
            </h2>
        </div>

        <div class="grid gap-6 p-6 sm:grid-cols-2 lg:grid-cols-4">

            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                    Nama
                </p>

                <p class="mt-1 font-medium text-slate-800">
                    {{ $leaveRequest->user?->name ?? '-' }}
                </p>
            </div>


            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                    NIK
                </p>

                <p class="mt-1 font-medium text-slate-800">
                    {{ $leaveRequest->user?->nik ?? '-' }}
                </p>
            </div>


            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                    Bidang
                </p>

                <p class="mt-1 font-medium text-slate-800">
                    {{ $leaveRequest->user?->department?->name ?? '-' }}
                </p>
            </div>


            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                    WhatsApp
                </p>

                <p class="mt-1 font-medium text-slate-800">
                    {{ $leaveRequest->user?->whatsapp ?? '-' }}
                </p>
            </div>
        </div>
    </div>


    {{-- INFORMASI IZIN --}}
    <div
        class="overflow-hidden rounded-xl border
               border-slate-200 bg-white shadow-sm">

        <div class="border-b border-slate-200 px-6 py-5">
            <h2 class="font-semibold text-slate-800">
                Informasi Perizinan
            </h2>
        </div>

        <div class="grid gap-6 p-6 sm:grid-cols-2 lg:grid-cols-4">

            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                    Jenis
                </p>

                <p class="mt-1 font-medium text-slate-800">
                    {{ $leaveRequest->permissionType?->name ?? '-' }}
                </p>
            </div>


            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                    Tanggal Mulai
                </p>

                <p class="mt-1 font-medium text-slate-800">
                    {{ $leaveRequest->start_date?->format('d/m/Y') ?? '-' }}
                </p>
            </div>


            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                    Tanggal Selesai
                </p>

                <p class="mt-1 font-medium text-slate-800">
                    {{ $leaveRequest->end_date?->format('d/m/Y') ?? '-' }}
                </p>
            </div>


            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                    Total
                </p>

                <p class="mt-1 font-medium text-slate-800">
                    {{ $leaveRequest->total_days }} hari
                </p>
            </div>


            <div class="sm:col-span-2 lg:col-span-4">

                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                    Alasan / Keperluan
                </p>

                <p class="mt-1 whitespace-pre-line text-sm leading-6 text-slate-700">
                    {{ $leaveRequest->reason ?: '-' }}
                </p>
            </div>
        </div>
    </div>


    {{-- RINGKASAN KEBIJAKAN --}}
    <div
        class="overflow-hidden rounded-xl border
               border-slate-200 bg-white shadow-sm">

        <div class="border-b border-slate-200 px-6 py-5">
            <h2 class="font-semibold text-slate-800">
                Ringkasan Ketentuan
            </h2>
        </div>

        <div class="grid gap-4 p-6 sm:grid-cols-2 lg:grid-cols-4">

            <div class="rounded-lg bg-slate-50 p-4">
                <p class="text-xs text-slate-500">
                    Ditanggung Kebijakan
                </p>

                <p class="mt-1 text-xl font-bold text-slate-800">
                    {{ $leaveRequest->policy_covered_days ?? 0 }} hari
                </p>
            </div>


            <div class="rounded-lg bg-blue-50 p-4">
                <p class="text-xs text-blue-600">
                    Memakai Cuti Tahunan
                </p>

                <p class="mt-1 text-xl font-bold text-blue-700">
                    {{ $leaveRequest->annual_leave_deducted_days ?? 0 }} hari
                </p>
            </div>


            <div class="rounded-lg bg-amber-50 p-4">
                <p class="text-xs text-amber-600">
                    Kelebihan Hari
                </p>

                <p class="mt-1 text-xl font-bold text-amber-700">
                    {{ $leaveRequest->excess_days ?? 0 }} hari
                </p>
            </div>


            <div class="rounded-lg bg-red-50 p-4">
                <p class="text-xs text-red-600">
                    Unpaid
                </p>

                <p class="mt-1 text-xl font-bold text-red-700">
                    {{ $leaveRequest->unpaid_days ?? 0 }} hari
                </p>
            </div>
        </div>
    </div>


    {{-- PENGGANTI --}}
    @if(
    $leaveRequest->substituteSchedules
    && $leaveRequest->substituteSchedules->isNotEmpty()
    )

    <div
        class="overflow-hidden rounded-xl border
                   border-slate-200 bg-white shadow-sm">

        <div class="border-b border-slate-200 px-6 py-5">

            <h2 class="font-semibold text-slate-800">
                Jadwal Pengganti
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Periksa orang dan jadwal pengganti pada setiap tanggal.
            </p>
        </div>


        <div class="overflow-x-auto">

            <table class="min-w-full divide-y divide-slate-200">

                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                            Tanggal
                        </th>

                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                            Pengganti
                        </th>

                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                            Kontak
                        </th>

                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                            Biaya
                        </th>

                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                            Jadwal
                        </th>
                    </tr>
                </thead>


                <tbody class="divide-y divide-slate-100">

                    @foreach($leaveRequest->substituteSchedules as $schedule)

                    <tr>
                        <td class="px-5 py-4 text-sm text-slate-700">
                            {{ $schedule->schedule_date?->format('d/m/Y') ?? '-' }}
                        </td>

                        <td class="px-5 py-4">
                            <p class="text-sm font-medium text-slate-800">
                                {{
                                            $schedule->substitute_name
                                            ?? $leaveRequest->substitute_name
                                            ?? '-'
                                        }}
                            </p>
                        </td>

                        <td class="px-5 py-4 text-sm text-slate-700">
                            {{
                                        $schedule->substitute_whatsapp
                                        ?? $leaveRequest->substitute_whatsapp
                                        ?? '-'
                                    }}
                        </td>

                        <td class="px-5 py-4 text-sm text-slate-700">
                            {{
                                        match($schedule->cost_bearer ?? null) {
                                            'company' => 'Perusahaan',
                                            'employee' => 'Pemohon',
                                            default => '-',
                                        }
                                    }}
                        </td>

                        <td class="px-5 py-4 text-sm text-slate-700">

                            @if(($schedule->replacement_mode ?? null) === 'full_shift')

                            Full Shift
                            @if($schedule->workShift)
                            • {{ $schedule->workShift->name }}
                            @endif

                            @elseif(($schedule->replacement_mode ?? null) === 'specific_hours')

                            Beberapa Jam

                            @if($schedule->start_time && $schedule->end_time)
                            •
                            {{ substr($schedule->start_time, 0, 5) }}
                            -
                            {{ substr($schedule->end_time, 0, 5) }}
                            @endif

                            @else

                            -

                            @endif
                        </td>
                    </tr>

                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @endif


    {{-- DOKUMEN --}}
    @if($leaveRequest->supporting_document)

    <div
        class="rounded-xl border border-slate-200
                   bg-white p-6 shadow-sm">

        <h2 class="font-semibold text-slate-800">
            Dokumen Pendukung
        </h2>

        <p class="mt-1 text-sm text-slate-500">
            Buka dokumen pada tab baru untuk memeriksa bukti yang dilampirkan.
        </p>

        <a
            href="{{ asset(
                    'storage/' . $leaveRequest->supporting_document
                ) }}"
            target="_blank"
            rel="noopener"
            class="mt-4 inline-flex rounded-lg bg-blue-50
                       px-4 py-2.5 text-sm font-semibold text-blue-700
                       hover:bg-blue-100">
            Buka Dokumen
        </a>
    </div>

    @endif


    {{-- HASIL REVIEW KABID --}}
    @if($kabidRejected)

    <div
        class="rounded-xl border border-red-200
                   bg-red-50 p-5">

        <p class="font-semibold text-red-800">
            Pengajuan Ditolak Kabid
        </p>

        <p class="mt-2 whitespace-pre-line text-sm leading-6 text-red-700">
            {{ $leaveRequest->kabid_rejection_reason }}
        </p>
    </div>

    @elseif($kabidApproved)

    <div
        class="rounded-xl border border-emerald-200
                   bg-emerald-50 p-5">

        <p class="font-semibold text-emerald-800">
            Sudah Disetujui Kabid
        </p>

        <p class="mt-1 text-sm text-emerald-700">
            Pengajuan telah diteruskan dan sedang menunggu keputusan final Administrator.
        </p>
    </div>

    @endif


    {{-- ACTION --}}
    @if($canReview)

    <div
        class="rounded-xl border border-slate-200
                   bg-white p-6 shadow-sm">

        <div>
            <h2 class="font-semibold text-slate-800">
                Keputusan Kabid
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Pastikan data sudah diperiksa sebelum menyetujui atau menolak.
            </p>
        </div>


        <div class="mt-5 grid gap-5 lg:grid-cols-2">

            {{-- APPROVE --}}
            <div
                class="rounded-xl border border-emerald-200
                           bg-emerald-50 p-5">

                <h3 class="font-semibold text-emerald-800">
                    Setujui Pengajuan
                </h3>

                <p class="mt-1 text-sm leading-6 text-emerald-700">
                    Pengajuan akan diteruskan ke Administrator.
                    Saldo cuti belum dipotong pada tahap ini.
                </p>


                <form
                    method="POST"
                    action="{{ route(
                            'kabid.leave-approvals.approve',
                            $leaveRequest
                        ) }}"
                    class="mt-4"
                    data-confirm
                    data-confirm-tone="success"
                    data-confirm-title="Setujui Pengajuan?"
                    data-confirm-message="Pengajuan akan diteruskan ke Administrator untuk keputusan final."
                    data-confirm-button="Ya, Setujui">

                    @csrf
                    @method('PUT')


                    <button
                        type="submit"
                        class="w-full rounded-lg bg-emerald-600
                                   px-4 py-3 text-sm font-semibold
                                   text-white hover:bg-emerald-700">
                        Setujui & Teruskan ke Admin
                    </button>
                </form>
            </div>


            {{-- REJECT --}}
            <div
                class="rounded-xl border border-red-200
                           bg-red-50 p-5">

                <h3 class="font-semibold text-red-800">
                    Tolak Pengajuan
                </h3>

                <p class="mt-1 text-sm leading-6 text-red-700">
                    Penolakan Kabid bersifat final.
                    Pengajuan tidak diteruskan ke Administrator.
                </p>


                <form
                    method="POST"
                    action="{{ route(
                            'kabid.leave-approvals.reject',
                            $leaveRequest
                        ) }}"
                    class="mt-4"
                    data-confirm
                    data-confirm-tone="danger"
                    data-confirm-title="Tolak Pengajuan?"
                    data-confirm-message="Pengajuan akan ditolak dan tidak diteruskan ke Administrator."
                    data-confirm-button="Ya, Tolak">

                    @csrf
                    @method('PUT')


                    <label
                        for="kabidRejectionReason"
                        class="mb-2 block text-sm font-semibold
                                   text-red-800">
                        Alasan Penolakan
                        <span class="text-red-500">*</span>
                    </label>

                    <textarea
                        id="kabidRejectionReason"
                        name="kabid_rejection_reason"
                        rows="4"
                        required
                        minlength="5"
                        maxlength="2000"
                        placeholder="Tuliskan alasan penolakan..."
                        class="w-full rounded-lg border-red-200
                                   bg-white text-sm
                                   focus:border-red-500
                                   focus:ring-red-500">{{ old('kabid_rejection_reason') }}</textarea>


                    @error('kabid_rejection_reason')
                    <p class="mt-2 text-xs text-red-600">
                        {{ $message }}
                    </p>
                    @enderror


                    <button
                        type="submit"
                        class="mt-4 w-full rounded-lg bg-red-600
                                   px-4 py-3 text-sm font-semibold
                                   text-white hover:bg-red-700">
                        Tolak Pengajuan
                    </button>
                </form>
            </div>
        </div>
    </div>

    @endif
</div>

@endsection