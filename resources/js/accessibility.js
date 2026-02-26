/**
 * ============================================
 * ROXWOOD HEALTH MEDICAL CENTER
 * Accessibility Controller - Alpine.js Component
 * ============================================
 *
 * Mengontrol fitur accessibility:
 * - Font size scaling
 * - High contrast mode
 * - Reduced motion mode
 */

export default function accessibilityController() {
    return {
        // Font size settings
        fontSize: 'medium', // 'small' | 'medium' | 'large' | 'extra-large'
        customScale: 100, // 75-150%

        // High contrast mode
        highContrast: false,

        // Reduced motion
        reducedMotion: false,

        init() {
            // Load preferences dari localStorage
            this.loadPreferences();

            // Deteksi system preference untuk reduced motion
            const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

            // Jika user belum set preference manual, gunakan system preference
            if (!localStorage.getItem('roxwood-reduced-motion')) {
                this.reducedMotion = prefersReducedMotion;
            }

            // Listen untuk perubahan system preference
            window.matchMedia('(prefers-reduced-motion: reduce)').addEventListener('change', (e) => {
                if (!localStorage.getItem('roxwood-reduced-motion')) {
                    this.reducedMotion = e.matches;
                    this.applyReducedMotion();
                }
            });

            // Apply semua settings
            this.applyAll();
        },

        loadPreferences() {
            const savedFontSize = localStorage.getItem('roxwood-font-size');
            const savedScale = localStorage.getItem('roxwood-font-scale');
            const savedHighContrast = localStorage.getItem('roxwood-high-contrast');
            const savedReducedMotion = localStorage.getItem('roxwood-reduced-motion');

            if (savedFontSize) this.fontSize = savedFontSize;
            if (savedScale) this.customScale = parseInt(savedScale);
            if (savedHighContrast) this.highContrast = savedHighContrast === 'true';
            if (savedReducedMotion) this.reducedMotion = savedReducedMotion === 'true';
        },

        applyAll() {
            this.applyFontSize();
            this.applyHighContrast();
            this.applyReducedMotion();
        },

        // Font Size Methods
        setFontSize(size) {
            this.fontSize = size;
            this.customScale = this.getSizeScale(size);
            this.applyFontSize();
            this.savePreferences();
        },

        setCustomScale(scale) {
            this.customScale = Math.max(75, Math.min(150, scale));
            this.fontSize = 'custom';
            this.applyFontSize();
            this.savePreferences();
        },

        getSizeScale(size) {
            const scales = {
                'small': 87.5,
                'medium': 100,
                'large': 112.5,
                'extra-large': 125
            };
            return scales[size] || 100;
        },

        applyFontSize() {
            const html = document.documentElement;

            // Remove semua size classes
            html.classList.remove('text-sm', 'text-base', 'text-lg', 'text-xl');

            // Set base font size using scale
            html.style.fontSize = `${this.customScale}%`;

            // Add class untuk预设 sizes
            switch (this.fontSize) {
                case 'small':
                    html.classList.add('text-sm');
                    break;
                case 'large':
                    html.classList.add('text-lg');
                    break;
                case 'extra-large':
                    html.classList.add('text-xl');
                    break;
                default:
                    html.classList.add('text-base');
            }

            // Dispatch event
            window.dispatchEvent(new CustomEvent('font-size-changed', {
                detail: { fontSize: this.fontSize, scale: this.customScale }
            }));
        },

        increaseFontSize() {
            const sizes = ['small', 'medium', 'large', 'extra-large'];
            const currentIndex = sizes.indexOf(this.fontSize);
            if (currentIndex < sizes.length - 1) {
                this.setFontSize(sizes[currentIndex + 1]);
            } else if (this.fontSize === 'custom' && this.customScale < 150) {
                this.setCustomScale(this.customScale + 12.5);
            }
        },

        decreaseFontSize() {
            const sizes = ['small', 'medium', 'large', 'extra-large'];
            const currentIndex = sizes.indexOf(this.fontSize);
            if (currentIndex > 0) {
                this.setFontSize(sizes[currentIndex - 1]);
            } else if (this.fontSize === 'custom' && this.customScale > 75) {
                this.setCustomScale(this.customScale - 12.5);
            }
        },

        resetFontSize() {
            this.setFontSize('medium');
        },

        // High Contrast Methods
        toggleHighContrast() {
            this.highContrast = !this.highContrast;
            this.applyHighContrast();
            this.savePreferences();
        },

        setHighContrast(enabled) {
            this.highContrast = enabled;
            this.applyHighContrast();
            this.savePreferences();
        },

        applyHighContrast() {
            const html = document.documentElement;

            if (this.highContrast) {
                html.classList.add('high-contrast');

                // Set CSS variables untuk high contrast
                html.style.setProperty('--color-text-primary', '#ffffff');
                html.style.setProperty('--color-text-secondary', '#e0e0e0');
                html.style.setProperty('--color-background', '#000000');
                html.style.setProperty('--color-surface', '#1a1a1a');
                html.style.setProperty('--color-border', '#ffffff');
                html.style.setProperty('--color-primary', '#ffff00');
                html.style.setProperty('--color-primary-500', '#ffff00');
            } else {
                html.classList.remove('high-contrast');

                // Reset CSS variables
                html.style.removeProperty('--color-text-primary');
                html.style.removeProperty('--color-text-secondary');
                html.style.removeProperty('--color-background');
                html.style.removeProperty('--color-surface');
                html.style.removeProperty('--color-border');
                html.style.removeProperty('--color-primary');
                html.style.removeProperty('--color-primary-500');
            }

            // Dispatch event
            window.dispatchEvent(new CustomEvent('contrast-changed', {
                detail: { highContrast: this.highContrast }
            }));
        },

        // Reduced Motion Methods
        toggleReducedMotion() {
            this.reducedMotion = !this.reducedMotion;
            this.applyReducedMotion();
            this.savePreferences();
        },

        setReducedMotion(enabled) {
            this.reducedMotion = enabled;
            this.applyReducedMotion();
            this.savePreferences();
        },

        applyReducedMotion() {
            const html = document.documentElement;

            if (this.reducedMotion) {
                html.classList.add('reduced-motion');

                // Disable semua animations
                const style = document.getElementById('reduced-motion-style') ||
                             document.createElement('style');
                style.id = 'reduced-motion-style';
                style.textContent = `
                    .reduced-motion *,
                    .reduced-motion *::before,
                    .reduced-motion *::after {
                        animation-duration: 0.01ms !important;
                        animation-iteration-count: 1 !important;
                        transition-duration: 0.01ms !important;
                        scroll-behavior: auto !important;
                    }
                `;

                if (!document.getElementById('reduced-motion-style')) {
                    document.head.appendChild(style);
                }
            } else {
                html.classList.remove('reduced-motion');

                // Remove style tag
                const style = document.getElementById('reduced-motion-style');
                if (style) {
                    style.remove();
                }
            }

            // Dispatch event
            window.dispatchEvent(new CustomEvent('motion-changed', {
                detail: { reducedMotion: this.reducedMotion }
            }));
        },

        // Reset semua settings
        resetAll() {
            this.resetFontSize();
            this.setHighContrast(false);
            this.setReducedMotion(false);
        },

        // Save preferences
        savePreferences() {
            localStorage.setItem('roxwood-font-size', this.fontSize);
            localStorage.setItem('roxwood-font-scale', this.customScale.toString());
            localStorage.setItem('roxwood-high-contrast', this.highContrast.toString());
            localStorage.setItem('roxwood-reduced-motion', this.reducedMotion.toString());
        },

        // Get current state sebagai object
        getState() {
            return {
                fontSize: this.fontSize,
                customScale: this.customScale,
                highContrast: this.highContrast,
                reducedMotion: this.reducedMotion
            };
        }
    };
}

// Register sebagai Alpine global
document.addEventListener('alpine:init', () => {
    Alpine.data('accessibilityController', accessibilityController);
});
