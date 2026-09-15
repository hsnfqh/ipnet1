@extends('layouts.app')

@section('title', 'Dashboard - BDM Portal')

@section('content')
<div class="flex h-screen overflow-hidden">
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto bg-[#FAF9F8]">
        @include('components.topbar', ['title' => 'Dashboard'])
        
        <div class="p-4 sm:p-5 lg:p-[26px] animate-fade-in space-y-6">

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 font-bold px-2">✕</button>
                </div>
            @endif

            {{-- Year Filter --}}
            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-gray-400">Year</label>
                <form method="GET" action="{{ route('dashboard.bdm') }}" id="yearFilterForm">
                    <div class="relative inline-block w-40">
                        <select name="year" onchange="document.getElementById('yearFilterForm').submit()" 
                                class="w-full appearance-none bg-white border border-gray-200 rounded-xl px-3.5 py-2 text-sm font-semibold text-gray-800 shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 cursor-pointer">
                            @foreach([2024, 2025, 2026, 2027] as $y)
                                <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Summary Title --}}
            <div>
                <h2 class="text-sm font-semibold text-gray-400">Summary</h2>
            </div>

            {{-- 5 Summary Cards Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                
                {{-- Card 1: Total Nilai Pipeline --}}
                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-[0_1px_3px_rgba(0,0,0,0.03)] flex flex-col justify-between space-y-4">
                    <div class="space-y-3">
                        <div class="w-10 h-10 rounded-xl bg-[#FDF1F2] border border-[#FADADF] text-[#C81E2C] flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-[11.5px] font-medium text-gray-400">Total nilai pipeline</p>
                            <h3 class="text-xl font-extrabold text-gray-900 tracking-tight mt-1">
                                Rp {{ $totalNilaiProject > 0 ? number_format($totalNilaiProject / 1000000, 1, ',', '.') . 'Jt' : '0,0Jt' }}
                            </h3>
                        </div>
                    </div>
                    <div class="flex items-center justify-between text-xs pt-3 border-t border-gray-50 text-gray-400">
                        <span>Total proyek</span>
                        <span class="font-bold text-gray-900">{{ $totalProjectCount }}</span>
                    </div>
                </div>

                {{-- Card 2: Peluang Baru (Opportunity) --}}
                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-[0_1px_3px_rgba(0,0,0,0.03)] flex flex-col justify-between space-y-4">
                    <div class="space-y-3">
                        <div class="w-10 h-10 rounded-xl bg-[#FDF1F2] border border-[#FADADF] text-[#C81E2C] flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-[11.5px] font-medium text-gray-400">Peluang baru (TOR/RFP)</p>
                            <h3 class="text-xl font-extrabold text-gray-900 tracking-tight mt-1">
                                Rp {{ $totalNilaiOpportunity > 0 ? number_format($totalNilaiOpportunity / 1000000, 1, ',', '.') . 'Jt' : '0,0Jt' }}
                            </h3>
                        </div>
                    </div>
                    <div class="flex items-center justify-between text-xs pt-3 border-t border-gray-50 text-gray-400">
                        <span>Total prospek</span>
                        <span class="font-bold text-gray-900">{{ $totalOpportunityCount }}</span>
                    </div>
                </div>

                {{-- Card 3: Diserahkan ke Sales --}}
                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-[0_1px_3px_rgba(0,0,0,0.03)] flex flex-col justify-between space-y-4">
                    <div class="space-y-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-100 text-blue-600 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-[11.5px] font-medium text-gray-400">Diserahkan ke Sales</p>
                            <h3 class="text-xl font-extrabold text-blue-700 tracking-tight mt-1">
                                {{ $totalHandoverCount }} Peluang
                            </h3>
                        </div>
                    </div>
                    <div class="flex items-center justify-between text-xs pt-3 border-t border-gray-50 text-gray-400">
                        <span>Konversi BD &rarr; Sales</span>
                        <span class="font-bold text-blue-600">{{ $conversionRate }}%</span>
                    </div>
                </div>

                {{-- Card 4: Market Intelligence --}}
                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-[0_1px_3px_rgba(0,0,0,0.03)] flex flex-col justify-between space-y-4">
                    <div class="space-y-3">
                        <div class="w-10 h-10 rounded-xl bg-purple-50 border border-purple-100 text-purple-600 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-[11.5px] font-medium text-gray-400">Market Intelligence</p>
                            <h3 class="text-xl font-extrabold text-gray-900 tracking-tight mt-1">
                                {{ $intelCount }} Laporan
                            </h3>
                        </div>
                    </div>
                    <div class="flex items-center justify-between text-xs pt-3 border-t border-gray-50 text-gray-400">
                        <span>Database riset</span>
                        <a href="{{ route('bdm.intelligence.index') }}" class="font-bold text-purple-600 hover:underline">Lihat Semua &rarr;</a>
                    </div>
                </div>

                {{-- Card 5: Jaringan Mitra & Prinsipal --}}
                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-[0_1px_3px_rgba(0,0,0,0.03)] flex flex-col justify-between space-y-4">
                    <div class="space-y-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-600 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-[11.5px] font-medium text-gray-400">Jaringan Vendor & Mitra</p>
                            <h3 class="text-xl font-extrabold text-gray-900 tracking-tight mt-1">
                                {{ $partnerCount }} Prinsipal
                            </h3>
                        </div>
                    </div>
                    <div class="flex items-center justify-between text-xs pt-3 border-t border-gray-50 text-gray-400">
                        <span>Katalog mitra</span>
                        <a href="{{ route('bdm.partnerships.index') }}" class="font-bold text-emerald-600 hover:underline">Lihat Semua &rarr;</a>
                    </div>
                </div>

            </div>

            {{-- 2 Charts Grid --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
                
                {{-- Chart 1: Tren Nilai Pipeline Bulanan --}}
                <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-[0_1px_3px_rgba(0,0,0,0.03)] p-5 space-y-4">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                        <div>
                            <h3 class="text-base font-bold text-gray-900">Tren Pipeline Nilai Peluang ({{ $selectedYear }})</h3>
                            <p class="text-xs text-gray-400">Akumulasi estimasi nilai kontrak tender per bulan (Juta Rupiah)</p>
                        </div>
                        <span class="text-xs font-semibold px-2.5 py-1 bg-red-50 text-[#C81E2C] rounded-lg">Tahap Acquire</span>
                    </div>
                    <div class="h-64 w-full">
                        <canvas id="monthlyTrendChart"></canvas>
                    </div>
                </div>

                {{-- Chart 2: Distribusi Peluang per Sektor Klien --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-[0_1px_3px_rgba(0,0,0,0.03)] p-5 space-y-4">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                        <div>
                            <h3 class="text-base font-bold text-gray-900">Sektor Industri Klien</h3>
                            <p class="text-xs text-gray-400">Penyebaran portofolio target pasar</p>
                        </div>
                    </div>
                    <div class="h-64 w-full flex items-center justify-center">
                        <canvas id="sectorDoughnutChart"></canvas>
                    </div>
                </div>

            </div>

            {{-- Bottom Section: Peluang Tender Terbaru & Direktori Mitra --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
                
                {{-- Left 2-Col: Daftar Peluang Tender Terbaru --}}
                <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-[0_1px_3px_rgba(0,0,0,0.03)] p-5 space-y-4">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                        <div class="flex items-center gap-2">
                            <div class="w-2.5 h-2.5 rounded-full bg-[#C81E2C]"></div>
                            <h3 class="text-base font-bold text-gray-900">Peluang & Tender Prospek Terkini</h3>
                        </div>
                        <a href="{{ route('bdm.opportunities.index') }}" class="text-xs font-semibold text-[#C81E2C] hover:underline flex items-center gap-1">
                            <span>Buka Menu Peluang</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-gray-50 text-gray-500 uppercase text-[10.5px] font-bold">
                                <tr>
                                    <th class="py-2.5 px-3 rounded-l-lg">Peluang / Tender</th>
                                    <th class="py-2.5 px-3">Klien & Sektor</th>
                                    <th class="py-2.5 px-3 text-right">Nilai Kontrak</th>
                                    <th class="py-2.5 px-3 text-center">Status Handover</th>
                                    <th class="py-2.5 px-3 rounded-r-lg text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 font-medium text-gray-700">
                                @forelse($recentOpportunities as $rp)
                                    <tr class="hover:bg-gray-50/80 transition-colors">
                                        <td class="py-3 px-3">
                                            <div class="font-bold text-gray-900">{{ $rp->name }}</div>
                                            <div class="text-[10.5px] text-gray-400">Target: {{ $rp->target_timeline_type ?: 'Q3 2026' }}</div>
                                        </td>
                                        <td class="py-3 px-3">
                                            <div class="font-semibold text-gray-800">{{ $rp->client ?: 'Umum / Prospek' }}</div>
                                            <div class="text-[10.5px] text-gray-400">{{ $rp->opportunity_source ?: 'Direct Inbound' }}</div>
                                        </td>
                                        <td class="py-3 px-3 text-right whitespace-nowrap font-bold text-gray-900">
                                            Rp {{ number_format($rp->contract_value ?? 0, 0, ',', '.') }}
                                        </td>
                                        <td class="py-3 px-3 text-center whitespace-nowrap">
                                            @if($rp->bdm_handover_status === 'Handed Over to Sales')
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[10.5px] font-bold text-blue-700 bg-blue-50 border border-blue-200 rounded-lg">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                                    Diserahkan: {{ $rp->sales_name ?: 'Sales' }}
                                                </span>
                                            @elseif($rp->bdm_handover_status === 'Accepted by Sales')
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[10.5px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-lg">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                    Diterima Sales
                                                </span>
                                            @else
                                                <span class="inline-flex px-2 py-0.5 text-[10.5px] font-medium text-amber-700 bg-amber-50 border border-amber-200 rounded-lg">
                                                    Draft Inisiasi
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-3 text-right whitespace-nowrap">
                                            <a href="{{ route('projects.show', $rp->id) }}" 
                                               class="px-2.5 py-1 bg-white hover:bg-gray-50 text-gray-700 text-[11px] font-semibold rounded-lg border border-gray-200 shadow-sm transition">
                                                Detail
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-8 text-center text-gray-400 text-xs">Belum ada peluang tender tercatat.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Right 1-Col: Market Intel & Direktori Mitra --}}
                <div class="space-y-5">
                    
                    {{-- Market Intelligence Terbaru --}}
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-[0_1px_3px_rgba(0,0,0,0.03)] p-5 space-y-3">
                        <div class="flex items-center justify-between border-b border-gray-100 pb-2.5">
                            <div class="flex items-center gap-2">
                                <div class="w-2 h-2 rounded-full bg-purple-500"></div>
                                <h3 class="text-sm font-bold text-gray-900">Market Intelligence Terbaru</h3>
                            </div>
                            <a href="{{ route('bdm.intelligence.index') }}" class="text-[11px] font-bold text-purple-600 hover:underline">Kelola</a>
                        </div>

                        <div class="divide-y divide-gray-50">
                            @forelse($recentIntels as $intel)
                                <div class="py-2.5 space-y-1">
                                    <div class="font-bold text-gray-900 text-xs truncate">{{ $intel->title }}</div>
                                    <div class="text-[11px] text-gray-400 flex items-center justify-between">
                                        <span>{{ $intel->industry_sector }}</span>
                                        <span class="font-semibold text-purple-600">{{ $intel->impact_level }} Impact</span>
                                    </div>
                                </div>
                            @empty
                                <div class="py-4 text-center text-gray-400 text-xs">Belum ada data market intelligence.</div>
                            @endforelse
                        </div>
                    </div>

                    {{-- Mitra Vendor & Prinsipal --}}
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-[0_1px_3px_rgba(0,0,0,0.03)] p-5 space-y-3">
                        <div class="flex items-center justify-between border-b border-gray-100 pb-2.5">
                            <div class="flex items-center gap-2">
                                <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                                <h3 class="text-sm font-bold text-gray-900">Mitra Vendor & Prinsipal</h3>
                            </div>
                            <a href="{{ route('bdm.partnerships.index') }}" class="text-[11px] font-bold text-emerald-600 hover:underline">Kelola</a>
                        </div>

                        <div class="divide-y divide-gray-50">
                            @forelse($recentPartners as $partner)
                                <div class="py-2 flex items-center justify-between text-xs">
                                    <div>
                                        <div class="font-bold text-gray-900">{{ $partner->partner_name }}</div>
                                        <div class="text-[10.5px] text-gray-400">{{ $partner->partner_type }}</div>
                                    </div>
                                    <span class="text-[10.5px] font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-100">
                                        {{ $partner->tier_level }}
                                    </span>
                                </div>
                            @empty
                                <div class="py-3 text-center text-gray-400 text-xs">Belum ada vendor terdaftar.</div>
                            @endforelse
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // 1. Chart Tren Nilai Pipeline Bulanan
        const monthlyData = @json($monthlyDataInMillions);
        const ctxTrend = document.getElementById('monthlyTrendChart').getContext('2d');
        
        const gradient = ctxTrend.createLinearGradient(0, 0, 0, 240);
        gradient.addColorStop(0, 'rgba(200, 30, 44, 0.25)');
        gradient.addColorStop(1, 'rgba(200, 30, 44, 0.00)');

        new Chart(ctxTrend, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                datasets: [{
                    label: 'Nilai Pipeline (Juta Rp)',
                    data: monthlyData,
                    borderColor: '#C81E2C',
                    backgroundColor: gradient,
                    borderWidth: 2.5,
                    pointBackgroundColor: '#C81E2C',
                    pointBorderColor: '#FFFFFF',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.35
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(ctx) {
                                return ' Rp ' + ctx.parsed.y.toLocaleString('id-ID') + ' Juta';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11, family: "'Inter', sans-serif" }, color: '#9CA3AF' }
                    },
                    y: {
                        grid: { color: '#F3F4F6' },
                        ticks: {
                            font: { size: 10.5, family: "'Inter', sans-serif" },
                            color: '#9CA3AF',
                            callback: function(val) { return 'Rp ' + val + 'Jt'; }
                        }
                    }
                }
            }
        });

        // 2. Chart Distribusi Sektor Klien
        const sectorData = @json($sectorDataInMillions);
        const sectorLabels = @json(array_keys($sectorCounts));
        const ctxSector = document.getElementById('sectorDoughnutChart').getContext('2d');

        new Chart(ctxSector, {
            type: 'doughnut',
            data: {
                labels: sectorLabels,
                datasets: [{
                    data: sectorData,
                    backgroundColor: [
                        '#C81E2C', // Primary Red
                        '#2563EB', // Blue
                        '#10B981', // Emerald
                        '#F59E0B'  // Amber
                    ],
                    borderWidth: 2,
                    borderColor: '#FFFFFF',
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 10,
                            font: { size: 10.5, family: "'Inter', sans-serif" },
                            color: '#4B5563',
                            padding: 12
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(ctx) {
                                return ' ' + ctx.label + ': Rp ' + ctx.parsed.toLocaleString('id-ID') + ' Jt';
                            }
                        }
                    }
                },
                cutout: '68%'
            }
        });
    });
</script>
@endpush
