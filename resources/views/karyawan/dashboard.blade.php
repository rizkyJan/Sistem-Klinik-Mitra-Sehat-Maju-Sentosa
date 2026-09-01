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