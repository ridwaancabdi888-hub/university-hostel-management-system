<x-dashboard-layout title="Hostels">
    <x-management-tabs active="hostels" />

    <div class="mb-4 flex items-center justify-between">
        <p class="text-sm text-gray-500 dark:text-gray-400">Manage the top-level hostel properties.</p>
        <a href="{{ route('hostels.create') }}" class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white hover:bg-indigo-500">
            Add New Hostel
        </a>
    </div>

    <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-900/50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Name</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Code</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Address</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Blocks</th>
                    <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($hostels as $hostel)
                    <tr>
                        <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-gray-100">{{ $hostel->name }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $hostel->code ?? '—' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $hostel->address ?? '—' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $hostel->blocks_count }}</td>
                        <td class="px-4 py-3 text-right text-sm">
                            <a href="{{ route('hostels.edit', $hostel) }}" class="font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">Edit</a>
                            <span class="mx-2 text-gray-300 dark:text-gray-600">|</span>
                            <x-delete-button :action="route('hostels.destroy', $hostel)" confirm="Delete this hostel?" class="inline">Delete</x-delete-button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-gray-400">No hostels yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $hostels->links() }}
    </div>
</x-dashboard-layout>
