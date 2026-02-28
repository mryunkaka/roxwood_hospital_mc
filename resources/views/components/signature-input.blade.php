{{-- Signature Input Component with Digital Signature and Upload Options --}}

@props([
    'name' => 'signature',
    'label' => null,
    'dataTranslateLabel' => null,
    'required' => false,
    'dataTranslateHint' => null,
])

@php
    $isRequired = $required ? 'required' : '';
    $requiredClass = $required ? 'text-red-500' : '';
    $requiredStar = $required ? '<span class="text-red-500 ml-1">*</span>' : '';
@endphp

<div class="space-y-4">
    {{-- Label --}}
    <div id="{{ $name }}_label" class="block text-sm font-medium text-text-primary" data-translate="{{ $dataTranslateLabel }}">
        {{ $label }}
        {!! $requiredStar !!}
    </div>

    {{-- Hint --}}
    @if($dataTranslateHint)
        <p class="text-xs text-text-secondary" data-translate="{{ $dataTranslateHint }}">
            {{ __("messages.{$dataTranslateHint}") }}
        </p>
    @endif

    {{-- Signature Type Toggle --}}
    <div class="flex border border-border rounded-lg overflow-hidden">
        <button
            type="button"
            x-data="{ signatureType: 'digital' }"
            @click="signatureType = 'digital'; $dispatch('signature-type-changed', { type: 'digital' })"
            class="signature-type-btn flex-1 px-4 py-2 text-sm font-medium transition-colors border-b-2"
            :class="signatureType === 'digital' ? 'border-primary-500 text-primary-500 bg-primary-50' : 'border-transparent text-text-secondary hover:text-text-primary'"
            data-type="digital"
        >
            <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732a2.5 2.5 0 013.536 3.536z"/>
            </svg>
            <span data-translate="signature_digital">{{ __('messages.signature_digital') }}</span>
        </button>
        <button
            type="button"
            x-data="{ signatureType: 'upload' }"
            @click="signatureType = 'upload'; $dispatch('signature-type-changed', { type: 'upload' })"
            class="signature-type-btn flex-1 px-4 py-2 text-sm font-medium transition-colors border-b-2"
            :class="signatureType === 'upload' ? 'border-primary-500 text-primary-500 bg-primary-50' : 'border-transparent text-text-secondary hover:text-text-primary'"
            data-type="upload"
        >
            <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <span data-translate="signature_upload">{{ __('messages.signature_upload') }}</span>
        </button>
    </div>

    {{-- Digital Signature Canvas --}}
    <div x-show="document.querySelector('.signature-type-btn[data-type=digital]')?.classList.contains('border-primary-500') ?? true" class="signature-container">
        <canvas
            id="{{ $name }}_canvas"
            aria-labelledby="{{ $name }}_label"
            tabindex="0"
            class="w-full border-2 border-dashed border-border rounded-lg bg-white cursor-crosshair"
            style="touch-action: none;"
            height="200"
        ></canvas>
        <input type="hidden" name="{{ $name }}_data" id="{{ $name }}_data" {{ $isRequired }}>
        <button
            type="button"
            id="{{ $name }}_clear"
            class="mt-2 text-sm text-text-secondary hover:text-text-primary transition-colors"
        >
            <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
            <span data-translate="signature_clear">{{ __('messages.signature_clear') }}</span>
        </button>
    </div>

    {{-- Upload File Option --}}
    <div x-show="document.querySelector('.signature-type-btn[data-type=upload]')?.classList.contains('border-primary-500') ?? false" class="signature-upload hidden">
        <x-file-input
            name="{{ $name }}_file"
            :label="__('messages.signature_upload_desc')"
            :dataTranslateLabel="'signature_upload_desc'"
            :dataTranslateUpload="'click_to_upload'"
            accept="image/png,image/jpeg,image/jpg"
            :required="false"
            :hint="__('messages.signature_upload_hint')"
        />
    </div>
</div>

@once
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
@endpush
@endonce

@push('scripts')
<script>
(function() {
    // Initialize signature pad for each signature input
    document.addEventListener('DOMContentLoaded', function() {
        const canvas = document.getElementById('{{ $name }}_canvas');
        if (!canvas) return;

        // Get 2D context and set default styles
        const ctx = canvas.getContext('2d');

        const signaturePad = new SignaturePad(canvas, {
            backgroundColor: 'rgba(255, 255, 255, 0)',
            penColor: 'black',
            velocityFilterWeight: 0.7,
            minWidth: 0.5,
            maxWidth: 2.5,
        });

        // Resize canvas to match display size
        function resizeCanvas() {
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            const ctx = canvas.getContext("2d");
            ctx.scale(ratio, ratio);
            // Ensure pen color stays black after resize
            ctx.strokeStyle = 'black';
            ctx.fillStyle = 'black';
            signaturePad.clear();
        }

        window.addEventListener("resize", resizeCanvas);
        resizeCanvas();

        // Clear button
        document.getElementById('{{ $name }}_clear')?.addEventListener('click', function(e) {
            e.preventDefault();
            signaturePad.clear();
            document.getElementById('{{ $name }}_data').value = '';
        });

        // Save signature data to hidden input
        function saveSignature() {
            if (signaturePad.isEmpty()) {
                document.getElementById('{{ $name }}_data').value = '';
            } else {
                // Force pen color to black before saving
                const dataURL = signaturePad.toDataURL('image/png');
                document.getElementById('{{ $name }}_data').value = dataURL;
            }
        }

        // Save on form submit
        canvas.closest('form')?.addEventListener('submit', saveSignature);

        // Handle signature type toggle
        const typeButtons = document.querySelectorAll('.signature-type-btn');
        const digitalContainer = document.querySelector('.signature-container');
        const uploadContainer = document.querySelector('.signature-upload');

        typeButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const type = this.dataset.type;
                if (type === 'digital') {
                    digitalContainer?.classList.remove('hidden');
                    uploadContainer?.classList.add('hidden');
                    // Resize canvas when showing digital signature
                    setTimeout(resizeCanvas, 100);
                } else {
                    digitalContainer?.classList.add('hidden');
                    uploadContainer?.classList.remove('hidden');
                }
            });
        });
    });
})();
</script>
@endpush
