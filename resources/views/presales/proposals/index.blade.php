@extends('layouts.app')

@section('title', 'Dokumen Desain & SOW - PT IP Network Solusindo')

@php
    $user = auth()->user();
    $canManageProposal = $user && ($user->hasRole('Presales') || $user->hasAnyRole(['Solution Architect', 'Solutions Architect', 'SA', 'PMO', 'Project Manager', 'Direktur', 'HD / Direktur', 'Group Leader', 'Lead Engineer'])) && !$user->hasAnyRole(['Sales', 'BDM']);
@endphp

@push('styles')
<style>
    /* ========================================================
       IPNET Brand Design System
       ======================================================== */
    :root {
        --ipnet-primary: #8F0A0D;
        --ipnet-primary-hover: #73080A;
        --ipnet-card-bg: #FFFFFF;
        --ipnet-card-border: #E2E8F0;
        --ipnet-text-main: #1E293B;
    }

    .ipnet-card {
        background-color: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03), 0 4px 12px rgba(0, 0, 0, 0.015);
        transition: all 0.2s ease;
    }

    .btn-ipnet-primary {
        background-color: #8F0A0D;
        color: #FFFFFF;
        transition: all 0.2s ease;
    }

    .btn-ipnet-primary:hover {
        background-color: #73080A;
        box-shadow: 0 4px 12px rgba(143, 10, 13, 0.25);
    }
</style>
@endpush

