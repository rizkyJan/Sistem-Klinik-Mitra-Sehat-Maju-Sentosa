{{-- ================================================================
    REUSABLE CONFIRMATION MODAL
    Menggantikan browser confirm() bawaan.
================================================================ --}}

<div
    id="reimbursementConfirmModal"
    class="fixed inset-0 z-[100] hidden"
    aria-hidden="true">

    {{-- Backdrop --}}
    <div
        data-confirm-backdrop
        class="absolute inset-0 bg-slate-950/50 backdrop-blur-[2px]
               transition-opacity duration-200">
    </div>

    {{-- Modal wrapper --}}
    <div
        class="relative flex min-h-full items-center justify-center
               p-4 sm:p-6">

        <div
            data-confirm-panel
            role="dialog"
            aria-modal="true"
            aria-labelledby="reimbursementConfirmTitle"
            aria-describedby="reimbursementConfirmMessage"
            class="w-full max-w-md translate-y-3 scale-[0.98]
                   rounded-2xl border border-slate-200 bg-white
                   p-6 opacity-0 shadow-2xl
                   transition-all duration-200">

            {{-- Icon --}}
            <div
                id="reimbursementConfirmIcon"
                class="flex h-12 w-12 items-center justify-center
                       rounded-full bg-blue-50 text-blue-600">

                <svg
                    data-icon="info"
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-6 w-6"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01
                           M21 12a9 9 0 11-18 0
                           9 9 0 0118 0z" />
                </svg>

                <svg
                    data-icon="success"
                    xmlns="http://www.w3.org/2000/svg"
                    class="hidden h-6 w-6"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M5 13l4 4L19 7" />
                </svg>

                <svg
                    data-icon="danger"
                    xmlns="http://www.w3.org/2000/svg"
                    class="hidden h-6 w-6"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M6 18L18 6M6 6l12 12" />
                </svg>

                <svg
                    data-icon="warning"
                    xmlns="http://www.w3.org/2000/svg"
                    class="hidden h-6 w-6"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 9v3m0 4h.01
                           M10.29 3.86L1.82 18a2 2 0
                           001.71 3h16.94a2 2 0
                           001.71-3L13.71 3.86a2 2 0
                           00-3.42 0z" />
                </svg>
            </div>


            {{-- Text --}}
            <div class="mt-5">
                <h3
                    id="reimbursementConfirmTitle"
                    class="text-lg font-bold text-slate-900">
                    Konfirmasi
                </h3>

                <p
                    id="reimbursementConfirmMessage"
                    class="mt-2 text-sm leading-relaxed text-slate-500">
                    Apakah Anda yakin ingin melanjutkan?
                </p>
            </div>


            {{-- Actions --}}
            <div
                class="mt-6 flex flex-col-reverse gap-3
                       sm:flex-row sm:justify-end">

                <button
                    type="button"
                    data-confirm-cancel
                    class="rounded-lg border border-slate-300
                           bg-white px-4 py-2.5 text-sm font-medium
                           text-slate-700 transition
                           hover:bg-slate-50">
                    Batal
                </button>

                <button
                    type="button"
                    data-confirm-submit
                    class="rounded-lg bg-blue-600
                           px-4 py-2.5 text-sm font-semibold text-white
                           transition hover:bg-blue-700">
                    Ya, Lanjutkan
                </button>
            </div>
        </div>
    </div>
</div>


