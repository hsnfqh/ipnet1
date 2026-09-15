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
        // 1. Central Document Register & Vault
        Schema::create('admin_documents', function (Blueprint $table) {
            $table->id();
            $table->string('doc_number')->unique(); // e.g. DOC-SPK-2026-001, DOC-PO-2026-004
            $table->string('title');
            $table->string('doc_type'); // SPK, PO Client, PO Vendor, Contract, WO, Tender, Proposal, BAST, SLA Agreement, Delivery Receipt, Other
            $table->string('category')->default('General'); // Commercial, Legal, Project Delivery, Operations, Procurement
            $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();
            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->foreignId('vendor_id')->nullable()->constrained('vendors')->nullOnDelete();
            $table->string('client_name')->nullable();
            $table->string('vendor_name')->nullable();
            $table->string('version')->default('v1.0');
            $table->string('status')->default('Under Review'); // Draft, Under Review, Clarification Requested, Verified / Complete, Archived
            $table->string('verification_status')->default('Pending'); // Pending, Approved, Clarification Requested, Rejected
            $table->text('rejection_notes')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->string('file_path')->nullable();
            $table->date('effective_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->decimal('value', 18, 2)->default(0);
            $table->string('physical_archive_location')->nullable(); // e.g. Lemari Arsip A - Box 04
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 2. Project Document Gatekeeper Checklists
        Schema::create('admin_document_checklists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->string('milestone'); // Pre-Sales Handover, Project Kickoff, Procurement, Implementation, BAST Handover
            $table->string('document_name'); // e.g. Signed SPK / PO, SOW, ABD, BAST 1, UAT Report
            $table->boolean('is_mandatory')->default(true);
            $table->boolean('is_submitted')->default(false);
            $table->foreignId('admin_document_id')->nullable()->constrained('admin_documents')->nullOnDelete();
            $table->boolean('is_verified')->default(false);
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 3. Logistics & Delivery Instructions (Surat Jalan / Delivery Order)
        Schema::create('admin_logistics_dispatches', function (Blueprint $table) {
            $table->id();
            $table->string('dispatch_number')->unique(); // e.g. SJ-2026-0001
            $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();
            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->string('client_name')->nullable();
            $table->date('dispatch_date');
            $table->string('courier_type')->default('Internal Driver'); // Internal Driver, Ekspedisi / Kurir, Vendor Direct, Hand Carry
            $table->string('courier_name')->nullable(); // e.g. Pak Supri (Driver IP-NET) / JNE Trucking
            $table->string('tracking_ref')->nullable(); // No Resi / Plat Mobil
            $table->string('origin_warehouse')->default('HQ Warehouse Jakarta');
            $table->text('destination_address');
            $table->string('recipient_name');
            $table->string('recipient_phone')->nullable();
            $table->string('status')->default('Ready to Dispatch'); // Draft, Ready to Dispatch, In Transit, Delivered, Confirmed / Signed
            $table->string('delivery_receipt_path')->nullable(); // Foto / Dokumen Tanda Terima
            $table->text('delivery_notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 4. Dispatch Items
        Schema::create('admin_dispatch_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dispatch_id')->constrained('admin_logistics_dispatches')->cascadeOnDelete();
            $table->string('item_name');
            $table->string('category')->default('Hardware');
            $table->integer('quantity')->default(1);
            $table->string('unit')->default('Unit');
            $table->text('serial_numbers')->nullable(); // comma-separated or JSON
            $table->string('condition')->default('New / Segel');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 5. Master Serial Number Registry (SN Tracking)
        Schema::create('admin_serial_numbers', function (Blueprint $table) {
            $table->id();
            $table->string('serial_number')->unique();
            $table->string('product_name');
            $table->string('brand')->nullable(); // Cisco, Huawei, Mikrotik, Fortinet, Dell, etc.
            $table->string('model')->nullable();
            $table->string('category')->default('Router'); // Router, Switch, Firewall, Access Point, Server, SFP, Optical, Power Supply
            $table->string('current_status')->default('In Warehouse'); // In Warehouse, Allocated to Project, Dispatched / In Transit, Installed at Site, RMA / Maintenance, Decommissioned
            $table->string('current_location')->default('HQ Warehouse Jakarta');
            $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();
            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->string('client_name')->nullable();
            $table->foreignId('dispatch_id')->nullable()->constrained('admin_logistics_dispatches')->nullOnDelete();
            $table->date('warranty_expiry')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 6. Equipment & Operational Tool Assets (OTDR, Splicer, Toolkit, etc.)
        Schema::create('admin_equipment_assets', function (Blueprint $table) {
            $table->id();
            $table->string('asset_code')->unique(); // e.g. AST-OTDR-001, AST-SPL-002
            $table->string('asset_name');
            $table->string('category')->default('Testing Tool'); // Testing Tool, Fusion Splicer, Power Meter, Toolkit, Laptop / Device, Fleet / Kendaraan
            $table->string('brand_model')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('condition')->default('Good'); // Excellent, Good, Fair, Need Calibration, Damaged
            $table->string('status')->default('Available in HQ'); // Available in HQ, Borrowed by Engineer, Deployed on Site, In Maintenance
            $table->foreignId('current_borrower_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('borrowed_at')->nullable();
            $table->date('expected_return_date')->nullable();
            $table->string('storage_location')->default('HQ Workshop / Rak Alat');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 7. Project Administration Handover Records & Repository Archive
        Schema::create('admin_project_handovers', function (Blueprint $table) {
            $table->id();
            $table->string('handover_number')->unique(); // e.g. HND-2026-0001
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->string('target_division')->default('Client'); // Client, Managed Service, Finance
            $table->date('handover_date');
            $table->string('status')->default('Audit Review'); // In Assembly, Audit Review, Verified & Accepted, Returned for Incomplete
            $table->integer('completeness_score')->default(100); // 0 - 100%
            $table->foreignId('auditor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('audited_at')->nullable();
            $table->text('audit_notes')->nullable();
            $table->string('archive_box_code')->nullable(); // e.g. BOX-2026-PRJ-01
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_project_handovers');
        Schema::dropIfExists('admin_equipment_assets');
        Schema::dropIfExists('admin_serial_numbers');
        Schema::dropIfExists('admin_dispatch_items');
        Schema::dropIfExists('admin_logistics_dispatches');
        Schema::dropIfExists('admin_document_checklists');
        Schema::dropIfExists('admin_documents');
    }
};
