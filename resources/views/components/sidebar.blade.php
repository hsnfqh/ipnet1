@php
    $user = auth()->user();
    $currentRoute = request()->route() ? request()->route()->getName() : '';
    $isPmoUser = $user && $user->hasAnyRole(['PMO', 'Project Manager']);
    $isDirekturOrGl = $user && $user->hasAnyRole(['Direktur', 'HD / Direktur', 'Group Leader']);

    $navSections = [];

    if ($isPmoUser) {
        $navSections = [
            [
                'title' => 'PMO & SIKLUS PROSES',
                'items' => [
                    ['key' => 'pmo_dashboard', 'label' => 'Dashboard PMO', 'route' => 'pmo.dashboard'],
                    ['key' => 'projects',      'label' => 'Portofolio Proyek', 'route' => 'projects.index'],
                ]
            ],
            [
                'title' => 'DELIVERY & TIM',
                'items' => [
                    ['key' => 'tasks',         'label' => 'WBS & Task',    'route' => 'tasks.index'],
                    ['key' => 'schedules',     'label' => 'Jadwal & Tim',  'route' => 'schedules.index'],
                ]
            ],
            [
                'title' => 'ADMINISTRASI & PRESENSI',
                'items' => [
                    ['key' => 'timesheets',    'label' => 'Timesheet',     'route' => 'timesheets.index'],
                    ['key' => 'attendance',    'label' => 'Presensi',      'route' => 'attendance.recap'],
                    ['key' => 'users',         'label' => 'Pengguna',      'route' => 'users.index'],
                ]
            ]
        ];
    } elseif (\App\Helpers\ScopeHelper::isManagerial($user)) {
        $coreItems = [
            ['key' => 'dashboard',   'label' => 'Dashboard',  'route' => 'dashboard.lead'],
        ];
        if ($isDirekturOrGl) {
            $coreItems[] = ['key' => 'pmo_dashboard', 'label' => 'Dashboard PMO', 'route' => 'pmo.dashboard'];
        }
        $coreItems[] = ['key' => 'projects', 'label' => 'Project', 'route' => 'projects.index'];

        $navSections = [
            [
                'title' => 'MANAJEMEN',
                'items' => $coreItems
            ],
            [
                'title' => 'OPERASIONAL',
                'items' => [
                    ['key' => 'tasks',       'label' => 'Task',       'route' => 'tasks.index'],
                    ['key' => 'schedules',   'label' => 'Jadwal',     'route' => 'schedules.index'],
                ]
            ],
            [
                'title' => 'ADMINISTRASI',
                'items' => [
                    ['key' => 'timesheets',  'label' => 'Timesheet',  'route' => 'timesheets.index'],
                    ['key' => 'attendance',  'label' => 'Presensi',   'route' => 'attendance.recap'],
                    ['key' => 'users',       'label' => 'Pengguna',   'route' => 'users.index'],
                ]
            ]
        ];
    } else {
        $navSections = [
            [
                'title' => 'MENU UTAMA',
                'items' => [
                    ['key' => 'dashboard',  'label' => 'Dashboard',  'route' => 'dashboard.engineer'],
                    ['key' => 'tasks',      'label' => 'Task Saya',  'route' => 'tasks.index'],
                    ['key' => 'schedules',  'label' => 'Jadwal',     'route' => 'schedules.index'],
                ]
            ],
            [
                'title' => 'AKTIVITAS KERJA',
                'items' => [
                    ['key' => 'timesheets', 'label' => 'Timesheet',  'route' => 'timesheets.index'],
                    ['key' => 'attendance', 'label' => 'Presensi',   'route' => 'attendance.index'],
                ]
            ]
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
    style="flex-shrink:0; position:sticky; top:0; height:100vh; background:linear-gradient(165deg, #83101D 0%, #6E0C16 55%, #5C0A13 100%); border-right:1px solid rgba(255,255,255,0.12); display:flex; flex-direction:column;">

    <!-- LOGO -->
    <div style="display:flex; align-items:center; gap:11px; padding:22px 18px 19px; border-bottom:1px solid rgba(255,255,255,0.12); flex-shrink:0;">
        <div style="width:34px; height:34px; flex-shrink:0; display:flex; align-items:center; justify-content:center;">
            <img src="{{ asset('images/ipnet1.png') }}"
                 alt="IPNET Logo"
                 style="width:100%; height:100%; object-fit:contain;">
        </div>
        <div x-show="!collapsed" x-cloak style="transition:opacity 0.2s;">
            <div style="font-family:'Space Grotesk',sans-serif; font-weight:700; font-size:15px; color:white; line-height:1.1;">IP Network Solusindo</div>
            <div style="font-size:9px; color:rgba(255,255,255,0.65); letter-spacing:1px; font-weight:700; margin-top:2px;">FIELD SYSTEM MANAGEMENT</div>
        </div>
    </div>

    <!-- NAVIGATION -->
    <div style="flex:1; padding:16px 12px; display:flex; flex-direction:column; gap:14px; overflow-y:auto;">
        @foreach($navSections as $section)
            <div>
                <div x-show="!collapsed" x-cloak style="font-size:10px; font-weight:700; letter-spacing:0.8px; color:rgba(255,255,255,0.42); padding:0 10px 6px;">
                    {{ $section['title'] }}
                </div>

                <div style="display:flex; flex-direction:column; gap:2px;">
                    @foreach($section['items'] as $item)
                        @php
                            $isActive = $currentRoute === $item['route'];
                        @endphp
                        <a href="{{ route($item['route']) }}"
                           class="sidebar-nav-item{{ $isActive ? ' sidebar-nav-item-active' : '' }}"
                           style="display:flex; align-items:center; gap:12px; padding:10px 12px; border-radius:8px; text-decoration:none; font-size:13.5px; font-weight:{{ $isActive ? '700' : '500' }}; {{ $isActive ? 'background:white; color:#AF1424; box-shadow:0 6px 16px rgba(0,0,0,0.18);' : 'color:rgba(255,255,255,0.72);' }} transition:all 0.15s ease;">
                            @switch($item['key'])
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
                            @endswitch
                            <span x-show="!collapsed" x-cloak>{{ $item['label'] }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    <!-- FOOTER -->
    <div style="padding:12px; border-top:1px solid rgba(255,255,255,0.12); flex-shrink:0;">
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
