@extends('layouts.app')

@section('title', 'Detail Proyek & Dokumen - ' . $project->client)

@push('styles')
<style>
    [x-cloak] { display: none !important; }

    .ipnet-card {
        background-color: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 20px;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.04);
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .ipnet-badge-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background-color: #8F0A0D;
        display: inline-block;
        margin-right: 8px;
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
    isDocModalOpen: false, 
    selectedDocType: 'spk', 
    selectedDocTitle: '', 
    isSlaModalOpen: false 
}">
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Detail Proyek & Dokumen'])
        
        <div class="p-4 sm:p-6 lg:p-7 space-y-5 max-w-[1600px] mx-auto">

            <!-- ========================================================== -->
            <!-- 1. BREADCRUMB & PAGE HEADER CARD                           -->
            <!-- ========================================================== -->
            <div class="ipnet-card p-5 sm:p-6 anim-fade-up anim-delay-1">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2 text-[12px] font-semibold text-[#64748B] mb-1">
                            <a href="{{ route('ms.projects.index') }}" class="hover:text-[#1E293B] transition">Daftar Proyek</a>
                            <svg class="w-3.5 h-3.5 text-[#94A3B8]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            <span class="text-[#8F0A0D] font-bold">Detail Proyek & Dokumen</span>
                        </div>
                        <div class="flex items-center gap-3 flex-wrap">
                            <h1 class="text-[22px] font-extrabold text-[#1E293B] tracking-tight">{{ $project->client }}</h1>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-50 text-[#8F0A0D] border border-rose-200">
                                {{ $project->project_type ?: 'Managed Service SLA' }}
                            </span>
                            @if($project->sla_tier)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                    Tier: {{ $project->sla_tier }}
                                </span>
                            @endif
                        </div>
                        <p class="text-[13px] text-[#64748B] mt-1">{{ $project->name }}</p>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center gap-2.5 shrink-0 flex-wrap">
                        <button type="button" @click="isSlaModalOpen = true" class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-xl border border-[#CBD5E1] bg-white hover:bg-[#F8FAFC] text-[#334155] text-[12.5px] font-bold transition shadow-xs cursor-pointer">
                            <svg class="w-4 h-4 text-[#64748B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            <span>Edit Parameter SLA</span>
                        </button>
                        <a href="{{ route('ms.monitoring.index', ['project_id' => $project->id]) }}" class="btn-ipnet-gradient inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-bold text-[12.5px] transition shadow-md text-white cursor-pointer">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            <span>Monitoring PM / CM</span>
                        </a>
                    </div>
                </div>
            </div>

            @if(session('success'))
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-[13px] font-semibold flex items-center justify-between shadow-xs anim-fade-up">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button type="button" @click="$el.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900 font-bold">&times;</button>
                </div>
            @endif

            <!-- ========================================================== -->
            <!-- 2. DATA CUSTOMER (KIRI) vs BERKAS DOKUMEN (KANAN)          -->
            <!-- ========================================================== -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 anim-fade-up anim-delay-2">
                
                <!-- KARTU KIRI: DATA CUSTOMER & PIC -->
                <div class="lg:col-span-5 ipnet-card p-5 sm:p-6 space-y-4">
                    <div class="flex items-center justify-between border-b border-[#E2E8F0] pb-3">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#8F0A0D]"></span>
                            <h2 class="text-[13px] font-bold text-[#1E293B] uppercase tracking-wider">Data Customer & PIC</h2>
                        </div>
                        <span class="text-[11px] font-mono font-bold px-2 py-0.5 rounded bg-[#F1F5F9] text-[#475569] border border-[#E2E8F0]">
                            ID: #PROJ-{{ $project->id }}
                        </span>
                    </div>

                    <div class="space-y-3.5">
                        <div>
                            <span class="text-[11px] font-bold text-[#94A3B8] uppercase tracking-wider block">Nama Customer / Perusahaan</span>
                            <p class="text-[16px] font-extrabold text-[#1E293B] mt-0.5">{{ $project->client }}</p>
                        </div>

                        <!-- Sales PIC & Pre-Sales / SA Cards -->
                        <div class="grid grid-cols-2 gap-3 pt-1">
                            <div class="bg-[#F8FAFC] p-3 rounded-xl border border-[#E2E8F0]">
                                <span class="text-[10.5px] font-bold text-[#64748B] uppercase tracking-wider block">Sales PIC</span>
                                <p class="text-[13px] font-bold text-[#1E293B] mt-0.5">{{ $project->sales_name ?: ($project->creator?->name ?: 'Raiza') }}</p>
                            </div>
                            <div class="bg-[#F8FAFC] p-3 rounded-xl border border-[#E2E8F0]">
                                <span class="text-[10.5px] font-bold text-[#64748B] uppercase tracking-wider block">Pre-Sales / SA</span>
                                <p class="text-[13px] font-bold text-[#1E293B] mt-0.5">{{ $project->presales_name ?: 'Akbar' }}</p>
                            </div>
                        </div>

                        <!-- Customer Contacts Detail -->
                        <div class="space-y-2.5 pt-2 border-t border-[#E2E8F0] text-[12.5px]">
                            <div class="flex items-start gap-2.5">
                                <div class="w-7 h-7 rounded-lg bg-[#F1F5F9] text-[#64748B] flex items-center justify-center shrink-0 mt-0.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                </div>
                                <div class="min-w-0">
                                    <span class="text-[11px] text-[#94A3B8] font-medium block">Customer PIC (Technical)</span>
                                    <p class="font-semibold text-[#1E293B]">{{ $project->customer_pic_technical ?: 'Bpk. Budi Santoso (0812-3456-7890)' }}</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-2.5">
                                <div class="w-7 h-7 rounded-lg bg-[#F1F5F9] text-[#64748B] flex items-center justify-center shrink-0 mt-0.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                </div>
                                <div class="min-w-0">
                                    <span class="text-[11px] text-[#94A3B8] font-medium block">Customer PIC (Procurement / Business)</span>
                                    <p class="font-semibold text-[#1E293B]">{{ $project->customer_pic_business ?: 'Ibu Diana Putri (0812-9876-5432)' }}</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-2.5">
                                <div class="w-7 h-7 rounded-lg bg-[#F1F5F9] text-[#64748B] flex items-center justify-center shrink-0 mt-0.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                </div>
                                <div class="min-w-0">
                                    <span class="text-[11px] text-[#94A3B8] font-medium block">Customer PIC (Finance)</span>
                                    <p class="font-semibold text-[#1E293B]">{{ $project->customer_pic_finance ?: 'Bpk. Hendra Gunawan' }}</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-2.5">
                                <div class="w-7 h-7 rounded-lg bg-[#F1F5F9] text-[#64748B] flex items-center justify-center shrink-0 mt-0.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                </div>
                                <div class="min-w-0">
                                    <span class="text-[11px] text-[#94A3B8] font-medium block">Lokasi Operasional / Site</span>
                                    <p class="font-semibold text-[#1E293B]">{{ $project->location ?: 'On-Site Klien (Data Center / Head Office)' }}</p>
                                </div>
                            </div>

                            @if($project->contract_value)
                                <div class="flex items-start gap-2.5 pt-1">
                                    <div class="w-7 h-7 rounded-lg bg-rose-50 text-[#8F0A0D] flex items-center justify-center shrink-0 mt-0.5 border border-rose-200">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                    <div class="min-w-0">
                                        <span class="text-[11px] text-[#94A3B8] font-medium block">Nilai Kontrak Layanan</span>
                                        <p class="font-extrabold text-[#8F0A0D] text-[14px]">Rp {{ number_format($project->contract_value, 0, ',', '.') }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- KARTU KANAN: BERKAS & DOKUMEN KONTRAK (SPK, KONTRAK, BOQ, BA, DOKUMEN PROYEK) -->
                <div class="lg:col-span-7 ipnet-card p-5 sm:p-6 space-y-4">
                    <div class="flex items-center justify-between border-b border-[#E2E8F0] pb-3">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#1E293B]"></span>
                            <h2 class="text-[13px] font-bold text-[#1E293B] uppercase tracking-wider">Berkas & Dokumen Kontrak</h2>
                        </div>
                        <span class="text-[11px] font-semibold text-[#64748B]">SPK, Kontrak, BOQ, BA, Dokumen Project</span>
                    </div>

                    <div class="space-y-3">
                        @foreach($documents as $docKey => $doc)
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-3.5 rounded-xl border border-[#E2E8F0] hover:border-[#CBD5E1] bg-white transition shadow-2xs">
                                <div class="flex items-start gap-3 min-w-0">
                                    <div class="w-9 h-9 rounded-xl {{ $doc['file_path'] ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-[#F1F5F9] text-[#64748B] border border-[#E2E8F0]' }} flex items-center justify-center shrink-0 mt-0.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <h3 class="text-[13px] font-bold text-[#1E293B]">{{ $doc['title'] }}</h3>
                                            @if($doc['file_path'])
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10.5px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                                    Tersedia
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10.5px] font-bold bg-slate-100 text-slate-500 border border-slate-200">
                                                    Belum Ada
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-[11.5px] text-[#64748B] mt-0.5">{{ $doc['description'] }}</p>
                                        @if($doc['number'])
                                            <p class="text-[11px] font-mono text-[#475569] mt-0.5 font-semibold">No: {{ $doc['number'] }}</p>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 self-end sm:self-center shrink-0">
                                    @if($doc['file_path'])
                                        <a href="{{ asset('storage/' . $doc['file_path']) }}" target="_blank" class="px-3 py-1.5 rounded-lg bg-[#F1F5F9] hover:bg-[#E2E8F0] text-[#1E293B] text-[11.5px] font-bold transition flex items-center gap-1.5 border border-[#CBD5E1]">
                                            <svg class="w-3.5 h-3.5 text-[#64748B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            <span>Lihat File</span>
                                        </a>
                                    @endif
                                    <button type="button" @click="selectedDocType = '{{ $docKey }}'; selectedDocTitle = '{{ $doc['title'] }}'; isDocModalOpen = true" class="px-3 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-[#8F0A0D] text-[11.5px] font-bold transition flex items-center gap-1.5 border border-rose-200 cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                        <span>{{ $doc['file_path'] ? 'Ganti File' : 'Upload' }}</span>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- ========================================================== -->
            <!-- 3. PARAMETER LAYANAN MANAGED SERVICE & SLA                 -->
            <!-- ========================================================== -->
            <div class="ipnet-card p-5 sm:p-6 space-y-5 anim-fade-up anim-delay-3">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-[#E2E8F0] pb-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#8F0A0D]"></span>
                            <h2 class="text-[15px] font-bold text-[#1E293B] tracking-tight">Parameter Layanan Managed Service & SLA</h2>
                        </div>
                        <p class="text-[12px] text-[#64748B] mt-0.5">Spesifikasi komitmen SLA, kuota Corrective Maintenance (CM), dan frekuensi Preventive Maintenance (PM)</p>
                    </div>
                    <button type="button" @click="isSlaModalOpen = true" class="btn-ipnet-gradient px-4 py-2 rounded-xl text-white text-[12.5px] font-bold shadow-md transition cursor-pointer self-start sm:self-auto flex items-center gap-2">
                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        <span>Ubah Parameter SLA</span>
                    </button>
                </div>

                <!-- 6 Metrics Parameter SLA -->
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3.5">
                    <div class="p-4 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0] space-y-1">
                        <span class="text-[11px] font-bold text-[#64748B] uppercase tracking-wider block">Durasi Kontrak</span>
                        <p class="text-[13px] font-bold text-[#1E293B]">
                            @if($project->service_start_date && $project->service_end_date)
                                {{ \Carbon\Carbon::parse($project->service_start_date)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($project->service_end_date)->format('d/m/Y') }}
                            @else
                                1 Tahun Kontrak
                            @endif
                        </p>
                        <span class="text-[10.5px] text-[#94A3B8]">Periode Layanan</span>
                    </div>

                    <div class="p-4 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0] space-y-1">
                        <span class="text-[11px] font-bold text-[#64748B] uppercase tracking-wider block">SLA Commitment</span>
                        <p class="text-[18px] font-black text-[#8F0A0D]">{{ $project->sla_target_percentage ?: '99.50' }}%</p>
                        <span class="text-[10.5px] text-[#94A3B8]">Target Availability</span>
                    </div>

                    <div class="p-4 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0] space-y-1">
                        <span class="text-[11px] font-bold text-[#64748B] uppercase tracking-wider block">MTTR Target</span>
                        <p class="text-[18px] font-black text-[#1E293B]">{{ $project->mttr_hours ?: 4 }} Jam</p>
                        <span class="text-[10.5px] text-[#94A3B8]">Mean Time to Repair</span>
                    </div>

                    <div class="p-4 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0] space-y-1">
                        <span class="text-[11px] font-bold text-[#64748B] uppercase tracking-wider block">Coverage Support</span>
                        <p class="text-[18px] font-black text-[#1E293B]">{{ $project->sla_coverage_hours ?: '24x7' }}</p>
                        <span class="text-[10.5px] text-[#94A3B8]">Standby Support</span>
                    </div>

                    <div class="p-4 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0] space-y-1">
                        <span class="text-[11px] font-bold text-[#64748B] uppercase tracking-wider block">Call Response</span>
                        <p class="text-[18px] font-black text-[#1E293B]">{{ $project->call_response_time ?: '15 menit' }}</p>
                        <span class="text-[10.5px] text-[#94A3B8]">Respon Panggilan</span>
                    </div>

                    <div class="p-4 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0] space-y-1">
                        <span class="text-[11px] font-bold text-[#64748B] uppercase tracking-wider block">Target Resolusi</span>
                        <p class="text-[18px] font-black text-[#1E293B]">{{ $project->incident_resolution_hours ?: 4 }} Jam</p>
                        <span class="text-[10.5px] text-[#94A3B8]">Resolusi Masalah</span>
                    </div>
                </div>

                <!-- CM & PM Quota Breakdown -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 pt-1">
                    <!-- CM Quota Card -->
                    <div class="p-5 rounded-2xl border border-[#E2E8F0] bg-white space-y-3 shadow-2xs">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-[11px] font-bold text-[#8F0A0D] uppercase tracking-wider">Corrective Maintenance</span>
                                <h3 class="text-[14px] font-bold text-[#1E293B] mt-0.5">Kuota Tiket CM (1 Tahun)</h3>
                            </div>
                            <span class="text-[12px] font-extrabold px-3 py-1 rounded-full bg-rose-50 text-[#8F0A0D] border border-rose-200">
                                {{ $cmQuota >= 999 ? 'Unlimited Tiket' : $cmQuota . ' Tiket' }}
                            </span>
                        </div>

                        <div class="space-y-1.5 pt-1">
                            <div class="flex items-center justify-between text-[12px] font-semibold text-[#475569]">
                                <span>Tiket Terpakai: <strong class="text-[#1E293B]">{{ $cmUsed }} Tiket</strong></span>
                                <span>Sisa Kuota: <strong class="text-emerald-700">{{ $cmRemaining }} Tiket</strong></span>
                            </div>
                            <div class="w-full bg-[#E2E8F0] h-2.5 rounded-full overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-500" 
                                     style="background: linear-gradient(90deg, #8F0A0D, #D62E3C); width: {{ $cmQuota >= 999 ? '15%' : min(100, round(($cmUsed / max(1, $cmQuota)) * 100)) }}%;"></div>
                            </div>
                        </div>

                        <p class="text-[11.5px] text-[#64748B]">Opsi kuota kontrak: <strong>4, 6, 8, 12, 18 tiket</strong>, atau unlimited per periode.</p>
                    </div>

                    <!-- PM Frequency Card -->
                    <div class="p-5 rounded-2xl border border-[#E2E8F0] bg-white space-y-3 shadow-2xs">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-[11px] font-bold text-[#475569] uppercase tracking-wider">Preventive Maintenance</span>
                                <h3 class="text-[14px] font-bold text-[#1E293B] mt-0.5">Jadwal Siklus PM (1 Tahun)</h3>
                            </div>
                            <span class="text-[12px] font-extrabold px-3 py-1 rounded-full bg-slate-100 text-[#1E293B] border border-[#CBD5E1]">
                                {{ $pmTotal }} Kali ({{ str_replace('_', ' ', $project->pm_frequency_type ?: 'per 3 bulan') }})
                            </span>
                        </div>

                        <div class="space-y-1.5 pt-1">
                            <div class="flex items-center justify-between text-[12px] font-semibold text-[#475569]">
                                <span>Siklus Selesai: <strong class="text-[#1E293B]">{{ $pmCompleted }} / {{ $pmTotal }} PM</strong></span>
                                <span>Progres: <strong class="text-[#8F0A0D]">{{ $pmProgress }}%</strong></span>
                            </div>
                            <div class="w-full bg-[#E2E8F0] h-2.5 rounded-full overflow-hidden">
                                <div class="bg-emerald-600 h-2.5 rounded-full transition-all duration-500" style="width: {{ min(100, $pmProgress) }}%;"></div>
                            </div>
                        </div>

                        <p class="text-[11.5px] text-[#64748B]">Frekuensi inspeksi berkala: <strong>Per Bulan, Per 2 Bulan, Per 3 Bulan (Quarterly)</strong>.</p>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- ========================================================== -->
    <!-- MODAL 1: UPLOAD DOKUMEN (SPK, KONTRAK, BOQ, BA)           -->
    <!-- ========================================================== -->
    <template x-teleport="body">
        <div x-show="isDocModalOpen" x-cloak class="fixed inset-0 z-50 bg-[#0E0D12]/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-[#E2E8F0]" @click.away="isDocModalOpen = false">
                <div class="flex items-center justify-between border-b border-[#E2E8F0] pb-3 mb-4">
                    <div>
                        <p class="text-[11px] font-bold text-[#8F0A0D] uppercase tracking-wider">Upload Berkas Dokumen</p>
                        <h3 class="text-[15px] font-bold text-[#1E293B]" x-text="selectedDocTitle"></h3>
                    </div>
                    <button @click="isDocModalOpen = false" class="text-slate-400 hover:text-slate-700 p-1 rounded-lg text-lg font-bold">&times;</button>
                </div>

                <form action="{{ route('ms.projects.documents.update', $project->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <input type="hidden" name="doc_type" :value="selectedDocType">

                    <div>
                        <label class="block text-[12px] font-semibold text-[#334155] mb-1">Nomor Dokumen (Opsional)</label>
                        <input type="text" name="doc_number" placeholder="Contoh: SPK/2026/001 atau KTR-014" class="w-full px-3.5 py-2.5 bg-white border border-[#CBD5E1] rounded-xl text-[12.5px] text-[#1E293B] outline-none focus:border-[#8F0A0D]">
                    </div>

                    <div>
                        <label class="block text-[12px] font-semibold text-[#334155] mb-1">Tanggal Dokumen (Opsional)</label>
                        <input type="date" name="doc_date" class="w-full px-3.5 py-2.5 bg-white border border-[#CBD5E1] rounded-xl text-[12.5px] text-[#1E293B] outline-none focus:border-[#8F0A0D]">
                    </div>

                    <div>
                        <label class="block text-[12px] font-semibold text-[#334155] mb-1">Pilih Berkas File (PDF / Word / Gambar / ZIP) <span class="text-[#8F0A0D]">*</span></label>
                        <input type="file" name="doc_file" required class="w-full px-3 py-2 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12px] text-[#1E293B] outline-none focus:border-[#8F0A0D] cursor-pointer">
                        <p class="text-[11px] text-[#94A3B8] mt-1">Maksimal ukuran file: 50MB.</p>
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-[#E2E8F0]">
                        <button type="button" @click="isDocModalOpen = false" class="px-4 py-2 rounded-xl text-[12.5px] font-semibold text-[#64748B] hover:bg-[#F8FAFC]">Batal</button>
                        <button type="submit" class="btn-ipnet-gradient px-4 py-2 rounded-xl text-white text-[12.5px] font-bold shadow-md cursor-pointer">Upload Sekarang</button>
                    </div>
                </form>
            </div>
        </div>
    </template>

    <!-- ========================================================== -->
    <!-- MODAL 2: UBAH PARAMETER SLA & KUOTA                        -->
    <!-- ========================================================== -->
    <template x-teleport="body">
        <div x-show="isSlaModalOpen" x-cloak class="fixed inset-0 z-50 bg-[#0E0D12]/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-xl w-full p-6 shadow-2xl border border-[#E2E8F0] max-h-[90vh] overflow-y-auto" @click.away="isSlaModalOpen = false">
                <div class="flex items-center justify-between border-b border-[#E2E8F0] pb-3 mb-4">
                    <div>
                        <p class="text-[11px] font-bold text-[#8F0A0D] uppercase tracking-wider">Konfigurasi Kontrak</p>
                        <h3 class="text-[16px] font-bold text-[#1E293B]">Ubah Parameter SLA & Kuota Tiket</h3>
                    </div>
                    <button @click="isSlaModalOpen = false" class="text-slate-400 hover:text-slate-700 p-1 rounded-lg text-lg font-bold">&times;</button>
                </div>

                <form action="{{ route('ms.projects.sla.update', $project->id) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div>
                            <label class="block text-[12px] font-semibold text-[#334155] mb-1">Kuota Corrective Maintenance (CM) <span class="text-[#8F0A0D]">*</span></label>
                            <select name="cm_quota" required class="w-full px-3.5 py-2.5 bg-white border border-[#CBD5E1] rounded-xl text-[12.5px] font-bold text-[#1E293B] outline-none focus:border-[#8F0A0D] cursor-pointer">
                                <option value="4" {{ ($project->cm_quota ?? 18) == 4 ? 'selected' : '' }}>4 Tiket / Tahun</option>
                                <option value="6" {{ ($project->cm_quota ?? 18) == 6 ? 'selected' : '' }}>6 Tiket / Tahun</option>
                                <option value="8" {{ ($project->cm_quota ?? 18) == 8 ? 'selected' : '' }}>8 Tiket / Tahun</option>
                                <option value="12" {{ ($project->cm_quota ?? 18) == 12 ? 'selected' : '' }}>12 Tiket / Tahun</option>
                                <option value="18" {{ ($project->cm_quota ?? 18) == 18 ? 'selected' : '' }}>18 Tiket / Tahun (Standar)</option>
                                <option value="999" {{ ($project->cm_quota ?? 18) == 999 ? 'selected' : '' }}>Unlimited Tiket</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[12px] font-semibold text-[#334155] mb-1">Frekuensi Preventive Maintenance (PM) <span class="text-[#8F0A0D]">*</span></label>
                            <select name="pm_frequency_type" required class="w-full px-3.5 py-2.5 bg-white border border-[#CBD5E1] rounded-xl text-[12.5px] font-bold text-[#1E293B] outline-none focus:border-[#8F0A0D] cursor-pointer">
                                <option value="per_bulan" {{ ($project->pm_frequency_type ?? 'per_3_bulan') == 'per_bulan' ? 'selected' : '' }}>Per Bulan (12 Kali/Tahun)</option>
                                <option value="per_2_bulan" {{ ($project->pm_frequency_type ?? 'per_3_bulan') == 'per_2_bulan' ? 'selected' : '' }}>Per 2 Bulan (6 Kali/Tahun)</option>
                                <option value="per_3_bulan" {{ ($project->pm_frequency_type ?? 'per_3_bulan') == 'per_3_bulan' ? 'selected' : '' }}>Per 3 Bulan / Quarterly (4 Kali/Tahun)</option>
                                <option value="per_4_bulan" {{ ($project->pm_frequency_type ?? 'per_3_bulan') == 'per_4_bulan' ? 'selected' : '' }}>Per 4 Bulan (3 Kali/Tahun)</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                        <div>
                            <label class="block text-[12px] font-semibold text-[#334155] mb-1">Total Siklus PM <span class="text-[#8F0A0D]">*</span></label>
                            <input type="number" name="pm_total_cycles" value="{{ $project->pm_total_cycles ?: 4 }}" min="1" max="24" required class="w-full px-3.5 py-2 bg-white border border-[#CBD5E1] rounded-xl text-[12.5px] font-bold text-[#1E293B] outline-none focus:border-[#8F0A0D]">
                        </div>
                        <div>
                            <label class="block text-[12px] font-semibold text-[#334155] mb-1">SLA Target (%) <span class="text-[#8F0A0D]">*</span></label>
                            <input type="number" step="0.01" name="sla_target_percentage" value="{{ $project->sla_target_percentage ?: 99.50 }}" min="80" max="100" required class="w-full px-3.5 py-2 bg-white border border-[#CBD5E1] rounded-xl text-[12.5px] font-bold text-[#1E293B] outline-none focus:border-[#8F0A0D]">
                        </div>
                        <div>
                            <label class="block text-[12px] font-semibold text-[#334155] mb-1">MTTR (Jam) <span class="text-[#8F0A0D]">*</span></label>
                            <input type="number" name="mttr_hours" value="{{ $project->mttr_hours ?: 4 }}" min="1" max="72" required class="w-full px-3.5 py-2 bg-white border border-[#CBD5E1] rounded-xl text-[12.5px] font-bold text-[#1E293B] outline-none focus:border-[#8F0A0D]">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                        <div>
                            <label class="block text-[12px] font-semibold text-[#334155] mb-1">Coverage Support <span class="text-[#8F0A0D]">*</span></label>
                            <select name="sla_coverage_hours" required class="w-full px-3.5 py-2 bg-white border border-[#CBD5E1] rounded-xl text-[12.5px] font-bold text-[#1E293B] outline-none focus:border-[#8F0A0D] cursor-pointer">
                                <option value="24x7" {{ ($project->sla_coverage_hours ?? '24x7') == '24x7' ? 'selected' : '' }}>24x7 Standby</option>
                                <option value="24x8" {{ ($project->sla_coverage_hours ?? '24x7') == '24x8' ? 'selected' : '' }}>24x8</option>
                                <option value="16x7" {{ ($project->sla_coverage_hours ?? '24x7') == '16x7' ? 'selected' : '' }}>16x7</option>
                                <option value="8x7" {{ ($project->sla_coverage_hours ?? '24x7') == '8x7' ? 'selected' : '' }}>8x7</option>
                                <option value="8x5" {{ ($project->sla_coverage_hours ?? '24x7') == '8x5' ? 'selected' : '' }}>8x5 (Office Hours)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[12px] font-semibold text-[#334155] mb-1">Call Response <span class="text-[#8F0A0D]">*</span></label>
                            <select name="call_response_time" required class="w-full px-3.5 py-2 bg-white border border-[#CBD5E1] rounded-xl text-[12.5px] font-bold text-[#1E293B] outline-none focus:border-[#8F0A0D] cursor-pointer">
                                <option value="3 menit" {{ ($project->call_response_time ?? '15 menit') == '3 menit' ? 'selected' : '' }}>3 Menit</option>
                                <option value="5 menit" {{ ($project->call_response_time ?? '15 menit') == '5 menit' ? 'selected' : '' }}>5 Menit</option>
                                <option value="10 menit" {{ ($project->call_response_time ?? '15 menit') == '10 menit' ? 'selected' : '' }}>10 Menit</option>
                                <option value="15 menit" {{ ($project->call_response_time ?? '15 menit') == '15 menit' ? 'selected' : '' }}>15 Menit</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[12px] font-semibold text-[#334155] mb-1">Target Resolusi (Jam) <span class="text-[#8F0A0D]">*</span></label>
                            <input type="number" name="incident_resolution_hours" value="{{ $project->incident_resolution_hours ?: 4 }}" min="1" max="72" required class="w-full px-3.5 py-2 bg-white border border-[#CBD5E1] rounded-xl text-[12.5px] font-bold text-[#1E293B] outline-none focus:border-[#8F0A0D]">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div>
                            <label class="block text-[12px] font-semibold text-[#334155] mb-1">Tanggal Mulai Layanan</label>
                            <input type="date" name="service_start_date" value="{{ $project->service_start_date ? \Carbon\Carbon::parse($project->service_start_date)->format('Y-m-d') : '' }}" class="w-full px-3.5 py-2 bg-white border border-[#CBD5E1] rounded-xl text-[12.5px] text-[#1E293B] outline-none focus:border-[#8F0A0D]">
                        </div>
                        <div>
                            <label class="block text-[12px] font-semibold text-[#334155] mb-1">Tanggal Akhir Layanan</label>
                            <input type="date" name="service_end_date" value="{{ $project->service_end_date ? \Carbon\Carbon::parse($project->service_end_date)->format('Y-m-d') : '' }}" class="w-full px-3.5 py-2 bg-white border border-[#CBD5E1] rounded-xl text-[12.5px] text-[#1E293B] outline-none focus:border-[#8F0A0D]">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div>
                            <label class="block text-[12px] font-semibold text-[#334155] mb-1">Sales PIC</label>
                            <input type="text" name="sales_name" value="{{ $project->sales_name ?: 'Raiza' }}" class="w-full px-3.5 py-2 bg-white border border-[#CBD5E1] rounded-xl text-[12.5px] text-[#1E293B] outline-none focus:border-[#8F0A0D]">
                        </div>
                        <div>
                            <label class="block text-[12px] font-semibold text-[#334155] mb-1">Pre-Sales / SA</label>
                            <input type="text" name="presales_name" value="{{ $project->presales_name ?: 'Akbar' }}" class="w-full px-3.5 py-2 bg-white border border-[#CBD5E1] rounded-xl text-[12.5px] text-[#1E293B] outline-none focus:border-[#8F0A0D]">
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-[#E2E8F0]">
                        <button type="button" @click="isSlaModalOpen = false" class="px-4 py-2 rounded-xl text-[12.5px] font-semibold text-[#64748B] hover:bg-[#F8FAFC]">Batal</button>
                        <button type="submit" class="btn-ipnet-gradient px-4 py-2 rounded-xl text-white text-[12.5px] font-bold shadow-md cursor-pointer">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </template>
</div>
@endsection
