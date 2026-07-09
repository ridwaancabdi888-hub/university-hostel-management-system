<x-dashboard-layout :title="$invoice->invoice_number">
    <div class="mb-4 flex items-center justify-between">
        <a href="{{ route('invoices.index') }}" class="text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">&larr; Back to Billing History</a>
        <div class="flex items-center gap-3">
            <a href="{{ route('invoices.pdf', $invoice) }}" class="inline-flex items-center rounded-md bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm ring-1 ring-gray-300 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:ring-gray-600 dark:hover:bg-gray-700">
                Download PDF
            </a>
            <a href="{{ route('invoices.edit', $invoice) }}" class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white hover:bg-indigo-500">
                Edit
            </a>
        </div>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">{{ $invoice->invoice_number }}</h2>
                    <x-invoices.status-badge :invoice="$invoice" />
                </div>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Billing month: {{ $invoice->billing_month->format('F Y') }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400">Due: {{ $invoice->due_date->format('M j, Y') }}</p>
            </div>

            <div class="text-right">
                <a href="{{ route('students.show', $invoice->studentProfile) }}" class="font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">{{ $invoice->studentProfile->user->name }}</a>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $invoice->studentProfile->student_id }}</p>
                @if ($invoice->roomAllocation)
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ $invoice->roomAllocation->room->room_number }} ({{ $invoice->roomAllocation->room->floor->block->name }})
                    </p>
                @endif
            </div>
        </div>

        <div class="mt-6 border-t border-gray-200 pt-6 dark:border-gray-700">
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <dt class="text-gray-500 dark:text-gray-400">Monthly Rent</dt>
                    <dd class="text-gray-900 dark:text-gray-100">${{ number_format($invoice->rent_amount, 2) }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500 dark:text-gray-400">Utility Charges</dt>
                    <dd class="text-gray-900 dark:text-gray-100">${{ number_format($invoice->utility_amount, 2) }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500 dark:text-gray-400">Late Fee</dt>
                    <dd class="text-gray-900 dark:text-gray-100">${{ number_format($invoice->late_fee_amount, 2) }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500 dark:text-gray-400">Discount</dt>
                    <dd class="text-red-600 dark:text-red-400">-${{ number_format($invoice->discount_amount, 2) }}</dd>
                </div>
                <div class="flex justify-between border-t border-gray-200 pt-3 text-base font-semibold dark:border-gray-700">
                    <dt class="text-gray-900 dark:text-gray-100">Total</dt>
                    <dd class="text-gray-900 dark:text-gray-100">${{ number_format($invoice->total_amount, 2) }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500 dark:text-gray-400">Amount Paid</dt>
                    <dd class="text-green-600 dark:text-green-400">${{ number_format($invoice->amountPaid(), 2) }}</dd>
                </div>
                <div class="flex justify-between text-base font-semibold">
                    <dt class="text-gray-900 dark:text-gray-100">Balance Due</dt>
                    <dd class="{{ $invoice->balance() > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-gray-900 dark:text-gray-100' }}">${{ number_format($invoice->balance(), 2) }}</dd>
                </div>
            </dl>

            @if ($invoice->notes)
                <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">{{ $invoice->notes }}</p>
            @endif

            @if ($invoice->status === \App\Enums\InvoiceStatus::Paid)
                <p class="mt-4 text-sm text-green-600 dark:text-green-400">Paid in full on {{ $invoice->paid_at->format('M j, Y') }}</p>
            @endif
        </div>

        @if ($invoice->status !== \App\Enums\InvoiceStatus::Cancelled)
            <div class="mt-6 flex items-center gap-3 border-t border-gray-200 pt-6 dark:border-gray-700">
                @if ($invoice->isOverdue())
                    <form method="POST" action="{{ route('invoices.apply-late-fee', $invoice) }}">
                        @csrf
                        <x-secondary-button type="submit">Apply Late Fee ($25.00)</x-secondary-button>
                    </form>
                @endif

                @if ($invoice->payments->isEmpty())
                    <x-delete-button :action="route('invoices.destroy', $invoice)" confirm="Delete this invoice?">
                        Delete
                    </x-delete-button>
                @endif
            </div>
        @endif
    </div>

    @if ($invoice->balance() > 0 && $invoice->status !== \App\Enums\InvoiceStatus::Cancelled)
        <div class="mt-6 rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
            <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Record Payment</h3>

            <form method="POST" action="{{ route('payments.store', $invoice) }}" class="mt-4">
                @csrf

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <x-input-label for="amount" value="Amount ($)" />
                        <x-text-input id="amount" name="amount" type="number" step="0.01" min="0.01" max="{{ $invoice->balance() }}" class="mt-1 block w-full" required
                            :value="old('amount', $invoice->balance())" />
                        <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Remaining balance: ${{ number_format($invoice->balance(), 2) }}</p>
                    </div>

                    <div>
                        <x-input-label for="payment_method" value="Payment Method" />
                        <x-select id="payment_method" name="payment_method" class="mt-1 block w-full" required>
                            @foreach (\App\Enums\PaymentMethod::cases() as $method)
                                <option value="{{ $method->value }}" @selected(old('payment_method') === $method->value)>{{ $method->label() }}</option>
                            @endforeach
                        </x-select>
                        <x-input-error :messages="$errors->get('payment_method')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="reference_number" value="Reference Number (optional)" />
                        <x-text-input id="reference_number" name="reference_number" type="text" class="mt-1 block w-full"
                            :value="old('reference_number')" />
                        <x-input-error :messages="$errors->get('reference_number')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="paid_at" value="Payment Date" />
                        <x-text-input id="paid_at" name="paid_at" type="date" class="mt-1 block w-full" required
                            :value="old('paid_at', now()->format('Y-m-d'))" />
                        <x-input-error :messages="$errors->get('paid_at')" class="mt-2" />
                    </div>
                </div>

                <div class="mt-4">
                    <x-input-label for="notes" value="Notes (optional)" />
                    <textarea id="notes" name="notes" rows="2"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600">{{ old('notes') }}</textarea>
                    <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                </div>

                <div class="mt-4 flex justify-end">
                    <x-primary-button>Record Payment</x-primary-button>
                </div>
            </form>
        </div>
    @endif

    <div class="mt-6 rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
        <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Payment History</h3>

        <div class="mt-4 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead>
                    <tr>
                        <th class="px-2 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Receipt</th>
                        <th class="px-2 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Amount</th>
                        <th class="px-2 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Method</th>
                        <th class="px-2 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Date</th>
                        <th class="px-2 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Recorded By</th>
                        <th class="px-2 py-2 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($invoice->payments as $payment)
                        <tr>
                            <td class="px-2 py-2 text-sm font-medium text-gray-900 dark:text-gray-100">{{ $payment->receipt_number }}</td>
                            <td class="px-2 py-2 text-sm text-gray-900 dark:text-gray-100">${{ number_format($payment->amount, 2) }}</td>
                            <td class="px-2 py-2 text-sm text-gray-500 dark:text-gray-400">{{ $payment->payment_method->label() }}</td>
                            <td class="px-2 py-2 text-sm text-gray-500 dark:text-gray-400">{{ $payment->paid_at->format('M j, Y') }}</td>
                            <td class="px-2 py-2 text-sm text-gray-500 dark:text-gray-400">{{ $payment->recordedBy?->name ?? '—' }}</td>
                            <td class="px-2 py-2 text-right text-sm">
                                <a href="{{ route('payments.receipt', $payment) }}" class="font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">Receipt</a>
                                <span class="mx-2 text-gray-300 dark:text-gray-600">|</span>
                                <x-delete-button :action="route('payments.destroy', $payment)" confirm="Remove this payment? This will reduce the amount paid on the invoice." class="inline">Remove</x-delete-button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-2 py-6 text-center text-sm text-gray-500 dark:text-gray-400">No payments recorded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-dashboard-layout>
