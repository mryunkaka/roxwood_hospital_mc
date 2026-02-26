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

### x-toast

Toast notification system dengan theme-aware styling untuk light, dark, dan stylis themes. Toast muncul sebagai notifikasi non-intrusif di pojok layar.

**Usage - Global API:**

```javascript
// Basic usage
$toast.success('Operation completed successfully!')
$toast.error('Something went wrong!')
$toast.warning('Please review your input.')
$info('New features available!')

// With options
$toast.show('Message here', 'success', {
    title: 'Custom Title',           // Optional title
    duration: 3000,                   // Auto-dismiss duration (ms)
    persistent: false,                // Don't auto-dismiss
    icon: true,                       // Show/hide icon
    actions: [                        // Optional action buttons
        { label: 'Undo', variant: 'default', handler: (id) => {} },
        { label: 'View', variant: 'primary', handler: (id) => {} }
    ]
})

// Clear all toasts
$toast.clear()
```

**Usage - In Blade/Alpine:**

```blade
<button @click="$toast.success('Saved successfully!')">Show Toast</button>

<button @click="$toast.show('Custom message', 'info', { title: 'With Title!' })">
    With Title
</button>

<button @click="$toast.show('Persistent message', 'warning', { persistent: true })">
    Persistent
</button>

<button @click="$toast.clear()">Clear All</button>
```

**Props (via options object):**
- `title`: string | null - Optional title above the message
- `message`: string - The toast message (required)
- `type`: `'success'` | `'danger'` | `'warning'` | `'info'`
- `duration`: int (default: 5000) - Auto-dismiss duration in milliseconds
- `persistent`: boolean (default: false) - Don't auto-dismiss
- `icon`: boolean (default: true) - Show/hide icon
- `actions`: array - Array of {label, variant, handler} objects

**Position Options:**

```blade
{{-- Change toast position --}}
<div x-data="{ position: 'top-right' }">
    <button @click="$toast.position = 'top-right'">Top Right</button>
    <button @click="$toast.position = 'top-center'">Top Center</button>
    <button @click="$toast.position = 'top-left'">Top Left</button>
    <button @click="$toast.position = 'bottom-right'">Bottom Right</button>
    <button @click="$toast.position = 'bottom-center'">Bottom Center</button>
    <button @click="$toast.position = 'bottom-left'">Bottom Left</button>
</div>
```

**Theme Support:**
- **Light Theme**: Surface background dengan shadow-lg, icon gradients
- **Dark Theme**: Semi-transparent backgrounds, light icon colors
- **Stylis Theme**: Soft color tones dengan glassmorphism effect

**Visual Features:**
- Smooth enter/exit animations (scale + translate + fade)
- Progress bar showing remaining time
- Pause on hover, resume on leave
- Stackable (multiple toasts can appear)
- Maximum 5 toasts visible at once (configurable)
- Mobile responsive width

---

### x-skeleton

Skeleton loading component dengan shimmer animation, theme-aware untuk light, dark, dan stylis themes. Digunakan sebagai placeholder content saat loading.

```blade
{{-- Text Skeleton - Multiple lines --}}
<x-skeleton type="text" :lines="3" />

 {{-- Circle Skeleton - Avatar loader --}}
<x-skeleton type="circle" width="48px" />

{{-- Card Skeleton - Full card placeholder --}}
<x-skeleton type="card" />

{{-- Table Skeleton - Table rows placeholder --}}
<x-skeleton type="table" />

{{-- Custom Skeleton - User provided content --}}
<x-skeleton type="custom" class="w-full h-32">
    <div class="flex gap-4">
        <div class="w-1/3 h-24 bg-surface rounded-lg"></div>
        <div class="w-2/3 h-24 bg-surface rounded-lg"></div>
    </div>
</x-skeleton>
```

**Props:**
- `type`: `'text'` | `'circle'` | `'card'` | `'table'` | `'custom'` (default: `'text'`)
- `width`: string (default: `'100%'`) - Width untuk circle/bar type
- `height`: string | null - Height untuk bar type
- `lines`: int (default: 3) - Number of lines untuk text type
- `class`: string untuk additional classes

