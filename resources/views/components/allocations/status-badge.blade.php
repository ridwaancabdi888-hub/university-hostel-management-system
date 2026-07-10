@props(['status'])

@php
    $classes = [
        'active' => 'bg-green-50 text-green-700 dark:bg-green-500/10 dark:text-green-400',
        'transferred' => 'bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400',
        'vacated' => 'bg-secondary-container/50 text-on-secondary-container dark:bg-night-surface-high dark:text-night-on-surface-variant',
    ];
@endphp

<span class="inline-flex items-center rounded-full px-sm py-1 font-label-sm {{ $classes[$status->value] }}">
    {{ $status->label() }}
</span>
