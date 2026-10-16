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
