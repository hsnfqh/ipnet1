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
        'sla_tier',
        'visit_schedule',
        'description',
        'start_date',
        'deadline',
        'status',
        'progress',
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
        'handover_target',
        'service_start_date',
        'service_end_date',
        'maintenance_frequency',
        'sla_coverage_hours',
        'ms_handover_status',
        'ms_accepted_at',
        'ms_accepted_by',
        'lost_reason',
        'lost_competitor',
        'created_by',
        'pm_id',
        'division_id',
        'documents_checklist',
    ];

    protected $casts = [
        'progress'                      => 'integer',
        'start_date'                    => 'date:Y-m-d',
        'deadline'                      => 'date:Y-m-d',
        'expected_closing_date'         => 'date:Y-m-d',
        'po_spk_date'                   => 'date:Y-m-d',
        'service_start_date'            => 'date:Y-m-d',
        'service_end_date'              => 'date:Y-m-d',
        'handover_conditional_deadline' => 'datetime',
        'handover_submitted_at'         => 'datetime',
        'handover_approved_at'          => 'datetime',
        'bdm_handover_at'               => 'datetime',
        'commercial_handover_at'        => 'datetime',
        'ms_accepted_at'                => 'datetime',
        'documents_checklist'           => 'array',
        'handover_data'                 => 'array',
        'stakeholders_data'             => 'array',
    ];

    protected $appends = [
        'duration_days',
        'is_recurring',
        'weighted_forecast_value',
        'sla_tier_info',
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

    public function msAcceptedBy()
    {
        return $this->belongsTo(User::class, 'ms_accepted_by');
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
        if ($this->status === 'Completed') {
            return 100;
        }

        if ($this->status === 'Planning') {
            return (isset($this->attributes['progress']) && $this->attributes['progress'] !== null) 
                ? (int) $this->attributes['progress'] 
                : 0;
        }

        // Jika terdapat manual progress yang ditentukan oleh Lead Engineer
        if (isset($this->attributes['progress']) && $this->attributes['progress'] !== null) {
            return (int) $this->attributes['progress'];
        }

        $tasks = $this->tasks;  // gunakan relasi yang sudah di-eager load (tidak ada extra query)
        $totalTasks = $tasks ? $tasks->count() : 0;
        if ($totalTasks === 0) {
            return (int) ($this->attributes['progress'] ?? 0);
        }

        // Hitung rata-rata progress riil dari seluruh task di project ini jika ada task aktif
        $avgProgress = $tasks->avg('progress');
        if ($avgProgress !== null) {
            return round($avgProgress);
        }

        $completedTasks = $tasks->where('status', 'Completed')->count();
        if ($completedTasks > 0) {
            return round(($completedTasks / $totalTasks) * 100);
        }

        return (int) ($this->attributes['progress'] ?? 0);
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

    public static function getSlaTierDetails(?string $tier): ?array
    {
        if (!$tier) {
            return null;
        }

        $tiers = [
            'Platinum' => [
                'tier'              => 'Platinum',
                'icon'              => '',
                'bg'                => 'bg-purple-50',
                'border'            => 'border-purple-200',
                'text'              => 'text-purple-700',
                'visits_per_year'   => 12,
                'visit_frequency'   => 'Bulanan (12x/Tahun)',
                'response_time_p1'  => '< 1 Jam (24x7)',
                'uptime_target'     => '99.9%',
            ],
            'Gold' => [
                'tier'              => 'Gold',
                'icon'              => '',
                'bg'                => 'bg-amber-50',
                'border'            => 'border-amber-200',
                'text'              => 'text-amber-700',
                'visits_per_year'   => 4,
                'visit_frequency'   => 'Triwulan / Quarterly (4x/Tahun)',
                'response_time_p1'  => '< 2 Jam (24x7 / 8x5)',
                'uptime_target'     => '99.5%',
            ],
            'Silver' => [
                'tier'              => 'Silver',
                'icon'              => '',
                'bg'                => 'bg-slate-100',
                'border'            => 'border-slate-300',
                'text'              => 'text-slate-700',
                'visits_per_year'   => 2,
                'visit_frequency'   => 'Semester / Biannual (2x/Tahun)',
                'response_time_p1'  => '< 4 Jam (8x5)',
                'uptime_target'     => '99.0%',
            ],
            'Bronze' => [
                'tier'              => 'Bronze',
                'icon'              => '',
                'bg'                => 'bg-orange-50',
                'border'            => 'border-orange-200',
                'text'              => 'text-orange-700',
                'visits_per_year'   => 1,
                'visit_frequency'   => 'Tahunan / Annual (1x/Tahun)',
                'response_time_p1'  => '< 8 Jam (8x5)',
                'uptime_target'     => '98.0%',
            ],
        ];

        return $tiers[$tier] ?? null;
    }

    public function getSlaTierInfoAttribute(): ?array
    {
        return static::getSlaTierDetails($this->sla_tier);
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

    public function projectDocuments()
    {
        return $this->hasMany(ProjectDocument::class, 'project_id');
    }

    public function stageDocuments(int $stageNumber)
    {
        return $this->projectDocuments()->where('stage_number', $stageNumber);
    }

    /**
     * Cek otorisasi akses khusus Berkas Sales (Confidential)
     * Hanya dapat diakses oleh:
     * 1. Sales PIC / Pembuat Proyek
     * 2. Pak Santoso (Susanto Djaya) & Pak Hari (Hariyadi)
     * 3. Direktur / Management / Head Divisi / BDM / Super Admin
     */
    public function canAccessSalesDocs($user = null): bool
    {
        $user = $user ?: auth()->user();
        if (!$user) return false;

        // Sales creator / PIC
        if ($this->created_by === $user->id || $this->sales_name === $user->name || ($this->sales_id && $this->sales_id === $user->id)) {
            return true;
        }

        // Role Pimpinan / Management
        if (method_exists($user, 'hasAnyRole') && $user->hasAnyRole(['Director', 'Direktur', 'HD / Direktur', 'Division Head', 'Group Leader', 'Group Leader Commercial & Solution', 'Super Admin', 'Admin', 'BDM', 'BusDev', 'Business Development'])) {
            return true;
        }

        // Otorisasi Pimpinan Eksekutif
        $lowerName = strtolower($user->name ?? '');
        if (str_contains($lowerName, 'santoso') || str_contains($lowerName, 'susanto') || str_contains($lowerName, 'hari') || str_contains($lowerName, 'hary')) {
            return true;
        }

        return false;
    }

    public function clientModel()
    {
        return $this->belongsTo(Client::class, 'client', 'name');
    }

    public function getClientRecordAttribute()
    {
        if (empty($this->client)) {
            return null;
        }

        return Client::where('name', $this->client)
            ->orWhere('department', $this->client)
            ->orWhere('id', $this->client)
            ->first();
    }
}