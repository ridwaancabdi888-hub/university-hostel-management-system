<?php

namespace App\Models;

use Database\Factories\RoomTypeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RoomType extends Model
{
    /** @use HasFactory<RoomTypeFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'default_capacity',
        'monthly_rate',
        'description',
        'amenities',
    ];

    protected function casts(): array
    {
        return [
            'default_capacity' => 'integer',
            'monthly_rate' => 'decimal:2',
            'amenities' => 'array',
        ];
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }
}
