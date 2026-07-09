<?php

namespace App\Http\Controllers;

use App\Enums\RoomStatus;
use App\Http\Requests\RoomRequest;
use App\Models\Block;
use App\Models\Floor;
use App\Models\Hostel;
use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Room::with(['floor.block.hostel', 'roomType']);

        if ($search = $request->string('search')->trim()->toString()) {
            $query->where(function ($query) use ($search) {
                $query->where('room_number', 'like', "%{$search}%")
                    ->orWhereHas('floor.block', fn ($q) => $q->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('floor.block.hostel', fn ($q) => $q->where('name', 'like', "%{$search}%"));
            });
        }

        if ($hostelId = $request->integer('hostel_id')) {
            $query->whereHas('floor.block', fn ($q) => $q->where('hostel_id', $hostelId));
        }

        if ($blockId = $request->integer('block_id')) {
            $query->whereHas('floor', fn ($q) => $q->where('block_id', $blockId));
        }

        if ($roomTypeId = $request->integer('room_type_id')) {
            $query->where('room_type_id', $roomTypeId);
        }

        match ($request->string('status')->toString()) {
            'available' => $query->where('status', RoomStatus::Available)->whereColumn('occupied_beds', '<', 'capacity'),
            'full' => $query->where('status', RoomStatus::Available)->whereColumn('occupied_beds', '>=', 'capacity'),
            'maintenance' => $query->where('status', RoomStatus::Maintenance),
            default => null,
        };

        $rooms = $query->orderBy('room_number')->paginate(15)->withQueryString();

        return view('rooms.index', [
            'rooms' => $rooms,
            'hostels' => Hostel::orderBy('name')->get(),
            'blocks' => Block::orderBy('name')->get(),
            'roomTypes' => RoomType::orderBy('name')->get(),
            'filters' => $request->only(['search', 'hostel_id', 'block_id', 'room_type_id', 'status']),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Room $room): View
    {
        $room->load(['floor.block.hostel', 'roomType']);

        $activeAllocations = $room->activeAllocations()->with('studentProfile.user')->get()->keyBy('bed_number');

        return view('rooms.show', [
            'room' => $room,
            'beds' => collect(range(1, $room->capacity))->map(fn (int $bed) => [
                'number' => $bed,
                'allocation' => $activeAllocations->get($bed),
            ]),
            'history' => $room->allocations()->with('studentProfile.user')->latest('allocated_at')->paginate(10),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('rooms.create', [
            'floors' => Floor::with('block.hostel')->orderBy('name')->get(),
            'roomTypes' => RoomType::orderBy('name')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RoomRequest $request): RedirectResponse
    {
        Room::create($request->validated());

        return redirect()->route('rooms.index')->with('status', 'Room created.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Room $room): View
    {
        return view('rooms.edit', [
            'room' => $room,
            'floors' => Floor::with('block.hostel')->orderBy('name')->get(),
            'roomTypes' => RoomType::orderBy('name')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RoomRequest $request, Room $room): RedirectResponse
    {
        $room->update($request->validated());

        return redirect()->route('rooms.index')->with('status', 'Room updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Room $room): RedirectResponse
    {
        if ($room->allocations()->exists()) {
            return redirect()->route('rooms.index')
                ->with('error', "Cannot delete room \"{$room->room_number}\" while it has allocation history.");
        }

        $room->delete();

        return redirect()->route('rooms.index')->with('status', 'Room deleted.');
    }
}
