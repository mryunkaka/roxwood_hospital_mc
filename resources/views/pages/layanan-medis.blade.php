@extends('layouts.app')

@section('title', __('messages.medis_services_title') . ' - ' . __('messages.app_name'))

@section('page-title', __('messages.medis_services_title'))
@section('page-description', __('messages.medis_services_subtitle'))

@section('content')
@php
    $thousandSep = app()->getLocale() === 'id' ? '.' : ',';
    $decimalSep = app()->getLocale() === 'id' ? ',' : '.';
    $fmt = fn ($n) => number_format((float) $n, 0, $decimalSep, $thousandSep);
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
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/>
        </svg>
        <h3 class="text-lg font-semibold text-text-primary" data-translate="medis_new_service">
            {{ __('messages.medis_new_service') }}
        </h3>
    </div>

    <div
        x-data="emsServicesForm({
            previewUrl: @js(route('api.medis.preview_price')),
            locale: @js(app()->getLocale()),
            saved: @js(session()->has('success')),
            prices: {
                bleedingNormal: @js((int) ($priceBleedingNormal ?? 0)),
                bleedingPeluru: @js((int) ($priceBleedingPeluru ?? 0)),
            },
            old: {
                service_type: @js(old('service_type', '')),
                service_detail: @js(old('service_detail', '')),
                operasi_tingkat: @js(old('operasi_tingkat', '')),
                patient_name: @js(old('patient_name', '')),
                location: @js(old('location', '')),
                qty: @js((int) old('qty', 1)),
                payment_type: @js(old('payment_type', '')),
                is_gunshot: @js((bool) old('is_gunshot', false)),
                meds: @js((array) old('meds', [])),
            }
        })"
    >
        <form method="post" action="{{ route('medis.ems.store') }}" class="space-y-4" x-ref="form">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <div>
                    <x-select
                        name="service_type"
                        id="serviceType"
                        :label="__('messages.medis_service_type')"
                        dataTranslateLabel="medis_service_type"
                        :placeholder="__('messages.medis_choose_service_type')"
                        :options="[
                            'Pingsan' => __('messages.medis_service_pingsan'),
                            'Treatment' => __('messages.medis_service_treatment'),
                            'Surat' => __('messages.medis_service_surat'),
                            'Operasi' => __('messages.medis_service_operasi'),
                            'Rawat Inap' => __('messages.medis_service_rawat_inap'),
                            'Kematian' => __('messages.medis_service_kematian'),
                            'Plastik' => __('messages.medis_service_plastik'),
                        ]"
                        :value="old('service_type', '')"
                        :error="$errors->first('service_type')"
                        required
                        x-model="serviceType"
                        @change="onServiceTypeChange()"
                    />
                </div>

                <div x-show="showDetail" x-cloak>
                    <x-select
                        name="service_detail"
                        id="serviceDetail"
                        :label="__('messages.medis_service_detail')"
                        dataTranslateLabel="medis_service_detail"
                        :placeholder="__('messages.medis_choose_detail')"
                        :options="[]"
                        :value="old('service_detail', '')"
                        :error="$errors->first('service_detail')"
                        x-model="serviceDetail"
                        @change="preview()"
                    />
                    <p class="mt-2 text-xs text-text-secondary" x-show="detailHint" x-text="detailHint" x-cloak></p>
                </div>

                <div x-show="showOperasiTingkat" x-cloak>
                    <x-select
                        name="operasi_tingkat"
                        id="operasiTingkat"
                        :label="__('messages.medis_operasi_tingkat')"
                        dataTranslateLabel="medis_operasi_tingkat"
                        :placeholder="__('messages.medis_choose_operasi_tingkat')"
                        :options="[
                            'ringan' => __('messages.medis_operasi_ringan'),
                            'sedang' => __('messages.medis_operasi_sedang'),
                            'berat' => __('messages.medis_operasi_berat'),
                        ]"
                        :value="old('operasi_tingkat', '')"
                        :error="$errors->first('operasi_tingkat')"
                        x-model="operasiTingkat"
                        @change="preview()"
                    />
                </div>

                <div x-show="showPatient" x-cloak>
                    <x-input
                        name="patient_name"
                        :label="__('messages.medis_patient_name')"
                        dataTranslateLabel="medis_patient_name"
                        :placeholder="__('messages.medis_patient_name_placeholder')"
                        :value="old('patient_name', '')"
                        :error="$errors->first('patient_name')"
                        x-model="patientName"
                        @input.debounce.200ms="preview()"
                    />
                </div>

                <div x-show="showLocation" x-cloak>
                    <x-input
                        name="location"
                        id="location"
                        :label="__('messages.medis_location')"
                        dataTranslateLabel="medis_location"
                        :placeholder="__('messages.medis_location_placeholder')"
                        :value="old('location', '')"
                        :error="$errors->first('location')"
                        x-model="location"
                        inputmode="numeric"
                        maxlength="4"
                        @input="location = String(location || '').replace(/\\D/g,'').slice(0,4); preview()"
                    />
                    <p class="mt-2 text-xs text-text-secondary" data-translate="medis_location_hint">
                        {{ __('messages.medis_location_hint') }}
                    </p>
                </div>

                <div x-show="showQty" x-cloak>
                    <x-input
                        type="number"
                        name="qty"
                        id="qty"
                        :label="__('messages.medis_qty')"
                        dataTranslateLabel="medis_qty"
                        :value="(int) old('qty', 1)"
                        :error="$errors->first('qty')"
                        min="1"
                        x-model.number="qty"
                        @input.debounce.150ms="preview()"
                    />
                </div>

                <div x-show="showPayment" x-cloak>
                    <x-select
                        name="payment_type"
                        id="paymentType"
                        :label="__('messages.medis_payment_type')"
                        dataTranslateLabel="medis_payment_type"
                        :options="[
                            'cash' => __('messages.medis_payment_cash'),
                            'billing' => __('messages.medis_payment_billing'),
                            'mixed' => __('messages.medis_payment_mixed'),
                        ]"
                        :value="old('payment_type', 'cash')"
                        :error="$errors->first('payment_type')"
                        x-model="paymentType"
                        @change="preview()"
                        x-bind:disabled="paymentLocked"
                    />
                    <p class="mt-2 text-xs text-text-secondary" x-show="paymentHint" x-text="paymentHint" x-cloak></p>
                </div>
            </div>

            <div x-show="showMedicine" x-cloak class="rounded-xl border border-border bg-surface-alt p-4 space-y-3">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-text-tertiary" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 010 2.828l-1.172 1.172a2 2 0 01-2.828 0L6 10.828V7h3.828l9.6 9.6z"/>
                    </svg>
                    <p class="text-sm font-semibold text-text-primary" data-translate="medis_medicine_section">
                        {{ __('messages.medis_medicine_section') }}
                    </p>
                </div>

                <x-checkbox
                    name="is_gunshot"
                    :label="__('messages.medis_is_gunshot')"
                    dataTranslateLabel="medis_is_gunshot"
                    value="1"
                    :checked="(bool) old('is_gunshot', false)"
                    x-model="isGunshot"
                    @change="preview()"
                    :description="__('messages.medis_is_gunshot_hint', ['price' => '$ ' . $fmt((int) ($priceBleedingPeluru ?? 0))])"
                />

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    @foreach([
                        'Head' => 'GAUZE',
                        'Body' => 'GAUZE',
                        'Left Arm' => 'IODINE',
                        'Right Arm' => 'IODINE',
                        'Left Leg' => 'SYRINGE',
                        'Right Leg' => 'SYRINGE',
                        'Left Foot' => 'SYRINGE',
                        'Right Foot' => 'SYRINGE',
                    ] as $area => $item)
                        <x-checkbox
                            name="meds[]"
                            :label="$area . ' (' . $item . ')'"
                            value="{{ $area }}"
                            :checked="in_array($area, (array) old('meds', []), true)"
                            @change="preview()"
                        />
                    @endforeach
                </div>

                <p class="text-xs text-text-secondary" data-translate="medis_medicine_per_item_hint">
                    {{ __('messages.medis_medicine_per_item_hint', ['price' => '$ ' . $fmt((int) ($priceBleedingNormal ?? 0))]) }}
                </p>
            </div>

            <div class="rounded-xl border border-border bg-surface-alt p-4">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-xs text-text-secondary" data-translate="total">{{ __('messages.total') }}</p>
                        <p class="text-xl font-semibold text-text-primary" x-html="totalHtml"></p>
                    </div>
                    <div class="flex items-center gap-2">
                        <x-button type="submit" variant="success">
                            <span data-translate="save">{{ __('messages.save') }}</span>
                        </x-button>
                        <x-button type="button" variant="secondary" @click="clearForm()">
                            <span data-translate="clear">{{ __('messages.clear') }}</span>
                        </x-button>
                    </div>
                </div>
                <p class="mt-2 text-xs text-text-tertiary" x-show="totalHint" x-text="totalHint" x-cloak></p>
            </div>
        </form>
    </div>
