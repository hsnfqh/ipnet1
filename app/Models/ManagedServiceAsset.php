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
}
