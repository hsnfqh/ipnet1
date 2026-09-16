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

    public function getSlaTierInfoAttribute(): ?array
    {
        if ($this->project && $this->project->sla_tier_info) {
            return $this->project->sla_tier_info;
        }

        if ($this->asset && $this->asset->sla_tier_info) {
            return $this->asset->sla_tier_info;
        }

        if ($this->client_name) {
            $matched = Project::where(function($q) {
                $q->where('client', 'like', "%{$this->client_name}%")
                  ->orWhere('name', 'like', "%{$this->client_name}%");
            })->whereNotNull('sla_tier')->first();

            if ($matched && $matched->sla_tier_info) {
                return $matched->sla_tier_info;
            }
        }

        return Project::getSlaTierDetails('Gold');
    }
}
