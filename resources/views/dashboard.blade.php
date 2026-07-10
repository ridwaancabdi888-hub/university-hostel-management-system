@php
    $quickLinks = array_filter(
        \App\Support\Navigation::for($role),
        fn (array $item) => $item['route'] !== 'dashboard'
    );
@endphp

<x-dashboard-layout title="Dashboard">
    <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
            Welcome back, {{ auth()->user()->name }}.
        </h2>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            You're signed in as <span class="font-medium text-indigo-600 dark:text-indigo-400">{{ $role->label() }}</span>.
        </p>
    </div>

    @if (isset($totalRooms))
        <div class="mb-4 mt-8 flex items-center justify-between">
            <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Housing Overview</h3>
            <a href="{{ route('blocks.create') }}" class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white hover:bg-indigo-500">
                Register New Block
            </a>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
                <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Total Occupancy</p>
                <p class="mt-2 text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ number_format($totalOccupied) }} <span class="text-base font-normal text-gray-400 dark:text-gray-500">/ {{ number_format($totalCapacity) }}</span></p>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $occupancyRate }}% of beds filled</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
                <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Maintenance Required</p>
                <p class="mt-2 text-2xl font-semibold text-amber-600 dark:text-amber-400">{{ number_format($activeMaintenanceCount) }}</p>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Active requests</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
                <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Pending Visitors</p>
                <p class="mt-2 text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ number_format($pendingVisitorsCount) }}</p>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Awaiting approval</p>
            </div>
            @if (isset($monthlyRevenue))
                <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Monthly Revenue</p>
                    <p class="mt-2 text-2xl font-semibold text-green-600 dark:text-green-400">${{ number_format($monthlyRevenue, 2) }}</p>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Collected this month</p>
                </div>
            @endif
        </div>

        <h3 class="mb-4 mt-8 text-base font-semibold text-gray-900 dark:text-gray-100">Residential Blocks</h3>
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($blockCards as $block)
                @php
                    $rateColor = match (true) {
                        $block['rate'] >= 90 => 'bg-red-500/90',
                        $block['rate'] >= 70 => 'bg-amber-500/90',
                        default => 'bg-green-500/90',
                    };
                    $barColor = match (true) {
                        $block['rate'] >= 90 => 'bg-red-500',
                        $block['rate'] >= 70 => 'bg-amber-500',
                        default => 'bg-green-500',
                    };
                @endphp
                <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm transition hover:shadow-md dark:border-gray-700 dark:bg-gray-800">
                    <div class="relative h-32 w-full">
                        @if ($block['photoUrl'])
                            <img src="{{ $block['photoUrl'] }}" alt="{{ $block['name'] }}" class="h-full w-full object-cover">
                        @else
                            <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-indigo-50 to-indigo-100 dark:from-gray-700 dark:to-gray-800">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-9 w-9 text-indigo-300 dark:text-gray-600">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21m4.5 0v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21" />
                                </svg>
                            </div>
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/5 to-transparent"></div>
                        <span class="absolute right-3 top-3 rounded-full {{ $rateColor }} px-2.5 py-1 text-xs font-semibold text-white">{{ $block['rate'] }}% Full</span>
                        @if ($block['activeMaintenance'] > 0)
                            <span class="absolute left-3 top-3 rounded-full bg-amber-500/90 px-2.5 py-1 text-xs font-semibold text-white">
                                {{ $block['activeMaintenance'] }} Maintenance
                            </span>
                        @endif
                        <div class="absolute inset-x-0 bottom-0 p-3">
                            <p class="text-sm font-semibold text-white">{{ $block['name'] }}</p>
                            <p class="text-xs text-white/80">{{ $block['hostel'] }}</p>
                        </div>
                    </div>
                    <div class="p-4">
                        <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                            <span>Occupancy</span>
                            <span class="font-medium text-gray-700 dark:text-gray-300">{{ $block['occupied'] }}/{{ $block['capacity'] }} beds &middot; {{ $block['rooms'] }} rooms</span>
                        </div>
                        <div class="mt-1.5 h-1.5 w-full overflow-hidden rounded-full bg-gray-100 dark:bg-gray-700">
                            <div class="h-full rounded-full {{ $barColor }}" style="width: {{ min($block['rate'], 100) }}%"></div>
                        </div>
                        <div class="mt-4 flex items-center gap-4 text-xs font-medium">
                            <a href="{{ route('rooms.index', ['block_id' => $block['id']]) }}" class="text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">Room Management</a>
                            <a href="{{ route('reports.occupancy') }}" class="text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">View Reports</a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full rounded-xl border border-dashed border-gray-300 bg-white p-8 text-center text-sm text-gray-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
                    No blocks registered yet.
                    <a href="{{ route('blocks.create') }}" class="font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">Register the first one &rarr;</a>
                </div>
            @endforelse
        </div>
    @elseif (isset($monthlyRevenue))
        <h3 class="mb-4 mt-8 text-base font-semibold text-gray-900 dark:text-gray-100">Financial Snapshot</h3>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
                <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Monthly Revenue</p>
                <p class="mt-2 text-2xl font-semibold text-green-600 dark:text-green-400">${{ number_format($monthlyRevenue, 2) }}</p>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Collected this month</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
                <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Outstanding Balance</p>
                <p class="mt-2 text-2xl font-semibold text-amber-600 dark:text-amber-400">${{ number_format($outstandingBalance, 2) }}</p>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Across all unpaid invoices</p>
            </div>
        </div>
    @endif

    @if (count($quickLinks))
        <h3 class="mb-4 mt-8 text-base font-semibold text-gray-900 dark:text-gray-100">Quick Links</h3>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($quickLinks as $item)
                <a
                    href="{{ route($item['route']) }}"
                    class="flex items-center gap-4 rounded-xl border border-gray-200 bg-white p-5 transition hover:border-indigo-300 hover:shadow-sm dark:border-gray-700 dark:bg-gray-800 dark:hover:border-indigo-500/50"
                >
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400">
                        <x-nav-icon :name="$item['icon']" />
                    </span>
                    <span class="font-medium text-gray-800 dark:text-gray-200">{{ $item['label'] }}</span>
                </a>
            @endforeach
        </div>
    @endif
</x-dashboard-layout>
