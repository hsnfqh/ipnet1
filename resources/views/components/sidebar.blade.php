@php
    $user = auth()->user();
    $currentRoute = request()->route() ? request()->route()->getName() : '';
    $isExecutiveOrGl = $user && $user->hasAnyRole(['Director', 'Direktur', 'HD / Direktur', 'Division Head', 'Group Leader', 'Group Leader Commercial & Solution', 'Group Leader Delivery & Operation']);
    $isPmoUser = $user && $user->hasAnyRole(['PMO', 'Project Manager']);
    $isMaintenance = $user && ($user->hasAnyRole(['Lead Maintenance', 'Maintenance']) || ($user->division && str_contains(strtolower($user->division->name), 'maintenance')));
    $isArchitect = $user && $user->hasAnyRole(['Solution Architect', 'Solutions Architect', 'SA', 'Tech Develop', 'Tech.Develp (R&D)', 'R&D']);
    $isBdm = $user && $user->hasAnyRole(['BDM', 'BusDev', 'Business Development']);
    $isPresales = $user && $user->hasAnyRole(['Presales', 'Pre-Sales']);
    $isAdminSupport = $user && $user->hasAnyRole(['Admin Support', 'Admin Logistik', 'Admin']);
    $isCro = $user && $user->hasAnyRole(['CRO', 'Customer Relation Officer', 'Customer Relationship Officer']);
    $isSales = $user && $user->hasAnyRole(['Sales', 'Account Manager']);

    $navItems = [];

    // Hitung permohonan persetujuan draft untuk pimpinan (Susanto & Hariyadi)
    $pendingApprovalsCount = 0;
    if ($isExecutiveOrGl && \Illuminate\Support\Facades\Schema::hasTable('projects')) {
        $isSusanto = str_contains(strtolower($user->name ?? ''), 'susanto') || $user->hasAnyRole(['Division Head', 'Head Divisi', 'Group Leader', 'HD / Direktur', 'Group Leader Delivery & Operation', 'Group Leader Commercial & Solution']);
        $isHariyadi = str_contains(strtolower($user->name ?? ''), 'hariyadi') || $user->hasAnyRole(['Director', 'Direktur', 'HD / Direktur']);

        $pendingApprovalsCount = \App\Models\Project::where(function($q) {
                $q->whereNotNull('handover_data')
                  ->orWhere('status', 'Draft')
                  ->orWhere('stage', 'Draft');
            })
            ->whereNull('deleted_at')
            ->get()
            ->filter(function($p) use ($isSusanto, $isHariyadi) {
                $hd = is_array($p->handover_data) ? $p->handover_data : (json_decode($p->handover_data ?? '', true) ?: []);
                $approvals = $hd['draft_approvals'] ?? [];
                $needHead = !empty($approvals['head']['assigned']) && empty($approvals['head']['approved']);
                $needDirector = !empty($approvals['director']['assigned']) && empty($approvals['director']['approved']);

                if ($isSusanto && $needHead) return true;
                if ($isHariyadi && $needDirector) return true;

                $isDraftState = in_array(strtolower($p->status ?? ''), ['draft']) || in_array(strtolower($p->stage ?? ''), ['draft']);
                if ($isDraftState) {
                    if ($isSusanto && empty($approvals['head']['approved'])) return true;
                    if ($isHariyadi && empty($approvals['director']['approved'])) return true;
                }
                return false;
            })->count();
    }

    if ($isExecutiveOrGl) {
        $navItems = [
            ['key' => 'dashboard',        'label' => 'Dashboard Utama',     'route' => 'dashboard.lead'],
            ['key' => 'draft_approvals',  'label' => 'Persetujuan Draft',   'route' => 'projects.index', 'params' => ['status' => 'Draft'], 'badge' => $pendingApprovalsCount],
            ['key' => 'pmo_dashboard',    'label' => 'Dashboard PMO',       'route' => 'pmo.dashboard'],
            ['key' => 'managed_service',  'label' => 'Managed Service',     'route' => 'ms.dashboard'],
            ['key' => 'acquire',          'label' => 'Peluang & Pipeline',  'route' => 'acquire.index'],
            ['key' => 'projects',         'label' => 'Daftar Proyek',       'route' => 'projects.index'],
            ['key' => 'tasks',            'label' => 'Daftar Tugas',        'route' => 'tasks.index'],
            ['key' => 'schedules',        'label' => 'Jadwal Kerja',        'route' => 'schedules.index'],
            ['key' => 'timesheets',       'label' => 'Timesheet',           'route' => 'timesheets.index'],
            ['key' => 'attendance',       'label' => 'Presensi',            'route' => 'attendance.recap'],
            ['key' => 'users',            'label' => 'Pengguna',            'route' => 'users.index'],
        ];
    } elseif ($isAdminSupport) {
        $navItems = [
            ['key' => 'admin_dashboard',  'label' => 'Dashboard',             'route' => 'admin_support.dashboard'],
            ['key' => 'admin_documents',  'label' => 'Register Dokumen',      'route' => 'admin_support.documents.index'],
            ['key' => 'admin_checklists', 'label' => 'Kelengkapan Dokumen',   'route' => 'admin_support.checklists.index'],
            ['key' => 'admin_logistics',  'label' => 'Surat Jalan & DO',      'route' => 'admin_support.logistics.index'],
            ['key' => 'admin_inventory',  'label' => 'Master Serial Number',  'route' => 'admin_support.inventory.index'],
            ['key' => 'admin_assets',     'label' => 'Aset & Alat Kerja',     'route' => 'admin_support.assets.index'],
            ['key' => 'admin_handovers',  'label' => 'Arsip & Handover',      'route' => 'admin_support.handovers.index'],
            ['key' => 'timesheets',       'label' => 'Timesheet',             'route' => 'timesheets.index'],
        ];
    } elseif ($isCro) {
        $navItems = [
            ['key' => 'cro',               'label' => 'Dashboard',         'route' => 'cro.dashboard'],
            ['key' => 'cro_engagements',   'label' => 'Relasi & Meeting',  'route' => 'cro.engagements.index'],
            ['key' => 'cro_satisfaction',  'label' => 'Kepuasan (CSAT)',   'route' => 'cro.satisfaction.index'],
            ['key' => 'cro_concerns',      'label' => 'Concern & Isu',     'route' => 'cro.concerns.index'],
            ['key' => 'cro_retention',     'label' => 'Retensi Akun',      'route' => 'cro.retention.index'],
            ['key' => 'cro_opportunities', 'label' => 'Peluang Ekspansi',  'route' => 'cro.opportunities.index'],
            ['key' => 'clients',           'label' => 'Database Klien',    'route' => 'clients.index'],
            ['key' => 'timesheets',        'label' => 'Timesheet',         'route' => 'timesheets.index'],
        ];
    } elseif ($isMaintenance) {
        // Dedicated Managed Service & Maintenance Portal (Doris, Mario, Eris)
        $navItems = [
            ['key' => 'ms_dashboard', 'label' => 'Dashboard',         'route' => 'ms.dashboard'],
            ['key' => 'ms_tickets',   'label' => 'Tiket & SLA',          'route' => 'ms.tickets.index'],
            ['key' => 'ms_assets',    'label' => 'Aset Perangkat',       'route' => 'ms.assets.index'],
            ['key' => 'tasks',        'label' => 'Daftar Tugas',         'route' => 'tasks.index'],
            ['key' => 'schedules',    'label' => 'Jadwal Kerja',         'route' => 'schedules.index'],
            ['key' => 'timesheets',   'label' => 'Timesheet',            'route' => 'timesheets.index'],
            ['key' => 'attendance',   'label' => 'Presensi',             'route' => \App\Helpers\ScopeHelper::isTeamLeader($user) ? 'attendance.recap' : 'attendance.index'],
        ];
        if (\App\Helpers\ScopeHelper::isTeamLeader($user)) {
            $navItems[] = ['key' => 'users', 'label' => 'Pengguna', 'route' => 'users.index'];
        }
    } elseif ($isPmoUser) {
        // Project Manager & PMO: Kontrol pengiriman proyek (Deliver)
        $navItems = [
            ['key' => 'pmo_dashboard',   'label' => 'Dashboard',           'route' => 'pmo.dashboard'],
            ['key' => 'projects',        'label' => 'Daftar Proyek',       'route' => 'projects.index'],
            ['key' => 'tasks',           'label' => 'Daftar Tugas',        'route' => 'tasks.index'],
            ['key' => 'schedules',       'label' => 'Jadwal Kerja',        'route' => 'schedules.index'],
            ['key' => 'timesheets',      'label' => 'Timesheet',           'route' => 'timesheets.index'],
        ];
    } elseif ($isArchitect) {
        $navItems = [
            ['key' => 'dashboard',  'label' => 'Dashboard',          'route' => 'dashboard.architect'],
            ['key' => 'proposals',  'label' => 'Desain & SOW',       'route' => 'presales.proposals.index'],
            ['key' => 'vendors',    'label' => 'Mitra Principal',    'route' => 'vendors.index'],
            ['key' => 'schedules',  'label' => 'Jadwal Kerja',       'route' => 'schedules.index'],
            ['key' => 'timesheets', 'label' => 'Timesheet',          'route' => 'timesheets.index'],
        ];
    } elseif ($isBdm) {
        $navItems = [
            ['key' => 'dashboard',     'label' => 'Dashboard',           'route' => 'dashboard.bdm'],
            ['key' => 'opportunities', 'label' => 'Inisiasi Peluang',    'route' => 'bdm.opportunities.index'],
            ['key' => 'intelligence',  'label' => 'Market Intelligence', 'route' => 'bdm.intelligence.index'],
            ['key' => 'partnerships',  'label' => 'Mitra Vendor',        'route' => 'bdm.partnerships.index'],
            ['key' => 'clients',       'label' => 'Database Klien',      'route' => 'clients.index'],
            ['key' => 'timesheets',    'label' => 'Timesheet',           'route' => 'timesheets.index'],
        ];
    } elseif ($isPresales) {
        $navItems = [
            ['key' => 'dashboard',  'label' => 'Dashboard',          'route' => 'dashboard.presales'],
            ['key' => 'proposals',  'label' => 'Proposal & SOW',      'route' => 'presales.proposals.index'],
            ['key' => 'schedules',  'label' => 'Jadwal Kerja',        'route' => 'schedules.index'],
            ['key' => 'timesheets', 'label' => 'Timesheet',           'route' => 'timesheets.index'],
        ];
    } elseif ($isSales) {
        $navItems = [
            ['key' => 'dashboard',  'label' => 'Dashboard',           'route' => 'dashboard.sales'],
            ['key' => 'clients',    'label' => 'Client',              'route' => 'clients.index'],
            ['key' => 'projects',   'label' => 'Project',             'route' => 'sales.pipeline.index'],
            ['key' => 'inventory',  'label' => 'Inventory',           'route' => 'inventory.index'],
            ['key' => 'vendors',    'label' => 'Vendor',              'route' => 'vendors.index'],
            ['key' => 'handover',   'label' => 'Serah Terima Proyek',  'route' => 'sales.handover.index'],
            ['key' => 'schedules',  'label' => 'Jadwal Kerja',        'route' => 'schedules.index'],
            ['key' => 'timesheets', 'label' => 'Timesheet',            'route' => 'timesheets.index'],
        ];
    } elseif (\App\Helpers\ScopeHelper::isTeamLeader($user)) {
        $navItems = [
            ['key' => 'dashboard',   'label' => 'Dashboard',         'route' => 'dashboard.lead'],
            ['key' => 'projects',    'label' => 'Daftar Proyek',     'route' => 'projects.index'],
            ['key' => 'tasks',       'label' => 'Penugasan Tim',     'route' => 'tasks.index'],
            ['key' => 'schedules',   'label' => 'Jadwal Kerja',      'route' => 'schedules.index'],
            ['key' => 'timesheets',  'label' => 'Timesheet',         'route' => 'timesheets.index'],
            ['key' => 'attendance',  'label' => 'Presensi',          'route' => 'attendance.recap'],
            ['key' => 'users',       'label' => 'Pengguna',          'route' => 'users.index'],
        ];
    } else {
        $navItems = [
            ['key' => 'dashboard',  'label' => 'Dashboard',          'route' => 'dashboard.engineer'],
            ['key' => 'tasks',      'label' => 'Tugas Saya',         'route' => 'tasks.index'],
            ['key' => 'schedules',  'label' => 'Jadwal Kerja',       'route' => 'schedules.index'],
            ['key' => 'timesheets', 'label' => 'Timesheet',          'route' => 'timesheets.index'],
            ['key' => 'attendance', 'label' => 'Presensi',           'route' => 'attendance.index'],
        ];
    }
