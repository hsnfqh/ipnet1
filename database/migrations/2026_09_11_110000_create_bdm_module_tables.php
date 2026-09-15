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
        // 1. Table Market Intelligence
        if (!Schema::hasTable('market_intelligences')) {
            Schema::create('market_intelligences', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('category')->default('Market Trend'); // Market Trend, Competitor Movement, Marketing Campaign, Regulatory / Policy, Industry Analysis
                $table->string('industry_sector')->default('Government / Kementerian'); // Government / Kementerian, Banking & Financial, BUMN & Enterprise, Healthcare & Hospital, Education / Others
                $table->text('summary');
                $table->decimal('potential_value', 15, 2)->nullable();
                $table->string('source_url')->nullable();
                $table->string('impact_level')->default('Medium'); // High, Medium, Low
                $table->string('status')->default('Active'); // Active, Converted to Opp, Archived
                $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }

        // 2. Table Partnerships & Channel Network
        if (!Schema::hasTable('partnerships')) {
            Schema::create('partnerships', function (Blueprint $table) {
                $table->id();
                $table->string('partner_name');
                $table->string('partner_type')->default('Principal'); // Principal, Distributor, Telco / ISP Provider, Technology Partner
                $table->string('tier_level')->default('Gold Partner'); // Platinum Partner, Gold Partner, Authorized Partner, Strategic Distributor
                $table->string('pic_name')->nullable();
                $table->string('pic_contact')->nullable();
                $table->string('pic_email')->nullable();
                $table->text('collaboration_scope')->nullable();
                $table->string('status')->default('Active'); // Active, In Discussion, Renewed
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }

        // 3. Extend projects table with BDM Opportunity Handover Package fields
        Schema::table('projects', function (Blueprint $table) {
            if (!Schema::hasColumn('projects', 'bdm_id')) {
                $table->foreignId('bdm_id')->nullable()->after('pm_id')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('projects', 'opportunity_source')) {
                $table->string('opportunity_source')->nullable()->after('bdm_id');
            }
            if (!Schema::hasColumn('projects', 'business_need_summary')) {
                $table->text('business_need_summary')->nullable()->after('opportunity_source');
            }
            if (!Schema::hasColumn('projects', 'stakeholders_data')) {
                $table->json('stakeholders_data')->nullable()->after('business_need_summary');
            }
            if (!Schema::hasColumn('projects', 'initial_requirement')) {
                $table->text('initial_requirement')->nullable()->after('stakeholders_data');
            }
            if (!Schema::hasColumn('projects', 'target_timeline_type')) {
                $table->string('target_timeline_type')->nullable()->after('initial_requirement');
            }
            if (!Schema::hasColumn('projects', 'competitor_analysis')) {
                $table->text('competitor_analysis')->nullable()->after('target_timeline_type');
            }
            if (!Schema::hasColumn('projects', 'partner_alignment')) {
                $table->string('partner_alignment')->nullable()->after('competitor_analysis');
            }
            if (!Schema::hasColumn('projects', 'bd_assessment_score')) {
                $table->integer('bd_assessment_score')->nullable()->after('partner_alignment');
            }
            if (!Schema::hasColumn('projects', 'bdm_handover_status')) {
                $table->string('bdm_handover_status')->default('Draft')->after('bd_assessment_score'); // Draft, Handed Over to Sales, Accepted by Sales
            }
            if (!Schema::hasColumn('projects', 'bdm_handover_at')) {
                $table->dateTime('bdm_handover_at')->nullable()->after('bdm_handover_status');
            }
            if (!Schema::hasColumn('projects', 'handover_document_file')) {
                $table->string('handover_document_file')->nullable()->after('bdm_handover_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('market_intelligences');
        Schema::dropIfExists('partnerships');

        Schema::table('projects', function (Blueprint $table) {
            $columns = [
                'handover_document_file',
                'bdm_handover_at',
                'bdm_handover_status',
                'bd_assessment_score',
                'partner_alignment',
                'competitor_analysis',
                'target_timeline_type',
                'initial_requirement',
                'stakeholders_data',
                'business_need_summary',
                'opportunity_source',
                'bdm_id'
            ];
            foreach ($columns as $column) {
                if (Schema::hasColumn('projects', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
