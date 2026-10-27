<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseProduct extends Model
{
    use HasFactory;

    protected $table = 'warehouse_products';

    protected $fillable = [
        'sku',
        'barcode',
        'name',
        'brand',
        'category_id',
        'category_name',
        'unit_id',
        'unit_name',
        'supplier_id',
        'supplier_name',
        'warehouse_id',
        'warehouse_name',
        'rack_position',
        'min_stock',
        'current_stock',
        'unit_price',
        'specifications',
        'status',
        'image',
    ];

    protected $casts = [
        'min_stock' => 'integer',
        'current_stock' => 'integer',
        'unit_price' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(WarehouseCategory::class, 'category_id');
    }

    public function unit()
    {
        return $this->belongsTo(WarehouseUnit::class, 'unit_id');
    }

    public function supplier()
    {
        return $this->belongsTo(WarehouseSupplier::class, 'supplier_id');
    }

    public function warehouseLocation()
    {
        return $this->belongsTo(WarehouseLocation::class, 'warehouse_id');
    }

    public function poItems()
    {
        return $this->hasMany(WarehousePoItem::class, 'product_id');
    }

    public function doItems()
    {
        return $this->hasMany(WarehouseDoItem::class, 'product_id');
    }

    public function soItems()
    {
        return $this->hasMany(WarehouseSoItem::class, 'product_id');
    }
}
