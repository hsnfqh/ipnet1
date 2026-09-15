<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CroAccountHealth extends Model
{
    use HasFactory;

    protected $table = 'cro_account_health';

    protected $fillable = [
        'client_id',
        'client_name',
        'health_status',
        'health_score',
        'contract_end_date',
        'estimated_annual_value',
        'renewal_probability',
        'risk_factors',
        'retention_strategy',
        'last_review_date',
        'next_touchpoint_date',
        'account_manager_id',
        'updated_by',
    ];

    protected $casts = [
        'contract_end_date' => 'date',
        'last_review_date' => 'date',
        'next_touchpoint_date' => 'date',
        'estimated_annual_value' => 'decimal:2',
        'health_score' => 'integer',
        'renewal_probability' => 'integer',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function accountManager()
    {
        return $this->belongsTo(User::class, 'account_manager_id');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function getHealthBadgeAttribute()
    {
        return match($this->health_status) {
            'Healthy' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-200', 'label' => 'Healthy'],
            'Warning' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'border' => 'border-amber-200', 'label' => 'Warning'],
            'At-Risk' => ['bg' => 'bg-rose-50', 'text' => 'text-rose-700', 'border' => 'border-rose-200', 'label' => 'At-Risk'],
            default   => ['bg' => 'bg-gray-50', 'text' => 'text-gray-700', 'border' => 'border-gray-200', 'label' => $this->health_status ?? '-'],
        };
    }
}
