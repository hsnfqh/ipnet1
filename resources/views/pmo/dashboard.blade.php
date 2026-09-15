@extends('layouts.app')

@section('title', 'Dashboard - PMO')

@section('content')
<div class="flex h-screen overflow-hidden" x-data="pmoDashboard()" x-cloak>
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto bg-[#FAF9F8]">
        @include('components.topbar', ['title' => 'Dashboard'])
        
        <div class="p-4 sm:p-5 lg:p-[26px] animate-fade-in space-y-6">
            
            {{-- Filter Controls --}}
            <div class="flex flex-wrap items-center justify-end gap-2.5">
                <div class="flex items-center gap-2 bg-white px-3.5 py-2 rounded-xl border border-gray-200 shadow-xs text-xs">
                    <span class="text-gray-400 font-semibold">Divisi:</span>
                    <select x-model="selectedDivision" @change="currentPage = 1" class="bg-transparent font-bold text-gray-800 outline-none cursor-pointer">
                        <option value="all">Semua Divisi</option>
                        @foreach($divisions as $div)
                            <option value="{{ $div->id }}">{{ $div->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center gap-2 bg-white px-3.5 py-2 rounded-xl border border-gray-200 shadow-xs text-xs">
                    <span class="text-gray-400 font-semibold">Project Manager:</span>
                    <select x-model="selectedPm" @change="currentPage = 1" class="bg-transparent font-bold text-gray-800 outline-none cursor-pointer">
                        <option value="all">Semua PM</option>
                        @foreach($pmList as $pm)
                            <option value="{{ $pm->id }}">{{ $pm->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- 4 Metric Cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                
                {{-- Card 1: Deliver Aktif --}}
                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-[0_1px_3px_rgba(0,0,0,0.03)] flex flex-col justify-between space-y-4 wms-card-hover animate-pop-in stagger-1">
                    <div class="space-y-3">
                        <div class="w-10 h-10 rounded-xl bg-red-50 border border-red-100 text-[#C81E2C] flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-[11.5px] font-medium text-gray-400">Proyek Tahap Deliver</p>
                            <h3 class="text-xl font-extrabold text-gray-900 tracking-tight mt-1" x-text="stageCounts.Deliver + ' Proyek'"></h3>
                        </div>
                    </div>
                    <div class="flex items-center justify-between text-xs pt-3 border-t border-gray-50 text-gray-400">
                        <span class="font-bold text-emerald-600" x-text="stageCounts.Deliver > 0 ? 'Sedang Berjalan' : 'Belum Ada Proyek'"></span>
                        <span>Fase Implementasi Teknis</span>
                    </div>
                </div>

                {{-- Card 2: Kesehatan Timeline --}}
                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-[0_1px_3px_rgba(0,0,0,0.03)] flex flex-col justify-between space-y-4 wms-card-hover animate-pop-in stagger-2">
                    <div class="space-y-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-600 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-[11.5px] font-medium text-gray-400">Kepatuhan Jadwal (Timeline)</p>
                            <h3 class="text-xl font-extrabold text-emerald-600 tracking-tight mt-1" x-text="onTrackCount + ' Sesuai Jadwal'"></h3>
                        </div>
                    </div>
                    <div class="flex items-center justify-between text-xs pt-3 border-t border-gray-50 text-gray-400">
                        <span class="font-bold" :class="delayedCount > 0 ? 'text-red-600' : 'text-emerald-600'" x-text="delayedCount > 0 ? delayedCount + ' Proyek Terlambat' : 'Semua Tepat Waktu'"></span>
                        <span x-text="delayedCount > 0 ? 'Perlu Mitigasi' : 'Jadwal Aman'"></span>
                    </div>
                </div>

                {{-- Card 3: Gerbang Handover Gateway --}}
                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-[0_1px_3px_rgba(0,0,0,0.03)] flex flex-col justify-between space-y-4 wms-card-hover animate-pop-in stagger-3">
                    <div class="space-y-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-100 text-blue-600 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-[11.5px] font-medium text-gray-400">Review Handover Gateway</p>
                            <h3 class="text-xl font-extrabold text-blue-600 tracking-tight mt-1" x-text="handoverPendingCount + ' Menunggu Verifikasi'"></h3>
                        </div>
                    </div>
                    <div class="flex items-center justify-between text-xs pt-3 border-t border-gray-50 text-gray-400">
                        <span class="font-bold text-amber-600" x-text="handoverConditionalCount > 0 ? handoverConditionalCount + ' Revisi 2x24 Jam' : 'SOP Gatekeeper'"></span>
                        <span>Commercial &rarr; PMO</span>
                    </div>
                </div>

                {{-- Card 4: Utilisasi Teknisi Lapangan --}}
                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-[0_1px_3px_rgba(0,0,0,0.03)] flex flex-col justify-between space-y-4 wms-card-hover animate-pop-in stagger-4">
                    <div class="space-y-3">
                        <div class="w-10 h-10 rounded-xl bg-purple-50 border border-purple-100 text-purple-600 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-[11.5px] font-medium text-gray-400">Alokasi Personil Engineer</p>
                            <h3 class="text-xl font-extrabold text-purple-700 tracking-tight mt-1">
                                <span x-text="activeEngineersCount"></span> / <span x-text="totalEngineersCount"></span> Ditugaskan
                            </h3>
                        </div>
                    </div>
                    <div class="flex items-center justify-between text-xs pt-3 border-t border-gray-50 text-gray-400">
                        <span class="font-bold text-emerald-600" x-text="standbyEngineersCount + ' Personil Siap Ditugaskan'"></span>
                        <span>Seluruh Divisi</span>
                    </div>
                </div>

            </div>

            {{-- Handover Pending Alert Banner --}}
            <template x-if="handoverPendingCount > 0 || handoverConditionalCount > 0">
                <div class="p-4 rounded-2xl bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-blue-600 text-white flex items-center justify-center font-bold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-blue-900">Perhatian Gatekeeper Serah Terima Proyek (PMO & Kadiv)</h4>
                            <p class="text-[11.5px] text-blue-700 mt-0.5">
                                Terdapat <strong x-text="handoverPendingCount"></strong> berkas serah terima baru yang menunggu pengesahan, dan <strong x-text="handoverConditionalCount"></strong> berkas dengan catatan bersyarat (tenggat 2x24 jam).
                            </p>
                        </div>
                    </div>
                    <button type="button" 
                            @click="handoverFilter = (handoverFilter === 'pending' ? 'all' : 'pending'); currentPage = 1;"
                            class="px-3.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl transition cursor-pointer shadow-xs whitespace-nowrap"
                            x-text="handoverFilter === 'pending' ? 'Tampilkan Semua Proyek' : 'Filter Menunggu Handover'">
                    </button>
                </div>
            </template>



            {{-- Tabel Portofolio Proyek & Matriks Dokumen --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-[0_1px_3px_rgba(0,0,0,0.03)] overflow-hidden">
                
                {{-- Table Search Header --}}
                <div class="p-4 sm:p-5 border-b border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3 bg-white">
                    <div class="relative w-full sm:w-80">
                        <input type="text" 
                               x-model="search" 
                               @input="currentPage = 1" 
                               placeholder="Cari nama proyek, klien, sales, atau PM..." 
                               class="w-full pl-9 pr-4 py-2 bg-gray-50/80 border border-gray-200 rounded-xl text-xs text-gray-900 placeholder-gray-400 focus:bg-white focus:border-[#C81E2C] focus:ring-2 focus:ring-red-500/20 transition outline-none">
                        <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>

                    <div class="flex items-center gap-2 text-xs text-gray-400 font-medium">
                        <span>Total: <strong class="text-gray-700" x-text="filteredProjects.length"></strong> Proyek Ditemukan</span>
                    </div>
                </div>

                {{-- Table Content --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="border-b border-gray-100 bg-gray-50/50 text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                                <th class="py-3.5 px-4 sm:px-6">NAMA PROYEK & KLIEN</th>
                                <th class="py-3.5 px-4">DIVISI & PROJECT MANAGER</th>
                                <th class="py-3.5 px-4">TAHAP & STATUS PROSES</th>
                                <th class="py-3.5 px-4">TARGET DEADLINE</th>
                                <th class="py-3.5 px-4">PROGRESS FISIK</th>
                                <th class="py-3.5 px-4">CHECKLIST HANDOVER</th>
                                <th class="py-3.5 px-4 sm:px-6 text-right">AKSI</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 font-medium text-gray-700">
                            <template x-for="project in paginatedProjects" :key="project.id">
                                <tr class="hover:bg-gray-50/80 transition" :class="project.handover_status === 'Submitted' ? 'bg-blue-50/30' : ''">
                                    {{-- Kolom 1: Project & Client --}}
                                    <td class="py-3.5 px-4 sm:px-6">
                                        <div class="font-bold text-gray-900 text-xs" x-text="project.name"></div>
                                        <div class="text-[11px] text-gray-400 mt-0.5 flex items-center gap-1.5">
                                            <span class="font-semibold text-gray-600" x-text="project.client"></span>
                                            <span>&bull;</span>
                                            <span x-text="'Sales: ' + project.sales_name"></span>
                                        </div>
                                    </td>

                                    {{-- Kolom 2: Divisi & PIC PM --}}
                                    <td class="py-3.5 px-4">
                                        <div class="font-semibold text-gray-800" x-text="project.division"></div>
                                        <div class="text-[11px] text-gray-400 mt-0.5 flex items-center gap-1">
                                            <span>PM:</span>
                                            <span class="font-bold" :class="project.pm ? 'text-blue-600' : 'text-gray-400'" x-text="project.pm || 'Belum Ditentukan'"></span>
                                        </div>
                                    </td>

                                    {{-- Kolom 3: Tahap & Status Handover --}}
                                    <td class="py-3.5 px-4">
                                        <div class="flex flex-col gap-1 items-start">
                                            <span class="px-2 py-0.5 rounded-lg text-[10.5px] font-bold border"
                                                  :class="{
                                                      'bg-red-50 text-[#C81E2C] border-red-200': project.stage === 'Deliver',
                                                      'bg-blue-50 text-blue-700 border-blue-200': project.stage === 'Design',
                                                      'bg-emerald-50 text-emerald-700 border-emerald-200': project.stage === 'Operate',
                                                      'bg-gray-100 text-gray-700 border-gray-200': project.stage === 'Acquire'
                                                  }"
                                                  x-text="'Tahap ' + project.stage">
                                            </span>

                                            {{-- Handover Badge --}}
                                            <template x-if="project.handover_status === 'Submitted'">
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10.5px] font-bold bg-blue-100 text-blue-800 animate-pulse">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                                                    <span>Menunggu Verifikasi PMO</span>
                                                </span>
                                            </template>
                                            <template x-if="project.handover_status === 'Conditional'">
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10.5px] font-bold bg-amber-100 text-amber-800" :title="project.handover_conditional_notes">
                                                    <svg class="w-3 h-3 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                                    <span>Bersyarat (2x24 Jam)</span>
                                                </span>
                                            </template>
                                            <template x-if="project.handover_status === 'Approved'">
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10.5px] font-semibold bg-emerald-50 text-emerald-700">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                    <span>Handover Approved</span>
                                                </span>
                                            </template>
                                        </div>
                                    </td>

                                    {{-- Kolom 4: Target & Health --}}
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <div class="font-semibold text-gray-800" x-text="project.deadline"></div>
                                        <div class="mt-0.5">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-[10.5px] font-bold border"
                                                  :class="{
                                                      'bg-emerald-50 text-emerald-700 border-emerald-200': project.health_status === 'On-Track',
                                                      'bg-red-50 text-red-700 border-red-200': project.health_status === 'Delayed',
                                                      'bg-amber-50 text-amber-700 border-amber-200': project.health_status === 'At-Risk'
                                                  }">
                                                <span class="w-1.5 h-1.5 rounded-full" :class="{
                                                    'bg-emerald-500': project.health_status === 'On-Track',
                                                    'bg-red-500': project.health_status === 'Delayed',
                                                    'bg-amber-500': project.health_status === 'At-Risk'
                                                }"></span>
                                                <span x-text="project.health_status === 'On-Track' ? 'Tepat Waktu' : (project.health_status === 'Delayed' ? 'Terlambat' : 'Perlu Perhatian')"></span>
                                            </span>
                                        </div>
                                    </td>

                                    {{-- Kolom 5: Progres Fisik --}}
                                    <td class="py-3.5 px-4">
                                        <div class="w-28">
                                            <div class="flex items-center justify-between text-[11px] font-bold text-gray-900 mb-1">
                                                <span x-text="project.progress + '%'"></span>
                                                <span class="text-[10px] text-gray-400 font-normal" x-text="project.completed_tasks + '/' + project.total_tasks + ' Task'"></span>
                                            </div>
                                            <div class="w-full h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                                <div class="h-full bg-[#C81E2C] rounded-full transition-all duration-300" :style="'width: ' + project.progress + '%'"></div>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Kolom 6: Dokumen Deliverables --}}
                                    <td class="py-3.5 px-4">
                                        <button type="button" 
                                                @click="openDocumentsModal(project)"
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-gray-50 hover:bg-gray-100 border border-gray-200 text-xs font-semibold text-gray-700 transition cursor-pointer shadow-xs">
                                            <span class="font-bold text-blue-600" x-text="project.docs_completed_count + '/11'"></span>
                                            <span>Item Form</span>
                                            <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                            </svg>
                                        </button>
                                    </td>

                                    {{-- Kolom 7: Aksi --}}
                                    <td class="py-3.5 px-4 sm:px-6 text-right whitespace-nowrap">
                                        <div class="inline-flex items-center justify-end gap-1.5">
                                            {{-- Review Handover Button for PMO Gatekeeper --}}
                                            <template x-if="project.handover_status === 'Submitted' || project.handover_status === 'Conditional' || project.stage === 'Design'">
                                                <button type="button"
                                                        @click="openHandoverReviewModal(project)"
                                                        class="px-2.5 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-bold transition shadow-xs flex items-center gap-1 cursor-pointer">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    <span>Review Handover</span>
                                                </button>
                                            </template>

                                            <a :href="'/projects/' + project.id" 
                                               title="Lihat Detail Proyek"
                                               class="px-3 py-1 bg-white hover:bg-gray-50 text-gray-700 border border-gray-200 rounded-lg text-xs font-semibold transition shadow-xs">
                                                Detail
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            </template>

                            <template x-if="paginatedProjects.length === 0">
                                <tr>
                                    <td colspan="7" class="py-12 text-center text-gray-400">
                                        <div class="w-12 h-12 rounded-xl bg-gray-50 border border-gray-200 flex items-center justify-center mx-auto mb-3 text-gray-400">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                        </div>
                                        <p class="font-semibold text-gray-700 text-sm">Tidak ada proyek yang sesuai dengan kriteria filter</p>
                                        <p class="text-xs text-gray-400 mt-1">Silakan sesuaikan kata kunci pencarian atau filter divisi / tahap di atas.</p>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                {{-- Table Footer Pagination --}}
                <div class="p-3.5 sm:px-5 sm:py-3.5 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-gray-500 bg-white">
                    <div class="flex items-center gap-2 flex-wrap justify-center sm:justify-start">
                        <span>Tampilkan:</span>
                        <select x-model="perPage" @change="currentPage = 1" class="px-2 py-1 bg-white border border-gray-200 rounded-lg text-xs font-semibold text-gray-700 outline-none hover:border-gray-300 transition cursor-pointer">
                            <option value="10">10</option>
                            <option value="20">20</option>
                            <option value="30">30</option>
                            <option value="all">Semua</option>
                        </select>
                        <span class="text-gray-300">&bull;</span>
                        <div>
                            Menampilkan 
                            <span class="font-bold text-gray-800" x-text="filteredProjects.length > 0 ? (perPage === 'all' ? 1 : (currentPage - 1) * (parseInt(perPage) || 10) + 1) : 0"></span> &ndash; 
                            <span class="font-bold text-gray-800" x-text="perPage === 'all' ? filteredProjects.length : Math.min(currentPage * (parseInt(perPage) || 10), filteredProjects.length)"></span> 
                            dari <span class="font-bold text-gray-800" x-text="filteredProjects.length"></span> proyek
                        </div>
                    </div>

                    <div class="flex items-center gap-1" x-show="totalPages > 1 && perPage !== 'all'">
                        <button @click="prevPage()" :disabled="currentPage === 1" title="Sebelumnya" class="w-7 h-7 rounded-lg border border-gray-200 hover:bg-gray-50 text-gray-600 disabled:opacity-30 disabled:cursor-not-allowed flex items-center justify-center transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </button>
                        <template x-for="p in totalPages" :key="p">
                            <button @click="goToPage(p)" 
                                     :class="currentPage === p ? 'bg-[#C81E2C] text-white border-[#C81E2C] font-bold' : 'border-gray-200 hover:bg-gray-50 text-gray-700'"
                                     class="w-7 h-7 rounded-lg border text-xs font-medium flex items-center justify-center transition"
                                     x-text="p">
                            </button>
                        </template>
                        <button @click="nextPage()" :disabled="currentPage === totalPages" title="Berikutnya" class="w-7 h-7 rounded-lg border border-gray-200 hover:bg-gray-50 text-gray-600 disabled:opacity-30 disabled:cursor-not-allowed flex items-center justify-center transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                            </svg>
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
             class="fixed inset-0 bg-[#0E0D12]/60 z-50 flex items-center justify-center p-3 sm:p-5 overflow-y-auto"
             @click.self="handoverReviewModalOpen = false">
            <div class="bg-white rounded-2xl w-[720px] max-w-full max-h-[92vh] flex flex-col shadow-[0_20px_50px_rgba(14,13,18,0.25)] overflow-hidden my-6">
                {{-- Modal Header --}}
                <div class="p-5 sm:p-6 bg-[#FAF9F8] border-b border-gray-100 flex items-start justify-between">
                    <div>
                        <span class="text-[10.5px] font-bold uppercase tracking-wider text-blue-700 bg-blue-50 px-2.5 py-0.5 rounded-full border border-blue-200">
                            SOP Gatekeeper Handover Approval
                        </span>
                        <h3 class="text-base font-bold text-gray-900 mt-1.5" x-text="'Verifikasi Serah Terima: ' + activeProject?.name"></h3>
                        <p class="text-xs text-gray-500" x-text="'Klien: ' + activeProject?.client + ' • PIC Sales: ' + activeProject?.sales_name"></p>
                    </div>
                    <button type="button" @click="handoverReviewModalOpen = false" class="w-8 h-8 rounded-lg bg-white border border-gray-200 text-gray-400 hover:text-gray-600 flex items-center justify-center">
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
                        <h4 class="text-xs font-bold text-gray-900 uppercase tracking-wider">Hasil Verifikasi 4 Kategori Checklist Serah Terima</h4>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                            {{-- Kategori 1: Legal --}}
                            <div class="p-3 rounded-xl border border-gray-100 bg-gray-50/50 space-y-1.5">
                                <div class="font-bold text-gray-800 flex items-center justify-between">
                                    <span>I. Legal & Komersial</span>
                                    <span class="text-[10px] text-gray-400">Kontrak, BoW & Addendum</span>
                                </div>
                                <div class="text-gray-600 space-y-1 text-[11.5px]">
                                    <div class="flex items-center gap-1.5">
                                        <span :class="activeProject?.handover_data?.contract_signed ? 'text-emerald-600 font-bold' : 'text-gray-400'">&bull;</span>
                                        <span>Kontrak / SPK / SLA Signed: <strong x-text="activeProject?.handover_data?.contract_signed ? 'Ada' : 'Tidak'"></strong></span>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <span :class="activeProject?.handover_data?.bom_proposal ? 'text-emerald-600 font-bold' : 'text-gray-400'">&bull;</span>
                                        <span>Dokumen BoW (Proposal): <strong x-text="activeProject?.handover_data?.bom_proposal ? 'Ada' : 'Tidak'"></strong></span>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <span :class="activeProject?.handover_data?.negotiation_addendum ? 'text-emerald-600 font-bold' : 'text-gray-400'">&bull;</span>
                                        <span>BA Negosiasi / Addendum: <strong x-text="activeProject?.handover_data?.negotiation_addendum ? 'Ada' : 'Tidak'"></strong></span>
                                    </div>
                                </div>
                            </div>

                            {{-- Kategori 2: Lingkup --}}
                            <div class="p-3 rounded-xl border border-gray-100 bg-gray-50/50 space-y-1.5">
                                <div class="font-bold text-gray-800 flex items-center justify-between">
                                    <span>II. Kebutuhan & Lingkup</span>
                                    <span class="text-[10px] text-gray-400">SoW, URS, Mockup & Maint.</span>
                                </div>
                                <div class="text-gray-600 space-y-1 text-[11.5px]">
                                    <div class="flex items-center gap-1.5">
                                        <span :class="activeProject?.handover_data?.sow_document ? 'text-emerald-600 font-bold' : 'text-gray-400'">&bull;</span>
                                        <span>Dokumen SoW / Lingkup: <strong x-text="activeProject?.handover_data?.sow_document ? 'Ada' : 'Tidak'"></strong></span>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <span :class="activeProject?.handover_data?.urs_requirement ? 'text-emerald-600 font-bold' : 'text-gray-400'">&bull;</span>
                                        <span>Dokumen URS Klien: <strong x-text="activeProject?.handover_data?.urs_requirement ? 'Ada' : 'Tidak'"></strong></span>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <span :class="activeProject?.handover_data?.mockup_wireframe ? 'text-emerald-600 font-bold' : 'text-gray-400'">&bull;</span>
                                        <span>Mockup / Wireframe: <strong x-text="activeProject?.handover_data?.mockup_wireframe ? 'Ada' : 'Tidak'"></strong></span>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <span :class="activeProject?.handover_data?.maintenance_contract ? 'text-emerald-600 font-bold' : 'text-gray-400'">&bull;</span>
                                        <span>Kontrak Maintenance: <strong x-text="activeProject?.handover_data?.maintenance_contract ? 'Ada' : 'Tidak'"></strong></span>
                                    </div>
                                </div>
                            </div>

                            {{-- Kategori 3: Finansial --}}
                            <div class="p-3 rounded-xl border border-gray-100 bg-gray-50/50 space-y-1.5">
                                <div class="font-bold text-gray-800 flex items-center justify-between">
                                    <span>III. Administrasi & Finansial</span>
                                    <span class="text-[10px] text-gray-400">DP & Billing</span>
                                </div>
                                <div class="text-gray-600 space-y-1 text-[11.5px]">
                                    <div class="flex items-center gap-1.5">
                                        <span :class="activeProject?.handover_data?.dp_payment_proof ? 'text-emerald-600 font-bold' : 'text-gray-400'">&bull;</span>
                                        <span>Bukti Pembayaran DP: <strong x-text="activeProject?.handover_data?.dp_payment_proof ? 'Ada' : 'Tidak'"></strong></span>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <span :class="activeProject?.handover_data?.billing_milestone ? 'text-emerald-600 font-bold' : 'text-gray-400'">&bull;</span>
                                        <span>Jadwal Billing Milestone: <strong x-text="activeProject?.handover_data?.billing_milestone ? 'Ada' : 'Tidak'"></strong></span>
                                    </div>
                                </div>
                            </div>

                            {{-- Kategori 4: Kontak Customer PIC --}}
                            <div class="p-3 rounded-xl border border-gray-100 bg-gray-50/50 space-y-1.5">
                                <div class="font-bold text-gray-800 flex items-center justify-between">
                                    <span>IV. Kontak & Akses Customer</span>
                                    <span class="text-[10px] text-gray-400">PIC & Meeting Notes</span>
                                </div>
                                <div class="text-gray-600 space-y-1 text-[11.5px]">
                                    <div class="flex items-center gap-1.5">
                                        <span :class="activeProject?.handover_data?.customer_contacts ? 'text-emerald-600 font-bold' : 'text-gray-400'">&bull;</span>
                                        <span>Daftar Kontak PIC: <strong x-text="activeProject?.handover_data?.customer_contacts ? 'Ada' : 'Tidak'"></strong></span>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <span :class="activeProject?.handover_data?.meeting_notes ? 'text-emerald-600 font-bold' : 'text-gray-400'">&bull;</span>
                                        <span>Catatan Meeting Notes: <strong x-text="activeProject?.handover_data?.meeting_notes ? 'Ada' : 'Tidak'"></strong></span>
                                    </div>
                                    <div class="pt-1 text-[11px] text-gray-500 border-t border-gray-100">
                                        <span>PIC Teknis: <strong x-text="activeProject?.customer_pic_technical || '-'"></strong></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Catatan Khusus Sales --}}
                    <div class="p-3.5 rounded-xl border border-gray-100 bg-gray-50/60">
                        <div class="text-[11.5px] font-bold text-gray-700 mb-1">Catatan Khusus Sales ke Tim Delivery (Komitmen Lisan / Batasan Akses):</div>
                        <p class="text-xs text-gray-600 italic" x-text="activeProject?.special_notes || 'Tidak ada catatan khusus.'"></p>
                    </div>

                    {{-- Form Penugasan PM & Divisi untuk Transisi Lapangan --}}
                    <div class="p-4 rounded-xl border border-blue-100 bg-blue-50/30 space-y-3">
                        <div class="text-xs font-bold text-blue-900 flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            <span>Penetapan PIC Project Manager & Divisi Pelaksana</span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[11.5px] font-bold text-gray-700 mb-1">Tugaskan Project Manager (PM) <span class="text-red-500">*</span></label>
                                <select x-model="handoverAssign.pm_id" class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded-lg text-xs outline-none focus:border-[#C81E2C]">
                                    <option value="">-- Pilih Project Manager --</option>
                                    @foreach($pmList as $pm)
                                        <option value="{{ $pm->id }}">{{ $pm->name }} ({{ $pm->email }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11.5px] font-bold text-gray-700 mb-1">Divisi Pelaksana Proyek <span class="text-red-500">*</span></label>
                                <select x-model="handoverAssign.division_id" class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded-lg text-xs outline-none focus:border-[#C81E2C]">
                                    <option value="">-- Pilih Divisi --</option>
                                    @foreach($divisions as $div)
                                        <option value="{{ $div->id }}">{{ $div->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Form Input Catatan Bersyarat (Jika Memilih Conditional) --}}
                    <div x-show="showConditionalInput" class="p-3.5 rounded-xl border border-amber-200 bg-amber-50 space-y-2">
                        <label class="block text-xs font-bold text-amber-900">
                            Catatan Kekurangan Berkas / Lingkup untuk Tim Sales (Batas Waktu Revisi 2x24 Jam):
                        </label>
                        <textarea x-model="conditionalNotes" rows="2" placeholder="Tuliskan dokumen yang belum lengkap atau klausul yang perlu klarifikasi..." class="w-full px-3 py-1.5 bg-white border border-amber-300 rounded-lg text-xs outline-none focus:border-amber-600"></textarea>
                    </div>

                </div>

                {{-- Modal Footer Actions --}}
                <div class="p-4 sm:px-6 border-t border-gray-100 bg-[#FAF9F8] flex flex-wrap items-center justify-between gap-2">
                    <button type="button" @click="handoverReviewModalOpen = false" class="px-4 py-2 bg-white border border-gray-200 rounded-lg text-xs font-semibold text-gray-600 hover:bg-gray-50 transition cursor-pointer">
                        Tutup
                    </button>

                    <div class="flex items-center gap-2">
                        {{-- Tombol Bersyarat (Conditional) --}}
                        <button type="button" 
                                @click="handleConditionalClick()" 
                                class="px-3.5 py-2 bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-300 rounded-lg text-xs font-bold transition cursor-pointer">
                            <span x-text="showConditionalInput ? 'Kirim Catatan Revisi (2x24 Jam)' : 'Tandai Bersyarat (Conditional)'"></span>
                        </button>

                        {{-- Tombol Sahkan (Approved) --}}
                        <button type="button" 
                                @click="approveHandover()" 
                                class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold shadow-xs transition cursor-pointer flex items-center gap-1.5">
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
             class="fixed inset-0 bg-[#0E0D12]/60 z-50 flex items-center justify-center p-3 sm:p-5 overflow-y-auto"
             @click.self="docsModalOpen = false">
            <div class="bg-white rounded-2xl w-[860px] max-w-full max-h-[92vh] flex flex-col shadow-[0_20px_50px_rgba(14,13,18,0.25)] overflow-hidden my-6">
                
                {{-- Modal Header --}}
                <div class="p-5 sm:p-6 bg-[#FAF9F8] border-b border-gray-100 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <img src="{{ asset('images/ipnet1.png') }}" alt="IP Network Solusindo" class="h-11 w-auto object-contain shrink-0" onerror="this.src='/images/ipnet.png'">
                        <div>
                            <h3 class="text-base sm:text-[17px] font-black text-gray-900 uppercase tracking-tight">FORMULIR CHECK LIST SERAH TERIMA (INTERNAL HANDOVER)</h3>
                        </div>
                    </div>
                    <button type="button" @click="docsModalOpen = false" class="w-8 h-8 rounded-lg bg-white border border-gray-200 text-gray-400 hover:text-gray-600 flex items-center justify-center cursor-pointer shadow-xs">
                        &times;
                    </button>
                </div>

                {{-- Modal Body --}}
                <div class="p-5 sm:p-6 overflow-y-auto flex-1 space-y-4">
                    
                    {{-- INFORMASI PROJECT (PROJECT INFORMATION) --}}
                    <div class="border border-gray-900 rounded-xl overflow-hidden shadow-xs">
                        <div class="bg-gray-900 text-white font-extrabold text-xs uppercase px-4 py-2.5 tracking-wider">
                            INFORMASI PROJECT (PROJECT INFORMATION)
                        </div>
                        <div class="divide-y divide-gray-200 text-xs bg-white">
                            <div class="grid grid-cols-1 sm:grid-cols-3 divide-y sm:divide-y-0 sm:divide-x divide-gray-200">
                                <div class="p-2.5 bg-gray-50 font-semibold text-gray-600 sm:col-span-1">Nama Project</div>
                                <div class="p-2.5 font-bold text-gray-900 sm:col-span-2" x-text="activeProject?.name || '-'"></div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-3 divide-y sm:divide-y-0 sm:divide-x divide-gray-200">
                                <div class="p-2.5 bg-gray-50 font-semibold text-gray-600 sm:col-span-1">Kode Project (SO)</div>
                                <div class="p-2.5 font-mono font-bold text-gray-900 sm:col-span-2" x-text="activeProject?.so_code || '-'"></div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-3 divide-y sm:divide-y-0 sm:divide-x divide-gray-200">
                                <div class="p-2.5 bg-gray-50 font-semibold text-gray-600 sm:col-span-1">Tanggal Handover</div>
                                <div class="p-2.5 text-gray-800 sm:col-span-2" x-text="activeProject?.handover_submitted_at || '-'"></div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-3 divide-y sm:divide-y-0 sm:divide-x divide-gray-200">
                                <div class="p-2.5 bg-gray-50 font-semibold text-gray-600 sm:col-span-1">Pihak Penyerah (Commercial)</div>
                                <div class="p-2.5 font-semibold text-gray-900 sm:col-span-2" x-text="activeProject?.sales_name || '-'"></div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-3 divide-y sm:divide-y-0 sm:divide-x divide-gray-200">
                                <div class="p-2.5 bg-gray-50 font-semibold text-gray-600 sm:col-span-1">Pihak Penerima (Delivery)</div>
                                <div class="p-2.5 font-semibold text-blue-700 sm:col-span-2" x-text="(activeProject?.pm || 'Tim Delivery / PMO') + ' (' + (activeProject?.division || 'Lintas Divisi') + ')'"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Tabel 4 Kategori & 11 Items --}}
                    <div class="border border-gray-900 rounded-xl overflow-hidden shadow-xs">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="bg-gray-900 text-white text-[11px] font-bold uppercase tracking-wider">
                                    <th class="py-2.5 px-3 w-10 text-center border-r border-gray-700">No</th>
                                    <th class="py-2.5 px-4 w-44 border-r border-gray-700">Kategori Dokumen / Item</th>
                                    <th class="py-2.5 px-4 border-r border-gray-700">Nama Dokumen / Item</th>
                                    <th class="py-2.5 px-3 w-28 text-center border-r border-gray-700">Ada (Ada/Tidak)</th>
                                    <th class="py-2.5 px-4 w-48">Status / Keterangan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white text-gray-800">
                                @foreach($handoverFormStructure as $category)
                                    @foreach($category['items'] as $itemIndex => $item)
                                        <tr class="hover:bg-gray-50/80 transition" :class="activeDocs['{{ $item['key'] }}'] ? 'bg-emerald-50/30' : ''">
                                            {{-- No & Kategori Span (rendered on first item only) --}}
                                            @if($itemIndex === 0)
                                                <td rowspan="{{ count($category['items']) }}" class="py-3 px-3 font-extrabold text-gray-900 text-center align-top border-r border-gray-200 bg-gray-50/50">
                                                    {{ $category['number'] }}
                                                </td>
                                                <td rowspan="{{ count($category['items']) }}" class="py-3 px-4 font-bold text-gray-900 align-top border-r border-gray-200 bg-gray-50/50">
                                                    {{ $category['category_name'] }}
                                                </td>
                                            @endif
                                            
                                            {{-- Nama Dokumen --}}
                                            <td class="py-2.5 px-4 font-medium text-gray-800 border-r border-gray-200">
                                                {{ $item['name'] }}
                                            </td>
                                            
                                            {{-- Checkbox Ada / Tidak --}}
                                            <td class="py-2.5 px-3 text-center border-r border-gray-200 whitespace-nowrap">
                                                <label class="inline-flex items-center gap-1.5 cursor-pointer select-none">
                                                    <input type="checkbox" 
                                                           name="{{ $item['key'] }}"
                                                           x-model="activeDocs['{{ $item['key'] }}']"
                                                           class="w-4 h-4 text-[#C81E2C] rounded border-gray-300 focus:ring-red-500">
                                                    <span class="text-[11px] font-bold" :class="activeDocs['{{ $item['key'] }}'] ? 'text-emerald-700' : 'text-gray-400'" x-text="activeDocs['{{ $item['key'] }}'] ? 'Ada' : 'Tidak'"></span>
                                                </label>
                                            </td>

                                            {{-- Status / Keterangan --}}
                                            <td class="py-2.5 px-4 text-gray-500 text-[11px]">
                                                <span class="px-2 py-0.5 rounded bg-gray-100 border border-gray-200 text-gray-700 font-medium">
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
                <div class="p-4 sm:px-6 border-t border-gray-100 bg-[#FAF9F8] flex items-center justify-between gap-2">
                    <div class="text-xs text-gray-500">
                        Total Terpenuhi: <strong class="text-emerald-600" x-text="Object.values(activeDocs).filter(v => v === true).length + '/11 Item'"></strong>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" @click="docsModalOpen = false" class="px-4 py-2 bg-white border border-gray-200 rounded-lg text-xs font-semibold text-gray-600 hover:bg-gray-50 transition cursor-pointer">Tutup</button>
                        <button type="button" @click="saveDocuments()" class="px-4 py-2 bg-[#C81E2C] hover:brightness-105 active:translate-y-[1px] text-white rounded-lg text-xs font-bold shadow-[0_8px_20px_rgba(200,30,44,0.24)] transition cursor-pointer flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            <span>Simpan Formulir Serah Terima</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('pmoDashboard', () => ({
            projects: @json($formattedProjects),
            handoverFormStructure: @json($handoverFormStructure),
            stageCounts: @json($stageCounts),
            onTrackCount: {{ $onTrackCount }},
            delayedCount: {{ $delayedCount }},
            ho2Count: {{ $ho2Count }},
            ho3Count: {{ $ho3Count }},
            handoverPendingCount: {{ $handoverPendingCount }},
            handoverConditionalCount: {{ $handoverConditionalCount }},
            activeEngineersCount: {{ $activeEngineersCount }},
            standbyEngineersCount: {{ $standbyEngineersCount }},
            totalEngineersCount: {{ $totalEngineersCount }},

            search: '',
            selectedDivision: 'all',
            selectedPm: 'all',
            selectedStage: 'all',
            handoverFilter: 'all',
            perPage: 10,
            currentPage: 1,

            docsModalOpen: false,
            handoverReviewModalOpen: false,
            showConditionalInput: false,
            conditionalNotes: '',
            activeProject: null,
            activeDocs: {},
            handoverAssign: {
                pm_id: '',
                division_id: '',
            },

            get filteredProjects() {
                return this.projects.filter(p => {
                    const s = this.search.toLowerCase();
                    const matchSearch = (p.name || '').toLowerCase().includes(s) ||
                                        (p.client || '').toLowerCase().includes(s) ||
                                        (p.sales_name || '').toLowerCase().includes(s) ||
                                        (p.pm || '').toLowerCase().includes(s);
                    const matchDiv = this.selectedDivision === 'all' || String(p.division_id) === String(this.selectedDivision);
                    const matchPm  = this.selectedPm === 'all' || String(p.pm_id) === String(this.selectedPm);
                    const matchStg = this.selectedStage === 'all' || p.stage === this.selectedStage;
                    const matchHandover = this.handoverFilter === 'all' || (this.handoverFilter === 'pending' && (p.handover_status === 'Submitted' || p.handover_status === 'Conditional'));
                    return matchSearch && matchDiv && matchPm && matchStg && matchHandover;
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
                    const response = await fetch(`/pmo/projects/${this.activeProject.id}/documents`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ documents_checklist: this.activeDocs })
                    });

                    if (response.ok) {
                        const data = await response.json();
                        this.activeProject.documents_checklist = this.activeDocs;
                        let count = 0;
                        for (let k in this.activeDocs) {
                            if (this.activeDocs[k] === true) count++;
                        }
                        this.activeProject.docs_completed_count = count;
                        this.docsModalOpen = false;
                        this.showToast('Formulir Serah Terima (11 Item) berhasil disimpan!');
                    } else {
                        this.showToast('Gagal memperbarui formulir serah terima.');
                    }
                } catch (e) {
                    console.error(e);
                    this.showToast('Terjadi kesalahan jaringan.');
                }
            },

            showToast(msg) {
                if (window.dispatchEvent) {
                    window.dispatchEvent(new CustomEvent('toast-notify', { detail: { message: msg } }));
                } else {
                    alert(msg);
                }
            }
        }));
    });
</script>
@endpush
@endsection
