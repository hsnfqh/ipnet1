<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminEquipmentAsset extends Model
{
    use HasFactory;

    protected $table = 'admin_equipment_assets';

    protected $fillable = [
        'asset_code',
        'asset_name',
        'category',
        'brand_model',
        'serial_number',
        'condition',
        'status',
        'current_borrower_id',
        'borrowed_at',
        'expected_return_date',
        'storage_location',
        'notes',
    ];

    protected $casts = [
        'borrowed_at'          => 'datetime',
        'expected_return_date' => 'date',
    ];

    public function borrower()
    {
        return $this->belongsTo(User::class, 'current_borrower_id');
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'Available in HQ' => [
                'bg'     => 'bg-emerald-50',
                'text'   => 'text-emerald-700',
                'border' => 'border-emerald-200',
                'label'  => 'Tersedia di HQ',
            ],
            'Borrowed by Engineer' => [
                'bg'     => 'bg-amber-50',
                'text'   => 'text-amber-700',
                'border' => 'border-amber-200',
                'label'  => 'Dipinjam Engineer',
            ],
            'Deployed on Site' => [
                'bg'     => 'bg-blue-50',
                'text'   => 'text-blue-700',
                'border' => 'border-blue-200',
                'label'  => 'Standby di Site',
            ],
            'In Maintenance' => [
                'bg'     => 'bg-rose-50',
                'text'   => 'text-rose-700',
                'border' => 'border-rose-200',
                'label'  => 'Kalibrasi / Servis',
            ],
            default => [
                'bg'     => 'bg-gray-100',
                'text'   => 'text-gray-700',
                'border' => 'border-gray-200',
                'label'  => $this->status ?: 'Tersedia',
            ],
        };
    }
}
