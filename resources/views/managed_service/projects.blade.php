@extends('layouts.app')

@section('title', 'Daftar Proyek & Kontrak Service - PT IP Network Solusindo')

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
<div class="flex h-screen overflow-hidden bg-[#F8FAFC] font-sans" x-data="{ activeTab: '{{ request('tab', 'active') }}' }">
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Daftar Proyek'])
        
        <div class="p-4 sm:p-6 lg:p-7 space-y-5 max-w-[1600px] mx-auto">

            {{-- Flash Alert --}}
            @if(session('success'))
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between text-xs font-semibold anim-fade-up">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800 text-sm font-bold">&times;</button>
                </div>
            @endif

            <!-- ========================================================== -->
            <!-- 1. HEADER CARD (WITH SEGMENTED TAB SWITCHER)               -->
            <!-- ========================================================== -->
            <div class="ipnet-card p-5 sm:p-6 anim-fade-up anim-delay-1">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <div>
                        <p class="text-[#8F0A0D] text-[12px] font-bold inline-flex items-center uppercase tracking-wider">
                            <span class="ipnet-badge-dot"></span> MANAJEMEN PORTOFOLIO &amp; KONTRAK
                        </p>
                        <h2 class="text-[20px] font-bold text-[#1E293B] tracking-tight">Daftar Proyek &amp; Kontrak Service</h2>
                        <p class="text-[13px] text-[#64748B] mt-0.5">Kelola pelaksanaan kontrak berjalan, dokumen legal BAST/SPK, serah terima masuk (onboarding), dan monitoring SLA</p>
                    </div>

                    {{-- Tab Switcher: Segmented Toggle Styled Like Reference (Gambar 2) on the right side --}}
                    <div class="inline-flex p-1 bg-[#F1F5F9] rounded-xl border border-[#E2E8F0] shadow-2xs shrink-0 self-start lg:self-center">
                        {{-- Tab 1: Kontrak Layanan Aktif --}}
                        <button type="button" 
                                @click="activeTab = 'active'" 
                                :class="activeTab === 'active' 
                                    ? 'bg-[#8F0A0D] text-white shadow-xs font-bold' 
                                    : 'text-[#64748B] hover:text-[#1E293B] hover:bg-white/60 font-semibold'"
                                class="px-4 py-2 rounded-lg text-xs transition-all flex items-center gap-2 cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                            <span>Kontrak Layanan Aktif</span>
                            <span :class="activeTab === 'active' ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700'" 
                                  class="px-2 py-0.5 rounded-full text-[10.5px] font-bold">
                                {{ $projects->total() }}
                            </span>
                        </button>

                        {{-- Tab 2: Serah Terima Masuk (Incoming Handover) --}}
                        <button type="button" 
                                @click="activeTab = 'incoming'" 
                                :class="activeTab === 'incoming' 
                                    ? 'bg-[#8F0A0D] text-white shadow-xs font-bold' 
                                    : 'text-[#64748B] hover:text-[#1E293B] hover:bg-white/60 font-semibold'"
                                class="px-4 py-2 rounded-lg text-xs transition-all flex items-center gap-2 cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            <span>Incoming Handover</span>
                            <span :class="activeTab === 'incoming' ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700'" 
                                  class="px-2 py-0.5 rounded-full text-[10.5px] font-bold">
                                {{ $pendingHandoversCount ?? ($incomingHandovers->count() ?? 0) }}
                            </span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- ========================================================== -->
            <!-- TAB 1: KONTRAK LAYANAN AKTIF                               -->
            <!-- ========================================================== -->
            <div x-show="activeTab === 'active'" class="space-y-5">
                
                {{-- Filter Controls Tab 1 --}}
                <div class="ipnet-card p-4 sm:p-5 anim-fade-up anim-delay-2">
                    <form method="GET" action="{{ route('ms.projects.index') }}" class="flex flex-col md:flex-row flex-wrap items-center justify-between gap-3">
                        <input type="hidden" name="tab" value="active">
                        <div class="flex flex-col sm:flex-row flex-wrap items-center gap-3 w-full md:w-auto flex-1">
                            <div class="relative flex-1 min-w-[240px] w-full sm:w-auto">
                                <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-[#94A3B8]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                <input type="text" 
                                       name="search"
                                       value="{{ request('search') }}"
                                       placeholder="Cari nama project, client, sales, atau lokasi..." 
                                       class="w-full pl-9 pr-3.5 py-2 rounded-xl border border-[#CBD5E1] text-[12.5px] font-semibold text-[#1E293B] bg-white outline-none hover:border-[#94A3B8] focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition-all shadow-xs placeholder-[#94A3B8]">
                            </div>
                            
                            <select name="status" onchange="this.form.submit()" 
                                    class="w-full sm:w-44 px-3 py-2 rounded-xl border border-[#CBD5E1] text-[12.5px] font-semibold text-[#1E293B] bg-white outline-none hover:border-[#94A3B8] focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition-all shadow-xs cursor-pointer">
                                <option value="all">Semua Status</option>
                                <option value="Active" {{ request('status') === 'Active' ? 'selected' : '' }}>Active / Operate</option>
                                <option value="In Progress" {{ request('status') === 'In Progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="Planning" {{ request('status') === 'Planning' ? 'selected' : '' }}>Planning</option>
                                <option value="Completed" {{ request('status') === 'Completed' ? 'selected' : '' }}>Completed</option>
                            </select>

                            <select name="type" onchange="this.form.submit()" 
                                    class="w-full sm:w-52 px-3 py-2 rounded-xl border border-[#CBD5E1] text-[12.5px] font-semibold text-[#1E293B] bg-white outline-none hover:border-[#94A3B8] focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition-all shadow-xs cursor-pointer">
                                <option value="all">Semua Tipe Proyek</option>
                                <option value="Managed Service SLA" {{ request('type') === 'Managed Service SLA' ? 'selected' : '' }}>Managed Service SLA</option>
                                <option value="Maintenance" {{ request('type') === 'Maintenance' ? 'selected' : '' }}>Maintenance Berkala</option>
                                <option value="One-Time" {{ request('type') === 'One-Time' ? 'selected' : '' }}>One-Time Project</option>
                            </select>

                            @if(request('search') || (request('status') && request('status') !== 'all') || (request('type') && request('type') !== 'all') || (request('per_page') && request('per_page') != 10))
                                <a href="{{ route('ms.projects.index', ['tab' => 'active']) }}" 
                                   class="px-3 py-2 text-[12px] font-bold text-[#64748B] hover:text-[#8F0A0D] bg-[#F8FAFC] hover:bg-[#FEF2F2] border border-[#E2E8F0] hover:border-[#FCA5A5] rounded-xl transition cursor-pointer">
                                    Reset Filter
                                </a>
                            @endif
                        </div>

                        <div class="flex items-center gap-2 self-end md:self-center shrink-0">
                            <span class="text-[12px] font-semibold text-[#64748B]">Tampilkan:</span>
                            <select name="per_page" onchange="this.form.submit()" class="px-2.5 py-1.5 rounded-xl border border-[#CBD5E1] text-[12px] font-bold text-[#1E293B] bg-white outline-none hover:border-[#94A3B8] focus:border-[#8F0A0D] cursor-pointer shadow-xs">
                                <option value="5" {{ request('per_page', $perPage ?? 10) == 5 ? 'selected' : '' }}>5 baris</option>
                                <option value="10" {{ request('per_page', $perPage ?? 10) == 10 ? 'selected' : '' }}>10 baris</option>
                                <option value="25" {{ request('per_page', $perPage ?? 10) == 25 ? 'selected' : '' }}>25 baris</option>
                                <option value="50" {{ request('per_page', $perPage ?? 10) == 50 ? 'selected' : '' }}>50 baris</option>
                            </select>
                        </div>
                    </form>
                </div>

                {{-- Table Card Tab 1 --}}
                <div class="ipnet-card overflow-hidden anim-fade-up anim-delay-3">
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[900px] border-collapse text-[13px] text-left">
                            <thead>
                                <tr class="bg-[#F8FAFC] border-b border-[#E2E8F0]">
                                    <th class="py-3 px-4 text-[11px] font-semibold text-[#64748B] uppercase tracking-wider">Nama Project &amp; Tipe</th>
                                    <th class="py-3 px-4 text-[11px] font-semibold text-[#64748B] uppercase tracking-wider">Client &amp; Sales</th>
                                    <th class="py-3 px-4 text-[11px] font-semibold text-[#64748B] uppercase tracking-wider">Lokasi</th>
                                    <th class="py-3 px-4 text-[11px] font-semibold text-[#64748B] uppercase tracking-wider">Durasi &amp; Deadline</th>
                                    <th class="py-3 px-4 text-[11px] font-semibold text-[#64748B] uppercase tracking-wider">Progress Siklus PM</th>
                                    <th class="py-3 px-4 text-[11px] font-semibold text-[#64748B] uppercase tracking-wider">Status</th>
                                    <th class="py-3 px-4 text-[11px] font-semibold text-[#64748B] uppercase tracking-wider text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#F1F5F9]">
                                @forelse($projects as $p)
                                    @php
                                        $pmCyclesCount = $p->pmCycles->count() ?: ($p->pm_total_cycles ?: 4);
                                        $pmCompletedCount = $p->pmCycles->where('activity_status', 'Completed')->count();
                                        $progressPct = $pmCyclesCount > 0 ? round(($pmCompletedCount / $pmCyclesCount) * 100) : 0;
                                        
                                        $deadlineDate = $p->service_end_date ?: $p->deadline;
                                        $formattedDeadline = $deadlineDate ? \Carbon\Carbon::parse($deadlineDate)->translatedFormat('d M Y') : '—';
                                        $startDate = $p->service_start_date ?: $p->start_date;
                                        
                                        $durationText = '—';
                                        if ($startDate && $deadlineDate) {
                                            $s = \Carbon\Carbon::parse($startDate);
                                            $e = \Carbon\Carbon::parse($deadlineDate);
                                            $diffMonths = round($s->floatDiffInMonths($e), 1);
                                            if ($diffMonths >= 12) {
                                                $durationText = round($diffMonths / 12, 1) . ' Tahun';
                                            } else {
                                                $durationText = $diffMonths . ' Bulan';
                                            }
                                        }
                                    @endphp
                                    <tr class="hover:bg-[#F8FAFC]/80 transition-colors duration-150">
                                        {{-- Nama Project & Tipe --}}
                                        <td class="py-3 px-4">
                                            <div class="flex items-center gap-1.5 flex-wrap">
                                                <a href="{{ route('ms.projects.show', $p->id) }}" class="font-medium text-[#1E293B] text-[13px] leading-snug hover:text-[#8F0A0D] transition">
                                                    {{ $p->name }}
                                                </a>
                                                @if($p->sla_tier)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10.5px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                                        Tier: {{ $p->sla_tier }}
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="flex items-center gap-1.5 mt-0.5">
                                                <span class="inline-block text-[10.5px] font-normal text-[#64748B] bg-[#F1F5F9] px-2 py-0.5 rounded border border-[#E2E8F0]">
                                                    {{ $p->project_type ?: 'Managed Service SLA' }}
                                                </span>
                                                @if($p->pm_frequency_type)
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10.5px] font-medium bg-[#EEF2FF] text-[#4F46E5] border border-[#E0E7FF]">
                                                        <svg class="w-3 h-3 text-[#4F46E5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                                        <span>PM: {{ str_replace('_', ' ', $p->pm_frequency_type) }}</span>
                                                    </span>
                                                @endif
                                            </div>
                                        </td>

                                        {{-- Client & Sales --}}
                                        <td class="py-3 px-4">
                                            <div class="font-medium text-[#334155] text-[13px]">{{ $p->client }}</div>
                                            <div class="text-[11.5px] text-[#64748B] flex items-center gap-1.5 mt-0.5">
                                                <span class="text-[#94A3B8]">Sales:</span>
                                                <span class="text-[#475569]">{{ $p->sales_name ?: ($p->creator?->name ?: '-') }}</span>
                                            </div>
                                        </td>

                                        {{-- Lokasi --}}
                                        <td class="py-3 px-4 text-[#475569] text-[12.5px] max-w-[160px] truncate" title="{{ $p->location ?: 'On-Site Klien' }}">
                                            {{ $p->location ?: '—' }}
                                        </td>

                                        {{-- Durasi & Deadline --}}
                                        <td class="py-3 px-4 whitespace-nowrap">
                                            <div class="font-mono text-[12px] text-[#334155]">{{ $formattedDeadline }}</div>
                                            <div class="text-[11px] text-[#64748B] mt-0.5 flex items-center gap-1">
                                                <span>Durasi:</span>
                                                <span class="font-medium text-[#8F0A0D]">{{ $durationText }}</span>
                                            </div>
                                        </td>

                                        {{-- Progress Siklus PM --}}
                                        <td class="py-3 px-4 whitespace-nowrap">
                                            <div class="w-32">
                                                <div class="w-full bg-[#F1F5F9] border border-[#E2E8F0] rounded-full h-2 overflow-hidden mb-1">
                                                    <div class="h-full rounded-full transition-all duration-300"
                                                         style="background: linear-gradient(90deg, #8F0A0D, #D62E3C); width: {{ min(100, $progressPct) }}%;"></div>
                                                </div>
                                                <div class="flex items-center justify-between text-[10.5px]">
                                                    <span class="font-bold text-[#1E293B]">{{ $progressPct }}%</span>
                                                    <span class="text-[#64748B] font-medium text-[10px]">{{ $pmCompletedCount }}/{{ $pmCyclesCount }} Siklus PM</span>
                                                </div>
                                            </div>
                                        </td>

                                        {{-- Status --}}
                                        <td class="py-3 px-4 whitespace-nowrap">
                                            @if(in_array(strtolower($p->status ?? ''), ['active', 'operate']) || in_array(strtolower($p->stage ?? ''), ['active', 'operate']))
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 animate-pulse"></span>
                                                    Active
                                                </span>
                                            @elseif(in_array(strtolower($p->status ?? ''), ['completed', 'selesai']))
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                                                    Completed
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                    {{ $p->status ?: 'In Progress' }}
                                                </span>
                                            @endif
                                        </td>

                                        {{-- Aksi --}}
                                        <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                            <div class="flex justify-end items-center gap-1.5">
                                                {{-- Buka Detail Kontrak & Dokumen --}}
                                                <a href="{{ route('ms.projects.show', ['project' => $p->id, 'tab' => 'documents']) }}" title="Buka Detail Proyek & Dokumen (SPK, Kontrak, BOQ, BA)" class="p-1.5 rounded-lg text-[#8F0A0D] hover:bg-[#FEF2F2] transition cursor-pointer flex items-center justify-center">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                    </svg>
                                                </a>

                                                {{-- Monitoring PM --}}
                                                <a href="{{ route('ms.projects.show', ['project' => $p->id, 'tab' => 'pm']) }}" title="Monitoring Preventive Maintenance (PM)" class="p-1.5 rounded-lg text-[#64748B] hover:text-emerald-700 hover:bg-emerald-50 transition cursor-pointer flex items-center justify-center">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                                    </svg>
                                                </a>

                                                 {{-- Monitoring CM --}}
                                                <a href="{{ route('ms.projects.show', ['project' => $p->id, 'tab' => 'cm']) }}" title="Monitoring Corrective Maintenance (CM)" class="p-1.5 rounded-lg text-[#64748B] hover:text-[#8F0A0D] hover:bg-rose-50 transition cursor-pointer flex items-center justify-center">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                                    </svg>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="py-12 text-center text-[#64748B] text-[13px]">
                                            Tidak ada proyek Managed Service yang sesuai dengan kriteria filter.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($projects instanceof \Illuminate\Pagination\LengthAwarePaginator && $projects->hasPages())
                        <div class="p-4 border-t border-[#E2E8F0] bg-white flex flex-col sm:flex-row items-center justify-between gap-3">
                            <div class="text-[12px] text-[#64748B]">
                                Menampilkan {{ $projects->firstItem() ?? 0 }} - {{ $projects->lastItem() ?? 0 }} dari total {{ $projects->total() }} proyek
                            </div>
                            <div>
                                {{ $projects->links() }}
                            </div>
                        </div>
                    @endif
                </div>

            </div>

            <!-- ========================================================== -->
            <!-- TAB 2: SERAH TERIMA MASUK (INCOMING HANDOVER)              -->
            <!-- ========================================================== -->
            <div x-show="activeTab === 'incoming'" class="space-y-5" x-cloak>
                

                {{-- Table Card Tab 2 --}}
                <div class="ipnet-card overflow-hidden anim-fade-up anim-delay-2">
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[900px] border-collapse text-[13px] text-left">
                            <thead>
                                <tr class="bg-[#F8FAFC] border-b border-[#E2E8F0]">
                                    <th class="py-3 px-4 text-[11px] font-semibold text-[#64748B] uppercase tracking-wider">Proyek &amp; Klien</th>
                                    <th class="py-3 px-4 text-[11px] font-semibold text-[#64748B] uppercase tracking-wider">Dokumen &amp; BAST</th>
                                    <th class="py-3 px-4 text-[11px] font-semibold text-[#64748B] uppercase tracking-wider">SLA &amp; Coverage</th>
                                    <th class="py-3 px-4 text-[11px] font-semibold text-[#64748B] uppercase tracking-wider">Jadwal PM &amp; Periode</th>
                                    <th class="py-3 px-4 text-[11px] font-semibold text-[#64748B] uppercase tracking-wider text-center">Status Layanan</th>
                                    <th class="py-3 px-4 text-[11px] font-semibold text-[#64748B] uppercase tracking-wider text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#F1F5F9]">
                                @forelse($incomingHandovers as $ho)
                                    @php
                                        $isAccepted = ($ho->ms_handover_status === 'Accepted');
                                        $formattedValue = $ho->contract_value ? 'Rp ' . number_format($ho->contract_value, 0, ',', '.') : null;
                                        $startDateCarbon = $ho->service_start_date 
                                            ? \Carbon\Carbon::parse($ho->service_start_date) 
                                            : ($ho->start_date ? \Carbon\Carbon::parse($ho->start_date) : now());

                                        // Validasi kontrak maintenance: jika service_end_date kosong atau <= start_date, hitung 1 tahun penuh (12 bulan)
                                        $hasValidEndDate = $ho->service_end_date && \Carbon\Carbon::parse($ho->service_end_date)->gt($startDateCarbon);
                                        $hasValidDeadline = $ho->deadline && \Carbon\Carbon::parse($ho->deadline)->gt($startDateCarbon);

                                        $endDateCarbon = $hasValidEndDate 
                                            ? \Carbon\Carbon::parse($ho->service_end_date) 
                                            : ($hasValidDeadline ? \Carbon\Carbon::parse($ho->deadline) : (clone $startDateCarbon)->addYear()->subDay());

                                        $monthsId = [
                                            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                                        ];

                                        $startDateVal = $startDateCarbon->format('Y-m-d');
                                        $endDateVal = $endDateCarbon->format('Y-m-d');
                                        $startDateDisplay = $startDateCarbon->format('d') . ' ' . ($monthsId[(int)$startDateCarbon->format('n')] ?? $startDateCarbon->format('F')) . ' ' . $startDateCarbon->format('Y');
                                        $endDateDisplay = $endDateCarbon->format('d') . ' ' . ($monthsId[(int)$endDateCarbon->format('n')] ?? $endDateCarbon->format('F')) . ' ' . $endDateCarbon->format('Y');

                                        // Resolusi nomor kontrak / PO yang valid dan resmi
                                        $resolvedContractNum = $ho->po_spk_number 
                                            ?: ($ho->po_number 
                                            ?: ($ho->so_code 
                                            ?: ($ho->quotation_number 
                                            ?: 'KTR/IPNET/' . ($ho->created_at ? $ho->created_at->format('Y') : date('Y')) . '/' . str_pad($ho->id, 4, '0', STR_PAD_LEFT))));
                                    @endphp
                                    <tr class="hover:bg-[#F8FAFC]/80 transition-colors {{ !$isAccepted ? 'bg-[#FFFDF7]' : '' }}">
                                        {{-- 1. Proyek & Klien --}}
                                        <td class="py-3.5 px-4">
                                            <a href="{{ route('ms.projects.show', $ho->id) }}" class="font-semibold text-[#1E293B] text-[13px] hover:text-[#8F0A0D] transition block leading-snug">
                                                {{ $ho->name }}
                                            </a>
                                            <div class="text-[11.5px] text-[#64748B] mt-0.5 flex items-center gap-1.5 flex-wrap">
                                                <span class="font-medium text-[#475569]">{{ $ho->client ?: '—' }}</span>
                                                @if($formattedValue)
                                                    <span class="text-[#CBD5E1]">&bull;</span>
                                                    <span class="text-[#8F0A0D] font-mono font-medium">{{ $formattedValue }}</span>
                                                @endif
                                            </div>
                                        </td>

                                        {{-- 2. Dokumen & BAST --}}
                                        <td class="py-3.5 px-4">
                                            <div class="space-y-0.5">
                                                @if($ho->bast_file)
                                                    <a href="{{ asset('storage/' . $ho->bast_file) }}" target="_blank" class="inline-flex items-center gap-1 text-[11.5px] font-semibold text-emerald-700 hover:text-emerald-800 transition">
                                                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                        <span>BAST Terlampir</span>
                                                    </a>
                                                @else
                                                    <span class="inline-flex items-center gap-1 text-[11.5px] text-[#94A3B8]">
                                                        <svg class="w-3.5 h-3.5 text-[#CBD5E1]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                        <span>Belum Ada BAST</span>
                                                    </span>
                                                @endif

                                                @if($ho->po_spk_number)
                                                    <div class="text-[11px] font-mono text-[#64748B]">
                                                        SPK: <span class="text-[#334155] font-medium">{{ $ho->po_spk_number }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </td>

                                        {{-- 3. SLA & Coverage --}}
                                        <td class="py-3.5 px-4 whitespace-nowrap">
                                            <div class="font-semibold text-[#1E293B] text-[12.5px] flex items-center gap-1.5">
                                                <span class="w-1.5 h-1.5 rounded-full bg-[#8F0A0D]"></span>
                                                <span>{{ $ho->sla_tier ?: 'Gold' }} Tier</span>
                                            </div>
                                            <div class="text-[11px] text-[#64748B] mt-0.5">
                                                Jam Dukungan: <span class="font-medium text-[#334155]">{{ $ho->sla_coverage_hours ?: '24×7' }}</span>
                                            </div>
                                        </td>

                                        {{-- 4. Jadwal PM & Periode --}}
                                        <td class="py-3.5 px-4 whitespace-nowrap">
                                            <div class="font-semibold text-[#1E293B] text-[12.5px]">
                                                {{ $ho->maintenance_frequency ?: 'Bulanan (Monthly)' }}
                                            </div>
                                            <div class="text-[11px] text-[#64748B] mt-0.5 font-mono">
                                                {{ $ho->service_start_date ? \Carbon\Carbon::parse($ho->service_start_date)->translatedFormat('d M Y') : 'Mulai: ' . date('d M Y') }}
                                                @if($ho->service_end_date)
                                                    &ndash; {{ \Carbon\Carbon::parse($ho->service_end_date)->translatedFormat('d M Y') }}
                                                @endif
                                            </div>
                                        </td>

                                        {{-- 5. Status Layanan --}}
                                        <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                            @if($isAccepted)
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                                    <span>Layanan Aktif</span>
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                    <span>Menunggu Aktivasi</span>
                                                </span>
                                            @endif
                                        </td>

                                        {{-- 6. Aksi --}}
                                        <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                            <div class="flex items-center justify-end gap-1.5">
                                                @php
                                                    $resolvedLeadMs = $ho->msAcceptedBy?->name ?? ($ho->handover_data['lead_ms_name'] ?? ($leadMsName ?? 'Doris'));
                                                @endphp
                                                @if(!$isAccepted)
                                                    <button type="button" 
                                                            data-id="{{ $ho->id }}"
                                                            data-name="{{ $ho->name }}"
                                                            data-client="{{ $ho->client ?: '-' }}"
                                                            data-value="{{ $formattedValue ?: '—' }}"
                                                            data-tier="{{ $ho->sla_tier ?: 'Gold' }}"
                                                            data-coverage="{{ $ho->sla_coverage_hours ?: '24x7' }}"
                                                            data-freq="{{ $ho->maintenance_frequency ?: 'Monthly' }}"
                                                            data-start="{{ $startDateVal }}"
                                                            data-end="{{ $endDateVal }}"
                                                            data-start-display="{{ $startDateDisplay }}"
                                                            data-end-display="{{ $endDateDisplay }}"
                                                            data-notes="{{ $ho->special_notes ?: '' }}"
                                                            data-so="{{ $resolvedContractNum }}"
                                                            data-sales="{{ $ho->sales_name ?: ($ho->creator?->name ?: '-') }}"
                                                            data-pm="{{ is_object($ho->pm) ? ($ho->pm->name ?? 'Tim Managed Service') : ($ho->pm ?: 'Tim Managed Service') }}"
                                                            data-lead-ms="{{ $resolvedLeadMs }}"
                                                            data-docs="{{ json_encode($ho->resolved_documents ?? []) }}"
                                                            data-handover-data="{{ json_encode($ho->handover_data ?? []) }}"
                                                            data-checklist="{{ json_encode($ho->documents_checklist ?? []) }}"
                                                            onclick="window.triggerAcceptModal(this)"
                                                            class="px-3 py-1.5 bg-[#8F0A0D] hover:bg-[#73080A] text-white text-[11.5px] font-bold rounded-lg shadow-2xs transition cursor-pointer flex items-center gap-1.5 active:scale-95">
                                                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                                        </svg>
                                                        <span>Terima &amp; Aktivasi</span>
                                                    </button>
                                                @else
                                                    <button type="button" 
                                                            data-id="{{ $ho->id }}"
                                                            data-name="{{ $ho->name }}"
                                                            data-client="{{ $ho->client ?: '-' }}"
                                                            data-value="{{ $formattedValue ?: '—' }}"
                                                            data-tier="{{ $ho->sla_tier ?: 'Gold' }}"
                                                            data-coverage="{{ $ho->sla_coverage_hours ?: '24x7' }}"
                                                            data-freq="{{ $ho->maintenance_frequency ?: 'Monthly' }}"
                                                            data-start="{{ $startDateVal }}"
                                                            data-end="{{ $endDateVal }}"
                                                            data-start-display="{{ $startDateDisplay }}"
                                                            data-end-display="{{ $endDateDisplay }}"
                                                            data-notes="{{ $ho->special_notes ?: '' }}"
                                                            data-so="{{ $resolvedContractNum }}"
                                                            data-sales="{{ $ho->sales_name ?: ($ho->creator?->name ?: '-') }}"
                                                            data-pm="{{ is_object($ho->pm) ? ($ho->pm->name ?? 'Tim Managed Service') : ($ho->pm ?: 'Tim Managed Service') }}"
                                                            data-lead-ms="{{ $resolvedLeadMs }}"
                                                            data-docs="{{ json_encode($ho->resolved_documents ?? []) }}"
                                                            data-handover-data="{{ json_encode($ho->handover_data ?? []) }}"
                                                            data-checklist="{{ json_encode($ho->documents_checklist ?? []) }}"
                                                            onclick="window.triggerAcceptModal(this)"
                                                            class="px-3 py-1.5 bg-white hover:bg-[#F8FAFC] text-[#334155] hover:text-[#1E293B] border border-[#CBD5E1] text-[11.5px] font-semibold rounded-lg shadow-2xs transition cursor-pointer active:scale-95">
                                                        <span>Edit SLA</span>
                                                    </button>
                                                @endif

                                                <a href="{{ route('ms.projects.show', ['project' => $ho->id, 'tab' => 'documents']) }}" title="Buka Detail Proyek &amp; Dokumen" class="p-1.5 rounded-lg text-[#64748B] hover:text-[#8F0A0D] hover:bg-[#FEF2F2] transition cursor-pointer flex items-center justify-center">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                                    </svg>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-12 text-center text-[#64748B] text-[13px]">
                                            <div class="flex flex-col items-center justify-center gap-2">
                                                <svg class="w-8 h-8 text-[#94A3B8]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                                <span class="font-medium">Tidak ada serah terima pending (semua kontrak layanan telah disetujui & aktif di tab Kontrak Layanan Aktif).</span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>

<!-- =================================================================== -->
<!-- MODAL TERIMA & AKTIVASI LAYANAN MANAGED SERVICE                     -->
<!-- =================================================================== -->
<div id="modal-accept-handover" 
     tabindex="-1"
     style="display: none;" 
     onclick="if(event.target === this) window.closeAcceptModal()"
     class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-5 overflow-y-auto">
    <div id="printable-accept-modal" class="bg-white rounded-2xl max-w-7xl w-full shadow-2xl border border-[#CBD5E1] max-h-[94vh] flex flex-col overflow-hidden my-auto">
        
        {{-- Header Bar Persis Gambar 2 --}}
        <div class="px-6 py-4 border-b border-[#E2E8F0] flex items-center justify-between bg-[#F8FAFC] shrink-0">
            <div class="flex items-center gap-2.5">
                <span class="w-2.5 h-2.5 rounded-full bg-[#8F0A0D]"></span>
                <div>
                    <h3 class="text-[16px] font-bold text-[#0F172A]" id="accept-modal-header-title">Aktivasi: Serah Terima Layanan Managed Service</h3>
                    <p class="text-[11px] text-[#64748B]">Verifikasi checklist serah terima resmi di bawah ini sesuai acuan template PT IP Network Solusindo.</p>
                </div>
            </div>
            <button type="button" onclick="window.closeAcceptModal()" class="text-[#64748B] hover:text-[#1E293B] p-1.5 rounded-lg hover:bg-[#E2E8F0] transition cursor-pointer" title="Tutup">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Form Body --}}
        <form id="form-accept-handover" 
              action="" 
              method="POST" 
              class="flex flex-col flex-1 min-h-0 overflow-hidden">
            @csrf

            {{-- Background Container Abu-Abu Lembut Sesuai Template Lebar --}}
            <div class="flex-1 overflow-y-auto px-4 pt-4 pb-8 sm:px-8 sm:pt-6 sm:pb-8 bg-[#F1F5F9]/80 space-y-4">
                
                {{-- Banner Kategori Laporan Konsisten dengan Form Engineer Activity --}}
                <div class="flex flex-wrap items-center justify-between gap-3 bg-white p-3.5 rounded-xl border border-[#CBD5E1] shadow-xs no-print">
                    <div class="flex items-center gap-2.5">
                        <span class="text-xs font-bold text-[#475569] uppercase tracking-wider">KATEGORI LAPORAN:</span>
                        <span class="px-3 py-1 bg-red-50 text-[#8F0A0D] border border-red-200 rounded-lg text-xs font-bold tracking-wide uppercase shadow-2xs">
                            Serah Terima Managed Service
                        </span>
                    </div>

                    <div class="text-xs text-slate-500 font-medium">
                        Format resmi PT IP Network Solusindo
                    </div>
                </div>

                {{-- ============================================================== --}}
                {{-- DOKUMEN RESMI SERAH TERIMA KONTRAK MAINTENANCE SESUAI PDF       --}}
                {{-- ============================================================== --}}
                <div class="bg-white border-2 border-[#CBD5E1] rounded-xl p-6 sm:p-10 shadow-sm space-y-5 text-[#0F172A] font-sans relative">
                    
                    {{-- Kop Dokumen Persis PDF (Proporsional & Rapi) --}}
                    <div class="flex items-start justify-between gap-4">
                        <div class="shrink-0">
                            <img src="{{ asset('images/ipnet1.png') }}" onerror="this.src='/images/ipnet.png'" alt="Logo IP Network Solusindo" class="h-12 sm:h-14 w-auto object-contain">
                        </div>
                        <div class="text-right shrink-0">
                            <div class="text-[#8F0A0D] font-black text-lg sm:text-xl uppercase tracking-tight leading-none">
                                CHECKLIST SERAH TERIMA
                            </div>
                            <div class="text-black font-black text-xs sm:text-sm uppercase tracking-normal mt-1">
                                IPNET#1
                            </div>
                            <div class="text-[10px] text-slate-500 italic font-medium mt-0.5 hidden print:block">
                                Hal 1
                            </div>
                        </div>
                    </div>

                    {{-- Judul Utama Dokumen Persis PDF --}}
                    <div class="text-center my-3 sm:my-4">
                        <h1 class="text-sm sm:text-base font-black text-black uppercase tracking-wide leading-snug">
                            SERAH TERIMA SALES / COMMERCIAL KE MANAGED SERVICE<br>
                            UNTUK KONTRAK MAINTENANCE
                        </h1>
                        <p class="text-xs italic text-slate-600 mt-1 font-serif">
                            Commercial-to-Project Handover Checklist
                        </p>
                    </div>

                    {{-- Petunjuk Pengisian Persis PDF --}}
                    <div class="text-[11.5px] text-slate-800 leading-relaxed border border-slate-300 bg-slate-50/70 p-3 rounded-lg">
                        <strong class="font-bold text-black">Petunjuk:</strong> Isi kolom yang relevan. Jika suatu butir tidak berlaku, tulis <span class="font-semibold">“Tidak Berlaku”</span> dan berikan catatan bila diperlukan. Simpan formulir beserta bukti pendukung pada lokasi dokumen yang ditetapkan.
                    </div>

                    {{-- 1. REFERENSI KONTRAK (Banner Hitam & Border Hitam Persis PDF) --}}
                    <div class="border border-black overflow-hidden">
                        <div class="bg-black text-white px-3 py-1.5 font-black text-xs uppercase tracking-wider">
                            REFERENSI KONTRAK
                        </div>
                        <table class="w-full border-collapse text-xs">
                            <tbody>
                                <tr class="border-b border-black">
                                    <td class="w-1/3 sm:w-1/4 py-2 px-3 font-bold bg-white border-r border-black">Pelanggan</td>
                                    <td class="py-2 px-3 font-black text-slate-900" id="accept-modal-client">—</td>
                                </tr>
                                <tr class="border-b border-black">
                                    <td class="py-2 px-3 font-bold bg-white border-r border-black">Nomor kontrak / PO</td>
                                    <td class="py-2 px-3 font-mono font-bold text-slate-900" id="accept-modal-contract-num">—</td>
                                </tr>
                                <tr class="border-b border-black">
                                    <td class="py-2 px-3 font-bold bg-white border-r border-black">Tanggal efektif dan masa kontrak</td>
                                    <td class="py-2 px-3 font-medium text-slate-800" id="accept-modal-dates">—</td>
                                </tr>
                                <tr class="border-b border-black">
                                    <td class="py-2 px-3 font-bold bg-white border-r border-black">Tanggal berakhir</td>
                                    <td class="py-2 px-3 font-semibold text-slate-800" id="accept-modal-end-text">—</td>
                                </tr>
                                <tr class="border-b border-black">
                                    <td class="py-2 px-3 font-bold bg-white border-r border-black">PIC Commercial</td>
                                    <td class="py-2 px-3 font-bold text-slate-900" id="accept-modal-sales">—</td>
                                </tr>
                                <tr>
                                    <td class="py-2 px-3 font-bold bg-white border-r border-black">PIC Managed Service</td>
                                    <td class="py-2 px-3 font-bold text-[#8F0A0D]" id="accept-modal-pm">{{ $leadMsName ?? 'Doris' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    {{-- 2. CHECKLIST (4 Kolom Sesuai PDF: NO. | INFORMASI | STATUS / ISIAN | CATATAN) --}}
                    <div class="border border-black overflow-hidden">
                        <div class="bg-black text-white px-3 py-1.5 font-black text-xs uppercase tracking-wider">
                            CHECKLIST
                        </div>
                        <table class="w-full border-collapse text-xs text-left">
                            <thead>
                                <tr class="bg-white text-black text-[11px] font-black uppercase tracking-wider border-b border-black">
                                    <th class="py-2 px-2.5 w-12 text-center border-r border-black">NO.</th>
                                    <th class="py-2 px-3 border-r border-black">INFORMASI</th>
                                    <th class="py-2 px-2 text-center w-36 sm:w-44 border-r border-black">STATUS / ISIAN</th>
                                    <th class="py-2 px-3 w-1/3 sm:w-2/5">CATATAN</th>
                                </tr>
                            </thead>
                            <tbody id="accept-modal-checklist-tbody" class="bg-white divide-y divide-black">
                                {{-- Dirender oleh JS --}}
                            </tbody>
                        </table>
                    </div>

                    {{-- 3. CATATAN KHUSUS (Urutan Sesuai PDF: Langsung Di Bawah Checklist) --}}
                    <div class="border border-black overflow-hidden">
                        <div class="bg-black text-white px-3 py-1.5 font-black text-xs uppercase tracking-wider">
                            CATATAN KHUSUS
                        </div>
                        <div class="p-3 bg-white">
                            <textarea id="accept-modal-notes" 
                                      name="special_notes" 
                                      rows="3" 
                                      placeholder="[Tuliskan jika ada ekspektasi khusus dari customer, janji fitur khusus, atau kendala yang perlu diwaspadai tim delivery]" 
                                      class="w-full p-2 bg-slate-50 border border-slate-300 rounded text-xs text-slate-800 outline-none focus:bg-white focus:border-black transition leading-relaxed"></textarea>
                        </div>
                    </div>

                    {{-- 4. STATUS HANDOVER (Urutan Sesuai PDF: Bagian Bawah Hal 1) --}}
                    <div class="border border-black overflow-hidden">
                        <div class="bg-black text-white px-3 py-1.5 font-black text-xs uppercase tracking-wider">
                            STATUS HANDOVER
                        </div>
                        <table class="w-full border-collapse text-xs">
                            <tbody>
                                <tr class="border-b border-black bg-white">
                                    <td class="w-1/3 sm:w-1/4 py-2.5 px-3 font-bold border-r border-black align-middle">Status handover:</td>
                                    <td class="py-2.5 px-3">
                                        <div class="flex items-center gap-6 flex-wrap">
                                            <label class="inline-flex items-center gap-2 font-bold text-xs cursor-pointer select-none text-emerald-800">
                                                <input type="radio" name="handover_status_selected" value="approved" checked class="w-4 h-4 text-emerald-600 focus:ring-emerald-500">
                                                <span>Diterima</span>
                                            </label>
                                            <label class="inline-flex items-center gap-2 font-bold text-xs cursor-pointer select-none text-amber-800">
                                                <input type="radio" name="handover_status_selected" value="conditional" class="w-4 h-4 text-amber-600 focus:ring-amber-500">
                                                <span>Diterima bersyarat</span>
                                            </label>
                                            <label class="inline-flex items-center gap-2 font-bold text-xs cursor-pointer select-none text-rose-800">
                                                <input type="radio" name="handover_status_selected" value="rejected" class="w-4 h-4 text-rose-600 focus:ring-rose-500">
                                                <span>Ditolak / dikembalikan</span>
                                            </label>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="border-b border-black">
                                    <td class="py-2 px-3 font-bold bg-white border-r border-black">Kekurangan / risiko yang masih terbuka</td>
                                    <td class="p-1.5">
                                        <input type="text" id="accept-modal-risks" name="handover_risks" placeholder="Catatan kekurangan atau risiko yang terbuka..." class="w-full px-2.5 py-1 bg-white border border-slate-300 rounded text-xs text-slate-800 outline-none focus:border-black">
                                    </td>
                                </tr>
                                <tr class="border-b border-black">
                                    <td class="py-2 px-3 font-bold bg-white border-r border-black">Informasi yang belum tersedia</td>
                                    <td class="p-1.5">
                                        <input type="text" id="accept-modal-missing-info" name="handover_missing_info" placeholder="Informasi / dokumen yang belum lengkap..." class="w-full px-2.5 py-1 bg-white border border-slate-300 rounded text-xs text-slate-800 outline-none focus:border-black">
                                    </td>
                                </tr>
                                <tr class="border-b border-black">
                                    <td class="py-2 px-3 font-bold bg-white border-r border-black">PIC dan target penyelesaian</td>
                                    <td class="p-1.5">
                                        <input type="text" id="accept-modal-pic-target" name="handover_pic_target" placeholder="PIC penanggung jawab & target tanggal penyelesaian..." class="w-full px-2.5 py-1 bg-white border border-slate-300 rounded text-xs text-slate-800 outline-none focus:border-black">
                                    </td>
                                </tr>
                                <tr class="border-b border-black">
                                    <td class="py-2 px-3 font-bold bg-white border-r border-black">Konfirmasi Commercial dan Managed Service</td>
                                    <td class="p-1.5">
                                        <input type="text" id="accept-modal-confirmation" name="handover_confirmation" placeholder="Konfirmasi kesepakatan antara Commercial & Managed Service..." class="w-full px-2.5 py-1 bg-white border border-slate-300 rounded text-xs text-slate-800 outline-none focus:border-black">
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-2 px-3 font-bold bg-white border-r border-black">Batasan pekerjaan yang boleh berjalan</td>
                                    <td class="p-1.5">
                                        <input type="text" id="accept-modal-work-limitations" name="handover_work_limitations" placeholder="Scope pekerjaan yang boleh berjalan..." class="w-full px-2.5 py-1 bg-white border border-slate-300 rounded text-xs text-slate-800 outline-none focus:border-black">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    {{-- Pemisah Halaman Khusus Print / Export PDF Saja (Di Tampilan Modal Tidak Kepotong) --}}
                    <div class="hidden print:block print-page-break pt-4 pb-2">
                        <div class="flex items-start justify-between gap-4 pb-4 border-b border-black">
                            <div class="shrink-0">
                                <img src="{{ asset('images/ipnet1.png') }}" onerror="this.src='/images/ipnet.png'" alt="Logo IP Network Solusindo" class="h-10 w-auto object-contain">
                            </div>
                            <div class="text-right shrink-0">
                                <div class="text-[#8F0A0D] font-black text-base uppercase tracking-tight leading-none">
                                    CHECKLIST SERAH TERIMA
                                </div>
                                <div class="text-black font-black text-xs uppercase tracking-normal mt-0.5">
                                    IPNET#1
                                </div>
                                <div class="text-[10px] text-slate-700 italic font-medium mt-0.5">
                                    Hal 2
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ATURAN STATUS Persis PDF --}}
                    <div class="space-y-1.5">
                        <h3 class="font-black text-xs uppercase text-[#0F172A] tracking-wider">ATURAN STATUS</h3>
                        <div class="text-xs text-slate-800 leading-relaxed border border-black p-3.5 bg-white">
                            <p>
                                <strong class="font-bold text-black">Diterima:</strong> informasi minimum cukup untuk memulai perencanaan. 
                                <strong class="font-bold text-black ml-2">Diterima bersyarat:</strong> kekurangan tercatat, ada PIC dan target penyelesaian, dan pekerjaan yang boleh berjalan dibatasi secara eksplisit. 
                                <strong class="font-bold text-black ml-2">Ditolak/dikembalikan:</strong> scope, kontrak, atau persyaratan penting belum jelas; jangan memulai pekerjaan terdampak
                            </p>
                        </div>
                    </div>

                    {{-- Tabel Tanda Tangan / Persetujuan Persis 4 Kolom PDF --}}
                    <div class="border border-black overflow-hidden">
                        <table class="w-full border-collapse text-xs">
                            <thead>
                                <tr class="bg-white border-b border-black text-center font-bold">
                                    <th class="py-2.5 px-3 border-r border-black w-1/4 align-top">
                                        <div class="text-[11px] text-slate-600 font-medium">Diserahkan oleh</div>
                                        <div class="font-black text-slate-900 mt-0.5">Sales / Commercial</div>
                                    </th>
                                    <th class="py-2.5 px-3 border-r border-black w-1/4 align-top">
                                        <div class="text-[11px] text-slate-600 font-medium">Diperiksa / diterima oleh</div>
                                        <div class="font-black text-slate-900 mt-0.5">Managed Service</div>
                                    </th>
                                    <th class="py-2.5 px-3 border-r border-black w-1/4 align-top">
                                        <div class="text-[11px] text-slate-600 font-medium">Validasi teknis oleh</div>
                                        <div class="font-black text-slate-900 mt-0.5">TL / Solution</div>
                                        <div class="font-normal text-[10px] text-slate-500">(bila perlu)</div>
                                    </th>
                                    <th class="py-2.5 px-3 w-1/4 align-top">
                                        <div class="text-[11px] text-slate-600 font-medium">Persetujuan / eskalasi</div>
                                        <div class="font-black text-slate-900 mt-0.5">pihak berwenang</div>
                                        <div class="font-normal text-[10px] text-slate-500">(bila perlu)</div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="h-40">
                                    {{-- 1. Sales / Commercial --}}
                                    <td class="p-3 border-r border-black align-top flex-col justify-between">
                                        <div class="space-y-1">
                                            <p><strong class="font-bold">Nama:</strong> <span id="accept-modal-sales-sign" class="font-bold text-slate-900">—</span></p>
                                            <p><strong class="font-bold">Tanggal:</strong> {{ date('d/m/Y') }}</p>
                                        </div>
                                        <div class="pt-6">
                                            <p class="font-bold">Tanda tangan:</p>
                                            <div class="mt-2 text-center">
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                    <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                    <span>Verified Digital</span>
                                                </span>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- 2. Managed Service --}}
                                    <td class="p-3 border-r border-black align-top flex-col justify-between">
                                        <div class="space-y-1">
                                            <p><strong class="font-bold">Nama:</strong> <span id="accept-modal-pm-sign" class="font-bold text-[#8F0A0D]">{{ $leadMsName ?? 'Doris' }}</span></p>
                                            <p><strong class="font-bold">Tanggal:</strong> {{ date('d/m/Y') }}</p>
                                        </div>
                                        <div class="pt-6">
                                            <p class="font-bold">Tanda tangan:</p>
                                            <div class="mt-2 text-center">
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                                    <span>MS Verified</span>
                                                </span>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- 3. TL / Solution --}}
                                    <td class="p-3 border-r border-black align-top flex-col justify-between">
                                        <div class="space-y-1">
                                            <p><strong class="font-bold">Nama:</strong> Solution Lead</p>
                                            <p><strong class="font-bold">Tanggal:</strong> {{ date('d/m/Y') }}</p>
                                        </div>
                                        <div class="pt-6">
                                            <p class="font-bold">Tanda tangan:</p>
                                            <div class="mt-4 border-b border-dashed border-slate-300 w-3/4 mx-auto"></div>
                                        </div>
                                    </td>

                                    {{-- 4. Persetujuan Pihak Berwenang --}}
                                    <td class="p-3 align-top flex-col justify-between">
                                        <div class="space-y-1">
                                            <p><strong class="font-bold">Nama:</strong> Operations Head</p>
                                            <p><strong class="font-bold">Tanggal:</strong> {{ date('d/m/Y') }}</p>
                                        </div>
                                        <div class="pt-6">
                                            <p class="font-bold">Tanda tangan:</p>
                                            <div class="mt-4 border-b border-dashed border-slate-300 w-3/4 mx-auto"></div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    {{-- Catatan Pengendalian Persis PDF --}}
                    <p class="text-[11px] text-slate-800 leading-relaxed">
                        <strong class="font-bold text-black">Catatan pengendalian:</strong> handover tidak mengubah kewenangan formal atau tanggung jawab atas output yang telah dibuat. Perubahan scope/komitmen pelanggan harus melalui mekanisme persetujuan yang berlaku.
                    </p>

                    

                </div>

                {{-- Parameter SLA & Operasional Managed Service disimpan sebagai hidden input agar data submit tetap lengkap sesuai backend --}}
                <input type="hidden" id="accept-modal-sla-tier" name="sla_tier" value="Gold">
                <input type="hidden" id="accept-modal-sla-coverage" name="sla_coverage_hours" value="24x7">
                <input type="hidden" id="accept-modal-maint-freq" name="maintenance_frequency" value="Monthly">
                <input type="hidden" id="accept-modal-start-date" name="service_start_date" value="">
                <input type="hidden" id="accept-modal-end-date" name="service_end_date" value="">

            </div>
{{-- Footer Action Bar Sesuai Template Lebar --}}
            <div class="px-6 py-4 border-t border-[#E2E8F0] bg-white flex items-center justify-between shrink-0 no-print">
                <button type="button" 
                        onclick="window.closeAcceptModal()" 
                        class="px-4 py-2 border border-[#CBD5E1] rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-50 transition cursor-pointer">
                    Batal
                </button>

                <div class="flex items-center gap-2">
                    <button type="button" 
                            onclick="window.print()" 
                            class="px-4 py-2 border border-slate-300 rounded-xl text-xs font-bold text-slate-700 hover:bg-slate-50 transition cursor-pointer flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Cetak Dokumen</span>
                    </button>

                    <button type="submit" 
                            class="px-5 py-2 text-xs font-bold text-white bg-[#8F0A0D] hover:bg-[#73080A] rounded-xl shadow-xs transition cursor-pointer flex items-center gap-1.5 active:scale-95">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        <span>Konfirmasi &amp; Aktifkan Layanan</span>
                    </button>
                </div>
            </div>

        </form>

    </div>
