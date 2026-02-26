{{-- Skeleton Loading Component --}}
---@props([
    'type' => 'text', // 'text' | 'circle' | 'card' | 'table' | 'custom'
    'width' => '100%', // width for text/bar type
    'height' => null, // height for bar type
    'lines' => 3, // number of lines for text type
    'class' => ''
])

@php
    $skeletonColor = 'bg-gradient-to-r from-gray-100 via-gray-50 to-gray-100 dark:from-gray-700 dark:via-gray-600 dark:to-gray-700';
    $skeletonAnimation = 'animate-shimmer';
@endphp

@if ($type === 'text')
    {{-- Text Skeleton - Single line or multiple lines --}}
    <div class="skeleton-text space-y-2 {{ $class }}">
        @for($i = 0; $i < $lines; $i++)
            <div class="{{ $skeletonColor }} {{ $skeletonAnimation }} rounded"
                 style="width: {{ $i === $lines - 1 ? '70%' : '100%' }}; height: 0.75rem;">
            </div>
        @endfor
    </div>

@elseif ($type === 'circle')
    {{-- Circle Skeleton - Avatar loader --}}
    <div class="skeleton-circle {{ $skeletonColor }} {{ $skeletonAnimation }} rounded-full {{ $class }}"
         style="width: {{ $width }}; height: {{ $width }}; min-width: 32px; min-height: 32px;">
    </div>

@elseif ($type === 'card')
    {{-- Card Skeleton - Full card placeholder --}}
    <div class="skeleton-card bg-surface border border-border rounded-xl p-4 shadow-sm {{ $class }}">
        {{-- Header with title and avatar --}}
        <div class="flex items-center gap-3 mb-4">
            <div class="{{ $skeletonColor }} {{ $skeletonAnimation }} rounded-full w-12 h-12"></div>
            <div class="flex-1">
                <div class="{{ $skeletonColor }} {{ $skeletonAnimation }} rounded h-4 w-3/4 mb-2"></div>
                <div class="{{ $skeletonColor }} {{ $skeletonAnimation }} rounded h-3 w-1/2"></div>
            </div>
        </div>
        {{-- Content lines --}}
        <div class="{{ $skeletonColor }} {{ $skeletonAnimation }} rounded h-3 w-full mb-2"></div>
        <div class="{{ $skeletonColor }} {{ $skeletonAnimation }} rounded h-3 w-full mb-2"></div>
        <div class="{{ $skeletonColor }} {{ $skeletonAnimation }} rounded h-3 w-2/3"></div>
    </div>

@elseif ($type === 'table')
    {{-- Table Skeleton - Table rows placeholder --}}
    <div class="skeleton-table {{ $class }}">
        {{-- Header --}}
        <div class="flex gap-2 mb-3">
            <div class="{{ $skeletonColor }} {{ $skeletonAnimation }} rounded h-4 flex-1"></div>
            <div class="{{ $skeletonColor }} {{ $skeletonAnimation }} rounded h-4 flex-1"></div>
            <div class="{{ $skeletonColor }} {{ $skeletonAnimation }} rounded h-4 flex-1"></div>
            <div class="{{ $skeletonColor }} {{ $skeletonAnimation }} rounded h-4 w-20"></div>
        </div>
        {{-- Rows --}}
        @for($i = 0; $i < 3; $i++)
            <div class="flex gap-2 mb-2">
                <div class="{{ $skeletonColor }} {{ $skeletonAnimation }} rounded h-10 flex-1"></div>
                <div class="{{ $skeletonColor }} {{ $skeletonAnimation }} rounded h-10 flex-1"></div>
                <div class="{{ $skeletonColor }} {{ $skeletonAnimation }} rounded h-10 flex-1"></div>
                <div class="{{ $skeletonColor }} {{ $skeletonAnimation }} rounded h-10 w-20"></div>
            </div>
        @endfor
    </div>

@elseif ($type === 'custom')
    {{-- Custom Skeleton - User provided content --}}
    <div class="{{ $class }}">
        {{ $slot }}
    </div>
@endif

@once
@push('styles)
<style>
/* Shimmer animation */
@keyframes shimmer {
    0% {
        background-position: -200% 0;
    }
    100% {
        background-position: 200% 0;
    }
}

.animate-shimmer {
    background: linear-gradient(
        90deg,
        rgba(255, 255, 255, 0) 0%,
        rgba(255, 255, 255, 0.3) 50%,
        rgba(255, 255, 255, 0) 100%
    );
    background-size: 200% 100%;
    animation: shimmer 1.5s infinite;
}

.theme-dark .animate-shimmer {
    background: linear-gradient(
        90deg,
        rgba(255, 255, 255, 0) 0%,
        rgba(255, 255, 255, 0.1) 50%,
        rgba(255, 255, 255, 0) 100%
    );
}

/* Skeleton text lines */
.skeleton-text > div {
    background: linear-gradient(
        90deg,
        #f0f0f0 0%,
        #e0e0e0 50%,
        #f0f0f0 100%
    );
    background-size: 200% 100%;
    animation: shimmer 1.5s infinite;
}

.theme-dark .skeleton-text > div {
    background: linear-gradient(
        90deg,
        #374151 0%,
        #4b5563 50%,
        #374151 100%
    );
}

.theme-stylis .skeleton-text > div {
    background: linear-gradient(
        90deg,
        rgba(255, 255, 255, 0.3) 0%,
        rgba(255, 255, 255, 0.5) 50%,
        rgba(255, 255, 255, 0.3) 100%
    );
}
</style>
@endpush
@endonce
