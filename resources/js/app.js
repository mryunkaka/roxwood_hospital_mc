/**
 * ============================================
 * ROXWOOD HEALTH MEDICAL CENTER
 * Main Application Entry Point
 * ============================================
 */

// Import Alpine.js
import Alpine from 'alpinejs';

// Import controllers
import themeController from './theme.js';
import langController from './lang.js';
import clockController from './clock.js';

// Setup Alpine
window.Alpine = Alpine;

// Register controllers
Alpine.data('themeController', themeController);
Alpine.data('langController', langController);
Alpine.data('clockController', clockController);

// Start Alpine
Alpine.start();

// Import CSS
import '../css/app.css';

// Log init
console.log('🏥 Roxwood Health Medical Center - UI System Initialized');
