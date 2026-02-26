<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="theme-light"
      x-data="{
          ...themeController(),
          ...accessibilityController()
      }"
      :class="{
          'theme-dark': theme === 'dark' || (theme === 'stylis' && isDark),
          'theme-stylis': theme === 'stylis',
          'high-contrast': highContrast,
          'reduced-motion': reducedMotion
      }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="locale" content="{{ app()->getLocale() }}">

    <title>@yield('title', __('messages.app_name'))</title>

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
<body class="bg-background text-text-primary antialiased min-h-screen"
      :class="{ 'overflow-hidden': sidebarOpen && window.innerWidth < 1024 }">

    {{-- App Container --}}
    <div class="flex h-screen overflow-hidden bg-pattern">

        {{-- Sidebar --}}
        @include('layouts.sidebar')

        {{-- Main Content Area --}}
        <div class="flex-1 flex flex-col overflow-hidden">

            {{-- Navbar --}}
            @include('layouts.navbar')

            {{-- Page Content --}}
            <main class="flex-1 overflow-y-auto p-4 lg:p-6">
                @yield('content')
            </main>

        </div>

    </div>

    {{-- Mobile Overlay --}}
    <div x-show="sidebarOpen && window.innerWidth < 1024"
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/50 backdrop-blur-sm z-40 lg:hidden"
         @click="closeSidebar()"
         x-cloak></div>

    {{-- Toast Container --}}
    <x-toast />

    {{-- Vite JS --}}
    @vite(['resources/js/app.js'])

    @stack('scripts')
</body>
</html>
