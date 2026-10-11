<?php

namespace Tests\Unit\Support;

use App\Enums\Role;
use App\Support\Navigation;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class NavigationTest extends TestCase
{
    /**
     * @param  list<string>  $expectedLabels
     * @param  list<string>  $expectedRoutes
     */
    #[DataProvider('roleNavigationProvider')]
    public function test_each_role_receives_the_expected_navigation_items(
        Role $role,
        array $expectedLabels,
        array $expectedRoutes,
    ): void {
        $items = Navigation::for($role);

        $this->assertSame($expectedLabels, array_column($items, 'label'));
        $this->assertSame($expectedRoutes, array_column($items, 'route'));
    }

    public function test_every_navigation_item_has_valid_metadata_and_a_registered_route(): void
    {
        foreach (Navigation::items() as $item) {
            $this->assertNotSame('', $item['label']);
            $this->assertNotSame('', $item['icon']);
            $this->assertTrue(Route::has($item['route']), "Unknown navigation route [{$item['route']}].");
            $this->assertNotEmpty($item['roles']);

            foreach ($item['roles'] as $role) {
                $this->assertInstanceOf(Role::class, $role);
            }

            $roleValues = array_map(fn (Role $role) => $role->value, $item['roles']);
            $this->assertSame($roleValues, array_values(array_unique($roleValues)));

            if (isset($item['active'])) {
                $this->assertNotEmpty($item['active']);
                $this->assertNotContains('', $item['active']);
            }
        }
    }

    /**
     * @return iterable<string, array{Role, list<string>, list<string>}>
     */
    public static function roleNavigationProvider(): iterable
    {
        yield 'administrator' => [
            Role::Admin,
            [
                'Dashboard',
                'Room Allocation',
                'Student Directory',
                'Maintenance',
                'Visitors',
                'Room Requests',
                'Financials',
                'Reports',
                'User Management',
                'Activity Log',
            ],
            [
                'dashboard',
                'rooms.index',
                'students.index',
                'maintenance.index',
                'visitors.index',
                'room-requests.index',
                'invoices.index',
                'reports.index',
                'users.index',
                'activity-log.index',
            ],
        ];

        yield 'warden' => [
            Role::Warden,
            [
                'Dashboard',
                'Room Allocation',
                'Student Directory',
                'Maintenance',
                'Visitors',
                'Room Requests',
                'Reports',
            ],
            [
                'dashboard',
                'rooms.index',
                'students.index',
                'maintenance.index',
                'visitors.index',
                'room-requests.index',
                'reports.index',
            ],
        ];

        yield 'accountant' => [
            Role::Accountant,
            ['Dashboard', 'Student Directory', 'Financials', 'Reports'],
            ['dashboard', 'students.index', 'invoices.index', 'reports.index'],
        ];

        yield 'student' => [
            Role::Student,
            ['Dashboard', 'Maintenance', 'Visitors', 'Room Requests', 'My Bills'],
            ['dashboard', 'maintenance.index', 'visitors.index', 'room-requests.index', 'invoices.index'],
        ];
    }
}
