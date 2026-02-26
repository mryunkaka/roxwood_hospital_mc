{{-- Chart Component - Reusable Chart.js wrapper --}}

@props([
    'type' => 'line',
    'height' => '300px',
    'data' => null,
    'options' => null,
    'class' => ''
])

@php
    $uniqueId = 'chart_' . uniqid();

    // Demo data jika tidak ada data yang diberikan
    if (!$data) {
        $data = [
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            'datasets' => [[
                'label' => 'Dataset 1',
                'data' => [65, 59, 80, 81, 56, 55],
                'color' => '#3b82f6'
            ]]
        ];
    }
@endphp

<div {{ $attributes->merge(['class' => 'chart-container relative ' . $class]) }} style="height: {{ $height }}">
    <canvas id="{{ $uniqueId }}"></canvas>
</div>

@once
@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('chartController', () => ({
        chart: null,

        init() {
            const canvas = this.$el.querySelector('canvas');
            if (!canvas) return;

            const type = canvas.closest('.chart-container').dataset.type || '{{ $type }}';
            const data = JSON.parse(canvas.closest('.chart-container').dataset.data || '{"labels":["Jan","Feb","Mar","Apr","May","Jun"],"datasets":[{"label":"Sample","data":[12,19,3,5,2,3],"color":"#3b82f6"}]}');

            this.createChart(canvas, type, data);
        },

        createChart(canvas, type, data) {
            if (this.chart) {
                this.chart.destroy();
            }

            const colors = this.getChartColors();

            // Format data untuk Chart.js
            const chartData = {
                labels: data.labels || [],
                datasets: (data.datasets || []).map(ds => ({
                    label: ds.label || 'Dataset',
                    data: ds.data || [],
                    borderColor: ds.color || colors.colors.primary,
                    backgroundColor: (ds.color || colors.colors.primary) + '80',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: type === 'line' ? false : true,
                    pointRadius: type === 'line' ? 4 : 0,
                    pointHoverRadius: 6,
                    borderRadius: type === 'bar' ? 6 : 0,
                    cutout: type === 'doughnut' ? '70%' : undefined,
                    hoverOffset: type === 'pie' || type === 'doughnut' ? 8 : 0
                }))
            };

            const options = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: {
                            color: colors.text.secondary,
                            font: { size: 12 },
                            usePointStyle: true,
                            padding: 16
                        }
                    },
                    tooltip: {
                        backgroundColor: colors.text.primary,
                        titleColor: colors.text.primary,
                        bodyColor: colors.text.secondary,
                        borderColor: colors.grid,
                        borderWidth: 1,
                        padding: 12,
                        cornerRadius: 8
                    }
                }
            };

            if (['line', 'bar'].includes(type)) {
                options.scales = {
                    x: {
                        grid: { color: colors.grid, drawBorder: false },
                        ticks: { color: colors.text.secondary, font: { size: 11 } }
                    },
                    y: {
                        grid: { color: colors.grid, drawBorder: false },
                        ticks: { color: colors.text.secondary, font: { size: 11 } }
                    }
                };
            }

            this.chart = new Chart(canvas, {
                type: type,
                data: chartData,
                options: options
            });
        },

        getChartColors() {
            const isDark = document.documentElement.classList.contains('theme-dark');
            const isStylis = document.documentElement.classList.contains('theme-stylis');

            return {
                text: {
                    primary: isDark ? '#f1f5f9' : '#0f172a',
                    secondary: isDark ? '#cbd5e1' : '#64748b'
                },
                grid: isDark ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.05)',
                colors: {
                    primary: isStylis ? '#14b8a6' : '#3b82f6',
                    success: isStylis ? '#10b981' : '#22c55e',
                    danger: isStylis ? '#ef4444' : '#f43f5e',
                    warning: isStylis ? '#f59e0b' : '#f59e0b',
                    info: isStylis ? '#06b6d4' : '#0ea5e9',
                    purple: '#8b5cf6',
                    orange: '#f97316',
                    pink: '#ec4899'
                }
            };
        }
    }));
});
</script>
@endpush
@endonce

@once
@push('styles')
<style>
.chart-container canvas {
    width: 100% !important;
    height: 100% !important;
}
</style>
@endpush
@endonce
