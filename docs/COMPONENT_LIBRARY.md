# Component Library

## Daftar Komponen Blade

---

## Layout Components

### x-container

Container dengan max-width responsive.

```blade
<x-container size="default">
    Content here
</x-container>
```

**Props:**
- `size`: `'sm'` | `'default'` | `'lg'` | `'xl'` | `'full'`

---

### x-section

Section wrapper.

```blade
<x-section class="mt-6">
    <h2>Section Title</h2>
</x-section>
```

---

### x-card

Card dengan opsional title dan subtitle.

```blade
<x-card title="Card Title" subtitle="Optional subtitle">
    Card content goes here
</x-card>
```

**Props:**
- `title`: string | null
- `subtitle`: string | null
- `padding`: `'none'` | `'sm'` | `'default'` | `'lg'`
- `border`: boolean
- `shadow`: `'none'` | `'sm'` | `'default'` | `'lg'` | `'xl'`
- `hoverable`: boolean

---

### x-grid

Grid responsive.

```blade
<x-grid :cols="3" :gap="'default'">
    <div>Item 1</div>
    <div>Item 2</div>
    <div>Item 3</div>
</x-grid>
```

**Props:**
- `cols`: 1 | 2 | 3 | 4
- `gap`: `'none'` | `'sm'` | `'default'` | `'lg'`

---

## Navigation Components

### x-breadcrumb

Breadcrumb navigation.

```blade
<x-breadcrumb :pages="[
    ['title' => 'Dashboard', 'url' => '/'],
    ['title' => 'Settings']
]" />
```

---

### x-tabs

Tab navigation dengan content switching.

```blade
<x-tabs :tabs="[
    ['key' => 'tab1', 'label' => 'Tab 1'],
    ['key' => 'tab2', 'label' => 'Tab 2']
]" :activeTab="'tab1'" :variant="'underline'">
    <!-- Tab content rendered dynamically -->
</x-tabs>
```

**Props:**
- `tabs`: array of {key, label}
- `activeTab`: string
- `variant`: `'underline'` | `'pills'` | `'bordered'`

---

### x-pagination

Pagination controls.

```blade
<x-pagination :currentPage="1" :totalPages="10" />
```

**Props:**
- `currentPage`: int
- `totalPages`: int
- `onEachSide`: int (default: 2)

---

## Form Components

### x-input

Input field dengan label dan error handling.

```blade
<x-input
    type="email"
    name="email"
    label="Email Address"
    :dataTranslateLabel="'email'"
    :dataTranslatePlaceholder="'email_placeholder'"
    :placeholder="__('messages.email_placeholder')"
    :required="true"
    :error="$errors->first('email')"
/>
```

**Props:**
- `type`: `'text'` | `'email'` | `'password'` | `etc`
- `name`: string
- `label`: string | null
- `dataTranslateLabel`: string | null (for dynamic label translation)
- `placeholder`: string
- `dataTranslatePlaceholder`: string | null (for dynamic placeholder translation)
- `value`: mixed
- `required`: boolean
- `disabled`: boolean
- `readonly`: boolean
- `error`: string | null
- `hint`: string | null
- `icon`: string (SVG HTML)

**Theme Support:**
- Uses `bg-surface` for background (theme-aware)
- Uses `border-border` for border color (theme-aware)
- Uses `text-text-primary` for text color (theme-aware)
- Dark theme: Automatic color adjustments

---

### x-select

Dropdown select.

```blade
<x-select
    name="department"
    label="Department"
    :dataTranslateLabel="'department'"
    :options="[
        ['value' => 'cardio', 'label' => 'Cardiology'],
        ['value' => 'neuro', 'label' => 'Neurology']
    ]"
    placeholder="Select department"
    :required="true"
/>
```

**Props:**
- `name`: string
- `label`: string | null
- `dataTranslateLabel`: string | null (for dynamic translation)
- `options`: array
- `placeholder`: string | null
- `value`: mixed
- `required`: boolean
- `disabled`: boolean
- `error`: string | null
- `hint`: string | null

**Theme Support:**
- Uses `bg-surface` for background (theme-aware)
- Uses `border-border` for border color (theme-aware)
- Uses `text-text-primary` for text color (theme-aware)
- Dark theme: `bg-slate-800 border-slate-700 text-white`

