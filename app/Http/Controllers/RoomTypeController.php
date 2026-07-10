<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoomTypeRequest;
use App\Models\RoomType;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RoomTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $roomTypes = RoomType::withCount('rooms')
            ->orderBy('name')
            ->paginate(10);

        return view('room-types.index', [
            'roomTypes' => $roomTypes,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('room-types.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RoomTypeRequest $request): RedirectResponse
    {
        RoomType::create($this->withAmenities($request->validated()));

        return redirect()->route('room-types.index')->with('status', 'Room type created.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RoomType $roomType): View
    {
        return view('room-types.edit', [
            'roomType' => $roomType,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RoomTypeRequest $request, RoomType $roomType): RedirectResponse
    {
        $roomType->update($this->withAmenities($request->validated()));

        return redirect()->route('room-types.index')->with('status', 'Room type updated.');
    }

    /**
     * Explode the comma-separated amenities input into a clean array
     * before it's persisted to the JSON column.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function withAmenities(array $data): array
    {
        $data['amenities'] = array_values(array_filter(array_map('trim', explode(',', $data['amenities'] ?? ''))));

        return $data;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RoomType $roomType): RedirectResponse
    {
        if ($roomType->rooms()->exists()) {
            return redirect()->route('room-types.index')
                ->with('error', "Cannot delete \"{$roomType->name}\" while rooms are still using it.");
        }

        $roomType->delete();

        return redirect()->route('room-types.index')->with('status', 'Room type deleted.');
    }
}
