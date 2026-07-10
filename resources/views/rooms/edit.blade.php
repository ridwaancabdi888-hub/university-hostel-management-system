<x-dashboard-layout title="Edit Room">
    <x-management-tabs active="rooms" />

    <div class="glass-card max-w-2xl rounded-lg p-lg">
        <form method="POST" action="{{ route('rooms.update', $room) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <x-rooms.form :room="$room" :floors="$floors" :room-types="$roomTypes" />

            <div class="mt-6 flex items-center justify-end gap-3">
                <a href="{{ route('rooms.index') }}">
                    <x-secondary-button type="button">Cancel</x-secondary-button>
                </a>
                <x-primary-button>Save Changes</x-primary-button>
            </div>
        </form>
    </div>
</x-dashboard-layout>
