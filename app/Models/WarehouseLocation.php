<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseLocation extends Model
{
    use HasFactory;

    protected $table = 'warehouse_locations';

    protected $fillable = [
        'code',
        'name',
        'pic_name',
        'pic_phone',
        'address',
        'capacity_desc',
        'status',
        'notes',
    ];

    public function products()
    {
        return $this->hasMany(WarehouseProduct::class, 'warehouse_id');
    }

    public function purchaseOrders()
    {
        return $this->hasMany(WarehousePurchaseOrder::class, 'warehouse_id');
    }

    public function deliveryOrders()
    {
        return $this->hasMany(WarehouseDeliveryOrder::class, 'warehouse_id');
    }

    public function stockOpnames()
    {
        return $this->hasMany(WarehouseStockOpname::class, 'warehouse_id');
    }
}
