@extends('layouts.admin')

@section('title', 'Data Karyawan')

@section('page-title', 'Data Karyawan')

@section('content')

<div class="space-y-6">

    {{-- Header --}}
    <div
        class="flex flex-col gap-4
               sm:flex-row sm:items-center
               sm:justify-between">

        <div>

            <h1 class="text-2xl font-bold text-slate-800">
                Data Karyawan
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Kelola data dan verifikasi karyawan Mitra Sehat Maju Sentosa.
            </p>

        </div>


        <a
            href="{{ route('admin.karyawan.create') }}"
            class="inline-flex items-center justify-center
                   gap-2 rounded-lg bg-blue-600
                   px-4 py-2.5 text-sm font-medium
                   text-white hover:bg-blue-700">
            + Tambah Karyawan
        </a>

    </div>


    {{-- Alert --}}
    @if(session('success'))

    <div
        class="rounded-lg border
                   border-emerald-200
                   bg-emerald-50 px-4 py-3
                   text-sm text-emerald-700">
        {{ session('success') }}
    </div>

    @endif


    {{-- Card --}}
    <div
        class="overflow-hidden rounded-xl
               border border-slate-200
               bg-white shadow-sm">

        {{-- Search --}}
        <div class="border-b border-slate-200 p-5">

            <form
                method="GET"
                class="flex flex-col gap-3 sm:flex-row">

                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari nama, NIK, email, bidang..."
                    class="w-full max-w-xl
                           rounded-lg border-slate-300">

                @if(auth()->user()->role === 'admin')
                <select
                    name="role"
                    class="rounded-lg border-slate-300 text-sm">

                    <option value="">Semua Jenis Akun</option>
                    <option value="karyawan" @selected(request('role')==='karyawan' )>Karyawan</option>
                    <option value="kabid" @selected(request('role')==='kabid' )>Kabid</option>
                    <option value="admin" @selected(request('role')==='admin' )>Admin</option>

                </select>
                @endif


                <select
                    name="approval_status"
                    class="rounded-lg border-slate-300 text-sm">

                    <option value="">Semua Status Verifikasi</option>
                    <option value="pending" @selected(request('approval_status')==='pending' )>Menunggu Verifikasi</option>
                    <option value="approved" @selected(request('approval_status')==='approved' )>Disetujui</option>
                    <option value="rejected" @selected(request('approval_status')==='rejected' )>Ditolak</option>

                </select>

                <button
                    class="rounded-lg bg-slate-800
                           px-5 py-2.5 text-sm
                           font-medium text-white">
                    Cari
                </button>


                @if(request('search') || request('approval_status') || request('role'))

                <a
                    href="{{ route('admin.karyawan.index') }}"
                    class="rounded-lg border
                               border-slate-300
                               px-5 py-2.5 text-center
                               text-sm text-slate-600">
                    Reset
                </a>

                @endif

            </form>

        </div>


        <div class="overflow-x-auto">

            <table class="min-w-full divide-y divide-slate-200">

                <thead class="bg-slate-50">

                    <tr>

                        <th class="px-6 py-3 text-left text-xs uppercase text-slate-500">
                            No
                        </th>

                        <th class="px-6 py-3 text-left text-xs uppercase text-slate-500">
                            Karyawan
                        </th>

                        <th class="px-6 py-3 text-left text-xs uppercase text-slate-500">
                            NIK
                        </th>

                        <th class="px-6 py-3 text-left text-xs uppercase text-slate-500">
                            Bidang
                        </th>

                        <th class="px-6 py-3 text-left text-xs uppercase text-slate-500">
                            Mulai Kerja
                        </th>

                        <th class="px-6 py-3 text-left text-xs uppercase text-slate-500">
                            WhatsApp
                        </th>

                        <th class="px-6 py-3 text-left text-xs uppercase text-slate-500">
                            Verifikasi
                        </th>

                        <th class="px-6 py-3 text-left text-xs uppercase text-slate-500">
                            Status
                        </th>

                        <th class="px-6 py-3 text-right text-xs uppercase text-slate-500">
                            Aksi
                        </th>

                    </tr>

                </thead>


                <tbody class="divide-y divide-slate-200">

                    @forelse($karyawan as $item)

                    <tr class="hover:bg-slate-50">

                        <td class="px-6 py-4 text-sm text-slate-500">

                            {{ $karyawan->firstItem() + $loop->index }}

                        </td>


                        {{-- Employee --}}
                        <td class="px-6 py-4">

                            <div class="flex items-center gap-3">

                                <div
                                    class="flex h-10 w-10
                                               items-center justify-center
                                               rounded-full bg-blue-100
                                               font-semibold text-blue-700">
                                    {{ strtoupper(
                                            substr($item->name, 0, 1)
                                        ) }}
                                </div>


                                <div>

                                    <p class="text-sm font-semibold text-slate-800">
                                        {{ $item->name }}
                                    </p>

                                    <p class="text-xs text-slate-500">
                                        {{ $item->email }}
                                    </p>

                                    <div class="mt-1.5 flex flex-wrap gap-1">
                                        @if($item->role === 'admin')
                                        <span class="rounded-full bg-violet-100 px-2 py-0.5 text-[10px] font-semibold text-violet-700">Admin</span>
                                        @elseif($item->role === 'kabid')
                                        <span class="rounded-full bg-cyan-100 px-2 py-0.5 text-[10px] font-semibold text-cyan-700">Kabid</span>
                                        @else
                                        <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-600">Karyawan</span>
                                        @endif

                                        @if($item->google_id)
                                        <span class="rounded-full bg-blue-50 px-2 py-0.5 text-[10px] font-semibold text-blue-700">Google</span>
                                        @endif
                                    </div>

                                </div>

                            </div>

                        </td>


                        <td class="whitespace-nowrap px-6 py-4 text-sm">
                            {{ $item->nik ?? '-' }}
                        </td>


                        <td class="whitespace-nowrap px-6 py-4">

                            <span
                                class="rounded-full
                                           bg-indigo-50 px-2.5 py-1
                                           text-xs font-medium
                                           text-indigo-700">
                                {{ $item->department?->name ?? '-' }}
                            </span>

                        </td>


                        {{-- Join Date --}}
                        <td class="whitespace-nowrap px-6 py-4">

                            @if($item->join_date)

                            <p class="text-sm font-medium text-slate-700">
                                {{ $item->join_date->format('d/m/Y') }}
                            </p>

                            <p class="mt-1 text-xs text-slate-400">
                                {{ $item->join_date->diffForHumans(
                                            now(),
                                            true
                                        ) }}
                            </p>

                            @else

                            <span class="text-sm text-slate-400">
                                Belum diatur
                            </span>

                            @endif

                        </td>


                        <td class="whitespace-nowrap px-6 py-4 text-sm">
                            {{ $item->whatsapp ?? '-' }}
                        </td>


                        {{-- Verifikasi --}}
                        <td class="whitespace-nowrap px-6 py-4">

                            @if($item->role !== 'karyawan')

                            <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600">
                                Tidak perlu
                            </span>

                            @elseif($item->approval_status === 'pending')

                            <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-700">
                                <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                                Menunggu
                            </span>

                            @elseif($item->approval_status === 'approved')

                            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700">
                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                Disetujui
                            </span>

                            @elseif($item->approval_status === 'rejected')

                            <span class="inline-flex items-center gap-1.5 rounded-full bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-700">
                                <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>
                                Ditolak
                            </span>

                            @else

                            <span class="text-xs text-slate-400">-</span>

                            @endif

                        </td>


                        {{-- Status --}}
                        <td class="whitespace-nowrap px-6 py-4">

                            @if($item->is_active)

                            <span
                                class="rounded-full bg-emerald-50
                                               px-2.5 py-1 text-xs
                                               font-medium text-emerald-700">
                                Aktif
                            </span>

                            @else

                            <span
                                class="rounded-full bg-red-50
                                               px-2.5 py-1 text-xs
                                               font-medium text-red-700">
                                Nonaktif
                            </span>

                            @endif

                        </td>


                        {{-- Action --}}
                        <td class="whitespace-nowrap px-6 py-4 text-right">

                            <div class="flex justify-end gap-2">

                                @if(
                                auth()->user()->role === 'admin'
                                && in_array($item->approval_status, ['pending', 'rejected'], true)
                                && $item->profile_completed_at
                                )

                                <button
                                    type="button"
                                    class="rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white hover:bg-blue-700"
                                    data-name="{{ $item->name }}"
                                    data-email="{{ $item->email }}"
                                    data-nik="{{ $item->nik ?? '-' }}"
                                    data-whatsapp="{{ $item->whatsapp ?? '-' }}"
                                    data-department="{{ $item->department?->name ?? '-' }}"
                                    data-join-date="{{ $item->join_date?->format('d/m/Y') ?? '-' }}"
                                    data-approve-url="{{ route('admin.karyawan.approve', $item) }}"
                                    data-reject-url="{{ route('admin.karyawan.reject', $item) }}"
                                    onclick="openVerificationModal(this)">
                                    Verifikasi
                                </button>

                                @endif


                                <a
                                    href="{{ route(
                                            'admin.karyawan.edit',
                                            $item
                                        ) }}"
                                    class="rounded-lg
                                               bg-amber-50
                                               px-3 py-2
                                               text-xs font-medium
                                               text-amber-700
                                               hover:bg-amber-100">
                                    Edit
                                </a>


                                <form
                                    action="{{ route(
                                            'admin.karyawan.destroy',
                                            $item
                                        ) }}"
                                    method="POST"
                                    onsubmit="return confirm(
                                            'Yakin ingin menghapus karyawan ini?'
                                        )">

                                    @csrf
                                    @method('DELETE')


                                    <button
                                        type="submit"
                                        class="rounded-lg bg-red-50
                                                   px-3 py-2
                                                   text-xs font-medium
                                                   text-red-700
                                                   hover:bg-red-100">
                                        Hapus
                                    </button>

                                </form>

                            </div>

                        </td>

                    </tr>

                    @empty

                    <tr>

                        <td
                            colspan="9"
                            class="px-6 py-14
                                       text-center text-sm
                                       text-slate-500">
                            Data karyawan tidak ditemukan.
                        </td>

                    </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        @if($karyawan->hasPages())

        <div class="border-t border-slate-200 px-6 py-4">
            {{ $karyawan->links() }}
        </div>

        @endif

    </div>

