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

// Auto Migrate & Cache Clear Helper for Production Deployment
// 1-Click Master Setup Helper for Production Deployment (Migrate + Seed All Official Accounts)
Route::get('/setup-hosting-database-2026', function () {
    try {
        Artisan::call('migrate', ['--force' => true]);
        $migrationOutput = Artisan::output();
        
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        Artisan::call('db:seed', ['--class' => 'RoleSeeder', '--force' => true]);
        Artisan::call('db:seed', ['--class' => 'DummyUserSeeder', '--force' => true]);
        Artisan::call('optimize:clear');
        
        return response()->json([
            'status' => 'success',
            'message' => 'Luar biasa! Seluruh migrasi database (kolom contract_value, sales_stage, dsb.) dan seluruh akun resmi (Akbar Presales, Aris SA, 9 Sales, 5 BDM, Rangga Lead Engineer, PMO, Direktur) BERHASIL dibuat & siap digunakan!',
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

// Auto Seed Accounts & Roles Helper for Production Deployment
Route::get('/seed-dummy-accounts-2026', function () {
    try {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        Artisan::call('db:seed', ['--class' => 'RoleSeeder', '--force' => true]);
        Artisan::call('db:seed', ['--class' => 'DummyUserSeeder', '--force' => true]);
        Artisan::call('optimize:clear');
        return response()->json([
            'status' => 'success',
            'message' => 'Seluruh akun resmi (Akbar Presales, Aris Solution Architect, 9 Sales, 5 BDM, PMO, Direktur) dan role Spatie berhasil di-seed!',
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
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
    Route::prefix('pmo')->middleware('role:Director|Direktur|HD / Direktur|Division Head|Group Leader|Group Leader Delivery & Operation|PMO|Project Manager')->group(function () {
        Route::get('/dashboard', [PmoController::class, 'dashboard'])->name('pmo.dashboard');
        Route::post('/projects/{project}/stage', [PmoController::class, 'updateStage'])->name('pmo.stage.update');
        Route::post('/projects/{project}/documents', [PmoController::class, 'updateDocuments'])->name('pmo.documents.update');
        Route::post('/projects/{project}/handover-approve', [PmoController::class, 'approveHandover'])->name('pmo.handover.approve');
        Route::post('/projects/{project}/handover-conditional', [PmoController::class, 'conditionalHandover'])->name('pmo.handover.conditional');
    });

    // Lead & Executive Dashboard (Dashboard 2)
    Route::get('/dashboard/lead', [DashboardController::class, 'lead'])
        ->name('dashboard.lead')
        ->middleware('role:Director|Direktur|HD / Direktur|Division Head|Group Leader|Group Leader Delivery & Operation|Lead Divisi|Team Leader Engineering|Team Leader|Lead Maintenance|Lead Engineer|Managed Service|PMO|Project Manager');
    
    // Field Engineer & Technical Staff Dashboard (Dashboard 7)
    Route::get('/dashboard/engineer', [DashboardController::class, 'engineer'])
        ->name('dashboard.engineer')
        ->middleware('role:Network Engineer|Security Engineer|Field Support (EOS)|Field Support|Managed Service|Engineer|Engineer L1|Engineer L2|Maintenance');

    // BDM & Business Development (Dashboard + Dedicated Sub-Menus)
    Route::prefix('bdm')->middleware('role:Director|Direktur|HD / Direktur|Division Head|Group Leader|Group Leader Commercial & Solution|BusDev|BDM|Business Development|Sales|Account Manager|Presales|Pre-Sales|Solution Architect|PMO|Project Manager')->group(function () {
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
        ->middleware('role:Director|Direktur|HD / Direktur|Division Head|Group Leader|Group Leader Commercial & Solution|BusDev|BDM|Business Development|Sales|Account Manager|Presales|Pre-Sales|Solution Architect|PMO|Project Manager');

    // Dashboard Sales & Account Manager (Dashboard 3)
    Route::get('/dashboard/sales', [DashboardController::class, 'sales'])
        ->name('dashboard.sales')
        ->middleware('role:Director|Direktur|HD / Direktur|Division Head|Group Leader|Group Leader Commercial & Solution|Sales|Account Manager|BusDev|BDM|Business Development|CRO|Customer Relation Officer|Presales|Pre-Sales|Solution Architect|PMO|Project Manager');

    // Modul 2: SALES / CRM (Pipeline, Activities, Commercial Handover)
    Route::prefix('sales')->middleware('role:Director|Direktur|HD / Direktur|Division Head|Group Leader|Group Leader Commercial & Solution|Sales|Account Manager|BusDev|BDM|Business Development|CRO|Customer Relation Officer|Presales|Pre-Sales|Solution Architect|PMO|Project Manager')->group(function () {
        // Dedicated Menu 1: Pipeline & Opportunity Register
        Route::get('/pipeline', [\App\Http\Controllers\SalesCrmController::class, 'pipeline'])->name('sales.pipeline.index');
        Route::post('/pipeline/create', [\App\Http\Controllers\SalesCrmController::class, 'storeOpportunity'])->name('sales.pipeline.store');
        Route::post('/pipeline/{project}/stage', [\App\Http\Controllers\SalesCrmController::class, 'updateStage'])->name('sales.pipeline.update');

        // Dedicated Menu 2: Sales Activity Log (CRM)
        Route::get('/activities', [\App\Http\Controllers\SalesCrmController::class, 'activities'])->name('sales.activities.index');
        Route::post('/activities', [\App\Http\Controllers\SalesCrmController::class, 'storeActivity'])->name('sales.activities.store');
        Route::put('/activities/{activity}', [\App\Http\Controllers\SalesCrmController::class, 'updateActivity'])->name('sales.activities.update');
        Route::delete('/activities/{activity}', [\App\Http\Controllers\SalesCrmController::class, 'destroyActivity'])->name('sales.activities.destroy');

        // Dedicated Menu 3: Commercial Handover to Delivery/PMO
        Route::get('/handover', [\App\Http\Controllers\SalesCrmController::class, 'commercialHandoverIndex'])->name('sales.handover.index');
        Route::post('/handover/{project}/submit', [\App\Http\Controllers\SalesCrmController::class, 'submitCommercialHandover'])->name('sales.handover.submit');
    });

    // Dashboard Solution Architect & R&D (Dashboard 6)
    Route::get('/dashboard/solution-architect', [DashboardController::class, 'solutionArchitect'])
        ->name('dashboard.architect')
        ->middleware('role:Director|Direktur|HD / Direktur|Division Head|Group Leader|Group Leader Commercial & Solution|Solution Architect|Solutions Architect|Tech Develop|Tech.Develp (R&D)|R&D|Presales|Pre-Sales|Sales|Account Manager|PMO|Project Manager');

    // Dashboard Presales Engineering (Dashboard 5)
    Route::get('/dashboard/presales', [DashboardController::class, 'presales'])
        ->name('dashboard.presales')
        ->middleware('role:Director|Direktur|HD / Direktur|Division Head|Group Leader|Group Leader Commercial & Solution|Presales|Pre-Sales|Solution Architect|Solutions Architect|Sales|Account Manager|BusDev|BDM|PMO|Project Manager');

    // Modul Presales: Proposal & SOW
    Route::prefix('presales')->middleware('role:Director|Direktur|HD / Direktur|Division Head|Group Leader|Group Leader Commercial & Solution|Presales|Pre-Sales|Solution Architect|Solutions Architect|Sales|Account Manager|BusDev|BDM|PMO|Project Manager')->group(function () {
        Route::get('/proposals', [\App\Http\Controllers\PresalesProposalController::class, 'index'])->name('presales.proposals.index');
        Route::post('/proposals/{project}', [\App\Http\Controllers\PresalesProposalController::class, 'store'])->name('presales.proposals.store');
        Route::delete('/proposals/{project}/file', [\App\Http\Controllers\PresalesProposalController::class, 'destroyFile'])->name('presales.proposals.file.delete');
        Route::get('/proposals/{project}/download', [\App\Http\Controllers\PresalesProposalController::class, 'download'])->name('presales.proposals.download');
    });

    // Modul 4: MANAGED SERVICE (SERVICE DELIVERY - OPERATE & MAINTAIN)
    Route::prefix('managed-service')->middleware('role:Director|Direktur|HD / Direktur|Division Head|Group Leader|Group Leader Delivery & Operation|Group Leader Commercial & Solution|Lead Maintenance|Maintenance|PMO|Project Manager|Lead Engineer|Lead Divisi|Team Leader Engineering|Team Leader|Sales|Account Manager|BusDev|BDM')->group(function () {
        Route::get('/', [\App\Http\Controllers\ManagedServiceController::class, 'dashboard'])->name('ms.dashboard');
        Route::get('/dashboard', [\App\Http\Controllers\ManagedServiceController::class, 'dashboard'])->name('ms.dashboard.alias');
        
        // CI Assets
        Route::get('/assets', [\App\Http\Controllers\ManagedServiceController::class, 'assets'])->name('ms.assets.index');
        Route::post('/assets', [\App\Http\Controllers\ManagedServiceController::class, 'storeAsset'])->name('ms.assets.store');
        Route::get('/assets/{asset}', fn() => redirect()->route('ms.assets.index'));
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

        // Reports
        Route::get('/reports', [\App\Http\Controllers\ManagedServiceController::class, 'reports'])->name('ms.reports.index');
        Route::post('/reports', [\App\Http\Controllers\ManagedServiceController::class, 'storeReport'])->name('ms.reports.store');
    });

    // Fallback direct route
    Route::get('/dashboard/managed-service', [\App\Http\Controllers\ManagedServiceController::class, 'dashboard'])
        ->name('dashboard.ms');

    // Modul 7: CUSTOMER MANAGEMENT / CRO (Customer Relationship Officer)
    Route::prefix('cro')->middleware('role:Director|Direktur|HD / Direktur|Division Head|Group Leader|Group Leader Commercial & Solution|Group Leader Delivery & Operation|CRO|Customer Relation Officer|Sales|Account Manager|BusDev|BDM|Business Development|PMO|Project Manager|Lead Maintenance|Maintenance|Lead Engineer|Lead Divisi|Team Leader Engineering|Team Leader')->group(function () {
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
    });

    // Tasks
    Route::prefix('tasks')->group(function () {
        Route::get('/', [TaskController::class, 'index'])->name('tasks.index');
        Route::post('/', [TaskController::class, 'store'])->name('tasks.store')
            ->middleware('role:Director|Direktur|HD / Direktur|Division Head|Group Leader|Group Leader Delivery & Operation|Lead Divisi|Team Leader Engineering|Team Leader|Lead Maintenance|Lead Engineer|Managed Service');
        Route::put('/{task}', [TaskController::class, 'update'])->name('tasks.update');
        Route::delete('/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy')
            ->middleware('role:Director|Direktur|HD / Direktur|Division Head|Group Leader|Group Leader Delivery & Operation|Lead Divisi|Team Leader Engineering|Team Leader|Lead Maintenance|Lead Engineer|Managed Service');
        Route::get('/kanban-data', [TaskController::class, 'getKanbanData'])->name('tasks.kanban');
    });

    // Schedules
    Route::prefix('schedules')->group(function () {
        Route::get('/', [ScheduleController::class, 'index'])->name('schedules.index');
        Route::get('/export', [ScheduleController::class, 'exportExcel'])->name('schedules.export');
        Route::get('/export/pdf', [ScheduleController::class, 'exportPdf'])->name('schedules.export.pdf');
        Route::post('/', [ScheduleController::class, 'store'])->name('schedules.store')
            ->middleware('role:Director|Direktur|HD / Direktur|Division Head|Group Leader|Group Leader Commercial & Solution|Group Leader Delivery & Operation|PMO|Project Manager|Lead Divisi|Team Leader Engineering|Team Leader|Lead Maintenance|Lead Engineer|Managed Service|Sales|Account Manager|BusDev|BDM|Business Development|CRO|Customer Relation Officer');
        Route::put('/{schedule}', [ScheduleController::class, 'update'])->name('schedules.update')
            ->middleware('role:Director|Direktur|HD / Direktur|Division Head|Group Leader|Group Leader Commercial & Solution|Group Leader Delivery & Operation|PMO|Project Manager|Lead Divisi|Team Leader Engineering|Team Leader|Lead Maintenance|Lead Engineer|Managed Service|Sales|Account Manager|BusDev|BDM|Business Development|CRO|Customer Relation Officer');
        Route::delete('/{schedule}', [ScheduleController::class, 'destroy'])->name('schedules.destroy')
            ->middleware('role:Director|Direktur|HD / Direktur|Division Head|Group Leader|Group Leader Commercial & Solution|Group Leader Delivery & Operation|PMO|Project Manager|Lead Divisi|Team Leader Engineering|Team Leader|Lead Maintenance|Lead Engineer|Managed Service|Sales|Account Manager|BusDev|BDM|Business Development|CRO|Customer Relation Officer');
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

    // ─── PENGGUNA ─────────────────────────────────────────────────────────
    Route::prefix('users')->middleware('role:Director|Direktur|HD / Direktur|Division Head|Group Leader|Group Leader Commercial & Solution|Group Leader Delivery & Operation|PMO|Project Manager|Lead Divisi|Team Leader Engineering|Team Leader|Lead Maintenance|Lead Engineer|Managed Service')->group(function () {
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
        if ($user->hasAnyRole(['Solution Architect', 'Solutions Architect', 'Tech Develop', 'Tech.Develp (R&D)', 'R&D'])) {
            return redirect()->route('dashboard.architect');
        }
        if ($user->hasAnyRole(['Presales', 'Pre-Sales'])) {
            return redirect()->route('dashboard.presales');
        }
        if ($user->hasAnyRole(['BusDev', 'BDM', 'Business Development'])) {
            return redirect()->route('dashboard.bdm');
        }
        if ($user->hasAnyRole(['Sales', 'Account Manager', 'CRO', 'Customer Relation Officer', 'Group Leader Commercial & Solution'])) {
            return redirect()->route('dashboard.sales');
        }
        if ($user->hasAnyRole(['PMO', 'Project Manager'])) {
            return redirect()->route('pmo.dashboard');
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
    $candidates = [
        public_path('storage/' . $path),
        storage_path('app/public/' . $path),
        base_path('public/storage/' . $path),
        base_path('storage/app/public/' . $path),
        base_path('../public_html/storage/' . $path),
    ];

    foreach ($candidates as $filePath) {
        if (file_exists($filePath) && is_file($filePath)) {
            $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
            $mimes = [
                'jpg'  => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png'  => 'image/png',
                'gif'  => 'image/gif',
                'webp' => 'image/webp',
                'svg'  => 'image/svg+xml',
                'pdf'  => 'application/pdf',
            ];
            $mime = $mimes[$ext] ?? 'application/octet-stream';
            return response(file_get_contents($filePath), 200, [
                'Content-Type'  => $mime,
                'Cache-Control' => 'public, max-age=86400',
            ]);
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
        Artisan::call('migrate', ['--force' => true]);
        Artisan::call('config:clear');
        Artisan::call('cache:clear');
        Artisan::call('view:clear');
        Artisan::call('route:clear');
        return response()->json([
            'status'  => 'success',
            'message' => 'Migrasi tabel database berhasil dijalankan di hosting!',
            'output'  => trim(Artisan::output()) ?: 'Database sudah up-to-date.',
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status'  => 'error',
            'message' => 'Gagal menjalankan migrasi: ' . $e->getMessage(),
        ], 500);
    }
});

// Utility route untuk menghapus seluruh data dummy (Project, Task, Schedule, Timesheet, Attendance, Notifikasi)
// SEMENTARA SELURUH DATA USER & ENGINEER TETAP UTUH
Route::get('/clear-dummy-data', function () {
    try {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        if (Schema::hasTable('task_user')) {
            DB::table('task_user')->truncate();
        }
        if (Schema::hasTable('schedule_user')) {
            DB::table('schedule_user')->truncate();
        }

        \App\Models\Task::truncate();
        \App\Models\Schedule::truncate();
        \App\Models\Project::truncate();
        \App\Models\Timesheet::truncate();
        \App\Models\Attendance::truncate();
        \App\Models\Notification::truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        Artisan::call('config:clear');
        Artisan::call('cache:clear');
        Artisan::call('view:clear');
        Artisan::call('route:clear');

        return response()->json([
            'status'           => 'success',
            'message'          => 'Semua data dummy (project, task, schedule, timesheet, presensi, notifikasi) berhasil dibersihkan total!',
            'kept_users_count' => \App\Models\User::count(),
            'projects_count'   => \App\Models\Project::count(),
            'tasks_count'      => \App\Models\Task::count(),
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status'  => 'error',
            'message' => 'Gagal membersihkan data: ' . $e->getMessage(),
        ], 500);
    }
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

// Utility route untuk migrate database & seed user resmi & clear cache dari browser di cPanel
Route::get('/run-migration', function () {
    $outputs = [];
    try {
        Artisan::call('migrate', ['--force' => true]);
        $outputs[] = "Artisan migrate output:\n" . trim(Artisan::output());

        // 0. Reset bersih tabel database agar sinkron 100% dengan tim resmi
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \App\Models\Notification::truncate();
        \App\Models\Attendance::truncate();
        \App\Models\Timesheet::truncate();
        \App\Models\Schedule::truncate();
        \App\Models\Task::truncate();
        \App\Models\Project::truncate();
        \App\Models\Certification::truncate();
        \App\Models\Team::truncate();
        \App\Models\Division::truncate();
        \App\Models\User::truncate();
        DB::table('model_has_roles')->truncate();
        DB::table('model_has_permissions')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $outputs[] = "Pembersihan total seluruh data dummy lama: OK";

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