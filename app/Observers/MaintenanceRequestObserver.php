<?php

namespace App\Observers;

use App\Enums\MaintenanceStatus;
use App\Models\MaintenanceRequest;

class MaintenanceRequestObserver
{
    /**
     * Handle the MaintenanceRequest "created" event.
     */
    public function created(MaintenanceRequest $maintenanceRequest): void
    {
        $maintenanceRequest->statusLogs()->create([
            'changed_by' => auth()->id(),
            'from_status' => null,
            'to_status' => $maintenanceRequest->status,
        ]);
    }

    /**
     * Handle the MaintenanceRequest "updated" event.
     *
     * Explicit status-change logging (with a note) happens in the
     * controller action; this just keeps resolved_at authoritative
     * whichever way the status was changed.
     */
    public function updated(MaintenanceRequest $maintenanceRequest): void
    {
        if (! $maintenanceRequest->wasChanged('status')) {
            return;
        }

        if ($maintenanceRequest->status === MaintenanceStatus::Completed && ! $maintenanceRequest->resolved_at) {
            $maintenanceRequest->updateQuietly(['resolved_at' => now()]);
        } elseif ($maintenanceRequest->status !== MaintenanceStatus::Completed && $maintenanceRequest->resolved_at) {
            $maintenanceRequest->updateQuietly(['resolved_at' => null]);
        }
    }
}
