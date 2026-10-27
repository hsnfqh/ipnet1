<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehousePurchaseOrder extends Model
{
    use HasFactory;

    protected $table = 'warehouse_purchase_orders';

    protected $fillable = [
        'po_number',
        'po_date',
        'supplier_id',
        'supplier_name',
        'warehouse_id',
        'warehouse_name',
        'expected_date',
        'total_amount',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'po_date' => 'date',
        'expected_date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    public function supplier()
    {
        return $this->belongsTo(WarehouseSupplier::class, 'supplier_id');
    }

    public function warehouseLocation()
    {
        return $this->belongsTo(WarehouseLocation::class, 'warehouse_id');
    }

    public function items()
    {
        return $this->hasMany(WarehousePoItem::class, 'purchase_order_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
