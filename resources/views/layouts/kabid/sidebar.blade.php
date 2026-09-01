{{-- ============================================================
    MOBILE OVERLAY
============================================================ --}}
<div
    id="kabidSidebarOverlay"
    class="
        fixed
        inset-0
        z-40
        hidden
        bg-slate-900/50
        backdrop-blur-[1px]
        lg:hidden
    "
    onclick="closeKabidSidebar()">
</div>


{{-- ============================================================
    SIDEBAR
============================================================ --}}
<aside
    id="kabidSidebar"
    class="
        fixed
        inset-y-0
        left-0
        z-50
        flex
        w-72
        max-w-[85vw]
        flex-col
        -translate-x-full
        bg-slate-900
        text-white
        shadow-2xl
        transition-transform
        duration-300
        ease-in-out
        lg:z-40
        lg:w-64
        lg:max-w-none
        lg:translate-x-0
        lg:shadow-none
    ">

    {{-- BRAND --}}
    <div
        class="
            flex
            h-16
            shrink-0
            items-center
            justify-between
            border-b
            border-slate-800
            px-4
            sm:px-6
        ">

        <div class="flex min-w-0 items-center gap-3">

            <div
                class="
                    flex
                    h-9
                    w-9
                    shrink-0
                    items-center
                    justify-center
                    rounded-lg
                    bg-blue-600
                    font-bold
                    text-white
                ">
                M
            </div>

            <div class="min-w-0">
                <h1
                    class="
                        truncate
                        text-sm
                        font-semibold
                        leading-tight
                    ">
                    Sistem Perizinan
                </h1>

                <p class="text-xs text-slate-400">
                    MSMS • Kabid
                </p>
            </div>
        </div>

        <button
            type="button"
            onclick="closeKabidSidebar()"
            class="
                flex
                h-9
                w-9
                shrink-0
                items-center
                justify-center
                rounded-lg
                text-slate-400
                transition
                hover:bg-slate-800
                hover:text-white
                lg:hidden
            "
            aria-label="Tutup menu">

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
                    d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>


    {{-- NAVIGATION --}}
    <nav
        class="
            flex-1
            overflow-y-auto
            overscroll-contain
            px-4
            py-5
        ">

        {{-- MENU --}}
        <div>
            <p
                class="
                    mb-3
                    px-3
                    text-xs
                    font-semibold
                    uppercase
                    tracking-wider
                    text-slate-500
                ">
                Menu
            </p>

            <a
                href="{{ route('kabid.dashboard') }}"
                onclick="closeKabidSidebar()"
                class="
                    flex
                    items-center
                    gap-3
                    rounded-lg
                    px-3
                    py-2.5
                    text-sm
                    font-medium
                    transition
                    {{ request()->routeIs('kabid.dashboard')
                        ? 'bg-blue-600 text-white shadow-sm'
                        : 'text-slate-300 hover:bg-slate-800 hover:text-white'
                    }}
                ">

                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 shrink-0"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor">

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="1.5"
                        d="M3 12l9-9 9 9M5 10v10h14V10" />
                </svg>

                <span class="truncate">
                    Dashboard
                </span>
            </a>
        </div>


        {{-- CUTI & PERIZINAN --}}
        <div class="mt-8">

            <p
                class="
                    mb-3
                    px-3
                    text-xs
                    font-semibold
                    uppercase
                    tracking-wider
                    text-slate-500
                ">
                Cuti & Perizinan
            </p>

            <div class="space-y-1">

                {{-- Ajukan Cuti --}}
                <a
                    href="{{ route('kabid.leave-requests.create') }}"
                    onclick="closeKabidSidebar()"
                    class="
                        flex
                        items-center
                        gap-3
                        rounded-lg
                        px-3
                        py-2.5
                        text-sm
                        font-medium
                        transition
                        {{ request()->routeIs('kabid.leave-requests.create')
                            ? 'bg-blue-600 text-white shadow-sm'
                            : 'text-slate-300 hover:bg-slate-800 hover:text-white'
                        }}
                    ">

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5 shrink-0"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.5"
                            d="M8 7V3
                               m8 4V3
                               M5 11h14
                               M5 5h14
                               a2 2 0 012 2v12
                               a2 2 0 01-2 2H5
                               a2 2 0 01-2-2V7
                               a2 2 0 012-2z" />
                    </svg>

                    <span class="truncate">
                        Ajukan Cuti
                    </span>
                </a>


                {{-- Riwayat Cuti --}}
                <a
                    href="{{ route('kabid.leave-requests.index') }}"
                    onclick="closeKabidSidebar()"
                    class="
                        flex
                        items-center
                        gap-3
                        rounded-lg
                        px-3
                        py-2.5
                        text-sm
                        font-medium
                        transition
                        {{ request()->routeIs('kabid.leave-requests.index')
                            ? 'bg-blue-600 text-white shadow-sm'
                            : 'text-slate-300 hover:bg-slate-800 hover:text-white'
                        }}
                    ">

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5 shrink-0"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.5"
                            d="M9 12h6M9 16h6M9 8h6M5 4h14v16H5z" />
                    </svg>

                    <span class="truncate">
                        Riwayat Cuti
                    </span>
                </a>
            </div>
        </div>


        {{-- KHUSUS KABID --}}
        <div class="mt-8">

            <p
                class="
                    mb-3
                    px-3
                    text-xs
                    font-semibold
                    uppercase
                    tracking-wider
                    text-slate-500
                ">
                Kabid
            </p>

            <div class="space-y-1">

                {{-- Anggota Saya --}}
                <a
                    href="{{ route('kabid.members.index') }}"
                    onclick="closeKabidSidebar()"
                    class="
                        flex
                        items-center
                        gap-3
                        rounded-lg
                        px-3
                        py-2.5
                        text-sm
                        font-medium
                        transition
                        {{ request()->routeIs('kabid.members.*')
                            ? 'bg-blue-600 text-white shadow-sm'
                            : 'text-slate-300 hover:bg-slate-800 hover:text-white'
                        }}
                    ">

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5 shrink-0"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.5"
                            d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8zM22 21v-2a4 4 0 00-3-3.87" />
                    </svg>

                    <span class="truncate">
                        Anggota Saya
                    </span>
                </a>


                {{-- Persetujuan Izin --}}
                <a
                    href="{{ route('kabid.leave-approvals.index') }}"
                    onclick="closeKabidSidebar()"
                    class="
                        flex
                        items-center
                        gap-3
                        rounded-lg
                        px-3
                        py-2.5
                        text-sm
                        font-medium
                        transition
                        {{ request()->routeIs('kabid.leave-approvals.*')
                            ? 'bg-blue-600 text-white shadow-sm'
                            : 'text-slate-300 hover:bg-slate-800 hover:text-white'
                        }}
                    ">

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5 shrink-0"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.5"
                            d="M9 12l2 2 4-4M5 4h14v16H5z" />
                    </svg>

                    <span class="truncate">
                        Persetujuan Izin
                    </span>

                    @if(($pendingKabidLeaveApprovalCount ?? 0) > 0)

                    <span
                        class="
                                ml-auto
                                inline-flex
                                min-w-5
                                items-center
                                justify-center
                                rounded-full
                                bg-red-500
                                px-1.5
                                py-0.5
                                text-[11px]
                                font-bold
                                leading-none
                                text-white
                            ">
                        {{
                                ($pendingKabidLeaveApprovalCount ?? 0) > 99
                                    ? '99+'
                                    : $pendingKabidLeaveApprovalCount
                            }}
                    </span>

                    @endif
                </a>
            </div>
        </div>


        {{-- KEUANGAN --}}
        <div class="mt-8">

            <p
                class="
                    mb-3
                    px-3
                    text-xs
                    font-semibold
                    uppercase
                    tracking-wider
                    text-slate-500
                ">
                Keuangan
            </p>

            <div class="space-y-1">

                {{-- Ajukan Reimburse --}}
                <a
                    href="{{ route('kabid.reimbursements.create') }}"
                    onclick="closeKabidSidebar()"
                    class="
                        flex
                        items-center
                        gap-3
                        rounded-lg
                        px-3
                        py-2.5
                        text-sm
                        font-medium
                        transition
                        {{ request()->routeIs('kabid.reimbursements.create')
                            ? 'bg-blue-600 text-white shadow-sm'
                            : 'text-slate-300 hover:bg-slate-800 hover:text-white'
                        }}
                    ">

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5 shrink-0"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.5"
                            d="M12 6v12m6-6H6m-1-8h14a2 2 0 012 2v12a2 2 0 01-2 2H5a2 2 0 01-2-2V6a2 2 0 012-2z" />
                    </svg>

                    <span class="truncate">
                        Ajukan Reimburse
                    </span>
                </a>


                {{-- Riwayat Reimburse --}}
                <a
                    href="{{ route('kabid.reimbursements.index') }}"
                    onclick="closeKabidSidebar()"
                    class="
                        flex
                        items-center
                        gap-3
                        rounded-lg
                        px-3
                        py-2.5
                        text-sm
                        font-medium
                        transition
                        {{
                            request()->routeIs('kabid.reimbursements.index')
                            || request()->routeIs('kabid.reimbursements.show')
                            || request()->routeIs('kabid.reimbursements.edit')
                                ? 'bg-blue-600 text-white shadow-sm'
                                : 'text-slate-300 hover:bg-slate-800 hover:text-white'
                        }}
                    ">

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5 shrink-0"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.5"
                            d="M9 12h6m-6 4h6M7 4h10a2 2 0 012 2v14H5V6a2 2 0 012-2z" />
                    </svg>

                    <span class="truncate">
                        Riwayat Reimburse
                    </span>
                </a>
            </div>
        </div>
    </nav>


    {{-- USER PROFILE --}}
    <div
        class="
            shrink-0
            border-t
            border-slate-800
            p-4
        ">

        <div class="flex items-center gap-3">

            <div
                class="
                    flex
                    h-10
                    w-10
                    shrink-0
                    items-center
                    justify-center
                    rounded-full
                    bg-slate-700
                    text-sm
                    font-semibold
                ">

                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>

            <div class="min-w-0 flex-1">

                <p
                    class="
                        truncate
                        text-sm
                        font-medium
                        text-white
                    ">
                    {{ auth()->user()->name }}
                </p>

                <p
                    class="
                        truncate
                        text-xs
                        text-slate-400
                    ">
                    Kabid
                    •
                    {{ auth()->user()->department?->name ?? 'Belum ada bidang' }}
                </p>
            </div>
        </div>
    </div>

</aside>