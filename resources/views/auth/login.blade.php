<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1">

    <meta
        name="csrf-token"
        content="{{ csrf_token() }}">


    <title>
        Login | SIMI-MS
    </title>


    @vite([
    'resources/css/app.css',
    'resources/js/app.js'
    ])

</head>


<body class="bg-slate-100 antialiased">

    <div
        class="
            min-h-screen
            flex
            items-center
            justify-center
            px-4
            py-8
        ">

        <div class="w-full max-w-md">


            {{-- ============================================================
                BRAND
            ============================================================ --}}
            <div class="mb-7 text-center">

                <div
                    class="
                        mx-auto
                        flex
                        h-14
                        w-14
                        items-center
                        justify-center
                        rounded-2xl
                        bg-blue-600
                        text-xl
                        font-bold
                        text-white
                        shadow-lg
                        shadow-blue-600/20
                    ">
                    S
                </div>


                <h1
                    class="
                        mt-4
                        text-xl
                        font-bold
                        text-slate-900
                    ">
                    SIMI-MS
                </h1>


                <p
                    class="
                        mt-1
                        text-sm
                        text-slate-500
                    ">
                    Sistem Informasi Manajemen Internal - Mitra Sehat
                </p>

            </div>



            {{-- ============================================================
                CARD LOGIN
            ============================================================ --}}
            <div
                class="
                    rounded-2xl
                    border
                    border-slate-200
                    bg-white
                    p-5
                    shadow-sm
                    sm:p-7
                ">


                {{-- HEADER --}}
                <div class="mb-6">

                    <h2
                        class="
                            text-2xl
                            font-bold
                            text-slate-900
                        ">
                        Masuk ke Sistem
                    </h2>


                    <p
                        class="
                            mt-2
                            text-sm
                            leading-6
                            text-slate-500
                        ">
                        Gunakan akun Google atau email dan password Anda.
                    </p>

                </div>



                {{-- ========================================================
                    SESSION STATUS
                ======================================================== --}}
                @if(session('status'))

                <div
                    class="
                            mb-5
                            rounded-xl
                            border
                            border-emerald-200
                            bg-emerald-50
                            px-4
                            py-3
                            text-sm
                            text-emerald-700
                        ">

                    {{ session('status') }}

                </div>

                @endif



                {{-- ========================================================
                    GOOGLE ERROR
                ======================================================== --}}
                @if($errors->has('google'))

                <div
                    class="
                            mb-5
                            rounded-xl
                            border
                            border-red-200
                            bg-red-50
                            px-4
                            py-3
                            text-sm
                            text-red-700
                        ">

                    {{ $errors->first('google') }}

                </div>

                @endif



                {{-- ========================================================
                    GOOGLE
                ======================================================== --}}
                <a
                    href="{{ route('google.redirect') }}"
                    class="
                        flex
                        w-full
                        items-center
                        justify-center
                        gap-3
                        rounded-xl
                        border
                        border-slate-300
                        bg-white
                        px-4
                        py-3
                        text-sm
                        font-semibold
                        text-slate-700
                        transition

                        hover:border-slate-400
                        hover:bg-slate-50

                        focus:outline-none
                        focus:ring-2
                        focus:ring-blue-500
                        focus:ring-offset-2
                    ">

                    <svg
                        class="h-5 w-5"
                        viewBox="0 0 24 24"
                        aria-hidden="true">

                        <path
                            fill="#4285F4"
                            d="M21.6 12.23c0-.71-.06-1.23-.2-1.77H12v3.4h5.52a4.72 4.72 0 0 1-2.05 3.09l-.02.11 2.97 2.3.21.02c1.93-1.78 2.97-4.4 2.97-7.15Z" />

                        <path
                            fill="#34A853"
                            d="M12 22c2.7 0 4.97-.89 6.63-2.42l-3.16-2.43c-.85.57-1.99.97-3.47.97-2.6 0-4.8-1.75-5.59-4.18l-.1.01-3.09 2.39-.04.09A10 10 0 0 0 12 22Z" />

                        <path
                            fill="#FBBC05"
                            d="M6.41 13.94A6.1 6.1 0 0 1 6.08 12c0-.67.12-1.32.32-1.94l-.01-.13-3.13-2.43-.1.05A10 10 0 0 0 2 12c0 1.61.38 3.14 1.18 4.45l3.23-2.51Z" />

                        <path
                            fill="#EA4335"
                            d="M12 5.88c1.88 0 3.15.81 3.88 1.49l2.82-2.75C16.97 3.01 14.7 2 12 2a10 10 0 0 0-8.84 5.55l3.24 2.51C7.2 7.63 9.4 5.88 12 5.88Z" />

                    </svg>


                    Masuk dengan Google

                </a>



                {{-- ========================================================
                    DIVIDER
                ======================================================== --}}
                <div
                    class="
                        my-6
                        flex
                        items-center
                        gap-3
                    ">

                    <div
                        class="
                            h-px
                            flex-1
                            bg-slate-200
                        "></div>


                    <span
                        class="
                            text-xs
                            text-slate-400
                        ">
                        atau
                    </span>


                    <div
                        class="
                            h-px
                            flex-1
                            bg-slate-200
                        "></div>

                </div>



                {{-- ========================================================
                    LOGIN FORM
                ======================================================== --}}
                <form
                    method="POST"
                    action="{{ route('login') }}"
                    class="space-y-5">

                    @csrf


                    {{-- EMAIL --}}
                    <div>

                        <label
                            for="email"
                            class="
                                mb-2
                                block
                                text-sm
                                font-medium
                                text-slate-700
                            ">
                            Email
                        </label>


                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            autocomplete="username"
                            placeholder="Masukkan email"
                            class="
                                block
                                w-full
                                rounded-xl
                                border-slate-300
                                px-4
                                py-3
                                text-sm
                                text-slate-800
                                shadow-sm

                                placeholder:text-slate-400

                                focus:border-blue-500
                                focus:ring-blue-500
                            ">


                        @error('email')

                        <p
                            class="
                                    mt-2
                                    text-sm
                                    text-red-600
                                ">
                            {{ $message }}
                        </p>

                        @enderror

                    </div>



                    {{-- PASSWORD --}}
                    <div>

                        <div
                            class="
                                mb-2
                                flex
                                items-center
                                justify-between
                            ">

                            <label
                                for="password"
                                class="
                                    text-sm
                                    font-medium
                                    text-slate-700
                                ">
                                Password
                            </label>


                            @if(Route::has('password.request'))

                            <a
                                href="{{ route('password.request') }}"
                                class="
                                        text-xs
                                        font-medium
                                        text-blue-600
                                        hover:text-blue-700
                                    ">
                                Lupa password?
                            </a>

                            @endif

                        </div>


                        <div class="relative">

                            <input
                                id="password"
                                type="password"
                                name="password"
                                required
                                autocomplete="current-password"
                                placeholder="Masukkan password"
                                class="
                                    block
                                    w-full
                                    rounded-xl
                                    border-slate-300
                                    px-4
                                    py-3
                                    pr-12
                                    text-sm
                                    text-slate-800
                                    shadow-sm

                                    placeholder:text-slate-400

                                    focus:border-blue-500
                                    focus:ring-blue-500
                                ">


                            <button
                                type="button"
                                onclick="togglePassword()"
                                class="
                                    absolute
                                    inset-y-0
                                    right-0
                                    flex
                                    items-center
                                    px-4
                                    text-slate-400
                                    hover:text-slate-600
                                "
                                aria-label="Tampilkan password">

                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor">

                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="1.8"
                                        d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6z" />

                                    <circle
                                        cx="12"
                                        cy="12"
                                        r="2.5" />

                                </svg>

                            </button>

                        </div>


                        @error('password')

                        <p
                            class="
                                    mt-2
                                    text-sm
                                    text-red-600
                                ">
                            {{ $message }}
                        </p>

                        @enderror

                    </div>



                    {{-- REMEMBER --}}
                    <label
                        for="remember_me"
                        class="
                            inline-flex
                            items-center
                            gap-2
                            text-sm
                            text-slate-600
                        ">

                        <input
                            id="remember_me"
                            type="checkbox"
                            name="remember"
                            class="
                                rounded
                                border-slate-300
                                text-blue-600
                                focus:ring-blue-500
                            ">

                        Ingat saya

                    </label>



                    {{-- SUBMIT --}}
                    <button
                        type="submit"
                        class="
                            w-full
                            rounded-xl
                            bg-blue-600
                            px-4
                            py-3
                            text-sm
                            font-semibold
                            text-white
                            transition

                            hover:bg-blue-700

                            focus:outline-none
                            focus:ring-2
                            focus:ring-blue-500
                            focus:ring-offset-2
                        ">
                        Masuk
                    </button>

                </form>

            </div>



            {{-- ============================================================
                FOOTER
            ============================================================ --}}
            <p
                class="
                    mt-6
                    text-center
                    text-xs
                    text-slate-400
                ">
                © {{ date('Y') }} SIMI-MS • Mitra Sehat
            </p>

        </div>

    </div>



    {{-- ============================================================
        SHOW / HIDE PASSWORD
    ============================================================ --}}
    <script>
        function togglePassword() {
            const password =
                document.getElementById('password');

            password.type =
                password.type === 'password' ?
                'text' :
                'password';
        }
    </script>

</body>

</html>