<x-dashboard-layout title="Billing Report">
    <x-reports-tabs active="billing" />

    <div class="mb-6 flex items-center justify-between">
        <p class="text-sm text-gray-500 dark:text-gray-400">Invoice totals, collections, and outstanding balances.</p>
        <x-report-export-buttons type="billing" />
    </div>

    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
            <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Total Billed</p>
            <p class="mt-2 text-2xl font-semibold text-gray-900 dark:text-gray-100">${{ number_format($totalBilled, 2) }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
            <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Total Collected</p>
            <p class="mt-2 text-2xl font-semibold text-green-600 dark:text-green-400">${{ number_format($totalCollected, 2) }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
            <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Outstanding</p>
            <p class="mt-2 text-2xl font-semibold text-amber-600 dark:text-amber-400">${{ number_format($totalOutstanding, 2) }}</p>
        </div>
    </div>

    <div class="mb-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800 lg:col-span-2">
            <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Billed vs Collected (last 12 months)</h3>
            <div class="mt-4">
                <canvas id="monthlyChart" height="90"></canvas>
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
            <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">By Status</h3>
            <div class="mt-4">
                <canvas id="statusChart" height="200"></canvas>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-900/50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Invoices</th>
                    <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($byStatus as $row)
                    <tr>
                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ \App\Enums\InvoiceStatus::from($row->status)->label() }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $row->count }}</td>
                        <td class="px-4 py-3 text-right text-sm font-medium text-gray-900 dark:text-gray-100">${{ number_format($row->total, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-gray-400">No invoices yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
        <script>
            new Chart(document.getElementById('monthlyChart'), {
                type: 'bar',
                data: {
                    labels: {!! $monthlyBreakdown->pluck('label')->toJson() !!},
                    datasets: [
                        {
                            label: 'Billed',
                            data: {!! $monthlyBreakdown->pluck('billed')->toJson() !!},
                            backgroundColor: '#4f46e5',
                            borderRadius: 4,
                        },
                        {
                            label: 'Collected',
                            data: {!! $monthlyBreakdown->pluck('collected')->toJson() !!},
                            backgroundColor: '#10b981',
                            borderRadius: 4,
                        },
                    ],
                },
                options: { scales: { y: { beginAtZero: true } } },
            });

            const statusLabels = {!! $byStatus->map(fn ($r) => \App\Enums\InvoiceStatus::from($r->status)->label())->toJson() !!};
            const statusTotals = {!! $byStatus->pluck('total')->toJson() !!};

            new Chart(document.getElementById('statusChart'), {
                type: 'doughnut',
                data: {
                    labels: statusLabels,
                    datasets: [{
                        data: statusTotals,
                        backgroundColor: ['#4f46e5', '#0ea5e9', '#f59e0b', '#10b981', '#ef4444'],
                    }],
                },
            });
        </script>
    @endpush
</x-dashboard-layout>