</div>



{{-- ============================================================
    POPUP VERIFIKASI KARYAWAN
============================================================ --}}
<div id="verificationModal" class="fixed inset-0 z-[100] hidden overflow-y-auto overscroll-contain p-2 sm:p-4">

    <div class="absolute inset-0 bg-slate-950/60 backdrop-blur-sm" onclick="closeVerificationModal()"></div>

    <div class="relative z-10 mx-auto my-2 flex w-full max-w-lg flex-col overflow-hidden rounded-2xl bg-white shadow-2xl sm:my-6" style="max-height: calc(100dvh - 1rem);">

        <div class="shrink-0 bg-gradient-to-r from-blue-600 to-indigo-600 px-4 py-3.5 text-white sm:px-6 sm:py-5">
            <div class="flex items-start justify-between gap-4">

                <div>
                    <h3 class="text-base font-bold sm:text-lg">Verifikasi Karyawan</h3>
                    <p class="mt-1 text-xs leading-5 text-blue-100 sm:text-sm">Periksa data sebelum memberikan akses perizinan.</p>
                </div>

                <button type="button" onclick="closeVerificationModal()" class="rounded-lg p-2 text-white/80 hover:bg-white/10 hover:text-white">
                    X
                </button>

            </div>
        </div>

        <div id="verificationBody" class="min-h-0 flex-1 overflow-y-auto overscroll-contain p-3 sm:p-6">

            <div class="mb-3 flex min-w-0 items-center gap-3 sm:mb-5">
                <div id="modalInitial" class="flex h-12 w-12 items-center justify-center rounded-full bg-blue-100 text-lg font-bold text-blue-700">K</div>
                <div class="min-w-0 flex-1">
                    <p id="modalName" class="break-words font-semibold text-slate-800">-</p>
                    <p id="modalEmail" class="break-all text-xs text-slate-500 sm:text-sm">-</p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-2 rounded-xl border border-slate-200 bg-slate-50 p-3 sm:gap-3 sm:p-4">

                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">NIK</p>
                    <p id="modalNik" class="mt-1 break-words text-xs font-semibold text-slate-700 sm:text-sm">-</p>
                </div>

                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">Bidang</p>
                    <p id="modalDepartment" class="mt-1 break-words text-xs font-semibold text-slate-700 sm:text-sm">-</p>
                </div>

                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">WhatsApp</p>
                    <p id="modalWhatsapp" class="mt-1 break-words text-xs font-semibold text-slate-700 sm:text-sm">-</p>
                </div>

                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">Mulai Kerja</p>
                    <p id="modalJoinDate" class="mt-1 break-words text-xs font-semibold text-slate-700 sm:text-sm">-</p>
                </div>

            </div>

            <div class="mt-3 rounded-xl border border-amber-200 bg-amber-50 p-3 text-xs leading-5 text-amber-800 sm:mt-4 sm:p-4 sm:text-sm">
                Setelah di-ACC, akun langsung aktif dan karyawan dapat menggunakan fitur perizinan.
            </div>

            <div id="rejectBox" class="mt-3 hidden rounded-xl border border-red-200 bg-red-50 p-3 sm:mt-4 sm:p-4">

                <form id="rejectForm" method="POST">
                    @csrf
                    @method('PUT')

                    <label for="rejectReason" class="text-sm font-semibold text-red-800">
                        Alasan Penolakan
                    </label>

                    <textarea
                        id="rejectReason"
                        name="reason"
                        rows="3"
                        style="min-height: 96px; max-height: 180px; resize: vertical;"
                        maxlength="1000"
                        required
                        placeholder="Contoh: NIK belum sesuai, silakan perbaiki dan kirim ulang."
                        class="mt-2 block w-full rounded-lg border-red-200 bg-white px-3 py-2.5 text-sm leading-5 focus:border-red-400 focus:ring-red-400"></textarea>

                    <p class="mt-1 text-xs text-red-600">
                        Alasan ini akan terlihat oleh karyawan saat login kembali.
                    </p>

                    <div class="mt-3 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                        <button type="button" onclick="hideRejectBox()" class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50 sm:w-auto">
                            Batal
                        </button>

                        <button type="submit" class="w-full rounded-lg bg-red-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-red-700 sm:w-auto">
                            Kirim Penolakan
                        </button>
                    </div>
                </form>

            </div>

            <div id="verificationActions" class="mt-3 grid grid-cols-2 gap-2 border-t border-slate-100 pt-3 sm:mt-6 sm:gap-3 sm:pt-4">

                <button
                    type="button"
                    onclick="showRejectBox()"
                    class="rounded-xl border border-red-200 bg-red-50 px-3 py-2.5 text-xs font-semibold text-red-700 hover:bg-red-100 sm:px-4 sm:py-3 sm:text-sm">
                    Tolak Data
                </button>

                <button
                    type="button"
                    onclick="openApproveModal()"
                    class="rounded-xl bg-emerald-600 px-3 py-2.5 text-xs font-semibold text-white shadow-sm hover:bg-emerald-700 sm:px-4 sm:py-3 sm:text-sm">
                    ACC Karyawan
                </button>

            </div>

        </div>
    </div>
