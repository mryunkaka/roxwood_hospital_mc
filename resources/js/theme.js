/**
 * ============================================
 * ROXWOOD HEALTH MEDICAL CENTER
 * Theme Controller - Alpine.js Component
 * ============================================
 *
 * Mengontrol theme switching (Light/Dark/Stylis)
 * Menyimpan preferensi di localStorage
 */

export default function themeController() {
    return {
        theme: 'light',
        isDark: false,
        isStylis: false,
        sidebarOpen: window.innerWidth >= 1024,
        mobileMenuOpen: false,

        init() {
            // Load theme dari localStorage atau deteksi sistem
            const savedTheme = localStorage.getItem('roxwood-theme');
            const systemDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

            if (savedTheme) {
                this.theme = savedTheme;
            } else if (systemDark) {
                this.theme = 'dark';
            }

            this.applyTheme();

            // Deteksi perubahan sistem
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
                if (!localStorage.getItem('roxwood-theme')) {
                    this.theme = e.matches ? 'dark' : 'light';
                    this.applyTheme();
                }
            });

            // Handle window resize untuk sidebar
            window.addEventListener('resize', () => {
                if (window.innerWidth >= 1024) {
                    this.sidebarOpen = true;
                    this.mobileMenuOpen = false;
                } else {
                    this.sidebarOpen = false;
                }
            });

            // Watch perubahan theme
            this.$watch('theme', () => this.applyTheme());
        },

        applyTheme() {
            const html = document.documentElement;

            // Remove semua class theme
            html.classList.remove('theme-light', 'theme-dark', 'theme-stylis');

            // Apply theme
            switch (this.theme) {
                case 'dark':
                    html.classList.add('theme-dark');
                    this.isDark = true;
                    this.isStylis = false;
                    break;
                case 'stylis':
                    html.classList.add('theme-stylis');
                    if (html.classList.contains('theme-dark')) {
                        html.classList.add('theme-dark');
                    }
                    this.isStylis = true;
                    this.isDark = false;
                    break;
                default:
                    html.classList.add('theme-light');
                    this.isDark = false;
                    this.isStylis = false;
            }

            // Save ke localStorage
            localStorage.setItem('roxwood-theme', this.theme);

            // Dispatch event untuk chart updates
            window.dispatchEvent(new CustomEvent('theme-changed', {
                detail: { theme: this.theme }
            }));
        },

        setTheme(themeName) {
            this.theme = themeName;
        },

        toggleTheme() {
            const themes = ['light', 'dark', 'stylis'];
            const currentIndex = themes.indexOf(this.theme);
            this.theme = themes[(currentIndex + 1) % themes.length];
        },

        toggleDarkMode() {
            this.theme = this.isDark ? 'light' : 'dark';
        },

        toggleSidebar() {
            this.sidebarOpen = !this.sidebarOpen;
        },

        closeSidebar() {
            this.sidebarOpen = false;
        },

        openSidebar() {
            this.sidebarOpen = true;
        },

        toggleMobileMenu() {
            this.mobileMenuOpen = !this.mobileMenuOpen;
        },

        closeMobileMenu() {
            this.mobileMenuOpen = false;
        },

        // Language switching function - delegates to global function
        async switchLanguage(code) {
            if (typeof window.switchLanguage === 'function') {
                return await window.switchLanguage(code);
            }
            console.error('switchLanguage function not available');
            return false;
        }
    };
}

// Register sebagai Alpine global jika Alpine sudah loaded
document.addEventListener('alpine:init', () => {
    Alpine.data('themeController', themeController);
});
