@extends('layouts.app')

@section('title', __('messages.farmasi_rekap_title') . ' - ' . __('messages.app_name'))

@section('page-title', __('messages.farmasi_rekap_title'))
@section('page-description', __('messages.farmasi_rekap_subtitle'))

@section('content')
@php
    $thousandSep = app()->getLocale() === 'id' ? '.' : ',';
    $decimalSep = app()->getLocale() === 'id' ? ',' : '.';
    $fmt = fn ($n) => number_format((float) $n, 0, $decimalSep, $thousandSep);
    $pkgAData = $pkgA ? [
        'id' => (int) $pkgA->id,
        'name' => (string) $pkgA->name,
        'price' => (int) $pkgA->price,
        'bandage' => (int) $pkgA->bandage_qty,
        'ifaks' => (int) $pkgA->ifaks_qty,
        'painkiller' => (int) $pkgA->painkiller_qty,
    ] : null;
    $pkgBData = $pkgB ? [
        'id' => (int) $pkgB->id,
        'name' => (string) $pkgB->name,
        'price' => (int) $pkgB->price,
        'bandage' => (int) $pkgB->bandage_qty,
        'ifaks' => (int) $pkgB->ifaks_qty,
        'painkiller' => (int) $pkgB->painkiller_qty,
    ] : null;
@endphp

@if(session('success') || $errors->any())
    <div class="space-y-4 mb-6">
        @if(session('success'))
            <x-alert type="success" dismissible autoHide :title="__('messages.success')">
                {{ session('success') }}
            </x-alert>
        @endif

        @if($errors->any())
            <x-alert type="danger" dismissible :title="__('messages.error')">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach($errors->all() as $msg)
                        <li>{{ $msg }}</li>
                    @endforeach
                </ul>
            </x-alert>
        @endif
    </div>
@endif

