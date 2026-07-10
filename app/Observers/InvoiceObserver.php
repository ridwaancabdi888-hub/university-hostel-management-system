<?php

namespace App\Observers;

use App\Models\Invoice;

class InvoiceObserver
{
    /**
     * Handle the Invoice "creating" event.
     */
    public function creating(Invoice $invoice): void
    {
        if (empty($invoice->invoice_number)) {
            $invoice->invoice_number = $this->nextInvoiceNumber();
        }
    }

    /**
     * Handle the Invoice "saving" event.
     *
     * Keeps total_amount authoritative regardless of which charge fields
     * were touched, rather than trusting callers to compute it themselves.
     */
    public function saving(Invoice $invoice): void
    {
        $total = $invoice->rent_amount + $invoice->utility_amount + $invoice->late_fee_amount - $invoice->discount_amount;

        $invoice->total_amount = max(0, $total);
    }

    /**
     * Generate the next sequential invoice number for the current year,
     * e.g. INV-2026-00001.
     *
     * Uses plain Eloquent (no raw SQL) so it works identically on MySQL and
     * SQLite (the test suite's driver), and locks the matching row so two
     * concurrent creations inside a transaction can't compute the same
     * sequence (see callers in InvoiceController, which wrap creation in
     * DB::transaction()).
     */
    private function nextInvoiceNumber(): string
    {
        $prefix = 'INV-'.now()->year.'-';

        $lastNumber = Invoice::where('invoice_number', 'like', "{$prefix}%")
            ->orderByDesc('invoice_number')
            ->lockForUpdate()
            ->value('invoice_number');

        $lastSequence = $lastNumber ? (int) substr($lastNumber, strlen($prefix)) : 0;

        return $prefix.str_pad($lastSequence + 1, 5, '0', STR_PAD_LEFT);
    }
}
