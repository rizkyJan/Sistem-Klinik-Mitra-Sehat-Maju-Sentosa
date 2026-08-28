@extends('layouts.admin')

@section('title', 'Pengajuan Cuti')

@section('page-title', 'Pengajuan Cuti')

@section('content')

<div class="space-y-6">

    <div>

        <h1 class="text-2xl font-bold text-slate-800">
            Pengajuan Cuti
        </h1>

        <p class="mt-1 text-sm text-slate-500">
            Kelola pengajuan cuti karyawan.
        </p>

    </div>


    @foreach(['success', 'error'] as $message)

    @if(session($message))

    <div class="rounded-lg border px-4 py-3 text-sm">
        {{ session($message) }}
    </div>

    @endif

    @endforeach


    {{-- Statistik --}}
    <div class="grid gap-5 sm:grid-cols-3">

        <div class="rounded-xl border bg-white p-5">

            <p class="text-sm text-slate-500">
                Menunggu
            </p>

            <p class="mt-2 text-3xl font-bold text-amber-600">
                {{ $pendingCount }}
            </p>

        </div>


        <div class="rounded-xl border bg-white p-5">

            <p class="text-sm text-slate-500">
                Disetujui
            </p>

            <p class="mt-2 text-3xl font-bold text-emerald-600">
                {{ $approvedCount }}
            </p>

        </div>


        <div class="rounded-xl border bg-white p-5">

            <p class="text-sm text-slate-500">
                Ditolak
            </p>

            <p class="mt-2 text-3xl font-bold text-red-600">
                {{ $rejectedCount }}
            </p>

        </div>

    </div>


    {{-- Filter --}}
    <div class="rounded-xl border bg-white">

        <div class="border-b p-5">

            <form
                method="GET"
                class="flex flex-col gap-3 sm:flex-row">

                <input
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari nama / NIK..."
                    class="rounded-lg border-slate-300">


                <select
                    name="status"
                    class="rounded-lg border-slate-300">

                    <option value="">
                        Semua Status
                    </option>

                    <option
                        value="pending"
                        @selected(request('status')==='pending' )>
                        Pending
                    </option>

                    <option
                        value="approved"
                        @selected(request('status')==='approved' )>
                        Disetujui
                    </option>

                    <option
                        value="rejected"
                        @selected(request('status')==='rejected' )>
                        Ditolak
                    </option>

                </select>


                <button
                    class="rounded-lg bg-slate-800
                           px-4 py-2 text-sm
                           text-white">
                    Tampilkan
                </button>

            </form>

        </div>


        <div class="overflow-x-auto">

            <table class="min-w-full divide-y">

                <thead class="bg-slate-50">

                    <tr>

                        <th class="px-5 py-3 text-left text-xs">
                            Karyawan
                        </th>

                        <th class="px-5 py-3 text-left text-xs">
                            Cuti
                        </th>

                        <th class="px-5 py-3 text-left text-xs">
                            Pengganti
                        </th>

                        <th class="px-5 py-3 text-left text-xs">
                            Status
                        </th>

                        <th class="px-5 py-3 text-right text-xs">
                            Aksi
                        </th>

                    </tr>

                </thead>


                <tbody class="divide-y">

                    @forelse($leaveRequests as $leave)

                    <tr class="align-top">

                        <td class="px-5 py-4">

                            <p class="font-semibold">
                                {{ $leave->user->name }}
                            </p>

                            <p class="text-xs text-slate-500">
                                {{ $leave->user->nik }}
                            </p>

                            <p class="text-xs text-slate-500">
                                {{ $leave->user->department?->name }}
                            </p>

                        </td>


                        <td class="px-5 py-4 text-sm">

                            {{ $leave->start_date->format('d/m/Y') }}
                            -
                            {{ $leave->end_date->format('d/m/Y') }}

                            <p class="font-semibold">
                                {{ $leave->total_days }} hari
                            </p>

                            <p class="mt-2 max-w-xs text-xs text-slate-500">
                                {{ $leave->reason }}
                            </p>

                        </td>


                        <td class="px-5 py-4 text-sm">

                            @php
                            $hasSelfReplacement =
                            $leave->self_replacement_days > 0;

                            $firstSubstituteSchedule =
                            $leave->substituteSchedules->first();

                            $hasRecordedSubstitute =
                            $leave->has_substitute
                            && $firstSubstituteSchedule !== null;

                            $companyScheduleCount =
                            $leave->substituteSchedules
                            ->where('substitute_fee_payer', 'company')
                            ->count();

                            $employeeScheduleCount =
                            $leave->substituteSchedules
                            ->where('substitute_fee_payer', 'employee')
                            ->count();
                            @endphp

                            @if($hasSelfReplacement)
                            <div>
                                <span class="rounded-full bg-amber-50 px-2.5 py-1 text-xs font-medium text-amber-700">
                                    Pengganti Mandiri
                                </span>

                                <p class="mt-2 font-semibold text-slate-700">
                                    {{ $leave->self_replacement_days }} hari
                                </p>

                                <p class="text-xs text-slate-500">
                                    Biaya ditanggung pemohon
                                </p>
                            </div>
                            @endif

                            @if($hasRecordedSubstitute)
                            <div class="{{ $hasSelfReplacement ? 'mt-3 border-t border-slate-100 pt-3' : '' }}">
                                <p class="font-semibold text-slate-700">
                                    {{ $leave->substitute_name ?? '-' }}
                                </p>

                                <p class="text-xs text-slate-500">
                                    {{ $leave->substitute_whatsapp ?? '-' }}
                                </p>

                                <p class="mt-1 text-xs text-slate-500">
                                    {{ $leave->substituteSchedules->count() }}
                                    jadwal tercatat
                                </p>

                                @if($companyScheduleCount > 0)
                                <p class="mt-1 text-xs font-medium text-blue-600">
                                    Perusahaan: {{ $companyScheduleCount }} hari
                                </p>
                                @endif

                                @if($employeeScheduleCount > 0)
                                <p class="mt-1 text-xs font-medium text-amber-600">
                                    Pemohon tercatat: {{ $employeeScheduleCount }} hari
                                </p>
                                @endif
                            </div>
                            @endif

                            @if(! $hasSelfReplacement && ! $hasRecordedSubstitute)
                            <span class="text-slate-400">-</span>
                            @endif

                        </td>


                        <td class="px-5 py-4">

                            <span class="text-sm capitalize">
                                {{ $leave->status }}
                            </span>

                        </td>


                        <td class="px-5 py-4 text-right">

                            <div class="flex justify-end">

                                <a
                                    href="{{ route(
                'admin.leave-requests.show',
                $leave
            ) }}"
                                    class="inline-flex items-center
                   gap-2 rounded-lg bg-blue-50
                   px-3 py-2 text-xs
                   font-medium text-blue-700
                   transition hover:bg-blue-100">

                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-4 w-4"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="1.5"
                                            d="M15 12a3 3 0 11-6 0
                       3 3 0 016 0z
                       M2.458 12C3.732 7.943
                       7.523 5 12 5
                       c4.478 0 8.268 2.943
                       9.542 7
                       -1.274 4.057
                       -5.064 7
                       -9.542 7
                       -4.477 0
                       -8.268-2.943
                       -9.542-7z" />
                                    </svg>

                                    Detail

                                </a>

                            </div>

                        </td>

                    </tr>

                    @empty

                    <tr>

                        <td
                            colspan="5"
                            class="px-5 py-12
                                       text-center text-slate-500">
                            Belum ada pengajuan.
                        </td>

                    </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        @if($leaveRequests->hasPages())

        <div class="border-t p-5">
            {{ $leaveRequests->links() }}
        </div>

        @endif

    </div>

</div>

@endsection