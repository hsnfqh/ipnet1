@extends('layouts.app')

@section('title', $project->name . ' - Detail Proyek')

@section('content')
<div class="flex h-screen overflow-hidden">
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto bg-[#FAF9F8]">
        @include('components.topbar', ['title' => 'Detail Proyek'])
        
        <div class="p-4 sm:p-5 lg:p-[26px] animate-fade-in space-y-6 max-w-7xl mx-auto">
            
            {{-- Breadcrumb & Top Actions --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-2 text-xs">
                    <button onclick="window.history.back()" 
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white hover:bg-[#F8F7F6] text-[#3D3A44] font-semibold text-[13px] rounded-lg border border-[#E7E5E3] shadow-[0_1px_2px_rgba(14,13,18,0.05)] transition-all cursor-pointer">
                        <svg class="w-3.5 h-3.5 text-[#75727C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        <span>Kembali</span>
                    </button>
                    <span class="text-gray-300">/</span>
                    <span class="text-[#75727C] font-semibold">Proyek #{{ str_pad($project->id, 4, '0', STR_PAD_LEFT) }}</span>
                    <span class="text-gray-300">/</span>
                    <span class="text-[#17151C] font-bold truncate max-w-xs">{{ $project->client }}</span>
                </div>

                <div class="flex items-center gap-2 flex-wrap">
                    @php
                        $statusBadge = match($project->status) {
                            'Draft'       => 'bg-gray-100 text-gray-700 border-gray-200',
                            'Opportunity' => 'bg-blue-50 text-blue-700 border-blue-200',
                            'Planning'    => 'bg-purple-50 text-purple-700 border-purple-200',
                            'On Progress' => 'bg-amber-50 text-amber-800 border-amber-200',
                            'Completed'   => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                            default       => 'bg-gray-50 text-gray-700 border-gray-200',
                        };

                    @endphp

                    <span class="inline-flex items-center px-3 py-1 text-xs font-bold rounded-lg border {{ $statusBadge }}">
                        {{ $project->status }}
                    </span>

                    @if($project->proposal_file)
                        <a href="{{ route('projects.proposal.download', $project->id) }}" 
                           class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-[#C81E2C] hover:bg-[#AF1424] text-white text-xs font-semibold rounded-lg shadow-[0_8px_20px_rgba(200,30,44,0.24)] transition-all cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            <span>Unduh Proposal</span>
                        </a>
                    @endif
                </div>
            </div>

            {{-- Main Title Banner Card --}}
            <div class="bg-white rounded-2xl border border-[#E7E5E3] shadow-[0_1px_3px_rgba(14,13,18,0.05)] p-5 sm:p-6">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                    <div class="space-y-2">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-[11px] font-bold uppercase tracking-wider bg-[#FDF1F2] text-[#C81E2C] border border-[#FADADF]">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#C81E2C]"></span>
                                Informasi Detail Proyek & Pelaksanaan
                            </span>
                            <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-gray-100 text-gray-700 border border-gray-200">
                                {{ $project->project_type ?: 'One-Time Project' }}
                            </span>
                        </div>
                        <h1 class="text-xl sm:text-2xl font-black text-[#17151C] tracking-tight leading-snug">{{ $project->name }}</h1>
                        <p class="text-[12.5px] text-[#75727C] font-medium flex items-center gap-2 flex-wrap">
                            <span class="font-bold text-[#17151C] flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-[#75727C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                {{ $project->client }}
                            </span>
                            <span>•</span>
                            <span class="text-[#3D3A44] font-semibold">{{ $project->division ? $project->division->name : 'Lintas Divisi' }}</span>
                            <span>•</span>
                            <span class="text-[#948F99]">Dibuat {{ $project->created_at ? $project->created_at->format('d M Y') : '—' }}</span>
                        </p>
                    </div>

                    <div class="flex items-center gap-6 border-t lg:border-t-0 lg:border-l border-[#E7E5E3] pt-4 lg:pt-0 lg:pl-6 flex-shrink-0">
                        <div>
                            <p class="text-[11.5px] font-semibold text-[#75727C] uppercase tracking-wide">Total Nilai Kontrak</p>
                            <h3 class="text-2xl font-black text-[#17151C] tracking-tight mt-0.5">
                                {{ $project->contract_value > 0 ? 'Rp ' . number_format($project->contract_value, 0, ',', '.') : '—' }}
                            </h3>
                        </div>
                        <div class="w-px h-12 bg-[#E7E5E3] hidden sm:block"></div>
                        <div>
                            <p class="text-[11.5px] font-semibold text-[#75727C] uppercase tracking-wide">Progress Riil</p>
                            <div class="flex items-center gap-3 mt-1">
                                <h3 class="text-2xl font-black text-[#C81E2C] tracking-tight">{{ $project->progress }}%</h3>
                                <div class="w-20 bg-[#EFEDEB] rounded-full h-2.5 overflow-hidden">
                                    <div class="bg-[#C81E2C] h-2.5 rounded-full transition-all duration-300" style="width: {{ $project->progress }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 4 Metric Summary Cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                
                {{-- Card 1: PIC Sales --}}
                <div class="bg-white p-4 sm:p-5 rounded-2xl border border-[#E7E5E3] shadow-[0_1px_2px_rgba(14,13,18,0.05)] flex items-center gap-3.5">
                    <div class="w-10 h-10 rounded-xl bg-[#FDF1F2] border border-[#FADADF] text-[#C81E2C] flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[11px] font-medium text-[#75727C]">Account Executive / Sales</p>
                        <h4 class="text-sm font-bold text-[#17151C] truncate mt-0.5">{{ $project->sales_name ?: 'Tim Sales' }}</h4>
                    </div>
                </div>

                {{-- Card 2: Estimasi Mandays --}}
                <div class="bg-white p-4 sm:p-5 rounded-2xl border border-[#E7E5E3] shadow-[0_1px_2px_rgba(14,13,18,0.05)] flex items-center gap-3.5">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-100 text-blue-600 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[11px] font-medium text-[#75727C]">Estimasi Presales</p>
                        <h4 class="text-sm font-bold text-[#17151C] truncate mt-0.5">
                            {{ $project->mandays ? $project->mandays . ' Mandays Engineer' : 'Menunggu SOW' }}
                        </h4>
                    </div>
                </div>

                {{-- Card 3: Target Selesai --}}
                <div class="bg-white p-4 sm:p-5 rounded-2xl border border-[#E7E5E3] shadow-[0_1px_2px_rgba(14,13,18,0.05)] flex items-center gap-3.5">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[11px] font-medium text-[#75727C]">Target Deadline</p>
                        <h4 class="text-sm font-bold text-[#17151C] truncate mt-0.5">
                            {{ $project->deadline ? \Carbon\Carbon::parse($project->deadline)->format('d M Y') : '—' }}
                        </h4>
                    </div>
                </div>

                {{-- Card 4: Total Task --}}
                <div class="bg-white p-4 sm:p-5 rounded-2xl border border-[#E7E5E3] shadow-[0_1px_2px_rgba(14,13,18,0.05)] flex items-center gap-3.5">
                    <div class="w-10 h-10 rounded-xl bg-purple-50 border border-purple-100 text-purple-600 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[11px] font-medium text-[#75727C]">Pekerjaan / Tasks</p>
                        <h4 class="text-sm font-bold text-[#17151C] truncate mt-0.5">
                            {{ $project->tasks->where('status', 'Completed')->count() }} / {{ $project->tasks->count() }} Selesai
                        </h4>
                    </div>
                </div>

            </div>

            {{-- 2-Column Content Layout --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
                
                {{-- Left 8-Cols: BDM Handover, SOW & Task List --}}
                <div class="lg:col-span-8 space-y-5">
                    
                    {{-- BDM INITIATION & HANDOVER PACKAGE (If Available) --}}
                    @if($project->opportunity_source || $project->business_need_summary || $project->stakeholders_data || $project->initial_requirement || $project->bdm_handover_status)
                        <div class="bg-white rounded-2xl border border-[#E7E5E3] shadow-[0_1px_3px_rgba(14,13,18,0.05)] p-5 sm:p-6 space-y-4">
                            <div class="flex items-center justify-between border-b border-[#E7E5E3] pb-3">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-lg bg-[#FDF1F2] border border-[#FADADF] text-[#C81E2C] flex items-center justify-center">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-[15px] font-bold text-[#17151C]">Inisiasi Peluang & Handover BDM</h3>
                                        <p class="text-[11.5px] text-[#75727C]">Paket data serah terima dari Business Development ke Sales</p>
                                    </div>
                                </div>
                                
                                @if($project->bd_assessment_score)
                                    @php
                                        $score = $project->bd_assessment_score;
                                        $scoreColor = $score >= 85 ? 'text-emerald-700 bg-emerald-50 border-emerald-200' : 'text-blue-700 bg-blue-50 border-blue-200';
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold border {{ $scoreColor }}">
                                        Skor Feasibility: {{ $score }}/100
                                    </span>
                                @endif
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 text-xs">
                                
                                {{-- 1. Kebutuhan Bisnis Klien --}}
                                <div class="sm:col-span-2 bg-[#FAF9F8] border border-[#E7E5E3] p-4 rounded-xl space-y-1.5">
                                    <div class="text-[11px] font-bold uppercase tracking-wider text-[#75727C] flex items-center gap-1.5">
                                        <span class="w-4 h-4 rounded-full bg-[#C81E2C] text-white flex items-center justify-center text-[9px] font-bold">1</span>
                                        <span>Ringkasan Kebutuhan Bisnis Klien</span>
                                    </div>
                                    <p class="text-[13px] text-[#17151C] leading-relaxed whitespace-pre-line">
                                        {{ $project->business_need_summary ?: 'Kebutuhan modernisasi infrastruktur jaringan dan peningkatan kapasitas server/konektivitas.' }}
                                    </p>
                                </div>

                                {{-- 2. Kontak Stakeholder --}}
                                <div class="bg-[#FAF9F8] border border-[#E7E5E3] p-3.5 rounded-xl space-y-2">
                                    <div class="text-[11px] font-bold uppercase tracking-wider text-[#75727C] flex items-center gap-1.5">
                                        <span class="w-4 h-4 rounded-full bg-[#C81E2C] text-white flex items-center justify-center text-[9px] font-bold">2</span>
                                        <span>Kontak Stakeholder / PIC</span>
                                    </div>
                                    @php
                                        $sh = $project->stakeholders_data;
                                    @endphp
                                    <div class="space-y-1 text-[12.5px]">
                                        <div class="font-bold text-[#17151C]">{{ $sh['pic_name'] ?? ($project->customer_pic_business ?? 'PIC Klien') }}</div>
                                        <div class="text-[#75727C] text-[11.5px]">{{ $sh['pic_role'] ?? 'Kepala IT / User' }}</div>
                                        <div class="text-[#3D3A44] font-medium pt-1 flex items-center gap-2">
                                            <span>📞 {{ $sh['pic_phone'] ?? '0812xxxxxxxx' }}</span>
                                            <span>•</span>
                                            <span>✉️ {{ $sh['pic_email'] ?? 'pic@perusahaan.com' }}</span>
                                        </div>
                                    </div>
                                </div>

                                {{-- 3. Analisis Teknis & Mitra --}}
                                <div class="bg-[#FAF9F8] border border-[#E7E5E3] p-3.5 rounded-xl space-y-2">
                                    <div class="text-[11px] font-bold uppercase tracking-wider text-[#75727C] flex items-center gap-1.5">
                                        <span class="w-4 h-4 rounded-full bg-[#C81E2C] text-white flex items-center justify-center text-[9px] font-bold">3</span>
                                        <span>Kompetitor & Mitra Prinsipal</span>
                                    </div>
                                    <div class="space-y-1.5 text-[12.5px]">
                                        <div>
                                            <span class="text-[#75727C] text-[11px]">Analisis Kompetitor:</span>
                                            <div class="font-semibold text-[#17151C]">{{ $project->competitor_analysis ?: 'Belum teridentifikasi' }}</div>
                                        </div>
                                        <div>
                                            <span class="text-[#75727C] text-[11px]">Mitra Prinsipal:</span>
                                            <div class="font-semibold text-[#C81E2C]">{{ $project->partner_alignment ?: 'Direct / Multi-vendor' }}</div>
                                        </div>
                                    </div>
                                </div>

                                {{-- 4. Kebutuhan Teknis Awal & Dokumen Pendukung --}}
                                <div class="sm:col-span-2 bg-[#FAF9F8] border border-[#E7E5E3] p-4 rounded-xl space-y-2">
                                    <div class="text-[11px] font-bold uppercase tracking-wider text-[#75727C] flex items-center gap-1.5">
                                        <span class="w-4 h-4 rounded-full bg-[#C81E2C] text-white flex items-center justify-center text-[9px] font-bold">4</span>
                                        <span>Spesifikasi Awal, Timeline & Berkas Pendukung (TOR/KAK)</span>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-1 text-[12.5px]">
                                        <div class="sm:col-span-2">
                                            <span class="text-[#75727C] text-[11px] block mb-0.5">Kebutuhan Teknis Awal:</span>
                                            <p class="text-[#17151C] font-medium leading-relaxed">{{ $project->initial_requirement ?: 'Kebutuhan umum infrastruktur / jaringan sesuai diskusi awal.' }}</p>
                                        </div>
                                        <div class="space-y-2 border-l border-[#E7E5E3] pl-3">
                                            <div>
                                                <span class="text-[#75727C] text-[11px] block">Target Timeline:</span>
                                                <span class="font-bold text-[#17151C]">{{ $project->target_timeline_type ?: 'Q3 2026' }}</span>
                                            </div>
                                            <div>
                                                <span class="text-[#75727C] text-[11px] block">Sumber Peluang:</span>
                                                <span class="font-semibold text-gray-700">{{ $project->opportunity_source ?: 'Inbound Market Intel' }}</span>
                                            </div>
                                            <div>
                                                <span class="text-[#75727C] text-[11px] block mb-1">Berkas TOR / KAK:</span>
                                                @if($project->handover_document_file)
                                                    <a href="{{ Storage::url($project->handover_document_file) }}" target="_blank" 
                                                       class="inline-flex items-center gap-1 px-2.5 py-1 bg-white border border-[#C81E2C] text-[#C81E2C] rounded-md font-bold text-[11px] hover:bg-red-50 transition">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                        <span>Unduh Dokumen</span>
                                                    </a>
                                                @else
                                                    <span class="text-gray-400 text-[11px] italic">Tidak ada lampiran berkas</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- SOW / Ruang Lingkup Teknis --}}
                    <div class="bg-white rounded-2xl border border-[#E7E5E3] shadow-[0_1px_3px_rgba(14,13,18,0.05)] p-5 sm:p-6 space-y-4">
                        <div class="flex items-center justify-between border-b border-[#E7E5E3] pb-3">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-[#C81E2C]"></span>
                                <h3 class="text-[15px] font-bold text-[#17151C]">Ruang Lingkup Teknis & SOW Presales</h3>
                            </div>
                            @if($project->proposal_file)
                                <span class="text-[11px] font-bold text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded-full border border-emerald-200">
                                    Dokumen Terlampir
                                </span>
                            @else
                                <span class="text-[11px] font-medium text-gray-400 bg-gray-50 px-2.5 py-0.5 rounded-full border border-gray-200">
                                    Belum Ada Dokumen
                                </span>
                            @endif
                        </div>

                        <div class="text-[13px] text-[#17151C] leading-relaxed bg-[#FAF9F8] border border-[#E7E5E3] p-4 rounded-xl space-y-2">
                            <p class="whitespace-pre-line">{{ $project->proposal_notes ?: ($project->description ?: 'Belum ada catatan teknis / SOW khusus untuk proyek ini.') }}</p>
                        </div>

                        @if($project->proposal_file)
                            <div class="flex items-center justify-between p-3.5 bg-emerald-50/60 border border-emerald-200 rounded-xl text-xs">
                                <div class="flex items-center gap-2.5 text-emerald-900 font-semibold truncate">
                                    <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <span class="truncate">Berkas Proposal Teknis & BoQ (PDF/Doc)</span>
                                </div>
                                <a href="{{ route('projects.proposal.download', $project->id) }}" 
                                   class="px-3.5 py-1.5 bg-[#C81E2C] hover:bg-[#AF1424] text-white font-semibold rounded-lg shadow-[0_8px_20px_rgba(200,30,44,0.24)] transition-all flex items-center gap-1.5 text-[11.5px] whitespace-nowrap cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    Unduh File
                                </a>
                            </div>
                        @endif
                    </div>

                    {{-- Daftar Task / Pekerjaan Engineer --}}
                    <div class="bg-white rounded-2xl border border-[#E7E5E3] shadow-[0_1px_3px_rgba(14,13,18,0.05)] overflow-hidden">
                        <div class="p-5 border-b border-[#E7E5E3] flex items-center justify-between">
                            <div>
                                <h3 class="text-[15px] font-bold text-[#17151C]">Pekerjaan Lapangan & Task Engineer</h3>
                                <p class="text-[11.5px] text-[#75727C] mt-0.5">Daftar item pekerjaan teknis yang ditugaskan kepada engineer</p>
                            </div>
                            <span class="px-2.5 py-1 rounded-md text-[11.5px] font-bold bg-gray-100 text-gray-700 border border-gray-200">
                                {{ $project->tasks->count() }} Task Terdaftar
                            </span>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse text-[13px]">
                                <thead>
                                    <tr class="bg-[#F1F0EE] border-b border-[#E7E5E3] text-[11.5px] font-semibold text-[#75727C] uppercase tracking-[0.3px]">
                                        <th class="py-3 px-4">Judul Pekerjaan</th>
                                        <th class="py-3 px-4">Engineer PIC</th>
                                        <th class="py-3 px-4">Target Waktu</th>
                                        <th class="py-3 px-4 text-center">Status</th>
                                        <th class="py-3 px-4 text-right">Progress</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-[#EFEDEB]">
                                    @forelse($project->tasks as $task)
                                        @php
                                            $taskBadge = match($task->status) {
                                                'Assigned'       => 'bg-blue-50 text-blue-700 border-blue-200',
                                                'In Progress'    => 'bg-amber-50 text-amber-800 border-amber-200',
                                                'Waiting Review' => 'bg-purple-50 text-purple-700 border-purple-200',
                                                'Completed'      => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                                default          => 'bg-gray-50 text-gray-700 border-gray-200',
                                            };
                                        @endphp
                                        <tr class="hover:bg-[#F8F7F6] transition-colors">
                                            <td class="py-3.5 px-4">
                                                <div class="font-bold text-[#17151C]">{{ $task->title }}</div>
                                                <div class="text-[11px] text-[#75727C] font-medium mt-0.5">{{ $task->priority }} Priority</div>
                                            </td>
                                            <td class="py-3.5 px-4 whitespace-nowrap">
                                                <span class="font-semibold text-[#3D3A44]">{{ $task->engineer?->name ?: 'Belum di-assign' }}</span>
                                            </td>
                                            <td class="py-3.5 px-4 whitespace-nowrap text-[#75727C]">
                                                {{ $task->deadline ? \Carbon\Carbon::parse($task->deadline)->format('d M Y') : '—' }}
                                            </td>
                                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                                <span class="inline-flex px-2.5 py-0.5 text-[10.5px] font-bold rounded-full border {{ $taskBadge }}">
                                                    {{ $task->status }}
                                                </span>
                                            </td>
                                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                                <span class="font-bold text-[#17151C]">{{ $task->progress }}%</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="py-10 text-center text-[#75727C] text-xs">
                                                Belum ada task pekerjaan teknis yang ditambahkan pada proyek ini.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>

                {{-- Right 4-Cols: Client & Contract Details --}}
                <div class="lg:col-span-4 space-y-5">
                    
                    {{-- Detail Klien & Spesifikasi --}}
                    <div class="bg-white rounded-2xl border border-[#E7E5E3] shadow-[0_1px_3px_rgba(14,13,18,0.05)] p-5 space-y-4">
                        <div class="flex items-center justify-between border-b border-[#E7E5E3] pb-3">
                            <h3 class="text-[15px] font-bold text-[#17151C]">Informasi Pelanggan & Lokasi</h3>
                        </div>

                        <div class="divide-y divide-[#EFEDEB] text-[13px]">
                            <div class="py-2.5 flex items-start justify-between gap-2">
                                <span class="text-[#75727C] font-medium">Instansi / Klien:</span>
                                <span class="font-bold text-[#17151C] text-right">{{ $project->client }}</span>
                            </div>

                            <div class="py-2.5 flex items-start justify-between gap-2">
                                <span class="text-[#75727C] font-medium">Lokasi Pelaksanaan:</span>
                                <span class="font-semibold text-[#3D3A44] text-right">{{ $project->location ?: '—' }}</span>
                            </div>

                            <div class="py-2.5 flex items-center justify-between">
                                <span class="text-[#75727C] font-medium">Tipe Proyek:</span>
                                <span class="font-semibold text-[#17151C]">{{ $project->project_type ?: 'One-Time Project' }}</span>
                            </div>

                            @if($project->visit_schedule && $project->visit_schedule !== 'None')
                                <div class="py-2.5 flex items-center justify-between">
                                    <span class="text-[#75727C] font-medium">Jadwal Visit:</span>
                                    <span class="font-bold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded border border-indigo-200 text-xs">
                                        {{ $project->visit_schedule }}
                                    </span>
                                </div>
                            @endif

                            @if($project->po_number)
                                <div class="py-2.5 flex items-center justify-between">
                                    <span class="text-[#75727C] font-medium">Nomor PO / SPK:</span>
                                    <span class="font-mono font-bold text-[#17151C]">{{ $project->po_number }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Timeline & Penanggung Jawab --}}
                    <div class="bg-white rounded-2xl border border-[#E7E5E3] shadow-[0_1px_3px_rgba(14,13,18,0.05)] p-5 space-y-4">
                        <div class="flex items-center justify-between border-b border-[#E7E5E3] pb-3">
                            <h3 class="text-[15px] font-bold text-[#17151C]">Jadwal & Tim Pelaksana</h3>
                        </div>

                        <div class="divide-y divide-[#EFEDEB] text-[13px]">
                            <div class="py-2.5 flex items-center justify-between">
                                <span class="text-[#75727C] font-medium">Tanggal Mulai:</span>
                                <span class="font-bold text-[#17151C]">
                                    {{ $project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('d M Y') : '—' }}
                                </span>
                            </div>

                            <div class="py-2.5 flex items-center justify-between">
                                <span class="text-[#75727C] font-medium">Target Selesai:</span>
                                <span class="font-bold text-[#17151C]">
                                    {{ $project->deadline ? \Carbon\Carbon::parse($project->deadline)->format('d M Y') : '—' }}
                                </span>
                            </div>

                            <div class="py-2.5 flex items-center justify-between">
                                <span class="text-[#75727C] font-medium">Total Durasi:</span>
                                <span class="font-bold text-[#C81E2C]">{{ $project->duration_formatted ?: '—' }}</span>
                            </div>

                            <div class="py-2.5 flex items-center justify-between">
                                <span class="text-[#75727C] font-medium">Divisi Pelaksana:</span>
                                <span class="font-semibold text-[#17151C]">{{ $project->division ? $project->division->name : 'Lintas Divisi' }}</span>
                            </div>

                            <div class="py-2.5 flex items-center justify-between">
                                <span class="text-[#75727C] font-medium">Project Manager (PM):</span>
                                <span class="font-semibold text-[#17151C]">{{ $project->pm ? $project->pm->name : 'Rizki (PMO)' }}</span>
                            </div>

                            @if($project->bdm)
                                <div class="py-2.5 flex items-center justify-between">
                                    <span class="text-[#75727C] font-medium">Inisiator BDM:</span>
                                    <span class="font-semibold text-[#C81E2C]">{{ $project->bdm->name }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </div>
</div>
@endsection
