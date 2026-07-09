@props(['invoice'])

@php
    $state = $invoice->isOverdue() ? 'overdue' : $invoice->status->value;

    $labels = [
        'unpaid' => 'Unpaid',
        'paid' => 'Paid',
        'cancelled' => 'Cancelled',
        'overdue' => 'Overdue',
    ];

    $classes = [
        'unpaid' => 'bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400',
        'paid' => 'bg-green-50 text-green-700 dark:bg-green-500/10 dark:text-green-400',
        'cancelled' => 'bg-gray-100 text-gray-600 dark:bg-gray-700/50 dark:text-gray-400',
        'overdue' => 'bg-red-50 text-red-700 dark:bg-red-500/10 dark:text-red-400',
    ];
@endphp

<span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium {{ $classes[$state] }}">
    {{ $labels[$state] }}
</span>
