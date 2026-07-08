<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@hostel.test',
            'role' => Role::Admin,
        ]);

        User::factory()->create([
            'name' => 'Warden User',
            'email' => 'warden@hostel.test',
            'role' => Role::Warden,
        ]);

        User::factory()->create([
            'name' => 'Accountant User',
            'email' => 'accountant@hostel.test',
            'role' => Role::Accountant,
        ]);

        User::factory()->create([
            'name' => 'Student User',
            'email' => 'student@hostel.test',
            'role' => Role::Student,
        ]);
    }
}
