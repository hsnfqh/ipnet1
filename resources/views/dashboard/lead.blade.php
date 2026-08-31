@extends('layouts.app')

@section('title', 'Dashboard - Lead Engineer')

@section('content')
<div class="flex h-screen overflow-hidden">
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Dashboard'])
        
        <div class="p-4 sm:p-5 lg:p-[26px] animate-fade-in">
            <!-- Metric Cards -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-2.5 sm:gap-3.5 mb-4 sm:mb-5">
                <x-metric-card label="Total Project" value="{{ $projectsCount }}" icon="FolderKanban" :accent="true" href="{{ route('projects.index') }}">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                </x-metric-card>
                
                <x-metric-card label="Total Task" value="{{ $tasksCount }}" icon="ListChecks" href="{{ route('tasks.index') }}">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </x-metric-card>
                
                <x-metric-card label="Task Assigned" value="{{ $tasksAssigned }}" icon="Circle" href="{{ route('tasks.index') }}">
                    <circle cx="12" cy="12" r="10"/>
                </x-metric-card>
                
                <x-metric-card label="Task In Progress" value="{{ $tasksInProgress }}" icon="Clock" href="{{ route('tasks.index') }}">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </x-metric-card>
                
                <x-metric-card label="Task Completed" value="{{ $tasksCompleted }}" icon="CheckCircle2" href="{{ route('tasks.index') }}">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </x-metric-card>
                
                <x-metric-card label="Deadline Terdekat" value="{{ $upcomingDeadline ? $upcomingDeadline->deadline->format('d M') : '-' }}" icon="AlertTriangle" :accent="true" href="{{ route('tasks.index') }}">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </x-metric-card>
            </div>

            <!-- Load Pekerjaan Engineer / Personil Lapangan -->
            <div class="wms-card p-4 sm:p-5 mb-4 sm:mb-5">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                    <div>
                        <h3 class="font-display text-[15px] font-semibold text-wms-ink-900">
                            Load Pekerjaan Personil
                        </h3>
                        <p class="text-[11px] text-wms-ink-500 mt-0.5">
                            Pantau kapasitas & task aktif personil untuk kemudahan delegasi tugas
                        </p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        @if($canFilterTeams)
                        <div class="relative">
                            <select id="engTeamFilter" onchange="filterEngineerTeam(this.value)" 
                                    class="text-[11.5px] font-semibold px-3 py-1.5 rounded-lg border border-wms-line bg-white text-wms-ink-700 outline-none hover:border-[#C81E2C] transition cursor-pointer shadow-sm">
                                <option value="Maintenance" {{ $defaultTeamFilter === 'Maintenance' ? 'selected' : '' }}>🛠️ Tim Maintenance & Helpdesk</option>
                                <option value="All" {{ $defaultTeamFilter === 'All' ? 'selected' : '' }}>👥 Semua Tim (Lintas Divisi)</option>
                                <option value="Network" {{ $defaultTeamFilter === 'Network' ? 'selected' : '' }}>🌐 Divisi Network</option>
                                <option value="Security" {{ $defaultTeamFilter === 'Security' ? 'selected' : '' }}>🔒 Divisi Security</option>
                            </select>
                        </div>
                        @endif
                        <div class="flex gap-1 bg-wms-paper p-0.5 rounded-lg border border-wms-line">
                            <button onclick="setEngineerPeriod('week')" 
                                    class="text-[11px] font-semibold px-2.5 py-1 rounded-md transition cursor-pointer" 
                                    id="engPeriodWeek"
                                    style="background:transparent; color:#75727C;">
                                Minggu Ini
                            </button>
                            <button onclick="setEngineerPeriod('month')" 
                                    class="text-[11px] font-semibold px-2.5 py-1 rounded-md transition cursor-pointer" 
                                    id="engPeriodMonth"
                                    style="background:#C81E2C; color:#FFFFFF;">
                                Bulan Ini
                            </button>
                        </div>
                    </div>
                </div>
                <div class="w-full overflow-x-auto">
                    <div style="height: 260px; min-width: 320px;">
                        <canvas id="engineerLoadChart"></canvas>
                    </div>
                </div>
                <div class="flex flex-wrap justify-center gap-3 sm:gap-4 mt-3 text-[11px] text-wms-ink-500">
                    <span class="flex items-center gap-1.5">
                        <span style="width:10px; height:10px; border-radius:3px; background:#C81E2C;"></span>
                        Task Aktif
                    </span>
                </div>
            </div>

            <!-- Charts -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-4 sm:mb-5">
                <!-- Project Progress Chart -->
                <div class="lg:col-span-2 wms-card p-4 sm:p-5">
                    <h3 class="font-display text-[15px] font-semibold text-wms-ink-900 mb-4">Progress Project Berjalan</h3>
                    <div class="w-full overflow-x-auto">
                        <div style="height: 220px; min-width: 280px;">
                            <canvas id="projectProgressChart"></canvas>
                        </div>
                    </div>
                </div>
                
                <!-- Task Status Pie Chart -->
                <div class="wms-card p-4 sm:p-5">
                    <h3 class="font-display text-[15px] font-semibold text-wms-ink-900 mb-4">Task Berdasarkan Status</h3>
                    <div style="height: 200px;">
                        <canvas id="taskStatusChart"></canvas>
                    </div>
                    <div class="flex flex-wrap gap-2.5 justify-center mt-3">
                        @foreach($statusData as $data)
                        <span class="text-[11.5px] flex items-center gap-1.5 text-wms-ink-500">
                            <span style="width: 8px; height: 8px; border-radius: 2px; background: {{ $data['color'] }};"></span>
                            {{ $data['name'] }}
                        </span>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Recent Projects & Tasks -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <!-- Recent Projects -->
                <div class="wms-card overflow-hidden">
                    <div class="flex flex-wrap justify-between items-center gap-2 p-4 pb-0">
                        <h3 class="font-display text-[15px] font-semibold text-wms-ink-900">Project Terbaru</h3>
                        <a href="{{ route('projects.index') }}" class="text-wms-red-600 text-[12.5px] font-semibold hover:underline">Lihat semua</a>
                    </div>
                    <div class="p-2">
                        @forelse($recentProjects as $project)
                        <div class="flex flex-wrap justify-between items-center gap-2 p-2.5 border-t border-wms-line2">
                            <div class="min-w-0 flex-1 pr-2">
                                <div class="text-[13px] font-medium text-wms-ink-900 break-words">{{ $project->name }}</div>
                                <div class="text-[11.5px] text-wms-ink-500 break-words">{{ $project->client }}</div>
                            </div>
                            <x-status-badge status="{{ $project->status }}" />
                        </div>
                        @empty
                        <div class="text-center py-8 text-wms-ink-500">
                            <p class="text-[13.5px]">Belum ada project</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- Recent Tasks -->
                <div class="wms-card overflow-hidden">
                    <div class="flex flex-wrap justify-between items-center gap-2 p-4 pb-0">
                        <h3 class="font-display text-[15px] font-semibold text-wms-ink-900">Task Terbaru</h3>
                        <a href="{{ route('tasks.index') }}" class="text-wms-red-600 text-[12.5px] font-semibold hover:underline">Lihat semua</a>
                    </div>
                    <div class="p-2">
                        @forelse($recentTasks as $task)
                        <div class="flex flex-wrap justify-between items-center gap-2 p-2.5 border-t border-wms-line2">
                            <div class="min-w-0 flex-1 pr-2">
                                <div class="text-[13px] font-medium text-wms-ink-900 break-words">{{ $task->title }}</div>
                                <div class="text-[11.5px] text-wms-ink-500 break-words">{{ $task->engineer?->name ?? 'Unassigned' }}</div>
                            </div>
                            <x-status-badge status="{{ $task->status }}" />
                        </div>
                        @empty
                        <div class="text-center py-8 text-wms-ink-500">
                            <p class="text-[13.5px]">Belum ada task</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const engineerMonthData = @json($engineerLoadMonthData);
    const engineerWeekData  = @json($engineerLoadWeekData);
    const defaultTeam       = @json($defaultTeamFilter);
    let currentPeriod       = 'month';
    let currentTeam         = defaultTeam || 'All';
    let engineerChart       = null;

    function getFilteredEngineerData() {
        const rawData = (currentPeriod === 'week') ? engineerWeekData : engineerMonthData;
        if (currentTeam === 'All') {
            return rawData.slice().sort((a, b) => b.active - a.active);
        }
        return rawData.filter(d => d.division === currentTeam).sort((a, b) => b.active - a.active);
    }

    function formatEngineerLabel(d) {
        if (currentTeam === 'All') {
            return d.name + ' (' + d.division + ')';
        }
        return d.name + (d.position ? ' — ' + d.position : '');
    }

    function updateEngineerChart() {
        if (!engineerChart) return;
        const dataToUse = getFilteredEngineerData();
        engineerChart.data.labels = dataToUse.map(formatEngineerLabel);
        engineerChart.data.datasets[0].data = dataToUse.map(d => d.active);
        engineerChart.update();
    }

    function filterEngineerTeam(team) {
        currentTeam = team;
        updateEngineerChart();
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Chart Load Pekerjaan Engineer
        const ctx3 = document.getElementById('engineerLoadChart').getContext('2d');
        const initialData = getFilteredEngineerData();
        
        engineerChart = new Chart(ctx3, {
            type: 'bar',
            data: {
                labels: initialData.map(formatEngineerLabel),
                datasets: [
                    {
                        label: 'Task Aktif',
                        data: initialData.map(d => d.active),
                        backgroundColor: '#C81E2C',
                        borderRadius: 4,
                    }
                ]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false,
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.parsed.x + ' task';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        stacked: false,
                        ticks: {
                            stepSize: 1,
                            font: { size: 10 }
                        },
                        grid: {
                            display: true,
                            color: '#EFEDEB'
                        }
                    },
                    y: {
                        stacked: false,
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: { size: 11, weight: '600' }
                        }
                    }
                }
            }
        });

     
        const projectData = @json($projectProgressData);
        const ctx1 = document.getElementById('projectProgressChart').getContext('2d');
        
        const barGradient = ctx1.createLinearGradient(0, 0, 0, 220);
        barGradient.addColorStop(0, '#EF4444');
        barGradient.addColorStop(0.5, '#DC2626');
        barGradient.addColorStop(1, '#991B1B');
        
        new Chart(ctx1, {
            type: 'bar',
            data: {
                labels: projectData.map(d => d.name),
                datasets: [{
                    label: 'Progress (%)',
                    data: projectData.map(d => d.progress),
                    backgroundColor: barGradient,
                    borderRadius: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false,
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.parsed.y + '%';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        min: 0,
                        max: 100,
                        ticks: {
                            stepSize: 20,
                            callback: function(value) {
                                return value + '%';
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

      
        const statusData = @json($statusData);
        const ctx2 = document.getElementById('taskStatusChart').getContext('2d');
        
        const gradientColors = [
            {
                gradient: function(ctx) {
                    const g = ctx.createRadialGradient(60, 60, 10, 100, 100, 120);
                    g.addColorStop(0, '#60A5FA');
                    g.addColorStop(0.7, '#3B82F6');
                    g.addColorStop(1, '#1D4ED8');
                    return g;
                }
            },
            {
                gradient: function(ctx) {
                    const g = ctx.createRadialGradient(60, 60, 10, 100, 100, 120);
                    g.addColorStop(0, '#FBBF24');
                    g.addColorStop(0.7, '#F59E0B');
                    g.addColorStop(1, '#ffae0b');
                    return g;
                }
            },
            {
                gradient: function(ctx) {
                    const g = ctx.createRadialGradient(60, 60, 10, 100, 100, 120);
                    g.addColorStop(0, '#A78BFA');
                    g.addColorStop(0.7, '#8B5CF6');
                    g.addColorStop(1, '#7c2bff');
                    return g;
                }
            },
            {
                gradient: function(ctx) {
                    const g = ctx.createRadialGradient(60, 60, 10, 100, 100, 120);
                    g.addColorStop(0, '#34D399');
                    g.addColorStop(0.7, '#13cf90');
                    g.addColorStop(1, '#10B981');
                    return g;
                }
            }
        ];

        const gradients = gradientColors.map(function(g) {
            return g.gradient(ctx2);
        });

        new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: statusData.map(d => d.name),
                datasets: [{
                    data: statusData.map(d => d.value),
                    backgroundColor: gradients,
                    borderWidth: 2,
                    borderColor: '#FFFFFF',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '60%',
                plugins: {
                    legend: {
                        display: false,
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? Math.round((context.parsed / total) * 100) : 0;
                                return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
    });

    // FILTER PERIODE CHART LOAD ENGINEER (Minggu/Bulan)
    function setEngineerPeriod(period) {
        currentPeriod = period;
        const isWeek = period === 'week';
        const weekBtn = document.getElementById('engPeriodWeek');
        const monthBtn = document.getElementById('engPeriodMonth');

        if (isWeek) {
            weekBtn.style.background = '#C81E2C';
            weekBtn.style.color = '#FFFFFF';
            monthBtn.style.background = 'transparent';
            monthBtn.style.color = '#75727C';
        } else {
            monthBtn.style.background = '#C81E2C';
            monthBtn.style.color = '#FFFFFF';
            weekBtn.style.background = 'transparent';
            weekBtn.style.color = '#75727C';
        }
        
        updateEngineerChart();
    }
</script>
@endpush
@endsection