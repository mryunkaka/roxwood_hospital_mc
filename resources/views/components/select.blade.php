{{-- Select Component --}}
@props([
    'name' => '',
    'id' => null,
    'label' => null,
    'dataTranslateLabel' => null,
    'options' => [],
    'dataTranslateOptions' => null, // JSON map of value -> translation key
    'placeholder' => null,
    'dataTranslatePlaceholder' => null,
    'value' => null,
    'required' => false,
    'disabled' => false,
    'error' => null,
    'hint' => null,
    'class' => ''
])

@php
    $inputId = $id ?? $name ?: 'select-' . uniqid();
    $hasError = $error !== null;
    $dataTranslateHint = $attributes->get('data-translate-hint') ?? $attributes->get('dataTranslateHint');

    // Build options map for dynamic translation
    $optionsMap = [];
    if ($dataTranslateOptions) {
        foreach ($options as $option) {
            if (is_array($option) && isset($option['value'])) {
                $val = $option['value'];
                $optionsMap[$val] = $option['label'] ?? $option;
            }
        }
    }
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

    <div class="relative">
        <select
            {{ $attributes->merge([
                'name' => $name,
                'id' => $inputId,
                'required' => $required,
                'disabled' => $disabled,
                'class' => 'w-full appearance-none rounded-xl border px-4 py-3 pr-10 text-sm outline-none transition-all duration-200 ' .
                         'bg-surface border-border text-text-primary ' .
                         'focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 ' .
                         'disabled:bg-surface-hover disabled:cursor-not-allowed ' .
                         ($hasError ? 'border-danger-500 focus:border-danger-500 focus:ring-danger-500/20' : '')
            ]) }}
            @if($dataTranslateOptions) data-translate-select="{{ json_encode($optionsMap) }}" @endif
        >
            @if($placeholder)
                <option value="" @if($dataTranslatePlaceholder) data-translate-placeholder="{{ $dataTranslatePlaceholder }}" @endif>{{ $placeholder }}</option>
            @endif

            @foreach($options as $key => $option)
                @if(is_array($option))
                    <option value="{{ $option['value'] ?? $key }}" {{ old($name, $value) == ($option['value'] ?? $key) ? 'selected' : '' }}>
                        {{ $option['label'] ?? $option }}
                    </option>
                @else
                    <option value="{{ $key }}" {{ old($name, $value) == $key ? 'selected' : '' }}>
                        {{ $option }}
                    </option>
                @endif
            @endforeach
        </select>

        {{-- Dropdown Icon --}}
        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
            <svg class="w-5 h-5 text-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
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
