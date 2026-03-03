<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" x-data="themeController()" :class="{
    'theme-light': theme === 'light',
    'theme-dark': theme === 'dark' || (theme === 'stylis' && isDark),
    'theme-stylis': theme === 'stylis'
}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="locale" content="{{ app()->getLocale() }}">
    <meta name="app-timezone" content="{{ $appTimezone ?? config('app.timezone') }}">

    {{-- Prevent theme flash before Alpine loads --}}
    <script>
        (() => {
            try {
                const t = localStorage.getItem('roxwood-theme') || 'light';
                const html = document.documentElement;
                html.classList.remove('theme-light', 'theme-dark', 'theme-stylis');
                if (t === 'dark') html.classList.add('theme-dark');
                else if (t === 'stylis') html.classList.add('theme-stylis');
                else html.classList.add('theme-light');
            } catch {}
        })();
    </script>

    <title>@yield('title', __('messages.login') . ' - ' . ($appName ?? config('app.name')))</title>

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

	    {{-- Guest Content --}}
	    <div class="min-h-screen grid place-items-center px-4 sm:px-6 py-12">
	        @yield('content')
    </div>

    {{-- Toast Container --}}
    <x-toast />

    {{-- Flash -> Toast --}}
    @include('partials.flash-toasts')

    {{-- Vite JS --}}
    @vite(['resources/js/app.js'])

    @stack('scripts')
</body>
</html>
