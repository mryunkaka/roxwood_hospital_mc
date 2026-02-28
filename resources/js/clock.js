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
        _sessionChecking: false,
        _lastSessionInvalidAt: 0,

        init() {
            this.updateClock();

            // Polling setiap detik
            setInterval(() => {
                if (this.isPolling) {
                    this.updateClock();
                }
                this.pollSession();
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
        },

        async pollSession() {
            // Poll session hanya jika user sedang login (layout app)
            const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
            if (!csrf) return;

            // Kalau modal session guard sudah terbuka, stop polling (hindari spam request)
            if (window.Alpine?.store?.('sessionGuard')?.open) return;

            if (this._sessionChecking) return;
            this._sessionChecking = true;

            try {
                const res = await fetch('/api/session/valid', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    cache: 'no-store',
                });

                if (res.ok) return;

                // Hanya tangani kasus session invalid (401). Abaikan 5xx/429/403 agar tidak logout karena error sementara.
                if (res.status !== 401) return;

                let payload = null;
                try { payload = await res.json(); } catch { /* ignore */ }

                const now = Date.now();
                if (now - this._lastSessionInvalidAt < 1500) return;
                this._lastSessionInvalidAt = now;

                const reason = payload?.reason || 'superseded';

                // Best effort: hit logout to clear Laravel session (only when session truly invalid).
                await fetch('/logout', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrf,
                    },
                    body: JSON.stringify({ reason }),
                }).catch(() => {});

                // Jika dipaksa logout, set cooldown login ulang 1 menit
                if (reason === 'force_offline') {
                    try {
                        localStorage.setItem('loginCooldownUntil', String(Date.now() + 60 * 1000));
                    } catch { /* ignore */ }
                }

                const store = window.Alpine?.store?.('sessionGuard');
                if (store?.show) {
                    // Hindari modal "forced logout" muncul lagi saat refresh.
                    // Jika sudah pernah ditampilkan di tab ini, langsung arahkan ke login.
                    if (reason === 'force_offline') {
                        try {
                            if (sessionStorage.getItem('forcedLogoutShown') === '1') {
                                window.location.href = '/login';
                                return;
                            }
                            sessionStorage.setItem('forcedLogoutShown', '1');
                        } catch { /* ignore */ }
                    }

                    store.show({
                        reason,
                        forcedByDevice: payload?.forced_by_device || null,
                    });
                } else {
                    window.location.href = '/login';
                }
            } catch (e) {
                // Ignore network errors
            } finally {
                this._sessionChecking = false;
            }
        }
    };
}

// Register sebagai Alpine global
document.addEventListener('alpine:init', () => {
    Alpine.data('clockController', clockController);
});
