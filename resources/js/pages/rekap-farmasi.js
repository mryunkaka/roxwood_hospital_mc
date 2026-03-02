/**
 * Auto-generated from Blade inline <script> blocks.
 * Keep page logic reusable & consistent across pages.
 */

if (!window.
__rhmc_rekap_farmasi_loaded
) {
  window.
__rhmc_rekap_farmasi_loaded
 = true;

window.rekapFarmasiForm = function rekapFarmasiForm(config) {
        const toInt = (v) => {
            const n = Number.parseInt(v ?? 0, 10);
            return Number.isFinite(n) ? n : 0;
        };

        const STORAGE_KEY = 'rhmc_rekap_farmasi_new_tx';

		        return {
		            consumerName: '',
		            uiLang: (window.globalLangState?.currentLang || config?.locale || 'id'),
		            checkingConsumer: false,
		            consumerLocked: false,
		            canChoosePackage: false,
		            packageType: '',
	            custom: { bandagePackageId: '', ifaksPackageId: '', painkillerPackageId: '' },
	            autoMerge: '0',
	            similarMatches: [],
	            mergeTargets: [],
	            mergeModalOpen: false,
	            mergeSelection: [],
	            pendingSubmit: false,
                submitErrorText: '',
		            lastCheckedName: '',
		            hydrating: false,
	                consumerResults: [],
	                consumerDropdownOpen: false,
	                consumerHighlighted: -1,
                consumerSearching: false,
                consumerSearchTimeout: null,
	            unit: {
	                bandage: toInt(config?.unitPrices?.bandage),
	                ifaks: toInt(config?.unitPrices?.ifaks),
	                painkiller: toInt(config?.unitPrices?.painkiller),
	            },
            pkgA: config?.pkgA || null,
            pkgB: config?.pkgB || null,
            customPackageMap: config?.customPackageMap || {},
            totals: { bandage: 0, ifaks: 0, painkiller: 0, price: 0, bonus: 0 },

	            persist() {
	                const payload = {
	                    consumerName: (this.consumerName || ''),
	                    packageType: (this.packageType || ''),
	                    custom: {
	                        bandagePackageId: (this.custom.bandagePackageId || ''),
	                        ifaksPackageId: (this.custom.ifaksPackageId || ''),
	                        painkillerPackageId: (this.custom.painkillerPackageId || ''),
	                    },
	                };
	                try {
	                    localStorage.setItem(STORAGE_KEY, JSON.stringify(payload));
	                } catch (e) {
	                    // ignore
	                }
	            },

            loadPersisted() {
                try {
                    const raw = localStorage.getItem(STORAGE_KEY);
                    if (!raw) return null;
                    const parsed = JSON.parse(raw);
                    return parsed && typeof parsed === 'object' ? parsed : null;
                } catch (e) {
                    return null;
                }
            },

            clearPersisted() {
                try {
                    localStorage.removeItem(STORAGE_KEY);
                } catch (e) {
                    // ignore
                }
            },

            get mergeTargetsJson() {
                try {
                    return JSON.stringify(this.mergeTargets || []);
                } catch (e) {
                    return '[]';
                }
            },

		            get consumerNameHint() {
		                const len = (this.consumerName || '').trim().length;
		                if (len < 2) return this.msg('fill_consumer') || (this.uiLang === 'id'
		                    ? 'Mohon isi terlebih dahulu nama konsumen.'
		                    : 'Please fill in the consumer name first.');
		                return '';
		            },

	            get canSubmit() {
	                if ((this.consumerName || '').trim().length < 2) return false;
	                if (this.consumerLocked) return false;
	                if (!this.packageType) return false;
	                if (this.packageType === 'paket_custom') {
	                    return !!(this.custom.bandagePackageId || this.custom.ifaksPackageId || this.custom.painkillerPackageId);
	                }
	                if (this.packageType === 'paket_a') return !!this.pkgA;
	                if (this.packageType === 'paket_b') return !!this.pkgB;
	                return true;
	            },

                msg(key) {
                    const lang = this.uiLang || config?.locale || 'id';
                    const table = config?.strings?.[lang] || config?.strings?.[config?.locale] || {};
                    return table?.[key] || '';
                },

	            openMergeModal() {
	                const list = Array.isArray(this.similarMatches) ? this.similarMatches : [];
	                if (list.length === 0) return;

	                this.mergeSelection = list
	                    .filter((m) => m && typeof m.name === 'string' && m.name.trim() !== '')
	                    .map((m) => ({ name: m.name, score: Number.parseInt(m.score ?? 0, 10) || 0 }));

	                this.mergeModalOpen = true;
	                this.pendingSubmit = true;
	            },

	            closeMergeModal() {
	                this.mergeModalOpen = false;
	                this.pendingSubmit = false;
	                this.mergeSelection = [];
	            },

	            removeMergeTarget(name) {
	                const n = String(name || '').trim();
	                if (!n) return;
	                this.mergeSelection = (this.mergeSelection || []).filter((m) => m?.name !== n);
	            },

	            confirmMergeAndSubmit() {
	                const targets = (this.mergeSelection || [])
	                    .map((m) => String(m?.name || '').trim())
	                    .filter((n) => n !== '');

	                this.mergeTargets = targets;
	                this.autoMerge = targets.length > 0 ? '1' : '0';
	                this.mergeModalOpen = false;
	                this.pendingSubmit = false;

	                this.$nextTick(() => {
	                    this.$refs.txForm?.submit();
	                });
	            },

		            submitForm() {
		                this.submitErrorText = '';

                        if (this.checkingConsumer) {
                            this.submitErrorText = this.msg('checking') || '';
                            return;
                        }

                        const nameLen = (this.consumerName || '').trim().length;
                        if (nameLen < 2) {
                            this.submitErrorText = this.msg('fill_consumer') || '';
                            return;
                        }

                        if (this.consumerLocked) {
                            this.submitErrorText = this.msg('already_today') || '';
                            return;
                        }

                        if (!this.packageType) {
                            this.submitErrorText = this.msg('choose_package') || '';
                            return;
                        }

                        if (this.packageType === 'paket_custom') {
                            const hasAny = !!(this.custom.bandagePackageId || this.custom.ifaksPackageId || this.custom.painkillerPackageId);
                            if (!hasAny) {
                                this.submitErrorText = this.msg('choose_custom_item') || '';
                                return;
                            }
                        }

		                this.$nextTick(() => this.$refs.txForm?.submit());
		            },

		            formatNumber(num) {
		                const n = toInt(num);
		                try {
		                    return new Intl.NumberFormat(this.uiLang === 'id' ? 'id-ID' : 'en-US').format(n);
		                } catch (e) {
		                    return String(n);
		                }
		            },

                formatLastPurchase(c) {
                    if (!c) return '';
                    const lang = window.globalLangState?.currentLang || config?.locale || 'id';
                    return lang === 'id' ? (c.last_purchase_id || '') : (c.last_purchase_en || '');
                },

                searchConsumers(query) {
                    const q = String(query || '').trim();
                    if (q.length < 2) {
                        this.consumerResults = [];
                        this.consumerDropdownOpen = false;
                        this.consumerSearching = false;
                        this.consumerHighlighted = -1;
                        if (this.consumerSearchTimeout) clearTimeout(this.consumerSearchTimeout);
                        return;
                    }

                    if (this.consumerSearchTimeout) clearTimeout(this.consumerSearchTimeout);
                    this.consumerDropdownOpen = true;
                    this.consumerSearching = true;

                    this.consumerSearchTimeout = setTimeout(async () => {
                        try {
                            const url = new URL(config.searchUrl, window.location.origin);
                            url.searchParams.set('q', q);
                            const res = await fetch(url.toString(), { headers: { 'Accept': 'application/json' }, cache: 'no-store' });
                            const json = await res.json();
                            this.consumerResults = Array.isArray(json.results) ? json.results : [];
                            this.consumerDropdownOpen = this.consumerResults.length > 0 || this.consumerSearching;
                            this.consumerHighlighted = this.consumerResults.length > 0 ? 0 : -1;
                        } catch (e) {
                            this.consumerResults = [];
                            this.consumerDropdownOpen = false;
                        } finally {
                            this.consumerSearching = false;
                            this.consumerDropdownOpen = this.consumerResults.length > 0;
                        }
                    }, 250);
                },

                selectConsumer(c) {
                    if (!c) return;
                    this.consumerName = c.name;
                    this.consumerDropdownOpen = false;
                    this.consumerResults = [];
                    this.consumerHighlighted = -1;
                    this.persist();
                    this.checkConsumer();
                    this.$nextTick(() => this.$refs.consumerInput?.focus());
                },

                highlightNext() {
                    if (!this.consumerDropdownOpen || this.consumerResults.length === 0) return;
                    this.consumerHighlighted = (this.consumerHighlighted + 1) % this.consumerResults.length;
                },

                highlightPrev() {
                    if (!this.consumerDropdownOpen || this.consumerResults.length === 0) return;
                    this.consumerHighlighted = this.consumerHighlighted <= 0
                        ? this.consumerResults.length - 1
                        : this.consumerHighlighted - 1;
                },

                selectHighlighted() {
                    if (!this.consumerDropdownOpen) return;
                    if (this.consumerHighlighted < 0 || this.consumerHighlighted >= this.consumerResults.length) return;
                    this.selectConsumer(this.consumerResults[this.consumerHighlighted]);
                },

	            resetForm() {
	                this.consumerName = '';
	                this.consumerLocked = false;
	                this.canChoosePackage = false;
	                this.packageType = '';
	                this.custom.bandagePackageId = '';
	                this.custom.ifaksPackageId = '';
	                this.custom.painkillerPackageId = '';
                    this.consumerResults = [];
                    this.consumerDropdownOpen = false;
                    this.consumerHighlighted = -1;
                    this.consumerSearching = false;
	                this.recalc();
	                this.clearPersisted();
	            },

            setPackage(type) {
	                if (!this.canChoosePackage) return;
	                if (this.consumerLocked) return;
	                this.packageType = type;

	                if (type === 'paket_custom') {
	                    this.custom.bandagePackageId = '';
	                    this.custom.ifaksPackageId = '';
	                    this.custom.painkillerPackageId = '';
	                }
	                this.recalc();
	                this.persist();
	            },

            recalc() {
                let bandage = 0;
                let ifaks = 0;
                let painkiller = 0;
                let price = 0;

                if (this.packageType === 'paket_a' && this.pkgA) {
                    bandage = toInt(this.pkgA.bandage);
                    ifaks = toInt(this.pkgA.ifaks);
                    painkiller = toInt(this.pkgA.painkiller);
                    price = toInt(this.pkgA.price);
                } else if (this.packageType === 'paket_b' && this.pkgB) {
                    bandage = toInt(this.pkgB.bandage);
                    ifaks = toInt(this.pkgB.ifaks);
                    painkiller = toInt(this.pkgB.painkiller);
                    price = toInt(this.pkgB.price);
                } else if (this.packageType === 'paket_custom') {
                    const ids = [this.custom.bandagePackageId, this.custom.ifaksPackageId, this.custom.painkillerPackageId]
                        .filter(Boolean);

                    ids.forEach((id) => {
                        const p = this.customPackageMap[id];
                        if (!p) return;
                        price += toInt(p.price);
                        if (p.item === 'bandage') bandage += toInt(p.qty);
                        if (p.item === 'ifaks') ifaks += toInt(p.qty);
                        if (p.item === 'painkiller') painkiller += toInt(p.qty);
                    });
                }

                this.totals.bandage = bandage;
                this.totals.ifaks = ifaks;
                this.totals.painkiller = painkiller;
                this.totals.price = price;
                this.totals.bonus = Math.floor(price * 0.4);
            },

	            async checkConsumer() {
	                const name = (this.consumerName || '').trim();
	                const normalizedName = name.toLowerCase().replace(/\s+/g, ' ').trim();
	                const nameChanged = this.lastCheckedName !== '' && normalizedName !== this.lastCheckedName;

	                this.consumerLocked = false;
	                this.canChoosePackage = name.length >= 2;
	                this.similarMatches = [];
	                this.mergeTargets = [];
	                this.autoMerge = '0';

	                if (name.length < 2) {
	                    this.canChoosePackage = false;
	                    this.packageType = '';
	                    this.custom.bandagePackageId = '';
	                    this.custom.ifaksPackageId = '';
	                    this.custom.painkillerPackageId = '';
	                    this.lastCheckedName = '';
                    this.recalc();
                    this.persist();
                    return;
                }

                this.checkingConsumer = true;
                try {
                    const url = new URL(config.checkUrl, window.location.origin);
                    url.searchParams.set('name', name);
                    const res = await fetch(url.toString(), { headers: { 'Accept': 'application/json' }, cache: 'no-store' });
                    const json = await res.json();
	                    this.consumerLocked = !!json.already;
	                    this.canChoosePackage = !this.consumerLocked;
	                    this.similarMatches = Array.isArray(json.similar) ? json.similar : [];
	                    this.mergeTargets = Array.isArray(json.merge_targets) ? json.merge_targets : [];

		                    if (this.consumerLocked) {
		                        // Locked consumers must not keep package selections.
		                        this.packageType = '';
		                        this.custom.bandagePackageId = '';
		                        this.custom.ifaksPackageId = '';
		                        this.custom.painkillerPackageId = '';
		                        this.autoMerge = '0';
		                        this.mergeSelection = [];
		                        this.mergeModalOpen = false;
		                    } else if (nameChanged && !this.hydrating) {
	                        // If user changes the name, force reselect packages to avoid mismatched draft.
	                        this.packageType = '';
	                        this.custom.bandagePackageId = '';
	                        this.custom.ifaksPackageId = '';
	                        this.custom.painkillerPackageId = '';
	                        this.autoMerge = '0';
	                        this.mergeTargets = [];
	                        this.mergeSelection = [];
	                        this.mergeModalOpen = false;
	                    }

                    this.lastCheckedName = normalizedName;
                    this.recalc();
                    this.persist();
                } catch (e) {
                    this.canChoosePackage = true;
                    this.lastCheckedName = normalizedName;
                    this.recalc();
                    this.persist();
                } finally {
                    this.checkingConsumer = false;
                    this.hydrating = false;
                }
            },

            async mergeSimilarNames() {
                if (!this.mergeTargets || this.mergeTargets.length === 0) return;
                const name = (this.consumerName || '').trim();
                if (name.length < 2) return;

                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                const mergeUrl = String(config?.mergeUrl || config?.mergeConsumerUrl || '');
                if (!mergeUrl) return;

                try {
                    const res = await fetch(mergeUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            ...(token ? { 'X-CSRF-TOKEN': token } : {}),
                        },
                        body: JSON.stringify({ name, targets: this.mergeTargets }),
                    });

                    if (!res.ok) return;
                    const json = await res.json();
                    if (!json?.success) return;

                    // Enable auto merge on submit too, so if user saves after merge, it's consistent.
                    this.autoMerge = '1';

                    // Refresh status (will lock if already today).
                    await this.checkConsumer();
                } catch (e) {
                    // ignore
                }
            },

		            init() {
		                this.hydrating = true;
		                this.uiLang = window.globalLangState?.currentLang || config?.locale || 'id';
		                window.addEventListener('language-changed', (e) => {
		                    this.uiLang = String(e?.detail?.lang || this.uiLang || config?.locale || 'id');
		                });

		                // If last action saved successfully, clear persisted draft.
		                if (config?.saved) {
		                    this.clearPersisted();
		                }

                // Prefer server old() values only when present (validation error flow).
                // Otherwise restore from localStorage so refresh/offline keeps draft.
                const server = {
                    consumerName: String(config?.old?.consumerName || ''),
                    packageType: String(config?.old?.packageType || ''),
                    bandagePackageId: String(config?.old?.custom?.bandagePackageId || ''),
                    ifaksPackageId: String(config?.old?.custom?.ifaksPackageId || ''),
                    painkillerPackageId: String(config?.old?.custom?.painkillerPackageId || ''),
                };

	                const hasServerOld = !config?.saved && (
	                    (server.consumerName || '').trim() !== '' ||
	                    (server.packageType || '').trim() !== '' ||
	                    server.bandagePackageId !== '' ||
	                    server.ifaksPackageId !== '' ||
	                    server.painkillerPackageId !== ''
	                );

                const draft = this.loadPersisted();
                const source = hasServerOld ? server : (draft || {});

                this.consumerName = String(source.consumerName || '');
                this.packageType = String(source.packageType || '');
                this.custom.bandagePackageId = String(source.custom?.bandagePackageId || source.bandagePackageId || '');
                this.custom.ifaksPackageId = String(source.custom?.ifaksPackageId || source.ifaksPackageId || '');
                this.custom.painkillerPackageId = String(source.custom?.painkillerPackageId || source.painkillerPackageId || '');

                const nameLen = (this.consumerName || '').trim().length;
                this.canChoosePackage = nameLen >= 2;
                if (!this.canChoosePackage) {
                    this.packageType = '';
                    this.custom.bandagePackageId = '';
                    this.custom.ifaksPackageId = '';
                    this.custom.painkillerPackageId = '';
                }

                this.recalc();
                this.persist();

                let t = null;
                this.$watch('consumerName', () => {
                    if (t) clearTimeout(t);
                    t = setTimeout(() => this.checkConsumer(), 300);
                    this.persist();
                });

                this.$watch('custom.bandagePackageId', () => this.recalc());
                this.$watch('custom.ifaksPackageId', () => this.recalc());
                this.$watch('custom.painkillerPackageId', () => this.recalc());
                this.$watch('custom.bandagePackageId', () => this.persist());
                this.$watch('custom.ifaksPackageId', () => this.persist());
                this.$watch('custom.painkillerPackageId', () => this.persist());

                this.$watch('packageType', () => this.persist());

                if ((this.consumerName || '').trim().length >= 2) {
                    this.checkConsumer();
                } else {
                    this.hydrating = false;
                }
            }
        };
	    }
