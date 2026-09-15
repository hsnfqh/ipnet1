<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminDocumentChecklist extends Model
{
    use HasFactory;

    protected $table = 'admin_document_checklists';

    protected $fillable = [
        'project_id',
        'milestone',
        'document_name',
        'is_mandatory',
        'is_submitted',
        'admin_document_id',
        'is_verified',
        'verified_by',
        'verified_at',
        'notes',
    ];

    protected $casts = [
        'is_mandatory' => 'boolean',
        'is_submitted' => 'boolean',
        'is_verified'  => 'boolean',
        'verified_at'  => 'datetime',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function document()
    {
        return $this->belongsTo(AdminDocument::class, 'admin_document_id');
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
