@props(['status'])

@php
    $classes = [
        'pending' => 'bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400',
        'in_progress' => 'bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400',
        'verification' => 'bg-purple-50 text-purple-700 dark:bg-purple-500/10 dark:text-purple-400',
        'completed' => 'bg-green-50 text-green-700 dark:bg-green-500/10 dark:text-green-400',
    ];
@endphp

<span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium {{ $classes[$status->value] }}">
    {{ $status->label() }}
</span>
