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
                'route' => 'maintenance.index',
                'active' => ['maintenance.*'],
                'icon' => 'wrench',
                'roles' => [Role::Admin, Role::Warden, Role::Student],
            ],
            [
                'label' => 'Visitors',
                'route' => 'visitors.index',
                'active' => ['visitors.*'],
                'icon' => 'user-plus',
                'roles' => [Role::Admin, Role::Warden, Role::Student],
            ],
            [
                'label' => 'Financials',
                'route' => 'invoices.index',
                'active' => ['invoices.*', 'payments.*'],
                'icon' => 'banknotes',
                'roles' => [Role::Admin, Role::Accountant],
            ],
            [
                'label' => 'Reports',
                'route' => 'reports.index',
                'active' => ['reports.*'],
                'icon' => 'chart-bar',
                'roles' => [Role::Admin, Role::Warden, Role::Accountant],
            ],
            [
                'label' => 'Activity Log',
                'route' => 'activity-log.index',
                'active' => ['activity-log.*'],
                'icon' => 'settings',
                'roles' => [Role::Admin],
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
