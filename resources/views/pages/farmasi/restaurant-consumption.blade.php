{{-- Farmasi: Restaurant Consumption --}}
@extends('layouts.app')

@section('title', __('messages.restaurant_consumption_title') . ' - ' . ($appName ?? config('app.name')))
@section('page-title', __('messages.restaurant_consumption_title'))
@section('page-description', __('messages.restaurant_consumption_subtitle'))

@section('content')
@php
    use Carbon\Carbon;

    $thousandSep = app()->getLocale() === 'id' ? '.' : ',';
    $decimalSep = app()->getLocale() === 'id' ? ',' : '.';
    $fmtMoney = fn ($n) => '$ ' . number_format((float) $n, 0, $decimalSep, $thousandSep);
    $fmtInt = fn ($n) => number_format((int) $n, 0, $decimalSep, $thousandSep);

    $activeRestaurants = array_values(array_filter($restaurants ?? [], fn ($r) => (int) ($r['is_active'] ?? 0) === 1));

    $icoBars = '<svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18"/></svg>';
    $icoMoney = '<svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-10V6m0 12v-2m9-4a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
    $icoCheck = '<svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
    $icoCog = '<svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>';
    $icoPlus = '<svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>';
    $icoTrash = '<svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>';
@endphp

<div
    x-data="restaurantConsumptionPage({
        storeUrl: @js(route('restaurant.consumption.store')),
        approveUrlTemplate: @js(route('restaurant.consumption.approve', ['id' => '__ID__'])),
        paidUrlTemplate: @js(route('restaurant.consumption.paid', ['id' => '__ID__'])),
        deleteUrlTemplate: @js(route('restaurant.consumption.delete', ['id' => '__ID__'])),
        restaurants: @js($activeRestaurants),
        defaultCode: @js($defaultCode ?? ''),
        strings: {
            confirmApprove: @js(__('messages.restaurant_consumption_confirm_approve')),
            confirmPaid: @js(__('messages.restaurant_consumption_confirm_paid')),
            confirmDelete: @js(__('messages.restaurant_consumption_confirm_delete')),
            saved: @js(__('messages.restaurant_consumption_saved')),
        }
    })"
    class="space-y-6"