</div>

{{-- Standalone Modal Controller Script --}}
<script>
    window.triggerAcceptModal = function(btn) {
        if (!btn) return;
        var docs = {};
        var hData = {};
        var clData = {};
        try { docs = JSON.parse(btn.getAttribute('data-docs') || '{}'); } catch(e) { docs = {}; }
        try { hData = JSON.parse(btn.getAttribute('data-handover-data') || '{}'); } catch(e) { hData = {}; }
        try { clData = JSON.parse(btn.getAttribute('data-checklist') || '{}'); } catch(e) { clData = {}; }

        var p = {
            id: btn.getAttribute('data-id'),
            name: btn.getAttribute('data-name'),
            client: btn.getAttribute('data-client'),
            so: btn.getAttribute('data-so'),
            sales: btn.getAttribute('data-sales'),
            pm: btn.getAttribute('data-pm'),
            leadMs: btn.getAttribute('data-lead-ms') || '{{ $leadMsName ?? "Doris" }}',
            contract_value_formatted: btn.getAttribute('data-value'),
            sla_tier: btn.getAttribute('data-tier'),
            sla_coverage_hours: btn.getAttribute('data-coverage'),
            maintenance_frequency: btn.getAttribute('data-freq'),
            service_start_date: btn.getAttribute('data-start'),
            service_end_date: btn.getAttribute('data-end'),
            start_display: btn.getAttribute('data-start-display'),
            end_display: btn.getAttribute('data-end-display'),
            special_notes: btn.getAttribute('data-notes'),
            docs: docs,
            handover_data: hData,
            checklist: clData
        };
        window.openAcceptModal(p);
    };

    window.openAcceptModal = function(p) {
        if (!p) return;
        var modal = document.getElementById('modal-accept-handover');
        if (!modal) return;
        
        var form = document.getElementById('form-accept-handover');
        if (form) {
            form.action = '/managed-service/handovers/' + p.id + '/accept';
        }
        
        // Referensi Kontrak
        var headerTitleEl = document.getElementById('accept-modal-header-title');
        if (headerTitleEl) headerTitleEl.textContent = 'Aktivasi: ' + (p.name || 'Serah Terima Layanan');
        var projNameEl = document.getElementById('accept-modal-project-name');
        if (projNameEl) projNameEl.textContent = p.name || '—';
        var clientEl = document.getElementById('accept-modal-client');
        if (clientEl) clientEl.textContent = p.client || '—';
        var soEl = document.getElementById('accept-modal-so');
        if (soEl) soEl.textContent = p.so || '—';
        var contractNumEl = document.getElementById('accept-modal-contract-num');
        if (contractNumEl) contractNumEl.textContent = p.so || ('KTR/IPNET/2026/' + String(p.id).padStart(4, '0'));
        var valEl = document.getElementById('accept-modal-contract-value');
        if (valEl) valEl.textContent = 'Nilai: ' + (p.contract_value_formatted || '—');
        var salesEl = document.getElementById('accept-modal-sales');
        if (salesEl) salesEl.textContent = p.sales || '—';
        var pmEl = document.getElementById('accept-modal-pm');
        if (pmEl) pmEl.textContent = p.leadMs || '{{ $leadMsName ?? "Doris" }}';
        
        // Tanggal Efektif & Masa Kontrak Rapi & Valid (Bahasa Indonesia & durasi 1 tahun penuh)
        var startFormatted = p.start_display || (p.service_start_date ? p.service_start_date.split('T')[0] : '');
        var endFormatted = p.end_display || (p.service_end_date ? p.service_end_date.split('T')[0] : '');

        // Proteksi jika start dan end sama atau tidak valid: otomatis hitung 1 tahun kontrak
        if (!endFormatted || endFormatted === startFormatted || (p.service_start_date && p.service_end_date && p.service_start_date >= p.service_end_date)) {
            if (p.service_start_date) {
                var sDate = new Date(p.service_start_date);
                if (!isNaN(sDate.getTime())) {
                    var eDate = new Date(sDate);
                    eDate.setFullYear(eDate.getFullYear() + 1);
                    eDate.setDate(eDate.getDate() - 1);
                    var idMonths = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
                    endFormatted = eDate.getDate() + ' ' + idMonths[eDate.getMonth()] + ' ' + eDate.getFullYear();
                    p.service_end_date = eDate.toISOString().split('T')[0];
                    p.end_display = endFormatted;
                }
            }
        }

        var datesEl = document.getElementById('accept-modal-dates');
        if (datesEl) {
            if (startFormatted && endFormatted && startFormatted !== endFormatted) {
                datesEl.textContent = startFormatted + ' s/d ' + endFormatted + ' (1 Tahun Kontrak)';
            } else if (startFormatted) {
                datesEl.textContent = startFormatted + ' (1 Tahun Kontrak)';
            } else {
                datesEl.textContent = '1 Tahun Masa Kontrak Maintenance';
            }
        }

        var endTextEl = document.getElementById('accept-modal-end-text');
        if (endTextEl) {
            endTextEl.textContent = endFormatted || (startFormatted ? '1 Tahun sejak tanggal mulai' : 'Sesuai masa kontrak SPK');
        }
        
        // Tanda tangan info
        var salesSignEl = document.getElementById('accept-modal-sales-sign');
        if (salesSignEl) salesSignEl.textContent = p.sales || '—';
        var pmSignEl = document.getElementById('accept-modal-pm-sign');
        if (pmSignEl) pmSignEl.textContent = p.leadMs || '{{ $leadMsName ?? "Doris" }}';

        // SLA Inputs
        var slaTierEl = document.getElementById('accept-modal-sla-tier');
        if (slaTierEl) slaTierEl.value = p.sla_tier || 'Gold';
        var slaCoverageEl = document.getElementById('accept-modal-sla-coverage');
        if (slaCoverageEl) slaCoverageEl.value = p.sla_coverage_hours || '24x7';
        var maintFreqEl = document.getElementById('accept-modal-maint-freq');
        if (maintFreqEl) maintFreqEl.value = p.maintenance_frequency || 'Monthly';
        var startDateEl = document.getElementById('accept-modal-start-date');
        if (startDateEl) startDateEl.value = p.service_start_date ? p.service_start_date.split('T')[0] : '{{ date('Y-m-d') }}';
        var endDateEl = document.getElementById('accept-modal-end-date');
        if (endDateEl) endDateEl.value = p.service_end_date ? p.service_end_date.split('T')[0] : '';
        var notesEl = document.getElementById('accept-modal-notes');
        if (notesEl) notesEl.value = p.special_notes || '';

        // Handover Risks & Info
        var hData = p.handover_data || {};
        var risksEl = document.getElementById('accept-modal-risks');
        if (risksEl) risksEl.value = hData.handover_risks || '';
        var missingEl = document.getElementById('accept-modal-missing-info');
        if (missingEl) missingEl.value = hData.handover_missing_info || '';
        var picTargetEl = document.getElementById('accept-modal-pic-target');
        if (picTargetEl) picTargetEl.value = hData.handover_pic_target || '';
        var limitEl = document.getElementById('accept-modal-work-limitations');
        if (limitEl) limitEl.value = hData.handover_work_limitations || '';
        var confirmEl = document.getElementById('accept-modal-confirmation');
        if (confirmEl) confirmEl.value = hData.handover_confirmation || '';

        // Status handover radio button
        var statusVal = hData.handover_status_selected || 'approved';
        var radio = document.querySelector('input[name="handover_status_selected"][value="' + statusVal + '"]');
        if (radio) radio.checked = true;

        // Helper untuk sinkronisasi Status Isian & N/A
        window.handleStatusChange = function(key, val) {
            var naInput = document.getElementById('accept-na-' + key);
            var notesInput = document.getElementById('accept-notes-' + key);
            if (!notesInput) return;
            if (val === 'Tidak Berlaku') {
                if (naInput) naInput.value = '1';
                notesInput.placeholder = 'Keterangan alasan tidak berlaku...';
                notesInput.classList.add('bg-amber-50/60', 'border-amber-300');
            } else {
                if (naInput) naInput.value = '0';
                notesInput.placeholder = 'Catatan / nomor dokumen...';
                notesInput.classList.remove('bg-amber-50/60', 'border-amber-300');
            }
        };

        // Render 8 Butir Checklist Sesuai Dokumen Resmi PDF (4 Kolom Sesuai PDF: NO. | INFORMASI | STATUS / ISIAN | CATATAN)
        var tbody = document.getElementById('accept-modal-checklist-tbody');
        if (tbody) {
            tbody.innerHTML = '';
            var officialItems = [
                { number: 1, key: 'ruang_lingkup', name: 'Ruang lingkup maintenance dan pengecualian', doc_key: 'tor', default_note: 'Dokumen TOR / SOW & Klausul Pengecualian' },
                { number: 2, key: 'daftar_aset', name: 'Daftar aset / perangkat / lokasi yang dicakup', doc_key: 'boq', default_note: 'Dokumen BOQ / Inventory Perangkat & Lokasi' },
                { number: 3, key: 'jadwal_pm', name: 'Jadwal dan kategori preventive maintenance', doc_key: 'pm_schedule', default_note: 'Jadwal kunjungan pemeliharaan berkala' },
                { number: 4, key: 'mekanisme_cm', name: 'Mekanisme corrective maintenance dan ticketing', doc_key: 'cm_ticketing', default_note: 'Alur ticketing & eskalasi penanganan kendala' },
                { number: 5, key: 'sla_layanan', name: 'SLA / jam layanan dan jalur kontak sesuai kontrak', doc_key: 'contract', default_note: 'Kontrak / SPK / Matriks SLA & Kontak Darurat' },
                { number: 6, key: 'dokumen_teknis', name: 'Dokumen teknis / as-built yang tersedia', doc_key: 'project_doc', default_note: 'Diagram topologi, IP addressing & konfigurasi teknis' },
                { number: 7, key: 'kondisi_awal', name: 'Kondisi awal dan masalah yang sudah diketahui', doc_key: 'baseline_doc', default_note: 'Laporan baseline perangkat eksisting & kendala awal' },
                { number: 8, key: 'pelaporan_berkala', name: 'Ketentuan pelaporan berkala', doc_key: 'ba', default_note: 'Berita Acara (BAST / BAP) & milestone penagihan' }
            ];

            var docs = p.docs || {};
            var savedStatuses = hData.checklist_status || {};
            var savedNas = hData.checklist_na || {};
            var savedNotes = hData.checklist_notes || {};


            officialItems.forEach(function(item) {
                var doc = docs[item.doc_key] || {};
                var hasFile = !!doc.file_path;
                var fileUrl = doc.file_url || (hasFile ? ('/storage/' + doc.file_path) : '');
                var isNa = !!savedNas[item.key];
                var rawStatus = savedStatuses[item.key];
                var currentStatusVal = isNa ? 'Tidak Berlaku' : (rawStatus || 'Ada');
                if (currentStatusVal === 'Tidak') currentStatusVal = 'Tidak Berlaku';
                var noteText = savedNotes[item.key] || (doc.number ? ('No: ' + doc.number) : '');

                var fileSection = hasFile ? `
                    <a href="${fileUrl}" target="_blank" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded bg-red-50 text-[#8F0A0D] hover:bg-red-100 text-[11px] font-bold border border-red-200 shadow-2xs transition active:scale-95">
                        <svg class="w-3.5 h-3.5 text-[#8F0A0D]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <span>Lihat File</span>
                    </a>
                ` : `
                    <span class="inline-block px-2 py-0.5 rounded text-[10.5px] font-medium text-slate-400 bg-slate-50 border border-slate-200">
                        Belum Ada File
                    </span>
                `;

                var tr = document.createElement('tr');
                tr.className = 'border-b border-black hover:bg-slate-50/50 transition';

                tr.innerHTML = `
                    <td class="py-2.5 px-2 text-center font-black text-slate-900 align-middle bg-white border-r border-black">
                        ${item.number}
                    </td>
                    <td class="py-2.5 px-3 align-middle border-r border-black">
                        <div class="font-black text-slate-900 text-xs leading-snug">
                            ${item.name}
                        </div>
                        <div class="text-[10.5px] text-slate-500 mt-0.5 leading-snug">
                            ${item.default_note}
                        </div>
                    </td>
                    <td class="py-2.5 px-2 text-center align-middle whitespace-nowrap border-r border-black">
                        <select id="accept-status-${item.key}" 
                                name="checklist_status[${item.key}]" 
                                onchange="window.handleStatusChange('${item.key}', this.value)"
                                class="w-full text-xs font-semibold px-2 py-1.5 border border-slate-300 rounded bg-white text-slate-900 outline-none focus:border-black ${currentStatusVal === 'Tidak Berlaku' ? 'bg-amber-50/60 text-amber-900 font-bold border-amber-300' : ''}">
                            <option value="Ada" ${currentStatusVal === 'Ada' ? 'selected' : ''}>Ada</option>
                            <option value="Tidak Berlaku" ${currentStatusVal === 'Tidak Berlaku' ? 'selected' : ''}>Tidak Berlaku</option>
                            <option value="Lengkap" ${currentStatusVal === 'Lengkap' ? 'selected' : ''}>Lengkap</option>
                            <option value="Tidak Ada" ${currentStatusVal === 'Tidak Ada' ? 'selected' : ''}>Tidak Ada</option>
                        </select>
                        <input type="hidden" id="accept-na-${item.key}" name="checklist_na[${item.key}]" value="${isNa ? '1' : '0'}">
                    </td>
                    <td class="py-2.5 px-3 align-middle">
                        <input type="text" 
                                id="accept-notes-${item.key}" 
                                name="checklist_notes[${item.key}]" 
                                value="${noteText}" 
                                placeholder="${currentStatusVal === 'Tidak Berlaku' ? 'Keterangan alasan tidak berlaku...' : 'Catatan / nomor dokumen...'}" 
                                class="w-full text-xs px-2.5 py-1.5 border border-slate-300 rounded outline-none focus:border-black bg-white text-slate-800 ${currentStatusVal === 'Tidak Berlaku' ? 'border-amber-300 bg-amber-50/40 text-amber-900 font-medium' : ''}">
                    </td>
                `;

                tbody.appendChild(tr);
            });
        }

        modal.style.display = 'flex';
    };

    window.closeAcceptModal = function() {
        var modal = document.getElementById('modal-accept-handover');
        if (modal) {
            modal.style.display = 'none';
        }
    };

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            window.closeAcceptModal();
        }
    });
</script>

<style>
@media print {
    body * {
        visibility: hidden !important;
    }
    #printable-accept-modal, #printable-accept-modal * {
        visibility: visible !important;
    }
    #printable-accept-modal {
        position: absolute !important;
        left: 0 !important;
        top: 0 !important;
        width: 100% !important;
        max-width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
        border: none !important;
        box-shadow: none !important;
        background: white !important;
        overflow: visible !important;
    }
    .print-page-break, .page-break {
        page-break-before: always !important;
        break-before: page !important;
    }
    .no-print {
        display: none !important;
    }
}
</style>
@endsection
