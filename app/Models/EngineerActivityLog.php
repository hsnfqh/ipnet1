<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EngineerActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'project_id',
        'category',
        'activity_type',
        'description',
        'location',
        'activity_date',
        'start_time',
        'end_time',
        'status',
        'notes',
    ];

    protected $casts = [
        'activity_date' => 'date',
    ];

    /**
     * Pastikan kolom category tersedia di tabel engineer_activity_logs
     */
    public static function ensureCategoryColumnExists(): void
    {
        try {
            if (!\Illuminate\Support\Facades\Schema::hasTable('engineer_activity_logs')) {
                return;
            }

            if (!\Illuminate\Support\Facades\Schema::hasColumn('engineer_activity_logs', 'category')) {
                \Illuminate\Support\Facades\Schema::table('engineer_activity_logs', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->string('category', 50)->default('project')->after('project_id')->index();
                });

                self::syncExistingCategories();
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('ensureCategoryColumnExists: ' . $e->getMessage());
        }
    }

    /**
     * Sinkronisasi kategori aktivitas lama berdasarkan relasi & metadata
     */
    public static function syncExistingCategories(): void
    {
        try {
            // 1. Dari ActivityDocumentSignature jika ada report_data
            if (\Illuminate\Support\Facades\Schema::hasTable('activity_document_signatures')) {
                $signatures = \App\Models\ActivityDocumentSignature::whereNotNull('report_data')->get();
                foreach ($signatures as $sig) {
                    $cat = $sig->report_data['category'] ?? ($sig->report_data['report_type'] ?? null);
                    if (!$cat) {
                        if (!empty($sig->report_data['ms_identitas'])) $cat = 'managed_service';
                        elseif (!empty($sig->report_data['hd_identitas'])) $cat = 'help_desk';
                    }
                    if ($cat && in_array($cat, ['project', 'managed_service', 'help_desk'])) {
                        if ($sig->project_id) {
                            self::where('project_id', $sig->project_id)->update(['category' => $cat]);
                        }
                    }
                }
            }

            // 2. Dari Project handover_target
            if (\Illuminate\Support\Facades\Schema::hasColumn('projects', 'handover_target')) {
                $msProjectIds = \App\Models\Project::where('handover_target', 'managed_service')->pluck('id');
                if ($msProjectIds->isNotEmpty()) {
                    self::whereIn('project_id', $msProjectIds)->where('category', 'project')->update(['category' => 'managed_service']);
                }
            }

            // 3. Dari tipe aktivitas
            self::where('activity_type', 'Maintenance')->where('category', 'project')->update(['category' => 'managed_service']);
            self::where('activity_type', 'Field Support')->where('category', 'project')->update(['category' => 'help_desk']);
        } catch (\Throwable $e) {}
    }

    public function engineer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function getDurationAttribute(): ?string
    {
        if (!$this->start_time || !$this->end_time) return null;
        $start = \Carbon\Carbon::parse($this->start_time);
        $end   = \Carbon\Carbon::parse($this->end_time);
        $diff  = $start->diff($end);
        return $diff->h . 'j ' . $diff->i . 'm';
    }
}
