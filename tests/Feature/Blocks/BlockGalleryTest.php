<?php

namespace Tests\Feature\Blocks;

use App\Enums\Role;
use App\Models\Block;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BlockGalleryTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_a_block(): void
    {
        $admin = User::factory()->create(['role' => Role::Admin]);
        $block = Block::factory()->create();

        $this->actingAs($admin)->get("/blocks/{$block->id}")->assertOk();
    }

    public function test_admin_can_upload_up_to_four_gallery_photos(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create(['role' => Role::Admin]);
        $block = Block::factory()->create();

        $photos = [
            UploadedFile::fake()->image('one.jpg'),
            UploadedFile::fake()->image('two.jpg'),
            UploadedFile::fake()->image('three.jpg'),
            UploadedFile::fake()->image('four.jpg'),
        ];

        $response = $this->actingAs($admin)->post("/blocks/{$block->id}/photos", [
            'photos' => $photos,
        ]);

        $response->assertRedirect("/blocks/{$block->id}");

        $block->refresh();
        $this->assertCount(4, $block->photo_paths);

        foreach ($block->photo_paths as $path) {
            Storage::disk('public')->assertExists($path);
        }
    }

    public function test_more_than_four_photos_is_rejected(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create(['role' => Role::Admin]);
        $block = Block::factory()->create();

        $photos = array_map(fn ($i) => UploadedFile::fake()->image("photo{$i}.jpg"), range(1, 5));

        $response = $this->actingAs($admin)->post("/blocks/{$block->id}/photos", [
            'photos' => $photos,
        ]);

        $response->assertSessionHasErrors('photos');
        $this->assertNull($block->fresh()->photo_paths);
    }

    public function test_uploading_a_new_gallery_replaces_and_deletes_the_old_one(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create(['role' => Role::Admin]);
        $block = Block::factory()->create();

        $this->actingAs($admin)->post("/blocks/{$block->id}/photos", [
            'photos' => [UploadedFile::fake()->image('old.jpg')],
        ]);

        $oldPath = $block->fresh()->photo_paths[0];
        Storage::disk('public')->assertExists($oldPath);

        $this->actingAs($admin)->post("/blocks/{$block->id}/photos", [
            'photos' => [UploadedFile::fake()->image('new.jpg')],
        ]);

        Storage::disk('public')->assertMissing($oldPath);
        $this->assertNotSame($oldPath, $block->fresh()->photo_paths[0]);
    }

    public function test_a_student_cannot_upload_block_photos(): void
    {
        Storage::fake('public');

        $student = User::factory()->create(['role' => Role::Student]);
        $block = Block::factory()->create();

        $this->actingAs($student)->post("/blocks/{$block->id}/photos", [
            'photos' => [UploadedFile::fake()->image('photo.jpg')],
        ])->assertForbidden();
    }
}
