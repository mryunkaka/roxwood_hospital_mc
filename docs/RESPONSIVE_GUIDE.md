# Responsive Design Guide

## Mobile-First Approach

Roxwood Health Medical Center menggunakan **mobile-first design**, artinya semua komponen didesain untuk mobile terlebih dahulu, kemudian di-enhance untuk tablet dan desktop.

---

## 📱 Breakpoints

| Nama | Ukuran | Device Target |
|------|--------|---------------|
| `mobile` | < 640px | Phone |
| `sm` | ≥ 640px | Large Phone, Small Tablet |
| `md` | ≥ 768px | Tablet |
| `lg` | ≥ 1024px | Small Desktop, Laptop |
| `xl` | ≥ 1280px | Desktop |
| `2xl` | ≥ 1536px | Large Desktop |

---

## 🎯 Responsive Grid

### Default Grid Behavior

```blade
<x-grid :cols="1">        <!-- Mobile: 1 column -->
<x-grid :cols="2">        <!-- Mobile: 1, SM+: 2 columns -->
<x-grid :cols="3">        <!-- Mobile: 1, SM: 2, LG+: 3 columns -->
<x-grid :cols="4">        <!-- Mobile: 1, SM: 2, LG: 3, XL+: 4 columns -->
```

### Manual Grid Classes

```blade
<!-- Mobile: 1 column, Tablet: 2 columns, Desktop: 3 columns -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
    <!-- Grid items -->
</div>
```

---

## 🧭 Navigation

### Sidebar Behavior

| Breakpoint | Behavior |
|------------|----------|
| Mobile (< 1024px) | Hidden by default, slide-in drawer |
| Desktop (≥ 1024px) | Always visible, collapsible |

### Mobile Menu Toggle

```blade
<!-- Hamburger button appears on mobile only -->
<button @click="sidebarOpen = !sidebarOpen" class="lg:hidden">
    <!-- Hamburger icon -->
</button>
```

---

## 📊 Tables

### Mobile Responsive Table

```blade
<!-- Table automatically scrollable on mobile -->
<div class="overflow-x-auto">
    <x-table>
        <!-- Table content -->
    </x-table>
</div>
```

---

## 🎨 Cards & Containers

### Responsive Padding

```blade
<!-- Smaller padding on mobile -->
<x-card :padding="'default'">
    <!-- Default: p-4 mobile, p-5 sm, p-6 lg -->
</x-card>
```

### Responsive Stats Grid

```blade
<!-- 1 column mobile, 2 tablet, 4 desktop -->
<x-grid :cols="4">
    <x-stat-card />
    <x-stat-card />
    <x-stat-card />
    <x-stat-card />
</x-grid>
```

---

## 📝 Forms

### Stacked on Mobile, Side-by-Side on Desktop

```blade
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <x-input name="firstName" label="First Name" />
    <x-input name="lastName" label="Last Name" />
</div>
```

### Full Width Button on Mobile

```blade
<x-button :fullWidth="true" class="md:w-auto">
    Submit
</x-button>
```

---

## 🔍 Search & Filters

### Hide on Mobile, Show on Desktop

```blade
<!-- Hidden on mobile, visible on lg+ -->
<div class="hidden lg:block">
    <input type="search" placeholder="Search..." class="w-64" />
</div>
```

---

## 📐 Spacing System

### Responsive Spacing

```blade
<!-- Margin: 4 mobile, 6 sm, 8 lg -->
<div class="mx-4 sm:mx-6 lg:mx-8">
    Content
</div>

<!-- Padding: 2 mobile, 4 sm, 6 lg -->
<div class="p-2 sm:p-4 lg:p-6">
    Content
</div>
```

---

## 🎯 Best Practices

### 1. Use Responsive Classes

```blade
<!-- Good -->
<div class="text-sm sm:text-base lg:text-lg">
    Responsive Text
</div>

<!-- Avoid -->
<div class="text-base">
    Fixed Text Size
</div>
```

### 2. Progressive Enhancement

```blade
<!-- Start with mobile, enhance for larger screens -->
<div class="flex flex-col md:flex-row gap-4">
    <div>Column 1</div>
    <div>Column 2</div>
</div>
```

### 3. Hide/Show Responsively

```blade
<!-- Mobile only -->
<div class="block lg:hidden">
    Mobile content
</div>

<!-- Desktop only -->
<div class="hidden lg:block">
    Desktop content
</div>
```

---

## 📋 Responsive Checklist

Setiap halaman harus ditest pada:

- [ ] **iPhone SE** (375px) - Smallest mobile
- [ ] **iPhone 12/13** (390px) - Standard mobile
- [ ] **iPad** (768px) - Tablet portrait
- [ ] **iPad Pro** (1024px) - Tablet landscape / Small laptop
- [ ] **Desktop** (1280px+) - Full desktop

---

## 🧪 Testing Tools

### Browser DevTools

1. Open Chrome DevTools (F12)
2. Click device toolbar icon (Ctrl+Shift+M)
3. Select device from dropdown or enter custom dimensions

### Common Test Sizes

```text
Mobile:   375x667  (iPhone SE)
Mobile:   390x844  (iPhone 12)
Tablet:   768x1024 (iPad)
Desktop:  1280x720 (Small Desktop)
Desktop:  1920x1080 (Full HD)
```

---

## 📚 Tailwind Responsive Modifiers

```blade
<!-- Syntax: breakpoint:class -->

mobile:   class="text-sm"          <!-- Always applied -->
sm:       class="sm:text-base"     <!-- ≥ 640px -->
md:       class="md:text-lg"       <!-- ≥ 768px -->
lg:       class="xl:text-xl"       <!-- ≥ 1024px -->
xl:       class="xl:text-2xl"      <!-- ≥ 1280px -->
2xl:      class="2xl:text-3xl"     <!-- ≥ 1536px -->
```

---

## 🎯 Quick Reference

| Element | Mobile | Tablet | Desktop |
|---------|--------|--------|---------|
| Grid | 1 col | 2 cols | 3-4 cols |
| Sidebar | Drawer | Collapsed | Full |
| Navbar | Hamburger | Full | Full |
| Tables | Scrollable | Scrollable | Normal |
| Forms | Stacked | Side-by-side | Side-by-side |
| Padding | 4 | 5-6 | 6-8 |
| Font size | sm | base | lg |
