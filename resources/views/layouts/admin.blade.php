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
            SIDEBAR
        ======================================================== --}}
        @include('layouts.admin.sidebar')



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
                HEADER
            ==================================================== --}}
            @include('layouts.admin.header')



            {{-- ====================================================
                MAIN CONTENT
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
            @include('layouts.admin.footer')

        </div>

    </div>



    {{-- ============================================================
        MOBILE SIDEBAR SCRIPT
    ============================================================ --}}
    <script>
        function openAdminSidebar() {

            const sidebar =
                document.getElementById('adminSidebar');

            const overlay =
                document.getElementById('adminSidebarOverlay');


            if (!sidebar || !overlay) {
                return;
            }


            sidebar.classList.remove('-translate-x-full');

            overlay.classList.remove('hidden');

            document.body.classList.add('overflow-hidden');

        }


        function closeAdminSidebar() {

            const sidebar =
                document.getElementById('adminSidebar');

            const overlay =
                document.getElementById('adminSidebarOverlay');


            if (!sidebar || !overlay) {
                return;
            }


            /*
             * Sidebar desktop tetap muncul karena
             * terdapat lg:translate-x-0.
             *
             * Sedangkan di mobile kembali disembunyikan.
             */
            sidebar.classList.add('-translate-x-full');

            overlay.classList.add('hidden');

            document.body.classList.remove('overflow-hidden');

        }


        /*
         * Tutup sidebar ketika tombol Escape ditekan.
         */
        document.addEventListener('keydown', function(event) {

            if (event.key === 'Escape') {

                closeAdminSidebar();

            }

        });


        /*
         * Ketika ukuran browser berubah ke desktop,
         * bersihkan overlay dan body scroll lock.
         */
        window.addEventListener('resize', function() {

            if (window.innerWidth >= 1024) {

                const sidebar =
                    document.getElementById('adminSidebar');

                const overlay =
                    document.getElementById('adminSidebarOverlay');


                if (sidebar) {

                    sidebar.classList.add('-translate-x-full');

                }


                if (overlay) {

                    overlay.classList.add('hidden');

                }


                document.body.classList.remove('overflow-hidden');

            }

        });
    </script>
    <script>
        /*
    |--------------------------------------------------------------------------
    | Logout Modal
    |--------------------------------------------------------------------------
    */

        function openLogoutModal() {

            const modal =
                document.getElementById('logoutModal');

            const content =
                document.getElementById('logoutModalContent');


            if (!modal || !content) {
                return;
            }


            /*
             * Tampilkan background modal
             */
            modal.classList.remove('hidden');
            modal.classList.add('flex');

            modal.setAttribute('aria-hidden', 'false');


            /*
             * Matikan scroll halaman
             */
            document.body.classList.add('overflow-hidden');


            /*
             * Animation
             */
            requestAnimationFrame(() => {

                content.classList.remove(
                    'opacity-0',
                    'scale-95',
                    'translate-y-4'
                );

                content.classList.add(
                    'opacity-100',
                    'scale-100',
                    'translate-y-0'
                );

            });

        }



        function closeLogoutModal() {

            const modal =
                document.getElementById('logoutModal');

            const content =
                document.getElementById('logoutModalContent');


            if (!modal || !content) {
                return;
            }


            /*
             * Closing animation
             */
            content.classList.remove(
                'opacity-100',
                'scale-100',
                'translate-y-0'
            );

            content.classList.add(
                'opacity-0',
                'scale-95',
                'translate-y-4'
            );


            /*
             * Tunggu animasi selesai
             */
            setTimeout(() => {

                modal.classList.remove('flex');
                modal.classList.add('hidden');

                modal.setAttribute('aria-hidden', 'true');

                document.body.classList.remove('overflow-hidden');

            }, 200);

        }



        /*
        |--------------------------------------------------------------------------
        | Klik background untuk menutup
        |--------------------------------------------------------------------------
        */

        document.addEventListener('click', function(event) {

            const modal =
                document.getElementById('logoutModal');


            if (
                modal &&
                event.target === modal
            ) {

                closeLogoutModal();

            }

        });



        /*
        |--------------------------------------------------------------------------
        | Tombol ESC
        |--------------------------------------------------------------------------
        */

        document.addEventListener('keydown', function(event) {

            if (event.key === 'Escape') {

                closeLogoutModal();

            }

        });
    </script>


    {{-- Modal konfirmasi global untuk seluruh halaman Admin --}}
    <x-confirm-action-modal />

    {{-- Script tambahan dari halaman tertentu --}}
    @stack('scripts')

</body>

</html>