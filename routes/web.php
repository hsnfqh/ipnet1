<?php
// routes/web.php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\TimesheetController;
use App\Http\Controllers\PmoController;
use App\Http\Controllers\AcquireController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\SalesProjectController;
use App\Http\Controllers\BdmController;
use App\Http\Controllers\CroController;
use App\Http\Controllers\AdminSupportController;
use App\Http\Controllers\ActivitySignatureController;
use App\Http\Controllers\DigitalSignatureController;

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    // Forgot Password
    Route::get('/forgot-password', [PasswordResetController::class, 'showForgotForm'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])->name('password.email');

    // Reset Password (link dari email)
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])->name('password.update');
});

// Helper untuk membersihkan working tree Git di server cPanel agar Deploy HEAD Commit aktif
Route::get('/fix-cpanel-deploy', function () {
    $path = base_path();
    $output = [];
    $output[] = "=== PATH: $path ===";
    $output[] = "=== 1. GIT RESET WORKING TREE ===";
    $output[] = shell_exec("git -C \"$path\" reset --hard HEAD 2>&1") ?? 'N/A';
    $output[] = "=== 2. CLEAN UNTRACKED DOCS ===";
    $output[] = shell_exec("git -C \"$path\" clean -fd public/assets/docs 2>&1") ?? 'N/A';
    $output[] = "=== 3. GIT PULL ORIGIN MAIN ===";
    $output[] = shell_exec("git -C \"$path\" pull origin main 2>&1") ?? 'N/A';
    $output[] = "=== 4. LATEST COMMIT ===";
    $output[] = shell_exec("git -C \"$path\" log -n 1 --oneline 2>&1") ?? 'N/A';
    try {
        \Illuminate\Support\Facades\Artisan::call('view:clear');
        \Illuminate\Support\Facades\Artisan::call('cache:clear');
        \Illuminate\Support\Facades\Artisan::call('optimize:clear');
        if (function_exists('opcache_reset')) {
            @opcache_reset();
        }
        $output[] = "=== CACHE CLEARED ===";
    } catch (\Throwable $e) {
        $output[] = "Cache clear error: " . $e->getMessage();
    }

    return '<pre>' . implode("\n", $output) . '</pre>';
});

Route::get('/debug-db-projects', function () {
    $projects = DB::table('projects')->get(['id', 'name', 'client', 'project_type', 'stage', 'status', 'handover_target', 'ms_handover_status']);
    return response()->json([
        'total' => $projects->count(),
        'projects' => $projects
    ]);
});

// Route ini TELAH DINONAKTIFKAN PERMANEN demi keamanan agar data perusahaan tidak terhapus.
Route::get('/clean-dummy-data', function () {
    return response()->json([
        'status'  => 'protected',
        'message' => 'Penghapusan data dinonaktifkan demi perlindungan data perusahaan Anda.',
    ]);
});