**Theme Support:**
- **Light Theme**: Gradient `from-gray-100 via-gray-50 to-gray-100`
- **Dark Theme**: Gradient `from-gray-700 via-gray-600 to-gray-700`
- **Stylis Theme**: Semi-transparent white gradient `rgba(255, 255, 255, 0.3-0.5)`

**Visual Features:**
- Smooth shimmer animation (1.5s infinite)
- Progressive width untuk text lines (last line 70%)
- Responsive height untuk custom sizing
- Compatible dengan semua theme variants

---

### x-progress

Progress bar component dengan berbagai variant dan size, theme-aware untuk light, dark, dan stylis themes.

```blade
{{-- Basic Progress --}}
<x-progress :value="75" />

{{-- With Label dan Percentage --}}
<x-progress :value="60" label="Upload Progress" :showPercentage="true" />

{{-- Variant Colors --}}
<x-progress :value="100" variant="success" />
<x-progress :value="30" variant="danger" />
<x-progress :value="50" variant="warning" />

{{-- Size Variants --}}
<x-progress :value="45" size="sm" />
<x-progress :value="45" size="md" />
<x-progress :value="45" size="lg" />

{{-- Striped Progress --}}
<x-progress :value="70" :striped="true" />

{{-- Animated Progress (Striped + Animation) --}}
<x-progress :value="80" :striped="true" :animated="true" />
```

**Props:**
- `value`: int (default: 0) - Current value (0-100)
- `max`: int (default: 100) - Maximum value
- `variant`: `'primary'` | `'success'` | `'danger'` | `'warning'` | `'info'` | `'secondary'` (default: `'primary'`)
- `size`: `'sm'` | `'md'` | `'lg'` (default: `'md'`)
- `showLabel`: boolean (default: false) - Show label above progress bar
- `label`: string | null - Custom label text
- `showPercentage`: boolean (default: false) - Show percentage value
- `striped`: boolean (default: false) - Add striped pattern
- `animated`: boolean (default: false) - Animate stripes (requires striped)
- `class`: string untuk additional classes

**Size Reference:**
- `sm`: Height `h-1` (0.25rem)
- `md`: Height `h-2` (0.5rem)
- `lg`: Height `h-3` (0.75rem)

**Theme Support:**
- **Light Theme**: Gray-200 background, colored progress fill
- **Dark Theme**: Gray-700 background, colored progress fill
- **Stylis Theme**: Teal-based colors (teal-500, emerald-500, rose-500, amber-500, sky-500)

**Visual Features:**
- Smooth transition animation (300ms ease-out)
- Striped pattern dengan 45deg gradient
- Animated stripes untuk active loading indication
- Rounded container (rounded-full)
- Accessible dengan ARIA attributes

---

### x-tooltip

Tooltip component dengan berbagai position dan trigger, theme-aware untuk light, dark, dan stylis themes.

```blade
{{-- Basic Tooltip - Hover on Top --}}
<x-tooltip content="This is a tooltip">
    <button>Hover Me</button>
</x-tooltip>

{{-- Positions --}}
<x-tooltip content="Top tooltip" position="top">
    <button>Top</button>
</x-tooltip>

<x-tooltip content="Bottom tooltip" position="bottom">
    <button>Bottom</button>
</x-tooltip>

<x-tooltip content="Left tooltip" position="left">
    <button>Left</button>
</x-tooltip>

<x-tooltip content="Right tooltip" position="right">
    <button>Right</button>
</x-tooltip>

{{-- Advanced Positions --}}
<x-tooltip content="Top Start" position="top-start">
    <button>Top Start</button>
</x-tooltip>

{{-- Triggers --}}
<x-tooltip content="Click to see tooltip" trigger="click">
    <button>Click Me</button>
</x-tooltip>

<x-tooltip content="Focus to see tooltip" trigger="focus">
    <input type="text" placeholder="Focus Me" />
</x-tooltip>

{{-- Variants --}}
<x-tooltip content="Light theme tooltip" variant="light">
    <button>Light Tooltip</button>
</x-tooltip>

{{-- Disabled --}}
<x-tooltip content="This won't show" :disabled="true">
    <button>Disabled Tooltip</button>
</x-tooltip>

{{-- HTML Content --}}
<x-tooltip content="<strong>Bold</strong> and <em>italic</em> text">
    <button>Rich Content</button>
</x-tooltip>
```

