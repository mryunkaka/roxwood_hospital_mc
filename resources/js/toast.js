/**
 * ============================================
 * ROXWOOD HEALTH MEDICAL CENTER
 * Toast Controller - Alpine.js Component
 * ============================================
 *
 * Sistem notifikasi toast yang dapat dipanggil dari mana saja
 * Penggunaan: window.$toast.show('message', 'type')
 */

// Daftar untuk menyimpan interval agar bisa dibersihkan
const toastIntervals = new Map();

// Register Alpine store untuk toast state (GLOBAL & REAKTIF)
document.addEventListener('alpine:init', () => {
    console.log('Alpine:init fired - registering toast store');

    // Buat store global yang reaktif
    Alpine.store('toast', {
        toasts: [],
        position: 'top-right',
        maxToasts: 5,
        toastIdCounter: 0,

        init() {
            // Load position dari localStorage
            const savedPosition = localStorage.getItem('roxwood-toast-position');
            if (savedPosition) {
                this.position = savedPosition;
            }
        },

        add(message, type = 'info', options = {}) {
            console.log('Menambahkan toast:', message, type);

            const id = ++this.toastIdCounter;
            const toast = {
                id,
                message,
                type,
                title: options.title || null,
                duration: options.duration !== undefined ? options.duration : 5000,
                persistent: options.persistent || false,
                icon: options.icon !== undefined ? options.icon : true,
                actions: options.actions || null,
                progress: 100,
                isPaused: false
            };

            // Tambah ke array (Alpine akan otomatis re-render karena ini store)
            this.toasts.push(toast);

            // Auto dismiss jika tidak persistent
            if (!toast.persistent) {
                this.startProgress(toast);
            }

            return id;
        },

        remove(id) {
            console.log('Menghapus toast:', id);
            const index = this.toasts.findIndex(t => t.id === id);
            if (index > -1) {
                // Bersihkan interval
                if (toastIntervals.has(id)) {
                    clearInterval(toastIntervals.get(id));
                    toastIntervals.delete(id);
                }
                this.toasts.splice(index, 1);
            }
        },

        startProgress(toast) {
            const step = 50;
            const decrement = (100 / (toast.duration / step));

            const interval = setInterval(() => {
                if (toast.isPaused) return;

                toast.progress -= decrement;

                if (toast.progress <= 0) {
                    clearInterval(interval);
                    toastIntervals.delete(toast.id);
                    this.remove(toast.id);
                }
            }, step);

            toastIntervals.set(toast.id, interval);
        },

        pause(id) {
            const toast = this.toasts.find(t => t.id === id);
            if (toast) {
                toast.isPaused = true;
            }
        },

        resume(id) {
            const toast = this.toasts.find(t => t.id === id);
            if (toast) {
                toast.isPaused = false;
            }
        },

        clear() {
            console.log('Membersihkan semua toasts');
            // Bersihkan semua interval
            toastIntervals.forEach(interval => clearInterval(interval));
            toastIntervals.clear();
            this.toasts = [];
        },

        setPosition(pos) {
            this.position = pos;
            localStorage.setItem('roxwood-toast-position', pos);
        }
    });

    // Register $toast sebagai magic helper
    Alpine.magic('toast', () => {
        const store = Alpine.store('toast');
        return {
            show: (message, type = 'info', options = {}) => store.add(message, type, options),
            success: (message, options = {}) => store.add(message, 'success', options),
            error: (message, options = {}) => store.add(message, 'danger', options),
            warning: (message, options = {}) => store.add(message, 'warning', options),
            info: (message, options = {}) => store.add(message, 'info', options),
            clear: () => store.clear(),
            pause: (id) => store.pause(id),
            resume: (id) => store.resume(id),
            get position() {
                return store.position;
            },
            set position(value) {
                store.setPosition(value);
            }
        };
    });

    console.log('Toast store dan magic helper terdaftar');
});

// Global API untuk vanilla JS
window.$toast = {
    show: (message, type = 'info', options = {}) => {
        const store = Alpine.store('toast');
        return store.add(message, type, options);
    },
    success: (message, options = {}) => {
        const store = Alpine.store('toast');
        return store.add(message, 'success', options);
    },
    error: (message, options = {}) => {
        const store = Alpine.store('toast');
        return store.add(message, 'danger', options);
    },
    warning: (message, options = {}) => {
        const store = Alpine.store('toast');
        return store.add(message, 'warning', options);
    },
    info: (message, options = {}) => {
        const store = Alpine.store('toast');
        return store.add(message, 'info', options);
    },
    clear: () => {
        const store = Alpine.store('toast');
        return store.clear();
    },
    get position() {
        return Alpine.store('toast').position;
    },
    set position(value) {
        Alpine.store('toast').setPosition(value);
    }
};

console.log('window.$toast global API terdaftar');
