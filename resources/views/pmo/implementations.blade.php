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
                            <option value="{{ $div->id }}">{{ preg_replace('/^Divisi\s+/i', 'Tim ', $div->name) }}</option>
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
                                                    <span x-text="(project.division || 'Belum Ditentukan Sales').replace(/divisi\s*/gi, 'Tim ')"></span>
                                                </span>
                                            </div>
                                            <template x-if="project.pm && project.pm !== 'Belum Ditentukan'">
                                                <div class="text-[11px] text-slate-500 font-medium flex items-center gap-1">
                                                    <span>PM:</span>
                                                    <span class="font-bold text-slate-700" x-text="project.pm"></span>
                                                </div>
                                            </template>
                                            <template x-if="!project.pm || project.pm === 'Belum Ditentukan'">
                                                <div class="text-[10.5px] text-slate-500 font-medium flex items-center gap-1">
                                                    <span>PM:</span>
                                                    <span class="italic text-amber-700 font-semibold" x-text="activeTab === 'pending_handover' ? 'Ditetapkan saat Review' : 'Belum Ditugaskan'"></span>
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
                                            {{-- Khusus Incoming Handover (Menunggu Verifikasi / Belum Sah): HANYA tombol Verifikasi Dokumen warna merah --}}
                                            <template x-if="activeTab === 'pending_handover' || project.handover_status === 'Submitted' || project.handover_status === 'Conditional'">
                                                <button type="button"
                                                        @click="openHandoverReviewModal(project)"
                                                        class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-[#8F0A0D] hover:bg-[#73080A] text-white rounded-xl text-xs font-bold transition shadow-xs cursor-pointer active:scale-95">
                                                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    <span>Verifikasi Dokumen</span>
                                                </button>
                                            </template>

                                            {{-- Khusus Projek Aktif (Setelah Verifikasi Sah): Tampilkan tombol Handover MS & Detail --}}
                                            <template x-if="activeTab !== 'pending_handover' && project.handover_status !== 'Submitted' && project.handover_status !== 'Conditional'">
                                                <div class="flex items-center gap-1.5">
                                                    <template x-if="project.ms_handover_status === 'Submitted'">
                                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-amber-50 text-amber-800 border border-amber-200/80 rounded-xl text-xs font-bold shadow-2xs">
                                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                                            <span>Menunggu Review MS</span>
                                                        </span>
                                                    </template>
                                                    <template x-if="project.ms_handover_status === 'Accepted'">
                                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-50 text-emerald-800 border border-emerald-200/80 rounded-xl text-xs font-bold shadow-2xs">
                                                            <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                                            <span>Diserahkan ke MS</span>
                                                        </span>
                                                    </template>
                                                    <template x-if="project.stage === 'Deliver' && (!project.ms_handover_status || project.ms_handover_status === 'Pending')">
                                                        <button type="button"
                                                                @click="openHandoverToMsModal(project)"
                                                                class="inline-flex items-center gap-1 px-3 py-1.5 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-bold transition shadow-xs cursor-pointer active:scale-95">
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
                                            </template>
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
                                        <p class="text-xs text-slate-400 mt-1">Silakan sesuaikan kata kunci pencarian, tab kategori, atau filter tim teknis di atas.</p>
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
                                            <span class="font-bold text-slate-800 text-[11px]" x-text="(project.division || 'Belum Ditentukan Sales').replace(/divisi\s*/gi, 'Tim ')"></span>
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
                                        {{-- Khusus Incoming Handover: HANYA tombol Verifikasi Dokumen warna merah --}}
                                        <template x-if="activeTab === 'pending_handover' || project.handover_status === 'Submitted' || project.handover_status === 'Conditional'">
                                            <button type="button"
                                                    @click="openHandoverReviewModal(project)"
                                                    class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-[#8F0A0D] hover:bg-[#73080A] text-white rounded-xl text-xs font-bold transition shadow-xs cursor-pointer active:scale-95">
                                                <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                <span>Verifikasi Dokumen</span>
                                            </button>
                                        </template>

                                        {{-- Khusus Projek Aktif (Setelah Verifikasi Sah): Tampilkan tombol Handover MS & Detail --}}
                                        <template x-if="activeTab !== 'pending_handover' && project.handover_status !== 'Submitted' && project.handover_status !== 'Conditional'">
                                            <div class="flex items-center gap-1.5">
                                                <template x-if="project.ms_handover_status === 'Submitted'">
                                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-amber-50 text-amber-800 border border-amber-200/80 rounded-xl text-xs font-bold shadow-2xs">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                                        <span>Menunggu Review MS</span>
                                                    </span>
                                                </template>
                                                <template x-if="project.ms_handover_status === 'Accepted'">
                                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-50 text-emerald-800 border border-emerald-200/80 rounded-xl text-xs font-bold shadow-2xs">
                                                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                                        <span>Diserahkan ke MS</span>
                                                    </span>
                                                </template>
                                                <template x-if="project.stage === 'Deliver' && (!project.ms_handover_status || project.ms_handover_status === 'Pending')">
                                                    <button type="button"
                                                            @click="openHandoverToMsModal(project)"
                                                            class="px-3 py-1.5 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-bold transition shadow-xs cursor-pointer active:scale-95">
                                                        <span>Handover MS</span>
                                                    </button>
                                                </template>

                                                <a :href="'/pmo/projects/' + project.id" 
                                                   title="Buka Dashboard PMO & Perhitungan Proyek"
                                                   class="px-3.5 py-1.5 bg-[#8F0A0D] hover:bg-[#73080A] text-white rounded-xl text-xs font-bold transition shadow-2xs inline-flex items-center gap-1">
                                                    <span>Detail</span>
                                                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                                                </a>
                                            </div>
                                        </template>
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
            
            <div id="printable-handover-modal" class="bg-white rounded-2xl max-w-7xl w-full max-h-[94vh] flex flex-col shadow-2xl border border-[#CBD5E1] overflow-hidden my-auto">
                
                {{-- Modal Header Persis Gambar 2 --}}
                <div class="px-6 py-4 border-b border-[#E2E8F0] flex items-center justify-between bg-[#F8FAFC] shrink-0">
                    <div class="flex items-center gap-2.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#8F0A0D]"></span>
                        <div>
                            <h3 class="text-[16px] font-bold text-[#0F172A]" x-text="'Tinjau Serah Terima: ' + (activeProject?.name || '')"></h3>
                            <p class="text-[11px] text-[#64748B]">Verifikasi checklist serah terima resmi di bawah ini sesuai acuan template PT IP Network Solusindo.</p>
                        </div>
                    </div>
                    <button type="button" @click="handoverReviewModalOpen = false" class="text-[#64748B] hover:text-[#1E293B] p-1.5 rounded-lg hover:bg-[#E2E8F0] transition cursor-pointer" title="Tutup">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Modal Body Container Abu-Abu Lembut Persis Template Lebar Managed Service --}}
                <div class="flex-1 overflow-y-auto px-4 pt-4 pb-8 sm:px-8 sm:pt-6 sm:pb-8 bg-[#F1F5F9]/80 space-y-4">
                    
                    {{-- Banner Kategori Laporan Konsisten dengan Form Engineer Activity & Serah Terima MS --}}
                    <div class="flex flex-wrap items-center justify-between gap-3 bg-white p-3.5 rounded-xl border border-[#CBD5E1] shadow-xs no-print">
                        <div class="flex items-center gap-2.5">
                            <span class="text-xs font-bold text-[#475569] uppercase tracking-wider">KATEGORI LAPORAN:</span>
                            <span class="px-3 py-1 bg-red-50 text-[#8F0A0D] border border-red-200 rounded-lg text-xs font-bold tracking-wide uppercase shadow-2xs">
                                Serah Terima Project Manager (PMO)
                            </span>
                        </div>

                        <div class="text-xs text-slate-500 font-medium">
                            Format resmi PT IP Network Solusindo
                        </div>
                    </div>

                    {{-- ============================================================== --}}
                    {{-- DOKUMEN RESMI SERAH TERIMA PROJECT MANAGER SESUAI PDF           --}}
                    {{-- ============================================================== --}}
                    <div class="bg-white border-2 border-[#CBD5E1] rounded-xl p-6 sm:p-10 shadow-sm space-y-5 text-[#0F172A] font-sans relative">
                        
                        {{-- Kop Dokumen Persis PDF (Proporsional & Rapi) --}}
                        <div class="flex items-start justify-between gap-4">
                            <div class="shrink-0">
                                <img src="{{ asset('images/ipnet1.png') }}" onerror="this.src='/images/ipnet.png'" alt="Logo IP Network Solusindo" class="h-12 sm:h-14 w-auto object-contain">
                            </div>
                            <div class="text-right shrink-0">
                                <div class="text-[#8F0A0D] font-black text-lg sm:text-xl uppercase tracking-tight leading-none">
                                    CHECKLIST SERAH TERIMA
                                </div>
                                <div class="text-black font-black text-xs sm:text-sm uppercase tracking-normal mt-1">
                                    IPNET#1
                                </div>
                                <div class="text-[10px] text-slate-500 italic font-medium mt-0.5">
                                    Hal 1
                                </div>
                            </div>
                        </div>

                        {{-- Judul Utama Dokumen Persis PDF --}}
                        <div class="text-center my-3 sm:my-4">
                            <h1 class="text-sm sm:text-base font-black text-black uppercase tracking-wide leading-snug">
                                SERAH TERIMA SALES / COMMERCIAL KE PROJECT MANAGER
                            </h1>
                            <p class="text-xs italic text-slate-600 mt-1 font-serif">
                                Commercial-to-Project Handover Checklist
                            </p>
                        </div>

                        {{-- Grid Metadata Form (Kode Form, Versi, Tanggal Efektif, No. Form / Handover) --}}
                        <div class="border border-black overflow-hidden text-xs">
                            <table class="w-full border-collapse border border-black">
                                <tbody>
                                    <tr class="border-b border-black">
                                        <td class="w-1/4 py-1.5 px-3 font-bold bg-[#F8FAFC] border-r border-black">Kode Form</td>
                                        <td class="w-1/4 py-1.5 px-3 border-r border-black font-mono font-bold text-slate-900">IPN-FRM-PM-01</td>
                                        <td class="w-1/4 py-1.5 px-3 font-bold bg-[#F8FAFC] border-r border-black">Versi</td>
                                        <td class="w-1/4 py-1.5 px-3 font-bold text-slate-900">1.0</td>
                                    </tr>
                                    <tr>
                                        <td class="py-1.5 px-3 font-bold bg-[#F8FAFC] border-r border-black">Tanggal Efektif</td>
                                        <td class="py-1.5 px-3 border-r border-black font-semibold text-slate-900" x-text="activeProject?.start_date || '{{ date('d M Y') }}'"></td>
                                        <td class="py-1.5 px-3 font-bold bg-[#F8FAFC] border-r border-black">No. Form / Handover</td>
                                        <td class="py-1.5 px-3 font-mono font-bold text-[#8F0A0D]" x-text="activeProject?.so_code || ('HO-PMO-' + (activeProject?.id ? String(activeProject.id).padStart(4, '0') : '0001'))"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        {{-- Deskripsi Penggunaan PMO Persis PDF --}}
                        <div class="text-[11.5px] text-slate-800 leading-relaxed border border-slate-300 bg-slate-50/70 p-3 rounded-lg italic">
                            Digunakan untuk pekerjaan yang mencakup implementasi/proyek dan memerlukan pengendalian PMO. Sales mengisi dan melampirkan dokumen; PMO memeriksa kesiapan. Item yang belum tersedia tidak otomatis dianggap diterima.
                        </div>

                        {{-- 1. TUJUAN DAN CARA PAKAI (Banner Hitam & Border Hitam Persis PDF) --}}
                        <div class="border border-black overflow-hidden">
                            <div class="bg-black text-white px-3 py-1.5 font-black text-xs uppercase tracking-wider">
                                TUJUAN DAN CARA PAKAI
                            </div>
                            <div class="p-0 text-xs">
                                <table class="w-full border-collapse border border-black">
                                    <tbody>
                                        <tr class="border-b border-black">
                                            <td class="w-1/5 py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-black">Nama customer</td>
                                            <td class="w-3/10 py-1.5 px-2.5 border-r border-black font-bold text-[#0F172A]" x-text="activeProject?.client || '—'"></td>
                                            <td class="w-1/5 py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-black">Nama proyek</td>
                                            <td class="w-3/10 py-1.5 px-2.5 font-black text-[#8F0A0D]" x-text="activeProject?.name || '—'"></td>
                                        </tr>
                                        <tr class="border-b border-black">
                                            <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-black">Kode proyek / Sales Order</td>
                                            <td class="py-1.5 px-2.5 border-r border-black font-mono font-bold" x-text="activeProject?.so_code || '—'"></td>
                                            <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-black">No. kontrak / PO / SPK</td>
                                            <td class="py-1.5 px-2.5 font-mono font-bold" x-text="activeProject?.po_number || activeProject?.so_code || '—'"></td>
                                        </tr>
                                        <tr class="border-b border-black">
                                            <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-black">Tanggal handover</td>
                                            <td class="py-1.5 px-2.5 border-r border-black font-semibold" x-text="activeProject?.handover_submitted_at || '{{ date('d M Y') }}'"></td>
                                            <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-black">Target mulai / milestone</td>
                                            <td class="py-1.5 px-2.5 font-semibold" x-text="activeProject?.start_date || '—'"></td>
                                        </tr>
                                        <tr class="border-b border-black">
                                            <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-black">Sales / Account Manager</td>
                                            <td class="py-1.5 px-2.5 border-r border-black font-bold text-[#0F172A]" x-text="activeProject?.sales_name || '—'"></td>
                                            <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-black">PM penerima</td>
                                            <td class="py-1.5 px-2.5 font-bold text-blue-900" x-text="activeProject?.pm || 'Tim Delivery PMO'"></td>
                                        </tr>
                                        <tr>
                                            <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-black">Folder dokumen / lokasi penyimpanan</td>
                                            <td class="py-1.5 px-2.5 border-r border-black">
                                                <input type="text" 
                                                       x-model="docStorageLocation" 
                                                       placeholder="Link Google Drive / Folder Server..." 
                                                       class="w-full text-xs px-2 py-0.5 border border-slate-300 rounded bg-white outline-none focus:border-[#8F0A0D]">
                                            </td>
                                            <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-black">Jenis proyek</td>
                                            <td class="py-1.5 px-2.5">
                                                <div class="flex items-center gap-3 flex-wrap text-xs">
                                                    <label class="inline-flex items-center gap-1 cursor-pointer select-none">
                                                        <input type="radio" value="Implementasi" x-model="projectTypeCategory" class="w-3.5 h-3.5 text-[#8F0A0D] focus:ring-[#8F0A0D]">
                                                        <span>Implementasi</span>
                                                    </label>
                                                    <label class="inline-flex items-center gap-1 cursor-pointer select-none">
                                                        <input type="radio" value="Upgrade" x-model="projectTypeCategory" class="w-3.5 h-3.5 text-[#8F0A0D] focus:ring-[#8F0A0D]">
                                                        <span>Upgrade</span>
                                                    </label>
                                                    <label class="inline-flex items-center gap-1 cursor-pointer select-none">
                                                        <input type="radio" value="Relokasi" x-model="projectTypeCategory" class="w-3.5 h-3.5 text-[#8F0A0D] focus:ring-[#8F0A0D]">
                                                        <span>Relokasi</span>
                                                    </label>
                                                    <label class="inline-flex items-center gap-1 cursor-pointer select-none">
                                                        <input type="radio" value="Lainnya" x-model="projectTypeCategory" class="w-3.5 h-3.5 text-[#8F0A0D] focus:ring-[#8F0A0D]">
                                                        <span>Lainnya:</span>
                                                    </label>
                                                    <template x-if="projectTypeCategory === 'Lainnya'">
                                                        <input type="text" 
                                                               x-model="projectTypeOther" 
                                                               placeholder="Sebutkan..." 
                                                               class="w-24 text-[11px] px-1.5 py-0.5 border border-slate-300 rounded outline-none focus:border-[#8F0A0D]">
                                                    </template>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Petunjuk Pengisian Baris Checklist Bawah Tabel --}}
                        <div class="text-[11px] text-slate-700 italic">
                            Tandai status setiap item. Jika tidak relevan, pilih N/A dan tuliskan alasannya pada kolom catatan.
                        </div>

                        {{-- 2. CHECKLIST KELENGKAPAN (14 Butir Sesuai PDF IPN-FRM-PM-01) --}}
                        <div class="border border-black overflow-hidden">
                            <div class="bg-black text-white px-3 py-1.5 font-black text-xs uppercase tracking-wider flex items-center justify-between">
                                <span>CHECKLIST KELENGKAPAN</span>
                                <span class="text-[10.5px] font-medium text-slate-200">
                                    Total 14 Butir Standar PMO
                                </span>
                            </div>

                            <div class="overflow-x-auto text-xs">
                                <table class="w-full border-collapse border border-black text-left">
                                    <thead>
                                        <tr class="bg-[#F8FAFC] text-black text-[11px] font-black uppercase tracking-wider border-b border-black">
                                            <th class="py-2 px-2 w-10 text-center border-r border-black">NO.</th>
                                            <th class="py-2 px-3 w-64 sm:w-72 border-r border-black">KATEGORI / ITEM PEMERIKSAAN</th>
                                            <th class="py-2 px-2 text-center w-28 border-r border-black">FILE / BUKTI</th>
                                            <th class="py-2 px-2 text-center w-24 border-r border-black">ADA / SIAP</th>
                                            <th class="py-2 px-2 text-center w-12 border-r border-black">N/A</th>
                                            <th class="py-2 px-3">CATATAN / REFERENSI DOKUMEN</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white">
                                        @foreach($handoverFormStructure as $item)
                                            @php
                                                $docKey = $item['doc_key'] ?? 'project_doc';
                                            @endphp
                                            <tr class="border-b border-black hover:bg-slate-50/50 transition">
                                                
                                                {{-- No --}}
                                                <td class="py-2.5 px-2 text-center font-bold text-black align-middle bg-[#F8FAFC] border-r border-black">
                                                    {{ $item['number'] }}
                                                </td>

                                                {{-- Kategori & Item Pemeriksaan --}}
                                                <td class="py-2 px-3 align-middle border-r border-black">
                                                    <div class="text-[9.5px] font-bold text-[#8F0A0D] uppercase tracking-wider">
                                                        {{ $item['category'] }}
                                                    </div>
                                                    <div class="font-bold text-black text-[11px] leading-tight mt-0.5">
                                                        {{ $item['name'] }}
                                                    </div>
                                                </td>

                                                {{-- File / Bukti (Lihat File) --}}
                                                <td class="py-2 px-2 text-center align-middle whitespace-nowrap border-r border-black">
                                                    <template x-if="activeProject?.resolved_documents?.['{{ $docKey }}']?.file_path">
                                                        <a :href="activeProject.resolved_documents['{{ $docKey }}'].file_url" 
                                                           target="_blank" 
                                                           class="inline-flex items-center gap-1 px-2 py-1 rounded bg-white hover:bg-slate-50 text-[#1E293B] text-[10.5px] font-bold border border-slate-300 shadow-2xs transition">
                                                            <svg class="w-3 h-3 text-[#64748B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                            <span>Lihat File</span>
                                                        </a>
                                                    </template>
                                                    <template x-if="!activeProject?.resolved_documents?.['{{ $docKey }}']?.file_path">
                                                        <span class="inline-block px-1.5 py-0.5 rounded bg-slate-50 text-[#94A3B8] text-[10px] font-medium border border-slate-200">
                                                            Belum Ada File
                                                        </span>
                                                    </template>
                                                </td>

                                                {{-- Ada / Siap --}}
                                                <td class="py-2 px-2 text-center align-middle whitespace-nowrap border-r border-black">
                                                    <select x-model="checklistStatus['{{ $item['key'] }}']" 
                                                            :class="checklistNa['{{ $item['key'] }}'] ? 'bg-slate-100 text-slate-400 border-slate-200' : 'bg-white text-black font-bold border-slate-400'"
                                                            class="text-[11px] px-1.5 py-1 border rounded outline-none focus:border-[#8F0A0D]">
                                                        <option value="Ada">Ya</option>
                                                        <option value="Tidak">Tidak</option>
                                                    </select>
                                                </td>

                                                {{-- N/A Checkbox --}}
                                                <td class="py-2 px-2 text-center align-middle border-r border-black">
                                                    <label class="inline-flex items-center justify-center cursor-pointer select-none" title="Tandai N/A jika butir ini tidak berlaku">
                                                        <input type="checkbox" 
                                                               x-model="checklistNa['{{ $item['key'] }}']"
                                                               @change="toggleNa('{{ $item['key'] }}')"
                                                               class="w-4 h-4 text-[#8F0A0D] rounded border-slate-400 focus:ring-[#8F0A0D] cursor-pointer">
                                                    </label>
                                                </td>

                                                {{-- Catatan / Referensi Dokumen --}}
                                                <td class="py-2 px-2.5 align-middle">
                                                    <input type="text" 
                                                           x-model="checklistNotes['{{ $item['key'] }}']" 
                                                           :id="'pmo-notes-' + '{{ $item['key'] }}'"
                                                           placeholder="{{ $item['default_note'] }}" 
                                                           :class="checklistNa['{{ $item['key'] }}'] ? 'border-amber-300 bg-amber-50/40 text-amber-900 font-medium' : 'border-slate-300 bg-white text-slate-800'"
                                                           class="w-full text-[11px] px-2 py-1 border rounded outline-none focus:bg-white focus:border-black">
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- 3. CATATAN KHUSUS --}}
                        <div class="border border-black overflow-hidden">
                            <div class="bg-black text-white px-3 py-1.5 font-black text-xs uppercase tracking-wider">
                                CATATAN KHUSUS
                            </div>
                            <div class="p-2.5 bg-white">
                                <textarea x-model="specialNotes" 
                                          rows="2.5" 
                                          placeholder="[Tuliskan jika ada ekspektasi khusus dari customer, janji fitur khusus, atau kendala yang perlu diwaspadai tim delivery]" 
                                          class="w-full px-3 py-1.5 bg-[#F8FAFC] border border-slate-300 rounded text-xs text-slate-800 outline-none focus:bg-white focus:border-black"></textarea>
                            </div>
                        </div>

                        {{-- 4. REVIEW HANDOVER (Persis PDF Halaman 2) --}}
                        <div class="border border-black overflow-hidden">
                            <div class="bg-black text-white px-3 py-1.5 font-black text-xs uppercase tracking-wider">
                                REVIEW HANDOVER
                            </div>
                            <div class="text-xs">
                                <table class="w-full border-collapse border border-black">
                                    <tbody>
                                        <tr class="border-b border-black">
                                            <td class="w-2/5 py-1.5 px-3 font-bold bg-[#F8FAFC] border-r border-black">Isu scope/komersial yang perlu klarifikasi</td>
                                            <td class="p-1.5">
                                                <input type="text" 
                                                       x-model="handoverClarificationIssues" 
                                                       placeholder="Catatan klarifikasi isu scope / komersial..." 
                                                       class="w-full px-2 py-1 bg-white border border-slate-300 rounded text-xs outline-none focus:border-[#8F0A0D]">
                                            </td>
                                        </tr>
                                        <tr class="border-b border-black">
                                            <td class="py-1.5 px-3 font-bold bg-[#F8FAFC] border-r border-black">Asumsi yang harus dikonfirmasi</td>
                                            <td class="p-1.5">
                                                <input type="text" 
                                                       x-model="handoverAssumptions" 
                                                       placeholder="Asumsi teknis, jadwal, atau sumber daya..." 
                                                       class="w-full px-2 py-1 bg-white border border-slate-300 rounded text-xs outline-none focus:border-[#8F0A0D]">
                                            </td>
                                        </tr>
                                        <tr class="border-b border-black">
                                            <td class="py-1.5 px-3 font-bold bg-[#F8FAFC] border-r border-black">Kebutuhan validasi Solution/Technical</td>
                                            <td class="p-1.5">
                                                <input type="text" 
                                                       x-model="handoverTechValidation" 
                                                       placeholder="Kebutuhan validasi teknis arsitektur / BoM..." 
                                                       class="w-full px-2 py-1 bg-white border border-slate-300 rounded text-xs outline-none focus:border-[#8F0A0D]">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="py-1.5 px-3 font-bold bg-[#F8FAFC] border-r border-black">Keputusan yang masih memerlukan persetujuan</td>
                                            <td class="p-1.5">
                                                <input type="text" 
                                                       x-model="handoverPendingDecisions" 
                                                       placeholder="Persetujuan manajemen / customer yang masih pending..." 
                                                       class="w-full px-2 py-1 bg-white border border-slate-300 rounded text-xs outline-none focus:border-[#8F0A0D]">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- 5. HASIL PEMERIKSAAN DAN KEPUTUSAN --}}
                        <div class="border border-black overflow-hidden text-xs">
                            <div class="bg-black text-white px-3 py-1.5 font-black text-xs uppercase tracking-wider">
                                HASIL PEMERIKSAAN DAN KEPUTUSAN
                            </div>
                            
                            {{-- Radio Pilihan Status Handover Persis PDF --}}
                            <div class="flex items-center gap-6 flex-wrap bg-[#F8FAFC] p-2.5 border-b border-black">
                                <span class="font-black text-black text-xs uppercase tracking-wide">Status handover:</span>
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

                            <table class="w-full border-collapse border-b border-black">
                                <tbody>
                                    <tr class="border-b border-black">
                                        <td class="w-2/5 py-1.5 px-3 font-bold bg-[#F8FAFC] border-r border-black">Kekurangan / risiko yang masih terbuka</td>
                                        <td class="p-1.5">
                                            <input type="text" x-model="handoverRisks" placeholder="Catatan risiko atau kekurangan berkas..." class="w-full px-2.5 py-1 bg-white border border-slate-300 rounded text-xs text-slate-800 outline-none focus:border-[#8F0A0D]">
                                        </td>
                                    </tr>
                                    <tr class="border-b border-black">
                                        <td class="py-1.5 px-3 font-bold bg-[#F8FAFC] border-r border-black">PIC tindak lanjut</td>
                                        <td class="p-1.5">
                                            <input type="text" x-model="handoverPicTarget" placeholder="Nama PIC Sales / Delivery tindak lanjut..." class="w-full px-2.5 py-1 bg-white border border-slate-300 rounded text-xs text-slate-800 outline-none focus:border-[#8F0A0D]">
                                        </td>
                                    </tr>
                                    <tr class="border-b border-black">
                                        <td class="py-1.5 px-3 font-bold bg-[#F8FAFC] border-r border-black">Target penyelesaian</td>
                                        <td class="p-1.5">
                                            <input type="text" x-model="handoverMissingInfo" placeholder="Batas waktu penyelesaian kelengkapan (contoh: 2x24 Jam)..." class="w-full px-2.5 py-1 bg-white border border-slate-300 rounded text-xs text-slate-800 outline-none focus:border-[#8F0A0D]">
                                        </td>
                                    </tr>
                                    <tr class="border-b border-black">
                                        <td class="py-1.5 px-3 font-bold bg-[#F8FAFC] border-r border-black">Batasan pekerjaan yang boleh berjalan</td>
                                        <td class="p-1.5">
                                            <input type="text" x-model="handoverWorkLimitations" placeholder="Pekerjaan yang diizinkan berjalan sebelum berkas lengkap..." class="w-full px-2.5 py-1 bg-white border border-slate-300 rounded text-xs text-slate-800 outline-none focus:border-[#8F0A0D]">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="py-1.5 px-3 font-bold bg-[#F8FAFC] border-r border-black">Nomor tiket / WO / dokumen terkait</td>
                                        <td class="p-1.5">
                                            <input type="text" x-model="handoverRelatedDocs" placeholder="Nomor tiket / WO / referensi berkas terkait..." class="w-full px-2.5 py-1 bg-white border border-slate-300 rounded text-xs text-slate-800 outline-none focus:border-[#8F0A0D]">
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            {{-- 6. ATURAN STATUS PERSIS PDF --}}
                            <div class="p-3 bg-[#F8FAFC] text-[11px] text-slate-800 leading-relaxed border-t border-black">
                                <strong class="text-black uppercase tracking-wider block mb-0.5">ATURAN STATUS:</strong>
                                <p>
                                    <strong>Diterima:</strong> informasi minimum cukup untuk memulai perencanaan. | 
                                    <strong>Diterima bersyarat:</strong> kekurangan tercatat, ada PIC dan target penyelesaian, dan pekerjaan yang boleh berjalan dibatasi secara eksplisit. | 
                                    <strong>Ditolak/dikembalikan:</strong> scope, kontrak, atau persyaratan penting belum jelas; jangan memulai pekerjaan terdampak.
                                </p>
                            </div>
                        </div>

                        {{-- 7. PENETAPAN INTERNAL TIM TEKNIS & PM PELAKSANA (Wajib PMO) --}}
                        <div class="p-3.5 bg-blue-50/60 border border-blue-200 rounded-xl space-y-2 text-xs">
                            <div class="text-xs font-bold text-blue-900 flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                <span>Penetapan Tim Teknis Pelaksana &amp; Project Manager (Delivery)</span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1">Tim Teknis Pelaksana <span class="text-red-500">*</span></label>
                                    <select x-model="handoverAssign.division_id" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs font-semibold text-slate-800 outline-none focus:border-[#8F0A0D]">
                                        <option value="">-- Pilih Tim Teknis --</option>
                                        @foreach($divisions ?? [] as $div)
                                            <option value="{{ $div->id }}">{{ preg_replace('/^Divisi\s+/i', 'Tim ', $div->name) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1">Tugaskan Project Manager (PM) <span class="text-red-500">*</span></label>
                                    <select x-model="handoverAssign.pm_id" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs font-semibold text-slate-800 outline-none focus:border-[#8F0A0D]">
                                        <option value="">-- Pilih Project Manager --</option>
                                        @foreach($pmList ?? [] as $pm)
                                            <option value="{{ $pm->id }}">{{ $pm->name }} ({{ $pm->email }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- 8. TANDA TANGAN / PENGESAHAN (4 Kolom Sesuai PDF Halaman 2) --}}
                        <div class="border border-black overflow-hidden text-xs">
                            <div class="grid grid-cols-2 sm:grid-cols-4 divide-x divide-y sm:divide-y-0 divide-black">
                                
                                {{-- 1. Sales / Commercial --}}
                                <div class="p-2.5 bg-white flex flex-col justify-between h-40">
                                    <div class="text-center">
                                        <div class="font-bold text-[11px] text-black">Diserahkan oleh</div>
                                        <div class="text-[10px] text-slate-600 font-medium">Sales / Commercial</div>
                                    </div>
                                    <div class="my-auto py-1 text-center">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[9.5px] font-mono font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            <span>Verified Digital</span>
                                        </span>
                                    </div>
                                    <div class="text-[10px] border-t border-slate-300 pt-1 text-slate-700 space-y-0.5">
                                        <p class="truncate">Nama: <strong class="text-black" x-text="activeProject?.sales_name || '—'"></strong></p>
                                        <p>Tanggal: <span x-text="activeProject?.handover_submitted_at ? activeProject.handover_submitted_at.substring(0, 10) : '{{ date('d/m/Y') }}'"></span></p>
                                        <p>Tanda tangan: <span class="italic text-slate-500">[Digital Approved]</span></p>
                                    </div>
                                </div>

                                {{-- 2. PMO / Project Manager --}}
                                <div class="p-2.5 bg-white flex flex-col justify-between h-40">
                                    <div class="text-center">
                                        <div class="font-bold text-[11px] text-black">Diterima oleh</div>
                                        <div class="text-[10px] text-slate-600 font-medium">PMO / Project Manager</div>
                                    </div>
                                    <div class="my-auto py-1 text-center">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[9.5px] font-mono font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                            <span>PMO Verified</span>
                                        </span>
                                    </div>
                                    <div class="text-[10px] border-t border-slate-300 pt-1 text-slate-700 space-y-0.5">
                                        <p class="truncate">Nama: <strong class="text-black" x-text="activeProject?.pm || 'Tim Delivery PMO'"></strong></p>
                                        <p>Tanggal: {{ date('d/m/Y') }}</p>
                                        <p>Tanda tangan: <span class="italic text-slate-500">[PMO Approved]</span></p>
                                    </div>
                                </div>

                                {{-- 3. TL / Solution --}}
                                <div class="p-2.5 bg-white flex flex-col justify-between h-40">
                                    <div class="text-center">
                                        <div class="font-bold text-[11px] text-black">Validasi teknis oleh</div>
                                        <div class="text-[10px] text-slate-600 font-medium">TL / Solution (bila perlu)</div>
                                    </div>
                                    <div class="my-auto py-1 text-center">
                                        <span class="text-[10px] text-slate-400 italic">Paraf / Approval</span>
                                    </div>
                                    <div class="text-[10px] border-t border-slate-300 pt-1 text-slate-700 space-y-0.5">
                                        <p class="truncate">Nama: <span class="text-black font-semibold">Lead Engineer</span></p>
                                        <p>Tanggal: {{ date('d/m/Y') }}</p>
                                        <p>Tanda tangan: <span class="italic text-slate-400">_______________</span></p>
                                    </div>
                                </div>

                                {{-- 4. Persetujuan / Eskalasi Pihak Berwenang --}}
                                <div class="p-2.5 bg-white flex flex-col justify-between h-40">
                                    <div class="text-center">
                                        <div class="font-bold text-[11px] text-black">Persetujuan / eskalasi pihak berwenang</div>
                                        <div class="text-[10px] text-slate-600 font-medium">(bila perlu)</div>
                                    </div>
                                    <div class="my-auto py-1 text-center">
                                        <span class="text-[10px] text-slate-400 italic">Otorisasi GM / PMO</span>
                                    </div>
                                    <div class="text-[10px] border-t border-slate-300 pt-1 text-slate-700 space-y-0.5">
                                        <p class="truncate">Nama: <span class="text-black font-semibold">PMO Head</span></p>
                                        <p>Tanggal: {{ date('d/m/Y') }}</p>
                                        <p>Tanda tangan: <span class="italic text-slate-400">_______________</span></p>
                                    </div>
                                </div>

                            </div>
                        </div>

                        {{-- Catatan Pengendalian Persis PDF --}}
                        <div class="p-2.5 bg-[#F8FAFC] border border-slate-300 rounded text-[10px] text-slate-700 leading-relaxed italic">
                            <strong>Catatan pengendalian:</strong> handover tidak mengubah kewenangan formal atau tanggung jawab atas output yang telah dibuat. Perubahan scope/komitmen pelanggan harus melalui mekanisme persetujuan yang berlaku.
                        </div>

                    </div>
                </div>

                {{-- Modal Footer Actions Persis Gambar 2 --}}
                <div class="px-6 py-4 border-t border-[#E2E8F0] bg-white flex flex-wrap items-center justify-between gap-2.5 shrink-0 no-print">
                    <button type="button" @click="handoverReviewModalOpen = false" class="px-4 py-2 border border-[#CBD5E1] rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-50 transition cursor-pointer">
                        Tutup
                    </button>

                    <div class="flex items-center gap-2">
                        {{-- Tombol sesuai status radio yang dipilih --}}
                        <template x-if="handoverStatusSelected === 'conditional'">
                            <button type="button" 
                                    @click="handleConditionalClick()" 
                                    class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-xs font-bold shadow-xs transition cursor-pointer flex items-center gap-1.5 active:scale-95">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                <span>Simpan Status Diterima Bersyarat (2x24 Jam)</span>
                            </button>
                        </template>

                        <template x-if="handoverStatusSelected === 'rejected'">
                            <button type="button" 
                                    @click="handleRejectClick()" 
                                    class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold shadow-xs transition cursor-pointer flex items-center gap-1.5 active:scale-95">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                <span>Kembalikan ke Sales (Ditolak)</span>
                            </button>
                        </template>

                        <template x-if="handoverStatusSelected === 'approved'">
                            <button type="button" 
                                    @click="approveHandover()" 
                                    class="px-5 py-2 bg-[#8F0A0D] hover:bg-[#73080A] text-white rounded-xl text-xs font-bold shadow-xs transition cursor-pointer flex items-center gap-1.5 active:scale-95">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                <span>Sahkan Serah Terima (Handover Approved)</span>
                            </button>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </template>

    {{-- MODAL SERAH TERIMA PM KE MANAGED SERVICE (PERSIS TATA LETAK GAMBAR 2) --}}
    <template x-teleport="body">
        <div x-show="handoverToMsModalOpen" 
             x-cloak 
             class="fixed inset-0 z-50 bg-[#0F172A]/60 backdrop-blur-xs flex items-center justify-center p-4 overflow-y-auto">
            <div class="bg-white rounded-2xl max-w-lg w-full shadow-2xl border border-[#E2E8F0] max-h-[90vh] flex flex-col overflow-hidden my-auto" 
                 @click.away="handoverToMsModalOpen = false">
                
                <!-- Fixed Header (Persis Tata Letak Gambar 2) -->
                <div class="px-6 py-4 border-b border-[#E2E8F0] flex items-center justify-between bg-[#F8FAFC] shrink-0">
                    <div class="min-w-0 pr-3">
                        <h3 class="text-[16px] font-bold text-[#1E293B]">Serah Terima ke Managed Service</h3>
                        <p class="text-[11.5px] text-[#64748B] mt-0.5 truncate" x-text="activeProject?.name || '-'"></p>
                    </div>
                    <button type="button" @click="handoverToMsModalOpen = false" class="text-[#64748B] hover:text-[#1E293B] p-1.5 rounded-lg hover:bg-[#E2E8F0] transition cursor-pointer shrink-0" title="Tutup">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Scrollable Body (Grid 2 Kolom Persis Gambar 2) -->
                <div class="p-5 sm:p-6 overflow-y-auto space-y-4 text-[12.5px] flex-1">
                    
                    <!-- Klien & Nomor SO (2 Kolom Sejajar) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div>
                            <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">
                                Klien / Pelanggan
                            </label>
                            <div class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-semibold text-[#1E293B] truncate" x-text="activeProject?.client || '-'"></div>
                        </div>
                        <div>
                            <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">
                                Nomor Kontrak / SO
                            </label>
                            <div class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-mono font-bold text-[#8F0A0D]" x-text="activeProject?.so_code || activeProject?.po_number || '-'"></div>
                        </div>
                    </div>

                    <!-- SLA Kontrak & Siklus Maintenance (2 Kolom Sejajar) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div>
                            <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">
                                SLA Kontrak <span class="text-[#94A3B8] font-normal lowercase">(dari sales)</span>
                            </label>
                            <div class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-semibold text-[#1E293B] flex items-center gap-1.5">
                                <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200" x-text="activeProject?.sla_tier || 'Gold'"></span>
                                <span class="text-slate-400">&bull;</span>
                                <span x-text="activeProject?.sla_coverage_hours || '24x7 Non-Stop'"></span>
                            </div>
                        </div>
                        <div>
                            <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">
                                Frekuensi Maintenance
                            </label>
                            <div class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-semibold text-[#1E293B]" x-text="activeProject?.maintenance_frequency || 'Bulanan (Monthly)'"></div>
                        </div>
                    </div>

                    <!-- Box Konfirmasi Penyerahan (Persis Box Dashed Gambar 2) -->
                    <div class="w-full p-3.5 rounded-xl border border-dashed border-[#CBD5E1] bg-[#F8FAFC] flex items-center gap-3 shadow-2xs">
                        <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="font-bold text-[#1E293B] text-[12px]">Pekerjaan Implementasi Selesai (Deliver)</div>
                            <div class="text-[10.5px] text-[#64748B] mt-0.5">Tanggung jawab operasional proyek ini siap dialihkan ke tim Managed Service.</div>
                        </div>
                    </div>

                    <!-- Project Manager Penyerah -->
                    <div>
                        <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">
                            Diserahkan oleh Project Manager
                        </label>
                        <div class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-semibold text-[#1E293B]" x-text="(activeProject?.pm || 'Rizki') + ' (Project Manager)'"></div>
                    </div>
                </div>

                <!-- Fixed Footer (Persis Gambar 2) -->
                <div class="flex items-center justify-end gap-2.5 p-4 px-6 border-t border-[#E2E8F0] bg-[#F8FAFC] shrink-0">
                    <button type="button" @click="handoverToMsModalOpen = false" 
                            class="px-4 py-2.5 text-[12.5px] font-bold text-[#475569] hover:bg-[#F1F5F9] border border-[#CBD5E1] rounded-xl transition cursor-pointer">
                        Batal
                    </button>
                    <button type="button" @click="submitHandoverToMs()" 
                            class="btn-ipnet-gradient px-5 py-2.5 text-[12.5px] font-bold rounded-xl shadow-md cursor-pointer transition active:scale-95">
                        Serahkan ke Managed Service
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
            projectTypeCategory: 'Implementasi',
            projectTypeOther: '',
            docStorageLocation: '',
            handoverClarificationIssues: '',
            handoverAssumptions: '',
            handoverTechValidation: '',
            handoverPendingDecisions: '',
            handoverRelatedDocs: '',
            checklistStatus: {},
            checklistNa: {},
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
                handover_status_selected: 'approved',
                service_acceptance: '',
                risks_and_pic: '',
                follow_up_date: '',
                confirmation_notes: '',
                checklist_status: {},
                checklist_na: {},
                checklist_notes: {},
            },
            msChecklistItems: [
                { no: 1, key: 'ruang_lingkup_layanan', name: 'Ruang lingkup layanan dan batasan', doc_key: 'tor', default_note: 'Sesuai batasan implementasi & SLA' },
                { no: 2, key: 'dokumentasi_as_built', name: 'Dokumentasi as-built / desain tersedia', doc_key: 'project_doc', default_note: 'As-built drawing & konfigurasi final' },
                { no: 3, key: 'daftar_perangkat_lokasi', name: 'Daftar perangkat, lokasi, dan dependensi tersedia', doc_key: 'boq', default_note: 'Daftar serial number, rak & IP address' },
                { no: 4, key: 'hasil_testing_status', name: 'Hasil testing dan status teknis terdokumentasi', doc_key: 'bast', default_note: 'Berita acara UAT / testing acceptance' },
                { no: 5, key: 'daftar_topologi', name: 'Daftar Topologi (HLD/LLD, Denah, As build dll)', doc_key: 'topology', default_note: 'Diagram topologi L2/L3 & layout denah' },
                { no: 6, key: 'daftar_masalah_terbuka', name: 'Daftar masalah / pekerjaan terbuka diserahkan', doc_key: 'punchlist', default_note: 'Daftar punchlist & catatan garansi' },
                { no: 7, key: 'kontak_pelanggan_eskalasi', name: 'Kontak pelanggan dan jalur eskalasi tersedia', doc_key: 'escalation_matrix', default_note: 'PIC operasional, level 1-3 eskalasi' },
                { no: 8, key: 'sla_jam_layanan', name: 'SLA / jam layanan sesuai kontrak dikomunikasikan', doc_key: 'contract', default_note: 'Target respons & MTTR penanganan insiden' },
                { no: 9, key: 'monitoring_akses_operasional', name: 'Monitoring / akses operasional siap sesuai kebijakan', doc_key: 'project_doc', default_note: 'Kredensial NMS, SNMP / VPN access operasional' },
            ],

            openHandoverToMsModal(project) {
                this.activeProject = project;
                this.msHandoverForm.sla_tier = project.sla_tier || 'Gold';
                this.msHandoverForm.sla_coverage_hours = project.sla_coverage_hours || '24x7';
                this.msHandoverForm.maintenance_frequency = project.maintenance_frequency || 'Monthly';
                this.msHandoverForm.service_start_date = project.service_start_date || '{{ date('Y-m-d') }}';
                this.msHandoverForm.service_end_date = project.service_end_date || '';
                this.msHandoverForm.special_notes = project.special_notes || '';
                this.msHandoverForm.handover_status_selected = 'approved';
                this.msHandoverForm.service_acceptance = 'Layanan implementasi telah rampung dan siap dialihkan ke tim pemeliharaan berkala.';
                this.msHandoverForm.risks_and_pic = '';
                this.msHandoverForm.follow_up_date = '';
                this.msHandoverForm.confirmation_notes = 'Penyerahan tanggung jawab operasional dari Project Manager kepada Managed Service.';

                const hData = (typeof project.handover_data === 'object' && project.handover_data) ? (project.handover_data.ms_handover || {}) : {};
                if (hData.service_acceptance) this.msHandoverForm.service_acceptance = hData.service_acceptance;
                if (hData.risks_and_pic) this.msHandoverForm.risks_and_pic = hData.risks_and_pic;
                if (hData.follow_up_date) this.msHandoverForm.follow_up_date = hData.follow_up_date;
                if (hData.confirmation_notes) this.msHandoverForm.confirmation_notes = hData.confirmation_notes;
                if (hData.handover_status_selected) this.msHandoverForm.handover_status_selected = hData.handover_status_selected;

                this.msHandoverForm.checklist_status = {};
                this.msHandoverForm.checklist_na = {};
                this.msHandoverForm.checklist_notes = {};

                const savedStatus = hData.checklist_status || {};
                const savedNas = hData.checklist_na || {};
                const savedNotes = hData.checklist_notes || {};

                this.msChecklistItems.forEach(item => {
                    const k = item.key;
                    this.msHandoverForm.checklist_status[k] = savedStatus[k] || 'Ada';
                    this.msHandoverForm.checklist_na[k] = !!savedNas[k];
                    this.msHandoverForm.checklist_notes[k] = savedNotes[k] || item.default_note;
                });

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
                        if (typeof Swal !== 'undefined') {
                            await Swal.fire({
                                icon: 'success',
                                title: 'Berhasil Diserahkan!',
                                text: data.message || 'Proyek berhasil diserahkan ke Managed Service!',
                                confirmButtonColor: '#8F0A0D',
                                confirmButtonText: 'OK, Lanjutkan'
                            });
                        }
                        window.location.reload();
                    } else {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal Menyerahkan',
                                text: data.message || 'Gagal menyerahkan ke Managed Service.',
                                confirmButtonColor: '#8F0A0D'
                            });
                        } else {
                            alert(data.message || 'Gagal menyerahkan ke Managed Service.');
                        }
                    }
                } catch (e) {
                    console.error(e);
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Kesalahan Sistem',
                            text: 'Terjadi kesalahan jaringan atau server saat memproses data.',
                            confirmButtonColor: '#8F0A0D'
                        });
                    } else {
                        alert('Terjadi kesalahan jaringan.');
                    }
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
                this.projectTypeCategory = hData.project_type_category || 'Implementasi';
                this.projectTypeOther = hData.project_type_other || '';
                this.docStorageLocation = hData.doc_storage_location || '';
                this.handoverClarificationIssues = hData.handover_clarification_issues || '';
                this.handoverAssumptions = hData.handover_assumptions || '';
                this.handoverTechValidation = hData.handover_tech_validation || '';
                this.handoverPendingDecisions = hData.handover_pending_decisions || '';
                this.handoverRelatedDocs = hData.handover_related_docs || (project.so_code || project.po_number || '');

                this.handoverRisks = hData.handover_risks || '';
                this.handoverMissingInfo = hData.handover_missing_info || project.handover_conditional_notes || '';
                this.handoverPicTarget = hData.handover_pic_target || '';
                this.handoverWorkLimitations = hData.handover_work_limitations || '';
                this.specialNotes = project.special_notes || hData.special_notes || '';
                this.conditionalNotes = project.handover_conditional_notes || '';

                // Inisialisasi 14 butir checklist dari handoverFormStructure
                const savedDocs = project.documents_checklist || hData.checklist || {};
                const savedStatus = hData.checklist_status || {};
                const savedNas = hData.checklist_na || {};
                const savedNotes = hData.checklist_notes || {};

                this.activeDocs = {};
                this.checklistStatus = {};
                this.checklistNa = {};
                this.checklistNotes = {};

                (this.handoverFormStructure || []).forEach(item => {
                    const k = item.key;
                    const dk = item.doc_key || 'project_doc';
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

                    const isNa = !!savedNas[k];
                    this.checklistNa[k] = isNa;
                    const rawStatus = savedStatus[k];
                    this.checklistStatus[k] = isNa ? 'Tidak' : ((rawStatus === 'Tidak' || rawStatus === 'Tidak Berlaku') ? 'Tidak' : (rawStatus === 'Ada' || rawStatus === 'Ya' || this.activeDocs[k] ? 'Ada' : 'Ada'));
                    this.checklistNotes[k] = savedNotes[k] || (docObj?.number ? ('No: ' + docObj.number) : '');
                });

                this.handoverReviewModalOpen = true;
            },

            toggleNa(k) {
                if (this.checklistNa[k]) {
                    this.checklistStatus[k] = 'Tidak';
                    this.$nextTick(() => {
                        const el = document.getElementById('pmo-notes-' + k);
                        if (el) el.focus();
                    });
                } else {
                    this.checklistStatus[k] = 'Ada';
                }
            },

            printHandoverForm() {
                window.print();
            },

            async approveHandover() {
                if (!this.handoverAssign.pm_id || !this.handoverAssign.division_id) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Penetapan Belum Lengkap',
                            text: 'Harap tentukan Project Manager (PM) dan Tim Teknis Pelaksana terlebih dahulu.',
                            confirmButtonColor: '#8F0A0D'
                        });
                    } else {
                        alert('Harap tentukan Project Manager (PM) dan Tim Teknis Pelaksana terlebih dahulu.');
                    }
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
                            checklist_na: this.checklistNa,
                            checklist_notes: this.checklistNotes,
                            handover_status_selected: this.handoverStatusSelected,
                            project_type_category: this.projectTypeCategory,
                            project_type_other: this.projectTypeOther,
                            doc_storage_location: this.docStorageLocation,
                            handover_clarification_issues: this.handoverClarificationIssues,
                            handover_assumptions: this.handoverAssumptions,
                            handover_tech_validation: this.handoverTechValidation,
                            handover_pending_decisions: this.handoverPendingDecisions,
                            handover_related_docs: this.handoverRelatedDocs,
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
                        if (typeof Swal !== 'undefined') {
                            await Swal.fire({
                                icon: 'success',
                                title: 'Handover Berhasil Disahkan!',
                                text: data.message || 'Proyek resmi masuk ke tahap Deliver dan siap didelegasikan oleh Lead Engineer.',
                                confirmButtonColor: '#8F0A0D',
                                confirmButtonText: 'OK, Lanjutkan'
                            });
                        }
                        window.location.reload();
                    } else {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal Mengesahkan',
                                text: data.message || 'Gagal mengesahkan handover.',
                                confirmButtonColor: '#8F0A0D'
                            });
                        } else {
                            alert(data.message || 'Gagal mengesahkan handover.');
                        }
                    }
                } catch (e) {
                    console.error(e);
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Kesalahan Sistem',
                            text: 'Terjadi kesalahan jaringan atau server saat memproses data.',
                            confirmButtonColor: '#8F0A0D'
                        });
                    } else {
                        alert('Terjadi kesalahan jaringan.');
                    }
                }
            },

            async handleConditionalClick() {
                const notes = this.handoverMissingInfo || this.conditionalNotes || this.handoverRisks;
                if (!notes || !notes.trim()) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Catatan Belum Diisi',
                            text: 'Harap isi catatan kekurangan berkas atau informasi yang belum tersedia pada Section Hasil Pemeriksaan.',
                            confirmButtonColor: '#8F0A0D'
                        });
                    } else {
                        alert('Harap isi catatan kekurangan berkas atau informasi yang belum tersedia pada Section Hasil Pemeriksaan.');
                    }
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
                            checklist_na: this.checklistNa,
                            checklist_notes: this.checklistNotes,
                            project_type_category: this.projectTypeCategory,
                            project_type_other: this.projectTypeOther,
                            doc_storage_location: this.docStorageLocation,
                            handover_clarification_issues: this.handoverClarificationIssues,
                            handover_assumptions: this.handoverAssumptions,
                            handover_tech_validation: this.handoverTechValidation,
                            handover_pending_decisions: this.handoverPendingDecisions,
                            handover_related_docs: this.handoverRelatedDocs,
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
                        if (typeof Swal !== 'undefined') {
                            await Swal.fire({
                                icon: 'info',
                                title: 'Diterima Bersyarat',
                                text: data.message || 'Status handover diterima bersyarat berhasil dicatat.',
                                confirmButtonColor: '#8F0A0D',
                                confirmButtonText: 'OK, Lanjutkan'
                            });
                        }
                        window.location.reload();
                    } else {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal Menyimpan',
                                text: data.message || 'Gagal menyimpan status bersyarat.',
                                confirmButtonColor: '#8F0A0D'
                            });
                        } else {
                            alert(data.message || 'Gagal menyimpan status bersyarat.');
                        }
                    }
                } catch (e) {
                    console.error(e);
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Kesalahan Sistem',
                            text: 'Terjadi kesalahan jaringan atau server saat memproses data.',
                            confirmButtonColor: '#8F0A0D'
                        });
                    } else {
                        alert('Terjadi kesalahan jaringan.');
                    }
                }
            },

            async handleRejectClick() {
                const reason = this.handoverRisks || this.handoverMissingInfo || 'Persyaratan dan kelengkapan dokumen belum memenuhi standar.';
                
                let isConfirmed = false;
                if (typeof Swal !== 'undefined') {
                    const result = await Swal.fire({
                        title: 'Tolak & Kembalikan?',
                        text: 'Apakah Anda yakin ingin menolak berkas serah terima ini dan mengembalikannya ke Sales?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#8F0A0D',
                        cancelButtonColor: '#64748B',
                        confirmButtonText: 'Ya, Tolak & Kembalikan',
                        cancelButtonText: 'Batal'
                    });
                    isConfirmed = result.isConfirmed;
                } else {
                    isConfirmed = confirm('Apakah Anda yakin ingin menolak berkas serah terima ini dan mengembalikannya ke Sales?');
                }

                if (!isConfirmed) {
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
                            checklist_na: this.checklistNa,
                            checklist_notes: this.checklistNotes,
                            project_type_category: this.projectTypeCategory,
                            project_type_other: this.projectTypeOther,
                            doc_storage_location: this.docStorageLocation,
                            handover_clarification_issues: this.handoverClarificationIssues,
                            handover_assumptions: this.handoverAssumptions,
                            handover_tech_validation: this.handoverTechValidation,
                            handover_pending_decisions: this.handoverPendingDecisions,
                            handover_related_docs: this.handoverRelatedDocs,
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
                        if (typeof Swal !== 'undefined') {
                            await Swal.fire({
                                icon: 'success',
                                title: 'Berkas Dikembalikan',
                                text: data.message || 'Berkas serah terima berhasil dikembalikan ke Sales.',
                                confirmButtonColor: '#8F0A0D',
                                confirmButtonText: 'OK'
                            });
                        }
                        window.location.reload();
                    } else {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal Mengembalikan',
                                text: data.message || 'Gagal mengembalikan berkas serah terima.',
                                confirmButtonColor: '#8F0A0D'
                            });
                        } else {
                            alert(data.message || 'Gagal mengembalikan berkas serah terima.');
                        }
                    }
                } catch (e) {
                    console.error(e);
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Kesalahan Sistem',
                            text: 'Terjadi kesalahan jaringan atau server saat memproses data.',
                            confirmButtonColor: '#8F0A0D'
                        });
                    } else {
                        alert('Terjadi kesalahan jaringan.');
                    }
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
                            checklist_na: this.checklistNa,
                            checklist_notes: this.checklistNotes,
                            special_notes: this.specialNotes
                        })
                    });

                    const data = await response.json();
                    if (response.ok && data.success) {
                        this.handoverReviewModalOpen = false;
                        window.location.reload();
                    } else {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal Menyimpan',
                                text: data.message || 'Gagal menyimpan checklist dokumen.',
                                confirmButtonColor: '#8F0A0D'
                            });
                        } else {
                            alert(data.message || 'Gagal menyimpan checklist dokumen.');
                        }
                    }
                } catch (e) {
                    console.error(e);
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Kesalahan Sistem',
                            text: 'Terjadi kesalahan jaringan atau server saat memproses data.',
                            confirmButtonColor: '#8F0A0D'
                        });
                    } else {
                        alert('Terjadi kesalahan jaringan.');
                    }
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
