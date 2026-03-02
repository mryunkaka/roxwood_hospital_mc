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
		                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
			                    <x-select
			                        name="custom_bandage_package_id"
			                        :label="__('messages.farmasi_bandage_qty')"
			                        dataTranslateLabel="farmasi_bandage_qty"
			                        :options="$bandageOptions ?? ['' => __('messages.none')]"
			                        :value="old('custom_bandage_package_id', '')"
			                        x-model="custom.bandagePackageId"
			                        x-bind:disabled="checkingConsumer || consumerLocked"
			                    />
			                    <x-select
			                        name="custom_ifaks_package_id"
			                        :label="__('messages.farmasi_ifaks_qty')"
			                        dataTranslateLabel="farmasi_ifaks_qty"
			                        :options="$ifaksOptions ?? ['' => __('messages.none')]"
			                        :value="old('custom_ifaks_package_id', '')"
			                        x-model="custom.ifaksPackageId"
			                        x-bind:disabled="checkingConsumer || consumerLocked"
			                    />
			                    <x-select
			                        name="custom_painkiller_package_id"
			                        :label="__('messages.farmasi_painkiller_qty')"
			                        dataTranslateLabel="farmasi_painkiller_qty"
			                        :options="$painkillerOptions ?? ['' => __('messages.none')]"
			                        :value="old('custom_painkiller_package_id', '')"
			                        x-model="custom.painkillerPackageId"
			                        x-bind:disabled="checkingConsumer || consumerLocked"
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
	
	    <div
	        x-data="rekapFarmasiTodayStats({
	            locale: @js(app()->getLocale()),
	            currentUserId: @js((int) (session('user')['id'] ?? 0)),
	            totalTrx: @js($todayTotalTrx),
	            totalPrice: @js($todayTotalPrice),
	        })"
	        x-cloak
	    >
	        <x-grid :cols="3" :gap="'default'">
	            <x-stat-card :title="__('messages.farmasi_total_transactions')" data-translate-title="farmasi_total_transactions" :value="$fmt($todayTotalTrx)">
	                <span x-text="formatNumber(totalTrx)"></span>
	            </x-stat-card>
	            <x-stat-card :title="__('messages.farmasi_total_income')" data-translate-title="farmasi_total_income" :value="'$ ' . $fmt($todayTotalPrice)">
	                <span x-text="formatMoney(totalPrice)"></span>
	            </x-stat-card>
	            <x-stat-card :title="__('messages.farmasi_bonus_40')" data-translate-title="farmasi_bonus_40" :value="'$ ' . $fmt($todayBonus)">
	                <span x-text="formatMoney(bonus)"></span>
	            </x-stat-card>
	        </x-grid>
	    </div>
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

	    @php
	        $currentUserId = (int) (session('user')['id'] ?? 0);
	        $salesRows = $sales->map(function ($s) use ($fmt) {
	            $price = (int) ($s->price ?? 0);
	            $bonus = (int) floor($price * 0.4);
	            $dt = \Carbon\Carbon::parse($s->created_at)->locale(app()->getLocale());
	            $isToday = $dt->isSameDay(\Carbon\Carbon::today());
	            return [
	                'id' => (int) $s->id,
	                'createdAt' => (string) $s->created_at,
	                'createdAtTs' => (int) $dt->timestamp,
	                'timeText' => (string) $dt->translatedFormat('d M Y H:i'),
	                'consumer' => (string) ($s->consumer_name ?? ''),
	                'package' => (string) ($s->package_name ?? ''),
	                'bandage' => (int) ($s->qty_bandage ?? 0),
	                'ifaks' => (int) ($s->qty_ifaks ?? 0),
	                'painkiller' => (int) ($s->qty_painkiller ?? 0),
	                'price' => $price,
	                'priceText' => '$ ' . $fmt($price),
	                'bonus' => $bonus,
	                'bonusText' => '$ ' . $fmt($bonus),
	                'medicUserId' => (int) ($s->medic_user_id ?? 0),
	                'isToday' => (bool) $isToday,
	            ];
	        })->values();
	    @endphp

		    <div
		        x-data="rekapFarmasiTable({
		            rows: @js($salesRows),
		            currentUserId: @js($currentUserId),
		            currentUserName: @js((string) (session('user')['name'] ?? '')),
		            locale: @js(app()->getLocale()),
		            destroyUrlTemplate: @js(route('farmasi.rekap.sales.destroy', ['sale' => 0])),
		            bulkDestroyUrl: @js(route('farmasi.rekap.sales.bulk_destroy')),
		        })"
		        x-cloak
	        class="space-y-3"
	    >
	        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
	            <div class="flex flex-col sm:flex-row sm:items-center gap-2">
	                <div class="relative w-full sm:w-72">
	                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
	                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
	                    </svg>
	                    <input
	                        type="text"
	                        class="w-full pl-9 pr-4 py-2 rounded-xl bg-surface border border-border focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none transition-all text-sm placeholder:text-text-hint"
	                        placeholder="{{ __('messages.farmasi_table_search_placeholder') }}"
	                        data-translate-placeholder="farmasi_table_search_placeholder"
	                        x-model.trim="search"
	                        @input.debounce.150ms="page = 1"
	                    >
	                </div>

	                <div class="flex items-center gap-2">
	                    <span class="text-xs text-text-secondary" data-translate="farmasi_show">{{ __('messages.farmasi_show') }}</span>
	                    <select
	                        class="rounded-xl bg-surface border border-border px-3 py-2 text-sm outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20"
	                        x-model.number="pageSize"
	                        @change="page = 1"
	                    >
	                        <option :value="10">10</option>
	                        <option :value="25">25</option>
	                        <option :value="50">50</option>
	                        <option :value="100">100</option>
	                    </select>
	                    <span class="text-xs text-text-secondary" data-translate="farmasi_rows">{{ __('messages.farmasi_rows') }}</span>
	                </div>
	            </div>

	            <div class="flex flex-col sm:flex-row sm:items-center gap-2">
	                <button
	                    type="button"
	                    class="inline-flex items-center justify-center gap-2 font-semibold transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 bg-surface text-text-primary border border-border hover:bg-surface-hover hover:border-border-medium px-4 py-2 text-sm rounded-xl active:scale-[0.98]"
	                    @click="exportTxt()"
	                >
	                    <span class="w-4 h-4 shrink-0" aria-hidden="true">
	                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
	                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v12m0 0l-3-3m3 3l3-3M4 17v3a1 1 0 001 1h14a1 1 0 001-1v-3"/>
	                        </svg>
	                    </span>
		                    <span>
		                        <span data-translate="export">{{ __('messages.export') }}</span>
		                        <span> TXT</span>
		                    </span>
		                </button>

	                <button
	                    type="button"
	                    class="inline-flex items-center justify-center gap-2 font-semibold transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 bg-danger-500 text-white border border-danger-500 hover:bg-danger-600 px-4 py-2 text-sm rounded-xl active:scale-[0.98] disabled:opacity-60 disabled:cursor-not-allowed"
	                    :disabled="selectedIds.length === 0"
	                    @click="deleteSelected()"
	                >
	                    <span class="w-4 h-4 shrink-0" aria-hidden="true">
	                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
	                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7h6m2 0H7m3-3h4a1 1 0 011 1v2H9V5a1 1 0 011-1z"/>
	                        </svg>
	                    </span>
		                    <span>
		                        <span data-translate="delete">{{ __('messages.delete') }}</span>
		                        <span x-text="' (' + selectedIds.length + ')'"></span>
		                    </span>
		                </button>
	            </div>
	        </div>

	        <div class="overflow-x-auto rounded-lg border border-border">
	            <table class="w-full text-left border-collapse border border-border divide-y divide-border">
	                <thead class="bg-surface">
	                    <tr>
	                        <th class="px-4 py-2 text-xs font-semibold tracking-wide text-text-secondary uppercase">
	                            <input type="checkbox" class="rounded border-border" :checked="allOnPageSelected" @change="toggleAllOnPage($event.target.checked)">
	                        </th>
		                        <th class="px-4 py-2 text-xs font-semibold tracking-wide text-text-secondary uppercase">
		                            <span data-translate="time">{{ __('messages.time') }}</span>
		                        </th>
		                        <th class="px-4 py-2 text-xs font-semibold tracking-wide text-text-secondary uppercase">
		                            <span data-translate="consumer_name">{{ __('messages.consumer_name') }}</span>
		                        </th>
		                        <th class="px-4 py-2 text-xs font-semibold tracking-wide text-text-secondary uppercase">
		                            <span data-translate="package">{{ __('messages.package') }}</span>
		                        </th>
		                        <th class="px-4 py-2 text-xs font-semibold tracking-wide text-text-secondary uppercase text-right">
		                            <span data-translate="farmasi_bandage">{{ __('messages.farmasi_bandage') }}</span>
		                        </th>
		                        <th class="px-4 py-2 text-xs font-semibold tracking-wide text-text-secondary uppercase text-right">
		                            <span data-translate="farmasi_ifaks">{{ __('messages.farmasi_ifaks') }}</span>
		                        </th>
		                        <th class="px-4 py-2 text-xs font-semibold tracking-wide text-text-secondary uppercase text-right">
		                            <span data-translate="farmasi_painkiller">{{ __('messages.farmasi_painkiller') }}</span>
		                        </th>
		                        <th class="px-4 py-2 text-xs font-semibold tracking-wide text-text-secondary uppercase text-right">
		                            <span data-translate="price">{{ __('messages.price') }}</span>
		                        </th>
		                        <th class="px-4 py-2 text-xs font-semibold tracking-wide text-text-secondary uppercase text-right">
		                            <span data-translate="farmasi_bonus_40">{{ __('messages.farmasi_bonus_40') }}</span>
		                        </th>
		                        <th class="px-4 py-2 text-xs font-semibold tracking-wide text-text-secondary uppercase text-right">
		                            <span data-translate="actions">{{ __('messages.actions') }}</span>
		                        </th>
		                    </tr>
		                </thead>

	                <tbody class="divide-y divide-border">
	                    <template x-if="pageRows.length === 0">
	                        <tr>
	                            <td colspan="10" class="px-4 py-10 text-center text-text-secondary">
	                                {{ __('messages.no_data') }}
	                            </td>
	                        </tr>
	                    </template>

	                    <template x-for="row in pageRows" :key="row.id">
	                        <tr class="hover:bg-surface-hover transition-colors">
	                            <td class="px-4 py-2 text-sm text-text-primary">
	                                <input
	                                    type="checkbox"
	                                    class="rounded border-border"
		                                    :checked="isSelected(row.id)"
		                                    :disabled="row.medicUserId !== currentUserId"
		                                    @change="toggleOne(row.id, $event.target.checked)"
		                                    :title="row.medicUserId !== currentUserId ? t('farmasi_cannot_select_other', 'Cannot select other user transactions') : ''"
		                                >
	                            </td>
	                            <td class="px-4 py-2 text-sm text-text-primary whitespace-nowrap" x-text="row.timeText"></td>
	                            <td class="px-4 py-2 text-sm text-text-primary" x-text="row.consumer"></td>
	                            <td class="px-4 py-2 text-sm text-text-secondary" x-text="row.package"></td>
	                            <td class="px-4 py-2 text-sm text-text-secondary text-right" x-text="row.bandage"></td>
	                            <td class="px-4 py-2 text-sm text-text-secondary text-right" x-text="row.ifaks"></td>
	                            <td class="px-4 py-2 text-sm text-text-secondary text-right" x-text="row.painkiller"></td>
	                            <td class="px-4 py-2 text-sm text-text-primary whitespace-nowrap text-right" x-text="row.priceText"></td>
	                            <td class="px-4 py-2 text-sm text-text-primary whitespace-nowrap text-right" x-text="row.bonusText"></td>
	                            <td class="px-4 py-2 text-sm text-right">
	                                <button
	                                    type="button"
		                                    class="inline-flex items-center justify-center px-3 py-1.5 text-xs font-semibold rounded-lg border border-border hover:bg-surface-hover transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
		                                    :disabled="row.medicUserId !== currentUserId"
		                                    @click="deleteOne(row.id)"
		                                    :title="row.medicUserId !== currentUserId ? t('farmasi_cannot_delete_other', 'Cannot delete other user transactions') : ''"
		                                >
		                                    <span data-translate="delete">{{ __('messages.delete') }}</span>
		                                </button>
	                            </td>
	                        </tr>
	                    </template>
	                </tbody>

		                <tfoot class="bg-surface-alt border-t border-border">
		                    <tr>
		                        <td colspan="4" class="px-4 py-2 text-sm font-semibold text-text-primary">
		                            <span data-translate="farmasi_total_shown">{{ __('messages.farmasi_total_shown') }}</span>
		                        </td>
	                        <td class="px-4 py-2 text-sm font-semibold text-text-primary text-right" x-text="footerTotals.bandage"></td>
	                        <td class="px-4 py-2 text-sm font-semibold text-text-primary text-right" x-text="footerTotals.ifaks"></td>
	                        <td class="px-4 py-2 text-sm font-semibold text-text-primary text-right" x-text="footerTotals.painkiller"></td>
	                        <td class="px-4 py-2 text-sm font-semibold text-text-primary text-right whitespace-nowrap" x-text="formatMoney(footerTotals.price)"></td>
	                        <td class="px-4 py-2 text-sm font-semibold text-text-primary text-right whitespace-nowrap" x-text="formatMoney(footerTotals.bonus)"></td>
	                        <td class="px-4 py-2"></td>
	                    </tr>
	                </tfoot>
	            </table>
	        </div>

		        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 text-sm text-text-secondary">
		            <div>
		                <span data-translate="showing">{{ __('messages.showing') }}</span>
		                <span class="font-semibold text-text-primary" x-text="pageRows.length"></span>
		                <span data-translate="of">{{ __('messages.of') }}</span>
		                <span class="font-semibold text-text-primary" x-text="filteredRows.length"></span>
		                <span data-translate="results">{{ __('messages.results') }}</span>
		            </div>
		            <div class="flex items-center gap-2">
		                <button
		                    type="button"
	                    class="px-3 py-2 rounded-xl border border-border hover:bg-surface-hover transition-colors disabled:opacity-60 disabled:cursor-not-allowed"
		                    :disabled="page <= 1"
		                    @click="page = Math.max(1, page - 1)"
		                >
		                    <span data-translate="previous">{{ __('messages.previous') }}</span>
		                </button>
	                <span class="text-xs">
	                    <span x-text="page"></span>/<span x-text="pageCount"></span>
	                </span>
	                <button
	                    type="button"
	                    class="px-3 py-2 rounded-xl border border-border hover:bg-surface-hover transition-colors disabled:opacity-60 disabled:cursor-not-allowed"
		                    :disabled="page >= pageCount"
		                    @click="page = Math.min(pageCount, page + 1)"
		                >
		                    <span data-translate="next">{{ __('messages.next') }}</span>
		                </button>
		            </div>
		        </div>
	    </div>
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
		            uiLang: (window.globalLangState?.currentLang || config?.locale || 'id'),
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
	                    },
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
		                if (len < 2) return this.msg('fill_consumer') || (this.uiLang === 'id'
		                    ? 'Mohon isi terlebih dahulu nama konsumen.'
		                    : 'Please fill in the consumer name first.');
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
                    const lang = this.uiLang || config?.locale || 'id';
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
		                    return new Intl.NumberFormat(this.uiLang === 'id' ? 'id-ID' : 'en-US').format(n);
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
		                this.uiLang = window.globalLangState?.currentLang || config?.locale || 'id';
		                window.addEventListener('language-changed', (e) => {
		                    this.uiLang = String(e?.detail?.lang || this.uiLang || config?.locale || 'id');
		                });

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

	                const hasServerOld = !config?.saved && (
	                    (server.consumerName || '').trim() !== '' ||
	                    (server.packageType || '').trim() !== '' ||
	                    server.bandagePackageId !== '' ||
	                    server.ifaksPackageId !== '' ||
	                    server.painkillerPackageId !== ''
	                );

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

	    function rekapFarmasiTodayStats(config) {
	        const toInt = (v) => {
	            const n = Number.parseInt(v ?? 0, 10);
	            return Number.isFinite(n) ? n : 0;
	        };

	        return {
	            uiLang: String(window.globalLangState?.currentLang || config?.locale || 'id'),
	            currentUserId: toInt(config?.currentUserId),
	            totalTrx: toInt(config?.totalTrx),
	            totalPrice: toInt(config?.totalPrice),

	            get bonus() {
	                return Math.floor(this.totalPrice * 0.4);
	            },

	            init() {
	                window.addEventListener('language-changed', (e) => {
	                    this.uiLang = String(e?.detail?.lang || this.uiLang || 'id');
	                });

	                window.addEventListener('farmasi-sales-deleted', (e) => {
	                    const rows = Array.isArray(e?.detail?.rows) ? e.detail.rows : [];
	                    rows.forEach((r) => {
	                        if (!r) return;
	                        if (toInt(r.medicUserId) !== this.currentUserId) return;
	                        if (!r.isToday) return;
	                        this.totalTrx = Math.max(0, this.totalTrx - 1);
	                        this.totalPrice = Math.max(0, this.totalPrice - toInt(r.price));
	                    });
	                });
	            },

	            formatNumber(num) {
	                const n = toInt(num);
	                try {
	                    return new Intl.NumberFormat(this.uiLang === 'id' ? 'id-ID' : 'en-US', { maximumFractionDigits: 0 }).format(n);
	                } catch (e) {
	                    return String(n);
	                }
	            },

	            formatMoney(amount) {
	                return '$ ' + this.formatNumber(amount);
	            },
	        };
	    }

	    function rekapFarmasiTable(config) {
	        const toInt = (v) => {
	            const n = Number.parseInt(v ?? 0, 10);
	            return Number.isFinite(n) ? n : 0;
	        };

	        const normalize = (v) => String(v ?? '').toLowerCase().replace(/\s+/g, ' ').trim();

	        const csrf = () => document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

	        const destroyUrl = (id) => {
	            const base = String(config?.destroyUrlTemplate || '');
	            return base.replace(/\/0$/, '/' + String(id));
	        };

	        return {
	            locale: config?.locale || 'id',
	            currentUserId: toInt(config?.currentUserId),
	            currentUserName: String(config?.currentUserName || ''),
	            rows: (Array.isArray(config?.rows) ? config.rows : [])
	                .map((r) => ({
	                    ...r,
	                    id: toInt(r?.id),
	                    createdAtTs: toInt(r?.createdAtTs),
	                    medicUserId: toInt(r?.medicUserId),
	                    bandage: toInt(r?.bandage),
	                    ifaks: toInt(r?.ifaks),
	                    painkiller: toInt(r?.painkiller),
	                    price: toInt(r?.price),
	                    bonus: toInt(r?.bonus),
	                }))
	                .sort((a, b) => (b.createdAtTs || 0) - (a.createdAtTs || 0)),
	            search: '',
	            pageSize: 25,
	            page: 1,
	            selectedIds: [],
	
	            init() {
	                const sync = (lang) => {
	                    this.locale = String(lang || this.locale || 'id');
	                };
	
	                sync(window.globalLangState?.currentLang || this.locale);
	                window.addEventListener('language-changed', (e) => sync(e?.detail?.lang));
	            },
	
	            t(key, fallback) {
	                const table = window.globalLangState?.translations || {};
	                return table?.[key] || fallback || '';
	            },

	            get filteredRows() {
	                const q = normalize(this.search);
	                if (!q) return this.rows;
	                return this.rows.filter((r) => {
	                    const hay = normalize([r.timeText, r.consumer, r.package].join(' '));
	                    return hay.includes(q);
	                });
	            },

	            get pageCount() {
	                const total = this.filteredRows.length;
	                const size = Math.max(1, toInt(this.pageSize));
	                return Math.max(1, Math.ceil(total / size));
	            },

	            get pageRows() {
	                const size = Math.max(1, toInt(this.pageSize));
	                const p = Math.min(Math.max(1, toInt(this.page)), this.pageCount);
	                const start = (p - 1) * size;
	                return this.filteredRows.slice(start, start + size);
	            },

	            get allOnPageSelected() {
	                const ids = this.pageRows
	                    .filter((r) => r.medicUserId === this.currentUserId)
	                    .map((r) => r.id);
	                if (ids.length === 0) return false;
	                return ids.every((id) => this.selectedIds.includes(id));
	            },

	            isSelected(id) {
	                return this.selectedIds.includes(toInt(id));
	            },

	            toggleOne(id, checked) {
	                const saleId = toInt(id);
	                if (!saleId) return;
	                const row = this.rows.find((r) => r.id === saleId);
	                if (!row) return;
	                if (row.medicUserId !== this.currentUserId) return;

	                if (checked) {
	                    if (!this.selectedIds.includes(saleId)) this.selectedIds = [...this.selectedIds, saleId];
	                } else {
	                    this.selectedIds = this.selectedIds.filter((x) => x !== saleId);
	                }
	            },

	            toggleAllOnPage(checked) {
	                const pageIds = this.pageRows
	                    .filter((r) => r.medicUserId === this.currentUserId)
	                    .map((r) => r.id);

	                if (!checked) {
	                    this.selectedIds = this.selectedIds.filter((id) => !pageIds.includes(id));
	                    return;
	                }

	                const next = new Set(this.selectedIds);
	                pageIds.forEach((id) => next.add(id));
	                this.selectedIds = Array.from(next);
	            },

	            get footerTotals() {
	                const list = this.pageRows;
	                return list.reduce(
	                    (acc, r) => {
	                        acc.bandage += toInt(r.bandage);
	                        acc.ifaks += toInt(r.ifaks);
	                        acc.painkiller += toInt(r.painkiller);
	                        acc.price += toInt(r.price);
	                        acc.bonus += toInt(r.bonus);
	                        return acc;
	                    },
	                    { bandage: 0, ifaks: 0, painkiller: 0, price: 0, bonus: 0 }
	                );
	            },

	            formatMoney(amount) {
	                const n = toInt(amount);
	                try {
	                    const nf = new Intl.NumberFormat(this.locale === 'id' ? 'id-ID' : 'en-US', { maximumFractionDigits: 0 });
	                    return '$ ' + nf.format(n);
	                } catch (e) {
	                    return '$ ' + String(n);
	                }
	            },

	            sanitizeFilePart(v) {
	                return String(v ?? '')
	                    .trim()
	                    .replace(/\s+/g, '_')
	                    .replace(/[^a-zA-Z0-9_-]/g, '');
	            },

	            exportTimestamp() {
	                const pad2 = (n) => String(n).padStart(2, '0');
	                const d = new Date();
	                const yyyy = d.getFullYear();
	                const mm = pad2(d.getMonth() + 1);
	                const dd = pad2(d.getDate());
	                const hh = pad2(d.getHours());
	                const mi = pad2(d.getMinutes());
	                return `${yyyy}-${mm}-${dd}_${hh}-${mi}`;
	            },

	            exportTxt() {
	                const useSelected = this.selectedIds.length > 0;
	                const selectedSet = new Set(this.selectedIds);
	                const list = useSelected
	                    ? this.rows.filter((r) => selectedSet.has(r.id))
	                    : this.filteredRows;

	                const cols = [
	                    { key: 'timeText', label: 'Time', align: 'left' },
	                    { key: 'consumer', label: 'Consumer', align: 'left' },
	                    { key: 'package', label: 'Package', align: 'left' },
	                    { key: 'bandage', label: 'Bandage', align: 'right', map: (r) => String(toInt(r.bandage)) },
	                    { key: 'ifaks', label: 'IFAKS', align: 'right', map: (r) => String(toInt(r.ifaks)) },
	                    { key: 'painkiller', label: 'Painkiller', align: 'right', map: (r) => String(toInt(r.painkiller)) },
	                    { key: 'price', label: 'Price', align: 'right', map: (r) => String(toInt(r.price)) },
	                    { key: 'bonus', label: 'Bonus40', align: 'right', map: (r) => String(toInt(r.bonus)) },
	                ];

	                const cellText = (col, row) => {
	                    if (col.map) return String(col.map(row) ?? '');
	                    return String(row?.[col.key] ?? '');
	                };

	                const widths = cols.map((c) => {
	                    const base = c.label.length;
	                    const maxRow = list.reduce((m, r) => Math.max(m, cellText(c, r).length), 0);
	                    return Math.max(base, maxRow);
	                });

	                const pad = (text, width, align) => {
	                    const s = String(text ?? '');
	                    if (align === 'right') return s.padStart(width, ' ');
	                    return s.padEnd(width, ' ');
	                };

	                const gap = '  ';
	                const lines = [];
	                lines.push(cols.map((c, i) => pad(c.label, widths[i], 'left')).join(gap));
	                list.forEach((r) => {
	                    lines.push(cols.map((c, i) => pad(cellText(c, r), widths[i], c.align)).join(gap));
	                });

	                const blob = new Blob([lines.join('\n')], { type: 'text/plain;charset=utf-8' });
	                const url = URL.createObjectURL(blob);
	                const a = document.createElement('a');
	                a.href = url;
	                const namePart = this.sanitizeFilePart(this.currentUserName) || 'Unknown';
	                const stamp = this.exportTimestamp();
	                const suffix = useSelected ? '_Selected' : '';
	                a.download = `Rekap_Farmasi_${namePart}_${stamp}${suffix}.txt`;
	                document.body.appendChild(a);
	                a.click();
	                a.remove();
	                URL.revokeObjectURL(url);
	            },

	            async deleteOne(id) {
	                const saleId = toInt(id);
	                if (!saleId) return;

	                const row = this.rows.find((r) => r.id === saleId);
	                if (!row) return;
	                if (row.medicUserId !== this.currentUserId) return;

	                const ok = confirm(this.t('farmasi_confirm_delete_one', 'Delete this transaction?'));
	                if (!ok) return;

	                try {
	                    const res = await fetch(destroyUrl(saleId), {
	                        method: 'DELETE',
	                        headers: {
	                            'Accept': 'application/json',
	                            ...(csrf() ? { 'X-CSRF-TOKEN': csrf() } : {}),
	                        },
	                    });
	                    if (!res.ok) throw new Error('request_failed');

	                    window.dispatchEvent(new CustomEvent('farmasi-sales-deleted', { detail: { rows: [row] } }));

	                    this.rows = this.rows.filter((r) => r.id !== saleId);
	                    this.selectedIds = this.selectedIds.filter((x) => x !== saleId);
	                    this.page = Math.min(this.page, this.pageCount);
	                } catch (e) {
	                    alert(this.t('farmasi_delete_failed', 'Failed to delete. Please refresh the page.'));
	                }
	            },

	            async deleteSelected() {
	                const ids = this.selectedIds.slice();
	                if (ids.length === 0) return;

	                const template = this.t('farmasi_confirm_delete_selected', '');
	                const msg = template
	                    ? template.replace(':count', String(ids.length))
	                    : `Delete ${ids.length} selected transactions?`;
	                const ok = confirm(msg);
	                if (!ok) return;

	                try {
	                    const res = await fetch(String(config?.bulkDestroyUrl || ''), {
	                        method: 'DELETE',
	                        headers: {
	                            'Content-Type': 'application/json',
	                            'Accept': 'application/json',
	                            ...(csrf() ? { 'X-CSRF-TOKEN': csrf() } : {}),
	                        },
	                        body: JSON.stringify({ ids }),
	                    });
	                    if (!res.ok) throw new Error('request_failed');

	                    const deletedSet = new Set(ids);
	                    const deletedRows = this.rows.filter((r) => deletedSet.has(r.id));
	                    if (deletedRows.length > 0) {
	                        window.dispatchEvent(new CustomEvent('farmasi-sales-deleted', { detail: { rows: deletedRows } }));
	                    }
	                    this.rows = this.rows.filter((r) => !deletedSet.has(r.id));
	                    this.selectedIds = [];
	                    this.page = Math.min(this.page, this.pageCount);
	                } catch (e) {
	                    alert(this.t('farmasi_delete_selected_failed', 'Failed to delete selected rows. Please refresh the page.'));
	                }
	            },
	        };
	    }
</script>
@endpush
@endsection
