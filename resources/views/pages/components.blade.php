@extends('layouts.app')

@section('title', 'Components - Roxwood Health Medical Center')

@section('page-title', 'Components')
@section('page-description', 'Component Library - Showcase semua komponen yang tersedia')

@section('content')

 {{-- Buttons Section --}}
 <x-section>
    <h2 class="text-xl font-bold text-text-primary mb-4 theme-dark:text-white">Buttons</h2>
    <x-card title="Button Variants">
        <div class="flex flex-wrap items-center gap-3">
            <x-button variant="primary">Primary</x-button>
            <x-button variant="secondary">Secondary</x-button>
            <x-button variant="success">Success</x-button>
            <x-button variant="danger">Danger</x-button>
            <x-button variant="warning">Warning</x-button>
            <x-button variant="info">Info</x-button>
            <x-button variant="ghost">Ghost</x-button>
            <x-button variant="link">Link</x-button>
        </div>
    </x-card>

    <x-card title="Button Sizes" class="mt-4">
        <div class="flex flex-wrap items-center gap-3">
            <x-button variant="primary" size="sm">Small</x-button>
            <x-button variant="primary" size="default">Default</x-button>
            <x-button variant="primary" size="lg">Large</x-button>
        </div>
    </x-card>

    <x-card title="Button with Icons" class="mt-4">
        <div class="flex flex-wrap items-center gap-3">
            <x-button variant="primary" :icon="'<svg class=\'w-5 h-5\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M12 4v16m8-8H4\'/></svg>'">
                Add New
            </x-button>
            <x-button variant="danger" :icon="'<svg class=\'w-5 h-5\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16\'/></svg>'">
                Delete
            </x-button>
            <x-button variant="success" iconPosition="right" :icon="'<svg class=\'w-5 h-5\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M14 5l7 7m0 0l-7 7m7-7H3\'/></svg>'">
                Next
            </x-button>
        </div>
    </x-card>
</x-section>

 {{-- Alerts Section --}}
 <x-section class="mt-6">
    <h2 class="text-xl font-bold text-text-primary mb-4 theme-dark:text-white">Alerts</h2>

    <x-alert type="success" :dismissible="true">
        <strong>Success!</strong> Your changes have been saved successfully.
    </x-alert>

    <x-alert type="danger" class="mt-3">
        <strong>Error!</strong> Something went wrong. Please try again.
    </x-alert>

    <x-alert type="warning" class="mt-3">
        <strong>Warning!</strong> Please review your input before submitting.
    </x-alert>

    <x-alert type="info" class="mt-3">
        <strong>Info:</strong> This is an informational message for you.
    </x-alert>
</x-section>

 {{-- Badges Section --}}
 <x-section class="mt-6">
    <h2 class="text-xl font-bold text-text-primary mb-4 theme-dark:text-white">Badges</h2>
    <x-card>
        <div class="flex flex-wrap items-center gap-3">
            <x-badge variant="default">Default</x-badge>
            <x-badge variant="primary">Primary</x-badge>
            <x-badge variant="success">Success</x-badge>
            <x-badge variant="danger">Danger</x-badge>
            <x-badge variant="warning">Warning</x-badge>
            <x-badge variant="info">Info</x-badge>
        </div>

        <div class="flex flex-wrap items-center gap-3 mt-4">
            <x-badge variant="success" :dot="true">Online</x-badge>
            <x-badge variant="danger" :dot="true">Busy</x-badge>
            <x-badge variant="warning" :dot="true">Away</x-badge>
        </div>
    </x-card>
