<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('sales:seed-dummy', function () {
    $this->info('Seeding realistic Sales Dummy Reports (Activity & Lesson Learned)...');
    $seeder = new \Database\Seeders\SalesDummyReportSeeder();
    $seeder->run();
    $this->info('Dummy Sales Activity (5 rows, Syaiful & Hasan Faqih) and Lesson Learned cards successfully created!');
})->purpose('Seed realistic Sales Activity & Lesson Learned dummy report data');

Artisan::command('project:clean-data', function () {
    $this->warn('Membersihkan seluruh data proyek dan operasional (data user & role tetap aman)...');

    $projectTables = [
        'activity_document_signatures',
        'digital_signature_documents',
        'engineer_activity_logs',
        'managed_service_tickets',
        'managed_service_pm_cycles',
        'managed_service_reports',
        'managed_service_assets',
        'admin_dispatch_items',
        'admin_logistics_dispatches',
        'admin_serial_numbers',
        'admin_equipment_assets',
        'admin_document_checklists',
        'admin_documents',
        'admin_project_handovers',
        'task_user',
        'tasks',
        'schedule_user',
        'schedules',
        'project_documents',
        'sales_activities',
        'cro_account_health',
        'cro_concerns',
        'cro_csat_surveys',
        'cro_engagements',
        'cro_opportunities',
        'market_intelligences',
        'partnerships',
        'inventory_transactions',
        'notifications',
        'projects',
    ];

    \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS = 0;');

    foreach ($projectTables as $table) {
        if (\Illuminate\Support\Facades\Schema::hasTable($table)) {
            \Illuminate\Support\Facades\DB::table($table)->truncate();
            $this->line("  ✓ Table [{$table}] dibersihkan & reset AUTO_INCREMENT.");
        }
    }

    \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS = 1;');

    $userCount = \App\Models\User::count();
    $this->info("Pembersihan selesai! Seluruh data proyek bersih, {$userCount} data user tetap aman.");
})->purpose('Bersihkan semua data projek & operasional dengan tetap mempertahankan data user dan role');
