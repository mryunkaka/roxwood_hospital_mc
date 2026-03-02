/**
 * ============================================
 * ROXWOOD HEALTH MEDICAL CENTER
 * Medis - Regulasi EMS Page Controller
 * ============================================
 */

window.regulasiEmsPage = function regulasiEmsPage(config) {
    const toInt = (v) => {
        const n = Number.parseInt(v ?? 0, 10);
        return Number.isFinite(n) ? n : 0;
    };

    const normalize = (v) => String(v ?? '').toLowerCase().replace(/\s+/g, ' ').trim();
    const csrf = () => document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    const urlWithId = (template, id) => {
        const t = String(template || '');
        // Common case: template ends with `/0`
        if (t.match(/\/0(\?|$)/)) {
            return t.replace(/\/0(\?|$)/, '/' + String(id) + '$1');
        }
        return t;
    };

    const pickFirstValidationError = (json) => {
        const errors = json?.errors || {};
        for (const key of Object.keys(errors)) {
            const v = errors[key];
            if (Array.isArray(v) && v.length > 0) return String(v[0]);
            if (typeof v === 'string' && v) return v;
        }
        return json?.message ? String(json.message) : '';
    };

    return {
        locale: config?.locale || 'id',

        packages: (Array.isArray(config?.packages) ? config.packages : []).map((p) => ({
            id: toInt(p?.id),
            name: String(p?.name || ''),
            bandage_qty: toInt(p?.bandage_qty),
            ifaks_qty: toInt(p?.ifaks_qty),
            painkiller_qty: toInt(p?.painkiller_qty),
            price: toInt(p?.price),
        })),

        regs: (Array.isArray(config?.regs) ? config.regs : []).map((r) => ({
            id: toInt(r?.id),
            category: String(r?.category || ''),
            code: String(r?.code || ''),
            name: String(r?.name || ''),
            location: r?.location === null || r?.location === undefined ? '' : String(r.location),
            price_type: String(r?.price_type || 'FIXED'),
            price_min: toInt(r?.price_min),
            price_max: toInt(r?.price_max),
            payment_type: String(r?.payment_type || 'CASH'),
            duration_minutes: r?.duration_minutes === null || r?.duration_minutes === undefined ? '' : toInt(r.duration_minutes),
            notes: r?.notes === null || r?.notes === undefined ? '' : String(r.notes),
            is_active: Boolean(r?.is_active),
        })),

        pkgSearch: '',
        pkgPageSize: 10,
        pkgPage: 1,

        regSearch: '',
        regPageSize: 10,
        regPage: 1,

        pkgEditOpen: false,
        regEditOpen: false,
        saving: false,

        pkgActiveId: 0,
        regActiveId: 0,

        pkgForm: {
            name: '',
            bandage_qty: 0,
            ifaks_qty: 0,
            painkiller_qty: 0,
            price: 0,
        },

        regForm: {
            category: '',
            code: '',
            name: '',
            location: '',
            price_type: 'FIXED',
            price_min: 0,
            price_max: 0,
            payment_type: 'CASH',
            duration_minutes: '',
            notes: '',
            is_active: true,
        },

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

        formatNumber(n) {
            try {
                return new Intl.NumberFormat(this.locale === 'id' ? 'id-ID' : 'en-US', { maximumFractionDigits: 0 }).format(Number(n || 0));
            } catch (e) {
                return String(n || 0);
            }
        },

        formatMoney(amount) {
            return '$ ' + this.formatNumber(amount);
        },

        // =========================
        // Packages table
        // =========================
        get pkgFilteredRows() {
            const q = normalize(this.pkgSearch);
            if (!q) return this.packages;
            return this.packages.filter((r) => normalize([r.name, r.bandage_qty, r.ifaks_qty, r.painkiller_qty, r.price].join(' ')).includes(q));
        },

        get pkgPageCount() {
            const total = this.pkgFilteredRows.length;
            const size = Math.max(1, toInt(this.pkgPageSize));
            return Math.max(1, Math.ceil(total / size));
        },

        get pkgPageRows() {
            const size = Math.max(1, toInt(this.pkgPageSize));
            const p = Math.min(Math.max(1, toInt(this.pkgPage)), this.pkgPageCount);
            const start = (p - 1) * size;
            return this.pkgFilteredRows.slice(start, start + size);
        },

        get pkgTotalShown() {
            return this.pkgPageRows.reduce((sum, r) => sum + toInt(r.price), 0);
        },

        openPkgEdit(row) {
            const r = row || {};
            this.pkgActiveId = toInt(r.id);
            this.pkgForm = {
                name: String(r.name || ''),
                bandage_qty: toInt(r.bandage_qty),
                ifaks_qty: toInt(r.ifaks_qty),
                painkiller_qty: toInt(r.painkiller_qty),
                price: toInt(r.price),
            };
            this.pkgEditOpen = true;
        },

        closePkgEdit() {
            this.pkgEditOpen = false;
            this.pkgActiveId = 0;
        },

        async savePkgEdit() {
            if (this.saving) return;
            const id = toInt(this.pkgActiveId);
            if (!id) return;

            this.saving = true;
            try {
                const url = urlWithId(config?.updatePackageUrlTemplate, id);
                const payload = {
                    name: String(this.pkgForm.name || '').trim(),
                    bandage_qty: toInt(this.pkgForm.bandage_qty),
                    ifaks_qty: toInt(this.pkgForm.ifaks_qty),
                    painkiller_qty: toInt(this.pkgForm.painkiller_qty),
                    price: toInt(this.pkgForm.price),
                };

                const res = await fetch(url, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        ...(csrf() ? { 'X-CSRF-TOKEN': csrf() } : {}),
                    },
                    body: JSON.stringify(payload),
                });

                const json = await res.json().catch(() => ({}));
                if (!res.ok || !json?.success) {
                    const msg = pickFirstValidationError(json) || this.t('medis_regulasi_save_failed', 'Gagal menyimpan');
                    window.$toast?.error ? window.$toast.error(msg) : alert(msg);
                    return;
                }

                const row = json?.row || {};
                this.packages = this.packages.map((p) => (toInt(p.id) === id ? { ...p, ...row } : p));
                window.$toast?.success ? window.$toast.success(this.t('medis_regulasi_saved', 'Tersimpan')) : null;
                this.closePkgEdit();
            } catch (e) {
                const msg = this.t('medis_regulasi_save_failed', 'Gagal menyimpan');
                window.$toast?.error ? window.$toast.error(msg) : alert(msg);
            } finally {
                this.saving = false;
            }
        },

        // =========================
        // Regulations table
        // =========================
        regPriceText(row) {
            const r = row || {};
            const min = toInt(r.price_min);
            const max = toInt(r.price_max);
            if (String(r.price_type || '') === 'FIXED') {
                return this.formatMoney(min);
            }
            return this.formatMoney(min) + ' - ' + this.formatMoney(Math.max(min, max));
        },

        get regFilteredRows() {
            const q = normalize(this.regSearch);
            if (!q) return this.regs;
            return this.regs.filter((r) => {
                const hay = normalize([r.category, r.code, r.name, r.location, r.price_type, r.price_min, r.price_max, r.payment_type, r.duration_minutes, r.notes, r.is_active ? 'aktif' : 'nonaktif'].join(' '));
                return hay.includes(q);
            });
        },

        get regPageCount() {
            const total = this.regFilteredRows.length;
            const size = Math.max(1, toInt(this.regPageSize));
            return Math.max(1, Math.ceil(total / size));
        },

        get regPageRows() {
            const size = Math.max(1, toInt(this.regPageSize));
            const p = Math.min(Math.max(1, toInt(this.regPage)), this.regPageCount);
            const start = (p - 1) * size;
            return this.regFilteredRows.slice(start, start + size);
        },

        get regTotalShown() {
            // For RANGE, use min as baseline total for "shown"
            return this.regPageRows.reduce((sum, r) => sum + toInt(r.price_min), 0);
        },

        openRegEdit(row) {
            const r = row || {};
            this.regActiveId = toInt(r.id);
            this.regForm = {
                category: String(r.category || ''),
                code: String(r.code || ''),
                name: String(r.name || ''),
                location: String(r.location || ''),
                price_type: String(r.price_type || 'FIXED'),
                price_min: toInt(r.price_min),
                price_max: toInt(r.price_max),
                payment_type: String(r.payment_type || 'CASH'),
                duration_minutes: r.duration_minutes === '' || r.duration_minutes === null || r.duration_minutes === undefined ? '' : toInt(r.duration_minutes),
                notes: String(r.notes || ''),
                is_active: Boolean(r.is_active),
            };
            this.onRegPriceTypeChange();
            this.regEditOpen = true;
        },

        closeRegEdit() {
            this.regEditOpen = false;
            this.regActiveId = 0;
        },

        onRegPriceTypeChange() {
            if (String(this.regForm.price_type || '') === 'FIXED') {
                this.regForm.price_max = toInt(this.regForm.price_min);
            } else {
                const min = toInt(this.regForm.price_min);
                const max = toInt(this.regForm.price_max);
                if (max < min) this.regForm.price_max = min;
            }
        },

        onRegMinChanged() {
            if (String(this.regForm.price_type || '') === 'FIXED') {
                this.regForm.price_max = toInt(this.regForm.price_min);
            }
        },

        async saveRegEdit() {
            if (this.saving) return;
            const id = toInt(this.regActiveId);
            if (!id) return;

            this.saving = true;
            try {
                const url = urlWithId(config?.updateRegUrlTemplate, id);

                const priceType = String(this.regForm.price_type || 'FIXED');
                const priceMin = toInt(this.regForm.price_min);
                const priceMax = priceType === 'FIXED' ? priceMin : Math.max(priceMin, toInt(this.regForm.price_max));

                const payload = {
                    category: String(this.regForm.category || '').trim(),
                    name: String(this.regForm.name || '').trim(),
                    location: String(this.regForm.location || '').trim() || null,
                    price_type: priceType,
                    price_min: priceMin,
                    price_max: priceMax,
                    payment_type: String(this.regForm.payment_type || 'CASH'),
                    duration_minutes: String(this.regForm.duration_minutes ?? '').trim() === '' ? null : toInt(this.regForm.duration_minutes),
                    notes: String(this.regForm.notes || '').trim() || null,
                    is_active: Boolean(this.regForm.is_active),
                };

                const res = await fetch(url, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        ...(csrf() ? { 'X-CSRF-TOKEN': csrf() } : {}),
                    },
                    body: JSON.stringify(payload),
                });

                const json = await res.json().catch(() => ({}));
                if (!res.ok || !json?.success) {
                    const msg = pickFirstValidationError(json) || this.t('medis_regulasi_save_failed', 'Gagal menyimpan');
                    window.$toast?.error ? window.$toast.error(msg) : alert(msg);
                    return;
                }

                const row = json?.row || {};
                this.regs = this.regs.map((r) => (toInt(r.id) === id ? { ...r, ...row } : r));
                window.$toast?.success ? window.$toast.success(this.t('medis_regulasi_saved', 'Tersimpan')) : null;
                this.closeRegEdit();
            } catch (e) {
                const msg = this.t('medis_regulasi_save_failed', 'Gagal menyimpan');
                window.$toast?.error ? window.$toast.error(msg) : alert(msg);
            } finally {
                this.saving = false;
            }
        },
    };
};

