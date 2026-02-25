/**
 * ============================================
 * ROXWOOD HEALTH MEDICAL CENTER
 * Clock Controller - Alpine.js Component
 * ============================================
 *
 * Live polling clock untuk UI demo purposes
 * Tidak ada backend call, hanya setInterval
 */

export default function clockController() {
    return {
        currentTime: '',
        currentDate: '',
        isPolling: true,

        init() {
            this.updateClock();

            // Polling setiap detik
            setInterval(() => {
                if (this.isPolling) {
                    this.updateClock();
                }
            }, 1000);
        },

        updateClock() {
            const now = new Date();

            // Format time HH:MM:SS - WIB (Asia/Jakarta)
            this.currentTime = now.toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: false,
                timeZone: 'Asia/Jakarta'
            });

            // Format date with day - WIB (Asia/Jakarta)
            this.currentDate = now.toLocaleDateString('id-ID', {
                weekday: 'long',
                day: 'numeric',
                month: 'short',
                year: 'numeric',
                timeZone: 'Asia/Jakarta'
            });
        },

        pausePolling() {
            this.isPolling = false;
        },

        resumePolling() {
            this.isPolling = true;
        }
    };
}

// Register sebagai Alpine global
document.addEventListener('alpine:init', () => {
    Alpine.data('clockController', clockController);
});
