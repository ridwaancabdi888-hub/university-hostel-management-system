<?php

namespace App\Observers;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use App\Models\Payment;

class PaymentObserver
{
    /**
     * Handle the Payment "creating" event.
     */
    public function creating(Payment $payment): void
    {
        if (empty($payment->receipt_number)) {
            $payment->receipt_number = $this->nextReceiptNumber();
        }
    }

    /**
     * Handle the Payment "created" event.
     */
    public function created(Payment $payment): void
    {
        $this->syncInvoiceStatus($payment->invoice_id);
    }

    /**
     * Handle the Payment "updated" event.
     */
    public function updated(Payment $payment): void
    {
        $this->syncInvoiceStatus($payment->invoice_id);

        if ($payment->wasChanged('invoice_id')) {
            $this->syncInvoiceStatus($payment->getOriginal('invoice_id'));
        }
    }

    /**
     * Handle the Payment "deleted" event.
     */
    public function deleted(Payment $payment): void
    {
        $this->syncInvoiceStatus($payment->invoice_id);
    }

    /**
     * Recalculate the invoice's paid status from its recorded payments,
     * leaving a cancelled invoice untouched.
     */
    private function syncInvoiceStatus(int $invoiceId): void
    {
        $invoice = Invoice::find($invoiceId);

        if (! $invoice || $invoice->status === InvoiceStatus::Cancelled) {
            return;
        }

        $paid = $invoice->payments()->sum('amount');
        $isFullyPaid = $paid >= $invoice->total_amount && $invoice->total_amount > 0;

        $invoice->updateQuietly([
            'status' => $isFullyPaid ? InvoiceStatus::Paid : InvoiceStatus::Unpaid,
            'paid_at' => $isFullyPaid ? ($invoice->payments()->latest('paid_at')->value('paid_at') ?? now()) : null,
        ]);
    }

    /**
     * Generate the next sequential receipt number for the current year,
     * e.g. RCT-2026-00001.
     *
     * Uses plain Eloquent (no raw SQL) so it works identically on MySQL and
     * SQLite (the test suite's driver), and locks the matching row so two
     * concurrent creations inside a transaction can't compute the same
     * sequence (see the caller in PaymentController, which wraps creation
     * in DB::transaction()).
     */
    private function nextReceiptNumber(): string
    {
        $prefix = 'RCT-'.now()->year.'-';

        $lastNumber = Payment::where('receipt_number', 'like', "{$prefix}%")
            ->orderByDesc('receipt_number')
            ->lockForUpdate()
            ->value('receipt_number');

        $lastSequence = $lastNumber ? (int) substr($lastNumber, strlen($prefix)) : 0;

        return $prefix.str_pad($lastSequence + 1, 5, '0', STR_PAD_LEFT);
    }
}
