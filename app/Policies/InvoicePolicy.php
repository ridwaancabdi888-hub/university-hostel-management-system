<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\Invoice;
use App\Models\User;

class InvoicePolicy
{
    /**
     * A student may only view their own invoices; staff may view all.
     */
    public function view(User $user, Invoice $invoice): bool
    {
        return ! $user->hasRole(Role::Student) || $invoice->studentProfile->user_id === $user->id;
    }
}
