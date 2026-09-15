@extends('layouts.app')

@section('title', 'Market Intelligence - BDM Portal')

<div class="flex h-screen overflow-hidden" x-data="{ 
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
    
    <div class="flex-1 min-w-0 overflow-y-auto bg-[#FAF9F8]">
        @include('components.topbar', ['title' => 'Market Intelligence'])
        
        <div class="p-4 sm:p-5 lg:p-[26px] animate-fade-in space-y-5 max-w-[1600px] mx-auto">

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between shadow-sm">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 font-bold px-2">✕</button>
                </div>
            @endif

            <!-- Filter & Action Bar -->
            <div class="flex flex-col sm:flex-row sm:flex-wrap sm:justify-between sm:items-center gap-2.5">
                <form method="GET" action="{{ route('bdm.intelligence.index') }}" class="flex flex-col sm:flex-row flex-wrap gap-2.5 w-full sm:w-auto">
                    <div class="relative">
                        <svg class="w-3.5 h-3.5 absolute left-3 top-3 text-[#948F99]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" 
                               name="search" 
                               value="{{ $search }}"
                               placeholder="Cari judul riset, kata kunci..." 
                               class="w-full sm:w-64 px-[11px] py-[9px] pl-8 rounded-lg border border-[#E7E5E3] text-[13.5px] text-[#17151C] outline-none bg-white focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all">
                    </div>
                    <select name="category" onchange="this.form.submit()" 
                            class="w-full sm:w-48 px-[11px] py-[9px] rounded-lg border border-[#E7E5E3] text-[13.5px] text-[#17151C] outline-none bg-white focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all cursor-pointer">
                        <option value="">Semua Kategori</option>
                        <option value="Market Trend" {{ $category === 'Market Trend' ? 'selected' : '' }}>Market Trend</option>
                        <option value="Competitor Movement" {{ $category === 'Competitor Movement' ? 'selected' : '' }}>Competitor Movement</option>
                        <option value="Industry Analysis" {{ $category === 'Industry Analysis' ? 'selected' : '' }}>Industry Analysis</option>
                        <option value="Regulatory / Policy" {{ $category === 'Regulatory / Policy' ? 'selected' : '' }}>Regulatory / Policy</option>
                        <option value="Marketing Campaign" {{ $category === 'Marketing Campaign' ? 'selected' : '' }}>Marketing Campaign</option>
                    </select>
                    @if($search || $category)
                        <a href="{{ route('bdm.intelligence.index') }}" 
                           class="px-3 py-2 text-[13px] font-semibold text-gray-500 hover:text-gray-700 self-center">
                            Reset
                        </a>
                    @endif
                </form>

                <button type="button" @click="isIntelModalOpen = true" 
                        class="w-full sm:w-auto justify-center bg-[#C81E2C] text-white shadow-[0_8px_20px_rgba(200,30,44,0.24)] px-[17px] py-[9px] rounded-lg font-semibold text-[13.5px] flex items-center gap-1.5 hover:brightness-105 active:translate-y-[1px] transition-all cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Input Laporan Intelijen</span>
                </button>
            </div>

            <!-- Table Container -->
            <div class="bg-white rounded-xl border border-[#E7E5E3] shadow-[0_1px_2px_rgba(14,13,18,0.05)] overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[900px] border-collapse text-[13.5px]">
                        <thead>
                            <tr class="bg-[#F1F0EE] border-b border-[#E7E5E3]">
                                <th class="text-left py-3.5 px-4 text-[11.5px] font-semibold text-[#75727C] uppercase tracking-[0.3px]">Judul Riset & Analisis</th>
                                <th class="text-left py-3.5 px-4 text-[11.5px] font-semibold text-[#75727C] uppercase tracking-[0.3px]">Kategori</th>
                                <th class="text-left py-3.5 px-4 text-[11.5px] font-semibold text-[#75727C] uppercase tracking-[0.3px]">Sektor Industri</th>
                                <th class="text-right py-3.5 px-4 text-[11.5px] font-semibold text-[#75727C] uppercase tracking-[0.3px]">Potensi Nilai</th>
                                <th class="text-center py-3.5 px-4 text-[11.5px] font-semibold text-[#75727C] uppercase tracking-[0.3px]">Impact</th>
                                <th class="text-right py-3.5 px-4 text-[11.5px] font-semibold text-[#75727C] uppercase tracking-[0.3px]">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#EFEDEB]">
                            @forelse($intels as $mi)
                                @php
                                    $impactBadge = match($mi->impact_level) {
                                        'High' => 'bg-red-50 text-[#C81E2C] border-[#FADADF]',
                                        'Medium' => 'bg-amber-50 text-amber-700 border-amber-200',
                                        default => 'bg-blue-50 text-blue-700 border-blue-200',
                                    };
                                @endphp
                                <tr class="hover:bg-[#F8F7F6] transition-colors">
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-[#17151C] text-[13px] leading-snug">{{ $mi->title }}</div>
                                        <div class="text-[11.5px] text-[#75727C] mt-1 line-clamp-1">{{ $mi->summary }}</div>
                                    </td>
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <span class="px-2.5 py-1 rounded-md text-[11px] font-semibold bg-gray-100 text-gray-700 border border-gray-200">
                                            {{ $mi->category }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-[#3D3A44] font-medium whitespace-nowrap">
                                        {{ $mi->industry_sector }}
                                    </td>
                                    <td class="py-3.5 px-4 text-right whitespace-nowrap font-bold text-[#17151C]">
                                        {{ $mi->potential_value ? 'Rp ' . number_format($mi->potential_value, 0, ',', '.') : '—' }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10.5px] font-bold border {{ $impactBadge }}">
                                            {{ $mi->impact_level }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <button type="button" 
                                                    @click="openDetailModal({{ json_encode($mi) }})"
                                                    class="px-2.5 py-1 bg-white hover:bg-gray-50 text-gray-700 hover:text-gray-900 text-[11.5px] font-semibold rounded-lg border border-gray-200 shadow-xs transition inline-flex items-center gap-1 cursor-pointer">
                                                <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                <span>Detail</span>
                                            </button>
                                            @if($mi->source_url && (str_starts_with($mi->source_url, 'http://') || str_starts_with($mi->source_url, 'https://')))
                                                <a href="{{ $mi->source_url }}" target="_blank" rel="noopener noreferrer"
                                                   class="px-2.5 py-1 bg-blue-50 hover:bg-blue-100 text-blue-700 text-[11.5px] font-semibold rounded-lg border border-blue-200 shadow-xs transition inline-flex items-center gap-1">
                                                    <span>Tautan</span>
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                                </a>
                                            @endif
                                            <button type="button" 
                                                    @click="openDeleteModal({{ json_encode($mi) }})"
                                                    class="px-2.5 py-1 bg-white hover:bg-red-50 text-red-600 hover:text-red-700 text-[11.5px] font-semibold rounded-lg border border-red-200 shadow-xs transition cursor-pointer">
                                                Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-10 text-center text-gray-400 text-xs">
                                        Belum ada data market intelligence yang sesuai filter.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($intels->hasPages())
                    <div class="p-3.5 bg-[#FAF9F8] border-t border-[#E7E5E3] flex items-center justify-between text-xs">
                        <div class="text-gray-500 font-medium">
                            Menampilkan <span class="font-bold text-gray-800">{{ $intels->firstItem() }}</span> - <span class="font-bold text-gray-800">{{ $intels->lastItem() }}</span> dari <span class="font-bold text-gray-800">{{ $intels->total() }}</span> data
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
         class="fixed inset-0 z-50 bg-[#0E0D12]/60 flex items-center justify-center p-3 sm:p-5 backdrop-blur-xs"
         @click.self="isIntelModalOpen = false">
        <div class="bg-white rounded-2xl w-[600px] max-w-full max-h-[85vh] flex flex-col overflow-hidden shadow-[0_20px_60px_rgba(14,13,18,0.2)] border border-[#E7E5E3] my-auto animate-fade-in-up">
            
            {{-- Modal Header (Fixed) --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-[#E7E5E3] flex-shrink-0 bg-white">
                <div>
                    <h3 class="font-display text-[17px] font-bold text-[#17151C]">Input Market Intelligence</h3>
                    <p class="text-[12px] text-[#75727C] mt-0.5">Riset tren industri, pergerakan kompetitor & potensi lelang.</p>
                </div>
                <button type="button" @click="isIntelModalOpen = false" class="rounded-lg p-1.5 text-[#75727C] hover:text-[#17151C] hover:bg-[#F1F0EE] transition-colors cursor-pointer">
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
                        <label class="block text-[12px] font-semibold text-[#17151C] mb-1">Judul Riset / Intelijen <span class="text-[#C81E2C]">*</span></label>
                        <input type="text" name="title" required class="w-full bg-white border border-[#E7E5E3] rounded-lg px-3 py-2 text-[13px] text-[#17151C] outline-none focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all" placeholder="e.g. Modernisasi Core Network & SD-WAN PLN 2026">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[12px] font-semibold text-[#17151C] mb-1">Kategori <span class="text-[#C81E2C]">*</span></label>
                            <select name="category" required class="w-full bg-white border border-[#E7E5E3] rounded-lg px-3 py-2 text-[13px] text-[#17151C] outline-none focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all cursor-pointer">
                                <option value="Market Trend">Market Trend</option>
                                <option value="Competitor Movement">Competitor Movement</option>
                                <option value="Industry Analysis">Industry Analysis</option>
                                <option value="Regulatory / Policy">Regulatory / Policy</option>
                                <option value="Marketing Campaign">Marketing Campaign</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[12px] font-semibold text-[#17151C] mb-1">Sektor Industri <span class="text-[#C81E2C]">*</span></label>
                            <select name="industry_sector" required class="w-full bg-white border border-[#E7E5E3] rounded-lg px-3 py-2 text-[13px] text-[#17151C] outline-none focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all cursor-pointer">
                                <option value="Government / Kementerian">Government / Kementerian</option>
                                <option value="Banking & Financial">Banking & Financial</option>
                                <option value="BUMN & Enterprise">BUMN & Enterprise</option>
                                <option value="Healthcare & Hospital">Healthcare & Hospital</option>
                                <option value="Education / Others">Education / Others</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[12px] font-semibold text-[#17151C] mb-1">Ringkasan Analisis Pasar <span class="text-[#C81E2C]">*</span></label>
                        <textarea name="summary" rows="2.5" required class="w-full bg-white border border-[#E7E5E3] rounded-lg p-2.5 text-[13px] text-[#17151C] outline-none focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all" placeholder="Detail peluang, peta persaingan vendor, tantangan, atau estimasi kebutuhan..."></textarea>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[12px] font-semibold text-[#17151C] mb-1">Potensi Nilai Pasar (Rp)</label>
                            <input type="number" name="potential_value" min="0" class="w-full bg-white border border-[#E7E5E3] rounded-lg px-3 py-2 text-[13px] font-bold text-[#17151C] outline-none focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all" placeholder="3000000000">
                        </div>
                        <div>
                            <label class="block text-[12px] font-semibold text-[#17151C] mb-1">Tingkat Dampak (Impact)</label>
                            <select name="impact_level" class="w-full bg-white border border-[#E7E5E3] rounded-lg px-3 py-2 text-[13px] text-[#17151C] outline-none focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all cursor-pointer">
                                <option value="High" selected>High Impact</option>
                                <option value="Medium">Medium Impact</option>
                                <option value="Low">Low Impact</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[12px] font-semibold text-[#17151C] mb-1">Sumber / Tautan Rujukan</label>
                        <input type="text" name="source_url" class="w-full bg-white border border-[#E7E5E3] rounded-lg px-3 py-2 text-[13px] text-[#17151C] outline-none focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all" placeholder="https://lpse.kemkes.go.id atau Catatan Rapat">
                    </div>
                </form>
            </div>

            {{-- Modal Footer (Fixed) --}}
            <div class="flex items-center gap-3 px-6 py-3.5 border-t border-[#E7E5E3] bg-[#FAF9F8] flex-shrink-0">
                <button type="submit" 
                        form="intelForm" 
                        class="flex-1 flex items-center justify-center min-h-[40px] bg-[#C81E2C] hover:bg-[#AF1424] text-white shadow-[0_8px_20px_rgba(200,30,44,0.24)] px-4 py-2 rounded-xl font-semibold text-[13px] active:translate-y-[1px] transition-all cursor-pointer">
                    Simpan Laporan
                </button>
                <button type="button" 
                        @click="isIntelModalOpen = false" 
                        class="flex-1 flex items-center justify-center min-h-[40px] bg-white hover:bg-[#F8F7F6] text-[#3D3A44] border border-[#E7E5E3] shadow-[0_1px_2px_rgba(14,13,18,0.05)] px-4 py-2 rounded-xl font-semibold text-[13px] active:translate-y-[1px] transition-all cursor-pointer">
                    Batal
                </button>
            </div>

        </div>
    </div>

    {{-- MODAL DETAIL MARKET INTELLIGENCE --}}
    <div x-show="isDetailModalOpen" 
         x-cloak 
         class="fixed inset-0 z-50 bg-[#0E0D12]/60 flex items-center justify-center p-3 sm:p-5 backdrop-blur-xs"
         @click.self="isDetailModalOpen = false">
        <div class="bg-white rounded-2xl w-[640px] max-w-full max-h-[85vh] flex flex-col overflow-hidden shadow-[0_20px_60px_rgba(14,13,18,0.2)] border border-[#E7E5E3] my-auto animate-fade-in-up">
            
            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-[#E7E5E3] flex-shrink-0 bg-white">
                <div>
                    <span class="px-2 py-0.5 rounded text-[10.5px] font-bold bg-[#FDF1F2] text-[#C81E2C] border border-[#FADADF]" x-text="selectedIntel ? selectedIntel.category : ''"></span>
                    <h3 class="font-display text-[16px] font-bold text-[#17151C] mt-1" x-text="selectedIntel ? selectedIntel.title : ''"></h3>
                </div>
                <button type="button" @click="isDetailModalOpen = false" class="rounded-lg p-1.5 text-[#75727C] hover:text-[#17151C] hover:bg-[#F1F0EE] transition-colors cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Body --}}
            <div class="px-6 py-4 overflow-y-auto flex-1 space-y-4 text-xs">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 bg-[#FAF9F8] p-3.5 rounded-xl border border-[#E7E5E3]">
                    <div>
                        <span class="text-[#75727C] text-[11px] block font-semibold">Sektor Industri</span>
                        <span class="text-[#17151C] font-bold text-[12.5px]" x-text="selectedIntel ? selectedIntel.industry_sector : '-'"></span>
                    </div>
                    <div>
                        <span class="text-[#75727C] text-[11px] block font-semibold">Potensi Nilai Pasar</span>
                        <span class="text-[#C81E2C] font-bold text-[12.5px]" x-text="selectedIntel && selectedIntel.potential_value ? 'Rp ' + Number(selectedIntel.potential_value).toLocaleString('id-ID') : '—'"></span>
                    </div>
                    <div>
                        <span class="text-[#75727C] text-[11px] block font-semibold">Tingkat Dampak</span>
                        <span class="font-bold text-[12.5px]" :class="selectedIntel && selectedIntel.impact_level === 'High' ? 'text-red-700' : 'text-blue-700'" x-text="selectedIntel ? selectedIntel.impact_level + ' Impact' : '-'"></span>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-[12px] font-bold text-[#17151C] block">Ringkasan Analisis & Peluang</label>
                    <div class="p-3.5 bg-white border border-[#E7E5E3] rounded-xl text-[13px] text-[#17151C] leading-relaxed whitespace-pre-line" x-text="selectedIntel ? selectedIntel.summary : ''"></div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-[12px] font-bold text-[#17151C] block">Sumber / Rujukan Informasi</label>
                    <div class="p-3 bg-[#FAF9F8] border border-[#E7E5E3] rounded-xl flex items-center justify-between gap-3">
                        <span class="text-[12.5px] text-gray-700 font-medium break-all" x-text="selectedIntel && selectedIntel.source_url ? selectedIntel.source_url : 'Informasi Internal / Riset BDM'"></span>
                        <template x-if="selectedIntel && selectedIntel.source_url && (selectedIntel.source_url.startsWith('http://') || selectedIntel.source_url.startsWith('https://'))">
                            <a :href="selectedIntel.source_url" target="_blank" rel="noopener noreferrer" class="px-2.5 py-1 bg-white border border-blue-300 text-blue-700 rounded-md font-bold text-[11px] hover:bg-blue-50 transition shrink-0 inline-flex items-center gap-1">
                                <span>Buka Tautan</span>
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
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
         class="fixed inset-0 z-50 bg-[#0E0D12]/60 flex items-center justify-center p-4 backdrop-blur-xs"
         @click.self="isDeleteModalOpen = false">
        <div class="bg-white rounded-2xl w-[440px] max-w-full overflow-hidden shadow-[0_20px_60px_rgba(14,13,18,0.25)] border border-[#E7E5E3] my-auto animate-fade-in-up p-6 text-center space-y-4">
            
            {{-- Warning Icon Badge --}}
            <div class="w-14 h-14 mx-auto rounded-2xl bg-red-50 border border-red-100 flex items-center justify-center text-[#C81E2C]">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </div>

            <div>
                <h3 class="font-display text-[17px] font-bold text-[#17151C]">Hapus Market Intelligence?</h3>
                <p class="text-[13px] text-[#75727C] mt-1.5 leading-relaxed">
                    Apakah Anda yakin ingin menghapus data intelijen <strong class="text-gray-900 font-semibold" x-text="deleteIntelData ? deleteIntelData.title : ''"></strong>?
                </p>
            </div>

            {{-- Summary Card --}}
            <template x-if="deleteIntelData">
                <div class="p-3 bg-[#FAF9F8] rounded-xl border border-[#E7E5E3] text-left text-xs space-y-1 text-gray-600">
                    <div class="flex justify-between">
                        <span class="text-gray-400">Kategori:</span>
                        <span class="font-bold text-gray-800" x-text="deleteIntelData.category || '—'"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Sektor:</span>
                        <span class="font-bold text-gray-800" x-text="deleteIntelData.industry_sector || '—'"></span>
                    </div>
                </div>
            </template>

            {{-- Action Buttons --}}
            <div class="flex items-center gap-3 pt-2">
                <button type="button" 
                        @click="isDeleteModalOpen = false" 
                        class="flex-1 min-h-[40px] bg-white hover:bg-[#F8F7F6] text-[#3D3A44] border border-[#E7E5E3] shadow-xs px-4 py-2 rounded-xl font-semibold text-[13px] transition cursor-pointer">
                    Batal
                </button>

                <form :action="deleteIntelData ? '{{ url('/bdm/intelligence') }}/' + deleteIntelData.id : '#'" method="POST" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="w-full min-h-[40px] bg-[#C81E2C] hover:bg-[#AF1424] text-white shadow-[0_8px_20px_rgba(200,30,44,0.24)] px-4 py-2 rounded-xl font-semibold text-[13px] transition cursor-pointer">
                        Ya, Hapus Data
                    </button>
                </form>
            </div>

        </div>
    </div>

</div>
@endsection
