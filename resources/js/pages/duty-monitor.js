/**
 * Duty Monitor page controllers (Alpine x-data factory)
 */

if (!window.__rhmc_duty_monitor_loaded) {
  window.__rhmc_duty_monitor_loaded = true;

  const toInt = (v) => {
    const n = Number.parseInt(v ?? 0, 10);
    return Number.isFinite(n) ? n : 0;
  };

  const normalize = (v) => String(v ?? "").toLowerCase().replace(/\s+/g, " ").trim();

  const fmtDuration = (seconds) => {
    const s = Math.max(0, toInt(seconds));
    const minutes = Math.floor(s / 60);
    const hours = Math.floor(minutes / 60);
    const mins = minutes % 60;
    if (hours > 0) return `${hours}h ${mins}m`;
    return `${minutes}m`;
  };

  const fmtClock = (seconds) => {
    const s = Math.max(0, toInt(seconds));
    const h = Math.floor(s / 3600);
    const m = Math.floor((s % 3600) / 60);
    const sec = s % 60;
    const pad2 = (n) => String(n).padStart(2, "0");
    return `${pad2(h)}:${pad2(m)}:${pad2(sec)}`;
  };

  window.dutyMonitorTable = function dutyMonitorTable(config) {
    const rows = (Array.isArray(config?.rows) ? config.rows : [])
      .map((r) => ({
        id: toInt(r?.id),
        name: String(r?.name || ""),
        position: String(r?.position || ""),
        status: String(r?.status || "offline"),
        lastActivityText: String(r?.lastActivityText || "-"),
        autoOfflineText: String(r?.autoOfflineText || "-"),
        dutyTotalSeconds: toInt(r?.dutyTotalSeconds),
        dutyFarmasiSeconds: toInt(r?.dutyFarmasiSeconds),
        dutyMedisSeconds: toInt(r?.dutyMedisSeconds),
        trxFarmasi: toInt(r?.trxFarmasi),
        trxMedis: toInt(r?.trxMedis),
        sortTs: toInt(r?.sortTs),
      }))
      .filter((r) => r.id > 0 && r.name)
      .sort((a, b) => {
        const aActive = a.status === "active" ? 0 : 1;
        const bActive = b.status === "active" ? 0 : 1;
        if (aActive !== bActive) return aActive - bActive;
        if ((b.dutyTotalSeconds || 0) !== (a.dutyTotalSeconds || 0)) return (b.dutyTotalSeconds || 0) - (a.dutyTotalSeconds || 0);
        if ((b.sortTs || 0) !== (a.sortTs || 0)) return (b.sortTs || 0) - (a.sortTs || 0);
        return String(a.name).localeCompare(String(b.name));
      });

    return {
      locale: config?.locale || "id",
      rows,
      search: "",
      pageSize: 25,
      page: 1,

      init() {
        const sync = (lang) => {
          this.locale = String(lang || this.locale || "id");
        };
        sync(window.globalLangState?.currentLang || this.locale);
        window.addEventListener("language-changed", (e) => sync(e?.detail?.lang));
      },

      t(key, fallback) {
        const table = window.globalLangState?.translations || {};
        return table?.[key] || fallback || "";
      },

      get filteredRows() {
        const q = normalize(this.search);
        if (!q) return this.rows;
        return this.rows.filter((r) => {
          const hay = normalize([r.name, r.position, r.status].join(" "));
          return hay.includes(q);
        });
      },

      get pageCount() {
        const total = this.filteredRows.length;
        const size = Math.max(1, toInt(this.pageSize));
        return Math.max(1, Math.ceil(total / size));
      },

      get pageRows() {
        const size = Math.max(1, toInt(this.pageSize));
        const p = Math.min(Math.max(1, toInt(this.page)), this.pageCount);
        const start = (p - 1) * size;
        return this.filteredRows.slice(start, start + size);
      },

      dutyText(seconds) {
        return fmtDuration(seconds);
      },
    };
  };

  window.dutyRealtimeCounter = function dutyRealtimeCounter(config) {
    const baseSeconds = toInt(config?.baseSeconds);
    const serverNowMs = toInt(config?.serverNowMs);
    const autoOfflineMs = config?.autoOfflineMs == null ? null : toInt(config?.autoOfflineMs);
    const active = Boolean(config?.active);

    return {
      baseSeconds,
      serverNowMs,
      autoOfflineMs,
      active,
      clientNowMsAtInit: Date.now(),
      offsetMs: 0,
      tickServerNowMs: 0,
      timer: null,

      init() {
        this.clientNowMsAtInit = Date.now();
        this.offsetMs = this.serverNowMs ? (this.serverNowMs - this.clientNowMsAtInit) : 0;
        this.tickServerNowMs = this.clientNowMsAtInit + this.offsetMs;
        this.timer = setInterval(() => {
          this.tickServerNowMs = Date.now() + this.offsetMs;
          // Stop ticking once offline window ends (no need to keep re-rendering).
          if (this.autoOfflineMs && this.tickServerNowMs >= this.autoOfflineMs) {
            clearInterval(this.timer);
            this.timer = null;
          }
        }, 1000);
      },

      get elapsedSeconds() {
        if (!this.active) return 0;
        if (!this.serverNowMs) return 0;
        const delta = Math.floor((this.tickServerNowMs - this.serverNowMs) / 1000);
        if (delta <= 0) return 0;
        if (!this.autoOfflineMs) return delta;
        const maxDelta = Math.floor((this.autoOfflineMs - this.serverNowMs) / 1000);
        return Math.max(0, Math.min(delta, maxDelta));
      },

      get totalSeconds() {
        return this.baseSeconds + this.elapsedSeconds;
      },

      get clockText() {
        return fmtClock(this.totalSeconds);
      },
    };
  };
}
