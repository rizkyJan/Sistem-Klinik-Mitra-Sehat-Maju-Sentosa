@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

<div class="space-y-6">

    {{-- HEADER --}}
    <div>
        <h1 class="text-2xl font-bold text-slate-800">
            Dashboard Administrator
        </h1>

        <p class="mt-1 text-sm text-slate-500">
            Ringkasan pekerjaan yang benar-benar membutuhkan tindakan Administrator.
        </p>
    </div>


    {{-- PEKERJAAN UTAMA --}}
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">

        <a
            href="{{ route(
                'admin.leave-requests.index',
                ['workflow' => 'ready_admin']
            ) }}"
            class="rounded-xl border border-blue-200 bg-blue-50
                   p-5 shadow-sm transition hover:border-blue-300">

            <p class="text-sm font-medium text-blue-700">
                Cuti Siap Diproses
            </p>

            <p class="mt-2 text-3xl font-bold text-blue-700">
                {{ $readyAdminLeaveCount }}
            </p>

            <p class="mt-1 text-xs text-blue-600">
                Sudah lolos Kabid / tidak memerlukan Kabid.
            </p>
        </a>


        <a
            href="{{ route(
                'admin.leave-requests.index',
                ['workflow' => 'waiting_kabid']
            ) }}"
            class="rounded-xl border border-amber-200 bg-amber-50
                   p-5 shadow-sm transition hover:border-amber-300">

            <p class="text-sm font-medium text-amber-700">
                Masih di Kabid
            </p>

            <p class="mt-2 text-3xl font-bold text-amber-600">
                {{ $waitingKabidLeaveCount }}
            </p>

            <p class="mt-1 text-xs text-amber-600">
                Hanya informasi, belum menjadi pekerjaan Admin.
            </p>
        </a>


        <a
            href="{{ route(
                'admin.reimbursements.index',
                ['status' => 'pending']
            ) }}"
            class="rounded-xl border border-violet-200 bg-violet-50
                   p-5 shadow-sm transition hover:border-violet-300">

            <p class="text-sm font-medium text-violet-700">
                Reimburse Menunggu
            </p>

            <p class="mt-2 text-3xl font-bold text-violet-700">
                {{ $pendingReimbursementCount }}
            </p>

            <p class="mt-1 text-xs text-violet-600">
                Pengajuan keuangan yang belum diperiksa.
            </p>
        </a>


        <div
            class="rounded-xl border border-slate-200 bg-white
                   p-5 shadow-sm">

            <p class="text-sm font-medium text-slate-600">
                Verifikasi Pegawai
            </p>

            <p class="mt-2 text-3xl font-bold text-slate-800">
                {{
                    $pendingKaryawanVerificationCount
                    + $pendingKabidVerificationCount
                }}
            </p>

            <p class="mt-1 text-xs text-slate-500">
                Karyawan {{ $pendingKaryawanVerificationCount }}
                • Kabid {{ $pendingKabidVerificationCount }}
            </p>
        </div>
    </div>


    {{-- SURAT DINAS --}}
    <div class="space-y-4">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-lg font-bold text-slate-800">Surat Dinas</h2>
                <p class="mt-1 text-sm text-slate-500">Ringkasan penugasan, laporan, dan pembayaran fee dinas.</p>
            </div>

            <a href="{{ route('admin.duty-letters.index') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-700">
                Kelola Surat Dinas →
            </a>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
            <a href="{{ route('admin.duty-letters.index') }}" class="rounded-xl border border-blue-200 bg-blue-50 p-4 shadow-sm transition hover:border-blue-300">
                <p class="text-xs font-semibold uppercase tracking-wide text-blue-700">Aktif / Mendatang</p>
                <p class="mt-2 text-2xl font-bold text-blue-800">{{ $dutyActiveCount }}</p>
            </a>

            <a href="{{ route('admin.duty-letters.index') }}" class="rounded-xl border border-amber-200 bg-amber-50 p-4 shadow-sm transition hover:border-amber-300">
                <p class="text-xs font-semibold uppercase tracking-wide text-amber-700">Menunggu Laporan</p>
                <p class="mt-2 text-2xl font-bold text-amber-800">{{ $dutyWaitingReportCount }}</p>
            </a>

            <a href="{{ route('admin.duty-letters.index') }}" class="rounded-xl border border-violet-200 bg-violet-50 p-4 shadow-sm transition hover:border-violet-300">
                <p class="text-xs font-semibold uppercase tracking-wide text-violet-700">Perlu Verifikasi</p>
                <p class="mt-2 text-2xl font-bold text-violet-800">{{ $dutyPendingVerificationCount }}</p>
            </a>

            <a href="{{ route('admin.duty-letters.index') }}" class="rounded-xl border border-orange-200 bg-orange-50 p-4 shadow-sm transition hover:border-orange-300">
                <p class="text-xs font-semibold uppercase tracking-wide text-orange-700">Perlu Perbaikan</p>
                <p class="mt-2 text-2xl font-bold text-orange-800">{{ $dutyRevisionCount }}</p>
            </a>

            <a href="{{ route('admin.duty-letters.index') }}" class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 shadow-sm transition hover:border-emerald-300">
                <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700">Fee Belum Dibayar</p>
                <p class="mt-2 text-2xl font-bold text-emerald-800">{{ $dutyUnpaidFeeCount }}</p>
            </a>
        </div>

        <div class="grid gap-6 xl:grid-cols-2">
            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                <div class="flex items-center justify-between gap-3 border-b border-slate-200 px-4 py-4 sm:px-5">
                    <div>
                        <h3 class="font-semibold text-slate-800">Laporan Menunggu Verifikasi</h3>
                        <p class="mt-1 text-xs text-slate-500">Lima laporan terbaru yang perlu diperiksa.</p>
                    </div>
                    <span class="rounded-full bg-violet-50 px-2.5 py-1 text-xs font-semibold text-violet-700">{{ $dutyPendingVerificationCount }}</span>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse($pendingDutyReports as $assignment)
                    <a href="{{ route('admin.duty-reports.show', [$assignment->dutyLetter, $assignment]) }}" class="block px-4 py-4 transition hover:bg-slate-50 sm:px-5">
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                            <div class="min-w-0">
                                <p class="break-words text-sm font-semibold text-slate-800">{{ $assignment->assignee_name }}</p>
                                <p class="mt-1 break-words text-sm text-slate-600">{{ $assignment->dutyLetter?->title ?? 'Surat Dinas' }}</p>
                                <p class="mt-1 text-xs text-slate-400">{{ $assignment->report_submitted_at?->format('d/m/Y H:i') }}</p>
                            </div>
                            <span class="w-fit shrink-0 rounded-full bg-violet-50 px-2.5 py-1 text-xs font-semibold text-violet-700">Periksa</span>
                        </div>
                    </a>
                    @empty
                    <div class="px-5 py-10 text-center text-sm text-slate-500">Tidak ada laporan dinas yang menunggu verifikasi.</div>
                    @endforelse
                </div>
            </div>

            <x-duty-notification-panel :notifications="$recentDutyNotifications" role="admin" />
        </div>
    </div>

    {{-- PEGAWAI --}}
    <div class="grid gap-4 sm:grid-cols-2">

        <a
            href="{{ route('admin.karyawan.index') }}"
            class="rounded-xl border border-slate-200
                   bg-white p-5 shadow-sm transition hover:bg-slate-50">

            <p class="text-sm text-slate-500">
                Karyawan Aktif
            </p>

            <p class="mt-2 text-3xl font-bold text-slate-800">
                {{ $activeKaryawanCount }}
            </p>
        </a>


        <a
            href="{{ route('admin.kabid.index') }}"
            class="rounded-xl border border-slate-200
                   bg-white p-5 shadow-sm transition hover:bg-slate-50">

            <p class="text-sm text-slate-500">
                Kabid Aktif
            </p>

            <p class="mt-2 text-3xl font-bold text-slate-800">
                {{ $activeKabidCount }}
            </p>
        </a>
    </div>


    <div class="grid gap-6 xl:grid-cols-2">

        {{-- CUTI SIAP ADMIN --}}
        <div
            class="overflow-hidden rounded-xl border
                   border-slate-200 bg-white shadow-sm">

            <div
                class="flex items-center justify-between gap-3
                       border-b border-slate-200 px-5 py-4">

                <div>
                    <h2 class="font-semibold text-slate-800">
                        Perlu Keputusan Admin
                    </h2>

                    <p class="mt-1 text-xs text-slate-500">
                        Lima pengajuan cuti terbaru yang sudah siap diproses.
                    </p>
                </div>

                <a
                    href="{{ route(
                        'admin.leave-requests.index',
                        ['workflow' => 'ready_admin']
                    ) }}"
                    class="text-sm font-semibold text-blue-600 hover:text-blue-700">
                    Lihat semua
                </a>
            </div>


            <div class="divide-y divide-slate-100">

                @forelse($readyAdminLeaveRequests as $leave)

                <a
                    href="{{ route(
                            'admin.leave-requests.show',
                            $leave
                        ) }}"
                    class="block p-5 transition hover:bg-slate-50">

                    <div class="flex items-start justify-between gap-3">

                        <div>
                            <p class="font-semibold text-slate-800">
                                {{ $leave->user?->name ?? '-' }}
                            </p>

                            <p class="mt-1 text-sm text-slate-600">
                                {{ $leave->permissionType?->name ?? 'Perizinan' }}
                            </p>

                            <p class="mt-1 text-xs text-slate-500">
                                {{ $leave->start_date?->format('d/m/Y') }}
                                -
                                {{ $leave->end_date?->format('d/m/Y') }}
                                • {{ $leave->total_days }} hari
                            </p>
                        </div>


                        <span
                            class="shrink-0 rounded-full bg-blue-50
                                       px-2.5 py-1 text-xs font-semibold
                                       text-blue-700">
                            Proses
                        </span>
                    </div>


                    @if(
                    $leave->kabid_status
                    === \App\Models\LeaveRequest::KABID_STATUS_APPROVED
                    )

                    <p class="mt-3 text-xs text-emerald-600">
                        ✓ ACC {{ $leave->kabidReviewer?->name ?? 'Kabid' }}
                    </p>

                    @else

                    <p class="mt-3 text-xs text-slate-500">
                        Tahap Kabid tidak diperlukan.
                    </p>

                    @endif
                </a>

                @empty

                <div class="p-8 text-center text-sm text-slate-500">
                    Tidak ada pengajuan cuti yang menunggu keputusan Admin.
                </div>

                @endforelse
            </div>
        </div>


        {{-- VERIFIKASI USER --}}
        <div
            class="overflow-hidden rounded-xl border
                   border-slate-200 bg-white shadow-sm">

            <div class="border-b border-slate-200 px-5 py-4">

                <h2 class="font-semibold text-slate-800">
                    Menunggu Verifikasi Akun
                </h2>

                <p class="mt-1 text-xs text-slate-500">
                    Pendaftar Karyawan/Kabid terbaru.
                </p>
            </div>


            <div class="divide-y divide-slate-100">

                @forelse($pendingUsers as $pendingUser)

                <div class="p-5">

                    <div class="flex items-start justify-between gap-3">

                        <div>
                            <p class="font-semibold text-slate-800">
                                {{ $pendingUser->name }}
                            </p>

                            <p class="mt-1 text-xs text-slate-500">
                                {{ $pendingUser->department?->name ?? '-' }}
                            </p>
                        </div>


                        <span
                            class="{{
                                    $pendingUser->role === 'kabid'
                                        ? 'bg-violet-50 text-violet-700'
                                        : 'bg-blue-50 text-blue-700'
                                }} rounded-full px-2.5 py-1
                                   text-xs font-semibold">

                            {{
                                    $pendingUser->role === 'kabid'
                                        ? 'Kabid'
                                        : 'Karyawan'
                                }}
                        </span>
                    </div>


                    <a
                        href="{{
                                $pendingUser->role === 'kabid'
                                    ? route(
                                        'admin.kabid.index',
                                        ['approval_status' => 'pending']
                                    )
                                    : route(
                                        'admin.karyawan.index',
                                        ['approval_status' => 'pending']
                                    )
                            }}"
                        class="mt-3 inline-flex text-xs font-semibold
                                   text-blue-600 hover:text-blue-700">
                        Buka verifikasi →
                    </a>
                </div>

                @empty

                <div class="p-8 text-center text-sm text-slate-500">
                    Tidak ada akun yang menunggu verifikasi.
                </div>

                @endforelse
            </div>
        </div>
    </div>
</div>

@endsection