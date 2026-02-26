@extends('layouts.app')

@section('title', __('messages.components') . ' - ' . __('messages.app_name'))

@section('page-title', __('messages.components'))
@section('page-description', __('messages.component_library'))

@section('content')

 {{-- Buttons Section --}}
 <x-section>
    <h2 class="text-xl font-bold text-text-primary mb-4 theme-dark:text-white" data-translate="buttons">{{ __('messages.buttons') }}</h2>
    <x-card :title="__('messages.button_variants')">
        <div class="flex flex-wrap items-center gap-3">
            <x-button variant="primary"><span data-translate="primary">{{ __('messages.primary') }}</span></x-button>
            <x-button variant="secondary"><span data-translate="secondary">{{ __('messages.secondary') }}</span></x-button>
            <x-button variant="success"><span data-translate="success">{{ __('messages.success') }}</span></x-button>
            <x-button variant="danger"><span data-translate="danger">{{ __('messages.danger') }}</span></x-button>
            <x-button variant="warning"><span data-translate="warning">{{ __('messages.warning') }}</span></x-button>
            <x-button variant="info"><span data-translate="info">{{ __('messages.info') }}</span></x-button>
            <x-button variant="ghost"><span data-translate="ghost">{{ __('messages.ghost') }}</span></x-button>
            <x-button variant="link"><span data-translate="link">{{ __('messages.link') }}</span></x-button>
        </div>
    </x-card>

    <x-card :title="__('messages.button_sizes')" class="mt-4">
        <div class="flex flex-wrap items-center gap-3">
            <x-button variant="primary" size="sm"><span data-translate="small">{{ __('messages.small') }}</span></x-button>
            <x-button variant="primary" size="default"><span data-translate="default">{{ __('messages.default') }}</span></x-button>
            <x-button variant="primary" size="lg"><span data-translate="large">{{ __('messages.large') }}</span></x-button>
        </div>
    </x-card>

    <x-card :title="__('messages.button_with_icons')" class="mt-4">
        <div class="flex flex-wrap items-center gap-3">
            <x-button variant="primary" :icon="'<svg class=\'w-5 h-5\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M12 4v16m8-8H4\'/></svg>'">
                <span data-translate="add_new">{{ __('messages.add_new') }}</span>
            </x-button>
            <x-button variant="danger" :icon="'<svg class=\'w-5 h-5\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16\'/></svg>'">
                <span data-translate="delete">{{ __('messages.delete') }}</span>
            </x-button>
            <x-button variant="success" iconPosition="right" :icon="'<svg class=\'w-5 h-5\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M14 5l7 7m0 0l-7 7m7-7H3\'/></svg>'">
                <span data-translate="next">{{ __('messages.next') }}</span>
            </x-button>
        </div>
    </x-card>
</x-section>

 {{-- Alerts Section --}}
 <x-section class="mt-6">
    <h2 class="text-xl font-bold text-text-primary mb-4 theme-dark:text-white" data-translate="alerts">{{ __('messages.alerts') }}</h2>

    <x-alert type="success" :dismissible="true">
        <strong data-translate="success">{{ __('messages.success') }}</strong> <span data-translate="success_message">{{ __('messages.success_message') }}</span>
    </x-alert>

    <x-alert type="danger" class="mt-3">
        <strong data-translate="error">{{ __('messages.error') }}</strong> <span data-translate="error_message">{{ __('messages.error_message') }}</span>
    </x-alert>

    <x-alert type="warning" class="mt-3">
        <strong data-translate="warning">{{ __('messages.warning') }}</strong> <span data-translate="warning_message">{{ __('messages.warning_message') }}</span>
    </x-alert>

    <x-alert type="info" class="mt-3">
        <strong data-translate="info">{{ __('messages.info') }}:</strong> <span data-translate="info_message">{{ __('messages.info_message') }}</span>
    </x-alert>
</x-section>

 {{-- Badges Section --}}
 <x-section class="mt-6">
    <h2 class="text-xl font-bold text-text-primary mb-4 theme-dark:text-white" data-translate="badges">{{ __('messages.badges') }}</h2>
    <x-card>
        <div class="flex flex-wrap items-center gap-3">
            <x-badge variant="default"><span data-translate="default">{{ __('messages.default') }}</span></x-badge>
            <x-badge variant="primary"><span data-translate="primary">{{ __('messages.primary') }}</span></x-badge>
            <x-badge variant="success"><span data-translate="success">{{ __('messages.success') }}</span></x-badge>
            <x-badge variant="danger"><span data-translate="danger">{{ __('messages.danger') }}</span></x-badge>
            <x-badge variant="warning"><span data-translate="warning">{{ __('messages.warning') }}</span></x-badge>
            <x-badge variant="info"><span data-translate="info">{{ __('messages.info') }}</span></x-badge>
        </div>

        <div class="flex flex-wrap items-center gap-3 mt-4">
            <x-badge variant="success" :dot="true"><span data-translate="online">{{ __('messages.online') }}</span></x-badge>
            <x-badge variant="danger" :dot="true"><span data-translate="busy">{{ __('messages.busy') }}</span></x-badge>
            <x-badge variant="warning" :dot="true"><span data-translate="away">{{ __('messages.away') }}</span></x-badge>
        </div>
    </x-card>
