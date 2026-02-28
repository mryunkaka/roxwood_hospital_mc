# Roxwood Health Medical Center - Architecture

## Proyek: Sistem UI Rumah Sakit

**Versi:** 1.0.0
**Framework:** Laravel 12
**CSS:** Tailwind CSS v4
**JavaScript:** Alpine.js

---

## ðŸ“ Struktur Proyek

```
roxwood_hospital_mc/
â”œâ”€â”€ app/
â”‚   â””â”€â”€ Http/
â”‚       â””â”€â”€ Controllers/         # Controller (minimal - UI only)
â”‚           â”œâ”€â”€ AuthController.php
â”‚           â”œâ”€â”€ DashboardController.php
â”‚           â”œâ”€â”€ ComponentController.php
â”‚           â”œâ”€â”€ SettingsController.php
â”‚           â””â”€â”€ LanguageController.php
â”œâ”€â”€ resources/
â”‚   â”œâ”€â”€ css/
â”‚   â”‚   â””â”€â”€ app.css              # Tailwind CSS v4 entry + Theme Tokens
â”‚   â”œâ”€â”€ js/
â”‚   â”‚   â”œâ”€â”€ app.js               # Main entry point
â”‚   â”‚   â”œâ”€â”€ theme.js             # Theme controller
â”‚   â”‚   â”œâ”€â”€ lang.js              # Language controller
â”‚   â”‚   â””â”€â”€ clock.js             # Polling clock controller
â”‚   â””â”€â”€ views/
â”‚       â”œâ”€â”€ components/          # Blade Components
â”‚       â”‚   â”œâ”€â”€ alert.blade.php
â”‚       â”‚   â”œâ”€â”€ avatar.blade.php
â”‚       â”‚   â”œâ”€â”€ badge.blade.php
â”‚       â”‚   â”œâ”€â”€ button.blade.php
â”‚       â”‚   â”œâ”€â”€ card.blade.php
â”‚       â”‚   â”œâ”€â”€ checkbox.blade.php
â”‚       â”‚   â”œâ”€â”€ container.blade.php
â”‚       â”‚   â”œâ”€â”€ dropdown.blade.php
â”‚       â”‚   â”œâ”€â”€ file-input.blade.php
â”‚       â”‚   â”œâ”€â”€ grid.blade.php
â”‚       â”‚   â”œâ”€â”€ input.blade.php
â”‚       â”‚   â”œâ”€â”€ login-card.blade.php
â”‚       â”‚   â”œâ”€â”€ modal.blade.php
â”‚       â”‚   â”œâ”€â”€ pagination.blade.php
â”‚       â”‚   â”œâ”€â”€ select.blade.php
â”‚       â”‚   â”œâ”€â”€ section.blade.php
â”‚       â”‚   â”œâ”€â”€ signature-input.blade.php
â”‚       â”‚   â”œâ”€â”€ stat-card.blade.php
â”‚       â”‚   â”œâ”€â”€ table.blade.php
â”‚       â”‚   â”œâ”€â”€ tabs.blade.php
â”‚       â”‚   â””â”€â”€ breadcrumb.blade.php
â”‚       â”œâ”€â”€ layouts/
â”‚       â”‚   â”œâ”€â”€ app.blade.php      # Master layout
â”‚       â”‚   â”œâ”€â”€ guest.blade.php    # Auth layout
â”‚       â”‚   â”œâ”€â”€ navbar.blade.php
â”‚       â”‚   â”œâ”€â”€ sidebar.blade.php
â”‚       â”‚   â”œâ”€â”€ footer.blade.php
â”‚       â”‚   â”œâ”€â”€ header.blade.php
â”‚       â”‚   â””â”€â”€ content.blade.php
â”‚       â””â”€â”€ pages/
â”‚           â”œâ”€â”€ login.blade.php
â”‚           â”œâ”€â”€ register.blade.php
â”‚           â”œâ”€â”€ dashboard.blade.php
â”‚           â”œâ”€â”€ components.blade.php
â”‚           â”œâ”€â”€ settings.blade.php
â”‚           â””â”€â”€ preview-pdf.blade.php
â”œâ”€â”€ routes/
â”‚   â””â”€â”€ web.php                   # Web routes
â”œâ”€â”€ lang/
â”‚   â”œâ”€â”€ en/
â”‚   â”‚   â””â”€â”€ messages.php
â”‚   â””â”€â”€ id/
â”‚       â””â”€â”€ messages.php
â”œâ”€â”€ docs/
â”‚   â”œâ”€â”€ ARCHITECTURE.md          # File ini
â”‚   â”œâ”€â”€ TODO.md
â”‚   â”œâ”€â”€ GUIDELINES.md
â”‚   â”œâ”€â”€ THEME_SYSTEM.md
â”‚   â”œâ”€â”€ COMPONENT_LIBRARY.md
â”‚   â”œâ”€â”€ RESPONSIVE_GUIDE.md
â”‚   â”œâ”€â”€ CRASH_RECOVERY_PROTOCOL.md
â”‚   â””â”€â”€ PDF_SIGNATURE_GUIDE.md   # PDF & Signature documentation
â”œâ”€â”€ public/                      # Public assets
â”œâ”€â”€ vite.config.js               # Vite configuration
â”œâ”€â”€ postcss.config.js            # PostCSS configuration
â”œâ”€â”€ composer.json                # PHP dependencies
â””â”€â”€ package.json                 # Node dependencies
```

