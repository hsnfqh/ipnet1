<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManagedServiceAsset extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'client_name',
        'device_name',
        'category',
        'brand',
        'model',
        'serial_number',
        'ip_address',
        'location_site',
        'rack_position',
        'status',
        'warranty_expiry',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'warranty_expiry' => 'date',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function tickets()
    {
        return $this->hasMany(ManagedServiceTicket::class, 'asset_id');
    }

    public function getSlaTierInfoAttribute(): ?array
    {
        if ($this->project && $this->project->sla_tier_info) {
            return $this->project->sla_tier_info;
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
