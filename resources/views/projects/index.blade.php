@extends('layouts.app')

@section('title', 'Manajemen Project')

@section('content')
<div class="flex h-screen overflow-hidden">
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Manajemen Project'])
        
        <div class="p-4 sm:p-5 lg:p-[26px] animate-fade-in" x-data="projectsManager()">
            
            <!-- Filter & Actions -->
            <div class="flex flex-col sm:flex-row sm:flex-wrap sm:justify-between sm:items-center gap-2.5 mb-4">
                <div class="flex flex-col sm:flex-row flex-wrap gap-2.5">
                    <div class="relative">
                        <svg class="w-3.5 h-3.5 absolute left-2.5 top-3 text-[#948F99]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" 
                               x-model="search"
                               @input="currentPage = 1"
                               placeholder="Cari project, client, sales..." 
                               class="w-full sm:w-60 px-[11px] py-[9px] pl-8 rounded-lg border border-[#E7E5E3] text-[14px] text-[#17151C] outline-none bg-white focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all">
                    </div>
                    <select x-model="statusFilter" @change="currentPage = 1" class="w-full sm:w-40 px-[11px] py-[9px] rounded-lg border border-[#E7E5E3] text-[14px] text-[#17151C] outline-none bg-white focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all">
                        <option value="Semua">Semua Status</option>
                        <option value="Planning">Planning</option>
                        <option value="On Progress">On Progress</option>
                        <option value="Completed">Completed</option>
                    </select>
                    <select x-model="typeFilter" @change="currentPage = 1" class="w-full sm:w-44 px-[11px] py-[9px] rounded-lg border border-[#E7E5E3] text-[14px] text-[#17151C] outline-none bg-white focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all">
                        <option value="Semua">Semua Tipe Proyek</option>
                        <option value="One-Time Project">One-Time Project</option>
                        <option value="Maintenance Berkala">Maintenance Berkala</option>
                        <option value="Managed Service">Managed Service</option>
                    </select>
                </div>
                
                @if($canCreate ?? false)
                <button @click="openModal()" class="wms-btn w-full sm:w-auto justify-center bg-[#C81E2C] text-white shadow-[0_8px_20px_rgba(200,30,44,0.24)] px-[17px] py-[10px] rounded-lg font-semibold text-[14px] flex items-center gap-1.5 hover:brightness-105 active:translate-y-[1px] transition-all">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah Project
                </button>
                @endif
            </div>

            <!-- Projects Table -->
            <div class="bg-white rounded-xl border border-[#E7E5E3] shadow-[0_1px_2px_rgba(14,13,18,0.05)] overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[900px] border-collapse text-[13.5px]">
                        <thead>
                            <tr class="bg-[#F1F0EE]">
                                <th class="text-left py-3 px-4 text-[11.5px] font-semibold text-[#75727C] uppercase tracking-[0.3px]">Nama Project & Tipe</th>
                                <th class="text-left py-3 px-4 text-[11.5px] font-semibold text-[#75727C] uppercase tracking-[0.3px]">Client & Sales</th>
                                <th class="text-left py-3 px-4 text-[11.5px] font-semibold text-[#75727C] uppercase tracking-[0.3px]">Lokasi</th>
                                <th class="text-left py-3 px-4 text-[11.5px] font-semibold text-[#75727C] uppercase tracking-[0.3px]">Durasi & Deadline</th>
                                <th class="text-left py-3 px-4 text-[11.5px] font-semibold text-[#75727C] uppercase tracking-[0.3px]">Progress</th>
                                <th class="text-left py-3 px-4 text-[11.5px] font-semibold text-[#75727C] uppercase tracking-[0.3px]">Status</th>
                                <th class="text-right py-3 px-4 text-[11.5px] font-semibold text-[#75727C] uppercase tracking-[0.3px]">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="project in paginatedProjects" :key="project.id">
                                <tr class="border-t border-[#EFEDEB] hover:bg-[#F1F0EE] transition-colors duration-150">
                                    <td class="py-3 px-4">
                                        <div class="font-semibold text-[#17151C]" x-text="project.name"></div>
                                        <div class="flex items-center gap-1.5 mt-1">
                                            <template x-if="project.visit_schedule && project.visit_schedule !== 'None' && project.visit_schedule !== '-'">
                                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10.5px] font-semibold bg-[#EEF2FF] text-[#4F46E5] border border-[#E0E7FF]" title="Proyek dengan jadwal visit berkala">
                                                    <svg class="w-3 h-3 text-[#4F46E5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                                    <span x-text="'Visit: ' + project.visit_schedule"></span>
                                                </span>
                                            </template>
                                            <template x-if="!project.visit_schedule || project.visit_schedule === 'None' || project.visit_schedule === '-'">
                                                <span class="inline-block text-[11px] text-[#75727C]" x-text="project.project_type || 'One-Time Project'"></span>
                                            </template>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="font-semibold text-[#17151C]" x-text="project.client"></div>
                                        <div class="text-[11.5px] text-[#75727C] flex items-center gap-1.5 mt-0.5">
                                            <span class="text-[#948F99] font-medium">Sales:</span>
                                            <span class="font-semibold text-[#3D3A44]" x-text="project.sales_name || '-'"></span>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4 text-[#3D3A44]" x-text="project.location"></td>
                                    <td class="py-3 px-4">
                                        <div class="font-mono text-[12px] font-semibold text-[#17151C]" x-text="formatDeadline(project.deadline)"></div>
                                        <div class="text-[11px] text-[#75727C] mt-0.5 flex items-center gap-1">
                                            <span>Durasi:</span>
                                            <span class="font-medium text-[#AF1424]" x-text="getDurationText(project)"></span>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="w-24">
                                            <div style="width: 100%; background: #EFEDEB; border-radius: 20px; height: 6px; overflow: hidden;">
                                                <div style="height: 100%; border-radius: 20px; background: linear-gradient(90deg, #AF1424, #D62E3C); transition: width .25s ease;" :style="{ width: getProjectProgress(project) + '%' }"></div>
                                            </div>
                                            <span class="text-[10.5px] text-[#948F99]" x-text="getProjectProgress(project) + '%'"></span>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4">
                                        <span x-html="getStatusBadge(project.status)"></span>
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="flex justify-end gap-2.5">
                                            <button @click="viewProject(project)" title="Lihat Detail Project" class="rounded-lg p-1.5 text-[#75727C] hover:text-[#17151C] hover:bg-[#E7E5E3] transition-colors duration-200">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </button>
                                            
                                            @if($canCreate ?? false)
                                             <button @click="editProject(project)" title="Edit Project" class="rounded-lg p-1.5 text-[#75727C] hover:text-[#17151C] hover:bg-[#F1F0EE] transition-colors duration-200">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </button>
                                            
                                            <button @click="confirmDelete(project)" title="Hapus Project" class="rounded-lg p-1.5 text-[#C81E2C] hover:text-[#7A0D18] hover:bg-[#FDF1F2] transition-colors duration-200">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
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
                
                <div x-show="filteredProjects.length === 0" class="text-center py-12 text-[#75727C]">
                    <div class="w-11 h-11 rounded-xl bg-[#F1F0EE] flex items-center justify-center mx-auto mb-3">
                        <svg class="w-5 h-5 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                        </svg>
                    </div>
                    <p class="text-[13.5px]">Tidak ada project yang cocok dengan filter</p>
                </div>

                <!-- Pagination -->
                <div class="p-3.5 sm:px-5 sm:py-3.5 border-t border-[#EFEDEB] flex flex-col sm:flex-row items-center justify-between gap-3 text-[12.5px] text-[#75727C]">
                    <div class="text-center sm:text-left">
                        Menampilkan <span class="font-medium text-[#17151C]" x-text="filteredProjects.length > 0 ? (currentPage - 1) * perPage + 1 : 0"></span> &ndash; <span class="font-medium text-[#17151C]" x-text="Math.min(currentPage * perPage, filteredProjects.length)"></span> dari <span class="font-medium text-[#17151C]" x-text="filteredProjects.length"></span> project
                    </div>
                    <div class="flex items-center gap-1" x-show="totalPages > 1">
                        <button @click="prevPage()" :disabled="currentPage === 1" title="Sebelumnya" class="w-7 h-7 rounded-md border border-[#E7E5E3] hover:bg-[#F1F0EE] text-[#3D3A44] disabled:opacity-30 disabled:cursor-not-allowed flex items-center justify-center transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </button>
                        <template x-for="p in totalPages" :key="p">
                            <button @click="goToPage(p)" 
                                    :class="currentPage === p ? 'bg-[#AF1424] text-white border-[#AF1424] font-bold' : 'border-[#E7E5E3] hover:bg-[#F1F0EE] text-[#17151C]'"
                                    class="w-7 h-7 rounded-md border text-[12px] font-medium flex items-center justify-center transition"
                                    x-text="p">
                            </button>
                        </template>
                        <button @click="nextPage()" :disabled="currentPage === totalPages" title="Berikutnya" class="w-7 h-7 rounded-md border border-[#E7E5E3] hover:bg-[#F1F0EE] text-[#3D3A44] disabled:opacity-30 disabled:cursor-not-allowed flex items-center justify-center transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Create / Edit Modal -->
            <template x-teleport="body">
                <div x-show="modalOpen" 
                     x-cloak
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-[#0E0D12]/60 z-50 flex items-center justify-center p-4 sm:p-6"
                     @click.self="modalOpen = false">
                    <div class="bg-white rounded-2xl w-[580px] max-w-full max-h-[85vh] flex flex-col overflow-hidden animate-fade-in-up shadow-[0_20px_60px_rgba(14,13,18,0.2)]">
                        {{-- Modal Header (Fixed / Tidak Ikut Tescroll) --}}
                        <div class="flex items-center justify-between p-4 sm:p-[22px] border-b border-[#E7E5E3] flex-shrink-0 bg-white">
                            <div>
                                <h3 class="font-display text-[18px] font-semibold text-[#17151C]" x-text="modalTitle"></h3>
                                <p class="text-[12.5px] text-[#75727C] mt-0.5">Kelola informasi proyek, tim sales, dan jadwal pemeliharaan berkala.</p>
                            </div>
                            <button @click="modalOpen = false" class="rounded-lg p-1.5 text-[#75727C] hover:text-[#17151C] hover:bg-[#F1F0EE] transition-colors duration-200 cursor-pointer">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        {{-- Modal Body (Hanya bagian ini yang di-scroll) --}}
                        <div class="p-4 sm:p-[22px] overflow-y-auto flex-1">
                            <form id="projectForm" @submit.prevent="saveProject()" class="space-y-4">
                                <div>
                                    <label class="block text-[13px] font-semibold text-[#17151C] mb-1.5">Nama Project <span class="text-[#C81E2C]">*</span></label>
                                    <input type="text" x-model="form.name" required class="wms-input" placeholder="Masukkan nama project lengkap">
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                    <div>
                                        <label class="block text-[13px] font-semibold text-[#17151C] mb-1.5">Klien / Pemilik Proyek <span class="text-[#C81E2C]">*</span></label>
                                        <input type="text" x-model="form.client" required class="wms-input" placeholder="Contoh: PT Bank Central Asia Tbk">
                                    </div>
                                    <div>
                                        <label class="block text-[13px] font-semibold text-[#17151C] mb-1.5">Nama Sales / PIC Sales <span class="text-[#C81E2C]">*</span></label>
                                        <input type="text" x-model="form.sales_name" required class="wms-input" placeholder="Contoh: Riko Wijaya">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                    <div>
                                        <label class="block text-[13px] font-semibold text-[#17151C] mb-1.5">Tipe Proyek <span class="text-[#C81E2C]">*</span></label>
                                        <select x-model="form.project_type" class="wms-input">
                                            <option value="One-Time Project">One-Time Project / Deployment</option>
                                            <option value="Maintenance Berkala">Maintenance Berkala / SLA</option>
                                            <option value="Managed Service">Managed Service & Support</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[13px] font-semibold text-[#17151C] mb-1.5">Jadwal Visit Berkala</label>
                                        <select x-model="form.visit_schedule" class="wms-input">
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
                                    <label class="block text-[13px] font-semibold text-[#17151C] mb-1.5">Lokasi <span class="text-[#C81E2C]">*</span></label>
                                    <input type="text" x-model="form.location" required class="wms-input" placeholder="Gedung / Data Center / Alamat Project">
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                    <div>
                                        <label class="block text-[13px] font-semibold text-[#17151C] mb-1.5">Tanggal Mulai <span class="text-[#C81E2C]">*</span></label>
                                        <input type="date" x-model="form.start_date" required class="wms-input">
                                    </div>
                                    <div>
                                        <label class="block text-[13px] font-semibold text-[#17151C] mb-1.5">Deadline / Akhir Kontrak <span class="text-[#C81E2C]">*</span></label>
                                        <input type="date" x-model="form.deadline" required class="wms-input">
                                    </div>
                                </div>

                                <template x-if="form.start_date && form.deadline">
                                    <div class="p-2.5 rounded-lg bg-[#F8F7F6] border border-[#E7E5E3] text-[12.5px] text-[#3D3A44] flex items-center justify-between">
                                        <span>Estimasi Durasi Proyek:</span>
                                        <span class="font-bold text-[#AF1424]" x-text="calculateFormDuration()"></span>
                                    </div>
                                </template>

                                <div>
                                    <label class="block text-[13px] font-semibold text-[#17151C] mb-1.5">Deskripsi & Catatan SLA</label>
                                    <textarea x-model="form.description" rows="3" class="wms-input resize-none" placeholder="Deskripsi teknis, ruang lingkup SLA, atau catatan project..."></textarea>
                                </div>
                            </form>
                        </div>

                        {{-- Modal Footer (Fixed / Selalu Tampak) --}}
                        <div class="flex items-center gap-3 p-4 sm:px-6 sm:py-4 border-t border-[#E7E5E3] bg-[#FAF9F8] flex-shrink-0">
                            <button type="submit" 
                                    form="projectForm" 
                                    class="flex-1 flex items-center justify-center min-h-[42px] bg-[#C81E2C] hover:bg-[#AF1424] text-white shadow-[0_4px_14px_rgba(200,30,44,0.25)] px-4 py-2.5 rounded-xl font-semibold text-[14px] active:translate-y-[1px] transition-all cursor-pointer">
                                <span x-text="editing ? 'Simpan Perubahan' : 'Simpan Project'">Simpan Project</span>
                            </button>
                            <button type="button" 
                                    @click="modalOpen = false" 
                                    class="flex-1 flex items-center justify-center min-h-[42px] bg-white hover:bg-[#F8F7F6] text-[#3D3A44] border border-[#E7E5E3] shadow-[0_1px_2px_rgba(14,13,18,0.05)] px-4 py-2.5 rounded-xl font-semibold text-[14px] active:translate-y-[1px] transition-all cursor-pointer">
                                Batal
                            </button>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Detail Modal -->
            <template x-teleport="body">
                <div x-show="detailOpen" 
                     x-cloak
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-[#0E0D12]/60 z-50 flex items-center justify-center p-3 sm:p-5"
                     @click.self="detailOpen = false">
                    <div class="bg-white rounded-2xl w-[600px] max-w-full max-h-[90vh] flex flex-col animate-fade-in-up shadow-[0_20px_50px_rgba(14,13,18,0.2)] overflow-hidden">
                        
                        {{-- Modal Header --}}
                        <div class="flex items-start justify-between p-5 sm:p-6 bg-[#FAF9F8] border-b border-[#E7E5E3] flex-shrink-0">
                            <div class="min-w-0 flex-1 pr-3">
                                <div class="flex flex-wrap items-center gap-2 mb-1.5">
                                    <span class="text-[11px] font-bold uppercase tracking-wider text-[#AF1424] bg-[#AF1424]/10 px-2 py-0.5 rounded">Detail Informasi Project</span>
                                    <span x-html="detailProject ? getStatusBadge(detailProject.status) : ''"></span>
                                    <template x-if="detailProject?.visit_schedule && detailProject?.visit_schedule !== 'None' && detailProject?.visit_schedule !== '-'">
                                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-[11px] font-bold bg-[#EEF2FF] text-[#4F46E5] border border-[#E0E7FF]">
                                            <svg class="w-3 h-3 text-[#4F46E5]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                            </svg>
                                            <span>Jadwal Visit: <span x-text="detailProject.visit_schedule"></span></span>
                                        </span>
                                    </template>
                                </div>
                                <h3 class="font-display text-[18px] sm:text-[20px] font-bold text-[#17151C] leading-snug" x-text="detailProject?.name"></h3>
                                <p class="text-[13px] text-[#75727C] font-medium mt-0.5" x-text="detailProject?.client"></p>
                            </div>
                            <button @click="detailOpen = false" class="rounded-lg p-2 text-[#75727C] hover:text-[#17151C] hover:bg-[#F1F0EE] transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        {{-- Modal Body --}}
                        <div class="p-5 sm:p-6 overflow-y-auto space-y-4 flex-1 text-[13.5px]">
                            
                            {{-- Overall Progress Card --}}
                            <div class="bg-[#F7F6F5] border border-[#E7E5E3] rounded-xl p-4">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-[12px] font-semibold text-[#75727C] uppercase tracking-wide">Progress Keseluruhan</span>
                                    <span class="text-[14px] font-bold text-[#AF1424]" x-text="detailProject ? getProjectProgress(detailProject) + '%' : '0%'"></span>
                                </div>
                                <div class="w-full bg-[#E7E5E3] rounded-full h-2.5 overflow-hidden">
                                    <div class="h-full rounded-full bg-gradient-to-r from-[#AF1424] to-[#D62E3C] transition-all duration-300"
                                         :style="{ width: (detailProject ? getProjectProgress(detailProject) : 0) + '%' }"></div>
                                </div>
                            </div>

                            {{-- Metadata Grid --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div class="border border-[#E7E5E3] rounded-xl p-3 bg-white">
                                    <div class="text-[11px] font-semibold text-[#75727C] uppercase tracking-wider mb-1 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-[#C81E2C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        Lokasi Project
                                    </div>
                                    <div class="font-semibold text-[#17151C]" x-text="detailProject?.location || '-'"></div>
                                </div>

                                <div class="border border-[#E7E5E3] rounded-xl p-3 bg-white">
                                    <div class="text-[11px] font-semibold text-[#75727C] uppercase tracking-wider mb-1 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-[#C81E2C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        Sales / Account Exec
                                    </div>
                                    <div class="font-semibold text-[#17151C]" x-text="detailProject?.sales_name || 'Tidak Ditentukan'"></div>
                                </div>

                                <div class="border border-[#E7E5E3] rounded-xl p-3 bg-white">
                                    <div class="text-[11px] font-semibold text-[#75727C] uppercase tracking-wider mb-1 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-[#C81E2C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                        Tipe & Jadwal Visit
                                    </div>
                                    <div class="font-semibold text-[#17151C]">
                                        <span x-text="detailProject?.project_type || 'One-Time Project'"></span>
                                        <template x-if="detailProject?.visit_schedule && detailProject.visit_schedule !== 'None'">
                                            <span class="text-[#4F46E5] block text-[12px]" x-text="'• Visit: ' + detailProject.visit_schedule"></span>
                                        </template>
                                    </div>
                                </div>

                                <div class="border border-[#E7E5E3] rounded-xl p-3 bg-white">
                                    <div class="text-[11px] font-semibold text-[#75727C] uppercase tracking-wider mb-1 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-[#C81E2C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        Periode & Total Durasi
                                    </div>
                                    <div class="text-[#17151C] font-mono text-[12px]">
                                        <span x-text="formatDeadline(detailProject?.start_date)"></span> &ndash; <span class="font-bold text-[#C81E2C]" x-text="formatDeadline(detailProject?.deadline)"></span>
                                        <div class="text-[11.5px] font-sans font-bold text-[#AF1424] mt-0.5" x-text="'Total: ' + (detailProject ? getDurationText(detailProject) : '-')"></div>
                                    </div>
                                </div>
                            </div>

                            {{-- Deskripsi --}}
                            <div class="border border-[#E7E5E3] rounded-xl p-3.5 bg-white">
                                <div class="text-[11px] font-semibold text-[#75727C] uppercase tracking-wider mb-1.5">Deskripsi Proyek & SLA</div>
                                <p class="text-[#3D3A44] leading-relaxed break-words text-[13px] whitespace-pre-line" x-text="detailProject?.description || 'Tidak ada deskripsi tambahan.'"></p>
                            </div>

                            {{-- Personel & Tim Lapangan Terlibat --}}
                            <div class="border border-[#E7E5E3] rounded-xl p-3.5 bg-white" x-show="detailProject?.tasks && detailProject.tasks.length > 0">
                                <div class="text-[11px] font-semibold text-[#75727C] uppercase tracking-wider mb-2.5 flex items-center justify-between">
                                    <span>Personel & Tugas Terkait</span>
                                    <span class="text-[11px] font-medium text-[#C81E2C]" x-text="(detailProject?.tasks?.length || 0) + ' Tugas Terkait'"></span>
                                </div>
                                <div class="space-y-2 max-h-48 overflow-y-auto">
                                    <template x-for="task in detailProject?.tasks" :key="task.id">
                                        <div class="flex items-center justify-between p-2.5 rounded-lg bg-[#F8F7F6] border border-[#EFEDEB] text-[12.5px]">
                                            <div class="min-w-0 flex-1 pr-2">
                                                <div class="font-medium text-[#17151C] truncate" x-text="task.title"></div>
                                                <div class="text-[11px] text-[#75727C] flex items-center gap-1.5 mt-0.5">
                                                    <span class="font-semibold text-[#3D3A44]" x-text="task.engineer?.name || 'Belum Ditugaskan'"></span>
                                                    <span>&bull;</span>
                                                    <span :class="task.status === 'Completed' ? 'text-[#10B981]' : (task.status === 'In Progress' ? 'text-[#F59E0B]' : 'text-[#3B82F6]')" x-text="task.status"></span>
                                                </div>
                                            </div>
                                            <span class="text-[11.5px] font-mono font-semibold text-[#17151C] bg-white px-2 py-0.5 rounded border border-[#E7E5E3]" x-text="(task.progress || 0) + '%'"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            <!-- CONFIRM DELETE MODAL -->
            <template x-teleport="body">
                <div x-show="confirmOpen" 
                     x-cloak
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-[#0E0D12]/60 z-[99999] flex items-center justify-center p-4 backdrop-blur-sm"
                     @click.self="confirmOpen = false"
                     @keydown.escape.window="confirmOpen = false">
                    
                    <div class="bg-white rounded-2xl w-[420px] max-w-full p-6 text-left shadow-[0_20px_60px_rgba(14,13,18,0.2)] animate-fade-in-up">
                        <div class="w-14 h-14 rounded-full bg-[#FEF2F2] flex items-center justify-center mx-auto mb-4 text-[#C81E2C]">
                            <svg class="w-7 h-7 text-[#C81E2C]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </div>

                        <h3 class="text-center font-display text-[17px] font-bold text-[#17151C] mb-2">Yakin Hapus Project?</h3>
                        <p class="text-center text-[13.5px] text-[#75727C] mb-6 break-words" x-text="'Project &quot;' + (confirmData ? confirmData.name : '') + '&quot; akan dihapus.'"></p>

                        <div class="flex gap-3">
                            <button type="button" @click="confirmDeleteAction()" class="flex-1 py-2.5 px-4 rounded-xl bg-[#C81E2C] text-white font-semibold text-[13.5px] hover:bg-[#A31622] transition cursor-pointer">
                                Hapus
                            </button>
                            <button type="button" @click="confirmOpen = false" class="flex-1 py-2.5 px-4 rounded-xl bg-white text-[#3D3A44] border border-[#E7E5E3] font-semibold text-[13.5px] hover:bg-[#F8F7F6] transition cursor-pointer">
                                Batal
                            </button>
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
                const start = (this.currentPage - 1) * this.perPage;
                return this.filteredProjects.slice(start, start + this.perPage);
            },

            get totalPages() {
                return Math.max(1, Math.ceil(this.filteredProjects.length / this.perPage));
            },

            goToPage(page) { this.currentPage = page; },
            prevPage() { if (this.currentPage > 1) this.currentPage--; },
            nextPage() { if (this.currentPage < this.totalPages) this.currentPage++; },

            get modalTitle() {
                return this.editing ? 'Edit Project' : 'Tambah Project';
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
                    'Planning': { bg: '#EFEDEC', fg: '#3D3A44', dot: '#948F99' },
                    'On Progress': { bg: '#FAF0D9', fg: '#9A6206', dot: '#9A6206' },
                    'Completed': { bg: '#E4F3EA', fg: '#1B7A46', dot: '#1B7A46' }
                };
                const s = styles[status] || styles['Planning'];
                return `<span style="background: ${s.bg}; color: ${s.fg}; font-size: 11.5px; font-weight: 700; padding: 4px 10px 4px 8px; border-radius: 20px; white-space: nowrap; display: inline-flex; align-items: center; gap: 6px; letter-spacing: 0.1px;">
                            <span style="width: 6px; height: 6px; border-radius: 50%; background: ${s.dot}; flex-shrink: 0;"></span>
                            ${status}
                        </span>`;
            },

            formatDeadline(dateStr) {
                if (!dateStr) return '-';
                const d = new Date(dateStr);
                if (isNaN(d.getTime())) return dateStr;
                const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
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
                toast.className = 'fixed bottom-4 right-4 bg-[#17151C] text-white px-6 py-3 rounded-lg shadow-[0_16px_40px_rgba(14,13,18,0.12)] text-[14px] animate-fade-in-up z-50';
                toast.textContent = message;
                document.body.appendChild(toast);
                setTimeout(() => {
                    toast.style.opacity = '0';
                    toast.style.transition = 'opacity 0.3s ease';
                    setTimeout(() => toast.remove(), 300);
                }, 3000);
            }
        }));
    });
</script>
@endpush
@endsection