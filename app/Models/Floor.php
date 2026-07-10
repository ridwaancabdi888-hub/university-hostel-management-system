<?php

namespace App\Models;

use Database\Factories\FloorFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Floor extends Model
{
    /** @use HasFactory<FloorFactory> */
    use HasFactory;

    protected $fillable = [
        'block_id',
        'name',
        'level',
    ];

    public function block(): BelongsTo
    {
        return $this->belongsTo(Block::class);
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }
}