**Props:**
- `content`: string (default: `''`) - Tooltip content (text or HTML)
- `position`: `'top'` | `'bottom'` | `'left'` | `'right'` | `'top-start'` | `'top-end'` | `'bottom-start'` | `'bottom-end'` | `'left-start'` | `'left-end'` | `'right-start'` | `'right-end'` (default: `'top'`)
- `variant`: `'dark'` | `'light'` (default: `'dark'`)
- `trigger`: `'hover'` | `'click'` | `'focus'` (default: `'hover'`)
- `disabled`: boolean (default: false)
- `class`: string untuk additional classes

**Position Reference:**
- `top`: Centered above element
- `bottom`: Centered below element
- `left`: Centered to the left
- `right`: Centered to the right
- `top-start`: Above, aligned left
- `top-end`: Above, aligned right
- `bottom-start`: Below, aligned left
- `bottom-end`: Below, aligned right
- `left-start`: Left, aligned top
- `left-end`: Left, aligned bottom
- `right-start`: Right, aligned top
- `right-end`: Right, aligned bottom

**Trigger Behavior:**
- `hover`: Shows on mouseenter, hides on mouseleave (also works with click)
- `click`: Toggles on click, closes when clicking outside
- `focus`: Shows on focus, hides on blur (for form elements)

**Theme Support:**
- **Light Theme**:
  - Dark variant: `bg-gray-900 text-white` dengan gray arrow
  - Light variant: `bg-white text-gray-900 border border-gray-200`
- **Dark Theme**: Same as light theme (consistent colors)
- **Stylis Theme**: Inherits theme colors

**Visual Features:**
- Smooth scale + fade transitions (150ms)
- Arrow indicator pointing to element
- Auto-positioning with spacing (8px offset)
- Non-wrapping text (`whitespace-nowrap`)
- Z-index 50 untuk proper layering

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

### x-chart

Chart component menggunakan Chart.js, theme-aware untuk light, dark, dan stylis themes. Mendukung berbagai jenis chart.

```blade
{{-- Line Chart --}}
<x-chart type="line" :height="'300px'" />

{{-- Bar Chart --}}
<x-chart type="bar" :height="'400px'" />

{{-- Pie Chart --}}
<x-chart type="pie" :height="'300px'" />

{{-- Doughnut Chart --}}
<x-chart type="doughnut" :height="'300px'" />

{{-- Polar Area Chart --}}
<x-chart type="polarArea" :height="'350px'" />

{{-- Radar Chart --}}
<x-chart type="radar" :height="'400px'" />
```

**Props:**
- `type`: `'line'` | `'bar'` | `'pie'` | `'doughnut'` | `'polarArea'` | `'radar'` (default: `'line'`)
- `height`: string (default: `'300px'`) - Chart container height
- `data`: object (optional) - Chart data configuration
- `options`: object (optional) - Chart.js options
- `class`: string untuk additional classes

**JavaScript Controller (chart.js):**

```javascript
export default function chartController() {
    return {
        type: 'line',
        data: {},
        options: {},
        chart: null,

        createChart() {
            const canvas = this.$el;
            const ctx = canvas.getContext('2d');

            // Default data if not provided
            const defaultData = {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Dataset',
                    data: [12, 19, 3, 5, 2, 3],
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4
                }]
            };

            // Default options if not provided
            const defaultOptions = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                }
            };

            // Destroy existing chart
            if (this.chart) {
                this.chart.destroy();
            }

            // Create new chart
            this.chart = new Chart(ctx, {
                type: this.type,
                data: this.data || defaultData,
                options: this.options || defaultOptions
            });
        }
    };
}
```

