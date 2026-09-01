{{-- ================================================================
    SIMI-MS GLOBAL TOAST NOTIFICATION

    Mendukung:
    - Flash session: success, error, warning, info
    - JavaScript: window.showSimiToast(message, type, title, duration)
    - type: success | error | warning | info
================================================================ --}}

@once
@php
$simiToastType = null;
$simiToastMessage = null;

foreach (['success', 'error', 'warning', 'info'] as $type) {
if (session()->has($type)) {
$simiToastType = $type;
$simiToastMessage = session($type);
break;
}
}
@endphp

<div
    id="simiToast"
    class="pointer-events-none invisible fixed right-4 top-4 z-[110]
           w-[calc(100%-2rem)] max-w-sm translate-x-8 opacity-0
           transition-all duration-300 sm:right-6 sm:top-6"
    role="status"
    aria-live="polite"
    aria-atomic="true">

    <div
        data-simi-toast-card
        class="pointer-events-auto overflow-hidden rounded-xl border border-slate-200 bg-white shadow-2xl">

        <div class="flex gap-3 p-4">

            <div
                data-simi-toast-icon-wrapper
                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-slate-50 text-slate-600">

                {{-- Success icon --}}
                <svg
                    data-simi-toast-icon="success"
                    xmlns="http://www.w3.org/2000/svg"
                    class="hidden h-5 w-5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M5 13l4 4L19 7" />
                </svg>

                {{-- Error icon --}}
                <svg
                    data-simi-toast-icon="error"
                    xmlns="http://www.w3.org/2000/svg"
                    class="hidden h-5 w-5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M6 18L18 6M6 6l12 12" />
                </svg>

                {{-- Warning icon --}}
                <svg
                    data-simi-toast-icon="warning"
                    xmlns="http://www.w3.org/2000/svg"
                    class="hidden h-5 w-5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 9v2m0 4h.01M10.29 3.86l-7.82 13.55A2 2 0 004.2 20h15.6a2 2 0 001.73-3l-7.82-13.55a2 2 0 00-3.42 0z" />
                </svg>

                {{-- Info icon --}}
                <svg
                    data-simi-toast-icon="info"
                    xmlns="http://www.w3.org/2000/svg"
                    class="hidden h-5 w-5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>

            <div class="min-w-0 flex-1">
                <p
                    data-simi-toast-title
                    class="text-sm font-semibold text-slate-900">
                    Informasi
                </p>

                <p
                    data-simi-toast-message
                    class="mt-1 break-words text-sm leading-relaxed text-slate-600">
                </p>
            </div>

            <button
                type="button"
                data-simi-toast-close
                class="shrink-0 self-start rounded-md p-1 text-slate-400 transition
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
            data-simi-toast-progress
            class="h-1 w-full bg-blue-500">
        </div>
    </div>
</div>

@push('scripts')
<script>
    (function() {
        const toast = document.getElementById('simiToast');

        if (!toast) {
            return;
        }

        const card = toast.querySelector('[data-simi-toast-card]');
        const iconWrapper = toast.querySelector('[data-simi-toast-icon-wrapper]');
        const icons = toast.querySelectorAll('[data-simi-toast-icon]');
        const titleElement = toast.querySelector('[data-simi-toast-title]');
        const messageElement = toast.querySelector('[data-simi-toast-message]');
        const closeButton = toast.querySelector('[data-simi-toast-close]');
        const progress = toast.querySelector('[data-simi-toast-progress]');

        const typeConfig = {
            success: {
                title: 'Berhasil',
                card: ['border-emerald-200'],
                icon: ['bg-emerald-50', 'text-emerald-600'],
                titleClass: ['text-emerald-900'],
                progress: ['bg-emerald-500'],
            },
            error: {
                title: 'Gagal',
                card: ['border-red-200'],
                icon: ['bg-red-50', 'text-red-600'],
                titleClass: ['text-red-900'],
                progress: ['bg-red-500'],
            },
            warning: {
                title: 'Perhatian',
                card: ['border-amber-200'],
                icon: ['bg-amber-50', 'text-amber-600'],
                titleClass: ['text-amber-900'],
                progress: ['bg-amber-500'],
            },
            info: {
                title: 'Informasi',
                card: ['border-blue-200'],
                icon: ['bg-blue-50', 'text-blue-600'],
                titleClass: ['text-blue-900'],
                progress: ['bg-blue-500'],
            },
        };

        const removableCardClasses = [
            'border-slate-200',
            'border-emerald-200',
            'border-red-200',
            'border-amber-200',
            'border-blue-200',
        ];

        const removableIconClasses = [
            'bg-slate-50',
            'text-slate-600',
            'bg-emerald-50',
            'text-emerald-600',
            'bg-red-50',
            'text-red-600',
            'bg-amber-50',
            'text-amber-600',
            'bg-blue-50',
            'text-blue-600',
        ];

        const removableTitleClasses = [
            'text-slate-900',
            'text-emerald-900',
            'text-red-900',
            'text-amber-900',
            'text-blue-900',
        ];

        const removableProgressClasses = [
            'bg-blue-500',
            'bg-emerald-500',
            'bg-red-500',
            'bg-amber-500',
        ];

        let closeTimer = null;
        let removeVisibilityTimer = null;

        function normaliseType(type) {
            return Object.prototype.hasOwnProperty.call(typeConfig, type) ?
                type :
                'info';
        }

        function applyType(type) {
            const safeType = normaliseType(type);
            const config = typeConfig[safeType];

            card.classList.remove(...removableCardClasses);
            card.classList.add(...config.card);

            iconWrapper.classList.remove(...removableIconClasses);
            iconWrapper.classList.add(...config.icon);

            titleElement.classList.remove(...removableTitleClasses);
            titleElement.classList.add(...config.titleClass);

            progress.classList.remove(...removableProgressClasses);
            progress.classList.add(...config.progress);

            icons.forEach(function(icon) {
                icon.classList.toggle(
                    'hidden',
                    icon.dataset.simiToastIcon !== safeType
                );
            });

            return config;
        }

        function hideToast() {
            if (closeTimer) {
                clearTimeout(closeTimer);
                closeTimer = null;
            }

            toast.classList.add('translate-x-8', 'opacity-0');
            toast.classList.remove('translate-x-0', 'opacity-100');

            removeVisibilityTimer = setTimeout(function() {
                toast.classList.add('invisible');
            }, 300);
        }

        window.showSimiToast = function(
            message,
            type = 'info',
            title = null,
            duration = 4500
        ) {
            if (!message) {
                return;
            }

            if (closeTimer) {
                clearTimeout(closeTimer);
            }

            if (removeVisibilityTimer) {
                clearTimeout(removeVisibilityTimer);
            }

            const safeType = normaliseType(type);
            const config = applyType(safeType);

            titleElement.textContent = title || config.title;
            messageElement.textContent = String(message);

            toast.classList.remove('invisible');

            progress.style.transition = 'none';
            progress.style.width = '100%';

            requestAnimationFrame(function() {
                toast.classList.remove('translate-x-8', 'opacity-0');
                toast.classList.add('translate-x-0', 'opacity-100');

                requestAnimationFrame(function() {
                    progress.style.transition = `width ${duration}ms linear`;
                    progress.style.width = '0%';
                });
            });

            closeTimer = setTimeout(hideToast, duration);
        };

        closeButton?.addEventListener('click', hideToast);

        @if($simiToastMessage)
        document.addEventListener('DOMContentLoaded', function() {
            window.showSimiToast(
                @json($simiToastMessage),
                @json($simiToastType)
            );
        });
        @endif
    })();
</script>
@endpush
@endonce