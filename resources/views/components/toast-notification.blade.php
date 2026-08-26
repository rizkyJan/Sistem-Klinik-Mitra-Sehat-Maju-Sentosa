{{-- ================================================================
    REUSABLE TOAST NOTIFICATION
    Untuk session success / error.
================================================================ --}}

@php
$toastType = null;
$toastMessage = null;

if (session('success')) {
$toastType = 'success';
$toastMessage = session('success');
} elseif (session('error')) {
$toastType = 'error';
$toastMessage = session('error');
}
@endphp


@if($toastMessage)
<div
    id="reimbursementToast"
    class="fixed right-4 top-4 z-[110]
               w-[calc(100%-2rem)] max-w-sm
               translate-x-8 opacity-0
               transition-all duration-300 sm:right-6 sm:top-6">

    <div
        class="overflow-hidden rounded-xl border bg-white shadow-2xl
            {{
                $toastType === 'success'
                    ? 'border-emerald-200'
                    : 'border-red-200'
            }}">

        <div class="flex gap-3 p-4">

            <div
                class="flex h-10 w-10 shrink-0 items-center justify-center
                           rounded-full
                    {{
                        $toastType === 'success'
                            ? 'bg-emerald-50 text-emerald-600'
                            : 'bg-red-50 text-red-600'
                    }}">

                @if($toastType === 'success')
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
                        d="M5 13l4 4L19 7" />
                </svg>
                @else
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
                @endif
            </div>


            <div class="min-w-0 flex-1">
                <p
                    class="text-sm font-semibold
                        {{
                            $toastType === 'success'
                                ? 'text-emerald-900'
                                : 'text-red-900'
                        }}">
                    {{
                            $toastType === 'success'
                                ? 'Berhasil'
                                : 'Gagal'
                        }}
                </p>

                <p class="mt-1 text-sm leading-relaxed text-slate-600">
                    {{ $toastMessage }}
                </p>
            </div>


            <button
                type="button"
                data-toast-close
                class="shrink-0 self-start rounded-md
                           p-1 text-slate-400 transition
                           hover:bg-slate-100 hover:text-slate-600"
                aria-label="Tutup notifikasi">
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
                        d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>


        <div
            class="h-1
                {{
                    $toastType === 'success'
                        ? 'bg-emerald-500'
                        : 'bg-red-500'
                }}"
            data-toast-progress>
        </div>
    </div>
</div>


@once
@push('scripts')
<script>
    document.addEventListener(
        'DOMContentLoaded',
        function() {
            const toast =
                document.getElementById(
                    'reimbursementToast'
                );

            if (!toast) {
                return;
            }

            const closeButton =
                toast.querySelector(
                    '[data-toast-close]'
                );

            const progress =
                toast.querySelector(
                    '[data-toast-progress]'
                );

            let closeTimer = null;


            function showToast() {
                requestAnimationFrame(function() {
                    toast.classList.remove(
                        'translate-x-8',
                        'opacity-0'
                    );

                    toast.classList.add(
                        'translate-x-0',
                        'opacity-100'
                    );
                });

                if (progress) {
                    progress.style.transition =
                        'width 4.5s linear';

                    progress.style.width = '100%';

                    requestAnimationFrame(
                        function() {
                            progress.style.width = '0%';
                        }
                    );
                }

                closeTimer = setTimeout(
                    hideToast,
                    4500
                );
            }


            function hideToast() {
                if (closeTimer) {
                    clearTimeout(closeTimer);
                }

                toast.classList.add(
                    'translate-x-8',
                    'opacity-0'
                );

                toast.classList.remove(
                    'translate-x-0',
                    'opacity-100'
                );

                setTimeout(function() {
                    toast.remove();
                }, 300);
            }


            closeButton?.addEventListener(
                'click',
                hideToast
            );

            showToast();
        }
    );
</script>
@endpush
@endonce
@endif