@props(['priority'])

@php
    $classes = [
        'low' => 'bg-gray-100 text-gray-600 dark:bg-gray-700/50 dark:text-gray-400',
        'medium' => 'bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400',
        'high' => 'bg-orange-50 text-orange-700 dark:bg-orange-500/10 dark:text-orange-400',
        'urgent' => 'bg-red-50 text-red-700 dark:bg-red-500/10 dark:text-red-400',
    ];
@endphp

<span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium uppercase tracking-wide {{ $classes[$priority->value] }}">
    {{ $priority->label() }}
</span>
