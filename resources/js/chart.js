/**
 * ============================================
 * ROXWOOD HEALTH MEDICAL CENTER
 * Chart Controller - Alpine.js Component
 * ============================================
 *
 * Controller untuk Chart.js integration
 * Theme-aware colors untuk light, dark, dan stylis themes
 */

import Chart from 'chart.js/auto';

// Theme-aware color configuration
const getChartColors = () => {
    const isDark = document.documentElement.classList.contains('theme-dark');
    const isStylis = document.documentElement.classList.contains('theme-stylis');

    // Text colors
    const primaryTextColor = isDark ? '#f1f5f9' : '#0f172a';
    const secondaryTextColor = isDark ? '#cbd5e1' : '#64748b';

    // Grid colors
    const gridColor = isDark ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.05)';

    // Main colors - Roxwood palette
    const colors = {
        primary: isStylis ? '#14b8a6' : '#3b82f6',
        primaryLight: isStylis ? '#5eead4' : '#93c5fd',
        primaryDark: isStylis ? '#0d9488' : '#1d4ed8',

        success: isStylis ? '#10b981' : '#22c55e',
        successLight: isStylis ? '#6ee7b7' : '#86efac',

        danger: isStylis ? '#ef4444' : '#f43f5e',
        dangerLight: isStylis ? '#fca5a5' : '#fda4af',

        warning: isStylis ? '#f59e0b' : '#f59e0b',
        warningLight: isStylis ? '#fcd34d' : '#fbbf24',

        info: isStylis ? '#06b6d4' : '#0ea5e9',
        infoLight: isStylis ? '#7dd3fc' : '#7dd3fc',

        purple: '#8b5cf6',
        purpleLight: '#c4b5fd',

        orange: '#f97316',
        orangeLight: '#fdba74',

        pink: '#ec4899',
        pinkLight: '#f9a8d4'
    };

    return {
        text: {
            primary: primaryTextColor,
            secondary: secondaryTextColor
        },
        grid: gridColor,
        colors,
        backgrounds: [
            colors.primary + '80',
            colors.success + '80',
            colors.danger + '80',
            colors.warning + '80',
            colors.info + '80',
            colors.purple + '80',
            colors.orange + '80',
            colors.pink + '80'
        ],
        borders: [
            colors.primary,
            colors.success,
            colors.danger,
            colors.warning,
            colors.info,
            colors.purple,
            colors.orange,
            colors.pink
        ]
    };
};

// Chart instances untuk cleanup
const chartInstances = new Map();

