<x-dashboard-layout :title="$student->user->name">
    <div class="mb-4 flex items-center justify-between">
        <a href="{{ route('students.index') }}" class="font-label-md text-on-surface-variant hover:text-on-surface dark:text-night-on-surface-variant dark:hover:text-night-on-surface">&larr; Back to Student Directory</a>
        <div class="flex items-center gap-3">
            <a href="{{ route('students.edit', $student) }}" class="inline-flex items-center gap-2 rounded-DEFAULT bg-primary px-md py-sm font-label-md text-on-primary hover:shadow-lg hover:shadow-primary/25 dark:bg-night-primary dark:text-night-on-primary transition-all">
                <span class="material-symbols-outlined text-[18px]">edit</span>
                Edit Student
            </a>
            <x-delete-button :action="route('students.destroy', $student)" confirm="Remove this student? This also deletes their login account.">
                Remove
            </x-delete-button>
        </div>
    </div>

    <div class="glass-card rounded-lg p-lg">
        <div class="flex flex-col items-start gap-6 sm:flex-row">
            <x-avatar :name="$student->user->name" :url="$student->photoUrl()" size="h-20 w-20" class="text-2xl ring-2 ring-outline-variant/30 dark:ring-night-border" />

            <div class="flex-1">
                <div class="flex flex-wrap items-center gap-3">
                    <h2 class="font-headline-sm text-on-surface dark:text-night-on-surface">{{ $student->user->name }}</h2>
                    <x-students.status-badge :status="$student->status" />
                </div>
                <p class="mt-1 font-body-md text-on-surface-variant dark:text-night-on-surface-variant">{{ $student->user->email }}</p>

                <dl class="mt-4 grid grid-cols-2 gap-4 sm:grid-cols-4">
                    <div>
                        <dt class="font-label-sm uppercase tracking-wide text-on-surface-variant dark:text-night-on-surface-variant">Student ID</dt>
                        <dd class="mt-1 font-body-md text-on-surface dark:text-night-on-surface">{{ $student->student_id }}</dd>
                    </div>
                    <div>
                        <dt class="font-label-sm uppercase tracking-wide text-on-surface-variant dark:text-night-on-surface-variant">Course</dt>
                        <dd class="mt-1 font-body-md text-on-surface dark:text-night-on-surface">{{ $student->course }}</dd>
                    </div>
                    <div>
                        <dt class="font-label-sm uppercase tracking-wide text-on-surface-variant dark:text-night-on-surface-variant">Year Level</dt>
                        <dd class="mt-1 font-body-md text-on-surface dark:text-night-on-surface">{{ $student->year_level->label() }}</dd>
                    </div>
                    <div>
                        <dt class="font-label-sm uppercase tracking-wide text-on-surface-variant dark:text-night-on-surface-variant">Phone</dt>
                        <dd class="mt-1 font-body-md text-on-surface dark:text-night-on-surface">{{ $student->phone ?? '—' }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>

    <div class="glass-card mt-6 rounded-lg p-lg">
        <h3 class="font-label-sm uppercase tracking-wide text-on-surface-variant dark:text-night-on-surface-variant">Housing</h3>

        @if ($student->activeAllocation)
            <div class="mt-4 flex flex-wrap items-center justify-between gap-4">
                <div>
                    <a href="{{ route('rooms.show', $student->activeAllocation->room) }}" class="font-label-md font-medium text-primary hover:underline dark:text-night-primary">
                        {{ $student->activeAllocation->room->room_number }} — Bed {{ $student->activeAllocation->bed_number }}
                    </a>
                    <p class="font-label-sm text-on-surface-variant dark:text-night-on-surface-variant">
                        {{ $student->activeAllocation->room->floor->block->name }} ({{ $student->activeAllocation->room->floor->block->hostel->name }}) &middot; Since {{ $student->activeAllocation->allocated_at->format('M j, Y') }}
                    </p>
                </div>
                @if (auth()->user()->role !== \App\Enums\Role::Accountant)
                    <div class="flex items-center gap-3">
                        <a href="{{ route('allocations.transfer.form', $student->activeAllocation) }}" class="inline-flex items-center rounded-DEFAULT border border-outline-variant/40 bg-surface-container-lowest px-3 py-2 font-label-md text-on-surface-variant hover:bg-secondary-container/30 dark:border-night-border dark:bg-night-surface-high dark:text-night-on-surface-variant dark:hover:bg-night-surface">
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
                <p class="font-body-md text-on-surface-variant dark:text-night-on-surface-variant">Not currently allocated to a room.</p>
                @if (auth()->user()->role !== \App\Enums\Role::Accountant)
                    <a href="{{ route('allocations.create', ['student_profile_id' => $student->id]) }}" class="inline-flex items-center gap-2 rounded-DEFAULT bg-primary px-3 py-2 font-label-md text-on-primary dark:bg-night-primary dark:text-night-on-primary">
                        Allocate Room
                    </a>
                @endif
            </div>
        @endif

        @if ($allocationHistory->isNotEmpty())
            <div class="mt-6 overflow-x-auto border-t border-outline-variant/20 pt-4 dark:border-night-border">
                <table class="min-w-full divide-y divide-outline-variant/15 dark:divide-night-border">
                    <thead>
                        <tr>
                            <th class="px-2 py-2 text-left font-label-sm uppercase tracking-wider text-on-surface-variant dark:text-night-on-surface-variant">Room</th>
                            <th class="px-2 py-2 text-left font-label-sm uppercase tracking-wider text-on-surface-variant dark:text-night-on-surface-variant">Bed</th>
                            <th class="px-2 py-2 text-left font-label-sm uppercase tracking-wider text-on-surface-variant dark:text-night-on-surface-variant">Allocated</th>
                            <th class="px-2 py-2 text-left font-label-sm uppercase tracking-wider text-on-surface-variant dark:text-night-on-surface-variant">Vacated</th>
                            <th class="px-2 py-2 text-left font-label-sm uppercase tracking-wider text-on-surface-variant dark:text-night-on-surface-variant">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant/15 dark:divide-night-border">
                        @foreach ($allocationHistory as $entry)
                            <tr>
                                <td class="px-2 py-2 font-body-md text-on-surface dark:text-night-on-surface">{{ $entry->room->room_number }}</td>
                                <td class="px-2 py-2 font-body-md text-on-surface-variant dark:text-night-on-surface-variant">{{ $entry->bed_number }}</td>
                                <td class="px-2 py-2 font-body-md text-on-surface-variant dark:text-night-on-surface-variant">{{ $entry->allocated_at->format('M j, Y') }}</td>
                                <td class="px-2 py-2 font-body-md text-on-surface-variant dark:text-night-on-surface-variant">{{ $entry->vacated_at?->format('M j, Y') ?? '—' }}</td>
                                <td class="px-2 py-2"><x-allocations.status-badge :status="$entry->status" /></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="glass-card rounded-lg p-lg">
            <h3 class="font-label-sm uppercase tracking-wide text-on-surface-variant dark:text-night-on-surface-variant">Profile</h3>
            <dl class="mt-4 space-y-3 font-body-md">
                <div>
                    <dt class="text-on-surface-variant dark:text-night-on-surface-variant">Date of Birth</dt>
                    <dd class="text-on-surface dark:text-night-on-surface">{{ $student->date_of_birth?->format('M j, Y') ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-on-surface-variant dark:text-night-on-surface-variant">Gender</dt>
                    <dd class="text-on-surface dark:text-night-on-surface">{{ $student->gender?->label() ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-on-surface-variant dark:text-night-on-surface-variant">Address</dt>
                    <dd class="text-on-surface dark:text-night-on-surface">{{ $student->address ?? '—' }}</dd>
                </div>
            </dl>
        </div>

        <div class="glass-card rounded-lg p-lg">
            <h3 class="font-label-sm uppercase tracking-wide text-on-surface-variant dark:text-night-on-surface-variant">Guardian Information</h3>
            <dl class="mt-4 space-y-3 font-body-md">
                <div>
                    <dt class="text-on-surface-variant dark:text-night-on-surface-variant">Name</dt>
                    <dd class="text-on-surface dark:text-night-on-surface">{{ $student->guardian_name ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-on-surface-variant dark:text-night-on-surface-variant">Relationship</dt>
                    <dd class="text-on-surface dark:text-night-on-surface">{{ $student->guardian_relationship ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-on-surface-variant dark:text-night-on-surface-variant">Phone</dt>
                    <dd class="text-on-surface dark:text-night-on-surface">{{ $student->guardian_phone ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-on-surface-variant dark:text-night-on-surface-variant">Email</dt>
                    <dd class="text-on-surface dark:text-night-on-surface">{{ $student->guardian_email ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-on-surface-variant dark:text-night-on-surface-variant">Address</dt>
                    <dd class="text-on-surface dark:text-night-on-surface">{{ $student->guardian_address ?? '—' }}</dd>
                </div>
            </dl>
        </div>

        <div class="glass-card rounded-lg p-lg">
            <h3 class="font-label-sm uppercase tracking-wide text-on-surface-variant dark:text-night-on-surface-variant">Emergency Contact</h3>
            <dl class="mt-4 space-y-3 font-body-md">
                <div>
                    <dt class="text-on-surface-variant dark:text-night-on-surface-variant">Name</dt>
                    <dd class="text-on-surface dark:text-night-on-surface">{{ $student->emergency_contact_name ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-on-surface-variant dark:text-night-on-surface-variant">Relationship</dt>
                    <dd class="text-on-surface dark:text-night-on-surface">{{ $student->emergency_contact_relationship ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-on-surface-variant dark:text-night-on-surface-variant">Phone</dt>
                    <dd class="text-on-surface dark:text-night-on-surface">{{ $student->emergency_contact_phone ?? '—' }}</dd>
                </div>
            </dl>
        </div>
    </div>
</x-dashboard-layout>
