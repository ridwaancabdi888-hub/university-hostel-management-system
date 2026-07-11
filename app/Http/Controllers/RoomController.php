<?php

namespace App\Http\Controllers;

use App\Enums\RoomStatus;
use App\Http\Controllers\Concerns\HandlesPhotoUploads;
use App\Http\Requests\BulkRoomPhotoRequest;
use App\Http\Requests\RoomPhotosRequest;
use App\Http\Requests\RoomRequest;
use App\Models\Block;
use App\Models\Floor;
use App\Models\Hostel;
use App\Models\Room;
use App\Models\RoomType;
use App\Support\RemoteImageFetcher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use InvalidArgumentException;

class RoomController extends Controller
{
    use HandlesPhotoUploads;

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

        if ($capacity = $request->integer('capacity')) {
            $query->where('capacity', $capacity);
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
            'capacities' => Room::query()->distinct()->orderBy('capacity')->pluck('capacity'),
            'filters' => $request->only(['search', 'hostel_id', 'block_id', 'room_type_id', 'capacity', 'status']),
        ]);
    }

    /**
     * Apply the same photo (an upload or a fetched URL) to many rooms in a
     * single action — fetches/reads the image once and writes a separate
     * stored copy per room, so replacing one room's photo later can never
     * delete a file another room still references.
     */
    public function bulkUpdatePhoto(BulkRoomPhotoRequest $request): RedirectResponse
    {
        if ($request->hasFile('photo')) {
            $body = file_get_contents($request->file('photo')->getRealPath());
            $extension = $request->file('photo')->extension() ?: 'jpg';
        } else {
            try {
                [$body, $extension] = RemoteImageFetcher::fetchBytes($request->string('photo_url')->toString());
            } catch (InvalidArgumentException $e) {
                return back()->withErrors(['photo_url' => $e->getMessage()]);
            }
        }

        $rooms = Room::whereIn('id', $request->input('room_ids'))->get();

        foreach ($rooms as $room) {
            if ($room->photo_path) {
                Storage::disk('public')->delete($room->photo_path);
            }

            $path = 'room-photos/'.Str::random(40).".{$extension}";
            Storage::disk('public')->put($path, $body);
            $room->update(['photo_path' => $path]);
        }

        return redirect()->route('rooms.index')->with('status', $rooms->count().' room photo(s) updated.');
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
     * Replace the "View Room" gallery (up to 4 photos), deleting whichever
     * gallery photos were there before.
     */
    public function addPhotos(RoomPhotosRequest $request, Room $room): RedirectResponse
    {
        foreach ($room->photo_paths ?? [] as $oldPath) {
            Storage::disk('public')->delete($oldPath);
        }

        $paths = collect($request->file('photos'))
            ->map(fn ($photo) => $photo->store('room-photos', 'public'))
            ->all();

        $room->update(['photo_paths' => $paths]);

        return redirect()->route('rooms.show', $room)->with('status', 'Room photos updated.');
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
        $data = $request->validated();

        Room::create([
            ...collect($data)->except(['photo', 'photo_url'])->toArray(),
            'photo_path' => $this->resolvePhotoPath($request, null, 'room-photos'),
        ]);

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
        $data = $request->validated();

        $room->update([
            ...collect($data)->except(['photo', 'photo_url'])->toArray(),
            'photo_path' => $this->resolvePhotoPath($request, $room->photo_path, 'room-photos'),
        ]);

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

        if ($room->photo_path) {
            Storage::disk('public')->delete($room->photo_path);
        }

        foreach ($room->photo_paths ?? [] as $path) {
            Storage::disk('public')->delete($path);
        }

        $room->delete();

        return redirect()->route('rooms.index')->with('status', 'Room deleted.');
    }
}
