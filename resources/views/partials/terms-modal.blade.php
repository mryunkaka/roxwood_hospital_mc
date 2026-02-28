{{-- Terms of Service Modal - Standalone Partial --}}

@php
    $logoPath = asset('storage/logo rh copy.png');
@endphp

<style>
/* ========================================
   PDF STYLES - SAME AS agreement.blade.php
   ======================================== */

/* Force ALL PDF content to be black text on white */
.tos-pdf-content,
.tos-pdf-content *,
.tos-pdf-content *::before,
.tos-pdf-content *::after {
    color: #000000 !important;
}

.tos-pdf-content {
    background: #ffffff !important;
    color: #000000 !important;
    font-family: 'Times New Roman', Times, serif;
    font-size: 10pt;
    line-height: 1.4;
    position: relative;
    width: 100%;
    max-width: 100%;
    box-sizing: border-box;
}

/* Reset */
.tos-pdf-content * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

/* Watermark */
.tos-pdf-content .watermark {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    opacity: 0.04;
    width: 45%;
    max-width: 250px;
    z-index: 0;
    pointer-events: none;
}

.tos-pdf-content .watermark img {
    width: 100%;
    display: block;
}

/* Header */
.tos-pdf-content .header {
    text-align: center;
    margin-bottom: 12px;
    padding-bottom: 8px;
    border-bottom: 2px solid #000;
    position: relative;
    z-index: 1;
}

.tos-pdf-content .header-logo {
    height: 50px;
    display: block;
    margin: 0 auto 5px auto;
}

.tos-pdf-content .header h2 {
    margin: 0;
    font-size: 12pt;
    font-weight: bold;
    color: #1e3a8a !important;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.tos-pdf-content .header .tagline {
    font-size: 7pt;
    margin: 2px 0 0 0;
    color: #666 !important;
    font-style: italic;
}

/* Title */
.tos-pdf-content .title {
    text-align: center;
    margin: 12px 0;
    position: relative;
    z-index: 1;
}

.tos-pdf-content .title h1 {
    margin: 0;
    font-size: 10pt;
    font-weight: bold;
    text-decoration: underline;
    text-transform: uppercase;
}

/* Content paragraphs */
.tos-pdf-content .content {
    margin-bottom: 6px;
    position: relative;
    z-index: 1;
}

.tos-pdf-content .content p {
    margin: 2px 0;
    text-align: justify;
    line-height: 1.4;
}

/* Party section */
.tos-pdf-content .party {
    margin-bottom: 8px;
    position: relative;
    z-index: 1;
}

.tos-pdf-content .party-label {
    font-weight: bold;
    margin: 4px 0 2px 0;
    display: block;
}

.tos-pdf-content .party-details {
    line-height: 1.4;
}

.tos-pdf-content .party-details-row {
    display: flex;
    align-items: baseline;
    margin: 2px 0;
}

.tos-pdf-content .party-details-label {
    flex: 0 0 145px;
    padding-right: 5px;
    white-space: nowrap;
}

.tos-pdf-content .party-details-value {
    flex: 1;
}

/* Points section */
.tos-pdf-content .points {
    margin: 8px 0 8px 18px;
    position: relative;
    z-index: 1;
}

.tos-pdf-content .points p {
    margin: 4px 0;
    text-align: justify;
    line-height: 1.4;
}

/* Statement */
.tos-pdf-content .statement {
    margin: 6px 0;
    position: relative;
    z-index: 1;
}

.tos-pdf-content .statement p {
    margin: 0;
    font-style: italic;
    text-align: center;
    line-height: 1.4;
}

/* Signatures */
.tos-pdf-content .signatures {
    margin-top: 30px;
    position: relative;
    z-index: 1;
}

.tos-pdf-content .signature-table {
    width: 100%;
    border-collapse: collapse;
    border: 1px solid #fff;
}

.tos-pdf-content .signature-table td {
    width: 50%;
    vertical-align: top;
    padding: 0;
    border: 1px solid #fff;
    text-align: center;
}

