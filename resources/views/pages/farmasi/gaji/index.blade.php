{{-- Farmasi: Gaji Mingguan --}}
@extends('layouts.app')

@section('title', __('messages.farmasi_gaji_title') . ' - ' . ($appName ?? config('app.name')))
@section('page-title', __('messages.farmasi_gaji_title'))
@section('page-description', __('messages.farmasi_gaji_subtitle'))

@section('content')
@php
    $thousandSep = app()->getLocale() === 'id' ? '.' : ',';
    $decimalSep = app()->getLocale() === 'id' ? ',' : '.';
    $fmt = fn ($n) => number_format((float) $n, 0, $decimalSep, $thousandSep);

    $rowsForJs = $salary->map(function ($r) use ($fmt) {
        $periodText = '';
        try {
            $start = \Carbon\Carbon::parse($r->period_start);
            $end = \Carbon\Carbon::parse($r->period_end);
            $periodText = $start->locale(app()->getLocale())->translatedFormat('d M Y') . ' - ' . $end->locale(app()->getLocale())->translatedFormat('d M Y');
        } catch (\Throwable $e) {
            $periodText = (string) ($r->period_start ?? '-') . ' - ' . (string) ($r->period_end ?? '-');
        }

        $paidAtText = '';
        if (!empty($r->paid_at)) {
            try {
                $paidAtText = \Carbon\Carbon::parse($r->paid_at)->locale(app()->getLocale())->translatedFormat('d M Y H:i');
            } catch (\Throwable $e) {
                $paidAtText = (string) $r->paid_at;
            }
        }

        return [
            'id' => (int) $r->id,
            'medic' => (string) ($r->medic_name ?? '-'),
            'jabatan' => (string) ($r->medic_jabatan ?? '-'),
            'period' => $periodText,
            'periodEndTs' => !empty($r->period_end) ? (int) \Carbon\Carbon::parse($r->period_end)->endOfDay()->timestamp : 0,
            'bonus' => (int) ($r->bonus_40 ?? 0),
            'bonusText' => '$ ' . $fmt((int) ($r->bonus_40 ?? 0)),
            'status' => (string) ($r->status ?? 'pending'),
            'paidBy' => (string) ($r->paid_by ?? '-'),
            'paidAtText' => $paidAtText,
        ];
    })->values();
@endphp

@if(request()->query('generated'))
    <x-alert type="success" class="mb-4">
        <span data-translate="salary_generated_success">{{ __('messages.salary_generated_success') }}</span>:
        <span class="font-semibold text-text-primary">{{ request()->query('generated') }}</span>
    </x-alert>
@elseif(request()->query('msg') === 'nosales')
    <x-alert type="warning" class="mb-4">
        <span data-translate="salary_no_sales">{{ __('messages.salary_no_sales') }}</span>
    </x-alert>
@endif

@if($isStaff)
    <x-alert type="info" class="mb-4">
        <span data-translate="salary_staff_notice">{{ __('messages.salary_staff_notice') }}</span>
    </x-alert>
@endif

