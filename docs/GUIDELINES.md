# Development Guidelines - Roxwood Health Medical Center

## 🚨 CRITICAL RULES - WAJIB DIBACA!

---

## 📋 Component Usage Rules

### ✅ WAJIB: Gunakan Komponen yang Sudah Ada
**SEMUA halaman HARUS menggunakan komponen dari `resources/views/components/`**

**DILARANG:**
- ❌ Mendesain langsung di halaman/view
- ❌ Menulis HTML/CSS raw di dalam view
- ❌ Menambahkan style inline kecuali untuk Alpine.js bindings
- ❌ Membuat komponen baru tanpa persetujuan

### ✅ Komponen yang Tersedia:

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

## 🗄️ Database Rules

### ✅ WAJIB: Gunakan Tabel & Kolom yang SUDAH ADA

**DILARANG:**
- ❌ Menambahkan tabel baru ke database
- ❌ Menambahkan kolom baru ke tabel yang sudah ada
- ❌ Mengubah struktur database yang sudah ada

### 📊 Struktur Database yang SUDAH ADA:

**Jika diperlukan backend di masa depan:**
- Gunakan migration yang sudah ada
- Ikuti struktur yang sudah ditentukan
- Tambahkan field hanya jika DISETUJUI oleh owner proyek

---

## 🔒 Backend Rules

### 🚫 DIKUNCI: Backend Implementation

**Status: TERKUNCI - TUNGGU PERINTAH**

**DILARANG:**
- ❌ Implementasi backend authentication
- ❌ Implementasi backend validation
- ❌ Implementasi database queries
- ❌ Implementasi API endpoints
- ❌ Implementasi CRUD operations

**MENUNGGU:**
- ⏳ Persetujuan dari owner proyek
- ⏳ Spesifikasi business logic
- ⏳ Spesifikasi validation rules
- ⏳ Spesifikasi API structure

### ✅ Yang Boleh Dilakukan Sekarang:
1. **UI/UX Development** - Frontend only
2. **Component Development** - Menambah komponen UI baru jika diperlukan
3. **Theme Enhancement** - Memperbaiki theme support
4. **Accessibility** - Menambah fitur aksesibilitas
5. **Responsive Design** - Memperbaiki mobile layout
6. **Documentation** - Update dokumentasi

---

## 🎨 Design Rules

### Theme Support
**SEMUA komponen/halaman WAJIB support 3 tema:**
1. **Light Theme** - Default
2. **Dark Theme** - `theme-dark`
3. **Stylis Theme** - `theme-stylis` (Teal-based)

### Class Naming Convention
Gunakan Tailwind classes dengan proper theme modifiers:

```blade
<!-- ✅ BENAR -->
<div class="bg-white dark:bg-gray-800 theme-stylis:bg-teal-50">
<div class="text-text-primary dark:text-white theme-stylis:text-gray-800">

<!-- ❌ SALAH -->
<div class="bg-white" style="background: #fff">
<div style="color: #333">
```

### Color Usage
Gunakan CSS variables yang sudah didefinisikan:

```blade
<!-- ✅ BENAR -->
<div class="bg-surface border-border text-text-primary">

<!-- ❌ SALAH -->
<div class="bg-white border-gray-200 text-gray-900">
```

---

## 🌐 Multi-Language Rules

### Translation Keys
**SEMUA text HARUS menggunakan translation keys:**

```blade
<!-- ✅ BENAR -->
<h1>{{ __('messages.dashboard') }}</h1>
<button>{{ __('messages.submit') }}</button>

<!-- ❌ SALAH -->
<h1>Dashboard</h1>
<button>Submit</button>
```

### Menambahkan Translation Key
1. Tambahkan ke `lang/en/messages.php`
2. Tambahkan ke `lang/id/messages.php`
3. Gunakan format: `__('messages.key_name')`

---

## 📱 Responsive Design Rules

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

## ♿ Accessibility Rules

