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
        @include('layouts.karyawan.sidebar')



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
            @include('layouts.karyawan.header')



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
            @include('layouts.karyawan.footer')

        </div>

    </div>



    {{-- ============================================================
        JAVASCRIPT
    ============================================================ --}}
    <script>
        /*
        |--------------------------------------------------------------------------
        | KARYAWAN SIDEBAR - OPEN
        |--------------------------------------------------------------------------
        */
        function openKaryawanSidebar() {

            const sidebar =
                document.getElementById(
                    'karyawanSidebar'
                );

            const overlay =
                document.getElementById(
                    'karyawanSidebarOverlay'
                );


            if (!sidebar || !overlay) {
                return;
            }


            /*
             * Tampilkan sidebar
             */
            sidebar.classList.remove(
                '-translate-x-full'
            );


            /*
             * Tampilkan overlay
             */
            overlay.classList.remove(
                'hidden'
            );


            /*
             * Disable scrolling pada halaman belakang
             */
            document.body.classList.add(
                'overflow-hidden'
            );

        }



        /*
        |--------------------------------------------------------------------------
        | KARYAWAN SIDEBAR - CLOSE
        |--------------------------------------------------------------------------
        */
        function closeKaryawanSidebar() {

            const sidebar =
                document.getElementById(
                    'karyawanSidebar'
                );

            const overlay =
                document.getElementById(
                    'karyawanSidebarOverlay'
                );


            if (!sidebar || !overlay) {
                return;
            }


            /*
             * Sembunyikan sidebar mobile
             */
            sidebar.classList.add(
                '-translate-x-full'
            );


            /*
             * Sembunyikan overlay
             */
            overlay.classList.add(
                'hidden'
            );


            /*
             * Aktifkan scroll kembali
             *
             * Jangan aktifkan apabila modal logout
             * sedang terbuka.
             */
            const logoutModal =
                document.getElementById(
                    'karyawanLogoutModal'
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
        | LOGOUT MODAL - OPEN
        |--------------------------------------------------------------------------
        */
        function openKaryawanLogoutModal() {

            const modal =
                document.getElementById(
                    'karyawanLogoutModal'
                );

            const content =
                document.getElementById(
                    'karyawanLogoutModalContent'
                );


            if (!modal || !content) {
                return;
            }


            /*
             * Jika sidebar mobile sedang terbuka,
             * tutup dahulu.
             */
            const sidebar =
                document.getElementById(
                    'karyawanSidebar'
                );

            const sidebarOverlay =
                document.getElementById(
                    'karyawanSidebarOverlay'
                );


            if (
                sidebar &&
                window.innerWidth < 1024
            ) {

                sidebar.classList.add(
                    '-translate-x-full'
                );

            }


            if (sidebarOverlay) {

                sidebarOverlay.classList.add(
                    'hidden'
                );

            }


            /*
             * Tampilkan modal
             */
            modal.classList.remove(
                'hidden'
            );

            modal.classList.add(
                'flex'
            );


            modal.setAttribute(
                'aria-hidden',
                'false'
            );


            /*
             * Disable scroll background
             */
            document.body.classList.add(
                'overflow-hidden'
            );


            /*
             * Reset modal terlebih dahulu
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
             * Jalankan animasi masuk
             */
            requestAnimationFrame(
                function() {

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

                }
            );

        }



        /*
        |--------------------------------------------------------------------------
        | LOGOUT MODAL - CLOSE
        |--------------------------------------------------------------------------
        */
        function closeKaryawanLogoutModal() {

            const modal =
                document.getElementById(
                    'karyawanLogoutModal'
                );

            const content =
                document.getElementById(
                    'karyawanLogoutModalContent'
                );


            if (!modal || !content) {
                return;
            }


            /*
             * Animasi keluar
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
            setTimeout(
                function() {

                    modal.classList.remove(
                        'flex'
                    );


                    modal.classList.add(
                        'hidden'
                    );


                    modal.setAttribute(
                        'aria-hidden',
                        'true'
                    );


                    document.body.classList.remove(
                        'overflow-hidden'
                    );

                },
                200
            );

        }



        /*
        |--------------------------------------------------------------------------
        | CLICK OUTSIDE LOGOUT MODAL
        |--------------------------------------------------------------------------
        */
        document.addEventListener(
            'click',
            function(event) {

                const modal =
                    document.getElementById(
                        'karyawanLogoutModal'
                    );


                if (
                    modal &&
                    event.target === modal
                ) {

                    closeKaryawanLogoutModal();

                }

            }
        );



        /*
        |--------------------------------------------------------------------------
        | ESC BUTTON
        |--------------------------------------------------------------------------
        */
        document.addEventListener(
            'keydown',
            function(event) {

                if (event.key !== 'Escape') {
                    return;
                }


                /*
                 * Cek modal logout
                 */
                const logoutModal =
                    document.getElementById(
                        'karyawanLogoutModal'
                    );


                if (
                    logoutModal &&
                    !logoutModal.classList.contains(
                        'hidden'
                    )
                ) {

                    closeKaryawanLogoutModal();

                    return;

                }


                /*
                 * Kalau modal tidak terbuka,
                 * tutup sidebar mobile.
                 */
                closeKaryawanSidebar();

            }
        );



        /*
        |--------------------------------------------------------------------------
        | WINDOW RESIZE
        |--------------------------------------------------------------------------
        */
        window.addEventListener(
            'resize',
            function() {

                /*
                 * Ketika masuk ukuran desktop
                 */
                if (window.innerWidth >= 1024) {

                    const overlay =
                        document.getElementById(
                            'karyawanSidebarOverlay'
                        );


                    if (overlay) {

                        overlay.classList.add(
                            'hidden'
                        );

                    }


                    /*
                     * Jangan aktifkan scroll apabila
                     * modal logout sedang terbuka.
                     */
                    const logoutModal =
                        document.getElementById(
                            'karyawanLogoutModal'
                        );


                    if (
                        !logoutModal ||
                        logoutModal.classList.contains(
                            'hidden'
                        )
                    ) {

                        document.body.classList.remove(
                            'overflow-hidden'
                        );

                    }

                }

            }
        );



        /*
        |--------------------------------------------------------------------------
        | PREVENT DOUBLE LOGOUT SUBMIT
        |--------------------------------------------------------------------------
        */
        document.addEventListener(
            'DOMContentLoaded',
            function() {

                const logoutForm =
                    document.getElementById(
                        'karyawanLogoutForm'
                    );


                if (!logoutForm) {
                    return;
                }


                logoutForm.addEventListener(
                    'submit',
                    function() {

                        const button =
                            document.getElementById(
                                'karyawanLogoutSubmitButton'
                            );

                        const text =
                            document.getElementById(
                                'karyawanLogoutButtonText'
                            );


                        /*
                         * Disable tombol supaya tidak
                         * diklik berkali-kali.
                         */
                        if (button) {

                            button.disabled = true;

                        }


                        /*
                         * Ganti text tombol ketika proses logout
                         */
                        if (text) {

                            text.textContent =
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