</x-card>

<x-card class="mb-6">
    <div class="flex items-center gap-2 mb-4">
        <svg class="w-5 h-5 text-text-tertiary" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 12a1 1 0 011-1h10a1 1 0 011 1v8a1 1 0 01-1 1H4a1 1 0 01-1-1v-8zM17 12a1 1 0 011-1h2a1 1 0 011 1v8a1 1 0 01-1 1h-2a1 1 0 01-1-1v-8z"/>
        </svg>
        <h3 class="text-lg font-semibold text-text-primary" data-translate="medis_filter_title">
            {{ __('messages.medis_filter_title') }}
        </h3>
    </div>

    <form method="get" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end" x-data="{ range: @js($range) }">
        <div>
            <x-select
                name="range"
                :label="__('messages.medis_date_range')"
                dataTranslateLabel="medis_date_range"
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

            @if($canShowAll)
                <a href="{{ route('medis.ems', array_merge(request()->except('show_all'), ['show_all' => $showAll ? null : 1])) }}"
                   class="inline-flex items-center justify-center px-4 py-2 rounded-xl text-sm font-semibold border border-border bg-surface hover:bg-surface-hover transition-colors">
                    <span data-translate="{{ $showAll ? 'medis_show_mine' : 'medis_show_all' }}">
                        {{ $showAll ? __('messages.medis_show_mine') : __('messages.medis_show_all') }}
                    </span>
                </a>
            @endif
        </div>
    </form>

    <p class="text-sm text-text-secondary mt-4">
        <span data-translate="medis_active_range">{{ __('messages.medis_active_range') }}</span>:
        <span class="font-semibold text-text-primary">{{ $rangeLabel }}</span>
    </p>
