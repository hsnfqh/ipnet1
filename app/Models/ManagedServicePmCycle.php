<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ManagedServicePmCycle extends Model
{
    use HasFactory;

    protected $table = 'managed_service_pm_cycles';

    protected $fillable = [
        'project_id',
        'cycle_number',
        'cycle_label',
        'scheduled_date',
        'actual_date',
        'activity_status',
        'activity_notes',
        'assigned_engineer_id',
        'report_status',
        'report_file',
        'report_filename',
        'billing_ba_status',
        'billing_ba_file',
        'billing_ba_filename',
    ];

    protected $casts = [
        'cycle_number'   => 'integer',
        'scheduled_date' => 'date',
        'actual_date'    => 'date',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function assignedEngineer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_engineer_id');
    }
}
