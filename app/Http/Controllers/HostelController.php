<?php

namespace App\Http\Controllers;

use App\Http\Requests\HostelRequest;
use App\Models\Hostel;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class HostelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $hostels = Hostel::withCount('blocks')
            ->orderBy('name')
            ->paginate(10);

        return view('hostels.index', [
            'hostels' => $hostels,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('hostels.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(HostelRequest $request): RedirectResponse
    {
        Hostel::create($request->validated());

        return redirect()->route('hostels.index')->with('status', 'Hostel created.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Hostel $hostel): View
    {
        return view('hostels.edit', [
            'hostel' => $hostel,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(HostelRequest $request, Hostel $hostel): RedirectResponse
    {
        $hostel->update($request->validated());

        return redirect()->route('hostels.index')->with('status', 'Hostel updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Hostel $hostel): RedirectResponse
    {
        if ($hostel->blocks()->exists()) {
            return redirect()->route('hostels.index')
                ->with('error', "Cannot delete \"{$hostel->name}\" while it still has blocks assigned to it.");
        }

        $hostel->delete();

        return redirect()->route('hostels.index')->with('status', 'Hostel deleted.');
    }
}
