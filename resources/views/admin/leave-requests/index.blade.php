@extends('layouts.admin')

@section('title', 'Pengajuan Cuti')
@section('page-title', 'Pengajuan Cuti')

@section('content')

<x-toast-notification />

<div class="space-y-6">

    {{-- ============================================================
        HEADER
    ============================================================ --}}
    <div>
        <h1 class="text-2xl font-bold text-slate-800">
            Pengajuan Cuti
        </h1>

        <p class="mt-1 text-sm text-slate-500">
            Kelola keputusan final setelah proses persetujuan Kabid selesai.
        </p>
    </div>


    {{-- ============================================================
        WORKFLOW INFO
    ============================================================ --}}
    <div
        class="rounded-xl border border-slate-200
               bg-white p-5 shadow-sm">

        <div class="grid gap-3 sm:grid-cols-3">

            <div
                class="rounded-lg border border-slate-200
                       bg-slate-50 p-4">

                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                    Tahap 1
                </p>

                <p class="mt-1 font-semibold text-slate-700">
                    Pegawai Mengajukan
                </p>
            </div>


            <div
                class="rounded-lg border border-amber-200
                       bg-amber-50 p-4">

                <p class="text-xs font-semibold uppercase tracking-wide text-amber-500">
                    Tahap 2
                </p>

                <p class="mt-1 font-semibold text-amber-800">
                    Kabid Memeriksa
                </p>
            </div>


            <div
                class="rounded-lg border border-blue-200
                       bg-blue-50 p-4">

                <p class="text-xs font-semibold uppercase tracking-wide text-blue-500">
                    Tahap 3
                </p>

                <p class="mt-1 font-semibold text-blue-800">
                    Admin Keputusan Final
                </p>
            </div>
        </div>
    </div>


    {{-- ============================================================
        STATISTICS
    ============================================================ --}}
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">

        <a
            href="{{ route(
                'admin.leave-requests.index',
                ['workflow' => 'ready_admin']
            ) }}"
            class="rounded-xl border border-blue-200
                   bg-blue-50 p-5 shadow-sm transition
                   hover:border-blue-300">

            <p class="text-sm font-medium text-blue-700">
                Siap Diproses Admin
            </p>

            <p class="mt-2 text-3xl font-bold text-blue-700">
                {{ $readyAdminCount }}
            </p>

            <p class="mt-1 text-xs text-blue-600">
                Sudah ACC Kabid / tidak perlu Kabid.
            </p>
        </a>


        <a
            href="{{ route(
                'admin.leave-requests.index',
                ['workflow' => 'waiting_kabid']
            ) }}"
            class="rounded-xl border border-amber-200
                   bg-amber-50 p-5 shadow-sm transition
                   hover:border-amber-300">

            <p class="text-sm font-medium text-amber-700">
                Menunggu Kabid
            </p>

            <p class="mt-2 text-3xl font-bold text-amber-600">
                {{ $waitingKabidCount }}
            </p>

            <p class="mt-1 text-xs text-amber-600">
                Belum menjadi pekerjaan Admin.
            </p>
        </a>


        <a
            href="{{ route(
                'admin.leave-requests.index',
                ['workflow' => 'approved']
            ) }}"
            class="rounded-xl border border-emerald-200
                   bg-emerald-50 p-5 shadow-sm transition
                   hover:border-emerald-300">

            <p class="text-sm font-medium text-emerald-700">
                Disetujui Final
            </p>

            <p class="mt-2 text-3xl font-bold text-emerald-600">
                {{ $approvedCount }}
            </p>
        </a>


        <a
            href="{{ route(
                'admin.leave-requests.index',
                ['workflow' => 'rejected']
            ) }}"
            class="rounded-xl border border-red-200
                   bg-red-50 p-5 shadow-sm transition
                   hover:border-red-300">

            <p class="text-sm font-medium text-red-700">
                Ditolak
            </p>

            <p class="mt-2 text-3xl font-bold text-red-600">
                {{ $rejectedCount }}
            </p>
        </a>
    </div>


    {{-- ============================================================
        FILTER
    ============================================================ --}}
    <div
        class="overflow-hidden rounded-xl border
               border-slate-200 bg-white shadow-sm">

        <div class="border-b border-slate-200 p-5">

            <form
                method="GET"
                action="{{ route('admin.leave-requests.index') }}"
                class="grid gap-3 lg:grid-cols-[1fr_260px_auto_auto]">

                <div>
                    <label
                        for="search"
                        class="mb-1 block text-xs font-semibold
                               uppercase tracking-wide text-slate-500">
                        Cari
                    </label>

                    <input
                        id="search"
                        type="text"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Nama, NIK, email, jenis izin, alasan..."
                        class="w-full rounded-lg border-slate-300
                               text-sm focus:border-blue-500
                               focus:ring-blue-500">
                </div>


                <div>
                    <label
                        for="workflow"
                        class="mb-1 block text-xs font-semibold
                               uppercase tracking-wide text-slate-500">
                        Tahap Pengajuan
                    </label>

                    <select
                        id="workflow"
                        name="workflow"
                        class="w-full rounded-lg border-slate-300
                               text-sm focus:border-blue-500
                               focus:ring-blue-500">

                        <option value="">
                            Semua Pengajuan
                        </option>

                        <option
                            value="ready_admin"
                            @selected($workflow==='ready_admin' )>
                            Siap Diproses Admin
                        </option>

                        <option
                            value="waiting_kabid"
                            @selected($workflow==='waiting_kabid' )>
                            Menunggu Kabid
                        </option>

                        <option
                            value="approved"
                            @selected($workflow==='approved' )>
                            Disetujui Final
                        </option>

                        <option
                            value="rejected"
                            @selected($workflow==='rejected' )>
                            Ditolak
                        </option>
                    </select>
                </div>


                <div class="flex items-end">

                    <button
                        type="submit"
                        class="w-full rounded-lg bg-slate-800
                               px-4 py-2.5 text-sm font-semibold
                               text-white hover:bg-slate-700">
                        Tampilkan
                    </button>
                </div>


                <div class="flex items-end">

                    @if($search !== '' || $workflow)

                    <a
                        href="{{ route('admin.leave-requests.index') }}"
                        class="w-full rounded-lg border
                                   border-slate-300 bg-white
                                   px-4 py-2.5 text-center
                                   text-sm font-medium text-slate-600
                                   hover:bg-slate-50">
                        Reset
                    </a>

                    @endif
                </div>
            </form>
        </div>


        {{-- ============================================================
            TABLE
        ============================================================ --}}
        <div class="overflow-x-auto">

            <table class="min-w-full divide-y divide-slate-200">

                <thead class="bg-slate-50">
                    <tr>
                        <th
                            class="px-5 py-3 text-left text-xs
                                   font-semibold uppercase
                                   tracking-wide text-slate-500">
                            Pengaju
                        </th>

                        <th
                            class="px-5 py-3 text-left text-xs
                                   font-semibold uppercase
                                   tracking-wide text-slate-500">
                            Perizinan
                        </th>

                        <th
                            class="px-5 py-3 text-left text-xs
                                   font-semibold uppercase
                                   tracking-wide text-slate-500">
                            Tahap Kabid
                        </th>

                        <th
                            class="px-5 py-3 text-left text-xs
                                   font-semibold uppercase
                                   tracking-wide text-slate-500">
                            Tahap Admin
                        </th>

                        <th
                            class="px-5 py-3 text-right text-xs
                                   font-semibold uppercase
                                   tracking-wide text-slate-500">
                            Aksi
                        </th>
                    </tr>
                </thead>


                <tbody class="divide-y divide-slate-100 bg-white">

                    @forelse($leaveRequests as $leave)

                    @php
                    $isKaryawan =
                    $leave->user?->role === 'karyawan';

                    $waitingKabid =
                    $leave->status === 'pending'
                    && $isKaryawan
                    && $leave->kabid_status
                    === \App\Models\LeaveRequest::KABID_STATUS_PENDING;

                    $readyAdmin =
                    $leave->status === 'pending'
                    && in_array(
                    $leave->kabid_status,
                    [
                    \App\Models\LeaveRequest::KABID_STATUS_APPROVED,
                    \App\Models\LeaveRequest::KABID_STATUS_NOT_REQUIRED,
                    ],
                    true
                    );
                    @endphp

                    <tr
                        class="{{
                                $readyAdmin
                                    ? 'bg-blue-50/40'
                                    : (
                                        $waitingKabid
                                            ? 'bg-amber-50/30'
                                            : ''
                                    )
                            }}">

                        {{-- PENGAJU --}}
                        <td class="px-5 py-4">

                            <p class="font-semibold text-slate-800">
                                {{ $leave->user?->name ?? '-' }}
                            </p>

                            <p class="mt-1 text-xs text-slate-500">
                                {{ $leave->user?->nik ?? '-' }}
                                •
                                {{ $leave->user?->department?->name ?? '-' }}
                            </p>

                            <span
                                class="mt-2 inline-flex rounded-full
                                           bg-slate-100 px-2 py-0.5
                                           text-[11px] font-medium
                                           text-slate-600">
                                {{
                                        $leave->user?->role === 'kabid'
                                            ? 'Kabid'
                                            : 'Karyawan'
                                    }}
                            </span>
                        </td>


                        {{-- PERIZINAN --}}
                        <td class="px-5 py-4">

                            <p class="text-sm font-semibold text-slate-800">
                                {{ $leave->permissionType?->name ?? '-' }}
                            </p>

                            <p class="mt-1 text-sm text-slate-600">
                                {{ $leave->start_date?->format('d/m/Y') }}
                                -
                                {{ $leave->end_date?->format('d/m/Y') }}
                            </p>

                            <p class="mt-1 text-xs text-slate-500">
                                {{ $leave->total_days }} hari
                                @if($leave->annual_leave_deducted_days > 0)
                                • memakai
                                {{ $leave->annual_leave_deducted_days }}
                                hari cuti tahunan
                                @endif
                            </p>
                        </td>


                        {{-- TAHAP KABID --}}
                        <td class="px-5 py-4">

                            @if(
                            $leave->kabid_status
                            === \App\Models\LeaveRequest::KABID_STATUS_PENDING
                            )

                            <span
                                class="inline-flex rounded-full
                                               bg-amber-50 px-2.5 py-1
                                               text-xs font-semibold
                                               text-amber-700">
                                Menunggu Kabid
                            </span>

                            @elseif(
                            $leave->kabid_status
                            === \App\Models\LeaveRequest::KABID_STATUS_APPROVED
                            )

                            <span
                                class="inline-flex rounded-full
                                               bg-emerald-50 px-2.5 py-1
                                               text-xs font-semibold
                                               text-emerald-700">
                                ✓ ACC Kabid
                            </span>

                            <p class="mt-2 text-xs text-slate-500">
                                {{ $leave->kabidReviewer?->name ?? 'Kabid' }}
                            </p>

                            @if($leave->kabid_reviewed_at)
                            <p class="text-[11px] text-slate-400">
                                {{ $leave->kabid_reviewed_at->format('d/m/Y H:i') }}
                            </p>
                            @endif

                            @elseif(
                            $leave->kabid_status
                            === \App\Models\LeaveRequest::KABID_STATUS_REJECTED
                            )

                            <span
                                class="inline-flex rounded-full
                                               bg-red-50 px-2.5 py-1
                                               text-xs font-semibold
                                               text-red-700">
                                ✕ Ditolak Kabid
                            </span>

                            @else

                            <span
                                class="inline-flex rounded-full
                                               bg-slate-100 px-2.5 py-1
                                               text-xs font-semibold
                                               text-slate-600">
                                Tidak Diperlukan
                            </span>

                            @if($leave->user?->role === 'kabid')
                            <p class="mt-2 text-xs text-slate-400">
                                Cuti milik Kabid.
                            </p>
                            @endif

                            @endif
                        </td>


                        {{-- TAHAP ADMIN --}}
                        <td class="px-5 py-4">

                            @if($readyAdmin)

                            <span
                                class="inline-flex rounded-full
                                               bg-blue-50 px-2.5 py-1
                                               text-xs font-semibold
                                               text-blue-700">
                                ● Siap Diproses Admin
                            </span>

                            @elseif($waitingKabid)

                            <span
                                class="inline-flex rounded-full
                                               bg-slate-100 px-2.5 py-1
                                               text-xs font-semibold
                                               text-slate-500">
                                🔒 Belum Tersedia
                            </span>

                            @elseif($leave->status === 'approved')

                            <span
                                class="inline-flex rounded-full
                                               bg-emerald-50 px-2.5 py-1
                                               text-xs font-semibold
                                               text-emerald-700">
                                ✓ Disetujui Final
                            </span>

                            @elseif($leave->status === 'rejected')

                            <span
                                class="inline-flex rounded-full
                                               bg-red-50 px-2.5 py-1
                                               text-xs font-semibold
                                               text-red-700">
                                ✕ Ditolak
                            </span>

                            @else

                            <span
                                class="text-xs text-slate-500">
                                {{ ucfirst($leave->status) }}
                            </span>

                            @endif
                        </td>


                        {{-- AKSI --}}
                        <td class="px-5 py-4 text-right">

                            <a
                                href="{{ route(
                                        'admin.leave-requests.show',
                                        $leave
                                    ) }}"
                                class="
                                        inline-flex rounded-lg px-3 py-2
                                        text-xs font-semibold transition
                                        {{
                                            $readyAdmin
                                                ? 'bg-blue-600 text-white hover:bg-blue-700'
                                                : 'bg-slate-100 text-slate-700 hover:bg-slate-200'
                                        }}
                                    ">

                                {{ $readyAdmin
                                        ? 'Proses'
                                        : 'Detail'
                                    }}
                            </a>
                        </td>
                    </tr>

                    @empty

                    <tr>
                        <td
                            colspan="5"
                            class="px-5 py-12 text-center
                                       text-sm text-slate-500">
                            Tidak ada pengajuan yang sesuai dengan filter.
                        </td>
                    </tr>

                    @endforelse
                </tbody>
            </table>
        </div>


        @if($leaveRequests->hasPages())

        <div class="border-t border-slate-200 p-5">
            {{ $leaveRequests->links() }}
        </div>

        @endif
    </div>
</div>

@endsection