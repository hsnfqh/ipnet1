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
        'owner_type',
        'device_name',
        'product_description',
        'category',
        'brand',
        'model',
        'os_version',
        'quantity',
        'serial_number',
        'ip_address',
        'location_site',
        'rack_position',
        'status',
        'warranty_expiry',
        'eol_date',
        'eos_date',
        'support_status',
        'support_expiry_date',
        'service_support_type',
        'backup_status',
        'last_backup_date',
        'backup_notes',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'warranty_expiry'     => 'date',
        'eol_date'            => 'date',
        'eos_date'            => 'date',
        'support_expiry_date' => 'date',
        'last_backup_date'    => 'date',
    ];

    public function getIsEolAttribute(): bool
    {
        return $this->eol_date && $this->eol_date->isPast();
    }

    public function getIsEosAttribute(): bool
    {
        return $this->eos_date && $this->eos_date->isPast();
    }

    public function getEffectiveSupportStatusAttribute(): string
    {
        if ($this->support_expiry_date && $this->support_expiry_date->isPast()) {
            return 'Expired';
        }
        return $this->support_status ?: 'Active';
    }

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
