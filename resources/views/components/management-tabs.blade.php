@props(['active'])

@php
    $tabs = [
        'rooms' => ['label' => 'Rooms', 'route' => 'rooms.index'],
        'allocations' => ['label' => 'Allocation History', 'route' => 'allocations.index'],
        'room-types' => ['label' => 'Room Types', 'route' => 'room-types.index'],
        'floors' => ['label' => 'Floors', 'route' => 'floors.index'],
        'blocks' => ['label' => 'Blocks', 'route' => 'blocks.index'],
        'hostels' => ['label' => 'Hostels', 'route' => 'hostels.index'],
    ];
@endphp

<div class="mb-6 border-b border-outline-variant/20 dark:border-night-border">
    <nav class="-mb-px flex gap-6 overflow-x-auto">
        @foreach ($tabs as $key => $tab)
            <a
                href="{{ route($tab['route']) }}"
                @class([
                    'whitespace-nowrap border-b-2 px-1 py-3 font-label-md',
                    'border-primary text-primary dark:border-night-primary dark:text-night-primary' => $active === $key,
                    'border-transparent text-on-surface-variant hover:border-outline-variant hover:text-on-surface dark:text-night-on-surface-variant dark:hover:text-night-on-surface' => $active !== $key,
                ])
            >
                {{ $tab['label'] }}
            </a>
        @endforeach
    </nav>
</div>
