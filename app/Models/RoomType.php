<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RoomType extends Model
{
    protected $fillable = [
        'name',
        'default_capacity',
        'monthly_rate',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'default_capacity' => 'integer',
            'monthly_rate' => 'decimal:2',
        ];
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }
}
