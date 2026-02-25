{{-- Login Card Component --}}
@props([
    'padding' => 'lg',
    'class' => ''
])

@php
    $paddingClasses = [
        'none' => '',
        'sm' => 'p-3 sm:p-4',
        'default' => 'p-4 sm:p-5 md:p-6',
        'lg' => 'p-4 sm:p-6 md:p-8',
    ];
@endphp

<div class="w-full max-w-md mx-auto {{ $class }}">
    {{-- Theme & Language Switcher --}}
    <div class="flex justify-end gap-2 mb-4">

        {{-- Theme Switcher - 3 Icons --}}
        <div class="flex items-center h-10 bg-surface/90 backdrop-blur rounded-xl px-1 border border-border shadow-sm" x-data="themeController()">
            <button @click="setTheme('light')"
                    class="p-2 h-full rounded-lg transition-all duration-200 flex items-center justify-center"
                    :class="theme === 'light' ? 'bg-blue-500 text-white shadow-md' : 'text-gray-600 hover:bg-blue-500 hover:text-white'"
                    title="Light Theme">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </button>
            <button @click="setTheme('dark')"
                    class="p-2 h-full rounded-lg transition-all duration-200 flex items-center justify-center"
                    :class="theme === 'dark' ? 'bg-gray-900 text-white shadow-md' : 'text-gray-600 hover:bg-gray-900 hover:text-white'"
                    title="Dark Theme">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                </svg>
            </button>
            <button @click="setTheme('stylis')"
                    class="p-2 h-full rounded-lg transition-all duration-200 flex items-center justify-center"
                    :class="theme === 'stylis' ? 'bg-teal-500 text-white shadow-md' : 'text-gray-600 hover:bg-teal-500 hover:text-white'"
                    title="Stylis Theme">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                </svg>
            </button>
        </div>

        {{-- Language Switcher --}}
        <div class="relative" x-data="langController()">
            <button @click="toggleLang = !toggleLang"
                    class="h-10 px-3 rounded-xl bg-surface/90 backdrop-blur border border-border hover:bg-surface-hover flex items-center justify-center transition-all shadow-sm text-text-secondary">
                <span class="text-lg" x-text="getLangInfo().flag"></span>
            </button>

            <div x-show="toggleLang"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 @click.away="toggleLang = false"
                 class="absolute top-full right-0 mt-2 w-36 rounded-xl bg-surface border border-border shadow-xl overflow-hidden z-50"
                 x-cloak>
                <template x-for="lang in availableLangs" :key="lang.code">
                    <button @click="setLang(lang.code)"
                            class="w-full flex items-center gap-3 px-4 py-3 hover:bg-surface-hover transition-colors"
                            :class="{ 'bg-surface-alt': lang.code === currentLang }">
                        <span class="text-xl" x-text="lang.flag"></span>
                        <span class="text-sm font-medium text-text-primary" x-text="lang.name"></span>
                    </button>
                </template>
            </div>
        </div>
    </div>

    {{-- Card --}}
    <div class="rounded-2xl bg-surface border border-border shadow-lg {{ $paddingClasses[$padding] }}">
        {{ $slot }}
    </div>
</div>
