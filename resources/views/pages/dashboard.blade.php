@extends('layouts.app')

@section('title', 'Dashboard - Roxwood Health Medical Center')

@section('page-title', 'Dashboard')
@section('page-description', 'Welcome - Overview')

@section('content')
{{-- Stats Cards --}}
<x-grid :cols="1" :gap="'default'" class="mb-6">
    <x-stat-card
        title="Total Patients"
        value="2,847"
        change="+12.5%"
        changeType="positive"
        :icon="'<svg class=\'w-6 h-6 text-white\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z\'/></svg>'"
        color="primary"
    />

    <x-stat-card
        title="Total Doctors"
        value="156"
        change="+3"
        changeType="positive"
        :icon="'<svg class=\'w-6 h-6 text-white\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z\'/></svg>'"
        color="success"
    />

    <x-stat-card
        title="Appointments"
        value="384"
        change="-2.4%"
        changeType="negative"
        :icon="'<svg class=\'w-6 h-6 text-white\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z\'/></svg>'"
        color="warning"
    />

    <x-stat-card
        title="Revenue"
        value="Rp 1.2M"
        change="+8.1%"
        changeType="positive"
        :icon="'<svg class=\'w-6 h-6 text-white\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z\'/></svg>'"
        color="info"
    />
</x-grid>

{{-- Charts & Tables Grid --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Chart Area --}}
    <x-card title="Revenue Overview" :cols="'lg:col-span-2'" class="h-full">
        <div class="h-64 flex items-center justify-center bg-surface/50 rounded-lg">
            <div class="text-center">
                <svg class="w-16 h-16 mx-auto text-text-muted mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                <p class="text-text-secondary">Chart Component Placeholder</p>
                <p class="text-sm text-text-muted mt-1">Integrate Chart.js or ApexCharts here</p>
            </div>
        </div>
    </x-card>

    {{-- Recent Activity --}}
    <x-card title="Recent Activity">
        <div class="space-y-4">
            @foreach([
                ['icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>', 'title' => 'New patient registered', 'time' => '2 minutes ago', 'color' => 'text-primary-500'],
                ['icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>', 'title' => 'Appointment scheduled', 'time' => '15 minutes ago', 'color' => 'text-success-500'],
                ['icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>', 'title' => 'Lab report approved', 'time' => '1 hour ago', 'color' => 'text-info-500'],
                ['icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>', 'title' => 'Payment received', 'time' => '2 hours ago', 'color' => 'text-success-500'],
            ] as $activity)
            <div class="flex items-start gap-3">
                <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-surface flex items-center justify-center {{ $activity['color'] }}">
                    {!! $activity['icon'] !!}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-text-primary">{{ $activity['title'] }}</p>
                    <p class="text-xs text-text-secondary">{{ $activity['time'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </x-card>
</div>

 {{-- Patients Table --}}
 <x-card title="Recent Patients" class="mt-6">
    <div class="overflow-x-auto -mx-5 sm:mx-0">
        <x-table
            :headers="[
                ['label' => 'Patient', 'key' => 'name'],
                ['label' => 'ID', 'key' => 'id'],
                ['label' => 'Visit Date', 'key' => 'date'],
                ['label' => 'Status', 'key' => 'status'],
                ['label' => 'Actions', 'key' => 'actions'],
            ]"
            :striped="true"
            :hoverable="true"
            class="sm:mx-0"
        >
            @foreach([
                ['name' => 'John Doe', 'id' => 'P-001', 'date' => 'Feb 24, 2026', 'status' => 'Active', 'statusColor' => 'success'],
                ['name' => 'Jane Smith', 'id' => 'P-002', 'date' => 'Feb 23, 2026', 'status' => 'Pending', 'statusColor' => 'warning'],
                ['name' => 'Robert Johnson', 'id' => 'P-003', 'date' => 'Feb 22, 2026', 'status' => 'Active', 'statusColor' => 'success'],
                ['name' => 'Emily Davis', 'id' => 'P-004', 'date' => 'Feb 21, 2026', 'status' => 'Inactive', 'statusColor' => 'default'],
            ] as $patient)
            <tr>
                <td class="px-4 py-3">
                    <div class="flex items-center gap-3">
                        <x-avatar :name="$patient['name']" size="sm" />
                        <span class="font-medium text-text-primary">{{ $patient['name'] }}</span>
                    </div>
                </td>
                <td class="px-4 py-3 text-text-secondary">{{ $patient['id'] }}</td>
                <td class="px-4 py-3 text-text-secondary">{{ $patient['date'] }}</td>
                <td class="px-4 py-3">
                    <x-badge :variant="$patient['statusColor']">{{ $patient['status'] }}</x-badge>
                </td>
                <td class="px-4 py-3">
                    <div class="flex items-center gap-2">
                        <button class="p-1.5 rounded-lg hover:bg-surface-hover transition-colors">
                            <svg class="w-4 h-4 text-text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                        <button class="p-1.5 rounded-lg hover:bg-surface-hover transition-colors">
                            <svg class="w-4 h-4 text-text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                            </svg>
                        </button>
                    </div>
                </td>
            </tr>
            @endforeach
        </x-table>
    </div>
</x-card>
@endsection
