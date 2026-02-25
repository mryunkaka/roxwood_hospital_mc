{{-- Dropdown Component --}}
@props([
    'trigger' => null,
    'placement' => 'bottom-end', // 'bottom-start', 'bottom-end', 'top-start', 'top-end'
    'offset' => 8
])

<div x-data="{ open: false }" class="relative inline-block">
    {{-- Trigger --}}
    @if($trigger)
        <div @click="open = !open">{{ $trigger }}</div>
    @else
        <button @click="open = !open" {{ $attributes }}>
            {{ $slot ?? 'Dropdown' }}
        </button>
    @endif

    {{-- Menu --}}
    <div x-show="open"
         @click.outside="open = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute z-50 w-56 rounded-lg bg-surface border border-border shadow-xl py-1 mt-{{ $offset }}
                {{ $placement === 'bottom-start' ? 'left-0' : ($placement === 'bottom-end' ? 'right-0' : '') }}
                {{ $placement === 'top-start' ? 'bottom-full left-0 mb-2' : '' }}
                {{ $placement === 'top-end' ? 'bottom-full right-0 mb-2' : '' }}"
         x-cloak>

        {{ $dropdownContent }}
    </div>
</div>
