@extends('layouts.kabid')

@section('title', 'Riwayat Perizinan')

@section('page-title', 'Riwayat Perizinan')

@section('content')

<div class="space-y-6">

    <div
        class="flex flex-col gap-4
               sm:flex-row sm:items-center
               sm:justify-between">

        <div>

            <h1 class="text-2xl font-bold text-slate-800">
                Riwayat Perizinan
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Pantau status seluruh pengajuan perizinan Anda.
            </p>

        </div>


        <a
            href="{{ route(
                'kabid.leave-requests.create'
            ) }}"
            class="rounded-lg bg-blue-600
                   px-4 py-2.5 text-center
                   text-sm font-medium text-white
                   hover:bg-blue-700">
            + Ajukan Perizinan
        </a>

    </div>



    @foreach(
    ['success', 'error']
    as $message
    )

    @if(session($message))

    <div
        class="rounded-lg border
                       px-4 py-3 text-sm

                {{ $message === 'success'
                    ? 'border-emerald-200 bg-emerald-50 text-emerald-700'
                    : 'border-red-200 bg-red-50 text-red-700'
                }}">
        {{ session($message) }}
    </div>

    @endif

    @endforeach



    {{-- ============================================================
        ALUR CUTI KABID
    ============================================================ --}}
    <div
        class="rounded-xl border border-blue-200
               bg-blue-50 p-5">

        <div class="flex items-start gap-3">

            <div
                class="flex h-9 w-9 shrink-0
                       items-center justify-center
                       rounded-lg bg-blue-100
                       text-blue-700">

                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor">

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>


            <div>
                <p class="font-semibold text-blue-800">
                    Cuti Kabid langsung diproses Administrator
                </p>

                <p class="mt-1 text-sm leading-6 text-blue-700">
                    Pengajuan cuti pribadi Kabid tidak memerlukan persetujuan Kabid
                    lagi agar tidak terjadi persetujuan terhadap pengajuan sendiri.
                </p>
            </div>
        </div>
    </div>


    {{-- ============================================================
        RINGKASAN STATUS
    ============================================================ --}}
    <div class="grid gap-4 sm:grid-cols-3">

        <div
            class="rounded-xl border border-blue-200
                   bg-blue-50 p-5 shadow-sm">

            <p class="text-sm font-medium text-blue-700">
                Menunggu Admin
            </p>

            <p class="mt-2 text-3xl font-bold text-blue-600">
                {{ $waitingAdminCount }}
            </p>

            <p class="mt-1 text-xs text-blue-600">
                Sedang menunggu keputusan final.
            </p>
        </div>


        <div
            class="rounded-xl border border-emerald-200
                   bg-emerald-50 p-5 shadow-sm">

            <p class="text-sm font-medium text-emerald-700">
                Disetujui
            </p>

            <p class="mt-2 text-3xl font-bold text-emerald-600">
                {{ $approvedCount }}
            </p>

            <p class="mt-1 text-xs text-emerald-600">
                Sudah disetujui Administrator.
            </p>
        </div>


        <div
            class="rounded-xl border border-red-200
                   bg-red-50 p-5 shadow-sm">

            <p class="text-sm font-medium text-red-700">
                Ditolak Admin
            </p>

            <p class="mt-2 text-3xl font-bold text-red-600">
                {{ $rejectedCount }}
            </p>

            <p class="mt-1 text-xs text-red-600">
                Pengajuan ditolak Administrator.
            </p>
        </div>
    </div>


    <div
        class="overflow-hidden rounded-xl
               border border-slate-200
               bg-white shadow-sm">

        {{-- FILTER --}}
        <div class="border-b p-5">

            <form
                method="GET"
                class="flex gap-3">

                <select
                    name="year"
                    class="rounded-lg border-slate-300">

                    @foreach(
                    $years
                    as $yearOption
                    )

                    <option
                        value="{{ $yearOption }}"

                        @selected(
                        $year==$yearOption
                        )>
                        {{ $yearOption }}
                    </option>

                    @endforeach

                </select>


                <button
                    class="rounded-lg bg-slate-800
                           px-4 py-2
                           text-sm text-white">
                    Tampilkan
                </button>

            </form>

        </div>



        <div class="overflow-x-auto">

            <table
                class="min-w-full
                       divide-y divide-slate-200">

                <thead class="bg-slate-50">

                    <tr>

                        <th
                            class="px-6 py-3 text-left
                                   text-xs uppercase
                                   text-slate-500">
                            Jenis
                        </th>

                        <th
                            class="px-6 py-3 text-left
                                   text-xs uppercase
                                   text-slate-500">
                            Tanggal
                        </th>

                        <th
                            class="px-6 py-3 text-left
                                   text-xs uppercase
                                   text-slate-500">
                            Hari
                        </th>

                        <th
                            class="px-6 py-3 text-left
                                   text-xs uppercase
                                   text-slate-500">
                            Pengganti
                        </th>

                        <th
                            class="px-6 py-3 text-left
                                   text-xs uppercase
                                   text-slate-500">
                            Status
                        </th>

                    </tr>

                </thead>


                <tbody
                    class="divide-y
                           divide-slate-200">

                    @forelse(
                    $leaveRequests
                    as $leave
                    )

                    <tr>


                        {{-- ===============================
                                JENIS
                            ================================ --}}
                        <td class="px-6 py-4">

                            <p class="font-semibold text-slate-800">

                                {{ $leave
                                        ->permissionType
                                        ?->name
                                        ?? 'Perizinan' }}

                            </p>


                            @if(
                            $leave
                            ->annual_leave_deducted_days
                            > 0
                            )

                            <p
                                class="mt-1
                                               text-xs
                                               text-amber-600">
                                Menggunakan
                                {{ $leave
                                            ->annual_leave_deducted_days }}
                                hari cuti tahunan
                            </p>

                            @endif

                        </td>



                        {{-- ===============================
                                TANGGAL
                            ================================ --}}
                        <td
                            class="whitespace-nowrap
                                       px-6 py-4 text-sm">

                            {{ $leave
                                    ->start_date
                                    ->format('d/m/Y') }}

                            -

                            {{ $leave
                                    ->end_date
                                    ->format('d/m/Y') }}


                            <p
                                class="mt-1 max-w-xs
                                           truncate text-xs
                                           text-slate-400">
                                {{ $leave->reason }}
                            </p>

                        </td>



                        {{-- ===============================
                                TOTAL
                            ================================ --}}
                        <td
                            class="whitespace-nowrap
                                       px-6 py-4 text-sm">

                            {{ $leave->total_days }}
                            hari


                            @if(
                            $leave->excess_days
                            > 0
                            )

                            <p class="mt-1 text-xs text-amber-600">

                                Kelebihan:

                                {{ $leave->excess_days }}

                                hari

                            </p>

                            @endif

                        </td>



                        {{-- ===============================
                                PENGGANTI
                            ================================ --}}
                        <td
                            class="min-w-[260px]
                                       px-6 py-4">

                            @if($leave->self_replacement_days > 0)

                            <span
                                class="rounded-full bg-amber-50 px-2.5 py-1 text-xs font-medium text-amber-700">
                                Pengganti Mandiri
                            </span>

                            <p class="mt-2 text-sm font-semibold text-slate-700">
                                {{ $leave->self_replacement_days }} hari
                            </p>

                            <p class="text-xs text-slate-400">
                                Nama pengganti tidak dicatat di sistem
                            </p>

                            @if($leave->self_replacement_consent)
                            <p class="mt-2 text-xs font-medium text-emerald-600">
                                ✓ Konfirmasi tanggung jawab sudah diberikan
                            </p>
                            @endif

                            @elseif(
                            $leave->has_substitute
                            )

                            <span
                                class="rounded-full
                                               bg-blue-50
                                               px-2.5 py-1
                                               text-xs font-medium
                                               text-blue-700">
                                Ada Pengganti
                            </span>


                            <p
                                class="mt-2 text-sm
                                               font-semibold
                                               text-slate-700">
                                {{ $leave
                                            ->substitute_name }}
                            </p>


                            <p class="text-xs text-slate-400">

                                {{ $leave
                                            ->substituteSchedules
                                            ->count() }}

                                jadwal penggantian

                            </p>


                            <div class="mt-3 space-y-1">

                                @foreach(
                                $leave
                                ->substituteSchedules
                                ->take(3)
                                as $schedule
                                )

                                <p
                                    class="text-xs
                                                       text-slate-500">

                                    {{ $schedule
                                                    ->schedule_date
                                                    ->format('d/m') }}

                                    •


                                    @if(
                                    $schedule
                                    ->schedule_type
                                    === 'full_shift'
                                    )

                                    {{ $schedule
                                                        ->workShift
                                                        ?->name
                                                        ?? '-' }}

                                    @else

                                    {{ substr(
                                                        $schedule
                                                            ->start_time,
                                                        0,
                                                        5
                                                    ) }}

                                    -

                                    {{ substr(
                                                        $schedule
                                                            ->end_time,
                                                        0,
                                                        5
                                                    ) }}

                                    @endif

                                </p>

                                @endforeach


                                @if(
                                $leave
                                ->substituteSchedules
                                ->count()
                                > 3
                                )

                                <p
                                    class="text-xs
                                                       font-medium
                                                       text-blue-600">
                                    +

                                    {{ $leave
                                                    ->substituteSchedules
                                                    ->count() - 3 }}

                                    hari lainnya
                                </p>

                                @endif

                            </div>


                            @else

                            <span
                                class="rounded-full
                                               bg-slate-100
                                               px-2.5 py-1
                                               text-xs
                                               text-slate-600">
                                Tanpa Pengganti
                            </span>

                            @endif

                        </td>



                        {{-- ===============================
                                STATUS FINAL ADMIN
                            ================================ --}}
                        <td class="min-w-[270px] px-6 py-4">

                            {{-- MENUNGGU ADMIN --}}
                            @if($leave->status === 'pending')

                            <span
                                class="inline-flex items-center gap-1.5
                                           rounded-full bg-blue-50
                                           px-3 py-1 text-xs
                                           font-semibold text-blue-700">

                                <span
                                    class="h-1.5 w-1.5 rounded-full
                                               bg-blue-500">
                                </span>

                                Menunggu Keputusan Admin
                            </span>


                            <p class="mt-2 text-xs leading-5 text-slate-500">
                                Pengajuan sudah masuk ke tahap keputusan final Administrator.
                            </p>


                            {{-- DISETUJUI ADMIN --}}
                            @elseif($leave->status === 'approved')

                            <span
                                class="inline-flex items-center gap-1.5
                                           rounded-full bg-emerald-50
                                           px-3 py-1 text-xs
                                           font-semibold text-emerald-700">
                                ✓ Disetujui
                            </span>


                            <p class="mt-2 text-xs leading-5 text-slate-500">
                                Diproses oleh
                                <span class="font-medium text-slate-700">
                                    {{ $leave->approver?->name ?? 'Administrator' }}
                                </span>

                                @if($leave->approved_at)
                                <br>
                                {{ $leave->approved_at->format('d/m/Y H:i') }}
                                @endif
                            </p>


                            {{-- DITOLAK ADMIN --}}
                            @elseif($leave->status === 'rejected')

                            <span
                                class="inline-flex items-center gap-1.5
                                           rounded-full bg-red-50
                                           px-3 py-1 text-xs
                                           font-semibold text-red-700">
                                ✕ Ditolak Admin
                            </span>


                            <p class="mt-2 text-xs text-slate-500">
                                {{ $leave->approver?->name ?? 'Administrator' }}

                                @if($leave->rejected_at)
                                •
                                {{ $leave->rejected_at->format('d/m/Y H:i') }}
                                @endif
                            </p>


                            @if($leave->rejection_reason)

                            <div
                                class="mt-2 max-w-sm rounded-lg
                                               border border-red-100
                                               bg-red-50/70 px-3 py-2">

                                <p
                                    class="text-[11px] font-semibold
                                                   uppercase tracking-wide
                                                   text-red-500">
                                    Alasan
                                </p>

                                <p
                                    class="mt-1 whitespace-pre-line
                                                   text-xs leading-5
                                                   text-red-700">
                                    {{ $leave->rejection_reason }}
                                </p>
                            </div>

                            @endif


                            {{-- FALLBACK --}}
                            @else

                            <span
                                class="inline-flex rounded-full
                                           bg-slate-100 px-3 py-1
                                           text-xs font-medium
                                           text-slate-600">
                                {{ ucfirst($leave->status) }}
                            </span>

                            @endif

                        </td>

                    </tr>


                    @empty

                    <tr>

                        <td
                            colspan="5"
                            class="px-6 py-12
                                       text-center
                                       text-sm
                                       text-slate-500">
                            Belum ada pengajuan perizinan.
                        </td>

                    </tr>

                    @endforelse

                </tbody>

            </table>

        </div>



        @if(
        $leaveRequests->hasPages()
        )

        <div class="border-t px-6 py-4">
            {{ $leaveRequests->links() }}
        </div>

        @endif

    </div>

</div>

@endsection