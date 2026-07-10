@php
    $isStudent = auth()->user()->role === \App\Enums\Role::Student;
@endphp

<x-dashboard-layout title="Room Requests">
    <div class="mb-4">
        <p class="font-body-md text-on-surface-variant dark:text-night-on-surface-variant">
            @if ($isStudent)
                Browse available rooms and request the one that suits you — your request will be reviewed by the hostel staff.
            @else
                Review room requests submitted by students and approve or reject them.
            @endif
        </p>
    </div>

    @if ($isStudent && $availableRooms->isNotEmpty())
        <h3 class="mb-md font-headline-sm text-on-surface dark:text-night-on-surface">Available Rooms</h3>
        <div class="mb-xl grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($availableRooms as $room)
                <div class="glass-card overflow-hidden rounded-lg">
                    <div class="relative h-32 w-full">
                        @if ($room->photoUrl())
                            <img src="{{ $room->photoUrl() }}" alt="Room {{ $room->room_number }}" class="h-full w-full object-cover">
                        @else
                            <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-secondary-container/60 to-secondary-container/20 dark:from-night-surface-high dark:to-night-surface">
                                <span class="material-symbols-outlined text-[36px] text-primary/40 dark:text-night-primary/40">bed</span>
                            </div>
                        @endif
                        <span class="absolute right-3 top-3 rounded-full bg-primary/90 px-sm py-1 font-label-sm text-white">${{ number_format($room->roomType->monthly_rate, 2) }}/mo</span>
                    </div>
                    <div class="p-md">
                        <p class="font-label-md font-semibold text-on-surface dark:text-night-on-surface">Room {{ $room->room_number }} &middot; {{ $room->roomType->name }}</p>
                        <p class="mt-1 font-label-sm text-on-surface-variant dark:text-night-on-surface-variant">
                            {{ $room->floor->name }}, {{ $room->floor->block->name }} &middot; {{ $room->floor->block->hostel->name }}
                        </p>

                        @if (! empty($room->roomType->amenities))
                            <div class="mt-3 flex flex-wrap gap-1.5">
                                @foreach ($room->roomType->amenities as $amenity)
                                    <span class="rounded-full bg-secondary-container/50 px-2.5 py-0.5 font-label-sm text-on-surface dark:bg-night-secondary-container dark:text-night-on-surface">{{ $amenity }}</span>
                                @endforeach
                            </div>
                        @endif

                        <form method="POST" action="{{ route('room-requests.store') }}" class="mt-4">
                            @csrf
                            <input type="hidden" name="room_id" value="{{ $room->id }}">
                            <x-primary-button type="submit" class="w-full justify-center">Request This Room</x-primary-button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <div class="glass-card overflow-x-auto rounded-lg">
        <table class="min-w-full divide-y divide-outline-variant/15 dark:divide-night-border">
            <thead class="bg-secondary-container/20 dark:bg-night-surface-high/50">
                <tr>
                    @if (! $isStudent)
                        <th class="px-4 py-3 text-left font-label-sm uppercase tracking-wider text-on-surface-variant dark:text-night-on-surface-variant">Student</th>
                    @endif
                    <th class="px-4 py-3 text-left font-label-sm uppercase tracking-wider text-on-surface-variant dark:text-night-on-surface-variant">Room</th>
                    <th class="px-4 py-3 text-left font-label-sm uppercase tracking-wider text-on-surface-variant dark:text-night-on-surface-variant">Submitted</th>
                    <th class="px-4 py-3 text-left font-label-sm uppercase tracking-wider text-on-surface-variant dark:text-night-on-surface-variant">Status</th>
                    @if (! $isStudent)
                        <th class="px-4 py-3 text-right font-label-sm uppercase tracking-wider text-on-surface-variant dark:text-night-on-surface-variant">Actions</th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/15 dark:divide-night-border">
                @forelse ($roomRequests as $roomRequest)
                    <tr class="transition hover:bg-secondary-container/10 dark:hover:bg-night-surface-high/40">
                        @if (! $isStudent)
                            <td class="px-4 py-3">
                                <a href="{{ route('students.show', $roomRequest->studentProfile) }}" class="font-label-md font-medium text-primary hover:underline dark:text-night-primary">{{ $roomRequest->studentProfile->user->name }}</a>
                            </td>
                        @endif
                        <td class="px-4 py-3 font-body-md text-on-surface dark:text-night-on-surface">
                            {{ $roomRequest->room->room_number }}
                            <div class="font-label-sm text-on-surface-variant dark:text-night-on-surface-variant">{{ $roomRequest->room->floor->block->name }}</div>
                        </td>
                        <td class="px-4 py-3 font-body-md text-on-surface-variant dark:text-night-on-surface-variant">{{ $roomRequest->created_at->format('M j, Y') }}</td>
                        <td class="px-4 py-3">
                            <x-room-requests.status-badge :status="$roomRequest->status" />
                            @if ($roomRequest->status === \App\Enums\RoomRequestStatus::Rejected && $roomRequest->rejection_reason)
                                <div class="mt-1 font-label-sm text-on-surface-variant dark:text-night-on-surface-variant">{{ $roomRequest->rejection_reason }}</div>
                            @endif
                        </td>
                        @if (! $isStudent)
                            <td class="px-4 py-3 text-right">
                                @if ($roomRequest->status === \App\Enums\RoomRequestStatus::Pending)
                                    <div class="flex items-center justify-end gap-2">
                                        <form method="POST" action="{{ route('room-requests.approve', $roomRequest) }}">
                                            @csrf
                                            <x-primary-button type="submit" class="!px-3 !py-1.5 !text-xs">Approve</x-primary-button>
                                        </form>

                                        <form method="POST" action="{{ route('room-requests.reject', $roomRequest) }}" class="flex items-center gap-2">
                                            @csrf
                                            <x-text-input name="rejection_reason" type="text" class="!h-auto !w-40 !py-1.5 !text-xs" placeholder="Reason (required)" required />
                                            <x-danger-button type="submit" class="!px-3 !py-1.5 !text-xs">Reject</x-danger-button>
                                        </form>
                                    </div>
                                @endif
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $isStudent ? 3 : 5 }}" class="px-4 py-6 text-center font-body-md text-on-surface-variant dark:text-night-on-surface-variant">No room requests yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $roomRequests->links() }}
    </div>
</x-dashboard-layout>