export default function chartController() {
    return {
        chart: null,
        type: 'line',
        data: null,
        options: null,

        init() {
            // Initialize chart when element is ready
            this.$nextTick(() => {
                if (this.$el && typeof this.$el.getContext === 'function' && this.data) {
                    this.createChart();
                }
            });

            // Cleanup on destroy
            this.$el._chart_destroy = () => {
                if (this.chart) {
                    this.chart.destroy();
                    this.chart = null;
                }
                chartInstances.delete(this.$el);
            };
        },

        destroy() {
            if (this.chart) {
                this.chart.destroy();
                this.chart = null;
            }
            chartInstances.delete(this.$el);
        },

        createChart() {
            if (this.chart) {
                this.chart.destroy();
                this.chart = null;
            }

            const colors = getChartColors();
            const canvas = this.$el;
            if (!canvas || typeof canvas.getContext !== 'function') return;

            const raw = (value) => (window.Alpine && typeof window.Alpine.raw === 'function')
                ? window.Alpine.raw(value)
                : value;

            this.chart = new Chart(canvas, {
                type: this.type,
                data: raw(this.data),
                options: raw(this.getOptions(colors))
            });

            // Store instance for cleanup
            chartInstances.set(this.$el, this.chart);
        },

        getOptions(colors) {
            const raw = (value) => (window.Alpine && typeof window.Alpine.raw === 'function')
                ? window.Alpine.raw(value)
                : value;

            const baseOptions = {
                responsive: true,
                maintainAspectRatio: false,
                animation: false,
                plugins: {
                    legend: {
                        labels: {
                            color: colors.text.secondary,
                            font: {
                                family: "'Inter', sans-serif",
                                size: 12
                            },
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
                        cornerRadius: 8,
                        displayColors: true,
                        usePointStyle: true
                    }
                }
            };

            // Type-specific options
            if (['line', 'bar'].includes(this.type)) {
                baseOptions.scales = {
                    x: {
                        grid: {
                            color: colors.grid,
                            drawBorder: false
                        },
                        ticks: {
                            color: colors.text.secondary,
                            font: {
                                family: "'Inter', sans-serif",
                                size: 11
                            }
                        }
                    },
                    y: {
                        grid: {
                            color: colors.grid,
                            drawBorder: false
                        },
                        ticks: {
                            color: colors.text.secondary,
                            font: {
                                family: "'Inter', sans-serif",
                                size: 11
                            }
                        }
                    }
                };
            }

            return { ...baseOptions, ...(raw(this.options) || {}) };
        },

        update() {
            if (this.chart) {
                const colors = getChartColors();
                const raw = (value) => (window.Alpine && typeof window.Alpine.raw === 'function')
                    ? window.Alpine.raw(value)
                    : value;
                this.chart.options = raw(this.getOptions(colors));
                this.chart.update();
            }
        }
    };
}

// Global function untuk theme change
export function updateAllCharts() {
    chartInstances.forEach((chart, el) => {
        if (!chart || !chart.ctx || !chart.canvas) {
            chartInstances.delete(el);
            return;
        }

        const colors = getChartColors();
        chart.options = {
            ...chart.options,
            plugins: {
                ...chart.options.plugins,
                legend: {
                    ...chart.options.plugins.legend,
                    labels: {
                        ...chart.options.plugins.legend.labels,
                        color: colors.text.secondary
                    }
                },
                tooltip: {
                    ...chart.options.plugins.tooltip,
                    backgroundColor: colors.colors.primary + '20',
                    titleColor: colors.text.primary,
                    bodyColor: colors.text.secondary
                }
            }
        };

        if (chart.options.scales) {
            const gridColor = colors.grid;
            if (chart.options.scales.x) {
                chart.options.scales.x.grid.color = gridColor;
                chart.options.scales.x.ticks.color = colors.text.secondary;
            }
            if (chart.options.scales.y) {
                chart.options.scales.y.grid.color = gridColor;
                chart.options.scales.y.ticks.color = colors.text.secondary;
            }
        }

        try {
            chart.update('none');
        } catch (e) {
            chartInstances.delete(el);
        }
    });
}

// Listen untuk theme changes
window.addEventListener('theme-changed', () => {
    updateAllCharts();
});

// Register sebagai Alpine global
document.addEventListener('alpine:init', () => {
    Alpine.data('chartController', chartController);
});

// Export helper untuk membuat chart data
export const ChartData = {
    lineChart(labels, datasets) {
        return {
            labels,
            datasets: datasets.map(d => ({
                label: d.label,
                data: d.data,
                borderColor: d.color || '#3b82f6',
                backgroundColor: (d.color || '#3b82f6') + '20',
                borderWidth: 2,
                tension: 0.4,
                fill: d.fill || false,
                pointRadius: 4,
                pointHoverRadius: 6,
                pointBackgroundColor: d.color || '#3b82f6',
                pointBorderColor: '#fff',
                pointBorderWidth: 2
            }))
        };
    },

    barChart(labels, datasets) {
        return {
            labels,
            datasets: datasets.map(d => ({
                label: d.label,
                data: d.data,
                backgroundColor: d.color || '#3b82f6',
                borderColor: d.color || '#3b82f6',
                borderWidth: 0,
                borderRadius: 6,
                borderSkipped: false
            }))
        };
    },

    pieChart(data, labels) {
        const colors = getChartColors();
        return {
            labels,
            datasets: [{
                data,
                backgroundColor: [
                    colors.colors.primary,
                    colors.colors.success,
                    colors.colors.warning,
                    colors.colors.danger,
                    colors.colors.info,
                    colors.colors.purple
                ],
                borderColor: 'transparent',
                borderWidth: 0,
                hoverOffset: 8
            }]
        };
    },

    doughnutChart(data, labels) {
        const colors = getChartColors();
        return {
            labels,
            datasets: [{
                data,
                backgroundColor: [
                    colors.colors.primary,
                    colors.colors.success,
                    colors.colors.warning,
                    colors.colors.danger
                ],
                borderColor: 'transparent',
                borderWidth: 0,
                hoverOffset: 8,
                cutout: '70%'
            }]
        };
    }
};
