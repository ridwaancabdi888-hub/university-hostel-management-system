<?php

namespace App\Observers;

use App\Enums\AllocationStatus;
use App\Models\Room;
use App\Models\RoomAllocation;

class RoomAllocationObserver
{
    /**
     * Handle the RoomAllocation "created" event.
     */
    public function created(RoomAllocation $roomAllocation): void
    {
        $this->syncOccupancy($roomAllocation->room_id);
    }

    /**
     * Handle the RoomAllocation "updated" event.
     */
    public function updated(RoomAllocation $roomAllocation): void
    {
        $this->syncOccupancy($roomAllocation->room_id);

        if ($roomAllocation->wasChanged('room_id')) {
            $this->syncOccupancy($roomAllocation->getOriginal('room_id'));
        }
    }

    /**
     * Handle the RoomAllocation "deleted" event.
     */
    public function deleted(RoomAllocation $roomAllocation): void
    {
        $this->syncOccupancy($roomAllocation->room_id);
    }

    /**
     * Recalculate a room's occupied bed count from its active allocations.
     */
    private function syncOccupancy(int $roomId): void
    {
        $room = Room::find($roomId);

        if (! $room) {
            return;
        }

        $room->updateQuietly([
            'occupied_beds' => $room->allocations()->where('status', AllocationStatus::Active)->count(),
        ]);
    }
}
