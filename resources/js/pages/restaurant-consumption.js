/**
 * Restaurant Consumption page controller (Alpine x-data factory)
 */

if (!window.__rhmc_restaurant_consumption_loaded) {
  window.__rhmc_restaurant_consumption_loaded = true;

  const toInt = (v) => {
    const n = Number.parseInt(v ?? 0, 10);
    return Number.isFinite(n) ? n : 0;
  };

  const toFloat = (v) => {
    const n = Number.parseFloat(v ?? 0);
    return Number.isFinite(n) ? n : 0;
  };

  const csrf = () => document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") || "";

  const pad2 = (n) => String(n).padStart(2, "0");

  const nowLocal = () => {
    const d = new Date();
    const y = d.getFullYear();
    const m = pad2(d.getMonth() + 1);
    const day = pad2(d.getDate());
    const hh = pad2(d.getHours());
    const mm = pad2(d.getMinutes());
    return { date: `${y}-${m}-${day}`, time: `${hh}:${mm}` };
  };

  const buildUrl = (template, id) => String(template || "").replace("__ID__", String(id));

  window.restaurantConsumptionPage = function restaurantConsumptionPage(config) {
    return {
      locale: window.globalLangState?.currentLang || "id",
      storeUrl: String(config?.storeUrl || ""),
      approveUrlTemplate: String(config?.approveUrlTemplate || ""),
      paidUrlTemplate: String(config?.paidUrlTemplate || ""),
      deleteUrlTemplate: String(config?.deleteUrlTemplate || ""),
      restaurants: Array.isArray(config?.restaurants) ? config.restaurants : [],
      strings: config?.strings || {},

      addOpen: false,
      submitting: false,

      docPreviewOpen: false,
      docPreviewSrc: "",
      docPreviewTitle: "",

      form: {
        code: String(config?.defaultCode || ""),
        restaurantId: "",
        deliveryDate: "",
        deliveryTime: "",
        packetCount: 1,
        notes: "",
      },

      calc: {
        pricePerPacket: 0,
        taxPercentage: 0,
        subtotal: 0,
        taxAmount: 0,
        totalAmount: 0,
      },

      init() {
        const sync = (lang) => {
          this.locale = String(lang || this.locale || "id");
        };
        sync(window.globalLangState?.currentLang || this.locale);
        window.addEventListener("language-changed", (e) => sync(e?.detail?.lang));

        const n = nowLocal();
        this.form.deliveryDate = n.date;
        this.form.deliveryTime = n.time;

        const first = this.restaurants?.[0];
        if (first?.id) {
          this.form.restaurantId = String(first.id);
        }
        this.recalc();
      },

      t(key, fallback) {
        const table = window.globalLangState?.translations || {};
        return table?.[key] || fallback || "";
      },

      formatMoney(amount) {
        try {
          const n = Number(amount || 0);
          const nf = new Intl.NumberFormat(this.locale === "id" ? "id-ID" : "en-US", { maximumFractionDigits: 0 });
          return "$ " + nf.format(n);
        } catch {
          return "$ " + String(amount || 0);
        }
      },

      restaurantById(id) {
        const rid = toInt(id);
        return (this.restaurants || []).find((r) => toInt(r?.id) === rid) || null;
      },

      recalc() {
        const r = this.restaurantById(this.form.restaurantId);
        const packets = Math.max(0, toInt(this.form.packetCount));
        const price = toFloat(r?.price_per_packet);
        const taxPct = toFloat(r?.tax_percentage);

        const subtotal = price * packets;
        const taxAmount = subtotal * (taxPct / 100);
        const total = subtotal + taxAmount;

        this.calc.pricePerPacket = price;
        this.calc.taxPercentage = taxPct;
        this.calc.subtotal = subtotal;
        this.calc.taxAmount = taxAmount;
        this.calc.totalAmount = total;
      },

      openDoc(src, title) {
        this.docPreviewSrc = String(src || "");
        this.docPreviewTitle = String(title || "");
        this.docPreviewOpen = true;
      },

      async submitCreate() {
        if (this.submitting) return;
        this.submitting = true;
        try {
          const fd = new FormData();
          fd.append("restaurant_id", String(this.form.restaurantId || ""));
          fd.append("delivery_date", String(this.form.deliveryDate || ""));
          fd.append("delivery_time", String(this.form.deliveryTime || ""));
          fd.append("packet_count", String(this.form.packetCount || ""));
          fd.append("notes", String(this.form.notes || ""));

          const fileInput = this.$root.querySelector('input[type="file"][name="ktp_file"]');
          const file = fileInput?.files?.[0] || null;
          if (file) fd.append("ktp_file", file);

          const res = await fetch(this.storeUrl, {
            method: "POST",
            headers: {
              "X-CSRF-TOKEN": csrf(),
              "X-Requested-With": "XMLHttpRequest",
            },
            body: fd,
          });

          const data = await res.json().catch(() => ({}));
          if (!res.ok || !data?.success) {
            const msg = String(data?.message || "");
            if (window.$toast?.error) window.$toast.error(msg || "Failed");
            else alert(msg || "Failed");
            return;
          }

          if (window.$toast?.success) window.$toast.success(String(data?.message || this.strings?.saved || "Saved"));
          this.addOpen = false;
          window.location.reload();
        } catch (e) {
          const msg = String(e?.message || "Error");
          if (window.$toast?.error) window.$toast.error(msg);
          else alert(msg);
        } finally {
          this.submitting = false;
        }
      },

      async approveRow(id) {
        const ok = window.confirm(String(this.strings?.confirmApprove || "Approve?"));
        if (!ok) return;
        await this.postAction(buildUrl(this.approveUrlTemplate, id));
      },

      async markPaidRow(id) {
        const ok = window.confirm(String(this.strings?.confirmPaid || "Mark as paid?"));
        if (!ok) return;
        await this.postAction(buildUrl(this.paidUrlTemplate, id));
      },

      async deleteRow(id) {
        const ok = window.confirm(String(this.strings?.confirmDelete || "Delete?"));
        if (!ok) return;
        await this.postAction(buildUrl(this.deleteUrlTemplate, id));
      },

      async postAction(url) {
        try {
          const res = await fetch(String(url || ""), {
            method: "POST",
            headers: {
              "X-CSRF-TOKEN": csrf(),
              "X-Requested-With": "XMLHttpRequest",
              "Content-Type": "application/x-www-form-urlencoded",
            },
            body: "",
          });
          const data = await res.json().catch(() => ({}));
          if (!res.ok || !data?.success) {
            const msg = String(data?.message || "");
            if (window.$toast?.error) window.$toast.error(msg || "Failed");
            else alert(msg || "Failed");
            return;
          }
          if (window.$toast?.success) window.$toast.success(String(data?.message || "OK"));
          window.location.reload();
        } catch (e) {
          const msg = String(e?.message || "Error");
          if (window.$toast?.error) window.$toast.error(msg);
          else alert(msg);
        }
      },
    };
  };
}
