{{-- Toast Container Component --}}
{{-- Tempatkan di layout utama untuk menampilkan semua toasts --}}

<div x-data class="toast-container" :class="$store.toast.position">
    {{-- Toast akan dirender di sini secara dinamis --}}
    <template x-for="toast in $store.toast.toasts.slice(0, $store.toast.maxToasts)" :key="toast.id">
        <div
            class="toast-notification"
            :class="'toast-' + toast.type"
            x-data="{
                isHovered: false
            }"
            @mouseenter="isHovered = true; $store.toast.pause(toast.id)"
            @mouseleave="isHovered = false; $store.toast.resume(toast.id)"
            x-show="true"
            x-transition:enter="transition-all duration-300 ease-out"
            x-transition:enter-start="opacity-0 translate-y-2 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition-all duration-200 ease-in"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-2 scale-95"
            x-cloak
        >
            <div class="toast-inner">
                {{-- Icon --}}
                <template x-if="toast.icon !== false">
                    <div class="toast-icon" :class="'toast-icon-' + toast.type">
                        <template x-if="toast.type === 'success'">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </template>
                        <template x-if="toast.type === 'danger'">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </template>
                        <template x-if="toast.type === 'warning'">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </template>
                        <template x-if="toast.type === 'info'">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </template>
                    </div>
                </template>

                {{-- Content --}}
                <div class="toast-content">
                    <template x-if="toast.title">
                        <h4 class="toast-title" x-text="toast.title"></h4>
                    </template>
                    <p class="toast-message" x-text="toast.message"></p>

                    {{-- Actions (optional buttons) --}}
                    <template x-if="toast.actions && toast.actions.length > 0">
                        <div class="toast-actions">
                            <template x-for="action in toast.actions" :key="action.label">
                                <button
                                    @click="action.handler && action.handler(toast.id)"
                                    class="toast-action-btn"
                                    :class="action.variant || 'default'"
                                    x-text="action.label"
                                ></button>
                            </template>
                        </div>
                    </template>
                </div>

                {{-- Close Button --}}
                <button @click="$store.toast.remove(toast.id)" class="toast-close">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Progress Bar (hanya jika tidak persistent) --}}
            <template x-if="!toast.persistent">
                <div class="toast-progress">
                    <div class="toast-progress-bar" :class="'toast-progress-bar-' + toast.type" :style="{ width: toast.progress + '%' }"></div>
                </div>
            </template>
        </div>
    </template>
</div>

@once
<style>
/* Container positioning */
.toast-container {
    position: fixed;
    z-index: 9999;
    pointer-events: none;
}

.toast-container.top-right {
    top: 16px;
    right: 16px;
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 8px;
}

.toast-container.top-center {
    top: 16px;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
}

.toast-container.top-left {
    top: 16px;
    left: 16px;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 8px;
}

.toast-container.bottom-right {
    bottom: 16px;
    right: 16px;
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 8px;
}

.toast-container.bottom-center {
    bottom: 16px;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
}

.toast-container.bottom-left {
    bottom: 16px;
    left: 16px;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 8px;
}

/* Toast Notification */
.toast-notification {
    pointer-events: auto;
    min-width: 320px;
    max-width: 420px;
    background: var(--color-surface);
    border: 1px solid var(--color-border);
    border-radius: 12px;
    box-shadow: var(--shadow-lg);
    overflow: hidden;
}

/* Toast Inner */
.toast-inner {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 16px;
}

