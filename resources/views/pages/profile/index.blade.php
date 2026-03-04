{{-- Profile --}}
@extends('layouts.app')

@section('title', __('messages.profile') . ' - ' . ($appName ?? config('app.name')))

@section('page-title', __('messages.profile'))
@section('page-description', __('messages.profile_page_description'))

@section('content')
    @php
        /** @var \App\Models\UserRh|null $u */
        $u = $userRh ?? null;
        $ui = $profileUi ?? null;

        $joinId = $ui['join_date_id'] ?? null;
        $joinEn = $ui['join_date_en'] ?? null;
        $tenureId = $ui['tenure_id'] ?? null;
        $tenureEn = $ui['tenure_en'] ?? null;

        $statusVariant = 'default';
        $statusLabel = null;
        if ($u?->resigned_at) {
            $statusVariant = 'danger';
            $statusLabel = __('messages.validation_status_resign');
        } elseif ($u?->is_active) {
            $statusVariant = 'success';
            $statusLabel = __('messages.validation_status_active');
        } else {
            $statusVariant = 'warning';
            $statusLabel = __('messages.validation_status_pending');
        }

        $verifyVariant = ($u?->is_verified) ? 'success' : 'warning';
        $verifyLabel = ($u?->is_verified) ? __('messages.profile_verified_badge') : __('messages.profile_unverified_badge');
    @endphp

    <div class="space-y-6">
        @if(!$u)
            <x-alert type="warning" :icon="false">
                <p class="text-sm">{{ __('messages.account_settings_no_session') }}</p>
            </x-alert>
        @endif

        <x-card :padding="'none'">
            <div class="p-5 sm:p-6 border-b border-border flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-4 min-w-0">
                    <x-avatar
                        :src="$ui['photo_url'] ?? null"
                        :name="$u?->full_name"
                        :alt="__('messages.profile_photo')"
                        size="2xl"
                        rounded="lg"
                    />

                    <div class="min-w-0">
                        <h2 class="text-xl font-semibold text-text-primary break-words">
                            {{ $u?->full_name ?? '-' }}
                        </h2>

                        <div class="mt-2 flex flex-wrap items-center gap-2">
                            @if($statusLabel)
                                <x-badge :variant="$statusVariant">
                                    <span class="font-semibold">{{ $statusLabel }}</span>
                                </x-badge>
                            @endif

                            <x-badge :variant="$verifyVariant">
                                <span class="font-semibold">{{ $verifyLabel }}</span>
                            </x-badge>
                        </div>
                    </div>
                </div>

                @if(!empty($u?->citizen_id))
                    <div class="text-sm text-text-secondary">
                        <span class="font-medium text-text-primary" data-translate="citizen_id">{{ __('messages.citizen_id') }}</span>
                        <span class="mx-1">:</span>
                        <span class="font-semibold text-text-secondary">{{ $u?->citizen_id }}</span>
                    </div>
                @endif
            </div>

            <div class="p-5 sm:p-6">
                <x-grid :cols="3" :gap="'sm'">
                    <x-card :padding="'default'" :shadow="'none'" class="bg-surface-alt border border-border">
                        <div class="flex items-start justify-between gap-3 text-sm">
                            <span class="text-text-secondary" data-translate="role">{{ __('messages.role') }}</span>
                            <span class="font-semibold text-text-primary text-right break-words">{{ $u?->role ?? '-' }}</span>
                        </div>
                    </x-card>

                    <x-card :padding="'default'" :shadow="'none'" class="bg-surface-alt border border-border">
                        <div class="flex items-start justify-between gap-3 text-sm">
                            <span class="text-text-secondary" data-translate="position">{{ __('messages.position') }}</span>
                            <span class="font-semibold text-text-primary text-right break-words">{{ $u?->position ?? '-' }}</span>
                        </div>
                    </x-card>

                    <x-card :padding="'default'" :shadow="'none'" class="bg-surface-alt border border-border">
                        <div class="flex items-start justify-between gap-3 text-sm">
                            <span class="text-text-secondary" data-translate="batch">{{ __('messages.batch') }}</span>
                            <span class="font-semibold text-text-primary text-right break-words">{{ $u?->batch ?? '-' }}</span>
                        </div>
                    </x-card>
                </x-grid>
            </div>
        </x-card>

        <x-grid :cols="2">
            <x-card :title="__('messages.profile_personal_info')">
                <div class="space-y-4">
                    <x-input
                        name="profile_full_name"
                        :label="__('messages.full_name')"
                        dataTranslateLabel="full_name"
                        :value="$u?->full_name"
                        readonly
                    />

                    <x-input
                        name="profile_citizen_id"
                        :label="__('messages.citizen_id')"
                        dataTranslateLabel="citizen_id"
                        :value="$u?->citizen_id"
                        readonly
                    />

                    <x-input
                        name="profile_gender"
                        :label="__('messages.gender')"
                        dataTranslateLabel="gender"
                        :value="$u?->jenis_kelamin"
                        readonly
                    />

                    <x-input
                        name="profile_phone"
                        :label="__('messages.ic_phone_number')"
                        dataTranslateLabel="ic_phone_number"
                        :value="$u?->no_hp_ic"
                        readonly
                    />
                </div>
            </x-card>

            <x-card :title="__('messages.profile_work_info')">
                <div class="space-y-4">
                    <div
                        class="space-y-2"
                        x-data="{
                            joinId: @js($joinId),
                            joinEn: @js($joinEn),
                            tenureId: @js($tenureId),
                            tenureEn: @js($tenureEn),
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
                        <div class="flex items-start justify-between gap-3 text-sm">
                            <span class="text-text-secondary" data-translate="join_date">{{ __('messages.join_date') }}</span>
                            <span class="font-semibold text-text-primary text-right break-words" x-text="joinDate || '-'"></span>
                        </div>
                        <div class="flex items-start justify-between gap-3 text-sm">
                            <span class="text-text-secondary" data-translate="validation_tenure_label">{{ __('messages.validation_tenure_label') }}</span>
                            <span class="font-semibold text-text-primary text-right break-words" x-text="tenure || '-'"></span>
                        </div>
                    </div>

                    <div class="pt-1">
                        <x-alert type="info" :icon="false">
                            <p class="text-sm" data-translate="join_date_hint">{{ __('messages.join_date_hint') }}</p>
                        </x-alert>
                    </div>
                </div>
            </x-card>
        </x-grid>
    </div>
@endsection
