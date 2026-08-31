<?php
// app/Models/Task.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'project_id',
        'engineer_id',
        'priority',
        'status',
        'deadline',
        'progress',
        'attachments',
        'doc_file',
        'description',
        'created_by',
    ];

    protected $casts = [
        'deadline' => 'date',
        'progress' => 'integer',
        'attachments' => 'integer',
    ];

    // Relationships
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function engineer()
    {
        return $this->belongsTo(User::class, 'engineer_id');
    }

    public function engineers()
    {
        return $this->belongsToMany(User::class, 'task_user')->withTimestamps();
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scopes
    public function scopePriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeAssignedTo($query, $userId)
    {
        return $query->where('engineer_id', $userId);
    }

    public function scopeIncomplete($query)
    {
        return $query->where('status', '!=', 'Completed');
    }

    // Accessors
    public function getIsCompletedAttribute()
    {
        return $this->status === 'Completed';
    }

    public function getIsOverdueAttribute()
    {
        return $this->status !== 'Completed' && $this->deadline < now();
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'Assigned' => 'gray',
            'In Progress' => 'amber',
            'Waiting Review' => 'blue',
            'Completed' => 'green',
            default => 'gray',
        };
    }

    public function getPriorityColorAttribute()
    {
        return match($this->priority) {
            'High' => 'red-600',
            'Medium' => 'amber',
            'Low' => 'ink-500',
            default => 'ink-500',
        };
    }
}