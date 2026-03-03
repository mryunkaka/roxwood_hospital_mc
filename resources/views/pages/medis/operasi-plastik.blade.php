{{-- Medis: Operasi Plastik --}}
@extends('layouts.app')

@section('title', __('messages.operasi_plastik_title') . ' - ' . ($appName ?? config('app.name')))

@section('page-title', __('messages.operasi_plastik_title'))
@section('page-description', __('messages.operasi_plastik_subtitle'))

@section('content')
@php
    $df = fn ($d) => $d ? \Carbon\Carbon::parse($d)->locale(app()->getLocale())->translatedFormat('d M Y') : '-';
@endphp

@if (session('success'))
    <x-alert type="success" dismissible autoHide>{{ session('success') }}</x-alert>
@endif
@if (session('error'))
    <x-alert type="danger" dismissible autoHide>{{ session('error') }}</x-alert>
@endif
@if ($errors->any())
    <x-alert type="danger" dismissible>
        <div class="space-y-1">
            @foreach ($errors->all() as $e)
                <div>{{ $e }}</div>
            @endforeach
        </div>
    </x-alert>
@endif

<x-card class="mb-6">
    <div class="flex items-start justify-between gap-4 mb-4">
        <div>
            <h3 class="text-lg font-semibold text-text-primary" data-translate="operasi_plastik_form_title">
                {{ __('messages.operasi_plastik_form_title') }}
            </h3>
            <p class="text-sm text-text-secondary mt-1" data-translate="operasi_plastik_form_hint">
                {{ __('messages.operasi_plastik_form_hint') }}
            </p>
        </div>

        <div class="shrink-0">
            @if(!$canInput)
                <x-badge variant="danger" size="sm">
                    {{ __('messages.operasi_plastik_wait_days', ['days' => $remainingDays]) }}
                </x-badge>
            @else
                <x-badge variant="success" size="sm">
                    {{ __('messages.operasi_plastik_available') }}
                </x-badge>
            @endif
        </div>
    </div>

    <form method="post" action="{{ route('medis.operasi_plastik.store') }}" class="space-y-4">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <x-input
                name="nama_medis_display"
                :label="__('messages.operasi_plastik_medic_name')"
                dataTranslateLabel="operasi_plastik_medic_name"
                :value="(string) ($user['name'] ?? '-')"
                readonly
            />

            <x-input
                type="date"
                name="tanggal"
                :label="__('messages.operasi_plastik_date')"
                dataTranslateLabel="operasi_plastik_date"
                :value="old('tanggal', now()->toDateString())"
                :error="$errors->first('tanggal')"
                required
                :disabled="!$canInput"
            />

            <x-select
                name="jenis_operasi"
                :label="__('messages.operasi_plastik_type')"
                dataTranslateLabel="operasi_plastik_type"
                :placeholder="__('messages.operasi_plastik_choose_type')"
                :options="collect($jenisOperasi)->mapWithKeys(fn ($t) => [$t => $t])->all()"
                :value="old('jenis_operasi', '')"
                :error="$errors->first('jenis_operasi')"
                required
                :disabled="!$canInput"
            />

            <x-autocomplete
                name="id_penanggung_jawab"
                displayName="penanggung_jawab_name"
                :label="__('messages.operasi_plastik_handler')"
                placeholder="{{ __('messages.operasi_plastik_choose_handler') }}"
                :options="$penanggungJawabOptions ?? []"
                :value="old('id_penanggung_jawab', '')"
                :error="$errors->first('id_penanggung_jawab')"
                required
                :disabled="!$canInput"
                :dropdownButton="false"
                :minChars="2"
            />
            @if(empty($penanggungJawabOptions))
                <p class="text-xs text-text-secondary mt-1.5">
                    {{ __('messages.operasi_plastik_no_handlers') }}
                </p>
            @endif
        </div>

        <div>
            <label class="block text-sm font-medium text-text-primary mb-1.5" data-translate="operasi_plastik_reason">
                {{ __('messages.operasi_plastik_reason') }} <span class="text-danger-500 ml-1">*</span>
            </label>
            <textarea
                name="alasan"
                rows="4"
                class="w-full rounded-xl border border-border bg-surface px-4 py-3 text-sm text-text-primary placeholder:text-text-hint outline-none transition-all duration-200 focus:ring-2 focus:border-primary-500 focus:ring-primary-500/20 disabled:bg-surface-alt disabled:cursor-not-allowed"
                placeholder="{{ __('messages.operasi_plastik_reason_placeholder') }}"
                data-translate-placeholder="operasi_plastik_reason_placeholder"
                @disabled(!$canInput)
            >{{ old('alasan', '') }}</textarea>
            @if($errors->first('alasan'))
                <p class="mt-1.5 text-xs text-danger-500">{{ $errors->first('alasan') }}</p>
            @endif
        </div>

        <div class="flex items-center justify-end gap-3">
            <x-button type="submit" variant="primary" :disabled="!$canInput">
                {{ __('messages.operasi_plastik_submit') }}
            </x-button>
        </div>

        @if(!$canInput && $nextEligible)
            <p class="text-xs text-text-secondary">
                {{ __('messages.operasi_plastik_next_date', ['date' => $nextEligible->locale(app()->getLocale())->translatedFormat('d M Y')]) }}
            </p>
        @endif
    </form>
</x-card>

