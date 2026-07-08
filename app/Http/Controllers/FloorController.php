<?php

namespace App\Http\Controllers;

use App\Http\Requests\FloorRequest;
use App\Models\Block;
use App\Models\Floor;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class FloorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $floors = Floor::with('block.hostel')
            ->withCount('rooms')
            ->orderBy('block_id')
            ->orderBy('level')
            ->paginate(10);

        return view('floors.index', [
            'floors' => $floors,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('floors.create', [
            'blocks' => Block::with('hostel')->orderBy('name')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FloorRequest $request): RedirectResponse
    {
        Floor::create($request->validated());

        return redirect()->route('floors.index')->with('status', 'Floor created.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Floor $floor): View
    {
        return view('floors.edit', [
            'floor' => $floor,
            'blocks' => Block::with('hostel')->orderBy('name')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(FloorRequest $request, Floor $floor): RedirectResponse
    {
        $floor->update($request->validated());

        return redirect()->route('floors.index')->with('status', 'Floor updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Floor $floor): RedirectResponse
    {
        if ($floor->rooms()->exists()) {
            return redirect()->route('floors.index')
                ->with('error', "Cannot delete \"{$floor->name}\" while it still has rooms assigned to it.");
        }

        $floor->delete();

        return redirect()->route('floors.index')->with('status', 'Floor deleted.');
    }
}
