@extends('layouts.app')

@section('title', 'Serah Terima Kontrak & Proyek - Sales Portal')

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
        padding: 20px 22px;
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
<div class="flex h-screen overflow-hidden bg-[#F8FAFC] font-sans" x-data="commercialHandoverPage()">
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Serah Terima Proyek & Layanan'])
        
        <div class="p-4 sm:p-6 lg:p-7 space-y-6 max-w-[1680px] mx-auto animate-fade-in">
            
            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between shadow-xs anim-fade-up">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 font-bold px-2">✕</button>
                </div>
            @endif

            {{-- 3 SUMMARY METRIC CARDS --}}
            @php
                $totalHandoverCount = $handoverProjects->total();
                $pmoTargetCount = $handoverProjects->getCollection()->where('handover_target', '!=', 'managed_service')->count();
                $msTargetCount = $handoverProjects->getCollection()->where('handover_target', 'managed_service')->count();
            @endphp
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 anim-fade-up anim-delay-1">
                {{-- Card 1 --}}
                <div class="ipnet-metric-card flex flex-col justify-between space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Total Kontrak Closed Deal</span>
                        <div class="w-8 h-8 rounded-lg bg-red-50 border border-red-100 text-[#8F0A0D] flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <div class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">
                            {{ $totalHandoverCount }} <span class="text-xs font-semibold text-gray-400">Kontrak PO</span>
                        </div>
                        <p class="text-[11.5px] text-gray-500 font-medium mt-1">Siap diserahterimakan ke tim teknis</p>
                    </div>
                </div>

                {{-- Card 2 --}}
                <div class="ipnet-metric-card flex flex-col justify-between space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">PMO Delivery (Tipe 1)</span>
                        <div class="w-8 h-8 rounded-lg bg-slate-100 border border-slate-200 text-slate-700 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <div class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">
                            {{ $pmoTargetCount }} <span class="text-xs font-semibold text-gray-400">Proyek Implementasi</span>
                        </div>
                        <p class="text-[11.5px] text-gray-500 font-medium mt-1">Tahap Deliver &bull; Instalasi &amp; UAT</p>
                    </div>
                </div>

                {{-- Card 3 --}}
                <div class="ipnet-metric-card flex flex-col justify-between space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Managed Service (Tipe 2)</span>
                        <div class="w-8 h-8 rounded-lg bg-slate-100 border border-slate-200 text-slate-700 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <div class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">
                            {{ $msTargetCount }} <span class="text-xs font-semibold text-gray-400">Kontrak Layanan</span>
                        </div>
                        <p class="text-[11.5px] text-gray-500 font-medium mt-1">Tahap Operate &bull; Monitoring &amp; SLA</p>
                    </div>
                </div>
            </div>

            <!-- Filter & Action Bar -->
            <div class="flex flex-col sm:flex-row sm:flex-wrap sm:justify-between sm:items-center gap-3 anim-fade-up anim-delay-2">
                <form method="GET" action="{{ route('sales.handover.index') }}" class="flex flex-col sm:flex-row flex-wrap gap-2.5 w-full sm:w-auto">
                    <div class="relative">
                        <svg class="w-4 h-4 absolute left-3.5 top-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" 
                               name="search" 
                               value="{{ $search }}"
                               placeholder="Cari proyek, klien, nomor PO/SPK..." 
                               class="w-full sm:w-72 px-3.5 py-2 pl-9 rounded-xl border border-gray-200 text-xs font-semibold text-gray-800 bg-white focus:outline-none focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D] transition-all shadow-xs">
                    </div>

                    <select name="target" onchange="this.form.submit()" 
                            class="w-full sm:w-56 px-3.5 py-2 rounded-xl border border-gray-200 text-xs font-semibold text-gray-800 bg-white focus:outline-none focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D] transition-all cursor-pointer shadow-xs">
                        <option value="">Semua Tipe Serah Terima</option>
                        <option value="pmo" {{ ($filterTarget ?? '') === 'pmo' ? 'selected' : '' }}>Tipe 1: Proyek Implementasi (PMO)</option>
                        <option value="managed_service" {{ ($filterTarget ?? '') === 'managed_service' ? 'selected' : '' }}>Tipe 2: Managed Service (Operate)</option>
                    </select>

                    <select name="status" onchange="this.form.submit()" 
                            class="w-full sm:w-52 px-3.5 py-2 rounded-xl border border-gray-200 text-xs font-semibold text-gray-800 bg-white focus:outline-none focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D] transition-all cursor-pointer shadow-xs">
                        <option value="">Semua Status Serah Terima</option>
                        <option value="Draft" {{ $filterStatus == 'Draft' ? 'selected' : '' }}>Draft Handover</option>
                        <option value="Submitted" {{ $filterStatus == 'Submitted' ? 'selected' : '' }}>Submitted ke Tim Teknis</option>
                        <option value="Approved" {{ $filterStatus == 'Approved' ? 'selected' : '' }}>Approved / Active</option>
                    </select>

                    @if($search || $filterStatus || ($filterTarget ?? ''))
                        <a href="{{ route('sales.handover.index') }}" 
                           class="px-3 py-2 text-xs font-bold text-gray-500 hover:text-gray-800 self-center">
                            Reset Filter
                        </a>
                    @endif
                </form>
            </div>

            {{-- Table Deals Handover --}}
            <div class="ipnet-card overflow-hidden anim-fade-up anim-delay-3">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-gray-50 text-gray-500 uppercase text-[10.5px] font-bold">
                            <tr>
                                <th class="py-3 px-4 rounded-l-lg">Proyek &amp; Klien</th>
                                <th class="py-3 px-4">Tipe &amp; Target Handover</th>
                                <th class="py-3 px-4">Nomor &amp; Tanggal PO/SPK</th>
                                <th class="py-3 px-4 text-right">Nilai Kontrak</th>
                                <th class="py-3 px-4">Ketentuan SLA / Termin</th>
                                <th class="py-3 px-4 text-center">Status Handover</th>
                                <th class="py-3 px-4 rounded-r-lg text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 font-medium text-gray-700">
                            @forelse($handoverProjects as $hp)
                                @php
                                    $isMs = ($hp->handover_target === 'managed_service' || $hp->stage === 'Operate');
                                    
                                    if ($isMs) {
                                        $badge = match($hp->ms_handover_status) {
                                            'Accepted'  => ['bg' => '#F0FDF4', 'color' => '#16A34A', 'label' => 'Active di Managed Service'],
                                            'Submitted' => ['bg' => '#F8FAFC', 'color' => '#475569', 'label' => 'Submitted ke MS Lead'],
                                            default     => ['bg' => '#FEFCE8', 'color' => '#CA8A04', 'label' => 'Draft MS Handover'],
                                        };
                                    } else {
                                        $badge = match($hp->commercial_handover_status) {
                                            'Approved'  => ['bg' => '#F0FDF4', 'color' => '#16A34A', 'label' => 'Approved by PMO'],
                                            'Submitted' => ['bg' => '#EFF6FF', 'color' => '#2563EB', 'label' => 'Submitted to Delivery'],
                                            default     => ['bg' => '#FEFCE8', 'color' => '#CA8A04', 'label' => 'Draft Handover'],
                                        };
                                    }
                                @endphp
                                <tr class="hover:bg-gray-50/80 transition-colors">
                                    {{-- Proyek & Klien --}}
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-gray-900 max-w-sm">{{ $hp->name }}</div>
                                        <div class="text-[11px] text-gray-400 font-medium mt-0.5">{{ $hp->client }} &bull; Sales: {{ $hp->sales_name ?: 'Sales' }}</div>
                                    </td>

                                    {{-- Tipe & Target Handover --}}
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        @if($isMs)
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-bold bg-slate-100 text-slate-800 border border-slate-200">
                                                <span>Managed Service</span>
                                            </span>
                                            <div class="text-[10.5px] text-gray-500 font-medium mt-0.5">
                                                Tier: {{ $hp->sla_tier ?: 'Gold' }} ({{ $hp->sla_coverage_hours ?: '24x7' }})
                                            </div>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-bold bg-slate-100 text-slate-800 border border-slate-200">
                                                <span>Proyek Implementasi</span>
                                            </span>
                                            <div class="text-[10.5px] text-gray-500 font-medium mt-0.5">
                                                Target: PMO &amp; Delivery Team
                                            </div>
                                        @endif
                                    </td>

                                    {{-- Nomor PO / SPK --}}
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        @if($hp->po_spk_number)
                                            <div class="font-mono font-bold text-gray-900">{{ $hp->po_spk_number }}</div>
                                            <div class="text-[11px] text-gray-400">{{ $hp->po_spk_date ? \Carbon\Carbon::parse($hp->po_spk_date)->format('d M Y') : '—' }}</div>
                                        @else
                                            <span class="text-gray-400 italic">Belum Input PO/SPK</span>
                                        @endif
                                    </td>

                                    {{-- Nilai Kontrak --}}
                                    <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                        <div class="font-black text-gray-900">
                                            {{ $hp->contract_value > 0 ? 'Rp ' . number_format($hp->contract_value, 0, ',', '.') : '—' }}
                                        </div>
                                        <div class="text-[10.5px] text-gray-400">
                                            Stage: {{ $hp->sales_stage }}
                                        </div>
                                    </td>

                                    {{-- Billing & SLA --}}
                                    <td class="py-3.5 px-4 max-w-xs">
                                        @if($isMs)
                                            <div class="text-[11.5px] font-semibold text-gray-900 truncate" title="{{ $hp->sla_commitment }}">
                                                SLA: {{ $hp->sla_commitment ?: 'SLA 99.5% Uptime & Response 15-30m' }}
                                            </div>
                                            <div class="text-[10.5px] text-gray-400">
                                                Jadwal Maint: {{ $hp->maintenance_frequency ?: 'Bulanan (Monthly)' }}
                                            </div>
                                        @else
                                            <div class="text-[11.5px] font-semibold text-gray-900 truncate" title="{{ $hp->billing_terms }}">
                                                {{ $hp->billing_terms ?: 'Termin belum diset' }}
                                            </div>
                                            <div class="text-[10.5px] text-gray-400 truncate" title="{{ $hp->sla_commitment }}">
                                                Garansi: {{ $hp->sla_commitment ?: 'Standar 8x5' }}
                                            </div>
                                        @endif
                                    </td>

                                    {{-- Status Handover --}}
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10.5px] font-bold" 
                                              style="background-color: {{ $badge['bg'] }}; color: {{ $badge['color'] }};">
                                            <span>{{ $badge['label'] }}</span>
                                        </span>
                                        @if($hp->commercial_handover_at)
                                            <div class="text-[10px] text-gray-400 mt-0.5">
                                                {{ \Carbon\Carbon::parse($hp->commercial_handover_at)->format('d/m/Y H:i') }}
                                            </div>
                                        @endif
                                    </td>

                                    {{-- Aksi --}}
                                    <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <button type="button" 
                                                    @click="openHandoverModal({{ json_encode($hp) }})"
                                                    class="px-2.5 py-1 bg-white hover:bg-red-50 text-[#8F0A0D] text-[11px] font-bold rounded-lg border border-red-200 shadow-xs hover:border-[#8F0A0D] transition cursor-pointer">
                                                {{ ($hp->commercial_handover_status === 'Submitted' || $hp->commercial_handover_status === 'Approved' || $hp->ms_handover_status === 'Accepted') ? 'Edit Handover' : 'Serah Terima' }}
                                            </button>
                                            <a href="{{ route('projects.show', $hp->id) }}" 
                                               class="px-2.5 py-1 bg-white hover:bg-gray-50 text-gray-700 text-[11px] font-semibold rounded-lg border border-gray-200 shadow-xs transition cursor-pointer">
                                                Detail
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-12 text-center text-gray-400 text-xs">
                                        Belum ada kontrak yang siap diserahterimakan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($handoverProjects->hasPages())
                    <div class="p-4 border-t border-gray-100 bg-[#F8FAFC]">
                        {{ $handoverProjects->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>

    {{-- MODAL COMMERCIAL HANDOVER SUBMISSION (CLEAN PROFESSIONAL UI) --}}
    <template x-teleport="body">
        <div x-show="isHandoverModalOpen" 
             x-cloak 
             class="fixed inset-0 z-50 bg-[#0F172A]/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-5"
             @click.self="isHandoverModalOpen = false">
            <div class="bg-white rounded-2xl w-[820px] max-w-full max-h-[92vh] flex flex-col overflow-hidden shadow-2xl border border-[#E2E8F0] my-auto anim-fade-up">
                
                {{-- Fixed Header --}}
                <div class="flex items-center justify-between border-b border-[#E2E8F0] p-5 sm:p-6 pb-4 shrink-0 bg-white">
                    <div>
                        <div class="flex items-center gap-2 text-[#8F0A0D] text-[11px] font-bold uppercase tracking-wider mb-0.5">
                            <span class="w-2 h-2 rounded-full bg-[#8F0A0D]"></span>
                            <span>Sales Commercial Handover</span>
                        </div>
                        <h3 class="text-[16px] font-bold text-[#1E293B]" x-text="'Serah Terima: ' + (selectedProject.name || '')"></h3>
                    </div>
                    <button type="button" @click="isHandoverModalOpen = false" class="text-[#94A3B8] hover:text-[#1E293B] p-1.5 rounded-lg hover:bg-[#F1F5F9] transition cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Form Body with Scrollable Area --}}
                <form id="commercialHandoverForm" 
                      :action="'/sales/handover/' + selectedProject.id + '/submit'" 
                      method="POST" 
                      enctype="multipart/form-data" 
                      class="flex flex-col flex-1 min-h-0 overflow-hidden">
                    @csrf

                    <div class="p-5 sm:p-6 overflow-y-auto space-y-4 text-[12.5px] flex-1">
                        
                        {{-- Ringkasan Peluang --}}
                        <div class="p-3.5 bg-[#F8FAFC] rounded-xl border border-[#E2E8F0] flex flex-wrap items-center justify-between gap-2 text-xs">
                            <div>
                                <span class="text-[#64748B] block font-medium">Klien / Instansi:</span>
                                <span class="font-bold text-[#1E293B] text-[13px]" x-text="selectedProject.client || '—'"></span>
                            </div>
                            <div class="text-right">
                                <span class="text-[#64748B] block font-medium">Sales PIC Penyerah:</span>
                                <span class="font-bold text-[#8F0A0D] text-[13px]">{{ auth()->user()->name }}</span>
                            </div>
                        </div>

                        {{-- 1. PILIHAN TIPE PROYEK / TARGET HANDOVER --}}
                        <div>
                            <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-2">
                                1. Pilih Karakteristik &amp; Target Serah Terima <span class="text-[#8F0A0D]">*</span>
                            </label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                {{-- Option 1: PMO Implementasi --}}
                                <label class="flex items-start gap-3 p-3.5 rounded-xl border-2 cursor-pointer transition"
                                       :class="formTarget === 'pmo' ? 'border-[#8F0A0D] bg-red-50/30 text-gray-900 shadow-xs' : 'border-gray-200 hover:border-gray-300 text-gray-600 bg-white'">
                                    <input type="radio" name="handover_target" value="pmo" x-model="formTarget" class="mt-0.5 text-[#8F0A0D] focus:ring-[#8F0A0D]">
                                    <div>
                                        <div class="font-bold text-xs flex items-center gap-1.5">
                                            <span>Proyek Implementasi</span>
                                            <span class="text-[10px] px-1.5 py-0.5 rounded bg-gray-100 text-gray-700 font-bold">PMO Delivery</span>
                                        </div>
                                        <p class="text-[11px] text-gray-500 mt-1 leading-snug">
                                            Capex / Deployment: Pengadaan perangkat, instalasi kabel/rack, konfigurasi, migrasi, UAT, dan BAST 1.
                                        </p>
                                    </div>
                                </label>

                                {{-- Option 2: Managed Service --}}
                                <label class="flex items-start gap-3 p-3.5 rounded-xl border-2 cursor-pointer transition"
                                       :class="formTarget === 'managed_service' ? 'border-[#8F0A0D] bg-red-50/30 text-gray-900 shadow-xs' : 'border-gray-200 hover:border-gray-300 text-gray-600 bg-white'">
                                    <input type="radio" name="handover_target" value="managed_service" x-model="formTarget" class="mt-0.5 text-[#8F0A0D] focus:ring-[#8F0A0D]">
                                    <div>
                                        <div class="font-bold text-xs flex items-center gap-1.5">
                                            <span>Kontrak Managed Service</span>
                                            <span class="text-[10px] px-1.5 py-0.5 rounded bg-gray-100 text-gray-700 font-bold">Operate MS</span>
                                        </div>
                                        <p class="text-[11px] text-gray-500 mt-1 leading-snug">
                                            Opex / Retainer: Pemeliharaan rutin, monitoring, Preventive Maintenance berkala, SLA Uptime, &amp; On-Call Support.
                                        </p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        {{-- DOKUMEN KONTRAK UMUM --}}
                        <div class="pt-2 border-t border-gray-100">
                            <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-2">
                                2. Legalitas Kontrak &amp; Finansial
                            </label>

                            {{-- Dokumen PO & Tanggal --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 mb-3.5">
                                <div>
                                    <label class="block font-semibold text-[#64748B] text-[11px] mb-1">
                                        Nomor PO / SPK / Kontrak <span class="text-[#8F0A0D]">*</span>
                                    </label>
                                    <input type="text" name="po_spk_number" x-model="formPoNum" placeholder="Contoh: PO-CLI/2026/09/001" required
                                           class="w-full px-3.5 py-2 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition">
                                </div>

                                <div>
                                    <label class="block font-semibold text-[#64748B] text-[11px] mb-1">
                                        Tanggal PO / SPK <span class="text-[#8F0A0D]">*</span>
                                    </label>
                                    <input type="date" name="po_spk_date" x-model="formPoDate" required
                                           class="w-full px-3.5 py-2 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition cursor-pointer">
                                </div>
                            </div>

                            {{-- Nilai Final & Upload PO --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 mb-3.5">
                                <div>
                                    <label class="block font-semibold text-[#64748B] text-[11px] mb-1">
                                        Nilai Final Kontrak (Rp) <span class="text-[#8F0A0D]">*</span>
                                    </label>
                                    <input type="number" name="contract_value" x-model="formContractValue" min="0" step="1000" required
                                           class="w-full px-3.5 py-2 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-bold text-[#1E293B] focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition">
                                </div>

                                <div>
                                    <label class="block font-semibold text-[#64748B] text-[11px] mb-1">
                                        Upload PO / Kontrak (PDF/ZIP)
                                    </label>
                                    <input type="file" name="po_spk_file" accept=".pdf,.docx,.xlsx,.zip,.rar"
                                           class="w-full px-3 py-1.5 bg-white border border-[#CBD5E1] rounded-xl text-[11px] text-[#64748B] file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[11px] file:font-bold file:bg-[#8F0A0D] file:text-white hover:file:bg-[#73080A] cursor-pointer">
                                </div>
                            </div>

                            {{-- Termin Pembayaran --}}
                            <div class="mb-3.5">
                                <label class="block font-semibold text-[#64748B] text-[11px] mb-1">
                                    Billing Terms (Termin &amp; Skema Pembayaran) <span class="text-[#8F0A0D]">*</span>
                                </label>
                                <textarea name="billing_terms" x-model="formBilling" rows="2" placeholder="Contoh: Pembayaran bulanan / termin DP 30%, Delivery 50%, BAST 20%..." required
                                          class="w-full px-3.5 py-2 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition resize-none"></textarea>
                            </div>
                        </div>

                        {{-- 3. BIDANG SPESIFIK MANAGED SERVICE (CLEAN NEUTRAL CARD) --}}
                        <div x-show="formTarget === 'managed_service'" class="p-4 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0] space-y-3.5">
                            <div class="flex items-center gap-2 text-gray-800 font-bold text-xs uppercase tracking-wider">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#8F0A0D]"></span>
                                <span>Parameter Khusus Layanan Managed Service &amp; SLA</span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                <div>
                                    <label class="block font-semibold text-gray-700 text-[11px] mb-1">
                                        SLA Tier <span class="text-[#8F0A0D]">*</span>
                                    </label>
                                    <select name="sla_tier" x-model="formSlaTier"
                                            class="w-full px-3.5 py-2 bg-white border border-[#CBD5E1] rounded-xl text-[12.5px] font-bold text-gray-800 focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 cursor-pointer">
                                        <option value="Platinum">Platinum (24x7 MTTR 2 Jam, Uptime 99.9%)</option>
                                        <option value="Gold">Gold (8x5 MTTR 4 Jam, Uptime 99.5%)</option>
                                        <option value="Silver">Silver (8x5 Next Business Day, Uptime 99.0%)</option>
                                        <option value="Bronze">Bronze (Best Effort On-Call Support)</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block font-semibold text-gray-700 text-[11px] mb-1">
                                        Coverage Support Hours
                                    </label>
                                    <select name="sla_coverage_hours" x-model="formSlaCoverage"
                                            class="w-full px-3.5 py-2 bg-white border border-[#CBD5E1] rounded-xl text-[12.5px] font-semibold text-gray-800 focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 cursor-pointer">
                                        <option value="24x7">24x7 (24 Jam 7 Hari - Termasuk Hari Libur)</option>
                                        <option value="8x5">8x5 (Jam Kerja Senin - Jumat 08:00 - 17:00)</option>
                                        <option value="12x7">12x7 (08:00 - 20:00 Setiap Hari)</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                                <div>
                                    <label class="block font-semibold text-gray-700 text-[11px] mb-1">
                                        Frekuensi Preventive Maint.
                                    </label>
                                    <select name="maintenance_frequency" x-model="formMaintFreq"
                                            class="w-full px-3.5 py-2 bg-white border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-gray-800 focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 cursor-pointer">
                                        <option value="Monthly">Bulanan (Monthly Routine)</option>
                                        <option value="Quarterly">Triwulanan (Quarterly - 3 Bulan)</option>
                                        <option value="Bi-Annual">Semesteran (Bi-Annual - 6 Bulan)</option>
                                        <option value="Annual">Tahunan (Annual)</option>
                                        <option value="On-Demand">On-Demand / Incident Based</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block font-semibold text-gray-700 text-[11px] mb-1">
                                        Tanggal Mulai Layanan
                                    </label>
                                    <input type="date" name="service_start_date" x-model="formServiceStart"
                                           class="w-full px-3.5 py-2 bg-white border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 cursor-pointer">
                                </div>

                                <div>
                                    <label class="block font-semibold text-gray-700 text-[11px] mb-1">
                                        Tanggal Akhir Kontrak Layanan
                                    </label>
                                    <input type="date" name="service_end_date" x-model="formServiceEnd"
                                           class="w-full px-3.5 py-2 bg-white border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 cursor-pointer">
                                </div>
                            </div>
                        </div>

                        {{-- 4. KETENTUAN TEKNIS & SLA --}}
                        <div class="pt-2 border-t border-gray-100">
                            <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-2">
                                3. Komitmen Layanan &amp; Batasan Scope (Exclusions)
                            </label>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 mb-3.5">
                                <div>
                                    <label class="block font-semibold text-[#64748B] text-[11px] mb-1">
                                        SLA &amp; Garansi Komitmen
                                    </label>
                                    <input type="text" name="sla_commitment" x-model="formSla" placeholder="Contoh: SLA Garansi Resmi Prinsipal 1 Tahun, Support Jam Kerja"
                                           class="w-full px-3.5 py-2 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition">
                                </div>

                                <div>
                                    <label class="block font-semibold text-[#64748B] text-[11px] mb-1">
                                        Commercial Terms
                                    </label>
                                    <input type="text" name="commercial_terms" x-model="formCommercialTerms" placeholder="Franco Jakarta, TOP 30 Hari"
                                           class="w-full px-3.5 py-2 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                <div>
                                    <label class="block font-semibold text-[#64748B] text-[11px] mb-1">
                                        Komitmen Khusus dari Sales
                                    </label>
                                    <textarea name="special_commitment" x-model="formSpecialCommitment" rows="2" placeholder="Termasuk pendampingan User Acceptance Testing (UAT) dan transfer knowledge..."
                                              class="w-full px-3.5 py-2 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition resize-none"></textarea>
                                </div>

                                <div>
                                    <label class="block font-semibold text-[#64748B] text-[11px] mb-1">
                                        Exclusions (Batasan di Luar Scope)
                                    </label>
                                    <textarea name="exclusions" x-model="formExclusions" rows="2" placeholder="Pengadaan kabel, rack, atau perangkat tambahan di luar BoQ dikenakan PO terpisah..."
                                              class="w-full px-3.5 py-2 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition resize-none"></textarea>
                                </div>
                            </div>
                        </div>

                        {{-- 4. LAMPIRAN DOKUMEN SERAH TERIMA KOMERSIAL (STAGE 1 KEY DOCUMENTS) --}}
                        <div class="pt-2 border-t border-gray-100">
                            <div class="flex items-center justify-between mb-2">
                                <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px]">
                                    4. Dokumen Alur Serah Terima (Stage 1: Commercial Handover Package)
                                </label>
                                <span class="text-[10.5px] font-semibold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-md">
                                    Format: PDF, DOCX, XLSX, ZIP (Maks. 25MB)
                                </span>
                            </div>

                            <p class="text-[11px] text-gray-500 mb-3">
                                Unggah berkas dokumen pendukung komersial. Dokumen yang diunggah otomatis tersimpan di <strong>Repositori Dokumen Proyek (Stage 1: Commercial)</strong>.
                            </p>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                {{-- 1. Customer Requirement --}}
                                <div class="p-3 bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl hover:border-slate-300 transition">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-[11.5px] font-bold text-slate-800">1. Customer Requirement (TOR / KAK)</span>
                                        <span class="text-[9.5px] font-bold text-[#8F0A0D] bg-red-50 px-1.5 py-0.5 rounded">Wajib</span>
                                    </div>
                                    <p class="text-[10.5px] text-slate-500 mb-2 leading-tight">TOR, Kerangka Acuan Kerja, atau spesifikasi kebutuhan klien.</p>
                                    <input type="file" name="doc_customer_requirement" accept=".pdf,.docx,.xlsx,.zip,.rar"
                                           class="w-full px-2.5 py-1 bg-white border border-[#CBD5E1] rounded-lg text-[11px] text-[#64748B] file:mr-2 file:py-0.5 file:px-2 file:rounded-md file:border-0 file:text-[10.5px] file:font-bold file:bg-slate-700 file:text-white hover:file:bg-slate-800 cursor-pointer">
                                </div>

                                {{-- 2. Commercial Proposal --}}
                                <div class="p-3 bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl hover:border-slate-300 transition">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-[11.5px] font-bold text-slate-800">2. Commercial Proposal (Quotation)</span>
                                        <span class="text-[9.5px] font-bold text-[#8F0A0D] bg-red-50 px-1.5 py-0.5 rounded">Wajib</span>
                                    </div>
                                    <p class="text-[10.5px] text-slate-500 mb-2 leading-tight">Penawaran harga resmi (Quotation) & commercial terms final.</p>
                                    <input type="file" name="doc_commercial_proposal" accept=".pdf,.docx,.xlsx,.zip,.rar"
                                           class="w-full px-2.5 py-1 bg-white border border-[#CBD5E1] rounded-lg text-[11px] text-[#64748B] file:mr-2 file:py-0.5 file:px-2 file:rounded-md file:border-0 file:text-[10.5px] file:font-bold file:bg-slate-700 file:text-white hover:file:bg-slate-800 cursor-pointer">
                                </div>

                                {{-- 3. Negotiation Record & MoM --}}
                                <div class="p-3 bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl hover:border-slate-300 transition">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-[11.5px] font-bold text-slate-800">3. Negotiation Record &amp; MoM</span>
                                        <span class="text-[9.5px] font-medium text-slate-500 bg-slate-200 px-1.5 py-0.5 rounded">Opsional</span>
                                    </div>
                                    <p class="text-[10.5px] text-slate-500 mb-2 leading-tight">Berita acara negosiasi harga, risalah rapat klarifikasi (MoM).</p>
                                    <input type="file" name="doc_negotiation_record" accept=".pdf,.docx,.xlsx,.zip,.rar"
                                           class="w-full px-2.5 py-1 bg-white border border-[#CBD5E1] rounded-lg text-[11px] text-[#64748B] file:mr-2 file:py-0.5 file:px-2 file:rounded-md file:border-0 file:text-[10.5px] file:font-bold file:bg-slate-700 file:text-white hover:file:bg-slate-800 cursor-pointer">
                                </div>

                                {{-- 4. RFI / RFP / RFQ --}}
                                <div class="p-3 bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl hover:border-slate-300 transition">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-[11.5px] font-bold text-slate-800">4. Dokumen RFI / RFP / RFQ</span>
                                        <span class="text-[9.5px] font-medium text-slate-500 bg-slate-200 px-1.5 py-0.5 rounded">Opsional</span>
                                    </div>
                                    <p class="text-[10.5px] text-slate-500 mb-2 leading-tight">Dokumen permintaan tender / penawaran resmi dari klien.</p>
                                    <input type="file" name="doc_rfi_rfp" accept=".pdf,.docx,.xlsx,.zip,.rar"
                                           class="w-full px-2.5 py-1 bg-white border border-[#CBD5E1] rounded-lg text-[11px] text-[#64748B] file:mr-2 file:py-0.5 file:px-2 file:rounded-md file:border-0 file:text-[10.5px] file:font-bold file:bg-slate-700 file:text-white hover:file:bg-slate-800 cursor-pointer">
                                </div>

                                {{-- 5. Commercial Handover Package ZIP --}}
                                <div class="p-3 bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl hover:border-slate-300 transition sm:col-span-2">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-[11.5px] font-bold text-slate-800">5. Commercial Handover Package (Berkas Bundel / Arsip Tambahan)</span>
                                        <span class="text-[9.5px] font-medium text-slate-500 bg-slate-200 px-1.5 py-0.5 rounded">Opsional</span>
                                    </div>
                                    <p class="text-[10.5px] text-slate-500 mb-2 leading-tight">Paket berkas lengkap serah terima komersial ke tim teknis (ZIP/RAR/PDF).</p>
                                    <input type="file" name="doc_commercial_package" accept=".pdf,.docx,.xlsx,.zip,.rar"
                                           class="w-full px-2.5 py-1 bg-white border border-[#CBD5E1] rounded-lg text-[11px] text-[#64748B] file:mr-2 file:py-0.5 file:px-2 file:rounded-md file:border-0 file:text-[10.5px] file:font-bold file:bg-slate-700 file:text-white hover:file:bg-slate-800 cursor-pointer">
                                </div>
                            </div>
                        </div>

                    </div>

                    {{-- Fixed Footer --}}
                    <div class="flex items-center justify-between p-4 px-6 border-t border-[#E2E8F0] bg-[#F8FAFC] shrink-0">
                        <div class="text-[11.5px] text-gray-500 font-medium">
                            Target Penyerahan: <strong class="text-gray-900" x-text="formTarget === 'managed_service' ? 'Tim Managed Service (Operate)' : 'Tim PMO & Delivery (Deliver)'"></strong>
                        </div>
                        <div class="flex items-center gap-2.5">
                            <button type="button" 
                                    @click="isHandoverModalOpen = false" 
                                    class="px-4 py-2 text-[12.5px] font-bold text-[#475569] hover:bg-[#F1F5F9] border border-[#CBD5E1] rounded-xl transition cursor-pointer">
                                Batal
                            </button>
                            <button type="submit" 
                                    class="px-5 py-2 text-[12.5px] font-bold text-white bg-[#8F0A0D] hover:bg-[#73080A] rounded-xl shadow-md transition cursor-pointer"
                                    x-text="formTarget === 'managed_service' ? 'Kirim Serah Terima ke Managed Service' : 'Kirim Serah Terima ke PMO'">
                            </button>
                        </div>
                    </div>

                </form>

            </div>
        </div>
    </template>

</div>
@endsection

@push('scripts')
<script>
    function commercialHandoverPage() {
        return {
            isHandoverModalOpen: false,
            selectedProject: {},
            formTarget: 'pmo',
            formPoNum: '',
            formPoDate: '',
            formContractValue: 0,
            formBilling: '',
            formSla: '',
            formCommercialTerms: '',
            formSpecialCommitment: '',
            formExclusions: '',
            formSlaTier: 'Gold',
            formSlaCoverage: '24x7',
            formMaintFreq: 'Monthly',
            formServiceStart: '',
            formServiceEnd: '',

            openHandoverModal(proj) {
                this.selectedProject = proj;
                this.formTarget = proj.handover_target || (proj.stage === 'Operate' ? 'managed_service' : 'pmo');
                this.formPoNum = proj.po_spk_number || '';
                this.formPoDate = proj.po_spk_date ? proj.po_spk_date.split('T')[0] : '{{ date('Y-m-d') }}';
                this.formContractValue = proj.contract_value || 0;
                this.formBilling = proj.billing_terms || (this.formTarget === 'managed_service' ? 'Pembayaran tagihan rutin per bulan (Net 30 hari) setelah rekap SLA bulanan terbit.' : 'DP 30% setelah PO terbit, 50% setelah delivery barang on-site, 20% pelunasan setelah BAST selesai.');
                this.formSla = proj.sla_commitment || (this.formTarget === 'managed_service' ? 'SLA Availability 99.5%, MTTR 4 Jam, Response Time 15-30 Menit.' : 'SLA Garansi Resmi Pabrik 1 Tahun, Layanan Support Jam Kerja (8x5) Response Time 4 Jam.');
                this.formCommercialTerms = proj.commercial_terms || 'Harga Franco Jakarta, Pembayaran Net 30 Hari (TOP 30) setelah invoice diterima.';
                this.formSpecialCommitment = proj.special_commitment || 'Termasuk pendampingan User Acceptance Testing (UAT) dan transfer knowledge.';
                this.formExclusions = proj.exclusions || 'Pengadaan kabel, rack, atau penggantian suku cadang di luar BoQ dikenakan biaya terpisah.';
                this.formSlaTier = proj.sla_tier || 'Gold';
                this.formSlaCoverage = proj.sla_coverage_hours || '24x7';
                this.formMaintFreq = proj.maintenance_frequency || 'Monthly';
                this.formServiceStart = proj.service_start_date ? proj.service_start_date.split('T')[0] : (proj.po_spk_date ? proj.po_spk_date.split('T')[0] : '{{ date('Y-m-d') }}');
                this.formServiceEnd = proj.service_end_date ? proj.service_end_date.split('T')[0] : '';
                this.isHandoverModalOpen = true;
            }
        }
    }
</script>
@endpush
