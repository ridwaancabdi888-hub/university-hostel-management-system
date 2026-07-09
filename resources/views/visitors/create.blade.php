<x-dashboard-layout title="Register Visitor">
    <div class="mb-4">
        <a href="{{ route('visitors.index') }}" class="text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">&larr; Back to Visitors</a>
    </div>

    <div class="max-w-2xl rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
        <form method="POST" action="{{ route('visitors.store') }}">
            @csrf

            @if ($students)
                <div>
                    <x-input-label for="student_profile_id" value="Student Being Visited" />
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

            <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <x-input-label for="name" value="Visitor Name" />
                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" required autofocus
                        :value="old('name')" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="phone" value="Visitor Phone" />
                    <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" required
                        :value="old('phone')" />
                    <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="email" value="Visitor Email (optional)" />
                    <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                        :value="old('email')" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="relationship" value="Relationship (optional)" />
                    <x-text-input id="relationship" name="relationship" type="text" class="mt-1 block w-full"
                        placeholder="e.g. Parent, Friend"
                        :value="old('relationship')" />
                    <x-input-error :messages="$errors->get('relationship')" class="mt-2" />
                </div>
            </div>

            <div class="mt-4">
                <x-input-label for="purpose" value="Purpose of Visit" />
                <textarea id="purpose" name="purpose" rows="3" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600">{{ old('purpose') }}</textarea>
                <x-input-error :messages="$errors->get('purpose')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="expected_at" value="Expected Date &amp; Time" />
                <x-text-input id="expected_at" name="expected_at" type="datetime-local" class="mt-1 block w-full" required
                    :value="old('expected_at')" />
                <x-input-error :messages="$errors->get('expected_at')" class="mt-2" />
            </div>

            <div class="mt-6 flex items-center justify-end gap-3">
                <a href="{{ route('visitors.index') }}">
                    <x-secondary-button type="button">Cancel</x-secondary-button>
                </a>
                <x-primary-button>Submit Registration</x-primary-button>
            </div>
        </form>
    </div>
</x-dashboard-layout>
