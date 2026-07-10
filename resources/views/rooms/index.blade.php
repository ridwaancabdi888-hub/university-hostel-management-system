<x-dashboard-layout title="Room Inventory">
    <x-management-tabs active="rooms" />

    <div class="mb-4 flex items-center justify-between">
        <p class="font-body-md text-on-surface-variant dark:text-night-on-surface-variant">Manage room allocations, track occupancy, and optimize student housing.</p>
        <a href="{{ route('rooms.create') }}" class="inline-flex items-center gap-2 rounded-DEFAULT bg-primary px-md py-sm font-label-md text-on-primary hover:shadow-lg hover:shadow-primary/25 dark:bg-night-primary dark:text-night-on-primary transition-all">
            <span class="material-symbols-outlined text-[18px]">add</span>
            Add New Room
        </a>
    </div>

    <form method="GET" action="{{ route('rooms.index') }}" class="glass-card mb-6 rounded-lg p-md">
        <div class="relative">
            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-outline dark:text-night-on-surface-variant">
                <span class="material-symbols-outlined text-[20px]">search</span>
            </span>
            <x-text-input id="search" name="search" type="text" class="block w-full !rounded-full !pl-11" value="{{ $filters['search'] ?? '' }}" placeholder="Search by room number, block, or hostel..." />
        </div>

        <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-4">
            <div>
                <x-input-label for="hostel_id" value="Hostel" />
                <x-select id="hostel_id" name="hostel_id" class="mt-1 block w-full">
                    <option value="">All Hostels</option>
                    @foreach ($hostels as $hostel)
                        <option value="{{ $hostel->id }}" @selected(($filters['hostel_id'] ?? '') == $hostel->id)>{{ $hostel->name }}</option>
                    @endforeach
                </x-select>
            </div>

            <div>
                <x-input-label for="block_id" value="Block" />
                <x-select id="block_id" name="block_id" class="mt-1 block w-full">
                    <option value="">All Blocks</option>
                    @foreach ($blocks as $block)
                        <option value="{{ $block->id }}" @selected(($filters['block_id'] ?? '') == $block->id)>{{ $block->name }}</option>
                    @endforeach
                </x-select>
            </div>

            <div>
                <x-input-label for="room_type_id" value="Room Type" />
                <x-select id="room_type_id" name="room_type_id" class="mt-1 block w-full">
                    <option value="">All Types</option>
                    @foreach ($roomTypes as $roomType)
                        <option value="{{ $roomType->id }}" @selected(($filters['room_type_id'] ?? '') == $roomType->id)>{{ $roomType->name }}</option>
                    @endforeach
                </x-select>
            </div>

            <div>
                <x-input-label for="status" value="Status" />
                <x-select id="status" name="status" class="mt-1 block w-full">
                    <option value="">All</option>
                    <option value="available" @selected(($filters['status'] ?? '') === 'available')>Available</option>
                    <option value="full" @selected(($filters['status'] ?? '') === 'full')>Full</option>
                    <option value="maintenance" @selected(($filters['status'] ?? '') === 'maintenance')>Maintenance</option>
                </x-select>
            </div>
        </div>

        <div class="mt-4 flex items-center gap-3">
            <x-primary-button type="submit">Apply Filters</x-primary-button>
            <a href="{{ route('rooms.index') }}" class="font-label-md text-on-surface-variant hover:text-on-surface dark:text-night-on-surface-variant dark:hover:text-night-on-surface">Reset</a>
        </div>
    </form>

    <div class="glass-card overflow-x-auto rounded-lg">
        <table class="min-w-full divide-y divide-outline-variant/15 dark:divide-night-border">
            <thead class="bg-secondary-container/20 dark:bg-night-surface-high/50">
                <tr>
                    <th class="px-4 py-3 text-left font-label-sm uppercase tracking-wider text-on-surface-variant dark:text-night-on-surface-variant">Room</th>
                    <th class="px-4 py-3 text-left font-label-sm uppercase tracking-wider text-on-surface-variant dark:text-night-on-surface-variant">Block</th>
                    <th class="px-4 py-3 text-left font-label-sm uppercase tracking-wider text-on-surface-variant dark:text-night-on-surface-variant">Floor</th>
                    <th class="px-4 py-3 text-left font-label-sm uppercase tracking-wider text-on-surface-variant dark:text-night-on-surface-variant">Occupancy</th>
                    <th class="px-4 py-3 text-right font-label-sm uppercase tracking-wider text-on-surface-variant dark:text-night-on-surface-variant">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/15 dark:divide-night-border">
                @forelse ($rooms as $room)
                    <tr class="transition hover:bg-secondary-container/10 dark:hover:bg-night-surface-high/40">
                        <td class="px-4 py-3">
                            <a href="{{ route('rooms.show', $room) }}" class="flex items-center gap-3">
                                <x-rooms.thumbnail :room="$room" />
                                <div>
                                    <div class="font-label-md font-semibold text-primary dark:text-night-primary">{{ $room->room_number }}</div>
                                    <div class="font-label-sm text-on-surface-variant dark:text-night-on-surface-variant">{{ $room->roomType->name }}</div>
                                </div>
                            </a>
                        </td>
                        <td class="px-4 py-3 font-body-md text-on-surface-variant dark:text-night-on-surface-variant">{{ $room->floor->block->name }}</td>
                        <td class="px-4 py-3 font-body-md text-on-surface-variant dark:text-night-on-surface-variant">{{ $room->floor->name }}</td>
                        <td class="px-4 py-3"><x-rooms.occupancy-badge :room="$room" /></td>
                        <td class="px-4 py-3 text-right font-label-md">
                            <a href="{{ route('rooms.show', $room) }}" class="font-medium text-primary hover:underline dark:text-night-primary">View</a>
                            <span class="mx-2 text-outline-variant dark:text-night-border">|</span>
                            <a href="{{ route('rooms.edit', $room) }}" class="font-medium text-primary hover:underline dark:text-night-primary">Edit</a>
                            <span class="mx-2 text-outline-variant dark:text-night-border">|</span>
                            <x-delete-button :action="route('rooms.destroy', $room)" confirm="Delete this room?" class="inline">Delete</x-delete-button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center font-body-md text-on-surface-variant dark:text-night-on-surface-variant">No rooms match your search.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $rooms->links() }}
    </div>
</x-dashboard-layout>
