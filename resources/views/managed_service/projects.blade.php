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
                                {{ ($pendingHandoversCount ?? 0) > 0 ? $pendingHandoversCount : ($incomingHandovers->count() ?? 0) }}
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
                                    <th class="py-3 px-4 text-[11px] font-semibold text-[#64748B] uppercase tracking-wider">Progress</th>
                                    <th class="py-3 px-4 text-[11px] font-semibold text-[#64748B] uppercase tracking-wider">Status</th>
                                    <th class="py-3 px-4 text-[11px] font-semibold text-[#64748B] uppercase tracking-wider text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#F1F5F9]">
                                @forelse($projects as $p)
                                    @php
                                        $pmCyclesCount = $p->pmCycles->count() ?: ($p->pm_total_cycles ?: 4);
                                        $pmCompletedCount = $p->pmCycles->where('activity_status', 'Completed')->count();
                                        $progressPct = (int) ($p->progress ?: ($pmCyclesCount > 0 ? round(($pmCompletedCount / $pmCyclesCount) * 100) : 0));
                                        
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

                                        {{-- Progress --}}
                                        <td class="py-3 px-4 whitespace-nowrap">
                                            <div class="w-28">
                                                <div class="w-full bg-[#F1F5F9] border border-[#E2E8F0] rounded-full h-2 overflow-hidden mb-1">
                                                    <div class="h-full rounded-full transition-all duration-300"
                                                         style="background: linear-gradient(90deg, #8F0A0D, #D62E3C); width: {{ min(100, $progressPct) }}%;"></div>
                                                </div>
                                                <div class="flex items-center justify-between text-[10.5px]">
                                                    <span class="font-medium text-[#1E293B]">{{ $progressPct }}%</span>
                                                    <span class="text-[#94A3B8]">{{ $progressPct >= 100 ? 'Selesai' : 'Progres' }}</span>
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
                                        $startDateVal = $ho->service_start_date ? \Carbon\Carbon::parse($ho->service_start_date)->format('Y-m-d') : date('Y-m-d');
                                        $endDateVal = $ho->service_end_date ? \Carbon\Carbon::parse($ho->service_end_date)->format('Y-m-d') : '';
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
                                                            data-notes="{{ $ho->special_notes ?: '' }}"
                                                            data-so="{{ $ho->so_code ?: ($ho->po_spk_number ?: ('KTR/' . $ho->id)) }}"
                                                            data-sales="{{ $ho->sales_name ?: ($ho->creator?->name ?: '-') }}"
                                                            data-pm="{{ is_object($ho->pm) ? ($ho->pm->name ?? 'Tim Managed Service') : ($ho->pm ?: 'Tim Managed Service') }}"
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
                                                            data-notes="{{ $ho->special_notes ?: '' }}"
                                                            data-so="{{ $ho->so_code ?: ($ho->po_spk_number ?: ('KTR/' . $ho->id)) }}"
                                                            data-sales="{{ $ho->sales_name ?: ($ho->creator?->name ?: '-') }}"
                                                            data-pm="{{ is_object($ho->pm) ? ($ho->pm->name ?? 'Tim Managed Service') : ($ho->pm ?: 'Tim Managed Service') }}"
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
                                                <span class="font-medium">Belum ada serah terima kontrak masuk.</span>
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

            {{-- Background Container Abu-Abu Lembut Persis Gambar 2 --}}
            <div class="flex-1 overflow-y-auto px-4 pt-3.5 pb-6 sm:px-6 sm:pt-4 sm:pb-6 bg-[#F1F5F9]/70">
                
                {{-- Lembar Putih Border-2 Persis Gambar 2 --}}
                <div class="bg-white border-2 border-[#CBD5E1] rounded-xl p-5 sm:p-7 shadow-sm space-y-5 text-[#0F172A] font-sans">
                    
                    {{-- Kop Dokumen Resmi Persis Gambar 2 (Logo, Judul, Red Line, Badge DOKUMEN RESMI) --}}
                    <div class="border-b-2 border-[#8F0A0D] pb-3 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <img src="{{ asset('images/ipnet1.png') }}" onerror="this.src='/images/ipnet.png'" alt="Logo IPNET" class="h-10 w-auto object-contain shrink-0">
                            <div>
                                <h2 class="text-[15px] sm:text-[17px] font-black text-[#0F172A] uppercase tracking-wide leading-tight">
                                    FORMULIR CHECK LIST SERAH TERIMA (INTERNAL HANDOVER)
                                </h2>
                                <p class="text-[11px] text-[#475569] mt-0.5">
                                    Checklist Serah Terima Sales / Commercial ke Managed Service untuk Kontrak Maintenance &bull; PT IP Network Solusindo
                                </p>
                            </div>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="px-3 py-1 bg-red-50 text-[#8F0A0D] border border-red-200 rounded-lg text-xs font-bold uppercase tracking-wider">
                                Dokumen Resmi
                            </span>
                        </div>
                    </div>

                    {{-- Petunjuk Pengisian Singkat --}}
                    <div class="p-2.5 bg-[#F8FAFC] border border-[#CBD5E1] rounded-lg text-[11px] text-[#475569] leading-snug">
                        <strong class="text-[#0F172A]">Petunjuk:</strong> Isi kolom yang relevan. Jika suatu butir tidak berlaku, pilih "Tidak Berlaku" dan berikan catatan bila diperlukan. Simpan formulir beserta bukti pendukung pada lokasi dokumen yang ditetapkan.
                    </div>

                    {{-- A. REFERENSI KONTRAK --}}
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">A. REFERENSI KONTRAK</h3>
                            <span id="accept-modal-so" class="text-[11px] font-mono font-bold text-[#8F0A0D]">—</span>
                        </div>
                        <div class="border border-[#1E293B] rounded-sm overflow-hidden text-xs">
                            <table class="w-full border-collapse border border-[#1E293B]">
                                <tbody>
                                    <tr class="border-b border-[#1E293B]">
                                        <td class="w-1/5 py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Nama Layanan / Proyek</td>
                                        <td class="w-3/10 py-1.5 px-2.5 border-r border-[#1E293B] font-black text-[#8F0A0D]" id="accept-modal-project-name">—</td>
                                        <td class="w-1/5 py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Nomor Kontrak / PO</td>
                                        <td class="w-3/10 py-1.5 px-2.5 font-mono font-bold" id="accept-modal-contract-num">—</td>
                                    </tr>
                                    <tr class="border-b border-[#1E293B]">
                                        <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Pelanggan</td>
                                        <td class="py-1.5 px-2.5 border-r border-[#1E293B] font-bold text-[#0F172A]" id="accept-modal-client">—</td>
                                        <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Nilai Kontrak</td>
                                        <td class="py-1.5 px-2.5 font-bold text-[#8F0A0D]" id="accept-modal-contract-value">—</td>
                                    </tr>
                                    <tr class="border-b border-[#1E293B]">
                                        <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Tgl Efektif &amp; Masa Kontrak</td>
                                        <td class="py-1.5 px-2.5 border-r border-[#1E293B]" id="accept-modal-dates">—</td>
                                        <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Tanggal Berakhir</td>
                                        <td class="py-1.5 px-2.5 font-semibold" id="accept-modal-end-text">—</td>
                                    </tr>
                                    <tr>
                                        <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">PIC Commercial</td>
                                        <td class="py-1.5 px-2.5 border-r border-[#1E293B] font-bold text-[#0F172A]" id="accept-modal-sales">—</td>
                                        <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">PIC Managed Service</td>
                                        <td class="py-1.5 px-2.5 font-bold text-blue-900" id="accept-modal-pm">Tim Managed Service</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- B. CHECKLIST --}}
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">B. CHECKLIST SERAH TERIMA</h3>
                            <span class="text-[10.5px] text-[#475569] font-medium">8 Butir Standar Serah Terima Maintenance</span>
                        </div>
                        <div class="border border-[#1E293B] rounded-sm overflow-hidden text-xs">
                            <table class="w-full border-collapse border border-[#1E293B] text-left">
                                <thead>
                                    <tr class="bg-[#F8FAFC] text-[#0F172A] text-[11px] font-black uppercase tracking-wider border-b border-[#1E293B]">
                                        <th class="py-2 px-2.5 w-10 text-center border-r border-[#1E293B]">No.</th>
                                        <th class="py-2 px-3 w-64 sm:w-72 border-r border-[#1E293B]">Butir / Informasi</th>
                                        <th class="py-2 px-2.5 w-32 text-center border-r border-[#1E293B]">Status / Isian</th>
                                        <th class="py-2 px-3">Catatan / PIC &bull; Berkas &amp; Verifikasi</th>
                                    </tr>
                                </thead>
                                <tbody id="accept-modal-checklist-tbody" class="bg-white">
                                    {{-- Dirender oleh JS --}}
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- C. STATUS HANDOVER --}}
                    <div class="space-y-1.5">
                        <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">C. STATUS HANDOVER</h3>
                        <div class="border border-[#1E293B] rounded-sm overflow-hidden text-xs">
                            <div class="flex items-center gap-6 flex-wrap bg-[#F8FAFC] p-2.5 border-b border-[#1E293B]">
                                <span class="font-black text-[#0F172A] text-xs uppercase tracking-wide">Status Handover:</span>
                                <label class="inline-flex items-center gap-1.5 font-bold text-xs cursor-pointer select-none text-emerald-700">
                                    <input type="radio" name="handover_status_selected" value="approved" checked class="w-4 h-4 text-emerald-600 focus:ring-emerald-500">
                                    <span>Diterima</span>
                                </label>
                                <label class="inline-flex items-center gap-1.5 font-bold text-xs cursor-pointer select-none text-amber-700">
                                    <input type="radio" name="handover_status_selected" value="conditional" class="w-4 h-4 text-amber-600 focus:ring-amber-500">
                                    <span>Diterima bersyarat</span>
                                </label>
                                <label class="inline-flex items-center gap-1.5 font-bold text-xs cursor-pointer select-none text-rose-700">
                                    <input type="radio" name="handover_status_selected" value="rejected" class="w-4 h-4 text-rose-600 focus:ring-rose-500">
                                    <span>Ditolak / dikembalikan</span>
                                </label>
                            </div>
                            <table class="w-full border-collapse border-b border-[#1E293B]">
                                <tbody>
                                    <tr class="border-b border-[#1E293B]">
                                        <td class="w-1/3 py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Kekurangan / risiko yang masih terbuka</td>
                                        <td class="p-1.5">
                                            <input type="text" id="accept-modal-risks" name="handover_risks" placeholder="Catatan risiko atau kekurangan berkas..." class="w-full px-2.5 py-1 bg-white border border-slate-300 rounded text-xs text-slate-800 outline-none focus:border-[#8F0A0D]">
                                        </td>
                                    </tr>
                                    <tr class="border-b border-[#1E293B]">
                                        <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Informasi yang belum tersedia</td>
                                        <td class="p-1.5">
                                            <input type="text" id="accept-modal-missing-info" name="handover_missing_info" placeholder="Informasi / dokumen yang masih ditunggu dari Sales..." class="w-full px-2.5 py-1 bg-white border border-slate-300 rounded text-xs text-slate-800 outline-none focus:border-[#8F0A0D]">
                                        </td>
                                    </tr>
                                    <tr class="border-b border-[#1E293B]">
                                        <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">PIC dan target penyelesaian</td>
                                        <td class="p-1.5">
                                            <input type="text" id="accept-modal-pic-target" name="handover_pic_target" placeholder="Nama PIC Sales & target tanggal revisi..." class="w-full px-2.5 py-1 bg-white border border-slate-300 rounded text-xs text-slate-800 outline-none focus:border-[#8F0A0D]">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Batasan pekerjaan yang boleh berjalan</td>
                                        <td class="p-1.5">
                                            <input type="text" id="accept-modal-work-limitations" name="handover_work_limitations" placeholder="Pekerjaan teknis yang diizinkan berjalan sebelum berkas lengkap..." class="w-full px-2.5 py-1 bg-white border border-slate-300 rounded text-xs text-slate-800 outline-none focus:border-[#8F0A0D]">
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="p-2.5 bg-[#F8FAFC] text-[10.5px] text-[#475569] leading-snug">
                                <strong class="text-[#0F172A] uppercase tracking-wider block mb-0.5">ATURAN STATUS:</strong>
                                <p><strong>Diterima:</strong> informasi minimum cukup untuk memulai perencanaan. | <strong>Diterima bersyarat:</strong> kekurangan tercatat, ada PIC dan target penyelesaian, pekerjaan dibatasi eksplisit. | <strong>Ditolak:</strong> scope/kontrak belum jelas; jangan memulai pekerjaan.</p>
                            </div>
                        </div>
                    </div>

                    {{-- D. CATATAN KHUSUS --}}
                    <div class="space-y-1.5">
                        <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">D. CATATAN KHUSUS</h3>
                        <div class="border border-[#1E293B] rounded-sm overflow-hidden text-xs p-2.5 bg-white">
                            <textarea id="accept-modal-notes" name="special_notes" rows="2" placeholder="[Tuliskan jika ada ekspektasi khusus dari customer, janji fitur khusus, atau kendala yang perlu diwaspadai tim managed service]" class="w-full px-3 py-1.5 bg-[#F8FAFC] border border-slate-300 rounded-lg text-xs text-slate-800 outline-none focus:bg-white focus:border-[#8F0A0D]"></textarea>
                        </div>
                    </div>

                    {{-- E. PARAMETER SLA & OPERASIONAL MANAGED SERVICE --}}
                    <div class="space-y-1.5">
                        <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">E. PARAMETER SLA &amp; OPERASIONAL MANAGED SERVICE</h3>
                        <div class="border border-[#1E293B] rounded-sm overflow-hidden text-xs p-3.5 bg-white space-y-3">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                <div>
                                    <label class="block font-bold text-slate-700 uppercase tracking-wider text-[11px] mb-1">
                                        Tier Komitmen SLA <span class="text-red-500">*</span>
                                    </label>
                                    <select id="accept-modal-sla-tier" name="sla_tier" required
                                            class="w-full px-3 py-2 bg-[#F8FAFC] border border-slate-300 rounded-xl text-xs font-bold text-slate-800 focus:bg-white focus:outline-none focus:border-[#8F0A0D]">
                                        <option value="Platinum">Platinum (Response 15m, MTTR 2h)</option>
                                        <option value="Gold">Gold (Response 30m, MTTR 4h)</option>
                                        <option value="Silver">Silver (Response 1h, MTTR 8h)</option>
                                        <option value="Bronze">Bronze (Best Effort / 24h)</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block font-bold text-slate-700 uppercase tracking-wider text-[11px] mb-1">
                                        Jam Dukungan (Coverage Hours)
                                    </label>
                                    <select id="accept-modal-sla-coverage" name="sla_coverage_hours"
                                            class="w-full px-3 py-2 bg-[#F8FAFC] border border-slate-300 rounded-xl text-xs font-bold text-slate-800 focus:bg-white focus:outline-none focus:border-[#8F0A0D]">
                                        <option value="24x7">24x7 (24 Jam Termasuk Libur)</option>
                                        <option value="8x5">8x5 (Jam Kerja Senin-Jumat 08:00 - 17:00)</option>
                                        <option value="12x7">12x7 (08:00 - 20:00 Setiap Hari)</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                                <div>
                                    <label class="block font-bold text-slate-700 uppercase tracking-wider text-[11px] mb-1">
                                        Frekuensi Kunjungan PM
                                    </label>
                                    <select id="accept-modal-maint-freq" name="maintenance_frequency"
                                            class="w-full px-3 py-2 bg-[#F8FAFC] border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 focus:bg-white focus:outline-none focus:border-[#8F0A0D]">
                                        <option value="Monthly">Bulanan (Monthly)</option>
                                        <option value="Quarterly">Triwulanan (3 Bulan)</option>
                                        <option value="Bi-Annual">Semesteran (6 Bulan)</option>
                                        <option value="Annual">Tahunan</option>
                                        <option value="On-Demand">On-Demand</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block font-bold text-slate-700 uppercase tracking-wider text-[11px] mb-1">
                                        Mulai Tanggal Layanan
                                    </label>
                                    <input type="date" id="accept-modal-start-date" name="service_start_date"
                                           class="w-full px-3 py-2 bg-[#F8FAFC] border border-slate-300 rounded-xl text-xs font-medium text-slate-800 focus:bg-white focus:outline-none focus:border-[#8F0A0D]">
                                </div>
                                <div>
                                    <label class="block font-bold text-slate-700 uppercase tracking-wider text-[11px] mb-1">
                                        Akhir Kontrak Layanan
                                    </label>
                                    <input type="date" id="accept-modal-end-date" name="service_end_date"
                                           class="w-full px-3 py-2 bg-[#F8FAFC] border border-slate-300 rounded-xl text-xs font-medium text-slate-800 focus:bg-white focus:outline-none focus:border-[#8F0A0D]">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- F. KONFIRMASI DAN PERSETUJUAN --}}
                    <div class="space-y-1.5">
                        <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">F. KONFIRMASI DAN PERSETUJUAN</h3>
                        <div class="border border-[#1E293B] rounded-sm overflow-hidden text-xs p-3.5 bg-white space-y-3">
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 text-center">
                                {{-- 1. Sales --}}
                                <div class="border border-[#CBD5E1] rounded-lg p-2.5 bg-[#F8FAFC] flex flex-col justify-between h-36">
                                    <div>
                                        <div class="font-bold text-[10.5px] text-[#0F172A] uppercase">Diserahkan oleh</div>
                                        <div class="text-[10px] text-[#64748B]">Sales / Commercial</div>
                                    </div>
                                    <div class="my-auto py-1">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[9.5px] font-mono font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            <span>Verified Digital</span>
                                        </span>
                                    </div>
                                    <div class="text-left text-[10px] border-t border-[#CBD5E1] pt-1 text-slate-600">
                                        <p class="truncate"><strong class="text-slate-800">Nama:</strong> <span id="accept-modal-sales-sign">—</span></p>
                                        <p><strong class="text-slate-800">Tgl:</strong> {{ date('d/m/Y') }}</p>
                                    </div>
                                </div>

                                {{-- 2. Managed Service --}}
                                <div class="border border-[#CBD5E1] rounded-lg p-2.5 bg-[#F8FAFC] flex flex-col justify-between h-36">
                                    <div>
                                        <div class="font-bold text-[10.5px] text-[#0F172A] uppercase">Diperiksa / diterima oleh</div>
                                        <div class="text-[10px] text-[#64748B]">Managed Service</div>
                                    </div>
                                    <div class="my-auto py-1">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[9.5px] font-mono font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                            <span>MS Verified</span>
                                        </span>
                                    </div>
                                    <div class="text-left text-[10px] border-t border-[#CBD5E1] pt-1 text-slate-600">
                                        <p class="truncate"><strong class="text-slate-800">Nama:</strong> <span id="accept-modal-pm-sign">Tim Managed Service</span></p>
                                        <p><strong class="text-slate-800">Tgl:</strong> {{ date('d/m/Y') }}</p>
                                    </div>
                                </div>

                                {{-- 3. TL / Solution --}}
                                <div class="border border-[#CBD5E1] rounded-lg p-2.5 bg-[#F8FAFC] flex flex-col justify-between h-36">
                                    <div>
                                        <div class="font-bold text-[10.5px] text-[#0F172A] uppercase">Validasi teknis oleh</div>
                                        <div class="text-[10px] text-[#64748B]">TL / Solution (bila perlu)</div>
                                    </div>
                                    <div class="my-auto py-1">
                                        <span class="text-[10px] text-slate-400 italic">Paraf / Approval</span>
                                    </div>
                                    <div class="text-left text-[10px] border-t border-[#CBD5E1] pt-1 text-slate-600">
                                        <p class="truncate"><strong class="text-slate-800">Nama:</strong> Solution Lead</p>
                                        <p><strong class="text-slate-800">Tgl:</strong> {{ date('d/m/Y') }}</p>
                                    </div>
                                </div>

                                {{-- 4. Pihak Berwenang --}}
                                <div class="border border-[#CBD5E1] rounded-lg p-2.5 bg-[#F8FAFC] flex flex-col justify-between h-36">
                                    <div>
                                        <div class="font-bold text-[10.5px] text-[#0F172A] uppercase">Persetujuan / eskalasi</div>
                                        <div class="text-[10px] text-[#64748B]">Pihak berwenang (bila perlu)</div>
                                    </div>
                                    <div class="my-auto py-1">
                                        <span class="text-[10px] text-slate-400 italic">Otorisasi GM / Ops</span>
                                    </div>
                                    <div class="text-left text-[10px] border-t border-[#CBD5E1] pt-1 text-slate-600">
                                        <p class="truncate"><strong class="text-slate-800">Nama:</strong> Operations Head</p>
                                        <p><strong class="text-slate-800">Tgl:</strong> {{ date('d/m/Y') }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="p-2.5 bg-[#F8FAFC] border border-slate-200 rounded-lg text-[10px] text-slate-600 leading-relaxed italic">
                                <strong>Catatan pengendalian:</strong> handover tidak mengubah kewenangan formal atau tanggung jawab atas output yang telah dibuat. Perubahan scope/komitmen pelanggan harus melalui mekanisme persetujuan yang berlaku.
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Footer Persis Gambar 2 --}}
            <div class="px-6 py-4 border-t border-[#E2E8F0] bg-white flex items-center justify-between shrink-0 no-print">
                <button type="button" 
                        onclick="window.closeAcceptModal()" 
                        class="px-4 py-2 border border-[#CBD5E1] rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-50 transition cursor-pointer">
                    Batal
                </button>

                <button type="submit" 
                        class="px-5 py-2 text-xs font-bold text-white bg-[#8F0A0D] hover:bg-[#73080A] rounded-xl shadow-xs transition cursor-pointer flex items-center gap-1.5 active:scale-95">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    <span>Konfirmasi &amp; Aktifkan Layanan</span>
                </button>
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
            contract_value_formatted: btn.getAttribute('data-value'),
            sla_tier: btn.getAttribute('data-tier'),
            sla_coverage_hours: btn.getAttribute('data-coverage'),
            maintenance_frequency: btn.getAttribute('data-freq'),
            service_start_date: btn.getAttribute('data-start'),
            service_end_date: btn.getAttribute('data-end'),
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
        if (contractNumEl) contractNumEl.textContent = p.so || ('KTR/' + p.id);
        var valEl = document.getElementById('accept-modal-contract-value');
        if (valEl) valEl.textContent = 'Nilai: ' + (p.contract_value_formatted || '—');
        var salesEl = document.getElementById('accept-modal-sales');
        if (salesEl) salesEl.textContent = p.sales || '—';
        var pmEl = document.getElementById('accept-modal-pm');
        if (pmEl) pmEl.textContent = p.pm || 'Tim Managed Service';
        
        var datesEl = document.getElementById('accept-modal-dates');
        if (datesEl) datesEl.textContent = (p.service_start_date ? p.service_start_date.split('T')[0] : '—') + ' s/d ' + (p.service_end_date ? p.service_end_date.split('T')[0] : '—');
        var endTextEl = document.getElementById('accept-modal-end-text');
        if (endTextEl) endTextEl.textContent = p.service_end_date ? p.service_end_date.split('T')[0] : '—';
        
        // Tanda tangan info
        var salesSignEl = document.getElementById('accept-modal-sales-sign');
        if (salesSignEl) salesSignEl.textContent = p.sales || '—';
        var pmSignEl = document.getElementById('accept-modal-pm-sign');
        if (pmSignEl) pmSignEl.textContent = p.pm || 'Tim Managed Service';

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

        // Render 8 Butir Checklist Sesuai Dokumen Resmi PDF
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
            var savedCl = p.checklist || {};
            var savedStatuses = hData.checklist_status || {};
            var savedNotes = hData.checklist_notes || {};

            officialItems.forEach(function(item) {
                var doc = docs[item.doc_key] || {};
                var hasFile = !!doc.file_path;
                var fileUrl = doc.file_url || (hasFile ? ('/storage/' + doc.file_path) : '');
                var isVerified = (savedCl[item.key] !== undefined) ? !!savedCl[item.key] : (!!doc.verified || hasFile);
                var isChecked = isVerified ? 'checked' : '';
                var currentStatus = savedStatuses[item.key] || (isVerified ? 'Ada' : 'Ada');
                var noteText = savedNotes[item.key] || (doc.number ? ('No: ' + doc.number) : '');

                var tr = document.createElement('tr');
                tr.className = 'border-b border-[#1E293B] transition ' + (isVerified ? 'bg-emerald-50/20' : 'hover:bg-slate-50/50');

                var fileSection = hasFile ? `
                    <a href="${fileUrl}" target="_blank" class="px-2.5 py-1 rounded-lg bg-white hover:bg-slate-50 text-[#1E293B] text-[11px] font-bold border border-[#CBD5E1] shadow-2xs flex items-center gap-1.5 transition">
                        <svg class="w-3.5 h-3.5 text-[#64748B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <span>Lihat File</span>
                    </a>
                ` : `
                    <span class="px-2.5 py-1 rounded-lg bg-slate-50 text-[#94A3B8] text-[10.5px] font-medium border border-slate-200">
                        Belum Ada File
                    </span>
                `;

                tr.innerHTML = `
                    <td class="py-2.5 px-2 text-center font-bold text-slate-800 align-middle bg-[#F8FAFC] border-r border-[#1E293B]">
                        ${item.number}
                    </td>
                    <td class="py-2.5 px-3 align-middle border-r border-[#1E293B]">
                        <div class="font-bold text-slate-900 text-[11.5px] leading-tight">
                            ${item.name}
                        </div>
                        <div class="text-[10px] text-slate-500 mt-0.5 leading-snug">
                            ${item.default_note}
                        </div>
                    </td>
                    <td class="py-2.5 px-2 text-center align-middle whitespace-nowrap border-r border-[#1E293B]">
                        <select name="checklist_status[${item.key}]" class="text-[11px] font-bold px-2 py-1 bg-white border border-slate-300 rounded-md outline-none text-slate-800 focus:border-[#8F0A0D]">
                            <option value="Ada" ${currentStatus === 'Ada' ? 'selected' : ''}>Ada</option>
                            <option value="Tidak Berlaku" ${currentStatus === 'Tidak Berlaku' ? 'selected' : ''}>Tidak Berlaku</option>
                            <option value="Menyusul" ${currentStatus === 'Menyusul' ? 'selected' : ''}>Menyusul</option>
                        </select>
                    </td>
                    <td class="py-2.5 px-3 align-middle">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <div class="min-w-0 flex-1">
                                <input type="text" name="checklist_notes[${item.key}]" value="${noteText}" placeholder="Catatan / PIC / bukti..." class="w-full text-[11px] px-2.5 py-1 bg-slate-50 border border-slate-200 rounded-md text-slate-700 outline-none focus:bg-white focus:border-slate-400">
                            </div>
                            <div class="flex items-center gap-2 shrink-0 self-end sm:self-center">
                                ${fileSection}
                                <label class="flex items-center gap-1.5 px-2.5 py-1 rounded-lg border border-[#E2E8F0] bg-[#F8FAFC] hover:bg-[#FEF2F2] hover:border-[#FCA5A5] text-[#1E293B] text-[11px] font-bold cursor-pointer transition select-none">
                                    <input type="checkbox" name="documents_checklist[${item.key}]" value="1" ${isChecked} class="w-3.5 h-3.5 text-[#8F0A0D] rounded border-gray-300 focus:ring-[#8F0A0D] cursor-pointer">
                                    <span>Terverifikasi</span>
                                </label>
                            </div>
                        </div>
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
    .no-print {
        display: none !important;
    }
}
</style>
@endsection