>
    <x-grid :cols="4" :gap="'default'">
        <x-stat-card
            :title="__('messages.restaurant_consumption_stat_packets')"
            dataTranslateTitle="restaurant_consumption_stat_packets"
            :value="$fmtInt($stats['total_packets'] ?? 0)"
            color="success"
            :icon="$icoBars"
        />
        <x-stat-card
            :title="__('messages.restaurant_consumption_stat_subtotal')"
            dataTranslateTitle="restaurant_consumption_stat_subtotal"
            :value="$fmtMoney($stats['total_subtotal'] ?? 0)"
            color="info"
            :icon="$icoMoney"
        />
        <x-stat-card
            :title="__('messages.restaurant_consumption_stat_tax')"
            dataTranslateTitle="restaurant_consumption_stat_tax"
            :value="$fmtMoney($stats['total_tax'] ?? 0)"
            color="warning"
            :icon="$icoMoney"
        />
        <x-stat-card
            :title="__('messages.restaurant_consumption_stat_total')"
            dataTranslateTitle="restaurant_consumption_stat_total"
            :value="$fmtMoney($stats['total_grand'] ?? 0)"
            color="success"
            :icon="$icoCheck"
        />
    </x-grid>

    <x-card>
        <div class="flex items-center gap-2 mb-4">
            <svg class="w-5 h-5 text-text-tertiary" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <h3 class="text-lg font-semibold text-text-primary" data-translate="restaurant_consumption_filter_title">{{ __('messages.restaurant_consumption_filter_title') }}</h3>
        </div>

        <form method="get" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end" x-data="{ range: @js($range) }">
            <div>
                <x-select
                    name="range"
                    :label="__('messages.farmasi_date_range')"
                    dataTranslateLabel="farmasi_date_range"
                    x-model="range"
                    :options="[
                        'week1' => __('messages.range_3_weeks_ago'),
                        'week2' => __('messages.range_2_weeks_ago'),
                        'week3' => __('messages.range_last_week'),
                        'week4' => __('messages.range_this_week'),
                        'month1' => __('messages.range_this_month'),
                        'custom' => __('messages.range_custom'),
                        'all' => __('messages.range_all'),
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

            <div class="md:col-span-3 flex items-center justify-between gap-3">
                <x-button type="submit" variant="secondary">
                    <span data-translate="apply_filter">{{ __('messages.apply_filter') }}</span>
                </x-button>
                <p class="text-sm text-text-secondary">
                    <span data-translate="farmasi_active_range">{{ __('messages.farmasi_active_range') }}</span>:
                    <span class="font-semibold text-text-primary">{{ $rangeLabel }}</span>
                </p>
            </div>
        </form>
    </x-card>

    <x-card>
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-text-tertiary" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18"/>
                </svg>
                <h3 class="text-lg font-semibold text-text-primary" data-translate="restaurant_consumption_list_title">{{ __('messages.restaurant_consumption_list_title') }}</h3>
            </div>

            <div class="flex items-center gap-2">
                @if($canManage ?? false)
                    <x-button type="button" variant="secondary" x-on:click="window.location.href = '{{ route('restaurant.settings.index') }}'"
                              :icon="$icoCog"
                    >
                        <span data-translate="restaurant_consumption_settings">{{ __('messages.restaurant_consumption_settings') }}</span>
                    </x-button>
                @endif

                <x-button type="button" variant="success" @click="addOpen = true" :icon="$icoPlus"
                >
                    <span data-translate="restaurant_consumption_add_button">{{ __('messages.restaurant_consumption_add_button') }}</span>
                </x-button>
            </div>
        </div>

        <x-table :headers="[
            ['label' => '#'],
            ['label' => __('messages.restaurant_consumption_code')],
            ['label' => __('messages.date')],
            ['label' => __('messages.restaurant_consumption_restaurant')],
            ['label' => __('messages.restaurant_consumption_recipient')],
            ['label' => __('messages.restaurant_consumption_packets')],
            ['label' => __('messages.restaurant_consumption_price_per_packet')],
            ['label' => __('messages.restaurant_consumption_subtotal')],
            ['label' => __('messages.restaurant_consumption_tax')],
            ['label' => __('messages.restaurant_consumption_total')],
            ['label' => __('messages.restaurant_consumption_ktp')],
            ['label' => __('messages.status')],
            ['label' => __('messages.actions')],
        ]" striped bordered compact>
            @forelse($rows as $i => $r)
                @php
                    $status = (string) ($r['status'] ?? 'pending');
                    $badgeVariant = match ($status) {
                        'paid' => 'success',
                        'approved' => 'info',
                        default => 'warning',
                    };

                    $deliveryAt = null;
                    try { $deliveryAt = Carbon::parse((string) ($r['delivery_at'] ?? '')); } catch (\Throwable $e) { $deliveryAt = null; }
                    $deliveryText = $deliveryAt ? $deliveryAt->copy()->locale(app()->getLocale())->translatedFormat('dddd, d M Y') : '-';
                    $timeText = $deliveryAt ? $deliveryAt->format('H:i') : '';

                    $approvedAt = null;
                    try { $approvedAt = Carbon::parse((string) ($r['approved_at'] ?? '')); } catch (\Throwable $e) { $approvedAt = null; }
                    $paidAt = null;
                    try { $paidAt = Carbon::parse((string) ($r['paid_at'] ?? '')); } catch (\Throwable $e) { $paidAt = null; }
                @endphp
                <tr>
                    <td class="px-4 py-2 text-sm text-text-secondary">{{ $i + 1 }}</td>
                    <td class="px-4 py-2 text-sm text-text-primary font-semibold">{{ (string) ($r['consumption_code'] ?? '-') }}</td>
                    <td class="px-4 py-2 text-sm text-text-primary">
                        <div class="font-semibold">{{ $deliveryText }}</div>
                        @if($timeText !== '')
                            <div class="text-xs text-text-secondary">{{ $timeText }}</div>
                        @endif
                    </td>
                    <td class="px-4 py-2 text-sm text-text-primary">
                        <div class="font-semibold">{{ (string) ($r['restaurant_name'] ?? '-') }}</div>
                    </td>
                    <td class="px-4 py-2 text-sm text-text-primary">
                        <div class="font-semibold">{{ (string) ($r['recipient_name'] ?? '-') }}</div>
                        <div class="text-xs text-text-secondary">
                            <span data-translate="restaurant_consumption_submitted_by">{{ __('messages.restaurant_consumption_submitted_by') }}</span>:
                            {{ (string) ($r['created_by_name'] ?? '-') }}
                        </div>
                    </td>
                    <td class="px-4 py-2 text-sm text-text-primary font-semibold whitespace-nowrap">
                        {{ $fmtInt($r['packet_count'] ?? 0) }}
                    </td>
                    <td class="px-4 py-2 text-sm text-text-primary whitespace-nowrap">{{ $fmtMoney($r['price_per_packet'] ?? 0) }}</td>
                    <td class="px-4 py-2 text-sm text-text-primary whitespace-nowrap">{{ $fmtMoney($r['subtotal'] ?? 0) }}</td>
                    <td class="px-4 py-2 text-sm text-text-primary whitespace-nowrap">
                        <div class="text-xs text-text-secondary">{{ (int) ($r['tax_percentage'] ?? 0) }}%</div>
                        <div class="font-semibold">{{ $fmtMoney($r['tax_amount'] ?? 0) }}</div>
                    </td>
                    <td class="px-4 py-2 text-sm text-text-primary font-semibold whitespace-nowrap">{{ $fmtMoney($r['total_amount'] ?? 0) }}</td>
                    <td class="px-4 py-2 text-sm text-text-primary">
                        @if(!empty($r['ktp_file']))
                            <x-button
                                type="button"
                                variant="secondary"
                                size="xs"
                                :data-src="asset((string) $r['ktp_file'])"
                                :data-title="__('messages.restaurant_consumption_ktp_title', ['code' => (string) ($r['consumption_code'] ?? '-')])"
                                @click="openDoc($event.currentTarget.dataset.src, $event.currentTarget.dataset.title)"
                            >
                                <span data-translate="view">{{ __('messages.view') }}</span>
                            </x-button>
                        @else
                            <span class="text-text-muted">-</span>
                        @endif
                    </td>
                    <td class="px-4 py-2 text-sm text-text-primary">
                        <div class="space-y-2">
                            <x-badge :variant="$badgeVariant">{{ strtoupper($status) }}</x-badge>

                            @if(!empty($r['approved_by_name']) && $approvedAt)
                                <div class="text-[11px] text-text-secondary">
                                    <span class="font-semibold text-success-600">{{ __('messages.restaurant_consumption_approved_by') }}</span>
                                    <span class="font-semibold">{{ (string) $r['approved_by_name'] }}</span>
                                    <div class="text-text-tertiary">{{ $approvedAt->copy()->locale(app()->getLocale())->translatedFormat('dddd, d M Y H:i') }}</div>
                                </div>
                            @endif

                            @if(!empty($r['paid_by_name']) && $paidAt)
                                <div class="text-[11px] text-text-secondary">
                                    <span class="font-semibold text-info-600">{{ __('messages.restaurant_consumption_paid_by') }}</span>
                                    <span class="font-semibold">{{ (string) $r['paid_by_name'] }}</span>
                                    <div class="text-text-tertiary">{{ $paidAt->copy()->locale(app()->getLocale())->translatedFormat('dddd, d M Y H:i') }}</div>
                                </div>
                            @endif
                        </div>
                    </td>
                    <td class="px-4 py-2 text-sm text-text-primary whitespace-nowrap">
                        <div class="flex items-center gap-2">
                            @if(($canManage ?? false) && $status === 'pending')
                                <x-button
                                    type="button"
                                    variant="success"
                                    size="xs"
                                    :icon="$icoCheck"
                                    @click="approveRow({{ (int) ($r['id'] ?? 0) }})"
                                >
                                    <span data-translate="approve">{{ __('messages.approve') }}</span>
                                </x-button>
                            @endif

                            @if(($canManage ?? false) && $status === 'approved')
                                <x-button
                                    type="button"
                                    variant="primary"
                                    size="xs"
                                    :icon="$icoMoney"
                                    @click="markPaidRow({{ (int) ($r['id'] ?? 0) }})"
                                >
                                    <span data-translate="paid">{{ __('messages.paid') }}</span>
                                </x-button>
                            @endif

                            @if(($isDirector ?? false))
                                <x-button
                                    type="button"
                                    variant="danger"
                                    size="xs"
                                    :icon="$icoTrash"
                                    @click="deleteRow({{ (int) ($r['id'] ?? 0) }})"
                                >
                                    <span data-translate="delete">{{ __('messages.delete') }}</span>
                                </x-button>
                            @endif

                            @if((!($canManage ?? false) || !in_array($status, ['pending','approved'], true)) && !($isDirector ?? false))
                                <span class="text-text-muted">-</span>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="13" class="px-4 py-10 text-center text-text-secondary">
                        <span data-translate="no_data">{{ __('messages.no_data') }}</span>
                    </td>
                </tr>
            @endforelse
        </x-table>
    </x-card>

    <x-modal x-model="addOpen" :title="__('messages.restaurant_consumption_add_title')" size="lg">
        <form class="space-y-4" @submit.prevent="submitCreate()">
            <x-input
                name="consumption_code_ui"
                :label="__('messages.restaurant_consumption_code')"
                dataTranslateLabel="restaurant_consumption_code"
                x-model="form.code"
                readonly
            />

            <x-select
                name="restaurant_id"
                :label="__('messages.restaurant_consumption_restaurant')"
                dataTranslateLabel="restaurant_consumption_restaurant"
                :options="collect($activeRestaurants)->mapWithKeys(fn($r) => [(string) ($r['id'] ?? '') => (string) ($r['restaurant_name'] ?? '-')])->all()"
                :placeholder="__('messages.restaurant_consumption_restaurant_placeholder')"
                dataTranslatePlaceholder="restaurant_consumption_restaurant_placeholder"
                x-model="form.restaurantId"
                :required="true"
            />

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <x-input
                    type="date"
                    name="delivery_date"
                    :label="__('messages.date')"
                    dataTranslateLabel="date"
                    x-model="form.deliveryDate"
                    :required="true"
                />
                <x-input
                    type="time"
                    name="delivery_time"
                    :label="__('messages.time')"
                    dataTranslateLabel="time"
                    x-model="form.deliveryTime"
                    :required="true"
                />
            </div>

            <x-input
                type="number"
                name="packet_count"
                :label="__('messages.restaurant_consumption_packets')"
                dataTranslateLabel="restaurant_consumption_packets"
                min="1"
                x-model="form.packetCount"
                @input="recalc()"
                :required="true"
            />

            <div class="rounded-xl border border-border bg-surface-alt p-4 space-y-2">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-text-secondary" data-translate="restaurant_consumption_price_per_packet">{{ __('messages.restaurant_consumption_price_per_packet') }}</span>
                    <span class="font-semibold text-text-primary" x-text="formatMoney(calc.pricePerPacket)"></span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-text-secondary" data-translate="restaurant_consumption_subtotal">{{ __('messages.restaurant_consumption_subtotal') }}</span>
                    <span class="font-semibold text-text-primary" x-text="formatMoney(calc.subtotal)"></span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-text-secondary">
                        <span data-translate="restaurant_consumption_tax">{{ __('messages.restaurant_consumption_tax') }}</span>
                        <span class="text-text-tertiary" x-text="`(${calc.taxPercentage}%)`"></span>
                    </span>
                    <span class="font-semibold text-text-primary" x-text="formatMoney(calc.taxAmount)"></span>
                </div>
                <div class="pt-2 border-t border-border flex items-center justify-between">
                    <span class="font-semibold text-text-primary" data-translate="restaurant_consumption_total">{{ __('messages.restaurant_consumption_total') }}</span>
                    <span class="font-bold text-success-600" x-text="formatMoney(calc.totalAmount)"></span>
                </div>
            </div>

            <x-input
                name="notes"
                :label="__('messages.notes')"
                dataTranslateLabel="notes"
                :placeholder="__('messages.restaurant_consumption_notes_placeholder')"
                dataTranslatePlaceholder="restaurant_consumption_notes_placeholder"
                x-model="form.notes"
            />

            <x-file-input
                name="ktp_file"
                :label="__('messages.restaurant_consumption_ktp_upload')"
                dataTranslateLabel="restaurant_consumption_ktp_upload"
                dataTranslateUpload="click_to_upload"
                accept="image/png,image/jpeg"
                :required="true"
            />

            <div class="flex justify-end gap-2 pt-2">
                <x-button type="button" variant="secondary" @click="addOpen = false" x-bind:disabled="submitting">
                    <span data-translate="cancel">{{ __('messages.cancel') }}</span>
                </x-button>
                <x-button type="submit" variant="success" x-bind:disabled="submitting">
                    <span x-show="!submitting" data-translate="save">{{ __('messages.save') }}</span>
                    <span x-show="submitting" x-cloak data-translate="loading">{{ __('messages.loading') }}</span>
                </x-button>
            </div>
        </form>
    </x-modal>

    <x-modal x-model="docPreviewOpen" :title="__('messages.document_preview')" size="xl">
        <div class="space-y-3">
            <div class="text-sm font-semibold text-text-primary" x-text="docPreviewTitle"></div>
            <div class="rounded-xl border border-border bg-surface-alt p-3">
                <img
                    :src="docPreviewSrc"
                    alt=""
                    class="w-full max-h-[70vh] object-contain"
                >
            </div>
            <div class="flex justify-end">
                <x-button type="button" variant="secondary" @click="docPreviewOpen = false">
                    <span data-translate="close">{{ __('messages.close') }}</span>
                </x-button>
            </div>
        </div>
    </x-modal>
</div>
@endsection
