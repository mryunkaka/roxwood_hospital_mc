{{-- Manajemen User --}}
@extends('layouts.app')

@section('title', __('messages.manage_users_title') . ' - ' . ($appName ?? config('app.name')))

@section('page-title', __('messages.manage_users_title'))
@section('page-description', __('messages.manage_users_subtitle'))

@section('content')
@php
    $positionOptions = [
        ['value' => 'Trainee', 'label' => __('messages.position_trainee')],
        ['value' => 'Paramedic', 'label' => __('messages.position_paramedic')],
        ['value' => 'Co. Asst', 'label' => __('messages.position_co_asst')],
        ['value' => 'General Doctor', 'label' => __('messages.position_doctor_umum')],
        ['value' => 'Specialist Doctor', 'label' => __('messages.position_doctor_specialist')],
    ];

    $roleOptions = [
        ['value' => 'Staff', 'label' => 'Staff'],
        ['value' => 'Staff Manager', 'label' => 'Staff Manager'],
        ['value' => 'Lead Manager', 'label' => 'Lead Manager'],
        ['value' => 'Head Manager', 'label' => 'Head Manager'],
        ['value' => 'Vice Director', 'label' => 'Vice Director'],
        ['value' => 'Director', 'label' => 'Director'],
    ];
@endphp

<div
    x-data="manageUsersPage({
        locale: @js(app()->getLocale()),
        users: @js($users ?? []),
        storeUrl: @js(route('users.manage.users.store')),
        updateUrlTemplate: @js(route('users.manage.users.update', ['userRh' => 0])),
        resignUrlTemplate: @js(route('users.manage.users.resign', ['userRh' => 0])),
        reactivateUrlTemplate: @js(route('users.manage.users.reactivate', ['userRh' => 0])),
        deleteKodeMedisUrlTemplate: @js(route('users.manage.users.delete_kode_medis', ['userRh' => 0])),
        destroyUrlTemplate: @js(route('users.manage.users.destroy', ['userRh' => 0])),
    })"
    class="space-y-6"
