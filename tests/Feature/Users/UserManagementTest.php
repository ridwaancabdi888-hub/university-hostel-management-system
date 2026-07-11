<?php

namespace Tests\Feature\Users;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_a_staff_user_with_a_role(): void
    {
        $admin = User::factory()->create(['role' => Role::Admin]);

        $response = $this->actingAs($admin)->post('/users', [
            'name' => 'New Warden',
            'email' => 'new.warden@hostel.test',
            'password' => 'password123',
            'role' => Role::Warden->value,
        ]);

        $response->assertRedirect('/users');
        $this->assertDatabaseHas('users', [
            'email' => 'new.warden@hostel.test',
            'role' => Role::Warden->value,
        ]);
    }

    public function test_the_role_field_only_accepts_staff_roles(): void
    {
        $admin = User::factory()->create(['role' => Role::Admin]);

        $response = $this->actingAs($admin)->post('/users', [
            'name' => 'Sneaky Student',
            'email' => 'sneaky@hostel.test',
            'password' => 'password123',
            'role' => Role::Student->value,
        ]);

        $response->assertSessionHasErrors('role');
        $this->assertDatabaseMissing('users', ['email' => 'sneaky@hostel.test']);
    }

    public function test_admin_can_update_a_users_name_email_and_role(): void
    {
        $admin = User::factory()->create(['role' => Role::Admin]);
        $warden = User::factory()->create(['role' => Role::Warden]);

        $response = $this->actingAs($admin)->put("/users/{$warden->id}", [
            'name' => 'Renamed Warden',
            'email' => $warden->email,
            'role' => Role::Accountant->value,
        ]);

        $response->assertRedirect('/users');
        $this->assertSame('Renamed Warden', $warden->fresh()->name);
        $this->assertSame(Role::Accountant, $warden->fresh()->role);
    }

    public function test_a_student_account_cannot_be_edited_from_user_management(): void
    {
        $admin = User::factory()->create(['role' => Role::Admin]);
        $student = User::factory()->create(['role' => Role::Student]);

        $this->actingAs($admin)->get("/users/{$student->id}/edit")->assertNotFound();
        $this->actingAs($admin)->put("/users/{$student->id}", [
            'name' => 'Hacked',
            'email' => $student->email,
            'role' => Role::Admin->value,
        ])->assertNotFound();
    }

    public function test_an_admin_cannot_change_their_own_role(): void
    {
        $admin = User::factory()->create(['role' => Role::Admin]);
        User::factory()->create(['role' => Role::Admin]);

        $response = $this->actingAs($admin)->put("/users/{$admin->id}", [
            'name' => $admin->name,
            'email' => $admin->email,
            'role' => Role::Warden->value,
        ]);

        $response->assertSessionHas('error');
        $this->assertSame(Role::Admin, $admin->fresh()->role);
    }

    public function test_a_user_cannot_delete_their_own_account(): void
    {
        $admin = User::factory()->create(['role' => Role::Admin]);
        User::factory()->create(['role' => Role::Admin]);

        $response = $this->actingAs($admin)->delete("/users/{$admin->id}");

        $response->assertSessionHas('error');
        $this->assertNotNull($admin->fresh());
    }

    public function test_an_admin_can_delete_another_admin(): void
    {
        $admin = User::factory()->create(['role' => Role::Admin]);
        $anotherAdmin = User::factory()->create(['role' => Role::Admin]);

        $this->actingAs($admin)->delete("/users/{$anotherAdmin->id}");

        $this->assertNull($anotherAdmin->fresh());
    }

    public function test_the_users_index_excludes_student_accounts(): void
    {
        $admin = User::factory()->create(['role' => Role::Admin]);
        $student = User::factory()->create(['role' => Role::Student, 'name' => 'A Student Nobody']);

        $response = $this->actingAs($admin)->get('/users');

        $response->assertOk();
        $response->assertDontSee('A Student Nobody');
    }
}
