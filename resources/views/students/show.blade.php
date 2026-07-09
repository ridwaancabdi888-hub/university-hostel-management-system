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

    <div class="mt-6 rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
        <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Housing</h3>

        @if ($student->activeAllocation)
            <div class="mt-4 flex flex-wrap items-center justify-between gap-4">
                <div>
                    <a href="{{ route('rooms.show', $student->activeAllocation->room) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">
                        {{ $student->activeAllocation->room->room_number }} — Bed {{ $student->activeAllocation->bed_number }}
                    </a>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        {{ $student->activeAllocation->room->floor->block->name }} ({{ $student->activeAllocation->room->floor->block->hostel->name }}) · Since {{ $student->activeAllocation->allocated_at->format('M j, Y') }}
                    </p>
                </div>
                @if (auth()->user()->role !== \App\Enums\Role::Accountant)
                    <div class="flex items-center gap-3">
                        <a href="{{ route('allocations.transfer.form', $student->activeAllocation) }}" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm ring-1 ring-gray-300 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:ring-gray-600 dark:hover:bg-gray-700">
                            Transfer
                        </a>
                        <x-delete-button :action="route('allocations.vacate', $student->activeAllocation)" confirm="Vacate this student's room?">
                            Vacate
                        </x-delete-button>
                    </div>
                @endif
            </div>
        @else
            <div class="mt-4 flex items-center justify-between gap-4">
                <p class="text-sm text-gray-500 dark:text-gray-400">Not currently allocated to a room.</p>
                @if (auth()->user()->role !== \App\Enums\Role::Accountant)
                    <a href="{{ route('allocations.create', ['student_profile_id' => $student->id]) }}" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-xs font-semibold uppercase tracking-widest text-white hover:bg-indigo-500">
                        Allocate Room
                    </a>
                @endif
            </div>
        @endif

        @if ($allocationHistory->isNotEmpty())
            <div class="mt-6 overflow-x-auto border-t border-gray-200 pt-4 dark:border-gray-700">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead>
                        <tr>
                            <th class="px-2 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Room</th>
                            <th class="px-2 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Bed</th>
                            <th class="px-2 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Allocated</th>
                            <th class="px-2 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Vacated</th>
                            <th class="px-2 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($allocationHistory as $entry)
                            <tr>
                                <td class="px-2 py-2 text-sm text-gray-900 dark:text-gray-100">{{ $entry->room->room_number }}</td>
                                <td class="px-2 py-2 text-sm text-gray-500 dark:text-gray-400">{{ $entry->bed_number }}</td>
                                <td class="px-2 py-2 text-sm text-gray-500 dark:text-gray-400">{{ $entry->allocated_at->format('M j, Y') }}</td>
                                <td class="px-2 py-2 text-sm text-gray-500 dark:text-gray-400">{{ $entry->vacated_at?->format('M j, Y') ?? '—' }}</td>
                                <td class="px-2 py-2 text-sm"><x-allocations.status-badge :status="$entry->status" /></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
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
