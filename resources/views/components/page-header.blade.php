{{-- Page Header Component - Consistent page headers with optional logo --}}
@props([
    'title' => '',
    'subtitle' => null,
    'showLogo' => true,
    'logoSize' => 'md',
    'dataTranslateTitle' => null,
    'dataTranslateSubtitle' => null,
    'class' => ''
])

<div class="text-center mb-8 {{ $class }}">
    @if($showLogo)
        <div class="inline-flex items-center justify-center gap-3 mb-6">
            <x-brand-logo :size="$logoSize" />
        </div>
    @endif

    @if($title)
        <h2 class="text-xl font-semibold text-text-primary" @if($dataTranslateTitle) data-translate="{{ $dataTranslateTitle }}" @endif>
            {!! $title !!}
        </h2>
    @endif

    @if($subtitle)
        <p class="mt-2 text-text-secondary" @if($dataTranslateSubtitle) data-translate="{{ $dataTranslateSubtitle }}" @endif>
            {!! $subtitle !!}
        </p>
    @endif
</div>
