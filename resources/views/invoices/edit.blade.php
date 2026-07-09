<x-dashboard-layout title="Edit Invoice">
    <div class="mb-4">
        <a href="{{ route('invoices.show', $invoice) }}" class="text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">&larr; Back to Invoice</a>
    </div>

    <div class="max-w-2xl rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
        <div class="mb-6 rounded-lg bg-gray-50 p-4 text-sm dark:bg-gray-900/50">
            <p class="font-medium text-gray-900 dark:text-gray-100">{{ $invoice->invoice_number }}</p>
            <p class="text-gray-500 dark:text-gray-400">{{ $invoice->studentProfile->user->name }} ({{ $invoice->studentProfile->student_id }}) — {{ $invoice->billing_month->format('F Y') }}</p>
        </div>

        <form method="POST" action="{{ route('invoices.update', $invoice) }}">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-input-label for="rent_amount" value="Monthly Rent ($)" />
                    <x-text-input id="rent_amount" name="rent_amount" type="number" step="0.01" min="0" class="mt-1 block w-full" required
                        :value="old('rent_amount', $invoice->rent_amount)" />
                    <x-input-error :messages="$errors->get('rent_amount')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="utility_amount" value="Utility Charges ($)" />
                    <x-text-input id="utility_amount" name="utility_amount" type="number" step="0.01" min="0" class="mt-1 block w-full" required
                        :value="old('utility_amount', $invoice->utility_amount)" />
                    <x-input-error :messages="$errors->get('utility_amount')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="late_fee_amount" value="Late Fee ($)" />
                    <x-text-input id="late_fee_amount" name="late_fee_amount" type="number" step="0.01" min="0" class="mt-1 block w-full" required
                        :value="old('late_fee_amount', $invoice->late_fee_amount)" />
                    <x-input-error :messages="$errors->get('late_fee_amount')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="discount_amount" value="Discount ($)" />
                    <x-text-input id="discount_amount" name="discount_amount" type="number" step="0.01" min="0" class="mt-1 block w-full" required
                        :value="old('discount_amount', $invoice->discount_amount)" />
                    <x-input-error :messages="$errors->get('discount_amount')" class="mt-2" />
                </div>
            </div>

            <div class="mt-4">
                <x-input-label for="due_date" value="Due Date" />
                <x-text-input id="due_date" name="due_date" type="date" class="mt-1 block w-full" required
                    :value="old('due_date', $invoice->due_date->format('Y-m-d'))" />
                <x-input-error :messages="$errors->get('due_date')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="status" value="Status" />
                <x-select id="status" name="status" class="mt-1 block w-full" required>
                    @foreach (\App\Enums\InvoiceStatus::cases() as $status)
                        <option value="{{ $status->value }}" @selected(old('status', $invoice->status->value) === $status->value)>{{ $status->label() }}</option>
                    @endforeach
                </x-select>
                <x-input-error :messages="$errors->get('status')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="notes" value="Notes (optional)" />
                <textarea id="notes" name="notes" rows="2"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600">{{ old('notes', $invoice->notes) }}</textarea>
                <x-input-error :messages="$errors->get('notes')" class="mt-2" />
            </div>

            <div class="mt-6 flex items-center justify-end gap-3">
                <a href="{{ route('invoices.show', $invoice) }}">
                    <x-secondary-button type="button">Cancel</x-secondary-button>
                </a>
                <x-primary-button>Save Changes</x-primary-button>
            </div>
        </form>
    </div>
</x-dashboard-layout>
