@props(['room'])

@php
    $state = $room->occupancyStatus();

    $labels = [
        'available' => 'Available',
        'partial' => 'Partial',
        'full' => 'Full',
        'unavailable' => 'Maintenance',
    ];

    $classes = [
        'available' => 'bg-green-50 text-green-700 dark:bg-green-500/10 dark:text-green-400',
        'partial' => 'bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400',
        'full' => 'bg-red-50 text-red-700 dark:bg-red-500/10 dark:text-red-400',
        'unavailable' => 'bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400',
    ];

    $dots = [
        'available' => 'bg-green-500',
        'partial' => 'bg-blue-500',
        'full' => 'bg-red-500',
        'unavailable' => 'bg-amber-500',
    ];
@endphp

<span class="inline-flex items-center gap-1.5 rounded-full px-sm py-1 font-label-sm {{ $classes[$state] }}">
    <span class="h-1.5 w-1.5 rounded-full {{ $dots[$state] }}"></span>
    {{ $labels[$state] }}
    @if ($state !== 'unavailable')
        ({{ $room->occupied_beds }}/{{ $room->capacity }})
    @endif
</span>