</x-card>

<x-card class="mb-6">
    <div class="flex items-center gap-2 mb-4">
        <svg class="w-5 h-5 text-text-tertiary" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5l5 5v11a2 2 0 01-2 2z"/>
        </svg>
        <h3 class="text-lg font-semibold text-text-primary" data-translate="medis_rekap_title">
            {{ __('messages.medis_rekap_title') }}
        </h3>
    </div>

    <x-grid :cols="4" :gap="'default'">
        <x-stat-card :title="__('messages.dashboard_total_bandage')" :value="$fmt($rekap['bandage'] ?? 0)" />
        <x-stat-card :title="__('messages.dashboard_total_p3k')" :value="$fmt($rekap['p3k'] ?? 0)" />
        <x-stat-card :title="__('messages.dashboard_total_gauze')" :value="$fmt($rekap['gauze'] ?? 0)" />
        <x-stat-card :title="__('messages.dashboard_total_iodine')" :value="$fmt($rekap['iodine'] ?? 0)" />
        <x-stat-card :title="__('messages.dashboard_total_syringe')" :value="$fmt($rekap['syringe'] ?? 0)" />
        <x-stat-card :title="__('messages.medis_total_billing')" :value="'$ ' . $fmt($rekap['billing'] ?? 0)" />
        <x-stat-card :title="__('messages.medis_total_cash')" :value="'$ ' . $fmt($rekap['cash'] ?? 0)" />
        <x-stat-card :title="__('messages.total')" :value="'$ ' . $fmt($rekap['total'] ?? 0)" />
    </x-grid>
</x-card>

