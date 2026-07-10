<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\View\View;

class NotificationController extends Controller
{
    /**
     * Display all of the authenticated user's notifications.
     */
    public function index(Request $request): View
    {
        $notifications = $request->user()->notifications()->paginate(20);

        return view('notifications.index', [
            'notifications' => $notifications,
        ]);
    }

    /**
     * Mark a single notification as read and go to what it's about.
     */
    public function read(Request $request, DatabaseNotification $notification): RedirectResponse
    {
        $this->authorize('view', $notification);

        $notification->markAsRead();

        return redirect($notification->data['url'] ?? route('dashboard'));
    }

    /**
     * Mark every unread notification as read.
     */
    public function readAll(Request $request): RedirectResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        return back();
    }
}