@if(!$isStaff && $range !== 'all')
    <x-card class="mb-6">
        <div class="flex items-center gap-2 mb-4">
            <svg class="w-5 h-5 text-text-tertiary" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <h3 class="text-lg font-semibold text-text-primary" data-translate="salary_filter_title">{{ __('messages.salary_filter_title') }}</h3>
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

            <div class="md:col-span-3 flex items-center gap-2">
                <x-button type="submit" variant="secondary">
                    <span data-translate="apply_filter">{{ __('messages.apply_filter') }}</span>
                </x-button>
            </div>
        </form>

        <p class="text-sm text-text-secondary mt-4">
            <span data-translate="farmasi_active_range">{{ __('messages.farmasi_active_range') }}</span>:
            <span class="font-semibold text-text-primary">{{ $rangeLabel }}</span>
        </p>
    </x-card>

    <x-card class="mb-6" title="{{ __('messages.salary_summary_title') }}" subtitle="{{ __('messages.salary_summary_subtitle') }}">
        <x-grid :cols="5" :gap="'default'">
            <x-stat-card :title="__('messages.salary_total_transactions')" dataTranslateTitle="salary_total_transactions" color="info">
                {{ (int) ($rekap['total_transaksi'] ?? 0) }}
            </x-stat-card>
            <x-stat-card :title="__('messages.salary_total_revenue')" dataTranslateTitle="salary_total_revenue" color="primary">
                $ {{ $fmt((int) ($rekap['total_rupiah'] ?? 0)) }}
            </x-stat-card>
            <x-stat-card :title="__('messages.salary_total_bonus_40')" dataTranslateTitle="salary_total_bonus_40" color="success">
                $ {{ $fmt((int) ($rekap['total_bonus'] ?? 0)) }}
            </x-stat-card>
            <x-stat-card :title="__('messages.salary_paid_bonus')" dataTranslateTitle="salary_paid_bonus" color="success">
                $ {{ $fmt((int) ($rekap['paid_bonus'] ?? 0)) }}
            </x-stat-card>
            <x-stat-card :title="__('messages.salary_remaining_bonus')" dataTranslateTitle="salary_remaining_bonus" color="warning">
                $ {{ $fmt((int) ($rekap['sisa_bonus'] ?? 0)) }}
            </x-stat-card>
        </x-grid>
    </x-card>
@endif