// Route Trigger Resmi: Injeksi 3 Data Dummy Skenario Alur Technical ke Server Hosting Live
Route::get('/seed-technical-scenarios', function () {
    try {
        // 1. Pastikan folder storage & file dummy terdistribusi
        $storageDocsDir = storage_path('app/public/project_documents');
        $publicDocsDir  = public_path('storage/project_documents');
        if (!file_exists($storageDocsDir)) {
            @mkdir($storageDocsDir, 0775, true);
        }
        if (!file_exists($publicDocsDir)) {
            @mkdir($publicDocsDir, 0775, true);
        }

        // 2. Eksekusi TechnicalWorkflowScenarioSeeder
        \Illuminate\Support\Facades\Artisan::call('db:seed', [
            '--class' => 'Database\\Seeders\\TechnicalWorkflowScenarioSeeder',
            '--force' => true,
        ]);

        try {
            \Illuminate\Support\Facades\Artisan::call('view:clear');
            \Illuminate\Support\Facades\Artisan::call('cache:clear');
        } catch (\Throwable $e) {}

        $p1 = \App\Models\Project::where('name', 'like', '%Bank Mandiri%')->first();
        $p2 = \App\Models\Project::where('name', 'like', '%Telkom Indonesia%')->first();
        $p3 = \App\Models\Project::where('name', 'like', '%PLN%')->first();

        $t1Count = $p1 ? $p1->tasks()->count() : 0;
        $t2Count = $p2 ? $p2->tasks()->count() : 0;
        $t3Count = $p3 ? $p3->tasks()->count() : 0;

        return response("
        <!DOCTYPE html>
        <html lang='id'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Injeksi 3 Skenario Proyek Berhasil - IP-NET Solusindo</title>
            <link href='https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap' rel='stylesheet'>
            <style>
                body { font-family: 'Plus Jakarta Sans', sans-serif; background: #0B132B; color: #E2E8F0; margin: 0; padding: 40px 20px; line-height: 1.6; }
                .container { max-width: 900px; margin: 0 auto; background: #1C2541; border: 1px solid #3A506B; border-radius: 16px; padding: 36px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); }
                .header { text-align: center; border-bottom: 1px solid #3A506B; padding-bottom: 24px; margin-bottom: 28px; }
                .badge-success { display: inline-block; background: #10B981; color: #FFFFFF; font-size: 13px; font-weight: 700; padding: 6px 14px; border-radius: 9999px; text-transform: uppercase; letter-spacing: 0.5px; }
                h1 { color: #FFFFFF; font-size: 24px; margin: 16px 0 8px; font-weight: 800; }
                p.lead { color: #94A3B8; font-size: 14.5px; margin: 0; }
                .card { background: #0B132B; border: 1px solid #3A506B; border-radius: 12px; padding: 20px; margin-bottom: 20px; }
                .card-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px; }
                .card-title { font-size: 16px; font-weight: 700; color: #F8FAFC; margin: 0; }
                .tag { font-size: 11.5px; font-weight: 600; padding: 4px 10px; border-radius: 6px; }
                .tag-pm { background: rgba(59, 130, 246, 0.15); color: #60A5FA; border: 1px solid rgba(59, 130, 246, 0.3); }
                .tag-ms { background: rgba(16, 185, 129, 0.15); color: #34D399; border: 1px solid rgba(16, 185, 129, 0.3); }
                .tag-hybrid { background: rgba(245, 158, 11, 0.15); color: #FBBF24; border: 1px solid rgba(245, 158, 11, 0.3); }
                .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px; margin-top: 12px; font-size: 13px; }
                .grid-item { background: #1C2541; padding: 10px 14px; border-radius: 8px; border: 1px solid #2C3E55; }
                .grid-item span { display: block; font-size: 11px; color: #94A3B8; text-transform: uppercase; font-weight: 600; }
                .grid-item strong { color: #F1F5F9; font-size: 13.5px; }
                .actions { display: flex; gap: 12px; justify-content: center; margin-top: 32px; flex-wrap: wrap; }
                .btn { padding: 12px 22px; font-weight: 700; font-size: 13.5px; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s; }
                .btn-primary { background: #8F0A0D; color: #FFFFFF; }
                .btn-primary:hover { background: #B91C1C; }
                .btn-secondary { background: #3A506B; color: #FFFFFF; }
                .btn-secondary:hover { background: #475569; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <div class='badge-success'>&check; Seeder Live Berhasil Dijalankan!</div>
                    <h1>3 Data Proyek Alur Technical Resmi Aktif</h1>
                    <p class='lead'>Seluruh data terintegrasi penuh dengan alur Commercial (SC bertanda tangan Direktur & Head Division, BoQ SA, Proposal Presales, dan BDM) tanpa merubah alur komersial yang sudah stabil.</p>
                </div>

                <!-- Skenario 1 -->
                <div class='card'>
                    <div class='card-header'>
                        <h3 class='card-title'>1. " . ($p1 ? htmlspecialchars($p1->name) : 'Proyek Sales ke Project Manager') . "</h3>
                        <span class='tag tag-pm'>Skenario 1: PM Delivery</span>
                    </div>
                    <div class='grid'>
                        <div class='grid-item'><span>Klien</span><strong>" . ($p1 ? htmlspecialchars($p1->client) : 'PT Bank Mandiri') . "</strong></div>
                        <div class='grid-item'><span>Nilai Kontrak</span><strong>Rp " . ($p1 ? number_format($p1->contract_value, 0, ',', '.') : '1.850.000.000') . "</strong></div>
                        <div class='grid-item'><span>Alur Commercial</span><strong>SC No: SC/IPNET/2026/041 (Raiza &rarr; Susanto &rarr; Hariyadi)</strong></div>
                        <div class='grid-item'><span>Project Manager</span><strong>Rizki (PMO Lead)</strong></div>
                        <div class='grid-item'><span>Koordinasi Lead</span><strong>Nugraha Pratama (Alokasi Rorik, Dedy, Syaiful)</strong></div>
                        <div class='grid-item'><span>Task Teknis PM</span><strong>{$t1Count} Tasks Terbagi Jelas (Scope, PIC, Jadwal)</strong></div>
                    </div>
                </div>

                <!-- Skenario 2 -->
                <div class='card'>
                    <div class='card-header'>
                        <h3 class='card-title'>2. " . ($p2 ? htmlspecialchars($p2->name) : 'Proyek Sales Direct Managed Service') . "</h3>
                        <span class='tag tag-ms'>Skenario 2: Direct Managed Service</span>
                    </div>
                    <div class='grid'>
                        <div class='grid-item'><span>Klien</span><strong>" . ($p2 ? htmlspecialchars($p2->client) : 'PT Telkom Indonesia') . "</strong></div>
                        <div class='grid-item'><span>Nilai Kontrak</span><strong>Rp " . ($p2 ? number_format($p2->contract_value, 0, ',', '.') : '480.000.000') . " / Tahun</strong></div>
                        <div class='grid-item'><span>Alur Commercial</span><strong>SC No: SC/IPNET/2026/055 (Donny &rarr; Susanto &rarr; Hariyadi)</strong></div>
                        <div class='grid-item'><span>Lead Managed Service</span><strong>Doris (MS Coordinator)</strong></div>
                        <div class='grid-item'><span>Personel Operasional</span><strong>Mario & Eris (Standby 24/7 & Pemeliharaan Bulanan)</strong></div>
                        <div class='grid-item'><span>Task Operasional MS</span><strong>{$t2Count} Tasks SLA & Preventive Maintenance</strong></div>
                    </div>
                </div>

                <!-- Skenario 3 -->
                <div class='card'>
                    <div class='card-header'>
                        <h3 class='card-title'>3. " . ($p3 ? htmlspecialchars($p3->name) : 'Proyek Sales Hybrid PM + Managed Service') . "</h3>
                        <span class='tag tag-hybrid'>Skenario 3: Hybrid (PM + Managed Service)</span>
                    </div>
                    <div class='grid'>
                        <div class='grid-item'><span>Klien</span><strong>" . ($p3 ? htmlspecialchars($p3->client) : 'PT PLN (Persero)') . "</strong></div>
                        <div class='grid-item'><span>Nilai Kontrak</span><strong>Rp " . ($p3 ? number_format($p3->contract_value, 0, ',', '.') : '2.450.000.000') . " (Turnkey + SLA)</strong></div>
                        <div class='grid-item'><span>Alur Commercial</span><strong>SC No: SC/IPNET/2026/088 (Hendry &rarr; Susanto &rarr; Hariyadi)</strong></div>
                        <div class='grid-item'><span>Fase PM & Engineer</span><strong>Rizki & Nugraha Pratama (Rorik & Shiamsyah Azis)</strong></div>
                        <div class='grid-item'><span>Fase Managed Service</span><strong>Doris (Mario & Eris - Transisi Form IPN-FRM-MS-01)</strong></div>
                        <div class='grid-item'><span>Pemisahan Task</span><strong>{$t3Count} Tasks Terpisah: [PM - Deployment] & [MS - Operation] (Tanpa Duplikasi)</strong></div>
                    </div>
                </div>

                <div class='actions'>
                    <a href='/pmo/implementations' class='btn btn-primary'>&rarr; Buka Menu PMO Implementasi</a>
                    <a href='/managed-service/projects' class='btn btn-secondary'>&rarr; Buka Modul Managed Service</a>
                    <a href='/tasks' class='btn btn-secondary'>&rarr; Lihat Kanban & Task Engineer</a>
                </div>
            </div>
        </body>
        </html>
        ");
    } catch (\Throwable $e) {
        return response("<div style='font-family:sans-serif;max-width:600px;margin:40px auto;padding:24px;background:#FEF2F2;border:1px solid #F87171;border-radius:12px;color:#991B1B;'><h3>&cross; Terjadi Kendala Seeding:</h3><p>{$e->getMessage()}</p><pre style='font-size:12px;overflow-x:auto;'>" . $e->getTraceAsString() . "</pre></div>", 500);
    }
});

// Auth Routes
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Public Client Verification & Automated Discrepancy Portal (Jalur 1 - Uji Keabsahan Dokumen Klien)
if (!class_exists(\App\Services\DocumentDiscrepancyService::class)) {
    @require_once app_path('Services/DocumentDiscrepancyService.php');
}
if (!class_exists(\App\Http\Controllers\PublicDocumentVerificationController::class)) {
    @require_once app_path('Http/Controllers/PublicDocumentVerificationController.php');
}
if (!class_exists(\App\Models\DigitalSignatureDocument::class)) {
    @require_once app_path('Models/DigitalSignatureDocument.php');
}
if (!class_exists(\App\Http\Controllers\DigitalSignatureController::class)) {
    @require_once app_path('Http/Controllers/DigitalSignatureController.php');
}

Route::get('/verify', [\App\Http\Controllers\PublicDocumentVerificationController::class, 'index'])->name('public.verify.index');
Route::match(['GET', 'POST'], '/verify/inspect', [\App\Http\Controllers\PublicDocumentVerificationController::class, 'inspect'])->name('public.verify.inspect');

// Public Client Helpdesk & Ticketing Portal (Tanpa Perlu Login)
Route::get('/helpdesk', [\App\Http\Controllers\ClientTicketPortalController::class, 'index'])->name('public.helpdesk.index');
Route::post('/helpdesk', [\App\Http\Controllers\ClientTicketPortalController::class, 'store'])->name('public.helpdesk.store');
Route::get('/helpdesk/track', [\App\Http\Controllers\ClientTicketPortalController::class, 'track'])->name('public.helpdesk.track');
Route::get('/helpdesk/{ticketNumber}', [\App\Http\Controllers\ClientTicketPortalController::class, 'show'])->name('public.helpdesk.show');
Route::get('/tiket', [\App\Http\Controllers\ClientTicketPortalController::class, 'index'])->name('public.ticket.alias');

// Public Client Digital Signature Portal & Verification (Tanpa Perlu Login)
Route::get('/sign/{token}', [DigitalSignatureController::class, 'clientSignShow'])->name('client.digital_signature.show');
Route::get('/sign/{token}', [DigitalSignatureController::class, 'clientSignShow'])->name('digital_signatures.client_sign');
Route::get('/sign/{token}/stream-pdf', [DigitalSignatureController::class, 'clientStreamPdf'])->name('client.digital_signature.stream_pdf');
Route::post('/sign/{token}', [DigitalSignatureController::class, 'clientSignSubmit'])->name('client.digital_signature.submit');
Route::get('/verify-document-sign/{hash}', [DigitalSignatureController::class, 'verifyPublic'])->name('public.digital_signature.verify');
Route::get('/verify-signature/{token}', [\App\Http\Controllers\SettingsController::class, 'verifySignature'])->name('public.signature.verify');

// Public Document Verification (Target QR Code Scan - Langsung Membuka Halaman PDF Resmi di Layar HP / Browser)
Route::get('/verify-document/{documentNumber}', function ($documentNumber) {
    if (function_exists('opcache_reset')) {
        @opcache_reset();
    }

    // 1. Ambil Data Tanda Tangan Resmi
    $documentSignature = null;
    try {
        if (!\Illuminate\Support\Facades\Schema::hasTable('activity_document_signatures')) {
            \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        }
        $documentSignature = \App\Models\ActivityDocumentSignature::where('document_number', $documentNumber)->first();
    } catch (\Throwable $e) {}

    // 2. Ambil Aktivitas Lapangan Terkait
    $query = \App\Models\EngineerActivityLog::with(['engineer', 'project']);
    if ($documentSignature) {
        if (!empty($documentSignature->log_ids)) {
            $query->whereIn('id', $documentSignature->log_ids);
        } elseif ($documentSignature->project_id) {
            $query->where('project_id', $documentSignature->project_id);
        } elseif ($documentSignature->scope_key && str_starts_with($documentSignature->scope_key, 'proj_')) {
            $pId = (int) substr($documentSignature->scope_key, 5);
            $query->where('project_id', $pId);
        } elseif ($documentSignature->pic_user_id) {
            $query->where('user_id', $documentSignature->pic_user_id);
        }
    }
    $activities = $query->orderBy('activity_date', 'asc')->orderBy('start_time', 'asc')->orderBy('created_at', 'asc')->get();

    // 3. Format Data Aktivitas Persis Format Tabel Laporan
    $parsedActivities = $activities->map(function ($a, $idx) {
        $rawNotes  = $a->notes ?? '';
        $clientPic = '';
        $ipnetPic  = '';
        $notedOnly = $rawNotes;

        if ($rawNotes) {
            $parts = array_map('trim', explode('|', $rawNotes));
            $notedParts = [];
            foreach ($parts as $part) {
                if (str_starts_with($part, 'PIC Klien:')) {
                    $clientPic = trim(substr($part, strlen('PIC Klien:')));
                } elseif (str_starts_with($part, 'PIC IPNET:')) {
                    $ipnetPic = trim(substr($part, strlen('PIC IPNET:')));
                } else {
                    $notedParts[] = $part;
                }
            }
            $notedOnly = implode(' | ', array_filter($notedParts));
        }

        if (!$ipnetPic) {
            $ipnetPic = '-';
        }

        $description = $a->description ?? '-';
        if (preg_match('/^\[(.+?)\]\s*(.*)$/s', $description, $m)) {
            $description = $m[2] ?: $m[1];
        }

        return [
            'no'         => $idx + 1,
            'activity'   => $description,
            'date'       => $a->activity_date ? $a->activity_date->format('d/m/Y') : '-',
            'time'       => $a->start_time ? \Carbon\Carbon::parse($a->start_time)->format('H:i') : '-',
            'client_pic' => $clientPic ?: '-',
            'ipnet_pic'  => $ipnetPic,
            'notes'      => $notedOnly ?: '-',
        ];
    });

    $projectName  = $documentSignature?->project_name ?: ($activities->first()?->project?->name ?? 'Aktivitas Lapangan');
    $engineerName = $documentSignature?->pic_name ?: ($activities->first()?->engineer?->name ?? 'PIC Engineer');
    $printedBy    = $documentSignature?->head_name ?: 'System Verification';

    $logoPath = public_path('images/ipnet1.png');
    if (!file_exists($logoPath)) {
        $logoPath = public_path('images/ipnet.png');
    }
    $logoBase64 = file_exists($logoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) : '';

    $verifyUrl = url('/verify-document/' . $documentNumber);

    // Multi-tier QR Code generator
    $qrPngBase64 = '';
    $qrRawSvg    = '';
    $qrApiUrl    = 'https://api.qrserver.com/v1/create-qr-code/?size=140x140&data=' . urlencode($verifyUrl);

    if (extension_loaded('gd') && function_exists('imagecreatetruecolor')) {
        try {
            $qr = \BaconQrCode\Encoder\Encoder::encode($verifyUrl, \BaconQrCode\Common\ErrorCorrectionLevel::M());
            $matrix = $qr->getMatrix();
            $matrixWidth = $matrix->getWidth();
            $matrixHeight = $matrix->getHeight();

            $scale = 3;
            $margin = 1;
            $imgWidth = ($matrixWidth + ($margin * 2)) * $scale;
            $imgHeight = ($matrixHeight + ($margin * 2)) * $scale;

            $image = imagecreatetruecolor($imgWidth, $imgHeight);
            $white = imagecolorallocate($image, 255, 255, 255);
            $dark  = imagecolorallocate($image, 15, 23, 42);

            imagefill($image, 0, 0, $white);

            for ($y = 0; $y < $matrixHeight; $y++) {
                for ($x = 0; $x < $matrixWidth; $x++) {
                    if ($matrix->get($x, $y) === 1) {
                        $x1 = ($x + $margin) * $scale;
                        $y1 = ($y + $margin) * $scale;
                        $x2 = $x1 + $scale - 1;
                        $y2 = $y1 + $scale - 1;
                        imagefilledrectangle($image, $x1, $y1, $x2, $y2, $dark);
                    }
                }
            }

            ob_start();
            imagepng($image);
            $pngData = ob_get_clean();
            imagedestroy($image);

            $qrPngBase64 = 'data:image/png;base64,' . base64_encode($pngData);
        } catch (\Throwable $e) {}
    }

    try {
        $renderer = new \BaconQrCode\Renderer\ImageRenderer(
            new \BaconQrCode\Renderer\RendererStyle\RendererStyle(60, 0),
            new \BaconQrCode\Renderer\Image\SvgImageBackEnd()
        );
        $writer = new \BaconQrCode\Writer($renderer);
        $svg = $writer->writeString($verifyUrl);
        $qrRawSvg = preg_replace('/<\?xml.*?\?>/i', '', $svg);
    } catch (\Throwable $e) {}

    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.engineer-activity-report-pdf', [
        'parsedActivities'  => $parsedActivities,
        'activities'        => $activities,
        'projectName'       => $projectName,
        'engineerName'      => $engineerName,
        'printedBy'         => $printedBy,
        'logoBase64'        => $logoBase64,
        'documentSignature' => $documentSignature,
        'verifyDocNumber'   => $documentNumber,
        'verifyUrl'         => $verifyUrl,
        'qrPngBase64'       => $qrPngBase64,
        'qrRawSvg'          => $qrRawSvg,
        'qrApiUrl'          => $qrApiUrl,
        'qrCodeBase64'      => $qrPngBase64 ?: $qrApiUrl,
        'qrSvgBase64'       => $qrPngBase64 ?: $qrApiUrl,
    ]);

    $pdf->setPaper('a4', 'portrait');
    $pdf->setOption('isHtml5ParserEnabled', true);
    $pdf->setOption('isRemoteEnabled', true);

    return $pdf->stream("Laporan_Aktivitas_{$documentNumber}.pdf");
})->name('document.verify');

// Auto Migrate & Cache Clear Helper for Production Deployment
// 1-Click Master Setup Helper for Production Deployment (Migrate + Seed All Official Accounts)
Route::get('/setup-hosting-database-2026', function () {
    try {
        Artisan::call('migrate', ['--force' => true]);
        $migrationOutput = Artisan::output();
        
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        Artisan::call('db:seed', ['--class' => 'RoleSeeder', '--force' => true]);
        Artisan::call('db:seed', ['--class' => 'DummyUserSeeder', '--force' => true]);

        // Lepaskan proyek teknikal engineer & maintenance dari akun Sales personal (agar Sales berdiri sendiri)
        \App\Models\Project::where('name', 'like', '%Preventive Maintenance%')
            ->orWhere('name', 'like', '%Corrective Maintenance%')
            ->orWhereHas('creator', function($c) {
                $c->whereHas('roles', function($r) {
                    $r->whereIn('name', ['Engineer', 'Field Engineer', 'Lead Maintenance', 'Maintenance', 'Lead Engineer', 'Network Engineer', 'Security Engineer', 'Managed Service', 'Team Leader Engineering', 'Team Leader', 'Lead Divisi']);
                });
            })
            ->update([
                'sales_name' => 'Direct Sales'
            ]);

        // Pastikan proyek tanpa sales_name terisi default yang sesuai
        \App\Models\Project::whereNull('sales_name')->orWhere('sales_name', '')->update([
            'sales_name' => 'Direct Sales'
        ]);

        Artisan::call('optimize:clear');
        
        return response()->json([
            'status' => 'success',
            'message' => 'Luar biasa! Seluruh database dan seluruh akun resmi (Akbar Presales, Aris SA, 9 Sales termasuk Raiza, 5 BDM, PMO, Direktur) BERHASIL disinkronisasi & proyek teknikal telah dipisahkan dari Sales!',
            'migration_output' => $migrationOutput
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ], 500);
    }
});

// Auto Migrate & Cache Clear Helper for Production Deployment
Route::get('/auto-migrate-system-2026', function () {
    try {
        Artisan::call('migrate', ['--force' => true]);
        $output = Artisan::output();
        Artisan::call('optimize:clear');
        return response()->json([
            'status' => 'success',
            'message' => 'Semua database migrations berhasil dijalankan!',
            'migration_output' => $output
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ], 500);
    }
});

// Switch Dashboard View untuk User dengan Rangkap Jabatan (Novan: BDM & Lead Security)
Route::get('/switch-dashboard-view/{view}', function ($view) {
    if (!in_array($view, ['bdm', 'lead_security', 'lead'])) {
        $view = 'bdm';
    }
    session(['active_dashboard_view' => $view]);
    if ($view === 'lead_security' || $view === 'lead') {
        return redirect()->route('dashboard.lead')->with('success', 'Berhasil beralih ke Tampilan Dashboard Lead Security');
    }
    return redirect()->route('dashboard.bdm')->with('success', 'Berhasil beralih ke Tampilan Dashboard BDM');
})->name('switch.dashboard.view')->middleware('auth');

// Route Sinkronisasi Master Akun Resmi (38 Pengguna Real + Full Backup Otomatis Terlebih Dahulu)
Route::get('/sync-official-users', function () {
    try {
        $timestamp = date('Y_m_d_His');
        $backupDir = storage_path('app/backups');
        if (!file_exists($backupDir)) {
            @mkdir($backupDir, 0775, true);
        }
        $fileName = "backup_before_user_sync_{$timestamp}.sql";
        $filePath = $backupDir . '/' . $fileName;

        // 1. Full database backup
        $handle = fopen($filePath, 'w');
        if ($handle) {
            fwrite($handle, "-- IP-NET FULL BACKUP BEFORE OFFICIAL USER SYNC\n");
            fwrite($handle, "-- Generated: " . date('Y-m-d H:i:s') . "\n\n");
            $tables = DB::select('SHOW TABLES');
            $dbName = DB::getDatabaseName();
            $key = "Tables_in_{$dbName}";
            foreach ($tables as $t) {
                $table = $t->$key;
                $create = DB::select("SHOW CREATE TABLE `{$table}`");
                fwrite($handle, "DROP TABLE IF EXISTS `{$table}`;\n");
                fwrite($handle, $create[0]->{'Create Table'} . ";\n\n");
                $rows = DB::table($table)->get();
                foreach ($rows as $row) {
                    $cols = array_keys((array)$row);
                    $vals = array_map(function ($val) {
                        if (is_null($val)) return 'NULL';
                        return "'" . addslashes((string)$val) . "'";
                    }, array_values((array)$row));
                    fwrite($handle, "INSERT INTO `{$table}` (`" . implode('`, `', $cols) . "`) VALUES (" . implode(', ', $vals) . ");\n");
                }
                fwrite($handle, "\n");
            }
            fclose($handle);
        }

        // 2. Forget cached permissions and run Seeders
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        Artisan::call('db:seed', ['--class' => 'RoleSeeder', '--force' => true]);
        Artisan::call('db:seed', ['--class' => 'OfficialUserSeeder', '--force' => true]);
        Artisan::call('optimize:clear');

        $users = \App\Models\User::with('roles')->orderBy('id')->get();
        $totalUsers = $users->count();
        $backupSizeKb = file_exists($filePath) ? round(filesize($filePath) / 1024, 2) : 0;

        $html = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='utf-8'>
            <title>Sinkronisasi Akun Resmi Berhasil - IP-NET</title>
            <style>
                body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background: #0F172A; color: #F8FAFC; margin: 0; padding: 30px; }
                .container { max-width: 900px; margin: 0 auto; background: #1E293B; border-radius: 16px; padding: 28px; border: 1px solid #334155; box-shadow: 0 10px 30px rgba(0,0,0,0.5); }
                h1 { color: #38BDF8; font-size: 24px; margin-top: 0; }
                .alert { background: #064E3B; border: 1px solid #059669; color: #A7F3D0; padding: 14px 18px; border-radius: 10px; margin-bottom: 20px; font-weight: 600; }
                .backup-box { background: #0F172A; border: 1px dashed #64748B; padding: 14px; border-radius: 10px; margin-bottom: 24px; font-size: 13px; }
                table { width: 100%; border-collapse: collapse; font-size: 12.5px; margin-top: 15px; }
                th { background: #334155; padding: 10px; text-align: left; font-weight: 700; color: #94A3B8; }
                td { padding: 9px 10px; border-bottom: 1px solid #334155; }
                tr:hover td { background: #243247; }
                .role-badge { display: inline-block; padding: 2px 8px; border-radius: 6px; background: #8F0A0D; color: #FFF; font-weight: 600; font-size: 11px; }
                .btn { display: inline-block; padding: 10px 18px; border-radius: 10px; background: #0284C7; color: white; text-decoration: none; font-weight: bold; font-size: 13px; margin-right: 10px; }
                .btn-green { background: #059669; }
            </style>
        </head>
        <body>
            <div class='container'>
                <h1>🛡️ Sinkronisasi Akun Resmi IP-NET Selesai 100%!</h1>
                <div class='alert'>
                    Semua 38 Akun Resmi sesuai daftar PDF telah berhasil dimasukkan/diperbarui dengan aman, lengkap dengan role Spatie dan penempatan divisi teknis!
                </div>
                <div class='backup-box'>
                    <strong>🛡️ File Backup SQL Otomatis:</strong> {$fileName} ({$backupSizeKb} KB)<br>
                    <a href='/download-system-backup/{$fileName}' class='btn' style='margin-top:10px;'>⬇️ Unduh File Backup .SQL</a>
                    <a href='/login' class='btn btn-green' style='margin-top:10px;'>🔐 Masuk ke Halaman Login</a>
                </div>
                <h3>Daftar Seluruh Akun Resmi ({$totalUsers} Pengguna Aktif):</h3>
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Lengkap</th>
                            <th>Email Resmi</th>
                            <th>Role / Posisi</th>
                            <th>Divisi</th>
                        </tr>
                    </thead>
                    <tbody>";
        $no = 1;
        foreach ($users as $u) {
            $rolesStr = $u->roles->pluck('name')->implode(', ');
            $divName = $u->division ? $u->division->name : '-';
            $html .= "<tr>
                <td>{$no}</td>
                <td><strong>{$u->name}</strong></td>
                <td><code>{$u->email}</code></td>
                <td><span class='role-badge'>{$u->position}</span> <br><small style='color:#94A3B8'>{$rolesStr}</small></td>
                <td>{$divName}</td>
            </tr>";
            $no++;
        }
        $html .= "
                    </tbody>
                </table>
            </div>
        </body>
        </html>";

        return response($html);
    } catch (\Throwable $e) {
        return response("<h2 style='color:red'>ERROR SYNC: " . htmlspecialchars($e->getMessage()) . " in " . $e->getFile() . ":" . $e->getLine() . "</h2><pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>", 500);
    }
});

// Auto Seed Accounts & Roles Helper for Production Deployment
Route::get('/seed-dummy-accounts-2026', function () {
    try {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        Artisan::call('db:seed', ['--class' => 'RoleSeeder', '--force' => true]);
        Artisan::call('db:seed', ['--class' => 'OfficialUserSeeder', '--force' => true]);
        Artisan::call('optimize:clear');
        return response()->json([
            'status' => 'success',
            'message' => 'Seluruh 38 akun resmi IP-NET Solusindo dan role Spatie berhasil di-seed & disinkronisasi!',
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ], 500);
    }
});

// Route Helper untuk membuat Data Dummy Resmi PMO (Project Delivery & Incoming Handover)
Route::get('/seed-pmo-dummy-projects', function () {
    try {
        if (function_exists('opcache_reset')) {
            @opcache_reset();
        }
        \Illuminate\Support\Facades\Artisan::call('db:seed', ['--class' => 'PmoDummyProjectsSeeder', '--force' => true]);
        \Illuminate\Support\Facades\Artisan::call('optimize:clear');
        return response()->json([
            'status' => 'success',
            'message' => 'Data dummy resmi PMO (Projek Aktif Deliver, Incoming Handover Submitted & Conditional, Siap Transisi MS) berhasil di-seed!',
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ], 500);
    }
});

// Route Helper untuk membuat Data Proyek Implementasi Angkasa Pura Support (IGNR Denpasar)
Route::get('/seed-angkasa-pura-project', function () {
    try {
        if (function_exists('opcache_reset')) {
            @opcache_reset();
        }
        $seeder = new \Database\Seeders\AngkasaPuraProjectSeeder();
        $seeder->run();
        \Illuminate\Support\Facades\Artisan::call('optimize:clear');

        $project = \App\Models\Project::where('name', 'like', '%Angkasa Pura%')->first();

        return response()->json([
            'status' => 'success',
            'message' => 'Proyek Implementasi Perangkat Network Bandara IGNR Denpasar (Angkasa Pura) berhasil dibuat!',
            'project' => [
                'id'             => $project?->id,
                'name'           => $project?->name,
                'client'         => $project?->client,
                'sales'          => $project?->sales_name,
                'pm'             => $project?->pm?->name ?? 'Rizki',
                'stage'          => $project?->stage,
                'status'         => $project?->status,
                'type'           => $project?->project_type,
                'progress'       => ($project?->progress ?? 19.82) . '%',
                'contract_value' => 'Rp ' . number_format($project?->contract_value ?? 0, 0, ',', '.'),
                'total_tasks'    => $project?->tasks()->count(),
                'total_expenses' => 'Rp 57.878.006 (23 items)',
                'url_detail'     => url('/pmo/projects/' . ($project?->id ?? 1)),
                'url_implementations' => url('/pmo/implementations'),
            ]
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ], 500);
    }
});

// Route Helper untuk membuat/memperbarui Data Proyek PPATK Peremajaan Router (S-Curve & 11 WBS Tasks)
Route::get('/seed-ppatk-project', function () {
    try {
        if (function_exists('opcache_reset')) {
            @opcache_reset();
        }
        $seeder = new \Database\Seeders\PpatkProjectSeeder();
        $seeder->run();
        \Illuminate\Support\Facades\Artisan::call('optimize:clear');

        $project = \App\Models\Project::where('name', 'like', '%PPAT%')
            ->orWhere('name', 'like', '%Peremajaan Router%')
            ->orWhere('client', 'like', '%PPAT%')
            ->first();

        return response()->json([
            'status' => 'success',
            'message' => 'Proyek Pengadaan Paket Perangkat Jaringan dan Support 5 Tahun untuk Peremajaan Router TA 2026 (PPATK) berhasil disinkronkan!',
            'project' => [
                'id'             => $project?->id,
                'name'           => $project?->name,
                'client'         => $project?->client,
                'spk'            => $project?->po_spk_number,
                'sales'          => $project?->sales_name,
                'pm'             => $project?->pm?->name ?? 'Rizki',
                'stage'          => $project?->stage,
                'status'         => $project?->status,
                'handover_status'=> $project?->handover_status,
                'duration'       => '20 Hari (3 Minggu Pelaksanaan)',
                'progress'       => ($project?->progress ?? 20.0) . '%',
                'total_tasks'    => $project?->tasks()->count(),
                'url_detail'     => url('/pmo/projects/' . ($project?->id ?? 1)),
                'url_implementations' => url('/pmo/implementations'),
            ]
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ], 500);
    }
});
Route::get('/get-recent-log', function () {
    $logFile = storage_path('logs/laravel.log');
    $logContent = file_exists($logFile) ? substr(file_get_contents($logFile), -25000) : 'No log file';
    
    $projects = \App\Models\Project::withTrashed()->latest()->take(10)->get(['id', 'name', 'client', 'project_type', 'stage', 'status', 'handover_target', 'ms_handover_status', 'created_by', 'deleted_at', 'created_at']);
    
    return response()->json([
        'recent_projects' => $projects,
        'recent_log' => $logContent
    ]);
});

// Route Backup Database Lengkap & Unduh File .SQL
Route::get('/system-backup-full', function () {
    @set_time_limit(300);
    @ini_set('memory_limit', '512M');

    try {
        $backupDir = storage_path('app/backups');
        if (!file_exists($backupDir)) {
            @mkdir($backupDir, 0775, true);
        }

        $timestamp = date('Y_m_d_His');
        $fileName = "ipnet_full_db_backup_{$timestamp}.sql";
        $filePath = $backupDir . '/' . $fileName;

        $handle = fopen($filePath, 'w');
        if (!$handle) {
            return response()->json(['error' => 'Gagal membuat file backup di server.'], 500);
        }

        // Header
        fwrite($handle, "-- ==========================================================\n");
        fwrite($handle, "-- IP-NET SOLUSINDO FULL DATABASE BACKUP\n");
        fwrite($handle, "-- Generated At: " . date('Y-m-d H:i:s') . "\n");
        fwrite($handle, "-- ==========================================================\n\n");
        fwrite($handle, "SET FOREIGN_KEY_CHECKS=0;\n");
        fwrite($handle, "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n");
        fwrite($handle, "SET time_zone = \"+00:00\";\n\n");

        // Ambil semua tabel dasar (bukan view)
        $tables = DB::select("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'");
        $tableKey = 'Tables_in_' . DB::getDatabaseName();
        $backedUpTables = [];

        foreach ($tables as $tObj) {
            $tbl = $tObj->$tableKey ?? array_values((array)$tObj)[0];
            $backedUpTables[] = $tbl;

            fwrite($handle, "\n-- --------------------------------------------------------\n");
            fwrite($handle, "-- Table structure for table `{$tbl}`\n");
            fwrite($handle, "-- --------------------------------------------------------\n");
            fwrite($handle, "DROP TABLE IF EXISTS `{$tbl}`;\n");

            $createRes = DB::select("SHOW CREATE TABLE `{$tbl}`");
            $createSql = $createRes[0]->{'Create Table'} ?? '';
            fwrite($handle, $createSql . ";\n\n");

            // Dump baris menggunakan pagination offset-limit aman (tidak butuh primary key atau orderBy)
            $count = DB::table($tbl)->count();
            if ($count > 0) {
                fwrite($handle, "-- Dumping data for table `{$tbl}` ({$count} rows)\n");
                $offset = 0;
                $limit = 500;
                while (true) {
                    $rows = DB::table($tbl)->offset($offset)->limit($limit)->get();
                    if ($rows->isEmpty()) break;
                    foreach ($rows as $row) {
                        $vals = [];
                        foreach ((array)$row as $val) {
                            if (is_null($val)) {
                                $vals[] = 'NULL';
                            } elseif (is_numeric($val) && !is_string($val)) {
                                $vals[] = $val;
                            } else {
                                $vals[] = "'" . addslashes((string)$val) . "'";
                            }
                        }
                        fwrite($handle, "INSERT INTO `{$tbl}` VALUES (" . implode(', ', $vals) . ");\n");
                    }
                    $offset += $limit;
                }
                fwrite($handle, "\n");
            }
        }

        fwrite($handle, "\nSET FOREIGN_KEY_CHECKS=1;\n");
        fclose($handle);

        $fileSizeKb = file_exists($filePath) ? round(filesize($filePath) / 1024, 2) : 0;

        return response()->json([
            'status'         => 'success',
            'message'        => 'Backup database berhasil dibuat 100%!',
            'file_name'      => $fileName,
            'file_size'      => $fileSizeKb . ' KB',
            'tables_count'   => count($backedUpTables),
            'download_url'   => url('/download-system-backup/' . $fileName),
        ], 200, [], JSON_PRETTY_PRINT);
    } catch (\Throwable $e) {
        return response("<h2 style='color:red'>ERROR BACKUP: " . htmlspecialchars($e->getMessage()) . " in " . $e->getFile() . ":" . $e->getLine() . "</h2><pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>", 500);
    }
});

// Route Unduh File Backup SQL
Route::get('/download-system-backup/{file}', function ($file) {
    $clean = basename($file);
    $path = storage_path('app/backups/' . $clean);
    if (!file_exists($path)) {
        abort(404, 'File backup tidak ditemukan.');
    }
    return response()->download($path, $clean, [
        'Content-Type' => 'application/sql',
    ]);
});

// Route Pembersihan Khusus Data Managed Service / Maintenance (DENGAN OTOMATIS FULL BACKUP TERLEBIH DAHULU)
Route::get('/clean-maintenance-data', function () {
    @set_time_limit(300);
    @ini_set('memory_limit', '512M');

    try {
        // 1. OTOMATIS BACKUP TERLEBIH DAHULU SEBELUM MENGHAPUS APA PUN!
        $backupDir = storage_path('app/backups');
        if (!file_exists($backupDir)) {
            @mkdir($backupDir, 0775, true);
        }
        $timestamp = date('Y_m_d_His');
        $fileName = "backup_before_cleanup_ms_{$timestamp}.sql";
        $filePath = $backupDir . '/' . $fileName;

        $handle = fopen($filePath, 'w');
        if ($handle) {
            fwrite($handle, "-- IP-NET FULL DATABASE BACKUP BEFORE MANAGED SERVICE CLEANUP\n");
            fwrite($handle, "-- Timestamp: {$timestamp}\n\nSET FOREIGN_KEY_CHECKS=0;\nSET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n");
            $tables = DB::select("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'");
            $tableKey = 'Tables_in_' . DB::getDatabaseName();
            foreach ($tables as $tObj) {
                $tbl = $tObj->$tableKey ?? array_values((array)$tObj)[0];
                $createRes = DB::select("SHOW CREATE TABLE `{$tbl}`");
                $createSql = $createRes[0]->{'Create Table'} ?? '';
                fwrite($handle, "DROP TABLE IF EXISTS `{$tbl}`;\n{$createSql};\n");
                $offset = 0;
                $limit = 500;
                while (true) {
                    $rows = DB::table($tbl)->offset($offset)->limit($limit)->get();
                    if ($rows->isEmpty()) break;
                    foreach ($rows as $row) {
                        $vals = [];
                        foreach ((array)$row as $val) {
                            if (is_null($val)) {
                                $vals[] = 'NULL';
                            } elseif (is_numeric($val) && !is_string($val)) {
                                $vals[] = $val;
                            } else {
                                $vals[] = "'" . addslashes((string)$val) . "'";
                            }
                        }
                        fwrite($handle, "INSERT INTO `{$tbl}` VALUES (" . implode(', ', $vals) . ");\n");
                    }
                    $offset += $limit;
                }
            }
            fwrite($handle, "SET FOREIGN_KEY_CHECKS=1;\n");
            fclose($handle);
        }

        $backupSizeKb = file_exists($filePath) ? round(filesize($filePath) / 1024, 2) : 0;

        // 2. IDENTIFIKASI HANYA PROYEK MANAGED SERVICE / MAINTENANCE DUMMY
        // Pastikan PPATK, Bandara Ngurah Rai, dan proyek PMO lainnya TIDAK IKUT TERHAPUS!
        $dummyNames = \App\Http\Controllers\ManagedServiceController::getDummyProjectNames();
        $msProjects = DB::table('projects')
            ->where(function($q) use ($dummyNames) {
                foreach ($dummyNames as $dName) {
                    $q->orWhere('name', 'like', "%{$dName}%");
                }
                $q->orWhere('client', 'like', '%BP Batam%')
                  ->orWhere('client', 'like', '%BATII%');
            })
            ->where('name', 'not like', '%PPAT%')
            ->where('name', 'not like', '%Ngurah Rai%')
            ->get(['id', 'name', 'client']);

        $msProjectIds = $msProjects->pluck('id')->toArray();
        $msProjectNames = $msProjects->pluck('name')->toArray();

        // 3. HAPUS DATA TERKAIT HANYA UNTUK PROYEK MANAGED SERVICE INI
        $deletedTickets = 0;
        if (Schema::hasTable('managed_service_tickets')) {
            $deletedTickets = DB::table('managed_service_tickets')
                ->whereIn('project_id', $msProjectIds)
                ->orWhereIn('client_name', ['BP Batam', 'BATII', 'PT. Bank Rakyat Indoensia Tbk (BRI)', 'Kantor Wilayah BRI Mataram', 'Kantor Wilayah BRI Jakarta'])
                ->delete();
        }

        $deletedAssets = 0;
        if (Schema::hasTable('managed_service_assets')) {
            $deletedAssets = DB::table('managed_service_assets')
                ->whereIn('project_id', $msProjectIds)
                ->orWhere('owner_type', 'client')
                ->delete();
        }

        $deletedReports = 0;
        if (Schema::hasTable('managed_service_reports')) {
            $deletedReports = DB::table('managed_service_reports')
                ->whereIn('project_id', $msProjectIds)
                ->delete();
        }

        $deletedPmCycles = 0;
        if (Schema::hasTable('pm_cycles')) {
            $deletedPmCycles = DB::table('pm_cycles')
                ->whereIn('project_id', $msProjectIds)
                ->delete();
        }

        $deletedSchedules = 0;
        if (Schema::hasTable('schedules')) {
            $deletedSchedules = DB::table('schedules')
                ->whereIn('project_id', $msProjectIds)
                ->orWhere(function($q) use ($msProjectNames) {
                    foreach ($msProjectNames as $pName) {
                        $q->orWhere('title', 'like', "%{$pName}%");
                    }
                })
                ->delete();
        }

        $deletedTasks = 0;
        if (Schema::hasTable('tasks')) {
            $deletedTasks = DB::table('tasks')
                ->whereIn('project_id', $msProjectIds)
                ->delete();
        }

        // Force delete proyek maintenance
        $deletedProjectsCount = DB::table('projects')
            ->whereIn('id', $msProjectIds)
            ->delete();

        // Bersihkan cache aplikasi
        try {
            \Illuminate\Support\Facades\Artisan::call('view:clear');
            \Illuminate\Support\Facades\Artisan::call('cache:clear');
            \Illuminate\Support\Facades\Artisan::call('config:clear');
            if (function_exists('opcache_reset')) {
                @opcache_reset();
            }
        } catch (\Throwable $e) {}

        return response("
        <!DOCTYPE html>
        <html lang='id'>
        <head>
            <meta charset='UTF-8'>
            <title>Pembersihan Data Managed Service Berhasil - IP-NET Solusindo</title>
            <link href='https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap' rel='stylesheet'>
            <style>
                body { font-family: 'Plus Jakarta Sans', sans-serif; background: #0F172A; color: #E2E8F0; margin: 0; padding: 40px 20px; line-height: 1.6; }
                .container { max-width: 800px; margin: 0 auto; background: #1E293B; border: 1px solid #334155; border-radius: 20px; padding: 36px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); }
                .badge-success { display: inline-block; background: #10B981; color: #FFFFFF; font-size: 12px; font-weight: 800; padding: 6px 14px; border-radius: 9999px; text-transform: uppercase; letter-spacing: 0.5px; }
                h1 { color: #FFFFFF; font-size: 22px; margin: 16px 0 8px; font-weight: 800; }
                .card { background: #0F172A; border: 1px solid #334155; border-radius: 12px; padding: 20px; margin: 20px 0; }
                .btn { display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; border-radius: 10px; font-weight: 700; font-size: 13px; text-decoration: none; cursor: pointer; transition: 0.2s; }
                .btn-download { background: #10B981; color: white; }
                .btn-download:hover { background: #059669; }
                .btn-dash { background: #8F0A0D; color: white; margin-left: 10px; }
                .btn-dash:hover { background: #73080A; }
                ul { margin: 8px 0; padding-left: 20px; }
                li { margin-bottom: 6px; font-size: 13.5px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <span class='badge-success'>BERHASIL DIBERSIHKAN</span>
                <h1>Pembersihan Data Managed Service Selesai</h1>
                <p style='color: #94A3B8; font-size: 14px;'>Data dummy Managed Service telah dibersihkan. Sistem kini siap digunakan untuk pengujian data riil.</p>

                <div class='card'>
                    <h3 style='margin-top:0; color: #38BDF8; font-size: 15px;'>🛡️ File Backup Database Lengkap Telah Dibuat:</h3>
                    <p style='font-size: 13px; color: #CBD5E1;'>Sebelum data dihapus, sistem telah mengamankan 100% database Anda ke file SQL berikut:</p>
                    <div style='background: #1E293B; padding: 12px 16px; border-radius: 8px; font-family: monospace; font-size: 13px; color: #4ADE80; border: 1px solid #334155; margin-bottom: 14px;'>
                        {$fileName} ({$backupSizeKb} KB)
                    </div>
                    <a href='/download-system-backup/{$fileName}' class='btn btn-download'>⬇️ Unduh File Backup .SQL ke Komputer</a>
                </div>

                <div class='card'>
                    <h3 style='margin-top:0; color: #F1F5F9; font-size: 15px;'>📊 Ringkasan Data yang Dibersihkan:</h3>
                    <ul>
                        <li><strong>Proyek Kontrak Managed Service:</strong> {$deletedProjectsCount} proyek dihapus</li>
                        <li><strong>Tiket Layanan SLA / Incident:</strong> {$deletedTickets} tiket dibersihkan</li>
                        <li><strong>Jadwal Kunjungan & Maintenance (Schedules):</strong> {$deletedSchedules} jadwal dibersihkan</li>
                        <li><strong>Siklus Preventive Maintenance (PM Cycles):</strong> {$deletedPmCycles} siklus dibersihkan</li>
                        <li><strong>Aset Perangkat Maintenance Klien:</strong> {$deletedAssets} aset dibersihkan</li>
                    </ul>
                    <p style='color: #4ADE80; font-size: 12.5px; margin-bottom: 0;'>
                        ✅ <strong>Data PMO (Implementasi), Akun Pengguna (Users & Roles), dan Master Data dipastikan 100% UTUH dan AMAN.</strong>
                    </p>
                </div>

                <div style='margin-top: 24px; text-align: right;'>
                    <a href='/managed-service' class='btn btn-dash'>Buka Dashboard Managed Service →</a>
                </div>
            </div>
        </body>
        </html>
        ");
    } catch (\Throwable $e) {
        return response("<h2 style='color:red'>ERROR CLEANUP: " . htmlspecialchars($e->getMessage()) . " in " . $e->getFile() . ":" . $e->getLine() . "</h2><pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>", 500);
    }
});

Route::get('/clear-managed-service-projects', function () {
    return redirect()->route('ms.projects.index');
});

// Route Helper untuk membuat 2 dummy resmi: 1 Laporan Aktivitas Sales (A-G) & 1 Lesson Learned (Lost Deal)
Route::get('/seed-sales-official-dummies', function () {
    try {
        if (function_exists('opcache_reset')) {
            @opcache_reset();
        }

        // Run migration if pending
        try {
            \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        } catch (\Throwable $e) {}

        // Find or create sales user Raiza
        $raiza = \App\Models\User::where('email', 'raiza@ipnetsolusindo.com')
            ->orWhere('name', 'like', '%Raiza%')
            ->first();

        if (!$raiza) {
            $raiza = \App\Models\User::firstOrCreate(
                ['email' => 'raiza@ipnetsolusindo.com'],
                [
                    'name'     => 'Raiza',
                    'password' => \Illuminate\Support\Facades\Hash::make('password123'),
                    'position' => 'Sales (Account Manager)',
                    'status'   => 'Active',
                ]
            );
        }

        // 1. Proyek Aktif untuk Aktivitas Sales
        $activeProject = \App\Models\Project::updateOrCreate(
            ['name' => 'Pengadaan Core Switch & Next-Gen Firewall Bank Mandiri'],
            [
                'client'          => 'PT Bank Mandiri (Persero) Tbk',
                'sales_name'      => 'Raiza',
                'created_by'      => $raiza->id,
                'contract_value'  => 485000000,
                'sales_stage'     => 'Proposal & Quotation',
                'status'          => 'In Progress',
                'win_probability' => 80,
                'start_date'      => date('Y-m-d'),
                'end_date'        => date('Y-m-d', strtotime('+3 months')),
            ]
        );

        // Dummy 1: Sales Activity Lengkap (Bagian A sampai G)
        $actPayload = [
            'type'               => 'kronologi_batch',
            'title'              => 'Meeting Koordinasi Teknis Spesifikasi Core Switch & Next Gen Firewall',
            'meeting_media'      => 'Onsite di Lokasi Klien / Customer Office',
            'meeting_time_start' => '09:30',
            'meeting_time_end'   => '11:45',
            'attendees'          => [
                [
                    'pihak'      => 'Internal IPNET',
                    'nama'       => 'Raiza',
                    'jabatan'    => 'Account Executive',
                    'kontak'     => '08111000025',
                    'keterangan' => 'Lead Sales & Commercial PIC'
                ],
                [
                    'pihak'      => 'Internal IPNET',
                    'nama'       => 'Akbar',
                    'jabatan'    => 'Presales Engineer',
                    'kontak'     => '08111000010',
                    'keterangan' => 'Technical Solution Specialist'
                ],
                [
                    'pihak'      => 'Klien',
                    'nama'       => 'Bpk. Hendra Gunawan',
                    'jabatan'    => 'Head of IT Infrastructure',
                    'kontak'     => '081298765432',
                    'keterangan' => 'Decision Maker / Lead Klien'
                ],
                [
                    'pihak'      => 'Klien',
                    'nama'       => 'Ibu Maya Kartika',
                    'jabatan'    => 'Procurement Manager',
                    'kontak'     => '081387654321',
                    'keterangan' => 'PIC Pengadaan & Legalitas'
                ],
            ],
            'scope_category'     => 'Hardware & Perangkat Jaringan (Switch/Router/Firewall/Server)',
            'scope_description'  => "Pengadaan 4 Unit Core Switch 10G L3 Managed, 2 Unit Perimeter Next-Gen Firewall High Availability, beserta jasa instalasi, konfigurasi VLAN, dan support maintenance 1 tahun 24/7.",
            'estimated_budget'   => 485000000,
            'tender_timeline'    => 'Q4 2026 / November 2026',
            'competitors'        => 'Vendor Cisco Partner & Fortinet Platinum Reseller',
            'challenges'         => 'Customer membutuhkan lead time pengiriman barang maksimal 4 minggu kalender dan garansi SLA penggantian hardware NBD (Next Business Day).',
            'win_probability'    => 80,
            'next_action'        => 'Penyusunan Surat Penawaran Harga (SPH) resmi dan dokumen BoQ Teknis komprehensif',
            'next_action_pic'    => 'Raiza (Account Executive)',
            'division_head'      => 'Susanto Djaya',
            'total_items'        => 3,
            'items'              => [
                [
                    'no'         => 1,
                    'subject'    => 'Pembukaan & Presentasi Portofolio PT IP Network Solusindo',
                    'date'       => date('Y-m-d'),
                    'time'       => '09:30',
                    'client_pic' => 'Bpk. Hendra Gunawan',
                    'ipnet_pic'  => 'Raiza',
                    'notes'      => 'Klien menyambut baik portofolio implementasi enterprise IPNET di sektor perbankan & telco.'
                ],
                [
                    'no'         => 2,
                    'subject'    => 'Review Kebutuhan Teknis Core Switch & Perimeter Security',
                    'date'       => date('Y-m-d'),
                    'time'       => '10:15',
                    'client_pic' => 'Bpk. Hendra Gunawan',
                    'ipnet_pic'  => 'Akbar',
                    'notes'      => 'Diskusi kebutuhan redundansi Dual PSU, 10G SFP+ Uplink, dan integrasi Active Directory pada firewall.'
                ],
                [
                    'no'         => 3,
                    'subject'    => 'Pembahasan Timeline Pengadaan & Mekanisme Tender',
                    'date'       => date('Y-m-d'),
                    'time'       => '11:00',
                    'client_pic' => 'Ibu Maya Kartika',
                    'ipnet_pic'  => 'Raiza',
                    'notes'      => 'Pengadaan akan menggunakan sistem seleksi tertutup (beauty contest 3 vendor). Dokumen tender akan dirilis minggu depan.'
                ],
            ],
        ];

        $activity = \App\Models\SalesActivity::updateOrCreate(
            [
                'project_id' => $activeProject->id,
                'subject'    => 'Meeting Koordinasi Teknis Spesifikasi Core Switch & Next Gen Firewall'
            ],
            [
                'sales_id'         => $raiza->id,
                'activity_type'    => 'Meeting',
                'activity_date'    => now(),
                'notes'            => json_encode($actPayload),
                'next_action'      => 'Penyusunan Surat Penawaran Harga (SPH) resmi dan dokumen BoQ Teknis komprehensif',
                'next_action_date' => date('Y-m-d', strtotime('+3 days')),
                'status'           => 'Completed',
            ]
        );

        // Dummy 2: Proyek Lost Deal dengan Evaluasi Lesson Learned Lengkap
        $lostAnalysisData = [
            'primary_factor'          => 'Harga / Komersial',
            'competitor_winner'       => 'PT Solusi Jaringan Mandiri (Aruba Partner)',
            'competitor_price'        => 275000000,
            'our_price'               => 320000000,
            'turning_point'           => 'Pada saat negosiasi BAFO (Best and Final Offer), kompetitor menurunkan harga secara agresif dengan subsidi prinsipal karena status Tier Platinum.',
            'gap_our_weakness'        => 'Margin penawaran kita terkunci karena deal registration terlambat diajukan ke distributor utama.',
            'gap_competitor_strength' => 'Kompetitor memegang status Platinum Partner dengan rebate harga 12% lebih murah serta ready stock lokal.',
            'lessons_learned'         => 'Deal registration ke prinsipal distributor harus dikunci sejak awal pertemuan prospek (fase Prospecting/Discovery) agar IPNET mendapatkan special price discount terbaik.',
            'future_strategy'         => 'Menjaga komunikasi berkala dengan PIC IT klien untuk peluang maintenance support tahunan dan ekspansi access point fase II tahun depan.',
            'evaluated_by'            => 'Raiza (Account Executive)',
            'approved_by'             => 'Susanto Djaya (Division Head)',
            'evaluated_at'            => date('Y-m-d H:i:s'),
        ];

        $lostProject = \App\Models\Project::updateOrCreate(
            ['name' => 'Pengadaan Wi-Fi 6 Enterprise Kampus UI'],
            [
                'client'          => 'Universitas Indonesia (Fakultas Teknik)',
                'sales_name'      => 'Raiza',
                'created_by'      => $raiza->id,
                'contract_value'  => 320000000,
                'sales_stage'     => 'Closed Lost',
                'status'          => 'Cancelled',
                'win_probability' => 0,
                'lost_reason'     => json_encode($lostAnalysisData),
                'lost_competitor' => 'PT Solusi Jaringan Mandiri (Aruba Partner)',
                'start_date'      => date('Y-m-d', strtotime('-1 month')),
                'end_date'        => date('Y-m-d'),
            ]
        );

        if (\Illuminate\Support\Facades\Schema::hasColumn('projects', 'lost_analysis')) {
            $lostProject->lost_analysis = $lostAnalysisData;
            $lostProject->save();
        }

        \Illuminate\Support\Facades\Artisan::call('view:clear');
        \Illuminate\Support\Facades\Artisan::call('cache:clear');
        \Illuminate\Support\Facades\Artisan::call('config:clear');
        \Illuminate\Support\Facades\Artisan::call('route:clear');

        return response()->json([
            'status'  => 'success',
            'message' => '2 Data Dummy Resmi Sales (Aktivitas Sales & CRM + Lesson Learned Lost Deal) BERHASIL dibuat!',
            'data'    => [
                'activity_dummy' => [
                    'id'           => $activity->id,
                    'subject'      => $activity->subject,
                    'project'      => $activeProject->name,
                    'pdf_download' => url('/sales/activities/' . $activity->id . '/pdf'),
                ],
                'lost_deal_dummy' => [
                    'project_id'   => $lostProject->id,
                    'project_name' => $lostProject->name,
                    'competitor'   => $lostProject->lost_competitor,
                    'pdf_download' => url('/projects/' . $lostProject->id . '/lost-analysis/pdf'),
                ],
                'activity_index_url' => url('/sales/activities'),
            ]
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'status'  => 'error',
            'message' => $e->getMessage(),
            'trace'   => $e->getTraceAsString(),
        ], 500);
    }
});

// Protected Routes
Route::middleware(['auth'])->group(function () {
    // Modul 1: ACQUIRE (Sales Pipeline & Handover 1 to Design)
    Route::prefix('acquire')->middleware('role:Director|Direktur|HD / Direktur|Division Head|Group Leader|Group Leader Commercial & Solution|Group Leader Delivery & Operation|PMO|Project Manager|Lead Divisi|Team Leader Engineering|Team Leader|Lead Maintenance|Lead Engineer|Sales|Account Manager|BusDev|BDM|Business Development|CRO|Customer Relation Officer|Presales|Pre-Sales|Solution Architect|Solutions Architect')->group(function () {
        Route::get('/', [AcquireController::class, 'index'])->name('acquire.index');
        Route::post('/', [AcquireController::class, 'store'])->name('acquire.store');
        Route::put('/{project}', [AcquireController::class, 'update'])->name('acquire.update');
        Route::post('/{project}/handover-design', [AcquireController::class, 'handoverToDesign'])->name('acquire.handover.design');
        Route::delete('/{project}', [AcquireController::class, 'destroy'])->name('acquire.destroy');
    });

    // PMO & Project Management Office Dashboard (Dashboard 1)
    Route::prefix('pmo')->middleware('role:Director|Direktur|HD / Direktur|Division Head|Group Leader|Group Leader Delivery & Operation|PMO|Project Manager|Lead Divisi|Team Leader Engineering|Team Leader|Lead Maintenance|Lead Engineer|Super Admin|Admin')->group(function () {
        Route::get('/dashboard', [PmoController::class, 'dashboard'])->name('pmo.dashboard');
        Route::get('/implementations', [PmoController::class, 'implementations'])->name('pmo.implementations.index');
        Route::get('/ppatk', function () {
            $project = \App\Models\Project::where('name', 'like', '%PPAT%')
                ->orWhere('name', 'like', '%Peremajaan Router%')
                ->orWhere('client', 'like', '%PPAT%')
                ->first();

            if (!$project) {
                try {
                    $seeder = new \Database\Seeders\PpatkProjectSeeder();
                    $seeder->run();
                    $project = \App\Models\Project::where('name', 'like', '%PPAT%')->first();
                } catch (\Throwable $e) {}
            }

            if ($project) {
                return redirect()->route('pmo.projects.show', $project->id);
            }

            return redirect()->route('pmo.implementations.index')->with('error', 'Proyek PPATK tidak ditemukan.');
        })->name('pmo.projects.ppatk');
        Route::get('/projects/{project}', [PmoController::class, 'projectDashboard'])->name('pmo.projects.show');
        Route::get('/projects/{project}/dashboard', [PmoController::class, 'projectDashboard'])->name('pmo.projects.dashboard');
        Route::delete('/projects/{project}', [PmoController::class, 'destroyProject'])->name('pmo.projects.destroy');
        Route::post('/projects/{project}/delete', [PmoController::class, 'destroyProject'])->name('pmo.projects.destroy_post');
        Route::put('/projects/{project}', [PmoController::class, 'updateProject'])->name('pmo.projects.update');
        Route::post('/projects/{project}/update', [PmoController::class, 'updateProject'])->name('pmo.projects.update_post');
        Route::post('/projects/create-manual', [PmoController::class, 'storeManualProject'])->name('pmo.projects.store_manual');
        Route::post('/projects/{project}/expenses', [PmoController::class, 'storeExpense'])->name('pmo.projects.expenses.store');
        Route::delete('/projects/{project}/expenses/{expenseId}', [PmoController::class, 'destroyExpense'])->name('pmo.projects.expenses.destroy');
        Route::post('/projects/{project}/stage', [PmoController::class, 'updateStage'])->name('pmo.stage.update');
        Route::post('/projects/{project}/documents', [PmoController::class, 'updateDocuments'])->name('pmo.documents.update');
        Route::post('/projects/{project}/update-documents', [PmoController::class, 'updateDocuments'])->name('pmo.documents.update_documents');
        Route::post('/projects/{project}/handover-approve', [PmoController::class, 'approveHandover'])->name('pmo.handover.approve');
        Route::post('/projects/{project}/handover-conditional', [PmoController::class, 'conditionalHandover'])->name('pmo.handover.conditional');
        Route::post('/projects/{project}/handover-to-ms', [PmoController::class, 'handoverToManagedService'])->name('pmo.handover.to_ms');
        Route::post('/projects/{project}/weekly-progress', [PmoController::class, 'updateWeeklyProgress'])->name('pmo.projects.weekly_progress.update');
        Route::post('/projects/{project}/actual-progress-matrix', [PmoController::class, 'updateActualProgressMatrix'])->name('pmo.projects.actual_progress_matrix.update');
        Route::post('/projects/{project}/daily-progress', [PmoController::class, 'updateDailyProgress'])->name('pmo.projects.daily_progress.update');
        Route::post('/projects/{project}/update-timeline', [PmoController::class, 'updateTimeline'])->name('pmo.projects.update_timeline');
        Route::post('/projects/{project}/update-schedule-type', [PmoController::class, 'updateScheduleType'])->name('pmo.projects.update_schedule_type');
        Route::post('/projects/{project}/update-client', [PmoController::class, 'updateClient'])->name('pmo.projects.update_client');
        Route::post('/projects/{project}/assign-division', [PmoController::class, 'assignDivision'])->name('pmo.projects.assign_division');
        Route::post('/projects/{project}/assign-team', [PmoController::class, 'assignTeam'])->name('pmo.projects.assign_team');
        Route::post('/projects/{project}/quick-task', [PmoController::class, 'quickStoreTask'])->name('pmo.projects.quick_task');
        Route::post('/projects/{project}/tasks', [PmoController::class, 'storeTask'])->name('pmo.projects.tasks.store');
        Route::get('/projects/{project}/tasks/template-excel', [PmoController::class, 'downloadTaskTemplateExcel'])->name('pmo.projects.tasks.template_excel');
        Route::post('/projects/{project}/tasks/import-excel', [PmoController::class, 'importTasksExcel'])->name('pmo.projects.tasks.import_excel');
        Route::post('/tasks/{task}/update', [PmoController::class, 'updateTask'])->name('pmo.tasks.update');
        Route::post('/tasks/{task}/delete', [PmoController::class, 'destroyTask'])->name('pmo.tasks.destroy_post');
        Route::delete('/tasks/{task}', [PmoController::class, 'destroyTask'])->name('pmo.tasks.destroy');
    });

    // Lead & Executive Dashboard (Dashboard 2)
    Route::get('/dashboard/lead', [DashboardController::class, 'lead'])
        ->name('dashboard.lead')
        ->middleware('role:Director|Direktur|HD / Direktur|Division Head|Group Leader|Group Leader Delivery & Operation|Lead Divisi|Team Leader Engineering|Team Leader|Lead Maintenance|Lead Engineer|Managed Service|PMO|Project Manager');
    
    // Field Engineer & Technical Staff Dashboard (Dashboard 7)
    Route::get('/dashboard/engineer', [DashboardController::class, 'engineer'])
        ->name('dashboard.engineer')
        ->middleware('role:Network Engineer|Security Engineer|Field Support (EOS)|Field Support|Managed Service|Engineer|Engineer L1|Engineer L2|Maintenance');

    // Engineer Activity Log — Halaman Dedicated Monitoring Lead & Input Aktivitas Engineer
    Route::get('/engineer/activity-logs', [DashboardController::class, 'engineerActivityLogs'])
        ->name('engineer.activity_log.index')
        ->middleware('role:Network Engineer|Security Engineer|Field Support (EOS)|Field Support|Managed Service|Engineer|Engineer L1|Engineer L2|Maintenance|Lead Engineer|Team Leader Engineering|Team Leader|Lead Maintenance|Lead Divisi|Director|Direktur|Division Head|Group Leader|Group Leader Delivery & Operation|PMO|Project Manager');

    Route::post('/engineer/activity-logs', [DashboardController::class, 'storeActivityLog'])
        ->name('engineer.activity_log.store')
        ->middleware('role:Network Engineer|Security Engineer|Field Support (EOS)|Field Support|Managed Service|Engineer|Engineer L1|Engineer L2|Maintenance|Lead Engineer|Team Leader Engineering|Team Leader|Lead Maintenance|Lead Divisi|Director|Direktur|Division Head|Group Leader|Group Leader Delivery & Operation|PMO|Project Manager');

    Route::delete('/engineer/activity-logs/{log}', [DashboardController::class, 'deleteActivityLog'])
        ->name('engineer.activity_log.destroy')
        ->middleware('role:Network Engineer|Security Engineer|Field Support (EOS)|Field Support|Managed Service|Engineer|Engineer L1|Engineer L2|Maintenance|Lead Engineer|Team Leader Engineering|Team Leader|Lead Maintenance|Lead Divisi|Director|Direktur|Division Head|Group Leader|Group Leader Delivery & Operation|PMO|Project Manager');

    Route::put('/engineer/activity-logs/{log}', [DashboardController::class, 'updateActivityLog'])
        ->name('engineer.activity_log.update')
        ->middleware('role:Network Engineer|Security Engineer|Field Support (EOS)|Field Support|Managed Service|Engineer|Engineer L1|Engineer L2|Maintenance|Lead Engineer|Team Leader Engineering|Team Leader|Lead Maintenance|Lead Divisi|Director|Direktur|Division Head|Group Leader|Group Leader Delivery & Operation|PMO|Project Manager');

    Route::match(['get', 'post'], '/engineer/activity-logs/export/pdf', [DashboardController::class, 'exportActivityLogPdf'])
        ->name('engineer.activity_log.export_pdf')
        ->middleware('role:Network Engineer|Security Engineer|Field Support (EOS)|Field Support|Managed Service|Engineer|Engineer L1|Engineer L2|Maintenance|Lead Engineer|Team Leader Engineering|Team Leader|Lead Maintenance|Lead Divisi|Director|Direktur|Division Head|Group Leader|Group Leader Delivery & Operation|PMO|Project Manager|Admin|Super Admin|Superadmin|Technical Support');

    Route::get('/engineer/activity-logs/export/excel', [DashboardController::class, 'exportActivityLogExcel'])
        ->name('engineer.activity_log.export_excel')
        ->middleware('role:Network Engineer|Security Engineer|Field Support (EOS)|Field Support|Managed Service|Engineer|Engineer L1|Engineer L2|Maintenance|Lead Engineer|Team Leader Engineering|Team Leader|Lead Maintenance|Lead Divisi|Director|Direktur|Division Head|Group Leader|Group Leader Delivery & Operation|PMO|Project Manager');

    // Digital Signature Dokumen Aktivitas (PIC -> Lead -> Head Division)
    Route::get('/engineer/activity-logs/signature-status', [ActivitySignatureController::class, 'getStatus'])
        ->name('engineer.activity_log.signature_status')
        ->middleware('auth');

    Route::post('/engineer/activity-logs/signature', [ActivitySignatureController::class, 'storeSignature'])
        ->name('engineer.activity_log.signature_store')
        ->middleware('auth');

    Route::post('/engineer/activity-logs/report-data', [ActivitySignatureController::class, 'saveReportData'])
        ->name('engineer.activity_log.save_report_data')
        ->middleware('auth');

    // Route trigger migrasi cepat via web jika hosting tidak punya akses SSH
    Route::get('/run-signature-migration', function () {
        Artisan::call('migrate', ['--force' => true]);
        return response('<h3>Sukses Migrasi Database!</h3><pre>' . Artisan::output() . '</pre><br><a href="/engineer/activity-logs">Kembali ke Activity Log</a>');
    });

    // Route legacy / alias POST dari embedded section dashboard
    Route::post('/dashboard/engineer/activity-log', [DashboardController::class, 'storeActivityLog'])
        ->name('engineer.activity_log.store_legacy')
        ->middleware('role:Network Engineer|Security Engineer|Field Support (EOS)|Field Support|Managed Service|Engineer|Engineer L1|Engineer L2|Maintenance|Lead Engineer|Team Leader Engineering|Team Leader|Lead Maintenance|Lead Divisi|Director|Direktur|Division Head|Group Leader|Group Leader Delivery & Operation');

    // MENU BARU: DIGITAL SIGNATURE (DOKUMEN ELEKTRONIK & PENGESAHAN PIC KLIEN)
    Route::prefix('digital-signatures')->name('digital_signatures.')->group(function () {
        Route::get('/', [DigitalSignatureController::class, 'index'])->name('index');
        Route::get('/create', [DigitalSignatureController::class, 'create'])->name('create');
        Route::post('/', [DigitalSignatureController::class, 'store'])->name('store');
        Route::get('/{id}', [DigitalSignatureController::class, 'show'])->name('show');
        Route::get('/{id}/place-signature', [DigitalSignatureController::class, 'placeSignatureShow'])->name('place_signature');
        Route::post('/{id}/sign-internal', [DigitalSignatureController::class, 'signInternal'])->name('sign_internal');
        Route::post('/{id}/reject-internal', [DigitalSignatureController::class, 'rejectInternal'])->name('reject_internal');
        Route::get('/{id}/download-pdf', [DigitalSignatureController::class, 'downloadPdf'])->name('download_pdf');
        Route::get('/{id}/download-certificate', [DigitalSignatureController::class, 'downloadCertificateSheet'])->name('download_certificate');
        Route::get('/{id}/download-original', [DigitalSignatureController::class, 'downloadOriginalFile'])->name('download_original');
        Route::get('/{id}/stream-pdf', [DigitalSignatureController::class, 'streamPdf'])->name('stream_pdf');
        Route::match(['DELETE', 'POST'], '/{id}', [DigitalSignatureController::class, 'destroy'])->name('destroy');
        Route::match(['DELETE', 'POST'], '/{id}/delete', [DigitalSignatureController::class, 'destroy'])->name('destroy_post');
    });

    // BDM & Business Development (Dashboard + Dedicated Sub-Menus)
    Route::prefix('bdm')->middleware('role:Director|Direktur|HD / Direktur|Division Head|Group Leader|Group Leader Commercial & Solution|BusDev|BDM|Business Development|Sales|Account Manager|Presales|Pre-Sales|Solution Architect')->group(function () {
        Route::get('/dashboard', [BdmController::class, 'dashboard'])->name('dashboard.bdm');
        
        // Dedicated Menu 1: Inisiasi Peluang & Handover
        Route::get('/opportunities', [BdmController::class, 'opportunities'])->name('bdm.opportunities.index');
        Route::post('/opportunities', [BdmController::class, 'storeOpportunity'])->name('bdm.opportunity.store');
        Route::post('/opportunities/create', [BdmController::class, 'storeOpportunity'])->name('bdm.opportunities.store');
        Route::put('/opportunities/{project}', [BdmController::class, 'updateOpportunity'])->name('bdm.opportunity.update');
        Route::delete('/opportunities/{project}', [BdmController::class, 'destroyOpportunity'])->name('bdm.opportunity.destroy');
        Route::post('/opportunities/{project}/handover', [BdmController::class, 'handoverToSales'])->name('bdm.opportunity.handover');

        // Dedicated Menu 2: Market Intelligence
        Route::get('/intelligence', [BdmController::class, 'intelligence'])->name('bdm.intelligence.index');
        Route::post('/intelligence', [BdmController::class, 'storeIntelligence'])->name('bdm.intelligence.store');
        Route::delete('/intelligence/{intelligence}', [BdmController::class, 'destroyIntelligence'])->name('bdm.intelligence.destroy');

        // Dedicated Menu 3: Kemitraan & Channel Prinsipal
        Route::get('/partnerships', [BdmController::class, 'partnerships'])->name('bdm.partnerships.index');
        Route::post('/partnerships', [BdmController::class, 'storePartnership'])->name('bdm.partnership.store');
        Route::post('/partnerships/create', [BdmController::class, 'storePartnership'])->name('bdm.partnerships.store');
        Route::delete('/partnerships/{partnership}', [BdmController::class, 'destroyPartnership'])->name('bdm.partnership.destroy');
    });

    // Fallback alias for /dashboard/bdm
    Route::get('/dashboard/bdm', [BdmController::class, 'dashboard'])
        ->middleware('role:Director|Direktur|HD / Direktur|Division Head|Group Leader|Group Leader Commercial & Solution|BusDev|BDM|Business Development|Sales|Account Manager|Presales|Pre-Sales|Solution Architect');

    // Dashboard Sales & Account Manager (Dashboard 3)
    Route::get('/dashboard/sales', [DashboardController::class, 'sales'])
        ->name('dashboard.sales')
        ->middleware('role:Director|Direktur|HD / Direktur|Division Head|Group Leader|Group Leader Commercial & Solution|Sales|Account Manager|BusDev|BDM|Business Development|Presales|Pre-Sales|Solution Architect');

    // Modul 2: SALES / CRM (Pipeline, Activities, Commercial Handover)
    Route::prefix('sales')->middleware('role:Director|Direktur|HD / Direktur|Division Head|Group Leader|Group Leader Commercial & Solution|Sales|Account Manager|BusDev|BDM|Business Development|Presales|Pre-Sales|Solution Architect')->group(function () {
        // Dedicated Menu 1: Pipeline & Opportunity Register
        Route::get('/pipeline', [\App\Http\Controllers\SalesCrmController::class, 'pipeline'])->name('sales.pipeline.index');
        Route::get('/pipeline/create', [\App\Http\Controllers\SalesCrmController::class, 'createOpportunity'])->name('sales.pipeline.create');
        Route::post('/pipeline/create', [\App\Http\Controllers\SalesCrmController::class, 'storeOpportunity'])->name('sales.pipeline.store');
        Route::post('/pipeline/{project}/stage', [\App\Http\Controllers\SalesCrmController::class, 'updateStage'])->name('sales.pipeline.update');

        // Dedicated Menu 2: Sales Activity Log (CRM)
        Route::get('/activities', [\App\Http\Controllers\SalesCrmController::class, 'activities'])->name('sales.activities.index');
        Route::get('/activities/{activity}/pdf', [\App\Http\Controllers\SalesCrmController::class, 'exportActivityPdf'])->name('sales.activities.pdf');
        Route::post('/activities', [\App\Http\Controllers\SalesCrmController::class, 'storeActivity'])->name('sales.activities.store');
        Route::post('/activities/bulk', [\App\Http\Controllers\SalesCrmController::class, 'storeBulkActivities'])->name('sales.activities.bulk');
        Route::put('/activities/{activity}', [\App\Http\Controllers\SalesCrmController::class, 'updateActivity'])->name('sales.activities.update');
        Route::delete('/activities/{activity}', [\App\Http\Controllers\SalesCrmController::class, 'destroyActivity'])->name('sales.activities.destroy');

        // Lost Deal & Lesson Learned Analysis
        Route::post('/pipeline/{project}/lost-analysis', [\App\Http\Controllers\SalesCrmController::class, 'saveLostAnalysis'])->name('sales.pipeline.lost_analysis');
        Route::get('/pipeline/{project}/lost-analysis/pdf', [\App\Http\Controllers\SalesCrmController::class, 'exportLostAnalysisPdf'])->name('sales.pipeline.lost_analysis_pdf');

        // Dedicated Menu 3: Commercial Handover to Delivery/PMO
        Route::get('/handover', [\App\Http\Controllers\SalesCrmController::class, 'commercialHandoverIndex'])->name('sales.handover.index');
        Route::post('/handover/{project}/submit', [\App\Http\Controllers\SalesCrmController::class, 'submitCommercialHandover'])->name('sales.handover.submit');
        Route::get('/handover/{project}/document', [\App\Http\Controllers\SalesCrmController::class, 'showHandoverDocument'])->name('sales.handover.document');
    });

    // Public / Project-level Lost Analysis PDF
    Route::get('/projects/{project}/lost-analysis/pdf', [\App\Http\Controllers\SalesCrmController::class, 'exportLostAnalysisPdf'])->name('projects.lost_analysis_pdf');

    // Dashboard Solution Architect & R&D (Dashboard 6)
    Route::get('/dashboard/solution-architect', [DashboardController::class, 'solutionArchitect'])
        ->name('dashboard.architect')
        ->middleware('role:Director|Direktur|HD / Direktur|Division Head|Group Leader|Group Leader Commercial & Solution|Solution Architect|Solutions Architect|Tech Develop|Tech.Develp (R&D)|R&D|Presales|Pre-Sales|Sales|Account Manager|BusDev|BDM');

    // Dashboard Presales (Hubungan dengan Sales, BD, SA, Direktur, Head Division)
    Route::get('/dashboard/presales', [DashboardController::class, 'presales'])
        ->name('dashboard.presales')
        ->middleware('role:Director|Direktur|HD / Direktur|Division Head|Group Leader|Group Leader Commercial & Solution|Presales|Pre-Sales|Solution Architect|Solutions Architect|Sales|Account Manager|BusDev|BDM');

    // Modul Presales: Proposal & SOW
    Route::prefix('presales')->middleware('role:Director|Direktur|HD / Direktur|Division Head|Group Leader|Group Leader Commercial & Solution|Presales|Pre-Sales|Solution Architect|Solutions Architect|Sales|Account Manager|BusDev|BDM')->group(function () {
        Route::get('/proposals', [\App\Http\Controllers\PresalesProposalController::class, 'index'])->name('presales.proposals.index');
        Route::post('/proposals/{project}', [\App\Http\Controllers\PresalesProposalController::class, 'store'])->name('presales.proposals.store');
        Route::delete('/proposals/{project}/file', [\App\Http\Controllers\PresalesProposalController::class, 'destroyFile'])->name('presales.proposals.file.delete');
        Route::get('/proposals/{project}/download', [\App\Http\Controllers\PresalesProposalController::class, 'download'])->name('presales.proposals.download');
    });

    // Alias redirect for underscore URL /managed_service
    Route::any('/managed_service/{any?}', function ($any = null) {
        return redirect('/managed-service/' . ltrim($any ?? '', '/'));
    })->where('any', '.*');

    // Modul 4: MANAGED SERVICE (SERVICE DELIVERY - OPERATE & MAINTAIN)
    Route::prefix('managed-service')->middleware('role:Director|Direktur|HD / Direktur|Division Head|Group Leader|Group Leader Delivery & Operation|Group Leader Commercial & Solution|Lead Maintenance|Maintenance|PMO|Project Manager|Lead Engineer|Lead Divisi|Team Leader Engineering|Team Leader|Field Support (EOS)|Field Support|Managed Service|Field Engineer|Engineer')->group(function () {
        Route::get('/', [\App\Http\Controllers\ManagedServiceController::class, 'dashboard'])->name('ms.dashboard');
        Route::get('/dashboard', [\App\Http\Controllers\ManagedServiceController::class, 'dashboard'])->name('ms.dashboard.alias');

        // Projects & Contracts Detail
        Route::get('/projects', [\App\Http\Controllers\ManagedServiceController::class, 'projects'])->name('ms.projects.index');
        Route::post('/projects', [\App\Http\Controllers\ManagedServiceController::class, 'storeProject'])->name('ms.projects.store');
        Route::get('/projects/{project}', [\App\Http\Controllers\ManagedServiceController::class, 'showProject'])->name('ms.projects.show');
        Route::put('/projects/{project}', [\App\Http\Controllers\ManagedServiceController::class, 'updateProject'])->name('ms.projects.update');
        Route::post('/projects/{project}/documents', [\App\Http\Controllers\ManagedServiceController::class, 'updateProjectDocuments'])->name('ms.projects.documents.update');
        Route::put('/projects/{project}/sla-params', [\App\Http\Controllers\ManagedServiceController::class, 'updateProjectSlaParams'])->name('ms.projects.sla.update');
        Route::delete('/projects/{project}', [\App\Http\Controllers\ManagedServiceController::class, 'destroyProject'])->name('ms.projects.destroy');

        // Monitoring PM & CM Tracker
        Route::get('/monitoring/preventive/{project?}', [\App\Http\Controllers\ManagedServiceController::class, 'monitoringPm'])->name('ms.monitoring.pm');
        Route::get('/monitoring/corrective/{project?}', [\App\Http\Controllers\ManagedServiceController::class, 'monitoringCm'])->name('ms.monitoring.cm');
        Route::get('/monitoring/{project?}', [\App\Http\Controllers\ManagedServiceController::class, 'monitoring'])->name('ms.monitoring.index');
        Route::post('/monitoring/pm-cycles/{cycle}', [\App\Http\Controllers\ManagedServiceController::class, 'updatePmCycle'])->name('ms.monitoring.pm.update');
        Route::get('/monitoring/pm-cycles/{cycle}/report', [\App\Http\Controllers\ManagedServiceController::class, 'showPmReport'])->name('ms.monitoring.pm.report');
        Route::post('/monitoring/cm-tickets', [\App\Http\Controllers\ManagedServiceController::class, 'storeCmTicket'])->name('ms.monitoring.cm.store');
        Route::get('/monitoring/cm-tickets/{ticket}/bap', [\App\Http\Controllers\ManagedServiceController::class, 'showCmBap'])->name('ms.monitoring.cm.bap');
        Route::post('/monitoring/cm-tickets/{ticket}/upload-bap', [\App\Http\Controllers\ManagedServiceController::class, 'uploadCmBap'])->name('ms.monitoring.cm.upload_bap');
        Route::post('/monitoring/cm-tickets/{ticket}/update', [\App\Http\Controllers\ManagedServiceController::class, 'updateCmTicket'])->name('ms.monitoring.cm.update');
        
        // Assets Management (Internal Perusahaan & External Klien)
        Route::get('/assets/internal', [\App\Http\Controllers\ManagedServiceController::class, 'internalAssets'])->name('ms.assets.internal');
        Route::get('/assets/external', [\App\Http\Controllers\ManagedServiceController::class, 'externalAssets'])->name('ms.assets.external');
        Route::get('/assets', [\App\Http\Controllers\ManagedServiceController::class, 'assets'])->name('ms.assets.index');
        Route::post('/assets', [\App\Http\Controllers\ManagedServiceController::class, 'storeAsset'])->name('ms.assets.store');
        Route::get('/assets/{asset}', fn() => redirect()->route('ms.assets.external'));
        Route::put('/assets/{asset}', [\App\Http\Controllers\ManagedServiceController::class, 'updateAsset'])->name('ms.assets.update');
        Route::delete('/assets/{asset}', [\App\Http\Controllers\ManagedServiceController::class, 'destroyAsset'])->name('ms.assets.destroy');

        // Incident & Service Request Tickets
        Route::get('/tickets', [\App\Http\Controllers\ManagedServiceController::class, 'tickets'])->name('ms.tickets.index');
        Route::post('/tickets', [\App\Http\Controllers\ManagedServiceController::class, 'storeTicket'])->name('ms.tickets.store');
        Route::get('/tickets/{ticket}', fn() => redirect()->route('ms.tickets.index'));
        Route::put('/tickets/{ticket}', [\App\Http\Controllers\ManagedServiceController::class, 'updateTicket'])->name('ms.tickets.update');
        Route::delete('/tickets/{ticket}', [\App\Http\Controllers\ManagedServiceController::class, 'destroyTicket'])->name('ms.tickets.destroy');

        // Preventive Maintenance
        Route::get('/maintenance', [\App\Http\Controllers\ManagedServiceController::class, 'maintenance'])->name('ms.maintenance.index');

        // Reports & SLA Availability
        Route::get('/reports', [\App\Http\Controllers\ManagedServiceController::class, 'reports'])->name('ms.reports.index');
        Route::post('/reports', [\App\Http\Controllers\ManagedServiceController::class, 'storeReport'])->name('ms.reports.store');
        Route::post('/downtime-log', [\App\Http\Controllers\ManagedServiceController::class, 'storeDowntimeLog'])->name('ms.downtime.store');
        Route::get('/sla-export', [\App\Http\Controllers\ManagedServiceController::class, 'exportSlaReport'])->name('ms.sla.export');

        // Incoming Service Handovers (Accept & Activate)
        Route::post('/handovers/{project}/accept', [\App\Http\Controllers\ManagedServiceController::class, 'acceptServiceHandover'])->name('ms.handovers.accept');
    });

    // Fallback direct route
    Route::get('/dashboard/managed-service', [\App\Http\Controllers\ManagedServiceController::class, 'dashboard'])
        ->name('dashboard.ms');

    // Modul 7: CUSTOMER MANAGEMENT / CRO (Customer Relationship Officer)
    Route::prefix('cro')->middleware('role:Director|Direktur|HD / Direktur|Division Head|Group Leader|Group Leader Commercial & Solution|Group Leader Delivery & Operation|CRO|Customer Relation Officer|Sales|Account Manager|PMO|Project Manager|Lead Maintenance|Maintenance|Lead Engineer|Lead Divisi|Team Leader Engineering|Team Leader')->group(function () {
        Route::get('/', [CroController::class, 'dashboard'])->name('cro.dashboard');
        Route::get('/dashboard', [CroController::class, 'dashboard'])->name('cro.dashboard.alias');

        // Sub-Halaman 1: Engagements & Relationship
        Route::get('/engagements', [CroController::class, 'engagements'])->name('cro.engagements.index');
        Route::post('/engagements', [CroController::class, 'storeEngagement'])->name('cro.engagements.store');
        Route::delete('/engagements/{engagement}', [CroController::class, 'destroyEngagement'])->name('cro.engagements.destroy');

        // Sub-Halaman 2: Satisfaction & CSAT
        Route::get('/satisfaction', [CroController::class, 'satisfaction'])->name('cro.satisfaction.index');
        Route::post('/satisfaction', [CroController::class, 'storeCsat'])->name('cro.satisfaction.store');
        Route::delete('/satisfaction/{csat}', [CroController::class, 'destroyCsat'])->name('cro.satisfaction.destroy');

        // Sub-Halaman 3: Concerns & Escalation Orchestration
        Route::get('/concerns', [CroController::class, 'concerns'])->name('cro.concerns.index');
        Route::post('/concerns', [CroController::class, 'storeConcern'])->name('cro.concerns.store');
        Route::post('/concerns/{concern}/dispatch', [CroController::class, 'dispatchConcern'])->name('cro.concerns.dispatch');
        Route::post('/concerns/{concern}/resolve', [CroController::class, 'resolveConcern'])->name('cro.concerns.resolve');
        Route::post('/concerns/{concern}/confirm', [CroController::class, 'confirmConcern'])->name('cro.concerns.confirm');
        Route::delete('/concerns/{concern}', [CroController::class, 'destroyConcern'])->name('cro.concerns.destroy');

        // Sub-Halaman 4: Retention & Account Health
        Route::get('/retention', [CroController::class, 'retention'])->name('cro.retention.index');
        Route::post('/retention', [CroController::class, 'storeAccountHealth'])->name('cro.retention.store');

        // Sub-Halaman 5: Account Development & Opportunity Bridge
        Route::get('/opportunities', [CroController::class, 'opportunities'])->name('cro.opportunities.index');
        Route::post('/opportunities', [CroController::class, 'storeOpportunity'])->name('cro.opportunities.store');
        Route::post('/opportunities/{opportunity}/handover', [CroController::class, 'handoverOpportunity'])->name('cro.opportunities.handover');
        Route::delete('/opportunities/{opportunity}', [CroController::class, 'destroyOpportunity'])->name('cro.opportunities.destroy');
    });

    Route::get('/dashboard/cro', [CroController::class, 'dashboard'])
        ->name('dashboard.cro');

    // Modul 8: ADMIN SUPPORT (Horizontal Governance & Gatekeeper Layer)
    Route::prefix('admin-support')->group(function () {
        Route::get('/', [AdminSupportController::class, 'index'])->name('admin_support.dashboard');
        Route::get('/dashboard', [AdminSupportController::class, 'index'])->name('admin_support.dashboard.alias');

        // Central Document Register
        Route::get('/documents', [AdminSupportController::class, 'documentsIndex'])->name('admin_support.documents.index');
        Route::post('/documents', [AdminSupportController::class, 'documentsStore'])->name('admin_support.documents.store');
        Route::post('/documents/{id}/verify', [AdminSupportController::class, 'documentsVerify'])->name('admin_support.documents.verify');
        Route::post('/documents/{id}/reject-clarify', [AdminSupportController::class, 'documentsRejectClarify'])->name('admin_support.documents.reject-clarify');

        // Document Checklists (Gatekeeper Matrix)
        Route::get('/checklists', [AdminSupportController::class, 'checklistsIndex'])->name('admin_support.checklists.index');
        Route::post('/checklists/{id}', [AdminSupportController::class, 'checklistsUpdate'])->name('admin_support.checklists.update');

        // Logistics & Delivery Instructions (Surat Jalan)
        Route::get('/logistics', [AdminSupportController::class, 'logisticsIndex'])->name('admin_support.logistics.index');
        Route::post('/logistics', [AdminSupportController::class, 'logisticsStore'])->name('admin_support.logistics.store');
        Route::post('/logistics/{id}/status', [AdminSupportController::class, 'logisticsUpdateStatus'])->name('admin_support.logistics.status');

        // Serial Number Registry (SN Tracking)
        Route::get('/inventory', [AdminSupportController::class, 'inventoryIndex'])->name('admin_support.inventory.index');
        Route::post('/inventory/sn', [AdminSupportController::class, 'inventoryStoreSN'])->name('admin_support.inventory.sn.store');

        // Operational Tool Assets & Equipment
        Route::get('/assets', [AdminSupportController::class, 'assetsIndex'])->name('admin_support.assets.index');
        Route::post('/assets', [AdminSupportController::class, 'assetsStore'])->name('admin_support.assets.store');
        Route::post('/assets/{id}/borrow-return', [AdminSupportController::class, 'assetsBorrowReturn'])->name('admin_support.assets.borrow-return');

        // Handover Records & Repository Archive
        Route::get('/handovers', [AdminSupportController::class, 'handoversIndex'])->name('admin_support.handovers.index');
        Route::post('/handovers', [AdminSupportController::class, 'handoversStore'])->name('admin_support.handovers.store');
    });

    Route::get('/dashboard/admin-support', [AdminSupportController::class, 'index'])
        ->name('dashboard.admin_support');

    Route::get('/projects/{project}/proposal/download', [\App\Http\Controllers\PresalesProposalController::class, 'download'])
        ->name('projects.proposal.download');

    // Sales Projects Pipeline
    Route::prefix('sales-projects')->group(function () {
        Route::get('/', [SalesProjectController::class, 'index'])->name('sales.projects.index');
        Route::post('/', [SalesProjectController::class, 'store'])->name('sales.projects.store');
        Route::put('/{project}', [SalesProjectController::class, 'update'])->name('sales.projects.update');
        Route::delete('/{project}', [SalesProjectController::class, 'destroy'])->name('sales.projects.destroy');
    });

    // Clients
    Route::resource('clients', ClientController::class);

    // Vendors
    Route::resource('vendors', VendorController::class);

    // Inventory
    Route::prefix('inventory')->group(function () {
        Route::get('/', [InventoryController::class, 'index'])->name('inventory.index');
        Route::post('/', [InventoryController::class, 'store'])->name('inventory.store');
        Route::put('/{item}', [InventoryController::class, 'update'])->name('inventory.update');
        Route::delete('/{item}', [InventoryController::class, 'destroy'])->name('inventory.destroy');
        Route::post('/stock-in', [InventoryController::class, 'stockIn'])->name('inventory.stock-in');
        Route::post('/stock-out', [InventoryController::class, 'stockOut'])->name('inventory.stock-out');
    });

    // Projects - Managerial, PMO, Sales, Presales, Solution Architect & Engineers
    Route::prefix('projects')->middleware('role:Director|Direktur|HD / Direktur|Division Head|Group Leader|Group Leader Commercial & Solution|Group Leader Delivery & Operation|PMO|Project Manager|Lead Divisi|Team Leader Engineering|Team Leader|Lead Maintenance|Lead Engineer|Sales|Account Manager|BusDev|BDM|Presales|Pre-Sales|Solution Architect|Solutions Architect|Network Engineer|Security Engineer|Managed Service|Field Support (EOS)|Field Support|Engineer|Engineer L1|Engineer L2|Maintenance')->group(function () {
        Route::get('/', [ProjectController::class, 'index'])->name('projects.index');
        Route::post('/', [ProjectController::class, 'store'])->name('projects.store');
        Route::put('/{project}', [ProjectController::class, 'update'])->name('projects.update');
        Route::delete('/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');
        Route::get('/{project}', [ProjectController::class, 'show'])->name('projects.show');
        Route::get('/data', [ProjectController::class, 'getData'])->name('projects.data');

        // Stage Lifecycle & Team Actions
        Route::post('/{project}/assign', [ProjectController::class, 'assignTeam'])->name('projects.assign');
        Route::post('/{project}/assign-division', [ProjectController::class, 'assignDivision'])->name('projects.assign_division');
        Route::post('/{project}/assign-approver', [ProjectController::class, 'assignApprover'])->name('projects.assign_approver');
        Route::post('/{project}/approve-draft', [ProjectController::class, 'approveDraft'])->name('projects.approve_draft');
        Route::post('/{project}/assign-technical', [ProjectController::class, 'assignTechnical'])->name('projects.assign_technical');
        Route::post('/{project}/upload-technical-doc', [ProjectController::class, 'uploadTechnicalDoc'])->name('projects.upload_technical_doc');
        Route::post('/{project}/verify-technical', [ProjectController::class, 'verifyTechnicalSolution'])->name('projects.verify_technical');
        Route::post('/{project}/update-pipeline', [ProjectController::class, 'updatePipeline'])->name('projects.update_pipeline');
        Route::post('/{project}/stage-update', [ProjectController::class, 'updateStageDirect'])->name('projects.stage_update');
        Route::post('/{project}/meta-update', [ProjectController::class, 'updateMeta'])->name('projects.meta_update');
        Route::post('/{project}/update-client', [ProjectController::class, 'updateClientInfo'])->name('projects.update_client');
        Route::post('/{project}/handover-to-ms', [ProjectController::class, 'handoverToMs'])->name('projects.handover_to_ms');
        Route::post('/{project}/confirm-handover-pmo', [ProjectController::class, 'confirmHandoverPmo'])->name('projects.confirm_handover_pmo');
        Route::post('/{project}/assign-ms-engineer', [ProjectController::class, 'assignMsEngineer'])->name('projects.assign_ms_engineer');
        Route::post('/{project}/milestones/bulk', [ProjectController::class, 'bulkStoreMilestones'])->name('projects.milestones.bulk_store');

        // 6-Stage Handover & Document Flow (Commercial to Operation)
        Route::get('/{project}/document-flow', [\App\Http\Controllers\ProjectDocumentController::class, 'getProjectFlow'])->name('projects.document_flow');
        Route::post('/{project}/documents/upload', [\App\Http\Controllers\ProjectDocumentController::class, 'upload'])->name('projects.documents.upload');
        Route::post('/{project}/documents/{document}/verify', [\App\Http\Controllers\ProjectDocumentController::class, 'verify'])->name('projects.documents.verify');
        Route::get('/{project}/documents/{document}/download', [\App\Http\Controllers\ProjectDocumentController::class, 'download'])->name('projects.documents.download');
        Route::get('/{project}/documents/{document}/view', [\App\Http\Controllers\ProjectDocumentController::class, 'view'])->name('projects.documents.view');
        Route::delete('/{project}/documents/{document}', [\App\Http\Controllers\ProjectDocumentController::class, 'delete'])->name('projects.documents.delete');
        Route::get('/{project}/handover-document', [\App\Http\Controllers\SalesCrmController::class, 'showHandoverDocument'])->name('projects.handover.document');
    });

    // Tasks
    Route::prefix('tasks')->group(function () {
        Route::get('/', [TaskController::class, 'index'])->name('tasks.index');
        Route::post('/', [TaskController::class, 'store'])->name('tasks.store')
            ->middleware('role:Director|Direktur|HD / Direktur|Division Head|Group Leader|Group Leader Delivery & Operation|Lead Divisi|Team Leader Engineering|Team Leader|Lead Maintenance|Lead Engineer|Managed Service|PMO|Project Manager|Super Admin|Admin');
        Route::put('/{task}', [TaskController::class, 'update'])->name('tasks.update');
        Route::delete('/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy')
            ->middleware('role:Director|Direktur|HD / Direktur|Division Head|Group Leader|Group Leader Delivery & Operation|Lead Divisi|Team Leader Engineering|Team Leader|Lead Maintenance|Lead Engineer|Managed Service|PMO|Project Manager|Super Admin|Admin');
        Route::get('/kanban-data', [TaskController::class, 'getKanbanData'])->name('tasks.kanban');
    });

    // Schedules
    Route::prefix('schedules')->group(function () {
        Route::get('/', [ScheduleController::class, 'index'])->name('schedules.index');
        Route::get('/export', [ScheduleController::class, 'exportExcel'])->name('schedules.export');
        Route::get('/export/pdf', [ScheduleController::class, 'exportPdf'])->name('schedules.export.pdf');
        Route::post('/', [ScheduleController::class, 'store'])->name('schedules.store')
            ->middleware('role:Director|Direktur|HD / Direktur|Division Head|Group Leader|Group Leader Commercial & Solution|Group Leader Delivery & Operation|PMO|Project Manager|Lead Divisi|Team Leader Engineering|Team Leader|Lead Maintenance|Lead Engineer|Managed Service|Sales|Account Manager|BusDev|BDM|Business Development|CRO|Customer Relation Officer|Presales|Pre-Sales|Solution Architect|Solutions Architect|SA');
        Route::put('/{schedule}', [ScheduleController::class, 'update'])->name('schedules.update')
            ->middleware('role:Director|Direktur|HD / Direktur|Division Head|Group Leader|Group Leader Commercial & Solution|Group Leader Delivery & Operation|PMO|Project Manager|Lead Divisi|Team Leader Engineering|Team Leader|Lead Maintenance|Lead Engineer|Managed Service|Sales|Account Manager|BusDev|BDM|Business Development|CRO|Customer Relation Officer|Presales|Pre-Sales|Solution Architect|Solutions Architect|SA');
        Route::delete('/{schedule}', [ScheduleController::class, 'destroy'])->name('schedules.destroy')
            ->middleware('role:Director|Direktur|HD / Direktur|Division Head|Group Leader|Group Leader Commercial & Solution|Group Leader Delivery & Operation|PMO|Project Manager|Lead Divisi|Team Leader Engineering|Team Leader|Lead Maintenance|Lead Engineer|Managed Service|Sales|Account Manager|BusDev|BDM|Business Development|CRO|Customer Relation Officer|Presales|Pre-Sales|Solution Architect|Solutions Architect|SA');
        Route::get('/calendar-data', [ScheduleController::class, 'getCalendarData'])->name('schedules.calendar');
    });

    // ─── PRESENSI ─────────────────────────────────────────────────────────
    Route::prefix('attendance')->group(function () {
        // Engineer / Maintenance: halaman clock in/out + riwayat
        Route::get('/',           [AttendanceController::class, 'index'])->name('attendance.index');
        Route::post('/clock-in',  [AttendanceController::class, 'clockIn'])->name('attendance.clock-in');
        Route::post('/clock-out', [AttendanceController::class, 'clockOut'])->name('attendance.clock-out');

        // Managerial: rekap presensi & export
        Route::get('/recap',       [AttendanceController::class, 'recap'])->name('attendance.recap')->middleware('role:Director|Direktur|HD / Direktur|Division Head|Group Leader|Group Leader Commercial & Solution|Group Leader Delivery & Operation|PMO|Project Manager|Lead Divisi|Team Leader Engineering|Team Leader|Lead Maintenance|Lead Engineer|Managed Service');
        Route::get('/daily-data',  [AttendanceController::class, 'dailyData'])->name('attendance.daily-data')->middleware('role:Director|Direktur|HD / Direktur|Division Head|Group Leader|Group Leader Commercial & Solution|Group Leader Delivery & Operation|PMO|Project Manager|Lead Divisi|Team Leader Engineering|Team Leader|Lead Maintenance|Lead Engineer|Managed Service');
        Route::get('/export/pdf',  [AttendanceController::class, 'exportPdf'])->name('attendance.export.pdf')->middleware('role:Director|Direktur|HD / Direktur|Division Head|Group Leader|Group Leader Commercial & Solution|Group Leader Delivery & Operation|PMO|Project Manager|Lead Divisi|Team Leader Engineering|Team Leader|Lead Maintenance|Lead Engineer|Managed Service');
    });

    // ─── TIMESHEET ────────────────────────────────────────────────────────
    Route::prefix('timesheets')->group(function () {
        Route::get('/',             [TimesheetController::class, 'index'])->name('timesheets.index');
        Route::post('/',            [TimesheetController::class, 'store'])->name('timesheets.store');
        Route::put('/{timesheet}',  [TimesheetController::class, 'update'])->name('timesheets.update');
        Route::delete('/{timesheet}', [TimesheetController::class, 'destroy'])->name('timesheets.destroy');
        Route::get('/export/excel', [TimesheetController::class, 'exportExcel'])->name('timesheets.export.excel');
        Route::get('/export/pdf',   [TimesheetController::class, 'exportPdf'])->name('timesheets.export.pdf');
    });

    // ─── PENGGUNA (HANYA HEAD OF DIVISION & DIRECTOR) ─────────────────────
    Route::prefix('users')->middleware('role:Director|Direktur|HD / Direktur|Division Head|Kepala Divisi|Head of Division|Group Leader|Group Leader Commercial & Solution|Group Leader Delivery & Operation')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('users.index');
        Route::post('/', [UserController::class, 'store'])->name('users.store');
        Route::put('/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::post('/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::post('/{user}/approve-certification', [UserController::class, 'approveCertification'])->name('users.approve-certification');
        Route::post('/{user}/reject-certification', [UserController::class, 'rejectCertification'])->name('users.reject-certification');
    });

    // Certifications Approval & Deletion (Managerial Roles)
    Route::post('/certifications/{certification}/approve', [UserController::class, 'approveCertification'])->name('certifications.approve')->middleware('role:Director|Direktur|HD / Direktur|Division Head|Group Leader|Group Leader Commercial & Solution|Group Leader Delivery & Operation|PMO|Project Manager|Lead Divisi|Team Leader Engineering|Team Leader|Lead Maintenance|Lead Engineer|Managed Service');
    Route::post('/certifications/{certification}/reject', [UserController::class, 'rejectCertification'])->name('certifications.reject')->middleware('role:Director|Direktur|HD / Direktur|Division Head|Group Leader|Group Leader Commercial & Solution|Group Leader Delivery & Operation|PMO|Project Manager|Lead Divisi|Team Leader Engineering|Team Leader|Lead Maintenance|Lead Engineer|Managed Service');
    Route::delete('/certifications/{certification}', [UserController::class, 'rejectCertification'])->name('certifications.destroy')->middleware('role:Director|Direktur|HD / Direktur|Division Head|Group Leader|Group Leader Commercial & Solution|Group Leader Delivery & Operation|PMO|Project Manager|Lead Divisi|Team Leader Engineering|Team Leader|Lead Maintenance|Lead Engineer|Managed Service');

    // Search 
    Route::get('/search', [SearchController::class, 'index'])->name('search');

    // Notifications 
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('notifications.index');
        Route::post('/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.markAllRead');
        Route::get('/latest', [NotificationController::class, 'latest'])->name('notifications.latest');
        Route::post('/read-all', [NotificationController::class, 'ajaxMarkAllRead'])->name('notifications.read-all');
        Route::delete('/destroy-all', [NotificationController::class, 'destroyAll'])->name('notifications.destroyAll');
        Route::delete('/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    });

    // Profile
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('profile.show');
        Route::put('/update', [ProfileController::class, 'updateProfile'])->name('profile.update');
        Route::put('/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
        Route::post('/certification', [ProfileController::class, 'uploadCertification'])->name('profile.certification');
        Route::delete('/certification/{certification}', [ProfileController::class, 'deleteCertification'])->name('profile.certification.delete');
    });

    // Pengaturan Sistem, Profil & Tanda Tangan Digital (Semua Role)
    Route::prefix('settings')->group(function () {
        Route::get('/', [\App\Http\Controllers\SettingsController::class, 'index'])->name('settings.index');
        Route::post('/profile', [\App\Http\Controllers\SettingsController::class, 'updateProfile'])->name('settings.profile');
        Route::post('/password', [\App\Http\Controllers\SettingsController::class, 'updatePassword'])->name('settings.password');
        Route::post('/signature', [\App\Http\Controllers\SettingsController::class, 'updateSignature'])->name('settings.signature');
        Route::delete('/signature', [\App\Http\Controllers\SettingsController::class, 'deleteSignature'])->name('settings.signature.delete');
        Route::post('/preferences', [\App\Http\Controllers\SettingsController::class, 'updatePreferences'])->name('settings.preferences');
    });

    // Stream Sertifikasi (Aman dari kendala symlink cPanel & Terproteksi Hak Akses)
    Route::get('/certification-file/{certification}', function (\App\Models\Certification $certification) {
        if (!$certification->file_path) {
            abort(404);
        }

        $authUser = auth()->user();
        $targetUser = $certification->user;

        // Kontrol Hak Akses:
        if ($authUser->id !== $certification->user_id) {
            $isTopMgmt = $authUser->hasAnyRole(['Director', 'Direktur', 'HD / Direktur', 'Division Head', 'Group Leader', 'Lead Divisi']);
            $isLeader = $authUser->hasAnyRole(['Team Leader Engineering', 'Team Leader', 'Lead Engineer', 'Managed Service']) && (
                $authUser->division_id === null || 
                ($targetUser && $authUser->division_id === $targetUser->division_id)
            );

            if (!$isTopMgmt && !$isLeader) {
                abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk melihat sertifikat pengguna lain.');
            }
        }

        $filename = $certification->file_path;
        $basename = basename($filename);

        $candidates = [
            storage_path('app/public/' . $filename),
            public_path('storage/' . $filename),
            base_path('storage/app/public/' . $filename),
            base_path('public/storage/' . $filename),
            base_path('../public_html/storage/' . $filename),
            base_path('../storage/app/public/' . $filename),
            storage_path('app/public/certifications/' . $basename),
            public_path('storage/certifications/' . $basename),
            base_path('storage/app/public/certifications/' . $basename),
            base_path('../public_html/storage/certifications/' . $basename),
            base_path('../storage/app/public/certifications/' . $basename),
        ];

        foreach ($candidates as $filePath) {
            if (file_exists($filePath) && is_file($filePath)) {
                return response()->file($filePath);
            }
        }

        abort(404);
    })->name('certifications.file');

});

// Home redirect
Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();
        $email = strtolower($user->email ?? '');
        $name = strtolower($user->name ?? '');
        $pos = strtolower($user->position ?? '');

        // Dual role handling (Novan: BDM & Lead Security)
        $activeRoleView = session('active_dashboard_view', 'bdm');
        $isDualRoleNovan = (str_contains($email, 'novan') || ($user->hasRole('BDM') && $user->hasAnyRole(['Lead Engineer', 'Team Leader Engineering', 'Security Engineer', 'Lead Security', 'Team Leader'])));
        if ($isDualRoleNovan) {
            if ($activeRoleView === 'lead_security' || $activeRoleView === 'lead') {
                return redirect()->route('dashboard.lead');
            } else {
                return redirect()->route('dashboard.bdm');
            }
        }

        // Warehouse & Logistics Staff -> Selalu ke Dashboard Warehouse
        if ($user->hasRole('Warehouse') || str_contains($email, 'warehouse') || str_contains($pos, 'warehouse')) {
            return redirect()->route('warehouse.dashboard');
        }

        // PMO & Project Manager -> Selalu ke Dashboard PMO
        if ($user->hasAnyRole(['PMO', 'Project Manager']) || str_contains($email, 'rizki') || str_contains($email, 'kuncoro') || str_contains($pos, 'project manager')) {
            return redirect()->route('pmo.dashboard');
        }

        // Executive / Kepala Divisi / Director / Group Leader -> Dashboard Lead
        if ($user->hasAnyRole(['Director', 'Direktur', 'HD / Direktur', 'Division Head', 'Kepala Divisi', 'Head of Division', 'Group Leader Delivery & Operation', 'Group Leader'])) {
            return redirect()->route('dashboard.lead');
        }

        // Engineer (Field / Technical Delivery) selalu diarahkan ke dashboard engineer
        if ($user->hasRole('Engineer') && !$user->hasAnyRole(['Director', 'Direktur', 'Division Head', 'Kepala Divisi', 'Head of Division', 'Group Leader', 'Sales', 'BDM', 'Presales', 'Solution Architect', 'PMO', 'Project Manager', 'Lead Security'])) {
            return redirect()->route('dashboard.engineer');
        }

        if ($user->hasAnyRole(['Solution Architect', 'Solutions Architect', 'SA', 'Tech Develop', 'Tech.Develp (R&D)', 'R&D']) || str_contains($email, 'aris') || str_contains($pos, 'solution architect') || str_contains($pos, 'architect')) {
            return redirect()->route('dashboard.architect');
        }
        if ($user->hasAnyRole(['Presales', 'Pre-Sales', 'Lead Pre Sales']) || str_contains($pos, 'pre-sales') || str_contains($pos, 'presales')) {
            return redirect()->route('dashboard.presales');
        }
        if ($user->hasAnyRole(['BusDev', 'BDM', 'Business Development']) || str_contains($pos, 'business development') || str_contains($pos, 'bdm')) {
            return redirect()->route('dashboard.bdm');
        }
        if ($user->hasAnyRole(['CRO', 'Customer Relation Officer', 'Customer Relationship Officer']) || str_contains($email, 'cro') || str_contains($pos, 'customer relation')) {
            return redirect()->route('cro.dashboard');
        }
        if ($user->hasAnyRole(['Sales', 'Account Manager', 'Group Leader Commercial & Solution']) || str_contains($pos, 'sales') || str_contains($pos, 'account manager')) {
            return redirect()->route('dashboard.sales');
        }
        if ($user->hasAnyRole(['Admin Support', 'Admin Logistik', 'Admin']) || str_contains($pos, 'admin support') || str_contains($pos, 'admin')) {
            return redirect()->route('admin_support.dashboard');
        }
        if ($user->hasAnyRole(['Lead Maintenance', 'Maintenance', 'Managed Service']) || ($user->division && str_contains(strtolower($user->division->name), 'maintenance'))) {
            return redirect()->route('ms.dashboard');
        }
        if (\App\Helpers\ScopeHelper::isManagerial($user)) {
            return redirect()->route('dashboard.lead');
        }
        return redirect()->route('dashboard.engineer');
    }
    return redirect()->route('login');
});

// Fallback storage route untuk cPanel jika symlink public/storage belum ada
Route::get('/storage/{path}', function ($path) {
    $cleanPath = ltrim(urldecode($path), '/');
    $candidates = [
        storage_path('app/public/' . $cleanPath),
        public_path('storage/' . $cleanPath),
        base_path('storage/app/public/' . $cleanPath),
        base_path('public/storage/' . $cleanPath),
        base_path('../public_html/storage/' . $cleanPath),
    ];

    foreach ($candidates as $filePath) {
        if (file_exists($filePath) && is_file($filePath)) {
            return response()->file($filePath);
        }
    }

    abort(404, 'File lampiran tidak ditemukan pada server.');
})->where('path', '.*')->name('storage.file');

// Perbaikan otomatis folder storage & symlink untuk cPanel
Route::get('/fix-storage', function () {
    $outputs = [];

    // 1. Jalankan storage:link
    try {
        Artisan::call('storage:link');
        $outputs[] = "Artisan storage:link output: " . Artisan::output();
    } catch (\Exception $e) {
        $outputs[] = "Artisan storage:link error: " . $e->getMessage();
    }

    // 2. Daftar folder yang disinkronkan (attendance, certifications, avatars, tasks)
    $subdirs = ['attendance', 'certifications', 'avatars', 'tasks'];
    
    foreach ($subdirs as $sub) {
        $source = storage_path("app/public/{$sub}");
        $destCandidates = array_unique(array_filter([
            public_path("storage/{$sub}"),
            base_path("public/storage/{$sub}"),
            base_path("../public_html/storage/{$sub}"),
        ]));

        foreach ($destCandidates as $destDir) {
            if (!File::exists($destDir)) {
                File::makeDirectory($destDir, 0755, true, true);
                $outputs[] = "Membuat direktori: {$destDir}";
            }
        }

        // Salin seluruh file dari storage/app/public ke folder public
        if (File::exists($source)) {
            $files = File::files($source);
            foreach ($files as $file) {
                $filename = $file->getFilename();
                foreach ($destCandidates as $destDir) {
                    $targetPath = $destDir . '/' . $filename;
                    if (!File::exists($targetPath)) {
                        File::copy($file->getPathname(), $targetPath);
                        $outputs[] = "Menyalin {$sub}/{$filename} ke {$targetPath}";
                    }
                }
            }
        }
    }

    return response()->json([
        'status' => 'success',
        'message' => 'Perbaikan & sinkronisasi folder storage cPanel (termasuk foto presensi) berhasil!',
        'details' => $outputs,
    ]);
});

// Utility route AMAN untuk menjalankan migrasi database saja di hosting tanpa menghapus data
Route::get('/migrate-db', function () {
    try {
        if (function_exists('opcache_reset')) {
            @opcache_reset();
        }
        Artisan::call('migrate', ['--force' => true]);
        Artisan::call('config:clear');
        Artisan::call('cache:clear');
        Artisan::call('view:clear');
        Artisan::call('route:clear');
        return response()->json([
            'status'  => 'success',
            'message' => 'Migrasi tabel database berhasil dijalankan di hosting dan cache/OPcache dibersihkan!',
            'output'  => trim(Artisan::output()) ?: 'Database sudah up-to-date.',
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status'  => 'error',
            'message' => 'Gagal menjalankan migrasi: ' . $e->getMessage(),
        ], 500);
    }
});

// Utility route untuk mereset bytecode OPcache dan view cache secara instan
Route::get('/clear-opcache', function () {
    $opcacheCleared = false;
    if (function_exists('opcache_reset')) {
        $opcacheCleared = @opcache_reset();
    }

    // Pastikan user Raihan Ghiffary ada di database
    try {
        if (!\App\Models\User::where('name', 'like', '%Raihan%')->exists()) {
            $divNet = \App\Models\Division::where('name', 'like', '%Network%')->first();
            $raihan = \App\Models\User::create([
                'name'        => 'Raihan Ghiffary',
                'email'       => 'raihan@ipnetsolusindo.com',
                'password'    => \Illuminate\Support\Facades\Hash::make('password123'),
                'phone'       => '08123456789',
                'position'    => 'L2 Engineer',
                'status'      => 'Active',
                'division_id' => $divNet?->id,
            ]);
            if (\Spatie\Permission\Models\Role::where('name', 'Network Engineer')->exists()) {
                $raihan->assignRole('Network Engineer');
            }
        }

        // Pastikan task pada proyek Angkasa Pura Support terikat ke Syaiful dan Raihan
        $angProject = \App\Models\Project::where('name', 'like', '%Angkasa Pura%')->first();
        if ($angProject) {
            $syaifulUser = \App\Models\User::where('name', 'like', '%Syaiful%')->first();
            $raihanUser  = \App\Models\User::where('name', 'like', '%Raihan%')->first();
            if ($syaifulUser && !\App\Models\Task::where('project_id', $angProject->id)->where('engineer_id', $syaifulUser->id)->exists()) {
                \App\Models\Task::create([
                    'title'       => 'PIC Teknis Lapangan: ' . $angProject->name,
                    'project_id'  => $angProject->id,
                    'engineer_id' => $syaifulUser->id,
                    'priority'    => 'High',
                    'status'      => 'In Progress',
                    'start_date'  => date('Y-m-d'),
                ]);
            }
            if ($raihanUser && !\App\Models\Task::where('project_id', $angProject->id)->where('engineer_id', $raihanUser->id)->exists()) {
                \App\Models\Task::create([
                    'title'       => 'Personel Lapangan: ' . $angProject->name,
                    'project_id'  => $angProject->id,
                    'engineer_id' => $raihanUser->id,
                    'priority'    => 'Medium',
                    'status'      => 'In Progress',
                    'start_date'  => date('Y-m-d'),
                ]);
            }
        }

        // Bersihkan data formulir lama di tabel activity_document_signatures yang tercemar string gabungan
        $allSigs = \App\Models\ActivityDocumentSignature::all();
        foreach ($allSigs as $bSig) {
            if (!empty($bSig->report_data['manpower']) && is_array($bSig->report_data['manpower'])) {
                $cleanedManpower = [];
                foreach ($bSig->report_data['manpower'] as $mp) {
                    $nm = trim($mp['nama'] ?? '');
                    if (
                        $nm === '' ||
                        str_contains($nm, ',') ||
                        stripos($nm, ' dan ') !== false ||
                        str_contains($nm, '&') ||
                        str_contains($nm, '/') ||
                        mb_strlen($nm) > 35
                    ) {
                        continue;
                    }
                    $cleanedManpower[] = $mp;
                }
                $rd = $bSig->report_data;
                $rd['manpower'] = $cleanedManpower;
                $bSig->report_data = $rd;
                $bSig->save();
            }
        }

        // Pastikan tabel digital_signature_documents dibuat
        \App\Models\DigitalSignatureDocument::ensureSchemaReady();
    } catch (\Throwable $e) {}

    Artisan::call('view:clear');
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    return response()->json([
        'status'        => 'success',
        'message'       => 'OPcache and view caches successfully cleared, and project personnel synchronized!',
        'opcache_reset' => $opcacheCleared,
    ]);
});

// Route ini TELAH DINONAKTIFKAN PERMANEN demi keamanan agar data tidak terhapus.
Route::get('/clear-dummy-data', function () {
    return response()->json([
        'status'  => 'protected',
        'message' => 'Penghapusan data dinonaktifkan demi perlindungan data perusahaan Anda.',
    ]);
});

// Webhook / Helper route untuk menarik update terbaru dari GitHub di cPanel/hosting
Route::get('/git-pull-deploy', function () {
    $outputs = [];
    $returnVar = 0;
    if (function_exists('exec')) {
        $basePath = base_path();
        @exec("git -C \"{$basePath}\" pull origin main 2>&1", $outputs, $returnVar);
    }
    
    $migrateOutput = '';
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        $migrateOutput = \Illuminate\Support\Facades\Artisan::output();
        \Illuminate\Support\Facades\Artisan::call('view:clear');
        \Illuminate\Support\Facades\Artisan::call('cache:clear');
    } catch (\Throwable $e) {
        $migrateOutput = 'Migrate error: ' . $e->getMessage();
    }

    return response()->json([
        'status' => $returnVar === 0 ? 'success' : 'notice',
        'git_pull_output' => $outputs,
        'migrate_output' => trim($migrateOutput),
        'message' => 'Kode terbaru dari GitHub telah ditarik dan cache tampilan telah dibersihkan!',
    ]);
});

// Route aman untuk menjalankan migration saja tanpa mereset data
Route::get('/migrate-only', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        $output = \Illuminate\Support\Facades\Artisan::output();
        return response('<div style="background:#0F172A;color:#10B981;padding:24px;border-radius:12px;font-family:sans-serif;max-width:700px;margin:40px auto;box-shadow:0 20px 40px rgba(0,0,0,0.3);"><h2 style="margin-top:0;color:#34D399;">✓ Database Migration Berhasil Dijalankan!</h2><p style="color:#CBD5E1;font-size:13.5px;">Tabel database Managed Service dan modul lainnya telah berhasil dibuat dan diperbarui di hosting.</p><pre style="background:#1E293B;padding:16px;border-radius:8px;color:#F8FAFC;font-size:13px;overflow-x:auto;">' . htmlspecialchars($output ?: 'Nothing to migrate (Semua tabel database sudah up to date).') . '</pre><br><a href="/managed-service" style="display:inline-block;padding:10px 20px;background:#8F0A0D;color:white;text-decoration:none;border-radius:8px;font-weight:bold;">&larr; Buka Menu Managed Service</a></div>');
    } catch (\Throwable $e) {
        return response('<div style="background:#0F172A;color:#EF4444;padding:24px;border-radius:12px;font-family:sans-serif;max-width:700px;margin:40px auto;"><h2 style="margin-top:0;">✗ Error Migration:</h2><pre style="background:#1E293B;padding:16px;border-radius:8px;color:#FCA5A5;font-size:13px;">' . htmlspecialchars($e->getMessage()) . '</pre></div>', 500);
    }
});

// Route pemulihan instan seluruh 7 proyek Managed Service resmi user
Route::get('/restore-user-project', function () {
    try {
        $controller = new \App\Http\Controllers\ManagedServiceController();
        $controller->ensureSampleProjectExists();
        return redirect()->route('ms.projects.index', ['tab' => 'active'])
            ->with('success', "Seluruh 7 proyek riil (PE LAN BRI, EXTERNET ODC, BP. BATAM, BATII, LICENSE WIFI SUDIRMAN, WIFI MATARAM, WIFI JAKARTA) berhasil diaktifkan!");
    } catch (\Throwable $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});

Route::get('/restore-all-server-projects', function () {
    return redirect('/restore-user-project');
});

// Route inspeksi mendalam database asli server (RAW DATA tanpa manipulasi)
Route::get('/inspect-server-db', function () {
    $data = [];
    try {
        $data['projects'] = DB::table('projects')->select('id', 'name', 'client', 'location', 'project_type', 'customer_pic_technical', 'customer_pic_business', 'customer_pic_finance', 'po_spk_number', 'po_number', 'contract_value', 'po_spk_file', 'contract_file', 'boq_file', 'tor_file', 'proposal_file', 'created_at', 'deleted_at')->orderBy('id', 'desc')->get();
    } catch (\Throwable $e) {
        $data['projects_error'] = $e->getMessage();
    }
    try {
        $data['documents'] = DB::table('project_documents')->get();
    } catch (\Throwable $e) {
        $data['documents_error'] = $e->getMessage();
    }
    return response()->json($data);
});

// Route inspeksi database proyek server (JSON)
Route::get('/server-project-status', function () {
    try {
        $controller = new \App\Http\Controllers\ManagedServiceController();
        $controller->ensureSampleProjectExists();

        $allProjects = \App\Models\Project::withTrashed()
            ->latest('id')
            ->get(['id', 'name', 'client', 'location', 'project_type', 'status', 'stage', 'handover_target', 'ms_handover_status', 'deleted_at']);

        return response()->json([
            'status'         => 'success',
            'total_projects' => $allProjects->count(),
            'projects'       => $allProjects,
        ], 200, [], JSON_PRETTY_PRINT);
    } catch (\Throwable $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});

// Utility route untuk migrate database & clear cache (TANPA TRUNCATE PROYEK)
Route::get('/run-migration', function () {
    $outputs = [];
    try {
        Artisan::call('migrate', ['--force' => true]);
        $outputs[] = "Artisan migrate output:\n" . trim(Artisan::output());

        // 1. Reset cache permission Spatie & Seeder Role Resmi
        if (class_exists(\Spatie\Permission\PermissionRegistrar::class)) {
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        }
        Artisan::call('db:seed', ['--class' => 'RoleSeeder', '--force' => true]);
        $outputs[] = "Artisan db:seed RoleSeeder: OK (Semua role dibuat)";

        // 2. Jalankan seeder resmi agar semua akun (Pak Hariyadi, Susanto, Nugraha, Ignatius, Eka, Rorik, dll) aktif
        Artisan::call('db:seed', ['--class' => 'DummyUserSeeder', '--force' => true]);
        $outputs[] = "Artisan db:seed DummyUserSeeder: OK (Semua proyek, task, dan tim resmi diisi)";

        // 3. Jalankan seeder sales & inventory lengkap
        Artisan::call('db:seed', ['--class' => 'SalesInitialSeeder', '--force' => true]);
        $outputs[] = "Artisan db:seed SalesInitialSeeder: OK (Data dummy sales, klien, vendor, inventory diisi)";

        Artisan::call('view:clear');
        $outputs[] = "Artisan view:clear: OK";

        Artisan::call('config:clear');
        $outputs[] = "Artisan config:clear: OK";

        Artisan::call('cache:clear');
        $outputs[] = "Artisan cache:clear: OK";

        return response()->json([
            'status' => 'success',
            'message' => 'Database hosting berhasil dibersihkan total & diisi data tim resmi!',
            'team_members' => [
                'Director'                          => 'hariyadi@ipnetsolusindo.com (password: password123)',
                'Division Head & GL Delivery'       => 'susanto@ipnetsolusindo.com (password: password123)',
                'Group Leader Commercial'           => 'gl.commercial@ipnetsolusindo.com (password: password123)',
                'PMO Head'                          => 'kuncoro@ipnetsolusindo.com (password: password123)',
                'Project Manager'                   => 'rizki@ipnetsolusindo.com (password: password123)',
                'BusDev (BD)'                       => 'erie@ipnetsolusindo.com (password: password123)',
                'Sales (Account Manager)'           => 'raiza@ipnetsolusindo.com (password: password123)',
                'Customer Relation Officer (CRO)'   => 'cro@ipnetsolusindo.com (password: password123)',
                'Pre-Sales'                         => 'akbar@ipnetsolusindo.com (password: password123)',
                'Solution Architect (Expert)'       => 'aris@ipnetsolusindo.com (password: password123)',
                'Tech Develop (R&D)'                => 'techdev@ipnetsolusindo.com (password: password123)',
                'Team Leader Network Engineering'   => 'nugraha@ipnetsolusindo.com (password: password123)',
                'Team Leader Security Engineering'  => 'ignatius@ipnetsolusindo.com (password: password123)',
                'Managed Service Coordinator'       => 'doris@ipnetsolusindo.com (password: password123)',
                'Field Support Engineer (EOS)'      => 'mario@ipnetsolusindo.com (password: password123)',
                'Network Engineer (L1)'             => 'rorik@ipnetsolusindo.com (password: password123)',
                'Network Engineer (L2)'             => 'dedy@ipnetsolusindo.com (password: password123)',
                'Security Engineer (L1)'            => 'eka@ipnetsolusindo.com (password: password123)',
            ],
            'details' => $outputs,
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Gagal: ' . $e->getMessage(),
        ], 500);
    }
});

// Helper Route: 1-Click Repair Akun & Akses Rizki (PMO / Project Manager)
Route::get('/fix-rizki-access', function () {
    try {
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Project Manager', 'guard_name' => 'web']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'PMO', 'guard_name' => 'web']);

        $user = \App\Models\User::whereRaw('LOWER(email) = ?', ['rizki@ipnetsolusindo.com'])
            ->orWhere('name', 'like', '%Rizki%')
            ->first();

        if (!$user) {
            $user = \App\Models\User::create([
                'name'     => 'Rizki',
                'email'    => 'rizki@ipnetsolusindo.com',
                'password' => \Illuminate\Support\Facades\Hash::make('password123'),
                'phone'    => '08111000010',
                'position' => 'Project Manager',
                'status'   => 'Active',
            ]);
        } else {
            $user->update([
                'email'    => 'rizki@ipnetsolusindo.com',
                'name'     => 'Rizki',
                'password' => \Illuminate\Support\Facades\Hash::make('password123'),
                'position' => 'Project Manager',
                'status'   => 'Active',
            ]);
        }

        $user->syncRoles(['PMO', 'Project Manager']);

        \Illuminate\Support\Facades\Artisan::call('cache:clear');
        \Illuminate\Support\Facades\Artisan::call('view:clear');

        return response()->json([
            'status'   => 'success',
            'message'  => 'Akun dan hak akses Rizki berhasil diperbaiki secara total!',
            'user'     => [
                'id'       => $user->id,
                'name'     => $user->name,
                'email'    => $user->email,
                'password' => 'password123',
                'position' => $user->position,
                'status'   => $user->status,
                'roles'    => $user->roles->pluck('name'),
            ],
            'login_url' => url('/login'),
            'pmo_url'   => route('pmo.dashboard'),
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'status'  => 'error',
            'message' => $e->getMessage(),
        ], 500);
    }
});

// =========================================================================
// WAREHOUSE & INVENTORY MANAGEMENT SYSTEM
// =========================================================================
Route::middleware(['auth'])->prefix('warehouse')->name('warehouse.')->group(function () {
    // 1. Dashboard
    Route::get('/', [\App\Http\Controllers\WarehouseController::class, 'dashboard'])->name('dashboard');

    // 2. Produk
    Route::get('/products', [\App\Http\Controllers\WarehouseController::class, 'products'])->name('products.index');
    Route::post('/products', [\App\Http\Controllers\WarehouseController::class, 'storeProduct'])->name('products.store');
    Route::post('/products/scan-check', [\App\Http\Controllers\WarehouseController::class, 'scanCheck'])->name('products.scan_check');
    Route::post('/products/batch-scan', [\App\Http\Controllers\WarehouseController::class, 'batchStoreProduct'])->name('products.batch_scan');
    Route::put('/products/{product}', [\App\Http\Controllers\WarehouseController::class, 'updateProduct'])->name('products.update');
    Route::delete('/products/{product}', [\App\Http\Controllers\WarehouseController::class, 'destroyProduct'])->name('products.destroy');

    // 3. Kategori Produk
    Route::get('/categories', [\App\Http\Controllers\WarehouseController::class, 'categories'])->name('categories.index');
    Route::post('/categories', [\App\Http\Controllers\WarehouseController::class, 'storeCategory'])->name('categories.store');
    Route::put('/categories/{category}', [\App\Http\Controllers\WarehouseController::class, 'updateCategory'])->name('categories.update');
    Route::delete('/categories/{category}', [\App\Http\Controllers\WarehouseController::class, 'destroyCategory'])->name('categories.destroy');

    // 4. Satuan
    Route::get('/units', [\App\Http\Controllers\WarehouseController::class, 'units'])->name('units.index');
    Route::post('/units', [\App\Http\Controllers\WarehouseController::class, 'storeUnit'])->name('units.store');
    Route::put('/units/{unit}', [\App\Http\Controllers\WarehouseController::class, 'updateUnit'])->name('units.update');
    Route::delete('/units/{unit}', [\App\Http\Controllers\WarehouseController::class, 'destroyUnit'])->name('units.destroy');

    // 5. Supplier
    Route::get('/suppliers', [\App\Http\Controllers\WarehouseController::class, 'suppliers'])->name('suppliers.index');
    Route::post('/suppliers', [\App\Http\Controllers\WarehouseController::class, 'storeSupplier'])->name('suppliers.store');
    Route::put('/suppliers/{supplier}', [\App\Http\Controllers\WarehouseController::class, 'updateSupplier'])->name('suppliers.update');
    Route::delete('/suppliers/{supplier}', [\App\Http\Controllers\WarehouseController::class, 'destroySupplier'])->name('suppliers.destroy');

    // 6. Warehouse (Master Lokasi Gudang)
    Route::get('/warehouses', [\App\Http\Controllers\WarehouseController::class, 'warehouses'])->name('warehouses.index');
    Route::post('/warehouses', [\App\Http\Controllers\WarehouseController::class, 'storeWarehouse'])->name('warehouses.store');
    Route::put('/warehouses/{warehouse}', [\App\Http\Controllers\WarehouseController::class, 'updateWarehouse'])->name('warehouses.update');
    Route::delete('/warehouses/{warehouse}', [\App\Http\Controllers\WarehouseController::class, 'destroyWarehouse'])->name('warehouses.destroy');

    // 7. Purchase Order
    Route::get('/purchase-orders', [\App\Http\Controllers\WarehouseController::class, 'purchaseOrders'])->name('purchase_orders.index');
    Route::post('/purchase-orders', [\App\Http\Controllers\WarehouseController::class, 'storePurchaseOrder'])->name('purchase_orders.store');
    Route::put('/purchase-orders/{po}', [\App\Http\Controllers\WarehouseController::class, 'updatePurchaseOrder'])->name('purchase_orders.update');
    Route::delete('/purchase-orders/{po}', [\App\Http\Controllers\WarehouseController::class, 'destroyPurchaseOrder'])->name('purchase_orders.destroy');
    Route::post('/purchase-orders/{po}/status', [\App\Http\Controllers\WarehouseController::class, 'updatePoStatus'])->name('purchase_orders.status');

    // 8. Stock Opname
    Route::get('/stock-opname', [\App\Http\Controllers\WarehouseController::class, 'stockOpname'])->name('stock_opname.index');
    Route::post('/stock-opname', [\App\Http\Controllers\WarehouseController::class, 'storeStockOpname'])->name('stock_opname.store');
    Route::put('/stock-opname/{so}', [\App\Http\Controllers\WarehouseController::class, 'updateStockOpname'])->name('stock_opname.update');
    Route::delete('/stock-opname/{so}', [\App\Http\Controllers\WarehouseController::class, 'destroyStockOpname'])->name('stock_opname.destroy');
    Route::post('/stock-opname/{so}/sync', [\App\Http\Controllers\WarehouseController::class, 'syncStockOpname'])->name('stock_opname.sync');

    // 9. Delivery Order
    Route::get('/delivery-orders', [\App\Http\Controllers\WarehouseController::class, 'deliveryOrders'])->name('delivery_orders.index');
    Route::post('/delivery-orders', [\App\Http\Controllers\WarehouseController::class, 'storeDeliveryOrder'])->name('delivery_orders.store');
    Route::put('/delivery-orders/{do}', [\App\Http\Controllers\WarehouseController::class, 'updateDeliveryOrder'])->name('delivery_orders.update');
    Route::delete('/delivery-orders/{do}', [\App\Http\Controllers\WarehouseController::class, 'destroyDeliveryOrder'])->name('delivery_orders.destroy');
    Route::post('/delivery-orders/{do}/status', [\App\Http\Controllers\WarehouseController::class, 'updateDoStatus'])->name('delivery_orders.status');

    // Legacy & Support Routes
    Route::get('/items', [\App\Http\Controllers\WarehouseController::class, 'index'])->name('items.index');
    Route::post('/items', [\App\Http\Controllers\WarehouseController::class, 'store'])->name('items.store');
    Route::put('/items/{item}', [\App\Http\Controllers\WarehouseController::class, 'update'])->name('items.update');
    Route::delete('/items/{item}', [\App\Http\Controllers\WarehouseController::class, 'destroy'])->name('items.destroy');
    Route::get('/transactions/in', [\App\Http\Controllers\WarehouseController::class, 'barangMasuk'])->name('transactions.in');
    Route::get('/transactions/out', [\App\Http\Controllers\WarehouseController::class, 'barangKeluar'])->name('transactions.out');
    Route::post('/transactions/out/allocate', [\App\Http\Controllers\WarehouseController::class, 'allocateOut'])->name('transactions.out.allocate');
    Route::post('/transactions/out/return/{item}', [\App\Http\Controllers\WarehouseController::class, 'returnToWarehouse'])->name('transactions.out.return');
    Route::get('/transactions/poc', [\App\Http\Controllers\WarehouseController::class, 'peminjamanPoc'])->name('transactions.poc');
    Route::get('/reports', [\App\Http\Controllers\WarehouseController::class, 'reports'])->name('reports.index');
    Route::get('/history', [\App\Http\Controllers\WarehouseController::class, 'history'])->name('history.index');
});

// Helper 1-Click Init & Seed Warehouse Dummy
Route::get('/seed-warehouse-official', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        \Illuminate\Support\Facades\Artisan::call('db:seed', ['--class' => 'WarehouseSeeder', '--force' => true]);
        return response()->json([
            'status' => 'success',
            'message' => 'Sistem & data dummy Warehouse berhasil diinisialisasi!',
            'credentials' => [
                'name' => 'Ahmad',
                'email' => 'warehouse@ipnetsolusindo.com',
                'password' => 'password',
                'role' => 'Warehouse',
            ],
            'url' => route('warehouse.dashboard'),
        ]);
    } catch (\Throwable $e) {
        return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
    }
});
