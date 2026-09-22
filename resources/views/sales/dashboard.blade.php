@extends('layouts.app')

@section('title', 'Dashboard Sales & CRM - PT IP Network Solusindo')

@push('styles')
<style>
    :root {
        --ipnet-red: #8F0A0D;
        --ipnet-red-dark: #73080A;
    }

    .dash-card {
        background: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 14px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.04), 0 4px 16px rgba(0,0,0,0.02);
        transition: box-shadow 0.2s ease, border-color 0.2s ease;
    }
    .dash-card:hover {
        border-color: #CBD5E1;
        box-shadow: 0 4px 20px rgba(0,0,0,0.07);
    }

    .kpi-card {
        background: #FFFFFF;
        border: 1px solid #E8EDF3;
        border-radius: 14px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
        padding: 18px 20px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 140px;
    }
    .kpi-card:hover {
        transform: translateY(-2px);
        border-color: #CBD5E1;
        box-shadow: 0 6px 20px rgba(0,0,0,0.08);
    }

    .icon-box {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border: 1px solid transparent;
    }

    .metric-tile {
        background: linear-gradient(135deg, #F8FAFC 0%, #FFFFFF 100%);
        border: 1px solid #E8EDF3;
        border-radius: 12px;
        padding: 14px 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }
    .metric-tile:hover {
        border-color: #CBD5E1;
        box-shadow: 0 3px 12px rgba(0,0,0,0.05);
    }

    .project-row:hover { background-color: #F8FAFC; }

    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 2px 9px;
        border-radius: 999px;
        font-size: 10.5px;
        font-weight: 700;
        border: 1px solid transparent;
        white-space: nowrap;
    }

    .pill-group {
        display: inline-flex;
        background: #F1F5F9;
        border-radius: 10px;
        padding: 3px;
        gap: 2px;
    }
    .pill-btn {
        padding: 4px 12px;
        border-radius: 8px;
        font-size: 11px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.15s ease;
        color: #64748B;
        border: 1px solid transparent;
        background: transparent;
    }
    .pill-btn.active {
        background: #FFFFFF;
        color: #0F172A;
        box-shadow: 0 1px 4px rgba(0,0,0,0.08);
        border-color: #E2E8F0;
    }

    @keyframes slideUp {
        from { opacity: 0; transform: translateY(10px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .anim-up { animation: slideUp 0.4s cubic-bezier(0.16,1,0.3,1) both; }
    .anim-up-1 { animation-delay: 0.04s; }
    .anim-up-2 { animation-delay: 0.08s; }
    .anim-up-3 { animation-delay: 0.12s; }
    .anim-up-4 { animation-delay: 0.16s; }
    .anim-up-5 { animation-delay: 0.20s; }

    .overflow-x-auto::-webkit-scrollbar { height: 5px; }
    .overflow-x-auto::-webkit-scrollbar-track { background: #F1F5F9; border-radius: 4px; }
    .overflow-x-auto::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 4px; }
</style>
@endpush

@section('content')
<div class="flex h-screen overflow-hidden bg-[#F5F7FA] font-sans" x-data="{
    searchQuery: '',
    statusFilter: 'ALL',
    filterRow(rowStatus, rowText) {
        const matchStatus = this.statusFilter === 'ALL' || rowStatus.toUpperCase().includes(this.statusFilter);
        const matchSearch = !this.searchQuery || rowText.toLowerCase().includes(this.searchQuery.toLowerCase());
        return matchStatus && matchSearch;
    }
}">
    @include('components.sidebar')

    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Dashboard Sales'])

        <div class="px-5 py-5 lg:px-8 lg:py-6 space-y-5 max-w-screen-2xl mx-auto">

            {{-- ═══ HERO BANNER (greeting) ═══ --}}
            <div style="background: linear-gradient(135deg, #8F0A0D 0%, #B81525 50%, #8F0A0D 100%); border-radius: 16px; position: relative; overflow: hidden; box-shadow: 0 4px 20px rgba(143,10,13,0.22); padding: 20px 28px;">
                {{-- Decorative overlay (SVG with explicit height) --}}
                <div style="position:absolute; inset:0; pointer-events:none; overflow:hidden;">
                    <svg width="100%" height="100%" viewBox="0 0 1440 120" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
                        <polygon points="0,0 480,0 800,120 0,120" fill="rgba(0,0,0,0.12)"/>
                        <polygon points="600,0 1440,0 1440,120 1000,120" fill="rgba(0,0,0,0.10)"/>
                        <polygon points="300,0 900,0 1300,120 600,120" fill="rgba(255,255,255,0.05)"/>
                        <circle cx="1380" cy="10" r="100" fill="rgba(255,255,255,0.04)"/>
                        <circle cx="60" cy="110" r="80" fill="rgba(0,0,0,0.08)"/>
                    </svg>
                </div>

                {{-- Content --}}
                <div style="position:relative; z-index:10; display:flex; flex-wrap:wrap; align-items:center; justify-content:space-between; gap:16px;">
                    <div>
                        <div style="display:inline-flex; align-items:center; gap:6px; padding:3px 10px; border-radius:999px; background:rgba(255,255,255,0.15); border:1px solid rgba(255,255,255,0.25); font-size:10.5px; font-weight:700; color:#FFFFFF; text-transform:uppercase; letter-spacing:0.06em; margin-bottom:8px;">
                            <span style="width:6px; height:6px; border-radius:50%; background:#FFFFFF; display:inline-block;"></span>
                            PT IP NETWORK SOLUSINDO &bull; SALES DASHBOARD
                        </div>
                        <h1 style="font-size:20px; font-weight:700; color:#FFFFFF; letter-spacing:-0.3px; line-height:1.25; margin:0 0 4px 0;">
                            Selamat Datang, {{ auth()->user()->name }}
                        </h1>
                        <p style="font-size:12.5px; color:rgba(255,255,255,0.75); margin:0; line-height:1.5;">
                            Monitoring performa project, pipeline &amp; konversi deals — Tahun {{ $selectedYear }}
                        </p>
                    </div>

                    <div style="display:flex; flex-wrap:wrap; align-items:center; gap:8px;">
                        {{-- Year selector --}}
                        <form method="GET" action="{{ route('dashboard.sales') }}">
                            <div style="position:relative; display:inline-flex; align-items:center;">
                                <svg style="width:14px; height:14px; position:absolute; left:10px; pointer-events:none; color:rgba(255,255,255,0.7);" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <select name="year" onchange="this.form.submit()"
                                        style="appearance:none; padding:7px 28px 7px 30px; background:rgba(255,255,255,0.15); border:1px solid rgba(255,255,255,0.25); border-radius:10px; font-size:11.5px; font-weight:700; color:#FFFFFF; cursor:pointer; outline:none;">
                                    @for($y = date('Y'); $y >= 2024; $y--)
                                        <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }} style="color:#1E293B; background:#FFFFFF;">Tahun {{ $y }}</option>
                                    @endfor
                                </select>
                                <svg style="width:12px; height:12px; position:absolute; right:8px; pointer-events:none; color:rgba(255,255,255,0.7);" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
                                </svg>
                            </div>
                        </form>

                        <a href="{{ route('sales.pipeline.index') }}"
                           style="display:inline-flex; align-items:center; gap:6px; padding:7px 14px; border-radius:10px; font-size:11.5px; font-weight:700; color:#FFFFFF; background:rgba(255,255,255,0.12); border:1px solid rgba(255,255,255,0.22); text-decoration:none; transition:background .15s;">
                            <svg style="width:14px; height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/>
                            </svg>
                            Pipeline Board
                        </a>

                        <a href="{{ route('sales.activities.index') }}"
                           style="display:inline-flex; align-items:center; gap:6px; padding:7px 14px; border-radius:10px; font-size:11.5px; font-weight:700; color:#FFFFFF; background:rgba(255,255,255,0.12); border:1px solid rgba(255,255,255,0.22); text-decoration:none; transition:background .15s;">
                            <svg style="width:14px; height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Activity Log
                        </a>
                    </div>
                </div>
            </div>

            {{-- ═══ KPI CARDS ═══ --}}
            <div>
                <div class="mb-3">
                    <p class="text-xs font-extrabold text-slate-400 uppercase tracking-widest">Ringkasan KPI — {{ $selectedYear }}</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">

                    {{-- 1 --}}
                    <div class="kpi-card border-t-2 border-t-[#8F0A0D] anim-up anim-up-1">
                        <div style="display:flex; align-items:flex-start; justify-content:space-between;">
                            <div class="icon-box bg-red-50 border-red-100">
                                <svg class="w-4 h-4 text-[#8F0A0D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <span class="text-xs font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-lg">{{ $totalProjectCount }}</span>
                        </div>
                        <div style="margin-top:12px;">
                            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide" style="margin-bottom:4px;">Total Nilai Project</p>
                            <p style="font-size:22px; font-weight:900; color:#0F172A; line-height:1; letter-spacing:-0.5px;"
                               title="{{ \App\Helpers\CurrencyHelper::formatRupiah($totalProjectValue) }}">
                                {{ \App\Helpers\CurrencyHelper::formatCompact($totalProjectValue) }}
                            </p>
                        </div>
                        <div style="display:flex; align-items:center; justify-content:space-between; margin-top:12px; padding-top:10px; border-top:1px solid #F1F5F9; font-size:11px; color:#94A3B8; font-weight:500;">
                            <span>Semua project</span>
                            <strong style="color:#334155;">{{ $totalProjectCount }}</strong>
                        </div>
                    </div>

                    {{-- 2 --}}
                    <div class="kpi-card border-t-2 border-t-blue-500 anim-up anim-up-2">
                        <div style="display:flex; align-items:flex-start; justify-content:space-between;">
                            <div class="icon-box bg-blue-50 border-blue-100">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z"/>
                                </svg>
                            </div>
                            <span class="text-xs font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-lg">{{ $totalOppCount }}</span>
                        </div>
                        <div style="margin-top:12px;">
                            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide" style="margin-bottom:4px;">Total Opportunity</p>
                            <p style="font-size:22px; font-weight:900; color:#0F172A; line-height:1; letter-spacing:-0.5px;"
                               title="{{ \App\Helpers\CurrencyHelper::formatRupiah($totalOppValue) }}">
                                {{ \App\Helpers\CurrencyHelper::formatCompact($totalOppValue) }}
                            </p>
                        </div>
                        <div style="display:flex; align-items:center; justify-content:space-between; margin-top:12px; padding-top:10px; border-top:1px solid #F1F5F9; font-size:11px; color:#94A3B8; font-weight:500;">
                            <span>Pipeline aktif</span>
                            <strong style="color:#2563EB;">{{ $totalOppCount }} prospek</strong>
                        </div>
                    </div>

                    {{-- 3 --}}
                    <div class="kpi-card border-t-2 border-t-amber-500 anim-up anim-up-3">
                        <div style="display:flex; align-items:flex-start; justify-content:space-between;">
                            <div class="icon-box bg-amber-50 border-amber-100">
                                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/>
                                </svg>
                            </div>
                            <span class="text-xs font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-lg">{{ $totalInProgressCount }}</span>
                        </div>
                        <div style="margin-top:12px;">
                            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide" style="margin-bottom:4px;">Total In Progress</p>
                            <p style="font-size:22px; font-weight:900; color:#0F172A; line-height:1; letter-spacing:-0.5px;"
                               title="{{ \App\Helpers\CurrencyHelper::formatRupiah($totalInProgressValue) }}">
                                {{ \App\Helpers\CurrencyHelper::formatCompact($totalInProgressValue) }}
                            </p>
                        </div>
                        <div style="display:flex; align-items:center; justify-content:space-between; margin-top:12px; padding-top:10px; border-top:1px solid #F1F5F9; font-size:11px; color:#94A3B8; font-weight:500;">
                            <span>Sedang dikerjakan</span>
                            <strong style="color:#B45309;">{{ $totalInProgressCount }} project</strong>
                        </div>
                    </div>

                    {{-- 4 --}}
                    <div class="kpi-card border-t-2 border-t-orange-500 anim-up anim-up-4">
                        <div style="display:flex; align-items:flex-start; justify-content:space-between;">
                            <div class="icon-box bg-orange-50 border-orange-100">
                                <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <span class="text-xs font-bold text-orange-700 bg-orange-50 px-2 py-0.5 rounded-lg">{{ $totalPendingCount }}</span>
                        </div>
                        <div style="margin-top:12px;">
                            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide" style="margin-bottom:4px;">Total Pending</p>
                            <p style="font-size:22px; font-weight:900; color:#0F172A; line-height:1; letter-spacing:-0.5px;"
                               title="{{ \App\Helpers\CurrencyHelper::formatRupiah($totalPendingValue) }}">
                                {{ \App\Helpers\CurrencyHelper::formatCompact($totalPendingValue) }}
                            </p>
                        </div>
                        <div style="display:flex; align-items:center; justify-content:space-between; margin-top:12px; padding-top:10px; border-top:1px solid #F1F5F9; font-size:11px; color:#94A3B8; font-weight:500;">
                            <span>Review / tertunda</span>
                            <strong style="color:#C2410C;">{{ $totalPendingCount }} project</strong>
                        </div>
                    </div>

                    {{-- 5 --}}
                    <div class="kpi-card border-t-2 border-t-emerald-500 anim-up anim-up-5">
                        <div style="display:flex; align-items:flex-start; justify-content:space-between;">
                            <div class="icon-box bg-emerald-50 border-emerald-100">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <span class="text-xs font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-lg">{{ $totalCompleteCount }}</span>
                        </div>
                        <div style="margin-top:12px;">
                            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide" style="margin-bottom:4px;">Total Complete</p>
                            <p style="font-size:22px; font-weight:900; color:#0F172A; line-height:1; letter-spacing:-0.5px;"
                               title="{{ \App\Helpers\CurrencyHelper::formatRupiah($totalCompleteValue) }}">
                                {{ \App\Helpers\CurrencyHelper::formatCompact($totalCompleteValue) }}
                            </p>
                        </div>
                        <div style="display:flex; align-items:center; justify-content:space-between; margin-top:12px; padding-top:10px; border-top:1px solid #F1F5F9; font-size:11px; color:#94A3B8; font-weight:500;">
                            <span>Project selesai</span>
                            <strong style="color:#047857;">{{ $totalCompleteCount }} project</strong>
                        </div>
                    </div>

                </div>
            </div>

            {{-- ═══ ANALYTICS: CHART + PIPELINE ═══ --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">

                {{-- Chart --}}
                <div class="lg:col-span-7 dash-card p-5">
                    <div class="flex items-start justify-between mb-4 pb-4 border-b border-slate-100">
                        <div>
                            <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-[#8F0A0D] inline-block"></span>
                                Tren Nilai Project Bulanan
                            </h2>
                            <p class="text-xs text-slate-500 mt-0.5">Total perolehan project per bulan — {{ $selectedYear }}</p>
                        </div>
                        <div class="text-right shrink-0">
                            <p class="text-xs font-semibold text-slate-400">Total YTD</p>
                            <p class="text-sm font-black text-slate-900">{{ \App\Helpers\CurrencyHelper::formatCompact($totalProjectValue) }}</p>
                        </div>
                    </div>

                    <div style="height: 230px; position: relative;">
                        <canvas id="salesMonthlyChart"></canvas>
                    </div>

                    <div class="grid grid-cols-3 gap-3 mt-4 pt-4 border-t border-slate-100">
                        <div class="bg-slate-50 rounded-xl p-3 text-center">
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wide" style="margin-bottom:4px;">Rata-rata/Bln</p>
                            <p class="text-sm font-black text-slate-800">{{ \App\Helpers\CurrencyHelper::formatCompact($totalProjectValue / 12) }}</p>
                        </div>
                        <div class="bg-indigo-50 rounded-xl p-3 text-center">
                            <p class="text-xs font-bold text-indigo-400 uppercase tracking-wide" style="margin-bottom:4px;">Weighted Pipeline</p>
                            <p class="text-sm font-black text-indigo-700">{{ \App\Helpers\CurrencyHelper::formatCompact($totalWeightedForecast ?? 0) }}</p>
                        </div>
                        <div class="bg-emerald-50 rounded-xl p-3 text-center">
                            <p class="text-xs font-bold text-emerald-500 uppercase tracking-wide" style="margin-bottom:4px;">Win Rate</p>
                            <p class="text-sm font-black text-emerald-700">{{ $winRate ?? 0 }}%</p>
                        </div>
                    </div>
                </div>

                {{-- Pipeline Panel --}}
                <div class="lg:col-span-5 dash-card p-5" style="display:flex; flex-direction:column;">
                    <div class="flex items-center justify-between mb-4 pb-4 border-b border-slate-100">
                        <div>
                            <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-blue-500 inline-block"></span>
                                Pipeline & Closing Horizon
                            </h2>
                            <p class="text-xs text-slate-500 mt-0.5">Status prospek & peluang closing</p>
                        </div>
                        <a href="{{ route('sales.pipeline.index') }}" class="text-xs font-bold text-[#8F0A0D] hover:underline shrink-0">Lihat →</a>
                    </div>

                    <div style="display:flex; flex-direction:column; gap:10px; flex:1;">
                        {{-- Weighted --}}
                        <div class="metric-tile">
                            <div style="display:flex; align-items:center; gap:12px; min-width:0;">
                                <div class="icon-box bg-indigo-50 border-indigo-100 shrink-0">
                                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941"/>
                                    </svg>
                                </div>
                                <div style="min-width:0;">
                                    <p class="text-xs font-bold text-slate-600 uppercase tracking-wide">Weighted Forecast</p>
                                    <p class="text-xs text-slate-400 mt-0.5">Estimasi nilai × probabilitas</p>
                                </div>
                            </div>
                            <div style="text-align:right; flex-shrink:0;">
                                <p class="text-sm font-black text-indigo-700" title="{{ \App\Helpers\CurrencyHelper::formatRupiah($totalWeightedForecast ?? 0) }}">
                                    {{ \App\Helpers\CurrencyHelper::formatCompact($totalWeightedForecast ?? 0) }}
                                </p>
                                <p class="text-xs text-slate-400 font-semibold">{{ $totalPipelineCount ?? $totalOppCount }} deals</p>
                            </div>
                        </div>

                        {{-- Negosiasi --}}
                        <div class="metric-tile">
                            <div style="display:flex; align-items:center; gap:12px; min-width:0;">
                                <div class="icon-box bg-amber-50 border-amber-100 shrink-0">
                                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.125 2.25h-4.5c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125v-9M10.125 2.25h.375a9 9 0 019 9v.375M10.125 2.25A3.375 3.375 0 0113.5 5.625v1.5c0 .621.504 1.125 1.125 1.125h1.5a3.375 3.375 0 013.375 3.375M9 15l2.25 2.25L15 12"/>
                                    </svg>
                                </div>
                                <div style="min-width:0;">
                                    <p class="text-xs font-bold text-slate-600 uppercase tracking-wide">Negosiasi / SPK</p>
                                    <p class="text-xs text-slate-400 mt-0.5">Closing dalam waktu dekat</p>
                                </div>
                            </div>
                            <div style="text-align:right; flex-shrink:0;">
                                <p class="text-sm font-black text-amber-700" title="{{ \App\Helpers\CurrencyHelper::formatRupiah($totalNegotiationValue ?? 0) }}">
                                    {{ \App\Helpers\CurrencyHelper::formatCompact($totalNegotiationValue ?? 0) }}
                                </p>
                                <p class="text-xs text-slate-400 font-semibold">{{ $totalNegotiationCount ?? 0 }} prospek</p>
                            </div>
                        </div>

                        {{-- Handover --}}
                        <div class="metric-tile">
                            <div style="display:flex; align-items:center; gap:12px; min-width:0;">
                                <div class="icon-box bg-red-50 border-red-100 shrink-0">
                                    <svg class="w-4 h-4 text-[#8F0A0D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 7.5h-.75A2.25 2.25 0 004.5 9.75v7.5a2.25 2.25 0 002.25 2.25h7.5a2.25 2.25 0 002.25-2.25v-7.5a2.25 2.25 0 00-2.25-2.25h-.75m0-3l-3-3m0 0l-3 3m3-3v11.25m6-2.25h.75a2.25 2.25 0 012.25 2.25v7.5a2.25 2.25 0 01-2.25 2.25h-7.5a2.25 2.25 0 01-2.25-2.25v-.75"/>
                                    </svg>
                                </div>
                                <div style="min-width:0;">
                                    <p class="text-xs font-bold text-slate-600 uppercase tracking-wide">Commercial Handover</p>
                                    <p class="text-xs text-slate-400 mt-0.5">Siap serahkan ke Delivery</p>
                                </div>
                            </div>
                            <div style="text-align:right; flex-shrink:0;">
                                <p class="text-sm font-black text-slate-800">{{ $pendingHandoverCount ?? 0 }} Draft</p>
                                <a href="{{ route('sales.handover.index') }}" class="text-xs font-bold text-[#8F0A0D] hover:underline">Kelola →</a>
                            </div>
                        </div>
                    </div>

                    {{-- Win Rate --}}
                    <div style="margin-top:16px; padding-top:14px; border-top:1px solid #F1F5F9;">
                        <div style="display:flex; align-items:center; justify-content:space-between; font-size:12px; font-weight:700; margin-bottom:8px;">
                            <span style="color:#475569; display:flex; align-items:center; gap:6px;">
                                <span style="width:6px; height:6px; border-radius:50%; background:#10B981; display:inline-block;"></span>
                                Win Rate Conversion
                            </span>
                            <span style="color:#059669;">{{ $winRate ?? 0 }}%</span>
                        </div>
                        <div style="width:100%; background:#F1F5F9; border-radius:999px; overflow:hidden; height:8px;">
                            <div style="height:100%; background:#10B981; border-radius:999px; transition:width .7s ease; width:{{ min(100, max(0, $winRate ?? 0)) }}%;"></div>
                        </div>
                        <div style="display:flex; justify-content:space-between; font-size:11px; color:#94A3B8; font-weight:500; margin-top:6px;">
                            <span>Won: <strong style="color:#334155;">{{ $totalWonCount ?? 0 }}</strong></span>
                            <span>Lost: <strong style="color:#334155;">{{ $totalLostCount ?? 0 }}</strong></span>
                        </div>
                    </div>
                </div>

            </div>

            {{-- ═══ PROJECT TABLE ═══ --}}
            <div class="dash-card overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 flex flex-col md:flex-row md:items-center md:justify-between gap-3 bg-white">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-[#8F0A0D]"></span>
                            Daftar Project Terdaftar
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">Semua project tercatat tahun {{ $selectedYear }}</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2.5">
                        <div class="relative">
                            <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                            </svg>
                            <input type="text" x-model="searchQuery" placeholder="Cari project, client, sales..."
                                   class="pl-8 pr-3 py-1.5 w-56 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-red-400 transition">
                        </div>

                        <div class="pill-group">
                            <button type="button" @click="statusFilter = 'ALL'" :class="statusFilter === 'ALL' ? 'active' : ''" class="pill-btn">Semua</button>
                            <button type="button" @click="statusFilter = 'OPPORTUNITY'" :class="statusFilter === 'OPPORTUNITY' ? 'active' : ''" class="pill-btn">Opportunity</button>
                            <button type="button" @click="statusFilter = 'PROGRESS'" :class="statusFilter === 'PROGRESS' ? 'active' : ''" class="pill-btn">Progress</button>
                            <button type="button" @click="statusFilter = 'COMPLET'" :class="statusFilter === 'COMPLET' ? 'active' : ''" class="pill-btn">Complete</button>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left" style="border-collapse:collapse; font-size:12px;">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200" style="font-size:10.5px; font-weight:700; color:#64748B; text-transform:uppercase; letter-spacing:0.06em;">
                                <th class="py-3 px-4">Sales PIC</th>
                                <th class="py-3 px-4">SO / PO</th>
                                <th class="py-3 px-4">Client</th>
                                <th class="py-3 px-4">Project Name</th>
                                <th class="py-3 px-4 text-right">Revenue</th>
                                <th class="py-3 px-4 text-right">Est. GP 25%</th>
                                <th class="py-3 px-4 text-center">Status</th>
                                <th class="py-3 px-4 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($projectList as $p)
                                @php
                                    $salesName = $p->sales_name ?: ($p->creator ? $p->creator->name : 'N/A');
                                    $sLow      = strtolower($p->status ?? '');
                                    $badge     = match(true) {
                                        str_contains($sLow,'complete') || str_contains($sLow,'done')        => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        str_contains($sLow,'progress') || str_contains($sLow,'active')      => 'bg-amber-50 text-amber-700 border-amber-200',
                                        str_contains($sLow,'opportunity') || str_contains($sLow,'prospect') => 'bg-blue-50 text-blue-700 border-blue-200',
                                        str_contains($sLow,'pending') || str_contains($sLow,'hold')         => 'bg-orange-50 text-orange-700 border-orange-200',
                                        default                                                              => 'bg-slate-100 text-slate-600 border-slate-200',
                                    };
                                    $blob = strtolower($p->name.' '.$p->client.' '.$salesName.' '.($p->po_number ?? '').' '.($p->quotation_number ?? ''));
                                @endphp
                                <tr class="project-row border-b border-slate-100 transition-colors group"
                                    x-show="filterRow('{{ addslashes($p->status ?? '') }}', '{{ addslashes($blob) }}')">

                                    <td class="py-3 px-4 whitespace-nowrap">
                                        <div style="display:flex; align-items:center; gap:8px;">
                                            <div style="width:28px; height:28px; border-radius:50%; background:#F1F5F9; border:1px solid #E2E8F0; display:flex; align-items:center; justify-content:center; font-size:10.5px; font-weight:800; color:#475569; text-transform:uppercase; flex-shrink:0;">
                                                {{ substr($salesName, 0, 2) }}
                                            </div>
                                            <span style="font-weight:600; color:#1E293B; max-width:110px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $salesName }}</span>
                                        </div>
                                    </td>

                                    <td class="py-3 px-4">
                                        <span style="font-family:monospace; font-size:11px; background:#F8FAFC; border:1px solid #E2E8F0; padding:2px 8px; border-radius:8px; color:#475569; font-weight:600;">
                                            {{ $p->po_number ?: ($p->quotation_number ?: '—') }}
                                        </span>
                                    </td>

                                    <td class="py-3 px-4" style="font-weight:600; color:#1E293B; max-width:120px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                                        {{ $p->client ?: '—' }}
                                    </td>

                                    <td class="py-3 px-4" style="max-width:200px;">
                                        <p style="font-weight:600; color:#1E293B; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="{{ $p->name }}">{{ $p->name }}</p>
                                        @if($p->division)
                                            <p style="font-size:10px; color:#94A3B8; margin-top:2px;">{{ $p->division->name }}</p>
                                        @endif
                                    </td>

                                    <td class="py-3 px-4 text-right whitespace-nowrap" title="{{ \App\Helpers\CurrencyHelper::formatRupiah($p->contract_value) }}">
                                        <p style="font-weight:900; color:#0F172A;">{{ \App\Helpers\CurrencyHelper::formatCompact($p->contract_value) }}</p>
                                        <p style="font-size:10px; color:#94A3B8; margin-top:2px;">Rp {{ number_format($p->contract_value, 0, ',', '.') }}</p>
                                    </td>

                                    <td class="py-3 px-4 text-right whitespace-nowrap">
                                        <span style="font-weight:700; color:#059669;">{{ \App\Helpers\CurrencyHelper::formatCompact($p->contract_value * 0.25) }}</span>
                                    </td>

                                    <td class="py-3 px-4 text-center whitespace-nowrap">
                                        <span class="status-badge {{ $badge }}">{{ $p->status ?: 'N/A' }}</span>
                                    </td>

                                    <td class="py-3 px-4 text-right whitespace-nowrap">
                                        <a href="{{ route('projects.show', $p->id) }}"
                                           style="display:inline-flex; align-items:center; gap:4px; padding:5px 12px; background:#FFFFFF; border:1px solid #E2E8F0; border-radius:10px; font-size:11px; font-weight:700; color:#475569; text-decoration:none; transition:all .15s ease; box-shadow:0 1px 3px rgba(0,0,0,0.04);"
                                           onmouseover="this.style.background='#FFF1F2'; this.style.color='#8F0A0D'; this.style.borderColor='#FECACA';"
                                           onmouseout="this.style.background='#FFFFFF'; this.style.color='#475569'; this.style.borderColor='#E2E8F0';">
                                            Detail →
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" style="padding:60px 20px; text-align:center; color:#94A3B8;">
                                        <svg style="width:40px; height:40px; color:#E2E8F0; margin:0 auto 12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                                        </svg>
                                        <p style="font-size:13px; font-weight:600; color:#94A3B8;">Belum ada data project</p>
                                        <p style="font-size:11px; color:#CBD5E1; margin-top:4px;">Tidak ada project tercatat pada periode ini.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-5 py-3.5 border-t border-slate-100 bg-slate-50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2" style="font-size:11.5px; color:#64748B;">
                    <span>
                        Menampilkan <strong style="color:#1E293B;">{{ $projectList->firstItem() ?? 0 }}–{{ $projectList->lastItem() ?? 0 }}</strong>
                        dari <strong style="color:#1E293B;">{{ $projectList->total() }}</strong> project
                    </span>
                    {{ $projectList->links() }}
                </div>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const canvas = document.getElementById('salesMonthlyChart');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');

    const rawAmounts     = @json($monthlyChartRaw ?? []);
    const billionAmounts = @json($monthlyChartData ?? []);
    const data = rawAmounts.length ? rawAmounts : billionAmounts.map(v => v * 1e9);

    function fmtRp(val) {
        if (!val || val === 0) return 'Rp 0';
        const abs = Math.abs(val), s = val < 0 ? '-' : '';
        if (abs >= 1e9) { let n = (abs/1e9).toFixed(2).replace('.',',').replace(/,00$/,'').replace(/(,[0-9])0$/,'$1'); return s+'Rp '+n+' M'; }
        if (abs >= 1e6) { let n = (abs/1e6).toFixed(2).replace('.',',').replace(/,00$/,'').replace(/(,[0-9])0$/,'$1'); return s+'Rp '+n+' Jt'; }
        if (abs >= 1e3) { let n = (abs/1e3).toFixed(1).replace('.',',').replace(/,0$/,''); return s+'Rp '+n+' Rb'; }
        return s+'Rp '+Math.round(abs).toLocaleString('id-ID');
    }

    const grad = ctx.createLinearGradient(0, 0, 0, 230);
    grad.addColorStop(0, 'rgba(143,10,13,0.18)');
    grad.addColorStop(1, 'rgba(143,10,13,0.00)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agt','Sep','Okt','Nov','Des'],
            datasets: [{
                label: 'Nilai Project',
                data: data,
                borderColor: '#8F0A0D',
                backgroundColor: grad,
                borderWidth: 2.5,
                fill: true,
                tension: 0.38,
                pointBackgroundColor: '#8F0A0D',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6,
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
                    bodyColor: '#CBD5E1',
                    padding: 12,
                    cornerRadius: 10,
                    displayColors: false,
                    callbacks: {
                        label: ctx => fmtRp(ctx.raw) + '  (Rp ' + Math.round(ctx.raw).toLocaleString('id-ID') + ')'
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#F1F5F9' },
                    ticks: { callback: fmtRp, font: { size: 10, family: 'Inter', weight: '600' }, color: '#94A3B8' }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 11, family: 'Inter', weight: '600' }, color: '#64748B' }
                }
            }
        }
    });
});
</script>
@endpush
@endsection
