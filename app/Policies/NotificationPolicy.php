<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Notifications\DatabaseNotification;

class NotificationPolicy
{
    /**
     * A user may only view (and mark as read) their own notifications.
     *
     * Registered manually in AppServiceProvider::boot() since
     * DatabaseNotification is a framework class, not an App\Models\* model
     * covered by Laravel's policy auto-discovery.
     */
    public function view(User $user, DatabaseNotification $notification): bool
    {
        return $notification->notifiable_type === User::class && $notification->notifiable_id === $user->id;
    }
}
