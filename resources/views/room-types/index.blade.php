<x-dashboard-layout title="Room Types">
    <x-management-tabs active="room-types" />

    <div class="mb-4 flex items-center justify-between">
        <p class="text-sm text-gray-500 dark:text-gray-400">Manage the room type catalog used across all rooms.</p>
        <a href="{{ route('room-types.create') }}" class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white hover:bg-indigo-500">
            Add New Room Type
        </a>
    </div>

    <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-900/50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Name</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Default Capacity</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Rooms Using It</th>
                    <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($roomTypes as $roomType)
                    <tr>
                        <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-gray-100">{{ $roomType->name }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $roomType->default_capacity }} beds</td>
                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $roomType->rooms_count }}</td>
                        <td class="px-4 py-3 text-right text-sm">
                            <a href="{{ route('room-types.edit', $roomType) }}" class="font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">Edit</a>
                            <span class="mx-2 text-gray-300 dark:text-gray-600">|</span>
                            <x-delete-button :action="route('room-types.destroy', $roomType)" confirm="Delete this room type?" class="inline">Delete</x-delete-button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-gray-400">No room types yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $roomTypes->links() }}
    </div>
</x-dashboard-layout>
