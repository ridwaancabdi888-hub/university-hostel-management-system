<?php

namespace App\Support;

use App\Enums\Role;

class Navigation
{
    /**
     * The application's primary navigation items.
     *
     * @return list<array{label: string, route: string, icon: string, roles: list<Role>}>
     */
    public static function items(): array
    {
        return [
            [
                'label' => 'Dashboard',
                'route' => 'dashboard',
                'icon' => 'home',
                'roles' => [Role::Admin, Role::Warden, Role::Accountant, Role::Student],
            ],
            [
                'label' => 'Room Allocation',
                'route' => 'rooms.index',
                'active' => ['rooms.*', 'hostels.*', 'blocks.*', 'floors.*', 'room-types.*', 'allocations.*'],
                'icon' => 'bed',
                'roles' => [Role::Admin, Role::Warden],
            ],
            [
                'label' => 'Student Directory',
                'route' => 'students.index',
                'active' => ['students.*'],
                'icon' => 'users',
                'roles' => [Role::Admin, Role::Warden, Role::Accountant],
            ],
            [
                'label' => 'Maintenance',
                'route' => 'maintenance',
                'icon' => 'wrench',
                'roles' => [Role::Admin, Role::Warden, Role::Student],
            ],
            [
                'label' => 'Financials',
                'route' => 'financials',
                'icon' => 'banknotes',
                'roles' => [Role::Admin, Role::Accountant],
            ],
        ];
    }

    /**
     * The navigation items visible to the given role.
     *
     * @return list<array{label: string, route: string, icon: string, roles: list<Role>}>
     */
    public static function for(Role $role): array
    {
        return array_values(array_filter(
            static::items(),
            fn (array $item) => in_array($role, $item['roles'], true)
        ));
    }
}
