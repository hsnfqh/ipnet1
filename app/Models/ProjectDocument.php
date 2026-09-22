<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectDocument extends Model
{
    use HasFactory;

    protected $table = 'project_documents';

    protected $fillable = [
        'project_id',
        'stage_number',
        'stage_name',
        'document_key',
        'document_title',
        'is_mandatory',
        'file_path',
        'file_name',
        'file_size',
        'file_extension',
        'status',
        'notes',
        'uploaded_by',
        'uploaded_at',
        'verified_by',
        'verified_at',
    ];

    protected $casts = [
        'stage_number' => 'integer',
        'is_mandatory' => 'boolean',
        'file_size'    => 'integer',
        'uploaded_at'  => 'datetime',
        'verified_at'  => 'datetime',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function getFormattedFileSizeAttribute(): string
    {
        if (!$this->file_size) return '—';
        $bytes = $this->file_size;
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 1) . ' MB';
        }
        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 0) . ' KB';
        }
        return $bytes . ' B';
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'Verified' => ['bg' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'label' => 'Verified'],
            'Uploaded' => ['bg' => 'bg-blue-50 text-blue-700 border-blue-200', 'label' => 'Uploaded'],
            'Rejected' => ['bg' => 'bg-red-50 text-red-700 border-red-200', 'label' => 'Perlu Revisi'],
            default    => ['bg' => 'bg-slate-100 text-slate-600 border-slate-200', 'label' => 'Pending / Kosong'],
        };
    }
}
