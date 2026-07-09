<x-dashboard-layout :title="$ticket->title">
    @php
        $isStudent = auth()->user()->role === \App\Enums\Role::Student;
        $isStaff = ! $isStudent;
        $canEdit = $isStaff || $ticket->status === \App\Enums\MaintenanceStatus::Pending;
    @endphp

    <div class="mb-4 flex items-center justify-between">
        <a href="{{ route('maintenance.index') }}" class="text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">&larr; Back to Maintenance</a>
        @if ($canEdit)
            <a href="{{ route('maintenance.edit', $ticket) }}" class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white hover:bg-indigo-500">
                Edit
            </a>
        @endif
    </div>

    <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <div class="flex flex-wrap items-center gap-2">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">{{ $ticket->title }}</h2>
                    <x-maintenance.category-badge :category="$ticket->category" />
                    <x-maintenance.priority-badge :priority="$ticket->priority" />
                    <x-maintenance.status-badge :status="$ticket->status" />
                </div>
                <p class="mt-2 whitespace-pre-line text-sm text-gray-700 dark:text-gray-300">{{ $ticket->description }}</p>
            </div>

            <div class="text-right text-sm">
                <a href="{{ route('students.show', $ticket->studentProfile) }}" class="font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">{{ $ticket->studentProfile->user->name }}</a>
                <p class="text-gray-500 dark:text-gray-400">{{ $ticket->studentProfile->student_id }}</p>
                @if ($ticket->room)
                    <p class="text-gray-500 dark:text-gray-400">Room {{ $ticket->room->room_number }}</p>
                @endif
                <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">Submitted {{ $ticket->created_at->format('M j, Y g:i A') }}</p>
            </div>
        </div>

        <div class="mt-4 border-t border-gray-200 pt-4 text-sm dark:border-gray-700">
            <span class="text-gray-500 dark:text-gray-400">Assigned to:</span>
            <span class="font-medium text-gray-900 dark:text-gray-100">{{ $ticket->assignedStaff->name ?? 'Unassigned' }}</span>
        </div>
    </div>

    @if ($isStaff)
        <div class="mt-6 rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
            <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Assign Staff</h3>
            <form method="POST" action="{{ route('maintenance.assign', $ticket) }}" class="mt-4 flex items-end gap-3">
                @csrf
                <div class="flex-1">
                    <x-select id="assigned_to" name="assigned_to" class="block w-full">
                        <option value="">Unassigned</option>
                        @foreach ($staff as $member)
                            <option value="{{ $member->id }}" @selected($ticket->assigned_to === $member->id)>{{ $member->name }} ({{ $member->role->label() }})</option>
                        @endforeach
                    </x-select>
                    <x-input-error :messages="$errors->get('assigned_to')" class="mt-2" />
                </div>
                <x-primary-button>Update</x-primary-button>
            </form>
        </div>
    @endif

    <div class="mt-6 rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
        <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Update Status</h3>

        @if ($isStaff)
            <form method="POST" action="{{ route('maintenance.status', $ticket) }}" class="mt-4">
                @csrf
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <x-input-label for="status" value="Status" />
                        <x-select id="status" name="status" class="mt-1 block w-full" required>
                            @foreach (\App\Enums\MaintenanceStatus::cases() as $status)
                                <option value="{{ $status->value }}" @selected($ticket->status->value === $status->value)>{{ $status->label() }}</option>
                            @endforeach
                        </x-select>
                        <x-input-error :messages="$errors->get('status')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="note" value="Note (optional)" />
                        <x-text-input id="note" name="note" type="text" class="mt-1 block w-full" placeholder="e.g. Replaced the valve" />
                        <x-input-error :messages="$errors->get('note')" class="mt-2" />
                    </div>
                </div>
                <div class="mt-4">
                    <x-primary-button>Update Status</x-primary-button>
                </div>
            </form>
        @elseif ($ticket->status === \App\Enums\MaintenanceStatus::Verification)
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Staff say this has been fixed. Can you confirm?</p>
            <div class="mt-4 flex items-center gap-3">
                <form method="POST" action="{{ route('maintenance.status', $ticket) }}">
                    @csrf
                    <input type="hidden" name="status" value="completed">
                    <x-primary-button>Confirm Fixed</x-primary-button>
                </form>
                <form method="POST" action="{{ route('maintenance.status', $ticket) }}">
                    @csrf
                    <input type="hidden" name="status" value="in_progress">
                    <input type="hidden" name="note" value="Student reported the issue is not resolved.">
                    <x-secondary-button>Not Fixed — Reopen</x-secondary-button>
                </form>
            </div>
        @else
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Only staff can change the status while this request is {{ strtolower($ticket->status->label()) }}.</p>
        @endif
    </div>

    <div class="mt-6 rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
        <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Resolution History</h3>
        <div class="mt-4 flow-root">
            <ul class="-mb-8">
                @foreach ($ticket->statusLogs as $log)
                    <li>
                        <div class="relative pb-8">
                            @if (! $loop->last)
                                <span class="absolute left-2 top-2 -ml-px h-full w-0.5 bg-gray-200 dark:bg-gray-700"></span>
                            @endif
                            <div class="relative flex items-start gap-3">
                                <span class="mt-1 h-4 w-4 flex-shrink-0 rounded-full bg-indigo-600"></span>
                                <div class="text-sm">
                                    <p class="text-gray-900 dark:text-gray-100">
                                        @if ($log->from_status)
                                            Changed from <strong>{{ $log->from_status->label() }}</strong> to <strong>{{ $log->to_status->label() }}</strong>
                                        @else
                                            Submitted as <strong>{{ $log->to_status->label() }}</strong>
                                        @endif
                                    </p>
                                    @if ($log->note)
                                        <p class="mt-1 text-gray-500 dark:text-gray-400">{{ $log->note }}</p>
                                    @endif
                                    <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">
                                        {{ $log->changedBy?->name ?? 'Unknown' }} · {{ $log->created_at->format('M j, Y g:i A') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    <div class="mt-6 rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
        <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Comments</h3>

        <div class="mt-4 space-y-4">
            @forelse ($ticket->comments as $comment)
                <div class="rounded-lg bg-gray-50 p-3 dark:bg-gray-900/50">
                    <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                        <span class="font-medium text-gray-900 dark:text-gray-100">{{ $comment->user?->name ?? 'Deleted user' }}</span>
                        <span>{{ $comment->created_at->format('M j, Y g:i A') }}</span>
                    </div>
                    <p class="mt-1 whitespace-pre-line text-sm text-gray-700 dark:text-gray-300">{{ $comment->body }}</p>
                </div>
            @empty
                <p class="text-sm text-gray-500 dark:text-gray-400">No comments yet.</p>
            @endforelse
        </div>

        <form method="POST" action="{{ route('maintenance.comments.store', $ticket) }}" class="mt-4">
            @csrf
            <textarea id="body" name="body" rows="3" required placeholder="Add a comment..."
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600">{{ old('body') }}</textarea>
            <x-input-error :messages="$errors->get('body')" class="mt-2" />
            <div class="mt-3 flex justify-end">
                <x-primary-button>Post Comment</x-primary-button>
            </div>
        </form>
    </div>
</x-dashboard-layout>
