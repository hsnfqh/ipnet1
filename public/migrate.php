<?php
// public/migrate.php

define('LARAVEL_START', microtime(true));

// Auto-detect base path for both Local Development and cPanel hosting
if (file_exists(__DIR__ . '/../web-ipnet-final/vendor/autoload.php')) {
    $basePath = __DIR__ . '/../web-ipnet-final';
} elseif (file_exists(__DIR__ . '/../web-ipnet/vendor/autoload.php')) {
    $basePath = __DIR__ . '/../web-ipnet';
} elseif (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    $basePath = __DIR__ . '/..';
} else {
    $basePath = __DIR__;
}

require $basePath . '/vendor/autoload.php';
$app = require_once $basePath . '/bootstrap/app.php';

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
    <h1> Database Migration & Optimizer</h1>
    <p style="color: #948F99; font-size: 14px;">Menjalankan sinkronisasi database MySQL cPanel dan optimasi cache...</p>

    <?php
    try {
        echo "<h3 style='color:#fff; font-size:14px;'>0. Git Base Path & Version:</h3>";
        echo "<pre>Base Path: " . htmlspecialchars($basePath) . "\n";
        $gitLog = @shell_exec("git -C \"$basePath\" log -n 1 --oneline 2>&1");
        echo "Current Server Commit: " . htmlspecialchars($gitLog ?: 'N/A') . "</pre>";

        echo "<h3 style='color:#fff; font-size:14px;'>1. Git Pull Origin Main:</h3>";
        $gitPull = @shell_exec("git -C \"$basePath\" pull origin main 2>&1");
        echo "<pre>" . htmlspecialchars($gitPull ?: 'git pull N/A (shell_exec disabled or git not in path)') . "</pre>";

        echo "<h3 style='color:#fff; font-size:14px;'>2. Status Migration:</h3>";
        Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        $output = Illuminate\Support\Facades\Artisan::output();
        echo "<pre>" . (trim($output) ?: "Semua tabel database sudah yang terbaru (Nothing to migrate).") . "</pre>";

        echo "<h3 style='color:#fff; font-size:14px;'>3. Clear Cache (Route, View, Config, Optimize):</h3>";
        Illuminate\Support\Facades\Artisan::call('route:clear');
        Illuminate\Support\Facades\Artisan::call('view:clear');
        Illuminate\Support\Facades\Artisan::call('config:clear');
        Illuminate\Support\Facades\Artisan::call('cache:clear');
        if (function_exists('opcache_reset')) {
            @opcache_reset();
        }
        echo "<pre>✅ Route cache cleared.\n✅ View cache cleared.\n✅ Config cache cleared.\n✅ Application cache cleared.\n✅ OPcache reset.</pre>";

        echo "<h3 style='color:#fff; font-size:14px;'>4. Uji Render PDF Langsung di Server:</h3>";
        try {
            $msCtrl = new \App\Http\Controllers\ManagedServiceController();
            $dummyReq = Illuminate\Http\Request::create('/managed-service/reporting/export-pdf', 'GET');
            
            // 4a. Portfolio PDF
            $resPort = $msCtrl->exportPortfolioPdf($dummyReq);
            echo "<pre>✅ Test Portfolio PDF BERHASIL: Status " . $resPort->getStatusCode() . ", Output Byte: " . strlen($resPort->getContent()) . "</pre>";

            // 4b. Project 54 PDF
            $proj54 = \App\Models\Project::find(54) ?? \App\Models\Project::first();
            if ($proj54) {
                $reqProj = Illuminate\Http\Request::create('/managed-service/projects/' . $proj54->id . '/export-pdf', 'GET');
                $resProj = $msCtrl->exportProjectPdf($reqProj, $proj54->id);
                echo "<pre>✅ Test Project PDF (ID " . $proj54->id . ": " . htmlspecialchars($proj54->name) . ") BERHASIL: Status " . $resProj->getStatusCode() . ", Output Byte: " . strlen($resProj->getContent()) . "</pre>";
            }
        } catch (\Throwable $pdfErr) {
            echo "<pre style='color:#f87171;'>❌ Error Render PDF di Server:\n" . htmlspecialchars($pdfErr->getMessage()) . "\nDi File: " . $pdfErr->getFile() . ":" . $pdfErr->getLine() . "\n" . htmlspecialchars($pdfErr->getTraceAsString()) . "</pre>";
        }

        echo "<p><span class='badge'>SUKSES</span> Sinkronisasi selesai!</p>";
        echo "<a href='/managed-service/reporting' class='btn'>Buka Reporting Managed Service →</a>";
    } catch (\Throwable $e) {
        echo "<h3 style='color:#f87171; font-size:14px;'>❌ Error Terjadi:</h3>";
        echo "<pre style='color:#f87171;'>" . htmlspecialchars($e->getMessage()) . "\n" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    }
    ?>
</div>
</body>
</html>