@section('content')
<div class="flex h-screen overflow-hidden bg-[#F8FAFC] font-sans">
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Dokumen Desain & SOW'])
        
        <div class="p-4 sm:p-6 lg:p-7 space-y-5 max-w-[1600px] mx-auto animate-fade-in" x-data="proposalManager()">
            
            {{-- Header Title Bar --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-1">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-4.5 bg-[#8F0A0D] rounded-full inline-block"></span>
                        <h1 class="text-[20px] font-bold text-[#1E293B] tracking-tight">Manajemen Desain Arsitektur &amp; SOW</h1>
                    </div>
                    <p class="text-[12.5px] text-[#64748B] mt-0.5 ml-4">
                        Kelola ruang lingkup teknis (SOW), estimasi mandays perancangan, dan berkas HLD/LLD proposal.
                    </p>
                </div>
            </div>

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between text-xs font-semibold shadow-xs">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="p-3.5 rounded-xl bg-red-50 border border-red-200 text-red-800 flex items-center justify-between text-xs font-semibold shadow-xs">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="p-3.5 rounded-xl bg-red-50 border border-red-200 text-red-800 text-xs shadow-xs space-y-1">
                    <div class="font-bold flex items-center gap-2">
                        <svg class="w-4 h-4 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span>Gagal Mengunggah Berkas:</span>
                    </div>
                    <ul class="list-disc list-inside text-red-700 pl-6 space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Search & Filter Container --}}
            <div class="ipnet-card p-4 sm:p-5">
                <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3">
                    <form method="GET" action="{{ route('presales.proposals.index') }}" class="flex-1 flex flex-col sm:flex-row items-center gap-2.5">
                        <div class="relative w-full sm:max-w-md">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#94A3B8]">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                            <input type="text" 
                                   name="search" 
                                   value="{{ request('search') }}"
                                   placeholder="Cari nama proyek, klien, sales PIC..." 
                                   class="w-full pl-9 pr-4 py-2 bg-[#F8FAFC] hover:bg-white border border-[#CBD5E1] rounded-xl text-xs font-medium text-[#1E293B] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D] transition-all">
                        </div>

                        <select name="division_id" onchange="this.form.submit()" 
                                class="w-full sm:w-auto px-3 py-2 bg-[#F8FAFC] hover:bg-white border border-[#CBD5E1] rounded-xl text-xs font-semibold text-[#1E293B] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D] cursor-pointer transition-all">
                            <option value="">Semua Divisi</option>
                            @foreach($divisions as $div)
                                <option value="{{ $div->id }}" {{ request('division_id') == $div->id ? 'selected' : '' }}>{{ $div->name }}</option>
                            @endforeach
                        </select>
                    </form>

                    <div class="text-[12px] text-[#64748B] font-medium whitespace-nowrap self-end md:self-auto">
                        Total: <span class="text-[#1E293B] font-extrabold">{{ $projects->total() }}</span> Proyek
                    </div>
                </div>
            </div>

            {{-- Table Container --}}
            <div class="ipnet-card overflow-hidden">
                @if($projects->isEmpty())
                    <div class="py-16 text-center space-y-2.5">
                        <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center text-gray-400 mx-auto">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <div class="text-[13px] font-bold text-[#1E293B]">
                            @if($tab === 'pending')
                                Tidak ada proyek yang menunggu dokumen SOW saat ini
                            @elseif($tab === 'submitted')
                                Belum ada berkas proposal teknis yang selesai diunggah
                            @elseif($tab === 'won')
                                Belum ada proyek atau tender berstatus menang pada filter ini
                            @elseif($tab === 'lost')
                                Tidak ada data proyek yang berstatus gagal atau dibatalkan
                            @else
                                Tidak ada dokumen proposal yang ditemukan
                            @endif
                        </div>
                        <p class="text-xs text-[#64748B] max-w-sm mx-auto">
                            Silakan sesuaikan kata kunci pencarian atau filter divisi di atas.
                        </p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="border-b border-[#E2E8F0] bg-[#F8FAFC] text-[10.5px] font-bold tracking-wider text-[#64748B] uppercase">
                                    <th class="py-3 px-4 sm:px-5">Proyek &amp; Klien</th>
                                    <th class="py-3 px-3.5">Sales PIC</th>
                                    <th class="py-3 px-3.5 text-right">Estimasi Nilai</th>
                                    <th class="py-3 px-3.5 text-center">Beban Kerja</th>
                                    <th class="py-3 px-3.5 text-center">Status Berkas</th>
                                    <th class="py-3 px-4 sm:px-5 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#F1F5F9] font-medium text-[#1E293B]">
                                @foreach($projects as $p)
                                    <tr class="hover:bg-[#F8FAFC] transition-colors">
                                        {{-- Proyek & Klien --}}
                                        <td class="py-3 px-4 sm:px-5">
                                            <div @click="openViewModal({{ json_encode($p) }})" 
                                                 class="font-semibold text-[#1E293B] hover:text-[#8F0A0D] text-[12.5px] leading-snug line-clamp-1 cursor-pointer transition flex items-center gap-1.5" 
                                                 title="Klik untuk melihat detail & spesifikasi teknis: {{ $p->name }}">
                                                <span>{{ $p->name }}</span>
                                                <svg class="w-3.5 h-3.5 text-[#94A3B8] hover:text-[#8F0A0D] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                            </div>
                                            <div class="text-[11px] text-[#64748B] flex items-center gap-1.5 mt-0.5 flex-wrap">
                                                <span class="w-1.5 h-1.5 rounded-full bg-[#8F0A0D]"></span>
                                                <span class="font-medium text-[#475569]">{{ $p->client ?: 'Prospek Umum' }}</span>
                                                @if($p->division)
                                                    <span class="text-gray-300">•</span>
                                                    <span class="text-[10.5px] text-[#64748B]">{{ $p->division->name }}</span>
                                                @endif
                                                @if($p->sales_stage === 'Closed Lost' || in_array($p->status, ['Cancelled', 'Rejected', 'Closed Lost', 'Lost', 'Drop']))
                                                    <span class="inline-flex px-1.5 py-0.2 text-[10px] font-semibold rounded bg-rose-50 text-rose-700 border border-rose-200">
                                                        {{ $p->lost_reason ? 'Alasan: ' . $p->lost_reason : 'Drop / Lost' }}
                                                    </span>
                                                @endif
                                            </div>
                                        </td>

                                        {{-- Sales PIC --}}
                                        <td class="py-3 px-3.5 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg bg-[#F1F5F9] text-[#1E293B] text-[11px] font-semibold border border-[#E2E8F0]">
                                                {{ $p->sales_name ?: 'Sales Team' }}
                                            </span>
                                        </td>

                                        {{-- Estimasi Nilai --}}
                                        <td class="py-3 px-3.5 text-right font-extrabold text-[#1E293B] text-[12px] whitespace-nowrap">
                                            {{ $p->contract_value > 0 ? 'Rp ' . number_format($p->contract_value / 1000000, 1, ',', '.') . ' Jt' : '—' }}
                                        </td>

                                        {{-- Beban Kerja (Mandays) --}}
                                        <td class="py-3 px-3.5 text-center whitespace-nowrap">
                                            @if($p->mandays)
                                                <span class="inline-flex items-center gap-1 text-[11.5px] font-bold text-[#1E293B]">
                                                    {{ $p->mandays }} <span class="text-[10px] font-normal text-[#64748B]">MD</span>
                                                </span>
                                            @else
                                                <span class="text-[11px] text-[#94A3B8]">Belum diisi</span>
                                            @endif
                                        </td>

                                        {{-- Dokumen Proposal Status --}}
                                        <td class="py-3 px-3.5 text-center whitespace-nowrap">
                                            @if($p->proposal_file)
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.8 text-[11px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-lg">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                    HLD/SOW Selesai
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.8 text-[11px] font-semibold text-amber-700 bg-amber-50 border border-amber-200 rounded-lg">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                    Perlu BoQ
                                                </span>
                                            @endif
                                        </td>

                                        {{-- Aksi --}}
                                        <td class="py-3 px-4 sm:px-5 text-right whitespace-nowrap">
                                            <div class="flex items-center justify-end gap-1.5">
                                                {{-- Tombol Detail / Spesifikasi --}}
                                                <button type="button" 
                                                        @click="openViewModal({{ json_encode($p) }})" 
                                                        title="Lihat Spesifikasi & Detail Kebutuhan Proyek"
                                                        class="p-1.5 rounded-lg text-[#64748B] hover:text-[#1E293B] hover:bg-[#F1F5F9] border border-[#E2E8F0] hover:border-[#CBD5E1] transition cursor-pointer"
                                                        aria-label="Lihat Detail">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                    </svg>
                                                </button>

                                                @if($p->proposal_file)
                                                    {{-- Unduh Berkas --}}
                                                    <a href="{{ route('presales.proposals.download', $p->id) }}" 
                                                       title="Unduh Berkas SOW / Proposal Teknis"
                                                       class="p-1.5 rounded-lg text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 transition inline-flex items-center justify-center cursor-pointer">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                                        </svg>
                                                    </a>
                                                @endif

                                                @if($canManageProposal)
                                                    @if($p->proposal_file)
                                                        {{-- Edit SOW (Sudah Ada Berkas) --}}
                                                        <button @click="openUploadModal({{ json_encode($p) }})" 
                                                                title="Perbarui SOW & Berkas Proposal"
                                                                class="px-2.5 py-1.5 rounded-lg text-[#8F0A0D] bg-red-50 hover:bg-red-100/80 border border-red-200/80 text-[11px] font-semibold transition inline-flex items-center gap-1 cursor-pointer">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                            </svg>
                                                            <span>Edit SOW</span>
                                                        </button>
                                                    @else
                                                        {{-- Unggah SOW (Belum Ada Berkas) --}}
                                                        <button @click="openUploadModal({{ json_encode($p) }})" 
                                                                title="Unggah SOW & Berkas Proposal"
                                                                class="px-2.5 py-1.5 rounded-lg text-white btn-ipnet-primary text-[11px] font-semibold transition inline-flex items-center gap-1 cursor-pointer shadow-2xs">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                                            </svg>
                                                            <span>Unggah SOW</span>
                                                        </button>
                                                    @endif
                                                    
                                                    @if($p->proposal_file)
                                                        {{-- Hapus Berkas --}}
                                                        <button type="button" 
                                                                @click="openDeleteModal({{ json_encode($p) }})"
                                                                title="Hapus / Reset Berkas Proposal" 
                                                                class="p-1.5 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 border border-transparent hover:border-red-100 transition-colors cursor-pointer">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                            </svg>
                                                        </button>
                                                    @endif
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($projects->hasPages())
                        <div class="p-3.5 border-t border-[#E2E8F0] bg-[#F8FAFC]">
                            {{ $projects->links() }}
                        </div>
                    @endif
                @endif
            </div>

            {{-- Modals for Upload / Delete / View --}}
            @if($canManageProposal)
                {{-- MODAL UPLOAD / UPDATE PROPOSAL TEKNIS & SOW --}}
                <template x-teleport="body">
                    <div x-show="isUploadModalOpen" 
                         x-cloak 
                         class="fixed inset-0 z-50 bg-[#0E0D12]/60 flex items-center justify-center p-4">
                        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4" @click.away="isUploadModalOpen = false">
                            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                                <div>
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-[#8F0A0D]">Presales &amp; Solution Architect</span>
                                    <h3 class="text-base font-bold text-[#1E293B]" x-text="activeTender.name || 'Dokumen Proposal Teknis'"></h3>
                                </div>
                                <button @click="isUploadModalOpen = false" class="text-gray-400 hover:text-gray-600 cursor-pointer">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>

                            <form :action="'/presales/proposals/' + activeTender.id" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs">
                                @csrf

                                <div class="p-3 bg-gray-50 rounded-xl border border-gray-200/70 space-y-1">
                                    <div class="flex justify-between">
                                        <span class="text-gray-400">Instansi / Klien:</span>
                                        <span class="font-bold text-gray-800" x-text="activeTender.client || '—'"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-400">Sales PIC:</span>
                                        <span class="font-bold text-[#8F0A0D]" x-text="activeTender.sales_name || 'Tim Sales'"></span>
                                    </div>
                                </div>

                                {{-- Notice if file already exists --}}
                                <template x-if="activeTender.proposal_file">
                                    <div class="p-3 bg-red-50/70 rounded-xl border border-red-200/80 flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-[#8F0A0D] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            <div>
                                                <div class="font-bold text-gray-900 text-[11px]">Berkas Proposal Sudah Terunggah</div>
                                                <div class="text-[10.5px] text-gray-500">Pilih berkas baru di bawah jika ingin memperbarui.</div>
                                            </div>
                                        </div>
                                        <button type="button" 
                                                @click="isUploadModalOpen = false; openDeleteModal(activeTender)" 
                                                class="px-2.5 py-1 text-[11px] font-bold text-red-700 bg-white hover:bg-red-50 border border-red-200 rounded-lg shadow-xs transition cursor-pointer">
                                            Hapus Berkas
                                        </button>
                                    </div>
                                </template>

                                <div>
                                    <label class="block font-bold text-gray-700 uppercase mb-1">Ruang Lingkup Teknis / SOW *</label>
                                    <textarea name="proposal_notes" x-model="form.proposal_notes" rows="3" required placeholder="Tulis ringkasan arsitektur solusi, spesifikasi perangkat, dan ruang lingkup teknis..." 
                                              class="w-full px-3 py-2 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D]"></textarea>
                                </div>

                                <div>
                                    <label class="block font-bold text-gray-700 uppercase mb-1">Estimasi Beban Kerja (Mandays Engineer) *</label>
                                    <input type="number" name="mandays" x-model="form.mandays" min="1" required placeholder="Contoh: 10" 
                                           class="w-full px-3 py-2 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D]">
                                </div>

                                <div>
                                    <label class="block font-bold text-gray-700 uppercase mb-1">
                                        <span x-text="activeTender.proposal_file ? 'Perbarui Berkas Proposal Teknis (Opsional)' : 'Unggah Berkas Proposal Teknis & SOW (PDF, DOCX, ZIP)'"></span>
                                    </label>
                                    <input type="file" name="proposal_file" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip,.rar,.7z,.png,.jpg,.jpeg,.txt"
                                           class="w-full px-3 py-2 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-xs file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-[#8F0A0D] file:text-white hover:file:bg-[#73080A] cursor-pointer">
                                    <p class="text-[10.5px] text-gray-400 mt-1">Format didukung: PDF, Word, Excel, PPT, ZIP/RAR. Maksimal 50MB.</p>
                                </div>

                                <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100">
                                    <button type="button" @click="isUploadModalOpen = false" class="px-4 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-100 rounded-lg transition cursor-pointer">Batal</button>
                                    <button type="submit" class="px-4 py-2 text-xs font-bold text-white btn-ipnet-primary rounded-xl shadow-xs transition cursor-pointer">
                                        Simpan &amp; Rilis SOW
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </template>

                {{-- MODAL KONFIRMASI HAPUS BERKAS PROPOSAL --}}
                <template x-teleport="body">
                    <div x-show="isDeleteModalOpen" 
                         x-cloak 
                         class="fixed inset-0 z-50 bg-[#0E0D12]/60 flex items-center justify-center p-4">
                        <div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-2xl space-y-4" @click.away="isDeleteModalOpen = false">
                            <div class="w-10 h-10 rounded-xl bg-red-50 border border-red-100 text-[#8F0A0D] flex items-center justify-center mx-auto">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </div>
                            <div class="text-center space-y-1">
                                <h3 class="text-sm font-bold text-gray-900">Hapus Berkas Proposal?</h3>
                                <p class="text-xs text-gray-500 leading-relaxed">
                                    Berkas proposal untuk proyek <span class="font-bold text-gray-800" x-text="deletingTender.name"></span> akan dihapus dari sistem.
                                </p>
                            </div>
                            <form :action="'/presales/proposals/' + deletingTender.id + '/file'" method="POST" class="flex items-center gap-2 pt-2">
                                @csrf
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="button" @click="isDeleteModalOpen = false" class="w-1/2 py-2 text-xs font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition cursor-pointer">
                                    Batal
                                </button>
                                <button type="submit" class="w-1/2 py-2 text-xs font-semibold text-white bg-[#8F0A0D] hover:bg-[#73080A] rounded-lg shadow-xs transition cursor-pointer">
                                    Ya, Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </template>
            @endif

            {{-- MODAL PREVIEW DETAIL & SPESIFIKASI PROYEK LENGKAP --}}
            <template x-teleport="body">
                <div x-show="isViewModalOpen" 
                     x-cloak 
                     class="fixed inset-0 z-50 bg-[#0E0D12]/60 flex items-center justify-center p-4 overflow-y-auto">
                    <div class="bg-white rounded-2xl max-w-2xl w-full p-6 shadow-2xl space-y-5 my-8 max-h-[90vh] flex flex-col" @click.away="isViewModalOpen = false">
                        
                        {{-- Modal Header --}}
                        <div class="flex items-start justify-between border-b border-gray-100 pb-3 flex-shrink-0">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="text-[10.5px] font-bold uppercase tracking-wider text-[#8F0A0D] bg-red-50 border border-red-100 px-2 py-0.5 rounded-md">
                                        Spesifikasi &amp; Detail Kebutuhan Proyek
                                    </span>
                                    <template x-if="viewingTender.proposal_file">
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            HLD/SOW Selesai
                                        </span>
                                    </template>
                                    <template x-if="!viewingTender.proposal_file">
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-amber-50 text-amber-700 border border-amber-200">
                                            Perlu BoQ
                                        </span>
                                    </template>
                                </div>
                                <h3 class="text-base font-bold text-[#1E293B] mt-1" x-text="viewingTender.name"></h3>
                                <p class="text-xs text-[#64748B]" x-text="(viewingTender.client || 'Klien') + ' • ' + (viewingTender.division ? viewingTender.division.name : 'Semua Divisi')"></p>
                            </div>
                            <button @click="isViewModalOpen = false" class="text-gray-400 hover:text-gray-600 cursor-pointer p-1 rounded-lg hover:bg-gray-100 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        {{-- Modal Content (Scrollable) --}}
                        <div class="space-y-4 text-xs overflow-y-auto pr-1 flex-1">
                            
                            {{-- Grid Parameter & Info Kunci --}}
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                                <div class="p-2.5 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0]">
                                    <span class="text-[10px] uppercase font-bold text-[#94A3B8]">Klien / Instansi</span>
                                    <div class="font-bold text-[#1E293B] mt-0.5 truncate" x-text="viewingTender.client || '—'"></div>
                                </div>
                                <div class="p-2.5 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0]">
                                    <span class="text-[10px] uppercase font-bold text-[#94A3B8]">Sales PIC</span>
                                    <div class="font-bold text-[#8F0A0D] mt-0.5 truncate" x-text="viewingTender.sales_name || 'Tim Sales'"></div>
                                </div>
                                <div class="p-2.5 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0]">
                                    <span class="text-[10px] uppercase font-bold text-[#94A3B8]">Estimasi Nilai</span>
                                    <div class="font-bold text-[#1E293B] mt-0.5 truncate" x-text="formatCurrency(viewingTender.contract_value)"></div>
                                </div>
                                <div class="p-2.5 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0]">
                                    <span class="text-[10px] uppercase font-bold text-[#94A3B8]">Beban Mandays</span>
                                    <div class="font-bold text-[#1E293B] mt-0.5" x-text="(viewingTender.mandays || 10) + ' MD'"></div>
                                </div>
                            </div>

                            {{-- Grid Info Tambahan --}}
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 text-[11.5px]">
                                <div class="p-2.5 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0]">
                                    <span class="text-[10px] uppercase font-bold text-[#94A3B8]">Lokasi Implementasi</span>
                                    <div class="font-medium text-[#334155] mt-0.5" x-text="viewingTender.location || '—'"></div>
                                </div>
                                <div class="p-2.5 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0]">
                                    <span class="text-[10px] uppercase font-bold text-[#94A3B8]">PIC Teknis Klien</span>
                                    <div class="font-medium text-[#334155] mt-0.5" x-text="viewingTender.customer_pic_technical || '—'"></div>
                                </div>
                                <div class="p-2.5 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0]">
                                    <span class="text-[10px] uppercase font-bold text-[#94A3B8]">Target Waktu / Deadline</span>
                                    <div class="font-medium text-[#334155] mt-0.5" x-text="formatDate(viewingTender.deadline || viewingTender.expected_closing_date)"></div>
                                </div>
                            </div>

                            {{-- Kebutuhan & Spesifikasi Proyek (Deskripsi dari Sales / Klien) --}}
                            <div>
                                <label class="block font-bold text-[#1E293B] uppercase tracking-wider text-[11px] mb-1.5 flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-[#8F0A0D]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <span>Kebutuhan &amp; Spesifikasi Teknis Proyek (Permintaan Klien):</span>
                                </label>
                                <div class="p-3 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-xs text-[#334155] whitespace-pre-line leading-relaxed max-h-48 overflow-y-auto" 
                                     x-text="viewingTender.initial_requirement || viewingTender.business_need_summary || viewingTender.description || 'Tidak ada deskripsi spesifikasi khusus dari sales.'"></div>
                            </div>

                            {{-- Ruang Lingkup Teknis / SOW (Catatan Solution Architect) --}}
                            <div>
                                <label class="block font-bold text-[#1E293B] uppercase tracking-wider text-[11px] mb-1.5 flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                    <span>Ruang Lingkup Teknis / SOW (Catatan Arsitektur Solusi):</span>
                                </label>
                                <div class="p-3 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-xs text-[#334155] whitespace-pre-line leading-relaxed max-h-48 overflow-y-auto" 
                                     x-text="viewingTender.proposal_notes || 'Belum ada catatan SOW khusus yang dirilis.'"></div>
                            </div>

                            {{-- Catatan Khusus & Komitmen SLA jika ada --}}
                            <template x-if="viewingTender.sla_commitment || viewingTender.special_notes">
                                <div>
                                    <label class="block font-bold text-[#1E293B] uppercase tracking-wider text-[11px] mb-1.5 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <span>Komitmen SLA &amp; Catatan Khusus:</span>
                                    </label>
                                    <div class="p-3 bg-indigo-50/50 border border-indigo-100 rounded-xl text-xs text-indigo-900 leading-relaxed" 
                                         x-text="(viewingTender.sla_commitment ? 'SLA: ' + viewingTender.sla_commitment + '\n' : '') + (viewingTender.special_notes || '')"></div>
                                </div>
                            </template>
                        </div>

                        {{-- Modal Footer Actions --}}
                        <div class="flex items-center justify-between gap-2 pt-3 border-t border-gray-100 flex-shrink-0">
                            <button type="button" @click="isViewModalOpen = false" class="px-4 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-100 rounded-xl transition cursor-pointer">
                                Tutup
                            </button>
                            <div class="flex items-center gap-2">
                                <template x-if="viewingTender.proposal_file">
                                    <a :href="'/presales/proposals/' + viewingTender.id + '/download'" 
                                       class="px-4 py-2 text-xs font-bold text-white btn-ipnet-primary rounded-xl shadow-xs transition inline-flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                        Unduh Dokumen SOW
                                    </a>
                                </template>
                                @if($canManageProposal)
                                    <button type="button" 
                                            @click="openUploadFromView()" 
                                            class="px-4 py-2 text-xs font-bold text-white bg-[#8F0A0D] hover:bg-[#73080A] rounded-xl shadow-xs transition inline-flex items-center gap-1.5 cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        <span x-text="viewingTender.proposal_file ? 'Edit SOW' : 'Unggah SOW'"></span>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </template>

        </div>
    </div>
</div>

<script>
    function proposalManager() {
        return {
            isUploadModalOpen: false,
            isDeleteModalOpen: false,
            isViewModalOpen: false,
            activeTender: {},
            deletingTender: {},
            viewingTender: {},
            form: {
                proposal_notes: '',
                mandays: 10,
            },

            formatCurrency(val) {
                if (!val || val <= 0) return '—';
                return 'Rp ' + (val / 1000000).toLocaleString('id-ID', { minimumFractionDigits: 1, maximumFractionDigits: 1 }) + ' Jt';
            },

            formatDate(dateStr) {
                if (!dateStr) return '—';
                const d = new Date(dateStr);
                if (isNaN(d.getTime())) return dateStr;
                const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
                return String(d.getUTCDate()).padStart(2,'0') + ' ' + months[d.getUTCMonth()] + ' ' + d.getUTCFullYear();
            },

            openUploadModal(tender) {
                this.activeTender = tender;
                this.form.proposal_notes = tender.proposal_notes || tender.description || '';
                this.form.mandays = tender.mandays || 10;
                this.isUploadModalOpen = true;
            },

            openDeleteModal(tender) {
                this.deletingTender = tender;
                this.isDeleteModalOpen = true;
            },

            openViewModal(tender) {
                this.viewingTender = tender;
                this.isViewModalOpen = true;
            },

            openUploadFromView() {
                const t = this.viewingTender;
                this.isViewModalOpen = false;
                this.openUploadModal(t);
            }
        }
    }
</script>
@endsection
