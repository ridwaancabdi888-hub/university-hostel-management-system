@props(['room' => null, 'floors', 'roomTypes'])

<div>
    <x-input-label for="floor_id" value="Floor" />
    <x-select id="floor_id" name="floor_id" class="mt-1 block w-full" required>
        <option value="">Select a floor</option>
        @foreach ($floors as $floor)
            <option value="{{ $floor->id }}" @selected(old('floor_id', $room->floor_id ?? '') == $floor->id)>
                {{ $floor->block->name }} — {{ $floor->name }} ({{ $floor->block->hostel->name }})
            </option>
        @endforeach
    </x-select>
    <x-input-error :messages="$errors->get('floor_id')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="room_number" value="Room Number" />
    <x-text-input id="room_number" name="room_number" type="text" class="mt-1 block w-full" required autofocus
        placeholder="e.g. A-102"
        :value="old('room_number', $room->room_number ?? '')" />
    <x-input-error :messages="$errors->get('room_number')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="room_type_id" value="Room Type" />
    <x-select id="room_type_id" name="room_type_id" class="mt-1 block w-full" required>
        <option value="">Select a room type</option>
        @foreach ($roomTypes as $roomType)
            <option value="{{ $roomType->id }}" @selected(old('room_type_id', $room->room_type_id ?? '') == $roomType->id)>
                {{ $roomType->name }} ({{ $roomType->default_capacity }} beds)
            </option>
        @endforeach
    </x-select>
    <x-input-error :messages="$errors->get('room_type_id')" class="mt-2" />
</div>

<div class="mt-4 grid grid-cols-2 gap-4">
    <div>
        <x-input-label for="capacity" value="Bed Capacity" />
        <x-text-input id="capacity" name="capacity" type="number" min="1" max="20" class="mt-1 block w-full" required
            :value="old('capacity', $room->capacity ?? '')" />
        <x-input-error :messages="$errors->get('capacity')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="occupied_beds" value="Occupied Beds" />
        <x-text-input id="occupied_beds" name="occupied_beds" type="number" min="0" max="20" class="mt-1 block w-full" required
            :value="old('occupied_beds', $room->occupied_beds ?? 0)" />
        <x-input-error :messages="$errors->get('occupied_beds')" class="mt-2" />
    </div>
</div>

<div class="mt-4">
    <x-input-label for="status" value="Status" />
    <x-select id="status" name="status" class="mt-1 block w-full" required>
        @foreach (\App\Enums\RoomStatus::cases() as $status)
            <option value="{{ $status->value }}" @selected(old('status', $room->status?->value ?? 'available') === $status->value)>
                {{ $status->label() }}
            </option>
        @endforeach
    </x-select>
    <x-input-error :messages="$errors->get('status')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="notes" value="Notes" />
    <textarea id="notes" name="notes" rows="3"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600">{{ old('notes', $room->notes ?? '') }}</textarea>
    <x-input-error :messages="$errors->get('notes')" class="mt-2" />
</div>
