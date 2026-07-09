<?php

namespace App\Models;

use App\Enums\AllocationStatus;
use App\Observers\RoomAllocationObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ObservedBy(RoomAllocationObserver::class)]
class RoomAllocation extends Model
{
    protected $fillable = [
        'room_id',
        'student_profile_id',
        'bed_number',
        'status',
        'allocated_at',
        'vacated_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'bed_number' => 'integer',
            'status' => AllocationStatus::class,
            'allocated_at' => 'datetime',
            'vacated_at' => 'datetime',
        ];
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function studentProfile(): BelongsTo
    {
        return $this->belongsTo(StudentProfile::class);
    }
}
