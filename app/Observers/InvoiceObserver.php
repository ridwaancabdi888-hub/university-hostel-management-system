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
     */
    private function nextInvoiceNumber(): string
    {
        $prefix = 'INV-'.now()->year.'-';

        $lastSequence = Invoice::where('invoice_number', 'like', "{$prefix}%")
            ->selectRaw('MAX(CAST(SUBSTRING(invoice_number, ?) AS UNSIGNED)) as max_seq', [strlen($prefix) + 1])
            ->value('max_seq');

        return $prefix.str_pad(((int) $lastSequence) + 1, 5, '0', STR_PAD_LEFT);
    }
}
