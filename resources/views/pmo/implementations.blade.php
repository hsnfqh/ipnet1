@extends('layouts.app')

@section('title', 'Tata Kelola Proyek - PMO Implementasi & Handover')

@section('content')
<div class="flex h-screen overflow-hidden" x-data="pmoDashboard()" x-cloak>
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto bg-[#F8FAFC]">
        @include('components.topbar', ['title' => 'Tata Kelola Proyek'])
        
        <div class="p-4 sm:p-6 lg:p-8 space-y-6 max-w-[1680px] mx-auto">
            
            <!-- ========================================================== -->
            <!-- 1. HEADER BANNER (IP-NET ENTERPRISE CONTROL TOWER)        -->
            <!-- ========================================================== -->
            <div class="bg-white p-6 sm:p-7 rounded-2xl border border-slate-200/90 shadow-2xs relative overflow-hidden">
                <div class="absolute -right-16 -top-16 w-56 h-56 bg-red-500/5 rounded-full blur-3xl pointer-events-none"></div>
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5 relative z-10">
                    <div>
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-red-50 border border-red-100 mb-2.5">
                            <span class="w-2 h-2 rounded-full bg-[#8F0A0D] animate-pulse"></span>
                            <span class="text-[#8F0A0D] text-[11px] font-bold uppercase tracking-wider">PROJECT MANAGEMENT OFFICE &bull; TATA KELOLA IMPLEMENTASI</span>
                        </div>
                        <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Pusat Tata Kelola Proyek &amp; Serah Terima</h2>
                        <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-2xl leading-relaxed">
                            Manajemen terpadu implementasi teknis tahap Deliver, monitoring kepatuhan timeline jadwal (SLA), verifikasi kelengkapan berkas serah terima dari Sales, serta transisi ke Managed Service.
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                        <div class="px-3.5 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs font-bold text-slate-800 flex items-center gap-2 shadow-2xs">
                            <span class="text-slate-400 font-medium">Total Implementasi:</span>
                            <span class="text-[#8F0A0D] font-black text-sm" x-text="stageCounts.Deliver || 0"></span>
                            <span class="text-slate-400 text-[11px] font-normal">Proyek</span>
                        </div>
                        <div class="px-3.5 py-2 rounded-xl bg-emerald-50/80 border border-emerald-200 text-xs font-bold text-emerald-800 flex items-center gap-2 shadow-2xs">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span class="text-emerald-700 font-medium">Sesuai Jadwal:</span>
                            <span class="text-emerald-900 font-black text-sm" x-text="onTrackCount"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ========================================================== -->
            <!-- 2. 4 EXECUTIVE KPI METRIC CARDS                            -->
            <!-- ========================================================== -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
                
                {{-- Card 1: Deliver Aktif --}}
                <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-2xs hover:shadow-md hover:border-red-200 transition-all flex flex-col justify-between group">
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div class="space-y-0.5">
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">TAHAP DELIVER</span>
                            <h3 class="text-xs font-bold text-slate-700">Implementasi Teknis</h3>
                        </div>
                        <div class="w-11 h-11 rounded-xl bg-red-50 border border-red-100 text-[#8F0A0D] flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform shadow-2xs">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-baseline gap-2">
                            <span class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight" x-text="stageCounts.Deliver || 0"></span>
                            <span class="text-xs font-semibold text-slate-400">Proyek Aktif</span>
                        </div>
                        <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                            <span>Siklus Pelaksanaan</span>
                            <span class="font-bold text-[#8F0A0D]">Fase Lapangan</span>
                        </div>
                    </div>
                </div>

                {{-- Card 2: Kepatuhan Timeline (SLA) --}}
                <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-2xs hover:shadow-md hover:border-emerald-200 transition-all flex flex-col justify-between group">
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div class="space-y-0.5">
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">KEPATUHAN JADWAL</span>
                            <h3 class="text-xs font-bold text-slate-700">Kepatuhan Timeline (SLA)</h3>
                        </div>
                        <div class="w-11 h-11 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-700 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform shadow-2xs">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-baseline gap-2">
                            <span class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight" x-text="onTrackCount"></span>
                            <span class="px-2 py-0.5 rounded-full text-[10.5px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Sesuai Jadwal</span>
                        </div>
                        <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-[11px]">
                            <span class="text-slate-500">Monitoring Risiko:</span>
                            <template x-if="delayedCount > 0">
                                <span class="font-bold text-rose-600" x-text="delayedCount + ' Melewati Tenggat'"></span>
                            </template>
                            <template x-if="delayedCount === 0 && atRiskCount > 0">
                                <span class="font-bold text-amber-700" x-text="atRiskCount + ' Mendekati Tenggat'"></span>
                            </template>
                            <template x-if="delayedCount === 0 && atRiskCount === 0">
                                <span class="font-semibold text-emerald-700">100% Proyek Tepat Waktu</span>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- Card 3: Gerbang Serah Terima (Handover Gateway) --}}
                <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-2xs hover:shadow-md hover:border-amber-200 transition-all flex flex-col justify-between group">
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div class="space-y-0.5">
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">SERAH TERIMA SALES</span>
                            <h3 class="text-xs font-bold text-slate-700">Verifikasi Dokumen PMO</h3>
                        </div>
                        <div class="w-11 h-11 rounded-xl bg-amber-50 border border-amber-100 text-amber-700 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform shadow-2xs">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-baseline gap-2">
                            <span class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight" x-text="handoverPendingCount"></span>
                            <span class="px-2 py-0.5 rounded-full text-[10.5px] font-bold bg-amber-50 text-amber-800 border border-amber-200">Menunggu Review</span>
                        </div>
                        <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                            <span>Status Revisi:</span>
                            <span class="font-bold text-amber-700" x-text="handoverConditionalCount + ' Bersyarat'"></span>
                        </div>
                    </div>
                </div>

                {{-- Card 4: Transisi Operasional & Managed Service --}}
                <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-2xs hover:shadow-md hover:border-blue-200 transition-all flex flex-col justify-between group">
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div class="space-y-0.5">
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">TRANSISI OPERASIONAL</span>
                            <h3 class="text-xs font-bold text-slate-700">Kesiapan Managed Service</h3>
                        </div>
                        <div class="w-11 h-11 rounded-xl bg-blue-50 border border-blue-100 text-blue-700 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform shadow-2xs">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-baseline gap-2">
                            <span class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight" x-text="readyToOperateCount"></span>
                            <span class="px-2 py-0.5 rounded-full text-[10.5px] font-bold bg-blue-50 text-blue-700 border border-blue-200">Siap Handover MS</span>
                        </div>
                        <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                            <span>Fase Operate Aktif:</span>
                            <span class="font-bold text-slate-800" x-text="(stageCounts.Operate || 0) + ' Proyek'"></span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- ========================================================== -->
            <!-- 3. HANDOVER PENDING ALERT BANNER (IF ANY)                  -->
            <!-- ========================================================== -->
            <template x-if="handoverPendingCount > 0 || handoverConditionalCount > 0">
                <div class="p-4 sm:p-5 rounded-2xl bg-gradient-to-r from-amber-50 to-orange-50/70 border border-amber-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-xs">
                    <div class="flex items-start gap-3.5">
                        <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center shrink-0 shadow-xs">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h4 class="text-xs font-bold text-amber-950 uppercase tracking-wide">Pemberitahuan Gatekeeper Handover Proyek</h4>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-200/80 text-amber-900" x-text="handoverPendingCount + ' Menunggu'"></span>
                            </div>
                            <p class="text-xs text-amber-800 mt-1 leading-relaxed">
                                Terdapat <strong class="text-amber-950 font-bold" x-text="handoverPendingCount"></strong> berkas serah terima dari Sales yang siap diverifikasi PMO, dan <strong class="text-amber-950 font-bold" x-text="handoverConditionalCount"></strong> berkas revisi bersyarat.
                            </p>
                        </div>
                    </div>
                    <button type="button" 
                            @click="activeTab = (activeTab === 'pending_handover' ? 'all' : 'pending_handover'); currentPage = 1;"
                            class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold rounded-xl transition cursor-pointer shadow-xs whitespace-nowrap self-start sm:self-center flex items-center gap-2">
                        <span x-text="activeTab === 'pending_handover' ? 'Tampilkan Semua Portofolio' : 'Tinjau Antrean Serah Terima'"></span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
            </template>

            <!-- ========================================================== -->
            <!-- 4. DAFTAR PORTOFOLIO PROYEK & KONTROL IMPLEMENTASI        -->
            <!-- ========================================================== -->
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden">
                
                {{-- A. QUICK FILTER TABS --}}
                <div class="p-3 sm:px-6 pt-4 border-b border-slate-100 flex items-center justify-between gap-3 overflow-x-auto bg-slate-50/50">
                    <div class="flex items-center gap-2">
                        <button type="button" 
                                @click="activeTab = 'all'; currentPage = 1;"
                                :class="activeTab === 'all' ? 'bg-white text-slate-900 border-slate-300 shadow-2xs font-bold' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-100 border-transparent font-semibold'"
                                class="px-3.5 py-1.5 rounded-xl border text-xs transition cursor-pointer flex items-center gap-2 whitespace-nowrap">
                            <span>Semua Portofolio</span>
                            <span class="px-1.5 py-0.5 rounded-md text-[10.5px] bg-slate-100 text-slate-600" x-text="projects.length"></span>
                        </button>

                        <button type="button" 
                                @click="activeTab = 'deliver'; currentPage = 1;"
                                :class="activeTab === 'deliver' ? 'bg-white text-[#8F0A0D] border-red-200 shadow-2xs font-bold' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-100 border-transparent font-semibold'"
                                class="px-3.5 py-1.5 rounded-xl border text-xs transition cursor-pointer flex items-center gap-2 whitespace-nowrap">
                            <span class="w-2 h-2 rounded-full bg-[#8F0A0D]"></span>
                            <span>Tahap Deliver (Aktif)</span>
                            <span class="px-1.5 py-0.5 rounded-md text-[10.5px] bg-red-50 text-[#8F0A0D] font-bold" x-text="stageCounts.Deliver || 0"></span>
                        </button>

                        <button type="button" 
                                @click="activeTab = 'pending_handover'; currentPage = 1;"
                                :class="activeTab === 'pending_handover' ? 'bg-white text-amber-800 border-amber-300 shadow-2xs font-bold' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-100 border-transparent font-semibold'"
                                class="px-3.5 py-1.5 rounded-xl border text-xs transition cursor-pointer flex items-center gap-2 whitespace-nowrap">
                            <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                            <span>Menunggu Review PMO</span>
                            <span class="px-1.5 py-0.5 rounded-md text-[10.5px] bg-amber-100 text-amber-800 font-bold" x-text="handoverPendingCount"></span>
                        </button>

                        <button type="button" 
                                @click="activeTab = 'ready_ms'; currentPage = 1;"
                                :class="activeTab === 'ready_ms' ? 'bg-white text-blue-700 border-blue-300 shadow-2xs font-bold' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-100 border-transparent font-semibold'"
                                class="px-3.5 py-1.5 rounded-xl border text-xs transition cursor-pointer flex items-center gap-2 whitespace-nowrap">
                            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                            <span>Siap Transisi MS</span>
                            <span class="px-1.5 py-0.5 rounded-md text-[10.5px] bg-blue-100 text-blue-700 font-bold" x-text="readyToOperateCount"></span>
                        </button>
                    </div>

                    {{-- View Mode Toggle (Table vs Cards) --}}
                    <div class="flex items-center gap-1 bg-slate-200/60 p-1 rounded-xl shrink-0">
                        <button type="button" 
                                @click="viewMode = 'table'" 
                                :class="viewMode === 'table' ? 'bg-white text-[#8F0A0D] shadow-2xs font-bold' : 'text-slate-500 hover:text-slate-800'"
                                title="Tampilan Tabel Rinci"
                                class="px-2.5 py-1 rounded-lg text-xs transition cursor-pointer flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                            <span class="hidden sm:inline">Tabel</span>
                        </button>
                        <button type="button" 
                                @click="viewMode = 'cards'" 
                                :class="viewMode === 'cards' ? 'bg-white text-[#8F0A0D] shadow-2xs font-bold' : 'text-slate-500 hover:text-slate-800'"
                                title="Tampilan Kartu Portofolio"
                                class="px-2.5 py-1 rounded-lg text-xs transition cursor-pointer flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                            <span class="hidden sm:inline">Kartu</span>
                        </button>
                    </div>
                </div>

                {{-- B. Table Toolbar: Search & Select Filters --}}
                <div class="p-4 sm:p-5 border-b border-slate-100 flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3.5 bg-white">
                    <div class="relative w-full md:w-96">
                        <input type="text" 
                               x-model="search" 
                               @input="currentPage = 1" 
                               placeholder="Cari nama proyek, nomor SO, klien, atau PIC..." 
                               class="w-full pl-9 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-900 placeholder-slate-400 focus:bg-white focus:border-[#8F0A0D] focus:ring-2 focus:ring-red-500/20 transition outline-none">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>

                    <div class="flex flex-wrap items-center gap-2.5">
                        {{-- Filter Divisi --}}
                        <select x-model="selectedDivision" @change="currentPage = 1" 
                                class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 outline-none hover:border-slate-300 focus:border-[#8F0A0D] focus:bg-white transition cursor-pointer">
                            <option value="all">Semua Divisi Pelaksana</option>
                            @foreach($divisions ?? [] as $div)
                                <option value="{{ $div->id }}">{{ $div->name }}</option>
                            @endforeach
                        </select>

                        {{-- Filter PM --}}
                        <select x-model="selectedPm" @change="currentPage = 1" 
                                class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 outline-none hover:border-slate-300 focus:border-[#8F0A0D] focus:bg-white transition cursor-pointer">
                            <option value="all">Semua Project Manager</option>
                            @foreach($pmList ?? [] as $pm)
                                <option value="{{ $pm->id }}">{{ $pm->name }}</option>
                            @endforeach
                        </select>

                        {{-- Total Counter Badge --}}
                        <span class="px-3 py-2 bg-slate-100/70 border border-slate-200 rounded-xl text-xs text-slate-600 font-medium whitespace-nowrap">
                            Hasil: <strong class="text-slate-900 font-extrabold" x-text="filteredProjects.length"></strong> Proyek
                        </span>
                    </div>
                </div>

                {{-- C1. TAMPILAN TABEL MODERN & LEGA (Fits 100% with No Ugly Wrapping) --}}
                <div x-show="viewMode === 'table'" class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="border-b border-slate-200/80 bg-slate-50/70 text-[10.5px] font-bold text-slate-400 uppercase tracking-wider">
                                <th class="py-3.5 px-4 sm:px-6 w-[34%]">PROYEK &amp; KLIEN</th>
                                <th class="py-3.5 px-4 w-[18%]">DIVISI &amp; PIC PM</th>
                                <th class="py-3.5 px-4 w-[18%]">STATUS TATA KELOLA</th>
                                <th class="py-3.5 px-4 w-[18%]">TENGGAT &amp; PROGRES</th>
                                <th class="py-3.5 px-4 sm:px-6 text-right w-[12%]">TINDAKAN</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                            <template x-for="project in paginatedProjects" :key="project.id">
                                <tr class="hover:bg-slate-50/80 transition-colors" :class="project.handover_status === 'Submitted' ? 'bg-amber-50/20' : ''">
                                    
                                    {{-- Kolom 1: Proyek & Klien (Dominan & Lega, Bebas Patah Kata) --}}
                                    <td class="py-4 px-4 sm:px-6 align-top">
                                        <div class="space-y-1.5">
                                            <a :href="'/projects/' + project.id" class="font-extrabold text-slate-900 text-sm hover:text-[#8F0A0D] transition block leading-snug" x-text="project.name"></a>
                                            
                                            <div class="flex items-center gap-2 flex-wrap text-[11px]">
                                                <span class="inline-flex items-center gap-1 font-semibold text-slate-700 bg-slate-100 px-2 py-0.5 rounded-md">
                                                    <svg class="w-3 h-3 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                                    <span x-text="project.client"></span>
                                                </span>

                                                <span class="inline-flex items-center gap-1 font-bold text-[#8F0A0D] bg-red-50 border border-red-100 px-2 py-0.5 rounded-md">
                                                    <svg class="w-3 h-3 text-[#8F0A0D] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                                    <span>Sales: </span><span x-text="project.sales_name"></span>
                                                </span>

                                                <template x-if="project.so_code">
                                                    <span class="font-mono text-[10.5px] text-slate-400 px-1 py-0.5 bg-slate-50 border border-slate-200 rounded" x-text="project.so_code"></span>
                                                </template>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Kolom 2: Divisi & PIC PM --}}
                                    <td class="py-4 px-4 align-top whitespace-nowrap">
                                        <div class="space-y-1.5">
                                            <div>
                                                <span class="px-2.5 py-0.5 rounded-md text-[10.5px] font-bold border inline-block"
                                                      :class="{
                                                          'bg-indigo-50 text-indigo-700 border-indigo-200': (project.division || '').includes('&') || ((project.division || '').includes('Network') && (project.division || '').includes('Security')),
                                                          'bg-blue-50 text-blue-700 border-blue-200': (project.division || '').includes('Network') && !(project.division || '').includes('Security'),
                                                          'bg-purple-50 text-purple-700 border-purple-200': (project.division || '').includes('Security') && !(project.division || '').includes('Network'),
                                                          'bg-amber-50 text-amber-800 border-amber-200': !(project.division) || (project.division || '').includes('Belum')
                                                      }"
                                                      x-text="project.division || 'Belum Didelegasikan'">
                                                </span>
                                            </div>
                                            <div>
                                                <template x-if="project.pm && project.pm !== 'Belum Ditentukan'">
                                                    <div class="flex items-center gap-1.5 text-[11.5px]">
                                                        <div class="w-5 h-5 rounded-full bg-slate-200 text-slate-700 font-extrabold text-[9px] flex items-center justify-center shrink-0"
                                                             x-text="project.pm.substring(0, 2).toUpperCase()">
                                                        </div>
                                                        <span class="font-bold text-slate-800" x-text="project.pm"></span>
                                                    </div>
                                                </template>
                                                <template x-if="!project.pm || project.pm === 'Belum Ditentukan'">
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200/80">
                                                        <svg class="w-3 h-3 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                                        <span>Belum Ada PM</span>
                                                    </span>
                                                </template>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Kolom 3: Status Tata Kelola & Dokumen Handover --}}
                                    <td class="py-4 px-4 align-top whitespace-nowrap">
                                        <div class="space-y-1.5">
                                            <div>
                                                {{-- Status Review --}}
                                                <template x-if="project.handover_status === 'Submitted'">
                                                    <button type="button" 
                                                            @click="openHandoverReviewModal(project)"
                                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10.5px] font-bold bg-amber-50 text-amber-900 border border-amber-300 hover:bg-amber-100 transition cursor-pointer shadow-2xs">
                                                        <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                                                        <span>Menunggu Review PMO</span>
                                                    </button>
                                                </template>

                                                <template x-if="project.handover_status === 'Conditional'">
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10.5px] font-bold bg-orange-50 text-orange-800 border border-orange-200" :title="project.handover_conditional_notes">
                                                        <svg class="w-3 h-3 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                                        <span>Revisi Bersyarat</span>
                                                    </span>
                                                </template>

                                                <template x-if="project.handover_status === 'Approved' && project.progress < 100">
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10.5px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                                        <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                                        <span>Serah Terima Disetujui</span>
                                                    </span>
                                                </template>

                                                <template x-if="project.stage === 'Deliver' && project.progress >= 100">
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10.5px] font-bold bg-blue-50 text-blue-800 border border-blue-200">
                                                        <svg class="w-3 h-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                                        <span>Siap Transisi MS</span>
                                                    </span>
                                                </template>

                                                <template x-if="project.handover_status !== 'Submitted' && project.handover_status !== 'Conditional' && project.handover_status !== 'Approved' && project.progress < 100">
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10.5px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                                        <span>Dalam Pelaksanaan</span>
                                                    </span>
                                                </template>
                                            </div>

                                            {{-- Dokumen Handover --}}
                                            <div>
                                                <button type="button" 
                                                        @click="openDocumentsModal(project)"
                                                        class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md border text-[11px] font-semibold transition cursor-pointer shadow-2xs group"
                                                        :class="project.docs_completed_count >= 11 ? 'bg-emerald-50 text-emerald-800 border-emerald-200' : 'bg-slate-50 hover:bg-red-50 text-slate-700 hover:text-[#8F0A0D] border-slate-200 hover:border-red-200'">
                                                    <svg class="w-3 h-3" :class="project.docs_completed_count >= 11 ? 'text-emerald-600' : 'text-slate-400 group-hover:text-[#8F0A0D]'" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                    <span class="font-extrabold" x-text="project.docs_completed_count + '/11'"></span>
                                                    <span x-text="project.docs_completed_count >= 11 ? 'Lengkap' : 'Dokumen'"></span>
                                                </button>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Kolom 4: Target Tenggat & Progres Fisik --}}
                                    <td class="py-4 px-4 align-top whitespace-nowrap">
                                        <div class="space-y-1.5">
                                            <div class="flex items-center gap-2">
                                                <div class="flex items-center gap-1 font-bold text-slate-800 text-xs">
                                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                    <span x-text="project.deadline || '-'"></span>
                                                </div>
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold border"
                                                      :class="{
                                                          'bg-emerald-50 text-emerald-700 border-emerald-200': project.health_status === 'On-Track',
                                                          'bg-rose-50 text-rose-700 border-rose-200': project.health_status === 'Delayed',
                                                          'bg-amber-50 text-amber-800 border-amber-200': project.health_status === 'At-Risk'
                                                      }">
                                                    <span class="w-1.5 h-1.5 rounded-full" :class="{
                                                        'bg-emerald-500': project.health_status === 'On-Track',
                                                        'bg-rose-500': project.health_status === 'Delayed',
                                                        'bg-amber-500 animate-pulse': project.health_status === 'At-Risk'
                                                    }"></span>
                                                    <span x-text="project.health_status === 'On-Track' ? 'Sesuai Jadwal' : (project.health_status === 'Delayed' ? 'Melewati Tenggat' : 'Mendekati Tenggat')"></span>
                                                </span>
                                            </div>

                                            <div class="w-36">
                                                <div class="flex items-center justify-between text-[11px] font-bold text-slate-900 mb-0.5">
                                                    <span x-text="project.progress + '%'"></span>
                                                    <span class="text-[9.5px] text-slate-400 font-semibold" x-text="project.completed_tasks + '/' + project.total_tasks + ' Task'"></span>
                                                </div>
                                                <div class="w-full h-1.5 bg-slate-100 rounded-full overflow-hidden border border-slate-200/60">
                                                    <div class="h-full rounded-full transition-all duration-300"
                                                         :class="project.progress >= 100 ? 'bg-emerald-500' : 'bg-gradient-to-r from-red-600 to-[#8F0A0D]'"
                                                         :style="'width: ' + project.progress + '%'"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Kolom 5: Tindakan / Aksi --}}
                                    <td class="py-4 px-4 sm:px-6 align-middle text-right whitespace-nowrap">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <template x-if="project.handover_status === 'Submitted'">
                                                <button type="button"
                                                        @click="openHandoverReviewModal(project)"
                                                        class="inline-flex items-center gap-1 px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-xs font-bold transition shadow-xs cursor-pointer">
                                                    <span>Verifikasi</span>
                                                </button>
                                            </template>

                                            <template x-if="project.stage === 'Deliver' && project.progress >= 100">
                                                <button type="button"
                                                        @click="openHandoverToMsModal(project)"
                                                        class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition shadow-xs cursor-pointer">
                                                    <span>Handover MS</span>
                                                </button>
                                            </template>

                                            <a :href="'/projects/' + project.id" 
                                               title="Buka Lembar Kerja Proyek"
                                               class="inline-flex items-center gap-1 px-3 py-1.5 bg-white hover:bg-slate-50 text-slate-700 hover:text-[#8F0A0D] border border-slate-200 hover:border-slate-300 rounded-xl text-xs font-bold transition shadow-2xs">
                                                <span>Detail</span>
                                                <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-[#8F0A0D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            </template>

                            <template x-if="paginatedProjects.length === 0">
                                <tr>
                                    <td colspan="5" class="py-16 text-center text-slate-400">
                                        <div class="w-14 h-14 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-center mx-auto mb-3 text-slate-400 shadow-2xs">
                                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                        </div>
                                        <p class="font-bold text-slate-800 text-sm">Tidak ada proyek yang sesuai dengan kriteria filter</p>
                                        <p class="text-xs text-slate-400 mt-1">Silakan sesuaikan kata kunci pencarian, tab kategori, atau filter divisi di atas.</p>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                {{-- C2. TAMPILAN KARTU GRID INTERAKTIF (Cards View Mode) --}}
                <div x-show="viewMode === 'cards'" class="p-5 bg-slate-50/50">
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
                        <template x-for="project in paginatedProjects" :key="project.id">
                            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs hover:shadow-md hover:border-red-200 transition-all p-5 flex flex-col justify-between group">
                                <div class="space-y-3">
                                    {{-- Header Card: Status & SO --}}
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="px-2 py-0.5 rounded font-mono text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200" x-text="project.so_code || 'SO-IPNET'"></span>
                                        
                                        <template x-if="project.handover_status === 'Submitted'">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-900 border border-amber-300">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                                <span>Menunggu Review</span>
                                            </span>
                                        </template>
                                        <template x-if="project.handover_status === 'Approved' && project.progress < 100">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                                <span>✓ Handover Approved</span>
                                            </span>
                                        </template>
                                        <template x-if="project.stage === 'Deliver' && project.progress >= 100">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-800 border border-blue-200">
                                                <span>Siap Transisi MS</span>
                                            </span>
                                        </template>
                                        <template x-if="project.handover_status !== 'Submitted' && project.handover_status !== 'Approved' && project.progress < 100">
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">
                                                <span>Dalam Pelaksanaan</span>
                                            </span>
                                        </template>
                                    </div>

                                    {{-- Project Title --}}
                                    <div>
                                        <a :href="'/projects/' + project.id" class="font-extrabold text-slate-900 text-sm hover:text-[#8F0A0D] transition block line-clamp-2" x-text="project.name"></a>
                                    </div>

                                    {{-- Client & Sales Chips --}}
                                    <div class="flex items-center gap-1.5 flex-wrap text-xs">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 text-[11px] font-medium">
                                            <svg class="w-3 h-3 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                            <span x-text="project.client"></span>
                                        </span>
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-red-50 text-[#8F0A0D] text-[11px] font-bold border border-red-100">
                                            <svg class="w-3 h-3 text-[#8F0A0D] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                            <span x-text="project.sales_name"></span>
                                        </span>
                                    </div>

                                    {{-- Divisi & PM Info --}}
                                    <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200/80 flex items-center justify-between text-xs">
                                        <div>
                                            <span class="text-[10px] text-slate-400 block font-bold uppercase">Divisi:</span>
                                            <span class="font-bold text-slate-800 text-[11px]" x-text="project.division || 'Belum Didelegasikan'"></span>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-[10px] text-slate-400 block font-bold uppercase">PIC PM:</span>
                                            <span class="font-bold text-slate-800 text-[11px]" x-text="project.pm || 'Belum Ada PM'"></span>
                                        </div>
                                    </div>

                                    {{-- Deadline & SLA --}}
                                    <div class="flex items-center justify-between text-xs">
                                        <div class="flex items-center gap-1 text-slate-600 font-semibold text-[11.5px]">
                                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            <span x-text="project.deadline || '-'"></span>
                                        </div>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border"
                                              :class="{
                                                  'bg-emerald-50 text-emerald-700 border-emerald-200': project.health_status === 'On-Track',
                                                  'bg-rose-50 text-rose-700 border-rose-200': project.health_status === 'Delayed',
                                                  'bg-amber-50 text-amber-800 border-amber-200': project.health_status === 'At-Risk'
                                              }">
                                            <span x-text="project.health_status === 'On-Track' ? 'Sesuai Jadwal' : (project.health_status === 'Delayed' ? 'Melewati Tenggat' : 'Mendekati Tenggat')"></span>
                                        </span>
                                    </div>

                                    {{-- Progress Bar --}}
                                    <div>
                                        <div class="flex items-center justify-between text-xs font-bold text-slate-900 mb-1">
                                            <span>Progres Fisik</span>
                                            <span x-text="project.progress + '%'"></span>
                                        </div>
                                        <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden border border-slate-200/60">
                                            <div class="h-full rounded-full transition-all duration-300"
                                                 :class="project.progress >= 100 ? 'bg-emerald-500' : 'bg-gradient-to-r from-red-600 to-[#8F0A0D]'"
                                                 :style="'width: ' + project.progress + '%'"></div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Card Footer Actions --}}
                                <div class="mt-4 pt-3.5 border-t border-slate-100 flex items-center justify-between gap-2">
                                    <button type="button" 
                                            @click="openDocumentsModal(project)"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl border border-slate-200 hover:border-red-200 text-[11px] font-semibold text-slate-700 hover:text-[#8F0A0D] transition">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        <span x-text="project.docs_completed_count + '/11 Berkas'"></span>
                                    </button>

                                    <div class="flex items-center gap-1.5">
                                        <template x-if="project.handover_status === 'Submitted'">
                                            <button type="button"
                                                    @click="openHandoverReviewModal(project)"
                                                    class="px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-xs font-bold transition shadow-xs">
                                                Verifikasi
                                            </button>
                                        </template>

                                        <a :href="'/projects/' + project.id" class="px-3 py-1.5 bg-white hover:bg-slate-50 border border-slate-200 hover:border-slate-300 text-slate-800 hover:text-[#8F0A0D] rounded-xl text-xs font-bold transition shadow-2xs">
                                            Detail &rarr;
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <template x-if="paginatedProjects.length === 0">
                        <div class="py-16 text-center text-slate-400">
                            <p class="font-bold text-slate-800 text-sm">Tidak ada proyek yang sesuai dengan kriteria filter</p>
                            <p class="text-xs text-slate-400 mt-1">Silakan sesuaikan kata kunci pencarian atau tab di atas.</p>
                        </div>
                    </template>
                </div>

                {{-- D. Table Footer Pagination --}}
                <div class="p-4 sm:px-6 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-500 bg-white">
                    <div class="flex items-center gap-2 flex-wrap justify-center sm:justify-start">
                        <span>Tampilkan:</span>
                        <select x-model="perPage" @change="currentPage = 1" class="px-2.5 py-1 bg-slate-50 border border-slate-200 rounded-lg text-xs font-semibold text-slate-700 outline-none hover:border-slate-300 transition cursor-pointer">
                            <option value="10">10</option>
                            <option value="20">20</option>
                            <option value="30">30</option>
                            <option value="all">Semua</option>
                        </select>
                        <span class="text-slate-300">&bull;</span>
                        <div>
                            Menampilkan 
                            <span class="font-bold text-slate-800" x-text="filteredProjects.length > 0 ? (perPage === 'all' ? 1 : (currentPage - 1) * (parseInt(perPage) || 10) + 1) : 0"></span> &ndash; 
                            <span class="font-bold text-slate-800" x-text="perPage === 'all' ? filteredProjects.length : Math.min(currentPage * (parseInt(perPage) || 10), filteredProjects.length)"></span> 
                            dari <span class="font-bold text-slate-800" x-text="filteredProjects.length"></span> proyek
                        </div>
                    </div>

                    <div class="flex items-center gap-1" x-show="totalPages > 1 && perPage !== 'all'">
                        <button @click="prevPage()" :disabled="currentPage === 1" title="Sebelumnya" class="w-8 h-8 rounded-lg border border-slate-200 hover:bg-slate-50 text-slate-600 disabled:opacity-30 disabled:cursor-not-allowed flex items-center justify-center transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                        </button>
                        <template x-for="p in totalPages" :key="p">
                            <button @click="goToPage(p)" 
                                    :class="currentPage === p ? 'bg-[#8F0A0D] text-white border-[#8F0A0D] font-bold shadow-xs' : 'border-slate-200 hover:bg-slate-50 text-slate-700'"
                                    class="w-8 h-8 rounded-lg border text-xs font-semibold flex items-center justify-center transition"
                                    x-text="p">
                            </button>
                        </template>
                        <button @click="nextPage()" :disabled="currentPage === totalPages" title="Berikutnya" class="w-8 h-8 rounded-lg border border-slate-200 hover:bg-slate-50 text-slate-600 disabled:opacity-30 disabled:cursor-not-allowed flex items-center justify-center transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- MODAL REVIEW & PENGESAHAN HANDOVER INTERNAL (PMO GATEKEEPER) --}}
    <template x-teleport="body">
        <div x-show="handoverReviewModalOpen" 
             x-cloak
             class="fixed inset-0 bg-slate-900/60 backdrop-blur-2xs z-50 flex items-center justify-center p-3 sm:p-5 overflow-y-auto"
             @click.self="handoverReviewModalOpen = false">
            <div class="bg-white rounded-2xl w-[720px] max-w-full max-h-[92vh] flex flex-col shadow-2xl border border-slate-200 overflow-hidden my-6">
                {{-- Modal Header --}}
                <div class="p-5 sm:p-6 bg-slate-50 border-b border-slate-200 flex items-start justify-between">
                    <div>
                        <span class="text-[10.5px] font-bold uppercase tracking-wider text-blue-700 bg-blue-50 px-2.5 py-0.5 rounded-full border border-blue-200">
                            SOP Gatekeeper Handover Approval
                        </span>
                        <h3 class="text-base font-bold text-slate-900 mt-1.5" x-text="'Verifikasi Serah Terima: ' + activeProject?.name"></h3>
                        <p class="text-xs text-slate-500 mt-0.5" x-text="'Klien: ' + activeProject?.client + ' • PIC Sales: ' + activeProject?.sales_name"></p>
                    </div>
                    <button type="button" @click="handoverReviewModalOpen = false" class="w-8 h-8 rounded-xl bg-white border border-slate-200 text-slate-400 hover:text-slate-600 flex items-center justify-center text-lg font-bold cursor-pointer">
                        &times;
                    </button>
                </div>

                {{-- Modal Body --}}
                <div class="p-5 sm:p-6 overflow-y-auto flex-1 space-y-4">
                    
                    {{-- Status Banner --}}
                    <div class="p-3.5 rounded-xl border flex items-center justify-between text-xs"
                         :class="{
                             'bg-blue-50 border-blue-200 text-blue-800': activeProject?.handover_status === 'Submitted',
                             'bg-amber-50 border-amber-200 text-amber-800': activeProject?.handover_status === 'Conditional',
                             'bg-emerald-50 border-emerald-200 text-emerald-800': activeProject?.handover_status === 'Approved'
                         }">
                        <div>
                            <span class="font-bold">Status Berkas:</span>
                            <span x-text="activeProject?.handover_status === 'Submitted' ? 'Diajukan oleh Sales (Menunggu Verifikasi PMO)' : (activeProject?.handover_status === 'Conditional' ? 'Bersyarat (Dalam Masa Perbaikan 2x24 Jam)' : 'Disetujui & Resmi Masuk Tahap Deliver')"></span>
                        </div>
                        <template x-if="activeProject?.handover_conditional_deadline">
                            <span class="text-[11px] font-semibold text-amber-700" x-text="'Tenggat: ' + activeProject?.handover_conditional_deadline"></span>
                        </template>
                    </div>

                    {{-- 4 Kategori Checklist Summary --}}
                    <div class="space-y-3">
                        <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Hasil Verifikasi 4 Kategori Checklist Serah Terima</h4>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                            {{-- Kategori 1: Legal --}}
                            <div class="p-3 rounded-xl border border-slate-200 bg-slate-50/50 space-y-1.5">
                                <div class="font-bold text-slate-800 flex items-center justify-between">
                                    <span>I. Legal &amp; Komersial</span>
                                    <span class="text-[10px] text-slate-400">Kontrak, BoW &amp; Addendum</span>
                                </div>
                                <div class="text-slate-600 space-y-1 text-[11.5px]">
                                    <div class="flex items-center gap-1.5">
                                        <span :class="activeProject?.handover_data?.contract_signed ? 'text-emerald-600 font-bold' : 'text-slate-400'">&bull;</span>
                                        <span>Kontrak / SPK / SLA Signed: <strong x-text="activeProject?.handover_data?.contract_signed ? 'Ada' : 'Tidak'"></strong></span>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <span :class="activeProject?.handover_data?.bom_proposal ? 'text-emerald-600 font-bold' : 'text-slate-400'">&bull;</span>
                                        <span>Dokumen BoW (Proposal): <strong x-text="activeProject?.handover_data?.bom_proposal ? 'Ada' : 'Tidak'"></strong></span>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <span :class="activeProject?.handover_data?.negotiation_addendum ? 'text-emerald-600 font-bold' : 'text-slate-400'">&bull;</span>
                                        <span>BA Negosiasi / Addendum: <strong x-text="activeProject?.handover_data?.negotiation_addendum ? 'Ada' : 'Tidak'"></strong></span>
                                    </div>
                                </div>
                            </div>

                            {{-- Kategori 2: Lingkup --}}
                            <div class="p-3 rounded-xl border border-slate-200 bg-slate-50/50 space-y-1.5">
                                <div class="font-bold text-slate-800 flex items-center justify-between">
                                    <span>II. Kebutuhan &amp; Lingkup</span>
                                    <span class="text-[10px] text-slate-400">SoW, URS, Mockup &amp; Maint.</span>
                                </div>
                                <div class="text-slate-600 space-y-1 text-[11.5px]">
                                    <div class="flex items-center gap-1.5">
                                        <span :class="activeProject?.handover_data?.sow_document ? 'text-emerald-600 font-bold' : 'text-slate-400'">&bull;</span>
                                        <span>Dokumen SoW / Lingkup: <strong x-text="activeProject?.handover_data?.sow_document ? 'Ada' : 'Tidak'"></strong></span>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <span :class="activeProject?.handover_data?.urs_requirement ? 'text-emerald-600 font-bold' : 'text-slate-400'">&bull;</span>
                                        <span>Dokumen URS Klien: <strong x-text="activeProject?.handover_data?.urs_requirement ? 'Ada' : 'Tidak'"></strong></span>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <span :class="activeProject?.handover_data?.mockup_wireframe ? 'text-emerald-600 font-bold' : 'text-slate-400'">&bull;</span>
                                        <span>Mockup / Wireframe: <strong x-text="activeProject?.handover_data?.mockup_wireframe ? 'Ada' : 'Tidak'"></strong></span>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <span :class="activeProject?.handover_data?.maintenance_contract ? 'text-emerald-600 font-bold' : 'text-slate-400'">&bull;</span>
                                        <span>Kontrak Maintenance: <strong x-text="activeProject?.handover_data?.maintenance_contract ? 'Ada' : 'Tidak'"></strong></span>
                                    </div>
                                </div>
                            </div>

                            {{-- Kategori 3: Finansial --}}
                            <div class="p-3 rounded-xl border border-slate-200 bg-slate-50/50 space-y-1.5">
                                <div class="font-bold text-slate-800 flex items-center justify-between">
                                    <span>III. Administrasi &amp; Finansial</span>
                                    <span class="text-[10px] text-slate-400">DP &amp; Billing</span>
                                </div>
                                <div class="text-slate-600 space-y-1 text-[11.5px]">
                                    <div class="flex items-center gap-1.5">
                                        <span :class="activeProject?.handover_data?.dp_payment_proof ? 'text-emerald-600 font-bold' : 'text-slate-400'">&bull;</span>
                                        <span>Bukti Pembayaran DP: <strong x-text="activeProject?.handover_data?.dp_payment_proof ? 'Ada' : 'Tidak'"></strong></span>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <span :class="activeProject?.handover_data?.billing_milestone ? 'text-emerald-600 font-bold' : 'text-slate-400'">&bull;</span>
                                        <span>Jadwal Billing Milestone: <strong x-text="activeProject?.handover_data?.billing_milestone ? 'Ada' : 'Tidak'"></strong></span>
                                    </div>
                                </div>
                            </div>

                            {{-- Kategori 4: Kontak Customer PIC --}}
                            <div class="p-3 rounded-xl border border-slate-200 bg-slate-50/50 space-y-1.5">
                                <div class="font-bold text-slate-800 flex items-center justify-between">
                                    <span>IV. Kontak &amp; Akses Customer</span>
                                    <span class="text-[10px] text-slate-400">PIC &amp; Meeting Notes</span>
                                </div>
                                <div class="text-slate-600 space-y-1 text-[11.5px]">
                                    <div class="flex items-center gap-1.5">
                                        <span :class="activeProject?.handover_data?.customer_contacts ? 'text-emerald-600 font-bold' : 'text-slate-400'">&bull;</span>
                                        <span>Daftar Kontak PIC: <strong x-text="activeProject?.handover_data?.customer_contacts ? 'Ada' : 'Tidak'"></strong></span>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <span :class="activeProject?.handover_data?.meeting_notes ? 'text-emerald-600 font-bold' : 'text-slate-400'">&bull;</span>
                                        <span>Catatan Meeting Notes: <strong x-text="activeProject?.handover_data?.meeting_notes ? 'Ada' : 'Tidak'"></strong></span>
                                    </div>
                                    <div class="pt-1 text-[11px] text-slate-500 border-t border-slate-200">
                                        <span>PIC Teknis: <strong x-text="activeProject?.customer_pic_technical || '-'"></strong></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Catatan Khusus Sales --}}
                    <div class="p-3.5 rounded-xl border border-slate-200 bg-slate-50/60">
                        <div class="text-[11.5px] font-bold text-slate-700 mb-1">Catatan Khusus Sales ke Tim Delivery (Komitmen Lisan / Batasan Akses):</div>
                        <p class="text-xs text-slate-600 italic" x-text="activeProject?.special_notes || 'Tidak ada catatan khusus.'"></p>
                    </div>

                    {{-- Form Penugasan PM & Divisi untuk Transisi Lapangan --}}
                    <div class="p-4 rounded-xl border border-blue-200 bg-blue-50/40 space-y-3">
                        <div class="text-xs font-bold text-blue-900 flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            <span>Penetapan PIC Project Manager &amp; Divisi Pelaksana</span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1">Tugaskan Project Manager (PM) <span class="text-red-500">*</span></label>
                                <select x-model="handoverAssign.pm_id" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 outline-none focus:border-[#8F0A0D]">
                                    <option value="">-- Pilih Project Manager --</option>
                                    @foreach($pmList ?? [] as $pm)
                                        <option value="{{ $pm->id }}">{{ $pm->name }} ({{ $pm->email }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1">Divisi Pelaksana Proyek <span class="text-red-500">*</span></label>
                                <select x-model="handoverAssign.division_id" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 outline-none focus:border-[#8F0A0D]">
                                    <option value="">-- Pilih Divisi --</option>
                                    @foreach($divisions ?? [] as $div)
                                        <option value="{{ $div->id }}">{{ $div->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Form Input Catatan Bersyarat (Jika Memilih Conditional) --}}
                    <div x-show="showConditionalInput" class="p-3.5 rounded-xl border border-amber-300 bg-amber-50 space-y-2">
                        <label class="block text-xs font-bold text-amber-900">
                            Catatan Kekurangan Berkas / Lingkup untuk Tim Sales (Batas Waktu Revisi 2x24 Jam):
                        </label>
                        <textarea x-model="conditionalNotes" rows="2" placeholder="Tuliskan dokumen yang belum lengkap atau klausul yang perlu klarifikasi..." class="w-full px-3 py-2 bg-white border border-amber-300 rounded-xl text-xs outline-none focus:border-amber-600"></textarea>
                    </div>

                </div>

                {{-- Modal Footer Actions --}}
                <div class="p-4 sm:px-6 border-t border-slate-200 bg-slate-50 flex flex-wrap items-center justify-between gap-2">
                    <button type="button" @click="handoverReviewModalOpen = false" class="px-4 py-2 bg-white border border-slate-300 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition cursor-pointer">
                        Tutup
                    </button>

                    <div class="flex items-center gap-2">
                        {{-- Tombol Bersyarat (Conditional) --}}
                        <button type="button" 
                                @click="handleConditionalClick()" 
                                class="px-3.5 py-2 bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-300 rounded-xl text-xs font-bold transition cursor-pointer">
                            <span x-text="showConditionalInput ? 'Kirim Catatan Revisi (2x24 Jam)' : 'Tandai Bersyarat (Conditional)'"></span>
                        </button>

                        {{-- Tombol Sahkan (Approved) --}}
                        <button type="button" 
                                @click="approveHandover()" 
                                class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-xs transition cursor-pointer flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            <span>Sahkan Serah Terima (Handover Approved)</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>

    {{-- MODAL FORMULIR CHECK LIST SERAH TERIMA (INTERNAL HANDOVER - 4 KATEGORI / 11 ITEM) --}}
    <template x-teleport="body">
        <div x-show="docsModalOpen" 
             x-cloak
             class="fixed inset-0 bg-slate-900/60 backdrop-blur-2xs z-50 flex items-center justify-center p-3 sm:p-5 overflow-y-auto"
             @click.self="docsModalOpen = false">
            <div class="bg-white rounded-2xl w-[860px] max-w-full max-h-[92vh] flex flex-col shadow-2xl border border-slate-200 overflow-hidden my-6">
                
                {{-- Modal Header --}}
                <div class="p-5 sm:p-6 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <img src="{{ asset('images/ipnet1.png') }}" alt="IP Network Solusindo" class="h-10 w-auto object-contain shrink-0" onerror="this.src='/images/ipnet.png'">
                        <div>
                            <h3 class="text-base sm:text-[17px] font-extrabold text-slate-900 uppercase tracking-tight">FORMULIR CHECK LIST SERAH TERIMA (INTERNAL HANDOVER)</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Dokumen kepatuhan resmi Serah Terima Pekerjaan Proyek</p>
                        </div>
                    </div>
                    <button type="button" @click="docsModalOpen = false" class="w-8 h-8 rounded-xl bg-white border border-slate-200 text-slate-400 hover:text-slate-600 flex items-center justify-center cursor-pointer shadow-2xs text-lg font-bold">
                        &times;
                    </button>
                </div>

                {{-- Modal Body --}}
                <div class="p-5 sm:p-6 overflow-y-auto flex-1 space-y-4">
                    
                    {{-- INFORMASI PROJECT (PROJECT INFORMATION) --}}
                    <div class="border border-slate-300 rounded-xl overflow-hidden shadow-2xs">
                        <div class="bg-slate-900 text-white font-extrabold text-xs uppercase px-4 py-2.5 tracking-wider">
                            INFORMASI PROJECT (PROJECT INFORMATION)
                        </div>
                        <div class="divide-y divide-slate-200 text-xs bg-white">
                            <div class="grid grid-cols-1 sm:grid-cols-3 divide-y sm:divide-y-0 sm:divide-x divide-slate-200">
                                <div class="p-2.5 bg-slate-50 font-semibold text-slate-600 sm:col-span-1">Nama Project</div>
                                <div class="p-2.5 font-bold text-slate-900 sm:col-span-2" x-text="activeProject?.name || '-'"></div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-3 divide-y sm:divide-y-0 sm:divide-x divide-slate-200">
                                <div class="p-2.5 bg-slate-50 font-semibold text-slate-600 sm:col-span-1">Kode Project (SO)</div>
                                <div class="p-2.5 font-mono font-bold text-slate-900 sm:col-span-2" x-text="activeProject?.so_code || '-'"></div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-3 divide-y sm:divide-y-0 sm:divide-x divide-slate-200">
                                <div class="p-2.5 bg-slate-50 font-semibold text-slate-600 sm:col-span-1">Tanggal Handover</div>
                                <div class="p-2.5 text-slate-800 sm:col-span-2" x-text="activeProject?.handover_submitted_at || '-'"></div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-3 divide-y sm:divide-y-0 sm:divide-x divide-slate-200">
                                <div class="p-2.5 bg-slate-50 font-semibold text-slate-600 sm:col-span-1">Pihak Penyerah (Commercial)</div>
                                <div class="p-2.5 font-semibold text-slate-900 sm:col-span-2" x-text="activeProject?.sales_name || '-'"></div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-3 divide-y sm:divide-y-0 sm:divide-x divide-slate-200">
                                <div class="p-2.5 bg-slate-50 font-semibold text-slate-600 sm:col-span-1">Pihak Penerima (Delivery)</div>
                                <div class="p-2.5 font-semibold text-blue-700 sm:col-span-2" x-text="(activeProject?.pm || 'Tim Delivery / PMO') + ' (' + (activeProject?.division || 'Belum Didelegasikan') + ')'"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Tabel 4 Kategori & 11 Items --}}
                    <div class="border border-slate-300 rounded-xl overflow-hidden shadow-2xs">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="bg-slate-900 text-white text-[11px] font-bold uppercase tracking-wider">
                                    <th class="py-2.5 px-3 w-10 text-center border-r border-slate-700">No</th>
                                    <th class="py-2.5 px-4 w-44 border-r border-slate-700">Kategori Dokumen / Item</th>
                                    <th class="py-2.5 px-4 border-r border-slate-700">Nama Dokumen / Item</th>
                                    <th class="py-2.5 px-3 w-28 text-center border-r border-slate-700">Ada (Ada/Tidak)</th>
                                    <th class="py-2.5 px-4 w-48">Status / Keterangan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 bg-white text-slate-800">
                                @foreach($handoverFormStructure as $category)
                                    @foreach($category['items'] as $itemIndex => $item)
                                        <tr class="hover:bg-slate-50/80 transition" :class="activeDocs['{{ $item['key'] }}'] ? 'bg-emerald-50/30' : ''">
                                            @if($itemIndex === 0)
                                                <td rowspan="{{ count($category['items']) }}" class="py-3 px-3 font-extrabold text-slate-900 text-center align-top border-r border-slate-200 bg-slate-50/60">
                                                    {{ $category['number'] }}
                                                </td>
                                                <td rowspan="{{ count($category['items']) }}" class="py-3 px-4 font-bold text-slate-900 align-top border-r border-slate-200 bg-slate-50/60">
                                                    {{ $category['category_name'] }}
                                                </td>
                                            @endif
                                            
                                             <td class="py-2.5 px-4 font-medium text-slate-800 border-r border-slate-200">
                                                {{ $item['name'] }}
                                            </td>
                                            
                                            <td class="py-2.5 px-3 text-center border-r border-slate-200 whitespace-nowrap">
                                                <label class="inline-flex items-center gap-1.5 cursor-pointer select-none">
                                                    <input type="checkbox" 
                                                           name="{{ $item['key'] }}"
                                                           x-model="activeDocs['{{ $item['key'] }}']"
                                                           class="w-4 h-4 text-[#8F0A0D] rounded border-slate-300 focus:ring-red-500">
                                                    <span class="text-[11px] font-bold" :class="activeDocs['{{ $item['key'] }}'] ? 'text-emerald-700' : 'text-slate-400'" x-text="activeDocs['{{ $item['key'] }}'] ? 'Ada' : 'Tidak'"></span>
                                                </label>
                                            </td>

                                            <td class="py-2.5 px-4 text-slate-500 text-[11px]">
                                                <span class="px-2 py-0.5 rounded bg-slate-100 border border-slate-200 text-slate-700 font-medium">
                                                    {{ $item['default_note'] }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>

                {{-- Modal Footer --}}
                <div class="p-4 sm:px-6 border-t border-slate-200 bg-slate-50 flex items-center justify-between gap-2 flex-wrap">
                    <div class="text-xs text-slate-500">
                        Total Terpenuhi: <strong class="text-emerald-600 font-bold" x-text="Object.values(activeDocs).filter(v => v === true).length + '/11 Dokumen'"></strong>
                    </div>
                    <div class="flex items-center gap-2">
                        <a :href="'/projects/' + (activeProject?.id || '')" 
                           class="px-3.5 py-2 bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 rounded-xl text-xs font-bold transition shadow-2xs cursor-pointer flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            <span>Repositori Proyek</span>
                        </a>
                        <button type="button" @click="docsModalOpen = false" class="px-4 py-2 bg-white border border-slate-300 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-50 transition cursor-pointer">Tutup</button>
                        <button type="button" @click="saveDocuments()" class="px-4 py-2 bg-[#8F0A0D] hover:bg-[#73080A] text-white rounded-xl text-xs font-bold shadow-xs transition cursor-pointer flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            <span>Simpan Formulir Checklist</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>

    {{-- MODAL GATE 4: SERAH TERIMA PASCA-IMPLEMENTASI (PMO -> MANAGED SERVICE) --}}
    <template x-teleport="body">
        <div x-show="handoverToMsModalOpen" 
             x-cloak
             class="fixed inset-0 bg-slate-900/60 backdrop-blur-2xs z-50 flex items-center justify-center p-3 sm:p-5 overflow-y-auto"
             @click.self="handoverToMsModalOpen = false">
            <div class="bg-white rounded-2xl w-[720px] max-w-full max-h-[92vh] flex flex-col shadow-2xl overflow-hidden my-6 border border-slate-200">
                
                {{-- Modal Header --}}
                <div class="p-5 sm:p-6 bg-slate-900 text-white flex items-center justify-between">
                    <div>
                        <div class="flex items-center gap-2 text-red-400 text-xs font-bold uppercase tracking-wider mb-1">
                            <span class="w-2 h-2 rounded-full bg-[#8F0A0D]"></span>
                            <span>Gate 4: Post-Implementation Service Handover</span>
                        </div>
                        <h3 class="text-base sm:text-lg font-bold text-white" x-text="'Serah Terima ke Managed Service: ' + (activeProject?.name || '')"></h3>
                    </div>
                    <button type="button" @click="handoverToMsModalOpen = false" class="w-8 h-8 rounded-xl bg-white/10 hover:bg-white/20 text-white flex items-center justify-center cursor-pointer text-lg font-bold">
                        &times;
                    </button>
                </div>

                {{-- Modal Body --}}
                <div class="p-5 sm:p-6 overflow-y-auto flex-1 space-y-4 text-xs">
                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-900">
                        <div class="font-bold mb-1">Transisi Tahap Deliver &rarr; Operate:</div>
                        <p class="text-slate-600 leading-relaxed">
                            Proyek telah selesai tahap implementasi fisik. Formulir ini akan melimpahkan tanggung jawab pemeliharaan berkala, monitoring, dan SLA kepada <strong>Tim Managed Service</strong>.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div>
                            <label class="block font-bold text-slate-700 uppercase tracking-wider text-[11px] mb-1">SLA Tier Kontrak <span class="text-red-500">*</span></label>
                            <select x-model="msHandoverForm.sla_tier" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl font-bold text-slate-800 outline-none focus:border-[#8F0A0D]">
                                <option value="Platinum">Platinum (24x7 MTTR 2 Jam, Uptime 99.9%)</option>
                                <option value="Gold">Gold (8x5 MTTR 4 Jam, Uptime 99.5%)</option>
                                <option value="Silver">Silver (8x5 Next Business Day, Uptime 99.0%)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 uppercase tracking-wider text-[11px] mb-1">Jam Layanan Dukungan <span class="text-red-500">*</span></label>
                            <select x-model="msHandoverForm.sla_coverage_hours" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl font-bold text-slate-800 outline-none focus:border-[#8F0A0D]">
                                <option value="24x7">24 Jam x 7 Hari (Non-Stop Real-Time)</option>
                                <option value="8x5">8 Jam x 5 Hari (Hari Kerja Normal)</option>
                                <option value="12x7">12 Jam x 7 Hari</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div>
                            <label class="block font-bold text-slate-700 uppercase tracking-wider text-[11px] mb-1">Frekuensi Preventive Maintenance</label>
                            <select x-model="msHandoverForm.maintenance_frequency" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl font-bold text-slate-800 outline-none focus:border-[#8F0A0D]">
                                <option value="Monthly">Bulanan (Monthly PM)</option>
                                <option value="Quarterly">Triwulan (Quarterly PM)</option>
                                <option value="Bi-Annual">Semesteran (Bi-Annual PM)</option>
                                <option value="Annual">Tahunan (Annual PM)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 uppercase tracking-wider text-[11px] mb-1">Mulai Layanan Operasional</label>
                            <input type="date" x-model="msHandoverForm.service_start_date" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl font-semibold text-slate-800 outline-none focus:border-[#8F0A0D]">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div>
                            <label class="block font-bold text-slate-700 uppercase tracking-wider text-[11px] mb-1">Akhir Kontrak Layanan</label>
                            <input type="date" x-model="msHandoverForm.service_end_date" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl font-semibold text-slate-800 outline-none focus:border-[#8F0A0D]">
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 uppercase tracking-wider text-[11px] mb-1">Catatan Serah Terima Hasil Implementasi ke Tim Operasional</label>
                        <textarea x-model="msHandoverForm.special_notes" rows="2.5" placeholder="Contoh: Seluruh konfigurasi telah selesai, password & IP schema terlampir di dokumentasi final..." class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-slate-800 outline-none focus:border-[#8F0A0D]"></textarea>
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="p-4 sm:px-6 border-t border-slate-200 bg-slate-50 flex items-center justify-end gap-2">
                    <button type="button" @click="handoverToMsModalOpen = false" class="px-4 py-2 bg-white border border-slate-300 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition cursor-pointer">
                        Batal
                    </button>
                    <button type="button" @click="submitHandoverToMs()" class="px-5 py-2 bg-[#8F0A0D] hover:bg-[#73080A] text-white rounded-xl text-xs font-bold shadow-md transition cursor-pointer flex items-center gap-1.5">
                        <span>Serahkan ke Tim Managed Service</span>
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('pmoDashboard', () => ({
            projects: @json($formattedProjects ?? []),
            handoverFormStructure: @json($handoverFormStructure ?? []),
            stageCounts: @json($stageCounts ?? []),
            onTrackCount: {{ $onTrackCount ?? 0 }},
            delayedCount: {{ $delayedCount ?? 0 }},
            atRiskCount: {{ $atRiskCount ?? 0 }},
            ho2Count: {{ $ho2Count ?? 0 }},
            ho3Count: {{ $ho3Count ?? 0 }},
            handoverPendingCount: {{ $handoverPendingCount ?? 0 }},
            handoverConditionalCount: {{ $handoverConditionalCount ?? 0 }},
            readyToOperateCount: {{ $readyToOperateCount ?? 0 }},

            search: '',
            selectedDivision: 'all',
            selectedPm: 'all',
            selectedStage: 'all',
            handoverFilter: 'all',
            activeTab: 'all', // 'all', 'deliver', 'pending_handover', 'ready_ms'
            viewMode: 'table', // 'table' | 'cards'
            perPage: 10,
            currentPage: 1,

            docsModalOpen: false,
            handoverReviewModalOpen: false,
            handoverToMsModalOpen: false,
            showConditionalInput: false,
            conditionalNotes: '',
            activeProject: null,
            activeDocs: {},
            handoverAssign: {
                pm_id: '',
                division_id: '',
            },
            msHandoverForm: {
                sla_tier: 'Gold',
                sla_coverage_hours: '24x7',
                maintenance_frequency: 'Monthly',
                service_start_date: '{{ date('Y-m-d') }}',
                service_end_date: '',
                special_notes: '',
            },

            openHandoverToMsModal(project) {
                this.activeProject = project;
                this.msHandoverForm.sla_tier = project.sla_tier || 'Gold';
                this.msHandoverForm.sla_coverage_hours = '24x7';
                this.msHandoverForm.maintenance_frequency = 'Monthly';
                this.msHandoverForm.service_start_date = '{{ date('Y-m-d') }}';
                this.msHandoverForm.service_end_date = '';
                this.msHandoverForm.special_notes = project.special_notes || '';
                this.handoverToMsModalOpen = true;
            },

            async submitHandoverToMs() {
                try {
                    const response = await fetch(`/pmo/projects/${this.activeProject.id}/handover-to-ms`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify(this.msHandoverForm)
                    });

                    const data = await response.json();
                    if (response.ok && data.success) {
                        this.handoverToMsModalOpen = false;
                        alert(data.message || 'Proyek berhasil diserahkan ke Managed Service!');
                        window.location.reload();
                    } else {
                        alert(data.message || 'Gagal menyerahkan ke Managed Service.');
                    }
                } catch (e) {
                    console.error(e);
                    alert('Terjadi kesalahan jaringan.');
                }
            },

            get filteredProjects() {
                return this.projects.filter(p => {
                    const s = (this.search || '').toLowerCase();
                    const matchSearch = !s ||
                                        (p.name || '').toLowerCase().includes(s) ||
                                        (p.so_code || '').toLowerCase().includes(s) ||
                                        (p.client || '').toLowerCase().includes(s) ||
                                        (p.sales_name || '').toLowerCase().includes(s) ||
                                        (p.pm || '').toLowerCase().includes(s);
                    const matchDiv = this.selectedDivision === 'all' || String(p.division_id) === String(this.selectedDivision);
                    const matchPm  = this.selectedPm === 'all' || String(p.pm_id) === String(this.selectedPm);
                    const matchStg = this.selectedStage === 'all' || p.stage === this.selectedStage;
                    const matchHandover = this.handoverFilter === 'all' || (this.handoverFilter === 'pending' && (p.handover_status === 'Submitted' || p.handover_status === 'Conditional'));

                    let matchTab = true;
                    if (this.activeTab === 'deliver') {
                        matchTab = (p.stage === 'Deliver');
                    } else if (this.activeTab === 'pending_handover') {
                        matchTab = (p.handover_status === 'Submitted' || p.handover_status === 'Conditional');
                    } else if (this.activeTab === 'ready_ms') {
                        matchTab = (p.stage === 'Deliver' && (p.progress >= 100 || p.docs_completed_count >= 11));
                    }

                    return matchSearch && matchDiv && matchPm && matchStg && matchHandover && matchTab;
                });
            },

            get paginatedProjects() {
                if (this.perPage === 'all') return this.filteredProjects;
                const limit = parseInt(this.perPage, 10) || 10;
                const start = (this.currentPage - 1) * limit;
                return this.filteredProjects.slice(start, start + limit);
            },

            get totalPages() {
                if (this.perPage === 'all') return 1;
                const limit = parseInt(this.perPage, 10) || 10;
                return Math.max(1, Math.ceil(this.filteredProjects.length / limit));
            },

            goToPage(p) { this.currentPage = p; },
            prevPage() { if (this.currentPage > 1) this.currentPage--; },
            nextPage() { if (this.currentPage < this.totalPages) this.currentPage++; },

            openDocumentsModal(project) {
                this.activeProject = project;
                this.activeDocs = Object.assign({}, project.documents_checklist || {});
                this.docsModalOpen = true;
            },

            openHandoverReviewModal(project) {
                this.activeProject = project;
                this.handoverAssign.pm_id = project.pm_id || '';
                this.handoverAssign.division_id = project.division_id || '';
                this.showConditionalInput = false;
                this.conditionalNotes = project.handover_conditional_notes || '';
                this.handoverReviewModalOpen = true;
            },

            async approveHandover() {
                if (!this.handoverAssign.pm_id || !this.handoverAssign.division_id) {
                    alert('Harap tentukan Project Manager (PM) dan Divisi Pelaksana terlebih dahulu.');
                    return;
                }

                try {
                    const response = await fetch(`/pmo/projects/${this.activeProject.id}/handover-approve`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify(this.handoverAssign)
                    });

                    const data = await response.json();
                    if (response.ok && data.success) {
                        this.handoverReviewModalOpen = false;
                        window.location.reload();
                    } else {
                        alert(data.message || 'Gagal mengesahkan handover.');
                    }
                } catch (e) {
                    console.error(e);
                    alert('Terjadi kesalahan jaringan.');
                }
            },

            async handleConditionalClick() {
                if (!this.showConditionalInput) {
                    this.showConditionalInput = true;
                    return;
                }

                if (!this.conditionalNotes.trim()) {
                    alert('Harap isi catatan revisi / kekurangan berkas.');
                    return;
                }

                try {
                    const response = await fetch(`/pmo/projects/${this.activeProject.id}/handover-conditional`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ notes: this.conditionalNotes })
                    });

                    const data = await response.json();
                    if (response.ok && data.success) {
                        this.handoverReviewModalOpen = false;
                        window.location.reload();
                    } else {
                        alert(data.message || 'Gagal menyimpan status bersyarat.');
                    }
                } catch (e) {
                    console.error(e);
                    alert('Terjadi kesalahan jaringan.');
                }
            },

            async saveDocuments() {
                try {
                    const response = await fetch(`/pmo/projects/${this.activeProject.id}/update-documents`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ checklist: this.activeDocs })
                    });

                    const data = await response.json();
                    if (response.ok && data.success) {
                        this.docsModalOpen = false;
                        window.location.reload();
                    } else {
                        alert(data.message || 'Gagal menyimpan checklist dokumen.');
                    }
                } catch (e) {
                    console.error(e);
                    alert('Terjadi kesalahan jaringan.');
                }
            }
        }));
    });
</script>
@endpush
@endsection
