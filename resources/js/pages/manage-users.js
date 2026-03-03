/**
 * ============================================
 * ROXWOOD HEALTH MEDICAL CENTER
 * Manage Users Page Controller
 * ============================================
 */

window.manageUsersPage = function manageUsersPage(config) {
    const toInt = (v) => {
        const n = Number.parseInt(v ?? 0, 10);
        return Number.isFinite(n) ? n : 0;
    };

    const normalize = (v) => String(v ?? '').toLowerCase().replace(/\s+/g, ' ').trim();

    const csrf = () => document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    const urlWithId = (template, id) => {
        const t = String(template || '');
        // Replace placeholder `0` whether it's the last segment (`/0`) or in the middle (`/0/resign`).
        return t.replace(/\/0(?=\/|\?|$)/, '/' + String(id));
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

    const basename = (path) => {
        const p = String(path || '').trim().replace(/\\/g, '/');
        if (!p) return '';
        const parts = p.split('/');
        return parts[parts.length - 1] || '';
    };

    const looksLikeDocPath = (value) => {
        const v = String(value || '').trim();
        if (!v) return false;
        if (v.startsWith('http://') || v.startsWith('https://')) return true;
        if (v.startsWith('/') || v.startsWith('storage/')) return true;
        return /\.(png|jpe?g|pdf)$/i.test(v);
    };

    const mapUser = (u) => ({
        id: toInt(u?.id),
        full_name: String(u?.full_name || ''),
        position: String(u?.position || ''),
        role: String(u?.role || ''),
        is_active: Boolean(u?.is_active),
        tanggal_masuk: u?.tanggal_masuk ? String(u.tanggal_masuk) : '',
        batch: u?.batch === null || u?.batch === undefined || u?.batch === '' ? null : toInt(u.batch),
        kode_nomor_induk_rs: String(u?.kode_nomor_induk_rs || ''),
        sertifikat_heli: String(u?.sertifikat_heli || ''),
        sertifikat_operasi: String(u?.sertifikat_operasi || ''),
        dokumen_lainnya: String(u?.dokumen_lainnya || ''),
        resign_reason: String(u?.resign_reason || ''),
        resigned_at: u?.resigned_at ? String(u.resigned_at) : '',
        resigned_by_name: String(u?.resigned_by_name || ''),
        reactivated_at: u?.reactivated_at ? String(u.reactivated_at) : '',
        reactivated_note: String(u?.reactivated_note || ''),
        reactivated_by_name: String(u?.reactivated_by_name || ''),
        can_manage: Boolean(u?.can_manage),
        is_self: Boolean(u?.is_self),
    });

    return {
        locale: config?.locale || 'id',
        users: (Array.isArray(config?.users) ? config.users : []).map(mapUser),

        searchColumn: 'all',
        search: '',

        saving: false,
        activeId: 0,

        addOpen: false,
        editOpen: false,
        resignOpen: false,
        reactivateOpen: false,
        deleteOpen: false,

        addForm: {
            full_name: '',
            position: 'Trainee',
            role: 'Staff',
            batch: '',
        },

        editForm: {
            full_name: '',
            position: 'Trainee',
            role: 'Staff',
            batch: '',
            kode_nomor_induk_rs: '',
            new_pin: '',
        },

        resignReason: '',
        reactivateNote: '',

        previewOpen: false,
        previewList: [],
        previewIndex: 0,
        previewDoc: null,
        previewScale: 1,
        previewTranslateX: 0,
        previewTranslateY: 0,
        previewPointers: new Map(),
        previewPinchStartDist: 0,
        previewPinchStartScale: 1,

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

        get searchPlaceholder() {
            const mode = String(this.searchColumn || 'all');
            const map = {
                all: this.t('manage_users_search_all_placeholder', 'Search users...'),
                name: this.t('manage_users_search_name_placeholder', 'Search name...'),
                position: this.t('manage_users_search_position_placeholder', 'Search position...'),
                role: this.t('manage_users_search_role_placeholder', 'Search role...'),
                docs: this.t('manage_users_search_docs_placeholder', 'Search documents...'),
                join: this.t('manage_users_search_join_placeholder', 'Search join date...'),
            };
            return map[mode] || map.all;
        },

        fileUrl(path) {
            const p = String(path || '').trim();
            if (!p) return '';
            if (p.startsWith('http://') || p.startsWith('https://') || p.startsWith('/')) return p;
            return '/' + p.replace(/^\.?\//, '');
        },

        formatDateHuman(value) {
            const v = String(value || '').trim();
            if (!v) return '';
            try {
                const dt = new Date(v);
                if (Number.isNaN(dt.getTime())) return v;
                return new Intl.DateTimeFormat(this.locale === 'id' ? 'id-ID' : 'en-US', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric',
                }).format(dt);
            } catch {
                return v;
            }
        },

        formatJoinDate(value) {
            const v = String(value || '').trim();
            if (!v) return '';
            try {
                const [y, m, d] = v.split('-').map((x) => toInt(x));
                const dt = new Date(y, Math.max(0, m - 1), d);
                if (Number.isNaN(dt.getTime())) return v;
                return new Intl.DateTimeFormat(this.locale === 'id' ? 'id-ID' : 'en-US', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric',
                }).format(dt);
            } catch {
                return v;
            }
        },

        tenureText(joinDate) {
            const v = String(joinDate || '').trim();
            if (!v) return '-';
            try {
                const [y, m, d] = v.split('-').map((x) => toInt(x));
                const start = new Date(y, Math.max(0, m - 1), d);
                const now = new Date();
                if (Number.isNaN(start.getTime()) || start > now) return '-';

                const ms = now.getTime() - start.getTime();
                const days = Math.floor(ms / (1000 * 60 * 60 * 24));
                const years = Math.floor(days / 365);
                const months = Math.floor((days % 365) / 30);

                if (years > 0) {
                    return this.locale === 'id'
                        ? `${years} tahun${months > 0 ? ` ${months} bulan` : ''}`
                        : `${years} year${years > 1 ? 's' : ''}${months > 0 ? ` ${months} mo` : ''}`;
                }

                if (months > 0) {
                    return this.locale === 'id' ? `${months} bulan` : `${months} mo`;
                }

                if (days >= 7) {
                    const weeks = Math.floor(days / 7);
                    return this.locale === 'id' ? `${weeks} minggu` : `${weeks} wk`;
                }

                return this.locale === 'id' ? `${days} hari` : `${days} d`;
            } catch {
                return '-';
            }
        },

        userDocs(u) {
            const out = [];
            const heli = String(u?.sertifikat_heli || '').trim();
            const operasi = String(u?.sertifikat_operasi || '').trim();
            const other = String(u?.dokumen_lainnya || '').trim();

            if (heli) {
                out.push({
                    key: 'heli',
                    label: this.t('manage_users_doc_heli', 'Heli Certificate'),
                    src: this.fileUrl(heli),
                });
            }

            if (operasi) {
                out.push({
                    key: 'operasi',
                    label: this.t('manage_users_doc_operasi', 'Operation Certificate'),
                    src: this.fileUrl(operasi),
                });
            }

            if (other) {
                const tryJson = other.startsWith('[') || other.startsWith('{');
                if (tryJson) {
                    try {
                        const parsed = JSON.parse(other);

                        // Supported formats:
                        // 1) Array of docs: [{name, path}, ...]
                        // 2) Wrapped format: {academy: [{id,name,path}, ...], legacy: "..."}
                        // 3) Single doc object: {name, path}
                        const isObj = parsed && typeof parsed === 'object' && !Array.isArray(parsed);
                        const academy = isObj && Array.isArray(parsed.academy) ? parsed.academy : null;
                        const legacy = isObj && typeof parsed.legacy === 'string' ? String(parsed.legacy || '').trim() : '';

                        const list = Array.isArray(parsed) ? parsed : academy ? academy : isObj ? [parsed] : [];
                        const prefixKey = academy ? 'manage_users_doc_academy_prefix' : 'manage_users_doc_other_prefix';
                        const prefixFallback = academy ? 'Academy' : 'Other';
                        for (const item of list) {
                            const src = String(item?.path || item?.src || '').trim();
                            if (!src || !looksLikeDocPath(src)) continue;
                            const name = String(item?.name || item?.label || basename(src) || '').trim();
                            const stableId = String(item?.id || '').trim();
                            out.push({
                                key: (academy ? 'academy_' : 'other_') + (stableId || basename(src) || String(out.length + 1)),
                                label: this.t(prefixKey, prefixFallback) + (name ? `: ${name}` : ''),
                                src: this.fileUrl(src),
                            });
                        }

                        if (legacy && !(legacy.startsWith('[') || legacy.startsWith('{')) && looksLikeDocPath(legacy)) {
                            out.push({
                                key: 'other_legacy_' + (basename(legacy) || String(out.length + 1)),
                                label: this.t('manage_users_doc_other', 'Other Document'),
                                src: this.fileUrl(legacy),
                            });
                        }
                    } catch {
                        out.push({
                            key: 'other',
                            label: this.t('manage_users_doc_other', 'Other Document'),
                            src: this.fileUrl(other),
                        });
                    }
                } else {
                    out.push({
                        key: 'other',
                        label: this.t('manage_users_doc_other', 'Other Document'),
                        src: this.fileUrl(other),
                    });
                }
            }

            return out;
        },

        get activeRow() {
            const id = toInt(this.activeId);
            if (!id) return null;
            return this.users.find((u) => toInt(u.id) === id) || null;
        },

        get filteredUsers() {
            const mode = String(this.searchColumn || 'all');
            const raw = normalize(this.search);
            const terms = raw ? raw.split(/\s+/).filter(Boolean) : [];

            return this.users.filter((u) => {
                if (terms.length === 0) return true;

                const docs = this.userDocs(u);
                const docsHay = normalize(docs.map((d) => `${d.label} ${basename(d.src)}`).join(' '));

                const joinHay = normalize([this.formatJoinDate(u.tanggal_masuk), u.tanggal_masuk].join(' '));
                const allHay = normalize([
                    u.full_name,
                    u.position,
                    u.role,
                    joinHay,
                    docsHay,
                    u.batch ?? '',
                ].join(' '));

                const hayByMode = {
                    name: normalize(u.full_name),
                    position: normalize(u.position),
                    role: normalize(u.role),
                    docs: docsHay,
                    join: joinHay,
                    all: allHay,
                };

                const hay = hayByMode[mode] || allHay;
                return terms.every((t) => hay.includes(t));
            });
        },

        get groups() {
            const map = new Map();

            for (const u of this.filteredUsers) {
                const b = u.batch === null || u.batch === undefined ? null : toInt(u.batch);
                const key = b ? `batch_${b}` : 'none';
                if (!map.has(key)) {
                    map.set(key, {
                        key,
                        batch: b,
                        title: '',
                        users: [],
                    });
                }
                map.get(key).users.push(u);
            }

            const groups = Array.from(map.values());
            groups.sort((a, b) => {
                if (a.key === 'none') return 1;
                if (b.key === 'none') return -1;
                return toInt(a.batch) - toInt(b.batch);
            });

            for (const g of groups) {
                if (g.key === 'none') {
                    g.title = this.t('manage_users_no_batch', 'No Batch');
                } else {
                    const batchLabel = this.t('batch', 'Batch');
                    g.title = `${batchLabel} ${toInt(g.batch)}`;
                }
                g.users.sort((x, y) => String(x.full_name).localeCompare(String(y.full_name)));
            }

            return groups;
        },

        openAdd() {
            this.activeId = 0;
            this.addForm = { full_name: '', position: 'Trainee', role: 'Staff', batch: '' };
            this.addOpen = true;
        },

        openEdit(u) {
            if (!u?.can_manage) return;
            this.activeId = toInt(u.id);
            this.editForm = {
                full_name: String(u.full_name || ''),
                position: String(u.position || 'Trainee') || 'Trainee',
                role: String(u.role || 'Staff') || 'Staff',
                batch: u.batch === null || u.batch === undefined ? '' : toInt(u.batch),
                kode_nomor_induk_rs: String(u.kode_nomor_induk_rs || ''),
                new_pin: '',
            };
            this.editOpen = true;
        },

        openResign(u) {
            if (!u?.can_manage) return;
            this.activeId = toInt(u.id);
            this.resignReason = '';
            this.resignOpen = true;
        },

        openReactivate(u) {
            if (!u?.can_manage) return;
            this.activeId = toInt(u.id);
            this.reactivateNote = '';
            this.reactivateOpen = true;
        },

        openDelete(u) {
            if (!u?.can_manage) return;
            this.activeId = toInt(u.id);
            this.deleteOpen = true;
        },

        toastSuccess(message) {
            if (window.$toast?.success) window.$toast.success(message);
        },

        toastError(message) {
            if (window.$toast?.error) window.$toast.error(message);
            else alert(message);
        },

        async submitAdd() {
            if (this.saving) return;
            this.saving = true;
            try {
                const payload = {
                    full_name: String(this.addForm.full_name || '').trim(),
                    position: String(this.addForm.position || '').trim(),
                    role: String(this.addForm.role || '').trim(),
                    batch: String(this.addForm.batch || '').trim() === '' ? null : toInt(this.addForm.batch),
                };

                const res = await fetch(String(config?.storeUrl || ''), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        ...(csrf() ? { 'X-CSRF-TOKEN': csrf() } : {}),
                    },
                    body: JSON.stringify(payload),
                });

                const json = await res.json().catch(() => ({}));
                if (!res.ok || !json?.success) {
                    const msg = pickFirstValidationError(json) || this.t('manage_users_save_failed', 'Failed to save. Please try again.');
                    this.toastError(msg);
                    return;
                }

                this.users = [...this.users, mapUser(json.row || {})];
                this.addOpen = false;
                this.toastSuccess(this.t('manage_users_added', 'User added.'));
            } catch {
                this.toastError(this.t('manage_users_save_failed', 'Failed to save. Please try again.'));
            } finally {
                this.saving = false;
            }
        },

        async submitEdit() {
            if (this.saving) return;
            const id = toInt(this.activeId);
            if (!id) return;

            this.saving = true;
            try {
                const url = urlWithId(config?.updateUrlTemplate, id);
                const payload = {
                    full_name: String(this.editForm.full_name || '').trim(),
                    position: String(this.editForm.position || '').trim(),
                    role: String(this.editForm.role || '').trim(),
                    batch: String(this.editForm.batch || '').trim() === '' ? null : toInt(this.editForm.batch),
                    new_pin: String(this.editForm.new_pin || '').trim() || null,
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
                    const msg = pickFirstValidationError(json) || this.t('manage_users_save_failed', 'Failed to save. Please try again.');
                    this.toastError(msg);
                    return;
                }

                const row = mapUser(json.row || {});
                this.users = this.users.map((u) => (toInt(u.id) === id ? { ...u, ...row } : u));
                this.editOpen = false;
                this.toastSuccess(this.t('manage_users_saved', 'Saved.'));
            } catch {
                this.toastError(this.t('manage_users_save_failed', 'Failed to save. Please try again.'));
            } finally {
                this.saving = false;
            }
        },

        async deleteKodeMedis() {
            const id = toInt(this.activeId);
            if (!id) return;

            const confirmMsg = this.t('manage_users_confirm_delete_kode', 'Delete medical code?');
            if (!window.confirm(confirmMsg)) return;

            if (this.saving) return;
            this.saving = true;
            try {
                const url = urlWithId(config?.deleteKodeMedisUrlTemplate, id);
                const res = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        ...(csrf() ? { 'X-CSRF-TOKEN': csrf() } : {}),
                    },
                });
                const json = await res.json().catch(() => ({}));
                if (!res.ok || !json?.success) {
                    const msg = pickFirstValidationError(json) || this.t('manage_users_action_failed', 'Action failed. Please try again.');
                    this.toastError(msg);
                    return;
                }

                this.editForm.kode_nomor_induk_rs = '';
                this.users = this.users.map((u) => (toInt(u.id) === id ? { ...u, kode_nomor_induk_rs: '' } : u));
                this.toastSuccess(this.t('manage_users_kode_deleted', 'Medical code deleted.'));
            } catch {
                this.toastError(this.t('manage_users_action_failed', 'Action failed. Please try again.'));
            } finally {
                this.saving = false;
            }
        },

        async submitResign() {
            if (this.saving) return;
            const id = toInt(this.activeId);
            if (!id) return;

            const reason = String(this.resignReason || '').trim();
            if (!reason) {
                this.toastError(this.t('manage_users_resign_reason_required', 'Reason is required.'));
                return;
            }

            this.saving = true;
            try {
                const url = urlWithId(config?.resignUrlTemplate, id);
                const res = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        ...(csrf() ? { 'X-CSRF-TOKEN': csrf() } : {}),
                    },
                    body: JSON.stringify({ resign_reason: reason }),
                });
                const json = await res.json().catch(() => ({}));
                if (!res.ok || !json?.success) {
                    const msg = pickFirstValidationError(json) || this.t('manage_users_action_failed', 'Action failed. Please try again.');
                    this.toastError(msg);
                    return;
                }

                const row = mapUser(json.row || {});
                this.users = this.users.map((u) => (toInt(u.id) === id ? { ...u, ...row } : u));
                this.resignOpen = false;
                this.toastSuccess(this.t('manage_users_resigned_success', 'User resigned.'));
            } catch {
                this.toastError(this.t('manage_users_action_failed', 'Action failed. Please try again.'));
            } finally {
                this.saving = false;
            }
        },

        async submitReactivate() {
            if (this.saving) return;
            const id = toInt(this.activeId);
            if (!id) return;

            this.saving = true;
            try {
                const url = urlWithId(config?.reactivateUrlTemplate, id);
                const res = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        ...(csrf() ? { 'X-CSRF-TOKEN': csrf() } : {}),
                    },
                    body: JSON.stringify({ reactivated_note: String(this.reactivateNote || '').trim() || null }),
                });
                const json = await res.json().catch(() => ({}));
                if (!res.ok || !json?.success) {
                    const msg = pickFirstValidationError(json) || this.t('manage_users_action_failed', 'Action failed. Please try again.');
                    this.toastError(msg);
                    return;
                }

                const row = mapUser(json.row || {});
                this.users = this.users.map((u) => (toInt(u.id) === id ? { ...u, ...row } : u));
                this.reactivateOpen = false;
                this.toastSuccess(this.t('manage_users_reactivated_success', 'User reactivated.'));
            } catch {
                this.toastError(this.t('manage_users_action_failed', 'Action failed. Please try again.'));
            } finally {
                this.saving = false;
            }
        },

        async submitDelete() {
            if (this.saving) return;
            const id = toInt(this.activeId);
            if (!id) return;

            this.saving = true;
            try {
                const url = urlWithId(config?.destroyUrlTemplate, id);
                const res = await fetch(url, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        ...(csrf() ? { 'X-CSRF-TOKEN': csrf() } : {}),
                    },
                });
                const json = await res.json().catch(() => ({}));
                if (!res.ok || !json?.success) {
                    const msg = pickFirstValidationError(json) || this.t('manage_users_action_failed', 'Action failed. Please try again.');
                    this.toastError(msg);
                    return;
                }

                this.users = this.users.filter((u) => toInt(u.id) !== id);
                this.deleteOpen = false;
                this.toastSuccess(this.t('manage_users_deleted_success', 'User deleted.'));
            } catch {
                this.toastError(this.t('manage_users_action_failed', 'Action failed. Please try again.'));
            } finally {
                this.saving = false;
            }
        },

        openPreview(doc, list) {
            const docs = Array.isArray(list) ? list : [];
            const d = doc || null;
            if (!d?.src) return;

            this.previewList = docs;
            this.previewIndex = Math.max(0, docs.findIndex((x) => x?.src === d.src));
            this.previewDoc = docs[this.previewIndex] || d;

            this.previewScale = 1;
            this.previewTranslateX = 0;
            this.previewTranslateY = 0;
            this.previewPointers = new Map();
            this.previewPinchStartDist = 0;
            this.previewPinchStartScale = 1;

            this.previewOpen = true;
        },

        prevDoc() {
            if (this.previewList.length <= 1) return;
            this.previewIndex = (this.previewIndex - 1 + this.previewList.length) % this.previewList.length;
            this.previewDoc = this.previewList[this.previewIndex] || null;
            this.resetZoom();
        },

        nextDoc() {
            if (this.previewList.length <= 1) return;
            this.previewIndex = (this.previewIndex + 1) % this.previewList.length;
            this.previewDoc = this.previewList[this.previewIndex] || null;
            this.resetZoom();
        },

        zoomIn() {
            this.previewScale = Math.min(5, this.previewScale + 0.1);
        },

        zoomOut() {
            this.previewScale = Math.max(0.3, this.previewScale - 0.1);
        },

        resetZoom() {
            this.previewScale = 1;
            this.previewTranslateX = 0;
            this.previewTranslateY = 0;
            this.previewPointers = new Map();
            this.previewPinchStartDist = 0;
            this.previewPinchStartScale = 1;
        },

        onPreviewWheel(e) {
            const delta = e?.deltaY || 0;
            if (delta > 0) this.zoomOut();
            if (delta < 0) this.zoomIn();
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
            const next = { x: e.clientX, y: e.clientY };
            this.previewPointers.set(e.pointerId, next);

            if (this.previewPointers.size === 1) {
                this.previewTranslateX += next.x - prev.x;
                this.previewTranslateY += next.y - prev.y;
                return;
            }

            if (this.previewPointers.size === 2) {
                const pts = Array.from(this.previewPointers.values());
                const dx = pts[0].x - pts[1].x;
                const dy = pts[0].y - pts[1].y;
                const dist = Math.hypot(dx, dy);
                if (!this.previewPinchStartDist) return;
                const ratio = dist / this.previewPinchStartDist;
                this.previewScale = Math.max(0.3, Math.min(5, this.previewPinchStartScale * ratio));
            }
        },

        onPreviewPointerUp(e) {
            this.previewPointers.delete(e.pointerId);
            if (this.previewPointers.size < 2) {
                this.previewPinchStartDist = 0;
                this.previewPinchStartScale = this.previewScale;
            }
        },

        exportText(opts = {}) {
            const onlyNoBatch = Boolean(opts?.onlyNoBatch);

            const toRoman = (num) => {
                const map = [
                    [1000, 'M'],
                    [900, 'CM'],
                    [500, 'D'],
                    [400, 'CD'],
                    [100, 'C'],
                    [90, 'XC'],
                    [50, 'L'],
                    [40, 'XL'],
                    [10, 'X'],
                    [9, 'IX'],
                    [5, 'V'],
                    [4, 'IV'],
                    [1, 'I'],
                ];
                let n = Math.floor(Number(num));
                if (!Number.isFinite(n) || n <= 0) return '';
                let out = '';
                for (const [value, roman] of map) {
                    while (n >= value) {
                        out += roman;
                        n -= value;
                    }
                }
                return out;
            };

            let output = '';
            const list = onlyNoBatch ? this.groups.filter((g) => g.key === 'none') : this.groups;

            for (const g of list) {
                if (!Array.isArray(g.users) || g.users.length === 0) continue;
                let title = String(g.title || '').trim();
                const m = title.match(/(\d+)/);
                if (m) {
                    const roman = toRoman(toInt(m[1]));
                    if (roman) title = `${this.t('manage_users_batch_upper', 'BATCH')} ${roman}`;
                } else if (g.key === 'none') {
                    title = this.t('manage_users_no_batch_upper', 'NO BATCH');
                } else {
                    title = title.toUpperCase();
                }

                output += title + '\n';
                let no = 1;
                for (const u of g.users) {
                    const noStr = String(no).padStart(2, '0');
                    const jabatan = String(u.position || '').trim();
                    output += `${noStr}. ${u.full_name}${jabatan ? ` (${jabatan})` : ''}\n`;
                    no += 1;
                }
                output += '\n';
            }

            if (!output.trim()) {
                this.toastError(this.t('manage_users_export_empty', 'No data to export.'));
                return;
            }

            const blob = new Blob([output], { type: 'text/plain;charset=utf-8;' });
            const url = URL.createObjectURL(blob);

            const a = document.createElement('a');
            a.href = url;
            a.download = onlyNoBatch ? 'no_batch.txt' : 'user_list.txt';
            document.body.appendChild(a);
            a.click();

            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        },
    };
};