<x-card class="mb-6">
    <div class="flex items-center gap-2 mb-4">
        <svg class="w-5 h-5 text-text-tertiary" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
        </svg>
        <h3 class="text-lg font-semibold text-text-primary" data-translate="farmasi_new_transaction">
            {{ __('messages.farmasi_new_transaction') }}
        </h3>
    </div>

			    <div
			        x-data="rekapFarmasiForm({
			            checkUrl: @js(route('api.farmasi.consumer.today')),
	                    searchUrl: @js(route('api.farmasi.consumers.search')),
			            unitPrices: @js($unitPrices),
			            pkgA: @js($pkgAData),
			            pkgB: @js($pkgBData),
			            customPackageMap: @js($customPackageMap ?? []),
		                saved: @js(session()->has('success')),
			            locale: @js(app()->getLocale()),
                        strings: {
                            id: {
                                checking: @js(trans('messages.farmasi_checking_consumer', [], 'id')),
                                fill_consumer: @js(trans('messages.farmasi_submit_fill_consumer', [], 'id')),
                                already_today: @js(trans('messages.farmasi_consumer_already_transacted_today', [], 'id')),
                                choose_package: @js(trans('messages.farmasi_submit_choose_package', [], 'id')),
                                choose_custom_item: @js(trans('messages.farmasi_submit_choose_custom_item', [], 'id')),
                            },
                            en: {
                                checking: @js(trans('messages.farmasi_checking_consumer', [], 'en')),
                                fill_consumer: @js(trans('messages.farmasi_submit_fill_consumer', [], 'en')),
                                already_today: @js(trans('messages.farmasi_consumer_already_transacted_today', [], 'en')),
                                choose_package: @js(trans('messages.farmasi_submit_choose_package', [], 'en')),
                                choose_custom_item: @js(trans('messages.farmasi_submit_choose_custom_item', [], 'en')),
                            },
                        }
			        })"
			    >
		        <form method="post" action="{{ route('farmasi.rekap.store') }}" class="space-y-4" x-ref="txForm" @submit.prevent="submitForm()">
		            @csrf
	                <input type="hidden" name="auto_merge" x-model="autoMerge">
	                <input type="hidden" name="merge_targets" :value="mergeTargetsJson">

		            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
	                <div class="relative" @click.outside="consumerDropdownOpen = false">
	                    <x-input
	                        name="consumer_name"
	                        :label="__('messages.consumer_name')"
	                        dataTranslateLabel="consumer_name"
	                        placeholder=" "
	                        x-model="consumerName"
	                        x-ref="consumerInput"
	                        @input="searchConsumers($event.target.value)"
	                        @keydown.arrow-down.prevent="highlightNext()"
	                        @keydown.arrow-up.prevent="highlightPrev()"
	                        @keydown.enter.prevent="selectHighlighted()"
	                        @focus="consumerDropdownOpen = consumerResults.length > 0"
	                        autocomplete="off"
	                        :value="old('consumer_name')"
	                    />

                        <div
                            x-show="consumerDropdownOpen"
                            x-transition
                            x-cloak
                            class="absolute z-50 w-full mt-2 max-h-72 overflow-auto rounded-xl border border-border bg-surface shadow-lg"
                        >
                            <template x-if="consumerSearching">
                                <div class="px-4 py-3 text-sm text-text-secondary" data-translate="farmasi_consumer_searching">
                                    {{ __('messages.farmasi_consumer_searching') }}
                                </div>
                            </template>

                            <template x-if="!consumerSearching && consumerResults.length === 0">
                                <div class="px-4 py-3 text-sm text-text-secondary" data-translate="farmasi_consumer_no_results">
                                    {{ __('messages.farmasi_consumer_no_results') }}
                                </div>
                            </template>

                            <template x-for="(c, idx) in consumerResults" :key="c.name">
                                <button
                                    type="button"
                                    class="w-full text-left px-4 py-3 border-b border-border last:border-b-0 hover:bg-surface-hover transition-colors"
                                    :class="idx === consumerHighlighted ? 'bg-surface-hover' : ''"
                                    @click="selectConsumer(c)"
                                >
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0">
                                            <div class="text-sm font-semibold text-text-primary truncate" x-text="c.name"></div>
                                            <div class="mt-2 grid grid-cols-4 gap-2 text-[11px] leading-4 text-text-secondary">
                                                <div class="inline-flex items-center justify-between gap-2 rounded-lg border border-border bg-surface px-2 py-1 whitespace-nowrap">
                                                    <span class="text-text-tertiary">Tx</span>
                                                    <span class="font-semibold text-text-primary" x-text="c.total_transactions"></span>
                                                </div>
                                                <div class="inline-flex items-center justify-between gap-2 rounded-lg border border-border bg-surface px-2 py-1 whitespace-nowrap">
                                                    <span class="text-text-tertiary">B</span>
                                                    <span class="font-semibold text-text-primary" x-text="c.total_bandage"></span>
                                                </div>
                                                <div class="inline-flex items-center justify-between gap-2 rounded-lg border border-border bg-surface px-2 py-1 whitespace-nowrap">
                                                    <span class="text-text-tertiary">I</span>
                                                    <span class="font-semibold text-text-primary" x-text="c.total_ifaks"></span>
                                                </div>
                                                <div class="inline-flex items-center justify-between gap-2 rounded-lg border border-border bg-surface px-2 py-1 whitespace-nowrap">
                                                    <span class="text-text-tertiary">P</span>
                                                    <span class="font-semibold text-text-primary" x-text="c.total_painkiller"></span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="shrink-0 text-right">
                                            <div class="inline-flex items-center gap-1 text-[11px] text-text-tertiary whitespace-nowrap">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                <span data-translate="farmasi_last_purchase">{{ __('messages.farmasi_last_purchase') }}</span>
                                            </div>
                                            <div class="text-[11px] text-text-secondary whitespace-nowrap" x-text="formatLastPurchase(c)"></div>
                                        </div>
                                    </div>
                                </button>
                            </template>
                        </div>

	                    <p class="mt-2 text-xs text-text-secondary" data-translate="farmasi_consumer_hint">
	                        {{ __('messages.farmasi_consumer_hint') }}
	                    </p>
	                </div>

	                <div>
	                    <p class="text-sm font-medium text-text-primary mb-2" data-translate="farmasi_package_type">
	                        {{ __('messages.farmasi_package_type') }}
	                    </p>
	
	                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
	                        <label class="flex items-center gap-2 p-3 rounded-xl border border-border bg-surface hover:bg-surface-hover transition-colors"
	                               :class="[
                                        packageType === 'paket_a' ? 'ring-2 ring-primary/30 border-primary/40' : '',
                                        !canChoosePackage ? 'opacity-60 cursor-not-allowed' : 'cursor-pointer'
                                    ].join(' ')"
	                               :aria-disabled="!canChoosePackage"
	                               :title="!canChoosePackage ? (consumerNameHint || '') : ''"
	                               @click.prevent="setPackage('paket_a')">
	                            <input type="radio" name="package_type" value="paket_a" class="sr-only" :checked="packageType === 'paket_a'">
	                            <span class="text-sm font-semibold text-text-primary" data-translate="farmasi_paket_a">{{ __('messages.farmasi_paket_a') }}</span>
	                        </label>
	
	                        <label class="flex items-center gap-2 p-3 rounded-xl border border-border bg-surface hover:bg-surface-hover transition-colors"
	                               :class="[
                                        packageType === 'paket_b' ? 'ring-2 ring-primary/30 border-primary/40' : '',
                                        !canChoosePackage ? 'opacity-60 cursor-not-allowed' : 'cursor-pointer'
                                    ].join(' ')"
	                               :aria-disabled="!canChoosePackage"
	                               :title="!canChoosePackage ? (consumerNameHint || '') : ''"
	                               @click.prevent="setPackage('paket_b')">
	                            <input type="radio" name="package_type" value="paket_b" class="sr-only" :checked="packageType === 'paket_b'">
	                            <span class="text-sm font-semibold text-text-primary" data-translate="farmasi_paket_b">{{ __('messages.farmasi_paket_b') }}</span>
	                        </label>
	
	                        <label class="flex items-center gap-2 p-3 rounded-xl border border-border bg-surface hover:bg-surface-hover transition-colors"
	                               :class="[
                                        packageType === 'paket_custom' ? 'ring-2 ring-primary/30 border-primary/40' : '',
                                        !canChoosePackage ? 'opacity-60 cursor-not-allowed' : 'cursor-pointer'
                                    ].join(' ')"
	                               :aria-disabled="!canChoosePackage"
	                               :title="!canChoosePackage ? (consumerNameHint || '') : ''"
	                               @click.prevent="setPackage('paket_custom')">
	                            <input type="radio" name="package_type" value="paket_custom" class="sr-only" :checked="packageType === 'paket_custom'">
	                            <span class="text-sm font-semibold text-text-primary" data-translate="farmasi_paket_custom">{{ __('messages.farmasi_paket_custom') }}</span>
	                        </label>
	                    </div>

                    <template x-if="checkingConsumer">
                        <p class="mt-2 text-xs text-text-secondary" data-translate="farmasi_checking_consumer">
                            {{ __('messages.farmasi_checking_consumer') }}
                        </p>
                    </template>

	                    <template x-if="consumerLocked">
	                        <div class="mt-3">
	                            <x-alert type="warning" :title="__('messages.warning')">
	                                <span data-translate="farmasi_consumer_already_transacted_today">{{ __('messages.farmasi_consumer_already_transacted_today') }}</span>
	                            </x-alert>
	                        </div>
	                    </template>

	                        <template x-if="similarMatches.length > 0 && consumerLocked">
	                            <div class="mt-3">
	                                <x-alert type="warning" :title="__('messages.warning')">
	                                    <div class="space-y-2">
	                                        <p data-translate="farmasi_similar_name_warning">{{ __('messages.farmasi_similar_name_warning') }}</p>
	                                        <ul class="list-disc pl-5 space-y-1">
	                                            <template x-for="m in similarMatches" :key="m.name">
	                                                <li class="text-sm">
	                                                    <span class="font-semibold text-text-primary" x-text="m.name"></span>
	                                                    <span class="text-text-tertiary" x-text="'(' + m.score + '%)'"></span>
	                                                </li>
	                                            </template>
	                                        </ul>
	                                        <div class="pt-1">
	                                            <button
	                                                type="button"
	                                                class="inline-flex items-center justify-center gap-2 font-semibold transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 bg-surface text-text-primary border border-border hover:bg-surface-hover hover:border-border-medium px-4 py-2 text-sm rounded-xl active:scale-[0.98]"
	                                                @click="openMergeModal()"
	                                                x-show="mergeTargets.length > 0"
	                                                x-cloak
	                                            >
	                                                <span class="w-4 h-4 shrink-0" aria-hidden="true">
	                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
	                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4M17 8v12m0 0l4-4m-4 4l-4-4"/>
	                                                    </svg>
	                                                </span>
	                                                <span data-translate="farmasi_merge_names">{{ __('messages.farmasi_merge_names') }}</span>
	                                            </button>
	                                        </div>
	                                    </div>
	                                </x-alert>
	                            </div>
	                        </template>

	                        <template x-if="similarMatches.length > 0 && !consumerLocked">
	                            <div class="mt-3">
	                                <x-alert type="info" :title="__('messages.warning')">
	                                    <div class="space-y-2">
	                                        <p data-translate="farmasi_similar_name_warning">{{ __('messages.farmasi_similar_name_warning') }}</p>
	                                        <ul class="list-disc pl-5 space-y-1">
	                                            <template x-for="m in similarMatches" :key="m.name">
	                                                <li class="text-sm">
	                                                    <span class="font-semibold text-text-primary" x-text="m.name"></span>
	                                                    <span class="text-text-tertiary" x-text="'(' + m.score + '%)'"></span>
	                                                </li>
	                                            </template>
	                                        </ul>
	                                        <div class="pt-1">
	                                            <button
	                                                type="button"
	                                                class="inline-flex items-center justify-center gap-2 font-semibold transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 bg-surface text-text-primary border border-border hover:bg-surface-hover hover:border-border-medium px-4 py-2 text-sm rounded-xl active:scale-[0.98]"
	                                                @click="openMergeModal()"
	                                            >
	                                                <span class="w-4 h-4 shrink-0" aria-hidden="true">
	                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
	                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4M17 8v12m0 0l4-4m-4 4l-4-4"/>
	                                                    </svg>
	                                                </span>
	                                                <span data-translate="farmasi_merge_names">{{ __('messages.farmasi_merge_names') }}</span>
	                                            </button>
	                                        </div>
	                                    </div>
	                                </x-alert>
	                            </div>
	                        </template>

                        <template x-if="!checkingConsumer && !consumerLocked && !canChoosePackage && consumerNameHint">
                            <p class="mt-2 text-xs text-text-secondary" x-text="consumerNameHint"></p>
                        </template>
	                </div>
	            </div>

	            <template x-if="packageType === 'paket_custom'">
	                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
	                    <x-select
	                        name="custom_bandage_package_id"
	                        :label="__('messages.farmasi_bandage_qty')"
	                        dataTranslateLabel="farmasi_bandage_qty"
	                        :options="$bandageOptions ?? ['' => __('messages.none')]"
	                        :value="old('custom_bandage_package_id', '')"
	                        x-model="custom.bandagePackageId"
	                        x-bind="{ disabled: (!canChoosePackage || consumerLocked || packageType !== 'paket_custom') }"
	                    />
	                    <x-select
	                        name="custom_ifaks_package_id"
	                        :label="__('messages.farmasi_ifaks_qty')"
	                        dataTranslateLabel="farmasi_ifaks_qty"
	                        :options="$ifaksOptions ?? ['' => __('messages.none')]"
	                        :value="old('custom_ifaks_package_id', '')"
	                        x-model="custom.ifaksPackageId"
	                        x-bind="{ disabled: (!canChoosePackage || consumerLocked || packageType !== 'paket_custom') }"
	                    />
	                    <x-select
	                        name="custom_painkiller_package_id"
	                        :label="__('messages.farmasi_painkiller_qty')"
	                        dataTranslateLabel="farmasi_painkiller_qty"
	                        :options="$painkillerOptions ?? ['' => __('messages.none')]"
	                        :value="old('custom_painkiller_package_id', '')"
	                        x-model="custom.painkillerPackageId"
	                        x-bind="{ disabled: (!canChoosePackage || consumerLocked || packageType !== 'paket_custom') }"
	                    />
	                </div>
	            </template>

            <div class="p-4 rounded-2xl border border-border bg-surface-alt">
                <div class="flex items-start justify-between gap-4">
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-text-primary mb-2" data-translate="farmasi_selected_items">
                            {{ __('messages.farmasi_selected_items') }}
                        </p>
                        <div class="space-y-1 text-sm text-text-secondary">
                            <p class="flex items-center justify-between gap-3">
                                <span class="truncate">
                                    <span data-translate="farmasi_bandage">{{ __('messages.farmasi_bandage') }}</span>
                                    <span class="text-text-tertiary">($ <span x-text="unit.bandage"></span>/pcs)</span>
                                </span>
                                <span class="font-semibold text-text-primary" x-text="totals.bandage"></span>
                            </p>
                            <p class="flex items-center justify-between gap-3">
                                <span class="truncate">
                                    <span data-translate="farmasi_ifaks">{{ __('messages.farmasi_ifaks') }}</span>
                                    <span class="text-text-tertiary">($ <span x-text="unit.ifaks"></span>/pcs)</span>
                                </span>
                                <span class="font-semibold text-text-primary" x-text="totals.ifaks"></span>
                            </p>
                            <p class="flex items-center justify-between gap-3">
                                <span class="truncate">
                                    <span data-translate="farmasi_painkiller">{{ __('messages.farmasi_painkiller') }}</span>
                                    <span class="text-text-tertiary">($ <span x-text="unit.painkiller"></span>/pcs)</span>
                                </span>
                                <span class="font-semibold text-text-primary" x-text="totals.painkiller"></span>
                            </p>
                            <p class="flex items-center justify-between gap-3 pt-2 border-t border-border">
                                <span data-translate="farmasi_bonus_40_est">{{ __('messages.farmasi_bonus_40_est') }}</span>
                                <span class="font-semibold text-text-primary">$ <span x-text="formatNumber(totals.bonus)"></span></span>
                            </p>
                        </div>
                    </div>

                    <div class="shrink-0 text-right">
                        <p class="text-xs text-text-secondary" data-translate="farmasi_total_pay">{{ __('messages.farmasi_total_pay') }}</p>
                        <p class="text-2xl font-extrabold text-text-primary mt-1">$ <span x-text="formatNumber(totals.price)"></span></p>
                    </div>
                </div>
            </div>

		            <div class="flex items-center gap-2">
		                <x-button
                            type="button"
                            variant="primary"
                            @click="submitForm()"
                            x-bind:class="!canSubmit ? 'opacity-60' : ''"
                            x-bind:aria-disabled="(!canSubmit).toString()"
                        >
		                    <span data-translate="save">{{ __('messages.save') }}</span>
		                </x-button>
			                <x-button type="button" variant="secondary" @click="resetForm()">
			                    <span data-translate="clear">{{ __('messages.clear') }}</span>
			                </x-button>
			            </div>

                        <template x-if="submitErrorText">
                            <p class="text-xs text-danger-600 mt-2" x-text="submitErrorText"></p>
                        </template>
		        </form>

            {{-- Merge Modal (shown on save when similar names exist) --}}
            <div
                x-show="mergeModalOpen"
                x-transition
                x-cloak
                class="fixed inset-0 z-[9997] flex items-center justify-center bg-black/50 backdrop-blur-sm p-4"
                @keydown.escape.window="closeMergeModal()"
            >
                <div class="w-full max-w-xl rounded-2xl bg-surface border border-border shadow-xl">
                    <div class="flex items-center justify-between px-6 py-4 border-b border-border">
                        <h3 class="text-lg font-semibold text-text-primary" data-translate="farmasi_merge_modal_title">
                            {{ __('messages.farmasi_merge_modal_title') }}
                        </h3>
                        <button type="button" class="p-2 rounded-xl hover:bg-surface-hover transition-colors" @click="closeMergeModal()" aria-label="Close">
                            <svg class="w-5 h-5 text-text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <div class="px-6 py-4 space-y-3">
                        <p class="text-sm text-text-secondary" data-translate="farmasi_merge_modal_desc">
                            {{ __('messages.farmasi_merge_modal_desc') }}
                        </p>

                        <div class="rounded-xl border border-border bg-surface-alt p-4">
                            <p class="text-sm font-semibold text-text-primary truncate" x-text="consumerName"></p>
                            <p class="text-xs text-text-tertiary mt-1" data-translate="farmasi_merge_modal_note">
                                {{ __('messages.farmasi_merge_modal_note') }}
                            </p>
                        </div>

                        <div class="space-y-2 max-h-56 overflow-auto pr-1">
                            <template x-for="m in mergeSelection" :key="m.name">
                                <div class="flex items-center justify-between gap-3 rounded-xl border border-border bg-surface px-3 py-2">
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium text-text-primary truncate" x-text="m.name"></p>
                                        <p class="text-xs text-text-tertiary" x-text="m.score + '%'"></p>
                                    </div>
                                    <button type="button" class="p-2 rounded-lg hover:bg-surface-hover transition-colors" @click="removeMergeTarget(m.name)" aria-label="Remove">
                                        <svg class="w-4 h-4 text-text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            </template>
                        </div>

                        <template x-if="mergeSelection.length === 0">
                            <p class="text-sm text-text-secondary" data-translate="farmasi_merge_modal_empty">
                                {{ __('messages.farmasi_merge_modal_empty') }}
                            </p>
                        </template>
                    </div>

                    <div class="flex items-center justify-end gap-2 px-6 py-4 border-t border-border">
                        <x-button type="button" variant="secondary" @click="closeMergeModal()" data-dismiss="modal">
                            <span data-translate="cancel">{{ __('messages.cancel') }}</span>
                        </x-button>
                        <x-button type="button" variant="primary" @click="confirmMergeAndSubmit()">
                            <span data-translate="farmasi_merge_modal_continue">{{ __('messages.farmasi_merge_modal_continue') }}</span>
                        </x-button>
                    </div>
                </div>
            </div>
	    </div>
