<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'sales_id',
        'activity_type',
        'subject',
        'activity_date',
        'notes',
        'next_action',
        'next_action_date',
        'status',
    ];

    protected $casts = [
        'activity_date'    => 'datetime',
        'next_action_date' => 'datetime',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function sales()
    {
        return $this->belongsTo(User::class, 'sales_id');
    }
}
