{{-- Farmasi: Konsumen --}}
@extends('layouts.app')

@section('title', __('messages.farmasi_konsumen_title') . ' - ' . __('messages.app_name'))

@section('page-title', __('messages.farmasi_konsumen_title'))
@section('page-description', __('messages.farmasi_konsumen_subtitle'))

@section('content')
@php
    $thousandSep = app()->getLocale() === 'id' ? '.' : ',';
    $decimalSep = app()->getLocale() === 'id' ? ',' : '.';
    $fmt = fn ($n) => number_format((float) $n, 0, $decimalSep, $thousandSep);
@endphp

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
        <div class="md:col-span-3">
            <x-input
                name="q"
                :label="__('messages.search')"
                dataTranslateLabel="search"
                :placeholder="__('messages.farmasi_konsumen_search_placeholder')"
                :value="$q"
            />
        </div>

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

            @if($canShowAll)
                <a href="{{ route('farmasi.konsumen', array_merge(request()->except('show_all'), ['show_all' => $showAll ? null : 1])) }}"
                   class="inline-flex items-center justify-center px-4 py-2 rounded-xl text-sm font-semibold border border-border bg-surface hover:bg-surface-hover transition-colors">
                    <span data-translate="{{ $showAll ? 'farmasi_show_mine' : 'farmasi_show_all' }}">
                        {{ $showAll ? __('messages.farmasi_show_mine') : __('messages.farmasi_show_all') }}
                    </span>
                </a>
            @endif
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
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5l5 5v11a2 2 0 01-2 2z"/>
        </svg>
        <h3 class="text-lg font-semibold text-text-primary" data-translate="farmasi_konsumen_transactions">
            {{ __('messages.farmasi_konsumen_transactions') }}
        </h3>
    </div>

    <div
        x-data="konsumenTable({
            locale: @js(app()->getLocale()),
            rows: @js(($rows ?? collect())->map(function ($r) {
                $dt = \Carbon\Carbon::parse($r->created_at)->locale(app()->getLocale());
                $b = (int) ($r->qty_bandage ?? 0);
                $i = (int) ($r->qty_ifaks ?? 0);
                $p = (int) ($r->qty_painkiller ?? 0);
                return [
                    'id' => (int) $r->id,
                    'createdAtTs' => (int) \Carbon\Carbon::parse($r->created_at)->timestamp,
                    'timeText' => $dt->translatedFormat('d M Y, H:i'),
                    'citizenId' => (string) ($r->citizen_id ?? ''),
                    'identityId' => (int) ($r->identity_id ?? 0),
                    'consumer' => (string) ($r->consumer_name ?? ''),
                    'medic' => (string) ($r->medic_name ?? ''),
                    'jabatan' => (string) ($r->medic_jabatan ?? ''),
                    'bandage' => $b,
                    'ifaks' => $i,
                    'painkiller' => $p,
                    'items' => $b + $i + $p,
                    'price' => (int) ($r->price ?? 0),
                ];
            })->values()),
            identityUrlTemplate: @js(route('api.identity.show', ['identity' => 0])),
        })"
        class="space-y-4"
    >
        {{-- Toolbar (match Rekap Farmasi style) --}}
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
            <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                <div class="relative w-full sm:w-72">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input
                        type="text"
                        class="w-full pl-9 pr-4 py-2 rounded-xl bg-surface border border-border focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none transition-all text-sm placeholder:text-text-hint"
                        :placeholder="t('farmasi_table_search_placeholder', '{{ __('messages.farmasi_table_search_placeholder') }}')"
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
                        <option :value="10" value="10">10</option>
                        <option :value="25" value="25">25</option>
                        <option :value="50" value="50">50</option>
                        <option :value="100" value="100">100</option>
                    </select>
                    <span class="text-xs text-text-secondary" data-translate="farmasi_rows">{{ __('messages.farmasi_rows') }}</span>
                </div>
            </div>

            <div class="flex items-center justify-end">
                <button
                    type="button"
                    class="inline-flex items-center justify-center gap-2 font-semibold transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 bg-surface text-text-primary border border-border hover:bg-surface-hover hover:border-border-medium px-4 py-2 text-sm rounded-xl active:scale-[0.98]"
                    @click="exportTxt(true)"
                >
                    <span class="w-4 h-4 shrink-0" aria-hidden="true">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v12m0 0l-3-3m3 3l3-3M4 17v3a1 1 0 001 1h14a1 1 0 001-1v-3"></path>
                        </svg>
                    </span>
                    <span>
                        <span data-translate="export">{{ __('messages.export') }}</span>
                        <span x-text="' (' + pageRows.length + ')'"></span>
                    </span>
                </button>
            </div>
        </div>

        <div class="overflow-x-auto rounded-xl border border-border">
            <table class="min-w-full divide-y divide-border">
                <thead class="bg-surface-alt">
                    <tr>
                        <th class="px-4 py-2 text-xs font-semibold tracking-wide text-text-secondary uppercase">#</th>
                        <th class="px-4 py-2 text-xs font-semibold tracking-wide text-text-secondary uppercase">
                            <span data-translate="date">{{ __('messages.date') }}</span>
                        </th>
                        <th class="px-4 py-2 text-xs font-semibold tracking-wide text-text-secondary uppercase">
                            <span data-translate="citizen_id">{{ __('messages.citizen_id') }}</span>
                        </th>
                        <th class="px-4 py-2 text-xs font-semibold tracking-wide text-text-secondary uppercase">
                            <span data-translate="consumer_name">{{ __('messages.consumer_name') }}</span>
                        </th>
                        <th class="px-4 py-2 text-xs font-semibold tracking-wide text-text-secondary uppercase">
                            <span data-translate="medic_name">{{ __('messages.medic_name') }}</span>
                        </th>
                        <th class="px-4 py-2 text-xs font-semibold tracking-wide text-text-secondary uppercase">
                            <span data-translate="position">{{ __('messages.position') }}</span>
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
                            <span data-translate="farmasi_selected_items">{{ __('messages.farmasi_selected_items') }}</span>
                        </th>
                        <th class="px-4 py-2 text-xs font-semibold tracking-wide text-text-secondary uppercase text-right">
                            <span data-translate="price">{{ __('messages.price') }}</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    <template x-if="pageRows.length === 0">
                        <tr>
                            <td colspan="11" class="px-4 py-10 text-center text-text-secondary">
                                {{ __('messages.no_data') }}
                            </td>
                        </tr>
                    </template>

                    <template x-for="(row, idx) in pageRows" :key="row.id">
                        <tr class="hover:bg-surface-hover transition-colors">
                            <td class="px-4 py-2 text-sm text-text-secondary" x-text="((page - 1) * pageSize) + idx + 1"></td>
                            <td class="px-4 py-2 text-sm text-text-primary whitespace-nowrap" x-text="row.timeText"></td>
                            <td class="px-4 py-2 text-sm">
                                <template x-if="row.citizenId && row.identityId">
                                    <button
                                        type="button"
                                        class="text-primary hover:text-primary-dark font-mono text-sm underline underline-offset-2"
                                        @click="openIdentity(row.identityId)"
                                        x-text="row.citizenId"
                                    ></button>
                                </template>
                                <template x-if="!row.citizenId">
                                    <span class="text-text-tertiary">-</span>
                                </template>
                            </td>
                            <td class="px-4 py-2 text-sm text-text-primary" x-text="row.consumer"></td>
                            <td class="px-4 py-2 text-sm text-text-primary" x-text="row.medic"></td>
                            <td class="px-4 py-2 text-sm text-text-secondary" x-text="row.jabatan"></td>
                            <td class="px-4 py-2 text-sm text-text-secondary text-right" x-text="row.bandage"></td>
                            <td class="px-4 py-2 text-sm text-text-secondary text-right" x-text="row.ifaks"></td>
                            <td class="px-4 py-2 text-sm text-text-secondary text-right" x-text="row.painkiller"></td>
                            <td class="px-4 py-2 text-sm text-text-secondary text-right" x-text="row.items"></td>
                            <td class="px-4 py-2 text-sm text-text-primary whitespace-nowrap text-right" x-text="formatMoney(row.price)"></td>
                        </tr>
                    </template>
                </tbody>
                <tfoot class="bg-surface-alt">
                    <tr>
                        <th colspan="6" class="px-4 py-2 text-xs font-semibold tracking-wide text-text-secondary uppercase text-right">
                            <span data-translate="total">{{ __('messages.total') }}</span>
                        </th>
                        <th class="px-4 py-2 text-xs font-semibold text-text-secondary text-right" x-text="pageTotals.bandage"></th>
                        <th class="px-4 py-2 text-xs font-semibold text-text-secondary text-right" x-text="pageTotals.ifaks"></th>
                        <th class="px-4 py-2 text-xs font-semibold text-text-secondary text-right" x-text="pageTotals.painkiller"></th>
                        <th class="px-4 py-2 text-xs font-semibold text-text-secondary text-right" x-text="pageTotals.items"></th>
                        <th class="px-4 py-2 text-xs font-semibold text-text-primary whitespace-nowrap text-right" x-text="formatMoney(pageTotals.price)"></th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <p class="text-sm text-text-secondary">
                <span data-translate="farmasi_total_shown">{{ __('messages.farmasi_total_shown') }}</span>:
                <span class="font-semibold text-text-primary" x-text="formatMoney(pageTotals.price)"></span>
                <span class="text-text-tertiary">
                    Â· <span x-text="pageRows.length"></span> <span data-translate="farmasi_rows">{{ __('messages.farmasi_rows') }}</span>
                </span>
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

        <x-modal
            x-model="identityOpen"
            :title="__('messages.farmasi_konsumen_identity_title')"
            size="lg"
        >
            <div class="space-y-3">
                <template x-if="identityLoading">
                    <p class="text-sm text-text-secondary" data-translate="loading">{{ __('messages.loading') }}</p>
                </template>

                <template x-if="!identityLoading && identityError">
                    <x-alert type="danger" :title="__('messages.error')">
                        <span x-text="identityError"></span>
                    </x-alert>
                </template>

                <template x-if="!identityLoading && identity && !identityError">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <p class="text-xs text-text-tertiary">{{ __('messages.citizen_id') }}</p>
                            <p class="text-sm font-mono text-text-primary" x-text="identity.citizen_id || '-'"></p>
                        </div>
                        <div>
                            <p class="text-xs text-text-tertiary">{{ __('messages.full_name') }}</p>
                            <p class="text-sm text-text-primary" x-text="identity.full_name || '-'"></p>
                        </div>
                        <div>
                            <p class="text-xs text-text-tertiary">{{ __('messages.date') }}</p>
                            <p class="text-sm text-text-primary" x-text="identity.dob || '-'"></p>
                        </div>
                        <div>
                            <p class="text-xs text-text-tertiary">{{ __('messages.gender') }}</p>
                            <p class="text-sm text-text-primary" x-text="identity.sex || '-'"></p>
                        </div>
                        <div class="sm:col-span-2">
                            <p class="text-xs text-text-tertiary">{{ __('messages.nationality') }}</p>
                            <p class="text-sm text-text-primary" x-text="identity.nationality || '-'"></p>
                        </div>
                        <div class="sm:col-span-2" x-show="identity.image_url" x-cloak>
                            <a :href="identity.image_url" target="_blank" class="block">
                                <img :src="identity.image_url" alt="Identity" class="w-full max-h-72 object-contain rounded-xl border border-border bg-surface">
                            </a>
                        </div>
                    </div>
                </template>
            </div>
        </x-modal>
    </div>
</x-card>

@endsection
