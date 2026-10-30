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

        // 2. Hapus proyek dummy Managed Service (Telkom MS 24/7 & Bank Raya)
        if (Schema::hasTable('projects')) {
            $msDummyProjects = Project::where(function($q) {
                $q->where('name', 'like', '%Kontrak Managed Service & Pemeliharaan Jaringan 24/7 PT Telkom Indonesia Tbk%')
                  ->orWhere('name', 'like', '%Layanan Managed Service & Pemeliharaan Jaringan Bank Raya%')
                  ->orWhere('po_spk_number', 'KTR/TLK-NET/2026/021')
                  ->orWhere('po_spk_number', 'SPK/IPNET/BRY/2026/014');
            })->get();

            foreach ($msDummyProjects as $dp) {
                if (Schema::hasTable('tasks')) {
                    Task::where('project_id', $dp->id)->forceDelete();
                }
                if (Schema::hasTable('schedules')) {
                    Schedule::where('project_id', $dp->id)->forceDelete();
                }
                $dp->forceDelete();
                $this->line(" - Proyek Dummy Managed Service dihapus: {$dp->name}");
            }

            // 3. Reset status Handover Managed Service di seluruh proyek tersisa agar portofolio & incoming handover MS kosong 0
            $updatedProjects = Project::where('stage', 'Operate')
                ->orWhereNotNull('ms_handover_status')
                ->orWhere('handover_target', 'managed_service')
                ->orWhere('handover_target', 'both')
                ->update([
                    'ms_handover_status' => null,
                    'ms_accepted_at'     => null,
                    'ms_accepted_by'     => null,
                    'stage'              => 'Implementation',
                    'handover_target'    => 'pmo',
                ]);
            $this->line(" - Reset Handover Proyek MS: {$updatedProjects} proyek di-reset.");
        }

        // 4. Bersihkan Task terkait Maintenance / CM
        if (Schema::hasTable('tasks')) {
            $tasksCount = Task::where(function($q) {
                $q->where('title', 'like', '[CM-%')
                  ->orWhere('title', 'like', '[MS-%')
                  ->orWhere('title', 'like', '%[MS -%')
                  ->orWhere('title', 'like', '%Preventive Maintenance%')
                  ->orWhere('title', 'like', '%Siklus-%')
                  ->orWhere('title', 'like', '%Maintenance%');
            })->forceDelete();
            $this->line(" - Tasks Maintenance / CM: {$tasksCount} task dihapus.");
        }

        // 5. Bersihkan Schedule terkait Maintenance / CM
        if (Schema::hasTable('schedules')) {
            $schedCount = Schedule::where(function($q) {
                $q->where('title', 'like', '[CM-%')
                  ->orWhere('title', 'like', '[MS-%')
                  ->orWhere('title', 'like', '%[MS -%')
                  ->orWhere('title', 'like', '%Preventive Maintenance%')
                  ->orWhere('title', 'like', '%Siklus-%')
                  ->orWhere('title', 'like', '%Maintenance%');
            })->forceDelete();
            $this->line(" - Schedules Maintenance / CM: {$schedCount} agenda dihapus.");
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
