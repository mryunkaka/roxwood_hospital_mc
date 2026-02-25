{{-- Pagination Component --}}
@props([
    'currentPage' => 1,
    'totalPages' => 1,
    'onEachSide' => 2,
    'class' => ''
])

@php
    $pages = [];

    // Add first page
    if ($currentPage > $onEachSide + 1) {
        $pages[] = 1;
        if ($currentPage > $onEachSide + 2) {
            $pages[] = '...';
        }
    }

    // Add pages around current
    for ($i = max(1, $currentPage - $onEachSide); $i <= min($totalPages, $currentPage + $onEachSide); $i++) {
        $pages[] = $i;
    }

    // Add last page
    if ($currentPage < $totalPages - $onEachSide) {
        if ($currentPage < $totalPages - $onEachSide - 1) {
            $pages[] = '...';
        }
        $pages[] = $totalPages;
    }
@endphp

<nav class="flex items-center justify-center gap-1 {{ $class }}">
    {{-- Previous --}}
    @if($currentPage > 1)
        <a href="?page={{ $currentPage - 1 }}"
           class="p-2 rounded-lg border border-border hover:bg-surface-hover transition-colors">
            <svg class="w-5 h-5 text-text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
    @else
        <span class="p-2 rounded-lg border border-border opacity-50 cursor-not-allowed">
            <svg class="w-5 h-5 text-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </span>
    @endif

    {{-- Page Numbers --}}
    @foreach($pages as $page)
        @if($page === '...')
            <span class="px-3 py-2 text-text-muted">...</span>
        @else
            <a href="?page={{ $page }}"
               class="px-3 py-2 rounded-lg border transition-colors
                      {{ $page == $currentPage
                          ? 'bg-primary-500 text-white border-primary-500'
                          : 'border-border hover:bg-surface-hover' }}">
                {{ $page }}
            </a>
        @endif
    @endforeach

    {{-- Next --}}
    @if($currentPage < $totalPages)
        <a href="?page={{ $currentPage + 1 }}"
           class="p-2 rounded-lg border border-border hover:bg-surface-hover transition-colors">
            <svg class="w-5 h-5 text-text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
    @else
        <span class="p-2 rounded-lg border border-border opacity-50 cursor-not-allowed">
            <svg class="w-5 h-5 text-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </span>
    @endif
</nav>
