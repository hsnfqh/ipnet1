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
<div class="flex h-screen overflow-hidden bg-[#F8FAFC]">
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Detail Proyek'])
        
        <div class="p-4 sm:p-6 lg:p-7 space-y-5 max-w-[1600px] mx-auto">
            
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
                            'Planning'    => 'bg-purple-50 text-purple-700 border-purple-200',
                            'On Progress' => 'bg-amber-50 text-amber-800 border-amber-200',
                            'Completed'   => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                            default       => 'bg-slate-50 text-slate-700 border-slate-200',
                        };
                    @endphp

                    <span class="inline-flex items-center px-3 py-1 text-[11.5px] font-bold rounded-lg border {{ $statusBadge }}">
                        {{ $project->status }}
                    </span>

                    @if($project->handover_document_file)
                        <a href="{{ Storage::url($project->handover_document_file) }}" target="_blank" 
                           class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-[#8F0A0D] hover:bg-[#73080A] text-white text-[12px] font-bold rounded-xl shadow-xs transition hover:scale-[1.02] active:scale-[0.98] cursor-pointer">
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
                                Detail Informasi Inisiasi Peluang
                            </span>
                            <span class="px-2 py-0.5 rounded-md text-[10.5px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                {{ $project->project_type ?: 'One-Time Project' }}
                            </span>
                        </div>
                        <h1 class="text-xl sm:text-2xl font-bold text-[#1E293B] tracking-tight leading-snug">{{ $project->name }}</h1>
                        <p class="text-[12.5px] text-[#64748B] font-medium flex items-center gap-2 flex-wrap">
                            <span class="font-bold text-[#1E293B]">{{ $project->client }}</span>
                            <span>•</span>
                            <span class="text-[#475569] font-semibold">{{ $project->division ? $project->division->name : 'Lintas Divisi' }}</span>
                            <span>•</span>
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

            {{-- 4 Metric Summary Cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 anim-fade-up anim-delay-2">
                
                {{-- Card 1: PIC Sales --}}
                <div class="ipnet-card p-4 sm:p-5 hover:-translate-y-0.5 transition-transform duration-200">
                    <p class="text-[11px] font-bold text-[#64748B] uppercase tracking-wider">Account Executive / Sales</p>
                    <h4 class="text-[15px] font-bold text-[#1E293B] truncate mt-1">{{ $project->sales_name ?: 'Tim Sales' }}</h4>
                    <p class="text-[11.5px] text-[#94A3B8] mt-0.5">PIC tindak lanjut komersial</p>
                </div>

                {{-- Card 2: Kelayakan Feasibility --}}
                <div class="ipnet-card p-4 sm:p-5 hover:-translate-y-0.5 transition-transform duration-200">
                    <p class="text-[11px] font-bold text-[#64748B] uppercase tracking-wider">Kelayakan BDM</p>
                    <h4 class="text-[15px] font-bold text-[#1E293B] truncate mt-1">
                        {{ $project->bd_assessment_score ?: 80 }}/100 Feasible
                    </h4>
                    <p class="text-[11.5px] text-[#94A3B8] mt-0.5">Skor asesmen potensi peluang</p>
                </div>

                {{-- Card 3: Target Timeline --}}
                <div class="ipnet-card p-4 sm:p-5 hover:-translate-y-0.5 transition-transform duration-200">
                    <p class="text-[11px] font-bold text-[#64748B] uppercase tracking-wider">Target Timeline</p>
                    <h4 class="text-[15px] font-bold text-[#1E293B] truncate mt-1">
                        {{ $project->target_timeline_type ?: 'Q3 2026' }}
                    </h4>
                    <p class="text-[11.5px] text-[#94A3B8] mt-0.5">Estimasi kebutuhan implementasi</p>
                </div>

                {{-- Card 4: Status Handover --}}
                <div class="ipnet-card p-4 sm:p-5 hover:-translate-y-0.5 transition-transform duration-200">
                    <p class="text-[11px] font-bold text-[#64748B] uppercase tracking-wider">Status Handover</p>
                    <h4 class="text-[15px] font-bold text-[#8F0A0D] truncate mt-1">
                        {{ $project->bdm_handover_status ?: 'Draft Inisiasi' }}
                    </h4>
                    <p class="text-[11.5px] text-[#94A3B8] mt-0.5">Tahapan serah terima BDM</p>
                </div>

            </div>

            {{-- 2-Column Content Layout --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 anim-fade-up anim-delay-3">
                
                {{-- Left 8-Cols: BDM Handover Details --}}
                <div class="lg:col-span-8 space-y-5">
                    
                    {{-- BDM INITIATION & HANDOVER PACKAGE --}}
                    <div class="ipnet-card p-5 sm:p-6 space-y-4">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-[#F1F5F9] pb-3.5">
                            <div>
                                <h3 class="text-[15px] font-bold text-[#1E293B]">Inisiasi Peluang & Handover BDM</h3>
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
                                        <span class="text-slate-300">•</span>
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
                                    Kompetitor & Rekomendasi Mitra
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
                                    Spesifikasi Awal, Timeline & Dokumen Pendukung (TOR/KAK)
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
                                        <div>
                                            <span class="text-[#64748B] text-[11px] block mb-1">Berkas TOR / KAK:</span>
                                            @if($project->handover_document_file)
                                                <a href="{{ Storage::url($project->handover_document_file) }}" target="_blank" 
                                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-[#CBD5E1] text-[#8F0A0D] rounded-xl font-bold text-[11.5px] hover:bg-red-50 transition hover:border-[#8F0A0D]/40 shadow-2xs">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                    <span>Unduh Dokumen</span>
                                                </a>
                                            @else
                                                <span class="text-slate-400 text-[11.5px] italic">Tidak ada lampiran berkas</span>
                                            @endif
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
                            <h3 class="text-[15px] font-bold text-[#1E293B]">Informasi Pelanggan & Lokasi</h3>
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
                                    <span class="font-bold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded-md border border-indigo-200 text-xs">
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
                            <h3 class="text-[15px] font-bold text-[#1E293B]">Jadwal & Tim Pelaksana</h3>
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
                                <span class="font-semibold text-[#1E293B]">{{ $project->pm ? $project->pm->name : 'Rizki (PMO)' }}</span>
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
</div>
@endsection
