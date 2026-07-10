@props(['status'])

@php
    $classes = [
        'pending' => 'bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400',
        'approved' => 'bg-green-50 text-green-700 dark:bg-green-500/10 dark:text-green-400',
        'rejected' => 'bg-red-50 text-red-700 dark:bg-red-500/10 dark:text-red-400',
    ];
@endphp

<span class="inline-flex items-center rounded-full px-sm py-1 font-label-sm {{ $classes[$status->value] }}">
    {{ $status->label() }}
</span>
