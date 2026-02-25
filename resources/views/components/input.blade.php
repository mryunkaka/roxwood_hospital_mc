{{-- Input Component --}}
@props([
    'name' => '',
    'id' => null,
    'type' => 'text',
    'label' => null,
    'placeholder' => '',
    'value' => null,
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'error' => null,
    'hint' => null,
    'icon' => null,
    'class' => ''
])

@php
    $inputId = $id ?? $name ?: 'input-' . uniqid();
    $hasError = $error !== null;

    // Border color berdasarkan state
    $borderColor = $hasError ? 'border-danger-500 focus:border-danger-500 focus:ring-danger-500/20' : 'border-border focus:border-primary-500 focus:ring-primary-500/20';

    // Background dan text color
    $bgClass = 'bg-surface';
    $textClass = 'text-text-primary placeholder:text-text-hint';

    // Dynamic translation support - support both camelCase and kebab-case
    $dataTranslateLabel = $attributes->get('data-translate-label') ?? $attributes->get('dataTranslateLabel');
    $dataTranslatePlaceholder = $attributes->get('data-translate-placeholder') ?? $attributes->get('dataTranslatePlaceholder');
    $dataTranslateHint = $attributes->get('data-translate-hint') ?? $attributes->get('dataTranslateHint');

    // Gunakan label langsung (sudah diterjemahkan di server-side)
    $finalLabel = $label;

    // Untuk placeholder, gunakan yang diberikan (sudah diterjemahkan di server-side)
    $finalPlaceholder = $placeholder;

    // Untuk hint, gunakan yang diberikan
    $finalHint = $hint;
@endphp

<div class="w-full {{ $class }}">
    @if($label)
        <label for="{{ $inputId }}" class="block text-sm font-medium text-text-primary mb-2" @if($dataTranslateLabel) data-translate="{{ $dataTranslateLabel }}" @endif>
            @if($dataTranslateLabel)
                <span class="label-text">{{ $finalLabel }}</span>
            @else
                {{ $finalLabel }}
            @endif
            @if($required)
                <span class="text-danger-500 ml-1">*</span>
            @endif
        </label>
    @endif

    <div class="relative">
        @if($icon)
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                <span class="w-5 h-5 text-text-muted">{!! $icon !!}</span>
            </div>
        @endif

        <input
            {{ $attributes->except('dataTranslateLabel', 'dataTranslatePlaceholder', 'dataTranslateHint', 'data-translate-label', 'data-translate-placeholder', 'data-translate-hint')->merge([
                'type' => $type,
                'name' => $name,
                'id' => $inputId,
                'placeholder' => $finalPlaceholder,
                'value' => old($name, $value),
                'required' => $required,
                'disabled' => $disabled,
                'readonly' => $readonly,
                'class' => 'w-full rounded-xl border px-4 py-3 text-sm outline-none transition-all duration-200 ' .
                         $bgClass . ' ' .
                         $borderColor . ' ' .
                         $textClass . ' ' .
                         'focus:ring-2 ' .
                         'disabled:bg-surface-alt disabled:cursor-not-allowed ' .
                         ($icon ? 'pl-11' : '')
            ]) }}@if($dataTranslatePlaceholder) data-translate-placeholder="{{ $dataTranslatePlaceholder }}"@endif
        >

        @if($type === 'password')
            <button type="button" x-data="{ show: false }" @click="show = !show"
                    class="absolute inset-y-0 right-0 pr-3.5 flex items-center group">
                <svg x-show="!show" class="w-5 h-5 text-text-muted group-hover:text-text-tertiary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                <svg x-show="show" class="w-5 h-5 text-text-muted group-hover:text-text-tertiary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                </svg>
            </button>
        @endif
    </div>

    @if($finalHint && !$hasError)
        <p class="mt-1.5 text-xs text-text-secondary" @if($dataTranslateHint) data-translate="{{ $dataTranslateHint }}" @endif>{{ $finalHint }}</p>
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
