<?php

namespace App\Enums;

enum AllocationStatus: string
{
    case Active = 'active';
    case Transferred = 'transferred';
    case Vacated = 'vacated';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Active',
            self::Transferred => 'Transferred',
            self::Vacated => 'Vacated',
        };
    }
}
