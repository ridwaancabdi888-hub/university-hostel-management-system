<x-dashboard-layout title="Allocate Room">
    <x-management-tabs active="allocations" />

    <div class="mb-4">
        <a href="{{ route('allocations.index') }}" class="text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">&larr; Back to Allocation History</a>
    </div>

    @php
        $selectedRoom = $selectedRoomId ? $rooms->firstWhere('id', $selectedRoomId) : null;
    @endphp

    <div class="max-w-2xl rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
        <form method="GET" action="{{ route('allocations.create') }}">
            <div>
                <x-input-label for="student_profile_id" value="Student" />
                <x-select id="student_profile_id" name="student_profile_id" class="mt-1 block w-full" onchange="this.form.submit()" required>
                    <option value="">Select an unallocated student</option>
                    @foreach ($students as $student)
                        <option value="{{ $student->id }}" @selected($selectedStudentId == $student->id)>
                            {{ $student->user->name }} ({{ $student->student_id }})
                        </option>
                    @endforeach
                </x-select>
                @if ($students->isEmpty())
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Every student already has an active room allocation.</p>
                @endif
            </div>

            <div class="mt-4">
                <x-input-label for="room_id" value="Room" />
                <x-select id="room_id" name="room_id" class="mt-1 block w-full" onchange="this.form.submit()" required>
                    <option value="">Select a room with available beds</option>
                    @foreach ($rooms as $room)
                        <option value="{{ $room->id }}" @selected($selectedRoomId == $room->id)>
                            {{ $room->room_number }} — {{ $room->floor->block->name }} ({{ $room->capacity - $room->occupied_beds }} of {{ $room->capacity }} beds free)
                        </option>
                    @endforeach
                </x-select>
                @if ($rooms->isEmpty())
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">No rooms currently have available beds.</p>
                @endif
            </div>

            <noscript>
                <div class="mt-4">
                    <x-secondary-button type="submit">Continue</x-secondary-button>
                </div>
            </noscript>
        </form>

        @if ($selectedStudentId && $selectedRoom)
            <form method="POST" action="{{ route('allocations.store') }}" class="mt-6 border-t border-gray-200 pt-6 dark:border-gray-700">
                @csrf
                <input type="hidden" name="student_profile_id" value="{{ $selectedStudentId }}">
                <input type="hidden" name="room_id" value="{{ $selectedRoom->id }}">

                <x-input-label for="bed_number" value="Bed" />
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

                <x-input-error :messages="$errors->get('student_profile_id')" class="mt-2" />
                <x-input-error :messages="$errors->get('room_id')" class="mt-2" />

                <div class="mt-6 flex items-center justify-end gap-3">
                    <a href="{{ route('allocations.index') }}">
                        <x-secondary-button type="button">Cancel</x-secondary-button>
                    </a>
                    <x-primary-button>Allocate Room</x-primary-button>
                </div>
            </form>
        @endif
    </div>
</x-dashboard-layout>
