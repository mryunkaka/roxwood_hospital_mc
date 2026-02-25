{{-- Data Table Component - Demo Version --}}
@props([
    'headers' => [],
    'data' => [],
    'title' => null,
    'pagination' => true,
    'searchable' => true,
    'class' => ''
])

@php
    $tableId = 'datatable-' . uniqid();
@endphp

<div class="w-full {{ $class }}" x-cloak>
    {{-- Table Card Container --}}
    <div class="rounded-2xl bg-surface border border-border shadow overflow-hidden
                theme-dark:bg-slate-800 theme-dark:border-slate-700
                theme-stylis:bg-white/80 theme-stylis:border-teal-200">

        {{-- Table Header with Search and Actions --}}
        @if($searchable || $title)
        <div class="p-4 border-b border-border flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4
                    theme-dark:border-slate-700
                    theme-stylis:border-teal-100/50">
            @if($title)
                <h3 class="text-lg font-semibold text-text-primary">{{ $title }}</h3>
            @endif

            @if($searchable)
            <div class="relative w-full sm:w-64">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" placeholder="Search..." class="w-full pl-10 pr-4 py-2 rounded-xl bg-surface border border-border focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none transition-all text-sm placeholder:text-text-hint theme-dark:bg-slate-700 theme-dark:border-slate-600 theme-dark:text-white theme-stylis:bg-white/60 theme-stylis:border-teal-200">
            </div>
        </div>
        @endif

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-surface-alt border-b border-border theme-dark:bg-slate-800/50 theme-dark:border-slate-700 theme-stylis:bg-teal-50/30 theme-stylis:border-teal-100/50">
                    <tr>
                        @foreach($headers as $header)
                            <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wider">
                                {{ $header['label'] }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-border theme-dark:divide-slate-700">
                    @foreach($data as $row)
                    <tr class="hover:bg-surface-hover transition-colors theme-dark:hover:bg-slate-700/30 theme-stylis:hover:bg-teal-50/30">
                        @foreach($headers as $header)
                            <td class="px-4 py-3 text-sm text-text-secondary whitespace-nowrap">
                                {{ $row[$header['key']] ?? '-' }}
                            </td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination (if enabled) --}}
        @if($pagination && count($data) > 0)
            <div class="p-4 border-t border-border flex flex flex-col sm:flex-row items-center justify-between gap-4 theme-dark:border-slate-700 theme-stylis:border-teal-100/50">
                <div class="text-sm text-text-secondary">
                    Showing <span class="font-medium text-text-primary">1</span>
                    to <span class="font-medium text-text-primary">{{ count($data) }}</span>
                    of <span class="font-medium text-text-primary">{{ count($data) }}</span> results
                </div>
                <div class="flex items-center gap-1">
                    <button disabled class="px-3 py-2 rounded-lg border border-border opacity-50 cursor-not-allowed transition-colors text-sm theme-dark:border-slate-600 theme-stylis:border-teal-200">Previous</button>
                    <button class="px-3 py-2 rounded-lg bg-primary text-white border-primary text-sm">1</button>
                    <button class="px-3 py-2 rounded-lg border border-border hover:bg-surface-hover text-sm theme-dark:bg-slate-700 theme-dark:border-slate-600 theme-stylis:border-teal-200">2</button>
                    <button class="px-3 py-2 rounded-lg border border-border hover:bg-surface-hover text-sm theme-dark:bg-slate-700 theme-dark:border-slate-600 theme-stylis:border-teal-200">Next</button>
                </div>
            </div>
        @endif
    </div>
</div>
