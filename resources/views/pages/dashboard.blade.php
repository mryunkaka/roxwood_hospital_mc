@extends('layouts.app')

@section('title', __('messages.dashboard') . ' - ' . __('messages.app_name'))

@section('page-title', __('messages.dashboard'))
@section('page-description', __('messages.dashboard_subtitle'))

@section('content')
@php
    $thousandSep = app()->getLocale() === 'id' ? '.' : ',';
    $decimalSep = app()->getLocale() === 'id' ? ',' : '.';
    $fmt = fn ($n) => number_format((float) $n, 0, $decimalSep, $thousandSep);
@endphp

<script>
    window.DASHBOARD_DATA = @json($dashboard);
</script>

<p class="text-sm text-text-secondary mb-6">
    {{ $rangeLabel }}
</p>

<x-section class="mb-6">
    <div class="flex items-center gap-2 mb-4">
        <svg class="w-5 h-5 text-text-tertiary" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
        </svg>
        <h3 class="text-lg font-semibold text-text-primary" data-translate="dashboard_farmasi_summary">
            {{ __('messages.dashboard_farmasi_summary') }}
        </h3>
    </div>

    <x-grid :cols="4" :gap="'default'">
        <x-stat-card :title="__('messages.dashboard_total_medic_serving')" data-translate-title="dashboard_total_medic_serving" :value="$fmt($dashboard['total_medic'] ?? 0)" />
        <x-stat-card :title="__('messages.dashboard_total_consumer_farmasi')" data-translate-title="dashboard_total_consumer_farmasi" :value="$fmt($dashboard['total_consumer'] ?? 0)" />
        <x-stat-card :title="__('messages.dashboard_paket_a_sold')" data-translate-title="dashboard_paket_a_sold" :value="$fmt($dashboard['total_paket_a'] ?? 0)" />
        <x-stat-card :title="__('messages.dashboard_paket_b_sold')" data-translate-title="dashboard_paket_b_sold" :value="$fmt($dashboard['total_paket_b'] ?? 0)" />
        <x-stat-card :title="__('messages.dashboard_bandage_sold')" data-translate-title="dashboard_bandage_sold" :value="$fmt($dashboard['total_bandage'] ?? 0)" />
        <x-stat-card :title="__('messages.dashboard_painkiller_sold')" data-translate-title="dashboard_painkiller_sold" :value="$fmt($dashboard['total_painkiller'] ?? 0)" />
        <x-stat-card :title="__('messages.dashboard_ifaks_sold')" data-translate-title="dashboard_ifaks_sold" :value="$fmt($dashboard['total_ifaks'] ?? 0)" />
        <x-stat-card :title="__('messages.dashboard_total_transactions')" data-translate-title="dashboard_total_transactions" :value="$fmt($dashboard['total_transaksi'] ?? 0)" />
        <x-stat-card :title="__('messages.dashboard_total_items_sold')" data-translate-title="dashboard_total_items_sold" :value="$fmt($dashboard['total_item'] ?? 0)" />
        <x-stat-card :title="__('messages.dashboard_total_income')" data-translate-title="dashboard_total_income" :value="'$ ' . $fmt($dashboard['total_income'] ?? 0)" />
        <x-stat-card :title="__('messages.dashboard_medic_bonus_40')" data-translate-title="dashboard_medic_bonus_40" :value="'$ ' . $fmt($dashboard['total_bonus'] ?? 0)" />
        <x-stat-card :title="__('messages.dashboard_company_profit_60')" data-translate-title="dashboard_company_profit_60" :value="'$ ' . $fmt($dashboard['company_profit'] ?? 0)" />
    </x-grid>
</x-section>

