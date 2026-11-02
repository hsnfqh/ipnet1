{{-- 3 SUMMARY METRIC CARDS --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 anim-fade-up anim-delay-1">
    {{-- Card 1 --}}
    <div class="ipnet-card p-5 flex flex-col justify-between space-y-3">
        <div class="flex items-center justify-between">
            <span class="text-[11px] font-bold text-[#64748B] uppercase tracking-wider">Total Kontrak Closed Deal</span>
            <div class="w-9 h-9 rounded-xl bg-red-50 border border-red-100 text-[#8F0A0D] flex items-center justify-center shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
        </div>
        <div>
            <div class="text-2xl sm:text-3xl font-black text-[#1E293B] tracking-tight">
                {{ $totalHandoverCount ?? $handoverProjects->total() }} <span class="text-xs font-semibold text-[#94A3B8]">Kontrak PO</span>
            </div>
            <p class="text-[11.5px] text-[#64748B] font-medium mt-1">Siap diserahterimakan ke tim teknis</p>
        </div>
    </div>

    {{-- Card 2 --}}
    <div class="ipnet-card p-5 flex flex-col justify-between space-y-3">
        <div class="flex items-center justify-between">
            <span class="text-[11px] font-bold text-[#64748B] uppercase tracking-wider">PMO Delivery (Tipe 1)</span>
            <div class="w-9 h-9 rounded-xl bg-blue-50 border border-blue-100 text-blue-700 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
            </div>
        </div>
        <div>
            <div class="text-2xl sm:text-3xl font-black text-[#1E293B] tracking-tight">
                {{ $pmoTargetCount ?? 0 }} <span class="text-xs font-semibold text-[#94A3B8]">Proyek Implementasi</span>
            </div>
            <p class="text-[11.5px] text-[#64748B] font-medium mt-1">Tahap Deliver &bull; Instalasi &amp; UAT</p>
        </div>
    </div>

    {{-- Card 3 --}}
    <div class="ipnet-card p-5 flex flex-col justify-between space-y-3">
        <div class="flex items-center justify-between">
            <span class="text-[11px] font-bold text-[#64748B] uppercase tracking-wider">Managed Service (Tipe 2)</span>
            <div class="w-9 h-9 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
        </div>
        <div>
            <div class="text-2xl sm:text-3xl font-black text-[#1E293B] tracking-tight">
                {{ $msTargetCount ?? 0 }} <span class="text-xs font-semibold text-[#94A3B8]">Kontrak Layanan</span>
            </div>
            <p class="text-[11.5px] text-[#64748B] font-medium mt-1">Tahap Operate &bull; Monitoring &amp; SLA</p>
        </div>
    </div>
</div>

<!-- Filter & Action Bar -->
<div class="ipnet-card p-4 anim-fade-up anim-delay-2">
    <form method="GET" action="{{ route('sales.pipeline.index') }}" class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <input type="hidden" name="tab" value="handover">
        <div class="flex flex-col sm:flex-row flex-wrap items-center gap-2.5 w-full sm:w-auto">
            <div class="relative w-full sm:w-72">
                <svg class="w-4 h-4 absolute left-3.5 top-3 text-[#94A3B8]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" 
                       name="search" 
                       value="{{ $search ?? '' }}"
                       placeholder="Cari proyek, klien, nomor PO/SPK..." 
                       class="w-full px-3.5 py-2 pl-9 rounded-xl border border-[#CBD5E1] text-xs font-semibold text-[#1E293B] bg-white focus:outline-none focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D] transition-all shadow-2xs">
            </div>

            <select name="target" onchange="this.form.submit()" 
                    class="w-full sm:w-56 px-3.5 py-2 rounded-xl border border-[#CBD5E1] text-xs font-semibold text-[#1E293B] bg-white focus:outline-none focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D] transition-all cursor-pointer shadow-2xs">
                <option value="">Semua Tipe Serah Terima</option>
                <option value="pmo" {{ ($filterHandoverTarget ?? request('target')) === 'pmo' ? 'selected' : '' }}>Tipe 1: Proyek Implementasi (PMO)</option>
                <option value="managed_service" {{ ($filterHandoverTarget ?? request('target')) === 'managed_service' ? 'selected' : '' }}>Tipe 2: Managed Service (Operate)</option>
            </select>

            <select name="handover_status" onchange="this.form.submit()" 
                    class="w-full sm:w-52 px-3.5 py-2 rounded-xl border border-[#CBD5E1] text-xs font-semibold text-[#1E293B] bg-white focus:outline-none focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D] transition-all cursor-pointer shadow-2xs">
                <option value="">Semua Status Serah Terima</option>
                <option value="Draft" {{ ($filterHandoverStatus ?? request('handover_status')) == 'Draft' ? 'selected' : '' }}>Draft Handover</option>
                <option value="Submitted" {{ ($filterHandoverStatus ?? request('handover_status')) == 'Submitted' ? 'selected' : '' }}>Submitted ke Tim Teknis</option>
                <option value="Approved" {{ ($filterHandoverStatus ?? request('handover_status')) == 'Approved' ? 'selected' : '' }}>Approved / Active</option>
            </select>
        </div>

        @if(($search ?? '') || ($filterHandoverStatus ?? request('handover_status')) || ($filterHandoverTarget ?? request('target')))
            <a href="{{ route('sales.pipeline.index', ['tab' => 'handover']) }}" 
               class="px-3 py-2 text-xs font-bold text-[#8F0A0D] hover:underline self-center shrink-0">
                Reset Filter
            </a>
        @endif
    </form>
</div>

{{-- Table Deals Handover --}}
<div class="ipnet-card overflow-hidden anim-fade-up anim-delay-3">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs border-collapse">
            <thead class="bg-[#F8FAFC] border-b border-[#E2E8F0] text-[#64748B] uppercase text-[11px] font-bold">
                <tr>
                    <th class="py-3.5 px-5">Proyek &amp; Klien</th>
                    <th class="py-3.5 px-5">Tipe &amp; Target Handover</th>
                    <th class="py-3.5 px-5">Nomor &amp; Tanggal PO/SPK</th>
                    <th class="py-3.5 px-5 text-right">Nilai Kontrak</th>
                    <th class="py-3.5 px-5">Ketentuan SLA / Termin</th>
                    <th class="py-3.5 px-5 text-center">Status Handover</th>
                    <th class="py-3.5 px-5 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#F1F5F9] font-medium text-[#1E293B]">
                @forelse($handoverProjects as $hp)
                    @php
                        $isMs = ($hp->handover_target === 'managed_service' 
                            || $hp->stage === 'Operate'
                            || str_contains(strtolower($hp->name), 'pemeliharaan')
                            || str_contains(strtolower($hp->name), 'maintenance')
                            || str_contains(strtolower($hp->project_type ?? ''), 'managed service'));
                        
                        if ($isMs) {
                            $badge = match($hp->ms_handover_status) {
                                'Accepted'  => ['bg' => 'bg-emerald-50 text-emerald-700 border border-emerald-200', 'label' => 'Active di Managed Service'],
                                'Submitted' => ['bg' => 'bg-slate-100 text-slate-700 border border-slate-200', 'label' => 'Submitted ke MS Lead'],
                                default     => ['bg' => 'bg-amber-50 text-amber-800 border border-amber-200', 'label' => 'Draft MS Handover'],
                            };
                        } else {
                            $badge = match($hp->commercial_handover_status) {
                                'Approved'  => ['bg' => 'bg-emerald-50 text-emerald-700 border border-emerald-200', 'label' => 'Approved by PMO'],
                                'Submitted' => ['bg' => 'bg-blue-50 text-blue-700 border border-blue-200', 'label' => 'Submitted to Delivery'],
                                default     => ['bg' => 'bg-amber-50 text-amber-800 border border-amber-200', 'label' => 'Draft Handover'],
                            };
                        }
                    @endphp
                    <tr class="hover:bg-[#F8FAFC] transition">
                        {{-- Proyek & Klien --}}
                        <td class="py-3.5 px-5">
                            <a href="{{ route('projects.show', $hp->id) }}" class="font-bold text-[#1E293B] hover:text-[#8F0A0D] text-[13px] block">
                                {{ $hp->name }}
                            </a>
                            <div class="text-[11.5px] text-[#64748B] font-medium mt-0.5">
                                {{ $hp->client }} &bull; Sales: {{ $hp->sales_name ?: 'Sales Team' }}
                            </div>
                        </td>

                        {{-- Tipe & Target Handover --}}
                        <td class="py-3.5 px-5 whitespace-nowrap">
                            @if($isMs)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-bold bg-slate-100 text-slate-800 border border-slate-200">
                                    <span>Managed Service</span>
                                </span>
                                <div class="text-[11px] text-[#64748B] font-medium mt-0.5">
                                    Tier: {{ $hp->sla_tier ?: 'Gold' }} ({{ $hp->sla_coverage_hours ?: '24x7' }})
                                </div>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-bold bg-slate-100 text-slate-800 border border-slate-200">
                                    <span>Proyek Implementasi</span>
                                </span>
                                <div class="text-[11px] text-[#64748B] font-medium mt-0.5">
                                    Target: PMO &amp; Delivery Team
                                </div>
                            @endif
                        </td>

                        {{-- Nomor PO / SPK --}}
                        <td class="py-3.5 px-5 whitespace-nowrap">
                            @if($hp->po_spk_number)
                                <div class="font-mono font-bold text-[#1E293B] text-[12px]">{{ $hp->po_spk_number }}</div>
                                <div class="text-[11px] text-[#64748B]">{{ $hp->po_spk_date ? \Carbon\Carbon::parse($hp->po_spk_date)->format('d M Y') : '—' }}</div>
                            @else
                                <span class="text-[#94A3B8] italic text-[11px]">Belum Input PO/SPK</span>
                            @endif
                        </td>

                        {{-- Nilai Kontrak --}}
                        <td class="py-3.5 px-5 text-right whitespace-nowrap">
                            <div class="font-extrabold text-[#8F0A0D] text-[13px]">
                                {{ $hp->contract_value > 0 ? 'Rp ' . number_format($hp->contract_value, 0, ',', '.') : '—' }}
                            </div>
                            <div class="text-[10.5px] text-[#64748B]">
                                Stage: {{ $hp->sales_stage }}
                            </div>
                        </td>

                        {{-- Billing & SLA --}}
                        <td class="py-3.5 px-5 max-w-xs">
                            @if($isMs)
                                <div class="text-[11.5px] font-semibold text-[#1E293B] truncate" title="{{ $hp->sla_commitment }}">
                                    SLA: {{ $hp->sla_commitment ?: 'SLA 99.5% Uptime & Response 15-30m' }}
                                </div>
                                <div class="text-[10.5px] text-[#64748B]">
                                    Jadwal: {{ $hp->maintenance_frequency ?: 'Bulanan (Monthly)' }}
                                </div>
                            @else
                                <div class="text-[11.5px] font-semibold text-[#1E293B] truncate" title="{{ $hp->billing_terms }}">
                                    {{ $hp->billing_terms ?: 'Termin belum diset' }}
                                </div>
                                <div class="text-[10.5px] text-[#64748B] truncate" title="{{ $hp->sla_commitment }}">
                                    Garansi: {{ $hp->sla_commitment ?: 'Standar 8x5' }}
                                </div>
                            @endif
                        </td>

                        {{-- Status Handover --}}
                        <td class="py-3.5 px-5 text-center whitespace-nowrap">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10.5px] font-bold {{ $badge['bg'] }}">
                                <span>{{ $badge['label'] }}</span>
                            </span>
                            @if($hp->commercial_handover_at)
                                <div class="text-[10px] text-[#94A3B8] mt-0.5">
                                    {{ \Carbon\Carbon::parse($hp->commercial_handover_at)->format('d/m/Y H:i') }}
                                </div>
                            @endif
                        </td>

                        {{-- Aksi --}}
                        <td class="py-3.5 px-5 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-1.5">
                                @if($hp->commercial_handover_status === 'Submitted' || $hp->commercial_handover_status === 'Approved' || $hp->ms_handover_status === 'Accepted' || !empty($hp->handover_data))
                                    <a href="{{ route('sales.handover.document', $hp->id) }}" target="_blank"
                                       class="px-2.5 py-1.5 bg-white hover:bg-[#F8FAFC] text-[#1E293B] text-[11px] font-bold rounded-lg border border-[#CBD5E1] hover:border-[#8F0A0D] hover:text-[#8F0A0D] shadow-2xs transition cursor-pointer inline-flex items-center gap-1"
                                       title="Lihat Berita Acara Serah Terima">
                                        <svg class="w-3.5 h-3.5 text-[#8F0A0D]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        <span>BA Serah Terima</span>
                                    </a>
                                @endif
                                <button type="button" 
                                        @click="openHandoverModal({{ json_encode($hp) }})"
                                        class="px-2.5 py-1.5 bg-gradient-to-r from-[#8F0A0D] to-[#B81525] hover:from-[#7A080A] hover:to-[#A0121F] text-white text-[11px] font-bold rounded-lg shadow-2xs transition cursor-pointer inline-flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    <span>{{ ($hp->commercial_handover_status === 'Submitted' || $hp->commercial_handover_status === 'Approved' || $hp->ms_handover_status === 'Accepted') ? 'Edit' : 'Serah Terima' }}</span>
                                </button>
                                <a href="{{ route('projects.show', $hp->id) }}" 
                                   class="px-2.5 py-1.5 bg-white hover:bg-[#F8FAFC] text-[#64748B] hover:text-[#1E293B] text-[11px] font-bold rounded-lg border border-[#CBD5E1] shadow-2xs transition cursor-pointer">
                                    Detail
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-[#94A3B8] text-xs">
                            Belum ada kontrak yang siap diserahterimakan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($handoverProjects->hasPages())
        <div class="p-4 border-t border-[#F1F5F9] bg-[#F8FAFC]">
            {{ $handoverProjects->links() }}
        </div>
    @endif
</div>

{{-- MODAL COMMERCIAL HANDOVER SUBMISSION (CLEAN PROFESSIONAL UI) --}}
<template x-teleport="body">
    <div x-show="isHandoverModalOpen" 
         x-cloak 
         class="fixed inset-0 z-50 bg-[#0F172A]/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-5"
         @click.self="isHandoverModalOpen = false">
        <div class="bg-white rounded-2xl w-[900px] max-w-5xl max-h-[92vh] flex flex-col overflow-hidden shadow-2xl border border-[#E2E8F0] my-auto anim-fade-up">
            
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
                  :action="formAction || ('/sales/handover/' + (selectedProject ? selectedProject.id : '') + '/submit')" 
                  @submit="if (!formAction || formAction.includes('undefined')) { if (selectedProject && selectedProject.id) { formAction = '/sales/handover/' + selectedProject.id + '/submit'; $el.action = formAction; } }"
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
                        <input type="hidden" name="handover_target" :value="formTarget">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            {{-- Option 1: PMO Implementasi --}}
                            <label @click="toggleFormTarget('pmo')"
                                   class="flex items-start gap-3 p-3.5 rounded-xl border-2 cursor-pointer transition shadow-2xs select-none"
                                   :class="formTargets.includes('pmo') ? '!border-[#8F0A0D] !bg-red-50/30 text-gray-900 shadow-xs ring-1 ring-[#8F0A0D]/30' : 'border-gray-200 hover:border-gray-300 text-gray-600 bg-white'">
                                <input type="checkbox" value="pmo" :checked="formTargets.includes('pmo')" @click.stop="toggleFormTarget('pmo')" class="mt-0.5 w-4 h-4 rounded text-[#8F0A0D] accent-[#8F0A0D] cursor-pointer">
                                <div>
                                    <div class="font-bold text-xs flex items-center gap-1.5">
                                        <span :class="formTargets.includes('pmo') ? 'text-[#8F0A0D] font-extrabold' : 'text-slate-800'">Proyek Implementasi</span>
                                        <span class="text-[10px] px-1.5 py-0.5 rounded bg-gray-100 text-gray-700 font-bold">PMO Delivery</span>
                                    </div>
                                    <p class="text-[11px] text-gray-500 mt-1 leading-snug">
                                        Capex / Deployment: Pengadaan perangkat, instalasi kabel/rack, konfigurasi, migrasi, UAT, dan BAST 1.
                                    </p>
                                </div>
                            </label>

                            {{-- Option 2: Managed Service --}}
                            <label @click="toggleFormTarget('managed_service')"
                                   class="flex items-start gap-3 p-3.5 rounded-xl border-2 cursor-pointer transition shadow-2xs select-none"
                                   :class="formTargets.includes('managed_service') ? '!border-[#8F0A0D] !bg-red-50/30 text-gray-900 shadow-xs ring-1 ring-[#8F0A0D]/30' : 'border-gray-200 hover:border-gray-300 text-gray-600 bg-white'">
                                <input type="checkbox" value="managed_service" :checked="formTargets.includes('managed_service')" @click.stop="toggleFormTarget('managed_service')" class="mt-0.5 w-4 h-4 rounded text-[#8F0A0D] accent-[#8F0A0D] cursor-pointer">
                                <div>
                                    <div class="font-bold text-xs flex items-center gap-1.5">
                                        <span :class="formTargets.includes('managed_service') ? 'text-[#8F0A0D] font-extrabold' : 'text-slate-800'">Kontrak Managed Service</span>
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
                    <div x-show="formTarget === 'managed_service' || formTarget === 'both'" class="p-4 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0] space-y-3.5">
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

                    {{-- 5. PENGESAHAN & TANDA TANGAN PENYERAH (SALES PIC) --}}
                    <div class="pt-3 border-t border-gray-100">
                        <div class="flex items-center justify-between mb-2">
                            <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px]">
                                5. Pengesahan &amp; Tanda Tangan Digital Penyerah (Sales PIC)
                            </label>
                            <span class="text-[10.5px] font-bold text-[#8F0A0D] bg-red-50 px-2 py-0.5 rounded-md border border-red-100">
                                Otomatis Tersambung dari Settings
                            </span>
                        </div>

                        <div class="p-4 rounded-xl bg-white border border-[#CBD5E1] shadow-2xs space-y-3">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-xs text-white shadow-xs"
                                         style="background: linear-gradient(135deg, #B91C1C 0%, #8F0A0D 60%, #750608 100%);">
                                        {{ auth()->user()->initials }}
                                    </div>
                                    <div>
                                        <h5 class="text-[13px] font-bold text-[#1E293B]">{{ auth()->user()->name }}</h5>
                                        <p class="text-[11px] text-[#64748B]">{{ auth()->user()->position ?? auth()->user()->role_label }} &bull; PT IP Network Solusindo</p>
                                    </div>
                                </div>

                                <div class="text-left sm:text-right">
                                    <span class="text-[10.5px] text-slate-400 block font-medium">Tanggal Penyerahan</span>
                                    <span class="text-[12px] font-bold text-slate-800">{{ date('d M Y') }}</span>
                                </div>
                            </div>

                            {{-- Pilihan Format Stempel Tanda Tangan Digital --}}
                            <input type="hidden" name="signature_type" :value="formSignatureType">
                            <div class="p-3 rounded-lg bg-slate-50 border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
                                <div class="text-[11px] font-bold text-slate-700 flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-[#8F0A0D]"></span>
                                    <span>FORMAT PENGESAHAN DOKUMEN:</span>
                                </div>
                                <div class="inline-flex rounded-lg bg-white p-0.5 border border-slate-300 shadow-2xs gap-1">
                                    <button type="button" 
                                            @click="formSignatureType = 'signature_qr'"
                                            :class="formSignatureType === 'signature_qr' ? 'bg-[#8F0A0D] text-white shadow-2xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100'"
                                            class="px-2.5 py-1 rounded text-[11px] font-bold transition cursor-pointer">
                                        TTD + QR Code
                                    </button>
                                    <button type="button" 
                                            @click="formSignatureType = 'signature_only'"
                                            :class="formSignatureType === 'signature_only' ? 'bg-[#8F0A0D] text-white shadow-2xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100'"
                                            class="px-2.5 py-1 rounded text-[11px] font-bold transition cursor-pointer">
                                        Tanda Tangan Saja
                                    </button>
                                    <button type="button" 
                                            @click="formSignatureType = 'qr_only'"
                                            :class="formSignatureType === 'qr_only' ? 'bg-[#8F0A0D] text-white shadow-2xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100'"
                                            class="px-2.5 py-1 rounded text-[11px] font-bold transition cursor-pointer">
                                        QR Code Saja
                                    </button>
                                </div>
                            </div>

                            {{-- Pratinjau Tanda Tangan Sales Sesuai Layout Referensi --}}
                            @if(auth()->user()->has_signature)
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between p-3.5 rounded-lg bg-[#F8FAFC] border border-slate-200 gap-3">
                                <div class="flex items-center gap-3">
                                    {{-- Widget Stampel Dinamis --}}
                                    <div class="inline-flex items-center gap-2 select-none text-left align-middle bg-white p-1 rounded-md border border-slate-200 shadow-2xs">
                                        {{-- QR Code Box --}}
                                        <div x-show="formSignatureType === 'signature_qr' || formSignatureType === 'qr_only'" class="w-13 h-13 shrink-0 p-1 bg-white border border-slate-300 rounded flex items-center justify-center shadow-2xs">
                                            {!! auth()->user()->signature_qr_svg !!}
                                        </div>
                                        {{-- Framed Stroke Box --}}
                                        <div x-show="formSignatureType === 'signature_qr' || formSignatureType === 'signature_only'" class="relative px-2.5 pt-2 pb-1 border border-slate-700 bg-white rounded min-w-[130px] max-w-[160px] min-h-[54px] flex flex-col justify-between shadow-2xs">
                                            <div class="absolute -top-2 left-1.5 px-1 bg-white text-[7.5px] font-mono font-bold text-slate-700 tracking-wide flex items-center gap-1 border border-slate-300 rounded-xs">
                                                <span class="w-1.5 h-1.5 rounded-full bg-[#8F0A0D]"></span>
                                                <span>digitally signed</span>
                                            </div>
                                            <div class="flex-1 flex items-center justify-center my-0.5 min-h-[28px]">
                                                <img src="{{ auth()->user()->signature_data }}" alt="Tanda Tangan" class="max-h-7 max-w-[105px] object-contain filter drop-shadow-2xs">
                                            </div>
                                            <div class="text-[7px] font-mono text-slate-500 tracking-tight text-center border-t border-dashed border-slate-200 pt-0.5 truncate">
                                                dsign.ipnet.id/{{ auth()->user()->signature_qr_token }}
                                            </div>
                                        </div>
                                    </div>

                                    <div>
                                        <div class="flex items-center gap-1.5">
                                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                            <span class="text-[11.5px] font-bold text-emerald-700">Tanda Tangan Terverifikasi &amp; Siap Dibubuhkan</span>
                                        </div>
                                        <p class="text-[10px] text-slate-500 font-mono mt-0.5">Token Audit: <span class="text-[#8F0A0D] font-bold">{{ auth()->user()->signature_qr_token }}</span></p>
                                    </div>
                                </div>
                                <a href="{{ route('settings.index') }}#signature" target="_blank" class="text-[11px] font-bold text-[#8F0A0D] hover:underline shrink-0">
                                    Atur di Settings &rarr;
                                </a>
                            </div>
                            @else
                            <div class="p-3 rounded-lg bg-amber-50 border border-amber-200 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    <span class="text-[11.5px] text-amber-800 font-medium">Anda belum menyimpan tanda tangan digital. Dokumen akan diajukan dengan otentikasi akun login Anda.</span>
                                </div>
                                <a href="{{ route('settings.index') }}" target="_blank" class="text-[11px] font-bold text-[#8F0A0D] underline shrink-0 ml-2">
                                    Atur di Settings
                                </a>
                            </div>
                            @endif

                            {{-- Pernyataan Penyerah --}}
                            <label class="flex items-start gap-2.5 pt-1 cursor-pointer select-none">
                                <input type="checkbox" required checked class="mt-0.5 w-4 h-4 text-[#8F0A0D] accent-[#8F0A0D] rounded cursor-pointer">
                                <span class="text-[11.5px] text-slate-700 leading-snug">
                                    Saya menyatakan dengan sadar bahwa seluruh data komersial, kontrak PO/SPK, dan lingkup pekerjaan di atas telah valid serta siap diserahterimakan kepada tim teknis pelaksanaan.
                                </span>
                            </label>
                        </div>
                    </div>

                </div>

                {{-- Fixed Footer (Semua Tombol 1 Baris Sejajar & Rapi) --}}
                <div class="flex items-center justify-between p-4 px-6 border-t border-[#E2E8F0] bg-[#F8FAFC] shrink-0 gap-3">
                    <div class="text-[11.5px] text-gray-500 font-medium shrink min-w-0 truncate">
                        Target Penyerahan: <strong class="text-gray-900" x-text="formTarget === 'managed_service' ? 'Tim Managed Service (Operate)' : 'Tim PMO & Delivery (Deliver)'"></strong>
                    </div>
                    <div class="flex items-center gap-2 shrink-0 whitespace-nowrap">
                        <template x-if="selectedProject && (selectedProject.commercial_handover_status === 'Submitted' || selectedProject.commercial_handover_status === 'Approved' || selectedProject.ms_handover_status === 'Accepted')">
                            <a :href="'/sales/handover/' + selectedProject.id + '/document'" target="_blank"
                               class="px-3.5 py-2.5 min-h-[38px] text-xs font-bold text-slate-700 bg-white hover:bg-slate-50 border border-slate-300 rounded-xl transition inline-flex items-center gap-1.5 shadow-2xs whitespace-nowrap cursor-pointer shrink-0">
                                <svg class="w-4 h-4 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <span class="whitespace-nowrap">Lihat BA Serah Terima</span>
                            </a>
                        </template>
                        <button type="button" 
                                @click="isHandoverModalOpen = false" 
                                class="px-4 py-2.5 min-h-[38px] text-xs font-bold text-[#475569] hover:bg-[#F1F5F9] border border-[#CBD5E1] rounded-xl transition cursor-pointer whitespace-nowrap shrink-0 inline-flex items-center justify-center">
                            Batal
                        </button>
                        <button type="submit" 
                                class="px-5 py-2.5 min-h-[38px] text-xs font-bold text-white bg-[#8F0A0D] hover:bg-[#73080A] rounded-xl shadow-xs transition cursor-pointer inline-flex items-center gap-2 whitespace-nowrap shrink-0 active:scale-95 leading-normal justify-center">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            <span class="whitespace-nowrap" x-text="formTarget === 'managed_service' ? 'Tanda Tangani & Serahkan ke Managed Service' : 'Tanda Tangani & Serahkan ke PMO'"></span>
                        </button>
                    </div>
                </div>

            </form>

        </div>
    </div>
</template>

