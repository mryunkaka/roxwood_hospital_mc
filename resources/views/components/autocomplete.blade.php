{{-- Autocomplete/Select Component --}}
@props([
    'name' => null,
    'label' => null,
    'placeholder' => 'Select an option',
    'options' => [],
    'value' => null,
    'required' => false,
    'disabled' => false,
    'searchable' => true,
    'clearable' => true,
    'error' => null,
    'class' => ''
])

@php
    $componentId = 'autocomplete-' . uniqid();
    $optionsJson = json_encode($options);

    // Normalize options to always have value/label structure
    $normalizedOptions = [];
    foreach ($options as $key => $opt) {
        if (is_array($opt)) {
            $normalizedOptions[] = $opt;
        } else {
            $normalizedOptions[] = ['value' => $key, 'label' => $opt];
        }
    }
    $optionsJson = json_encode($normalizedOptions);

    // Find initial display value
    $initialLabel = '';
    if ($value) {
        foreach ($normalizedOptions as $opt) {
            if (isset($opt['value']) && $opt['value'] == $value) {
                $initialLabel = $opt['label'] ?? '';
                break;
            }
        }
    }
@endphp

<div class="w-full {{ $class }}" x-cloak x-data="{
    open: false,
    search: '',
    selected: {{ $value ? json_encode($value) : 'null' }},
    displayValue: {{ $initialLabel ? json_encode($initialLabel) : "''" }},
    options: {{ $optionsJson }},
    searchable: {{ $searchable ? 'true' : 'false' }},
    disabled: {{ $disabled ? 'true' : 'false' }},

    get visibleOptions() {
        if (!this.searchable || !this.search) return this.options;
        return this.options.filter(o =>
            o.label.toLowerCase().includes(this.search.toLowerCase())
        );
    },

    get selectedLabel() {
        if (!this.selected) return '';
        const opt = this.options.find(o => o.value === this.selected);
        return opt ? opt.label : '';
    },

    select(value, label) {
        this.selected = value;
        this.displayValue = label;
        this.open = false;
        this.search = '';
        this.$refs.input.dispatchEvent(new Event('change'));
    },

    clear() {
        this.selected = null;
        this.displayValue = '';
        this.search = '';
        this.$refs.input?.focus();
    },

    toggle() {
        if (this.disabled) return;
        this.open = !this.open;
        if (this.open) this.$refs.input?.focus();
    }
}">
    @if($label)
        <label for="{{ $componentId }}" class="block text-sm font-medium text-text-primary mb-1.5">
            {{ $label }}
            @if($required) <span class="text-danger-500 ml-1">*</span> @endif
        </label>
    @endif

    <div class="relative">
        {{-- Input Field --}}
        <div class="relative">
            <input
                id="{{ $componentId }}"
                @if($name) name="{{ $name }}" @endif
                type="text"
                x-model="displayValue"
                @focus="open = true"
                @input="search = $event.target.value"
                placeholder="{{ $placeholder }}"
                @if($required) required @endif
                @if($disabled) disabled @endif
                readonly="{{ !$searchable ? 'true' : 'false' }}"
                x-ref="input"
                class="w-full appearance-none rounded-xl border px-4 pr-10 py-3 text-sm outline-none transition-all duration-200
                       bg-surface border-border text-text-primary placeholder:text-text-hint
                       focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20
                       disabled:bg-surface-alt disabled:cursor-not-allowed
                       theme-dark:bg-slate-700 theme-dark:border-slate-600 theme-dark:text-white
                       theme-stylis:bg-white/80 theme-stylis:border-teal-200
                       {{ $error ? 'border-danger-500 focus:border-danger-500 focus:ring-danger-500/20' : '' }}
                       {{ $disabled ? 'opacity-50 cursor-not-allowed' : '' }}"
            />

            {{-- Dropdown Arrow --}}
            @if(!$disabled)
                <button
                    @click="toggle()"
                    type="button"
                    class="absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer"
                >
                    <svg class="w-5 h-5 text-text-secondary transition-transform duration-200"
                         :class="open ? 'rotate-180' : ''"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
            @endif

            {{-- Clear Button --}}
            @if($clearable && !$disabled)
                <button
                    @click="clear()"
                    type="button"
                    x-show="selected !== null"
                    class="absolute inset-y-0 right-8 flex items-center pr-3"
                >
                    <svg class="w-4 h-4 text-text-muted hover:text-text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            @endif
        </div>

        {{-- Error Message --}}
        @if($error)
            <p class="mt-1.5 text-xs text-danger-500">{{ $error }}</p>
        @endif

        {{-- Dropdown List --}}
        <div x-show="open"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
             @click.away="open = false"
             class="absolute z-50 w-full mt-1 max-h-60 overflow-auto rounded-xl shadow-xl
                    bg-surface border-border
                    theme-dark:bg-slate-700 theme-dark:border-slate-600
                    theme-stylis:bg-white/95 theme-stylis:border-teal-200 theme-stylis:backdrop-blur-xl
                    py-1"
             style="display: none;">

            @if(count($options) > 0)
                <template x-for="option in visibleOptions" :key="option.value">
                    <div @click="select(option.value, option.label)"
                         class="px-4 py-2.5 text-sm cursor-pointer transition-colors
                                flex items-center gap-3
                                text-text-secondary hover:bg-surface-hover
                                theme-dark:text-slate-300 theme-dark:hover:bg-slate-600/50
                                theme-stylis:text-slate-600 theme-stylis:hover:bg-teal-50"
                                :class="selected === option.value ? 'bg-primary-50 text-primary-700 theme-dark:bg-primary-900/30 theme-dark:text-primary-400 theme-stylis:bg-teal-50 theme-stylis:text-teal-700' : ''">
                        <span x-show="option.icon" x-html="option.icon" class="w-5 h-5 flex-shrink-0"></span>
                        <span x-text="option.label"></span>
                        <svg x-show="selected === option.value" class="w-4 h-4 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                    </div>
                </template>
            @else
                <div class="px-4 py-3 text-sm text-text-secondary text-center">
                    No options available
                </div>
            @endif
        </div>

        {{-- Hidden Input for Form Submission --}}
        @if($name)
            <input type="hidden" name="{{ $name }}" x-model="selected">
        @endif
    </div>
</div>
