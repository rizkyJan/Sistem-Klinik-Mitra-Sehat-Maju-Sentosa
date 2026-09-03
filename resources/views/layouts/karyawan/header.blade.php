<header
    class="
        sticky
        top-0
        z-30

        h-16

        border-b
        border-slate-200

        bg-white/95
        backdrop-blur
    ">

    <div
        class="
            flex
            h-full
            items-center
            justify-between
            gap-3

            px-4

            sm:px-6

            lg:px-8
        ">


        {{-- ========================================================
            LEFT
        ======================================================== --}}
        <div class="flex min-w-0 items-center gap-3">


            {{-- Hamburger Mobile --}}
            <button
                type="button"
                onclick="openKaryawanSidebar()"
                class="
                    flex
                    h-10
                    w-10
                    shrink-0
                    items-center
                    justify-center

                    rounded-lg

                    border
                    border-slate-200

                    bg-white

                    text-slate-600

                    transition

                    hover:bg-slate-50
                    hover:text-blue-600

                    lg:hidden
                "
                aria-label="Buka menu"
                aria-controls="karyawanSidebar">

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
                        d="M4 6h16M4 12h16M4 18h16" />

                </svg>

            </button>



            {{-- Page Title --}}
            <div class="min-w-0">

                <h2
                    class="
                        truncate
                        text-sm
                        font-semibold
                        text-slate-800

                        sm:text-base
                    ">

                    @yield('page-title', 'Dashboard')

                </h2>

            </div>

        </div>



        {{-- ========================================================
            RIGHT
        ======================================================== --}}
        <div
            class="
                flex
                shrink-0
                items-center
                gap-2

                sm:gap-4
            ">


            {{-- User Information + Avatar Profil --}}
            <a
                href="{{ route('profile.edit') }}"
                class="
                    group
                    flex
                    shrink-0
                    items-center
                    gap-2
                    rounded-lg
                    transition

                    hover:bg-slate-50

                    md:pl-2
                "
                title="Lihat / Edit Profil">

                {{-- User Information --}}
                <div class="hidden text-right md:block">

                    <p
                        class="
                            max-w-[180px]
                            truncate
                            text-sm
                            font-medium
                            text-slate-700

                            transition
                            group-hover:text-blue-700
                        ">

                        {{ auth()->user()->name }}

                    </p>


                    <p
                        class="
                            max-w-[180px]
                            truncate
                            text-xs
                            text-slate-500
                        ">

                        {{ auth()->user()->department?->name ?? 'Belum ada bidang' }}

                    </p>

                </div>


                {{-- Avatar Profil --}}
                <div
                    class="
                        flex
                        h-9
                        w-9
                        shrink-0
                        items-center
                        justify-center

                        overflow-hidden
                        rounded-full

                        border
                        border-slate-200

                        bg-blue-100

                        text-sm
                        font-semibold
                        text-blue-700

                        shadow-sm

                        sm:h-10
                        sm:w-10
                    ">

                    @if(auth()->user()->formal_photo_path)
                    <img
                        src="{{ route('profile.photo') }}"
                        alt="Foto profil {{ auth()->user()->name }}"
                        class="block h-full w-full object-cover object-center">
                    @else
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    @endif

                </div>

            </a>



            {{-- Divider --}}
            <div
                class="
                    hidden
                    h-7
                    w-px
                    bg-slate-200

                    sm:block
                ">
            </div>



            {{-- ====================================================
                LOGOUT BUTTON
            ==================================================== --}}
            <button
                type="button"
                onclick="openKaryawanLogoutModal()"
                class="
                    flex
                    h-9
                    items-center
                    justify-center
                    gap-2

                    rounded-lg

                    px-2

                    text-sm
                    font-medium
                    text-slate-500

                    transition

                    hover:bg-red-50
                    hover:text-red-600

                    sm:px-3
                "
                title="Logout">

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
                        d="M17 16l4-4m0 0l-4-4m4 4H7
                           m6 4v1a2 2 0 01-2 2H5
                           a2 2 0 01-2-2V7
                           a2 2 0 012-2h6
                           a2 2 0 012 2v1" />

                </svg>


                <span class="hidden sm:inline">
                    Logout
                </span>

            </button>

        </div>

    </div>

</header>