<x-section class="mb-6">
    <div class="flex items-center gap-2 mb-4">
        <svg class="w-5 h-5 text-text-tertiary" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
        </svg>
        <h3 class="text-lg font-semibold text-text-primary" data-translate="dashboard_medis_summary">
            {{ __('messages.dashboard_medis_summary') }}
        </h3>
    </div>

    <x-grid :cols="4" :gap="'default'">
        <x-stat-card :title="__('messages.dashboard_total_p3k')" data-translate-title="dashboard_total_p3k" :value="$fmt($dashboard['rekap_medis']['p3k'] ?? 0)" />
        <x-stat-card :title="__('messages.dashboard_total_bandage')" data-translate-title="dashboard_total_bandage" :value="$fmt($dashboard['rekap_medis']['bandage'] ?? 0)" />
        <x-stat-card :title="__('messages.dashboard_total_gauze')" data-translate-title="dashboard_total_gauze" :value="$fmt($dashboard['rekap_medis']['gauze'] ?? 0)" />
        <x-stat-card :title="__('messages.dashboard_total_iodine')" data-translate-title="dashboard_total_iodine" :value="$fmt($dashboard['rekap_medis']['iodine'] ?? 0)" />
        <x-stat-card :title="__('messages.dashboard_total_syringe')" data-translate-title="dashboard_total_syringe" :value="$fmt($dashboard['rekap_medis']['syringe'] ?? 0)" />
        <x-stat-card :title="__('messages.dashboard_operasi_plastik')" data-translate-title="dashboard_operasi_plastik" :value="$fmt($dashboard['rekap_medis']['operasi_plastik'] ?? 0)" />
        <x-stat-card :title="__('messages.dashboard_operasi_ringan')" data-translate-title="dashboard_operasi_ringan" :value="$fmt($dashboard['rekap_medis']['operasi_ringan'] ?? 0)" />
        <x-stat-card :title="__('messages.dashboard_operasi_berat')" data-translate-title="dashboard_operasi_berat" :value="$fmt($dashboard['rekap_medis']['operasi_berat'] ?? 0)" />
    </x-grid>
</x-section>

<x-card class="mb-6">
    <div class="flex items-center gap-2 mb-4">
        <svg class="w-5 h-5 text-text-tertiary" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
        </svg>
        <h3 class="text-lg font-semibold text-text-primary" data-translate="dashboard_weekly_sales_company">
            {{ __('messages.dashboard_weekly_sales_company') }}
        </h3>
    </div>
    <div class="h-72">
        <canvas
            x-data="chartController()"
            x-init="(() => {
                type = 'bar'
                const weekly = window.DASHBOARD_DATA?.chart_weekly ?? { labels: [], values: [] }
                data = window.ChartData.barChart(weekly.labels, [{
                    label: @js(__('messages.dashboard_weekly_profit_100')),
                    data: weekly.values,
                    color: '#0ea5e9'
                }])
                options = {
                    plugins: { legend: { display: false } },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: (value) => '$ ' + Number(value).toLocaleString(window.globalLangState?.currentLang === 'id' ? 'id-ID' : 'en-US')
                            }
                        }
                    }
                }
            })()"
        ></canvas>
    </div>
</x-card>

<x-card>
    <div class="flex items-center gap-2 mb-4">
        <svg class="w-5 h-5 text-text-tertiary" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
        </svg>
        <h3 class="text-lg font-semibold text-text-primary" data-translate="dashboard_weekly_winner_medic">
            {{ __('messages.dashboard_weekly_winner_medic') }}
        </h3>
    </div>
    <x-grid :cols="4" :gap="'default'">
        @foreach(($dashboard['weekly_winner'] ?? []) as $weekLabel => $data)
            <x-card padding="default" shadow="sm" :border="true">
                <p class="text-sm font-semibold text-text-primary mb-2">{{ $weekLabel }}</p>
                <p class="text-sm text-text-secondary mb-1">{{ $data['medic'] ?? '-' }}</p>
                <p class="text-sm text-text-tertiary">
                    <span data-translate="dashboard_bonus_prefix">{{ __('messages.dashboard_bonus_prefix') }}</span>
                    <span class="font-medium">$ {{ $fmt($data['bonus_40'] ?? 0) }}</span>
                </p>
            </x-card>
        @endforeach
    </x-grid>
</x-card>

@endsection