.tos-pdf-content .signature-title {
    margin: 5px 0 5px 0;
    font-weight: bold;
    font-size: 9pt;
}

.tos-pdf-content .signature-logo-wrapper {
    text-align: center;
    margin: 0;
    padding: 5px 0;
}

.tos-pdf-content .signature-logo {
    height: 70px;
    display: inline-block;
}

.tos-pdf-content .signature-image {
    height: 50px;
    display: inline-block;
}

.tos-pdf-content .signature-line {
    border: none;
    border-bottom: 1px solid #000;
    width: 70%;
    margin: 0 auto;
}

.tos-pdf-content .signature-name {
    margin: 2px 0 1px 0;
    font-weight: bold;
    font-size: 9pt;
    text-align: center;
}

.tos-pdf-content .signature-role {
    margin: 0 0 5px 0;
    font-size: 8pt;
    text-align: center;
}

/* Force row 3 to start at top */
.tos-pdf-content .signature-table tr:nth-child(3) td {
    vertical-align: top;
}

/* Helper classes */
.tos-pdf-content .text-bold {
    font-weight: bold;
}

/* ========================================
   PREVIEW CONTAINER STYLES
   ======================================== */

/* Preview container wrapper */
#tos-preview-content {
    border: 1px solid #e2e8f0;
    border-radius: 0.75rem;
    background: #f8fafc;
    padding: 20px;
    overflow-x: auto;
    overflow-y: auto;
    max-height: 500px;
}

/* PDF content needs proper padding to match PDF */
#tos-preview-content .tos-pdf-content {
    padding: 18mm 25mm 15mm 25mm !important;
    min-width: 210mm; /* A4 width */
    width: fit-content;
    margin: 0 auto;
}

/* Dark theme */
.theme-dark #tos-preview-content {
    background: #1e293b;
    border-color: #475569;
}

/* Stylis theme */
.theme-stylis #tos-preview-content {
    background: #f1f5f9;
    border-color: #cbd5e1;
}

/* ========================================
   MODAL THEMING
   ======================================== */

/* Modal styling - Dark theme */
.theme-dark [x-data="termsModalController()"] .bg-surface {
    background: #1e293b !important;
}

.theme-dark [x-data="termsModalController()"] .bg-surface-alt {
    background: #0f172a !important;
}

.theme-dark [x-data="termsModalController()"] .text-text-primary {
    color: #f1f5f9 !important;
}

.theme-dark [x-data="termsModalController()"] .text-text-secondary {
    color: #94a3b8 !important;
}

.theme-dark [x-data="termsModalController()"] button svg {
    stroke: #94a3b8 !important;
}

.theme-dark [x-data="termsModalController()"] button:hover svg {
    stroke: #f1f5f9 !important;
}

.theme-dark [x-data="termsModalController()"] .border-border {
    border-color: #334155 !important;
}

/* Modal styling - Stylis theme */
.theme-stylis [x-data="termsModalController()"] .bg-surface {
    background: rgba(255, 255, 255, 0.95) !important;
}

.theme-stylis [x-data="termsModalController()"] .bg-surface-alt {
    background: rgba(248, 250, 252, 0.9) !important;
}

.theme-stylis [x-data="termsModalController()"] .text-text-primary {
    color: #1e293b !important;
}

.theme-stylis [x-data="termsModalController()"] .text-text-secondary {
    color: #64748b !important;
}

.theme-stylis [x-data="termsModalController()"] button svg {
    stroke: #64748b !important;
}

.theme-stylis [x-data="termsModalController()"] button:hover svg {
    stroke: #1e293b !important;
}

.theme-stylis [x-data="termsModalController()"] .border-border {
    border-color: #cbd5e1 !important;
}

/* PDF Preview iframe background fix */
.theme-dark #pdf-preview-frame {
    background: #ffffff !important;
}

.theme-stylis #pdf-preview-frame {
    background: #ffffff !important;
}
</style>

<div
    x-data="termsModalController()"
    x-cloak
