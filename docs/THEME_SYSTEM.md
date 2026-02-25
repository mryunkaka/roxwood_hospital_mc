# Theme System Documentation

## Overview

Sistem tema Roxwood Health Medical Center menggunakan CSS Variables + Alpine.js untuk memberikan 3 tema yang berbeda:
1. **Light** - Tema default bersih
2. **Dark** - Tema gelap untuk kenyamanan mata
3. **Stylis** - Modern gradient soft UI dengan glassmorphism

---

## 🎨 Design Tokens

### Color Palette

#### Primary Colors
```
Primary-50:  #eff6ff
Primary-100: #dbeafe
Primary-200: #bfdbfe
Primary-300: #93c5fd
Primary-400: #60a5fa
Primary-500: #3b82f6  (Main)
Primary-600: #2563eb
Primary-700: #1d4ed8
Primary-800: #1e40af
Primary-900: #1e3a8a
```

#### Semantic Colors
```
Success: Green (#22c55e)
Danger:  Red (#ef4444)
Warning: Amber (#f59e0b)
Info:    Sky Blue (#0ea5e9)
```

### Surface Colors (Light Theme)
```
Background: #f8fafc (slate-50) - Light gray background
Surface:    #ffffff (white) - Card/surface background
Border:     #e2e8f0 (slate-200)
```

### Surface Colors (Dark Theme)
```
Background: #0a0f1a - Deep dark blue-gray (NOT just cards)
Surface:    #151e2e - Slightly lighter for cards
Border:     #253345 - Dark borders
```

### Surface Colors (Stylis Theme)
```
Background: Linear gradient (soft teal/mint tones - Medical appropriate)
            - No pure white or black
            - Soft gradient with teal tones
            - Colors: #e0f2f1, #b2dfdb, #99d5c9, #ccfbf1
Surface:    rgba(255, 255, 255, 0.85) - Semi-transparent white
Border:     rgba(153, 213, 201, 0.3) - Soft teal borders
```

**Update History (February 2025):**
- Changed Stylis theme from purple/lavender to soft teal/mint green for medical appropriateness
- Updated theme switcher buttons to use solid colors (blue-500, gray-900, teal-500) for better visibility
- Inactive state: `text-gray-600` for all themes
- Hover state: Solid colored background with white text for all themes
- Active state: Solid colored background with shadow for all themes

### Typography
```
Text Primary:   #0f172a (light) / #f1f5f9 (dark)
Text Secondary: #475569 (light) / #cbd5e1 (dark)
Text Tertiary:  #94a3b8 (both)
```

### Logo & Brand Colors

Logo menggunakan CSS variables yang berubah sesuai tema:

#### Light Theme
```css
--color-logo-icon: #ffffff;      /* Icon putih */
--color-logo-bg-from: #3b82f6;   /* Gradient biru (start) */
--color-logo-bg-to: #1d4ed8;     /* Gradient biru (end) */
```

#### Dark Theme
```css
--color-logo-icon: #ffffff;      /* Icon putih */
--color-logo-bg-from: #60a5fa;   /* Gradient biru muda (start) */
--color-logo-bg-to: #3b82f6;     /* Gradient biru (end) */
```

#### Stylis Theme
```css
--color-logo-icon: #ffffff;      /* Icon putih */
--color-logo-bg-from: #14b8a6;   /* Gradient teal (start) */
--color-logo-bg-to: #0d9488;     /* Gradient teal (end) */
```

---

## 🌗 Theme Implementation

### 1. HTML Structure

Theme diterapkan pada elemen `<html>`:

```html
<html class="theme-light">     <!-- Light theme -->
<html class="theme-dark">      <!-- Dark theme -->
<html class="theme-stylis">    <!-- Stylis theme -->
```

Dark + Stylis combination:
```html
<html class="theme-stylis theme-dark">
```

### 2. CSS Variables Definition

```css
:root {
    --color-primary: #3b82f6;
    --color-surface: #f8fafc;
    --color-text-primary: #0f172a;
    /* ... more tokens */
}

.theme-dark {
    --color-surface: #1e293b;
    --color-text-primary: #f1f5f9;
    /* ... override tokens */
}

.theme-stylis {
    --color-surface: rgba(255, 255, 255, 0.8);
    /* ... gradient & glass effect */
}
```

### 3. Tailwind Theme Integration

```css
@theme {
    --color-surface: var(--color-surface);
    --color-text-primary: var(--color-text-primary);
    /* ... map CSS vars to Tailwind */
}
```

### 4. Component Usage

```blade
<!-- Otomatis berubah sesuai theme -->
<div class="bg-surface text-text-primary">
    Content follows theme automatically
</div>
```

