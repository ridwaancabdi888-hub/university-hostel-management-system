<?php

namespace Tests\Feature\Rooms;

use App\Enums\Role;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RoomGalleryTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_upload_up_to_four_gallery_photos(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create(['role' => Role::Admin]);
        $room = Room::factory()->create();

        $photos = [
            UploadedFile::fake()->image('one.jpg'),
            UploadedFile::fake()->image('two.jpg'),
            UploadedFile::fake()->image('three.jpg'),
            UploadedFile::fake()->image('four.jpg'),
        ];

        $response = $this->actingAs($admin)->post("/rooms/{$room->id}/photos", [
            'photos' => $photos,
        ]);

        $response->assertRedirect("/rooms/{$room->id}");

        $room->refresh();
        $this->assertCount(4, $room->photo_paths);

        foreach ($room->photo_paths as $path) {
            Storage::disk('public')->assertExists($path);
        }
    }

    public function test_more_than_four_photos_is_rejected(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create(['role' => Role::Admin]);
        $room = Room::factory()->create();

        $photos = array_map(fn ($i) => UploadedFile::fake()->image("photo{$i}.jpg"), range(1, 5));

        $response = $this->actingAs($admin)->post("/rooms/{$room->id}/photos", [
            'photos' => $photos,
        ]);

        $response->assertSessionHasErrors('photos');
        $this->assertNull($room->fresh()->photo_paths);
    }

    public function test_uploading_a_new_gallery_replaces_and_deletes_the_old_one(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create(['role' => Role::Admin]);
        $room = Room::factory()->create();

        $this->actingAs($admin)->post("/rooms/{$room->id}/photos", [
            'photos' => [UploadedFile::fake()->image('old.jpg')],
        ]);

        $oldPath = $room->fresh()->photo_paths[0];
        Storage::disk('public')->assertExists($oldPath);

        $this->actingAs($admin)->post("/rooms/{$room->id}/photos", [
            'photos' => [UploadedFile::fake()->image('new.jpg')],
        ]);

        Storage::disk('public')->assertMissing($oldPath);
        $this->assertNotSame($oldPath, $room->fresh()->photo_paths[0]);
    }

    public function test_a_student_cannot_upload_room_photos(): void
    {
        Storage::fake('public');

        $student = User::factory()->create(['role' => Role::Student]);
        $room = Room::factory()->create();

        $this->actingAs($student)->post("/rooms/{$room->id}/photos", [
            'photos' => [UploadedFile::fake()->image('photo.jpg')],
        ])->assertForbidden();
    }
}
