@extends('layouts.app')

@section('title', 'Dashboard Sales & CRM - PT IP Network Solusindo')

@push('styles')
<style>
    .ipnet-card {
        background-color: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 18px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03), 0 4px 12px rgba(0, 0, 0, 0.02);
        transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .ipnet-card:hover {
        border-color: #CBD5E1;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.05);
    }
    .stat-card {
        background-color: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
        transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .stat-card:hover {
        transform: translateY(-2px);
        border-color: #CBD5E1;
        box-shadow: 0 8px 22px rgba(0, 0, 0, 0.06);
    }
</style>
@endpush

@section('content')
<div class="flex h-screen overflow-hidden bg-[#F8FAFC] font-sans" x-data="{
    searchQuery: '',
    statusFilter: 'ALL',
    filterTable(rowStatus, rowText) {
        const matchesStatus = this.statusFilter === 'ALL' || rowStatus.toUpperCase().includes(this.statusFilter);
        const matchesSearch = !this.searchQuery || rowText.toLowerCase().includes(this.searchQuery.toLowerCase());
        return matchesStatus && matchesSearch;
    }
}">
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Dashboard Sales'])
        
        <div class="p-4 sm:p-6 lg:p-8 space-y-6 max-w-full mx-auto">
            
            {{-- HEADER: Title, Subtitle, Year Selector & Quick Actions --}}
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200/90 shadow-2xs">
                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-red-50 text-[#8F0A0D] border border-red-200/60 uppercase tracking-wider">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#8F0A0D] animate-pulse"></span>
                            Commercial Workspace
                        </span>
                        <span class="text-xs font-medium text-slate-400">•</span>
                        <span class="text-xs font-semibold text-slate-500">Tahun {{ $selectedYear }}</span>
                    </div>
                    <h1 class="text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-red-50 text-[#8F0A0D] flex items-center justify-center border border-red-100 shrink-0">
                            <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>
                            </svg>
                        </div>
                        Executive Sales Dashboard
                    </h1>
                    <p class="text-xs text-slate-500">Monitoring performa pendapatan, pipeline deals, dan konversi status project secara terpadu.</p>
                </div>

                {{-- Header Actions --}}
                <div class="flex flex-wrap items-center gap-2.5">
                    {{-- Year Selector Form --}}
                    <form method="GET" action="{{ route('dashboard.sales') }}" class="inline-flex items-center">
                        <div class="relative">
                            <select name="year" onchange="this.form.submit()" 
                                    class="appearance-none pl-8 pr-8 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-700 bg-slate-50/80 hover:bg-slate-100/80 focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 cursor-pointer shadow-2xs transition">
                                @for($y = date('Y'); $y >= 2024; $y--)
                                    <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
                                @endfor
                            </select>
                            <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>
                            </svg>
                            <svg class="w-3.5 h-3.5 text-slate-400 absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
                            </svg>
                        </div>
                    </form>

                    {{-- Quick Navigation: Pipeline --}}
                    <a href="{{ route('sales.pipeline.index') }}" 
                       class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 hover:border-slate-300 shadow-2xs transition">
                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 4.5v15m6-15v15m-10.875 0h15.75c.621 0 1.125-.504 1.125-1.125V5.625c0-.621-.504-1.125-1.125-1.125H4.125C3.504 4.5 3 5.004 3 5.625v12.75c0 .621.504 1.125 1.125 1.125z"/>
                        </svg>
                        Pipeline Board
                    </a>

                    {{-- Quick Navigation: Activities --}}
                    <a href="{{ route('sales.activities.index') }}" 
                       class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold text-white bg-[#8F0A0D] hover:bg-[#73080A] shadow-xs shadow-red-900/20 transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Activity Log
                    </a>
                </div>
            </div>

            {{-- 5 SUMMARY EXECUTIVE METRIC CARDS --}}
            <div>
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2">
                        <h3 class="text-xs font-extrabold text-slate-500 uppercase tracking-wider">Summary KPI Overview</h3>
                        <span class="text-[11px] font-medium text-slate-400">(Periode {{ $selectedYear }})</span>
                    </div>
                    <span class="text-[11px] font-medium text-slate-400">Nilai otomatis dikonversi (Miliar / Juta)</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3.5 lg:gap-4">
                    
                    {{-- 1. Total Nilai Project --}}
                    <div class="stat-card p-4.5 flex flex-col justify-between border-l-4 border-l-[#8F0A0D]">
                        <div>
                            <div class="flex items-center justify-between mb-2.5">
                                <div class="w-8.5 h-8.5 rounded-xl bg-red-50 text-[#8F0A0D] flex items-center justify-center border border-red-100 shadow-2xs">
                                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10.5px] font-extrabold bg-slate-100 text-slate-700">
                                    {{ $totalProjectCount }} Proj
                                </span>
                            </div>
                            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block">Total Nilai Project</span>
                            <div class="text-[21px] xl:text-[23px] font-black text-slate-900 mt-1 tracking-tight truncate" 
                                 title="{{ \App\Helpers\CurrencyHelper::formatRupiah($totalProjectValue) }}">
                                {{ \App\Helpers\CurrencyHelper::formatCompact($totalProjectValue) }}
                            </div>
                        </div>
                        <div class="flex items-center justify-between text-[11px] text-slate-500 mt-3 pt-2.5 border-t border-slate-100 font-medium">
                            <span class="text-slate-400">Semua Project</span>
                            <span class="font-bold text-slate-800">{{ $totalProjectCount }} <span class="text-[10px] font-normal text-slate-400">items</span></span>
                        </div>
                    </div>

                    {{-- 2. Total Nilai Opportunity --}}
                    <div class="stat-card p-4.5 flex flex-col justify-between border-l-4 border-l-blue-500">
                        <div>
                            <div class="flex items-center justify-between mb-2.5">
                                <div class="w-8.5 h-8.5 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-100 shadow-2xs">
                                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.01 6.01 0 001.5-.189m-1.5.189a6.01 6.01 0 01-1.5-.189m3.75 7.478a12.06 12.06 0 01-4.5 0m3.75 2.383a14.406 14.406 0 01-3 0M14.25 18v-.192c0-.983.658-1.823 1.508-2.316a7.5 7.5 0 10-7.516 0c.85.493 1.508 1.333 1.508 2.316V18"/>
                                    </svg>
                                </div>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10.5px] font-extrabold bg-blue-50 text-blue-700">
                                    {{ $totalOppCount }} Deals
                                </span>
                            </div>
                            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block">Total Opportunity</span>
                            <div class="text-[21px] xl:text-[23px] font-black text-slate-900 mt-1 tracking-tight truncate" 
                                 title="{{ \App\Helpers\CurrencyHelper::formatRupiah($totalOppValue) }}">
                                {{ \App\Helpers\CurrencyHelper::formatCompact($totalOppValue) }}
                            </div>
                        </div>
                        <div class="flex items-center justify-between text-[11px] text-slate-500 mt-3 pt-2.5 border-t border-slate-100 font-medium">
                            <span class="text-slate-400">Pipeline Aktif</span>
                            <span class="font-bold text-blue-700">{{ $totalOppCount }} <span class="text-[10px] font-normal text-slate-400">prospek</span></span>
                        </div>
                    </div>

                    {{-- 3. Total Nilai In Progress --}}
                    <div class="stat-card p-4.5 flex flex-col justify-between border-l-4 border-l-amber-500">
                        <div>
                            <div class="flex items-center justify-between mb-2.5">
                                <div class="w-8.5 h-8.5 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center border border-amber-100 shadow-2xs">
                                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/>
                                    </svg>
                                </div>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10.5px] font-extrabold bg-amber-50 text-amber-700">
                                    {{ $totalInProgressCount }} On Going
                                </span>
                            </div>
                            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block">Total In Progress</span>
                            <div class="text-[21px] xl:text-[23px] font-black text-slate-900 mt-1 tracking-tight truncate" 
                                 title="{{ \App\Helpers\CurrencyHelper::formatRupiah($totalInProgressValue) }}">
                                {{ \App\Helpers\CurrencyHelper::formatCompact($totalInProgressValue) }}
                            </div>
                        </div>
                        <div class="flex items-center justify-between text-[11px] text-slate-500 mt-3 pt-2.5 border-t border-slate-100 font-medium">
                            <span class="text-slate-400">Sedang Dikerjakan</span>
                            <span class="font-bold text-amber-700">{{ $totalInProgressCount }} <span class="text-[10px] font-normal text-slate-400">proj</span></span>
                        </div>
                    </div>

                    {{-- 4. Total Nilai Pending --}}
                    <div class="stat-card p-4.5 flex flex-col justify-between border-l-4 border-l-orange-500">
                        <div>
                            <div class="flex items-center justify-between mb-2.5">
                                <div class="w-8.5 h-8.5 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center border border-orange-100 shadow-2xs">
                                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10.5px] font-extrabold bg-orange-50 text-orange-700">
                                    {{ $totalPendingCount }} Hold
                                </span>
                            </div>
                            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block">Total Pending</span>
                            <div class="text-[21px] xl:text-[23px] font-black text-slate-900 mt-1 tracking-tight truncate" 
                                 title="{{ \App\Helpers\CurrencyHelper::formatRupiah($totalPendingValue) }}">
                                {{ \App\Helpers\CurrencyHelper::formatCompact($totalPendingValue) }}
                            </div>
                        </div>
                        <div class="flex items-center justify-between text-[11px] text-slate-500 mt-3 pt-2.5 border-t border-slate-100 font-medium">
                            <span class="text-slate-400">Review / Tertunda</span>
                            <span class="font-bold text-orange-700">{{ $totalPendingCount }} <span class="text-[10px] font-normal text-slate-400">proj</span></span>
                        </div>
                    </div>

                    {{-- 5. Total Nilai Complete --}}
                    <div class="stat-card p-4.5 flex flex-col justify-between border-l-4 border-l-emerald-500">
                        <div>
                            <div class="flex items-center justify-between mb-2.5">
                                <div class="w-8.5 h-8.5 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-100 shadow-2xs">
                                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10.5px] font-extrabold bg-emerald-50 text-emerald-700">
                                    {{ $totalCompleteCount }} Done
                                </span>
                            </div>
                            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block">Total Complete</span>
                            <div class="text-[21px] xl:text-[23px] font-black text-slate-900 mt-1 tracking-tight truncate" 
                                 title="{{ \App\Helpers\CurrencyHelper::formatRupiah($totalCompleteValue) }}">
                                {{ \App\Helpers\CurrencyHelper::formatCompact($totalCompleteValue) }}
                            </div>
                        </div>
                        <div class="flex items-center justify-between text-[11px] text-slate-500 mt-3 pt-2.5 border-t border-slate-100 font-medium">
                            <span class="text-slate-400">Project Selesai</span>
                            <span class="font-bold text-emerald-700">{{ $totalCompleteCount }} <span class="text-[10px] font-normal text-slate-400">proj</span></span>
                        </div>
                    </div>

                </div>
            </div>

            {{-- ANALYTICS ROW: Monthly Revenue Trend + Pipeline Performance Summary --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
                
                {{-- LEFT (7 Cols): NILAI PROJECT MONTHLY CHART --}}
                <div class="lg:col-span-7 ipnet-card p-5 sm:p-6 flex flex-col justify-between">
                    <div>
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border-b border-slate-100 pb-4 mb-4">
                            <div>
                                <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-[#8F0A0D]"></span>
                                    Tren Nilai Project Bulanan
                                </h3>
                                <p class="text-xs text-slate-500 mt-0.5">Perkembangan total perolehan project per bulan tahun {{ $selectedYear }}</p>
                            </div>
                            <div class="inline-flex items-center gap-2 self-start sm:self-auto">
                                <span class="text-[11px] font-bold text-slate-500 bg-slate-100 px-2.5 py-1 rounded-lg">
                                    Total YTD: <strong class="text-slate-900">{{ \App\Helpers\CurrencyHelper::formatCompact($totalProjectValue) }}</strong>
                                </span>
                            </div>
                        </div>
                        <div class="h-64 sm:h-72 w-full">
                            <canvas id="salesMonthlyChart"></canvas>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-t border-slate-100 grid grid-cols-3 gap-2 text-center">
                        <div class="p-2 rounded-xl bg-slate-50">
                            <span class="text-[10px] font-bold text-slate-400 uppercase block">Rata-rata/Bulan</span>
                            <span class="text-xs font-black text-slate-800">{{ \App\Helpers\CurrencyHelper::formatCompact($totalProjectValue / 12) }}</span>
                        </div>
                        <div class="p-2 rounded-xl bg-slate-50">
                            <span class="text-[10px] font-bold text-slate-400 uppercase block">Weighted Pipeline</span>
                            <span class="text-xs font-black text-indigo-700">{{ \App\Helpers\CurrencyHelper::formatCompact($totalWeightedForecast ?? 0) }}</span>
                        </div>
                        <div class="p-2 rounded-xl bg-slate-50">
                            <span class="text-[10px] font-bold text-slate-400 uppercase block">Win Rate</span>
                            <span class="text-xs font-black text-emerald-700">{{ $winRate ?? 0 }}%</span>
                        </div>
                    </div>
                </div>

                {{-- RIGHT (5 Cols): PIPELINE & CLOSING HEALTH --}}
                <div class="lg:col-span-5 ipnet-card p-5 sm:p-6 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-4">
                            <div>
                                <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
                                    Pipeline & Closing Horizon
                                </h3>
                                <p class="text-xs text-slate-500 mt-0.5">Status prospek dan peluang closing</p>
                            </div>
                            <a href="{{ route('sales.pipeline.index') }}" class="text-[11px] font-bold text-red-600 hover:text-red-800 hover:underline">
                                Lihat Semua &rarr;
                            </a>
                        </div>

                        {{-- Metric Tiles in Right Column --}}
                        <div class="space-y-3">
                            {{-- Weighted Forecast --}}
                            <div class="p-3.5 rounded-xl border border-slate-200/80 bg-gradient-to-r from-slate-50 to-white flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center border border-indigo-100 shrink-0">
                                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <span class="text-[11px] font-bold text-slate-500 block uppercase">Weighted Forecast Value</span>
                                        <span class="text-xs text-slate-400">Estimasi nilai berdasarkan probabilitas</span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="text-sm font-black text-indigo-700 block" title="{{ \App\Helpers\CurrencyHelper::formatRupiah($totalWeightedForecast ?? 0) }}">
                                        {{ \App\Helpers\CurrencyHelper::formatCompact($totalWeightedForecast ?? 0) }}
                                    </span>
                                    <span class="text-[10px] font-semibold text-slate-400">{{ $totalPipelineCount ?? $totalOppCount }} deals</span>
                                </div>
                            </div>

                            {{-- Closing Horizon (Negotiation / PO) --}}
                            <div class="p-3.5 rounded-xl border border-slate-200/80 bg-gradient-to-r from-slate-50 to-white flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center border border-amber-100 shrink-0">
                                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.125 2.25h-4.5c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125v-9M10.125 2.25h.375a9 9 0 019 9v.375M10.125 2.25A3.375 3.375 0 0113.5 5.625v1.5c0 .621.504 1.125 1.125 1.125h1.5a3.375 3.375 0 013.375 3.375M9 15l2.25 2.25L15 12"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <span class="text-[11px] font-bold text-slate-500 block uppercase">Tahap Negosiasi / SPK</span>
                                        <span class="text-xs text-slate-400">Peluang closing dalam waktu dekat</span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="text-sm font-black text-amber-700 block" title="{{ \App\Helpers\CurrencyHelper::formatRupiah($totalNegotiationValue ?? 0) }}">
                                        {{ \App\Helpers\CurrencyHelper::formatCompact($totalNegotiationValue ?? 0) }}
                                    </span>
                                    <span class="text-[10px] font-semibold text-slate-400">{{ $totalNegotiationCount ?? 0 }} prospek</span>
                                </div>
                            </div>

                            {{-- Handover Pending --}}
                            <div class="p-3.5 rounded-xl border border-slate-200/80 bg-gradient-to-r from-slate-50 to-white flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-red-50 text-[#8F0A0D] flex items-center justify-center border border-red-100 shrink-0">
                                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 7.5h-.75A2.25 2.25 0 004.5 9.75v7.5a2.25 2.25 0 002.25 2.25h7.5a2.25 2.25 0 002.25-2.25v-7.5a2.25 2.25 0 00-2.25-2.25h-.75m0-3l-3-3m0 0l-3 3m3-3v11.25m6-2.25h.75a2.25 2.25 0 012.25 2.25v7.5a2.25 2.25 0 01-2.25 2.25h-7.5a2.25 2.25 0 01-2.25-2.25v-.75"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <span class="text-[11px] font-bold text-slate-500 block uppercase">Commercial Handover</span>
                                        <span class="text-xs text-slate-400">Siap diserahterimakan ke Delivery</span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="text-sm font-black text-slate-800 block">{{ $pendingHandoverCount ?? 0 }} Draft</span>
                                    <a href="{{ route('sales.handover.index') }}" class="text-[10px] font-bold text-red-600 hover:underline">Kelola &rarr;</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Win Rate Bar --}}
                    <div class="mt-4 pt-3.5 border-t border-slate-100">
                        <div class="flex items-center justify-between text-xs mb-1.5 font-bold">
                            <span class="text-slate-600 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                Win Rate Conversion
                            </span>
                            <span class="text-emerald-700 font-extrabold">{{ $winRate ?? 0 }}%</span>
                        </div>
                        <div class="w-full bg-slate-100 h-2.5 rounded-full overflow-hidden flex">
                            <div class="bg-emerald-500 h-full transition-all duration-500" style="width: {{ min(100, max(0, $winRate ?? 0)) }}%"></div>
                            <div class="bg-slate-300 h-full" style="width: {{ 100 - min(100, max(0, $winRate ?? 0)) }}%"></div>
                        </div>
                        <div class="flex items-center justify-between text-[10px] text-slate-400 mt-1.5 font-medium">
                            <span>Won: <strong class="text-slate-700">{{ $totalWonCount ?? 0 }}</strong></span>
                            <span>Lost: <strong class="text-slate-700">{{ $totalLostCount ?? 0 }}</strong></span>
                        </div>
                    </div>
                </div>

            </div>

            {{-- PROJECT LIST TABLE SECTION --}}
            <div class="ipnet-card overflow-hidden">
                {{-- Table Top Filter Bar --}}
                <div class="p-4 sm:p-5 border-b border-slate-100 flex flex-col md:flex-row md:items-center md:justify-between gap-3.5 bg-slate-50/50">
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#8F0A0D]"></span>
                            Daftar Project Terdaftar
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">Seluruh project yang tercatat pada tahun {{ $selectedYear }}</p>
                    </div>

                    {{-- Search & Tab Filters --}}
                    <div class="flex flex-wrap items-center gap-2.5">
                        {{-- Search Input --}}
                        <div class="relative min-w-[200px]">
                            <input type="text" 
                                   x-model="searchQuery"
                                   placeholder="Cari project / client / sales..." 
                                   class="w-full pl-8 pr-3 py-1.5 rounded-xl border border-slate-200 text-xs text-slate-800 bg-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 shadow-2xs">
                            <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                            </svg>
                        </div>

                        {{-- Filter Pill Buttons --}}
                        <div class="inline-flex p-1 rounded-xl bg-slate-200/70 text-[11px] font-bold">
                            <button type="button" @click="statusFilter = 'ALL'" 
                                    :class="statusFilter === 'ALL' ? 'bg-white text-slate-900 shadow-2xs' : 'text-slate-600 hover:text-slate-900'"
                                    class="px-2.5 py-1 rounded-lg transition">Semua</button>
                            <button type="button" @click="statusFilter = 'OPPORTUNITY'" 
                                    :class="statusFilter === 'OPPORTUNITY' ? 'bg-white text-blue-700 shadow-2xs' : 'text-slate-600 hover:text-slate-900'"
                                    class="px-2.5 py-1 rounded-lg transition">Opportunity</button>
                            <button type="button" @click="statusFilter = 'PROGRESS'" 
                                    :class="statusFilter === 'PROGRESS' ? 'bg-white text-amber-700 shadow-2xs' : 'text-slate-600 hover:text-slate-900'"
                                    class="px-2.5 py-1 rounded-lg transition">Progress</button>
                            <button type="button" @click="statusFilter = 'COMPLET'" 
                                    :class="statusFilter === 'COMPLET' ? 'bg-white text-emerald-700 shadow-2xs' : 'text-slate-600 hover:text-slate-900'"
                                    class="px-2.5 py-1 rounded-lg transition">Complete</button>
                        </div>
                    </div>
                </div>

                {{-- Table Content --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="border-b border-slate-200 bg-slate-50/80 text-slate-500 uppercase text-[10.5px] font-extrabold tracking-wider">
                                <th class="py-3.5 px-5">SALES PIC</th>
                                <th class="py-3.5 px-5">SO / PO NUMBER</th>
                                <th class="py-3.5 px-5">CLIENT</th>
                                <th class="py-3.5 px-5">PROJECT NAME</th>
                                <th class="py-3.5 px-5 text-right">REVENUE</th>
                                <th class="py-3.5 px-5 text-right">EST. GP (25%)</th>
                                <th class="py-3.5 px-5 text-center">STATUS</th>
                                <th class="py-3.5 px-5 text-right">ACTION</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-800 font-medium">
                            @forelse($projectList as $p)
                                @php
                                    $salesName = $p->sales_name ?: ($p->creator ? $p->creator->name : 'N/A');
                                    $statusLower = strtolower($p->status ?? '');
                                    $statusBadge = match(true) {
                                        str_contains($statusLower, 'complete') || str_contains($statusLower, 'done') => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        str_contains($statusLower, 'progress') || str_contains($statusLower, 'active') => 'bg-amber-50 text-amber-700 border-amber-200',
                                        str_contains($statusLower, 'opportunity') || str_contains($statusLower, 'prospect') => 'bg-blue-50 text-blue-700 border-blue-200',
                                        str_contains($statusLower, 'pending') || str_contains($statusLower, 'hold') => 'bg-orange-50 text-orange-700 border-orange-200',
                                        default => 'bg-slate-100 text-slate-700 border-slate-200',
                                    };
                                    $searchBlob = strtolower($p->name . ' ' . $p->client . ' ' . $salesName . ' ' . ($p->po_number ?? '') . ' ' . ($p->quotation_number ?? ''));
                                @endphp
                                <tr class="hover:bg-slate-50/80 transition group"
                                    x-show="filterTable('{{ addslashes($p->status ?? '') }}', '{{ addslashes($searchBlob) }}')">
                                    {{-- Sales PIC --}}
                                    <td class="py-3.5 px-5">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-7 h-7 rounded-full bg-slate-100 text-slate-700 font-extrabold text-[11px] flex items-center justify-center border border-slate-200 shrink-0 uppercase">
                                                {{ substr($salesName, 0, 2) }}
                                            </div>
                                            <span class="font-bold text-slate-900 truncate max-w-[120px]">{{ $salesName }}</span>
                                        </div>
                                    </td>

                                    {{-- SO / PO Number --}}
                                    <td class="py-3.5 px-5 font-mono text-[11px] text-slate-600">
                                        <span class="px-2 py-0.5 rounded bg-slate-100 border border-slate-200 font-semibold">
                                            {{ $p->po_number ?: ($p->quotation_number ?: '-') }}
                                        </span>
                                    </td>

                                    {{-- Client --}}
                                    <td class="py-3.5 px-5 font-bold text-slate-900">
                                        {{ $p->client ?: '-' }}
                                    </td>

                                    {{-- Project --}}
                                    <td class="py-3.5 px-5">
                                        <div class="font-bold text-slate-900 max-w-xs truncate" title="{{ $p->name }}">{{ $p->name }}</div>
                                        @if($p->division)
                                            <span class="text-[10px] text-slate-400 font-normal">{{ $p->division->name }}</span>
                                        @endif
                                    </td>

                                    {{-- Revenue --}}
                                    <td class="py-3.5 px-5 text-right font-black text-slate-900 whitespace-nowrap" 
                                        title="{{ \App\Helpers\CurrencyHelper::formatRupiah($p->contract_value) }}">
                                        {{ \App\Helpers\CurrencyHelper::formatCompact($p->contract_value) }}
                                        <span class="block text-[10px] font-normal text-slate-400">
                                            Rp {{ number_format($p->contract_value, 0, ',', '.') }}
                                        </span>
                                    </td>

                                    {{-- Gross Profit --}}
                                    <td class="py-3.5 px-5 text-right font-bold text-emerald-600 whitespace-nowrap">
                                        {{ \App\Helpers\CurrencyHelper::formatCompact($p->contract_value * 0.25) }}
                                    </td>

                                    {{-- Status --}}
                                    <td class="py-3.5 px-5 text-center whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10.5px] font-extrabold border {{ $statusBadge }}">
                                            {{ $p->status ?: 'N/A' }}
                                        </span>
                                    </td>

                                    {{-- Action --}}
                                    <td class="py-3.5 px-5 text-right whitespace-nowrap">
                                        <a href="{{ route('projects.show', $p->id) }}" 
                                           class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-xs font-bold text-slate-700 bg-white border border-slate-200 hover:bg-red-50 hover:text-red-700 hover:border-red-200 shadow-2xs transition">
                                            Detail
                                            <svg class="w-3 h-3 text-slate-400 group-hover:text-red-600 group-hover:translate-x-0.5 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="py-12 text-center text-slate-400 text-xs">
                                        <svg class="w-8 h-8 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                                        </svg>
                                        Belum ada data project pada periode tahun ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="p-4 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-xs text-slate-500 bg-slate-50/40">
                    <div class="font-medium">
                        Menampilkan <strong class="text-slate-800">{{ $projectList->firstItem() ?? 0 }}-{{ $projectList->lastItem() ?? 0 }}</strong> dari total <strong class="text-slate-800">{{ $projectList->total() }}</strong> project
                    </div>
                    <div>
                        {{ $projectList->links() }}
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('salesMonthlyChart').getContext('2d');
        const rawAmounts = @json($monthlyChartRaw ?? []);
        const billionAmounts = @json($monthlyChartData ?? []);

        // Dynamic compact currency formatter for Chart JS
        function formatCompactRupiah(val) {
            if (!val || val === 0) return 'Rp 0';
            const abs = Math.abs(val);
            const sign = val < 0 ? '-' : '';
            if (abs >= 1000000000) {
                let n = (abs / 1000000000).toFixed(2).replace('.', ',');
                n = n.replace(/,00$/, '').replace(/(,[0-9])0$/, '$1');
                return sign + 'Rp ' + n + ' M';
            }
            if (abs >= 1000000) {
                let n = (abs / 1000000).toFixed(2).replace('.', ',');
                n = n.replace(/,00$/, '').replace(/(,[0-9])0$/, '$1');
                return sign + 'Rp ' + n + ' Jt';
            }
            if (abs >= 1000) {
                let n = (abs / 1000).toFixed(1).replace('.', ',');
                n = n.replace(/,0$/, '');
                return sign + 'Rp ' + n + ' Rb';
            }
            return sign + 'Rp ' + Math.round(abs).toLocaleString('id-ID');
        }

        // Gradient Background for Chart
        const gradient = ctx.createLinearGradient(0, 0, 0, 260);
        gradient.addColorStop(0, 'rgba(143, 10, 13, 0.22)');
        gradient.addColorStop(1, 'rgba(143, 10, 13, 0.00)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'],
                datasets: [{
                    label: 'Nilai Project',
                    data: rawAmounts.length ? rawAmounts : billionAmounts.map(v => v * 1000000000),
                    borderColor: '#8F0A0D',
                    backgroundColor: gradient,
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.35,
                    pointBackgroundColor: '#8F0A0D',
                    pointBorderColor: '#FFFFFF',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointHoverBackgroundColor: '#73080A',
                    pointHoverBorderColor: '#FFFFFF',
                    pointHoverBorderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0F172A',
                        titleColor: '#F8FAFC',
                        bodyColor: '#F8FAFC',
                        padding: 10,
                        cornerRadius: 10,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                const val = context.raw || 0;
                                const compact = formatCompactRupiah(val);
                                const exact = 'Rp ' + Math.round(val).toLocaleString('id-ID');
                                return `${compact} (${exact})`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#F1F5F9', drawBorder: false },
                        ticks: {
                            font: { size: 10, family: 'Inter', weight: '600' },
                            color: '#94A3B8',
                            callback: function(value) {
                                return formatCompactRupiah(value);
                            }
                        }
                    },
                    x: {
                        grid: { display: false, drawBorder: false },
                        ticks: {
                            font: { size: 11, family: 'Inter', weight: '600' },
                            color: '#64748B'
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
@endsection
