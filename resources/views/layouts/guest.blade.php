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
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

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
        body {
            font-family: var(--font-sans);
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
    </style>

    @stack('styles')
</head>
<body class="min-h-screen">

    {{-- Guest Content --}}
    <div class="min-h-screen flex items-center justify-center p-4 sm:p-6 w-full">
        @yield('content')
    </div>

    {{-- Vite JS --}}
    @vite(['resources/js/app.js'])

    @stack('scripts')
</body>
</html>
