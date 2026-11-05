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
                    <div class="min-w-0 flex-1 pr-0 lg:pr-4">
                        <p class="text-[#8F0A0D] text-[12px] font-bold inline-flex items-center uppercase tracking-wider">
                            <span class="w-2 h-2 rounded-full bg-[#8F0A0D] inline-block mr-2"></span> MANAJEMEN PORTOFOLIO &amp; PROJECT DELIVERY
                        </p>
                        <h2 class="text-[20px] font-bold text-[#1E293B] tracking-tight">Daftar Proyek &amp; Project Delivery</h2>
                        <p class="text-[13px] text-[#64748B] mt-0.5 leading-relaxed">Kelola pelaksanaan proyek aktif, verifikasi serah terima (handover) dari Sales, dan kontrol implementasi lapangan</p>
                    </div>

                    {{-- Action Button (Buat Proyek Baru) --}}
                    <div class="flex items-center gap-3 shrink-0 self-start lg:self-center">
                        <button type="button" 
                                @click="showCreateProjectModal = true"
                                class="h-10 px-4 rounded-xl btn-ipnet-primary text-xs font-bold shadow-xs transition-all flex items-center justify-center gap-2 cursor-pointer hover:scale-[1.02] active:scale-95 whitespace-nowrap shrink-0">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span>Buat Proyek Baru</span>
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
                    
                    <select x-model="activeTab" @change="currentPage = 1" 
                            class="w-full sm:w-56 px-3 py-2.5 rounded-xl border border-[#CBD5E1] text-[12.5px] font-semibold text-[#1E293B] bg-white outline-none hover:border-[#94A3B8] focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition-all shadow-xs cursor-pointer">
                        <option value="all">Projek Aktif</option>
                        <option value="pending_handover">Incoming Handover</option>
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
                            x-show="search || activeTab !== 'all' || selectedDivision !== 'all' || selectedPm !== 'all'"
                            @click="search = ''; activeTab = 'all'; selectedDivision = 'all'; selectedPm = 'all'; currentPage = 1;"
                            class="px-3.5 py-2.5 text-[12px] font-bold text-[#64748B] hover:text-[#8F0A0D] bg-[#F8FAFC] hover:bg-[#FEF2F2] border border-[#E2E8F0] hover:border-[#FCA5A5] rounded-xl transition cursor-pointer">
                        Reset Filter
                    </button>
                </div>
            </div>




            {{-- ALERT BANNER KETIKA ADA INCOMING HANDOVER MENUNGGU PERSETUJUAN --}}
            <template x-if="activeTab !== 'pending_handover' && (handoverPendingCount + handoverConditionalCount) > 0">
                <div class="p-4 rounded-2xl bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200/90 shadow-2xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 anim-fade-up">
                    <div class="flex items-center gap-3">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-ping shrink-0"></span>
                        <div>
                            <h4 class="text-xs font-bold text-amber-950 flex items-center gap-2">
                                <span>Ada <span x-text="handoverPendingCount + handoverConditionalCount"></span> Berkas Serah Terima Baru Masuk dari Sales!</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-200 text-amber-900">Menunggu TTD &amp; Verifikasi</span>
                            </h4>
                            <p class="text-[11px] text-amber-800 mt-0.5">
                                Berkas handover telah ditandatangani digital oleh Sales PIC. Buka tab Incoming Handover untuk verifikasi &amp; tanda tangani persetujuan PMO.
                            </p>
                        </div>
                    </div>
                    <button type="button" 
                            @click="activeTab = 'pending_handover'; currentPage = 1;" 
                            class="px-4 py-2 bg-[#8F0A0D] hover:bg-[#73080A] text-white rounded-xl text-xs font-bold shadow-xs transition cursor-pointer flex items-center gap-1.5 self-start sm:self-auto shrink-0 active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <span>Buka Incoming Handover</span>
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
                            <tr class="border-b border-[#E2E8F0] bg-[#F8FAFC] text-[11px] font-bold text-[#64748B] uppercase tracking-wider">
                                <th class="py-3.5 px-4 sm:px-6 w-[28%]">PROYEK</th>
                                <th class="py-3.5 px-4 w-[26%]">KLIEN &amp; PIC KLIEN</th>
                                <th class="py-3.5 px-4 w-[18%]">TIM TEKNIS</th>
                                <th class="py-3.5 px-4 w-[16%]">TENGGAT</th>
                                <th class="py-3.5 px-4 sm:px-6 text-right w-[12%]">TINDAKAN</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#F1F5F9] font-medium text-[#1E293B]">
                            <template x-for="project in paginatedProjects" :key="project.id">
                                <tr class="hover:bg-[#F8FAFC]/80 transition-colors" :class="project.handover_status === 'Submitted' ? 'bg-amber-50/20' : ''">
                                    
                                    {{-- Kolom 1: Proyek --}}
                                    <td class="py-3.5 px-4 sm:px-6 align-middle">
                                        <div class="max-w-xs space-y-1">
                                            <a :href="'/pmo/projects/' + project.id" class="font-bold text-[#1E293B] hover:text-[#8F0A0D] text-[13px] leading-snug block transition" :title="project.name">
                                                <span x-text="project.name"></span>
                                            </a>
                                            <div class="flex items-center gap-2 text-[11px] text-[#64748B] flex-wrap">
                                                <span class="font-bold text-[#8F0A0D]" x-text="'Sales: ' + (project.sales_name || 'Raiza')"></span>
                                                <template x-if="project.so_code || project.po_number">
                                                    <span class="flex items-center gap-1.5">
                                                        <span class="text-slate-300">&bull;</span>
                                                        <span class="font-mono text-[10.5px] text-slate-500" x-text="project.so_code || project.po_number"></span>
                                                    </span>
                                                </template>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Kolom 2: Klien & PIC Klien (Pendamping Diskusi PM) --}}
                                    <td class="py-3.5 px-4 align-middle">
                                        <div class="space-y-1 max-w-xs">
                                            <div class="font-bold text-[#1E293B] text-[12px] truncate" :title="project.client" x-text="project.client || '-'"></div>
                                            <div class="text-[11px] text-[#64748B] flex items-start gap-1.5">
                                                <span class="text-slate-400 font-semibold shrink-0">PIC:</span>
                                                <span class="text-slate-700 leading-snug line-clamp-2" 
                                                      x-text="project.customer_pic && project.customer_pic !== '-' ? project.customer_pic : 'Belum Ditentukan'">
                                                </span>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Kolom 3: Tim Teknis & PM --}}
                                    <td class="py-3.5 px-4 align-middle whitespace-nowrap">
                                        <div class="font-bold text-[#1E293B] text-[12px]" x-text="project.division_name || project.division || 'Tim Network & Security'"></div>
                                        <div class="text-[11px] text-[#64748B] flex items-center gap-1 mt-0.5">
                                            <span>PM:</span>
                                            <span class="font-semibold text-slate-700" x-text="project.pm || 'Rizki'"></span>
                                        </div>
                                    </td>

                                    {{-- Kolom 4: Tenggat --}}
                                    <td class="py-3.5 px-4 align-middle whitespace-nowrap">
                                        <div class="space-y-1.5">
                                            <div class="flex items-center gap-2">
                                                <span class="font-semibold text-slate-700 text-xs" x-text="project.deadline ? project.deadline : '30 Okt 2027'"></span>
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold"
                                                      :class="{
                                                          'bg-emerald-50 text-emerald-700 border border-emerald-200': project.health_status === 'On-Track' || !project.health_status,
                                                          'bg-rose-50 text-rose-700 border border-rose-200': project.health_status === 'Delayed',
                                                          'bg-amber-50 text-amber-800 border border-amber-200': project.health_status === 'At-Risk'
                                                      }">
                                                    <span class="w-1.5 h-1.5 rounded-full"
                                                          :class="{
                                                              'bg-emerald-500': project.health_status === 'On-Track' || !project.health_status,
                                                              'bg-rose-500': project.health_status === 'Delayed',
                                                              'bg-amber-500 animate-pulse': project.health_status === 'At-Risk'
                                                          }"></span>
                                                    <span x-text="project.health_status === 'Delayed' ? 'Melewati Tenggat' : (project.health_status === 'At-Risk' ? 'Mendekati Tenggat' : 'Sesuai Jadwal')"></span>
                                                </span>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Kolom 5: Tindakan --}}
                                    <td class="py-3.5 px-4 sm:px-6 align-middle text-right whitespace-nowrap">
                                        <div class="flex items-center justify-end gap-2">
                                            {{-- Handover MS: Hanya muncul jika proyek memang memiliki alur Managed Service --}}
                                            <template x-if="project.has_ms && project.stage === 'Deliver' && (!project.ms_handover_status || project.ms_handover_status === 'Pending')">
                                                <button type="button"
                                                        @click="openHandoverToMsModal(project)"
                                                        class="px-3 py-1.5 bg-[#8F0A0D] hover:bg-[#73080A] text-white rounded-lg text-[11.5px] font-bold transition shadow-xs cursor-pointer flex items-center gap-1 active:scale-95">
                                                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    <span>Handover MS</span>
                                                </button>
                                            </template>

                                            {{-- Verifikasi Dokumen Incoming --}}
                                            <template x-if="project.handover_status === 'Submitted' || project.handover_status === 'Conditional'">
                                                <button type="button"
                                                        @click="openHandoverReviewModal(project)"
                                                        class="px-3 py-1.5 bg-[#8F0A0D] hover:bg-[#73080A] text-white rounded-lg text-[11.5px] font-bold transition shadow-xs cursor-pointer flex items-center gap-1 active:scale-95">
                                                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    <span>Verifikasi</span>
                                                </button>
                                            </template>

                                            {{-- Detail Button (Konsisten outline persis Sales & MS) --}}
                                            <a :href="'/pmo/projects/' + project.id" 
                                               class="px-3 py-1.5 rounded-lg border border-[#CBD5E1] text-[#1E293B] hover:bg-[#F8FAFC] text-[11px] font-bold inline-flex items-center gap-1 transition shadow-2xs">
                                                <span>Detail</span>
                                                <svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
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
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                            </svg>
                                        </div>
                                        <div class="text-sm font-bold text-slate-700" x-text="activeTab === 'pending_handover' ? 'Tidak ada berkas handover yang menunggu review' : 'Tidak ada data proyek implementasi'"></div>
                                        <div class="text-xs text-slate-400 mt-1" x-text="activeTab === 'pending_handover' ? 'Semua berkas handover dari tim sales telah selesai ditinjau.' : 'Coba sesuaikan kata kunci pencarian atau filter status di atas.'"></div>
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
                                                <template x-if="project.has_ms && project.stage === 'Deliver' && (!project.ms_handover_status || project.ms_handover_status === 'Pending')">
                                                    <button type="button"
                                                            @click="openHandoverToMsModal(project)"
                                                            class="px-3 py-1.5 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-bold transition shadow-xs cursor-pointer active:scale-95">
                                                        <span>Handover MS</span>
                                                    </button>
                                                </template>

                                                <a :href="'/pmo/projects/' + project.id" 
                                                   title="Buka Dashboard PMO & Perhitungan Proyek"
                                                   class="px-3 py-1.5 rounded-lg border border-[#CBD5E1] text-[#1E293B] hover:bg-[#F8FAFC] text-[11px] font-bold inline-flex items-center gap-1 transition shadow-2xs">
                                                    <span>Detail</span>
                                                    <svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
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

                        {{-- Kontrol Pilihan Format Tanda Tangan Digital Resmi (Persis Referensi) --}}
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-3 bg-slate-50 border border-black text-xs no-print">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="w-2 h-2 rounded-full bg-[#8F0A0D]"></span>
                                <span class="font-bold text-slate-900 uppercase tracking-wide">FORMAT STEMPEL SAYA (PMO / PM):</span>
                                <span class="text-[11px] text-slate-500 italic">(Stempel Sales telah disahkan &amp; terkunci secara kriptografis)</span>
                            </div>
                            <div class="inline-flex rounded-lg bg-white p-1 border border-slate-300 shadow-2xs gap-1">
                                <button type="button" 
                                        @click="signatureMode = 'signature_qr'" 
                                        :class="signatureMode === 'signature_qr' ? 'bg-[#8F0A0D] text-white shadow-2xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100'"
                                        class="px-2.5 py-1 rounded text-xs font-bold transition flex items-center gap-1.5 cursor-pointer">
                                    <span>TTD + QR Code (Resmi)</span>
                                </button>
                                <button type="button" 
                                        @click="signatureMode = 'signature_only'" 
                                        :class="signatureMode === 'signature_only' ? 'bg-[#8F0A0D] text-white shadow-2xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100'"
                                        class="px-2.5 py-1 rounded text-xs font-bold transition flex items-center gap-1.5 cursor-pointer">
                                    <span>Tanda Tangan Saja</span>
                                </button>
                                <button type="button" 
                                        @click="signatureMode = 'qr_only'" 
                                        :class="signatureMode === 'qr_only' ? 'bg-[#8F0A0D] text-white shadow-2xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100'"
                                        class="px-2.5 py-1 rounded text-xs font-bold transition flex items-center gap-1.5 cursor-pointer">
                                    <span>QR Code Saja</span>
                                </button>
                            </div>
                        </div>
                        <input type="hidden" name="signature_mode" :value="signatureMode">

                        {{-- 8. TANDA TANGAN / PENGESAHAN (4 Kolom Sesuai PDF Halaman 2) --}}
                        <div class="border border-black overflow-hidden text-xs">
                            <div class="grid grid-cols-2 sm:grid-cols-4 divide-x divide-y sm:divide-y-0 divide-black">
                                
                                {{-- 1. Sales / Commercial --}}
                                <div class="p-2.5 bg-white flex flex-col justify-between min-h-[160px]">
                                    <div class="text-center">
                                        <div class="font-bold text-[11px] text-black">Diserahkan oleh</div>
                                        <div class="text-[10px] text-slate-600 font-medium">Sales / Commercial</div>
                                    </div>
                                    <div class="my-auto py-2 text-center flex items-center justify-center">
                                        <div class="inline-flex items-center gap-2 select-none text-left align-middle">
                                            {{-- QR Code Box (Terkunci sesuai pilihan Sales, tidak berubah saat PM memilih mode) --}}
                                            <div x-show="(activeProject?.handover_data?.sales_signature?.type || 'signature_qr') !== 'signature_only'" 
                                                 class="shrink-0 p-1 bg-white border border-slate-300 rounded flex items-center justify-center shadow-2xs overflow-hidden [&>svg]:!w-full [&>svg]:!h-full [&>svg]:block" 
                                                 style="width: 54px; height: 54px; min-width: 54px; min-height: 54px; max-width: 54px; max-height: 54px;">
                                                <template x-if="activeProject?.handover_data?.sales_signature?.qr_svg">
                                                    <div class="w-full h-full flex items-center justify-center" x-html="activeProject.handover_data.sales_signature.qr_svg"></div>
                                                </template>
                                                <template x-if="!activeProject?.handover_data?.sales_signature?.qr_svg">
                                                    <img :src="'https://api.qrserver.com/v1/create-qr-code/?size=80x80&data=' + encodeURIComponent('https://dsign.ipnet.id/' + (activeProject?.handover_data?.sales_signature?.qr_token || 'COMMERCIAL'))" alt="QR Sales" class="w-full h-full object-contain">
                                                </template>
                                            </div>
                                            {{-- Framed Stroke Box (Terkunci sesuai pilihan Sales) --}}
                                            <div x-show="(activeProject?.handover_data?.sales_signature?.type || 'signature_qr') !== 'qr_only'" 
                                                 class="relative px-2 pt-1.5 pb-1 border border-slate-700 bg-white rounded flex flex-col justify-between shadow-2xs shrink-0" 
                                                 style="width: 130px; min-width: 120px; max-width: 135px; height: 54px; min-height: 54px; max-height: 54px;">
                                                <div class="absolute -top-2 left-1.5 px-1 bg-white text-[7.5px] font-mono font-bold text-slate-700 tracking-wide flex items-center gap-1 border border-slate-300 rounded-xs">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-[#8F0A0D]"></span>
                                                    <span>digitally signed</span>
                                                </div>
                                                <div class="flex-1 flex items-center justify-center my-0.5 min-h-[26px]">
                                                    <template x-if="activeProject?.handover_data?.sales_signature?.signature">
                                                        <img :src="activeProject.handover_data.sales_signature.signature" alt="TTD Sales" class="max-h-6 max-w-[95px] object-contain">
                                                    </template>
                                                    <template x-if="!activeProject?.handover_data?.sales_signature?.signature">
                                                        <div class="font-serif italic text-[11px] font-bold text-slate-800 text-center" x-text="activeProject?.handover_data?.sales_signature?.user_name || activeProject?.sales_name || 'Sales PIC'"></div>
                                                    </template>
                                                </div>
                                                <div class="text-[7px] font-mono text-slate-500 tracking-tight text-center border-t border-dashed border-slate-200 pt-0.5 truncate" x-text="'dsign.ipnet.id/' + (activeProject?.handover_data?.sales_signature?.qr_token || 'COMMERCIAL')">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-[10px] border-t border-slate-300 pt-1 text-slate-700 space-y-0.5">
                                        <p class="truncate">Nama: <strong class="text-black" x-text="activeProject?.handover_data?.sales_signature?.user_name || activeProject?.sales_name || '—'"></strong></p>
                                        <p>Tanggal: <span x-text="activeProject?.handover_data?.sales_signature?.signed_at ? activeProject.handover_data.sales_signature.signed_at.substring(0, 10) : (activeProject?.handover_submitted_at ? activeProject.handover_submitted_at.substring(0, 10) : '{{ date('d/m/Y') }}')"></span></p>
                                        <div class="text-[8.5px] text-slate-500 font-mono flex items-center gap-1 pt-0.5" title="Kunci tanda tangan digital Sales telah diverifikasi dan terkunci">
                                            <svg class="w-2.5 h-2.5 text-slate-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/></svg>
                                            <span class="truncate">Terkunci secara kriptografis</span>
                                        </div>
                                    </div>
                                </div>

                                {{-- 2. PMO / Project Manager --}}
                                <div class="p-2.5 bg-white flex flex-col justify-between min-h-[160px]">
                                    <div class="text-center">
                                        <div class="font-bold text-[11px] text-black">Diterima oleh</div>
                                        <div class="text-[10px] text-slate-600 font-medium">PMO / Project Manager</div>
                                    </div>
                                    <div class="my-auto py-2 text-center flex items-center justify-center">
                                        <div class="inline-flex items-center gap-2 select-none text-left align-middle">
                                            {{-- QR Code Box --}}
                                            <div x-show="signatureMode === 'signature_qr' || signatureMode === 'qr_only'" 
                                                 class="shrink-0 p-1 bg-white border border-slate-300 rounded flex items-center justify-center shadow-2xs overflow-hidden [&>svg]:!w-full [&>svg]:!h-full [&>svg]:block" 
                                                 style="width: 54px; height: 54px; min-width: 54px; min-height: 54px; max-width: 54px; max-height: 54px;">
                                                {!! auth()->user()->signature_qr_svg !!}
                                            </div>
                                            {{-- Framed Stroke Box --}}
                                            <div x-show="signatureMode === 'signature_qr' || signatureMode === 'signature_only'" 
                                                 class="relative px-2 pt-1.5 pb-1 border border-slate-700 bg-white rounded flex flex-col justify-between shadow-2xs shrink-0" 
                                                 style="width: 130px; min-width: 120px; max-width: 135px; height: 54px; min-height: 54px; max-height: 54px;">
                                                <div class="absolute -top-2 left-1.5 px-1 bg-white text-[7.5px] font-mono font-bold text-slate-700 tracking-wide flex items-center gap-1 border border-slate-300 rounded-xs">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-[#8F0A0D]"></span>
                                                    <span>digitally signed</span>
                                                </div>
                                                <div class="flex-1 flex items-center justify-center my-0.5 min-h-[28px]">
                                                    @if(!empty(auth()->user()->signature_data))
                                                        <img src="{{ auth()->user()->signature_data }}" alt="TTD PM" class="max-h-7 max-w-[100px] object-contain">
                                                    @else
                                                        <div class="font-serif italic text-[11px] font-bold text-slate-800 text-center">{{ auth()->user()->name }}</div>
                                                    @endif
                                                </div>
                                                <div class="text-[7px] font-mono text-slate-500 tracking-tight text-center border-t border-dashed border-slate-200 pt-0.5 truncate">
                                                    dsign.ipnet.id/{{ auth()->user()->signature_qr_token }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-[10px] border-t border-slate-300 pt-1 text-slate-700 space-y-0.5">
                                        <p class="truncate">Nama: <strong class="text-[#8F0A0D]" x-text="activeProject?.handover_data?.pm_signature?.user_name || activeProject?.pm || '{{ auth()->user()->name }}'"></strong></p>
                                        <p>Tanggal: <span x-text="activeProject?.handover_data?.pm_signature?.signed_at ? activeProject.handover_data.pm_signature.signed_at.substring(0, 10) : '{{ date('d/m/Y') }}'"></span></p>
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
                    <div class="flex items-center gap-2">
                        <button type="button" @click="handoverReviewModalOpen = false" class="px-4 py-2 border border-[#CBD5E1] rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-50 transition cursor-pointer">
                            Tutup
                        </button>
                        <template x-if="activeProject">
                            <a :href="'/sales/handover/' + activeProject.id + '/document'" target="_blank"
                               class="px-3.5 py-2 border border-slate-300 rounded-xl text-xs font-bold text-slate-700 bg-white hover:bg-slate-50 transition cursor-pointer flex items-center gap-1.5 shadow-2xs">
                                <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <span>Lihat Berita Acara</span>
                            </a>
                        </template>
                    </div>

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
                                <span>Tanda Tangani & Sahkan Serah Terima</span>
                            </button>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </template>

            {{-- MODAL RESMI DOKUMEN SERAH TERIMA PM KE MANAGED SERVICE (PERSIS TEMPLATE MANAGED SERVICE & ACTIVITY LOG) --}}
    <template x-teleport="body">
        <div x-show="handoverToMsModalOpen" 
             x-cloak 
             class="fixed inset-0 z-50 bg-[#0F172A]/70 backdrop-blur-xs flex items-center justify-center p-3 sm:p-5 overflow-y-auto"
             @keydown.escape.window="handoverToMsModalOpen = false">
            <div class="bg-white rounded-2xl max-w-7xl w-full shadow-2xl border border-[#CBD5E1] max-h-[94vh] flex flex-col overflow-hidden my-auto" 
                 @click.away="handoverToMsModalOpen = false">
                
                {{-- Header Bar Persis Template Dokumen Resmi IPNET --}}
                <div class="px-6 py-4 border-b border-[#E2E8F0] flex items-center justify-between bg-[#F8FAFC] shrink-0">
                    <div class="flex items-center gap-2.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#8F0A0D]"></span>
                        <div>
                            <h3 class="text-[16px] font-bold text-[#0F172A]">Serah Terima Project Manager ke Managed Service</h3>
                            <p class="text-[11px] text-[#64748B]">Verifikasi checklist serah terima resmi operasional &amp; pemeliharaan dari Project Manager ke Divisi Managed Service.</p>
                        </div>
                    </div>
                    <button type="button" @click="handoverToMsModalOpen = false" class="text-[#64748B] hover:text-[#1E293B] p-1.5 rounded-lg hover:bg-[#E2E8F0] transition cursor-pointer" title="Tutup">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Scrollable Container Abu-Abu Lembut Persis Activity Log & MS --}}
                <div class="flex-1 overflow-y-auto px-4 pt-4 pb-8 sm:px-8 sm:pt-6 sm:pb-8 bg-[#F1F5F9]/80 space-y-4">
                    
                    {{-- Banner Kategori Laporan Konsisten dengan Form Engineer Activity --}}
                    <div class="flex flex-wrap items-center justify-between gap-3 bg-white p-3.5 rounded-xl border border-[#CBD5E1] shadow-xs no-print">
                        <div class="flex items-center gap-2.5">
                            <span class="text-xs font-bold text-[#475569] uppercase tracking-wider">KATEGORI LAPORAN:</span>
                            <span class="px-3 py-1 bg-red-50 text-[#8F0A0D] border border-red-200 rounded-lg text-xs font-bold tracking-wide uppercase shadow-2xs">
                                SERAH TERIMA PROJECT MANAGER KE MANAGED SERVICE
                            </span>
                        </div>

                        <div class="text-xs text-slate-500 font-medium">
                            Format resmi PT IP Network Solusindo
                        </div>
                    </div>

                    {{-- DOKUMEN RESMI SERAH TERIMA PROJECT MANAGER KE MANAGED SERVICE --}}
                    <div class="bg-white border-2 border-[#CBD5E1] rounded-xl p-6 sm:p-10 shadow-sm space-y-5 text-[#0F172A] font-sans relative">
                        
                        {{-- Kop Dokumen Persis PDF --}}
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
                                <div class="text-[10px] text-slate-500 italic font-medium mt-0.5 hidden print:block">
                                    Hal 1
                                </div>
                            </div>
                        </div>

                        {{-- Judul Utama Dokumen Persis PDF --}}
                        <div class="text-center my-3 sm:my-4">
                            <div class="text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-0.5">
                                BERITA ACARA SERAH TERIMA PROYEK
                            </div>
                            <h1 class="text-sm sm:text-base font-black text-black uppercase tracking-wide leading-snug">
                                SERAH TERIMA PROJECT MANAGER KE MANAGED SERVICE
                            </h1>
                            <p class="text-xs italic text-slate-600 mt-1 font-serif">
                                Project Handover to Managed Service Checklist – Tahap proses: DELIVER
                            </p>
                        </div>

                        {{-- Grid Metadata Form (Kode Form, Versi, Tanggal Efektif, No. Berita Acara) --}}
                        <div class="border border-black overflow-hidden text-xs">
                            <table class="w-full border-collapse border border-black">
                                <tbody>
                                    <tr class="border-b border-black">
                                        <td class="w-1/4 py-1.5 px-3 font-bold bg-[#F8FAFC] border-r border-black">Kode Form</td>
                                        <td class="w-1/4 py-1.5 px-3 border-r border-black font-mono font-bold text-slate-900">IPN-FRM-DEL-01</td>
                                        <td class="w-1/4 py-1.5 px-3 font-bold bg-[#F8FAFC] border-r border-black">Versi</td>
                                        <td class="w-1/4 py-1.5 px-3 font-bold text-slate-900">1.0</td>
                                    </tr>
                                    <tr>
                                        <td class="py-1.5 px-3 font-bold bg-[#F8FAFC] border-r border-black">Tanggal Efektif</td>
                                        <td class="py-1.5 px-3 border-r border-black font-mono text-slate-900">01/10/2026</td>
                                        <td class="py-1.5 px-3 font-bold bg-[#F8FAFC] border-r border-black">No. Berita Acara</td>
                                        <td class="py-1.5 px-3 font-mono font-bold text-[#8F0A0D]" x-text="'BA-MS/' + new Date().getFullYear() + '/' + (activeProject ? String(activeProject.id).padStart(4, '0') : '0001')"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        {{-- Petunjuk Pengisian Persis PDF --}}
                        <div class="text-[11.5px] text-slate-800 leading-relaxed border border-slate-300 bg-slate-50/70 p-3 rounded-lg">
                            <strong class="font-bold text-black">Petunjuk:</strong> Formulir checklist serah terima operasional hasil implementasi proyek dari Project Manager (PMO) kepada Divisi Managed Service. Isi kolom yang relevan. Jika suatu butir tidak berlaku, tulis <span class="font-semibold">“Tidak Berlaku”</span> dan berikan catatan bila diperlukan. Simpan formulir beserta bukti pendukung pada lokasi dokumen yang ditetapkan.
                        </div>

                        {{-- 1. REFERENSI KONTRAK & PROYEK IMPLEMENTASI (Banner Hitam & Border Hitam Persis PDF) --}}
                        <div class="border border-black overflow-hidden">
                            <div class="bg-black text-white px-3 py-1.5 font-black text-xs uppercase tracking-wider">
                                1. REFERENSI KONTRAK &amp; PROYEK IMPLEMENTASI
                            </div>
                            <table class="w-full border-collapse text-xs">
                                <tbody>
                                    <tr class="border-b border-black">
                                        <td class="w-1/3 sm:w-1/4 py-2 px-3 font-bold bg-white border-r border-black">Pelanggan / Klien</td>
                                        <td class="py-2 px-3 font-black text-slate-900" x-text="activeProject?.client || '-'"></td>
                                    </tr>
                                    <tr class="border-b border-black">
                                        <td class="py-2 px-3 font-bold bg-white border-r border-black">Nama Proyek Implementasi</td>
                                        <td class="py-2 px-3 font-black text-[#8F0A0D]" x-text="activeProject?.name || '-'"></td>
                                    </tr>
                                    <tr class="border-b border-black">
                                        <td class="py-2 px-3 font-bold bg-white border-r border-black">Nomor Kontrak / PO / SPK</td>
                                        <td class="py-2 px-3 font-mono font-bold text-slate-900" x-text="activeProject?.po_number || activeProject?.so_code || '-'"></td>
                                    </tr>
                                    <tr class="border-b border-black">
                                        <td class="py-2 px-3 font-bold bg-white border-r border-black">Nomor Sales Order (SO)</td>
                                        <td class="py-2 px-3 font-mono font-bold text-[#8F0A0D]" x-text="activeProject?.so_code || '-'"></td>
                                    </tr>
                                    <tr class="border-b border-black">
                                        <td class="py-2 px-3 font-bold bg-white border-r border-black">Tanggal Efektif &amp; Masa Operasional</td>
                                        <td class="py-2 px-3 font-medium text-slate-800" x-text="(msHandoverForm.service_start_date ? msHandoverForm.service_start_date + ' s/d ' + (msHandoverForm.service_end_date || '') + ' (Masa Pemeliharaan Operasional)' : 'Masa Pemeliharaan Sistem')"></td>
                                    </tr>
                                    <tr class="border-b border-black">
                                        <td class="py-2 px-3 font-bold bg-white border-r border-black">Diserahkan oleh (Project Manager)</td>
                                        <td class="py-2 px-3 font-bold text-[#8F0A0D]" x-text="(activeProject?.pm || '{{ auth()->user()->name }}') + ' (Project Manager)'"></td>
                                    </tr>
                                    <tr>
                                        <td class="py-2 px-3 font-bold bg-white border-r border-black">Diterima oleh (Managed Service)</td>
                                        <td class="py-2 px-3 font-bold text-slate-900">Tim Managed Service &amp; Maintenance Support</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        {{-- 2. CHECKLIST (4 Kolom Sesuai PDF: NO. | INFORMASI | STATUS / ISIAN | CATATAN) --}}
                        <div class="border border-black overflow-hidden">
                            <div class="bg-black text-white px-3 py-1.5 font-black text-xs uppercase tracking-wider">
                                CHECKLIST
                            </div>
                            <table class="w-full border-collapse text-xs text-left">
                                <thead>
                                    <tr class="bg-white text-black text-[11px] font-black uppercase tracking-wider border-b border-black">
                                        <th class="py-2 px-2.5 w-12 text-center border-r border-black">NO.</th>
                                        <th class="py-2 px-3 border-r border-black">INFORMASI</th>
                                        <th class="py-2 px-2 text-center w-36 sm:w-44 border-r border-black">STATUS / ISIAN</th>
                                        <th class="py-2 px-3 w-1/3 sm:w-2/5">CATATAN</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-black">
                                    <template x-for="(item, idx) in msChecklistItems" :key="item.key">
                                        <tr>
                                            <td class="py-2 px-2.5 text-center font-bold border-r border-black" x-text="item.no"></td>
                                            <td class="py-2 px-3 font-semibold text-slate-900 border-r border-black" x-text="item.name"></td>
                                            <td class="py-1.5 px-2 text-center border-r border-black">
                                                <select x-model="msHandoverForm.checklist_status[item.key]" class="w-full py-1 px-1.5 bg-slate-50 border border-slate-300 rounded text-xs text-center font-bold text-slate-800">
                                                    <option value="Ada">Ada</option>
                                                    <option value="Tidak Ada">Tidak Ada</option>
                                                    <option value="N/A">N/A</option>
                                                </select>
                                            </td>
                                            <td class="py-1.5 px-3">
                                                <input type="text" x-model="msHandoverForm.checklist_notes[item.key]" :placeholder="item.default_note" class="w-full py-1 px-2 bg-slate-50 border border-slate-300 rounded text-xs text-slate-800">
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>

                        {{-- 3. CATATAN KHUSUS --}}
                        <div class="border border-black overflow-hidden">
                            <div class="bg-black text-white px-3 py-1.5 font-black text-xs uppercase tracking-wider">
                                CATATAN KHUSUS
                            </div>
                            <div class="p-3 bg-white">
                                <textarea x-model="msHandoverForm.special_notes" 
                                          rows="3" 
                                          placeholder="[Tuliskan jika ada ekspektasi khusus dari customer, janji fitur khusus, atau kendala yang perlu diwaspadai tim delivery]" 
                                          class="w-full p-2 bg-slate-50 border border-slate-300 rounded text-xs text-slate-800 outline-none focus:bg-white focus:border-black transition leading-relaxed"></textarea>
                            </div>
                        </div>

                        {{-- 4. STATUS HANDOVER --}}
                        <div class="border border-black overflow-hidden">
                            <div class="bg-black text-white px-3 py-1.5 font-black text-xs uppercase tracking-wider">
                                STATUS HANDOVER
                            </div>
                            <table class="w-full border-collapse text-xs">
                                <tbody>
                                    <tr class="border-b border-black bg-white">
                                        <td class="w-1/3 sm:w-1/4 py-2.5 px-3 font-bold border-r border-black align-middle">Status handover:</td>
                                        <td class="py-2.5 px-3">
                                            <div class="flex items-center gap-6 flex-wrap">
                                                <label class="inline-flex items-center gap-2 font-bold text-xs cursor-pointer select-none text-emerald-800">
                                                    <input type="radio" value="approved" x-model="msHandoverForm.handover_status_selected" class="w-4 h-4 text-emerald-600 focus:ring-emerald-500">
                                                    <span>Diterima</span>
                                                </label>
                                                <label class="inline-flex items-center gap-2 font-bold text-xs cursor-pointer select-none text-amber-800">
                                                    <input type="radio" value="conditional" x-model="msHandoverForm.handover_status_selected" class="w-4 h-4 text-amber-600 focus:ring-amber-500">
                                                    <span>Diterima bersyarat</span>
                                                </label>
                                                <label class="inline-flex items-center gap-2 font-bold text-xs cursor-pointer select-none text-rose-800">
                                                    <input type="radio" value="rejected" x-model="msHandoverForm.handover_status_selected" class="w-4 h-4 text-rose-600 focus:ring-rose-500">
                                                    <span>Ditolak / dikembalikan</span>
                                                </label>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr class="border-b border-black">
                                        <td class="py-2 px-3 font-bold bg-white border-r border-black">Kekurangan / risiko yang masih terbuka</td>
                                        <td class="p-1.5">
                                            <input type="text" x-model="msHandoverForm.risks_and_pic" placeholder="Catatan kekurangan atau risiko yang terbuka..." class="w-full px-2.5 py-1 bg-white border border-slate-300 rounded text-xs text-slate-800 outline-none focus:border-black">
                                        </td>
                                    </tr>
                                    <tr class="border-b border-black">
                                        <td class="py-2 px-3 font-bold bg-white border-r border-black">Informasi yang belum tersedia</td>
                                        <td class="p-1.5">
                                            <input type="text" x-model="msHandoverForm.confirmation_notes" placeholder="Informasi / dokumen yang belum lengkap..." class="w-full px-2.5 py-1 bg-white border border-slate-300 rounded text-xs text-slate-800 outline-none focus:border-black">
                                        </td>
                                    </tr>
                                    <tr class="border-b border-black">
                                        <td class="py-2 px-3 font-bold bg-white border-r border-black">PIC dan target penyelesaian</td>
                                        <td class="p-1.5">
                                            <input type="text" x-model="msHandoverForm.follow_up_date" placeholder="PIC penanggung jawab & target tanggal penyelesaian..." class="w-full px-2.5 py-1 bg-white border border-slate-300 rounded text-xs text-slate-800 outline-none focus:border-black">
                                        </td>
                                    </tr>
                                    <tr class="border-b border-black">
                                        <td class="py-2 px-3 font-bold bg-white border-r border-black">Konfirmasi Cakupan Layanan / SLA</td>
                                        <td class="p-1.5">
                                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                                                <select x-model="msHandoverForm.sla_tier" class="px-2 py-1 bg-slate-50 border border-slate-300 rounded text-xs font-bold text-[#8F0A0D]">
                                                    <option value="Platinum">SLA Platinum</option>
                                                    <option value="Gold">SLA Gold</option>
                                                    <option value="Silver">SLA Silver</option>
                                                    <option value="Bronze">SLA Bronze</option>
                                                </select>
                                                <select x-model="msHandoverForm.sla_coverage_hours" class="px-2 py-1 bg-slate-50 border border-slate-300 rounded text-xs font-semibold text-slate-800">
                                                    <option value="24x7 Non-Stop">24x7 Non-Stop</option>
                                                    <option value="8x5 Business Hours">8x5 Jam Kerja</option>
                                                    <option value="On-Call Emergency">On-Call Emergency</option>
                                                </select>
                                                <select x-model="msHandoverForm.maintenance_frequency" class="px-2 py-1 bg-slate-50 border border-slate-300 rounded text-xs font-semibold text-slate-800">
                                                    <option value="Bulanan (Monthly)">Bulanan (Monthly)</option>
                                                    <option value="Triwulanan (Quarterly)">Triwulanan (Quarterly)</option>
                                                    <option value="Tahunan (Annual)">Tahunan (Annual)</option>
                                                </select>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="py-2 px-3 font-bold bg-white border-r border-black">Pekerjaan yang boleh berjalan (bila bersyarat)</td>
                                        <td class="p-1.5">
                                            <input type="text" x-model="msHandoverForm.service_acceptance" placeholder="Scope pekerjaan yang boleh berjalan..." class="w-full px-2.5 py-1 bg-white border border-slate-300 rounded text-xs text-slate-800 outline-none focus:border-black">
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        {{-- ATURAN STATUS Persis PDF --}}
                        <div class="space-y-1.5">
                            <h3 class="font-black text-xs uppercase text-[#0F172A] tracking-wider">ATURAN STATUS</h3>
                            <div class="text-xs text-slate-800 leading-relaxed border border-black p-3.5 bg-white">
                                <p>
                                    <strong class="font-bold text-black">Diterima:</strong> informasi minimum cukup untuk memulai perencanaan. 
                                    <strong class="font-bold text-black ml-2">Diterima bersyarat:</strong> kekurangan tercatat, ada PIC dan target penyelesaian, dan pekerjaan yang boleh berjalan dibatasi secara eksplisit. 
                                    <strong class="font-bold text-black ml-2">Ditolak/dikembalikan:</strong> scope, kontrak, atau persyaratan penting belum jelas; jangan memulai pekerjaan terdampak
                                </p>
                            </div>
                        </div>

                        {{-- Kontrol Pilihan Format Tanda Tangan Digital Resmi --}}
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-3 bg-slate-50 border border-black text-xs no-print">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-[#8F0A0D]"></span>
                                <span class="font-bold text-slate-900 uppercase tracking-wide">PILIHAN FORMAT STEMPEL TANDA TANGAN:</span>
                            </div>
                            <div class="inline-flex rounded-lg bg-white p-1 border border-slate-300 shadow-2xs gap-1">
                                <button type="button" 
                                        @click="msSignatureMode = 'signature_qr'" 
                                        :class="msSignatureMode === 'signature_qr' ? 'bg-[#8F0A0D] text-white shadow-2xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100'"
                                        class="px-2.5 py-1 rounded text-xs font-bold transition flex items-center gap-1.5 cursor-pointer">
                                    <span>TTD + QR Code (Resmi)</span>
                                </button>
                                <button type="button" 
                                        @click="msSignatureMode = 'signature_only'" 
                                        :class="msSignatureMode === 'signature_only' ? 'bg-[#8F0A0D] text-white shadow-2xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100'"
                                        class="px-2.5 py-1 rounded text-xs font-bold transition flex items-center gap-1.5 cursor-pointer">
                                    <span>Tanda Tangan Saja</span>
                                </button>
                                <button type="button" 
                                        @click="msSignatureMode = 'qr_only'" 
                                        :class="msSignatureMode === 'qr_only' ? 'bg-[#8F0A0D] text-white shadow-2xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100'"
                                        class="px-2.5 py-1 rounded text-xs font-bold transition flex items-center gap-1.5 cursor-pointer">
                                    <span>QR Code Saja</span>
                                </button>
                            </div>
                        </div>

                        {{-- Tabel Tanda Tangan / Persetujuan Persis 4 Kolom PDF (PM ke Managed Service) --}}
                        <div class="border border-black overflow-hidden">
                            <table class="w-full border-collapse text-xs">
                                <thead>
                                    <tr class="bg-white border-b border-black text-center font-bold">
                                        <th class="py-2.5 px-3 border-r border-black w-1/4 align-top">
                                            <div class="text-[11px] text-slate-600 font-medium">Diserahkan oleh</div>
                                            <div class="font-black text-slate-900 mt-0.5">Project Manager / PMO</div>
                                        </th>
                                        <th class="py-2.5 px-3 border-r border-black w-1/4 align-top">
                                            <div class="text-[11px] text-slate-600 font-medium">Diterima oleh</div>
                                            <div class="font-black text-slate-900 mt-0.5">Managed Service Lead</div>
                                        </th>
                                        <th class="py-2.5 px-3 border-r border-black w-1/4 align-top">
                                            <div class="text-[11px] text-slate-600 font-medium">Validasi Teknis</div>
                                            <div class="font-black text-slate-900 mt-0.5">Technical / Solution Lead</div>
                                        </th>
                                        <th class="py-2.5 px-3 w-1/4 align-top">
                                            <div class="text-[11px] text-slate-600 font-medium">Mengetahui</div>
                                            <div class="font-black text-slate-900 mt-0.5">Head of Operations / PMO</div>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="min-h-[160px]">
                                        {{-- 1. Project Manager / PMO (Tinggal TTD disini: Preview TTD & QR) --}}
                                        <td class="p-2.5 border-r border-black align-top flex-col justify-between">
                                            <div class="space-y-1">
                                                <p><strong class="font-bold">Nama:</strong> <span class="font-bold text-[#8F0A0D]">{{ auth()->user()->name }}</span></p>
                                                <p><strong class="font-bold">Tanggal:</strong> {{ date('d/m/Y') }}</p>
                                            </div>
                                            <div class="py-2 flex items-center justify-center">
                                                <div class="inline-flex items-center gap-2 select-none text-left align-middle">
                                                    {{-- QR Code --}}
                                                    <div x-show="msSignatureMode === 'signature_qr' || msSignatureMode === 'qr_only'" class="w-13 h-13 shrink-0 p-1 bg-white border border-slate-300 rounded flex items-center justify-center shadow-2xs">
                                                        {!! auth()->user()->signature_qr_svg !!}
                                                    </div>
                                                    {{-- Digitally Signed Frame --}}
                                                    <div x-show="msSignatureMode === 'signature_qr' || msSignatureMode === 'signature_only'" class="relative px-2.5 pt-2 pb-1 border border-slate-700 bg-white rounded min-w-[125px] max-w-[160px] min-h-[54px] flex flex-col justify-between shadow-2xs">
                                                        <div class="absolute -top-2 left-1.5 px-1 bg-white text-[7.5px] font-mono font-bold text-slate-700 tracking-wide flex items-center gap-1 border border-slate-300 rounded-xs">
                                                            <span class="w-1.5 h-1.5 rounded-full bg-[#8F0A0D]"></span>
                                                            <span>digitally signed</span>
                                                        </div>
                                                        <div class="flex-1 flex items-center justify-center my-0.5 min-h-[28px]">
                                                            @if(!empty(auth()->user()->signature_data))
                                                                <img src="{{ auth()->user()->signature_data }}" alt="TTD" class="max-h-7 max-w-[100px] object-contain">
                                                            @else
                                                                <div class="font-serif italic text-[11px] font-bold text-slate-800 text-center">{{ auth()->user()->name }}</div>
                                                            @endif
                                                        </div>
                                                        <div class="text-[7px] font-mono text-slate-500 tracking-tight text-center border-t border-dashed border-slate-200 pt-0.5 truncate">
                                                            dsign.ipnet.id/{{ auth()->user()->signature_qr_token }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="text-[9.5px] text-center font-mono text-emerald-700 font-bold">
                                                &check; Signed by Project Manager
                                            </div>
                                        </td>

                                        {{-- 2. Managed Service Lead (Penerima) --}}
                                        <td class="p-2.5 border-r border-black align-top flex-col justify-between">
                                            <div class="space-y-1">
                                                <p><strong class="font-bold">Nama:</strong> Tim Managed Service</p>
                                                <p><strong class="font-bold">Tanggal:</strong> [Saat Verifikasi]</p>
                                            </div>
                                            <div class="py-4 text-center">
                                                <div class="inline-flex items-center gap-1 px-2 py-1 rounded bg-amber-50 text-amber-800 border border-amber-200 font-mono text-[9.5px]">
                                                    Menunggu Acceptance MS
                                                </div>
                                            </div>
                                        </td>

                                        {{-- 3. Technical / Solution Lead --}}
                                        <td class="p-2.5 border-r border-black align-top flex-col justify-between">
                                            <div class="space-y-1">
                                                <p><strong class="font-bold">Nama:</strong> Solution Lead</p>
                                                <p><strong class="font-bold">Tanggal:</strong> {{ date('d/m/Y') }}</p>
                                            </div>
                                            <div class="py-4 text-center">
                                                <div class="border-b border-dashed border-slate-400 w-3/4 mx-auto my-3"></div>
                                                <span class="text-[9px] text-slate-400 font-mono">(Solution Architect)</span>
                                            </div>
                                        </td>

                                        {{-- 4. Operations Head / PMO Head --}}
                                        <td class="p-2.5 align-top flex-col justify-between">
                                            <div class="space-y-1">
                                                <p><strong class="font-bold">Nama:</strong> Susanto</p>
                                                <p><strong class="font-bold">Tanggal:</strong> {{ date('d/m/Y') }}</p>
                                            </div>
                                            <div class="py-4 text-center">
                                                <div class="border-b border-dashed border-slate-400 w-3/4 mx-auto my-3"></div>
                                                <span class="text-[9px] text-slate-400 font-mono">(Division Head)</span>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        {{-- Catatan Pengendalian Persis PDF --}}
                        <p class="text-[11px] text-slate-800 leading-relaxed">
                            <strong class="font-bold text-black">Catatan pengendalian:</strong> handover tidak mengubah kewenangan formal atau tanggung jawab atas output yang telah dibuat. Perubahan scope/komitmen pelanggan harus melalui mekanisme persetujuan yang berlaku.
                        </p>

                    </div>
                </div>

                {{-- Footer Action Bar Sesuai Template Lebar --}}
                <div class="px-6 py-4 border-t border-[#E2E8F0] bg-white flex items-center justify-between shrink-0 no-print gap-3">
                    <button type="button" 
                            @click="handoverToMsModalOpen = false" 
                            class="px-4 py-2.5 min-h-[38px] text-xs font-bold text-slate-600 hover:bg-slate-50 border border-[#CBD5E1] rounded-xl transition cursor-pointer whitespace-nowrap shrink-0 inline-flex items-center justify-center">
                        Batal
                    </button>

                    <div class="flex items-center gap-2 shrink-0 whitespace-nowrap">
                        <template x-if="activeProject">
                            <a :href="'/sales/handover/' + activeProject.id + '/document?type=pm_to_ms'" target="_blank"
                               class="px-3.5 py-2.5 min-h-[38px] border border-slate-300 rounded-xl text-xs font-bold text-slate-700 hover:bg-slate-50 transition cursor-pointer inline-flex items-center gap-1.5 shadow-2xs whitespace-nowrap shrink-0">
                                <svg class="w-4 h-4 text-[#8F0A0D] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <span class="whitespace-nowrap">Lihat BA Resmi (Template 3)</span>
                            </a>
                        </template>

                        <button type="button" 
                                onclick="window.print()" 
                                class="px-3.5 py-2.5 min-h-[38px] border border-slate-300 rounded-xl text-xs font-bold text-slate-700 hover:bg-slate-50 transition cursor-pointer inline-flex items-center gap-1.5 shadow-2xs whitespace-nowrap shrink-0">
                            <svg class="w-4 h-4 text-slate-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            <span class="whitespace-nowrap">Cetak Dokumen</span>
                        </button>

                        <button type="button" 
                                @click="submitHandoverToMs()" 
                                class="px-5 py-2.5 min-h-[38px] text-xs font-bold text-white bg-[#8F0A0D] hover:bg-[#73080A] rounded-xl shadow-xs transition cursor-pointer inline-flex items-center gap-2 active:scale-95 whitespace-nowrap shrink-0 leading-normal justify-center">
                            <svg class="w-4 h-4 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            <span class="whitespace-nowrap">Tanda Tangani &amp; Serahkan ke Managed Service</span>
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </template>

        {{-- ========================================================== --}}
    {{-- MODAL: BUAT PROYEK BARU SECARA MANUAL OLEH PMO (CANVAS KOSONG) --}}
    {{-- ========================================================== --}}
    <template x-teleport="body">
        <div x-show="showCreateProjectModal" 
             x-cloak 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 bg-[#0F172A]/70 backdrop-blur-xs flex items-center justify-center p-3 sm:p-5 overflow-y-auto"
             @click.self="showCreateProjectModal = false"
             @keydown.escape.window="showCreateProjectModal = false">
            
            <div @click.stop
                 class="bg-white rounded-2xl max-w-2xl sm:max-w-3xl w-full shadow-2xl border border-[#CBD5E1] max-h-[92vh] flex flex-col overflow-hidden my-auto animate-fade-in-up">
                
                {{-- Header Bar Konsisten Template Resmi IPNET --}}
                <div class="px-6 py-4 border-b border-[#E2E8F0] flex items-center justify-between bg-[#F8FAFC] shrink-0">
                    <div class="flex items-center gap-2.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#8F0A0D] shrink-0"></span>
                        <div>
                            <h3 class="text-[16px] font-bold text-[#0F172A]">Buat Proyek Baru (Direct PMO Delivery)</h3>
                            <p class="text-[11px] text-[#64748B]">Inisialisasi proyek baru langsung berstatus aktif tanpa melalui alur pipeline sales.</p>
                        </div>
                    </div>
                    <button type="button" @click="showCreateProjectModal = false" class="text-[#64748B] hover:text-[#1E293B] p-1.5 rounded-lg hover:bg-[#E2E8F0] transition cursor-pointer" title="Tutup">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Formulir Tambah Proyek --}}
                <form action="{{ route('pmo.projects.store_manual') }}" 
                      method="POST" 
                      x-data="{ 
                          selectedTeams: ['Tim Network'], 
                          newCustomTeam: '', 
                          teamError: '',
                          rawContractValue: '',
                          contractValueDisplay: '',
                          formatContractValue(val) {
                              let clean = (val || '').toString().replace(/\D/g, '');
                              this.rawContractValue = clean;
                              this.contractValueDisplay = clean ? new Intl.NumberFormat('id-ID').format(clean) : '';
                          },
                          toggleTeam(team) {
                              const idx = this.selectedTeams.indexOf(team);
                              if (idx > -1) {
                                  this.selectedTeams.splice(idx, 1);
                              } else {
                                  this.selectedTeams.push(team);
                              }
                              this.teamError = '';
                          },
                          hasTeam(team) {
                              return this.selectedTeams.includes(team);
                          },
                          addCustomTeam() {
                              let val = this.newCustomTeam.trim();
                              if (!val) return;
                              if (!/^tim\s+/i.test(val)) {
                                  val = 'Tim ' + val;
                              }
                              if (!this.selectedTeams.includes(val)) {
                                  this.selectedTeams.push(val);
                              }
                              this.newCustomTeam = '';
                              this.teamError = '';
                          },
                          removeTeam(team) {
                              this.selectedTeams = this.selectedTeams.filter(t => t !== team);
                          },
                          submitCheck(e) {
                              if (this.newCustomTeam.trim()) {
                                  this.addCustomTeam();
                              }
                              if (this.selectedTeams.length === 0) {
                                  e.preventDefault();
                                  this.teamError = 'Pilih minimal satu tim teknis pelaksana.';
                                  return false;
                              }
                          }
                      }"
                      @submit="submitCheck($event)"
                      class="flex flex-col flex-1 overflow-hidden m-0 p-0">
                    @csrf

                    {{-- Scrollable Form Fields --}}
                    <div class="flex-1 overflow-y-auto p-6 sm:p-7 space-y-4">
                    
                    {{-- Baris 1: Nama Proyek --}}
                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-slate-800">
                            Nama Proyek <span class="text-rose-600">*</span>
                        </label>
                        <input type="text" 
                               name="name" 
                               required 
                               placeholder="Contoh: Pengadaan & Instalasi Router PE Sudirman"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-800 focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 outline-none transition">
                    </div>

                    {{-- Baris 2: Klien & PIC Klien (Sesuai Kolom KLIEN & PIC KLIEN di Tabel) --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="block text-xs font-bold text-slate-800">
                                Klien / Pelanggan <span class="text-rose-600">*</span>
                            </label>
                            <input type="text" 
                                   name="client" 
                                   required 
                                   placeholder="Contoh: PT Angkasa Pura Support"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-800 focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 outline-none transition">
                        </div>

                        <div class="space-y-1">
                            <div class="flex items-center justify-between">
                                <label class="block text-xs font-bold text-slate-800">
                                    PIC Klien (Pendamping Diskusi PM)
                                </label>
                                <span class="text-[10.5px] text-slate-400">Nama, Jabatan, & No HP</span>
                            </div>
                            <input type="text" 
                                   name="customer_pic" 
                                   placeholder="Contoh: Bpk. Wayan Koster (Head of ICT - 081238991122)"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-800 focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 outline-none transition">
                        </div>
                    </div>

                    {{-- Baris 2: Tim Teknis & Project Manager --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- Kolom Tim Teknis (Hanya Tim Network & Tim Security + Multi-select + Ketik Manual) --}}
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between">
                                <label class="block text-xs font-bold text-slate-800">
                                    Tim Teknis Pelaksana <span class="text-rose-600">*</span>
                                </label>
                                <span class="text-[11px] text-slate-400">Pilih satu atau lebih</span>
                            </div>

                            {{-- Pilihan Tim (Tim Network & Tim Security) + Custom Chips --}}
                            <div class="flex flex-wrap items-center gap-2 pt-0.5">
                                {{-- Tombol Tim Network --}}
                                <button type="button" 
                                        @click="toggleTeam('Tim Network')"
                                        :class="hasTeam('Tim Network') 
                                            ? 'bg-red-50 border-[#8F0A0D] text-[#8F0A0D] font-bold shadow-2xs ring-1 ring-[#8F0A0D]/20' 
                                            : 'bg-white border-slate-200 text-slate-700 hover:bg-slate-50 hover:border-slate-300 font-semibold'"
                                        class="px-3 py-1.5 rounded-xl border text-xs flex items-center gap-1.5 transition cursor-pointer select-none">
                                    <template x-if="hasTeam('Tim Network')">
                                        <svg class="w-3.5 h-3.5 text-[#8F0A0D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    </template>
                                    <template x-if="!hasTeam('Tim Network')">
                                        <span class="w-3.5 h-3.5 rounded-full border border-slate-300 inline-block"></span>
                                    </template>
                                    <span>Tim Network</span>
                                </button>

                                {{-- Tombol Tim Security --}}
                                <button type="button" 
                                        @click="toggleTeam('Tim Security')"
                                        :class="hasTeam('Tim Security') 
                                            ? 'bg-red-50 border-[#8F0A0D] text-[#8F0A0D] font-bold shadow-2xs ring-1 ring-[#8F0A0D]/20' 
                                            : 'bg-white border-slate-200 text-slate-700 hover:bg-slate-50 hover:border-slate-300 font-semibold'"
                                        class="px-3 py-1.5 rounded-xl border text-xs flex items-center gap-1.5 transition cursor-pointer select-none">
                                    <template x-if="hasTeam('Tim Security')">
                                        <svg class="w-3.5 h-3.5 text-[#8F0A0D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    </template>
                                    <template x-if="!hasTeam('Tim Security')">
                                        <span class="w-3.5 h-3.5 rounded-full border border-slate-300 inline-block"></span>
                                    </template>
                                    <span>Tim Security</span>
                                </button>

                                {{-- Tag Tim Kustom Hasil Ketik Manual --}}
                                <template x-for="t in selectedTeams.filter(x => x !== 'Tim Network' && x !== 'Tim Security')" :key="t">
                                    <span class="px-2.5 py-1.5 rounded-xl border border-red-200 bg-red-50 text-[#8F0A0D] text-xs font-bold flex items-center gap-1.5 shadow-2xs">
                                        <svg class="w-3.5 h-3.5 text-[#8F0A0D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                        <span x-text="t"></span>
                                        <button type="button" @click="removeTeam(t)" class="text-red-400 hover:text-red-700 ml-0.5 cursor-pointer" title="Hapus tim ini">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </span>
                                </template>
                            </div>

                            {{-- Input Tambah Tim Baru (Ketik Manual) --}}
                            <div class="flex items-center gap-1.5 pt-1">
                                <div class="relative flex-1">
                                    <input type="text" 
                                           x-model="newCustomTeam"
                                           @keydown.enter.prevent="addCustomTeam()"
                                           placeholder="+ Ketik manual tim lain (misal: Tim Fiber Optic, Cloud)..."
                                           class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-800 placeholder-slate-400 bg-slate-50/60 focus:bg-white focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 outline-none transition">
                                </div>
                                <button type="button" 
                                        @click="addCustomTeam()"
                                        :disabled="!newCustomTeam.trim()"
                                        :class="newCustomTeam.trim() ? 'bg-slate-800 text-white hover:bg-slate-900 cursor-pointer shadow-2xs' : 'bg-slate-100 text-slate-400 cursor-not-allowed'"
                                        class="px-3 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1 shrink-0">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                    <span>Tambah</span>
                                </button>
                            </div>

                            {{-- Hidden inputs pengirim array tim ke backend --}}
                            <template x-for="t in selectedTeams" :key="t">
                                <input type="hidden" name="teams[]" :value="t">
                            </template>

                            {{-- Pesan Peringatan Jika Belum Memilih Tim --}}
                            <p x-show="teamError" x-text="teamError" class="text-[11px] text-rose-600 font-semibold"></p>
                        </div>

                        {{-- Kolom Project Manager --}}
                        <div class="space-y-1">
                            <label class="block text-xs font-bold text-slate-800">
                                Project Manager (PM PIC) <span class="text-rose-600">*</span>
                            </label>
                            <select name="pm_id" 
                                    required 
                                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-white focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 outline-none cursor-pointer transition">
                                <option value="">-- Pilih Project Manager --</option>
                                @foreach($pmList as $pm)
                                    <option value="{{ $pm->id }}" {{ (auth()->id() == $pm->id || str_contains(strtolower($pm->name), 'rizki')) ? 'selected' : '' }}>
                                        {{ $pm->name }} ({{ $pm->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Baris 3: Nilai Kontrak, No SPK/PO, Tipe Proyek --}}
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="space-y-1">
                            <label class="block text-xs font-bold text-slate-800">
                                Nilai Kontrak / Budget (Rp)
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-xs font-bold text-slate-400 select-none">
                                    Rp
                                </span>
                                <input type="text" 
                                       inputmode="numeric"
                                       x-model="contractValueDisplay"
                                       @input="formatContractValue($event.target.value)"
                                       @keydown="if (!/[0-9]/.test($event.key) && !['Backspace', 'Delete', 'ArrowLeft', 'ArrowRight', 'Tab', 'Home', 'End'].includes($event.key) && !($event.ctrlKey || $event.metaKey)) $event.preventDefault();"
                                       placeholder="0"
                                       autocomplete="off"
                                       class="w-full pl-10 pr-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 outline-none transition">
                                <input type="hidden" name="contract_value" :value="rawContractValue">
                            </div>
                        </div>

                        <div class="space-y-1">
                            <label class="block text-xs font-bold text-slate-800">
                                No. PO / SPK / Kontrak
                            </label>
                            <input type="text" 
                                   name="po_spk_number" 
                                   placeholder="Contoh: PO-2026-DIR-001"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-800 focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 outline-none transition">
                        </div>

                        <div class="space-y-1">
                            <label class="block text-xs font-bold text-slate-800">
                                Tipe Proyek
                            </label>
                            <select name="project_type" 
                                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-white focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 outline-none cursor-pointer transition">
                                <option value="One-Time Project">One-Time Project</option>
                                <option value="Turnkey Deployment">Turnkey Deployment</option>
                                <option value="Hardware Installation & Cabling">Hardware Installation & Cabling</option>
                                <option value="Implementation + Managed Service">Implementation + Managed Service</option>
                            </select>
                        </div>
                    </div>

                    {{-- Baris 4: Tanggal Mulai & Deadline --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="block text-xs font-bold text-slate-800">
                                Tanggal Mulai Proyek
                            </label>
                            <input type="date" 
                                   name="start_date" 
                                   value="{{ date('Y-m-d') }}"
                                   class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs text-slate-800 focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 outline-none cursor-pointer transition">
                        </div>

                        <div class="space-y-1">
                            <label class="block text-xs font-bold text-slate-800">
                                Target Selesai (Deadline)
                            </label>
                            <input type="date" 
                                   name="deadline" 
                                   value="{{ date('Y-m-d', strtotime('+30 days')) }}"
                                   class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs text-slate-800 focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 outline-none cursor-pointer transition">
                        </div>
                    </div>

                    {{-- Baris 5: Deskripsi Scope --}}
                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-slate-800">
                            Deskripsi / Ruang Lingkup Singkat
                        </label>
                        <textarea name="description" 
                                  rows="2" 
                                  placeholder="Ringkasan ruang lingkup pekerjaan atau catatan implementasi proyek..."
                                  class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs text-slate-800 focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 outline-none transition"></textarea>
                    </div>

                    </div>

                    {{-- Footer Bar Edge-to-Edge Konsisten Modal Lainnya --}}
                    <div class="px-6 py-3.5 border-t border-[#E2E8F0] bg-[#F8FAFC] flex items-center justify-end gap-2.5 shrink-0">
                        <button type="button" @click="showCreateProjectModal = false" class="px-4 py-2.5 min-h-[38px] text-xs font-bold text-slate-600 hover:bg-slate-100 border border-[#CBD5E1] rounded-xl transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2.5 min-h-[38px] text-xs font-bold rounded-xl btn-ipnet-primary transition shadow-xs flex items-center gap-2 cursor-pointer hover:scale-[1.02] active:scale-95">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Simpan &amp; Buka Canvas Proyek</span>
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
            showCreateProjectModal: false,

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
            signatureMode: '{{ auth()->user()->signature_type ?: "signature_qr" }}',
            msSignatureMode: '{{ auth()->user()->signature_type ?: "signature_qr" }}',
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
                this.msHandoverForm.sla_coverage_hours = project.sla_coverage_hours || '24x7 Non-Stop';
                this.msHandoverForm.maintenance_frequency = project.maintenance_frequency || 'Bulanan (Monthly)';
                this.msHandoverForm.service_start_date = project.service_start_date || '{{ date('Y-m-d') }}';
                
                if (project.service_end_date) {
                    this.msHandoverForm.service_end_date = project.service_end_date.split('T')[0];
                } else {
                    const d = new Date(this.msHandoverForm.service_start_date);
                    d.setFullYear(d.getFullYear() + 1);
                    d.setDate(d.getDate() - 1);
                    this.msHandoverForm.service_end_date = d.toISOString().split('T')[0];
                }

                this.msHandoverForm.special_notes = project.special_notes || 'Pekerjaan implementasi dan UAT fisik telah selesai sesuai spesifikasi SPK. Tim Managed Service dipersilakan melanjutkan monitoring proaktif dan koordinasi jadwal preventive maintenance perdana dengan PIC Klien.';
                this.msSignatureMode = '{{ auth()->user()->signature_type ?: "signature_qr" }}';
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
                    this.msHandoverForm.signature_type = this.msSignatureMode;
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
                    const matchDiv = this.selectedDivision === 'all' || String(p.division_id) === String(this.selectedDivision) || (String(p.division_id) === '4' && (this.selectedDivision === '1' || this.selectedDivision === '2'));
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
