{{-- Shared Agreement Content Template --}}
{{-- Used by both agreement.blade.php (PDF) and terms-modal.blade.php (Preview) --}}
{{-- Supports dynamic language switching via JavaScript classes --}}

@php
    $logoPath = $logoPath ?? asset('storage/logo rh copy.png');
    $signaturePath = $signaturePath ?? null; // User/Employee signature image path

    // For PDF: Use actual data passed from controller
    // For Preview: Use placeholder '-' that will be populated by JavaScript
    $isPdf = isset($isIndonesian) && isset($formattedDate);

    if ($isPdf) {
        // PDF Mode: Use actual data
        $displayDate = $formattedDate ?? '-';
        $displayTime = $formattedTime ?? '-';
        $displayName = $fullName ?? '-';
        $displayCitizenId = $citizenId ?? '-';
        $displayBatch = $batch ?? '-';
        $showIndonesian = $isIndonesian;
    } else {
        // Preview Mode: Use placeholders
        $displayDate = '-';
        $displayTime = '-';
        $displayName = '-';
        $displayCitizenId = '-';
        $displayBatch = '-';
        $showIndonesian = true; // Default, will be overridden by JavaScript
    }
@endphp

<div class="pdf-container">
    {{-- Header --}}
    <div class="header">
        <img src="{{ $logoPath }}" alt="Logo" class="header-logo">
        <h2>Roxwood Health Medical Center</h2>
        <p class="tagline">Healthcare Excellence with Compassion</p>
    </div>

    {{-- Title --}}
    <div class="title">
        <h1 class="agreement-title">
            {{-- Indonesian --}}
            <span class="lang-id" @if($isPdf && !$showIndonesian) style="display: none;" @endif>PERNYATAAN PERSETUJUAN DAN KOMITMEN</span>
            {{-- English --}}
            <span class="lang-en" @if($isPdf && $showIndonesian) style="display: none;" @endif>AGREEMENT AND COMMITMENT STATEMENT</span>
        </h1>
    </div>

    {{-- Preamble --}}
    <div class="content">
        <p class="agreement-preamble">
            {{-- Indonesian --}}
            <span class="lang-id" @if($isPdf && !$showIndonesian) style="display: none;" @endif>
                Pada hari ini <span class="agreement-date text-bold">{{ $displayDate }}</span>, pada jam <span class="agreement-time text-bold">{{ $displayTime }}</span>, saya yang bertanda tangan di bawah ini:
            </span>
            {{-- English --}}
            <span class="lang-en" @if($isPdf && $showIndonesian) style="display: none;" @endif>
                On this <span class="agreement-date text-bold">{{ $displayDate }}</span>, at <span class="agreement-time text-bold">{{ $displayTime }}</span>, I the undersigned:
            </span>
        </p>
    </div>

    {{-- Parties --}}
    <div class="party">
        {{-- First Party --}}
        <span class="party-label agreement-first-party-label">
            <span class="lang-id" @if($isPdf && !$showIndonesian) style="display: none;" @endif>Pihak Pertama:</span>
            <span class="lang-en" @if($isPdf && $showIndonesian) style="display: none;" @endif>First Party:</span>
        </span>
        <div class="party-details">
            <p class="agreement-first-party-desc">
                <span class="lang-id" @if($isPdf && !$showIndonesian) style="display: none;" @endif>Manajemen Rumah Sakit Roxwood Health Medical Center</span>
                <span class="lang-en" @if($isPdf && $showIndonesian) style="display: none;" @endif>Roxwood Health Medical Center Hospital Management</span>
            </p>
        </div>

        {{-- Second Party --}}
        <span class="party-label agreement-second-party-label">
            <span class="lang-id" @if($isPdf && !$showIndonesian) style="display: none;" @endif>Pihak Kedua:</span>
            <span class="lang-en" @if($isPdf && $showIndonesian) style="display: none;" @endif>Second Party:</span>
        </span>
        <div class="party-details">
            <div class="party-details-row">
                <span class="party-details-label agreement-name-label">
                    <span class="lang-id" @if($isPdf && !$showIndonesian) style="display: none;" @endif>Nama</span>
                    <span class="lang-en" @if($isPdf && $showIndonesian) style="display: none;" @endif>Name</span>
                </span>
                <span class="party-details-value">: <span class="agreement-full-name text-bold">{{ $displayName }}</span></span>
            </div>
            <div class="party-details-row">
                <span class="party-details-label agreement-citizen-id-label">
                    <span class="lang-id" @if($isPdf && !$showIndonesian) style="display: none;" @endif>ID Kewarganegaraan</span>
                    <span class="lang-en" @if($isPdf && $showIndonesian) style="display: none;" @endif>Citizenship ID</span>
                </span>
                <span class="party-details-value">: <span class="agreement-citizen-id">{{ $displayCitizenId }}</span></span>
            </div>
            <div class="party-details-row">
                <span class="party-details-label agreement-batch-label">
                    <span class="lang-id" @if($isPdf && !$showIndonesian) style="display: none;" @endif>Batch</span>
                    <span class="lang-en" @if($isPdf && $showIndonesian) style="display: none;" @endif>Batch</span>
                </span>
                <span class="party-details-value">: <span class="agreement-batch">{{ $displayBatch }}</span></span>
            </div>
        </div>
    </div>

    {{-- Agreement Text --}}
    <div class="content">
        <p class="agreement-text text-bold">
            <span class="lang-id" @if($isPdf && !$showIndonesian) style="display: none;" @endif>Pihak Pertama telah memberikan poin-poin penting untuk Pihak Kedua, dan dengan ini Pihak Kedua menyatakan hal-hal sebagai berikut:</span>
            <span class="lang-en" @if($isPdf && $showIndonesian) style="display: none;" @endif>The First Party has provided important points for the Second Party, and the Second Party hereby states the following:</span>
        </p>
    </div>

    {{-- Points --}}
    <div class="points">
        <p>
            <span class="text-bold">1.</span>
            <span class="agreement-point-1">
                <span class="lang-id" @if($isPdf && !$showIndonesian) style="display: none;" @endif>Pihak Pertama menetapkan dan Pihak Kedua bersedia mematuhi dan melaksanakan semua Standar Operasional Prosedur (SOP) dan peraturan yang berlaku di Roxwood Health Medical Center, dan Pihak Kedua dengan ini menyatakan sampai seterusnya.</span>
                <span class="lang-en" @if($isPdf && $showIndonesian) style="display: none;" @endif>The First Party establishes and the Second Party is willing to comply and implement all Standard Operating Procedures (SOP) and regulations applicable at Roxwood Health Medical Center, and the Second Party hereby declares for all time.</span>
            </span>
        </p>
        <p>
            <span class="text-bold">2.</span>
            <span class="agreement-point-2">
                <span class="lang-id" @if($isPdf && !$showIndonesian) style="display: none;" @endif>Pihak Pertama menginformasikan dan Pihak Kedua memahami bahwa pelanggaran terhadap SOP dan peraturan yang berlaku akan dikenakan sanksi sesuai dengan ketentuan yang tercantum dalam kontrak kerja yang terdapat dalam In Character (IC), dan Pihak Kedua dengan ini menyatakan sampai seterusnya.</span>
                <span class="lang-en" @if($isPdf && $showIndonesian) style="display: none;" @endif>The First Party informs and the Second Party understands that violations of applicable SOPs and regulations will be subject to sanctions in accordance with the provisions stipulated in the employment contract contained in In Character (IC), and the Second Party hereby declares for all time.</span>
            </span>
        </p>
        <p>
            <span class="text-bold">3.</span>
            <span class="agreement-point-3">
                <span class="lang-id" @if($isPdf && !$showIndonesian) style="display: none;" @endif>Pihak Pertama menegaskan dan Pihak Kedua bertanggung jawab penuh dan bersedia menerima semua konsekuensi jika terbukti bersalah melakukan pelanggaran, dan Pihak Kedua dengan ini menyatakan sampai seterusnya.</span>
                <span class="lang-en" @if($isPdf && $showIndonesian) style="display: none;" @endif>The First Party emphasizes and the Second Party is fully responsible and willing to accept all consequences if proven guilty of violations, and the Second Party hereby declares for all time.</span>
            </span>
        </p>
        <p>
            <span class="text-bold">4.</span>
            <span class="agreement-point-4">
                <span class="lang-id" @if($isPdf && !$showIndonesian) style="display: none;" @endif>Pihak Pertama mengatur dan Pihak Kedua menyetujui bahwa segala bonus atau gaji sesuai kontrak kerja akan ditahan dan dikembalikan ke rumah sakit, bukan kepada pihak manajemen perseorangan, dan Pihak Kedua dengan ini menyatakan sampai seterusnya.</span>
                <span class="lang-en" @if($isPdf && $showIndonesian) style="display: none;" @endif>The First Party arranges and the Second Party agrees that any bonuses or salary according to the employment contract will be withheld and returned to the hospital, not to individual management personnel, and the Second Party hereby declares for all time.</span>
            </span>
        </p>
    </div>

    {{-- Statement --}}
    <div class="statement">
        <p class="agreement-statement">
            <span class="lang-id" @if($isPdf && !$showIndonesian) style="display: none;" @endif>Pernyataan ini dibuat oleh Pihak Pertama dan Pihak Kedua dengan kesadaran penuh dan tanpa paksaan dari pihak manapun.</span>
            <span class="lang-en" @if($isPdf && $showIndonesian) style="display: none;" @endif>This statement is made by both the First Party and the Second Party with full awareness and without coercion from any party.</span>
        </p>
    </div>

    {{-- Signatures --}}
    <div class="signatures">
        <table class="signature-table">
            {{-- Row 1: Titles --}}
            <tr>
                <td>
                    <p class="signature-title agreement-second-party-signature">
                        <span class="lang-id" @if($isPdf && !$showIndonesian) style="display: none;" @endif>Pihak Kedua,</span>
                        <span class="lang-en" @if($isPdf && $showIndonesian) style="display: none;" @endif>Second Party,</span>
                    </p>
                </td>
                <td>
                    <p class="signature-title agreement-first-party-signature">
                        <span class="lang-id" @if($isPdf && !$showIndonesian) style="display: none;" @endif>Pihak Pertama,</span>
                        <span class="lang-en" @if($isPdf && $showIndonesian) style="display: none;" @endif>First Party,</span>
                    </p>
                </td>
            </tr>

            {{-- Row 2: Signature & Logo --}}
            <tr>
                <td>
                    @if($signaturePath)
                        <div class="signature-logo-wrapper">
                            <img src="{{ $signaturePath }}" alt="Signature" class="signature-image">
                        </div>
                    @endif
                </td>
                <td>
                    <div class="signature-logo-wrapper">
                        <img src="{{ $logoPath }}" alt="Logo" class="signature-logo">
                    </div>
                </td>
            </tr>

            {{-- Row 3: Signature Lines & Names --}}
            <tr style="vertical-align: top;">
                <td>
                    <hr class="signature-line">
                    <p class="signature-name agreement-full-name-sign">{{ $displayName }}</p>
                    <p class="signature-role agreement-employee-label">
                        <span class="lang-id" @if($isPdf && !$showIndonesian) style="display: none;" @endif>Pegawai/User</span>
                        <span class="lang-en" @if($isPdf && $showIndonesian) style="display: none;" @endif>Employee/User</span>
                    </p>
                </td>
                <td>
                    <hr class="signature-line">
                    <p class="signature-name agreement-management">
                        <span class="lang-id" @if($isPdf && !$showIndonesian) style="display: none;" @endif>Manajemen Roxwood Health Medical Center</span>
                        <span class="lang-en" @if($isPdf && $showIndonesian) style="display: none;" @endif>Roxwood Health Medical Center Management</span>
                    </p>
                </td>
            </tr>
        </table>
    </div>
</div>