</x-section>

 {{-- Toast Notifications Section --}}
 <x-section class="mt-6">
    <h2 class="text-xl font-bold text-text-primary mb-4 theme-dark:text-white">Toast Notifications</h2>
    <x-card title="Toast Demos">
        <div class="flex flex-wrap items-center gap-3 mb-4">
            <x-button variant="success" @click="$toast.success('Operation completed successfully!')">
                Success Toast
            </x-button>
            <x-button variant="danger" @click="$toast.error('Something went wrong!')">
                Error Toast
            </x-button>
            <x-button variant="warning" @click="$toast.warning('Please review your input.')">
                Warning Toast
            </x-button>
            <x-button variant="info" @click="$toast.info('New features available!')">
                Info Toast
            </x-button>
        </div>

        <div class="flex flex-wrap items-center gap-3 mb-4">
            <x-button variant="secondary" @click="$toast.show('Custom message', 'success', { title: 'With Title!' })">
                With Title
            </x-button>
            <x-button variant="secondary" @click="$toast.show('This toast will not auto-dismiss', 'info', { persistent: true })">
                Persistent
            </x-button>
            <x-button variant="secondary" @click="$toast.show('This toast will disappear in 2 seconds', 'info', { duration: 2000 })">
                2 Seconds
            </x-button>
        </div>

        <div class="flex flex-wrap items-center gap-3 mb-4">
            <x-button variant="ghost" @click="$toast.success('Data saved successfully!', {
                actions: [
                    { label: 'Undo', variant: 'default' },
                    { label: 'View', variant: 'primary' }
                ]
            })">
                With Actions
            </x-button>
            <x-button variant="ghost" @click="$toast.clear()">
                Clear All
            </x-button>
        </div>

        <div class="border-t border-border pt-4 mt-4">
            <p class="text-sm text-text-secondary mb-2">Toast Position:</p>
            <div class="flex flex-wrap items-center gap-2">
                <button @click="$toast.position = 'top-right'" class="px-3 py-1.5 text-sm rounded-lg border border-border hover:bg-surface-hover transition-colors" :class="$toast.position === 'top-right' ? 'bg-primary-50 border-primary text-primary' : ''">Top Right</button>
                <button @click="$toast.position = 'top-center'" class="px-3 py-1.5 text-sm rounded-lg border border-border hover:bg-surface-hover transition-colors" :class="$toast.position === 'top-center' ? 'bg-primary-50 border-primary text-primary' : ''">Top Center</button>
                <button @click="$toast.position = 'top-left'" class="px-3 py-1.5 text-sm rounded-lg border border-border hover:bg-surface-hover transition-colors" :class="$toast.position === 'top-left' ? 'bg-primary-50 border-primary text-primary' : ''">Top Left</button>
                <button @click="$toast.position = 'bottom-right'" class="px-3 py-1.5 text-sm rounded-lg border border-border hover:bg-surface-hover transition-colors" :class="$toast.position === 'bottom-right' ? 'bg-primary-50 border-primary text-primary' : ''">Bottom Right</button>
                <button @click="$toast.position = 'bottom-center'" class="px-3 py-1.5 text-sm rounded-lg border border-border hover:bg-surface-hover transition-colors" :class="$toast.position === 'bottom-center' ? 'bg-primary-50 border-primary text-primary' : ''">Bottom Center</button>
                <button @click="$toast.position = 'bottom-left'" class="px-3 py-1.5 text-sm rounded-lg border border-border hover:bg-surface-hover transition-colors" :class="$toast.position === 'bottom-left' ? 'bg-primary-50 border-primary text-primary' : ''">Bottom Left</button>
            </div>
        </div>
    </x-card>
</x-section>

 {{-- Forms Section --}}
 <x-section class="mt-6">
    <h2 class="text-xl font-bold text-text-primary mb-4 theme-dark:text-white" data-translate="form_elements">{{ __('messages.form_elements') }}</h2>
    <x-grid :cols="1" :gap="'default'" :cols-sm="2">
        <x-card :title="__('messages.input_fields')">
            <form class="space-y-4">
                <x-input
                    type="text"
                    name="name"
                    :label="__('messages.full_name')"
                    placeholder="{{ __('messages.name_placeholder') }}"
                    :required="true"
                />

                <x-input
                    type="email"
                    name="email"
                    :label="__('messages.email_address')"
                    placeholder="{{ __('messages.email_address_placeholder') }}"
                />

                <x-input
                    type="password"
                    name="password"
                    :label="__('messages.password')"
                    placeholder="{{ __('messages.password_placeholder') }}"
                />

                <x-input
                    type="text"
                    name="disabled"
                    :label="__('messages.disabled_input')"
                    placeholder="{{ __('messages.disabled_placeholder') }}"
                    :disabled="true"
                />
            </form>
        </x-card>

        <x-card :title="__('messages.select_textarea')">
            <form class="space-y-4">
                <x-select
                    name="department"
                    :label="__('messages.department')"
                    :options="[
                        ['value' => 'cardio', 'label' => __('messages.cardiology')],
                        ['value' => 'neuro', 'label' => __('messages.neurology')],
                        ['value' => 'ortho', 'label' => __('messages.orthopedics')],
                        ['value' => 'pedia', 'label' => __('messages.pediatrics')],
                    ]"
                    placeholder="{{ __('messages.select_department') }}"
                />

                <div>
                    <label class="block text-sm font-medium text-text-primary mb-1.5">
                        <span data-translate="message">{{ __('messages.message') }}</span>
                    </label>
                    <textarea
                        rows="4"
                        placeholder="{{ __('messages.enter_message') }}"
                        class="w-full rounded-lg border border-border px-4 py-2.5 text-sm outline-none transition-all duration-200 bg-surface text-text-primary placeholder:text-text-hint focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20"
                    ></textarea>
                </div>

                <div class="flex items-center gap-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" class="w-4 h-4 rounded border-border text-primary-500 focus:ring-primary-500 theme-dark:bg-slate-800 theme-dark:border-slate-700">
                        <span class="text-sm text-text-secondary theme-dark:text-slate-400" data-translate="remember_me_checkbox">{{ __('messages.remember_me_checkbox') }}</span>
                    </label>
                </div>
            </form>
        </x-card>
    </x-grid>
</x-section>

 {{-- Avatars Section --}}
 <x-section class="mt-6">
    <h2 class="text-xl font-bold text-text-primary mb-4 theme-dark:text-white" data-translate="avatars">{{ __('messages.avatars') }}</h2>
    <x-card>
        <div class="flex flex-wrap items-center gap-4">
            <p class="text-sm text-text-secondary theme-dark:text-slate-400 w-full mb-2">Sizes:</p>
            <x-avatar name="John Doe" size="xs" />
            <x-avatar name="Jane Smith" size="sm" />
            <x-avatar name="Robert Johnson" size="md" />
            <x-avatar name="Emily Davis" size="lg" />
            <x-avatar name="Michael Brown" size="xl" />
        </div>

        <div class="flex flex-wrap items-center gap-4 mt-6">
            <p class="text-sm text-text-secondary theme-dark:text-slate-400 w-full mb-2">With Status:</p>
            <x-avatar name="Online User" size="lg" status="online" />
            <x-avatar name="Offline User" size="lg" status="offline" />
            <x-avatar name="Away User" size="lg" status="away" />
            <x-avatar name="Busy User" size="lg" status="busy" />
        </div>
    </x-card>
