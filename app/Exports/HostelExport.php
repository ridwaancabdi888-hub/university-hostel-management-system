<?php

namespace App\Exports;

use App\Enums\InvoiceStatus;
use App\Enums\MaintenanceStatus;
use App\Models\Hostel;
use App\Models\Invoice;
use App\Models\MaintenanceRequest;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class HostelExport implements FromCollection, WithHeadings
{
    public function headings(): array
    {
        return ['Hostel', 'Blocks', 'Rooms', 'Bed Capacity', 'Occupied Beds', 'Occupancy Rate', 'Monthly Revenue', 'Active Maintenance Issues'];
    }

    public function collection(): Collection
    {
        return Hostel::with('blocks.floors.rooms')->get()->map(function (Hostel $hostel) {
            $rooms = $hostel->blocks->flatMap->floors->flatMap->rooms;
            $capacity = (int) $rooms->sum('capacity');
            $occupied = (int) $rooms->sum('occupied_beds');
            $roomIds = $rooms->pluck('id');

            $revenue = Invoice::whereHas('roomAllocation', fn ($q) => $q->whereIn('room_id', $roomIds))
                ->where('status', '!=', InvoiceStatus::Cancelled)
                ->sum('total_amount');

            $activeMaintenance = MaintenanceRequest::whereIn('room_id', $roomIds)
                ->where('status', '!=', MaintenanceStatus::Completed)
                ->count();

            return [
                $hostel->name,
                $hostel->blocks->count(),
                $rooms->count(),
                $capacity,
                $occupied,
                $capacity > 0 ? round($occupied / $capacity * 100, 1).'%' : '0%',
                $revenue,
                $activeMaintenance,
            ];
        });
    }
}
