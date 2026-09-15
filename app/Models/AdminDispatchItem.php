<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminDispatchItem extends Model
{
    use HasFactory;

    protected $table = 'admin_dispatch_items';

    protected $fillable = [
        'dispatch_id',
        'item_name',
        'category',
        'quantity',
        'unit',
        'serial_numbers',
        'condition',
        'notes',
    ];

    public function dispatch()
    {
        return $this->belongsTo(AdminLogisticsDispatch::class, 'dispatch_id');
    }
}
