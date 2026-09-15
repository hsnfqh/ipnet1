@extends('layouts.app')

@section('title', 'Daftar Proyek - PT IP Network Solusindo')

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
<div class="flex h-screen overflow-hidden bg-[#F8FAFC] font-sans" x-data="projectsManager()">
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Daftar Proyek'])
        
        <div class="p-4 sm:p-6 lg:p-7 space-y-5 max-w-[1600px] mx-auto">
            
            <!-- ========================================================== -->
            <!-- 1. STATS METRIC CARDS (IPNET CARD STYLE)                   -->
            <!-- ========================================================== -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 anim-fade-up anim-delay-1">
                {{-- Card 1: Total Proyek --}}
                <div class="ipnet-card p-5 flex items-center justify-between">
                    <div>
                        <p class="text-[11.5px] font-bold text-[#64748B] uppercase tracking-wider">Total Proyek</p>
                        <h3 class="text-[22px] font-extrabold text-[#1E293B] mt-1" x-text="projects.length">0</h3>
                        <p class="text-[11.5px] text-[#94A3B8] mt-0.5">Portofolio aktif & selesai</p>
                    </div>
                    <div class="w-11 h-11 rounded-2xl bg-[#FEF2F2] border border-[#FECACA] flex items-center justify-center text-[#8F0A0D] shadow-xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                        </svg>
                    </div>
                </div>

                {{-- Card 2: Proyek Berjalan --}}
                <div class="ipnet-card p-5 flex items-center justify-between">
                    <div>
                        <p class="text-[11.5px] font-bold text-[#64748B] uppercase tracking-wider">Sedang Berjalan</p>
                        <h3 class="text-[22px] font-extrabold text-[#D97706] mt-1" x-text="projects.filter(p => p.status === 'On Progress').length">0</h3>
                        <p class="text-[11.5px] text-[#94A3B8] mt-0.5">Status On Progress</p>
                    </div>
                    <div class="w-11 h-11 rounded-2xl bg-[#FFFBEB] border border-[#FDE68A] flex items-center justify-center text-[#D97706] shadow-xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>

                {{-- Card 3: Proyek Selesai --}}
                <div class="ipnet-card p-5 flex items-center justify-between">
                    <div>
                        <p class="text-[11.5px] font-bold text-[#64748B] uppercase tracking-wider">Proyek Selesai</p>
                        <h3 class="text-[22px] font-extrabold text-[#16A34A] mt-1" x-text="projects.filter(p => p.status === 'Completed').length">0</h3>
                        <p class="text-[11.5px] text-[#94A3B8] mt-0.5">Berhasil diserahterimakan</p>
                    </div>
                    <div class="w-11 h-11 rounded-2xl bg-[#F0FDF4] border border-[#BBF7D0] flex items-center justify-center text-[#16A34A] shadow-xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>

                {{-- Card 4: Planning & Persiapan --}}
                <div class="ipnet-card p-5 flex items-center justify-between">
                    <div>
                        <p class="text-[11.5px] font-bold text-[#64748B] uppercase tracking-wider">Planning / Persiapan</p>
                        <h3 class="text-[22px] font-extrabold text-[#2563EB] mt-1" x-text="projects.filter(p => p.status === 'Planning').length">0</h3>
                        <p class="text-[11.5px] text-[#94A3B8] mt-0.5">Tahap perencanaan awal</p>
                    </div>
                    <div class="w-11 h-11 rounded-2xl bg-[#EFF6FF] border border-[#BFDBFE] flex items-center justify-center text-[#2563EB] shadow-xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- ========================================================== -->
            <!-- 2. SECTION HEADER & FILTER CONTROLS                        -->
            <!-- ========================================================== -->
            <div class="ipnet-card p-5 sm:p-6 anim-fade-up anim-delay-2">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 pb-4 mb-4 border-b border-[#E2E8F0]">
                    <div>
                        <p class="text-[#8F0A0D] text-[12px] font-bold inline-flex items-center uppercase tracking-wider">
                            <span class="ipnet-badge-dot"></span> MANAJEMEN PORTOFOLIO
                        </p>
                        <h2 class="text-[20px] font-bold text-[#1E293B] tracking-tight">Daftar Proyek & Integrasi Lapangan</h2>
                        <p class="text-[13px] text-[#64748B] mt-0.5">Kelola pelaksanaan proyek klien, jadwal visit berkala, dan monitoring progres instalasi</p>
                    </div>

                    @if($canCreate ?? false)
                    <div class="shrink-0">
                        <button @click="openModal()" class="btn-ipnet-gradient px-4 py-2.5 rounded-xl font-bold text-[13px] flex items-center gap-2 cursor-pointer shadow-md">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span>Tambah Proyek Baru</span>
                        </button>
                    </div>
                    @endif
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
                               placeholder="Cari nama project, client, sales, atau lokasi..." 
                               class="w-full pl-9 pr-3.5 py-2 rounded-xl border border-[#CBD5E1] text-[12.5px] font-semibold text-[#1E293B] bg-white outline-none hover:border-[#94A3B8] focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition-all shadow-xs placeholder-[#94A3B8]">
                    </div>
                    
                    <select x-model="statusFilter" @change="currentPage = 1" 
                            class="w-full sm:w-44 px-3 py-2 rounded-xl border border-[#CBD5E1] text-[12.5px] font-semibold text-[#1E293B] bg-white outline-none hover:border-[#94A3B8] focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition-all shadow-xs cursor-pointer">
                        <option value="Semua">Semua Status</option>
                        <option value="Planning">Planning</option>
                        <option value="On Progress">On Progress</option>
                        <option value="Completed">Completed</option>
                    </select>

                    <select x-model="typeFilter" @change="currentPage = 1" 
                            class="w-full sm:w-52 px-3 py-2 rounded-xl border border-[#CBD5E1] text-[12.5px] font-semibold text-[#1E293B] bg-white outline-none hover:border-[#94A3B8] focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition-all shadow-xs cursor-pointer">
                        <option value="Semua">Semua Tipe Proyek</option>
                        <option value="One-Time Project">One-Time Project</option>
                        <option value="Maintenance Berkala">Maintenance Berkala</option>
                        <option value="Managed Service">Managed Service</option>
                    </select>

                    {{-- Reset Button --}}
                    <button type="button"
                            x-show="search || statusFilter !== 'Semua' || typeFilter !== 'Semua'"
                            @click="search = ''; statusFilter = 'Semua'; typeFilter = 'Semua'; currentPage = 1;"
                            class="px-3 py-2 text-[12px] font-bold text-[#64748B] hover:text-[#8F0A0D] bg-[#F8FAFC] hover:bg-[#FEF2F2] border border-[#E2E8F0] hover:border-[#FCA5A5] rounded-xl transition cursor-pointer">
                        Reset Filter
                    </button>
                </div>
            </div>

            <!-- ========================================================== -->
            <!-- 3. PROJECTS TABLE CARD                                     -->
            <!-- ========================================================== -->
            <div class="ipnet-card overflow-hidden anim-fade-up anim-delay-3">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[900px] border-collapse text-[13px] text-left">
                        <thead>
                            <tr class="bg-[#F8FAFC] border-b border-[#E2E8F0]">
                                <th class="py-3.5 px-4 text-[11.5px] font-bold text-[#64748B] uppercase tracking-wider">Nama Project & Tipe</th>
                                <th class="py-3.5 px-4 text-[11.5px] font-bold text-[#64748B] uppercase tracking-wider">Client & Sales</th>
                                <th class="py-3.5 px-4 text-[11.5px] font-bold text-[#64748B] uppercase tracking-wider">Lokasi</th>
                                <th class="py-3.5 px-4 text-[11.5px] font-bold text-[#64748B] uppercase tracking-wider">Durasi & Deadline</th>
                                <th class="py-3.5 px-4 text-[11.5px] font-bold text-[#64748B] uppercase tracking-wider">Progress</th>
                                <th class="py-3.5 px-4 text-[11.5px] font-bold text-[#64748B] uppercase tracking-wider">Status</th>
                                <th class="py-3.5 px-4 text-[11.5px] font-bold text-[#64748B] uppercase tracking-wider text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#F1F5F9]">
                            <template x-for="project in paginatedProjects" :key="project.id">
                                <tr class="hover:bg-[#F8FAFC]/80 transition-colors duration-150">
                                    {{-- Nama Project & Tipe --}}
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-[#1E293B] text-[13.5px] leading-snug" x-text="project.name"></div>
                                        <div class="flex items-center gap-1.5 mt-1">
                                            <template x-if="project.visit_schedule && project.visit_schedule !== 'None' && project.visit_schedule !== '-'">
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10.5px] font-bold bg-[#EEF2FF] text-[#4F46E5] border border-[#E0E7FF]" title="Proyek dengan jadwal visit berkala">
                                                    <svg class="w-3 h-3 text-[#4F46E5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                                    <span x-text="'Visit: ' + project.visit_schedule"></span>
                                                </span>
                                            </template>
                                            <template x-if="!project.visit_schedule || project.visit_schedule === 'None' || project.visit_schedule === '-'">
                                                <span class="inline-block text-[11px] font-medium text-[#64748B] bg-[#F1F5F9] px-2 py-0.5 rounded border border-[#E2E8F0]" x-text="project.project_type || 'One-Time Project'"></span>
                                            </template>
                                        </div>
                                    </td>

                                    {{-- Client & Sales --}}
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-[#1E293B] text-[13px]" x-text="project.client"></div>
                                        <div class="text-[11.5px] text-[#64748B] flex items-center gap-1.5 mt-0.5">
                                            <span class="text-[#94A3B8] font-medium">Sales:</span>
                                            <span class="font-bold text-[#334155]" x-text="project.sales_name || '-'"></span>
                                        </div>
                                    </td>

                                    {{-- Lokasi --}}
                                    <td class="py-3.5 px-4 text-[#334155] text-[12.5px]" x-text="project.location"></td>

                                    {{-- Durasi & Deadline --}}
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <div class="font-mono text-[12px] font-bold text-[#1E293B]" x-text="formatDeadline(project.deadline)"></div>
                                        <div class="text-[11px] text-[#64748B] mt-0.5 flex items-center gap-1">
                                            <span>Durasi:</span>
                                            <span class="font-extrabold text-[#8F0A0D]" x-text="getDurationText(project)"></span>
                                        </div>
                                    </td>

                                    {{-- Progress --}}
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <div class="w-28">
                                            <div class="w-full bg-[#F1F5F9] border border-[#E2E8F0] rounded-full h-2 overflow-hidden mb-1">
                                                <div class="h-full rounded-full transition-all duration-300"
                                                     style="background: linear-gradient(90deg, #8F0A0D, #D62E3C);"
                                                     :style="{ width: getProjectProgress(project) + '%' }"></div>
                                            </div>
                                            <div class="flex items-center justify-between text-[10.5px]">
                                                <span class="font-extrabold text-[#1E293B]" x-text="getProjectProgress(project) + '%'"></span>
                                                <span class="text-[#94A3B8]" x-text="project.status === 'Completed' ? 'Selesai' : 'Progres'"></span>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Status --}}
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <span x-html="getStatusBadge(project.status)"></span>
                                    </td>

                                    {{-- Aksi --}}
                                    <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                        <div class="flex justify-end items-center gap-1">
                                            {{-- Detail --}}
                                            <button @click="viewProject(project)" title="Lihat Detail Project" class="p-1.5 rounded-lg text-[#64748B] hover:text-[#1E293B] hover:bg-[#F1F5F9] transition cursor-pointer">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </button>
                                            
                                            @if($canCreate ?? false)
                                            {{-- Edit --}}
                                            <button @click="editProject(project)" title="Edit Project" class="p-1.5 rounded-lg text-[#64748B] hover:text-[#1E293B] hover:bg-[#F1F5F9] transition cursor-pointer">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </button>
                                            
                                            {{-- Delete --}}
                                            <button @click="confirmDelete(project)" title="Hapus Project" class="p-1.5 rounded-lg text-[#8F0A0D] hover:bg-[#FEF2F2] transition cursor-pointer">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
                
                {{-- Empty State --}}
                <div x-show="filteredProjects.length === 0" class="text-center py-12 text-[#64748B]">
                    <div class="w-12 h-12 rounded-2xl bg-[#F8FAFC] border border-[#E2E8F0] flex items-center justify-center mx-auto mb-3 text-[#94A3B8]">
                        <svg class="w-6 h-6 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                        </svg>
                    </div>
                    <h4 class="text-[14px] font-bold text-[#1E293B]">Tidak Ada Data Project</h4>
                    <p class="text-[12.5px] text-[#64748B] mt-1">Tidak ada project yang sesuai dengan kata kunci pencarian atau filter.</p>
                </div>

                {{-- Pagination Footer --}}
                <div class="p-3.5 sm:px-5 sm:py-3.5 border-t border-[#E2E8F0] bg-white flex flex-col sm:flex-row items-center justify-between gap-3 text-[12px] text-[#64748B]">
                    <div class="flex items-center gap-2 flex-wrap justify-center sm:justify-start">
                        <span>Tampilkan:</span>
                        <select x-model="perPage" @change="currentPage = 1" class="px-2 py-1 bg-white border border-[#CBD5E1] rounded-lg text-[12px] font-bold text-[#1E293B] outline-none hover:border-[#94A3B8] transition cursor-pointer">
                            <option value="10">10</option>
                            <option value="20">20</option>
                            <option value="30">30</option>
                            <option value="all">Semua</option>
                        </select>
                        <span class="text-[#CBD5E1]">&bull;</span>
                        <div>
                            Menampilkan 
                            <strong class="text-[#1E293B]" x-text="filteredProjects.length > 0 ? (perPage === 'all' ? 1 : (currentPage - 1) * (parseInt(perPage) || 10) + 1) : 0"></strong> &ndash; 
                            <strong class="text-[#1E293B]" x-text="perPage === 'all' ? filteredProjects.length : Math.min(currentPage * (parseInt(perPage) || 10), filteredProjects.length)"></strong> 
                            dari <strong class="text-[#1E293B]" x-text="filteredProjects.length"></strong> project
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-1.5" x-show="totalPages > 1 && perPage !== 'all'">
                        <button @click="prevPage()" :disabled="currentPage === 1" title="Sebelumnya" class="w-8 h-8 rounded-lg border border-[#CBD5E1] hover:bg-[#F8FAFC] text-[#334155] disabled:opacity-40 disabled:cursor-not-allowed flex items-center justify-center transition cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </button>
                        <template x-for="p in totalPages" :key="p">
                            <button @click="goToPage(p)" 
                                    :class="currentPage === p ? 'btn-ipnet-gradient shadow-xs' : 'bg-white text-[#1E293B] border border-[#CBD5E1] hover:bg-[#F8FAFC]'"
                                    class="w-8 h-8 rounded-lg text-xs font-bold flex items-center justify-center transition cursor-pointer"
                                    x-text="p">
                            </button>
                        </template>
                        <button @click="nextPage()" :disabled="currentPage === totalPages" title="Berikutnya" class="w-8 h-8 rounded-lg border border-[#CBD5E1] hover:bg-[#F8FAFC] text-[#334155] disabled:opacity-40 disabled:cursor-not-allowed flex items-center justify-center transition cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- ============================================================ -->
            <!-- CREATE / EDIT MODAL                                          -->
            <!-- ============================================================ -->
            <template x-teleport="body">
                <div x-show="modalOpen" 
                     x-cloak
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-[#0F172A]/60 z-50 flex items-center justify-center p-4 sm:p-6 backdrop-blur-xs"
                     @click.self="modalOpen = false">
                    <div class="bg-white rounded-2xl w-[580px] max-w-full max-h-[85vh] flex flex-col overflow-hidden animate-fade-in-up shadow-[0_20px_60px_rgba(15,23,42,0.25)] border border-[#E2E8F0]">
                        {{-- Modal Header --}}
                        <div class="flex items-center justify-between p-4 sm:p-5 border-b border-[#E2E8F0] flex-shrink-0 bg-[#F8FAFC]">
                            <div>
                                <h3 class="font-display text-[16px] font-bold text-[#1E293B]" x-text="modalTitle"></h3>
                                <p class="text-[12px] text-[#64748B] mt-0.5">Kelola informasi proyek, tim sales, dan jadwal pemeliharaan berkala.</p>
                            </div>
                            <button @click="modalOpen = false" class="rounded-lg p-1.5 text-[#64748B] hover:text-[#1E293B] hover:bg-[#E2E8F0] transition-colors cursor-pointer">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        {{-- Modal Body --}}
                        <div class="p-5 sm:p-6 overflow-y-auto flex-1">
                            <form id="projectForm" @submit.prevent="saveProject()" class="space-y-4 text-[13px]">
                                <div>
                                    <label class="block text-[11.5px] font-bold text-[#475569] uppercase tracking-wider mb-1.5">Nama Project <span class="text-[#8F0A0D]">*</span></label>
                                    <input type="text" x-model="form.name" required class="w-full py-2.5 px-3.5 text-[13px] bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 focus:bg-white transition text-[#1E293B]" placeholder="Masukkan nama project lengkap">
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                    <div>
                                        <label class="block text-[11.5px] font-bold text-[#475569] uppercase tracking-wider mb-1.5">Klien / Pemilik Proyek <span class="text-[#8F0A0D]">*</span></label>
                                        <input type="text" x-model="form.client" required class="w-full py-2.5 px-3.5 text-[13px] bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 focus:bg-white transition text-[#1E293B]" placeholder="Contoh: PT Bank Central Asia Tbk">
                                    </div>
                                    <div>
                                        <label class="block text-[11.5px] font-bold text-[#475569] uppercase tracking-wider mb-1.5">Nama Sales / PIC Sales <span class="text-[#8F0A0D]">*</span></label>
                                        <input type="text" x-model="form.sales_name" required class="w-full py-2.5 px-3.5 text-[13px] bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 focus:bg-white transition text-[#1E293B]" placeholder="Contoh: Donny / Erie / Hendry">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                    <div>
                                        <label class="block text-[11.5px] font-bold text-[#475569] uppercase tracking-wider mb-1.5">Tipe Proyek <span class="text-[#8F0A0D]">*</span></label>
                                        <select x-model="form.project_type" class="w-full py-2.5 px-3.5 text-[13px] bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 focus:bg-white transition text-[#1E293B]">
                                            <option value="One-Time Project">One-Time Project / Deployment</option>
                                            <option value="Maintenance Berkala">Maintenance Berkala / SLA</option>
                                            <option value="Managed Service">Managed Service & Support</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[11.5px] font-bold text-[#475569] uppercase tracking-wider mb-1.5">Jadwal Visit Berkala</label>
                                        <select x-model="form.visit_schedule" class="w-full py-2.5 px-3.5 text-[13px] bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 focus:bg-white transition text-[#1E293B]">
                                            <option value="None">None (Tidak Ada Jadwal Visit Rutin)</option>
                                            <option value="Mingguan (Weekly)">Mingguan (Weekly Visit)</option>
                                            <option value="Bulanan (Monthly)">Bulanan (Monthly SLA Visit)</option>
                                            <option value="Triwulanan (Quarterly)">Triwulanan (Quarterly Check)</option>
                                            <option value="Semesteran (Semi-Annual)">Semesteran (6 Bulanan)</option>
                                            <option value="On-Call (Incidental)">On-Call (Sesuai Permintaan)</option>
                                        </select>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-[11.5px] font-bold text-[#475569] uppercase tracking-wider mb-1.5">Lokasi <span class="text-[#8F0A0D]">*</span></label>
                                    <input type="text" x-model="form.location" required class="w-full py-2.5 px-3.5 text-[13px] bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 focus:bg-white transition text-[#1E293B]" placeholder="Gedung / Data Center / Alamat Project">
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                    <div>
                                        <label class="block text-[11.5px] font-bold text-[#475569] uppercase tracking-wider mb-1.5">Tanggal Mulai <span class="text-[#8F0A0D]">*</span></label>
                                        <input type="date" x-model="form.start_date" required class="w-full py-2.5 px-3 text-[13px] bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 focus:bg-white transition text-[#1E293B]">
                                    </div>
                                    <div>
                                        <label class="block text-[11.5px] font-bold text-[#475569] uppercase tracking-wider mb-1.5">Deadline / Akhir Kontrak <span class="text-[#8F0A0D]">*</span></label>
                                        <input type="date" x-model="form.deadline" required class="w-full py-2.5 px-3 text-[13px] bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 focus:bg-white transition text-[#1E293B]">
                                    </div>
                                </div>

                                <template x-if="form.start_date && form.deadline">
                                    <div class="p-3 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0] text-[12px] text-[#334155] flex items-center justify-between">
                                        <span>Estimasi Durasi Proyek:</span>
                                        <span class="font-extrabold text-[#8F0A0D]" x-text="calculateFormDuration()"></span>
                                    </div>
                                </template>

                                <div>
                                    <label class="block text-[11.5px] font-bold text-[#475569] uppercase tracking-wider mb-1.5">Deskripsi & Catatan SLA</label>
                                    <textarea x-model="form.description" rows="3" class="w-full py-2.5 px-3.5 text-[13px] bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 focus:bg-white transition text-[#1E293B] resize-none" placeholder="Deskripsi teknis, ruang lingkup SLA, atau catatan project..."></textarea>
                                </div>
                            </form>
                        </div>

                        {{-- Modal Footer --}}
                        <div class="flex items-center gap-3 p-4 sm:px-6 sm:py-3.5 border-t border-[#E2E8F0] bg-[#F8FAFC] flex-shrink-0">
                            <button type="button" 
                                    @click="modalOpen = false" 
                                    class="flex-1 flex items-center justify-center min-h-[40px] bg-white hover:bg-[#F8FAFC] text-[#334155] border border-[#CBD5E1] px-4 py-2 rounded-xl font-bold text-[13px] transition-all cursor-pointer">
                                Batal
                            </button>
                            <button type="submit" 
                                    form="projectForm" 
                                    class="btn-ipnet-gradient flex-1 flex items-center justify-center min-h-[40px] px-4 py-2 rounded-xl font-bold text-[13px] cursor-pointer shadow-md">
                                <span x-text="editing ? 'Simpan Perubahan' : 'Simpan Project'">Simpan Project</span>
                            </button>
                        </div>
                    </div>
                </div>
            </template>

            <!-- ============================================================ -->
            <!-- DETAIL MODAL                                                 -->
            <!-- ============================================================ -->
            <template x-teleport="body">
                <div x-show="detailOpen" 
                     x-cloak
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-[#0F172A]/60 z-50 flex items-center justify-center p-3 sm:p-5 backdrop-blur-xs"
                     @click.self="detailOpen = false">
                    <div class="bg-white rounded-2xl w-[600px] max-w-full max-h-[90vh] flex flex-col animate-fade-in-up shadow-[0_20px_60px_rgba(15,23,42,0.25)] overflow-hidden border border-[#E2E8F0]">
                        
                        {{-- Modal Header --}}
                        <div class="flex items-start justify-between p-5 sm:p-6 bg-[#F8FAFC] border-b border-[#E2E8F0] flex-shrink-0">
                            <div class="min-w-0 flex-1 pr-3">
                                <div class="flex flex-wrap items-center gap-2 mb-1.5">
                                    <span class="text-[10.5px] font-extrabold uppercase tracking-wider text-[#8F0A0D] bg-[#FEF2F2] border border-[#FECACA] px-2 py-0.5 rounded-md">Detail Project</span>
                                    <span x-html="detailProject ? getStatusBadge(detailProject.status) : ''"></span>
                                    <template x-if="detailProject?.visit_schedule && detailProject?.visit_schedule !== 'None' && detailProject?.visit_schedule !== '-'">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10.5px] font-bold bg-[#EEF2FF] text-[#4F46E5] border border-[#E0E7FF]">
                                            <svg class="w-3 h-3 text-[#4F46E5]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                            </svg>
                                            <span>Visit: <span x-text="detailProject.visit_schedule"></span></span>
                                        </span>
                                    </template>
                                </div>
                                <h3 class="font-display text-[17px] sm:text-[19px] font-bold text-[#1E293B] leading-snug" x-text="detailProject?.name"></h3>
                                <p class="text-[12.5px] text-[#64748B] font-medium mt-0.5" x-text="detailProject?.client"></p>
                            </div>
                            <button @click="detailOpen = false" class="rounded-lg p-1.5 text-[#64748B] hover:text-[#1E293B] hover:bg-[#E2E8F0] transition-colors cursor-pointer">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        {{-- Modal Body --}}
                        <div class="p-5 sm:p-6 overflow-y-auto space-y-4 flex-1 text-[13px]">
                            
                            {{-- Overall Progress Card --}}
                            <div class="bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl p-4">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-[11.5px] font-bold text-[#64748B] uppercase tracking-wider">Progress Keseluruhan</span>
                                    <span class="text-[14px] font-extrabold text-[#8F0A0D]" x-text="detailProject ? getProjectProgress(detailProject) + '%' : '0%'"></span>
                                </div>
                                <div class="w-full bg-[#E2E8F0] rounded-full h-2.5 overflow-hidden">
                                    <div class="h-full rounded-full transition-all duration-300"
                                         style="background: linear-gradient(90deg, #8F0A0D, #D62E3C);"
                                         :style="{ width: (detailProject ? getProjectProgress(detailProject) : 0) + '%' }"></div>
                                </div>
                            </div>

                            {{-- Metadata Grid --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div class="border border-[#E2E8F0] rounded-xl p-3.5 bg-white">
                                    <div class="text-[11px] font-bold text-[#64748B] uppercase tracking-wider mb-1 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-[#8F0A0D]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        Lokasi Project
                                    </div>
                                    <div class="font-bold text-[#1E293B]" x-text="detailProject?.location || '-'"></div>
                                </div>

                                <div class="border border-[#E2E8F0] rounded-xl p-3.5 bg-white">
                                    <div class="text-[11px] font-bold text-[#64748B] uppercase tracking-wider mb-1 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-[#8F0A0D]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        Sales / Account Exec
                                    </div>
                                    <div class="font-bold text-[#1E293B]" x-text="detailProject?.sales_name || 'Tidak Ditentukan'"></div>
                                </div>

                                <div class="border border-[#E2E8F0] rounded-xl p-3.5 bg-white">
                                    <div class="text-[11px] font-bold text-[#64748B] uppercase tracking-wider mb-1 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-[#8F0A0D]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                        Tipe & Jadwal Visit
                                    </div>
                                    <div class="font-bold text-[#1E293B]">
                                        <span x-text="detailProject?.project_type || 'One-Time Project'"></span>
                                        <template x-if="detailProject?.visit_schedule && detailProject.visit_schedule !== 'None'">
                                            <span class="text-[#4F46E5] block text-[11.5px] mt-0.5" x-text="'• Visit: ' + detailProject.visit_schedule"></span>
                                        </template>
                                    </div>
                                </div>

                                <div class="border border-[#E2E8F0] rounded-xl p-3.5 bg-white">
                                    <div class="text-[11px] font-bold text-[#64748B] uppercase tracking-wider mb-1 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-[#8F0A0D]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        Periode & Total Durasi
                                    </div>
                                    <div class="text-[#1E293B] font-mono text-[12px]">
                                        <span x-text="formatDeadline(detailProject?.start_date)"></span> &ndash; <span class="font-bold text-[#8F0A0D]" x-text="formatDeadline(detailProject?.deadline)"></span>
                                        <div class="text-[11.5px] font-sans font-bold text-[#8F0A0D] mt-0.5" x-text="'Total: ' + (detailProject ? getDurationText(detailProject) : '-')"></div>
                                    </div>
                                </div>
                            </div>

                            {{-- Deskripsi --}}
                            <div class="border border-[#E2E8F0] rounded-xl p-3.5 bg-white">
                                <div class="text-[11px] font-bold text-[#64748B] uppercase tracking-wider mb-1.5">Deskripsi Proyek & SLA</div>
                                <p class="text-[#334155] leading-relaxed break-words text-[13px] whitespace-pre-line" x-text="detailProject?.description || 'Tidak ada deskripsi tambahan.'"></p>
                            </div>

                            {{-- Personel & Tim Lapangan Terlibat --}}
                            <div class="border border-[#E2E8F0] rounded-xl p-3.5 bg-white" x-show="detailProject?.tasks && detailProject.tasks.length > 0">
                                <div class="text-[11px] font-bold text-[#64748B] uppercase tracking-wider mb-2.5 flex items-center justify-between">
                                    <span>Personel & Tugas Terkait</span>
                                    <span class="text-[11px] font-bold text-[#8F0A0D]" x-text="(detailProject?.tasks?.length || 0) + ' Tugas Terkait'"></span>
                                </div>
                                <div class="space-y-2 max-h-48 overflow-y-auto">
                                    <template x-for="task in detailProject?.tasks" :key="task.id">
                                        <div class="flex items-center justify-between p-2.5 rounded-lg bg-[#F8FAFC] border border-[#E2E8F0] text-[12.5px]">
                                            <div class="min-w-0 flex-1 pr-2">
                                                <div class="font-bold text-[#1E293B] truncate" x-text="task.title"></div>
                                                <div class="text-[11px] text-[#64748B] flex items-center gap-1.5 mt-0.5">
                                                    <span class="font-semibold text-[#475569]" x-text="task.engineer?.name || 'Belum Ditugaskan'"></span>
                                                    <span>&bull;</span>
                                                    <span :class="task.status === 'Completed' ? 'text-[#16A34A] font-bold' : (task.status === 'In Progress' ? 'text-[#D97706] font-bold' : 'text-[#2563EB] font-bold')" x-text="task.status"></span>
                                                </div>
                                            </div>
                                            <span class="text-[11px] font-mono font-bold text-[#1E293B] bg-white px-2 py-0.5 rounded border border-[#CBD5E1]" x-text="(task.progress || 0) + '%'"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            <!-- ============================================================ -->
            <!-- CONFIRM DELETE MODAL                                         -->
            <!-- ============================================================ -->
            <template x-teleport="body">
                <div x-show="confirmOpen" 
                     x-cloak
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-[#0F172A]/60 z-[99999] flex items-center justify-center p-4 backdrop-blur-xs"
                     @click.self="confirmOpen = false"
                     @keydown.escape.window="confirmOpen = false">
                    
                    <div class="bg-white rounded-2xl w-[420px] max-w-full p-6 text-left shadow-[0_20px_60px_rgba(15,23,42,0.25)] animate-fade-in-up border border-[#E2E8F0]">
                        <div class="w-12 h-12 rounded-full bg-[#FEF2F2] flex items-center justify-center mx-auto mb-4 text-[#8F0A0D]">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </div>

                        <h3 class="text-center font-display text-[16px] font-bold text-[#1E293B] mb-1.5">Yakin Hapus Project?</h3>
                        <p class="text-center text-[12.5px] text-[#64748B] mb-6 break-words" x-text="'Project &quot;' + (confirmData ? confirmData.name : '') + '&quot; akan dihapus secara permanen.'"></p>

                        <div class="flex gap-2.5">
                            <button type="button" @click="confirmOpen = false" class="flex-1 py-2.5 px-4 rounded-xl bg-white text-[#334155] border border-[#CBD5E1] font-bold text-[12.5px] hover:bg-[#F8FAFC] transition cursor-pointer">
                                Batal
                            </button>
                            <button type="button" @click="confirmDeleteAction()" class="flex-1 py-2.5 px-4 rounded-xl btn-ipnet-gradient font-bold text-[12.5px] transition cursor-pointer shadow-md">
                                Hapus
                            </button>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('projectsManager', () => ({
            projects: @json($projects),
            search: '',
            statusFilter: 'Semua',
            typeFilter: 'Semua',
            currentPage: 1,
            perPage: 10,
            modalOpen: false,
            detailOpen: false,
            editing: false,
            confirmOpen: false,
            confirmData: null,
            form: {
                id: null,
                name: '',
                client: '',
                sales_name: '',
                project_type: 'One-Time Project',
                visit_schedule: 'None',
                location: '',
                description: '',
                start_date: '',
                deadline: ''
            },
            detailProject: null,

            get filteredProjects() {
                return this.projects.filter(p => {
                    const s = this.search.toLowerCase();
                    const matchSearch = (p.name || '').toLowerCase().includes(s) ||
                                       (p.client || '').toLowerCase().includes(s) ||
                                       (p.sales_name || '').toLowerCase().includes(s) ||
                                       (p.location || '').toLowerCase().includes(s);
                    const matchStatus = this.statusFilter === 'Semua' || p.status === this.statusFilter;
                    const matchType = this.typeFilter === 'Semua' || (p.project_type || 'One-Time Project') === this.typeFilter;
                    return matchSearch && matchStatus && matchType;
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

            goToPage(page) { this.currentPage = page; },
            prevPage() { if (this.currentPage > 1) this.currentPage--; },
            nextPage() { if (this.currentPage < this.totalPages) this.currentPage++; },

            get modalTitle() {
                return this.editing ? 'Edit Project' : 'Tambah Project Baru';
            },

            calculateFormDuration() {
                if (!this.form.start_date || !this.form.deadline) return '-';
                const s = new Date(this.form.start_date);
                const d = new Date(this.form.deadline);
                const diffTime = d - s;
                if (diffTime < 0) return 'Deadline harus setelah tanggal mulai';
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
                if (diffDays < 30) return diffDays + ' Hari';
                const months = Math.round((diffDays / 30) * 10) / 10;
                return months + ' Bulan (' + diffDays + ' Hari)';
            },

            getDurationText(project) {
                if (project.duration_formatted) return project.duration_formatted;
                if (!project.start_date || !project.deadline) return '-';
                const s = new Date(project.start_date);
                const d = new Date(project.deadline);
                const diffTime = d - s;
                if (diffTime < 0) return '-';
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
                if (diffDays < 30) return diffDays + ' Hari';
                const months = Math.round((diffDays / 30) * 10) / 10;
                return months + ' Bulan';
            },

            openModal(project = null) {
                if (project) {
                    this.editing = true;
                    this.form = { 
                        id: project.id,
                        name: project.name || '',
                        client: project.client || '',
                        sales_name: project.sales_name || '',
                        project_type: project.project_type || 'One-Time Project',
                        visit_schedule: project.visit_schedule || 'None',
                        location: project.location || '',
                        description: project.description || '',
                        start_date: project.start_date ? String(project.start_date).substring(0, 10) : '',
                        deadline: project.deadline ? String(project.deadline).substring(0, 10) : ''
                    };
                } else {
                    this.editing = false;
                    this.form = {
                        id: null,
                        name: '',
                        client: '',
                        sales_name: '',
                        project_type: 'One-Time Project',
                        visit_schedule: 'None',
                        location: '',
                        description: '',
                        start_date: '',
                        deadline: ''
                    };
                }
                this.modalOpen = true;
            },

            editProject(project) {
                this.openModal(project);
            },

            viewProject(project) {
                this.detailProject = project;
                this.detailOpen = true;
            },

            confirmDelete(project) {
                this.confirmData = project;
                this.confirmOpen = true;
            },

            confirmDeleteAction() {
                if (this.confirmData) {
                    this.deleteProject(this.confirmData);
                }
                this.confirmOpen = false;
            },

            async saveProject() {
                try {
                    const url = this.editing ? `/projects/${this.form.id}` : '/projects';
                    const method = this.editing ? 'PUT' : 'POST';
                    
                    const response = await fetch(url, {
                        method: method,
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify(this.form)
                    });

                    if (response.ok) {
                        const data = await response.json();
                        if (this.editing) {
                            const index = this.projects.findIndex(p => p.id === this.form.id);
                            if (index !== -1) {
                                this.projects[index] = { ...this.projects[index], ...data };
                            }
                        } else {
                            this.projects.unshift(data);
                        }
                        this.projects = JSON.parse(JSON.stringify(this.projects));
                        this.modalOpen = false;
                        this.showToast('Project berhasil ' + (this.editing ? 'diperbarui' : 'ditambahkan') + '!');
                    } else {
                        const error = await response.json();
                        this.showToast('Error: ' + (error.message || 'Terjadi kesalahan'));
                    }
                } catch (error) {
                    console.error('Error saving project:', error);
                    this.showToast('Terjadi kesalahan saat menyimpan project.');
                }
            },

            async deleteProject(project) {
                try {
                    const response = await fetch(`/projects/${project.id}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    if (response.ok) {
                        this.projects = this.projects.filter(p => p.id !== project.id);
                        this.showToast('Project berhasil dihapus!');
                    } else {
                        const error = await response.json();
                        console.error('Error response:', error);
                        this.showToast('Error: ' + (error.message || 'Gagal menghapus project'));
                    }
                } catch (error) {
                    console.error('Error deleting project:', error);
                    this.showToast('Terjadi kesalahan saat menghapus project.');
                }
            },

            getStatusBadge(status) {
                const styles = {
                    'Planning': { bg: '#F1F5F9', fg: '#475569', border: '#E2E8F0', dot: '#94A3B8' },
                    'On Progress': { bg: '#FFFBEB', fg: '#D97706', border: '#FDE68A', dot: '#D97706' },
                    'Completed': { bg: '#F0FDF4', fg: '#16A34A', border: '#BBF7D0', dot: '#16A34A' }
                };
                const s = styles[status] || styles['Planning'];
                return `<span style="background: ${s.bg}; color: ${s.fg}; border: 1px solid ${s.border}; font-size: 11.5px; font-weight: 700; padding: 3.5px 9px 3.5px 7px; border-radius: 9999px; white-space: nowrap; display: inline-flex; align-items: center; gap: 5px;">
                            <span style="width: 6px; height: 6px; border-radius: 50%; background: ${s.dot}; flex-shrink: 0;"></span>
                            ${status}
                        </span>`;
            },

            formatDeadline(dateStr) {
                if (!dateStr) return '-';
                const d = new Date(dateStr);
                if (isNaN(d.getTime())) return dateStr;
                const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
                return String(d.getUTCDate()).padStart(2,'0') + ' ' + months[d.getUTCMonth()] + ' ' + d.getUTCFullYear();
            },

            getProjectProgress(project) {
                if (project.status === 'Completed') return 100;
                if (!project.tasks || project.tasks.length === 0) return 0;
                const total = project.tasks.reduce((sum, t) => sum + (parseInt(t.progress) || 0), 0);
                return Math.round(total / project.tasks.length);
            },

            showToast(message) {
                const toast = document.createElement('div');
                toast.style.cssText = 'position:fixed; bottom:20px; right:20px; background:#1E293B; color:white; padding:12px 20px; border-radius:12px; box-shadow:0 16px 40px rgba(15,23,42,0.25); font-size:13px; font-weight:600; animation:fadeUpStagger 0.25s ease; z-index:999999; display:flex; align-items:center; gap:8px; border:1px solid rgba(255,255,255,0.1);';
                toast.innerHTML = `<svg style="width:16px;height:16px;color:#16A34A;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg><span>${message}</span>`;
                document.body.appendChild(toast);
                setTimeout(() => {
                    toast.style.opacity = '0';
                    toast.style.transition = 'opacity 0.3s ease';
                    setTimeout(() => toast.remove(), 300);
                }, 3200);
            }
        }));
    });
</script>
@endpush
@endsection