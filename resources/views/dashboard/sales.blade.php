@extends('layouts.app')

@section('title', 'Dashboard Sales - PT IP Network Solusindo')

@push('styles')
<style>
    .ipnet-card {
        background-color: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .ipnet-card:hover {
        border-color: #CBD5E1;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.06);
    }
</style>
@endpush

@section('content')
<div class="flex h-screen overflow-hidden bg-[#F8FAFC] font-sans">
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Dashboard'])
        
        <div class="p-4 sm:p-6 lg:p-8 space-y-6 max-w-full mx-auto">
            
            {{-- HEADER: Title with Red Icon matching reference --}}
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-red-50 text-[#8F0A0D] flex items-center justify-center border border-red-200">
                    <svg class="w-4.5 h-4.5 text-[#8F0A0D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <h1 class="text-xl font-bold text-[#1E293B] tracking-tight">Dashboard</h1>
            </div>

            {{-- YEAR SELECTOR --}}
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Year</label>
                <form method="GET" action="{{ route('dashboard.sales') }}">
                    <select name="year" onchange="this.form.submit()" 
                            class="w-36 px-3 py-2 rounded-xl border border-gray-200 text-xs font-bold text-gray-800 bg-white focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 cursor-pointer shadow-2xs">
                        @for($y = date('Y'); $y >= 2024; $y--)
                            <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </form>
            </div>

            {{-- SUMMARY 5 CARDS ROW (Matching Screenshot 4) --}}
            <div>
                <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Summary</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3.5">
                    
                    {{-- 1. Total nilai project --}}
                    <div class="ipnet-card p-4.5 flex flex-col justify-between">
                        <div class="w-8 h-8 rounded-lg bg-red-50 text-red-600 flex items-center justify-center border border-red-100 mb-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <span class="text-[11px] font-semibold text-gray-400">Total nilai project</span>
                        <div class="text-lg font-black text-gray-900 mt-1">
                            Rp {{ number_format($totalProjectValue / 1000000, 2, ',', '.') }}Jt
                        </div>
                        <div class="flex items-center justify-between text-[11px] text-gray-400 mt-3 pt-2 border-t border-gray-100 font-medium">
                            <span>Total project</span>
                            <span class="font-bold text-gray-700">{{ $totalProjectCount }}</span>
                        </div>
                    </div>

                    {{-- 2. Total nilai opportunity --}}
                    <div class="ipnet-card p-4.5 flex flex-col justify-between">
                        <div class="w-8 h-8 rounded-lg bg-red-50 text-red-600 flex items-center justify-center border border-red-100 mb-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                        </div>
                        <span class="text-[11px] font-semibold text-gray-400">Total nilai opportunity</span>
                        <div class="text-lg font-black text-gray-900 mt-1">
                            Rp {{ number_format($totalOppValue / 1000000, 2, ',', '.') }}Jt
                        </div>
                        <div class="flex items-center justify-between text-[11px] text-gray-400 mt-3 pt-2 border-t border-gray-100 font-medium">
                            <span>Total opportunity</span>
                            <span class="font-bold text-gray-700">{{ $totalOppCount }}</span>
                        </div>
                    </div>

                    {{-- 3. Total nilai in progress --}}
                    <div class="ipnet-card p-4.5 flex flex-col justify-between">
                        <div class="w-8 h-8 rounded-lg bg-red-50 text-red-600 flex items-center justify-center border border-red-100 mb-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <span class="text-[11px] font-semibold text-gray-400">Total nilai in progress</span>
                        <div class="text-lg font-black text-gray-900 mt-1">
                            Rp {{ number_format($totalInProgressValue / 1000000, 2, ',', '.') }}Jt
                        </div>
                        <div class="flex items-center justify-between text-[11px] text-gray-400 mt-3 pt-2 border-t border-gray-100 font-medium">
                            <span>Total in progress</span>
                            <span class="font-bold text-gray-700">{{ $totalInProgressCount }}</span>
                        </div>
                    </div>

                    {{-- 4. Total nilai pending --}}
                    <div class="ipnet-card p-4.5 flex flex-col justify-between">
                        <div class="w-8 h-8 rounded-lg bg-red-50 text-red-600 flex items-center justify-center border border-red-100 mb-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <span class="text-[11px] font-semibold text-gray-400">Total nilai pending</span>
                        <div class="text-lg font-black text-gray-900 mt-1">
                            Rp {{ number_format($totalPendingValue / 1000000, 2, ',', '.') }}Jt
                        </div>
                        <div class="flex items-center justify-between text-[11px] text-gray-400 mt-3 pt-2 border-t border-gray-100 font-medium">
                            <span>Total pending</span>
                            <span class="font-bold text-gray-700">{{ $totalPendingCount }}</span>
                        </div>
                    </div>

                    {{-- 5. Total nilai complete --}}
                    <div class="ipnet-card p-4.5 flex flex-col justify-between">
                        <div class="w-8 h-8 rounded-lg bg-red-50 text-red-600 flex items-center justify-center border border-red-100 mb-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <span class="text-[11px] font-semibold text-gray-400">Total nilai complete</span>
                        <div class="text-lg font-black text-gray-900 mt-1">
                            Rp {{ number_format($totalCompleteValue / 1000000, 2, ',', '.') }}Jt
                        </div>
                        <div class="flex items-center justify-between text-[11px] text-gray-400 mt-3 pt-2 border-t border-gray-100 font-medium">
                            <span>Total complete</span>
                            <span class="font-bold text-gray-700">{{ $totalCompleteCount }}</span>
                        </div>
                    </div>

                </div>
            </div>

            {{-- NILAI PROJECT CHART SECTION --}}
            <div class="ipnet-card p-5 sm:p-6 space-y-4">
                <div>
                    <h3 class="text-sm font-bold text-gray-900">Nilai Project</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Nilai project per bulan di tahun {{ $selectedYear }} (Dalam Milyar Rupiah)</p>
                </div>
                <div class="h-64 w-full">
                    <canvas id="salesMonthlyChart"></canvas>
                </div>
            </div>

            {{-- PROJECT LIST TABLE SECTION --}}
            <div class="ipnet-card overflow-hidden">
                <div class="p-5 border-b border-gray-100">
                    <h3 class="text-sm font-bold text-gray-900">Project List</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="border-b border-gray-200 text-gray-400 uppercase text-[10px] font-bold tracking-wider">
                                <th class="py-3 px-5">SALES</th>
                                <th class="py-3 px-5">SO NUMBER</th>
                                <th class="py-3 px-5">CLIENT</th>
                                <th class="py-3 px-5">PROJECT</th>
                                <th class="py-3 px-5 text-right">REVENUE</th>
                                <th class="py-3 px-5 text-right">GROSS PROFIT</th>
                                <th class="py-3 px-5 text-center">STATUS</th>
                                <th class="py-3 px-5 text-right">ACTION</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-800 font-medium">
                            @forelse($projectList as $p)
                                <tr class="hover:bg-gray-50/70 transition">
                                    <td class="py-3.5 px-5 font-bold text-gray-900">
                                        {{ $p->sales_name ?: ($p->creator ? $p->creator->name : 'N/A') }}
                                    </td>
                                    <td class="py-3.5 px-5 font-mono text-gray-500">
                                        {{ $p->po_number ?: ($p->quotation_number ?: '-') }}
                                    </td>
                                    <td class="py-3.5 px-5 font-bold text-gray-900">
                                        {{ $p->client }}
                                    </td>
                                    <td class="py-3.5 px-5">
                                        <div class="font-bold text-gray-900 max-w-xs truncate">{{ $p->name }}</div>
                                    </td>
                                    <td class="py-3.5 px-5 text-right font-black text-gray-900">
                                        Rp {{ number_format($p->contract_value, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3.5 px-5 text-right font-bold text-emerald-600">
                                        Rp {{ number_format($p->contract_value * 0.25, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3.5 px-5 text-center whitespace-nowrap">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10.5px] font-bold bg-slate-100 text-slate-700">
                                            {{ $p->status }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-5 text-right whitespace-nowrap">
                                        <a href="{{ route('projects.show', $p->id) }}" class="text-red-600 hover:text-red-800 font-bold hover:underline">
                                            Detail &rarr;
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="py-10 text-center text-gray-400 text-xs">
                                        Belum ada data project pada periode ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="p-4 border-t border-gray-100 flex items-center justify-between text-xs text-gray-400">
                    <div>
                        {{ $projectList->firstItem() ?? 0 }}-{{ $projectList->lastItem() ?? 0 }} from {{ $projectList->total() }}
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
        const chartData = @json($monthlyChartData);
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'],
                datasets: [{
                    label: 'Nilai Project (Milyar Rp)',
                    data: chartData,
                    borderColor: '#DC2626',
                    backgroundColor: 'rgba(220, 38, 38, 0.08)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3,
                    pointBackgroundColor: '#DC2626',
                    pointRadius: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#F1F5F9' },
                        ticks: { font: { size: 10 } }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 10 } }
                    }
                }
            }
        });
    });
</script>
@endpush
@endsection
