<?php

namespace App\Models;

use App\Enums\InvoiceStatus;
use App\Observers\InvoiceObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[ObservedBy(InvoiceObserver::class)]
class Invoice extends Model
{
    protected $fillable = [
        'invoice_number',
        'student_profile_id',
        'room_allocation_id',
        'billing_month',
        'rent_amount',
        'utility_amount',
        'late_fee_amount',
        'discount_amount',
        'total_amount',
        'due_date',
        'status',
        'paid_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'billing_month' => 'date',
            'due_date' => 'date',
            'paid_at' => 'datetime',
            'rent_amount' => 'decimal:2',
            'utility_amount' => 'decimal:2',
            'late_fee_amount' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'status' => InvoiceStatus::class,
        ];
    }

    public function studentProfile(): BelongsTo
    {
        return $this->belongsTo(StudentProfile::class);
    }

    public function roomAllocation(): BelongsTo
    {
        return $this->belongsTo(RoomAllocation::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Total recorded against this invoice. Uses the eager-loaded
     * payments_sum_amount (via withSum) when available to avoid an
     * extra query per row on listing pages.
     */
    public function amountPaid(): float
    {
        return (float) ($this->payments_sum_amount ?? $this->payments()->sum('amount'));
    }

    public function balance(): float
    {
        return round((float) $this->total_amount - $this->amountPaid(), 2);
    }

    /**
     * unpaid | partial | paid | cancelled — independent of the stored
     * status column, which only distinguishes cancelled from active.
     */
    public function paymentStatus(): string
    {
        if ($this->status === InvoiceStatus::Cancelled) {
            return 'cancelled';
        }

        if ($this->balance() <= 0) {
            return 'paid';
        }

        return $this->amountPaid() > 0 ? 'partial' : 'unpaid';
    }

    public function isOverdue(): bool
    {
        return $this->status !== InvoiceStatus::Cancelled && $this->balance() > 0 && $this->due_date->isPast();
    }

    public function scopeOverdue(Builder $query): Builder
    {
        return $query->where('status', InvoiceStatus::Unpaid)->where('due_date', '<', now()->startOfDay());
    }
}
