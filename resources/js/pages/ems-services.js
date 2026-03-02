/**
 * Auto-generated from Blade inline <script> blocks.
 * Keep page logic reusable & consistent across pages.
 */

if (!window.
__rhmc_ems_services_loaded
) {
  window.
__rhmc_ems_services_loaded
 = true;

window.emsServicesForm = function emsServicesForm(config) {
        const csrf = () => document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const toInt = (v) => {
            const n = Number.parseInt(v ?? 0, 10);
            return Number.isFinite(n) ? n : 0;
        };
        const STORAGE_KEY = 'rhmc_ems_services_form_v1';

        const DETAIL_OPTIONS = {
            Pingsan: ['RS', 'Paleto', 'Gunung/Laut', 'Zona Perang', 'UFC'],
            Treatment: ['RS', 'Luar'],
            Surat: ['Kesehatan', 'Psikologi'],
            Operasi: ['Besar', 'Kecil'],
            'Rawat Inap': ['Reguler', 'VIP'],
            Kematian: ['Pemakaman', 'Kremasi'],
            Plastik: ['Operasi Plastik'],
        };

        return {
            locale: config?.locale || 'id',
            previewUrl: String(config?.previewUrl || ''),

            serviceType: String(config?.old?.service_type || ''),
            serviceDetail: String(config?.old?.service_detail || ''),
            operasiTingkat: String(config?.old?.operasi_tingkat || ''),
            patientName: String(config?.old?.patient_name || ''),
            location: String(config?.old?.location || ''),
            qty: toInt(config?.old?.qty || 1) || 1,
            paymentType: String(config?.old?.payment_type || ''),
            isGunshot: Boolean(config?.old?.is_gunshot || false),

            showDetail: true,
            showOperasiTingkat: false,
            showPatient: false,
            showLocation: false,
            showQty: false,
            showPayment: false,
            showMedicine: false,
            paymentLocked: false,
            paymentHint: '',
            detailHint: '',

            totalHtml: '$ 0',
            totalHint: '',
            _restoring: false,
            _saveTimer: null,

            init() {
                if (config?.saved) {
                    this.clearDraft();
                } else {
                    this.restoreDraft();
                }

                this.applyUiRules();
                this.refreshDetailOptions(true);

                const form = this.$refs.form;
                if (form) {
                    const handler = () => this.saveDraft();
                    form.addEventListener('input', handler);
                    form.addEventListener('change', handler);
                }

                this.preview();
            },

            t(key, fallback) {
                const table = window.globalLangState?.translations || {};
                return table?.[key] || fallback || '';
            },

            clearForm() {
                this.serviceType = '';
                this.serviceDetail = '';
                this.operasiTingkat = '';
                this.patientName = '';
                this.location = '';
                this.qty = 1;
                this.paymentType = '';
                this.isGunshot = false;

                try { this.$refs.form?.reset(); } catch { /* ignore */ }
                this.clearDraft();
                this.applyUiRules();
                this.refreshDetailOptions(true);
                this.totalHtml = '$ 0';
                this.totalHint = '';
            },

            onServiceTypeChange(restoring = false) {
                if (!restoring) {
                    this.serviceDetail = '';
                    this.operasiTingkat = '';
                    this.isGunshot = false;
                    this.patientName = '';
                    this.location = '';
                    this.qty = 1;
                    this.paymentType = '';

                    const form = this.$refs.form;
                    if (form) {
                        form.querySelectorAll('input[name="meds[]"]').forEach((el) => { el.checked = false; });
                    }
                }
                this.applyUiRules();
                this.refreshDetailOptions(false);
                this.preview();
            },

            applyUiRules() {
                const type = this.serviceType || '';
                const detail = this.serviceDetail || '';

                this.showDetail = type !== 'Plastik' && type !== '';
                this.showOperasiTingkat = type === 'Operasi';
                this.showMedicine = type === 'Pingsan' || type === 'Treatment';
                this.showPatient = ['Surat', 'Operasi', 'Rawat Inap', 'Kematian', 'Plastik'].includes(type);
                this.showQty = type === 'Rawat Inap';

                if (type === 'Kematian') {
                    this.showLocation = true;
                } else if ((type === 'Pingsan' || type === 'Treatment') && detail && detail !== 'RS') {
                    this.showLocation = true;
                } else {
                    this.showLocation = false;
                }

                this.showPayment = type !== '';
                this.paymentLocked = false;
                this.paymentHint = '';

                if (type === 'Pingsan' || type === 'Treatment' || type === 'Surat') {
                    this.paymentType = 'cash';
                    this.paymentLocked = true;
                    this.paymentHint = this.t('medis_payment_locked_cash', 'Cash is enforced for this service');
                } else if (type === 'Operasi' || type === 'Rawat Inap') {
                    this.paymentType = 'billing';
                    this.paymentLocked = true;
                    this.paymentHint = this.t('medis_payment_locked_billing', 'Billing is enforced for this service');
                } else if (type === 'Plastik') {
                    this.paymentType = 'mixed';
                    this.paymentLocked = true;
                    this.paymentHint = this.t('medis_payment_locked_mixed', 'Cash + Billing enforced for this service');
                } else if (type === 'Kematian') {
                    if (!this.paymentType) this.paymentType = 'cash';
                    this.paymentLocked = false;
                    this.paymentHint = '';
                }

                this.detailHint = type ? this.t('medis_choose_detail_hint', '') : '';
            },

            refreshDetailOptions(restoreOld) {
                const el = document.getElementById('serviceDetail');
                if (!el) return;

                const options = DETAIL_OPTIONS[this.serviceType] || [];
                el.innerHTML = '';

                const placeholder = document.createElement('option');
                placeholder.value = '';
                placeholder.textContent = this.t('medis_choose_detail', 'Choose detail');
                el.appendChild(placeholder);

                options.forEach((v) => {
                    const opt = document.createElement('option');
                    opt.value = v;
                    opt.textContent = v;
                    el.appendChild(opt);
                });

                if (restoreOld && this.serviceDetail) {
                    el.value = this.serviceDetail;
                }
            },

            clearDraft() {
                try { localStorage.removeItem(STORAGE_KEY); } catch { /* ignore */ }
            },

            saveDraft() {
                if (this._restoring) return;

                if (this._saveTimer) {
                    clearTimeout(this._saveTimer);
                }

                this._saveTimer = setTimeout(() => {
                    const form = this.$refs.form;
                    if (!form) return;

                    const meds = Array.from(form.querySelectorAll('input[name="meds[]"]:checked')).map((el) => String(el.value || ''));
                    const gunshotEl = form.querySelector('input[name="is_gunshot"]');

                    const draft = {
                        service_type: String(this.serviceType || ''),
                        service_detail: String(this.serviceDetail || ''),
                        operasi_tingkat: String(this.operasiTingkat || ''),
                        patient_name: String(this.patientName || ''),
                        location: String(this.location || ''),
                        qty: toInt(this.qty || 1) || 1,
                        payment_type: String(this.paymentType || ''),
                        is_gunshot: Boolean(gunshotEl?.checked || false),
                        meds: meds.filter((v) => v !== ''),
                        saved_at: new Date().toISOString(),
                    };

                    try {
                        localStorage.setItem(STORAGE_KEY, JSON.stringify(draft));
                    } catch { /* ignore */ }
                }, 120);
            },

            restoreDraft() {
                let raw = null;
                try { raw = localStorage.getItem(STORAGE_KEY); } catch { raw = null; }
                if (!raw) return;

                let data = null;
                try { data = JSON.parse(raw); } catch { data = null; }
                if (!data || typeof data !== 'object') return;

                this._restoring = true;

                this.serviceType = String(data.service_type || '');
                // Make sure detail options are present before setting detail.
                this.applyUiRules();
                this.refreshDetailOptions(false);

                this.serviceDetail = String(data.service_detail || '');
                this.operasiTingkat = String(data.operasi_tingkat || '');
                this.patientName = String(data.patient_name || '');
                this.location = String(data.location || '');
                this.qty = toInt(data.qty || 1) || 1;
                this.paymentType = String(data.payment_type || '');
                this.isGunshot = Boolean(data.is_gunshot || false);

                // Sync checkbox DOM (meds + gunshot) because meds[] are not bound.
                const form = this.$refs.form;
                if (form) {
                    const meds = Array.isArray(data.meds) ? data.meds.map((v) => String(v)) : [];
                    form.querySelectorAll('input[name="meds[]"]').forEach((el) => {
                        el.checked = meds.includes(String(el.value || ''));
                    });
                    const gunshotEl = form.querySelector('input[name="is_gunshot"]');
                    if (gunshotEl) gunshotEl.checked = this.isGunshot;
                }

                // Apply UI rules that depend on detail.
                this.applyUiRules();

                setTimeout(() => {
                    this._restoring = false;
                    this.preview();
                }, 50);
            },

            async preview() {
                const type = this.serviceType || '';
                if (!type) {
                    this.totalHtml = '$ 0';
                    this.totalHint = this.t('medis_choose_service_type', 'Choose service type');
                    return;
                }

                this.applyUiRules();

                // Avoid 422 spam: only hit preview API when required inputs are ready.
                // Backend rules (calculate): service_detail required for most types, operasi_tingkat required for Operasi.
                const detail = String(this.serviceDetail || '');
                const tingkat = String(this.operasiTingkat || '');
                if (type !== 'Plastik') {
                    if (!detail) {
                        this.totalHtml = '$ 0';
                        this.totalHint = this.t('medis_choose_service_detail', 'Choose service detail');
                        return;
                    }
                }
                if (type === 'Operasi' && !tingkat) {
                    this.totalHtml = '$ 0';
                    this.totalHint = this.t('medis_operasi_tingkat_required', 'Choose operasi level');
                    return;
                }

                const form = this.$refs.form;
                if (!form || !this.previewUrl) return;

                const data = new FormData(form);
                data.set('service_type', this.serviceType);
                if (this.paymentType) data.set('payment_type', this.paymentType);
                if (this.qty) data.set('qty', String(this.qty));
                if (this.location) data.set('location', String(this.location));
                if (this.isGunshot) data.set('is_gunshot', '1');

                try {
                    const res = await fetch(this.previewUrl, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            ...(csrf() ? { 'X-CSRF-TOKEN': csrf() } : {}),
                        },
                        body: data,
                    });
                    const json = await res.json().catch(() => null);
                    if (!res.ok || !json || !json.success) {
                        this.totalHtml = this.t('medis_cannot_calculate', 'Cannot calculate');
                        this.totalHint = (json && json.message) ? String(json.message) : this.t('medis_check_form', 'Check your input');
                        return;
                    }

                    const total = toInt(json.total || 0);
                    const med = (json.breakdown || {}).medicine || {};
                    const medCount = toInt(med.count || 0);
                    const perItem = toInt(med.per_item || 0);
                    const sub = toInt(med.subtotal || 0);

                    let html = '$ ' + total.toLocaleString(this.locale === 'id' ? 'id-ID' : 'en-US');
                    if (medCount > 0) {
                        html += `<div class="mt-1 text-xs text-text-secondary">${this.t('medis_medicine_breakdown', 'Medicine')}: ${medCount} Ã— $ ${perItem.toLocaleString()} = $ ${sub.toLocaleString()}</div>`;
                    }
                    if (String(json.payment_type || '') === 'mixed') {
                        html += `<div class="mt-1 text-xs text-text-secondary">${this.t('medis_payment_mixed_note', 'Includes cash + billing')}</div>`;
                    }

                    this.totalHtml = html;
                    this.totalHint = '';
                } catch (e) {
                    this.totalHtml = this.t('medis_cannot_calculate', 'Cannot calculate');
                    this.totalHint = this.t('medis_preview_failed', 'Failed to fetch preview');
                }
            },
        };
    }
