{{-- Avatar Component --}}
@props([
    'src' => null,
    'alt' => 'Avatar',
    'name' => null,
    'size' => 'md', // 'xs', 'sm', 'md', 'lg', 'xl', '2xl'
    'rounded' => 'full', // 'full', 'lg', 'md', 'none'
    'status' => null, // 'online', 'offline', 'away', 'busy'
    'class' => ''
])

@php
    $sizeClasses = [
        'xs' => 'w-6 h-6 text-xs',
        'sm' => 'w-8 h-8 text-sm',
        'md' => 'w-10 h-10 text-base',
        'lg' => 'w-12 h-12 text-lg',
        'xl' => 'w-16 h-16 text-xl',
        '2xl' => 'w-20 h-20 text-2xl',
    ];
    $roundedClasses = [
        'full' => 'rounded-full',
        'lg' => 'rounded-xl',
        'md' => 'rounded-lg',
        'none' => 'rounded-none',
    ];
    $statusColors = [
        'online' => 'bg-success-500',
        'offline' => 'bg-slate-400',
        'away' => 'bg-warning-500',
        'busy' => 'bg-danger-500',
    ];

    // Get initials from name
    $initials = '';
    if ($name && !$src) {
        $words = explode(' ', trim($name));
        if (count($words) >= 2) {
            $initials = strtoupper(substr($words[0], 0, 1) . substr($words[count($words) - 1], 0, 1));
        } else {
            $initials = strtoupper(substr($name, 0, 2));
        }
    }

    // Generate consistent color based on name
    $colors = ['from-primary-400 to-primary-600', 'from-success-400 to-success-600', 'from-warning-400 to-warning-600', 'from-danger-400 to-danger-600', 'from-info-400 to-info-600', 'from-purple-400 to-purple-600', 'from-pink-400 to-pink-600', 'from-indigo-400 to-indigo-600'];
    $colorIndex = $name ? crc32($name) % count($colors) : 0;
    $bgGradient = $colors[$colorIndex];
@endphp

<div class="relative inline-flex {{ $class }}">
    {{-- Avatar --}}
    <div class="{{ $sizeClasses[$size] }} {{ $roundedClasses[$rounded] }} overflow-hidden flex items-center justify-center shrink-0 bg-linear-to-br {{ $bgGradient }} text-white font-semibold shadow-sm">
        @if($src)
            <img src="{{ $src }}" alt="{{ $alt }}" class="w-full h-full object-cover">
        @elseif($initials)
            {{ $initials }}
        @else
            <svg class="w-1/2 h-1/2 text-white/50" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
            </svg>
        @endif
    </div>

    {{-- Status Indicator --}}
    @if($status)
        <span class="absolute bottom-0 right-0 w-3 h-3 {{ $sizeClasses[$size] === 'w-6 h-6 text-xs' ? 'w-2 h-2' : '' }} rounded-full border-2 border-white {{ $statusColors[$status] }}"></span>
    @endif
</div>