</x-section>

 {{-- Forms Section --}}
 <x-section class="mt-6">
    <h2 class="text-xl font-bold text-text-primary mb-4 theme-dark:text-white">Form Elements</h2>
    <x-grid :cols="1" :gap="'default'" :cols-sm="2">
        <x-card title="Input Fields">
            <form class="space-y-4">
                <x-input
                    type="text"
                    name="name"
                    label="Full Name"
                    placeholder="Enter your name"
                    :required="true"
                />

                <x-input
                    type="email"
                    name="email"
                    label="Email Address"
                    placeholder="you@example.com"
                />

                <x-input
                    type="password"
                    name="password"
                    label="Password"
                    placeholder="•••••••••"
                />

                <x-input
                    type="text"
                    name="disabled"
                    label="Disabled Input"
                    placeholder="This is disabled"
                    :disabled="true"
                />
            </form>
        </x-card>

        <x-card title="Select & Textarea">
            <form class="space-y-4">
                <x-select
                    name="department"
                    label="Department"
                    :options="[
                        ['value' => 'cardio', 'label' => 'Cardiology'],
                        ['value' => 'neuro', 'label' => 'Neurology'],
                        ['value' => 'ortho', 'label' => 'Orthopedics'],
                        ['value' => 'pedia', 'label' => 'Pediatrics'],
                    ]"
                    placeholder="Select a department"
                />

                <div>
                    <label class="block text-sm font-medium text-text-primary mb-1.5 theme-dark:text-white">
                        Message
                    </label>
                    <textarea
                        rows="4"
                        placeholder="Enter your message"
                        class="w-full rounded-lg border border-border px-4 py-2.5 text-sm outline-none transition-all duration-200 bg-white text-text-primary placeholder:text-text-muted focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 theme-dark:bg-slate-800 theme-dark:border-slate-700 theme-dark:text-white theme-dark:focus:border-primary-400 theme-dark:focus:ring-primary-400/20"
                    ></textarea>
                </div>

                <div class="flex items-center gap-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" class="w-4 h-4 rounded border-border text-primary-500 focus:ring-primary-500 theme-dark:bg-slate-800 theme-dark:border-slate-700">
                        <span class="text-sm text-text-secondary theme-dark:text-slate-400">Remember me</span>
                    </label>
                </div>
            </form>
        </x-card>
    </x-grid>
</x-section>

 {{-- Avatars Section --}}
 <x-section class="mt-6">
    <h2 class="text-xl font-bold text-text-primary mb-4 theme-dark:text-white">Avatars</h2>
    <x-card>
        <div class="flex flex-wrap items-center gap-4">
            <p class="text-sm text-text-secondary theme-dark:text-slate-400 w-full mb-2">Sizes:</p>
            <x-avatar name="John Doe" size="xs" />
            <x-avatar name="Jane Smith" size="sm" />
            <x-avatar name="Robert Johnson" size="md" />
            <x-avatar name="Emily Davis" size="lg" />
            <x-avatar name="Michael Brown" size="xl" />
        </div>

        <div class="flex flex-wrap items-center gap-4 mt-6">
            <p class="text-sm text-text-secondary theme-dark:text-slate-400 w-full mb-2">With Status:</p>
            <x-avatar name="Online User" size="lg" status="online" />
            <x-avatar name="Offline User" size="lg" status="offline" />
            <x-avatar name="Away User" size="lg" status="away" />
            <x-avatar name="Busy User" size="lg" status="busy" />
        </div>
    </x-card>
</x-section>

 {{-- Cards Section --}}
 <x-section class="mt-6">
    <h2 class="text-xl font-bold text-text-primary mb-4 theme-dark:text-white">Cards</h2>
    <x-grid :cols="1" :gap="'default'" :cols-sm="2" :cols-lg="3">
        <x-card
            title="Default Card"
            subtitle="This is a card subtitle"
        >
            <p class="text-text-secondary theme-dark:text-slate-400">
                This is the content area of the card. You can put any content here.
            </p>
        </x-card>

        <x-card
            title="Hoverable Card"
            :hoverable="true"
            :shadow="'lg'"
        >
            <p class="text-text-secondary theme-dark:text-slate-400">
                This card has a hover effect. Try hovering over it!
            </p>
        </x-card>

        <x-card
            title="No Padding"
            :padding="'none'"
        >
            <img src="https://images.unsplash.com/photo-1519494026892-80bbd2d6fd0d?w=400&h=200&fit=crop" alt="Hospital" class="w-full h-32 object-cover">
            <div class="p-4">
                <p class="text-text-secondary theme-dark:text-slate-400">
                    Card with image and custom content.
                </p>
            </div>
        </x-card>
    </x-grid>
</x-section>

@endsection
