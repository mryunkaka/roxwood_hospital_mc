@extends('layouts.guest')

@section('title', __('messages.login'))

@push('styles')
<style>
    .autocomplete-dropdown {
        position: absolute;
        width: 100%;
        max-height: 300px;
        overflow-y: auto;
        background: var(--color-surface);
        border: 1px solid var(--color-border);
        border-radius: 0.5rem;
        box-shadow: var(--shadow-lg);
        z-index: 50;
        margin-top: 4px;
        overflow: hidden;
    }

    .autocomplete-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px;
        cursor: pointer;
        border-bottom: 1px solid var(--color-border-light);
        transition: background-color 0.2s;
    }

    .autocomplete-item:last-child {
        border-bottom: none;
    }

    .autocomplete-item:hover,
    .autocomplete-item.selected {
        background-color: var(--color-surface-hover);
    }

    .autocomplete-item.disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .autocomplete-item-avatar {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        object-fit: cover;
        flex-shrink: 0;
    }

    .autocomplete-avatar {
        width: 52px;
        height: 52px;
        border-radius: 9999px;
        padding: 2px;
        flex-shrink: 0;
        display: grid;
        place-items: center;
        background: var(--color-border-medium);
        box-shadow: var(--shadow-xs);
    }

    .autocomplete-avatar-inner {
        width: 100%;
        height: 100%;
        border-radius: 9999px;
        background: var(--color-surface);
        padding: 2px;
        display: grid;
        place-items: center;
    }

    .autocomplete-avatar--trainee {
        background: linear-gradient(135deg, #94a3b8, #cbd5e1);
    }

    .autocomplete-avatar--paramedic {
        background: linear-gradient(135deg, #3b82f6, #60a5fa);
    }

    .autocomplete-avatar--coast {
        background: linear-gradient(135deg, #10b981, #34d399);
    }

    .autocomplete-avatar--doctor {
        background: linear-gradient(135deg, #ec4899, #f472b6);
    }

    .autocomplete-avatar--specialist-doctor {
        background: linear-gradient(135deg, #f59e0b, #fbbf24);
    }

    .autocomplete-avatar--manager {
        background: linear-gradient(135deg, #ef4444, #f87171);
    }

    .autocomplete-avatar--director {
        background: conic-gradient(from 180deg, #ec4899, #f59e0b, #ef4444, #ec4899);
    }

    .autocomplete-item-info {
        flex: 1;
        min-width: 0;
    }

    .autocomplete-item-name {
        font-weight: 600;
        color: var(--color-text-primary);
        margin-bottom: 2px;
    }

    .autocomplete-item-details {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
        font-size: 0.85rem;
        color: var(--color-text-secondary);
    }

    .autocomplete-item-badge {
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 500;
    }

    .autocomplete-item-badge.role {
        background-color: var(--color-primary-100);
        color: var(--color-primary-700);
    }

    .autocomplete-item-badge.batch {
        background-color: var(--color-warning-100);
        color: var(--color-warning-700);
    }

    .autocomplete-item-status {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .autocomplete-item-status.online {
        background-color: #22c55e;
        box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.2);
    }

    .autocomplete-item-status.offline {
        background-color: #9ca3af;
    }

    .autocomplete-item-inactive {
        font-size: 0.75rem;
        color: #ef4444;
        margin-top: 4px;
    }

    .theme-stylis .autocomplete-dropdown {
        background: rgba(255, 255, 255, 0.92);
        border-color: rgba(20, 184, 166, 0.35);
        backdrop-filter: blur(14px);
        -webkit-backdrop-filter: blur(14px);
    }

    .theme-stylis .autocomplete-item:hover,
    .theme-stylis .autocomplete-item.selected {
        background-color: rgba(20, 184, 166, 0.08);
    }

    .theme-stylis .autocomplete-item-name {
        color: #0f2f2f;
    }

    .theme-stylis .autocomplete-item-details {
        color: rgba(45, 90, 90, 0.9);
    }

    .theme-dark .autocomplete-dropdown {
        background: rgba(10, 15, 26, 0.98);
        border-color: rgba(96, 165, 250, 0.25);
    }

    .theme-dark .autocomplete-item:hover,
    .theme-dark .autocomplete-item.selected {
        background-color: rgba(37, 51, 69, 0.9);
    }

    .theme-dark .autocomplete-item-name {
        color: #f1f5f9;
    }

    .theme-dark .autocomplete-item-details {
        color: rgba(203, 213, 225, 0.9);
    }

    .theme-dark .autocomplete-item-badge.role {
        background-color: rgba(96, 165, 250, 0.18);
        color: #bfdbfe;
    }

    .theme-dark .autocomplete-item-badge.batch {
        background-color: rgba(245, 158, 11, 0.18);
        color: #fde68a;
    }

    .theme-stylis .autocomplete-item-badge.role {
        background-color: rgba(20, 184, 166, 0.14);
        color: #0d9488;
    }

    .theme-stylis .autocomplete-item-badge.batch {
        background-color: rgba(20, 184, 166, 0.10);
        color: #0f766e;
    }

    .pin-input::-webkit-outer-spin-button,
    .pin-input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    .pin-input {
        -moz-appearance: textfield;
    }

    .modal-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 100;
        display: flex;
        align-items: center;
        justify-content: center;
    }

	    .modal-content {
	        background: var(--color-surface);
	        border: 1px solid var(--color-border);
	        border-radius: 1rem;
	        padding: 1.5rem;
	        max-width: 400px;
	        width: 90%;
	        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
	    }

	    .theme-stylis .modal-content {
	        background: rgba(255, 255, 255, 0.92);
	        border-color: rgba(20, 184, 166, 0.35);
	        box-shadow: 0 24px 50px -16px rgba(13, 148, 136, 0.25);
	        backdrop-filter: blur(14px);
	        -webkit-backdrop-filter: blur(14px);
	    }

	    .theme-stylis.theme-dark .modal-content {
	        background: rgba(20, 40, 40, 0.92);
	        border-color: rgba(153, 213, 201, 0.28);
	        box-shadow: 0 24px 50px -16px rgba(0, 0, 0, 0.55);
	    }
	</style>
	@endpush

@section('content')
    <div x-data="loginCooldown()" x-init="init()">
    <x-login-card :padding="'lg'">
        {{-- Page Header with Logo --}}
        <x-page-header :title="__('messages.login')" :subtitle="__('messages.login_subtitle')" :dataTranslateTitle="'login'" :dataTranslateSubtitle="'login_subtitle'" />

        {{-- Success/Error Messages --}}
        <div class="space-y-4 mb-6">
            @if (session('success'))
                <x-alert type="success" :icon="true" :autoHide="true" :title="session('success_title')">
                    <p class="text-sm">{{ session('success') }}</p>
                </x-alert>
            @endif

            @if (session('error'))
                <x-alert type="danger" :icon="true">
                    <p class="text-sm">{{ session('error') }}</p>
                </x-alert>
            @endif

            @if ($errors->any())
                <x-alert type="danger" :icon="true">
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </x-alert>
            @endif

            <div x-show="disabled" x-cloak class="rounded-xl border border-warning-200 bg-warning-50/30 p-4 text-sm text-warning-600">
                <span data-translate="login_cooldown_prefix">{{ __('messages.login_cooldown_prefix') }}</span>
                <span class="font-semibold" x-text="remaining"></span>
                <span data-translate="login_cooldown_suffix">{{ __('messages.login_cooldown_suffix') }}</span>
            </div>
        </div>

        {{-- Login Form --}}
        <form method="POST" action="{{ route('login.post') }}" class="space-y-5" id="loginForm"
              @submit.prevent="if (disabled) return; $event.target.submit()">
            @csrf
            <input type="hidden" name="force_login" id="force_login" value="0">

            {{-- Full Name Input with Autocomplete --}}
            <div class="relative" x-data="autocomplete()" x-init="init()" @click.outside="showDropdown = false">
                <x-input type="text" name="full_name" :label="__('messages.full_name')"
                    placeholder="{{ __('messages.full_name_placeholder') }}" :dataTranslateLabel="'full_name'" :dataTranslatePlaceholder="'full_name_placeholder'"
                    :required="true" autocomplete="off" autocorrect="off" autocapitalize="words"
                    :value="$savedFullName ?? ''"
                    x-model="query"
                    x-ref="input"
                    @input.debounce.300="search($event.target.value)"
                    @focus="showDropdown = true"
                    @keydown.arrow-down.prevent="highlightNext()"
                    @keydown.arrow-up.prevent="highlightPrevious()"
                    @keydown.enter.prevent="selectHighlighted()"
                    @keydown.escape="showDropdown = false"
                    class="pin-input" />

                {{-- Autocomplete Dropdown --}}
                <div x-show="showDropdown" class="autocomplete-dropdown" x-cloak
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100">
                    <template x-for="(user, index) in results" :key="user.id">
                        <div
                            x-show="results.length > 0"
                            class="autocomplete-item"
                            :class="{ 'selected': highlightedIndex === index, 'disabled': !user.is_active }"
                            @click="selectUser(user)"
                            @mouseenter="highlightedIndex = index">
                            <div class="autocomplete-avatar" :class="'autocomplete-avatar--' + (user.avatar_variant || 'trainee')">
                                <div class="autocomplete-avatar-inner">
                                    <img :src="user.photo" class="autocomplete-item-avatar" alt="Avatar">
                                </div>
                            </div>
                            <div class="autocomplete-item-info">
                                <div class="autocomplete-item-name" x-text="user.full_name"></div>
                                <div class="autocomplete-item-details">
                                    <span class="autocomplete-item-badge role" x-text="user.role"></span>
                                    <span class="autocomplete-item-badge batch" x-text="'Batch ' + user.batch"></span>
                                    <span x-text="user.position"></span>
                                </div>
                                <div x-show="!user.is_active" class="autocomplete-item-inactive">{{ __('messages.account_not_validated') }}</div>
                            </div>
                            <div
                                class="autocomplete-item-status"
                                :class="user.is_online ? 'online' : 'offline'"
                                :title="user.is_online ? activeTitle : inactiveTitle">
                            </div>
                        </div>
                    </template>
                    <div x-show="results.length === 0 && searching" class="p-4 text-center text-gray-500 dark:text-gray-400">
                        {{ __('messages.searching_users') }}
                    </div>
                    <div x-show="results.length === 0 && !searching && query.length >= 2" class="p-4 text-center text-gray-500 dark:text-gray-400">
                        {{ __('messages.no_users_found') }}
                    </div>
                </div>
            </div>

            {{-- PIN Input --}}
            <x-input type="password" name="pin" :label="__('messages.pin')" placeholder="{{ __('messages.pin_placeholder') }}"
                :dataTranslateLabel="'pin'" :dataTranslatePlaceholder="'pin_placeholder'" :required="true" maxlength="4"
                inputmode="numeric" autocomplete="current-password" :hint="__('messages.pin_hint')" :dataTranslateHint="'pin_hint'"
                :value="$savedPin ?? ''"
                class="pin-input"
                oninput="this.value = this.value.replace(/[^0-9]/g, '')" />

            {{-- Remember Me & Forgot Password --}}
            <div class="flex items-center justify-between gap-4">
                <x-checkbox name="remember" :label="__('messages.remember_me')" :dataTranslateLabel="'remember_me'"
                    :value="'on'" :checked="$savedFullName !== null" />
                <a href="#"
                    class="text-sm text-primary-500 hover:text-primary-600 theme-stylis:text-teal-600 theme-stylis:hover:text-teal-700 whitespace-nowrap"
                    data-translate="forgot_password">
                    {{ __('messages.forgot_password') }}
                </a>
            </div>

            {{-- Submit Button --}}
            <x-button type="submit" variant="primary" size="lg" :fullWidth="true" x-bind:disabled="disabled">
                <span data-translate="login">{{ __('messages.login') }}</span>
            </x-button>
        </form>

        {{-- Register Link --}}
        <p class="mt-6 text-center text-sm text-text-secondary">
            <span data-translate="dont_have_account">{{ __('messages.dont_have_account') }}</span>
            <a href="{{ route('register') }}"
                class="text-primary-500 hover:text-primary-600 theme-stylis:text-teal-600 theme-stylis:hover:text-teal-700 font-medium"
                data-translate="register">
                {{ __('messages.register') }}
            </a>
        </p>
    </x-login-card>

    {{-- Confirm Force Login Modal --}}
    @php
        $forceLoginModalData = [
            'show' => (bool) session('confirm_force_login'),
            'activeDevice' => (string) session('active_device', ''),
            'fullName' => (string) session('full_name', ''),
            'pin' => (string) session('pin', ''),
            'remember' => (bool) session('remember'),
        ];
    @endphp
	    <div class="modal-backdrop"
	         x-data='@json($forceLoginModalData)'
	         x-show="show"
	         x-transition
	         x-cloak>
	        <div class="modal-content" x-show="show" x-transition>
	            <h3 class="text-lg font-semibold mb-2" data-translate="login_confirm_title">{{ __('messages.login_confirm_title') }}</h3>
	            <p class="text-sm text-text-secondary mb-4" data-translate="login_confirm_message">{{ __('messages.login_confirm_message') }}</p>
	            @if(session('active_device'))
	                <p class="text-sm text-text-tertiary mb-6">
	                    <strong class="text-text-primary">{{ __('messages.currently_active') }}:</strong> {{ session('active_device') }}
	                </p>
	            @endif
	            <div class="flex gap-3 justify-end">
	                <button type="button" @click="show = false"
	                        class="px-4 py-2 rounded-xl border border-border bg-surface hover:bg-surface-hover text-text-primary transition-colors">
	                    <span data-translate="cancel_login">{{ __('messages.cancel_login') }}</span>
	                </button>
	                <form method="POST" action="{{ route('login.post') }}" class="inline">
	                    @csrf
	                    <input type="hidden" name="full_name" x-model="fullName">
	                    <input type="hidden" name="pin" x-model="pin">
	                    <input type="hidden" name="remember" value="1" x-show="remember">
	                    <input type="hidden" name="force_login" value="1">
	                    <button type="submit"
	                            class="px-4 py-2 rounded-xl bg-danger text-white hover:bg-danger-600 transition-colors">
	                        <span data-translate="force_login">{{ __('messages.force_login') }}</span>
	                    </button>
	                </form>
	            </div>
	        </div>
	    </div>

    </div>

@push('scripts')
<script>
    function loginCooldown() {
        return {
            remaining: 0,

            init() {
                this.tick();
                setInterval(() => this.tick(), 1000);
            },

            tick() {
                const until = parseInt(localStorage.getItem('loginCooldownUntil') || '0', 10);
                const diffMs = until - Date.now();
                this.remaining = diffMs > 0 ? Math.ceil(diffMs / 1000) : 0;
                if (this.remaining === 0) {
                    localStorage.removeItem('loginCooldownUntil');
                }
            },

            get disabled() {
                return this.remaining > 0;
            }
        };
    }

    function autocomplete() {
        return {
            query: @json($savedFullName ?? ''),
            results: [],
            showDropdown: false,
            highlightedIndex: -1,
            searching: false,
            searchTimeout: null,
            activeTitle: @json(__('messages.currently_active')),
            inactiveTitle: @json(__('messages.currently_inactive')),

            init() {
                // Initialize input with saved value
                if (this.query) {
                    this.$refs.input.value = this.query;
                }
            },

            search(query) {
                this.query = query;
                this.highlightedIndex = -1;

                if (query.length < 2) {
                    this.results = [];
                    this.showDropdown = false;
                    this.searching = false;
                    return;
                }

                this.showDropdown = true;
                this.searching = true;

                clearTimeout(this.searchTimeout);
                this.searchTimeout = setTimeout(() => {
                    fetch(`{{ route('api.users.search') }}?q=${encodeURIComponent(query)}`)
                        .then(response => response.json())
                        .then(data => {
                            this.results = data.results || [];
                        })
                        .catch(() => {
                            this.results = [];
                        })
                        .finally(() => {
                            this.searching = false;
                        });
                }, 300);
            },

            selectUser(user) {
                if (!user.is_active) return;

                this.$refs.input.value = user.full_name;
                this.results = [];
                this.showDropdown = false;
                this.query = user.full_name;

                // Trigger input event to update any listeners
                this.$refs.input.dispatchEvent(new Event('input', { bubbles: true }));
            },

            selectHighlighted() {
                if (this.highlightedIndex >= 0 && this.highlightedIndex < this.results.length) {
                    this.selectUser(this.results[this.highlightedIndex]);
                }
            },

            highlightNext() {
                if (this.results.length === 0) return;
                this.highlightedIndex = (this.highlightedIndex + 1) % this.results.length;
            },

            highlightPrevious() {
                if (this.results.length === 0) return;
                this.highlightedIndex = this.highlightedIndex <= 0
                    ? this.results.length - 1
                    : this.highlightedIndex - 1;
            }
        };
    }
</script>
@endpush

@endsection
