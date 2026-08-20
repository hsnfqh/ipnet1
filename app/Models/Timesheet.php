<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Timesheet extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'project_id',
        'task_id',
        'date',
        'start_time',
        'end_time',
        'duration_minutes',
        'category',
        'activity',
        'notes',
    ];

    protected $casts = [
        'date'             => 'date',
        'duration_minutes' => 'integer',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    // Accessors
    public function getDurationHoursAttribute()
    {
        return round($this->duration_minutes / 60, 2);
    }

    public function getFormattedDurationAttribute()
    {
        $hours = floor($this->duration_minutes / 60);
        $minutes = $this->duration_minutes % 60;
        
        if ($hours > 0 && $minutes > 0) {
            return "{$hours}j {$minutes}m";
        } elseif ($hours > 0) {
            return "{$hours} Jam";
        } else {
            return "{$minutes} Menit";
        }
    }

    public function getCategoryColorAttribute()
    {
        return match($this->category) {
            'On-Site'     => ['bg' => '#FDF1F2', 'text' => '#C81E2C', 'border' => '#FADADF'],
            'Remote'      => ['bg' => '#EFF6FF', 'text' => '#1D4ED8', 'border' => '#BFDBFE'],
            'Overtime'    => ['bg' => '#FEF3C7', 'text' => '#B45309', 'border' => '#FDE68A'],
            'Maintenance' => ['bg' => '#ECFDF5', 'text' => '#047857', 'border' => '#A7F3D0'],
            default       => ['bg' => '#F1F0EE', 'text' => '#3D3A44', 'border' => '#E7E5E3'],
        };
    }
}
