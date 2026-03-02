{{-- Patients --}}
@extends('layouts.app')

@section('title', __('messages.patients') . ' - ' . __('messages.app_name'))

@section('page-title', __('messages.patients'))
@section('page-description', __('messages.manage_patients'))

@section('content')
{{-- Page Header with Actions --}}
<x-card class="mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-text-primary">{{ __('messages.all_patients') }}</h1>
            <p class="text-sm text-text-secondary mt-1">{{ __('messages.total_patients') }}: <span class="font-medium text-text-primary">2,847</span></p>
        </div>
        <div class="flex items-center gap-3">
            <x-button variant="secondary" @click="$toast.info('Export feature coming soon!')">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span class="hidden sm:inline">{{ __('messages.export') }}</span>
            </x-button>
            <x-button variant="primary" @click="openAddModal = true">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span class="hidden sm:inline">{{ __('messages.add_patient') }}</span>
            </x-button>
        </div>
    </div>
</x-card>

{{-- Search and Filters --}}
<x-card class="mb-6">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        {{-- Search --}}
        <div class="md:col-span-2">
            <x-input
                type="text"
                name="search"
                placeholder="{{ __('messages.search_patients') }}"
                x-model="searchQuery"
            />
        </div>

        {{-- Department Filter --}}
        <div>
            <x-select
                name="department"
                :label="null"
                :options="[
                    ['value' => '', 'label' => __('messages.all_departments')],
                    ['value' => 'cardiology', 'label' => __('messages.cardiology')],
                    ['value' => 'neurology', 'label' => __('messages.neurology')],
                    ['value' => 'orthopedics', 'label' => __('messages.orthopedics')],
                    ['value' => 'pediatrics', 'label' => __('messages.pediatrics')],
                    ['value' => 'radiology', 'label' => __('messages.radiology')],
                ]"
                x-model="selectedDepartment"
            />
        </div>

        {{-- Status Filter --}}
        <div>
            <x-select
                name="status"
                :label="null"
                :options="[
                    ['value' => '', 'label' => __('messages.all_status')],
                    ['value' => 'active', 'label' => __('messages.active')],
                    ['value' => 'pending', 'label' => __('messages.pending')],
                    ['value' => 'inactive', 'label' => __('messages.inactive')],
                ]"
                x-model="selectedStatus"
            />
        </div>
    </div>
</x-card>

