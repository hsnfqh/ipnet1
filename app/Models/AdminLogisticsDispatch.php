<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminLogisticsDispatch extends Model
{
    use HasFactory;

    protected $table = 'admin_logistics_dispatches';

    protected $fillable = [
        'dispatch_number',
        'project_id',
        'client_id',
        'client_name',
        'dispatch_date',
        'courier_type',
        'courier_name',
        'tracking_ref',
        'origin_warehouse',
        'destination_address',
        'recipient_name',
        'recipient_phone',
        'status',
        'delivery_receipt_path',
        'delivery_notes',
        'created_by',
    ];

    protected $casts = [
        'dispatch_date' => 'date',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function items()
    {
        return $this->hasMany(AdminDispatchItem::class, 'dispatch_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'Confirmed / Signed' => [
                'bg'     => 'bg-emerald-50',
                'text'   => 'text-emerald-700',
                'border' => 'border-emerald-200',
                'label'  => 'Selesai & Tertandatangani',
            ],
            'Delivered' => [
                'bg'     => 'bg-teal-50',
                'text'   => 'text-teal-700',
                'border' => 'border-teal-200',
                'label'  => 'Terkirim di Site',
            ],
            'In Transit' => [
                'bg'     => 'bg-blue-50',
                'text'   => 'text-blue-700',
                'border' => 'border-blue-200',
                'label'  => 'Dalam Pengiriman',
            ],
            'Ready to Dispatch' => [
                'bg'     => 'bg-amber-50',
                'text'   => 'text-amber-700',
                'border' => 'border-amber-200',
                'label'  => 'Siap Kirim',
            ],
            default => [
                'bg'     => 'bg-gray-100',
                'text'   => 'text-gray-700',
                'border' => 'border-gray-200',
                'label'  => $this->status ?: 'Draft',
            ],
        };
    }
}
