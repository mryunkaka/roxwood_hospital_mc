# Development Guidelines - Roxwood Health Medical Center

## ðŸš¨ CRITICAL RULES - WAJIB DIBACA!

---

## ðŸ“‹ Component Usage Rules

### âœ… WAJIB: Gunakan Komponen yang Sudah Ada
**SEMUA halaman HARUS menggunakan komponen dari `resources/views/components/`**

**DILARANG:**
- âŒ Mendesain langsung di halaman/view
- âŒ Menulis HTML/CSS raw di dalam view
- âŒ Menambahkan style inline kecuali untuk Alpine.js bindings
- âŒ Membuat komponen baru tanpa persetujuan

### âœ… Komponen yang Tersedia:

#### Layout Components
- `x-container` - Container dengan responsive width
- `x-section` - Section wrapper dengan margin
- `x-card` - Card component
- `x-grid` - Grid system

#### Form Components
- `x-input` - Text, email, password, number inputs
- `x-select` - Dropdown select
- `x-button` - Button dengan berbagai variant
- `x-checkbox` - Checkbox dengan label (BARU!)
- `x-file-input` - File upload component

#### Navigation Components
- `x-breadcrumb` - Breadcrumb navigation
- `x-tabs` - Tab navigation
- `x-pagination` - Pagination

#### Feedback Components
- `x-alert` - Alert/notification messages
- `x-badge` - Badge/label
- `x-avatar` - User avatar

#### Data Components
- `x-table` - Data table
- `x-stat-card` - Statistics card
- `x-skeleton` - Skeleton loading
- `x-progress` - Progress bar
- `x-chart` - Chart.js integration

#### Overlay Components
- `x-modal` - Modal/dialog
- `x-dropdown` - Dropdown menu
- `x-tooltip` - Tooltip
- `x-popover` - Popover
- `x-toast` - Toast notifications

#### Advanced Components
- `x-file-upload` - Drag & drop file upload
- `x-date-time-picker` - Date/time picker dengan calendar

---

## ðŸ—„ï¸ Database Rules

### âœ… WAJIB: Gunakan Tabel & Kolom yang SUDAH ADA

**DILARANG:**
- âŒ Menambahkan tabel baru ke database
- âŒ Menambahkan kolom baru ke tabel yang sudah ada
- âŒ Mengubah struktur database yang sudah ada

### ðŸ“Š Struktur Database yang SUDAH ADA:

**Jika diperlukan backend di masa depan:**
- Gunakan migration yang sudah ada
- Ikuti struktur yang sudah ditentukan
- Tambahkan field hanya jika DISETUJUI oleh owner proyek

---

## ðŸ”’ Backend Rules

### ðŸš« DIKUNCI: Backend Implementation

**Status: TERKUNCI - TUNGGU PERINTAH**

**DILARANG:**
- âŒ Implementasi backend authentication
- âŒ Implementasi backend validation
- âŒ Implementasi database queries
- âŒ Implementasi API endpoints
- âŒ Implementasi CRUD operations

**MENUNGGU:**
- â³ Persetujuan dari owner proyek
- â³ Spesifikasi business logic
- â³ Spesifikasi validation rules
- â³ Spesifikasi API structure

### âœ… Yang Boleh Dilakukan Sekarang:
1. **UI/UX Development** - Frontend only
2. **Component Development** - Menambah komponen UI baru jika diperlukan
3. **Theme Enhancement** - Memperbaiki theme support
4. **Accessibility** - Menambah fitur aksesibilitas
5. **Responsive Design** - Memperbaiki mobile layout
6. **Documentation** - Update dokumentasi

---

## ðŸŽ¨ Design Rules

### Theme Support
**SEMUA komponen/halaman WAJIB support 3 tema:**
1. **Light Theme** - Default
2. **Dark Theme** - `theme-dark`
3. **Stylis Theme** - `theme-stylis` (Teal-based)

