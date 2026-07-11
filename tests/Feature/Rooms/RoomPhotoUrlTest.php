<?php

namespace Tests\Feature\Rooms;

use App\Enums\Role;
use App\Enums\RoomStatus;
use App\Models\Floor;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RoomPhotoUrlTest extends TestCase
{
    use RefreshDatabase;

    private const TINY_PNG = "\x89PNG\r\n\x1a\n\x00\x00\x00\rIHDR\x00\x00\x00\x01\x00\x00\x00\x01\x08\x06\x00\x00\x00\x1f\x15\xc4\x89\x00\x00\x00\nIDATx\x9cc\x00\x01\x00\x00\x05\x00\x01\r\n-\xb4\x00\x00\x00\x00IEND\xaeB`\x82";

    public function test_admin_can_set_a_room_photo_by_pasting_a_url(): void
    {
        Storage::fake('public');
        Http::fake([
            'example.com/*' => Http::response(self::TINY_PNG, 200, ['Content-Type' => 'image/png']),
        ]);

        $admin = User::factory()->create(['role' => Role::Admin]);
        $floor = Floor::factory()->create();
        $roomType = RoomType::factory()->create();

        $response = $this->actingAs($admin)->post('/rooms', [
            'floor_id' => $floor->id,
            'room_type_id' => $roomType->id,
            'room_number' => 'Z-999',
            'capacity' => 2,
            'status' => RoomStatus::Available->value,
            'photo_url' => 'https://example.com/photo.png',
        ]);

        $response->assertRedirect('/rooms');
        $room = Room::where('room_number', 'Z-999')->first();
        $this->assertNotNull($room->photo_path);
        Storage::disk('public')->assertExists($room->photo_path);
    }

    public function test_an_invalid_photo_url_is_rejected_with_a_validation_error(): void
    {
        $admin = User::factory()->create(['role' => Role::Admin]);
        $floor = Floor::factory()->create();
        $roomType = RoomType::factory()->create();

        $response = $this->actingAs($admin)->post('/rooms', [
            'floor_id' => $floor->id,
            'room_type_id' => $roomType->id,
            'room_number' => 'Z-998',
            'capacity' => 2,
            'status' => RoomStatus::Available->value,
            'photo_url' => 'http://127.0.0.1/photo.png',
        ]);

        $response->assertSessionHasErrors('photo_url');
        $this->assertDatabaseMissing('rooms', ['room_number' => 'Z-998']);
    }
}
