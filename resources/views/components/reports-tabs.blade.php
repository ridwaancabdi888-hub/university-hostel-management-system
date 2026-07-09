@props(['active'])

@php
    $allTabs = [
        'occupancy' => ['label' => 'Occupancy', 'route' => 'reports.occupancy'],
        'billing' => ['label' => 'Billing', 'route' => 'reports.billing'],
        'payments' => ['label' => 'Payments', 'route' => 'reports.payments'],
        'students' => ['label' => 'Students', 'route' => 'reports.students'],
        'hostels' => ['label' => 'Hostels', 'route' => 'reports.hostels'],
    ];

    $accessible = \App\Support\ReportAccess::for(auth()->user()->role);
    $tabs = collect($allTabs)->only($accessible);
@endphp

<div class="mb-6 border-b border-gray-200 dark:border-gray-700">
    <nav class="-mb-px flex gap-6 overflow-x-auto">
        @foreach ($tabs as $key => $tab)
            <a
                href="{{ route($tab['route']) }}"
                @class([
                    'whitespace-nowrap border-b-2 px-1 py-3 text-sm font-medium',
                    'border-indigo-600 text-indigo-600 dark:border-indigo-400 dark:text-indigo-400' => $active === $key,
                    'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200' => $active !== $key,
                ])
            >
                {{ $tab['label'] }}
            </a>
        @endforeach
    </nav>
</div>
