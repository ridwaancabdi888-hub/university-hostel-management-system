@props(['category'])

@php
    $classes = [
        'maintenance' => 'bg-indigo-50 text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-400',
        'complaint' => 'bg-pink-50 text-pink-700 dark:bg-pink-500/10 dark:text-pink-400',
    ];
@endphp

<span class="inline-flex items-center rounded-full px-sm py-1 font-label-sm {{ $classes[$category->value] }}">
    {{ $category->label() }}
</span>
