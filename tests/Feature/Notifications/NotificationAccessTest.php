<?php

namespace Tests\Feature\Notifications;

use App\Models\User;
use App\Models\Visitor;
use App\Notifications\VisitorApproved;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_user_cannot_mark_another_users_notification_as_read(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();

        $visitor = Visitor::factory()->create();
        $owner->notify(new VisitorApproved($visitor));
        $notification = $owner->notifications()->firstOrFail();

        $this->actingAs($intruder)
            ->post("/notifications/{$notification->id}/read")
            ->assertForbidden();

        $this->assertNull($notification->fresh()->read_at);
    }

    public function test_a_user_can_mark_their_own_notification_as_read(): void
    {
        $owner = User::factory()->create();

        $visitor = Visitor::factory()->create();
        $owner->notify(new VisitorApproved($visitor));
        $notification = $owner->notifications()->firstOrFail();

        $this->actingAs($owner)
            ->post("/notifications/{$notification->id}/read")
            ->assertRedirect();

        $this->assertNotNull($notification->fresh()->read_at);
    }
}
