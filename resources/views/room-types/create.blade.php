<x-dashboard-layout title="Add Room Type">
    <x-management-tabs active="room-types" />

    <div class="max-w-2xl rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
        <form method="POST" action="{{ route('room-types.store') }}">
            @csrf

            <x-room-types.form />

            <div class="mt-6 flex items-center justify-end gap-3">
                <a href="{{ route('room-types.index') }}">
                    <x-secondary-button type="button">Cancel</x-secondary-button>
                </a>
                <x-primary-button>Create Room Type</x-primary-button>
            </div>
        </form>
    </div>
</x-dashboard-layout>
