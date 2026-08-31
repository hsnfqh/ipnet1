<?php
// app/Models/Schedule.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Schedule extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'project_id',
        'engineer_id',
        'date',
        'start_time',
        'end_time',
        'location',
        'description',
        'created_by',
    ];

    protected $casts = [
        'date'       => 'date',
        'start_time' => 'string',
        'end_time'   => 'string',
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
        return $this->belongsToMany(User::class, 'schedule_user')->withTimestamps();
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scopes
    public function scopeDate($query, $date)
    {
        return $query->where('date', $date);
    }

    public function scopeEngineer($query, $userId)
    {
        return $query->where('engineer_id', $userId);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('date', '>=', now()->toDateString());
    }

    // Accessors
    public function getDurationAttribute()
    {
        $start = \Carbon\Carbon::parse($this->start_time);
        $end = \Carbon\Carbon::parse($this->end_time);
        return $start->diffInMinutes($end);
    }

    public function getIsTodayAttribute()
    {
        return $this->date->isToday();
    }
}