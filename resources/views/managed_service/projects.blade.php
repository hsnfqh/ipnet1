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
<div class="flex h-screen overflow-hidden bg-[#F8FAFC] font-sans">
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Daftar Proyek'])
        
        <div class="p-4 sm:p-6 lg:p-7 space-y-5 max-w-[1600px] mx-auto">

            <!-- ========================================================== -->
            <!-- 1. HEADER & FILTER CONTROLS (LEAD ENGINEER DESIGN PATTERN) -->
            <!-- ========================================================== -->
            <div class="ipnet-card p-5 sm:p-6 anim-fade-up anim-delay-1">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 pb-4 mb-4 border-b border-[#E2E8F0]">
                    <div>
                        <p class="text-[#8F0A0D] text-[12px] font-bold inline-flex items-center uppercase tracking-wider">
                            <span class="ipnet-badge-dot"></span> MANAJEMEN PORTOFOLIO
                        </p>
                        <h2 class="text-[20px] font-bold text-[#1E293B] tracking-tight">Daftar Proyek & Kontrak Service</h2>
                        <p class="text-[13px] text-[#64748B] mt-0.5">Kelola pelaksanaan proyek klien, dokumen legal (SPK, Kontrak, BOQ, BA), dan monitoring SLA</p>
                    </div>

                    <div class="flex items-center gap-2.5 shrink-0">
                        @php
                            $firstProj = $projects->first();
                        @endphp
                        @if($firstProj)
                            <a href="{{ route('ms.projects.show', ['project' => $firstProj->id, 'tab' => 'pm']) }}" class="btn-ipnet-gradient px-4 py-2 rounded-xl font-bold text-xs flex items-center gap-2 cursor-pointer shadow-md text-white">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                                <span>Monitoring PM / CM</span>
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Filter Controls -->
                <form method="GET" action="{{ route('ms.projects.index') }}" class="flex flex-col md:flex-row flex-wrap items-center justify-between gap-3">
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
                            <a href="{{ route('ms.projects.index') }}" 
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

            <!-- ========================================================== -->
            <!-- 2. PROJECTS TABLE CARD                                     -->
            <!-- ========================================================== -->
            <div class="ipnet-card overflow-hidden anim-fade-up anim-delay-3">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[900px] border-collapse text-[13px] text-left">
                        <thead>
                            <tr class="bg-[#F8FAFC] border-b border-[#E2E8F0]">
                                <th class="py-3 px-4 text-[11px] font-semibold text-[#64748B] uppercase tracking-wider">Nama Project & Tipe</th>
                                <th class="py-3 px-4 text-[11px] font-semibold text-[#64748B] uppercase tracking-wider">Client & Sales</th>
                                <th class="py-3 px-4 text-[11px] font-semibold text-[#64748B] uppercase tracking-wider">Lokasi</th>
                                <th class="py-3 px-4 text-[11px] font-semibold text-[#64748B] uppercase tracking-wider">Durasi & Deadline</th>
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
                                                @php
                                                    $tier = $p->sla_tier;
                                                    $tierClass = match($tier) {
                                                        'Platinum' => 'bg-purple-50 text-purple-700 border-purple-200',
                                                        'Gold'     => 'bg-amber-50 text-amber-700 border-amber-200',
                                                        'Silver'   => 'bg-slate-100 text-slate-700 border-slate-300',
                                                        default    => 'bg-orange-50 text-orange-700 border-orange-200',
                                                    };
                                                @endphp
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $tierClass }}">
                                                    Tier: {{ $tier }}
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
    </div>
</div>
@endsection