</x-card>

<x-card class="mb-6">
    <div class="flex items-center gap-2 mb-4">
        <svg class="w-5 h-5 text-text-tertiary" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <h3 class="text-lg font-semibold text-text-primary" data-translate="farmasi_today_total">
            {{ __('messages.farmasi_today_total') }}
        </h3>
    </div>

    @php
        $todayTotalTrx = (int) ($todayStats?->total_transaksi ?? 0);
        $todayTotalPrice = (int) ($todayStats?->total_harga ?? 0);
        $todayBonus = (int) floor($todayTotalPrice * 0.4);
    @endphp

    <x-grid :cols="3" :gap="'default'">
        <x-stat-card :title="__('messages.farmasi_total_transactions')" data-translate-title="farmasi_total_transactions" :value="$fmt($todayTotalTrx)" />
        <x-stat-card :title="__('messages.farmasi_total_income')" data-translate-title="farmasi_total_income" :value="'$ ' . $fmt($todayTotalPrice)" />
        <x-stat-card :title="__('messages.farmasi_bonus_40')" data-translate-title="farmasi_bonus_40" :value="'$ ' . $fmt($todayBonus)" />
    </x-grid>
</x-card>

<x-card class="mb-6">
    <div class="flex items-center gap-2 mb-4">
        <svg class="w-5 h-5 text-text-tertiary" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 12a1 1 0 011-1h10a1 1 0 011 1v8a1 1 0 01-1 1H4a1 1 0 01-1-1v-8zM17 12a1 1 0 011-1h2a1 1 0 011 1v8a1 1 0 01-1 1h-2a1 1 0 01-1-1v-8z"/>
        </svg>
        <h3 class="text-lg font-semibold text-text-primary" data-translate="farmasi_filter_title">
            {{ __('messages.farmasi_filter_title') }}
        </h3>
    </div>

    <form method="get" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end" x-data="{ range: @js($range) }">
        <div>
            <x-select
                name="range"
                :label="__('messages.farmasi_date_range')"
                dataTranslateLabel="farmasi_date_range"
                x-model="range"
                :options="[
                    'today' => __('messages.range_today'),
                    'yesterday' => __('messages.range_yesterday'),
                    'last7' => __('messages.range_last7'),
                    'week1' => $weeks['week1']['start']->locale(app()->getLocale())->translatedFormat('d M') . ' - ' . $weeks['week1']['end']->locale(app()->getLocale())->translatedFormat('d M'),
                    'week2' => $weeks['week2']['start']->locale(app()->getLocale())->translatedFormat('d M') . ' - ' . $weeks['week2']['end']->locale(app()->getLocale())->translatedFormat('d M'),
                    'week3' => $weeks['week3']['start']->locale(app()->getLocale())->translatedFormat('d M') . ' - ' . $weeks['week3']['end']->locale(app()->getLocale())->translatedFormat('d M'),
                    'week4' => $weeks['week4']['start']->locale(app()->getLocale())->translatedFormat('d M') . ' - ' . $weeks['week4']['end']->locale(app()->getLocale())->translatedFormat('d M'),
                    'custom' => __('messages.range_custom'),
                ]"
                :value="$range"
            />
        </div>

        <div x-show="range === 'custom'" x-cloak>
            <x-input type="date" name="from" :label="__('messages.range_from')" dataTranslateLabel="range_from" :value="$fromInput" />
        </div>

        <div x-show="range === 'custom'" x-cloak>
            <x-input type="date" name="to" :label="__('messages.range_to')" dataTranslateLabel="range_to" :value="$toInput" />
        </div>

        <div class="md:col-span-3 flex items-center gap-2">
            @if($showAll)
                <input type="hidden" name="show_all" value="1">
            @endif
            <x-button type="submit" variant="secondary">
                <span data-translate="apply_filter">{{ __('messages.apply_filter') }}</span>
            </x-button>

            <a href="{{ route('farmasi.rekap', array_merge(request()->except('show_all'), ['show_all' => $showAll ? null : 1])) }}"
               class="inline-flex items-center justify-center px-4 py-2 rounded-xl text-sm font-semibold border border-border bg-surface hover:bg-surface-hover transition-colors">
                <span data-translate="{{ $showAll ? 'farmasi_show_mine' : 'farmasi_show_all' }}">
                    {{ $showAll ? __('messages.farmasi_show_mine') : __('messages.farmasi_show_all') }}
                </span>
            </a>
        </div>
    </form>

    <p class="text-sm text-text-secondary mt-4">
        <span data-translate="farmasi_active_range">{{ __('messages.farmasi_active_range') }}</span>:
        <span class="font-semibold text-text-primary">{{ $rangeLabel }}</span>
    </p>
