<x-dashboard-layout title="Blocks">
    <x-management-tabs active="blocks" />

    <div class="mb-4 flex items-center justify-between">
        <p class="text-sm text-gray-500 dark:text-gray-400">Manage residential blocks within each hostel.</p>
        <a href="{{ route('blocks.create') }}" class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white hover:bg-indigo-500">
            Add New Block
        </a>
    </div>

    <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-900/50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Name</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Hostel</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Code</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Floors</th>
                    <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($blocks as $block)
                    <tr>
                        <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-gray-100">{{ $block->name }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $block->hostel->name }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $block->code ?? '—' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $block->floors_count }}</td>
                        <td class="px-4 py-3 text-right text-sm">
                            <a href="{{ route('blocks.edit', $block) }}" class="font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">Edit</a>
                            <span class="mx-2 text-gray-300 dark:text-gray-600">|</span>
                            <x-delete-button :action="route('blocks.destroy', $block)" confirm="Delete this block?" class="inline">Delete</x-delete-button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-gray-400">No blocks yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $blocks->links() }}
    </div>
</x-dashboard-layout>
