@extends('layouts.app')

@section('title', 'Inisiasi Peluang - BDM Portal PT IP Network Solusindo')

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
<div class="flex h-screen overflow-hidden bg-[#F8FAFC] font-sans" x-data="opportunityPortal()">
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Inisiasi Peluang'])
        
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

            @if(session('error'))
                <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-xs font-medium flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700 font-bold px-2">✕</button>
                </div>
            @endif

            @if($errors->any())
                <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-xs font-medium space-y-1 shadow-xs">
                    <div class="font-bold">Gagal Memproses Serah Terima:</div>
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- 3 SUMMARY METRIC CARDS --}}
            @php
                $totalVal = $opportunities->sum('contract_value');
                $handedCount = $opportunities->where('bdm_handover_status', 'Handed Over to Sales')->count();
                $acceptedCount = $opportunities->where('bdm_handover_status', 'Accepted by Sales')->count();
            @endphp
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 anim-fade-up anim-delay-1">
                <div class="ipnet-metric-card flex items-center justify-between">
                    <div>
                        <span class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider block">Total Peluang Terdata</span>
                        <div class="text-xl font-bold text-gray-800 tracking-tight mt-0.5">
                            {{ $opportunities->total() }} Peluang Prospek
                        </div>
                        <span class="text-[11.5px] text-gray-400 mt-0.5 block">Halaman ini &amp; database aktif</span>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-red-50 border border-red-100 text-[#8F0A0D] flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    </div>
                </div>

                <div class="ipnet-metric-card flex items-center justify-between">
                    <div>
                        <span class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider block">Diserahkan / Diterima Sales</span>
                        <div class="text-xl font-bold text-blue-700 tracking-tight mt-0.5">
                            {{ $handedCount + $acceptedCount }} Prospek SOP
                        </div>
                        <span class="text-[11.5px] text-blue-600 font-medium mt-0.5 block">{{ $acceptedCount }} Terkonfirmasi diterima</span>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-100 text-blue-600 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                    </div>
                </div>

                <div class="ipnet-metric-card flex items-center justify-between">
                    <div>
                        <span class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider block">Nilai Estimasi Pipeline</span>
                        <div class="text-xl font-bold text-emerald-700 tracking-tight mt-0.5">
                            Rp {{ number_format($totalVal / 1000000, 1, ',', '.') }} Jt
                        </div>
                        <span class="text-[11.5px] text-gray-400 mt-0.5 block">Akumulasi estimasi nilai proyek</span>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    </div>
                </div>
            </div>

            <!-- Filter & Action Bar -->
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 anim-fade-up anim-delay-2">
                <form method="GET" action="{{ route('bdm.opportunities.index') }}" class="flex flex-col sm:flex-row flex-wrap gap-2.5 w-full sm:w-auto">
                    <div class="relative">
                        <svg class="w-4 h-4 absolute left-3.5 top-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" 
                               name="search" 
                               value="{{ $search }}"
                               placeholder="Cari nama peluang, klien, sumber..." 
                               class="w-full sm:w-72 px-3.5 py-2 pl-9 rounded-xl border border-gray-200 text-xs font-medium text-gray-800 bg-white focus:outline-none focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D] transition-all shadow-xs">
                    </div>

                    <select name="status" onchange="this.form.submit()" 
                            class="w-full sm:w-56 px-3.5 py-2 rounded-xl border border-gray-200 text-xs font-medium text-gray-800 bg-white focus:outline-none focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D] transition-all cursor-pointer shadow-xs">
                        <option value="">Semua Status Handover</option>
                        <option value="draft" {{ $filterStatus === 'draft' ? 'selected' : '' }}>Draft (Belum Serah Terima)</option>
                        <option value="handed_over" {{ $filterStatus === 'handed_over' ? 'selected' : '' }}>Diserahkan ke Sales</option>
                        <option value="accepted" {{ $filterStatus === 'accepted' ? 'selected' : '' }}>Diterima Sales</option>
                    </select>

                    @if($search || $filterStatus)
                        <a href="{{ route('bdm.opportunities.index') }}" 
                           class="px-3 py-2 text-xs font-medium text-gray-500 hover:text-gray-800 self-center">
                            Reset Filter
                        </a>
                    @endif
                </form>

                <div class="flex items-center gap-2">
                    <button type="button" 
                            @click="isNewOppModalOpen = true" 
                            class="btn-ipnet-primary inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs font-semibold shadow-md transition-all cursor-pointer whitespace-nowrap">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>Inisiasi Peluang Baru</span>
                    </button>
                </div>
            </div>

            <!-- Table Container -->
            <div class="ipnet-card overflow-hidden anim-fade-up anim-delay-3">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-gray-50/80 text-gray-500 uppercase text-[10.5px] font-semibold tracking-wider">
                            <tr>
                                <th class="py-3 px-4 rounded-l-lg">Peluang &amp; Target</th>
                                <th class="py-3 px-4">Klien &amp; Sumber</th>
                                <th class="py-3 px-4 text-right">Estimasi Nilai</th>
                                <th class="py-3 px-4 text-center">Kelayakan BD</th>
                                <th class="py-3 px-4 text-center">Status Handover</th>
                                <th class="py-3 px-4 rounded-r-lg text-right">Aksi SOP</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 font-normal text-gray-600">
                            @forelse($opportunities as $opp)
                                @php
                                    $score = $opp->bd_assessment_score ?: 80;
                                    $scoreBadge = $score >= 85 ? 'text-emerald-700 bg-emerald-50 border-emerald-200' : ($score >= 70 ? 'text-blue-700 bg-blue-50 border-blue-200' : 'text-amber-700 bg-amber-50 border-amber-200');
                                @endphp
                                <tr class="hover:bg-gray-50/80 transition-colors">
                                    {{-- 1. Nama Peluang & Target --}}
                                    <td class="py-3.5 px-4">
                                        <div class="font-semibold text-gray-800 max-w-sm">{{ $opp->name }}</div>
                                        <div class="text-[11px] text-gray-400 mt-1 flex items-center gap-1.5 flex-wrap">
                                            <span class="px-1.5 py-0.5 rounded bg-gray-100 text-gray-600 font-mono text-[10px] font-medium">#PRJ-{{ str_pad($opp->id, 4, '0', STR_PAD_LEFT) }}</span>
                                            <span class="text-gray-300">•</span>
                                            <span>Timeline: <strong class="text-gray-600 font-medium">{{ $opp->target_timeline_type ?: 'Q3 2026' }}</strong></span>
                                        </div>
                                    </td>

                                    {{-- 2. Klien & Sumber --}}
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <div class="font-semibold text-gray-800">{{ $opp->client ?: 'Calon Klien' }}</div>
                                        <div class="text-[11px] text-gray-400 mt-0.5">{{ $opp->opportunity_source ?: 'Direct Inbound' }}</div>
                                    </td>

                                    {{-- 3. Estimasi Nilai --}}
                                    <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                        <div class="font-bold text-gray-800">
                                            {{ $opp->contract_value > 0 ? 'Rp ' . number_format($opp->contract_value, 0, ',', '.') : '—' }}
                                        </div>
                                    </td>

                                    {{-- 4. Skor Kelayakan --}}
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[10.5px] font-medium border {{ $scoreBadge }}">
                                            {{ $score }}/100 Feasible
                                        </span>
                                    </td>

                                    {{-- 5. Status Handover --}}
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        @if($opp->bdm_handover_status === 'Handed Over to Sales')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 text-[10.5px] font-medium text-blue-700 bg-blue-50 border border-blue-200 rounded-md" title="Diserahkan ke {{ $opp->sales_name }}">
                                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                                <span>Diserahkan: {{ Str::limit($opp->sales_name ?: 'Sales', 14) }}</span>
                                            </span>
                                        @elseif($opp->bdm_handover_status === 'Accepted by Sales')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 text-[10.5px] font-medium text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-md">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                <span>Diterima Sales</span>
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 text-[10.5px] font-medium text-amber-700 bg-amber-50 border border-amber-200 rounded-md">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                                                <span>Draft Inisiasi</span>
                                            </span>
                                        @endif
                                    </td>

                                    {{-- 6. Aksi SOP --}}
                                    <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                        <div class="flex items-center justify-end gap-1.5">
                                            {{-- Tombol Utama Handover --}}
                                            <button type="button" 
                                                    @click="openHandoverModal({{ $opp->id }})"
                                                    class="px-3 py-1.5 bg-[#8F0A0D] hover:bg-[#73080A] text-white text-xs font-semibold rounded-xl shadow-xs transition inline-flex items-center gap-1.5 cursor-pointer"
                                                    title="Proses Serah Terima SOP ke Sales">
                                                <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                                <span>{{ $opp->bdm_handover_status && $opp->bdm_handover_status !== 'Draft' ? 'Handover' : 'Serah Terima' }}</span>
                                            </button>

                                            {{-- Tombol Edit --}}
                                            <button type="button" 
                                                    @click="openEditModal({{ $opp->id }})"
                                                    class="p-1.5 bg-white hover:bg-gray-50 text-gray-600 hover:text-gray-800 rounded-lg border border-gray-200 shadow-xs transition cursor-pointer"
                                                    title="Edit Inisiasi Peluang">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </button>

                                            {{-- Detail Peluang Modal --}}
                                            <button type="button" 
                                                    @click="openDetailModal({{ $opp->id }})"
                                                    class="p-1.5 bg-white hover:bg-gray-50 text-gray-600 hover:text-gray-800 rounded-lg border border-gray-200 shadow-xs transition inline-flex items-center cursor-pointer"
                                                    title="Lihat Detail Peluang">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            </button>

                                            {{-- Hapus --}}
                                            <button type="button" 
                                                    @click="openDeleteModal({{ $opp->id }})"
                                                    class="p-1.5 bg-white hover:bg-red-50 text-red-600 hover:text-red-700 rounded-lg border border-red-200 shadow-xs transition cursor-pointer"
                                                    title="Hapus Peluang">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-12 text-center text-gray-400 text-xs font-medium">
                                        Belum ada peluang bisnis yang sesuai dengan filter pencarian.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($opportunities->hasPages())
                    <div class="p-4 bg-gray-50/70 border-t border-gray-100 flex items-center justify-between text-xs">
                        <div class="text-gray-500 font-medium">
                            Menampilkan <span class="font-semibold text-gray-800">{{ $opportunities->firstItem() ?? 0 }}</span> - <span class="font-semibold text-gray-800">{{ $opportunities->lastItem() ?? 0 }}</span> dari <span class="font-semibold text-gray-800">{{ $opportunities->total() }}</span> peluang
                        </div>
                        <div>
                            {{ $opportunities->appends(request()->query())->links() }}
                        </div>
                    </div>
                @endif
            </div>

        </div>
    </div>

    {{-- MODAL SERAH TERIMA PELUANG KE SALES (STANDARD LEAD & MAINTENANCE SOP TEMPLATE) --}}
    <div x-show="isHandoverModalOpen" 
         x-cloak 
         class="fixed inset-0 z-[99999] bg-[#0F172A]/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-5"
         @click.self="isHandoverModalOpen = false">
        <div class="bg-white rounded-2xl w-[720px] max-w-full max-h-[90vh] flex flex-col overflow-hidden shadow-2xl border border-[#E2E8F0] my-auto anim-fade-up">
            
            {{-- Modal Header --}}
            <div class="flex items-center justify-between border-b border-[#E2E8F0] p-5 sm:p-6 pb-4 shrink-0 bg-white">
                <div>
                    <div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-red-50 text-[#8F0A0D] text-[10.5px] font-bold uppercase tracking-wider mb-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#8F0A0D]"></span>
                        <span>Handover SOP BDM ke Tim Sales</span>
                    </div>
                    <h3 class="text-[17px] font-bold text-[#1E293B]" x-text="activeOpp ? 'Serah Terima: ' + activeOpp.name : 'Serah Terima Peluang'"></h3>
                </div>
                <button type="button" @click="isHandoverModalOpen = false" class="text-[#94A3B8] hover:text-[#1E293B] p-1.5 rounded-lg hover:bg-[#F1F5F9] transition cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Modal Body --}}
            <div class="p-5 sm:p-6 overflow-y-auto flex-1 space-y-4 text-[12.5px]">
                
                {{-- Context Summary Card --}}
                <div class="p-3.5 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0]">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <span class="text-[11px] text-[#64748B] block font-medium">Klien / Instansi:</span>
                            <span class="font-bold text-[#1E293B] text-[13px]" x-text="activeOpp ? activeOpp.client : '—'"></span>
                        </div>
                        <div>
                            <span class="text-[11px] text-[#64748B] block font-medium">Estimasi Nilai:</span>
                            <span class="font-bold text-[#8F0A0D] text-[13px]" x-text="activeOpp && activeOpp.contract_value ? 'Rp ' + Number(activeOpp.contract_value).toLocaleString('id-ID') : '—'"></span>
                        </div>
                        <div>
                            <span class="text-[11px] text-[#64748B] block font-medium">Target Timeline:</span>
                            <span class="inline-flex px-2 py-0.5 rounded text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200 mt-0.5" x-text="activeOpp ? (activeOpp.target_timeline_type || 'Q3 2026') : '—'"></span>
                        </div>
                    </div>
                </div>

                <form id="handoverForm"
                      :action="'/bdm/opportunities/' + (activeOpp ? activeOpp.id : '') + '/handover'" 
                      method="POST" 
                      enctype="multipart/form-data" 
                      class="space-y-4">
                    @csrf

                    {{-- 1. Kebutuhan Bisnis --}}
                    <div>
                        <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">
                            1. Ringkasan Kebutuhan Bisnis Klien <span class="text-[#8F0A0D]">*</span>
                        </label>
                        <textarea name="business_need_summary" rows="3" required
                                  placeholder="Jelaskan latar belakang pengadaan, problem utama, dan kebutuhan klien..."
                                  class="w-full px-3.5 py-2.5 bg-white border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition resize-none shadow-2xs"
                                  x-text="activeOpp ? activeOpp.business_need_summary : ''"></textarea>
                    </div>

                    {{-- 2. Kontak Stakeholder --}}
                    <div>
                        <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">
                            2. Kontak PIC &amp; Stakeholder Klien
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[11px] font-medium text-[#64748B] mb-1">Nama PIC</label>
                                <input type="text" name="pic_name" :value="activeOpp && activeOpp.stakeholders_data ? activeOpp.stakeholders_data.pic_name : ''"
                                       class="w-full px-3 py-2 bg-white border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition shadow-2xs" placeholder="Nama lengkap PIC">
                            </div>
                            <div>
                                <label class="block text-[11px] font-medium text-[#64748B] mb-1">Jabatan / Peran</label>
                                <input type="text" name="pic_role" :value="activeOpp && activeOpp.stakeholders_data ? activeOpp.stakeholders_data.pic_role : ''"
                                       class="w-full px-3 py-2 bg-white border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition shadow-2xs" placeholder="Kepala IT / Procurement">
                            </div>
                            <div>
                                <label class="block text-[11px] font-medium text-[#64748B] mb-1">No. WhatsApp / HP</label>
                                <input type="text" name="pic_phone" :value="activeOpp && activeOpp.stakeholders_data ? activeOpp.stakeholders_data.pic_phone : ''"
                                       class="w-full px-3 py-2 bg-white border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition shadow-2xs" placeholder="0812xxxxxxxx">
                            </div>
                            <div>
                                <label class="block text-[11px] font-medium text-[#64748B] mb-1">Email</label>
                                <input type="email" name="pic_email" :value="activeOpp && activeOpp.stakeholders_data ? activeOpp.stakeholders_data.pic_email : ''"
                                       class="w-full px-3 py-2 bg-white border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition shadow-2xs" placeholder="pic@instansi.go.id">
                            </div>
                        </div>
                    </div>

                    {{-- 3. Kebutuhan Teknis & Analisis --}}
                    <div>
                        <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">
                            3. Kebutuhan Teknis, Kompetitor &amp; Rekomendasi Mitra
                        </label>
                        <div class="space-y-3">
                            <div>
                                <textarea name="initial_requirement" rows="2" 
                                          class="w-full px-3.5 py-2 bg-white border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition resize-none shadow-2xs"
                                          placeholder="Kebutuhan teknis awal: spesifikasi hardware, bandwidth, atau scope layanan..."
                                          x-text="activeOpp ? activeOpp.initial_requirement : ''"></textarea>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[11px] font-medium text-[#64748B] mb-1">Potensi Kompetitor</label>
                                    <input type="text" name="competitor_analysis" 
                                           :value="activeOpp ? activeOpp.competitor_analysis : ''"
                                           class="w-full px-3 py-2 bg-white border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition shadow-2xs" 
                                           placeholder="e.g. Lintasarta, Telkom, Moratel">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-medium text-[#64748B] mb-1">Rekomendasi Mitra / Vendor</label>
                                    <input type="text" name="recommended_partner" 
                                           :value="activeOpp ? activeOpp.recommended_partner : ''"
                                           class="w-full px-3 py-2 bg-white border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition shadow-2xs" 
                                           placeholder="e.g. Fortinet, Cisco, Ruijie">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 4. Penugasan Sales & Penilaian --}}
                    <div>
                        <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">
                            4. Penugasan Sales PIC &amp; Kelayakan
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[11px] font-medium text-[#64748B] mb-1">
                                    Serahkan ke Sales PIC <span class="text-[#8F0A0D]">*</span>
                                </label>
                                <select name="sales_name" required 
                                        class="w-full px-3 py-2 bg-white border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition cursor-pointer shadow-2xs">
                                    <option value="">-- Pilih Sales PIC Resmi --</option>
                                    @foreach($salesTeamNames as $st)
                                        <option value="{{ $st }}" :selected="activeOpp && activeOpp.sales_name === '{{ $st }}'">{{ $st }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-[11px] font-medium text-[#64748B] mb-1">
                                    Skor Kelayakan BD (0 - 100) <span class="text-[#8F0A0D]">*</span>
                                </label>
                                <input type="number" name="bd_assessment_score" min="10" max="100" required 
                                       :value="activeOpp && activeOpp.bd_assessment_score ? activeOpp.bd_assessment_score : 80"
                                       class="w-full px-3 py-2 bg-white border border-[#CBD5E1] rounded-xl text-[12.5px] font-bold text-[#8F0A0D] focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition shadow-2xs">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-3">
                            <div>
                                <label class="block text-[11px] font-medium text-[#64748B] mb-1">Dokumen Lampiran TOR / RFP (Opsional)</label>
                                <input type="file" name="attachment_file" 
                                       class="w-full px-2.5 py-1.5 bg-white border border-[#CBD5E1] rounded-xl text-[11.5px] text-[#64748B] file:mr-2.5 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[11px] file:font-bold file:bg-[#F1F5F9] file:text-[#334155] hover:file:bg-[#E2E8F0] cursor-pointer shadow-2xs">
                            </div>

                            <div>
                                <label class="block text-[11px] font-medium text-[#64748B] mb-1">Catatan Khusus BDM</label>
                                <input type="text" name="notes" 
                                       :value="activeOpp ? activeOpp.notes : ''"
                                       class="w-full px-3 py-2 bg-white border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition shadow-2xs"
                                       placeholder="Urgensi waktu, arahan khusus...">
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Modal Footer --}}
            <div class="flex items-center justify-end gap-2.5 p-4 px-6 border-t border-[#E2E8F0] bg-[#F8FAFC] shrink-0">
                <button type="button" 
                        @click="isHandoverModalOpen = false" 
                        class="px-4 py-2.5 text-[12px] font-bold text-[#475569] hover:bg-[#F1F5F9] border border-[#CBD5E1] rounded-xl transition cursor-pointer bg-white">
                    Batal
                </button>
                <button type="submit" 
                        form="handoverForm" 
                        class="btn-ipnet-primary px-5 py-2.5 text-[12px] font-bold rounded-xl shadow-md transition cursor-pointer flex items-center gap-2">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                    <span>Kirim &amp; Serah Terima ke Tim Sales</span>
                </button>
            </div>

        </div>
    </div>

    {{-- MODAL INISIASI PELUANG BARU (STANDARD LEAD & MAINTENANCE SOP TEMPLATE) --}}
    <div x-show="isNewOppModalOpen" 
         x-cloak 
         class="fixed inset-0 z-[99999] bg-[#0F172A]/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-5"
         @click.self="isNewOppModalOpen = false">
        <div class="bg-white rounded-2xl w-[600px] max-w-full max-h-[90vh] flex flex-col overflow-hidden shadow-2xl border border-[#E2E8F0] my-auto anim-fade-up">
            
            {{-- Header --}}
            <div class="flex items-center justify-between border-b border-[#E2E8F0] p-5 sm:p-6 pb-4 shrink-0 bg-white">
                <div>
                    <div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-red-50 text-[#8F0A0D] text-[10.5px] font-bold uppercase tracking-wider mb-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#8F0A0D]"></span>
                        <span>Formulir Inisiasi</span>
                    </div>
                    <h3 class="text-[17px] font-bold text-[#1E293B]">Inisiasi Peluang Bisnis Baru</h3>
                </div>
                <button type="button" @click="isNewOppModalOpen = false" class="text-[#94A3B8] hover:text-[#1E293B] p-1.5 rounded-lg hover:bg-[#F1F5F9] transition cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Body --}}
            <div class="p-5 sm:p-6 overflow-y-auto flex-1 text-[12.5px]">
                <form action="{{ route('bdm.opportunities.store') }}" method="POST" id="newOppForm" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">Nama Peluang / Proyek <span class="text-[#8F0A0D]">*</span></label>
                        <input type="text" name="name" required class="w-full px-3.5 py-2.5 bg-white border border-[#CBD5E1] rounded-xl text-[13px] font-medium text-[#1E293B] focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition shadow-2xs" placeholder="e.g. Pengadaan Firewall &amp; SD-WAN BPOM 2026">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div>
                            <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">Nama Klien / Instansi <span class="text-[#8F0A0D]">*</span></label>
                            <input type="text" name="client" required class="w-full px-3.5 py-2.5 bg-white border border-[#CBD5E1] rounded-xl text-[13px] font-medium text-[#1E293B] focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition shadow-2xs" placeholder="e.g. BPOM">
                        </div>
                        <div>
                            <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">Estimasi Nilai Kontrak (Rp) <span class="text-[#8F0A0D]">*</span></label>
                            <input type="number" name="contract_value" required min="0" class="w-full px-3.5 py-2.5 bg-white border border-[#CBD5E1] rounded-xl text-[13px] font-bold text-[#8F0A0D] focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition shadow-2xs" placeholder="1500000000">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div>
                            <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">Sumber Peluang <span class="text-[#8F0A0D]">*</span></label>
                            <select name="opportunity_source" required class="w-full px-3.5 py-2.5 bg-white border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition cursor-pointer shadow-2xs">
                                <option value="Inbound Market Intel">Inbound Market Intel</option>
                                <option value="Gov Procurement (LPSE)">Gov Procurement (LPSE)</option>
                                <option value="Partner Referral">Partner Referral (Prinsipal)</option>
                                <option value="Direct Prospecting">Direct Prospecting</option>
                                <option value="Event / Exhibition">Event / Exhibition</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">Target Timeline</label>
                            <select name="target_timeline_type" class="w-full px-3.5 py-2.5 bg-white border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition cursor-pointer shadow-2xs">
                                <option value="Urgent (< 1 Bulan)">Urgent (&lt; 1 Bulan)</option>
                                <option value="Q3 2026" selected>Q3 2026</option>
                                <option value="Q4 2026">Q4 2026</option>
                                <option value="Tender APBD/APBN 2027">Tender APBD/APBN 2027</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">Kebutuhan Teknis Awal</label>
                        <textarea name="initial_requirement" rows="3" class="w-full px-3.5 py-2.5 bg-white border border-[#CBD5E1] rounded-xl text-[12.5px] text-[#1E293B] focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition resize-none shadow-2xs" placeholder="Ringkasan kebutuhan spesifikasi hardware / layanan jaringan yang dibutuhkan..."></textarea>
                    </div>
                </form>
            </div>

            {{-- Footer --}}
            <div class="flex items-center justify-end gap-2.5 p-4 px-6 border-t border-[#E2E8F0] bg-[#F8FAFC] shrink-0">
                <button type="button" 
                        @click="isNewOppModalOpen = false" 
                        class="px-4 py-2.5 text-[12px] font-bold text-[#475569] hover:bg-[#F1F5F9] border border-[#CBD5E1] rounded-xl transition cursor-pointer bg-white">
                    Batal
                </button>
                <button type="submit" 
                        form="newOppForm" 
                        class="btn-ipnet-primary px-5 py-2.5 text-[12px] font-bold rounded-xl shadow-md transition cursor-pointer">
                    Simpan Peluang
                </button>
            </div>

        </div>
    </div>

    {{-- MODAL EDIT PELUANG BISNIS (STANDARD LEAD & MAINTENANCE SOP TEMPLATE) --}}
    <div x-show="isEditOppModalOpen" 
         x-cloak 
         class="fixed inset-0 z-[99999] bg-[#0F172A]/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-5"
         @click.self="isEditOppModalOpen = false">
        <div class="bg-white rounded-2xl w-[600px] max-w-full max-h-[90vh] flex flex-col overflow-hidden shadow-2xl border border-[#E2E8F0] my-auto anim-fade-up">
            
            {{-- Header --}}
            <div class="flex items-center justify-between border-b border-[#E2E8F0] p-5 sm:p-6 pb-4 shrink-0 bg-white">
                <div>
                    <div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-red-50 text-[#8F0A0D] text-[10.5px] font-bold uppercase tracking-wider mb-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#8F0A0D]"></span>
                        <span>Perbarui Data</span>
                    </div>
                    <h3 class="text-[17px] font-bold text-[#1E293B]">Edit Inisiasi Peluang Bisnis</h3>
                </div>
                <button type="button" @click="isEditOppModalOpen = false" class="text-[#94A3B8] hover:text-[#1E293B] p-1.5 rounded-lg hover:bg-[#F1F5F9] transition cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Body --}}
            <div class="p-5 sm:p-6 overflow-y-auto flex-1 text-[12.5px]">
                <form :action="editOppData ? '{{ url('/bdm/opportunities') }}/' + editOppData.id : '#'" method="POST" id="editOppForm" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">Nama Peluang / Proyek <span class="text-[#8F0A0D]">*</span></label>
                        <input type="text" name="name" :value="editOppData ? editOppData.name : ''" required class="w-full px-3.5 py-2.5 bg-white border border-[#CBD5E1] rounded-xl text-[13px] font-medium text-[#1E293B] focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition shadow-2xs" placeholder="Nama proyek">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div>
                            <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">Nama Klien / Instansi <span class="text-[#8F0A0D]">*</span></label>
                            <input type="text" name="client" :value="editOppData ? editOppData.client : ''" required class="w-full px-3.5 py-2.5 bg-white border border-[#CBD5E1] rounded-xl text-[13px] font-medium text-[#1E293B] focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition shadow-2xs" placeholder="Nama instansi">
                        </div>
                        <div>
                            <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">Estimasi Nilai Kontrak (Rp) <span class="text-[#8F0A0D]">*</span></label>
                            <input type="number" name="contract_value" :value="editOppData ? editOppData.contract_value : ''" required min="0" class="w-full px-3.5 py-2.5 bg-white border border-[#CBD5E1] rounded-xl text-[13px] font-bold text-[#8F0A0D] focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition shadow-2xs">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div>
                            <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">Sumber Peluang <span class="text-[#8F0A0D]">*</span></label>
                            <select name="opportunity_source" required class="w-full px-3.5 py-2.5 bg-white border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition cursor-pointer shadow-2xs">
                                <option value="Inbound Market Intel" :selected="editOppData && editOppData.opportunity_source === 'Inbound Market Intel'">Inbound Market Intel</option>
                                <option value="Gov Procurement (LPSE)" :selected="editOppData && editOppData.opportunity_source === 'Gov Procurement (LPSE)'">Gov Procurement (LPSE)</option>
                                <option value="Partner Referral" :selected="editOppData && editOppData.opportunity_source === 'Partner Referral'">Partner Referral (Prinsipal)</option>
                                <option value="Direct Prospecting" :selected="editOppData && editOppData.opportunity_source === 'Direct Prospecting'">Direct Prospecting</option>
                                <option value="Event / Exhibition" :selected="editOppData && editOppData.opportunity_source === 'Event / Exhibition'">Event / Exhibition</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">Target Timeline</label>
                            <select name="target_timeline_type" class="w-full px-3.5 py-2.5 bg-white border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition cursor-pointer shadow-2xs">
                                <option value="Urgent (< 1 Bulan)" :selected="editOppData && editOppData.target_timeline_type === 'Urgent (< 1 Bulan)'">Urgent (&lt; 1 Bulan)</option>
                                <option value="Q3 2026" :selected="editOppData && editOppData.target_timeline_type === 'Q3 2026'">Q3 2026</option>
                                <option value="Q4 2026" :selected="editOppData && editOppData.target_timeline_type === 'Q4 2026'">Q4 2026</option>
                                <option value="Tender APBD/APBN 2027" :selected="editOppData && editOppData.target_timeline_type === 'Tender APBD/APBN 2027'">Tender APBD/APBN 2027</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">Kebutuhan Teknis Awal</label>
                        <textarea name="initial_requirement" rows="3" class="w-full px-3.5 py-2.5 bg-white border border-[#CBD5E1] rounded-xl text-[12.5px] text-[#1E293B] focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition resize-none shadow-2xs" placeholder="Ringkasan kebutuhan spesifikasi..." x-text="editOppData ? editOppData.initial_requirement : ''"></textarea>
                    </div>
                </form>
            </div>

            {{-- Footer --}}
            <div class="flex items-center justify-end gap-2.5 p-4 px-6 border-t border-[#E2E8F0] bg-[#F8FAFC] shrink-0">
                <button type="button" 
                        @click="isEditOppModalOpen = false" 
                        class="px-4 py-2.5 text-[12px] font-bold text-[#475569] hover:bg-[#F1F5F9] border border-[#CBD5E1] rounded-xl transition cursor-pointer bg-white">
                    Batal
                </button>
                <button type="submit" 
                        form="editOppForm" 
                        class="btn-ipnet-primary px-5 py-2.5 text-[12px] font-bold rounded-xl shadow-md transition cursor-pointer">
                    Simpan Perubahan
                </button>
            </div>

        </div>
    </div>

    {{-- MODAL KONFIRMASI HAPUS PELUANG (STANDARD LEAD & MAINTENANCE SOP TEMPLATE) --}}
    <div x-show="isDeleteModalOpen" 
         x-cloak 
         class="fixed inset-0 z-[99999] bg-[#0F172A]/60 backdrop-blur-xs flex items-center justify-center p-4"
         @click.self="isDeleteModalOpen = false">
        <div class="bg-white rounded-2xl w-[440px] max-w-full overflow-hidden shadow-2xl border border-[#E2E8F0] my-auto anim-fade-up p-6 text-center space-y-4">
            
            {{-- Warning Icon Badge --}}
            <div class="w-12 h-12 mx-auto rounded-2xl bg-red-50 border border-red-100 flex items-center justify-center text-[#8F0A0D] shadow-xs">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </div>

            <div>
                <h3 class="text-[17px] font-bold text-[#1E293B]">Hapus Peluang Bisnis?</h3>
                <p class="text-[12.5px] text-[#64748B] mt-1.5 leading-relaxed">
                    Apakah Anda yakin ingin menghapus peluang <strong class="text-[#1E293B] font-bold" x-text="deleteOppData ? deleteOppData.name : ''"></strong>? Tindakan ini tidak dapat dibatalkan.
                </p>
            </div>

            {{-- Summary card --}}
            <template x-if="deleteOppData">
                <div class="p-3.5 bg-[#F8FAFC] rounded-xl border border-[#E2E8F0] text-left text-[12px] space-y-1.5 text-[#64748B]">
                    <div class="flex justify-between">
                        <span class="text-[#64748B] font-medium">Klien / Instansi:</span>
                        <span class="font-bold text-[#1E293B]" x-text="deleteOppData.client || '—'"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-[#64748B] font-medium">Nilai Kontrak:</span>
                        <span class="font-bold text-[#8F0A0D]" x-text="'Rp ' + Number(deleteOppData.contract_value || 0).toLocaleString('id-ID')"></span>
                    </div>
                </div>
            </template>

            {{-- Action Buttons --}}
            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" 
                        @click="isDeleteModalOpen = false" 
                        class="flex-1 min-h-[38px] bg-white hover:bg-[#F1F5F9] text-[#475569] border border-[#CBD5E1] shadow-2xs px-4 py-2 rounded-xl font-bold text-[12.5px] transition cursor-pointer">
                    Batal
                </button>

                <form :action="deleteOppData ? '{{ url('/bdm/opportunities') }}/' + deleteOppData.id : '#'" method="POST" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="w-full min-h-[38px] bg-[#8F0A0D] hover:bg-[#73080A] text-white shadow-md px-4 py-2 rounded-xl font-bold text-[12.5px] transition cursor-pointer">
                        Ya, Hapus
                    </button>
                </form>
            </div>

        </div>
    </div>

    {{-- MODAL DETAIL PELUANG BISNIS & HANDOVER (STANDARD LEAD & MAINTENANCE SOP TEMPLATE) --}}
    <div x-show="isDetailModalOpen" 
         x-cloak 
         class="fixed inset-0 z-[99999] bg-[#0F172A]/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-5"
         @click.self="isDetailModalOpen = false">
        <div class="bg-white rounded-2xl w-[720px] max-w-full max-h-[90vh] flex flex-col overflow-hidden shadow-2xl border border-[#E2E8F0] my-auto anim-fade-up">
            
            {{-- Modal Header --}}
            <div class="flex items-center justify-between border-b border-[#E2E8F0] p-5 sm:p-6 pb-4 shrink-0 bg-white">
                <div>
                    <div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-red-50 text-[#8F0A0D] text-[10.5px] font-bold uppercase tracking-wider mb-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#8F0A0D]"></span>
                        <span>Detail Peluang &amp; Handover SOP</span>
                    </div>
                    <h3 class="text-[17px] font-bold text-[#1E293B]" x-text="selectedOpp ? selectedOpp.name : 'Detail Peluang'"></h3>
                </div>
                <button type="button" @click="isDetailModalOpen = false" class="text-[#94A3B8] hover:text-[#1E293B] p-1.5 rounded-lg hover:bg-[#F1F5F9] transition cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Modal Body --}}
            <div class="p-5 sm:p-6 overflow-y-auto flex-1 space-y-4 text-[12.5px]" x-show="selectedOpp">
                
                {{-- Context Summary Card --}}
                <div class="p-3.5 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0]">
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <div>
                            <span class="text-[11px] text-[#64748B] block font-medium">Klien / Instansi:</span>
                            <span class="font-bold text-[#1E293B] text-[13px]" x-text="selectedOpp ? selectedOpp.client : '—'"></span>
                        </div>
                        <div>
                            <span class="text-[11px] text-[#64748B] block font-medium">Estimasi Nilai:</span>
                            <span class="font-bold text-[#8F0A0D] text-[13px]" x-text="selectedOpp && selectedOpp.contract_value ? 'Rp ' + Number(selectedOpp.contract_value).toLocaleString('id-ID') : '—'"></span>
                        </div>
                        <div>
                            <span class="text-[11px] text-[#64748B] block font-medium">Timeline:</span>
                            <span class="inline-flex px-2 py-0.5 rounded text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200 mt-0.5" x-text="selectedOpp ? (selectedOpp.target_timeline_type || 'Q3 2026') : '—'"></span>
                        </div>
                        <div>
                            <span class="text-[11px] text-[#64748B] block font-medium">Status Handover:</span>
                            <span class="inline-flex px-2 py-0.5 rounded text-[11px] font-bold mt-0.5"
                                  :class="selectedOpp && selectedOpp.bdm_handover_status === 'Handed Over to Sales' ? 'bg-blue-50 text-blue-700 border border-blue-200' : (selectedOpp && selectedOpp.bdm_handover_status === 'Accepted by Sales' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200')"
                                  x-text="selectedOpp && selectedOpp.bdm_handover_status ? selectedOpp.bdm_handover_status : 'Draft Inisiasi'"></span>
                        </div>
                    </div>
                </div>

                {{-- 1. Kebutuhan Bisnis --}}
                <div class="space-y-1">
                    <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px]">
                        1. Ringkasan Kebutuhan Bisnis Klien
                    </label>
                    <div class="p-3 bg-white border border-[#CBD5E1] rounded-xl text-[12.5px] text-[#1E293B] leading-relaxed whitespace-pre-line"
                         x-text="selectedOpp && selectedOpp.business_need_summary ? selectedOpp.business_need_summary : 'Belum diisi deskripsi kebutuhan bisnis.'"></div>
                </div>

                {{-- 2. Kontak Stakeholder --}}
                <div class="space-y-1">
                    <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px]">
                        2. Kontak PIC &amp; Stakeholder Klien
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 p-3.5 bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl">
                        <div>
                            <span class="text-[11px] text-[#64748B] block">Nama PIC &amp; Jabatan:</span>
                            <span class="font-bold text-[#1E293B]" x-text="selectedOpp && selectedOpp.stakeholders_data ? selectedOpp.stakeholders_data.pic_name + ' (' + (selectedOpp.stakeholders_data.pic_role || '-') + ')' : '-'"></span>
                        </div>
                        <div>
                            <span class="text-[11px] text-[#64748B] block">Kontak WhatsApp / Email:</span>
                            <span class="font-medium text-[#1E293B]" x-text="selectedOpp && selectedOpp.stakeholders_data ? selectedOpp.stakeholders_data.pic_phone + ' • ' + (selectedOpp.stakeholders_data.pic_email || '-') : '-'"></span>
                        </div>
                    </div>
                </div>

                {{-- 3. Analisis Teknis & Mitra --}}
                <div class="space-y-1">
                    <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px]">
                        3. Kebutuhan Teknis, Kompetitor &amp; Mitra
                    </label>
                    <div class="p-3.5 bg-white border border-[#CBD5E1] rounded-xl space-y-2">
                        <div>
                            <span class="text-[11px] text-[#64748B] block font-medium">Kebutuhan Teknis Awal:</span>
                            <div class="text-[#1E293B]" x-text="selectedOpp && selectedOpp.initial_requirement ? selectedOpp.initial_requirement : 'Standard Solution'"></div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 pt-1.5 border-t border-[#F1F5F9]">
                            <div>
                                <span class="text-[11px] text-[#64748B] block font-medium">Potensi Kompetitor:</span>
                                <span class="font-bold text-[#1E293B]" x-text="selectedOpp && selectedOpp.competitor_analysis ? selectedOpp.competitor_analysis : 'Belum teridentifikasi'"></span>
                            </div>
                            <div>
                                <span class="text-[11px] text-[#64748B] block font-medium">Rekomendasi Mitra:</span>
                                <span class="font-bold text-[#8F0A0D]" x-text="selectedOpp && selectedOpp.partner_alignment ? selectedOpp.partner_alignment : 'Direct / Multi-vendor'"></span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 4. Penugasan Sales PIC --}}
                <div class="space-y-1">
                    <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px]">
                        4. Penugasan Sales PIC &amp; Penilaian
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 p-3.5 bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl">
                        <div>
                            <span class="text-[11px] text-[#64748B] block">Sales PIC Resmi:</span>
                            <span class="font-bold text-[#1E293B]" x-text="selectedOpp && selectedOpp.sales_name ? selectedOpp.sales_name : 'Belum ditugaskan'"></span>
                        </div>
                        <div>
                            <span class="text-[11px] text-[#64748B] block">Skor Kelayakan:</span>
                            <span class="font-bold text-[#8F0A0D]" x-text="selectedOpp && selectedOpp.bd_assessment_score ? selectedOpp.bd_assessment_score + '/100 Feasible' : '80/100'"></span>
                        </div>
                        <div>
                            <span class="text-[11px] text-[#64748B] block">Dokumen Lampiran:</span>
                            <template x-if="selectedOpp && selectedOpp.handover_document_file">
                                <a :href="'/storage/' + selectedOpp.handover_document_file" target="_blank" class="text-blue-600 font-bold hover:underline inline-flex items-center gap-1">
                                    <span>Unduh File</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                </a>
                            </template>
                            <template x-if="!selectedOpp || !selectedOpp.handover_document_file">
                                <span class="text-[#94A3B8]">Tidak ada lampiran</span>
                            </template>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Modal Footer --}}
            <div class="flex items-center justify-between p-4 px-6 border-t border-[#E2E8F0] bg-[#F8FAFC] shrink-0">
                <div>
                    <template x-if="selectedOpp">
                        <a :href="'/projects/' + selectedOpp.id" class="text-[12px] font-bold text-[#8F0A0D] hover:underline inline-flex items-center gap-1">
                            <span>Buka Halaman Proyek Penuh</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                    </template>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" 
                            @click="isDetailModalOpen = false" 
                            class="px-4 py-2.5 text-[12px] font-bold text-[#475569] hover:bg-[#F1F5F9] border border-[#CBD5E1] rounded-xl transition cursor-pointer bg-white">
                        Tutup
                    </button>
                    <template x-if="selectedOpp && (!selectedOpp.bdm_handover_status || selectedOpp.bdm_handover_status === 'Draft')">
                        <button type="button" 
                                @click="isDetailModalOpen = false; openHandoverModal(selectedOpp.id)" 
                                class="btn-ipnet-primary px-4 py-2.5 text-[12px] font-bold rounded-xl shadow-md transition cursor-pointer flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                            <span>Proses Serah Terima</span>
                        </button>
                    </template>
                </div>
            </div>

        </div>
    </div>

</div>
</div>

</div>
@endsection

@push('scripts')
<script>
    function opportunityPortal() {
        return {
            isHandoverModalOpen: false,
            isNewOppModalOpen: false,
            isEditOppModalOpen: false,
            isDeleteModalOpen: false,
            isDetailModalOpen: false,
            activeOpp: null,
            selectedOpp: null,
            editOppData: null,
            deleteOppData: null,
            opps: @json($opportunities->items()),

            openHandoverModal(id) {
                this.activeOpp = this.opps.find(o => o.id == id) || null;
                this.isHandoverModalOpen = true;
            },

            openDetailModal(id) {
                this.selectedOpp = this.opps.find(o => o.id == id) || null;
                this.isDetailModalOpen = true;
            },

            openEditModal(id) {
                this.editOppData = this.opps.find(o => o.id == id) || null;
                this.isEditOppModalOpen = true;
            },

            openDeleteModal(id) {
                this.deleteOppData = this.opps.find(o => o.id == id) || null;
                this.isDeleteModalOpen = true;
            }
        };
    }
</script>
@endpush
