/**
 * ============================================
 * ROXWOOD HEALTH MEDICAL CENTER
 * Language Controller - Alpine.js Component
 * ============================================
 *
 * Mengontrol multi-language switching (English/Indonesia)
 * Tanpa refresh halaman - menggunakan AJAX
 */

// Global state untuk language (singleton)
const globalLangState = {
    currentLang: 'id',
    translations: {},
    isLoading: false,
    availableLangs: [
        { code: 'en', name: 'English', flag: '🇺🇸' },
        { code: 'id', name: 'Bahasa Indonesia', flag: '🇮🇩' }
    ],

    // Update semua elemen di seluruh halaman
    updateAll() {
        const translations = this.translations;

        // Update semua elemen dengan atribut data-translate
        document.querySelectorAll('[data-translate]').forEach(el => {
            const key = el.getAttribute('data-translate');
            const text = translations[key];
            if (text) {
                // Check if element has a span.label-text (for input labels with required asterisk)
                const labelSpan = el.querySelector('.label-text');
                if (labelSpan) {
                    labelSpan.textContent = text;
                } else if (el.tagName === 'A' || el.tagName === 'SPAN' || el.tagName === 'P') {
                    // For links, spans, and paragraphs - preserve child elements if any
                    if (el.children.length === 0) {
                        el.textContent = text;
                    }
                } else {
                    el.textContent = text;
                }
            }
        });

        // Update placeholder inputs
        document.querySelectorAll('[data-translate-placeholder]').forEach(el => {
            const key = el.getAttribute('data-translate-placeholder');
            const placeholder = translations[key];
            if (placeholder) {
                el.setAttribute('placeholder', placeholder);
            }
        });

        // Update hint elements
        document.querySelectorAll('[data-translate-hint]').forEach(el => {
            const key = el.getAttribute('data-translate-hint');
            const hint = translations[key];
            if (hint) {
                el.textContent = hint;
            }
        });

        // Update select options by finding parent select with data-translate-select
        document.querySelectorAll('[data-translate-select]').forEach(select => {
            const optionsData = select.getAttribute('data-translate-select');
            if (optionsData) {
                try {
                    const optionsMap = JSON.parse(optionsData);
                    select.querySelectorAll('option').forEach(option => {
                        const value = option.value;
                        // Handle empty option (placeholder) with data-translate-placeholder attribute
                        if (value === '' && option.hasAttribute('data-translate-placeholder')) {
                            const placeholderKey = option.getAttribute('data-translate-placeholder');
                            if (translations[placeholderKey]) {
                                option.textContent = translations[placeholderKey];
                            }
                        } else if (optionsMap[value]) {
                            // Use the translation key to get the actual translated text
                            const translationKey = optionsMap[value];
                            if (translations[translationKey]) {
                                option.textContent = translations[translationKey];
                            }
                        }
                    });
                } catch (e) {
                    console.error('Error parsing select options:', e);
                }
            }
        });

        // Update file input upload text
        document.querySelectorAll('[data-translate-upload]').forEach(el => {
            const key = el.getAttribute('data-translate-upload');
            const text = translations[key];
            if (text) {
                el.textContent = text;
            }
        });

        // Update title
        const titleTranslate = document.querySelector('[data-translate-title]');
        if (titleTranslate) {
            const key = titleTranslate.getAttribute('data-translate-title');
            const title = translations[key];
            if (title) {
                document.title = title;
            }
        }

        // Update meta locale
        const metaLocale = document.querySelector('meta[name="locale"]');
        if (metaLocale) {
            metaLocale.setAttribute('content', this.currentLang);
        }

        // Update html lang attribute
        document.documentElement.lang = this.currentLang;
    },

    async loadTranslations(lang) {
        try {
            // Load translations file
            const response = await fetch(`/lang/${lang}/json`);
            if (!response.ok) throw new Error('Failed to load translations');

            this.translations = await response.json();
            this.currentLang = lang;

            // Update semua elemen
            this.updateAll();

            // Simpan ke localStorage
            localStorage.setItem('app_locale', lang);
        } catch (error) {
            console.error('Error loading translations:', error);
        }
    },

    async setLang(code) {
        if (this.isLoading || code === this.currentLang) return false;

        this.isLoading = true;

        try {
            // Simpan ke session menggunakan AJAX
            await fetch('/lang/' + code, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                },
                body: JSON.stringify({ lang: code })
            });

            // Load dan update translations
            await this.loadTranslations(code);

            // Dispatch event untuk komponen lain
            window.dispatchEvent(new CustomEvent('language-changed', {
                detail: { lang: code }
            }));

            return true;
        } catch (error) {
            console.error('Error switching language:', error);
            return false;
        } finally {
            this.isLoading = false;
        }
    },

    getLangInfo() {
        return this.availableLangs.find(l => l.code === this.currentLang) || this.availableLangs[0];
    },

    init() {
        // Prioritas 1: Cek localStorage dulu
        const storedLang = localStorage.getItem('app_locale');
        if (storedLang) {
            this.currentLang = storedLang;
        } else {
            // Prioritas 2: Cek session dari Laravel (meta tag)
            const currentLocale = document.querySelector('meta[name="locale"]')?.content;
            if (currentLocale) {
                this.currentLang = currentLocale;
                // Simpan ke localStorage untuk pertama kalinya
                localStorage.setItem('app_locale', currentLocale);
            }
        }

        // Load translations saat init
        this.loadTranslations(this.currentLang);
    }
};

// Initialize global state when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => globalLangState.init());
} else {
    globalLangState.init();
}

// Expose globalLangState to window for other components to access
window.globalLangState = globalLangState;

// Listen untuk custom event dari komponen lain
window.addEventListener('language-changed', (e) => {
    globalLangState.currentLang = e.detail.lang;
});

export default function langController() {
    return {
        toggleLang: false,
        // Local reactive copy untuk UI update
        _currentLang: globalLangState.currentLang,

        get currentLang() {
            return this._currentLang;
        },

        get translations() {
            return globalLangState.translations;
        },

        get isLoading() {
            return globalLangState.isLoading;
        },

        get availableLangs() {
            return globalLangState.availableLangs;
        },

        init() {
            // Sync dengan global state saat init
            this._currentLang = globalLangState.currentLang;

            // Listen untuk language changed event
            window.addEventListener('language-changed', (e) => {
                this._currentLang = e.detail.lang;
            });
        },

        async setLang(code) {
            const success = await globalLangState.setLang(code);
            if (success) {
                // Update local state immediately untuk UI reactivity
                this._currentLang = code;
                this.toggleLang = false;
            }
        },

        getLangInfo() {
            return this.availableLangs.find(l => l.code === this._currentLang) || this.availableLangs[0];
        }
    };
}

// Register sebagai Alpine global
document.addEventListener('alpine:init', () => {
    Alpine.data('langController', langController);
});

// Global function untuk switch language (bisa dipanggil dari mana saja)
window.switchLanguage = async function(code) {
    if (typeof globalLangState !== 'undefined') {
        return await globalLangState.setLang(code);
    }
    console.error('globalLangState not available');
    return false;
};
