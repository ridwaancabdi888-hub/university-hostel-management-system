<?php

namespace App\Support;

use App\Enums\Role;

class ReportAccess
{
    /**
     * Which reports each role is allowed to see, in tab display order.
     *
     * @var array<string, list<string>>
     */
    private const REPORTS_BY_ROLE = [
        'admin' => ['occupancy', 'billing', 'payments', 'students', 'hostels'],
        'warden' => ['occupancy', 'students', 'hostels'],
        'accountant' => ['billing', 'payments', 'students'],
    ];

    /**
     * @return list<string>
     */
    public static function for(Role $role): array
    {
        return self::REPORTS_BY_ROLE[$role->value] ?? [];
    }
}
