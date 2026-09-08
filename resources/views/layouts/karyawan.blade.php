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

        @include('layouts.karyawan.sidebar')


        <div
            class="
                flex
                min-h-screen
                min-w-0
                flex-col

                lg:ml-64
            ">

            @include('layouts.karyawan.header')


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

                @if(($hearYouLocked ?? false) && !request()->routeIs('karyawan.hear-you.*'))
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
                        <a href="{{ route('karyawan.hear-you.index') }}" class="inline-flex shrink-0 items-center justify-center rounded-xl bg-amber-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-amber-700">
                            Selesaikan Hear You
                        </a>
                    </div>
                </div>
                @endif

                @yield('content')

            </main>



            @include('layouts.karyawan.footer')

        </div>

    </div>



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


    <x-toast-notification />

    @stack('scripts')


</body>

</html>