window.rekapFarmasiTodayStats = function rekapFarmasiTodayStats(config) {
	        const toInt = (v) => {
	            const n = Number.parseInt(v ?? 0, 10);
	            return Number.isFinite(n) ? n : 0;
	        };

	        return {
	            uiLang: String(window.globalLangState?.currentLang || config?.locale || 'id'),
	            currentUserId: toInt(config?.currentUserId),
	            totalTrx: toInt(config?.totalTrx),
	            totalPrice: toInt(config?.totalPrice),

	            get bonus() {
	                return Math.floor(this.totalPrice * 0.4);
	            },

	            init() {
	                window.addEventListener('language-changed', (e) => {
	                    this.uiLang = String(e?.detail?.lang || this.uiLang || 'id');
	                });

	                window.addEventListener('farmasi-sales-deleted', (e) => {
	                    const rows = Array.isArray(e?.detail?.rows) ? e.detail.rows : [];
	                    rows.forEach((r) => {
	                        if (!r) return;
	                        if (toInt(r.medicUserId) !== this.currentUserId) return;
	                        if (!r.isToday) return;
	                        this.totalTrx = Math.max(0, this.totalTrx - 1);
	                        this.totalPrice = Math.max(0, this.totalPrice - toInt(r.price));
	                    });
	                });
	            },

	            formatNumber(num) {
	                const n = toInt(num);
	                try {
	                    return new Intl.NumberFormat(this.uiLang === 'id' ? 'id-ID' : 'en-US', { maximumFractionDigits: 0 }).format(n);
	                } catch (e) {
	                    return String(n);
	                }
	            },

	            formatMoney(amount) {
	                return '$ ' + this.formatNumber(amount);
	            },
	        };
	    }
