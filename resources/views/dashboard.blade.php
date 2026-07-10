@php
    $quickLinks = array_filter(
        \App\Support\Navigation::for($role),
        fn (array $item) => $item['route'] !== 'dashboard'
    );
@endphp

<x-dashboard-layout title="Dashboard">
    <div class="glass-card rounded-lg p-lg">
        <h2 class="font-headline-sm text-on-surface dark:text-night-on-surface">
            Welcome back, {{ auth()->user()->name }}.
        </h2>
        <p class="mt-1 font-body-md text-on-surface-variant dark:text-night-on-surface-variant">
            You're signed in as <span class="font-medium text-primary dark:text-night-primary">{{ $role->label() }}</span>.
        </p>
    </div>

    @if (isset($totalRooms))
        <div class="mb-md mt-xl flex items-center justify-between">
            <h3 class="font-headline-sm text-on-surface dark:text-night-on-surface">Housing Overview</h3>
            <a href="{{ route('blocks.create') }}" class="inline-flex items-center gap-2 rounded-DEFAULT bg-primary px-md py-sm font-label-md text-on-primary hover:shadow-lg hover:shadow-primary/25 dark:bg-night-primary dark:text-night-on-primary transition-all">
                <span class="material-symbols-outlined text-[18px]">add</span>
                Register New Block
            </a>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="glass-card rounded-lg p-md">
                <p class="font-label-sm uppercase tracking-wide text-on-surface-variant dark:text-night-on-surface-variant">Total Occupancy</p>
                <p class="mt-2 font-headline-lg text-on-surface dark:text-night-on-surface">{{ number_format($totalOccupied) }} <span class="font-body-md font-normal text-outline dark:text-night-on-surface-variant">/ {{ number_format($totalCapacity) }}</span></p>
                <p class="mt-1 font-label-sm text-on-surface-variant dark:text-night-on-surface-variant">{{ $occupancyRate }}% of beds filled</p>
            </div>
            <div class="glass-card rounded-lg p-md">
                <p class="font-label-sm uppercase tracking-wide text-on-surface-variant dark:text-night-on-surface-variant">Maintenance Required</p>
                <p class="mt-2 font-headline-lg text-tertiary dark:text-orange-400">{{ number_format($activeMaintenanceCount) }}</p>
                <p class="mt-1 font-label-sm text-on-surface-variant dark:text-night-on-surface-variant">Active requests</p>
            </div>
            <div class="glass-card rounded-lg p-md">
                <p class="font-label-sm uppercase tracking-wide text-on-surface-variant dark:text-night-on-surface-variant">Pending Visitors</p>
                <p class="mt-2 font-headline-lg text-on-surface dark:text-night-on-surface">{{ number_format($pendingVisitorsCount) }}</p>
                <p class="mt-1 font-label-sm text-on-surface-variant dark:text-night-on-surface-variant">Awaiting approval</p>
            </div>
            @if (isset($monthlyRevenue))
                <div class="glass-card rounded-lg p-md">
                    <p class="font-label-sm uppercase tracking-wide text-on-surface-variant dark:text-night-on-surface-variant">Monthly Revenue</p>
                    <p class="mt-2 font-headline-lg text-green-600 dark:text-green-400">${{ number_format($monthlyRevenue, 2) }}</p>
                    <p class="mt-1 font-label-sm text-on-surface-variant dark:text-night-on-surface-variant">Collected this month</p>
                </div>
            @endif
        </div>

        <h3 class="mb-md mt-xl font-headline-sm text-on-surface dark:text-night-on-surface">Residential Blocks</h3>
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
                    <div class="relative h-32 w-full">
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
                    </div>
                    <div class="p-md">
                        <div class="flex items-center justify-between font-label-sm text-on-surface-variant dark:text-night-on-surface-variant">
                            <span>Occupancy</span>
                            <span class="font-medium text-on-surface dark:text-night-on-surface">{{ $block['occupied'] }}/{{ $block['capacity'] }} beds &middot; {{ $block['rooms'] }} rooms</span>
                        </div>
                        <div class="mt-1.5 h-1.5 w-full overflow-hidden rounded-full bg-surface-container-high dark:bg-night-surface-high">
                            <div class="h-full rounded-full {{ $barColor }}" style="width: {{ min($block['rate'], 100) }}%"></div>
                        </div>
                        <div class="mt-4 flex items-center gap-4 font-label-sm font-medium">
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
        <h3 class="mb-md mt-xl font-headline-sm text-on-surface dark:text-night-on-surface">Financial Snapshot</h3>
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
