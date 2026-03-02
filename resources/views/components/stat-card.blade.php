{{-- Stat Card Component --}}
@props([
    'title' => '',
    'dataTranslateTitle' => null,
    'value' => '',
    'change' => null,
    'changeType' => 'positive', // 'positive', 'negative', 'neutral'
    'icon' => null,
    'color' => 'primary', // 'primary', 'success', 'danger', 'warning', 'info'
    'class' => ''
])

@php
    $gradients = [
        'primary' => 'from-blue-500 to-blue-600',
        'success' => 'from-emerald-500 to-emerald-600',
        'danger' => 'from-rose-500 to-rose-600',
        'warning' => 'from-amber-500 to-amber-600',
        'info' => 'from-sky-500 to-sky-600',
    ];
    $bgGradient = $gradients[$color] ?? $gradients['primary'];

    $changeColors = [
        'positive' => 'text-emerald-600',
        'negative' => 'text-rose-600',
        'neutral' => 'text-text-secondary',
    ];

    $bgMild = [
        'primary' => 'bg-primary-50',
        'success' => 'bg-success-50',
        'danger' => 'bg-danger-50',
        'warning' => 'bg-warning-50',
        'info' => 'bg-info-50',
    ];
@endphp

<div class="rounded-2xl bg-surface p-5 sm:p-6 border border-border shadow-sm hover:shadow-md transition-all duration-300 {{ $class }}">
    <div class="flex items-start justify-between">
        <div class="flex-1">
            <p class="text-sm font-medium text-text-secondary mb-1"
               @if($dataTranslateTitle) data-translate="{{ $dataTranslateTitle }}" @endif
            >{{ $title }}</p>
            <p class="text-3xl font-bold text-text-primary">
                @if(trim((string) $slot) !== '')
                    {{ $slot }}
                @else
                    {{ $value }}
                @endif
            </p>

            @if($change !== null)
                <p class="mt-2 flex items-center gap-1 text-sm font-medium {{ $changeColors[$changeType] }}">
                    @if($changeType === 'positive')
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                        </svg>
                    @elseif($changeType === 'negative')
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                        </svg>
                    @endif
                    <span>{{ $change }}</span>
                </p>
            @endif
        </div>

        @if($icon)
            <div class="w-14 h-14 rounded-2xl bg-linear-to-br {{ $bgGradient }} flex items-center justify-center shrink-0 shadow-lg">
                <span class="w-7 h-7 text-white">{!! $icon !!}</span>
            </div>
        @endif
    </div>
</div>