window.rekapFarmasiTable = function rekapFarmasiTable(config) {
	        const toInt = (v) => {
	            const n = Number.parseInt(v ?? 0, 10);
	            return Number.isFinite(n) ? n : 0;
	        };

	        const normalize = (v) => String(v ?? '').toLowerCase().replace(/\s+/g, ' ').trim();

	        const csrf = () => document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

	        const destroyUrl = (id) => {
	            const base = String(config?.destroyUrlTemplate || '');
	            return base.replace(/\/0$/, '/' + String(id));
	        };

	        return {
	            locale: config?.locale || 'id',
	            currentUserId: toInt(config?.currentUserId),
	            currentUserName: String(config?.currentUserName || ''),
	            rows: (Array.isArray(config?.rows) ? config.rows : [])
	                .map((r) => ({
	                    ...r,
	                    id: toInt(r?.id),
	                    createdAtTs: toInt(r?.createdAtTs),
	                    medicUserId: toInt(r?.medicUserId),
	                    bandage: toInt(r?.bandage),
	                    ifaks: toInt(r?.ifaks),
	                    painkiller: toInt(r?.painkiller),
	                    price: toInt(r?.price),
	                    bonus: toInt(r?.bonus),
	                }))
	                .sort((a, b) => (b.createdAtTs || 0) - (a.createdAtTs || 0)),
	            search: '',
	            pageSize: 25,
	            page: 1,
	            selectedIds: [],
	
	            init() {
	                const sync = (lang) => {
	                    this.locale = String(lang || this.locale || 'id');
	                };
	
	                sync(window.globalLangState?.currentLang || this.locale);
	                window.addEventListener('language-changed', (e) => sync(e?.detail?.lang));
	            },
	
	            t(key, fallback) {
	                const table = window.globalLangState?.translations || {};
	                return table?.[key] || fallback || '';
	            },

	            get filteredRows() {
	                const q = normalize(this.search);
	                if (!q) return this.rows;
	                return this.rows.filter((r) => {
	                    const hay = normalize([r.timeText, r.consumer, r.package].join(' '));
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

	            get allOnPageSelected() {
	                const ids = this.pageRows
	                    .filter((r) => r.medicUserId === this.currentUserId)
	                    .map((r) => r.id);
	                if (ids.length === 0) return false;
	                return ids.every((id) => this.selectedIds.includes(id));
	            },

	            isSelected(id) {
	                return this.selectedIds.includes(toInt(id));
	            },

	            toggleOne(id, checked) {
	                const saleId = toInt(id);
	                if (!saleId) return;
	                const row = this.rows.find((r) => r.id === saleId);
	                if (!row) return;
	                if (row.medicUserId !== this.currentUserId) return;

	                if (checked) {
	                    if (!this.selectedIds.includes(saleId)) this.selectedIds = [...this.selectedIds, saleId];
	                } else {
	                    this.selectedIds = this.selectedIds.filter((x) => x !== saleId);
	                }
	            },

	            toggleAllOnPage(checked) {
	                const pageIds = this.pageRows
	                    .filter((r) => r.medicUserId === this.currentUserId)
	                    .map((r) => r.id);

	                if (!checked) {
	                    this.selectedIds = this.selectedIds.filter((id) => !pageIds.includes(id));
	                    return;
	                }

	                const next = new Set(this.selectedIds);
	                pageIds.forEach((id) => next.add(id));
	                this.selectedIds = Array.from(next);
	            },

	            get footerTotals() {
	                const list = this.pageRows;
	                return list.reduce(
	                    (acc, r) => {
	                        acc.bandage += toInt(r.bandage);
	                        acc.ifaks += toInt(r.ifaks);
	                        acc.painkiller += toInt(r.painkiller);
	                        acc.price += toInt(r.price);
	                        acc.bonus += toInt(r.bonus);
	                        return acc;
	                    },
	                    { bandage: 0, ifaks: 0, painkiller: 0, price: 0, bonus: 0 }
	                );
	            },

	            formatMoney(amount) {
	                const n = toInt(amount);
	                try {
	                    const nf = new Intl.NumberFormat(this.locale === 'id' ? 'id-ID' : 'en-US', { maximumFractionDigits: 0 });
	                    return '$ ' + nf.format(n);
	                } catch (e) {
	                    return '$ ' + String(n);
	                }
	            },

	            sanitizeFilePart(v) {
	                return String(v ?? '')
	                    .trim()
	                    .replace(/\s+/g, '_')
	                    .replace(/[^a-zA-Z0-9_-]/g, '');
	            },

	            exportTimestamp() {
	                const pad2 = (n) => String(n).padStart(2, '0');
	                const d = new Date();
	                const yyyy = d.getFullYear();
	                const mm = pad2(d.getMonth() + 1);
	                const dd = pad2(d.getDate());
	                const hh = pad2(d.getHours());
	                const mi = pad2(d.getMinutes());
	                return `${yyyy}-${mm}-${dd}_${hh}-${mi}`;
	            },

	            exportTxt() {
	                const useSelected = this.selectedIds.length > 0;
	                const selectedSet = new Set(this.selectedIds);
	                const list = useSelected
	                    ? this.rows.filter((r) => selectedSet.has(r.id))
	                    : this.filteredRows;

	                const cols = [
	                    { key: 'timeText', label: 'Time', align: 'left' },
	                    { key: 'consumer', label: 'Consumer', align: 'left' },
	                    { key: 'package', label: 'Package', align: 'left' },
	                    { key: 'bandage', label: 'Bandage', align: 'right', map: (r) => String(toInt(r.bandage)) },
	                    { key: 'ifaks', label: 'IFAKS', align: 'right', map: (r) => String(toInt(r.ifaks)) },
	                    { key: 'painkiller', label: 'Painkiller', align: 'right', map: (r) => String(toInt(r.painkiller)) },
	                    { key: 'price', label: 'Price', align: 'right', map: (r) => String(toInt(r.price)) },
	                    { key: 'bonus', label: 'Bonus40', align: 'right', map: (r) => String(toInt(r.bonus)) },
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
	                const namePart = this.sanitizeFilePart(this.currentUserName) || 'Unknown';
	                const stamp = this.exportTimestamp();
	                const suffix = useSelected ? '_Selected' : '';
	                a.download = `Rekap_Farmasi_${namePart}_${stamp}${suffix}.txt`;
	                document.body.appendChild(a);
	                a.click();
	                a.remove();
	                URL.revokeObjectURL(url);
	            },

	            async deleteOne(id) {
	                const saleId = toInt(id);
	                if (!saleId) return;

	                const row = this.rows.find((r) => r.id === saleId);
	                if (!row) return;
	                if (row.medicUserId !== this.currentUserId) return;

	                const ok = confirm(this.t('farmasi_confirm_delete_one', 'Delete this transaction?'));
	                if (!ok) return;

	                try {
	                    const res = await fetch(destroyUrl(saleId), {
	                        method: 'DELETE',
	                        headers: {
	                            'Accept': 'application/json',
	                            ...(csrf() ? { 'X-CSRF-TOKEN': csrf() } : {}),
	                        },
	                    });
	                    if (!res.ok) throw new Error('request_failed');

	                    window.dispatchEvent(new CustomEvent('farmasi-sales-deleted', { detail: { rows: [row] } }));

	                    this.rows = this.rows.filter((r) => r.id !== saleId);
	                    this.selectedIds = this.selectedIds.filter((x) => x !== saleId);
	                    this.page = Math.min(this.page, this.pageCount);
	                } catch (e) {
	                    alert(this.t('farmasi_delete_failed', 'Failed to delete. Please refresh the page.'));
	                }
	            },

	            async deleteSelected() {
	                const ids = this.selectedIds.slice();
	                if (ids.length === 0) return;

	                const template = this.t('farmasi_confirm_delete_selected', '');
	                const msg = template
	                    ? template.replace(':count', String(ids.length))
	                    : `Delete ${ids.length} selected transactions?`;
	                const ok = confirm(msg);
	                if (!ok) return;

	                try {
	                    const res = await fetch(String(config?.bulkDestroyUrl || ''), {
	                        method: 'DELETE',
	                        headers: {
	                            'Content-Type': 'application/json',
	                            'Accept': 'application/json',
	                            ...(csrf() ? { 'X-CSRF-TOKEN': csrf() } : {}),
	                        },
	                        body: JSON.stringify({ ids }),
	                    });
	                    if (!res.ok) throw new Error('request_failed');

	                    const deletedSet = new Set(ids);
	                    const deletedRows = this.rows.filter((r) => deletedSet.has(r.id));
	                    if (deletedRows.length > 0) {
	                        window.dispatchEvent(new CustomEvent('farmasi-sales-deleted', { detail: { rows: deletedRows } }));
	                    }
	                    this.rows = this.rows.filter((r) => !deletedSet.has(r.id));
	                    this.selectedIds = [];
	                    this.page = Math.min(this.page, this.pageCount);
	                } catch (e) {
	                    alert(this.t('farmasi_delete_selected_failed', 'Failed to delete selected rows. Please refresh the page.'));
	                }
	            },
	        };
	    }

}

