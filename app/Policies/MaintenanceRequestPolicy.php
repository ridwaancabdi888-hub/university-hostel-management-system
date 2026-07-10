<?php

namespace App\Policies;

use App\Enums\MaintenanceStatus;
use App\Enums\Role;
use App\Models\MaintenanceRequest;
use App\Models\User;

class MaintenanceRequestPolicy
{
    /**
     * A student may only see their own ticket; staff may see all.
     */
    public function view(User $user, MaintenanceRequest $ticket): bool
    {
        return ! $user->hasRole(Role::Student) || $ticket->studentProfile->user_id === $user->id;
    }

    /**
     * A student may only edit their own ticket while it's still pending;
     * staff may edit at any stage.
     */
    public function update(User $user, MaintenanceRequest $ticket): bool
    {
        if (! $this->view($user, $ticket)) {
            return false;
        }

        return ! $user->hasRole(Role::Student) || $ticket->status === MaintenanceStatus::Pending;
    }

    /**
     * Commenting follows the same ownership rule as viewing — comments
     * aren't status-gated.
     */
    public function comment(User $user, MaintenanceRequest $ticket): bool
    {
        return $this->view($user, $ticket);
    }

    /**
     * Same ownership rule as viewing. The fine-grained "student can only
     * confirm/reopen while awaiting verification" transition rule lives in
     * UpdateMaintenanceStatusRequest::withValidator(), not here.
     */
    public function updateStatus(User $user, MaintenanceRequest $ticket): bool
    {
        return $this->view($user, $ticket);
    }

    /**
     * Only staff may (re)assign a ticket to a staff member.
     */
    public function assign(User $user): bool
    {
        return $user->hasRole(Role::Admin) || $user->hasRole(Role::Warden);
    }
}