**Usage Example with Custom Data:**

```blade
<x-chart
    type="line"
    :height="'400px'"
    :data="[
        'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        'datasets' => [
            [
                'label' => 'Patients',
                'data' => [65, 59, 80, 81, 56, 55],
                'borderColor' => '#3b82f6',
                'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                'tension' => 0.4
            ]
        ]
    ]"
/>
```

**Theme Support:**
- **Light Theme**: Default Chart.js colors
- **Dark Theme**: Updated legend text colors (white) for visibility
- **Stylis Theme**: Inherits stylis color palette (teal-based)

**Visual Features:**
- Responsive dengan maintainAspectRatio: false
- Smooth animations
- Lazy loading dengan x-intersect directive
- Auto-destroy pada re-initialization
- Legend dan tooltips included

---

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

### x-file-upload

File upload component dengan drag & drop, preview, dan validation, theme-aware untuk light, dark, dan stylis themes.

```blade
{{-- Basic File Upload --}}
<x-file-upload name="document" label="Upload Document" />

{{-- Multiple Files --}}
<x-file-upload name="files" label="Upload Files" :multiple="true" :maxFiles="5" />

{{-- With File Type Restriction --}}
<x-file-upload
    name="image"
    label="Upload Image"
    accept="image/*"
    :maxSize="5120"
/>

{{-- Custom Width --}}
<x-file-upload name="file" width="w-96" />

{{-- Without Preview --}}
<x-file-upload name="file" :preview="false" />
```

**Props:**
- `name`: string (default: `'file'`) - Input name attribute
- `label`: string | null - Field label
- `accept`: string | null - Accepted file types (e.g., `"image/*,.pdf"`)
- `multiple`: boolean (default: false) - Allow multiple files
- `maxFiles`: int | null - Maximum number of files
- `maxSize`: int (default: 5120) - Max file size in KB
- `preview`: boolean (default: true) - Show image previews
- `required`: boolean (default: false)
- `disabled`: boolean (default: false)
- `error`: string | null - Error message
- `class`: string untuk additional classes

**JavaScript Methods:**
```javascript
// Get all valid files
$el.querySelector('[x-data*="fileUploadController"]').getValidFiles();

// Clear all files
$el.querySelector('[x-data*="fileUploadController"]').clearAll();

// Remove specific file
$el.querySelector('[x-data*="fileUploadController"]').removeFile(index);
```

**Theme Support:**
- **Light Theme**: Border dengan hover state, smooth transitions
- **Dark Theme**: Darker borders, adjusted hover colors
- **Stylis Theme**: Teal-based border colors dengan semi-transparent background

**Visual Features:**
- Drag & drop dengan visual feedback
- Image preview untuk image files
- File size validation
- File type validation
- Progress bar (untuk simulated upload)
- Remove individual files
- Clear all files button
- Error display untuk invalid files
- Paste support untuk images

---

### x-date-time-picker

Date/Time picker component dengan calendar UI, theme-aware untuk light, dark, dan stylis themes.

```blade
{{-- Date Picker --}}
<x-date-time-picker
    name="date"
    type="date"
    label="Select Date"
/>

{{-- Time Picker --}}
<x-date-time-picker
    name="time"
    type="time"
    label="Select Time"
/>

{{-- DateTime Picker --}}
<x-date-time-picker
    name="datetime"
    type="datetime"
    label="Select Date & Time"
/>

{{-- Inline Calendar --}}
<x-date-time-picker
    name="date"
    type="date"
    :inline="true"
/>

{{-- With Min/Max Date --}}
<x-date-time-picker
    name="appointment"
    type="date"
    :min="date('Y-m-d')"
    :max="date('Y-m-d', strtotime('+30 days'))"
/>

{{-- Month Picker --}}
<x-date-time-picker
    name="month"
    type="month"
    label="Select Month"
/>

{{-- Year Picker --}}
<x-date-time-picker
    name="year"
    type="year"
    label="Select Year"
/>
```