---

### x-button

Button dengan berbagai variant dan size.

```blade
<x-button variant="primary" size="lg" :fullWidth="true">
    Submit Form
</x-button>

<x-button variant="danger" :icon="'<svg>...</svg>'">
    Delete
</x-button>
```

**Props:**
- `variant`: `'primary'` | `'secondary'` | `'success'` | `'danger'` | `'warning'` | `'info'` | `'ghost'` | `'link'`
- `size`: `'sm'` | `'default'` | `'lg'`
- `type`: `'button'` | `'submit'` | `'reset'`
- `disabled`: boolean
- `fullWidth`: boolean
- `icon`: string (SVG HTML)
- `iconPosition`: `'left'` | `'right'`

---

## Feedback Components

### x-alert

Alert/notification message dengan theme-aware styling untuk light, dark, dan stylis themes.

```blade
<x-alert type="success" :dismissible="true">
    <strong>Success!</strong> Your changes have been saved.
</x-alert>

<x-alert type="danger">
    <strong>Error!</strong> Something went wrong.
</x-alert>

<x-alert type="warning" :icon="false">
    <strong>Warning!</strong> Please review your input.
</x-alert>

<x-alert type="info" :dismissible="true">
    <strong>Info:</strong> New features available.
</x-alert>
```

**Props:**
- `type`: `'success'` | `'danger'` | `'warning'` | `'info'`
- `dismissible`: boolean (default: false)
- `icon`: boolean (default: true)
- `class`: string untuk additional classes

**Theme Support:**
- **Light Theme**: Soft pastel backgrounds (bg-success-50, bg-danger-50, dll) dengan border yang kontras
- **Dark Theme**: Semi-transparent backgrounds (bg-success-900/25, dll) untuk integrasi yang baik dengan background gelap
- **Stylis Theme**: Medical-appropriate soft color tones (emerald, rose, amber, sky) dengan glassmorphism effect

**Visual Features:**
- Rounded corners (rounded-xl) untuk modern look
- Subtle shadow (shadow-sm) untuk depth
- Icon dengan ring opacity untuk visual hierarchy
- Smooth transitions untuk dismiss action
- Proper ARIA attributes (role="alert", aria-live="polite") untuk accessibility

---

### x-badge

Badge/status indicator dengan theme-aware styling untuk light, dark, dan stylis themes.

```blade
<x-badge variant="default">Default</x-badge>
<x-badge variant="primary">Primary</x-badge>
<x-badge variant="success">Active</x-badge>
<x-badge variant="danger">Busy</x-badge>
<x-badge variant="warning" :dot="true">Pending</x-badge>
<x-badge variant="info">New</x-badge>
```

**Props:**
- `variant`: `'default'` | `'primary'` | `'success'` | `'danger'` | `'warning'` | `'info'`
- `size`: `'sm'` | `'default'` | `'lg'`
- `dot`: boolean (default: false)
- `class`: string untuk additional classes

**Theme Support:**
- **Light Theme**: Soft pastel backgrounds (bg-success-50, bg-danger-50, dll) dengan text colors yang kontras
- **Dark Theme**: Semi-transparent backgrounds (bg-success-900/30, dll) dengan text colors yang lebih terang
- **Stylis Theme**: Medical-appropriate soft color tones (emerald, rose, amber, sky) dengan semi-transparent backgrounds

---

### x-checkbox

Checkbox input dengan label, theme-aware untuk light, dark, dan stylis themes.

```blade
{{-- Basic Checkbox --}}
<x-checkbox name="terms" label="I agree to the terms" />

{{-- With Description --}}
<x-checkbox name="newsletter" label="Subscribe to newsletter" description="Get weekly updates" />

{{-- Checked --}}
<x-checkbox name="remember" label="Remember me" :checked="true" />

{{-- Disabled --}}
<x-checkbox name="disabled" label="Disabled option" :disabled="true" />
```

**Props:**
- `name`: string (optional) - Input name attribute
- `label`: string (optional) - Label text
- `checked`: boolean (default: false)
- `disabled`: boolean (default: false)
- `description`: string (optional) - Additional description text
- `class`: string untuk additional classes

