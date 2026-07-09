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
            </dl>

            @if ($invoice->notes)
                <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">{{ $invoice->notes }}</p>
            @endif

            @if ($invoice->status === \App\Enums\InvoiceStatus::Paid)
                <p class="mt-4 text-sm text-green-600 dark:text-green-400">Paid on {{ $invoice->paid_at->format('M j, Y') }}</p>
            @endif
        </div>

        @if ($invoice->status !== \App\Enums\InvoiceStatus::Paid && $invoice->status !== \App\Enums\InvoiceStatus::Cancelled)
            <div class="mt-6 flex items-center gap-3 border-t border-gray-200 pt-6 dark:border-gray-700">
                <form method="POST" action="{{ route('invoices.mark-paid', $invoice) }}">
                    @csrf
                    <x-primary-button type="submit">Mark as Paid</x-primary-button>
                </form>

                @if ($invoice->isOverdue())
                    <form method="POST" action="{{ route('invoices.apply-late-fee', $invoice) }}">
                        @csrf
                        <x-secondary-button type="submit">Apply Late Fee ($25.00)</x-secondary-button>
                    </form>
                @endif

                <x-delete-button :action="route('invoices.destroy', $invoice)" confirm="Delete this invoice?">
                    Delete
                </x-delete-button>
            </div>
        @endif
    </div>
</x-dashboard-layout>
