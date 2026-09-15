@extends('layouts.app')

@section('title', 'Dashboard - Sales Portal')

@section('content')
<div class="flex h-screen overflow-hidden" x-data="salesDashboard()">
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
                <form method="GET" action="{{ route('dashboard.sales') }}" id="yearFilterForm">
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
                                Rp {{ $totalPipelineValue > 0 ? number_format($totalPipelineValue / 1000000, 1, ',', '.') . 'Jt' : '0,0Jt' }}
                            </h3>
                        </div>
                    </div>
                    <div class="flex items-center justify-between text-xs pt-3 border-t border-gray-50 text-gray-400">
                        <span>Peluang aktif</span>
                        <span class="font-bold text-gray-900">{{ $totalPipelineCount }}</span>
                    </div>
                </div>

                {{-- Card 2: Weighted Forecast --}}
                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-[0_1px_3px_rgba(0,0,0,0.03)] flex flex-col justify-between space-y-4">
                    <div class="space-y-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-50 border border-amber-100 text-amber-600 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-[11.5px] font-medium text-gray-400">Weighted forecast</p>
                            <h3 class="text-xl font-extrabold text-amber-900 tracking-tight mt-1">
                                Rp {{ $totalWeightedForecast > 0 ? number_format($totalWeightedForecast / 1000000, 1, ',', '.') . 'Jt' : '0,0Jt' }}
                            </h3>
                        </div>
                    </div>
                    <div class="flex items-center justify-between text-xs pt-3 border-t border-gray-50 text-gray-400">
                        <span>Bobot estimasi</span>
                        <span class="font-bold text-amber-700">Probabilitas %</span>
                    </div>
                </div>

                {{-- Card 3: Closing Horizon --}}
                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-[0_1px_3px_rgba(0,0,0,0.03)] flex flex-col justify-between space-y-4">
                    <div class="space-y-3">
                        <div class="w-10 h-10 rounded-xl bg-purple-50 border border-purple-100 text-purple-600 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-[11.5px] font-medium text-gray-400">Closing horizon</p>
                            <h3 class="text-xl font-extrabold text-gray-900 tracking-tight mt-1">
                                Rp {{ $totalNegotiationValue > 0 ? number_format($totalNegotiationValue / 1000000, 1, ',', '.') . 'Jt' : '0,0Jt' }}
                            </h3>
                        </div>
                    </div>
                    <div class="flex items-center justify-between text-xs pt-3 border-t border-gray-50 text-gray-400">
                        <span>Negosiasi / SPK</span>
                        <span class="font-bold text-purple-600">{{ $totalNegotiationCount }} Prospek</span>
                    </div>
                </div>

                {{-- Card 4: Total Nilai Complete / Won --}}
                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-[0_1px_3px_rgba(0,0,0,0.03)] flex flex-col justify-between space-y-4">
                    <div class="space-y-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-600 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-[11.5px] font-medium text-gray-400">Total nilai won (YTD)</p>
                            <h3 class="text-xl font-extrabold text-emerald-700 tracking-tight mt-1">
                                Rp {{ $totalWonValue > 0 ? number_format($totalWonValue / 1000000, 1, ',', '.') . 'Jt' : '0,0Jt' }}
                            </h3>
                        </div>
                    </div>
                    <div class="flex items-center justify-between text-xs pt-3 border-t border-gray-50 text-gray-400">
                        <span>Total deal won</span>
                        <span class="font-bold text-emerald-700">{{ $totalWonCount }}</span>
                    </div>
                </div>

                {{-- Card 5: Win Rate & Aktivitas --}}
                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-[0_1px_3px_rgba(0,0,0,0.03)] flex flex-col justify-between space-y-4">
                    <div class="space-y-3">
                        <div class="w-10 h-10 rounded-xl bg-rose-50 border border-rose-100 text-rose-600 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-[11.5px] font-medium text-gray-400">Win rate penjualan</p>
                            <h3 class="text-xl font-extrabold text-gray-900 tracking-tight mt-1">
                                {{ $winRate }}%
                            </h3>
                        </div>
                    </div>
                    <div class="flex items-center justify-between text-xs pt-3 border-t border-gray-50 text-gray-400">
                        <span>Aktivitas bulan ini</span>
                        <span class="font-bold text-gray-900">{{ $crmActivitiesCount }} Log</span>
                    </div>
                </div>

            </div>

            {{-- Sales Pipeline Section --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-[0_1px_3px_rgba(0,0,0,0.03)] p-5 space-y-4">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <div class="flex items-center gap-2">
                        <div class="w-2.5 h-2.5 rounded-full bg-[#C81E2C]"></div>
                        <div>
                            <h3 class="text-base font-bold text-gray-900">Pipeline Penjualan</h3>
                            <p class="text-xs text-gray-400">Distribusi peluang aktif berdasarkan tahapan progres dan estimasi nilai transaksi</p>
                        </div>
                    </div>
                    <a href="{{ route('sales.pipeline.index') }}" class="text-xs font-semibold text-[#C81E2C] hover:underline flex items-center gap-1">
                        <span>Lihat Semua Peluang</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-8 gap-3">
                    @foreach($stageFunnel as $key => $stage)
                        @if($key !== 'Closed Lost')
                            <div class="p-3 rounded-xl border border-gray-100 bg-[#FAF9F8] flex flex-col justify-between space-y-2 hover:border-[#C81E2C]/30 hover:shadow-xs transition">
                                <div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded" style="background-color: {{ $stage['bg'] }}; color: {{ $stage['color'] }};">
                                            {{ $stage['default_prob'] }}%
                                        </span>
                                        <span class="text-xs font-bold text-gray-900">{{ $stage['count'] }}</span>
                                    </div>
                                    <div class="text-[11.5px] font-bold text-gray-900 mt-1.5 truncate" title="{{ $stage['label'] }}">
                                        {{ $stage['label'] }}
                                    </div>
                                </div>
                                <div class="border-t border-gray-200/60 pt-1.5">
                                    <div class="text-[11px] font-extrabold text-gray-900">
                                        Rp {{ $stage['value'] > 0 ? number_format($stage['value'] / 1000000, 1, ',', '.') . 'Jt' : '0' }}
                                    </div>
                                    <div class="text-[9.5px] text-gray-400">
                                        Wtd: Rp {{ $stage['weighted_value'] > 0 ? number_format($stage['weighted_value'] / 1000000, 1, ',', '.') . 'Jt' : '0' }}
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            {{-- 2 Charts Grid --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
                
                {{-- Chart 1: Tren Nilai Penjualan Bulanan (Left 2-Col) --}}
                <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-[0_1px_3px_rgba(0,0,0,0.03)] p-5 space-y-4">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                        <div>
                            <h3 class="text-base font-bold text-gray-900">Tren Penjualan & Forecast Bulanan ({{ $selectedYear }})</h3>
                            <p class="text-xs text-gray-400">Perbandingan nilai estimasi berbobot probabilitas vs deal Closed Won (Juta Rp)</p>
                        </div>
                        <span class="text-xs font-semibold px-2.5 py-1 bg-red-50 text-[#C81E2C] rounded-lg">Commercial CRM</span>
                    </div>
                    <div class="h-64 w-full">
                        <canvas id="monthlyTrendChart"></canvas>
                    </div>
                </div>

                {{-- Chart 2: Komposisi Nilai Deals (Right 1-Col) --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-[0_1px_3px_rgba(0,0,0,0.03)] p-5 space-y-4">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                        <div>
                            <h3 class="text-base font-bold text-gray-900">Komposisi Pipeline Nilai Deals</h3>
                            <p class="text-xs text-gray-400">Porsi nominal proyek per tahapan</p>
                        </div>
                    </div>
                    <div class="h-64 w-full flex items-center justify-center">
                        <canvas id="stageDoughnutChart"></canvas>
                    </div>
                </div>

            </div>

            {{-- Bottom Section: Peluang Prioritas & Aktivitas CRM / Handover --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
                
                {{-- Left 2-Col: Daftar Peluang Prioritas (High-Value Deals) --}}
                <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-[0_1px_3px_rgba(0,0,0,0.03)] p-5 space-y-4">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                        <div class="flex items-center gap-2">
                            <div class="w-2.5 h-2.5 rounded-full bg-[#C81E2C]"></div>
                            <h3 class="text-base font-bold text-gray-900">Peluang & Pipeline Prioritas</h3>
                        </div>
                        <a href="{{ route('sales.pipeline.index') }}" class="text-xs font-semibold text-[#C81E2C] hover:underline flex items-center gap-1">
                            <span>Buka Menu Peluang</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-gray-50 text-gray-500 uppercase text-[10.5px] font-bold">
                                <tr>
                                    <th class="py-2.5 px-3 rounded-l-lg">Peluang / Prospek</th>
                                    @if($isManagerial)
                                        <th class="py-2.5 px-3">Sales PIC</th>
                                    @else
                                        <th class="py-2.5 px-3">BDM Origin</th>
                                    @endif
                                    <th class="py-2.5 px-3 text-right">Nilai Kontrak</th>
                                    <th class="py-2.5 px-3 text-center">Tahap / Prob</th>
                                    <th class="py-2.5 px-3 rounded-r-lg text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 font-medium text-gray-700">
                                @forelse($priorityDeals as $deal)
                                    @php
                                        $stageMeta = $stages[$deal->sales_stage] ?? ['color' => '#6B7280', 'bg' => '#F3F4F6'];
                                    @endphp
                                    <tr class="hover:bg-gray-50/80 transition-colors">
                                        <td class="py-3 px-3">
                                            <div class="font-bold text-gray-900">{{ $deal->name }}</div>
                                            <div class="text-[10.5px] text-gray-400">{{ $deal->client }}</div>
                                        </td>
                                        @if($isManagerial)
                                            <td class="py-3 px-3 whitespace-nowrap">
                                                <span class="font-semibold text-gray-800">{{ $deal->sales_name ?: 'Belum Assign' }}</span>
                                            </td>
                                        @else
                                            <td class="py-3 px-3 whitespace-nowrap">
                                                <span class="font-semibold text-gray-800">{{ $deal->bdm->name ?? ($deal->creator->name ?? 'BDM') }}</span>
                                            </td>
                                        @endif
                                        <td class="py-3 px-3 text-right whitespace-nowrap font-bold text-gray-900">
                                            {{ $deal->contract_value > 0 ? 'Rp ' . number_format($deal->contract_value, 0, ',', '.') : '—' }}
                                        </td>
                                        <td class="py-3 px-3 text-center whitespace-nowrap">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[10.5px] font-bold rounded-lg" 
                                                  style="background-color: {{ $stageMeta['bg'] }}; color: {{ $stageMeta['color'] }};">
                                                <span>{{ $deal->sales_stage }}</span>
                                                <span class="text-[10px] opacity-80">({{ $deal->win_probability ?? 10 }}%)</span>
                                            </span>
                                        </td>
                                        <td class="py-3 px-3 text-right whitespace-nowrap">
                                            <div class="flex items-center justify-end gap-1.5">
                                                <button type="button" 
                                                        @click="openStageModal({{ json_encode($deal) }})"
                                                        class="px-2.5 py-1 bg-white hover:bg-gray-50 text-[#C81E2C] text-[11px] font-semibold rounded-lg border border-red-200 shadow-sm transition">
                                                    Update
                                                </button>
                                                <a href="{{ route('projects.show', $deal->id) }}" 
                                                   class="px-2.5 py-1 bg-white hover:bg-gray-50 text-gray-700 text-[11px] font-semibold rounded-lg border border-gray-200 shadow-sm transition">
                                                    Detail
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-8 text-center text-gray-400 text-xs">Belum ada peluang aktif dalam pipeline.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Right 1-Col: Aktivitas CRM & Commercial Handover --}}
                <div class="space-y-5">
                    
                    {{-- Log Aktivitas CRM Terbaru --}}
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-[0_1px_3px_rgba(0,0,0,0.03)] p-5 space-y-3">
                        <div class="flex items-center justify-between border-b border-gray-100 pb-2.5">
                            <div class="flex items-center gap-2">
                                <div class="w-2 h-2 rounded-full bg-purple-500"></div>
                                <h3 class="text-sm font-bold text-gray-900">Aktivitas CRM Terkini</h3>
                            </div>
                            <a href="{{ route('sales.activities.index') }}" class="text-[11px] font-bold text-purple-600 hover:underline">Kelola</a>
                        </div>

                        <div class="divide-y divide-gray-50">
                            @forelse($recentActivities as $act)
                                <div class="py-2.5 space-y-1">
                                    <div class="flex items-center justify-between text-xs">
                                        <div class="font-bold text-gray-900 truncate max-w-[180px]">{{ $act->subject }}</div>
                                        <span class="text-[10px] text-gray-400">{{ \Carbon\Carbon::parse($act->activity_date)->format('d M') }}</span>
                                    </div>
                                    <div class="text-[11px] text-gray-400 flex items-center justify-between">
                                        <span class="truncate">{{ $act->project->name ?? 'Proyek' }}</span>
                                        <span class="font-semibold text-gray-700">{{ $act->sales->name ?? 'Sales' }}</span>
                                    </div>
                                </div>
                            @empty
                                <div class="py-4 text-center text-gray-400 text-xs">Belum ada aktivitas CRM.</div>
                            @endforelse
                        </div>
                    </div>

                    {{-- Commercial Handover Siap Kirim --}}
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-[0_1px_3px_rgba(0,0,0,0.03)] p-5 space-y-3">
                        <div class="flex items-center justify-between border-b border-gray-100 pb-2.5">
                            <div class="flex items-center gap-2">
                                <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                                <h3 class="text-sm font-bold text-gray-900">Commercial Handover</h3>
                            </div>
                            <a href="{{ route('sales.handover.index') }}" class="text-[11px] font-bold text-emerald-600 hover:underline">Kelola</a>
                        </div>

                        <div class="divide-y divide-gray-50">
                            @php
                                $wonHandoverDeals = $priorityDeals->whereIn('sales_stage', ['Contract / PO / SPK', 'Closed Won', 'Approval'])->take(3);
                            @endphp
                            @forelse($wonHandoverDeals as $wDeal)
                                <div class="py-2 flex items-center justify-between text-xs">
                                    <div class="min-w-0 pr-2">
                                        <div class="font-bold text-gray-900 truncate">{{ $wDeal->name }}</div>
                                        <div class="text-[10.5px] text-gray-400">{{ $wDeal->client }}</div>
                                    </div>
                                    <a href="{{ route('sales.handover.index') }}" class="text-[10.5px] font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-100 flex-shrink-0">
                                        Serah Terima
                                    </a>
                                </div>
                            @empty
                                <div class="py-3 text-center text-gray-400 text-xs">Belum ada deal siap handover.</div>
                            @endforelse
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </div>

    {{-- MODAL QUICK UPDATE STAGE & FORECAST --}}
    <template x-teleport="body">
        <div x-show="isStageModalOpen" 
             x-cloak 
             class="fixed inset-0 z-50 bg-[#0E0D12]/60 flex items-center justify-center p-3 sm:p-5 backdrop-blur-xs">
            <div class="bg-white rounded-2xl w-[600px] sm:w-[680px] max-w-full max-h-[85vh] flex flex-col overflow-hidden shadow-[0_20px_60px_rgba(14,13,18,0.2)] border border-[#E7E5E3] my-auto animate-fade-in-up"
                 @click.away="isStageModalOpen = false">
                
                {{-- Header Modal --}}
                <div class="px-6 py-4 border-b border-[#E7E5E3] flex-shrink-0 bg-white flex items-center justify-between">
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-[#C81E2C]">Sales CRM Lifecycle</span>
                        <h3 class="text-base font-bold font-['Space_Grotesk'] text-[#0E0D12]" x-text="'Update Stage: ' + (selectedDeal.name || '')"></h3>
                    </div>
                    <button type="button" @click="isStageModalOpen = false" class="text-[#736E7D] hover:text-[#0E0D12] p-1.5 rounded-lg hover:bg-[#FAF9F8] transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Form Body --}}
                <form :action="'/sales/pipeline/' + selectedDeal.id + '/stage'" method="POST" enctype="multipart/form-data" class="flex flex-col flex-1 overflow-hidden">
                    @csrf
                    
                    <div class="px-6 py-4 overflow-y-auto flex-1 space-y-4 text-xs">
                        
                        {{-- Ringkasan Peluang --}}
                        <div class="p-3 bg-[#FAF9F8] rounded-xl border border-[#E7E5E3] space-y-1.5">
                            <div class="flex justify-between">
                                <span class="text-[#736E7D]">Klien / Instansi:</span>
                                <span class="font-bold text-[#0E0D12]" x-text="selectedDeal.client || '—'"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-[#736E7D]">Sales Commercial PIC:</span>
                                <span class="font-bold text-[#C81E2C]" x-text="selectedDeal.sales_name || 'Belum Ditugaskan'"></span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <div>
                                <label class="block font-bold text-[#0E0D12] mb-1">Tahapan Siklus Penjualan (Sales Stage) *</label>
                                <select name="sales_stage" x-model="formStage" @change="syncProbability()" required
                                        class="w-full bg-[#FAF9F8] border border-[#E7E5E3] rounded-xl px-3 py-2 text-xs font-semibold text-[#0E0D12] focus:ring-2 focus:ring-[#C81E2C]/20 focus:border-[#C81E2C]">
                                    <option value="Qualification">1. Qualification (10%)</option>
                                    <option value="Qualified Opportunity">2. Qualified Opportunity (25%)</option>
                                    <option value="Proposal Request">3. Proposal Request (50%)</option>
                                    <option value="Quotation">4. Quotation Submitted (70%)</option>
                                    <option value="Negotiation">5. Negotiation (85%)</option>
                                    <option value="Approval">6. Internal Approval (95%)</option>
                                    <option value="Contract / PO / SPK">7. Contract / PO / SPK (98%)</option>
                                    <option value="Closed Won">8. Closed Won (100% Handover)</option>
                                    <option value="Closed Lost">Closed Lost / Drop (0%)</option>
                                </select>
                            </div>

                            <div>
                                <label class="block font-bold text-[#0E0D12] mb-1">Win Probability (%) *</label>
                                <input type="number" name="win_probability" x-model="formProb" min="0" max="100" required
                                       class="w-full bg-[#FAF9F8] border border-[#E7E5E3] rounded-xl px-3 py-2 text-xs font-semibold text-[#0E0D12] focus:ring-2 focus:ring-[#C81E2C]/20 focus:border-[#C81E2C]">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <div>
                                <label class="block font-bold text-[#0E0D12] mb-1">Nilai Kontrak Estimasi (Rp)</label>
                                <input type="number" name="contract_value" x-model="formContractValue" min="0" step="1000"
                                       class="w-full bg-[#FAF9F8] border border-[#E7E5E3] rounded-xl px-3 py-2 text-xs font-semibold text-[#0E0D12] focus:ring-2 focus:ring-[#C81E2C]/20 focus:border-[#C81E2C]">
                            </div>

                            <div>
                                <label class="block font-bold text-[#0E0D12] mb-1">Expected Closing Date</label>
                                <input type="date" name="expected_closing_date" x-model="formClosingDate"
                                       class="w-full bg-[#FAF9F8] border border-[#E7E5E3] rounded-xl px-3 py-2 text-xs font-semibold text-[#0E0D12] focus:ring-2 focus:ring-[#C81E2C]/20 focus:border-[#C81E2C]">
                            </div>
                        </div>

                        {{-- Quotation Details --}}
                        <div class="p-3.5 rounded-xl border border-[#E7E5E3] bg-[#FAF9F8] space-y-3">
                            <span class="text-[11px] font-bold text-[#0E0D12] uppercase tracking-wider block">Dokumen Penawaran Harga (Quotation)</span>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block font-medium text-[#736E7D] mb-1">Nomor Quotation</label>
                                    <input type="text" name="quotation_number" x-model="formQuotationNum" placeholder="Contoh: QUO-IPNET/2026/09/001"
                                           class="w-full bg-white border border-[#E7E5E3] rounded-xl px-3 py-2 text-xs text-[#0E0D12]">
                                </div>
                                <div>
                                    <label class="block font-medium text-[#736E7D] mb-1">Upload File Quotation Resmi</label>
                                    <input type="file" name="quotation_file" accept=".pdf,.docx,.xlsx,.zip"
                                           class="w-full bg-white border border-[#E7E5E3] rounded-xl px-2.5 py-1.5 text-xs text-[#736E7D]">
                                </div>
                            </div>
                        </div>

                        {{-- Lost Reason if Closed Lost --}}
                        <div x-show="formStage === 'Closed Lost'" class="p-3.5 rounded-xl border border-rose-200 bg-rose-50 space-y-3">
                            <span class="text-[11px] font-bold text-rose-800 uppercase tracking-wider block">Alasan Deal Drop / Kalah Tender</span>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block font-medium text-rose-900 mb-1">Kompetitor Pemenang</label>
                                    <input type="text" name="lost_competitor" placeholder="Contoh: PT Integrator Lain"
                                           class="w-full bg-white border border-rose-300 rounded-xl px-3 py-2 text-xs">
                                </div>
                                <div>
                                    <label class="block font-medium text-rose-900 mb-1">Alasan Utama</label>
                                    <input type="text" name="lost_reason" placeholder="Contoh: Harga lebih tinggi, Spek tidak masuk"
                                           class="w-full bg-white border border-rose-300 rounded-xl px-3 py-2 text-xs">
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block font-bold text-[#0E0D12] mb-1">Catatan Progres Negosiasi / Follow-up</label>
                            <textarea name="sales_notes" rows="2" placeholder="Tuliskan ringkasan perkembangan negosiasi dengan klien..."
                                      class="w-full bg-[#FAF9F8] border border-[#E7E5E3] rounded-xl p-3 text-xs text-[#0E0D12] focus:ring-2 focus:ring-[#C81E2C]/20 focus:border-[#C81E2C]"></textarea>
                        </div>

                    </div>

                    {{-- Footer Modal --}}
                    <div class="px-6 py-3.5 border-t border-[#E7E5E3] bg-[#FAF9F8] flex-shrink-0 flex items-center justify-end gap-2.5">
                        <button type="button" @click="isStageModalOpen = false" 
                                class="px-4 py-2 text-xs font-semibold text-[#736E7D] hover:bg-white rounded-xl border border-[#E7E5E3] transition">
                            Batal
                        </button>
                        <button type="submit" 
                                class="px-5 py-2 text-xs font-bold text-white bg-[#C81E2C] hover:bg-[#AF1424] rounded-xl shadow-[0_8px_20px_rgba(200,30,44,0.24)] transition">
                            Simpan Perubahan Stage
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </template>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function salesDashboard() {
        return {
            isStageModalOpen: false,
            selectedDeal: {},
            formStage: 'Qualification',
            formProb: 10,
            formContractValue: 0,
            formClosingDate: '',
            formQuotationNum: '',

            openStageModal(deal) {
                this.selectedDeal = deal;
                this.formStage = deal.sales_stage || 'Qualification';
                this.formProb = deal.win_probability !== null ? deal.win_probability : 10;
                this.formContractValue = deal.contract_value || 0;
                this.formClosingDate = deal.expected_closing_date ? deal.expected_closing_date.split('T')[0] : '';
                this.formQuotationNum = deal.quotation_number || '';
                this.isStageModalOpen = true;
            },

            syncProbability() {
                const stageProbs = {
                    'Qualification': 10,
                    'Qualified Opportunity': 25,
                    'Proposal Request': 50,
                    'Quotation': 70,
                    'Negotiation': 85,
                    'Approval': 95,
                    'Contract / PO / SPK': 98,
                    'Closed Won': 100,
                    'Closed Lost': 0
                };
                if (stageProbs[this.formStage] !== undefined) {
                    this.formProb = stageProbs[this.formStage];
                }
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        // 1. Chart Tren Nilai Penjualan & Forecast Bulanan
        const forecastData = @json($monthlyForecastChart);
        const actualData = @json($monthlyActualChart);
        const ctxTrend = document.getElementById('monthlyTrendChart').getContext('2d');
        
        const gradient = ctxTrend.createLinearGradient(0, 0, 0, 240);
        gradient.addColorStop(0, 'rgba(200, 30, 44, 0.22)');
        gradient.addColorStop(1, 'rgba(200, 30, 44, 0.00)');

        new Chart(ctxTrend, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                datasets: [
                    {
                        type: 'bar',
                        label: 'Weighted Forecast (Juta Rp)',
                        data: forecastData,
                        backgroundColor: '#F59E0B',
                        borderRadius: 6,
                        maxBarThickness: 16,
                    },
                    {
                        type: 'bar',
                        label: 'Closed Won (Juta Rp)',
                        data: actualData,
                        backgroundColor: '#C81E2C',
                        borderRadius: 6,
                        maxBarThickness: 16,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        align: 'end',
                        labels: {
                            boxWidth: 10,
                            font: { size: 10.5, family: "'Inter', sans-serif" },
                            color: '#4B5563',
                            padding: 10
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(ctx) {
                                return ' ' + ctx.dataset.label + ': Rp ' + ctx.parsed.y.toLocaleString('id-ID') + ' Jt';
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

        // 2. Chart Komposisi Nilai Deals
        const stageData = [
            {{ ($stageFunnel['Qualification']['value'] ?? 0) / 1000000 }},
            {{ ($stageFunnel['Proposal Request']['value'] ?? 0) / 1000000 }},
            {{ ($stageFunnel['Quotation']['value'] ?? 0) / 1000000 }},
            {{ ($stageFunnel['Negotiation']['value'] ?? 0) / 1000000 }},
            {{ ($stageFunnel['Contract / PO / SPK']['value'] ?? 0) / 1000000 }},
            {{ ($stageFunnel['Closed Won']['value'] ?? 0) / 1000000 }}
        ];
        const totalVal = stageData.reduce((a, b) => a + b, 0);

        const ctxDoughnut = document.getElementById('stageDoughnutChart').getContext('2d');
        new Chart(ctxDoughnut, {
            type: 'doughnut',
            data: {
                labels: ['Qualification', 'Proposal', 'Quotation', 'Negotiation', 'Contract/PO', 'Won'],
                datasets: [{
                    data: totalVal > 0 ? stageData : [1, 0, 0, 0, 0, 0],
                    backgroundColor: totalVal > 0 ? ['#3B82F6', '#8B5CF6', '#EAB308', '#F97316', '#10B981', '#C81E2C'] : ['#E5E7EB', '#E5E7EB', '#E5E7EB', '#E5E7EB', '#E5E7EB', '#E5E7EB'],
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
                            padding: 10
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(ctx) {
                                if (totalVal === 0) return ' Belum ada data';
                                return ' ' + ctx.label + ': Rp ' + ctx.parsed.toFixed(1) + ' Jt';
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
