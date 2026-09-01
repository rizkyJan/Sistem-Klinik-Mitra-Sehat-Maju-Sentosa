@extends('layouts.admin')

@section('title', 'Detail Pengajuan Perizinan')

@section('page-title', 'Detail Pengajuan Perizinan')

@section('content')

<div class="mx-auto max-w-6xl space-y-6">


    {{-- ============================================================
        HEADER
    ============================================================ --}}
    <div
        class="flex flex-col gap-4
               sm:flex-row
               sm:items-center
               sm:justify-between">

        <div>

            <h1 class="text-2xl font-bold text-slate-800">
                Detail Pengajuan Perizinan
            </h1>

            <p class="mt-1 text-sm text-slate-500">

                Informasi lengkap pengajuan

                {{ $leaveRequest
                    ->permissionType
                    ?->name
                    ?? 'perizinan' }}.

            </p>

        </div>


        <a
            href="{{ route(
                'admin.leave-requests.index'
            ) }}"
            class="inline-flex items-center
                   justify-center rounded-lg
                   border border-slate-300
                   bg-white px-4 py-2.5
                   text-sm font-medium
                   text-slate-600
                   hover:bg-slate-50">
            ← Kembali
        </a>

    </div>



    {{-- ============================================================
        ALERT
    ============================================================ --}}
    @if(session('success'))

    <div
        class="rounded-xl border
                   border-emerald-200
                   bg-emerald-50
                   px-5 py-4
                   text-sm text-emerald-700">
        {{ session('success') }}
    </div>

    @endif


    @if(session('error'))

    <div
        class="rounded-xl border
                   border-red-200
                   bg-red-50
                   px-5 py-4
                   text-sm text-red-700">
        {{ session('error') }}
    </div>

    @endif


    @if($errors->any())

    <div
        class="rounded-xl border
                   border-red-200
                   bg-red-50 p-5">

        <p class="font-semibold text-red-700">
            Terdapat kesalahan.
        </p>

        <ul
            class="mt-2 list-disc space-y-1
                       pl-5 text-sm text-red-600">

            @foreach($errors->all() as $error)

            <li>
                {{ $error }}
            </li>

            @endforeach

        </ul>

    </div>

    @endif



    {{-- ============================================================
        STATUS
    ============================================================ --}}
    <div
        class="rounded-xl border
               border-slate-200
               bg-white p-6 shadow-sm">

        <div
            class="flex flex-col gap-4
                   sm:flex-row
                   sm:items-center
                   sm:justify-between">

            <div>

                <p class="text-sm text-slate-500">
                    Status Pengajuan
                </p>


                <div class="mt-2">

                    @if(
                    $leaveRequest->status
                    === 'pending'
                    )

                    @if(
                    $leaveRequest->user?->role === 'karyawan'
                    &&
                    $leaveRequest->kabid_status
                    === \App\Models\LeaveRequest::KABID_STATUS_PENDING
                    )

                    <span
                        class="inline-flex rounded-full
                                       bg-amber-50 px-3 py-1.5
                                       text-sm font-medium
                                       text-amber-700">
                        Menunggu Persetujuan Kabid
                    </span>

                    @else

                    <span
                        class="inline-flex rounded-full
                                       bg-blue-50 px-3 py-1.5
                                       text-sm font-medium
                                       text-blue-700">
                        Menunggu Keputusan Admin
                    </span>

                    @endif


                    @elseif(
                    $leaveRequest->status
                    === 'approved'
                    )

                    <span
                        class="inline-flex rounded-full
                                   bg-emerald-50
                                   px-3 py-1.5
                                   text-sm font-medium
                                   text-emerald-700">
                        Disetujui
                    </span>


                    @elseif(
                    $leaveRequest->status
                    === 'rejected'
                    )

                    <span
                        class="inline-flex rounded-full
                                   bg-red-50
                                   px-3 py-1.5
                                   text-sm font-medium
                                   text-red-700">
                        Ditolak
                    </span>


                    @else

                    <span
                        class="inline-flex rounded-full
                                   bg-slate-100
                                   px-3 py-1.5
                                   text-sm font-medium
                                   text-slate-600">
                        {{ ucfirst(
                                $leaveRequest->status
                            ) }}
                    </span>

                    @endif

                </div>

            </div>


            <div class="text-sm text-slate-500">

                <p>
                    Diajukan pada
                </p>

                <p
                    class="mt-1 font-medium
                           text-slate-700">

                    {{ $leaveRequest
                        ->created_at
                        ->format('d/m/Y H:i') }}

                </p>

            </div>

        </div>

    </div>



    {{-- ============================================================
        DATA KARYAWAN
    ============================================================ --}}
    <div
        class="overflow-hidden rounded-xl
               border border-slate-200
               bg-white shadow-sm">

        <div
            class="border-b border-slate-200
                   px-6 py-5">

            <h2 class="font-semibold text-slate-800">
                Data Karyawan
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Informasi pegawai yang mengajukan perizinan.
            </p>

        </div>


        <div class="p-6">

            <div
                class="grid grid-cols-1
                       gap-6 sm:grid-cols-2
                       lg:grid-cols-4">

                {{-- Nama --}}
                <div>

                    <p
                        class="text-xs font-semibold
                               uppercase tracking-wide
                               text-slate-400">
                        Nama
                    </p>

                    <p
                        class="mt-1 font-medium
                               text-slate-800">
                        {{ $leaveRequest
                            ->user
                            ->name }}
                    </p>

                </div>


                {{-- NIK --}}
                <div>

                    <p
                        class="text-xs font-semibold
                               uppercase tracking-wide
                               text-slate-400">
                        NIK
                    </p>

                    <p
                        class="mt-1 font-medium
                               text-slate-800">
                        {{ $leaveRequest
                            ->user
                            ->nik
                            ?? '-' }}
                    </p>

                </div>


                {{-- Bidang --}}
                <div>

                    <p
                        class="text-xs font-semibold
                               uppercase tracking-wide
                               text-slate-400">
                        Bidang
                    </p>

                    <p
                        class="mt-1 font-medium
                               text-slate-800">

                        {{ $leaveRequest
                            ->user
                            ->department
                            ?->name
                            ?? '-' }}

                    </p>

                </div>


                {{-- WA --}}
                <div>

                    <p
                        class="text-xs font-semibold
                               uppercase tracking-wide
                               text-slate-400">
                        WhatsApp
                    </p>

                    <p
                        class="mt-1 font-medium
                               text-slate-800">

                        {{ $leaveRequest
                            ->user
                            ->whatsapp
                            ?? '-' }}

                    </p>

                </div>


                {{-- Tanggal Masuk --}}
                <div>

                    <p
                        class="text-xs font-semibold
                               uppercase tracking-wide
                               text-slate-400">
                        Mulai Kerja
                    </p>

                    <p
                        class="mt-1 font-medium
                               text-slate-800">

                        {{ $leaveRequest
                            ->user
                            ->join_date
                            ?->format('d/m/Y')
                            ?? '-' }}

                    </p>

                </div>


                {{-- Masa Kerja --}}
                <div>

                    <p
                        class="text-xs font-semibold
                               uppercase tracking-wide
                               text-slate-400">
                        Masa Kerja
                    </p>


                    @if(
                    $leaveRequest
                    ->user
                    ->join_date
                    )

                    <p
                        class="mt-1 font-medium
                                   text-slate-800">

                        {{ $leaveRequest
                                ->user
                                ->join_date
                                ->diffForHumans(
                                    now(),
                                    true
                                ) }}

                    </p>

                    @else

                    <p class="mt-1 text-slate-400">
                        -
                    </p>

                    @endif

                </div>

            </div>

        </div>

    </div>



    {{-- ============================================================
        DETAIL PERIZINAN
    ============================================================ --}}
    <div
        class="overflow-hidden rounded-xl
               border border-slate-200
               bg-white shadow-sm">

        <div
            class="border-b border-slate-200
                   px-6 py-5">

            <h2 class="font-semibold text-slate-800">
                Detail Perizinan
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Jenis, periode, dan ketentuan pengajuan.
            </p>

        </div>


        <div class="p-6">

            <div
                class="grid grid-cols-1
                       gap-6 sm:grid-cols-2
                       lg:grid-cols-4">

                {{-- Jenis --}}
                <div>

                    <p
                        class="text-xs font-semibold
                               uppercase tracking-wide
                               text-slate-400">
                        Jenis Perizinan
                    </p>

                    <p
                        class="mt-1 font-semibold
                               text-slate-800">

                        {{ $leaveRequest
                            ->permissionType
                            ?->name
                            ?? 'Data Lama' }}

                    </p>

                </div>


                {{-- Tanggal Mulai --}}
                <div>

                    <p
                        class="text-xs font-semibold
                               uppercase tracking-wide
                               text-slate-400">
                        Tanggal Mulai
                    </p>

                    <p
                        class="mt-1 font-medium
                               text-slate-800">

                        {{ $leaveRequest
                            ->start_date
                            ->format('d/m/Y') }}

                    </p>

                </div>


                {{-- Tanggal Akhir --}}
                <div>

                    <p
                        class="text-xs font-semibold
                               uppercase tracking-wide
                               text-slate-400">
                        Tanggal Selesai
                    </p>

                    <p
                        class="mt-1 font-medium
                               text-slate-800">

                        {{ $leaveRequest
                            ->end_date
                            ->format('d/m/Y') }}

                    </p>

                </div>


                {{-- Total --}}
                <div>

                    <p
                        class="text-xs font-semibold
                               uppercase tracking-wide
                               text-slate-400">
                        Total
                    </p>

                    <p
                        class="mt-1 font-bold
                               text-slate-800">

                        {{ $leaveRequest
                            ->total_days }}

                        hari

                    </p>

                </div>

            </div>



            {{-- Reason --}}
            <div class="mt-6">

                <p
                    class="text-xs font-semibold
                           uppercase tracking-wide
                           text-slate-400">
                    Alasan / Keperluan
                </p>

                <div
                    class="mt-2 rounded-lg
                           bg-slate-50
                           px-4 py-3
                           text-sm leading-relaxed
                           text-slate-700">

                    {{ $leaveRequest->reason }}

                </div>

            </div>



            {{-- ====================================================
                POLICY BREAKDOWN
            ==================================================== --}}
            @if(
            $leaveRequest->permissionType
            )

            <div
                class="mt-7 border-t
                           border-slate-200 pt-6">

                <h3
                    class="font-semibold
                               text-slate-800">
                    Perhitungan Berdasarkan Kebijakan
                </h3>

                <p class="mt-1 text-sm text-slate-500">
                    Ringkasan pembagian hari pengajuan berdasarkan hak perizinan,
                    cuti tahunan, pengganti mandiri, dan hari tidak dibayar bila ada.
                </p>


                <div
                    class="mt-5 grid
                               grid-cols-1 gap-4
                               sm:grid-cols-2
                               lg:grid-cols-4">

                    {{-- Covered --}}
                    @if(
                    $leaveRequest
                    ->policy_covered_days
                    !== null
                    )

                    <div
                        class="rounded-xl
                                       bg-blue-50 p-4">

                        <p class="text-xs text-blue-600">
                            Sesuai Ketentuan
                        </p>

                        <p
                            class="mt-1 text-xl
                                           font-bold
                                           text-blue-800">

                            {{ $leaveRequest
                                        ->policy_covered_days }}

                            hari

                        </p>

                    </div>

                    @endif



                    {{-- Excess --}}
                    @if(
                    $leaveRequest
                    ->excess_days
                    > 0
                    )

                    <div
                        class="rounded-xl
                                       bg-amber-50 p-4">

                        <p class="text-xs text-amber-600">
                            Kelebihan
                        </p>

                        <p
                            class="mt-1 text-xl
                                           font-bold
                                           text-amber-800">

                            {{ $leaveRequest
                                        ->excess_days }}

                            hari

                        </p>

                    </div>

                    @endif



                    {{-- Deduction --}}
                    @if(
                    $leaveRequest
                    ->annual_leave_deducted_days
                    > 0
                    )

                    <div
                        class="rounded-xl
                                       bg-red-50 p-4">

                        <p class="text-xs text-red-600">
                            Potong Cuti Tahunan
                        </p>

                        <p
                            class="mt-1 text-xl
                                           font-bold
                                           text-red-700">

                            {{ $leaveRequest
                                        ->annual_leave_deducted_days }}

                            hari

                        </p>

                    </div>

                    @endif



                    {{-- Pengganti Mandiri --}}
                    @if($leaveRequest->self_replacement_days > 0)

                    <div
                        class="rounded-xl border border-amber-200 bg-amber-50 p-4">

                        <p class="text-xs text-amber-600">
                            Pengganti & Biaya Mandiri
                        </p>

                        <p class="mt-1 text-xl font-bold text-amber-800">
                            {{ $leaveRequest->self_replacement_days }} hari
                        </p>

                        <p class="mt-1 text-xs leading-relaxed text-amber-700">
                            Karyawan mencari pengganti dan menyelesaikan biaya pengganti secara pribadi.
                        </p>

                    </div>

                    @endif


                    {{-- Unpaid --}}
                    @if($leaveRequest->unpaid_days > 0)

                    <div
                        class="rounded-xl border border-rose-200 bg-rose-50 p-4">

                        <p class="text-xs text-rose-600">
                            Hari Tidak Dibayar
                        </p>

                        <p class="mt-1 text-xl font-bold text-rose-700">
                            {{ $leaveRequest->unpaid_days }} hari
                        </p>

                        <p class="mt-1 text-xs leading-relaxed text-rose-600">
                            Tidak tertutup oleh hak izin maupun sisa cuti tahunan.
                        </p>

                    </div>

                    @elseif(
                    $leaveRequest->excess_days > 0
                    && $leaveRequest->self_replacement_days === 0
                    )

                    <div class="rounded-xl bg-emerald-50 p-4">
                        <p class="text-xs text-emerald-600">
                            Sisa Tidak Tercover
                        </p>

                        <p class="mt-1 text-xl font-bold text-emerald-700">
                            0 hari
                        </p>

                        <p class="mt-1 text-xs leading-relaxed text-emerald-600">
                            Seluruh kelebihan sudah tertutup sesuai pembagian pengajuan.
                        </p>
                    </div>

                    @endif

                </div>


                {{-- ====================================================
                    RINGKASAN PEMBAGIAN
                ==================================================== --}}
                @if(
                $leaveRequest
                ->excess_days
                > 0
                )

                <div
                    class="mt-5 rounded-xl
                           border border-slate-200
                           bg-slate-50 p-5">

                    <p class="text-sm font-semibold text-slate-800">
                        Ringkasan Pembagian Hari
                    </p>

                    <p class="mt-2 text-sm leading-relaxed text-slate-600">
                        Dari total
                        <strong class="text-slate-800">
                            {{ $leaveRequest->total_days }} hari
                        </strong>,

                        <strong class="text-blue-700">
                            {{ $leaveRequest->policy_covered_days ?? 0 }} hari
                        </strong>
                        tercatat sebagai hak sesuai ketentuan,

                        <strong class="text-red-700">
                            {{ $leaveRequest->annual_leave_deducted_days }} hari
                        </strong>
                        menggunakan cuti tahunan

                        @if($leaveRequest->self_replacement_days > 0)
                        dan
                        <strong class="text-amber-700">
                            {{ $leaveRequest->self_replacement_days }} hari
                        </strong>
                        menjadi tanggung jawab mencari pengganti dan biaya mandiri
                        @endif

                        @if($leaveRequest->unpaid_days > 0)
                        serta
                        <strong class="text-rose-700">
                            {{ $leaveRequest->unpaid_days }} hari
                        </strong>
                        dicatat sebagai hari tidak dibayar / potong gaji.
                        @else
                        .
                        @endif
                    </p>

                </div>

                @endif


                {{-- ====================================================
                    KONFIRMASI PENGGANTI MANDIRI - IZIN SAKIT
                ==================================================== --}}
                @if($leaveRequest->self_replacement_days > 0)

                <div
                    class="mt-5 rounded-xl border {{ $leaveRequest->self_replacement_consent ? 'border-emerald-200 bg-emerald-50' : 'border-red-200 bg-red-50' }} p-5">

                    <p class="text-sm font-semibold {{ $leaveRequest->self_replacement_consent ? 'text-emerald-800' : 'text-red-800' }}">
                        {{ $leaveRequest->self_replacement_consent ? '✓ Konfirmasi Pengganti Mandiri Tercatat' : 'Konfirmasi Pengganti Mandiri Belum Tercatat' }}
                    </p>

                    <p class="mt-1 text-sm leading-relaxed {{ $leaveRequest->self_replacement_consent ? 'text-emerald-700' : 'text-red-700' }}">
                        Karyawan memilih {{ $leaveRequest->self_replacement_days }} hari untuk mencari pengganti sendiri dan membayar pengganti secara pribadi.
                        Nama pengganti tidak diwajibkan dicatat di sistem.
                    </p>

                    @if($leaveRequest->self_replacement_consent_at)
                    <p class="mt-2 text-xs text-emerald-600">
                        Konfirmasi diberikan pada
                        <strong>{{ $leaveRequest->self_replacement_consent_at->format('d/m/Y H:i') }}</strong>.
                    </p>
                    @endif
                </div>

                @endif


                {{-- ====================================================
                    PERSETUJUAN KARYAWAN UNTUK UNPAID
                ==================================================== --}}
                @if(
                $leaveRequest
                ->unpaid_days
                > 0
                )

                @if(
                $leaveRequest
                ->salary_deduction_consent
                )

                <div
                    class="mt-5 rounded-xl
                           border border-emerald-200
                           bg-emerald-50 p-5">

                    <div class="flex items-start gap-3">

                        <div
                            class="flex h-9 w-9 shrink-0
                                   items-center justify-center
                                   rounded-full bg-emerald-100
                                   text-emerald-700">
                            ✓
                        </div>

                        <div>

                            <p
                                class="text-sm font-semibold
                                       text-emerald-800">
                                Persetujuan Karyawan Sudah Tercatat
                            </p>

                            <p
                                class="mt-1 text-sm
                                       leading-relaxed
                                       text-emerald-700">

                                Karyawan memahami dan menyetujui bahwa

                                <strong>
                                    {{ $leaveRequest->unpaid_days }} hari
                                </strong>

                                yang tidak tertutup hak izin maupun cuti tahunan
                                diajukan sebagai hari tidak dibayar / potong gaji
                                apabila pengajuan disetujui oleh Admin/Kabid.

                            </p>


                            @if(
                            $leaveRequest
                            ->salary_deduction_consent_at
                            )

                            <p class="mt-2 text-xs text-emerald-600">

                                Persetujuan diberikan pada

                                <strong>
                                    {{ $leaveRequest
                                        ->salary_deduction_consent_at
                                        ->format('d/m/Y H:i') }}
                                </strong>.

                            </p>

                            @endif

                        </div>

                    </div>

                </div>


                @else

                <div
                    class="mt-5 rounded-xl
                           border border-red-200
                           bg-red-50 p-5">

                    <p
                        class="text-sm font-semibold
                               text-red-800">
                        Persetujuan Karyawan Belum Tercatat
                    </p>

                    <p
                        class="mt-1 text-sm
                               leading-relaxed
                               text-red-700">

                        Pengajuan memiliki

                        <strong>
                            {{ $leaveRequest->unpaid_days }} hari
                        </strong>

                        yang tidak tercover, tetapi persetujuan hari tidak dibayar
                        belum tercatat. Pengajuan ini sebaiknya tidak disetujui
                        sebelum persetujuan karyawan tersedia.

                    </p>

                </div>

                @endif

                @endif

            </div>

            @endif



            {{-- ====================================================
                SALDO CUTI
            ==================================================== --}}
            @if(
            $leaveRequest->leaveBalance
            )

            <div
                class="mt-7 border-t
                           border-slate-200 pt-6">

                <h3
                    class="font-semibold
                               text-slate-800">
                    Saldo Cuti Tahunan
                </h3>


                <div
                    class="mt-4 grid
                               grid-cols-1 gap-4
                               sm:grid-cols-3">

                    <div
                        class="rounded-xl
                                   bg-blue-50 p-4">

                        <p class="text-xs text-blue-600">
                            Jatah
                        </p>

                        <p
                            class="mt-1 text-xl
                                       font-bold
                                       text-blue-800">

                            {{ $leaveRequest
                                    ->leaveBalance
                                    ->quota_days }}

                            hari

                        </p>

                    </div>


                    <div
                        class="rounded-xl
                                   bg-amber-50 p-4">

                        <p class="text-xs text-amber-600">
                            Terpakai
                        </p>

                        <p
                            class="mt-1 text-xl
                                       font-bold
                                       text-amber-800">

                            {{ $leaveRequest
                                    ->leaveBalance
                                    ->used_days }}

                            hari

                        </p>

                    </div>


                    <div
                        class="rounded-xl
                                   bg-emerald-50 p-4">

                        <p class="text-xs text-emerald-600">
                            Sisa Saat Ini
                        </p>

                        <p
                            class="mt-1 text-xl
                                       font-bold
                                       text-emerald-800">

                            {{ max(
                                    0,

                                    $leaveRequest
                                        ->leaveBalance
                                        ->quota_days

                                    -

                                    $leaveRequest
                                        ->leaveBalance
                                        ->used_days
                                ) }}

                            hari

                        </p>

                    </div>

                </div>

            </div>

            @endif

        </div>

    </div>



    {{-- ============================================================
        DOKUMEN
    ============================================================ --}}
    @if(
    $leaveRequest
    ->supporting_document
    )

    <div
        class="rounded-xl border
                   border-slate-200
                   bg-white p-6 shadow-sm">

        <h2 class="font-semibold text-slate-800">
            Dokumen Pendukung
        </h2>

        <p class="mt-1 text-sm text-slate-500">
            Dokumen yang dilampirkan dalam pengajuan.
        </p>


        <a
            href="{{ asset(
                    'storage/'
                    .
                    $leaveRequest
                        ->supporting_document
                ) }}"
            target="_blank"
            rel="noopener noreferrer"

            class="mt-5 inline-flex
                       rounded-lg bg-blue-50
                       px-4 py-2.5
                       text-sm font-medium
                       text-blue-700
                       hover:bg-blue-100">
            Lihat Dokumen
        </a>

    </div>

    @endif



    {{-- ============================================================
        MELAHIRKAN
    ============================================================ --}}
    @if(
    $leaveRequest
    ->permissionType
    ?->code
    === 'maternity'
    )

    <div
        class="overflow-hidden
                   rounded-xl
                   border border-purple-200
                   bg-white shadow-sm">

        <div
            class="border-b
                       border-purple-100
                       bg-purple-50
                       px-6 py-5">

            <h2
                class="font-semibold
                           text-purple-900">
                Informasi Cuti Melahirkan
            </h2>

        </div>


        <div class="p-6">

            <div
                class="grid grid-cols-1
                           gap-6 sm:grid-cols-2">

                <div>

                    <p
                        class="text-xs uppercase
                                   text-slate-400">
                        Perkiraan Melahirkan
                    </p>

                    <p
                        class="mt-1 font-semibold
                                   text-slate-800">

                        {{ $leaveRequest
                                ->expected_delivery_date
                                ?->format('d/m/Y')
                                ?? '-' }}

                    </p>

                </div>


                <div>

                    <p
                        class="text-xs uppercase
                                   text-slate-400">
                        Status Gaji
                    </p>


                    @if(
                    $leaveRequest
                    ->maternity_salary_status
                    === 'paid_base_salary'
                    )

                    <span
                        class="mt-1 inline-flex
                                       rounded-full
                                       bg-emerald-50
                                       px-3 py-1
                                       text-xs font-medium
                                       text-emerald-700">
                        Mendapat Gaji Pokok
                    </span>


                    @elseif(
                    $leaveRequest
                    ->maternity_salary_status
                    === 'unpaid'
                    )

                    <span
                        class="mt-1 inline-flex
                                       rounded-full
                                       bg-amber-50
                                       px-3 py-1
                                       text-xs font-medium
                                       text-amber-700">
                        Tidak Mendapat Gaji
                    </span>


                    @else

                    <span
                        class="mt-1 text-sm
                                       text-slate-400">
                        Belum dapat ditentukan
                    </span>

                    @endif

                </div>

            </div>

        </div>

    </div>

    @endif



    {{-- ============================================================
        DATA ORANG PENGGANTI
    ============================================================ --}}
    <div
        class="overflow-hidden rounded-xl
               border border-slate-200
               bg-white shadow-sm">
        <div class="border-b border-slate-200 px-6 py-5">
            <h2 class="font-semibold text-slate-800">
                Data Pengganti
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Pengganti dapat berbeda untuk setiap tanggal perizinan.
            </p>
        </div>


        <div class="p-6">
            @if($leaveRequest->self_replacement_days > 0)

            <div class="rounded-xl border border-amber-200 bg-amber-50 p-5">
                <p class="text-sm font-semibold text-amber-900">
                    Pengganti Mandiri untuk Izin Sakit
                </p>

                <p class="mt-2 text-sm leading-relaxed text-amber-800">
                    {{ $leaveRequest->self_replacement_days }} hari menjadi tanggung jawab karyawan untuk mencari pengganti dan menyelesaikan biaya secara pribadi.
                    Nama, nomor WhatsApp, dan rekening pengganti tidak perlu dicatat di aplikasi.
                </p>

                <p class="mt-3 text-sm font-medium {{ $leaveRequest->self_replacement_consent ? 'text-emerald-700' : 'text-red-700' }}">
                    {{ $leaveRequest->self_replacement_consent ? '✓ Konfirmasi karyawan sudah tercatat.' : 'Konfirmasi karyawan belum tercatat.' }}
                </p>
            </div>

            @elseif(! $leaveRequest->has_substitute)

            <div class="rounded-lg border border-slate-200 bg-slate-50 px-4 py-4">
                <p class="text-sm font-medium text-slate-600">
                    Pengajuan ini tidak menggunakan pengganti yang dicatat di sistem.
                </p>
            </div>

            @elseif($leaveRequest->substituteSchedules->isNotEmpty())

            <div class="mb-5 grid gap-4 sm:grid-cols-2">
                <div class="rounded-xl bg-blue-50 p-4">
                    <p class="text-xs font-medium uppercase tracking-wide text-blue-600">
                        Hari Menggunakan Pengganti
                    </p>

                    <p class="mt-1 text-2xl font-bold text-blue-800">
                        {{ $leaveRequest->substituteSchedules->count() }} hari
                    </p>
                </div>

                <div class="rounded-xl bg-slate-50 p-4">
                    <p class="text-xs font-medium uppercase tracking-wide text-slate-500">
                        Total Periode Pengajuan
                    </p>

                    <p class="mt-1 text-2xl font-bold text-slate-800">
                        {{ $leaveRequest->total_days }} hari
                    </p>
                </div>
            </div>


            <div class="overflow-hidden rounded-xl border border-slate-200">
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
                                    Rekening
                                </th>

                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Jadwal
                                </th>
                            </tr>
                        </thead>


                        <tbody class="divide-y divide-slate-200">
                            @foreach($leaveRequest->substituteSchedules as $schedule)
                            @php
                            // Fallback ke kolom lama untuk data sebelum migration baru.
                            $scheduleName = $schedule->substitute_name
                            ?? $leaveRequest->substitute_name
                            ?? '-';

                            $scheduleWhatsapp = $schedule->substitute_whatsapp
                            ?? $leaveRequest->substitute_whatsapp
                            ?? '-';

                            $scheduleAddress = $schedule->substitute_address
                            ?? $leaveRequest->substitute_address
                            ?? '-';

                            $scheduleBank = $schedule->substitute_bank_name
                            ?? $leaveRequest->substitute_bank_name
                            ?? '-';

                            $scheduleAccount = $schedule->substitute_bank_account_number
                            ?? $leaveRequest->substitute_bank_account_number
                            ?? '-';

                            $scheduleHolder = $schedule->substitute_bank_account_holder
                            ?? $leaveRequest->substitute_bank_account_holder
                            ?? '-';
                            @endphp

                            <tr class="align-top">
                                <td class="whitespace-nowrap px-5 py-4">
                                    <p class="text-sm font-semibold text-slate-700">
                                        {{ $schedule->schedule_date->format('d/m/Y') }}
                                    </p>

                                    <p class="mt-1 text-xs text-slate-400">
                                        {{ $schedule->schedule_date->translatedFormat('l') }}
                                    </p>
                                </td>


                                <td class="min-w-[220px] px-5 py-4">
                                    <p class="font-semibold text-slate-800">
                                        {{ $scheduleName }}
                                    </p>

                                    <p class="mt-1 text-xs leading-relaxed text-slate-500">
                                        {{ $scheduleAddress }}
                                    </p>
                                </td>


                                <td class="whitespace-nowrap px-5 py-4">
                                    <p class="text-sm font-medium text-slate-800">
                                        {{ $scheduleWhatsapp }}
                                    </p>
                                </td>


                                <td class="min-w-[210px] px-5 py-4">
                                    <p class="text-sm font-semibold text-slate-800">
                                        {{ $scheduleBank }}
                                    </p>

                                    <p class="mt-1 text-xs text-slate-500">
                                        {{ $scheduleAccount }}
                                    </p>

                                    <p class="mt-1 text-xs text-slate-400">
                                        a.n. {{ $scheduleHolder }}
                                    </p>
                                </td>


                                <td class="min-w-[180px] px-5 py-4">
                                    @if($schedule->schedule_type === 'full_shift')
                                    <span class="inline-flex rounded-full bg-blue-50 px-2.5 py-1 text-xs font-medium text-blue-700">
                                        Full Shift
                                    </span>

                                    <p class="mt-2 text-sm font-semibold text-slate-800">
                                        {{ $schedule->workShift?->name ?? '-' }}
                                    </p>

                                    @if($schedule->workShift?->start_time && $schedule->workShift?->end_time)
                                    <p class="mt-1 text-xs text-slate-400">
                                        {{ substr($schedule->workShift->start_time, 0, 5) }}
                                        -
                                        {{ substr($schedule->workShift->end_time, 0, 5) }}
                                    </p>
                                    @endif

                                    @elseif($schedule->schedule_type === 'partial_hours')
                                    <span class="inline-flex rounded-full bg-purple-50 px-2.5 py-1 text-xs font-medium text-purple-700">
                                        Beberapa Jam
                                    </span>

                                    <p class="mt-2 text-sm font-semibold text-slate-800">
                                        {{ $schedule->start_time ? substr($schedule->start_time, 0, 5) : '-' }}
                                        -
                                        {{ $schedule->end_time ? substr($schedule->end_time, 0, 5) : '-' }}
                                    </p>
                                    @else
                                    <span class="text-sm text-slate-400">
                                        -
                                    </span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            @else

            {{-- Data lama: masih memakai kolom pengganti global. --}}
            <div class="rounded-xl border border-amber-200 bg-amber-50 p-5">
                <p class="font-semibold text-amber-800">
                    Data pengganti menggunakan format lama
                </p>

                <p class="mt-1 text-sm text-amber-700">
                    Pengajuan ini belum mempunyai data pengganti per tanggal. Informasi di bawah diambil dari kolom lama.
                </p>

                <div class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-amber-600">
                            Nama
                        </p>
                        <p class="mt-1 font-semibold text-slate-800">
                            {{ $leaveRequest->substitute_name ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs uppercase tracking-wide text-amber-600">
                            WhatsApp
                        </p>
                        <p class="mt-1 font-semibold text-slate-800">
                            {{ $leaveRequest->substitute_whatsapp ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs uppercase tracking-wide text-amber-600">
                            Bank / Rekening
                        </p>
                        <p class="mt-1 font-semibold text-slate-800">
                            {{ $leaveRequest->substitute_bank_name ?? '-' }}
                            -
                            {{ $leaveRequest->substitute_bank_account_number ?? '-' }}
                        </p>
                    </div>
                </div>
            </div>

            @endif
        </div>
    </div>


    {{-- ============================================================
        INFORMASI APPROVAL
    ============================================================ --}}
    @if(
    $leaveRequest->status
    !== 'pending'
    )

    <div
        class="overflow-hidden
                   rounded-xl
                   border border-slate-200
                   bg-white shadow-sm">

        <div
            class="border-b
                       border-slate-200
                       px-6 py-5">

            <h2
                class="font-semibold
                           text-slate-800">
                Informasi Persetujuan
            </h2>

        </div>


        <div class="p-6">

            <div
                class="grid grid-cols-1
                           gap-6 sm:grid-cols-2">

                {{-- Approver --}}
                <div>

                    <p
                        class="text-xs font-semibold
                                   uppercase tracking-wide
                                   text-slate-400">
                        Diproses Oleh
                    </p>

                    <p
                        class="mt-1 font-medium
                                   text-slate-800">

                        {{ $leaveRequest
                                ->approver
                                ?->name
                                ?? '-' }}

                    </p>

                </div>


                {{-- Waktu --}}
                <div>

                    <p
                        class="text-xs font-semibold
                                   uppercase tracking-wide
                                   text-slate-400">
                        Waktu Diproses
                    </p>


                    <p
                        class="mt-1 font-medium
                                   text-slate-800">

                        @if(
                        $leaveRequest->status
                        === 'approved'
                        )

                        {{ $leaveRequest
                                    ->approved_at
                                    ?->format(
                                        'd/m/Y H:i'
                                    )
                                    ?? '-' }}

                        @else

                        {{ $leaveRequest
                                    ->rejected_at
                                    ?->format(
                                        'd/m/Y H:i'
                                    )
                                    ?? '-' }}

                        @endif

                    </p>

                </div>

            </div>


            {{-- Rejection --}}
            @if(
            $leaveRequest->status
            === 'rejected'
            &&
            $leaveRequest
            ->rejection_reason
            )

            <div class="mt-6">

                <p
                    class="text-xs font-semibold
                                   uppercase tracking-wide
                                   text-slate-400">
                    Alasan Penolakan
                </p>

                <div
                    class="mt-2 rounded-lg
                                   bg-red-50
                                   px-4 py-3
                                   text-sm leading-relaxed
                                   text-red-700">

                    {{ $leaveRequest
                                ->rejection_reason }}

                </div>

            </div>

            @endif

        </div>

    </div>

    @endif



    {{-- ============================================================
        HASIL REVIEW KABID
    ============================================================ --}}
    @if(
    $leaveRequest->user?->role === 'karyawan'
    &&
    in_array(
    $leaveRequest->kabid_status,
    [
    \App\Models\LeaveRequest::KABID_STATUS_APPROVED,
    \App\Models\LeaveRequest::KABID_STATUS_REJECTED,
    ],
    true
    )
    )

    <div
        class="
                rounded-xl border p-5 shadow-sm
                {{
                    $leaveRequest->kabid_status
                        === \App\Models\LeaveRequest::KABID_STATUS_APPROVED
                        ? 'border-emerald-200 bg-emerald-50'
                        : 'border-red-200 bg-red-50'
                }}
            ">

        <div
            class="flex flex-col gap-4
                       sm:flex-row sm:items-start sm:justify-between">

            <div>

                <p
                    class="
                            text-xs font-semibold uppercase tracking-wider
                            {{
                                $leaveRequest->kabid_status
                                    === \App\Models\LeaveRequest::KABID_STATUS_APPROVED
                                    ? 'text-emerald-600'
                                    : 'text-red-600'
                            }}
                        ">
                    Hasil Pemeriksaan Tahap Kabid
                </p>

                <p
                    class="
                            mt-1 text-lg font-semibold
                            {{
                                $leaveRequest->kabid_status
                                    === \App\Models\LeaveRequest::KABID_STATUS_APPROVED
                                    ? 'text-emerald-800'
                                    : 'text-red-800'
                            }}
                        ">

                    {{
                            $leaveRequest->kabid_status
                                === \App\Models\LeaveRequest::KABID_STATUS_APPROVED
                                ? '✓ Disetujui Kabid'
                                : '✕ Ditolak Kabid'
                        }}

                </p>

                @if(
                $leaveRequest->kabid_status
                === \App\Models\LeaveRequest::KABID_STATUS_APPROVED
                )

                <p class="mt-1 text-sm text-emerald-700">
                    Pengajuan telah lolos tahap Kabid dan dapat diproses final oleh Admin.
                </p>

                @else

                <p class="mt-1 text-sm text-red-700">
                    Pengajuan tidak dilanjutkan ke tahap Admin.
                </p>

                @endif
            </div>


            <div
                class="rounded-lg bg-white/70
                           px-4 py-3 text-sm">

                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                    Diproses Oleh
                </p>

                <p class="mt-1 font-semibold text-slate-800">
                    {{ $leaveRequest->kabidReviewer?->name ?? 'Kabid' }}
                </p>

                <p class="mt-1 text-xs text-slate-500">
                    {{ $leaveRequest->kabidReviewer?->department?->name
                            ?? $leaveRequest->user?->department?->name
                            ?? '-'
                        }}
                </p>

                @if($leaveRequest->kabid_reviewed_at)
                <p class="mt-1 text-xs text-slate-500">
                    {{ $leaveRequest->kabid_reviewed_at->format('d/m/Y H:i') }}
                </p>
                @endif
            </div>
        </div>


        @if(
        $leaveRequest->kabid_status
        === \App\Models\LeaveRequest::KABID_STATUS_REJECTED
        &&
        $leaveRequest->kabid_rejection_reason
        )

        <div
            class="mt-4 rounded-lg border border-red-200
                           bg-white/70 px-4 py-3">

            <p class="text-xs font-semibold uppercase tracking-wide text-red-500">
                Alasan Penolakan Kabid
            </p>

            <p class="mt-1 whitespace-pre-line text-sm leading-6 text-red-700">
                {{ $leaveRequest->kabid_rejection_reason }}
            </p>
        </div>

        @endif
    </div>

    @endif


    @php
    /*
    * UI hanya membantu Admin memahami posisi workflow.
    * Proteksi utama tetap berada di Admin\LeaveRequestController.
    */
    $isKaryawanRequest =
    $leaveRequest->user?->role === 'karyawan';

    $waitingForKabid =
    $isKaryawanRequest
    && $leaveRequest->kabid_status
    === \App\Models\LeaveRequest::KABID_STATUS_PENDING;

    $approvedByKabid =
    ! $isKaryawanRequest
    || in_array(
    $leaveRequest->kabid_status,
    [
    \App\Models\LeaveRequest::KABID_STATUS_APPROVED,
    \App\Models\LeaveRequest::KABID_STATUS_NOT_REQUIRED,
    ],
    true
    );

    $rejectedByKabid =
    $isKaryawanRequest
    && $leaveRequest->kabid_status
    === \App\Models\LeaveRequest::KABID_STATUS_REJECTED;

    /*
    * Admin hanya boleh berinteraksi jika tahap Kabid sudah selesai
    * atau memang tidak diperlukan.
    */
    $adminStageLocked =
    ! $approvedByKabid;
    @endphp


    {{-- ============================================================
        WORKFLOW APPROVAL
    ============================================================ --}}
    @if(
    $leaveRequest->status === 'pending'
    && $isKaryawanRequest
    )

    <div
        class="rounded-xl border border-slate-200
                   bg-white p-5 shadow-sm">

        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">

            <div>

                <p
                    class="text-xs font-semibold uppercase
                               tracking-wider text-slate-400">
                    Alur Persetujuan
                </p>

                <h2 class="mt-1 font-semibold text-slate-800">
                    Kabid → Admin
                </h2>

                <p class="mt-1 text-sm leading-6 text-slate-500">

                    @if($waitingForKabid)

                    Pengajuan masih berada pada tahap Kabid.
                    Admin dapat melihat detail, tetapi belum dapat
                    menyetujui atau menolak.

                    @elseif($rejectedByKabid)

                    Pengajuan telah ditolak oleh Kabid sehingga
                    tidak dapat diproses lebih lanjut oleh Admin.

                    @else

                    Persetujuan Kabid sudah selesai.
                    Pengajuan siap diproses final oleh Admin.

                    @endif

                </p>
            </div>


            <div
                class="grid min-w-full grid-cols-2 gap-2
                           sm:min-w-[360px]">

                {{-- TAHAP KABID --}}
                <div
                    class="
                            rounded-xl border p-3
                            {{
                                $waitingForKabid
                                    ? 'border-amber-200 bg-amber-50'
                                    : (
                                        $rejectedByKabid
                                            ? 'border-red-200 bg-red-50'
                                            : 'border-emerald-200 bg-emerald-50'
                                    )
                            }}
                        ">

                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                        Tahap 1
                    </p>

                    <p
                        class="
                                mt-1 text-sm font-semibold
                                {{
                                    $waitingForKabid
                                        ? 'text-amber-700'
                                        : (
                                            $rejectedByKabid
                                                ? 'text-red-700'
                                                : 'text-emerald-700'
                                        )
                                }}
                            ">
                        Kabid
                    </p>

                    <p
                        class="
                                mt-0.5 text-xs
                                {{
                                    $waitingForKabid
                                        ? 'text-amber-600'
                                        : (
                                            $rejectedByKabid
                                                ? 'text-red-600'
                                                : 'text-emerald-600'
                                        )
                                }}
                            ">

                        @if($waitingForKabid)
                        ● Menunggu
                        @elseif($rejectedByKabid)
                        ✕ Ditolak
                        @else
                        ✓ Disetujui
                        @endif

                    </p>
                </div>


                {{-- TAHAP ADMIN --}}
                <div
                    class="
                            rounded-xl border p-3
                            {{
                                $adminStageLocked
                                    ? 'border-slate-200 bg-slate-100'
                                    : 'border-blue-200 bg-blue-50'
                            }}
                        ">

                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                        Tahap 2
                    </p>

                    <p
                        class="
                                mt-1 text-sm font-semibold
                                {{
                                    $adminStageLocked
                                        ? 'text-slate-500'
                                        : 'text-blue-700'
                                }}
                            ">
                        Admin
                    </p>

                    <p
                        class="
                                mt-0.5 text-xs
                                {{
                                    $adminStageLocked
                                        ? 'text-slate-400'
                                        : 'text-blue-600'
                                }}
                            ">
                        {{ $adminStageLocked
                                ? '🔒 Belum tersedia'
                                : '● Siap diproses'
                            }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    @endif


    {{-- ============================================================
        ACTION APPROVE / REJECT
    ============================================================ --}}
    @if(
    $leaveRequest->status
    === 'pending'
    )

    <div
        class="rounded-xl border
                   border-slate-200
                   bg-white p-6 shadow-sm">

        <div>

            <h2
                class="font-semibold
                       {{ $adminStageLocked
                            ? 'text-slate-500'
                            : 'text-slate-800'
                       }}">
                Proses Pengajuan Admin
            </h2>

            <p
                class="mt-1 text-sm
                       {{ $adminStageLocked
                            ? 'text-slate-400'
                            : 'text-slate-500'
                       }}">

                @if($waitingForKabid)
                Panel Admin dikunci sampai Kabid menyetujui pengajuan.
                @elseif($rejectedByKabid)
                Pengajuan telah ditolak Kabid dan tidak dapat diproses Admin.
                @else
                Periksa seluruh data sebelum menyetujui atau menolak pengajuan.
                @endif

            </p>

        </div>



        {{-- ====================================================
                WARNING CUTI
            ==================================================== --}}
        @if(
        $leaveRequest
        ->annual_leave_deducted_days
        > 0
        )

        <div
            class="mt-5 rounded-lg
                           border border-amber-200
                           bg-amber-50 p-4">

            <p
                class="text-sm font-semibold
                               text-amber-800">
                Perhatian
            </p>

            <p
                class="mt-1 text-sm
                               text-amber-700">

                Jika pengajuan ini disetujui,
                saldo cuti tahunan akan berkurang

                <strong>

                    {{ $leaveRequest
                                ->annual_leave_deducted_days }}

                    hari.

                </strong>

            </p>

        </div>

        @endif



        {{-- ====================================================
                WARNING PENGGANTI MANDIRI
            ==================================================== --}}
        @if($leaveRequest->self_replacement_days > 0)

        <div class="mt-5 rounded-lg border border-amber-200 bg-amber-50 p-4">
            <p class="text-sm font-semibold text-amber-800">
                Pengganti Mandiri
            </p>

            <p class="mt-1 text-sm leading-relaxed text-amber-700">
                Sebanyak <strong>{{ $leaveRequest->self_replacement_days }} hari</strong>
                menjadi tanggung jawab karyawan untuk mencari pengganti dan membayar pengganti secara pribadi.
            </p>

            <p class="mt-2 text-sm font-medium {{ $leaveRequest->self_replacement_consent ? 'text-emerald-700' : 'text-red-700' }}">
                {{ $leaveRequest->self_replacement_consent ? '✓ Konfirmasi karyawan sudah tercatat.' : 'Konfirmasi belum tercatat. Pengajuan tidak dapat disetujui.' }}
            </p>
        </div>

        @endif


        {{-- ====================================================
                WARNING HARI TIDAK DIBAYAR
            ==================================================== --}}
        @if(
        $leaveRequest
        ->unpaid_days
        > 0
        )

        <div
            class="mt-5 rounded-lg
                   border border-rose-200
                   bg-rose-50 p-4">

            <p
                class="text-sm font-semibold
                       text-rose-800">
                Ada Hari Tidak Dibayar
            </p>

            <p
                class="mt-1 text-sm
                       leading-relaxed
                       text-rose-700">

                Dari pengajuan ini,

                <strong>
                    {{ $leaveRequest->unpaid_days }} hari
                </strong>

                tidak tertutup oleh hak izin maupun sisa cuti tahunan
                dan akan dicatat sebagai hari tidak dibayar / potong gaji
                apabila pengajuan disetujui.

            </p>


            @if(
            $leaveRequest
            ->salary_deduction_consent
            )

            <div
                class="mt-3 rounded-lg
                       border border-emerald-200
                       bg-emerald-50
                       px-3 py-2">

                <p
                    class="text-sm font-medium
                           text-emerald-700">
                    ✓ Karyawan sudah memberikan persetujuan.
                </p>

                @if(
                $leaveRequest
                ->salary_deduction_consent_at
                )

                <p class="mt-1 text-xs text-emerald-600">

                    Tercatat pada

                    {{ $leaveRequest
                        ->salary_deduction_consent_at
                        ->format('d/m/Y H:i') }}.

                </p>

                @endif

            </div>

            @else

            <div
                class="mt-3 rounded-lg
                       border border-red-200
                       bg-white
                       px-3 py-2">

                <p
                    class="text-sm font-semibold
                           text-red-700">
                    Persetujuan karyawan belum tercatat.
                    Pengajuan tidak dapat disetujui.
                </p>

            </div>

            @endif

        </div>

        @endif



        {{-- ====================================================
                WARNING PENGGANTI
            ==================================================== --}}
        @if(
        $leaveRequest->has_substitute
        &&
        $leaveRequest
        ->substituteSchedules
        ->isEmpty()
        )

        <div
            class="mt-5 rounded-lg
                           border border-red-200
                           bg-red-50 p-4">

            <p
                class="text-sm font-semibold
                               text-red-700">
                Jadwal pengganti belum tersedia.
            </p>

            <p
                class="mt-1 text-sm
                               text-red-600">
                Pengajuan ini kemungkinan merupakan
                data lama sebelum sistem jadwal
                per tanggal diterapkan.
            </p>

        </div>

        @endif



        {{-- ====================================================
                APPROVE & REJECT
            ==================================================== --}}
        <div
            class="mt-6 grid
                       grid-cols-1 gap-6
                       md:grid-cols-2">

            {{-- APPROVE --}}
            <div
                class="
                    rounded-xl border p-5
                    {{
                        $adminStageLocked
                            ? 'border-slate-200 bg-slate-100'
                            : 'border-emerald-200 bg-emerald-50'
                    }}
                ">

                <h3
                    class="
                        font-semibold
                        {{
                            $adminStageLocked
                                ? 'text-slate-500'
                                : 'text-emerald-800'
                        }}
                    ">
                    Setujui Pengajuan
                </h3>

                <p
                    class="
                        mt-1 text-sm
                        {{
                            $adminStageLocked
                                ? 'text-slate-400'
                                : 'text-emerald-700'
                        }}
                    ">

                    {{ $adminStageLocked
                        ? 'Menunggu persetujuan Kabid terlebih dahulu.'
                        : 'Pengajuan akan ditandai sebagai disetujui.'
                    }}

                </p>


                <form
                    id="approveForm"
                    action="{{ route(
                            'admin.leave-requests.approve',
                            $leaveRequest
                        ) }}"
                    method="POST"
                    class="mt-5">

                    @csrf
                    @method('PUT')


                    <button
                        type="button"
                        id="openApproveModal"

                        @disabled(
                        $adminStageLocked
                        ||
                        (
                        $leaveRequest->unpaid_days > 0
                        && ! $leaveRequest->salary_deduction_consent
                        )
                        ||
                        (
                        $leaveRequest->self_replacement_days > 0
                        && ! $leaveRequest->self_replacement_consent
                        )
                        )

                        class="w-full rounded-lg
                        bg-emerald-600
                        px-4 py-3
                        text-sm font-medium
                        text-white
                        transition
                        hover:bg-emerald-700
                        focus:outline-none
                        focus:ring-2
                        focus:ring-emerald-500
                        focus:ring-offset-2
                        disabled:cursor-not-allowed
                        disabled:bg-slate-300
                        disabled:text-slate-500
                        disabled:hover:bg-slate-300">
                        Setujui Pengajuan
                    </button>

                </form>

            </div>



            {{-- REJECT --}}
            <div
                class="
                    rounded-xl border p-5
                    {{
                        $adminStageLocked
                            ? 'border-slate-200 bg-slate-100'
                            : 'border-red-200 bg-red-50'
                    }}
                ">

                <h3
                    class="
                        font-semibold
                        {{
                            $adminStageLocked
                                ? 'text-slate-500'
                                : 'text-red-800'
                        }}
                    ">
                    Tolak Pengajuan
                </h3>

                <p
                    class="
                        mt-1 text-sm
                        {{
                            $adminStageLocked
                                ? 'text-slate-400'
                                : 'text-red-700'
                        }}
                    ">

                    {{ $adminStageLocked
                        ? 'Fitur ini aktif setelah tahap Kabid selesai.'
                        : 'Tuliskan alasan jika pengajuan tidak dapat disetujui.'
                    }}

                </p>


                <form
                    id="rejectForm"
                    action="{{ route(
                            'admin.leave-requests.reject',
                            $leaveRequest
                        ) }}"
                    method="POST"
                    class="mt-5">

                    @csrf
                    @method('PUT')


                    <textarea
                        id="rejectionReason"
                        name="rejection_reason"
                        rows="4"
                        required
                        @disabled($adminStageLocked)

                        placeholder="{{
                            $adminStageLocked
                                ? 'Menunggu persetujuan Kabid...'
                                : 'Tuliskan alasan penolakan...'
                        }}"

                        class="
                            w-full rounded-lg text-sm
                            {{
                                $adminStageLocked
                                    ? 'cursor-not-allowed border-slate-200 bg-slate-200 text-slate-400'
                                    : 'border-red-200 bg-white focus:border-red-500 focus:ring-red-500'
                            }}
                        ">{{ old('rejection_reason') }}</textarea>


                    @error(
                    'rejection_reason'
                    )

                    <p
                        class="mt-1 text-sm
                                       text-red-600">
                        {{ $message }}
                    </p>

                    @enderror


                    <button
                        type="button"
                        id="openRejectModal"
                        @disabled($adminStageLocked)

                        class="
                            mt-3 w-full rounded-lg px-4 py-3
                            text-sm font-medium transition
                            {{
                                $adminStageLocked
                                    ? 'cursor-not-allowed bg-slate-300 text-slate-500'
                                    : 'bg-red-600 text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2'
                            }}
                        ">
                        Tolak Pengajuan
                    </button>

                </form>

            </div>

        </div>

    </div>

    @endif


    @if(
    $leaveRequest->status
    === 'pending'
    )

    {{-- ============================================================
            MODAL KONFIRMASI SETUJUI
        ============================================================ --}}
    <div
        id="approveModal"
        class="fixed inset-0 z-[100] hidden overflow-y-auto"
        role="dialog"
        aria-modal="true"
        aria-labelledby="approveModalTitle">
        <div
            id="approveModalBackdrop"
            class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm"></div>

        <div
            class="relative flex min-h-full items-end
                       justify-center p-0
                       sm:items-center sm:p-4">
            <div
                id="approveModalPanel"
                class="relative w-full translate-y-6
                           rounded-t-3xl bg-white opacity-0
                           shadow-2xl transition duration-200
                           sm:max-w-lg sm:scale-95
                           sm:rounded-2xl
                           max-h-[calc(100dvh-1rem)]
                           sm:max-h-[calc(100dvh-2rem)]
                           overflow-hidden">
                <div
                    class="max-h-[calc(100dvh-1rem)]
                               overflow-y-auto
                               sm:max-h-[calc(100dvh-2rem)]">
                    <div class="p-5 sm:p-6">
                        <div class="flex items-start gap-3 sm:gap-4">
                            <div
                                class="flex h-11 w-11 shrink-0
                                           items-center justify-center
                                           rounded-full bg-emerald-100
                                           text-emerald-600
                                           sm:h-12 sm:w-12">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 sm:h-7 sm:w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>

                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-semibold uppercase tracking-wider text-emerald-600">
                                    Konfirmasi
                                </p>
                                <h3 id="approveModalTitle" class="mt-1 text-lg font-bold leading-snug text-slate-900 sm:text-xl">
                                    Setujui pengajuan ini?
                                </h3>
                                <p class="mt-1.5 text-sm leading-relaxed text-slate-500">
                                    Pastikan seluruh informasi sudah diperiksa sebelum memberikan persetujuan.
                                </p>
                            </div>

                            <button type="button" id="closeApproveModal" class="shrink-0 rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600" aria-label="Tutup">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <div class="mt-5 overflow-hidden rounded-xl border border-slate-200 bg-slate-50">
                            <div class="grid grid-cols-1 divide-y divide-slate-200 sm:grid-cols-2 sm:divide-x sm:divide-y-0">
                                <div class="p-4">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Karyawan</p>
                                    <p class="mt-1 break-words font-semibold text-slate-800">{{ $leaveRequest->user->name }}</p>
                                    <p class="mt-0.5 text-xs text-slate-500">NIK {{ $leaveRequest->user->nik ?? '-' }}</p>
                                </div>
                                <div class="p-4">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Jenis Perizinan</p>
                                    <p class="mt-1 break-words font-semibold text-slate-800">{{ $leaveRequest->permissionType?->name ?? 'Perizinan' }}</p>
                                    <p class="mt-0.5 text-xs text-slate-500">{{ $leaveRequest->total_days }} hari</p>
                                </div>
                            </div>
                            <div class="border-t border-slate-200 p-4">
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Periode</p>
                                <p class="mt-1 text-sm font-medium text-slate-700">
                                    {{ $leaveRequest->start_date->format('d/m/Y') }}
                                    <span class="mx-1 text-slate-400">-</span>
                                    {{ $leaveRequest->end_date->format('d/m/Y') }}
                                </p>
                            </div>
                        </div>

                        @if($leaveRequest->annual_leave_deducted_days > 0)
                        <div class="mt-4 flex gap-3 rounded-xl border border-amber-200 bg-amber-50 p-4">
                            <div class="mt-0.5 shrink-0 text-amber-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.667 1.73-3L13.73 4c-.77-1.333-2.69-1.333-3.46 0L3.34 16c-.77 1.333.19 3 1.73 3z" />
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-amber-800">Saldo cuti akan berkurang</p>
                                <p class="mt-1 text-sm leading-relaxed text-amber-700">
                                    Persetujuan ini akan memotong <strong>{{ $leaveRequest->annual_leave_deducted_days }} hari</strong> dari saldo cuti tahunan.
                                </p>
                            </div>
                        </div>
                        @endif

                        @if($leaveRequest->self_replacement_days > 0)
                        <div class="mt-4 flex gap-3 rounded-xl border border-amber-200 bg-amber-50 p-4">
                            <div class="mt-0.5 shrink-0 text-amber-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-amber-800">
                                    {{ $leaveRequest->self_replacement_days }} hari pengganti mandiri
                                </p>
                                <p class="mt-1 text-sm leading-relaxed text-amber-700">
                                    Karyawan bertanggung jawab mencari pengganti sendiri dan menyelesaikan biaya pengganti secara pribadi.
                                </p>
                                <p class="mt-2 text-xs font-semibold {{ $leaveRequest->self_replacement_consent ? 'text-emerald-700' : 'text-red-700' }}">
                                    {{ $leaveRequest->self_replacement_consent ? '✓ Konfirmasi karyawan sudah tercatat' : 'Konfirmasi karyawan belum tercatat' }}
                                </p>
                            </div>
                        </div>
                        @endif

                        @if($leaveRequest->unpaid_days > 0)
                        <div class="mt-4 flex gap-3 rounded-xl border border-rose-200 bg-rose-50 p-4">
                            <div class="mt-0.5 shrink-0 text-rose-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M4.93 19h14.14A2 2 0 0020.8 16L13.73 4a2 2 0 00-3.46 0L3.2 16A2 2 0 004.93 19z" />
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-rose-800">
                                    {{ $leaveRequest->unpaid_days }} hari tidak dibayar
                                </p>
                                <p class="mt-1 text-sm leading-relaxed text-rose-700">
                                    Hari tersebut tidak tertutup oleh hak izin maupun sisa cuti tahunan
                                    dan akan dicatat sebagai hari tidak dibayar / potong gaji apabila pengajuan disetujui.
                                </p>

                                @if($leaveRequest->salary_deduction_consent)
                                <p class="mt-2 text-xs font-semibold text-emerald-700">
                                    ✓ Persetujuan karyawan sudah tercatat
                                    @if($leaveRequest->salary_deduction_consent_at)
                                    pada {{ $leaveRequest->salary_deduction_consent_at->format('d/m/Y H:i') }}
                                    @endif
                                </p>
                                @else
                                <p class="mt-2 text-xs font-semibold text-red-700">
                                    Persetujuan karyawan belum tercatat. Pengajuan tidak dapat disetujui.
                                </p>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>

                    <div class="sticky bottom-0 border-t border-slate-200 bg-white/95 p-4 backdrop-blur sm:p-5">
                        <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                            <button type="button" id="cancelApproveModal" class="inline-flex w-full items-center justify-center rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-300 sm:w-auto">
                                Batal
                            </button>
                            <button type="button" id="confirmApprove" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 sm:w-auto">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Ya, Setujui
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================
            MODAL KONFIRMASI TOLAK
        ============================================================ --}}
    <div
        id="rejectModal"
        class="fixed inset-0 z-[100] hidden overflow-y-auto"
        role="dialog"
        aria-modal="true"
        aria-labelledby="rejectModalTitle">
        <div id="rejectModalBackdrop" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm"></div>

        <div class="relative flex min-h-full items-end justify-center p-0 sm:items-center sm:p-4">
            <div
                id="rejectModalPanel"
                class="relative w-full translate-y-6 rounded-t-3xl bg-white opacity-0 shadow-2xl transition duration-200 sm:max-w-lg sm:scale-95 sm:rounded-2xl max-h-[calc(100dvh-1rem)] sm:max-h-[calc(100dvh-2rem)] overflow-hidden">
                <div class="max-h-[calc(100dvh-1rem)] overflow-y-auto sm:max-h-[calc(100dvh-2rem)]">
                    <div class="p-5 sm:p-6">
                        <div class="flex items-start gap-3 sm:gap-4">
                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-red-100 text-red-600 sm:h-12 sm:w-12">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 sm:h-7 sm:w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </div>

                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-semibold uppercase tracking-wider text-red-600">Konfirmasi</p>
                                <h3 id="rejectModalTitle" class="mt-1 text-lg font-bold leading-snug text-slate-900 sm:text-xl">Tolak pengajuan ini?</h3>
                                <p class="mt-1.5 text-sm leading-relaxed text-slate-500">
                                    Pengajuan akan ditandai sebagai ditolak dan alasan penolakan akan disimpan.
                                </p>
                            </div>

                            <button type="button" id="closeRejectModal" class="shrink-0 rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600" aria-label="Tutup">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <div class="mt-5 rounded-xl border border-red-200 bg-red-50 p-4">
                            <p class="text-xs font-semibold uppercase tracking-wide text-red-500">Alasan Penolakan</p>
                            <p id="rejectReasonPreview" class="mt-2 whitespace-pre-wrap break-words text-sm leading-relaxed text-red-800"></p>
                        </div>
                    </div>

                    <div class="sticky bottom-0 border-t border-slate-200 bg-white/95 p-4 backdrop-blur sm:p-5">
                        <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                            <button type="button" id="cancelRejectModal" class="inline-flex w-full items-center justify-center rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-300 sm:w-auto">
                                Batal
                            </button>
                            <button type="button" id="confirmReject" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-red-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 sm:w-auto">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Ya, Tolak
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const approveForm = document.getElementById('approveForm');
            const rejectForm = document.getElementById('rejectForm');
            const rejectionReason = document.getElementById('rejectionReason');

            const approveModal = document.getElementById('approveModal');
            const approvePanel = document.getElementById('approveModalPanel');
            const openApproveButton = document.getElementById('openApproveModal');
            const closeApproveButton = document.getElementById('closeApproveModal');
            const cancelApproveButton = document.getElementById('cancelApproveModal');
            const confirmApproveButton = document.getElementById('confirmApprove');
            const approveBackdrop = document.getElementById('approveModalBackdrop');

            const rejectModal = document.getElementById('rejectModal');
            const rejectPanel = document.getElementById('rejectModalPanel');
            const openRejectButton = document.getElementById('openRejectModal');
            const closeRejectButton = document.getElementById('closeRejectModal');
            const cancelRejectButton = document.getElementById('cancelRejectModal');
            const confirmRejectButton = document.getElementById('confirmReject');
            const rejectBackdrop = document.getElementById('rejectModalBackdrop');
            const rejectReasonPreview = document.getElementById('rejectReasonPreview');

            let submitting = false;

            function lockBody() {
                document.body.classList.add('overflow-hidden');
            }

            function unlockBody() {
                if (
                    approveModal?.classList.contains('hidden') &&
                    rejectModal?.classList.contains('hidden')
                ) {
                    document.body.classList.remove('overflow-hidden');
                }
            }

            function showModal(modal, panel, focusTarget) {
                if (!modal || !panel) {
                    return;
                }

                modal.classList.remove('hidden');
                lockBody();

                requestAnimationFrame(function() {
                    panel.classList.remove('translate-y-6', 'opacity-0', 'sm:scale-95');
                    panel.classList.add('translate-y-0', 'opacity-100', 'sm:scale-100');
                });

                setTimeout(function() {
                    focusTarget?.focus();
                }, 120);
            }

            function hideModal(modal, panel) {
                if (!modal || !panel || submitting) {
                    return;
                }

                panel.classList.remove('translate-y-0', 'opacity-100', 'sm:scale-100');
                panel.classList.add('translate-y-6', 'opacity-0', 'sm:scale-95');

                setTimeout(function() {
                    modal.classList.add('hidden');
                    unlockBody();
                }, 180);
            }

            function setLoading(button, text) {
                if (!button) {
                    return;
                }

                button.disabled = true;
                button.classList.add('cursor-not-allowed', 'opacity-70');
                button.innerHTML = `
                        <svg class="h-5 w-5 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                        ${text}
                    `;
            }

            openApproveButton?.addEventListener('click', function() {
                showModal(approveModal, approvePanel, confirmApproveButton);
            });

            closeApproveButton?.addEventListener('click', function() {
                hideModal(approveModal, approvePanel);
            });

            cancelApproveButton?.addEventListener('click', function() {
                hideModal(approveModal, approvePanel);
            });

            approveBackdrop?.addEventListener('click', function() {
                hideModal(approveModal, approvePanel);
            });

            confirmApproveButton?.addEventListener('click', function() {
                if (submitting || !approveForm) {
                    return;
                }

                submitting = true;
                setLoading(confirmApproveButton, 'Memproses...');
                approveForm.submit();
            });

            openRejectButton?.addEventListener('click', function() {
                if (!rejectForm || !rejectionReason) {
                    return;
                }

                if (!rejectForm.reportValidity()) {
                    return;
                }

                const reason = rejectionReason.value.trim();

                if (!reason) {
                    rejectionReason.focus();
                    return;
                }

                if (rejectReasonPreview) {
                    rejectReasonPreview.textContent = reason;
                }

                showModal(rejectModal, rejectPanel, confirmRejectButton);
            });

            closeRejectButton?.addEventListener('click', function() {
                hideModal(rejectModal, rejectPanel);
            });

            cancelRejectButton?.addEventListener('click', function() {
                hideModal(rejectModal, rejectPanel);
            });

            rejectBackdrop?.addEventListener('click', function() {
                hideModal(rejectModal, rejectPanel);
            });

            confirmRejectButton?.addEventListener('click', function() {
                if (submitting || !rejectForm) {
                    return;
                }

                submitting = true;
                setLoading(confirmRejectButton, 'Memproses...');
                rejectForm.submit();
            });

            document.addEventListener('keydown', function(event) {
                if (event.key !== 'Escape' || submitting) {
                    return;
                }

                if (approveModal && !approveModal.classList.contains('hidden')) {
                    hideModal(approveModal, approvePanel);
                    return;
                }

                if (rejectModal && !rejectModal.classList.contains('hidden')) {
                    hideModal(rejectModal, rejectPanel);
                }
            });
        });
    </script>

    @endif

</div>

@endsection