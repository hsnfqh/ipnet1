@extends('layouts.app')

@section('title', 'Projects - PMO Project Delivery & Serah Terima')

@section('content')
<div class="flex h-screen overflow-hidden" x-data="pmoDashboard()" x-cloak>
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto bg-[#F8FAFC]">
        @include('components.topbar', ['title' => 'Projects'])
        
        <div class="p-4 sm:p-6 lg:p-8 space-y-6 max-w-[1680px] mx-auto">
            
            <!-- ========================================================== -->
            <!-- 1. HEADER CARD (WITH SEGMENTED TAB SWITCHER)               -->
            <!-- ========================================================== -->
            <div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200/90 shadow-2xs">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 pb-4 mb-4 border-b border-[#E2E8F0]">
                    <div>
                        <p class="text-[#8F0A0D] text-[12px] font-bold inline-flex items-center uppercase tracking-wider">
                            <span class="w-2 h-2 rounded-full bg-[#8F0A0D] inline-block mr-2"></span> MANAJEMEN PORTOFOLIO &amp; PROJECT DELIVERY
                        </p>
                        <h2 class="text-[20px] font-bold text-[#1E293B] tracking-tight">Daftar Proyek &amp; Project Delivery</h2>
                        <p class="text-[13px] text-[#64748B] mt-0.5">Kelola pelaksanaan proyek aktif, verifikasi serah terima (handover) dari Sales, dan kontrol implementasi lapangan</p>
                    </div>

                    {{-- Tab Switcher: Segmented Toggle Styled Like Managed Service (Gambar 2 / Reference) --}}
                    <div class="inline-flex p-1 bg-[#F1F5F9] rounded-xl border border-[#E2E8F0] shadow-2xs shrink-0 self-start lg:self-center">
                        {{-- Tab 1: Projek Aktif (Deliver & Selesai) --}}
                        <button type="button" 
                                @click="activeTab = 'all'; currentPage = 1;" 
                                :class="(activeTab !== 'pending_handover') 
                                    ? 'bg-[#8F0A0D] text-white shadow-xs font-bold' 
                                    : 'text-[#64748B] hover:text-[#1E293B] hover:bg-white/60 font-semibold'"
                                class="px-4 py-2 rounded-lg text-xs transition-all flex items-center gap-2 cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                            <span>Projek Aktif</span>
                            <span :class="(activeTab !== 'pending_handover') ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700'" 
                                  class="px-2 py-0.5 rounded-full text-[10.5px] font-bold"
                                  x-text="(stageCounts.Deliver || 0) + (stageCounts.Operate || 0)">
                            </span>
                        </button>

                        {{-- Tab 2: Serah Terima Masuk (Incoming Handover) --}}
                        <button type="button" 
                                @click="activeTab = 'pending_handover'; currentPage = 1;" 
                                :class="(activeTab === 'pending_handover') 
                                    ? 'bg-[#8F0A0D] text-white shadow-xs font-bold' 
                                    : 'text-[#64748B] hover:text-[#1E293B] hover:bg-white/60 font-semibold'"
                                class="px-4 py-2 rounded-lg text-xs transition-all flex items-center gap-2 cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            <span>Incoming Handover</span>
                            <span :class="(activeTab === 'pending_handover') ? 'bg-white/20 text-white' : 'bg-amber-100 text-amber-800 font-bold'" 
                                  class="px-2 py-0.5 rounded-full text-[10.5px] font-bold"
                                  x-text="handoverPendingCount + handoverConditionalCount">
                            </span>
                        </button>
                    </div>
                </div>

                <!-- Filter Controls -->
                <div class="flex flex-col sm:flex-row flex-wrap items-center gap-3">
                    <div class="relative flex-1 min-w-[240px] w-full sm:w-auto">
                        <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-[#94A3B8]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" 
                               x-model="search"
                               @input="currentPage = 1"
                               placeholder="Cari nama project, nomor SO, client, sales, atau PIC..." 
                               class="w-full pl-9 pr-3.5 py-2.5 rounded-xl border border-[#CBD5E1] text-[12.5px] font-semibold text-[#1E293B] bg-white outline-none hover:border-[#94A3B8] focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition-all shadow-xs placeholder-[#94A3B8]">
                    </div>
                    
                    <select x-model="statusFilter" @change="currentPage = 1" 
                            class="w-full sm:w-56 px-3 py-2.5 rounded-xl border border-[#CBD5E1] text-[12.5px] font-semibold text-[#1E293B] bg-white outline-none hover:border-[#94A3B8] focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition-all shadow-xs cursor-pointer">
                        <option value="Semua">Semua Status Proyek</option>
                        <option value="Deliver">Tahap Deliver (Aktif Lapangan)</option>
                        <option value="ReadyMS">Siap Transisi ke MS (100%)</option>
                        <option value="Operate">Operasional / Transisi Selesai</option>
                        <option value="Pending">Menunggu Review PMO</option>
                    </select>

                    <select x-model="selectedDivision" @change="currentPage = 1" 
                            class="w-full sm:w-48 px-3 py-2.5 rounded-xl border border-[#CBD5E1] text-[12.5px] font-semibold text-[#1E293B] bg-white outline-none hover:border-[#94A3B8] focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition-all shadow-xs cursor-pointer">
                        <option value="all">Semua Tim Teknis</option>
                        @foreach($divisions ?? [] as $div)
                            <option value="{{ $div->id }}">{{ $div->name }}</option>
                        @endforeach
                    </select>

                    <select x-model="selectedPm" @change="currentPage = 1" 
                            class="w-full sm:w-48 px-3 py-2.5 rounded-xl border border-[#CBD5E1] text-[12.5px] font-semibold text-[#1E293B] bg-white outline-none hover:border-[#94A3B8] focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition-all shadow-xs cursor-pointer">
                        <option value="all">Semua Project Manager</option>
                        @foreach($pmList ?? [] as $pm)
                            <option value="{{ $pm->id }}">{{ $pm->name }}</option>
                        @endforeach
                    </select>

                    {{-- Reset Button --}}
                    <button type="button"
                            x-show="search || statusFilter !== 'Semua' || selectedDivision !== 'all' || selectedPm !== 'all'"
                            @click="search = ''; statusFilter = 'Semua'; selectedDivision = 'all'; selectedPm = 'all'; currentPage = 1;"
                            class="px-3.5 py-2.5 text-[12px] font-bold text-[#64748B] hover:text-[#8F0A0D] bg-[#F8FAFC] hover:bg-[#FEF2F2] border border-[#E2E8F0] hover:border-[#FCA5A5] rounded-xl transition cursor-pointer">
                        Reset Filter
                    </button>
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
                            @click="activeTab = (activeTab === 'pending_handover' ? 'deliver' : 'pending_handover'); currentPage = 1;"
                            class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold rounded-xl transition cursor-pointer shadow-xs whitespace-nowrap self-start sm:self-center flex items-center gap-2">
                        <span x-text="activeTab === 'pending_handover' ? 'Beralih ke Projek Aktif' : 'Tinjau Antrean Serah Terima'"></span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
            </template>

            <!-- ========================================================== -->
            <!-- 4. DAFTAR PORTOFOLIO PROYEK & KONTROL IMPLEMENTASI        -->
            <!-- ========================================================== -->
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden">
                
                {{-- A. TABLE HEADER & VIEW TOGGLE --}}
                <div class="p-3.5 sm:px-6 py-3 border-b border-slate-100 flex items-center justify-between gap-3 bg-slate-50/50">
                    <div class="flex items-center gap-2.5">
                        <span class="w-2 h-2 rounded-full bg-[#8F0A0D]"></span>
                        <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wide" 
                            x-text="activeTab === 'pending_handover' ? 'Daftar Berkas Serah Terima Masuk (Sales)' : 'Daftar Portofolio Proyek Implementasi'"></h3>
                        <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-white text-slate-600 border border-slate-200" 
                              x-text="filteredProjects.length + ' Proyek'"></span>
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

                {{-- B. Table Info Bar: Counter & Pagination Context --}}
                <div class="px-5 py-3 border-b border-slate-100 flex flex-wrap items-center justify-between gap-3 bg-white">
                    <div class="flex items-center gap-2 text-xs text-slate-500">
                        <span>Menampilkan:</span>
                        <span class="px-2.5 py-0.5 rounded-md bg-red-50 text-[#8F0A0D] font-extrabold" x-text="filteredProjects.length + ' Proyek'"></span>
                        <span class="text-slate-400">&bull;</span>
                        <span class="text-slate-500" x-text="'Halaman ' + currentPage + ' dari ' + (totalPages || 1)"></span>
                    </div>

                    <div class="flex items-center gap-2 text-xs text-slate-500">
                        <span class="hidden sm:inline text-slate-400">Tampilkan per halaman:</span>
                        <select x-model="perPage" @change="currentPage = 1" class="px-2.5 py-1 bg-slate-50 border border-slate-200 rounded-lg text-xs font-semibold text-slate-700 outline-none hover:border-slate-300">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="all">Semua</option>
                        </select>
                    </div>
                </div>

                {{-- C1. TAMPILAN TABEL MODERN & LEGA (Fits 100% with No Ugly Wrapping) --}}
                <div x-show="viewMode === 'table'" class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="border-b border-slate-200/80 bg-slate-50/70 text-[10.5px] font-bold text-slate-400 uppercase tracking-wider">
                                <th class="py-3.5 px-4 sm:px-6 w-[34%]">PROYEK &amp; KLIEN</th>
                                <th class="py-3.5 px-4 w-[18%]">TIM TEKNIS</th>
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
                                            <a :href="'/pmo/projects/' + project.id" class="font-extrabold text-slate-900 text-sm hover:text-[#8F0A0D] transition block leading-snug" x-text="project.name"></a>
                                            
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

                                    {{-- Kolom 2: Tim Teknis (Ditentukan dari Sales / Divisi Pelaksana) --}}
                                    <td class="py-4 px-4 align-top whitespace-nowrap">
                                        <div class="space-y-1">
                                            <div>
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-bold border"
                                                      :class="{
                                                          'bg-indigo-50 text-indigo-700 border-indigo-200': (project.division || '').includes('&') || ((project.division || '').includes('Network') && (project.division || '').includes('Security')),
                                                          'bg-blue-50 text-blue-700 border-blue-200': (project.division || '').includes('Network') && !(project.division || '').includes('Security'),
                                                          'bg-purple-50 text-purple-700 border-purple-200': (project.division || '').includes('Security') && !(project.division || '').includes('Network'),
                                                          'bg-amber-50 text-amber-800 border-amber-200': !(project.division) || (project.division || '').includes('Belum')
                                                      }">
                                                    {{-- Icon Tim Teknis --}}
                                                    <template x-if="(project.division || '').includes('&') || ((project.division || '').includes('Network') && (project.division || '').includes('Security'))">
                                                        <svg class="w-3.5 h-3.5 text-indigo-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                                    </template>
                                                    <template x-if="(project.division || '').includes('Network') && !(project.division || '').includes('Security')">
                                                        <svg class="w-3.5 h-3.5 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                                                    </template>
                                                    <template x-if="(project.division || '').includes('Security') && !(project.division || '').includes('Network')">
                                                        <svg class="w-3.5 h-3.5 text-purple-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                                    </template>
                                                    <template x-if="!(project.division) || (project.division || '').includes('Belum')">
                                                        <svg class="w-3.5 h-3.5 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                                    </template>
                                                    <span x-text="project.division || 'Belum Ditentukan Sales'"></span>
                                                </span>
                                            </div>
                                            <template x-if="project.pm && project.pm !== 'Belum Ditentukan'">
                                                <div class="text-[11px] text-slate-500 font-medium flex items-center gap-1">
                                                    <span>PM:</span>
                                                    <span class="font-bold text-slate-700" x-text="project.pm"></span>
                                                </div>
                                            </template>
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
                                                    <span>Verifikasi Dokumen</span>
                                                </button>
                                            </template>

                                            <template x-if="project.stage === 'Deliver' && project.progress >= 100">
                                                <button type="button"
                                                        @click="openHandoverToMsModal(project)"
                                                        class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition shadow-xs cursor-pointer">
                                                    <span>Handover MS</span>
                                                </button>
                                            </template>

                                            <a :href="'/pmo/projects/' + project.id" 
                                               title="Buka Dashboard PMO & Perhitungan Proyek"
                                               class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-[#8F0A0D] hover:bg-[#73080A] text-white rounded-xl text-xs font-bold transition shadow-2xs">
                                                <span>Detail</span>
                                                <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
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
                                                <svg class="w-3 h-3 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                                </svg>
                                                <span>Handover Approved</span>
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
                                        <a :href="'/pmo/projects/' + project.id" class="font-extrabold text-slate-900 text-sm hover:text-[#8F0A0D] transition block line-clamp-2" x-text="project.name"></a>
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

                                    {{-- Tim Teknis & PM Info --}}
                                    <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200/80 flex items-center justify-between text-xs">
                                        <div>
                                            <span class="text-[10px] text-slate-400 block font-bold uppercase">Tim Teknis:</span>
                                            <span class="font-bold text-slate-800 text-[11px]" x-text="project.division || 'Belum Ditentukan Sales'"></span>
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
                                <div class="mt-4 pt-3.5 border-t border-slate-100 flex items-center justify-end gap-2">
                                    <div class="flex items-center gap-1.5">
                                        <template x-if="project.handover_status === 'Submitted'">
                                            <button type="button"
                                                    @click="openHandoverReviewModal(project)"
                                                    class="px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-xs font-bold transition shadow-xs cursor-pointer">
                                                Verifikasi Dokumen
                                            </button>
                                        </template>

                                        <a :href="'/pmo/projects/' + project.id" 
                                           title="Buka Dashboard PMO & Perhitungan Proyek"
                                           class="px-3.5 py-1.5 bg-[#8F0A0D] hover:bg-[#73080A] text-white rounded-xl text-xs font-bold transition shadow-2xs inline-flex items-center gap-1">
                                            <span>Detail</span>
                                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
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

    {{-- MODAL RESMI FORMULIR CHECK LIST SERAH TERIMA (INTERNAL HANDOVER SESUAI PDF RESMI IPNET) --}}
    <template x-teleport="body">
        <div x-show="handoverReviewModalOpen" 
             x-cloak
             class="fixed inset-0 bg-slate-900/60 backdrop-blur-2xs z-50 flex items-center justify-center p-3 sm:p-5 overflow-y-auto"
             @click.self="handoverReviewModalOpen = false">
            
            <div id="printable-handover-modal" class="bg-white rounded-2xl w-[860px] max-w-full max-h-[92vh] flex flex-col shadow-2xl border border-slate-300 overflow-hidden my-6">
                
                {{-- Modal Header: Kop Surat Resmi Formulir Internal Handover IPNET --}}
                <div class="p-4 sm:p-5 bg-white border-b border-slate-300 shrink-0">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex items-center gap-3.5">
                            <img src="{{ asset('images/ipnet1.png') }}" alt="IP Network Solusindo" class="h-10 sm:h-12 w-auto object-contain shrink-0" onerror="this.src='/images/ipnet.png'">
                            <div>
                                <h4 class="text-[11px] sm:text-xs font-black uppercase text-slate-800 tracking-wider">PT. IP Network Solusindo</h4>
                                <h2 class="text-xs sm:text-sm font-black text-slate-900 uppercase tracking-tight">FORMULIR CHECK LIST SERAH TERIMA</h2>
                                <p class="text-[10.5px] font-bold text-slate-600 uppercase tracking-wide">(INTERNAL HANDOVER)</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 no-print">
                            <button type="button" 
                                    @click="printHandoverForm()" 
                                    title="Cetak Formulir Serah Terima"
                                    class="px-2.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold border border-slate-300 transition flex items-center gap-1.5 cursor-pointer shadow-2xs">
                                <svg class="w-3.5 h-3.5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                </svg>
                                <span class="hidden sm:inline">Cetak</span>
                            </button>
                            <button type="button" @click="handoverReviewModalOpen = false" class="w-8 h-8 rounded-xl bg-white border border-slate-200 text-slate-400 hover:text-slate-600 flex items-center justify-center text-lg font-bold cursor-pointer shadow-2xs">
                                &times;
                            </button>
                        </div>
                    </div>

                    {{-- Judul Besar & Subtitle Sesuai Dokumen PDF --}}
                    <div class="text-center py-2 border-y-2 border-slate-900 my-3">
                        <h1 class="text-xs sm:text-[13px] font-black text-slate-900 uppercase tracking-tight">
                            CHECKLIST SERAH TERIMA SALES / COMMERCIAL KE MANAGED SERVICE UNTUK KONTRAK MAINTENANCE
                        </h1>
                        <p class="text-[10px] text-slate-500 italic mt-0.5 font-medium">Commercial-to-Project Handover Checklist</p>
                    </div>

                    <div class="p-2.5 bg-slate-50 border border-slate-200 rounded-lg text-[10.5px] text-slate-600 leading-snug">
                        <strong class="text-slate-800">Petunjuk:</strong> Isi kolom yang relevan. Jika suatu butir tidak berlaku, tulis "Tidak Berlaku" dan berikan catatan bila diperlukan. Simpan formulir beserta bukti pendukung pada lokasi dokumen yang ditetapkan.
                    </div>
                </div>

                {{-- Modal Body: 5 Section Sesuai PDF (A, B, C, D, E) --}}
                <div class="p-4 sm:p-5 overflow-y-auto flex-1 space-y-4 text-xs bg-white">
                    
                    {{-- A. REFERENSI KONTRAK (Format Grid/Tabel Sesuai PDF Halaman 1) --}}
                    <div class="border border-slate-800 rounded-lg overflow-hidden shadow-2xs">
                        <div class="bg-slate-800 text-white px-3 py-1.5 font-black text-xs uppercase tracking-wider flex items-center justify-between">
                            <span>A. REFERENSI KONTRAK</span>
                            <span class="text-[10.5px] font-mono font-normal text-slate-200" x-text="activeProject?.so_code || ''"></span>
                        </div>
                        <table class="w-full text-xs border-collapse">
                            <tbody class="divide-y divide-slate-200 bg-white">
                                <tr class="divide-x divide-slate-200">
                                    <td class="w-48 sm:w-56 bg-slate-50 px-3 py-2 font-bold text-slate-700">Pelanggan</td>
                                    <td class="px-3 py-2 font-extrabold text-slate-900" x-text="activeProject?.client || '—'"></td>
                                </tr>
                                <tr class="divide-x divide-slate-200">
                                    <td class="bg-slate-50 px-3 py-2 font-bold text-slate-700">Nomor kontrak / PO</td>
                                    <td class="px-3 py-2 font-mono font-bold text-slate-900">
                                        <div class="flex items-center justify-between flex-wrap gap-2">
                                            <span x-text="activeProject?.so_code || activeProject?.po_number || '—'"></span>
                                            <span class="text-[11px] font-sans font-bold text-[#8F0A0D]" x-text="'Nilai: ' + (activeProject?.contract_value_formatted || '—')"></span>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="divide-x divide-slate-200">
                                    <td class="bg-slate-50 px-3 py-2 font-bold text-slate-700">Tanggal efektif dan masa kontrak</td>
                                    <td class="px-3 py-2 text-slate-800">
                                        <span x-text="(activeProject?.start_date || '—') + ' s/d ' + (activeProject?.end_date || '—')"></span>
                                        <template x-if="activeProject?.days_span">
                                            <span class="ml-1 text-[11px] font-bold text-slate-500" x-text="'(' + activeProject.days_span + ' Hari)'"></span>
                                        </template>
                                    </td>
                                </tr>
                                <tr class="divide-x divide-slate-200">
                                    <td class="bg-slate-50 px-3 py-2 font-bold text-slate-700">Tanggal berakhir</td>
                                    <td class="px-3 py-2 font-semibold text-slate-800" x-text="activeProject?.end_date || '—'"></td>
                                </tr>
                                <tr class="divide-x divide-slate-200">
                                    <td class="bg-slate-50 px-3 py-2 font-bold text-slate-700">PIC Commercial</td>
                                    <td class="px-3 py-2 font-bold text-slate-900" x-text="activeProject?.sales_name || '—'"></td>
                                </tr>
                                <tr class="divide-x divide-slate-200">
                                    <td class="bg-slate-50 px-3 py-2 font-bold text-slate-700">PIC Managed Service / Delivery</td>
                                    <td class="px-3 py-2 font-bold text-blue-900" x-text="activeProject?.pm || 'Tim Delivery PMO'"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    {{-- B. CHECKLIST (8 Butir Sesuai PDF, Kolom Rapi Tidak Terlalu Lebar, Samping Ada Tombol File & Ceklis Persis Managed Service) --}}
                    <div class="border border-slate-800 rounded-lg overflow-hidden shadow-2xs">
                        <div class="bg-slate-800 text-white px-3 py-1.5 font-black text-xs uppercase tracking-wider flex items-center justify-between">
                            <span>B. CHECKLIST</span>
                            <span class="text-[10.5px] font-normal text-slate-200">
                                Terverifikasi: <strong class="text-emerald-400 font-extrabold" x-text="Object.values(activeDocs).filter(v => v === true).length + ' / 8 Butir'"></strong>
                            </span>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs border-collapse">
                                <thead>
                                    <tr class="bg-slate-100 text-slate-800 text-[11px] font-black uppercase tracking-wider border-b border-slate-300">
                                        <th class="py-2 px-2.5 w-10 text-center border-r border-slate-300">No.</th>
                                        <th class="py-2 px-3 w-56 sm:w-60 border-r border-slate-300">Butir / Informasi</th>
                                        <th class="py-2 px-2.5 w-28 text-center border-r border-slate-300">Status / Isian</th>
                                        <th class="py-2 px-3 min-w-[280px]">Catatan / Bukti / PIC &bull; Berkas &amp; Verifikasi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200 bg-white">
                                    @foreach($handoverFormStructure as $item)
                                        @php
                                            $docKey = $item['doc_key'] ?? 'project_doc';
                                        @endphp
                                        <tr class="divide-x divide-slate-200 transition"
                                            :class="activeDocs['{{ $item['key'] }}'] ? 'bg-emerald-50/20' : 'hover:bg-slate-50/50'">
                                            
                                            {{-- No --}}
                                            <td class="py-2.5 px-2 text-center font-extrabold text-slate-800 align-middle bg-slate-50/60">
                                                {{ $item['number'] }}
                                            </td>

                                            {{-- Butir / Informasi --}}
                                            <td class="py-2.5 px-3 align-middle">
                                                <div class="font-bold text-slate-900 text-[11.5px] leading-tight">
                                                    {{ $item['name'] }}
                                                </div>
                                                <div class="text-[10px] text-slate-500 mt-0.5 leading-snug">
                                                    {{ $item['default_note'] }}
                                                </div>
                                            </td>

                                            {{-- Status / Isian --}}
                                            <td class="py-2.5 px-2 text-center align-middle whitespace-nowrap">
                                                <select x-model="checklistStatus['{{ $item['key'] }}']" 
                                                        class="text-[11px] font-bold px-2 py-1 bg-white border border-slate-300 rounded-md outline-none text-slate-800 focus:border-[#8F0A0D]">
                                                    <option value="Ada">Ada</option>
                                                    <option value="Tidak Berlaku">Tidak Berlaku</option>
                                                    <option value="Menyusul">Menyusul</option>
                                                </select>
                                            </td>

                                            {{-- Catatan / Bukti / PIC + Tombol Lihat File & Ceklis Terverifikasi (Persis Managed Service!) --}}
                                            <td class="py-2.5 px-3 align-middle">
                                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                                    <div class="min-w-0 flex-1">
                                                        <input type="text" 
                                                               x-model="checklistNotes['{{ $item['key'] }}']" 
                                                               placeholder="Catatan / PIC / bukti pendukung..." 
                                                               class="w-full text-[11px] px-2.5 py-1 bg-slate-50 border border-slate-200 rounded-md text-slate-700 outline-none focus:bg-white focus:border-slate-400">
                                                    </div>

                                                    {{-- Samping ada Filenya & Ceklis (Persis Managed Service) --}}
                                                    <div class="flex items-center gap-2 shrink-0 self-end sm:self-center">
                                                        {{-- Cek resolved_documents untuk doc_key ini --}}
                                                        <template x-if="activeProject?.resolved_documents?.['{{ $docKey }}']?.file_path">
                                                            <a :href="activeProject.resolved_documents['{{ $docKey }}'].file_url" 
                                                               target="_blank" 
                                                               class="px-2.5 py-1 rounded-lg bg-white hover:bg-slate-50 text-[#1E293B] text-[11px] font-bold border border-[#CBD5E1] shadow-2xs flex items-center gap-1.5 transition">
                                                                <svg class="w-3.5 h-3.5 text-[#64748B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                                <span>Lihat File</span>
                                                            </a>
                                                        </template>
                                                        <template x-if="!activeProject?.resolved_documents?.['{{ $docKey }}']?.file_path">
                                                            <span class="px-2.5 py-1 rounded-lg bg-slate-50 text-[#94A3B8] text-[10.5px] font-medium border border-slate-200">
                                                                Belum Ada File
                                                            </span>
                                                        </template>

                                                        {{-- Ceklis Terverifikasi --}}
                                                        <label class="flex items-center gap-1.5 px-2.5 py-1 rounded-lg border border-[#E2E8F0] bg-[#F8FAFC] hover:bg-[#FEF2F2] hover:border-[#FCA5A5] text-[#1E293B] text-[11px] font-bold cursor-pointer transition select-none"
                                                               :class="activeDocs['{{ $item['key'] }}'] ? 'bg-emerald-50 text-emerald-800 border-emerald-300' : ''">
                                                            <input type="checkbox" 
                                                                   x-model="activeDocs['{{ $item['key'] }}']" 
                                                                   class="w-3.5 h-3.5 text-[#8F0A0D] rounded border-gray-300 focus:ring-[#8F0A0D] cursor-pointer">
                                                            <span>Terverifikasi</span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- C. STATUS HANDOVER (Persis PDF Halaman 1 & Aturan Status) --}}
                    <div class="border border-slate-800 rounded-lg overflow-hidden shadow-2xs">
                        <div class="bg-slate-800 text-white px-3 py-1.5 font-black text-xs uppercase tracking-wider">
                            C. STATUS HANDOVER
                        </div>
                        <div class="p-3.5 space-y-3 bg-white">
                            {{-- Radio Opsi Status --}}
                            <div class="flex items-center gap-6 flex-wrap bg-slate-50 p-2.5 rounded-lg border border-slate-200">
                                <span class="font-black text-slate-800 text-xs uppercase tracking-wide">Status Handover:</span>
                                <label class="inline-flex items-center gap-1.5 font-bold text-xs cursor-pointer select-none" :class="handoverStatusSelected === 'approved' ? 'text-emerald-700' : 'text-slate-600'">
                                    <input type="radio" value="approved" x-model="handoverStatusSelected" class="w-4 h-4 text-emerald-600 focus:ring-emerald-500">
                                    <span>Diterima</span>
                                </label>
                                <label class="inline-flex items-center gap-1.5 font-bold text-xs cursor-pointer select-none" :class="handoverStatusSelected === 'conditional' ? 'text-amber-700' : 'text-slate-600'">
                                    <input type="radio" value="conditional" x-model="handoverStatusSelected" class="w-4 h-4 text-amber-600 focus:ring-amber-500">
                                    <span>Diterima bersyarat</span>
                                </label>
                                <label class="inline-flex items-center gap-1.5 font-bold text-xs cursor-pointer select-none" :class="handoverStatusSelected === 'rejected' ? 'text-rose-700' : 'text-slate-600'">
                                    <input type="radio" value="rejected" x-model="handoverStatusSelected" class="w-4 h-4 text-rose-600 focus:ring-rose-500">
                                    <span>Ditolak / dikembalikan</span>
                                </label>
                            </div>

                            {{-- Tabel Isian Status Handover --}}
                            <table class="w-full text-xs border border-slate-200 divide-y divide-slate-200 rounded-lg overflow-hidden">
                                <tbody class="divide-y divide-slate-200">
                                    <tr class="divide-x divide-slate-200">
                                        <td class="w-64 bg-slate-50 px-3 py-2 font-bold text-slate-700">Kekurangan / risiko yang masih terbuka</td>
                                        <td class="p-1.5">
                                            <input type="text" x-model="handoverRisks" placeholder="Catatan risiko atau kekurangan berkas..." class="w-full px-2.5 py-1 bg-white border border-slate-200 rounded text-xs text-slate-800 outline-none focus:border-[#8F0A0D]">
                                        </td>
                                    </tr>
                                    <tr class="divide-x divide-slate-200">
                                        <td class="bg-slate-50 px-3 py-2 font-bold text-slate-700">Informasi yang belum tersedia</td>
                                        <td class="p-1.5">
                                            <input type="text" x-model="handoverMissingInfo" placeholder="Informasi / dokumen yang masih ditunggu dari Sales..." class="w-full px-2.5 py-1 bg-white border border-slate-200 rounded text-xs text-slate-800 outline-none focus:border-[#8F0A0D]">
                                        </td>
                                    </tr>
                                    <tr class="divide-x divide-slate-200">
                                        <td class="bg-slate-50 px-3 py-2 font-bold text-slate-700">PIC dan target penyelesaian</td>
                                        <td class="p-1.5">
                                            <input type="text" x-model="handoverPicTarget" placeholder="Nama PIC Sales & target tanggal revisi (2x24 jam)..." class="w-full px-2.5 py-1 bg-white border border-slate-200 rounded text-xs text-slate-800 outline-none focus:border-[#8F0A0D]">
                                        </td>
                                    </tr>
                                    <tr class="divide-x divide-slate-200">
                                        <td class="bg-slate-50 px-3 py-2 font-bold text-slate-700">Batasan pekerjaan yang boleh berjalan</td>
                                        <td class="p-1.5">
                                            <input type="text" x-model="handoverWorkLimitations" placeholder="Pekerjaan teknis yang diizinkan berjalan sebelum berkas lengkap..." class="w-full px-2.5 py-1 bg-white border border-slate-200 rounded text-xs text-slate-800 outline-none focus:border-[#8F0A0D]">
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            {{-- Box Aturan Status Sesuai PDF --}}
                            <div class="p-2.5 bg-slate-50 border border-slate-200 rounded-lg text-[10.5px] text-slate-600 leading-snug">
                                <strong class="text-slate-800 uppercase tracking-wider block mb-1">ATURAN STATUS:</strong>
                                <p class="mb-0.5"><strong>Diterima:</strong> informasi minimum cukup untuk memulai perencanaan.</p>
                                <p class="mb-0.5"><strong>Diterima bersyarat:</strong> kekurangan tercatat, ada PIC dan target penyelesaian, dan pekerjaan yang boleh berjalan dibatasi secara eksplisit.</p>
                                <p><strong>Ditolak/dikembalikan:</strong> scope, kontrak, atau persyaratan penting belum jelas; jangan memulai pekerjaan terdampak.</p>
                            </div>
                        </div>
                    </div>

                    {{-- D. CATATAN KHUSUS (Sesuai PDF Halaman 2) --}}
                    <div class="border border-slate-800 rounded-lg overflow-hidden shadow-2xs">
                        <div class="bg-slate-800 text-white px-3 py-1.5 font-black text-xs uppercase tracking-wider">
                            D. CATATAN KHUSUS
                        </div>
                        <div class="p-3 bg-white">
                            <textarea x-model="specialNotes" rows="2" placeholder="[Tuliskan jika ada ekspektasi khusus dari customer, janji fitur khusus, atau kendala yang perlu diwaspadai tim delivery / managed service]" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-800 outline-none focus:bg-white focus:border-[#8F0A0D]"></textarea>
                        </div>
                    </div>

                    {{-- E. KONFIRMASI DAN PERSETUJUAN (Sesuai PDF Halaman 2 + Penugasan Tim Teknis & PM) --}}
                    <div class="border border-slate-800 rounded-lg overflow-hidden shadow-2xs">
                        <div class="bg-slate-800 text-white px-3 py-1.5 font-black text-xs uppercase tracking-wider flex items-center justify-between">
                            <span>KONFIRMASI DAN PERSETUJUAN</span>
                            <span class="text-[10px] font-normal text-slate-200">Kepatuhan Tata Kelola Serah Terima Internal</span>
                        </div>
                        <div class="p-3.5 space-y-3 bg-white">
                            
                            {{-- Penugasan Tim Teknis & PM Pelaksana --}}
                            <div class="p-3 bg-blue-50/50 border border-blue-200 rounded-xl space-y-2">
                                <div class="text-xs font-bold text-blue-900 flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    <span>Penetapan Tim Teknis Pelaksana &amp; Project Manager (Delivery)</span>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1">Tim Teknis / Divisi <span class="text-red-500">*</span></label>
                                        <select x-model="handoverAssign.division_id" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 outline-none focus:border-[#8F0A0D]">
                                            <option value="">-- Pilih Tim Teknis --</option>
                                            @foreach($divisions ?? [] as $div)
                                                <option value="{{ $div->id }}">{{ $div->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1">Tugaskan Project Manager (PM) <span class="text-red-500">*</span></label>
                                        <select x-model="handoverAssign.pm_id" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 outline-none focus:border-[#8F0A0D]">
                                            <option value="">-- Pilih Project Manager --</option>
                                            @foreach($pmList ?? [] as $pm)
                                                <option value="{{ $pm->id }}">{{ $pm->name }} ({{ $pm->email }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            {{-- 4 Kolom Tanda Tangan Sesuai PDF Halaman 2 --}}
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 text-center">
                                {{-- 1. Sales --}}
                                <div class="border border-slate-300 rounded-lg p-2.5 bg-slate-50 flex flex-col justify-between h-36">
                                    <div>
                                        <div class="font-bold text-[10.5px] text-slate-900 uppercase">Diserahkan oleh</div>
                                        <div class="text-[10px] text-slate-500">Sales / Commercial</div>
                                    </div>
                                    <div class="my-auto py-1">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[9.5px] font-mono font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            <span>Verified Digital</span>
                                        </span>
                                    </div>
                                    <div class="text-left text-[10px] border-t border-slate-200 pt-1 text-slate-600">
                                        <p class="truncate"><strong class="text-slate-800">Nama:</strong> <span x-text="activeProject?.sales_name || '—'"></span></p>
                                        <p><strong class="text-slate-800">Tgl:</strong> <span x-text="activeProject?.handover_submitted_at ? activeProject.handover_submitted_at.substring(0, 10) : '—'"></span></p>
                                    </div>
                                </div>

                                {{-- 2. Delivery / PM --}}
                                <div class="border border-slate-300 rounded-lg p-2.5 bg-slate-50 flex flex-col justify-between h-36">
                                    <div>
                                        <div class="font-bold text-[10.5px] text-slate-900 uppercase">Diperiksa / diterima oleh</div>
                                        <div class="text-[10px] text-slate-500">Managed Service / PM</div>
                                    </div>
                                    <div class="my-auto py-1">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[9.5px] font-mono font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                            <span>PMO Verified</span>
                                        </span>
                                    </div>
                                    <div class="text-left text-[10px] border-t border-slate-200 pt-1 text-slate-600">
                                        <p class="truncate"><strong class="text-slate-800">Nama:</strong> <span x-text="activeProject?.pm || 'PM Delivery'"></span></p>
                                        <p><strong class="text-slate-800">Tgl:</strong> {{ date('d/m/Y') }}</p>
                                    </div>
                                </div>

                                {{-- 3. TL / Solution --}}
                                <div class="border border-slate-300 rounded-lg p-2.5 bg-slate-50 flex flex-col justify-between h-36">
                                    <div>
                                        <div class="font-bold text-[10.5px] text-slate-900 uppercase">Validasi teknis oleh</div>
                                        <div class="text-[10px] text-slate-500">TL / Solution (bila perlu)</div>
                                    </div>
                                    <div class="my-auto py-1">
                                        <span class="text-[10px] text-slate-400 italic">Paraf / Approval</span>
                                    </div>
                                    <div class="text-left text-[10px] border-t border-slate-200 pt-1 text-slate-600">
                                        <p class="truncate"><strong class="text-slate-800">Nama:</strong> Lead Engineer</p>
                                        <p><strong class="text-slate-800">Tgl:</strong> {{ date('d/m/Y') }}</p>
                                    </div>
                                </div>

                                {{-- 4. Pihak Berwenang --}}
                                <div class="border border-slate-300 rounded-lg p-2.5 bg-slate-50 flex flex-col justify-between h-36">
                                    <div>
                                        <div class="font-bold text-[10.5px] text-slate-900 uppercase">Persetujuan / eskalasi</div>
                                        <div class="text-[10px] text-slate-500">Pihak berwenang (bila perlu)</div>
                                    </div>
                                    <div class="my-auto py-1">
                                        <span class="text-[10px] text-slate-400 italic">Otorisasi GM / PMO</span>
                                    </div>
                                    <div class="text-left text-[10px] border-t border-slate-200 pt-1 text-slate-600">
                                        <p class="truncate"><strong class="text-slate-800">Nama:</strong> PMO Head</p>
                                        <p><strong class="text-slate-800">Tgl:</strong> {{ date('d/m/Y') }}</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Catatan Pengendalian Sesuai Dokumen PDF Halaman 2 --}}
                            <div class="p-2.5 bg-slate-50 border border-slate-200 rounded-lg text-[10px] text-slate-600 leading-relaxed italic">
                                <strong>Catatan pengendalian:</strong> handover tidak mengubah kewenangan formal atau tanggung jawab atas output yang telah dibuat. Perubahan scope/komitmen pelanggan harus melalui mekanisme persetujuan yang berlaku.
                            </div>
                        </div>
                    </div>

                </div>

                {{-- Modal Footer Actions --}}
                <div class="p-4 sm:px-6 border-t border-slate-300 bg-slate-50 flex flex-wrap items-center justify-between gap-2.5 shrink-0 no-print">
                    <div class="flex items-center gap-2">
                        <button type="button" 
                                @click="printHandoverForm()" 
                                class="px-3.5 py-2 bg-white hover:bg-slate-100 text-slate-700 border border-slate-300 rounded-xl text-xs font-bold transition shadow-2xs flex items-center gap-1.5 cursor-pointer">
                            <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                            </svg>
                            <span>Cetak Formulir (PDF)</span>
                        </button>
                        <button type="button" @click="handoverReviewModalOpen = false" class="px-4 py-2 bg-white border border-slate-300 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition cursor-pointer">
                            Tutup
                        </button>
                    </div>

                    <div class="flex items-center gap-2">
                        {{-- Tombol sesuai status radio yang dipilih --}}
                        <template x-if="handoverStatusSelected === 'conditional'">
                            <button type="button" 
                                    @click="handleConditionalClick()" 
                                    class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-xs font-bold shadow-xs transition cursor-pointer flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                <span>Simpan Status Diterima Bersyarat (2x24 Jam)</span>
                            </button>
                        </template>

                        <template x-if="handoverStatusSelected === 'rejected'">
                            <button type="button" 
                                    @click="handleRejectClick()" 
                                    class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold shadow-xs transition cursor-pointer flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                <span>Kembalikan ke Sales (Ditolak)</span>
                            </button>
                        </template>

                        <template x-if="handoverStatusSelected === 'approved'">
                            <button type="button" 
                                    @click="approveHandover()" 
                                    class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-xs transition cursor-pointer flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                <span>Sahkan Serah Terima (Handover Approved)</span>
                            </button>
                        </template>
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
            statusFilter: 'Semua',
            selectedDivision: 'all',
            selectedPm: 'all',
            selectedStage: 'all',
            handoverFilter: 'all',
            activeTab: (new URLSearchParams(window.location.search).get('tab') === 'incoming' || new URLSearchParams(window.location.search).get('tab') === 'pending_handover') ? 'pending_handover' : 'all',
            viewMode: 'table', // 'table' | 'cards'
            perPage: 10,
            currentPage: 1,

            docsModalOpen: false,
            handoverReviewModalOpen: false,
            handoverToMsModalOpen: false,
            showConditionalInput: false,
            conditionalNotes: '',
            handoverStatusSelected: 'approved',
            handoverRisks: '',
            handoverMissingInfo: '',
            handoverPicTarget: '',
            handoverWorkLimitations: '',
            specialNotes: '',
            checklistStatus: {},
            checklistNotes: {},
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
                    const matchHandover = this.handoverFilter === 'all' || 
                                          (this.handoverFilter === 'pending' && p.handover_status === 'Submitted') ||
                                          (this.handoverFilter === 'conditional' && p.handover_status === 'Conditional');

                    let matchStatusFilter = true;
                    if (this.statusFilter === 'Deliver') {
                        matchStatusFilter = (p.stage === 'Deliver');
                    } else if (this.statusFilter === 'ReadyMS') {
                        matchStatusFilter = (p.progress >= 100 || (p.stage === 'Deliver' && (p.progress >= 100 || p.docs_completed_count >= 11)));
                    } else if (this.statusFilter === 'Operate') {
                        matchStatusFilter = (p.stage === 'Operate');
                    } else if (this.statusFilter === 'Pending') {
                        matchStatusFilter = (p.handover_status === 'Submitted' || p.handover_status === 'Conditional');
                    } else if (this.statusFilter === 'Completed') {
                        matchStatusFilter = (p.stage === 'Operate' || p.stage === 'Completed' || p.progress >= 100);
                    }

                    let matchTab = true;
                    if (this.activeTab === 'pending_handover') {
                        matchTab = (p.handover_status === 'Submitted' || p.handover_status === 'Conditional');
                    } else {
                        matchTab = (p.handover_status !== 'Submitted');
                    }

                    return matchSearch && matchDiv && matchPm && matchStg && matchHandover && matchTab && matchStatusFilter;
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
                this.openHandoverReviewModal(project);
            },

            openHandoverReviewModal(project) {
                this.activeProject = project;
                this.handoverAssign.pm_id = project.pm_id || '';
                this.handoverAssign.division_id = project.division_id || '';
                
                // Status pilihan: approved / conditional / rejected
                if (project.handover_status === 'Conditional') {
                    this.handoverStatusSelected = 'conditional';
                } else if (project.handover_status === 'Rejected') {
                    this.handoverStatusSelected = 'rejected';
                } else {
                    this.handoverStatusSelected = 'approved';
                }

                const hData = (typeof project.handover_data === 'object' && project.handover_data) ? project.handover_data : {};
                this.handoverRisks = hData.handover_risks || '';
                this.handoverMissingInfo = hData.handover_missing_info || project.handover_conditional_notes || '';
                this.handoverPicTarget = hData.handover_pic_target || '';
                this.handoverWorkLimitations = hData.handover_work_limitations || '';
                this.specialNotes = project.special_notes || hData.special_notes || '';
                this.conditionalNotes = project.handover_conditional_notes || '';

                // Inisialisasi 8 butir checklist
                const savedDocs = project.documents_checklist || hData.checklist || {};
                const savedStatus = hData.checklist_status || {};
                const savedNotes = hData.checklist_notes || {};

                this.activeDocs = {};
                this.checklistStatus = {};
                this.checklistNotes = {};

                const items = [
                    { key: 'ruang_lingkup', doc_key: 'tor' },
                    { key: 'daftar_aset', doc_key: 'boq' },
                    { key: 'jadwal_pm', doc_key: 'pm_schedule' },
                    { key: 'mekanisme_cm', doc_key: 'cm_ticketing' },
                    { key: 'sla_layanan', doc_key: 'contract' },
                    { key: 'dokumen_teknis', doc_key: 'project_doc' },
                    { key: 'kondisi_awal', doc_key: 'baseline_doc' },
                    { key: 'pelaporan_berkala', doc_key: 'ba' },
                ];

                items.forEach(item => {
                    const k = item.key;
                    const dk = item.doc_key;
                    const docObj = (project.resolved_documents && project.resolved_documents[dk]) ? project.resolved_documents[dk] : null;
                    const hasFile = !!(docObj && docObj.file_path);
                    const isVerified = !!(docObj && docObj.verified);

                    if (savedDocs[k] !== undefined) {
                        this.activeDocs[k] = !!savedDocs[k];
                    } else if (savedDocs[dk] !== undefined) {
                        this.activeDocs[k] = !!savedDocs[dk];
                    } else {
                        this.activeDocs[k] = isVerified || hasFile;
                    }

                    this.checklistStatus[k] = savedStatus[k] || (this.activeDocs[k] ? 'Ada' : 'Ada');
                    this.checklistNotes[k] = savedNotes[k] || (docObj?.number ? ('No: ' + docObj.number) : '');
                });

                this.handoverReviewModalOpen = true;
            },

            printHandoverForm() {
                window.print();
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
                        body: JSON.stringify({
                            pm_id: this.handoverAssign.pm_id,
                            division_id: this.handoverAssign.division_id,
                            checklist: this.activeDocs,
                            checklist_status: this.checklistStatus,
                            checklist_notes: this.checklistNotes,
                            handover_status_selected: this.handoverStatusSelected,
                            handover_risks: this.handoverRisks,
                            handover_missing_info: this.handoverMissingInfo,
                            handover_pic_target: this.handoverPicTarget,
                            handover_work_limitations: this.handoverWorkLimitations,
                            special_notes: this.specialNotes,
                        })
                    });

                    const data = await response.json();
                    if (response.ok && data.success) {
                        this.handoverReviewModalOpen = false;
                        alert(data.message || 'Handover berhasil disahkan!');
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
                const notes = this.handoverMissingInfo || this.conditionalNotes || this.handoverRisks;
                if (!notes || !notes.trim()) {
                    alert('Harap isi catatan kekurangan berkas atau informasi yang belum tersedia pada Section C.');
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
                        body: JSON.stringify({
                            notes: notes,
                            status: 'Conditional',
                            checklist: this.activeDocs,
                            checklist_status: this.checklistStatus,
                            checklist_notes: this.checklistNotes,
                            handover_risks: this.handoverRisks,
                            handover_missing_info: this.handoverMissingInfo,
                            handover_pic_target: this.handoverPicTarget,
                            handover_work_limitations: this.handoverWorkLimitations,
                            special_notes: this.specialNotes,
                        })
                    });

                    const data = await response.json();
                    if (response.ok && data.success) {
                        this.handoverReviewModalOpen = false;
                        alert(data.message || 'Status handover diterima bersyarat berhasil dicatat.');
                        window.location.reload();
                    } else {
                        alert(data.message || 'Gagal menyimpan status bersyarat.');
                    }
                } catch (e) {
                    console.error(e);
                    alert('Terjadi kesalahan jaringan.');
                }
            },

            async handleRejectClick() {
                const reason = this.handoverRisks || this.handoverMissingInfo || 'Persyaratan dan kelengkapan dokumen belum memenuhi standar.';
                if (!confirm('Apakah Anda yakin ingin menolak berkas serah terima ini dan mengembalikannya ke Sales?')) {
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
                        body: JSON.stringify({
                            notes: '[Ditolak / Dikembalikan]: ' + reason,
                            status: 'Rejected',
                            checklist: this.activeDocs,
                            checklist_status: this.checklistStatus,
                            checklist_notes: this.checklistNotes,
                            handover_risks: this.handoverRisks,
                            handover_missing_info: this.handoverMissingInfo,
                            handover_pic_target: this.handoverPicTarget,
                            handover_work_limitations: this.handoverWorkLimitations,
                            special_notes: this.specialNotes,
                        })
                    });

                    const data = await response.json();
                    if (response.ok && data.success) {
                        this.handoverReviewModalOpen = false;
                        alert(data.message || 'Berkas serah terima berhasil dikembalikan ke Sales.');
                        window.location.reload();
                    } else {
                        alert(data.message || 'Gagal mengembalikan berkas serah terima.');
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
                        body: JSON.stringify({ 
                            documents_checklist: this.activeDocs,
                            checklist: this.activeDocs,
                            checklist_status: this.checklistStatus,
                            checklist_notes: this.checklistNotes,
                            special_notes: this.specialNotes
                        })
                    });

                    const data = await response.json();
                    if (response.ok && data.success) {
                        this.handoverReviewModalOpen = false;
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

<style>
@media print {
    body * {
        visibility: hidden !important;
    }
    #printable-handover-modal, #printable-handover-modal * {
        visibility: visible !important;
    }
    #printable-handover-modal {
        position: absolute !important;
        left: 0 !important;
        top: 0 !important;
        width: 100% !important;
        max-width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
        border: none !important;
        box-shadow: none !important;
        background: white !important;
        overflow: visible !important;
    }
    .no-print {
        display: none !important;
    }
}
</style>
@endpush
@endsection
