@extends('layouts.app')

@section('title', $project->name . ' - Detail Inisiasi Peluang & Proyek')

@push('styles')
<style>
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

    .ipnet-card {
        background-color: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .ipnet-card:hover {
        border-color: #CBD5E1;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.05);
    }
</style>
@endpush

@section('content')
<div class="flex h-screen overflow-hidden bg-[#F8FAFC]" x-data="projectDocumentHub()" x-cloak>
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Detail Proyek'])
        
        <div class="p-4 sm:p-6 lg:p-7 space-y-6 max-w-[1680px] mx-auto">
            
            {{-- Breadcrumb & Top Actions --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 anim-fade-up">
                <div class="flex items-center gap-2 text-xs">
                    <button onclick="window.history.back()" 
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white hover:bg-gray-50 text-[#334155] font-semibold text-[12.5px] rounded-xl border border-[#CBD5E1] shadow-2xs transition cursor-pointer hover:border-[#94A3B8]">
                        <svg class="w-3.5 h-3.5 text-[#64748B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        <span>Kembali</span>
                    </button>
                    <span class="text-slate-300">/</span>
                    <span class="text-[#64748B] font-semibold">Proyek #{{ str_pad($project->id, 4, '0', STR_PAD_LEFT) }}</span>
                    <span class="text-slate-300">/</span>
                    <span class="text-[#1E293B] font-bold truncate max-w-xs">{{ $project->client }}</span>
                </div>

                <div class="flex items-center gap-2 flex-wrap">
                    @php
                        $statusBadge = match($project->status) {
                            'Draft'       => 'bg-slate-100 text-slate-700 border-slate-200',
                            'Opportunity' => 'bg-blue-50 text-blue-700 border-blue-200',
                            'Planning'    => 'bg-slate-100 text-slate-800 border-slate-200',
                            'On Progress' => 'bg-amber-50 text-amber-800 border-amber-200',
                            'Completed'   => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                            default       => 'bg-slate-50 text-slate-700 border-slate-200',
                        };
                    @endphp

                    <span class="inline-flex items-center px-3 py-1 text-[11.5px] font-bold rounded-lg border {{ $statusBadge }}">
                        {{ $project->status }}
                    </span>

                    @if($project->handover_document_file)
                        <a href="{{ asset('storage/' . $project->handover_document_file) }}" target="_blank" 
                           class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-[#8F0A0D] hover:bg-[#73080A] text-white text-[12px] font-bold rounded-xl shadow-xs transition cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            <span>Unduh Berkas TOR / KAK</span>
                        </a>
                    @endif
                </div>
            </div>

            {{-- Main Title Banner Card --}}
            <div class="ipnet-card p-5 sm:p-6 anim-fade-up anim-delay-1">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                    <div class="space-y-2">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-[10.5px] font-bold uppercase tracking-wider bg-red-50 text-[#8F0A0D] border border-red-100">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#8F0A0D]"></span>
                                Detail Informasi Inisiasi Peluang &amp; Proyek
                            </span>
                            <span class="px-2 py-0.5 rounded-md text-[10.5px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                {{ $project->project_type ?: 'One-Time Project' }}
                            </span>
                        </div>
                        <h1 class="text-xl sm:text-2xl font-bold text-[#1E293B] tracking-tight leading-snug">{{ $project->name }}</h1>
                        <p class="text-[12.5px] text-[#64748B] font-medium flex items-center gap-2 flex-wrap">
                            <span class="font-bold text-[#1E293B]">{{ $project->client }}</span>
                            <span>&bull;</span>
                            <span class="text-[#475569] font-semibold">{{ $project->division ? $project->division->name : 'Lintas Divisi' }}</span>
                            <span>&bull;</span>
                            <span class="text-[#94A3B8]">Dibuat {{ $project->created_at ? $project->created_at->format('d M Y') : '—' }}</span>
                        </p>
                    </div>

                    <div class="flex items-center gap-6 border-t lg:border-t-0 lg:border-l border-[#E2E8F0] pt-4 lg:pt-0 lg:pl-6 shrink-0">
                        <div>
                            <p class="text-[11px] font-bold text-[#64748B] uppercase tracking-wider">Estimasi Nilai Kontrak</p>
                            <h3 class="text-2xl font-bold text-[#1E293B] tracking-tight mt-0.5">
                                {{ $project->contract_value > 0 ? 'Rp ' . number_format($project->contract_value, 0, ',', '.') : '—' }}
                            </h3>
                        </div>
                        <div class="w-px h-12 bg-[#E2E8F0] hidden sm:block"></div>
                        <div>
                            <p class="text-[11px] font-bold text-[#64748B] uppercase tracking-wider">Inisiator BDM</p>
                            <h3 class="text-[16px] font-bold text-[#8F0A0D] tracking-tight mt-1">
                                {{ $project->bdm ? $project->bdm->name : 'Tim Business Development' }}
                            </h3>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 6-STAGE HANDOVER & DOCUMENT FLOW HUB (FROM COMMERCIAL TO OPERATION) --}}
            <div class="ipnet-card p-5 sm:p-6 space-y-6 anim-fade-up anim-delay-2">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-[#F1F5F9] pb-4">
                    <div>
                        <div class="flex items-center gap-2 text-[#8F0A0D] text-[11px] font-bold uppercase tracking-wider mb-0.5">
                            <span class="w-2 h-2 rounded-full bg-[#8F0A0D]"></span>
                            <span>Lifecycle Handover &amp; Document Flow</span>
                        </div>
                        <h2 class="text-[18px] font-bold text-[#1E293B] tracking-tight">
                            Alur Serah Terima &amp; Repositori Dokumen 6 Tahapan
                        </h2>
                        <p class="text-[12px] text-[#64748B] mt-0.5">
                            Transisi dokumen terstruktur dari inisiasi komersial hingga operasional layanan purnajual
                        </p>
                    </div>

                    <div class="flex items-center gap-2 text-xs">
                        <span class="px-2.5 py-1 rounded-lg bg-slate-100 border border-slate-200 text-slate-700 font-bold">
                            Total Berkas: <span class="text-gray-900" x-text="totalUploadedAll + '/' + totalDocsAll"></span>
                        </span>
                    </div>
                </div>

                {{-- 6 Milestone Cards Grid --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
                    <template x-for="(stg, stgNum) in stages" :key="stgNum">
                        <div @click="activeStageNumber = parseInt(stgNum)"
                             class="p-3.5 rounded-xl border-2 cursor-pointer transition-all flex flex-col justify-between space-y-3"
                             :class="activeStageNumber === parseInt(stgNum) 
                                ? 'border-[#8F0A0D] bg-red-50/20 shadow-xs ring-1 ring-[#8F0A0D]/20' 
                                : 'border-[#E2E8F0] hover:border-slate-300 bg-white'">
                            
                            {{-- Stage Number & Name --}}
                            <div>
                                <div class="flex items-center justify-between gap-1 mb-1">
                                    <span class="w-5 h-5 rounded-full text-[11px] font-black flex items-center justify-center"
                                          :class="activeStageNumber === parseInt(stgNum) ? 'bg-[#8F0A0D] text-white' : 'bg-slate-100 text-slate-700'"
                                          x-text="stgNum">
                                    </span>
                                    <span class="text-[10px] font-extrabold uppercase tracking-wider"
                                          :class="stg.is_complete ? 'text-emerald-700' : 'text-slate-400'"
                                          x-text="stg.uploaded_docs + '/' + stg.total_docs + ' Doc'">
                                    </span>
                                </div>
                                <h4 class="font-bold text-[12.5px] text-[#1E293B] leading-tight" x-text="stg.stage_name"></h4>
                                <p class="text-[10.5px] text-[#64748B] font-medium mt-0.5 line-clamp-1" x-text="stg.sub_title"></p>
                            </div>

                            {{-- Mini Progress & Gate Action --}}
                            <div>
                                <div class="w-full h-1 bg-gray-100 rounded-full overflow-hidden mb-1.5">
                                    <div class="h-full bg-[#8F0A0D] rounded-full transition-all duration-300"
                                         :style="'width: ' + stg.percentage + '%'"></div>
                                </div>
                                <div class="text-[10px] font-semibold text-[#475569] truncate" x-text="stg.gate_action"></div>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Active Stage Detail & Document Repository Card --}}
                <div class="p-4 sm:p-5 rounded-2xl bg-[#F8FAFC] border border-[#E2E8F0] space-y-4" x-show="activeStage">
                    
                    {{-- Active Stage Header --}}
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 border-b border-[#E2E8F0] pb-3.5">
                        <div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="px-2.5 py-0.5 rounded-md text-[11px] font-bold bg-[#8F0A0D] text-white">
                                    Tahap <span x-text="activeStageNumber"></span>: <span x-text="activeStage.stage_name"></span>
                                </span>
                                <span class="text-xs text-[#64748B] font-semibold" x-text="activeStage.sub_title"></span>
                            </div>
                            <p class="text-[12px] text-[#475569] mt-1" x-text="activeStage.description"></p>
                            <div class="text-[11px] text-[#64748B] mt-0.5">
                                <strong>Penanggung Jawab:</strong> <span class="text-[#1E293B] font-semibold" x-text="activeStage.owner"></span>
                            </div>
                        </div>

                        <div class="flex items-center gap-2.5 shrink-0">
                            <div class="text-right">
                                <span class="text-[10.5px] font-bold text-[#64748B] uppercase tracking-wider block">Handover Package:</span>
                                <span class="text-[12px] font-bold text-[#8F0A0D]" x-text="activeStage.handover_package"></span>
                            </div>
                        </div>
                    </div>

                    {{-- Document Table / Items List --}}
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-white text-gray-500 uppercase text-[10.5px] font-bold border-b border-[#E2E8F0]">
                                <tr>
                                    <th class="py-3 px-3.5 rounded-l-lg">No</th>
                                    <th class="py-3 px-3.5">Nama Dokumen Kunci (*Key Document*)</th>
                                    <th class="py-3 px-3.5">Ketentuan</th>
                                    <th class="py-3 px-3.5">Status Berkas</th>
                                    <th class="py-3 px-3.5">Detail Berkas Terunggah</th>
                                    <th class="py-3 px-3.5 text-right rounded-r-lg">Aksi Dokumen</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200/60 font-medium text-gray-700">
                                <template x-for="(doc, idx) in activeStage.documents" :key="doc.id || idx">
                                    <tr class="hover:bg-white/60 transition-colors">
                                        {{-- No --}}
                                        <td class="py-3 px-3.5 font-bold text-gray-400" x-text="idx + 1"></td>

                                        {{-- Title & Desc --}}
                                        <td class="py-3 px-3.5 max-w-sm">
                                            <div class="font-bold text-gray-900 text-[12.5px]" x-text="doc.document_title"></div>
                                            <div class="text-[11px] text-gray-500 font-normal mt-0.5" x-text="doc.notes || '—'"></div>
                                        </td>

                                        {{-- Mandatory --}}
                                        <td class="py-3 px-3.5 whitespace-nowrap">
                                            <span class="px-2 py-0.5 rounded text-[10.5px] font-bold"
                                                  :class="doc.is_mandatory ? 'bg-red-50 text-[#8F0A0D] border border-red-200' : 'bg-slate-100 text-slate-600 border border-slate-200'"
                                                  x-text="doc.is_mandatory ? 'Wajib' : 'Opsional'">
                                            </span>
                                        </td>

                                        {{-- Status --}}
                                        <td class="py-3 px-3.5 whitespace-nowrap">
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg text-[10.5px] font-bold border"
                                                  :class="{
                                                      'bg-emerald-50 text-emerald-700 border-emerald-200': doc.status === 'Verified',
                                                      'bg-blue-50 text-blue-700 border-blue-200': doc.status === 'Uploaded',
                                                      'bg-red-50 text-red-700 border-red-200': doc.status === 'Rejected',
                                                      'bg-slate-100 text-slate-600 border-slate-200': doc.status === 'Pending' || !doc.status
                                                  }">
                                                <span class="w-1.5 h-1.5 rounded-full"
                                                      :class="{
                                                          'bg-emerald-500': doc.status === 'Verified',
                                                          'bg-blue-500': doc.status === 'Uploaded',
                                                          'bg-red-500': doc.status === 'Rejected',
                                                          'bg-slate-400': doc.status === 'Pending' || !doc.status
                                                      }"></span>
                                                <span x-text="doc.status || 'Pending / Kosong'"></span>
                                            </span>
                                        </td>

                                        {{-- File Info --}}
                                        <td class="py-3 px-3.5">
                                            <template x-if="doc.file_path">
                                                <div class="space-y-0.5">
                                                    <div class="font-semibold text-gray-900 truncate max-w-xs flex items-center gap-1">
                                                        <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                        <span x-text="doc.file_name"></span>
                                                    </div>
                                                    <div class="text-[10.5px] text-gray-400">
                                                        <span x-text="formatBytes(doc.file_size)"></span>
                                                        <span x-show="doc.uploaded_at">&bull; <span x-text="formatDate(doc.uploaded_at)"></span></span>
                                                    </div>
                                                </div>
                                            </template>
                                            <template x-if="!doc.file_path">
                                                <span class="text-gray-400 text-xs italic">Belum ada berkas terlampir</span>
                                            </template>
                                        </td>

                                        {{-- Actions --}}
                                        <td class="py-3 px-3.5 text-right whitespace-nowrap">
                                            <div class="inline-flex items-center justify-end gap-1.5">
                                                {{-- Upload / Replace Button --}}
                                                <button type="button" 
                                                        @click="openUploadModal(doc)"
                                                        class="px-2.5 py-1 bg-white hover:bg-gray-50 text-[#8F0A0D] border border-red-200 hover:border-[#8F0A0D] rounded-lg text-[11px] font-bold transition shadow-2xs cursor-pointer flex items-center gap-1">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                                    <span x-text="doc.file_path ? 'Ganti Berkas' : 'Unggah File'"></span>
                                                </button>

                                                {{-- Download Button --}}
                                                <template x-if="doc.file_path">
                                                    <a :href="'/projects/' + projectData.id + '/documents/' + doc.id + '/download'" 
                                                       target="_blank"
                                                       class="px-2.5 py-1 bg-white hover:bg-gray-50 text-gray-700 border border-gray-200 rounded-lg text-[11px] font-semibold transition shadow-2xs cursor-pointer flex items-center gap-1">
                                                        <svg class="w-3 h-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                        <span>Unduh</span>
                                                    </a>
                                                </template>

                                                {{-- Verify Button (For Uploaded Docs) --}}
                                                <template x-if="doc.file_path && doc.status !== 'Verified'">
                                                    <button type="button" 
                                                            @click="verifyDocument(doc, 'Verified')"
                                                            class="px-2 py-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 rounded-lg text-[11px] font-bold transition shadow-2xs cursor-pointer"
                                                            title="Sahkan / Verifikasi Dokumen">
                                                        <span>Sahkan</span>
                                                    </button>
                                                </template>

                                                {{-- Delete Button --}}
                                                <template x-if="doc.file_path">
                                                    <button type="button" 
                                                            @click="deleteDocument(doc)"
                                                            class="p-1 text-gray-400 hover:text-red-600 rounded transition cursor-pointer"
                                                            title="Hapus Berkas">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                    </button>
                                                </template>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>

                </div>

            </div>

            {{-- 2-Column Content Layout (Details Peluang & Klien) --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 anim-fade-up anim-delay-3">
                
                {{-- Left 8-Cols: BDM Handover Details --}}
                <div class="lg:col-span-8 space-y-5">
                    
                    {{-- BDM INITIATION & HANDOVER PACKAGE --}}
                    <div class="ipnet-card p-5 sm:p-6 space-y-4">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-[#F1F5F9] pb-3.5">
                            <div>
                                <h3 class="text-[15px] font-bold text-[#1E293B]">Inisiasi Peluang &amp; Handover BDM</h3>
                                <p class="text-[12px] text-[#64748B] mt-0.5">Paket data serah terima dari Business Development ke Sales</p>
                            </div>
                            
                            <div class="flex items-center gap-2">
                                @if($project->bdm_handover_status)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        {{ $project->bdm_handover_status }}
                                    </span>
                                @endif
                                @if($project->bd_assessment_score)
                                    @php
                                        $score = $project->bd_assessment_score;
                                        $scoreColor = $score >= 80 ? 'text-emerald-700 bg-emerald-50 border-emerald-200' : ($score >= 60 ? 'text-blue-700 bg-blue-50 border-blue-200' : 'text-amber-700 bg-amber-50 border-amber-200');
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[11px] font-bold border {{ $scoreColor }}">
                                        Feasibility: {{ $score }}/100
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 text-xs">
                            
                            {{-- 1. Kebutuhan Bisnis Klien --}}
                            <div class="sm:col-span-2 bg-[#F8FAFC] border border-[#E2E8F0] p-4 rounded-xl space-y-1.5 hover:border-[#CBD5E1] transition-colors">
                                <div class="text-[11px] font-bold uppercase tracking-wider text-[#64748B]">
                                    Ringkasan Kebutuhan Bisnis Klien
                                </div>
                                <p class="text-[13px] text-[#1E293B] leading-relaxed font-normal whitespace-pre-line">
                                    {{ $project->business_need_summary ?: 'Kebutuhan modernisasi infrastruktur jaringan dan peningkatan kapasitas server/konektivitas.' }}
                                </p>
                            </div>

                            {{-- 2. Kontak Stakeholder --}}
                            <div class="bg-[#F8FAFC] border border-[#E2E8F0] p-4 rounded-xl space-y-2 hover:border-[#CBD5E1] transition-colors">
                                <div class="text-[11px] font-bold uppercase tracking-wider text-[#64748B]">
                                    Kontak Stakeholder / PIC
                                </div>
                                @php
                                    $sh = $project->stakeholders_data;
                                @endphp
                                <div class="space-y-1.5 text-[12.5px] pt-1">
                                    <div>
                                        <span class="text-[#64748B] text-[11px] block">Nama PIC:</span>
                                        <span class="font-bold text-[#1E293B]">{{ $sh['pic_name'] ?? ($project->customer_pic_business ?? 'PIC Klien') }}</span>
                                    </div>
                                    <div>
                                        <span class="text-[#64748B] text-[11px] block">Jabatan / Posisi:</span>
                                        <span class="text-[#334155] font-medium">{{ $sh['pic_role'] ?? 'Kepala IT / User' }}</span>
                                    </div>
                                    <div class="pt-1 flex flex-wrap items-center gap-3 text-[12px]">
                                        <div>
                                            <span class="text-[#64748B] text-[11px]">Telepon:</span>
                                            <span class="font-semibold text-[#1E293B] ml-1">{{ $sh['pic_phone'] ?? '-' }}</span>
                                        </div>
                                        <span class="text-slate-300">&bull;</span>
                                        <div>
                                            <span class="text-[#64748B] text-[11px]">Email:</span>
                                            <span class="font-semibold text-[#1E293B] ml-1">{{ $sh['pic_email'] ?? '-' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- 3. Analisis Teknis & Mitra --}}
                            <div class="bg-[#F8FAFC] border border-[#E2E8F0] p-4 rounded-xl space-y-2 hover:border-[#CBD5E1] transition-colors">
                                <div class="text-[11px] font-bold uppercase tracking-wider text-[#64748B]">
                                    Kompetitor &amp; Rekomendasi Mitra
                                </div>
                                <div class="space-y-2 text-[12.5px] pt-1">
                                    <div>
                                        <span class="text-[#64748B] text-[11px] block">Analisis Kompetitor:</span>
                                        <div class="font-medium text-[#1E293B]">{{ $project->competitor_analysis ?: 'Belum teridentifikasi' }}</div>
                                    </div>
                                    <div>
                                        <span class="text-[#64748B] text-[11px] block">Mitra Prinsipal Disarankan:</span>
                                        <div class="font-semibold text-[#8F0A0D]">{{ $project->partner_alignment ?: 'Direct / Multi-vendor' }}</div>
                                    </div>
                                </div>
                            </div>

                            {{-- 4. Kebutuhan Teknis Awal & Dokumen Pendukung --}}
                            <div class="sm:col-span-2 bg-[#F8FAFC] border border-[#E2E8F0] p-4 rounded-xl space-y-3 hover:border-[#CBD5E1] transition-colors">
                                <div class="text-[11px] font-bold uppercase tracking-wider text-[#64748B]">
                                    Spesifikasi Awal, Timeline &amp; Dokumen Pendukung (TOR/KAK)
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-1 text-[12.5px]">
                                    <div class="sm:col-span-2">
                                        <span class="text-[#64748B] text-[11px] block mb-1">Kebutuhan Teknis Awal:</span>
                                        <p class="text-[#1E293B] font-normal leading-relaxed">{{ $project->initial_requirement ?: 'Kebutuhan umum infrastruktur / jaringan sesuai hasil diskusi awal BDM.' }}</p>
                                    </div>
                                    <div class="space-y-2.5 sm:border-l sm:border-[#E2E8F0] sm:pl-4">
                                        <div>
                                            <span class="text-[#64748B] text-[11px] block">Target Timeline:</span>
                                            <span class="font-semibold text-[#1E293B]">{{ $project->target_timeline_type ?: '-' }}</span>
                                        </div>
                                        <div>
                                            <span class="text-[#64748B] text-[11px] block">Sumber Peluang:</span>
                                            <span class="font-semibold text-[#1E293B]">{{ $project->opportunity_source ?: 'Inbound Market Intel' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- Right 4-Cols: Client & Contract Details --}}
                <div class="lg:col-span-4 space-y-5">
                    
                    {{-- Detail Klien & Spesifikasi --}}
                    <div class="ipnet-card p-5 space-y-4">
                        <div class="flex items-center justify-between border-b border-[#F1F5F9] pb-3">
                            <h3 class="text-[15px] font-bold text-[#1E293B]">Informasi Pelanggan &amp; Lokasi</h3>
                        </div>

                        <div class="divide-y divide-[#F1F5F9] text-[13px]">
                            <div class="py-2.5 flex items-start justify-between gap-2">
                                <span class="text-[#64748B] font-medium">Instansi / Klien:</span>
                                <span class="font-bold text-[#1E293B] text-right">{{ $project->client }}</span>
                            </div>

                            <div class="py-2.5 flex items-start justify-between gap-2">
                                <span class="text-[#64748B] font-medium">Lokasi Pelaksanaan:</span>
                                <span class="font-semibold text-[#334155] text-right">{{ $project->location ?: '—' }}</span>
                            </div>

                            <div class="py-2.5 flex items-center justify-between">
                                <span class="text-[#64748B] font-medium">Tipe Proyek:</span>
                                <span class="font-semibold text-[#1E293B]">{{ $project->project_type ?: 'One-Time Project' }}</span>
                            </div>

                            @if($project->visit_schedule && $project->visit_schedule !== 'None')
                                <div class="py-2.5 flex items-center justify-between">
                                    <span class="text-[#64748B] font-medium">Jadwal Visit:</span>
                                    <span class="font-bold text-slate-800 bg-slate-100 px-2 py-0.5 rounded-md border border-slate-200 text-xs">
                                        {{ $project->visit_schedule }}
                                    </span>
                                </div>
                            @endif

                            @if($project->po_number)
                                <div class="py-2.5 flex items-center justify-between">
                                    <span class="text-[#64748B] font-medium">Nomor PO / SPK:</span>
                                    <span class="font-mono font-bold text-[#1E293B]">{{ $project->po_number }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Timeline & Penanggung Jawab --}}
                    <div class="ipnet-card p-5 space-y-4">
                        <div class="flex items-center justify-between border-b border-[#F1F5F9] pb-3">
                            <h3 class="text-[15px] font-bold text-[#1E293B]">Jadwal &amp; Tim Pelaksana</h3>
                        </div>

                        <div class="divide-y divide-[#F1F5F9] text-[13px]">
                            <div class="py-2.5 flex items-center justify-between">
                                <span class="text-[#64748B] font-medium">Tanggal Mulai:</span>
                                <span class="font-bold text-[#1E293B]">
                                    {{ $project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('d M Y') : '—' }}
                                </span>
                            </div>

                            <div class="py-2.5 flex items-center justify-between">
                                <span class="text-[#64748B] font-medium">Target Selesai:</span>
                                <span class="font-bold text-[#1E293B]">
                                    {{ $project->deadline ? \Carbon\Carbon::parse($project->deadline)->format('d M Y') : '—' }}
                                </span>
                            </div>

                            <div class="py-2.5 flex items-center justify-between">
                                <span class="text-[#64748B] font-medium">Total Durasi:</span>
                                <span class="font-bold text-[#8F0A0D]">{{ $project->duration_formatted ?: '—' }}</span>
                            </div>

                            <div class="py-2.5 flex items-center justify-between">
                                <span class="text-[#64748B] font-medium">Divisi Pelaksana:</span>
                                <span class="font-semibold text-[#1E293B]">{{ $project->division ? $project->division->name : 'Lintas Divisi' }}</span>
                            </div>

                            <div class="py-2.5 flex items-center justify-between">
                                <span class="text-[#64748B] font-medium">Project Manager (PM):</span>
                                <span class="font-semibold text-[#1E293B]">{{ $project->pm ? $project->pm->name : 'PMO' }}</span>
                            </div>

                            @if($project->bdm)
                                <div class="py-2.5 flex items-center justify-between">
                                    <span class="text-[#64748B] font-medium">Inisiator BDM:</span>
                                    <span class="font-semibold text-[#8F0A0D]">{{ $project->bdm->name }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </div>

    {{-- MODAL UPLOAD DOKUMEN (CLEAN CORPORATE MODAL) --}}
    <template x-teleport="body">
        <div x-show="isUploadModalOpen" 
             x-cloak 
             class="fixed inset-0 z-50 bg-[#0F172A]/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-5"
             @click.self="isUploadModalOpen = false">
            <div class="bg-white rounded-2xl w-[600px] max-w-full max-h-[92vh] flex flex-col overflow-hidden shadow-2xl border border-[#E2E8F0] my-auto anim-fade-up">
                
                {{-- Header --}}
                <div class="flex items-center justify-between border-b border-[#E2E8F0] p-5 sm:p-6 pb-4 shrink-0 bg-white">
                    <div>
                        <div class="flex items-center gap-2 text-[#8F0A0D] text-[11px] font-bold uppercase tracking-wider mb-0.5">
                            <span class="w-2 h-2 rounded-full bg-[#8F0A0D]"></span>
                            <span>Unggah Dokumen Tahap <span x-text="activeStageNumber"></span></span>
                        </div>
                        <h3 class="text-[16px] font-bold text-[#1E293B]" x-text="selectedDoc?.document_title"></h3>
                    </div>
                    <button type="button" @click="isUploadModalOpen = false" class="text-[#94A3B8] hover:text-[#1E293B] p-1.5 rounded-lg hover:bg-[#F1F5F9] transition cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Form Body --}}
                <form @submit.prevent="submitUpload()" class="flex flex-col flex-1 min-h-0 overflow-hidden">
                    <div class="p-5 sm:p-6 overflow-y-auto space-y-4 text-[12.5px] flex-1">
                        
                        {{-- Info Box --}}
                        <div class="p-3.5 bg-[#F8FAFC] rounded-xl border border-[#E2E8F0] text-xs text-gray-600">
                            <div class="font-bold text-gray-900 mb-0.5">Deskripsi Dokumen:</div>
                            <div x-text="selectedDoc?.notes || 'Dokumen resmi persyaratan tahapan.'"></div>
                        </div>

                        {{-- File Input --}}
                        <div>
                            <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">
                                Pilih Berkas Dokumen <span class="text-[#8F0A0D]">*</span>
                            </label>
                            <input type="file" 
                                   x-ref="fileInput" 
                                   required
                                   accept=".pdf,.docx,.doc,.xlsx,.xls,.zip,.rar,.png,.jpg,.jpeg,.txt,.csv"
                                   class="w-full px-3.5 py-2.5 bg-white border border-[#CBD5E1] rounded-xl text-xs text-[#64748B] file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-[11px] file:font-bold file:bg-[#8F0A0D] file:text-white hover:file:bg-[#73080A] cursor-pointer">
                            <span class="text-[11px] text-gray-400 mt-1 block">Format didukung: PDF, DOCX, XLSX, ZIP, RAR, Gambar (Maks 50MB)</span>
                        </div>

                        {{-- Notes Input --}}
                        <div>
                            <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">
                                Catatan / Keterangan Tambahan
                            </label>
                            <textarea x-model="uploadNotes" 
                                      rows="2.5" 
                                      placeholder="Contoh: Dokumen versi final v1.2 telah ditandatangani oleh Direktur Klien..."
                                      class="w-full px-3.5 py-2 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition resize-none"></textarea>
                        </div>

                        {{-- Loading State --}}
                        <div x-show="isUploading" class="p-3 bg-red-50 rounded-xl border border-red-200 text-[#8F0A0D] text-xs font-semibold flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4 text-[#8F0A0D]" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>Sedang mengunggah berkas dokumen...</span>
                        </div>

                    </div>

                    {{-- Footer --}}
                    <div class="flex items-center justify-end gap-2.5 p-4 px-6 border-t border-[#E2E8F0] bg-[#FAF9F8] shrink-0">
                        <button type="button" 
                                @click="isUploadModalOpen = false" 
                                class="px-4 py-2 text-[12.5px] font-bold text-[#475569] hover:bg-gray-100 border border-gray-300 rounded-xl transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" 
                                :disabled="isUploading"
                                class="px-5 py-2 text-[12.5px] font-bold text-white bg-[#8F0A0D] hover:bg-[#73080A] disabled:opacity-50 rounded-xl shadow-xs transition cursor-pointer">
                            Simpan &amp; Unggah Berkas
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </template>
</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('projectDocumentHub', () => ({
            projectData: @json($project),
            stages: @json($documentFlow),
            activeStageNumber: 1,
            isUploadModalOpen: false,
            selectedDoc: null,
            uploadNotes: '',
            isUploading: false,

            init() {
                // Set default active stage based on project stage
                const stageMap = { 'Acquire': 1, 'Design': 2, 'Deliver': 4, 'Operate': 6 };
                if (this.projectData.stage && stageMap[this.projectData.stage]) {
                    this.activeStageNumber = stageMap[this.projectData.stage];
                }
            },

            get activeStage() {
                return this.stages[this.activeStageNumber] || null;
            },

            get totalUploadedAll() {
                let count = 0;
                for (let k in this.stages) {
                    count += this.stages[k].uploaded_docs || 0;
                }
                return count;
            },

            get totalDocsAll() {
                let count = 0;
                for (let k in this.stages) {
                    count += this.stages[k].total_docs || 0;
                }
                return count;
            },

            openUploadModal(doc) {
                this.selectedDoc = doc;
                this.uploadNotes = doc.notes || '';
                this.isUploadModalOpen = true;
                if (this.$refs.fileInput) {
                    this.$refs.fileInput.value = '';
                }
            },

            async submitUpload() {
                if (!this.$refs.fileInput.files[0]) {
                    alert('Silakan pilih file dokumen terlebih dahulu.');
                    return;
                }

                this.isUploading = true;
                const formData = new FormData();
                formData.append('stage_number', this.activeStageNumber);
                formData.append('document_key', this.selectedDoc.document_key);
                formData.append('document_file', this.$refs.fileInput.files[0]);
                formData.append('notes', this.uploadNotes);

                try {
                    const response = await fetch(`/projects/${this.projectData.id}/documents/upload`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: formData
                    });

                    const res = await response.json();
                    if (response.ok && res.success) {
                        this.isUploadModalOpen = false;
                        await this.refreshDocumentFlow();
                        alert(res.message || 'Dokumen berhasil diunggah!');
                    } else {
                        alert(res.message || 'Gagal mengunggah dokumen.');
                    }
                } catch (e) {
                    console.error(e);
                    alert('Terjadi kesalahan jaringan saat mengunggah dokumen.');
                } finally {
                    this.isUploading = false;
                }
            },

            async verifyDocument(doc, status) {
                if (!confirm(`Sahkan dokumen '${doc.document_title}'?`)) return;

                try {
                    const response = await fetch(`/projects/${this.projectData.id}/documents/${doc.id}/verify`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ status: status })
                    });

                    const res = await response.json();
                    if (response.ok && res.success) {
                        await this.refreshDocumentFlow();
                    } else {
                        alert(res.message || 'Gagal memverifikasi dokumen.');
                    }
                } catch (e) {
                    console.error(e);
                    alert('Terjadi kesalahan jaringan.');
                }
            },

            async deleteDocument(doc) {
                if (!confirm(`Hapus berkas dokumen '${doc.document_title}'?`)) return;

                try {
                    const response = await fetch(`/projects/${this.projectData.id}/documents/${doc.id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    });

                    const res = await response.json();
                    if (response.ok && res.success) {
                        await this.refreshDocumentFlow();
                    } else {
                        alert(res.message || 'Gagal menghapus dokumen.');
                    }
                } catch (e) {
                    console.error(e);
                    alert('Terjadi kesalahan jaringan.');
                }
            },

            async refreshDocumentFlow() {
                try {
                    const response = await fetch(`/projects/${this.projectData.id}/document-flow`, {
                        headers: { 'Accept': 'application/json' }
                    });
                    const res = await response.json();
                    if (res.success && res.stages) {
                        this.stages = res.stages;
                    }
                } catch (e) {
                    console.error('Error refreshing document flow:', e);
                }
            },

            formatBytes(bytes) {
                if (!bytes) return '—';
                if (bytes >= 1048576) return (bytes / 1048576).toFixed(1) + ' MB';
                if (bytes >= 1024) return (bytes / 1024).toFixed(0) + ' KB';
                return bytes + ' B';
            },

            formatDate(dt) {
                if (!dt) return '';
                const d = new Date(dt);
                return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
            }
        }));
    });
</script>
@endpush
@endsection
