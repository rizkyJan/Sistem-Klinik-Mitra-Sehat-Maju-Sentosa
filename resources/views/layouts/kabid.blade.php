<!DOCTYPE html>

<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>
        @hasSection('title')
        @yield('title') | SIMI-MS
        @else
        SIMI-MS
        @endif
    </title>

    {{-- Vite --}}
    @vite([
    'resources/css/app.css',
    'resources/js/app.js'
    ])

    <link rel="stylesheet" href="{{ asset('css/simi-theme.css') }}">

</head>


<body
    class="simi-theme 
        overflow-x-hidden
        bg-slate-50
        text-slate-800
        antialiased
    ">


    <div class="min-h-screen">


        {{-- ========================================================
            SIDEBAR KABID
        ======================================================== --}}
        @include('layouts.kabid.sidebar')


        {{-- ========================================================
            PAGE WRAPPER
        ======================================================== --}}
        <div
            class="
                flex
                min-h-screen
                min-w-0
                flex-col
                lg:ml-64
            ">


            {{-- ====================================================
                HEADER KABID
            ==================================================== --}}
            @include('layouts.kabid.header')


            {{-- ====================================================
                CONTENT
            ==================================================== --}}
            <main
                class="
                    min-w-0
                    flex-1
                    px-4
                    py-5
                    sm:px-6
                    sm:py-6
                    lg:px-8
                    lg:py-8
                ">

                @if(($hearYouLocked ?? false) && !request()->routeIs('kabid.hear-you.*'))
                <div class="mb-6 rounded-2xl border border-amber-200 bg-amber-50 p-5 shadow-sm">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-amber-100 text-amber-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M5.07 19h13.86a2 2 0 001.74-3L13.74 4a2 2 0 00-3.48 0L3.33 16a2 2 0 001.74 3z" />
                                </svg>
                            </div>
                            <div>
                                <p class="font-bold text-amber-950">Kewajiban Hear You belum selesai</p>
                                <p class="mt-1 text-sm leading-relaxed text-amber-800">
                                    Anda memiliki {{ $hearYouObligationCount ?? 1 }} kewajiban. Fitur operasional sementara dikunci sampai Hear You bulan ini dan evaluasi yang tertunda diselesaikan.
                                </p>
                            </div>
                        </div>
                        <a href="{{ route('kabid.hear-you.index') }}" class="inline-flex shrink-0 items-center justify-center rounded-xl bg-amber-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-amber-700">
                            Selesaikan Hear You
                        </a>
                    </div>
                </div>
                @endif

                @yield('content')

            </main>


            {{-- ====================================================
                FOOTER
            ==================================================== --}}
            @include('layouts.kabid.footer')

        </div>

    </div>


    {{-- ============================================================
        JAVASCRIPT KABID
    ============================================================ --}}
    <script>
        /*
        |--------------------------------------------------------------------------
        | SIDEBAR KABID - OPEN
        |--------------------------------------------------------------------------
        */
        function openKabidSidebar() {
            const sidebar =
                document.getElementById('kabidSidebar');

            const overlay =
                document.getElementById('kabidSidebarOverlay');


            if (!sidebar || !overlay) {
                return;
            }


            sidebar.classList.remove(
                '-translate-x-full'
            );


            overlay.classList.remove(
                'hidden'
            );


            document.body.classList.add(
                'overflow-hidden'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | SIDEBAR KABID - CLOSE
        |--------------------------------------------------------------------------
        */
        function closeKabidSidebar() {
            const sidebar =
                document.getElementById('kabidSidebar');

            const overlay =
                document.getElementById('kabidSidebarOverlay');


            if (!sidebar || !overlay) {
                return;
            }


            sidebar.classList.add(
                '-translate-x-full'
            );


            overlay.classList.add(
                'hidden'
            );


            const logoutModal =
                document.getElementById(
                    'kabidLogoutModal'
                );


            if (
                !logoutModal ||
                logoutModal.classList.contains('hidden')
            ) {
                document.body.classList.remove(
                    'overflow-hidden'
                );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | MODAL LOGOUT - OPEN
        |--------------------------------------------------------------------------
        */
        function openKabidLogoutModal() {
            const modal =
                document.getElementById(
                    'kabidLogoutModal'
                );

            const content =
                document.getElementById(
                    'kabidLogoutModalContent'
                );


            if (!modal || !content) {
                return;
            }


            /*
             * Tutup sidebar mobile jika sedang terbuka.
             */
            closeKabidSidebar();


            modal.classList.remove(
                'hidden'
            );


            modal.classList.add(
                'flex'
            );


            document.body.classList.add(
                'overflow-hidden'
            );


            /*
             * Animasi masuk.
             */
            requestAnimationFrame(() => {

                content.classList.remove(
                    'scale-95',
                    'opacity-0'
                );

                content.classList.add(
                    'scale-100',
                    'opacity-100'
                );

            });
        }


        /*
        |--------------------------------------------------------------------------
        | MODAL LOGOUT - CLOSE
        |--------------------------------------------------------------------------
        */
        function closeKabidLogoutModal() {
            const modal =
                document.getElementById(
                    'kabidLogoutModal'
                );

            const content =
                document.getElementById(
                    'kabidLogoutModalContent'
                );


            if (!modal || !content) {
                return;
            }


            content.classList.remove(
                'scale-100',
                'opacity-100'
            );


            content.classList.add(
                'scale-95',
                'opacity-0'
            );


            setTimeout(() => {

                modal.classList.add(
                    'hidden'
                );

                modal.classList.remove(
                    'flex'
                );


                document.body.classList.remove(
                    'overflow-hidden'
                );

            }, 150);
        }


        /*
        |--------------------------------------------------------------------------
        | ESC KEY
        |--------------------------------------------------------------------------
        */
        document.addEventListener(
            'keydown',
            function(event) {

                if (event.key !== 'Escape') {
                    return;
                }


                const logoutModal =
                    document.getElementById(
                        'kabidLogoutModal'
                    );


                if (
                    logoutModal &&
                    !logoutModal.classList.contains('hidden')
                ) {
                    closeKabidLogoutModal();

                    return;
                }


                closeKabidSidebar();
            }
        );


        /*
        |--------------------------------------------------------------------------
        | RESIZE
        |--------------------------------------------------------------------------
        | Saat layar kembali ke desktop, bersihkan state sidebar mobile.
        */
        window.addEventListener(
            'resize',
            function() {

                if (window.innerWidth < 1024) {
                    return;
                }


                const overlay =
                    document.getElementById(
                        'kabidSidebarOverlay'
                    );


                if (overlay) {
                    overlay.classList.add(
                        'hidden'
                    );
                }


                document.body.classList.remove(
                    'overflow-hidden'
                );
            }
        );


        /*
        |--------------------------------------------------------------------------
        | FORM LOGOUT
        |--------------------------------------------------------------------------
        */
        document.addEventListener(
            'DOMContentLoaded',
            function() {

                const form =
                    document.getElementById(
                        'kabidLogoutForm'
                    );

                const button =
                    document.getElementById(
                        'kabidLogoutSubmitButton'
                    );

                const buttonText =
                    document.getElementById(
                        'kabidLogoutButtonText'
                    );


                if (!form) {
                    return;
                }


                form.addEventListener(
                    'submit',
                    function() {

                        if (button) {

                            button.disabled = true;

                            button.classList.add(
                                'cursor-not-allowed',
                                'opacity-70'
                            );
                        }


                        if (buttonText) {

                            buttonText.textContent =
                                'Keluar...';
                        }

                    }
                );

            }
        );
    </script>


    {{-- Notifikasi global SIMI-MS --}}
    <x-toast-notification />

    {{-- Script tambahan dari halaman tertentu --}}
    @stack('scripts')

</body>

</html>