**Props:**
- `name`: string (default: `'date'`) - Input name attribute
- `type`: `'date'` | `'time'` | `'datetime'` | `'month'` | `'year'` (default: `'date'`)
- `label`: string | null - Field label
- `placeholder`: string | null - Placeholder text
- `format`: string | null - Custom date format
- `min`: string | null - Minimum date
- `max`: string | null - Maximum date
- `value`: string | null - Selected value
- `inline`: boolean (default: false) - Show inline calendar
- `clearable`: boolean (default: true) - Show clear button
- `todayButton`: boolean (default: true) - Show today button
- `required`: boolean (default: false)
- `disabled`: boolean (default: false)
- `readonly`: boolean (default: false)
- `error`: string | null - Error message
- `class`: string untuk additional classes

**Theme Support:**
- **Light Theme**: White background dengan gray borders
- **Dark Theme**: Dark background dengan light text
- **Stylis Theme**: Teal accent colors untuk selected dates

**Visual Features:**
- Month/year navigation
- Today highlighting
- Selected date highlighting
- Disabled dates styling
- Weekend styling (optional)
- Smooth transitions
- Calendar grid layout
- Today button untuk quick selection
- Clear button untuk reset selection

---

### x-popover

Popover component dengan rich content, seperti tooltip tapi lebih kompleks, theme-aware untuk light, dark, dan stylis themes.

```blade
{{-- Basic Popover --}}
<x-popover title="Popover Title" content="Popover content here">
    <button>Click Me</button>
</x-popover>

{{-- With Custom Content --}}
<x-popover title="Help">
    <x-button>Help</x-button>

    @slot('popoverContent')
        <p class="mb-2">This is a custom popover content.</p>
        <ul class="list-disc list-inside">
            <li>Feature 1</li>
            <li>Feature 2</li>
            <li>Feature 3</li>
        </ul>
    @endslot
</x-popover>

{{-- Different Placements --}}
<x-popover placement="top" content="Top popover">
    <button>Top</button>
</x-popover>

<x-popover placement="bottom" content="Bottom popover">
    <button>Bottom</button>
</x-popover>

<x-popover placement="left" content="Left popover">
    <button>Left</button>
</x-popover>

<x-popover placement="right" content="Right popover">
    <button>Right</button>
</x-popover>

{{-- Hover Trigger --}}
<x-popover trigger="hover" content="Hover to see popover">
    <button>Hover Me</button>
</x-popover>

{{-- Custom Width --}}
<x-popover width="w-96" content="Wide popover with more content">
    <button>Wide Popover</button>
</x-popover>

{{-- Without Arrow --}}
<x-popover :arrow="false" content="Popover without arrow">
    <button>No Arrow</button>
</x-popover>

{{-- With Footer --}}
<x-popover title="Confirm Action">
    <x-button>Click</x-button>

    @slot('popoverContent')
        <p>Are you sure you want to proceed?</p>
    @endslot

    @slot('popoverFooter')
        <div class="flex justify-end gap-2 pt-3 border-t border-border">
            <button class="px-3 py-1.5 text-sm rounded-lg border border-border">Cancel</button>
            <button class="px-3 py-1.5 text-sm rounded-lg bg-danger text-white">Delete</button>
        </div>
    @endslot
</x-popover>
```

**Props:**
- `title`: string | null - Popover title
- `content`: string | null - Popover content (text or HTML)
- `placement`: `'top'` | `'bottom'` | `'left'` | `'right'` | `'auto'` (default: `'top'`)
- `trigger`: `'click'` | `'hover'` | `'focus'` | `'manual'` (default: `'click'`)
- `width`: string | null - Custom width (e.g., `'w-64'`, `'300px'`)
- `offset`: int (default: 8) - Distance from trigger element
- `arrow`: boolean (default: true) - Show arrow
- `closeOnClickOutside`: boolean (default: true) - Close when clicking outside
- `disabled`: boolean (default: false)
- `class`: string untuk additional classes