</x-section>

 {{-- Cards Section --}}
 <x-section class="mt-6">
    <h2 class="text-xl font-bold text-text-primary mb-4 theme-dark:text-white" data-translate="cards">{{ __('messages.cards') }}</h2>
    <x-grid :cols="1" :gap="'default'" :cols-sm="2" :cols-lg="3">
        <x-card
            :title="__('messages.default_card')"
            :subtitle="__('messages.card_subtitle')"
        >
            <p class="text-text-secondary theme-dark:text-slate-400">
                <span data-translate="card_content">{{ __('messages.card_content') }}</span>
            </p>
        </x-card>

        <x-card
            :title="__('messages.hoverable_card')"
            :hoverable="true"
            :shadow="'lg'"
        >
            <p class="text-text-secondary theme-dark:text-slate-400">
                <span data-translate="hoverable_description">{{ __('messages.hoverable_description') }}</span>
            </p>
        </x-card>

        <x-card
            :title="__('messages.no_padding')"
            :padding="'none'"
        >
            <img src="https://images.unsplash.com/photo-1519494026892-80bbd2d6fd0d?w=400&h=200&fit=crop" alt="Hospital" class="w-full h-32 object-cover">
            <div class="p-4">
                <p class="text-text-secondary theme-dark:text-slate-400">
                    <span data-translate="image_card">{{ __('messages.image_card') }}</span>
                </p>
            </div>
        </x-card>
    </x-grid>
