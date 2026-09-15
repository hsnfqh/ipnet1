<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminProjectHandover extends Model
{
    use HasFactory;

    protected $table = 'admin_project_handovers';

    protected $fillable = [
        'handover_number',
        'project_id',
        'target_division',
        'handover_date',
        'status',
        'completeness_score',
        'auditor_id',
        'audited_at',
        'audit_notes',
        'archive_box_code',
    ];

    protected $casts = [
        'handover_date' => 'date',
        'audited_at'    => 'datetime',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function auditor()
    {
        return $this->belongsTo(User::class, 'auditor_id');
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'Verified & Accepted' => [
                'bg'     => 'bg-emerald-50',
                'text'   => 'text-emerald-700',
                'border' => 'border-emerald-200',
                'label'  => 'Lengkap & Diterima',
            ],
            'Audit Review' => [
                'bg'     => 'bg-amber-50',
                'text'   => 'text-amber-700',
                'border' => 'border-amber-200',
                'label'  => 'Sedang Diaudit Admin',
            ],
            'Returned for Incomplete' => [
                'bg'     => 'bg-rose-50',
                'text'   => 'text-rose-700',
                'border' => 'border-rose-200',
                'label'  => 'Berkas Belum Lengkap',
            ],
            default => [
                'bg'     => 'bg-gray-100',
                'text'   => 'text-gray-700',
                'border' => 'border-gray-200',
                'label'  => $this->status ?: 'In Assembly',
            ],
        };
    }
}
