@extends('layouts.app')

@section('title', 'Dashboard PMO - Project Control Tower')

@section('content')
<div x-data="pmoDashboard()" x-cloak class="min-h-screen bg-[#F8F7F6] pb-12">
    
    {{-- TOPBAR CONTAINER --}}
    <div class="px-4 sm:px-6 lg:px-8 pt-6 pb-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-5 sm:p-6 rounded-2xl border border-[#E7E5E3] shadow-[0_1px_3px_rgba(0,0,0,0.03)]">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11.5px] font-bold bg-[#FDF1F2] text-[#C81E2C] border border-[#FCD4D7]">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#C81E2C] animate-pulse"></span>
                        PMO & Delivery Control Tower
                    </span>
                    <span class="text-[12px] text-[#75727C] font-medium">&bull; Ver. 2.0 Integrated Process</span>
                </div>
                <h1 class="text-[20px] sm:text-[24px] font-bold text-[#17151C] tracking-tight">
                    Pusat Kendali Portofolio Proyek
                </h1>
                <p class="text-[13px] sm:text-[13.5px] text-[#75727C] mt-0.5">
                    Monitoring siklus <span class="font-semibold text-[#17151C]">Acquire &rarr; Design &rarr; Deliver &rarr; Operate</span> dan serah terima dokumen wajib.
                </p>
            </div>

            {{-- Quick Filter Controls --}}
            <div class="flex items-center gap-2.5 flex-wrap">
                <div class="flex items-center gap-1.5 bg-[#F8F7F6] px-3 py-1.5 rounded-xl border border-[#E7E5E3]">
                    <span class="text-[11.5px] font-semibold text-[#75727C]">Divisi:</span>
                    <select x-model="selectedDivision" @change="currentPage = 1" class="bg-transparent text-[12.5px] font-bold text-[#17151C] outline-none cursor-pointer">
                        <option value="all">Semua Divisi</option>
                        @foreach($divisions as $div)
                            <option value="{{ $div->id }}">{{ $div->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center gap-1.5 bg-[#F8F7F6] px-3 py-1.5 rounded-xl border border-[#E7E5E3]">
                    <span class="text-[11.5px] font-semibold text-[#75727C]">PIC PM:</span>
                    <select x-model="selectedPm" @change="currentPage = 1" class="bg-transparent text-[12.5px] font-bold text-[#17151C] outline-none cursor-pointer">
                        <option value="all">Semua PM</option>
                        @foreach($pmList as $pm)
                            <option value="{{ $pm->id }}">{{ $pm->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- MAIN STATS (TOP 4 KPI CARDS) --}}
    <div class="px-4 sm:px-6 lg:px-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        {{-- Card 1: Total Deliver --}}
        <div class="bg-white p-5 rounded-2xl border border-[#E7E5E3] shadow-[0_1px_3px_rgba(0,0,0,0.03)] hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-[12px] font-bold text-[#75727C] uppercase tracking-wider">Proyek Deliver Aktif</span>
                <div class="w-8 h-8 rounded-lg bg-[#FDF1F2] text-[#C81E2C] flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                </div>
            </div>
            <div class="mt-3 flex items-baseline gap-2">
                <span class="text-[28px] font-bold text-[#17151C]" x-text="stageCounts.Deliver"></span>
                <span class="text-[12.5px] text-[#75727C] font-medium">Proyek Tahap 3</span>
            </div>
            <div class="mt-2 text-[11.5px] text-[#75727C] flex items-center gap-1.5">
                <span class="text-[#059669] font-bold" x-text="stageCounts.Deliver > 0 ? 'Sedang Berjalan' : 'Standby'"></span>
                <span>&bull; Domain Utama PMO & Delivery</span>
            </div>
        </div>

        {{-- Card 2: Timeline Health --}}
        <div class="bg-white p-5 rounded-2xl border border-[#E7E5E3] shadow-[0_1px_3px_rgba(0,0,0,0.03)] hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-[12px] font-bold text-[#75727C] uppercase tracking-wider">Kesehatan Timeline</span>
                <div class="w-8 h-8 rounded-lg bg-[#ECFDF5] text-[#059669] flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-3 flex items-baseline gap-2">
                <span class="text-[28px] font-bold text-[#059669]" x-text="onTrackCount"></span>
                <span class="text-[12.5px] text-[#75727C] font-medium">On-Track</span>
                <span class="text-[12.5px] text-[#EF4444] font-bold ml-auto" x-text="delayedCount + ' Delayed'"></span>
            </div>
            <div class="mt-2 text-[11.5px] text-[#75727C] flex items-center gap-1.5">
                <span class="inline-block w-2 h-2 rounded-full" :class="delayedCount > 0 ? 'bg-[#EF4444]' : 'bg-[#059669]'"></span>
                <span x-text="delayedCount > 0 ? delayedCount + ' proyek perlu mitigasi segera' : 'Seluruh jadwal terkendali aman'"></span>
            </div>
        </div>

        {{-- Card 3: Handover Gateways --}}
        <div class="bg-white p-5 rounded-2xl border border-[#E7E5E3] shadow-[0_1px_3px_rgba(0,0,0,0.03)] hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-[12px] font-bold text-[#75727C] uppercase tracking-wider">Gerbang Serah Terima</span>
                <div class="w-8 h-8 rounded-lg bg-[#EFF6FF] text-[#2563EB] flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                </div>
            </div>
            <div class="mt-3 flex items-baseline gap-2">
                <span class="text-[28px] font-bold text-[#2563EB]" x-text="ho3Count"></span>
                <span class="text-[12.5px] text-[#75727C] font-medium">Siap Handover 3</span>
            </div>
            <div class="mt-2 text-[11.5px] text-[#75727C] flex items-center gap-1.5">
                <span class="text-[#D97706] font-bold" x-text="ho2Count + ' di Design (HO 2)'"></span>
                <span>&bull; Menuju Manage Service</span>
            </div>
        </div>

        {{-- Card 4: Engineer Load --}}
        <div class="bg-white p-5 rounded-2xl border border-[#E7E5E3] shadow-[0_1px_3px_rgba(0,0,0,0.03)] hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-[12px] font-bold text-[#75727C] uppercase tracking-wider">Utilisasi Teknisi</span>
                <div class="w-8 h-8 rounded-lg bg-[#F5F3FF] text-[#7C3AED] flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-3 flex items-baseline gap-2">
                <span class="text-[28px] font-bold text-[#7C3AED]" x-text="activeEngineersCount"></span>
                <span class="text-[12.5px] text-[#75727C] font-medium">/ <span x-text="totalEngineersCount"></span> Di Lapangan</span>
            </div>
            <div class="mt-2 text-[11.5px] text-[#75727C] flex items-center gap-1.5">
                <span class="text-[#059669] font-bold" x-text="standbyEngineersCount + ' Standby / Siap Task'"></span>
                <span>&bull; Lintas Network & Security</span>
            </div>
        </div>
    </div>

    {{-- PIPELINE 4-TAHAP SIKLUS PROSES TERINTEGRASI VER 2.0 --}}
    <div class="px-4 sm:px-6 lg:px-8 mb-6">
        <div class="bg-white p-5 sm:p-6 rounded-2xl border border-[#E7E5E3] shadow-[0_1px_3px_rgba(0,0,0,0.03)]">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-[15px] font-bold text-[#17151C]">Alur Siklus Terintegrasi Ver. 2.0</h2>
                    <p class="text-[12px] text-[#75727C]">Klik tahapan untuk memfilter portofolio proyek di bawah</p>
                </div>
                <button type="button" @click="selectedStage = 'all'" class="text-[12px] font-bold text-[#C81E2C] hover:underline cursor-pointer">
                    Lihat Semua Tahap
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                {{-- 1. ACQUIRE --}}
                <div @click="selectedStage = (selectedStage === 'Acquire' ? 'all' : 'Acquire'); currentPage = 1;"
                     :class="selectedStage === 'Acquire' ? 'border-[#C81E2C] bg-[#FDF1F2] shadow-sm' : 'border-[#E7E5E3] hover:border-[#CBD5E1] bg-[#FAF9F8]'"
                     class="p-4 rounded-xl border transition cursor-pointer relative overflow-hidden">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-[10.5px] font-bold tracking-wider uppercase text-[#75727C]">1. ACQUIRE</span>
                        <span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-white border border-[#E7E5E3]" x-text="stageCounts.Acquire + ' Proyek'"></span>
                    </div>
                    <div class="font-bold text-[13.5px] text-[#17151C] mt-1">Mencari & Mendapatkan</div>
                    <div class="text-[11px] text-[#75727C] mt-1">Sales, BusDev &bull; PO / Kontrak</div>
                    <div class="mt-3 text-[10.5px] font-bold text-[#C81E2C] flex items-center gap-1">
                        <span>Handover 1 &rarr; Design</span>
                    </div>
                </div>

                {{-- 2. DESIGN --}}
                <div @click="selectedStage = (selectedStage === 'Design' ? 'all' : 'Design'); currentPage = 1;"
                     :class="selectedStage === 'Design' ? 'border-[#2563EB] bg-[#EFF6FF] shadow-sm' : 'border-[#E7E5E3] hover:border-[#CBD5E1] bg-[#FAF9F8]'"
                     class="p-4 rounded-xl border transition cursor-pointer relative overflow-hidden">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-[10.5px] font-bold tracking-wider uppercase text-[#75727C]">2. DESIGN</span>
                        <span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-white border border-[#E7E5E3]" x-text="stageCounts.Design + ' Proyek'"></span>
                    </div>
                    <div class="font-bold text-[13.5px] text-[#17151C] mt-1">Merancang Solusi</div>
                    <div class="text-[11px] text-[#75727C] mt-1">Pre-Sales & Arch &bull; Proposal & BoQ</div>
                    <div class="mt-3 text-[10.5px] font-bold text-[#2563EB] flex items-center gap-1">
                        <span>Handover 2 &rarr; Deliver</span>
                    </div>
                </div>

                {{-- 3. DELIVER (PMO DOMAIN) --}}
                <div @click="selectedStage = (selectedStage === 'Deliver' ? 'all' : 'Deliver'); currentPage = 1;"
                     :class="selectedStage === 'Deliver' ? 'border-[#C81E2C] bg-[#FDF1F2] shadow-sm' : 'border-[#E7E5E3] hover:border-[#CBD5E1] bg-[#FAF9F8]'"
                     class="p-4 rounded-xl border transition cursor-pointer relative overflow-hidden">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-[10.5px] font-bold tracking-wider uppercase text-[#C81E2C]">3. DELIVER (Fokus PMO)</span>
                        <span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-[#C81E2C] text-white" x-text="stageCounts.Deliver + ' Proyek'"></span>
                    </div>
                    <div class="font-bold text-[13.5px] text-[#17151C] mt-1">Melaksanakan Proyek</div>
                    <div class="text-[11px] text-[#75727C] mt-1">PMO, Lead & Eng &bull; WBS & UAT</div>
                    <div class="mt-3 text-[10.5px] font-bold text-[#059669] flex items-center gap-1">
                        <span>Handover 3 &rarr; Operate</span>
                    </div>
                </div>

                {{-- 4. OPERATE --}}
                <div @click="selectedStage = (selectedStage === 'Operate' ? 'all' : 'Operate'); currentPage = 1;"
                     :class="selectedStage === 'Operate' ? 'border-[#059669] bg-[#ECFDF5] shadow-sm' : 'border-[#E7E5E3] hover:border-[#CBD5E1] bg-[#FAF9F8]'"
                     class="p-4 rounded-xl border transition cursor-pointer relative overflow-hidden">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-[10.5px] font-bold tracking-wider uppercase text-[#75727C]">4. OPERATE</span>
                        <span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-white border border-[#E7E5E3]" x-text="stageCounts.Operate + ' Proyek'"></span>
                    </div>
                    <div class="font-bold text-[13.5px] text-[#17151C] mt-1">Mengelola Layanan</div>
                    <div class="text-[11px] text-[#75727C] mt-1">Manage Service &bull; SLA Report</div>
                    <div class="mt-3 text-[10.5px] font-bold text-[#75727C] flex items-center gap-1">
                        <span>Feedback &rarr; Acquire</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- TABEL PORTOFOLIO PROYEK & MATRIKS DOKUMEN --}}
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-2xl border border-[#E7E5E3] shadow-[0_1px_3px_rgba(0,0,0,0.03)] overflow-hidden">
            
            {{-- Table Filter & Search Header --}}
            <div class="p-4 sm:p-5 border-b border-[#EFEDEB] flex flex-col sm:flex-row items-center justify-between gap-3 bg-[#FAF9F8]">
                <div class="relative w-full sm:w-80">
                    <input type="text" 
                           x-model="search" 
                           @input="currentPage = 1" 
                           placeholder="Cari nama project, klien, sales, atau PIC..." 
                           class="w-full pl-9 pr-4 py-2 bg-white border border-[#E7E5E3] rounded-xl text-[13px] text-[#17151C] placeholder-[#A19DA8] focus:border-[#C81E2C] focus:ring-1 focus:ring-[#C81E2C] transition outline-none">
                    <svg class="w-4 h-4 text-[#A19DA8] absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>

                <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                    <span class="text-[12px] text-[#75727C] font-medium" x-text="'Total ' + filteredProjects.length + ' Proyek Ditemukan'"></span>
                </div>
            </div>

            {{-- Table Content --}}
            <div class="overflow-x-auto">
                <table class="w-full text-left text-[13px]">
                    <thead>
                        <tr class="border-b border-[#EFEDEB] text-[11px] font-bold text-[#75727C] uppercase tracking-wider bg-[#FBFBFA]">
                            <th class="py-3 px-4 sm:px-6">Nama Project & Klien</th>
                            <th class="py-3 px-4">Divisi & PIC</th>
                            <th class="py-3 px-4">Tahap & Status</th>
                            <th class="py-3 px-4">Target & Health</th>
                            <th class="py-3 px-4">Progres Fisik</th>
                            <th class="py-3 px-4">Dokumen Wajib (8 Ver 2.0)</th>
                            <th class="py-3 px-4 sm:px-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#EFEDEB]">
                        <template x-for="project in paginatedProjects" :key="project.id">
                            <tr class="hover:bg-[#FDFCFB] transition">
                                {{-- Kolom 1: Project & Client --}}
                                <td class="py-4 px-4 sm:px-6">
                                    <div class="font-bold text-[#17151C] text-[13.5px]" x-text="project.name"></div>
                                    <div class="text-[12px] text-[#75727C] mt-0.5 flex items-center gap-1.5">
                                        <span x-text="project.client"></span>
                                        <span>&bull;</span>
                                        <span class="text-[#A19DA8]" x-text="'Sales: ' + project.sales_name"></span>
                                    </div>
                                </td>

                                {{-- Kolom 2: Divisi & PIC PM --}}
                                <td class="py-4 px-4">
                                    <div class="font-semibold text-[#17151C]" x-text="project.division"></div>
                                    <div class="text-[11.5px] text-[#75727C] mt-0.5 flex items-center gap-1">
                                        <span>PM:</span>
                                        <span class="font-bold text-[#2563EB]" x-text="project.pm"></span>
                                    </div>
                                </td>

                                {{-- Kolom 3: Tahap & Status --}}
                                <td class="py-4 px-4">
                                    <div class="flex flex-col gap-1 items-start">
                                        <span class="px-2 py-0.5 rounded text-[11px] font-bold"
                                              :class="{
                                                  'bg-[#FDF1F2] text-[#C81E2C] border border-[#FCD4D7]': project.stage === 'Deliver',
                                                  'bg-[#EFF6FF] text-[#2563EB] border border-[#DBEAFE]': project.stage === 'Design',
                                                  'bg-[#ECFDF5] text-[#059669] border border-[#D1FAE5]': project.stage === 'Operate',
                                                  'bg-[#F1F5F9] text-[#475569] border border-[#E2E8F0]': project.stage === 'Acquire'
                                              }"
                                              x-text="'Tahap ' + project.stage">
                                        </span>
                                        <span class="text-[11.5px] font-semibold text-[#64748B]" x-text="project.process_status"></span>
                                    </div>
                                </td>

                                {{-- Kolom 4: Target & Health --}}
                                <td class="py-4 px-4">
                                    <div class="font-semibold text-[#17151C]" x-text="project.deadline"></div>
                                    <div class="mt-0.5">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10.5px] font-bold"
                                              :class="{
                                                  'bg-[#ECFDF5] text-[#059669]': project.health_status === 'On-Track',
                                                  'bg-[#FEF2F2] text-[#EF4444]': project.health_status === 'Delayed',
                                                  'bg-[#FFFBEB] text-[#D97706]': project.health_status === 'At-Risk'
                                              }">
                                            <span class="w-1.5 h-1.5 rounded-full" :class="{
                                                'bg-[#059669]': project.health_status === 'On-Track',
                                                'bg-[#EF4444]': project.health_status === 'Delayed',
                                                'bg-[#D97706]': project.health_status === 'At-Risk'
                                            }"></span>
                                            <span x-text="project.health_status"></span>
                                        </span>
                                    </div>
                                </td>

                                {{-- Kolom 5: Progres Fisik --}}
                                <td class="py-4 px-4">
                                    <div class="w-28">
                                        <div class="flex items-center justify-between text-[11px] font-bold text-[#17151C] mb-1">
                                            <span x-text="project.progress + '%'"></span>
                                            <span class="text-[10px] text-[#75727C]" x-text="project.completed_tasks + '/' + project.total_tasks + ' Task'"></span>
                                        </div>
                                        <div class="w-full h-1.5 bg-[#E7E5E3] rounded-full overflow-hidden">
                                            <div class="h-full bg-[#C81E2C] rounded-full transition-all duration-300" :style="'width: ' + project.progress + '%'"></div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Kolom 6: Dokumen Deliverables --}}
                                <td class="py-4 px-4">
                                    <button type="button" 
                                            @click="openDocumentsModal(project)"
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-[#FAF9F8] hover:bg-[#F1F0EE] border border-[#E7E5E3] text-[12px] font-semibold text-[#3D3A44] transition cursor-pointer">
                                        <span class="font-bold text-[#2563EB]" x-text="project.docs_completed_count + '/8'"></span>
                                        <span>Dokumen</span>
                                        <svg class="w-3.5 h-3.5 text-[#75727C]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </button>
                                </td>

                                {{-- Kolom 7: Aksi --}}
                                <td class="py-4 px-4 sm:px-6 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <button type="button" 
                                                @click="openGatewayModal(project)"
                                                title="Ubah Tahap / Approval Gateway" 
                                                class="px-2.5 py-1.5 bg-white hover:bg-[#FDF1F2] text-[#C81E2C] border border-[#E7E5E3] hover:border-[#FCD4D7] rounded-lg text-[12px] font-bold transition cursor-pointer">
                                            Gateway
                                        </button>
                                        <a :href="'/projects/' + project.id" 
                                           title="Detail Proyek"
                                           class="p-1.5 bg-white hover:bg-[#F1F0EE] text-[#475569] border border-[#E7E5E3] rounded-lg transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        </template>

                        <template x-if="paginatedProjects.length === 0">
                            <tr>
                                <td colspan="7" class="py-12 text-center text-[#75727C]">
                                    <div class="w-12 h-12 rounded-xl bg-[#F1F0EE] flex items-center justify-center mx-auto mb-3 text-[#A19DA8]">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    <p class="font-semibold text-[#17151C]">Tidak ada proyek yang sesuai dengan filter</p>
                                    <p class="text-[12px] text-[#A19DA8] mt-1">Coba sesuaikan kata kunci pencarian atau filter tahap di atas.</p>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            {{-- Table Footer Pagination & Items Per Page Selector --}}
            <div class="p-3.5 sm:px-5 sm:py-3.5 border-t border-[#EFEDEB] flex flex-col sm:flex-row items-center justify-between gap-3 text-[12.5px] text-[#75727C]">
                <div class="flex items-center gap-2 flex-wrap justify-center sm:justify-start">
                    <span>Tampilkan:</span>
                    <select x-model="perPage" @change="currentPage = 1" class="px-2 py-1 bg-white border border-[#E7E5E3] rounded-lg text-[12px] font-semibold text-[#17151C] outline-none hover:border-[#CBD5E1] transition cursor-pointer">
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="30">30</option>
                        <option value="all">Semua</option>
                    </select>
                    <span class="text-[#A19DA8]">&bull;</span>
                    <div>
                        Menampilkan 
                        <span class="font-medium text-[#17151C]" x-text="filteredProjects.length > 0 ? (perPage === 'all' ? 1 : (currentPage - 1) * (parseInt(perPage) || 10) + 1) : 0"></span> &ndash; 
                        <span class="font-medium text-[#17151C]" x-text="perPage === 'all' ? filteredProjects.length : Math.min(currentPage * (parseInt(perPage) || 10), filteredProjects.length)"></span> 
                        dari <span class="font-medium text-[#17151C]" x-text="filteredProjects.length"></span> project
                    </div>
                </div>

                <div class="flex items-center gap-1" x-show="totalPages > 1 && perPage !== 'all'">
                    <button @click="prevPage()" :disabled="currentPage === 1" title="Sebelumnya" class="w-7 h-7 rounded-md border border-[#E7E5E3] hover:bg-[#F1F0EE] text-[#3D3A44] disabled:opacity-30 disabled:cursor-not-allowed flex items-center justify-center transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </button>
                    <template x-for="p in totalPages" :key="p">
                        <button @click="goToPage(p)" 
                                :class="currentPage === p ? 'bg-[#C81E2C] text-white border-[#C81E2C] font-bold' : 'border-[#E7E5E3] hover:bg-[#F1F0EE] text-[#17151C]'"
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
    </div>

    {{-- MODAL CHECKLIST DOKUMEN DELIVERABLE (8 DOKUMEN VER 2.0) --}}
    <template x-teleport="body">
        <div x-show="docsModalOpen" 
             x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-[#0E0D12]/60 z-50 flex items-center justify-center p-3 sm:p-5"
             @click.self="docsModalOpen = false">
            <div class="bg-white rounded-2xl w-[640px] max-w-full max-h-[90vh] flex flex-col shadow-[0_20px_50px_rgba(14,13,18,0.2)] overflow-hidden">
                
                {{-- Modal Header --}}
                <div class="p-5 sm:p-6 bg-[#FAF9F8] border-b border-[#E7E5E3] flex items-start justify-between">
                    <div>
                        <span class="text-[11px] font-bold uppercase tracking-wider text-[#C81E2C] bg-[#FDF1F2] px-2.5 py-0.5 rounded-full border border-[#FCD4D7]">
                            Paket Serah Terima (Handover Package)
                        </span>
                        <h3 class="text-[18px] font-bold text-[#17151C] mt-1.5" x-text="activeProject?.name"></h3>
                        <p class="text-[12.5px] text-[#75727C]" x-text="'Klien: ' + activeProject?.client"></p>
                    </div>
                    <button type="button" @click="docsModalOpen = false" class="w-8 h-8 rounded-lg bg-white border border-[#E7E5E3] text-[#75727C] hover:bg-[#F1F0EE] flex items-center justify-center">
                        &times;
                    </button>
                </div>

                {{-- Modal Body (8 Dokumen Checklist) --}}
                <div class="p-5 sm:p-6 overflow-y-auto flex-1 space-y-3">
                    <p class="text-[12.5px] text-[#75727C] mb-3">
                        Centang dokumen yang telah lengkap diverifikasi sebelum serah terima ke tim operasional (*Operate*):
                    </p>

                    <div class="space-y-2.5">
                        <template x-for="(docName, docKey) in documentKeys" :key="docKey">
                            <label class="flex items-center gap-3 p-3 rounded-xl border border-[#E7E5E3] hover:bg-[#FAF9F8] transition cursor-pointer">
                                <input type="checkbox" 
                                       x-model="activeDocs[docKey]" 
                                       class="w-4 h-4 text-[#C81E2C] rounded border-[#CBD5E1] focus:ring-[#C81E2C] cursor-pointer">
                                <span class="text-[13px] font-semibold text-[#17151C]" x-text="docName"></span>
                            </label>
                        </template>
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="p-4 sm:px-6 border-t border-[#E7E5E3] bg-[#FAF9F8] flex items-center justify-end gap-2.5">
                    <button type="button" @click="docsModalOpen = false" class="px-4 py-2 bg-white border border-[#E7E5E3] rounded-xl text-[13px] font-semibold text-[#475569] hover:bg-[#F1F0EE] transition cursor-pointer">
                        Batal
                    </button>
                    <button type="button" @click="saveDocuments()" class="px-5 py-2 bg-[#C81E2C] hover:bg-[#AF1424] text-white rounded-xl text-[13px] font-bold shadow-[0_4px_14px_rgba(200,30,44,0.25)] transition cursor-pointer">
                        Simpan Checklist Dokumen
                    </button>
                </div>
            </div>
        </div>
    </template>

    {{-- MODAL GATEWAY / UPDATE TAHAP SIKLUS --}}
    <template x-teleport="body">
        <div x-show="gatewayModalOpen" 
             x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-[#0E0D12]/60 z-50 flex items-center justify-center p-3 sm:p-5"
             @click.self="gatewayModalOpen = false">
            <div class="bg-white rounded-2xl w-[520px] max-w-full shadow-[0_20px_50px_rgba(14,13,18,0.2)] overflow-hidden">
                
                {{-- Header --}}
                <div class="p-5 sm:p-6 bg-[#FAF9F8] border-b border-[#E7E5E3] flex items-start justify-between">
                    <div>
                        <span class="text-[11px] font-bold uppercase tracking-wider text-[#2563EB] bg-[#EFF6FF] px-2.5 py-0.5 rounded-full border border-[#DBEAFE]">
                            Gerbang Serah Terima (Gateway Approval)
                        </span>
                        <h3 class="text-[18px] font-bold text-[#17151C] mt-1.5" x-text="activeProject?.name"></h3>
                    </div>
                    <button type="button" @click="gatewayModalOpen = false" class="w-8 h-8 rounded-lg bg-white border border-[#E7E5E3] text-[#75727C] hover:bg-[#F1F0EE] flex items-center justify-center">
                        &times;
                    </button>
                </div>

                {{-- Form Body --}}
                <div class="p-5 sm:p-6 space-y-4">
                    <div>
                        <label class="block text-[12.5px] font-bold text-[#17151C] mb-1.5">Tahapan Siklus Ver. 2.0</label>
                        <select x-model="gatewayForm.stage" class="w-full px-3.5 py-2.5 bg-white border border-[#CBD5E1] rounded-xl text-[13px] font-semibold text-[#17151C] outline-none focus:border-[#C81E2C]">
                            <option value="Acquire">1. ACQUIRE (Peluang / Kontrak PO)</option>
                            <option value="Design">2. DESIGN (Proposal & BoQ Disetujui)</option>
                            <option value="Deliver">3. DELIVER (Pelaksanaan Proyek PMO & Tim Teknis)</option>
                            <option value="Operate">4. OPERATE (Serah Terima Manage Service & SLA)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[12.5px] font-bold text-[#17151C] mb-1.5">Status Proses</label>
                        <select x-model="gatewayForm.process_status" class="w-full px-3.5 py-2.5 bg-white border border-[#CBD5E1] rounded-xl text-[13px] font-semibold text-[#17151C] outline-none focus:border-[#C81E2C]">
                            <option value="Belum Mulai">Belum Mulai</option>
                            <option value="In Progress">In Progress (Sedang Berjalan)</option>
                            <option value="Menunggu Handover">Menunggu Handover (Siap Serah Terima)</option>
                            <option value="Selesai">Selesai</option>
                            <option value="Dibatalkan">Dibatalkan</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[12.5px] font-bold text-[#17151C] mb-1.5">Tugaskan PIC Project Manager (PM)</label>
                        <select x-model="gatewayForm.pm_id" class="w-full px-3.5 py-2.5 bg-white border border-[#CBD5E1] rounded-xl text-[13px] font-semibold text-[#17151C] outline-none focus:border-[#C81E2C]">
                            <option value="">-- Pilih PIC PM --</option>
                            @foreach($pmList as $pm)
                                <option value="{{ $pm->id }}">{{ $pm->name }} ({{ $pm->email }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="p-4 sm:px-6 border-t border-[#E7E5E3] bg-[#FAF9F8] flex items-center justify-end gap-2.5">
                    <button type="button" @click="gatewayModalOpen = false" class="px-4 py-2 bg-white border border-[#E7E5E3] rounded-xl text-[13px] font-semibold text-[#475569] hover:bg-[#F1F0EE] transition cursor-pointer">
                        Batal
                    </button>
                    <button type="button" @click="saveGateway()" class="px-5 py-2 bg-[#C81E2C] hover:bg-[#AF1424] text-white rounded-xl text-[13px] font-bold shadow-[0_4px_14px_rgba(200,30,44,0.25)] transition cursor-pointer">
                        Simpan Perubahan Tahap
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
            projects: @json($formattedProjects),
            documentKeys: @json($documentKeys),
            stageCounts: @json($stageCounts),
            onTrackCount: {{ $onTrackCount }},
            delayedCount: {{ $delayedCount }},
            ho2Count: {{ $ho2Count }},
            ho3Count: {{ $ho3Count }},
            activeEngineersCount: {{ $activeEngineersCount }},
            standbyEngineersCount: {{ $standbyEngineersCount }},
            totalEngineersCount: {{ $totalEngineersCount }},

            search: '',
            selectedDivision: 'all',
            selectedPm: 'all',
            selectedStage: 'all',
            perPage: 10,
            currentPage: 1,

            docsModalOpen: false,
            gatewayModalOpen: false,
            activeProject: null,
            activeDocs: {},
            gatewayForm: {
                stage: 'Deliver',
                process_status: 'In Progress',
                pm_id: ''
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
                    return matchSearch && matchDiv && matchPm && matchStg;
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
                        this.showToast('Checklist 8 dokumen berhasil diperbarui!');
                    } else {
                        this.showToast('Gagal memperbarui checklist dokumen.');
                    }
                } catch (e) {
                    console.error(e);
                    this.showToast('Terjadi kesalahan jaringan.');
                }
            },

            openGatewayModal(project) {
                this.activeProject = project;
                this.gatewayForm = {
                    stage: project.stage || 'Deliver',
                    process_status: project.process_status || 'In Progress',
                    pm_id: project.pm_id || ''
                };
                this.gatewayModalOpen = true;
            },

            async saveGateway() {
                try {
                    const response = await fetch(`/pmo/projects/${this.activeProject.id}/stage`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify(this.gatewayForm)
                    });

                    if (response.ok) {
                        const data = await response.json();
                        this.activeProject.stage = this.gatewayForm.stage;
                        this.activeProject.process_status = this.gatewayForm.process_status;
                        this.activeProject.pm_id = this.gatewayForm.pm_id;
                        this.gatewayModalOpen = false;
                        this.showToast('Tahapan siklus & status berhasil diperbarui!');
                        setTimeout(() => window.location.reload(), 600);
                    } else {
                        this.showToast('Gagal memperbarui tahapan siklus.');
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