**Theme Support:**
- **Light Theme**: Border rounded dengan background surface, checked state biru primary
- **Dark Theme**: Darker background dan border, checked state tetap primary
- **Stylis Theme**: Semi-transparent background dengan teal border pada hover/checked

---

### x-radio

Radio button input dengan label, theme-aware untuk light, dark, dan stylis themes.

```blade
{{-- Basic Radio --}}
<x-radio name="gender" value="male" label="Male" />
<x-radio name="gender" value="female" label="Female" />

{{-- With Description --}}
<x-radio name="plan" value="free" label="Free Plan" description="Basic features" />
<x-radio name="plan" value="pro" label="Pro Plan" description="All features" :checked="true" />

{{-- Disabled --}}
<x-radio name="option" value="disabled" label="Disabled" :disabled="true" />
```

**Props:**
- `name`: string (optional) - Input name attribute (must be same for radio group)
- `value`: string (optional) - Input value
- `label`: string (optional) - Label text
- `checked`: boolean (default: false)
- `disabled`: boolean (default: false)
- `description`: string (optional) - Additional description text
- `class`: string untuk additional classes

**Theme Support:**
- **Light Theme**: Circular border dengan background surface, checked dengan dot primary
- **Dark Theme**: Darker background dan border, checked dot tetap primary
- **Stylis Theme**: Semi-transparent background dengan teal styling

---

### x-autocomplete

Autocomplete/select dropdown dengan search functionality, theme-aware untuk light, dark, dan stylis themes.

```blade
{{-- Basic Select --}}
<x-autocomplete
    label="Select Department"
    name="department"
    :options="[
        ['value' => 'cardio', 'label' => 'Cardiology'],
        ['value' => 'neuro', 'label' => 'Neurology'],
    ]"
/>

{{-- Searchable --}}
<x-autocomplete
    label="Select Doctor"
    name="doctor"
    :searchable="true"
    :options="[
        ['value' => 'dr_smith', 'label' => 'Dr. John Smith'],
        ['value' => 'dr_jones', 'label' => 'Dr. Sarah Jones'],
    ]"
    placeholder="Search doctor..."
/>

{{-- Clearable --}}
<x-autocomplete
    label="Patient"
    name="patient"
    :clearable="true"
    :options="[
        ['value' => 'p1', 'label' => 'Patient 1'],
        ['value' => 'p2', 'label' => 'Patient 2'],
    ]"
/>
```

**Props:**
- `name`: string (optional) - Input name attribute
- `label`: string (optional) - Field label
- `placeholder`: string (default: "Select an option")
- `options`: array - Array of {value, label} pairs
- `value`: string (optional) - Selected value
- `required`: boolean (default: false)
- `disabled`: boolean (default: false)
- `searchable`: boolean (default: true) - Enable search functionality
- `clearable`: boolean (default: true) - Show clear button
- `error`: string (optional) - Error message

**Theme Support:**
- **Light Theme**: Surface background dengan border, dropdown dengan shadow
- **Dark Theme**: Slate background, dropdown dengan shadow yang lebih gelap
- **Stylis Theme**: Semi-transparent background dengan teal border, backdrop blur

---

### x-avatar

User avatar dengan initials.

```blade
<x-avatar name="John Doe" size="md" />
<x-avatar name="Jane Smith" size="lg" status="online" />
```

**Props:**
- `src`: string | null (image URL)
- `name`: string | null (for initials)
- `size`: `'xs'` | `'sm'` | `'md'` | `'lg'` | `'xl'` | `'2xl'`
- `rounded`: `'full'` | `'lg'` | `'md'` | `'none'`
- `status`: `'online'` | `'offline'` | `'away'` | `'busy'` | null

---

## Data Components

### x-table

Data table dengan responsive overflow.

```blade
<x-table
    :headers="[
        ['label' => 'Name', 'key' => 'name'],
        ['label' => 'Email', 'key' => 'email'],
        ['label' => 'Status', 'key' => 'status']
    ]"
    :striped="true"
    :hoverable="true"
>
    <tr>
        <td>John Doe</td>
        <td>john@example.com</td>
        <td><x-badge variant="success">Active</x-badge></td>
    </tr>
</x-table>
```

**Props:**
- `headers`: array
- `striped`: boolean
- `bordered`: boolean
- `hoverable`: boolean
- `compact`: boolean