{{-- ================================================================
    LOGOUT CONFIRMATION MODAL
================================================================ --}}
<div
    id="karyawanLogoutModal"
    class="
        fixed
        inset-0
        z-[9999]

        hidden
        items-center
        justify-center

        bg-slate-900/60

        p-4

        backdrop-blur-sm
    "
    aria-hidden="true">


    {{-- Modal --}}
    <div
        id="karyawanLogoutModalContent"
        role="dialog"
        aria-modal="true"
        aria-labelledby="karyawanLogoutModalTitle"
        class="
            w-full
            max-w-sm

            translate-y-4
            scale-95

            overflow-hidden

            rounded-2xl

            bg-white

            opacity-0

            shadow-2xl

            transition-all
            duration-200
            ease-out
        ">


        <div class="px-5 pb-5 pt-6 sm:px-6 sm:pb-6">


            {{-- ====================================================
                ICON
            ==================================================== --}}
            <div
                class="
                    mx-auto

                    flex
                    h-16
                    w-16
                    items-center
                    justify-center

                    rounded-full

                    bg-red-50
                ">

                <div
                    class="
                        flex
                        h-12
                        w-12
                        items-center
                        justify-center

                        rounded-full

                        bg-red-100

                        text-red-600
                    ">

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-6 w-6"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.8"
                            d="M17 16l4-4m0 0l-4-4m4 4H7
                               m6 4v1a2 2 0 01-2 2H5
                               a2 2 0 01-2-2V7
                               a2 2 0 012-2h6
                               a2 2 0 012 2v1" />

                    </svg>

                </div>

            </div>



            {{-- ====================================================
                TEXT
            ==================================================== --}}
            <div class="mt-5 text-center">

                <h3
                    id="karyawanLogoutModalTitle"
                    class="
                        text-lg
                        font-bold
                        text-slate-800

                        sm:text-xl
                    ">

                    Keluar dari Sistem?

                </h3>


                <p
                    class="
                        mx-auto
                        mt-2
                        max-w-xs

                        text-sm
                        leading-6
                        text-slate-500
                    ">

                    Apakah Anda yakin ingin keluar dari akun

                    <span class="font-semibold text-slate-700">
                        {{ auth()->user()->name }}
                    </span>?

                </p>

            </div>



            {{-- ====================================================
                USER INFO
            ==================================================== --}}
            <div
                class="
                    mt-5

                    flex
                    items-center
                    gap-3

                    rounded-xl

                    border
                    border-slate-100

                    bg-slate-50

                    p-3
                ">

                {{-- Avatar --}}
                <div
                    class="
                        flex
                        h-10
                        w-10
                        shrink-0
                        items-center
                        justify-center

                        rounded-full

                        bg-blue-100

                        text-sm
                        font-semibold
                        text-blue-700
                    ">

                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}

                </div>


                <div class="min-w-0">

                    <p
                        class="
                            truncate
                            text-sm
                            font-semibold
                            text-slate-700
                        ">

                        {{ auth()->user()->name }}

                    </p>


                    <p
                        class="
                            truncate
                            text-xs
                            text-slate-500
                        ">

                        {{ auth()->user()->department?->name ?? 'Belum ada bidang' }}

                    </p>

                </div>

            </div>



            {{-- ====================================================
                BUTTONS
            ==================================================== --}}
            <div
                class="
                    mt-6
                    grid
                    grid-cols-2
                    gap-3
                ">


                {{-- Cancel --}}
                <button
                    type="button"
                    onclick="closeKaryawanLogoutModal()"
                    class="
                        inline-flex
                        items-center
                        justify-center

                        rounded-xl

                        border
                        border-slate-200

                        bg-white

                        px-3
                        py-2.5

                        text-sm
                        font-semibold
                        text-slate-600

                        shadow-sm

                        transition

                        hover:border-slate-300
                        hover:bg-slate-50
                        hover:text-slate-800

                        focus:outline-none
                        focus:ring-2
                        focus:ring-slate-300
                        focus:ring-offset-2

                        sm:px-4
                    ">

                    Batal

                </button>



                {{-- Logout Form --}}
                <form
                    id="karyawanLogoutForm"
                    action="{{ route('logout') }}"
                    method="POST"
                    class="w-full">

                    @csrf


                    <button
                        id="karyawanLogoutSubmitButton"
                        type="submit"
                        class="
                            inline-flex
                            w-full
                            items-center
                            justify-center
                            gap-2

                            rounded-xl

                            bg-red-600

                            px-3
                            py-2.5

                            text-sm
                            font-semibold
                            text-white

                            shadow-sm

                            transition

                            hover:bg-red-700

                            focus:outline-none
                            focus:ring-2
                            focus:ring-red-500
                            focus:ring-offset-2

                            disabled:cursor-not-allowed
                            disabled:opacity-60

                            sm:px-4
                        ">

                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-4 w-4"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor">

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7" />

                        </svg>


                        <span id="karyawanLogoutButtonText">
                            Ya, Keluar
                        </span>

                    </button>

                </form>

            </div>

        </div>

    </div>

</div>