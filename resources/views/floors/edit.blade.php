<x-dashboard-layout title="Edit Floor">
    <x-management-tabs active="floors" />

    <div class="max-w-2xl rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
        <form method="POST" action="{{ route('floors.update', $floor) }}">
            @csrf
            @method('PUT')

            <x-floors.form :floor="$floor" :blocks="$blocks" />

            <div class="mt-6 flex items-center justify-end gap-3">
                <a href="{{ route('floors.index') }}">
                    <x-secondary-button type="button">Cancel</x-secondary-button>
                </a>
                <x-primary-button>Save Changes</x-primary-button>
            </div>
        </form>
    </div>
</x-dashboard-layout>
