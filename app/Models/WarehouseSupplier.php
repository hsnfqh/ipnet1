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
        'contacts',
        'address',
        'products_supplied',
        'brands',
        'status',
        'notes',
    ];

    protected $casts = [
        'contacts' => 'array',
        'brands'   => 'array',
    ];

    protected $appends = ['contact_list', 'brand_list'];

    public function getContactListAttribute(): array
    {
        if (!empty($this->contacts) && is_array($this->contacts)) {
            return $this->contacts;
        }
        if ($this->contact_person || $this->phone || $this->email) {
            return [
                [
                    'name'  => $this->contact_person ?: '',
                    'phone' => $this->phone ?: '',
                    'email' => $this->email ?: '',
                ]
            ];
        }
        return [
            [
                'name'  => '',
                'phone' => '',
                'email' => '',
            ]
        ];
    }

    public function getBrandListAttribute(): array
    {
        if (!empty($this->brands) && is_array($this->brands)) {
            return $this->brands;
        }
        if (!empty($this->products_supplied)) {
            return array_values(array_filter(array_map('trim', explode(',', $this->products_supplied))));
        }
        return [];
    }

    public function products()
    {
        return $this->hasMany(WarehouseProduct::class, 'supplier_id');
    }

    public function purchaseOrders()
    {
        return $this->hasMany(WarehousePurchaseOrder::class, 'supplier_id');
    }
}
