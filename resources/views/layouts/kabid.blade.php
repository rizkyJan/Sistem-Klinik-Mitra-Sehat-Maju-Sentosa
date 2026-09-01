<!DOCTYPE html>

<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>
        @yield('title', 'Sistem Perizinan MSMS')
    </title>

    {{-- Vite --}}
    @vite([
    'resources/css/app.css',
    'resources/js/app.js'
    ])

</head>


<body
    class="
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


    @stack('scripts')

</body>

</html>