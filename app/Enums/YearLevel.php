<?php

namespace App\Enums;

enum YearLevel: string
{
    case Freshman = 'freshman';
    case Sophomore = 'sophomore';
    case Junior = 'junior';
    case Senior = 'senior';
    case Graduate = 'graduate';

    public function label(): string
    {
        return match ($this) {
            self::Freshman => 'Freshman',
            self::Sophomore => 'Sophomore',
            self::Junior => 'Junior',
            self::Senior => 'Senior',
            self::Graduate => 'Graduate',
        };
    }
}
