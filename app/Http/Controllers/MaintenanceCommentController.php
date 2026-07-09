<?php

namespace App\Http\Controllers;

use App\Enums\Role;
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
        $user = $request->user();

        abort_if(
            $user->hasRole(Role::Student) && $ticket->studentProfile->user_id !== $user->id,
            403
        );

        $ticket->comments()->create([
            'user_id' => $user->id,
            'body' => $request->validated()['body'],
        ]);

        return redirect()->route('maintenance.show', $ticket)->with('status', 'Comment added.');
    }
}
