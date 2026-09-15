@extends('layouts.app')

@section('title', 'Timesheet & Log Aktivitas - PT IP Network Solusindo')

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
<div class="flex h-screen overflow-hidden bg-[#F8FAFC]" x-data="timesheetApp()">
    @include('components.sidebar')

    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Timesheet & Log Aktivitas'])

        <div class="p-4 sm:p-6 lg:p-7 space-y-5 max-w-[1600px] mx-auto">
            
            {{-- Toast Feedback Alerts --}}
            @if(session('success'))
                <div class="p-3.5 px-4 rounded-xl bg-[#F0FDF4] border border-[#BBF7D0] text-[#16A34A] text-[13px] flex items-center justify-between shadow-xs anim-fade-up">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-[#16A34A] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="font-bold">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="p-3.5 px-4 rounded-xl bg-[#FEF2F2] border border-[#FECACA] text-[#8F0A0D] text-[13px] flex items-center justify-between shadow-xs anim-fade-up">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-[#8F0A0D] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <span class="font-bold">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            <!-- ========================================================== -->
            <!-- 1. STATS METRIC CARDS (IPNET CARD STYLE)                   -->
            <!-- ========================================================== -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 anim-fade-up anim-delay-1">
                {{-- Card 1: Jam Minggu Ini --}}
                <div class="ipnet-card p-5 flex items-center justify-between">
                    <div>
                        <p class="text-[11.5px] font-bold text-[#64748B] uppercase tracking-wider">
                            {{ $isLead ? 'Jam Kerja Tim (Minggu Ini)' : 'Jam Kerja (Minggu Ini)' }}
                        </p>
                        <h3 class="text-[22px] font-extrabold text-[#1E293B] mt-1">
                            {{ $totalWeekHours }} <span class="text-[12.5px] font-semibold text-[#64748B]">Jam</span>
                        </h3>
                        <p class="text-[11.5px] text-[#94A3B8] mt-0.5">Akumulasi pekan berjalan</p>
                    </div>
                    <div class="w-11 h-11 rounded-2xl bg-[#FEF2F2] border border-[#FECACA] flex items-center justify-center text-[#8F0A0D] shadow-xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>

                {{-- Card 2: Jam Bulan Ini --}}
                <div class="ipnet-card p-5 flex items-center justify-between">
                    <div>
                        <p class="text-[11.5px] font-bold text-[#64748B] uppercase tracking-wider">
                            {{ $isLead ? 'Jam Kerja Tim (Bulan Ini)' : 'Jam Kerja (Bulan Ini)' }}
                        </p>
                        <h3 class="text-[22px] font-extrabold text-[#2563EB] mt-1">
                            {{ $totalMonthHours }} <span class="text-[12.5px] font-semibold text-[#64748B]">Jam</span>
                        </h3>
                        <p class="text-[11.5px] text-[#94A3B8] mt-0.5">Total jam kerja bulanan</p>
                    </div>
                    <div class="w-11 h-11 rounded-2xl bg-[#EFF6FF] border border-[#BFDBFE] flex items-center justify-center text-[#2563EB] shadow-xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </div>

                {{-- Card 3: Total Log Aktivitas --}}
                <div class="ipnet-card p-5 flex items-center justify-between">
                    <div>
                        <p class="text-[11.5px] font-bold text-[#64748B] uppercase tracking-wider">Total Log Aktivitas</p>
                        <h3 class="text-[22px] font-extrabold text-[#16A34A] mt-1">
                            {{ $totalLogsCount }} <span class="text-[12.5px] font-semibold text-[#64748B]">Log</span>
                        </h3>
                        <p class="text-[11.5px] text-[#94A3B8] mt-0.5">Catatan aktivitas terdaftar</p>
                    </div>
                    <div class="w-11 h-11 rounded-2xl bg-[#F0FDF4] border border-[#BBF7D0] flex items-center justify-center text-[#16A34A] shadow-xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                </div>

                {{-- Card 4: Total Jam Lembur --}}
                <div class="ipnet-card p-5 flex items-center justify-between">
                    <div>
                        <p class="text-[11.5px] font-bold text-[#64748B] uppercase tracking-wider">Total Jam Lembur</p>
                        <h3 class="text-[22px] font-extrabold text-[#D97706] mt-1">
                            {{ $totalOvertimeHours }} <span class="text-[12.5px] font-semibold text-[#64748B]">Jam</span>
                        </h3>
                        <p class="text-[11.5px] text-[#94A3B8] mt-0.5">Waktu lembur tervalidasi</p>
                    </div>
                    <div class="w-11 h-11 rounded-2xl bg-[#FFFBEB] border border-[#FDE68A] flex items-center justify-center text-[#D97706] shadow-xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- ========================================================== -->
            <!-- 2. SECTION HEADER & UNIFIED FILTER BAR                     -->
            <!-- ========================================================== -->
            <div class="ipnet-card p-5 sm:p-6 space-y-4 anim-fade-up anim-delay-2">
                
                {{-- Header Title & Action Buttons --}}
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 pb-4 border-b border-[#E2E8F0]">
                    <div>
                        <p class="text-[#8F0A0D] text-[12px] font-bold inline-flex items-center uppercase tracking-wider">
                            <span class="ipnet-badge-dot"></span> TIMESHEET & LOG AKTIVITAS
                        </p>
                        <h2 class="text-[20px] font-bold text-[#1E293B] tracking-tight">Catatan Log Kerja & Aktivitas</h2>
                        <p class="text-[13px] text-[#64748B] mt-0.5">Pantau jam kerja tim teknis, lembur tervalidasi, dan detail log tugas lapangan</p>
                    </div>

                    <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                        {{-- Export Dropdown --}}
                        @if(\App\Helpers\ScopeHelper::isManagerial(auth()->user()))
                        <div class="relative" x-data="{ exportOpen: false }">
                            <button type="button" 
                                    @click="exportOpen = !exportOpen"
                                    class="px-3.5 py-2.5 rounded-xl border border-[#CBD5E1] bg-white hover:bg-[#F8FAFC] text-[#1E293B] text-[13px] font-bold transition flex items-center gap-2 shadow-xs cursor-pointer">
                                <svg class="w-4 h-4 text-[#64748B]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                                <span>Export Laporan</span>
                                <svg class="w-3.5 h-3.5 text-[#64748B] transition-transform duration-200" :class="exportOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <div x-show="exportOpen" 
                                 x-cloak
                                 @click.outside="exportOpen = false"
                                 class="absolute right-0 mt-2 w-52 bg-white border border-[#E2E8F0] rounded-2xl shadow-xl z-30 py-1.5 overflow-hidden animate-fade-in-up">
                                
                                <a href="{{ route('timesheets.export.excel', request()->all()) }}" 
                                   class="flex items-center gap-2.5 px-4 py-2.5 text-[13px] font-semibold text-[#1E293B] hover:bg-[#F0FDF4] hover:text-[#16A34A] transition">
                                    <svg class="w-4 h-4 text-[#16A34A] flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zM6 4h7v5h5v11H6V4zm2 8h2.5l1.5 2.5 1.5-2.5H16l-2.25 3.5L16 19h-2.5L12 16.5 10.5 19H8l2.25-3.5L8 12z"/>
                                    </svg>
                                    Export Excel (.xlsx)
                                </a>

                                <a href="{{ route('timesheets.export.pdf', request()->all()) }}" 
                                   class="flex items-center gap-2.5 px-4 py-2.5 text-[13px] font-semibold text-[#1E293B] hover:bg-[#FEF2F2] hover:text-[#8F0A0D] transition border-t border-[#F1F5F9]">
                                    <svg class="w-4 h-4 text-[#8F0A0D] flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14H9v-2h2v2zm0-4H9V7h2v5zm4 4h-2v-6h2v6z"/>
                                    </svg>
                                    Export PDF (.pdf)
                                </a>
                            </div>
                        </div>
                        @endif

                        {{-- Catat Log Button --}}
                        @if(!auth()->user()->hasAnyRole(['Direktur', 'HD / Direktur', 'Group Leader', 'Lead Divisi']))
                        <button type="button" 
                                @click="openAddModal()"
                                class="btn-ipnet-gradient px-4 py-2.5 rounded-xl font-bold text-[13px] flex items-center gap-2 cursor-pointer shadow-md">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span>Catat Log Kerja</span>
                        </button>
                        @endif
                    </div>
                </div>

                {{-- Filter Form --}}
                <form method="GET" action="{{ route('timesheets.index') }}" id="timesheetFilterForm" class="space-y-3">
                    
                    {{-- Row 1: Search & Dropdowns --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                        {{-- Search Input --}}
                        <div class="relative">
                            <svg class="w-4 h-4 text-[#94A3B8] absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input type="text" 
                                   name="search" 
                                   value="{{ request('search') }}" 
                                   placeholder="Cari aktivitas, task, atau catatan..."
                                   class="w-full pl-9 pr-3.5 py-2 rounded-xl border border-[#CBD5E1] text-[12.5px] font-semibold text-[#1E293B] bg-white outline-none hover:border-[#94A3B8] focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition-all shadow-xs">
                        </div>

                        {{-- Filter Engineer (Hanya Lead Engineer) --}}
                        @if($isLead)
                            <div>
                                <select name="engineer_id" onchange="this.form.submit()" class="w-full px-3 py-2 rounded-xl border border-[#CBD5E1] text-[12.5px] font-semibold text-[#1E293B] bg-white outline-none hover:border-[#94A3B8] focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition-all shadow-xs cursor-pointer">
                                    <option value="">Semua Engineer</option>
                                    @foreach($engineers as $eng)
                                        <option value="{{ $eng->id }}" {{ request('engineer_id') == $eng->id ? 'selected' : '' }}>
                                            {{ $eng->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        {{-- Filter Project --}}
                        <div>
                            <select name="project_id" onchange="this.form.submit()" class="w-full px-3 py-2 rounded-xl border border-[#CBD5E1] text-[12.5px] font-semibold text-[#1E293B] bg-white outline-none hover:border-[#94A3B8] focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition-all shadow-xs cursor-pointer">
                                <option value="">Semua Project</option>
                                @foreach($projects as $p)
                                    <option value="{{ $p->id }}" {{ request('project_id') == $p->id ? 'selected' : '' }}>
                                        {{ $p->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Filter Kategori --}}
                        <div>
                            <select name="category" onchange="this.form.submit()" class="w-full px-3 py-2 rounded-xl border border-[#CBD5E1] text-[12.5px] font-semibold text-[#1E293B] bg-white outline-none hover:border-[#94A3B8] focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition-all shadow-xs cursor-pointer">
                                <option value="">Semua Kategori</option>
                                <option value="On-Site" {{ request('category') == 'On-Site' ? 'selected' : '' }}>On-Site</option>
                                <option value="Remote" {{ request('category') == 'Remote' ? 'selected' : '' }}>Remote</option>
                                <option value="Maintenance" {{ request('category') == 'Maintenance' ? 'selected' : '' }}>Maintenance</option>
                                <option value="Meeting" {{ request('category') == 'Meeting' ? 'selected' : '' }}>Meeting</option>
                            </select>
                        </div>
                    </div>

                    {{-- Row 2: Date Range Picker & Filter Buttons --}}
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 pt-2">
                        <div class="flex items-center gap-2 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl px-3 py-1.5 shadow-xs w-full sm:w-auto">
                            <svg class="w-4 h-4 text-[#64748B] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <input type="date" 
                                   name="date_start" 
                                   value="{{ request('date_start') }}" 
                                   title="Dari Tanggal"
                                   class="py-0.5 px-1 text-[12.5px] font-semibold bg-transparent border-none focus:outline-none text-[#1E293B] cursor-pointer">
                            <span class="text-[#94A3B8] text-[12px] font-medium">s/d</span>
                            <input type="date" 
                                   name="date_end" 
                                   value="{{ request('date_end') }}" 
                                   title="Sampai Tanggal"
                                   class="py-0.5 px-1 text-[12.5px] font-semibold bg-transparent border-none focus:outline-none text-[#1E293B] cursor-pointer">
                        </div>

                        <div class="flex items-center gap-2">
                            <button type="submit" 
                                    class="px-4 py-2 rounded-xl bg-[#1E293B] hover:bg-[#0F172A] text-white text-[12.5px] font-bold transition flex items-center gap-1.5 cursor-pointer shadow-xs">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                                Terapkan Filter
                            </button>

                            @if(request()->hasAny(['search', 'engineer_id', 'project_id', 'category', 'date_start', 'date_end']))
                                <a href="{{ route('timesheets.index') }}" 
                                   class="px-3.5 py-2 rounded-xl bg-[#F1F5F9] hover:bg-[#E2E8F0] text-[#64748B] hover:text-[#1E293B] text-[12.5px] font-bold transition"
                                   title="Reset Semua Filter">
                                    Reset
                                </a>
                            @endif
                        </div>
                    </div>

                </form>

            </div>

            <!-- ========================================================== -->
            <!-- 3. TIMESHEET TABLE (Clean & High-Contrast Readability)     -->
            <!-- ========================================================== -->
            <div class="ipnet-card overflow-hidden anim-fade-up anim-delay-3">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-[13px]">
                        <thead>
                            <tr class="border-b border-[#E2E8F0] bg-[#F8FAFC] text-[11.5px] font-bold text-[#64748B] uppercase tracking-wider">
                                <th class="py-3.5 px-4 text-center w-12">No</th>
                                <th class="py-3.5 px-4 w-36">Tanggal</th>
                                @if($isLead)
                                    <th class="py-3.5 px-4 w-44">Engineer</th>
                                @endif
                                <th class="py-3.5 px-4 w-60">Project & Task</th>
                                <th class="py-3.5 px-4 text-center w-36">Waktu Kerja</th>
                                <th class="py-3.5 px-4 text-center w-28">Durasi</th>
                                <th class="py-3.5 px-4 text-center w-28">Kategori</th>
                                <th class="py-3.5 px-4 min-w-[280px]">Uraian Aktivitas</th>
                                <th class="py-3.5 px-4 text-center w-24">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#F1F5F9]">
                            @php
                                $daysIndo = [
                                    'Sunday'    => 'Minggu',
                                    'Monday'    => 'Senin',
                                    'Tuesday'   => 'Selasa',
                                    'Wednesday' => 'Rabu',
                                    'Thursday'  => 'Kamis',
                                    'Friday'    => 'Jumat',
                                    'Saturday'  => 'Sabtu',
                                ];
                            @endphp

                            @forelse($timesheets as $index => $ts)
                                @php
                                    $dayName = $ts->date ? ($daysIndo[$ts->date->format('l')] ?? $ts->date->format('l')) : '';
                                    $initials = strtoupper(substr($ts->user?->name ?? 'U', 0, 2));
                                @endphp
                                <tr class="hover:bg-[#F8FAFC]/80 transition-colors">
                                    {{-- No --}}
                                    <td class="py-3.5 px-4 text-center text-[#64748B] text-[12px] font-mono">
                                        {{ $timesheets->firstItem() + $index }}
                                    </td>

                                    {{-- Tanggal --}}
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <div class="font-bold text-[#1E293B] text-[13px]">
                                            {{ $ts->date ? $ts->date->format('d M Y') : '-' }}
                                        </div>
                                        <div class="text-[11.5px] font-medium text-[#64748B] mt-0.5">
                                            {{ $dayName }}
                                        </div>
                                    </td>

                                    {{-- Engineer --}}
                                    @if($isLead)
                                        <td class="py-3.5 px-4 whitespace-nowrap">
                                            <div class="flex items-center gap-2.5">
                                                <div class="w-7 h-7 rounded-full text-white text-[10px] font-bold flex items-center justify-center flex-shrink-0 shadow-xs"
                                                     style="background: linear-gradient(135deg, #B91C1C 0%, #8F0A0D 60%, #750608 100%);">
                                                    {{ $initials }}
                                                </div>
                                                <span class="font-bold text-[#1E293B] text-[13px]">{{ $ts->user?->name ?? 'Unassigned' }}</span>
                                            </div>
                                        </td>
                                    @endif

                                    {{-- Project & Task --}}
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-[#1E293B] leading-snug text-[13px]">
                                            {{ $ts->project?->name ?? 'Non-Project / Rutin' }}
                                        </div>
                                        @if($ts->task)
                                            <div class="inline-flex items-center gap-1.5 mt-1 px-2 py-0.5 rounded-md bg-[#F1F5F9] text-[#475569] text-[11px] font-medium border border-[#E2E8F0]">
                                                <svg class="w-3 h-3 text-[#94A3B8] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                                </svg>
                                                <span class="truncate max-w-[180px]">{{ $ts->task->title }}</span>
                                            </div>
                                        @endif
                                    </td>

                                    {{-- Waktu Kerja --}}
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        <span class="inline-flex items-center text-[12px] font-mono font-semibold text-[#334155] bg-[#F1F5F9] border border-[#E2E8F0] px-2.5 py-1 rounded-lg">
                                            {{ substr($ts->start_time, 0, 5) }} - {{ substr($ts->end_time, 0, 5) }}
                                        </span>
                                    </td>

                                    {{-- Durasi --}}
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        <span class="font-extrabold text-[#1E293B] text-[13px]">
                                            {{ $ts->formatted_duration }}
                                        </span>
                                    </td>

                                    {{-- Kategori --}}
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        @php
                                            $catColor = $ts->category_color;
                                        @endphp
                                        <span class="inline-block px-2.5 py-1 text-[11px] font-bold rounded-full"
                                              style="background: {{ $catColor['bg'] }}; color: {{ $catColor['text'] }}; border: 1px solid {{ $catColor['border'] }};">
                                            {{ $ts->category }}
                                        </span>
                                    </td>

                                    {{-- Uraian Aktivitas --}}
                                    <td class="py-3.5 px-4">
                                        <p class="text-[#1E293B] leading-relaxed font-normal text-[13px]">{{ $ts->activity }}</p>
                                        @if($ts->notes)
                                            <div class="mt-1.5 text-[11.5px] text-[#64748B] bg-[#F8FAFC] border-l-2 border-[#CBD5E1] pl-2 py-0.5 rounded-r">
                                                <span class="font-bold text-[#475569]">Catatan:</span> {{ $ts->notes }}
                                            </div>
                                        @endif
                                    </td>

                                    {{-- Aksi --}}
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        <div class="flex items-center justify-center gap-1">
                                            {{-- Tombol Lihat Detail --}}
                                            <button type="button" 
                                                    @click="openDetailModal({{ json_encode($ts) }})" 
                                                    class="p-1.5 rounded-lg hover:bg-[#F1F5F9] text-[#64748B] hover:text-[#1E293B] transition cursor-pointer"
                                                    title="Lihat Detail Log">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </button>

                                            @if(!auth()->user()->hasAnyRole(['Direktur', 'HD / Direktur', 'Group Leader', 'Lead Divisi']))
                                                <button type="button"
                                                        @click="openEditModal({{ json_encode($ts) }})" 
                                                        class="p-1.5 rounded-lg hover:bg-[#F1F5F9] text-[#64748B] hover:text-[#1E293B] transition cursor-pointer"
                                                        title="Edit Log">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                    </svg>
                                                </button>
                                                <button type="button"
                                                        @click="promptDelete({{ $ts->id }}, '{{ addslashes($ts->activity) }}')" 
                                                        class="p-1.5 rounded-lg hover:bg-[#FEF2F2] text-[#64748B] hover:text-[#8F0A0D] transition cursor-pointer"
                                                        title="Hapus Log">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $isLead ? 9 : 8 }}" class="py-14 text-center text-[#64748B]">
                                        <div class="w-12 h-12 rounded-2xl bg-[#F8FAFC] border border-[#E2E8F0] flex items-center justify-center mx-auto mb-3 text-[#94A3B8]">
                                            <svg class="w-6 h-6 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </div>
                                        <p class="text-[14px] font-bold text-[#1E293B]">Belum Ada Catatan Log Timesheet</p>
                                        <p class="text-[12.5px] text-[#64748B] mt-1">Klik tombol "+ Catat Log Kerja" untuk mulai merekam aktivitas harian.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($timesheets->hasPages())
                    <div class="p-3.5 sm:px-5 sm:py-3.5 border-t border-[#E2E8F0] bg-white">
                        {{ $timesheets->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>

    <!-- ============================================================ -->
    <!-- MODAL DETAIL TIMESHEET                                       -->
    <!-- ============================================================ -->
    <template x-teleport="body">
        <div x-show="detailModalOpen" 
             x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-[#0F172A]/60 z-[99999] flex items-center justify-center p-4 backdrop-blur-xs"
             @click.self="detailModalOpen = false">
            
            <div class="bg-white rounded-2xl w-[560px] max-w-full overflow-hidden shadow-[0_20px_60px_rgba(15,23,42,0.25)] text-left animate-fade-in-up border border-[#E2E8F0]">
                
                {{-- Modal Header --}}
                <div class="px-6 py-4 border-b border-[#E2E8F0] flex items-center justify-between bg-[#F8FAFC]">
                    <div>
                        <span class="text-[11px] font-bold text-[#8F0A0D] uppercase tracking-wider">Detail Log Aktivitas</span>
                        <h3 class="font-display text-[16px] font-bold text-[#1E293B]" x-text="detailData?.project?.name || 'Non-Project / Aktivitas Rutin'"></h3>
                    </div>
                    <button type="button" @click="detailModalOpen = false" class="text-[#64748B] hover:text-[#1E293B] p-1.5 rounded-lg hover:bg-[#E2E8F0] transition cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Modal Body --}}
                <div class="p-6 space-y-4 text-[13px]">
                    
                    {{-- Grid Metadata --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div class="p-3.5 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0]">
                            <div class="text-[11px] font-bold text-[#64748B] uppercase tracking-wider mb-1">Engineer Pelaksana</div>
                            <div class="font-bold text-[#1E293B]" x-text="detailData?.user?.name || '-'"></div>
                        </div>

                        <div class="p-3.5 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0]">
                            <div class="text-[11px] font-bold text-[#64748B] uppercase tracking-wider mb-1">Kategori Pekerjaan</div>
                            <span class="inline-block px-2.5 py-0.5 text-[11.5px] font-bold rounded-md bg-white border border-[#CBD5E1] text-[#1E293B]" x-text="detailData?.category || '-'"></span>
                        </div>

                        <div class="p-3.5 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0]">
                            <div class="text-[11px] font-bold text-[#64748B] uppercase tracking-wider mb-1">Tanggal Kerja</div>
                            <div class="font-bold text-[#1E293B]" x-text="detailData?.date ? detailData.date.substring(0, 10) : '-'"></div>
                        </div>

                        <div class="p-3.5 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0]">
                            <div class="text-[11px] font-bold text-[#64748B] uppercase tracking-wider mb-1">Waktu & Durasi</div>
                            <div class="font-bold text-[#1E293B]">
                                <span x-text="(detailData?.start_time ? detailData.start_time.substring(0,5) : '') + ' - ' + (detailData?.end_time ? detailData.end_time.substring(0,5) : '')"></span>
                                <span class="text-[#8F0A0D] font-extrabold ml-1" x-text="detailData?.formatted_duration ? '(' + detailData.formatted_duration + ')' : (detailData?.duration_minutes ? '(' + Math.floor(detailData.duration_minutes/60) + 'j ' + (detailData.duration_minutes%60) + 'm)' : '')"></span>
                            </div>
                        </div>
                    </div>

                    {{-- Task Spesifik --}}
                    <div class="p-3.5 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0]" x-show="detailData?.task">
                        <div class="text-[11px] font-bold text-[#64748B] uppercase tracking-wider mb-1">Task Terkait</div>
                        <div class="font-bold text-[#1E293B]" x-text="detailData?.task?.title || '-'"></div>
                    </div>

                    {{-- Uraian Aktivitas --}}
                    <div class="p-4 rounded-xl bg-white border border-[#CBD5E1]">
                        <div class="text-[11px] font-bold text-[#64748B] uppercase tracking-wider mb-1.5">Uraian Aktivitas Pekerjaan</div>
                        <p class="text-[#1E293B] leading-relaxed whitespace-pre-line text-[13px]" x-text="detailData?.activity || '-'"></p>
                    </div>

                    {{-- Catatan Tambahan --}}
                    <div class="p-4 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0]" x-show="detailData?.notes">
                        <div class="text-[11px] font-bold text-[#64748B] uppercase tracking-wider mb-1">Catatan Tambahan / Kendala</div>
                        <p class="text-[#475569] leading-relaxed italic text-[12.5px]" x-text="detailData?.notes || '-'"></p>
                    </div>

                </div>

            </div>
        </div>
    </template>

    <!-- ============================================================ -->
    <!-- MODAL TAMBAH / EDIT TIMESHEET                                -->
    <!-- ============================================================ -->
    <template x-teleport="body">
        <div x-show="formModalOpen" 
             x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-[#0F172A]/60 z-[99999] flex items-center justify-center p-4 backdrop-blur-xs"
             @click.self="formModalOpen = false">
            
            <div class="bg-white rounded-2xl w-[560px] max-w-full overflow-hidden shadow-[0_20px_60px_rgba(15,23,42,0.25)] text-left animate-fade-in-up border border-[#E2E8F0]">
                
                {{-- Modal Header --}}
                <div class="px-6 py-4 border-b border-[#E2E8F0] flex items-center justify-between bg-[#F8FAFC]">
                    <h3 class="text-[16px] font-bold text-[#1E293B]" x-text="isEditing ? 'Edit Log Aktivitas Kerja' : 'Catat Log Aktivitas Kerja Baru'"></h3>
                    <button type="button" @click="formModalOpen = false" class="text-[#64748B] hover:text-[#1E293B] p-1.5 rounded-lg hover:bg-[#E2E8F0] transition cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Form Body --}}
                <form :action="isEditing ? `/timesheets/${formData.id}` : '{{ route('timesheets.store') }}'" method="POST" class="p-6 flex flex-col gap-4">
                    @csrf
                    <template x-if="isEditing">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    {{-- Engineer Selector (Lead Only) --}}
                    @if($isLead)
                        <div>
                            <label class="block text-[11.5px] font-bold text-[#475569] uppercase tracking-wider mb-1.5">Engineer Pelaksana</label>
                            <select name="engineer_id" x-model="formData.engineer_id" class="w-full py-2.5 px-3.5 text-[13px] font-semibold bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 focus:bg-white transition text-[#1E293B]">
                                @foreach($engineers as $eng)
                                    <option value="{{ $eng->id }}">{{ $eng->name }} ({{ $eng->role_label }})</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- Project --}}
                        <div>
                            <label class="block text-[11.5px] font-bold text-[#475569] uppercase tracking-wider mb-1.5">Project Terkait</label>
                            <select name="project_id" x-model="formData.project_id" class="w-full py-2.5 px-3.5 text-[13px] font-semibold bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 focus:bg-white transition text-[#1E293B]">
                                <option value="">-- Tanpa Project / Rutin --</option>
                                @foreach($projects as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Task --}}
                        <div>
                            <label class="block text-[11.5px] font-bold text-[#475569] uppercase tracking-wider mb-1.5">Task Spesifik (Opsional)</label>
                            <select name="task_id" x-model="formData.task_id" class="w-full py-2.5 px-3.5 text-[13px] font-semibold bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 focus:bg-white transition text-[#1E293B]">
                                <option value="">-- Pilih Task (Bila ada) --</option>
                                @foreach($myTasks as $task)
                                    <option value="{{ $task->id }}">{{ $task->title }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Tanggal, Jam Mulai & Jam Selesai --}}
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-[11.5px] font-bold text-[#475569] uppercase tracking-wider mb-1.5">Tanggal</label>
                            <input type="date" name="date" x-model="formData.date" required class="w-full py-2.5 px-3 text-[13px] font-semibold bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 focus:bg-white transition text-[#1E293B]">
                        </div>
                        <div>
                            <label class="block text-[11.5px] font-bold text-[#475569] uppercase tracking-wider mb-1.5">Jam Mulai</label>
                            <input type="time" name="start_time" x-model="formData.start_time" required class="w-full py-2.5 px-3 text-[13px] font-semibold bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 focus:bg-white transition text-[#1E293B]">
                        </div>
                        <div>
                            <label class="block text-[11.5px] font-bold text-[#475569] uppercase tracking-wider mb-1.5">Jam Selesai</label>
                            <input type="time" name="end_time" x-model="formData.end_time" required class="w-full py-2.5 px-3 text-[13px] font-semibold bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 focus:bg-white transition text-[#1E293B]">
                        </div>
                    </div>

                    {{-- Kategori Pekerjaan --}}
                    <div>
                        <label class="block text-[11.5px] font-bold text-[#475569] uppercase tracking-wider mb-1.5">Kategori Pekerjaan</label>
                        <select name="category" x-model="formData.category" required class="w-full py-2.5 px-3.5 text-[13px] font-semibold bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 focus:bg-white transition text-[#1E293B]">
                            <option value="On-Site">On-Site / Lapangan</option>
                            <option value="Remote">Remote / Konfigurasi</option>
                            <option value="Overtime">Lembur / Overtime</option>
                            <option value="Maintenance">Standby / Maintenance</option>
                        </select>
                    </div>

                    {{-- Uraian Aktivitas --}}
                    <div>
                        <label class="block text-[11.5px] font-bold text-[#475569] uppercase tracking-wider mb-1.5">Uraian Aktivitas Pekerjaan *</label>
                        <textarea name="activity" 
                                  x-model="formData.activity" 
                                  rows="3" 
                                  required 
                                  placeholder="Jelaskan secara ringkas pekerjaan yang Anda kerjakan..."
                                  class="w-full py-2.5 px-3.5 text-[13px] bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 focus:bg-white transition text-[#1E293B]"></textarea>
                    </div>

                    {{-- Catatan / Kendala --}}
                    <div>
                        <label class="block text-[11.5px] font-bold text-[#475569] uppercase tracking-wider mb-1.5">Catatan Tambahan / Kendala (Opsional)</label>
                        <input type="text" 
                               name="notes" 
                               x-model="formData.notes" 
                               placeholder="Contoh: Menunggu material fiber optik tambahan, konfigurasi selesai..."
                               class="w-full py-2.5 px-3.5 text-[13px] bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 focus:bg-white transition text-[#1E293B]">
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-[#E2E8F0]">
                        <button type="button" @click="formModalOpen = false" class="py-2.5 px-4 rounded-xl bg-white text-[#334155] border border-[#CBD5E1] font-bold text-[13px] hover:bg-[#F8FAFC] transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="btn-ipnet-gradient py-2.5 px-5 rounded-xl font-bold text-[13px] transition shadow-md cursor-pointer">
                            Simpan Log
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>

    <!-- ============================================================ -->
    <!-- MODAL KONFIRMASI HAPUS                                       -->
    <!-- ============================================================ -->
    <template x-teleport="body">
        <div x-show="deleteModalOpen" 
             x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-[#0F172A]/60 z-[99999] flex items-center justify-center p-4 backdrop-blur-xs"
             @click.self="deleteModalOpen = false">
            
            <div class="bg-white rounded-2xl w-[420px] max-w-full p-6 text-left shadow-[0_20px_60px_rgba(15,23,42,0.25)] animate-fade-in-up border border-[#E2E8F0]">
                <div class="w-12 h-12 rounded-full bg-[#FEF2F2] flex items-center justify-center mx-auto mb-4 text-[#8F0A0D]">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>
                
                <h3 class="text-center font-display text-[16px] font-bold text-[#1E293B] mb-1.5">Yakin Hapus Catatan Timesheet?</h3>
                <p class="text-center text-[12.5px] text-[#64748B] mb-6 break-words" x-text="'Log aktivitas: &quot;' + deleteTitle + '&quot; akan dihapus secara permanen.'"></p>

                <form :action="`/timesheets/${deleteId}`" method="POST" class="flex gap-2.5">
                    @csrf
                    @method('DELETE')
                    <button type="button" @click="deleteModalOpen = false" class="flex-1 py-2.5 px-4 rounded-xl bg-white text-[#334155] border border-[#CBD5E1] font-bold text-[12.5px] hover:bg-[#F8FAFC] transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 py-2.5 px-4 rounded-xl btn-ipnet-gradient font-bold text-[12.5px] transition cursor-pointer shadow-md">
                        Hapus
                    </button>
                </form>
            </div>
        </div>
    </template>

</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('timesheetApp', () => ({
            formModalOpen: false,
            deleteModalOpen: false,
            detailModalOpen: false,
            detailData: null,
            isEditing: false,
            deleteId: null,
            deleteTitle: '',
            computedDuration: '0 Jam',
            formData: {
                id: null,
                engineer_id: '{{ auth()->id() }}',
                project_id: '',
                task_id: '',
                date: '{{ now()->format('Y-m-d') }}',
                start_time: '08:30',
                end_time: '17:00',
                category: 'On-Site',
                activity: '',
                notes: ''
            },

            init() {
                this.recalcDuration();
            },

            openDetailModal(item) {
                this.detailData = item;
                this.detailModalOpen = true;
            },

            openAddModal() {
                this.isEditing = false;
                this.formData = {
                    id: null,
                    engineer_id: '{{ auth()->id() }}',
                    project_id: '',
                    task_id: '',
                    date: '{{ now()->format('Y-m-d') }}',
                    start_time: '08:30',
                    end_time: '17:00',
                    category: 'On-Site',
                    activity: '',
                    notes: ''
                };
                this.recalcDuration();
                this.formModalOpen = true;
            },

            openEditModal(item) {
                this.isEditing = true;
                this.formData = {
                    id: item.id,
                    engineer_id: item.user_id,
                    project_id: item.project_id || '',
                    task_id: item.task_id || '',
                    date: item.date ? item.date.substring(0, 10) : '{{ now()->format('Y-m-d') }}',
                    start_time: item.start_time ? item.start_time.substring(0, 5) : '08:30',
                    end_time: item.end_time ? item.end_time.substring(0, 5) : '17:00',
                    category: item.category || 'On-Site',
                    activity: item.activity || '',
                    notes: item.notes || ''
                };
                this.recalcDuration();
                this.formModalOpen = true;
            },

            promptDelete(id, title) {
                this.deleteId = id;
                this.deleteTitle = title || 'Log ini';
                this.deleteModalOpen = true;
            },

            recalcDuration() {
                if (!this.formData.start_time || !this.formData.end_time) {
                    this.computedDuration = '0 Jam';
                    return;
                }
                const [startH, startM] = this.formData.start_time.split(':').map(Number);
                const [endH, endM] = this.formData.end_time.split(':').map(Number);
                
                let startMins = startH * 60 + startM;
                let endMins = endH * 60 + endM;
                
                if (endMins < startMins) {
                    endMins += 24 * 60; // Crossing midnight
                }
                
                const diffMins = endMins - startMins;
                const hours = Math.floor(diffMins / 60);
                const mins = diffMins % 60;

                if (hours > 0 && mins > 0) {
                    this.computedDuration = `${(diffMins / 60).toFixed(1)} Jam`;
                } else if (hours > 0) {
                    this.computedDuration = `${hours} Jam`;
                } else {
                    this.computedDuration = `${mins} Menit`;
                }
            }
        }));
    });
</script>
@endsection
