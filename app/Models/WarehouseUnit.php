<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseUnit extends Model
{
    use HasFactory;

    protected $table = 'warehouse_units';

    protected $fillable = [
        'code',
        'name',
        'short_name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function products()
    {
        return $this->hasMany(WarehouseProduct::class, 'unit_id');
    }
}
