{{-- Tabs Component --}}
@props([
    'tabs' => [],
    'activeTab' => null,
    'variant' => 'underline', // 'underline', 'pills', 'bordered'
])

@php
    $variantClasses = [
        'underline' => [
            'tab' => 'px-4 py-2 text-sm font-medium transition-colors relative',
            'active' => 'text-primary-500',
            'inactive' => 'text-text-secondary hover:text-text-primary',
            'indicator' => 'absolute bottom-0 left-0 right-0 h-0.5 bg-primary-500 rounded-full',
        ],
        'pills' => [
            'tab' => 'px-4 py-2 text-sm font-medium rounded-lg transition-colors',
            'active' => 'bg-primary-500 text-white',
            'inactive' => 'text-text-secondary hover:bg-surface-hover hover:text-text-primary',
            'indicator' => '',
        ],
        'bordered' => [
            'tab' => 'px-4 py-2 text-sm font-medium border-b-2 transition-colors -mb-px',
            'active' => 'border-primary-500 text-primary-500',
            'inactive' => 'border-transparent text-text-secondary hover:text-text-primary hover:border-border',
            'indicator' => '',
        ],
    ];
    $classes = $variantClasses[$variant] ?? $variantClasses['underline'];
@endphp

<div x-data="{ activeTab: '{{ $activeTab ?? ($tabs[0]['key'] ?? key($tabs)) }}' }" class="w-full">
    {{-- Tab Headers --}}
    <div class="flex gap-1 {{ $variant === 'bordered' ? 'border-b border-border' : '' }}">
        @foreach($tabs as $tab)
            @php
                $tabKey = is_array($tab) ? $tab['key'] : $tab;
                $tabLabel = is_array($tab) ? $tab['label'] : $tab;
            @endphp

            <button @click="activeTab = '{{ $tabKey }}'"
                    :class="activeTab === '{{ $tabKey }}' ? '{{ $classes['active'] }}' : '{{ $classes['inactive'] }}'"
                    class="{{ $classes['tab'] }}">
                {{ $tabLabel }}
                @if($variant === 'underline' && $tabKey === ($activeTab ?? ($tabs[0]['key'] ?? key($tabs))))
                    <span class="{{ $classes['indicator'] }}"></span>
                @endif
            </button>
        @endforeach
    </div>

    {{-- Tab Content --}}
    <div class="mt-4">
        @foreach($tabs as $tab)
            @php
                $tabKey = is_array($tab) ? $tab['key'] : $tab;
            @endphp
            <div x-show="activeTab === '{{ $tabKey }}'"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-cloak>
                @isset($tab['content'])
                    {{ $tab['content'] }}
                @endisset
            </div>
        @endforeach
    </div>

    {{-- Slot for custom content --}}
    {{ $slot ?? '' }}
</div>
