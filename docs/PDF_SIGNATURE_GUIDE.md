# PDF & Signature Guide - Roxwood Health Medical Center

## 📄 PDF Agreement Letter System

---

## Overview

Sistem pembuatan surat perjanjian kerja (Agreement Letter) dalam format PDF dengan dukungan bilingual (Indonesia & Inggris) dan integrasi tanda tangan digital.

---

## 🗂️ File Structure

```
resources/views/pdf/
├── agreement.blade.php              # PDF main template with styles
└── agreement-content.blade.php      # Shared content (PDF + Preview)

resources/views/components/
└── signature-input.blade.php         # Signature input component

resources/views/pages/
└── preview-pdf.blade.php             # Preview PDF form page

app/Http/Controllers/
└── AuthController.php                # PDF generation methods

database/migrations/
└── 2026_02_27_021837_add_ttd_to_user_rh_table.php  # TTD column migration
```

---

## 🎨 PDF Styling

### Page Layout
- **Size**: A4 portrait
- **Margins**: 18mm (top), 25mm (sides), 15mm (bottom)
- **Font**: Times New Roman, 10pt
- **Line Height**: 1.4

### Signature Styling
```css
.signature-image {
    height: 80px;          /* User signature height */
    display: inline-block;
}

.signature-logo {
    height: 70px;          /* Hospital logo height */
    display: inline-block;
}

.signature-name {
    margin: 0 0 1px 0;     /* Minimal gap to signature line */
    font-weight: bold;
    font-size: 9pt;
}
```

### Signature Table Layout (3 Rows)

**Row 1**: Titles (Pihak Kedua, | Pihak Pertama,)
**Row 2**: Signature Image | Hospital Logo
**Row 3**: Name Line & Name | Management Line & Name

---

## ✍️ Signature Input Component

### Usage

```blade
<x-signature-input
    name="signature"
    :label="__('messages.signature')"
    :dataTranslateLabel="'signature'"
    :required="true"
    :dataTranslateHint="'signature_hint'"
/>
```

### Features

1. **Digital Signature** (Default)
   - Canvas-based drawing with signature_pad.js
   - Touch/stylus support
   - Black pen color
   - Clear button

2. **Upload Signature**
   - File upload (PNG, JPEG, JPG)
   - Automatic background removal
   - White/near-white pixel transparency

### Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `name` | string | 'signature' | Input name attribute |
| `label` | string | null | Label text |
| `dataTranslateLabel` | string | null | Translation key for label |
| `required` | boolean | false | Required field validation |
| `dataTranslateHint` | string | null | Translation key for hint |

### Signature Processing

#### Digital Signature (Canvas)
```php
// In AuthController@register()
$signatureImage = imagecreatefromstring(base64_decode($signatureData));
imagepalettetotruecolor($signatureImage);
imagealphablending($signatureImage, false);
imagesavealpha($signatureImage, true);
imagepng($signatureImage, $signaturePath, 9);
```

#### Uploaded Signature
```php
// Background removal threshold: 245 (very strict)
// Only pure white pixels (245-255) are made transparent
// Preserves dark/gray signature strokes
if ($r > 245 && $g > 245 && $b > 245) {
    imagesetpixel($image, $x, $y, imagecolorallocatealpha($image, 255, 255, 255, 127));
}
```

---

## 🌐 Preview PDF Routes

### Indonesian
- **Route**: `/preview-pdf/id`
- **Name**: `preview.pdf.id`
- **Controller**: `AuthController@previewPdfIndonesian`
- **Methods**: GET (show form), POST (generate PDF)

### English
- **Route**: `/preview-pdf/en`
- **Name**: `preview.pdf.en`
- **Controller**: `AuthController@previewPdfEnglish`
- **Methods**: GET (show form), POST (generate PDF)

### Form Fields (Required)

| Field | Validation | Format |
|-------|-----------|--------|
| `full_name` | required|string|max:100 | Title Case |
| `batch` | required|integer|min:1|max:26 | Integer |
| `citizen_id` | required|string|max:30 | UPPERCASE |
| `signature_data` | required|string | Base64 PNG |

---

## 🔧 AuthController Methods

### generateAgreementPDF()

Private method untuk generate PDF agreement letter.

```php
private function generateAgreementPDF(
    $data,              // User data
    $savedFiles,        // File paths
    $isIndonesian,      // Language flag
    $locale,            // 'id' or 'en'
    $signatureBase64Override = null  // Optional signature
)
```

**Returns**: PDF binary data

**Parameters Used**:
- `full_name`: Display name
- `citizen_id`: Citizenship ID
- `batch`: Batch number
- `signature_base64` OR `savedFiles['signature']`: Signature image