### Mandatory Features
Semua halaman WAJIB mendukung:
1. **Font Size Scaling** - 75% - 150%
2. **High Contrast Mode** - Untuk pengguna dengan low vision
3. **Reduced Motion** - Untuk pengguna dengan vestibular disorders
4. **Keyboard Navigation** - Semua interaksi bisa diakses dengan keyboard
5. **Screen Reader Support** - Proper ARIA labels

### ARIA Attributes
```blade
<!-- ✅ BENAR -->
<button aria-label="Close modal" @click="close()">
    <svg>...</svg>
</button>

<!-- ❌ SALAH -->
<button @click="close()">
    <svg>...</svg>
</button>
```

---

## 🧪 Testing & Quality Assurance Rules

### 🚨 CRITICAL: Test Before Declare Complete
**SEBELUM mengatakan "selesai", WAJIB:**

1. **Uji Coba Langsung** - Test di environment yang sebenarnya
   - ✅ PDF: Generate PDF, buka file, periksa hasil
   - ✅ Form: Submit form, cek validation
   - ✅ UI: Buka di browser, test interaksi
   - ✅ Responsive: Test di mobile, tablet, desktop

2. **Jangan Asumsikan Berhasil**
   - ❌ Jangan hanya melihat code
   - ❌ Jangan mengasumsikan "seharusnya jalan"
   - ❌ Jangan katakan "selesai" tanpa testing

3. **Perbaiki Sampai Benar-Benar Berhasil**
   - ✅ Test → Gagal → Perbaiki → Test → Ulangi
   - ✅ Pastikan hasil sesuai ekspektasi user
   - ✅ Baru setelah itu katakan "selesai"

### 📋 Development Workflow

#### Step-by-Step Process:
```
1. Pahami Requirement
   ↓
2. Buat Implementasi
   ↓
3. TEST DI ENVIRONMENT YANG SEBENARNYA
   ↓
4. Jika Gagal → Perbaiki → Kembali ke Step 3
   ↓
5. Jika Berhasil → Verifikasi dengan User
   ↓
6. Baru kemudian katakan "Selesai"
```

#### Contoh Kasus PDF:
```
❌ SALAH:
- Edit code
- Katakan "selesai"
- User test → gagal → frustrasi

✅ BENAR:
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

### 🎯 Problem Solving Principles

#### 1. Think Simple, Not Complex
- ✅ Mulai dengan solusi paling sederhana
- ✅ Jika simple solution works, selesai
- ❌ Jangan langsung ke complex solution

**Contoh:**
```
Problem: Spasi terlalu tinggi di atas garis tanda tangan

❌ Overthinking:
- Coba pakai flexbox
- Coba pakai absolute positioning
- Coba pakai transform
- Coba 10 pendekatan berbeda
- Hasil: 2 jam, belum selesai

✅ Simple Solution:
- Lihat CSS: `height: 50px` pada .signature-line
- Hapus `height: 50px`
- Test: Berhasil
- Hasil: 2 menit, selesai
```

#### 2. Test Changes Incrementally
- ✅ Buat 1 perubahan kecil
- ✅ Test hasilnya
- ✅ Jika berhasil, lanjut ke perubahan berikutnya
- ❌ Jangan buat 10 perubahan sekaligus, baru test

#### 3. Use Preview/Testing Routes
Untuk fitur seperti PDF, email, dll:
```php
// ✅ BENAR: Buat route testing sementara
Route::get('/test-pdf', [Controller::class, 'testPdf']);
Route::get('/preview-feature', [Controller::class, 'previewFeature']);

// Test dulu di route ini
// Setelah berhasil, hapus route testing
```

### ⚠️ Common Mistakes to Avoid

#### 1. "Overthinking" Solutions
```
❌ "Mungkin perlu pakai complex algorithm"
❌ "Mungkin perlu refactor semua code"
❌ "Mungkin perlu tambah 10 CSS classes"

✅ "Apa problem sebenarnya?"
✅ "Solusi paling simple apa?"
✅ "Apakah ada built-in method/function?"
```

#### 2. Saying "Done" Without Testing
```
❌ "Sudah saya perbaiki" (tapi belum di-test)
❌ "Seharusnya jalan" (tapi belum dibuka di browser)
❌ "Code sudah benar" (tapi PDF belum digenerate)

