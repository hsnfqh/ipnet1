<?php
// app/Models/Project.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'client',
        'sales_name',
        'location',
        'project_type',
        'visit_schedule',
        'description',
        'start_date',
        'deadline',
        'status',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date:Y-m-d',
        'deadline'   => 'date:Y-m-d',
    ];

    protected $appends = [
        'duration_days',
        'is_recurring',
    ];

    // Relationships
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    // Scopes
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['Planning', 'On Progress']);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'Completed');
    }

    // Accessors
    public function getProgressAttribute()
    {
        $tasks = $this->tasks;  // gunakan relasi yang sudah di-eager load (tidak ada extra query)
        $totalTasks = $tasks->count();
        if ($totalTasks === 0) return 0;

        $completedTasks = $tasks->where('status', 'Completed')->count();
        return round(($completedTasks / $totalTasks) * 100);
    }

    public function getTaskCountAttribute()
    {
        return $this->tasks->count();  // gunakan eager-loaded relation
    }

    public function getCompletedTaskCountAttribute()
    {
        return $this->tasks->where('status', 'Completed')->count();  // gunakan eager-loaded relation
    }

    public function getDurationDaysAttribute()
    {
        if (!$this->start_date || !$this->deadline) return 0;
        return max(1, $this->start_date->diffInDays($this->deadline) + 1);
    }

    public function getDurationFormattedAttribute()
    {
        $days = $this->duration_days;
        if ($days < 30) {
            return $days . ' Hari';
        }
        $months = round($days / 30, 1);
        return $months == round($months) ? intval($months) . ' Bulan' : $months . ' Bulan (' . $days . ' Hari)';
    }

    public function getIsRecurringAttribute(): bool
    {
        return !empty($this->visit_schedule) && $this->visit_schedule !== 'None' && $this->visit_schedule !== '-';
    }

    // Helpers
    public function isOverdue()
    {
        return $this->status !== 'Completed' && $this->deadline < now();
    }
}