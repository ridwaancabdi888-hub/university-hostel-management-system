<?php

namespace App\Models;

use App\Enums\AllocationStatus;
use App\Enums\Gender;
use App\Enums\RoomRequestStatus;
use App\Enums\StudentStatus;
use App\Enums\YearLevel;
use Database\Factories\StudentProfileFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class StudentProfile extends Model
{
    /** @use HasFactory<StudentProfileFactory> */
    use HasFactory;

    use LogsActivity;

    protected $fillable = [
        'user_id',
        'student_id',
        'course',
        'year_level',
        'date_of_birth',
        'gender',
        'phone',
        'address',
        'photo_path',
        'guardian_name',
        'guardian_relationship',
        'guardian_phone',
        'guardian_email',
        'guardian_address',
        'emergency_contact_name',
        'emergency_contact_relationship',
        'emergency_contact_phone',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'gender' => Gender::class,
            'year_level' => YearLevel::class,
            'status' => StudentStatus::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function photoUrl(): ?string
    {
        return $this->photo_path ? Storage::disk('public')->url($this->photo_path) : null;
    }

    public function allocations(): HasMany
    {
        return $this->hasMany(RoomAllocation::class);
    }

    public function activeAllocation(): HasOne
    {
        return $this->hasOne(RoomAllocation::class)->where('status', AllocationStatus::Active);
    }

    public function roomRequests(): HasMany
    {
        return $this->hasMany(RoomRequest::class);
    }

    public function pendingRoomRequest(): HasOne
    {
        return $this->hasOne(RoomRequest::class)->where('status', RoomRequestStatus::Pending);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'course', 'year_level'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