{{-- Data Table with Bulk Actions --}}
<x-card x-data="{ openAddModal: false, openViewModal: false, selectedPatient: null, searchQuery: '', selectedDepartment: '', selectedStatus: '' }">
    {{-- Bulk Actions Bar --}}
    <div x-show="selectedRows > 0" class="mb-4 p-3 bg-primary-50 border border-primary-200 rounded-xl flex items-center justify-between" x-cloak>
        <span class="text-sm text-primary-700"><span x-text="selectedRows"></span> {{ __('messages.selected') }}</span>
        <div class="flex items-center gap-2">
            <button class="px-3 py-1.5 text-sm rounded-lg border border-primary-300 bg-white text-primary-700 hover:bg-primary-50 transition-colors" @click="$toast.success('Bulk export started!')">
                {{ __('messages.export') }}
            </button>
            <button class="px-3 py-1.5 text-sm rounded-lg bg-danger text-white hover:bg-danger-600 transition-colors" @click="$toast.error('Bulk delete not implemented')">
                {{ __('messages.delete') }}
            </button>
        </div>
    </div>

    {{-- Patients Table --}}
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-border">
                    <th class="w-10 px-4 py-3 text-left">
                        <input type="checkbox" class="custom-checkbox" x-model="selectAll" @change="toggleAll()">
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">{{ __('messages.patient') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">{{ __('messages.id') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">{{ __('messages.department') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">{{ __('messages.status') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">{{ __('messages.last_visit') }}</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-text-secondary uppercase tracking-wider">{{ __('messages.actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                @foreach([
                    [
                        'id' => 'P-001',
                        'name' => 'John Doe',
                        'email' => 'john.doe@email.com',
                        'department' => 'cardiology',
                        'department_label' => 'Cardiology',
                        'status' => 'active',
                        'status_label' => 'Active',
                        'status_variant' => 'success',
                        'last_visit' => 'Feb 24, 2026',
                        'phone' => '+62 812 3456 7890',
                        'blood_type' => 'A+',
                        'age' => 45,
                        'gender' => 'Male',
                        'address' => 'Jl. Sudirman No. 123, Jakarta'
                    ],
                    [
                        'id' => 'P-002',
                        'name' => 'Jane Smith',
                        'email' => 'jane.smith@email.com',
                        'department' => 'neurology',
                        'department_label' => 'Neurology',
                        'status' => 'pending',
                        'status_label' => 'Pending',
                        'status_variant' => 'warning',
                        'last_visit' => 'Feb 23, 2026',
                        'phone' => '+62 813 4567 8901',
                        'blood_type' => 'B+',
                        'age' => 32,
                        'gender' => 'Female',
                        'address' => 'Jl. Thamrin No. 456, Jakarta'
                    ],
                    [
                        'id' => 'P-003',
                        'name' => 'Robert Johnson',
                        'email' => 'robert.j@email.com',
                        'department' => 'orthopedics',
                        'department_label' => 'Orthopedics',
                        'status' => 'active',
                        'status_label' => 'Active',
                        'status_variant' => 'success',
                        'last_visit' => 'Feb 22, 2026',
                        'phone' => '+62 814 5678 9012',
                        'blood_type' => 'O+',
                        'age' => 58,
                        'gender' => 'Male',
                        'address' => 'Jl. Gatot Subroto No. 789, Jakarta'
                    ],
                    [
                        'id' => 'P-004',
                        'name' => 'Emily Davis',
                        'email' => 'emily.davis@email.com',
                        'department' => 'pediatrics',
                        'department_label' => 'Pediatrics',
                        'status' => 'inactive',
                        'status_label' => 'Inactive',
                        'status_variant' => 'default',
                        'last_visit' => 'Feb 21, 2026',
                        'phone' => '+62 815 6789 0123',
                        'blood_type' => 'AB+',
                        'age' => 8,
                        'gender' => 'Female',
                        'address' => 'Jl. Rasuna Said No. 234, Jakarta'
                    ],
                    [
                        'id' => 'P-005',
                        'name' => 'Michael Brown',
                        'email' => 'm.brown@email.com',
                        'department' => 'radiology',
                        'department_label' => 'Radiology',
                        'status' => 'active',
                        'status_label' => 'Active',
                        'status_variant' => 'success',
                        'last_visit' => 'Feb 20, 2026',
                        'phone' => '+62 816 7890 1234',
                        'blood_type' => 'A-',
                        'age' => 41,
                        'gender' => 'Male',
                        'address' => 'Jl. FX Sudirman No. 567, Jakarta'
                    ],
                    [
                        'id' => 'P-006',
                        'name' => 'Sarah Wilson',
                        'email' => 'sarah.w@email.com',
                        'department' => 'cardiology',
                        'department_label' => 'Cardiology',
                        'status' => 'active',
                        'status_label' => 'Active',
                        'status_variant' => 'success',
                        'last_visit' => 'Feb 19, 2026',
                        'phone' => '+62 817 8901 2345',
                        'blood_type' => 'B-',
                        'age' => 29,
                        'gender' => 'Female',
                        'address' => 'Jl. Senopati No. 890, Jakarta'
                    ],
                ] as $patient)
                <tr class="hover:bg-surface-hover/50 transition-colors">
                    <td class="px-4 py-3">
                        <input type="checkbox" class="custom-checkbox">
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            <x-avatar :name="$patient['name']" size="sm" />
                            <div>
                                <p class="font-medium text-text-primary">{{ $patient['name'] }}</p>
                                <p class="text-xs text-text-secondary">{{ $patient['email'] }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        <span class="text-sm font-mono text-text-secondary">{{ $patient['id'] }}</span>
                    </td>
                    <td class="px-4 py-3">
                        <span class="text-sm text-text-primary">{{ $patient['department_label'] }}</span>
                    </td>
                    <td class="px-4 py-3">
                        <x-badge :variant="$patient['status_variant']">
                            {{ $patient['status_label'] }}
                        </x-badge>
                    </td>
                    <td class="px-4 py-3">
                        <span class="text-sm text-text-secondary">{{ $patient['last_visit'] }}</span>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-end gap-1">
                            <button
                                class="p-2 rounded-lg hover:bg-surface-hover transition-colors text-text-secondary hover:text-primary"
                                title="{{ __('messages.view') }}"
                                @click="selectedPatient = {{ json_encode($patient) }}; openViewModal = true"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                            <button
                                class="p-2 rounded-lg hover:bg-surface-hover transition-colors text-text-secondary hover:text-warning"
                                title="{{ __('messages.edit') }}"
                                @click="$toast.info('Edit feature coming soon!')"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                </svg>
                            </button>
                            <button
                                class="p-2 rounded-lg hover:bg-danger-50 transition-colors text-text-secondary hover:text-danger"
                                title="{{ __('messages.delete') }}"
                                @click="
                                    if (confirm('{{ __('messages.confirm_delete') }}: {{ $patient['name'] }}?')) {
                                        $toast.success('Patient deleted successfully!');
                                    }
                                "
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mt-6 pt-4 border-t border-border">
        <p class="text-sm text-text-secondary">
            {{ __('messages.showing') }} 1-6 {{ __('messages.of') }} 2,847 {{ __('messages.results') }}
        </p>
        <div class="flex items-center gap-2">
            <button class="px-3 py-2 rounded-lg border border-border text-text-secondary hover:bg-surface-hover disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>
            <button class="px-3 py-2 rounded-lg border border-primary bg-primary text-white">1</button>
            <button class="px-3 py-2 rounded-lg border border-border text-text-secondary hover:bg-surface-hover">2</button>
            <button class="px-3 py-2 rounded-lg border border-border text-text-secondary hover:bg-surface-hover">3</button>
            <span class="px-2 text-text-muted">...</span>
            <button class="px-3 py-2 rounded-lg border border-border text-text-secondary hover:bg-surface-hover">475</button>
            <button class="px-3 py-2 rounded-lg border border-border text-text-secondary hover:bg-surface-hover">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
        </div>
    </div>
</x-card>

{{-- Patient View Modal --}}
<x-modal
    x-show="openViewModal"
    title="{{ __('messages.patient_details') }}"
    size="lg"
    x-data="{
        openAddModal: false,
        openViewModal: false,
        selectedPatient: null
    }"
    @close-modal="openViewModal = false"
>
    <div x-show="selectedPatient" x-cloak>
        <div class="flex items-center gap-4 mb-6">
            <div class="w-16 h-16 rounded-full bg-primary-100 text-primary-600 flex items-center justify-center text-xl font-bold" x-text="selectedPatient?.name ? selectedPatient.name.charAt(0) : '?'"></div>
            <div>
                <h3 class="text-xl font-bold text-text-primary" x-text="selectedPatient?.name"></h3>
                <p class="text-text-secondary" x-text="selectedPatient?.email"></p>
                <span class="inline-block mt-2 px-3 py-1 rounded-full text-xs font-medium"
                      :class="{
                          'bg-success-50 text-success-600': selectedPatient?.status_variant === 'success',
                          'bg-danger-50 text-danger-600': selectedPatient?.status_variant === 'danger',
                          'bg-warning-50 text-warning-600': selectedPatient?.status_variant === 'warning',
                          'bg-gray-50 text-gray-600': !selectedPatient?.status_variant || selectedPatient?.status_variant === 'default'
                      }"
                      x-text="selectedPatient?.status_label"></span>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div class="p-4 bg-surface-alt rounded-xl">
                <p class="text-xs text-text-secondary uppercase tracking-wider mb-1">{{ __('messages.patient_id') }}</p>
                <p class="font-mono text-text-primary" x-text="selectedPatient?.id"></p>
            </div>
            <div class="p-4 bg-surface-alt rounded-xl">
                <p class="text-xs text-text-secondary uppercase tracking-wider mb-1">{{ __('messages.department') }}</p>
                <p class="text-text-primary" x-text="selectedPatient?.department_label"></p>
            </div>
            <div class="p-4 bg-surface-alt rounded-xl">
                <p class="text-xs text-text-secondary uppercase tracking-wider mb-1">{{ __('messages.phone') }}</p>
                <p class="text-text-primary" x-text="selectedPatient?.phone"></p>
            </div>
            <div class="p-4 bg-surface-alt rounded-xl">
                <p class="text-xs text-text-secondary uppercase tracking-wider mb-1">{{ __('messages.blood_type') }}</p>
                <p class="text-text-primary" x-text="selectedPatient?.blood_type"></p>
            </div>
            <div class="p-4 bg-surface-alt rounded-xl">
                <p class="text-xs text-text-secondary uppercase tracking-wider mb-1">{{ __('messages.age') }}</p>
                <p class="text-text-primary" x-text="selectedPatient?.age + ' years'"></p>
            </div>
            <div class="p-4 bg-surface-alt rounded-xl">
                <p class="text-xs text-text-secondary uppercase tracking-wider mb-1">{{ __('messages.gender') }}</p>
                <p class="text-text-primary" x-text="selectedPatient?.gender"></p>
            </div>
        </div>

        <div class="mt-4 p-4 bg-surface-alt rounded-xl">
            <p class="text-xs text-text-secondary uppercase tracking-wider mb-1">{{ __('messages.address') }}</p>
            <p class="text-text-primary" x-text="selectedPatient?.address"></p>
        </div>
    </div>

    @slot('footer')
        <div class="flex justify-end gap-3">
            <button @click="openViewModal = false" class="px-4 py-2 rounded-xl border border-border text-text-primary hover:bg-surface-hover transition-colors">
                {{ __('messages.close') }}
            </button>
            <button @click="$toast.info('Print feature coming soon!')" class="px-4 py-2 rounded-xl bg-primary text-white hover:bg-primary-600 transition-colors">
                {{ __('messages.print') }}
            </button>
        </div>
    @endslot
</x-modal>
@endsection
