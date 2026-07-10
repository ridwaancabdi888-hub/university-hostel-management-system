@props(['priority'])

@php
    $classes = [
        'low' => 'bg-secondary-container/50 text-on-secondary-container dark:bg-night-surface-high dark:text-night-on-surface-variant',
        'medium' => 'bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400',
        'high' => 'bg-orange-50 text-orange-700 dark:bg-orange-500/10 dark:text-orange-400',
        'urgent' => 'bg-red-50 text-red-700 dark:bg-red-500/10 dark:text-red-400',
    ];
@endphp

<span class="inline-flex items-center rounded-full px-sm py-1 font-label-sm uppercase tracking-wide {{ $classes[$priority->value] }}">
    {{ $priority->label() }}
</span>
