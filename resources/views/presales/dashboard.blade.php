@extends('layouts.app')

@section('title', 'Dashboard - Presales Portal')

@section('content')
<div class="flex h-screen overflow-hidden">
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto bg-[#FAF9F8]">
        @include('components.topbar', ['title' => 'Dashboard'])
        
        <div class="p-4 sm:p-5 lg:p-[26px] animate-fade-in space-y-6">

            {{-- Year Filter --}}
            <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-gray-400">Year</label>
                <form method="GET" action="{{ route('dashboard.presales') }}" id="yearFilterForm">
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

            {{-- 4 Summary Metric Cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                
                {{-- Card 1: Request Proposal Masuk --}}
                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-[0_1px_3px_rgba(0,0,0,0.03)] flex flex-col justify-between space-y-4">
                    <div class="space-y-3">
                        <div class="w-10 h-10 rounded-xl bg-[#FDF1F2] border border-[#FADADF] text-[#C81E2C] flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-[11.5px] font-medium text-gray-400">Request proposal masuk</p>
                            <h3 class="text-xl font-extrabold text-gray-900 tracking-tight mt-1">
                                {{ $totalProposalNeeded }} Dokumen
                            </h3>
                        </div>
                    </div>
                    <div class="flex items-center justify-between text-xs pt-3 border-t border-gray-50 text-gray-400">
                        <span>Nilai prospek</span>
                        <span class="font-bold text-gray-900">Rp {{ number_format($totalPipelineValue / 1000000, 1, ',', '.') }} Jt</span>
                    </div>
                </div>

                {{-- Card 2: Proposal & BoQ Siap --}}
                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-[0_1px_3px_rgba(0,0,0,0.03)] flex flex-col justify-between space-y-4">
                    <div class="space-y-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-100 text-blue-600 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-[11.5px] font-medium text-gray-400">Proposal & BoQ siap</p>
                            <h3 class="text-xl font-extrabold text-blue-600 tracking-tight mt-1">
                                {{ $proposalsReadyCount }} Dokumen
                            </h3>
                        </div>
                    </div>
                    <div class="flex items-center justify-between text-xs pt-3 border-t border-gray-50 text-gray-400">
                        <span>Status dokumen</span>
                        <span class="font-bold text-blue-600">Siap Tender</span>
                    </div>
                </div>

                {{-- Card 3: Tender Menang (Won) --}}
                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-[0_1px_3px_rgba(0,0,0,0.03)] flex flex-col justify-between space-y-4">
                    <div class="space-y-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-600 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-[11.5px] font-medium text-gray-400">Tender menang (Won)</p>
                            <h3 class="text-xl font-extrabold text-emerald-600 tracking-tight mt-1">
                                {{ $wonCount }} Proyek
                            </h3>
                        </div>
                    </div>
                    <div class="flex items-center justify-between text-xs pt-3 border-t border-gray-50 text-gray-400">
                        <span>Total kontrak</span>
                        <span class="font-bold text-emerald-700">Rp {{ number_format($totalWonValue / 1000000, 1, ',', '.') }} Jt</span>
                    </div>
                </div>

                {{-- Card 4: Agenda POC & Demo --}}
                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-[0_1px_3px_rgba(0,0,0,0.03)] flex flex-col justify-between space-y-4">
                    <div class="space-y-3">
                        <div class="w-10 h-10 rounded-xl bg-purple-50 border border-purple-100 text-purple-600 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-[11.5px] font-medium text-gray-400">Agenda POC & demo</p>
                            <h3 class="text-xl font-extrabold text-purple-700 tracking-tight mt-1">
                                {{ $pocSchedules->count() }} Agenda
                            </h3>
                        </div>
                    </div>
                    <div class="flex items-center justify-between text-xs pt-3 border-t border-gray-50 text-gray-400">
                        <span>Uji coba teknis</span>
                        <span class="font-bold text-purple-700">Aktif</span>
                    </div>
                </div>

            </div>

            {{-- 2 Charts Section --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
                
                {{-- Left Chart (5 Cols): Distribusi Pipeline Teknis --}}
                <div class="lg:col-span-5 bg-white p-5 sm:p-6 rounded-2xl border border-gray-100 shadow-[0_1px_3px_rgba(0,0,0,0.03)] flex flex-col justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-gray-900">Distribusi Pipeline Teknis</h3>
                        <p class="text-xs text-gray-400">Komposisi nilai tender berdasarkan status</p>
                    </div>

                    <div class="relative my-4 flex items-center justify-center" style="height: 230px;">
                        <canvas id="presalesDistributionChart"></canvas>
                    </div>

                    <div class="grid grid-cols-1 gap-2 text-xs pt-2 border-t border-gray-50 text-gray-500">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-1.5">
                                <span class="w-2.5 h-2.5 rounded-full bg-[#AF1424]"></span>
                                <span>Request Masuk</span>
                            </div>
                            <span class="font-bold text-gray-800">Rp {{ number_format($totalPipelineValue / 1000000, 1, ',', '.') }} Jt</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-1.5">
                                <span class="w-2.5 h-2.5 rounded-full bg-[#3B82F6]"></span>
                                <span>Proposal & BoQ Siap</span>
                            </div>
                            <span class="font-bold text-gray-800">Rp {{ number_format($totalReviewValue / 1000000, 1, ',', '.') }} Jt</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-1.5">
                                <span class="w-2.5 h-2.5 rounded-full bg-[#10B981]"></span>
                                <span>Deal Won / PMO</span>
                            </div>
                            <span class="font-bold text-gray-800">Rp {{ number_format($totalWonValue / 1000000, 1, ',', '.') }} Jt</span>
                        </div>
                    </div>
                </div>

                {{-- Right Chart (7 Cols): Nilai project per bulan di tahun {year} --}}
                <div class="lg:col-span-7 bg-white p-5 sm:p-6 rounded-2xl border border-gray-100 shadow-[0_1px_3px_rgba(0,0,0,0.03)] space-y-4">
                    <div>
                        <h3 class="text-sm font-bold text-gray-900">Nilai Pipeline Pre-Sales per Bulan di Tahun {{ $selectedYear }}</h3>
                        <p class="text-xs text-gray-400">Akumulasi estimasi nilai proyek tender yang ditangani</p>
                    </div>

                    <div class="w-full relative" style="height: 270px;">
                        <canvas id="presalesMonthlyChart"></canvas>
                    </div>
                </div>

            </div>

            {{-- 2-Column Bottom Section --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
                
                {{-- Left 2-Cols: Permintaan Proposal & SOW Terkini --}}
                <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-[0_1px_3px_rgba(0,0,0,0.03)] p-5 sm:p-6 space-y-4">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3.5">
                        <div>
                            <h3 class="text-sm font-bold text-gray-900">Pipeline Dokumen Proposal Terkini</h3>
                            <p class="text-xs text-gray-400">Daftar tender aktif yang memerlukan kajian teknis & SOW</p>
                        </div>
                        <a href="{{ route('presales.proposals.index') }}" class="text-xs font-bold text-[#C81E2C] hover:underline">
                            Kelola Semua Proposal &rarr;
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="text-[11px] font-bold text-gray-400 uppercase border-b border-gray-100">
                                    <th class="py-2.5 px-3">Nama Tender & Klien</th>
                                    <th class="py-2.5 px-3">Sales PIC</th>
                                    <th class="py-2.5 px-3">Nilai Kontrak</th>
                                    <th class="py-2.5 px-3 text-center">Status</th>
                                    <th class="py-2.5 px-3 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 text-xs font-medium text-gray-700">
                                @forelse($recentRequests as $rp)
                                    <tr class="hover:bg-gray-50/70 transition">
                                        <td class="py-3 px-3">
                                            <div class="font-bold text-gray-900 truncate max-w-xs">{{ $rp->name }}</div>
                                            <div class="text-[11px] text-gray-400 font-medium">{{ $rp->client }}</div>
                                        </td>
                                        <td class="py-3 px-3 whitespace-nowrap">
                                            <span class="inline-flex px-2 py-0.5 text-[10.5px] font-semibold rounded-full bg-red-50 text-[#AF1424]">
                                                {{ $rp->sales_name ?: 'Sales Rep' }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-3 font-semibold text-gray-800 whitespace-nowrap">
                                            {{ $rp->contract_value > 0 ? 'Rp ' . number_format($rp->contract_value / 1000000, 1, ',', '.') . ' Jt' : '—' }}
                                        </td>
                                        <td class="py-3 px-3 text-center whitespace-nowrap">
                                            @if($rp->proposal_file)
                                                <span class="inline-flex px-2.5 py-1 text-[10.5px] font-bold rounded-lg border bg-emerald-50 text-emerald-700 border-emerald-200">
                                                    Proposal Ready
                                                </span>
                                            @else
                                                <span class="inline-flex px-2.5 py-1 text-[10.5px] font-bold rounded-lg border bg-amber-50 text-amber-700 border-amber-200">
                                                    Perlu SOW
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-3 text-right whitespace-nowrap">
                                            <div class="flex items-center justify-end gap-1.5">
                                                @if($rp->proposal_file)
                                                    <a href="{{ route('presales.proposals.download', $rp->id) }}" 
                                                       class="px-2.5 py-1 text-[11px] font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 rounded-lg transition">
                                                        Unduh SOW
                                                    </a>
                                                @endif
                                                <a href="{{ route('presales.proposals.index') }}" 
                                                   class="px-2.5 py-1 text-[11px] font-semibold text-gray-700 bg-white hover:bg-gray-50 border border-gray-200 rounded-lg transition shadow-2xs">
                                                    Update
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-6 text-center text-gray-400 text-xs">
                                            Tidak ada data tender aktif saat ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Right 1-Col: Agenda POC & Demo Terdekat --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-[0_1px_3px_rgba(0,0,0,0.03)] p-5 sm:p-6 space-y-4">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3.5">
                        <div>
                            <h3 class="text-sm font-bold text-gray-900">Agenda POC & Demo</h3>
                            <p class="text-xs text-gray-400">Jadwal uji coba teknis terdekat</p>
                        </div>
                    </div>

                    <div class="space-y-3">
                        @forelse($pocSchedules as $poc)
                            <div class="p-3 rounded-xl bg-gray-50/70 border border-gray-100 flex items-start gap-3">
                                <div class="w-8 h-8 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center flex-shrink-0 text-xs font-bold">
                                    {{ \Carbon\Carbon::parse($poc->date)->format('d') }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="text-xs font-bold text-gray-900 truncate">{{ $poc->title }}</div>
                                    <div class="text-[11px] text-gray-500 mt-0.5 truncate">{{ $poc->project ? $poc->project->name : 'Sesi Uji Konsep' }}</div>
                                    <div class="text-[10.5px] text-gray-400 mt-1 flex items-center gap-1.5">
                                        <span>{{ \Carbon\Carbon::parse($poc->date)->format('d M Y') }}</span>
                                        <span>•</span>
                                        <span class="font-semibold text-gray-600">{{ $poc->engineer ? $poc->engineer->name : 'Presales PIC' }}</span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-6 text-xs text-gray-400">
                                Tidak ada jadwal POC dalam waktu dekat.
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // 1. Doughnut Chart: Distribusi Pipeline Teknis
        const distCtx = document.getElementById('presalesDistributionChart');
        if (distCtx) {
            new Chart(distCtx.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: ['Request Masuk', 'Proposal Siap / Tender', 'Deal Won / PMO'],
                    datasets: [{
                        data: {!! json_encode($distributionData) !!},
                        backgroundColor: ['#AF1424', '#3B82F6', '#10B981'],
                        borderWidth: 2,
                        borderColor: '#ffffff',
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '72%',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.label + ': Rp ' + context.raw + ' Jt';
                                }
                            }
                        }
                    }
                }
            });
        }

        // 2. Bar Chart: Nilai Pipeline per Bulan
        const monthlyCtx = document.getElementById('presalesMonthlyChart');
        if (monthlyCtx) {
            new Chart(monthlyCtx.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                    datasets: [{
                        label: 'Nilai Pipeline (Juta Rp)',
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
    });
</script>
@endsection
