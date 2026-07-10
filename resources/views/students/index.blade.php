<x-dashboard-layout title="Student Directory">
    <div class="mb-4 flex items-center justify-between">
        <p class="font-body-md text-on-surface-variant dark:text-night-on-surface-variant">Manage student records, enrollment, and profile information.</p>
        @if (auth()->user()->role !== \App\Enums\Role::Accountant)
            <a href="{{ route('students.create') }}" class="inline-flex items-center gap-2 rounded-DEFAULT bg-primary px-md py-sm font-label-md text-on-primary hover:shadow-lg hover:shadow-primary/25 dark:bg-night-primary dark:text-night-on-primary transition-all">
                <span class="material-symbols-outlined text-[18px]">person_add</span>
                Add New Student
            </a>
        @endif
    </div>

    <form method="GET" action="{{ route('students.index') }}" class="glass-card mb-6 rounded-lg p-md">
        <div class="relative">
            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-outline dark:text-night-on-surface-variant">
                <span class="material-symbols-outlined text-[20px]">search</span>
            </span>
            <x-text-input id="search" name="search" type="text" class="block w-full !rounded-full !pl-11" value="{{ $filters['search'] ?? '' }}" placeholder="Search by name, email, student ID, or course..." />
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
            <a href="{{ route('students.index') }}" class="font-label-md text-on-surface-variant hover:text-on-surface dark:text-night-on-surface-variant dark:hover:text-night-on-surface">Reset</a>
        </div>
    </form>

    <div class="glass-card overflow-x-auto rounded-lg">
        <table class="min-w-full divide-y divide-outline-variant/15 dark:divide-night-border">
            <thead class="bg-secondary-container/20 dark:bg-night-surface-high/50">
                <tr>
                    <th class="px-4 py-3 text-left font-label-sm uppercase tracking-wider text-on-surface-variant dark:text-night-on-surface-variant">Student</th>
                    <th class="px-4 py-3 text-left font-label-sm uppercase tracking-wider text-on-surface-variant dark:text-night-on-surface-variant">Student ID</th>
                    <th class="px-4 py-3 text-left font-label-sm uppercase tracking-wider text-on-surface-variant dark:text-night-on-surface-variant">Course</th>
                    <th class="px-4 py-3 text-left font-label-sm uppercase tracking-wider text-on-surface-variant dark:text-night-on-surface-variant">Year</th>
                    <th class="px-4 py-3 text-left font-label-sm uppercase tracking-wider text-on-surface-variant dark:text-night-on-surface-variant">Status</th>
                    <th class="px-4 py-3 text-right font-label-sm uppercase tracking-wider text-on-surface-variant dark:text-night-on-surface-variant">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/15 dark:divide-night-border">
                @forelse ($students as $student)
                    <tr class="transition hover:bg-secondary-container/10 dark:hover:bg-night-surface-high/40">
                        <td class="px-4 py-3">
                            <a href="{{ route('students.show', $student) }}" class="flex items-center gap-3">
                                <x-avatar :name="$student->user->name" :url="$student->photoUrl()" size="h-10 w-10" class="ring-1 ring-outline-variant/30 dark:ring-night-border" />
                                <div>
                                    <div class="font-label-md font-semibold text-on-surface dark:text-night-on-surface">{{ $student->user->name }}</div>
                                    <div class="font-label-sm text-on-surface-variant dark:text-night-on-surface-variant">{{ $student->user->email }}</div>
                                </div>
                            </a>
                        </td>
                        <td class="px-4 py-3 font-label-md font-medium text-on-surface-variant dark:text-night-on-surface-variant">{{ $student->student_id }}</td>
                        <td class="px-4 py-3 font-body-md text-on-surface-variant dark:text-night-on-surface-variant">{{ $student->course }}</td>
                        <td class="px-4 py-3 font-body-md text-on-surface-variant dark:text-night-on-surface-variant">{{ $student->year_level->label() }}</td>
                        <td class="px-4 py-3"><x-students.status-badge :status="$student->status" /></td>
                        <td class="px-4 py-3 text-right font-label-md">
                            <a href="{{ route('students.show', $student) }}" class="font-medium text-primary hover:underline dark:text-night-primary">View</a>
                            @if (auth()->user()->role !== \App\Enums\Role::Accountant)
                                <span class="mx-2 text-outline-variant dark:text-night-border">|</span>
                                <a href="{{ route('students.edit', $student) }}" class="font-medium text-primary hover:underline dark:text-night-primary">Edit</a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center font-body-md text-on-surface-variant dark:text-night-on-surface-variant">No students match your search.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $students->links() }}
    </div>
</x-dashboard-layout>
