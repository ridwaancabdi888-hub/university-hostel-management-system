<x-dashboard-layout title="Maintenance">
    <div class="mb-4 flex items-center justify-between">
        <p class="text-sm text-gray-500 dark:text-gray-400">
            @if (auth()->user()->role === \App\Enums\Role::Student)
                Submit and track your maintenance requests and complaints.
            @else
                Track and resolve maintenance requests and complaints across the hostel.
            @endif
        </p>
        <a href="{{ route('maintenance.create') }}" class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white hover:bg-indigo-500">
            New Request
        </a>
    </div>

    <form method="GET" action="{{ route('maintenance.index') }}" class="mb-6 rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div>
                <x-input-label for="category" value="Category" />
                <x-select id="category" name="category" class="mt-1 block w-full">
                    <option value="">All</option>
                    @foreach (\App\Enums\MaintenanceCategory::cases() as $category)
                        <option value="{{ $category->value }}" @selected(($filters['category'] ?? '') === $category->value)>{{ $category->label() }}</option>
                    @endforeach
                </x-select>
            </div>

            <div>
                <x-input-label for="status" value="Status" />
                <x-select id="status" name="status" class="mt-1 block w-full">
                    <option value="">All</option>
                    @foreach (\App\Enums\MaintenanceStatus::cases() as $status)
                        <option value="{{ $status->value }}" @selected(($filters['status'] ?? '') === $status->value)>{{ $status->label() }}</option>
                    @endforeach
                </x-select>
            </div>

            <div>
                <x-input-label for="priority" value="Priority" />
                <x-select id="priority" name="priority" class="mt-1 block w-full">
                    <option value="">All</option>
                    @foreach (\App\Enums\MaintenancePriority::cases() as $priority)
                        <option value="{{ $priority->value }}" @selected(($filters['priority'] ?? '') === $priority->value)>{{ $priority->label() }}</option>
                    @endforeach
                </x-select>
            </div>
        </div>

        <div class="mt-4 flex items-center gap-3">
            <x-primary-button type="submit">Apply Filters</x-primary-button>
            <a href="{{ route('maintenance.index') }}" class="text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">Reset</a>
        </div>
    </form>

    <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-900/50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Title</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Student</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Category</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Priority</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Assigned To</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Submitted</th>
                    <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($tickets as $ticket)
                    <tr>
                        <td class="px-4 py-3 text-sm font-medium">
                            <a href="{{ route('maintenance.show', $ticket) }}" class="text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">{{ $ticket->title }}</a>
                            @if ($ticket->room)
                                <div class="text-xs text-gray-500 dark:text-gray-400">Room {{ $ticket->room->room_number }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $ticket->studentProfile->user->name }}</td>
                        <td class="px-4 py-3 text-sm"><x-maintenance.category-badge :category="$ticket->category" /></td>
                        <td class="px-4 py-3 text-sm"><x-maintenance.priority-badge :priority="$ticket->priority" /></td>
                        <td class="px-4 py-3 text-sm"><x-maintenance.status-badge :status="$ticket->status" /></td>
                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $ticket->assignedStaff->name ?? 'Unassigned' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $ticket->created_at->format('M j, Y') }}</td>
                        <td class="px-4 py-3 text-right text-sm">
                            <a href="{{ route('maintenance.show', $ticket) }}" class="font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-gray-400">No requests match your search.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $tickets->links() }}
    </div>
</x-dashboard-layout>