/* Icon */
.toast-icon {
    flex-shrink: 0;
    width: 36px;
    height: 36px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.toast-icon-success {
    background: linear-gradient(135deg, #dcfce7, #bbf7d0);
    color: #15803d;
}

.toast-icon-danger {
    background: linear-gradient(135deg, #fee2e2, #fecaca);
    color: #b91c1c;
}

.toast-icon-warning {
    background: linear-gradient(135deg, #fef3c7, #fde68a);
    color: #b45309;
}

.toast-icon-info {
    background: linear-gradient(135deg, #e0f2fe, #bae6fd);
    color: #0369a1;
}

/* Dark theme icons */
.theme-dark .toast-icon-success {
    background: linear-gradient(135deg, rgba(34, 197, 94, 0.25), rgba(34, 197, 94, 0.15));
    color: #86efac;
}

.theme-dark .toast-icon-danger {
    background: linear-gradient(135deg, rgba(239, 68, 68, 0.25), rgba(239, 68, 68, 0.15));
    color: #fca5a5;
}

.theme-dark .toast-icon-warning {
    background: linear-gradient(135deg, rgba(245, 158, 11, 0.25), rgba(245, 158, 11, 0.15));
    color: #fcd34d;
}

.theme-dark .toast-icon-info {
    background: linear-gradient(135deg, rgba(14, 165, 233, 0.25), rgba(14, 165, 233, 0.15));
    color: #7dd3fc;
}

/* Stylis theme icons */
.theme-stylis .toast-icon-success {
    background: linear-gradient(135deg, rgba(220, 252, 231, 0.9), rgba(167, 243, 208, 0.7));
    color: #047857;
}

.theme-stylis .toast-icon-danger {
    background: linear-gradient(135deg, rgba(254, 226, 226, 0.9), rgba(254, 202, 202, 0.7));
    color: #b91c1c;
}

.theme-stylis .toast-icon-warning {
    background: linear-gradient(135deg, rgba(254, 243, 199, 0.9), rgba(254, 240, 138, 0.7));
    color: #b45309;
}

.theme-stylis .toast-icon-info {
    background: linear-gradient(135deg, rgba(224, 242, 254, 0.9), rgba(186, 230, 254, 0.7));
    color: #0284c7;
}

/* Stylis Dark theme icons */
.theme-stylis.theme-dark .toast-icon-success {
    background: linear-gradient(135deg, rgba(34, 197, 94, 0.2), rgba(34, 197, 94, 0.1));
    color: #6ee7b7;
}

.theme-stylis.theme-dark .toast-icon-danger {
    background: linear-gradient(135deg, rgba(239, 68, 68, 0.2), rgba(239, 68, 68, 0.1));
    color: #fda4af;
}

.theme-stylis.theme-dark .toast-icon-warning {
    background: linear-gradient(135deg, rgba(245, 158, 11, 0.2), rgba(245, 158, 11, 0.1));
    color: #fbbf24;
}

.theme-stylis.theme-dark .toast-icon-info {
    background: linear-gradient(135deg, rgba(14, 165, 233, 0.2), rgba(14, 165, 233, 0.1));
    color: #7dd3fc;
}

/* Content */
.toast-content {
    flex: 1;
    min-width: 0;
}

.toast-title {
    font-weight: 600;
    font-size: 14px;
    color: var(--color-text-primary);
    margin: 0 0 4px 0;
}

.toast-message {
    font-size: 13px;
    color: var(--color-text-secondary);
    margin: 0;
    line-height: 1.4;
}

/* Actions */
.toast-actions {
    display: flex;
    gap: 8px;
    margin-top: 12px;
}

.toast-action-btn {
    padding: 6px 12px;
    font-size: 12px;
    font-weight: 500;
    border-radius: 8px;
    border: 1px solid var(--color-border);
    background: var(--color-surface-alt);
    color: var(--color-text-primary);
    cursor: pointer;
    transition: all 0.15s;
}

.toast-action-btn:hover {
    background: var(--color-surface-hover);
}

.toast-action-btn.primary {
    background: var(--color-primary);
    color: white;
    border-color: var(--color-primary);
}

.toast-action-btn.primary:hover {
    opacity: 0.9;
}

/* Close Button */
.toast-close {
    flex-shrink: 0;
    width: 28px;
    height: 28px;
    border-radius: 8px;
    border: none;
    background: transparent;
    color: var(--color-text-tertiary);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.15s;
}

.toast-close:hover {
    background: var(--color-surface-alt);
    color: var(--color-text-primary);
}

/* Progress Bar */
.toast-progress {
    height: 3px;
    background: var(--color-border-light);
    overflow: hidden;
}

.toast-progress-bar {
    height: 100%;
    transition: width 0.05s linear;
}

.toast-progress-bar-success {
    background: linear-gradient(90deg, #22c55e, #16a34a);
}

.toast-progress-bar-danger {
    background: linear-gradient(90deg, #ef4444, #dc2626);
}

.toast-progress-bar-warning {
    background: linear-gradient(90deg, #f59e0b, #d97706);
}

.toast-progress-bar-info {
    background: linear-gradient(90deg, #0ea5e9, #0284c7);
}

/* Stylis theme progress bars */
.theme-stylis .toast-progress-bar-success {
    background: linear-gradient(90deg, #14b8a6, #0d9488);
}

.theme-stylis .toast-progress-bar-danger {
    background: linear-gradient(90deg, #f43f5e, #e11d48);
}

.theme-stylis .toast-progress-bar-warning {
    background: linear-gradient(90deg, #f59e0b, #d97706);
}

.theme-stylis .toast-progress-bar-info {
    background: linear-gradient(90deg, #06b6d4, #0891b2);
}

/* Position specific adjustments */
@media (max-width: 640px) {
    .toast-notification {
        min-width: calc(100vw - 32px);
        max-width: calc(100vw - 32px);
    }
}
</style>
@endonce
