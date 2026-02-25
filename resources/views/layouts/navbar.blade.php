{{-- Navbar Component --}}
<header class="h-16 bg-surface/80 backdrop-blur-md border-b border-border flex items-center justify-between px-4 lg:px-6 sticky top-0 z-30">
    {{-- Left: Mobile Menu & Breadcrumb --}}
    <div class="flex items-center gap-3">
        {{-- Mobile Menu Toggle --}}
        <button @click="sidebarOpen = !sidebarOpen"
                class="lg:hidden p-2.5 rounded-xl bg-surface-alt border border-border hover:bg-surface-hover transition-colors text-text-secondary">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>

        {{-- Breadcrumb (Desktop) --}}
        <nav class="hidden lg:flex items-center gap-2 text-sm">
            <span class="text-text-secondary">Home</span>
            <svg class="w-4 h-4 text-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="font-medium text-text-primary">@yield('page-title', 'Dashboard')</span>
        </nav>
    </div>

    {{-- Right: Theme, Clock, Search, Notifications, Profile --}}
    <div class="flex items-center gap-2">

        {{-- Theme Switcher - 3 Icons --}}
        <div class="flex items-center bg-surface-alt rounded-xl p-1 border border-border" x-data="themeController()">
            <button @click="setTheme('light')"
                    class="p-2 rounded-lg transition-all duration-200"
                    :class="theme === 'light' ? 'bg-primary-100 text-primary-700 shadow-md' : 'text-text-secondary hover:bg-primary-50 hover:text-primary-600'"
                    title="Light Theme">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </button>
            <button @click="setTheme('dark')"
                    class="p-2 rounded-lg transition-all duration-200"
                    :class="theme === 'dark' ? 'bg-surface-hover text-text-primary shadow-md' : 'text-text-secondary hover:bg-surface-hover hover:text-text-primary'"
                    title="Dark Theme">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                </svg>
            </button>
            <button @click="setTheme('stylis')"
                    class="p-2 rounded-lg transition-all duration-200"
                    :class="theme === 'stylis' ? 'bg-teal-100 text-teal-700 shadow-md' : 'text-text-secondary hover:bg-teal-50 hover:text-teal-600'"
                    title="Stylis Theme">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                </svg>
            </button>
        </div>

        {{-- Clock Widget (Hidden on small mobile) --}}
        <div x-data="clockController()" class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-xl bg-surface-alt border border-border">
            <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span class="text-xs font-medium text-text-primary" x-text="currentTime"></span>
        </div>

        {{-- Language Switcher --}}
        <div class="relative" x-data="langController()">
            <button @click="toggleLang = !toggleLang"
                    class="flex items-center gap-2 px-3 py-2 rounded-xl bg-surface-alt border border-border hover:bg-surface-hover transition-all">
                <span class="text-lg" x-text="getLangInfo().flag"></span>
                <span class="text-sm font-medium text-text-primary" x-text="getLangInfo().code.toUpperCase()"></span>
                <svg class="w-4 h-4 text-text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            <div x-show="toggleLang"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 @click.away="toggleLang = false"
                 class="absolute top-full right-0 mt-2 w-44 rounded-xl bg-surface border border-border shadow-xl overflow-hidden z-50"
                 x-cloak>
                <button @click="setLang('en')"
                        class="w-full flex items-center gap-3 px-4 py-3 hover:bg-surface-hover transition-colors border-l-4"
                        :class="currentLang === 'en' ? 'bg-surface-alt border-primary' : 'border-transparent'">
                    <span class="text-xl">🇺🇸</span>
                    <div class="text-left">
                        <p class="text-sm font-medium text-text-primary">English</p>
                        <p class="text-xs text-text-secondary">United States</p>
                    </div>
                </button>
                <button @click="setLang('id')"
                        class="w-full flex items-center gap-3 px-4 py-3 hover:bg-surface-hover transition-colors border-l-4"
                        :class="currentLang === 'id' ? 'bg-surface-alt border-primary' : 'border-transparent'">
                    <span class="text-xl">🇮🇩</span>
                    <div class="text-left">
                        <p class="text-sm font-medium text-text-primary">Bahasa Indonesia</p>
                        <p class="text-xs text-text-secondary">Indonesia</p>
                    </div>
                </button>
            </div>
        </div>

        {{-- Search (Desktop) --}}
        <div class="hidden lg:block relative">
            <input type="text"
                   placeholder="Search..."
                   class="w-56 pl-10 pr-4 py-2 rounded-xl bg-surface border border-border focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none transition-all text-sm placeholder:text-text-hint">
            <svg class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </div>

        {{-- Notifications --}}
        <button class="relative p-2.5 rounded-xl hover:bg-surface-hover transition-colors text-text-secondary">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
            <span class="absolute top-1.5 right-1.5 w-2.5 h-2.5 rounded-full bg-danger-500 border-2 border-surface"></span>
        </button>

        {{-- Profile Dropdown --}}
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open"
                    class="flex items-center gap-3 p-1 rounded-xl hover:bg-surface-hover transition-colors">
                <div class="w-9 h-9 rounded-xl bg-linear-to-br from-primary to-primary-dark flex items-center justify-center text-white text-sm font-semibold shadow-md">
                    A
                </div>
                <svg class="w-4 h-4 text-text-secondary hidden sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            <div x-show="open"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 @click.away="open = false"
                 class="absolute top-full right-0 mt-2 w-56 rounded-xl bg-surface border border-border shadow-xl overflow-hidden z-50"
                 x-cloak>
                <a href="#" class="flex items-center gap-3 px-4 py-3 hover:bg-surface-hover transition-colors">
                    <svg class="w-5 h-5 text-text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <span class="text-sm text-text-primary">Profile</span>
                </a>
                <a href="#" class="flex items-center gap-3 px-4 py-3 hover:bg-surface-hover transition-colors">
                    <svg class="w-5 h-5 text-text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span class="text-sm text-text-primary">Settings</span>
                </a>
                <hr class="border-border">
                <form method="POST" action="{{ route('logout') }}" @csrf>
                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 hover:bg-danger-50 transition-colors text-danger">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        <span class="text-sm">Logout</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
