<?php

namespace App\Models;

use App\Enums\AllocationStatus;
use App\Enums\RoomStatus;
use Database\Factories\RoomFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Room extends Model
{
    /** @use HasFactory<RoomFactory> */
    use HasFactory;

    protected $fillable = [
        'floor_id',
        'room_type_id',
        'room_number',
        'capacity',
        'occupied_beds',
        'status',
        'notes',
        'photo_path',
        'photo_paths',
    ];

    protected function casts(): array
    {
        return [
            'capacity' => 'integer',
            'occupied_beds' => 'integer',
            'status' => RoomStatus::class,
            'photo_paths' => 'array',
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

    public function allocations(): HasMany
    {
        return $this->hasMany(RoomAllocation::class);
    }

    public function activeAllocations(): HasMany
    {
        return $this->allocations()->where('status', AllocationStatus::Active);
    }

    public function photoUrl(): ?string
    {
        return $this->photo_path ? Storage::disk('public')->url($this->photo_path) : null;
    }

    /**
     * URLs for the "View Room" gallery — up to 4 photos.
     *
     * @return list<string>
     */
    public function photoUrls(): array
    {
        return collect($this->photo_paths ?? [])
            ->map(fn (string $path) => Storage::disk('public')->url($path))
            ->all();
    }

    /**
     * The bed numbers (1..capacity) that are not currently occupied.
     *
     * @return list<int>
     */
    public function availableBedNumbers(): array
    {
        $taken = $this->activeAllocations()->pluck('bed_number')->all();

        return collect(range(1, $this->capacity))
            ->reject(fn (int $bed) => in_array($bed, $taken, true))
            ->values()
            ->all();
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
