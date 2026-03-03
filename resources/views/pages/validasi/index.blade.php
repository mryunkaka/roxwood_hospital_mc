{{-- Validasi: Akun & Dokumen --}}
@extends('layouts.app')

@section('title', __('messages.validation_title') . ' - ' . ($appName ?? config('app.name')))

@section('page-title', __('messages.validation_title'))
@section('page-description', __('messages.validation_subtitle'))

@section('content')
<div
    x-data="validasiAkunPage({
        locale: @js(app()->getLocale()),
        users: @js($users ?? []),
        updateUserUrlTemplate: @js(route('validasi.users.update', ['userRh' => 0])),
    })"
    class="space-y-6"
>
    <x-card>
        <x-alert type="info" :icon="true">
            <p class="text-sm" data-translate="validation_hint">{{ __('messages.validation_hint') }}</p>
        </x-alert>

        <div class="mt-5 flex flex-col md:flex-row md:items-center gap-3">
            <div class="flex-1">
                <x-input
                    name="validation_search"
                    :label="__('messages.search')"
                    dataTranslateLabel="search"
                    :placeholder="__('messages.validation_search_placeholder')"
                    x-model="search"
                    @input.debounce.150ms="page = 1"
                />
            </div>

            <div class="w-full md:w-52">
                <x-select
                    name="validation_filter"
                    :label="__('messages.status')"
                    dataTranslateLabel="status"
                    :options="[
                        'all' => __('messages.all'),
                        'pending' => __('messages.validation_status_pending'),
                        'active' => __('messages.validation_status_active'),
                        'resign' => __('messages.validation_status_resign'),
                    ]"
                    :value="'pending'"
                    x-model="filter"
                    @change="page = 1"
                />
            </div>

            <div class="w-full md:w-40">
                <x-select
                    name="validation_page_size"
                    :label="__('messages.show')"
                    dataTranslateLabel="show"
                    :options="[
                        10 => '10',
                        25 => '25',
                        50 => '50',
                    ]"
                    :value="10"
                    x-model.number="pageSize"
                    @change="page = 1"
                />
            </div>
        </div>
    </x-card>

    <x-card :padding="'none'">
        <div class="overflow-x-auto rounded-2xl border border-border">
            <table class="min-w-full divide-y divide-border">
                <thead class="bg-surface-alt">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold tracking-wide text-text-secondary uppercase" data-translate="full_name">{{ __('messages.full_name') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold tracking-wide text-text-secondary uppercase" data-translate="role">{{ __('messages.role') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold tracking-wide text-text-secondary uppercase" data-translate="position">{{ __('messages.position') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold tracking-wide text-text-secondary uppercase" data-translate="batch">{{ __('messages.batch') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold tracking-wide text-text-secondary uppercase" data-translate="join_date">{{ __('messages.join_date') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold tracking-wide text-text-secondary uppercase" data-translate="status">{{ __('messages.status') }}</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold tracking-wide text-text-secondary uppercase" data-translate="actions">{{ __('messages.actions') }}</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-border bg-surface">
                    <template x-if="pageRows.length === 0">
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center text-text-secondary">
                                {{ __('messages.no_data') }}
                            </td>
                        </tr>
                    </template>

                    <template x-for="row in pageRows" :key="row.id">
                        <tr class="hover:bg-surface-hover transition-colors">
                            <td class="px-4 py-3 text-sm text-text-primary font-medium" x-text="row.full_name"></td>
                            <td class="px-4 py-3 text-sm text-text-secondary" x-text="row.role || '-'"></td>
                            <td class="px-4 py-3 text-sm text-text-secondary" x-text="row.position || '-'"></td>
                            <td class="px-4 py-3 text-sm text-text-secondary" x-text="row.batch || '-'"></td>
                            <td class="px-4 py-3 text-sm text-text-secondary" x-text="formatDate(row.tanggal_masuk)"></td>
                            <td class="px-4 py-3 text-sm">
                                <div class="space-y-1">
                                    <span
                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium border"
                                        :class="statusClass(row)"
                                        x-text="statusLabel(row)"
                                    ></span>

                                    <template x-if="isResigned(row)">
                                        <div class="text-xs text-text-tertiary leading-snug">
                                            <div x-text="formatDate(row.resigned_at)"></div>
                                            <div>
                                                <span data-translate="validation_tenure_label">{{ __('messages.validation_tenure_label') }}</span>
                                                <span class="font-semibold text-text-secondary" x-text="tenureText(row)"></span>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm text-right whitespace-nowrap">
                                <x-button size="sm" variant="secondary" type="button" @click="openEdit(row)">
                                    <span data-translate="validation_action_review">{{ __('messages.validation_action_review') }}</span>
                                </x-button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <div class="p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <p class="text-sm text-text-secondary">
                <span data-translate="validation_showing">{{ __('messages.validation_showing') }}</span>
                <span class="font-semibold text-text-primary" x-text="pageRows.length"></span>
                <span data-translate="validation_of">{{ __('messages.validation_of') }}</span>
                <span class="font-semibold text-text-primary" x-text="filteredRows.length"></span>
            </p>

            <div class="flex items-center gap-2">
                <x-button size="sm" variant="secondary" type="button" @click="prevPage()" x-bind:disabled="page <= 1">
                    <span data-translate="previous">{{ __('messages.previous') }}</span>
                </x-button>
                <span class="text-sm text-text-secondary">
                    <span data-translate="page">{{ __('messages.page') }}</span>
                    <span class="font-semibold text-text-primary" x-text="page"></span>
                    <span class="text-text-tertiary">/</span>
                    <span class="font-semibold text-text-primary" x-text="pageCount"></span>
                </span>
                <x-button size="sm" variant="secondary" type="button" @click="nextPage()" x-bind:disabled="page >= pageCount">
                    <span data-translate="next">{{ __('messages.next') }}</span>
                </x-button>
            </div>
        </div>
    </x-card>

    <x-modal
        x-model="editOpen"
        :title="__('messages.validation_modal_title')"
        size="xl"
    >
        <form class="space-y-5" @submit.prevent="saveEdit()">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <x-input
                    name="v_full_name"
                    :label="__('messages.full_name')"
                    dataTranslateLabel="full_name"
                    x-model="form.full_name"
                    disabled
                />

                <x-input
                    name="v_citizen_id"
                    :label="__('messages.citizen_id')"
                    dataTranslateLabel="citizen_id"
                    x-model="form.citizen_id"
                    disabled
                />

                <x-select
                    name="v_role"
                    :label="__('messages.role')"
                    dataTranslateLabel="role"
                    :options="[
                        'Staff' => __('messages.role_staff'),
                        'Staff Manager' => __('messages.role_staff_manager'),
                        'Lead Manager' => __('messages.role_lead_manager'),
                        'Head Manager' => __('messages.role_head_manager'),
                        'Vice Director' => __('messages.role_vice_director'),
                        'Director' => __('messages.role_director'),
                    ]"
                    x-model="form.role"
                />

                <x-select
                    name="v_position"
                    :label="__('messages.position')"
                    dataTranslateLabel="position"
                    :placeholder="__('messages.select_position')"
                    :options="[
                        'Trainee' => __('messages.position_trainee'),
                        'Paramedic' => __('messages.position_paramedic'),
                        'Co. Asst' => __('messages.position_co_asst'),
                        'General Doctor' => __('messages.position_doctor_umum'),
                        'Specialist Doctor' => __('messages.position_doctor_specialist'),
                    ]"
                    x-model="form.position"
                />

                <x-input
                    name="v_batch"
                    :label="__('messages.batch')"
                    dataTranslateLabel="batch"
                    x-model="form.batch"
                    disabled
                />

                <x-input
                    name="v_join_date"
                    :label="__('messages.join_date')"
                    dataTranslateLabel="join_date"
                    x-model="form.tanggal_masuk"
                    disabled
                />
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <div class="rounded-2xl border border-border bg-surface-alt p-4">
                    <p class="text-sm font-semibold text-text-primary mb-3" data-translate="validation_documents">
                        {{ __('messages.validation_documents') }}
                    </p>

                    <p class="text-xs text-text-tertiary mb-3" data-translate="validation_doc_hint">
                        {{ __('messages.validation_doc_hint') }}
                    </p>

                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        @php
                            $docItems = [
                                ['key' => 'file_ktp', 'label' => __('messages.validation_doc_ktp')],
                                ['key' => 'file_skb', 'label' => __('messages.validation_doc_skb')],
                                ['key' => 'file_sim', 'label' => __('messages.validation_doc_sim')],
                                ['key' => 'file_kta', 'label' => __('messages.validation_doc_kta')],
                                ['key' => 'sertifikat_heli', 'label' => __('messages.validation_doc_heli')],
                                ['key' => 'sertifikat_operasi', 'label' => __('messages.validation_doc_operasi')],
                                ['key' => 'dokumen_lainnya', 'label' => __('messages.validation_doc_other')],
                            ];
                        @endphp

                        @foreach($docItems as $doc)
                            <button
                                type="button"
                                class="group relative rounded-2xl border border-border bg-surface overflow-hidden text-left hover:bg-surface-hover transition-colors"
                                x-bind:class="form.{{ $doc['key'] }} ? '' : 'opacity-50 pointer-events-none'"
                                @click="openPreview(@js($doc['label']), form.{{ $doc['key'] }})"
                            >
                                <div class="aspect-[4/3] bg-surface-alt flex items-center justify-center overflow-hidden">
                                    <template x-if="form.{{ $doc['key'] }}">
                                        <img
                                            x-bind:src="fileUrl(form.{{ $doc['key'] }})"
                                            x-bind:alt="@js($doc['label'])"
                                            class="w-full h-full object-cover group-hover:scale-[1.02] transition-transform"
                                            loading="lazy"
                                        >
                                    </template>
                                    <template x-if="!form.{{ $doc['key'] }}">
                                        <svg class="w-8 h-8 text-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                    </template>
                                </div>

                                <div class="p-3">
                                    <p class="text-xs font-semibold text-text-primary truncate">
                                        {{ $doc['label'] }}
                                    </p>
                                </div>
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="rounded-2xl border border-border bg-surface-alt p-4">
                    <p class="text-sm font-semibold text-text-primary mb-3" data-translate="validation_actions">
                        {{ __('messages.validation_actions') }}
                    </p>

                    <div class="space-y-3">
                        <template x-if="activeRow && isResigned(activeRow)">
                            <div class="rounded-xl border border-danger/30 bg-danger/10 p-3">
                                <p class="text-xs font-semibold text-danger" data-translate="validation_status_resign">
                                    {{ __('messages.validation_status_resign') }}
                                </p>
                                <p class="text-xs text-text-tertiary mt-1" x-text="formatDate(activeRow.resigned_at)"></p>
                                <p class="text-xs text-text-tertiary mt-1">
                                    <span data-translate="validation_tenure_label">{{ __('messages.validation_tenure_label') }}</span>
                                    <span class="font-semibold text-text-secondary" x-text="tenureText(activeRow)"></span>
                                </p>
                            </div>
                        </template>

                        <label class="inline-flex items-center gap-2 text-sm text-text-secondary">
                            <input type="checkbox" class="rounded border-border" x-model="form.is_verified">
                            <span data-translate="validation_is_verified">{{ __('messages.validation_is_verified') }}</span>
                        </label>

                        <label class="inline-flex items-center gap-2 text-sm text-text-secondary">
                            <input type="checkbox" class="rounded border-border" x-model="form.is_active">
                            <span data-translate="validation_is_active">{{ __('messages.validation_is_active') }}</span>
                        </label>

                        <x-alert type="warning" :icon="true">
                            <p class="text-sm" data-translate="validation_warning">{{ __('messages.validation_warning') }}</p>
                        </x-alert>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <x-button type="button" variant="secondary" @click="editOpen = false">
                    <span data-translate="cancel">{{ __('messages.cancel') }}</span>
                </x-button>
                <x-button type="submit" variant="primary" x-bind:disabled="saving">
                    <span data-translate="save">{{ __('messages.save') }}</span>
                </x-button>
            </div>
        </form>
    </x-modal>

    <x-modal
        x-model="previewOpen"
        :title="__('messages.validation_doc_preview')"
        size="full"
    >
        <div class="space-y-4">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-text-primary truncate" x-text="previewTitle"></p>
                    <p class="text-xs text-text-tertiary mt-1" data-translate="validation_zoom_hint">{{ __('messages.validation_zoom_hint') }}</p>
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    <x-button type="button" variant="secondary" size="sm" @click="zoomOut()">
                        <span data-translate="zoom_out">{{ __('messages.zoom_out') }}</span>
                    </x-button>
                    <x-button type="button" variant="secondary" size="sm" @click="resetZoom()">
                        <span data-translate="reset">{{ __('messages.reset') }}</span>
                    </x-button>
                    <x-button type="button" variant="secondary" size="sm" @click="zoomIn()">
                        <span data-translate="zoom_in">{{ __('messages.zoom_in') }}</span>
                    </x-button>
                </div>
            </div>

            <div class="flex items-center justify-end">
                <span class="text-xs text-text-tertiary" x-text="Math.round(previewScale * 100) + '%'"></span>
            </div>

            <div
                class="w-full h-[75svh] rounded-2xl border border-border bg-surface-alt overflow-hidden"
                @wheel.prevent="onPreviewWheel($event)"
            >
                <div
                    class="w-full h-full flex items-center justify-center touch-none"
                    @pointerdown="onPreviewPointerDown($event)"
                    @pointermove="onPreviewPointerMove($event)"
                    @pointerup="onPreviewPointerUp($event)"
                    @pointercancel="onPreviewPointerUp($event)"
                    @dblclick="resetZoom()"
                >
                    <img
                        x-bind:src="previewSrc"
                        x-bind:alt="previewTitle"
                        class="w-full h-full object-contain select-none"
                        draggable="false"
                        x-bind:style="`transform: translate(${previewTranslateX}px, ${previewTranslateY}px) scale(${previewScale}); transform-origin: center center;`"
                    >
                </div>
            </div>
        </div>
    </x-modal>
</div>
@endsection
