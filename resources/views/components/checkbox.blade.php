{{-- Checkbox Component --}}
{{-- Checkbox dengan label, error, dan theme support --}}

@props([
    'name' => null,
    'label' => null,
    'dataTranslateLabel' => null,
    'value' => '1',
    'checked' => false,
    'required' => false,
    'disabled' => false,
    'description' => null,
    'error' => null,
    'class' => ''
])

@php
    $uniqueId = 'checkbox_' . uniqid();
    $errorClass = $error ? 'border-danger-500 focus:ring-danger-500/50' : 'border-gray-300 dark:border-gray-600 focus:ring-primary-500/50 dark:focus:ring-primary-400/50';
@endphp

<div class="checkbox-wrapper {{ $class }}">
    <div class="flex items-start gap-3">
        <div class="relative flex items-center">
            <input
                type="checkbox"
                id="{{ $uniqueId }}"
                name="{{ $name }}"
                value="{{ $value }}"
                @if($checked) checked @endif
                @if($required) required @endif
                @if($disabled) disabled @endif
                {{ $attributes->except(['type', 'id', 'name', 'value', 'checked', 'required', 'disabled', 'class']) }}
                class="custom-checkbox w-4 h-4 mt-1 rounded border
                       {{ $errorClass }}
                       text-primary-500 dark:text-primary-400
                       focus:ring-2 focus:ring-offset-0
                       @if($disabled) opacity-50 cursor-not-allowed @endif
                       theme-stylis:border-teal-300 theme-stylis:checked:bg-teal-500 theme-stylis:focus:ring-teal-500/50
                       cursor-{{ $disabled ? 'not-allowed' : 'pointer' }}
                       transition-all duration-200"
            />
        </div>

        <div class="flex-1">
            @if($label)
                <label for="{{ $uniqueId }}" class="text-sm text-text-primary theme-stylis:text-gray-800 cursor-{{ $disabled ? 'not-allowed' : 'pointer' }} select-none" @if($dataTranslateLabel) data-translate="{{ $dataTranslateLabel }}" @endif>
                    @if($dataTranslateLabel)
                        <span class="label-text">{!! $label !!}</span>
                    @else
                        {!! $label !!}
                    @endif
                    @if($required)
                        <span class="text-danger-500 ml-1">*</span>
                    @endif
                </label>
            @endif

            @if($description)
                <p class="mt-1 text-xs text-text-secondary theme-stylis:text-gray-600">
                    {!! $description !!}
                </p>
            @endif

            @if($error)
                <p class="mt-1 text-xs text-danger-500">{{ $error }}</p>
            @endif
        </div>
    </div>
</div>

@once
@push('styles')
<style>
/* Custom checkbox styling */
.custom-checkbox {
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    padding: 0;
    background-color: #ffffff;
    background-image: url("data:image/svg+xml,%3csvg viewBox='0 0 16 16' fill='white' xmlns='http://www.w3.org/2000/svg'%3e%3cpath d='M12.207 4.793a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0l-2-2a1 1 0 011.414-1.414L6.5 9.086l4.293-4.293a1 1 0 011.414 0z'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: center;
    background-size: 0% 0%;
    transition: all 0.2s ease-in-out;
}

.custom-checkbox:checked {
    background-color: #3b82f6;
    background-image: url("data:image/svg+xml,%3csvg viewBox='0 0 16 16' fill='white' xmlns='http://www.w3.org/2000/svg'%3e%3cpath d='M12.207 4.793a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0l-2-2a1 1 0 011.414-1.414L6.5 9.086l4.293-4.293a1 1 0 011.414 0z'/%3e%3c/svg%3e");
    background-size: 65% 65%;
    border-color: #3b82f6;
}

/* Dark theme checkbox */
.theme-dark .custom-checkbox {
    background-color: #1e293b;
}

.theme-dark .custom-checkbox:checked {
    background-color: #3b82f6;
    border-color: #3b82f6;
}

/* Stylis theme checkbox */
.theme-stylis .custom-checkbox {
    border-color: rgba(20, 184, 166, 0.3);
}

.theme-stylis .custom-checkbox:checked {
    background-color: #14b8a6;
    border-color: #14b8a6;
}

/* Focus states */
.custom-checkbox:focus-visible {
    outline: 2px solid #3b82f6;
    outline-offset: 2px;
}

.theme-stylis .custom-checkbox:focus-visible {
    outline-color: #14b8a6;
}

/* Disabled state */
.custom-checkbox:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

/* Hover state */
.custom-checkbox:not(:disabled):hover {
    border-color: #3b82f6;
}

.theme-stylis .custom-checkbox:not(:disabled):hover {
    border-color: #14b8a6;
}
</style>
@endpush
@endonce
