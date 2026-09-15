<?php
// app/Models/Project.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'client',
        'sales_name',
        'location',
        'project_type',
        'visit_schedule',
        'description',
        'start_date',
        'deadline',
        'status',
        'stage',
        'process_status',
        'acquire_status',
        'contract_value',
        'po_number',
        'po_file',
        'proposal_file',
        'proposal_notes',
        'mandays',
        'presales_status',
        'handover_status',
        'handover_data',
        'special_notes',
        'handover_conditional_notes',
        'handover_conditional_deadline',
        'handover_submitted_at',
        'handover_approved_at',
        'handover_approved_by',
        'customer_pic_technical',
        'customer_pic_business',
        'customer_pic_finance',
        'bdm_id',
        'opportunity_source',
        'business_need_summary',
        'stakeholders_data',
        'initial_requirement',
        'target_timeline_type',
        'competitor_analysis',
        'partner_alignment',
        'bd_assessment_score',
        'bdm_handover_status',
        'bdm_handover_at',
        'handover_document_file',
        'sales_stage',
        'win_probability',
        'expected_closing_date',
        'quotation_number',
        'quotation_amount',
        'quotation_file',
        'po_spk_number',
        'po_spk_date',
        'po_spk_file',
        'billing_terms',
        'commercial_terms',
        'sla_commitment',
        'special_commitment',
        'exclusions',
        'commercial_handover_status',
        'commercial_handover_at',
        'commercial_handover_by',
        'lost_reason',
        'lost_competitor',
        'created_by',
        'pm_id',
        'division_id',
        'documents_checklist',
    ];

    protected $casts = [
        'start_date'                    => 'date:Y-m-d',
        'deadline'                      => 'date:Y-m-d',
        'expected_closing_date'         => 'date:Y-m-d',
        'po_spk_date'                   => 'date:Y-m-d',
        'handover_conditional_deadline' => 'datetime',
        'handover_submitted_at'         => 'datetime',
        'handover_approved_at'          => 'datetime',
        'bdm_handover_at'               => 'datetime',
        'commercial_handover_at'        => 'datetime',
        'documents_checklist'           => 'array',
        'handover_data'                 => 'array',
        'stakeholders_data'             => 'array',
    ];

    protected $appends = [
        'duration_days',
        'is_recurring',
        'weighted_forecast_value',
    ];

    public function getWeightedForecastValueAttribute()
    {
        $value = (float) ($this->contract_value ?? $this->quotation_amount ?? 0);
        $prob = (int) ($this->win_probability ?? 10);
        return round($value * ($prob / 100), 2);
    }

    // Relationships
    public function salesActivities()
    {
        return $this->hasMany(SalesActivity::class)->orderByDesc('activity_date');
    }

    public function commercialHandoverBy()
    {
        return $this->belongsTo(User::class, 'commercial_handover_by');
    }

    public function bdm()
    {
        return $this->belongsTo(User::class, 'bdm_id');
    }

    public function pm()
    {
        return $this->belongsTo(User::class, 'pm_id');
    }

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    // Scopes
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['Planning', 'On Progress']);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'Completed');
    }

    // Accessors
    public function getProgressAttribute()
    {
        $tasks = $this->tasks;  // gunakan relasi yang sudah di-eager load (tidak ada extra query)
        $totalTasks = $tasks->count();
        if ($totalTasks === 0) {
            return (int) ($this->attributes['progress'] ?? 0);
        }

        // Hitung rata-rata progress riil dari seluruh task di project ini
        $avgProgress = $tasks->avg('progress');
        if ($avgProgress !== null && $avgProgress > 0) {
            return round($avgProgress);
        }

        $completedTasks = $tasks->where('status', 'Completed')->count();
        return round(($completedTasks / $totalTasks) * 100);
    }

    public function getTaskCountAttribute()
    {
        return $this->tasks->count();  // gunakan eager-loaded relation
    }

    public function getCompletedTaskCountAttribute()
    {
        return $this->tasks->where('status', 'Completed')->count();  // gunakan eager-loaded relation
    }

    public function getDurationDaysAttribute()
    {
        if (!$this->start_date || !$this->deadline) return 0;
        return max(1, $this->start_date->diffInDays($this->deadline) + 1);
    }

    public function getDurationFormattedAttribute()
    {
        $days = $this->duration_days;
        if ($days < 30) {
            return $days . ' Hari';
        }
        $months = round($days / 30, 1);
        return $months == round($months) ? intval($months) . ' Bulan' : $months . ' Bulan (' . $days . ' Hari)';
    }

    public function getIsRecurringAttribute(): bool
    {
        return !empty($this->visit_schedule) && $this->visit_schedule !== 'None' && $this->visit_schedule !== '-';
    }

    // Helpers
    public function isOverdue()
    {
        return $this->status !== 'Completed' && $this->deadline < now();
    }

    /**
     * Sinkronisasi status project secara otomatis berdasarkan status & progress tasks terkait
     */
    public function syncStatusWithTasks(): void
    {
        $tasks = $this->tasks()->get();
        if ($tasks->isEmpty()) {
            return;
        }

        if ($tasks->every(fn($t) => $t->status === 'Completed')) {
            $newStatus = 'Completed';
        } elseif ($tasks->some(fn($t) => in_array($t->status, ['In Progress', 'Waiting Review']) || $t->progress > 0)) {
            $newStatus = 'On Progress';
        } else {
            $newStatus = $this->status === 'Completed' ? 'Planning' : $this->status;
        }

        if ($this->status !== $newStatus) {
            $this->updateQuietly(['status' => $newStatus]);
        }
    }
}