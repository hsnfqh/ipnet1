<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseStockOpname extends Model
{
    use HasFactory;

    protected $table = 'warehouse_stock_opnames';

    protected $fillable = [
        'so_number',
        'so_date',
        'warehouse_id',
        'warehouse_name',
        'auditor_name',
        'status',
        'notes',
        'total_items_checked',
        'total_discrepancy',
        'created_by',
    ];

    protected $casts = [
        'so_date' => 'date',
        'total_items_checked' => 'integer',
        'total_discrepancy' => 'integer',
    ];

    public function warehouseLocation()
    {
        return $this->belongsTo(WarehouseLocation::class, 'warehouse_id');
    }

    public function items()
    {
        return $this->hasMany(WarehouseSoItem::class, 'stock_opname_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
