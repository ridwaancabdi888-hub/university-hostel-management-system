<x-dashboard-layout title="Student Directory">
    <div class="mb-4 flex items-center justify-between">
        <p class="text-sm text-gray-500 dark:text-gray-400">Manage student records, enrollment, and profile information.</p>
        @if (auth()->user()->role !== \App\Enums\Role::Accountant)
            <a href="{{ route('students.create') }}" class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white hover:bg-indigo-500">
                Add New Student
            </a>
        @endif
    </div>

    <form method="GET" action="{{ route('students.index') }}" class="mb-6 rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
        <div class="relative">
            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-gray-400 dark:text-gray-500">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                </svg>
            </span>
            <x-text-input id="search" name="search" type="text" class="block w-full !rounded-full !pl-10" value="{{ $filters['search'] ?? '' }}" placeholder="Search by name, email, student ID, or course..." />
        </div>

        <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div>
                <x-input-label for="course" value="Course" />
                <x-select id="course" name="course" class="mt-1 block w-full">
                    <option value="">All Courses</option>
                    @foreach ($courses as $course)
                        <option value="{{ $course }}" @selected(($filters['course'] ?? '') === $course)>{{ $course }}</option>
                    @endforeach
                </x-select>
            </div>

            <div>
                <x-input-label for="year_level" value="Year Level" />
                <x-select id="year_level" name="year_level" class="mt-1 block w-full">
                    <option value="">All Years</option>
                    @foreach (\App\Enums\YearLevel::cases() as $level)
                        <option value="{{ $level->value }}" @selected(($filters['year_level'] ?? '') === $level->value)>{{ $level->label() }}</option>
                    @endforeach
                </x-select>
            </div>

            <div>
                <x-input-label for="status" value="Status" />
                <x-select id="status" name="status" class="mt-1 block w-full">
                    <option value="">All Statuses</option>
                    @foreach (\App\Enums\StudentStatus::cases() as $status)
                        <option value="{{ $status->value }}" @selected(($filters['status'] ?? '') === $status->value)>{{ $status->label() }}</option>
                    @endforeach
                </x-select>
            </div>
        </div>

        <div class="mt-4 flex items-center gap-3">
            <x-primary-button type="submit">Apply Filters</x-primary-button>
            <a href="{{ route('students.index') }}" class="text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">Reset</a>
        </div>
    </form>

    <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-900/50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Student</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Student ID</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Course</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Year</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Status</th>
                    <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($students as $student)
                    <tr class="transition hover:bg-gray-50 dark:hover:bg-gray-900/40">
                        <td class="px-4 py-3">
                            <a href="{{ route('students.show', $student) }}" class="flex items-center gap-3">
                                <x-avatar :name="$student->user->name" :url="$student->photoUrl()" size="h-10 w-10" class="text-sm ring-1 ring-gray-200 dark:ring-gray-700" />
                                <div>
                                    <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $student->user->name }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $student->user->email }}</div>
                                </div>
                            </a>
                        </td>
                        <td class="px-4 py-3 text-sm font-medium text-gray-700 dark:text-gray-300">{{ $student->student_id }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $student->course }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $student->year_level->label() }}</td>
                        <td class="px-4 py-3 text-sm"><x-students.status-badge :status="$student->status" /></td>
                        <td class="px-4 py-3 text-right text-sm">
                            <a href="{{ route('students.show', $student) }}" class="font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">View</a>
                            @if (auth()->user()->role !== \App\Enums\Role::Accountant)
                                <span class="mx-2 text-gray-300 dark:text-gray-600">|</span>
                                <a href="{{ route('students.edit', $student) }}" class="font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">Edit</a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-gray-400">No students match your search.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $students->links() }}
    </div>
</x-dashboard-layout>
