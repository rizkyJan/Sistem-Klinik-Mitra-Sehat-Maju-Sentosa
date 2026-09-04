@props([
'inputId' => 'formal_photo',
'inputName' => 'formal_photo',
'required' => false,
'label' => 'Pas Foto / Foto Profil',
'help' => 'Pilih JPG, JPEG, PNG, atau WEBP. Setelah memilih foto, geser dan zoom sampai bagian yang diinginkan pas di kotak 1:1.',
])

@php
$cropInputId = $inputId;
$cropInputName = $inputName;
$cropRequired = (bool) $required;
$cropKey = preg_replace('/[^A-Za-z0-9_-]/', '-', $cropInputId . '-' . uniqid());
@endphp

<div
    data-profile-photo-cropper
    data-cropper-key="{{ $cropKey }}"
    class="space-y-3">

    <label
        for="{{ $cropInputId }}"
        class="block text-sm font-medium text-slate-700">
        {{ $label }}
        @if($cropRequired)
        <span class="text-red-500">*</span>
        @endif
    </label>

    <input
        id="{{ $cropInputId }}"
        type="file"
        name="{{ $cropInputName }}"
        accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
        @required($cropRequired)
        data-crop-input
        class="block w-full rounded-lg border border-slate-300 bg-white text-sm text-slate-600 file:mr-4 file:border-0 file:bg-slate-100 file:px-4 file:py-2.5 file:text-sm file:font-medium file:text-slate-700 hover:file:bg-slate-200">

    <p class="text-xs leading-5 text-slate-500">
        {{ $help }}
    </p>

    @error($cropInputName)
    <p class="text-sm text-red-600">{{ $message }}</p>
    @enderror

    {{-- PREVIEW HASIL AKHIR --}}
    <div
        data-crop-preview-wrap
        class="hidden rounded-xl border border-blue-100 bg-blue-50 p-4">

        <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
            <img
                data-crop-preview
                alt="Preview hasil crop foto profil"
                class="h-32 w-32 shrink-0 rounded-xl border border-blue-200 bg-white object-cover shadow-sm">

            <div class="min-w-0 text-xs leading-5 text-slate-600">
                <p class="font-semibold text-slate-800">
                    Foto sudah siap 1:1
                </p>
                <p class="mt-1">
                    Ini foto yang akan dikirim saat form disimpan.
                </p>

                <button
                    type="button"
                    data-crop-edit
                    class="mt-3 inline-flex items-center rounded-lg border border-blue-200 bg-white px-3 py-2 font-semibold text-blue-700 hover:bg-blue-50">
                    Atur Ulang Crop
                </button>
            </div>
        </div>
    </div>

    {{-- MODAL CROPPER --}}
    <div
        data-crop-modal
        class="bg-slate-950/80 p-3 backdrop-blur-sm sm:p-6"
        style="display:none;"
        aria-hidden="true">

        <div data-crop-dialog class="rounded-2xl bg-white shadow-2xl">

            <div class="flex items-start justify-between gap-4 border-b border-slate-200 px-4 py-4 sm:px-5">
                <div>
                    <h3 class="font-semibold text-slate-900">
                        Atur Foto Profil
                    </h3>
                    <p class="mt-1 text-xs leading-5 text-slate-500">
                        Geser foto sampai wajah atau bagian yang diinginkan pas di dalam kotak putih 1:1.
                    </p>
                </div>

                <button
                    type="button"
                    data-crop-cancel
                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100"
                    aria-label="Tutup crop foto">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div data-crop-scroll class="p-4 sm:p-5">

                {{--
                    Viewport dibuat lebih lebar daripada kotak crop.
                    Area di luar kotak digelapkan sehingga pengguna tetap bisa
                    melihat bagian foto yang sedang berada di luar hasil akhir.
                --}}
                <div data-crop-stage class="overflow-hidden rounded-xl bg-slate-950 shadow-inner">
                    <canvas
                        data-crop-canvas
                        width="840"
                        height="630"
                        class="block aspect-[4/3] w-full cursor-grab touch-none select-none active:cursor-grabbing">
                    </canvas>
                </div>

                <div class="mx-auto mt-5 max-w-[560px] space-y-4">
                    <div>
                        <div class="mb-2 flex items-center justify-between gap-3 text-xs font-medium text-slate-600">
                            <label>Zoom Foto</label>
                            <span data-crop-zoom-value>100%</span>
                        </div>

                        <input
                            data-crop-zoom
                            type="range"
                            min="1"
                            max="3"
                            step="0.01"
                            value="1"
                            class="w-full accent-blue-600">
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <button
                            type="button"
                            data-crop-reset
                            class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                            Reset Posisi
                        </button>
                    </div>

                    <div class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-2.5 text-xs leading-5 text-amber-800">
                        Kotak putih adalah hasil akhir. Bagian yang gelap di luar kotak tidak ikut disimpan. Foto portrait 4:6, 3:4, landscape, dan ukuran lain tetap bisa dipaskan sendiri.
                    </div>
                </div>
            </div>

            <div class="flex flex-col-reverse gap-2 border-t border-slate-200 bg-slate-50 px-4 py-4 sm:flex-row sm:justify-end sm:px-5">
                <button
                    type="button"
                    data-crop-cancel
                    class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    Batal
                </button>

                <button
                    type="button"
                    data-crop-apply
                    class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">
                    Gunakan Foto
                </button>
            </div>
        </div>
    </div>
