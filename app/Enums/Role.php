<?php

namespace App\Enums;

enum Role: string
{
    case Admin = 'admin';
    case Warden = 'warden';
    case Accountant = 'accountant';
    case Student = 'student';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Administrator',
            self::Warden => 'Warden',
            self::Accountant => 'Accountant',
            self::Student => 'Student',
        };
    }

    public function dashboardRoute(): string
    {
        return match ($this) {
            self::Admin => 'admin.dashboard',
            self::Warden => 'warden.dashboard',
            self::Accountant => 'accountant.dashboard',
            self::Student => 'student.dashboard',
        };
    }
}
