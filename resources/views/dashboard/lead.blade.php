@extends('layouts.app')

@section('title', 'Dashboard - Lead Engineer')

@section('content')
<div class="flex h-screen overflow-hidden" x-data="{ leadPhotoModal: false, leadPhotoUrl: '', leadPhotoName: '', leadPhotoNote: '', previewPhoto(url, name, note) { this.leadPhotoUrl = url; this.leadPhotoName = name; this.leadPhotoNote = note || ''; this.leadPhotoModal = true; } }">
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
                                <option value="Maintenance" {{ $defaultTeamFilter === 'Maintenance' ? 'selected' : '' }}>Tim Maintenance & Helpdesk</option>
                                <option value="All" {{ $defaultTeamFilter === 'All' ? 'selected' : '' }}>Semua Tim (Lintas Divisi)</option>
                                <option value="Network" {{ $defaultTeamFilter === 'Network' ? 'selected' : '' }}>Divisi Network</option>
                                <option value="Security" {{ $defaultTeamFilter === 'Security' ? 'selected' : '' }}>Divisi Security</option>
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

            <!-- Presensi Personil Hari Ini (Live Feed & Foto) -->
            <div class="wms-card overflow-hidden mt-4 sm:mt-5">
                <div class="flex flex-wrap justify-between items-center gap-2 p-4 border-b border-wms-line2 bg-[#FAF9F8]">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-red-50 text-[#C81E2C] border border-red-100 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-display text-[15px] font-bold text-wms-ink-900">Presensi Personil Hari Ini</h3>
                            <p class="text-[11px] text-wms-ink-500">Live monitoring kehadiran, verifikasi GPS, dan foto bukti personil lapangan</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-semibold rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            {{ $clockInCount }} Hadir
                        </span>
                        @if($outOfRangeCount > 0)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-semibold rounded-lg bg-amber-50 text-amber-700 border border-amber-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                            {{ $outOfRangeCount }} Luar Kantor
                        </span>
                        @endif
                        <a href="{{ route('attendance.recap') }}" class="text-wms-red-600 text-[12.5px] font-semibold hover:underline ml-1">
                            Buka Rekap &amp; Foto &rarr;
                        </a>
                    </div>
                </div>

                <div class="divide-y divide-wms-line2">
                    @forelse($todayAttendances as $att)
                    <div class="p-3.5 sm:px-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-[#FBFBFA] transition">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-9 h-9 rounded-xl bg-[#F1F0EE] border border-[#E7E5E3] flex items-center justify-center font-bold text-[12.5px] text-[#17151C] flex-shrink-0">
                                {{ strtoupper(substr($att->user?->name ?? '?', 0, 2)) }}
                            </div>
                            <div class="min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="text-[13.5px] font-semibold text-[#17151C]">{{ $att->user?->name ?? 'Personil' }}</span>
                                    <span class="text-[11px] font-medium text-[#75727C]">({{ $att->user?->division?->name ?? $att->user?->role ?? '-' }})</span>
                                </div>
                                <div class="flex items-center gap-2 text-[11.5px] text-[#75727C] mt-0.5 flex-wrap">
                                    <span>Clock In: <strong class="font-mono text-[#17151C]">{{ $att->created_at->format('H:i') }} WIB</strong></span>
                                    <span>&bull;</span>
                                    <span>Jarak: <strong class="font-mono text-[#17151C]">{{ $att->distance_meters }} m</strong></span>
                                    @if($att->address)
                                    <span>&bull;</span>
                                    <span class="max-w-[260px] truncate" title="{{ $att->address }}">{{ $att->address }}</span>
                                    @endif
                                </div>
                                @if($att->note)
                                <div class="mt-1 text-[11.5px] text-amber-800 bg-amber-50 border border-amber-200/80 px-2 py-0.5 rounded-md inline-flex items-center gap-1">
                                    <span>📝 Alasan: <strong>{{ $att->note }}</strong></span>
                                </div>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center gap-2 self-end sm:self-center flex-shrink-0">
                            @if($att->is_within_range)
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-semibold rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                Hadir (Kantor)
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-semibold rounded-lg bg-amber-50 text-amber-700 border border-amber-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                Luar Jangkauan
                            </span>
                            @endif

                            @if($att->photo_url)
                            <button type="button" 
                                    @click="previewPhoto('{{ $att->photo_url }}', '{{ addslashes($att->user?->name ?? 'Personil') }}', '{{ addslashes($att->note ?? '') }}')"
                                    class="px-2.5 py-1 rounded-lg bg-[#FDF1F2] hover:bg-[#F9E0E2] text-[#C81E2C] text-[11.5px] font-semibold transition flex items-center gap-1.5 border border-[#FADADF] cursor-pointer">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <span>Lihat Foto</span>
                            </button>
                            @else
                            <span class="text-[11px] text-[#948F99] italic px-1">Tanpa Foto</span>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="py-10 text-center text-[#75727C]">
                        <div class="w-10 h-10 rounded-xl bg-[#F1F0EE] flex items-center justify-center mx-auto mb-2 text-[#75727C]">
                            <svg class="w-5 h-5 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <p class="text-[13px] font-semibold text-[#17151C]">Belum ada personil yang presensi hari ini</p>
                        <p class="text-[11.5px] text-[#75727C] mt-0.5">Presensi personil akan otomatis muncul realtime di sini</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Preview Foto Presensi di Dashboard --}}
    <div x-show="leadPhotoModal" 
         x-cloak
         class="fixed inset-0 bg-[#0E0D12]/75 z-[9999] flex items-center justify-center p-4 backdrop-blur-sm"
         @click.self="leadPhotoModal = false"
         style="display: none;">
        
        <div class="bg-white rounded-2xl overflow-hidden max-w-md w-full shadow-[0_24px_64px_rgba(14,13,18,0.3)] border border-[#E7E5E3] text-left animate-fade-in-up">
            <div class="flex items-center justify-between px-5 py-3.5 border-b border-[#EFEDEB] bg-[#FBFBFA]">
                <div>
                    <h3 class="text-[14px] font-bold text-[#17151C]" x-text="'Foto Presensi — ' + leadPhotoName"></h3>
                    <span class="text-[11px] text-[#75727C]">Verifikasi foto bukti personil lapangan</span>
                </div>
                <button type="button" @click="leadPhotoModal = false" class="text-[#75727C] hover:text-[#17151C] p-1 rounded-lg hover:bg-[#F1F0EE] transition cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="p-4 bg-[#F8F7F6] space-y-3">
                <img :src="leadPhotoUrl" class="w-full rounded-xl object-contain max-h-[380px] bg-black/5 shadow-sm" alt="Foto Selfie Presensi">
                <div x-show="leadPhotoNote" class="p-3 bg-white border border-[#E7E5E3] rounded-xl text-left">
                    <span class="text-[10.5px] font-bold text-[#75727C] uppercase tracking-wider block mb-0.5">Alasan / Catatan Presensi:</span>
                    <p class="text-[13px] text-[#17151C] font-semibold" x-text="leadPhotoNote"></p>
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
        return d.name;
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
                            },
                            afterLabel: function(context) {
                                const dataToUse = getFilteredEngineerData();
                                const item = dataToUse[context.dataIndex];
                                return item && item.position ? item.position : '';
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
                            title: function(context) {
                                const item = projectData[context[0].dataIndex];
                                return item && item.fullName ? item.fullName : context[0].label;
                            },
                            label: function(context) {
                                return 'Progress: ' + context.parsed.y + '%';
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