</div>

@once
<style>
    /*
     * Layout inti cropper dibuat mandiri dari utility Tailwind agar modal tetap
     * responsif meskipun CSS production belum di-build ulang setelah komponen
     * Blade ini ditambahkan.
     */
    [data-crop-modal] {
        position: fixed !important;
        inset: 0 !important;
        z-index: 9999 !important;
        align-items: center !important;
        justify-content: center !important;
        padding: 12px;
        background: rgba(2, 6, 23, .80);
        box-sizing: border-box;
    }

    [data-crop-dialog] {
        display: flex;
        flex-direction: column;
        width: min(680px, 100%);
        max-height: calc(100vh - 24px);
        max-height: calc(100dvh - 24px);
        overflow: hidden;
        background: #fff;
    }

    [data-crop-scroll] {
        flex: 1 1 auto;
        min-height: 0;
        overflow-y: auto;
        overscroll-behavior: contain;
    }

    [data-crop-stage] {
        width: min(560px, 100%);
        margin-inline: auto;
    }

    [data-crop-canvas] {
        display: block;
        width: 100% !important;
        height: auto !important;
        max-width: 100%;
        aspect-ratio: 4 / 3;
        touch-action: none;
    }

    @media (max-width: 640px) {
        [data-crop-modal] {
            padding: 8px;
            align-items: center !important;
        }

        [data-crop-dialog] {
            width: 100%;
            max-height: calc(100vh - 16px);
            max-height: calc(100dvh - 16px);
            border-radius: 14px;
        }
    }

    @media (max-height: 680px) and (min-width: 641px) {
        [data-crop-stage] {
            width: min(460px, 100%);
        }
    }
