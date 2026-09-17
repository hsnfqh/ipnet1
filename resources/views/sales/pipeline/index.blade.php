@extends('layouts.app')

@section('title', 'Peluang & Pipeline - Sales Portal PT IP Network Solusindo')

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
        animation: fadeUpStagger 0.5s cubic-bezier(0.16, 1, 0.3, 1) both;
    }

    .anim-delay-1 { animation-delay: 0.06s !important; }
    .anim-delay-2 { animation-delay: 0.12s !important; }
    .anim-delay-3 { animation-delay: 0.18s !important; }
    .anim-delay-4 { animation-delay: 0.24s !important; }
</style>
@endpush

@section('content')
<div class="flex h-screen overflow-hidden bg-[#F8FAFC] font-sans" x-data="salesPipelinePage()">
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Peluang & Pipeline'])
        
        <div class="p-4 sm:p-6 lg:p-7 space-y-6 max-w-[1680px] mx-auto animate-fade-in">
            
            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 font-bold px-2">✕</button>
                </div>
            @endif

            {{-- 3 SUMMARY METRIC CARDS --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 anim-fade-up anim-delay-1">
                <div class="ipnet-metric-card flex items-center justify-between">
                    <div>
                        <span class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider block">Total Active Pipeline</span>
                        <div class="text-xl font-black text-gray-900 tracking-tight mt-0.5">
                            Rp {{ number_format($totalPipelineValue / 1000000, 1, ',', '.') }} Jt
                        </div>
                        <span class="text-[11px] text-gray-400 mt-0.5 block">Seluruh peluang belum Won/Lost</span>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-blue-50 border border-blue-100 text-blue-600 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    </div>
                </div>

                <div class="ipnet-metric-card flex items-center justify-between">
                    <div>
                        <span class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider block">Weighted Forecast</span>
                        <div class="text-xl font-black text-amber-900 tracking-tight mt-0.5">
                            Rp {{ number_format($totalWeightedForecast / 1000000, 1, ',', '.') }} Jt
                        </div>
                        <span class="text-[11px] text-gray-400 mt-0.5 block">Estimasi bobot probabilitas (%)</span>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-amber-50 border border-amber-100 text-amber-600 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    </div>
                </div>

                <div class="ipnet-metric-card flex items-center justify-between">
                    <div>
                        <span class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider block">Total Won Deals</span>
                        <div class="text-xl font-black text-emerald-700 tracking-tight mt-0.5">
                            Rp {{ number_format($totalWonValue / 1000000, 1, ',', '.') }} Jt
                        </div>
                        <span class="text-[11px] text-gray-400 mt-0.5 block">Kontrak tereksekusi resmi</span>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    </div>
                </div>
            </div>

            <!-- Filter & Action Bar -->
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 anim-fade-up anim-delay-2">
                <form method="GET" action="{{ route('sales.pipeline.index') }}" class="flex flex-col sm:flex-row flex-wrap gap-2.5 w-full sm:w-auto">
                    <div class="relative">
                        <svg class="w-4 h-4 absolute left-3.5 top-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" 
                               name="search" 
                               value="{{ $search }}"
                               placeholder="Cari nama peluang, klien, quotation..." 
                               class="w-full sm:w-72 px-3.5 py-2 pl-9 rounded-xl border border-gray-200 text-xs font-semibold text-gray-800 bg-white focus:outline-none focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D] transition-all shadow-xs">
                    </div>

                    <select name="stage" onchange="this.form.submit()" 
                            class="w-full sm:w-52 px-3.5 py-2 rounded-xl border border-gray-200 text-xs font-semibold text-gray-800 bg-white focus:outline-none focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D] transition-all cursor-pointer shadow-xs">
                        <option value="">Semua Tahapan SOP</option>
                        @foreach($stages as $stageKey => $meta)
                            <option value="{{ $stageKey }}" {{ $filterStage == $stageKey ? 'selected' : '' }}>{{ $meta['label'] }}</option>
                        @endforeach
                    </select>

                    @if($isManagerial)
                    <select name="sales" onchange="this.form.submit()" 
                            class="w-full sm:w-48 px-3.5 py-2 rounded-xl border border-gray-200 text-xs font-semibold text-gray-800 bg-white focus:outline-none focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D] transition-all cursor-pointer shadow-xs">
                        <option value="">Semua Sales PIC</option>
                        @foreach($salesTeam as $sp)
                            <option value="{{ $sp }}" {{ $filterSales == $sp ? 'selected' : '' }}>{{ $sp }}</option>
                        @endforeach
                    </select>
                    @endif

                    @if($search || $filterStage || ($isManagerial && $filterSales))
                        <a href="{{ route('sales.pipeline.index') }}" 
                           class="px-3 py-2 text-xs font-bold text-gray-500 hover:text-gray-800 self-center">
                            Reset Filter
                        </a>
                    @endif
                </form>

                <div class="flex items-center gap-2">
                    <button type="button" 
                            @click="isNewOppModalOpen = true"
                            class="btn-ipnet-primary inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold shadow-md transition-all cursor-pointer whitespace-nowrap">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        <span>Tambah Peluang Baru</span>
                    </button>
                </div>
            </div>

            {{-- Main Data Table --}}
            <div class="ipnet-card overflow-hidden anim-fade-up anim-delay-3">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-gray-50 text-gray-500 uppercase text-[10.5px] font-bold">
                            <tr>
                                <th class="py-3 px-4 rounded-l-lg">Peluang &amp; Klien</th>
                                <th class="py-3 px-4">Sales &amp; BDM Origin</th>
                                <th class="py-3 px-4 text-right">Nilai Nominal &amp; Quo</th>
                                <th class="py-3 px-4 text-center">Tahapan &amp; Probabilitas</th>
                                <th class="py-3 px-4">Closing Horizon</th>
                                <th class="py-3 px-4 rounded-r-lg text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 font-medium text-gray-700">
                            @forelse($projects as $p)
                                @php
                                    $stageMeta = $stages[$p->sales_stage] ?? ['color' => '#6B7280', 'bg' => '#F3F4F6'];
                                    $prob = $p->win_probability ?? ($stages[$p->sales_stage]['default_prob'] ?? 10);
                                    $wtd = ($p->contract_value ?? 0) * ($prob / 100);
                                @endphp
                                <tr class="hover:bg-gray-50/80 transition-colors">
                                    {{-- Proyek & Klien --}}
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-gray-900 max-w-sm">{{ $p->name }}</div>
                                        <div class="text-[11px] text-gray-400 font-medium mt-0.5 flex items-center gap-1.5">
                                            <span>{{ $p->client }}</span>
                                            @if($p->proposal_file)
                                                <span>•</span>
                                                <a href="{{ route('projects.proposal.download', $p->id) }}" class="text-emerald-700 font-bold hover:underline">
                                                    SOW Presales ✓
                                                </a>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- Sales PIC & BDM --}}
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <div class="font-bold text-gray-900">{{ $p->sales_name ?: 'Belum Assign' }}</div>
                                        @php
                                            $bdmName = 'Direct Sales';
                                            if ($p->bdm) {
                                                $bdmName = $p->bdm->name;
                                            } elseif ($p->creator && ($p->creator->hasAnyRole(['BDM', 'BusDev', 'Business Development']) || str_contains(strtolower($p->creator->position ?? ''), 'bdm'))) {
                                                $bdmName = $p->creator->name;
                                            }
                                        @endphp
                                        <div class="text-[11px] text-gray-400">BDM: {{ $bdmName }}</div>
                                    </td>

                                    {{-- Nilai & Quotation --}}
                                    <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                        <div class="font-black text-gray-900">
                                            {{ $p->contract_value > 0 ? 'Rp ' . number_format($p->contract_value, 0, ',', '.') : '—' }}
                                        </div>
                                        <div class="text-[11px] text-gray-400">
                                            @if($p->quotation_number)
                                                <span class="font-mono text-gray-700">Quo: {{ $p->quotation_number }}</span>
                                            @else
                                                <span class="italic text-gray-400">Belum ada Quo</span>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- Tahapan & Probabilitas --}}
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        <div class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10.5px] font-bold" 
                                              style="background-color: {{ $stageMeta['bg'] }}; color: {{ $stageMeta['color'] }};">
                                            <span>{{ $p->sales_stage }}</span>
                                            <span class="text-[10px] opacity-90">({{ $prob }}%)</span>
                                        </div>
                                        <div class="text-[10px] text-gray-400 mt-1">
                                            Weighted: Rp {{ number_format($wtd / 1000000, 1, ',', '.') }} Jt
                                        </div>
                                    </td>

                                    {{-- Closing Date --}}
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        @if($p->expected_closing_date)
                                            <div class="font-bold text-gray-900">
                                                {{ \Carbon\Carbon::parse($p->expected_closing_date)->format('d M Y') }}
                                            </div>
                                            <div class="text-[10.5px] text-gray-400">
                                                {{ \Carbon\Carbon::parse($p->expected_closing_date)->diffForHumans() }}
                                            </div>
                                        @else
                                            <span class="text-gray-400 italic">Belum diset</span>
                                        @endif
                                    </td>

                                    {{-- Aksi --}}
                                    <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <button type="button" 
                                                    @click="openStageModal({{ json_encode($p) }})"
                                                    class="px-2.5 py-1 bg-white hover:bg-red-50 text-[#8F0A0D] text-[11px] font-bold rounded-lg border border-red-200 shadow-xs hover:border-[#8F0A0D] transition cursor-pointer">
                                                Update
                                            </button>
                                            <a href="{{ route('projects.show', $p->id) }}" 
                                               class="px-2.5 py-1 bg-white hover:bg-gray-50 text-gray-700 text-[11px] font-semibold rounded-lg border border-gray-200 shadow-xs transition cursor-pointer">
                                                Detail
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-12 text-center text-gray-400 text-xs">
                                        Tidak ada peluang yang cocok dengan filter.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($projects->hasPages())
                    <div class="p-4 border-t border-gray-100 bg-[#F8FAFC]">
                        {{ $projects->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>

    {{-- MODAL UPDATE SALES LIFECYCLE STAGE & FORECAST --}}
    <template x-teleport="body">
        <div x-show="isStageModalOpen" 
             x-cloak 
             class="fixed inset-0 z-50 bg-[#0F172A]/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-5"
             @click.self="isStageModalOpen = false">
            <div class="bg-white rounded-2xl w-[720px] max-w-full max-h-[90vh] flex flex-col overflow-hidden shadow-2xl border border-[#E2E8F0] my-auto anim-fade-up">
                
                {{-- Modal Header --}}
                <div class="flex items-center justify-between border-b border-[#E2E8F0] p-5 sm:p-6 pb-4 shrink-0 bg-white">
                    <div>
                        <p class="text-[#8F0A0D] text-[11px] font-bold uppercase tracking-wider">Update Pipeline</p>
                        <h3 class="text-[16px] font-bold text-[#1E293B]" x-text="'Update Lifecycle: ' + (selectedDeal.name || '')"></h3>
                    </div>
                    <button type="button" @click="isStageModalOpen = false" class="text-[#94A3B8] hover:text-[#1E293B] p-1.5 rounded-lg hover:bg-[#F1F5F9] transition cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Modal Body Form --}}
                <form :action="'/sales/pipeline/' + selectedDeal.id + '/stage'" method="POST" enctype="multipart/form-data" class="flex flex-col flex-1 min-h-0 overflow-hidden">
                    @csrf
                    
                    <div class="p-5 sm:p-6 overflow-y-auto flex-1 space-y-4 text-[12.5px]">
                        
                        {{-- Ringkasan Peluang --}}
                        <div class="p-3.5 bg-[#F8FAFC] rounded-xl border border-[#E2E8F0] flex flex-wrap items-center justify-between gap-2 text-xs">
                            <div>
                                <span class="text-[#64748B] block font-medium">Klien / Perusahaan:</span>
                                <span class="font-bold text-[#1E293B] text-[13px]" x-text="selectedDeal.client || '—'"></span>
                            </div>
                            <div class="text-right">
                                <span class="text-[#64748B] block font-medium">Sales Commercial PIC:</span>
                                <span class="font-bold text-[#8F0A0D] text-[13px]" x-text="selectedDeal.sales_name || 'Belum Ditugaskan'"></span>
                            </div>
                        </div>

                        {{-- Tahapan & Probabilitas --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <div>
                                <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">
                                    Tahapan Siklus Penjualan <span class="text-[#8F0A0D]">*</span>
                                </label>
                                <select name="sales_stage" x-model="formStage" @change="syncProbability()" required
                                        class="w-full px-3.5 py-2.5 bg-white border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition cursor-pointer">
                                    @foreach($stages as $sKey => $sMeta)
                                        <option value="{{ $sKey }}">{{ $sMeta['label'] }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">
                                    Win Probability (%) <span class="text-[#8F0A0D]">*</span>
                                </label>
                                <input type="number" name="win_probability" x-model="formProb" min="0" max="100" required
                                       class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition">
                            </div>
                        </div>

                        {{-- Nilai & Closing Date --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <div>
                                <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">
                                    Nilai Kontrak Estimasi (Rp)
                                </label>
                                <input type="number" name="contract_value" x-model="formContractValue" min="0" step="1000"
                                       class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition">
                            </div>

                            <div>
                                <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">
                                    Target Closing Date
                                </label>
                                <input type="date" name="expected_closing_date" x-model="formClosingDate"
                                       class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition cursor-pointer">
                            </div>
                        </div>

                        {{-- Section Quotation --}}
                        <div class="p-4 rounded-xl border border-[#E2E8F0] bg-[#F8FAFC] space-y-3">
                            <span class="text-[11px] font-bold text-[#1E293B] uppercase tracking-wider block">Dokumen Penawaran Resmi (Quotation)</span>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                <div>
                                    <label class="block font-bold text-[#64748B] text-[11px] mb-1">Nomor Quotation</label>
                                    <input type="text" name="quotation_number" x-model="formQuotationNum" placeholder="QUO-IPNET/2026/..."
                                           class="w-full px-3.5 py-2 bg-white border border-[#CBD5E1] rounded-xl text-[12px] font-medium text-[#1E293B] focus:outline-none focus:border-[#8F0A0D]">
                                </div>
                                <div>
                                    <label class="block font-bold text-[#64748B] text-[11px] mb-1">Upload Berkas Quotation</label>
                                    <input type="file" name="quotation_file" accept=".pdf,.docx,.xlsx,.zip"
                                           class="w-full px-3 py-1.5 bg-white border border-[#CBD5E1] rounded-xl text-[11px] text-[#64748B] file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[11px] file:font-bold file:bg-[#8F0A0D] file:text-white hover:file:bg-[#73080A] cursor-pointer">
                                </div>
                            </div>
                        </div>

                        {{-- Closed Lost Reason --}}
                        <div x-show="formStage === 'Closed Lost'" class="p-4 rounded-xl border border-rose-200 bg-rose-50/70 space-y-3">
                            <span class="text-[11px] font-bold text-rose-900 uppercase tracking-wider block">Alasan Deal Drop / Kalah Tender</span>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                <div>
                                    <label class="block font-bold text-rose-900 text-[11px] mb-1">Kompetitor Pemenang</label>
                                    <input type="text" name="lost_competitor" placeholder="Contoh: PT Integrator Lain"
                                           class="w-full px-3.5 py-2 bg-white border border-rose-200 rounded-xl text-[12px]">
                                </div>
                                <div>
                                    <label class="block font-bold text-rose-900 text-[11px] mb-1">Alasan Utama</label>
                                    <input type="text" name="lost_reason" placeholder="Contoh: Harga lebih tinggi, Spek tidak masuk"
                                           class="w-full px-3.5 py-2 bg-white border border-rose-200 rounded-xl text-[12px]">
                                </div>
                            </div>
                        </div>

                        {{-- Catatan --}}
                        <div>
                            <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">
                                Catatan Progres Negosiasi / Follow-up
                            </label>
                            <textarea name="sales_notes" rows="2" placeholder="Tuliskan ringkasan perkembangan negosiasi dengan klien..."
                                      class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition resize-none"></textarea>
                        </div>

                    </div>

                    {{-- Footer --}}
                    <div class="flex items-center justify-end gap-2.5 p-4 px-6 border-t border-[#E2E8F0] bg-[#F8FAFC] shrink-0">
                        <button type="button" @click="isStageModalOpen = false" 
                                class="px-4 py-2.5 text-[12.5px] font-bold text-[#475569] hover:bg-[#F1F5F9] border border-[#CBD5E1] rounded-xl transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" 
                                class="px-5 py-2.5 text-[12.5px] font-bold text-white bg-[#8F0A0D] hover:bg-[#73080A] rounded-xl shadow-md transition cursor-pointer">
                            Simpan Perubahan Stage
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </template>

    {{-- MODAL TAMBAH PELUANG BARU --}}
    <template x-teleport="body">
        <div x-show="isNewOppModalOpen" 
             x-cloak 
             class="fixed inset-0 z-50 bg-[#0F172A]/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-5"
             @click.self="isNewOppModalOpen = false">
            <div class="bg-white rounded-2xl w-[700px] max-w-full max-h-[90vh] flex flex-col overflow-hidden shadow-2xl border border-[#E2E8F0] my-auto anim-fade-up">
                
                {{-- Modal Header --}}
                <div class="flex items-center justify-between border-b border-[#E2E8F0] p-5 sm:p-6 pb-4 shrink-0 bg-white">
                    <div>
                        <p class="text-[#8F0A0D] text-[11px] font-bold uppercase tracking-wider">Peluang Bisnis</p>
                        <h3 class="text-[16px] font-bold text-[#1E293B]">Tambah Peluang Komersial Baru</h3>
                    </div>
                    <button type="button" @click="isNewOppModalOpen = false" class="text-[#94A3B8] hover:text-[#1E293B] p-1.5 rounded-lg hover:bg-[#F1F5F9] transition cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Modal Body Form --}}
                <form action="{{ route('sales.pipeline.store') }}" method="POST" class="flex flex-col flex-1 min-h-0 overflow-hidden">
                    @csrf

                    <div class="p-5 sm:p-6 overflow-y-auto flex-1 space-y-3.5 text-[12.5px]">
                        
                        <div>
                            <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">
                                Nama Proyek / Peluang <span class="text-[#8F0A0D]">*</span>
                            </label>
                            <input type="text" name="name" required placeholder="Contoh: Pengadaan Switch Core & WiFi 6 Kantor Cabang"
                                   class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <div>
                                <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">
                                    Nama Klien / Perusahaan <span class="text-[#8F0A0D]">*</span>
                                </label>
                                <input type="text" name="client" required placeholder="Contoh: PT Bank Mandiri (Persero)"
                                       class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition">
                            </div>

                            <div>
                                <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">
                                    Sumber Peluang (Lead Source)
                                </label>
                                <select name="opportunity_source"
                                        class="w-full px-3.5 py-2.5 bg-white border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition cursor-pointer">
                                    <option value="Direct Sales Prospecting">Direct Sales Prospecting</option>
                                    <option value="Inbound Leads / Website">Inbound Leads / Website</option>
                                    <option value="Tender Pemerintah / BUMN">Tender Pemerintah / BUMN</option>
                                    <option value="Referral Klien Lama">Referral Klien Lama</option>
                                    <option value="Channel Partner / Prinsipal">Channel Partner / Prinsipal</option>
                                    <option value="BDM Handover">BDM Handover</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <div>
                                <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">
                                    Estimasi Nilai Kontrak (Rp) <span class="text-[#8F0A0D]">*</span>
                                </label>
                                <input type="number" name="contract_value" min="0" step="1000" required placeholder="0"
                                       class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition">
                            </div>

                            <div>
                                <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">
                                    Tahapan Awal (Sales Stage)
                                </label>
                                <select name="sales_stage" 
                                        class="w-full px-3.5 py-2.5 bg-white border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition cursor-pointer">
                                    @foreach($stages as $sKey => $sMeta)
                                        <option value="{{ $sKey }}" {{ $sKey == 'Qualification' ? 'selected' : '' }}>{{ $sMeta['label'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <div>
                                <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">
                                    Target Closing Date
                                </label>
                                <input type="date" name="expected_closing_date"
                                       class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition cursor-pointer">
                            </div>

                            <div>
                                <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">
                                    Win Probability (%) (Opsional)
                                </label>
                                <input type="number" name="win_probability" min="0" max="100" placeholder="Default sesuai stage"
                                       class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition">
                            </div>
                        </div>

                        <div>
                            <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">
                                Deskripsi Kebutuhan &amp; Ruang Lingkup Singkat
                            </label>
                            <textarea name="sales_notes" rows="2.5" placeholder="Catatan singkat mengenai kebutuhan solusi teknis, anggaran klien, atau konteks tender..."
                                      class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition resize-none"></textarea>
                        </div>
                    </div>

                    {{-- Fixed Footer --}}
                    <div class="flex items-center justify-end gap-2.5 p-4 px-6 border-t border-[#E2E8F0] bg-[#F8FAFC] shrink-0">
                        <button type="button" @click="isNewOppModalOpen = false" 
                                class="px-4 py-2.5 text-[12.5px] font-bold text-[#475569] hover:bg-[#F1F5F9] border border-[#CBD5E1] rounded-xl transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" 
                                class="px-5 py-2.5 text-[12.5px] font-bold text-white bg-[#8F0A0D] hover:bg-[#73080A] rounded-xl shadow-md transition cursor-pointer">
                            Simpan Peluang Bisnis
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </template>

</div>
@endsection

@push('scripts')
<script>
    function salesPipelinePage() {
        return {
            isStageModalOpen: false,
            isNewOppModalOpen: false,
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
</script>
@endpush
