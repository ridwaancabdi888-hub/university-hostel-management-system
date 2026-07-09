<?php

namespace App\Models;

use App\Enums\VisitorStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Visitor extends Model
{
    protected $fillable = [
        'student_profile_id',
        'approved_by',
        'name',
        'phone',
        'email',
        'relationship',
        'purpose',
        'expected_at',
        'status',
        'approved_at',
        'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'expected_at' => 'datetime',
            'approved_at' => 'datetime',
            'status' => VisitorStatus::class,
        ];
    }

    public function studentProfile(): BelongsTo
    {
        return $this->belongsTo(StudentProfile::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
