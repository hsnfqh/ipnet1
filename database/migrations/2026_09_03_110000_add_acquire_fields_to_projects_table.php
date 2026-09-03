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
        Schema::table('projects', function (Blueprint $table) {
            if (!Schema::hasColumn('projects', 'contract_value')) {
                $table->decimal('contract_value', 15, 2)->nullable()->after('client')->comment('Nilai estimasi / nominal kontrak PO');
            }
            if (!Schema::hasColumn('projects', 'po_number')) {
                $table->string('po_number')->nullable()->after('contract_value')->comment('Nomor Purchase Order / Kontrak');
            }
            if (!Schema::hasColumn('projects', 'po_file')) {
                $table->string('po_file')->nullable()->after('po_number')->comment('Path file attachment PO / Kontrak');
            }
            if (!Schema::hasColumn('projects', 'acquire_status')) {
                $table->string('acquire_status')->default('Deal / PO Terbit')->after('stage')->comment('Prospek Awal, Kualifikasi Kebutuhan, Penawaran Komersial, Deal / PO Terbit, Handover to Design');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            if (Schema::hasColumn('projects', 'acquire_status')) {
                $table->dropColumn('acquire_status');
            }
            if (Schema::hasColumn('projects', 'po_file')) {
                $table->dropColumn('po_file');
            }
            if (Schema::hasColumn('projects', 'po_number')) {
                $table->dropColumn('po_number');
            }
            if (Schema::hasColumn('projects', 'contract_value')) {
                $table->dropColumn('contract_value');
            }
        });
    }
};
