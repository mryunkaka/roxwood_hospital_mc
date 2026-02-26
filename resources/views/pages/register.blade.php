@extends('layouts.guest')

@section('title', __('messages.register'))

@section('content')
<x-login-card :padding="'lg'">
    {{-- Page Header with Logo --}}
    <x-page-header
        :title="__('messages.register')"
        :subtitle="__('messages.register_subtitle')"
        :dataTranslateTitle="'register'"
        :dataTranslateSubtitle="'register_subtitle'"
    />

    {{-- Register Form --}}
    <form method="POST" action="{{ route('register.post') }}" class="space-y-6" enctype="multipart/form-data">
        @csrf

        {{-- Account Information Section --}}
        <div class="space-y-4">
            <h3 class="text-sm font-semibold text-text-primary border-b border-border pb-2" data-translate="account_info">
                {{ __('messages.account_info') }}
            </h3>

            {{-- Full Name --}}
            <x-input
                type="text"
                name="full_name"
                :label="__('messages.full_name')"
                :dataTranslateLabel="'full_name'"
                :dataTranslatePlaceholder="'full_name_placeholder'"
                placeholder="{{ __('messages.full_name_placeholder') }}"
                :required="true"
                autocomplete="name"
                autocorrect="off"
                autocapitalize="words"
            />

            {{-- 4 Digit PIN --}}
            <x-input
                type="password"
                name="pin"
                :label="__('messages.pin')"
                :dataTranslateLabel="'pin'"
                :dataTranslatePlaceholder="'pin_placeholder'"
                placeholder="{{ __('messages.pin_placeholder') }}"
                :required="true"
                maxlength="4"
                pattern="[0-9]{4}"
                inputmode="numeric"
                autocomplete="new-password"
                :hint="__('messages.pin_hint')"
                :dataTranslateHint="'pin_hint'"
            />

            {{-- Batch --}}
            <x-input
                type="number"
                name="batch"
                :label="__('messages.batch')"
                :dataTranslateLabel="'batch'"
                :dataTranslatePlaceholder="'batch_placeholder'"
                placeholder="{{ __('messages.batch_placeholder') }}"
                :required="true"
                min="1"
                max="26"
            />
        </div>

        {{-- Personal Information Section --}}
        <div class="space-y-4">
            <h3 class="text-sm font-semibold text-text-primary border-b border-border pb-2" data-translate="personal_info">
                {{ __('messages.personal_info') }}
            </h3>

            {{-- Citizen ID --}}
            <x-input
                type="text"
                name="citizen_id"
                :label="__('messages.citizen_id')"
                :dataTranslateLabel="'citizen_id'"
                :dataTranslatePlaceholder="'citizen_id_placeholder'"
                placeholder="{{ __('messages.citizen_id_placeholder') }}"
                :required="true"
            />

            {{-- Phone Number --}}
            <x-input
                type="tel"
                name="no_hp_ic"
                :label="__('messages.phone_number')"
                :dataTranslateLabel="'phone_number'"
                :dataTranslatePlaceholder="'phone_number_placeholder'"
                placeholder="{{ __('messages.phone_number_placeholder') }}"
                :required="true"
            />

            {{-- Gender --}}
            <x-select
                name="jenis_kelamin"
                :label="__('messages.gender')"
                :dataTranslateLabel="'gender'"
                :options="[
                    ['value' => '', 'label' => __('messages.select_gender')],
                    ['value' => 'Laki-laki', 'label' => 'Laki-laki'],
                    ['value' => 'Perempuan', 'label' => 'Perempuan']
                ]"
                :required="true"
            />
        </div>

        {{-- Documents Section --}}
        <div class="space-y-4">
            <h3 class="text-sm font-semibold text-text-primary border-b border-border pb-2" data-translate="identity_documents">
                {{ __('messages.identity_documents') }}
            </h3>

            {{-- KTP File --}}
            <x-file-input
                name="file_ktp"
                :label="__('messages.ktp_file')"
                :dataTranslateLabel="'ktp_file'"
                accept="image/png,image/jpeg"
                :required="true"
                :hint="__('messages.file_formats_hint')"
            />

            {{-- SKB File --}}
            <x-file-input
                name="file_skb"
                :label="__('messages.skb_file')"
                :dataTranslateLabel="'skb_file'"
                accept="image/png,image/jpeg"
                :required="true"
                :hint="__('messages.file_formats_hint')"
            />

            {{-- SIM File (Optional) --}}
            <x-file-input
                name="file_sim"
                :label="__('messages.sim_file') . ' (' . __('messages.optional') . ')'"
                :dataTranslateLabel="'sim_file'"
                accept="image/png,image/jpeg"
                :required="false"
                :hint="__('messages.file_formats_hint')"
            />
        </div>

        {{-- Role Selection --}}
        <x-select
            name="role"
            :label="__('messages.role')"
            :dataTranslateLabel="'role'"
            :options="[
                ['value' => 'Staff', 'label' => 'Staff'],
                ['value' => 'Staff Manager', 'label' => 'Staff Manager'],
                ['value' => 'Manager', 'label' => 'Manager'],
                ['value' => 'Vice Director', 'label' => 'Vice Director'],
                ['value' => 'Director', 'label' => 'Director']
            ]"
            :required="true"
        />

        {{-- Terms and Conditions --}}
        <div class="flex items-start gap-2">
            <input
                type="checkbox"
                name="terms"
                required
                class="mt-1 w-4 h-4 rounded border-border text-primary-500 focus:ring-primary-500 focus:ring-offset-0"
            >
            <label class="text-sm text-text-secondary cursor-pointer">
                <span data-translate="agree_terms">{{ __('messages.agree_terms') }}</span>
                <a href="#" class="text-primary-500 hover:text-primary-600" data-translate="terms_of_service">
                    {{ __('messages.terms_of_service') }}
                </a>
            </label>
        </div>

        {{-- Submit Button --}}
        <x-button type="submit" variant="primary" size="lg" :fullWidth="true">
            <span data-translate="register">{{ __('messages.register') }}</span>
        </x-button>
    </form>

    {{-- Login Link --}}
    <p class="mt-6 text-center text-sm text-text-secondary">
        <span data-translate="already_have_account">{{ __('messages.already_have_account') }}</span>
        <a href="{{ route('login') }}" class="text-primary-500 hover:text-primary-600 font-medium" data-translate="login">
            {{ __('messages.login') }}
        </a>
    </p>
</x-login-card>
@endsection
