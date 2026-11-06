@extends('layouts.app')

@section('title', 'Detail Proyek & SLA - ' . $project->client)

@push('styles')
<style>
    [x-cloak] { display: none !important; }

    .ipnet-card {
        background-color: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04), 0 4px 12px rgba(0, 0, 0, 0.02);
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .ipnet-card:hover {
        border-color: #CBD5E1;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
    }
    .ipnet-badge-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background-color: #8F0A0D;
        display: inline-block;
        margin-right: 8px;
    }
    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(12px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .anim-fade-up {
        animation: fadeUp 0.35s cubic-bezier(0.16, 1, 0.3, 1) both;
    }
</style>
@endpush

@section('content')
<div class="flex h-screen overflow-hidden bg-[#F8FAFC] font-sans" 
     x-data="{ 
        activeTab: '{{ ($activeTab ?? 'documents') === 'sla' ? 'documents' : ($activeTab ?? 'documents') }}',
        isDocModalOpen: false, 
        selectedDocType: 'spk', 
        selectedDocTitle: '', 
        isSlaModalOpen: false,
        isPmModalOpen: false,
        activeCycle: null,
        isSavingPm: false,
        isSavingCm: false,
        isSavingSla: false,
        selectedEngineers: [],
        engineerDropdownOpen: false,
        allEngineerMap: {{ json_encode($allEngineers->pluck('name', 'id')) }},
        pmFile1: null,
        pmFile2: null,
        handlePmFile(event, slot) {
            const file = event.target.files && event.target.files[0];
            if (!file) return;
            if (file.size > 52428800) {
                alert('Ukuran berkas ' + file.name + ' melebihi batas 50MB. Silakan pilih berkas lain.');
                event.target.value = '';
                if (slot === 1) this.pmFile1 = null;
                if (slot === 2) this.pmFile2 = null;
                return;
            }
            const info = {
                name: file.name,
                size: file.size < 1048576 ? (file.size / 1024).toFixed(1) + ' KB' : (file.size / 1048576).toFixed(1) + ' MB'
            };
            if (slot === 1) this.pmFile1 = info;
            if (slot === 2) this.pmFile2 = info;
        },
        clearPmFile(slot) {
            if (slot === 1) {
                if (this.$refs.pmFileInput1) this.$refs.pmFileInput1.value = '';
                this.pmFile1 = null;
            } else if (slot === 2) {
                if (this.$refs.pmFileInput2) this.$refs.pmFileInput2.value = '';
                this.pmFile2 = null;
            }
        },
        openPmModal(cycle) {
            this.activeCycle = cycle;
            this.selectedEngineers = [];
            if (cycle && cycle.assigned_engineers && Array.isArray(cycle.assigned_engineers)) {
                const picMsId = cycle.pic_ms_id ? Number(cycle.pic_ms_id) : null;
                const leadEngId = cycle.lead_engineer_id ? Number(cycle.lead_engineer_id) : null;
                this.selectedEngineers = cycle.assigned_engineers
                    .map(e => typeof e === 'object' ? Number(e.id) : Number(e))
                    .filter(id => id !== picMsId && id !== leadEngId);
            }
            this.engineerDropdownOpen = false;
            this.isSavingPm = false;
            this.pmFile1 = null;
            this.pmFile2 = null;
            if (this.$refs.pmFileInput1) this.$refs.pmFileInput1.value = '';
            if (this.$refs.pmFileInput2) this.$refs.pmFileInput2.value = '';
            this.isPmModalOpen = true;
        },
        isCmModalOpen: false,
        isCmDetailModalOpen: false,
        activeCmTicket: null,
        selectedCmSlot: null,
        isUploadBapModalOpen: false,
        selectedUploadTicket: { id: null, number: '' }
     }">
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Detail Proyek & Layanan SLA'])
        
        <div class="p-4 sm:p-6 lg:p-7 space-y-5 max-w-[1680px] mx-auto anim-fade-up">

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 font-bold px-2">✕</button>
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-[#8F0A0D] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700 font-bold px-2">✕</button>
                </div>
            @endif

            @if(isset($errors) && $errors->any())
                <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold space-y-1.5 shadow-xs">
                    <div class="flex items-center justify-between font-bold text-rose-900">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Terjadi kendala pada pengunggahan:</span>
                        </div>
                        <button type="button" onclick="this.parentElement.parentElement.remove()" class="text-rose-500 hover:text-rose-700 font-bold px-2 cursor-pointer">✕</button>
                    </div>
                    <ul class="list-disc list-inside pl-6 text-[11.5px] space-y-0.5 text-rose-700">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- ========================================================== -->
            <!-- 1. HEADER SECTION & QUICK PROJECT SWITCHER                 -->
            <!-- ========================================================== -->
            <div class="ipnet-card p-5 sm:p-6">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 pb-4 mb-4 border-b border-[#E2E8F0]">
                    <div>
                        <div class="flex items-center gap-2 text-[11.5px] font-semibold text-[#64748B] mb-1.5">
                            <a href="{{ route('ms.projects.index') }}" class="hover:text-[#8F0A0D] transition flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                                <span>Daftar Proyek</span>
                            </a>
                            <span class="text-[#CBD5E1]">/</span>
                            <span class="text-[#8F0A0D] font-bold">Detail Layanan Proyek</span>
                        </div>
                        <div class="flex items-center gap-2.5 flex-wrap">
                            <h1 class="text-[20px] font-bold text-[#1E293B] tracking-tight">{{ $project->client }}</h1>
                            {{-- [HIDDEN] Project Type, Tier & Status Badges --}}@if(false)<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10.5px] font-bold bg-rose-50 text-[#8F0A0D] border border-rose-200">
                                {{ $project->project_type ?: 'Managed Service SLA' }}
                            </span>
                            @if($project->sla_tier)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10.5px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                    Tier: {{ $project->sla_tier }}
                                </span>
                            @endif
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10.5px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                {{ $project->status ?: 'Active' }}
                            </span>@endif
                        </div>
                        <p class="text-[12.5px] text-[#64748B] mt-1">{{ $project->name }} &bull; Lokasi: {{ $project->location ?: 'On-Site Klien' }}</p>
                    </div>

                    <!-- Right Controls: Switch Project & Actions -->
                    <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                        {{-- [HIDDEN] Project Switcher Dropdown --}}@if(false){{-- Project Switcher Dropdown --}}
                        <div class="flex items-center gap-2">
                            <span class="text-[11.5px] font-semibold text-[#64748B] hidden sm:inline">Pilih Proyek:</span>
                            <select onchange="window.location.href = this.value" 
                                    class="px-3 py-2 bg-white border border-[#CBD5E1] rounded-xl text-xs font-semibold text-[#1E293B] outline-none hover:border-[#94A3B8] focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition shadow-2xs cursor-pointer max-w-[240px]">
                                @foreach($allProjects as $pOption)
                                    <option value="{{ route('ms.projects.show', ['project' => $pOption->id, 'tab' => $activeTab]) }}" {{ $pOption->id == $project->id ? 'selected' : '' }}>
                                        {{ $pOption->client }} - {{ Str::limit($pOption->name, 24) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>@endif

                        {{-- Ubah Parameter SLA (Hanya tombol ini di header agar rapi dan tidak dobel) --}}
                        <button type="button" @click="isSlaModalOpen = true" 
                                class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white hover:bg-[#F8FAFC] border border-[#CBD5E1] text-[#334155] text-xs font-bold rounded-xl shadow-2xs transition cursor-pointer">
                            <svg class="w-3.5 h-3.5 text-[#64748B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span>Edit Parameter SLA</span>
                        </button>




                    </div>
                </div>

                <!-- 4 SYMMETRICAL KPI CARDS (SALES DESIGN STANDARD) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
                    {{-- KPI 1: Periode Layanan --}}
                    <div class="p-3.5 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0] space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-bold text-[#64748B] uppercase tracking-wider">Periode Layanan</span>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">Aktif</span>
                        </div>
                        <p class="text-[14px] font-bold text-[#1E293B]">
                            @if($project->service_start_date && $project->service_end_date)
                                {{ \Carbon\Carbon::parse($project->service_start_date)->format('d M Y') }} — {{ \Carbon\Carbon::parse($project->service_end_date)->format('d M Y') }}
                            @else
                                1 Tahun Kontrak
                            @endif
                        </p>
                        <p class="text-[11.5px] text-[#64748B]">Durasi Kontrak Operasional</p>
                    </div>

                    {{-- KPI 2: Preventive Maintenance --}}
                    <div class="p-3.5 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0] space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-bold text-[#64748B] uppercase tracking-wider">Preventive Maint.</span>
                            <span class="text-[10.5px] font-bold px-2 py-0.5 rounded-full bg-rose-50 text-[#8F0A0D] border border-rose-200">
                                {{ $pmProgress }}% Progres
                            </span>
                        </div>
                        <p class="text-[15px] font-bold text-[#1E293B]">{{ $pmCompleted }} / {{ $pmTotal }} Visit Selesai</p>
                        <p class="text-[11.5px] text-[#64748B]">Siklus: {{ $project->pm_frequency_label }}</p>
                    </div>

                    {{-- KPI 3: Corrective Maintenance --}}
                    <div class="p-3.5 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0] space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-bold text-[#64748B] uppercase tracking-wider">Corrective Maint.</span>
                            <span class="text-[10.5px] font-bold px-2 py-0.5 rounded-full bg-rose-50 text-[#8F0A0D] border border-rose-200">
                                Sisa {{ $cmRemaining }} Tiket
                            </span>
                        </div>
                        <p class="text-[15px] font-bold text-[#8F0A0D]">{{ $cmUsed }} / {{ $cmQuota >= 999 ? '∞' : $cmQuota }} Tiket Terpakai</p>
                        <p class="text-[11.5px] text-[#64748B]">Target MTTR: {{ $project->mttr_hours ?: 4 }} Jam</p>
                    </div>

                    {{-- KPI 4: Komitmen SLA --}}
                    <div class="p-3.5 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0] space-y-1 cursor-pointer hover:border-[#8F0A0D]/60 hover:shadow-2xs transition group"
                         @click="activeTab = 'sla'; window.history.replaceState(null, '', '?tab=sla')"
                         title="Klik untuk membuka rincian perhitungan dan evaluasi SLA">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-bold text-[#64748B] uppercase tracking-wider group-hover:text-[#8F0A0D] transition-colors">Komitmen SLA</span>
                            <span class="text-[10.5px] font-bold px-2 py-0.5 rounded-full bg-rose-50 text-[#8F0A0D] border border-rose-200">
                                Tier: {{ $project->sla_tier ?: 'Gold' }}
                            </span>
                        </div>
                        <div class="flex items-baseline justify-between gap-1">
                            <p class="text-[15px] font-bold text-[#1E293B]">{{ $project->sla_target_percentage ?: '99.50' }}% Target</p>
                            <span class="text-[11px] font-bold {{ ($isSlaMet ?? true) ? 'text-emerald-700 bg-emerald-50 border border-emerald-200' : 'text-[#8F0A0D] bg-rose-50 border border-rose-200' }} px-1.5 py-0.5 rounded">
                                Realisasi: {{ number_format($projectSlaUptime ?? 99.8, 1) }}%
                            </span>
                        </div>
                        <p class="text-[11.5px] text-[#64748B] flex items-center justify-between">
                            <span>Support Standby {{ $project->sla_coverage_hours ?: '24x7' }}</span>
                            <span class="text-[10.5px] text-[#8F0A0D] font-bold group-hover:underline">Lihat Detail SLA &rarr;</span>
                        </p>
                    </div>
                </div>

                <!-- TEAM & ENGINEER DELEGATION INFO BANNER (FLOW MAINTENANCE & LEAD ENGINEER) -->
                @php
                    $allAssigned = $project->handover_data['assigned_engineers'] ?? [];
                    $delegatedBy = $project->handover_data['team_delegated_by_name'] ?? 'Lead Engineer';
                    if (stripos($delegatedBy, 'Maintenance') !== false) {
                        $delegatedBy = 'Lead Engineer';
                    }
                    $delegatedAt = $project->handover_data['team_delegated_at'] ?? null;
                    $delegationNotes = $project->handover_data['team_delegation_notes'] ?? null;
                    
                    // PIC Penanggung Jawab dari Tim Maintenance (Operasional & Helpdesk)
                    $picMs = $project->handover_data['pic_ms_name'] 
                        ?? ($project->handover_data['coordinator_name'] 
                        ?? ($project->msAcceptedBy?->name ?? 'Doris (Tim Maintenance)'));
                    
                    // Personil Lapangan yang ditugaskan & dibagi task-nya oleh Lead Engineer
                    $fieldEngineers = collect($allAssigned)->filter(function($e) use ($picMs) {
                        $name = strtolower($e['name'] ?? '');
                        $pic = strtolower($picMs);
                        return !str_contains($name, 'doris') && !str_contains($name, 'eris') && !str_contains($name, 'helpdesk') && $name !== $pic;
                    })->values()->all();

                    // Fallback jika belum ada teknisi selain PIC
                    if (empty($fieldEngineers)) {
                        $fieldEngineers = collect($allAssigned)->filter(fn($e) => strtolower($e['name'] ?? '') !== strtolower($picMs))->values()->all();
                    }
                    if (empty($fieldEngineers) && (stripos($project->client, 'Mandiri') !== false || stripos($project->name, 'Mandiri') !== false)) {
                        $fieldEngineers = [
                            ['name' => 'Mario', 'role' => 'Teknisi Lapangan', 'position' => 'Field Support']
                        ];
                    }
                @endphp

                {{-- [HIDDEN] PIC & Field Team Banner Card --}}@if(false)<div class="p-3.5 sm:p-4 rounded-xl bg-white border border-[#E2E8F0] shadow-2xs mt-3.5 flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-4">
                    
                    {{-- 1. SISI KIRI: PIC DARI TIM MAINTENANCE (Penanggung Jawab Layanan & Kontrak Aktif) --}}
                    <div class="flex items-center gap-3 pr-0 lg:pr-5 border-b lg:border-b-0 lg:border-r border-slate-200 pb-3 lg:pb-0 shrink-0 min-w-[260px]">
                        <div class="w-10 h-10 rounded-xl bg-red-50 text-[#8F0A0D] border border-red-200/80 flex items-center justify-center shrink-0 shadow-2xs">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <div class="flex items-center gap-1.5">
                                <span class="text-[10px] font-extrabold uppercase tracking-wider text-[#8F0A0D]">PIC Tim Maintenance</span>
                                <span class="text-[9.5px] font-bold px-1.5 py-0.2 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">Aktif</span>
                            </div>
                            <div class="text-[13px] font-extrabold text-[#1E293B] truncate mt-0.5">
                                {{ $picMs }}
                            </div>
                            <p class="text-[11px] text-[#64748B] truncate">Koordinator Layanan SLA &amp; Helpdesk</p>
                        </div>
                    </div>

                    {{-- 2. SISI KANAN: PERSONIL TIM LAPANGAN (Didelegasikan & Dibagi Task oleh Lead Engineer) --}}
                    <div class="flex-1 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 min-w-0">
                        <div class="min-w-0">
                            <div class="flex items-center gap-2 flex-wrap mb-1.5">
                                <span class="text-[10.5px] font-extrabold uppercase tracking-wider text-slate-700">Personil Tim Lapangan</span>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 border border-blue-200">
                                    Didelegasikan oleh {{ $delegatedBy }}
                                </span>
                                @if($delegatedAt)
                                    <span class="text-[10.5px] text-slate-400">({{ $delegatedAt }})</span>
                                @endif
                            </div>

                            @if(!empty($fieldEngineers) && count($fieldEngineers) > 0)
                                <div class="flex items-center gap-2 flex-wrap">
                                    @foreach($fieldEngineers as $idx => $eng)
                                        <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-50 border border-slate-200 shadow-2xs">
                                            <span class="w-2 h-2 rounded-full {{ $idx === 0 ? 'bg-emerald-500' : 'bg-blue-500' }}"></span>
                                            <span class="font-bold text-slate-800 text-xs">{{ $eng['name'] }}</span>
                                            <span class="text-[10.5px] text-slate-500 font-medium">({{ $eng['role'] ?? ($idx === 0 ? 'Teknisi Lapangan' : 'Support Lapangan') }})</span>
                                        </div>
                                    @endforeach
                                </div>
                                @if($delegationNotes)
                                    <p class="text-[11px] text-slate-600 mt-1 italic font-normal">
                                        <span class="font-semibold text-slate-700">Catatan Lead:</span> &ldquo;{{ $delegationNotes }}&rdquo;
                                    </p>
                                @endif
                            @else
                                <div class="flex items-center gap-2 text-xs text-amber-800 bg-amber-50 px-2.5 py-1 rounded-lg border border-amber-200">
                                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                                    <span class="font-medium">Jadwal layanan aktif. Menunggu Lead Engineer membagikan penugasan teknisi lapangan ke task proyek ini.</span>
                                </div>
                            @endif
                        </div>

                        <a href="{{ route('schedules.index') }}" class="shrink-0 px-3 py-1.5 rounded-lg text-xs font-bold text-slate-700 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 border border-slate-200 transition shadow-2xs flex items-center gap-1.5 cursor-pointer">
                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span>Jadwal Kerja Tim</span>
                        </a>
                    </div>

                </div>@endif
            </div>

            <!-- ========================================================== -->
            <!-- 2. CLEAN SALES-STYLE TABS NAVIGATION                       -->
            <!-- ========================================================== -->
            <div class="flex items-center gap-6 border-b border-[#E2E8F0] px-2 text-xs font-bold overflow-x-auto">
                <button type="button" 
                        @click="activeTab = 'documents'; window.history.replaceState(null, '', '?tab=documents')"
                        :class="activeTab === 'documents' ? 'border-[#8F0A0D] text-[#8F0A0D]' : 'border-transparent text-[#64748B] hover:text-[#1E293B]'"
                        class="pb-3 border-b-2 transition-all flex items-center gap-2 whitespace-nowrap cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Dokumen & Data Kontrak</span>
                    <span :class="activeTab === 'documents' ? 'bg-red-50 text-[#8F0A0D]' : 'bg-gray-100 text-[#64748B]'" class="text-[10.5px] font-bold px-1.5 py-0.5 rounded-full">
                        {{ $availableDocCount ?? count(array_filter($documents, fn($d) => !empty($d['file_path']))) }} Berkas
                    </span>
                </button>

                <button type="button" 
                        @click="activeTab = 'pm'; window.history.replaceState(null, '', '?tab=pm')"
                        :class="activeTab === 'pm' ? 'border-[#8F0A0D] text-[#8F0A0D]' : 'border-transparent text-[#64748B] hover:text-[#1E293B]'"
                        class="pb-3 border-b-2 transition-all flex items-center gap-2 whitespace-nowrap cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    <span>Preventive Maintenance (PM)</span>
                    <span :class="activeTab === 'pm' ? 'bg-red-50 text-[#8F0A0D]' : 'bg-gray-100 text-[#64748B]'" class="text-[10.5px] font-bold px-1.5 py-0.5 rounded-full">
                        {{ $pmCompleted }}/{{ $pmTotal }} Visit
                    </span>
                </button>

                <button type="button" 
                        @click="activeTab = 'cm'; window.history.replaceState(null, '', '?tab=cm')"
                        :class="activeTab === 'cm' ? 'border-[#8F0A0D] text-[#8F0A0D]' : 'border-transparent text-[#64748B] hover:text-[#1E293B]'"
                        class="pb-3 border-b-2 transition-all flex items-center gap-2 whitespace-nowrap cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    <span>Corrective Maintenance (CM)</span>
                    <span :class="activeTab === 'cm' ? 'bg-red-50 text-[#8F0A0D]' : 'bg-gray-100 text-[#64748B]'" class="text-[10.5px] font-bold px-1.5 py-0.5 rounded-full">
                        {{ $cmUsed }}/{{ $cmQuota >= 999 ? '∞' : $cmQuota }} Tiket
                    </span>
                </button>

                {{-- [HIDDEN] SLA Tab Button --}}@if(false)<button type="button" 
                        @click="activeTab = 'sla'; window.history.replaceState(null, '', '?tab=sla')"
                        :class="activeTab === 'sla' ? 'border-[#8F0A0D] text-[#8F0A0D]' : 'border-transparent text-[#64748B] hover:text-[#1E293B]'"
                        class="pb-3 border-b-2 transition-all flex items-center gap-2 whitespace-nowrap cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    <span>Perhitungan &amp; Evaluasi SLA</span>
                    <span :class="activeTab === 'sla' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-gray-100 text-[#64748B]'" class="text-[10.5px] font-bold px-2 py-0.5 rounded-full">
                        {{ number_format($projectSlaUptime ?? 99.8, 1) }}%
                    </span>
                </button>@endif


            </div>

            <!-- ========================================================== -->
            <!-- TAB 1: DOKUMEN & DATA KONTRAK                              -->
            <!-- ========================================================== -->
            <div x-show="activeTab === 'documents'" x-cloak class="grid grid-cols-1 lg:grid-cols-12 gap-5 anim-fade-up">
                
                <!-- KARTU KIRI: DATA CUSTOMER & PIC (5 COLS) -->
                <div class="lg:col-span-5 ipnet-card p-5 sm:p-6 space-y-4">
                    <div class="flex items-center justify-between border-b border-[#E2E8F0] pb-3">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-[#8F0A0D]"></span>
                            <h2 class="text-[13px] font-bold text-[#1E293B] uppercase tracking-wider">Data Customer & PIC</h2>
                        </div>
                        <span class="text-[10.5px] font-mono font-bold px-2 py-0.5 rounded bg-[#F1F5F9] text-[#475569] border border-[#E2E8F0]">
                            #PROJ-{{ $project->id }}
                        </span>
                    </div>

                    <div class="space-y-3.5">
                        <div>
                            <span class="text-[10.5px] font-bold text-[#64748B] uppercase tracking-wider block">Nama Customer / Perusahaan</span>
                            <p class="text-[15px] font-bold text-[#1E293B] mt-0.5">{{ $project->client }}</p>
                        </div>

                        <!-- Sales PIC & Pre-Sales PIC & PIC Managed Service -->
                        <div class="grid grid-cols-2 gap-3 pt-1">
                            <div class="bg-[#F8FAFC] p-3 rounded-xl border border-[#E2E8F0]">
                                <span class="text-[10.5px] font-bold text-[#64748B] uppercase tracking-wider block">Sales PIC</span>
                                <p class="text-[13px] font-bold text-[#1E293B] mt-0.5">{{ $project->sales_name ?: ($project->creator?->name ?: 'Raiza') }}</p>
                            </div>
                            <div class="bg-[#F8FAFC] p-3 rounded-xl border border-[#E2E8F0]">
                                <span class="text-[10.5px] font-bold text-[#64748B] uppercase tracking-wider block">Pre-Sales / SA</span>
                                <p class="text-[13px] font-bold text-[#1E293B] mt-0.5">{{ $project->presales_name ?: 'Akbar' }}</p>
                            </div>
                            {{-- [HIDDEN] PIC MS Contract Receiver Card --}}@if(false)<div class="bg-[#FFF5F5] p-3 rounded-xl border border-[#FECDD3] col-span-2">
                                <span class="text-[10.5px] font-bold text-[#8F0A0D] uppercase tracking-wider block">PIC Managed Service (Penerima Kontrak)</span>
                                <p class="text-[13px] font-bold text-[#8F0A0D] mt-0.5 flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-[#8F0A0D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    <span>{{ $project->msAcceptedBy?->name ?? ($project->handover_data['pic_ms_name'] ?? ($project->handover_data['lead_ms_name'] ?? 'Tim Managed Service')) }}</span>
                                </p>
                            </div>@endif
                        </div>

                        <!-- Customer Contacts & Lokasi -->
                        @php
                            $hd = is_array($project->handover_data) ? $project->handover_data : [];

                            // 1. Resolve Customer PIC Name
                            $customerPicName = null;
                            $candidateNames = [
                                $hd['customer_pic'] ?? null,
                                $project->customer_pic_name,
                                $project->clientRecord?->pic_name,
                                $project->customer_pic_technical,
                                $project->customer_pic_business,
                                $project->customer_pic_finance,
                            ];
                            foreach ($candidateNames as $cand) {
                                if (!empty($cand)) {
                                    $cTrim = trim($cand);
                                    // Make sure it is not an email and not digits only
                                    if (!str_contains($cTrim, '@') && !preg_match('/^[0-9+\-\s().]{7,25}$/', $cTrim)) {
                                        $customerPicName = $cTrim;
                                        break;
                                    }
                                }
                            }
                            if (empty($customerPicName)) {
                                $customerPicName = '—';
                            }

                            // 2. Resolve Customer Phone (Strictly Digits / Numeric Only)
                            $customerPhone = null;
                            $candidatePhones = [
                                $hd['customer_pic_phone'] ?? null,
                                $project->clientRecord?->phone,
                                $project->customer_pic_business,
                                $project->customer_pic_technical,
                                $project->customer_pic_finance,
                            ];
                            foreach ($candidatePhones as $pVal) {
                                if (!empty($pVal)) {
                                    $pTrim = trim($pVal);
                                    // Must NOT have letters (cannot be person name) and must match phone digits pattern
                                    if (!preg_match('/[a-zA-Z]/', $pTrim) && preg_match('/^[0-9+\-\s().]{7,25}$/', $pTrim)) {
                                        $customerPhone = $pTrim;
                                        break;
                                    }
                                }
                            }

                            // 3. Resolve Customer Email (Must contain @ and no spaces)
                            $customerEmail = null;
                            $candidateEmails = [
                                $hd['customer_pic_email'] ?? null,
                                $project->clientRecord?->email,
                                $project->customer_pic_finance,
                                $project->customer_pic_technical,
                                $project->customer_pic_business,
                            ];
                            foreach ($candidateEmails as $eVal) {
                                if (!empty($eVal)) {
                                    $eTrim = trim($eVal);
                                    if (str_contains($eTrim, '@') && !str_contains($eTrim, ' ')) {
                                        $customerEmail = $eTrim;
                                        break;
                                    }
                                }
                            }
                        @endphp

                        <div class="space-y-2.5 pt-3 border-t border-[#E2E8F0]">
                            <!-- Nama PIC Customer -->
                            <div class="p-3 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0] hover:border-[#CBD5E1] transition flex items-center gap-3.5">
                                <div class="w-8 h-8 rounded-lg bg-white border border-[#CBD5E1] text-[#64748B] flex items-center justify-center shrink-0 shadow-2xs">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <span class="text-[10px] text-[#64748B] font-bold uppercase tracking-wider block">Nama PIC Customer</span>
                                    <p class="font-bold text-[#1E293B] text-[12.5px] mt-0.5 truncate">{{ $customerPicName }}</p>
                                </div>
                            </div>

                            <!-- No. Telepon / WhatsApp -->
                            <div class="p-3 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0] hover:border-[#CBD5E1] transition flex items-center gap-3.5">
                                <div class="w-8 h-8 rounded-lg bg-white border border-[#CBD5E1] text-[#64748B] flex items-center justify-center shrink-0 shadow-2xs">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <span class="text-[10px] text-[#64748B] font-bold uppercase tracking-wider block">No. Telepon / WhatsApp</span>
                                    @if($customerPhone && $customerPhone !== '-')
                                        <a href="tel:{{ preg_replace('/[^0-9+]/', '', $customerPhone) }}" class="font-bold text-[#1E293B] hover:text-[#8F0A0D] text-[12.5px] mt-0.5 block truncate transition">
                                            {{ $customerPhone }}
                                        </a>
                                    @else
                                        <p class="font-bold text-[#1E293B] text-[12.5px] mt-0.5 truncate">—</p>
                                    @endif
                                </div>
                            </div>

                            <!-- Email PIC Customer -->
                            <div class="p-3 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0] hover:border-[#CBD5E1] transition flex items-center gap-3.5">
                                <div class="w-8 h-8 rounded-lg bg-white border border-[#CBD5E1] text-[#64748B] flex items-center justify-center shrink-0 shadow-2xs">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <span class="text-[10px] text-[#64748B] font-bold uppercase tracking-wider block">Email PIC Customer</span>
                                    @if($customerEmail && $customerEmail !== '-')
                                        <a href="mailto:{{ $customerEmail }}" class="font-bold text-[#1E293B] hover:text-[#8F0A0D] text-[12.5px] mt-0.5 block truncate transition">
                                            {{ $customerEmail }}
                                        </a>
                                    @else
                                        <p class="font-bold text-[#1E293B] text-[12.5px] mt-0.5 truncate">—</p>
                                    @endif
                                </div>
                            </div>

                            <!-- Lokasi Operasional / Site -->
                            <div class="p-3 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0] hover:border-[#CBD5E1] transition flex items-center gap-3.5">
                                <div class="w-8 h-8 rounded-lg bg-white border border-[#CBD5E1] text-[#64748B] flex items-center justify-center shrink-0 shadow-2xs">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <span class="text-[10px] text-[#64748B] font-bold uppercase tracking-wider block">Lokasi Operasional / Site</span>
                                    <p class="font-bold text-[#1E293B] text-[12.5px] mt-0.5">{{ $project->location ?: 'On-Site Klien (Data Center / Head Office)' }}</p>
                                </div>
                            </div>

                            <!-- Ruang Lingkup Pekerjaan -->
                            <div class="p-3 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0] hover:border-[#CBD5E1] transition flex items-start gap-3.5">
                                <div class="w-8 h-8 rounded-lg bg-white border border-[#CBD5E1] text-[#64748B] flex items-center justify-center shrink-0 shadow-2xs mt-0.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <span class="text-[10px] text-[#64748B] font-bold uppercase tracking-wider block">Ruang Lingkup Pekerjaan</span>
                                    <p class="font-bold text-[#1E293B] text-[12px] mt-0.5 whitespace-pre-line leading-relaxed">{{ $project->description ?: 'Pemeliharaan perangkat dan monitoring operasional SLA.' }}</p>
                                </div>
                            </div>

                            @if($project->contract_value)
                                <!-- Nilai Kontrak Layanan (Neutral uniform styling, no red box) -->
                                <div class="p-3 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0] hover:border-[#CBD5E1] transition flex items-center gap-3.5">
                                    <div class="w-8 h-8 rounded-lg bg-white border border-[#CBD5E1] text-[#64748B] flex items-center justify-center shrink-0 shadow-2xs">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <span class="text-[10px] text-[#64748B] font-bold uppercase tracking-wider block">Nilai Kontrak Layanan</span>
                                        <p class="font-bold text-[#1E293B] text-[13px] mt-0.5">Rp {{ number_format($project->contract_value, 0, ',', '.') }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- KARTU KANAN: 5 BERKAS DOKUMEN (7 COLS) -->
                <div class="lg:col-span-7 ipnet-card p-5 sm:p-6 space-y-4">
                    <div class="flex items-center justify-between border-b border-[#E2E8F0] pb-3">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-[#8F0A0D]"></span>
                            <h2 class="text-[13px] font-bold text-[#1E293B] uppercase tracking-wider">Berkas Dokumen Kontrak & Legal</h2>
                        </div>
                        <span class="text-[11px] font-semibold text-[#64748B]">Dokumen Legalitas & Kontrak dari Tim Sales</span>
                    </div>

                    <div class="space-y-3">
                        @php
                            $authUser = auth()->user();
                            $canUploadDoc = $authUser && ($authUser->hasRole(['Admin', 'Super Admin', 'PMO', 'Sales', 'Account Manager']) || $authUser->division_id == 1 || in_array(strtolower($authUser->position ?? ''), ['admin', 'sales', 'pmo', 'account manager']));
                        @endphp

                        @foreach($documents as $docKey => $doc)
                            @php
                                $hasDocFile = !empty($doc['file_path']) || !empty($doc['file_url']);
                                $viewUrl = !empty($doc['file_url']) ? $doc['file_url'] : (!empty($doc['file_path']) ? asset('storage/' . $doc['file_path']) : null);
                            @endphp
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-3.5 rounded-xl border border-[#E2E8F0] hover:border-[#CBD5E1] bg-white transition shadow-2xs">
                                <div class="flex items-start gap-3 min-w-0">
                                    <div class="w-9 h-9 rounded-xl {{ $hasDocFile ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-[#F1F5F9] text-[#64748B] border border-[#E2E8F0]' }} flex items-center justify-center shrink-0 mt-0.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <h3 class="text-[13px] font-bold text-[#1E293B]">{{ $doc['title'] }}</h3>
                                            @if($hasDocFile)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                                    Tersedia
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-500 border border-slate-200">
                                                    Menunggu dari Sales
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-[11.5px] text-[#64748B] mt-0.5">{{ $doc['description'] }}</p>
                                        @if($doc['number'])
                                            <p class="text-[11px] font-mono text-[#475569] mt-0.5 font-semibold">No: {{ $doc['number'] }}</p>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 self-end sm:self-center shrink-0">
                                    @if($hasDocFile && $viewUrl)
                                        {{-- View / Lihat File --}}
                                        <a href="{{ $viewUrl }}" target="_blank" class="px-3 py-1.5 rounded-xl bg-white hover:bg-[#F8FAFC] text-[#1E293B] text-[11px] font-bold transition flex items-center gap-1.5 border border-[#CBD5E1] shadow-2xs">
                                            <svg class="w-3.5 h-3.5 text-[#64748B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            <span>Lihat File</span>
                                        </a>

                                        {{-- Download / Unduh --}}
                                        <a href="{{ $viewUrl }}" download class="px-3 py-1.5 rounded-xl bg-[#F8FAFC] hover:bg-[#F1F5F9] text-[#1E293B] text-[11px] font-bold transition flex items-center gap-1.5 border border-[#CBD5E1] shadow-2xs">
                                            <svg class="w-3.5 h-3.5 text-[#64748B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                            <span>Download</span>
                                        </a>

                                        @if($canUploadDoc)
                                            <button type="button" @click="selectedDocType = '{{ $docKey }}'; selectedDocTitle = '{{ $doc['title'] }}'; isDocModalOpen = true" title="Ganti Berkas Dokumen" class="p-1.5 rounded-lg text-[#64748B] hover:text-[#8F0A0D] hover:bg-rose-50 transition border border-transparent hover:border-rose-200 cursor-pointer">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                            </button>
                                        @endif
                                    @else
                                        {{-- Belum Ada: Status Menunggu Berkas --}}
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0] text-[11px] font-medium text-[#64748B]">
                                            <svg class="w-3.5 h-3.5 text-[#94A3B8]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <span>Menunggu Sales</span>
                                        </span>

                                        @if($canUploadDoc)
                                            <button type="button" @click="selectedDocType = '{{ $docKey }}'; selectedDocTitle = '{{ $doc['title'] }}'; isDocModalOpen = true" title="Upload Berkas Manual" class="px-2.5 py-1.5 rounded-xl bg-white hover:bg-[#F8FAFC] text-[#64748B] hover:text-[#8F0A0D] text-[11px] font-bold transition flex items-center gap-1.5 border border-[#CBD5E1] shadow-2xs cursor-pointer">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                                <span>Upload</span>
                                            </button>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        @endforeach


                    </div>
                </div>
            </div>

            <!-- ========================================================== -->
            <!-- TAB 2: PREVENTIVE MAINTENANCE (PM)                         -->
            <!-- ========================================================== -->
            <div x-show="activeTab === 'pm'" x-cloak class="space-y-5 anim-fade-up">
                
                {{-- Card 1: Visual Timeline Stepper --}}
                <div class="ipnet-card p-5 sm:p-6 space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border-b border-[#E2E8F0] pb-3">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-[#8F0A0D]"></span>
                            <h3 class="text-[13px] font-bold text-[#1E293B] uppercase tracking-wider">
                                Timeline Siklus Kunjungan PM ({{ $pmTotal }}x Visit &bull; {{ $project->pm_frequency_label }})
                            </h3>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-[11.5px] font-semibold text-[#64748B]">Realisasi:</span>
                            <span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-rose-50 text-[#8F0A0D] border border-rose-200">
                                {{ $pmCompleted }} dari {{ $pmTotal }} Visit Selesai
                            </span>
                        </div>
                    </div>

                    {{-- Visual Stepper: Memanjang jika <= 6, Berkelok seperti Ular (Snake Flow) jika > 6 --}}
                    <div class="py-3 px-2 overflow-x-auto">
                        @php
                            // Siapkan koleksi seluruh node PM lengkap dengan Start (Bendera) dan Finish, per baris konsisten 6 bulatan
                            $pmNodeItems = collect();
                            $pmNodeItems->push([
                                'is_milestone' => true,
                                'type'         => 'start',
                                'label'        => 'Start',
                                'sub'          => $project->service_start_date ? date('d/m/Y', strtotime($project->service_start_date)) : 'Awal',
                                'icon'         => 'flag',
                            ]);

                            $cyclesCollection = $pmCycles->count() ? $pmCycles : collect(range(1, $pmTotal))->map(function($n) {
                                return (object)[
                                    'cycle_number'    => $n,
                                    'cycle_label'     => 'PM ' . $n,
                                    'activity_status' => 'Scheduled',
                                    'actual_date'     => null,
                                    'scheduled_date'  => null,
                                ];
                            });

                            foreach($cyclesCollection as $cycle) {
                                $actStatus = strtolower($cycle->activity_status ?? 'scheduled');
                                $repStatus = strtolower($cycle->report_status ?? '');
                                $hasActual = !empty($cycle->actual_date);

                                // Berubah hijau (done) jika status Completed/Solved, atau Laporan Approved/Submitted, atau sudah ada inputan realisasi visit
                                $isCycleDone = in_array($actStatus, ['completed', 'solved', 'selesai', 'done', 'resolved'])
                                    || in_array($repStatus, ['approved', 'submitted', 'signed', 'invoiced'])
                                    || $hasActual;
                                $isCycleInProg = !$isCycleDone && (in_array($actStatus, ['in progress', 'assigned', 'on progress']) || $repStatus === 'draft');

                                // Tanggal hanya tampil jika sudah ada inputan realisasi visit (actual_date terisi)
                                $subText = $hasActual 
                                    ? date('d/m/Y', strtotime($cycle->actual_date)) 
                                    : (!empty($cycle->scheduled_date) ? date('d/m/Y', strtotime($cycle->scheduled_date)) : ($isCycleInProg ? 'In Progress' : 'Set Jadwal'));

                                $pmNodeItems->push([
                                    'is_milestone' => false,
                                    'type'         => 'cycle',
                                    'cycle'        => $cycle,
                                    'number'       => $cycle->cycle_number,
                                    'label'        => 'PM ' . $cycle->cycle_number,
                                    'full_label'   => $cycle->cycle_label ?? ('PM ' . $cycle->cycle_number),
                                    'sub'          => $subText,
                                    'has_date'     => $hasActual,
                                    'status'       => $cycle->activity_status ?? 'Scheduled',
                                    'is_done'      => $isCycleDone,
                                    'is_in_prog'   => $isCycleInProg,
                                ]);
                            }

                            $pmNodeItems->push([
                                'is_milestone' => true,
                                'type'         => 'end',
                                'label'        => 'Finish',
                                'sub'          => $project->service_end_date ? date('d/m/Y', strtotime($project->service_end_date)) : 'Akhir Kontrak',
                                'icon'         => 'check',
                            ]);

                            // Konsisten 6 bulatan per baris (Start + PM 1..5 = 6 bulatan di baris pertama)
                            $chunkSize = 6; 
                            $isSnakePm = $pmNodeItems->count() > $chunkSize;
                            $pmRows = $isSnakePm ? $pmNodeItems->chunk($chunkSize) : collect([$pmNodeItems]);
                        @endphp

                        @if(!$isSnakePm)
                            {{-- ======================================================== --}}
                            {{-- MODE 1: MEMANJANG HORIZONTAL LURUS (KONDISI VISIT <= 6) --}}
                            {{-- ======================================================== --}}
                            <div class="flex items-center justify-between min-w-[650px] relative py-2">
                                {{-- Connecting Line --}}
                                <div class="absolute left-6 right-6 top-4 h-0.5 bg-[#E2E8F0] -z-0"></div>

                                @foreach($pmNodeItems as $node)
                                    @if($node['is_milestone'])
                                        <div class="relative z-10 flex flex-col items-center text-center">
                                            <div class="w-8 h-8 rounded-full bg-[#F8FAFC] border-2 border-[#94A3B8] flex items-center justify-center text-[#475569] shadow-2xs">
                                                @if($node['icon'] === 'flag')
                                                    <svg class="w-3.5 h-3.5 text-[#475569]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2z"/></svg>
                                                @else
                                                    <svg class="w-3.5 h-3.5 text-[#475569]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                @endif
                                            </div>
                                            <span class="text-[11px] font-bold text-[#1E293B] mt-1.5">{{ $node['label'] }}</span>
                                            <span class="text-[10px] text-[#64748B]">{{ $node['sub'] }}</span>
                                        </div>
                                    @else
                                        @php
                                            $isDone = $node['is_done'] ?? ($node['status'] === 'Completed');
                                            $isInProg = $node['is_in_prog'] ?? ($node['status'] === 'In Progress');
                                        @endphp
                                        <div class="relative z-10 flex flex-col items-center text-center cursor-pointer group"
                                             @if(isset($node['cycle']->id)) @click="openPmModal({{ json_encode($node['cycle']) }})" @endif>
                                            <div class="w-8 h-8 rounded-full border-2 flex items-center justify-center font-bold text-[11px] transition-all group-hover:scale-110
                                                {{ $isDone ? 'bg-emerald-600 border-emerald-600 text-white shadow-xs' : ($isInProg ? 'bg-amber-500 border-amber-500 text-white animate-pulse' : 'bg-white border-[#CBD5E1] text-[#64748B] group-hover:border-[#8F0A0D] group-hover:text-[#8F0A0D]') }}">
                                                @if($isDone)
                                                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                                @else
                                                    {{ $node['number'] }}
                                                @endif
                                            </div>
                                            <span class="text-[11px] font-bold mt-1.5 {{ $isDone ? 'text-emerald-700' : ($isInProg ? 'text-amber-700' : 'text-[#1E293B]') }}">
                                                {{ $node['label'] }}
                                            </span>
                                            <span class="text-[10px] {{ $isDone ? 'text-emerald-600 font-medium' : ($isInProg ? 'text-amber-600 font-medium' : 'text-[#94A3B8]') }}">{{ $node['sub'] }}</span>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @else
                            {{-- ======================================================== --}}
                            {{-- MODE 2: VISUALISASI BERKELOK KEK ULER (VISIT BANYAK > 6)  --}}
                            {{-- ======================================================== --}}
                            <div class="min-w-[720px] max-w-5xl mx-auto space-y-7 py-3">
                                @foreach($pmRows as $rowIndex => $rowNodes)
                                    @php
                                        $isRtl = ($rowIndex % 2 === 1);
                                        $hasNextRow = ($rowIndex < $pmRows->count() - 1);
                                        $nodeList = $isRtl ? $rowNodes->reverse() : $rowNodes;
                                    @endphp

                                    <div class="relative">
                                        {{-- Garis Utama Horizontal Baris Ini --}}
                                        <div class="absolute top-4 left-6 right-6 h-0.5 bg-[#CBD5E1] -z-0"></div>

                                        {{-- Lekukan U-Turn Berkelok (Loop Belok) Menyambung Baris Bawahnya --}}
                                        @if($hasNextRow)
                                            @if(!$isRtl)
                                                {{-- Belokan Loop Kanan: Mengalir ke bawah kanan --}}
                                                <div class="absolute -right-1 top-4 w-10 h-[calc(100%+1.75rem)] border-t-2 border-r-2 border-b-2 rounded-r-3xl border-[#CBD5E1] pointer-events-none -z-0"></div>
                                                <div class="absolute right-7 -bottom-3.5 text-[#94A3B8] text-[10px]">
                                                    <svg class="w-3.5 h-3.5 transform rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                                </div>
                                            @else
                                                {{-- Belokan Loop Kiri: Mengalir ke bawah kiri --}}
                                                <div class="absolute -left-1 top-4 w-10 h-[calc(100%+1.75rem)] border-t-2 border-l-2 border-b-2 rounded-l-3xl border-[#CBD5E1] pointer-events-none -z-0"></div>
                                                <div class="absolute left-7 -bottom-3.5 text-[#94A3B8] text-[10px]">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                                </div>
                                            @endif
                                        @endif

                                        {{-- Node Stepper Baris Ini --}}
                                        <div class="flex items-start justify-between relative z-10 px-2 {{ $isRtl ? 'flex-row-reverse' : '' }}">
                                            @foreach($rowNodes as $node)
                                                <div class="flex-1 flex flex-col items-center text-center">
                                                    @if($node['is_milestone'])
                                                        <div class="w-8 h-8 rounded-full bg-[#F8FAFC] border-2 border-[#94A3B8] flex items-center justify-center text-[#475569] shadow-2xs">
                                                            @if($node['icon'] === 'flag')
                                                                <svg class="w-3.5 h-3.5 text-[#475569]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2z"/></svg>
                                                            @else
                                                                <svg class="w-3.5 h-3.5 text-[#475569]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                            @endif
                                                        </div>
                                                        <span class="text-[11px] font-bold text-[#1E293B] mt-1.5">{{ $node['label'] }}</span>
                                                        <span class="text-[10px] text-[#64748B]">{{ $node['sub'] }}</span>
                                                    @else
                                                        @php
                                                            $isDone = $node['is_done'] ?? ($node['status'] === 'Completed');
                                                            $isInProg = $node['is_in_prog'] ?? ($node['status'] === 'In Progress');
                                                        @endphp
                                                        <div class="cursor-pointer group flex flex-col items-center"
                                                             @if(isset($node['cycle']->id)) @click="openPmModal({{ json_encode($node['cycle']) }})" @endif>
                                                            <div class="w-8 h-8 rounded-full border-2 flex items-center justify-center font-bold text-[11px] transition-all group-hover:scale-110
                                                                {{ $isDone ? 'bg-emerald-600 border-emerald-600 text-white shadow-xs' : ($isInProg ? 'bg-amber-500 border-amber-500 text-white animate-pulse' : 'bg-white border-[#CBD5E1] text-[#64748B] group-hover:border-[#8F0A0D] group-hover:text-[#8F0A0D]') }}">
                                                                @if($isDone)
                                                                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                                                @else
                                                                    {{ $node['number'] }}
                                                                @endif
                                                            </div>
                                                            <span class="text-[11px] font-bold mt-1.5 {{ $isDone ? 'text-emerald-700' : ($isInProg ? 'text-amber-700' : 'text-[#1E293B]') }}">
                                                                {{ $node['label'] }}
                                                            </span>
                                                            <span class="text-[10px] {{ $isDone ? 'text-emerald-600 font-medium' : ($isInProg ? 'text-amber-600 font-medium' : 'text-[#94A3B8]') }}">{{ $node['sub'] }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endforeach

                                            {{-- Penyeimbang lebar jika baris terakhir memiliki item lebih sedikit --}}
                                            @if($rowNodes->count() < $chunkSize)
                                                @for($fill = 0; $fill < ($chunkSize - $rowNodes->count()); $fill++)
                                                    <div class="flex-1 opacity-0 pointer-events-none"></div>
                                                @endfor
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Card 2: PM Cycles Table --}}
                <div class="ipnet-card overflow-hidden">
                    <div class="p-4 sm:p-5 border-b border-[#E2E8F0]">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-[#8F0A0D]"></span>
                            <h3 class="text-[13px] font-bold text-[#1E293B] uppercase tracking-wider">Daftar Siklus Preventive Maintenance (PM)</h3>
                        </div>
                        <p class="text-[12px] text-[#64748B] mt-0.5">Pantau realisasi visit lapangan, teknisi pelaksana, laporan inspeksi PDF, dan berita acara penagihan</p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="bg-[#F8FAFC] text-[10.5px] font-bold tracking-wider text-[#64748B] uppercase border-b border-[#E2E8F0]">
                                    <th class="py-3 px-4">Siklus PM</th>
                                    <th class="py-3 px-4">Jadwal Target</th>
                                    <th class="py-3 px-4">Realisasi Visit</th>
                                    <th class="py-3 px-4">PIC Managed Service</th>
                                    <th class="py-3 px-4">Lead Engineer</th>
                                    <th class="py-3 px-4">Engineer</th>
                                    <th class="py-3 px-4 text-center">Status Visit</th>
                                    <th class="py-3 px-4 text-center">Laporan PM</th>
                                    <th class="py-3 px-4 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#F1F5F9]">
                                @forelse($pmCycles as $cycle)
                                    <tr class="hover:bg-[#F8FAFC]/80 transition">
                                        <td class="py-3.5 px-4">
                                            <div class="font-bold text-[#1E293B] text-[12.5px]">{{ $cycle->cycle_label }}</div>
                                            <div class="text-[10.5px] text-[#64748B]">Siklus ke-{{ $cycle->cycle_number }}</div>
                                        </td>
                                        {{-- Jadwal Target (Bisa Diubah oleh Tim Managed Service) --}}
                                        <td class="py-3.5 px-4 font-medium">
                                            <button type="button" 
                                                    @click="openPmModal({{ json_encode($cycle) }})" 
                                                    class="group inline-flex items-center gap-1.5 text-[#334155] hover:text-[#8F0A0D] transition cursor-pointer text-left" 
                                                    title="Klik untuk ubah jadwal target kunjungan PM">
                                                <span class="font-semibold text-[12px] group-hover:underline">
                                                    @if($cycle->scheduled_date)
                                                        <span class="text-slate-800 font-semibold">{{ date('d M Y', strtotime($cycle->scheduled_date)) }}</span>
                                                    @else
                                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">Set Jadwal</span>
                                                    @endif
                                                </span>
                                                <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-[#8F0A0D] transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </button>
                                        </td>
                                        <td class="py-3.5 px-4 font-medium">
                                            @if($cycle->actual_date)
                                                <span class="text-emerald-700 font-bold text-[12px]">{{ date('d M Y', strtotime($cycle->actual_date)) }}</span>
                                            @else
                                                <span class="text-slate-300 font-bold text-sm select-none">&mdash;</span>
                                            @endif
                                        </td>
                                        {{-- Kolom 4: PIC Managed Service (Hanya muncul jika sudah diset, jika belum strip rapi) --}}
                                        <td class="py-3.5 px-4 text-[#334155] font-medium">
                                            @if($cycle->picMs)
                                                @php
                                                    $picMsName = trim($cycle->picMs->name);
                                                    $picMsFirstName = explode(' ', $picMsName)[0] ?: $picMsName;
                                                    $picMsInitial = strtoupper(substr($picMsFirstName, 0, 1)) ?: 'E';
                                                @endphp
                                                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-rose-50 text-[#8F0A0D] border border-rose-200 text-[11px] font-bold shadow-2xs" title="{{ $picMsName }}">
                                                    <span class="w-4 h-4 rounded-full bg-[#8F0A0D] text-white text-[8.5px] font-black flex items-center justify-center shrink-0">
                                                        {{ $picMsInitial }}
                                                    </span>
                                                    <span>{{ $picMsFirstName }}</span>
                                                </div>
                                            @else
                                                <span class="text-slate-300 font-bold text-sm select-none">&mdash;</span>
                                            @endif
                                        </td>

                                        {{-- Kolom 5: Lead Engineer (Hanya muncul jika sudah diset, jika belum strip rapi) --}}
                                        <td class="py-3.5 px-4 text-[#334155] font-medium">
                                            @if($cycle->leadEngineer)
                                                @php
                                                    $leadEngName = trim($cycle->leadEngineer->name);
                                                    $leadEngFirstName = explode(' ', $leadEngName)[0] ?: $leadEngName;
                                                    $leadEngInitial = strtoupper(substr($leadEngFirstName, 0, 1)) ?: 'L';
                                                @endphp
                                                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-blue-50 text-blue-700 border border-blue-200 text-[11px] font-bold shadow-2xs" title="{{ $leadEngName }} ({{ $cycle->leadEngineer->position ?: 'Lead' }})">
                                                    <span class="w-4 h-4 rounded-full bg-blue-600 text-white text-[8.5px] font-black flex items-center justify-center shrink-0">
                                                        {{ $leadEngInitial }}
                                                    </span>
                                                    <span>{{ $leadEngFirstName }}</span>
                                                </div>
                                            @else
                                                <span class="text-slate-300 font-bold text-sm select-none">&mdash;</span>
                                            @endif
                                        </td>

                                        {{-- Kolom 6: Engineer Lapangan (Hanya Nama Depan; Jika belum ada dikosongi / strip rapi) --}}
                                        <td class="py-3.5 px-4 text-[#334155] font-medium">
                                            @php
                                                $engList = is_array($cycle->assigned_engineers) ? $cycle->assigned_engineers : [];
                                                $picMsId = $cycle->pic_ms_id;
                                                $leadEngId = $cycle->lead_engineer_id;
                                                $filteredEngList = array_filter($engList, function($eng) use ($picMsId, $leadEngId) {
                                                    $eId = $eng['id'] ?? null;
                                                    if ($picMsId && $eId == $picMsId) return false;
                                                    if ($leadEngId && $eId == $leadEngId) return false;
                                                    return true;
                                                });
                                            @endphp
                                            @if(!empty($filteredEngList))
                                                <div class="flex flex-wrap gap-1 items-center">
                                                    @foreach($filteredEngList as $ae)
                                                        @php
                                                            $fullName = trim($ae['name'] ?? '');
                                                            $firstName = explode(' ', $fullName)[0] ?: '-';
                                                            $engInitial = strtoupper(substr($firstName, 0, 1)) ?: 'E';
                                                        @endphp
                                                        <div class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-[11px] font-semibold shadow-2xs" title="{{ $fullName }}">
                                                            <span class="w-3.5 h-3.5 rounded-full bg-emerald-600 text-white text-[8px] font-bold flex items-center justify-center shrink-0">
                                                                {{ $engInitial }}
                                                            </span>
                                                            <span>{{ $firstName }}</span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @elseif($cycle->assignedEngineer && $cycle->assigned_engineer_id != $picMsId && $cycle->assigned_engineer_id != $leadEngId)
                                                @php
                                                    $fullName = trim($cycle->assignedEngineer->name);
                                                    $firstName = explode(' ', $fullName)[0] ?: '-';
                                                    $engInitial = strtoupper(substr($firstName, 0, 1)) ?: 'E';
                                                @endphp
                                                <div class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-[11px] font-semibold shadow-2xs" title="{{ $fullName }}">
                                                    <span class="w-3.5 h-3.5 rounded-full bg-emerald-600 text-white text-[8px] font-bold flex items-center justify-center shrink-0">
                                                        {{ $engInitial }}
                                                    </span>
                                                    <span>{{ $firstName }}</span>
                                                </div>
                                            @else
                                                <span class="text-slate-300 font-bold text-sm select-none">&mdash;</span>
                                            @endif
                                        </td>
                                        <td class="py-3.5 px-4 text-center">
                                            @php
                                                $actStatus = strtolower($cycle->activity_status ?? 'scheduled');
                                                $repStatus = strtolower($cycle->report_status ?? '');
                                                $isDone = in_array($actStatus, ['completed', 'solved']) 
                                                    || in_array($repStatus, ['approved', 'submitted', 'signed', 'invoiced']) 
                                                    || !empty($cycle->actual_date);
                                                $isInProg = !$isDone && (in_array($actStatus, ['in progress', 'assigned', 'on progress']) || $repStatus === 'draft');
                                            @endphp
                                            @if($isDone)
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10.5px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 shadow-2xs">
                                                    <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                    <span>Completed</span>
                                                </span>
                                            @elseif($isInProg)
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10.5px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                                    <span>In Progress</span>
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-medium bg-slate-100 text-slate-500 border border-slate-200">
                                                    Scheduled
                                                </span>
                                            @endif
                                        </td>
                                        {{-- Laporan PM (Icon View & Icon Download; Jika belum ada laporan tampilkan strip bersih) --}}
                                        <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                            @php
                                                $allReports = $cycle->all_report_files;
                                                $reportCount = count($allReports);
                                            @endphp
                                            @if($reportCount === 1)
                                                <div class="inline-flex items-center justify-center gap-1.5">
                                                    {{-- 1. Icon View (Lihat / Preview) --}}
                                                    <a href="{{ asset('storage/' . $allReports[0]['path']) }}" 
                                                       target="_blank" 
                                                       class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 hover:text-emerald-800 border border-emerald-200 transition-all flex items-center justify-center shadow-2xs cursor-pointer" 
                                                       title="Lihat Laporan PM ({{ $allReports[0]['filename'] }})">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                        </svg>
                                                    </a>
                                                    {{-- 2. Icon Download (Unduh) --}}
                                                    <a href="{{ asset('storage/' . $allReports[0]['path']) }}" 
                                                       download="{{ $allReports[0]['filename'] ?? 'Laporan_PM_' . $cycle->cycle_label . '.pdf' }}" 
                                                       class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 hover:text-blue-800 border border-blue-200 transition-all flex items-center justify-center shadow-2xs cursor-pointer" 
                                                       title="Unduh Laporan PM ({{ $allReports[0]['filename'] }})">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                                        </svg>
                                                    </a>
                                                </div>
                                            @elseif($reportCount > 1)
                                                <div class="relative inline-block text-left" x-data="{ openDocs: false }">
                                                    <button type="button" @click="openDocs = !openDocs" class="inline-flex items-center gap-1.5 px-2.5 py-1.5 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 rounded-lg text-xs font-bold border border-emerald-200 transition shadow-2xs cursor-pointer">
                                                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                        <span>{{ $reportCount }} Berkas</span>
                                                        <svg class="w-3 h-3 text-emerald-600 transition-transform" :class="openDocs ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                                    </button>
                                                    <div x-show="openDocs" @click.outside="openDocs = false" x-cloak class="absolute left-1/2 -translate-x-1/2 top-full mt-1.5 bg-white border border-[#CBD5E1] rounded-xl shadow-xl z-50 min-w-[280px] py-1 text-left text-xs">
                                                        <div class="px-3 py-1 font-bold text-[10px] uppercase text-slate-400 bg-slate-50 border-b border-slate-100">Daftar Berkas Laporan PM</div>
                                                        @foreach($allReports as $rIdx => $rf)
                                                            <div class="px-3 py-2 flex items-center justify-between gap-2 border-b border-slate-50 last:border-b-0 hover:bg-slate-50 transition">
                                                                <span class="text-[11.5px] text-slate-700 truncate max-w-[170px]" title="{{ $rf['filename'] }}">
                                                                    <span class="font-bold text-emerald-700">#{{ $rIdx + 1 }}.</span> {{ $rf['filename'] }}
                                                                </span>
                                                                <div class="flex items-center gap-1 shrink-0">
                                                                    {{-- Icon View --}}
                                                                    <a href="{{ asset('storage/' . $rf['path']) }}" target="_blank" class="w-6 h-6 rounded bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 flex items-center justify-center transition shadow-2xs" title="Lihat Berkas (Preview)">
                                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                                    </a>
                                                                    {{-- Icon Download --}}
                                                                    <a href="{{ asset('storage/' . $rf['path']) }}" download="{{ $rf['filename'] ?? 'Laporan_PM.pdf' }}" class="w-6 h-6 rounded bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 flex items-center justify-center transition shadow-2xs" title="Unduh Berkas (Download)">
                                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-slate-300 font-bold text-sm select-none">&mdash;</span>
                                            @endif
                                        </td>
                                        <td class="py-3.5 px-4 text-center">
                                            <button type="button" 
                                                    @click="openPmModal({{ json_encode($cycle) }})"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white border border-[#CBD5E1] hover:border-[#8F0A0D] text-[#1E293B] hover:text-[#8F0A0D] hover:bg-rose-50/50 text-[11px] font-bold transition shadow-2xs cursor-pointer">
                                                <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-[#8F0A0D]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                <span>Update Realisasi</span>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="py-10 text-center text-[#64748B] text-[12px]">
                                            Belum ada siklus PM yang tersimpan untuk proyek ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- ========================================================== -->
            <!-- TAB 3: CORRECTIVE MAINTENANCE (CM)                         -->
            <!-- ========================================================== -->
            <div x-show="activeTab === 'cm'" x-cloak class="space-y-5 anim-fade-up">
                
                {{-- KARTU 1: DAFTAR TIKET CM (PINDAH KE ATAS) --}}
                <div class="ipnet-card overflow-hidden">
                    <div class="p-4 sm:p-5 border-b border-[#E2E8F0] flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-[#8F0A0D]"></span>
                                <h3 class="text-[13px] font-bold text-[#1E293B] uppercase tracking-wider">Daftar Tiket Corrective Maintenance Proyek Ini</h3>
                            </div>
                            <p class="text-[12px] text-[#64748B] mt-0.5">Riwayat insiden, perbaikan perangkat, teknisi yang bertugas, dan berita acara perbaikan (BAP)</p>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <button type="button" @click="selectedCmSlot = null; isCmModalOpen = true" class="px-3.5 py-2 rounded-xl bg-[#8F0A0D] hover:bg-[#72080A] text-white text-xs font-bold shadow-xs transition flex items-center gap-1.5 cursor-pointer">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                <span>+ Catat Tiket CM Baru</span>
                            </button>
                        </div>
                    </div>

                    @if($cmTickets->isEmpty())
                        <div class="py-14 text-center space-y-3">
                            <div class="w-12 h-12 rounded-2xl bg-gray-50 border border-gray-200 flex items-center justify-center text-gray-400 mx-auto">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            <div class="text-xs font-bold text-[#1E293B]">Belum ada tiket Corrective Maintenance</div>
                            <p class="text-[11.5px] text-[#64748B] max-w-sm mx-auto">Semua perangkat dan sistem berjalan normal. Kuota tiket masih utuh sebanyak {{ $cmRemaining }} tiket.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs border-collapse">
                                <thead>
                                    <tr class="bg-[#F8FAFC] text-[10.5px] font-bold tracking-wider text-[#64748B] uppercase border-b border-[#E2E8F0]">
                                        <th class="py-3 px-4">No. Tiket & Tanggal</th>
                                        <th class="py-3 px-4">Judul Insiden / Masalah</th>
                                        <th class="py-3 px-4">Prioritas & SLA</th>
                                        <th class="py-3 px-4">Pelapor & Kontak</th>
                                        <th class="py-3 px-4">PIC Managed Service</th>
                                        <th class="py-3 px-4 text-center">Status</th>
                                        <th class="py-3 px-4 text-right">Lampiran BAP</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-[#F1F5F9]">
                                    @foreach($cmTickets as $t)
                                        <tr class="hover:bg-[#F8FAFC]/80 transition">
                                            <td class="py-3.5 px-4 whitespace-nowrap">
                                                <div class="font-mono font-bold text-[#1E293B] text-[12px]">{{ $t->ticket_number }}</div>
                                                <div class="text-[10.5px] text-[#64748B] mt-0.5">{{ $t->created_at->format('d/m/Y H:i') }}</div>
                                            </td>
                                            <td class="py-3.5 px-4">
                                                <div class="font-bold text-[#1E293B] text-[12.5px]">{{ $t->title }}</div>
                                                <div class="text-[11px] text-[#64748B] mt-0.5 line-clamp-1">{{ $t->description ?: '—' }}</div>
                                            </td>
                                            <td class="py-3.5 px-4 whitespace-nowrap">
                                                @php
                                                    $pVal = match(strtolower($t->priority ?? '')) {
                                                        'critical', 'platinum'      => 'Critical',
                                                        'high', 'gold', 'major'     => 'High',
                                                        'medium', 'silver', 'minor' => 'Medium',
                                                        'low', 'bronze'             => 'Low',
                                                        default                     => ucfirst(strtolower($t->priority ?? 'Medium')),
                                                    };
                                                    $pColor = match($pVal) {
                                                        'Critical' => 'bg-purple-50 text-purple-700 border-purple-200',
                                                        'High'     => 'bg-rose-50 text-rose-700 border-rose-200',
                                                        'Medium'   => 'bg-blue-50 text-blue-700 border-blue-200',
                                                        default    => 'bg-slate-100 text-slate-700 border-slate-200',
                                                    };
                                                @endphp
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold border {{ $pColor }}">
                                                    {{ $pVal }}
                                                </span>
                                            </td>
                                            <td class="py-3.5 px-4">
                                                <div class="font-medium text-[#1E293B] text-[12px]">{{ $t->reported_by ?: 'PIC Klien' }}</div>
                                                <div class="text-[10.5px] text-[#64748B]">{{ $t->contact_phone ?: '—' }}</div>
                                            </td>
                                            <td class="py-3.5 px-4 text-[#334155] font-medium whitespace-nowrap">
                                                {{ $t->assignedEngineer?->name ?: '—' }}
                                            </td>
                                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                                @php
                                                    $rawSt = strtolower(trim((string)$t->status));
                                                    $isStResolved = in_array($rawSt, ['resolved', 'closed', 'solved', 'completed', 'submitted', 'selesai', 'done', 'approved']) || !empty($t->resolved_at);
                                                    $isStInProg = !$isStResolved && in_array($rawSt, ['in progress', 'assigned', 'open', 'investigating', 'on progress']);
                                                @endphp
                                                @if($isStResolved)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                        Resolved
                                                    </span>
                                                @elseif($isStInProg)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                        In Progress
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-[#8F0A0D] border border-rose-200">
                                                        {{ $t->status ?: 'Open' }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                                <div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" @click="activeCmTicket = {{ json_encode($t) }}; isCmDetailModalOpen = true" title="Detail & Update Tiket" class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-slate-100 text-slate-700 hover:bg-slate-200 rounded-lg text-xs font-semibold border border-slate-300 transition cursor-pointer">
                                                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                        <span>Update</span>
                                                    </button>
                                                    @if($t->attachment_path)
                                                        <a href="{{ asset('storage/' . $t->attachment_path) }}" target="_blank" class="inline-flex items-center gap-1.5 px-2.5 py-1.5 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 rounded-lg text-xs font-semibold border border-emerald-200 transition shadow-2xs">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                            <span>Lihat BAP</span>
                                                        </a>
                                                    @else
                                                        <button type="button" @click="selectedUploadTicket = { id: {{ $t->id }}, number: '{{ $t->ticket_number }}' }; isUploadBapModalOpen = true;" class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-slate-100 text-slate-700 hover:bg-slate-200 rounded-lg text-xs font-semibold border border-slate-300 transition cursor-pointer">
                                                            <svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                                            <span>Upload</span>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                {{-- KARTU 2: VISUALISASI SLOT KUOTA TIKET (PINDAH KE BAWAH, TANPA LINEAR PROGRESS BAR) --}}
                <div class="ipnet-card p-5 sm:p-6 space-y-4">
                    @php
                        $displayQuota = ($cmQuota >= 999) ? max(6, $cmUsed + 2) : max(1, (int)$cmQuota);
                        $isSnakeCm = $displayQuota > 6;
                        $cmChunkSize = 6; // 6 slot tiket per baris

                        $cmSlots = collect();
                        $sortedTickets = $cmTickets->values();

                        for ($slotIndex = 1; $slotIndex <= $displayQuota; $slotIndex++) {
                            $ticket = $sortedTickets->get($slotIndex - 1);
                            $isUsed = ($ticket !== null) || ($slotIndex <= $cmUsed);
                            $ticketStatus = $ticket ? ($ticket->status ?: 'Resolved') : ($isUsed ? 'Resolved' : 'Available');
                            $rawStatus = strtolower(trim((string)$ticketStatus));
                            $isResolved = in_array($rawStatus, ['resolved', 'closed', 'solved', 'completed', 'submitted', 'selesai', 'done', 'berhasil', 'sukses', 'approved']) || ($ticket && !empty($ticket->resolved_at));
                            $isInProg = !$isResolved && in_array($rawStatus, ['in progress', 'assigned', 'open', 'investigating', 'on progress']);

                            // Format tanggal d/m/Y seperti pada PM jika ada tanggal insiden / pelaksanaan
                            if ($ticket) {
                                $tDate = $ticket->incident_date ?: ($ticket->resolved_at ?: $ticket->created_at);
                                $subText = !empty($tDate) ? \Carbon\Carbon::parse($tDate)->format('d/m/Y') : ($ticket->status ?: 'Resolved');
                            } else {
                                $subText = 'Tersedia';
                            }

                            $cmSlots->push([
                                'slot_number' => $slotIndex,
                                'is_used'     => $isUsed,
                                'status'      => $ticketStatus,
                                'is_resolved' => $isResolved,
                                'is_in_prog'  => $isInProg,
                                'ticket'      => $ticket,
                                'label'       => 'Tiket #' . $slotIndex,
                                'sub'         => $subText,
                                'is_next'     => (!$isUsed && $slotIndex === ($cmUsed + 1)),
                            ]);
                        }

                        $cmRows = $isSnakeCm ? $cmSlots->chunk($cmChunkSize) : collect([$cmSlots]);
                    @endphp

                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-[#E2E8F0] pb-3">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="w-2 h-2 rounded-full bg-[#8F0A0D]"></span>
                                <span class="text-[11px] font-bold text-[#8F0A0D] uppercase tracking-wider block">Visualisasi Slot Kuota Tiket</span>
                            </div>
                            <h3 class="text-[14px] font-bold text-[#1E293B]">
                                Visualisasi Slot Kuota Tiket ({{ $displayQuota }} Kuota &bull; {{ $cmUsed }} Terpakai &bull; {{ $cmRemaining }} Tersisa)
                            </h3>
                            <p class="text-[11.5px] text-[#64748B] mt-0.5">Alur kuota penanganan insiden SLA &bull; Target MTTR: {{ $project->mttr_hours ?: 4 }} Jam &bull; Klik bulatan tiket untuk melihat detail atau mencatat tiket baru</p>
                        </div>
                    </div>

                    <div class="pt-2 overflow-x-auto">
                        @if(!$isSnakeCm)
                            {{-- MODE 1: MEMANJANG HORIZONTAL LURUS (KUOTA <= 6) --}}
                            <div class="flex items-center justify-between min-w-[550px] relative py-2 px-3">
                                <div class="absolute left-6 right-6 top-4 h-0.5 bg-[#CBD5E1] -z-0"></div>

                                @foreach($cmSlots as $slot)
                                    @php
                                        $isResolved = $slot['is_resolved'];
                                        $isInProg = $slot['is_in_prog'];
                                        $isNext = $slot['is_next'] ?? false;
                                        $ticketObj = $slot['ticket'];
                                    @endphp
                                    <div class="relative z-10 flex flex-col items-center text-center cursor-pointer group"
                                         @if($ticketObj)
                                             @click="activeCmTicket = {{ json_encode($ticketObj) }}; isCmDetailModalOpen = true"
                                         @else
                                             @click="selectedCmSlot = {{ $slot['slot_number'] }}; isCmModalOpen = true"
                                         @endif
                                         title="{{ $ticketObj ? ($ticketObj->ticket_number . ' - ' . $ticketObj->title . ' (' . $ticketObj->status . ') - Klik untuk Update') : ('Tiket #' . $slot['slot_number'] . ' - Klik untuk Catat / Mulai Tiket Baru') }}">
                                        <div class="w-8 h-8 rounded-full border-2 flex items-center justify-center font-bold text-[11px] transition-all group-hover:scale-110
                                            {{ $slot['is_used'] 
                                                ? ($isResolved 
                                                    ? 'bg-emerald-600 border-emerald-600 text-white shadow-xs' 
                                                    : ($isInProg 
                                                        ? 'bg-amber-500 border-amber-500 text-white animate-pulse' 
                                                        : 'bg-[#8F0A0D] border-[#8F0A0D] text-white shadow-xs')) 
                                                : ($isNext 
                                                    ? 'bg-white border-[#8F0A0D] text-[#8F0A0D] ring-2 ring-[#8F0A0D]/20 shadow-xs' 
                                                    : 'bg-white border-[#CBD5E1] text-[#94A3B8] group-hover:border-[#8F0A0D] group-hover:text-[#8F0A0D]') }}">
                                            @if($slot['is_used'])
                                                @if($isResolved)
                                                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                                @elseif($isInProg)
                                                    {{ $slot['slot_number'] }}
                                                @else
                                                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                @endif
                                            @else
                                                {{ $slot['slot_number'] }}
                                            @endif
                                        </div>
                                        <span class="text-[11px] font-bold mt-1.5 transition-colors {{ $slot['is_used'] ? ($isResolved ? 'text-emerald-700' : ($isInProg ? 'text-amber-700' : 'text-[#8F0A0D]')) : ($isNext ? 'text-[#8F0A0D]' : 'text-[#1E293B] group-hover:text-[#8F0A0D]') }}">
                                            Tiket #{{ $slot['slot_number'] }}
                                        </span>
                                        <span class="text-[10px] transition-colors {{ $slot['is_used'] ? ($isResolved ? 'text-emerald-600 font-medium' : ($isInProg ? 'text-amber-600 font-medium' : 'text-[#8F0A0D] font-medium')) : ($isNext ? 'text-[#8F0A0D] font-bold' : 'text-[#94A3B8] group-hover:text-[#8F0A0D]') }}">
                                            {{ $slot['sub'] }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            {{-- MODE 2: BERKELOK KEK ULER (KUOTA BANYAK > 6) --}}
                            <div class="min-w-[720px] max-w-5xl mx-auto space-y-6 py-2">
                                @foreach($cmRows as $rowIndex => $rowSlots)
                                    @php
                                        $isRtl = ($rowIndex % 2 === 1);
                                        $hasNextRow = ($rowIndex < $cmRows->count() - 1);
                                    @endphp

                                    <div class="relative">
                                        {{-- Garis Utama Horizontal Baris Ini --}}
                                        <div class="absolute top-4 left-6 right-6 h-0.5 bg-[#CBD5E1] -z-0"></div>

                                        {{-- Garis Lengkung U-Turn Penghubung Antar Baris --}}
                                        @if($hasNextRow)
                                            @if(!$isRtl)
                                                {{-- Belokan di Kanan: Baris LTR -> Baris RTL --}}
                                                <div class="absolute right-6 top-4 w-10 h-16 border-t-2 border-r-2 border-b-2 border-[#CBD5E1] rounded-r-2xl pointer-events-none -z-0"></div>
                                            @else
                                                {{-- Belokan di Kiri: Baris RTL -> Baris LTR --}}
                                                <div class="absolute left-6 top-4 w-10 h-16 border-t-2 border-l-2 border-b-2 border-[#CBD5E1] rounded-l-2xl pointer-events-none -z-0"></div>
                                            @endif
                                        @endif

                                        {{-- Slot Tiket Baris Ini --}}
                                        <div class="flex items-start justify-between relative z-10 px-2 {{ $isRtl ? 'flex-row-reverse' : '' }}">
                                            @foreach($rowSlots as $slot)
                                                @php
                                                    $isResolved = $slot['is_resolved'];
                                                    $isInProg = $slot['is_in_prog'];
                                                    $isNext = $slot['is_next'] ?? false;
                                                    $ticketObj = $slot['ticket'];
                                                @endphp
                                                <div class="flex-1 flex flex-col items-center text-center">
                                                    <div class="cursor-pointer group flex flex-col items-center"
                                                         @if($ticketObj)
                                                             @click="activeCmTicket = {{ json_encode($ticketObj) }}; isCmDetailModalOpen = true"
                                                         @else
                                                             @click="selectedCmSlot = {{ $slot['slot_number'] }}; isCmModalOpen = true"
                                                         @endif
                                                         title="{{ $ticketObj ? ($ticketObj->ticket_number . ' - ' . $ticketObj->title . ' (' . $ticketObj->status . ') - Klik untuk Update') : ('Tiket #' . $slot['slot_number'] . ' - Klik untuk Catat / Mulai Tiket Baru') }}">
                                                        <div class="w-8 h-8 rounded-full border-2 flex items-center justify-center font-bold text-[11px] transition-all group-hover:scale-110
                                                            {{ $slot['is_used'] 
                                                                ? ($isResolved 
                                                                    ? 'bg-emerald-600 border-emerald-600 text-white shadow-xs' 
                                                                    : ($isInProg 
                                                                        ? 'bg-amber-500 border-amber-500 text-white animate-pulse' 
                                                                        : 'bg-[#8F0A0D] border-[#8F0A0D] text-white shadow-xs')) 
                                                                : ($isNext 
                                                                    ? 'bg-white border-[#8F0A0D] text-[#8F0A0D] ring-2 ring-[#8F0A0D]/20 shadow-xs' 
                                                                    : 'bg-white border-[#CBD5E1] text-[#94A3B8] group-hover:border-[#8F0A0D] group-hover:text-[#8F0A0D]') }}">
                                                            @if($slot['is_used'])
                                                                @if($isResolved)
                                                                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                                                @elseif($isInProg)
                                                                    {{ $slot['slot_number'] }}
                                                                @else
                                                                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                                @endif
                                                            @else
                                                                {{ $slot['slot_number'] }}
                                                            @endif
                                                        </div>
                                                        <span class="text-[11px] font-bold mt-1.5 transition-colors {{ $slot['is_used'] ? ($isResolved ? 'text-emerald-700' : ($isInProg ? 'text-amber-700' : 'text-[#8F0A0D]')) : ($isNext ? 'text-[#8F0A0D]' : 'text-[#1E293B] group-hover:text-[#8F0A0D]') }}">
                                                            Tiket #{{ $slot['slot_number'] }}
                                                        </span>
                                                        <span class="text-[10px] transition-colors {{ $slot['is_used'] ? ($isResolved ? 'text-emerald-600 font-medium' : ($isInProg ? 'text-amber-600 font-medium' : 'text-[#8F0A0D] font-medium')) : ($isNext ? 'text-[#8F0A0D] font-bold' : 'text-[#94A3B8] group-hover:text-[#8F0A0D]') }}">
                                                            {{ $slot['sub'] }}
                                                        </span>
                                                    </div>
                                                </div>
                                            @endforeach

                                            {{-- Penyeimbang lebar jika baris terakhir memiliki slot lebih sedikit --}}
                                            @if($rowSlots->count() < $cmChunkSize)
                                                @for($fill = 0; $fill < ($cmChunkSize - $rowSlots->count()); $fill++)
                                                    <div class="flex-1 opacity-0 pointer-events-none"></div>
                                                @endfor
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- ========================================================== -->
            {{-- [HIDDEN] SLA Tab Content --}}@if(false)<!-- TAB 4: PERHITUNGAN & EVALUASI SLA (AVAILABILITY & DOWNTIME) -->
            <!-- ========================================================== -->
            <div x-show="activeTab === 'sla'" x-cloak class="space-y-5 anim-fade-up">
                
                {{-- 4 Clean KPI Cards Symmetrical with Sales Design Standard --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
                    {{-- KPI 1: Realisasi SLA Uptime --}}
                    <div class="p-3.5 rounded-xl bg-[#F8FAFC] border {{ ($isSlaMet ?? true) ? 'border-emerald-200 bg-emerald-50/20' : 'border-rose-200 bg-rose-50/20' }} space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-bold text-[#64748B] uppercase tracking-wider">Realisasi SLA Uptime</span>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ ($isSlaMet ?? true) ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : 'bg-rose-100 text-[#8F0A0D] border border-rose-300' }}">
                                {{ ($isSlaMet ?? true) ? 'Memenuhi Target' : 'Di Bawah Target' }}
                            </span>
                        </div>
                        <p class="text-[20px] font-extrabold {{ ($isSlaMet ?? true) ? 'text-emerald-700' : 'text-[#8F0A0D]' }}">
                            {{ number_format($projectSlaUptime ?? 99.8, 2) }}%
                        </p>
                        <p class="text-[11.5px] text-[#64748B]">Bulan {{ now()->isoFormat('MMMM Y') }} ({{ $daysInMonth ?? 30 }} Hari)</p>
                    </div>

                    {{-- KPI 2: Target SLA Kontrak --}}
                    <div class="p-3.5 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0] space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-bold text-[#64748B] uppercase tracking-wider">Target Kontrak</span>
                            <span class="text-[10.5px] font-bold px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 border border-slate-200">
                                {{ $project->sla_tier ?: 'Gold' }} Tier
                            </span>
                        </div>
                        <p class="text-[20px] font-extrabold text-[#1E293B]">
                            {{ number_format($targetSla ?? ($project->sla_target_percentage ?: 99.50), 2) }}%
                        </p>
                        <p class="text-[11.5px] text-[#64748B]">Cakupan Standby {{ $project->sla_coverage_hours ?: '24x7' }}</p>
                    </div>

                    {{-- KPI 3: Akumulasi Downtime --}}
                    <div class="p-3.5 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0] space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-bold text-[#64748B] uppercase tracking-wider">Akumulasi Downtime</span>
                            <span class="text-[10.5px] font-bold px-2 py-0.5 rounded-full bg-rose-50 text-[#8F0A0D] border border-rose-200">
                                {{ count($downtimeDeviceList ?? []) }} Insiden
                            </span>
                        </div>
                        <p class="text-[20px] font-extrabold text-[#8F0A0D]">
                            {{ $totalProjDowntimeHours ?? 0 }} Jam
                        </p>
                        <p class="text-[11.5px] text-[#64748B]">Dari Total {{ $totalPeriodHours ?? 720 }} Jam Operasional</p>
                    </div>

                    {{-- KPI 4: Sisa Buffer Toleransi Downtime --}}
                    <div class="p-3.5 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0] space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-bold text-[#64748B] uppercase tracking-wider">Sisa Buffer Toleransi</span>
                            <span class="text-[10.5px] font-bold px-2 py-0.5 rounded-full {{ ($remainingBufferHours ?? 0) >= 0 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-[#8F0A0D] border border-rose-200' }}">
                                Maks. {{ $maxAllowedDowntimeHours ?? 3.6 }} Jam
                            </span>
                        </div>
                        <p class="text-[20px] font-extrabold {{ ($remainingBufferHours ?? 0) >= 0 ? 'text-emerald-700' : 'text-[#8F0A0D]' }}">
                            {{ ($remainingBufferHours ?? 0) >= 0 ? '+' : '' }}{{ $remainingBufferHours ?? 0 }} Jam
                        </p>
                        <p class="text-[11.5px] text-[#64748B]">Sebelum Pelanggaran SLA / Penalty</p>
                    </div>
                </div>

                {{-- Table: Rincian Gangguan & Evaluasi SLA Perangkat --}}
                <div class="ipnet-card overflow-hidden">
                    <div class="p-4 sm:p-5 pb-3 border-b border-[#E2E8F0] bg-[#F8FAFC] flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-4 bg-[#8F0A0D] rounded-full inline-block"></span>
                            <div>
                                <h3 class="text-[15px] font-bold text-[#1E293B]">Tabel Perhitungan SLA &amp; Rekapitulasi Downtime Perangkat</h3>
                                <p class="text-xs text-[#64748B]">Rincian durasi insiden perangkat jaringan, realisasi persentase availability, dan root cause sesuai standar SLA operasional.</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <button type="button" onclick="window.print()" class="px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-semibold text-slate-700 transition shadow-2xs inline-flex items-center gap-1.5 cursor-pointer">
                                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                <span>Cetak Laporan SLA</span>
                            </button>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="border-b border-[#E2E8F0] bg-slate-50/70 text-[11px] font-bold text-[#64748B] uppercase tracking-wider">
                                    <th class="py-3 px-3.5 w-12 text-center">No</th>
                                    <th class="py-3 px-3.5">Perangkat Jaringan</th>
                                    <th class="py-3 px-3.5">Tiket / Masalah</th>
                                    <th class="py-3 px-3.5 whitespace-nowrap">Mulai Down</th>
                                    <th class="py-3 px-3.5 whitespace-nowrap">Up Kembali</th>
                                    <th class="py-3 px-3.5 whitespace-nowrap text-center">Durasi Down</th>
                                    <th class="py-3 px-3.5 whitespace-nowrap text-center">Realisasi SLA</th>
                                    <th class="py-3 px-3.5">Penyebab (Root Cause)</th>
                                    <th class="py-3 px-3.5 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700 font-medium">
                                @forelse($downtimeDeviceList as $idx => $item)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="py-3.5 px-3.5 text-center font-bold text-slate-400">{{ $idx + 1 }}</td>
                                    <td class="py-3.5 px-3.5">
                                        <div class="font-bold text-slate-900 text-[12.5px]">{{ $item['device'] }}</div>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-600 mt-0.5">
                                            {{ $item['category'] }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-3.5">
                                        <div class="font-mono text-[11px] font-bold text-[#8F0A0D]">{{ $item['ticket_number'] }}</div>
                                        <div class="text-[11.5px] text-slate-800 font-medium line-clamp-1" title="{{ $item['title'] }}">{{ $item['title'] }}</div>
                                    </td>
                                    <td class="py-3.5 px-3.5 whitespace-nowrap text-slate-600">
                                        {{ $item['start_down'] }}
                                    </td>
                                    <td class="py-3.5 px-3.5 whitespace-nowrap text-slate-600">
                                        {{ $item['end_down'] }}
                                    </td>
                                    <td class="py-3.5 px-3.5 whitespace-nowrap text-center">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-bold bg-rose-50 text-[#8F0A0D] border border-rose-200">
                                            {{ $item['duration_text'] }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-3.5 whitespace-nowrap text-center">
                                        <div class="font-mono font-extrabold text-[12.5px] {{ $item['device_sla'] >= $targetSla ? 'text-emerald-700' : 'text-[#8F0A0D]' }}">
                                            {{ number_format($item['device_sla'], 2) }}%
                                        </div>
                                        <span class="text-[10px] text-slate-400 block">Ketersediaan</span>
                                    </td>
                                    <td class="py-3.5 px-3.5 text-[11.5px] text-slate-600 max-w-xs">
                                        {{ $item['root_cause'] }}
                                    </td>
                                    <td class="py-3.5 px-3.5 whitespace-nowrap text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10.5px] font-bold {{ $item['is_resolved'] ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                                            {{ $item['status'] }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="py-8 text-center">
                                        <div class="w-10 h-10 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto mb-2 border border-emerald-200">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        </div>
                                        <p class="text-[13px] font-bold text-slate-800">Zero Downtime (Ketersediaan Layanan 100%)</p>
                                        <p class="text-xs text-slate-500 mt-0.5">Tidak ada insiden atau gangguan perangkat yang tercatat pada periode ini.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                            @if(!empty($downtimeDeviceList) && count($downtimeDeviceList) > 0)
                            <tfoot>
                                <tr class="bg-slate-100/80 border-t-2 border-slate-300 font-bold text-slate-900 text-xs">
                                    <td colspan="5" class="py-3 px-3.5 text-right uppercase tracking-wider text-[11px] text-slate-600">
                                        Total Akumulasi Periode Ini:
                                    </td>
                                    <td class="py-3 px-3.5 text-center whitespace-nowrap">
                                        <span class="font-extrabold text-[#8F0A0D]">{{ $totalProjDowntimeHours }} Jam</span>
                                    </td>
                                    <td class="py-3 px-3.5 text-center whitespace-nowrap">
                                        <span class="font-mono font-extrabold text-[13px] {{ ($isSlaMet ?? true) ? 'text-emerald-700' : 'text-[#8F0A0D]' }}">
                                            {{ number_format($projectSlaUptime, 2) }}%
                                        </span>
                                    </td>
                                    <td colspan="2" class="py-3 px-3.5 text-center whitespace-nowrap">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md text-[11px] font-bold {{ ($isSlaMet ?? true) ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : 'bg-rose-100 text-[#8F0A0D] border border-rose-300' }}">
                                            <span class="w-2 h-2 rounded-full {{ ($isSlaMet ?? true) ? 'bg-emerald-600' : 'bg-[#8F0A0D]' }}"></span>
                                            {{ ($isSlaMet ?? true) ? 'MEMENUHI TARGET SLA (KOMPLIAN)' : 'PELANGGARAN SLA (BREACH)' }}
                                        </span>
                                    </td>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>
                </div>

                {{-- Catatan Metode Perhitungan & Evaluasi SLA (Executive Corporate) --}}
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-600 space-y-1.5">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="font-bold text-slate-800">Dasar Rumus &amp; Parameter Pengukuran SLA Ketersediaan Jaringan</span>
                    </div>
                    <p class="leading-relaxed pl-6 text-slate-600">
                        Perhitungan SLA Uptime didasarkan pada rumus ketersediaan: 
                        <code class="px-1.5 py-0.5 rounded bg-white border border-slate-300 font-mono text-[11px] text-slate-800">SLA (%) = (Total Durasi Periode - Total Downtime) / Total Durasi Periode &times; 100%</code>.
                        Total periode bulan ini adalah <strong class="text-slate-800">{{ $daysInMonth ?? 30 }} hari &times; 24 jam = {{ $totalPeriodHours ?? 720 }} jam</strong>. Dengan batas toleransi downtime maksimum <strong class="text-slate-800">{{ $maxAllowedDowntimeHours ?? 3.6 }} jam</strong> untuk mempertahankan standar SLA Tier {{ $project->sla_tier ?: 'Gold' }} ({{ number_format($targetSla ?? 99.5, 2) }}%).
                    </p>
                </div>

            </div>@endif



        </div>
    </div>

    <!-- ========================================================== -->
    <!-- MODAL 1: UPLOAD DOKUMEN (SPK, KONTRAK, BOQ, BA)           -->
    <!-- ========================================================== -->
    <template x-teleport="body">
        <div x-show="isDocModalOpen" x-cloak class="fixed inset-0 z-50 bg-[#0E0D12]/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-[#E2E8F0]" @click.away="isDocModalOpen = false">
                <div class="flex items-center justify-between border-b border-[#E2E8F0] pb-3 mb-4">
                    <div>
                        <p class="text-[10.5px] font-bold text-[#8F0A0D] uppercase tracking-wider">Upload Berkas Dokumen</p>
                        <h3 class="text-[15px] font-bold text-[#1E293B]" x-text="selectedDocTitle"></h3>
                    </div>
                    <button @click="isDocModalOpen = false" class="text-slate-400 hover:text-slate-700 p-1 rounded-lg text-lg font-bold">&times;</button>
                </div>

                <form action="{{ route('ms.projects.documents.update', $project->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <input type="hidden" name="doc_type" :value="selectedDocType">

                    <div>
                        <label class="block text-xs font-semibold text-[#334155] mb-1">Nomor Dokumen (Opsional)</label>
                        <input type="text" name="doc_number" placeholder="Contoh: SPK/2026/001 atau KTR-014" class="w-full px-3 py-2 bg-white border border-[#CBD5E1] rounded-xl text-xs text-[#1E293B] outline-none focus:border-[#8F0A0D]">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#334155] mb-1">Tanggal Dokumen (Opsional)</label>
                        <input type="date" name="doc_date" class="w-full px-3 py-2 bg-white border border-[#CBD5E1] rounded-xl text-xs text-[#1E293B] outline-none focus:border-[#8F0A0D]">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#334155] mb-1">Pilih Berkas File (PDF / Word / Gambar / ZIP) <span class="text-[#8F0A0D]">*</span></label>
                        <input type="file" name="doc_file" required 
                               @change="if ($event.target.files.length && $event.target.files[0].size > 52428800) { alert('Ukuran berkas ' + $event.target.files[0].name + ' melebihi batas 50MB. Silakan pilih berkas lain.'); $event.target.value = ''; }"
                               class="w-full px-3 py-2 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-xs text-[#1E293B] outline-none focus:border-[#8F0A0D] cursor-pointer">
                        <p class="text-[10.5px] text-[#94A3B8] mt-1">Maksimal ukuran file: 50MB.</p>
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-[#E2E8F0]">
                        <button type="button" @click="isDocModalOpen = false" class="px-4 py-2 rounded-xl text-xs font-semibold text-[#64748B] hover:bg-[#F8FAFC]">Batal</button>
                        <button type="submit" class="btn-ipnet-gradient px-4 py-2 rounded-xl text-white text-xs font-bold shadow-md cursor-pointer">Upload Sekarang</button>
                    </div>
                </form>
            </div>
        </div>
    </template>

    <!-- ========================================================== -->
    <!-- MODAL 2: UPDATE SIKLUS PM                                  -->
    <!-- ========================================================== -->
    <template x-teleport="body">
        <div x-show="isPmModalOpen" x-cloak class="fixed inset-0 z-50 bg-[#0E0D12]/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-xl w-full shadow-2xl border border-[#E2E8F0] max-h-[90vh] flex flex-col overflow-hidden" @click.away="if (!engineerDropdownOpen) isPmModalOpen = false">
                <div class="flex items-center justify-between border-b border-[#E2E8F0] px-6 py-4 shrink-0 bg-white">
                    <div>
                        <p class="text-[10.5px] font-bold text-emerald-700 uppercase tracking-wider">Update Realisasi PM</p>
                        <h3 class="text-[15px] font-bold text-[#1E293B]" x-text="activeCycle ? activeCycle.cycle_label : 'Siklus PM'"></h3>
                    </div>
                    <button @click="isPmModalOpen = false" class="text-slate-400 hover:text-slate-700 p-1 rounded-lg text-lg font-bold leading-none">&times;</button>
                </div>

                <form :action="'/managed-service/monitoring/pm-cycles/' + (activeCycle ? activeCycle.id : '')" 
                      method="POST" 
                      enctype="multipart/form-data" 
                      @submit="isSavingPm = true" 
                      class="flex flex-col flex-1 min-h-0 overflow-hidden">
                    @csrf

                    {{-- Hidden Status BA (dihide sesuai instruksi user) --}}
                    <input type="hidden" name="billing_ba_status" :value="activeCycle ? (activeCycle.billing_ba_status || 'Pending') : 'Pending'">

                    <div class="p-6 overflow-y-auto space-y-4 flex-1">
                        <div>
                            <label class="block text-xs font-semibold text-[#334155] mb-1">Status Kunjungan (Activity) <span class="text-[#8F0A0D]">*</span></label>
                            <select name="activity_status" :value="activeCycle ? activeCycle.activity_status : 'Scheduled'" required class="w-full px-3 py-2 bg-white border border-[#CBD5E1] rounded-xl text-xs font-bold text-[#1E293B] outline-none focus:border-[#8F0A0D] cursor-pointer">
                                <option value="Scheduled">Scheduled (Terjadwal)</option>
                                <option value="In Progress">In Progress (Sedang Dikerjakan)</option>
                                <option value="Completed">Completed (Selesai Dilaksanakan)</option>
                            </select>
                        </div>

                        {{-- Jadwal Target & Tanggal Realisasi Visit (Bisa Diubah oleh Tim MS) --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <div>
                                <label class="block text-xs font-semibold text-[#334155] mb-1">
                                    Jadwal Target Kunjungan <span class="text-[#8F0A0D]">*</span>
                                </label>
                                <input type="date" name="scheduled_date" :value="activeCycle ? (activeCycle.scheduled_date ? String(activeCycle.scheduled_date).split('T')[0] : '') : ''" required class="w-full px-3 py-2 bg-white border border-[#CBD5E1] rounded-xl text-xs font-bold text-[#1E293B] outline-none focus:border-[#8F0A0D]">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-[#334155] mb-1">
                                    Tanggal Realisasi Visit
                                </label>
                                <input type="date" name="actual_date" :value="activeCycle ? (activeCycle.actual_date ? String(activeCycle.actual_date).split('T')[0] : '') : ''" class="w-full px-3 py-2 bg-white border border-[#CBD5E1] rounded-xl text-xs text-[#1E293B] outline-none focus:border-[#8F0A0D]">
                            </div>
                        </div>

                        {{-- PIC Managed Service & Lead Engineer --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <div>
                                <label class="block text-xs font-semibold text-[#334155] mb-1">PIC Managed Service (Koordinator)</label>
                                <select name="pic_ms_id" :value="activeCycle ? (activeCycle.pic_ms_id || '') : ''" class="w-full px-3 py-2 bg-white border border-[#CBD5E1] rounded-xl text-xs text-[#1E293B] outline-none focus:border-[#8F0A0D] cursor-pointer font-semibold">
                                    <option value="">Pilih PIC Managed Service</option>
                                    @foreach($picMsUsers as $pms)
                                        <option value="{{ $pms->id }}">{{ $pms->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-[#334155] mb-1">Lead Engineer (Koordinator Teknis)</label>
                                <select name="lead_engineer_id" :value="activeCycle ? (activeCycle.lead_engineer_id || '') : ''" class="w-full px-3 py-2 bg-white border border-[#CBD5E1] rounded-xl text-xs text-[#1E293B] outline-none focus:border-[#8F0A0D] cursor-pointer font-semibold">
                                    <option value="">Pilih Lead Engineer</option>
                                    @foreach($leadEngineers as $leng)
                                        <option value="{{ $leng->id }}">{{ $leng->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- KOLOM ENGINEER: Dikelompokkan (Tim Network & Tim Security), Bentuk Dropdown, Multi-Select --}}
                        <div class="relative">
                            <div class="flex items-center justify-between mb-1">
                                <label class="block text-xs font-semibold text-[#334155]">
                                    Engineer Pelaksana Lapangan
                                </label>
                                <span class="text-[10.5px] font-bold text-[#8F0A0D]" x-show="selectedEngineers.length > 0" x-text="selectedEngineers.length + ' Dipilih'"></span>
                            </div>

                            {{-- Dropdown Trigger Button --}}
                            <button type="button" 
                                    @click="engineerDropdownOpen = !engineerDropdownOpen" 
                                    class="w-full px-3 py-2 bg-white border border-[#CBD5E1] rounded-xl text-xs font-semibold text-[#1E293B] flex items-center justify-between text-left focus:border-[#8F0A0D] transition shadow-2xs cursor-pointer">
                                <span class="truncate" x-text="
                                    selectedEngineers.length === 0 
                                        ? 'Pilih Engineer Lapangan' 
                                        : (selectedEngineers.length === 1 
                                            ? (allEngineerMap[selectedEngineers[0]] || '1 Engineer Dipilih') 
                                            : selectedEngineers.length + ' Engineer Dipilih (' + selectedEngineers.map(id => allEngineerMap[id]).filter(Boolean).slice(0, 2).join(', ') + (selectedEngineers.length > 2 ? ' +' + (selectedEngineers.length - 2) : '') + ')')
                                "></span>
                                <svg class="w-4 h-4 text-slate-400 shrink-0 ml-1 transition-transform" :class="engineerDropdownOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>

                            {{-- Menu Dropdown Dikelompokkan Berdasarkan Divisi / Bidang Keahlian --}}
                            <div x-show="engineerDropdownOpen" 
                                 @click.outside="engineerDropdownOpen = false" 
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="opacity-0 translate-y-1"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="opacity-100 translate-y-0"
                                 x-transition:leave-end="opacity-0 translate-y-1"
                                 class="absolute left-0 right-0 top-full mt-1 bg-white border border-[#CBD5E1] rounded-xl shadow-xl z-50 max-h-60 overflow-y-auto py-1 text-xs">
                                
                                @foreach($groupedEngineers as $groupName => $groupUsers)
                                    <div class="px-3 py-1.5 font-bold text-[10px] uppercase tracking-wider text-slate-400 bg-slate-50 border-y border-slate-100 first:border-t-0">
                                        {{ $groupName }}
                                    </div>
                                    @foreach($groupUsers as $eng)
                                        <div @click="
                                                if (selectedEngineers.includes({{ $eng->id }})) {
                                                    selectedEngineers = selectedEngineers.filter(id => id !== {{ $eng->id }});
                                                } else {
                                                    selectedEngineers.push({{ $eng->id }});
                                                }
                                             "
                                             :class="selectedEngineers.includes({{ $eng->id }}) ? 'bg-red-50 text-[#8F0A0D] font-bold' : 'text-slate-700 hover:bg-slate-50'"
                                             class="px-3 py-2 cursor-pointer transition flex items-center justify-between border-b border-slate-50 last:border-b-0">
                                            <div class="flex items-center gap-2.5">
                                                <input type="checkbox" :checked="selectedEngineers.includes({{ $eng->id }})" class="w-3.5 h-3.5 rounded text-[#8F0A0D] focus:ring-[#8F0A0D] cursor-pointer pointer-events-none">
                                                <span class="text-xs">{{ $eng->name }}</span>
                                            </div>
                                            @if($eng->position)
                                                <span class="text-[10px] text-slate-400 font-normal">{{ $eng->position }}</span>
                                            @endif
                                        </div>
                                    @endforeach
                                @endforeach
                            </div>

                            {{-- Badge / Tag Engineer Terpilih --}}
                            <div class="flex flex-wrap gap-1.5 mt-2" x-show="selectedEngineers.length > 0">
                                <template x-for="id in selectedEngineers" :key="id">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-bold bg-red-50 text-[#8F0A0D] border border-red-200 shadow-2xs">
                                        <span x-text="allEngineerMap[id] || ('Engineer #' + id)"></span>
                                        <button type="button" @click.stop="selectedEngineers = selectedEngineers.filter(x => x !== id)" class="hover:text-red-900 text-xs font-bold leading-none cursor-pointer p-0.5">&times;</button>
                                    </span>
                                </template>
                            </div>

                            {{-- Hidden Inputs --}}
                            <template x-for="id in selectedEngineers" :key="id">
                                <input type="hidden" name="assigned_engineer_ids[]" :value="id">
                            </template>
                        </div>

                        {{-- UPLOAD BERKAS LAPORAN PM: SIMETRIS 2 SLOT (Laporan Utama & Berkas Pendukung/Foto/BAP) --}}
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <label class="block text-xs font-semibold text-[#334155]">
                                    Upload Berkas Laporan PM <span class="text-[11px] font-normal text-slate-500">(Bisa 2 Berkas Sekaligus)</span>
                                </label>
                                <span class="text-[10px] font-bold text-slate-400 bg-slate-100 px-2 py-0.5 rounded-md">Maks. 50 MB / Berkas</span>
                            </div>

                            {{-- Hidden native inputs to maintain reliable form submission --}}
                            <input type="file" name="report_files[]" x-ref="pmFileInput1" @change="handlePmFile($event, 1)" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png" class="hidden">
                            <input type="file" name="report_files[]" x-ref="pmFileInput2" @change="handlePmFile($event, 2)" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png" class="hidden">

                            {{-- Symmetrical 2-Column Upload Cards Grid --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                {{-- Slot 1: Berkas Laporan Utama --}}
                                <div class="border rounded-xl p-3 transition flex flex-col justify-between"
                                     :class="pmFile1 ? 'border-emerald-300 bg-emerald-50/40' : 'border-slate-200 bg-slate-50/50 hover:border-[#8F0A0D]/50 hover:bg-red-50/20'">
                                    
                                    {{-- Slot 1 Header --}}
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center gap-1.5 min-w-0">
                                            <span class="w-5 h-5 rounded-md bg-[#8F0A0D] text-white flex items-center justify-center text-[10px] font-extrabold shadow-2xs shrink-0">1</span>
                                            <span class="text-[11.5px] font-bold text-slate-800 truncate">Berkas Utama</span>
                                        </div>
                                        <span class="text-[9.5px] font-semibold px-1.5 py-0.5 rounded shrink-0" :class="pmFile1 ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-500'">
                                            Laporan PM
                                        </span>
                                    </div>

                                    {{-- Slot 1 Empty / Drop state --}}
                                    <div x-show="!pmFile1" 
                                         @click="$refs.pmFileInput1.click()" 
                                         class="h-[84px] border-2 border-dashed border-slate-200 hover:border-[#8F0A0D] rounded-xl p-2.5 flex flex-col items-center justify-center text-center cursor-pointer transition bg-white/80 hover:bg-white group">
                                        <div class="w-7 h-7 mb-1 rounded-lg bg-red-50 text-[#8F0A0D] group-hover:scale-110 flex items-center justify-center transition shrink-0">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                        </div>
                                        <p class="text-[11px] font-bold text-slate-700 group-hover:text-[#8F0A0D]">Pilih Berkas 1</p>
                                        <p class="text-[9.5px] text-slate-400">PDF / Dokumen PM</p>
                                    </div>

                                    {{-- Slot 1 Selected state --}}
                                    <div x-show="pmFile1" class="h-[84px] p-2.5 rounded-xl bg-white border border-emerald-200 shadow-2xs flex items-center justify-between">
                                        <div class="flex items-center gap-2 min-w-0 pr-1">
                                            <div class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            </div>
                                            <div class="truncate">
                                                <p class="text-[11px] font-bold text-slate-800 truncate" x-text="pmFile1 ? pmFile1.name : ''"></p>
                                                <p class="text-[9.5px] text-emerald-600 font-semibold" x-text="pmFile1 ? (pmFile1.size + ' • Siap') : ''"></p>
                                            </div>
                                        </div>
                                        <button type="button" @click.stop="clearPmFile(1)" class="w-6 h-6 rounded-md hover:bg-red-50 text-slate-400 hover:text-red-600 flex items-center justify-center transition cursor-pointer text-base leading-none font-bold shrink-0" title="Hapus Berkas 1">&times;</button>
                                    </div>
                                </div>

                                {{-- Slot 2: Berkas Pendukung / BAP / Foto --}}
                                <div class="border rounded-xl p-3 transition flex flex-col justify-between"
                                     :class="pmFile2 ? 'border-emerald-300 bg-emerald-50/40' : 'border-slate-200 bg-slate-50/50 hover:border-blue-500/50 hover:bg-blue-50/20'">
                                    
                                    {{-- Slot 2 Header --}}
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center gap-1.5 min-w-0">
                                            <span class="w-5 h-5 rounded-md bg-[#2563EB] text-white flex items-center justify-center text-[10px] font-extrabold shadow-2xs shrink-0">2</span>
                                            <span class="text-[11.5px] font-bold text-slate-800 truncate">Berkas Lampiran</span>
                                        </div>
                                        <span class="text-[9.5px] font-semibold px-1.5 py-0.5 rounded shrink-0" :class="pmFile2 ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-500'">
                                            BAP / Foto
                                        </span>
                                    </div>

                                    {{-- Slot 2 Empty / Drop state --}}
                                    <div x-show="!pmFile2" 
                                         @click="$refs.pmFileInput2.click()" 
                                         class="h-[84px] border-2 border-dashed border-slate-200 hover:border-blue-500 rounded-xl p-2.5 flex flex-col items-center justify-center text-center cursor-pointer transition bg-white/80 hover:bg-white group">
                                        <div class="w-7 h-7 mb-1 rounded-lg bg-blue-50 text-blue-600 group-hover:scale-110 flex items-center justify-center transition shrink-0">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                        </div>
                                        <p class="text-[11px] font-bold text-slate-700 group-hover:text-blue-600">Pilih Berkas 2</p>
                                        <p class="text-[9.5px] text-slate-400">BAP / Foto / Arsip</p>
                                    </div>

                                    {{-- Slot 2 Selected state --}}
                                    <div x-show="pmFile2" class="h-[84px] p-2.5 rounded-xl bg-white border border-emerald-200 shadow-2xs flex items-center justify-between">
                                        <div class="flex items-center gap-2 min-w-0 pr-1">
                                            <div class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            </div>
                                            <div class="truncate">
                                                <p class="text-[11px] font-bold text-slate-800 truncate" x-text="pmFile2 ? pmFile2.name : ''"></p>
                                                <p class="text-[9.5px] text-emerald-600 font-semibold" x-text="pmFile2 ? (pmFile2.size + ' • Siap') : ''"></p>
                                            </div>
                                        </div>
                                        <button type="button" @click.stop="clearPmFile(2)" class="w-6 h-6 rounded-md hover:bg-red-50 text-slate-400 hover:text-red-600 flex items-center justify-center transition cursor-pointer text-base leading-none font-bold shrink-0" title="Hapus Berkas 2">&times;</button>
                                    </div>
                                </div>
                            </div>
                            <p class="text-[10px] text-slate-400">Tips: Anda dapat memilih 1 atau 2 berkas sekaligus sebelum mengklik tombol Simpan Realisasi PM.</p>
                        </div>

                        {{-- View Dokumen Laporan yang Sudah Tersimpan --}}
                        <template x-if="activeCycle && ((activeCycle.all_report_files && activeCycle.all_report_files.length > 0) || activeCycle.report_file)">
                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-[11px] font-bold text-slate-700 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        <span>Berkas Laporan Tersimpan</span>
                                    </span>
                                    <span class="text-[10px] font-bold text-emerald-700 bg-emerald-100/60 px-2 py-0.5 rounded-full" x-text="(activeCycle.all_report_files ? activeCycle.all_report_files.length : (activeCycle.report_file ? 1 : 0)) + ' Berkas'"></span>
                                </div>

                                <div class="space-y-1.5 max-h-36 overflow-y-auto pr-1">
                                    <template x-for="(file, fIdx) in (activeCycle.all_report_files || (activeCycle.report_file ? [{path: activeCycle.report_file, filename: activeCycle.report_filename || 'Laporan_PM.pdf', uploaded_at: ''}] : []))" :key="fIdx">
                                        <div class="flex items-center justify-between p-2 rounded-lg bg-white border border-slate-200 text-xs">
                                            <div class="flex items-center gap-2 min-w-0 pr-2">
                                                <svg class="w-4 h-4 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                                <div class="truncate">
                                                    <p class="font-bold text-slate-800 text-[11px] truncate" x-text="file.filename"></p>
                                                    <p class="text-[9.5px] text-slate-400" x-text="file.uploaded_at ? ('Diupload: ' + file.uploaded_at) : 'Tersimpan'"></p>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-1.5 shrink-0">
                                                <a :href="'/storage/' + file.path" target="_blank" class="inline-flex items-center gap-1 px-2 py-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 rounded-md text-[10.5px] font-bold border border-emerald-200 transition cursor-pointer" title="Lihat Berkas (Preview)">
                                                    <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                    <span>Lihat</span>
                                                </a>
                                                <a :href="'/storage/' + file.path" :download="file.filename || 'Laporan_PM.pdf'" class="inline-flex items-center gap-1 px-2 py-1 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-md text-[10.5px] font-bold border border-blue-200 transition cursor-pointer" title="Unduh Berkas (Download)">
                                                    <svg class="w-3 h-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                    <span>Unduh</span>
                                                </a>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>

                        <div>
                            <label class="block text-xs font-semibold text-[#334155] mb-1">Status Laporan PM</label>
                            <select name="report_status" :value="activeCycle ? activeCycle.report_status : 'Pending'" class="w-full px-3 py-2 bg-white border border-[#CBD5E1] rounded-xl text-xs text-[#1E293B] outline-none focus:border-[#8F0A0D] cursor-pointer font-semibold">
                                <option value="Pending">Pending (Belum Diupload)</option>
                                <option value="Submitted">Submitted (Menunggu Review)</option>
                                <option value="Approved">Approved (Disetujui Klien)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-[#334155] mb-1">Catatan Pelaksanaan Inspeksi</label>
                            <textarea name="notes" rows="2" :value="activeCycle ? (activeCycle.activity_notes || activeCycle.notes) : ''" placeholder="Catatan temuan teknis saat kunjungan..." class="w-full px-3 py-2 border border-[#CBD5E1] rounded-xl text-xs text-[#1E293B] outline-none focus:border-[#8F0A0D]"></textarea>
                        </div>
                    </div>

                    {{-- Fixed Footer (Always visible, does NOT scroll) --}}
                    <div class="flex items-center justify-end gap-2.5 px-6 py-3.5 border-t border-[#E2E8F0] bg-[#F8FAFC] shrink-0">
                        <button type="button" @click="isPmModalOpen = false" class="px-4 py-2 rounded-xl text-xs font-semibold text-[#64748B] hover:bg-slate-200 transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" :disabled="isSavingPm" class="btn-ipnet-gradient px-4 py-2 rounded-xl text-xs font-bold shadow-md text-white cursor-pointer disabled:opacity-50">
                            Simpan Realisasi PM
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>

    {{-- POP UP LOADING SEDANG MENYIMPAN (Sesuai Permintaan User: Muter rapih seperti digital signature) --}}
    <template x-teleport="body">
        <div x-show="isSavingPm" x-cloak class="fixed inset-0 z-[100] bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl p-7 max-w-sm w-full shadow-2xl border border-slate-100 flex flex-col items-center text-center space-y-4">
                <div class="relative w-16 h-16 flex items-center justify-center">
                    <div class="absolute inset-0 rounded-full border-4 border-slate-100"></div>
                    <div class="absolute inset-0 rounded-full border-4 border-[#8F0A0D] border-t-transparent animate-spin"></div>
                    <svg class="w-7 h-7 text-[#8F0A0D]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                </div>
                <div class="space-y-1">
                    <h4 class="text-sm font-bold text-[#1E293B]">Sedang Menyimpan Realisasi PM...</h4>
                    <p class="text-xs text-[#64748B]">Mohon tunggu sebentar, sistem sedang memproses data siklus dan mengunggah berkas laporan.</p>
                </div>
            </div>
        </div>
    </template>

    {{-- POP UP LOADING SEDANG MENYIMPAN TIKET CM --}}
    <template x-teleport="body">
        <div x-show="isSavingCm" x-cloak class="fixed inset-0 z-[100] bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl p-7 max-w-sm w-full shadow-2xl border border-slate-100 flex flex-col items-center text-center space-y-4">
                <div class="relative w-16 h-16 flex items-center justify-center">
                    <div class="absolute inset-0 rounded-full border-4 border-slate-100"></div>
                    <div class="absolute inset-0 rounded-full border-4 border-[#8F0A0D] border-t-transparent animate-spin"></div>
                    <svg class="w-7 h-7 text-[#8F0A0D]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="space-y-1">
                    <h4 class="text-sm font-bold text-[#1E293B]">Sedang Menyimpan Tiket CM...</h4>
                    <p class="text-xs text-[#64748B]">Mohon tunggu sebentar, sistem sedang memproses tiket corrective maintenance dan memperbarui status layanan.</p>
                </div>
            </div>
        </div>
    </template>

    {{-- POP UP LOADING SEDANG MENYIMPAN PARAMETER SLA --}}
    <template x-teleport="body">
        <div x-show="isSavingSla" x-cloak class="fixed inset-0 z-[100] bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl p-7 max-w-sm w-full shadow-2xl border border-slate-100 flex flex-col items-center text-center space-y-4">
                <div class="relative w-16 h-16 flex items-center justify-center">
                    <div class="absolute inset-0 rounded-full border-4 border-slate-100"></div>
                    <div class="absolute inset-0 rounded-full border-4 border-[#8F0A0D] border-t-transparent animate-spin"></div>
                    <svg class="w-7 h-7 text-[#8F0A0D]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <div class="space-y-1">
                    <h4 class="text-sm font-bold text-[#1E293B]">Sedang Memperbarui Parameter SLA...</h4>
                    <p class="text-xs text-[#64748B]">Mohon tunggu sebentar, sistem sedang mengupdate target SLA, matriks kuota tiket, dan jadwal pemeliharaan.</p>
                </div>
            </div>
        </div>
    </template>

    <!-- ========================================================== -->
    <!-- MODAL 3: CATAT TIKET CM                                    -->
    <!-- ========================================================== -->
    <template x-teleport="body">
        <div x-show="isCmModalOpen" x-cloak class="fixed inset-0 z-50 bg-[#0E0D12]/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-lg w-full shadow-2xl border border-[#E2E8F0] max-h-[90vh] flex flex-col overflow-hidden" @click.away="isCmModalOpen = false">
                <div class="flex items-center justify-between border-b border-[#E2E8F0] px-6 py-4 shrink-0 bg-white">
                    <div>
                        <p class="text-[10.5px] font-bold text-[#8F0A0D] uppercase tracking-wider">Tiket Insiden / Perbaikan</p>
                        <h3 class="text-[15px] font-bold text-[#1E293B] flex items-center gap-1.5">
                            <span>Catat Tiket Corrective Maintenance</span>
                            <span x-show="selectedCmSlot" class="text-xs font-semibold text-[#8F0A0D]" x-text="'(Slot #' + selectedCmSlot + ')'"></span>
                        </h3>
                    </div>
                    <button @click="isCmModalOpen = false" class="text-slate-400 hover:text-slate-700 p-1 rounded-lg text-lg font-bold leading-none">&times;</button>
                </div>

                <form action="{{ route('ms.monitoring.cm.store') }}" method="POST" enctype="multipart/form-data" @submit="isSavingCm = true" class="flex flex-col flex-1 min-h-0 overflow-hidden">
                    @csrf
                    <input type="hidden" name="project_id" value="{{ $project->id }}">

                    <div class="p-6 overflow-y-auto space-y-4 flex-1">
                        <div>
                            <label class="block text-xs font-semibold text-[#334155] mb-1">Judul Insiden / Aktivitas CM <span class="text-[#8F0A0D]">*</span></label>
                            <input type="text" name="title" required placeholder="Contoh: Penggantian SFP Module Port 2 Core Switch" class="w-full px-3 py-2 bg-white border border-[#CBD5E1] rounded-xl text-xs text-[#1E293B] outline-none focus:border-[#8F0A0D]">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <div>
                                <label class="block text-xs font-semibold text-[#334155] mb-1">Prioritas & SLA <span class="text-[#8F0A0D]">*</span></label>
                                <select name="priority" required class="w-full px-3 py-2 bg-white border border-[#CBD5E1] rounded-xl text-xs text-[#1E293B] outline-none focus:border-[#8F0A0D] cursor-pointer">
                                    <option value="Critical">Critical (P1 - MTTR 1 Jam)</option>
                                    <option value="High" selected>High (P2 - MTTR 4 Jam)</option>
                                    <option value="Medium">Medium (P3 - MTTR 8 Jam)</option>
                                    <option value="Low">Low (P4 - MTTR 24 Jam)</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-[#334155] mb-1">Tanggal Mulai Insiden / Pelaksanaan <span class="text-[#8F0A0D]">*</span></label>
                                <input type="date" name="incident_date" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 bg-white border border-[#CBD5E1] rounded-xl text-xs font-bold text-[#1E293B] outline-none focus:border-[#8F0A0D]">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <div>
                                <label class="block text-xs font-semibold text-[#334155] mb-1">Nama Pelapor / PIC Klien</label>
                                <input type="text" name="reported_by" value="{{ $project->customer_pic_technical ?: ($project->client ?: '') }}" placeholder="Nama PIC" class="w-full px-3 py-2 bg-white border border-[#CBD5E1] rounded-xl text-xs text-[#1E293B] outline-none focus:border-[#8F0A0D]">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-[#334155] mb-1">Kontak Telepon / WA</label>
                                <input type="text" name="contact_phone" placeholder="08xxxxxxxxxx" class="w-full px-3 py-2 bg-white border border-[#CBD5E1] rounded-xl text-xs text-[#1E293B] outline-none focus:border-[#8F0A0D]">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <div>
                                <label class="block text-xs font-semibold text-[#334155] mb-1">PIC Managed Service</label>
                                <select name="assigned_to" class="w-full px-3 py-2 bg-white border border-[#CBD5E1] rounded-xl text-xs text-[#1E293B] outline-none focus:border-[#8F0A0D] cursor-pointer">
                                    <option value="">Pilih PIC Managed Service</option>
                                    @foreach($engineers as $eng)
                                        <option value="{{ $eng->id }}">{{ $eng->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-[#334155] mb-1">Status Tiket</label>
                                <select name="status" class="w-full px-3 py-2 bg-white border border-[#CBD5E1] rounded-xl text-xs text-[#1E293B] outline-none focus:border-[#8F0A0D] cursor-pointer">
                                    <option value="In Progress" selected>In Progress (Sedang Ditangani)</option>
                                    <option value="Resolved">Resolved (Selesai Penanganan)</option>
                                    <option value="Open">Open (Baru Diterima)</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-[#334155] mb-1">Rincian Gangguan / Masalah</label>
                            <textarea name="description" rows="2" placeholder="Jelaskan indikasi error atau alasan kebutuhan corrective..." class="w-full px-3 py-2 border border-[#CBD5E1] rounded-xl text-xs text-[#1E293B] outline-none focus:border-[#8F0A0D]"></textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-[#334155] mb-1">Upload Berita Acara Perbaikan (BAP / Foto Bukti)</label>
                            <input type="file" name="attachment" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" class="w-full px-3 py-2 border border-[#CBD5E1] rounded-xl text-xs bg-white outline-none focus:border-[#8F0A0D]">
                        </div>
                    </div>

                    {{-- Fixed Footer --}}
                    <div class="flex items-center justify-end gap-2.5 px-6 py-3.5 border-t border-[#E2E8F0] bg-[#F8FAFC] shrink-0">
                        <button type="button" @click="isCmModalOpen = false" class="px-4 py-2 rounded-xl text-xs font-semibold text-[#64748B] hover:bg-slate-200 transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" :disabled="isSavingCm" class="btn-ipnet-gradient px-4 py-2 rounded-xl text-xs font-bold shadow-md text-white cursor-pointer disabled:opacity-50">
                            Catat Tiket CM
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>

    <!-- ========================================================== -->
    <!-- MODAL 3B: DETAIL & UPDATE TIKET CM (KLIK NODE TIKET / AKSI)-->
    <!-- ========================================================== -->
    <template x-teleport="body">
        <div x-show="isCmDetailModalOpen" x-cloak class="fixed inset-0 z-50 bg-[#0E0D12]/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-lg w-full shadow-2xl border border-[#E2E8F0] max-h-[90vh] flex flex-col overflow-hidden" @click.away="isCmDetailModalOpen = false">
                <div class="flex items-center justify-between border-b border-[#E2E8F0] px-6 py-4 shrink-0 bg-white">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-[10.5px] font-bold text-[#8F0A0D] uppercase tracking-wider">Detail &amp; Update Tiket CM</span>
                            <span class="font-mono text-xs font-bold text-slate-700 bg-slate-100 px-2 py-0.5 rounded border border-slate-200" x-text="activeCmTicket ? activeCmTicket.ticket_number : ''"></span>
                        </div>
                        <h3 class="text-[15px] font-bold text-[#1E293B] mt-0.5" x-text="activeCmTicket ? activeCmTicket.title : 'Tiket CM'"></h3>
                    </div>
                    <button @click="isCmDetailModalOpen = false" class="text-slate-400 hover:text-slate-700 p-1 rounded-lg text-lg font-bold leading-none">&times;</button>
                </div>

                <form :action="'/managed-service/monitoring/cm-tickets/' + (activeCmTicket ? activeCmTicket.id : '') + '/update'" method="POST" enctype="multipart/form-data" @submit="isSavingCm = true" class="flex flex-col flex-1 min-h-0 overflow-hidden">
                    @csrf

                    <div class="p-6 overflow-y-auto space-y-4 flex-1">
                        <div>
                            <label class="block text-xs font-semibold text-[#334155] mb-1">Judul Insiden / Aktivitas CM <span class="text-[#8F0A0D]">*</span></label>
                            <input type="text" name="title" :value="activeCmTicket ? activeCmTicket.title : ''" required class="w-full px-3 py-2 bg-white border border-[#CBD5E1] rounded-xl text-xs text-[#1E293B] outline-none focus:border-[#8F0A0D]">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <div>
                                <label class="block text-xs font-semibold text-[#334155] mb-1">Status Tiket <span class="text-[#8F0A0D]">*</span></label>
                                <select name="status" :value="activeCmTicket ? activeCmTicket.status : 'Resolved'" required class="w-full px-3 py-2 bg-white border border-[#CBD5E1] rounded-xl text-xs font-bold text-[#1E293B] outline-none focus:border-[#8F0A0D] cursor-pointer">
                                    <option value="In Progress">In Progress (Sedang Ditangani)</option>
                                    <option value="Resolved">Resolved (Selesai Perbaikan)</option>
                                    <option value="Closed">Closed (Ditutup Resmi)</option>
                                    <option value="Open">Open (Baru Diterima)</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-[#334155] mb-1">Prioritas &amp; SLA <span class="text-[#8F0A0D]">*</span></label>
                                <select name="priority" :value="activeCmTicket ? activeCmTicket.priority : 'High'" required class="w-full px-3 py-2 bg-white border border-[#CBD5E1] rounded-xl text-xs text-[#1E293B] outline-none focus:border-[#8F0A0D] cursor-pointer">
                                    <option value="Critical">Critical (P1 - MTTR 1 Jam)</option>
                                    <option value="High">High (P2 - MTTR 4 Jam)</option>
                                    <option value="Medium">Medium (P3 - MTTR 8 Jam)</option>
                                    <option value="Low">Low (P4 - MTTR 24 Jam)</option>
                                </select>
                            </div>
                        </div>

                        {{-- Tanggal Mulai & Tanggal Selesai Restorasi --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <div>
                                <label class="block text-xs font-semibold text-[#334155] mb-1">
                                    Tanggal Mulai / Insiden <span class="text-[#8F0A0D]">*</span>
                                </label>
                                <input type="date" name="incident_date" :value="activeCmTicket ? (activeCmTicket.incident_date ? String(activeCmTicket.incident_date).split('T')[0] : (activeCmTicket.created_at ? String(activeCmTicket.created_at).split('T')[0] : '')) : ''" required class="w-full px-3 py-2 bg-white border border-[#CBD5E1] rounded-xl text-xs font-bold text-[#1E293B] outline-none focus:border-[#8F0A0D]">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-[#334155] mb-1">
                                    Tanggal Selesai Restorasi
                                </label>
                                <input type="date" name="resolved_at" :value="activeCmTicket ? (activeCmTicket.resolved_at ? String(activeCmTicket.resolved_at).split('T')[0] : '') : ''" class="w-full px-3 py-2 bg-white border border-[#CBD5E1] rounded-xl text-xs text-[#1E293B] outline-none focus:border-[#8F0A0D]">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <div>
                                <label class="block text-xs font-semibold text-[#334155] mb-1">PIC Managed Service</label>
                                <select name="assigned_to" :value="activeCmTicket ? activeCmTicket.assigned_to : ''" class="w-full px-3 py-2 bg-white border border-[#CBD5E1] rounded-xl text-xs text-[#1E293B] outline-none focus:border-[#8F0A0D] cursor-pointer">
                                    <option value="">Pilih PIC Managed Service</option>
                                    @foreach($engineers as $eng)
                                        <option value="{{ $eng->id }}">{{ $eng->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-[#334155] mb-1">Pelapor / Kontak Klien</label>
                                <input type="text" name="reported_by" :value="activeCmTicket ? activeCmTicket.reported_by : ''" placeholder="Nama Pelapor" class="w-full px-3 py-2 bg-white border border-[#CBD5E1] rounded-xl text-xs text-[#1E293B] outline-none focus:border-[#8F0A0D]">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-[#334155] mb-1">Catatan Tindakan / Resolusi</label>
                            <textarea name="resolution_notes" rows="2" :value="activeCmTicket ? activeCmTicket.resolution_notes : ''" placeholder="Catatan langkah perbaikan insiden..." class="w-full px-3 py-2 border border-[#CBD5E1] rounded-xl text-xs text-[#1E293B] outline-none focus:border-[#8F0A0D]"></textarea>
                        </div>

                        {{-- Upload / View BAP Berkas --}}
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="text-xs font-semibold text-[#334155]">Upload Berkas BAP (PDF / Foto Bukti)</label>
                                <template x-if="activeCmTicket && activeCmTicket.attachment_path">
                                    <a :href="'/storage/' + activeCmTicket.attachment_path" target="_blank" class="text-[11px] font-bold text-emerald-700 hover:underline flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        <span>Lihat Berkas BAP Terunggah</span>
                                    </a>
                                </template>
                            </div>
                            <input type="file" name="attachment" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" class="w-full px-3 py-2 border border-[#CBD5E1] rounded-xl text-xs bg-white outline-none focus:border-[#8F0A0D]">
                        </div>
                    </div>

                    {{-- Fixed Footer --}}
                    <div class="flex items-center justify-end gap-2.5 px-6 py-3.5 border-t border-[#E2E8F0] bg-[#F8FAFC] shrink-0">
                        <button type="button" @click="isCmDetailModalOpen = false" class="px-4 py-2 rounded-xl text-xs font-semibold text-[#64748B] hover:bg-slate-200 transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" :disabled="isSavingCm" class="btn-ipnet-gradient px-4 py-2 rounded-xl text-xs font-bold shadow-md text-white cursor-pointer disabled:opacity-50">
                            Simpan Perubahan Tiket
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>

    <!-- ========================================================== -->
    <!-- MODAL 4: UBAH PARAMETER SLA & KUOTA                        -->
    <!-- ========================================================== -->
    <template x-teleport="body">
        <div x-show="isSlaModalOpen" x-cloak class="fixed inset-0 z-50 bg-[#0E0D12]/60 backdrop-blur-xs flex items-start justify-center p-4 pt-6 sm:pt-10 overflow-y-auto">
            <div class="bg-white rounded-2xl max-w-xl w-full p-6 shadow-2xl border border-[#E2E8F0] max-h-[90vh] overflow-y-auto" @click.away="isSlaModalOpen = false">
                <div class="flex items-center justify-between border-b border-[#E2E8F0] pb-3 mb-4">
                    <div>
                        <p class="text-[10.5px] font-bold text-[#8F0A0D] uppercase tracking-wider">Konfigurasi Kontrak</p>
                        <h3 class="text-[15px] font-bold text-[#1E293B]">Ubah Parameter SLA & Kuota Tiket</h3>
                    </div>
                    <button @click="isSlaModalOpen = false" class="text-slate-400 hover:text-slate-700 p-1 rounded-lg text-lg font-bold">&times;</button>
                </div>

                <form action="{{ route('ms.projects.sla.update', $project->id) }}" method="POST" @submit="isSavingSla = true" class="space-y-4">
                    @csrf
                    @method('PUT')

                    {{-- Tanggal Mulai (Start) & Tanggal Berakhir (Finish) Kontrak Layanan --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 bg-slate-50 p-3.5 rounded-xl border border-slate-200">
                        <div>
                            <label class="block text-xs font-bold text-[#1E293B] mb-1 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                <span>Tanggal Mulai Layanan (Start)</span> <span class="text-[#8F0A0D]">*</span>
                            </label>
                            <input type="date" name="service_start_date" 
                                   value="{{ $project->service_start_date ? \Carbon\Carbon::parse($project->service_start_date)->format('Y-m-d') : ($project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('Y-m-d') : '') }}" 
                                   required class="w-full px-3 py-2 bg-white border border-[#CBD5E1] rounded-xl text-xs font-bold text-[#1E293B] outline-none focus:border-[#8F0A0D]">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-[#1E293B] mb-1 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-red-500"></span>
                                <span>Tanggal Berakhir Layanan (Finish)</span> <span class="text-[#8F0A0D]">*</span>
                            </label>
                            <input type="date" name="service_end_date" 
                                   value="{{ $project->service_end_date ? \Carbon\Carbon::parse($project->service_end_date)->format('Y-m-d') : ($project->deadline ? \Carbon\Carbon::parse($project->deadline)->format('Y-m-d') : '') }}" 
                                   required class="w-full px-3 py-2 bg-white border border-[#CBD5E1] rounded-xl text-xs font-bold text-[#1E293B] outline-none focus:border-[#8F0A0D]">
                        </div>
                    </div>

                    @php
                        $cmLabels = [
                            '4'   => '4 Tiket / 1 Tahun',
                            '6'   => '6 Tiket / 1 Tahun',
                            '8'   => '8 Tiket / 1 Tahun',
                            '12'  => '12 Tiket / 1 Tahun',
                            '18'  => '18 Tiket / 1 Tahun (Standar)',
                            '16'  => '16 Tiket / 2 Tahun',
                            '24'  => '24 Tiket / 2 Tahun',
                            '36'  => '36 Tiket / 2 Tahun (Standar 2 Tahun)',
                            '54'  => '54 Tiket / 3 Tahun (Standar 3 Tahun)',
                            '999' => 'Unlimited Tiket',
                        ];
                        $initialCmVal = (string)($project->cm_quota ?? '18');
                        $initialCmText = $cmLabels[$initialCmVal] ?? ($initialCmVal . ' Tiket');

                        $pmLabels = [
                            '1_thn_1_bln'  => '1 Tahun - Setiap Bulan (12x)',
                            'per_bulan'    => '1 Tahun - Setiap Bulan (12x)',
                            '1_thn_2_bln'  => '1 Tahun - Per 2 Bulan (6x)',
                            'per_2_bulan'  => '1 Tahun - Per 2 Bulan (6x)',
                            '1_thn_3_bln'  => '1 Tahun - Per 3 Bulan (4x)',
                            'per_3_bulan'  => '1 Tahun - Per 3 Bulan (4x)',
                            '1_thn_4_bln'  => '1 Tahun - Per 4 Bulan (3x)',
                            'per_4_bulan'  => '1 Tahun - Per 4 Bulan (3x)',
                            '1_thn_6_bln'  => '1 Tahun - Per 6 Bulan (2x)',
                            'per_6_bulan'  => '1 Tahun - Per 6 Bulan (2x)',
                            '1_thn_12_bln' => '1 Tahun - Per 12 Bulan (1x)',
                            'tahunan'      => '1 Tahun - Per 12 Bulan (1x)',
                            '2_thn_1_bln'  => '2 Tahun - Setiap Bulan (24x)',
                            '2_thn_2_bln'  => '2 Tahun - Per 2 Bulan (12x)',
                            '2_thn_3_bln'  => '2 Tahun - Per 3 Bulan (8x)',
                            '2_thn_4_bln'  => '2 Tahun - Per 4 Bulan (6x)',
                            '2_thn_6_bln'  => '2 Tahun - Per 6 Bulan (4x)',
                            '2_thn_12_bln' => '2 Tahun - Per 12 Bulan (2x)',
                            '3_thn_1_bln'  => '3 Tahun - Setiap Bulan (36x)',
                            '3_thn_2_bln'  => '3 Tahun - Per 2 Bulan (18x)',
                            '3_thn_3_bln'  => '3 Tahun - Per 3 Bulan (12x)',
                            '3_thn_4_bln'  => '3 Tahun - Per 4 Bulan (9x)',
                            '3_thn_6_bln'  => '3 Tahun - Per 6 Bulan (6x)',
                            '3_thn_12_bln' => '3 Tahun - Per 12 Bulan (3x)',
                        ];
                        $initialPmVal = $project->pm_frequency_type ?? '1_thn_3_bln';
                        $initialPmText = $pmLabels[$initialPmVal] ?? '1 Tahun - Per 3 Bulan (4x)';
                    @endphp

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5"
                         x-data="{
                             cmOpen: false,
                             selectedCm: '{{ $initialCmVal }}',
                             selectedCmText: '{{ $initialCmText }}',
                             pmOpen: false,
                             selectedPm: '{{ $initialPmVal }}',
                             selectedPmText: '{{ $initialPmText }}'
                         }">

                        {{-- Kuota CM Custom Dropdown: Buka ke BAWAH selalu --}}
                        <div class="relative">
                            <label class="block text-xs font-semibold text-[#334155] mb-1">Kuota Corrective Maintenance (CM) <span class="text-[#8F0A0D]">*</span></label>
                            <input type="hidden" name="cm_quota" :value="selectedCm">
                            <button type="button" 
                                    @click="cmOpen = !cmOpen; pmOpen = false" 
                                    class="w-full px-3 py-2 bg-white border border-[#CBD5E1] rounded-xl text-xs font-bold text-[#1E293B] flex items-center justify-between text-left focus:border-[#8F0A0D] transition shadow-2xs cursor-pointer">
                                <span class="truncate" x-text="selectedCmText"></span>
                                <svg class="w-4 h-4 text-slate-400 shrink-0 ml-1 transition-transform" :class="cmOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>

                            {{-- Menu Dropdown Selalu Muncul di Bawah (top-full mt-1.5) --}}
                            <div x-show="cmOpen" 
                                 @click.outside="cmOpen = false" 
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="opacity-0 translate-y-1"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="opacity-100 translate-y-0"
                                 x-transition:leave-end="opacity-0 translate-y-1"
                                 class="absolute left-0 right-0 top-full mt-1 bg-white border border-[#CBD5E1] rounded-xl shadow-xl z-50 max-h-56 overflow-y-auto py-1 text-xs">
                                
                                <div class="px-3 py-1 font-bold text-[10px] uppercase tracking-wider text-slate-400 bg-slate-50">Periode Kontrak 1 Tahun</div>
                                <div @click="selectedCm = 4; selectedCmText = '4 Tiket / 1 Tahun'; cmOpen = false" :class="selectedCm == 4 ? 'bg-red-50 text-[#8F0A0D] font-bold' : 'text-slate-700 hover:bg-slate-50'" class="px-3 py-2 cursor-pointer transition">4 Tiket / 1 Tahun</div>
                                <div @click="selectedCm = 6; selectedCmText = '6 Tiket / 1 Tahun'; cmOpen = false" :class="selectedCm == 6 ? 'bg-red-50 text-[#8F0A0D] font-bold' : 'text-slate-700 hover:bg-slate-50'" class="px-3 py-2 cursor-pointer transition">6 Tiket / 1 Tahun</div>
                                <div @click="selectedCm = 8; selectedCmText = '8 Tiket / 1 Tahun'; cmOpen = false" :class="selectedCm == 8 ? 'bg-red-50 text-[#8F0A0D] font-bold' : 'text-slate-700 hover:bg-slate-50'" class="px-3 py-2 cursor-pointer transition">8 Tiket / 1 Tahun</div>
                                <div @click="selectedCm = 12; selectedCmText = '12 Tiket / 1 Tahun'; cmOpen = false" :class="selectedCm == 12 ? 'bg-red-50 text-[#8F0A0D] font-bold' : 'text-slate-700 hover:bg-slate-50'" class="px-3 py-2 cursor-pointer transition">12 Tiket / 1 Tahun</div>
                                <div @click="selectedCm = 18; selectedCmText = '18 Tiket / 1 Tahun (Standar)'; cmOpen = false" :class="selectedCm == 18 ? 'bg-red-50 text-[#8F0A0D] font-bold' : 'text-slate-700 hover:bg-slate-50'" class="px-3 py-2 cursor-pointer transition">18 Tiket / 1 Tahun (Standar)</div>

                                <div class="px-3 py-1 font-bold text-[10px] uppercase tracking-wider text-slate-400 bg-slate-50 mt-1">Periode Kontrak 2 Tahun</div>
                                <div @click="selectedCm = 8; selectedCmText = '8 Tiket / 2 Tahun'; cmOpen = false" :class="selectedCm == 8 ? 'bg-red-50 text-[#8F0A0D] font-bold' : 'text-slate-700 hover:bg-slate-50'" class="px-3 py-2 cursor-pointer transition">8 Tiket / 2 Tahun</div>
                                <div @click="selectedCm = 12; selectedCmText = '12 Tiket / 2 Tahun'; cmOpen = false" :class="selectedCm == 12 ? 'bg-red-50 text-[#8F0A0D] font-bold' : 'text-slate-700 hover:bg-slate-50'" class="px-3 py-2 cursor-pointer transition">12 Tiket / 2 Tahun</div>
                                <div @click="selectedCm = 16; selectedCmText = '16 Tiket / 2 Tahun'; cmOpen = false" :class="selectedCm == 16 ? 'bg-red-50 text-[#8F0A0D] font-bold' : 'text-slate-700 hover:bg-slate-50'" class="px-3 py-2 cursor-pointer transition">16 Tiket / 2 Tahun</div>
                                <div @click="selectedCm = 24; selectedCmText = '24 Tiket / 2 Tahun'; cmOpen = false" :class="selectedCm == 24 ? 'bg-red-50 text-[#8F0A0D] font-bold' : 'text-slate-700 hover:bg-slate-50'" class="px-3 py-2 cursor-pointer transition">24 Tiket / 2 Tahun</div>
                                <div @click="selectedCm = 36; selectedCmText = '36 Tiket / 2 Tahun (Standar 2 Tahun)'; cmOpen = false" :class="selectedCm == 36 ? 'bg-red-50 text-[#8F0A0D] font-bold' : 'text-slate-700 hover:bg-slate-50'" class="px-3 py-2 cursor-pointer transition">36 Tiket / 2 Tahun (Standar 2 Tahun)</div>

                                <div class="px-3 py-1 font-bold text-[10px] uppercase tracking-wider text-slate-400 bg-slate-50 mt-1">Periode Kontrak 3 Tahun</div>
                                <div @click="selectedCm = 12; selectedCmText = '12 Tiket / 3 Tahun'; cmOpen = false" :class="selectedCm == 12 ? 'bg-red-50 text-[#8F0A0D] font-bold' : 'text-slate-700 hover:bg-slate-50'" class="px-3 py-2 cursor-pointer transition">12 Tiket / 3 Tahun</div>
                                <div @click="selectedCm = 18; selectedCmText = '18 Tiket / 3 Tahun'; cmOpen = false" :class="selectedCm == 18 ? 'bg-red-50 text-[#8F0A0D] font-bold' : 'text-slate-700 hover:bg-slate-50'" class="px-3 py-2 cursor-pointer transition">18 Tiket / 3 Tahun</div>
                                <div @click="selectedCm = 24; selectedCmText = '24 Tiket / 3 Tahun'; cmOpen = false" :class="selectedCm == 24 ? 'bg-red-50 text-[#8F0A0D] font-bold' : 'text-slate-700 hover:bg-slate-50'" class="px-3 py-2 cursor-pointer transition">24 Tiket / 3 Tahun</div>
                                <div @click="selectedCm = 36; selectedCmText = '36 Tiket / 3 Tahun (12 Tiket/Tahun)'; cmOpen = false" :class="selectedCm == 36 ? 'bg-red-50 text-[#8F0A0D] font-bold' : 'text-slate-700 hover:bg-slate-50'" class="px-3 py-2 cursor-pointer transition">36 Tiket / 3 Tahun (12 Tiket/Tahun)</div>
                                <div @click="selectedCm = 54; selectedCmText = '54 Tiket / 3 Tahun (Standar 3 Tahun)'; cmOpen = false" :class="selectedCm == 54 ? 'bg-red-50 text-[#8F0A0D] font-bold' : 'text-slate-700 hover:bg-slate-50'" class="px-3 py-2 cursor-pointer transition">54 Tiket / 3 Tahun (Standar 3 Tahun)</div>

                                <div class="px-3 py-1 font-bold text-[10px] uppercase tracking-wider text-slate-400 bg-slate-50 mt-1">Paket Khusus</div>
                                <div @click="selectedCm = 999; selectedCmText = 'Unlimited Tiket'; cmOpen = false" :class="selectedCm == 999 ? 'bg-red-50 text-[#8F0A0D] font-bold' : 'text-slate-700 hover:bg-slate-50'" class="px-3 py-2 cursor-pointer transition">Unlimited Tiket</div>
                            </div>
                        </div>

                        {{-- Frekuensi PM Custom Dropdown: Buka ke BAWAH selalu --}}
                        <div class="relative">
                            <label class="block text-xs font-semibold text-[#334155] mb-1">Frekuensi Preventive Maintenance (PM) <span class="text-[#8F0A0D]">*</span></label>
                            <input type="hidden" name="pm_frequency_type" :value="selectedPm">
                            <button type="button" 
                                    @click="pmOpen = !pmOpen; cmOpen = false" 
                                    class="w-full px-3 py-2 bg-white border border-[#CBD5E1] rounded-xl text-xs font-bold text-[#1E293B] flex items-center justify-between text-left focus:border-[#8F0A0D] transition shadow-2xs cursor-pointer">
                                <span class="truncate" x-text="selectedPmText"></span>
                                <svg class="w-4 h-4 text-slate-400 shrink-0 ml-1 transition-transform" :class="pmOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>

                            {{-- Menu Dropdown Selalu Muncul di Bawah (top-full mt-1.5) --}}
                            <div x-show="pmOpen" 
                                 @click.outside="pmOpen = false" 
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="opacity-0 translate-y-1"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="opacity-100 translate-y-0"
                                 x-transition:leave-end="opacity-0 translate-y-1"
                                 class="absolute left-0 right-0 top-full mt-1 bg-white border border-[#CBD5E1] rounded-xl shadow-xl z-50 max-h-56 overflow-y-auto py-1 text-xs">
                                
                                <div class="px-3 py-1 font-bold text-[10px] uppercase tracking-wider text-slate-400 bg-slate-50">Periode Kontrak 1 Tahun</div>
                                <div @click="selectedPm = '1_thn_1_bln'; selectedPmText = '1 Tahun - Setiap Bulan (12x)'; pmOpen = false" :class="selectedPm == '1_thn_1_bln' ? 'bg-red-50 text-[#8F0A0D] font-bold' : 'text-slate-700 hover:bg-slate-50'" class="px-3 py-2 cursor-pointer transition">1 Tahun - Setiap Bulan (12x)</div>
                                <div @click="selectedPm = '1_thn_2_bln'; selectedPmText = '1 Tahun - Per 2 Bulan (6x)'; pmOpen = false" :class="selectedPm == '1_thn_2_bln' ? 'bg-red-50 text-[#8F0A0D] font-bold' : 'text-slate-700 hover:bg-slate-50'" class="px-3 py-2 cursor-pointer transition">1 Tahun - Per 2 Bulan (6x)</div>
                                <div @click="selectedPm = '1_thn_3_bln'; selectedPmText = '1 Tahun - Per 3 Bulan (4x)'; pmOpen = false" :class="selectedPm == '1_thn_3_bln' ? 'bg-red-50 text-[#8F0A0D] font-bold' : 'text-slate-700 hover:bg-slate-50'" class="px-3 py-2 cursor-pointer transition">1 Tahun - Per 3 Bulan (4x)</div>
                                <div @click="selectedPm = '1_thn_4_bln'; selectedPmText = '1 Tahun - Per 4 Bulan (3x)'; pmOpen = false" :class="selectedPm == '1_thn_4_bln' ? 'bg-red-50 text-[#8F0A0D] font-bold' : 'text-slate-700 hover:bg-slate-50'" class="px-3 py-2 cursor-pointer transition">1 Tahun - Per 4 Bulan (3x)</div>
                                <div @click="selectedPm = '1_thn_6_bln'; selectedPmText = '1 Tahun - Per 6 Bulan (2x)'; pmOpen = false" :class="selectedPm == '1_thn_6_bln' ? 'bg-red-50 text-[#8F0A0D] font-bold' : 'text-slate-700 hover:bg-slate-50'" class="px-3 py-2 cursor-pointer transition">1 Tahun - Per 6 Bulan (2x)</div>
                                <div @click="selectedPm = '1_thn_12_bln'; selectedPmText = '1 Tahun - Per 12 Bulan (1x)'; pmOpen = false" :class="selectedPm == '1_thn_12_bln' ? 'bg-red-50 text-[#8F0A0D] font-bold' : 'text-slate-700 hover:bg-slate-50'" class="px-3 py-2 cursor-pointer transition">1 Tahun - Per 12 Bulan (1x)</div>

                                <div class="px-3 py-1 font-bold text-[10px] uppercase tracking-wider text-slate-400 bg-slate-50 mt-1">Periode Kontrak 2 Tahun</div>
                                <div @click="selectedPm = '2_thn_1_bln'; selectedPmText = '2 Tahun - Setiap Bulan (24x)'; pmOpen = false" :class="selectedPm == '2_thn_1_bln' ? 'bg-red-50 text-[#8F0A0D] font-bold' : 'text-slate-700 hover:bg-slate-50'" class="px-3 py-2 cursor-pointer transition">2 Tahun - Setiap Bulan (24x)</div>
                                <div @click="selectedPm = '2_thn_2_bln'; selectedPmText = '2 Tahun - Per 2 Bulan (12x)'; pmOpen = false" :class="selectedPm == '2_thn_2_bln' ? 'bg-red-50 text-[#8F0A0D] font-bold' : 'text-slate-700 hover:bg-slate-50'" class="px-3 py-2 cursor-pointer transition">2 Tahun - Per 2 Bulan (12x)</div>
                                <div @click="selectedPm = '2_thn_3_bln'; selectedPmText = '2 Tahun - Per 3 Bulan (8x)'; pmOpen = false" :class="selectedPm == '2_thn_3_bln' ? 'bg-red-50 text-[#8F0A0D] font-bold' : 'text-slate-700 hover:bg-slate-50'" class="px-3 py-2 cursor-pointer transition">2 Tahun - Per 3 Bulan (8x)</div>
                                <div @click="selectedPm = '2_thn_4_bln'; selectedPmText = '2 Tahun - Per 4 Bulan (6x)'; pmOpen = false" :class="selectedPm == '2_thn_4_bln' ? 'bg-red-50 text-[#8F0A0D] font-bold' : 'text-slate-700 hover:bg-slate-50'" class="px-3 py-2 cursor-pointer transition">2 Tahun - Per 4 Bulan (6x)</div>
                                <div @click="selectedPm = '2_thn_6_bln'; selectedPmText = '2 Tahun - Per 6 Bulan (4x)'; pmOpen = false" :class="selectedPm == '2_thn_6_bln' ? 'bg-red-50 text-[#8F0A0D] font-bold' : 'text-slate-700 hover:bg-slate-50'" class="px-3 py-2 cursor-pointer transition">2 Tahun - Per 6 Bulan (4x)</div>
                                <div @click="selectedPm = '2_thn_12_bln'; selectedPmText = '2 Tahun - Per 12 Bulan (2x)'; pmOpen = false" :class="selectedPm == '2_thn_12_bln' ? 'bg-red-50 text-[#8F0A0D] font-bold' : 'text-slate-700 hover:bg-slate-50'" class="px-3 py-2 cursor-pointer transition">2 Tahun - Per 12 Bulan (2x)</div>

                                <div class="px-3 py-1 font-bold text-[10px] uppercase tracking-wider text-slate-400 bg-slate-50 mt-1">Periode Kontrak 3 Tahun</div>
                                <div @click="selectedPm = '3_thn_1_bln'; selectedPmText = '3 Tahun - Setiap Bulan (36x)'; pmOpen = false" :class="selectedPm == '3_thn_1_bln' ? 'bg-red-50 text-[#8F0A0D] font-bold' : 'text-slate-700 hover:bg-slate-50'" class="px-3 py-2 cursor-pointer transition">3 Tahun - Setiap Bulan (36x)</div>
                                <div @click="selectedPm = '3_thn_2_bln'; selectedPmText = '3 Tahun - Per 2 Bulan (18x)'; pmOpen = false" :class="selectedPm == '3_thn_2_bln' ? 'bg-red-50 text-[#8F0A0D] font-bold' : 'text-slate-700 hover:bg-slate-50'" class="px-3 py-2 cursor-pointer transition">3 Tahun - Per 2 Bulan (18x)</div>
                                <div @click="selectedPm = '3_thn_3_bln'; selectedPmText = '3 Tahun - Per 3 Bulan (12x)'; pmOpen = false" :class="selectedPm == '3_thn_3_bln' ? 'bg-red-50 text-[#8F0A0D] font-bold' : 'text-slate-700 hover:bg-slate-50'" class="px-3 py-2 cursor-pointer transition">3 Tahun - Per 3 Bulan (12x)</div>
                                <div @click="selectedPm = '3_thn_4_bln'; selectedPmText = '3 Tahun - Per 4 Bulan (9x)'; pmOpen = false" :class="selectedPm == '3_thn_4_bln' ? 'bg-red-50 text-[#8F0A0D] font-bold' : 'text-slate-700 hover:bg-slate-50'" class="px-3 py-2 cursor-pointer transition">3 Tahun - Per 4 Bulan (9x)</div>
                                <div @click="selectedPm = '3_thn_6_bln'; selectedPmText = '3 Tahun - Per 6 Bulan (6x)'; pmOpen = false" :class="selectedPm == '3_thn_6_bln' ? 'bg-red-50 text-[#8F0A0D] font-bold' : 'text-slate-700 hover:bg-slate-50'" class="px-3 py-2 cursor-pointer transition">3 Tahun - Per 6 Bulan (6x)</div>
                                <div @click="selectedPm = '3_thn_12_bln'; selectedPmText = '3 Tahun - Per 12 Bulan (3x)'; pmOpen = false" :class="selectedPm == '3_thn_12_bln' ? 'bg-red-50 text-[#8F0A0D] font-bold' : 'text-slate-700 hover:bg-slate-50'" class="px-3 py-2 cursor-pointer transition">3 Tahun - Per 12 Bulan (3x)</div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div>
                            <label class="block text-xs font-semibold text-[#334155] mb-1">Target SLA Uptime (%) <span class="text-[#8F0A0D]">*</span></label>
                            <input type="number" step="0.01" min="90" max="100" name="sla_target_percentage" value="{{ $project->sla_target_percentage ?: 99.50 }}" required class="w-full px-3 py-2 bg-white border border-[#CBD5E1] rounded-xl text-xs text-[#1E293B] outline-none focus:border-[#8F0A0D]">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-[#334155] mb-1">SLA Tier</label>
                            <select name="sla_tier" class="w-full px-3 py-2 bg-white border border-[#CBD5E1] rounded-xl text-xs font-bold text-[#1E293B] outline-none focus:border-[#8F0A0D] cursor-pointer">
                                <option value="Platinum" {{ ($project->sla_tier ?? 'Gold') == 'Platinum' ? 'selected' : '' }}>Platinum (Mission Critical)</option>
                                <option value="Gold" {{ ($project->sla_tier ?? 'Gold') == 'Gold' ? 'selected' : '' }}>Gold (Standard SLA)</option>
                                <option value="Silver" {{ ($project->sla_tier ?? 'Gold') == 'Silver' ? 'selected' : '' }}>Silver (Basic SLA)</option>
                                <option value="Bronze" {{ ($project->sla_tier ?? 'Gold') == 'Bronze' ? 'selected' : '' }}>Bronze (Best Effort)</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                        <div>
                            <label class="block text-xs font-semibold text-[#334155] mb-1">MTTR (Jam)</label>
                            <input type="number" min="1" max="72" name="mttr_hours" value="{{ $project->mttr_hours ?: 4 }}" class="w-full px-3 py-2 bg-white border border-[#CBD5E1] rounded-xl text-xs text-[#1E293B] outline-none focus:border-[#8F0A0D]">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-[#334155] mb-1">Coverage Hours</label>
                            <input type="text" name="sla_coverage_hours" value="{{ $project->sla_coverage_hours ?: '24x7' }}" placeholder="24x7 atau 8x5" class="w-full px-3 py-2 bg-white border border-[#CBD5E1] rounded-xl text-xs text-[#1E293B] outline-none focus:border-[#8F0A0D]">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-[#334155] mb-1">Call Response</label>
                            <input type="text" name="call_response_time" value="{{ $project->call_response_time ?: '15 menit' }}" placeholder="15 menit" class="w-full px-3 py-2 bg-white border border-[#CBD5E1] rounded-xl text-xs text-[#1E293B] outline-none focus:border-[#8F0A0D]">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#334155] mb-1">Sharing Knowledge / Pelatihan</label>
                        <select name="sharing_knowledge" class="w-full px-3 py-2 bg-white border border-[#CBD5E1] rounded-xl text-xs font-semibold text-[#1E293B] outline-none focus:border-[#8F0A0D] cursor-pointer">
                            <optgroup label="Periode 1 Tahun">
                                <option value="1_thn_1x" {{ ($project->sharing_knowledge ?? '1_thn_1x') == '1_thn_1x' ? 'selected' : '' }}>1 Tahun - 1x Sesi / Tahun</option>
                                <option value="1_thn_2x" {{ ($project->sharing_knowledge ?? '') == '1_thn_2x' ? 'selected' : '' }}>1 Tahun - 2x Sesi / Tahun (Per Semester)</option>
                            </optgroup>
                            <optgroup label="Periode 2 Tahun">
                                <option value="2_thn_1x" {{ ($project->sharing_knowledge ?? '') == '2_thn_1x' ? 'selected' : '' }}>2 Tahun - 1x Sesi / Tahun (Total 2x)</option>
                                <option value="2_thn_2x" {{ ($project->sharing_knowledge ?? '') == '2_thn_2x' ? 'selected' : '' }}>2 Tahun - 2x Sesi / Tahun (Total 4x)</option>
                            </optgroup>
                            <optgroup label="Periode 3 Tahun">
                                <option value="3_thn_1x" {{ ($project->sharing_knowledge ?? '') == '3_thn_1x' ? 'selected' : '' }}>3 Tahun - 1x Sesi / Tahun (Total 3x)</option>
                                <option value="3_thn_2x" {{ ($project->sharing_knowledge ?? '') == '3_thn_2x' ? 'selected' : '' }}>3 Tahun - 2x Sesi / Tahun (Total 6x)</option>
                            </optgroup>
                            <optgroup label="Lainnya">
                                <option value="on_demand" {{ ($project->sharing_knowledge ?? '') == 'on_demand' ? 'selected' : '' }}>Sesuai Kebutuhan / On-Demand</option>
                                <option value="none" {{ ($project->sharing_knowledge ?? '') == 'none' ? 'selected' : '' }}>Tidak Ada Sharing Knowledge</option>
                            </optgroup>
                        </select>
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-[#E2E8F0]">
                        <button type="button" @click="isSlaModalOpen = false" class="px-4 py-2 rounded-xl text-xs font-semibold text-[#64748B] hover:bg-[#F8FAFC]">Batal</button>
                        <button type="submit" :disabled="isSavingSla" class="btn-ipnet-gradient px-4 py-2 rounded-xl text-white text-xs font-bold shadow-md cursor-pointer disabled:opacity-50">Simpan Perubahan SLA</button>
                    </div>
                </form>
            </div>
        </div>
    </template>


    <!-- ========================================================== -->
    <!-- MODAL 5: UPLOAD BERKAS BAP CM                              -->
    <!-- ========================================================== -->
    <template x-teleport="body">
        <div x-show="isUploadBapModalOpen" x-cloak class="fixed inset-0 z-50 bg-[#0E0D12]/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-[#E2E8F0]" @click.away="isUploadBapModalOpen = false">
                <div class="flex items-center justify-between border-b border-[#E2E8F0] pb-3 mb-4">
                    <div>
                        <p class="text-[10.5px] font-bold text-[#8F0A0D] uppercase tracking-wider">Berita Acara Perbaikan (BAP)</p>
                        <h3 class="text-[15px] font-bold text-[#1E293B]">Upload BAP Tiket <span x-text="selectedUploadTicket.number"></span></h3>
                    </div>
                    <button @click="isUploadBapModalOpen = false" class="text-slate-400 hover:text-slate-700 p-1 rounded-lg text-lg font-bold">&times;</button>
                </div>

                <form :action="'/managed-service/monitoring/cm-tickets/' + selectedUploadTicket.id + '/upload-bap'" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-[#334155] mb-1">Pilih Berkas BAP (PDF / Scan Bertanda Tangan) <span class="text-[#8F0A0D]">*</span></label>
                        <input type="file" name="bap_file" required accept=".pdf,.jpg,.jpeg,.png" class="w-full text-xs text-[#64748B] file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-rose-50 file:text-[#8F0A0D] hover:file:bg-rose-100 cursor-pointer">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#334155] mb-1">Status Tiket Setelah Upload</label>
                        <select name="status" class="w-full px-3 py-2 bg-white border border-[#CBD5E1] rounded-xl text-xs text-[#1E293B] outline-none focus:border-[#8F0A0D]">
                            <option value="Resolved" selected>Resolved (Perbaikan Tuntas & Disetujui Klien)</option>
                            <option value="In Progress">Tetap In Progress (Sedang Berjalan)</option>
                            <option value="Closed">Closed (Ditutup Resmi)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#334155] mb-1">Catatan Tindakan / Resolusi (Opsional)</label>
                        <textarea name="resolution_notes" rows="2" placeholder="Langkah perbaikan yang dilakukan..." class="w-full px-3 py-2 bg-white border border-[#CBD5E1] rounded-xl text-xs text-[#1E293B] outline-none focus:border-[#8F0A0D]"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-[#E2E8F0]">
                        <button type="button" @click="isUploadBapModalOpen = false" class="px-4 py-2 rounded-xl text-xs font-semibold text-[#64748B] hover:bg-[#F8FAFC]">
                            Batal
                        </button>
                        <button type="submit" class="btn-ipnet-gradient px-4 py-2 rounded-xl text-xs font-bold shadow-md text-white">
                            Simpan &amp; Unggah BAP
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>

</div>
@endsection
