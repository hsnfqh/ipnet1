<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManagedServiceReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'client_name',
        'title',
        'report_type',
        'period_month',
        'period_year',
        'sla_score',
        'summary',
        'status',
        'file_path',
        'created_by',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
