@props(['status'])

@php
    $classes = [
        'pending' => 'bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400',
        'active' => 'bg-green-50 text-green-700 dark:bg-green-500/10 dark:text-green-400',
        'suspended' => 'bg-red-50 text-red-700 dark:bg-red-500/10 dark:text-red-400',
        'graduated' => 'bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400',
        'withdrawn' => 'bg-gray-100 text-gray-600 dark:bg-gray-700/50 dark:text-gray-400',
    ];
@endphp

<span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium {{ $classes[$status->value] }}">
    {{ $status->label() }}
</span>
