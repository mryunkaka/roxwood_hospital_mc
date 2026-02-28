<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="theme-light" x-data="themeController()" :class="{
    'theme-dark': theme === 'dark' || (theme === 'stylis' && isDark),
    'theme-stylis': theme === 'stylis'
}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="locale" content="{{ app()->getLocale() }}">

    <title>@yield('title', __('messages.login') . ' - ' . __('messages.app_name'))</title>

    {{-- Favicon --}}
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

    {{-- Vite CSS --}}
    @vite(['resources/css/app.css'])

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Custom Styles --}}
    <style>
        :root {
            --font-sans: 'Inter', ui-sans-serif, system-ui, -apple-system, sans-serif;
        }
        html {
            scrollbar-gutter: stable;
        }
        body {
            font-family: var(--font-sans);
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        /* Alpine.js x-cloak - hide until Alpine is ready */
        [x-cloak] { display: none !important; }
    </style>

    @stack('styles')
</head>
<body class="min-h-screen bg-background">

    {{-- Language & Theme Switcher (Top Right) --}}
    <div class="fixed top-4 right-4 z-50 flex items-center gap-2">
        {{-- Language Switcher --}}
        <div class="relative" x-data="langController()">
            <button
                @click="toggleLang = !toggleLang"
                @click.outside="toggleLang = false"
                class="flex items-center gap-2 px-3 py-2 rounded-lg bg-surface border border-border hover:bg-surface-hover transition-colors shadow-sm"
                :aria-label="currentLang === 'id' ? 'Ganti Bahasa' : 'Switch Language'"
            >
                <svg class="w-4 h-4 text-text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/>
                </svg>
                <span class="text-sm font-medium text-text-primary" x-text="currentLang === 'id' ? 'ID' : 'EN'"></span>
                <svg class="w-3 h-3 text-text-tertiary" :class="{ 'rotate-180': toggleLang }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            <div
                x-show="toggleLang"
                x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="opacity-0 scale-95 translate-y-1"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-75"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-1"
                class="absolute right-0 mt-2 w-48 rounded-lg bg-surface border border-border shadow-lg py-1 z-50"
                x-cloak
            >
                <button
                    @click="setLang('en')"
                    class="w-full px-4 py-2 text-left text-sm hover:bg-surface-hover transition-colors flex items-center gap-3"
                    :class="{ 'bg-surface-alt': currentLang === 'en' }"
                >
                    <svg class="w-5 h-5 text-text-secondary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/>
                    </svg>
                    <div>
                        <div class="font-medium text-text-primary">English</div>
                        <div class="text-xs text-text-tertiary">United States</div>
                    </div>
                </button>
                <button
                    @click="setLang('id')"
                    class="w-full px-4 py-2 text-left text-sm hover:bg-surface-hover transition-colors flex items-center gap-3"
                    :class="{ 'bg-surface-alt': currentLang === 'id' }"
                >
                    <svg class="w-5 h-5 text-text-secondary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/>
                    </svg>
                    <div>
                        <div class="font-medium text-text-primary">Bahasa Indonesia</div>
                        <div class="text-xs text-text-tertiary">Indonesia</div>
                    </div>
                </button>
            </div>
        </div>

        {{-- Theme Switcher --}}
        <button
            @click="toggleTheme()"
            class="p-2 rounded-lg bg-surface border border-border hover:bg-surface-hover transition-colors shadow-sm"
            :aria-label="theme === 'dark' ? 'Switch to Light Mode' : 'Switch to Dark Mode'"
        >
            <svg x-show="theme === 'light'" class="w-5 h-5 text-text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            <svg x-show="theme === 'dark'" class="w-5 h-5 text-text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
            </svg>
            <svg x-show="theme === 'stylis'" class="w-5 h-5 text-teal-600 theme-stylis:text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
            </svg>
        </button>
    </div>

    {{-- Guest Content --}}
    <div class="min-h-screen grid place-items-center px-4 sm:px-6 py-12">
        @yield('content')
    </div>

    {{-- Toast Container --}}
    <x-toast />

    {{-- Vite JS --}}
    @vite(['resources/js/app.js'])

    @stack('scripts')
</body>
</html>
