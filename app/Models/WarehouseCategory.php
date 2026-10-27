<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseCategory extends Model
{
    use HasFactory;

    protected $table = 'warehouse_categories';

    protected $fillable = [
        'code',
        'name',
        'description',
        'icon',
        'status',
    ];

    public function products()
    {
        return $this->hasMany(WarehouseProduct::class, 'category_id');
    }
}
