@extends('layouts.karyawan')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

<div class="space-y-6">

    {{-- HEADER --}}
    <div
        class="rounded-xl border border-slate-200
               bg-white p-6 shadow-sm">

        <p class="text-sm text-slate-500">
            Selamat datang,
        </p>

        <h1 class="mt-1 text-2xl font-bold text-slate-800">
            {{ $user->name }}
        </h1>

        <p class="mt-2 text-sm text-slate-500">
            {{ $user->department?->name ?? 'Bidang belum diatur' }}
            @if($user->nik)
            • NIK {{ $user->nik }}
            @endif
        </p>
    </div>


    {{-- STATUS CUTI --}}
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">

        <a
            href="{{ route('karyawan.leave-requests.index') }}"
            class="rounded-xl border border-amber-200
                   bg-amber-50 p-5 shadow-sm transition
                   hover:border-amber-300">

            <p class="text-sm font-medium text-amber-700">
                Menunggu Kabid
            </p>

            <p class="mt-2 text-3xl font-bold text-amber-600">
                {{ $waitingKabidCount }}
            </p>
        </a>


        <a
            href="{{ route('karyawan.leave-requests.index') }}"
            class="rounded-xl border border-blue-200
                   bg-blue-50 p-5 shadow-sm transition
                   hover:border-blue-300">

            <p class="text-sm font-medium text-blue-700">
                Menunggu Admin
            </p>

            <p class="mt-2 text-3xl font-bold text-blue-600">
                {{ $waitingAdminCount }}
            </p>
        </a>


        <div
            class="rounded-xl border border-emerald-200
                   bg-emerald-50 p-5 shadow-sm">

            <p class="text-sm font-medium text-emerald-700">
                Disetujui {{ $year }}
            </p>

            <p class="mt-2 text-3xl font-bold text-emerald-600">
                {{ $approvedLeaveCount }}
            </p>
        </div>


        <div
            class="rounded-xl border border-red-200
                   bg-red-50 p-5 shadow-sm">

            <p class="text-sm font-medium text-red-700">
                Ditolak {{ $year }}
            </p>

            <p class="mt-2 text-3xl font-bold text-red-600">
                {{ $rejectedLeaveCount }}
            </p>
        </div>
    </div>


    {{-- SURAT DINAS SAYA --}}
    <div class="space-y-4">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-lg font-bold text-slate-800">Surat Dinas Saya</h2>
                <p class="mt-1 text-sm text-slate-500">Pantau tugas, laporan, dan status fee dinas Anda.</p>
            </div>
            <a href="{{ route('karyawan.duty-letters.index') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-700">Lihat semua →</a>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <a href="{{ route('karyawan.duty-letters.index', ['filter' => 'upcoming']) }}" class="rounded-xl border border-blue-200 bg-blue-50 p-4 shadow-sm transition hover:border-blue-300">
                <p class="text-xs font-semibold uppercase tracking-wide text-blue-700">Mendatang</p>
                <p class="mt-2 text-2xl font-bold text-blue-800">{{ $dutyUpcomingCount }}</p>
            </a>

            <a href="{{ route('karyawan.duty-letters.index', ['filter' => 'report_pending']) }}" class="rounded-xl border border-amber-200 bg-amber-50 p-4 shadow-sm transition hover:border-amber-300">
                <p class="text-xs font-semibold uppercase tracking-wide text-amber-700">Laporan Belum Dibuat</p>
                <p class="mt-2 text-2xl font-bold text-amber-800">{{ $dutyWaitingReportCount }}</p>
            </a>

            <a href="{{ route('karyawan.duty-letters.index') }}" class="rounded-xl border border-violet-200 bg-violet-50 p-4 shadow-sm transition hover:border-violet-300">
                <p class="text-xs font-semibold uppercase tracking-wide text-violet-700">Menunggu Verifikasi</p>
                <p class="mt-2 text-2xl font-bold text-violet-800">{{ $dutyWaitingVerificationCount }}</p>
            </a>

            <a href="{{ route('karyawan.duty-letters.index', ['filter' => 'report_verified']) }}" class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 shadow-sm transition hover:border-emerald-300">
                <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700">Fee Belum Dibayar</p>
                <p class="mt-2 text-2xl font-bold text-emerald-800">{{ $dutyUnpaidFeeCount }}</p>
            </a>
        </div>

        @if($todayDutyAssignments->isNotEmpty())
        <div class="rounded-xl border border-blue-200 bg-blue-50 p-4 sm:p-5">
            <div class="flex items-start gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-blue-100 text-blue-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3M5 11h14M5 5h14a2 2 0 012 2v12a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2z" />
                    </svg>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="font-semibold text-blue-900">Surat Dinas Hari Ini</p>
                    <div class="mt-2 space-y-2">
                        @foreach($todayDutyAssignments as $assignment)
                        <a href="{{ route('karyawan.duty-letters.show', $assignment) }}" class="block rounded-lg bg-white/80 p-3 transition hover:bg-white">
                            <p class="break-words text-sm font-semibold text-slate-800">{{ $assignment->dutyLetter?->title }}</p>
                            <p class="mt-1 break-words text-xs text-slate-500">
                                {{ substr((string) $assignment->dutyLetter?->start_time, 0, 5) }} WIB
                                • {{ $assignment->dutyLetter?->location_name }}
                            </p>
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @endif

        <x-duty-notification-panel :notifications="$recentDutyNotifications" role="karyawan" />
    </div>

    <div class="grid gap-6 xl:grid-cols-3">

        {{-- SALDO CUTI --}}
        <div
            class="rounded-xl border border-slate-200
                   bg-white p-6 shadow-sm">

            <h2 class="font-semibold text-slate-800">
                Saldo Cuti {{ $year }}
            </h2>

            @if($leaveBalance)

            <p class="mt-4 text-4xl font-bold text-blue-600">
                {{ $availableAnnualLeave }}
                <span class="text-base font-medium text-slate-500">
                    hari tersedia
                </span>
            </p>

            <div class="mt-5 space-y-2 text-sm">

                <div class="flex justify-between gap-3">
                    <span class="text-slate-500">Jatah</span>
                    <span class="font-semibold text-slate-700">
                        {{ $leaveBalance->quota_days }} hari
                    </span>
                </div>

                <div class="flex justify-between gap-3">
                    <span class="text-slate-500">Sudah terpakai</span>
                    <span class="font-semibold text-slate-700">
                        {{ $leaveBalance->used_days }} hari
                    </span>
                </div>

                <div class="flex justify-between gap-3">
                    <span class="text-slate-500">Sedang diajukan</span>
                    <span class="font-semibold text-amber-600">
                        {{ $pendingAnnualDays }} hari
                    </span>
                </div>
            </div>

            @else

            <p class="mt-4 text-sm leading-6 text-slate-500">
                Jatah cuti tahun {{ $year }} belum tersedia.
            </p>

            @endif


            <a
                href="{{ route('karyawan.leave-requests.create') }}"
                class="mt-5 block rounded-lg bg-blue-600
                       px-4 py-2.5 text-center text-sm
                       font-semibold text-white hover:bg-blue-700">
                Ajukan Cuti
            </a>
        </div>


        {{-- REIMBURSE --}}
        <div
            class="rounded-xl border border-slate-200
                   bg-white p-6 shadow-sm">

            <h2 class="font-semibold text-slate-800">
                Reimburse Saya
            </h2>

            <div class="mt-4">

                <p class="text-sm text-slate-500">
                    Menunggu Admin
                </p>

                <p class="mt-1 text-3xl font-bold text-violet-600">
                    {{ $pendingReimbursementCount }}
                </p>
            </div>


            <div class="mt-5 border-t border-slate-100 pt-4">

                <p class="text-sm text-slate-500">
                    Total Sudah Dibayar
                </p>

                <p class="mt-1 text-xl font-bold text-slate-800">
                    Rp{{ number_format(
                        $paidReimbursementTotal,
                        0,
                        ',',
                        '.'
                    ) }}
                </p>
            </div>


            <div class="mt-5 grid grid-cols-2 gap-2">

                <a
                    href="{{ route('karyawan.reimbursements.create') }}"
                    class="rounded-lg bg-violet-600
                           px-3 py-2.5 text-center text-sm
                           font-semibold text-white
                           hover:bg-violet-700">
                    Ajukan
                </a>

                <a
                    href="{{ route('karyawan.reimbursements.index') }}"
                    class="rounded-lg border border-slate-300
                           px-3 py-2.5 text-center text-sm
                           font-semibold text-slate-600
                           hover:bg-slate-50">
                    Riwayat
                </a>
            </div>
        </div>


        {{-- ALUR --}}
        <div
            class="rounded-xl border border-slate-200
                   bg-white p-6 shadow-sm">

            <h2 class="font-semibold text-slate-800">
                Alur Persetujuan Cuti
            </h2>

            <div class="mt-5 space-y-3">

                <div
                    class="rounded-lg border border-slate-200
                           bg-slate-50 px-4 py-3">
                    <p class="text-xs text-slate-400">1</p>
                    <p class="font-medium text-slate-700">
                        Anda mengajukan
                    </p>
                </div>

                <div
                    class="rounded-lg border border-amber-200
                           bg-amber-50 px-4 py-3">
                    <p class="text-xs text-amber-500">2</p>
                    <p class="font-medium text-amber-700">
                        Kabid memeriksa
                    </p>
                </div>

                <div
                    class="rounded-lg border border-blue-200
                           bg-blue-50 px-4 py-3">
                    <p class="text-xs text-blue-500">3</p>
                    <p class="font-medium text-blue-700">
                        Admin keputusan final
                    </p>
                </div>
            </div>
        </div>
    </div>


    {{-- RIWAYAT TERBARU --}}
    <div
        class="overflow-hidden rounded-xl border
               border-slate-200 bg-white shadow-sm">

        <div
            class="flex items-center justify-between gap-3
                   border-b border-slate-200 px-5 py-4">

            <div>
                <h2 class="font-semibold text-slate-800">
                    Pengajuan Cuti Terbaru
                </h2>

                <p class="mt-1 text-xs text-slate-500">
                    Status terbaru dari pengajuan Anda.
                </p>
            </div>

            <a
                href="{{ route('karyawan.leave-requests.index') }}"
                class="text-sm font-semibold text-blue-600 hover:text-blue-700">
                Lihat semua
            </a>
        </div>


        <div class="divide-y divide-slate-100">

            @forelse($recentLeaveRequests as $leave)

            @php
            $waitingKabid =
            $leave->status === 'pending'
            &&
            $leave->kabid_status
            === \App\Models\LeaveRequest::KABID_STATUS_PENDING;

            $waitingAdmin =
            $leave->status === 'pending'
            &&
            in_array(
            $leave->kabid_status,
            [
            \App\Models\LeaveRequest::KABID_STATUS_APPROVED,
            \App\Models\LeaveRequest::KABID_STATUS_NOT_REQUIRED,
            ],
            true
            );

            $rejectedKabid =
            $leave->status === 'rejected'
            &&
            $leave->kabid_status
            === \App\Models\LeaveRequest::KABID_STATUS_REJECTED;
            @endphp

            <div class="p-5">

                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">

                    <div>
                        <p class="font-semibold text-slate-800">
                            {{ $leave->permissionType?->name ?? 'Perizinan' }}
                        </p>

                        <p class="mt-1 text-sm text-slate-500">
                            {{ $leave->start_date?->format('d/m/Y') }}
                            -
                            {{ $leave->end_date?->format('d/m/Y') }}
                            • {{ $leave->total_days }} hari
                        </p>
                    </div>


                    @if($waitingKabid)

                    <span
                        class="w-fit rounded-full bg-amber-50
                                       px-2.5 py-1 text-xs font-semibold
                                       text-amber-700">
                        Menunggu Kabid
                    </span>

                    @elseif($waitingAdmin)

                    <span
                        class="w-fit rounded-full bg-blue-50
                                       px-2.5 py-1 text-xs font-semibold
                                       text-blue-700">
                        Menunggu Admin
                    </span>

                    @elseif($leave->status === 'approved')

                    <span
                        class="w-fit rounded-full bg-emerald-50
                                       px-2.5 py-1 text-xs font-semibold
                                       text-emerald-700">
                        ✓ Disetujui
                    </span>

                    @elseif($rejectedKabid)

                    <span
                        class="w-fit rounded-full bg-red-50
                                       px-2.5 py-1 text-xs font-semibold
                                       text-red-700">
                        Ditolak Kabid
                    </span>

                    @else

                    <span
                        class="w-fit rounded-full bg-red-50
                                       px-2.5 py-1 text-xs font-semibold
                                       text-red-700">
                        Ditolak Admin
                    </span>

                    @endif
                </div>
            </div>

            @empty

            <div class="p-8 text-center text-sm text-slate-500">
                Belum ada pengajuan cuti/perizinan.
            </div>

            @endforelse
        </div>
    </div>
</div>

@endsection