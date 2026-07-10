<?php

namespace App\Models;

use App\Enums\MaintenanceCategory;
use App\Enums\MaintenancePriority;
use App\Enums\MaintenanceStatus;
use App\Observers\MaintenanceRequestObserver;
use Database\Factories\MaintenanceRequestFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

#[ObservedBy(MaintenanceRequestObserver::class)]
class MaintenanceRequest extends Model
{
    /** @use HasFactory<MaintenanceRequestFactory> */
    use HasFactory;

    use LogsActivity;

    protected $fillable = [
        'student_profile_id',
        'room_id',
        'assigned_to',
        'category',
        'title',
        'description',
        'priority',
        'status',
        'resolution_notes',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'category' => MaintenanceCategory::class,
            'priority' => MaintenancePriority::class,
            'status' => MaintenanceStatus::class,
            'resolved_at' => 'datetime',
        ];
    }

    public function studentProfile(): BelongsTo
    {
        return $this->belongsTo(StudentProfile::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function assignedStaff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(MaintenanceComment::class)->latest();
    }

    public function statusLogs(): HasMany
    {
        return $this->hasMany(MaintenanceStatusLog::class)->latest();
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'priority', 'assigned_to', 'resolution_notes'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