</style>
<script>
    (function() {
        'use strict';

        const OUTPUT_SIZE = 720;
        const OUTPUT_QUALITY = 0.90;

        function initProfilePhotoCroppers() {
            document.querySelectorAll('[data-profile-photo-cropper]').forEach(function(root) {
                if (root.dataset.cropperReady === '1') {
                    return;
                }

                root.dataset.cropperReady = '1';

                const input = root.querySelector('[data-crop-input]');
                const modal = root.querySelector('[data-crop-modal]');
                const canvas = root.querySelector('[data-crop-canvas]');
                const previewWrap = root.querySelector('[data-crop-preview-wrap]');
                const preview = root.querySelector('[data-crop-preview]');
                const editButton = root.querySelector('[data-crop-edit]');
                const zoomInput = root.querySelector('[data-crop-zoom]');
                const zoomValue = root.querySelector('[data-crop-zoom-value]');
                const resetButton = root.querySelector('[data-crop-reset]');
                const applyButton = root.querySelector('[data-crop-apply]');
                const cancelButtons = root.querySelectorAll('[data-crop-cancel]');

                if (!input || !modal || !canvas || !previewWrap || !preview) {
                    return;
                }

                const ctx = canvas.getContext('2d');

                // Kotak crop berada di tengah viewport dan selalu berbentuk 1:1.
                const CROP_SIZE = 420;
                const CROP_X = (canvas.width - CROP_SIZE) / 2;
                const CROP_Y = (canvas.height - CROP_SIZE) / 2;

                const state = {
                    image: null,
                    originalFile: null,
                    appliedFile: null,
                    zoom: 1,
                    offsetX: 0,
                    offsetY: 0,
                    dragging: false,
                    pointerId: null,
                    lastX: 0,
                    lastY: 0,
                    previewUrl: null,
                    hasAppliedCrop: false,
                };

                function revokePreviewUrl() {
                    if (state.previewUrl) {
                        URL.revokeObjectURL(state.previewUrl);
                        state.previewUrl = null;
                    }
                }

                function baseScale() {
                    if (!state.image) {
                        return 1;
                    }

                    // Gambar minimal harus menutup seluruh kotak crop.
                    return Math.max(
                        CROP_SIZE / state.image.naturalWidth,
                        CROP_SIZE / state.image.naturalHeight
                    );
                }

                function renderedSize() {
                    const scale = baseScale() * state.zoom;

                    return {
                        width: state.image.naturalWidth * scale,
                        height: state.image.naturalHeight * scale,
                        scale: scale,
                    };
                }

                function imageRect() {
                    const size = renderedSize();

                    return {
                        x: ((canvas.width - size.width) / 2) + state.offsetX,
                        y: ((canvas.height - size.height) / 2) + state.offsetY,
                        width: size.width,
                        height: size.height,
                    };
                }

                function clampOffsets() {
                    if (!state.image) {
                        return;
                    }

                    const size = renderedSize();

                    // Pengguna boleh menggeser foto, tetapi crop square tidak boleh
                    // sampai menampilkan area kosong.
                    const maxX = Math.max(0, (size.width - CROP_SIZE) / 2);
                    const maxY = Math.max(0, (size.height - CROP_SIZE) / 2);

                    state.offsetX = Math.min(maxX, Math.max(-maxX, state.offsetX));
                    state.offsetY = Math.min(maxY, Math.max(-maxY, state.offsetY));
                }

                function draw() {
                    ctx.clearRect(0, 0, canvas.width, canvas.height);
                    ctx.fillStyle = '#020617';
                    ctx.fillRect(0, 0, canvas.width, canvas.height);

                    if (!state.image) {
                        return;
                    }

                    clampOffsets();

                    const rect = imageRect();

                    ctx.drawImage(
                        state.image,
                        rect.x,
                        rect.y,
                        rect.width,
                        rect.height
                    );

                    // Gelapkan bagian di luar crop square.
                    ctx.save();
                    ctx.fillStyle = 'rgba(2, 6, 23, 0.70)';

                    ctx.fillRect(0, 0, canvas.width, CROP_Y);
                    ctx.fillRect(0, CROP_Y + CROP_SIZE, canvas.width, canvas.height - (CROP_Y + CROP_SIZE));
                    ctx.fillRect(0, CROP_Y, CROP_X, CROP_SIZE);
                    ctx.fillRect(CROP_X + CROP_SIZE, CROP_Y, canvas.width - (CROP_X + CROP_SIZE), CROP_SIZE);
                    ctx.restore();

                    // Garis rule-of-thirds di dalam kotak crop.
                    ctx.save();
                    ctx.beginPath();
                    ctx.rect(CROP_X, CROP_Y, CROP_SIZE, CROP_SIZE);
                    ctx.clip();

                    ctx.strokeStyle = 'rgba(255,255,255,.50)';
                    ctx.lineWidth = 1.5;

                    const third = CROP_SIZE / 3;
                    ctx.beginPath();
                    ctx.moveTo(CROP_X + third, CROP_Y);
                    ctx.lineTo(CROP_X + third, CROP_Y + CROP_SIZE);
                    ctx.moveTo(CROP_X + (third * 2), CROP_Y);
                    ctx.lineTo(CROP_X + (third * 2), CROP_Y + CROP_SIZE);
                    ctx.moveTo(CROP_X, CROP_Y + third);
                    ctx.lineTo(CROP_X + CROP_SIZE, CROP_Y + third);
                    ctx.moveTo(CROP_X, CROP_Y + (third * 2));
                    ctx.lineTo(CROP_X + CROP_SIZE, CROP_Y + (third * 2));
                    ctx.stroke();
                    ctx.restore();

                    // Border putih crop square.
                    ctx.save();
                    ctx.strokeStyle = 'rgba(255,255,255,.98)';
                    ctx.lineWidth = 4;
                    ctx.strokeRect(
                        CROP_X + 2,
                        CROP_Y + 2,
                        CROP_SIZE - 4,
                        CROP_SIZE - 4
                    );
                    ctx.restore();
                }

                let previousBodyOverflow = '';

                function openModal() {
                    previousBodyOverflow = document.body.style.overflow;
                    modal.style.display = 'flex';
                    modal.setAttribute('aria-hidden', 'false');
                    document.body.style.overflow = 'hidden';
                    draw();
                }

                function closeModal() {
                    modal.style.display = 'none';
                    modal.setAttribute('aria-hidden', 'true');
                    document.body.style.overflow = previousBodyOverflow;
                }

                function resetCrop() {
                    state.zoom = 1;
                    state.offsetX = 0;
                    state.offsetY = 0;

                    if (zoomInput) {
                        zoomInput.value = '1';
                    }

                    if (zoomValue) {
                        zoomValue.textContent = '100%';
                    }

                    draw();
                }

                function restoreAppliedFileOrClear() {
                    if (state.appliedFile) {
                        const transfer = new DataTransfer();
                        transfer.items.add(state.appliedFile);
                        input.files = transfer.files;
                        return;
                    }

                    input.value = '';
                }

                function loadSelectedFile(file) {
                    if (!file || !file.type.startsWith('image/')) {
                        restoreAppliedFileOrClear();
                        return;
                    }

                    const objectUrl = URL.createObjectURL(file);
                    const image = new Image();

                    image.onload = function() {
                        URL.revokeObjectURL(objectUrl);
                        state.image = image;
                        state.originalFile = file;
                        resetCrop();
                        openModal();
                    };

                    image.onerror = function() {
                        URL.revokeObjectURL(objectUrl);
                        restoreAppliedFileOrClear();
                        window.alert('Foto tidak dapat dibaca. Silakan pilih file JPG, JPEG, PNG, atau WEBP lain.');
                    };

                    image.src = objectUrl;
                }

                function canvasPoint(event) {
                    const rect = canvas.getBoundingClientRect();

                    return {
                        x: (event.clientX - rect.left) * (canvas.width / rect.width),
                        y: (event.clientY - rect.top) * (canvas.height / rect.height),
                    };
                }

                function cancelCrop() {
                    restoreAppliedFileOrClear();
                    closeModal();
                }

                function outputBlob() {
                    return new Promise(function(resolve, reject) {
                        if (!state.image) {
                            reject(new Error('Foto belum dimuat.'));
                            return;
                        }

                        clampOffsets();

                        const rect = imageRect();
                        const output = document.createElement('canvas');
                        output.width = OUTPUT_SIZE;
                        output.height = OUTPUT_SIZE;

                        const outputCtx = output.getContext('2d');
                        const scaleToOutput = OUTPUT_SIZE / CROP_SIZE;

                        // JPG tidak mempunyai transparency. Pakai latar putih agar
                        // PNG transparan tidak berubah menjadi hitam.
                        outputCtx.fillStyle = '#ffffff';
                        outputCtx.fillRect(0, 0, OUTPUT_SIZE, OUTPUT_SIZE);

                        outputCtx.drawImage(
                            state.image,
                            (rect.x - CROP_X) * scaleToOutput,
                            (rect.y - CROP_Y) * scaleToOutput,
                            rect.width * scaleToOutput,
                            rect.height * scaleToOutput
                        );

                        output.toBlob(
                            function(blob) {
                                if (blob) {
                                    resolve(blob);
                                } else {
                                    reject(new Error('Hasil crop gagal dibuat.'));
                                }
                            },
                            'image/jpeg',
                            OUTPUT_QUALITY
                        );
                    });
                }

                function safeBaseName(filename) {
                    const base = String(filename || 'foto-profil')
                        .replace(/\.[^.]+$/, '')
                        .replace(/[^A-Za-z0-9_-]+/g, '-')
                        .replace(/^-+|-+$/g, '');

                    return base || 'foto-profil';
                }

                input.addEventListener('change', function() {
                    const file = input.files && input.files[0];

                    if (!file) {
                        return;
                    }

                    loadSelectedFile(file);
                });

                canvas.addEventListener('pointerdown', function(event) {
                    if (!state.image) {
                        return;
                    }

                    event.preventDefault();
                    const point = canvasPoint(event);

                    state.dragging = true;
                    state.pointerId = event.pointerId;
                    state.lastX = point.x;
                    state.lastY = point.y;

                    canvas.setPointerCapture(event.pointerId);
                });

                canvas.addEventListener('pointermove', function(event) {
                    if (!state.dragging || state.pointerId !== event.pointerId) {
                        return;
                    }

                    event.preventDefault();
                    const point = canvasPoint(event);

                    state.offsetX += point.x - state.lastX;
                    state.offsetY += point.y - state.lastY;
                    state.lastX = point.x;
                    state.lastY = point.y;

                    draw();
                });

                function endDrag(event) {
                    if (state.pointerId !== event.pointerId) {
                        return;
                    }

                    state.dragging = false;
                    state.pointerId = null;
                }

                canvas.addEventListener('pointerup', endDrag);
                canvas.addEventListener('pointercancel', endDrag);

                if (zoomInput) {
                    zoomInput.addEventListener('input', function() {
                        state.zoom = Number(zoomInput.value) || 1;

                        if (zoomValue) {
                            zoomValue.textContent = Math.round(state.zoom * 100) + '%';
                        }

                        draw();
                    });
                }

                if (resetButton) {
                    resetButton.addEventListener('click', resetCrop);
                }

                if (editButton) {
                    editButton.addEventListener('click', function() {
                        if (!state.image) {
                            return;
                        }

                        openModal();
                    });
                }

                cancelButtons.forEach(function(button) {
                    button.addEventListener('click', cancelCrop);
                });

                modal.addEventListener('click', function(event) {
                    if (event.target === modal) {
                        cancelCrop();
                    }
                });

                document.addEventListener('keydown', function(event) {
                    if (
                        event.key === 'Escape' &&
                        modal.getAttribute('aria-hidden') === 'false'
                    ) {
                        cancelCrop();
                    }
                });

                if (applyButton) {
                    applyButton.addEventListener('click', async function() {
                        if (!state.image) {
                            return;
                        }

                        applyButton.disabled = true;
                        const oldText = applyButton.textContent;
                        applyButton.textContent = 'Memproses...';

                        try {
                            const blob = await outputBlob();
                            const fileName = safeBaseName(
                                state.originalFile ? state.originalFile.name : 'foto-profil'
                            ) + '-1x1.jpg';

                            const croppedFile = new File(
                                [blob],
                                fileName, {
                                    type: 'image/jpeg',
                                    lastModified: Date.now(),
                                }
                            );

                            const transfer = new DataTransfer();
                            transfer.items.add(croppedFile);
                            input.files = transfer.files;

                            state.appliedFile = croppedFile;
                            state.hasAppliedCrop = true;

                            revokePreviewUrl();
                            state.previewUrl = URL.createObjectURL(croppedFile);
                            preview.src = state.previewUrl;
                            previewWrap.classList.remove('hidden');

                            closeModal();
                        } catch (error) {
                            console.error(error);
                            window.alert('Hasil crop gagal dibuat. Silakan coba lagi.');
                        } finally {
                            applyButton.disabled = false;
                            applyButton.textContent = oldText;
                        }
                    });
                }
            });
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initProfilePhotoCroppers);
        } else {
            initProfilePhotoCroppers();
        }
    })();
</script>
@endonce