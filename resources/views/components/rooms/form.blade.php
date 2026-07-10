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
        @if ($room && $room->occupied_beds > 0)
            <p class="mt-1 font-label-sm text-on-surface-variant dark:text-night-on-surface-variant">Can't be set below {{ $room->occupied_beds }} — the beds currently occupied.</p>
        @endif
    </div>

    <div>
        <x-input-label value="Occupied Beds" />
        <p class="mt-1 flex h-[48px] w-full items-center rounded-DEFAULT border-2 border-outline-variant/30 bg-surface-container px-sm font-body-md text-on-surface-variant dark:border-night-border dark:bg-night-surface-high dark:text-night-on-surface-variant">
            {{ $room->occupied_beds ?? 0 }} — updates automatically from allocations
        </p>
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
        class="mt-1 block w-full rounded-DEFAULT border-2 border-outline-variant/40 bg-surface-container-lowest px-sm py-2 font-body-md text-on-surface shadow-none focus:border-primary focus:ring-0 dark:border-night-border dark:bg-night-surface dark:text-night-on-surface dark:focus:border-night-primary">{{ old('notes', $room->notes ?? '') }}</textarea>
    <x-input-error :messages="$errors->get('notes')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="photo" value="Room Photo" />
    <input id="photo" name="photo" type="file" accept="image/*"
        class="mt-1 block w-full font-body-md text-on-surface-variant file:mr-4 file:rounded-DEFAULT file:border-0 file:bg-secondary-container/50 file:px-4 file:py-2 file:font-label-md file:font-semibold file:text-primary hover:file:bg-secondary-container dark:text-night-on-surface-variant dark:file:bg-night-secondary-container dark:file:text-night-primary">
    <x-input-error :messages="$errors->get('photo')" class="mt-2" />
    @if ($room?->photoUrl())
        <div class="mt-2 flex items-center gap-2">
            <img src="{{ $room->photoUrl() }}" alt="{{ $room->room_number }}" class="h-12 w-16 rounded-DEFAULT object-cover">
            <span class="font-label-sm text-on-surface-variant dark:text-night-on-surface-variant">Current photo — upload a new file to replace it.</span>
        </div>
    @endif
</div>
