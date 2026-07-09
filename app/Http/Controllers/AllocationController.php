<?php

namespace App\Http\Controllers;

use App\Enums\AllocationStatus;
use App\Enums\RoomStatus;
use App\Http\Requests\AllocateRoomRequest;
use App\Http\Requests\TransferRoomRequest;
use App\Models\Room;
use App\Models\RoomAllocation;
use App\Models\StudentProfile;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AllocationController extends Controller
{
    /**
     * Display the allocation history.
     */
    public function index(Request $request): View
    {
        $query = RoomAllocation::with(['room.floor.block.hostel', 'studentProfile.user']);

        if ($status = $request->string('status')->toString()) {
            $query->where('status', $status);
        }

        if ($search = $request->string('search')->trim()->toString()) {
            $query->where(function ($query) use ($search) {
                $query->whereHas('room', fn ($q) => $q->where('room_number', 'like', "%{$search}%"))
                    ->orWhereHas('studentProfile', fn ($q) => $q->where('student_id', 'like', "%{$search}%"))
                    ->orWhereHas('studentProfile.user', fn ($q) => $q->where('name', 'like', "%{$search}%"));
            });
        }

        $allocations = $query->latest('allocated_at')->paginate(20)->withQueryString();

        return view('allocations.index', [
            'allocations' => $allocations,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    /**
     * Show the form for allocating a student to a room.
     */
    public function create(Request $request): View
    {
        return view('allocations.create', [
            'students' => StudentProfile::with('user')->whereDoesntHave('activeAllocation')->orderBy('student_id')->get(),
            'rooms' => $this->allocatableRooms(),
            'selectedStudentId' => $request->integer('student_profile_id') ?: null,
            'selectedRoomId' => $request->integer('room_id') ?: null,
        ]);
    }

    /**
     * Allocate a student to a room and bed.
     */
    public function store(AllocateRoomRequest $request): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($data) {
            $room = Room::lockForUpdate()->findOrFail($data['room_id']);

            if (! in_array((int) $data['bed_number'], $room->availableBedNumbers(), true)) {
                throw ValidationException::withMessages(['bed_number' => 'This bed was just taken by another allocation.']);
            }

            RoomAllocation::create([
                'room_id' => $room->id,
                'student_profile_id' => $data['student_profile_id'],
                'bed_number' => $data['bed_number'],
                'status' => AllocationStatus::Active,
                'allocated_at' => now(),
                'notes' => $data['notes'] ?? null,
            ]);
        });

        return redirect()->route('students.show', $data['student_profile_id'])->with('status', 'Room allocated.');
    }

    /**
     * Show the form for transferring a student to a different room.
     */
    public function transferForm(Request $request, RoomAllocation $allocation): View
    {
        abort_if($allocation->status !== AllocationStatus::Active, 404);

        $allocation->load(['room.floor.block.hostel', 'studentProfile.user']);

        return view('allocations.transfer', [
            'allocation' => $allocation,
            'rooms' => $this->allocatableRooms(),
            'selectedRoomId' => $request->integer('room_id') ?: null,
        ]);
    }

    /**
     * Transfer a student's active allocation to a different room and bed.
     */
    public function transfer(TransferRoomRequest $request, RoomAllocation $allocation): RedirectResponse
    {
        abort_if($allocation->status !== AllocationStatus::Active, 404);

        $data = $request->validated();

        DB::transaction(function () use ($data, $allocation) {
            $room = Room::lockForUpdate()->findOrFail($data['room_id']);

            if (! in_array((int) $data['bed_number'], $room->availableBedNumbers(), true)) {
                throw ValidationException::withMessages(['bed_number' => 'This bed was just taken by another allocation.']);
            }

            $allocation->update([
                'status' => AllocationStatus::Transferred,
                'vacated_at' => now(),
            ]);

            RoomAllocation::create([
                'room_id' => $room->id,
                'student_profile_id' => $allocation->student_profile_id,
                'bed_number' => $data['bed_number'],
                'status' => AllocationStatus::Active,
                'allocated_at' => now(),
                'notes' => $data['notes'] ?? null,
            ]);
        });

        return redirect()->route('students.show', $allocation->student_profile_id)->with('status', 'Student transferred.');
    }

    /**
     * Vacate a student's active allocation, freeing up the bed.
     */
    public function vacate(RoomAllocation $allocation): RedirectResponse
    {
        abort_if($allocation->status !== AllocationStatus::Active, 404);

        $allocation->update([
            'status' => AllocationStatus::Vacated,
            'vacated_at' => now(),
        ]);

        return redirect()->route('students.show', $allocation->student_profile_id)->with('status', 'Room vacated.');
    }

    /**
     * Rooms currently open to new allocations.
     */
    private function allocatableRooms(): Collection
    {
        return Room::with('floor.block.hostel')
            ->where('status', RoomStatus::Available)
            ->whereColumn('occupied_beds', '<', 'capacity')
            ->orderBy('room_number')
            ->get();
    }
}
