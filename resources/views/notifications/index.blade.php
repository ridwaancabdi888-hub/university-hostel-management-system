<x-dashboard-layout title="Notifications">
    <div class="mb-4 flex items-center justify-between">
        <p class="text-sm text-gray-500 dark:text-gray-400">Everything you've been notified about.</p>
        @if ($notifications->contains(fn ($n) => is_null($n->read_at)))
            <form method="POST" action="{{ route('notifications.read-all') }}">
                @csrf
                <x-secondary-button type="submit">Mark all read</x-secondary-button>
            </form>
        @endif
    </div>

    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
        <ul class="divide-y divide-gray-200 dark:divide-gray-700">
            @forelse ($notifications as $notification)
                <li>
                    <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                        @csrf
                        <button type="submit" class="flex w-full items-start justify-between gap-4 px-6 py-4 text-left hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <div>
                                <div class="flex items-center gap-2">
                                    @if (! $notification->read_at)
                                        <span class="h-2 w-2 rounded-full bg-indigo-600"></span>
                                    @endif
                                    <span class="font-medium text-gray-900 dark:text-gray-100">{{ $notification->data['title'] ?? 'Notification' }}</span>
                                </div>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $notification->data['message'] ?? '' }}</p>
                            </div>
                            <span class="whitespace-nowrap text-xs text-gray-400 dark:text-gray-500">{{ $notification->created_at->diffForHumans() }}</span>
                        </button>
                    </form>
                </li>
            @empty
                <li class="px-6 py-10 text-center text-sm text-gray-500 dark:text-gray-400">No notifications yet.</li>
            @endforelse
        </ul>
    </div>

    <div class="mt-4">
        {{ $notifications->links() }}
    </div>
</x-dashboard-layout>
