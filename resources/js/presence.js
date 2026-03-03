/**
 * Presence heartbeat (server-time) to support "auto offline on close/logout".
 *
 * - Ping every 20s while authenticated pages are open.
 * - Send "offline" best-effort on pagehide/beforeunload.
 */

if (!window.__rhmc_presence_loaded) {
  window.__rhmc_presence_loaded = true;

  const csrf = () => document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") || "";

  const postJson = (url, payload, keepalive = false) => {
    const token = csrf();
    if (!token) return Promise.resolve(null);
    return fetch(url, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
        "X-Requested-With": "XMLHttpRequest",
        "X-CSRF-TOKEN": token,
      },
      body: JSON.stringify(payload || {}),
      cache: "no-store",
      keepalive,
    }).catch(() => null);
  };

  const ping = () => postJson("/api/presence/ping", {}, false);
  const offline = (reason) => postJson("/api/presence/offline", { reason: String(reason || "") }, true);

  const boot = () => {
    // Only run on authenticated pages (csrf meta present).
    if (!csrf()) return;
    ping();
    setInterval(ping, 20000);

    // Best-effort: mark offline when tab is closed.
    window.addEventListener("pagehide", () => offline("pagehide"), { capture: true });
    window.addEventListener("beforeunload", () => offline("beforeunload"), { capture: true });
  };

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", boot, { once: true });
  } else {
    boot();
  }
}

