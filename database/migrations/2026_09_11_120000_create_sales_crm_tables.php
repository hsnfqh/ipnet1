<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tabel Sales Activities (CRM Log)
        if (!Schema::hasTable('sales_activities')) {
            Schema::create('sales_activities', function (Blueprint $table) {
                $table->id();
                $table->foreignId('project_id')->nullable()->constrained('projects')->cascadeOnDelete();
                $table->foreignId('sales_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('activity_type')->default('Meeting'); // Meeting, Phone Call, Email, Demo / Presentation, Quotation Submission, Negotiation, Follow Up
                $table->string('subject');
                $table->dateTime('activity_date');
                $table->text('notes')->nullable();
                $table->string('next_action')->nullable();
                $table->dateTime('next_action_date')->nullable();
                $table->string('status')->default('Completed'); // Planned, Completed, Rescheduled
                $table->timestamps();
            });
        }

        // 2. Extend Projects table for full Sales / CRM Lifecycle & Commercial Handover
        Schema::table('projects', function (Blueprint $table) {
            if (!Schema::hasColumn('projects', 'sales_stage')) {
                $table->string('sales_stage')->default('Qualification')->after('status')->comment('Qualification, Qualified Opportunity, Proposal Request, Quotation, Negotiation, Approval, Contract/PO/SPK, Closed Won, Closed Lost');
            }
            if (!Schema::hasColumn('projects', 'win_probability')) {
                $table->integer('win_probability')->default(10)->after('sales_stage');
            }
            if (!Schema::hasColumn('projects', 'expected_closing_date')) {
                $table->date('expected_closing_date')->nullable()->after('win_probability');
            }
            if (!Schema::hasColumn('projects', 'quotation_number')) {
                $table->string('quotation_number')->nullable()->after('expected_closing_date');
            }
            if (!Schema::hasColumn('projects', 'quotation_amount')) {
                $table->decimal('quotation_amount', 15, 2)->nullable()->after('quotation_number');
            }
            if (!Schema::hasColumn('projects', 'quotation_file')) {
                $table->string('quotation_file')->nullable()->after('quotation_amount');
            }
            if (!Schema::hasColumn('projects', 'po_spk_number')) {
                $table->string('po_spk_number')->nullable()->after('quotation_file');
            }
            if (!Schema::hasColumn('projects', 'po_spk_date')) {
                $table->date('po_spk_date')->nullable()->after('po_spk_number');
            }
            if (!Schema::hasColumn('projects', 'po_spk_file')) {
                $table->string('po_spk_file')->nullable()->after('po_spk_date');
            }
            if (!Schema::hasColumn('projects', 'billing_terms')) {
                $table->text('billing_terms')->nullable()->after('po_spk_file');
            }
            if (!Schema::hasColumn('projects', 'commercial_terms')) {
                $table->text('commercial_terms')->nullable()->after('billing_terms');
            }
            if (!Schema::hasColumn('projects', 'sla_commitment')) {
                $table->text('sla_commitment')->nullable()->after('commercial_terms');
            }
            if (!Schema::hasColumn('projects', 'special_commitment')) {
                $table->text('special_commitment')->nullable()->after('sla_commitment');
            }
            if (!Schema::hasColumn('projects', 'exclusions')) {
                $table->text('exclusions')->nullable()->after('special_commitment');
            }
            if (!Schema::hasColumn('projects', 'commercial_handover_status')) {
                $table->string('commercial_handover_status')->default('Draft')->after('exclusions')->comment('Draft, Submitted, Accepted');
            }
            if (!Schema::hasColumn('projects', 'commercial_handover_at')) {
                $table->dateTime('commercial_handover_at')->nullable()->after('commercial_handover_status');
            }
            if (!Schema::hasColumn('projects', 'commercial_handover_by')) {
                $table->foreignId('commercial_handover_by')->nullable()->after('commercial_handover_at')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('projects', 'lost_reason')) {
                $table->string('lost_reason')->nullable()->after('commercial_handover_by');
            }
            if (!Schema::hasColumn('projects', 'lost_competitor')) {
                $table->string('lost_competitor')->nullable()->after('lost_reason');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_activities');

        Schema::table('projects', function (Blueprint $table) {
            $cols = [
                'lost_competitor',
                'lost_reason',
                'commercial_handover_by',
                'commercial_handover_at',
                'commercial_handover_status',
                'exclusions',
                'special_commitment',
                'sla_commitment',
                'commercial_terms',
                'billing_terms',
                'po_spk_file',
                'po_spk_date',
                'po_spk_number',
                'quotation_file',
                'quotation_amount',
                'quotation_number',
                'expected_closing_date',
                'win_probability',
                'sales_stage',
            ];

            foreach ($cols as $col) {
                if (Schema::hasColumn('projects', $col)) {
                    if ($col === 'commercial_handover_by') {
                        $table->dropForeign(['commercial_handover_by']);
                    }
                    $table->dropColumn($col);
                }
            }
        });
    }
};
