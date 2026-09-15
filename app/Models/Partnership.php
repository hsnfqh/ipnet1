<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Partnership extends Model
{
    use HasFactory;

    protected $table = 'partnerships';

    protected $fillable = [
        'partner_name',
        'partner_type',
        'tier_level',
        'pic_name',
        'pic_contact',
        'pic_email',
        'collaboration_scope',
        'status',
        'created_by',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
