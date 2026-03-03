{{-- Farmasi: Restaurant Settings --}}
@extends('layouts.app')

@section('title', __('messages.restaurant_settings_title') . ' - ' . ($appName ?? config('app.name')))
@section('page-title', __('messages.restaurant_settings_title'))
@section('page-description', __('messages.restaurant_settings_subtitle'))

@section('content')
@php
    use Carbon\Carbon;

    $thousandSep = app()->getLocale() === 'id' ? '.' : ',';
    $decimalSep = app()->getLocale() === 'id' ? ',' : '.';
    $fmtMoney = fn ($n) => '$ ' . number_format((float) $n, 0, $decimalSep, $thousandSep);
@endphp

<div
    x-data="{
        editOpen: false,
        edit: { id: null, name: '', price: 0, tax: 5, active: true },
        openEdit(r) {
            this.edit.id = Number(r?.id || 0);
            this.edit.name = String(r?.restaurant_name || '');
            this.edit.price = Number(r?.price_per_packet || 0);
            this.edit.tax = Number(r?.tax_percentage || 0);
            this.edit.active = Boolean(Number(r?.is_active || 0) === 1);
            this.editOpen = true;
        },
    }"
    class="space-y-6"
>
    @if(session('success'))
        <x-alert type="success">{{ session('success') }}</x-alert>
    @elseif(session('error'))
        <x-alert type="danger">{{ session('error') }}</x-alert>
    @endif

    <div class="flex items-center justify-between gap-3">
        <div class="text-sm text-text-secondary">
            <span data-translate="restaurant_settings_hint">{{ __('messages.restaurant_settings_hint') }}</span>
        </div>
        <x-button
            type="button"
            variant="secondary"
            x-on:click="window.location.href = '{{ route('restaurant.consumption.index') }}'"
            :icon="'
                <svg class=&quot;w-full h-full&quot; fill=&quot;none&quot; stroke=&quot;currentColor&quot; viewBox=&quot;0 0 24 24&quot; aria-hidden=&quot;true&quot;>
                    <path stroke-linecap=&quot;round&quot; stroke-linejoin=&quot;round&quot; stroke-width=&quot;2&quot; d=&quot;M10 19l-7-7m0 0l7-7m-7 7h18&quot; />
                </svg>
            '"
        >
            <span data-translate="restaurant_settings_back">{{ __('messages.restaurant_settings_back') }}</span>
        </x-button>
    </div>

    <x-card :title="__('messages.restaurant_settings_add_title')" :subtitle="__('messages.restaurant_settings_add_subtitle')">
        <form method="post" action="{{ route('restaurant.settings.store') }}" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-input
                    name="restaurant_name"
                    :label="__('messages.restaurant_settings_restaurant_name')"
                    dataTranslateLabel="restaurant_settings_restaurant_name"
                    :placeholder="__('messages.restaurant_settings_restaurant_name_placeholder')"
                    dataTranslatePlaceholder="restaurant_settings_restaurant_name_placeholder"
                    :value="old('restaurant_name', '')"
                    :required="true"
                />
                <x-input
                    type="number"
                    name="price_per_packet"
                    :label="__('messages.restaurant_settings_price_per_packet')"
                    dataTranslateLabel="restaurant_settings_price_per_packet"
                    min="0"
                    step="0.01"
                    :value="old('price_per_packet', '')"
                    :required="true"
                />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-end">
                <x-input
                    type="number"
                    name="tax_percentage"
                    :label="__('messages.restaurant_settings_tax_percentage')"
                    dataTranslateLabel="restaurant_settings_tax_percentage"
                    min="0"
                    max="100"
                    step="0.01"
                    :value="old('tax_percentage', 5)"
                    :required="true"
                />

                <x-checkbox
                    name="is_active"
                    :label="__('messages.active')"
                    dataTranslateLabel="active"
                    :checked="old('is_active', '1') === '1'"
                />
            </div>

            <div class="flex justify-end">
                <x-button
                    type="submit"
                    variant="success"
                    :icon="'
                        <svg class=&quot;w-full h-full&quot; fill=&quot;none&quot; stroke=&quot;currentColor&quot; viewBox=&quot;0 0 24 24&quot; aria-hidden=&quot;true&quot;>
                            <path stroke-linecap=&quot;round&quot; stroke-linejoin=&quot;round&quot; stroke-width=&quot;2&quot; d=&quot;M12 4v16m8-8H4&quot; />
                        </svg>
                    '"
                >
                    <span data-translate="restaurant_settings_add_button">{{ __('messages.restaurant_settings_add_button') }}</span>
                </x-button>
            </div>
        </form>
    </x-card>

    <x-card :title="__('messages.restaurant_settings_list_title')">
        <x-table :headers="[
            ['label' => '#'],
            ['label' => __('messages.restaurant_settings_restaurant_name')],
            ['label' => __('messages.restaurant_settings_price_per_packet')],
            ['label' => __('messages.restaurant_settings_tax_percentage')],
            ['label' => __('messages.status')],
            ['label' => __('messages.restaurant_settings_created_at')],
            ['label' => __('messages.actions')],
        ]" striped bordered compact>
            @forelse($restaurants as $i => $r)
                @php
                    $active = (int) ($r['is_active'] ?? 0) === 1;
                    $createdAt = null;
                    try { $createdAt = Carbon::parse((string) ($r['created_at'] ?? '')); } catch (\Throwable $e) { $createdAt = null; }
                    $createdText = $createdAt ? $createdAt->copy()->locale(app()->getLocale())->translatedFormat('d M Y') : '-';
                @endphp
                <tr>
                    <td class="px-4 py-2 text-sm text-text-secondary">{{ $i + 1 }}</td>
                    <td class="px-4 py-2 text-sm text-text-primary font-semibold">{{ (string) ($r['restaurant_name'] ?? '-') }}</td>
                    <td class="px-4 py-2 text-sm text-text-primary whitespace-nowrap">{{ $fmtMoney($r['price_per_packet'] ?? 0) }}</td>
                    <td class="px-4 py-2 text-sm text-text-primary whitespace-nowrap">{{ (float) ($r['tax_percentage'] ?? 0) }}%</td>
                    <td class="px-4 py-2 text-sm text-text-primary">
                        @if($active)
                            <x-badge variant="success">{{ strtoupper(__('messages.active')) }}</x-badge>
                        @else
                            <x-badge variant="danger">{{ strtoupper(__('messages.inactive')) }}</x-badge>
                        @endif
                    </td>
                    <td class="px-4 py-2 text-sm text-text-secondary">{{ $createdText }}</td>
                    <td class="px-4 py-2 text-sm text-text-primary whitespace-nowrap">
                        <div class="flex items-center gap-2">
                            <x-button
                                type="button"
                                variant="secondary"
                                size="xs"
                                x-on:click="openEdit({
                                    id: {{ (int) ($r['id'] ?? 0) }},
                                    restaurant_name: @json((string) ($r['restaurant_name'] ?? '')),
                                    price_per_packet: {{ (float) ($r['price_per_packet'] ?? 0) }},
                                    tax_percentage: {{ (float) ($r['tax_percentage'] ?? 0) }},
                                    is_active: {{ (int) ($r['is_active'] ?? 0) }},
                                })"
                            >
                                <span data-translate="edit">{{ __('messages.edit') }}</span>
                            </x-button>

                            <form method="post" action="{{ route('restaurant.settings.toggle') }}" onsubmit="return confirm('{{ __('messages.restaurant_settings_toggle_confirm') }}')">
                                @csrf
                                <input type="hidden" name="id" value="{{ (int) ($r['id'] ?? 0) }}">
                                <input type="hidden" name="is_active" value="{{ $active ? 0 : 1 }}">
                                <x-button type="submit" variant="{{ $active ? 'warning' : 'success' }}" size="xs">
                                    <span>{{ $active ? __('messages.restaurant_settings_deactivate') : __('messages.restaurant_settings_activate') }}</span>
                                </x-button>
                            </form>

                            <form method="post" action="{{ route('restaurant.settings.delete') }}" onsubmit="return confirm('{{ __('messages.restaurant_settings_delete_confirm') }}')">
                                @csrf
                                <input type="hidden" name="id" value="{{ (int) ($r['id'] ?? 0) }}">
                                <x-button type="submit" variant="danger" size="xs">
                                    <span data-translate="delete">{{ __('messages.delete') }}</span>
                                </x-button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-10 text-center text-text-secondary">
                        <span data-translate="no_data">{{ __('messages.no_data') }}</span>
                    </td>
                </tr>
            @endforelse
        </x-table>
    </x-card>

    <x-modal x-model="editOpen" :title="__('messages.restaurant_settings_edit_title')" size="lg">
        <form method="post" action="{{ route('restaurant.settings.update') }}" class="space-y-4">
            @csrf

            <input type="hidden" name="id" :value="edit.id">

            <x-input
                name="restaurant_name"
                :label="__('messages.restaurant_settings_restaurant_name')"
                dataTranslateLabel="restaurant_settings_restaurant_name"
                x-model="edit.name"
                :required="true"
            />

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <x-input
                    type="number"
                    name="price_per_packet"
                    :label="__('messages.restaurant_settings_price_per_packet')"
                    dataTranslateLabel="restaurant_settings_price_per_packet"
                    min="0"
                    step="0.01"
                    x-model="edit.price"
                    :required="true"
                />
                <x-input
                    type="number"
                    name="tax_percentage"
                    :label="__('messages.restaurant_settings_tax_percentage')"
                    dataTranslateLabel="restaurant_settings_tax_percentage"
                    min="0"
                    max="100"
                    step="0.01"
                    x-model="edit.tax"
                    :required="true"
                />
            </div>

            <x-checkbox
                name="is_active"
                :label="__('messages.active')"
                dataTranslateLabel="active"
                x-model="edit.active"
                value="1"
            />

            <div class="flex justify-end gap-2 pt-2">
                <x-button type="button" variant="secondary" @click="editOpen = false">
                    <span data-translate="cancel">{{ __('messages.cancel') }}</span>
                </x-button>
                <x-button type="submit" variant="success">
                    <span data-translate="save">{{ __('messages.save') }}</span>
                </x-button>
            </div>
        </form>
    </x-modal>
</div>
@endsection
