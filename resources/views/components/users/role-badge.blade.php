@props(['role'])

@php
    $classes = [
        'admin' => 'bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400',
        'warden' => 'bg-purple-50 text-purple-700 dark:bg-purple-500/10 dark:text-purple-400',
        'accountant' => 'bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400',
        'student' => 'bg-secondary-container/50 text-on-secondary-container dark:bg-night-surface-high dark:text-night-on-surface-variant',
    ];
@endphp

<span class="inline-flex items-center rounded-full px-sm py-1 font-label-sm {{ $classes[$role->value] }}">
    {{ $role->label() }}
</span>
