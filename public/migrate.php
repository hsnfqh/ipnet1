<?php
// public/migrate.php

define('LARAVEL_START', microtime(true));

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Database Migration & Cache Optimizer - IP-Net</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #0E0D12; color: #fff; padding: 40px 20px; line-height: 1.6; }
        .container { max-width: 700px; margin: 0 auto; background: #17151C; border: 1px solid #2E2C34; border-radius: 16px; padding: 30px; box-shadow: 0 20px 50px rgba(0,0,0,0.5); }
        h1 { font-size: 20px; color: #fff; margin-top: 0; display: flex; align-items: center; gap: 10px; }
        pre { background: #000; color: #4ade80; padding: 16px; border-radius: 10px; overflow-x: auto; font-family: monospace; font-size: 13px; border: 1px solid #222; }
        .btn { display: inline-block; background: #C81E2C; color: white; padding: 10px 20px; border-radius: 10px; text-decoration: none; font-weight: 600; font-size: 14px; margin-top: 15px; }
        .btn:hover { background: #A31622; }
        .badge { background: #1B7A46; color: #fff; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: bold; }
    </style>
</head>
<body>
<div class="container">
    <h1>🚀 Database Migration & Optimizer</h1>
    <p style="color: #948F99; font-size: 14px;">Menjalankan sinkronisasi database MySQL cPanel dan optimasi cache...</p>

    <?php
    try {
        echo "<h3 style='color:#fff; font-size:14px;'>1. Status Migration:</h3>";
        Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        $output = Illuminate\Support\Facades\Artisan::output();
        echo "<pre>" . (trim($output) ?: "Semua tabel database sudah yang terbaru (Nothing to migrate).") . "</pre>";

        echo "<h3 style='color:#fff; font-size:14px;'>2. Clear View & Config Cache:</h3>";
        Illuminate\Support\Facades\Artisan::call('view:clear');
        Illuminate\Support\Facades\Artisan::call('config:clear');
        Illuminate\Support\Facades\Artisan::call('cache:clear');
        echo "<pre>✅ View cache cleared.\n✅ Config cache cleared.\n✅ Application cache cleared.</pre>";

        echo "<p><span class='badge'>SUKSES</span> Database dan cache berhasil diperbarui 100%!</p>";
        echo "<a href='/' class='btn'>Buka Dashboard Aplikasi →</a>";
    } catch (\Throwable $e) {
        echo "<h3 style='color:#f87171; font-size:14px;'>❌ Error Terjadi:</h3>";
        echo "<pre style='color:#f87171;'>" . htmlspecialchars($e->getMessage()) . "</pre>";
    }
    ?>
</div>
</body>
</html>
