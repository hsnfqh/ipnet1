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
        'stage',
        'process_status',
        'created_by',
        'pm_id',
        'division_id',
        'documents_checklist',
    ];

    protected $casts = [
        'start_date'          => 'date:Y-m-d',
        'deadline'            => 'date:Y-m-d',
        'documents_checklist' => 'array',
    ];

    protected $appends = [
        'duration_days',
        'is_recurring',
    ];

    // Relationships
    public function pm()
    {
        return $this->belongsTo(User::class, 'pm_id');
    }

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

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
        if ($totalTasks === 0) {
            return (int) ($this->attributes['progress'] ?? 0);
        }

        // Hitung rata-rata progress riil dari seluruh task di project ini
        $avgProgress = $tasks->avg('progress');
        if ($avgProgress !== null && $avgProgress > 0) {
            return round($avgProgress);
        }

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

    /**
     * Sinkronisasi status project secara otomatis berdasarkan status & progress tasks terkait
     */
    public function syncStatusWithTasks(): void
    {
        $tasks = $this->tasks()->get();
        if ($tasks->isEmpty()) {
            return;
        }

        if ($tasks->every(fn($t) => $t->status === 'Completed')) {
            $newStatus = 'Completed';
        } elseif ($tasks->some(fn($t) => in_array($t->status, ['In Progress', 'Waiting Review']) || $t->progress > 0)) {
            $newStatus = 'On Progress';
        } else {
            $newStatus = $this->status === 'Completed' ? 'Planning' : $this->status;
        }

        if ($this->status !== $newStatus) {
            $this->updateQuietly(['status' => $newStatus]);
        }
    }
}