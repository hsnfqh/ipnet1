<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityDocumentSignature extends Model
{
    use HasFactory;

    protected $table = 'activity_document_signatures';

    protected $fillable = [
        'document_number',
        'scope_key',
        'project_id',
        'project_name',
        'log_ids',
        'pic_user_id',
        'pic_name',
        'pic_title',
        'pic_signature',
        'pic_signed_at',
        'lead_user_id',
        'lead_name',
        'lead_title',
        'lead_signature',
        'lead_signed_at',
        'head_user_id',
        'head_name',
        'head_title',
        'head_signature',
        'head_signed_at',
        'status',
        'verification_hash',
        'notes',
        'report_data',
    ];

    protected $casts = [
        'log_ids'       => 'array',
        'report_data'   => 'array',
        'pic_signed_at' => 'datetime',
        'lead_signed_at'=> 'datetime',
        'head_signed_at'=> 'datetime',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function picUser()
    {
        return $this->belongsTo(User::class, 'pic_user_id');
    }

    public function leadUser()
    {
        return $this->belongsTo(User::class, 'lead_user_id');
    }

    public function headUser()
    {
        return $this->belongsTo(User::class, 'head_user_id');
    }

    /**
     * Pastikan tabel dan kolom report_data selalu siap di server MySQL
     */
    public static function ensureSchemaReady(): void
    {
        try {
            if (!\Illuminate\Support\Facades\Schema::hasTable('activity_document_signatures')) {
                \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
            }

            if (\Illuminate\Support\Facades\Schema::hasTable('activity_document_signatures')) {
                if (!\Illuminate\Support\Facades\Schema::hasColumn('activity_document_signatures', 'report_data')) {
                    try {
                        \Illuminate\Support\Facades\Schema::table('activity_document_signatures', function (\Illuminate\Database\Schema\Blueprint $table) {
                            $table->longText('report_data')->nullable()->after('notes');
                        });
                    } catch (\Throwable $ex) {
                        try {
                            \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
                        } catch (\Throwable $e2) {}
                    }
                }
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('ensureSchemaReady: ' . $e->getMessage());
        }
    }

    /**
     * Generate document registration number e.g. IPNET-ACT-202610-0001
     */
    public static function generateDocumentNumber(): string
    {
        self::ensureSchemaReady();
        try {
            $prefix = 'IPNET-ACT-' . date('Ym') . '-';
            $count  = self::where('document_number', 'like', $prefix . '%')->count() + 1;
            return $prefix . str_pad((string)$count, 4, '0', STR_PAD_LEFT);
        } catch (\Throwable $e) {
            return 'IPNET-ACT-' . date('Ym') . '-0001';
        }
    }
}
