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
                
                @if(auth()->user()->hasRole('Lead Engineer'))
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
                                            <button @click="viewProject(project)" class="rounded-lg p-1.5 text-[#75727C] hover:text-[#17151C] hover:bg-[#F1F0EE] transition-colors duration-200">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </button>
                                            
                                            @if(auth()->user()->hasRole('Lead Engineer'))
                                            <button @click="editProject(project)" class="rounded-lg p-1.5 text-[#75727C] hover:text-[#17151C] hover:bg-[#F1F0EE] transition-colors duration-200">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </button>
                                            
                                            <button @click="confirmDelete(project)" class="rounded-lg p-1.5 text-[#C81E2C] hover:text-[#7A0D18] hover:bg-[#FDF1F2] transition-colors duration-200">
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
                <div x-show="filteredProjects.length > 0" class="flex items-center justify-between px-4 py-3 border-t border-[#EFEDEB]">
                    <span class="text-[12.5px] text-[#948F99]">
                        Menampilkan <span class="font-semibold text-[#17151C]" x-text="((currentPage - 1) * perPage) + 1"></span>–<span class="font-semibold text-[#17151C]" x-text="Math.min(currentPage * perPage, filteredProjects.length)"></span> dari <span class="font-semibold text-[#17151C]" x-text="filteredProjects.length"></span> project
                    </span>
                    <div class="flex items-center gap-1">
                        <button @click="prevPage" :disabled="currentPage === 1"
                            class="rounded-lg px-2.5 py-1.5 text-[12.5px] font-medium border border-[#E7E5E3] text-[#3D3A44] hover:bg-[#F1F0EE] disabled:opacity-40 disabled:cursor-not-allowed transition-all">
                            ← Prev
                        </button>
                        <template x-for="page in totalPages" :key="page">
                            <button @click="goToPage(page)"
                                :class="page === currentPage ? 'bg-[#C81E2C] text-white border-[#C81E2C]' : 'bg-white text-[#3D3A44] border-[#E7E5E3] hover:bg-[#F1F0EE]'"
                                class="rounded-lg w-8 h-8 text-[12.5px] font-medium border transition-all"
                                x-text="page">
                            </button>
                        </template>
                        <button @click="nextPage" :disabled="currentPage === totalPages"
                            class="rounded-lg px-2.5 py-1.5 text-[12.5px] font-medium border border-[#E7E5E3] text-[#3D3A44] hover:bg-[#F1F0EE] disabled:opacity-40 disabled:cursor-not-allowed transition-all">
                            Next →
                        </button>
                    </div>
                </div>
            </div>

            <!-- ============================================================ -->
            <!-- CONFIRM DELETE MODAL - POP UP MODERN -->
            <!-- ============================================================ -->
            <div x-show="confirmOpen" 
                 x-cloak
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-[#0E0D12]/60 z-[99999] flex items-center justify-center p-4 sm:p-5 backdrop-blur-sm"
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
                        
                        <!-- Title -->
                        <h3 class="text-center font-display text-[18px] font-bold text-[#17151C] mb-2">
                            Yakin hapus data?
                        </h3>
                        
                        <!-- Description -->
                        <p class="text-center text-[14px] text-[#75727C] mb-6 break-words">
                            Project "<span x-text="confirmData?.name" class="font-semibold text-[#17151C]"></span>" akan dihapus permanen.
                            <br>
                        </p>
                        
                        <!-- Buttons -->
                        <div class="flex flex-col sm:flex-row gap-3">
                            <button @click="confirmDeleteAction()" 
                                    class="flex-1 py-2.5 px-4 rounded-lg bg-[#C81E2C] text-white font-semibold text-[14px] hover:brightness-105 transition-all">
                                Yakin
                            </button>
                            <button @click="confirmOpen = false" 
                                    class="flex-1 py-2.5 px-4 rounded-lg bg-white text-[#3D3A44] border border-[#E7E5E3] font-semibold text-[14px] hover:bg-[#F8F7F6] transition-all">
                                Batal
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Project Modal -->
            <div x-show="modalOpen" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-[#0E0D12]/60 z-50 flex items-center justify-center p-3 sm:p-5 backdrop-blur-sm"
                 @click.self="modalOpen = false">
                <div class="bg-white rounded-2xl w-[640px] max-w-full max-h-[90vh] sm:max-h-[88vh] overflow-y-auto animate-fade-in-up shadow-[0_16px_40px_rgba(14,13,18,0.12)]">
                    <div class="flex items-center justify-between p-[18px] 22px sticky top-0 bg-white border-b border-[#E7E5E3]">
                        <h3 class="font-display text-[17px] font-semibold text-[#17151C] break-words pr-3" x-text="modalTitle"></h3>
                        <button @click="modalOpen = false" class="flex-shrink-0 rounded-lg p-1.5 text-[#75727C] hover:text-[#17151C] hover:bg-[#F1F0EE] transition-colors duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <div class="p-4 sm:p-[22px]">
                        <form @submit.prevent="saveProject">
                            <div class="space-y-3.5">
                                <div>
                                    <label class="block text-[12px] font-bold text-[#75727C] mb-1.5 uppercase tracking-[0.3px]">Nama Project</label>
                                    <input type="text" x-model="form.name" class="w-full px-[11px] py-[9px] rounded-lg border border-[#E7E5E3] text-[14px] text-[#17151C] outline-none bg-white focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all" required>
                                </div>
                                <div>
                                    <label class="block text-[12px] font-bold text-[#75727C] mb-1.5 uppercase tracking-[0.3px]">Client</label>
                                    <input type="text" x-model="form.client" class="w-full px-[11px] py-[9px] rounded-lg border border-[#E7E5E3] text-[14px] text-[#17151C] outline-none bg-white focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all" required>
                                </div>
                                <div>
                                    <label class="block text-[12px] font-bold text-[#75727C] mb-1.5 uppercase tracking-[0.3px]">Lokasi</label>
                                    <input type="text" x-model="form.location" class="w-full px-[11px] py-[9px] rounded-lg border border-[#E7E5E3] text-[14px] text-[#17151C] outline-none bg-white focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all" required>
                                </div>
                                <div>
                                    <label class="block text-[12px] font-bold text-[#75727C] mb-1.5 uppercase tracking-[0.3px]">Deskripsi</label>
                                    <textarea x-model="form.description" class="w-full px-[11px] py-[9px] rounded-lg border border-[#E7E5E3] text-[14px] text-[#17151C] outline-none bg-white focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all min-h-[70px]" rows="3"></textarea>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-[12px] font-bold text-[#75727C] mb-1.5 uppercase tracking-[0.3px]">Tanggal Mulai</label>
                                        <input type="date" x-model="form.start_date" class="w-full px-[11px] py-[9px] rounded-lg border border-[#E7E5E3] text-[14px] text-[#17151C] outline-none bg-white focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all" required>
                                    </div>
                                    <div>
                                        <label class="block text-[12px] font-bold text-[#75727C] mb-1.5 uppercase tracking-[0.3px]">Deadline</label>
                                        <input type="date" x-model="form.deadline" class="w-full px-[11px] py-[9px] rounded-lg border border-[#E7E5E3] text-[14px] text-[#17151C] outline-none bg-white focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all" required>
                                    </div>
                                </div>

                            </div>
                            <div class="flex flex-col sm:flex-row gap-2.5 mt-4">
                                <button type="submit" class="wms-btn flex-1 justify-center bg-[#C81E2C] text-white shadow-[0_8px_20px_rgba(200,30,44,0.24)] py-[10px] px-[17px] rounded-lg font-semibold text-[14px] hover:brightness-105 active:translate-y-[1px] transition-all">
                                    Simpan
                                </button>
                                <button type="button" @click="modalOpen = false" class="wms-btn flex-1 justify-center bg-white text-[#3D3A44] border border-[#E7E5E3] shadow-[0_1px_2px_rgba(14,13,18,0.05)] py-[10px] px-[17px] rounded-lg font-semibold text-[14px] hover:brightness-105 active:translate-y-[1px] transition-all">
                                    Batal
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Detail Modal -->
            <div x-show="detailOpen" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-[#0E0D12]/60 z-50 flex items-center justify-center p-3 sm:p-5 backdrop-blur-sm"
                 @click.self="detailOpen = false">
                <div class="bg-white rounded-2xl w-[460px] max-w-full max-h-[90vh] sm:max-h-[88vh] overflow-y-auto animate-fade-in-up shadow-[0_16px_40px_rgba(14,13,18,0.12)]">
                    <div class="flex items-center justify-between p-[18px] 22px sticky top-0 bg-white border-b border-[#E7E5E3]">
                        <h3 class="font-display text-[17px] font-semibold text-[#17151C] break-words pr-3" x-text="detailProject?.name"></h3>
                        <button @click="detailOpen = false" class="flex-shrink-0 rounded-lg p-1.5 text-[#75727C] hover:text-[#17151C] hover:bg-[#F1F0EE] transition-colors duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <div class="p-4 sm:p-[22px] space-y-2.5 text-[13.5px]">
                        <div x-html="detailProject ? getStatusBadge(detailProject.status) : ''"></div>
                        <p class="text-[#3D3A44] leading-relaxed break-words" x-text="detailProject?.description"></p>
                        <div class="break-words"><span class="font-semibold">Client:</span> <span x-text="detailProject?.client"></span></div>
                        <div class="break-words"><span class="font-semibold">Lokasi:</span> <span x-text="detailProject?.location"></span></div>
                        <div class="break-words"><span class="font-semibold">Mulai:</span> <span x-text="formatDeadline(detailProject?.start_date)"></span> <span class="font-semibold">Deadline:</span> <span x-text="formatDeadline(detailProject?.deadline)"></span></div>
                        <div><span class="font-semibold">Progress:</span> <span x-text="detailProject ? getProjectProgress(detailProject) + '%' : ''"></span></div>
                    </div>
                </div>
            </div>
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
            perPage: 8,
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