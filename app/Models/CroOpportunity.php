<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CroOpportunity extends Model
{
    use HasFactory;

    protected $table = 'cro_opportunities';

    protected $fillable = [
        'client_id',
        'client_name',
        'opportunity_type',
        'title',
        'estimated_value',
        'requirement_notes',
        'status',
        'handed_over_to',
        'handed_over_at',
        'created_by',
    ];

    protected $casts = [
        'estimated_value' => 'decimal:2',
        'handed_over_at' => 'datetime',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function salesPic()
    {
        return $this->belongsTo(User::class, 'handed_over_to');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'Identified'        => ['bg' => 'bg-purple-50', 'text' => 'text-purple-700', 'border' => 'border-purple-200', 'label' => 'Teridentifikasi'],
            'Handed Over'       => ['bg' => 'bg-indigo-50', 'text' => 'text-indigo-700', 'border' => 'border-indigo-200', 'label' => 'Handover ke Sales'],
            'In Sales Pipeline' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'border' => 'border-blue-200', 'label' => 'Di Pipeline Sales'],
            'Won'               => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-200', 'label' => 'Won / Deal Baru'],
            'Dropped'           => ['bg' => 'bg-gray-50', 'text' => 'text-gray-700', 'border' => 'border-gray-200', 'label' => 'Dropped'],
            default             => ['bg' => 'bg-gray-50', 'text' => 'text-gray-700', 'border' => 'border-gray-200', 'label' => $this->status ?? '-'],
        };
    }
}
