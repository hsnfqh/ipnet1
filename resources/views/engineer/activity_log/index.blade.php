@extends('layouts.app')

@section('title', 'Activity Log Engineer - IP Network Solusindo')

@push('styles')
<style>
    :root {
        --ipnet-primary: #8F0A0D;
        --ipnet-primary-hover: #73080A;
        --ipnet-card-bg: #FFFFFF;
        --ipnet-card-border: #E2E8F0;
        --ipnet-text-main: #1E293B;
    }

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

    .btn-ipnet-primary {
        background: linear-gradient(135deg, #8F0A0D 0%, #B81525 100%);
        color: #FFFFFF;
        font-weight: 700;
        transition: all 0.2s ease;
        box-shadow: 0 2px 4px rgba(143, 10, 13, 0.15);
    }

    .btn-ipnet-primary:hover {
        background: linear-gradient(135deg, #7A080B 0%, #9E0E1D 100%);
        box-shadow: 0 6px 16px rgba(143, 10, 13, 0.28);
        transform: translateY(-1px);
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
<div class="flex h-screen overflow-hidden bg-[#F8FAFC] font-sans" x-data="engineerActivityManager()">
    @include('components.sidebar')

    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => $isLead ? 'Activity Log & Monitoring Engineer' : 'Catatan Aktivitas Harian Engineer'])

        <div class="p-4 sm:p-6 lg:p-7 space-y-6 max-w-[1680px] mx-auto">

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between shadow-xs anim-fade-up">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 font-bold px-2">✕</button>
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold flex items-center justify-between shadow-xs anim-fade-up">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700 font-bold px-2">✕</button>
                </div>
            @endif

            {{-- Top Summary Metric Cards --}}
            <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 {{ $isLead ? 'xl:grid-cols-5' : '' }} gap-3.5 anim-fade-up">
                {{-- Metric 1: Total --}}
                <div class="ipnet-card p-4 flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0" style="background:#FEF2F2; color:#8F0A0D;">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Total Aktivitas</p>
                        <p class="text-xl font-extrabold text-gray-900 mt-0.5 leading-none">{{ number_format($totalActivities) }}</p>
                    </div>
                </div>

                {{-- Metric 2: Selesai --}}
                <div class="ipnet-card p-4 flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Selesai</p>
                        <p class="text-xl font-extrabold text-emerald-600 mt-0.5 leading-none">{{ number_format($totalCompleted) }}</p>
                    </div>
                </div>

                {{-- Metric 3: Sedang Berjalan --}}
                <div class="ipnet-card p-4 flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Sedang Berjalan</p>
                        <p class="text-xl font-extrabold text-sky-600 mt-0.5 leading-none">{{ number_format($totalInProgress) }}</p>
                    </div>
                </div>

                {{-- Metric 4: Ditunda --}}
                <div class="ipnet-card p-4 flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Ditunda</p>
                        <p class="text-xl font-extrabold text-amber-600 mt-0.5 leading-none">{{ number_format($totalDelayed) }}</p>
                    </div>
                </div>

                {{-- Metric 5: Engineer Aktif (Khusus Managerial / Lead) --}}
                @if($isLead)
                <div class="ipnet-card p-4 flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Engineer Terlibat</p>
                        <p class="text-xl font-extrabold text-indigo-600 mt-0.5 leading-none">{{ number_format($activeEngineersCount) }}</p>
                    </div>
                </div>
                @endif
            </div>

            {{-- Filter & Action Bar --}}
            <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center gap-3 anim-fade-up anim-delay-1">
                <form method="GET" action="{{ route('engineer.activity_log.index') }}" class="flex flex-col sm:flex-row flex-wrap gap-2.5 w-full lg:w-auto">
                    {{-- Search Input --}}
                    <div class="relative">
                        <svg class="w-4 h-4 absolute left-3.5 top-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               placeholder="Cari kegiatan, lokasi, nama..."
                               class="w-full sm:w-64 px-3.5 py-2 pl-9 rounded-xl border border-gray-200 text-xs font-semibold text-gray-800 bg-white focus:outline-none focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D] transition-all shadow-xs">
                    </div>

                    {{-- Dropdown Engineer (Khusus Lead) --}}
                    @if($isLead && $engineers->isNotEmpty())
                    <select name="user_id" onchange="this.form.submit()"
                            class="w-full sm:w-48 px-3.5 py-2 rounded-xl border border-gray-200 text-xs font-semibold text-gray-800 bg-white focus:outline-none focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D] transition-all cursor-pointer shadow-xs truncate">
                        <option value="">Semua Engineer</option>
                        @foreach($engineers as $eng)
                            <option value="{{ $eng->id }}" {{ request('user_id') == $eng->id ? 'selected' : '' }}>
                                {{ $eng->name }}
                            </option>
                        @endforeach
                    </select>
                    @endif

                    {{-- Dropdown Tipe Aktivitas --}}
                    <select name="activity_type" onchange="this.form.submit()"
                            class="w-full sm:w-48 px-3.5 py-2 rounded-xl border border-gray-200 text-xs font-semibold text-gray-800 bg-white focus:outline-none focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D] transition-all cursor-pointer shadow-xs">
                        <option value="">Semua Tipe Kegiatan</option>
                        @foreach($activityTypes as $type)
                            <option value="{{ $type }}" {{ request('activity_type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                        @endforeach
                    </select>

                    {{-- Dropdown Status --}}
                    <select name="status" onchange="this.form.submit()"
                            class="w-full sm:w-40 px-3.5 py-2 rounded-xl border border-gray-200 text-xs font-semibold text-gray-800 bg-white focus:outline-none focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D] transition-all cursor-pointer shadow-xs">
                        <option value="">Semua Status</option>
                        <option value="Selesai" {{ request('status') === 'Selesai' ? 'selected' : '' }}>Selesai</option>
                        <option value="Sedang Berjalan" {{ request('status') === 'Sedang Berjalan' ? 'selected' : '' }}>Sedang Berjalan</option>
                        <option value="Ditunda" {{ request('status') === 'Ditunda' ? 'selected' : '' }}>Ditunda</option>
                    </select>

                    {{-- Date Picker --}}
                    <input type="date"
                           name="date"
                           value="{{ request('date') }}"
                           onchange="this.form.submit()"
                           class="w-full sm:w-36 px-3.5 py-2 rounded-xl border border-gray-200 text-xs font-semibold text-gray-800 bg-white focus:outline-none focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D] transition-all shadow-xs cursor-pointer">

                    @if(request('search') || request('user_id') || request('activity_type') || request('status') || request('project_id') || request('date'))
                        <a href="{{ route('engineer.activity_log.index') }}"
                           class="px-3 py-2 text-xs font-bold text-gray-500 hover:text-gray-800 self-center">
                            Reset Filter
                        </a>
                    @endif
                </form>

                {{-- Action Buttons Group --}}
                <div class="flex items-center gap-2 flex-shrink-0">
                    {{-- Export Dropdown --}}
                    <div class="relative" x-data="{ exportOpen: false }">
                        <button type="button"
                                @click="exportOpen = !exportOpen"
                                class="w-full sm:w-auto justify-center px-4 py-2.5 rounded-xl font-bold text-xs flex items-center gap-2 transition-all cursor-pointer shrink-0 bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 hover:border-gray-300 shadow-xs">
                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            <span>Export</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        {{-- Dropdown Menu --}}
                        <div x-show="exportOpen"
                             x-cloak
                             @click.outside="exportOpen = false"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-56 bg-white border border-gray-200 rounded-xl shadow-lg z-30 overflow-hidden"
                             style="display:none;">
                            <div class="p-2 space-y-0.5">
                                {{-- Export PDF --}}
                                <a href="{{ route('engineer.activity_log.export_pdf', request()->except('page')) }}"
                                   target="_blank"
                                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-bold text-gray-700 hover:bg-red-50 hover:text-[#8F0A0D] transition-colors group">
                                    <div class="w-7 h-7 rounded-lg bg-red-50 group-hover:bg-red-100 flex items-center justify-center flex-shrink-0 transition-colors">
                                        <svg class="w-3.5 h-3.5 text-[#8F0A0D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold">Download PDF</p>
                                        <p class="text-[10px] text-gray-400 font-normal">Laporan berlogo IP Network</p>
                                    </div>
                                </a>

                                <div class="border-t border-gray-100 my-1"></div>

                                {{-- Export Excel --}}
                                <a href="{{ route('engineer.activity_log.export_excel', request()->except('page')) }}"
                                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-bold text-gray-700 hover:bg-emerald-50 hover:text-emerald-700 transition-colors group">
                                    <div class="w-7 h-7 rounded-lg bg-emerald-50 group-hover:bg-emerald-100 flex items-center justify-center flex-shrink-0 transition-colors">
                                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold">Download Excel</p>
                                        <p class="text-[10px] text-gray-400 font-normal">Format CSV (bisa dibuka Excel)</p>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Tombol Input Aktivitas --}}
                    <button type="button" @click="$dispatch('open-engineer-activity-modal')"
                            class="btn-ipnet-primary w-full sm:w-auto justify-center shadow-md px-4 py-2.5 rounded-xl font-bold text-xs flex items-center gap-2 transition-all cursor-pointer shrink-0">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>Input Aktivitas</span>
                    </button>
                </div>
            </div>

            {{-- Activity Grid / Feed --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 anim-fade-up anim-delay-2">
                @forelse($activities as $act)
                    <div class="ipnet-card p-5 flex flex-col justify-between space-y-3.5 hover:border-red-200">
                        <div class="space-y-3">
                            {{-- Header Card: Engineer & Waktu --}}
                            <div class="flex items-start justify-between gap-2.5 pb-2.5 border-b border-gray-100">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-white font-bold text-xs shrink-0 shadow-xs"
                                         style="background:linear-gradient(135deg, #8F0A0D 0%, #D62E3C 100%);">
                                        {{ strtoupper(substr($act->engineer->name ?? 'E', 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs font-bold text-gray-900 truncate">{{ $act->engineer->name ?? 'Engineer' }}</p>
                                        <p class="text-[10.5px] text-gray-400 truncate">{{ $act->engineer->position ?? 'Technical Team' }}</p>
                                    </div>
                                </div>
                                <div class="text-right shrink-0">
                                    <span class="inline-block text-[11px] font-bold text-gray-600">
                                        {{ $act->activity_date ? $act->activity_date->format('d M Y') : '-' }}
                                    </span>
                                    @if($act->start_time && $act->end_time)
                                        <p class="text-[10px] text-gray-400 font-medium">
                                            {{ \Carbon\Carbon::parse($act->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($act->end_time)->format('H:i') }}
                                            @if($act->duration)
                                                <span class="text-[#8F0A0D] font-bold">({{ $act->duration }})</span>
                                            @endif
                                        </p>
                                    @endif
                                </div>
                            </div>

                            {{-- Badges: Tipe & Status --}}
                            <div class="flex items-center justify-between gap-2">
                                <span class="px-2.5 py-1 rounded-lg text-[10.5px] font-bold bg-red-50 text-[#8F0A0D] border border-red-200/80 truncate">
                                    {{ $act->activity_type }}
                                </span>

                                @if($act->status === 'Selesai')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10.5px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 shrink-0">
                                        Selesai
                                    </span>
                                @elseif($act->status === 'Sedang Berjalan')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10.5px] font-bold bg-sky-50 text-sky-700 border border-sky-200 shrink-0">
                                        Sedang Berjalan
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[10.5px] font-bold bg-amber-50 text-amber-700 border border-amber-200 shrink-0">
                                        Ditunda
                                    </span>
                                @endif
                            </div>

                            {{-- Deskripsi Pekerjaan --}}
                            <p class="text-xs text-gray-800 leading-relaxed font-medium bg-[#FAFAFA] p-3 rounded-xl border border-gray-100 whitespace-pre-line">
                                {{ $act->description }}
                            </p>

                            {{-- Project & Location Metadata --}}
                            <div class="space-y-1.5 text-[11px] text-gray-500 pt-1">
                                @if($act->project)
                                    <div class="flex items-center gap-1.5 truncate">
                                        <svg class="w-3.5 h-3.5 text-[#8F0A0D] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                        </svg>
                                        <span class="font-bold text-gray-800 truncate">{{ $act->project->name }}</span>
                                        @if($act->project->client)
                                            <span class="text-gray-400">({{ $act->project->client }})</span>
                                        @endif
                                    </div>
                                @endif

                                @if($act->location)
                                    <div class="flex items-center gap-1.5 truncate">
                                        <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        <span class="truncate">{{ $act->location }}</span>
                                    </div>
                                @endif

                                @if($act->notes)
                                    <div class="p-2 rounded-lg bg-amber-50/60 border border-amber-200/80 text-[10.5px] text-amber-900 mt-2">
                                        <span class="font-bold">Catatan/Kendala:</span> {{ $act->notes }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Footer Card: Aksi Hapus (Jika pembuat atau Lead) --}}
                        <div class="flex items-center justify-between pt-3 border-t border-gray-100 text-[11px]">
                            <span class="text-gray-400 text-[10.5px]">
                                Dicatat: {{ $act->created_at->diffForHumans() }}
                            </span>

                            @if(auth()->id() === $act->user_id || $isLead)
                                <form method="POST" action="{{ route('engineer.activity_log.destroy', $act) }}"
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus catatan aktivitas ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-[11px] font-bold text-gray-400 hover:text-red-600 transition-colors flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        <span>Hapus</span>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-16 text-center ipnet-card">
                        <div class="w-16 h-16 mx-auto mb-3 rounded-2xl bg-red-50 text-[#8F0A0D] flex items-center justify-center">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                            </svg>
                        </div>
                        <h4 class="text-sm font-bold text-gray-800 mb-1">Belum Ada Catatan Aktivitas</h4>
                        <p class="text-xs text-gray-400 max-w-md mx-auto mb-4">
                            Belum ditemukan aktivitas engineer sesuai kriteria filter yang dipilih. Silakan catat aktivitas teknis baru.
                        </p>
                        <button type="button" @click="$dispatch('open-engineer-activity-modal')" class="btn-ipnet-primary px-4 py-2 rounded-xl text-xs font-bold inline-flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span>Input Aktivitas Sekarang</span>
                        </button>
                    </div>
                @endforelse
            </div>

            {{-- Pagination Links --}}
            @if($activities->hasPages())
                <div class="pt-4 flex justify-center">
                    {{ $activities->links() }}
                </div>
            @endif

        </div>
    </div>

    {{-- MODAL INPUT AKTIVITAS SPREADSHEET --}}
    @include('components.engineer-activity-bulk-modal')
</div>
@endsection
