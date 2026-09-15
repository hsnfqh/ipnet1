@extends('layouts.app')

@section('title', 'Peluang & Kontrak - Sales Pipeline (Acquire)')

@section('content')
<div class="flex h-screen overflow-hidden">
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Peluang & Kontrak (Acquire)'])
        
        <div class="p-4 sm:p-5 lg:p-[26px] animate-fade-in" x-data="acquireApp()" x-cloak>
            

            {{-- TOP 4 KPI CARDS --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5 mb-5">
                {{-- KPI 1: Total Peluang --}}
                <div class="bg-white p-4 sm:p-5 rounded-2xl border border-[#E7E5E3] shadow-[0_1px_2px_rgba(0,0,0,0.03)] hover:border-[#CBD5E1] transition">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[11.5px] font-bold uppercase tracking-wider text-[#75727C]">Total Peluang</span>
                        <div class="w-8 h-8 rounded-lg bg-[#F8F7F6] border border-[#E7E5E3] text-[#17151C] flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                        </div>
                    </div>
                    <div class="flex items-baseline gap-2">
                        <span class="text-[26px] font-bold text-[#17151C]" x-text="projects.length"></span>
                        <span class="text-[12px] text-[#75727C]">Calon Proyek</span>
                    </div>
                    <div class="text-[11.5px] font-medium text-[#75727C] mt-1">
                        Domain Tim Sales & BusDev
                    </div>
                </div>

                {{-- KPI 2: Negosiasi / Penawaran --}}
                <div class="bg-white p-4 sm:p-5 rounded-2xl border border-[#E7E5E3] shadow-[0_1px_2px_rgba(0,0,0,0.03)] hover:border-[#CBD5E1] transition">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[11.5px] font-bold uppercase tracking-wider text-[#75727C]">Tahap Negosiasi</span>
                        <div class="w-8 h-8 rounded-lg bg-[#FEF3C7] text-[#D97706] flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="flex items-baseline gap-2">
                        <span class="text-[26px] font-bold text-[#D97706]" x-text="inNegotiationCount"></span>
                        <span class="text-[12px] text-[#75727C]">Proposal Komersial</span>
                    </div>
                    <div class="text-[11.5px] font-medium text-[#D97706] mt-1">
                        Sedang dalam pembahasan harga
                    </div>
                </div>

                {{-- KPI 3: Kontrak Deal / PO Terbit --}}
                <div class="bg-white p-4 sm:p-5 rounded-2xl border border-[#E7E5E3] shadow-[0_1px_2px_rgba(0,0,0,0.03)] hover:border-[#CBD5E1] transition">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[11.5px] font-bold uppercase tracking-wider text-[#75727C]">Deal / PO Terbit</span>
                        <div class="w-8 h-8 rounded-lg bg-[#DEF7EC] text-[#03543F] flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="flex items-baseline gap-2">
                        <span class="text-[26px] font-bold text-[#057A55]" x-text="dealPoCount"></span>
                        <span class="text-[12px] text-[#75727C]">PO Resmi</span>
                    </div>
                    <div class="text-[11.5px] font-medium text-[#057A55] mt-1">
                        Kontrak resmi disepakati
                    </div>
                </div>

                {{-- KPI 4: Diserahkan ke Design --}}
                <div class="bg-white p-4 sm:p-5 rounded-2xl border border-[#E7E5E3] shadow-[0_1px_2px_rgba(0,0,0,0.03)] hover:border-[#CBD5E1] transition">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[11.5px] font-bold uppercase tracking-wider text-[#75727C]">Handover 1 Selesai</span>
                        <div class="w-8 h-8 rounded-lg bg-[#F3E8FF] text-[#7E22CE] flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
                            </svg>
                        </div>
                    </div>
                    <div class="flex items-baseline gap-2">
                        <span class="text-[26px] font-bold text-[#7E22CE]" x-text="handoverDesignCount"></span>
                        <span class="text-[12px] text-[#75727C]">Di Tim Design</span>
                    </div>
                    <div class="text-[11.5px] font-medium text-[#7E22CE] mt-1">
                        Tahap perancangan arsitektur & BoQ
                    </div>
                </div>
            </div>

            {{-- TABLE CARD --}}
            <div class="bg-white rounded-2xl border border-[#E7E5E3] shadow-[0_1px_3px_rgba(0,0,0,0.03)] overflow-hidden">
                
                {{-- Table Controls & Filter Bar --}}
                <div class="p-4 sm:p-5 border-b border-[#E7E5E3] flex flex-col md:flex-row md:items-center justify-between gap-3.5 bg-[#FAF9F8]">
                    <div class="flex items-center gap-2.5 flex-1 max-w-lg">
                        <div class="relative w-full">
                            <svg class="w-4 h-4 absolute left-3 top-3 text-[#948F99]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input type="text" 
                                   x-model="search"
                                   @input="currentPage = 1"
                                   placeholder="Cari nama peluang, klien, nomor PO, atau sales..." 
                                   class="w-full px-3.5 py-2 pl-9 rounded-xl border border-[#E7E5E3] text-[13.5px] text-[#17151C] outline-none bg-white focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition">
                        </div>
                    </div>

                    <div class="flex items-center gap-2.5 flex-wrap">
                        {{-- Status Filter --}}
                        <select x-model="statusFilter" @change="currentPage = 1" class="px-3 py-2 rounded-xl border border-[#E7E5E3] text-[13px] font-medium text-[#17151C] bg-white outline-none focus:border-[#C81E2C]">
                            <option value="all">Semua Status Pipeline</option>
                            <option value="Prospek Awal">Prospek Awal</option>
                            <option value="Kualifikasi Kebutuhan">Kualifikasi Kebutuhan</option>
                            <option value="Penawaran Komersial">Penawaran Komersial</option>
                            <option value="Deal / PO Terbit">Deal / PO Terbit</option>
                            <option value="Handover to Design">Handover ke Design</option>
                        </select>

                        {{-- Sales Filter --}}
                        <select x-model="salesFilter" @change="currentPage = 1" class="px-3 py-2 rounded-xl border border-[#E7E5E3] text-[13px] font-medium text-[#17151C] bg-white outline-none focus:border-[#C81E2C]">
                            <option value="all">Semua PIC Sales</option>
                            <template x-for="s in salesList" :key="s">
                                <option :value="s" x-text="s"></option>
                            </template>
                        </select>

                        {{-- Add Button --}}
                        <button @click="openCreateModal()"
                                class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-[#C81E2C] hover:brightness-105 active:translate-y-[1px] text-white text-xs font-semibold rounded-lg shadow-[0_8px_20px_rgba(200,30,44,0.24)] transition-all cursor-pointer whitespace-nowrap">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span>Tambah Peluang</span>
                        </button>
                    </div>
                </div>

                {{-- Table Body --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-[#F8F7F6] border-b border-[#E7E5E3] text-[11.5px] font-bold uppercase tracking-wider text-[#75727C]">
                                <th class="py-3.5 px-4 sm:px-6">Nama Peluang & Klien</th>
                                <th class="py-3.5 px-4">PIC Sales</th>
                                <th class="py-3.5 px-4">Nilai Kontrak (PO)</th>
                                <th class="py-3.5 px-4">Target Selesai</th>
                                <th class="py-3.5 px-4">Status Pipeline</th>
                                <th class="py-3.5 px-4 text-center">Berkas PO</th>
                                <th class="py-3.5 px-4 sm:px-6 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#E7E5E3] text-[13.5px]">
                            <template x-for="item in paginatedProjects" :key="item.id">
                                <tr class="hover:bg-[#FDFBFB] transition group">
                                    {{-- Name & Client --}}
                                    <td class="py-3.5 px-4 sm:px-6">
                                        <div class="font-bold text-[#17151C] group-hover:text-[#C81E2C] transition" x-text="item.name"></div>
                                        <div class="text-[12px] text-[#75727C] flex items-center gap-1.5 mt-0.5">
                                            <span class="font-medium text-[#475569]" x-text="item.client"></span>
                                            <span class="text-[#CBD5E1]">&bull;</span>
                                            <span class="text-[11.5px] text-[#948F99]" x-text="item.division_name"></span>
                                        </div>
                                    </td>

                                    {{-- Sales --}}
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-[#F8F7F6] border border-[#E7E5E3] text-[12px] font-semibold text-[#17151C]">
                                            <svg class="w-3.5 h-3.5 text-[#75727C]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                            <span x-text="item.sales_name"></span>
                                        </div>
                                    </td>

                                    {{-- Nilai Kontrak --}}
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <div class="font-bold text-[#17151C] text-[13px]" x-text="item.contract_formatted"></div>
                                        <div class="text-[11px] text-[#75727C]" x-text="'PO: ' + item.po_number"></div>
                                    </td>

                                    {{-- Deadline --}}
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <div class="font-medium text-[#17151C] text-[12.5px]" x-text="item.deadline_formatted"></div>
                                    </td>

                                    {{-- Status Badge --}}
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <div class="flex flex-col gap-1 items-start">
                                            <template x-if="item.stage === 'Design' || item.acquire_status === 'Handover to Design'">
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11.5px] font-bold border bg-[#F3E8FF] text-[#6B21A8] border-[#E9D5FF]">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-[#6B21A8]"></span>
                                                    <span>Tahap Desain (Presales)</span>
                                                </span>
                                            </template>
                                            <template x-if="item.stage !== 'Design' && item.acquire_status !== 'Handover to Design'">
                                                <span :class="{
                                                    'bg-[#F3F4F6] text-[#4B5563] border-[#E5E7EB]': item.acquire_status === 'Prospek Awal',
                                                    'bg-[#EFF6FF] text-[#1D4ED8] border-[#DBEAFE]': item.acquire_status === 'Kualifikasi Kebutuhan',
                                                    'bg-[#FEF3C7] text-[#B45309] border-[#FDE68A]': item.acquire_status === 'Penawaran Komersial',
                                                    'bg-[#DEF7EC] text-[#03543F] border-[#BCF0DA]': item.acquire_status === 'Deal / PO Terbit'
                                                }" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11.5px] font-bold border">
                                                    <span class="w-1.5 h-1.5 rounded-full" :class="{
                                                        'bg-[#4B5563]': item.acquire_status === 'Prospek Awal',
                                                        'bg-[#1D4ED8]': item.acquire_status === 'Kualifikasi Kebutuhan',
                                                        'bg-[#B45309]': item.acquire_status === 'Penawaran Komersial',
                                                        'bg-[#03543F]': item.acquire_status === 'Deal / PO Terbit'
                                                    }"></span>
                                                    <span x-text="item.acquire_status"></span>
                                                </span>
                                            </template>

                                            {{-- Handover Status Indicator --}}
                                            <template x-if="item.handover_status === 'Approved'">
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10.5px] font-semibold bg-[#DEF7EC] text-[#03543F]">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                    <span>Handover Approved</span>
                                                </span>
                                            </template>
                                            <template x-if="item.handover_status === 'Submitted'">
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10.5px] font-semibold bg-[#EFF6FF] text-[#1D4ED8]">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-[#1D4ED8] animate-pulse"></span>
                                                    <span>Review PMO</span>
                                                </span>
                                            </template>
                                            <template x-if="item.handover_status === 'Conditional'">
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10.5px] font-semibold bg-[#FEF3C7] text-[#92400E]" :title="item.handover_conditional_notes">
                                                    <svg class="w-3 h-3 text-[#D97706]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                                    <span>Conditional (2x24 Jam)</span>
                                                </span>
                                            </template>
                                        </div>
                                    </td>

                                    {{-- Attachment --}}
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        <template x-if="item.po_file">
                                            <a :href="item.po_file" target="_blank" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-[#EFF6FF] text-[#2563EB] text-[11.5px] font-semibold border border-[#DBEAFE] hover:bg-[#DBEAFE] transition">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                                </svg>
                                                <span>Lihat PO</span>
                                            </a>
                                        </template>
                                        <template x-if="!item.po_file">
                                            <span class="text-[12px] text-gray-400 font-medium">-</span>
                                        </template>
                                    </td>

                                    {{-- Aksi --}}
                                    <td class="py-3.5 px-4 sm:px-6 text-right whitespace-nowrap">
                                        <div class="inline-flex items-center justify-end gap-1.5">
                                            {{-- Handover Button --}}
                                            <template x-if="item.is_ready_handover && item.handover_status !== 'Approved'">
                                                <button @click="openHandoverModal(item)" 
                                                        :title="item.handover_status === 'Conditional' ? 'Revisi Serah Terima (2x24 Jam)' : 'Formulir Serah Terima Proyek'"
                                                        class="px-2.5 py-1 rounded-lg border text-[11.5px] font-bold flex items-center gap-1 transition cursor-pointer"
                                                        :class="item.handover_status === 'Conditional' ? 'bg-[#FEF3C7] text-[#92400E] border-[#FDE68A] hover:bg-[#FDE68A]' : 'bg-[#EFF6FF] text-[#1D4ED8] border-[#BFDBFE] hover:bg-[#DBEAFE]'">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                                    </svg>
                                                    <span x-text="item.handover_status === 'Conditional' ? 'Revisi Handover' : 'Serah Terima'"></span>
                                                </button>
                                            </template>

                                            {{-- Edit --}}
                                            <button @click="openEditModal(item)" title="Edit Peluang" class="w-8 h-8 rounded-lg border border-[#E7E5E3] hover:bg-[#F1F0EE] text-[#475569] flex items-center justify-center transition cursor-pointer">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                                </svg>
                                            </button>

                                            {{-- Hapus --}}
                                            <button @click="deleteItem(item)" title="Hapus Peluang" class="w-8 h-8 rounded-lg border border-[#E7E5E3] hover:bg-[#FEE2E2] text-[#EF4444] flex items-center justify-center transition cursor-pointer">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </template>

                            <template x-if="paginatedProjects.length === 0">
                                <tr>
                                    <td colspan="7" class="py-12 text-center text-[#75727C]">
                                        <div class="w-12 h-12 rounded-2xl bg-[#F8F7F6] text-[#A19DA8] flex items-center justify-center mx-auto mb-3">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </div>
                                        <div class="font-bold text-[#17151C]">Belum ada peluang / deal yang sesuai</div>
                                        <div class="text-[12.5px] mt-1 text-[#75727C]">Silakan ubah filter atau tambahkan peluang baru.</div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                {{-- Pagination Footer --}}
                <div class="p-4 sm:p-5 border-t border-[#E7E5E3] flex flex-col sm:flex-row sm:items-center justify-between gap-3.5 bg-[#FAF9F8]">
                    <div class="flex items-center gap-3 text-[12.5px] text-[#75727C]">
                        <span>Tampilkan:</span>
                        <select x-model="perPage" @change="currentPage = 1" class="px-2.5 py-1 rounded-lg border border-[#E7E5E3] text-[12.5px] font-medium text-[#17151C] bg-white outline-none">
                            <option value="10">10 data</option>
                            <option value="25">25 data</option>
                            <option value="50">50 data</option>
                            <option value="all">Semua</option>
                        </select>
                        <span>Menampilkan <strong class="text-[#17151C]" x-text="filteredProjects.length"></strong> total peluang</span>
                    </div>

                    <div class="flex items-center gap-2 self-end sm:self-auto" x-show="totalPages > 1">
                        <button @click="prevPage()" :disabled="currentPage === 1" class="px-3 py-1.5 rounded-lg border border-[#E7E5E3] text-[12.5px] font-semibold text-[#17151C] bg-white hover:bg-[#F1F0EE] disabled:opacity-40 disabled:cursor-not-allowed transition">
                            Sebelumnya
                        </button>
                        <span class="text-[12.5px] text-[#75727C] px-2">
                            Hal <strong class="text-[#17151C]" x-text="currentPage"></strong> dari <strong class="text-[#17151C]" x-text="totalPages"></strong>
                        </span>
                        <button @click="nextPage()" :disabled="currentPage === totalPages" class="px-3 py-1.5 rounded-lg border border-[#E7E5E3] text-[12.5px] font-semibold text-[#17151C] bg-white hover:bg-[#F1F0EE] disabled:opacity-40 disabled:cursor-not-allowed transition">
                            Selanjutnya
                        </button>
                    </div>
                </div>
            </div>

            {{-- MODAL CREATE / EDIT FORM --}}
            <template x-teleport="body">
                <div x-show="formModalOpen" 
                     x-cloak
                     class="fixed inset-0 bg-[#0E0D12]/60 z-50 flex items-center justify-center p-3 sm:p-5 overflow-y-auto"
                     @click.self="formModalOpen = false">
                    <div class="bg-white rounded-2xl w-[640px] max-w-full shadow-[0_20px_50px_rgba(14,13,18,0.2)] overflow-hidden my-8">
                        {{-- Modal Header --}}
                        <div class="p-5 sm:p-6 border-b border-[#E7E5E3] flex items-center justify-between bg-[#FAF9F8]">
                            <div>
                                <h3 class="text-[17px] font-bold text-[#17151C]" x-text="isEdit ? 'Edit Peluang & Kontrak Sales' : 'Tambah Peluang / Calon Proyek Baru'"></h3>
                                <p class="text-[12.5px] text-[#75727C] mt-0.5">Lengkapi informasi komersial sebelum diserahkan ke tim teknis.</p>
                            </div>
                            <button @click="formModalOpen = false" class="w-8 h-8 rounded-lg hover:bg-[#F1F0EE] text-[#75727C] flex items-center justify-center transition cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        {{-- Modal Body Form --}}
                        <form @submit.prevent="saveForm()" class="p-5 sm:p-6 space-y-4 max-h-[75vh] overflow-y-auto">
                            <div>
                                <label class="block text-[12.5px] font-bold text-[#17151C] mb-1">Nama Proyek / Pengadaan <span class="text-[#EF4444]">*</span></label>
                                <input type="text" x-model="formData.name" required placeholder="Contoh: Pengadaan Firewall & Switch Core Bank X" class="w-full px-3.5 py-2 rounded-xl border border-[#CBD5E1] text-[13.5px] outline-none focus:border-[#C81E2C]">
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                <div>
                                    <label class="block text-[12.5px] font-bold text-[#17151C] mb-1">Nama Klien / Instansi <span class="text-[#EF4444]">*</span></label>
                                    <input type="text" x-model="formData.client" required placeholder="Contoh: PT Bank Mandiri Tbk" class="w-full px-3.5 py-2 rounded-xl border border-[#CBD5E1] text-[13.5px] outline-none focus:border-[#C81E2C]">
                                </div>
                                <div>
                                    <label class="block text-[12.5px] font-bold text-[#17151C] mb-1">PIC Sales Penanggung Jawab <span class="text-[#EF4444]">*</span></label>
                                    <select x-model="formData.sales_name" required class="w-full px-3.5 py-2 rounded-xl border border-[#CBD5E1] text-[13.5px] bg-white outline-none focus:border-[#C81E2C]">
                                        <template x-for="s in salesList" :key="s">
                                            <option :value="s" x-text="s"></option>
                                        </template>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                <div>
                                    <label class="block text-[12.5px] font-bold text-[#17151C] mb-1">Nilai Kontrak / PO (Rp)</label>
                                    <input type="number" x-model="formData.contract_value" placeholder="Contoh: 150000000" class="w-full px-3.5 py-2 rounded-xl border border-[#CBD5E1] text-[13.5px] outline-none focus:border-[#C81E2C]">
                                </div>
                                <div>
                                    <label class="block text-[12.5px] font-bold text-[#17151C] mb-1">Nomor PO / SPK Resmi</label>
                                    <input type="text" x-model="formData.po_number" placeholder="Contoh: PO-IPNET-2026-009" class="w-full px-3.5 py-2 rounded-xl border border-[#CBD5E1] text-[13.5px] outline-none focus:border-[#C81E2C]">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                <div>
                                    <label class="block text-[12.5px] font-bold text-[#17151C] mb-1">Status Pipeline <span class="text-[#EF4444]">*</span></label>
                                    <select x-model="formData.acquire_status" class="w-full px-3.5 py-2 rounded-xl border border-[#CBD5E1] text-[13.5px] bg-white outline-none focus:border-[#C81E2C]">
                                        <option value="Prospek Awal">Prospek Awal</option>
                                        <option value="Kualifikasi Kebutuhan">Kualifikasi Kebutuhan</option>
                                        <option value="Penawaran Komersial">Penawaran Komersial</option>
                                        <option value="Deal / PO Terbit">Deal / PO Terbit</option>
                                        <option value="Handover to Design">Handover to Design</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[12.5px] font-bold text-[#17151C] mb-1">Divisi Pelaksana</label>
                                    <select x-model="formData.division_id" class="w-full px-3.5 py-2 rounded-xl border border-[#CBD5E1] text-[13.5px] bg-white outline-none focus:border-[#C81E2C]">
                                        <option value="">Lintas Divisi (General)</option>
                                        @foreach($divisions as $div)
                                            <option value="{{ $div->id }}">{{ $div->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                <div>
                                    <label class="block text-[12.5px] font-bold text-[#17151C] mb-1">Target Tanggal Mulai</label>
                                    <input type="date" x-model="formData.start_date" class="w-full px-3.5 py-2 rounded-xl border border-[#CBD5E1] text-[13.5px] outline-none focus:border-[#C81E2C]">
                                </div>
                                <div>
                                    <label class="block text-[12.5px] font-bold text-[#17151C] mb-1">Target Deadline Selesai</label>
                                    <input type="date" x-model="formData.deadline" class="w-full px-3.5 py-2 rounded-xl border border-[#CBD5E1] text-[13.5px] outline-none focus:border-[#C81E2C]">
                                </div>
                            </div>

                            <div>
                                <label class="block text-[12.5px] font-bold text-[#17151C] mb-1">Upload File PO / RFI / Dokumen Kebutuhan (PDF / Gambar / Doc)</label>
                                <input type="file" @change="handleFileUpload($event)" class="w-full px-3.5 py-2 rounded-xl border border-[#CBD5E1] text-[13px] bg-white outline-none file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:bg-[#EFF6FF] file:text-[#2563EB] file:font-semibold">
                            </div>

                            {{-- Modal Footer Buttons --}}
                            <div class="pt-4 border-t border-[#E7E5E3] flex items-center justify-between gap-2.5">
                                <template x-if="isEdit && activeProject?.stage === 'Acquire' && activeProject?.handover_status !== 'Approved'">
                                    <button type="button" 
                                            @click="formModalOpen = false; openHandoverModal(activeProject)" 
                                            class="px-3.5 py-2 bg-[#EFF6FF] hover:bg-[#DBEAFE] text-[#1D4ED8] border border-[#BFDBFE] rounded-xl text-[12.5px] font-bold transition flex items-center gap-1.5 cursor-pointer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                        </svg>
                                        <span>Buka Formulir Serah Terima &rarr;</span>
                                    </button>
                                </template>
                                <div class="flex items-center gap-2.5 ml-auto">
                                    <button type="button" @click="formModalOpen = false" class="px-4 py-2 bg-white border border-[#E7E5E3] rounded-xl text-[13px] font-semibold text-[#475569] hover:bg-[#F1F0EE] transition cursor-pointer">
                                        Batal
                                    </button>
                                    <button type="submit" class="px-5 py-2 bg-[#C81E2C] hover:bg-[#AF1424] text-white rounded-xl text-[13px] font-bold shadow-[0_4px_14px_rgba(200,30,44,0.25)] transition cursor-pointer">
                                        Simpan Peluang
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </template>

            {{-- MODAL FORMULIR SERAH TERIMA PROYEK (INTERNAL HANDOVER SOP - 4 KATEGORI) --}}
            <template x-teleport="body">
                <div x-show="handoverModalOpen" 
                     x-cloak
                     class="fixed inset-0 bg-[#0E0D12]/60 z-50 flex items-center justify-center p-3 sm:p-5 overflow-y-auto"
                     @click.self="handoverModalOpen = false">
                    <div class="bg-white rounded-2xl w-[720px] max-w-full shadow-[0_20px_50px_rgba(14,13,18,0.2)] overflow-hidden my-8">
                        {{-- Modal Header --}}
                        <div class="p-5 sm:p-6 border-b border-[#E7E5E3] bg-[#FAF9F8] flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-[#EFF6FF] text-[#2563EB] flex items-center justify-center font-bold">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-[17px] font-bold text-[#17151C]">Formulir Serah Terima Proyek (Internal Handover)</h3>
                                    <p class="text-[12px] text-[#75727C]">SOP Gatekeeper Commercial &rarr; Pre-Sales & Solution Architect &rarr; PMO / Delivery</p>
                                </div>
                            </div>
                            <button @click="handoverModalOpen = false" class="w-8 h-8 rounded-lg hover:bg-[#F1F0EE] text-[#75727C] flex items-center justify-center transition cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        {{-- Modal Body --}}
                        <form @submit.prevent="submitHandover()" class="p-5 sm:p-6 space-y-5 max-h-[75vh] overflow-y-auto">
                            
                            {{-- Info Banner --}}
                            <div class="p-4 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0] flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div>
                                    <div class="text-[11.5px] font-bold uppercase tracking-wider text-[#64748B]">Proyek yang Diserahkan</div>
                                    <div class="text-[15px] font-bold text-[#0F172A] mt-0.5" x-text="activeProject?.name"></div>
                                    <div class="text-[12.5px] text-[#475569]">Klien: <strong x-text="activeProject?.client"></strong> &bull; Nilai: <strong x-text="activeProject?.contract_formatted"></strong></div>
                                </div>
                                <div class="sm:text-right">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[12px] font-semibold bg-[#EFF6FF] text-[#1D4ED8]">
                                        PIC Sales: <span x-text="activeProject?.sales_name"></span>
                                    </span>
                                </div>
                            </div>

                            {{-- Conditional Alert if previously marked Conditional --}}
                            <template x-if="activeProject?.handover_status === 'Conditional'">
                                <div class="p-4 rounded-xl bg-[#FFFBEB] border border-[#FDE68A] text-[#92400E]">
                                    <div class="flex items-center gap-2 font-bold text-[13px]">
                                        <svg class="w-4 h-4 text-[#D97706]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                        <span>Catatan Perbaikan Handover dari PMO (Tenggat 2x24 Jam):</span>
                                    </div>
                                    <p class="text-[12.5px] mt-1 font-medium" x-text="activeProject?.handover_conditional_notes || 'Harap lengkapi dokumen dan konfirmasi ruang lingkup yang belum jelas.'"></p>
                                </div>
                            </template>

                            {{-- 4 KATEGORI CHECKLIST --}}
                            <div class="space-y-4">
                                <h4 class="text-[13.5px] font-bold text-[#17151C] flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-[#C81E2C]"></span>
                                    <span>Checklist Kelengkapan Berkas (4 Kategori Standar SOP)</span>
                                </h4>

                                {{-- Kategori 1: Legal & Komersial --}}
                                <div class="p-3.5 rounded-xl border border-[#E7E5E3] bg-[#FAF9F8]">
                                    <div class="text-[12.5px] font-bold text-[#17151C] mb-2 flex items-center justify-between">
                                        <span>I. Legal & Komersial</span>
                                        <span class="text-[11px] text-[#75727C] font-normal">Wajib disepakati</span>
                                    </div>
                                    <div class="space-y-2 text-[12.5px] text-[#334155]">
                                        <label class="flex items-center gap-2.5 cursor-pointer hover:text-[#0F172A]">
                                            <input type="checkbox" x-model="handoverForm.checklist.contract_signed" class="w-4 h-4 rounded text-[#C81E2C] focus:ring-[#C81E2C]">
                                            <span>Kontrak / SPK / Surat Penunjukan (SLA) telah ditandatangani</span>
                                        </label>
                                        <label class="flex items-center gap-2.5 cursor-pointer hover:text-[#0F172A]">
                                            <input type="checkbox" x-model="handoverForm.checklist.bom_proposal" class="w-4 h-4 rounded text-[#C81E2C] focus:ring-[#C81E2C]">
                                            <span>BoM / BoW Proposal Final telah disepakati kedua belah pihak</span>
                                        </label>
                                        <label class="flex items-center gap-2.5 cursor-pointer hover:text-[#0F172A]">
                                            <input type="checkbox" x-model="handoverForm.checklist.negotiation_addendum" class="w-4 h-4 rounded text-[#C81E2C] focus:ring-[#C81E2C]">
                                            <span>Notulen Negosiasi & Addendum Perubahan Lingkup (jika ada)</span>
                                        </label>
                                    </div>
                                </div>

                                {{-- Kategori 2: Kebutuhan & Lingkup (Scope) --}}
                                <div class="p-3.5 rounded-xl border border-[#E7E5E3] bg-[#FAF9F8]">
                                    <div class="text-[12.5px] font-bold text-[#17151C] mb-2 flex items-center justify-between">
                                        <span>II. Kebutuhan & Lingkup (Scope of Work)</span>
                                        <span class="text-[11px] text-[#2563EB] font-semibold">Validasi Gatekeeper</span>
                                    </div>
                                    <div class="space-y-2 text-[12.5px] text-[#334155]">
                                        <label class="flex items-center gap-2.5 cursor-pointer hover:text-[#0F172A]">
                                            <input type="checkbox" x-model="handoverForm.checklist.sow_document" class="w-4 h-4 rounded text-[#C81E2C] focus:ring-[#C81E2C]">
                                            <span>Dokumen Scope of Work (SoW) terperinci & batasan pekerjaan jelas</span>
                                        </label>
                                        <label class="flex items-center gap-2.5 cursor-pointer hover:text-[#0F172A]">
                                            <input type="checkbox" x-model="handoverForm.checklist.urs_requirement" class="w-4 h-4 rounded text-[#C81E2C] focus:ring-[#C81E2C]">
                                            <span>User Requirement Specification (URS) / Kebutuhan Klien lengkap</span>
                                        </label>
                                        <label class="flex items-center gap-2.5 cursor-pointer hover:text-[#0F172A]">
                                            <input type="checkbox" x-model="handoverForm.checklist.mockup_wireframe" class="w-4 h-4 rounded text-[#C81E2C] focus:ring-[#C81E2C]">
                                            <span>Topologi / Diagram Desain / Arsitektur Jaringan Awal terlampir</span>
                                        </label>
                                        <label class="flex items-center gap-2.5 cursor-pointer hover:text-[#0F172A]">
                                            <input type="checkbox" x-model="handoverForm.checklist.maintenance_contract" class="w-4 h-4 rounded text-[#C81E2C] focus:ring-[#C81E2C]">
                                            <span>Ketentuan Garansi & Kontrak Pemeliharaan Operasional terdefinisi</span>
                                        </label>
                                    </div>
                                </div>

                                {{-- Kategori 3: Administrasi & Finansial --}}
                                <div class="p-3.5 rounded-xl border border-[#E7E5E3] bg-[#FAF9F8]">
                                    <div class="text-[12.5px] font-bold text-[#17151C] mb-2 flex items-center justify-between">
                                        <span>III. Administrasi & Finansial</span>
                                        <span class="text-[11px] text-[#75727C] font-normal">Finance Alignment</span>
                                    </div>
                                    <div class="space-y-2 text-[12.5px] text-[#334155]">
                                        <label class="flex items-center gap-2.5 cursor-pointer hover:text-[#0F172A]">
                                            <input type="checkbox" x-model="handoverForm.checklist.dp_payment_proof" class="w-4 h-4 rounded text-[#C81E2C] focus:ring-[#C81E2C]">
                                            <span>Bukti Pembayaran DP / Konfirmasi Termin 1 dari Finance</span>
                                        </label>
                                        <label class="flex items-center gap-2.5 cursor-pointer hover:text-[#0F172A]">
                                            <input type="checkbox" x-model="handoverForm.checklist.billing_milestone" class="w-4 h-4 rounded text-[#C81E2C] focus:ring-[#C81E2C]">
                                            <span>Jadwal Penagihan (Billing Milestone) telah diselaraskan</span>
                                        </label>
                                    </div>
                                </div>

                                {{-- Kategori 4: Kontak PIC Klien --}}
                                <div class="p-3.5 rounded-xl border border-[#E7E5E3] bg-[#FAF9F8]">
                                    <div class="text-[12.5px] font-bold text-[#17151C] mb-2">
                                        IV. Kontak PIC & Akses Klien
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                        <div>
                                            <label class="block text-[11.5px] font-semibold text-[#475569] mb-1">PIC Teknis Klien</label>
                                            <input type="text" x-model="handoverForm.customer_pic_technical" placeholder="Nama, HP/Email" class="w-full px-3 py-1.5 rounded-lg border border-[#CBD5E1] text-[12px] outline-none focus:border-[#C81E2C]">
                                        </div>
                                        <div>
                                            <label class="block text-[11.5px] font-semibold text-[#475569] mb-1">PIC Bisnis / Owner</label>
                                            <input type="text" x-model="handoverForm.customer_pic_business" placeholder="Nama, HP/Email" class="w-full px-3 py-1.5 rounded-lg border border-[#CBD5E1] text-[12px] outline-none focus:border-[#C81E2C]">
                                        </div>
                                        <div>
                                            <label class="block text-[11.5px] font-semibold text-[#475569] mb-1">PIC Finance / Tagihan</label>
                                            <input type="text" x-model="handoverForm.customer_pic_finance" placeholder="Nama, HP/Email" class="w-full px-3 py-1.5 rounded-lg border border-[#CBD5E1] text-[12px] outline-none focus:border-[#C81E2C]">
                                        </div>
                                    </div>
                                </div>

                                {{-- Catatan Khusus Sales --}}
                                <div>
                                    <label class="block text-[12.5px] font-bold text-[#17151C] mb-1">
                                        Catatan Khusus Sales ke Tim PMO & Delivery (Komitmen Lisan / Batasan Lapangan)
                                    </label>
                                    <textarea x-model="handoverForm.special_notes" rows="3" placeholder="Tuliskan komitmen khusus klien, jam kerja akses gedung, atau catatan penting lainnya..." class="w-full px-3.5 py-2 rounded-xl border border-[#CBD5E1] text-[12.5px] outline-none focus:border-[#C81E2C]"></textarea>
                                </div>

                            </div>

                            {{-- Modal Footer --}}
                            <div class="pt-4 border-t border-[#E7E5E3] flex items-center justify-between">
                                <button type="button" @click="handoverModalOpen = false" class="px-4 py-2 bg-white border border-[#E7E5E3] rounded-xl text-[13px] font-semibold text-[#475569] hover:bg-[#F1F0EE] transition cursor-pointer">
                                    Batal
                                </button>
                                <button type="submit" class="px-5 py-2 bg-[#2563EB] hover:bg-[#1D4ED8] text-white rounded-xl text-[13px] font-bold shadow-[0_4px_14px_rgba(37,99,235,0.25)] transition cursor-pointer flex items-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    <span>Ajukan Serah Terima ke PMO & Tim Desain &rarr;</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </template>

        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('acquireApp', () => ({
            projects: @json($projects),
            salesList: @json($salesList),
            inNegotiationCount: {{ $inNegotiationCount }},
            dealPoCount: {{ $dealPoCount }},
            handoverDesignCount: {{ $handoverDesignCount }},

            search: '',
            statusFilter: 'all',
            salesFilter: 'all',
            perPage: '10',
            currentPage: 1,

            formModalOpen: false,
            handoverModalOpen: false,
            isEdit: false,
            activeProject: null,
            uploadedFile: null,

            formData: {
                id: null,
                name: '',
                client: '',
                sales_name: 'Ribka',
                contract_value: '',
                po_number: '',
                acquire_status: 'Deal / PO Terbit',
                division_id: '',
                start_date: '',
                deadline: '',
                description: '',
            },

            handoverForm: {
                customer_pic_technical: '',
                customer_pic_business: '',
                customer_pic_finance: '',
                special_notes: '',
                checklist: {
                    contract_signed: true,
                    bom_proposal: true,
                    negotiation_addendum: false,
                    sow_document: true,
                    urs_requirement: true,
                    mockup_wireframe: false,
                    maintenance_contract: false,
                    dp_payment_proof: true,
                    billing_milestone: true,
                }
            },

            get filteredProjects() {
                return this.projects.filter(p => {
                    const matchSearch = !this.search || 
                        p.name.toLowerCase().includes(this.search.toLowerCase()) ||
                        p.client.toLowerCase().includes(this.search.toLowerCase()) ||
                        p.sales_name.toLowerCase().includes(this.search.toLowerCase()) ||
                        (p.po_number && p.po_number.toLowerCase().includes(this.search.toLowerCase()));

                    const matchStatus = this.statusFilter === 'all' || p.acquire_status === this.statusFilter;
                    const matchSales = this.salesFilter === 'all' || p.sales_name === this.salesFilter;

                    return matchSearch && matchStatus && matchSales;
                });
            },

            get totalPages() {
                if (this.perPage === 'all') return 1;
                const count = parseInt(this.perPage) || 10;
                return Math.ceil(this.filteredProjects.length / count) || 1;
            },

            get paginatedProjects() {
                if (this.perPage === 'all') return this.filteredProjects;
                const count = parseInt(this.perPage) || 10;
                const start = (this.currentPage - 1) * count;
                return this.filteredProjects.slice(start, start + count);
            },

            goToPage(p) { this.currentPage = p; },
            prevPage() { if (this.currentPage > 1) this.currentPage--; },
            nextPage() { if (this.currentPage < this.totalPages) this.currentPage++; },

            openCreateModal() {
                this.isEdit = false;
                this.uploadedFile = null;
                this.formData = {
                    id: null,
                    name: '',
                    client: '',
                    sales_name: this.salesList[0] || 'Ribka',
                    contract_value: '',
                    po_number: '',
                    acquire_status: 'Deal / PO Terbit',
                    division_id: '',
                    start_date: new Date().toISOString().split('T')[0],
                    deadline: new Date(Date.now() + 30*24*60*60*1000).toISOString().split('T')[0],
                    description: '',
                };
                this.formModalOpen = true;
            },

            openEditModal(item) {
                this.isEdit = true;
                this.activeProject = item;
                this.uploadedFile = null;
                this.formData = {
                    id: item.id,
                    name: item.name,
                    client: item.client,
                    sales_name: item.sales_name,
                    contract_value: item.contract_value || '',
                    po_number: item.po_number === '-' ? '' : item.po_number,
                    acquire_status: item.acquire_status,
                    division_id: item.division_id || '',
                    start_date: item.start_date || '',
                    deadline: item.deadline || '',
                    description: item.description || '',
                };
                this.formModalOpen = true;
            },

            handleFileUpload(e) {
                if (e.target.files.length > 0) {
                    this.uploadedFile = e.target.files[0];
                }
            },

            async saveForm() {
                const formDataPayload = new FormData();
                formDataPayload.append('name', this.formData.name);
                formDataPayload.append('client', this.formData.client);
                formDataPayload.append('sales_name', this.formData.sales_name);
                if (this.formData.contract_value) formDataPayload.append('contract_value', this.formData.contract_value);
                if (this.formData.po_number) formDataPayload.append('po_number', this.formData.po_number);
                formDataPayload.append('acquire_status', this.formData.acquire_status);
                if (this.formData.division_id) formDataPayload.append('division_id', this.formData.division_id);
                if (this.formData.start_date) formDataPayload.append('start_date', this.formData.start_date);
                if (this.formData.deadline) formDataPayload.append('deadline', this.formData.deadline);
                if (this.formData.description) formDataPayload.append('description', this.formData.description);
                if (this.uploadedFile) formDataPayload.append('po_file', this.uploadedFile);

                let url = '/acquire';
                if (this.isEdit) {
                    url = `/acquire/${this.formData.id}`;
                    formDataPayload.append('_method', 'PUT');
                }

                try {
                    const res = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                        },
                        body: formDataPayload
                    });

                    const data = await res.json();
                    if (data.success) {
                        this.formModalOpen = false;
                        window.location.reload();
                    } else {
                        alert(data.message || 'Terjadi kesalahan');
                    }
                } catch (err) {
                    console.error(err);
                    alert('Gagal menyimpan data peluang.');
                }
            },

            openHandoverModal(item) {
                this.activeProject = item;
                const existingData = item.handover_data || {};
                this.handoverForm = {
                    customer_pic_technical: item.customer_pic_technical || '',
                    customer_pic_business: item.customer_pic_business || '',
                    customer_pic_finance: item.customer_pic_finance || '',
                    special_notes: item.special_notes || '',
                    checklist: {
                        contract_signed: existingData.contract_signed !== undefined ? existingData.contract_signed : true,
                        bom_proposal: existingData.bom_proposal !== undefined ? existingData.bom_proposal : true,
                        negotiation_addendum: existingData.negotiation_addendum !== undefined ? existingData.negotiation_addendum : false,
                        sow_document: existingData.sow_document !== undefined ? existingData.sow_document : true,
                        urs_requirement: existingData.urs_requirement !== undefined ? existingData.urs_requirement : true,
                        mockup_wireframe: existingData.mockup_wireframe !== undefined ? existingData.mockup_wireframe : false,
                        maintenance_contract: existingData.maintenance_contract !== undefined ? existingData.maintenance_contract : false,
                        dp_payment_proof: existingData.dp_payment_proof !== undefined ? existingData.dp_payment_proof : true,
                        billing_milestone: existingData.billing_milestone !== undefined ? existingData.billing_milestone : true,
                    }
                };
                this.handoverModalOpen = true;
            },

            async submitHandover() {
                if (!this.activeProject) return;

                try {
                    const res = await fetch(`/acquire/${this.activeProject.id}/handover-design`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            customer_pic_technical: this.handoverForm.customer_pic_technical,
                            customer_pic_business: this.handoverForm.customer_pic_business,
                            customer_pic_finance: this.handoverForm.customer_pic_finance,
                            special_notes: this.handoverForm.special_notes,
                            handover_checklist: this.handoverForm.checklist,
                        })
                    });

                    const data = await res.json();
                    if (data.success) {
                        this.handoverModalOpen = false;
                        window.location.reload();
                    } else {
                        alert(data.message || 'Gagal melakukan Handover.');
                    }
                } catch (err) {
                    console.error(err);
                    alert('Gagal menghubungi server.');
                }
            },

            async deleteItem(item) {
                if (!confirm(`Hapus data peluang "${item.name}"?`)) return;

                try {
                    const res = await fetch(`/acquire/${item.id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                        }
                    });

                    const data = await res.json();
                    if (data.success) {
                        this.projects = this.projects.filter(p => p.id !== item.id);
                    } else {
                        alert('Gagal menghapus peluang.');
                    }
                } catch (err) {
                    console.error(err);
                    alert('Gagal menghapus data.');
                }
            }
        }));
    });
</script>
@endpush
@endsection
