<x-dashboard-layout title="Allocation History">
    <x-management-tabs active="allocations" />

    <div class="mb-4 flex items-center justify-between">
        <p class="text-sm text-gray-500 dark:text-gray-400">Track every room allocation, transfer, and vacancy.</p>
        @if (auth()->user()->role !== \App\Enums\Role::Accountant)
            <a href="{{ route('allocations.create') }}" class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white hover:bg-indigo-500">
                Allocate Room
            </a>
        @endif
    </div>

    <form method="GET" action="{{ route('allocations.index') }}" class="mb-6 rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
        <x-input-label for="search" value="Search" />
        <x-text-input id="search" name="search" type="text" class="mt-1 block w-full" value="{{ $filters['search'] ?? '' }}" placeholder="Search by student name, student ID, or room number..." />

        <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div>
                <x-input-label for="status" value="Status" />
                <x-select id="status" name="status" class="mt-1 block w-full">
                    <option value="">All Statuses</option>
                    @foreach (\App\Enums\AllocationStatus::cases() as $status)
                        <option value="{{ $status->value }}" @selected(($filters['status'] ?? '') === $status->value)>{{ $status->label() }}</option>
                    @endforeach
                </x-select>
            </div>
        </div>

        <div class="mt-4 flex items-center gap-3">
            <x-primary-button type="submit">Apply Filters</x-primary-button>
            <a href="{{ route('allocations.index') }}" class="text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">Reset</a>
        </div>
    </form>

    <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-900/50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Student</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Room</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Bed</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Allocated</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Vacated</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($allocations as $allocation)
                    <tr>
                        <td class="px-4 py-3 text-sm">
                            <a href="{{ route('students.show', $allocation->studentProfile) }}" class="font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">{{ $allocation->studentProfile->user->name }}</a>
                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $allocation->studentProfile->student_id }}</div>
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <a href="{{ route('rooms.show', $allocation->room) }}" class="font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">{{ $allocation->room->room_number }}</a>
                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $allocation->room->floor->block->name }}</div>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $allocation->bed_number }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $allocation->allocated_at->format('M j, Y') }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $allocation->vacated_at?->format('M j, Y') ?? '—' }}</td>
                        <td class="px-4 py-3 text-sm"><x-allocations.status-badge :status="$allocation->status" /></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-gray-400">No allocations match your search.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $allocations->links() }}
    </div>
</x-dashboard-layout>
