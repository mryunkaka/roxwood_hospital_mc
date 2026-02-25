{{-- File Input Component with Preview --}}
@props([
    'name' => '',
    'id' => null,
    'label' => null,
    'dataTranslateLabel' => null,
    'accept' => 'image/png,image/jpeg',
    'required' => false,
    'error' => null,
    'hint' => null,
    'class' => ''
])

@php
    $inputId = $id ?? $name ?: 'file-' . uniqid();
    $hasError = $error !== null;
    $dataTranslateHint = $attributes->get('data-translate-hint') ?? $attributes->get('dataTranslateHint');
@endphp

<div class="w-full {{ $class }}">
    @if($label)
        <label for="{{ $inputId }}" class="block text-sm font-medium text-text-primary mb-1.5" @if($dataTranslateLabel) data-translate="{{ $dataTranslateLabel }}" @endif>
            @if($dataTranslateLabel)
                <span class="label-text">{{ $label }}</span>
            @else
                {{ $label }}
            @endif
            @if($required)
                <span class="text-danger-500">*</span>
            @endif
        </label>
    @endif

    {{-- File Input Area --}}
    <div
        x-data="filePreview('{{ $inputId }}')"
        class="relative"
    >
        {{-- Hidden File Input --}}
        <input
            type="file"
            name="{{ $name }}"
            id="{{ $inputId }}"
            {{ $attributes->merge([
                'accept' => $accept,
                'required' => $required,
                'class' => 'hidden',
                '@change' => 'handleFileSelect($event)'
            ]) }}
        >

        {{-- Upload Area --}}
        <div
            @click="$el.previousElementSibling.click()"
            class="relative border-2 border-dashed rounded-xl p-6 text-center cursor-pointer transition-all duration-200
                   bg-surface border-border text-text-secondary
                   hover:border-primary-400 hover:bg-primary-50/30
                   {{ $hasError ? 'border-danger-500 bg-danger-50/30' : '' }}"
        >
            {{-- Default State --}}
            <div x-show="!preview" class="space-y-3">
                <div class="flex justify-center">
                    <div class="w-16 h-16 rounded-full bg-primary-100 flex items-center justify-center">
                        <svg class="w-8 h-8 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </div>
                <p class="text-sm font-medium text-text-primary">
                    {{ __('messages.click_to_upload') }}
                </p>
            </div>

            {{-- Preview State --}}
            <div x-show="preview" x-transition class="space-y-3" style="display: none;">
                <div class="relative inline-block">
                    <img :src="preview" :alt="fileName" class="max-w-full max-h-48 rounded-lg object-contain mx-auto shadow-lg">
                    {{-- Remove Button --}}
                    <button
                        @click.stop="removeFile()"
                        type="button"
                        class="absolute -top-2 -right-2 w-8 h-8 rounded-full bg-danger text-white
                               flex items-center justify-center hover:bg-danger-600 shadow-lg
                               transition-colors"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                    {{-- Expand Button --}}
                    <button
                        @click.stop="showModal = true"
                        type="button"
                        class="absolute -bottom-2 -right-2 w-8 h-8 rounded-full bg-primary text-white
                               flex items-center justify-center hover:bg-primary-600 shadow-lg
                               transition-colors"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/>
                        </svg>
                    </button>
                </div>
                <p class="text-sm font-medium text-text-primary" x-text="fileName"></p>
                <p class="text-xs text-text-secondary" x-text="fileSize"></p>
            </div>
        </div>

        {{-- Fullscreen Modal for Preview --}}
        <div
            x-show="showModal"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm p-4"
            @click.self="showModal = false"
            @keydown.escape.window="showModal = false"
            style="display: none;"
        >
            <div class="relative max-w-5xl max-h-full">
                <button
                    @click="showModal = false"
                    type="button"
                    class="absolute -top-12 right-0 w-10 h-10 rounded-full bg-white/20 hover:bg-white/30
                           text-white flex items-center justify-center backdrop-blur-sm
                           transition-colors"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                <img :src="preview" :alt="fileName" class="max-w-full max-h-[80vh] rounded-lg shadow-2xl object-contain">
            </div>
        </div>
    </div>

    @if($hint && !$hasError)
        <p class="mt-1.5 text-xs text-text-secondary" @if($dataTranslateHint) data-translate="{{ $dataTranslateHint }}" @endif>{{ $hint }}</p>
    @endif

    @if($hasError)
        <p class="mt-1.5 text-xs text-danger-500 flex items-center gap-1">
            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            {{ $error }}
        </p>
    @endif
</div>

<script>
function filePreview(inputId) {
    return {
        preview: null,
        fileName: '',
        fileSize: '',
        showModal: false,
        labelText: '{{ __('messages.click_to_upload') }}',

        handleFileSelect(event) {
            const file = event.target.files[0];
            if (file) {
                // Check if file is an image
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.preview = e.target.result;
                        this.fileName = file.name;
                        this.fileSize = this.formatFileSize(file.size);
                    };
                    reader.readAsDataURL(file);
                } else {
                    // For non-image files, just show the name
                    this.preview = null;
                    this.fileName = file.name;
                    this.fileSize = this.formatFileSize(file.size);
                }
            }
        },

        removeFile() {
            const input = document.getElementById(inputId);
            input.value = '';
            this.preview = null;
            this.fileName = '';
            this.fileSize = '';
        },

        formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
        }
    };
}
</script>
