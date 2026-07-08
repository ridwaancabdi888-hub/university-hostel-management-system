<?php

namespace App\Models;

use App\Enums\RoomStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Room extends Model
{
    protected $fillable = [
        'floor_id',
        'room_type_id',
        'room_number',
        'capacity',
        'occupied_beds',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'capacity' => 'integer',
            'occupied_beds' => 'integer',
            'status' => RoomStatus::class,
        ];
    }

    public function floor(): BelongsTo
    {
        return $this->belongsTo(Floor::class);
    }

    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }

    /**
     * The occupancy state derived from bed usage, independent of the
     * administrative status (e.g. a room can be fully booked yet still
     * "available" from a status standpoint).
     */
    public function occupancyStatus(): string
    {
        if ($this->status === RoomStatus::Maintenance) {
            return 'unavailable';
        }

        if ($this->occupied_beds >= $this->capacity) {
            return 'full';
        }

        if ($this->occupied_beds > 0) {
            return 'partial';
        }

        return 'available';
    }
}
