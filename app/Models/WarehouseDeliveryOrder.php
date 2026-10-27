<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseDeliveryOrder extends Model
{
    use HasFactory;

    protected $table = 'warehouse_delivery_orders';

    protected $fillable = [
        'do_number',
        'do_date',
        'reference_no',
        'warehouse_id',
        'warehouse_name',
        'recipient_name',
        'recipient_company',
        'recipient_phone',
        'delivery_address',
        'courier_name',
        'vehicle_no',
        'tracking_no',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'do_date' => 'date',
    ];

    public function warehouseLocation()
    {
        return $this->belongsTo(WarehouseLocation::class, 'warehouse_id');
    }

    public function items()
    {
        return $this->hasMany(WarehouseDoItem::class, 'delivery_order_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
