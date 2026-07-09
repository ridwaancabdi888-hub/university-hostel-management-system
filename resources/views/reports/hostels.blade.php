<x-dashboard-layout title="Hostel Report">
    <x-reports-tabs active="hostels" />

    <div class="mb-6 flex items-center justify-between">
        <p class="text-sm text-gray-500 dark:text-gray-400">Per-hostel occupancy, revenue, and maintenance load.</p>
        <x-report-export-buttons type="hostels" />
    </div>

    <div class="mb-6 rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
        <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Revenue by Hostel</h3>
        <div class="mt-4">
            <canvas id="revenueChart" height="90"></canvas>
        </div>
    </div>

    <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-900/50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Hostel</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Blocks</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Rooms</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Occupied/Capacity</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Rate</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Active Maintenance</th>
                    <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Revenue</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($hostels as $row)
                    <tr>
                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ $row['name'] }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $row['blocks'] }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $row['rooms'] }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $row['occupied'] }}/{{ $row['capacity'] }}</td>
                        <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-gray-100">{{ $row['rate'] }}%</td>
                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $row['activeMaintenance'] }}</td>
                        <td class="px-4 py-3 text-right text-sm font-medium text-gray-900 dark:text-gray-100">${{ number_format($row['revenue'], 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-gray-400">No hostels yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
        <script>
            new Chart(document.getElementById('revenueChart'), {
                type: 'bar',
                data: {
                    labels: {!! $hostels->pluck('name')->toJson() !!},
                    datasets: [{
                        label: 'Revenue',
                        data: {!! $hostels->pluck('revenue')->toJson() !!},
                        backgroundColor: '#4f46e5',
                        borderRadius: 4,
                    }],
                },
                options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } },
            });
        </script>
    @endpush
</x-dashboard-layout>
