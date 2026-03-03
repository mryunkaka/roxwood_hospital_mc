{{-- Reimbursement --}}
@extends('layouts.app')

@section('title', __('messages.reimbursement_title') . ' - ' . ($appName ?? config('app.name')))
@section('page-title', __('messages.reimbursement_title'))
@section('page-description', __('messages.reimbursement_subtitle'))

@section('content')
@php
    $thousandSep = app()->getLocale() === 'id' ? '.' : ',';
    $decimalSep = app()->getLocale() === 'id' ? ',' : '.';
    $fmtMoney = fn ($n) => '$ ' . number_format((float) $n, 0, $decimalSep, $thousandSep);
@endphp

<div
    x-data="{
        addOpen: false,
        docPreviewOpen: false,
        docPreviewSrc: '',
        docPreviewTitle: '',
        openDoc(src, title) {
            this.docPreviewSrc = String(src || '');
            this.docPreviewTitle = String(title || '');
            this.docPreviewOpen = true;
        }
    }"
    class="space-y-6"
>
    @if(session('success'))
        <x-alert type="success">
            {{ session('success') }}
        </x-alert>
    @elseif(session('info'))
        <x-alert type="info">
            {{ session('info') }}
        </x-alert>
    @endif

    <x-card>
        <div class="flex items-center gap-2 mb-4">
            <svg class="w-5 h-5 text-text-tertiary" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <h3 class="text-lg font-semibold text-text-primary" data-translate="reimbursement_filter_title">{{ __('messages.reimbursement_filter_title') }}</h3>
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5l5 5v11a2 2 0 01-2 2z"/>
                </svg>
                <h3 class="text-lg font-semibold text-text-primary" data-translate="reimbursement_list_title">{{ __('messages.reimbursement_list_title') }}</h3>
            </div>

            <x-button type="button" variant="success" @click="addOpen = true">
                <span data-translate="reimbursement_add_button">{{ __('messages.reimbursement_add_button') }}</span>
            </x-button>
        </div>

        <x-table :headers="[
            ['label' => '#'],
            ['label' => __('messages.date')],
            ['label' => __('messages.reimbursement_code')],
            ['label' => __('messages.reimbursement_source')],
            ['label' => __('messages.reimbursement_submitted_by')],
            ['label' => __('messages.status')],
            ['label' => __('messages.reimbursement_receipt')],
            ['label' => __('messages.total')],
            ['label' => __('messages.reimbursement_paid_by')],
            ['label' => __('messages.actions')],
        ]" :striped="false" :bordered="true" :compact="true">
            @forelse($rows as $i => $r)
                @php
                    $status = (string) ($r->status ?? 'draft');
                    $badgeVariant = match ($status) {
                        'paid' => 'success',
                        'submitted' => 'warning',
                        'rejected' => 'danger',
                        default => 'default',
                    };

                    $createdAtText = '-';
                    if (!empty($r->created_at)) {
                        try {
                            $createdAtText = \Carbon\Carbon::parse($r->created_at)->locale(app()->getLocale())->translatedFormat('d M Y H:i');
                        } catch (\Throwable $e) {
                            $createdAtText = (string) $r->created_at;
                        }
                    }

                    $paidAtText = '';
                    if (!empty($r->paid_at)) {
                        try {
                            $paidAtText = \Carbon\Carbon::parse($r->paid_at)->locale(app()->getLocale())->translatedFormat('d M Y H:i');
                        } catch (\Throwable $e) {
                            $paidAtText = (string) $r->paid_at;
                        }
                    }
                @endphp

                <tr>
                    <td class="px-4 py-2 text-sm text-text-primary">{{ $i + 1 }}</td>
                    <td class="px-4 py-2 text-sm text-text-primary">{{ $createdAtText }}</td>
                    <td class="px-4 py-2 text-sm text-text-primary font-semibold">{{ (string) ($r->reimbursement_code ?? '-') }}</td>
                    <td class="px-4 py-2 text-sm text-text-primary">
                        <div class="font-semibold">
                            {{ ucfirst((string) ($r->billing_source_type ?? '-')) }} – {{ (string) ($r->billing_source_name ?? '-') }}
                        </div>
                        @if(!empty($r->item_name))
                            <div class="text-xs text-text-secondary">
                                <span data-translate="reimbursement_item_prefix">{{ __('messages.reimbursement_item_prefix') }}</span>:
                                {{ (string) $r->item_name }}
                            </div>
                        @endif
                    </td>
                    <td class="px-4 py-2 text-sm text-text-primary">
                        @if(!empty($r->created_by_name))
                            {{ (string) $r->created_by_name }}
                        @else
                            <span class="text-text-muted">-</span>
                        @endif
                    </td>
                    <td class="px-4 py-2 text-sm text-text-primary">
                        <x-badge :variant="$badgeVariant">{{ strtoupper($status) }}</x-badge>
                    </td>
                    <td class="px-4 py-2 text-sm text-text-primary">
                        @if(!empty($r->receipt_file))
                            <x-button
                                type="button"
                                variant="secondary"
                                size="xs"
                                :data-src="route('reimbursement.receipt', ['code' => (string) ($r->reimbursement_code ?? '')])"
                                :data-title="__('messages.reimbursement_receipt_title', ['code' => (string) ($r->reimbursement_code ?? '-') ])"
                                @click="openDoc($event.currentTarget.dataset.src, $event.currentTarget.dataset.title)"
                            >
                                <span data-translate="view">{{ __('messages.view') }}</span>
                            </x-button>
                        @else
                            <span class="text-text-muted">-</span>
                        @endif
                    </td>
                    <td class="px-4 py-2 text-sm text-text-primary font-semibold">{{ $fmtMoney((float) ($r->total_amount ?? 0)) }}</td>
                    <td class="px-4 py-2 text-sm text-text-primary">
                        @if(!empty($r->paid_by_name))
                            <div class="font-semibold">{{ (string) $r->paid_by_name }}</div>
                            @if($paidAtText !== '')
                                <div class="text-xs text-text-secondary">{{ $paidAtText }}</div>
                            @endif
                        @else
                            <span class="text-text-muted">-</span>
                        @endif
                    </td>
                    <td class="px-4 py-2 text-sm text-text-primary whitespace-nowrap">
                        <div class="flex items-center gap-2">
                            @if($canPay && $status === 'submitted')
                                <form method="post" action="{{ route('reimbursement.pay') }}" onsubmit="return confirm('{{ __('messages.reimbursement_pay_confirm') }}')">
                                    @csrf
                                    <input type="hidden" name="code" value="{{ (string) ($r->reimbursement_code ?? '') }}">
                                    <x-button type="submit" variant="success" size="xs">
                                        <span data-translate="reimbursement_pay_button">{{ __('messages.reimbursement_pay_button') }}</span>
                                    </x-button>
                                </form>
                            @endif

                            @if($isDirector)
                                <form method="post" action="{{ route('reimbursement.delete') }}" onsubmit="return confirm('{{ __('messages.reimbursement_delete_confirm') }}')">
                                    @csrf
                                    <input type="hidden" name="code" value="{{ (string) ($r->reimbursement_code ?? '') }}">
                                    <x-button type="submit" variant="danger" size="xs">
                                        <span data-translate="delete">{{ __('messages.delete') }}</span>
                                    </x-button>
                                </form>
                            @endif

                            @if((!$canPay || $status !== 'submitted') && !$isDirector)
                                <span class="text-text-muted">-</span>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="px-4 py-10 text-center text-text-secondary">
                        <span data-translate="no_data">{{ __('messages.no_data') }}</span>
                    </td>
                </tr>
            @endforelse
        </x-table>
    </x-card>

    <x-modal x-model="addOpen" :title="__('messages.reimbursement_add_title')" size="lg">
        <form method="post" action="{{ route('reimbursement.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <x-input
                name="reimbursement_code"
                :label="__('messages.reimbursement_code')"
                dataTranslateLabel="reimbursement_code"
                :value="old('reimbursement_code', $defaultCode)"
                readonly
                :error="$errors->first('reimbursement_code')"
            />

            <x-select
                name="billing_source_type"
                :label="__('messages.reimbursement_source_type')"
                dataTranslateLabel="reimbursement_source_type"
                :options="[
                    'instansi' => __('messages.reimbursement_source_instansi'),
                    'restoran' => __('messages.reimbursement_source_restoran'),
                    'toko' => __('messages.reimbursement_source_toko'),
                    'vendor' => __('messages.reimbursement_source_vendor'),
                    'lainnya' => __('messages.reimbursement_source_other'),
                ]"
                :value="old('billing_source_type', 'instansi')"
                :error="$errors->first('billing_source_type')"
                :required="true"
            />

            <x-input
                name="billing_source_name"
                :label="__('messages.reimbursement_source_name')"
                dataTranslateLabel="reimbursement_source_name"
                :placeholder="__('messages.reimbursement_source_name_placeholder')"
                dataTranslatePlaceholder="reimbursement_source_name_placeholder"
                :value="old('billing_source_name')"
                :error="$errors->first('billing_source_name')"
                :required="true"
            />

            <x-input
                name="item_name"
                :label="__('messages.reimbursement_item_name')"
                dataTranslateLabel="reimbursement_item_name"
                :placeholder="__('messages.reimbursement_item_name_placeholder')"
                dataTranslatePlaceholder="reimbursement_item_name_placeholder"
                :value="old('item_name')"
                :error="$errors->first('item_name')"
                :required="true"
            />

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <x-input
                    type="number"
                    name="qty"
                    :label="__('messages.reimbursement_qty')"
                    dataTranslateLabel="reimbursement_qty"
                    min="1"
                    :value="old('qty', 1)"
                    :error="$errors->first('qty')"
                    :required="true"
                />
                <x-input
                    type="number"
                    name="price"
                    :label="__('messages.price')"
                    dataTranslateLabel="price"
                    min="0"
                    step="0.01"
                    :value="old('price')"
                    :error="$errors->first('price')"
                    :required="true"
                />
            </div>

            <x-file-input
                name="receipt_file"
                :label="__('messages.reimbursement_receipt_upload')"
                dataTranslateLabel="reimbursement_receipt_upload"
                dataTranslateUpload="click_to_upload"
                accept="image/png,image/jpeg"
                :error="$errors->first('receipt_file')"
                :hint="__('messages.reimbursement_receipt_hint')"
                data-translate-hint="reimbursement_receipt_hint"
            />

            <div class="flex justify-end gap-2 pt-2">
                <x-button type="button" variant="secondary" @click="addOpen = false">
                    <span data-translate="cancel">{{ __('messages.cancel') }}</span>
                </x-button>
                <x-button type="submit" variant="success">
                    <span data-translate="save">{{ __('messages.save') }}</span>
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
