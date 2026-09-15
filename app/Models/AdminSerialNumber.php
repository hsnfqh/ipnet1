<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminSerialNumber extends Model
{
    use HasFactory;

    protected $table = 'admin_serial_numbers';

    protected $fillable = [
        'serial_number',
        'product_name',
        'brand',
        'model',
        'category',
        'current_status',
        'current_location',
        'project_id',
        'client_id',
        'client_name',
        'dispatch_id',
        'warranty_expiry',
        'notes',
    ];

    protected $casts = [
        'warranty_expiry' => 'date',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function dispatch()
    {
        return $this->belongsTo(AdminLogisticsDispatch::class, 'dispatch_id');
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->current_status) {
            'In Warehouse' => [
                'bg'     => 'bg-emerald-50',
                'text'   => 'text-emerald-700',
                'border' => 'border-emerald-200',
                'label'  => 'Di Gudang HQ',
            ],
            'Allocated to Project' => [
                'bg'     => 'bg-indigo-50',
                'text'   => 'text-indigo-700',
                'border' => 'border-indigo-200',
                'label'  => 'Alokasi Proyek',
            ],
            'Dispatched / In Transit' => [
                'bg'     => 'bg-blue-50',
                'text'   => 'text-blue-700',
                'border' => 'border-blue-200',
                'label'  => 'Dalam Pengiriman',
            ],
            'Installed at Site' => [
                'bg'     => 'bg-teal-50',
                'text'   => 'text-teal-700',
                'border' => 'border-teal-200',
                'label'  => 'Terpasang di Site',
            ],
            'RMA / Maintenance' => [
                'bg'     => 'bg-amber-50',
                'text'   => 'text-amber-700',
                'border' => 'border-amber-200',
                'label'  => 'RMA / Servis',
            ],
            'Decommissioned' => [
                'bg'     => 'bg-gray-100',
                'text'   => 'text-gray-600',
                'border' => 'border-gray-200',
                'label'  => 'Afkir / Pensiun',
            ],
            default => [
                'bg'     => 'bg-gray-50',
                'text'   => 'text-gray-700',
                'border' => 'border-gray-200',
                'label'  => $this->current_status ?: 'Unknown',
            ],
        };
    }
}
