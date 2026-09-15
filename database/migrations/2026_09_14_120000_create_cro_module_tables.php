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
        // 1. Customer Relationship & Engagements / Touchpoints / Meetings
        Schema::create('cro_engagements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->string('client_name');
            $table->string('pic_name')->nullable();
            $table->string('pic_contact')->nullable();
            $table->string('engagement_type')->default('Meeting'); // Meeting, Courtesy Visit, QBR Review, Phone Call, Lunch / Coffee, Site Visit
            $table->string('title');
            $table->text('discussion_summary')->nullable();
            $table->text('action_items')->nullable();
            $table->string('sentiment')->default('Positive'); // Positive, Neutral, Concerned, Negative
            $table->date('engagement_date');
            $table->string('location')->nullable();
            $table->string('mom_file_path')->nullable(); // Minutes of Meeting doc/pdf
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 2. Customer Satisfaction & Feedback / CSAT
        Schema::create('cro_csat_surveys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->string('client_name');
            $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();
            $table->string('service_category')->default('Managed Service'); // Managed Service, Project Delivery, Technical Support, Commercial / Account Mgmt
            $table->string('respondent_name');
            $table->string('respondent_role')->nullable();
            $table->integer('csat_score')->default(5); // 1 to 5
            $table->integer('nps_score')->nullable(); // 0 to 10
            $table->integer('sla_satisfaction_score')->nullable(); // 1 to 5
            $table->integer('support_speed_score')->nullable(); // 1 to 5
            $table->text('feedback_notes')->nullable();
            $table->text('areas_of_improvement')->nullable();
            $table->string('sentiment')->default('Positive'); // Positive, Neutral, Negative
            $table->date('survey_date');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 3. Customer Concern, Complaint & Escalation Orchestration
        Schema::create('cro_concerns', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique(); // e.g. CR-2026-001
            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->string('client_name');
            $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();
            $table->string('title');
            $table->text('description');
            $table->string('source')->default('Client Direct'); // Client Direct, SLA Breach, Incident Escalation, Survey Feedback, QBR Meeting
            $table->string('severity')->default('Medium'); // P1 - Critical, P2 - High, P3 - Medium, P4 - Low
            $table->string('assigned_dept')->default('Managed Service'); // Managed Service, PMO, Technical / Network, Sales / BDM
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete(); // Assigned internal PIC
            $table->string('status')->default('Open'); // Open, Dispatched, In Progress, Pending Confirmation, Closed
            $table->date('sla_due_date')->nullable();
            $table->text('root_cause')->nullable();
            $table->text('action_taken')->nullable();
            $table->dateTime('resolved_at')->nullable();
            $table->dateTime('customer_confirmed_at')->nullable();
            $table->text('customer_confirmation_notes')->nullable();
            $table->integer('customer_satisfaction_rating')->nullable(); // 1 to 5 after confirmation
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 4. Customer Retention & Account Health Monitoring
        Schema::create('cro_account_health', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->string('client_name');
            $table->string('health_status')->default('Healthy'); // Healthy (Green), Warning (Amber), At-Risk (Red)
            $table->integer('health_score')->default(90); // 0 - 100
            $table->date('contract_end_date')->nullable();
            $table->decimal('estimated_annual_value', 18, 2)->default(0);
            $table->integer('renewal_probability')->default(85); // 0 - 100 %
            $table->text('risk_factors')->nullable();
            $table->text('retention_strategy')->nullable();
            $table->date('last_review_date')->nullable();
            $table->date('next_touchpoint_date')->nullable();
            $table->foreignId('account_manager_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 5. Account Development & Expansion Opportunities (Bridge to Sales / BDM)
        Schema::create('cro_opportunities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->string('client_name');
            $table->string('opportunity_type')->default('Renewal'); // Renewal, Upsell Bandwidth, Cross-Sell New Site, Hardware / License Addon, New Solution
            $table->string('title');
            $table->decimal('estimated_value', 18, 2)->default(0);
            $table->text('requirement_notes')->nullable();
            $table->string('status')->default('Identified'); // Identified, Handed Over, In Sales Pipeline, Won, Dropped
            $table->foreignId('handed_over_to')->nullable()->constrained('users')->nullOnDelete(); // Sales PIC
            $table->dateTime('handed_over_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cro_opportunities');
        Schema::dropIfExists('cro_account_health');
        Schema::dropIfExists('cro_concerns');
        Schema::dropIfExists('cro_csat_surveys');
        Schema::dropIfExists('cro_engagements');
    }
};
