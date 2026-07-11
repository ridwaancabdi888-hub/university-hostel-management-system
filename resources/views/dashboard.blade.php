@php
    $quickLinks = array_filter(
        \App\Support\Navigation::for($role),
        fn (array $item) => $item['route'] !== 'dashboard'
    );
@endphp

<x-dashboard-layout title="Dashboard">
    @if (isset($totalRooms))
        <div class="sticky top-0 z-10 -mx-4 -mt-4 mb-xl flex flex-wrap items-center gap-x-2 gap-y-3 border-b border-primary/20 bg-primary/10 px-4 py-3 backdrop-blur-xl dark:border-night-primary/20 dark:bg-night-primary/10 sm:-mx-6 sm:-mt-6 sm:px-6 lg:-mx-8 lg:-mt-8 lg:px-8">
            <a href="{{ route('rooms.index') }}" class="flex items-center gap-2.5 rounded-DEFAULT px-3 py-2 transition-colors hover:bg-primary/30 dark:hover:bg-night-primary/30">
                <span class="material-symbols-outlined text-primary dark:text-night-primary">bed</span>
                <div>
                    <p class="font-headline-sm leading-none text-on-surface dark:text-night-on-surface">{{ number_format($totalOccupied) }}<span class="font-body-md font-normal text-outline dark:text-night-on-surface-variant"> / {{ number_format($totalCapacity) }}</span></p>
                    <p class="font-label-sm text-on-surface-variant dark:text-night-on-surface-variant">{{ $occupancyRate }}% Occupancy</p>
                </div>
            </a>

            <div class="hidden h-9 w-px bg-primary/20 dark:bg-night-primary/20 sm:block"></div>

            <a href="{{ route('maintenance.index') }}" class="flex items-center gap-2.5 rounded-DEFAULT px-3 py-2 transition-colors hover:bg-primary/30 dark:hover:bg-night-primary/30">
                <span class="material-symbols-outlined text-tertiary dark:text-orange-400">build</span>
                <div>
                    <p class="font-headline-sm leading-none text-on-surface dark:text-night-on-surface">{{ number_format($activeMaintenanceCount) }}</p>
                    <p class="font-label-sm text-on-surface-variant dark:text-night-on-surface-variant">Maintenance Active</p>
                </div>
            </a>

            <div class="hidden h-9 w-px bg-primary/20 dark:bg-night-primary/20 sm:block"></div>

            <a href="{{ route('visitors.index') }}" class="flex items-center gap-2.5 rounded-DEFAULT px-3 py-2 transition-colors hover:bg-primary/30 dark:hover:bg-night-primary/30">
                <span class="material-symbols-outlined text-primary dark:text-night-primary">person_add</span>
                <div>
                    <p class="font-headline-sm leading-none text-on-surface dark:text-night-on-surface">{{ number_format($pendingVisitorsCount) }}</p>
                    <p class="font-label-sm text-on-surface-variant dark:text-night-on-surface-variant">Pending Visitors</p>
                </div>
            </a>

            @if (isset($monthlyRevenue))
                <div class="hidden h-9 w-px bg-primary/20 dark:bg-night-primary/20 sm:block"></div>

                <a href="{{ route('invoices.index') }}" class="flex items-center gap-2.5 rounded-DEFAULT px-3 py-2 transition-colors hover:bg-primary/30 dark:hover:bg-night-primary/30">
                    <span class="material-symbols-outlined text-green-600 dark:text-green-400">payments</span>
                    <div>
                        <p class="font-headline-sm leading-none text-green-600 dark:text-green-400">${{ number_format($monthlyRevenue, 2) }}</p>
                        <p class="font-label-sm text-on-surface-variant dark:text-night-on-surface-variant">Monthly Revenue</p>
                    </div>
                </a>
            @endif

            <a href="{{ route('blocks.create') }}" class="ml-auto inline-flex items-center gap-2 rounded-DEFAULT bg-primary px-md py-sm font-label-md text-on-primary hover:shadow-lg hover:shadow-primary/25 dark:bg-night-primary dark:text-night-on-primary transition-all">
                <span class="material-symbols-outlined text-[18px]">add</span>
                Register New Block
            </a>
        </div>

        <h3 class="mb-md font-headline-sm text-on-surface dark:text-night-on-surface">Residential Blocks</h3>
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($blockCards as $block)
                @php
                    $rateColor = match (true) {
                        $block['rate'] >= 90 => 'bg-error/90',
                        $block['rate'] >= 70 => 'bg-tertiary/90',
                        default => 'bg-green-600/90',
                    };
                    $barColor = match (true) {
                        $block['rate'] >= 90 => 'bg-error',
                        $block['rate'] >= 70 => 'bg-tertiary',
                        default => 'bg-green-600',
                    };
                @endphp
                <div class="glass-card overflow-hidden rounded-lg transition hover:shadow-lg">
                    <a href="{{ route('blocks.show', $block['id']) }}" class="relative block h-32 w-full">
                        @if ($block['photoUrl'])
                            <img src="{{ $block['photoUrl'] }}" alt="{{ $block['name'] }}" class="h-full w-full object-cover">
                        @else
                            <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-secondary-container/60 to-secondary-container/20 dark:from-night-surface-high dark:to-night-surface">
                                <span class="material-symbols-outlined text-[36px] text-primary/40 dark:text-night-primary/40">apartment</span>
                            </div>
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-on-background/70 via-on-background/5 to-transparent"></div>
                        <span class="absolute right-3 top-3 rounded-full {{ $rateColor }} px-sm py-1 font-label-sm text-white">{{ $block['rate'] }}% Full</span>
                        @if ($block['activeMaintenance'] > 0)
                            <span class="absolute left-3 top-3 rounded-full bg-tertiary/90 px-sm py-1 font-label-sm text-white">
                                {{ $block['activeMaintenance'] }} Maintenance
                            </span>
                        @endif
                        <div class="absolute inset-x-0 bottom-0 p-3">
                            <p class="font-label-md font-semibold text-white">{{ $block['name'] }}</p>
                            <p class="font-label-sm text-white/80">{{ $block['hostel'] }}</p>
                        </div>
                    </a>
                    <div class="p-md">
                        <div class="flex items-center justify-between font-label-sm text-on-surface-variant dark:text-night-on-surface-variant">
                            <span>Occupancy</span>
                            <span class="font-medium text-on-surface dark:text-night-on-surface">{{ $block['occupied'] }}/{{ $block['capacity'] }} beds &middot; {{ $block['rooms'] }} rooms</span>
                        </div>
                        <div class="mt-1.5 h-1.5 w-full overflow-hidden rounded-full bg-surface-container-high dark:bg-night-surface-high">
                            <div class="h-full rounded-full {{ $barColor }}" style="width: {{ min($block['rate'], 100) }}%"></div>
                        </div>
                        <div class="mt-4 flex items-center gap-4 font-label-sm font-medium">
                            <a href="{{ route('blocks.show', $block['id']) }}" class="text-primary hover:underline dark:text-night-primary">View Block</a>
                            <a href="{{ route('rooms.index', ['block_id' => $block['id']]) }}" class="text-primary hover:underline dark:text-night-primary">Room Management</a>
                            <a href="{{ route('reports.occupancy') }}" class="text-primary hover:underline dark:text-night-primary">View Reports</a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="glass-card col-span-full rounded-lg p-xl text-center font-body-md text-on-surface-variant dark:text-night-on-surface-variant">
                    No blocks registered yet.
                    <a href="{{ route('blocks.create') }}" class="font-medium text-primary hover:underline dark:text-night-primary">Register the first one &rarr;</a>
                </div>
            @endforelse
        </div>
    @elseif (isset($monthlyRevenue))
        <h3 class="mb-md font-headline-sm text-on-surface dark:text-night-on-surface">Financial Snapshot</h3>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div class="glass-card rounded-lg p-md">
                <p class="font-label-sm uppercase tracking-wide text-on-surface-variant dark:text-night-on-surface-variant">Monthly Revenue</p>
                <p class="mt-2 font-headline-lg text-green-600 dark:text-green-400">${{ number_format($monthlyRevenue, 2) }}</p>
                <p class="mt-1 font-label-sm text-on-surface-variant dark:text-night-on-surface-variant">Collected this month</p>
            </div>
            <div class="glass-card rounded-lg p-md">
                <p class="font-label-sm uppercase tracking-wide text-on-surface-variant dark:text-night-on-surface-variant">Outstanding Balance</p>
                <p class="mt-2 font-headline-lg text-tertiary dark:text-orange-400">${{ number_format($outstandingBalance, 2) }}</p>
                <p class="mt-1 font-label-sm text-on-surface-variant dark:text-night-on-surface-variant">Across all unpaid invoices</p>
            </div>
        </div>
    @elseif ($role === \App\Enums\Role::Student)
        <h3 class="mb-md font-headline-sm text-on-surface dark:text-night-on-surface">My Room</h3>

        @if ($profilePending)
            <div class="glass-card rounded-lg p-lg">
                <p class="font-body-md text-on-surface-variant dark:text-night-on-surface-variant">Your student profile hasn't been set up yet. Contact the hostel administration to complete your registration before requesting a room.</p>
            </div>
        @elseif ($myRoom)
            <div class="glass-card overflow-hidden rounded-lg">
                <div class="flex flex-col sm:flex-row">
                    <div class="relative h-40 w-full sm:h-auto sm:w-64 sm:shrink-0">
                        @if ($myRoom['photoUrl'])
                            <img src="{{ $myRoom['photoUrl'] }}" alt="Room {{ $myRoom['roomNumber'] }}" class="h-full w-full object-cover">
                        @else
                            <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-secondary-container/60 to-secondary-container/20 dark:from-night-surface-high dark:to-night-surface">
                                <span class="material-symbols-outlined text-[36px] text-primary/40 dark:text-night-primary/40">bed</span>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1 p-lg">
                        <div class="flex flex-wrap items-start justify-between gap-2">
                            <div>
                                <h4 class="font-headline-sm text-on-surface dark:text-night-on-surface">Room {{ $myRoom['roomNumber'] }} &middot; Bed {{ $myRoom['bedNumber'] }}</h4>
                                <p class="mt-1 font-body-md text-on-surface-variant dark:text-night-on-surface-variant">
                                    <span class="material-symbols-outlined align-middle text-[16px]">location_on</span>
                                    {{ $myRoom['floor'] }}, {{ $myRoom['block'] }} &middot; {{ $myRoom['hostel'] }}
                                </p>
                            </div>
                            <p class="font-headline-sm text-primary dark:text-night-primary">${{ number_format($myRoom['monthlyRate'], 2) }}<span class="font-label-sm font-normal text-on-surface-variant dark:text-night-on-surface-variant">/mo</span></p>
                        </div>

                        @if (count($myRoom['amenities']))
                            <div class="mt-4 flex flex-wrap gap-2">
                                @foreach ($myRoom['amenities'] as $amenity)
                                    <span class="rounded-full bg-secondary-container/50 px-3 py-1 font-label-sm text-on-surface dark:bg-night-secondary-container dark:text-night-on-surface">{{ $amenity }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @elseif ($pendingRoomRequest)
            <div class="glass-card rounded-lg p-lg">
                <p class="font-body-md text-on-surface dark:text-night-on-surface">
                    Your request for Room {{ $pendingRoomRequest->room->room_number }} is <span class="font-medium text-tertiary dark:text-orange-400">pending review</span>.
                </p>
                <a href="{{ route('room-requests.index') }}" class="mt-3 inline-block font-label-md font-medium text-primary hover:underline dark:text-night-primary">View Request Status &rarr;</a>
            </div>
        @else
            <div class="glass-card rounded-lg p-lg">
                <p class="font-body-md text-on-surface-variant dark:text-night-on-surface-variant">You don't have a room yet.</p>
                <a href="{{ route('room-requests.index') }}" class="mt-3 inline-flex items-center gap-2 rounded-DEFAULT bg-primary px-md py-sm font-label-md text-on-primary hover:shadow-lg hover:shadow-primary/25 dark:bg-night-primary dark:text-night-on-primary transition-all">
                    <span class="material-symbols-outlined text-[18px]">meeting_room</span>
                    Browse Available Rooms
                </a>
            </div>
        @endif
    @endif

    @if (count($quickLinks))
        <h3 class="mb-md mt-xl font-headline-sm text-on-surface dark:text-night-on-surface">Quick Links</h3>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($quickLinks as $item)
                <a
                    href="{{ route($item['route']) }}"
                    class="glass-card flex items-center gap-4 rounded-lg p-5 transition hover:shadow-lg"
                >
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-DEFAULT bg-secondary-container/50 text-primary dark:bg-night-secondary-container dark:text-night-primary">
                        <x-nav-icon :name="$item['icon']" />
                    </span>
                    <span class="font-label-md font-medium text-on-surface dark:text-night-on-surface">{{ $item['label'] }}</span>
                </a>
            @endforeach
        </div>
    @endif
</x-dashboard-layout>