### Icons (WAJIB)
**DILARANG menggunakan emoticon/emoji di UI.**
- Gunakan **Heroicons (SVG)** yang konsisten dengan desain sistem.

### Class Naming Convention
Gunakan Tailwind classes dengan proper theme modifiers:

```blade
<!-- âœ… BENAR -->
<div class="bg-white dark:bg-gray-800 theme-stylis:bg-teal-50">
<div class="text-text-primary dark:text-white theme-stylis:text-gray-800">

<!-- âŒ SALAH -->
<div class="bg-white" style="background: #fff">
<div style="color: #333">
```

### Color Usage
Gunakan CSS variables yang sudah didefinisikan:

```blade
<!-- âœ… BENAR -->
<div class="bg-surface border-border text-text-primary">

<!-- âŒ SALAH -->
<div class="bg-white border-gray-200 text-gray-900">
```

---

## ðŸŒ Multi-Language Rules

### Translation Keys
**SEMUA text HARUS menggunakan translation keys:**

```blade
<!-- âœ… BENAR -->
<h1>{{ __('messages.dashboard') }}</h1>
<button>{{ __('messages.submit') }}</button>

<!-- âŒ SALAH -->
<h1>Dashboard</h1>
<button>Submit</button>
```

### Menambahkan Translation Key
1. Tambahkan ke `lang/en/messages.php`
2. Tambahkan ke `lang/id/messages.php`
3. Gunakan format: `__('messages.key_name')`

---

## ðŸ“± Responsive Design Rules

### Mobile-First Approach
```blade
<!-- Mobile: 1 column, Tablet: 2 columns, Desktop: 3-4 columns -->
<x-grid :cols="1" :smCols="2" :lgCols="3">
    <!-- Content -->
</x-grid>
```

### Breakpoints
- Mobile: `default` (< 640px)
- Tablet: `sm` (640px - 1024px)
- Desktop: `lg` (>= 1024px)

---

## â™¿ Accessibility Rules

### Mandatory Features
Semua halaman WAJIB mendukung:
1. **Font Size Scaling** - 75% - 150%
2. **High Contrast Mode** - Untuk pengguna dengan low vision
3. **Reduced Motion** - Untuk pengguna dengan vestibular disorders
4. **Keyboard Navigation** - Semua interaksi bisa diakses dengan keyboard
5. **Screen Reader Support** - Proper ARIA labels

### ARIA Attributes
```blade
<!-- âœ… BENAR -->
<button aria-label="Close modal" @click="close()">
    <svg>...</svg>
</button>

<!-- âŒ SALAH -->
<button @click="close()">
    <svg>...</svg>
</button>
```

---

## ðŸ§ª Testing & Quality Assurance Rules

### ðŸš¨ CRITICAL: Test Before Declare Complete
**SEBELUM mengatakan "selesai", WAJIB:**

1. **Uji Coba Langsung** - Test di environment yang sebenarnya
   - âœ… PDF: Generate PDF, buka file, periksa hasil
   - âœ… Form: Submit form, cek validation
   - âœ… UI: Buka di browser, test interaksi
   - âœ… Responsive: Test di mobile, tablet, desktop

2. **Jangan Asumsikan Berhasil**
   - âŒ Jangan hanya melihat code
   - âŒ Jangan mengasumsikan "seharusnya jalan"
   - âŒ Jangan katakan "selesai" tanpa testing

3. **Perbaiki Sampai Benar-Benar Berhasil**
   - âœ… Test â†’ Gagal â†’ Perbaiki â†’ Test â†’ Ulangi
   - âœ… Pastikan hasil sesuai ekspektasi user
   - âœ… Baru setelah itu katakan "selesai"

### ðŸ“‹ Development Workflow

#### Step-by-Step Process:
```
1. Pahami Requirement
   â†“
2. Buat Implementasi
   â†“
3. TEST DI ENVIRONMENT YANG SEBENARNYA
   â†“
4. Jika Gagal â†’ Perbaiki â†’ Kembali ke Step 3
   â†“
5. Jika Berhasil â†’ Verifikasi dengan User
   â†“
6. Baru kemudian katakan "Selesai"
```