@endphp

<style>
    #app-sidebar.is-ready {
        transition: width 0.2s ease;
    }

    @media (hover: hover) {
        #app-sidebar .sidebar-nav-item:not(.sidebar-nav-item-active):hover,
        #app-sidebar .sidebar-action:hover {
            background: rgba(255, 255, 255, 0.12) !important;
            color: #fff !important;
            transform: translateX(3px);
        }

        #app-sidebar .sidebar-nav-item:hover svg,
        #app-sidebar .sidebar-action:hover svg {
            opacity: 1;
            transform: scale(1.05);
        }

        #app-sidebar .sidebar-nav-item-active:hover {
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.24) !important;
            transform: translateX(2px);
        }
    }

    #app-sidebar .sidebar-nav-item:active,
    #app-sidebar .sidebar-action:active {
        opacity: 0.85;
        transform: scale(0.98);
    }

    #app-sidebar .sidebar-nav-item:focus-visible,
    #app-sidebar .sidebar-action:focus-visible {
        outline: 2px solid rgba(255, 255, 255, 0.9);
        outline-offset: 2px;
    }

    @media (prefers-reduced-motion: reduce) {
        #app-sidebar,
        #app-sidebar .sidebar-nav-item,
        #app-sidebar .sidebar-action,
        #app-sidebar .sidebar-action svg {
            transition: none !important;
        }
    }
