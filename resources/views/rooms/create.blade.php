<x-dashboard-layout title="Add Room">
    <x-management-tabs active="rooms" />

    <div class="max-w-2xl rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
        <form method="POST" action="{{ route('rooms.store') }}" enctype="multipart/form-data">
            @csrf

            <x-rooms.form :floors="$floors" :room-types="$roomTypes" />

            <div class="mt-6 flex items-center justify-end gap-3">
                <a href="{{ route('rooms.index') }}">
                    <x-secondary-button type="button">Cancel</x-secondary-button>
                </a>
                <x-primary-button>Create Room</x-primary-button>
            </div>
        </form>
    </div>
</x-dashboard-layout>
