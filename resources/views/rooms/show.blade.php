<x-dashboard-layout :title="$room->room_number">
    <div class="mb-4 flex items-center justify-between">
        <a href="{{ route('rooms.index') }}" class="font-label-md text-on-surface-variant hover:text-on-surface dark:text-night-on-surface-variant dark:hover:text-night-on-surface">&larr; Back to Room Inventory</a>
        <a href="{{ route('rooms.edit', $room) }}" class="inline-flex items-center gap-2 rounded-DEFAULT bg-primary px-md py-sm font-label-md text-on-primary hover:shadow-lg hover:shadow-primary/25 dark:bg-night-primary dark:text-night-on-primary transition-all">
            <span class="material-symbols-outlined text-[18px]">edit</span>
            Edit Room
        </a>
    </div>

    <div class="glass-card overflow-hidden rounded-lg">
        <div class="flex flex-col sm:flex-row">
            <div class="relative h-48 w-full sm:h-auto sm:w-72 sm:shrink-0">
                @if ($room->photoUrl())
                    <img src="{{ $room->photoUrl() }}" alt="{{ $room->room_number }}" class="h-full w-full object-cover">
                @else
                    <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-secondary-container/60 to-secondary-container/20 dark:from-night-surface-high dark:to-night-surface">
                        <span class="material-symbols-outlined text-[36px] text-primary/40 dark:text-night-primary/40">bed</span>
                    </div>
                @endif
            </div>
            <div class="flex-1 p-lg">
                <div class="flex flex-wrap items-start justify-between gap-2">
                    <div>
                        <div class="flex flex-wrap items-center gap-3">
                            <h2 class="font-headline-sm text-on-surface dark:text-night-on-surface">{{ $room->room_number }}</h2>
                            <x-rooms.occupancy-badge :room="$room" />
                        </div>
                        <p class="mt-1 font-body-md text-on-surface-variant dark:text-night-on-surface-variant">
                            {{ $room->floor->block->name }} — {{ $room->floor->name }} ({{ $room->floor->block->hostel->name }}) &middot; {{ $room->roomType->name }}
                        </p>
                    </div>
                    <p class="font-headline-sm text-primary dark:text-night-primary">${{ number_format($room->roomType->monthly_rate, 2) }}<span class="font-label-sm font-normal text-on-surface-variant dark:text-night-on-surface-variant">/mo</span></p>
                </div>

                @if (! empty($room->roomType->amenities))
                    <div class="mt-4 flex flex-wrap gap-2">
                        @foreach ($room->roomType->amenities as $amenity)
                            <span class="rounded-full bg-secondary-container/50 px-3 py-1 font-label-sm text-on-surface dark:bg-night-secondary-container dark:text-night-on-surface">{{ $amenity }}</span>
                        @endforeach
                    </div>
                @endif

                @if ($room->notes)
                    <p class="mt-4 font-body-md text-on-surface-variant dark:text-night-on-surface-variant">{{ $room->notes }}</p>
                @endif
            </div>
        </div>
    </div>

    <div class="glass-card mt-6 rounded-lg p-lg" x-data="{}">
        <div class="flex items-center justify-between">
            <h3 class="font-label-sm uppercase tracking-wide text-on-surface-variant dark:text-night-on-surface-variant">Gallery</h3>
            <button type="button" @click="$refs.photoForm.classList.toggle('hidden')" class="inline-flex items-center gap-2 font-label-md font-medium text-primary hover:underline dark:text-night-primary">
                <span class="material-symbols-outlined text-[18px]">add_photo_alternate</span>
                Add Photo
            </button>
        </div>

        <form x-ref="photoForm" method="POST" action="{{ route('rooms.photos', $room) }}" enctype="multipart/form-data" class="{{ $errors->any() ? '' : 'hidden' }} mt-4 rounded-DEFAULT border border-outline-variant/30 p-md dark:border-night-border">
            @csrf
            <x-input-label for="photos" value="Upload up to 4 photos (replaces the current gallery)" />
            <input id="photos" name="photos[]" type="file" accept="image/*" multiple
                class="mt-1 block w-full font-body-md text-on-surface-variant file:mr-4 file:rounded-DEFAULT file:border-0 file:bg-secondary-container/50 file:px-4 file:py-2 file:font-label-md file:font-semibold file:text-primary hover:file:bg-secondary-container dark:text-night-on-surface-variant dark:file:bg-night-secondary-container dark:file:text-night-primary">
            <x-input-error :messages="$errors->get('photos')" class="mt-2" />
            <x-input-error :messages="$errors->get('photos.0')" class="mt-2" />
            <div class="mt-3">
                <x-primary-button type="submit">Save Photos</x-primary-button>
            </div>
        </form>

        <div class="mt-4">
            <x-photo-gallery :photos="$room->photoUrls()" placeholder-icon="bed" />
        </div>
    </div>

    <div class="glass-card mt-6 rounded-lg p-lg">
        <h3 class="font-label-sm uppercase tracking-wide text-on-surface-variant dark:text-night-on-surface-variant">Bed Assignment</h3>
        <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($beds as $bed)
                <div class="rounded-DEFAULT border border-outline-variant/30 p-md dark:border-night-border">
                    <div class="font-label-sm uppercase tracking-wide text-on-surface-variant dark:text-night-on-surface-variant">Bed {{ $bed['number'] }}</div>
                    @if ($bed['allocation'])
                        @php $occupant = $bed['allocation']->studentProfile; @endphp
                        <div class="mt-2 flex items-center gap-2">
                            <x-avatar :name="$occupant->user->name" :url="$occupant->photoUrl()" size="h-8 w-8" class="text-xs" />
                            <div>
                                <a href="{{ route('students.show', $occupant) }}" class="font-label-md font-medium text-primary hover:underline dark:text-night-primary">{{ $occupant->user->name }}</a>
                                <div class="font-label-sm text-on-surface-variant dark:text-night-on-surface-variant">{{ $occupant->student_id }}</div>
                            </div>
                        </div>
                        <div class="mt-3 flex items-center gap-2">
                            <a href="{{ route('allocations.transfer.form', $bed['allocation']) }}" class="font-label-sm font-medium text-primary hover:underline dark:text-night-primary">Transfer</a>
                            <span class="text-outline-variant dark:text-night-border">|</span>
                            <x-delete-button :action="route('allocations.vacate', $bed['allocation'])" confirm="Vacate this bed?" class="font-label-sm">Vacate</x-delete-button>
                        </div>
                    @else
                        <div class="mt-2 font-body-md text-outline dark:text-night-on-surface-variant">Vacant</div>
                        @if ($room->status === \App\Enums\RoomStatus::Available)
                            <a href="{{ route('allocations.create', ['room_id' => $room->id]) }}" class="mt-3 inline-block font-label-sm font-medium text-primary hover:underline dark:text-night-primary">Allocate</a>
                        @endif
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <div class="glass-card mt-6 rounded-lg p-lg">
        <h3 class="font-label-sm uppercase tracking-wide text-on-surface-variant dark:text-night-on-surface-variant">Allocation History</h3>
        <div class="mt-4 overflow-x-auto">
            <table class="min-w-full divide-y divide-outline-variant/15 dark:divide-night-border">
                <thead>
                    <tr>
                        <th class="px-4 py-2 text-left font-label-sm uppercase tracking-wider text-on-surface-variant dark:text-night-on-surface-variant">Student</th>
                        <th class="px-4 py-2 text-left font-label-sm uppercase tracking-wider text-on-surface-variant dark:text-night-on-surface-variant">Bed</th>
                        <th class="px-4 py-2 text-left font-label-sm uppercase tracking-wider text-on-surface-variant dark:text-night-on-surface-variant">Allocated</th>
                        <th class="px-4 py-2 text-left font-label-sm uppercase tracking-wider text-on-surface-variant dark:text-night-on-surface-variant">Vacated</th>
                        <th class="px-4 py-2 text-left font-label-sm uppercase tracking-wider text-on-surface-variant dark:text-night-on-surface-variant">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/15 dark:divide-night-border">
                    @forelse ($history as $entry)
                        <tr>
                            <td class="px-4 py-2 font-body-md text-on-surface dark:text-night-on-surface">{{ $entry->studentProfile->user->name }}</td>
                            <td class="px-4 py-2 font-body-md text-on-surface-variant dark:text-night-on-surface-variant">{{ $entry->bed_number }}</td>
                            <td class="px-4 py-2 font-body-md text-on-surface-variant dark:text-night-on-surface-variant">{{ $entry->allocated_at->format('M j, Y') }}</td>
                            <td class="px-4 py-2 font-body-md text-on-surface-variant dark:text-night-on-surface-variant">{{ $entry->vacated_at?->format('M j, Y') ?? '—' }}</td>
                            <td class="px-4 py-2"><x-allocations.status-badge :status="$entry->status" /></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center font-body-md text-on-surface-variant dark:text-night-on-surface-variant">No allocation history for this room.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $history->links() }}
        </div>
    </div>
</x-dashboard-layout>
