@extends('layouts.app')

@section('title', 'Project & Pipeline - PT IP Network Solusindo')

@push('styles')
<style>
    .ipnet-card {
        background-color: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 20px;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.04);
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .ipnet-card:hover {
        border-color: #CBD5E1;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
    }
    .ipnet-badge-dot {
        width: 9px;
        height: 9px;
        border-radius: 50%;
        background: linear-gradient(135deg, #EF4444 0%, #8F0A0D 100%);
        box-shadow: 0 0 0 2px #FEE2E2, 0 1px 3px rgba(143, 10, 13, 0.4);
        display: inline-block;
        margin-right: 8px;
        flex-shrink: 0;
    }

    /* Modern Status Gradient Bullets */
    .bullet-draft {
        width: 11px;
        height: 11px;
        border-radius: 50%;
        background: linear-gradient(135deg, #94A3B8 0%, #475569 100%);
        box-shadow: 0 0 0 2.5px #F1F5F9, 0 1px 3px rgba(71, 85, 105, 0.35);
        display: inline-block;
        flex-shrink: 0;
    }
    .bullet-opportunity {
        width: 11px;
        height: 11px;
        border-radius: 50%;
        background: linear-gradient(135deg, #38BDF8 0%, #2563EB 100%);
        box-shadow: 0 0 0 2.5px #EFF6FF, 0 1px 4px rgba(37, 99, 235, 0.4);
        display: inline-block;
        flex-shrink: 0;
    }
    .bullet-in-progress {
        width: 11px;
        height: 11px;
        border-radius: 50%;
        background: linear-gradient(135deg, #FBBF24 0%, #EA580C 100%);
        box-shadow: 0 0 0 2.5px #FFFBEB, 0 1px 4px rgba(234, 88, 12, 0.4);
        display: inline-block;
        flex-shrink: 0;
    }
    .bullet-pending {
        width: 11px;
        height: 11px;
        border-radius: 50%;
        background: linear-gradient(135deg, #C084FC 0%, #7C3AED 100%);
        box-shadow: 0 0 0 2.5px #FAF5FF, 0 1px 4px rgba(124, 58, 237, 0.4);
        display: inline-block;
        flex-shrink: 0;
    }
    .bullet-completed {
        width: 11px;
        height: 11px;
        border-radius: 50%;
        background: linear-gradient(135deg, #34D399 0%, #059669 100%);
        box-shadow: 0 0 0 2.5px #ECFDF5, 0 1px 4px rgba(5, 150, 105, 0.4);
        display: inline-block;
        flex-shrink: 0;
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

    /* Custom Kanban Scrollbars */
    .kanban-scroll::-webkit-scrollbar {
        height: 7px;
    }
    .kanban-scroll::-webkit-scrollbar-track {
        background: #F1F5F9;
        border-radius: 8px;
    }
    .kanban-scroll::-webkit-scrollbar-thumb {
        background: #CBD5E1;
        border-radius: 8px;
    }
    .kanban-scroll::-webkit-scrollbar-thumb:hover {
        background: #94A3B8;
    }

    /* Vertical Scroll on Kanban Columns (Spacious & Breathable, Matching Lead Engineer) */
    .kanban-col {
        display: flex;
        flex-direction: column;
        max-height: calc(100vh - 270px);
        min-height: 560px;
        overflow: hidden;
        background: #F8FAFC;
        border: 1px solid #E2E8F0;
        border-radius: 18px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
    }
    .kanban-col-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 18px;
        flex-shrink: 0;
        background: #F8FAFC;
        border-bottom: 1px solid #E2E8F0;
        border-radius: 18px 18px 0 0;
    }
    .kanban-col-body {
        display: flex;
        flex-direction: column;
        gap: 14px;
        padding: 16px 12px 16px 16px;
        overflow-y: auto;
        overflow-x: hidden;
        flex: 1;
        scrollbar-width: thin;
        scrollbar-color: #CBD5E1 transparent;
    }
    .kanban-col-body::-webkit-scrollbar {
        width: 6px;
    }
    .kanban-col-body::-webkit-scrollbar-track {
        background: transparent;
        margin-block: 8px;
    }
    .kanban-col-body::-webkit-scrollbar-thumb {
        background: #CBD5E1;
        border-radius: 99px;
    }
    .kanban-col-body::-webkit-scrollbar-thumb:hover {
        background: #94A3B8;
    }
</style>
@endpush

@section('content')
<div class="flex h-screen overflow-hidden bg-[#F8FAFC] font-sans" x-data="projectKanbanPage()">
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Project'])
        
        <div class="p-4 sm:p-6 lg:p-7 space-y-5 max-w-[1680px] mx-auto">
            
            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between shadow-xs anim-fade-up">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 font-bold px-2">✕</button>
                </div>
            @endif

            <!-- ========================================================== -->
            <!-- 1. OFFICIAL IPNET SECTION HEADER & ACTION CONTROLS          -->
            <!-- ========================================================== -->
            <div class="ipnet-card p-5 sm:p-6 anim-fade-up anim-delay-1">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 pb-4 mb-4 border-b border-[#E2E8F0]">
                    <div>
                        <p class="text-[#8F0A0D] text-[12px] font-bold inline-flex items-center uppercase tracking-wider">
                            <span class="ipnet-badge-dot"></span> MANAJEMEN PIPELINE &amp; PORTOFOLIO
                        </p>
                        <h2 class="text-[20px] font-bold text-[#1E293B] tracking-tight">Project Management &amp; Pipeline Sales</h2>
                        <p class="text-[13px] text-[#64748B] mt-0.5">Monitoring pergerakan peluang, tahapan persetujuan, dan status pengerjaan proyek integrasi</p>
                    </div>

                    <div class="flex flex-wrap items-center gap-3 shrink-0">
                        {{-- Total Projects Badge --}}
                        <div class="px-3.5 py-2.5 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0] text-[12.5px] font-bold text-[#1E293B] flex items-center gap-2 shadow-2xs">
                            <span class="text-[#64748B]">Total Proyek:</span>
                            <span class="text-[#8F0A0D] font-extrabold text-sm">{{ $allProjects->count() }}</span>
                        </div>

                        {{-- Add New Project (Red) --}}
                        <button type="button" 
                                @click="openAddProjectModal('Opportunity')"
                                class="btn-ipnet-gradient px-4 py-2.5 rounded-xl font-bold text-[13px] flex items-center gap-2 cursor-pointer shadow-md hover:shadow-lg active:scale-[0.98] transition-all">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span>Add New Project</span>
                        </button>

                        {{-- Add Complete Project (Emerald) --}}
                        <button type="button" 
                                @click="openAddProjectModal('Completed')"
                                class="px-4 py-2.5 rounded-xl font-bold text-[13px] flex items-center gap-2 cursor-pointer shadow-md hover:shadow-lg text-white bg-emerald-600 hover:bg-emerald-700 active:scale-[0.98] transition-all">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Add Complete Project</span>
                        </button>
                    </div>
                </div>

                <!-- Filter Controls Toolbar -->
                <form method="GET" action="{{ route('sales.pipeline.index') }}" class="flex flex-col sm:flex-row flex-wrap items-center gap-3">
                    {{-- Search Input --}}
                    <div class="relative flex-1 min-w-[240px] w-full sm:w-auto">
                        <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-[#94A3B8]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" 
                               name="search" 
                               value="{{ $search }}"
                               placeholder="Search nama project, client, quotation..." 
                               class="w-full pl-9 pr-3.5 py-2 rounded-xl border border-[#CBD5E1] text-[12.5px] font-semibold text-[#1E293B] bg-white outline-none hover:border-[#94A3B8] focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition-all shadow-xs placeholder-[#94A3B8]">
                    </div>

                    {{-- Select Division Dropdown --}}
                    <select name="division_id" onchange="this.form.submit()" 
                            class="w-full sm:w-48 px-3 py-2 rounded-xl border border-[#CBD5E1] text-[12.5px] font-semibold text-[#1E293B] bg-white outline-none hover:border-[#94A3B8] focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition-all shadow-xs cursor-pointer">
                        <option value="">Select division</option>
                        @foreach($divisions as $div)
                            <option value="{{ $div->id }}" {{ $filterDivision == $div->id ? 'selected' : '' }}>{{ $div->name }}</option>
                        @endforeach
                    </select>

                    {{-- Select Status Approval Dropdown --}}
                    <select name="approval_status" onchange="this.form.submit()" 
                            class="w-full sm:w-52 px-3 py-2 rounded-xl border border-[#CBD5E1] text-[12.5px] font-semibold text-[#1E293B] bg-white outline-none hover:border-[#94A3B8] focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition-all shadow-xs cursor-pointer">
                        <option value="">Select status approval</option>
                        <option value="Draft" {{ $filterApproval == 'Draft' ? 'selected' : '' }}>Draft</option>
                        <option value="Submitted" {{ $filterApproval == 'Submitted' ? 'selected' : '' }}>Submitted</option>
                        <option value="Approved" {{ $filterApproval == 'Approved' ? 'selected' : '' }}>Approved</option>
                        <option value="Pending" {{ $filterApproval == 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="Completed" {{ $filterApproval == 'Completed' ? 'selected' : '' }}>Completed</option>
                    </select>

                    {{-- View Mode Toggle --}}
                    <div class="flex items-center p-1 bg-[#F1F5F9] rounded-xl border border-[#E2E8F0] shrink-0">
                        <button type="button" 
                                @click="viewMode = 'kanban'"
                                :class="viewMode === 'kanban' ? 'bg-white text-[#8F0A0D] font-bold shadow-xs' : 'text-[#64748B] font-semibold hover:text-[#1E293B]'"
                                class="px-3 py-1.5 rounded-lg text-xs transition flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                            <span>Kanban</span>
                        </button>
                        <button type="button" 
                                @click="viewMode = 'table'"
                                :class="viewMode === 'table' ? 'bg-white text-[#8F0A0D] font-bold shadow-xs' : 'text-[#64748B] font-semibold hover:text-[#1E293B]'"
                                class="px-3 py-1.5 rounded-lg text-xs transition flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                            <span>Tabel</span>
                        </button>
                    </div>

                    @if($search || $filterDivision || $filterApproval)
                        <a href="{{ route('sales.pipeline.index') }}" 
                           class="px-3 py-2 text-[12px] font-bold text-[#64748B] hover:text-[#8F0A0D] bg-[#F8FAFC] hover:bg-[#FEF2F2] border border-[#E2E8F0] hover:border-[#FCA5A5] rounded-xl transition cursor-pointer">
                            Reset Filter
                        </a>
                    @endif
                </form>
            </div>

            <!-- ========================================================== -->
            <!-- 2. KANBAN BOARD VIEW (COHESIVE & CLEAN)                     -->
            <!-- ========================================================== -->
            <div x-show="viewMode === 'kanban'" class="anim-fade-up anim-delay-2">
                <div class="kanban-scroll overflow-x-auto pb-4">
                    <div class="grid grid-cols-5 gap-5 min-w-[1550px] items-start">
                        
                        {{-- 1. DRAFT --}}
                        <div class="kanban-col">
                            <div class="kanban-col-head">
                                <div class="flex items-center gap-2.5">
                                    <span class="bullet-draft"></span>
                                    <h3 class="text-[13.5px] font-bold text-[#1E293B]">Draft</h3>
                                </div>
                                <span class="text-[11.5px] font-extrabold px-2.5 py-0.5 rounded-full bg-white border border-[#E2E8F0] text-slate-700 shadow-2xs">
                                    {{ $kanban['draft']->count() }}
                                </span>
                            </div>
                            <div class="kanban-col-body">
                                @forelse($kanban['draft'] as $p)
                                    @include('sales.pipeline.partials.kanban-card', ['project' => $p, 'column' => 'Draft'])
                                @empty
                                    <div class="py-14 px-4 text-center flex flex-col items-center justify-center text-slate-400 text-xs font-medium border-2 border-dashed border-[#E2E8F0] rounded-xl bg-white/50 space-y-2">
                                        <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-400">
                                            <svg class="w-5 h-5 fill-none stroke-current" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        </div>
                                        <span class="text-slate-500 font-semibold text-[12px]">Belum ada project draft</span>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        {{-- 2. OPPORTUNITY --}}
                        <div class="kanban-col">
                            <div class="kanban-col-head">
                                <div class="flex items-center gap-2.5">
                                    <span class="bullet-opportunity"></span>
                                    <h3 class="text-[13.5px] font-bold text-[#1E293B]">Opportunity</h3>
                                </div>
                                <span class="text-[11.5px] font-extrabold px-2.5 py-0.5 rounded-full bg-white border border-[#E2E8F0] text-blue-700 shadow-2xs">
                                    {{ $kanban['opportunity']->count() }}
                                </span>
                            </div>
                            <div class="kanban-col-body">
                                @forelse($kanban['opportunity'] as $p)
                                    @include('sales.pipeline.partials.kanban-card', ['project' => $p, 'column' => 'Opportunity'])
                                @empty
                                    <div class="py-14 px-4 text-center flex flex-col items-center justify-center text-slate-400 text-xs font-medium border-2 border-dashed border-[#E2E8F0] rounded-xl bg-white/50 space-y-2">
                                        <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center text-blue-400">
                                            <svg class="w-5 h-5 fill-none stroke-current" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                        </div>
                                        <span class="text-slate-500 font-semibold text-[12px]">Belum ada opportunity</span>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        {{-- 3. IN PROGRESS --}}
                        <div class="kanban-col">
                            <div class="kanban-col-head">
                                <div class="flex items-center gap-2.5">
                                    <span class="bullet-in-progress"></span>
                                    <h3 class="text-[13.5px] font-bold text-[#1E293B]">In Progress</h3>
                                </div>
                                <span class="text-[11.5px] font-extrabold px-2.5 py-0.5 rounded-full bg-white border border-[#E2E8F0] text-amber-700 shadow-2xs">
                                    {{ $kanban['in_progress']->count() }}
                                </span>
                            </div>
                            <div class="kanban-col-body">
                                @forelse($kanban['in_progress'] as $p)
                                    @include('sales.pipeline.partials.kanban-card', ['project' => $p, 'column' => 'In Progress'])
                                @empty
                                    <div class="py-14 px-4 text-center flex flex-col items-center justify-center text-slate-400 text-xs font-medium border-2 border-dashed border-[#E2E8F0] rounded-xl bg-white/50 space-y-2">
                                        <div class="w-10 h-10 rounded-full bg-amber-50 flex items-center justify-center text-amber-400">
                                            <svg class="w-5 h-5 fill-none stroke-current" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        </div>
                                        <span class="text-slate-500 font-semibold text-[12px]">Belum ada project in progress</span>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        {{-- 4. PENDING --}}
                        <div class="kanban-col">
                            <div class="kanban-col-head">
                                <div class="flex items-center gap-2.5">
                                    <span class="bullet-pending"></span>
                                    <h3 class="text-[13.5px] font-bold text-[#1E293B]">Pending</h3>
                                </div>
                                <span class="text-[11.5px] font-extrabold px-2.5 py-0.5 rounded-full bg-white border border-[#E2E8F0] text-purple-700 shadow-2xs">
                                    {{ $kanban['pending']->count() }}
                                </span>
                            </div>
                            <div class="kanban-col-body">
                                @forelse($kanban['pending'] as $p)
                                    @include('sales.pipeline.partials.kanban-card', ['project' => $p, 'column' => 'Pending'])
                                @empty
                                    <div class="py-14 px-4 text-center flex flex-col items-center justify-center text-slate-400 text-xs font-medium border-2 border-dashed border-[#E2E8F0] rounded-xl bg-white/50 space-y-2">
                                        <div class="w-10 h-10 rounded-full bg-purple-50 flex items-center justify-center text-purple-400">
                                            <svg class="w-5 h-5 fill-none stroke-current" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        </div>
                                        <span class="text-slate-500 font-semibold text-[12px]">Belum ada project pending</span>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        {{-- 5. COMPLETED --}}
                        <div class="kanban-col">
                            <div class="kanban-col-head">
                                <div class="flex items-center gap-2.5">
                                    <span class="bullet-completed"></span>
                                    <h3 class="text-[13.5px] font-bold text-[#1E293B]">Completed</h3>
                                </div>
                                <span class="text-[11.5px] font-extrabold px-2.5 py-0.5 rounded-full bg-white border border-[#E2E8F0] text-emerald-700 shadow-2xs">
                                    {{ $kanban['completed']->count() }}
                                </span>
                            </div>
                            <div class="kanban-col-body">
                                @forelse($kanban['completed'] as $p)
                                    @include('sales.pipeline.partials.kanban-card', ['project' => $p, 'column' => 'Completed'])
                                @empty
                                    <div class="py-14 px-4 text-center flex flex-col items-center justify-center text-slate-400 text-xs font-medium border-2 border-dashed border-[#E2E8F0] rounded-xl bg-white/50 space-y-2">
                                        <div class="w-10 h-10 rounded-full bg-emerald-50 flex items-center justify-center text-emerald-500">
                                            <svg class="w-5 h-5 fill-none stroke-current" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        </div>
                                        <span class="text-slate-500 font-semibold text-[12px]">Belum ada project completed</span>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- ========================================================== -->
            <!-- 3. DATA TABLE VIEW (CLEAN & METRIC-RICH)                    -->
            <!-- ========================================================== -->
            <div x-show="viewMode === 'table'" x-cloak class="ipnet-card overflow-hidden anim-fade-up anim-delay-2">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-[#F8FAFC] border-b border-[#E2E8F0] text-[#64748B] uppercase text-[11px] font-bold">
                                <th class="py-3.5 px-5">Nama Project &amp; Client</th>
                                <th class="py-3.5 px-5">Sales &amp; Divisi</th>
                                <th class="py-3.5 px-5 text-right">Nilai Kontrak</th>
                                <th class="py-3.5 px-5 text-center">Status Kanban</th>
                                <th class="py-3.5 px-5 text-center">Tahapan CRM</th>
                                <th class="py-3.5 px-5 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#F1F5F9] font-medium text-[#1E293B]">
                            @forelse($allProjects as $p)
                                <tr class="hover:bg-[#F8FAFC] transition">
                                    <td class="py-3.5 px-5">
                                        <a href="{{ route('projects.show', $p->id) }}" class="font-bold text-[#1E293B] hover:text-[#8F0A0D] text-[13px] block">
                                            {{ $p->name }}
                                        </a>
                                        <span class="text-[11.5px] text-[#64748B] font-medium">{{ $p->client }}</span>
                                    </td>
                                    <td class="py-3.5 px-5">
                                        <div class="font-bold text-[#1E293B]">{{ $p->sales_name ?: '-' }}</div>
                                        <div class="text-[11px] text-[#64748B]">{{ $p->division ? $p->division->name : 'Divisi Umum' }}</div>
                                    </td>
                                    <td class="py-3.5 px-5 text-right whitespace-nowrap">
                                        <span class="font-extrabold text-[#8F0A0D] text-[13px]">
                                            {{ $p->contract_value > 0 ? 'Rp ' . number_format($p->contract_value, 0, ',', '.') : '—' }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-5 text-center whitespace-nowrap">
                                        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-[11px] font-bold bg-[#F8FAFC] border border-[#E2E8F0] text-[#1E293B] shadow-2xs">
                                            @if($p->status === 'Draft') <span class="bullet-draft" style="width:8px; height:8px;"></span>
                                            @elseif($p->status === 'Opportunity') <span class="bullet-opportunity" style="width:8px; height:8px;"></span>
                                            @elseif($p->status === 'In Progress') <span class="bullet-in-progress" style="width:8px; height:8px;"></span>
                                            @elseif($p->status === 'Pending') <span class="bullet-pending" style="width:8px; height:8px;"></span>
                                            @elseif($p->status === 'Completed') <span class="bullet-completed" style="width:8px; height:8px;"></span>
                                            @endif
                                            <span>{{ $p->status }}</span>
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-5 text-center whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-red-50 text-[#8F0A0D] border border-red-200">
                                            {{ $p->sales_stage ?: 'Qualification' }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-5 text-right whitespace-nowrap">
                                        <a href="{{ route('projects.show', $p->id) }}" 
                                           class="px-3 py-1.5 rounded-lg border border-[#CBD5E1] text-[#1E293B] hover:bg-[#F8FAFC] text-[11px] font-bold inline-flex items-center gap-1 transition">
                                            <span>Detail</span>
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-12 text-center text-[#94A3B8] text-xs">
                                        Tidak ada data project yang sesuai dengan filter.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    {{-- MODAL: + ADD NEW PROJECT / + ADD COMPLETE PROJECT --}}
    <div x-show="isAddModalOpen" x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
        <div @click.away="isAddModalOpen = false" 
             class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-gray-100 space-y-4 max-h-[90vh] overflow-y-auto">
            
            <div class="flex items-center justify-between border-b pb-3">
                <h3 class="text-base font-bold text-gray-900" x-text="modalTitle"></h3>
                <button type="button" @click="isAddModalOpen = false" class="text-gray-400 hover:text-gray-700 text-lg font-bold">✕</button>
            </div>

            <form action="{{ route('sales.pipeline.store') }}" method="POST" class="space-y-4 text-xs font-semibold">
                @csrf
                <input type="hidden" name="status" :value="formStatus">
                <input type="hidden" name="is_completed" :value="formStatus === 'Completed' ? '1' : '0'">

                <div>
                    <label class="block text-gray-700 mb-1">Nama Project <span class="text-red-500">*</span></label>
                    <input type="text" name="name" required placeholder="Contoh: Pengadaan Firewall & Switch Datacenter"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs font-normal text-gray-900 focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D]">
                </div>

                <div>
                    <label class="block text-gray-700 mb-1">Nama Client <span class="text-red-500">*</span></label>
                    <input type="text" name="client" required placeholder="Contoh: PT Telkom Indonesia / Bank BRI"
                           list="clientListOptions"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs font-normal text-gray-900 focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D]">
                    <datalist id="clientListOptions">
                        @foreach($clients as $cl)
                            <option value="{{ $cl->name }}">{{ $cl->department ? "({$cl->department})" : '' }}</option>
                        @endforeach
                    </datalist>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-gray-700 mb-1">Divisi Terkait</label>
                        <select name="division_id" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs text-gray-900 focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D] cursor-pointer">
                            <option value="">Pilih Divisi...</option>
                            @foreach($divisions as $div)
                                <option value="{{ $div->id }}">{{ $div->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-gray-700 mb-1">Nilai Kontrak (Rp)</label>
                        <input type="number" name="contract_value" min="0" placeholder="0"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs font-normal text-gray-900 focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D]">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3" x-show="formStatus !== 'Completed'">
                    <div>
                        <label class="block text-gray-700 mb-1">Tahapan Sales</label>
                        <select name="sales_stage" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs text-gray-900 focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D] cursor-pointer">
                            @foreach($stages as $k => $stg)
                                <option value="{{ $k }}">{{ $stg['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-700 mb-1">Estimasi Closing</label>
                        <input type="date" name="expected_closing_date" value="{{ date('Y-m-d', strtotime('+1 month')) }}"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs font-normal text-gray-900 focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D]">
                    </div>
                </div>

                <div>
                    <label class="block text-gray-700 mb-1">Catatan Tambahan</label>
                    <textarea name="sales_notes" rows="2.5" placeholder="Detail kebutuhan klien atau spesifikasi..."
                              class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs font-normal text-gray-900 focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D]"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t">
                    <button type="button" @click="isAddModalOpen = false" class="px-4 py-2 rounded-xl border border-gray-200 text-gray-600 font-bold hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="submit" 
                            :class="formStatus === 'Completed' ? 'bg-emerald-600 hover:bg-emerald-700 text-white' : 'btn-ipnet-gradient'"
                            class="px-5 py-2.5 rounded-xl font-bold shadow-md cursor-pointer">
                        Simpan Project
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
    function projectKanbanPage() {
        return {
            viewMode: 'kanban',
            isAddModalOpen: false,
            modalTitle: 'Tambah Project Baru',
            formStatus: 'Opportunity',

            openAddProjectModal(statusType) {
                this.formStatus = statusType;
                if (statusType === 'Completed') {
                    this.modalTitle = 'Tambah Project Selesai (Completed)';
                } else {
                    this.modalTitle = 'Tambah Project Baru';
                }
                this.isAddModalOpen = true;
            }
        };
    }
</script>
@endsection
