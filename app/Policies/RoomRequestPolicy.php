<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\RoomRequest;
use App\Models\User;

class RoomRequestPolicy
{
    /**
     * A student may only see their own room requests; staff may see all.
     */
    public function view(User $user, RoomRequest $roomRequest): bool
    {
        return ! $user->hasRole(Role::Student) || $roomRequest->studentProfile->user_id === $user->id;
    }

    /**
     * Only staff may approve or reject a room request.
     */
    public function approve(User $user): bool
    {
        return $user->hasRole(Role::Admin) || $user->hasRole(Role::Warden);
    }

    /**
     * Only staff may approve or reject a room request.
     */
    public function reject(User $user): bool
    {
        return $this->approve($user);
    }
}
