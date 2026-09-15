@extends('layouts.app')

@section('title', 'Rekap Presensi & Kehadiran - PT IP Network Solusindo')

@push('styles')
<style>
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
    @keyframes fadeUpStagger {
        0% { opacity: 0; transform: translateY(14px); }
        100% { opacity: 1; transform: translateY(0); }
    }
    .anim-fade-up {
        animation: fadeUpStagger 0.45s cubic-bezier(0.16, 1, 0.3, 1) both;
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
        @include('components.topbar', ['title' => 'Rekap Presensi & Kehadiran'])

        <div class="p-4 sm:p-6 lg:p-7 space-y-5 max-w-[1600px] mx-auto" 
             x-data="recapManager()" 
             x-init="init()">

            <!-- ========================================================== -->
            <!-- 1. SECTION HEADER & TAB CONTROLS                           -->
            <!-- ========================================================== -->
            <div class="ipnet-card p-5 sm:p-6 anim-fade-up anim-delay-1">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 pb-4 mb-4 border-b border-[#E2E8F0]">
                    <div>
                        <p class="text-[#8F0A0D] text-[12px] font-bold inline-flex items-center uppercase tracking-wider">
                            <span class="ipnet-badge-dot"></span> MANAJEMEN PRESENSI
                        </p>
                        <h2 class="text-[20px] font-bold text-[#1E293B] tracking-tight">Rekap Kehadiran Personil Lapangan</h2>
                        <p class="text-[13px] text-[#64748B] mt-0.5">Pantau status kehadiran teknisi, verifikasi selfie & lokasi GPS, dan laporan rekapitulasi</p>
                    </div>

                    {{-- Segmented View Tabs (Daily / Monthly) --}}
                    <div class="flex items-center p-1 bg-[#F1F5F9] rounded-xl border border-[#E2E8F0] self-start lg:self-auto">
                        <button type="button" 
                                class="px-5 py-2 rounded-lg text-[12.5px] font-bold transition-all duration-200 cursor-pointer"
                                :class="tab === 'daily' ? 'bg-[#8F0A0D] text-white shadow-sm' : 'text-[#64748B] hover:text-[#1E293B]'"
                                @click="tab = 'daily'">
                            Daily <span class="font-normal opacity-80">(Harian)</span>
                        </button>
                        <button type="button" 
                                class="px-5 py-2 rounded-lg text-[12.5px] font-bold transition-all duration-200 cursor-pointer"
                                :class="tab === 'monthly' ? 'bg-[#8F0A0D] text-white shadow-sm' : 'text-[#64748B] hover:text-[#1E293B]'"
                                @click="tab = 'monthly'">
                            Monthly <span class="font-normal opacity-80">(Bulanan)</span>
                        </button>
                    </div>
                </div>

                {{-- Date / Month Picker & Export Actions --}}
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div class="flex flex-wrap items-center gap-2.5">
                        <template x-if="tab === 'daily'">
                            <div class="flex items-center gap-2 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl px-3.5 py-1.5 shadow-xs">
                                <svg class="w-4 h-4 text-[#64748B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span class="text-[12px] font-bold text-[#64748B] uppercase tracking-wider">Tanggal:</span>
                                <input type="date" 
                                       x-model="selectedDate" 
                                       @change="fetchDaily()"
                                       class="bg-transparent border-none text-[13px] font-semibold text-[#1E293B] focus:outline-none cursor-pointer">
                            </div>
                        </template>

                        <template x-if="tab === 'monthly'">
                            <div class="flex items-center gap-2 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl px-3.5 py-1.5 shadow-xs">
                                <svg class="w-4 h-4 text-[#64748B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span class="text-[12px] font-bold text-[#64748B] uppercase tracking-wider">Bulan:</span>
                                <input type="month" 
                                       x-model="selectedMonth" 
                                       @change="init()"
                                       class="bg-transparent border-none text-[13px] font-semibold text-[#1E293B] focus:outline-none cursor-pointer">
                            </div>
                        </template>
                    </div>

                    <div class="flex flex-wrap items-center gap-2.5">
                        {{-- Tombol Presensi Saya (Clock In/Out) Khusus Team Leader / Lead Lapangan --}}
                        @if(auth()->user() && !auth()->user()->hasAnyRole(['Direktur', 'HD / Direktur', 'Group Leader', 'PMO', 'Project Manager']))
                        <a href="{{ route('attendance.index') }}" 
                           class="px-4 py-2 rounded-xl bg-[#8F0A0D] hover:bg-[#73080A] text-white text-[12.5px] font-bold transition-all flex items-center gap-2 shadow-md hover:scale-[1.02] active:scale-[0.98] cursor-pointer"
                           title="Buka Form Clock In/Out Selfie & GPS Saya">
                            <svg class="w-4 h-4 text-white flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Presensi Saya</span>
                        </a>
                        @endif

                        {{-- Tombol Export PDF --}}
                        <a :href="'{{ route('attendance.export.pdf') }}?tab=' + tab + '&date=' + selectedDate + '&month=' + selectedMonth" 
                           target="_blank"
                           class="px-4 py-2 rounded-xl bg-white border border-[#CBD5E1] hover:border-[#8F0A0D] hover:bg-[#F8FAFC] text-[#1E293B] hover:text-[#8F0A0D] text-[12.5px] font-bold transition-all flex items-center gap-2 shadow-xs cursor-pointer"
                           title="Download Laporan PDF Resmi">
                            <svg class="w-4 h-4 text-[#8F0A0D] flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14H9v-2h2v2zm0-4H9V7h2v5zm4 4h-2v-6h2v6z"/>
                            </svg>
                            <span>Export PDF</span>
                        </a>
                    </div>
                </div>

            </div>

            {{-- ── 2. EXECUTIVE METRIC CARDS ────────────── --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 sm:gap-4 anim-fade-up anim-delay-2" x-show="tab === 'daily'">
                
                {{-- Total Engineer --}}
                <div class="ipnet-metric-card group block">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[11px] font-bold text-[#75727C] uppercase tracking-wider">Total Personil</span>
                        <div class="w-7 h-7 rounded-lg bg-[#8F0A0D]/10 text-[#8F0A0D] flex items-center justify-center group-hover:bg-[#8F0A0D] group-hover:text-white transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="text-[24px] font-extrabold text-[#292929] tracking-tight" x-text="summaryDaily.total">0</div>
                    <p class="text-[11px] text-[#75727C] mt-0.5">Personil terdaftar</p>
                </div>

                {{-- Hadir --}}
                <div class="ipnet-metric-card group block">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[11px] font-bold text-[#75727C] uppercase tracking-wider">Hadir (Sesuai Radius)</span>
                        <div class="w-7 h-7 rounded-lg bg-emerald-500/10 text-emerald-600 flex items-center justify-center group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="text-[24px] font-extrabold text-emerald-600 tracking-tight" x-text="summaryDaily.hadir">0</div>
                    <p class="text-[11px] text-[#75727C] mt-0.5">Presensi on-radius</p>
                </div>

                {{-- Luar Jangkauan --}}
                <div class="ipnet-metric-card group block">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[11px] font-bold text-[#75727C] uppercase tracking-wider">Luar Jangkauan</span>
                        <div class="w-7 h-7 rounded-lg bg-amber-500/10 text-amber-600 flex items-center justify-center group-hover:bg-amber-600 group-hover:text-white transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="text-[24px] font-extrabold text-amber-600 tracking-tight" x-text="summaryDaily.luarJangkauan">0</div>
                    <p class="text-[11px] text-[#75727C] mt-0.5">Di luar perimeter</p>
                </div>

                {{-- Belum Presensi / Tidak Hadir --}}
                <div class="ipnet-metric-card group block">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[11px] font-bold text-[#75727C] uppercase tracking-wider">Belum Presensi</span>
                        <div class="w-7 h-7 rounded-lg bg-rose-500/10 text-rose-600 flex items-center justify-center group-hover:bg-rose-600 group-hover:text-white transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="text-[24px] font-extrabold text-rose-600 tracking-tight" x-text="summaryDaily.tidakHadir">0</div>
                    <p class="text-[11px] text-[#75727C] mt-0.5">Tanpa konfirmasi</p>
                </div>

            </div>

            {{-- ── 3. TABEL HARIAN ─────────────────────────────────────────── --}}
            <div x-show="tab === 'daily'" x-cloak class="anim-fade-up anim-delay-3">
                <div class="ipnet-card overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-[13px]">
                            <thead>
                                <tr class="border-b border-[#E2E8F0] bg-[#F8FAFC] text-[11.5px] font-bold text-[#475569] uppercase tracking-wider">
                                    <th class="py-3.5 px-4 w-60">Engineer</th>
                                    <th class="py-3.5 px-4 text-center w-32">Clock In</th>
                                    <th class="py-3.5 px-4 text-center w-32">Clock Out</th>
                                    <th class="py-3.5 px-4 text-center w-28">Durasi</th>
                                    <th class="py-3.5 px-4 text-center w-32">Jarak Lokasi</th>
                                    <th class="py-3.5 px-4 text-center w-40">Status Presensi</th>
                                    <th class="py-3.5 px-4 text-center w-24">Bukti Foto</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#F1F5F9]">
                                <template x-if="loadingDaily">
                                    <tr>
                                        <td colspan="7" class="text-center py-12 text-[#64748B]">
                                            <div class="inline-flex items-center gap-2">
                                                <svg class="w-5 h-5 text-[#8F0A0D] animate-spin" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                                </svg>
                                                <span class="font-medium text-[13.5px]">Memuat data presensi harian...</span>
                                            </div>
                                        </td>
                                    </tr>
                                </template>

                                <template x-if="!loadingDaily">
                                    <template x-for="row in dailyRows" :key="row.engineer_id">
                                        <tr class="hover:bg-[#F8FAFC] transition-colors">
                                            {{-- Engineer Name & Avatar --}}
                                            <td class="py-3.5 px-4 whitespace-nowrap">
                                                <div class="flex items-center gap-2.5">
                                                    <div class="w-7 h-7 rounded-full bg-[#FEE2E2] border border-[#FECACA] text-[#8F0A0D] text-[10.5px] font-bold flex items-center justify-center flex-shrink-0" 
                                                         x-text="initials(row.name)">
                                                    </div>
                                                    <span class="font-semibold text-[#1E293B] text-[13.5px]" x-text="row.name"></span>
                                                </div>
                                            </td>

                                            {{-- Clock In --}}
                                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                                <template x-if="row.clock_in_time">
                                                    <span class="inline-flex items-center font-mono font-medium text-[12.5px] text-[#1E293B] bg-[#F1F5F9] px-2.5 py-1 rounded-lg" x-text="row.clock_in_time"></span>
                                                </template>
                                                <template x-if="!row.clock_in_time">
                                                    <span class="text-[#94A3B8] font-mono text-[12px]">—</span>
                                                </template>
                                            </td>

                                            {{-- Clock Out --}}
                                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                                <template x-if="row.clock_out_time">
                                                    <span class="inline-flex items-center font-mono font-medium text-[12.5px] text-[#1E293B] bg-[#F1F5F9] px-2.5 py-1 rounded-lg" x-text="row.clock_out_time"></span>
                                                </template>
                                                <template x-if="!row.clock_out_time">
                                                    <span class="text-[#94A3B8] font-mono text-[12px]">—</span>
                                                </template>
                                            </td>

                                            {{-- Durasi --}}
                                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                                <span class="font-bold text-[#1E293B] text-[13px]" x-text="row.duration || '—'"></span>
                                            </td>

                                            {{-- Jarak & Lokasi --}}
                                            <td class="py-3.5 px-4 text-center">
                                                <template x-if="row.distance !== null">
                                                    <div>
                                                        <span class="font-mono text-[12.5px] font-medium text-[#334155]" x-text="row.distance + ' m'"></span>
                                                        <template x-if="row.address">
                                                            <div class="text-[11px] text-[#64748B] font-normal leading-tight mt-0.5 max-w-[220px] mx-auto truncate" :title="row.address" x-text="row.address"></div>
                                                        </template>
                                                    </div>
                                                </template>
                                                <template x-if="row.distance === null">
                                                    <span class="text-[#94A3B8] font-mono text-[12px]">—</span>
                                                </template>
                                            </td>

                                            {{-- Status Presensi (Corporate Badges) --}}
                                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                                <template x-if="row.status === 'Hadir'">
                                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 text-[11.5px] font-semibold rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                        Hadir (Tepat)
                                                    </span>
                                                </template>
                                                <template x-if="row.status === 'Luar Jangkauan'">
                                                    <div>
                                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 text-[11.5px] font-semibold rounded-lg bg-amber-50 text-amber-700 border border-amber-200">
                                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                            Luar Jangkauan
                                                        </span>
                                                        <template x-if="row.note">
                                                            <div class="text-[11px] text-amber-800 font-medium mt-1 max-w-[200px] mx-auto truncate" :title="row.note" x-text="'Alasan: ' + row.note"></div>
                                                        </template>
                                                    </div>
                                                </template>
                                                <template x-if="row.status === 'Tidak Hadir'">
                                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 text-[11.5px] font-semibold rounded-lg bg-red-50 text-[#8F0A0D] border border-red-200">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-[#8F0A0D]"></span>
                                                        Tidak Hadir
                                                    </span>
                                                </template>
                                            </td>

                                            {{-- Foto Bukti --}}
                                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                                <template x-if="row.photo_url">
                                                    <button @click="viewPhoto(row.photo_url, row.name, row.note)"
                                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-[#FEE2E2] hover:bg-[#FECACA] text-[#8F0A0D] text-[12px] font-semibold transition cursor-pointer">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                        </svg>
                                                        Lihat Foto
                                                    </button>
                                                </template>
                                                <template x-if="!row.photo_url">
                                                    <span class="text-[#94A3B8] text-[12px]">—</span>
                                                </template>
                                            </td>
                                        </tr>
                                    </template>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- ── 4. TABEL BULANAN ────────────────────────────────────────── --}}
            <div x-show="tab === 'monthly'" x-cloak class="anim-fade-up anim-delay-3">
                <div class="ipnet-card overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-[13px]">
                            <thead>
                                <tr class="border-b border-[#E2E8F0] bg-[#F8FAFC] text-[11.5px] font-bold text-[#475569] uppercase tracking-wider">
                                    <th class="py-3.5 px-4 w-60">Engineer</th>
                                    <th class="py-3.5 px-4 text-center w-36">Hadir (Dalam Radius)</th>
                                    <th class="py-3.5 px-4 text-center w-36">Luar Jangkauan</th>
                                    <th class="py-3.5 px-4 text-center w-36">Total Kehadiran</th>
                                    <th class="py-3.5 px-4 min-w-[200px]">Persentase Kehadiran</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#F1F5F9]">
                                @forelse($engineers as $eng)
                                    @php
                                        $engRecords = $monthlyAttendances->get($eng->id, collect());
                                        $hadir      = $engRecords->where('is_within_range', true)->count();
                                        $luar       = $engRecords->where('is_within_range', false)->count();
                                        $total      = $engRecords->count();
                                        $pct        = $total > 0 ? round($total / 26 * 100) : 0;
                                        $initials   = strtoupper(substr($eng->name, 0, 1)) . strtoupper(substr(explode(' ', $eng->name)[1] ?? '', 0, 1));
                                    @endphp
                                    <tr class="hover:bg-[#F8FAFC] transition-colors">
                                        <td class="py-3.5 px-4 whitespace-nowrap">
                                            <div class="flex items-center gap-2.5">
                                                <div class="w-7 h-7 rounded-full bg-[#FDF1F2] border border-[#FADADF] text-[#C81E2C] text-[10.5px] font-bold flex items-center justify-center flex-shrink-0">
                                                    {{ $initials }}
                                                </div>
                                                <div>
                                                    <div class="font-semibold text-[#17151C] text-[13.5px]">{{ $eng->name }}</div>
                                                    <div class="text-[11px] text-[#75727C]">{{ $eng->position ?? 'Engineer' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 text-[11.5px] font-semibold rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                {{ $hadir }} Hari
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 text-[11.5px] font-semibold rounded-lg bg-amber-50 text-amber-700 border border-amber-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                {{ $luar }} Hari
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                            <span class="font-mono text-[13.5px] font-bold text-[#17151C]">{{ $total }} / 26 Hari</span>
                                        </td>
                                        <td class="py-3.5 px-4">
                                            <div class="flex items-center gap-3">
                                                <div class="flex-1 bg-[#EFEDEB] rounded-full h-2 overflow-hidden">
                                                    <div class="h-full rounded-full bg-gradient-to-r from-[#AF1424] to-[#D62E3C] transition-all duration-300" style="width: {{ min($pct, 100) }}%;"></div>
                                                </div>
                                                <span class="text-[12.5px] font-bold text-[#17151C] font-mono min-w-[40px] text-right">{{ $pct }}%</span>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-12 text-center text-[#75727C]">Tidak ada data engineer yang ditemukan.</td>
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

{{-- ── MODAL PREVIEW FOTO ────────────────────────────────────────────────── --}}
<div x-data="{ photoOpen: false, photoUrl: '', photoName: '', photoNote: '' }"
     x-show="photoOpen" 
     x-cloak
     @att-view-photo.window="photoOpen = true; photoUrl = $event.detail.url; photoName = $event.detail.name; photoNote = $event.detail.note || ''"
     class="fixed inset-0 bg-[#0E0D12]/70 z-50 flex items-center justify-center p-4 backdrop-blur-sm"
     @click.self="photoOpen = false"
     style="display: none;">
    
    <div class="bg-white rounded-2xl overflow-hidden max-w-md w-full shadow-[0_20px_60px_rgba(14,13,18,0.25)] border border-[#E7E5E3] animate-fade-in-up">
        <div class="flex items-center justify-between px-5 py-3.5 border-b border-[#EFEDEB] bg-[#FBFBFA]">
            <h3 class="text-[14px] font-bold text-[#17151C]" x-text="'Foto Presensi — ' + photoName"></h3>
            <button @click="photoOpen = false" class="text-[#75727C] hover:text-[#17151C] p-1 rounded-lg hover:bg-[#F1F0EE] transition cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="p-4 bg-[#F8F7F6] space-y-3">
            <img :src="photoUrl" class="w-full rounded-xl object-contain max-h-[380px] bg-black/5 shadow-sm" alt="Foto Selfie Presensi">
            <div x-show="photoNote" class="p-3 bg-white border border-[#E7E5E3] rounded-xl text-left">
                <span class="text-[10.5px] font-bold text-[#75727C] uppercase tracking-wider block mb-0.5">Alasan / Catatan Presensi:</span>
                <p class="text-[13px] text-[#17151C] font-semibold" x-text="photoNote"></p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('recapManager', () => ({
        tab:           'daily',
        selectedDate:  '{{ $date }}',
        selectedMonth: '{{ $month }}',
        dailyRows:     [],
        loadingDaily:  false,
        engineers:     @json($engineers->map(fn($e) => ['id' => $e->id, 'name' => $e->name, 'position' => $e->position ?? ''])),

        get summaryDaily() {
            const total = this.engineers.length;
            const hadir = this.dailyRows.filter(r => r.status === 'Hadir').length;
            const luar  = this.dailyRows.filter(r => r.status === 'Luar Jangkauan').length;
            return { total, hadir, luarJangkauan: luar, tidakHadir: Math.max(0, total - hadir - luar) };
        },

        async init() {
            await this.fetchDaily();
        },

        async fetchDaily() {
            this.loadingDaily = true;
            try {
                const resp = await fetch(`/attendance/daily-data?date=${this.selectedDate}`, {
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                });
                const data = await resp.json();

                const ciMap = {}, coMap = {};
                data.forEach(a => {
                    if (a.type === 'clock_in')  ciMap[a.user_id] = a;
                    if (a.type === 'clock_out') coMap[a.user_id] = a;
                });

                this.dailyRows = this.engineers.map(eng => {
                    const ci = ciMap[eng.id];
                    const co = coMap[eng.id];
                    let duration = '—';
                    if (ci && co) {
                        const diff = Math.round((new Date('2000-01-01 ' + co.time) - new Date('2000-01-01 ' + ci.time)) / 60000);
                        if (diff > 0) duration = Math.floor(diff/60) + 'j ' + (diff%60) + 'm';
                    }
                    return {
                        engineer_id:    eng.id,
                        name:           eng.name,
                        clock_in_time:  ci ? ci.time : null,
                        clock_out_time: co ? co.time : null,
                        distance:       ci ? ci.distance : null,
                        address:        ci ? (ci.address || null) : (co ? co.address : null),
                        photo_url:      ci ? (ci.photo_url || (co ? co.photo_url : null)) : (co ? co.photo_url : null),
                        note:           ci ? (ci.note || (co ? co.note : null)) : (co ? co.note : null),
                        duration,
                        status: ci
                            ? (ci.is_within_range ? 'Hadir' : 'Luar Jangkauan')
                            : 'Tidak Hadir',
                    };
                });
            } catch (e) {
                console.error(e);
            } finally {
                this.loadingDaily = false;
            }
        },

        viewPhoto(url, name, note) {
            window.dispatchEvent(new CustomEvent('att-view-photo', { detail: { url, name, note: note || '' } }));
        },

        initials(name) {
            const parts = (name || '').trim().split(' ');
            return parts.length >= 2
                ? (parts[0][0] + parts[1][0]).toUpperCase()
                : (parts[0][0] || '?').toUpperCase();
        },
    }));
});
</script>
@endpush
@endsection