---

## ðŸŽ¯ Prinsip Desain

### 1. UI First Approach
- Tidak ada backend logic
- Tidak ada database logic
- Tidak ada autentikasi real
- Login redirect langsung ke dashboard

### 2. Mobile-First Design
- Semua breakpoint didesain dari mobile ke desktop
- Sidebar collapsible pada mobile
- Drawer navigation untuk small screens

### 3. Theme System
- 3 Theme: Light, Dark, Stylis
- CSS Variables untuk design tokens
- Alpine.js untuk theme switching

### 4. Component-Based
- Blade Components untuk reusable UI
- Props-based configuration
- Slot-based content injection

---

## ðŸ”§ Teknologi

| Teknologi | Versi | Penggunaan |
|-----------|-------|------------|
| Laravel | 12.11.2 | Backend Framework |
| PHP | 8.4.11 | Runtime |
| Tailwind CSS | 4.0.0 | Styling |
| Alpine.js | Latest | Interactivity |
| Vite | 7.0.7 | Asset Bundling |

---

## ðŸŽ¨ Sistem Tema

### Design Tokens (CSS Variables)

```css
:root {
    /* Warna */
    --color-primary: #3b82f6;
    --color-secondary: #64748b;
    --color-success: #22c55e;
    --color-danger: #ef4444;
    --color-warning: #f59e0b;
    --color-info: #0ea5e9;

    /* Surface */
    --color-background: #ffffff;
    --color-surface: #f8fafc;
    --color-border: #e2e8f0;

    /* Text */
    --color-text-primary: #0f172a;
    --color-text-secondary: #475569;
}
```

### Theme Classes

- `.theme-light` - Theme default
- `.theme-dark` - Dark mode
- `.theme-stylis` - Modern gradient soft UI

---

## ðŸŒ Sistem Bahasa

### Dukungan Bahasa
- English (en)
- Bahasa Indonesia (id)

### Implementasi
```php
// Gunakan di Blade
{{ __('messages.key') }}

// Switch language
Route::get('/lang/{code}', LanguageController::class);
```

---

## ðŸ“± Breakpoints

| Breakpoint | Ukuran | Kolom Grid |
|------------|--------|------------|
| mobile | < 640px | 1 kolom |
| sm | â‰¥ 640px | 2 kolom |
| md | â‰¥ 768px | 2 kolom |
| lg | â‰¥ 1024px | 3 kolom |
| xl | â‰¥ 1280px | 4 kolom |

