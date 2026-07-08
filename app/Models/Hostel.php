<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Hostel extends Model
{
    protected $fillable = [
        'name',
        'code',
        'address',
        'description',
    ];

    public function blocks(): HasMany
    {
        return $this->hasMany(Block::class);
    }
}
