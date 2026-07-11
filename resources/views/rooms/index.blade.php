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
                <x-input-label for="capacity" value="Beds" />
                <x-select id="capacity" name="capacity" class="mt-1 block w-full">
                    <option value="">Any Capacity</option>
                    @foreach ($capacities as $capacity)
                        <option value="{{ $capacity }}" @selected(($filters['capacity'] ?? '') == $capacity)>{{ $capacity }} bed{{ $capacity > 1 ? 's' : '' }}</option>
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

    <div x-data="{ selected: [], allIds: {{ Illuminate\Support\Js::from($rooms->pluck('id')) }} }">
        {{-- A plain <form> cannot be nested inside another <form> — HTML
             silently breaks the outer one the moment it hits the first
             inner one, which each row's Delete button renders. So this
             bulk-photo form stays a sibling of the table below rather than
             wrapping it; both share the same Alpine "selected" state. --}}
        <form method="POST" action="{{ route('rooms.bulk-photo') }}" enctype="multipart/form-data">
            @csrf
            <template x-for="id in selected" :key="id">
                <input type="hidden" name="room_ids[]" :value="id">
            </template>

            <div class="glass-card mb-6 rounded-lg p-md" x-show="selected.length > 0" style="display: none;">
                <p class="font-label-md font-medium text-on-surface dark:text-night-on-surface">
                    <span x-text="selected.length"></span> room(s) selected — apply one photo to all of them:
                </p>
                <div class="mt-3 grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <x-input-label for="bulk_photo" value="Upload File" />
                        <input id="bulk_photo" name="photo" type="file" accept="image/*"
                            class="mt-1 block w-full font-body-md text-on-surface-variant file:mr-4 file:rounded-DEFAULT file:border-0 file:bg-secondary-container/50 file:px-4 file:py-2 file:font-label-md file:font-semibold file:text-primary hover:file:bg-secondary-container dark:text-night-on-surface-variant dark:file:bg-night-secondary-container dark:file:text-night-primary">
                    </div>
                    <div>
                        <x-input-label for="bulk_photo_url" value="Or Paste Image URL" />
                        <x-text-input id="bulk_photo_url" name="photo_url" type="url" class="mt-1 block w-full" placeholder="https://example.com/photo.jpg" />
                    </div>
                </div>
                <x-input-error :messages="$errors->get('photo')" class="mt-2" />
                <x-input-error :messages="$errors->get('photo_url')" class="mt-2" />
                <div class="mt-3">
                    <x-primary-button type="submit">Apply Photo to Selected</x-primary-button>
                </div>
            </div>
        </form>

        <div class="glass-card overflow-x-auto rounded-lg">
            <table class="min-w-full divide-y divide-outline-variant/15 dark:divide-night-border">
                <thead class="bg-secondary-container/20 dark:bg-night-surface-high/50">
                    <tr>
                        <th class="w-10 px-4 py-3">
                            <input type="checkbox" class="rounded border-outline-variant/40 text-primary focus:ring-primary dark:border-night-border"
                                @change="selected = $event.target.checked ? [...allIds] : []"
                                :checked="selected.length > 0 && selected.length === allIds.length">
                        </th>
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
                                <input type="checkbox" class="rounded border-outline-variant/40 text-primary focus:ring-primary dark:border-night-border"
                                    value="{{ $room->id }}"
                                    @change="$event.target.checked ? selected.push({{ $room->id }}) : selected = selected.filter(i => i !== {{ $room->id }})"
                                    :checked="selected.includes({{ $room->id }})">
                            </td>
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
                            <td colspan="6" class="px-4 py-6 text-center font-body-md text-on-surface-variant dark:text-night-on-surface-variant">No rooms match your search.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $rooms->links() }}
    </div>
</x-dashboard-layout>