---

## 🏢 Brand Components

### x-brand-logo

Komponen logo brand yang reusable untuk menampilkan logo Roxwood dengan icon dan teks.

```blade
<!-- Default size (md) -->
<x-brand-logo />

<!-- Large size -->
<x-brand-logo size="lg" />

<!-- Small size, tanpa teks -->
<x-brand-logo size="sm" :showText="false" />

<!-- Custom text styling -->
<x-brand-logo textClass="text-primary-500" />
```

**Props:**
- `size`: `'sm'` | `'md'` | `'lg'` (default: `'md'`)
- `showText`: boolean (default: `true`)
- `textClass`: string untuk custom styling pada teks
- `class`: string untuk additional container classes

**Size Reference:**
- `sm`: Container 40x40px, icon 20x20px, text 1.25rem
- `md`: Container 56x56px, icon 32x32px, text 1.875rem
- `lg`: Container 64x64px, icon 40x40px, text 2.25rem

### x-page-header

Komponen header halaman dengan logo, judul, dan deskripsi yang konsisten.

```blade
<!-- Dengan logo default -->
<x-page-header
    title="Login"
    subtitle="Masuk ke akun Anda"
/>

<!-- Tanpa logo -->
<x-page-header
    title="Dashboard"
    :showLogo="false"
/>

<!-- Logo size berbeda -->
<x-page-header
    title="Welcome"
    logoSize="lg"
/>
```

**Props:**
- `title`: string - Judul halaman (required)
- `subtitle`: string | null - Deskripsi halaman (optional)
- `showLogo`: boolean (default: `true`)
- `logoSize`: `'sm'` | `'md'` | `'lg'` (default: `'md'`)
- `dataTranslateTitle`: string | null - Attribute untuk translation
- `dataTranslateSubtitle`: string | null - Attribute untuk translation
- `class`: string untuk additional container classes

---

## 🔧 Theme Controller

### Alpine.js Component

```javascript
// resources/js/theme.js
export default function themeController() {
    return {
        theme: 'light',
        isDark: false,
        isStylis: false,

        init() {
            // Load from localStorage
            const saved = localStorage.getItem('roxwood-theme');
            if (saved) this.theme = saved;

            this.applyTheme();
        },

        applyTheme() {
            const html = document.documentElement;
            html.classList.remove('theme-light', 'theme-dark', 'theme-stylis');
            html.classList.add(`theme-${this.theme}`);

            localStorage.setItem('roxwood-theme', this.theme);
        },

        setTheme(name) {
            this.theme = name;
        },

        toggleTheme() {
            const themes = ['light', 'dark', 'stylis'];
            const idx = themes.indexOf(this.theme);
            this.theme = themes[(idx + 1) % themes.length];
        }
    };
}
```

### Blade Usage

```blade
<div x-data="themeController()">
    <button @click="setTheme('dark')">Dark Mode</button>
    <button @click="toggleTheme()">Toggle Theme</button>
</div>
```

---

## 🎨 Stylis Theme

### Glassmorphism Effect

Kelas `.glass` untuk efek glassmorphism:

```css
.glass {
    background: rgba(255, 255, 255, 0.8);
    backdrop-filter: blur(12px);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.theme-dark .glass {
    background: rgba(30, 41, 59, 0.8);
    border: 1px solid rgba(51, 65, 85, 0.3);
}
```

### Usage

```blade
<div class="glass rounded-xl p-6">
    Glassmorphism card
</div>
```

---

## 🎯 Best Practices

### 1. SELALU gunakan CSS variables

❌ **JANGAN:**
```blade
<div class="bg-blue-500">
```

✅ **GUNAKAN:**
```blade
<div class="bg-primary text-primary-500">
```

### 2. Pastikan semua komponen theme-aware

```blade
<!-- Component yang baik -->
<div class="bg-surface border border-border text-text-primary">
    <!-- Semua warna mengikuti theme -->
</div>
```

### 3. Test di semua theme

Pastikan setiap komponen ditest di:
- Light theme
- Dark theme
- Stylis theme
- Stylis + Dark theme

---

## 📋 Theme Checklist

- [ ] Semua komponen menggunakan design tokens
- [ ] Tidak ada hardcoded colors
- [ ] Dark mode bekerja dengan baik
- [ ] Stylis theme bekerja dengan baik
- [ ] Transitions smooth antar theme
- [ ] localStorage persist bekerja
- [ ] System preference detection bekerja

---

## 🔮 Future Enhancements

1. Custom theme colors
2. Font size scaling
3. High contrast mode
4. Reduced motion mode
5. More color themes
