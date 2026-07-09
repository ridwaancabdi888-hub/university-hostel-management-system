<?php

namespace App\Exports;

use App\Models\Room;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OccupancyExport implements FromCollection, WithHeadings
{
    public function headings(): array
    {
        return ['Hostel', 'Block', 'Floor', 'Room Number', 'Room Type', 'Capacity', 'Occupied Beds', 'Status'];
    }

    public function collection(): Collection
    {
        return Room::with(['floor.block.hostel', 'roomType'])
            ->get()
            ->map(fn (Room $room) => [
                $room->floor->block->hostel->name,
                $room->floor->block->name,
                $room->floor->name,
                $room->room_number,
                $room->roomType->name,
                $room->capacity,
                $room->occupied_beds,
                $room->status->label(),
            ]);
    }
}
