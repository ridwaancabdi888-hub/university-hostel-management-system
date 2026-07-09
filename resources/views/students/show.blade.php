<x-dashboard-layout :title="$student->user->name">
    <div class="mb-4 flex items-center justify-between">
        <a href="{{ route('students.index') }}" class="text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">&larr; Back to Student Directory</a>
        <div class="flex items-center gap-3">
            <a href="{{ route('students.edit', $student) }}" class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white hover:bg-indigo-500">
                Edit Student
            </a>
            <x-delete-button :action="route('students.destroy', $student)" confirm="Remove this student? This also deletes their login account.">
                Remove
            </x-delete-button>
        </div>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
        <div class="flex flex-col items-start gap-6 sm:flex-row">
            <x-avatar :name="$student->user->name" :url="$student->photoUrl()" size="h-20 w-20" class="text-2xl" />

            <div class="flex-1">
                <div class="flex flex-wrap items-center gap-3">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">{{ $student->user->name }}</h2>
                    <x-students.status-badge :status="$student->status" />
                </div>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $student->user->email }}</p>

                <dl class="mt-4 grid grid-cols-2 gap-4 sm:grid-cols-4">
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Student ID</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $student->student_id }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Course</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $student->course }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Year Level</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $student->year_level->label() }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Phone</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $student->phone ?? '—' }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
            <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Profile</h3>
            <dl class="mt-4 space-y-3 text-sm">
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">Date of Birth</dt>
                    <dd class="text-gray-900 dark:text-gray-100">{{ $student->date_of_birth?->format('M j, Y') ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">Gender</dt>
                    <dd class="text-gray-900 dark:text-gray-100">{{ $student->gender?->label() ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">Address</dt>
                    <dd class="text-gray-900 dark:text-gray-100">{{ $student->address ?? '—' }}</dd>
                </div>
            </dl>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
            <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Guardian Information</h3>
            <dl class="mt-4 space-y-3 text-sm">
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">Name</dt>
                    <dd class="text-gray-900 dark:text-gray-100">{{ $student->guardian_name ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">Relationship</dt>
                    <dd class="text-gray-900 dark:text-gray-100">{{ $student->guardian_relationship ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">Phone</dt>
                    <dd class="text-gray-900 dark:text-gray-100">{{ $student->guardian_phone ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">Email</dt>
                    <dd class="text-gray-900 dark:text-gray-100">{{ $student->guardian_email ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">Address</dt>
                    <dd class="text-gray-900 dark:text-gray-100">{{ $student->guardian_address ?? '—' }}</dd>
                </div>
            </dl>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
            <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Emergency Contact</h3>
            <dl class="mt-4 space-y-3 text-sm">
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">Name</dt>
                    <dd class="text-gray-900 dark:text-gray-100">{{ $student->emergency_contact_name ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">Relationship</dt>
                    <dd class="text-gray-900 dark:text-gray-100">{{ $student->emergency_contact_relationship ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">Phone</dt>
                    <dd class="text-gray-900 dark:text-gray-100">{{ $student->emergency_contact_phone ?? '—' }}</dd>
                </div>
            </dl>
        </div>
    </div>
</x-dashboard-layout>
