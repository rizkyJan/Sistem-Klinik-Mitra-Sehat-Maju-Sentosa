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