@once
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal =
            document.getElementById('reimbursementConfirmModal');

        if (!modal) {
            return;
        }

        const panel =
            modal.querySelector('[data-confirm-panel]');

        const backdrop =
            modal.querySelector('[data-confirm-backdrop]');

        const cancelButton =
            modal.querySelector('[data-confirm-cancel]');

        const submitButton =
            modal.querySelector('[data-confirm-submit]');

        const titleElement =
            document.getElementById('reimbursementConfirmTitle');

        const messageElement =
            document.getElementById('reimbursementConfirmMessage');

        const iconWrapper =
            document.getElementById('reimbursementConfirmIcon');

        const icons =
            modal.querySelectorAll('[data-icon]');

        let pendingForm = null;


        const tones = {
            info: {
                icon: 'info',
                wrapper: 'bg-blue-50 text-blue-600',
                button: 'bg-blue-600 hover:bg-blue-700'
            },

            success: {
                icon: 'success',
                wrapper: 'bg-emerald-50 text-emerald-600',
                button: 'bg-emerald-600 hover:bg-emerald-700'
            },

            warning: {
                icon: 'warning',
                wrapper: 'bg-amber-50 text-amber-600',
                button: 'bg-amber-600 hover:bg-amber-700'
            },

            danger: {
                icon: 'danger',
                wrapper: 'bg-red-50 text-red-600',
                button: 'bg-red-600 hover:bg-red-700'
            }
        };


        function applyTone(toneName) {
            const tone = tones[toneName] || tones.info;

            iconWrapper.className =
                'flex h-12 w-12 items-center justify-center ' +
                'rounded-full ' +
                tone.wrapper;

            icons.forEach(function(icon) {
                icon.classList.toggle(
                    'hidden',
                    icon.dataset.icon !== tone.icon
                );
            });

            submitButton.className =
                'rounded-lg px-4 py-2.5 text-sm ' +
                'font-semibold text-white transition ' +
                tone.button;
        }


        function openModal(form) {
            pendingForm = form;

            titleElement.textContent =
                form.dataset.confirmTitle || 'Konfirmasi';

            messageElement.textContent =
                form.dataset.confirmMessage ||
                'Apakah Anda yakin ingin melanjutkan?';

            submitButton.textContent =
                form.dataset.confirmButton || 'Ya, Lanjutkan';

            applyTone(
                form.dataset.confirmTone || 'info'
            );

            modal.classList.remove('hidden');
            modal.setAttribute('aria-hidden', 'false');

            requestAnimationFrame(function() {
                panel.classList.remove(
                    'translate-y-3',
                    'scale-[0.98]',
                    'opacity-0'
                );

                panel.classList.add(
                    'translate-y-0',
                    'scale-100',
                    'opacity-100'
                );
            });

            document.body.classList.add('overflow-hidden');

            setTimeout(function() {
                submitButton.focus();
            }, 150);
        }


        function closeModal() {
            panel.classList.add(
                'translate-y-3',
                'scale-[0.98]',
                'opacity-0'
            );

            panel.classList.remove(
                'translate-y-0',
                'scale-100',
                'opacity-100'
            );

            modal.setAttribute('aria-hidden', 'true');

            setTimeout(function() {
                modal.classList.add('hidden');
                pendingForm = null;
                document.body.classList.remove('overflow-hidden');
            }, 180);
        }


        document
            .querySelectorAll('form[data-confirm]')
            .forEach(function(form) {
                form.addEventListener(
                    'submit',
                    function(event) {
                        if (
                            form.dataset.confirmed === 'true'
                        ) {
                            return;
                        }

                        event.preventDefault();
                        openModal(form);
                    }
                );
            });


        submitButton.addEventListener('click', function() {
            if (!pendingForm) {
                return;
            }

            pendingForm.dataset.confirmed = 'true';

            submitButton.disabled = true;
            submitButton.classList.add(
                'cursor-not-allowed',
                'opacity-70'
            );

            const originalText =
                submitButton.textContent;

            submitButton.textContent =
                'Memproses...';

            pendingForm.submit();

            setTimeout(function() {
                submitButton.disabled = false;
                submitButton.classList.remove(
                    'cursor-not-allowed',
                    'opacity-70'
                );

                submitButton.textContent =
                    originalText;
            }, 3000);
        });


        cancelButton.addEventListener(
            'click',
            closeModal
        );

        backdrop.addEventListener(
            'click',
            closeModal
        );

        document.addEventListener(
            'keydown',
            function(event) {
                if (
                    event.key === 'Escape' &&
                    !modal.classList.contains('hidden')
                ) {
                    closeModal();
                }
            }
        );
    });
</script>
@endpush
@endonce