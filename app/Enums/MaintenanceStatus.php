<?php

namespace App\Enums;

enum MaintenanceStatus: string
{
    case Pending = 'pending';
    case InProgress = 'in_progress';
    case Verification = 'verification';
    case Completed = 'completed';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::InProgress => 'In Progress',
            self::Verification => 'Verification',
            self::Completed => 'Completed',
        };
    }
}
