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
                onclick="openKabidSidebar()"
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
                aria-controls="kabidSidebar">

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


            {{-- User Information --}}
            <div class="hidden text-right md:block">

                <p
                    class="
                        max-w-[180px]
                        truncate
                        text-sm
                        font-medium
                        text-slate-700
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

                    Kabid
                    •
                    {{ auth()->user()->department?->name ?? 'Belum ada bidang' }}

                </p>

            </div>


            {{-- Avatar Profil --}}
            <a
                href="{{ route('profile.edit') }}"
                class="
                    flex
                    h-9
                    w-9
                    shrink-0
                    items-center
                    justify-center
                    overflow-hidden
                    rounded-full
                    bg-blue-100
                    text-sm
                    font-semibold
                    text-blue-700
                    transition
                    hover:ring-2
                    hover:ring-blue-200
                    hover:ring-offset-2
                    sm:h-10
                    sm:w-10
                "
                title="Lihat / Edit Profil"
                aria-label="Lihat / Edit Profil {{ auth()->user()->name }}">

                @if(auth()->user()->formal_photo_path)
                <img
                    src="{{ route('profile.photo') }}"
                    alt="Foto profil {{ auth()->user()->name }}"
                    class="block h-full w-full object-cover object-center">
                @else
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                @endif

            </a>

            {{-- Logout --}}
            <button
                type="button"
                onclick="openKabidLogoutModal()"
                class="
                    flex
                    h-9
                    w-9
                    shrink-0
                    items-center
                    justify-center
                    rounded-lg
                    text-slate-500
                    transition
                    hover:bg-red-50
                    hover:text-red-600
                    sm:h-10
                    sm:w-auto
                    sm:gap-2
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
                        d="
                            M17 16l4-4m0 0l-4-4m4 4H7
                            m6 4v1a2 2 0 01-2 2H5
                            a2 2 0 01-2-2V7
                            a2 2 0 012-2h6
                            a2 2 0 012 2v1
                        " />

                </svg>


                <span
                    class="
                        hidden
                        text-sm
                        font-medium
                        sm:inline
                    ">
                    Logout
                </span>

            </button>

        </div>

    </div>

</header>



{{-- ================================================================
    LOGOUT MODAL
================================================================ --}}
<div
    id="kabidLogoutModal"
    class="
        fixed
        inset-0
        z-[100]
        hidden
        items-center
        justify-center
        bg-slate-900/60
        px-4
        backdrop-blur-sm
    "
    onclick="
        if (event.target === this) {
            closeKabidLogoutModal()
        }
    ">

    <div
        id="kabidLogoutModalContent"
        class="
            w-full
            max-w-md
            scale-95
            rounded-2xl
            bg-white
            p-6
            opacity-0
            shadow-2xl
            transition
            duration-150
            sm:p-7
        "
        role="dialog"
        aria-modal="true"
        aria-labelledby="kabidLogoutModalTitle">


        {{-- Icon --}}
        <div
            class="
                mx-auto
                flex
                h-14
                w-14
                items-center
                justify-center
                rounded-full
                bg-red-50
                text-red-600
            ">

            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-7 w-7"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor">

                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="1.7"
                    d="
                        M17 16l4-4m0 0l-4-4m4 4H7
                        m6 4v1a2 2 0 01-2 2H5
                        a2 2 0 01-2-2V7
                        a2 2 0 012-2h6
                        a2 2 0 012 2v1
                    " />

            </svg>

        </div>


        {{-- Content --}}
        <div class="mt-5 text-center">

            <h3
                id="kabidLogoutModalTitle"
                class="
                    text-lg
                    font-semibold
                    text-slate-800
                ">

                Keluar dari sistem?

            </h3>


            <p
                class="
                    mt-2
                    text-sm
                    leading-6
                    text-slate-500
                ">

                Anda akan keluar dari akun
                <span class="font-medium text-slate-700">
                    {{ auth()->user()->name }}
                </span>.

            </p>

        </div>


        {{-- Actions --}}
        <div
            class="
                mt-6
                flex
                flex-col-reverse
                gap-3
                sm:flex-row
                sm:justify-end
            ">

            <button
                type="button"
                onclick="closeKabidLogoutModal()"
                class="
                    inline-flex
                    items-center
                    justify-center
                    rounded-lg
                    border
                    border-slate-300
                    bg-white
                    px-4
                    py-2.5
                    text-sm
                    font-medium
                    text-slate-700
                    transition
                    hover:bg-slate-50
                ">

                Batal

            </button>


            <form
                id="kabidLogoutForm"
                action="{{ route('logout') }}"
                method="POST">

                @csrf

                <button
                    id="kabidLogoutSubmitButton"
                    type="submit"
                    class="
                        inline-flex
                        w-full
                        items-center
                        justify-center
                        gap-2
                        rounded-lg
                        bg-red-600
                        px-4
                        py-2.5
                        text-sm
                        font-medium
                        text-white
                        transition
                        hover:bg-red-700
                        sm:w-auto
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

                    <span id="kabidLogoutButtonText">
                        Ya, Keluar
                    </span>

                </button>

            </form>

        </div>

    </div>

</div>