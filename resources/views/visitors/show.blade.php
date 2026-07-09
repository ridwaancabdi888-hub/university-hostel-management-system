<x-dashboard-layout :title="$visitor->name">
    @php
        $isStudent = auth()->user()->role === \App\Enums\Role::Student;
        $isStaff = ! $isStudent;
        $canEdit = $isStaff || $visitor->status === \App\Enums\VisitorStatus::Pending;
    @endphp

    <div class="mb-4 flex items-center justify-between">
        <a href="{{ route('visitors.index') }}" class="text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">&larr; Back to Visitors</a>
        @if ($canEdit)
            <a href="{{ route('visitors.edit', $visitor) }}" class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white hover:bg-indigo-500">
                Edit
            </a>
        @endif
    </div>

    <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <div class="flex flex-wrap items-center gap-2">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">{{ $visitor->name }}</h2>
                    <x-visitors.status-badge :status="$visitor->status" />
                </div>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $visitor->phone }}{{ $visitor->email ? ' · '.$visitor->email : '' }}</p>
                @if ($visitor->relationship)
                    <p class="text-sm text-gray-500 dark:text-gray-400">Relationship: {{ $visitor->relationship }}</p>
                @endif
            </div>

            <div class="text-right text-sm">
                <span class="text-gray-500 dark:text-gray-400">Visiting</span>
                <a href="{{ route('students.show', $visitor->studentProfile) }}" class="block font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">{{ $visitor->studentProfile->user->name }}</a>
                <p class="text-gray-500 dark:text-gray-400">{{ $visitor->studentProfile->student_id }}</p>
            </div>
        </div>

        <div class="mt-4 space-y-3 border-t border-gray-200 pt-4 text-sm dark:border-gray-700">
            <div>
                <span class="text-gray-500 dark:text-gray-400">Purpose:</span>
                <p class="mt-1 whitespace-pre-line text-gray-700 dark:text-gray-300">{{ $visitor->purpose }}</p>
            </div>
            <div>
                <span class="text-gray-500 dark:text-gray-400">Expected:</span>
                <span class="font-medium text-gray-900 dark:text-gray-100">{{ $visitor->expected_at->format('M j, Y g:i A') }}</span>
            </div>

            @if ($visitor->status !== \App\Enums\VisitorStatus::Pending)
                <div>
                    <span class="text-gray-500 dark:text-gray-400">{{ $visitor->status->label() }} by:</span>
                    <span class="font-medium text-gray-900 dark:text-gray-100">{{ $visitor->approvedBy?->name ?? '—' }}</span>
                    <span class="text-gray-500 dark:text-gray-400">on {{ $visitor->approved_at?->format('M j, Y g:i A') }}</span>
                </div>
            @endif

            @if ($visitor->status === \App\Enums\VisitorStatus::Rejected && $visitor->rejection_reason)
                <div class="rounded-lg bg-red-50 p-3 text-red-700 dark:bg-red-500/10 dark:text-red-400">
                    <span class="font-medium">Reason:</span> {{ $visitor->rejection_reason }}
                </div>
            @endif
        </div>
    </div>

    @if ($isStaff && $visitor->status === \App\Enums\VisitorStatus::Pending)
        <div class="mt-6 rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
            <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Approval</h3>

            <div class="mt-4 flex flex-wrap items-start gap-6">
                <form method="POST" action="{{ route('visitors.approve', $visitor) }}">
                    @csrf
                    <x-primary-button>Approve Visitor</x-primary-button>
                </form>

                <form method="POST" action="{{ route('visitors.reject', $visitor) }}" class="flex-1">
                    @csrf
                    <div class="flex items-start gap-3">
                        <div class="flex-1">
                            <x-text-input name="rejection_reason" type="text" class="block w-full" placeholder="Reason for declining (required)" required />
                            <x-input-error :messages="$errors->get('rejection_reason')" class="mt-2" />
                        </div>
                        <x-danger-button type="submit">Reject</x-danger-button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</x-dashboard-layout>
