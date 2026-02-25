{{-- Checkbox Component --}}
@props([
    'name' => null,
    'label' => null,
    'checked' => false,
    'disabled' => false,
    'description' => null,
    'class' => ''
])

@php
    $checkboxId = $name ?? 'checkbox-' . uniqid();
    $baseClasses = 'flex-shrink-0 w-4 h-4 rounded border transition-all duration-200 cursor-pointer ' .
                   'focus:ring-2 focus:ring-offset-0 focus:ring-offset-transparent ' .
                   'checked:bg-primary checked:border-primary ' .
                   'theme-dark:checked:bg-primary-500 theme-dark:checked:border-primary-500 ' .
                   'theme-stylis:checked:bg-teal-500 theme-stylis:checked:border-teal-500';

    $uncheckedClasses = 'bg-surface border-border ' .
                         'hover:border-primary-300 ' .
                         'theme-dark:bg-slate-700 theme-dark:border-slate-600 theme-dark:hover:border-slate-500 ' .
                         'theme-stylis:bg-white/80 theme-stylis:border-teal-200 theme-stylis:hover:border-teal-400 ' .
                         'theme-stylis.theme-dark:bg-slate-700/50 theme-stylis.theme-dark:border-teal-600/50';

    $disabledClasses = $disabled ? 'opacity-50 cursor-not-allowed' : '';
    $ringClasses = 'focus:ring-primary/50 theme-dark:focus:ring-primary-400/50 theme-stylis:focus:ring-teal-500/50';
@endphp

<div class="flex items-start gap-3 {{ $disabled ? 'opacity-50 cursor-not-allowed' : '' }}">
    <div class="relative flex items-start pt-0.5">
        <input type="checkbox"
               id="{{ $checkboxId }}"
               @if($name) name="{{ $name }}" @endif
               value="1"
               {{ $checked ? 'checked' : '' }}
               {{ $disabled ? 'disabled' : '' }}
               class="peer appearance-none {{ $baseClasses }} {{ $uncheckedClasses }} {{ $ringClasses }} {{ $disabledClasses }}"/>

        {{-- Custom Checkmark Icon --}}
        <svg class="absolute left-0.5 top-1.5 w-3 h-3 text-white pointer-events-none peer-checked:block hidden"
             fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
        </svg>
    </div>

    @if($label || $description)
        <div class="flex-1">
            @if($label)
                <label for="{{ $checkboxId }}" class="text-sm font-medium text-text-primary cursor-pointer {{ $disabled ? 'cursor-not-allowed' : '' }}">
                    {{ $label }}
                </label>
            @endif
            @if($description)
                <p class="text-xs text-text-secondary mt-0.5">{{ $description }}</p>
            @endif
        </div>
    @endif
</div>