#### Contoh Kasus PDF:
```
âŒ SALAH:
- Edit code
- Katakan "selesai"
- User test â†’ gagal â†’ frustrasi

âœ… BENAR:
- Edit code
- Generate PDF
- Buka file PDF
- Periksa apakah logo muncul? Ya
- Periksa apakah margin benar? Tidak
- Perbaiki margin
- Generate PDF lagi
- Periksa lagi? Ya sudah benar
- Baru katakan "selesai"
```

### ðŸŽ¯ Problem Solving Principles

#### 1. Think Simple, Not Complex
- âœ… Mulai dengan solusi paling sederhana
- âœ… Jika simple solution works, selesai
- âŒ Jangan langsung ke complex solution

**Contoh:**
```
Problem: Spasi terlalu tinggi di atas garis tanda tangan

âŒ Overthinking:
- Coba pakai flexbox
- Coba pakai absolute positioning
- Coba pakai transform
- Coba 10 pendekatan berbeda
- Hasil: 2 jam, belum selesai

âœ… Simple Solution:
- Lihat CSS: `height: 50px` pada .signature-line
- Hapus `height: 50px`
- Test: Berhasil
- Hasil: 2 menit, selesai
```

#### 2. Test Changes Incrementally
- âœ… Buat 1 perubahan kecil
- âœ… Test hasilnya
- âœ… Jika berhasil, lanjut ke perubahan berikutnya
- âŒ Jangan buat 10 perubahan sekaligus, baru test

#### 3. Use Preview/Testing Routes
Untuk fitur seperti PDF, email, dll:
```php
// âœ… BENAR: Buat route testing sementara
Route::get('/test-pdf', [Controller::class, 'testPdf']);
Route::get('/preview-feature', [Controller::class, 'previewFeature']);

// Test dulu di route ini
// Setelah berhasil, hapus route testing
```

### âš ï¸ Common Mistakes to Avoid

#### 1. "Overthinking" Solutions
```
âŒ "Mungkin perlu pakai complex algorithm"
âŒ "Mungkin perlu refactor semua code"
âŒ "Mungkin perlu tambah 10 CSS classes"

âœ… "Apa problem sebenarnya?"
âœ… "Solusi paling simple apa?"
âœ… "Apakah ada built-in method/function?"
```

#### 2. Saying "Done" Without Testing
```
âŒ "Sudah saya perbaiki" (tapi belum di-test)
âŒ "Seharusnya jalan" (tapi belum dibuka di browser)
âŒ "Code sudah benar" (tapi PDF belum digenerate)

âœ… "Saya test, hasilnya seperti ini..."
âœ… "Bisa dicek di /test-route"
âœ… "Screenshot hasilnya terlampir"
```

#### 3. Being Inconsistent
```
âŒ Kadang pakai component, kadang langsung HTML
âŒ Kadang test, kadang tidak
âŒ Kadang ikuti guidelines, kadang tidak

âœ… Selalu ikuti guidelines yang sama
âœ… Selalu test sebelum katakan selesai
âœ… Selalu gunakan komponen yang sudah ada
```

### ðŸ“Š Quality Checklist

Sebelum mengatakan "selesai", pastikan:

- [ ] Sudah di-test di environment yang sebenarnya
- [ ] Hasil sesuai dengan requirement user
- [ ] Tidak ada error di console
- [ ] Tidak ada visual glitch
- [ ] Responsive di mobile/tablet/desktop
- [ ] Support semua tema (light/dark/stylis)
- [ ] Translation keys ada untuk EN dan ID
- [ ] User sudah melihat hasil dan approve

---

## ðŸ”§ Code Quality Rules

