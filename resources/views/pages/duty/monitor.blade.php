{{-- Monitoring: Jam Duty (Farmasi + Medis) --}}
@extends('layouts.app')

@section('title', __('messages.duty_monitor_title') . ' - ' . ($appName ?? config('app.name')))
@section('page-title', __('messages.duty_monitor_title'))
@section('page-description', __('messages.duty_monitor_subtitle'))

@section('content')
@php
    $thousandSep = app()->getLocale() === 'id' ? '.' : ',';
    $decimalSep = app()->getLocale() === 'id' ? ',' : '.';
    $fmtInt = fn ($n) => number_format((float) $n, 0, $decimalSep, $thousandSep);

    $fmtDuration = function (int $seconds) {
        $seconds = max(0, $seconds);
        $minutes = (int) floor($seconds / 60);
        $h = (int) floor($minutes / 60);
        $m = $minutes % 60;
        if ($h > 0) {
            return sprintf('%dh %dm', $h, $m);
        }
        return sprintf('%dm', $minutes);
    };

    $fmtClock = function (int $seconds) {
        $seconds = max(0, $seconds);
        $h = (int) floor($seconds / 3600);
        $m = (int) floor(($seconds % 3600) / 60);
        $s = (int) ($seconds % 60);
        return sprintf('%02d:%02d:%02d', $h, $m, $s);
    };

    $rangeOptions = [
        'week1' => __('messages.range_3_weeks_ago'),
        'week2' => __('messages.range_2_weeks_ago'),
        'week3' => __('messages.range_last_week'),
        'week4' => __('messages.range_this_week'),
        'custom' => __('messages.range_custom'),
    ];

    $rowsForJs = collect($rows)->map(function ($r) {
        $last = $r['last_activity_at'] ?? null;
        $offlineAt = $r['auto_offline_at'] ?? null;

        $lastTs = 0;
        $offTs = 0;
        $lastText = '-';
        $offText = '-';

        if ($last) {
            try {
                $dt = \Carbon\Carbon::parse($last)->locale(app()->getLocale());
                $lastTs = (int) $dt->timestamp;
                $lastText = (string) $dt->translatedFormat('d M Y H:i:s');
            } catch (\Throwable $e) {
                // ignore
            }
        }

        if ($offlineAt) {
            try {
                $dt = \Carbon\Carbon::parse($offlineAt)->locale(app()->getLocale());
                $offTs = (int) $dt->timestamp;
                $offText = (string) $dt->translatedFormat('d M Y H:i:s');
            } catch (\Throwable $e) {
                // ignore
            }
        }

        return [
            'id' => (int) ($r['id'] ?? 0),
            'name' => (string) ($r['full_name'] ?? '-'),
            'position' => (string) ($r['position'] ?? '-'),
            'status' => (string) ($r['status'] ?? 'offline'),
            'lastActivityText' => $lastText,
            'autoOfflineText' => $offText,
            'dutyTotalSeconds' => (int) ($r['duty_seconds_total'] ?? 0),
            'dutyFarmasiSeconds' => (int) ($r['duty_seconds_farmasi'] ?? 0),
            'dutyMedisSeconds' => (int) ($r['duty_seconds_medis'] ?? 0),
            'trxFarmasi' => (int) ($r['trx_count_farmasi'] ?? 0),
            'trxMedis' => (int) ($r['trx_count_medis'] ?? 0),
            'sortTs' => max($offTs, $lastTs),
        ];
    })->values();
@endphp

@if(!empty($meta['unmapped_ems_names']))
    <x-alert type="warning" class="mb-4">
        <span data-translate="duty_unmapped_ems_notice">{{ __('messages.duty_unmapped_ems_notice') }}</span>
        <div class="mt-2 text-xs text-text-secondary space-y-1">
            @foreach(collect($meta['unmapped_ems_names'])->sortDesc()->take(8) as $name => $count)
                <div class="flex items-center justify-between gap-3">
                    <span class="truncate">{{ $name }}</span>
                    <span class="shrink-0 tabular-nums">{{ $fmtInt($count) }}</span>
                </div>
            @endforeach
        </div>
    </x-alert>
@endif

