<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseSoItem extends Model
{
    use HasFactory;

    protected $table = 'warehouse_so_items';

    protected $fillable = [
        'stock_opname_id',
        'product_id',
        'item_code',
        'product_name',
        'system_stock',
        'physical_stock',
        'difference',
        'condition_status',
        'notes',
    ];

    protected $casts = [
        'system_stock' => 'integer',
        'physical_stock' => 'integer',
        'difference' => 'integer',
    ];

    public function stockOpname()
    {
        return $this->belongsTo(WarehouseStockOpname::class, 'stock_opname_id');
    }

    public function product()
    {
        return $this->belongsTo(WarehouseProduct::class, 'product_id');
    }
}