### File Organization
```
resources/views/
â”œâ”€â”€ components/        # Reusable components (x- prefix)
â”œâ”€â”€ layouts/          # Layout templates
â””â”€â”€ pages/            # Page views (gunakan komponen!)
```

### Naming Convention
- **Components**: `kebab-case.blade.php` dengan prefix `x-`
- **Pages**: `kebab-case.blade.php`
- **Variables**: `camelCase`

### Comment Rules
```blade
{{-- âœ… BENAR: Section comment --}}
{{-- User Profile Section --}}

{{-- âŒ SALAH: HTML comment --}}
<!-- User Profile Section -->
```

---

## ðŸš€ Deployment Rules

### Before Deploying
1. âœ… Test semua halaman di 3 tema
2. âœ… Test responsive design (mobile/tablet/desktop)
3. âœ… test multi-language (EN/ID)
4. âœ… Test accessibility features
5. âœ… Run `npm run build`
6. âœ… Clear view cache: `php artisan view:clear`

### Production Build
```bash
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## ðŸ“ Template untuk Halaman Baru

```blade
@extends('layouts.app')

@section('title', __('messages.page_name'))

@section('page-title', __('messages.page_title'))
@section('page-description', __('messages.page_description'))

@section('content')
{{-- Gunakan KOMPONEN untuk SEMUA UI elements --}}
<x-section>
    <x-card>
        {{-- Content di sini - gunakan komponen x- --}}
    </x-card>
</x-section>
@endsection
```

---

## ðŸš¨ Emergency Contacts

Jika menemukan masalah atau butuh klarifikasi:
1. Cek `docs/TODO.md` untuk status
2. Cek `docs/COMPONENT_LIBRARY.md` untuk daftar komponen
3. Cek `docs/ARCHITECTURE.md` untuk arsitektur
4. TUNGGU perintah sebelum implementasi backend

---

*Last Updated: 2026-02-27*
*Version: 1.1.0*

---

## âš ï¸ PENTING UNTUK AI/CLAUDE CLI

**Jika ini dibaca oleh AI atau Claude CLI:**

### ðŸš¨ WAJIB DILAKUKAN:
1. **TEST SEBELUM KATAKAN "SELESAI"**
   - Generate PDF â†’ Buka file â†’ Periksa hasil
   - Edit form â†’ Submit â†’ Cek hasil
   - Ubah UI â†’ Buka browser â†’ Test interaksi
   - JANGAN katakan "selesai" tanpa testing

2. **PIKIRKAN SOLUSI SEDERHANA**
   - Mulai dengan solusi paling simple
   - Jangan langsung ke complex solution
   - Contoh: "Hapus height: 50px" lebih baik dari "refactor semua dengan flexbox"

3. **KONSISTEN DENGAN GUIDELINES**
   - Selalu gunakan komponen yang sudah ada
   - Selalu test sebelum katakan selesai
   - Selalu ikuti workflow yang sama

### ðŸš« DILARANG:
1. **JANGAN** buat desain langsung di halaman
2. **JANGAN** tambah tabel/kolom database
3. **JANGAN** implementasi backend tanpa perintah
4. **JANGAN** katakan "selesai" tanpa testing
5. **JANGAN** overthinking solusi sederhana

### ðŸ“‹ Checklist Sebelum Katakan "Selesai":
- [ ] Sudah di-test di environment nyata?
- [ ] Hasil sesuai requirement?
- [ ] Tidak ada error/glitch?
- [ ] User sudah approve?

**Pertanyaan? Tanya user dulu, jangan asumsikan. JANGAN katakan selesai sebelum testing.**

**Contoh Baik:**
âœ… "Saya sudah test PDF yang di-generate. Logo muncul, margin benar. Bisa dicek di /preview-pdf/id"

**Contoh Buruk:**
âŒ "Sudah saya perbaiki" (tanpa testing)
âŒ "Seharusnya jalan" (tanpa buka file)
âŒ "Code sudah benar" (tanpa verify)
