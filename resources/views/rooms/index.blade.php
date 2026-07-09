<x-dashboard-layout title="Room Inventory">
    <x-management-tabs active="rooms" />

    <div class="mb-4 flex items-center justify-between">
        <p class="text-sm text-gray-500 dark:text-gray-400">Manage room allocations, track occupancy, and optimize student housing.</p>
        <a href="{{ route('rooms.create') }}" class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white hover:bg-indigo-500">
            Add New Room
        </a>
    </div>

    <form method="GET" action="{{ route('rooms.index') }}" class="mb-6 rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
        <x-input-label for="search" value="Search" />
        <x-text-input id="search" name="search" type="text" class="mt-1 block w-full" value="{{ $filters['search'] ?? '' }}" placeholder="Search by room number, block, or hostel..." />

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
            <a href="{{ route('rooms.index') }}" class="text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">Reset</a>
        </div>
    </form>

    <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-900/50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Room No</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Block</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Floor</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Room Type</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Occupancy</th>
                    <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($rooms as $room)
                    <tr>
                        <td class="px-4 py-3 text-sm font-semibold">
                            <a href="{{ route('rooms.show', $room) }}" class="text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">{{ $room->room_number }}</a>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $room->floor->block->name }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $room->floor->name }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $room->roomType->name }}</td>
                        <td class="px-4 py-3 text-sm"><x-rooms.occupancy-badge :room="$room" /></td>
                        <td class="px-4 py-3 text-right text-sm">
                            <a href="{{ route('rooms.show', $room) }}" class="font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">View</a>
                            <span class="mx-2 text-gray-300 dark:text-gray-600">|</span>
                            <a href="{{ route('rooms.edit', $room) }}" class="font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">Edit</a>
                            <span class="mx-2 text-gray-300 dark:text-gray-600">|</span>
                            <x-delete-button :action="route('rooms.destroy', $room)" confirm="Delete this room?" class="inline">Delete</x-delete-button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-gray-400">No rooms match your search.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $rooms->links() }}
    </div>
</x-dashboard-layout>