</div>


{{-- ============================================================
    POPUP KONFIRMASI ACC
============================================================ --}}
<div id="approveModal" class="fixed inset-0 z-[110] hidden overflow-y-auto overscroll-contain p-3 sm:p-4">

    <div class="absolute inset-0 bg-slate-950/60 backdrop-blur-sm" onclick="closeApproveModal()"></div>

    <div class="relative z-10 mx-auto my-4 w-full max-w-sm rounded-2xl bg-white p-4 text-center shadow-2xl sm:my-8 sm:p-6">

        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-emerald-100 text-3xl text-emerald-600">
            ✓
        </div>

        <h3 class="mt-4 text-lg font-bold text-slate-800">
            ACC Karyawan?
        </h3>

        <p class="mt-2 text-sm leading-6 text-slate-500">
            Akun <strong id="approveName" class="text-slate-700">karyawan</strong>
            akan langsung aktif dan dapat menggunakan sistem perizinan.
        </p>

        <div class="mt-6 grid grid-cols-2 gap-3">

            <button
                type="button"
                onclick="closeApproveModal()"
                class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50">
                Batal
            </button>

            <form id="approveForm" method="POST">
                @csrf
                @method('PUT')

                <button
                    type="submit"
                    class="w-full rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700">
                    Ya, ACC
                </button>
            </form>

        </div>
    </div>
