<?php

namespace App\Http\Controllers;

use App\Enums\InvoiceStatus;
use App\Enums\MaintenanceStatus;
use App\Enums\Role;
use App\Enums\VisitorStatus;
use App\Models\Block;
use App\Models\Invoice;
use App\Models\MaintenanceRequest;
use App\Models\Payment;
use App\Models\StudentProfile;
use App\Models\Visitor;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $role = $request->user()->role;
        $data = ['role' => $role];

        if (in_array($role, [Role::Admin, Role::Warden], true)) {
            $data += $this->occupancyOverview();
        }

        if (in_array($role, [Role::Admin, Role::Accountant], true)) {
            $data += $this->financeOverview();
        }

        if ($role === Role::Student) {
            $profile = $request->user()->studentProfile;

            $data += $profile
                ? $this->studentRoomOverview($profile)
                : ['myRoom' => null, 'pendingRoomRequest' => null, 'profilePending' => true];
        }

        return view('dashboard', $data);
    }

    /**
     * Per-block occupancy snapshot for Admin/Warden — the roles that
     * actually manage rooms and blocks.
     *
     * @return array<string, mixed>
     */
    private function occupancyOverview(): array
    {
        $blocks = Block::with(['hostel', 'floors.rooms'])->orderBy('name')->get();

        // A room -> block lookup built once from the already-eager-loaded
        // tree above, so the maintenance count below runs a single query
        // instead of one per block (same pattern as ReportController).
        $roomBlockMap = $blocks->flatMap(
            fn (Block $block) => $block->floors->flatMap->rooms
                ->map(fn ($room) => ['room_id' => $room->id, 'block_id' => $block->id])
        )->pluck('block_id', 'room_id');

        $maintenanceByBlock = MaintenanceRequest::where('status', '!=', MaintenanceStatus::Completed)
            ->whereNotNull('room_id')
            ->get(['id', 'room_id'])
            ->groupBy(fn (MaintenanceRequest $request) => $roomBlockMap->get($request->room_id))
            ->map->count();

        $allRooms = $blocks->flatMap(fn (Block $block) => $block->floors->flatMap->rooms);
        $totalCapacity = (int) $allRooms->sum('capacity');
        $totalOccupied = (int) $allRooms->sum('occupied_beds');

        $blockCards = $blocks->map(function (Block $block) use ($maintenanceByBlock) {
            $rooms = $block->floors->flatMap->rooms;
            $capacity = (int) $rooms->sum('capacity');
            $occupied = (int) $rooms->sum('occupied_beds');

            return [
                'id' => $block->id,
                'name' => $block->name,
                'hostel' => $block->hostel->name,
                'photoUrl' => $block->photoUrl(),
                'rooms' => $rooms->count(),
                'capacity' => $capacity,
                'occupied' => $occupied,
                'rate' => $capacity > 0 ? round($occupied / $capacity * 100, 1) : 0,
                'activeMaintenance' => (int) ($maintenanceByBlock->get($block->id) ?? 0),
            ];
        })->values();

        return [
            'totalRooms' => $allRooms->count(),
            'totalCapacity' => $totalCapacity,
            'totalOccupied' => $totalOccupied,
            'occupancyRate' => $totalCapacity > 0 ? round($totalOccupied / $totalCapacity * 100, 1) : 0,
            'activeMaintenanceCount' => MaintenanceRequest::where('status', '!=', MaintenanceStatus::Completed)->count(),
            'pendingVisitorsCount' => Visitor::where('status', VisitorStatus::Pending)->count(),
            'blockCards' => $blockCards,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function financeOverview(): array
    {
        return [
            'monthlyRevenue' => (float) Payment::whereBetween('paid_at', [now()->startOfMonth(), now()->endOfMonth()])->sum('amount'),
            'outstandingBalance' => (float) Invoice::where('status', '!=', InvoiceStatus::Cancelled)
                ->withSum('payments', 'amount')
                ->get()
                ->sum(fn (Invoice $invoice) => $invoice->balance()),
        ];
    }

    /**
     * The student's own room details (if allocated) or their pending
     * request status (if not) — the only "housing" view a Student gets.
     *
     * @return array<string, mixed>
     */
    private function studentRoomOverview(StudentProfile $profile): array
    {
        $allocation = $profile->activeAllocation()->with(['room.roomType', 'room.floor.block.hostel'])->first();

        if (! $allocation) {
            return [
                'myRoom' => null,
                'pendingRoomRequest' => $profile->pendingRoomRequest()->with('room')->first(),
                'profilePending' => false,
            ];
        }

        $room = $allocation->room;

        return [
            'myRoom' => [
                'roomNumber' => $room->room_number,
                'bedNumber' => $allocation->bed_number,
                'monthlyRate' => (float) $room->roomType->monthly_rate,
                'amenities' => $room->roomType->amenities ?? [],
                'block' => $room->floor->block->name,
                'hostel' => $room->floor->block->hostel->name,
                'floor' => $room->floor->name,
                'photoUrl' => $room->photoUrl(),
            ],
            'profilePending' => false,
        ];
    }
}
