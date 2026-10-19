<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('managed_service_tickets', function (Blueprint $table) {
            if (!Schema::hasColumn('managed_service_tickets', 'contact_email')) {
                $table->string('contact_email')->nullable()->after('contact_phone');
            }
            if (!Schema::hasColumn('managed_service_tickets', 'incident_date')) {
                $table->dateTime('incident_date')->nullable()->after('description');
            }
            if (!Schema::hasColumn('managed_service_tickets', 'category')) {
                $table->string('category')->nullable()->after('type'); // Jaringan, Server, Hardware, Security, etc.
            }
            if (!Schema::hasColumn('managed_service_tickets', 'attachment_path')) {
                $table->string('attachment_path')->nullable()->after('root_cause');
            }
            if (!Schema::hasColumn('managed_service_tickets', 'source')) {
                $table->string('source')->default('Internal')->after('status'); // External Portal, Internal, Email, Phone
            }
        });
    }

    public function down(): void
    {
        Schema::table('managed_service_tickets', function (Blueprint $table) {
            $columns = ['contact_email', 'incident_date', 'category', 'attachment_path', 'source'];
            foreach ($columns as $col) {
                if (Schema::hasColumn('managed_service_tickets', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