<x-card>
    <div class="flex items-center gap-2 mb-4">
        <svg class="w-5 h-5 text-text-tertiary" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 12a1 1 0 011-1h10a1 1 0 011 1v8a1 1 0 01-1 1H4a1 1 0 01-1-1v-8zM17 12a1 1 0 011-1h2a1 1 0 011 1v8a1 1 0 01-1 1h-2a1 1 0 01-1-1v-8z"/>
        </svg>
        <h3 class="text-lg font-semibold text-text-primary" data-translate="medis_transactions_filtered">
            {{ __('messages.medis_transactions_filtered') }}
        </h3>
    </div>

    @php
        $currentMedicName = (string) ($medicName ?? '');
        $tableRows = collect($rows ?? [])->map(function ($r) {
            $dt = \Carbon\Carbon::parse($r->created_at)->locale(app()->getLocale());
            $billing = (int) ($r->billing_amount ?? 0);
            $cash = (int) ($r->cash_amount ?? 0);
            $total = (int) ($r->total ?? 0);
            $pt = \Illuminate\Support\Str::of((string) ($r->payment_type ?? ''))->lower()->toString();
            $paymentLabel = ($billing > 0 && $cash > 0) ? 'mixed' : ($pt ?: (($billing > 0) ? 'billing' : (($cash > 0) ? 'cash' : '')));

            return [
                'id' => (int) $r->id,
                'createdAtTs' => (int) \Carbon\Carbon::parse($r->created_at)->timestamp,
                'timeText' => $dt->translatedFormat('d M Y, H:i'),
                'serviceType' => (string) ($r->service_type ?? ''),
                'detail' => (string) ($r->service_detail ?? ''),
                'patient' => (string) ($r->patient_name ?? '-'),
                'paymentType' => $paymentLabel,
                'total' => $total,
                'medicName' => (string) ($r->medic_name ?? ''),
            ];
        })->values();
    @endphp

    <div
        x-data="emsServicesTable({
            locale: @js(app()->getLocale()),
            currentMedicName: @js($currentMedicName),
            rows: @js($tableRows),
            destroyUrlTemplate: @js(route('medis.ems.sales.destroy', ['sale' => 0])),
            bulkDestroyUrl: @js(route('medis.ems.sales.bulk_destroy')),
        })"
        class="space-y-4"
    >
        <div class="flex flex-col md:flex-row md:items-center gap-3">
            <div class="flex-1">
                <x-input
                    name="table_search"
                    :label="__('messages.search')"
                    dataTranslateLabel="search"
                    :placeholder="__('messages.medis_table_search_placeholder')"
                    x-model="search"
                    @input.debounce.150ms="page = 1"
                />
            </div>
            <div class="w-full md:w-48">
                <x-select
                    name="page_size"
                    :label="__('messages.medis_show')"
                    dataTranslateLabel="medis_show"
                    :options="[
                        10 => '10',
                        25 => '25',
                        50 => '50',
                    ]"
                    :value="25"
                    x-model.number="pageSize"
                    @change="page = 1"
                />
            </div>
            <div class="flex items-end gap-2">
                <x-button type="button" variant="danger" @click="deleteSelected()" x-bind:disabled="selectedIds.length === 0">
                    <span data-translate="delete">{{ __('messages.delete') }}</span>
                    <span x-show="selectedIds.length > 0" x-cloak x-text="' (' + selectedIds.length + ')'"></span>
                </x-button>
            </div>
        </div>

        <div class="overflow-x-auto rounded-xl border border-border">
            <table class="min-w-full divide-y divide-border">
                <thead class="bg-surface-alt">
                    <tr>
                        <th class="px-4 py-2 text-xs font-semibold tracking-wide text-text-secondary uppercase w-10">
                            <input type="checkbox" class="rounded border-border" :checked="allOnPageSelected" @change="toggleAllOnPage($event.target.checked)">
                        </th>
                        <th class="px-4 py-2 text-xs font-semibold tracking-wide text-text-secondary uppercase">
                            <span data-translate="time">{{ __('messages.time') }}</span>
                        </th>
                        <th class="px-4 py-2 text-xs font-semibold tracking-wide text-text-secondary uppercase">
                            <span data-translate="medis_service_type">{{ __('messages.medis_service_type') }}</span>
                        </th>
                        <th class="px-4 py-2 text-xs font-semibold tracking-wide text-text-secondary uppercase">
                            <span data-translate="medis_service_detail">{{ __('messages.medis_service_detail') }}</span>
                        </th>
                        <th class="px-4 py-2 text-xs font-semibold tracking-wide text-text-secondary uppercase">
                            <span data-translate="medis_patient_name">{{ __('messages.medis_patient_name') }}</span>
                        </th>
                        <th class="px-4 py-2 text-xs font-semibold tracking-wide text-text-secondary uppercase">
                            <span data-translate="medis_payment_type">{{ __('messages.medis_payment_type') }}</span>
                        </th>
                        <th class="px-4 py-2 text-xs font-semibold tracking-wide text-text-secondary uppercase text-right">
                            <span data-translate="total">{{ __('messages.total') }}</span>
                        </th>
                        <th class="px-4 py-2 text-xs font-semibold tracking-wide text-text-secondary uppercase text-right">
                            <span data-translate="actions">{{ __('messages.actions') }}</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    <template x-if="pageRows.length === 0">
                        <tr>
                            <td colspan="8" class="px-4 py-10 text-center text-text-secondary">
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
                                    :disabled="row.medicName !== currentMedicName"
                                    @change="toggleOne(row.id, $event.target.checked)"
                                    :title="row.medicName !== currentMedicName ? t('medis_cannot_select_other', 'Cannot select other user transactions') : ''"
                                >
                            </td>
                            <td class="px-4 py-2 text-sm text-text-primary whitespace-nowrap" x-text="row.timeText"></td>
                            <td class="px-4 py-2 text-sm text-text-primary" x-text="row.serviceType"></td>
                            <td class="px-4 py-2 text-sm text-text-secondary" x-text="row.detail"></td>
                            <td class="px-4 py-2 text-sm text-text-secondary" x-text="row.patient"></td>
                            <td class="px-4 py-2 text-sm text-text-secondary" x-text="paymentText(row)"></td>
                            <td class="px-4 py-2 text-sm text-text-primary whitespace-nowrap text-right" x-text="formatMoney(row.total)"></td>
                            <td class="px-4 py-2 text-sm text-right">
                                <button
                                    type="button"
                                    class="inline-flex items-center justify-center px-3 py-1.5 text-xs font-semibold rounded-lg border border-border hover:bg-surface-hover transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                    :disabled="row.medicName !== currentMedicName"
                                    @click="deleteOne(row.id)"
                                    :title="row.medicName !== currentMedicName ? t('medis_cannot_delete_other', 'Cannot delete other user transactions') : ''"
                                >
                                    <span data-translate="delete">{{ __('messages.delete') }}</span>
                                </button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <p class="text-sm text-text-secondary">
                <span data-translate="medis_total_shown">{{ __('messages.medis_total_shown') }}</span>:
                <span class="font-semibold text-text-primary" x-text="formatMoney(totalShown)"></span>
            </p>

            <div class="flex items-center justify-end">
                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        class="inline-flex items-center justify-center px-3 py-1.5 rounded-lg border border-border hover:bg-surface-hover transition-colors text-sm font-semibold disabled:opacity-50"
                        :disabled="page <= 1"
                        @click="page = Math.max(1, page - 1)"
                    >
                        <span data-translate="previous">{{ __('messages.previous') }}</span>
                    </button>
                    <span class="text-sm text-text-secondary" x-text="page + ' / ' + pageCount"></span>
                    <button
                        type="button"
                        class="inline-flex items-center justify-center px-3 py-1.5 rounded-lg border border-border hover:bg-surface-hover transition-colors text-sm font-semibold disabled:opacity-50"
                        :disabled="page >= pageCount"
                        @click="page = Math.min(pageCount, page + 1)"
                    >
                        <span data-translate="next">{{ __('messages.next') }}</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-card>