**Slots:**
- `popoverContent` - Custom popover content (overrides `content` prop)
- `popoverFooter` - Optional footer section

**JavaScript Methods:**
```javascript
// Open popover programmatically
$el.querySelector('[x-data*="popoverController"]').open();

// Close popover programmatically
$el.querySelector('[x-data*="popoverController"]').close();

// Toggle popover
$el.querySelector('[x-data*="popoverController"]').toggle();
```

**Theme Support:**
- **Light Theme**: White background dengan shadow
- **Dark Theme**: Dark background dengan light text
- **Stylis Theme**: Glassmorphism effect dengan backdrop blur

**Visual Features:**
- Smooth fade-in/scale-in animations
- Arrow indicator pointing to trigger
- Auto-positioning dengan boundary detection
- Close on click outside
- Optional close button
- Responsive positioning (mobile: centered)

---

## Accessibility Features

### Font Size Scaling

Control ukuran font untuk readability:

```blade
<div x-data="accessibilityController">
    {{-- Font size controls --}}
    <div class="flex items-center gap-2">
        <button @click="decreaseFontSize()">A-</button>
        <span x-text="fontSize"></span>
        <button @click="increaseFontSize()">A+</button>
        <button @click="resetFontSize()">Reset</button>
    </div>

    {{-- Custom scale slider --}}
    <input
        type="range"
        min="75"
        max="150"
        :value="customScale"
        @input="setCustomScale($event.target.value)"
    />
</div>
```

**Methods:**
- `setFontSize(size)` - Set preset size: `'small'` | `'medium'` | `'large'` | `'extra-large'`
- `setCustomScale(scale)` - Set custom scale (75-150%)
- `increaseFontSize()` - Increase by one preset
- `decreaseFontSize()` - Decrease by one preset
- `resetFontSize()` - Reset to medium (100%)

### High Contrast Mode

Toggle high contrast untuk visibility:

```blade
<div x-data="accessibilityController">
    <label class="flex items-center gap-2">
        <input
            type="checkbox"
            :checked="highContrast"
            @change="toggleHighContrast()"
        />
        <span>High Contrast Mode</span>
    </label>
</div>
```

**Methods:**
- `toggleHighContrast()` - Toggle high contrast mode
- `setHighContrast(enabled)` - Set high contrast state

**Effects:**
- White text on black background
- Yellow primary color untuk maximum contrast
- Increased border visibility
- Enhanced text readability

### Reduced Motion Mode

Disable animations untuk users dengan motion sensitivity:

```blade
<div x-data="accessibilityController">
    <label class="flex items-center gap-2">
        <input
            type="checkbox"
            :checked="reducedMotion"
            @change="toggleReducedMotion()"
        />
        <span>Reduce Motion</span>
    </label>
</div>
```

**Methods:**
- `toggleReducedMotion()` - Toggle reduced motion mode
- `setReducedMotion(enabled)` - Set reduced motion state

**Effects:**
- Disables semua CSS animations
- Disables transitions
- Sets scroll-behavior to auto
- Auto-detects system preference

### RTL Support

Enable right-to-left layout untuk Arabic, Hebrew, dll:

```blade
<html dir="rtl">
    {{-- Content akan otomatis flip --}}
</html>
```

**Supported Elements:**
- Flex containers (auto-flipped)
- Margins/paddings (start/end based)
- Borders (start/end based)
- Text alignment
- Icons dengan `.rtl-flip` class
- Tables
- Forms
- Modals & dropdowns

### Print Styles

Optimized printing:

```blade
{{-- Hide element saat print --}}
<div class="no-print">...</div>

{{-- Show only saat print --}}
<div class="print-only">...</div>

{{-- Page break --}}
<div class="page-break-before"></div>
<div class="page-break-after"></div>

 {{-- Avoid break inside --}}
<div class="page-break-inside-avoid">...</div>
```

**Print Features:**
- Auto-hide non-essential elements
- Black text on white background
- Links with URLs
- Optimized table printing
- Page break controls
- Maximum width content
- Remove shadows/effects

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
