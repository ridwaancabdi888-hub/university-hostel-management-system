<?php

namespace App\Models;

use App\Enums\InvoiceStatus;
use App\Observers\InvoiceObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function isOverdue(): bool
    {
        return $this->status === InvoiceStatus::Unpaid && $this->due_date->isPast();
    }

    public function scopeOverdue(Builder $query): Builder
    {
        return $query->where('status', InvoiceStatus::Unpaid)->where('due_date', '<', now()->startOfDay());
    }
}
