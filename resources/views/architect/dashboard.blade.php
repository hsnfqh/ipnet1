@extends('layouts.app')

@section('title', 'Dashboard - Solution Architect')

@section('content')
<div class="flex h-screen overflow-hidden" x-data="architectDashboard()">
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto bg-[#FAF9F8]">
        @include('components.topbar', ['title' => 'Dashboard'])
        
        <div class="p-4 sm:p-5 lg:p-[26px] animate-fade-in space-y-6">

            {{-- Year Filter --}}
            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-gray-400">Year</label>
                <form method="GET" action="{{ route('dashboard.architect') }}" id="yearFilterForm">
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

            {{-- 5 Summary Metric Cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                
                {{-- Card 1: Total Nilai Solusi Teknis --}}
                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-[0_1px_3px_rgba(0,0,0,0.03)] flex flex-col justify-between space-y-4">
                    <div class="space-y-3">
                        <div class="w-10 h-10 rounded-xl bg-red-50 border border-red-100 text-[#C81E2C] flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-[11.5px] font-medium text-gray-400">Total nilai solusi teknis</p>
                            <h3 class="text-xl font-extrabold text-gray-900 tracking-tight mt-1">
                                Rp {{ number_format($totalPipelineValue / 1000000000, 1, ',', '.') }} Miliar
                            </h3>
                        </div>
                    </div>
                    <div class="flex items-center justify-between text-xs pt-3 border-t border-gray-50 text-gray-400">
                        <span>Total prospek</span>
                        <span class="font-bold text-gray-700">{{ $totalProjectsCount }} Peluang</span>
                    </div>
                </div>

                {{-- Card 2: Dokumen Desain Selesai --}}
                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-[0_1px_3px_rgba(0,0,0,0.03)] flex flex-col justify-between space-y-4">
                    <div class="space-y-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-600 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-[11.5px] font-medium text-gray-400">Dokumen desain selesai</p>
                            <h3 class="text-xl font-extrabold text-emerald-600 tracking-tight mt-1">
                                {{ $proposalsReadyCount }} Dokumen
                            </h3>
                        </div>
                    </div>
                    <div class="flex items-center justify-between text-xs pt-3 border-t border-gray-50 text-gray-400">
                        <span>Status dokumen</span>
                        <span class="font-bold text-emerald-600">HLD/LLD Valid</span>
                    </div>
                </div>

                {{-- Card 3: Kajian & Sizing Berjalan --}}
                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-[0_1px_3px_rgba(0,0,0,0.03)] flex flex-col justify-between space-y-4">
                    <div class="space-y-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-50 border border-amber-100 text-amber-600 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-[11.5px] font-medium text-gray-400">Kajian & sizing berjalan</p>
                            <h3 class="text-xl font-extrabold text-amber-600 tracking-tight mt-1">
                                {{ $designPendingCount }} Proyek
                            </h3>
                        </div>
                    </div>
                    <div class="flex items-center justify-between text-xs pt-3 border-t border-gray-50 text-gray-400">
                        <span>Antrean kaji teknis</span>
                        <span class="font-bold text-amber-600">Perlu BoQ</span>
                    </div>
                </div>

                {{-- Card 4: Estimasi Beban Kerja --}}
                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-[0_1px_3px_rgba(0,0,0,0.03)] flex flex-col justify-between space-y-4">
                    <div class="space-y-3">
                        <div class="w-10 h-10 rounded-xl bg-purple-50 border border-purple-100 text-purple-600 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-[11.5px] font-medium text-gray-400">Estimasi beban kerja</p>
                            <h3 class="text-xl font-extrabold text-purple-700 tracking-tight mt-1">
                                {{ $totalMandays }} Mandays
                            </h3>
                        </div>
                    </div>
                    <div class="flex items-center justify-between text-xs pt-3 border-t border-gray-50 text-gray-400">
                        <span>Proyek pelaksanaan</span>
                        <span class="font-bold text-purple-700">{{ $activeProjectsCount }} Deliver</span>
                    </div>
                </div>

                {{-- Card 5: Katalog Produk & SKU --}}
                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-[0_1px_3px_rgba(0,0,0,0.03)] flex flex-col justify-between space-y-4">
                    <div class="space-y-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-100 text-blue-600 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-[11.5px] font-medium text-gray-400">Katalog produk & SKU</p>
                            <h3 class="text-xl font-extrabold text-gray-900 tracking-tight mt-1">
                                {{ $partnerVendors->count() }} Principal
                            </h3>
                        </div>
                    </div>
                    <div class="flex items-center justify-between text-xs pt-3 border-t border-gray-50 text-gray-400">
                        <span>Mitra & inventory</span>
                        <span class="font-bold text-blue-600">Terintegrasi</span>
                    </div>
                </div>

            </div>

            {{-- 2 Charts Grid --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
                
                {{-- Chart 1: Tren Nilai Proyek Arsitektur Bulanan --}}
                <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-[0_1px_3px_rgba(0,0,0,0.03)] p-5 space-y-4">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                        <div>
                            <h3 class="text-base font-bold text-gray-900">Tren Perancangan Solusi Arsitektur ({{ $selectedYear }})</h3>
                            <p class="text-xs text-gray-400">Volume akumulasi nilai proyek yang dalam tahap perancangan arsitektur (Juta Rupiah)</p>
                        </div>
                        <span class="text-xs font-semibold px-2.5 py-1 bg-red-50 text-[#C81E2C] rounded-lg">Plan & Design</span>
                    </div>
                    <div class="h-64 w-full">
                        <canvas id="architectMonthlyChart"></canvas>
                    </div>
                </div>

                {{-- Chart 2: Distribusi Domain Solusi Teknologi --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-[0_1px_3px_rgba(0,0,0,0.03)] p-5 space-y-4">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                        <div>
                            <h3 class="text-base font-bold text-gray-900">Domain Arsitektur Solusi</h3>
                            <p class="text-xs text-gray-400">Komposisi portofolio teknologi yang dirancang</p>
                        </div>
                    </div>
                    <div class="h-64 w-full flex items-center justify-center">
                        <canvas id="domainDoughnutChart"></canvas>
                    </div>
                </div>

            </div>

            {{-- Bottom Section: Tabel Desain & Widget Pendukung --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
                
                {{-- Left 2-Col: Daftar Dokumen Desain & Kaji Teknis Terkini --}}
                <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-[0_1px_3px_rgba(0,0,0,0.03)] p-5 space-y-4">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                        <div class="flex items-center gap-2">
                            <div class="w-2.5 h-2.5 rounded-full bg-[#C81E2C]"></div>
                            <h3 class="text-base font-bold text-gray-900">Dokumen Desain Arsitektur & SOW Terkini</h3>
                        </div>
                        <a href="{{ route('presales.proposals.index') }}" class="text-xs font-semibold text-[#C81E2C] hover:underline flex items-center gap-1">
                            <span>Kelola Semua SOW</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-gray-50 text-gray-500 uppercase text-[10.5px] font-bold">
                                <tr>
                                    <th class="py-3 px-3.5 rounded-l-lg">Tender / Proyek</th>
                                    <th class="py-3 px-3.5">Klien</th>
                                    <th class="py-3 px-3.5 text-right">Nilai Kontrak</th>
                                    <th class="py-3 px-3.5 text-center">Status SOW & HLD</th>
                                    <th class="py-3 px-3.5 rounded-r-lg text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 font-medium text-gray-700">
                                @forelse($recentDesignProjects as $rp)
                                    <tr class="hover:bg-gray-50/80 transition-colors">
                                        <td class="py-3.5 px-3.5">
                                            <div class="font-bold text-gray-900">{{ $rp->name }}</div>
                                            <div class="text-[10.5px] text-gray-400 mt-0.5">
                                                Divisi: {{ $rp->division ? $rp->division->name : ($rp->project_type ?: 'Network Architecture') }}
                                            </div>
                                        </td>
                                        <td class="py-3.5 px-3.5">
                                            <div class="font-semibold text-gray-800">{{ $rp->client ?: 'Umum / Prospek' }}</div>
                                        </td>
                                        <td class="py-3.5 px-3.5 text-right whitespace-nowrap font-bold text-gray-900">
                                            Rp {{ number_format($rp->contract_value ?? 0, 0, ',', '.') }}
                                        </td>
                                        <td class="py-3.5 px-3.5 text-center whitespace-nowrap">
                                            @if($rp->proposal_file)
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-lg">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                    HLD/SOW Selesai
                                                </span>
                                            @else
                                                <span class="inline-flex px-2.5 py-1 text-[11px] font-medium text-amber-700 bg-amber-50 border border-amber-200 rounded-lg">
                                                    Menunggu BoQ & Sizing
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-3.5 px-3.5 text-right whitespace-nowrap">
                                            <div class="flex items-center justify-end gap-1.5">
                                                @if($rp->proposal_file)
                                                    <a href="{{ route('presales.proposals.download', $rp->id) }}" 
                                                       class="px-2.5 py-1 text-[11px] font-semibold text-gray-700 bg-white hover:bg-gray-50 border border-gray-200 rounded-lg transition shadow-2xs">
                                                        SOW
                                                    </a>
                                                @endif
                                                <a href="{{ route('projects.show', $rp->id) }}" 
                                                   class="px-3 py-1 bg-white hover:bg-gray-50 text-gray-700 text-[11px] font-semibold rounded-lg border border-gray-200 shadow-2xs transition">
                                                    Detail
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-10 text-center text-gray-400">
                                            Belum ada data proyek desain arsitektur yang terdaftar.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Right 1-Col: Widgets PoC, Principal & Inventory Sizing --}}
                <div class="space-y-5">
                    
                    {{-- Widget 1: Jadwal PoC & Uji Konsep --}}
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-[0_1px_3px_rgba(0,0,0,0.03)] p-5 space-y-3.5">
                        <div class="flex items-center justify-between border-b border-gray-100 pb-2.5">
                            <h4 class="text-xs font-bold text-gray-900 uppercase tracking-wider">Jadwal PoC & Uji Coba</h4>
                            <span class="text-[11px] font-semibold px-2 py-0.5 bg-blue-50 text-blue-700 rounded-md">Uji Lab</span>
                        </div>
                        <div class="space-y-2.5">
                            @forelse($pocSchedules as $poc)
                                <div class="p-2.5 rounded-xl bg-gray-50/70 border border-gray-100 flex items-start gap-2.5">
                                    <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0 text-xs font-bold">
                                        {{ \Carbon\Carbon::parse($poc->date)->format('d') }}
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="text-xs font-bold text-gray-900 truncate">{{ $poc->title }}</div>
                                        <div class="text-[11px] text-gray-500">{{ $poc->project ? $poc->project->name : 'Sesi Uji Konsep' }}</div>
                                        <div class="text-[10.5px] text-gray-400 mt-0.5">{{ \Carbon\Carbon::parse($poc->date)->format('d M Y') }} • {{ substr($poc->start_time, 0, 5) }} WIB</div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-4 text-xs text-gray-400">Tidak ada jadwal PoC terdekat</div>
                            @endforelse
                        </div>
                    </div>

                    {{-- Widget 2: Partner Vendor & Principal Terhubung --}}
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-[0_1px_3px_rgba(0,0,0,0.03)] p-5 space-y-3.5">
                        <div class="flex items-center justify-between border-b border-gray-100 pb-2.5">
                            <h4 class="text-xs font-bold text-gray-900 uppercase tracking-wider">Principal & Distributor</h4>
                            <a href="{{ route('vendors.index') }}" class="text-[11px] font-semibold text-[#C81E2C] hover:underline">Semua</a>
                        </div>
                        <div class="space-y-2">
                            @foreach($partnerVendors as $v)
                                <div class="flex items-center justify-between p-2 rounded-xl hover:bg-gray-50 transition border border-transparent hover:border-gray-100">
                                    <div class="min-w-0">
                                        <div class="text-xs font-bold text-gray-900 truncate">{{ $v->name }}</div>
                                        <div class="text-[11px] text-gray-400">{{ $v->channel_manager ?: ($v->department ?: 'Principal Partner') }}</div>
                                    </div>
                                    <span class="text-[10.5px] font-semibold px-2 py-0.5 bg-gray-100 text-gray-600 rounded-md">Active</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Widget 3: Referensi Hardware SKU (Inventory) --}}
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-[0_1px_3px_rgba(0,0,0,0.03)] p-5 space-y-3.5">
                        <div class="flex items-center justify-between border-b border-gray-100 pb-2.5">
                            <h4 class="text-xs font-bold text-gray-900 uppercase tracking-wider">Hardware SKU Reference</h4>
                            <a href="{{ route('inventory.index') }}" class="text-[11px] font-semibold text-[#C81E2C] hover:underline">Inventory</a>
                        </div>
                        <div class="space-y-2">
                            @foreach($inventoryHighlights as $inv)
                                <div class="flex items-center justify-between text-xs">
                                    <div class="min-w-0 pr-2">
                                        <div class="font-semibold text-gray-800 truncate">{{ $inv->product_name }}</div>
                                        <div class="text-[10px] text-gray-400 font-mono">{{ $inv->product_code }}</div>
                                    </div>
                                    <span class="text-[11px] font-bold text-gray-700 whitespace-nowrap">{{ $inv->stock }} Unit</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function architectDashboard() {
        return {};
    }

    document.addEventListener('DOMContentLoaded', function () {
        // Chart 1: Monthly Chart
        const monthlyCtx = document.getElementById('architectMonthlyChart');
        if (monthlyCtx) {
            new Chart(monthlyCtx.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                    datasets: [{
                        label: 'Nilai Solusi (Juta Rp)',
                        data: {!! json_encode($monthlyChartData) !!},
                        backgroundColor: '#C81E2C',
                        hoverBackgroundColor: '#AF1424',
                        borderRadius: 6,
                        maxBarThickness: 32
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'Rp ' + Number(context.raw).toLocaleString('id-ID') + ' Juta';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: '#F3F4F6' },
                            ticks: {
                                font: { size: 10 },
                                callback: function(value) { return 'Rp ' + value + 'M'; }
                            }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 10 } }
                        }
                    }
                }
            });
        }

        // Chart 2: Domain Doughnut Chart
        const domainCtx = document.getElementById('domainDoughnutChart');
        if (domainCtx) {
            new Chart(domainCtx.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($domainLabels) !!},
                    datasets: [{
                        data: {!! json_encode($domainChartData) !!},
                        backgroundColor: ['#C81E2C', '#3B82F6', '#10B981', '#8B5CF6'],
                        borderWidth: 2,
                        borderColor: '#ffffff',
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 10,
                                font: { size: 10 },
                                padding: 12
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endsection
