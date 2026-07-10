<?php

namespace App\Http\Controllers;

use App\Enums\AllocationStatus;
use App\Enums\InvoiceStatus;
use App\Enums\Role;
use App\Enums\RoomRequestStatus;
use App\Enums\RoomStatus;
use App\Http\Requests\RejectRoomRequestRequest;
use App\Http\Requests\RoomRequestRequest;
use App\Models\Invoice;
use App\Models\Room;
use App\Models\RoomAllocation;
use App\Models\RoomRequest;
use App\Models\RoomType;
use App\Models\User;
use App\Notifications\RoomRequestApproved;
use App\Notifications\RoomRequestRejected;
use App\Notifications\RoomRequestSubmitted;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification as NotificationFacade;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RoomRequestController extends Controller
{
    /**
     * Display a listing of the resource. Students see only their own room
     * requests plus the rooms they can request; staff see everything,
     * filterable by status.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $query = RoomRequest::with(['studentProfile.user', 'room.floor.block.hostel', 'reviewedBy']);

        if ($user->hasRole(Role::Student)) {
            $query->whereHas('studentProfile', fn ($q) => $q->where('user_id', $user->id));
        }

        if ($status = $request->string('status')->toString()) {
            $query->where('status', $status);
        }

        $roomRequests = $query->latest()->paginate(15)->withQueryString();

        $availableRooms = new Collection;

        if ($user->hasRole(Role::Student) && $user->studentProfile
            && ! $user->studentProfile->activeAllocation
            && ! $user->studentProfile->pendingRoomRequest) {
            $availableRooms = $this->requestableRooms();
        }

        return view('room-requests.index', [
            'roomRequests' => $roomRequests,
            'availableRooms' => $availableRooms,
            'filters' => $request->only(['status']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RoomRequestRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $profile = $request->user()->studentProfile;

        if (! $profile) {
            return back()->with('error', 'Your student profile has not been set up yet. Contact the hostel administration.');
        }

        if ($profile->activeAllocation) {
            return back()->with('error', 'You already have an allocated room.');
        }

        if ($profile->pendingRoomRequest) {
            return back()->with('error', 'You already have a room request awaiting review.');
        }

        $roomRequest = RoomRequest::create([
            'student_profile_id' => $profile->id,
            'room_id' => $data['room_id'],
            'notes' => $data['notes'] ?? null,
            'status' => RoomRequestStatus::Pending,
        ]);

        $staff = User::whereIn('role', [Role::Admin, Role::Warden])->get();
        NotificationFacade::send($staff, new RoomRequestSubmitted($roomRequest));

        return redirect()->route('room-requests.index')->with('status', 'Room request submitted and awaiting approval.');
    }

    /**
     * Approve the room request, auto-assigning the first available bed.
     */
    public function approve(Request $request, RoomRequest $roomRequest): RedirectResponse
    {
        $this->authorize('approve', $roomRequest);

        DB::transaction(function () use ($request, $roomRequest) {
            $room = Room::lockForUpdate()->findOrFail($roomRequest->room_id);
            $beds = $room->availableBedNumbers();

            if ($beds === []) {
                throw ValidationException::withMessages(['room_id' => 'This room was just filled by another allocation.']);
            }

            $allocation = RoomAllocation::create([
                'room_id' => $room->id,
                'student_profile_id' => $roomRequest->student_profile_id,
                'bed_number' => $beds[0],
                'status' => AllocationStatus::Active,
                'allocated_at' => now(),
            ]);

            $roomRequest->update([
                'status' => RoomRequestStatus::Approved,
                'reviewed_by' => $request->user()->id,
                'reviewed_at' => now(),
                'rejection_reason' => null,
            ]);

            $this->billFirstMonth($allocation, $room->roomType);
        });

        $roomRequest->studentProfile->user->notify(new RoomRequestApproved($roomRequest));

        return redirect()->route('room-requests.index')->with('status', 'Room request approved.');
    }

    /**
     * Reject the room request.
     */
    public function reject(RejectRoomRequestRequest $request, RoomRequest $roomRequest): RedirectResponse
    {
        $this->authorize('reject', $roomRequest);

        $roomRequest->update([
            'status' => RoomRequestStatus::Rejected,
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
            'rejection_reason' => $request->validated()['rejection_reason'],
        ]);

        $roomRequest->studentProfile->user->notify(new RoomRequestRejected($roomRequest));

        return redirect()->route('room-requests.index')->with('status', 'Room request rejected.');
    }

    /**
     * Rooms currently open for a student to request.
     */
    private function requestableRooms(): Collection
    {
        return Room::with(['roomType', 'floor.block.hostel'])
            ->where('status', RoomStatus::Available)
            ->whereColumn('occupied_beds', '<', 'capacity')
            ->orderBy('room_number')
            ->get();
    }

    /**
     * Bill the student for the current month as soon as their room is
     * approved, rather than leaving them unbilled until the next manual
     * "Generate Bills" run — skips if they were already billed this month.
     */
    private function billFirstMonth(RoomAllocation $allocation, RoomType $roomType): void
    {
        $billingMonth = now()->startOfMonth();

        $alreadyBilled = Invoice::where('student_profile_id', $allocation->student_profile_id)
            ->whereYear('billing_month', $billingMonth->year)
            ->whereMonth('billing_month', $billingMonth->month)
            ->exists();

        if ($alreadyBilled) {
            return;
        }

        Invoice::create([
            'student_profile_id' => $allocation->student_profile_id,
            'room_allocation_id' => $allocation->id,
            'billing_month' => $billingMonth,
            'rent_amount' => $roomType->monthly_rate,
            'utility_amount' => 0,
            'due_date' => now()->addDays(14),
            'status' => InvoiceStatus::Unpaid,
        ]);
    }
}
