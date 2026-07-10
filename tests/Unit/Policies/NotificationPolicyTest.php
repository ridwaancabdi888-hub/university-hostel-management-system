<?php

namespace Tests\Unit\Policies;

use App\Models\User;
use App\Models\Visitor;
use App\Notifications\VisitorApproved;
use App\Policies\NotificationPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_user_can_view_their_own_notification(): void
    {
        $owner = User::factory()->create();
        $visitor = Visitor::factory()->create();
        $owner->notify(new VisitorApproved($visitor));

        $notification = $owner->notifications()->firstOrFail();

        $this->assertTrue((new NotificationPolicy)->view($owner, $notification));
    }

    public function test_a_user_cannot_view_someone_elses_notification(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $visitor = Visitor::factory()->create();
        $owner->notify(new VisitorApproved($visitor));

        $notification = $owner->notifications()->firstOrFail();

        $this->assertFalse((new NotificationPolicy)->view($intruder, $notification));
    }
}