</style>

<div
    id="app-sidebar"
    x-data="{
        collapsed: localStorage.getItem('sidebarCollapsed') === 'true',
        toggle() {
            this.collapsed = !this.collapsed;
            localStorage.setItem('sidebarCollapsed', this.collapsed);
        }
    }"
    :class="collapsed ? 'w-[76px]' : 'w-[246px]'"
    style="flex-shrink:0; position:sticky; top:0; height:100vh; background:#7A0813; border-right:1px solid rgba(255,255,255,0.12); display:flex; flex-direction:column; overflow:hidden;">

    {{-- Layered Geometric Faceted Red Planes (Matching Official Reference Image) --}}
    <div style="position:absolute; inset:0; pointer-events:none; overflow:hidden; user-select:none; z-index:0;">
        <svg style="width:100%; height:100%; object-fit:cover;" viewBox="0 0 300 1000" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <linearGradient id="sbGradBase" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="#9E0E1D" />
                    <stop offset="50%" stop-color="#730810" />
                    <stop offset="100%" stop-color="#4D030A" />
                </linearGradient>
                <linearGradient id="sbGrad1" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="#C61828" />
                    <stop offset="100%" stop-color="#9E0E1D" />
                </linearGradient>
                <linearGradient id="sbGrad2" x1="100%" y1="0%" x2="0%" y2="100%">
                    <stop offset="0%" stop-color="#B01423" />
                    <stop offset="100%" stop-color="#7A0813" />
                </linearGradient>
                <linearGradient id="sbGrad3" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="#940E1B" />
                    <stop offset="100%" stop-color="#5A040C" />
                </linearGradient>
                <linearGradient id="sbGradHighlight" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="#FFA8B2" stop-opacity="0.32" />
                    <stop offset="100%" stop-color="#FFFFFF" stop-opacity="0" />
                </linearGradient>
                <filter id="sbDropShadow" x="-20%" y="-20%" width="140%" height="140%">
                    <feDropShadow dx="-6" dy="12" stdDeviation="16" flood-color="#2D0206" flood-opacity="0.55" />
                </filter>
            </defs>

            <!-- Base Gradient Background -->
            <rect width="300" height="1000" fill="url(#sbGradBase)" />

            <!-- Top Facet Plane -->
            <polygon points="0,0 300,0 300,260 0,140" fill="url(#sbGrad1)" />

            <!-- Crossing Diagonal Facet 1 -->
            <polygon points="0,90 300,340 300,580 0,330" fill="url(#sbGrad2)" filter="url(#sbDropShadow)" />

            <!-- Reverse Intersecting Facet 2 -->
            <polygon points="300,240 0,510 0,740 300,470" fill="url(#sbGrad1)" opacity="0.92" filter="url(#sbDropShadow)" />

            <!-- Lower Diagonal Facet 3 -->
            <polygon points="0,590 300,840 300,1000 0,1000" fill="url(#sbGrad3)" filter="url(#sbDropShadow)" />

            <!-- Ambient Luminous Highlight Streaks along Facet Angles -->
            <polygon points="0,90 300,340 300,352 0,102" fill="url(#sbGradHighlight)" />
            <polygon points="300,240 0,510 0,522 300,252" fill="url(#sbGradHighlight)" />
            <polygon points="0,590 300,840 300,852 0,602" fill="url(#sbGradHighlight)" />
        </svg>
    </div>

    <!-- LOGO -->
    <div style="position:relative; z-index:10; display:flex; align-items:center; gap:11px; padding:22px 18px 19px; border-bottom:1px solid rgba(255,255,255,0.12); flex-shrink:0;">
        <div style="width:34px; height:34px; flex-shrink:0; display:flex; align-items:center; justify-content:center;">
            <img src="{{ asset('images/ipnet1.png') }}"
                 alt="IPNET Logo"
                 style="width:100%; height:100%; object-fit:contain;">
        </div>
        <div x-show="!collapsed" x-cloak style="transition:opacity 0.2s;">
            <div style="font-family:'Inter',sans-serif; font-weight:700; font-size:15px; color:white; line-height:1.1;">IP Network Solusindo</div>
            <div style="font-size:9px; color:rgba(255,255,255,0.65); letter-spacing:1px; font-weight:700; margin-top:2px;">FIELD SYSTEM MANAGEMENT</div>
        </div>
    </div>

    <!-- NAVIGATION -->
    <div style="position:relative; z-index:10; flex:1; padding:16px 12px; display:flex; flex-direction:column; gap:3px; overflow-y:auto;">
        <div x-show="!collapsed" x-cloak style="font-size:10.5px; font-weight:700; letter-spacing:0.8px; color:rgba(255,255,255,0.48); padding:0 10px 8px;">MENU UTAMA</div>

        @foreach($navItems as $item)
            @php
                $itemParams = $item['params'] ?? [];
                $itemUrl = !empty($itemParams) ? route($item['route'], $itemParams) : route($item['route']);
                if (isset($itemParams['status'])) {
                    $isActive = ($currentRoute === $item['route']) && (request('status') === $itemParams['status']);
                } else {
                    $isActive = ($currentRoute === $item['route']) && (!request()->has('status') || request('status') === '');
                }
            @endphp
            <a href="{{ $itemUrl }}"
               class="sidebar-nav-item{{ $isActive ? ' sidebar-nav-item-active' : '' }}"
               style="display:flex; align-items:center; gap:12px; padding:10px 14px; border-radius:12px; text-decoration:none; font-size:13.5px; font-weight:{{ $isActive ? '700' : '500' }}; {{ $isActive ? 'background:white; color:#8F0A0D; box-shadow:0 6px 18px rgba(0,0,0,0.22);' : 'color:rgba(255,255,255,0.85);' }} transition:all 0.15s ease;">
                @switch($item['key'])
                    @case('opportunities')
                    <svg style="width:17px; height:17px; flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    @break
                    @case('intelligence')
                    <svg style="width:17px; height:17px; flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    @break
                    @case('pipeline')
                    <svg style="width:17px; height:17px; flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    @break
                    @case('activities')
                    <svg style="width:17px; height:17px; flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    @break
                    @case('handover')
                    <svg style="width:17px; height:17px; flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    @break
                    @case('partnerships')
                    <svg style="width:17px; height:17px; flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    @break
                    @case('acquire')
                    <svg style="width:17px; height:17px; flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    @break
                    @case('managed_service')
                    @case('ms_dashboard')
                    <svg style="width:17px; height:17px; flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    @break
                    @case('cro')
                    <svg style="width:17px; height:17px; flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                    @break
                    @case('cro_engagements')
                    <svg style="width:17px; height:17px; flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    @break
                    @case('cro_satisfaction')
                    <svg style="width:17px; height:17px; flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                    </svg>
                    @break
                    @case('cro_concerns')
                    <svg style="width:17px; height:17px; flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    @break
                    @case('cro_retention')
                    <svg style="width:17px; height:17px; flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    @break
                    @case('admin_dashboard')
                    <svg style="width:17px; height:17px; flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    @break
                    @case('admin_documents')
                    <svg style="width:17px; height:17px; flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    @break
                    @case('admin_checklists')
                    <svg style="width:17px; height:17px; flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                    @break
                    @case('admin_logistics')
                    <svg style="width:17px; height:17px; flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h2m-8 0a2 2 0 100 4 2 2 0 000-4zm10 0a2 2 0 100 4 2 2 0 000-4z"/>
                    </svg>
                    @break
                    @case('admin_inventory')
                    <svg style="width:17px; height:17px; flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                    @break
                    @case('admin_assets')
                    <svg style="width:17px; height:17px; flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    @break
                    @case('admin_handovers')
                    <svg style="width:17px; height:17px; flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                    </svg>
                    @break
                    @case('cro_opportunities')
                    <svg style="width:17px; height:17px; flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                    @break
                    @case('ms_tickets')
                    <svg style="width:17px; height:17px; flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                    </svg>
                    @break
                    @case('ms_assets')
                    <svg style="width:17px; height:17px; flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
                    </svg>
                    @break
                    @case('ms_pm')
                    <svg style="width:17px; height:17px; flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    @break
                    @case('pmo_dashboard')
                    <svg style="width:17px; height:17px; flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/>
                    </svg>
                    @break
                    @case('dashboard')
                    <svg style="width:17px; height:17px; flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                    </svg>
                    @break
                    @case('proposals')
                    <svg style="width:17px; height:17px; flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    @break
                    @case('clients')
                    <svg style="width:17px; height:17px; flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    @break
                    @case('inventory')
                    <svg style="width:17px; height:17px; flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    @break
                    @case('vendors')
                    <svg style="width:17px; height:17px; flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                    @break
                    @case('projects')
                    <svg style="width:17px; height:17px; flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                    </svg>
                    @break
                    @case('tasks')
                    <svg style="width:17px; height:17px; flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                    @break
                    @case('schedules')
                    <svg style="width:17px; height:17px; flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    @break
                    @case('timesheets')
                    <svg style="width:17px; height:17px; flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    @break
                    @case('attendance')
                    <svg style="width:17px; height:17px; flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    @break
                    @case('users')
                    <svg style="width:17px; height:17px; flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    @break
                    @case('profile')
                    <svg style="width:17px; height:17px; flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    @break
                    @case('draft_approvals')
                    <svg style="width:17px; height:17px; flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    @break
                @endswitch
                <span x-show="!collapsed" x-cloak>{{ $item['label'] }}</span>
                @if(isset($item['badge']) && $item['badge'] > 0)
                    <span x-show="!collapsed" x-cloak style="margin-left:auto; background:{{ $isActive ? '#8F0A0D' : '#ef4444' }}; color:white; font-size:10px; font-weight:800; padding:2px 7.5px; border-radius:9999px; line-height:1.2; box-shadow:0 1px 4px rgba(0,0,0,0.2);">
                        {{ $item['badge'] }}
                    </span>
                @endif
            </a>
        @endforeach
    </div>

    <!-- FOOTER -->
    <div style="position:relative; z-index:10; padding:12px; border-top:1px solid rgba(255,255,255,0.12); flex-shrink:0;">
        <form action="{{ route('logout') }}" method="POST" style="width:100%;">
            @csrf
            <button type="submit" class="sidebar-action" style="display:flex; align-items:center; gap:12px; padding:10px 12px; border-radius:8px; width:100%; background:transparent; border:none; cursor:pointer; color:rgba(255,255,255,0.72); font-size:13.5px; transition:all 0.15s ease;">
                <svg style="width:17px; height:17px; flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                <span x-show="!collapsed" x-cloak>Keluar</span>
            </button>
        </form>
        <button @click="toggle()" class="sidebar-action" style="display:flex; align-items:center; gap:12px; padding:10px 12px; border-radius:8px; width:100%; background:transparent; border:none; cursor:pointer; color:rgba(255,255,255,0.42); font-size:12px; margin-top:2px; transition:all 0.15s ease;">
            <svg style="width:15px; height:15px; flex-shrink:0; transition:transform 0.2s;" :style="{ transform: collapsed ? 'rotate(180deg)' : 'none' }" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
            <span x-show="!collapsed" x-cloak>Sembunyikan</span>
        </button>
    </div>
</div>

<script>
    (function() {
        var isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
        var sidebar = document.getElementById('app-sidebar');
        if (sidebar) {
            sidebar.classList.add(isCollapsed ? 'w-[76px]' : 'w-[246px]');
            requestAnimationFrame(function() {
                sidebar.classList.add('is-ready');
            });
        }
    })();
</script>
