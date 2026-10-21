@extends('layouts.app')

@section('title', 'Dashboard - PMO Pusat Pengawasan & Portofolio')

@section('content')
@php
    $formattedProjects = $formattedProjects ?? collect();
    $yourProjects = $yourProjects ?? $formattedProjects;
    $totalBudgetSpend = $totalBudgetSpend ?? 0;
    $formattedTotalBudget = $formattedTotalBudget ?? ('Rp ' . number_format($totalBudgetSpend, 0, ',', '.'));
    $activeProjectsCount = $activeProjectsCount ?? $formattedProjects->whereIn('stage', ['Deliver', 'Operate'])->count();
    $totalTasksCount = $totalTasksCount ?? $formattedProjects->sum('total_tasks');
    $portfolioStatus = $portfolioStatus ?? 'Healthy';
    $portfolioStatusText = $portfolioStatusText ?? 'Active S-Curves';
    $handoverPendingCount = $handoverPendingCount ?? 0;
    $handoverConditionalCount = $handoverConditionalCount ?? 0;
    $taskShareList = $taskShareList ?? [];
    $taskShareChartData = $taskShareChartData ?? ['labels' => [], 'counts' => [], 'colors' => []];
@endphp
<div class="flex h-screen overflow-hidden" x-data="{}" x-cloak>
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto bg-[#F8FAFC]">
        @include('components.topbar', ['title' => 'Dashboard'])
        
        <div class="p-4 sm:p-6 lg:p-8 space-y-6 max-w-[1680px] mx-auto">
            
            <!-- ========================================================== -->
            <!-- 1. CLEAN EXECUTIVE PAGE HEADER                             -->
            <!-- ========================================================== -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-1">
                <div>
                    <h2 class="text-lg sm:text-xl font-extrabold text-slate-900 tracking-tight">Overview Portofolio PMO</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Monitoring alokasi anggaran, status beban tugas tim, dan progres portofolio implementasi aktif.</p>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('pmo.implementations.index') }}" 
                       class="inline-flex items-center gap-2 px-3.5 py-2 text-xs font-semibold rounded-xl bg-white border border-slate-200/90 text-slate-700 hover:text-[#8F0A0D] hover:border-slate-300 hover:bg-slate-50 transition shadow-2xs">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                        </svg>
                        <span>Buka Halaman Projects</span>
                    </a>
                </div>
            </div>

            <!-- ========================================================== -->
            <!-- 2. 4 UNIFIED, CLEAN EXECUTIVE KPI METRIC CARDS             -->
            <!-- ========================================================== -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
                
                {{-- Card 1: Total Budget Spend --}}
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-2xs hover:shadow-xs transition-all flex flex-col justify-between group">
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div class="space-y-0.5">
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Total Budget Spend</span>
                            <h3 class="text-xs font-semibold text-slate-600">Anggaran Proyek</h3>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-slate-100/80 border border-slate-200/70 text-slate-600 flex items-center justify-center shrink-0 group-hover:text-[#8F0A0D] group-hover:bg-rose-50/50 group-hover:border-rose-100 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-baseline gap-2">
                            <span class="text-[20px] sm:text-[22px] font-extrabold text-slate-900 tracking-tight">{{ $formattedTotalBudget }}</span>
                        </div>
                        <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-[11.5px] text-slate-400">
                            <span>Across all projects</span>
                            <span class="font-semibold text-slate-600">Total Portofolio</span>
                        </div>
                    </div>
                </div>

                {{-- Card 2: Project Number --}}
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-2xs hover:shadow-xs transition-all flex flex-col justify-between group">
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div class="space-y-0.5">
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Project Number</span>
                            <h3 class="text-xs font-semibold text-slate-600">Jumlah Proyek</h3>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-slate-100/80 border border-slate-200/70 text-slate-600 flex items-center justify-center shrink-0 group-hover:text-[#8F0A0D] group-hover:bg-rose-50/50 group-hover:border-rose-100 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-baseline gap-2">
                            <span class="text-[22px] sm:text-[24px] font-extrabold text-slate-900 tracking-tight">{{ $activeProjectsCount }}</span>
                            <span class="text-xs font-semibold text-slate-500">Active</span>
                        </div>
                        <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-[11.5px] text-slate-400">
                            <span>Portfolios</span>
                            <span class="font-semibold text-slate-600">{{ $formattedProjects->count() }} Total Proyek</span>
                        </div>
                    </div>
                </div>

                {{-- Card 3: Total Task List --}}
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-2xs hover:shadow-xs transition-all flex flex-col justify-between group">
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div class="space-y-0.5">
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Total Task List</span>
                            <h3 class="text-xs font-semibold text-slate-600">Daftar Tugas Tim</h3>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-slate-100/80 border border-slate-200/70 text-slate-600 flex items-center justify-center shrink-0 group-hover:text-[#8F0A0D] group-hover:bg-rose-50/50 group-hover:border-rose-100 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-baseline gap-2">
                            <span class="text-[22px] sm:text-[24px] font-extrabold text-slate-900 tracking-tight">{{ $totalTasksCount }}</span>
                            <span class="text-xs font-semibold text-slate-500">Tasks</span>
                        </div>
                        <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-[11.5px] text-slate-400">
                            <span>Workload</span>
                            <span class="font-semibold text-slate-600">Alokasi Tim Lapangan</span>
                        </div>
                    </div>
                </div>

                {{-- Card 4: Portfolio Status --}}
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-2xs hover:shadow-xs transition-all flex flex-col justify-between group">
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div class="space-y-0.5">
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Portfolio Status</span>
                            <h3 class="text-xs font-semibold text-slate-600">Kesehatan Portofolio</h3>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-slate-100/80 border border-slate-200/70 text-slate-600 flex items-center justify-center shrink-0 group-hover:text-emerald-700 group-hover:bg-emerald-50/50 group-hover:border-emerald-100 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full {{ $portfolioStatus === 'Healthy' ? 'bg-emerald-500' : ($portfolioStatus === 'At-Risk' ? 'bg-amber-500' : 'bg-rose-500') }}"></span>
                            <span class="text-[20px] sm:text-[22px] font-extrabold text-slate-900 tracking-tight">{{ $portfolioStatus }}</span>
                        </div>
                        <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-[11.5px] text-slate-400">
                            <span>{{ $portfolioStatusText }}</span>
                            <span class="font-semibold {{ $portfolioStatus === 'Healthy' ? 'text-emerald-700' : ($portfolioStatus === 'At-Risk' ? 'text-amber-700' : 'text-rose-700') }}">Sesuai SLA</span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- ========================================================== -->
            <!-- 3. HANDOVER PENDING ALERT BANNER (IF ANY)                  -->
            <!-- ========================================================== -->
            @if($handoverPendingCount > 0 || $handoverConditionalCount > 0)
                <div class="p-4 sm:p-4.5 rounded-2xl bg-white border border-slate-200/90 shadow-2xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-slate-100 text-slate-600 border border-slate-200 flex items-center justify-center shrink-0">
                            <svg class="w-4.5 h-4.5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h4 class="text-xs font-bold text-slate-800">Review Handover Proyek</h4>
                                <span class="px-2 py-0.5 rounded-full text-[10.5px] font-semibold bg-slate-100 text-slate-700 border border-slate-200/60">{{ $handoverPendingCount }} Berkas</span>
                            </div>
                            <p class="text-xs text-slate-500 mt-0.5">
                                Terdapat {{ $handoverPendingCount }} berkas serah terima dari Sales menunggu verifikasi PMO.
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('pmo.implementations.index', ['tab' => 'incoming']) }}" 
                       class="px-3.5 py-1.5 bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold rounded-xl transition shadow-2xs whitespace-nowrap self-start sm:self-center">
                        Tinjau Handover &rarr;
                    </a>
                </div>
            @endif

            <!-- ========================================================== -->
            <!-- 4. MAIN WORKSPACE: TASK SHARE (LEFT) & YOUR PROJECTS (RIGHT)-->
            <!-- ========================================================== -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                
                {{-- LEFT COLUMN: Task Share by Project (Donut Chart & Breakdown) --}}
                <div class="lg:col-span-5 bg-white p-5 sm:p-6 rounded-2xl border border-slate-200/80 shadow-2xs space-y-4">
                    <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-bold text-slate-900 tracking-tight">Task Share by Project</h3>
                            <p class="text-xs text-slate-400 mt-0.5">Porsi alokasi tugas teknis per proyek</p>
                        </div>
                        <span class="text-[11px] font-semibold px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-600 border border-slate-200/60">
                            {{ $totalTasksCount }} Total Tasks
                        </span>
                    </div>

                    {{-- Chart Area or Balanced Empty State --}}
                    @if($totalTasksCount > 0)
                        <div style="height: 200px;" class="flex items-center justify-center relative">
                            <canvas id="pmoTaskShareChart"></canvas>
                        </div>
                    @else
                        <div class="h-[190px] flex flex-col items-center justify-center text-center p-4 border border-dashed border-slate-200 rounded-xl bg-slate-50/50">
                            <div class="w-10 h-10 rounded-xl bg-white border border-slate-200/80 text-slate-400 flex items-center justify-center mb-2 shadow-2xs">
                                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/>
                                </svg>
                            </div>
                            <p class="text-xs font-semibold text-slate-700">Belum Ada Tugas Teknis</p>
                            <p class="text-[11px] text-slate-400 max-w-[220px] mt-0.5 leading-relaxed">
                                Distribusi beban akan tampil otomatis saat task diinput pada proyek.
                            </p>
                        </div>
                    @endif

                    {{-- Task Breakdown List --}}
                    <div class="space-y-2 pt-2 border-t border-slate-100">
                        @forelse($taskShareList as $item)
                            <div class="flex items-center justify-between text-xs py-1.5 px-2 rounded-xl hover:bg-slate-50 transition">
                                <div class="flex items-center gap-2.5 min-w-0 pr-2">
                                    <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background-color: {{ $item['color'] }}"></span>
                                    <div class="truncate">
                                        <div class="font-semibold text-slate-800 truncate" title="{{ $item['name'] }}">{{ $item['name'] }}</div>
                                        <div class="text-[11px] text-slate-400 truncate">{{ $item['client'] }}</div>
                                    </div>
                                </div>
                                <div class="text-right shrink-0">
                                    <span class="font-bold text-slate-700">{{ $item['tasks_count'] }} Tasks</span>
                                    <span class="text-[11px] text-slate-400 block">({{ $item['percentage'] }}%)</span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4 text-slate-400 text-xs">
                                Belum ada task yang teralokasi pada portofolio.
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- RIGHT COLUMN: Your Projects (List of Clean Project Cards) --}}
                <div class="lg:col-span-7 space-y-4">
                    <div class="flex items-center justify-between pb-1">
                        <div>
                            <h3 class="text-sm font-bold text-slate-900">Your Projects</h3>
                            <p class="text-xs text-slate-400 mt-0.5">Daftar portofolio implementasi aktif &amp; kontrol progres EV</p>
                        </div>
                        <a href="{{ route('pmo.implementations.index') }}" 
                           class="text-xs font-semibold text-slate-500 hover:text-[#8F0A0D] inline-flex items-center gap-1 cursor-pointer transition-colors">
                            <span>Click to view details</span>
                            <span class="text-sm">&rarr;</span>
                        </a>
                    </div>

                    {{-- List of Projects --}}
                    <div class="space-y-3.5">
                        @forelse($yourProjects as $p)
                            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-2xs hover:border-slate-300 hover:shadow-xs transition-all flex flex-col justify-between group">
                                
                                {{-- Card Top Row: Days Span badge & Health status --}}
                                <div class="flex items-center justify-between gap-2">
                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-700 border border-slate-200/70">
                                            {{ $p['days_span'] ?? 0 }} Days Span
                                        </span>
                                        @if(($p['sla_tier'] ?? '') !== '')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10.5px] font-semibold bg-slate-100 text-slate-600">
                                                SLA: {{ $p['sla_tier'] }}
                                            </span>
                                        @endif
                                    </div>

                                    <div>
                                        @if($p['health_status'] === 'On-Track')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200/70">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                On-Track
                                            </span>
                                        @elseif($p['health_status'] === 'At-Risk')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-800 border border-amber-200/70">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                At-Risk
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-rose-50 text-rose-800 border border-rose-200/70">
                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                                Delayed
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Card Middle: Project Title & Client & Spend --}}
                                <div class="mt-2.5">
                                    <h4 class="text-[15px] font-bold text-slate-900 group-hover:text-[#8F0A0D] transition-colors leading-snug">
                                        {{ $p['name'] }}
                                    </h4>
                                    <div class="text-[12px] text-slate-500 mt-1 flex flex-wrap items-center gap-2">
                                        <span class="font-semibold text-slate-700">{{ $p['client'] }}</span>
                                        <span class="text-slate-300">&bull;</span>
                                        <span>{{ $p['division'] }}</span>
                                        <span class="text-slate-300">&bull;</span>
                                        <span>PM: <strong class="text-slate-700 font-semibold">{{ $p['pm'] }}</strong></span>
                                    </div>
                                    <div class="mt-2 text-xs font-semibold text-slate-500">
                                        <span class="text-slate-400 font-normal">Spend:</span>
                                        <span class="text-slate-900 font-bold ml-1">{{ $p['contract_value_formatted'] ?? 'Rp 0' }}</span>
                                    </div>
                                </div>

                                {{-- Card Bottom Row: Actual EV (Progress) & Link --}}
                                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between gap-4">
                                    <div class="flex items-center gap-3 flex-1 min-w-0 max-w-[260px]">
                                        <span class="text-[11.5px] font-semibold text-slate-600 shrink-0">
                                            Actual EV: <strong class="text-slate-900">{{ $p['progress'] }}%</strong>
                                        </span>
                                        <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                            <div class="h-full rounded-full transition-all duration-300 bg-[#8F0A0D]" 
                                                 style="width: {{ $p['progress'] }}%"></div>
                                        </div>
                                    </div>

                                    <a href="{{ route('projects.show', $p['id']) }}" 
                                       class="text-xs font-bold text-[#8F0A0D] hover:text-[#B81525] hover:underline inline-flex items-center gap-1 shrink-0">
                                        <span>View Dashboard</span>
                                        <span class="text-sm">&rarr;</span>
                                    </a>
                                </div>

                            </div>
                        @empty
                            <div class="bg-white p-8 rounded-2xl border border-slate-200/80 text-center text-slate-400 text-xs font-medium">
                                Belum ada proyek dalam portofolio Anda.
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Chart: Task Share by Project (Donut Chart)
        const taskShareData = @json($taskShareChartData ?? ['labels' => [], 'counts' => [], 'colors' => []]);
        const ctxShare = document.getElementById('pmoTaskShareChart');
        if (ctxShare && taskShareData.labels && taskShareData.labels.length > 0) {
            new Chart(ctxShare.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: taskShareData.labels,
                    datasets: [{
                        data: taskShareData.counts,
                        backgroundColor: taskShareData.colors,
                        borderWidth: 2,
                        borderColor: '#FFFFFF',
                        hoverOffset: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '66%',
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const val = context.raw || 0;
                                    const pct = total > 0 ? Math.round((val / total) * 100) : 0;
                                    return ` ${context.label}: ${val} Tasks (${pct}%)`;
                                }
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endpush
