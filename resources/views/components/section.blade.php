{{-- Section Component --}}
@props([
    'class' => ''
])

<section class="{{ $class }}">
    {{ $slot }}
</section>
