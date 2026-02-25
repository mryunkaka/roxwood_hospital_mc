{{-- Breadcrumb Component --}}
@props([
    'pages' => [],
    'separator' => 'chevron',
    'class' => ''
])

@php
    $separatorIcon = $separator === 'chevron'
        ? '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>'
        : '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>';
@endphp

<nav class="flex items-center gap-2 text-sm {{ $class }}">
    {{-- Home --}}
    <a href="{{ url('/') }}"
       class="flex items-center gap-1 text-text-secondary hover:text-primary-500 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
        </svg>
    </a>

    {{-- Pages --}}
    @foreach($pages as $index => $page)
        <span class="text-text-muted">
            {!! $separatorIcon !!}
        </span>

        @if($index === count($pages) - 1)
            <span class="font-medium text-text-primary">
                {{ $page['title'] ?? $page }}
            </span>
        @else
            <a href="{{ $page['url'] ?? '#' }}"
               class="text-text-secondary hover:text-primary-500 transition-colors">
                {{ $page['title'] ?? $page }}
            </a>
        @endif
    @endforeach
</nav>
