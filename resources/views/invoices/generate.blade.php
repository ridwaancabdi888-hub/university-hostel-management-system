<x-dashboard-layout title="Generate Monthly Bills">
    <div class="mb-4">
        <a href="{{ route('invoices.index') }}" class="text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">&larr; Back to Billing History</a>
    </div>

    <div class="max-w-xl rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
        <p class="text-sm text-gray-500 dark:text-gray-400">
            Creates one invoice for every student with an active room allocation, using their room type's monthly rent.
            Students who already have a bill for the selected month are skipped automatically.
        </p>

        <form method="POST" action="{{ route('invoices.generate') }}" class="mt-6">
            @csrf

            <x-input-label for="billing_month" value="Billing Month" />
            <x-text-input id="billing_month" name="billing_month" type="month" class="mt-1 block w-full" required
                :value="old('billing_month', now()->format('Y-m'))" />
            <x-input-error :messages="$errors->get('billing_month')" class="mt-2" />

            <div class="mt-4">
                <x-input-label for="due_date" value="Due Date" />
                <x-text-input id="due_date" name="due_date" type="date" class="mt-1 block w-full" required
                    :value="old('due_date', now()->addDays(10)->format('Y-m-d'))" />
                <x-input-error :messages="$errors->get('due_date')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="utility_amount" value="Utility Charge (applied to every bill, optional)" />
                <x-text-input id="utility_amount" name="utility_amount" type="number" step="0.01" min="0" class="mt-1 block w-full"
                    :value="old('utility_amount', 0)" />
                <x-input-error :messages="$errors->get('utility_amount')" class="mt-2" />
            </div>

            <div class="mt-6 flex items-center justify-end gap-3">
                <a href="{{ route('invoices.index') }}">
                    <x-secondary-button type="button">Cancel</x-secondary-button>
                </a>
                <x-primary-button>Generate Bills</x-primary-button>
            </div>
        </form>
    </div>
</x-dashboard-layout>
