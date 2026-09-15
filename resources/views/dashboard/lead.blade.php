@extends('layouts.app')

@section('title', 'Dashboard - PT IP Network Solusindo')

@push('styles')
<style>
    /* ========================================================
       IPNET Official Brand Design System (ipnetsolusindo.com)
       ======================================================== */
    :root {
        --ipnet-primary: #8F0A0D;
        --ipnet-primary-hover: #73080A;
        --ipnet-card-bg: #FFFFFF;
        --ipnet-card-border: #E2E8F0;
        --ipnet-text-main: #1E293B;
    }

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

    .ipnet-card-blush {
        background-color: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .ipnet-card-blush:hover, .ipnet-card:hover {
        border-color: #CBD5E1;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
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
        background-color: #8F0A0D;
        color: #FFFFFF;
        transition: all 0.2s ease;
    }

    .btn-ipnet-primary:hover {
        background-color: #73080A;
        box-shadow: 0 6px 16px rgba(143, 10, 13, 0.3);
        transform: translateY(-1px);
    }

    /* ========================================================
       Smooth Fluid Entrance Animations
       ======================================================== */
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
    .anim-delay-5 { animation-delay: 0.32s !important; }
    .anim-delay-6 { animation-delay: 0.38s !important; }
    .anim-delay-7 { animation-delay: 0.44s !important; }
    .anim-delay-8 { animation-delay: 0.52s !important; }
</style>
@endpush

@section('content')
<div class="flex h-screen overflow-hidden bg-[#F8FAFC] font-sans">
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Dashboard'])
        
        <div class="p-4 sm:p-6 lg:p-7 space-y-6 max-w-[1600px] mx-auto animate-fade-in">
            
            {{-- ======================================================== --}}
            {{-- 1. EXECUTIVE HERO BANNER (COMPACT IPNET BRAND PATTERN)   --}}
            {{-- ======================================================== --}}
            <div class="ipnet-hero-banner rounded-2xl px-5 py-4 sm:px-6 sm:py-4.5 text-white shadow-md shadow-red-950/15 relative anim-hero-reveal">
                {{-- Layered Geometric Faceted Red Planes --}}
                <div class="absolute inset-0 pointer-events-none overflow-hidden select-none">
                    <svg class="w-full h-full object-cover" viewBox="0 0 1440 200" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
                        <defs>
                            <linearGradient id="redGrad1" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#C61828" />
                                <stop offset="100%" stop-color="#9E0E1D" />
                            </linearGradient>
                            <linearGradient id="redGrad2" x1="100%" y1="0%" x2="0%" y2="100%">
                                <stop offset="0%" stop-color="#B01423" />
                                <stop offset="100%" stop-color="#7A0813" />
                            </linearGradient>
                            <linearGradient id="redGrad3" x1="0%" y1="100%" x2="100%" y2="0%">
                                <stop offset="0%" stop-color="#940E1B" />
                                <stop offset="100%" stop-color="#5A040C" />
                            </linearGradient>
                            <linearGradient id="redGradHighlight" x1="0%" y1="0%" x2="100%" y2="50%">
                                <stop offset="0%" stop-color="#FFA8B2" stop-opacity="0.20" />
                                <stop offset="100%" stop-color="#FFFFFF" stop-opacity="0" />
                            </linearGradient>
                            <filter id="facetDropShadow" x="-10%" y="-10%" width="130%" height="130%">
                                <feDropShadow dx="-8" dy="10" stdDeviation="14" flood-color="#3A0207" flood-opacity="0.4" />
                            </filter>
                        </defs>

                        <!-- Base Background -->
                        <rect width="1440" height="200" fill="url(#redGrad1)" />

                        <!-- Top-Left Large Diagonal Angled Plane -->
                        <polygon points="0,0 650,0 280,200 0,200" fill="url(#redGrad1)" />

                        <!-- Intersecting Broad Diagonal Facet Strip -->
                        <polygon points="220,0 850,0 1300,200 600,200" fill="url(#redGrad2)" filter="url(#facetDropShadow)" />

                        <!-- Crossing Foreground Diagonal Bright Red Plane -->
                        <polygon points="0,0 520,0 1080,200 480,200" fill="url(#redGrad1)" opacity="0.9" filter="url(#facetDropShadow)" />

                        <!-- Right Edge Deeper Contrast Facet -->
                        <polygon points="780,0 1440,0 1440,200 1100,200" fill="url(#redGrad3)" filter="url(#facetDropShadow)" />

                        <!-- Soft Angular Ambient Highlight Overlays -->
                        <polygon points="0,0 680,0 1120,200 380,200" fill="url(#redGradHighlight)" />
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
                            Pantau kapasitas tim teknisi, progres instalasi proyek jaringan, dan jadwal operasional lapangan.
                        </p>
                    </div>

                    {{-- Quick Action Buttons --}}
                    <div class="flex items-center gap-2.5 shrink-0">
                        <a href="{{ route('tasks.index') }}" class="px-3.5 py-2 rounded-xl bg-white text-[#8F0A0D] font-bold text-[12.5px] hover:bg-[#FFF7F6] hover:scale-[1.02] transition-all shadow-sm flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-[#8F0A0D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span>Delegasi Tugas Baru</span>
                        </a>
                        <a href="{{ route('schedules.index') }}" class="px-3.5 py-2 rounded-xl bg-white/10 hover:bg-white/20 hover:scale-[1.02] border border-white/25 text-white font-bold text-[12.5px] transition-all flex items-center gap-1.5 backdrop-blur-sm">
                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span>Jadwal Kerja</span>
                        </a>
                    </div>
                </div>
            </div>

            {{-- ======================================================== --}}
            {{-- 2. METRIC SUMMARY CARDS (IPNET BLUSH ACCENTS)             --}}
            {{-- ======================================================== --}}
            <div class="anim-fade-up anim-delay-1">
                <div class="flex items-center justify-between mb-3.5">
                    <div>
                        <p class="text-[#8F0A0D] text-[12px] font-bold inline-flex items-center uppercase tracking-wider">
                            <span class="ipnet-badge-dot"></span> RINGKASAN OPERASIONAL
                        </p>
                        <h2 class="text-[18px] font-bold text-[#292929] tracking-tight">Status & Distribusi Penugasan</h2>
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3.5">
                    
                    {{-- Metric 1: Total Proyek --}}
                    <a href="{{ route('projects.index') }}" class="ipnet-metric-card group block anim-fade-up anim-delay-1">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[11px] font-bold text-[#75727C] uppercase tracking-wider">Total Proyek</span>
                            <div class="w-7 h-7 rounded-lg bg-[#8F0A0D]/10 text-[#8F0A0D] flex items-center justify-center group-hover:bg-[#8F0A0D] group-hover:text-white transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="text-[22px] font-extrabold text-[#292929] tracking-tight">{{ $projectsCount }}</div>
                        <p class="text-[11px] text-[#75727C] mt-0.5">Portofolio aktif</p>
                    </a>

                    {{-- Metric 2: Total Tugas --}}
                    <a href="{{ route('tasks.index') }}" class="ipnet-metric-card group block anim-fade-up anim-delay-2">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[11px] font-bold text-[#75727C] uppercase tracking-wider">Total Tugas</span>
                            <div class="w-7 h-7 rounded-lg bg-blue-500/10 text-blue-600 flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                </svg>
                            </div>
                        </div>
                        <div class="text-[22px] font-extrabold text-[#292929] tracking-tight">{{ $tasksCount }}</div>
                        <p class="text-[11px] text-[#75727C] mt-0.5">Seluruh kegiatan</p>
                    </a>

                    {{-- Metric 3: Ditugaskan --}}
                    <a href="{{ route('tasks.index') }}" class="ipnet-metric-card group block anim-fade-up anim-delay-3">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[11px] font-bold text-[#75727C] uppercase tracking-wider">Ditugaskan</span>
                            <div class="w-7 h-7 rounded-lg bg-purple-500/10 text-purple-600 flex items-center justify-center group-hover:bg-purple-600 group-hover:text-white transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <circle cx="12" cy="12" r="9"/>
                                </svg>
                            </div>
                        </div>
                        <div class="text-[22px] font-extrabold text-[#292929] tracking-tight">{{ $tasksAssigned }}</div>
                        <p class="text-[11px] text-[#75727C] mt-0.5">Menunggu respon</p>
                    </a>

                    {{-- Metric 4: Dalam Proses --}}
                    <a href="{{ route('tasks.index') }}" class="ipnet-metric-card group block anim-fade-up anim-delay-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[11px] font-bold text-[#75727C] uppercase tracking-wider">Dalam Proses</span>
                            <div class="w-7 h-7 rounded-lg bg-amber-500/10 text-amber-600 flex items-center justify-center group-hover:bg-amber-600 group-hover:text-white transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="text-[22px] font-extrabold text-[#292929] tracking-tight">{{ $tasksInProgress }}</div>
                        <p class="text-[11px] text-amber-600 font-semibold mt-0.5">Sedang berjalan</p>
                    </a>

                    {{-- Metric 5: Selesai --}}
                    <a href="{{ route('tasks.index') }}" class="ipnet-metric-card group block anim-fade-up anim-delay-5">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[11px] font-bold text-[#75727C] uppercase tracking-wider">Selesai</span>
                            <div class="w-7 h-7 rounded-lg bg-emerald-500/10 text-emerald-600 flex items-center justify-center group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="text-[22px] font-extrabold text-emerald-600 tracking-tight">{{ $tasksCompleted }}</div>
                        <p class="text-[11px] text-[#75727C] mt-0.5">Tuntas diverifikasi</p>
                    </a>

                    {{-- Metric 6: Tenggat Waktu --}}
                    <a href="{{ route('tasks.index') }}" class="ipnet-metric-card group block anim-fade-up anim-delay-6">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[11px] font-bold text-[#75727C] uppercase tracking-wider">Tenggat Waktu</span>
                            <div class="w-7 h-7 rounded-lg bg-rose-500/10 text-rose-600 flex items-center justify-center group-hover:bg-rose-600 group-hover:text-white transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="text-[20px] font-extrabold text-[#8F0A0D] tracking-tight truncate">
                            {{ $upcomingDeadline ? $upcomingDeadline->deadline->format('d M') : '-' }}
                        </div>
                        <p class="text-[11px] text-[#75727C] mt-0.5 truncate">
                            {{ $overdueTasksCount > 0 ? $overdueTasksCount . ' tugas terlewat' : 'Jadwal terdekat' }}
                        </p>
                    </a>

                </div>
            </div>

            {{-- ======================================================== --}}
            {{-- 3. WORKLOAD LOAD CHART PERSONIL LAPANGAN                 --}}
            {{-- ======================================================== --}}
            <div class="ipnet-card p-5 sm:p-6 anim-fade-up anim-delay-3">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-5 pb-4 border-b border-[#E2E8F0]">
                    <div>
                        <p class="text-[#8F0A0D] text-[12px] font-bold inline-flex items-center uppercase tracking-wider">
                            <span class="ipnet-badge-dot"></span> MANAJEMEN PERSONIL
                        </p>
                        <h3 class="text-[18px] font-bold text-[#292929] tracking-tight">
                            Beban & Kapasitas Penugasan Personil
                        </h3>
                        <p class="text-[12.5px] text-[#75727C] mt-0.5">
                            Pantau perbandingan tugas aktif dan tugas selesai untuk keseimbangan distribusi tim
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-2.5">
                        @if($canFilterTeams)
                        <div class="relative">
                            <select id="engTeamFilter" onchange="filterEngineerTeam(this.value)" 
                                    class="text-[12px] font-semibold px-3 py-2 rounded-xl border border-[#CBD5E1] bg-white text-[#1E293B] outline-none hover:border-[#8F0A0D] transition cursor-pointer shadow-xs">
                                <option value="Maintenance" {{ $defaultTeamFilter === 'Maintenance' ? 'selected' : '' }}>Tim Maintenance & Helpdesk</option>
                                <option value="All" {{ $defaultTeamFilter === 'All' ? 'selected' : '' }}>Semua Tim (Lintas Divisi)</option>
                                <option value="Network" {{ $defaultTeamFilter === 'Network' ? 'selected' : '' }}>Divisi Network</option>
                                <option value="Security" {{ $defaultTeamFilter === 'Security' ? 'selected' : '' }}>Divisi Security</option>
                            </select>
                        </div>
                        @endif

                        {{-- Period Switcher --}}
                        <div class="flex gap-1 bg-[#F1F5F9] p-1 rounded-xl border border-[#E2E8F0]">
                            <button onclick="setEngineerPeriod('week')" 
                                     class="text-[11.5px] font-bold px-3 py-1.5 rounded-lg transition cursor-pointer" 
                                     id="engPeriodWeek"
                                     style="background:transparent; color:#64748B;">
                                Minggu Ini
                            </button>
                            <button onclick="setEngineerPeriod('month')" 
                                     class="text-[11.5px] font-bold px-3 py-1.5 rounded-lg transition cursor-pointer shadow-xs" 
                                     id="engPeriodMonth"
                                     style="background:linear-gradient(135deg, #B91C1C 0%, #8F0A0D 60%, #750608 100%); color:#FFFFFF;">
                                Bulan Ini
                            </button>
                        </div>
                    </div>
                </div>

                <div class="w-full overflow-x-auto">
                    <div style="height: 270px; min-width: 340px;">
                        <canvas id="engineerLoadChart"></canvas>
                    </div>
                </div>

                <div class="flex flex-wrap justify-center items-center gap-6 mt-4 pt-3 border-t border-[#F1F5F9] text-[12px] text-[#64748B]">
                    <span class="flex items-center gap-2">
                        <span class="w-3.5 h-3.5 rounded-sm" style="background: linear-gradient(135deg, #D31A2C 0%, #8C0B17 100%);"></span>
                        <span class="font-semibold text-[#1E293B]">Tugas & Kegiatan Aktif</span>
                    </span>
                    <span class="flex items-center gap-2">
                        <span class="w-3.5 h-3.5 rounded-sm" style="background: linear-gradient(135deg, #34D399 0%, #059669 100%);"></span>
                        <span class="font-semibold text-[#1E293B]">Tugas Selesai</span>
                    </span>
                </div>
            </div>

            {{-- ======================================================== --}}
            {{-- 4. CHARTS: PROJECT PROGRESS & STATUS BREAKDOWN           --}}
            {{-- ======================================================== --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 anim-fade-up anim-delay-4">
                
                {{-- Project Progress Chart --}}
                <div class="lg:col-span-2 ipnet-card p-5 sm:p-6 flex flex-col justify-between">
                    <div>
                        <div class="flex flex-wrap justify-between items-center gap-2 mb-4 pb-3 border-b border-[#E2E8F0]">
                            <div>
                                <p class="text-[#8F0A0D] text-[12px] font-bold inline-flex items-center uppercase tracking-wider">
                                    <span class="ipnet-badge-dot"></span> PROYEK AKTIF
                                </p>
                                <h3 class="text-[17px] font-bold text-[#1E293B] tracking-tight">Progres Pelaksanaan Proyek (5 Terbaru)</h3>
                            </div>
                            <a href="{{ route('projects.index') }}" class="text-[#8F0A0D] text-[12.5px] font-bold hover:underline inline-flex items-center gap-1">
                                <span>Lihat Semua</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </div>
                        <div class="w-full overflow-x-auto">
                            <div style="height: 230px; min-width: 280px;">
                                <canvas id="projectProgressChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                
                {{-- Task Status Doughnut Chart --}}
                <div class="ipnet-card p-5 sm:p-6 flex flex-col justify-between">
                    <div>
                        <div class="mb-4 pb-3 border-b border-[#E2E8F0]">
                            <p class="text-[#8F0A0D] text-[12px] font-bold inline-flex items-center uppercase tracking-wider">
                                <span class="ipnet-badge-dot"></span> DISTRIBUSI
                            </p>
                            <h3 class="text-[17px] font-bold text-[#1E293B] tracking-tight">Distribusi Status Tugas</h3>
                        </div>
                        <div style="height: 190px;">
                            <canvas id="taskStatusChart"></canvas>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-2.5 justify-center mt-3 pt-3 border-t border-[#F1F5F9]">
                        @foreach($statusData as $data)
                        <span class="text-[11.5px] font-medium flex items-center gap-1.5 text-[#64748B]">
                            <span style="width: 8px; height: 8px; border-radius: 2px; background: {{ $data['color'] }};"></span>
                            {{ $data['name'] }}
                        </span>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- ======================================================== --}}
            {{-- 5. RECENT ACTIVITY: PROJECTS, TASKS & SCHEDULES           --}}
            {{-- ======================================================== --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 anim-fade-up anim-delay-5">
                
                {{-- Recent Projects --}}
                <div class="ipnet-card overflow-hidden flex flex-col">
                    <div class="flex justify-between items-center gap-2 p-4 sm:p-5 pb-3 border-b border-[#E2E8F0] bg-[#F8FAFC]">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-4 bg-[#8F0A0D] rounded-full inline-block"></span>
                            <h3 class="text-[15px] font-bold text-[#1E293B]">Proyek Terbaru</h3>
                        </div>
                        <a href="{{ route('projects.index') }}" class="text-[#8F0A0D] text-[12px] font-bold hover:underline">Lihat semua</a>
                    </div>
                    <div class="p-2 divide-y divide-[#F1F5F9] flex-1">
                        @forelse($recentProjects as $project)
                        <div class="flex items-center justify-between gap-3 p-3 rounded-xl hover:bg-[#F8FAFC] transition-colors">
                            <div class="min-w-0 flex-1">
                                <div class="text-[13px] font-bold text-[#1E293B] truncate" title="{{ $project->name }}">{{ $project->name }}</div>
                                <div class="text-[11.5px] text-[#64748B] truncate mt-0.5" title="{{ $project->client }}">{{ $project->client }}</div>
                            </div>
                            <div class="shrink-0">
                                <x-status-badge status="{{ $project->status }}" />
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-8 text-[#64748B]">
                            <p class="text-[13px]">Belum ada proyek</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                {{-- Recent Tasks --}}
                <div class="ipnet-card overflow-hidden flex flex-col">
                    <div class="flex justify-between items-center gap-2 p-4 sm:p-5 pb-3 border-b border-[#E2E8F0] bg-[#F8FAFC]">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-4 bg-blue-600 rounded-full inline-block"></span>
                            <h3 class="text-[15px] font-bold text-[#1E293B]">Tugas Terbaru</h3>
                        </div>
                        <a href="{{ route('tasks.index') }}" class="text-[#8F0A0D] text-[12px] font-bold hover:underline">Lihat semua</a>
                    </div>
                    <div class="p-2 divide-y divide-[#F1F5F9] flex-1">
                        @forelse($recentTasks as $task)
                        <div class="flex items-center justify-between gap-3 p-3 rounded-xl hover:bg-[#F8FAFC] transition-colors">
                            <div class="min-w-0 flex-1">
                                <div class="text-[13px] font-bold text-[#1E293B] truncate" title="{{ $task->title }}">{{ $task->title }}</div>
                                <div class="text-[11.5px] text-[#64748B] truncate mt-0.5" title="{{ $task->engineer?->name ?? 'Belum Ditugaskan' }} · {{ $task->project?->name ?? '-' }}">
                                    {{ $task->engineer?->name ?? 'Belum Ditugaskan' }}
                                    @if($task->project)
                                        <span class="text-gray-400">·</span> {{ $task->project->name }}
                                    @endif
                                </div>
                            </div>
                            <div class="shrink-0">
                                <x-status-badge status="{{ $task->status }}" />
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-8 text-[#64748B]">
                            <p class="text-[13px]">Belum ada tugas</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                {{-- Recent Schedules --}}
                <div class="ipnet-card overflow-hidden flex flex-col">
                    <div class="flex justify-between items-center gap-2 p-4 sm:p-5 pb-3 border-b border-[#E2E8F0] bg-[#F8FAFC]">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-4 bg-emerald-600 rounded-full inline-block"></span>
                            <h3 class="text-[15px] font-bold text-[#1E293B]">Jadwal Terdekat</h3>
                        </div>
                        <a href="{{ route('schedules.index') }}" class="text-[#8F0A0D] text-[12px] font-bold hover:underline">Lihat semua</a>
                    </div>
                    <div class="p-2 divide-y divide-[#F1F5F9] flex-1">
                        @forelse($recentSchedules as $sch)
                        @php
                            $isToday = $sch->date && $sch->date->isToday();
                            $dateLabel = $sch->date ? ($isToday ? 'Hari ini' : $sch->date->format('d M')) : '-';
                            $timeLabel = $sch->start_time ? substr($sch->start_time, 0, 5) . ' WIB' : '';
                            $badgeCategory = ($sch->category === 'Task' || $sch->category === 'Kegiatan') ? 'Kegiatan' : ($sch->category ?: 'Meeting');
                            
                            $engineerNames = '';
                            if ($sch->relationLoaded('engineers') && $sch->engineers->isNotEmpty()) {
                                $engineerNames = $sch->engineers->pluck('name')->join(', ');
                            } elseif ($sch->engineer) {
                                $engineerNames = $sch->engineer->name;
                            }
                        @endphp
                        <div class="flex items-center justify-between gap-3 p-3 rounded-xl hover:bg-[#F8FAFC] transition-colors">
                            <div class="min-w-0 flex-1">
                                <div class="text-[13px] font-bold text-[#292929] truncate" title="{{ $sch->title }}">
                                    {{ $sch->title }}
                                </div>
                                <div class="text-[11.5px] text-[#75727C] flex items-center gap-1.5 mt-0.5 min-w-0">
                                    <span class="shrink-0 whitespace-nowrap {{ $isToday ? 'text-[#8F0A0D] font-bold' : 'font-medium' }}">
                                        {{ $dateLabel }}{{ $timeLabel ? ', ' . $timeLabel : '' }}
                                    </span>
                                    @if($engineerNames || $sch->project)
                                        <span class="text-gray-400 shrink-0">·</span>
                                        <span class="truncate" title="{{ $engineerNames ?: $sch->project?->name }}">
                                             {{ $engineerNames ?: $sch->project?->name }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="shrink-0">
                                <x-status-badge :status="$badgeCategory" />
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-8 text-[#75727C]">
                            <p class="text-[13px]">Belum ada jadwal</p>
                        </div>
                        @endforelse
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const engineerMonthData = @json($engineerLoadMonthData);
    const engineerWeekData  = @json($engineerLoadWeekData);
    const defaultTeam       = @json($defaultTeamFilter);
    let currentPeriod       = 'month';
    let currentTeam         = defaultTeam || 'All';
    let engineerChart       = null;

    function getFilteredEngineerData() {
        const rawData = (currentPeriod === 'week') ? engineerWeekData : engineerMonthData;
        if (currentTeam === 'All') {
            return rawData.slice().sort((a, b) => (b.active + (b.completed || 0)) - (a.active + (a.completed || 0)));
        }
        return rawData.filter(d => d.division === currentTeam).sort((a, b) => (b.active + (b.completed || 0)) - (a.active + (a.completed || 0)));
    }

    function formatEngineerLabel(d) {
        return d.name;
    }

    function updateEngineerChart() {
        if (!engineerChart) return;
        const dataToUse = getFilteredEngineerData();
        engineerChart.data.labels = dataToUse.map(formatEngineerLabel);
        engineerChart.data.datasets[0].data = dataToUse.map(d => d.active);
        if (engineerChart.data.datasets[1]) {
            engineerChart.data.datasets[1].data = dataToUse.map(d => d.completed || 0);
        }
        engineerChart.update();
    }

    function filterEngineerTeam(team) {
        currentTeam = team;
        updateEngineerChart();
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Chart Load Pekerjaan Engineer (Rich Red Gradient & Emerald Green)
        const ctx3 = document.getElementById('engineerLoadChart').getContext('2d');
        const initialData = getFilteredEngineerData();
        
        // Gradient Merah IPNET Elegan & Seimbang (Tidak terlalu gelap, tidak kemudaan)
        const activeBarGrad = ctx3.createLinearGradient(0, 0, 500, 0);
        activeBarGrad.addColorStop(0, '#D31A2C');
        activeBarGrad.addColorStop(0.55, '#B21322');
        activeBarGrad.addColorStop(1, '#8C0B17');

        const activeBarHoverGrad = ctx3.createLinearGradient(0, 0, 500, 0);
        activeBarHoverGrad.addColorStop(0, '#E02336');
        activeBarHoverGrad.addColorStop(0.55, '#C41829');
        activeBarHoverGrad.addColorStop(1, '#9E0E1D');

        const completedBarGrad = ctx3.createLinearGradient(0, 0, 500, 0);
        completedBarGrad.addColorStop(0, '#34D399');
        completedBarGrad.addColorStop(1, '#059669');
        
        engineerChart = new Chart(ctx3, {
            type: 'bar',
            data: {
                labels: initialData.map(formatEngineerLabel),
                datasets: [
                    {
                        label: 'Task & Kegiatan Aktif',
                        data: initialData.map(d => d.active),
                        backgroundColor: activeBarGrad,
                        hoverBackgroundColor: activeBarHoverGrad,
                        borderRadius: 6,
                    },
                    {
                        label: 'Task Selesai',
                        data: initialData.map(d => d.completed || 0),
                        backgroundColor: completedBarGrad,
                        hoverBackgroundColor: '#047857',
                        borderRadius: 6,
                    }
                ]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false,
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.parsed.x + ' penugasan';
                            },
                            afterLabel: function(context) {
                                const dataToUse = getFilteredEngineerData();
                                const item = dataToUse[context.dataIndex];
                                return item && item.position ? item.position : '';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        stacked: true,
                        ticks: {
                            stepSize: 1,
                            font: { size: 10, family: 'Inter' }
                        },
                        grid: {
                            display: true,
                            color: '#F0E8E7'
                        }
                    },
                    y: {
                        stacked: true,
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: { size: 11.5, weight: '600', family: 'Inter' }
                        }
                    }
                }
            }
        });

        // Project Progress Chart
        const projectData = @json($projectProgressData);
        const ctx1 = document.getElementById('projectProgressChart').getContext('2d');
        
        const barGradient = ctx1.createLinearGradient(0, 0, 0, 220);
        barGradient.addColorStop(0, '#D31A2C');
        barGradient.addColorStop(0.55, '#B21322');
        barGradient.addColorStop(1, '#8C0B17');
        
        new Chart(ctx1, {
            type: 'bar',
            data: {
                labels: projectData.map(d => d.name),
                datasets: [{
                    label: 'Progress (%)',
                    data: projectData.map(d => d.progress),
                    backgroundColor: barGradient,
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                onClick: function(event, elements) {
                    window.location.href = "{{ route('projects.index') }}";
                },
                onHover: function(event, chartElement) {
                    event.native.target.style.cursor = chartElement[0] ? 'pointer' : 'default';
                },
                plugins: {
                    legend: {
                        display: false,
                    },
                    tooltip: {
                        callbacks: {
                            title: function(context) {
                                const item = projectData[context[0].dataIndex];
                                return item && item.fullName ? item.fullName : context[0].label;
                            },
                            label: function(context) {
                                return 'Progress: ' + context.parsed.y + '%';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        min: 0,
                        max: 100,
                        ticks: {
                            stepSize: 20,
                            callback: function(value) {
                                return value + '%';
                            },
                            font: { family: 'Inter', size: 10 }
                        },
                        grid: {
                            color: '#F0E8E7'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: { family: 'Inter', size: 11, weight: '500' }
                        }
                    }
                }
            }
        });

        // Status Breakdown Doughnut Chart
        const statusData = @json($statusData);
        const ctx2 = document.getElementById('taskStatusChart').getContext('2d');
        
        const gradientColors = [
            {
                gradient: function(ctx) {
                    const g = ctx.createRadialGradient(60, 60, 10, 100, 100, 120);
                    g.addColorStop(0, '#60A5FA');
                    g.addColorStop(1, '#2563EB');
                    return g;
                }
            },
            {
                gradient: function(ctx) {
                    const g = ctx.createRadialGradient(60, 60, 10, 100, 100, 120);
                    g.addColorStop(0, '#FBBF24');
                    g.addColorStop(1, '#D97706');
                    return g;
                }
            },
            {
                gradient: function(ctx) {
                    const g = ctx.createRadialGradient(60, 60, 10, 100, 100, 120);
                    g.addColorStop(0, '#A78BFA');
                    g.addColorStop(1, '#7C3AED');
                    return g;
                }
            },
            {
                gradient: function(ctx) {
                    const g = ctx.createRadialGradient(60, 60, 10, 100, 100, 120);
                    g.addColorStop(0, '#34D399');
                    g.addColorStop(1, '#059669');
                    return g;
                }
            }
        ];

        const gradients = gradientColors.map(function(g) {
            return g.gradient(ctx2);
        });

        new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: statusData.map(d => d.name),
                datasets: [{
                    data: statusData.map(d => d.value),
                    backgroundColor: gradients,
                    borderWidth: 2,
                    borderColor: '#FFFFFF',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: {
                        display: false,
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? Math.round((context.parsed / total) * 100) : 0;
                                return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
    });

    // FILTER PERIODE CHART LOAD ENGINEER (Minggu/Bulan)
    function setEngineerPeriod(period) {
        currentPeriod = period;
        const isWeek = period === 'week';
        const weekBtn = document.getElementById('engPeriodWeek');
        const monthBtn = document.getElementById('engPeriodMonth');
        const activeGradient = 'linear-gradient(135deg, #B91C1C 0%, #8F0A0D 60%, #750608 100%)';

        if (isWeek) {
            weekBtn.style.background = activeGradient;
            weekBtn.style.color = '#FFFFFF';
            weekBtn.classList.add('shadow-xs');
            monthBtn.style.background = 'transparent';
            monthBtn.style.color = '#64748B';
            monthBtn.classList.remove('shadow-xs');
        } else {
            monthBtn.style.background = activeGradient;
            monthBtn.style.color = '#FFFFFF';
            monthBtn.classList.add('shadow-xs');
            weekBtn.style.background = 'transparent';
            weekBtn.style.color = '#64748B';
            weekBtn.classList.remove('shadow-xs');
        }
        
        updateEngineerChart();
    }
</script>
@endpush
@endsection