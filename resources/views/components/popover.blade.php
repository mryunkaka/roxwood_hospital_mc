{{-- Popover Component --}}
{{-- Popover dengan rich content, seperti tooltip tapi lebih kompleks, theme-aware --}}

@props([
    'title' => null,
    'content' => null,
    'placement' => 'top',
    'trigger' => 'click',
    'width' => null,
    'offset' => 8,
    'arrow' => true,
    'closeOnClickOutside' => true,
    'disabled' => false,
    'class' => ''
])

@php
    $uniqueId = 'popover_' . uniqid();
    $widthClass = $width ?? 'w-64';
@endphp

<span class="popover-wrapper inline-flex relative {{ $class }}"
      x-data="{
          isOpen: false,
          toggle() {
              if (!{{ $disabled ? 'true' : 'false' }}) {
                  this.isOpen = !this.isOpen;
              }
          },
          open() {
              if (!{{ $disabled ? 'true' : 'false' }}) {
                  this.isOpen = true;
              }
          },
          close() {
              this.isOpen = false;
          }
      }">

    {{-- Trigger Element (Slot) --}}
    <span class="popover-trigger inline-flex"
          @click="{{ $trigger === 'click' ? 'toggle()' : '' }}"
          @mouseenter="{{ $trigger === 'hover' ? 'open()' : '' }}"
          @mouseleave="{{ $trigger === 'hover' ? 'close()' : '' }}"
          @focus="{{ $trigger === 'focus' ? 'open()' : '' }}"
          @blur="{{ $trigger === 'focus' ? 'close()' : '' }}"
          @click.outside="{{ $closeOnClickOutside && $trigger === 'click' ? 'close()' : '' }}"
          :aria-expanded="isOpen"
          :aria-haspopup="{{ $disabled ? 'null' : 'dialog' }}">
        {{ $slot }}
    </span>

    {{-- Popover Content --}}
    <template x-if="!{{ $disabled ? 'true' : 'false' }} && isOpen">
        <div class="popover-content absolute z-50
                      {{ $widthClass }}
                      bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-lg
                      text-text-primary dark:text-gray-100
                      theme-stylis:bg-white theme-stylis:border-teal-200 theme-stylis:shadow-xl
                      transition-all duration-200"
             x-cloak
             :class="{
                 'bottom-full left-1/2 -translate-x-1/2 mb-2': '{{ $placement }}' === 'top',
                 'top-full left-1/2 -translate-x-1/2 mt-2': '{{ $placement }}' === 'bottom',
                 'right-full top-1/2 -translate-y-1/2 mr-2': '{{ $placement }}' === 'left',
                 'left-full top-1/2 -translate-y-1/2 ml-2': '{{ $placement }}' === 'right'
             }"
             role="dialog"
             :aria-labelledby="'{{ $uniqueId }}'">

            {{-- Arrow --}}
            <template x-if="{{ $arrow ? 'true' : 'false' }}">
                <div class="popover-arrow absolute w-3 h-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rotate-45 theme-stylis:bg-white theme-stylis:border-teal-200"
                     :class="{
                         '-bottom-1.5 left-1/2 -translate-x-1/2': '{{ $placement }}' === 'top',
                         '-top-1.5 left-1/2 -translate-x-1/2': '{{ $placement }}' === 'bottom',
                         '-right-1.5 top-1/2 -translate-y-1/2': '{{ $placement }}' === 'left',
                         '-left-1.5 top-1/2 -translate-y-1/2': '{{ $placement }}' === 'right'
                     }"></div>
            </template>

            <div class="relative z-10">
                {{-- Title --}}
                @if($title)
                    <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 theme-stylis:border-teal-200">
                        <h3 id="{{ $uniqueId }}" class="text-sm font-semibold text-text-primary dark:text-white theme-stylis:text-gray-800">
                            {{ $title }}
                        </h3>
                    </div>
                @endif

                {{-- Content --}}
                <div class="p-4 text-sm text-text-secondary dark:text-gray-300 theme-stylis:text-gray-600">
                    @if($content)
                        {!! $content !!}
                    @endif

                    @hasSection('content')
                        @yield('content')
                    @endif
                </div>

                {{-- Footer slot (optional) --}}
                @hasSection('footer')
                    <div class="px-4 py-3 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-200 dark:border-gray-700 theme-stylis:bg-teal-50/50 theme-stylis:border-teal-200 rounded-b-xl">
                        @yield('footer')
                    </div>
                @endif
            </div>
        </div>
    </template>
</span>

@once
@push('styles')
<style>
/* Popover animations */
.popover-content {
    animation: popover-fade-in 0.2s ease-out;
}

@keyframes popover-fade-in {
    from {
        opacity: 0;
        transform: scale(0.95);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

/* Arrow styling */
.popover-arrow {
    transition: all 0.2s ease-in-out;
}

/* Hover state for trigger */
.popover-trigger:hover {
    cursor: pointer;
}

/* Theme-specific popover styles */
.theme-stylis .popover-content {
    backdrop-filter: blur(12px);
    box-shadow: 0 20px 40px -10px rgba(20, 184, 166, 0.15);
}

.theme-dark .popover-content {
    box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.4);
}

/* Mobile adjustments */
@media (max-width: 640px) {
    .popover-content {
        max-width: 90vw !important;
        left: 50% !important;
        transform: translateX(-50%) !important;
    }
}
</style>
@endpush
@endonce
