@extends('layouts.app')

@section('title', 'Reporting Kinerja & SLA Layanan Proyek - PT IP Network Solusindo')

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
        animation: fadeUpStagger 0.4s cubic-bezier(0.16, 1, 0.3, 1) both;
    }
    .anim-delay-1 { animation-delay: 0.05s !important; }
    .anim-delay-2 { animation-delay: 0.1s !important; }
    .anim-delay-3 { animation-delay: 0.15s !important; }
</style>
@endpush

@section('content')
<div class="flex h-screen overflow-hidden bg-[#F8FAFC] font-sans" 
     x-data="{ 
         filterTier: '{{ request('tier', 'all') }}',
         perPage: '{{ request('per_page', $perPage ?? 10) }}'
     }">
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Reporting Kinerja & SLA Layanan'])
        
        <div class="p-4 sm:p-6 lg:p-7 space-y-6 max-w-[1680px] mx-auto anim-fade-up">

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between text-[13px] font-semibold shadow-xs">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-600 shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            <!-- ========================================================== -->
            <!-- 1. HEADER CARD                                             -->
            <!-- ========================================================== -->
            <div class="ipnet-card p-5 sm:p-6 anim-fade-up anim-delay-1">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <div>
                        <p class="text-[#8F0A0D] text-[12px] font-bold inline-flex items-center uppercase tracking-wider">
                            <span class="ipnet-badge-dot"></span> IP NETWORK SOLUSINDO • MANAGED SERVICE &amp; SLA
                        </p>
                        <h2 class="text-[20px] font-bold text-[#1E293B] tracking-tight mt-0.5">Reporting Kinerja &amp; Rekapitulasi Proyek</h2>
                        <p class="text-[13px] text-[#64748B] mt-0.5">Monitoring real-time siklus Preventive Maintenance (PM), pencapaian SLA operasional, serta tiket Corrective Maintenance (CM) aktif dan closed.</p>
                    </div>

                    <div class="flex items-center gap-3 shrink-0 self-start lg:self-center">
                        <a href="{{ route('ms.projects.index') }}" 
                           class="btn-ipnet-gradient px-4 py-2 text-xs font-bold text-white rounded-xl shadow-md transition flex items-center gap-2 cursor-pointer active:scale-95">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                            <span>Daftar Proyek</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- ========================================================== -->
            <!-- 2. FILTER CONTROLS                                         -->
            <!-- ========================================================== -->
            <div class="ipnet-card p-4 sm:p-5 anim-fade-up anim-delay-2">
                <form method="GET" action="{{ route('ms.reporting.index') }}" class="flex flex-col md:flex-row flex-wrap items-center justify-between gap-3">
                    <div class="flex flex-col sm:flex-row flex-wrap items-center gap-3 w-full md:w-auto flex-1">
                        {{-- Search Input --}}
                        <div class="relative flex-1 min-w-[260px] w-full sm:w-auto">
                            <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-[#94A3B8]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input type="text" 
                                   name="search"
                                   value="{{ request('search') }}"
                                   placeholder="Cari nama project, client, sales, atau lokasi..." 
                                   class="w-full pl-9 pr-3.5 py-2 rounded-xl border border-[#CBD5E1] text-[12.5px] font-semibold text-[#1E293B] bg-white outline-none hover:border-[#94A3B8] focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition-all shadow-xs placeholder-[#94A3B8]">
                        </div>

                        {{-- Filter Tier SLA --}}
                        <select name="tier" onchange="this.form.submit()" 
                                class="w-full sm:w-48 px-3 py-2 rounded-xl border border-[#CBD5E1] text-[12.5px] font-semibold text-[#1E293B] bg-white outline-none hover:border-[#94A3B8] focus:border-[#8F0A0D] cursor-pointer shadow-xs">
                            <option value="all">Semua Tier SLA</option>
                            <option value="Platinum" {{ request('tier') === 'Platinum' ? 'selected' : '' }}>Platinum Tier</option>
                            <option value="Gold" {{ request('tier') === 'Gold' ? 'selected' : '' }}>Gold Tier</option>
                            <option value="Silver" {{ request('tier') === 'Silver' ? 'selected' : '' }}>Silver Tier</option>
                            <option value="Bronze" {{ request('tier') === 'Bronze' ? 'selected' : '' }}>Bronze Tier</option>
                        </select>

                        {{-- Reset Filter Button --}}
                        @if(request('search') || (request('tier') && request('tier') !== 'all') || (request('per_page') && request('per_page') != 10))
                            <a href="{{ route('ms.reporting.index') }}" 
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
            <!-- 4. TABEL REPORTING KINERJA & SLA PROYEK                     -->
            <!-- ========================================================== -->
            <div class="ipnet-card overflow-hidden anim-fade-up anim-delay-3">
                @if($projects->isEmpty())
                    <div class="p-12 text-center space-y-3">
                        <div class="w-12 h-12 rounded-2xl bg-gray-50 border border-gray-200 flex items-center justify-center text-gray-400 mx-auto">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <h4 class="text-sm font-bold text-[#1E293B]">Tidak ada data proyek yang sesuai filter</h4>
                        <p class="text-xs text-[#64748B]">Silakan reset filter pencarian atau pastikan kontrak proyek berada pada status aktif.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-[#F8FAFC] border-b border-[#E2E8F0] text-[10.5px] font-extrabold tracking-wider text-[#64748B] uppercase">
                                    <th class="py-3.5 px-4">Nama Project &amp; Client</th>
                                    <th class="py-3.5 px-4">Siklus PM</th>
                                    <th class="py-3.5 px-4">SLA Project</th>
                                    <th class="py-3.5 px-4 text-center">Tiket Aktif</th>
                                    <th class="py-3.5 px-4 text-center">Tiket Closed</th>
                                    <th class="py-3.5 px-4 text-center">Kuota &amp; Sisa CM</th>
                                    <th class="py-3.5 px-4 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#F1F5F9]">
                                @foreach($projects as $p)
                                    @php
                                        // 1. Perhitungan Siklus PM
                                        $cycles = $p->pmCycles ?: collect();
                                        $totalCycles = (int) ($p->pm_total_cycles ?: max($cycles->count(), 1));
                                        $completedCycles = $cycles->filter(function($c) {
                                            $act = strtolower($c->activity_status ?? '');
                                            $rep = strtolower($c->report_status ?? '');
                                            return in_array($act, ['completed', 'solved']) 
                                                || in_array($rep, ['approved', 'submitted', 'signed', 'invoiced']) 
                                                || !empty($c->actual_date);
                                        })->count();
                                        $pmPercent = $totalCycles > 0 ? round(($completedCycles / $totalCycles) * 100) : 0;
                                        
                                        // Siklus berikutnya
                                        $nextCycle = $cycles->where('activity_status', 'Scheduled')->where('scheduled_date', '>=', now()->toDateString())->sortBy('scheduled_date')->first();
                                        $lastCompleted = $cycles->filter(fn($c) => !empty($c->actual_date) || strtolower($c->activity_status ?? '') === 'completed')->sortByDesc('actual_date')->first();

                                        // 2. Perhitungan Tiket CM
                                        $tickets = $p->managedServiceTickets ?: collect();
                                        $cmQuota = (int) ($p->cm_quota ?: 18);
                                        $totalTickets = $tickets->count();
                                        
                                        $activeTickets = $tickets->filter(function($t) {
                                            $st = strtolower(trim((string)$t->status));
                                            $isResolved = in_array($st, ['resolved', 'closed', 'solved', 'completed', 'submitted', 'selesai', 'done', 'approved']) || !empty($t->resolved_at);
                                            return !$isResolved;
                                        });
                                        $activeCount = $activeTickets->count();

                                        $closedTickets = $tickets->filter(function($t) {
                                            $st = strtolower(trim((string)$t->status));
                                            return in_array($st, ['resolved', 'closed', 'solved', 'completed', 'submitted', 'selesai', 'done', 'approved']) || !empty($t->resolved_at);
                                        });
                                        $closedCount = $closedTickets->count();
                                        $remainingQuota = max(0, $cmQuota - $totalTickets);

                                        // 3. Perhitungan SLA Project
                                        $targetSla = (float) ($p->sla_target_percentage ?: 99.50);
                                        $slaMetTicketsCount = $closedTickets->filter(fn($t) => $t->sla_met || $t->sla_met === null)->count();
                                        $slaCompliancePercent = $closedCount > 0 ? round(($slaMetTicketsCount / $closedCount) * 100, 1) : 100.0;
                                        $isSlaMet = ($slaCompliancePercent >= 90.0);

                                        $tierColor = match(strtolower($p->sla_tier ?? 'gold')) {
                                            'platinum' => 'bg-purple-50 text-purple-700 border-purple-200',
                                            'gold'     => 'bg-amber-50 text-amber-800 border-amber-200',
                                            'silver'   => 'bg-slate-100 text-slate-700 border-slate-200',
                                            default    => 'bg-blue-50 text-blue-700 border-blue-200',
                                        };
                                    @endphp
                                    <tr class="hover:bg-[#F8FAFC]/80 transition">
                                        {{-- 1. Nama Project & Client --}}
                                        <td class="py-3.5 px-4">
                                            <div class="max-w-[340px]">
                                                <a href="{{ route('ms.projects.show', $p->id) }}" class="font-bold text-[#1E293B] text-[13px] hover:text-[#8F0A0D] transition line-clamp-1" title="{{ $p->name }}">
                                                    {{ $p->name }}
                                                </a>
                                                <div class="text-[11.5px] text-[#64748B] mt-0.5 font-medium line-clamp-1">
                                                    {{ $p->client ?: '-' }}
                                                    @if($p->sales_name)
                                                        <span class="text-slate-400 font-normal">• Sales: {{ $p->sales_name }}</span>
                                                    @endif
                                                </div>
                                                <div class="flex items-center gap-1.5 mt-1.5">
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9.5px] font-bold border {{ $tierColor }}">
                                                        Tier: {{ $p->sla_tier ?: 'Gold' }}
                                                    </span>
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9.5px] font-semibold bg-slate-50 text-slate-600 border border-slate-200">
                                                        {{ $p->pm_frequency_label ?: '1 Tahun' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </td>

                                        {{-- 2. Siklus PM --}}
                                        <td class="py-3.5 px-4 whitespace-nowrap">
                                            <div class="space-y-1.5">
                                                <div class="flex items-center justify-between text-xs">
                                                    <span class="font-bold text-[#1E293B]">{{ $completedCycles }} / {{ $totalCycles }} Siklus</span>
                                                    <span class="font-extrabold text-[11px] {{ $pmPercent >= 80 ? 'text-emerald-700' : 'text-[#8F0A0D]' }}">{{ $pmPercent }}%</span>
                                                </div>
                                                <div class="w-36 bg-slate-100 rounded-full h-1.5 overflow-hidden border border-slate-200">
                                                    <div class="h-1.5 rounded-full transition-all {{ $pmPercent >= 80 ? 'bg-emerald-600' : 'bg-[#8F0A0D]' }}" style="width: {{ $pmPercent }}%"></div>
                                                </div>
                                                <p class="text-[10px] text-slate-400">
                                                    @if($nextCycle)
                                                        Next: <span class="text-slate-600 font-semibold">{{ \Carbon\Carbon::parse($nextCycle->scheduled_date)->format('d M Y') }}</span>
                                                    @elseif($lastCompleted)
                                                        Terakhir: <span class="text-emerald-600 font-semibold">{{ \Carbon\Carbon::parse($lastCompleted->actual_date ?: $lastCompleted->scheduled_date)->format('d M Y') }}</span>
                                                    @else
                                                        Terjadwal rutin
                                                    @endif
                                                </p>
                                            </div>
                                        </td>

                                        {{-- 3. SLA Project --}}
                                        <td class="py-3.5 px-4 whitespace-nowrap">
                                            <div class="space-y-1">
                                                <div class="flex items-center gap-1.5">
                                                    <span class="text-xs font-bold text-[#1E293B]">Target: {{ number_format($targetSla, 2) }}%</span>
                                                </div>
                                                <div>
                                                    @if($isSlaMet)
                                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 shadow-2xs">
                                                            <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                            <span>SLA Terpenuhi ({{ $slaCompliancePercent }}%)</span>
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200 shadow-2xs">
                                                            <svg class="w-3 h-3 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                                            <span>Perhatian SLA ({{ $slaCompliancePercent }}%)</span>
                                                        </span>
                                                    @endif
                                                </div>
                                                <p class="text-[10px] text-slate-400">MTTR: {{ $p->mttr_hours ?: 4 }} Jam • {{ $p->sla_coverage_hours ?: '24x7' }}</p>
                                            </div>
                                        </td>

                                        {{-- 4. Tiket Aktif (CM) --}}
                                        <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                            @if($activeCount > 0)
                                                <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200 shadow-2xs">
                                                    {{ $activeCount }} Tiket Aktif
                                                </span>
                                            @else
                                                <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-slate-50 text-slate-500 border border-slate-200">
                                                    0 Aktif (Aman)
                                                </span>
                                            @endif
                                        </td>

                                        {{-- 5. Tiket Closed (CM) --}}
                                        <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                            <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 shadow-2xs">
                                                {{ $closedCount }} Tiket Closed
                                            </span>
                                        </td>

                                        {{-- 6. Kuota & Sisa CM --}}
                                        <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                            <div class="space-y-0.5">
                                                <span class="font-bold text-[#1E293B] text-xs">{{ $totalTickets }} / {{ $cmQuota >= 999 ? '∞' : $cmQuota }}</span>
                                                <p class="text-[10.5px] font-semibold {{ $remainingQuota <= 2 ? 'text-red-600' : 'text-slate-500' }}">
                                                    Sisa: {{ $cmQuota >= 999 ? '∞' : $remainingQuota }} Tiket
                                                </p>
                                            </div>
                                        </td>

                                        {{-- 7. Aksi --}}
                                        <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                            <div class="flex items-center justify-center gap-1.5">
                                                <a href="{{ route('ms.projects.show', ['project' => $p->id, 'tab' => 'sla']) }}" 
                                                   class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-white border border-[#CBD5E1] hover:border-[#8F0A0D] text-[#1E293B] hover:text-[#8F0A0D] text-[11px] font-bold transition shadow-2xs cursor-pointer"
                                                   title="Buka Detail SLA &amp; Laporan Proyek">
                                                    <svg class="w-3.5 h-3.5 text-slate-500 group-hover:text-[#8F0A0D]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                    <span>Lihat Detail</span>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination Controls --}}
                    @if($projects->hasPages())
                        <div class="p-4 border-t border-[#E2E8F0] bg-[#F8FAFC] flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
                            <span class="text-slate-500">
                                Menampilkan {{ $projects->firstItem() }} s/d {{ $projects->lastItem() }} dari {{ $projects->total() }} proyek
                            </span>
                            <div>
                                {{ $projects->links() }}
                            </div>
                        </div>
                    @endif
                @endif
            </div>

        </div>
    </div>
</div>
@endsection