✅ "Saya test, hasilnya seperti ini..."
✅ "Bisa dicek di /test-route"
✅ "Screenshot hasilnya terlampir"
```

#### 3. Being Inconsistent
```
❌ Kadang pakai component, kadang langsung HTML
❌ Kadang test, kadang tidak
❌ Kadang ikuti guidelines, kadang tidak

✅ Selalu ikuti guidelines yang sama
✅ Selalu test sebelum katakan selesai
✅ Selalu gunakan komponen yang sudah ada
```

### 📊 Quality Checklist

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

## 🔧 Code Quality Rules

### File Organization
```
resources/views/
├── components/        # Reusable components (x- prefix)
├── layouts/          # Layout templates
└── pages/            # Page views (gunakan komponen!)
```

### Naming Convention
- **Components**: `kebab-case.blade.php` dengan prefix `x-`
- **Pages**: `kebab-case.blade.php`
- **Variables**: `camelCase`

### Comment Rules
```blade
{{-- ✅ BENAR: Section comment --}}
{{-- User Profile Section --}}

{{-- ❌ SALAH: HTML comment --}}
<!-- User Profile Section -->
```

---

## 🚀 Deployment Rules

### Before Deploying
1. ✅ Test semua halaman di 3 tema
2. ✅ Test responsive design (mobile/tablet/desktop)
3. ✅ test multi-language (EN/ID)
4. ✅ Test accessibility features
5. ✅ Run `npm run build`
6. ✅ Clear view cache: `php artisan view:clear`

### Production Build
```bash
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 📝 Template untuk Halaman Baru

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

## 🚨 Emergency Contacts

Jika menemukan masalah atau butuh klarifikasi:
1. Cek `docs/TODO.md` untuk status
2. Cek `docs/COMPONENT_LIBRARY.md` untuk daftar komponen
3. Cek `docs/ARCHITECTURE.md` untuk arsitektur
4. TUNGGU perintah sebelum implementasi backend

---

*Last Updated: 2026-02-27*
*Version: 1.1.0*

---

## ⚠️ PENTING UNTUK AI/CLAUDE CLI

**Jika ini dibaca oleh AI atau Claude CLI:**

### 🚨 WAJIB DILAKUKAN:
1. **TEST SEBELUM KATAKAN "SELESAI"**
   - Generate PDF → Buka file → Periksa hasil
   - Edit form → Submit → Cek hasil
   - Ubah UI → Buka browser → Test interaksi
   - JANGAN katakan "selesai" tanpa testing

2. **PIKIRKAN SOLUSI SEDERHANA**
   - Mulai dengan solusi paling simple
   - Jangan langsung ke complex solution
   - Contoh: "Hapus height: 50px" lebih baik dari "refactor semua dengan flexbox"

3. **KONSISTEN DENGAN GUIDELINES**
   - Selalu gunakan komponen yang sudah ada
   - Selalu test sebelum katakan selesai
   - Selalu ikuti workflow yang sama

### 🚫 DILARANG:
1. **JANGAN** buat desain langsung di halaman
2. **JANGAN** tambah tabel/kolom database
3. **JANGAN** implementasi backend tanpa perintah
4. **JANGAN** katakan "selesai" tanpa testing
5. **JANGAN** overthinking solusi sederhana

### 📋 Checklist Sebelum Katakan "Selesai":
- [ ] Sudah di-test di environment nyata?
- [ ] Hasil sesuai requirement?
- [ ] Tidak ada error/glitch?
- [ ] User sudah approve?

**Pertanyaan? Tanya user dulu, jangan asumsikan. JANGAN katakan selesai sebelum testing.**

**Contoh Baik:**
✅ "Saya sudah test PDF yang di-generate. Logo muncul, margin benar. Bisa dicek di /preview-pdf/id"

**Contoh Buruk:**
❌ "Sudah saya perbaiki" (tanpa testing)
❌ "Seharusnya jalan" (tanpa buka file)
❌ "Code sudah benar" (tanpa verify)
