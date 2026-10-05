@extends('layouts.app')

@section('title', 'Dashboard PMO - Pusat Kendali & Pengawasan Implementasi')

@section('content')
@php
    $formattedProjects = $formattedProjects ?? collect();
    $recentProjects = $recentProjects ?? $formattedProjects->take(5)->values();
    $stageCounts = $stageCounts ?? [];
    $onTrackCount = $onTrackCount ?? 0;
    $delayedCount = $delayedCount ?? 0;
    $atRiskCount = $atRiskCount ?? 0;
    $handoverPendingCount = $handoverPendingCount ?? 0;
    $handoverConditionalCount = $handoverConditionalCount ?? 0;
    $readyToOperateCount = $readyToOperateCount ?? 0;
    $slaChartData = $slaChartData ?? ['on_track' => $onTrackCount, 'at_risk' => $atRiskCount, 'delayed' => $delayedCount];
    $divisionChartData = $divisionChartData ?? [];
@endphp
<div class="flex h-screen overflow-hidden" x-data="{}" x-cloak>
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto bg-[#F8FAFC]">
        @include('components.topbar', ['title' => 'Dashboard PMO'])
        
        <div class="p-4 sm:p-6 lg:p-8 space-y-6 max-w-[1680px] mx-auto">
            
            <!-- ========================================================== -->
            <!-- 1. HEADER BANNER RINGKAS & MODERN                          -->
            <!-- ========================================================== -->
            <div class="bg-white p-6 sm:p-7 rounded-2xl border border-slate-200/90 shadow-2xs relative overflow-hidden">
                <div class="absolute -right-16 -top-16 w-56 h-56 bg-red-500/5 rounded-full blur-3xl pointer-events-none"></div>
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5 relative z-10">
                    <div>
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-red-50 border border-red-100 mb-2.5">
                            <span class="w-2 h-2 rounded-full bg-[#8F0A0D]"></span>
                            <span class="text-[#8F0A0D] text-[11px] font-bold uppercase tracking-wider">PROJECT MANAGEMENT OFFICE &bull; OVERVIEW EKSEKUTIF</span>
                        </div>
                        <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Pusat Kendali Pengawasan Proyek</h2>
                        <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-2xl leading-relaxed">
                            Monitoring terpadu implementasi teknis tahap Deliver, kepatuhan jadwal (SLA), verifikasi kelengkapan berkas serah terima, dan kesiapan transisi ke Managed Service.
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-3 shrink-0">
                        <a href="{{ route('pmo.implementations.index') }}" 
                           class="px-4 py-2.5 rounded-xl bg-[#8F0A0D] text-white font-bold text-xs hover:bg-[#72080a] hover:scale-[1.02] transition-all shadow-sm flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                            </svg>
                            <span>Buka Tata Kelola Proyek</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- ========================================================== -->
            <!-- 2. 4 EXECUTIVE KPI METRIC CARDS                            -->
            <!-- ========================================================== -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
                
                {{-- Card 1: Deliver Aktif --}}
                <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-2xs hover:shadow-md hover:border-red-200 transition-all flex flex-col justify-between group">
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div class="space-y-0.5">
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">TAHAP DELIVER</span>
                            <h3 class="text-xs font-bold text-slate-700">Implementasi Teknis</h3>
                        </div>
                        <div class="w-11 h-11 rounded-xl bg-red-50 border border-red-100 text-[#8F0A0D] flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform shadow-2xs">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-baseline gap-2">
                            <span class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">{{ $stageCounts['Deliver'] ?? 0 }}</span>
                            <span class="text-xs font-semibold text-slate-400">Proyek Aktif</span>
                        </div>
                        <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                            <span>Siklus Pelaksanaan:</span>
                            <span class="font-bold text-[#8F0A0D]">Fase Lapangan</span>
                        </div>
                    </div>
                </div>

                {{-- Card 2: Kepatuhan Timeline (SLA) --}}
                <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-2xs hover:shadow-md hover:border-emerald-200 transition-all flex flex-col justify-between group">
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div class="space-y-0.5">
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">KEPATUHAN JADWAL</span>
                            <h3 class="text-xs font-bold text-slate-700">Kepatuhan Timeline (SLA)</h3>
                        </div>
                        <div class="w-11 h-11 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-700 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform shadow-2xs">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-baseline gap-2">
                            <span class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">{{ $onTrackCount }}</span>
                            <span class="px-2 py-0.5 rounded-full text-[10.5px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Sesuai Jadwal</span>
                        </div>
                        <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-[11px]">
                            <span class="text-slate-500">Monitoring Risiko:</span>
                            @if($delayedCount > 0)
                                <span class="font-bold text-rose-600">{{ $delayedCount }} Melewati Tenggat</span>
                            @elseif($atRiskCount > 0)
                                <span class="font-bold text-amber-700">{{ $atRiskCount }} Mendekati Tenggat</span>
                            @else
                                <span class="font-semibold text-emerald-700">100% Proyek Tepat Waktu</span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Card 3: Gerbang Serah Terima (Handover Gateway) --}}
                <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-2xs hover:shadow-md hover:border-amber-200 transition-all flex flex-col justify-between group">
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div class="space-y-0.5">
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">SERAH TERIMA SALES</span>
                            <h3 class="text-xs font-bold text-slate-700">Verifikasi Dokumen PMO</h3>
                        </div>
                        <div class="w-11 h-11 rounded-xl bg-amber-50 border border-amber-100 text-amber-700 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform shadow-2xs">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-baseline gap-2">
                            <span class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">{{ $handoverPendingCount }}</span>
                            <span class="px-2 py-0.5 rounded-full text-[10.5px] font-bold bg-amber-50 text-amber-800 border border-amber-200">Menunggu Review</span>
                        </div>
                        <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                            <span>Status Revisi:</span>
                            <span class="font-bold text-amber-700">{{ $handoverConditionalCount }} Bersyarat</span>
                        </div>
                    </div>
                </div>

                {{-- Card 4: Transisi Operasional & Managed Service --}}
                <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-2xs hover:shadow-md hover:border-blue-200 transition-all flex flex-col justify-between group">
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div class="space-y-0.5">
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">TRANSISI OPERASIONAL</span>
                            <h3 class="text-xs font-bold text-slate-700">Kesiapan Managed Service</h3>
                        </div>
                        <div class="w-11 h-11 rounded-xl bg-blue-50 border border-blue-100 text-blue-700 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform shadow-2xs">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-baseline gap-2">
                            <span class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">{{ $readyToOperateCount }}</span>
                            <span class="px-2 py-0.5 rounded-full text-[10.5px] font-bold bg-blue-50 text-blue-700 border border-blue-200">Siap Handover MS</span>
                        </div>
                        <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                            <span>Fase Operate Aktif:</span>
                            <span class="font-bold text-slate-800">{{ $stageCounts['Operate'] ?? 0 }} Proyek</span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- ========================================================== -->
            <!-- 3. HANDOVER PENDING ALERT BANNER (IF ANY)                  -->
            <!-- ========================================================== -->
            @if($handoverPendingCount > 0 || $handoverConditionalCount > 0)
                <div class="p-4 sm:p-5 rounded-2xl bg-gradient-to-r from-amber-50 to-orange-50/70 border border-amber-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-xs">
                    <div class="flex items-start gap-3.5">
                        <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center shrink-0 shadow-xs">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h4 class="text-xs font-bold text-amber-950 uppercase tracking-wide">Pemberitahuan Gatekeeper Handover Proyek</h4>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-200/80 text-amber-900">{{ $handoverPendingCount }} Menunggu Review</span>
                            </div>
                            <p class="text-xs text-amber-800 mt-1 leading-relaxed">
                                Terdapat <strong class="text-amber-950 font-bold">{{ $handoverPendingCount }}</strong> berkas serah terima dari Sales yang siap diverifikasi PMO, dan <strong class="text-amber-950 font-bold">{{ $handoverConditionalCount }}</strong> berkas revisi bersyarat.
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('pmo.implementations.index') }}" 
                       class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold rounded-xl transition shadow-xs whitespace-nowrap self-start sm:self-center flex items-center gap-2">
                        <span>Tinjau di Tata Kelola Proyek</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            @endif

            <!-- ========================================================== -->
            <!-- 4. VISUAL ANALYTICS GRID (2 CHARTS: SLA & BEBAN DIVISI)    -->
            <!-- ========================================================== -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                
                {{-- Chart 1: Kepatuhan Jadwal & Kesehatan Timeline (SLA) --}}
                <div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200/90 shadow-2xs space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div>
                            <h3 class="text-sm font-bold text-slate-800 tracking-tight">Kepatuhan Jadwal &amp; SLA Proyek</h3>
                            <p class="text-xs text-slate-400">Monitoring ketepatan waktu deliver lapangan</p>
                        </div>
                        <span class="text-xs font-semibold px-2.5 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-md">
                            {{ $onTrackCount }} / {{ $formattedProjects->count() }} Tepat Waktu
                        </span>
                    </div>
                    <div style="height: 240px;" class="flex items-center justify-center">
                        <canvas id="pmoSlaChart"></canvas>
                    </div>
                </div>

                {{-- Chart 2: Distribusi Beban Proyek per Divisi Pelaksana --}}
                <div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200/90 shadow-2xs space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div>
                            <h3 class="text-sm font-bold text-slate-800 tracking-tight">Distribusi Proyek per Divisi</h3>
                            <p class="text-xs text-slate-400">Alokasi pelaksanaan teknis Network vs Security</p>
                        </div>
                        <span class="text-xs font-semibold px-2.5 py-0.5 bg-slate-50 text-slate-700 border border-slate-200 rounded-md">
                            {{ $formattedProjects->count() }} Proyek Terdata
                        </span>
                    </div>
                    <div style="height: 240px;" class="flex items-center justify-center">
                        <canvas id="pmoDivisionChart"></canvas>
                    </div>
                </div>

            </div>

            <!-- ========================================================== -->
            <!-- 5. PREVIEW 5 PROYEK IMPLEMENTASI TERKINI                   -->
            <!-- ========================================================== -->
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden">
                <div class="p-5 sm:px-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-white">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#8F0A0D]"></span>
                            Proyek Implementasi Terkini
                        </h3>
                        <p class="text-xs text-slate-400 mt-0.5">Ringkasan 5 proyek implementasi lapangan terbaru</p>
                    </div>
                    <a href="{{ route('pmo.implementations.index') }}" 
                       class="text-xs font-bold text-[#8F0A0D] hover:underline inline-flex items-center gap-1.5 self-start sm:self-auto">
                        <span>Buka Tata Kelola Lengkap ({{ $formattedProjects->count() }} Proyek)</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50/80 text-slate-500 uppercase text-[10.5px] font-semibold tracking-wider border-b border-slate-100">
                            <tr>
                                <th class="py-3 px-4">Proyek &amp; Klien</th>
                                <th class="py-3 px-4">Divisi &amp; PIC PM</th>
                                <th class="py-3 px-4 text-center">Progress Task</th>
                                <th class="py-3 px-4 text-center">Status Jadwal</th>
                                <th class="py-3 px-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-normal text-slate-600">
                            @forelse($recentProjects as $p)
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-slate-800 text-[13px]">{{ $p['name'] }}</div>
                                        <div class="text-[11px] text-slate-400 mt-0.5 flex items-center gap-2">
                                            <span>{{ $p['client'] }}</span>
                                            <span class="text-slate-300">&bull;</span>
                                            <span class="font-mono text-slate-500">{{ $p['so_code'] }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <span class="inline-block px-2 py-0.5 rounded text-[10.5px] font-medium {{ str_contains(strtolower($p['division']), 'net') ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                                            {{ $p['division'] }}
                                        </span>
                                        <div class="text-[11px] text-slate-500 mt-1 flex items-center gap-1">
                                            <span class="text-slate-400">PM:</span>
                                            <span class="font-semibold">{{ $p['pm'] }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        <div class="inline-flex flex-col items-center">
                                            <div class="w-24 bg-slate-100 rounded-full h-2 overflow-hidden mb-1">
                                                <div class="bg-[#8F0A0D] h-2 rounded-full transition-all" style="width: {{ $p['progress'] }}%"></div>
                                            </div>
                                            <span class="text-[10.5px] font-bold text-slate-700">{{ $p['progress'] }}% ({{ $p['completed_tasks'] }}/{{ $p['total_tasks'] }})</span>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        @if($p['health_status'] === 'On-Track')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10.5px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                Sesuai Jadwal
                                            </span>
                                        @elseif($p['health_status'] === 'At-Risk')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10.5px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                Mendekati Tenggat
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10.5px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                                Melewati Tenggat
                                            </span>
                                        @endif
                                        <div class="text-[10px] text-slate-400 mt-0.5">{{ $p['deadline'] }}</div>
                                    </td>
                                    <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                        <a href="{{ route('projects.show', $p['id']) }}" 
                                           class="px-3 py-1.5 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold rounded-lg border border-slate-200 shadow-2xs transition inline-flex items-center gap-1">
                                            Detail &rarr;
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-slate-400 text-xs font-medium">Belum ada proyek implementasi tercatat.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
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
        // 1. Chart Kepatuhan SLA (Doughnut)
        const slaData = @json($slaChartData ?? ['on_track' => 0, 'at_risk' => 0, 'delayed' => 0]);
        const ctxSla = document.getElementById('pmoSlaChart');
        if (ctxSla) {
            new Chart(ctxSla.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: ['Sesuai Jadwal (On-Track)', 'Mendekati Tenggat (At-Risk)', 'Melewati Tenggat (Delayed)'],
                    datasets: [{
                        data: [slaData.on_track, slaData.at_risk, slaData.delayed],
                        backgroundColor: ['#10B981', '#F59E0B', '#EF4444'],
                        borderWidth: 2,
                        borderColor: '#FFFFFF',
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '68%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 10,
                                boxHeight: 10,
                                font: { size: 10.5, family: "'Inter', sans-serif" },
                                color: '#4B5563',
                                padding: 12
                            }
                        }
                    }
                }
            });
        }

        // 2. Chart Distribusi Beban per Divisi (Bar)
        const divData = @json($divisionChartData ?? []);
        const ctxDiv = document.getElementById('pmoDivisionChart');
        if (ctxDiv) {
            new Chart(ctxDiv.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: Object.keys(divData),
                    datasets: [{
                        label: 'Jumlah Proyek',
                        data: Object.values(divData),
                        backgroundColor: ['#3B82F6', '#8F0A0D', '#94A3B8'],
                        borderRadius: 8,
                        maxBarThickness: 36
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 11, family: "'Inter', sans-serif" }, color: '#64748B' }
                        },
                        y: {
                            grid: { color: '#F1F5F9' },
                            ticks: {
                                stepSize: 1,
                                font: { size: 10.5, family: "'Inter', sans-serif" },
                                color: '#94A3B8'
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endpush
