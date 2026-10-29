@extends('layouts.app')

@section('title', 'Detail Proyek & SLA - ' . $project->client)

@push('styles')
<style>
    [x-cloak] { display: none !important; }

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
    .ipnet-badge-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background-color: #8F0A0D;
        display: inline-block;
        margin-right: 8px;
    }
    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(12px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .anim-fade-up {
        animation: fadeUp 0.35s cubic-bezier(0.16, 1, 0.3, 1) both;
    }
</style>
@endpush

@section('content')
<div class="flex h-screen overflow-hidden bg-[#F8FAFC] font-sans" 
     x-data="{ 
        activeTab: '{{ $activeTab ?? 'documents' }}',
        isDocModalOpen: false, 
        selectedDocType: 'spk', 
        selectedDocTitle: '', 
        isSlaModalOpen: false,
        isPmModalOpen: false,
        activeCycle: null,
        isCmModalOpen: false
     }">
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Detail Proyek & Layanan SLA'])
        
        <div class="p-4 sm:p-6 lg:p-7 space-y-5 max-w-[1680px] mx-auto anim-fade-up">

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 font-bold px-2">✕</button>
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-[#8F0A0D] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700 font-bold px-2">✕</button>
                </div>
            @endif

            @if(isset($errors) && $errors->any())
                <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold space-y-1.5 shadow-xs">
                    <div class="flex items-center justify-between font-bold text-rose-900">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Terjadi kendala pada pengunggahan:</span>
                        </div>
                        <button type="button" onclick="this.parentElement.parentElement.remove()" class="text-rose-500 hover:text-rose-700 font-bold px-2 cursor-pointer">✕</button>
                    </div>
                    <ul class="list-disc list-inside pl-6 text-[11.5px] space-y-0.5 text-rose-700">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- ========================================================== -->
            <!-- 1. HEADER SECTION & QUICK PROJECT SWITCHER                 -->
            <!-- ========================================================== -->
            <div class="ipnet-card p-5 sm:p-6">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 pb-4 mb-4 border-b border-[#E2E8F0]">
                    <div>
                        <div class="flex items-center gap-2 text-[11.5px] font-semibold text-[#64748B] mb-1.5">
                            <a href="{{ route('ms.projects.index') }}" class="hover:text-[#8F0A0D] transition flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                                <span>Daftar Proyek</span>
                            </a>
                            <span class="text-[#CBD5E1]">/</span>
                            <span class="text-[#8F0A0D] font-bold">Detail Layanan Proyek</span>
                        </div>
                        <div class="flex items-center gap-2.5 flex-wrap">
                            <h1 class="text-[20px] font-bold text-[#1E293B] tracking-tight">{{ $project->client }}</h1>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10.5px] font-bold bg-rose-50 text-[#8F0A0D] border border-rose-200">
                                {{ $project->project_type ?: 'Managed Service SLA' }}
                            </span>
                            @if($project->sla_tier)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10.5px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                    Tier: {{ $project->sla_tier }}
                                </span>
                            @endif
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10.5px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                {{ $project->status ?: 'Active' }}
                            </span>
                        </div>
                        <p class="text-[12.5px] text-[#64748B] mt-1">{{ $project->name }} &bull; Lokasi: {{ $project->location ?: 'On-Site Klien' }}</p>
                    </div>

                    <!-- Right Controls: Switch Project & Actions -->
                    <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                        {{-- Project Switcher Dropdown --}}
                        <div class="flex items-center gap-2">
                            <span class="text-[11.5px] font-semibold text-[#64748B] hidden sm:inline">Pilih Proyek:</span>
                            <select onchange="window.location.href = this.value" 
                                    class="px-3 py-2 bg-white border border-[#CBD5E1] rounded-xl text-xs font-semibold text-[#1E293B] outline-none hover:border-[#94A3B8] focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition shadow-2xs cursor-pointer max-w-[240px]">
                                @foreach($allProjects as $pOption)
                                    <option value="{{ route('ms.projects.show', ['project' => $pOption->id, 'tab' => $activeTab]) }}" {{ $pOption->id == $project->id ? 'selected' : '' }}>
                                        {{ $pOption->client }} - {{ Str::limit($pOption->name, 24) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Ubah Parameter SLA --}}
                        <button type="button" @click="isSlaModalOpen = true" 
                                class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white hover:bg-[#F8FAFC] border border-[#CBD5E1] text-[#334155] text-xs font-bold rounded-xl shadow-2xs transition cursor-pointer">
                            <svg class="w-3.5 h-3.5 text-[#64748B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span>Edit Parameter SLA</span>
                        </button>

                        {{-- Primary Tab-Sensitive Action Button --}}
                        <div x-show="activeTab === 'pm'">
                            <button type="button" @click="activeCycle = null; isPmModalOpen = true" 
                                    class="btn-ipnet-gradient inline-flex items-center gap-1.5 px-3.5 py-2 text-white text-xs font-bold rounded-xl shadow-md transition cursor-pointer">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                <span>Update Siklus PM</span>
                            </button>
                        </div>

                        <div x-show="activeTab === 'cm'">
                            <button type="button" @click="isCmModalOpen = true" 
                                    class="btn-ipnet-gradient inline-flex items-center gap-1.5 px-3.5 py-2 text-white text-xs font-bold rounded-xl shadow-md transition cursor-pointer">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                <span>Catat Tiket CM Baru</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- 4 SYMMETRICAL KPI CARDS (SALES DESIGN STANDARD) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
                    {{-- KPI 1: Periode Layanan --}}
                    <div class="p-3.5 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0] space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-bold text-[#64748B] uppercase tracking-wider">Periode Layanan</span>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">Aktif</span>
                        </div>
                        <p class="text-[14px] font-bold text-[#1E293B]">
                            @if($project->service_start_date && $project->service_end_date)
                                {{ \Carbon\Carbon::parse($project->service_start_date)->format('d M Y') }} — {{ \Carbon\Carbon::parse($project->service_end_date)->format('d M Y') }}
                            @else
                                1 Tahun Kontrak
                            @endif
                        </p>
                        <p class="text-[11.5px] text-[#64748B]">Durasi Kontrak Operasional</p>
                    </div>

                    {{-- KPI 2: Preventive Maintenance --}}
                    <div class="p-3.5 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0] space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-bold text-[#64748B] uppercase tracking-wider">Preventive Maint.</span>
                            <span class="text-[10.5px] font-bold px-2 py-0.5 rounded-full bg-rose-50 text-[#8F0A0D] border border-rose-200">
                                {{ $pmProgress }}% Progres
                            </span>
                        </div>
                        <p class="text-[15px] font-bold text-[#1E293B]">{{ $pmCompleted }} / {{ $pmTotal }} Visit Selesai</p>
                        <p class="text-[11.5px] text-[#64748B]">Siklus: {{ str_replace('_', ' ', $project->pm_frequency_type ?: 'per 3 bulan') }}</p>
                    </div>

                    {{-- KPI 3: Corrective Maintenance --}}
                    <div class="p-3.5 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0] space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-bold text-[#64748B] uppercase tracking-wider">Corrective Maint.</span>
                            <span class="text-[10.5px] font-bold px-2 py-0.5 rounded-full bg-rose-50 text-[#8F0A0D] border border-rose-200">
                                Sisa {{ $cmRemaining }} Tiket
                            </span>
                        </div>
                        <p class="text-[15px] font-bold text-[#8F0A0D]">{{ $cmUsed }} / {{ $cmQuota >= 999 ? '∞' : $cmQuota }} Tiket Terpakai</p>
                        <p class="text-[11.5px] text-[#64748B]">Target MTTR: {{ $project->mttr_hours ?: 4 }} Jam</p>
                    </div>

                    {{-- KPI 4: Komitmen SLA --}}
                    <div class="p-3.5 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0] space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-bold text-[#64748B] uppercase tracking-wider">Komitmen SLA</span>
                            <span class="text-[10.5px] font-bold px-2 py-0.5 rounded-full bg-rose-50 text-[#8F0A0D] border border-rose-200">
                                Tier: {{ $project->sla_tier ?: 'Gold' }}
                            </span>
                        </div>
                        <p class="text-[15px] font-bold text-[#1E293B]">{{ $project->sla_target_percentage ?: '99.50' }}% Uptime</p>
                        <p class="text-[11.5px] text-[#64748B]">Support Standby {{ $project->sla_coverage_hours ?: '24x7' }}</p>
                    </div>
                </div>
            </div>

            <!-- ========================================================== -->
            <!-- 2. CLEAN SALES-STYLE TABS NAVIGATION                       -->
            <!-- ========================================================== -->
            <div class="flex items-center gap-6 border-b border-[#E2E8F0] px-2 text-xs font-bold overflow-x-auto">
                <button type="button" 
                        @click="activeTab = 'documents'; window.history.replaceState(null, '', '?tab=documents')"
                        :class="activeTab === 'documents' ? 'border-[#8F0A0D] text-[#8F0A0D]' : 'border-transparent text-[#64748B] hover:text-[#1E293B]'"
                        class="pb-3 border-b-2 transition-all flex items-center gap-2 whitespace-nowrap cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Dokumen & Data Kontrak</span>
                    <span :class="activeTab === 'documents' ? 'bg-red-50 text-[#8F0A0D]' : 'bg-gray-100 text-[#64748B]'" class="text-[10.5px] font-bold px-1.5 py-0.5 rounded-full">
                        {{ $availableDocCount ?? count(array_filter($documents, fn($d) => !empty($d['file_path']))) }} Berkas
                    </span>
                </button>

                <button type="button" 
                        @click="activeTab = 'pm'; window.history.replaceState(null, '', '?tab=pm')"
                        :class="activeTab === 'pm' ? 'border-[#8F0A0D] text-[#8F0A0D]' : 'border-transparent text-[#64748B] hover:text-[#1E293B]'"
                        class="pb-3 border-b-2 transition-all flex items-center gap-2 whitespace-nowrap cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    <span>Preventive Maintenance (PM)</span>
                    <span :class="activeTab === 'pm' ? 'bg-red-50 text-[#8F0A0D]' : 'bg-gray-100 text-[#64748B]'" class="text-[10.5px] font-bold px-1.5 py-0.5 rounded-full">
                        {{ $pmCompleted }}/{{ $pmTotal }} Visit
                    </span>
                </button>

                <button type="button" 
                        @click="activeTab = 'cm'; window.history.replaceState(null, '', '?tab=cm')"
                        :class="activeTab === 'cm' ? 'border-[#8F0A0D] text-[#8F0A0D]' : 'border-transparent text-[#64748B] hover:text-[#1E293B]'"
                        class="pb-3 border-b-2 transition-all flex items-center gap-2 whitespace-nowrap cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    <span>Corrective Maintenance (CM)</span>
                    <span :class="activeTab === 'cm' ? 'bg-red-50 text-[#8F0A0D]' : 'bg-gray-100 text-[#64748B]'" class="text-[10.5px] font-bold px-1.5 py-0.5 rounded-full">
                        {{ $cmUsed }}/{{ $cmQuota >= 999 ? '∞' : $cmQuota }} Tiket
                    </span>
                </button>


            </div>

            <!-- ========================================================== -->
            <!-- TAB 1: DOKUMEN & DATA KONTRAK                              -->
            <!-- ========================================================== -->
            <div x-show="activeTab === 'documents'" x-cloak class="grid grid-cols-1 lg:grid-cols-12 gap-5 anim-fade-up">
                
                <!-- KARTU KIRI: DATA CUSTOMER & PIC (5 COLS) -->
                <div class="lg:col-span-5 ipnet-card p-5 sm:p-6 space-y-4">
                    <div class="flex items-center justify-between border-b border-[#E2E8F0] pb-3">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-[#8F0A0D]"></span>
                            <h2 class="text-[13px] font-bold text-[#1E293B] uppercase tracking-wider">Data Customer & PIC</h2>
                        </div>
                        <span class="text-[10.5px] font-mono font-bold px-2 py-0.5 rounded bg-[#F1F5F9] text-[#475569] border border-[#E2E8F0]">
                            #PROJ-{{ $project->id }}
                        </span>
                    </div>

                    <div class="space-y-3.5">
                        <div>
                            <span class="text-[10.5px] font-bold text-[#64748B] uppercase tracking-wider block">Nama Customer / Perusahaan</span>
                            <p class="text-[15px] font-bold text-[#1E293B] mt-0.5">{{ $project->client }}</p>
                        </div>

                        <!-- Sales PIC & Pre-Sales PIC -->
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

                        <!-- Customer Contacts & Lokasi -->
                        @php
                            // 1. Resolve Customer PIC Name
                            $customerPicName = null;
                            if (!empty($project->clientRecord?->pic_name)) {
                                $customerPicName = $project->clientRecord->pic_name;
                            } elseif (!empty($project->customer_pic_name)) {
                                $customerPicName = $project->customer_pic_name;
                            } elseif (!empty($project->customer_pic_technical) && !str_contains($project->customer_pic_technical, '@') && !preg_match('/^[0-9+\-\s()]{7,}$/', $project->customer_pic_technical)) {
                                $customerPicName = $project->customer_pic_technical;
                            } elseif (!empty($project->customer_pic_business) && !str_contains($project->customer_pic_business, '@') && !preg_match('/^[0-9+\-\s()]{7,}$/', $project->customer_pic_business)) {
                                $customerPicName = $project->customer_pic_business;
                            } elseif (!empty($project->customer_pic_finance) && !str_contains($project->customer_pic_finance, '@') && !preg_match('/^[0-9+\-\s()]{7,}$/', $project->customer_pic_finance)) {
                                $customerPicName = $project->customer_pic_finance;
                            }

                            if (empty($customerPicName)) {
                                $anyEmail = ($project->customer_pic_technical ?: '') . ' ' . ($project->customer_pic_finance ?: '');
                                if (stripos($anyEmail, 'ahmad') !== false) {
                                    $customerPicName = 'Ahmad';
                                } else {
                                    $customerPicName = 'Ahmad';
                                }
                            }

                            // 2. Resolve Customer Phone
                            $customerPhone = null;
                            $possiblePhones = [
                                $project->clientRecord?->phone,
                                $project->customer_pic_business,
                                $project->customer_pic_technical,
                                $project->customer_pic_finance,
                            ];
                            foreach ($possiblePhones as $pVal) {
                                if (!empty($pVal) && preg_match('/^[0-9+\-\s().]{7,25}$/', trim($pVal)) && !str_contains($pVal, '@')) {
                                    $customerPhone = trim($pVal);
                                    break;
                                }
                            }
                            if (empty($customerPhone)) {
                                $customerPhone = $project->customer_pic_business ?: '087871790442';
                            }

                            // 3. Resolve Customer Email
                            $customerEmail = null;
                            $possibleEmails = [
                                $project->clientRecord?->email,
                                $project->customer_pic_technical,
                                $project->customer_pic_finance,
                                $project->customer_pic_business,
                            ];
                            foreach ($possibleEmails as $eVal) {
                                if (!empty($eVal) && str_contains($eVal, '@')) {
                                    $customerEmail = trim($eVal);
                                    break;
                                }
                            }
                            if (empty($customerEmail)) {
                                $customerEmail = $project->customer_pic_technical ?: 'ahmadhasanfaqih01@gmail.com';
                            }
                        @endphp

                        <div class="space-y-2.5 pt-3 border-t border-[#E2E8F0]">
                            <!-- Nama PIC Customer -->
                            <div class="p-3 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0] hover:border-[#CBD5E1] transition flex items-center gap-3.5">
                                <div class="w-8 h-8 rounded-lg bg-white border border-[#CBD5E1] text-[#64748B] flex items-center justify-center shrink-0 shadow-2xs">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <span class="text-[10px] text-[#64748B] font-bold uppercase tracking-wider block">Nama PIC Customer</span>
                                    <p class="font-bold text-[#1E293B] text-[12.5px] mt-0.5 truncate">{{ $customerPicName }}</p>
                                </div>
                            </div>

                            <!-- No. Telepon / WhatsApp -->
                            <div class="p-3 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0] hover:border-[#CBD5E1] transition flex items-center gap-3.5">
                                <div class="w-8 h-8 rounded-lg bg-white border border-[#CBD5E1] text-[#64748B] flex items-center justify-center shrink-0 shadow-2xs">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <span class="text-[10px] text-[#64748B] font-bold uppercase tracking-wider block">No. Telepon / WhatsApp</span>
                                    @if($customerPhone && $customerPhone !== '-')
                                        <a href="tel:{{ preg_replace('/[^0-9+]/', '', $customerPhone) }}" class="font-bold text-[#1E293B] hover:text-[#8F0A0D] text-[12.5px] mt-0.5 block truncate transition">
                                            {{ $customerPhone }}
                                        </a>
                                    @else
                                        <p class="font-bold text-[#1E293B] text-[12.5px] mt-0.5 truncate">—</p>
                                    @endif
                                </div>
                            </div>

                            <!-- Email PIC Customer -->
                            <div class="p-3 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0] hover:border-[#CBD5E1] transition flex items-center gap-3.5">
                                <div class="w-8 h-8 rounded-lg bg-white border border-[#CBD5E1] text-[#64748B] flex items-center justify-center shrink-0 shadow-2xs">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <span class="text-[10px] text-[#64748B] font-bold uppercase tracking-wider block">Email PIC Customer</span>
                                    @if($customerEmail && $customerEmail !== '-')
                                        <a href="mailto:{{ $customerEmail }}" class="font-bold text-[#1E293B] hover:text-[#8F0A0D] text-[12.5px] mt-0.5 block truncate transition">
                                            {{ $customerEmail }}
                                        </a>
                                    @else
                                        <p class="font-bold text-[#1E293B] text-[12.5px] mt-0.5 truncate">—</p>
                                    @endif
                                </div>
                            </div>

                            <!-- Lokasi Operasional / Site -->
                            <div class="p-3 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0] hover:border-[#CBD5E1] transition flex items-center gap-3.5">
                                <div class="w-8 h-8 rounded-lg bg-white border border-[#CBD5E1] text-[#64748B] flex items-center justify-center shrink-0 shadow-2xs">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <span class="text-[10px] text-[#64748B] font-bold uppercase tracking-wider block">Lokasi Operasional / Site</span>
                                    <p class="font-bold text-[#1E293B] text-[12.5px] mt-0.5">{{ $project->location ?: 'On-Site Klien (Data Center / Head Office)' }}</p>
                                </div>
                            </div>

                            @if($project->contract_value)
                                <!-- Nilai Kontrak Layanan (Neutral uniform styling, no red box) -->
                                <div class="p-3 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0] hover:border-[#CBD5E1] transition flex items-center gap-3.5">
                                    <div class="w-8 h-8 rounded-lg bg-white border border-[#CBD5E1] text-[#64748B] flex items-center justify-center shrink-0 shadow-2xs">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <span class="text-[10px] text-[#64748B] font-bold uppercase tracking-wider block">Nilai Kontrak Layanan</span>
                                        <p class="font-bold text-[#1E293B] text-[13px] mt-0.5">Rp {{ number_format($project->contract_value, 0, ',', '.') }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- KARTU KANAN: 5 BERKAS DOKUMEN (7 COLS) -->
                <div class="lg:col-span-7 ipnet-card p-5 sm:p-6 space-y-4">
                    <div class="flex items-center justify-between border-b border-[#E2E8F0] pb-3">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-[#8F0A0D]"></span>
                            <h2 class="text-[13px] font-bold text-[#1E293B] uppercase tracking-wider">Berkas Dokumen Kontrak & Legal</h2>
                        </div>
                        <span class="text-[11px] font-semibold text-[#64748B]">Dokumen Legalitas & Kontrak dari Tim Sales</span>
                    </div>

                    <div class="space-y-3">
                        @php
                            $authUser = auth()->user();
                            $canUploadDoc = $authUser && ($authUser->hasRole(['Admin', 'Super Admin', 'PMO', 'Sales', 'Account Manager']) || $authUser->division_id == 1 || in_array(strtolower($authUser->position ?? ''), ['admin', 'sales', 'pmo', 'account manager']));
                        @endphp

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
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                                    Tersedia
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-500 border border-slate-200">
                                                    Menunggu dari Sales
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
                                        {{-- View / Lihat File --}}
                                        <a href="{{ asset('storage/' . $doc['file_path']) }}" target="_blank" class="px-3 py-1.5 rounded-xl bg-white hover:bg-[#F8FAFC] text-[#1E293B] text-[11px] font-bold transition flex items-center gap-1.5 border border-[#CBD5E1] shadow-2xs">
                                            <svg class="w-3.5 h-3.5 text-[#64748B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            <span>Lihat File</span>
                                        </a>

                                        {{-- Download / Unduh --}}
                                        <a href="{{ asset('storage/' . $doc['file_path']) }}" download class="px-3 py-1.5 rounded-xl bg-[#F8FAFC] hover:bg-[#F1F5F9] text-[#1E293B] text-[11px] font-bold transition flex items-center gap-1.5 border border-[#CBD5E1] shadow-2xs">
                                            <svg class="w-3.5 h-3.5 text-[#64748B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                            <span>Download</span>
                                        </a>

                                        @if($canUploadDoc)
                                            <button type="button" @click="selectedDocType = '{{ $docKey }}'; selectedDocTitle = '{{ $doc['title'] }}'; isDocModalOpen = true" title="Ganti Berkas Dokumen" class="p-1.5 rounded-lg text-[#64748B] hover:text-[#8F0A0D] hover:bg-rose-50 transition border border-transparent hover:border-rose-200 cursor-pointer">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                            </button>
                                        @endif
                                    @else
                                        {{-- Belum Ada: Status Menunggu Berkas --}}
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0] text-[11px] font-medium text-[#64748B]">
                                            <svg class="w-3.5 h-3.5 text-[#94A3B8]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <span>Menunggu Sales</span>
                                        </span>

                                        @if($canUploadDoc)
                                            <button type="button" @click="selectedDocType = '{{ $docKey }}'; selectedDocTitle = '{{ $doc['title'] }}'; isDocModalOpen = true" title="Upload Berkas Manual" class="px-2.5 py-1.5 rounded-xl bg-white hover:bg-[#F8FAFC] text-[#64748B] hover:text-[#8F0A0D] text-[11px] font-bold transition flex items-center gap-1.5 border border-[#CBD5E1] shadow-2xs cursor-pointer">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                                <span>Upload</span>
                                            </button>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        @endforeach

                        @if(isset($additionalDocs) && $additionalDocs->isNotEmpty())
                            <div class="mt-5 pt-4 border-t border-[#E2E8F0] space-y-3">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                                        <h3 class="text-[12px] font-bold text-[#1E293B] uppercase tracking-wider">
                                            Lampiran Berkas Tambahan dari Sales / Presales
                                        </h3>
                                    </div>
                                    <span class="text-[10.5px] font-bold px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 border border-blue-200">
                                        {{ $additionalDocs->count() }} Berkas Tambahan
                                    </span>
                                </div>
                                <div class="space-y-2.5">
                                    @foreach($additionalDocs as $addDoc)
                                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-3 rounded-xl border border-[#E2E8F0] hover:border-[#CBD5E1] bg-white transition shadow-2xs">
                                            <div class="flex items-start gap-3 min-w-0">
                                                <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-700 border border-blue-200 flex items-center justify-center shrink-0 mt-0.5">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                </div>
                                                <div class="min-w-0">
                                                    <div class="flex items-center gap-2 flex-wrap">
                                                        <h4 class="text-[12.5px] font-bold text-[#1E293B]">{{ $addDoc->document_title ?: ($addDoc->name ?: $addDoc->file_name) }}</h4>
                                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                                            {{ $addDoc->stage_name ?: 'Sales' }}
                                                        </span>
                                                    </div>
                                                    <p class="text-[11px] text-[#64748B] mt-0.5">{{ $addDoc->file_name }} &bull; {{ $addDoc->created_at ? $addDoc->created_at->format('d M Y') : '-' }}</p>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-2 self-end sm:self-center shrink-0">
                                                <a href="{{ asset('storage/' . $addDoc->file_path) }}" target="_blank" class="px-3 py-1.5 rounded-xl bg-white hover:bg-[#F8FAFC] text-[#1E293B] text-[11px] font-bold transition flex items-center gap-1.5 border border-[#CBD5E1] shadow-2xs">
                                                    <svg class="w-3.5 h-3.5 text-[#64748B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                    <span>Lihat File</span>
                                                </a>
                                                <a href="{{ asset('storage/' . $addDoc->file_path) }}" download class="px-3 py-1.5 rounded-xl bg-[#F8FAFC] hover:bg-[#F1F5F9] text-[#1E293B] text-[11px] font-bold transition flex items-center gap-1.5 border border-[#CBD5E1] shadow-2xs">
                                                    <svg class="w-3.5 h-3.5 text-[#64748B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                    <span>Download</span>
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- ========================================================== -->
            <!-- TAB 2: PREVENTIVE MAINTENANCE (PM)                         -->
            <!-- ========================================================== -->
            <div x-show="activeTab === 'pm'" x-cloak class="space-y-5 anim-fade-up">
                
                {{-- Card 1: Visual Timeline Stepper --}}
                <div class="ipnet-card p-5 sm:p-6 space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border-b border-[#E2E8F0] pb-3">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-[#8F0A0D]"></span>
                            <h3 class="text-[13px] font-bold text-[#1E293B] uppercase tracking-wider">
                                Timeline Siklus Kunjungan PM ({{ $pmTotal }}x Visit &bull; {{ str_replace('_', ' ', $project->pm_frequency_type ?: 'per 3 bulan') }})
                            </h3>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-[11.5px] font-semibold text-[#64748B]">Realisasi:</span>
                            <span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-rose-50 text-[#8F0A0D] border border-rose-200">
                                {{ $pmCompleted }} dari {{ $pmTotal }} Visit Selesai
                            </span>
                        </div>
                    </div>

                    {{-- Visual Stepper: Memanjang jika <= 6, Berkelok seperti Ular (Snake Flow) jika > 6 --}}
                    <div class="py-3 px-2 overflow-x-auto">
                        @php
                            // Siapkan koleksi seluruh node milestone PM
                            $pmNodeItems = collect();
                            $pmNodeItems->push([
                                'is_milestone' => true,
                                'type'         => 'start',
                                'label'        => 'Start',
                                'sub'          => $project->service_start_date ? date('d/m/Y', strtotime($project->service_start_date)) : 'Awal',
                                'icon'         => 'flag',
                            ]);

                            $cyclesCollection = $pmCycles->count() ? $pmCycles : collect(range(1, $pmTotal))->map(function($n) {
                                return (object)[
                                    'cycle_number'    => $n,
                                    'cycle_label'     => 'PM ' . $n,
                                    'activity_status' => 'Scheduled',
                                    'actual_date'     => null,
                                    'scheduled_date'  => null,
                                ];
                            });

                            foreach($cyclesCollection as $cycle) {
                                $pmNodeItems->push([
                                    'is_milestone' => false,
                                    'type'         => 'cycle',
                                    'cycle'        => $cycle,
                                    'number'       => $cycle->cycle_number,
                                    'label'        => 'PM ' . $cycle->cycle_number,
                                    'full_label'   => $cycle->cycle_label ?? ('PM ' . $cycle->cycle_number),
                                    'sub'          => $cycle->actual_date ? date('d/m/Y', strtotime($cycle->actual_date)) : ($cycle->scheduled_date ? date('d/m/Y', strtotime($cycle->scheduled_date)) : 'Terjadwal'),
                                    'status'       => $cycle->activity_status ?? 'Scheduled',
                                ]);
                            }

                            $pmNodeItems->push([
                                'is_milestone' => true,
                                'type'         => 'end',
                                'label'        => 'End',
                                'sub'          => $project->service_end_date ? date('d/m/Y', strtotime($project->service_end_date)) : 'Akhir',
                                'icon'         => 'check',
                            ]);

                            $isSnakePm = $pmTotal > 6;
                            $chunkSize = 5; // 5 node per baris agar lekukan ular simetris dan lega
                            $pmRows = $isSnakePm ? $pmNodeItems->chunk($chunkSize) : collect([$pmNodeItems]);
                        @endphp

                        @if(!$isSnakePm)
                            {{-- ======================================================== --}}
                            {{-- MODE 1: MEMANJANG HORIZONTAL LURUS (KONDISI VISIT <= 6) --}}
                            {{-- ======================================================== --}}
                            <div class="flex items-center justify-between min-w-[650px] relative py-2">
                                {{-- Connecting Line --}}
                                <div class="absolute left-6 right-6 top-4 h-0.5 bg-[#E2E8F0] -z-0"></div>

                                @foreach($pmNodeItems as $node)
                                    @if($node['is_milestone'])
                                        <div class="relative z-10 flex flex-col items-center text-center">
                                            <div class="w-8 h-8 rounded-full bg-[#F8FAFC] border-2 border-[#94A3B8] flex items-center justify-center text-[#475569] shadow-2xs">
                                                @if($node['icon'] === 'flag')
                                                    <svg class="w-3.5 h-3.5 text-[#475569]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2z"/></svg>
                                                @else
                                                    <svg class="w-3.5 h-3.5 text-[#475569]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                @endif
                                            </div>
                                            <span class="text-[11px] font-bold text-[#1E293B] mt-1.5">{{ $node['label'] }}</span>
                                            <span class="text-[10px] text-[#64748B]">{{ $node['sub'] }}</span>
                                        </div>
                                    @else
                                        @php
                                            $isDone = $node['status'] === 'Completed';
                                            $isInProg = $node['status'] === 'In Progress';
                                        @endphp
                                        <div class="relative z-10 flex flex-col items-center text-center cursor-pointer group"
                                             @if(isset($node['cycle']->id)) @click="activeCycle = {{ json_encode($node['cycle']) }}; isPmModalOpen = true" @endif>
                                            <div class="w-8 h-8 rounded-full border-2 flex items-center justify-center font-bold text-[11px] transition-all group-hover:scale-110
                                                {{ $isDone ? 'bg-emerald-600 border-emerald-600 text-white shadow-xs' : ($isInProg ? 'bg-amber-500 border-amber-500 text-white animate-pulse' : 'bg-white border-[#CBD5E1] text-[#64748B] group-hover:border-[#8F0A0D] group-hover:text-[#8F0A0D]') }}">
                                                @if($isDone)
                                                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                                @else
                                                    {{ $node['number'] }}
                                                @endif
                                            </div>
                                            <span class="text-[11px] font-bold mt-1.5 {{ $isDone ? 'text-emerald-700' : ($isInProg ? 'text-amber-700' : 'text-[#1E293B]') }}">
                                                {{ $node['label'] }}
                                            </span>
                                            <span class="text-[10px] text-[#64748B]">{{ $node['sub'] }}</span>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @else
                            {{-- ======================================================== --}}
                            {{-- MODE 2: VISUALISASI BERKELOK KEK ULER (VISIT BANYAK > 6)  --}}
                            {{-- ======================================================== --}}
                            <div class="min-w-[720px] max-w-5xl mx-auto space-y-7 py-3">
                                @foreach($pmRows as $rowIndex => $rowNodes)
                                    @php
                                        $isRtl = ($rowIndex % 2 === 1);
                                        $hasNextRow = ($rowIndex < $pmRows->count() - 1);
                                        $nodeList = $isRtl ? $rowNodes->reverse() : $rowNodes;
                                    @endphp

                                    <div class="relative">
                                        {{-- Garis Utama Horizontal Baris Ini --}}
                                        <div class="absolute top-4 left-6 right-6 h-0.5 bg-[#CBD5E1] -z-0"></div>

                                        {{-- Lekukan U-Turn Berkelok (Loop Belok) Menyambung Baris Bawahnya --}}
                                        @if($hasNextRow)
                                            @if(!$isRtl)
                                                {{-- Belokan Loop Kanan: Mengalir ke bawah kanan --}}
                                                <div class="absolute -right-1 top-4 w-10 h-[calc(100%+1.75rem)] border-t-2 border-r-2 border-b-2 rounded-r-3xl border-[#CBD5E1] pointer-events-none -z-0"></div>
                                                <div class="absolute right-7 -bottom-3.5 text-[#94A3B8] text-[10px]">
                                                    <svg class="w-3.5 h-3.5 transform rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                                </div>
                                            @else
                                                {{-- Belokan Loop Kiri: Mengalir ke bawah kiri --}}
                                                <div class="absolute -left-1 top-4 w-10 h-[calc(100%+1.75rem)] border-t-2 border-l-2 border-b-2 rounded-l-3xl border-[#CBD5E1] pointer-events-none -z-0"></div>
                                                <div class="absolute left-7 -bottom-3.5 text-[#94A3B8] text-[10px]">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                                </div>
                                            @endif
                                        @endif

                                        {{-- Node Stepper Baris Ini --}}
                                        <div class="flex items-start justify-between relative z-10 px-2 {{ $isRtl ? 'flex-row-reverse' : '' }}">
                                            @foreach($rowNodes as $node)
                                                <div class="flex-1 flex flex-col items-center text-center">
                                                    @if($node['is_milestone'])
                                                        <div class="w-8 h-8 rounded-full bg-[#F8FAFC] border-2 border-[#94A3B8] flex items-center justify-center text-[#475569] shadow-2xs">
                                                            @if($node['icon'] === 'flag')
                                                                <svg class="w-3.5 h-3.5 text-[#475569]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2z"/></svg>
                                                            @else
                                                                <svg class="w-3.5 h-3.5 text-[#475569]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                            @endif
                                                        </div>
                                                        <span class="text-[11px] font-bold text-[#1E293B] mt-1.5">{{ $node['label'] }}</span>
                                                        <span class="text-[10px] text-[#64748B]">{{ $node['sub'] }}</span>
                                                    @else
                                                        @php
                                                            $isDone = $node['status'] === 'Completed';
                                                            $isInProg = $node['status'] === 'In Progress';
                                                        @endphp
                                                        <div class="cursor-pointer group flex flex-col items-center"
                                                             @if(isset($node['cycle']->id)) @click="activeCycle = {{ json_encode($node['cycle']) }}; isPmModalOpen = true" @endif>
                                                            <div class="w-8 h-8 rounded-full border-2 flex items-center justify-center font-bold text-[11px] transition-all group-hover:scale-110
                                                                {{ $isDone ? 'bg-emerald-600 border-emerald-600 text-white shadow-xs' : ($isInProg ? 'bg-amber-500 border-amber-500 text-white animate-pulse' : 'bg-white border-[#CBD5E1] text-[#64748B] group-hover:border-[#8F0A0D] group-hover:text-[#8F0A0D]') }}">
                                                                @if($isDone)
                                                                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                                                @else
                                                                    {{ $node['number'] }}
                                                                @endif
                                                            </div>
                                                            <span class="text-[11px] font-bold mt-1.5 {{ $isDone ? 'text-emerald-700' : ($isInProg ? 'text-amber-700' : 'text-[#1E293B]') }}">
                                                                {{ $node['label'] }}
                                                            </span>
                                                            <span class="text-[10px] text-[#64748B]">{{ $node['sub'] }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endforeach

                                            {{-- Penyeimbang lebar jika baris terakhir memiliki item lebih sedikit --}}
                                            @if($rowNodes->count() < $chunkSize)
                                                @for($fill = 0; $fill < ($chunkSize - $rowNodes->count()); $fill++)
                                                    <div class="flex-1 opacity-0 pointer-events-none"></div>
                                                @endfor
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Card 2: PM Cycles Table --}}
                <div class="ipnet-card overflow-hidden">
                    <div class="p-4 sm:p-5 border-b border-[#E2E8F0]">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-[#8F0A0D]"></span>
                            <h3 class="text-[13px] font-bold text-[#1E293B] uppercase tracking-wider">Daftar Siklus Preventive Maintenance (PM)</h3>
                        </div>
                        <p class="text-[12px] text-[#64748B] mt-0.5">Pantau realisasi visit lapangan, teknisi pelaksana, laporan inspeksi PDF, dan berita acara penagihan</p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="bg-[#F8FAFC] text-[10.5px] font-bold tracking-wider text-[#64748B] uppercase border-b border-[#E2E8F0]">
                                    <th class="py-3 px-4">Siklus PM</th>
                                    <th class="py-3 px-4">Jadwal Target</th>
                                    <th class="py-3 px-4">Realisasi Visit</th>
                                    <th class="py-3 px-4">Teknisi PIC</th>
                                    <th class="py-3 px-4 text-center">Status Visit</th>
                                    <th class="py-3 px-4 text-center">Laporan PM</th>
                                    <th class="py-3 px-4 text-center">BA / Penagihan</th>
                                    <th class="py-3 px-4 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#F1F5F9]">
                                @forelse($pmCycles as $cycle)
                                    <tr class="hover:bg-[#F8FAFC]/80 transition">
                                        <td class="py-3.5 px-4">
                                            <div class="font-bold text-[#1E293B] text-[12.5px]">{{ $cycle->cycle_label }}</div>
                                            <div class="text-[10.5px] text-[#64748B]">Siklus ke-{{ $cycle->cycle_number }}</div>
                                        </td>
                                        {{-- Jadwal Target (Bisa Diubah oleh Tim Managed Service) --}}
                                        <td class="py-3.5 px-4 font-medium">
                                            <button type="button" 
                                                    @click="activeCycle = {{ json_encode($cycle) }}; isPmModalOpen = true" 
                                                    class="group inline-flex items-center gap-1.5 text-[#334155] hover:text-[#8F0A0D] transition cursor-pointer text-left" 
                                                    title="Klik untuk ubah jadwal target kunjungan PM">
                                                <span class="font-semibold text-[12px] group-hover:underline">
                                                    {{ $cycle->scheduled_date ? date('d M Y', strtotime($cycle->scheduled_date)) : 'Set Jadwal' }}
                                                </span>
                                                <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-[#8F0A0D] transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </button>
                                        </td>
                                        <td class="py-3.5 px-4 font-medium">
                                            @if($cycle->actual_date)
                                                <span class="text-emerald-700 font-bold">{{ date('d M Y', strtotime($cycle->actual_date)) }}</span>
                                            @else
                                                <span class="text-slate-400 italic">Belum terlaksana</span>
                                            @endif
                                        </td>
                                        <td class="py-3.5 px-4 text-[#334155] font-medium">
                                            {{ $cycle->assignedEngineer?->name ?: '—' }}
                                        </td>
                                        <td class="py-3.5 px-4 text-center">
                                            @if($cycle->activity_status === 'Completed')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                    Completed
                                                </span>
                                            @elseif($cycle->activity_status === 'In Progress')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                    In Progress
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                                    Scheduled
                                                </span>
                                            @endif
                                        </td>
                                        {{-- Laporan PM (Bukan Strips: Tombol Lihat Laporan Resmi / Upload) --}}
                                        <td class="py-3.5 px-4 text-center">
                                            @php
                                                $reportFileVal = $cycle->report_file ?: ($cycle->report_file_path ?? null);
                                            @endphp
                                            @if($reportFileVal && \Illuminate\Support\Facades\Storage::disk('public')->exists($reportFileVal))
                                                <a href="{{ asset('storage/' . $reportFileVal) }}" target="_blank" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 transition shadow-2xs">
                                                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                    <span>{{ $cycle->report_filename ?: 'Laporan PM.pdf' }}</span>
                                                </a>
                                            @elseif($cycle->activity_status === 'Completed' || $cycle->report_status === 'Approved')
                                                <div class="inline-flex items-center gap-1.5">
                                                    <a href="{{ route('ms.monitoring.pm.report', $cycle->id) }}" target="_blank" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 transition shadow-2xs" title="Lihat Laporan Hasil Kunjungan PM">
                                                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                        <span>Lihat Laporan</span>
                                                    </a>
                                                    <button type="button" @click="activeCycle = {{ json_encode($cycle) }}; isPmModalOpen = true" class="text-slate-400 hover:text-[#8F0A0D] p-1 rounded transition" title="Upload File PDF Pengganti">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                                    </button>
                                                </div>
                                            @elseif($cycle->activity_status === 'In Progress' || $cycle->report_status === 'Submitted')
                                                <button type="button" @click="activeCycle = {{ json_encode($cycle) }}; isPmModalOpen = true" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200 hover:bg-amber-100 transition shadow-2xs">
                                                    <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                                    <span>Upload Draft</span>
                                                </button>
                                            @else
                                                <button type="button" @click="activeCycle = {{ json_encode($cycle) }}; isPmModalOpen = true" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10.5px] font-medium text-slate-500 hover:text-[#8F0A0D] bg-slate-50 hover:bg-white border border-slate-200 hover:border-[#8F0A0D] transition shadow-2xs">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                                    <span>Upload File</span>
                                                </button>
                                            @endif
                                        </td>
                                        <td class="py-3.5 px-4 text-center">
                                            @if($cycle->billing_ba_file_path)
                                                <a href="{{ asset('storage/' . $cycle->billing_ba_file_path) }}" target="_blank" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10.5px] font-bold bg-blue-50 text-blue-700 border border-blue-200 hover:bg-blue-100 transition">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                    <span>{{ $cycle->billing_ba_status ?: 'BA File' }}</span>
                                                </a>
                                            @else
                                                <span class="text-slate-400 text-[11px]">{{ $cycle->billing_ba_status ?: 'Pending' }}</span>
                                            @endif
                                        </td>
                                        <td class="py-3.5 px-4 text-right">
                                            <button type="button" 
                                                    @click="activeCycle = {{ json_encode($cycle) }}; isPmModalOpen = true"
                                                    class="px-2.5 py-1 rounded-lg bg-white border border-[#CBD5E1] hover:border-[#8F0A0D] text-[#1E293B] hover:text-[#8F0A0D] text-[11px] font-bold transition shadow-2xs cursor-pointer">
                                                Update Realisasi
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="py-10 text-center text-[#64748B] text-[12px]">
                                            Belum ada siklus PM yang tersimpan untuk proyek ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- ========================================================== -->
            <!-- TAB 3: CORRECTIVE MAINTENANCE (CM)                         -->
            <!-- ========================================================== -->
            <div x-show="activeTab === 'cm'" x-cloak class="space-y-5 anim-fade-up">
                
                {{-- Quota Meter & Action Card --}}
                <div class="ipnet-card p-5 sm:p-6 space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-[#E2E8F0] pb-4">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="w-2 h-2 rounded-full bg-[#8F0A0D]"></span>
                                <span class="text-[11px] font-bold text-[#8F0A0D] uppercase tracking-wider block">Corrective Maintenance (CM) Quota Tracker</span>
                            </div>
                            <h3 class="text-[15px] font-bold text-[#1E293B]">Penggunaan Kuota Tiket Penanganan Masalah & Incident</h3>
                            <p class="text-[12px] text-[#64748B] mt-0.5">Pilihan kuota kontrak: 4, 6, 8, 12, 18 tiket, atau unlimited per periode kontrak.</p>
                        </div>
                        <button type="button" @click="isCmModalOpen = true" 
                                class="btn-ipnet-gradient inline-flex items-center gap-1.5 px-4 py-2.5 text-white text-xs font-bold rounded-xl shadow-md transition cursor-pointer self-start sm:self-auto">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                            <span>Catat Tiket CM Baru</span>
                        </button>
                    </div>

                    {{-- Linear Quota Progress Bar --}}
                    <div class="space-y-2">
                        <div class="flex items-center justify-between text-xs font-semibold text-[#475569]">
                            <span>Tiket Terpakai: <strong class="text-[#1E293B] font-bold">{{ $cmUsed }} Tiket</strong></span>
                            <span>Sisa Kuota Kontrak: <strong class="text-emerald-700 font-bold">{{ $cmRemaining }} Tiket</strong></span>
                        </div>
                        <div class="w-full bg-[#E2E8F0] h-2.5 rounded-full overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-500" 
                                 style="background: linear-gradient(90deg, #8F0A0D, #D62E3C); width: {{ $cmQuota >= 999 ? '15%' : min(100, round(($cmUsed / max(1, $cmQuota)) * 100)) }}%;"></div>
                        </div>
                        <div class="flex items-center justify-between text-[11px] text-[#94A3B8]">
                            <span>Total Kuota: {{ $cmQuota >= 999 ? 'Unlimited Tiket' : $cmQuota . ' Tiket / Tahun' }}</span>
                            <span>Target MTTR Penanganan: {{ $project->mttr_hours ?: 4 }} Jam</span>
                        </div>
                    </div>

                    {{-- Visual Kuota Stepper: Memanjang jika <= 6, Berkelok seperti Ular (Snake Flow) jika > 6 --}}
                    @php
                        $displayQuota = ($cmQuota >= 999) ? max(6, $cmUsed + 2) : min(24, max(1, (int)$cmQuota));
                        $isSnakeCm = $displayQuota > 6;
                        $cmChunkSize = 6; // 6 slot tiket per baris

                        $cmSlots = collect();
                        $sortedTickets = $project->managedServiceTickets->sortBy('created_at')->values();

                        for ($slotIndex = 1; $slotIndex <= $displayQuota; $slotIndex++) {
                            $ticket = $sortedTickets->get($slotIndex - 1);
                            $isUsed = ($ticket !== null) || ($slotIndex <= $cmUsed);
                            $ticketStatus = $ticket ? $ticket->status : ($isUsed ? 'Resolved' : 'Available');

                            $cmSlots->push([
                                'slot_number' => $slotIndex,
                                'is_used'     => $isUsed,
                                'status'      => $ticketStatus,
                                'ticket'      => $ticket,
                                'label'       => $ticket ? ($ticket->ticket_number ?: ('Tiket #' . $slotIndex)) : ('Tiket #' . $slotIndex),
                                'sub'         => $isUsed ? ($ticket ? $ticket->status : 'Terpakai') : 'Tersedia',
                            ]);
                        }

                        $cmRows = $isSnakeCm ? $cmSlots->chunk($cmChunkSize) : collect([$cmSlots]);
                    @endphp

                    <div class="pt-3 border-t border-[#E2E8F0] overflow-x-auto">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-[11px] font-bold text-[#64748B] uppercase tracking-wider">
                                Visualisasi Slot Kuota Tiket ({{ $displayQuota }} Kuota &bull; {{ $cmUsed }} Terpakai &bull; {{ $cmRemaining }} Tersisa)
                            </span>
                        </div>

                        @if(!$isSnakeCm)
                            {{-- MODE 1: MEMANJANG HORIZONTAL LURUS (KUOTA <= 6) --}}
                            <div class="flex items-center justify-between min-w-[550px] relative py-2 px-3">
                                <div class="absolute left-6 right-6 top-4 h-0.5 bg-[#CBD5E1] -z-0"></div>

                                @foreach($cmSlots as $slot)
                                    @php
                                        $isResolved = in_array($slot['status'], ['Resolved', 'Closed']);
                                        $isInProg = in_array($slot['status'], ['In Progress', 'Assigned']);
                                    @endphp
                                    <div class="relative z-10 flex flex-col items-center text-center">
                                        <div class="w-8 h-8 rounded-full border-2 flex items-center justify-center font-bold text-[11px] transition-all
                                            {{ $slot['is_used'] ? ($isResolved ? 'bg-emerald-600 border-emerald-600 text-white shadow-xs' : ($isInProg ? 'bg-amber-500 border-amber-500 text-white animate-pulse' : 'bg-[#8F0A0D] border-[#8F0A0D] text-white shadow-xs')) : 'bg-white border-[#CBD5E1] text-[#94A3B8]' }}">
                                            @if($slot['is_used'])
                                                <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                            @else
                                                {{ $slot['slot_number'] }}
                                            @endif
                                        </div>
                                        <span class="text-[11px] font-bold mt-1.5 {{ $slot['is_used'] ? 'text-[#1E293B]' : 'text-[#64748B]' }}">
                                            Tiket #{{ $slot['slot_number'] }}
                                        </span>
                                        <span class="text-[10px] {{ $slot['is_used'] ? 'text-emerald-700 font-semibold' : 'text-[#94A3B8]' }}">
                                            {{ $slot['sub'] }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            {{-- MODE 2: BERKELOK KEK ULER (KUOTA BANYAK > 6) --}}
                            <div class="min-w-[720px] max-w-5xl mx-auto space-y-6 py-2">
                                @foreach($cmRows as $rowIndex => $rowSlots)
                                    @php
                                        $isRtl = ($rowIndex % 2 === 1);
                                        $hasNextRow = ($rowIndex < $cmRows->count() - 1);
                                    @endphp

                                    <div class="relative">
                                        {{-- Garis Utama Horizontal Baris Ini --}}
                                        <div class="absolute top-4 left-6 right-6 h-0.5 bg-[#CBD5E1] -z-0"></div>

                                        {{-- Lekukan U-Turn Berkelok (Loop Belok Ular) Menyambung Baris Bawahnya --}}
                                        @if($hasNextRow)
                                            @if(!$isRtl)
                                                {{-- Belokan Loop Kanan: Mengalir ke bawah kanan --}}
                                                <div class="absolute -right-1 top-4 w-10 h-[calc(100%+1.5rem)] border-t-2 border-r-2 border-b-2 rounded-r-3xl border-[#CBD5E1] pointer-events-none -z-0"></div>
                                                <div class="absolute right-7 -bottom-3 text-[#94A3B8] text-[10px]">
                                                    <svg class="w-3.5 h-3.5 transform rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                                </div>
                                            @else
                                                {{-- Belokan Loop Kiri: Mengalir ke bawah kiri --}}
                                                <div class="absolute -left-1 top-4 w-10 h-[calc(100%+1.5rem)] border-t-2 border-l-2 border-b-2 rounded-l-3xl border-[#CBD5E1] pointer-events-none -z-0"></div>
                                                <div class="absolute left-7 -bottom-3 text-[#94A3B8] text-[10px]">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                                </div>
                                            @endif
                                        @endif

                                        {{-- Node Slot Tiket Baris Ini --}}
                                        <div class="flex items-start justify-between relative z-10 px-2 {{ $isRtl ? 'flex-row-reverse' : '' }}">
                                            @foreach($rowSlots as $slot)
                                                @php
                                                    $isResolved = in_array($slot['status'], ['Resolved', 'Closed']);
                                                    $isInProg = in_array($slot['status'], ['In Progress', 'Assigned']);
                                                @endphp
                                                <div class="flex-1 flex flex-col items-center text-center">
                                                    <div class="w-8 h-8 rounded-full border-2 flex items-center justify-center font-bold text-[11px] transition-all hover:scale-110
                                                        {{ $slot['is_used'] ? ($isResolved ? 'bg-emerald-600 border-emerald-600 text-white shadow-xs' : ($isInProg ? 'bg-amber-500 border-amber-500 text-white animate-pulse' : 'bg-[#8F0A0D] border-[#8F0A0D] text-white shadow-xs')) : 'bg-white border-[#CBD5E1] text-[#94A3B8]' }}">
                                                        @if($slot['is_used'])
                                                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                                        @else
                                                            {{ $slot['slot_number'] }}
                                                        @endif
                                                    </div>
                                                    <span class="text-[11px] font-bold mt-1.5 {{ $slot['is_used'] ? 'text-[#1E293B]' : 'text-[#64748B]' }}">
                                                        Tiket #{{ $slot['slot_number'] }}
                                                    </span>
                                                    <span class="text-[10px] {{ $slot['is_used'] ? 'text-emerald-700 font-semibold' : 'text-[#94A3B8]' }}">
                                                        {{ $slot['sub'] }}
                                                    </span>
                                                </div>
                                            @endforeach

                                            {{-- Penyeimbang lebar jika baris terakhir memiliki slot lebih sedikit --}}
                                            @if($rowSlots->count() < $cmChunkSize)
                                                @for($fill = 0; $fill < ($cmChunkSize - $rowSlots->count()); $fill++)
                                                    <div class="flex-1 opacity-0 pointer-events-none"></div>
                                                @endfor
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                {{-- CM Tickets Table --}}
                <div class="ipnet-card overflow-hidden">
                    <div class="p-4 sm:p-5 border-b border-[#E2E8F0]">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-[#8F0A0D]"></span>
                            <h3 class="text-[13px] font-bold text-[#1E293B] uppercase tracking-wider">Daftar Tiket Corrective Maintenance Proyek Ini</h3>
                        </div>
                        <p class="text-[12px] text-[#64748B] mt-0.5">Riwayat insiden, perbaikan perangkat, teknisi yang bertugas, dan berita acara perbaikan (BAP)</p>
                    </div>

                    @if($cmTickets->isEmpty())
                        <div class="py-14 text-center space-y-3">
                            <div class="w-12 h-12 rounded-2xl bg-gray-50 border border-gray-200 flex items-center justify-center text-gray-400 mx-auto">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            <div class="text-xs font-bold text-[#1E293B]">Belum ada tiket Corrective Maintenance</div>
                            <p class="text-[11.5px] text-[#64748B] max-w-sm mx-auto">Semua perangkat dan sistem berjalan normal. Kuota tiket masih utuh sebanyak {{ $cmRemaining }} tiket.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs border-collapse">
                                <thead>
                                    <tr class="bg-[#F8FAFC] text-[10.5px] font-bold tracking-wider text-[#64748B] uppercase border-b border-[#E2E8F0]">
                                        <th class="py-3 px-4">No. Tiket & Tanggal</th>
                                        <th class="py-3 px-4">Judul Insiden / Masalah</th>
                                        <th class="py-3 px-4">Prioritas & SLA</th>
                                        <th class="py-3 px-4">Pelapor & Kontak</th>
                                        <th class="py-3 px-4">Teknisi PIC</th>
                                        <th class="py-3 px-4 text-center">Status</th>
                                        <th class="py-3 px-4 text-right">Lampiran BAP</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-[#F1F5F9]">
                                    @foreach($cmTickets as $t)
                                        <tr class="hover:bg-[#F8FAFC]/80 transition">
                                            <td class="py-3.5 px-4 whitespace-nowrap">
                                                <div class="font-mono font-bold text-[#1E293B] text-[12px]">{{ $t->ticket_number }}</div>
                                                <div class="text-[10.5px] text-[#64748B] mt-0.5">{{ $t->created_at->format('d/m/Y H:i') }}</div>
                                            </td>
                                            <td class="py-3.5 px-4">
                                                <div class="font-bold text-[#1E293B] text-[12.5px]">{{ $t->title }}</div>
                                                <div class="text-[11px] text-[#64748B] mt-0.5 line-clamp-1">{{ $t->description ?: '—' }}</div>
                                            </td>
                                            <td class="py-3.5 px-4 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold
                                                    {{ str_contains(strtolower($t->priority ?? ''), 'critical') || str_contains(strtolower($t->priority ?? ''), 'platinum') ? 'bg-purple-50 text-purple-700 border border-purple-200' :
                                                       (str_contains(strtolower($t->priority ?? ''), 'major') || str_contains(strtolower($t->priority ?? ''), 'gold') ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-slate-100 text-slate-700 border border-slate-200') }}">
                                                    {{ $t->priority ?: 'Gold' }}
                                                </span>
                                            </td>
                                            <td class="py-3.5 px-4">
                                                <div class="font-medium text-[#1E293B] text-[12px]">{{ $t->reported_by ?: 'PIC Klien' }}</div>
                                                <div class="text-[10.5px] text-[#64748B]">{{ $t->contact_phone ?: '—' }}</div>
                                            </td>
                                            <td class="py-3.5 px-4 text-[#334155] font-medium whitespace-nowrap">
                                                {{ $t->assignedEngineer?->name ?: '—' }}
                                            </td>
                                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                                @if($t->status === 'Resolved' || $t->status === 'Closed')
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                        Resolved
                                                    </span>
                                                @elseif($t->status === 'In Progress')
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                        In Progress
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-[#8F0A0D] border border-rose-200">
                                                        Open
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                                @if($t->attachment_path)
                                                    <a href="{{ asset('storage/' . $t->attachment_path) }}" target="_blank" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10.5px] font-bold bg-[#F1F5F9] text-[#1E293B] border border-[#CBD5E1] hover:bg-[#E2E8F0] transition">
                                                        <svg class="w-3 h-3 text-[#64748B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                        <span>Lihat BAP</span>
                                                    </a>
                                                @else
                                                    <span class="text-slate-400 text-[11px]">—</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
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
                        <p class="text-[10.5px] font-bold text-[#8F0A0D] uppercase tracking-wider">Upload Berkas Dokumen</p>
                        <h3 class="text-[15px] font-bold text-[#1E293B]" x-text="selectedDocTitle"></h3>
                    </div>
                    <button @click="isDocModalOpen = false" class="text-slate-400 hover:text-slate-700 p-1 rounded-lg text-lg font-bold">&times;</button>
                </div>

                <form action="{{ route('ms.projects.documents.update', $project->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <input type="hidden" name="doc_type" :value="selectedDocType">

                    <div>
                        <label class="block text-xs font-semibold text-[#334155] mb-1">Nomor Dokumen (Opsional)</label>
                        <input type="text" name="doc_number" placeholder="Contoh: SPK/2026/001 atau KTR-014" class="w-full px-3 py-2 bg-white border border-[#CBD5E1] rounded-xl text-xs text-[#1E293B] outline-none focus:border-[#8F0A0D]">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#334155] mb-1">Tanggal Dokumen (Opsional)</label>
                        <input type="date" name="doc_date" class="w-full px-3 py-2 bg-white border border-[#CBD5E1] rounded-xl text-xs text-[#1E293B] outline-none focus:border-[#8F0A0D]">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#334155] mb-1">Pilih Berkas File (PDF / Word / Gambar / ZIP) <span class="text-[#8F0A0D]">*</span></label>
                        <input type="file" name="doc_file" required 
                               @change="if ($event.target.files.length && $event.target.files[0].size > 52428800) { alert('Ukuran berkas ' + $event.target.files[0].name + ' melebihi batas 50MB. Silakan pilih berkas lain.'); $event.target.value = ''; }"
                               class="w-full px-3 py-2 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-xs text-[#1E293B] outline-none focus:border-[#8F0A0D] cursor-pointer">
                        <p class="text-[10.5px] text-[#94A3B8] mt-1">Maksimal ukuran file: 50MB.</p>
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-[#E2E8F0]">
                        <button type="button" @click="isDocModalOpen = false" class="px-4 py-2 rounded-xl text-xs font-semibold text-[#64748B] hover:bg-[#F8FAFC]">Batal</button>
                        <button type="submit" class="btn-ipnet-gradient px-4 py-2 rounded-xl text-white text-xs font-bold shadow-md cursor-pointer">Upload Sekarang</button>
                    </div>
                </form>
            </div>
        </div>
    </template>

    <!-- ========================================================== -->
    <!-- MODAL 2: UPDATE SIKLUS PM                                  -->
    <!-- ========================================================== -->
    <template x-teleport="body">
        <div x-show="isPmModalOpen" x-cloak class="fixed inset-0 z-50 bg-[#0E0D12]/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-[#E2E8F0] max-h-[90vh] overflow-y-auto" @click.away="isPmModalOpen = false">
                <div class="flex items-center justify-between border-b border-[#E2E8F0] pb-3 mb-4">
                    <div>
                        <p class="text-[10.5px] font-bold text-emerald-700 uppercase tracking-wider">Update Realisasi PM</p>
                        <h3 class="text-[15px] font-bold text-[#1E293B]" x-text="activeCycle ? activeCycle.cycle_label : 'Siklus PM'"></h3>
                    </div>
                    <button @click="isPmModalOpen = false" class="text-slate-400 hover:text-slate-700 p-1 rounded-lg text-lg font-bold">&times;</button>
                </div>

                <form :action="'/managed-service/monitoring/pm-cycles/' + (activeCycle ? activeCycle.id : '')" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-xs font-semibold text-[#334155] mb-1">Status Kunjungan (Activity) <span class="text-[#8F0A0D]">*</span></label>
                        <select name="activity_status" :value="activeCycle ? activeCycle.activity_status : 'Scheduled'" required class="w-full px-3 py-2 bg-white border border-[#CBD5E1] rounded-xl text-xs font-bold text-[#1E293B] outline-none focus:border-[#8F0A0D] cursor-pointer">
                            <option value="Scheduled">Scheduled (Terjadwal)</option>
                            <option value="In Progress">In Progress (Sedang Dikerjakan)</option>
                            <option value="Completed">Completed (Selesai Dilaksanakan)</option>
                        </select>
                    </div>

                    {{-- Jadwal Target & Tanggal Realisasi Visit (Bisa Diubah oleh Tim MS) --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div>
                            <label class="block text-xs font-semibold text-[#334155] mb-1">
                                Jadwal Target Kunjungan <span class="text-[#8F0A0D]">*</span>
                            </label>
                            <input type="date" name="scheduled_date" :value="activeCycle ? (activeCycle.scheduled_date ? String(activeCycle.scheduled_date).split('T')[0] : '') : ''" required class="w-full px-3 py-2 bg-white border border-[#CBD5E1] rounded-xl text-xs font-bold text-[#1E293B] outline-none focus:border-[#8F0A0D]">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-[#334155] mb-1">
                                Tanggal Realisasi Visit
                            </label>
                            <input type="date" name="actual_date" :value="activeCycle ? (activeCycle.actual_date ? String(activeCycle.actual_date).split('T')[0] : '') : ''" class="w-full px-3 py-2 bg-white border border-[#CBD5E1] rounded-xl text-xs text-[#1E293B] outline-none focus:border-[#8F0A0D]">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#334155] mb-1">PIC Field Engineer</label>
                        <select name="assigned_engineer_id" :value="activeCycle ? activeCycle.assigned_engineer_id : ''" class="w-full px-3 py-2 bg-white border border-[#CBD5E1] rounded-xl text-xs text-[#1E293B] outline-none focus:border-[#8F0A0D] cursor-pointer">
                            <option value="">Pilih Field Engineer</option>
                            @foreach($engineers as $eng)
                                <option value="{{ $eng->id }}">{{ $eng->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#334155] mb-1">Upload Laporan PM (PDF)</label>
                        <input type="file" name="report_file" accept=".pdf" class="w-full px-3 py-2 border border-[#CBD5E1] rounded-xl text-xs bg-white outline-none focus:border-[#8F0A0D]">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div>
                            <label class="block text-xs font-semibold text-[#334155] mb-1">Status Laporan PM</label>
                            <select name="report_status" :value="activeCycle ? activeCycle.report_status : 'Pending'" class="w-full px-3 py-2 bg-white border border-[#CBD5E1] rounded-xl text-xs text-[#1E293B] outline-none focus:border-[#8F0A0D] cursor-pointer">
                                <option value="Pending">Pending (Belum Diupload)</option>
                                <option value="Submitted">Submitted (Menunggu Review)</option>
                                <option value="Approved">Approved (Disetujui Klien)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-[#334155] mb-1">Status BA / Milestone Penagihan</label>
                            <select name="billing_ba_status" :value="activeCycle ? activeCycle.billing_ba_status : 'Pending'" class="w-full px-3 py-2 bg-white border border-[#CBD5E1] rounded-xl text-xs text-[#1E293B] outline-none focus:border-[#8F0A0D] cursor-pointer">
                                <option value="Pending">Pending</option>
                                <option value="Submitted">Submitted</option>
                                <option value="Signed">Signed (Ditandatangani)</option>
                                <option value="Invoiced">Invoiced (Ditagihkan)</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#334155] mb-1">Upload Berita Acara (BA / Milestone)</label>
                        <input type="file" name="billing_ba_file" accept=".pdf,.doc,.docx" class="w-full px-3 py-2 border border-[#CBD5E1] rounded-xl text-xs bg-white outline-none focus:border-[#8F0A0D]">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#334155] mb-1">Catatan Pelaksanaan Inspeksi</label>
                        <textarea name="notes" rows="2" :value="activeCycle ? activeCycle.notes : ''" placeholder="Catatan temuan teknis saat kunjungan..." class="w-full px-3 py-2 border border-[#CBD5E1] rounded-xl text-xs text-[#1E293B] outline-none focus:border-[#8F0A0D]"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-[#E2E8F0]">
                        <button type="button" @click="isPmModalOpen = false" class="px-4 py-2 rounded-xl text-xs font-semibold text-[#64748B] hover:bg-[#F8FAFC]">
                            Batal
                        </button>
                        <button type="submit" class="btn-ipnet-gradient px-4 py-2 rounded-xl text-xs font-bold shadow-md text-white">
                            Simpan Realisasi PM
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>

    <!-- ========================================================== -->
    <!-- MODAL 3: CATAT TIKET CM                                    -->
    <!-- ========================================================== -->
    <template x-teleport="body">
        <div x-show="isCmModalOpen" x-cloak class="fixed inset-0 z-50 bg-[#0E0D12]/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-[#E2E8F0] max-h-[90vh] overflow-y-auto" @click.away="isCmModalOpen = false">
                <div class="flex items-center justify-between border-b border-[#E2E8F0] pb-3 mb-4">
                    <div>
                        <p class="text-[10.5px] font-bold text-[#8F0A0D] uppercase tracking-wider">Tiket Insiden / Perbaikan</p>
                        <h3 class="text-[15px] font-bold text-[#1E293B]">Catat Tiket Corrective Maintenance</h3>
                    </div>
                    <button @click="isCmModalOpen = false" class="text-slate-400 hover:text-slate-700 p-1 rounded-lg text-lg font-bold">&times;</button>
                </div>

                <form action="{{ route('ms.monitoring.cm.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <input type="hidden" name="project_id" value="{{ $project->id }}">

                    <div>
                        <label class="block text-xs font-semibold text-[#334155] mb-1">Judul Insiden / Aktivitas CM <span class="text-[#8F0A0D]">*</span></label>
                        <input type="text" name="title" required placeholder="Contoh: Penggantian SFP Module Port 2 Core Switch" class="w-full px-3 py-2 bg-white border border-[#CBD5E1] rounded-xl text-xs text-[#1E293B] outline-none focus:border-[#8F0A0D]">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div>
                            <label class="block text-xs font-semibold text-[#334155] mb-1">Prioritas & SLA <span class="text-[#8F0A0D]">*</span></label>
                            <select name="priority" required class="w-full px-3 py-2 bg-white border border-[#CBD5E1] rounded-xl text-xs text-[#1E293B] outline-none focus:border-[#8F0A0D] cursor-pointer">
                                <option value="Platinum">P1 - Critical (MTTR 1 Jam)</option>
                                <option value="Gold" selected>P2 - Major (MTTR 4 Jam)</option>
                                <option value="Silver">P3 - Minor (MTTR 8 Jam)</option>
                                <option value="Bronze">P4 - Low (MTTR 24 Jam)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-[#334155] mb-1">Tanggal Insiden</label>
                            <input type="date" name="incident_date" value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 bg-white border border-[#CBD5E1] rounded-xl text-xs text-[#1E293B] outline-none focus:border-[#8F0A0D]">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div>
                            <label class="block text-xs font-semibold text-[#334155] mb-1">Nama Pelapor / PIC Klien</label>
                            <input type="text" name="reported_by" placeholder="Nama PIC" class="w-full px-3 py-2 bg-white border border-[#CBD5E1] rounded-xl text-xs text-[#1E293B] outline-none focus:border-[#8F0A0D]">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-[#334155] mb-1">Kontak Telepon / WA</label>
                            <input type="text" name="contact_phone" placeholder="08xxxxxxxxxx" class="w-full px-3 py-2 bg-white border border-[#CBD5E1] rounded-xl text-xs text-[#1E293B] outline-none focus:border-[#8F0A0D]">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div>
                            <label class="block text-xs font-semibold text-[#334155] mb-1">PIC Field Support Engineer</label>
                            <select name="assigned_to" class="w-full px-3 py-2 bg-white border border-[#CBD5E1] rounded-xl text-xs text-[#1E293B] outline-none focus:border-[#8F0A0D] cursor-pointer">
                                <option value="">Pilih Teknisi</option>
                                @foreach($engineers as $eng)
                                    <option value="{{ $eng->id }}">{{ $eng->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-[#334155] mb-1">Status Tiket</label>
                            <select name="status" class="w-full px-3 py-2 bg-white border border-[#CBD5E1] rounded-xl text-xs text-[#1E293B] outline-none focus:border-[#8F0A0D] cursor-pointer">
                                <option value="Open">Open (Baru Diterima)</option>
                                <option value="In Progress">In Progress (Sedang Ditangani)</option>
                                <option value="Resolved">Resolved (Selesai)</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#334155] mb-1">Rincian Gangguan / Masalah</label>
                        <textarea name="description" rows="2" placeholder="Jelaskan indikasi error atau alasan kebutuhan corrective..." class="w-full px-3 py-2 border border-[#CBD5E1] rounded-xl text-xs text-[#1E293B] outline-none focus:border-[#8F0A0D]"></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#334155] mb-1">Upload Berita Acara Perbaikan (BAP / Foto)</label>
                        <input type="file" name="attachment" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" class="w-full px-3 py-2 border border-[#CBD5E1] rounded-xl text-xs bg-white outline-none focus:border-[#8F0A0D]">
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-[#E2E8F0]">
                        <button type="button" @click="isCmModalOpen = false" class="px-4 py-2 rounded-xl text-xs font-semibold text-[#64748B] hover:bg-[#F8FAFC]">
                            Batal
                        </button>
                        <button type="submit" class="btn-ipnet-gradient px-4 py-2 rounded-xl text-xs font-bold shadow-md text-white">
                            Catat Tiket CM
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>

    <!-- ========================================================== -->
    <!-- MODAL 4: UBAH PARAMETER SLA & KUOTA                        -->
    <!-- ========================================================== -->
    <template x-teleport="body">
        <div x-show="isSlaModalOpen" x-cloak class="fixed inset-0 z-50 bg-[#0E0D12]/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-xl w-full p-6 shadow-2xl border border-[#E2E8F0] max-h-[90vh] overflow-y-auto" @click.away="isSlaModalOpen = false">
                <div class="flex items-center justify-between border-b border-[#E2E8F0] pb-3 mb-4">
                    <div>
                        <p class="text-[10.5px] font-bold text-[#8F0A0D] uppercase tracking-wider">Konfigurasi Kontrak</p>
                        <h3 class="text-[15px] font-bold text-[#1E293B]">Ubah Parameter SLA & Kuota Tiket</h3>
                    </div>
                    <button @click="isSlaModalOpen = false" class="text-slate-400 hover:text-slate-700 p-1 rounded-lg text-lg font-bold">&times;</button>
                </div>

                <form action="{{ route('ms.projects.sla.update', $project->id) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div>
                            <label class="block text-xs font-semibold text-[#334155] mb-1">Kuota Corrective Maintenance (CM) <span class="text-[#8F0A0D]">*</span></label>
                            <select name="cm_quota" required class="w-full px-3 py-2 bg-white border border-[#CBD5E1] rounded-xl text-xs font-bold text-[#1E293B] outline-none focus:border-[#8F0A0D] cursor-pointer">
                                <option value="4" {{ ($project->cm_quota ?? 18) == 4 ? 'selected' : '' }}>4 Tiket / Tahun</option>
                                <option value="6" {{ ($project->cm_quota ?? 18) == 6 ? 'selected' : '' }}>6 Tiket / Tahun</option>
                                <option value="8" {{ ($project->cm_quota ?? 18) == 8 ? 'selected' : '' }}>8 Tiket / Tahun</option>
                                <option value="12" {{ ($project->cm_quota ?? 18) == 12 ? 'selected' : '' }}>12 Tiket / Tahun</option>
                                <option value="18" {{ ($project->cm_quota ?? 18) == 18 ? 'selected' : '' }}>18 Tiket / Tahun (Standar)</option>
                                <option value="999" {{ ($project->cm_quota ?? 18) == 999 ? 'selected' : '' }}>Unlimited Tiket</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-[#334155] mb-1">Frekuensi Preventive Maintenance (PM) <span class="text-[#8F0A0D]">*</span></label>
                            <select name="pm_frequency_type" required class="w-full px-3 py-2 bg-white border border-[#CBD5E1] rounded-xl text-xs font-bold text-[#1E293B] outline-none focus:border-[#8F0A0D] cursor-pointer">
                                <option value="per_bulan" {{ ($project->pm_frequency_type ?? 'per_3_bulan') == 'per_bulan' ? 'selected' : '' }}>Per Bulan (12 Kali/Tahun)</option>
                                <option value="per_2_bulan" {{ ($project->pm_frequency_type ?? 'per_3_bulan') == 'per_2_bulan' ? 'selected' : '' }}>Per 2 Bulan (6 Kali/Tahun)</option>
                                <option value="per_3_bulan" {{ ($project->pm_frequency_type ?? 'per_3_bulan') == 'per_3_bulan' ? 'selected' : '' }}>Per 3 Bulan / Quarterly (4 Kali/Tahun)</option>
                                <option value="per_4_bulan" {{ ($project->pm_frequency_type ?? 'per_3_bulan') == 'per_4_bulan' ? 'selected' : '' }}>Per 4 Bulan (3 Kali/Tahun)</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div>
                            <label class="block text-xs font-semibold text-[#334155] mb-1">Target SLA Uptime (%) <span class="text-[#8F0A0D]">*</span></label>
                            <input type="number" step="0.01" min="90" max="100" name="sla_target_percentage" value="{{ $project->sla_target_percentage ?: 99.50 }}" required class="w-full px-3 py-2 bg-white border border-[#CBD5E1] rounded-xl text-xs text-[#1E293B] outline-none focus:border-[#8F0A0D]">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-[#334155] mb-1">SLA Tier</label>
                            <select name="sla_tier" class="w-full px-3 py-2 bg-white border border-[#CBD5E1] rounded-xl text-xs font-bold text-[#1E293B] outline-none focus:border-[#8F0A0D] cursor-pointer">
                                <option value="Platinum" {{ ($project->sla_tier ?? 'Gold') == 'Platinum' ? 'selected' : '' }}>Platinum (Mission Critical)</option>
                                <option value="Gold" {{ ($project->sla_tier ?? 'Gold') == 'Gold' ? 'selected' : '' }}>Gold (Standard SLA)</option>
                                <option value="Silver" {{ ($project->sla_tier ?? 'Gold') == 'Silver' ? 'selected' : '' }}>Silver (Basic SLA)</option>
                                <option value="Bronze" {{ ($project->sla_tier ?? 'Gold') == 'Bronze' ? 'selected' : '' }}>Bronze (Best Effort)</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                        <div>
                            <label class="block text-xs font-semibold text-[#334155] mb-1">MTTR (Jam)</label>
                            <input type="number" min="1" max="72" name="mttr_hours" value="{{ $project->mttr_hours ?: 4 }}" class="w-full px-3 py-2 bg-white border border-[#CBD5E1] rounded-xl text-xs text-[#1E293B] outline-none focus:border-[#8F0A0D]">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-[#334155] mb-1">Coverage Hours</label>
                            <input type="text" name="sla_coverage_hours" value="{{ $project->sla_coverage_hours ?: '24x7' }}" placeholder="24x7 atau 8x5" class="w-full px-3 py-2 bg-white border border-[#CBD5E1] rounded-xl text-xs text-[#1E293B] outline-none focus:border-[#8F0A0D]">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-[#334155] mb-1">Call Response</label>
                            <input type="text" name="call_response_time" value="{{ $project->call_response_time ?: '15 menit' }}" placeholder="15 menit" class="w-full px-3 py-2 bg-white border border-[#CBD5E1] rounded-xl text-xs text-[#1E293B] outline-none focus:border-[#8F0A0D]">
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-[#E2E8F0]">
                        <button type="button" @click="isSlaModalOpen = false" class="px-4 py-2 rounded-xl text-xs font-semibold text-[#64748B] hover:bg-[#F8FAFC]">Batal</button>
                        <button type="submit" class="btn-ipnet-gradient px-4 py-2 rounded-xl text-white text-xs font-bold shadow-md cursor-pointer">Simpan Perubahan SLA</button>
                    </div>
                </form>
            </div>
        </div>
    </template>

</div>
@endsection
