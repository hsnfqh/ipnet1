<?php
// routes/web.php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
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

// Auth Routes
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Protected Routes
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard/lead', [DashboardController::class, 'lead'])
        ->name('dashboard.lead')
        ->middleware('role:Lead Engineer|Direktur|HD / Direktur|Group Leader|Lead Divisi|Team Leader');
    
    Route::get('/dashboard/engineer', [DashboardController::class, 'engineer'])
        ->name('dashboard.engineer')
        ->middleware('role:Engineer L1|Engineer L2|Engineer');

    // Projects - Managerial Roles
    Route::prefix('projects')->middleware('role:Lead Engineer|Direktur|HD / Direktur|Group Leader|Lead Divisi|Team Leader')->group(function () {
        Route::get('/', [ProjectController::class, 'index'])->name('projects.index');
        Route::post('/', [ProjectController::class, 'store'])->name('projects.store');
        Route::put('/{project}', [ProjectController::class, 'update'])->name('projects.update');
        Route::delete('/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');
        Route::get('/{project}', [ProjectController::class, 'show'])->name('projects.show');
        Route::get('/data', [ProjectController::class, 'getData'])->name('projects.data');
    });

    // Tasks
    Route::prefix('tasks')->group(function () {
        Route::get('/', [TaskController::class, 'index'])->name('tasks.index');
        Route::post('/', [TaskController::class, 'store'])->name('tasks.store')
            ->middleware('role:Lead Engineer|Direktur|HD / Direktur|Group Leader|Lead Divisi|Team Leader');
        Route::put('/{task}', [TaskController::class, 'update'])->name('tasks.update');
        Route::delete('/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy')
            ->middleware('role:Lead Engineer|Direktur|HD / Direktur|Group Leader|Lead Divisi|Team Leader');
        Route::get('/kanban-data', [TaskController::class, 'getKanbanData'])->name('tasks.kanban');
    });

    // Schedules
    Route::prefix('schedules')->group(function () {
        Route::get('/', [ScheduleController::class, 'index'])->name('schedules.index');
        Route::get('/export', [ScheduleController::class, 'exportExcel'])->name('schedules.export');
        Route::get('/export/pdf', [ScheduleController::class, 'exportPdf'])->name('schedules.export.pdf');
        Route::post('/', [ScheduleController::class, 'store'])->name('schedules.store')
            ->middleware('role:Lead Engineer|Direktur|HD / Direktur|Group Leader|Lead Divisi|Team Leader');
        Route::put('/{schedule}', [ScheduleController::class, 'update'])->name('schedules.update')
            ->middleware('role:Lead Engineer|Direktur|HD / Direktur|Group Leader|Lead Divisi|Team Leader');
        Route::delete('/{schedule}', [ScheduleController::class, 'destroy'])->name('schedules.destroy')
            ->middleware('role:Lead Engineer|Direktur|HD / Direktur|Group Leader|Lead Divisi|Team Leader');
        Route::get('/calendar-data', [ScheduleController::class, 'getCalendarData'])->name('schedules.calendar');
    });

    // ─── PRESENSI ─────────────────────────────────────────────────────────
    Route::prefix('attendance')->group(function () {
        // Engineer: halaman clock in/out + riwayat
        Route::get('/',           [AttendanceController::class, 'index'])->name('attendance.index');
        Route::post('/clock-in',  [AttendanceController::class, 'clockIn'])->name('attendance.clock-in');
        Route::post('/clock-out', [AttendanceController::class, 'clockOut'])->name('attendance.clock-out');

        // Managerial: rekap presensi & export
        Route::get('/recap',       [AttendanceController::class, 'recap'])->name('attendance.recap')->middleware('role:Lead Engineer|Direktur|HD / Direktur|Group Leader|Lead Divisi|Team Leader');
        Route::get('/daily-data',  [AttendanceController::class, 'dailyData'])->name('attendance.daily-data')->middleware('role:Lead Engineer|Direktur|HD / Direktur|Group Leader|Lead Divisi|Team Leader');
        Route::get('/export/pdf',  [AttendanceController::class, 'exportPdf'])->name('attendance.export.pdf')->middleware('role:Lead Engineer|Direktur|HD / Direktur|Group Leader|Lead Divisi|Team Leader');
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

    // ─── PENGGUNA ─────────────────────────────────────────────────────────
    Route::prefix('users')->middleware('role:Lead Engineer|Direktur|HD / Direktur|Group Leader|Lead Divisi|Team Leader')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('users.index');
        Route::post('/', [UserController::class, 'store'])->name('users.store');
        Route::put('/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::post('/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::post('/{user}/approve-certification', [UserController::class, 'approveCertification'])->name('users.approve-certification');
        Route::post('/{user}/reject-certification', [UserController::class, 'rejectCertification'])->name('users.reject-certification');
    });

    // Certifications Approval & Deletion (Managerial Roles)
    Route::post('/certifications/{certification}/approve', [UserController::class, 'approveCertification'])->name('certifications.approve')->middleware('role:Lead Engineer|Direktur|HD / Direktur|Group Leader|Lead Divisi|Team Leader');
    Route::post('/certifications/{certification}/reject', [UserController::class, 'rejectCertification'])->name('certifications.reject')->middleware('role:Lead Engineer|Direktur|HD / Direktur|Group Leader|Lead Divisi|Team Leader');
    Route::delete('/certifications/{certification}', [UserController::class, 'rejectCertification'])->name('certifications.destroy')->middleware('role:Lead Engineer|Direktur|HD / Direktur|Group Leader|Lead Divisi|Team Leader');



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

    // Stream Sertifikasi (Aman dari kendala symlink cPanel & Terproteksi Hak Akses)
    Route::get('/certification-file/{certification}', function (\App\Models\Certification $certification) {
        if (!$certification->file_path) {
            abort(404);
        }

        $authUser = auth()->user();
        $targetUser = $certification->user;

        // Kontrol Hak Akses:
        // 1. Pemilik file selalu boleh lihat
        // 2. Direktur & Group Leader boleh lihat semua
        // 3. Team Leader (Lead Network / Lead Security) boleh lihat anggota divisinya
        // 4. Engineer biasa TIDAK BISA melihat file milik engineer lain
        if ($authUser->id !== $certification->user_id) {
            $isTopMgmt = $authUser->hasAnyRole(['Direktur', 'HD / Direktur', 'Group Leader', 'Lead Divisi']);
            $isLeader = $authUser->hasAnyRole(['Team Leader', 'Lead Engineer']) && (
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
        if (\App\Helpers\ScopeHelper::isManagerial($user)) {
            return redirect()->route('dashboard.lead');
        }
        return redirect()->route('dashboard.engineer');
    }
    return redirect()->route('login');
});

// Fallback storage route untuk cPanel jika symlink public/storage belum ada
Route::get('/storage/{path}', function ($path) {
    $candidates = [
        public_path('storage/' . $path),
        storage_path('app/public/' . $path),
        base_path('public/storage/' . $path),
        base_path('storage/app/public/' . $path),
        base_path('../public_html/storage/' . $path),
    ];

    foreach ($candidates as $filePath) {
        if (file_exists($filePath) && is_file($filePath)) {
            return response()->file($filePath);
        }
    }

    abort(404);
})->where('path', '.*');

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

    // 2. Buat folder direktori jika belum ada
    $source = storage_path('app/public/certifications');
    $dest1 = public_path('storage/certifications');
    $dest2 = base_path('../public_html/storage/certifications');

    foreach ([$dest1, $dest2] as $destDir) {
        if (!File::exists($destDir)) {
            File::makeDirectory($destDir, 0755, true, true);
            $outputs[] = "Membuat direktori: {$destDir}";
        }
    }

    // 3. Salin file yang sudah di-upload sebelumnya ke direktori publik
    if (File::exists($source)) {
        $files = File::files($source);
        foreach ($files as $file) {
            $filename = $file->getFilename();

            if (!File::exists($dest1 . '/' . $filename)) {
                File::copy($file->getPathname(), $dest1 . '/' . $filename);
                $outputs[] = "Menyalin {$filename} ke public/storage/certifications";
            }

            if (File::isDirectory(base_path('../public_html')) && !File::exists($dest2 . '/' . $filename)) {
                File::copy($file->getPathname(), $dest2 . '/' . $filename);
                $outputs[] = "Menyalin {$filename} ke public_html/storage/certifications";
            }
        }
    }

    return response()->json([
        'status' => 'success',
        'message' => 'Perbaikan & sinkronisasi folder storage cPanel berhasil!',
        'details' => $outputs,
    ]);
});

