{{-- Farmasi: Regulasi --}}
@extends('layouts.app')

@section('title', __('messages.farmasi_regulasi_title') . ' - ' . ($appName ?? config('app.name')))

@section('page-title', __('messages.farmasi_regulasi_title'))
@section('page-description', __('messages.farmasi_regulasi_subtitle'))

@section('content')
<div
    x-data="regulasiEmsPage({
        locale: @js(app()->getLocale()),
        packages: @js($packages ?? []),
        regs: [],
        updatePackageUrlTemplate: @js(route('medis.regulasi.packages.update', ['package' => 0])),
        updateRegUrlTemplate: '',
    })"
    class="space-y-6"
>
    <x-card>
        <div class="flex items-center gap-2 mb-4">
            <svg class="w-5 h-5 text-text-tertiary" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2"/>
            </svg>
            <h3 class="text-lg font-semibold text-text-primary" data-translate="medis_regulasi_packages_title">
                {{ __('messages.medis_regulasi_packages_title') }}
            </h3>
        </div>

        <div class="flex flex-col md:flex-row md:items-center gap-3 mb-4">
            <div class="flex-1">
                <x-input
                    name="pkg_table_search"
                    :label="__('messages.search')"
                    dataTranslateLabel="search"
                    :placeholder="__('messages.medis_regulasi_search_package')"
                    x-model="pkgSearch"
                    @input.debounce.150ms="pkgPage = 1"
                />
            </div>
            <div class="w-full md:w-48">
                <x-select
                    name="pkg_page_size"
                    :label="__('messages.medis_show')"
                    dataTranslateLabel="medis_show"
                    :options="[
                        10 => '10',
                        25 => '25',
                        50 => '50',
                    ]"
                    :value="10"
                    x-model.number="pkgPageSize"
                    @change="pkgPage = 1"
                />
            </div>
        </div>

        <div class="overflow-x-auto rounded-xl border border-border">
            <table class="min-w-full divide-y divide-border">
                <thead class="bg-surface-alt">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold tracking-wide text-text-secondary uppercase" data-translate="name">{{ __('messages.name') }}</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold tracking-wide text-text-secondary uppercase" data-translate="farmasi_bandage">{{ __('messages.farmasi_bandage') }}</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold tracking-wide text-text-secondary uppercase" data-translate="farmasi_ifaks">{{ __('messages.farmasi_ifaks') }}</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold tracking-wide text-text-secondary uppercase" data-translate="farmasi_painkiller">{{ __('messages.farmasi_painkiller') }}</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold tracking-wide text-text-secondary uppercase" data-translate="price">{{ __('messages.price') }}</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold tracking-wide text-text-secondary uppercase" data-translate="actions">{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border bg-surface">
                    <template x-if="pkgPageRows.length === 0">
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-text-secondary">
                                {{ __('messages.no_data') }}
                            </td>
                        </tr>
                    </template>

                    <template x-for="row in pkgPageRows" :key="row.id">
                        <tr class="hover:bg-surface-hover transition-colors">
                            <td class="px-4 py-2 text-sm text-text-primary" x-text="row.name"></td>
                            <td class="px-4 py-2 text-sm text-text-secondary text-right" x-text="row.bandage_qty"></td>
                            <td class="px-4 py-2 text-sm text-text-secondary text-right" x-text="row.ifaks_qty"></td>
                            <td class="px-4 py-2 text-sm text-text-secondary text-right" x-text="row.painkiller_qty"></td>
                            <td class="px-4 py-2 text-sm text-text-primary whitespace-nowrap text-right" x-text="formatMoney(row.price)"></td>
                            <td class="px-4 py-2 text-sm text-right">
                                <x-button
                                    type="button"
                                    variant="warning"
                                    size="xs"
                                    class="!bg-warning-500 !text-white hover:!bg-warning-600"
                                    @click="openPkgEdit(row)"
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
                <span class="font-semibold text-text-primary" x-text="formatMoney(pkgTotalShown)"></span>
            </p>

            <div class="flex items-center justify-end">
                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        class="inline-flex items-center justify-center px-3 py-1.5 rounded-lg border border-border hover:bg-surface-hover transition-colors text-sm font-semibold disabled:opacity-50"
                        :disabled="pkgPage <= 1"
                        @click="pkgPage = Math.max(1, pkgPage - 1)"
                    >
                        <span data-translate="previous">{{ __('messages.previous') }}</span>
                    </button>
                    <span class="text-sm text-text-secondary" x-text="pkgPage + ' / ' + pkgPageCount"></span>
                    <button
                        type="button"
                        class="inline-flex items-center justify-center px-3 py-1.5 rounded-lg border border-border hover:bg-surface-hover transition-colors text-sm font-semibold disabled:opacity-50"
                        :disabled="pkgPage >= pkgPageCount"
                        @click="pkgPage = Math.min(pkgPageCount, pkgPage + 1)"
                    >
                        <span data-translate="next">{{ __('messages.next') }}</span>
                    </button>
                </div>
            </div>
        </div>
    </x-card>

    <x-modal
        x-model="pkgEditOpen"
        :title="__('messages.medis_regulasi_edit_package')"
        size="lg"
        @close-modal="closePkgEdit()"
    >
        <form class="space-y-4" @submit.prevent="savePkgEdit()">
            <div>
                <x-input
                    name="pkg_name"
                    :label="__('messages.name')"
                    dataTranslateLabel="name"
                    x-model="pkgForm.name"
                    required
                />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <x-input
                    type="number"
                    name="pkg_bandage"
                    :label="__('messages.farmasi_bandage_qty')"
                    dataTranslateLabel="farmasi_bandage_qty"
                    min="0"
                    x-model.number="pkgForm.bandage_qty"
                    required
                />
                <x-input
                    type="number"
                    name="pkg_ifaks"
                    :label="__('messages.farmasi_ifaks_qty')"
                    dataTranslateLabel="farmasi_ifaks_qty"
                    min="0"
                    x-model.number="pkgForm.ifaks_qty"
                    required
                />
                <x-input
                    type="number"
                    name="pkg_painkiller"
                    :label="__('messages.farmasi_painkiller_qty')"
                    dataTranslateLabel="farmasi_painkiller_qty"
                    min="0"
                    x-model.number="pkgForm.painkiller_qty"
                    required
                />
                <x-input
                    type="number"
                    name="pkg_price"
                    :label="__('messages.price')"
                    dataTranslateLabel="price"
                    min="0"
                    x-model.number="pkgForm.price"
                    required
                />
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <x-button type="button" variant="secondary" @click="closePkgEdit()">
                    <span data-translate="cancel">{{ __('messages.cancel') }}</span>
                </x-button>
                <x-button type="submit" variant="success" x-bind:disabled="saving">
                    <span data-translate="save">{{ __('messages.save') }}</span>
                </x-button>
            </div>
        </form>
    </x-modal>
</div>
@endsection
