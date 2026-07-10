<x-dashboard-layout title="Edit Block">
    <x-management-tabs active="blocks" />

    <div class="max-w-2xl rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
        <form method="POST" action="{{ route('blocks.update', $block) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <x-blocks.form :block="$block" :hostels="$hostels" />

            <div class="mt-6 flex items-center justify-end gap-3">
                <a href="{{ route('blocks.index') }}">
                    <x-secondary-button type="button">Cancel</x-secondary-button>
                </a>
                <x-primary-button>Save Changes</x-primary-button>
            </div>
        </form>
    </div>
</x-dashboard-layout>
