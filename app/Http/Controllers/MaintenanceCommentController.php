<?php

namespace App\Http\Controllers;

use App\Http\Requests\MaintenanceCommentRequest;
use App\Models\MaintenanceRequest;
use Illuminate\Http\RedirectResponse;

class MaintenanceCommentController extends Controller
{
    /**
     * Add a comment to the given maintenance ticket.
     */
    public function store(MaintenanceCommentRequest $request, MaintenanceRequest $ticket): RedirectResponse
    {
        $this->authorize('comment', $ticket);

        $ticket->comments()->create([
            'user_id' => $request->user()->id,
            'body' => $request->validated()['body'],
        ]);

        return redirect()->route('maintenance.show', $ticket)->with('status', 'Comment added.');
    }
}
