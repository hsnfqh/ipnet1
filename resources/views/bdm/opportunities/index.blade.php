@extends('layouts.app')

@section('title', 'Inisiasi Peluang - BDM Portal')

@section('content')
<div class="flex h-screen overflow-hidden" x-data="opportunityPortal()">
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto bg-[#FAF9F8]">
        @include('components.topbar', ['title' => 'Inisiasi Peluang'])
        
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
                <form method="GET" action="{{ route('bdm.opportunities.index') }}" class="flex flex-col sm:flex-row flex-wrap gap-2.5 w-full sm:w-auto">
                    <div class="relative">
                        <svg class="w-3.5 h-3.5 absolute left-3 top-3 text-[#948F99]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" 
                               name="search" 
                               value="{{ $search }}"
                               placeholder="Cari peluang, klien, sumber..." 
                               class="w-full sm:w-64 px-[11px] py-[9px] pl-8 rounded-lg border border-[#E7E5E3] text-[13.5px] text-[#17151C] outline-none bg-white focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all">
                    </div>
                    <select name="status" onchange="this.form.submit()" 
                            class="w-full sm:w-48 px-[11px] py-[9px] rounded-lg border border-[#E7E5E3] text-[13.5px] text-[#17151C] outline-none bg-white focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all cursor-pointer">
                        <option value="">Semua Status Handover</option>
                        <option value="draft" {{ $filterStatus === 'draft' ? 'selected' : '' }}>Draft (Belum Serah Terima)</option>
                        <option value="handed_over" {{ $filterStatus === 'handed_over' ? 'selected' : '' }}>Diserahkan ke Sales</option>
                        <option value="accepted" {{ $filterStatus === 'accepted' ? 'selected' : '' }}>Diterima Sales</option>
                    </select>
                    @if($search || $filterStatus)
                        <a href="{{ route('bdm.opportunities.index') }}" 
                           class="px-3 py-2 text-[13px] font-semibold text-gray-500 hover:text-gray-700 self-center">
                            Reset
                        </a>
                    @endif
                </form>

                <button type="button" @click="isNewOppModalOpen = true" 
                        class="w-full sm:w-auto justify-center bg-[#C81E2C] text-white shadow-[0_8px_20px_rgba(200,30,44,0.24)] px-[17px] py-[9px] rounded-lg font-semibold text-[13.5px] flex items-center gap-1.5 hover:brightness-105 active:translate-y-[1px] transition-all cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Inisiasi Peluang Baru</span>
                </button>
            </div>

            <!-- Table Container -->
            <div class="bg-white rounded-xl border border-[#E7E5E3] shadow-[0_1px_2px_rgba(14,13,18,0.05)] overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[1100px] border-collapse text-[13px]">
                        <thead>
                            <tr class="bg-[#F1F0EE] border-b border-[#E7E5E3]">
                                <th class="text-left py-3.5 px-4 text-[11px] font-semibold text-[#75727C] uppercase tracking-[0.3px] w-[30%]">Peluang & Target</th>
                                <th class="text-left py-3.5 px-4 text-[11px] font-semibold text-[#75727C] uppercase tracking-[0.3px] w-[18%]">Klien & Sumber</th>
                                <th class="text-right py-3.5 px-4 text-[11px] font-semibold text-[#75727C] uppercase tracking-[0.3px] w-[14%] whitespace-nowrap">Estimasi Nilai</th>
                                <th class="text-center py-3.5 px-3 text-[11px] font-semibold text-[#75727C] uppercase tracking-[0.3px] w-[10%] whitespace-nowrap">Kelayakan BD</th>
                                <th class="text-center py-3.5 px-3 text-[11px] font-semibold text-[#75727C] uppercase tracking-[0.3px] w-[13%] whitespace-nowrap">Status Handover</th>
                                <th class="text-right py-3.5 px-4 text-[11px] font-semibold text-[#75727C] uppercase tracking-[0.3px] w-[15%] whitespace-nowrap">Aksi SOP</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#EFEDEB]">
                            @forelse($opportunities as $opp)
                                @php
                                    $score = $opp->bd_assessment_score ?: 80;
                                    $scoreBadge = $score >= 85 ? 'text-emerald-700 bg-emerald-50 border-emerald-200' : ($score >= 70 ? 'text-blue-700 bg-blue-50 border-blue-200' : 'text-amber-700 bg-amber-50 border-amber-200');
                                @endphp
                                <tr class="hover:bg-[#F8F7F6] transition-colors">
                                    {{-- 1. Nama Peluang & Target --}}
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-[#17151C] text-[13.5px] leading-snug">{{ $opp->name }}</div>
                                        <div class="text-[11px] text-[#75727C] mt-1 flex items-center gap-1.5 flex-wrap">
                                            <span class="px-1.5 py-0.5 rounded bg-gray-100 text-gray-600 font-mono text-[10.5px]">#PRJ-{{ str_pad($opp->id, 4, '0', STR_PAD_LEFT) }}</span>
                                            <span class="text-gray-300">•</span>
                                            <span>Timeline: <strong class="text-gray-800 font-semibold">{{ $opp->target_timeline_type ?: 'Q3 2026' }}</strong></span>
                                        </div>
                                    </td>

                                    {{-- 2. Klien & Sumber --}}
                                    <td class="py-3.5 px-4">
                                        <div class="font-semibold text-gray-900 text-[13px]">{{ $opp->client ?: 'Calon Klien' }}</div>
                                        <div class="text-[11px] text-[#75727C] mt-0.5 truncate">{{ $opp->opportunity_source ?: 'Direct Inbound' }}</div>
                                    </td>

                                    {{-- 3. Estimasi Nilai --}}
                                    <td class="py-3.5 px-4 text-right whitespace-nowrap font-bold text-[#17151C]">
                                        Rp {{ number_format($opp->contract_value ?? 0, 0, ',', '.') }}
                                    </td>

                                    {{-- 4. Skor Kelayakan --}}
                                    <td class="py-3.5 px-3 text-center whitespace-nowrap">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10.5px] font-bold border {{ $scoreBadge }}">
                                            {{ $score }}/100 Feasible
                                        </span>
                                    </td>

                                    {{-- 5. Status Handover --}}
                                    <td class="py-3.5 px-3 text-center whitespace-nowrap">
                                        @if($opp->bdm_handover_status === 'Handed Over to Sales')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[10.5px] font-bold text-blue-700 bg-blue-50 border border-blue-200 rounded-md" title="Diserahkan ke {{ $opp->sales_name }}">
                                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                                <span>Diserahkan: {{ Str::limit($opp->sales_name ?: 'Sales', 14) }}</span>
                                            </span>
                                        @elseif($opp->bdm_handover_status === 'Accepted by Sales')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[10.5px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-md">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                <span>Diterima Sales</span>
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[10.5px] font-semibold text-amber-700 bg-amber-50 border border-amber-200 rounded-md">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                                                <span>Draft Inisiasi</span>
                                            </span>
                                        @endif
                                    </td>

                                    {{-- 6. Aksi SOP --}}
                                    <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                        <div class="flex items-center justify-end gap-1">
                                            {{-- Tombol Utama Handover --}}
                                            <button type="button" 
                                                    @click="openHandoverModal({{ $opp->id }})"
                                                    class="px-2.5 py-1 bg-[#C81E2C] hover:bg-[#AF1424] text-white text-[11px] font-semibold rounded-lg shadow-xs transition inline-flex items-center gap-1 cursor-pointer"
                                                    title="Proses Serah Terima SOP ke Sales">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                                <span>{{ $opp->bdm_handover_status && $opp->bdm_handover_status !== 'Draft' ? 'Handover' : 'Serah Terima' }}</span>
                                            </button>

                                            {{-- Group Tombol Penunjang --}}
                                            <button type="button" 
                                                    @click="openEditModal({{ $opp->id }})"
                                                    class="p-1.5 bg-white hover:bg-gray-100 text-gray-600 hover:text-gray-900 rounded-lg border border-gray-200 shadow-xs transition cursor-pointer"
                                                    title="Edit Inisiasi Peluang">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </button>

                                            <a href="{{ route('projects.show', $opp->id) }}" 
                                               class="p-1.5 bg-white hover:bg-gray-100 text-gray-600 hover:text-gray-900 rounded-lg border border-gray-200 shadow-xs transition inline-flex items-center"
                                               title="Lihat Detail Proyek">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            </a>

                                            <button type="button" 
                                                    @click="openDeleteModal({{ $opp->id }})"
                                                    class="p-1.5 bg-white hover:bg-red-50 text-red-500 hover:text-red-700 rounded-lg border border-red-200 shadow-xs transition cursor-pointer"
                                                    title="Hapus Peluang">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-10 text-center text-gray-400 text-xs">
                                        Belum ada peluang bisnis yang sesuai dengan filter pencarian.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($opportunities->hasPages())
                    <div class="p-3.5 bg-[#FAF9F8] border-t border-[#E7E5E3] flex items-center justify-between text-xs">
                        <div class="text-gray-500 font-medium">
                            Menampilkan <span class="font-bold text-gray-800">{{ $opportunities->firstItem() ?? 0 }}</span> - <span class="font-bold text-gray-800">{{ $opportunities->lastItem() ?? 0 }}</span> dari <span class="font-bold text-gray-800">{{ $opportunities->total() }}</span> peluang
                        </div>
                        <div>
                            {{ $opportunities->appends(request()->query())->links() }}
                        </div>
                    </div>
                @endif
            </div>

        </div>
    </div>

    {{-- MODAL SERAH TERIMA PELUANG KE SALES (11 PARAMETERS) --}}
    <div x-show="isHandoverModalOpen" 
         x-cloak 
         class="fixed inset-0 z-50 bg-[#0E0D12]/60 flex items-center justify-center p-3 sm:p-5 backdrop-blur-xs"
         @click.self="isHandoverModalOpen = false">
        <div class="bg-white rounded-2xl w-[720px] max-w-full max-h-[85vh] flex flex-col overflow-hidden shadow-[0_20px_60px_rgba(14,13,18,0.2)] border border-[#E7E5E3] my-auto animate-fade-in-up">
            
            {{-- Modal Header (Fixed) --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-[#E7E5E3] flex-shrink-0 bg-white">
                <div>
                    <h3 class="font-display text-[17px] font-bold text-[#17151C]">Serah Terima Peluang ke Tim Sales</h3>
                    <p class="text-[12px] text-[#75727C] mt-0.5">Lengkapi 11 parameter Handover SOP sebelum dilanjutkan ke tahap penawaran & SOW.</p>
                </div>
                <button type="button" @click="isHandoverModalOpen = false" class="rounded-lg p-1.5 text-[#75727C] hover:text-[#17151C] hover:bg-[#F1F0EE] transition-colors cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Modal Body (Scrollable) --}}
            <div class="px-6 py-4 overflow-y-auto flex-1">
                <form id="handoverForm"
                      :action="'/bdm/opportunities/' + (activeOpp ? activeOpp.id : '') + '/handover'" 
                      method="POST" 
                      enctype="multipart/form-data" 
                      class="space-y-3.5 text-xs">
                    @csrf

                    {{-- Section 1: Customer & Business Need --}}
                    <div class="bg-[#FAF9F8] p-3.5 rounded-xl border border-[#E7E5E3] space-y-2.5">
                        <div class="font-bold text-[#17151C] text-[12.5px] flex items-center gap-2">
                            <span class="w-4.5 h-4.5 rounded-full bg-[#C81E2C] text-white flex items-center justify-center text-[10px] font-bold">1</span>
                            <span>Informasi Peluang & Kebutuhan Bisnis Klien</span>
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                            <div>
                                <label class="block text-[11px] font-semibold text-[#75727C] mb-1">Nama Peluang / Proyek</label>
                                <input type="text" :value="activeOpp ? activeOpp.name : ''" disabled 
                                       class="w-full bg-[#F1F0EE] border border-[#E7E5E3] rounded-lg px-3 py-1.5 text-[12.5px] font-semibold text-[#17151C]">
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-[#75727C] mb-1">Nama Klien / Instansi</label>
                                <input type="text" :value="activeOpp ? activeOpp.client : ''" disabled 
                                       class="w-full bg-[#F1F0EE] border border-[#E7E5E3] rounded-lg px-3 py-1.5 text-[12.5px] font-semibold text-[#17151C]">
                            </div>
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-[#17151C] mb-1">
                                Ringkasan Kebutuhan Bisnis Klien <span class="text-[#C81E2C]">*</span>
                            </label>
                            <textarea name="business_need_summary" rows="2.5" required
                                      class="w-full bg-white border border-[#E7E5E3] rounded-lg p-2.5 text-[12.5px] text-[#17151C] outline-none focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all"
                                      placeholder="Deskripsikan latar belakang pengadaan, problem utama, dan kebutuhan klien..."
                                      x-text="activeOpp ? activeOpp.business_need_summary : ''"></textarea>
                        </div>
                    </div>

                    {{-- Section 2: Stakeholder Klien --}}
                    <div class="bg-[#FAF9F8] p-3.5 rounded-xl border border-[#E7E5E3] space-y-2.5">
                        <div class="font-bold text-[#17151C] text-[12.5px] flex items-center gap-2">
                            <span class="w-4.5 h-4.5 rounded-full bg-[#C81E2C] text-white flex items-center justify-center text-[10px] font-bold">2</span>
                            <span>Kontak PIC & Stakeholder Klien</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-2">
                            <div>
                                <label class="block text-[10.5px] font-semibold text-[#75727C] mb-1">Nama PIC</label>
                                <input type="text" name="pic_name" :value="activeOpp && activeOpp.stakeholders_data ? activeOpp.stakeholders_data.pic_name : ''"
                                       class="w-full bg-white border border-[#E7E5E3] rounded-lg px-2.5 py-1.5 text-[12px] text-[#17151C] outline-none focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all" placeholder="Nama lengkap">
                            </div>
                            <div>
                                <label class="block text-[10.5px] font-semibold text-[#75727C] mb-1">Jabatan / Peran</label>
                                <input type="text" name="pic_role" :value="activeOpp && activeOpp.stakeholders_data ? activeOpp.stakeholders_data.pic_role : ''"
                                       class="w-full bg-white border border-[#E7E5E3] rounded-lg px-2.5 py-1.5 text-[12px] text-[#17151C] outline-none focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all" placeholder="Kepala IT / Jaringan">
                            </div>
                            <div>
                                <label class="block text-[10.5px] font-semibold text-[#75727C] mb-1">No. WhatsApp / HP</label>
                                <input type="text" name="pic_phone" :value="activeOpp && activeOpp.stakeholders_data ? activeOpp.stakeholders_data.pic_phone : ''"
                                       class="w-full bg-white border border-[#E7E5E3] rounded-lg px-2.5 py-1.5 text-[12px] text-[#17151C] outline-none focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all" placeholder="0812xxxxxxxx">
                            </div>
                            <div>
                                <label class="block text-[10.5px] font-semibold text-[#75727C] mb-1">Email</label>
                                <input type="email" name="pic_email" :value="activeOpp && activeOpp.stakeholders_data ? activeOpp.stakeholders_data.pic_email : ''"
                                       class="w-full bg-white border border-[#E7E5E3] rounded-lg px-2.5 py-1.5 text-[12px] text-[#17151C] outline-none focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all" placeholder="pic@instansi.go.id">
                            </div>
                        </div>
                    </div>

                    {{-- Section 3: Technical Requirement, Competitor & Partner --}}
                    <div class="bg-[#FAF9F8] p-3.5 rounded-xl border border-[#E7E5E3] space-y-2.5">
                        <div class="font-bold text-[#17151C] text-[12.5px] flex items-center gap-2">
                            <span class="w-4.5 h-4.5 rounded-full bg-[#C81E2C] text-white flex items-center justify-center text-[10px] font-bold">3</span>
                            <span>Kebutuhan Teknis, Analisis Kompetitor & Mitra</span>
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-[#17151C] mb-1">Kebutuhan Teknis Awal</label>
                            <textarea name="initial_requirement" rows="2" 
                                      class="w-full bg-white border border-[#E7E5E3] rounded-lg p-2 text-[12px] text-[#17151C] outline-none focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all"
                                      placeholder="Spesifikasi hardware, software, perkiraan port/access point, atau scope layanan..."
                                      x-text="activeOpp ? activeOpp.initial_requirement : ''"></textarea>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                            <div>
                                <label class="block text-[11px] font-semibold text-[#17151C] mb-1">Analisis Kompetitor</label>
                                <input type="text" name="competitor_analysis" :value="activeOpp ? activeOpp.competitor_analysis : ''"
                                       class="w-full bg-white border border-[#E7E5E3] rounded-lg px-3 py-1.5 text-[12px] text-[#17151C] outline-none focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all"
                                       placeholder="Kompetitor mengajukan brand Cisco / Fortinet...">
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-[#17151C] mb-1">Mitra Prinsipal Pendukung</label>
                                <input type="text" name="partner_alignment" :value="activeOpp ? activeOpp.partner_alignment : ''"
                                       class="w-full bg-white border border-[#E7E5E3] rounded-lg px-3 py-1.5 text-[12px] text-[#17151C] outline-none focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all"
                                       placeholder="Fortinet / Cisco / Aruba / Sangfor...">
                            </div>
                        </div>
                    </div>

                    {{-- Section 4: Penugasan Tim Sales & Feasibility --}}
                    <div class="bg-[#FAF9F8] p-3.5 rounded-xl border border-[#E7E5E3] space-y-2.5">
                        <div class="font-bold text-[#17151C] text-[12.5px] flex items-center gap-2">
                            <span class="w-4.5 h-4.5 rounded-full bg-[#C81E2C] text-white flex items-center justify-center text-[10px] font-bold">4</span>
                            <span>Penugasan ke Tim Sales & SOP Handover</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                            <div>
                                <label class="block text-[11px] font-semibold text-[#17151C] mb-1">
                                    Pilih Sales PIC <span class="text-[#C81E2C]">*</span>
                                </label>
                                <select name="sales_name" required
                                        class="w-full bg-white border border-[#E7E5E3] rounded-lg px-2.5 py-1.5 text-[12px] font-semibold text-[#17151C] outline-none focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all cursor-pointer">
                                    <option value="">-- Pilih Sales --</option>
                                    @if(isset($salesUsers))
                                        @foreach($salesUsers as $sales)
                                            <option value="{{ $sales->name }}" :selected="activeOpp && activeOpp.sales_name === '{{ $sales->name }}'">
                                                {{ $sales->name }} (Sales / Account Manager)
                                            </option>
                                        @endforeach
                                    @elseif(isset($salesTeamNames))
                                        @foreach($salesTeamNames as $sName)
                                            <option value="{{ $sName }}" :selected="activeOpp && activeOpp.sales_name === '{{ $sName }}'">{{ $sName }} (Sales / Account Manager)</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            <div>
                                <label class="block text-[11px] font-semibold text-[#17151C] mb-1">Target Timeline <span class="text-[#C81E2C]">*</span></label>
                                <select name="target_timeline_type" required
                                        class="w-full bg-white border border-[#E7E5E3] rounded-lg px-2.5 py-1.5 text-[12px] font-semibold text-[#17151C] outline-none focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all cursor-pointer">
                                    <option value="Urgent (< 1 Bulan)">Urgent (< 1 Bulan)</option>
                                    <option value="Q3 2026" selected>Q3 2026</option>
                                    <option value="Q4 2026">Q4 2026</option>
                                    <option value="Tender APBD/APBN 2027">Tender APBD/APBN 2027</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-[11px] font-semibold text-[#17151C] mb-1">Skor Kelayakan BD (1-100) <span class="text-[#C81E2C]">*</span></label>
                                <input type="number" name="bd_assessment_score" min="1" max="100" required
                                       :value="activeOpp ? (activeOpp.bd_assessment_score || 85) : 85"
                                       class="w-full bg-white border border-[#E7E5E3] rounded-lg px-2.5 py-1.5 text-[12px] font-bold text-[#C81E2C] outline-none focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all">
                            </div>
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold text-[#17151C] mb-1">Upload TOR / KAK / Dokumen (Opsional)</label>
                            <input type="file" name="handover_document_file" 
                                   class="w-full bg-white border border-[#E7E5E3] rounded-lg p-1 text-[11.5px] text-gray-600 file:mr-2.5 file:py-1 file:px-2.5 file:rounded-md file:border-0 file:text-[11px] file:font-semibold file:bg-[#FDF1F2] file:text-[#C81E2C] hover:file:bg-red-100 cursor-pointer">
                        </div>
                    </div>

                </form>
            </div>

            {{-- Modal Footer (Fixed) --}}
            <div class="flex items-center gap-3 px-6 py-3.5 border-t border-[#E7E5E3] bg-[#FAF9F8] flex-shrink-0">
                <button type="submit" 
                        form="handoverForm" 
                        class="flex-1 flex items-center justify-center min-h-[40px] bg-[#C81E2C] hover:bg-[#AF1424] text-white shadow-[0_8px_20px_rgba(200,30,44,0.24)] px-4 py-2 rounded-xl font-semibold text-[13px] active:translate-y-[1px] transition-all cursor-pointer">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>Kirim Berkas Serah Terima</span>
                </button>
                <button type="button" 
                        @click="isHandoverModalOpen = false" 
                        class="flex-1 flex items-center justify-center min-h-[40px] bg-white hover:bg-[#F8F7F6] text-[#3D3A44] border border-[#E7E5E3] shadow-[0_1px_2px_rgba(14,13,18,0.05)] px-4 py-2 rounded-xl font-semibold text-[13px] active:translate-y-[1px] transition-all cursor-pointer">
                    Batal
                </button>
            </div>

        </div>
    </div>

    {{-- MODAL INPUT PELUANG BISNIS BARU --}}
    <div x-show="isNewOppModalOpen" 
         x-cloak 
         class="fixed inset-0 z-50 bg-[#0E0D12]/60 flex items-center justify-center p-3 sm:p-5 backdrop-blur-xs"
         @click.self="isNewOppModalOpen = false">
        <div class="bg-white rounded-2xl w-[600px] max-w-full max-h-[85vh] flex flex-col overflow-hidden shadow-[0_20px_60px_rgba(14,13,18,0.2)] border border-[#E7E5E3] my-auto animate-fade-in-up">
            
            {{-- Header (Fixed) --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-[#E7E5E3] flex-shrink-0 bg-white">
                <div>
                    <h3 class="font-display text-[17px] font-bold text-[#17151C]">Inisiasi Peluang Bisnis Baru</h3>
                    <p class="text-[12px] text-[#75727C] mt-0.5">Daftarkan prospek tender atau peluang proyek ke pipeline BD.</p>
                </div>
                <button type="button" @click="isNewOppModalOpen = false" class="rounded-lg p-1.5 text-[#75727C] hover:text-[#17151C] hover:bg-[#F1F0EE] transition-colors cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Body (Scrollable) --}}
            <div class="px-6 py-4 overflow-y-auto flex-1">
                <form id="newOppForm" action="{{ route('bdm.opportunity.store') }}" method="POST" class="space-y-3.5 text-xs">
                    @csrf
                    <div>
                        <label class="block text-[12px] font-semibold text-[#17151C] mb-1">Nama Peluang / Proyek <span class="text-[#C81E2C]">*</span></label>
                        <input type="text" name="name" required class="w-full bg-white border border-[#E7E5E3] rounded-lg px-3 py-2 text-[13px] text-[#17151C] outline-none focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all" placeholder="e.g. Modernisasi Jaringan & SD-WAN Kemenkeu">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[12px] font-semibold text-[#17151C] mb-1">Nama Klien / Instansi <span class="text-[#C81E2C]">*</span></label>
                            <input type="text" name="client" required class="w-full bg-white border border-[#E7E5E3] rounded-lg px-3 py-2 text-[13px] text-[#17151C] outline-none focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all" placeholder="e.g. Kementerian Keuangan RI">
                        </div>
                        <div>
                            <label class="block text-[12px] font-semibold text-[#17151C] mb-1">Estimasi Nilai Kontrak (Rp) <span class="text-[#C81E2C]">*</span></label>
                            <input type="number" name="contract_value" required min="0" class="w-full bg-white border border-[#E7E5E3] rounded-lg px-3 py-2 text-[13px] font-bold text-[#C81E2C] outline-none focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all" placeholder="4500000000">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[12px] font-semibold text-[#17151C] mb-1">Sumber Peluang <span class="text-[#C81E2C]">*</span></label>
                            <select name="opportunity_source" required class="w-full bg-white border border-[#E7E5E3] rounded-lg px-3 py-2 text-[13px] text-[#17151C] outline-none focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all cursor-pointer">
                                <option value="Inbound Market Intel">Inbound Market Intel</option>
                                <option value="Gov Procurement (LPSE)">Gov Procurement (LPSE)</option>
                                <option value="Partner Referral">Partner Referral (Prinsipal)</option>
                                <option value="Direct Prospecting">Direct Prospecting</option>
                                <option value="Event / Exhibition">Event / Webinar Exhibition</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[12px] font-semibold text-[#17151C] mb-1">Target Timeline</label>
                            <select name="target_timeline_type" class="w-full bg-white border border-[#E7E5E3] rounded-lg px-3 py-2 text-[13px] text-[#17151C] outline-none focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all cursor-pointer">
                                <option value="Urgent (< 1 Bulan)">Urgent (< 1 Bulan)</option>
                                <option value="Q3 2026" selected>Q3 2026</option>
                                <option value="Q4 2026">Q4 2026</option>
                                <option value="Tender APBD/APBN 2027">Tender APBD/APBN 2027</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[12px] font-semibold text-[#17151C] mb-1">Kebutuhan Teknis Awal</label>
                        <textarea name="initial_requirement" rows="2.5" class="w-full bg-white border border-[#E7E5E3] rounded-lg p-2.5 text-[13px] text-[#17151C] outline-none focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all" placeholder="Ringkasan kebutuhan spesifikasi hardware / layanan jaringan yang dibutuhkan..."></textarea>
                    </div>
                </form>
            </div>

            {{-- Footer (Fixed) --}}
            <div class="flex items-center gap-3 px-6 py-3.5 border-t border-[#E7E5E3] bg-[#FAF9F8] flex-shrink-0">
                <button type="submit" 
                        form="newOppForm" 
                        class="flex-1 flex items-center justify-center min-h-[40px] bg-[#C81E2C] hover:bg-[#AF1424] text-white shadow-[0_8px_20px_rgba(200,30,44,0.24)] px-4 py-2 rounded-xl font-semibold text-[13px] active:translate-y-[1px] transition-all cursor-pointer">
                    Simpan Peluang
                </button>
                <button type="button" 
                        @click="isNewOppModalOpen = false" 
                        class="flex-1 flex items-center justify-center min-h-[40px] bg-white hover:bg-[#F8F7F6] text-[#3D3A44] border border-[#E7E5E3] shadow-[0_1px_2px_rgba(14,13,18,0.05)] px-4 py-2 rounded-xl font-semibold text-[13px] active:translate-y-[1px] transition-all cursor-pointer">
                    Batal
                </button>
            </div>

        </div>
    </div>

    {{-- MODAL EDIT PELUANG BISNIS --}}
    <div x-show="isEditOppModalOpen" 
         x-cloak 
         class="fixed inset-0 z-50 bg-[#0E0D12]/60 flex items-center justify-center p-3 sm:p-5 backdrop-blur-xs"
         @click.self="isEditOppModalOpen = false">
        <div class="bg-white rounded-2xl w-[600px] max-w-full max-h-[85vh] flex flex-col overflow-hidden shadow-[0_20px_60px_rgba(14,13,18,0.2)] border border-[#E7E5E3] my-auto animate-fade-in-up">
            
            {{-- Header (Fixed) --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-[#E7E5E3] flex-shrink-0 bg-white">
                <div>
                    <h3 class="font-display text-[17px] font-bold text-[#17151C]">Edit Inisiasi Peluang Bisnis</h3>
                    <p class="text-[12px] text-[#75727C] mt-0.5">Perbarui informasi prospek tender atau nilai estimasi.</p>
                </div>
                <button type="button" @click="isEditOppModalOpen = false" class="rounded-lg p-1.5 text-[#75727C] hover:text-[#17151C] hover:bg-[#F1F0EE] transition-colors cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Body (Scrollable) --}}
            <div class="px-6 py-4 overflow-y-auto flex-1">
                <form :action="editOppData ? '{{ url('/bdm/opportunities') }}/' + editOppData.id : '#'" method="POST" id="editOppForm" class="space-y-3.5 text-xs">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-[12px] font-semibold text-[#17151C] mb-1">Nama Peluang / Proyek <span class="text-[#C81E2C]">*</span></label>
                        <input type="text" name="name" :value="editOppData ? editOppData.name : ''" required class="w-full bg-white border border-[#E7E5E3] rounded-lg px-3 py-2 text-[13px] text-[#17151C] outline-none focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all" placeholder="Nama proyek">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[12px] font-semibold text-[#17151C] mb-1">Nama Klien / Instansi <span class="text-[#C81E2C]">*</span></label>
                            <input type="text" name="client" :value="editOppData ? editOppData.client : ''" required class="w-full bg-white border border-[#E7E5E3] rounded-lg px-3 py-2 text-[13px] text-[#17151C] outline-none focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all" placeholder="Nama instansi">
                        </div>
                        <div>
                            <label class="block text-[12px] font-semibold text-[#17151C] mb-1">Estimasi Nilai Kontrak (Rp) <span class="text-[#C81E2C]">*</span></label>
                            <input type="number" name="contract_value" :value="editOppData ? editOppData.contract_value : ''" required min="0" class="w-full bg-white border border-[#E7E5E3] rounded-lg px-3 py-2 text-[13px] font-bold text-[#C81E2C] outline-none focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[12px] font-semibold text-[#17151C] mb-1">Sumber Peluang <span class="text-[#C81E2C]">*</span></label>
                            <select name="opportunity_source" required class="w-full bg-white border border-[#E7E5E3] rounded-lg px-3 py-2 text-[13px] text-[#17151C] outline-none focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all cursor-pointer">
                                <option value="Inbound Market Intel" :selected="editOppData && editOppData.opportunity_source === 'Inbound Market Intel'">Inbound Market Intel</option>
                                <option value="Gov Procurement (LPSE)" :selected="editOppData && editOppData.opportunity_source === 'Gov Procurement (LPSE)'">Gov Procurement (LPSE)</option>
                                <option value="Partner Referral" :selected="editOppData && editOppData.opportunity_source === 'Partner Referral'">Partner Referral (Prinsipal)</option>
                                <option value="Direct Prospecting" :selected="editOppData && editOppData.opportunity_source === 'Direct Prospecting'">Direct Prospecting</option>
                                <option value="Event / Exhibition" :selected="editOppData && editOppData.opportunity_source === 'Event / Exhibition'">Event / Webinar Exhibition</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[12px] font-semibold text-[#17151C] mb-1">Target Timeline</label>
                            <select name="target_timeline_type" class="w-full bg-white border border-[#E7E5E3] rounded-lg px-3 py-2 text-[13px] text-[#17151C] outline-none focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all cursor-pointer">
                                <option value="Urgent (< 1 Bulan)" :selected="editOppData && editOppData.target_timeline_type === 'Urgent (< 1 Bulan)'">Urgent (< 1 Bulan)</option>
                                <option value="Q3 2026" :selected="editOppData && editOppData.target_timeline_type === 'Q3 2026'">Q3 2026</option>
                                <option value="Q4 2026" :selected="editOppData && editOppData.target_timeline_type === 'Q4 2026'">Q4 2026</option>
                                <option value="Tender APBD/APBN 2027" :selected="editOppData && editOppData.target_timeline_type === 'Tender APBD/APBN 2027'">Tender APBD/APBN 2027</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[12px] font-semibold text-[#17151C] mb-1">Kebutuhan Teknis Awal</label>
                        <textarea name="initial_requirement" rows="2.5" class="w-full bg-white border border-[#E7E5E3] rounded-lg p-2.5 text-[13px] text-[#17151C] outline-none focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all" placeholder="Ringkasan kebutuhan spesifikasi hardware / layanan..." x-text="editOppData ? editOppData.initial_requirement : ''"></textarea>
                    </div>
                </form>
            </div>

            {{-- Footer (Fixed) --}}
            <div class="flex items-center gap-3 px-6 py-3.5 border-t border-[#E7E5E3] bg-[#FAF9F8] flex-shrink-0">
                <button type="submit" 
                        form="editOppForm" 
                        class="flex-1 flex items-center justify-center min-h-[40px] bg-[#C81E2C] hover:bg-[#AF1424] text-white shadow-[0_8px_20px_rgba(200,30,44,0.24)] px-4 py-2 rounded-xl font-semibold text-[13px] active:translate-y-[1px] transition-all cursor-pointer">
                    Simpan Perubahan
                </button>
                <button type="button" 
                        @click="isEditOppModalOpen = false" 
                        class="flex-1 flex items-center justify-center min-h-[40px] bg-white hover:bg-[#F8F7F6] text-[#3D3A44] border border-[#E7E5E3] shadow-[0_1px_2px_rgba(14,13,18,0.05)] px-4 py-2 rounded-xl font-semibold text-[13px] active:translate-y-[1px] transition-all cursor-pointer">
                    Batal
                </button>
            </div>

        </div>
    </div>

    {{-- MODAL KONFIRMASI HAPUS PELUANG --}}
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
                <h3 class="font-display text-[17px] font-bold text-[#17151C]">Hapus Peluang Bisnis?</h3>
                <p class="text-[13px] text-[#75727C] mt-1.5 leading-relaxed">
                    Apakah Anda yakin ingin menghapus peluang <strong class="text-gray-900 font-semibold" x-text="deleteOppData ? deleteOppData.name : ''"></strong>? Tindakan ini tidak dapat dibatalkan.
                </p>
            </div>

            {{-- Summary card --}}
            <template x-if="deleteOppData">
                <div class="p-3 bg-[#FAF9F8] rounded-xl border border-[#E7E5E3] text-left text-xs space-y-1 text-gray-600">
                    <div class="flex justify-between">
                        <span class="text-gray-400">Klien / Instansi:</span>
                        <span class="font-bold text-gray-800" x-text="deleteOppData.client || '—'"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Nilai Kontrak:</span>
                        <span class="font-bold text-[#C81E2C]" x-text="'Rp ' + Number(deleteOppData.contract_value || 0).toLocaleString('id-ID')"></span>
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

                <form :action="deleteOppData ? '{{ url('/bdm/opportunities') }}/' + deleteOppData.id : '#'" method="POST" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="w-full min-h-[40px] bg-[#C81E2C] hover:bg-[#AF1424] text-white shadow-[0_8px_20px_rgba(200,30,44,0.24)] px-4 py-2 rounded-xl font-semibold text-[13px] transition cursor-pointer">
                        Ya, Hapus Peluang
                    </button>
                </form>
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
            activeOpp: null,
            editOppData: null,
            deleteOppData: null,
            opps: @json($opportunities->items()),

            openHandoverModal(id) {
                this.activeOpp = this.opps.find(o => o.id == id) || null;
                this.isHandoverModalOpen = true;
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
