<x-dashboard-layout title="Occupancy Report">
    <x-reports-tabs active="occupancy" />

    <div class="mb-6 flex items-center justify-between">
        <p class="text-sm text-gray-500 dark:text-gray-400">Bed capacity and occupancy across every hostel and room type.</p>
        <x-report-export-buttons type="occupancy" />
    </div>

    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-4">
        <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
            <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Total Rooms</p>
            <p class="mt-2 text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $totalRooms }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
            <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Bed Capacity</p>
            <p class="mt-2 text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $totalCapacity }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
            <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Occupied Beds</p>
            <p class="mt-2 text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $totalOccupied }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
            <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Occupancy Rate</p>
            <p class="mt-2 text-2xl font-semibold text-indigo-600 dark:text-indigo-400">{{ $occupancyRate }}%</p>
        </div>
    </div>

    <div class="mb-6 rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
        <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Occupancy Rate by Hostel</h3>
        <div class="mt-4">
            <canvas id="hostelChart" height="90"></canvas>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
            <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">By Hostel</h3>
            <table class="mt-4 min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead>
                    <tr>
                        <th class="px-2 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Hostel</th>
                        <th class="px-2 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Rooms</th>
                        <th class="px-2 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Occupied/Capacity</th>
                        <th class="px-2 py-2 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Rate</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($byHostel as $row)
                        <tr>
                            <td class="px-2 py-2 text-sm text-gray-900 dark:text-gray-100">{{ $row['name'] }}</td>
                            <td class="px-2 py-2 text-sm text-gray-500 dark:text-gray-400">{{ $row['rooms'] }}</td>
                            <td class="px-2 py-2 text-sm text-gray-500 dark:text-gray-400">{{ $row['occupied'] }}/{{ $row['capacity'] }}</td>
                            <td class="px-2 py-2 text-right text-sm font-medium text-gray-900 dark:text-gray-100">{{ $row['rate'] }}%</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-2 py-6 text-center text-sm text-gray-500 dark:text-gray-400">No hostels yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
            <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">By Room Type</h3>
            <table class="mt-4 min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead>
                    <tr>
                        <th class="px-2 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Room Type</th>
                        <th class="px-2 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Rooms</th>
                        <th class="px-2 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Occupied/Capacity</th>
                        <th class="px-2 py-2 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Rate</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($byRoomType as $row)
                        <tr>
                            <td class="px-2 py-2 text-sm text-gray-900 dark:text-gray-100">{{ $row['name'] }}</td>
                            <td class="px-2 py-2 text-sm text-gray-500 dark:text-gray-400">{{ $row['rooms'] }}</td>
                            <td class="px-2 py-2 text-sm text-gray-500 dark:text-gray-400">{{ $row['occupied'] }}/{{ $row['capacity'] }}</td>
                            <td class="px-2 py-2 text-right text-sm font-medium text-gray-900 dark:text-gray-100">{{ $row['rate'] }}%</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-2 py-6 text-center text-sm text-gray-500 dark:text-gray-400">No room types yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
        <script>
            new Chart(document.getElementById('hostelChart'), {
                type: 'bar',
                data: {
                    labels: {!! $byHostel->pluck('name')->toJson() !!},
                    datasets: [{
                        label: 'Occupancy Rate (%)',
                        data: {!! $byHostel->pluck('rate')->toJson() !!},
                        backgroundColor: '#4f46e5',
                        borderRadius: 4,
                    }],
                },
                options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, max: 100 } } },
            });
        </script>
    @endpush
</x-dashboard-layout>
