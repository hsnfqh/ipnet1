<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use App\Models\Task;
use App\Models\Schedule;
use App\Models\Project;

class CleanMaintenanceData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ms:clean {--force : Force the operation to run without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Bersihkan seluruh data operasional dan dummy di modul Maintenance / Managed Service';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->option('force') && !$this->confirm('Apakah Anda yakin ingin membersihkan semua data transaksi, tiket CM, siklus PM, dan dummy di modul Maintenance?')) {
            $this->info('Operasi dibatalkan.');
            return 0;
        }

        $this->info('Memulai pembersihan data Maintenance / Managed Service...');

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // 1. Truncate tabel transaksional Managed Service
        $msTables = [
            'managed_service_tickets'   => 'Tiket Gangguan / Insiden (CM & Service Request)',
            'managed_service_pm_cycles' => 'Siklus Preventive Maintenance (PM 1–12)',
            'managed_service_reports'   => 'Laporan Maintenance & SLA',
            'managed_service_assets'    => 'Aset Klien SLA (Configuration Items)',
        ];

        foreach ($msTables as $tbl => $label) {
            if (Schema::hasTable($tbl)) {
                $count = DB::table($tbl)->count();
                DB::table($tbl)->truncate();
                $this->line(" - Tabel {$tbl} ({$label}): {$count} baris dihapus.");
            }
        }

        // 2. Bersihkan Task terkait Maintenance / CM
        if (Schema::hasTable('tasks')) {
            $tasksCount = Task::where(function($q) {
                $q->where('title', 'like', '[CM-%')
                  ->orWhere('title', 'like', '[MS -%')
                  ->orWhere('title', 'like', '%Preventive Maintenance%')
                  ->orWhere('title', 'like', '%Siklus-%');
            })->delete();
            $this->line(" - Tasks Maintenance / CM: {$tasksCount} task dihapus.");
        }

        // 3. Bersihkan Schedule terkait Maintenance / CM
        if (Schema::hasTable('schedules')) {
            $schedCount = Schedule::where(function($q) {
                $q->where('title', 'like', '[CM-%')
                  ->orWhere('title', 'like', '[MS -%')
                  ->orWhere('title', 'like', '%Preventive Maintenance%')
                  ->orWhere('title', 'like', '%Siklus-%');
            })->delete();
            $this->line(" - Schedules Maintenance / CM: {$schedCount} agenda dihapus.");
        }

        // 4. Hapus proyek dummy Bank Raya jika ada
        if (Schema::hasTable('projects')) {
            $deletedDummy = Project::where('name', 'like', '%Layanan Managed Service & Pemeliharaan Jaringan Bank Raya%')
                ->orWhere('po_spk_number', 'SPK/IPNET/BRY/2026/014')
                ->delete();
            if ($deletedDummy > 0) {
                $this->line(" - Proyek Dummy Bank Raya: {$deletedDummy} proyek dihapus.");
            }

            // 5. Reset status Handover Managed Service di proyek lain
            $updatedProjects = Project::where('stage', 'Operate')
                ->orWhereNotNull('ms_handover_status')
                ->update([
                    'ms_handover_status' => null,
                    'ms_accepted_at'     => null,
                    'ms_accepted_by'     => null,
                    'stage'              => 'Implementation',
                ]);
            $this->line(" - Reset Handover Proyek MS: {$updatedProjects} proyek di-reset.");
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        if (function_exists('opcache_reset')) {
            @opcache_reset();
        }

        Artisan::call('optimize:clear');

        $this->info('✓ Seluruh data Maintenance / Managed Service berhasil dibersihkan!');
        return 0;
    }
}
