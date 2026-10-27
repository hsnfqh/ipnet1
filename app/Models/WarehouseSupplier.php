<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseSupplier extends Model
{
    use HasFactory;

    protected $table = 'warehouse_suppliers';

    protected $fillable = [
        'code',
        'name',
        'contact_person',
        'phone',
        'email',
        'address',
        'products_supplied',
        'status',
        'notes',
    ];

    public function products()
    {
        return $this->hasMany(WarehouseProduct::class, 'supplier_id');
    }

    public function purchaseOrders()
    {
        return $this->hasMany(WarehousePurchaseOrder::class, 'supplier_id');
    }
}