// Utility route untuk migrate database & seed user resmi & clear cache dari browser di cPanel
Route::get('/run-migration', function () {
    $outputs = [];
    try {
        Artisan::call('migrate', ['--force' => true]);
        $outputs[] = "Artisan migrate output:\n" . trim(Artisan::output());

        // 0. Bersihkan SEMUA akun dummy lama (Rangga, Fajar, Dimas, Sinta, Bayu, Rani, atau domain lama @ipnetwork.co.id)
        \App\Models\User::whereIn('name', ['Rangga Saputra', 'Fajar Nugroho', 'Dimas Prakoso', 'Sinta Wulandari', 'Bayu Kusuma', 'Rani Oktaviani'])
            ->orWhere('email', 'like', '%@ipnetwork.co.id')
            ->delete();
        $outputs[] = "Pembersihan akun dummy lama (Rangga, Fajar, Dimas, Sinta, Bayu, Rani): OK";

        // 1. Reset cache permission Spatie & Seeder Role Resmi
        if (class_exists(\Spatie\Permission\PermissionRegistrar::class)) {
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        }
        Artisan::call('db:seed', ['--class' => 'RoleSeeder', '--force' => true]);
        $outputs[] = "Artisan db:seed RoleSeeder: OK (Semua role: Direktur, Group Leader, Team Leader, Engineer, dll dibuat)";

        // 2. Jalankan seeder resmi agar semua akun (Pak Hariyadi, Susanto, Nugraha, Ignatius, Eka, Rorik, dll) aktif
        Artisan::call('db:seed', ['--class' => 'DummyUserSeeder', '--force' => true]);
        $outputs[] = "Artisan db:seed DummyUserSeeder: OK (Semua akun resmi dibuat dengan password: password123)";

        Artisan::call('view:clear');
        $outputs[] = "Artisan view:clear: OK";

        Artisan::call('config:clear');
        $outputs[] = "Artisan config:clear: OK";

        Artisan::call('cache:clear');
        $outputs[] = "Artisan cache:clear: OK";

        return response()->json([
            'status' => 'success',
            'message' => 'Database migration, seeding akun resmi & cache clear berhasil dijalankan di cPanel!',
            'credentials_info' => [
                'Direktur'              => 'hariyadi@ipnetsolusindo.com (password: password123)',
                'Group Leader'          => 'susanto@ipnetsolusindo.com (password: password123)',
                'Team Leader Network'   => 'nugraha@ipnetsolusindo.com (password: password123)',
                'Team Leader Security'  => 'ignatius@ipnetsolusindo.com (password: password123)',
                'Engineer Network'      => 'rorik@ipnetsolusindo.com (password: password123)',
                'Engineer Security'     => 'eka@ipnetsolusindo.com (password: password123)',
            ],
            'details' => $outputs,
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Gagal menjalankan migration / seeder: ' . $e->getMessage(),
        ], 500);
    }
});