</x-card>

<x-card>
    <div class="flex items-center gap-2 mb-4">
        <svg class="w-5 h-5 text-text-tertiary" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5l5 5v11a2 2 0 01-2 2z"/>
        </svg>
        <h3 class="text-lg font-semibold text-text-primary" data-translate="farmasi_transactions_filtered">
            {{ __('messages.farmasi_transactions_filtered') }}
        </h3>
    </div>

    <x-table
        :headers="[
            ['label' => __('messages.time'), 'key' => 'time'],
            ['label' => __('messages.consumer_name'), 'key' => 'consumer'],
            ['label' => __('messages.package'), 'key' => 'package'],
            ['label' => __('messages.farmasi_bandage'), 'key' => 'bandage'],
            ['label' => __('messages.farmasi_ifaks'), 'key' => 'ifaks'],
            ['label' => __('messages.farmasi_painkiller'), 'key' => 'painkiller'],
            ['label' => __('messages.price'), 'key' => 'price'],
            ['label' => __('messages.farmasi_bonus_40'), 'key' => 'bonus'],
        ]"
        striped
        bordered
        compact
    >
        @foreach($sales as $s)
            @php
                $bonus = (int) floor(((int) $s->price) * 0.4);
                $dt = \Carbon\Carbon::parse($s->created_at)->locale(app()->getLocale());
                $timeText = $dt->translatedFormat('d M Y H:i');
            @endphp
            <tr>
                <td class="px-4 py-2 text-sm text-text-primary whitespace-nowrap">{{ $timeText }}</td>
                <td class="px-4 py-2 text-sm text-text-primary">{{ $s->consumer_name }}</td>
                <td class="px-4 py-2 text-sm text-text-secondary">{{ $s->package_name }}</td>
                <td class="px-4 py-2 text-sm text-text-secondary text-right">{{ (int) $s->qty_bandage }}</td>
                <td class="px-4 py-2 text-sm text-text-secondary text-right">{{ (int) $s->qty_ifaks }}</td>
                <td class="px-4 py-2 text-sm text-text-secondary text-right">{{ (int) $s->qty_painkiller }}</td>
                <td class="px-4 py-2 text-sm text-text-primary whitespace-nowrap">$ {{ $fmt((int) $s->price) }}</td>
                <td class="px-4 py-2 text-sm text-text-primary whitespace-nowrap">$ {{ $fmt($bonus) }}</td>
            </tr>
        @endforeach
    </x-table>
