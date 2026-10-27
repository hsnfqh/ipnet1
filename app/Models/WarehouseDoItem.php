<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseDoItem extends Model
{
    use HasFactory;

    protected $table = 'warehouse_do_items';

    protected $fillable = [
        'delivery_order_id',
        'product_id',
        'product_name',
        'serial_number',
        'quantity',
        'unit',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    public function deliveryOrder()
    {
        return $this->belongsTo(WarehouseDeliveryOrder::class, 'delivery_order_id');
    }

    public function product()
    {
        return $this->belongsTo(WarehouseProduct::class, 'product_id');
    }
}
