{{-- Table Component --}}
@props([
    'headers' => [],
    'striped' => false,
    'bordered' => false,
    'hoverable' => true,
    'compact' => false,
    'class' => ''
])

@php
    $tableClass = 'w-full text-left border-collapse ' .
                  ($bordered ? 'border border-border divide-y divide-border' : '') . ' ' .
                  ($striped ? 'divide-y divide-border' : '');
    $thClass = 'px-4 py-3 text-xs font-semibold tracking-wide text-text-secondary uppercase bg-surface ' .
               ($compact ? 'py-2' : 'py-3');
    $tdClass = 'px-4 ' . ($compact ? 'py-2' : 'py-3') . ' text-sm text-text-primary ' .
               ($striped ? 'even:bg-surface-alt' : '') . ' ' .
               ($hoverable ? 'hover:bg-surface-hover transition-colors' : '');
@endphp

<div class="overflow-x-auto rounded-lg border border-border {{ $class }}">
    <table class="{{ $tableClass }}">
        @if(!empty($headers))
            <thead>
                <tr>
                    @foreach($headers as $header)
                        <th class="{{ $thClass }}">
                            {{ is_array($header) ? $header['label'] : $header }}
                        </th>
                    @endforeach
                </tr>
            </thead>
        @endif

        <tbody {{ $striped ? 'class="divide-y divide-border"' : '' }}>
            {{ $slot }}
        </tbody>
    </table>

    @if(empty($slot))
        <div class="p-8 text-center text-text-secondary">
            <svg class="w-12 h-12 mx-auto mb-3 text-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
            </svg>
            <p>{{ __('messages.no_data') }}</p>
        </div>
    @endif
</div>
