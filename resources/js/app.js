/**
 * ============================================
 * ROXWOOD HEALTH MEDICAL CENTER
 * Main Application Entry Point
 * ============================================
 */

// Import Alpine.js
import Alpine from 'alpinejs';

// Import controllers (toastController registers itself in alpine:init)
import themeController from './theme.js';
import langController from './lang.js';
import clockController from './clock.js';
import accessibilityController from './accessibility.js';
import './toast.js'; // Import toast.js to register its alpine:init handler
import chartController, { ChartData, updateAllCharts } from './chart.js';

// Setup Alpine
window.Alpine = Alpine;

// Global session-guard store (used for forced logout / session invalid modal)
Alpine.store('sessionGuard', {
    open: false,
    reason: null,
    forcedByDevice: null,

    show(payload = {}) {
        this.reason = payload.reason || 'superseded';
        this.forcedByDevice = payload.forcedByDevice || null;
        this.open = true;
    },

    acknowledge() {
        this.open = false;
        window.location.href = '/login';
    }
});

// Register controllers (toastController is registered in toast.js alpine:init event)
Alpine.data('themeController', themeController);
Alpine.data('langController', langController);
Alpine.data('clockController', clockController);
Alpine.data('accessibilityController', accessibilityController);
Alpine.data('chartController', chartController);

// Export chart helpers for global access
window.ChartData = ChartData;
window.updateAllCharts = updateAllCharts;

// Start Alpine
Alpine.start();

// Import CSS
import '../css/app.css';

// Log init
console.log('🏥 Roxwood Health Medical Center - UI System Initialized');
