# Roxwood Health Medical Center - Architecture

## Proyek: Sistem UI Rumah Sakit

**Versi:** 1.0.0
**Framework:** Laravel 12
**CSS:** Tailwind CSS v4
**JavaScript:** Alpine.js

---

## 📁 Struktur Proyek

```
roxwood_hospital_mc/
├── app/
│   └── Http/
│       └── Controllers/         # Controller (minimal - UI only)
│           ├── AuthController.php
│           ├── DashboardController.php
│           ├── ComponentController.php
│           ├── SettingsController.php
│           └── LanguageController.php
├── resources/
│   ├── css/
│   │   └── app.css              # Tailwind CSS v4 entry + Theme Tokens
│   ├── js/
│   │   ├── app.js               # Main entry point
│   │   ├── theme.js             # Theme controller
│   │   ├── lang.js              # Language controller
│   │   └── clock.js             # Polling clock controller
│   └── views/
│       ├── components/          # Blade Components
│       │   ├── alert.blade.php
│       │   ├── avatar.blade.php
│       │   ├── badge.blade.php
│       │   ├── button.blade.php
│       │   ├── card.blade.php
│       │   ├── container.blade.php
│       │   ├── dropdown.blade.php
│       │   ├── grid.blade.php
│       │   ├── input.blade.php
│       │   ├── modal.blade.php
│       │   ├── pagination.blade.php
│       │   ├── select.blade.php
│       │   ├── section.blade.php
│       │   ├── stat-card.blade.php
│       │   ├── table.blade.php
│       │   ├── tabs.blade.php
│       │   └── breadcrumb.blade.php
│       ├── layouts/
│       │   ├── app.blade.php      # Master layout
│       │   ├── guest.blade.php    # Auth layout
│       │   ├── navbar.blade.php
│       │   ├── sidebar.blade.php
│       │   ├── footer.blade.php
│       │   ├── header.blade.php
│       │   └── content.blade.php
│       └── pages/
│           ├── login.blade.php
│           ├── dashboard.blade.php
│           ├── components.blade.php
│           └── settings.blade.php
├── routes/
│   └── web.php                   # Web routes
├── lang/
│   ├── en/
│   │   └── messages.php
│   └── id/
│       └── messages.php
├── docs/
│   ├── ARCHITECTURE.md          # File ini
│   ├── TODO.md
│   ├── THEME_SYSTEM.md
│   ├── COMPONENT_LIBRARY.md
│   ├── RESPONSIVE_GUIDE.md
│   └── CRASH_RECOVERY_PROTOCOL.md
├── public/                      # Public assets
├── vite.config.js               # Vite configuration
├── postcss.config.js            # PostCSS configuration
├── composer.json                # PHP dependencies
└── package.json                 # Node dependencies
```

---

## 🎯 Prinsip Desain

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

## 🔧 Teknologi

| Teknologi | Versi | Penggunaan |
|-----------|-------|------------|
| Laravel | 12.11.2 | Backend Framework |
| PHP | 8.4.11 | Runtime |
| Tailwind CSS | 4.0.0 | Styling |
| Alpine.js | Latest | Interactivity |
| Vite | 7.0.7 | Asset Bundling |

---

## 🎨 Sistem Tema

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

## 🌍 Sistem Bahasa

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

## 📱 Breakpoints

| Breakpoint | Ukuran | Kolom Grid |
|------------|--------|------------|
| mobile | < 640px | 1 kolom |
| sm | ≥ 640px | 2 kolom |
| md | ≥ 768px | 2 kolom |
| lg | ≥ 1024px | 3 kolom |
| xl | ≥ 1280px | 4 kolom |

---

## 🧩 Komponen

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

### Feedback Components
- `x-alert` - Alert/notification
- `x-badge` - Badge/status indicator
- `x-avatar` - User avatar

### Data Components
- `x-table` - Data table
- `x-stat-card` - Statistics card

---

## 🚀 Development

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

## 📝 Catatan Penting

1. **JANGAN ubah struktur file tanpa persetujuan**
2. **Gunakan Blade Components, bukan inline HTML**
3. **Gunakan CSS Variables, jangan hardcode colors**
4. **Mobile-first design selalu diutamakan**
5. **Semua text harus menggunakan __('key') untuk multi-language**
