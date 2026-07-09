<x-dashboard-layout :title="$room->room_number">
    <div class="mb-4 flex items-center justify-between">
        <a href="{{ route('rooms.index') }}" class="text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">&larr; Back to Room Inventory</a>
        <a href="{{ route('rooms.edit', $room) }}" class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white hover:bg-indigo-500">
            Edit Room
        </a>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
        <div class="flex flex-wrap items-center gap-3">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">{{ $room->room_number }}</h2>
            <x-rooms.occupancy-badge :room="$room" />
        </div>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            {{ $room->floor->block->name }} — {{ $room->floor->name }} ({{ $room->floor->block->hostel->name }}) · {{ $room->roomType->name }}
        </p>
    </div>

    <div class="mt-6 rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
        <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Bed Assignment</h3>
        <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($beds as $bed)
                <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-700">
                    <div class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Bed {{ $bed['number'] }}</div>
                    @if ($bed['allocation'])
                        @php $occupant = $bed['allocation']->studentProfile; @endphp
                        <div class="mt-2 flex items-center gap-2">
                            <x-avatar :name="$occupant->user->name" :url="$occupant->photoUrl()" size="h-8 w-8" class="text-xs" />
                            <div>
                                <a href="{{ route('students.show', $occupant) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">{{ $occupant->user->name }}</a>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $occupant->student_id }}</div>
                            </div>
                        </div>
                        <div class="mt-3 flex items-center gap-2">
                            <a href="{{ route('allocations.transfer.form', $bed['allocation']) }}" class="text-xs font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">Transfer</a>
                            <span class="text-gray-300 dark:text-gray-600">|</span>
                            <x-delete-button :action="route('allocations.vacate', $bed['allocation'])" confirm="Vacate this bed?" class="text-xs">Vacate</x-delete-button>
                        </div>
                    @else
                        <div class="mt-2 text-sm text-gray-400 dark:text-gray-500">Vacant</div>
                        @if ($room->status === \App\Enums\RoomStatus::Available)
                            <a href="{{ route('allocations.create', ['room_id' => $room->id]) }}" class="mt-3 inline-block text-xs font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">Allocate</a>
                        @endif
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <div class="mt-6 rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
        <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Allocation History</h3>
        <div class="mt-4 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead>
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Student</th>
                        <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Bed</th>
                        <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Allocated</th>
                        <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Vacated</th>
                        <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($history as $entry)
                        <tr>
                            <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">{{ $entry->studentProfile->user->name }}</td>
                            <td class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">{{ $entry->bed_number }}</td>
                            <td class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">{{ $entry->allocated_at->format('M j, Y') }}</td>
                            <td class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">{{ $entry->vacated_at?->format('M j, Y') ?? '—' }}</td>
                            <td class="px-4 py-2 text-sm"><x-allocations.status-badge :status="$entry->status" /></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-gray-400">No allocation history for this room.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $history->links() }}
        </div>
    </div>
</x-dashboard-layout>
