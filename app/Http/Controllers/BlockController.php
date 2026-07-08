<?php

namespace App\Http\Controllers;

use App\Http\Requests\BlockRequest;
use App\Models\Block;
use App\Models\Hostel;
use Illuminate\Http\RedirectResponse;
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
        Block::create($request->validated());

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
        $block->update($request->validated());

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

        $block->delete();

        return redirect()->route('blocks.index')->with('status', 'Block deleted.');
    }
}
