<x-dashboard-layout title="Transfer Student">
    <div class="mb-4">
        <a href="{{ route('students.show', $allocation->studentProfile) }}" class="text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">&larr; Back to Profile</a>
    </div>

    @php
        $selectedRoom = $selectedRoomId ? $rooms->firstWhere('id', $selectedRoomId) : null;
    @endphp

    <div class="max-w-2xl rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
        <div class="mb-6 rounded-lg bg-gray-50 p-4 text-sm dark:bg-gray-900/50">
            <p class="text-gray-500 dark:text-gray-400">Transferring</p>
            <p class="font-medium text-gray-900 dark:text-gray-100">{{ $allocation->studentProfile->user->name }} ({{ $allocation->studentProfile->student_id }})</p>
            <p class="mt-2 text-gray-500 dark:text-gray-400">Current room</p>
            <p class="font-medium text-gray-900 dark:text-gray-100">{{ $allocation->room->room_number }} — Bed {{ $allocation->bed_number }} ({{ $allocation->room->floor->block->name }})</p>
        </div>

        <form method="GET" action="{{ route('allocations.transfer.form', $allocation) }}">
            <x-input-label for="room_id" value="New Room" />
            <x-select id="room_id" name="room_id" class="mt-1 block w-full" onchange="this.form.submit()" required>
                <option value="">Select a room with available beds</option>
                @foreach ($rooms as $room)
                    <option value="{{ $room->id }}" @selected($selectedRoomId == $room->id)>
                        {{ $room->room_number }} — {{ $room->floor->block->name }} ({{ $room->capacity - $room->occupied_beds }} of {{ $room->capacity }} beds free)
                    </option>
                @endforeach
            </x-select>

            <noscript>
                <div class="mt-4">
                    <x-secondary-button type="submit">Continue</x-secondary-button>
                </div>
            </noscript>
        </form>

        @if ($selectedRoom)
            <form method="POST" action="{{ route('allocations.transfer', $allocation) }}" class="mt-6 border-t border-gray-200 pt-6 dark:border-gray-700">
                @csrf
                <input type="hidden" name="room_id" value="{{ $selectedRoom->id }}">

                <x-input-label for="bed_number" value="New Bed" />
                <x-select id="bed_number" name="bed_number" class="mt-1 block w-full" required>
                    @foreach ($selectedRoom->availableBedNumbers() as $bed)
                        <option value="{{ $bed }}" @selected(old('bed_number') == $bed)>Bed {{ $bed }}</option>
                    @endforeach
                </x-select>
                <x-input-error :messages="$errors->get('bed_number')" class="mt-2" />

                <div class="mt-4">
                    <x-input-label for="notes" value="Notes (optional)" />
                    <textarea id="notes" name="notes" rows="2"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600">{{ old('notes') }}</textarea>
                    <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                </div>

                <x-input-error :messages="$errors->get('room_id')" class="mt-2" />

                <div class="mt-6 flex items-center justify-end gap-3">
                    <a href="{{ route('students.show', $allocation->studentProfile) }}">
                        <x-secondary-button type="button">Cancel</x-secondary-button>
                    </a>
                    <x-primary-button>Transfer Student</x-primary-button>
                </div>
            </form>
        @endif
    </div>
</x-dashboard-layout>
