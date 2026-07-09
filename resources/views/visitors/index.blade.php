<x-dashboard-layout title="Visitors">
    <div class="mb-4 flex items-center justify-between">
        <p class="text-sm text-gray-500 dark:text-gray-400">
            @if (auth()->user()->role === \App\Enums\Role::Student)
                Register and track visitors coming to see you.
            @else
                Review and approve visitor registrations across the hostel.
            @endif
        </p>
        <a href="{{ route('visitors.create') }}" class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white hover:bg-indigo-500">
            Register Visitor
        </a>
    </div>

    <form method="GET" action="{{ route('visitors.index') }}" class="mb-6 rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div>
                <x-input-label for="status" value="Status" />
                <x-select id="status" name="status" class="mt-1 block w-full">
                    <option value="">All</option>
                    @foreach (\App\Enums\VisitorStatus::cases() as $status)
                        <option value="{{ $status->value }}" @selected(($filters['status'] ?? '') === $status->value)>{{ $status->label() }}</option>
                    @endforeach
                </x-select>
            </div>
        </div>

        <div class="mt-4 flex items-center gap-3">
            <x-primary-button type="submit">Apply Filters</x-primary-button>
            <a href="{{ route('visitors.index') }}" class="text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">Reset</a>
        </div>
    </form>

    <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-900/50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Visitor</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Visiting</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Purpose</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Expected</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Status</th>
                    <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($visitors as $visitor)
                    <tr>
                        <td class="px-4 py-3 text-sm font-medium">
                            <a href="{{ route('visitors.show', $visitor) }}" class="text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">{{ $visitor->name }}</a>
                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $visitor->phone }}</div>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $visitor->studentProfile->user->name }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ \Illuminate\Support\Str::limit($visitor->purpose, 40) }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $visitor->expected_at->format('M j, Y g:i A') }}</td>
                        <td class="px-4 py-3 text-sm"><x-visitors.status-badge :status="$visitor->status" /></td>
                        <td class="px-4 py-3 text-right text-sm">
                            <a href="{{ route('visitors.show', $visitor) }}" class="font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-gray-400">No visitor registrations match your search.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $visitors->links() }}
    </div>
</x-dashboard-layout>