<x-card class="mb-6">
    <div class="flex items-center gap-2 mb-4">
        <svg class="w-5 h-5 text-text-tertiary" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        <h3 class="text-lg font-semibold text-text-primary" data-translate="duty_filter_title">{{ __('messages.duty_filter_title') }}</h3>
    </div>

    <form method="get" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end" x-data="{ range: @js($range) }">
        <div>
            <x-select
                name="range"
                :label="__('messages.farmasi_date_range')"
                dataTranslateLabel="farmasi_date_range"
                x-model="range"
                :options="$rangeOptions"
                :value="$range"
            />
        </div>

        <div x-show="range === 'custom'" x-cloak>
            <x-input type="date" name="from" :label="__('messages.range_from')" dataTranslateLabel="range_from" :value="$fromInput" />
        </div>

        <div x-show="range === 'custom'" x-cloak>
            <x-input type="date" name="to" :label="__('messages.range_to')" dataTranslateLabel="range_to" :value="$toInput" />
        </div>

        <div class="md:col-span-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-2">
                <x-button type="submit" variant="secondary">
                    <span data-translate="apply_filter">{{ __('messages.apply_filter') }}</span>
                </x-button>
                <a href="{{ route('duty.monitor') }}"
                   class="inline-flex items-center justify-center px-4 py-2 rounded-xl border border-border bg-surface hover:bg-surface-hover text-text-secondary transition-colors">
                    <span data-translate="reset_filter">{{ __('messages.reset_filter') }}</span>
                </a>
            </div>

            <p class="text-sm text-text-secondary">
                <span data-translate="farmasi_active_range">{{ __('messages.farmasi_active_range') }}</span>:
                <span class="font-semibold text-text-primary">{{ $rangeLabel }}</span>
            </p>
        </div>
    </form>
</x-card>

@if(!empty($me))
    @php
        $meStatus = (string) ($me['status'] ?? 'offline');
        $meBadge = $meStatus === 'active' ? 'success' : 'danger';
        $meStatusText = $meStatus === 'active' ? __('messages.duty_status_active') : __('messages.duty_status_offline');
    @endphp
    <x-card class="mb-6">
        <div class="flex items-start justify-between gap-3 mb-4">
            <div class="min-w-0">
                <h3 class="text-lg font-semibold text-text-primary" data-translate="duty_my_title">{{ __('messages.duty_my_title') }}</h3>
                <p class="text-sm text-text-secondary break-words">
                    <span class="font-semibold text-text-primary">{{ $me['name'] ?? '-' }}</span>
                    <span class="text-text-tertiary">·</span>
                    <span>{{ $me['position'] ?? '-' }}</span>
                </p>
            </div>
            <x-badge :variant="$meBadge" dot>
                {{ $meStatusText }}
            </x-badge>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <x-stat-card
                :title="__('messages.duty_since_join')"
                dataTranslateTitle="duty_since_join"
                :value="$fmtClock((int) ($me['duty_seconds_all'] ?? 0))"
                color="primary"
            />
            <x-stat-card
                :title="__('messages.duty_in_period')"
                dataTranslateTitle="duty_in_period"
                :value="$fmtClock((int) ($me['duty_seconds_period'] ?? 0))"
                color="info"
            />
            <x-stat-card
                :title="__('messages.duty_trx_since_join')"
                dataTranslateTitle="duty_trx_since_join"
                :value="$fmtInt((int) ($me['trx_count_all'] ?? 0))"
                color="warning"
            />
            <x-stat-card
                :title="__('messages.duty_trx_in_period')"
                dataTranslateTitle="duty_trx_in_period"
                :value="$fmtInt((int) ($me['trx_count_period'] ?? 0))"
                color="warning"
            />
        </div>

        <div class="mt-4 rounded-2xl bg-surface-alt border border-border p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="min-w-0">
                <p class="text-xs text-text-tertiary" data-translate="duty_week_realtime">{{ __('messages.duty_week_realtime') }}</p>
                <p
                    class="mt-1 text-2xl font-bold text-text-primary tabular-nums tracking-tight"
                    x-data="dutyRealtimeCounter({
                        baseSeconds: @js((int) ($me['duty_seconds_week_elapsed'] ?? 0)),
                        serverNowMs: @js((int) ($me['server_now_ms'] ?? 0)),
                        autoOfflineMs: @js($me['auto_offline_ms'] ?? null),
                        active: @js(($me['status'] ?? 'offline') === 'active'),
                    })"
                    x-init="init()"
                    x-text="clockText"
                ></p>
            </div>
            <div class="text-xs text-text-secondary tabular-nums space-y-1">
                <div class="flex items-center justify-between gap-3">
                    <span data-translate="duty_last_activity">{{ __('messages.duty_last_activity') }}</span>
                    <span class="font-medium text-text-primary">
                        @php
                            $meLast = $me['last_activity_at'] ?? null;
                            $meLastText = '-';
                            if ($meLast instanceof \Carbon\CarbonInterface) {
                                $meLastText = $meLast->copy()->locale(app()->getLocale())->translatedFormat('d M Y H:i:s');
                            } elseif (!empty($meLast)) {
                                try { $meLastText = \Carbon\Carbon::parse($meLast)->locale(app()->getLocale())->translatedFormat('d M Y H:i:s'); } catch (\Throwable $e) {}
                            }
                        @endphp
                        {{ $meLastText }}
                    </span>
                </div>
                <div class="flex items-center justify-between gap-3">
                    <span data-translate="duty_auto_offline">{{ __('messages.duty_auto_offline') }}</span>
                    <span class="font-medium text-text-primary">
                        @php
                            $meOff = $me['auto_offline_at'] ?? null;
                            $meOffText = '-';
                            if ($meOff instanceof \Carbon\CarbonInterface) {
                                $meOffText = $meOff->copy()->locale(app()->getLocale())->translatedFormat('d M Y H:i:s');
                            } elseif (!empty($meOff)) {
                                try { $meOffText = \Carbon\Carbon::parse($meOff)->locale(app()->getLocale())->translatedFormat('d M Y H:i:s'); } catch (\Throwable $e) {}
                            }
                        @endphp
                        {{ $meOffText }}
                    </span>
                </div>
            </div>
        </div>
    </x-card>
