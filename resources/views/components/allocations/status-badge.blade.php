@props(['status'])

@php
    $classes = [
        'active' => 'bg-green-50 text-green-700 dark:bg-green-500/10 dark:text-green-400',
        'transferred' => 'bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400',
        'vacated' => 'bg-gray-100 text-gray-600 dark:bg-gray-700/50 dark:text-gray-400',
    ];
@endphp

<span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium {{ $classes[$status->value] }}">
    {{ $status->label() }}
</span>