<x-card>
    <div class="flex items-center justify-between gap-3 mb-4">
        <h3 class="text-lg font-semibold text-text-primary" data-translate="operasi_plastik_history_title">
            {{ __('messages.operasi_plastik_history_title') }}
        </h3>
        @if(!$isStaff)
            <x-badge variant="info" size="sm">{{ __('messages.operasi_plastik_history_all') }}</x-badge>
        @else
            <x-badge variant="default" size="sm">{{ __('messages.operasi_plastik_history_mine') }}</x-badge>
        @endif
    </div>

	    <div
	        x-data="{
	            alasanOpen: false,
	            operasiId: null,
	            operasiName: '',
	            operasiReason: '',
	            approveBase: @js(url('/operasi-plastik')),
	            close() {
	                this.alasanOpen = false;
	                this.operasiId = null;
	                this.operasiName = '';
	                this.operasiReason = '';
	            }
	        }"
	    >
        <x-table
            :headers="[
                '#',
                __('messages.operasi_plastik_date'),
                __('messages.operasi_plastik_medic_name'),
                __('messages.operasi_plastik_type'),
                __('messages.operasi_plastik_handler'),
                __('messages.operasi_plastik_status'),
                __('messages.operasi_plastik_approved_by'),
                __('messages.operasi_plastik_action'),
            ]"
            striped
        >
	            @forelse($rows as $i => $row)
	                @php
	                    $currentUserId = (int) ($user['id'] ?? 0);
	                    $status = (string) ($row->status ?? 'pending');
                    $variant = match ($status) {
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'warning',
                    };

	                    $rowMedic = $row->user;
	                    $rowPj = $row->penanggungJawab;
	                    $rowAp = $row->approvedBy;

	                    $allowAction = $status === 'pending' && (int) ($row->id_penanggung_jawab ?? 0) === $currentUserId;
                        $alasanId = (int) ($row->id ?? 0);
                        $alasanName = (string) ($rowMedic?->full_name ?? '');
                        $alasanReason = (string) ($row->alasan ?? '');
                        $jsName = \Illuminate\Support\Js::from($alasanName);
                        $jsReason = \Illuminate\Support\Js::from($alasanReason);
	                @endphp

                <tr>
                    <td class="px-4 py-3 text-sm text-text-primary">{{ $i + 1 }}</td>
                    <td class="px-4 py-3 text-sm text-text-primary">{{ $df($row->tanggal) }}</td>
                    <td class="px-4 py-3 text-sm text-text-primary">
                        <div class="font-semibold">{{ $rowMedic?->full_name ?? '-' }}</div>
                        <div class="text-xs text-text-secondary">{{ $rowMedic?->position ?? '-' }}</div>
                    </td>
                    <td class="px-4 py-3 text-sm text-text-primary">{{ $row->jenis_operasi ?? '-' }}</td>
                    <td class="px-4 py-3 text-sm text-text-primary">
                        <div class="font-semibold">{{ $rowPj?->full_name ?? '-' }}</div>
                        <div class="text-xs text-text-secondary">{{ $rowPj?->position ?? '-' }}</div>
                    </td>
                    <td class="px-4 py-3 text-sm text-text-primary">
                        <x-badge :variant="$variant" size="sm" dot>
                            {{ __('messages.operasi_plastik_status_' . $status) }}
                        </x-badge>
                    </td>
                    <td class="px-4 py-3 text-sm text-text-primary">
                        @if($rowAp)
                            <div class="font-semibold">{{ $rowAp->full_name }}</div>
                            <div class="text-xs text-text-secondary">{{ $df($row->approved_at) }}</div>
                        @else
                            <span class="text-text-tertiary">-</span>
                        @endif
                    </td>
	                    <td class="px-4 py-3 text-sm text-text-primary">
	                        @if($allowAction)
	                            <x-button
	                                type="button"
	                                variant="warning"
	                                size="sm"
	                                x-on:click="operasiId={{ $alasanId }}; operasiName={{ $jsName }}; operasiReason={{ $jsReason }}; alasanOpen=true"
	                            >
	                                {{ __('messages.operasi_plastik_view_reason') }}
	                            </x-button>
	                        @else
	                            <span class="text-text-tertiary">-</span>
	                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-4 py-6 text-center text-text-secondary">{{ __('messages.no_data') }}</td>
                </tr>
            @endforelse
        </x-table>

	        <x-modal
	            x-model="alasanOpen"
	            :title="__('messages.operasi_plastik_modal_title')"
	            size="lg"
	        >
            <div class="space-y-4">
                <div class="rounded-xl border border-border bg-surface-alt p-4">
                    <div class="text-sm font-semibold text-text-primary" x-text="operasiName"></div>
                    <div class="mt-2 text-sm text-text-secondary whitespace-pre-wrap" x-text="operasiReason"></div>
                </div>

                <div class="flex items-center justify-end gap-3">
                    <form method="post" x-bind:action="approveBase + '/' + operasiId + '/approve'">
                        @csrf
                        <x-button type="submit" variant="success" x-bind:disabled="!operasiId">
                            {{ __('messages.operasi_plastik_approve') }}
                        </x-button>
                    </form>
                    <form method="post" x-bind:action="approveBase + '/' + operasiId + '/reject'">
                        @csrf
                        <x-button type="submit" variant="danger" x-bind:disabled="!operasiId">
                            {{ __('messages.operasi_plastik_reject') }}
                        </x-button>
                    </form>
	                    <x-button type="button" variant="secondary" @click="close()">
	                        {{ __('messages.cancel') }}
	                    </x-button>
	                </div>
	            </div>
	        </x-modal>
    </div>
</x-card>
@endsection