@endif

<x-card>
    <div
        x-data="dutyMonitorTable({
            rows: @js($rowsForJs),
            locale: @js(app()->getLocale()),
        })"
        x-cloak
        class="space-y-4"
    >
        <div class="flex items-center gap-2 mb-4">
            <svg class="w-5 h-5 text-text-tertiary" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <h3 class="text-lg font-semibold text-text-primary" data-translate="duty_monitor_title">{{ __('messages.duty_monitor_title') }}</h3>
        </div>

        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
            <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                <div class="relative w-full sm:w-72">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input
                        type="text"
                        class="w-full pl-9 pr-4 py-2 rounded-xl bg-surface border border-border focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none transition-all text-sm placeholder:text-text-hint"
                        :placeholder="t('duty_search_placeholder', '{{ __('messages.duty_search_placeholder') }}')"
                        data-translate-placeholder="duty_search_placeholder"
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
                        <option :value="10" value="10">10</option>
                        <option :value="25" value="25">25</option>
                        <option :value="50" value="50">50</option>
                        <option :value="100" value="100">100</option>
                    </select>
                    <span class="text-xs text-text-secondary" data-translate="farmasi_rows">{{ __('messages.farmasi_rows') }}</span>
                </div>
            </div>
        </div>

        <div class="mt-2">
            <x-table :headers="[
                ['label' => '#'],
                ['label' => __('messages.name')],
                ['label' => __('messages.position')],
                ['label' => __('messages.status')],
                ['label' => __('messages.duty_last_activity')],
                ['label' => __('messages.duty_auto_offline')],
                ['label' => __('messages.duty_time_total')],
                ['label' => __('messages.duty_time_farmasi')],
                ['label' => __('messages.duty_time_medis')],
                ['label' => __('messages.duty_trx_farmasi')],
                ['label' => __('messages.duty_trx_medis')],
            ]" :striped="false" :bordered="true" :compact="true">
                <template x-if="pageRows.length === 0">
                    <tr>
                        <td colspan="11" class="px-4 py-10 text-center text-text-secondary">
                            <span data-translate="no_data">{{ __('messages.no_data') }}</span>
                        </td>
                    </tr>
                </template>

                <template x-for="(row, idx) in pageRows" :key="row.id">
                    <tr>
                        <td class="px-4 py-2 text-sm text-text-secondary" x-text="(page - 1) * pageSize + idx + 1"></td>
                        <td class="px-4 py-2 text-sm text-text-primary">
                            <div class="font-semibold" x-text="row.name"></div>
                            <div class="text-xs text-text-tertiary" x-text="'#' + row.id"></div>
                        </td>
                        <td class="px-4 py-2 text-sm text-text-secondary" x-text="row.position"></td>
                        <td class="px-4 py-2 text-sm">
                            <template x-if="row.status === 'active'">
                                <x-badge variant="success" dot>
                                    <span x-text="t('duty_status_active', '{{ __('messages.duty_status_active') }}')"></span>
                                </x-badge>
                            </template>
                            <template x-if="row.status !== 'active'">
                                <x-badge variant="danger" dot>
                                    <span x-text="t('duty_status_offline', '{{ __('messages.duty_status_offline') }}')"></span>
                                </x-badge>
                            </template>
                        </td>
                        <td class="px-4 py-2 text-sm text-text-secondary whitespace-nowrap tabular-nums" x-text="row.lastActivityText"></td>
                        <td class="px-4 py-2 text-sm text-text-secondary whitespace-nowrap tabular-nums" x-text="row.autoOfflineText"></td>
                        <td class="px-4 py-2 text-sm text-text-primary whitespace-nowrap tabular-nums font-semibold text-right" x-text="dutyText(row.dutyTotalSeconds)"></td>
                        <td class="px-4 py-2 text-sm text-text-secondary whitespace-nowrap tabular-nums text-right" x-text="dutyText(row.dutyFarmasiSeconds)"></td>
                        <td class="px-4 py-2 text-sm text-text-secondary whitespace-nowrap tabular-nums text-right" x-text="dutyText(row.dutyMedisSeconds)"></td>
                        <td class="px-4 py-2 text-sm text-text-secondary tabular-nums text-right" x-text="row.trxFarmasi"></td>
                        <td class="px-4 py-2 text-sm text-text-secondary tabular-nums text-right" x-text="row.trxMedis"></td>
                    </tr>
                </template>
            </x-table>
        </div>

        <div class="mt-2 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 text-sm text-text-secondary">
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
@endsection