### previewPdfIndonesian()

Handle preview PDF untuk bahasa Indonesia.

```php
public function previewPdfIndonesian(Request $request)
```

**GET**: Return form view (`pages.preview-pdf`)
**POST**: Validate, generate PDF, stream to browser

### previewPdfEnglish()

Handle preview PDF untuk bahasa Inggris.

```php
public function previewPdfEnglish(Request $request)
```

**GET**: Return form view (`pages.preview-pdf`)
**POST**: Validate, generate PDF, stream to browser

---

## 📝 Translation Keys

### English (`lang/en/messages.php`)

```php
'signature' => 'Signature',
'signature_digital' => 'Digital Signature',
'signature_upload' => 'Upload Signature Photo',
'signature_clear' => 'Clear',
'signature_required' => 'Signature is required',
'signature_hint' => 'Draw your signature or upload a photo',
'preview_pdf_title' => 'PDF Preview',
'preview_pdf_subtitle' => 'Fill in the form below to generate a preview of the agreement letter',
'preview_pdf_generate' => 'Generate PDF',
```

### Indonesian (`lang/id/messages.php`)

```php
'signature' => 'Tanda Tangan',
'signature_digital' => 'Tanda Tangan Digital',
'signature_upload' => 'Upload Foto Tanda Tangan',
'signature_clear' => 'Hapus',
'signature_required' => 'Tanda tangan wajib diisi',
'signature_hint' => 'Gambar tanda tangan atau upload foto',
'preview_pdf_title' => 'Preview PDF',
'preview_pdf_subtitle' => 'Isi formulir di bawah untuk membuat preview surat perjanjian',
'preview_pdf_generate' => 'Buat PDF',
```

---

## 🗄️ Database Schema

### user_rh Table

| Column | Type | Nullable | Description |
|--------|------|----------|-------------|
| `signature` | string | YES | Path to signature image file |
| `ttd` | string | YES | Additional signature column |

### Storage Path

```
public/storage/user_docs/
└── user_{id}-{sanitized_name}-{citizen_id}/
    ├── signature.png          # Signature with transparency
    ├── file_ktp.jpg           # KTP (compressed)
    ├── file_skb.jpg           # SKB (compressed)
    ├── file_sim.jpg           # SIM (compressed, optional)
    ├── profile_photo.jpg      # Profile photo (optional)
    └── agreement_letter.pdf   # Generated agreement PDF
```

---

## 🐛 Known Issues & Solutions

### Issue 1: Signature Appears Red/Black
**Cause**: Pen color not set correctly or background removal too aggressive

**Solution**:
```javascript
// In signature-input.blade.php
const signaturePad = new SignaturePad(canvas, {
    backgroundColor: 'rgba(255, 255, 255, 0)',
    penColor: 'black',  // Ensure black color
    // ...
});
```

### Issue 2: Signature Not Showing in PDF
**Cause**: Base64 encoding issue or path problem

**Solution**:
```php
// Use base64 for reliable PDF rendering
$signatureBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($path));
```

### Issue 3: Signature Stroke Disappears
**Cause**: Background removal threshold too low

**Solution**: Use threshold 245 (only remove pure white)
```php
if ($r > 245 && $g > 245 && $b > 245) {
    // Make transparent
}
```

---

## 📊 PDF Generation Flow

```
User Submits Form
        ↓
Validate Input (name, batch, citizen_id, signature)
        ↓
Format Data (Title Case, UPPERCASE)
        ↓
Process Signature (base64 → PNG with transparency)
        ↓
Load PDF Template (agreement.blade.php)
        ↓
Include Shared Content (agreement-content.blade.php)
        ↓
Convert Images to Base64 (logo, signature)
        ↓
Generate PDF with domPDF
        ↓
Stream to Browser (inline preview)
```

---

## 🔍 Testing Checklist

- [ ] Digital signature drawing works
- [ ] Signature upload works
- [ ] Background removal preserves signature strokes
- [ ] Signature appears black (not red)
- [ ] PDF shows signature correctly
- [ ] Signature size is appropriate (80px height)
- [ ] Signature aligns with name text
- [ ] Form validation works (all fields required)
- [ ] Indonesian PDF generates correctly
- [ ] English PDF generates correctly
- [ ] Date/time formatting correct for each language

---

## 🚀 Quick Test

```bash
# Test Indonesian Preview
http://localhost:8000/preview-pdf/id

# Test English Preview
http://localhost:8000/preview-pdf/en

# Test Registration with Signature
http://localhost:8000/register
```

---

*Last Updated: 2026-02-27*
