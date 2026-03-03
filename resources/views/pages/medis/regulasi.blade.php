{{-- Medis: Regulasi EMS --}}
@extends('layouts.app')

@section('title', __('messages.medis_regulasi_title') . ' - ' . ($appName ?? config('app.name')))

@section('page-title', __('messages.medis_regulasi_title'))
@section('page-description', __('messages.medis_regulasi_subtitle'))

@section('content')
@php
    $thousandSep = app()->getLocale() === 'id' ? '.' : ',';
    $decimalSep = app()->getLocale() === 'id' ? ',' : '.';
    $fmt = fn ($n) => number_format((float) $n, 0, $decimalSep, $thousandSep);
@endphp

<div
    x-data="regulasiEmsPage({
        locale: @js(app()->getLocale()),
        packages: [],
        regs: @js($regs ?? []),
        updatePackageUrlTemplate: '',
        updateRegUrlTemplate: @js(route('medis.regulasi.regulations.update', ['medicalRegulation' => 0])),
    })"
    class="space-y-6"
>
    <x-card>
        <div class="flex items-center gap-2 mb-4">
            <svg class="w-5 h-5 text-text-tertiary" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/>
            </svg>
            <h3 class="text-lg font-semibold text-text-primary" data-translate="medis_regulasi_regs_title">
                {{ __('messages.medis_regulasi_regs_title') }}
            </h3>
        </div>

        <div class="flex flex-col md:flex-row md:items-center gap-3 mb-4">
            <div class="flex-1">
                <x-input
                    name="reg_table_search"
                    :label="__('messages.search')"
                    dataTranslateLabel="search"
                    :placeholder="__('messages.medis_regulasi_search_reg')"
                    x-model="regSearch"
                    @input.debounce.150ms="regPage = 1"
                />
            </div>
            <div class="w-full md:w-48">
                <x-select
                    name="reg_page_size"
                    :label="__('messages.medis_show')"
                    dataTranslateLabel="medis_show"
                    :options="[
                        10 => '10',
                        25 => '25',
                        50 => '50',
                    ]"
                    :value="10"
                    x-model.number="regPageSize"
                    @change="regPage = 1"
                />
            </div>
        </div>

        <div class="overflow-x-auto rounded-xl border border-border">
            <table class="min-w-full divide-y divide-border">
                <thead class="bg-surface-alt">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold tracking-wide text-text-secondary uppercase" data-translate="medis_regulasi_category">{{ __('messages.medis_regulasi_category') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold tracking-wide text-text-secondary uppercase" data-translate="medis_regulasi_code">{{ __('messages.medis_regulasi_code') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold tracking-wide text-text-secondary uppercase" data-translate="name">{{ __('messages.name') }}</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold tracking-wide text-text-secondary uppercase" data-translate="price">{{ __('messages.price') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold tracking-wide text-text-secondary uppercase" data-translate="medis_payment_type">{{ __('messages.medis_payment_type') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold tracking-wide text-text-secondary uppercase" data-translate="status">{{ __('messages.status') }}</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold tracking-wide text-text-secondary uppercase" data-translate="actions">{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border bg-surface">
                    <template x-if="regPageRows.length === 0">
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center text-text-secondary">
                                {{ __('messages.no_data') }}
                            </td>
                        </tr>
                    </template>

                    <template x-for="row in regPageRows" :key="row.id">
                        <tr class="hover:bg-surface-hover transition-colors">
                            <td class="px-4 py-2 text-sm text-text-primary" x-text="row.category"></td>
                            <td class="px-4 py-2 text-sm text-text-secondary font-mono" x-text="row.code"></td>
                            <td class="px-4 py-2 text-sm text-text-primary" x-text="row.name"></td>
                            <td class="px-4 py-2 text-sm text-text-primary whitespace-nowrap text-right" x-text="regPriceText(row)"></td>
                            <td class="px-4 py-2 text-sm text-text-secondary" x-text="row.payment_type"></td>
                            <td class="px-4 py-2 text-sm text-text-secondary" x-text="row.is_active ? t('active', 'Aktif') : t('inactive', 'Nonaktif')"></td>
                            <td class="px-4 py-2 text-sm text-right">
                                <x-button
                                    type="button"
                                    variant="warning"
                                    size="xs"
                                    class="!bg-warning-500 !text-white hover:!bg-warning-600"
                                    @click="openRegEdit(row)"
                                >
                                    <span data-translate="edit">{{ __('messages.edit') }}</span>
                                </x-button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mt-4">
            <p class="text-sm text-text-secondary">
                <span data-translate="medis_regulasi_total_shown">{{ __('messages.medis_regulasi_total_shown') }}</span>:
                <span class="font-semibold text-text-primary" x-text="formatMoney(regTotalShown)"></span>
            </p>

            <div class="flex items-center justify-end">
                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        class="inline-flex items-center justify-center px-3 py-1.5 rounded-lg border border-border hover:bg-surface-hover transition-colors text-sm font-semibold disabled:opacity-50"
                        :disabled="regPage <= 1"
                        @click="regPage = Math.max(1, regPage - 1)"
                    >
                        <span data-translate="previous">{{ __('messages.previous') }}</span>
                    </button>
                    <span class="text-sm text-text-secondary" x-text="regPage + ' / ' + regPageCount"></span>
                    <button
                        type="button"
                        class="inline-flex items-center justify-center px-3 py-1.5 rounded-lg border border-border hover:bg-surface-hover transition-colors text-sm font-semibold disabled:opacity-50"
                        :disabled="regPage >= regPageCount"
                        @click="regPage = Math.min(regPageCount, regPage + 1)"
                    >
                        <span data-translate="next">{{ __('messages.next') }}</span>
                    </button>
                </div>
            </div>
        </div>
    </x-card>

    <x-modal
        x-model="regEditOpen"
        :title="__('messages.medis_regulasi_edit_reg')"
        size="xl"
        @close-modal="closeRegEdit()"
    >
        <form class="space-y-4" @submit.prevent="saveRegEdit()">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-3">
                <x-input
                    name="reg_category"
                    :label="__('messages.medis_regulasi_category')"
                    dataTranslateLabel="medis_regulasi_category"
                    x-model="regForm.category"
                    required
                />

                <div class="rounded-xl border border-border bg-surface-alt p-3">
                    <p class="text-xs text-text-tertiary" data-translate="medis_regulasi_code">{{ __('messages.medis_regulasi_code') }}</p>
                    <p class="text-sm font-mono text-text-primary mt-1" x-text="regForm.code || '-'"></p>
                </div>

                <div class="lg:col-span-2">
                    <x-input
                        name="reg_name"
                        :label="__('messages.name')"
                        dataTranslateLabel="name"
                        x-model="regForm.name"
                        required
                    />
                </div>

                <x-input
                    name="reg_location"
                    :label="__('messages.medis_regulasi_location')"
                    dataTranslateLabel="medis_regulasi_location"
                    x-model="regForm.location"
                />

                <x-select
                    name="reg_price_type"
                    :label="__('messages.medis_regulasi_price_type')"
                    dataTranslateLabel="medis_regulasi_price_type"
                    :options="[
                        'FIXED' => 'FIXED',
                        'RANGE' => 'RANGE',
                    ]"
                    :value="'FIXED'"
                    x-model="regForm.price_type"
                    @change="onRegPriceTypeChange()"
                />

                <x-input
                    type="number"
                    name="reg_price_min"
                    :label="__('messages.medis_regulasi_price_min')"
                    dataTranslateLabel="medis_regulasi_price_min"
                    min="0"
                    x-model.number="regForm.price_min"
                    required
                    @input.debounce.0ms="onRegMinChanged()"
                />

                <x-input
                    type="number"
                    name="reg_price_max"
                    :label="__('messages.medis_regulasi_price_max')"
                    dataTranslateLabel="medis_regulasi_price_max"
                    min="0"
                    x-model.number="regForm.price_max"
                    x-bind:disabled="regForm.price_type === 'FIXED'"
                    required
                />

                <x-select
                    name="reg_payment"
                    :label="__('messages.medis_payment_type')"
                    dataTranslateLabel="medis_payment_type"
                    :options="[
                        'CASH' => 'CASH',
                        'INVOICE' => 'INVOICE',
                        'BILLING' => 'BILLING',
                    ]"
                    :value="'CASH'"
                    x-model="regForm.payment_type"
                />

                <x-input
                    type="number"
                    name="reg_duration"
                    :label="__('messages.medis_regulasi_duration')"
                    dataTranslateLabel="medis_regulasi_duration"
                    min="0"
                    x-model.number="regForm.duration_minutes"
                />

                <div class="lg:col-span-2">
                    <label class="block text-sm font-medium text-text-primary mb-2" data-translate="medis_regulasi_notes">
                        {{ __('messages.medis_regulasi_notes') }}
                    </label>
                    <textarea
                        class="w-full px-4 py-3 rounded-xl bg-surface border border-border focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none transition-all text-sm placeholder:text-text-hint theme-dark:bg-slate-700 theme-dark:border-slate-600 theme-dark:text-white theme-stylis:bg-white/60 theme-stylis:border-teal-200"
                        rows="3"
                        x-model="regForm.notes"
                    ></textarea>
                </div>

                <div class="lg:col-span-2">
                    <label class="inline-flex items-center gap-2 text-sm text-text-secondary">
                        <input type="checkbox" class="rounded border-border" x-model="regForm.is_active">
                        <span data-translate="medis_regulasi_active">{{ __('messages.medis_regulasi_active') }}</span>
                    </label>
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <x-button type="button" variant="secondary" @click="closeRegEdit()">
                    <span data-translate="cancel">{{ __('messages.cancel') }}</span>
                </x-button>
                <x-button type="submit" variant="primary" x-bind:disabled="saving">
                    <span data-translate="save">{{ __('messages.save') }}</span>
                </x-button>
            </div>
        </form>
    </x-modal>
</div>
@endsection
