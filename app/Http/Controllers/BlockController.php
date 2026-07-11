<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesPhotoUploads;
use App\Http\Requests\BlockPhotosRequest;
use App\Http\Requests\BlockRequest;
use App\Models\Block;
use App\Models\Hostel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class BlockController extends Controller
{
    use HandlesPhotoUploads;

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
            ...collect($data)->except(['photo', 'photo_url'])->toArray(),
            'photo_path' => $this->resolvePhotoPath($request, null, 'block-photos'),
        ]);

        return redirect()->route('blocks.index')->with('status', 'Block created.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Block $block): View
    {
        $block->load('hostel');

        return view('blocks.show', [
            'block' => $block,
            'floorCount' => $block->floors()->count(),
        ]);
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

        $block->update([
            ...collect($data)->except(['photo', 'photo_url'])->toArray(),
            'photo_path' => $this->resolvePhotoPath($request, $block->photo_path, 'block-photos'),
        ]);

        return redirect()->route('blocks.index')->with('status', 'Block updated.');
    }

    /**
     * Replace the "View Block" gallery (up to 4 photos), deleting whichever
     * gallery photos were there before.
     */
    public function addPhotos(BlockPhotosRequest $request, Block $block): RedirectResponse
    {
        foreach ($block->photo_paths ?? [] as $oldPath) {
            Storage::disk('public')->delete($oldPath);
        }

        $paths = collect($request->file('photos'))
            ->map(fn ($photo) => $photo->store('block-photos', 'public'))
            ->all();

        $block->update(['photo_paths' => $paths]);

        return redirect()->route('blocks.show', $block)->with('status', 'Block photos updated.');
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

        foreach ($block->photo_paths ?? [] as $path) {
            Storage::disk('public')->delete($path);
        }

        $block->delete();

        return redirect()->route('blocks.index')->with('status', 'Block deleted.');
    }
}