</div>


<script>
    let selectedEmployee = null;

    function showPopup(element) {
        element.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function hidePopup(element) {
        element.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    function openVerificationModal(button) {
        selectedEmployee = {
            name: button.dataset.name,
            email: button.dataset.email,
            nik: button.dataset.nik,
            whatsapp: button.dataset.whatsapp,
            department: button.dataset.department,
            joinDate: button.dataset.joinDate,
            approveUrl: button.dataset.approveUrl,
            rejectUrl: button.dataset.rejectUrl
        };

        document.getElementById('modalName').textContent = selectedEmployee.name || '-';
        document.getElementById('modalEmail').textContent = selectedEmployee.email || '-';
        document.getElementById('modalNik').textContent = selectedEmployee.nik || '-';
        document.getElementById('modalWhatsapp').textContent = selectedEmployee.whatsapp || '-';
        document.getElementById('modalDepartment').textContent = selectedEmployee.department || '-';
        document.getElementById('modalJoinDate').textContent = selectedEmployee.joinDate || '-';
        document.getElementById('modalInitial').textContent = (selectedEmployee.name || 'K').charAt(0).toUpperCase();

        document.getElementById('approveForm').action = selectedEmployee.approveUrl;
        document.getElementById('rejectForm').action = selectedEmployee.rejectUrl;

        hideRejectBox();
        showPopup(document.getElementById('verificationModal'));
    }

    function closeVerificationModal() {
        hidePopup(document.getElementById('verificationModal'));
        hideRejectBox();
        selectedEmployee = null;
    }

    function showRejectBox() {
        const rejectBox = document.getElementById('rejectBox');
        const actions = document.getElementById('verificationActions');
        const body = document.getElementById('verificationBody');
        const reason = document.getElementById('rejectReason');

        rejectBox.classList.remove('hidden');
        actions.classList.add('hidden');

        setTimeout(function() {
            if (body) {
                body.scrollTo({
                    top: body.scrollHeight,
                    behavior: 'smooth'
                });
            }

            if (reason) {
                reason.focus({
                    preventScroll: true
                });
            }
        }, 100);
    }

    function hideRejectBox() {
        document.getElementById('rejectBox').classList.add('hidden');
        document.getElementById('verificationActions').classList.remove('hidden');
        document.getElementById('rejectReason').value = '';
    }

    function openApproveModal() {
        if (!selectedEmployee) {
            return;
        }

        document.getElementById('approveName').textContent = selectedEmployee.name || 'karyawan';
        showPopup(document.getElementById('approveModal'));
    }

    function closeApproveModal() {
        hidePopup(document.getElementById('approveModal'));
    }

    document.addEventListener('keydown', function(event) {
        if (event.key !== 'Escape') {
            return;
        }

        const approveModal = document.getElementById('approveModal');
        const verificationModal = document.getElementById('verificationModal');

        if (!approveModal.classList.contains('hidden')) {
            closeApproveModal();
            return;
        }

        if (!verificationModal.classList.contains('hidden')) {
            closeVerificationModal();
        }
    });
</script>

@endsection