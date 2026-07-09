<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Enums\VisitorStatus;
use App\Http\Requests\RejectVisitorRequest;
use App\Http\Requests\VisitorRequest;
use App\Models\StudentProfile;
use App\Models\User;
use App\Models\Visitor;
use App\Notifications\VisitorApproved;
use App\Notifications\VisitorRegistered;
use App\Notifications\VisitorRejected;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification as NotificationFacade;
use Illuminate\View\View;

class VisitorController extends Controller
{
    /**
     * Display a listing of the resource. Students see only their own
     * visitors; staff see everything, filterable by status.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $query = Visitor::with(['studentProfile.user', 'approvedBy']);

        if ($user->hasRole(Role::Student)) {
            $query->whereHas('studentProfile', fn ($q) => $q->where('user_id', $user->id));
        }

        if ($status = $request->string('status')->toString()) {
            $query->where('status', $status);
        }

        $visitors = $query->latest('expected_at')->paginate(15)->withQueryString();

        return view('visitors.index', [
            'visitors' => $visitors,
            'filters' => $request->only(['status']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        return view('visitors.create', [
            'students' => $request->user()->hasRole(Role::Student)
                ? null
                : StudentProfile::with('user')->orderBy('student_id')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(VisitorRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $user = $request->user();

        $studentProfile = $user->hasRole(Role::Student)
            ? $user->studentProfile
            : StudentProfile::findOrFail($data['student_profile_id']);

        $visitor = Visitor::create([
            'student_profile_id' => $studentProfile->id,
            'name' => $data['name'],
            'phone' => $data['phone'],
            'email' => $data['email'] ?? null,
            'relationship' => $data['relationship'] ?? null,
            'purpose' => $data['purpose'],
            'expected_at' => $data['expected_at'],
            'status' => VisitorStatus::Pending,
        ]);

        $staff = User::whereIn('role', [Role::Admin, Role::Warden])->get();
        NotificationFacade::send($staff, new VisitorRegistered($visitor));

        return redirect()->route('visitors.show', $visitor)->with('status', 'Visitor registered and awaiting approval.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Visitor $visitor): View
    {
        $this->authorizeAccess($request, $visitor);

        $visitor->load(['studentProfile.user', 'approvedBy']);

        return view('visitors.show', [
            'visitor' => $visitor,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Visitor $visitor): View
    {
        $this->authorizeAccess($request, $visitor);
        $this->authorizeEdit($request, $visitor);

        return view('visitors.edit', [
            'visitor' => $visitor,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(VisitorRequest $request, Visitor $visitor): RedirectResponse
    {
        $this->authorizeAccess($request, $visitor);
        $this->authorizeEdit($request, $visitor);

        $data = $request->validated();

        $visitor->update([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'email' => $data['email'] ?? null,
            'relationship' => $data['relationship'] ?? null,
            'purpose' => $data['purpose'],
            'expected_at' => $data['expected_at'],
        ]);

        return redirect()->route('visitors.show', $visitor)->with('status', 'Visitor details updated.');
    }

    /**
     * Approve the visitor registration.
     */
    public function approve(Request $request, Visitor $visitor): RedirectResponse
    {
        $visitor->update([
            'status' => VisitorStatus::Approved,
            'approved_by' => $request->user()->id,
            'approved_at' => now(),
            'rejection_reason' => null,
        ]);

        $visitor->studentProfile->user->notify(new VisitorApproved($visitor));

        return redirect()->route('visitors.show', $visitor)->with('status', 'Visitor approved.');
    }

    /**
     * Reject the visitor registration.
     */
    public function reject(RejectVisitorRequest $request, Visitor $visitor): RedirectResponse
    {
        $visitor->update([
            'status' => VisitorStatus::Rejected,
            'approved_by' => $request->user()->id,
            'approved_at' => now(),
            'rejection_reason' => $request->validated()['rejection_reason'],
        ]);

        $visitor->studentProfile->user->notify(new VisitorRejected($visitor));

        return redirect()->route('visitors.show', $visitor)->with('status', 'Visitor rejected.');
    }

    /**
     * A student may only see their own visitor registrations; staff may
     * see all.
     */
    private function authorizeAccess(Request $request, Visitor $visitor): void
    {
        $user = $request->user();

        abort_if(
            $user->hasRole(Role::Student) && $visitor->studentProfile->user_id !== $user->id,
            403
        );
    }

    /**
     * A student may only edit their own registration while it's still
     * pending; staff may edit at any stage.
     */
    private function authorizeEdit(Request $request, Visitor $visitor): void
    {
        $user = $request->user();

        abort_if(
            $user->hasRole(Role::Student) && $visitor->status !== VisitorStatus::Pending,
            403,
            'This registration can no longer be edited.'
        );
    }
}
