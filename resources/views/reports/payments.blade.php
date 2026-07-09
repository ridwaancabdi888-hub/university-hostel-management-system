<x-dashboard-layout title="Payment Report">
    <x-reports-tabs active="payments" />

    <div class="mb-6 flex items-center justify-between">
        <p class="text-sm text-gray-500 dark:text-gray-400">Daily and monthly income, pending balances, and every recorded payment.</p>
        <x-report-export-buttons type="payments" />
    </div>

    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
            <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Today's Income</p>
            <p class="mt-2 text-2xl font-semibold text-gray-900 dark:text-gray-100">${{ number_format($todayIncome, 2) }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
            <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">This Month's Income</p>
            <p class="mt-2 text-2xl font-semibold text-gray-900 dark:text-gray-100">${{ number_format($monthIncome, 2) }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
            <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Outstanding Balance</p>
            <p class="mt-2 text-2xl font-semibold text-amber-600 dark:text-amber-400">${{ number_format($pendingBalance, 2) }}</p>
            <a href="{{ route('invoices.index', ['status' => 'pending']) }}" class="mt-1 inline-block text-xs font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">View pending payments &rarr;</a>
        </div>
    </div>

    <div class="mb-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800 lg:col-span-2">
            <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Monthly Income Trend</h3>
            <div class="mt-4">
                <canvas id="monthlyIncomeChart" height="90"></canvas>
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
            <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">By Payment Method</h3>
            <div class="mt-4">
                <canvas id="methodChart" height="200"></canvas>
            </div>
        </div>
    </div>

    <div class="mb-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
        <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
            <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Monthly Income</h3>
            <table class="mt-4 min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead>
                    <tr>
                        <th class="px-2 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Month</th>
                        <th class="px-2 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Payments</th>
                        <th class="px-2 py-2 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($monthlyIncome as $row)
                        <tr>
                            <td class="px-2 py-2 text-sm text-gray-900 dark:text-gray-100">{{ \Illuminate\Support\Carbon::createFromFormat('Y-m', $row->month)->format('F Y') }}</td>
                            <td class="px-2 py-2 text-sm text-gray-500 dark:text-gray-400">{{ $row->payment_count }}</td>
                            <td class="px-2 py-2 text-right text-sm font-medium text-gray-900 dark:text-gray-100">${{ number_format($row->total, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-2 py-6 text-center text-sm text-gray-500 dark:text-gray-400">No payments recorded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
            <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Daily Income (selected range)</h3>
            <div class="mt-4 max-h-80 overflow-y-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead>
                        <tr>
                            <th class="px-2 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Date</th>
                            <th class="px-2 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Payments</th>
                            <th class="px-2 py-2 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($dailyIncome as $row)
                            <tr>
                                <td class="px-2 py-2 text-sm text-gray-900 dark:text-gray-100">{{ \Illuminate\Support\Carbon::parse($row->day)->format('M j, Y') }}</td>
                                <td class="px-2 py-2 text-sm text-gray-500 dark:text-gray-400">{{ $row->payment_count }}</td>
                                <td class="px-2 py-2 text-right text-sm font-medium text-gray-900 dark:text-gray-100">${{ number_format($row->total, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-2 py-6 text-center text-sm text-gray-500 dark:text-gray-400">No payments in this range.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <form method="GET" action="{{ route('reports.payments') }}" class="mb-6 rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div>
                <x-input-label for="date_from" value="From" />
                <x-text-input id="date_from" name="date_from" type="date" class="mt-1 block w-full" value="{{ $filters['date_from'] }}" />
            </div>
            <div>
                <x-input-label for="date_to" value="To" />
                <x-text-input id="date_to" name="date_to" type="date" class="mt-1 block w-full" value="{{ $filters['date_to'] }}" />
            </div>
            <div class="flex items-end">
                <x-primary-button type="submit">Apply Range</x-primary-button>
                <a href="{{ route('reports.payments') }}" class="ml-3 text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">Reset</a>
            </div>
        </div>
    </form>

    <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-900/50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Receipt</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Student</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Invoice</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Method</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Date</th>
                    <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Amount</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($payments as $payment)
                    <tr>
                        <td class="px-4 py-3 text-sm">
                            <a href="{{ route('payments.receipt', $payment) }}" class="font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">{{ $payment->receipt_number }}</a>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ $payment->invoice->studentProfile->user->name }}</td>
                        <td class="px-4 py-3 text-sm">
                            <a href="{{ route('invoices.show', $payment->invoice) }}" class="text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">{{ $payment->invoice->invoice_number }}</a>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $payment->payment_method->label() }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $payment->paid_at->format('M j, Y') }}</td>
                        <td class="px-4 py-3 text-right text-sm font-medium text-gray-900 dark:text-gray-100">${{ number_format($payment->amount, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-gray-400">No payments in this range.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $payments->links() }}
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
        <script>
            const monthlyLabels = {!! $monthlyIncome->reverse()->values()->map(fn ($r) => \Illuminate\Support\Carbon::createFromFormat('Y-m', $r->month)->format('M Y'))->toJson() !!};
            const monthlyTotals = {!! $monthlyIncome->reverse()->values()->pluck('total')->toJson() !!};

            new Chart(document.getElementById('monthlyIncomeChart'), {
                type: 'line',
                data: {
                    labels: monthlyLabels,
                    datasets: [{
                        label: 'Income',
                        data: monthlyTotals,
                        borderColor: '#4f46e5',
                        backgroundColor: 'rgba(79, 70, 229, 0.1)',
                        tension: 0.3,
                        fill: true,
                    }],
                },
                options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } },
            });

            const methodLabels = {!! $byMethod->map(fn ($r) => \App\Enums\PaymentMethod::from($r->payment_method)->label())->toJson() !!};
            const methodTotals = {!! $byMethod->pluck('total')->toJson() !!};

            new Chart(document.getElementById('methodChart'), {
                type: 'doughnut',
                data: {
                    labels: methodLabels,
                    datasets: [{
                        data: methodTotals,
                        backgroundColor: ['#4f46e5', '#0ea5e9', '#f59e0b', '#10b981', '#ec4899'],
                    }],
                },
            });
        </script>
    @endpush
</x-dashboard-layout>