---

### x-stat-card

Statistics card untuk dashboard.

```blade
<x-stat-card
    title="Total Patients"
    value="2,847"
    change="+12.5%"
    changeType="positive"
    :icon="'<svg>...</svg>'"
    color="primary"
/>
```

**Props:**
- `title`: string
- `value`: string
- `change`: string | null
- `changeType`: `'positive'` | `'negative'` | `'neutral'`
- `icon`: string (SVG HTML) | null
- `color`: `'primary'` | `'success'` | `'danger'` | `'warning'` | `'info'`

---

## Overlay Components

### x-modal

Modal dialog dengan backdrop, theme-aware untuk light, dark, dan stylis themes.

```blade
{{-- Basic Modal --}}
<x-modal title="Modal Title" trigger="Open Modal">
    <p>Modal content here</p>

    @slot('footer')
        <button class="px-4 py-2 rounded-xl bg-surface border border-border">Cancel</button>
        <button class="px-4 py-2 rounded-xl bg-primary text-white">Confirm</button>
    @endslot
</x-modal>

{{-- Small Modal --}}
<x-modal title="Confirm" size="sm" trigger="Confirm Action">
    <p class="text-center">Are you sure you want to proceed?</p>
    @slot('footer')
        <button class="px-4 py-2 rounded-xl bg-danger text-white">Delete</button>
    @endslot
</x-modal>

{{-- Large Modal --}}
<x-modal title="Patient Details" size="lg" trigger="View Details">
    <p>More content here...</p>
</x-modal>
```

**Props:**
- `title`: string | null (optional)
- `size`: `'sm'` | `'default'` | `'lg'` | `'xl'` | `'full'`
- `centered`: boolean (default: true)
- `backdrop`: boolean (default: true)
- `trigger`: string untuk custom trigger button text
- `class`: string untuk additional trigger button classes

**Slots:**
- `footer` - Untuk tombol action di bagian bawah modal

**Theme Support:**
- **Light Theme**: `bg-surface` dengan border dan shadow yang jelas
- **Dark Theme**: Shadow lebih intens (`shadow-black/50`), backdrop dengan blur
- **Stylis Theme**:
  - `rounded-3xl` untuk lebih rounded
  - `backdrop-blur-xl` untuk glassmorphism effect
  - `bg-white/90` semi-transparent
  - Border `border-white/30` untuk subtle outline
  - Dark stylis: `bg-surface/90` dengan `border-teal-700/30`

**Visual Features:**
- Smooth scale + translate transitions
- Backdrop blur untuk focus
- Close button dengan hover scale effect
- Responsive sizing dengan max-width variants

---

### x-dropdown

Dropdown menu.

```blade
<x-dropdown>
    <x-button>Click Me</x-button>

    @slot('dropdownContent')
        <a href="#" class="block px-4 py-2">Option 1</a>
        <a href="#" class="block px-4 py-2">Option 2</a>
    @endslot
</x-dropdown>
```

**Props:**
- `placement`: `'bottom-start'` | `'bottom-end'` | `'top-start'` | `'top-end'`
- `offset`: int (default: 8)

---

## Utility Components

### x-brand-logo

Brand logo component untuk menampilkan logo Roxwood dengan icon dan teks.

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

**Theme Support:**
- Uses CSS variables `--color-logo-icon`, `--color-logo-bg-from`, `--color-logo-bg-to`
- Automatically adapts to Light, Dark, and Stylis themes

---

### x-page-header

Page header component dengan logo, judul, dan deskripsi.

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

**Theme Support:**
- Text uses `text-text-primary` and `text-text-secondary`
- Logo inherits all theme support from `x-brand-logo`

---

### x-icon-wrapper

Wrapper untuk icon dengan size konsisten.

```blade
<x-icon-wrapper size="md">
    <svg><!-- icon --></svg>
</x-icon-wrapper>
```

---

## Tips Penggunaan

1. **Selalu gunakan komponen, bukan raw HTML**
2. **Props dengan `:` untuk data dinamis**
3. **Tanpa `:` untuk string literal**
4. **Slot untuk custom content**

```blade
<!-- Benar -->
<x-card :title="$pageTitle">

<!-- Salah -->
<x-card title="$pageTitle">
```