<x-card>
    <div
        x-data="gajiTable({
            rows: @js($rowsForJs),
            locale: @js(app()->getLocale()),
            isStaff: @js($isStaff),
            canGenerateManual: @js($canGenerateManual),
            canPay: @js(!$isStaff),
            payUrl: @js(route('api.gaji.pay')),
            userSearchUrl: @js(route('api.users.search')),
            generateUrl: @js(route('farmasi.gaji.generate_manual')),
        })"
        class="space-y-4"
    >
    <div class="flex items-center gap-2 mb-4">
        <svg class="w-5 h-5 text-text-tertiary" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-10V6m0 12v-2m9-4a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <h3 class="text-lg font-semibold text-text-primary" data-translate="salary_list_title">{{ __('messages.salary_list_title') }}</h3>
    </div>

    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
        <div class="flex flex-col sm:flex-row sm:items-center gap-2">
            <div class="relative w-full sm:w-72">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text"
                       class="w-full pl-9 pr-4 py-2 rounded-xl bg-surface border border-border focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none transition-all text-sm placeholder:text-text-hint"
                       :placeholder="t('salary_search_placeholder', '{{ __('messages.salary_search_placeholder') }}')"
                       data-translate-placeholder="salary_search_placeholder"
                       x-model.trim="search"
                       @input.debounce.150ms="page = 1">
            </div>

            <div class="flex items-center gap-2">
                <span class="text-xs text-text-secondary" data-translate="farmasi_show">{{ __('messages.farmasi_show') }}</span>
                <select class="rounded-xl bg-surface border border-border px-3 py-2 text-sm outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20"
                        x-model.number="pageSize"
                        @change="page = 1">
                    <option :value="10" value="10">10</option>
                    <option :value="25" value="25">25</option>
                    <option :value="50" value="50">50</option>
                    <option :value="100" value="100">100</option>
                </select>
                <span class="text-xs text-text-secondary" data-translate="farmasi_rows">{{ __('messages.farmasi_rows') }}</span>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center gap-2">
            <x-button type="button" variant="secondary" @click="exportTxt(true)">
                <span class="inline-flex items-center gap-2">
                    <span class="w-4 h-4 shrink-0" aria-hidden="true">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v12m0 0l-3-3m3 3l3-3M4 17v3a1 1 0 001 1h14a1 1 0 001-1v-3"/>
                        </svg>
                    </span>
                    <span>
                        <span data-translate="export">{{ __('messages.export') }}</span>
                        <span x-text="' (' + pageRows.length + ')'"></span>
                    </span>
                </span>
            </x-button>

            <template x-if="canGenerateManual">
                <x-button type="button" variant="warning" @click="generateManual()">
                    <span data-translate="salary_generate_manual">{{ __('messages.salary_generate_manual') }}</span>
                </x-button>
            </template>
        </div>
    </div>

    <div class="mt-4">
        <x-table :headers="[
            ['label' => '#'],
            ['label' => __('messages.medic_name')],
            ['label' => __('messages.position')],
            ['label' => __('messages.salary_period')],
            ['label' => __('messages.salary_bonus')],
            ['label' => __('messages.salary_status')],
            ['label' => __('messages.salary_paid_by')],
            ['label' => __('messages.actions')],
        ]" :striped="false" :bordered="true" :compact="true">
            <template x-if="pageRows.length === 0">
                <tr>
                    <td colspan="8" class="px-4 py-10 text-center text-text-secondary">
                        <span data-translate="no_data">{{ __('messages.no_data') }}</span>
                    </td>
                </tr>
            </template>

            <template x-for="(row, idx) in pageRows" :key="row.id">
                <tr>
                    <td class="px-4 py-2 text-sm text-text-secondary" x-text="(page - 1) * pageSize + idx + 1"></td>
                    <td class="px-4 py-2 text-sm text-text-primary" x-text="row.medic"></td>
                    <td class="px-4 py-2 text-sm text-text-secondary" x-text="row.jabatan"></td>
                    <td class="px-4 py-2 text-sm text-text-secondary whitespace-nowrap" x-text="row.period"></td>
	                    <td class="px-4 py-2 text-sm text-text-primary whitespace-nowrap text-right" x-text="row.bonusText"></td>
	                    <td class="px-4 py-2 text-sm">
	                        <template x-if="row.status === 'paid'">
	                            <x-badge variant="success">
	                                <span x-text="t('salary_paid', '{{ __('messages.salary_paid') }}')"></span>
	                            </x-badge>
	                        </template>
	                        <template x-if="row.status !== 'paid'">
	                            <x-badge variant="warning">
	                                <span x-text="t('salary_pending', '{{ __('messages.salary_pending') }}')"></span>
	                            </x-badge>
	                        </template>
	                    </td>
                    <td class="px-4 py-2 text-sm text-text-secondary">
                        <div x-text="row.paidBy || '-'"></div>
                        <div class="text-xs text-text-tertiary mt-0.5" x-show="row.paidAtText" x-text="row.paidAtText" x-cloak></div>
                    </td>
                    <td class="px-4 py-2 text-sm text-right">
                        <template x-if="canPay && row.status !== 'paid'">
                            <x-button type="button" size="sm" variant="success" @click="openPay(row)">
                                <span data-translate="salary_pay">{{ __('messages.salary_pay') }}</span>
                            </x-button>
                        </template>
                        <template x-if="!canPay || row.status === 'paid'">
                            <span class="text-text-tertiary">-</span>
                        </template>
                    </td>
                </tr>
            </template>

            <tr class="bg-surface-alt border-t border-border">
                <td colspan="4" class="px-4 py-2 text-sm font-semibold text-text-primary">
                    <span data-translate="farmasi_total_shown">{{ __('messages.farmasi_total_shown') }}</span>
                </td>
                <td class="px-4 py-2 text-sm font-semibold text-text-primary text-right whitespace-nowrap" x-text="formatMoney(pageTotals.bonus)"></td>
                <td colspan="3" class="px-4 py-2"></td>
            </tr>
        </x-table>
    </div>

    <div class="mt-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 text-sm text-text-secondary">
        <div>
            <span data-translate="showing">{{ __('messages.showing') }}</span>
            <span class="font-semibold text-text-primary" x-text="pageRows.length"></span>
            <span data-translate="of">{{ __('messages.of') }}</span>
            <span class="font-semibold text-text-primary" x-text="filteredRows.length"></span>
            <span data-translate="results">{{ __('messages.results') }}</span>
        </div>
        <div class="flex items-center gap-2">
            <button type="button"
                    class="px-3 py-2 rounded-xl border border-border hover:bg-surface-hover transition-colors disabled:opacity-60 disabled:cursor-not-allowed"
                    :disabled="page <= 1"
                    @click="page = Math.max(1, page - 1)">
                <span data-translate="previous">{{ __('messages.previous') }}</span>
            </button>
            <span class="text-xs">
                <span x-text="page"></span>/<span x-text="pageCount"></span>
            </span>
            <button type="button"
                    class="px-3 py-2 rounded-xl border border-border hover:bg-surface-hover transition-colors disabled:opacity-60 disabled:cursor-not-allowed"
                    :disabled="page >= pageCount"
                    @click="page = Math.min(pageCount, page + 1)">
                <span data-translate="next">{{ __('messages.next') }}</span>
            </button>
        </div>
    </div>

    <x-modal title="{{ __('messages.salary_pay_title') }}" x-model="payOpen">
        <div class="space-y-4">
            <div class="rounded-2xl bg-surface-alt border border-border p-4">
                <p class="text-xs text-text-tertiary" data-translate="salary_pay_target">{{ __('messages.salary_pay_target') }}</p>
                <div class="mt-1 flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-text-primary break-words" x-text="payTarget?.medic || '-'"></p>
                        <p class="text-xs text-text-secondary mt-1" x-text="payTarget?.period || ''"></p>
                    </div>
                <div class="text-sm font-bold text-success-700 theme-dark:text-success-300 whitespace-nowrap" x-text="payTarget?.bonusText || ''"></div>
                </div>
            </div>

            <div class="space-y-2">
                <p class="text-sm font-semibold text-text-primary" data-translate="salary_pay_method">{{ __('messages.salary_pay_method') }}</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    <label class="flex items-center gap-2 rounded-xl border border-border bg-surface px-3 py-2 cursor-pointer hover:bg-surface-hover transition-colors">
                        <input type="radio" class="rounded border-border" value="direct" x-model="payMethod">
                        <span class="text-sm text-text-primary" data-translate="salary_pay_direct">{{ __('messages.salary_pay_direct') }}</span>
                    </label>
                    <label class="flex items-center gap-2 rounded-xl border border-border bg-surface px-3 py-2 cursor-pointer hover:bg-surface-hover transition-colors">
                        <input type="radio" class="rounded border-border" value="titip" x-model="payMethod">
                        <span class="text-sm text-text-primary" data-translate="salary_pay_titip">{{ __('messages.salary_pay_titip') }}</span>
                    </label>
                </div>
            </div>

            <div x-show="payMethod === 'titip'" x-cloak class="space-y-2">
                <x-input
                    name="titip_to"
                    :label="__('messages.salary_pay_titip_to')"
                    dataTranslateLabel="salary_pay_titip_to"
                    :placeholder="__('messages.salary_pay_titip_placeholder')"
                    dataTranslatePlaceholder="salary_pay_titip_placeholder"
                    data-titip-input
                    x-model.trim="titipQuery"
                    @input.debounce.250ms="searchTitip()"
                    autocomplete="off"
                />

                <div class="relative" x-show="titipOpen" x-cloak data-titip-dropdown>
                    <div class="absolute z-50 w-full max-h-64 overflow-auto rounded-xl border border-border bg-surface shadow-lg">
                        <template x-if="titipLoading">
                            <div class="px-4 py-3 text-sm text-text-secondary">{{ __('messages.loading') }}</div>
                        </template>
                        <template x-if="!titipLoading && titipResults.length === 0">
                            <div class="px-4 py-3 text-sm text-text-secondary">{{ __('messages.no_data') }}</div>
                        </template>
                        <template x-for="u in titipResults" :key="u.id">
                            <button type="button"
                                    class="w-full text-left px-4 py-3 hover:bg-surface-hover transition-colors"
                                    @click="selectTitip(u)">
                                <div class="text-sm font-semibold text-text-primary" x-text="u.full_name"></div>
                                <div class="text-xs text-text-secondary" x-text="u.position || '-'"></div>
                            </button>
                        </template>
                    </div>
                </div>

                <p class="text-xs text-text-tertiary" data-translate="salary_pay_titip_hint">{{ __('messages.salary_pay_titip_hint') }}</p>
            </div>

            <div class="flex items-center justify-end gap-2">
                <x-button type="button" variant="secondary" @click="payOpen = false">
                    <span data-translate="cancel">{{ __('messages.cancel') }}</span>
                </x-button>
                <x-button type="button" variant="success" x-bind:disabled="paySubmitting" @click="submitPay()">
                    <span data-translate="salary_pay_process">{{ __('messages.salary_pay_process') }}</span>
                </x-button>
            </div>
        </div>
    </x-modal>
    </div>
</x-card>
@endsection
