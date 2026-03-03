@php
    $flashSuccess = session('success');
    $flashError = session('error');
    $flashWarning = session('warning');
    $flashInfo = session('info');

    $validationErrors = $errors?->all() ?? [];
    $firstValidationError = $validationErrors[0] ?? null;

    $toastQueue = [];
    if (is_string($flashError) && trim($flashError) !== '') {
        $toastQueue[] = ['type' => 'error', 'message' => trim($flashError)];
    } elseif (is_string($firstValidationError) && trim($firstValidationError) !== '') {
        $toastQueue[] = ['type' => 'error', 'message' => trim($firstValidationError)];
    } elseif (is_string($flashWarning) && trim($flashWarning) !== '') {
        $toastQueue[] = ['type' => 'warning', 'message' => trim($flashWarning)];
    } elseif (is_string($flashSuccess) && trim($flashSuccess) !== '') {
        $toastQueue[] = ['type' => 'success', 'message' => trim($flashSuccess)];
    } elseif (is_string($flashInfo) && trim($flashInfo) !== '') {
        $toastQueue[] = ['type' => 'info', 'message' => trim($flashInfo)];
    }
@endphp

<script>
    (() => {
        const flashQueue = @js($toastQueue);
        const pendingKey = 'roxwood_pending_toasts';

        const readPending = () => {
            try {
                const raw = window.sessionStorage?.getItem(pendingKey);
                if (!raw) return [];
                window.sessionStorage.removeItem(pendingKey);
                const parsed = JSON.parse(raw);
                return Array.isArray(parsed) ? parsed : [];
            } catch {
                try { window.sessionStorage?.removeItem(pendingKey); } catch {}
                return [];
            }
        };

        const queue = ([]).concat(flashQueue || [], readPending());
        if (!queue.length) return;

        let tries = 0;
        const show = () => {
            tries += 1;
            const api = window.$toast;
            const storeReady = !!(window.Alpine && typeof window.Alpine.store === 'function' && window.Alpine.store('toast'));

            if (!api || !storeReady) {
                if (tries < 40) setTimeout(show, 50);
                return;
            }

            queue.forEach((t) => {
                const msg = String(t?.message || '').trim();
                const type = String(t?.type || 'info');
                if (!msg) return;

                if (type === 'success') api.success(msg);
                else if (type === 'warning') api.warning(msg);
                else if (type === 'error') api.error(msg);
                else api.info(msg);
            });
        };

        // Run after the current tick; toast store is registered on alpine:init.
        setTimeout(show, 0);
    })();
</script>
