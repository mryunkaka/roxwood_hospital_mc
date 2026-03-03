/**
 * Auto-generated from Blade inline <script> blocks.
 * Keep page logic reusable & consistent across pages.
 */

if (!window.
__rhmc_konsumen_loaded
) {
  window.
__rhmc_konsumen_loaded
 = true;

window.konsumenTable = function konsumenTable(config) {
        const toInt = (v) => {
            const n = Number.parseInt(v ?? 0, 10);
            return Number.isFinite(n) ? n : 0;
        };
        const normalize = (v) => String(v ?? '').toLowerCase().replace(/\s+/g, ' ').trim();
        const csrf = () => document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const identityUrl = (id) => String(config?.identityUrlTemplate || '').replace(/\/0$/, '/' + String(id));

        return {
            locale: config?.locale || 'id',
            rows: (Array.isArray(config?.rows) ? config.rows : [])
                .map((r) => ({
                    ...r,
                    id: toInt(r?.id),
                    identityId: toInt(r?.identityId),
                    bandage: toInt(r?.bandage),
                    ifaks: toInt(r?.ifaks),
                    painkiller: toInt(r?.painkiller),
                    items: toInt(r?.items),
                    price: toInt(r?.price),
                }))
                .sort((a, b) => (toInt(b?.createdAtTs) - toInt(a?.createdAtTs))),

            search: '',
            pageSize: 25,
            page: 1,

            identityOpen: false,
            identityLoading: false,
            identityError: '',
            identity: null,

            t(key, fallback) {
                const table = window.globalLangState?.translations || {};
                return table?.[key] || fallback || '';
            },

            init() {
                const sync = (lang) => {
                    this.locale = String(lang || this.locale || 'id');
                };
                sync(window.globalLangState?.currentLang || this.locale);
                window.addEventListener('language-changed', (e) => sync(e?.detail?.lang));
            },

            formatMoney(amount) {
                try {
                    const n = Number(amount || 0);
                    return '$ ' + new Intl.NumberFormat(this.locale === 'id' ? 'id-ID' : 'en-US', { maximumFractionDigits: 0 }).format(n);
                } catch (e) {
                    return '$ ' + String(amount || 0);
                }
            },

            get filteredRows() {
                const q = normalize(this.search);
                if (!q) return this.rows;
                return this.rows.filter((r) => {
                    const hay = normalize([r.timeText, r.citizenId, r.consumer, r.medic, r.jabatan].join(' '));
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
                return this.pageRows.reduce((acc, r) => {
                    acc.bandage += toInt(r.bandage);
                    acc.ifaks += toInt(r.ifaks);
                    acc.painkiller += toInt(r.painkiller);
                    acc.items += toInt(r.items);
                    acc.price += toInt(r.price);
                    return acc;
                }, { bandage: 0, ifaks: 0, painkiller: 0, items: 0, price: 0 });
            },

            exportTimestamp() {
                const d = new Date();
                const pad = (n) => String(n).padStart(2, '0');
                return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}_${pad(d.getHours())}-${pad(d.getMinutes())}`;
            },

            sanitizeFilePart(s) {
                return String(s || '').replace(/[^a-z0-9\-_]+/gi, '_').replace(/^_+|_+$/g, '').slice(0, 48);
            },

            exportTxt(onlyPage = false) {
                const list = onlyPage ? this.pageRows : this.filteredRows;
                const cols = [
                    { key: 'timeText', label: 'DATE' },
                    { key: 'citizenId', label: 'CITIZEN_ID' },
                    { key: 'consumer', label: 'CONSUMER' },
                    { key: 'medic', label: 'MEDIC' },
                    { key: 'jabatan', label: 'POSITION' },
                    { key: 'bandage', label: 'BANDAGE', align: 'right' },
                    { key: 'ifaks', label: 'IFAKS', align: 'right' },
                    { key: 'painkiller', label: 'OBAT', align: 'right' },
                    { key: 'items', label: 'ITEMS', align: 'right' },
                    { key: 'price', label: 'PRICE', align: 'right', map: (r) => this.formatMoney(r.price) },
                ];

                const cellText = (col, row) => {
                    if (col.map) return String(col.map(row) ?? '');
                    return String(row?.[col.key] ?? '');
                };

                const widths = cols.map((c) => {
                    const base = c.label.length;
                    const maxRow = list.reduce((m, r) => Math.max(m, cellText(c, r).length), 0);
                    return Math.max(base, maxRow);
                });

                const pad = (text, width, align) => {
                    const s = String(text ?? '');
                    if (align === 'right') return s.padStart(width, ' ');
                    return s.padEnd(width, ' ');
                };

                const gap = '  ';
                const lines = [];
                lines.push(cols.map((c, i) => pad(c.label, widths[i], 'left')).join(gap));
                list.forEach((r) => {
                    lines.push(cols.map((c, i) => pad(cellText(c, r), widths[i], c.align)).join(gap));
                });

                const blob = new Blob([lines.join('\n')], { type: 'text/plain;charset=utf-8' });
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                const stamp = this.exportTimestamp();
                const suffix = onlyPage ? '_Page' : '';
                a.download = `Konsumen_${stamp}${suffix}.txt`;
                document.body.appendChild(a);
                a.click();
                a.remove();
                URL.revokeObjectURL(url);
            },

            async openIdentity(identityId) {
                const id = toInt(identityId);
                if (!id) return;

                this.identityOpen = true;
                this.identityLoading = true;
                this.identityError = '';
                this.identity = null;

                try {
                    const res = await fetch(identityUrl(id), {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            ...(csrf() ? { 'X-CSRF-TOKEN': csrf() } : {}),
                        },
                    });
                    const json = await res.json().catch(() => null);
                    if (!res.ok || !json) throw new Error('request_failed');
                    this.identity = json;
                } catch (e) {
                    this.identityError = this.t('farmasi_konsumen_identity_failed', 'Failed to load data.');
                } finally {
                    this.identityLoading = false;
                }
            },
        };
    }

  window.konsumenSearch = function konsumenSearch(config) {
    const toInt = (v) => {
      const n = Number.parseInt(v ?? 0, 10);
      return Number.isFinite(n) ? n : 0;
    };

    const consumerUrl = String(config?.consumerUrl || "");
    const medicUrl = String(config?.medicUrl || "");

    const t = (key, fallback) => {
      const table = window.globalLangState?.translations || {};
      return table?.[key] || fallback || "";
    };

    const norm = (v) => String(v ?? "").trim();

    return {
      consumerUrl,
      medicUrl,

      consumerQuery: norm(config?.old?.consumer || ""),
      medicQuery: norm(config?.old?.medic || ""),

      consumerResults: [],
      medicResults: [],

      consumerOpen: false,
      medicOpen: false,

      consumerLoading: false,
      medicLoading: false,

      _consumerAbort: null,
      _medicAbort: null,

      t,

      init() {
        const sync = () => {
          /* no-op: kept for future language-specific formatting */
        };
        sync();
        window.addEventListener("language-changed", () => sync());
      },

      async searchConsumer() {
        const q = norm(this.consumerQuery);
        if (q.length < 2 || !this.consumerUrl) {
          this.consumerResults = [];
          this.consumerOpen = false;
          this.consumerLoading = false;
          return;
        }

        if (this._consumerAbort) this._consumerAbort.abort();
        this._consumerAbort = new AbortController();

        this.consumerLoading = true;
        this.consumerOpen = true;

        try {
          const res = await fetch(this.consumerUrl + "?q=" + encodeURIComponent(q), {
            method: "GET",
            headers: { Accept: "application/json", "X-Requested-With": "XMLHttpRequest" },
            cache: "no-store",
            signal: this._consumerAbort.signal,
          });
          const json = await res.json().catch(() => null);
          const list = Array.isArray(json?.results) ? json.results : [];
          this.consumerResults = list
            .map((r) => ({
              name: String(r?.name || ""),
              total_transactions: toInt(r?.total_transactions),
            }))
            .filter((r) => r.name);
        } catch {
          this.consumerResults = [];
        } finally {
          this.consumerLoading = false;
        }
      },

      selectConsumer(name) {
        this.consumerQuery = String(name || "");
        this.consumerOpen = false;
      },

      async searchMedic() {
        const q = norm(this.medicQuery);
        if (q.length < 2 || !this.medicUrl) {
          this.medicResults = [];
          this.medicOpen = false;
          this.medicLoading = false;
          return;
        }

        if (this._medicAbort) this._medicAbort.abort();
        this._medicAbort = new AbortController();

        this.medicLoading = true;
        this.medicOpen = true;

        try {
          const res = await fetch(this.medicUrl + "?q=" + encodeURIComponent(q), {
            method: "GET",
            headers: { Accept: "application/json", "X-Requested-With": "XMLHttpRequest" },
            cache: "no-store",
            signal: this._medicAbort.signal,
          });
          const json = await res.json().catch(() => null);
          const list = Array.isArray(json?.results) ? json.results : [];
          this.medicResults = list
            .map((u) => ({
              id: toInt(u?.id),
              full_name: String(u?.full_name || ""),
              position: String(u?.position || ""),
            }))
            .filter((u) => u.id > 0 && u.full_name);
        } catch {
          this.medicResults = [];
        } finally {
          this.medicLoading = false;
        }
      },

      selectMedic(name) {
        this.medicQuery = String(name || "");
        this.medicOpen = false;
      },
    };
  };

}

