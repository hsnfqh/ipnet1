<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminDocument extends Model
{
    use HasFactory;

    protected $table = 'admin_documents';

    protected $fillable = [
        'doc_number',
        'title',
        'doc_type',
        'category',
        'project_id',
        'client_id',
        'vendor_id',
        'client_name',
        'vendor_name',
        'version',
        'status',
        'verification_status',
        'rejection_notes',
        'verified_by',
        'verified_at',
        'file_path',
        'effective_date',
        'expiry_date',
        'value',
        'physical_archive_location',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'effective_date' => 'date',
        'expiry_date'    => 'date',
        'verified_at'    => 'datetime',
        'value'          => 'decimal:2',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'Verified / Complete' => [
                'bg'     => 'bg-emerald-50',
                'text'   => 'text-emerald-700',
                'border' => 'border-emerald-200',
                'label'  => 'Verified / Valid',
            ],
            'Clarification Requested' => [
                'bg'     => 'bg-rose-50',
                'text'   => 'text-rose-700',
                'border' => 'border-rose-200',
                'label'  => 'Dikembalikan (Revisi)',
            ],
            'Under Review' => [
                'bg'     => 'bg-amber-50',
                'text'   => 'text-amber-700',
                'border' => 'border-amber-200',
                'label'  => 'Menunggu Review',
            ],
            'Archived' => [
                'bg'     => 'bg-gray-100',
                'text'   => 'text-gray-700',
                'border' => 'border-gray-200',
                'label'  => 'Tersimpan (Arsip)',
            ],
            default => [
                'bg'     => 'bg-gray-50',
                'text'   => 'text-gray-600',
                'border' => 'border-gray-200',
                'label'  => $this->status ?: 'Draft',
            ],
        };
    }

    public function getTypeBadgeAttribute(): array
    {
        return match ($this->doc_type) {
            'SPK' => [
                'bg'   => 'bg-blue-50 text-blue-700 border-blue-200',
                'name' => 'SPK Klien',
            ],
            'PO Client' => [
                'bg'   => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                'name' => 'PO Klien',
            ],
            'PO Vendor' => [
                'bg'   => 'bg-purple-50 text-purple-700 border-purple-200',
                'name' => 'PO Vendor',
            ],
            'Contract' => [
                'bg'   => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                'name' => 'Kontrak Induk',
            ],
            'Tender', 'Proposal' => [
                'bg'   => 'bg-amber-50 text-amber-700 border-amber-200',
                'name' => $this->doc_type,
            ],
            'BAST' => [
                'bg'   => 'bg-teal-50 text-teal-700 border-teal-200',
                'name' => 'BAST Final',
            ],
            default => [
                'bg'   => 'bg-gray-50 text-gray-700 border-gray-200',
                'name' => $this->doc_type ?: 'Dokumen',
            ],
        };
    }
}
