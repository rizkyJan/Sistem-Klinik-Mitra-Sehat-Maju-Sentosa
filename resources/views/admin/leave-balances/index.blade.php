@extends('layouts.admin')

@section('title', 'Jatah Cuti')
@section('page-title', 'Jatah Cuti')

@section('content')

<div class="space-y-6">

    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">
                Jatah Cuti Karyawan
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Kelola jatah cuti tahunan karyawan Mitra Sehat Maju Sentosa.
            </p>
        </div>

        @if($year <= now()->year)
            <form
                action="{{ route('admin.leave-balances.generate') }}"
                method="POST"
                data-confirm
                data-confirm-tone="warning"
                data-confirm-title="Generate Jatah Cuti {{ $year }}?"
                data-confirm-message="Jatah cuti akan dibuat untuk seluruh karyawan yang sudah genap 12 bulan dan memenuhi syarat."
                data-confirm-button="Ya, Generate">
                @csrf

                <input
                    type="hidden"
                    name="year"
                    value="{{ $year }}">

                <button
                    type="submit"
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-blue-700">
                    <span class="text-xl leading-none">+</span>
                    Generate Jatah {{ $year }}
                </button>
            </form>
            @else
            <button
                type="button"
                disabled
                class="inline-flex cursor-not-allowed items-center justify-center gap-2 rounded-lg bg-slate-300 px-5 py-3 text-sm font-semibold text-white">
                Generate Jatah {{ $year }}
            </button>
            @endif
    </div>


    @if(session('success'))
    <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm text-emerald-700">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="rounded-xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-700">
        {{ session('error') }}
    </div>
    @endif

    @if($errors->any())
    <div class="rounded-xl border border-red-200 bg-red-50 p-5">
        <p class="font-semibold text-red-700">
            Terdapat data yang belum benar.
        </p>

        <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-red-600">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif


    <div class="rounded-xl border border-blue-200 bg-blue-50 p-5">
        <div class="flex items-start gap-3">
            <div class="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-full border border-blue-300 text-sm font-bold text-blue-600">
                i
            </div>

            <div>
                <h3 class="font-semibold text-blue-800">
                    Hak cuti dimulai tepat setelah genap 12 bulan
                </h3>

                <p class="mt-1 text-sm leading-relaxed text-blue-700">
                    Contoh: mulai kerja
                    <strong>27/07/2025</strong>,
                    maka mulai berhak
                    <strong>27/07/2026</strong>.
                    Pada tanggal tersebut karyawan sudah termasuk berhak menerima jatah cuti tahunan.
                </p>
            </div>
        </div>
    </div>


    {{-- ============================================================
        STATISTIK
    ============================================================ --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">

        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500">
                        Total Karyawan Aktif
                    </p>

                    <p class="mt-2 text-3xl font-bold text-slate-800">
                        {{ $totalKaryawan }}
                    </p>
                </div>

                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-6 w-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.25a7.5 7.5 0 0115 0" />
                    </svg>
                </div>
            </div>
        </div>


        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500">
                        Sudah Memiliki Jatah
                    </p>

                    <p class="mt-2 text-3xl font-bold text-slate-800">
                        {{ $sudahDiberi }}
                    </p>

                    <p class="mt-1 text-xs text-slate-400">
                        Tahun {{ $year }}
                    </p>
                </div>

                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-6 w-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                </div>
            </div>
        </div>


        <div class="rounded-xl border {{ $belumDiberi > 0 ? 'border-amber-300 bg-amber-50/30' : 'border-slate-200 bg-white' }} p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500">
                        Belum Memiliki Jatah
                    </p>

                    <p class="mt-2 text-3xl font-bold {{ $belumDiberi > 0 ? 'text-amber-600' : 'text-slate-800' }}">
                        {{ $belumDiberi }}
                    </p>

                    <p class="mt-1 text-xs text-slate-400">
                        Sudah genap 12 bulan
                    </p>
                </div>

                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-50 text-amber-600">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-6 w-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>


        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500">
                        Belum Berhak
                    </p>

                    <p class="mt-2 text-3xl font-bold text-slate-800">
                        {{ $belumBerhak }}
                    </p>

                    <p class="mt-1 text-xs text-slate-400">
                        Belum genap 12 bulan
                    </p>
                </div>

                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-slate-100 text-slate-500">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-6 w-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 4.5h.008v.008H12v-.008z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.34 2.75L1.82 17.5A2 2 0 003.55 20.5h16.9a2 2 0 001.73-3L13.66 2.75a2 2 0 00-3.32 0z" />
                    </svg>
                </div>
            </div>
        </div>

    </div>


    {{-- ============================================================
        TABLE CARD
    ============================================================ --}}
    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">

        <div class="border-b border-slate-200 p-5">
            <form
                method="GET"
                action="{{ route('admin.leave-balances.index') }}"
                class="grid grid-cols-1 gap-4 lg:grid-cols-[165px_1fr_auto]">
                <div>
                    <label class="mb-2 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                        Tahun
                    </label>

                    <select
                        name="year"
                        class="w-full rounded-lg border-slate-300">
                        @foreach($years as $yearOption)
                        <option
                            value="{{ $yearOption }}"
                            @selected($year==$yearOption)>
                            {{ $yearOption }}
                        </option>
                        @endforeach
                    </select>
                </div>


                <div>
                    <label class="mb-2 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                        Cari Karyawan
                    </label>

                    <input
                        type="text"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Nama, NIK, email, bidang..."
                        class="w-full rounded-lg border-slate-300">
                </div>


                <div class="flex items-end">
                    <button
                        type="submit"
                        class="w-full rounded-lg bg-slate-800 px-6 py-2.5 text-sm font-semibold text-white hover:bg-slate-900 lg:w-auto">
                        Tampilkan
                    </button>
                </div>
            </form>
        </div>


        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">No</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Karyawan</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Bidang</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Mulai Kerja</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Mulai Berhak</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Jatah</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Terpakai</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Sisa</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-200">
                    @forelse($employees as $employee)

                    @php
                    $balance = $employee->selected_leave_balance;
                    $eligibleDate = $employee->annual_leave_eligible_date;
                    $isEligible = $employee->is_annual_leave_eligible;

                    $remaining = $balance
                    ? max(0, $balance->quota_days - $balance->used_days)
                    : 0;
                    @endphp

                    <tr class="hover:bg-slate-50/70 {{ $isEligible && ! $balance ? 'bg-amber-50/40' : '' }}">

                        <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-500">
                            {{ $loop->iteration }}
                        </td>


                        <td class="min-w-[240px] px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-blue-100 font-semibold text-blue-600">
                                    {{ strtoupper(substr($employee->name, 0, 1)) }}
                                </div>

                                <div>
                                    <p class="font-semibold text-slate-800">
                                        {{ $employee->name }}
                                    </p>

                                    <p class="text-xs text-slate-500">
                                        NIK: {{ $employee->nik ?? '-' }}
                                    </p>

                                    @if($isEligible && ! $balance)
                                    <p class="mt-1 text-xs font-semibold text-amber-600">
                                        Sudah berhak, jatah belum dibuat
                                    </p>
                                    @endif
                                </div>
                            </div>
                        </td>


                        <td class="whitespace-nowrap px-6 py-4">
                            <span class="inline-flex rounded-full bg-indigo-50 px-3 py-1 text-xs font-medium text-indigo-700">
                                {{ $employee->department?->name ?? '-' }}
                            </span>
                        </td>


                        <td class="whitespace-nowrap px-6 py-4 text-sm">
                            @if($employee->join_date)
                            <p class="font-medium text-slate-700">
                                {{ \Carbon\Carbon::parse($employee->join_date)->format('d/m/Y') }}
                            </p>

                            <p class="mt-1 text-xs text-slate-400">
                                {{ \Carbon\Carbon::parse($employee->join_date)->diffForHumans(now(), true) }}
                            </p>
                            @else
                            <span class="text-slate-400">
                                Belum diatur
                            </span>
                            @endif
                        </td>


                        <td class="whitespace-nowrap px-6 py-4 text-sm">
                            @if($eligibleDate)
                            <p class="font-medium {{ $isEligible ? 'text-emerald-700' : 'text-slate-700' }}">
                                {{ $eligibleDate->format('d/m/Y') }}
                            </p>

                            @if($isEligible)
                            <p class="mt-1 text-xs font-semibold text-emerald-600">
                                Sudah berhak
                            </p>
                            @else
                            <p class="mt-1 text-xs text-amber-600">
                                Belum 12 bulan
                            </p>
                            @endif
                            @else
                            <span class="text-slate-400">-</span>
                            @endif
                        </td>


                        <td class="whitespace-nowrap px-6 py-4">
                            @if($balance)
                            <span class="font-bold text-slate-800">
                                {{ $balance->quota_days }}
                            </span>

                            <span class="text-xs text-slate-400">
                                hari
                            </span>
                            @else
                            <span class="text-slate-400">-</span>
                            @endif
                        </td>


                        <td class="whitespace-nowrap px-6 py-4">
                            @if($balance)
                            <span class="font-bold text-amber-600">
                                {{ $balance->used_days }}
                            </span>

                            <span class="text-xs text-slate-400">
                                hari
                            </span>
                            @else
                            <span class="text-slate-400">-</span>
                            @endif
                        </td>


                        <td class="whitespace-nowrap px-6 py-4">
                            @if($balance)
                            <span class="font-bold text-emerald-600">
                                {{ $remaining }}
                            </span>

                            <span class="text-xs text-slate-400">
                                hari
                            </span>
                            @else
                            <span class="text-slate-400">-</span>
                            @endif
                        </td>


                        <td class="whitespace-nowrap px-6 py-4">
                            @if($balance)
                            <span class="inline-flex rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700">
                                Sudah diberikan
                            </span>
                            @elseif($isEligible)
                            <span class="inline-flex rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">
                                Belum diberikan
                            </span>
                            @elseif($employee->join_date)
                            <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600">
                                Belum berhak
                            </span>
                            @else
                            <span class="inline-flex rounded-full bg-red-50 px-3 py-1 text-xs font-medium text-red-600">
                                Join date belum diatur
                            </span>
                            @endif
                        </td>


                        <td class="whitespace-nowrap px-6 py-4 text-right">
                            @if($balance)
                            <form
                                action="{{ route('admin.leave-balances.update', $balance) }}"
                                method="POST"
                                class="inline-flex items-center gap-2">
                                @csrf
                                @method('PUT')

                                <input
                                    type="number"
                                    name="quota_days"
                                    min="{{ $balance->used_days }}"
                                    max="365"
                                    value="{{ $balance->quota_days }}"
                                    class="w-24 rounded-lg border-slate-300 text-center text-sm">

                                <button
                                    type="submit"
                                    class="rounded-lg bg-blue-50 px-4 py-2 text-sm font-medium text-blue-700 hover:bg-blue-100">
                                    Simpan
                                </button>
                            </form>
                            @elseif($isEligible)
                            <span class="inline-flex rounded-lg bg-amber-50 px-3 py-2 text-xs font-semibold text-amber-700">
                                Klik Generate Jatah {{ $year }}
                            </span>
                            @else
                            <span class="text-xs text-slate-400">-</span>
                            @endif
                        </td>

                    </tr>

                    @empty
                    <tr>
                        <td colspan="10" class="px-6 py-12 text-center text-sm text-slate-500">
                            Data karyawan tidak ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>


    {{-- ============================================================
        KETERANGAN
    ============================================================ --}}
    <div class="rounded-xl border border-blue-200 bg-blue-50 p-5">
        <div class="flex items-start gap-3">
            <div class="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-full border border-blue-300 text-sm font-bold text-blue-600">
                i
            </div>

            <div>
                <h3 class="font-semibold text-blue-800">
                    Ketentuan Jatah Cuti
                </h3>

                <p class="mt-1 text-sm leading-relaxed text-blue-700">
                    Karyawan memperoleh jatah cuti tahunan
                    <strong>9 hari</strong>
                    setelah genap
                    <strong>12 bulan</strong>
                    sejak tanggal mulai kerja.
                    Contoh: mulai kerja
                    <strong>27/07/2025</strong>,
                    maka mulai berhak
                    <strong>27/07/2026</strong>.
                    Sisa cuti tahun sebelumnya tidak ditambahkan ke tahun berikutnya.
                </p>
            </div>
        </div>
    </div>

</div>

@endsection