@push('scripts')
<script>
    function emsServicesForm(config) {
        const csrf = () => document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const toInt = (v) => {
            const n = Number.parseInt(v ?? 0, 10);
            return Number.isFinite(n) ? n : 0;
        };
        const STORAGE_KEY = 'rhmc_ems_services_form_v1';

        const DETAIL_OPTIONS = {
            Pingsan: ['RS', 'Paleto', 'Gunung/Laut', 'Zona Perang', 'UFC'],
            Treatment: ['RS', 'Luar'],
            Surat: ['Kesehatan', 'Psikologi'],
            Operasi: ['Besar', 'Kecil'],
            'Rawat Inap': ['Reguler', 'VIP'],
            Kematian: ['Pemakaman', 'Kremasi'],
            Plastik: ['Operasi Plastik'],
        };

        return {
            locale: config?.locale || 'id',
            previewUrl: String(config?.previewUrl || ''),

            serviceType: String(config?.old?.service_type || ''),
            serviceDetail: String(config?.old?.service_detail || ''),
            operasiTingkat: String(config?.old?.operasi_tingkat || ''),
            patientName: String(config?.old?.patient_name || ''),
            location: String(config?.old?.location || ''),
            qty: toInt(config?.old?.qty || 1) || 1,
            paymentType: String(config?.old?.payment_type || ''),
            isGunshot: Boolean(config?.old?.is_gunshot || false),

            showDetail: true,
            showOperasiTingkat: false,
            showPatient: false,
            showLocation: false,
            showQty: false,
            showPayment: false,
            showMedicine: false,
            paymentLocked: false,
            paymentHint: '',
            detailHint: '',

            totalHtml: '$ 0',
            totalHint: '',
            _restoring: false,
            _saveTimer: null,

            init() {
                if (config?.saved) {
                    this.clearDraft();
                } else {
                    this.restoreDraft();
                }

                this.applyUiRules();
                this.refreshDetailOptions(true);

                const form = this.$refs.form;
                if (form) {
                    const handler = () => this.saveDraft();
                    form.addEventListener('input', handler);
                    form.addEventListener('change', handler);
                }

                this.preview();
            },

            t(key, fallback) {
                const table = window.globalLangState?.translations || {};
                return table?.[key] || fallback || '';
            },

            clearForm() {
                this.serviceType = '';
                this.serviceDetail = '';
                this.operasiTingkat = '';
                this.patientName = '';
                this.location = '';
                this.qty = 1;
                this.paymentType = '';
                this.isGunshot = false;

                try { this.$refs.form?.reset(); } catch { /* ignore */ }
                this.clearDraft();
                this.applyUiRules();
                this.refreshDetailOptions(true);
                this.totalHtml = '$ 0';
                this.totalHint = '';
            },

            onServiceTypeChange(restoring = false) {
                if (!restoring) {
                    this.serviceDetail = '';
                    this.operasiTingkat = '';
                    this.isGunshot = false;
                    this.patientName = '';
                    this.location = '';
                    this.qty = 1;
                    this.paymentType = '';

                    const form = this.$refs.form;
                    if (form) {
                        form.querySelectorAll('input[name="meds[]"]').forEach((el) => { el.checked = false; });
                    }
                }
                this.applyUiRules();
                this.refreshDetailOptions(false);
                this.preview();
            },

            applyUiRules() {
                const type = this.serviceType || '';
                const detail = this.serviceDetail || '';

                this.showDetail = type !== 'Plastik' && type !== '';
                this.showOperasiTingkat = type === 'Operasi';
                this.showMedicine = type === 'Pingsan' || type === 'Treatment';
                this.showPatient = ['Surat', 'Operasi', 'Rawat Inap', 'Kematian', 'Plastik'].includes(type);
                this.showQty = type === 'Rawat Inap';

                if (type === 'Kematian') {
                    this.showLocation = true;
                } else if ((type === 'Pingsan' || type === 'Treatment') && detail && detail !== 'RS') {
                    this.showLocation = true;
                } else {
                    this.showLocation = false;
                }

                this.showPayment = type !== '';
                this.paymentLocked = false;
                this.paymentHint = '';

                if (type === 'Pingsan' || type === 'Treatment' || type === 'Surat') {
                    this.paymentType = 'cash';
                    this.paymentLocked = true;
                    this.paymentHint = this.t('medis_payment_locked_cash', 'Cash is enforced for this service');
                } else if (type === 'Operasi' || type === 'Rawat Inap') {
                    this.paymentType = 'billing';
                    this.paymentLocked = true;
                    this.paymentHint = this.t('medis_payment_locked_billing', 'Billing is enforced for this service');
                } else if (type === 'Plastik') {
                    this.paymentType = 'mixed';
                    this.paymentLocked = true;
                    this.paymentHint = this.t('medis_payment_locked_mixed', 'Cash + Billing enforced for this service');
                } else if (type === 'Kematian') {
                    if (!this.paymentType) this.paymentType = 'cash';
                    this.paymentLocked = false;
                    this.paymentHint = '';
                }

                this.detailHint = type ? this.t('medis_choose_detail_hint', '') : '';
            },

            refreshDetailOptions(restoreOld) {
                const el = document.getElementById('serviceDetail');
                if (!el) return;

                const options = DETAIL_OPTIONS[this.serviceType] || [];
                el.innerHTML = '';

                const placeholder = document.createElement('option');
                placeholder.value = '';
                placeholder.textContent = this.t('medis_choose_detail', 'Choose detail');
                el.appendChild(placeholder);

                options.forEach((v) => {
                    const opt = document.createElement('option');
                    opt.value = v;
                    opt.textContent = v;
                    el.appendChild(opt);
                });

                if (restoreOld && this.serviceDetail) {
                    el.value = this.serviceDetail;
                }
            },

            clearDraft() {
                try { localStorage.removeItem(STORAGE_KEY); } catch { /* ignore */ }
            },

            saveDraft() {
                if (this._restoring) return;

                if (this._saveTimer) {
                    clearTimeout(this._saveTimer);
                }

                this._saveTimer = setTimeout(() => {
                    const form = this.$refs.form;
                    if (!form) return;

                    const meds = Array.from(form.querySelectorAll('input[name="meds[]"]:checked')).map((el) => String(el.value || ''));
                    const gunshotEl = form.querySelector('input[name="is_gunshot"]');

                    const draft = {
                        service_type: String(this.serviceType || ''),
                        service_detail: String(this.serviceDetail || ''),
                        operasi_tingkat: String(this.operasiTingkat || ''),
                        patient_name: String(this.patientName || ''),
                        location: String(this.location || ''),
                        qty: toInt(this.qty || 1) || 1,
                        payment_type: String(this.paymentType || ''),
                        is_gunshot: Boolean(gunshotEl?.checked || false),
                        meds: meds.filter((v) => v !== ''),
                        saved_at: new Date().toISOString(),
                    };

                    try {
                        localStorage.setItem(STORAGE_KEY, JSON.stringify(draft));
                    } catch { /* ignore */ }
                }, 120);
            },

            restoreDraft() {
                let raw = null;
                try { raw = localStorage.getItem(STORAGE_KEY); } catch { raw = null; }
                if (!raw) return;

                let data = null;
                try { data = JSON.parse(raw); } catch { data = null; }
                if (!data || typeof data !== 'object') return;

                this._restoring = true;

                this.serviceType = String(data.service_type || '');
                // Make sure detail options are present before setting detail.
                this.applyUiRules();
                this.refreshDetailOptions(false);

                this.serviceDetail = String(data.service_detail || '');
                this.operasiTingkat = String(data.operasi_tingkat || '');
                this.patientName = String(data.patient_name || '');
                this.location = String(data.location || '');
                this.qty = toInt(data.qty || 1) || 1;
                this.paymentType = String(data.payment_type || '');
                this.isGunshot = Boolean(data.is_gunshot || false);

                // Sync checkbox DOM (meds + gunshot) because meds[] are not bound.
                const form = this.$refs.form;
                if (form) {
                    const meds = Array.isArray(data.meds) ? data.meds.map((v) => String(v)) : [];
                    form.querySelectorAll('input[name="meds[]"]').forEach((el) => {
                        el.checked = meds.includes(String(el.value || ''));
                    });
                    const gunshotEl = form.querySelector('input[name="is_gunshot"]');
                    if (gunshotEl) gunshotEl.checked = this.isGunshot;
                }

                // Apply UI rules that depend on detail.
                this.applyUiRules();

                setTimeout(() => {
                    this._restoring = false;
                    this.preview();
                }, 50);
            },

            async preview() {
                const type = this.serviceType || '';
                if (!type) {
                    this.totalHtml = '$ 0';
                    this.totalHint = this.t('medis_choose_service_type', 'Choose service type');
                    return;
                }

                this.applyUiRules();

                const form = this.$refs.form;
                if (!form || !this.previewUrl) return;

                const data = new FormData(form);
                data.set('service_type', this.serviceType);
                if (this.paymentType) data.set('payment_type', this.paymentType);
                if (this.qty) data.set('qty', String(this.qty));
                if (this.location) data.set('location', String(this.location));
                if (this.isGunshot) data.set('is_gunshot', '1');

                try {
                    const res = await fetch(this.previewUrl, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            ...(csrf() ? { 'X-CSRF-TOKEN': csrf() } : {}),
                        },
                        body: data,
                    });
                    const json = await res.json().catch(() => null);
                    if (!res.ok || !json || !json.success) {
                        this.totalHtml = this.t('medis_cannot_calculate', 'Cannot calculate');
                        this.totalHint = (json && json.message) ? String(json.message) : this.t('medis_check_form', 'Check your input');
                        return;
                    }

                    const total = toInt(json.total || 0);
                    const med = (json.breakdown || {}).medicine || {};
                    const medCount = toInt(med.count || 0);
                    const perItem = toInt(med.per_item || 0);
                    const sub = toInt(med.subtotal || 0);

                    let html = '$ ' + total.toLocaleString(this.locale === 'id' ? 'id-ID' : 'en-US');
                    if (medCount > 0) {
                        html += `<div class="mt-1 text-xs text-text-secondary">${this.t('medis_medicine_breakdown', 'Medicine')}: ${medCount} × $ ${perItem.toLocaleString()} = $ ${sub.toLocaleString()}</div>`;
                    }
                    if (String(json.payment_type || '') === 'mixed') {
                        html += `<div class="mt-1 text-xs text-text-secondary">${this.t('medis_payment_mixed_note', 'Includes cash + billing')}</div>`;
                    }

                    this.totalHtml = html;
                    this.totalHint = '';
                } catch (e) {
                    this.totalHtml = this.t('medis_cannot_calculate', 'Cannot calculate');
                    this.totalHint = this.t('medis_preview_failed', 'Failed to fetch preview');
                }
            },
        };
    }

    function emsServicesTable(config) {
        const toInt = (v) => {
            const n = Number.parseInt(v ?? 0, 10);
            return Number.isFinite(n) ? n : 0;
        };
        const normalize = (v) => String(v ?? '').toLowerCase().replace(/\s+/g, ' ').trim();
        const csrf = () => document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const destroyUrl = (id) => String(config?.destroyUrlTemplate || '').replace(/\/0$/, '/' + String(id));

        return {
            locale: config?.locale || 'id',
            currentMedicName: String(config?.currentMedicName || ''),
            rows: (Array.isArray(config?.rows) ? config.rows : [])
                .map((r) => ({
                    id: toInt(r?.id),
                    createdAtTs: toInt(r?.createdAtTs),
                    timeText: String(r?.timeText || ''),
                    serviceType: String(r?.serviceType || ''),
                    detail: String(r?.detail || ''),
                    patient: String(r?.patient || '-'),
                    paymentType: String(r?.paymentType || ''),
                    total: toInt(r?.total),
                    medicName: String(r?.medicName || ''),
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

            formatNumber(n) {
                try {
                    return new Intl.NumberFormat(this.locale === 'id' ? 'id-ID' : 'en-US', { maximumFractionDigits: 0 }).format(Number(n || 0));
                } catch (e) {
                    return String(n || 0);
                }
            },

            formatMoney(amount) {
                return '$ ' + this.formatNumber(amount);
            },

            paymentText(row) {
                const pt = String(row?.paymentType || '');
                if (pt === 'mixed') return this.t('medis_payment_mixed', 'Mixed');
                if (pt === 'billing') return this.t('medis_payment_billing', 'Billing');
                if (pt === 'cash') return this.t('medis_payment_cash', 'Cash');
                return pt || '-';
            },

            get filteredRows() {
                const q = normalize(this.search);
                if (!q) return this.rows;
                return this.rows.filter((r) => {
                    const hay = normalize([r.timeText, r.serviceType, r.detail, r.patient, r.paymentType].join(' '));
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

            get totalShown() {
                return this.pageRows.reduce((sum, r) => sum + toInt(r.total), 0);
            },

            get allOnPageSelected() {
                const ids = this.pageRows
                    .filter((r) => r.medicName === this.currentMedicName)
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
                if (row.medicName !== this.currentMedicName) return;

                if (checked) {
                    if (!this.selectedIds.includes(saleId)) this.selectedIds = [...this.selectedIds, saleId];
                } else {
                    this.selectedIds = this.selectedIds.filter((x) => x !== saleId);
                }
            },

            toggleAllOnPage(checked) {
                const pageIds = this.pageRows
                    .filter((r) => r.medicName === this.currentMedicName)
                    .map((r) => r.id);

                if (!checked) {
                    this.selectedIds = this.selectedIds.filter((id) => !pageIds.includes(id));
                    return;
                }

                const next = new Set(this.selectedIds);
                pageIds.forEach((id) => next.add(id));
                this.selectedIds = Array.from(next);
            },

            async deleteOne(id) {
                const saleId = toInt(id);
                if (!saleId) return;
                const row = this.rows.find((r) => r.id === saleId);
                if (!row) return;
                if (row.medicName !== this.currentMedicName) return;

                const ok = confirm(this.t('medis_confirm_delete_one', 'Delete this transaction?'));
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

                    this.rows = this.rows.filter((r) => r.id !== saleId);
                    this.selectedIds = this.selectedIds.filter((x) => x !== saleId);
                    this.page = Math.min(this.page, this.pageCount);
                } catch (e) {
                    alert(this.t('medis_delete_failed', 'Failed to delete. Please refresh the page.'));
                }
            },

            async deleteSelected() {
                const ids = this.selectedIds.slice();
                if (ids.length === 0) return;

                const template = this.t('medis_confirm_delete_selected', '');
                const msg = template ? template.replace(':count', String(ids.length)) : `Delete ${ids.length} selected transactions?`;
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
                    this.rows = this.rows.filter((r) => !deletedSet.has(r.id));
                    this.selectedIds = [];
                    this.page = Math.min(this.page, this.pageCount);
                } catch (e) {
                    alert(this.t('medis_delete_selected_failed', 'Failed to delete selected rows. Please refresh the page.'));
                }
            },
        };
    }
</script>
@endpush
@endsection
