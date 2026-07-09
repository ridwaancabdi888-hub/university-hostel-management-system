<x-dashboard-layout title="New Request">
    <div class="mb-4">
        <a href="{{ route('maintenance.index') }}" class="text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">&larr; Back to Maintenance</a>
    </div>

    <div class="max-w-2xl rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
        <form method="POST" action="{{ route('maintenance.store') }}">
            @csrf

            @if ($students)
                <div>
                    <x-input-label for="student_profile_id" value="Student" />
                    <x-select id="student_profile_id" name="student_profile_id" class="mt-1 block w-full" required>
                        <option value="">Select a student</option>
                        @foreach ($students as $student)
                            <option value="{{ $student->id }}" @selected(old('student_profile_id') == $student->id)>
                                {{ $student->user->name }} ({{ $student->student_id }})
                            </option>
                        @endforeach
                    </x-select>
                    <x-input-error :messages="$errors->get('student_profile_id')" class="mt-2" />
                </div>
            @endif

            <div class="mt-4">
                <x-input-label for="category" value="Category" />
                <x-select id="category" name="category" class="mt-1 block w-full" required>
                    @foreach (\App\Enums\MaintenanceCategory::cases() as $category)
                        <option value="{{ $category->value }}" @selected(old('category') === $category->value)>{{ $category->label() }}</option>
                    @endforeach
                </x-select>
                <x-input-error :messages="$errors->get('category')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="title" value="Title" />
                <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" required autofocus
                    placeholder="e.g. Leaking faucet in bathroom"
                    :value="old('title')" />
                <x-input-error :messages="$errors->get('title')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="description" value="Description" />
                <textarea id="description" name="description" rows="4" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600">{{ old('description') }}</textarea>
                <x-input-error :messages="$errors->get('description')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="priority" value="Priority" />
                <x-select id="priority" name="priority" class="mt-1 block w-full" required>
                    @foreach (\App\Enums\MaintenancePriority::cases() as $priority)
                        <option value="{{ $priority->value }}" @selected(old('priority', 'medium') === $priority->value)>{{ $priority->label() }}</option>
                    @endforeach
                </x-select>
                <x-input-error :messages="$errors->get('priority')" class="mt-2" />
            </div>

            <div class="mt-6 flex items-center justify-end gap-3">
                <a href="{{ route('maintenance.index') }}">
                    <x-secondary-button type="button">Cancel</x-secondary-button>
                </a>
                <x-primary-button>Submit Request</x-primary-button>
            </div>
        </form>
    </div>
</x-dashboard-layout>
