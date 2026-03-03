/**
 * ============================================
 * ROXWOOD HEALTH MEDICAL CENTER
 * Validasi Akun Page Controller
 * ============================================
 */

window.validasiAkunPage = function validasiAkunPage(config) {
    const toInt = (v) => {
        const n = Number.parseInt(v ?? 0, 10);
        return Number.isFinite(n) ? n : 0;
    };

    const normalize = (v) => String(v ?? '').toLowerCase().replace(/\s+/g, ' ').trim();
    const csrf = () => document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    const urlWithId = (template, id) => {
        const t = String(template || '');
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

    const mapUser = (u) => ({
        id: toInt(u?.id),
        full_name: String(u?.full_name || ''),
        citizen_id: String(u?.citizen_id || ''),
        no_hp_ic: String(u?.no_hp_ic || ''),
        jenis_kelamin: String(u?.jenis_kelamin || ''),
        role: String(u?.role || ''),
        batch: u?.batch === null || u?.batch === undefined ? '' : toInt(u?.batch),
        kode_nomor_induk_rs: String(u?.kode_nomor_induk_rs || ''),
        position: String(u?.position || ''),
        tanggal_masuk: u?.tanggal_masuk === null || u?.tanggal_masuk === undefined ? '' : String(u?.tanggal_masuk),
        resigned_at: u?.resigned_at === null || u?.resigned_at === undefined ? '' : String(u?.resigned_at),
        photo_profile: String(u?.photo_profile || ''),
        file_ktp: String(u?.file_ktp || ''),
        file_sim: String(u?.file_sim || ''),
        file_kta: String(u?.file_kta || ''),
        file_skb: String(u?.file_skb || ''),
        sertifikat_heli: String(u?.sertifikat_heli || ''),
        sertifikat_operasi: String(u?.sertifikat_operasi || ''),
        dokumen_lainnya: String(u?.dokumen_lainnya || ''),
        is_verified: Boolean(u?.is_verified),
        is_active: Boolean(u?.is_active),
        created_at: u?.created_at === null || u?.created_at === undefined ? '' : String(u?.created_at),
    });

    return {
        locale: config?.locale || 'id',
        users: (Array.isArray(config?.users) ? config.users : []).map(mapUser),

        search: '',
        filter: 'pending', // all | pending | active
        pageSize: 10,
        page: 1,

        editOpen: false,
        saving: false,
        activeId: 0,

        previewOpen: false,
        previewTitle: '',
        previewSrc: '',
        previewScale: 1,
        previewTranslateX: 0,
        previewTranslateY: 0,
        previewPointers: new Map(),
        previewPinchStartDist: 0,
        previewPinchStartScale: 1,

        form: {
            full_name: '',
            citizen_id: '',
            role: '',
            position: '',
            batch: '',
            tanggal_masuk: '',
            is_verified: false,
            is_active: false,
            resigned_at: '',
            file_ktp: '',
            file_skb: '',
            file_sim: '',
            file_kta: '',
            sertifikat_heli: '',
            sertifikat_operasi: '',
            dokumen_lainnya: '',
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

        normalizePosition(value) {
            const v = String(value || '').trim();
            const lower = v.toLowerCase();
            if (!v) return '';

            if (lower === 'dokter umum') return 'General Doctor';
            if (lower === 'dokter spesialis') return 'Specialist Doctor';
            if (lower === 'doctor umum') return 'General Doctor';
            if (lower === 'doctor specialist') return 'Specialist Doctor';
            if (lower === 'wakil direktur') return '';
            if (lower === 'direktur') return '';

            return v;
        },

        isImagePath(path) {
            const p = String(path || '').toLowerCase().trim();
            return Boolean(p.match(/\.(png|jpe?g|webp|gif)$/));
        },

        fileUrl(path) {
            const p = String(path || '').trim();
            if (!p) return '#';
            if (p.startsWith('http://') || p.startsWith('https://') || p.startsWith('/')) return p;
            return '/' + p.replace(/^\.?\//, '');
        },

        openPreview(title, path) {
            const p = String(path || '').trim();
            if (!p) return;

            const url = this.fileUrl(p);
            if (!this.isImagePath(url)) {
                window.open(url, '_blank', 'noopener');
                return;
            }

            this.previewTitle = String(title || '');
            this.previewSrc = url;
            this.previewScale = 1;
            this.previewTranslateX = 0;
            this.previewTranslateY = 0;
            this.previewPointers = new Map();
            this.previewPinchStartDist = 0;
            this.previewPinchStartScale = 1;
            this.previewOpen = true;
        },

        closePreview() {
            this.previewOpen = false;
            this.previewSrc = '';
            this.previewTitle = '';
        },

        clampScale(s) {
            const v = Number(s);
            if (!Number.isFinite(v)) return 1;
            return Math.min(6, Math.max(1, v));
        },

        zoomIn() {
            this.previewScale = this.clampScale(this.previewScale + 0.25);
        },

        zoomOut() {
            this.previewScale = this.clampScale(this.previewScale - 0.25);
        },

        resetZoom() {
            this.previewScale = 1;
            this.previewTranslateX = 0;
            this.previewTranslateY = 0;
        },

        onPreviewWheel(e) {
            const delta = Number(e?.deltaY || 0);
            const dir = delta > 0 ? -1 : 1;
            const next = this.previewScale + dir * 0.15;
            this.previewScale = this.clampScale(next);
        },

        onPreviewPointerDown(e) {
            try {
                e?.target?.setPointerCapture?.(e.pointerId);
            } catch {
                // ignore
            }
            this.previewPointers.set(e.pointerId, { x: e.clientX, y: e.clientY });
            if (this.previewPointers.size === 2) {
                const pts = Array.from(this.previewPointers.values());
                const dx = pts[0].x - pts[1].x;
                const dy = pts[0].y - pts[1].y;
                this.previewPinchStartDist = Math.hypot(dx, dy);
                this.previewPinchStartScale = this.previewScale;
            }
        },

        onPreviewPointerMove(e) {
            if (!this.previewPointers.has(e.pointerId)) return;
            const prev = this.previewPointers.get(e.pointerId);
            this.previewPointers.set(e.pointerId, { x: e.clientX, y: e.clientY });

            if (this.previewPointers.size === 1 && this.previewScale > 1) {
                const dx = e.clientX - (prev?.x ?? e.clientX);
                const dy = e.clientY - (prev?.y ?? e.clientY);
                this.previewTranslateX += dx;
                this.previewTranslateY += dy;
                return;
            }

            if (this.previewPointers.size === 2) {
                const pts = Array.from(this.previewPointers.values());
                const dx = pts[0].x - pts[1].x;
                const dy = pts[0].y - pts[1].y;
                const dist = Math.hypot(dx, dy);
                if (this.previewPinchStartDist > 0) {
                    const ratio = dist / this.previewPinchStartDist;
                    this.previewScale = this.clampScale(this.previewPinchStartScale * ratio);
                }
            }
        },

        onPreviewPointerUp(e) {
            this.previewPointers.delete(e.pointerId);
            if (this.previewPointers.size < 2) {
                this.previewPinchStartDist = 0;
                this.previewPinchStartScale = this.previewScale;
            }
        },

        formatDate(ymd) {
            const v = String(ymd || '').trim();
            if (!v) return '-';
            try {
                const d = new Date(v.length === 10 ? v + 'T00:00:00' : v);
                return new Intl.DateTimeFormat(this.locale === 'id' ? 'id-ID' : 'en-US', { year: 'numeric', month: 'short', day: '2-digit' }).format(d);
            } catch {
                return v;
            }
        },

        isResigned(row) {
            return Boolean(String(row?.resigned_at || '').trim());
        },

        statusLabel(row) {
            if (this.isResigned(row)) return this.t('validation_status_resign', 'Resign');
            return row?.is_active ? this.t('validation_status_active', 'Aktif') : this.t('validation_status_pending', 'Pending');
        },

        statusClass(row) {
            if (this.isResigned(row)) return 'bg-danger/10 text-danger border-danger/30';
            return row?.is_active ? 'bg-success/10 text-success border-success/30' : 'bg-warning/10 text-warning border-warning/30';
        },

        tenureText(row) {
            const startRaw = String(row?.tanggal_masuk || '').trim();
            const endRaw = this.isResigned(row) ? String(row?.resigned_at || '').trim() : '';
            if (!startRaw || !endRaw) return '-';

            try {
                const start = new Date(startRaw.length === 10 ? startRaw + 'T00:00:00' : startRaw);
                const end = new Date(endRaw);
                const diffMs = end.getTime() - start.getTime();
                if (!Number.isFinite(diffMs) || diffMs <= 0) return '-';

                const totalDays = Math.floor(diffMs / 86400000);
                const years = Math.floor(totalDays / 365);
                const months = Math.floor((totalDays % 365) / 30);
                const days = totalDays - (years * 365) - (months * 30);

                const parts = [];
                if (years > 0) parts.push(this.locale === 'id' ? `${years} th` : `${years} yr`);
                if (months > 0) parts.push(this.locale === 'id' ? `${months} bln` : `${months} mo`);
                if (days > 0 || parts.length === 0) parts.push(this.locale === 'id' ? `${days} hr` : `${days} d`);
                return parts.join(' ');
            } catch {
                return '-';
            }
        },

        get filteredRows() {
            const q = normalize(this.search);
            const status = String(this.filter || 'all');
            return this.users.filter((u) => {
                const resigned = this.isResigned(u);
                if (status === 'pending' && (u.is_active || resigned)) return false;
                if (status === 'active' && (!u.is_active || resigned)) return false;
                if (status === 'resign' && !resigned) return false;
                if (!q) return true;
                const hay = normalize([u.full_name, u.citizen_id, u.role, u.position, u.batch, u.kode_nomor_induk_rs].join(' '));
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

        get activeRow() {
            const id = toInt(this.activeId);
            if (!id) return null;
            return this.users.find((u) => toInt(u.id) === id) || null;
        },

        prevPage() {
            this.page = Math.max(1, toInt(this.page) - 1);
        },

        nextPage() {
            this.page = Math.min(this.pageCount, toInt(this.page) + 1);
        },

        openEdit(row) {
            const r = row || {};
            this.activeId = toInt(r.id);
            this.form = {
                full_name: String(r.full_name || ''),
                citizen_id: String(r.citizen_id || ''),
                role: String(r.role || 'Staff'),
                position: this.normalizePosition(r.position),
                batch: r.batch === '' || r.batch === null || r.batch === undefined ? '' : toInt(r.batch),
                tanggal_masuk: this.formatDate(r.tanggal_masuk),
                is_verified: Boolean(r.is_verified),
                is_active: Boolean(r.is_active),
                resigned_at: String(r.resigned_at || ''),
                file_ktp: String(r.file_ktp || ''),
                file_skb: String(r.file_skb || ''),
                file_sim: String(r.file_sim || ''),
                file_kta: String(r.file_kta || ''),
                sertifikat_heli: String(r.sertifikat_heli || ''),
                sertifikat_operasi: String(r.sertifikat_operasi || ''),
                dokumen_lainnya: String(r.dokumen_lainnya || ''),
            };
            this.editOpen = true;
        },

        async saveEdit() {
            if (this.saving) return;
            const id = toInt(this.activeId);
            if (!id) return;

            this.saving = true;
            try {
                const url = urlWithId(config?.updateUserUrlTemplate, id);

                const payload = {
                    role: String(this.form.role || '').trim() || null,
                    position: this.normalizePosition(this.form.position),
                    is_verified: Boolean(this.form.is_verified),
                    is_active: Boolean(this.form.is_active),
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
                    const msg = pickFirstValidationError(json) || this.t('validation_save_failed', 'Gagal menyimpan. Silakan coba lagi.');
                    window.$toast?.error ? window.$toast.error(msg) : alert(msg);
                    return;
                }

                const row = mapUser(json?.row || {});
                this.users = this.users.map((u) => (toInt(u.id) === id ? { ...u, ...row } : u));

                window.$toast?.success ? window.$toast.success(this.t('validation_saved', 'Berhasil disimpan.')) : null;
                this.editOpen = false;
            } catch (e) {
                const msg = this.t('validation_save_failed', 'Gagal menyimpan. Silakan coba lagi.');
                window.$toast?.error ? window.$toast.error(msg) : alert(msg);
            } finally {
                this.saving = false;
            }
        },
    };
};
