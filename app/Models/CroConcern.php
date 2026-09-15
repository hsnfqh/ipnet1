<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CroConcern extends Model
{
    use HasFactory;

    protected $table = 'cro_concerns';

    protected $fillable = [
        'ticket_number',
        'client_id',
        'client_name',
        'project_id',
        'title',
        'description',
        'source',
        'severity',
        'assigned_dept',
        'assigned_to',
        'status',
        'sla_due_date',
        'root_cause',
        'action_taken',
        'resolved_at',
        'customer_confirmed_at',
        'customer_confirmation_notes',
        'customer_satisfaction_rating',
        'created_by',
    ];

    protected $casts = [
        'sla_due_date' => 'date',
        'resolved_at' => 'datetime',
        'customer_confirmed_at' => 'datetime',
        'customer_satisfaction_rating' => 'integer',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getSeverityBadgeAttribute()
    {
        return match($this->severity) {
            'P1 - Critical', 'Critical' => ['bg' => 'bg-rose-50', 'text' => 'text-rose-700', 'border' => 'border-rose-200', 'label' => 'P1 - Critical'],
            'P2 - High', 'High'         => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'border' => 'border-amber-200', 'label' => 'P2 - High'],
            'P3 - Medium', 'Medium'     => ['bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'border' => 'border-blue-200', 'label' => 'P3 - Medium'],
            'P4 - Low', 'Low'           => ['bg' => 'bg-gray-50', 'text' => 'text-gray-700', 'border' => 'border-gray-200', 'label' => 'P4 - Low'],
            default                     => ['bg' => 'bg-gray-50', 'text' => 'text-gray-700', 'border' => 'border-gray-200', 'label' => $this->severity ?? '-'],
        };
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'Open'                 => ['bg' => 'bg-rose-50', 'text' => 'text-rose-700', 'border' => 'border-rose-200', 'label' => 'Open'],
            'Dispatched'           => ['bg' => 'bg-indigo-50', 'text' => 'text-indigo-700', 'border' => 'border-indigo-200', 'label' => 'Dispatched'],
            'In Progress'          => ['bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'border' => 'border-blue-200', 'label' => 'In Progress'],
            'Pending Confirmation' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'border' => 'border-amber-200', 'label' => 'Pending Confirm'],
            'Closed'               => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-200', 'label' => 'Closed'],
            default                => ['bg' => 'bg-gray-50', 'text' => 'text-gray-700', 'border' => 'border-gray-200', 'label' => $this->status ?? '-'],
        };
    }
}
