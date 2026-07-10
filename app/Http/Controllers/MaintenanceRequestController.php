<?php

namespace App\Http\Controllers;

use App\Enums\MaintenanceStatus;
use App\Enums\Role;
use App\Http\Requests\AssignStaffRequest;
use App\Http\Requests\MaintenanceRequestRequest;
use App\Http\Requests\UpdateMaintenanceStatusRequest;
use App\Models\MaintenanceRequest;
use App\Models\StudentProfile;
use App\Models\User;
use App\Notifications\MaintenanceAssigned;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MaintenanceRequestController extends Controller
{
    /**
     * Display a listing of the resource. Students see only their own
     * tickets; staff see everything, filterable by category/status/priority.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $query = MaintenanceRequest::with(['studentProfile.user', 'room.floor.block', 'assignedStaff']);

        if ($user->hasRole(Role::Student)) {
            $query->whereHas('studentProfile', fn ($q) => $q->where('user_id', $user->id));
        }

        if ($category = $request->string('category')->toString()) {
            $query->where('category', $category);
        }

        if ($status = $request->string('status')->toString()) {
            $query->where('status', $status);
        }

        if ($priority = $request->string('priority')->toString()) {
            $query->where('priority', $priority);
        }

        $tickets = $query->latest()->paginate(15)->withQueryString();

        return view('maintenance.index', [
            'tickets' => $tickets,
            'filters' => $request->only(['category', 'status', 'priority']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        return view('maintenance.create', [
            'students' => $request->user()->hasRole(Role::Student)
                ? null
                : StudentProfile::with('user')->orderBy('student_id')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MaintenanceRequestRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $user = $request->user();

        $studentProfile = $user->hasRole(Role::Student)
            ? $user->studentProfile
            : StudentProfile::findOrFail($data['student_profile_id']);

        $ticket = MaintenanceRequest::create([
            'student_profile_id' => $studentProfile->id,
            'room_id' => $studentProfile->activeAllocation?->room_id,
            'category' => $data['category'],
            'title' => $data['title'],
            'description' => $data['description'],
            'priority' => $data['priority'],
            'status' => MaintenanceStatus::Pending,
        ]);

        return redirect()->route('maintenance.show', $ticket)->with('status', 'Request submitted.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, MaintenanceRequest $ticket): View
    {
        $this->authorize('view', $ticket);

        $ticket->load(['studentProfile.user', 'room.floor.block.hostel', 'assignedStaff', 'comments.user', 'statusLogs.changedBy']);

        return view('maintenance.show', [
            'ticket' => $ticket,
            'staff' => User::whereIn('role', [Role::Admin, Role::Warden])->orderBy('name')->get(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, MaintenanceRequest $ticket): View
    {
        $this->authorize('update', $ticket);

        return view('maintenance.edit', [
            'ticket' => $ticket,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MaintenanceRequestRequest $request, MaintenanceRequest $ticket): RedirectResponse
    {
        $this->authorize('update', $ticket);

        $data = $request->validated();

        $ticket->update([
            'category' => $data['category'],
            'title' => $data['title'],
            'description' => $data['description'],
            'priority' => $data['priority'],
        ]);

        return redirect()->route('maintenance.show', $ticket)->with('status', 'Request updated.');
    }

    /**
     * Assign (or unassign) the staff member responsible for this ticket.
     */
    public function assign(AssignStaffRequest $request, MaintenanceRequest $ticket): RedirectResponse
    {
        $this->authorize('assign', $ticket);

        $assignedTo = $request->validated()['assigned_to'];

        $ticket->update(['assigned_to' => $assignedTo]);

        if ($assignedTo) {
            User::find($assignedTo)->notify(new MaintenanceAssigned($ticket));
        }

        return redirect()->route('maintenance.show', $ticket)->with('status', 'Staff assignment updated.');
    }

    /**
     * Transition the ticket's status, recording the change in its
     * resolution history.
     */
    public function updateStatus(UpdateMaintenanceStatusRequest $request, MaintenanceRequest $ticket): RedirectResponse
    {
        $this->authorize('updateStatus', $ticket);

        $data = $request->validated();
        $fromStatus = $ticket->status;

        $ticket->update([
            'status' => $data['status'],
            'resolution_notes' => $data['status'] === MaintenanceStatus::Completed->value
                ? ($data['note'] ?? $ticket->resolution_notes)
                : $ticket->resolution_notes,
        ]);

        $ticket->statusLogs()->create([
            'changed_by' => $request->user()->id,
            'from_status' => $fromStatus,
            'to_status' => $data['status'],
            'note' => $data['note'] ?? null,
        ]);

        return redirect()->route('maintenance.show', $ticket)->with('status', 'Status updated.');
    }
}
