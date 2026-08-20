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
        'location',
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

    // Helpers
    public function isOverdue()
    {
        return $this->status !== 'Completed' && $this->deadline < now();
    }
}