---

## ðŸ§© Komponen

### Layout Components
- `x-container` - Container dengan max-width
- `x-section` - Section wrapper
- `x-card` - Card dengan opsi title/subtitle
- `x-grid` - Grid responsive

### Navigation Components
- `x-navbar` - Top navigation bar
- `x-sidebar` - Sidebar navigation
- `x-breadcrumb` - Breadcrumb navigation
- `x-tabs` - Tab navigation
- `x-pagination` - Pagination controls

### Form Components
- `x-input` - Input field dengan label
- `x-select` - Dropdown select
- `x-button` - Button dengan berbagai variant
- `x-checkbox` - Checkbox dengan label
- `x-file-input` - File upload dengan drag & drop
- `x-signature-input` - Signature input (digital/upload)

### Feedback Components
- `x-alert` - Alert/notification
- `x-badge` - Badge/status indicator
- `x-avatar` - User avatar
- `x-modal` - Modal/dialog
- `x-dropdown` - Dropdown menu
- `x-toast` - Toast notifications

### Data Components
- `x-table` - Data table
- `x-stat-card` - Statistics card

---

## ðŸš€ Development

### Install Dependencies
```bash
composer install
npm install
```

### Run Development Server
```bash
npm run dev
php artisan serve
```

### Build for Production
```bash
npm run build
```

---

## ðŸ“„ PDF Generation System

### Overview
Sistem pembuatan surat perjanjian kerja (Agreement Letter) dengan dukungan:
- Bilingual (Indonesia & Inggris)
- Digital signature integration
- Base64 image encoding untuk reliable PDF rendering

### Key Files
```
resources/views/pdf/
â”œâ”€â”€ agreement.blade.php              # PDF template dengan styles
â””â”€â”€ agreement-content.blade.php      # Shared content (PDF + preview)

resources/views/pages/
â””â”€â”€ preview-pdf.blade.php             # Preview PDF form page

app/Http/Controllers/AuthController.php
â”œâ”€â”€ generateAgreementPDF()           # Private: Generate PDF
â”œâ”€â”€ previewPdfIndonesian()           # Public: Preview Indonesian
â””â”€â”€ previewPdfEnglish()              # Public: Preview English
```

### Dependencies
- **barryvdh/laravel-dompdf**: PDF generation
- **signature_pad.js** (v4.1.7): Digital signature pad

### Routes
- `/preview-pdf/id` - Preview PDF Indonesian (GET/POST)
- `/preview-pdf/en` - Preview PDF English (GET/POST)

### Features
1. **PDF Styling**: A4 portrait, Times New Roman, 10pt
2. **Signature Layout**: 3-row table structure (Title, Signature/Logo, Name)
3. **Bilingual**: Full Indonesian & English support
4. **Auto-format**: Title Case names, UPPERCASE citizen IDs
5. **Signature Processing**: Transparency preservation, background removal

### Storage Structure
```
public/storage/user_docs/
â””â”€â”€ user_{id}-{sanitized_name}-{citizen_id}/
    â”œâ”€â”€ signature.png              # Digital/uploaded signature
    â”œâ”€â”€ file_ktp.jpg               # KTP (compressed)
    â”œâ”€â”€ file_skb.jpg               # SKB (compressed)
    â”œâ”€â”€ file_sim.jpg               # SIM (optional)
    â”œâ”€â”€ profile_photo.jpg          # Profile photo (optional)
    â””â”€â”€ agreement_letter.pdf       # Generated agreement PDF
```

**Full Documentation**: Lihat `docs/PDF_SIGNATURE_GUIDE.md`

---

## ðŸ“ Catatan Penting

1. **JANGAN ubah struktur file tanpa persetujuan**
2. **Gunakan Blade Components, bukan inline HTML**
3. **Gunakan CSS Variables, jangan hardcode colors**
4. **Mobile-first design selalu diutamakan**
5. **Semua text harus menggunakan __('key') untuk multi-language**
