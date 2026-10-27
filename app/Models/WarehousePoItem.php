<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehousePoItem extends Model
{
    use HasFactory;

    protected $table = 'warehouse_po_items';

    protected $fillable = [
        'purchase_order_id',
        'product_id',
        'product_name',
        'quantity',
        'unit',
        'unit_price',
        'subtotal',
        'received_qty',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'received_qty' => 'integer',
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(WarehousePurchaseOrder::class, 'purchase_order_id');
    }

    public function product()
    {
        return $this->belongsTo(WarehouseProduct::class, 'product_id');
    }
}
