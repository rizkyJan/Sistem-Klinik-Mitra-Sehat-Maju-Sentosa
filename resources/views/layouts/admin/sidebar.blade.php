{{-- ============================================================
    MOBILE OVERLAY
============================================================ --}}
<div
    id="adminSidebarOverlay"
    class="fixed inset-0 z-40 hidden bg-slate-900/50 backdrop-blur-[1px] lg:hidden"
    onclick="closeAdminSidebar()">
</div>


{{-- ============================================================
    SIDEBAR
============================================================ --}}
<aside
    id="adminSidebar"
    class="
        fixed inset-y-0 left-0 z-50
        flex w-72 max-w-[85vw] flex-col
        -translate-x-full
        bg-slate-900 text-white
        shadow-2xl
        transition-transform duration-300 ease-in-out

        lg:z-40
        lg:w-64
        lg:max-w-none
        lg:translate-x-0
        lg:shadow-none
    ">

    {{-- ============================================================
        BRAND / LOGO
    ============================================================ --}}
    <div
        class="
            flex h-16 shrink-0
            items-center justify-between
            border-b border-slate-800
            px-4 sm:px-6
        ">

        <div class="flex min-w-0 items-center gap-3">

            {{-- Logo --}}
            <div
                class="
                    flex h-9 w-9 shrink-0
                    items-center justify-center
                    rounded-lg
                    bg-blue-600
                    font-bold
                    text-white
                ">
                M
            </div>


            {{-- Brand --}}
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
                    MSMS
                </p>

            </div>

        </div>


        {{-- Close Sidebar Mobile --}}
        <button
            type="button"
            onclick="closeAdminSidebar()"
            class="
                flex h-9 w-9 shrink-0
                items-center justify-center
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


    {{-- ============================================================
        NAVIGATION
    ============================================================ --}}
    <nav
        class="
            flex-1
            overflow-y-auto
            overscroll-contain
            px-4
            py-5
        ">

        {{-- ========================================================
            MENU
        ======================================================== --}}
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
                href="{{ route('admin.dashboard') }}"
                onclick="closeAdminSidebar()"
                class="
                    flex items-center gap-3
                    rounded-lg px-3 py-2.5
                    text-sm font-medium transition
                    {{ request()->routeIs('admin.dashboard')
                        ? 'bg-blue-600 text-white shadow-sm'
                        : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}
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


        {{-- ========================================================
            MANAJEMEN DATA
        ======================================================== --}}
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
                Manajemen Data
            </p>

            <div class="space-y-1">

                {{-- Admin --}}
                @if(auth()->user()->role === 'admin')
                <a
                    href="{{ route('admin.admins.index') }}"
                    onclick="closeAdminSidebar()"
                    class="
                            flex items-center gap-3
                            rounded-lg px-3 py-2.5
                            text-sm font-medium transition
                            {{ request()->routeIs('admin.admins.*')
                                ? 'bg-blue-600 text-white shadow-sm'
                                : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}
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
                            d="M12 3l7 3v5c0 5-3 8.5-7 10-4-1.5-7-5-7-10V6l7-3z" />
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.5"
                            d="M9.5 11.5l1.5 1.5 3.5-3.5" />
                    </svg>

                    <span class="truncate">
                        Admin
                    </span>
                </a>
                @endif


                {{-- Karyawan --}}
                <a
                    href="{{ route('admin.karyawan.index') }}"
                    onclick="closeAdminSidebar()"
                    class="
                        flex items-center gap-3
                        rounded-lg px-3 py-2.5
                        text-sm font-medium transition
                        {{ request()->routeIs('admin.karyawan.*')
                            ? 'bg-blue-600 text-white shadow-sm'
                            : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}
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
                            d="M17 20h5v-2a4 4 0 00-4-4h-1M9 20H2v-2a4 4 0 014-4h1m6-4a4 4 0 100-8 4 4 0 000 8m-4 4a4 4 0 100 8 4 4 0 000-8z" />
                    </svg>

                    <span class="truncate">
                        Karyawan
                    </span>

                    <x-sidebar-notification-badge
                        :count="$pendingEmployeeVerificationCount ?? 0" />
                </a>


                {{-- Bidang --}}
                <a
                    href="{{ route('admin.departments.index') }}"
                    onclick="closeAdminSidebar()"
                    class="
                        flex items-center gap-3
                        rounded-lg px-3 py-2.5
                        text-sm font-medium transition
                        {{ request()->routeIs('admin.departments.*')
                            ? 'bg-blue-600 text-white shadow-sm'
                            : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}
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
                            d="M3 7h18M5 7v13h14V7M9 11h6" />
                    </svg>

                    <span class="truncate">
                        Bidang
                    </span>
                </a>

            </div>
        </div>


        {{-- ========================================================
            CUTI & PERIZINAN
        ======================================================== --}}
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

                {{-- Jatah Cuti --}}
                <a
                    href="{{ route('admin.leave-balances.index') }}"
                    onclick="closeAdminSidebar()"
                    class="
                        flex items-center gap-3
                        rounded-lg px-3 py-2.5
                        text-sm font-medium transition
                        {{ request()->routeIs('admin.leave-balances.*')
                            ? 'bg-blue-600 text-white shadow-sm'
                            : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}
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
                            d="M8 7V3m8 4V3M5 11h14M5 5h14a2 2 0 012 2v12a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2z" />
                    </svg>

                    <span class="truncate">
                        Jatah Cuti
                    </span>
                </a>


                {{-- Pengajuan Cuti --}}
                <a
                    href="{{ route('admin.leave-requests.index') }}"
                    onclick="closeAdminSidebar()"
                    class="
                        flex items-center gap-3
                        rounded-lg px-3 py-2.5
                        text-sm font-medium transition
                        {{ request()->routeIs('admin.leave-requests.*')
                            ? 'bg-blue-600 text-white shadow-sm'
                            : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}
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
                            d="M9 12h6M9 16h6M9 8h6M5 4h14a2 2 0 012 2v14H3V6a2 2 0 012-2z" />
                    </svg>

                    <span class="truncate">
                        Pengajuan Cuti
                    </span>

                    <x-sidebar-notification-badge
                        :count="$pendingLeaveRequestCount ?? 0" />
                </a>

            </div>
        </div>


        {{-- ========================================================
            KEUANGAN
        ======================================================== --}}
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

                {{-- Reimburse --}}
                <a
                    href="{{ route('admin.reimbursements.index') }}"
                    onclick="closeAdminSidebar()"
                    class="
                        flex items-center gap-3
                        rounded-lg px-3 py-2.5
                        text-sm font-medium transition
                        {{ request()->routeIs('admin.reimbursements.*')
                            ? 'bg-blue-600 text-white shadow-sm'
                            : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}
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
                            d="M3 7h18M5 4h14a2 2 0 012 2v12a2 2 0 01-2 2H5a2 2 0 01-2-2V6a2 2 0 012-2zm3 8h4" />
                    </svg>

                    <span class="truncate">
                        Reimburse
                    </span>

                    <x-sidebar-notification-badge
                        :count="$pendingReimbursementCount ?? 0" />
                </a>

            </div>
        </div>

    </nav>



    {{-- ============================================================
        USER PROFILE BOTTOM
    ============================================================ --}}
    <div
        class="
            shrink-0
            border-t
            border-slate-800
            p-4
        ">

        <div class="flex items-center gap-3">

            {{-- Avatar --}}
            <div
                class="
                    flex h-10 w-10 shrink-0
                    items-center justify-center
                    rounded-full
                    bg-slate-700
                    text-sm
                    font-semibold
                ">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>


            {{-- User Info --}}
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
                        capitalize
                        text-slate-400
                    ">
                    {{ auth()->user()->role }}
                </p>

            </div>

        </div>

    </div>

</aside>