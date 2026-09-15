<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManagedServiceTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_number',
        'project_id',
        'client_name',
        'title',
        'description',
        'type',
        'priority',
        'status',
        'reported_by',
        'contact_phone',
        'assigned_to',
        'asset_id',
        'sla_deadline',
        'resolved_at',
        'sla_met',
        'resolution_notes',
        'root_cause',
        'created_by',
    ];

    protected $casts = [
        'sla_deadline' => 'datetime',
        'resolved_at'  => 'datetime',
        'sla_met'      => 'boolean',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function assignedEngineer()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function asset()
    {
        return $this->belongsTo(ManagedServiceAsset::class, 'asset_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