window.emsServicesTable = function emsServicesTable(config) {
        const toInt = (v) => {
            const n = Number.parseInt(v ?? 0, 10);
            return Number.isFinite(n) ? n : 0;
        };
        const normalize = (v) => String(v ?? '').toLowerCase().replace(/\s+/g, ' ').trim();
        const csrf = () => document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const destroyUrl = (id) => String(config?.destroyUrlTemplate || '').replace(/\/0$/, '/' + String(id));

        return {
            locale: config?.locale || 'id',
            currentMedicName: String(config?.currentMedicName || ''),
            rows: (Array.isArray(config?.rows) ? config.rows : [])
                .map((r) => ({
                    id: toInt(r?.id),
                    createdAtTs: toInt(r?.createdAtTs),
                    timeText: String(r?.timeText || ''),
                    serviceType: String(r?.serviceType || ''),
                    detail: String(r?.detail || ''),
                    patient: String(r?.patient || '-'),
                    paymentType: String(r?.paymentType || ''),
                    total: toInt(r?.total),
                    medicName: String(r?.medicName || ''),
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

            paymentText(row) {
                const pt = String(row?.paymentType || '');
                if (pt === 'mixed') return this.t('medis_payment_mixed', 'Mixed');
                if (pt === 'billing') return this.t('medis_payment_billing', 'Billing');
                if (pt === 'cash') return this.t('medis_payment_cash', 'Cash');
                return pt || '-';
            },

            get filteredRows() {
                const q = normalize(this.search);
                if (!q) return this.rows;
                return this.rows.filter((r) => {
                    const hay = normalize([r.timeText, r.serviceType, r.detail, r.patient, r.paymentType].join(' '));
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

            get totalShown() {
                return this.pageRows.reduce((sum, r) => sum + toInt(r.total), 0);
            },

            get allOnPageSelected() {
                const ids = this.pageRows
                    .filter((r) => r.medicName === this.currentMedicName)
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
                if (row.medicName !== this.currentMedicName) return;

                if (checked) {
                    if (!this.selectedIds.includes(saleId)) this.selectedIds = [...this.selectedIds, saleId];
                } else {
                    this.selectedIds = this.selectedIds.filter((x) => x !== saleId);
                }
            },

            toggleAllOnPage(checked) {
                const pageIds = this.pageRows
                    .filter((r) => r.medicName === this.currentMedicName)
                    .map((r) => r.id);

                if (!checked) {
                    this.selectedIds = this.selectedIds.filter((id) => !pageIds.includes(id));
                    return;
                }

                const next = new Set(this.selectedIds);
                pageIds.forEach((id) => next.add(id));
                this.selectedIds = Array.from(next);
            },

            async deleteOne(id) {
                const saleId = toInt(id);
                if (!saleId) return;
                const row = this.rows.find((r) => r.id === saleId);
                if (!row) return;
                if (row.medicName !== this.currentMedicName) return;

                const ok = confirm(this.t('medis_confirm_delete_one', 'Delete this transaction?'));
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

                    this.rows = this.rows.filter((r) => r.id !== saleId);
                    this.selectedIds = this.selectedIds.filter((x) => x !== saleId);
                    this.page = Math.min(this.page, this.pageCount);
                } catch (e) {
                    alert(this.t('medis_delete_failed', 'Failed to delete. Please refresh the page.'));
                }
            },

            async deleteSelected() {
                const ids = this.selectedIds.slice();
                if (ids.length === 0) return;

                const template = this.t('medis_confirm_delete_selected', '');
                const msg = template ? template.replace(':count', String(ids.length)) : `Delete ${ids.length} selected transactions?`;
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
                    this.rows = this.rows.filter((r) => !deletedSet.has(r.id));
                    this.selectedIds = [];
                    this.page = Math.min(this.page, this.pageCount);
                } catch (e) {
                    alert(this.t('medis_delete_selected_failed', 'Failed to delete selected rows. Please refresh the page.'));
                }
            },
        };
    }

}

