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
                               placeholder="Cari project atau client..." 
                               class="w-full sm:w-60 px-[11px] py-[9px] pl-8 rounded-lg border border-[#E7E5E3] text-[14px] text-[#17151C] outline-none bg-white focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all">
                    </div>
                    <select x-model="statusFilter" @change="currentPage = 1" class="w-full sm:w-40 px-[11px] py-[9px] rounded-lg border border-[#E7E5E3] text-[14px] text-[#17151C] outline-none bg-white focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all">
                        <option value="Semua">Semua Status</option>
                        <option value="Planning">Planning</option>
                        <option value="On Progress">On Progress</option>
                        <option value="Completed">Completed</option>
                    </select>
                </div>
                
                @if($canManage)
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
                    <table class="w-full min-w-[760px] border-collapse text-[13.5px]">
                        <thead>
                            <tr class="bg-[#F1F0EE]">
                                <th class="text-left py-3 px-4 text-[11.5px] font-semibold text-[#75727C] uppercase tracking-[0.3px]">Nama Project</th>
                                <th class="text-left py-3 px-4 text-[11.5px] font-semibold text-[#75727C] uppercase tracking-[0.3px]">Client</th>
                                <th class="text-left py-3 px-4 text-[11.5px] font-semibold text-[#75727C] uppercase tracking-[0.3px]">Lokasi</th>
                                <th class="text-left py-3 px-4 text-[11.5px] font-semibold text-[#75727C] uppercase tracking-[0.3px]">Deadline</th>
                                <th class="text-left py-3 px-4 text-[11.5px] font-semibold text-[#75727C] uppercase tracking-[0.3px]">Progress</th>
                                <th class="text-left py-3 px-4 text-[11.5px] font-semibold text-[#75727C] uppercase tracking-[0.3px]">Status</th>
                                <th class="text-right py-3 px-4 text-[11.5px] font-semibold text-[#75727C] uppercase tracking-[0.3px]">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="project in paginatedProjects" :key="project.id">
                                <tr class="border-t border-[#EFEDEB] hover:bg-[#F1F0EE] transition-colors duration-150">
                                    <td class="py-3 px-4 font-medium text-[#17151C]" x-text="project.name"></td>
                                    <td class="py-3 px-4 text-[#3D3A44]" x-text="project.client"></td>
                                    <td class="py-3 px-4 text-[#3D3A44]" x-text="project.location"></td>
                                    <td class="py-3 px-4 text-[#3D3A44] font-mono text-[12.5px]" x-text="formatDeadline(project.deadline)"></td>
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
                                            
                                            @if($canManage)
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
                     class="fixed inset-0 bg-[#0E0D12]/60 z-50 flex items-center justify-center p-3 sm:p-5"
                     @click.self="modalOpen = false">
                    <div class="bg-white rounded-2xl w-[520px] max-w-full max-h-[90vh] overflow-y-auto animate-fade-in-up shadow-[0_16px_40px_rgba(14,13,18,0.12)]">
                        <div class="flex items-center justify-between p-4 sm:p-[22px] border-b border-[#E7E5E3]">
                            <h3 class="font-display text-[18px] font-semibold text-[#17151C]" x-text="modalTitle"></h3>
                            <button @click="modalOpen = false" class="rounded-lg p-1.5 text-[#75727C] hover:text-[#17151C] hover:bg-[#F1F0EE] transition-colors duration-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        <div class="p-4 sm:p-[22px]">
                            <form @submit.prevent="saveProject()" class="space-y-4">
                                <div>
                                    <label class="block text-[13px] font-semibold text-[#17151C] mb-1.5">Nama Project <span class="text-[#C81E2C]">*</span></label>
                                    <input type="text" x-model="form.name" required class="wms-input" placeholder="Masukkan nama project">
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                    <div>
                                        <label class="block text-[13px] font-semibold text-[#17151C] mb-1.5">Client <span class="text-[#C81E2C]">*</span></label>
                                        <input type="text" x-model="form.client" required class="wms-input" placeholder="Nama client">
                                    </div>
                                    <div>
                                        <label class="block text-[13px] font-semibold text-[#17151C] mb-1.5">Lokasi <span class="text-[#C81E2C]">*</span></label>
                                        <input type="text" x-model="form.location" required class="wms-input" placeholder="Lokasi project">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                    <div>
                                        <label class="block text-[13px] font-semibold text-[#17151C] mb-1.5">Tanggal Mulai <span class="text-[#C81E2C]">*</span></label>
                                        <input type="date" x-model="form.start_date" required class="wms-input">
                                    </div>
                                    <div>
                                        <label class="block text-[13px] font-semibold text-[#17151C] mb-1.5">Deadline <span class="text-[#C81E2C]">*</span></label>
                                        <input type="date" x-model="form.deadline" required class="wms-input">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-[13px] font-semibold text-[#17151C] mb-1.5">Deskripsi</label>
                                    <textarea x-model="form.description" rows="3" class="wms-input resize-none" placeholder="Deskripsi atau catatan project..."></textarea>
                                </div>

                                <div class="flex items-center gap-3 pt-3">
                                    <button type="submit" class="wms-btn flex-1 justify-center bg-[#C81E2C] text-white shadow-[0_8px_20px_rgba(200,30,44,0.24)] py-[10px] px-[17px] rounded-lg font-semibold text-[14px] hover:brightness-105 active:translate-y-[1px] transition-all">
                                        Simpan Project
                                    </button>
                                    <button type="button" @click="modalOpen = false" class="wms-btn flex-1 justify-center bg-white text-[#3D3A44] border border-[#E7E5E3] shadow-[0_1px_2px_rgba(14,13,18,0.05)] py-[10px] px-[17px] rounded-lg font-semibold text-[14px] hover:brightness-105 active:translate-y-[1px] transition-all">
                                        Batal
                                    </button>
                                </div>
                            </form>
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
                    <div class="bg-white rounded-2xl w-[560px] max-w-full max-h-[90vh] flex flex-col animate-fade-in-up shadow-[0_20px_50px_rgba(14,13,18,0.2)] overflow-hidden">
                        
                        {{-- Modal Header --}}
                        <div class="flex items-start justify-between p-5 sm:p-6 bg-[#FAF9F8] border-b border-[#E7E5E3] flex-shrink-0">
                            <div>
                                <div class="flex items-center gap-2 mb-1.5">
                                    <span class="text-[11px] font-bold uppercase tracking-wider text-[#AF1424] bg-[#AF1424]/10 px-2 py-0.5 rounded">Detail Informasi Project</span>
                                    <span x-html="detailProject ? getStatusBadge(detailProject.status) : ''"></span>
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
                        <div class="p-5 sm:p-6 overflow-y-auto space-y-5 flex-1 text-[13.5px]">
                            
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
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                <div class="border border-[#E7E5E3] rounded-xl p-3.5 bg-white">
                                    <div class="text-[11px] font-semibold text-[#75727C] uppercase tracking-wider mb-1 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-[#C81E2C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        Lokasi Project
                                    </div>
                                    <div class="font-semibold text-[#17151C]" x-text="detailProject?.location || '-'"></div>
                                </div>
                                <div class="border border-[#E7E5E3] rounded-xl p-3.5 bg-white">
                                    <div class="text-[11px] font-semibold text-[#75727C] uppercase tracking-wider mb-1 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-[#C81E2C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        Periode & Deadline
                                    </div>
                                    <div class="text-[#17151C] font-mono text-[12.5px]">
                                        <span x-text="formatDeadline(detailProject?.start_date)"></span> &ndash; <span class="font-bold text-[#C81E2C]" x-text="formatDeadline(detailProject?.deadline)"></span>
                                    </div>
                                </div>
                            </div>

                            {{-- Deskripsi --}}
                            <div class="border border-[#E7E5E3] rounded-xl p-4 bg-white">
                                <div class="text-[11px] font-semibold text-[#75727C] uppercase tracking-wider mb-1.5">Deskripsi Proyek</div>
                                <p class="text-[#3D3A44] leading-relaxed break-words text-[13px] whitespace-pre-line" x-text="detailProject?.description || 'Tidak ada deskripsi tambahan.'"></p>
                            </div>

                            {{-- Personel & Tim Lapangan Terlibat --}}
                            <div class="border border-[#E7E5E3] rounded-xl p-4 bg-white" x-show="detailProject?.tasks && detailProject.tasks.length > 0">
                                <div class="text-[11px] font-semibold text-[#75727C] uppercase tracking-wider mb-2.5 flex items-center justify-between">
                                    <span>Personel & Tim Lapangan Terlibat</span>
                                    <span class="text-[11px] font-medium text-[#C81E2C]" x-text="(detailProject?.tasks?.length || 0) + ' Tugas Terkait'"></span>
                                </div>
                                <div class="space-y-2">
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

            <!-- CONFIRM DELETE MODAL - POP UP MODERN -->
            <template x-teleport="body">
                <div x-show="confirmOpen" 
                     x-cloak
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-[#0E0D12]/60 z-[99999] flex items-center justify-center p-4 sm:p-5"
                     @click.self="confirmOpen = false">
                    
                    <div class="bg-white rounded-2xl w-[420px] max-w-full overflow-y-auto animate-fade-in-up shadow-[0_20px_60px_rgba(14,13,18,0.2)]">
                        
                        <div class="p-5 sm:p-6">
                            <!-- Icon -->
                            <div class="flex justify-center mb-4">
                                <div class="w-14 h-14 rounded-full bg-[#FEF2F2] flex items-center justify-center">
                                    <svg class="w-7 h-7 text-[#C81E2C]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="text-center mb-6">
                                <h3 class="font-display text-[18px] font-bold text-[#17151C] mb-2">
                                    Yakin Hapus Project Ini?
                                </h3>
                                <p class="text-[13.5px] text-[#75727C] leading-relaxed">
                                    Project <strong class="text-[#17151C]" x-text="confirmData ? confirmData.name : ''"></strong> akan dihapus permanen. Tindakan ini tidak dapat dibatalkan.
                                </p>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center gap-3">
                                <button @click="confirmDeleteAction()" 
                                        class="flex-1 py-2.5 px-4 rounded-lg bg-[#C81E2C] text-white font-semibold text-[14px] shadow-[0_8px_20px_rgba(200,30,44,0.24)] hover:brightness-105 active:translate-y-[1px] transition-all">
                                    Yakin, Hapus
                                </button>
                                <button @click="confirmOpen = false" 
                                        class="flex-1 py-2.5 px-4 rounded-lg bg-white text-[#3D3A44] border border-[#E7E5E3] font-semibold text-[14px] hover:bg-[#F8F7F6] transition-all">
                                    Batal
                                </button>
                            </div>
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
                location: '',
                description: '',
                start_date: '',
                deadline: ''
            },
            detailProject: null,

            get filteredProjects() {
                return this.projects.filter(p => {
                    const matchSearch = p.name.toLowerCase().includes(this.search.toLowerCase()) ||
                                       p.client.toLowerCase().includes(this.search.toLowerCase());
                    const matchStatus = this.statusFilter === 'Semua' || p.status === this.statusFilter;
                    return matchSearch && matchStatus;
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

            openModal(project = null) {
                if (project) {
                    this.editing = true;
                    this.form = { ...project };
                } else {
                    this.editing = false;
                    this.form = {
                        id: null,
                        name: '',
                        client: '',
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
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify(this.form)
                    });

                    if (response.ok) {
                        const data = await response.json();
                        if (this.editing) {
                            const index = this.projects.findIndex(p => p.id === this.form.id);
                            this.projects[index] = data;
                        } else {
                            this.projects.push(data);
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
                // Kalau status Completed, otomatis 100%
                if (project.status === 'Completed') return 100;
                // Kalau Planning dan belum ada tasks, 0%
                if (!project.tasks || project.tasks.length === 0) return 0;
                // Hitung rata-rata progress dari semua tasks
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