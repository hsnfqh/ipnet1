<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EngineerActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'project_id',
        'activity_type',
        'description',
        'location',
        'activity_date',
        'start_time',
        'end_time',
        'status',
        'notes',
    ];

    protected $casts = [
        'activity_date' => 'date',
    ];

    public function engineer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function getDurationAttribute(): ?string
    {
        if (!$this->start_time || !$this->end_time) return null;
        $start = \Carbon\Carbon::parse($this->start_time);
        $end   = \Carbon\Carbon::parse($this->end_time);
        $diff  = $start->diff($end);
        return $diff->h . 'j ' . $diff->i . 'm';
    }
}
