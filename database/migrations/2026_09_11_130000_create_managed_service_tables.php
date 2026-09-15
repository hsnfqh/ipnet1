<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Asset / CI Data Table
        if (!Schema::hasTable('managed_service_assets')) {
            Schema::create('managed_service_assets', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('project_id')->nullable();
                $table->string('client_name');
                $table->string('device_name');
                $table->string('category')->default('Switch'); // Router, Switch, Firewall, Server, AP, UPS, Storage, Other
                $table->string('brand')->nullable(); // Cisco, Fortinet, Mikrotik, Ruijie, Dell, HP, etc.
                $table->string('model')->nullable();
                $table->string('serial_number')->nullable();
                $table->string('ip_address')->nullable();
                $table->string('location_site')->nullable(); // HQ Data Center, Cabang Surabaya, etc.
                $table->string('rack_position')->nullable(); // Rack 02 - U14
                $table->string('status')->default('Online'); // Online, Warning, Offline, Maintenance
                $table->date('warranty_expiry')->nullable();
                $table->text('notes')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();

                $table->foreign('project_id')->references('id')->on('projects')->onDelete('set null');
                $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            });
        }

        // 2. Incident & Service Request Tickets Table
        if (!Schema::hasTable('managed_service_tickets')) {
            Schema::create('managed_service_tickets', function (Blueprint $table) {
                $table->id();
                $table->string('ticket_number')->unique(); // INC-2026-001, REQ-2026-001, CR-2026-001
                $table->unsignedBigInteger('project_id')->nullable();
                $table->string('client_name');
                $table->string('title');
                $table->text('description')->nullable();
                $table->string('type')->default('Incident'); // Incident, Service Request, Change Request
                $table->string('priority')->default('P2 - Major'); // P1 - Critical (1h), P2 - Major (4h), P3 - Minor (8h), P4 - Low (24h)
                $table->string('status')->default('Open'); // Open, In Progress, Pending Vendor, Resolved, Closed
                $table->string('reported_by')->nullable();
                $table->string('contact_phone')->nullable();
                $table->unsignedBigInteger('assigned_to')->nullable(); // foreign to users (Doris/Mario/Eris)
                $table->unsignedBigInteger('asset_id')->nullable(); // foreign to managed_service_assets
                $table->dateTime('sla_deadline')->nullable();
                $table->dateTime('resolved_at')->nullable();
                $table->boolean('sla_met')->default(true);
                $table->text('resolution_notes')->nullable();
                $table->string('root_cause')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();

                $table->foreign('project_id')->references('id')->on('projects')->onDelete('set null');
                $table->foreign('assigned_to')->references('id')->on('users')->onDelete('set null');
                $table->foreign('asset_id')->references('id')->on('managed_service_assets')->onDelete('set null');
                $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            });
        }

        // 3. Managed Service Reports (SLA, PM, Incident Post-Mortem)
        if (!Schema::hasTable('managed_service_reports')) {
            Schema::create('managed_service_reports', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('project_id')->nullable();
                $table->string('client_name');
                $table->string('title');
                $table->string('report_type')->default('Preventive Maintenance'); // Preventive Maintenance, SLA Review, Incident Post-Mortem, Service Activation
                $table->string('period_month')->nullable(); // September
                $table->integer('period_year')->default(2026);
                $table->float('sla_score')->default(99.9);
                $table->text('summary')->nullable();
                $table->string('status')->default('Published'); // Draft, Published, Approved by Client
                $table->string('file_path')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();

                $table->foreign('project_id')->references('id')->on('projects')->onDelete('set null');
                $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('managed_service_reports');
        Schema::dropIfExists('managed_service_tickets');
        Schema::dropIfExists('managed_service_assets');
    }
};
