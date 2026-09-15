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
            if (!Schema::hasColumn('projects', 'proposal_file')) {
                $table->string('proposal_file')->nullable()->after('po_file')->comment('Path file attachment proposal teknis dari presales');
            }
            if (!Schema::hasColumn('projects', 'proposal_notes')) {
                $table->text('proposal_notes')->nullable()->after('proposal_file')->comment('Catatan ruang lingkup teknis / SOW dari presales');
            }
            if (!Schema::hasColumn('projects', 'mandays')) {
                $table->integer('mandays')->nullable()->after('proposal_notes')->comment('Estimasi mandays engineer');
            }
            if (!Schema::hasColumn('projects', 'presales_status')) {
                $table->string('presales_status')->default('Pending')->after('mandays')->comment('Pending, In Progress, Submitted, Approved');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            if (Schema::hasColumn('projects', 'presales_status')) {
                $table->dropColumn('presales_status');
            }
            if (Schema::hasColumn('projects', 'mandays')) {
                $table->dropColumn('mandays');
            }
            if (Schema::hasColumn('projects', 'proposal_notes')) {
                $table->dropColumn('proposal_notes');
            }
            if (Schema::hasColumn('projects', 'proposal_file')) {
                $table->dropColumn('proposal_file');
            }
        });
    }
};
