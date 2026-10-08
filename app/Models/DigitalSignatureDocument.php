<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class DigitalSignatureDocument extends Model
{
    use HasFactory;

    protected $table = 'digital_signature_documents';

    protected $fillable = [
        'document_number',
        'title',
        'category',
        'project_id',
        'project_name',
        'file_path',
        'file_name',
        'file_size',
        'description',
        'workflow_type',
        'status',
        'created_by',
        'internal_signers',
        'client_name',
        'client_position',
        'client_company',
        'client_phone',
        'client_email',
        'client_signature',
        'client_signed_at',
        'client_ip',
        'client_signing_token',
        'client_token_expires_at',
        'verification_hash',
        'notes',
    ];

    protected $casts = [
        'internal_signers'        => 'array',
        'client_signed_at'        => 'datetime',
        'client_token_expires_at' => 'datetime',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Generate unique document number for Digital Signature
     * Format: IPNET-DS-YYYYMM-XXXX
     */
    public static function generateDocumentNumber(): string
    {
        $prefix = 'IPNET-DS-' . date('Ym') . '-';
        $lastDoc = self::where('document_number', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->first();

        if ($lastDoc && preg_match('/-(\d{4})$/', $lastDoc->document_number, $m)) {
            $nextSeq = (int) $m[1] + 1;
        } else {
            $nextSeq = 1;
        }

        return $prefix . str_pad($nextSeq, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Generate unique client signing token
     */
    public static function generateClientToken(): string
    {
        return bin2hex(random_bytes(24));
    }

    /**
     * Generate verification hash
     */
    public function generateVerificationHash(): string
    {
        $payload = $this->document_number . '|' . $this->id . '|' . $this->client_name . '|' . ($this->client_signed_at ? $this->client_signed_at->toIso8601String() : time());
        return hash('sha256', $payload);
    }

    /**
     * Check if all internal signers have signed
     */
    public function isAllInternalSigned(): bool
    {
        $signers = $this->internal_signers ?? [];
        if (empty($signers)) {
            return true;
        }

        foreach ($signers as $s) {
            if (($s['status'] ?? 'pending') !== 'signed') {
                return false;
            }
        }

        return true;
    }

    /**
     * Get current pending internal signer (for sequential mode)
     */
    public function getCurrentPendingInternalSigner(): ?array
    {
        $signers = $this->internal_signers ?? [];
        foreach ($signers as $s) {
            if (($s['status'] ?? 'pending') !== 'signed') {
                return $s;
            }
        }
        return null;
    }

    /**
     * Check if a specific user can sign now
     */
    public function canUserSign(int $userId): bool
    {
        if ($this->status === 'completed') {
            return false;
        }

        $signers = $this->internal_signers ?? [];
        $foundIndex = -1;

        foreach ($signers as $idx => $s) {
            if ((int)($s['user_id'] ?? 0) === $userId) {
                $foundIndex = $idx;
                if (($s['status'] ?? 'pending') === 'signed') {
                    return false; // already signed
                }
                break;
            }
        }

        if ($foundIndex === -1) {
            return false;
        }

        // Parallel mode: anyone pending can sign anytime
        if ($this->workflow_type === 'parallel') {
            return true;
        }

        // Sequential mode: all previous signers must be signed
        for ($i = 0; $i < $foundIndex; $i++) {
            if (($signers[$i]['status'] ?? 'pending') !== 'signed') {
                return false;
            }
        }

        return true;
    }

    /**
     * Pastikan tabel digital_signature_documents selalu ada & siap di database remote
     */
    public static function ensureSchemaReady(): void
    {
        try {
            if (!Schema::hasTable('digital_signature_documents')) {
                Schema::create('digital_signature_documents', function (Blueprint $table) {
                    $table->id();
                    $table->string('document_number')->unique();
                    $table->string('title');
                    $table->string('category')->default('BAST');
                    $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();
                    $table->string('project_name')->nullable();
                    $table->string('file_path');
                    $table->string('file_name');
                    $table->bigInteger('file_size')->nullable();
                    $table->text('description')->nullable();
                    $table->string('workflow_type')->default('sequential'); // sequential | parallel
                    $table->string('status')->default('draft'); // draft | internal_in_progress | ready_for_client | completed | declined
                    $table->foreignId('created_by')->constrained('users');
                    $table->longText('internal_signers')->nullable();
                    $table->string('client_name')->nullable();
                    $table->string('client_position')->nullable();
                    $table->string('client_company')->nullable();
                    $table->string('client_phone')->nullable();
                    $table->string('client_email')->nullable();
                    $table->longText('client_signature')->nullable();
                    $table->dateTime('client_signed_at')->nullable();
                    $table->string('client_ip')->nullable();
                    $table->string('client_signing_token', 64)->nullable()->unique();
                    $table->dateTime('client_token_expires_at')->nullable();
                    $table->string('verification_hash', 100)->nullable()->unique();
                    $table->text('notes')->nullable();
                    $table->timestamps();
                });
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('ensureSchemaReady digital_signature_documents error: ' . $e->getMessage());
        }
    }
}