>
    <x-card>
        <x-alert type="info" :icon="true">
            <p class="text-sm" data-translate="manage_users_hint">{{ __('messages.manage_users_hint') }}</p>
        </x-alert>

        <div class="mt-5 flex flex-col lg:flex-row lg:items-end gap-3">
            <div class="w-full lg:w-56">
                <x-select
                    name="mu_search_column"
                    :label="__('messages.manage_users_search_column')"
                    dataTranslateLabel="manage_users_search_column"
                    :options="[
                        'all' => __('messages.manage_users_search_all'),
                        'name' => __('messages.manage_users_search_name'),
                        'position' => __('messages.manage_users_search_position'),
                        'role' => __('messages.manage_users_search_role'),
                        'docs' => __('messages.manage_users_search_docs'),
                        'join' => __('messages.manage_users_search_join'),
                    ]"
                    :value="'all'"
                    x-model="searchColumn"
                />
            </div>

            <div class="flex-1">
                <x-input
                    name="mu_search"
                    :label="__('messages.search')"
                    dataTranslateLabel="search"
                    :placeholder="__('messages.manage_users_search_all_placeholder')"
                    x-model="search"
                    x-bind:placeholder="searchPlaceholder"
                />
            </div>

            <div class="flex flex-col sm:flex-row gap-2 w-full lg:w-auto">
                <x-button type="button" variant="secondary" @click="exportText()">
                    <span data-translate="manage_users_export_text">{{ __('messages.manage_users_export_text') }}</span>
                </x-button>

                <x-button type="button" variant="success" @click="openAdd()">
                    <span data-translate="manage_users_add">{{ __('messages.manage_users_add') }}</span>
                </x-button>
            </div>
        </div>
    </x-card>

    <div class="space-y-4">
        <template x-if="groups.length === 0">
            <x-card>
                <p class="text-sm text-text-secondary">{{ __('messages.no_data') }}</p>
            </x-card>
        </template>

        <template x-for="group in groups" :key="group.key">
            <div class="rounded-2xl bg-surface border border-border shadow">
                <div class="p-5 sm:p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="min-w-0">
                        <p class="text-base font-semibold text-text-primary truncate" x-text="group.title"></p>
                        <p class="text-xs text-text-tertiary mt-1">
                            <span data-translate="total">{{ __('messages.total') }}</span>
                            <span class="font-semibold text-text-secondary" x-text="group.users.length"></span>
                        </p>
                    </div>

                    <template x-if="group.key === 'none'">
                        <x-button type="button" variant="secondary" size="sm" @click="exportText({ onlyNoBatch: true })">
                            <span data-translate="manage_users_export_no_batch">{{ __('messages.manage_users_export_no_batch') }}</span>
                        </x-button>
                    </template>
                </div>

                <div class="px-5 sm:px-6 pb-6">
                    <x-table
                        :headers="[
                            __('messages.manage_users_col_no'),
                            __('messages.full_name'),
                            __('messages.position'),
                            __('messages.role'),
                            __('messages.join_date'),
                            __('messages.manage_users_col_docs'),
                            __('messages.actions'),
                        ]"
                        :striped="true"
                        :bordered="true"
                        :compact="true"
                    >
                        <template x-for="(u, idx) in group.users" :key="u.id">
                            <tr class="align-top">
                                <td class="px-4 py-2 text-sm text-text-tertiary" x-text="idx + 1"></td>

                                <td class="px-4 py-2">
                                    <p class="text-sm font-semibold text-text-primary" x-text="u.full_name"></p>

                                    <template x-if="u.reactivated_at">
                                        <p class="text-xs text-success mt-1">
                                            <span data-translate="manage_users_reactivated">{{ __('messages.manage_users_reactivated') }}</span>
                                            <span x-text="formatDateHuman(u.reactivated_at)"></span>
                                        </p>
                                    </template>

                                    <template x-if="!u.is_active && u.resigned_at">
                                        <p class="text-xs text-text-tertiary mt-1">
                                            <span data-translate="manage_users_resigned">{{ __('messages.manage_users_resigned') }}</span>
                                            <span x-text="formatDateHuman(u.resigned_at)"></span>
                                        </p>
                                    </template>
                                </td>

                                <td class="px-4 py-2 text-sm text-text-secondary" x-text="u.position || '-'"></td>
                                <td class="px-4 py-2 text-sm text-text-secondary" x-text="u.role || '-'"></td>

                                <td class="px-4 py-2">
                                    <template x-if="u.tanggal_masuk">
                                        <div class="space-y-0.5">
                                            <p class="text-sm text-text-secondary" x-text="formatJoinDate(u.tanggal_masuk)"></p>
                                            <p class="text-xs text-text-tertiary" x-text="tenureText(u.tanggal_masuk)"></p>
                                        </div>
                                    </template>
                                    <template x-if="!u.tanggal_masuk">
                                        <span class="text-sm text-text-tertiary">-</span>
                                    </template>
                                </td>

                                <td class="px-4 py-2">
                                    <div class="flex flex-wrap gap-2">
                                        <template x-for="doc in userDocs(u)" :key="doc.key">
                                            <x-button
                                                type="button"
                                                variant="secondary"
                                                size="xs"
                                                @click="openPreview(doc, userDocs(u))"
                                            >
                                                <span x-text="doc.label"></span>
                                            </x-button>
                                        </template>

                                        <template x-if="userDocs(u).length === 0">
                                            <span class="text-xs text-text-tertiary">-</span>
                                        </template>
                                    </div>
                                </td>

                                <td class="px-4 py-2">
                                    <div class="flex flex-wrap justify-end gap-2">
                                        <x-button type="button" variant="secondary" size="xs" @click="openEdit(u)" x-bind:disabled="!u.can_manage">
                                            <span data-translate="edit">{{ __('messages.edit') }}</span>
                                        </x-button>

                                        <template x-if="u.is_active">
                                            <x-button type="button" variant="warning" size="xs" @click="openResign(u)" x-bind:disabled="!u.can_manage">
                                                <span data-translate="manage_users_resign_btn">{{ __('messages.manage_users_resign_btn') }}</span>
                                            </x-button>
                                        </template>
                                        <template x-if="!u.is_active">
                                            <x-button type="button" variant="success" size="xs" @click="openReactivate(u)" x-bind:disabled="!u.can_manage">
                                                <span data-translate="manage_users_reactivate_btn">{{ __('messages.manage_users_reactivate_btn') }}</span>
                                            </x-button>
                                        </template>

                                        <x-button type="button" variant="danger" size="xs" @click="openDelete(u)" x-bind:disabled="!u.can_manage">
                                            <span data-translate="delete">{{ __('messages.delete') }}</span>
                                        </x-button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </x-table>
                </div>
            </div>
        </template>
    </div>

    {{-- Add User --}}
    <x-modal x-model="addOpen" :title="__('messages.manage_users_add_title')">
        <form class="space-y-4" @submit.prevent="submitAdd()">
            <x-input
                name="add_full_name"
                :label="__('messages.full_name')"
                dataTranslateLabel="full_name"
                x-model="addForm.full_name"
                :required="true"
            />

            <x-select
                name="add_position"
                :label="__('messages.position')"
                dataTranslateLabel="position"
                :options="$positionOptions"
                x-model="addForm.position"
                :required="true"
            />

            <x-select
                name="add_role"
                :label="__('messages.role')"
                dataTranslateLabel="role"
                :options="$roleOptions"
                x-model="addForm.role"
                :required="true"
            />

            <x-input
                type="number"
                name="add_batch"
                :label="__('messages.batch')"
                dataTranslateLabel="batch"
                min="1"
                max="26"
                x-model="addForm.batch"
                :hint="__('messages.manage_users_batch_hint')"
            />

            <x-alert type="info" :icon="true">
                <p class="text-sm">
                    <span data-translate="manage_users_default_pin">{{ __('messages.manage_users_default_pin') }}</span>
                    <span class="font-semibold">0000</span>
                </p>
            </x-alert>

            <div class="flex justify-end gap-2 pt-1">
                <x-button type="button" variant="secondary" @click="addOpen = false">
                    <span data-translate="cancel">{{ __('messages.cancel') }}</span>
                </x-button>
                <x-button type="submit" variant="success" x-bind:disabled="saving">
                    <span data-translate="save">{{ __('messages.save') }}</span>
                </x-button>
            </div>
        </form>
    </x-modal>

    {{-- Edit User --}}
    <x-modal x-model="editOpen" :title="__('messages.manage_users_edit_title')">
        <form class="space-y-4" @submit.prevent="submitEdit()">
            <x-input
                type="number"
                name="edit_batch"
                :label="__('messages.batch')"
                dataTranslateLabel="batch"
                min="1"
                max="26"
                x-model="editForm.batch"
                :hint="__('messages.manage_users_batch_hint')"
            />

            <div class="flex items-end gap-2">
                <div class="flex-1 min-w-0">
                    <x-input
                        name="edit_kode"
                        :label="__('messages.manage_users_kode_medis')"
                        dataTranslateLabel="manage_users_kode_medis"
                        x-model="editForm.kode_nomor_induk_rs"
                        readonly
                    />
                </div>

                <x-button type="button" variant="danger" size="sm" @click="deleteKodeMedis()" x-bind:disabled="!editForm.kode_nomor_induk_rs || saving">
                    <span data-translate="manage_users_delete_kode">{{ __('messages.manage_users_delete_kode') }}</span>
                </x-button>
            </div>

            <template x-if="editForm.kode_nomor_induk_rs">
                <x-alert type="warning" :icon="true">
                    <p class="text-sm" data-translate="manage_users_delete_kode_warning">{{ __('messages.manage_users_delete_kode_warning') }}</p>
                </x-alert>
            </template>

            <x-input
                name="edit_full_name"
                :label="__('messages.full_name')"
                dataTranslateLabel="full_name"
                x-model="editForm.full_name"
                :required="true"
            />

            <x-select
                name="edit_position"
                :label="__('messages.position')"
                dataTranslateLabel="position"
                :options="$positionOptions"
                x-model="editForm.position"
                :required="true"
            />

            <x-select
                name="edit_role"
                :label="__('messages.role')"
                dataTranslateLabel="role"
                :options="$roleOptions"
                x-model="editForm.role"
                :required="true"
            />

            <x-input
                type="password"
                name="edit_new_pin"
                :label="__('messages.manage_users_new_pin')"
                dataTranslateLabel="manage_users_new_pin"
                inputmode="numeric"
                pattern="[0-9]{4}"
                maxlength="4"
                x-model="editForm.new_pin"
                :hint="__('messages.manage_users_new_pin_hint')"
            />

            <div class="flex justify-end gap-2 pt-1">
                <x-button type="button" variant="secondary" @click="editOpen = false">
                    <span data-translate="cancel">{{ __('messages.cancel') }}</span>
                </x-button>
                <x-button type="submit" variant="success" x-bind:disabled="saving">
                    <span data-translate="save">{{ __('messages.save') }}</span>
                </x-button>
            </div>
        </form>
    </x-modal>

    {{-- Resign --}}
    <x-modal x-model="resignOpen" :title="__('messages.manage_users_resign_title')">
        <form class="space-y-4" @submit.prevent="submitResign()">
            <p class="text-sm text-text-secondary">
                <span data-translate="manage_users_resign_confirm">{{ __('messages.manage_users_resign_confirm') }}</span>
                <span class="font-semibold text-text-primary" x-text="activeRow?.full_name || '-'"></span>
            </p>

            <div>
                <label class="block text-sm font-medium text-text-primary mb-2" data-translate="manage_users_resign_reason">
                    {{ __('messages.manage_users_resign_reason') }}
                </label>
                <textarea
                    class="w-full px-4 py-3 rounded-xl bg-surface border border-border focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none transition-all text-sm placeholder:text-text-hint"
                    rows="4"
                    x-model="resignReason"
                    required
                ></textarea>
            </div>

            <div class="flex justify-end gap-2 pt-1">
                <x-button type="button" variant="secondary" @click="resignOpen = false">
                    <span data-translate="cancel">{{ __('messages.cancel') }}</span>
                </x-button>
                <x-button type="submit" variant="warning" x-bind:disabled="saving">
                    <span data-translate="manage_users_resign_btn">{{ __('messages.manage_users_resign_btn') }}</span>
                </x-button>
            </div>
        </form>
    </x-modal>

    {{-- Reactivate --}}
    <x-modal x-model="reactivateOpen" :title="__('messages.manage_users_reactivate_title')">
        <form class="space-y-4" @submit.prevent="submitReactivate()">
            <p class="text-sm text-text-secondary">
                <span data-translate="manage_users_reactivate_confirm">{{ __('messages.manage_users_reactivate_confirm') }}</span>
                <span class="font-semibold text-text-primary" x-text="activeRow?.full_name || '-'"></span>
            </p>

            <div>
                <label class="block text-sm font-medium text-text-primary mb-2" data-translate="manage_users_reactivate_note">
                    {{ __('messages.manage_users_reactivate_note') }}
                </label>
                <textarea
                    class="w-full px-4 py-3 rounded-xl bg-surface border border-border focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none transition-all text-sm placeholder:text-text-hint"
                    rows="4"
                    x-model="reactivateNote"
                    x-bind:placeholder="t('manage_users_reactivate_note_placeholder', '')"
                ></textarea>
            </div>

            <div class="flex justify-end gap-2 pt-1">
                <x-button type="button" variant="secondary" @click="reactivateOpen = false">
                    <span data-translate="cancel">{{ __('messages.cancel') }}</span>
                </x-button>
                <x-button type="submit" variant="success" x-bind:disabled="saving">
                    <span data-translate="manage_users_reactivate_btn">{{ __('messages.manage_users_reactivate_btn') }}</span>
                </x-button>
            </div>
        </form>
    </x-modal>

    {{-- Delete --}}
    <x-modal x-model="deleteOpen" :title="__('messages.manage_users_delete_title')">
        <form class="space-y-4" @submit.prevent="submitDelete()">
            <x-alert type="danger" :icon="true" :title="__('messages.manage_users_delete_warning_title')">
                <p class="text-sm">
                    <span data-translate="manage_users_delete_warning">{{ __('messages.manage_users_delete_warning') }}</span>
                    <span class="font-semibold" x-text="activeRow?.full_name || '-'"></span>
                </p>
            </x-alert>

            <div class="flex justify-end gap-2 pt-1">
                <x-button type="button" variant="secondary" @click="deleteOpen = false">
                    <span data-translate="cancel">{{ __('messages.cancel') }}</span>
                </x-button>
                <x-button type="submit" variant="danger" x-bind:disabled="saving">
                    <span data-translate="delete">{{ __('messages.delete') }}</span>
                </x-button>
            </div>
        </form>
    </x-modal>

    {{-- Document Preview --}}
    <x-modal x-model="previewOpen" :title="__('messages.manage_users_doc_preview')" size="full">
        <div class="space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-text-primary truncate" x-text="previewDoc?.label || ''"></p>
                    <p class="text-xs text-text-tertiary mt-1" data-translate="manage_users_zoom_hint">{{ __('messages.manage_users_zoom_hint') }}</p>
                </div>

                <div class="flex flex-wrap items-center gap-2 shrink-0">
                    <x-button type="button" variant="secondary" size="sm" @click="prevDoc()" x-bind:disabled="previewList.length <= 1">
                        <span data-translate="previous">{{ __('messages.previous') }}</span>
                    </x-button>
                    <x-button type="button" variant="secondary" size="sm" @click="nextDoc()" x-bind:disabled="previewList.length <= 1">
                        <span data-translate="next">{{ __('messages.next') }}</span>
                    </x-button>
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

            <div class="flex items-center justify-between text-xs text-text-tertiary">
                <span x-text="(previewIndex + 1) + ' / ' + (previewList.length || 0)"></span>
                <span x-text="Math.round(previewScale * 100) + '%'"></span>
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
                        x-bind:src="previewDoc?.src || ''"
                        x-bind:alt="previewDoc?.label || ''"
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
