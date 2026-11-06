<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('projects')) {
            Schema::table('projects', function (Blueprint $table) {
                if (!Schema::hasColumn('projects', 'sharing_knowledge')) {
                    $table->string('sharing_knowledge')->nullable()->after('pm_total_cycles');
                }
            });

            // Pembersihan proyek ganda akibat double-click submit
            try {
                $duplicates = DB::table('projects')
                    ->select('name', 'client', DB::raw('COUNT(*) as total'))
                    ->groupBy('name', 'client')
                    ->having('total', '>', 1)
                    ->get();

                foreach ($duplicates as $dup) {
                    $sameProjects = DB::table('projects')
                        ->where('name', $dup->name)
                        ->where('client', $dup->client)
                        ->orderBy('id', 'asc')
                        ->get();

                    if ($sameProjects->count() > 1) {
                        $toDelete = $sameProjects->slice(1);
                        foreach ($toDelete as $pDel) {
                            DB::table('managed_service_pm_cycles')->where('project_id', $pDel->id)->delete();
                            DB::table('managed_service_tickets')->where('project_id', $pDel->id)->delete();
                            DB::table('project_documents')->where('project_id', $pDel->id)->delete();
                            DB::table('projects')->where('id', $pDel->id)->delete();
                        }
                    }
                }
            } catch (\Throwable $e) {}
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('projects')) {
            Schema::table('projects', function (Blueprint $table) {
                if (Schema::hasColumn('projects', 'sharing_knowledge')) {
                    $table->dropColumn('sharing_knowledge');
                }
            });
        }
    }
};
