<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Http\Requests\StudentRequest;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = StudentProfile::with('user');

        if ($search = $request->string('search')->trim()->toString()) {
            $query->where(function ($query) use ($search) {
                $query->where('student_id', 'like', "%{$search}%")
                    ->orWhere('course', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($q) => $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%"));
            });
        }

        if ($course = $request->string('course')->trim()->toString()) {
            $query->where('course', $course);
        }

        if ($yearLevel = $request->string('year_level')->toString()) {
            $query->where('year_level', $yearLevel);
        }

        if ($status = $request->string('status')->toString()) {
            $query->where('status', $status);
        }

        $students = $query->latest()->paginate(15)->withQueryString();

        return view('students.index', [
            'students' => $students,
            'courses' => StudentProfile::query()->distinct()->orderBy('course')->pluck('course'),
            'filters' => $request->only(['search', 'course', 'year_level', 'status']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('students.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StudentRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $student = DB::transaction(function () use ($data, $request) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => Role::Student,
                'email_verified_at' => now(),
            ]);

            return $user->studentProfile()->create([
                ...collect($data)->except(['name', 'email', 'password', 'photo'])->toArray(),
                'photo_path' => $request->hasFile('photo') ? $request->file('photo')->store('student-photos', 'public') : null,
            ]);
        });

        return redirect()->route('students.show', $student)->with('status', 'Student registered.');
    }

    /**
     * Display the specified resource.
     */
    public function show(StudentProfile $student): View
    {
        $student->load(['user', 'activeAllocation.room.floor.block.hostel']);

        return view('students.show', [
            'student' => $student,
            'allocationHistory' => $student->allocations()->with('room.floor.block.hostel')->latest('allocated_at')->get(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StudentProfile $student): View
    {
        $student->load('user');

        return view('students.edit', [
            'student' => $student,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StudentRequest $request, StudentProfile $student): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($data, $request, $student) {
            $student->user->update([
                'name' => $data['name'],
                'email' => $data['email'],
            ]);

            if (! empty($data['password'])) {
                $student->user->update(['password' => Hash::make($data['password'])]);
            }

            $photoPath = $student->photo_path;

            if ($request->hasFile('photo')) {
                if ($photoPath) {
                    Storage::disk('public')->delete($photoPath);
                }

                $photoPath = $request->file('photo')->store('student-photos', 'public');
            }

            $student->update([
                ...collect($data)->except(['name', 'email', 'password', 'photo'])->toArray(),
                'photo_path' => $photoPath,
            ]);
        });

        return redirect()->route('students.show', $student)->with('status', 'Student updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StudentProfile $student): RedirectResponse
    {
        if ($student->activeAllocation) {
            return redirect()->route('students.show', $student)
                ->with('error', 'Vacate this student\'s room allocation before removing them.');
        }

        if ($student->photo_path) {
            Storage::disk('public')->delete($student->photo_path);
        }

        $student->user->delete();

        return redirect()->route('students.index')->with('status', 'Student removed.');
    }
}
