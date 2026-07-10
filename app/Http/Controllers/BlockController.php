<?php

namespace App\Http\Controllers;

use App\Http\Requests\BlockRequest;
use App\Models\Block;
use App\Models\Hostel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class BlockController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $blocks = Block::with('hostel')
            ->withCount('floors')
            ->orderBy('name')
            ->paginate(10);

        return view('blocks.index', [
            'blocks' => $blocks,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('blocks.create', [
            'hostels' => Hostel::orderBy('name')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BlockRequest $request): RedirectResponse
    {
        $data = $request->validated();

        Block::create([
            ...collect($data)->except('photo')->toArray(),
            'photo_path' => $request->hasFile('photo') ? $request->file('photo')->store('block-photos', 'public') : null,
        ]);

        return redirect()->route('blocks.index')->with('status', 'Block created.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Block $block): View
    {
        return view('blocks.edit', [
            'block' => $block,
            'hostels' => Hostel::orderBy('name')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BlockRequest $request, Block $block): RedirectResponse
    {
        $data = $request->validated();
        $photoPath = $block->photo_path;

        if ($request->hasFile('photo')) {
            if ($photoPath) {
                Storage::disk('public')->delete($photoPath);
            }

            $photoPath = $request->file('photo')->store('block-photos', 'public');
        }

        $block->update([
            ...collect($data)->except('photo')->toArray(),
            'photo_path' => $photoPath,
        ]);

        return redirect()->route('blocks.index')->with('status', 'Block updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Block $block): RedirectResponse
    {
        if ($block->floors()->exists()) {
            return redirect()->route('blocks.index')
                ->with('error', "Cannot delete \"{$block->name}\" while it still has floors assigned to it.");
        }

        if ($block->photo_path) {
            Storage::disk('public')->delete($block->photo_path);
        }

        $block->delete();

        return redirect()->route('blocks.index')->with('status', 'Block deleted.');
    }
}
