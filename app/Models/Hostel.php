<?php

namespace App\Models;

use Database\Factories\HostelFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Hostel extends Model
{
    /** @use HasFactory<HostelFactory> */
    use HasFactory;

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
