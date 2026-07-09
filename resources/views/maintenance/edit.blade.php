<x-dashboard-layout title="Edit Request">
    <div class="mb-4">
        <a href="{{ route('maintenance.show', $ticket) }}" class="text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">&larr; Back to Request</a>
    </div>

    <div class="max-w-2xl rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
        <form method="POST" action="{{ route('maintenance.update', $ticket) }}">
            @csrf
            @method('PUT')

            <div>
                <x-input-label for="category" value="Category" />
                <x-select id="category" name="category" class="mt-1 block w-full" required>
                    @foreach (\App\Enums\MaintenanceCategory::cases() as $category)
                        <option value="{{ $category->value }}" @selected(old('category', $ticket->category->value) === $category->value)>{{ $category->label() }}</option>
                    @endforeach
                </x-select>
                <x-input-error :messages="$errors->get('category')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="title" value="Title" />
                <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" required autofocus
                    :value="old('title', $ticket->title)" />
                <x-input-error :messages="$errors->get('title')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="description" value="Description" />
                <textarea id="description" name="description" rows="4" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600">{{ old('description', $ticket->description) }}</textarea>
                <x-input-error :messages="$errors->get('description')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="priority" value="Priority" />
                <x-select id="priority" name="priority" class="mt-1 block w-full" required>
                    @foreach (\App\Enums\MaintenancePriority::cases() as $priority)
                        <option value="{{ $priority->value }}" @selected(old('priority', $ticket->priority->value) === $priority->value)>{{ $priority->label() }}</option>
                    @endforeach
                </x-select>
                <x-input-error :messages="$errors->get('priority')" class="mt-2" />
            </div>

            <div class="mt-6 flex items-center justify-end gap-3">
                <a href="{{ route('maintenance.show', $ticket) }}">
                    <x-secondary-button type="button">Cancel</x-secondary-button>
                </a>
                <x-primary-button>Save Changes</x-primary-button>
            </div>
        </form>
    </div>
</x-dashboard-layout>
