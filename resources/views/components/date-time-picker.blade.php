{{-- Date/Time Picker Component --}}
{{-- Date, time, and datetime picker dengan calendar UI, theme-aware --}}

@props([
    'name' => 'date',
    'type' => 'date',
    'label' => null,
    'placeholder' => null,
    'format' => null,
    'min' => null,
    'max' => null,
    'value' => null,
    'clearable' => true,
    'todayButton' => true,
    'required' => false,
    'disabled' => false,
    'error' => null,
    'class' => ''
])

@php
    $uniqueId = 'datetime_' . uniqid();

    $formats = [
        'date' => 'Y-m-d',
        'time' => 'H:i',
        'datetime' => 'Y-m-d H:i',
        'month' => 'Y-m',
        'year' => 'Y'
    ];

    $displayFormat = $format ?? $formats[$type] ?? 'Y-m-d';
@endphp

<div class="datetime-picker-wrapper {{ $class }}" x-data="dateTimePickerController({ type: '{{ $type }}', value: '{{ $value ?? '' }}', min: '{{ $min ?? '' }}', max: '{{ $max ?? '' }}', clearable: {{ $clearable ? 'true' : 'false' }}, todayButton: {{ $todayButton ? 'true' : 'false' }} })">
    @if($label)
        <label class="block text-sm font-medium text-text-primary mb-2">
            {{ $label }}
            @if($required) <span class="text-danger-500">*</span> @endif
        </label>
    @endif

    <div class="relative">
        <input
            type="hidden"
            name="{{ $name }}"
            :value="value"
        />

        <input
            type="text"
            :id="'{{ $uniqueId }}'"
            readonly
            :placeholder="'{{ $placeholder ?? 'Select ' . $type }}'"
            :value="displayValue"
            @if($disabled) disabled @endif
            @if($required) required @endif
            class="w-full px-4 py-2.5 pr-24 rounded-xl border
                   bg-surface dark:bg-gray-800 text-text-primary dark:text-gray-100
                   placeholder:text-text-tertiary dark:placeholder:text-gray-500
                   focus:outline-none focus:ring-2
                   {{ $error ? 'border-danger-500 focus:ring-danger-500/50 focus:border-danger-500' : 'border-border dark:border-gray-600 focus:ring-primary-500/50 focus:border-primary-500 dark:focus:border-teal-500' }}
                   {{ $disabled ? 'opacity-50 cursor-not-allowed' : '' }}
                   cursor-pointer transition-all duration-200
                   theme-stylis:bg-white/90 theme-stylis:border-teal-200 theme-stylis:focus:ring-teal-500/50"
            @click="!disabled && (open = !open)"
            x-cloak
        />

        <div class="absolute right-2 top-1/2 -translate-y-1/2 flex items-center gap-1">
            <button type="button"
                    x-show="clearable && value"
                    @click.stop="clear(); open = false"
                    class="p-1.5 rounded-lg text-text-secondary hover:text-danger-500
                           hover:bg-danger-50 dark:hover:bg-danger-900/20 transition-colors"
                    x-cloak>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

            <button type="button"
                    @click.stop="open = !open"
                    class="p-1.5 rounded-lg text-text-secondary hover:text-primary-500 dark:hover:text-primary-400
                           hover:bg-primary-50 dark:hover:bg-primary-900/20 theme-stylis:hover:text-teal-600 theme-stylis:hover:bg-teal-50 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </button>
        </div>

        {{-- Calendar Dropdown --}}
        <div x-show="open"
             @click.outside="open = false"
             @keydown.escape.prevent="open = false"
             class="absolute z-50 mt-2 w-full sm:w-80 rounded-xl shadow-lg border
                    bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700
                    theme-stylis:bg-white theme-stylis:border-teal-200 theme-stylis:shadow-xl
                    calendar-dropdown"
             x-cloak>
            <div class="p-4">
                {{-- Month Navigation --}}
                <div class="flex items-center justify-between mb-4">
                    <button type="button" @click="prevMonth()" class="p-2 rounded-lg text-text-secondary dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 theme-stylis:hover:bg-teal-50 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </button>
                    <div class="text-lg font-semibold text-text-primary dark:text-white">
                        <span x-text="monthNames[currentMonth]"></span>
                        <span x-text="currentYear" class="ml-1"></span>
                    </div>
                    <button type="button" @click="nextMonth()" class="p-2 rounded-lg text-text-secondary dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 theme-stylis:hover:bg-teal-50 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>

                {{-- Day Headers --}}
                <div class="grid grid-cols-7 gap-1 mb-2">
                    <template x-for="day in dayNames" :key="day">
                        <div class="text-center text-xs font-semibold text-text-tertiary dark:text-gray-500 theme-stylis:text-teal-600/70 py-2" x-text="day"></div>
                    </template>
                </div>

                {{-- Calendar Days --}}
                <div class="grid grid-cols-7 gap-1">
                    <template x-for="(day, index) in calendarDays" :key="index">
                        <button type="button"
                                :disabled="isDisabled(day)"
                                @click="selectDay(day)"
                                class="aspect-square flex items-center justify-center rounded-lg text-sm
                                       text-text-primary dark:text-gray-200
                                       hover:bg-primary-50 dark:hover:bg-primary-900/20
                                       theme-stylis:hover:bg-teal-50
                                       disabled:opacity-30 disabled:cursor-not-allowed transition-colors"
                                :class="{
                                    'bg-primary-500 dark:bg-primary-600 text-white': isSelected(day),
                                    'border-2 border-primary-500 dark:border-primary-400': isToday(day) && !isSelected(day),
                                    'theme-stylis:bg-teal-500 theme-stylis:text-white': isSelected(day),
                                    'theme-stylis:border-teal-500': isToday(day) && !isSelected(day)
                                }"
                                x-show="day"
                                x-text="day">
                        </button>
                    </template>
                </div>

                {{-- Today Button --}}
                <div x-show="todayButton" class="mt-4 pt-4 border-t border-border dark:border-gray-700 theme-stylis:border-teal-200">
                    <button type="button" @click="selectToday()" class="w-full py-2 px-4 text-sm font-medium
                           text-primary-600 dark:text-primary-400
                           hover:bg-primary-50 dark:hover:bg-primary-900/20
                           theme-stylis:text-teal-600 theme-stylis:hover:bg-teal-50
                           rounded-lg transition-colors">
                        {{ __('messages.today') ?? 'Today' }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    @if($error)
        <p class="mt-2 text-sm text-danger-500">{{ $error }}</p>
    @endif
</div>

@once
@push('scripts')
<script>
function dateTimePickerController(options = {}) {
    return {
        type: options.type || 'date',
        value: options.value || null,
        min: options.min || null,
        max: options.max || null,
        clearable: options.clearable !== false,
        todayButton: options.todayButton !== false,
        open: false,
        currentMonth: new Date().getMonth(),
        currentYear: new Date().getFullYear(),

        get monthNames() {
            return ['January', 'February', 'March', 'April', 'May', 'June',
                    'July', 'August', 'September', 'October', 'November', 'December'];
        },

        get dayNames() {
            return ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'];
        },

        get displayValue() {
            if (!this.value) return '';
            const date = new Date(this.value);
            if (isNaN(date.getTime())) return '';

            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
                           'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

            if (this.type === 'month') {
                return `${months[date.getMonth()]} ${date.getFullYear()}`;
            }
            if (this.type === 'year') {
                return date.getFullYear().toString();
            }

            const day = date.getDate().toString().padStart(2, '0');
            const month = (date.getMonth() + 1).toString().padStart(2, '0');
            const year = date.getFullYear();

            if (this.type === 'datetime') {
                const hours = date.getHours().toString().padStart(2, '0');
                const minutes = date.getMinutes().toString().padStart(2, '0');
                return `${year}-${month}-${day} ${hours}:${minutes}`;
            }

            return `${year}-${month}-${day}`;
        },

        get calendarDays() {
            const firstDay = new Date(this.currentYear, this.currentMonth, 1);
            const lastDay = new Date(this.currentYear, this.currentMonth + 1, 0);
            const startDay = firstDay.getDay();
            const totalDays = lastDay.getDate();

            const days = [];
            for (let i = 0; i < startDay; i++) {
                days.push(null);
            }
            for (let i = 1; i <= totalDays; i++) {
                days.push(i);
            }
            return days;
        },

        get today() {
            const today = new Date();
            return { day: today.getDate(), month: today.getMonth(), year: today.getFullYear() };
        },

        isSelected(day) {
            if (!this.value || !day) return false;
            const date = new Date(this.value);
            return date.getDate() === day &&
                   date.getMonth() === this.currentMonth &&
                   date.getFullYear() === this.currentYear;
        },

        isToday(day) {
            return day === this.today.day &&
                   this.currentMonth === this.today.month &&
                   this.currentYear === this.today.year;
        },

        isDisabled(day) {
            if (!day) return true;
            const date = new Date(this.currentYear, this.currentMonth, day);

            if (this.min) {
                const minDate = new Date(this.min);
                if (date < minDate) return true;
            }
            if (this.max) {
                const maxDate = new Date(this.max);
                if (date > maxDate) return true;
            }
            return false;
        },

        selectDay(day) {
            if (this.isDisabled(day)) return;
            const date = new Date(this.currentYear, this.currentMonth, day);
            this.value = date.toISOString().split('T')[0];
            this.open = false;
        },

        prevMonth() {
            if (this.currentMonth === 0) {
                this.currentMonth = 11;
                this.currentYear--;
            } else {
                this.currentMonth--;
            }
        },

        nextMonth() {
            if (this.currentMonth === 11) {
                this.currentMonth = 0;
                this.currentYear++;
            } else {
                this.currentMonth++;
            }
        },

        selectToday() {
            const today = new Date();
            this.currentMonth = today.getMonth();
            this.currentYear = today.getFullYear();
            this.value = today.toISOString().split('T')[0];
        },

        clear() {
            this.value = null;
        }
    };
}
</script>
@endpush
@endonce

@once
@push('styles')
<style>
/* Calendar dropdown theme-specific styles */
.calendar-dropdown {
    box-shadow: 0 10px 40px -10px rgba(0, 0, 0, 0.1);
}

.theme-dark .calendar-dropdown {
    box-shadow: 0 10px 40px -10px rgba(0, 0, 0, 0.4);
}

.theme-stylis .calendar-dropdown {
    backdrop-filter: blur(12px);
    box-shadow: 0 10px 40px -10px rgba(20, 184, 166, 0.15);
}

/* Calendar buttons hover states */
.theme-stylis .calendar-dropdown button:hover:not(:disabled) {
    transform: translateY(-1px);
}
</style>
@endpush
@endonce
