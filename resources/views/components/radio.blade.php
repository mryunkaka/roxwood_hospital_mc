{{-- Radio Button Component --}}
@props([
    'name' => null,
    'value' => null,
    'label' => null,
    'checked' => false,
    'disabled' => false,
    'description' => null,
    'class' => '',
    'color' => 'primary'
])

@php
    $radioId = 'radio-' . ($name ?? 'radio') . '-' . ($value ?? uniqid());
@endphp

<div class="flex items-start gap-3 {{ $class }}">
    <label for="{{ $radioId }}" class="relative flex items-start gap-3 cursor-pointer {{ $disabled ? 'opacity-50 cursor-not-allowed' : '' }}">
        {{-- Radio Input --}}
        <input type="radio"
               id="{{ $radioId }}"
               @if($name) name="{{ $name }}" @endif
               @if($value) value="{{ $value }}" @endif
               {{ $checked ? 'checked' : '' }}
               {{ $disabled ? 'disabled' : '' }}
               class="custom-radio appearance-none w-5 h-5 mt-0.5 rounded-full border-2 transition-all duration-200 ease-out
                      border-gray-300 bg-white
                      hover:border-blue-400
                      focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:ring-offset-1 focus:border-blue-500
                      checked:border-blue-500
                      disabled:opacity-50 disabled:cursor-not-allowed
                      theme-dark:border-slate-600 theme-dark:bg-slate-800
                      theme-dark:hover:border-slate-500
                      theme-dark:focus:border-slate-400
                      theme-dark:checked:border-blue-400
                      theme-stylis:border-teal-200 theme-stylis:bg-white
                      theme-stylis:hover:border-teal-400
                      theme-stylis:checked:border-teal-500"/>

        {{-- Label Content --}}
        @if($label || $description)
            <div class="flex-1 -mt-0.5">
                @if($label)
                    <span class="text-sm font-medium text-text-primary {{ $disabled ? 'text-text-muted' : '' }}">
                        {{ $label }}
                    </span>
                @endif
                @if($description)
                    <p class="text-xs text-text-secondary mt-0.5">{{ $description }}</p>
                @endif
            </div>
        @endif
    </label>
</div>

<style>
    .custom-radio {
        background-image: radial-gradient(circle, white 40%, transparent 40%);
        background-size: 0 0;
        background-position: center;
        background-repeat: no-repeat;
    }
    .custom-radio:checked {
        background-size: 10px 10px;
        background-color: rgb(59 130 246);
        border-color: rgb(59 130 246);
    }
    .theme-dark .custom-radio:checked {
        background-image: radial-gradient(circle, #1e293b 40%, transparent 40%);
    }
</style>
