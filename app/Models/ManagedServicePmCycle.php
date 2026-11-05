<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ManagedServicePmCycle extends Model
{
    use HasFactory;

    protected $table = 'managed_service_pm_cycles';

    protected $fillable = [
        'project_id',
        'cycle_number',
        'cycle_label',
        'scheduled_date',
        'actual_date',
        'activity_status',
        'activity_notes',
        'assigned_engineer_id',
        'assigned_engineers',
        'report_status',
        'report_file',
        'report_filename',
        'report_files',
        'billing_ba_status',
        'billing_ba_file',
        'billing_ba_filename',
    ];

    protected $casts = [
        'cycle_number'       => 'integer',
        'scheduled_date'     => 'date',
        'actual_date'        => 'date',
        'assigned_engineers' => 'array',
        'report_files'       => 'array',
    ];

    protected $appends = [
        'all_report_files',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function assignedEngineer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_engineer_id');
    }

    /**
     * Dapatkan daftar seluruh file laporan yang valid
     */
    public function getAllReportFilesAttribute(): array
    {
        $files = is_array($this->report_files) ? $this->report_files : [];
        if (empty($files) && !empty($this->report_file)) {
            $files[] = [
                'path'        => $this->report_file,
                'filename'    => $this->report_filename ?: basename($this->report_file),
                'uploaded_at' => $this->updated_at ? $this->updated_at->format('d/m/Y H:i') : null,
            ];
        }
        return $files;
    }
}
