<?php

namespace Tests\Feature\Rooms;

use App\Enums\Role;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RoomBulkPhotoTest extends TestCase
{
    use RefreshDatabase;

    private const TINY_PNG = "\x89PNG\r\n\x1a\n\x00\x00\x00\rIHDR\x00\x00\x00\x01\x00\x00\x00\x01\x08\x06\x00\x00\x00\x1f\x15\xc4\x89\x00\x00\x00\nIDATx\x9cc\x00\x01\x00\x00\x05\x00\x01\r\n-\xb4\x00\x00\x00\x00IEND\xaeB`\x82";

    public function test_a_url_photo_can_be_applied_to_many_rooms_at_once(): void
    {
        Storage::fake('public');
        Http::fake([
            'example.com/*' => Http::response(self::TINY_PNG, 200, ['Content-Type' => 'image/png']),
        ]);

        $admin = User::factory()->create(['role' => Role::Admin]);
        $rooms = Room::factory()->count(3)->create();

        $response = $this->actingAs($admin)->post('/rooms/bulk-photo', [
            'room_ids' => $rooms->pluck('id')->all(),
            'photo_url' => 'https://example.com/photo.png',
        ]);

        $response->assertRedirect('/rooms');

        $paths = [];

        foreach ($rooms as $room) {
            $room->refresh();
            $this->assertNotNull($room->photo_path);
            Storage::disk('public')->assertExists($room->photo_path);
            $paths[] = $room->photo_path;
        }

        // Each room gets its own stored copy rather than sharing one path,
        // so replacing one room's photo later can never delete a file
        // another room still references.
        $this->assertSame(3, count(array_unique($paths)));
    }

    public function test_bulk_photo_update_requires_a_photo_or_a_url(): void
    {
        $admin = User::factory()->create(['role' => Role::Admin]);
        $rooms = Room::factory()->count(2)->create();

        $response = $this->actingAs($admin)->post('/rooms/bulk-photo', [
            'room_ids' => $rooms->pluck('id')->all(),
        ]);

        $response->assertSessionHasErrors('photo');
    }

    public function test_a_student_cannot_bulk_update_room_photos(): void
    {
        $student = User::factory()->create(['role' => Role::Student]);
        $rooms = Room::factory()->count(2)->create();

        $this->actingAs($student)->post('/rooms/bulk-photo', [
            'room_ids' => $rooms->pluck('id')->all(),
            'photo_url' => 'https://example.com/photo.png',
        ])->assertForbidden();
    }

    public function test_rooms_can_be_filtered_by_bed_capacity(): void
    {
        $admin = User::factory()->create(['role' => Role::Admin]);
        $single = Room::factory()->create(['room_number' => 'CAP-1', 'capacity' => 1]);
        $double = Room::factory()->create(['room_number' => 'CAP-2', 'capacity' => 2]);

        $response = $this->actingAs($admin)->get('/rooms?capacity=1');

        $response->assertOk();
        $response->assertSee($single->room_number);
        $response->assertDontSee($double->room_number);
    }

    /**
     * Regression test: the bulk-photo <form> previously wrapped the whole
     * room table, and each row's Delete button renders its own <form> —
     * illegal nested HTML forms. Browsers respond to a nested <form> by
     * reparenting its contents (including its "_method=DELETE" spoofing
     * field) into the outer form instead of dropping them, so submitting
     * "Apply Photo to Selected" was actually submitted as a DELETE to
     * /rooms/bulk-photo, which 404'd trying to route-model-bind
     * "bulk-photo" as a room ID. The bulk form must never wrap the table.
     */
    public function test_the_bulk_photo_form_does_not_wrap_the_room_table(): void
    {
        $admin = User::factory()->create(['role' => Role::Admin]);
        Room::factory()->create();

        $html = $this->actingAs($admin)->get('/rooms')->getContent();

        $formStart = strpos($html, 'action="'.route('rooms.bulk-photo').'"');
        $formEnd = strpos($html, '</form>', $formStart);
        $tableStart = strpos($html, '<table');

        $this->assertNotFalse($formStart);
        $this->assertNotFalse($tableStart);
        $this->assertLessThan($tableStart, $formEnd, 'The bulk-photo form must close before the room table (with its per-row Delete forms) begins.');
    }
}
