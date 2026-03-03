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
        sidebarHidden: false,
        mobileMenuOpen: false,

	        init() {
            const storedSidebarHidden = localStorage.getItem('roxwood-sidebar-hidden');
            if (storedSidebarHidden !== null) {
                this.sidebarHidden = storedSidebarHidden === '1';
            }

            if (this.sidebarHidden) {
                this.sidebarOpen = false;
            }

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
                    this.sidebarOpen = !this.sidebarHidden;
                    this.mobileMenuOpen = false;
                } else {
                    this.sidebarOpen = false;
                }
            });

	            // Watch perubahan theme
	            this.$watch('theme', () => this.applyTheme());

                // Allow other components/pages to request a theme change.
                window.addEventListener('set-theme', (e) => {
                    const next = e?.detail?.theme;
                    if (typeof next === 'string' && next.trim() !== '') {
                        this.setTheme(next.trim());
                    }
                });
	            this.$watch('sidebarHidden', () => {
	                localStorage.setItem('roxwood-sidebar-hidden', this.sidebarHidden ? '1' : '0');
	                if (this.sidebarHidden) {
	                    this.sidebarOpen = false;
	                } else if (window.innerWidth >= 1024) {
                    this.sidebarOpen = true;
                }
            });
        },

	        applyTheme() {
	            switch (this.theme) {
	                case 'dark':
	                    this.isDark = true;
	                    this.isStylis = false;
	                    break;
	                case 'stylis':
	                    this.isStylis = true;
	                    this.isDark = false;
	                    break;
	                default:
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

        hideSidebar() {
            this.sidebarHidden = true;
        },

        showSidebar() {
            this.sidebarHidden = false;
        },

        toggleSidebarHidden() {
            this.sidebarHidden = !this.sidebarHidden;
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
