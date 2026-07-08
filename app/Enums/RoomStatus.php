<?php

namespace App\Enums;

enum RoomStatus: string
{
    case Available = 'available';
    case Maintenance = 'maintenance';

    public function label(): string
    {
        return match ($this) {
            self::Available => 'Available',
            self::Maintenance => 'Maintenance',
        };
    }
}
