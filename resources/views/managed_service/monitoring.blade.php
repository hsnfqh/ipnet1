@extends('layouts.app')

@section('title', 'Monitoring SLA Proyek - ' . ($project ? $project->client : 'Managed Service'))

@push('styles')
<style>
    [x-cloak] { display: none !important; }

    .ipnet-card {
        background-color: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 20px;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.04);
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .ipnet-badge-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background-color: #8F0A0D;
        display: inline-block;
        margin-right: 8px;
    }
    @keyframes fadeUpStagger {
        0% { opacity: 0; transform: translateY(16px); }
        100% { opacity: 1; transform: translateY(0); }
    }
    .anim-fade-up {
        animation: fadeUpStagger 0.5s cubic-bezier(0.16, 1, 0.3, 1) both;
    }
    .anim-delay-1 { animation-delay: 0.06s !important; }
    .anim-delay-2 { animation-delay: 0.12s !important; }
    .anim-delay-3 { animation-delay: 0.18s !important; }
</style>
@endpush

@section('content')
<div class="flex h-screen overflow-hidden bg-[#F8FAFC] font-sans" x-data="{ 
    activeTab: '{{ $activeTab ?? 'pm' }}',
    isPmModalOpen: false, 
    activeCycle: null,
    isCmModalOpen: false,
    openPmModal(cycle) {
        this.activeCycle = cycle;
        this.isPmModalOpen = true;
    }
}">
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Monitoring SLA Proyek'])
        
        <div class="p-4 sm:p-6 lg:p-7 space-y-5 max-w-[1600px] mx-auto">

            <!-- ========================================================== -->
            <!-- 1. DEDICATED PROJECT MONITORING HEADER CARD                -->
            <!-- ========================================================== -->
            <div class="ipnet-card p-5 sm:p-6 anim-fade-up anim-delay-1">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5 pb-5 mb-5 border-b border-[#E2E8F0]">
                    <div class="space-y-1.5">
                        <div class="flex items-center gap-2 text-[12px] font-semibold text-[#64748B]">
                            <a href="{{ route('ms.projects.index') }}" class="hover:text-[#1E293B] transition">Daftar Proyek</a>
                            <svg class="w-3.5 h-3.5 text-[#94A3B8]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            <span class="text-[#8F0A0D] font-bold">Monitoring SLA Proyek</span>
                        </div>

                        @if($project)
                            <div class="flex items-center gap-3 flex-wrap">
                                <h1 class="text-[22px] font-black text-[#1E293B] tracking-tight">{{ $project->name }}</h1>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-50 text-[#8F0A0D] border border-rose-200">
                                    {{ $project->project_type ?: 'Managed Service SLA' }}
                                </span>
                                @if($project->sla_tier)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                        Tier: {{ $project->sla_tier }}
                                    </span>
                                @endif
                            </div>

                            <div class="flex items-center gap-4 text-[12.5px] text-[#64748B] flex-wrap pt-0.5">
                                <span class="font-bold text-[#1E293B] flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-[#8F0A0D]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                    Customer: <strong class="text-[#1E293B]">{{ $project->client }}</strong>
                                </span>
                                <span class="text-[#CBD5E1]">•</span>
                                <span class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-[#64748B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    {{ $project->location ?: 'On-Site Klien' }}
                                </span>
                                <span class="text-[#CBD5E1]">•</span>
                                <span class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-[#64748B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    No. SPK: {{ $project->po_spk_number ?: ('SPK-'.$project->id) }}
                                </span>
                            </div>
                        @else
                            <h1 class="text-[20px] font-black text-[#1E293B]">Pilih Proyek untuk Memulai Monitoring</h1>
                        @endif
                    </div>

                    <!-- Project Selector & Quick Nav Controls -->
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 shrink-0">
                        <form method="GET" action="{{ route('ms.monitoring.index') }}" class="flex items-center gap-2">
                            <input type="hidden" name="tab" :value="activeTab">
                            <label for="project_select" class="text-[12px] font-bold text-[#64748B] whitespace-nowrap">Ganti Proyek:</label>
                            <select id="project_select" name="project_id" onchange="this.form.submit()" class="px-3.5 py-2 rounded-xl border border-[#CBD5E1] text-[12.5px] font-bold text-[#1E293B] bg-white outline-none hover:border-[#94A3B8] focus:border-[#8F0A0D] shadow-xs cursor-pointer min-w-[260px]">
                                @foreach($allProjects as $proj)
                                    <option value="{{ $proj->id }}" {{ ($project && $project->id == $proj->id) ? 'selected' : '' }}>
                                        {{ $proj->client }} — {{ \Illuminate\Support\Str::limit($proj->name, 28) }}
                                    </option>
                                @endforeach
                            </select>
                        </form>

                        @if($project)
                            <a href="{{ route('ms.projects.show', $project->id) }}" class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 rounded-xl border border-[#CBD5E1] bg-white hover:bg-[#F8FAFC] text-[#334155] text-[12.5px] font-bold transition shadow-xs">
                                <svg class="w-4 h-4 text-[#64748B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                <span>Detail Dokumen</span>
                            </a>
                        @endif
                    </div>
                </div>

                @if($project)
                    <!-- 4 SYMMETRICAL PROJECT PARAMETER KPI CARDS -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        {{-- Card 1: Status & Periode Kontrak --}}
                        <div class="p-4 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0] space-y-1.5">
                            <div class="flex items-center justify-between">
                                <span class="text-[11px] font-bold text-[#64748B] uppercase tracking-wider">Periode Kontrak</span>
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10.5px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 animate-pulse"></span>
                                    Active
                                </span>
                            </div>
                            <p class="text-[15px] font-black text-[#1E293B]">
                                @if($project->service_start_date && $project->service_end_date)
                                    {{ \Carbon\Carbon::parse($project->service_start_date)->translatedFormat('d M Y') }} - {{ \Carbon\Carbon::parse($project->service_end_date)->translatedFormat('d M Y') }}
                                @else
                                    1 Tahun Kontrak
                                @endif
                            </p>
                            <p class="text-[11.5px] text-[#64748B]">Standby Support: <strong class="text-[#1E293B]">{{ $project->sla_coverage_hours ?: '24x7' }}</strong></p>
                        </div>

                        {{-- Card 2: Realisasi Siklus PM --}}
                        <div class="p-4 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0] space-y-1.5">
                            <div class="flex items-center justify-between">
                                <span class="text-[11px] font-bold text-[#64748B] uppercase tracking-wider">Preventive Maintenance</span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10.5px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                    {{ $pmProgress }}% Selesai
                                </span>
                            </div>
                            <p class="text-[15px] font-black text-[#1E293B]">{{ $pmCompleted }} / {{ $pmTotal }} Visit Selesai</p>
                            <p class="text-[11.5px] text-[#64748B]">Frekuensi: <strong class="text-[#1E293B]">{{ str_replace('_', ' ', $project->pm_frequency_type ?: 'per 3 bulan') }}</strong></p>
                        </div>

                        {{-- Card 3: Utilisasi Tiket CM --}}
                        <div class="p-4 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0] space-y-1.5">
                            <div class="flex items-center justify-between">
                                <span class="text-[11px] font-bold text-[#64748B] uppercase tracking-wider">Corrective Maintenance</span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10.5px] font-bold bg-rose-50 text-[#8F0A0D] border border-rose-200">
                                    Sisa: {{ $cmRemaining }} Tiket
                                </span>
                            </div>
                            <p class="text-[15px] font-black text-[#8F0A0D]">{{ $cmUsed }} / {{ $cmQuota >= 999 ? '∞' : $cmQuota }} Tiket Terpakai</p>
                            <p class="text-[11.5px] text-[#64748B]">Target MTTR: <strong class="text-[#1E293B]">{{ $project->mttr_hours ?: 4 }} Jam</strong></p>
                        </div>

                        {{-- Card 4: Target SLA & Respon --}}
                        <div class="p-4 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0] space-y-1.5">
                            <div class="flex items-center justify-between">
                                <span class="text-[11px] font-bold text-[#64748B] uppercase tracking-wider">Komitmen SLA</span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10.5px] font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                    Tier: {{ $project->sla_tier ?: 'Gold' }}
                                </span>
                            </div>
                            <p class="text-[15px] font-black text-[#1E293B]">{{ $project->sla_target_percentage ?: '99.50' }}% Uptime</p>
                            <p class="text-[11.5px] text-[#64748B]">Call Response: <strong class="text-[#1E293B]">{{ $project->call_response_time ?: '15 menit' }}</strong></p>
                        </div>
                    </div>
                @endif
            </div>

            @if(session('success'))
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-[13px] font-semibold flex items-center justify-between shadow-xs anim-fade-up">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button type="button" @click="$el.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900 font-bold">&times;</button>
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-[13px] font-semibold flex items-center justify-between shadow-xs anim-fade-up">
                    <span>{{ session('error') }}</span>
                    <button type="button" @click="$el.parentElement.remove()" class="text-rose-600 hover:text-rose-900 font-bold">&times;</button>
                </div>
            @endif

            @if($project)
                <!-- ========================================================== -->
                <!-- 2. PROJECT-SCOPED TAB SWITCHER                             -->
                <!-- ========================================================== -->
                <div class="flex items-center gap-3 anim-fade-up anim-delay-2">
                    <div class="bg-white p-1 rounded-2xl border border-[#E2E8F0] shadow-xs inline-flex items-center">
                        <button type="button" 
                                @click="activeTab = 'pm'" 
                                :class="activeTab === 'pm' ? 'bg-[#8F0A0D] text-white shadow-md' : 'text-[#64748B] hover:text-[#1E293B] hover:bg-[#F8FAFC]'"
                                class="px-5 py-2.5 rounded-xl text-[13px] font-bold flex items-center gap-2.5 transition-all cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            <span>Preventive Maintenance (PM)</span>
                            <span :class="activeTab === 'pm' ? 'bg-white/20 text-white' : 'bg-slate-100 text-[#475569]'" class="px-2 py-0.5 rounded-full text-[11px] font-bold">
                                {{ $pmCompleted }} / {{ $pmTotal }} Visit
                            </span>
                        </button>

                        <button type="button" 
                                @click="activeTab = 'cm'" 
                                :class="activeTab === 'cm' ? 'bg-[#8F0A0D] text-white shadow-md' : 'text-[#64748B] hover:text-[#1E293B] hover:bg-[#F8FAFC]'"
                                class="px-5 py-2.5 rounded-xl text-[13px] font-bold flex items-center gap-2.5 transition-all cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            <span>Corrective Maintenance (CM)</span>
                            <span :class="activeTab === 'cm' ? 'bg-white/20 text-white' : 'bg-slate-100 text-[#475569]'" class="px-2 py-0.5 rounded-full text-[11px] font-bold">
                                {{ $cmUsed }} / {{ $cmQuota >= 999 ? '∞' : $cmQuota }} Tiket
                            </span>
                        </button>
                    </div>
                </div>

                <!-- ========================================================== -->
                <!-- 3. TAB 1: PREVENTIVE MAINTENANCE (PM) - FULL WIDTH         -->
                <!-- ========================================================== -->
                <div x-show="activeTab === 'pm'" x-cloak class="space-y-5 anim-fade-up">
                    
                    {{-- Timeline Card --}}
                    <div class="ipnet-card p-6 space-y-4">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border-b border-[#E2E8F0] pb-3">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span>
                                <h3 class="text-[14px] font-bold text-[#1E293B] uppercase tracking-wider">
                                    Timeline Siklus Kunjungan Proyek Ini ({{ $pmTotal }}x Visit — {{ str_replace('_', ' ', $project->pm_frequency_type ?: 'per 3 bulan') }})
                                </h3>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-[12px] text-[#64748B]">Realisasi Visit:</span>
                                <span class="text-[#8F0A0D] font-black text-[14px]">{{ $pmCompleted }} dari {{ $pmTotal }} Selesai ({{ $pmProgress }}%)</span>
                            </div>
                        </div>

                        <!-- Interactive Nodes Timeline (Linear jika <= 6, Snake Uler jika > 6) -->
                        <div class="p-6 rounded-2xl bg-[#F8FAFC] border border-[#E2E8F0] space-y-4 overflow-x-auto">
                            @php
                                $mNodeItems = collect();
                                $mNodeItems->push([
                                    'is_milestone' => true,
                                    'label'        => 'Start',
                                    'sub'          => $project->service_start_date ? \Carbon\Carbon::parse($project->service_start_date)->format('d/m/Y') : 'Awal',
                                    'char'         => 'S',
                                ]);

                                foreach($pmCycles as $c) {
                                    $subText = !empty($c->actual_date) 
                                        ? \Carbon\Carbon::parse($c->actual_date)->format('d/m/Y') 
                                        : ($c->activity_status === 'In Progress' ? 'In Progress' : 'Terjadwal');

                                    $mNodeItems->push([
                                        'is_milestone' => false,
                                        'cycle'        => $c,
                                        'number'       => $c->cycle_number,
                                        'label'        => 'PM ' . $c->cycle_number,
                                        'sub'          => $subText,
                                        'status'       => $c->activity_status,
                                    ]);
                                }

                                $mNodeItems->push([
                                    'is_milestone' => true,
                                    'label'        => 'End',
                                    'sub'          => ($pmCompleted == $pmTotal && $project->service_end_date) ? \Carbon\Carbon::parse($project->service_end_date)->format('d/m/Y') : 'Akhir Kontrak',
                                    'char'         => 'E',
                                ]);

                                $isMonitoringSnake = $pmTotal > 6;
                                $mChunkSize = 5;
                                $mRows = $isMonitoringSnake ? $mNodeItems->chunk($mChunkSize) : collect([$mNodeItems]);
                            @endphp

                            @if(!$isMonitoringSnake)
                                <div class="relative py-4">
                                    <!-- Background Line -->
                                    <div class="absolute top-1/2 left-8 right-8 h-2 bg-[#E2E8F0] -translate-y-1/2 rounded-full"></div>
                                    <!-- Active Fill Line -->
                                    <div class="absolute top-1/2 left-8 h-2 rounded-full transition-all duration-700"
                                         style="background: linear-gradient(90deg, #8F0A0D, #D62E3C); width: calc({{ min(100, $pmProgress) }}% * 0.90);"></div>

                                    <div class="relative flex items-center justify-between px-4 min-w-[650px]">
                                        <!-- Start Node -->
                                        <div class="flex flex-col items-center">
                                            <div class="w-9 h-9 rounded-full bg-[#1E293B] text-white flex items-center justify-center text-[11px] font-black shadow-md">
                                                S
                                            </div>
                                            <span class="text-[11.5px] font-bold text-[#64748B] mt-2">Start</span>
                                            <span class="text-[10.5px] text-[#94A3B8]">{{ $project->service_start_date ? \Carbon\Carbon::parse($project->service_start_date)->format('d/m/Y') : 'Awal' }}</span>
                                        </div>

                                        <!-- PM Cycle Nodes -->
                                        @foreach($pmCycles as $c)
                                            @php
                                                $isDone = ($c->activity_status === 'Completed');
                                                $isInProg = ($c->activity_status === 'In Progress');
                                            @endphp
                                            <div class="flex flex-col items-center cursor-pointer group" @click="openPmModal({{ json_encode($c) }})">
                                                <div class="w-10 h-10 rounded-full flex items-center justify-center text-[12.5px] font-black transition-all shadow-md {{ $isDone ? 'bg-[#8F0A0D] text-white ring-4 ring-rose-100' : ($isInProg ? 'bg-amber-500 text-white ring-4 ring-amber-100 animate-pulse' : 'bg-white text-[#64748B] border border-[#CBD5E1] group-hover:border-[#8F0A0D] group-hover:text-[#8F0A0D]') }}">
                                                    @if($isDone)
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                    @else
                                                        {{ $c->cycle_number }}
                                                    @endif
                                                </div>
                                                <span class="text-[11.5px] font-bold mt-2 {{ $isDone ? 'text-[#1E293B]' : 'text-[#64748B]' }} group-hover:text-[#8F0A0D] transition">
                                                    PM {{ $c->cycle_number }}
                                                </span>
                                                <span class="text-[10.5px] {{ $isDone ? 'text-emerald-600 font-medium' : ($isInProg ? 'text-amber-600 font-medium' : 'text-[#94A3B8]') }}">
                                                    {{ !empty($c->actual_date) ? \Carbon\Carbon::parse($c->actual_date)->format('d/m/Y') : ($isInProg ? 'In Progress' : 'Terjadwal') }}
                                                </span>
                                            </div>
                                        @endforeach

                                        <!-- End Node -->
                                        <div class="flex flex-col items-center">
                                            <div class="w-9 h-9 rounded-full bg-[#E2E8F0] text-[#64748B] flex items-center justify-center text-[11px] font-black shadow-xs">
                                                E
                                            </div>
                                            <span class="text-[11.5px] font-bold text-[#64748B] mt-2">End</span>
                                            <span class="text-[10.5px] text-[#94A3B8]">{{ $project->service_end_date ? \Carbon\Carbon::parse($project->service_end_date)->format('d/m/Y') : 'Akhir' }}</span>
                                        </div>
                                    </div>
                                </div>
                            @else
                                {{-- Serpentine Snake Flow --}}
                                <div class="min-w-[700px] max-w-5xl mx-auto space-y-7 py-3">
                                    @foreach($mRows as $rIdx => $rNodes)
                                        @php
                                            $isRtl = ($rIdx % 2 === 1);
                                            $hasNext = ($rIdx < $mRows->count() - 1);
                                        @endphp
                                        <div class="relative">
                                            <div class="absolute top-4 left-6 right-6 h-0.5 bg-[#CBD5E1] -z-0"></div>

                                            @if($hasNext)
                                                @if(!$isRtl)
                                                    <div class="absolute -right-1 top-4 w-10 h-[calc(100%+1.75rem)] border-t-2 border-r-2 border-b-2 rounded-r-3xl border-[#CBD5E1] pointer-events-none -z-0"></div>
                                                @else
                                                    <div class="absolute -left-1 top-4 w-10 h-[calc(100%+1.75rem)] border-t-2 border-l-2 border-b-2 rounded-l-3xl border-[#CBD5E1] pointer-events-none -z-0"></div>
                                                @endif
                                            @endif

                                            <div class="flex items-start justify-between relative z-10 px-2 {{ $isRtl ? 'flex-row-reverse' : '' }}">
                                                @foreach($rNodes as $nd)
                                                    <div class="flex-1 flex flex-col items-center text-center">
                                                        @if($nd['is_milestone'])
                                                            <div class="w-8 h-8 rounded-full {{ $nd['label'] === 'Start' ? 'bg-[#1E293B] text-white' : 'bg-[#E2E8F0] text-[#64748B]' }} flex items-center justify-center text-[11px] font-black shadow-xs">
                                                                {{ $nd['char'] }}
                                                            </div>
                                                            <span class="text-[11px] font-bold text-[#1E293B] mt-1.5">{{ $nd['label'] }}</span>
                                                            <span class="text-[10px] text-[#64748B]">{{ $nd['sub'] }}</span>
                                                        @else
                                                            @php
                                                                $isDone = ($nd['status'] === 'Completed');
                                                                $isInProg = ($nd['status'] === 'In Progress');
                                                            @endphp
                                                            <div class="cursor-pointer group flex flex-col items-center" @click="openPmModal({{ json_encode($nd['cycle']) }})">
                                                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-[11px] font-black transition-all group-hover:scale-110 shadow-xs
                                                                    {{ $isDone ? 'bg-[#8F0A0D] text-white' : ($isInProg ? 'bg-amber-500 text-white animate-pulse' : 'bg-white text-[#64748B] border border-[#CBD5E1] group-hover:border-[#8F0A0D]') }}">
                                                                    @if($isDone)
                                                                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                                    @else
                                                                        {{ $nd['number'] }}
                                                                    @endif
                                                                </div>
                                                                <span class="text-[11px] font-bold mt-1.5 {{ $isDone ? 'text-[#1E293B]' : 'text-[#64748B]' }} group-hover:text-[#8F0A0D] transition">
                                                                    {{ $nd['label'] }}
                                                                </span>
                                                                <span class="text-[10px] text-[#64748B]">{{ $nd['sub'] }}</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endforeach

                                                @if($rNodes->count() < $mChunkSize)
                                                    @for($pad = 0; $pad < ($mChunkSize - $rNodes->count()); $pad++)
                                                        <div class="flex-1 opacity-0 pointer-events-none"></div>
                                                    @endfor
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <p class="text-[11.5px] text-[#94A3B8] text-center font-medium">Klik pada salah satu node siklus PM di atas untuk membuka modal pembaruan status kunjungan dan upload dokumen Laporan/BA.</p>
                        </div>
                    </div>

                    {{-- Table PM Cycles --}}
                    <div class="ipnet-card overflow-hidden">
                        <div class="p-5 border-b border-[#E2E8F0] flex items-center justify-between">
                            <div>
                                <h3 class="text-[15px] font-bold text-[#1E293B]">Daftar Siklus Preventive Maintenance — {{ $project->client }}</h3>
                                <p class="text-[12px] text-[#64748B] mt-0.5">Kelola status visit teknisi, dokumen laporan inspeksi (PDF), dan milestone Berita Acara (BA) penagihan</p>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full min-w-[900px] border-collapse text-[13px] text-left">
                                <thead>
                                    <tr class="bg-[#F8FAFC] border-b border-[#E2E8F0]">
                                        <th class="py-3.5 px-4 text-[11px] font-semibold text-[#64748B] uppercase tracking-wider">Siklus</th>
                                        <th class="py-3.5 px-4 text-[11px] font-semibold text-[#64748B] uppercase tracking-wider">Jadwal & Realisasi Visit</th>
                                        <th class="py-3.5 px-4 text-[11px] font-semibold text-[#64748B] uppercase tracking-wider">PIC Engineer</th>
                                        <th class="py-3.5 px-4 text-[11px] font-semibold text-[#64748B] uppercase tracking-wider">Status Visit</th>
                                        <th class="py-3.5 px-4 text-[11px] font-semibold text-[#64748B] uppercase tracking-wider">Laporan PM (PDF)</th>
                                        <th class="py-3.5 px-4 text-[11px] font-semibold text-[#64748B] uppercase tracking-wider">BA / Milestone</th>
                                        <th class="py-3.5 px-4 text-[11px] font-semibold text-[#64748B] uppercase tracking-wider text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-[#F1F5F9]">
                                    @forelse($pmCycles as $cycle)
                                        @php
                                            $isDone = ($cycle->activity_status === 'Completed');
                                            $isInProg = ($cycle->activity_status === 'In Progress');
                                        @endphp
                                        <tr class="hover:bg-[#F8FAFC]/80 transition-colors duration-150">
                                            {{-- Siklus --}}
                                            <td class="py-3.5 px-4 whitespace-nowrap">
                                                <div class="flex items-center gap-2.5">
                                                    <span class="w-7 h-7 rounded-lg {{ $isDone ? 'bg-[#8F0A0D] text-white' : ($isInProg ? 'bg-amber-500 text-white' : 'bg-[#E2E8F0] text-[#475569]') }} flex items-center justify-center text-[12px] font-black">
                                                        {{ $cycle->cycle_number }}
                                                    </span>
                                                    <div>
                                                        <span class="font-bold text-[#1E293B] block leading-tight">{{ $cycle->cycle_label }}</span>
                                                        <span class="text-[11px] text-[#94A3B8]">Kunjungan ke-{{ $cycle->cycle_number }}</span>
                                                    </div>
                                                </div>
                                            </td>

                                            {{-- Jadwal & Realisasi --}}
                                            <td class="py-3.5 px-4 whitespace-nowrap">
                                                <div class="font-mono text-[12px] text-[#334155]">
                                                    {{ $cycle->scheduled_date ? \Carbon\Carbon::parse($cycle->scheduled_date)->translatedFormat('d M Y') : '—' }}
                                                </div>
                                                <div class="text-[11px] text-[#64748B] mt-0.5">
                                                    Realisasi: <span class="font-semibold {{ $isDone ? 'text-emerald-700' : 'text-[#94A3B8]' }}">{{ $cycle->actual_date ? \Carbon\Carbon::parse($cycle->actual_date)->translatedFormat('d M Y') : 'Belum visit' }}</span>
                                                </div>
                                            </td>

                                            {{-- PIC Engineer --}}
                                            <td class="py-3.5 px-4 whitespace-nowrap">
                                                <div class="font-medium text-[#334155]">
                                                    {{ $cycle->assignedEngineer?->name ?: 'Belum Ditugaskan' }}
                                                </div>
                                            </td>

                                            {{-- Status Visit --}}
                                            <td class="py-3.5 px-4 whitespace-nowrap">
                                                @if($isDone)
                                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                                        Completed
                                                    </span>
                                                @elseif($isInProg)
                                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                                        In Progress
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                                        Scheduled
                                                    </span>
                                                @endif
                                            </td>

                                            {{-- Laporan PM (PDF) --}}
                                            <td class="py-3.5 px-4 whitespace-nowrap">
                                                <div class="flex items-center gap-2">
                                                    @if($cycle->report_file)
                                                        <a href="{{ asset('storage/' . $cycle->report_file) }}" target="_blank" class="px-2.5 py-1 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-800 text-[11px] font-bold transition border border-emerald-200 flex items-center gap-1.5 shadow-2xs">
                                                            <svg class="w-3.5 h-3.5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                            <span>Lihat PDF</span>
                                                        </a>
                                                    @else
                                                        <button type="button" @click="openPmModal({{ json_encode($cycle) }})" class="text-[11.5px] text-[#8F0A0D] font-bold hover:underline cursor-pointer flex items-center gap-1">
                                                            <span>+ Upload PDF</span>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>

                                            {{-- BA / Milestone --}}
                                            <td class="py-3.5 px-4 whitespace-nowrap">
                                                <div class="flex items-center gap-2">
                                                    @if($cycle->billing_ba_file)
                                                        <a href="{{ asset('storage/' . $cycle->billing_ba_file) }}" target="_blank" class="px-2.5 py-1 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-800 text-[11px] font-bold transition border border-blue-200 flex items-center gap-1.5 shadow-2xs">
                                                            <svg class="w-3.5 h-3.5 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                            <span>Dokumen BA</span>
                                                        </a>
                                                    @else
                                                        <button type="button" @click="openPmModal({{ json_encode($cycle) }})" class="text-[11.5px] text-[#8F0A0D] font-bold hover:underline cursor-pointer flex items-center gap-1">
                                                            <span>+ Upload BA</span>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>

                                            {{-- Aksi --}}
                                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                                <button type="button" @click="openPmModal({{ json_encode($cycle) }})" class="px-3 py-1.5 rounded-lg text-[12px] font-bold text-[#8F0A0D] hover:bg-[#FEF2F2] border border-[#FECACA] transition cursor-pointer">
                                                    Update Siklus
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="py-12 text-center text-[#64748B] text-[13px]">
                                                Belum ada siklus Preventive Maintenance yang diinisiasi pada proyek ini.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>

                <!-- ========================================================== -->
                <!-- 4. TAB 2: CORRECTIVE MAINTENANCE (CM) - FULL WIDTH         -->
                <!-- ========================================================== -->
                <div x-show="activeTab === 'cm'" x-cloak class="space-y-5 anim-fade-up">

                    {{-- Quota & Utilisasi Card --}}
                    <div class="ipnet-card p-6 space-y-4">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-[#E2E8F0] pb-3">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-[#8F0A0D]"></span>
                                <div>
                                    <h3 class="text-[15px] font-bold text-[#1E293B]">Utilisasi Kuota Corrective Maintenance — {{ $project->client }}</h3>
                                    <p class="text-[12px] text-[#64748B]">Total alokasi tiket insiden dan perbaikan darurat yang disepakati dalam kontrak SLA</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2.5">
                                <span class="text-[12px] font-bold px-3 py-1 rounded-full bg-rose-50 text-[#8F0A0D] border border-rose-200">
                                    Kuota Kontrak: {{ $cmQuota >= 999 ? 'Unlimited' : $cmQuota . ' Tiket' }}
                                </span>
                                @if($cmRemaining > 0 || $cmQuota >= 999)
                                    <button type="button" @click="isCmModalOpen = true" class="btn-ipnet-gradient px-4 py-2 rounded-xl text-[12.5px] font-bold flex items-center gap-2 cursor-pointer shadow-md text-white">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                        <span>Catat Tiket CM Baru</span>
                                    </button>
                                @endif
                            </div>
                        </div>

                        <!-- Symmetrical Quota Progress Bar -->
                        <div class="p-6 rounded-2xl bg-[#F8FAFC] border border-[#E2E8F0] space-y-3.5">
                            <div class="flex items-center justify-between text-[13px] font-bold text-[#334155]">
                                <span>Utilisasi Tiket Insiden / Perbaikan Lapangan</span>
                                <span class="text-[#8F0A0D] font-black">{{ $cmUsed }} dari {{ $cmQuota >= 999 ? '∞' : $cmQuota }} Tiket Terpakai</span>
                            </div>
                            <div class="w-full bg-[#E2E8F0] h-3 rounded-full overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-500" 
                                     style="background: linear-gradient(90deg, #8F0A0D, #D62E3C); width: {{ $cmQuota >= 999 ? '15%' : min(100, round(($cmUsed / max(1, $cmQuota)) * 100)) }}%;"></div>
                            </div>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-[12px] text-[#64748B] pt-1">
                                <div class="bg-white p-2.5 rounded-xl border border-[#E2E8F0] text-center">
                                    <span class="text-[10.5px] font-bold text-[#94A3B8] uppercase block">Tiket Terpakai</span>
                                    <strong class="text-[14px] text-[#1E293B]">{{ $cmUsed }} Tiket</strong>
                                </div>
                                <div class="bg-white p-2.5 rounded-xl border border-[#E2E8F0] text-center">
                                    <span class="text-[10.5px] font-bold text-[#94A3B8] uppercase block">Sisa Kuota Tiket</span>
                                    <strong class="text-[14px] text-emerald-700">{{ $cmRemaining }} Tiket</strong>
                                </div>
                                <div class="bg-white p-2.5 rounded-xl border border-[#E2E8F0] text-center">
                                    <span class="text-[10.5px] font-bold text-[#94A3B8] uppercase block">MTTR Target</span>
                                    <strong class="text-[14px] text-[#1E293B]">{{ $project->mttr_hours ?: 4 }} Jam</strong>
                                </div>
                                <div class="bg-white p-2.5 rounded-xl border border-[#E2E8F0] text-center">
                                    <span class="text-[10.5px] font-bold text-[#94A3B8] uppercase block">Target Uptime SLA</span>
                                    <strong class="text-[14px] text-[#8F0A0D]">{{ $project->sla_target_percentage ?: 99.50 }}%</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- CM Incidents Table --}}
                    <div class="ipnet-card overflow-hidden">
                        <div class="p-5 border-b border-[#E2E8F0] flex items-center justify-between">
                            <div>
                                <h3 class="text-[15px] font-bold text-[#1E293B]">Riwayat Insiden & Tiket CM — {{ $project->client }}</h3>
                                <p class="text-[12px] text-[#64748B] mt-0.5">Daftar penanganan insiden, perbaikan hardware, atau intervensi darurat khusus proyek ini</p>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full min-w-[900px] border-collapse text-[13px] text-left">
                                <thead>
                                    <tr class="bg-[#F8FAFC] border-b border-[#E2E8F0]">
                                        <th class="py-3.5 px-4 text-[11px] font-semibold text-[#64748B] uppercase tracking-wider">No. Tiket & Masalah</th>
                                        <th class="py-3.5 px-4 text-[11px] font-semibold text-[#64748B] uppercase tracking-wider">Pelapor & Tanggal</th>
                                        <th class="py-3.5 px-4 text-[11px] font-semibold text-[#64748B] uppercase tracking-wider">Prioritas & SLA</th>
                                        <th class="py-3.5 px-4 text-[11px] font-semibold text-[#64748B] uppercase tracking-wider">PIC Engineer</th>
                                        <th class="py-3.5 px-4 text-[11px] font-semibold text-[#64748B] uppercase tracking-wider">Status Tiket</th>
                                        <th class="py-3.5 px-4 text-[11px] font-semibold text-[#64748B] uppercase tracking-wider">Dokumen BAP</th>
                                        <th class="py-3.5 px-4 text-[11px] font-semibold text-[#64748B] uppercase tracking-wider text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-[#F1F5F9]">
                                    @forelse($cmTickets as $idx => $ticket)
                                        <tr class="hover:bg-[#F8FAFC]/80 transition-colors duration-150">
                                            {{-- No. Tiket & Masalah --}}
                                            <td class="py-3.5 px-4 max-w-sm">
                                                <div class="flex items-center gap-2 mb-1">
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10.5px] font-mono font-bold bg-[#F1F5F9] text-[#1E293B] border border-[#E2E8F0]">
                                                        {{ $ticket->ticket_number }}
                                                    </span>
                                                    <span class="text-[11px] text-[#94A3B8]">Tiket ke-{{ $idx + 1 }}</span>
                                                </div>
                                                <div class="font-bold text-[#1E293B] text-[13px] leading-snug">
                                                    {{ $ticket->title }}
                                                </div>
                                                @if($ticket->description)
                                                    <p class="text-[11.5px] text-[#64748B] mt-0.5 line-clamp-1">
                                                        {{ $ticket->description }}
                                                    </p>
                                                @endif
                                            </td>

                                            {{-- Pelapor & Tanggal --}}
                                            <td class="py-3.5 px-4 whitespace-nowrap">
                                                <div class="font-medium text-[#334155] text-[13px]">{{ $ticket->reported_by ?: '-' }}</div>
                                                <div class="text-[11px] text-[#64748B] mt-0.5">
                                                    {{ $ticket->incident_date ? \Carbon\Carbon::parse($ticket->incident_date)->translatedFormat('d M Y') : $ticket->created_at->translatedFormat('d M Y') }}
                                                </div>
                                            </td>

                                            {{-- Prioritas & SLA --}}
                                            <td class="py-3.5 px-4 whitespace-nowrap">
                                                @php
                                                    $prio = $ticket->priority ?: 'Gold';
                                                    $prioClass = match(strtolower($prio)) {
                                                        'platinum', 'p1 - critical', 'critical' => 'bg-purple-50 text-purple-700 border-purple-200',
                                                        'gold', 'p2 - major', 'major'           => 'bg-amber-50 text-amber-700 border-amber-200',
                                                        'silver', 'p3 - minor', 'minor'         => 'bg-slate-100 text-slate-700 border-slate-300',
                                                        default                                 => 'bg-blue-50 text-blue-700 border-blue-200',
                                                    };
                                                @endphp
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10.5px] font-bold border {{ $prioClass }}">
                                                    {{ $prio }}
                                                </span>
                                                @if($ticket->sla_deadline)
                                                    <div class="text-[10.5px] text-[#94A3B8] font-mono mt-0.5">
                                                        Batas: {{ $ticket->sla_deadline->format('H:i, d/m') }}
                                                    </div>
                                                @endif
                                            </td>

                                            {{-- PIC Engineer --}}
                                            <td class="py-3.5 px-4 whitespace-nowrap">
                                                <div class="font-medium text-[#334155]">
                                                    {{ $ticket->assignedEngineer?->name ?: '-' }}
                                                </div>
                                            </td>

                                            {{-- Status Tiket --}}
                                            <td class="py-3.5 px-4 whitespace-nowrap">
                                                @if(in_array($ticket->status, ['Resolved', 'Closed']))
                                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                                        {{ $ticket->status }}
                                                    </span>
                                                @elseif($ticket->status === 'In Progress')
                                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                                        In Progress
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-50 text-[#8F0A0D] border border-rose-200">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-[#8F0A0D]"></span>
                                                        Open
                                                    </span>
                                                @endif
                                            </td>

                                            {{-- Dokumen BAP --}}
                                            <td class="py-3.5 px-4 whitespace-nowrap">
                                                @if($ticket->attachment_path)
                                                    <a href="{{ asset('storage/' . $ticket->attachment_path) }}" target="_blank" class="px-2.5 py-1 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-[11px] font-bold transition border border-emerald-200 flex items-center gap-1.5 shadow-2xs">
                                                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                        <span>BAP Lampiran</span>
                                                    </a>
                                                @elseif(in_array($ticket->status, ['Resolved', 'Closed']))
                                                    <a href="{{ route('ms.monitoring.cm.bap', $ticket->id) }}" target="_blank" class="px-2.5 py-1 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-[11px] font-bold transition border border-emerald-200 flex items-center gap-1.5 shadow-2xs">
                                                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                        <span>Lihat BAP</span>
                                                    </a>
                                                @else
                                                    <a href="{{ route('ms.monitoring.cm.bap', $ticket->id) }}" target="_blank" class="px-2.5 py-1 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-700 text-[11px] font-bold transition border border-amber-200 flex items-center gap-1.5 shadow-2xs">
                                                        <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                        <span>Draft BAP</span>
                                                    </a>
                                                @endif
                                            </td>

                                            {{-- Aksi --}}
                                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                                <a href="{{ route('ms.tickets.index', ['search' => $ticket->ticket_number]) }}" class="px-2.5 py-1 rounded-lg text-[11.5px] font-bold text-[#475569] hover:text-[#1E293B] hover:bg-[#F1F5F9] border border-[#CBD5E1] transition cursor-pointer">
                                                    Buka Tiket
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="py-12 text-center text-[#64748B] text-[13px]">
                                                Belum ada insiden Corrective Maintenance (CM) yang dicatat pada proyek ini.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>

            @else
                <div class="ipnet-card p-12 text-center text-[#64748B]">
                    Pilih proyek untuk melihat monitoring Preventive & Corrective Maintenance.
                </div>
            @endif

        </div>
    </div>

    <!-- ========================================================== -->
    <!-- MODAL 1: UPDATE SIKLUS PM                                  -->
    <!-- ========================================================== -->
    <template x-teleport="body">
        <div x-show="isPmModalOpen" x-cloak class="fixed inset-0 z-50 bg-[#0E0D12]/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-[#E2E8F0] max-h-[90vh] overflow-y-auto" @click.away="isPmModalOpen = false">
                <div class="flex items-center justify-between border-b border-[#E2E8F0] pb-3 mb-4">
                    <div>
                        <p class="text-[11px] font-bold text-emerald-700 uppercase tracking-wider">Update Realisasi PM</p>
                        <h3 class="text-[16px] font-bold text-[#1E293B]" x-text="activeCycle ? activeCycle.cycle_label : 'Siklus PM'"></h3>
                    </div>
                    <button @click="isPmModalOpen = false" class="text-slate-400 hover:text-slate-700 p-1 rounded-lg text-lg font-bold">&times;</button>
                </div>

                <form :action="'/managed-service/monitoring/pm-cycles/' + (activeCycle ? activeCycle.id : '')" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-[12px] font-semibold text-[#334155] mb-1">Status Kunjungan (Activity) <span class="text-[#8F0A0D]">*</span></label>
                        <select name="activity_status" :value="activeCycle ? activeCycle.activity_status : 'Scheduled'" required class="w-full px-3.5 py-2.5 bg-white border border-[#CBD5E1] rounded-xl text-[12.5px] font-bold text-[#1E293B] outline-none focus:border-[#8F0A0D] cursor-pointer">
                            <option value="Scheduled">Scheduled (Terjadwal)</option>
                            <option value="In Progress">In Progress (Sedang Dikerjakan)</option>
                            <option value="Completed">Completed (Selesai Dilaksanakan)</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div>
                            <label class="block text-[12px] font-semibold text-[#334155] mb-1">Tanggal Realisasi Visit</label>
                            <input type="date" name="actual_date" :value="activeCycle ? activeCycle.actual_date : ''" class="w-full px-3.5 py-2 bg-white border border-[#CBD5E1] rounded-xl text-[12.5px] text-[#1E293B] outline-none focus:border-[#8F0A0D]">
                        </div>

                        <div>
                            <label class="block text-[12px] font-semibold text-[#334155] mb-1">PIC Field Engineer</label>
                            <select name="assigned_engineer_id" :value="activeCycle ? activeCycle.assigned_engineer_id : ''" class="w-full px-3.5 py-2 bg-white border border-[#CBD5E1] rounded-xl text-[12.5px] text-[#1E293B] outline-none focus:border-[#8F0A0D] cursor-pointer">
                                <option value="">Pilih Field Engineer</option>
                                @foreach($engineers as $eng)
                                    <option value="{{ $eng->id }}">{{ $eng->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[12px] font-semibold text-[#334155] mb-1">Upload Laporan PM (PDF)</label>
                        <input type="file" name="report_file" accept=".pdf" class="w-full px-3.5 py-2 border border-[#CBD5E1] rounded-xl text-[12px] bg-white outline-none focus:border-[#8F0A0D]">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div>
                            <label class="block text-[12px] font-semibold text-[#334155] mb-1">Status Laporan PM</label>
                            <select name="report_status" :value="activeCycle ? activeCycle.report_status : 'Pending'" class="w-full px-3.5 py-2 bg-white border border-[#CBD5E1] rounded-xl text-[12.5px] text-[#1E293B] outline-none focus:border-[#8F0A0D] cursor-pointer">
                                <option value="Pending">Pending (Belum Diupload)</option>
                                <option value="Submitted">Submitted (Menunggu Review)</option>
                                <option value="Approved">Approved (Disetujui Klien)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[12px] font-semibold text-[#334155] mb-1">Status BA / Milestone Penagihan</label>
                            <select name="billing_ba_status" :value="activeCycle ? activeCycle.billing_ba_status : 'Pending'" class="w-full px-3.5 py-2 bg-white border border-[#CBD5E1] rounded-xl text-[12.5px] text-[#1E293B] outline-none focus:border-[#8F0A0D] cursor-pointer">
                                <option value="Pending">Pending</option>
                                <option value="Submitted">Submitted</option>
                                <option value="Signed">Signed (Ditandatangani)</option>
                                <option value="Invoiced">Invoiced (Ditagihkan)</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[12px] font-semibold text-[#334155] mb-1">Upload Berita Acara (BA / Milestone)</label>
                        <input type="file" name="billing_ba_file" accept=".pdf,.doc,.docx" class="w-full px-3.5 py-2 border border-[#CBD5E1] rounded-xl text-[12px] bg-white outline-none focus:border-[#8F0A0D]">
                    </div>

                    <div>
                        <label class="block text-[12px] font-semibold text-[#334155] mb-1">Catatan Pelaksanaan Inspeksi</label>
                        <textarea name="notes" rows="2" :value="activeCycle ? activeCycle.notes : ''" placeholder="Catatan temuan teknis saat kunjungan..." class="w-full px-3.5 py-2 border border-[#CBD5E1] rounded-xl text-[12.5px] text-[#1E293B] outline-none focus:border-[#8F0A0D]"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-[#E2E8F0]">
                        <button type="button" @click="isPmModalOpen = false" class="px-4 py-2 rounded-xl text-[12.5px] font-semibold text-[#64748B] hover:bg-[#F8FAFC]">
                            Batal
                        </button>
                        <button type="submit" class="btn-ipnet-gradient px-5 py-2 rounded-xl text-[12.5px] font-bold shadow-md text-white">
                            Simpan Realisasi PM
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>

    <!-- ========================================================== -->
    <!-- MODAL 2: CATAT TIKET CORRECTIVE MAINTENANCE (CM)           -->
    <!-- ========================================================== -->
    <template x-teleport="body">
        <div x-show="isCmModalOpen" x-cloak class="fixed inset-0 z-50 bg-[#0E0D12]/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-[#E2E8F0] max-h-[90vh] overflow-y-auto" @click.away="isCmModalOpen = false">
                <div class="flex items-center justify-between border-b border-[#E2E8F0] pb-3 mb-4">
                    <div>
                        <p class="text-[11px] font-bold text-[#8F0A0D] uppercase tracking-wider">Tiket Insiden / Perbaikan</p>
                        <h3 class="text-[16px] font-bold text-[#1E293B]">Catat Tiket Corrective Maintenance</h3>
                    </div>
                    <button @click="isCmModalOpen = false" class="text-slate-400 hover:text-slate-700 p-1 rounded-lg text-lg font-bold">&times;</button>
                </div>

                <form action="{{ route('ms.monitoring.cm.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <input type="hidden" name="project_id" value="{{ $project ? $project->id : '' }}">

                    <div>
                        <label class="block text-[12px] font-semibold text-[#334155] mb-1">Judul Insiden / Aktivitas CM <span class="text-[#8F0A0D]">*</span></label>
                        <input type="text" name="title" required placeholder="Contoh: Penggantian SFP Module Port 2 Core Switch" class="w-full px-3.5 py-2.5 bg-white border border-[#CBD5E1] rounded-xl text-[12.5px] text-[#1E293B] outline-none focus:border-[#8F0A0D]">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div>
                            <label class="block text-[12px] font-semibold text-[#334155] mb-1">Prioritas & SLA <span class="text-[#8F0A0D]">*</span></label>
                            <select name="priority" required class="w-full px-3.5 py-2 bg-white border border-[#CBD5E1] rounded-xl text-[12.5px] text-[#1E293B] outline-none focus:border-[#8F0A0D] cursor-pointer">
                                <option value="Platinum">P1 - Critical (MTTR 1 Jam)</option>
                                <option value="Gold" selected>P2 - Major (MTTR 4 Jam)</option>
                                <option value="Silver">P3 - Minor (MTTR 8 Jam)</option>
                                <option value="Bronze">P4 - Low (MTTR 24 Jam)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[12px] font-semibold text-[#334155] mb-1">Tanggal Insiden</label>
                            <input type="date" name="incident_date" value="{{ date('Y-m-d') }}" class="w-full px-3.5 py-2 bg-white border border-[#CBD5E1] rounded-xl text-[12.5px] text-[#1E293B] outline-none focus:border-[#8F0A0D]">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div>
                            <label class="block text-[12px] font-semibold text-[#334155] mb-1">Nama Pelapor / PIC Klien</label>
                            <input type="text" name="reported_by" placeholder="Nama PIC" class="w-full px-3.5 py-2 bg-white border border-[#CBD5E1] rounded-xl text-[12.5px] text-[#1E293B] outline-none focus:border-[#8F0A0D]">
                        </div>

                        <div>
                            <label class="block text-[12px] font-semibold text-[#334155] mb-1">Kontak Telepon / WA</label>
                            <input type="text" name="contact_phone" placeholder="08xxxxxxxxxx" class="w-full px-3.5 py-2 bg-white border border-[#CBD5E1] rounded-xl text-[12.5px] text-[#1E293B] outline-none focus:border-[#8F0A0D]">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div>
                            <label class="block text-[12px] font-semibold text-[#334155] mb-1">PIC Field Support Engineer</label>
                            <select name="assigned_to" class="w-full px-3.5 py-2 bg-white border border-[#CBD5E1] rounded-xl text-[12.5px] text-[#1E293B] outline-none focus:border-[#8F0A0D] cursor-pointer">
                                <option value="">Pilih Teknisi</option>
                                @foreach($engineers as $eng)
                                    <option value="{{ $eng->id }}">{{ $eng->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-[12px] font-semibold text-[#334155] mb-1">Status Tiket</label>
                            <select name="status" class="w-full px-3.5 py-2 bg-white border border-[#CBD5E1] rounded-xl text-[12.5px] text-[#1E293B] outline-none focus:border-[#8F0A0D] cursor-pointer">
                                <option value="Open">Open (Baru Diterima)</option>
                                <option value="In Progress">In Progress (Sedang Ditangani)</option>
                                <option value="Resolved">Resolved (Selesai)</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[12px] font-semibold text-[#334155] mb-1">Rincian Gangguan / Masalah</label>
                        <textarea name="description" rows="2" placeholder="Jelaskan indikasi error atau alasan kebutuhan corrective..." class="w-full px-3.5 py-2 border border-[#CBD5E1] rounded-xl text-[12.5px] text-[#1E293B] outline-none focus:border-[#8F0A0D]"></textarea>
                    </div>

                    <div>
                        <label class="block text-[12px] font-semibold text-[#334155] mb-1">Upload Berita Acara Perbaikan (BAP / Foto)</label>
                        <input type="file" name="attachment" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" class="w-full px-3.5 py-2 border border-[#CBD5E1] rounded-xl text-[12px] bg-white outline-none focus:border-[#8F0A0D]">
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-[#E2E8F0]">
                        <button type="button" @click="isCmModalOpen = false" class="px-4 py-2 rounded-xl text-[12.5px] font-semibold text-[#64748B] hover:bg-[#F8FAFC]">
                            Batal
                        </button>
                        <button type="submit" class="btn-ipnet-gradient px-5 py-2 rounded-xl text-[12.5px] font-bold shadow-md text-white">
                            Catat Tiket CM
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>

</div>
@endsection