</x-card>

@push('scripts')
<script>
    function rekapFarmasiForm(config) {
        const toInt = (v) => {
            const n = Number.parseInt(v ?? 0, 10);
            return Number.isFinite(n) ? n : 0;
        };

        const STORAGE_KEY = 'rhmc_rekap_farmasi_new_tx';

	        return {
	            consumerName: '',
	            checkingConsumer: false,
	            consumerLocked: false,
	            canChoosePackage: false,
	            packageType: '',
	            custom: { bandagePackageId: '', ifaksPackageId: '', painkillerPackageId: '' },
	            autoMerge: '0',
	            similarMatches: [],
	            mergeTargets: [],
	            mergeModalOpen: false,
	            mergeSelection: [],
	            pendingSubmit: false,
                submitErrorText: '',
		            lastCheckedName: '',
		            hydrating: false,
	                consumerResults: [],
	                consumerDropdownOpen: false,
	                consumerHighlighted: -1,
                consumerSearching: false,
                consumerSearchTimeout: null,
	            unit: {
	                bandage: toInt(config?.unitPrices?.bandage),
	                ifaks: toInt(config?.unitPrices?.ifaks),
	                painkiller: toInt(config?.unitPrices?.painkiller),
	            },
            pkgA: config?.pkgA || null,
            pkgB: config?.pkgB || null,
            customPackageMap: config?.customPackageMap || {},
            totals: { bandage: 0, ifaks: 0, painkiller: 0, price: 0, bonus: 0 },

            persist() {
                const payload = {
                    consumerName: (this.consumerName || ''),
                    packageType: (this.packageType || ''),
                    custom: {
                        bandagePackageId: (this.custom.bandagePackageId || ''),
                        ifaksPackageId: (this.custom.ifaksPackageId || ''),
                        painkillerPackageId: (this.custom.painkillerPackageId || ''),
                    }
                };
                try {
                    localStorage.setItem(STORAGE_KEY, JSON.stringify(payload));
                } catch (e) {
                    // ignore
                }
            },

            loadPersisted() {
                try {
                    const raw = localStorage.getItem(STORAGE_KEY);
                    if (!raw) return null;
                    const parsed = JSON.parse(raw);
                    return parsed && typeof parsed === 'object' ? parsed : null;
                } catch (e) {
                    return null;
                }
            },

            clearPersisted() {
                try {
                    localStorage.removeItem(STORAGE_KEY);
                } catch (e) {
                    // ignore
                }
            },

            get mergeTargetsJson() {
                try {
                    return JSON.stringify(this.mergeTargets || []);
                } catch (e) {
                    return '[]';
                }
            },

	            get consumerNameHint() {
	                const len = (this.consumerName || '').trim().length;
	                if (len < 2) return config?.locale === 'id'
	                    ? 'Mohon isi terlebih dahulu nama konsumen.'
	                    : 'Please fill in the consumer name first.';
	                return '';
	            },

	            get canSubmit() {
	                if ((this.consumerName || '').trim().length < 2) return false;
	                if (this.consumerLocked) return false;
	                if (!this.packageType) return false;
	                if (this.packageType === 'paket_custom') {
	                    return !!(this.custom.bandagePackageId || this.custom.ifaksPackageId || this.custom.painkillerPackageId);
	                }
	                if (this.packageType === 'paket_a') return !!this.pkgA;
	                if (this.packageType === 'paket_b') return !!this.pkgB;
	                return true;
	            },

                msg(key) {
                    const lang = window.globalLangState?.currentLang || config?.locale || 'id';
                    const table = config?.strings?.[lang] || config?.strings?.[config?.locale] || {};
                    return table?.[key] || '';
                },

	            openMergeModal() {
	                const list = Array.isArray(this.similarMatches) ? this.similarMatches : [];
	                if (list.length === 0) return;

	                this.mergeSelection = list
	                    .filter((m) => m && typeof m.name === 'string' && m.name.trim() !== '')
	                    .map((m) => ({ name: m.name, score: Number.parseInt(m.score ?? 0, 10) || 0 }));

	                this.mergeModalOpen = true;
	                this.pendingSubmit = true;
	            },

	            closeMergeModal() {
	                this.mergeModalOpen = false;
	                this.pendingSubmit = false;
	                this.mergeSelection = [];
	            },

	            removeMergeTarget(name) {
	                const n = String(name || '').trim();
	                if (!n) return;
	                this.mergeSelection = (this.mergeSelection || []).filter((m) => m?.name !== n);
	            },

	            confirmMergeAndSubmit() {
	                const targets = (this.mergeSelection || [])
	                    .map((m) => String(m?.name || '').trim())
	                    .filter((n) => n !== '');

	                this.mergeTargets = targets;
	                this.autoMerge = targets.length > 0 ? '1' : '0';
	                this.mergeModalOpen = false;
	                this.pendingSubmit = false;

	                this.$nextTick(() => {
	                    this.$refs.txForm?.submit();
	                });
	            },

		            submitForm() {
		                this.submitErrorText = '';

                        if (this.checkingConsumer) {
                            this.submitErrorText = this.msg('checking') || '';
                            return;
                        }

                        const nameLen = (this.consumerName || '').trim().length;
                        if (nameLen < 2) {
                            this.submitErrorText = this.msg('fill_consumer') || '';
                            return;
                        }

                        if (this.consumerLocked) {
                            this.submitErrorText = this.msg('already_today') || '';
                            return;
                        }

                        if (!this.packageType) {
                            this.submitErrorText = this.msg('choose_package') || '';
                            return;
                        }

                        if (this.packageType === 'paket_custom') {
                            const hasAny = !!(this.custom.bandagePackageId || this.custom.ifaksPackageId || this.custom.painkillerPackageId);
                            if (!hasAny) {
                                this.submitErrorText = this.msg('choose_custom_item') || '';
                                return;
                            }
                        }

		                this.$nextTick(() => this.$refs.txForm?.submit());
		            },

		            formatNumber(num) {
		                const n = toInt(num);
		                try {
		                    return new Intl.NumberFormat(config?.locale === 'id' ? 'id-ID' : 'en-US').format(n);
		                } catch (e) {
	                    return String(n);
	                }
	            },

                formatLastPurchase(c) {
                    if (!c) return '';
                    const lang = window.globalLangState?.currentLang || config?.locale || 'id';
                    return lang === 'id' ? (c.last_purchase_id || '') : (c.last_purchase_en || '');
                },

                searchConsumers(query) {
                    const q = String(query || '').trim();
                    if (q.length < 2) {
                        this.consumerResults = [];
                        this.consumerDropdownOpen = false;
                        this.consumerSearching = false;
                        this.consumerHighlighted = -1;
                        if (this.consumerSearchTimeout) clearTimeout(this.consumerSearchTimeout);
                        return;
                    }

                    if (this.consumerSearchTimeout) clearTimeout(this.consumerSearchTimeout);
                    this.consumerDropdownOpen = true;
                    this.consumerSearching = true;

                    this.consumerSearchTimeout = setTimeout(async () => {
                        try {
                            const url = new URL(config.searchUrl, window.location.origin);
                            url.searchParams.set('q', q);
                            const res = await fetch(url.toString(), { headers: { 'Accept': 'application/json' }, cache: 'no-store' });
                            const json = await res.json();
                            this.consumerResults = Array.isArray(json.results) ? json.results : [];
                            this.consumerDropdownOpen = this.consumerResults.length > 0 || this.consumerSearching;
                            this.consumerHighlighted = this.consumerResults.length > 0 ? 0 : -1;
                        } catch (e) {
                            this.consumerResults = [];
                            this.consumerDropdownOpen = false;
                        } finally {
                            this.consumerSearching = false;
                            this.consumerDropdownOpen = this.consumerResults.length > 0;
                        }
                    }, 250);
                },

                selectConsumer(c) {
                    if (!c) return;
                    this.consumerName = c.name;
                    this.consumerDropdownOpen = false;
                    this.consumerResults = [];
                    this.consumerHighlighted = -1;
                    this.persist();
                    this.checkConsumer();
                    this.$nextTick(() => this.$refs.consumerInput?.focus());
                },

                highlightNext() {
                    if (!this.consumerDropdownOpen || this.consumerResults.length === 0) return;
                    this.consumerHighlighted = (this.consumerHighlighted + 1) % this.consumerResults.length;
                },

                highlightPrev() {
                    if (!this.consumerDropdownOpen || this.consumerResults.length === 0) return;
                    this.consumerHighlighted = this.consumerHighlighted <= 0
                        ? this.consumerResults.length - 1
                        : this.consumerHighlighted - 1;
                },

                selectHighlighted() {
                    if (!this.consumerDropdownOpen) return;
                    if (this.consumerHighlighted < 0 || this.consumerHighlighted >= this.consumerResults.length) return;
                    this.selectConsumer(this.consumerResults[this.consumerHighlighted]);
                },

	            resetForm() {
	                this.consumerName = '';
	                this.consumerLocked = false;
	                this.canChoosePackage = false;
	                this.packageType = '';
	                this.custom.bandagePackageId = '';
	                this.custom.ifaksPackageId = '';
	                this.custom.painkillerPackageId = '';
                    this.consumerResults = [];
                    this.consumerDropdownOpen = false;
                    this.consumerHighlighted = -1;
                    this.consumerSearching = false;
	                this.recalc();
	                this.clearPersisted();
	            },

            setPackage(type) {
	                if (!this.canChoosePackage) return;
	                if (this.consumerLocked) return;
	                this.packageType = type;

	                if (type === 'paket_custom') {
	                    this.custom.bandagePackageId = '';
	                    this.custom.ifaksPackageId = '';
	                    this.custom.painkillerPackageId = '';
	                }
	                this.recalc();
	                this.persist();
	            },

            recalc() {
                let bandage = 0;
                let ifaks = 0;
                let painkiller = 0;
                let price = 0;

                if (this.packageType === 'paket_a' && this.pkgA) {
                    bandage = toInt(this.pkgA.bandage);
                    ifaks = toInt(this.pkgA.ifaks);
                    painkiller = toInt(this.pkgA.painkiller);
                    price = toInt(this.pkgA.price);
                } else if (this.packageType === 'paket_b' && this.pkgB) {
                    bandage = toInt(this.pkgB.bandage);
                    ifaks = toInt(this.pkgB.ifaks);
                    painkiller = toInt(this.pkgB.painkiller);
                    price = toInt(this.pkgB.price);
                } else if (this.packageType === 'paket_custom') {
                    const ids = [this.custom.bandagePackageId, this.custom.ifaksPackageId, this.custom.painkillerPackageId]
                        .filter(Boolean);

                    ids.forEach((id) => {
                        const p = this.customPackageMap[id];
                        if (!p) return;
                        price += toInt(p.price);
                        if (p.item === 'bandage') bandage += toInt(p.qty);
                        if (p.item === 'ifaks') ifaks += toInt(p.qty);
                        if (p.item === 'painkiller') painkiller += toInt(p.qty);
                    });
                }

                this.totals.bandage = bandage;
                this.totals.ifaks = ifaks;
                this.totals.painkiller = painkiller;
                this.totals.price = price;
                this.totals.bonus = Math.floor(price * 0.4);
            },

	            async checkConsumer() {
	                const name = (this.consumerName || '').trim();
	                const normalizedName = name.toLowerCase().replace(/\s+/g, ' ').trim();
	                const nameChanged = this.lastCheckedName !== '' && normalizedName !== this.lastCheckedName;

	                this.consumerLocked = false;
	                this.canChoosePackage = name.length >= 2;
	                this.similarMatches = [];
	                this.mergeTargets = [];
	                this.autoMerge = '0';

	                if (name.length < 2) {
	                    this.canChoosePackage = false;
	                    this.packageType = '';
	                    this.custom.bandagePackageId = '';
	                    this.custom.ifaksPackageId = '';
	                    this.custom.painkillerPackageId = '';
	                    this.lastCheckedName = '';
                    this.recalc();
                    this.persist();
                    return;
                }

                this.checkingConsumer = true;
                try {
                    const url = new URL(config.checkUrl, window.location.origin);
                    url.searchParams.set('name', name);
                    const res = await fetch(url.toString(), { headers: { 'Accept': 'application/json' }, cache: 'no-store' });
                    const json = await res.json();
	                    this.consumerLocked = !!json.already;
	                    this.canChoosePackage = !this.consumerLocked;
	                    this.similarMatches = Array.isArray(json.similar) ? json.similar : [];
	                    this.mergeTargets = Array.isArray(json.merge_targets) ? json.merge_targets : [];

		                    if (this.consumerLocked) {
		                        // Locked consumers must not keep package selections.
		                        this.packageType = '';
		                        this.custom.bandagePackageId = '';
		                        this.custom.ifaksPackageId = '';
		                        this.custom.painkillerPackageId = '';
		                        this.autoMerge = '0';
		                        this.mergeSelection = [];
		                        this.mergeModalOpen = false;
		                    } else if (nameChanged && !this.hydrating) {
	                        // If user changes the name, force reselect packages to avoid mismatched draft.
	                        this.packageType = '';
	                        this.custom.bandagePackageId = '';
	                        this.custom.ifaksPackageId = '';
	                        this.custom.painkillerPackageId = '';
	                        this.autoMerge = '0';
	                        this.mergeTargets = [];
	                        this.mergeSelection = [];
	                        this.mergeModalOpen = false;
	                    }

                    this.lastCheckedName = normalizedName;
                    this.recalc();
                    this.persist();
                } catch (e) {
                    this.canChoosePackage = true;
                    this.lastCheckedName = normalizedName;
                    this.recalc();
                    this.persist();
                } finally {
                    this.checkingConsumer = false;
                    this.hydrating = false;
                }
            },

            async mergeSimilarNames() {
                if (!this.mergeTargets || this.mergeTargets.length === 0) return;
                const name = (this.consumerName || '').trim();
                if (name.length < 2) return;

                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

                try {
                    const res = await fetch(@js(route('api.farmasi.consumer.merge')), {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            ...(token ? { 'X-CSRF-TOKEN': token } : {}),
                        },
                        body: JSON.stringify({ name, targets: this.mergeTargets }),
                    });

                    if (!res.ok) return;
                    const json = await res.json();
                    if (!json?.success) return;

                    // Enable auto merge on submit too, so if user saves after merge, it's consistent.
                    this.autoMerge = '1';

                    // Refresh status (will lock if already today).
                    await this.checkConsumer();
                } catch (e) {
                    // ignore
                }
            },

            init() {
                this.hydrating = true;

                // If last action saved successfully, clear persisted draft.
                if (config?.saved) {
                    this.clearPersisted();
                }

                // Prefer server old() values only when present (validation error flow).
                // Otherwise restore from localStorage so refresh/offline keeps draft.
                const server = {
                    consumerName: @js(old('consumer_name', '')),
                    packageType: @js(old('package_type', '')),
                    bandagePackageId: String(@js(old('custom_bandage_package_id', '')) || ''),
                    ifaksPackageId: String(@js(old('custom_ifaks_package_id', '')) || ''),
                    painkillerPackageId: String(@js(old('custom_painkiller_package_id', '')) || ''),
                };

                const hasServerOld =
                    (server.consumerName || '').trim() !== '' ||
                    (server.packageType || '').trim() !== '' ||
                    server.bandagePackageId !== '' ||
                    server.ifaksPackageId !== '' ||
                    server.painkillerPackageId !== '';

                const draft = this.loadPersisted();
                const source = hasServerOld ? server : (draft || {});

                this.consumerName = String(source.consumerName || '');
                this.packageType = String(source.packageType || '');
                this.custom.bandagePackageId = String(source.custom?.bandagePackageId || source.bandagePackageId || '');
                this.custom.ifaksPackageId = String(source.custom?.ifaksPackageId || source.ifaksPackageId || '');
                this.custom.painkillerPackageId = String(source.custom?.painkillerPackageId || source.painkillerPackageId || '');

                const nameLen = (this.consumerName || '').trim().length;
                this.canChoosePackage = nameLen >= 2;
                if (!this.canChoosePackage) {
                    this.packageType = '';
                    this.custom.bandagePackageId = '';
                    this.custom.ifaksPackageId = '';
                    this.custom.painkillerPackageId = '';
                }

                this.recalc();
                this.persist();

                let t = null;
                this.$watch('consumerName', () => {
                    if (t) clearTimeout(t);
                    t = setTimeout(() => this.checkConsumer(), 300);
                    this.persist();
                });

                this.$watch('custom.bandagePackageId', () => this.recalc());
                this.$watch('custom.ifaksPackageId', () => this.recalc());
                this.$watch('custom.painkillerPackageId', () => this.recalc());
                this.$watch('custom.bandagePackageId', () => this.persist());
                this.$watch('custom.ifaksPackageId', () => this.persist());
                this.$watch('custom.painkillerPackageId', () => this.persist());

                this.$watch('packageType', () => this.persist());

                if ((this.consumerName || '').trim().length >= 2) {
                    this.checkConsumer();
                } else {
                    this.hydrating = false;
                }
            }
        };
    }
</script>
@endpush
@endsection
