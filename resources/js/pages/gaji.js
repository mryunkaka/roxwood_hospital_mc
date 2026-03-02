/**
 * Farmasi - Gaji page controllers (Alpine x-data factories)
 */

if (!window.__rhmc_gaji_loaded) {
  window.__rhmc_gaji_loaded = true;

  const toInt = (v) => {
    const n = Number.parseInt(v ?? 0, 10);
    return Number.isFinite(n) ? n : 0;
  };

  const normalize = (v) => String(v ?? "").toLowerCase().replace(/\s+/g, " ").trim();

  const csrf = () => document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") || "";

  window.gajiTable = function gajiTable(config) {
    return {
      locale: config?.locale || "id",
      rows: (Array.isArray(config?.rows) ? config.rows : [])
        .map((r) => ({
          id: toInt(r?.id),
          medic: String(r?.medic || ""),
          jabatan: String(r?.jabatan || ""),
          period: String(r?.period || ""),
          periodEndTs: toInt(r?.periodEndTs),
          bonus: toInt(r?.bonus),
          bonusText: String(r?.bonusText || ""),
          status: String(r?.status || "pending"),
          paidBy: String(r?.paidBy || "-"),
          paidAtText: String(r?.paidAtText || ""),
        }))
        .sort((a, b) => (b.periodEndTs || 0) - (a.periodEndTs || 0)),

      isStaff: Boolean(config?.isStaff),
      canPay: Boolean(config?.canPay),
      canGenerateManual: Boolean(config?.canGenerateManual),
      payUrl: String(config?.payUrl || ""),
      userSearchUrl: String(config?.userSearchUrl || ""),
      generateUrl: String(config?.generateUrl || ""),

      search: "",
      pageSize: 25,
      page: 1,

      payOpen: false,
      payTarget: null,
      payMethod: "direct",
      paySubmitting: false,

      titipQuery: "",
      titipResults: [],
      titipOpen: false,
      titipLoading: false,
      titipSelected: null,
      _titipAbort: null,

      t(key, fallback) {
        const table = window.globalLangState?.translations || {};
        return table?.[key] || fallback || "";
      },

      init() {
        const sync = (lang) => {
          this.locale = String(lang || this.locale || "id");
        };
        sync(window.globalLangState?.currentLang || this.locale);
        window.addEventListener("language-changed", (e) => sync(e?.detail?.lang));

        document.addEventListener("click", (e) => {
          const within = e.target?.closest?.("[data-titip-dropdown]");
          const withinInput = e.target?.closest?.("[data-titip-input]");
          if (!within && !withinInput) {
            this.titipOpen = false;
          }
        });
      },

      formatMoney(amount) {
        try {
          const n = Number(amount || 0);
          return "$ " + new Intl.NumberFormat(this.locale === "id" ? "id-ID" : "en-US", { maximumFractionDigits: 0 }).format(n);
        } catch {
          return "$ " + String(amount || 0);
        }
      },

      get filteredRows() {
        const q = normalize(this.search);
        if (!q) return this.rows;
        return this.rows.filter((r) => {
          const hay = normalize([r.medic, r.jabatan, r.period, r.bonusText, r.status, r.paidBy].join(" "));
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

      get pageTotals() {
        return this.pageRows.reduce(
          (acc, r) => {
            acc.bonus += toInt(r.bonus);
            return acc;
          },
          { bonus: 0 }
        );
      },

      exportTimestamp() {
        const d = new Date();
        const pad = (n) => String(n).padStart(2, "0");
        return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}_${pad(d.getHours())}-${pad(d.getMinutes())}`;
      },

      exportTxt(onlyPage = true) {
        const list = onlyPage ? this.pageRows : this.filteredRows;
        const cols = [
          { key: "medic", label: "NAME" },
          { key: "jabatan", label: "POSITION" },
          { key: "period", label: "PERIOD" },
          { key: "bonus", label: "BONUS", align: "right", map: (r) => this.formatMoney(r.bonus) },
          { key: "status", label: "STATUS" },
          { key: "paidBy", label: "PAID_BY" },
          { key: "paidAtText", label: "PAID_AT" },
        ];

        const cellText = (col, row) => {
          if (col.map) return String(col.map(row) ?? "");
          return String(row?.[col.key] ?? "");
        };

        const widths = cols.map((c) => {
          const base = c.label.length;
          const maxRow = list.reduce((m, r) => Math.max(m, cellText(c, r).length), 0);
          return Math.max(base, maxRow);
        });

        const pad = (text, width, align) => {
          const s = String(text ?? "");
          if (align === "right") return s.padStart(width, " ");
          return s.padEnd(width, " ");
        };

        const gap = "  ";
        const lines = [];
        lines.push(cols.map((c, i) => pad(c.label, widths[i], "left")).join(gap));
        list.forEach((r) => {
          lines.push(cols.map((c, i) => pad(cellText(c, r), widths[i], c.align)).join(gap));
        });

        const blob = new Blob([lines.join("\n")], { type: "text/plain;charset=utf-8" });
        const url = URL.createObjectURL(blob);
        const a = document.createElement("a");
        a.href = url;
        const stamp = this.exportTimestamp();
        const suffix = onlyPage ? "_Page" : "";
        a.download = `Gaji_${stamp}${suffix}.txt`;
        document.body.appendChild(a);
        a.click();
        a.remove();
        URL.revokeObjectURL(url);
      },

      openPay(row) {
        this.payTarget = row;
        this.payMethod = "direct";
        this.paySubmitting = false;
        this.titipQuery = "";
        this.titipResults = [];
        this.titipOpen = false;
        this.titipLoading = false;
        this.titipSelected = null;
        this.payOpen = true;
      },

      async searchTitip() {
        if (this.payMethod !== "titip") return;
        const q = (this.titipQuery || "").trim();
        if (q.length < 2) {
          this.titipResults = [];
          this.titipOpen = false;
          this.titipSelected = null;
          return;
        }

        if (!this.userSearchUrl) return;
        if (this._titipAbort) this._titipAbort.abort();
        this._titipAbort = new AbortController();

        this.titipLoading = true;
        this.titipOpen = true;

        try {
          const res = await fetch(this.userSearchUrl + "?q=" + encodeURIComponent(q), {
            method: "GET",
            headers: { Accept: "application/json", "X-Requested-With": "XMLHttpRequest" },
            cache: "no-store",
            signal: this._titipAbort.signal,
          });
          const json = await res.json().catch(() => null);
          const list = Array.isArray(json?.results) ? json.results : [];
          this.titipResults = list.map((u) => ({
            id: toInt(u?.id),
            full_name: String(u?.full_name || ""),
            position: String(u?.position || ""),
          })).filter((u) => u.id > 0 && u.full_name);
        } catch {
          this.titipResults = [];
        } finally {
          this.titipLoading = false;
        }
      },

      selectTitip(u) {
        this.titipSelected = u;
        this.titipQuery = String(u?.full_name || "");
        this.titipOpen = false;
      },

      async submitPay() {
        if (!this.payUrl || !this.payTarget?.id) return;
        if (this.paySubmitting) return;

        if (this.payMethod === "titip" && !(this.titipSelected?.id > 0)) {
          window.$toast?.error?.(this.t("salary_titip_required", "Please select a user."));
          return;
        }

        this.paySubmitting = true;

        try {
          const payload = {
            salary_id: toInt(this.payTarget.id),
            pay_method: this.payMethod,
            titip_to: this.payMethod === "titip" ? toInt(this.titipSelected?.id) : null,
          };

          const res = await fetch(this.payUrl, {
            method: "POST",
            headers: {
              "Content-Type": "application/json",
              Accept: "application/json",
              "X-Requested-With": "XMLHttpRequest",
              ...(csrf() ? { "X-CSRF-TOKEN": csrf() } : {}),
            },
            body: JSON.stringify(payload),
          });

          const json = await res.json().catch(() => null);
          if (!res.ok || !json?.success) {
            window.$toast?.error?.(json?.message || this.t("error", "Error"));
            return;
          }

          const updated = json?.row || {};
          const id = toInt(updated?.id);
          if (id) {
            this.rows = (this.rows || []).map((r) => {
              if (toInt(r.id) !== id) return r;
              return {
                ...r,
                status: "paid",
                paidBy: String(updated?.paid_by || r.paidBy || "-"),
                paidAtText: String(updated?.paid_at_text || r.paidAtText || ""),
              };
            });
          }

          window.$toast?.success?.(json?.message || this.t("saved", "Saved"));
          this.payOpen = false;
        } catch {
          window.$toast?.error?.(this.t("error", "Error"));
        } finally {
          this.paySubmitting = false;
        }
      },

      async generateManual() {
        if (!this.generateUrl) return;
        const ok = confirm(this.t("salary_generate_confirm", "Generate salary now?"));
        if (!ok) return;

        try {
          const res = await fetch(this.generateUrl, {
            method: "POST",
            headers: {
              Accept: "text/html",
              "X-Requested-With": "XMLHttpRequest",
              ...(csrf() ? { "X-CSRF-TOKEN": csrf() } : {}),
            },
          });
          // Redirect response -> reload page
          if (res.redirected) {
            window.location.href = res.url;
            return;
          }
          window.location.reload();
        } catch {
          window.$toast?.error?.(this.t("error", "Error"));
        }
      },
    };
  };
}

