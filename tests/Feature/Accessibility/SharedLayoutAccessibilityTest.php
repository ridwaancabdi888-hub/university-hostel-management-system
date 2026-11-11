<?php

namespace Tests\Feature\Accessibility;

use App\Models\User;
use App\Models\Visitor;
use App\Notifications\VisitorApproved;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SharedLayoutAccessibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_shared_layout_exposes_navigation_and_flash_message_state(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->withSession([
                'status' => 'Profile saved.',
                'error' => 'A second action failed.',
            ])
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('aria-controls="primary-navigation"', false)
            ->assertSee('aria-label="Primary navigation"', false)
            ->assertSee('aria-current="page"', false)
            ->assertSee('role="status" aria-live="polite"', false)
            ->assertSee('role="alert" aria-live="assertive"', false);
    }

    public function test_notification_state_and_timestamps_have_text_alternatives(): void
    {
        $user = User::factory()->create();
        $visitor = Visitor::factory()->create();
        $user->notify(new VisitorApproved($visitor));

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('aria-label="Notifications, 1 unread"', false)
            ->assertSee('<time datetime=', false);

        $this->actingAs($user)
            ->get('/notifications')
            ->assertOk()
            ->assertSee('<span class="sr-only">Unread:</span>', false)
            ->assertSee('<time datetime=', false);
    }
}
