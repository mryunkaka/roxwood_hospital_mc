{{-- Sidebar Component --}}
<aside
       x-show="!sidebarHidden"
       x-transition.opacity.duration.150ms
       :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0 lg:w-20'"
       class="fixed lg:relative z-50 h-[100svh] w-64 bg-surface/80 backdrop-blur-md border-r border-border transition-transform duration-300 ease-in-out flex flex-col"
       x-cloak>

    {{-- Sidebar Header --}}
    <div class="flex items-center justify-between h-16 px-4 border-b border-border">
        {{-- Logo --}}
        <div class="flex items-center gap-3 overflow-hidden" :class="{ 'justify-center lg:w-full': !sidebarOpen && window.innerWidth >= 1024 }">
            <div class="w-10 h-10 rounded-xl bg-surface flex items-center justify-center shrink-0 shadow-lg border border-border overflow-hidden">
                <img
                    src="{{ $appLogoUrl }}"
                    alt="{{ $appName }}"
                    class="w-full h-full object-cover"
                    loading="lazy"
                >
	            </div>
		            <div :class="sidebarOpen ? 'block' : 'hidden'" class="transition-all flex-1 min-w-0">
                        <h1
                            class="w-full font-bold tracking-tight text-text-primary whitespace-nowrap overflow-hidden text-ellipsis leading-tight text-[20px]"
                            title="{{ $appName ?? config('app.name') }}"
                        >
                            {{ $appName ?? config('app.name') }}
                        </h1>
                        <p class="text-xs text-text-secondary whitespace-nowrap overflow-hidden text-ellipsis">
                            {{ $appTagline ?? 'Health Medical' }}
                        </p>
		            </div>
	        </div>

        {{-- Close Button (Mobile Only) --}}
        <button @click="closeSidebar()"
                class="lg:hidden p-2 rounded-xl bg-surface-alt border border-border cursor-pointer hover:bg-surface-hover transition-colors text-text-secondary">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    {{-- Sidebar Navigation --}}
	    <nav class="flex-1 min-h-0 overflow-y-scroll p-3 space-y-1 scrollbar-thin" style="scrollbar-gutter: stable;">

        {{-- Group: Main --}}
        <div :class="sidebarOpen ? 'block' : 'hidden'">
            <p class="px-3 pb-1 text-[11px] font-semibold text-text-tertiary uppercase tracking-wider" data-translate="sidebar_group_main">
                {{ __('messages.sidebar_group_main') }}
            </p>
        </div>

        {{-- Dashboard Link --}}
        <a href="{{ route('dashboard') }}"
           @click="window.innerWidth < 1024 && closeSidebar()"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 group
                  {{ request()->routeIs('dashboard') ? 'bg-primary text-white shadow-md' : 'text-text-secondary hover:bg-surface-hover hover:text-text-primary' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
            </svg>
            <span :class="sidebarOpen ? 'block' : 'hidden'" class="font-medium" data-translate="dashboard_menu">{{ __('messages.dashboard') }}</span>
        </a>

        {{-- Rekap Farmasi Link --}}
        @php
            $userPosition = (string) session('user.position', '');
            $isTrainee = \Illuminate\Support\Str::of($userPosition)->lower()->contains('trainee');
            $userRoleNorm = \Illuminate\Support\Str::of((string) session('user.role', ''))->lower()->trim()->toString();
            $isStaffRole = $userRoleNorm === 'staff';
        @endphp
        @if(!$isTrainee)
            {{-- Group: Farmasi --}}
            <div class="pt-2" :class="sidebarOpen ? 'block' : 'hidden'">
                <p class="px-3 pb-1 text-[11px] font-semibold text-text-tertiary uppercase tracking-wider" data-translate="sidebar_group_farmasi">
                    {{ __('messages.sidebar_group_farmasi') }}
                </p>
            </div>

            <a href="{{ route('farmasi.rekap') }}"
               @click="window.innerWidth < 1024 && closeSidebar()"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 group
                      {{ request()->routeIs('farmasi.rekap') ? 'bg-primary text-white shadow-md' : 'text-text-secondary hover:bg-surface-hover hover:text-text-primary' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5l5 5v11a2 2 0 01-2 2z"/>
                </svg>
                <span :class="sidebarOpen ? 'block' : 'hidden'" class="font-medium" data-translate="farmasi_rekap_menu">{{ __('messages.farmasi_rekap_menu') }}</span>
            </a>

            @if(!$isStaffRole)
                <a href="{{ route('farmasi.regulasi') }}"
                   @click="window.innerWidth < 1024 && closeSidebar()"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 group
                          {{ request()->routeIs('farmasi.regulasi') ? 'bg-primary text-white shadow-md' : 'text-text-secondary hover:bg-surface-hover hover:text-text-primary' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h8l4 4v12a2 2 0 01-2 2H6a2 2 0 01-2-2V6z"/>
                    </svg>
                    <span :class="sidebarOpen ? 'block' : 'hidden'" class="font-medium" data-translate="farmasi_regulasi_menu">{{ __('messages.farmasi_regulasi_menu') }}</span>
                </a>
            @endif

            <a href="{{ route('farmasi.konsumen') }}"
               @click="window.innerWidth < 1024 && closeSidebar()"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 group
                      {{ request()->routeIs('farmasi.konsumen') ? 'bg-primary text-white shadow-md' : 'text-text-secondary hover:bg-surface-hover hover:text-text-primary' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span :class="sidebarOpen ? 'block' : 'hidden'" class="font-medium" data-translate="farmasi_konsumen_menu">{{ __('messages.farmasi_konsumen_menu') }}</span>
            </a>

            <a href="{{ route('farmasi.gaji') }}"
               @click="window.innerWidth < 1024 && closeSidebar()"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 group
                      {{ request()->routeIs('farmasi.gaji') ? 'bg-primary text-white shadow-md' : 'text-text-secondary hover:bg-surface-hover hover:text-text-primary' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-10V6m0 12v-2m9-4a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span :class="sidebarOpen ? 'block' : 'hidden'" class="font-medium" data-translate="farmasi_gaji_menu">{{ __('messages.farmasi_gaji_menu') }}</span>
            </a>

            {{-- Reimbursement Link --}}
            <a href="{{ route('reimbursement.index') }}"
               @click="window.innerWidth < 1024 && closeSidebar()"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 group
                      {{ request()->routeIs('reimbursement.*') ? 'bg-primary text-white shadow-md' : 'text-text-secondary hover:bg-surface-hover hover:text-text-primary' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l2-2 4 4m0 0l2-2m-2 2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2v-1"/>
                </svg>
                <span :class="sidebarOpen ? 'block' : 'hidden'" class="font-medium" data-translate="reimbursement_menu">{{ __('messages.reimbursement_menu') }}</span>
            </a>

            {{-- Restaurant Consumption Link --}}
            <a href="{{ route('restaurant.consumption.index') }}"
               @click="window.innerWidth < 1024 && closeSidebar()"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 group
                      {{ request()->routeIs('restaurant.consumption.*') ? 'bg-primary text-white shadow-md' : 'text-text-secondary hover:bg-surface-hover hover:text-text-primary' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18"/>
                </svg>
                <span :class="sidebarOpen ? 'block' : 'hidden'" class="font-medium" data-translate="restaurant_consumption_menu">{{ __('messages.restaurant_consumption_menu') }}</span>
            </a>

            {{-- Group: Medis --}}
            <div class="pt-2" :class="sidebarOpen ? 'block' : 'hidden'">
                <p class="px-3 pb-1 text-[11px] font-semibold text-text-tertiary uppercase tracking-wider" data-translate="sidebar_group_medis">
                    {{ __('messages.sidebar_group_medis') }}
                </p>
            </div>

	            <a href="{{ route('medis.ems') }}"
	               @click="window.innerWidth < 1024 && closeSidebar()"
	               class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 group
	                      {{ request()->routeIs('medis.ems') ? 'bg-primary text-white shadow-md' : 'text-text-secondary hover:bg-surface-hover hover:text-text-primary' }}">
	                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
	                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.5 12.75l6 6 9-13.5"/>
	                </svg>
	                <span :class="sidebarOpen ? 'block' : 'hidden'" class="font-medium" data-translate="medis_services_menu">{{ __('messages.medis_services_menu') }}</span>
	            </a>

                <a href="{{ route('medis.operasi_plastik.index') }}"
                   @click="window.innerWidth < 1024 && closeSidebar()"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 group
                          {{ request()->routeIs('medis.operasi_plastik.*') ? 'bg-primary text-white shadow-md' : 'text-text-secondary hover:bg-surface-hover hover:text-text-primary' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c1.656 0 3-1.567 3-3.5S13.656 4 12 4s-3 1.567-3 3.5S10.344 11 12 11zm7 9a7 7 0 00-14 0"/>
                    </svg>
                    <span :class="sidebarOpen ? 'block' : 'hidden'" class="font-medium" data-translate="operasi_plastik_menu">{{ __('messages.operasi_plastik_menu') }}</span>
                </a>

	            @if(!$isStaffRole)
	                <a href="{{ route('medis.regulasi') }}"
	                   @click="window.innerWidth < 1024 && closeSidebar()"
	                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 group
                          {{ request()->routeIs('medis.regulasi') ? 'bg-primary text-white shadow-md' : 'text-text-secondary hover:bg-surface-hover hover:text-text-primary' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5l5 5v11a2 2 0 01-2 2z"/>
                    </svg>
                    <span :class="sidebarOpen ? 'block' : 'hidden'" class="font-medium" data-translate="medis_regulasi_menu">{{ __('messages.medis_regulasi_menu') }}</span>
                </a>
            @endif
        @endif

        {{-- Group: Tools --}}
        <div class="pt-2" :class="sidebarOpen ? 'block' : 'hidden'">
            <p class="px-3 pb-1 text-[11px] font-semibold text-text-tertiary uppercase tracking-wider" data-translate="sidebar_group_tools">
                {{ __('messages.sidebar_group_tools') }}
            </p>
        </div>

        @if(!$isStaffRole)
            {{-- Monitoring Jam Duty --}}
            <a href="{{ route('duty.monitor') }}"
               @click="window.innerWidth < 1024 && closeSidebar()"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 group
                      {{ request()->routeIs('duty.monitor') ? 'bg-primary text-white shadow-md' : 'text-text-secondary hover:bg-surface-hover hover:text-text-primary' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 2m6-2a10 10 0 11-20 0 10 10 0 0120 0z"/>
                </svg>
                <span :class="sidebarOpen ? 'block' : 'hidden'" class="font-medium" data-translate="duty_monitor_menu">{{ __('messages.duty_monitor_menu') }}</span>
            </a>
        @endif

        {{-- Components Link --}}
        <a href="{{ route('components') }}"
           @click="window.innerWidth < 1024 && closeSidebar()"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 group
                  {{ request()->routeIs('components') ? 'bg-primary text-white shadow-md' : 'text-text-secondary hover:bg-surface-hover hover:text-text-primary' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/>
            </svg>
            <span :class="sidebarOpen ? 'block' : 'hidden'" class="font-medium" data-translate="components_menu">{{ __('messages.components') }}</span>
        </a>

        {{-- Patients Link --}}
        <a href="{{ route('patients') }}"
           @click="window.innerWidth < 1024 && closeSidebar()"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 group
                  {{ request()->routeIs('patients') ? 'bg-primary text-white shadow-md' : 'text-text-secondary hover:bg-surface-hover hover:text-text-primary' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            <span :class="sidebarOpen ? 'block' : 'hidden'" class="font-medium" data-translate="patients_menu">{{ __('messages.patients') }}</span>
        </a>

        {{-- Settings Group Label --}}
        <div class="pt-2" :class="sidebarOpen ? 'block' : 'hidden'">
            <p class="px-3 pb-1 text-[11px] font-semibold text-text-tertiary uppercase tracking-wider" data-translate="sidebar_group_settings">
                {{ __('messages.sidebar_group_settings') }}
            </p>
        </div>

        @if(!$isStaffRole)
            {{-- Validasi Akun Link --}}
            <a href="{{ route('validasi.index') }}"
               @click="window.innerWidth < 1024 && closeSidebar()"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 group
                      {{ request()->routeIs('validasi.*') ? 'bg-primary text-white shadow-md' : 'text-text-secondary hover:bg-surface-hover hover:text-text-primary' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span :class="sidebarOpen ? 'block' : 'hidden'" class="font-medium" data-translate="validation_menu">{{ __('messages.validation_menu') }}</span>
            </a>

            {{-- Manajemen User Link --}}
            <a href="{{ route('users.manage') }}"
               @click="window.innerWidth < 1024 && closeSidebar()"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 group
                      {{ request()->routeIs('users.manage') ? 'bg-primary text-white shadow-md' : 'text-text-secondary hover:bg-surface-hover hover:text-text-primary' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span :class="sidebarOpen ? 'block' : 'hidden'" class="font-medium" data-translate="manage_users_menu">{{ __('messages.manage_users_menu') }}</span>
            </a>
        @endif

        {{-- Settings Link --}}
        <a href="{{ route('settings') }}"
           @click="window.innerWidth < 1024 && closeSidebar()"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 group
                  {{ request()->routeIs('settings') ? 'bg-primary text-white shadow-md' : 'text-text-secondary hover:bg-surface-hover hover:text-text-primary' }}">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <span :class="sidebarOpen ? 'block' : 'hidden'" class="font-medium" data-translate="settings_menu">{{ __('messages.settings') }}</span>
        </a>

    </nav>

    {{-- Sidebar Footer --}}
    <div class="p-4 border-t border-border">
        @php
            $ui = $authUserUi ?? null;
        @endphp

        {{-- User Info --}}
        <div class="p-3 rounded-xl bg-surface-alt border border-border">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl overflow-hidden shadow border border-border bg-surface shrink-0">
                    @if(!empty($ui['photo_url']))
                        <img src="{{ $ui['photo_url'] }}" alt="Profile" class="w-full h-full object-cover" loading="lazy">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-linear-to-br from-primary to-primary-dark text-white font-semibold">
                            {{ $ui['initial'] ?? 'A' }}
                        </div>
                    @endif
                </div>

                <div :class="sidebarOpen ? 'block' : 'hidden'" class="flex-1 min-w-0">
                    <p class="font-semibold text-sm text-text-primary break-words leading-snug">
                        {{ $ui['full_name'] ?? '-' }}
                    </p>

                    <div class="mt-2 space-y-1.5">
                        @if(!empty($ui['batch']))
                            <div class="flex items-start justify-between gap-3 text-xs">
                                <span class="text-text-secondary" data-translate="batch">{{ __('messages.batch') }}</span>
                                <span class="font-medium text-text-primary text-right break-words">{{ $ui['batch'] }}</span>
                            </div>
                        @endif

                        @if(!empty($ui['position']))
                            <div class="flex items-start justify-between gap-3 text-xs">
                                <span class="text-text-secondary" data-translate="position">{{ __('messages.position') }}</span>
                                <span class="font-medium text-text-primary text-right break-words">{{ $ui['position'] }}</span>
                            </div>
                        @endif

                        @if(!empty($ui['role']))
                            <div class="flex items-start justify-between gap-3 text-xs">
                                <span class="text-text-secondary" data-translate="role">{{ __('messages.role') }}</span>
                                <span class="font-medium text-text-primary text-right break-words">{{ $ui['role'] }}</span>
                            </div>
                        @endif
                    </div>

                    <div
                        class="mt-3 flex items-start justify-between gap-3 text-xs"
                        x-data="{
                            joinId: @js($ui['join_date_id'] ?? null),
                            joinEn: @js($ui['join_date_en'] ?? null),
                            tenureId: @js($ui['tenure_id'] ?? null),
                            tenureEn: @js($ui['tenure_en'] ?? null),
                            joinDate: null,
                            tenure: null,
                            init() {
                                const apply = (lang) => {
                                    this.joinDate = lang === 'id' ? this.joinId : this.joinEn;
                                    this.tenure = lang === 'id' ? this.tenureId : this.tenureEn;
                                };
                                apply(window.globalLangState?.currentLang || 'id');
                                window.addEventListener('language-changed', (e) => apply(e.detail.lang));
                            }
                        }"
                    >
                        <div class="flex items-start gap-2 text-text-secondary min-w-0">
                            <svg class="w-4 h-4 mt-0.5 text-text-tertiary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div class="font-medium text-text-primary text-right break-words leading-snug" x-text="tenure || '-'"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Copyright Footer --}}
        <p class="text-xs text-center text-text-tertiary mt-3" :class="sidebarOpen ? 'block' : 'hidden'">
            &copy; {{ date('Y') }} {{ $appName ?? config('app.name') }}
        </p>
    </div>
</aside>
