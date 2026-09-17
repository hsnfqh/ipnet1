@extends('layouts.app')

@section('title', 'Market Intelligence - BDM Portal PT IP Network Solusindo')

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
        font-weight: 600;
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
</style>
@endpush

@section('content')
<div class="flex h-screen overflow-hidden bg-[#F8FAFC] font-sans" x-data="{ 
    isIntelModalOpen: false, 
    isDetailModalOpen: false, 
    isDeleteModalOpen: false,
    selectedIntel: null, 
    deleteIntelData: null,
    openDetailModal(mi) { 
        this.selectedIntel = mi; 
        this.isDetailModalOpen = true; 
    },
    openDeleteModal(mi) {
        this.deleteIntelData = mi;
        this.isDeleteModalOpen = true;
    }
}">
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Market Intelligence'])
        
        <div class="p-4 sm:p-6 lg:p-7 space-y-6 max-w-[1680px] mx-auto animate-fade-in">

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-medium flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 font-bold px-2">✕</button>
                </div>
            @endif

            {{-- 3 SUMMARY METRIC CARDS --}}
            @php
                $highImpactCount = $intels->where('impact_level', 'High')->count();
                $totalPotensi = $intels->sum('potential_value');
            @endphp
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 anim-fade-up anim-delay-1">
                <div class="ipnet-metric-card flex items-center justify-between">
                    <div>
                        <span class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider block">Total Laporan Riset</span>
                        <div class="text-xl font-bold text-gray-800 tracking-tight mt-0.5">
                            {{ $intels->total() }} Dokumen
                        </div>
                        <span class="text-[11.5px] text-gray-400 mt-0.5 block">Database riset pasar &amp; kompetisi</span>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-purple-50 border border-purple-100 text-purple-600 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>
                </div>

                <div class="ipnet-metric-card flex items-center justify-between">
                    <div>
                        <span class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider block">High Impact Analysis</span>
                        <div class="text-xl font-bold text-[#8F0A0D] tracking-tight mt-0.5">
                            {{ $highImpactCount }} Prioritas Tinggi
                        </div>
                        <span class="text-[11.5px] text-red-600 font-medium mt-0.5 block">Dampak signifikan ke bisnis</span>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-red-50 border border-red-100 text-[#8F0A0D] flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    </div>
                </div>

                <div class="ipnet-metric-card flex items-center justify-between">
                    <div>
                        <span class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider block">Potensi Nilai Pasar</span>
                        <div class="text-xl font-bold text-emerald-700 tracking-tight mt-0.5">
                            Rp {{ number_format($totalPotensi / 1000000, 1, ',', '.') }} Jt
                        </div>
                        <span class="text-[11.5px] text-gray-400 mt-0.5 block">Estimasi opportunity pipeline</span>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    </div>
                </div>
            </div>

            <!-- Filter & Action Bar -->
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 anim-fade-up anim-delay-2">
                <form method="GET" action="{{ route('bdm.intelligence.index') }}" class="flex flex-col sm:flex-row flex-wrap gap-2.5 w-full sm:w-auto">
                    <div class="relative">
                        <svg class="w-4 h-4 absolute left-3.5 top-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" 
                               name="search" 
                               value="{{ $search }}"
                               placeholder="Cari judul riset, kata kunci..." 
                               class="w-full sm:w-72 px-3.5 py-2 pl-9 rounded-xl border border-gray-200 text-xs font-medium text-gray-800 bg-white focus:outline-none focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D] transition-all shadow-xs">
                    </div>

                    <select name="category" onchange="this.form.submit()" 
                            class="w-full sm:w-56 px-3.5 py-2 rounded-xl border border-gray-200 text-xs font-medium text-gray-800 bg-white focus:outline-none focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D] transition-all cursor-pointer shadow-xs">
                        <option value="">Semua Kategori</option>
                        <option value="Market Trend" {{ $category === 'Market Trend' ? 'selected' : '' }}>Market Trend</option>
                        <option value="Competitor Movement" {{ $category === 'Competitor Movement' ? 'selected' : '' }}>Competitor Movement</option>
                        <option value="Industry Analysis" {{ $category === 'Industry Analysis' ? 'selected' : '' }}>Industry Analysis</option>
                        <option value="Regulatory / Policy" {{ $category === 'Regulatory / Policy' ? 'selected' : '' }}>Regulatory / Policy</option>
                        <option value="Marketing Campaign" {{ $category === 'Marketing Campaign' ? 'selected' : '' }}>Marketing Campaign</option>
                    </select>

                    @if($search || $category)
                        <a href="{{ route('bdm.intelligence.index') }}" 
                           class="px-3 py-2 text-xs font-medium text-gray-500 hover:text-gray-800 self-center">
                            Reset Filter
                        </a>
                    @endif
                </form>

                <div class="flex items-center gap-2">
                    <button type="button" 
                            @click="isIntelModalOpen = true" 
                            class="btn-ipnet-primary inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs font-semibold shadow-md transition-all cursor-pointer whitespace-nowrap">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>Input Laporan Intelijen</span>
                    </button>
                </div>
            </div>

            <!-- Table Container -->
            <div class="ipnet-card overflow-hidden anim-fade-up anim-delay-3">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-gray-50/80 text-gray-500 uppercase text-[10.5px] font-semibold tracking-wider">
                            <tr>
                                <th class="py-3 px-4 rounded-l-lg">Judul Riset &amp; Analisis</th>
                                <th class="py-3 px-4">Kategori</th>
                                <th class="py-3 px-4">Sektor Industri</th>
                                <th class="py-3 px-4 text-right">Potensi Nilai</th>
                                <th class="py-3 px-4 text-center">Impact</th>
                                <th class="py-3 px-4 rounded-r-lg text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 font-normal text-gray-600">
                            @forelse($intels as $mi)
                                @php
                                    $impactBadge = match($mi->impact_level) {
                                        'High' => 'bg-red-50 text-[#8F0A0D] border-red-200',
                                        'Medium' => 'bg-amber-50 text-amber-700 border-amber-200',
                                        default => 'bg-blue-50 text-blue-700 border-blue-200',
                                    };
                                @endphp
                                <tr class="hover:bg-gray-50/80 transition-colors">
                                    <td class="py-3.5 px-4">
                                        <div class="font-semibold text-gray-800 max-w-sm">{{ $mi->title }}</div>
                                        <div class="text-[11px] text-gray-400 mt-0.5 line-clamp-1">{{ $mi->summary }}</div>
                                    </td>
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <span class="px-2.5 py-0.5 rounded-md text-[10.5px] font-medium bg-gray-100 text-gray-700 border border-gray-200">
                                            {{ $mi->category }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 font-medium text-gray-700 whitespace-nowrap">
                                        {{ $mi->industry_sector }}
                                    </td>
                                    <td class="py-3.5 px-4 text-right whitespace-nowrap font-bold text-gray-800">
                                        {{ $mi->potential_value ? 'Rp ' . number_format($mi->potential_value, 0, ',', '.') : '—' }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[10.5px] font-medium border {{ $impactBadge }}">
                                            {{ $mi->impact_level }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <button type="button" 
                                                    @click="openDetailModal({{ json_encode($mi) }})"
                                                    class="px-2.5 py-1.5 bg-white hover:bg-gray-50 text-gray-700 text-xs font-medium rounded-lg border border-gray-200 shadow-xs transition inline-flex items-center gap-1 cursor-pointer">
                                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                <span>Detail</span>
                                            </button>
                                            @if($mi->source_url && (str_starts_with($mi->source_url, 'http://') || str_starts_with($mi->source_url, 'https://')))
                                                <a href="{{ $mi->source_url }}" target="_blank" rel="noopener noreferrer"
                                                   class="px-2.5 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-medium rounded-lg border border-blue-200 shadow-xs transition inline-flex items-center gap-1">
                                                    <span>Tautan</span>
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                                </a>
                                            @endif
                                            <button type="button" 
                                                    @click="openDeleteModal({{ json_encode($mi) }})"
                                                    class="p-1.5 bg-white hover:bg-red-50 text-red-600 hover:text-red-700 rounded-lg border border-red-200 shadow-xs transition cursor-pointer"
                                                    title="Hapus Laporan">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-12 text-center text-gray-400 text-xs font-medium">
                                        Belum ada data market intelligence yang sesuai filter.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($intels->hasPages())
                    <div class="p-4 bg-gray-50/70 border-t border-gray-100 flex items-center justify-between text-xs">
                        <div class="text-gray-500 font-medium">
                            Menampilkan <span class="font-semibold text-gray-800">{{ $intels->firstItem() }}</span> - <span class="font-semibold text-gray-800">{{ $intels->lastItem() }}</span> dari <span class="font-semibold text-gray-800">{{ $intels->total() }}</span> data
                        </div>
                        <div>
                            {{ $intels->appends(request()->query())->links() }}
                        </div>
                    </div>
                @endif
            </div>

        </div>
    </div>

    {{-- MODAL INPUT MARKET INTELLIGENCE --}}
    <div x-show="isIntelModalOpen" 
         x-cloak 
         class="fixed inset-0 z-50 bg-slate-900/60 flex items-center justify-center p-3 sm:p-5 backdrop-blur-sm"
         @click.self="isIntelModalOpen = false">
        <div class="bg-white rounded-2xl w-[600px] max-w-full max-h-[85vh] flex flex-col overflow-hidden shadow-2xl border border-gray-200 my-auto animate-fade-in-up">
            
            {{-- Modal Header (Fixed) --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 flex-shrink-0 bg-white">
                <div>
                    <h3 class="text-base font-bold text-gray-800">Input Market Intelligence</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Riset tren industri, pergerakan kompetitor &amp; potensi lelang.</p>
                </div>
                <button type="button" @click="isIntelModalOpen = false" class="rounded-xl p-2 text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition-colors cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Modal Body (Scrollable) --}}
            <div class="px-6 py-4 overflow-y-auto flex-1">
                <form id="intelForm" action="{{ route('bdm.intelligence.store') }}" method="POST" class="space-y-3.5 text-xs">
                    @csrf
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Judul Riset / Intelijen <span class="text-[#8F0A0D]">*</span></label>
                        <input type="text" name="title" required class="w-full bg-white border border-gray-200 rounded-xl px-3.5 py-2 text-xs font-medium text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D] transition-all" placeholder="e.g. Modernisasi Core Network &amp; SD-WAN PLN 2026">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Kategori <span class="text-[#8F0A0D]">*</span></label>
                            <select name="category" required class="w-full bg-white border border-gray-200 rounded-xl px-3.5 py-2 text-xs font-medium text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D] transition-all cursor-pointer">
                                <option value="Market Trend">Market Trend</option>
                                <option value="Competitor Movement">Competitor Movement</option>
                                <option value="Industry Analysis">Industry Analysis</option>
                                <option value="Regulatory / Policy">Regulatory / Policy</option>
                                <option value="Marketing Campaign">Marketing Campaign</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Sektor Industri <span class="text-[#8F0A0D]">*</span></label>
                            <select name="industry_sector" required class="w-full bg-white border border-gray-200 rounded-xl px-3.5 py-2 text-xs font-medium text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D] transition-all cursor-pointer">
                                <option value="Government / Kementerian">Government / Kementerian</option>
                                <option value="Banking & Financial">Banking & Financial</option>
                                <option value="BUMN & Enterprise">BUMN & Enterprise</option>
                                <option value="Healthcare & Hospital">Healthcare & Hospital</option>
                                <option value="Education / Others">Education / Others</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Ringkasan Analisis Pasar <span class="text-[#8F0A0D]">*</span></label>
                        <textarea name="summary" rows="2.5" required class="w-full bg-white border border-gray-200 rounded-xl p-2.5 text-xs text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D] transition-all" placeholder="Detail peluang, peta persaingan vendor, tantangan, atau estimasi kebutuhan..."></textarea>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Potensi Nilai Pasar (Rp)</label>
                            <input type="number" name="potential_value" min="0" class="w-full bg-white border border-gray-200 rounded-xl px-3.5 py-2 text-xs font-semibold text-[#8F0A0D] focus:outline-none focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D] transition-all" placeholder="3000000000">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Tingkat Dampak (Impact)</label>
                            <select name="impact_level" class="w-full bg-white border border-gray-200 rounded-xl px-3.5 py-2 text-xs font-medium text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D] transition-all cursor-pointer">
                                <option value="High" selected>High Impact</option>
                                <option value="Medium">Medium Impact</option>
                                <option value="Low">Low Impact</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Sumber / Tautan Rujukan</label>
                        <input type="text" name="source_url" class="w-full bg-white border border-gray-200 rounded-xl px-3.5 py-2 text-xs font-medium text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D] transition-all" placeholder="https://lpse.kemkes.go.id atau Catatan Rapat">
                    </div>
                </form>
            </div>

            {{-- Modal Footer (Fixed) --}}
            <div class="flex items-center gap-3 px-6 py-4 border-t border-gray-100 bg-gray-50 flex-shrink-0">
                <button type="submit" 
                        form="intelForm" 
                        class="btn-ipnet-primary flex-1 flex items-center justify-center min-h-[40px] rounded-xl font-semibold text-xs shadow-md transition-all cursor-pointer">
                    Simpan Laporan
                </button>
                <button type="button" 
                        @click="isIntelModalOpen = false" 
                        class="flex-1 flex items-center justify-center min-h-[40px] bg-white hover:bg-gray-100 text-gray-600 border border-gray-200 shadow-xs rounded-xl font-medium text-xs transition-all cursor-pointer">
                    Batal
                </button>
            </div>

        </div>
    </div>

    {{-- MODAL DETAIL MARKET INTELLIGENCE --}}
    <div x-show="isDetailModalOpen" 
         x-cloak 
         class="fixed inset-0 z-50 bg-slate-900/60 flex items-center justify-center p-3 sm:p-5 backdrop-blur-sm"
         @click.self="isDetailModalOpen = false">
        <div class="bg-white rounded-2xl w-[620px] max-w-full max-h-[85vh] flex flex-col overflow-hidden shadow-2xl border border-gray-200 my-auto animate-fade-in-up">
            
            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 flex-shrink-0 bg-white">
                <div>
                    <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-red-50 text-[#8F0A0D] border border-red-200" x-text="selectedIntel ? selectedIntel.category : ''"></span>
                    <h3 class="text-base font-bold text-gray-800 mt-1" x-text="selectedIntel ? selectedIntel.title : ''"></h3>
                </div>
                <button type="button" @click="isDetailModalOpen = false" class="rounded-xl p-2 text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition-colors cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Body --}}
            <div class="px-6 py-4 overflow-y-auto flex-1 space-y-4 text-xs">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 bg-gray-50 p-3.5 rounded-xl border border-gray-200">
                    <div>
                        <span class="text-gray-400 text-[11px] block font-medium">Sektor Industri</span>
                        <span class="text-gray-800 font-semibold text-xs" x-text="selectedIntel ? selectedIntel.industry_sector : '-'"></span>
                    </div>
                    <div>
                        <span class="text-gray-400 text-[11px] block font-medium">Potensi Nilai Pasar</span>
                        <span class="text-[#8F0A0D] font-bold text-xs" x-text="selectedIntel && selectedIntel.potential_value ? 'Rp ' + Number(selectedIntel.potential_value).toLocaleString('id-ID') : '—'"></span>
                    </div>
                    <div>
                        <span class="text-gray-400 text-[11px] block font-medium">Tingkat Dampak</span>
                        <span class="font-semibold text-xs" :class="selectedIntel && selectedIntel.impact_level === 'High' ? 'text-red-700' : 'text-blue-700'" x-text="selectedIntel ? selectedIntel.impact_level + ' Impact' : '-'"></span>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-gray-800 block">Ringkasan Analisis &amp; Peluang</label>
                    <div class="p-3 bg-white border border-gray-200 rounded-xl text-xs text-gray-700 leading-relaxed whitespace-pre-line" x-text="selectedIntel ? selectedIntel.summary : ''"></div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-gray-800 block">Sumber / Rujukan Informasi</label>
                    <div class="p-3 bg-gray-50 border border-gray-200 rounded-xl flex items-center justify-between gap-3">
                        <span class="text-xs text-gray-600 font-medium break-all" x-text="selectedIntel && selectedIntel.source_url ? selectedIntel.source_url : 'Informasi Internal / Riset BDM'"></span>
                        <template x-if="selectedIntel && selectedIntel.source_url && (selectedIntel.source_url.startsWith('http://') || selectedIntel.source_url.startsWith('https://'))">
                            <a :href="selectedIntel.source_url" target="_blank" rel="noopener noreferrer" class="px-2.5 py-1 bg-white border border-blue-300 text-blue-700 rounded-lg font-medium text-xs hover:bg-blue-50 transition shrink-0 inline-flex items-center gap-1">
                                <span>Buka Tautan</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            </a>
                        </template>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- MODAL KONFIRMASI HAPUS MARKET INTELLIGENCE --}}
    <div x-show="isDeleteModalOpen" 
         x-cloak 
         class="fixed inset-0 z-50 bg-slate-900/60 flex items-center justify-center p-4 backdrop-blur-sm"
         @click.self="isDeleteModalOpen = false">
        <div class="bg-white rounded-2xl w-[440px] max-w-full overflow-hidden shadow-2xl border border-gray-200 my-auto animate-fade-in-up p-6 text-center space-y-4">
            
            {{-- Warning Icon Badge --}}
            <div class="w-12 h-12 mx-auto rounded-2xl bg-red-50 border border-red-100 flex items-center justify-center text-[#8F0A0D]">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </div>

            <div>
                <h3 class="text-base font-bold text-gray-800">Hapus Market Intelligence?</h3>
                <p class="text-xs text-gray-500 mt-1.5 leading-relaxed">
                    Apakah Anda yakin ingin menghapus data riset <strong class="text-gray-800 font-semibold" x-text="deleteIntelData ? deleteIntelData.title : ''"></strong>?
                </p>
            </div>

            {{-- Summary Card --}}
            <template x-if="deleteIntelData">
                <div class="p-3.5 bg-gray-50 rounded-xl border border-gray-200 text-left text-xs space-y-1.5 text-gray-600">
                    <div class="flex justify-between">
                        <span class="text-gray-400">Kategori:</span>
                        <span class="font-semibold text-gray-800" x-text="deleteIntelData.category || '—'"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Sektor:</span>
                        <span class="font-semibold text-gray-800" x-text="deleteIntelData.industry_sector || '—'"></span>
                    </div>
                </div>
            </template>

            {{-- Action Buttons --}}
            <div class="flex items-center gap-3 pt-2">
                <button type="button" 
                        @click="isDeleteModalOpen = false" 
                        class="flex-1 min-h-[40px] bg-white hover:bg-gray-100 text-gray-600 border border-gray-200 shadow-xs px-4 py-2 rounded-xl font-medium text-xs transition cursor-pointer">
                    Batal
                </button>

                <form :action="deleteIntelData ? '{{ url('/bdm/intelligence') }}/' + deleteIntelData.id : '#'" method="POST" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="w-full min-h-[40px] bg-[#8F0A0D] hover:bg-[#73080A] text-white shadow-md px-4 py-2 rounded-xl font-semibold text-xs transition cursor-pointer">
                        Ya, Hapus
                    </button>
                </form>
            </div>

        </div>
    </div>

</div>
@endsection
