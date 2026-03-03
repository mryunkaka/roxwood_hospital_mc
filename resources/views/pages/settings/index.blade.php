{{-- Settings --}}
@extends('layouts.app')

@section('title', __('messages.settings') . ' - ' . ($appName ?? config('app.name')))

@section('page-title', __('messages.settings'))
@section('page-description', __('messages.settings_page_description'))

@section('content')

<div class="max-w-4xl">
    @php
        $canManageWebSettings = (bool) ($canManageWebSettings ?? false);
        $activeTab = request()->query('tab', 'account');
        if (!$canManageWebSettings && $activeTab === 'general') {
            $activeTab = 'account';
        }

	        $tabs = [
	            ['key' => 'account', 'label' => __('messages.account_settings')],
	            ['key' => 'appearance', 'label' => __('messages.appearance')],
	        ];

        if ($canManageWebSettings) {
            array_splice($tabs, 1, 0, [[
                'key' => 'general',
                'label' => __('messages.web_settings'),
            ]]);
        }
    @endphp

    <x-card :padding="'none'">
        <x-tabs
            :tabs="$tabs"
            :activeTab="$activeTab"
            :variant="'bordered'"
        >
            {{-- Account Settings --}}
	            <div class="p-6 space-y-6" x-show="activeTab === 'account'" x-cloak>
	                @php
	                    $u = $userRh ?? null;
	                    $docItems = [
	                        ['field' => 'sertifikat_heli', 'label' => __('messages.heli_certificate')],
	                        ['field' => 'sertifikat_operasi', 'label' => __('messages.operation_certificate')],
	                    ];
                        $academyDocsRaw = (string) ($u?->dokumen_lainnya ?? '');
                        $academyDocs = [];
                        try {
                            $decoded = json_decode($academyDocsRaw, true);
                            if (is_array($decoded) && isset($decoded['academy']) && is_array($decoded['academy'])) {
                                $academyDocs = $decoded['academy'];
                            } elseif (is_array($decoded) && array_is_list($decoded)) {
                                $academyDocs = $decoded;
                            }
                        } catch (\Throwable $e) {
                            $academyDocs = [];
                        }

                        // Ensure each doc has an id (for delete/replace mapping)
                        $tmp = [];
                        foreach ($academyDocs as $d) {
                            if (!is_array($d)) continue;
                            $id = (string) ($d['id'] ?? '');
                            $name = (string) ($d['name'] ?? '');
                            $path = (string) ($d['path'] ?? '');
                            if (trim($id) === '') continue;
                            $tmp[] = ['id' => $id, 'name' => $name, 'path' => $path];
                        }
                        $academyDocs = $tmp;
	                @endphp

                <div
                    x-data="{
                        docPreviewOpen: false,
                        docTitle: '',
                        docSrc: '',
                        zoom: 1,
                        openDoc(title, src) {
                            if (!src) return;
                            this.docTitle = title || '';
                            this.docSrc = src;
                            this.zoom = 1;
                            this.docPreviewOpen = true;
                        },
                        zoomIn() { this.zoom = Math.min(this.zoom + 0.2, 3); },
                        zoomOut() { this.zoom = Math.max(this.zoom - 0.2, 0.5); },
                        zoomReset() { this.zoom = 1; },
                        reloadDoc() {
                            if (!this.docSrc) return;
                            const base = this.docSrc.split('?')[0];
                            this.docSrc = base + '?v=' + Date.now();
                        }
                    }"
                    class="space-y-6"
                >
                    <h3 class="text-lg font-semibold text-text-primary theme-dark:text-white">{{ __('messages.account_settings') }}</h3>

                    @if(!$u)
                        <x-alert type="warning" :icon="false">
                            <p class="text-sm">{{ __('messages.account_settings_no_session') }}</p>
                        </x-alert>
                    @endif

                    <form method="POST" action="{{ route('settings.account.update') }}" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        @method('PATCH')

	                        <x-card :title="__('messages.medical_identity')" :subtitle="__('messages.medical_identity_subtitle')">
	                            <div class="space-y-5">
	                                <x-grid :cols="2">
	                                    <x-input
	                                        type="number"
	                                        name="batch"
	                                        :label="__('messages.batch')"
	                                        :required="true"
	                                        min="1"
	                                        max="26"
	                                        :value="$u?->batch"
	                                        :error="$errors->first('batch')"
	                                        :hint="__('messages.batch_hint')"
	                                    />

                                    <x-input
                                        type="date"
                                        name="tanggal_masuk"
                                        :label="__('messages.join_date')"
                                        :required="true"
                                        :value="$u?->tanggal_masuk?->format('Y-m-d')"
                                        :error="$errors->first('tanggal_masuk')"
	                                        :hint="__('messages.join_date_hint')"
	                                    />
	                                </x-grid>
	                            </div>
	                        </x-card>

                        <x-card :title="__('messages.personal_data')" :subtitle="__('messages.personal_data_subtitle')">
                            <div class="space-y-5">
                                <x-input
                                    type="text"
                                    name="full_name"
                                    :label="__('messages.full_name')"
                                    :required="true"
                                    :value="$u?->full_name"
                                    :error="$errors->first('full_name')"
                                    :hint="__('messages.full_name_hint')"
                                />

                                <x-select
                                    name="position"
                                    :label="__('messages.position')"
                                    :required="true"
                                    :value="$u?->position"
	                                    :error="$errors->first('position')"
	                                    :options="[
	                                        ['value' => 'Trainee', 'label' => __('messages.position_trainee')],
	                                        ['value' => 'Paramedic', 'label' => __('messages.position_paramedic')],
	                                        ['value' => '(Co.Ast)', 'label' => __('messages.position_co_asst')],
	                                        ['value' => 'General Doctor', 'label' => __('messages.position_doctor_umum')],
	                                        ['value' => 'Specialist Doctor', 'label' => __('messages.position_doctor_specialist')],
	                                    ]"
	                                    :placeholder="__('messages.select_position')"
	                                />

                                <x-grid :cols="2">
                                    <x-input
                                        type="text"
                                        name="citizen_id"
                                        :label="__('messages.citizen_id')"
                                        :required="true"
                                        :value="$u?->citizen_id"
                                        :error="$errors->first('citizen_id')"
                                        :hint="__('messages.citizen_id_hint')"
                                        pattern="[A-Z0-9]+"
                                        title="{{ __('messages.citizen_id_title') }}"
                                        @input="$event.target.value = ($event.target.value || '').replace(/[^A-Za-z0-9]/g,'').toUpperCase()"
                                    />

                                    <x-select
                                        name="jenis_kelamin"
                                        :label="__('messages.gender')"
                                        :required="true"
                                        :value="$u?->jenis_kelamin"
                                        :error="$errors->first('jenis_kelamin')"
                                        :options="[
                                            ['value' => 'Laki-laki', 'label' => __('messages.gender_male')],
                                            ['value' => 'Perempuan', 'label' => __('messages.gender_female')],
                                        ]"
                                        :placeholder="__('messages.select_gender')"
                                    />
                                </x-grid>

                                <x-input
                                    type="text"
                                    name="no_hp_ic"
                                    :label="__('messages.ic_phone_number')"
                                    :required="true"
                                    :value="$u?->no_hp_ic"
                                    :error="$errors->first('no_hp_ic')"
                                    :hint="__('messages.ic_phone_number_hint')"
                                    inputmode="numeric"
                                    autocomplete="tel"
                                />
                            </div>
                        </x-card>

	                        <x-card :title="__('messages.supporting_documents')" :subtitle="__('messages.supporting_documents_subtitle')">
	                            <div class="space-y-5">
	                                <x-alert type="info" :icon="false">
	                                    <p class="text-sm">{{ __('messages.validation_doc_hint') }}</p>
	                                </x-alert>

	                                <x-grid :cols="2">
	                                    @foreach($docItems as $doc)
	                                        @php
	                                            $field = $doc['field'];
	                                            $label = $doc['label'];
	                                            $path = $u?->{$field} ?? null;
                                            $url = $path ? asset($path) : null;
                                        @endphp

                                        <x-card :padding="'default'">
                                            <div class="flex items-center justify-between gap-3 mb-4">
                                                <div class="min-w-0">
                                                        <p class="text-sm font-semibold text-text-primary truncate">{{ $label }}</p>
                                                    <div class="mt-1">
                                                        @if($path)
                                                            <x-badge variant="success">{{ __('messages.uploaded') }}</x-badge>
                                                        @else
                                                            <x-badge variant="default">{{ __('messages.not_uploaded') }}</x-badge>
                                                        @endif
                                                    </div>
                                                </div>

                                                @if($url)
                                                    <x-button
                                                        type="button"
                                                        variant="link"
                                                        size="sm"
                                                        data-doc-title="{{ $label }}"
                                                        data-doc-src="{{ $url }}"
                                                        @click="openDoc($el.dataset.docTitle, $el.dataset.docSrc)"
                                                    >
                                                        {{ __('messages.view_document') }}
                                                    </x-button>
                                                @endif
                                            </div>

                                            <x-file-input
                                                :name="$field"
                                                :label="__('messages.upload_replacement')"
                                                :hint="$path ? __('messages.upload_replace_hint') : __('messages.upload_first_hint')"
                                                :error="$errors->first($field)"
                                            />
	                                        </x-card>
	                                    @endforeach
	                                </x-grid>

                                    <x-card :padding="'default'">
                                        <div class="flex items-center justify-between gap-3 mb-4">
                                            <div class="min-w-0">
                                                <p class="text-sm font-semibold text-text-primary truncate">{{ __('messages.academy_certificates') }}</p>
                                                <p class="mt-1 text-xs text-text-secondary">{{ __('messages.academy_certificates_hint') }}</p>
                                            </div>
                                        </div>

                                        <div class="space-y-4">
                                            @foreach($academyDocs as $ad)
                                                @php
                                                    $adId = (string) ($ad['id'] ?? '');
                                                    $adName = (string) ($ad['name'] ?? '');
                                                    $adPath = (string) ($ad['path'] ?? '');
                                                    $adUrl = $adPath !== '' ? asset($adPath) : null;
                                                @endphp

                                                <div class="rounded-2xl border border-border bg-surface p-4 space-y-3">
                                                    <div class="flex items-start justify-between gap-3">
                                                        <div class="min-w-0 flex-1">
                                                            <input type="hidden" name="academy_doc_id[]" value="{{ $adId }}">
                                                            <x-input
                                                                type="text"
                                                                :label="__('messages.certificate_name')"
                                                                name="academy_doc_name[]"
                                                                :value="$adName"
                                                            />
                                                        </div>

                                                        <div class="shrink-0 pt-7">
                                                            <x-checkbox
                                                                name="academy_doc_delete[]"
                                                                :value="$adId"
                                                                :label="__('messages.delete')"
                                                            />
                                                        </div>
                                                    </div>

                                                    @if($adUrl)
                                                        <x-button
                                                            type="button"
                                                            variant="link"
                                                            size="sm"
                                                            data-doc-title="{{ $adName }}"
                                                            data-doc-src="{{ $adUrl }}"
                                                            @click="openDoc($el.dataset.docTitle, $el.dataset.docSrc)"
                                                        >
                                                            {{ __('messages.view_document') }}
                                                        </x-button>
                                                    @endif

                                                    <x-file-input
                                                        name="academy_doc_file[]"
                                                        :label="__('messages.upload_replacement')"
                                                        :hint="__('messages.upload_replace_hint')"
                                                    />
                                                </div>
                                            @endforeach

                                            <div x-data="{ rows: 1, max: 8 }" class="space-y-3">
                                                @for($i = 0; $i < 8; $i++)
                                                    <div x-show="rows > {{ $i }}" x-cloak class="rounded-2xl border border-border bg-surface p-4 space-y-3">
                                                        <input type="hidden" name="academy_doc_id[]" value="">
                                                        <x-input
                                                            type="text"
                                                            :label="__('messages.certificate_name')"
                                                            :placeholder="__('messages.certificate_name_placeholder')"
                                                            name="academy_doc_name[]"
                                                        />
                                                        <x-file-input
                                                            :label="__('messages.upload_replacement')"
                                                            :hint="__('messages.upload_first_hint')"
                                                            name="academy_doc_file[]"
                                                        />
                                                    </div>
                                                @endfor

                                                <div class="flex justify-end">
                                                    <x-button type="button" variant="secondary" x-bind:disabled="rows >= max" @click="rows = Math.min(rows + 1, max)">
                                                        {{ __('messages.add_certificate') }}
                                                    </x-button>
                                                </div>
                                            </div>
                                        </div>
                                    </x-card>
	                            </div>
	                        </x-card>

                        <x-card :title="__('messages.account_security')" :subtitle="__('messages.account_security_subtitle')">
                            <div class="space-y-5">
                                <x-alert type="info" :icon="false">
                                    <p class="text-sm">{{ __('messages.pin_optional_hint') }}</p>
                                </x-alert>

                                <x-input
                                    type="password"
                                    name="old_pin"
                                    :label="__('messages.old_pin')"
                                    :error="$errors->first('old_pin')"
                                    autocomplete="current-password"
                                    inputmode="numeric"
                                    pattern="[0-9]{4}"
                                    maxlength="4"
                                    placeholder="{{ __('messages.pin_placeholder') }}"
                                />

                                <x-grid :cols="2">
                                    <x-input
                                        type="password"
                                        name="new_pin"
                                        :label="__('messages.manage_users_new_pin')"
                                        :error="$errors->first('new_pin')"
                                        autocomplete="new-password"
                                        inputmode="numeric"
                                        pattern="[0-9]{4}"
                                        maxlength="4"
                                        placeholder="{{ __('messages.pin_placeholder') }}"
                                    />

                                    <x-input
                                        type="password"
                                        name="confirm_pin"
                                        :label="__('messages.confirm_new_pin')"
                                        :error="$errors->first('confirm_pin')"
                                        autocomplete="new-password"
                                        inputmode="numeric"
                                        pattern="[0-9]{4}"
                                        maxlength="4"
                                        placeholder="{{ __('messages.pin_placeholder') }}"
                                    />
                                </x-grid>
                            </div>
                        </x-card>

                        <div class="flex items-center justify-end">
                            <x-button type="submit" variant="primary">
                                {{ __('messages.save_changes') }}
                            </x-button>
                        </div>
                    </form>

                    <x-modal x-model="docPreviewOpen" :title="__('messages.document_preview')" size="xl">
                        <div class="flex items-start justify-between gap-3 mb-4">
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-text-primary truncate" x-text="docTitle"></p>
                                <p class="text-xs text-text-secondary">{{ __('messages.validation_zoom_hint') }}</p>
                            </div>

                            <div class="flex items-center gap-2 shrink-0">
                                <x-button type="button" variant="secondary" size="xs" @click="zoomOut()">
                                    {{ __('messages.zoom_out') }}
                                </x-button>
                                <x-button type="button" variant="secondary" size="xs" @click="zoomIn()">
                                    {{ __('messages.zoom_in') }}
                                </x-button>
                                <x-button type="button" variant="secondary" size="xs" @click="zoomReset()">
                                    {{ __('messages.zoom_reset') }}
                                </x-button>
                                <x-button type="button" variant="secondary" size="xs" @click="reloadDoc()">
                                    {{ __('messages.reload') }}
                                </x-button>
                            </div>
                        </div>

                        <div class="rounded-2xl border border-border bg-surface-alt p-4 flex items-center justify-center min-h-[55vh] overflow-auto">
                            <img
                                :src="docSrc"
                                :alt="docTitle"
                                class="max-h-[70vh] object-contain transition-transform duration-200"
                                :style="`transform: scale(${zoom})`"
                            >
                        </div>
                    </x-modal>
                </div>
            </div>

            {{-- Web Settings --}}
            <div class="p-6 space-y-6" x-show="activeTab === 'general'" x-cloak>
                <h3 class="text-lg font-semibold text-text-primary theme-dark:text-white">{{ __('messages.web_settings') }}</h3>

                <x-alert type="info" :icon="false">
                    <p class="text-sm">{{ __('messages.web_settings_hint') }}</p>
                </x-alert>

		                <form method="POST" action="{{ route('settings.web.update') }}" enctype="multipart/form-data" class="space-y-5">
	                    @csrf
	                    @method('PATCH')

                        @php
                            $rawAppName = (string) ($appSetting?->app_name ?? ($appName ?? config('app.name')));
                            $appNameParts = array_map('trim', explode('|', $rawAppName, 2));
                            $projectNameValue = $appNameParts[0] ?? ($appName ?? config('app.name'));
                            $projectTaglineValue = $appNameParts[1] ?? ($appTagline ?? 'Health Medical');
                        @endphp

	                    <x-input
	                        type="text"
	                        name="app_name"
	                        :label="__('messages.project_name')"
	                        :required="true"
	                        :value="$projectNameValue"
	                        :error="$errors->first('app_name')"
	                        :hint="__('messages.project_name_hint')"
	                        @input="window.dispatchEvent(new CustomEvent('app-settings-preview', { detail: { name: $event.target.value } }))"
	                    />

                        <x-input
                            type="text"
                            name="app_tagline"
                            :label="__('messages.project_tagline')"
                            :value="$projectTaglineValue"
                            :error="$errors->first('app_tagline')"
                            :hint="__('messages.project_tagline_hint')"
                            @input="window.dispatchEvent(new CustomEvent('app-settings-preview', { detail: { tagline: $event.target.value } }))"
                        />

	                    <x-file-input
	                        name="app_logo"
	                        :label="__('messages.project_logo')"
	                        accept="image/png,image/jpeg,image/svg+xml"
                        :error="$errors->first('app_logo')"
                        :hint="__('messages.project_logo_hint')"
                    />

                    <x-select
                        name="timezone"
                        :label="__('messages.timezone')"
                        :placeholder="__('messages.select_timezone')"
                        :value="$appSetting?->timezone"
                        :error="$errors->first('timezone')"
                        :options="[
                            ['value' => 'Asia/Jakarta', 'label' => __('messages.tz_asia_jakarta')],
                            ['value' => 'Asia/Singapore', 'label' => __('messages.tz_asia_singapore')],
                            ['value' => 'Pacific/Auckland', 'label' => __('messages.tz_pacific_auckland')],
                        ]"
                    />

                    <div class="flex items-center justify-end">
                        <x-button type="submit" variant="primary">
                            {{ __('messages.save_changes') }}
                        </x-button>
                    </div>
	                </form>
	            </div>

            {{-- Appearance Settings --}}
            <div class="p-6 space-y-6" x-show="activeTab === 'appearance'" x-cloak>
                <h3 class="text-lg font-semibold text-text-primary theme-dark:text-white">{{ __('messages.appearance_settings') }}</h3>

                <div
                    class="space-y-5"
                    x-data="{
                        themeChoice: 'light',
                        langChoice: 'id',
                        saving: false,
                        init() {
                            const html = document.documentElement;

                            const storedTheme = localStorage.getItem('roxwood-theme');
                            if (storedTheme) {
                                this.themeChoice = storedTheme;
                            } else if (html.classList.contains('theme-stylis')) {
                                this.themeChoice = 'stylis';
                            } else if (html.classList.contains('theme-dark')) {
                                this.themeChoice = 'dark';
                            } else {
                                this.themeChoice = 'light';
                            }

                            const storedLang = localStorage.getItem('app_locale');
                            const metaLang = document.querySelector('meta[name=locale]')?.content;
                            this.langChoice = storedLang || metaLang || 'id';
                        },
	                        async save() {
	                            if (this.saving) return;
	                            this.saving = true;

	                            try {
	                                // Save theme preference (applied on reload via themeController init)
	                                localStorage.setItem('roxwood-theme', this.themeChoice);

	                                // Save language preference to session + localStorage
	                                localStorage.setItem('app_locale', this.langChoice);

	                                const csrf = document.querySelector('meta[name=csrf-token]')?.content;
	                                if (csrf) {
	                                    const res = await fetch('/lang/' + this.langChoice, {
	                                        method: 'POST',
	                                        headers: {
	                                            'Content-Type': 'application/json',
	                                            'Accept': 'application/json',
	                                            'X-Requested-With': 'XMLHttpRequest',
	                                            'X-CSRF-TOKEN': csrf,
	                                        },
	                                        body: JSON.stringify({ lang: this.langChoice }),
	                                    });
	                                    if (!res.ok) throw new Error('Failed to update language');
	                                }

	                                try {
	                                    window.sessionStorage?.setItem('roxwood_pending_toasts', JSON.stringify([
	                                        { type: 'success', message: @js(__('messages.preferences_saved')) }
	                                    ]));
	                                } catch {}

	                                window.location.reload();
	                            } catch (e) {
	                                const msg = @js(__('messages.failed_save_preferences'));
	                                if (window.$toast?.error) window.$toast.error(msg);
	                                else alert(msg);
	                            } finally {
	                                this.saving = false;
	                            }
	                        }
	                    }"
                    x-init="init()"
                >
	                    <x-card :padding="'default'">
	                        <div class="space-y-4">
	                            <p class="text-sm font-medium text-text-primary theme-dark:text-white">{{ __('messages.theme_preference') }}</p>
	                            <x-grid :cols="3">
	                                <x-radio
	                                    name="theme_pref"
	                                    value="light"
	                                    :label="__('messages.theme_light')"
	                                    x-model="themeChoice"
                                        @change="window.dispatchEvent(new CustomEvent('set-theme', { detail: { theme: themeChoice } }))"
	                                />
	                                <x-radio
	                                    name="theme_pref"
	                                    value="dark"
	                                    :label="__('messages.theme_dark')"
	                                    x-model="themeChoice"
                                        @change="window.dispatchEvent(new CustomEvent('set-theme', { detail: { theme: themeChoice } }))"
	                                />
	                                <x-radio
	                                    name="theme_pref"
	                                    value="stylis"
	                                    :label="__('messages.theme_stylis')"
	                                    x-model="themeChoice"
                                        @change="window.dispatchEvent(new CustomEvent('set-theme', { detail: { theme: themeChoice } }))"
	                                />
	                            </x-grid>
	                            <p class="text-xs text-text-secondary">
	                                {{ __('messages.appearance_changes_applied') }}
	                            </p>
	                        </div>
	                    </x-card>

                    <x-card :padding="'default'">
                        <div class="space-y-4">
                            <p class="text-sm font-medium text-text-primary theme-dark:text-white">{{ __('messages.language') }}</p>
	                            <x-grid :cols="2">
	                                <x-radio
	                                    name="language_pref"
	                                    value="en"
	                                    :label="__('messages.english')"
	                                    x-model="langChoice"
                                        @change="window.switchLanguage?.(langChoice)"
	                                />
	                                <x-radio
	                                    name="language_pref"
	                                    value="id"
	                                    :label="__('messages.indonesian')"
	                                    x-model="langChoice"
                                        @change="window.switchLanguage?.(langChoice)"
	                                />
	                            </x-grid>
                            <p class="text-xs text-text-secondary">
                                {{ __('messages.appearance_changes_applied') }}
                            </p>
                        </div>
                    </x-card>

                    <div class="flex items-center justify-between gap-3">
                        <p class="text-xs text-text-secondary">
                            <span class="font-medium text-text-primary theme-dark:text-white">{{ __('messages.current') ?? 'Current' }}:</span>
                            <span x-text="(themeChoice || '-') + ' · ' + (langChoice || '-')"></span>
                        </p>
                        <x-button type="button" variant="primary" x-bind:disabled="saving" @click="save()">
                            <span x-show="!saving">{{ __('messages.save_preferences') }}</span>
                            <span x-show="saving" x-cloak>{{ __('messages.loading') }}</span>
                        </x-button>
                    </div>
                </div>
            </div>

	            {{-- Notifications & Security removed (handled in Account Settings) --}}
	        </x-tabs>
    </x-card>
</div>

@endsection
