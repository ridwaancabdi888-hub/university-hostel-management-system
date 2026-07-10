<?php

namespace App\Policies;

use App\Enums\Role;
use App\Enums\VisitorStatus;
use App\Models\User;
use App\Models\Visitor;

class VisitorPolicy
{
    /**
     * A student may only see their own visitor registrations; staff may
     * see all.
     */
    public function view(User $user, Visitor $visitor): bool
    {
        return ! $user->hasRole(Role::Student) || $visitor->studentProfile->user_id === $user->id;
    }

    /**
     * A student may only edit their own registration while it's still
     * pending; staff may edit at any stage.
     */
    public function update(User $user, Visitor $visitor): bool
    {
        if (! $this->view($user, $visitor)) {
            return false;
        }

        return ! $user->hasRole(Role::Student) || $visitor->status === VisitorStatus::Pending;
    }

    /**
     * Only staff may approve or reject a visitor registration.
     */
    public function approve(User $user): bool
    {
        return $user->hasRole(Role::Admin) || $user->hasRole(Role::Warden);
    }

    /**
     * Only staff may approve or reject a visitor registration.
     */
    public function reject(User $user): bool
    {
        return $this->approve($user);
    }
}
