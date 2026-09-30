@extends('layouts.app')

@section('title', 'Dashboard - PT IP Network Solusindo')

@push('styles')
<style>
    /* ========================================================
       IPNET Official Brand Design System (ipnetsolusindo.com)
       ======================================================== */
    .ipnet-hero-banner {
        background: linear-gradient(125deg, #B81525 0%, #9E0E1D 40%, #830B17 75%, #63050F 100%);
        position: relative;
        overflow: hidden;
    }

    .ipnet-card {
        background-color: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04), 0 4px 12px rgba(0, 0, 0, 0.02);
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .ipnet-metric-card {
        background-color: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 16px;
        padding: 16px 18px;
        position: relative;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
        transition: all 0.2s ease;
    }

    .ipnet-metric-card:hover {
        transform: translateY(-2px);
        border-color: #CBD5E1;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);
    }

    .ipnet-badge-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background-color: #8F0A0D;
        display: inline-block;
        margin-right: 8px;
    }
    .btn-ipnet-primary {
        background-color: #E01E2E;
        color: #FFFFFF;
        transition: all 0.2s ease;
    }

    /* Fluid Entrance Animations */
    @keyframes heroReveal {
        0% {
            opacity: 0;
            transform: translateY(22px) scale(0.985);
        }
        100% {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    @keyframes fadeUpStagger {
        0% {
            opacity: 0;
            transform: translateY(18px);
        }
        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .anim-hero-reveal {
        animation: heroReveal 0.65s cubic-bezier(0.16, 1, 0.3, 1) both;
    }

    .anim-fade-up {
        animation: fadeUpStagger 0.55s cubic-bezier(0.16, 1, 0.3, 1) both;
    }

    .anim-delay-1 { animation-delay: 0.08s !important; }
    .anim-delay-2 { animation-delay: 0.14s !important; }
    .anim-delay-3 { animation-delay: 0.20s !important; }
    .anim-delay-4 { animation-delay: 0.26s !important; }
    .anim-delay-5 { animation-delay: 0.34s !important; }
</style>
@endpush

@section('content')
<div class="flex h-screen overflow-hidden bg-[#F8FAFC] font-sans">
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Dashboard'])
        
        <div class="p-4 sm:p-6 lg:p-7 space-y-6 max-w-[1600px] mx-auto animate-fade-in">
            
            {{-- ======================================================== --}}
            {{-- 1. EXECUTIVE HERO BANNER (COMPACT FIELD ENGINEER)         --}}
            {{-- ======================================================== --}}
            <div class="ipnet-hero-banner rounded-2xl px-5 py-4 sm:px-6 sm:py-4.5 text-white shadow-md shadow-red-950/15 relative anim-hero-reveal">
                {{-- Layered Geometric Faceted Red Planes --}}
                <div class="absolute inset-0 pointer-events-none overflow-hidden select-none">
                    <svg class="w-full h-full object-cover" viewBox="0 0 1440 200" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
                        <defs>
                            <linearGradient id="redGrad1Eng" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#C61828" />
                                <stop offset="100%" stop-color="#9E0E1D" />
                            </linearGradient>
                            <linearGradient id="redGrad2Eng" x1="100%" y1="0%" x2="0%" y2="100%">
                                <stop offset="0%" stop-color="#B01423" />
                                <stop offset="100%" stop-color="#7A0813" />
                            </linearGradient>
                            <linearGradient id="redGrad3Eng" x1="0%" y1="100%" x2="100%" y2="0%">
                                <stop offset="0%" stop-color="#940E1B" />
                                <stop offset="100%" stop-color="#5A040C" />
                            </linearGradient>
                            <linearGradient id="redGradHighlightEng" x1="0%" y1="0%" x2="100%" y2="50%">
                                <stop offset="0%" stop-color="#FFA8B2" stop-opacity="0.20" />
                                <stop offset="100%" stop-color="#FFFFFF" stop-opacity="0" />
                            </linearGradient>
                            <filter id="facetDropShadowEng" x="-10%" y="-10%" width="130%" height="130%">
                                <feDropShadow dx="-8" dy="10" stdDeviation="14" flood-color="#3A0207" flood-opacity="0.4" />
                            </filter>
                        </defs>

                        <!-- Base Background -->
                        <rect width="1440" height="200" fill="url(#redGrad1Eng)" />

                        <!-- Top-Left Large Diagonal Angled Plane -->
                        <polygon points="0,0 650,0 280,200 0,200" fill="url(#redGrad1Eng)" />

                        <!-- Intersecting Broad Diagonal Facet Strip -->
                        <polygon points="220,0 850,0 1300,200 600,200" fill="url(#redGrad2Eng)" filter="url(#facetDropShadowEng)" />

                        <!-- Crossing Foreground Diagonal Bright Red Plane -->
                        <polygon points="0,0 520,0 1080,200 480,200" fill="url(#redGrad1Eng)" opacity="0.9" filter="url(#facetDropShadowEng)" />

                        <!-- Right Edge Deeper Contrast Facet -->
                        <polygon points="780,0 1440,0 1440,200 1100,200" fill="url(#redGrad3Eng)" filter="url(#facetDropShadowEng)" />

                        <!-- Soft Angular Ambient Highlight Overlays -->
                        <polygon points="0,0 680,0 1120,200 380,200" fill="url(#redGradHighlightEng)" />
                    </svg>
                </div>

                <div class="relative z-10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="max-w-xl">
                        <div class="inline-flex items-center mb-1.5 px-2.5 py-0.5 text-[10.5px] font-bold text-white bg-white/15 backdrop-blur-md rounded-full border border-white/20 tracking-wider uppercase">
                            <span class="w-1.5 h-1.5 mr-1.5 bg-white rounded-full inline-block"></span>
                            PT IP NETWORK SOLUSINDO &bull; DASHBOARD
                        </div>

                        <h1 class="text-[18px] sm:text-[21px] font-extrabold text-white tracking-tight leading-tight">
                            Selamat Datang, {{ auth()->user()->name }}
                        </h1>
                        <p class="mt-0.5 text-[12.5px] text-white/80 leading-relaxed line-clamp-1">
                            Kelola penugasan harian, jadwal operasional lapangan, dan pelaporan timesheet terpadu.
                        </p>
                    </div>

                    <div class="flex items-center gap-2.5 shrink-0">
                        <a href="{{ route('timesheets.index') }}" class="px-3.5 py-2 rounded-xl bg-white text-[#8F0A0D] font-bold text-[12.5px] hover:bg-[#FFF7F6] hover:scale-[1.02] transition-all shadow-sm flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-[#8F0A0D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Input Timesheet</span>
                        </a>
                        <a href="{{ route('attendance.index') }}" class="px-3.5 py-2 rounded-xl bg-white/10 hover:bg-white/20 hover:scale-[1.02] border border-white/25 text-white font-bold text-[12.5px] transition-all flex items-center gap-1.5 backdrop-blur-sm">
                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span>Presensi Lapangan</span>
                        </a>
                    </div>
                </div>
            </div>

            {{-- ======================================================== --}}
            {{-- 2. METRICS CARDS                                         --}}
            {{-- ======================================================== --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5">
                
                {{-- Metric 1: Tugas Saya --}}
                <a href="{{ route('tasks.index') }}" class="ipnet-metric-card group block anim-fade-up anim-delay-1">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[11px] font-bold text-[#75727C] uppercase tracking-wider">Tugas Saya</span>
                        <div class="w-7 h-7 rounded-lg bg-[#E01E2E]/10 text-[#E01E2E] flex items-center justify-center group-hover:bg-[#E01E2E] group-hover:text-white transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                            </svg>
                        </div>
                    </div>
                    <div class="text-[22px] font-extrabold text-[#292929] tracking-tight">{{ $myTasksCount }}</div>
                    <p class="text-[11px] text-[#75727C] mt-0.5">Penugasan aktif</p>
                </a>

                {{-- Metric 2: Jadwal Hari Ini --}}
                <a href="{{ route('schedules.index') }}" class="ipnet-metric-card group block anim-fade-up anim-delay-2">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[11px] font-bold text-[#75727C] uppercase tracking-wider">Jadwal Hari Ini</span>
                        <div class="w-7 h-7 rounded-lg bg-blue-500/10 text-blue-600 flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="text-[22px] font-extrabold text-[#292929] tracking-tight">{{ $todaySchedulesCount }}</div>
                    <p class="text-[11px] text-[#75727C] mt-0.5">Agenda lapangan</p>
                </a>

                {{-- Metric 3: Deadline Terdekat --}}
                <a href="{{ route('tasks.index') }}" class="ipnet-metric-card group block anim-fade-up anim-delay-3">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[11px] font-bold text-[#75727C] uppercase tracking-wider">Deadline</span>
                        <div class="w-7 h-7 rounded-lg bg-rose-500/10 text-rose-600 flex items-center justify-center group-hover:bg-rose-600 group-hover:text-white transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="text-[20px] font-extrabold text-[#E01E2E] tracking-tight truncate">
                        {{ $nearestDeadline ? $nearestDeadline->deadline->format('d M') : '-' }}
                    </div>
                    <p class="text-[11px] text-[#75727C] mt-0.5">Target penyelesaian</p>
                </a>

                {{-- Metric 4: Rata-rata Progress --}}
                <a href="{{ route('tasks.index') }}" class="ipnet-metric-card group block anim-fade-up anim-delay-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[11px] font-bold text-[#75727C] uppercase tracking-wider">Rata-rata Progress</span>
                        <div class="w-7 h-7 rounded-lg bg-emerald-500/10 text-emerald-600 flex items-center justify-center group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="text-[22px] font-extrabold text-emerald-600 tracking-tight">{{ $avgProgress }}%</div>
                    <p class="text-[11px] text-[#75727C] mt-0.5">Penyelesaian tugas</p>
                </a>

            </div>

            {{-- ======================================================== --}}
            {{-- 3. MY TASKS & TODAY'S SCHEDULES                          --}}
            {{-- ======================================================== --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 anim-fade-up anim-delay-5">
                
                {{-- Tugas Saya --}}
                <div class="ipnet-card overflow-hidden flex flex-col">
                    <div class="p-4 sm:p-5 pb-3 flex items-center justify-between border-b border-[#E2E8F0] bg-[#F8FAFC]">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-4 bg-[#8F0A0D] rounded-full inline-block"></span>
                            <h3 class="text-[15px] font-bold text-[#1E293B]">Tugas Saya</h3>
                        </div>
                        <a href="{{ route('tasks.index') }}" class="text-[#8F0A0D] text-[12px] font-bold hover:underline">Lihat semua</a>
                    </div>
                    <div class="p-3 divide-y divide-[#F1F5F9] overflow-y-auto" style="max-height: 440px;">
                        @forelse($myTasks as $task)
                        <div class="p-3.5 rounded-xl hover:bg-[#F8FAFC] transition-all duration-200">
                            <div class="flex flex-wrap justify-between items-start gap-2 mb-1.5">
                                <span class="text-[13.5px] font-bold text-[#1E293B] break-words min-w-0 flex-1">{{ $task->title }}</span>
                                <x-priority-flag level="{{ $task->priority }}" />
                            </div>
                            <div class="text-[12px] text-[#64748B] mb-2.5 break-words">{{ $task->project?->name ?? 'Tanpa Proyek' }}</div>
                            <div>
                                <x-progress-bar value="{{ $task->progress }}" />
                            </div>
                            <div class="flex flex-wrap justify-between items-center gap-2 mt-2.5 text-[11.5px] text-[#64748B]">
                                <span class="font-bold text-[#1E293B]">{{ $task->status }}</span>
                                <span>Batas Waktu: {{ $task->deadline ? $task->deadline->format('d M Y') : '-' }}</span>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-10 text-[#64748B]">
                            <p class="text-[13.5px]">Belum ada penugasan aktif</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                {{-- Today's Schedule --}}
                <div class="ipnet-card overflow-hidden flex flex-col">
                    <div class="p-4 sm:p-5 pb-3 flex items-center justify-between border-b border-[#E2E8F0] bg-[#F8FAFC]">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-4 bg-blue-600 rounded-full inline-block"></span>
                            <h3 class="text-[15px] font-bold text-[#1E293B]">Jadwal Hari Ini</h3>
                            @if(count($todaySchedules) > 0)
                                <span class="px-2 py-0.5 text-[11px] font-bold rounded-full bg-[#8F0A0D]/10 text-[#8F0A0D]">
                                    {{ count($todaySchedules) }}
                                </span>
                            @endif
                        </div>
                        <span class="text-[12px] font-bold text-[#64748B]">{{ now()->format('d M Y') }}</span>
                    </div>
                    <div class="p-4 space-y-3 overflow-y-auto" style="max-height: 440px;">
                        @forelse($todaySchedules as $schedule)
                        <div class="p-4 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0] hover:border-[#8F0A0D]/30 transition-all duration-200">
                            <div class="flex items-start justify-between gap-2 mb-2">
                                <h4 class="text-[13.5px] font-bold text-[#292929] leading-snug break-words flex-1">
                                    {{ $schedule->title }}
                                </h4>
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-bold bg-[#E01E2E]/10 text-[#E01E2E] shrink-0 border border-[#E01E2E]/20">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                                </span>
                            </div>
                            
                            <p class="text-[12px] font-medium text-[#75727C] mb-2 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-[#E01E2E] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                </svg>
                                <span class="truncate">{{ $schedule->project?->name ?? 'Tanpa Project' }}</span>
                            </p>

                            <div class="pt-2 border-t border-[#F3E5E4] flex items-center text-[12px] text-[#75727C]">
                                <span class="flex items-center gap-1.5 truncate">
                                    <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <span class="truncate">{{ $schedule->location }}</span>
                                </span>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-10 text-[#75727C]">
                            <p class="text-[13.5px]">Tidak ada agenda jadwal hari ini</p>
                        </div>
                        @endforelse
                    </div>
                </div>

            </div>

        </div>

        {{-- ═══════════════════════════════════════════════════════════════════
             SECTION: ACTIVITY LOG ENGINEER
        ════════════════════════════════════════════════════════════════════ --}}
        <div class="mt-6 grid grid-cols-1 lg:grid-cols-5 gap-5">

            {{-- Form Input Activity Log --}}
            <div class="lg:col-span-2">
                <div class="ipnet-card p-5 h-full">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg" style="background:linear-gradient(135deg,#8F0A0D,#D62E3C);">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </span>
                            <div>
                                <h3 class="text-[14px] font-bold text-[#1E293B]">Catat Aktivitas Harian</h3>
                                <p class="text-[11px] text-[#94A3B8]">Log kegiatan kerja kamu hari ini</p>
                            </div>
                        </div>
                        <a href="{{ route('engineer.activity_log.index') }}" class="text-[11.5px] font-bold text-[#8F0A0D] hover:underline flex items-center gap-1">
                            <span>Buka Menu Activity Log &rarr;</span>
                        </a>
                    </div>

                    @if(session('success'))
                        <div class="mb-3 px-3 py-2.5 bg-green-50 border border-green-200 rounded-xl text-[12.5px] text-green-700 flex items-center gap-2">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            {{ session('success') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('engineer.activity_log.store') }}" class="space-y-3">
                        @csrf

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[11.5px] font-semibold text-[#475569] mb-1">Tanggal <span class="text-red-500">*</span></label>
                                <input type="date" name="activity_date" value="{{ date('Y-m-d') }}" required
                                    class="w-full px-3 py-2 text-[12.5px] border border-[#E2E8F0] rounded-lg focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20">
                            </div>
                            <div>
                                <label class="block text-[11.5px] font-semibold text-[#475569] mb-1">Jenis Aktivitas <span class="text-red-500">*</span></label>
                                <select name="activity_type" required
                                    class="w-full px-3 py-2 text-[12.5px] border border-[#E2E8F0] rounded-lg focus:outline-none focus:border-[#8F0A0D] bg-white">
                                    <option>Kegiatan Harian</option>
                                    <option>Kunjungan Klien</option>
                                    <option>Troubleshooting</option>
                                    <option>Preventive Maintenance</option>
                                    <option>Konfigurasi</option>
                                    <option>Instalasi</option>
                                    <option>Meeting Internal</option>
                                    <option>Lainnya</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-[11.5px] font-semibold text-[#475569] mb-1">Deskripsi Kegiatan <span class="text-red-500">*</span></label>
                            <textarea name="description" rows="3" required placeholder="Jelaskan kegiatan yang dilakukan..."
                                class="w-full px-3 py-2 text-[12.5px] border border-[#E2E8F0] rounded-lg focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 resize-none"></textarea>
                        </div>

                        <div>
                            <label class="block text-[11.5px] font-semibold text-[#475569] mb-1">Project Terkait <span class="text-[#94A3B8] font-normal">(opsional)</span></label>
                            <select name="project_id" class="w-full px-3 py-2 text-[12.5px] border border-[#E2E8F0] rounded-lg focus:outline-none focus:border-[#8F0A0D] bg-white">
                                <option value="">-- Tidak Ada / Umum --</option>
                                @foreach($myProjects as $proj)
                                    <option value="{{ $proj->id }}">{{ $proj->name }}{{ $proj->client ? " ({$proj->client})" : '' }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-[11.5px] font-semibold text-[#475569] mb-1">Lokasi <span class="text-[#94A3B8] font-normal">(opsional)</span></label>
                            <input type="text" name="location" placeholder="Contoh: Kantor Klien, Remote, Gudang..."
                                class="w-full px-3 py-2 text-[12.5px] border border-[#E2E8F0] rounded-lg focus:outline-none focus:border-[#8F0A0D]">
                        </div>

                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <label class="block text-[11.5px] font-semibold text-[#475569] mb-1">Jam Mulai</label>
                                <input type="time" name="start_time"
                                    class="w-full px-3 py-2 text-[12.5px] border border-[#E2E8F0] rounded-lg focus:outline-none focus:border-[#8F0A0D]">
                            </div>
                            <div>
                                <label class="block text-[11.5px] font-semibold text-[#475569] mb-1">Jam Selesai</label>
                                <input type="time" name="end_time"
                                    class="w-full px-3 py-2 text-[12.5px] border border-[#E2E8F0] rounded-lg focus:outline-none focus:border-[#8F0A0D]">
                            </div>
                            <div>
                                <label class="block text-[11.5px] font-semibold text-[#475569] mb-1">Status <span class="text-red-500">*</span></label>
                                <select name="status" required class="w-full px-3 py-2 text-[12.5px] border border-[#E2E8F0] rounded-lg focus:outline-none focus:border-[#8F0A0D] bg-white">
                                    <option>Selesai</option>
                                    <option>Sedang Berjalan</option>
                                    <option>Ditunda</option>
                                </select>
                            </div>
                        </div>

                        <button type="submit"
                            class="w-full py-2.5 text-[13px] font-bold text-white rounded-xl transition-all hover:opacity-90 hover:shadow-md"
                            style="background:linear-gradient(135deg,#8F0A0D,#D62E3C);">
                            &#10003; Simpan Activity Log
                        </button>
                    </form>
                </div>
            </div>

            {{-- Riwayat Activity Log Engineer --}}
            <div class="lg:col-span-3">
                <div class="ipnet-card p-5 h-full">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg" style="background:linear-gradient(135deg,#3B82F6,#6366F1);">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </span>
                            <div>
                                <h3 class="text-[14px] font-bold text-[#1E293B]">Riwayat Aktivitas Saya</h3>
                                <p class="text-[11px] text-[#94A3B8]">{{ $myActivityLogs->count() }} log tercatat</p>
                            </div>
                        </div>
                    </div>

                    @if($myActivityLogs->isEmpty())
                        <div class="flex flex-col items-center justify-center py-12 text-center">
                            <div class="w-12 h-12 rounded-full bg-[#F8FAFC] border border-[#E2E8F0] flex items-center justify-center mb-3">
                                <svg class="w-6 h-6 text-[#CBD5E1]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                            <p class="text-[13px] font-medium text-[#94A3B8]">Belum ada activity log</p>
                            <p class="text-[11.5px] text-[#CBD5E1] mt-1">Catat kegiatan harianmu menggunakan form di sebelah kiri</p>
                        </div>
                    @else
                        <div class="space-y-2.5 overflow-y-auto" style="max-height: 520px;">
                            @foreach($myActivityLogs as $log)
                            <div class="flex items-start gap-3 p-3 rounded-xl border border-[#F1F5F9] hover:border-[#E2E8F0] hover:bg-[#FAFAFA] transition-all group">
                                <div class="mt-0.5 shrink-0">
                                    @if($log->status === 'Selesai')
                                        <span class="inline-flex w-6 h-6 items-center justify-center rounded-full bg-green-100">
                                            <svg class="w-3.5 h-3.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        </span>
                                    @elseif($log->status === 'Sedang Berjalan')
                                        <span class="inline-flex w-6 h-6 items-center justify-center rounded-full bg-blue-100">
                                            <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
                                        </span>
                                    @else
                                        <span class="inline-flex w-6 h-6 items-center justify-center rounded-full bg-yellow-100">
                                            <svg class="w-3.5 h-3.5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01"/></svg>
                                        </span>
                                    @endif
                                </div>

                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="min-w-0">
                                            <span class="inline-block text-[10.5px] font-semibold px-2 py-0.5 rounded-full mb-1" style="background:#FEF2F2;color:#8F0A0D;">{{ $log->activity_type }}</span>
                                            <p class="text-[12.5px] font-medium text-[#1E293B] leading-snug">{{ $log->description }}</p>
                                        </div>
                                        <form method="POST" action="{{ route('engineer.activity_log.destroy', $log) }}" onsubmit="return confirm('Hapus log ini?')" class="shrink-0 opacity-0 group-hover:opacity-100 transition-opacity">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="w-6 h-6 flex items-center justify-center rounded-lg text-[#94A3B8] hover:text-red-500 hover:bg-red-50 transition-all">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                    <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-1.5">
                                        <span class="text-[11px] text-[#64748B] flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            {{ \Carbon\Carbon::parse($log->activity_date)->translatedFormat('d M Y') }}
                                        </span>
                                        @if($log->start_time && $log->end_time)
                                        <span class="text-[11px] text-[#64748B] flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            {{ \Carbon\Carbon::parse($log->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($log->end_time)->format('H:i') }}
                                        </span>
                                        @endif
                                        @if($log->location)
                                        <span class="text-[11px] text-[#64748B] flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                            {{ $log->location }}
                                        </span>
                                        @endif
                                        @if($log->project)
                                        <span class="text-[11px] text-[#64748B] flex items-center gap-1 max-w-[150px] truncate">
                                            <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                            <span class="truncate">{{ $log->project->name }}</span>
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

        </div>

    </div>
</div>
@endsection