{{-- Sidebar Component --}}
<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0 lg:w-20'"
       class="fixed lg:relative z-50 h-full w-64 bg-surface/80 backdrop-blur-md border-r border-border transition-transform duration-300 ease-in-out"
       x-cloak>

    {{-- Sidebar Header --}}
    <div class="flex items-center justify-between h-16 px-4 border-b border-border">
        {{-- Logo --}}
        <div class="flex items-center gap-3 overflow-hidden" :class="{ 'justify-center lg:w-full': !sidebarOpen && window.innerWidth >= 1024 }">
            <div class="w-10 h-10 rounded-xl bg-linear-to-br from-primary to-primary-dark flex items-center justify-center shrink-0 shadow-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <div :class="sidebarOpen ? 'block' : 'hidden lg:block'" class="transition-all">
                <h1 class="font-bold text-lg text-text-primary">Roxwood</h1>
                <p class="text-xs text-text-secondary">Health Medical</p>
            </div>
        </div>

        {{-- Close Button (Mobile Only) --}}
        <button @click="closeSidebar()"
                class="lg:hidden p-2 rounded-xl hover:bg-surface-hover transition-colors text-text-secondary">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    {{-- Sidebar Navigation --}}
    <nav class="flex-1 overflow-y-auto p-3 space-y-1 scrollbar-thin">

        {{-- Dashboard Link --}}
        <a href="{{ route('dashboard') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 group
                  {{ request()->routeIs('dashboard') ? 'bg-primary text-white shadow-md' : 'text-text-secondary hover:bg-surface-hover hover:text-text-primary' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
            </svg>
            <span :class="sidebarOpen ? 'block' : 'hidden lg:block'" class="font-medium" data-translate="dashboard_menu">{{ __('messages.dashboard') }}</span>
        </a>

        {{-- Components Link --}}
        <a href="{{ route('components') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 group
                  {{ request()->routeIs('components') ? 'bg-primary text-white shadow-md' : 'text-text-secondary hover:bg-surface-hover hover:text-text-primary' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/>
            </svg>
            <span :class="sidebarOpen ? 'block' : 'hidden lg:block'" class="font-medium" data-translate="components_menu">{{ __('messages.components') }}</span>
        </a>

        {{-- Settings Link --}}
        <a href="{{ route('settings') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 group
                  {{ request()->routeIs('settings') ? 'bg-primary text-white shadow-md' : 'text-text-secondary hover:bg-surface-hover hover:text-text-primary' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <span :class="sidebarOpen ? 'block' : 'hidden lg:block'" class="font-medium" data-translate="settings_menu">{{ __('messages.settings') }}</span>
        </a>

    </nav>

    {{-- Sidebar Footer --}}
    <div class="p-4 border-t border-border">
        {{-- User Info --}}
        <div class="flex items-center gap-3 p-3 rounded-xl bg-surface-alt">
            <div class="w-10 h-10 rounded-xl bg-linear-to-br from-primary to-primary-dark flex items-center justify-center text-white font-semibold shadow">
                A
            </div>
            <div :class="sidebarOpen ? 'block' : 'hidden lg:block'" class="flex-1 min-w-0">
                <p class="font-medium text-sm text-text-primary truncate">Admin User</p>
                <p class="text-xs text-text-secondary truncate">admin@roxwood.com</p>
            </div>
        </div>

        {{-- Copyright Footer --}}
        <p class="text-xs text-center text-text-tertiary mt-3" :class="sidebarOpen ? 'block' : 'hidden lg:block'">
            © {{ date('Y') }} Roxwood
        </p>
    </div>
</aside>