</x-section>

 {{-- Modals Section --}}
 <x-section class="mt-6">
    <h2 class="text-xl font-bold text-text-primary mb-4" data-translate="modals">{{ __('messages.modals') }}</h2>

    <x-grid :cols="1" :gap="'default'" :cols-sm="2" :cols-lg="3">
        {{-- Basic Modal --}}
        <x-card :title="__('messages.basic_modal')">
            <p class="text-text-secondary mb-4" data-translate="basic_modal_description">{{ __('messages.basic_modal_description') }}</p>
            <x-modal :title="__('messages.basic_modal')" size="default" :trigger="__('messages.open_basic_modal') ?? 'Open Basic Modal'" class="w-full">
                <p class="text-text-secondary mb-4" data-translate="basic_modal_content">{{ __('messages.basic_modal_content') }}</p>
                <p class="text-text-secondary" data-translate="click_to_close">{{ __('messages.click_to_close') }}</p>
                @slot('footer')
                    <button data-dismiss="modal" class="px-4 py-2 rounded-xl bg-surface border border-border text-text-primary hover:bg-surface-hover transition-colors"><span data-translate="cancel">{{ __('messages.cancel') }}</span></button>
                    <button class="px-4 py-2 rounded-xl bg-primary text-white hover:bg-primary-dark transition-colors"><span data-translate="confirm">{{ __('messages.confirm') }}</span></button>
                @endslot
            </x-modal>
        </x-card>

        {{-- Small Modal --}}
        <x-card :title="__('messages.small_modal')">
            <p class="text-text-secondary mb-4" data-translate="small_modal_description">{{ __('messages.small_modal_description') }}</p>
            <x-modal :title="__('messages.confirm_action')" size="sm" :trigger="__('messages.open_small_modal') ?? 'Open Small Modal'" class="w-full">
                <div class="text-center">
                    <div class="w-12 h-12 mx-auto rounded-full bg-warning-50 flex items-center justify-center mb-4 theme-dark:bg-warning-900/30">
                        <svg class="w-6 h-6 text-warning-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <h4 class="text-text-primary font-semibold mb-2" data-translate="are_you_sure">{{ __('messages.are_you_sure') }}</h4>
                    <p class="text-text-secondary text-sm" data-translate="action_cannot_be_undone">{{ __('messages.action_cannot_be_undone') }}</p>
                </div>
                @slot('footer')
                    <button data-dismiss="modal" class="px-4 py-2 rounded-xl bg-surface border border-border text-text-primary hover:bg-surface-hover transition-colors"><span data-translate="cancel">{{ __('messages.cancel') }}</span></button>
                    <button class="px-4 py-2 rounded-xl bg-danger text-white hover:bg-danger-600 transition-colors"><span data-translate="delete">{{ __('messages.delete') }}</span></button>
                @endslot
            </x-modal>
        </x-card>

        {{-- Large Modal --}}
        <x-card :title="__('messages.large_modal')">
            <p class="text-text-secondary mb-4" data-translate="large_modal_description">{{ __('messages.large_modal_description') }}</p>
            <x-modal :title="__('messages.patient_information')" size="lg" :trigger="__('messages.open_large_modal') ?? 'Open Large Modal'" class="w-full">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-text-secondary mb-1" data-translate="patient_name">{{ __('messages.patient_name') }}</label>
                        <p class="text-text-primary font-medium">John Doe</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-text-secondary mb-1" data-translate="patient_id">{{ __('messages.patient_id') }}</label>
                        <p class="text-text-primary font-medium">P-001234</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-text-secondary mb-1" data-translate="date_of_birth">{{ __('messages.date_of_birth') }}</label>
                        <p class="text-text-primary font-medium">January 15, 1985</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-text-secondary mb-1" data-translate="blood_type">{{ __('messages.blood_type') }}</label>
                        <p class="text-text-primary font-medium">A+</p>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-text-secondary mb-1" data-translate="medical_notes">{{ __('messages.medical_notes') }}</label>
                        <p class="text-text-primary" data-translate="patient_history">{{ __('messages.patient_history') }}</p>
                    </div>
                </div>
                @slot('footer')
                    <button data-dismiss="modal" class="px-4 py-2 rounded-xl bg-surface border border-border text-text-primary hover:bg-surface-hover transition-colors"><span data-translate="close">{{ __('messages.close') }}</span></button>
                    <button class="px-4 py-2 rounded-xl bg-primary text-white hover:bg-primary-dark transition-colors"><span data-translate="save_changes">{{ __('messages.save_changes') }}</span></button>
                @endslot
            </x-modal>
        </x-card>

        {{-- Success Modal --}}
        <x-card :title="__('messages.success_modal')">
            <p class="text-text-secondary mb-4" data-translate="success_modal_description">{{ __('messages.success_modal_description') }}</p>
            <x-modal :title="__('messages.success_title')" size="sm" :trigger="__('messages.open_success_modal') ?? 'Open Success Modal'" class="w-full">
                <div class="text-center">
                    <div class="w-16 h-16 mx-auto rounded-full bg-success-50 flex items-center justify-center mb-4 theme-dark:bg-success-900/30 theme-stylis:bg-emerald-50 theme-stylis.theme-dark:bg-emerald-900/30">
                        <svg class="w-8 h-8 text-success-500 theme-dark:text-success-400 theme-stylis:text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <h4 class="text-text-primary font-semibold mb-2" data-translate="appointment_scheduled_title">{{ __('messages.appointment_scheduled_title') }}</h4>
                    <p class="text-text-secondary text-sm" data-translate="appointment_success_message">{{ __('messages.appointment_success_message') }}</p>
                </div>
                @slot('footer')
                    <button data-dismiss="modal" class="w-full px-4 py-2 rounded-xl bg-success text-white hover:bg-success-600 transition-colors"><span data-translate="done">{{ __('messages.done') }}</span></button>
                @endslot
            </x-modal>
        </x-card>

        {{-- Form Modal --}}
        <x-card :title="__('messages.form_modal')">
            <p class="text-text-secondary mb-4" data-translate="form_modal_description">{{ __('messages.form_modal_description') }}</p>
            <x-modal :title="__('messages.add_new_patient')" size="lg" :trigger="__('messages.open_form_modal') ?? 'Open Form Modal'" class="w-full">
                <form class="space-y-4">
                    <x-input
                        type="text"
                        name="fullname"
                        :label="__('messages.full_name')"
                        placeholder="{{ __('messages.enter_patient_name') }}"
                    />
                    <x-input
                        type="email"
                        name="email"
                        :label="__('messages.email_address')"
                        placeholder="{{ __('messages.email_address_placeholder') }}"
                    />
                    <x-select
                        name="department"
                        :label="__('messages.department')"
                        :options="[
                            ['value' => 'cardio', 'label' => __('messages.cardiology')],
                            ['value' => 'neuro', 'label' => __('messages.neurology')],
                            ['value' => 'pedia', 'label' => __('messages.pediatrics')],
                        ]"
                        placeholder="{{ __('messages.select_department') }}"
                    />
                </form>
                @slot('footer')
                    <button data-dismiss="modal" class="px-4 py-2 rounded-xl bg-surface border border-border text-text-primary hover:bg-surface-hover transition-colors"><span data-translate="cancel">{{ __('messages.cancel') }}</span></button>
                    <button class="px-4 py-2 rounded-xl bg-primary text-white hover:bg-primary-dark transition-colors"><span data-translate="add_patient">{{ __('messages.add_patient') }}</span></button>
                @endslot
            </x-modal>
        </x-card>

        {{-- XL Modal --}}
        <x-card :title="__('messages.xl_modal')">
            <p class="text-text-secondary mb-4" data-translate="xl_modal_description">{{ __('messages.xl_modal_description') }}</p>
            <x-modal :title="__('messages.medical_records')" size="xl" :trigger="__('messages.open_xl_modal') ?? 'Open XL Modal'" class="w-full">
                <div class="space-y-4">
                    {{-- Patient Info --}}
                    <div class="flex items-center gap-4 p-4 rounded-xl bg-surface-alt">
                        <div class="w-12 h-12 rounded-full bg-primary flex items-center justify-center text-white font-semibold">JD</div>
                        <div>
                            <h4 class="font-semibold text-text-primary">John Doe</h4>
                            <p class="text-sm text-text-secondary">Patient ID: P-001234</p>
                        </div>
                    </div>

                    {{-- Records Table --}}
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-border">
                                    <th class="text-left py-2 px-3 text-text-secondary font-medium" data-translate="date">{{ __('messages.date') }}</th>
                                    <th class="text-left py-2 px-3 text-text-secondary font-medium" data-translate="doctor">{{ __('messages.doctor') }}</th>
                                    <th class="text-left py-2 px-3 text-text-secondary font-medium" data-translate="diagnosis">{{ __('messages.diagnosis') }}</th>
                                    <th class="text-left py-2 px-3 text-text-secondary font-medium" data-translate="status">{{ __('messages.status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="border-b border-border">
                                    <td class="py-2 px-3 text-text-primary">Feb 20, 2026</td>
                                    <td class="py-2 px-3 text-text-primary">Dr. Smith</td>
                                    <td class="py-2 px-3 text-text-secondary" data-translate="annual_checkup">{{ __('messages.annual_checkup') }}</td>
                                    <td class="py-2 px-3"><x-badge variant="success" data-translate="completed">{{ __('messages.completed') }}</x-badge></td>
                                </tr>
                                <tr class="border-b border-border">
                                    <td class="py-2 px-3 text-text-primary">Jan 15, 2026</td>
                                    <td class="py-2 px-3 text-text-primary">Dr. Johnson</td>
                                    <td class="py-2 px-3 text-text-secondary" data-translate="follow_up_visit">{{ __('messages.follow_up_visit') }}</td>
                                    <td class="py-2 px-3"><x-badge variant="success" data-translate="completed">{{ __('messages.completed') }}</x-badge></td>
                                </tr>
                                <tr>
                                    <td class="py-2 px-3 text-text-primary">Dec 10, 2025</td>
                                    <td class="py-2 px-3 text-text-primary">Dr. Williams</td>
                                    <td class="py-2 px-3 text-text-secondary" data-translate="lab_results_review">{{ __('messages.lab_results_review') }}</td>
                                    <td class="py-2 px-3"><x-badge variant="warning" data-translate="pending">{{ __('messages.pending') }}</x-badge></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                @slot('footer')
                    <button data-dismiss="modal" class="px-4 py-2 rounded-xl bg-surface border border-border text-text-primary hover:bg-surface-hover transition-colors"><span data-translate="close">{{ __('messages.close') }}</span></button>
                    <button class="px-4 py-2 rounded-xl bg-primary text-white hover:bg-primary-dark transition-colors"><span data-translate="export_records">{{ __('messages.export_records') }}</span></button>
                @endslot
            </x-modal>
        </x-card>
    </x-grid>
</x-section>

 {{-- Form Components - Checkbox & Radio --}}
 <x-section class="mt-6">
    <h2 class="text-xl font-bold text-text-primary mb-4" data-translate="checkbox_radio">{{ __('messages.checkbox_radio') }}</h2>
    <x-grid :cols="1" :gap="'default'" :cols-sm="2">
        {{-- Checkboxes --}}
        <x-card :title="__('messages.checkboxes')">
            <div class="space-y-4">
                <x-checkbox name="remember" :label="__('messages.remember_me_checkbox')" :checked="true" />
                <x-checkbox name="terms" :label="__('messages.i_agree_terms') . ' ' . __('messages.terms_of_service')" />
                <x-checkbox name="newsletter" :label="__('messages.subscribe_newsletter')" :description="__('messages.newsletter_description')" />
                <x-checkbox name="disabled" :label="__('messages.disabled_option')" :disabled="true" />
            </div>
        </x-card>

        {{-- Radio Buttons --}}
        <x-card :title="__('messages.radio_buttons')">
            <div class="space-y-4">
                <x-radio name="gender" value="male" :label="__('messages.male')" />
                <x-radio name="gender" value="female" :label="__('messages.female')" />
                <x-radio name="plan" value="free" :label="__('messages.free_plan')" :description="__('messages.free_plan_description')" />
                <x-radio name="plan" value="pro" :label="__('messages.pro_plan')" :description="__('messages.pro_plan_description')" :checked="true" />
                <x-radio name="disabled" value="option" :label="__('messages.disabled_option')" :disabled="true" />
            </div>
        </x-card>
    </x-grid>
</x-section>

 {{-- Autocomplete Section --}}
 <x-section class="mt-6">
    <h2 class="text-xl font-bold text-text-primary mb-4" data-translate="autocomplete">{{ __('messages.autocomplete') }}</h2>
    <x-grid :cols="1" :gap="'default'" :cols-sm="2">
        <x-card :title="__('messages.single_select')">
            <x-autocomplete
                :label="__('messages.select_department_label')"
                name="department"
                :options="[
                    ['value' => 'cardio', 'label' => __('messages.cardiology')],
                    ['value' => 'neuro', 'label' => __('messages.neurology')],
                    ['value' => 'ortho', 'label' => __('messages.orthopedics')],
                    ['value' => 'pedia', 'label' => __('messages.pediatrics')],
                    ['value' => 'radiology', 'label' => __('messages.radiology')],
                ]"
                placeholder="{{ __('messages.choose_department') }}"
            />
        </x-card>

        <x-card :title="__('messages.searchable_select')">
            <x-autocomplete
                :label="__('messages.select_doctor_label')"
                name="doctor"
                :searchable="true"
                :options="[
                    ['value' => 'dr_smith', 'label' => __('messages.dr_smith')],
                    ['value' => 'dr_jones', 'label' => __('messages.dr_jones')],
                    ['value' => 'dr_williams', 'label' => __('messages.dr_williams')],
                    ['value' => 'dr_brown', 'label' => __('messages.dr_brown')],
                    ['value' => 'dr_davis', 'label' => __('messages.dr_davis')],
                ]"
                placeholder="{{ __('messages.search_select_doctor') }}"
            />
        </x-card>
    </x-grid>
</x-section>

 {{-- DataTable Section --}}
 <x-section class="mt-6">
    <h2 class="text-xl font-bold text-text-primary mb-4" data-translate="data_table">{{ __('messages.data_table') }}</h2>
    <x-card :title="__('messages.ajax_data_table')">
        <p class="text-text-secondary mb-4" data-translate="data_table_description">{{ __('messages.data_table_description') }}</p>

        {{-- Demo DataTable (static data for demo) --}}
        <div class="rounded-2xl bg-surface border border-border shadow overflow-hidden
                    theme-dark:bg-slate-800 theme-dark:border-slate-700
                    theme-stylis:bg-white/80 theme-stylis:border-teal-200">
            {{-- Table Header with Search and Actions --}}
            <div class="p-4 border-b border-border flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4
                        theme-dark:border-slate-700
                        theme-stylis:border-teal-100/50">
                <div class="relative w-full sm:w-64">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" placeholder="{{ __('messages.search_patients') }}" data-translate-placeholder="search_patients" class="w-full pl-10 pr-4 py-2 rounded-xl bg-surface border border-border focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none transition-all text-sm placeholder:text-text-hint theme-dark:bg-slate-700 theme-dark:border-slate-600 theme-stylis:bg-white/60 theme-stylis:border-teal-200">
                </div>
                <div class="flex items-center gap-2">
                    <button class="p-2 rounded-xl hover:bg-surface-hover transition-colors text-text-secondary" title="{{ __('messages.refresh') }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                    </button>
                    <button class="p-2 rounded-xl hover:bg-surface-hover transition-colors text-text-secondary" title="{{ __('messages.export') }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.707.293H19a2 2 0 012 2v9a2 2 0 01-2 2h-2M9 19h6"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-surface-alt border-b border-border theme-dark:bg-slate-800/50 theme-dark:border-slate-700 theme-stylis:bg-teal-50/30 theme-stylis:border-teal-100/50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wider" data-translate="patient">{{ __('messages.patient') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wider" data-translate="id">{{ __('messages.id') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wider" data-translate="visit_date">{{ __('messages.visit_date') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wider" data-translate="status">{{ __('messages.status') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wider" data-translate="actions">{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border theme-dark:divide-slate-700">
                        <tr class="hover:bg-surface-hover transition-colors theme-dark:hover:bg-slate-700/30 theme-stylis:hover:bg-teal-50/30">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <x-avatar name="John Doe" size="sm" />
                                    <div>
                                        <p class="text-sm font-medium text-text-primary">John Doe</p>
                                        <p class="text-xs text-text-secondary">john@example.com</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm text-text-secondary">P-001234</td>
                            <td class="px-4 py-3 text-sm text-text-secondary">Feb 25, 2026</td>
                            <td class="px-4 py-3">
                                <x-badge variant="success" data-translate="active">{{ __('messages.active') }}</x-badge>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <button class="p-1.5 rounded-lg hover:bg-surface-hover transition-colors" title="{{ __('messages.view') }}">
                                        <svg class="w-4 h-4 text-text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </button>
                                    <button class="p-1.5 rounded-lg hover:bg-surface-hover transition-colors" title="{{ __('messages.edit') }}">
                                        <svg class="w-4 h-4 text-text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr class="hover:bg-surface-hover transition-colors theme-dark:hover:bg-slate-700/30 theme-stylis:hover:bg-teal-50/30">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <x-avatar name="Jane Smith" size="sm" />
                                    <div>
                                        <p class="text-sm font-medium text-text-primary">Jane Smith</p>
                                        <p class="text-xs text-text-secondary">jane@example.com</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm text-text-secondary">P-001235</td>
                            <td class="px-4 py-3 text-sm text-text-secondary">Feb 24, 2026</td>
                            <td class="px-4 py-3">
                                <x-badge variant="warning" data-translate="pending">{{ __('messages.pending') }}</x-badge>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <button class="p-1.5 rounded-lg hover:bg-surface-hover transition-colors" title="{{ __('messages.view') }}">
                                        <svg class="w-4 h-4 text-text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </button>
                                    <button class="p-1.5 rounded-lg hover:bg-surface-hover transition-colors" title="{{ __('messages.edit') }}">
                                        <svg class="w-4 h-4 text-text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr class="hover:bg-surface-hover transition-colors theme-dark:hover:bg-slate-700/30 theme-stylis:hover:bg-teal-50/30">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <x-avatar name="Robert Johnson" size="sm" />
                                    <div>
                                        <p class="text-sm font-medium text-text-primary">Robert Johnson</p>
                                        <p class="text-xs text-text-secondary">robert@example.com</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm text-text-secondary">P-001236</td>
                            <td class="px-4 py-3 text-sm text-text-secondary">Feb 23, 2026</td>
                            <td class="px-4 py-3">
                                <x-badge variant="success" data-translate="active">{{ __('messages.active') }}</x-badge>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <button class="p-1.5 rounded-lg hover:bg-surface-hover transition-colors" title="{{ __('messages.view') }}">
                                        <svg class="w-4 h-4 text-text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </button>
                                    <button class="p-1.5 rounded-lg hover:bg-surface-hover transition-colors" title="{{ __('messages.edit') }}">
                                        <svg class="w-4 h-4 text-text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr class="hover:bg-surface-hover transition-colors theme-dark:hover:bg-slate-700/30 theme-stylis:hover:bg-teal-50/30">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <x-avatar name="Emily Davis" size="sm" />
                                    <div>
                                        <p class="text-sm font-medium text-text-primary">Emily Davis</p>
                                        <p class="text-xs text-text-secondary">emily@example.com</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm text-text-secondary">P-001237</td>
                            <td class="px-4 py-3 text-sm text-text-secondary">Feb 22, 2026</td>
                            <td class="px-4 py-3">
                                <x-badge variant="default" data-translate="inactive">{{ __('messages.inactive') }}</x-badge>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <button class="p-1.5 rounded-lg hover:bg-surface-hover transition-colors" title="{{ __('messages.view') }}">
                                        <svg class="w-4 h-4 text-text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </button>
                                    <button class="p-1.5 rounded-lg hover:bg-surface-hover transition-colors" title="{{ __('messages.edit') }}">
                                        <svg class="w-4 h-4 text-text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Pagination Demo --}}
            <div class="p-4 border-t border-border flex flex-col sm:flex-row items-center justify-between gap-4 theme-dark:border-slate-700 theme-stylis:border-teal-100/50">
                <div class="text-sm text-text-secondary">
                    <span data-translate="showing">{{ __('messages.showing') }}</span> <span class="font-medium text-text-primary">1</span>
                    <span data-translate="to">{{ __('messages.to') }}</span> <span class="font-medium text-text-primary">4</span>
                    <span data-translate="of">{{ __('messages.of') }}</span> <span class="font-medium text-text-primary">24</span> <span data-translate="results">{{ __('messages.results') }}</span>
                </div>
                <div class="flex items-center gap-1">
                    <button class="px-3 py-2 rounded-lg border border-border hover:bg-surface-hover disabled:opacity-50 disabled:cursor-not-allowed transition-colors text-sm theme-dark:border-slate-600 theme-stylis:border-teal-200" disabled data-translate="first">{{ __('messages.first') }}</button>
                    <button class="px-3 py-2 rounded-lg bg-primary text-white border-primary text-sm">1</button>
                    <button class="px-3 py-2 rounded-lg border border-border hover:bg-surface-hover text-sm theme-dark:bg-slate-700 theme-dark:border-slate-600 theme-stylis:border-teal-200">2</button>
                    <button class="px-3 py-2 rounded-lg border border-border hover:bg-surface-hover text-sm theme-dark:bg-slate-700 theme-dark:border-slate-600 theme-stylis:border-teal-200">3</button>
                    <button class="px-3 py-2 rounded-lg border border-border hover:bg-surface-hover text-sm theme-dark:bg-slate-700 theme-dark:border-slate-600 theme-stylis:border-teal-200">...</button>
                    <button class="px-3 py-2 rounded-lg border border-border hover:bg-surface-hover text-sm theme-dark:bg-slate-700 theme-dark:border-slate-600 theme-stylis:border-teal-200">10</button>
                    <button class="px-3 py-2 rounded-lg border border-border hover:bg-surface-hover disabled:opacity-50 disabled:cursor-not-allowed transition-colors text-sm theme-dark:border-slate-600 theme-stylis:border-teal-200" data-translate="last">{{ __('messages.last') }}</button>
                </div>
            </div>
        </div>
    </x-card>
</x-section>

 {{-- Skeleton Loaders Section --}}
 <x-section class="mt-6">
    <h2 class="text-xl font-bold text-text-primary mb-4">{{ __('messages.skeleton_loaders') }}</h2>
    <x-grid :cols="2">
        <x-card :title="__('messages.text_skeleton')">
            <x-skeleton type="text" :lines="3" />
        </x-card>

        <x-card :title="__('messages.circle_skeleton')">
            <div class="flex items-center gap-4">
                <x-skeleton type="circle" width="48px" />
                <x-skeleton type="circle" width="64px" />
                <x-skeleton type="circle" width="40px" />
            </div>
        </x-card>

        <x-card :title="__('messages.card_skeleton')">
            <x-skeleton type="card" />
        </x-card>

        <x-card :title="__('messages.table_skeleton')">
            <x-skeleton type="table" />
        </x-card>
    </x-grid>
</x-section>

 {{-- Progress Bars Section --}}
 <x-section class="mt-6">
    <h2 class="text-xl font-bold text-text-primary mb-4">{{ __('messages.progress_bars') }}</h2>
    <x-card>
        <div class="space-y-6">
            <div>
                <label class="block text-sm font-medium text-text-primary mb-2">{{ __('messages.basic_progress') }}</label>
                <x-progress :value="75" />
            </div>

            <div>
                <label class="block text-sm font-medium text-text-primary mb-2">{{ __('messages.with_label') }}</label>
                <x-progress :value="60" label="Upload Progress" :showPercentage="true" />
            </div>

            <div>
                <label class="block text-sm font-medium text-text-primary mb-2">{{ __('messages.variants') }}</label>
                <div class="space-y-3">
                    <x-progress :value="100" variant="success" />
                    <x-progress :value="30" variant="danger" />
                    <x-progress :value="50" variant="warning" />
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-text-primary mb-2">{{ __('messages.sizes') }}</label>
                <div class="space-y-3">
                    <x-progress :value="45" size="sm" />
                    <x-progress :value="45" size="md" />
                    <x-progress :value="45" size="lg" />
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-text-primary mb-2">{{ __('messages.striped_animated') }}</label>
                <div class="space-y-3">
                    <x-progress :value="70" :striped="true" />
                    <x-progress :value="80" :striped="true" :animated="true" />
                </div>
            </div>
        </div>
    </x-card>
</x-section>

 {{-- Tooltip Section --}}
 <x-section class="mt-6">
    <h2 class="text-xl font-bold text-text-primary mb-4">{{ __('messages.tooltips') }}</h2>
    <x-card>
        <div class="flex flex-wrap items-center gap-4">
            <x-tooltip content="Top tooltip" position="top">
                <x-button variant="secondary">Top</x-button>
            </x-tooltip>

            <x-tooltip content="Bottom tooltip" position="bottom">
                <x-button variant="secondary">Bottom</x-button>
            </x-tooltip>

            <x-tooltip content="Left tooltip" position="left">
                <x-button variant="secondary">Left</x-button>
            </x-tooltip>

            <x-tooltip content="Right tooltip" position="right">
                <x-button variant="secondary">Right</x-button>
            </x-tooltip>

            <x-tooltip content="<strong>Rich</strong> <em>content</em> tooltip">
                <x-button variant="secondary">Rich Content</x-button>
            </x-tooltip>

            <x-tooltip trigger="click" content="Click to see tooltip">
                <x-button variant="secondary">Click Trigger</x-button>
            </x-tooltip>
        </div>
    </x-card>
</x-section>

 {{-- Popover Section --}}
 <x-section class="mt-6">
    <h2 class="text-xl font-bold text-text-primary mb-4">{{ __('messages.popovers') }}</h2>
    <x-card>
        <div class="flex flex-wrap items-center gap-4">
            <x-popover title="Popover Title" content="This is a popover content">
                <x-button variant="primary">Basic Popover</x-button>
            </x-popover>

            <x-popover placement="bottom" content="Bottom popover content">
                <x-button variant="secondary">Bottom Popover</x-button>
            </x-popover>

            <x-popover trigger="hover" content="Hover to see popover">
                <x-button variant="success">Hover Popover</x-button>
            </x-popover>
        </div>
    </x-card>
</x-section>

 {{-- File Upload Section --}}
 <x-section class="mt-6">
    <h2 class="text-xl font-bold text-text-primary mb-4">{{ __('messages.file_upload') }}</h2>
    <x-grid :cols="2">
        <x-card :title="__('messages.single_upload')">
            <x-file-upload
                name="document"
                label="Upload Document"
                accept=".pdf,.doc,.docx"
                :maxSize="5120"
            />
        </x-card>

        <x-card :title="__('messages.image_upload')">
            <x-file-upload
                name="image"
                label="Upload Image"
                accept="image/*"
                :multiple="true"
                :maxFiles="5"
            />
        </x-card>
    </x-grid>
</x-section>

 {{-- Date/Time Picker Section --}}
 <x-section class="mt-6">
    <h2 class="text-xl font-bold text-text-primary mb-4">{{ __('messages.date_time_pickers') }}</h2>
    <x-grid :cols="2">
        <x-card :title="__('messages.date_picker')">
            <x-date-time-picker
                name="date"
                type="date"
                label="Select Date"
                :clearable="true"
                :todayButton="true"
            />
        </x-card>

        <x-card :title="__('messages.time_picker')">
            <x-date-time-picker
                name="time"
                type="time"
                label="Select Time"
            />
        </x-card>

        <x-card :title="__('messages.datetime_picker')">
            <x-date-time-picker
                name="datetime"
                type="datetime"
                label="Select Date & Time"
            />
        </x-card>

        <x-card :title="__('messages.inline_calendar')">
            <div x-data="dateTimePickerController({
                type: 'date',
                inline: true,
                todayButton: true
            })">
                @slot('calendar')
                <div class="calendar-container p-4 rounded-xl shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <button type="button" @click="prevMonth()" class="p-2 rounded hover:bg-gray-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </button>
                        <div class="text-lg font-semibold">
                            <span x-text="monthNames[currentMonth]"></span>
                            <span x-text="currentYear" class="ml-1"></span>
                        </div>
                        <button type="button" @click="nextMonth()" class="p-2 rounded hover:bg-gray-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                    </div>
                    <div class="calendar-grid grid grid-cols-7 gap-1 mb-2">
                        <template x-for="day in dayNames" :key="day">
                            <div class="text-center text-xs text-text-tertiary py-1" x-text="day"></div>
                        </template>
                    </div>
                    <div class="calendar-grid grid grid-cols-7 gap-1">
                        <template x-for="(day, index) in calendarDays" :key="index">
                            <button type="button"
                                    :disabled="isDisabled(day)"
                                    @click="selectDay(day)"
                                    class="calendar-day aspect-square flex items-center justify-center rounded-lg text-sm"
                                    :class="{
                                        'selected': isSelected(day),
                                        'today': isToday(day),
                                        'invisible': !day
                                    }"
                                    x-text="day">
                            </button>
                        </template>
                    </div>
                </div>
                @endslot
            </div>
        </x-card>
    </x-grid>
</x-section>

 {{-- Toast Notifications Section --}}
 <x-section class="mt-6">
    <h2 class="text-xl font-bold text-text-primary mb-4">{{ __('messages.toast_notifications') }}</h2>
    <x-card>
        <p class="text-text-secondary mb-4">{{ __('messages.toast_description') }}</p>
        <div class="flex flex-wrap items-center gap-3">
            <x-button @click="$toast.success('Success message!')" variant="success">
                Success Toast
            </x-button>
            <x-button @click="$toast.error('Error message!')" variant="danger">
                Error Toast
            </x-button>
            <x-button @click="$toast.warning('Warning message!')" variant="warning">
                Warning Toast
            </x-button>
            <x-button @click="$toast.info('Info message!')" variant="info">
                Info Toast
            </x-button>
            <x-button @click="$toast.show('Custom toast with title', 'info', { title: 'Custom Title' })" variant="secondary">
                Custom Toast
            </x-button>
            <x-button @click="$toast.clear()" variant="ghost">
                Clear All
            </x-button>
        </div>

        {{-- Toast Container --}}
        <x-toast />
    </x-card>
</x-section>

 {{-- Chart Section --}}
 <x-section class="mt-6">
    <h2 class="text-xl font-bold text-text-primary mb-4">{{ __('messages.charts') }}</h2>
    <x-grid :cols="2">
        <x-card :title="__('messages.line_chart')">
            <div x-data="chartController" class="chart-container" data-type="line" data-data='{"labels":["Jan","Feb","Mar","Apr","May","Jun"],"datasets":[{"label":"Revenue","data":[65,59,80,81,56,55],"color":"#3b82f6"}]}' style="height:250px">
                <canvas></canvas>
            </div>
        </x-card>

        <x-card :title="__('messages.bar_chart')">
            <div x-data="chartController" class="chart-container" data-type="bar" data-data='{"labels":["Q1","Q2","Q3","Q4"],"datasets":[{"label":"Sales","data":[120,190,30,50],"color":"#22c55e"}]}' style="height:250px">
                <canvas></canvas>
            </div>
        </x-card>

        <x-card :title="__('messages.doughnut_chart')">
            <div x-data="chartController" class="chart-container" data-type="doughnut" data-data='{"labels":["Active","Pending","Inactive"],"datasets":[{"label":"Status","data":[300,50,100],"color":"#3b82f6"}]}' style="height:250px">
                <canvas></canvas>
            </div>
        </x-card>

        <x-card :title="__('messages.pie_chart')">
            <div x-data="chartController" class="chart-container" data-type="pie" data-data='{"labels":["Cardiology","Neurology","Orthopedics","Pediatrics"],"datasets":[{"label":"Departments","data":[35,25,20,20],"color":"#f43f5e"}]}' style="height:250px">
                <canvas></canvas>
            </div>
        </x-card>
    </x-grid>
</x-section>

 {{-- Accessibility Features Section --}}
 <x-section class="mt-6">
    <h2 class="text-xl font-bold text-text-primary mb-4">{{ __('messages.accessibility_features') }}</h2>
    <x-card>
        <div class="space-y-6">
            {{-- Font Size --}}
            <div class="flex items-center justify-between p-4 bg-surface-alt rounded-xl">
                <div>
                    <h3 class="font-medium text-text-primary">{{ __('messages.font_size') }}</h3>
                    <p class="text-sm text-text-secondary">{{ __('messages.current_font') }}: <span x-text="fontSize"></span> (<span x-text="customScale"></span>%)</p>
                </div>
                <div class="flex items-center gap-2">
                    <button @click="decreaseFontSize()" class="p-2 rounded-lg hover:bg-surface-hover border border-border transition-colors">A-</button>
                    <button @click="resetFontSize()" class="p-2 rounded-lg hover:bg-surface-hover border border-border transition-colors">Reset</button>
                    <button @click="increaseFontSize()" class="p-2 rounded-lg hover:bg-surface-hover border border-border transition-colors">A+</button>
                </div>
            </div>

            {{-- High Contrast --}}
            <div class="flex items-center justify-between p-4 bg-surface-alt rounded-xl">
                <div>
                    <h3 class="font-medium text-text-primary">{{ __('messages.high_contrast') }}</h3>
                    <p class="text-sm text-text-secondary">{{ __('messages.high_contrast_desc') }}</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" :checked="highContrast" @change="toggleHighContrast()" class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 dark:bg-gray-700 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 dark:peer-focus:ring-primary-800 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-600 dark:peer-checked:bg-primary-500"></div>
                </label>
            </div>

            {{-- Reduced Motion --}}
            <div class="flex items-center justify-between p-4 bg-surface-alt rounded-xl">
                <div>
                    <h3 class="font-medium text-text-primary">{{ __('messages.reduced_motion') }}</h3>
                    <p class="text-sm text-text-secondary">{{ __('messages.reduced_motion_desc') }}</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" :checked="reducedMotion" @change="toggleReducedMotion()" class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 dark:bg-gray-700 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 dark:peer-focus:ring-primary-800 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-600 dark:peer-checked:bg-primary-500"></div>
                </label>
            </div>
        </div>
    </x-card>
</x-section>

@endsection
