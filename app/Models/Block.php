<?php

namespace App\Models;

use Database\Factories\BlockFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Block extends Model
{
    /** @use HasFactory<BlockFactory> */
    use HasFactory;

    protected $fillable = [
        'hostel_id',
        'name',
        'code',
        'description',
        'photo_path',
        'photo_paths',
    ];

    protected function casts(): array
    {
        return [
            'photo_paths' => 'array',
        ];
    }

    public function hostel(): BelongsTo
    {
        return $this->belongsTo(Hostel::class);
    }

    public function floors(): HasMany
    {
        return $this->hasMany(Floor::class);
    }

    public function photoUrl(): ?string
    {
        return $this->photo_path ? Storage::disk('public')->url($this->photo_path) : null;
    }

    /**
     * URLs for the "View Block" gallery — up to 4 photos.
     *
     * @return list<string>
     */
    public function photoUrls(): array
    {
        return collect($this->photo_paths ?? [])
            ->map(fn (string $path) => Storage::disk('public')->url($path))
            ->all();
    }
}
