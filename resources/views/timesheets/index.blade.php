@extends('layouts.app')

@section('title', 'Timesheet & Log Aktivitas')

@section('content')
<div class="flex h-screen overflow-hidden" x-data="timesheetApp()">
    @include('components.sidebar')

    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Timesheet & Log Aktivitas'])

        <div class="p-4 sm:p-5 lg:p-[26px] animate-fade-in space-y-4">
            
            {{-- Toast Feedback Alerts --}}
            @if(session('success'))
                <div class="p-3.5 px-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-[13px] flex items-center justify-between shadow-sm animate-fade-in">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="font-medium">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="p-3.5 px-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-[13px] flex items-center justify-between shadow-sm animate-fade-in">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <span class="font-medium">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            <!-- 1. STATS METRIC CARDS -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 sm:gap-4">
                {{-- Card 1: Jam Minggu Ini --}}
                <div class="wms-card p-4 sm:p-5 relative overflow-hidden bg-white flex flex-col justify-between hover:shadow-md transition-shadow">
                    <div class="absolute top-0 left-0 right-0 h-[3px] bg-gradient-to-r from-[#C81E2C] to-[#991B1B]"></div>
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] sm:text-[11.5px] text-[#75727C] font-bold uppercase tracking-wider">
                            {{ $isLead ? 'Jam Tim (Minggu Ini)' : 'Jam Saya (Minggu Ini)' }}
                        </span>
                        <div class="w-8 h-8 rounded-lg bg-[#FDF1F2] flex items-center justify-center text-[#C81E2C]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-3 flex items-baseline gap-1.5">
                        <span class="font-display text-[28px] sm:text-[32px] font-bold text-[#17151C] leading-none tracking-tight">{{ $totalWeekHours }}</span>
                        <span class="text-[13px] font-semibold text-[#75727C]">Jam</span>
                    </div>
                </div>

                {{-- Card 2: Jam Bulan Ini --}}
                <div class="wms-card p-4 sm:p-5 bg-white flex flex-col justify-between hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] sm:text-[11.5px] text-[#75727C] font-bold uppercase tracking-wider">
                            {{ $isLead ? 'Jam Tim (Bulan Ini)' : 'Jam Saya (Bulan Ini)' }}
                        </span>
                        <div class="w-8 h-8 rounded-lg bg-[#F1F0EE] flex items-center justify-center text-[#3D3A44]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-3 flex items-baseline gap-1.5">
                        <span class="font-display text-[28px] sm:text-[32px] font-bold text-[#17151C] leading-none tracking-tight">{{ $totalMonthHours }}</span>
                        <span class="text-[13px] font-semibold text-[#75727C]">Jam</span>
                    </div>
                </div>

                {{-- Card 3: Total Entri Log --}}
                <div class="wms-card p-4 sm:p-5 bg-white flex flex-col justify-between hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] sm:text-[11.5px] text-[#75727C] font-bold uppercase tracking-wider">Total Entri Log</span>
                        <div class="w-8 h-8 rounded-lg bg-[#F1F0EE] flex items-center justify-center text-[#3D3A44]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-3 flex items-baseline gap-1.5">
                        <span class="font-display text-[28px] sm:text-[32px] font-bold text-[#17151C] leading-none tracking-tight">{{ $totalLogsCount }}</span>
                        <span class="text-[13px] font-semibold text-[#75727C]">Log</span>
                    </div>
                </div>

                {{-- Card 4: Total Jam Lembur --}}
                <div class="wms-card p-4 sm:p-5 bg-white flex flex-col justify-between hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] sm:text-[11.5px] text-[#75727C] font-bold uppercase tracking-wider">Total Jam Lembur</span>
                        <div class="w-8 h-8 rounded-lg bg-[#FEF3C7] flex items-center justify-center text-[#B45309]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-3 flex items-baseline gap-1.5">
                        <span class="font-display text-[28px] sm:text-[32px] font-bold text-[#B45309] leading-none tracking-tight">{{ $totalOvertimeHours }}</span>
                        <span class="text-[13px] font-semibold text-[#75727C]">Jam</span>
                    </div>
                </div>
            </div>

            <!-- 2. UNIFIED ACTION & FILTER BAR (Clean, Proportional, No Floating Buttons) -->
            <div class="wms-card p-4 sm:p-5 bg-white space-y-3.5">
                
                {{-- Form Filter Terpadu --}}
                <form method="GET" action="{{ route('timesheets.index') }}" id="timesheetFilterForm" class="space-y-3.5">
                    
                    {{-- Row 1: Search Bar & Action Buttons --}}
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
                        
                        {{-- Search Input --}}
                        <div class="relative flex-1 max-w-lg">
                            <svg class="w-4 h-4 text-[#948F99] absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input type="text" 
                                   name="search" 
                                   value="{{ request('search') }}" 
                                   placeholder="Cari aktivitas, task, atau catatan..."
                                   class="w-full pl-10 pr-4 py-2.5 text-[13px] bg-[#FBFBFA] border border-[#E7E5E3] rounded-xl focus:outline-none focus:border-[#C81E2C] focus:bg-white transition text-[#17151C] shadow-sm">
                        </div>

                        {{-- Action Buttons (Export & Tambah) --}}
                        <div class="flex items-center gap-2.5 justify-end">
                            
                            {{-- Dropdown Export --}}
                            <div class="relative" x-data="{ exportOpen: false }">
                                <button type="button" 
                                        @click="exportOpen = !exportOpen"
                                        class="px-3.5 py-2.5 rounded-xl border border-[#E7E5E3] bg-white hover:bg-[#F8F7F6] text-[#17151C] text-[13px] font-semibold transition flex items-center gap-2 shadow-sm cursor-pointer">
                                    <svg class="w-4 h-4 text-[#75727C]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                    Export Laporan
                                    <svg class="w-3.5 h-3.5 text-[#75727C] transition-transform duration-200" :class="exportOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>

                                <div x-show="exportOpen" 
                                     x-cloak
                                     @click.outside="exportOpen = false"
                                     class="absolute right-0 mt-2 w-52 bg-white border border-[#E7E5E3] rounded-2xl shadow-xl z-30 py-1.5 overflow-hidden animate-fade-in-up"
                                     style="display: none;">
                                    
                                    {{-- Export Excel --}}
                                    <a href="{{ route('timesheets.export.excel', request()->all()) }}" 
                                       class="flex items-center gap-3 px-4 py-2.5 text-[13px] font-medium text-[#17151C] hover:bg-emerald-50 hover:text-emerald-700 transition">
                                        <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zM6 4h7v5h5v11H6V4zm2 8h2.5l1.5 2.5 1.5-2.5H16l-2.25 3.5L16 19h-2.5L12 16.5 10.5 19H8l2.25-3.5L8 12z"/>
                                        </svg>
                                        Export Excel (.xlsx)
                                    </a>

                                    {{-- Export PDF --}}
                                    <a href="{{ route('timesheets.export.pdf', request()->all()) }}" 
                                       class="flex items-center gap-3 px-4 py-2.5 text-[13px] font-medium text-[#17151C] hover:bg-red-50 hover:text-red-700 transition border-t border-[#F1F0EE]">
                                        <svg class="w-4 h-4 text-red-600 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14H9v-2h2v2zm0-4H9V7h2v5zm4 4h-2v-6h2v6z"/>
                                        </svg>
                                        Export PDF (.pdf)
                                    </a>
                                </div>
                            </div>

                            {{-- Tombol Tambah Log --}}
                            <button type="button" 
                                    @click="openAddModal()"
                                    class="px-4 py-2.5 rounded-xl bg-[#C81E2C] hover:bg-[#A31622] text-white text-[13px] font-semibold transition flex items-center gap-2 shadow-[0_4px_14px_rgba(200,30,44,0.25)] cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                                </svg>
                                Catat Log Kerja
                            </button>
                        </div>

                    </div>

                    {{-- Row 2: Filter Selectors & Date Range Bar --}}
                    <div class="pt-3 border-t border-[#EFEDEB] flex flex-wrap items-center justify-between gap-3">
                        
                        <div class="flex flex-wrap items-center gap-2.5 flex-1">
                            {{-- Filter Engineer (Hanya Lead Engineer) --}}
                            @if($isLead)
                                <div class="min-w-[150px] flex-1 sm:flex-initial">
                                    <select name="engineer_id" onchange="this.form.submit()" class="w-full py-2 px-3 text-[12.5px] bg-[#FBFBFA] border border-[#E7E5E3] rounded-xl focus:outline-none focus:border-[#C81E2C] focus:bg-white transition text-[#17151C] cursor-pointer">
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
                            <div class="min-w-[150px] flex-1 sm:flex-initial">
                                <select name="project_id" onchange="this.form.submit()" class="w-full py-2 px-3 text-[12.5px] bg-[#FBFBFA] border border-[#E7E5E3] rounded-xl focus:outline-none focus:border-[#C81E2C] focus:bg-white transition text-[#17151C] cursor-pointer">
                                    <option value="">Semua Project</option>
                                    @foreach($projects as $p)
                                        <option value="{{ $p->id }}" {{ request('project_id') == $p->id ? 'selected' : '' }}>
                                            {{ $p->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Filter Kategori --}}
                            <div class="min-w-[140px] flex-1 sm:flex-initial">
                                <select name="category" onchange="this.form.submit()" class="w-full py-2 px-3 text-[12.5px] bg-[#FBFBFA] border border-[#E7E5E3] rounded-xl focus:outline-none focus:border-[#C81E2C] focus:bg-white transition text-[#17151C] cursor-pointer">
                                    <option value="">Semua Kategori</option>
                                    <option value="On-Site" {{ request('category') == 'On-Site' ? 'selected' : '' }}>On-Site</option>
                                    <option value="Remote" {{ request('category') == 'Remote' ? 'selected' : '' }}>Remote</option>
                                    <option value="Overtime" {{ request('category') == 'Overtime' ? 'selected' : '' }}>Lembur</option>
                                    <option value="Maintenance" {{ request('category') == 'Maintenance' ? 'selected' : '' }}>Maintenance</option>
                                </select>
                            </div>

                            {{-- Rentang Tanggal --}}
                            <div class="flex items-center gap-1.5 bg-[#FBFBFA] border border-[#E7E5E3] rounded-xl px-3 py-1.5">
                                <svg class="w-3.5 h-3.5 text-[#75727C]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <input type="date" 
                                       name="date_start" 
                                       value="{{ request('date_start') }}" 
                                       title="Dari Tanggal"
                                       class="py-0.5 px-1 text-[12.5px] bg-transparent border-none focus:outline-none text-[#17151C] cursor-pointer">
                                <span class="text-[#948F99] text-[12px]">s/d</span>
                                <input type="date" 
                                       name="date_end" 
                                       value="{{ request('date_end') }}" 
                                       title="Sampai Tanggal"
                                       class="py-0.5 px-1 text-[12.5px] bg-transparent border-none focus:outline-none text-[#17151C] cursor-pointer">
                            </div>
                        </div>

                        {{-- Tombol Terapkan Tanggal & Reset --}}
                        <div class="flex items-center gap-2">
                            <button type="submit" 
                                    class="px-3.5 py-2 rounded-xl bg-[#3D3A44] hover:bg-[#17151C] text-white text-[12.5px] font-semibold transition flex items-center gap-1.5 cursor-pointer shadow-sm">
                                Terapkan
                            </button>

                            @if(request()->hasAny(['search', 'engineer_id', 'project_id', 'category', 'date_start', 'date_end']))
                                <a href="{{ route('timesheets.index') }}" 
                                   class="px-3 py-2 rounded-xl bg-[#F1F0EE] hover:bg-[#E7E5E3] text-[#75727C] hover:text-[#17151C] text-[12.5px] font-medium transition"
                                   title="Reset Semua Filter">
                                    Reset
                                </a>
                            @endif
                        </div>

                    </div>
                </form>

            </div>

            <!-- 3. TIMESHEET TABLE (Clean, Spacious, Indonesian Days) -->
            <div class="wms-card overflow-hidden bg-white shadow-sm border border-[#E7E5E3]">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-[#EFEDEB] bg-[#F8F7F6] text-[11px] font-bold text-[#75727C] uppercase tracking-wider">
                                <th class="py-3.5 px-4 text-center w-12">No</th>
                                <th class="py-3.5 px-4 w-36">Tanggal</th>
                                @if($isLead)
                                    <th class="py-3.5 px-4 w-44">Engineer</th>
                                @endif
                                <th class="py-3.5 px-4 w-52">Project & Task</th>
                                <th class="py-3.5 px-4 text-center w-36">Waktu Kerja</th>
                                <th class="py-3.5 px-4 text-center w-28">Durasi</th>
                                <th class="py-3.5 px-4 text-center w-28">Kategori</th>
                                <th class="py-3.5 px-4 min-w-[240px]">Uraian Aktivitas</th>
                                <th class="py-3.5 px-4 text-center w-20">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#EFEDEB] text-[13px]">
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
                                <tr class="hover:bg-[#FBFBFA] transition-colors">
                                    <td class="py-3.5 px-4 text-center text-[#75727C] text-[12px] font-mono">
                                        {{ $timesheets->firstItem() + $index }}
                                    </td>
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <div class="font-semibold text-[#17151C] text-[13.5px]">
                                            {{ $ts->date ? $ts->date->format('d M Y') : '-' }}
                                        </div>
                                        <div class="text-[11.5px] font-medium text-[#75727C] mt-0.5">
                                            {{ $dayName }}
                                        </div>
                                    </td>
                                    @if($isLead)
                                        <td class="py-3.5 px-4 whitespace-nowrap">
                                            <div class="flex items-center gap-2.5">
                                                <div class="w-7 h-7 rounded-full bg-[#FDF1F2] border border-[#FADADF] text-[#C81E2C] text-[10.5px] font-bold flex items-center justify-center flex-shrink-0">
                                                    {{ $initials }}
                                                </div>
                                                <span class="font-semibold text-[#17151C] text-[13px]">{{ $ts->user?->name ?? 'Unassigned' }}</span>
                                            </div>
                                        </td>
                                    @endif
                                    <td class="py-3.5 px-4">
                                        <div class="font-semibold text-[#17151C] leading-snug">{{ $ts->project?->name ?? 'Non-Project / Rutin' }}</div>
                                        @if($ts->task)
                                            <div class="text-[11.5px] text-[#75727C] flex items-center gap-1.5 mt-1 bg-[#F8F7F6] px-2 py-0.5 rounded-md inline-flex border border-[#EFEDEB]">
                                                <svg class="w-3 h-3 text-[#948F99] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                                </svg>
                                                <span class="truncate max-w-[160px]">{{ $ts->task->title }}</span>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        <span class="inline-flex items-center text-[12px] font-mono font-medium text-[#3D3A44] bg-[#F1F0EE] px-2.5 py-1 rounded-lg">
                                            {{ substr($ts->start_time, 0, 5) }} - {{ substr($ts->end_time, 0, 5) }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        <span class="font-bold text-[#17151C] text-[13.5px]">
                                            {{ $ts->formatted_duration }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        @php
                                            $catColor = $ts->category_color;
                                        @endphp
                                        <span class="inline-block px-3 py-1 text-[11px] font-semibold rounded-lg"
                                              style="background: {{ $catColor['bg'] }}; color: {{ $catColor['text'] }}; border: 1px solid {{ $catColor['border'] }};">
                                            {{ $ts->category }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <p class="text-[#17151C] leading-snug font-normal text-[13px]">{{ $ts->activity }}</p>
                                        @if($ts->notes)
                                            <p class="text-[11.5px] text-[#75727C] mt-1 italic flex items-center gap-1">
                                                <span class="font-semibold not-italic text-[#948F99]">Catatan:</span> {{ $ts->notes }}
                                            </p>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        <div class="flex items-center justify-center gap-1">
                                            <button @click="openEditModal({{ json_encode($ts) }})" 
                                                    class="p-1.5 rounded-lg hover:bg-[#F1F0EE] text-[#75727C] hover:text-[#17151C] transition cursor-pointer"
                                                    title="Edit Log">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </button>
                                            <button @click="promptDelete({{ $ts->id }}, '{{ addslashes($ts->activity) }}')" 
                                                    class="p-1.5 rounded-lg hover:bg-red-50 text-[#75727C] hover:text-[#C81E2C] transition cursor-pointer"
                                                    title="Hapus Log">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $isLead ? 9 : 8 }}" class="py-14 text-center text-[#75727C]">
                                        <div class="w-12 h-12 rounded-2xl bg-[#F1F0EE] flex items-center justify-center mx-auto mb-3 text-[#75727C]">
                                            <svg class="w-6 h-6 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </div>
                                        <p class="text-[14.5px] font-semibold text-[#17151C]">Belum ada catatan log timesheet</p>
                                        <p class="text-[12.5px] text-[#75727C] mt-1">Klik tombol "+ Catat Log Kerja" untuk mulai merekam aktivitas harian.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($timesheets->hasPages())
                    <div class="p-4 border-t border-[#EFEDEB] bg-[#FAF9F8]">
                        {{ $timesheets->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>

    <!-- MODAL TAMBAH / EDIT TIMESHEET -->
    <div x-show="formModalOpen" 
         x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-[#0E0D12]/60 z-50 flex items-center justify-center p-4 backdrop-blur-sm"
         @click.self="formModalOpen = false"
         style="display: none;">
        
        <div class="bg-white rounded-2xl w-[560px] max-w-full overflow-hidden shadow-[0_20px_60px_rgba(14,13,18,0.2)] text-left animate-fade-in-up">
            
            {{-- Modal Header --}}
            <div class="px-6 py-4 border-b border-[#EFEDEB] flex items-center justify-between bg-[#FBFBFA]">
                <h3 class="font-display text-[16px] font-bold text-[#17151C]" x-text="isEditing ? 'Edit Log Aktivitas Kerja' : 'Catat Log Aktivitas Kerja Baru'"></h3>
                <button type="button" @click="formModalOpen = false" class="text-[#75727C] hover:text-[#17151C] p-1 rounded-lg hover:bg-[#F1F0EE] transition cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Form Body --}}
            <form :action="isEditing ? `/timesheets/${formData.id}` : '{{ route('timesheets.store') }}'" method="POST" class="p-6 space-y-4">
                @csrf
                <template x-if="isEditing">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                {{-- Engineer Selector (Lead Only) --}}
                @if($isLead)
                    <div>
                        <label class="block text-[12px] font-bold text-[#75727C] uppercase tracking-wider mb-1.5">Engineer Pelaksana</label>
                        <select name="engineer_id" x-model="formData.engineer_id" class="w-full py-2.5 px-3.5 text-[13.5px] bg-[#FBFBFA] border border-[#E7E5E3] rounded-xl focus:outline-none focus:border-[#C81E2C] focus:bg-white transition text-[#17151C]">
                            @foreach($engineers as $eng)
                                <option value="{{ $eng->id }}">{{ $eng->name }} ({{ $eng->role_label }})</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Project --}}
                    <div>
                        <label class="block text-[12px] font-bold text-[#75727C] uppercase tracking-wider mb-1.5">Project Terkait</label>
                        <select name="project_id" x-model="formData.project_id" class="w-full py-2.5 px-3.5 text-[13.5px] bg-[#FBFBFA] border border-[#E7E5E3] rounded-xl focus:outline-none focus:border-[#C81E2C] focus:bg-white transition text-[#17151C]">
                            <option value="">-- Tanpa Project / Rutin --</option>
                            @foreach($projects as $p)
                                <option value="{{ $p->id }}">{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Task --}}
                    <div>
                        <label class="block text-[12px] font-bold text-[#75727C] uppercase tracking-wider mb-1.5">Task Spesifik (Opsional)</label>
                        <select name="task_id" x-model="formData.task_id" class="w-full py-2.5 px-3.5 text-[13.5px] bg-[#FBFBFA] border border-[#E7E5E3] rounded-xl focus:outline-none focus:border-[#C81E2C] focus:bg-white transition text-[#17151C]">
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
                        <label class="block text-[12px] font-bold text-[#75727C] uppercase tracking-wider mb-1.5">Tanggal</label>
                        <input type="date" name="date" x-model="formData.date" required class="w-full py-2.5 px-3 text-[13px] bg-[#FBFBFA] border border-[#E7E5E3] rounded-xl focus:outline-none focus:border-[#C81E2C] focus:bg-white transition text-[#17151C]">
                    </div>
                    <div>
                        <label class="block text-[12px] font-bold text-[#75727C] uppercase tracking-wider mb-1.5">Jam Mulai</label>
                        <input type="time" name="start_time" x-model="formData.start_time" required class="w-full py-2.5 px-3 text-[13px] bg-[#FBFBFA] border border-[#E7E5E3] rounded-xl focus:outline-none focus:border-[#C81E2C] focus:bg-white transition text-[#17151C]">
                    </div>
                    <div>
                        <label class="block text-[12px] font-bold text-[#75727C] uppercase tracking-wider mb-1.5">Jam Selesai</label>
                        <input type="time" name="end_time" x-model="formData.end_time" required class="w-full py-2.5 px-3 text-[13px] bg-[#FBFBFA] border border-[#E7E5E3] rounded-xl focus:outline-none focus:border-[#C81E2C] focus:bg-white transition text-[#17151C]">
                    </div>
                </div>

                {{-- Kategori Pekerjaan --}}
                <div>
                    <label class="block text-[12px] font-bold text-[#75727C] uppercase tracking-wider mb-1.5">Kategori Pekerjaan</label>
                    <select name="category" x-model="formData.category" required class="w-full py-2.5 px-3.5 text-[13.5px] bg-[#FBFBFA] border border-[#E7E5E3] rounded-xl focus:outline-none focus:border-[#C81E2C] focus:bg-white transition text-[#17151C]">
                        <option value="On-Site">On-Site / Lapangan</option>
                        <option value="Remote">Remote / Konfigurasi</option>
                        <option value="Overtime">Lembur / Overtime</option>
                        <option value="Maintenance">Standby / Maintenance</option>
                    </select>
                </div>

                {{-- Uraian Aktivitas --}}
                <div>
                    <label class="block text-[12px] font-bold text-[#75727C] uppercase tracking-wider mb-1.5">Uraian Aktivitas Pekerjaan *</label>
                    <textarea name="activity" 
                              x-model="formData.activity" 
                              rows="3" 
                              required 
                              placeholder="Jelaskan secara ringkas pekerjaan yang Anda kerjakan..."
                              class="w-full py-2.5 px-3.5 text-[13.5px] bg-[#FBFBFA] border border-[#E7E5E3] rounded-xl focus:outline-none focus:border-[#C81E2C] focus:bg-white transition text-[#17151C]"></textarea>
                </div>

                {{-- Catatan / Kendala --}}
                <div>
                    <label class="block text-[12px] font-bold text-[#75727C] uppercase tracking-wider mb-1.5">Catatan Tambahan / Kendala (Opsional)</label>
                    <input type="text" 
                           name="notes" 
                           x-model="formData.notes" 
                           placeholder="Contoh: Menunggu material fiber optik tambahan, konfigurasi switch selesai..."
                           class="w-full py-2.5 px-3.5 text-[13.5px] bg-[#FBFBFA] border border-[#E7E5E3] rounded-xl focus:outline-none focus:border-[#C81E2C] focus:bg-white transition text-[#17151C]">
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-end gap-3 pt-3 border-t border-[#EFEDEB]">
                    <button type="button" @click="formModalOpen = false" class="py-2.5 px-4 rounded-xl bg-white text-[#3D3A44] border border-[#E7E5E3] font-semibold text-[13.5px] hover:bg-[#F8F7F6] transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="py-2.5 px-5 rounded-xl bg-[#C81E2C] hover:bg-[#A31622] text-white font-semibold text-[13.5px] transition shadow-sm cursor-pointer">
                        Simpan Log
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL KONFIRMASI HAPUS -->
    <div x-show="deleteModalOpen" 
         x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-[#0E0D12]/60 z-50 flex items-center justify-center p-4 backdrop-blur-sm"
         @click.self="deleteModalOpen = false"
         style="display: none;">
        
        <div class="bg-white rounded-2xl w-[420px] max-w-full p-6 text-left shadow-[0_20px_60px_rgba(14,13,18,0.2)] animate-fade-in-up">
            <div class="w-14 h-14 rounded-full bg-[#FEF2F2] flex items-center justify-center mx-auto mb-4 text-[#C81E2C]">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </div>
            
            <h3 class="text-center font-display text-[17px] font-bold text-[#17151C] mb-2">Hapus Catatan Timesheet?</h3>
            <p class="text-center text-[13.5px] text-[#75727C] mb-6 break-words" x-text="'Log aktivitas: &quot;' + deleteTitle + '&quot; akan dihapus.'"></p>

            <form :action="`/timesheets/${deleteId}`" method="POST" class="flex gap-3">
                @csrf
                @method('DELETE')
                <button type="submit" class="flex-1 py-2.5 px-4 rounded-xl bg-[#C81E2C] text-white font-semibold text-[13.5px] hover:bg-[#A31622] transition cursor-pointer">
                    Hapus
                </button>
                <button type="button" @click="deleteModalOpen = false" class="flex-1 py-2.5 px-4 rounded-xl bg-white text-[#3D3A44] border border-[#E7E5E3] font-semibold text-[13.5px] hover:bg-[#F8F7F6] transition cursor-pointer">
                    Batal
                </button>
            </form>
        </div>
    </div>

</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('timesheetApp', () => ({
            formModalOpen: false,
            deleteModalOpen: false,
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
