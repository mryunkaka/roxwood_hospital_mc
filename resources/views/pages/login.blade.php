@extends('layouts.guest')

@section('title', __('messages.login'))

@section('content')
<x-login-card :padding="'lg'">
    {{-- Page Header with Logo --}}
    <x-page-header
        :title="__('messages.login')"
        :subtitle="__('messages.login_subtitle')"
        :dataTranslateTitle="'login'"
        :dataTranslateSubtitle="'login_subtitle'"
    />

    {{-- Login Form --}}
    <form method="POST" action="{{ route('login.post') }}" class="space-y-5">
        @csrf

        {{-- Full Name Input --}}
        <x-input
            type="text"
            name="full_name"
            :label="__('messages.full_name')"
            :placeholder="__('messages.full_name_placeholder')"
            :dataTranslateLabel="'full_name'"
            :dataTranslatePlaceholder="'full_name_placeholder'"
            :required="true"
            autocomplete="username"
            autocorrect="off"
            autocapitalize="words"
        />

        {{-- PIN Input --}}
        <x-input
            type="password"
            name="pin"
            :label="__('messages.pin')"
            :placeholder="__('messages.pin_placeholder')"
            :dataTranslateLabel="'pin'"
            :dataTranslatePlaceholder="'pin_placeholder'"
            :required="true"
            maxlength="4"
            pattern="[0-9]{4}"
            inputmode="numeric"
            autocomplete="current-password"
            :hint="__('messages.pin_hint')"
            :dataTranslateHint="'pin_hint'"
        />

        {{-- Remember Me & Forgot Password --}}
        <div class="flex items-center justify-between">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="remember" class="w-4 h-4 rounded border-border text-primary-500 focus:ring-primary-500 focus:ring-offset-0">
                <span class="text-sm text-text-secondary" data-translate="remember_me">
                    {{ __('messages.remember_me') }}
                </span>
            </label>
            <a href="#" class="text-sm text-primary-500 hover:text-primary-600" data-translate="forgot_password">
                {{ __('messages.forgot_password') }}
            </a>
        </div>

        {{-- Submit Button --}}
        <x-button type="submit" variant="primary" size="lg" :fullWidth="true">
            <span data-translate="login">{{ __('messages.login') }}</span>
        </x-button>
    </form>

    {{-- Demo Note --}}
    <div x-data="langController()" class="mt-6">
        <x-alert type="info" :icon="false">
            <p class="text-sm">
                <strong x-text="translations.demo_mode || 'Demo Mode'"></strong>: <span x-text="translations.demo_mode_description_login || 'Enter any full name and 4-digit PIN to access the dashboard.'"></span>
            </p>
        </x-alert>
    </div>

    {{-- Register Link --}}
    <p class="mt-6 text-center text-sm text-text-secondary">
        <span data-translate="dont_have_account">{{ __('messages.dont_have_account') }}</span>
        <a href="{{ route('register') }}" class="text-primary-500 hover:text-primary-600 font-medium" data-translate="register">
            {{ __('messages.register') }}
        </a>
    </p>
</x-login-card>
@endsection
