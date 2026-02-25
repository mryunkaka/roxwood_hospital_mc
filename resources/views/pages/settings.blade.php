@extends('layouts.app')

@section('title', __('messages.settings') . ' - ' . __('messages.app_name'))

@section('page-title', __('messages.settings'))
@section('page-description', __('messages.settings') . ' - ' . __('messages.notifications'))

@section('content')

<div class="max-w-4xl">
    {{-- Settings Tabs --}}
    <x-card :padding="'none'">
        <x-tabs :tabs="[
            ['key' => 'general', 'label' => __('messages.general')],
            ['key' => 'appearance', 'label' => __('messages.appearance')],
            ['key' => 'notifications', 'label' => __('messages.notifications')],
            ['key' => 'security', 'label' => __('messages.security')],
        ]" :activeTab="'general'" :variant="'bordered'">

            {{-- General Settings --}}
            <div class="p-6 space-y-6">
                <h3 class="text-lg font-semibold text-text-primary theme-dark:text-white">{{ __('messages.general_settings') }}</h3>

                <form class="space-y-5">
                    <x-input
                        type="text"
                        name="site_name"
                        :label="__('messages.site_name')"
                        value="Roxwood Health Medical Center"
                        :hint="__('messages.the_name_of_your_medical_center')"
                    />

                    <x-input
                        type="email"
                        name="admin_email"
                        :label="__('messages.admin_email')"
                        value="admin@roxwood.com"
                        :hint="__('messages.administrative_contact_email')"
                    />

                    <div>
                        <label class="block text-sm font-medium text-text-primary mb-2 theme-dark:text-white">
                            {{ __('messages.timezone') }}
                        </label>
                        <select class="w-full rounded-lg border border-border px-4 py-2.5 text-sm outline-none transition-all duration-200 bg-white text-text-primary focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 theme-dark:bg-slate-800 theme-dark:border-slate-700 theme-dark:text-white">
                            <option>Asia/Jakarta (GMT+7)</option>
                            <option>Asia/Singapore (GMT+8)</option>
                            <option>Pacific/Auckland (GMT+12)</option>
                        </select>
                    </div>

                    <x-button type="submit" variant="primary">
                        {{ __('messages.save_changes') }}
                    </x-button>
                </form>
            </div>

            {{-- Appearance Settings --}}
            <div class="p-6 space-y-6">
                <h3 class="text-lg font-semibold text-text-primary theme-dark:text-white">{{ __('messages.appearance_settings') }}</h3>

                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-text-primary mb-3 theme-dark:text-white">
                            {{ __('messages.theme_preference') }}
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <label class="relative">
                                <input type="radio" name="theme" value="light" class="peer sr-only">
                                <div class="p-4 rounded-lg border-2 border-border cursor-pointer transition-all peer-checked:border-primary-500 peer-checked:bg-primary-50 theme-dark:peer-checked:bg-primary-900/20 hover:bg-surface theme-dark:hover:bg-slate-800">
                                    <div class="flex items-center gap-3">
                                        <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                                        </svg>
                                        <span class="font-medium text-text-primary theme-dark:text-white">{{ __('messages.theme_light') }}</span>
                                    </div>
                                </div>
                            </label>

                            <label class="relative">
                                <input type="radio" name="theme" value="dark" class="peer sr-only">
                                <div class="p-4 rounded-lg border-2 border-border cursor-pointer transition-all peer-checked:border-primary-500 peer-checked:bg-primary-50 theme-dark:peer-checked:bg-primary-900/20 hover:bg-surface theme-dark:hover:bg-slate-800">
                                    <div class="flex items-center gap-3">
                                        <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                                        </svg>
                                        <span class="font-medium text-text-primary theme-dark:text-white">{{ __('messages.theme_dark') }}</span>
                                    </div>
                                </div>
                            </label>

                            <label class="relative">
                                <input type="radio" name="theme" value="stylis" class="peer sr-only">
                                <div class="p-4 rounded-lg border-2 border-border cursor-pointer transition-all peer-checked:border-primary-500 peer-checked:bg-primary-50 theme-dark:peer-checked:bg-primary-900/20 hover:bg-surface theme-dark:hover:bg-slate-800">
                                    <div class="flex items-center gap-3">
                                        <svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                                        </svg>
                                        <span class="font-medium text-text-primary theme-dark:text-white">{{ __('messages.theme_stylis') }}</span>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-text-primary mb-3 theme-dark:text-white">
                            Language
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <label class="relative">
                                <input type="radio" name="language" value="en" class="peer sr-only">
                                <div class="p-4 rounded-lg border-2 border-border cursor-pointer transition-all peer-checked:border-primary-500 peer-checked:bg-primary-50 theme-dark:peer-checked:bg-primary-900/20 hover:bg-surface theme-dark:hover:bg-slate-800">
                                    <div class="flex items-center gap-3">
                                        <span class="text-2xl">🇺🇸</span>
                                        <span class="font-medium text-text-primary theme-dark:text-white">English</span>
                                    </div>
                                </div>
                            </label>

                            <label class="relative">
                                <input type="radio" name="language" value="id" class="peer sr-only">
                                <div class="p-4 rounded-lg border-2 border-border cursor-pointer transition-all peer-checked:border-primary-500 peer-checked:bg-primary-50 theme-dark:peer-checked:bg-primary-900/20 hover:bg-surface theme-dark:hover:bg-slate-800">
                                    <div class="flex items-center gap-3">
                                        <span class="text-2xl">🇮🇩</span>
                                        <span class="font-medium text-text-primary theme-dark:text-white">Bahasa Indonesia</span>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <x-button type="button" variant="primary">
                        {{ __('messages.save_preferences') }}
                    </x-button>
                </div>
            </div>

            {{-- Notification Settings --}}
            <div class="p-6 space-y-6">
                <h3 class="text-lg font-semibold text-text-primary theme-dark:text-white">{{ __('messages.notification_settings') }}</h3>

                <div class="space-y-4">
                    @foreach([
                        ['label' => __('messages.email_notifications_appointments'), 'checked' => true],
                        ['label' => __('messages.email_notifications_patient_registration'), 'checked' => true],
                        ['label' => __('messages.sms_reminders_appointments'), 'checked' => false],
                        ['label' => __('messages.push_notifications_critical_alerts'), 'checked' => true],
                    ] as $i => $notif)
                    <label class="flex items-center justify-between p-4 rounded-lg border border-border hover:bg-surface transition-colors theme-dark:border-slate-700 theme-dark:hover:bg-slate-800">
                        <span class="text-text-primary theme-dark:text-white">{{ $notif['label'] }}</span>
                        <div class="relative">
                            <input type="checkbox" {{ $notif['checked'] ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-11 h-6 bg-surface peer-checked:bg-primary-500 rounded-full transition-colors theme-dark:bg-slate-700"></div>
                            <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform peer-checked:translate-x-5"></div>
                        </div>
                    </label>
                    @endforeach
                </div>

                <x-button type="button" variant="primary">
                    {{ __('messages.update_notifications') }}
                </x-button>
            </div>

            {{-- Security Settings --}}
            <div class="p-6 space-y-6">
                <h3 class="text-lg font-semibold text-text-primary theme-dark:text-white">{{ __('messages.security_settings') }}</h3>

                <x-alert type="info" :icon="false">
                    <p class="text-sm">{{ __('messages.configure_your_security_preferences') }}</p>
                </x-alert>

                <form class="space-y-5">
                    <x-input
                        type="password"
                        name="current_password"
                        :label="__('messages.current_password')"
                        :placeholder="__('messages.current_password_placeholder')"
                    />

                    <x-input
                        type="password"
                        name="new_password"
                        :label="__('messages.new_password')"
                        :placeholder="__('messages.new_password_placeholder')"
                    />

                    <x-input
                        type="password"
                        name="confirm_password"
                        :label="__('messages.confirm_password')"
                        :placeholder="__('messages.confirm_password_placeholder')"
                    />

                    <div class="flex items-center justify-between p-4 rounded-lg border border-danger-200 bg-danger-50 theme-dark:border-danger-800 theme-dark:bg-danger-900/20">
                        <div>
                            <p class="font-medium text-danger-700 theme-dark:text-danger-300">{{ __('messages.two_factor_auth') }}</p>
                            <p class="text-sm text-danger-600 theme-dark:text-danger-400">{{ __('messages.two_factor_description') }}</p>
                        </div>
                        <x-button variant="danger" size="sm">{{ __('messages.enable') }}</x-button>
                    </div>

                    <x-button type="submit" variant="primary">
                        {{ __('messages.update_security') }}
                    </x-button>
                </form>
            </div>

        </x-tabs>
    </x-card>
</div>

@endsection
