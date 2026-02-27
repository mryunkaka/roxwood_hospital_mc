<!DOCTYPE html>
<html lang="{{ $locale ?? 'id' }}">

<head>
    <meta charset="utf-8">
    <title>{{ $title ?? 'Agreement' }}</title>
    <style>
        @page {
            margin: 0;
            size: A4 portrait;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 10pt;
            line-height: 1.4;
            color: #000000;
            background: #ffffff;
            padding: 18mm 25mm 15mm 25mm;
        }

        .pdf-container {
            position: relative;
            width: 100%;
            max-width: 100%;
        }

        /* Watermark */
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0.04;
            width: 45%;
            max-width: 250px;
            z-index: 0;
            pointer-events: none;
        }

        .watermark img {
            width: 100%;
            display: block;
        }

        /* Header */
        .header {
            text-align: center;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 2px solid #000;
            position: relative;
            z-index: 1;
        }

        .header-logo {
            height: 50px;
            display: block;
            margin: 0 auto 5px auto;
        }

        .header h2 {
            margin: 0;
            font-size: 12pt;
            font-weight: bold;
            color: #1e3a8a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .header .tagline {
            font-size: 7pt;
            margin: 2px 0 0 0;
            color: #666;
            font-style: italic;
        }

        /* Title */
        .title {
            text-align: center;
            margin: 12px 0;
            position: relative;
            z-index: 1;
        }

        .title h1 {
            margin: 0;
            font-size: 10pt;
            font-weight: bold;
            text-decoration: underline;
            text-transform: uppercase;
        }

        /* Content paragraphs */
        .content {
            margin-bottom: 6px;
            position: relative;
            z-index: 1;
        }

        .content p {
            margin: 2px 0;
            text-align: justify;
            line-height: 1.4;
        }

        /* Party section */
        .party {
            margin-bottom: 8px;
            position: relative;
            z-index: 1;
        }

        .party-label {
            font-weight: bold;
            margin: 4px 0 2px 0;
            display: block;
        }

        .party-details {
            line-height: 1.4;
        }

        .party-details-row {
            display: flex;
            align-items: baseline;
            margin: 2px 0;
        }

        .party-details-label {
            flex: 0 0 145px;
            padding-right: 5px;
            white-space: nowrap;
        }

        .party-details-value {
            flex: 1;
        }

        /* Points section */
        .points {
            margin: 8px 0 8px 18px;
            position: relative;
            z-index: 1;
        }

        .points p {
            margin: 4px 0;
            text-align: justify;
            line-height: 1.4;
        }

        /* Statement */
        .statement {
            margin: 6px 0;
            position: relative;
            z-index: 1;
        }

        .statement p {
            margin: 0;
            font-style: italic;
            text-align: center;
            line-height: 1.4;
        }

        /* Signatures */
        .signatures {
            margin-top: 30px;
            position: relative;
            z-index: 1;
        }

        .signature-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #fff;
        }

        .signature-table td {
            width: 50%;
            vertical-align: top;
            padding: 0;
            border: 1px solid #fff;
            text-align: center;
        }

        .signature-title {
            margin: 5px 0 5px 0;
            font-weight: bold;
            font-size: 9pt;
        }

        .signature-logo-wrapper {
            text-align: center;
            margin: 0;
            padding: 5px 0;
        }

        .signature-logo {
            height: 70px;
            display: inline-block;
        }

        .signature-image {
            height: 80px;
            display: inline-block;
        }

        .signature-line {
            border: none;
            border-bottom: 1px solid #000;
            width: 70%;
            margin: 0 auto;
        }

        .signature-name {
            margin: 0 0 1px 0;
            font-weight: bold;
            font-size: 9pt;
            text-align: center;
        }

        .signature-role {
            margin: 0 0 5px 0;
            font-size: 8pt;
            text-align: center;
        }

        /* Force row 3 to start at top */
        .signature-table tr:nth-child(3) td {
            vertical-align: top;
        }

        /* Helper classes */
        .text-bold {
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="watermark">
        <img src="{{ $logoPath }}" alt="Logo">
    </div>

    <div class="pdf-container">
        {{-- Include shared agreement content --}}
        {{-- Pass ALL data variables to populate the template --}}
        @include('pdf.agreement-content', [
            'logoPath' => $logoPath,
            'signaturePath' => $signaturePath ?? null,
            'isIndonesian' => $isIndonesian ?? true,
            'formattedDate' => $formattedDate ?? null,
            'formattedTime' => $formattedTime ?? null,
            'fullName' => $fullName ?? null,
            'citizenId' => $citizenId ?? null,
            'batch' => $batch ?? null,
            'locale' => $locale ?? 'id'
        ])
    </div>
</body>

</html>