>
    {{-- Modal Backdrop --}}
    <div
        x-show="isOpen"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        {{-- Backdrop --}}
        <div
            class="absolute inset-0 bg-black/60 backdrop-blur-sm"
            @click="close()"
        ></div>

        {{-- Modal Content --}}
        <div
            class="relative bg-surface rounded-2xl shadow-2xl w-full max-w-6xl max-h-[90vh] overflow-hidden flex flex-col"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95 translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 translate-y-4"
        >
            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-border">
                <div>
                    <h3 class="text-xl font-semibold text-text-primary" x-text="translations.tos_title || 'Terms of Service'"></h3>
                    <p class="text-sm text-text-secondary" x-text="translations.tos_subtitle || 'Employment Agreement'"></p>
                </div>
                <button
                    @click="close()"
                    class="p-2 rounded-lg hover:bg-surface-hover text-text-secondary hover:text-text-primary transition-colors"
                    :aria-label="translations.tos_close || 'Close'"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Content Area with Tabs --}}
            <div class="flex-1 overflow-hidden flex flex-col">
                {{-- Tab Buttons --}}
                <div class="flex border-b border-border px-6">
                    <button
                        @click="currentTab = 'content'"
                        class="px-4 py-3 text-sm font-medium transition-colors border-b-2"
                        :class="currentTab === 'content' ? 'border-primary-500 text-primary-500' : 'border-transparent text-text-secondary hover:text-text-primary'"
                        x-text="translations.tos_statement_letter || 'Statement Letter'"
                    ></button>
                    <button
                        @click="currentTab = 'preview'"
                        class="px-4 py-3 text-sm font-medium transition-colors border-b-2"
                        :class="currentTab === 'preview' ? 'border-primary-500 text-primary-500' : 'border-transparent text-text-secondary hover:text-text-primary'"
                        x-text="translations.tos_preview_pdf || 'PDF Preview'"
                    ></button>
                </div>

                {{-- Tab Content --}}
                <div class="flex-1 overflow-y-auto p-6">
                    {{-- Statement Letter Content --}}
                    <div x-show="currentTab === 'content'" class="space-y-6">
                        {{-- PDF Content Template (hidden, for cloning) --}}
                        <div id="tos-content-template" class="hidden">
                            <div id="tos-pdf-content" class="tos-pdf-content">
                                {{-- Include shared agreement content --}}
                                @include('pdf.agreement-content', [
                                    'logoPath' => $logoPath
                                ])
                            </div>
                        </div>

                        {{-- Preview in Modal --}}
                        <div id="tos-preview-content">
                            {{-- Content will be cloned here --}}
                        </div>
                    </div>

                    {{-- PDF Preview Tab --}}
                    <div x-show="currentTab === 'preview'" class="space-y-4">
                        <iframe id="pdf-preview-frame" class="w-full h-[500px] border border-border rounded-xl bg-white"></iframe>
                        <div class="flex justify-end gap-3">
                            <button
                                @click="downloadPDF()"
                                class="px-4 py-2 bg-primary text-white hover:bg-primary-dark rounded-xl transition-all duration-200 flex items-center gap-2 font-semibold active:scale-[0.98]"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                                <span x-text="translations.tos_download || 'Download'"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="flex items-center justify-between px-6 py-4 border-t border-border bg-surface-alt">
                <p class="text-sm text-text-secondary"><span x-text="new Date().getFullYear()"></span> © Roxwood Health Medical Center</p>
                <div class="flex gap-3">
                    <button
                        @click="$el.disabled = true; currentTab = 'preview'; generatePDF($event)"
                        class="px-4 py-2 bg-primary text-white hover:bg-primary-dark rounded-xl transition-all duration-200 flex items-center gap-2 font-semibold active:scale-[0.98]"
                        :disabled="false"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        <span x-text="translations.tos_print_pdf || 'Print PDF'"></span>
                    </button>
                    <button
                        @click="close()"
                        class="px-4 py-2 bg-surface text-text-primary border border-border hover:bg-surface-hover hover:border-border-medium rounded-xl transition-all duration-200 font-semibold"
                        x-text="translations.tos_close || 'Close'"
                    ></button>
                </div>
            </div>
        </div>
    </div>
</div>
