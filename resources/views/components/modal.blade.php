@props([
    'name',
    'show' => false,
    'maxWidth' => '2xl',
    'label' => 'Dialog',
])

@php
$maxWidth = [
    'sm' => 'sm:max-w-sm',
    'md' => 'sm:max-w-md',
    'lg' => 'sm:max-w-lg',
    'xl' => 'sm:max-w-xl',
    '2xl' => 'sm:max-w-2xl',
][$maxWidth];
@endphp

<div
    x-data="{
        show: @js($show),
        returnFocus: null,
        open() {
            this.returnFocus = document.activeElement;
            this.show = true;
        },
        close() {
            this.show = false;
        },
        moveFocus(direction) {
            window.modalFocus.getAdjacentFocusable(this.$refs.panel, document.activeElement, direction)?.focus();
        },
    }"
    x-init="$watch('show', value => {
        if (value) {
            document.body.classList.add('overflow-y-hidden');
            {{ $attributes->has('focusable') ? 'setTimeout(() => window.modalFocus.getAdjacentFocusable($refs.panel, null)?.focus(), 100)' : '' }}
        } else {
            document.body.classList.remove('overflow-y-hidden');
            window.modalFocus.restoreFocus(returnFocus);
        }
    })"
    x-on:open-modal.window="$event.detail == '{{ $name }}' ? open() : null"
    x-on:close-modal.window="$event.detail == '{{ $name }}' ? close() : null"
    x-on:close.stop="close()"
    x-on:keydown.escape.window="show && close()"
    x-on:keydown.tab.prevent="moveFocus($event.shiftKey ? -1 : 1)"
    x-show="show"
    class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50"
    style="display: {{ $show ? 'block' : 'none' }};"
>
    <div
        x-show="show"
        class="fixed inset-0 transform transition-all"
        x-on:click="close()"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div class="absolute inset-0 bg-gray-500 dark:bg-gray-900 opacity-75"></div>
    </div>

    <div
        x-ref="panel"
        x-show="show"
        role="dialog"
        aria-modal="true"
        aria-label="{{ $label }}"
        tabindex="-1"
        class="mb-6 bg-white dark:bg-gray-800 rounded-lg overflow-hidden shadow-xl transform transition-all sm:w-full {{ $maxWidth }} sm:mx-auto"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
    >
        {{ $slot }}
    </div>
</div>
