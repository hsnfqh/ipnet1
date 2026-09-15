@extends('layouts.app')

@section('title', 'Proposal & SOW - Presales Portal')

@php
    $user = auth()->user();
    $canManageProposal = $user && ($user->hasRole('Presales') || $user->hasAnyRole(['Solution Architect', 'Solutions Architect', 'SA', 'PMO', 'Project Manager', 'Direktur', 'HD / Direktur', 'Group Leader', 'Lead Engineer'])) && !$user->hasAnyRole(['Sales', 'BDM']);
@endphp

@section('content')
<div class="flex h-screen overflow-hidden">
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto bg-[#FAF9F8]">
        @include('components.topbar', ['title' => 'Proposal & SOW'])
        
        <div class="p-4 sm:p-5 lg:p-[26px] animate-fade-in space-y-6" x-data="proposalManager()">
            
            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between text-sm shadow-sm">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 flex items-center justify-between text-sm shadow-sm">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm shadow-sm space-y-1">
                    <div class="font-bold flex items-center gap-2">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span>Gagal Mengunggah Berkas:</span>
                    </div>
                    <ul class="list-disc list-inside text-xs text-red-700 pl-6">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif


            {{-- Search and Filter Bar --}}
            <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4">
                <form method="GET" action="{{ route('presales.proposals.index') }}" class="flex-1 flex flex-col sm:flex-row items-center gap-3">
                    <input type="hidden" name="tab" value="{{ $tab }}">
                    <div class="relative w-full sm:max-w-xs">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}"
                               placeholder="Cari proyek, instansi/klien, sales..." 
                               class="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-all">
                    </div>

                    <select name="division_id" onchange="this.form.submit()" 
                            class="w-full sm:w-auto px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-semibold text-gray-700 focus:outline-none focus:ring-2 focus:ring-red-500/20">
                        <option value="">Semua Divisi</option>
                        @foreach($divisions as $div)
                            <option value="{{ $div->id }}" {{ request('division_id') == $div->id ? 'selected' : '' }}>{{ $div->name }}</option>
                        @endforeach
                    </select>
                </form>

                <div class="text-xs text-gray-400 font-medium whitespace-nowrap">
                    Total: <span class="text-gray-700 font-bold">{{ $projects->total() }}</span> Proyek
                </div>
            </div>

            {{-- Tabs Navigation --}}
            <div class="flex items-center gap-6 border-b border-gray-200 px-2 text-sm font-bold overflow-x-auto">
                <a href="{{ route('presales.proposals.index', ['tab' => 'all', 'search' => request('search'), 'division_id' => request('division_id')]) }}" 
                   class="pb-3 border-b-2 whitespace-nowrap transition-all flex items-center gap-1.5 {{ $tab === 'all' ? 'border-[#C81E2C] text-[#C81E2C]' : 'border-transparent text-gray-500 hover:text-gray-900' }}">
                    Semua Dokumen <span class="text-xs font-semibold text-gray-400">({{ $counts['all'] }})</span>
                </a>
                <a href="{{ route('presales.proposals.index', ['tab' => 'pending', 'search' => request('search'), 'division_id' => request('division_id')]) }}" 
                   class="pb-3 border-b-2 whitespace-nowrap transition-all flex items-center gap-1.5 {{ $tab === 'pending' ? 'border-[#C81E2C] text-[#C81E2C]' : 'border-transparent text-gray-500 hover:text-gray-900' }}">
                    Menunggu SOW <span class="text-xs font-semibold text-gray-400">({{ $counts['pending'] }})</span>
                </a>
                <a href="{{ route('presales.proposals.index', ['tab' => 'submitted', 'search' => request('search'), 'division_id' => request('division_id')]) }}" 
                   class="pb-3 border-b-2 whitespace-nowrap transition-all flex items-center gap-1.5 {{ $tab === 'submitted' ? 'border-[#C81E2C] text-[#C81E2C]' : 'border-transparent text-gray-500 hover:text-gray-900' }}">
                    Proposal Selesai <span class="text-xs font-semibold text-gray-400">({{ $counts['submitted'] }})</span>
                </a>
                <a href="{{ route('presales.proposals.index', ['tab' => 'won', 'search' => request('search'), 'division_id' => request('division_id')]) }}" 
                   class="pb-3 border-b-2 whitespace-nowrap transition-all flex items-center gap-1.5 {{ $tab === 'won' ? 'border-[#C81E2C] text-[#C81E2C]' : 'border-transparent text-gray-500 hover:text-gray-900' }}">
                    Tender Menang <span class="text-xs font-semibold text-gray-400">({{ $counts['won'] }})</span>
                </a>
                <a href="{{ route('presales.proposals.index', ['tab' => 'lost', 'search' => request('search'), 'division_id' => request('division_id')]) }}" 
                   class="pb-3 border-b-2 whitespace-nowrap transition-all flex items-center gap-1.5 {{ $tab === 'lost' ? 'border-[#C81E2C] text-[#C81E2C]' : 'border-transparent text-gray-500 hover:text-gray-900' }}">
                    Gagal / Batal <span class="text-xs font-semibold text-gray-400">({{ $counts['lost'] }})</span>
                </a>
            </div>

            {{-- Table --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                @if($projects->isEmpty())
                    <div class="py-20 text-center space-y-3">
                        <div class="w-12 h-12 rounded-2xl bg-gray-50 border border-gray-200 flex items-center justify-center text-gray-400 mx-auto">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <div class="text-sm font-bold text-gray-700">
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
                        <p class="text-xs text-gray-400 max-w-sm mx-auto">
                            @if($tab === 'pending')
                                Seluruh proyek aktif telah memiliki proposal teknis atau belum memerlukan kajian SOW.
                            @elseif($tab === 'submitted')
                                Proposal teknis yang telah selesai diunggah oleh tim Presales akan tampil di sini.
                            @elseif($tab === 'lost')
                                Proyek atau tender yang dinyatakan Closed Lost atau Drop akan tercatat di sini.
                            @else
                                Silakan sesuaikan kata kunci pencarian atau filter divisi di atas.
                            @endif
                        </p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-gray-100 bg-gray-50/50 text-[11px] font-bold tracking-wider text-gray-500 uppercase">
                                    <th class="py-4 px-6">PROYEK & KLIEN</th>
                                    <th class="py-4 px-6">SALES PIC</th>
                                    <th class="py-4 px-6">ESTIMASI NILAI</th>
                                    <th class="py-4 px-6">BEBAN KERJA (MANDAYS)</th>
                                    <th class="py-4 px-6">DOKUMEN PROPOSAL</th>
                                    <th class="py-4 px-6 text-right">AKSI</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-sm">
                                @foreach($projects as $p)
                                    <tr class="hover:bg-gray-50/60 transition-colors">
                                        <td class="py-4 px-6">
                                            <div class="font-bold text-gray-900">{{ $p->name }}</div>
                                            <div class="text-xs text-gray-500 font-medium flex items-center gap-1.5 mt-0.5 flex-wrap">
                                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                                <span>{{ $p->client }}</span>
                                                @if($p->sales_stage === 'Closed Lost' || in_array($p->status, ['Cancelled', 'Rejected', 'Closed Lost', 'Lost', 'Drop']))
                                                    <span class="inline-flex px-2 py-0.5 text-[10.5px] font-semibold rounded-md bg-rose-50 text-rose-700 border border-rose-200">
                                                        {{ $p->lost_reason ? 'Alasan: ' . $p->lost_reason : ($p->lost_competitor ? 'Kalah vs ' . $p->lost_competitor : 'Status: Gagal / Drop') }}
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="py-4 px-6 whitespace-nowrap">
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-red-50 text-[#AF1424] text-xs font-semibold">
                                                {{ $p->sales_name ?: 'Sales PIC' }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-6 font-semibold text-gray-800 text-xs whitespace-nowrap">
                                            {{ $p->contract_value > 0 ? 'Rp ' . number_format($p->contract_value / 1000000, 1, ',', '.') . ' Jt' : '—' }}
                                        </td>
                                        <td class="py-4 px-6 whitespace-nowrap text-xs">
                                            @if($p->mandays)
                                                <span class="font-bold text-gray-900">{{ $p->mandays }} Mandays</span>
                                            @else
                                                <span class="text-gray-400">Belum diisi</span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-6 whitespace-nowrap text-xs">
                                            @if($p->proposal_file)
                                                <div class="inline-flex items-center gap-2">
                                                    <a href="{{ route('presales.proposals.download', $p->id) }}" 
                                                       class="inline-flex items-center gap-1.5 text-xs font-bold text-[#AF1424] hover:underline">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                        Unduh Proposal
                                                    </a>
                                                    @if($canManageProposal)
                                                        <button type="button" 
                                                                @click="openDeleteModal({{ json_encode($p) }})"
                                                                title="Hapus / Reset Berkas Proposal" 
                                                                class="p-1 rounded-md text-gray-400 hover:text-red-600 hover:bg-red-50 transition-colors">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                            </svg>
                                                        </button>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-gray-400">Belum diunggah</span>
                                            @endif
                                        </td>
                                        <td class="py-4 px-6 text-right whitespace-nowrap">
                                            @if($canManageProposal)
                                                <button @click="openUploadModal({{ json_encode($p) }})" 
                                                        class="px-3.5 py-1.5 bg-[#C81E2C] hover:brightness-105 active:translate-y-[1px] text-white text-xs font-semibold rounded-lg shadow-[0_4px_12px_rgba(200,30,44,0.24)] transition-all inline-flex items-center gap-1.5">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                                    </svg>
                                                    {{ $p->proposal_file ? 'Perbarui Proposal' : 'Unggah Proposal' }}
                                                </button>
                                            @else
                                                @if($p->proposal_file)
                                                    <button @click="openViewModal({{ json_encode($p) }})" 
                                                            class="px-3.5 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 text-xs font-bold rounded-lg shadow-xs transition-all inline-flex items-center gap-1.5">
                                                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                        Lihat SOW & Berkas
                                                    </button>
                                                @else
                                                    <span class="inline-flex px-2.5 py-1 text-xs font-semibold text-amber-700 bg-amber-50 rounded-lg border border-amber-200">
                                                        Menunggu Presales
                                                    </span>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($projects->hasPages())
                        <div class="p-4 border-t border-gray-100">
                            {{ $projects->links() }}
                        </div>
                    @endif
                @endif
            </div>

            @if($canManageProposal)
                {{-- MODAL UPLOAD / UPDATE PROPOSAL TEKNIS & SOW (PRESALES ONLY) --}}
                <template x-teleport="body">
                    <div x-show="isUploadModalOpen" 
                         x-cloak 
                         class="fixed inset-0 z-50 bg-[#0E0D12]/60 flex items-center justify-center p-4">
                        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-[0_20px_50px_rgba(14,13,18,0.25)] space-y-4" @click.away="isUploadModalOpen = false">
                            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                                <div>
                                    <span class="text-[10.5px] font-bold uppercase tracking-wider text-[#AF1424]">Presales Engineering Portal</span>
                                    <h3 class="text-lg font-bold text-gray-900" x-text="activeTender.name || 'Dokumen Proposal Teknis'"></h3>
                                </div>
                                <button @click="isUploadModalOpen = false" class="text-gray-400 hover:text-gray-600">
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
                                        <span class="font-bold text-[#AF1424]" x-text="activeTender.sales_name || 'Tim Sales'"></span>
                                    </div>
                                </div>

                                {{-- Notice if file already exists with quick delete action --}}
                                <template x-if="activeTender.proposal_file">
                                    <div class="p-3 bg-red-50/70 rounded-xl border border-red-200/80 flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-[#AF1424] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            <div>
                                                <div class="font-bold text-gray-900 text-[11px]">Berkas Proposal Sudah Terunggah</div>
                                                <div class="text-[10.5px] text-gray-500">Pilih berkas baru di bawah jika ingin memperbarui.</div>
                                            </div>
                                        </div>
                                        <button type="button" 
                                                @click="isUploadModalOpen = false; openDeleteModal(activeTender)" 
                                                class="px-2.5 py-1 text-[11px] font-bold text-red-700 bg-white hover:bg-red-50 border border-red-200 rounded-lg shadow-xs transition">
                                            Hapus Berkas
                                        </button>
                                    </div>
                                </template>

                                <div>
                                    <label class="block font-bold text-gray-700 uppercase mb-1">Ruang Lingkup Teknis / SOW *</label>
                                    <textarea name="proposal_notes" x-model="form.proposal_notes" rows="3" required placeholder="Tulis ringkasan arsitektur solusi, spesifikasi perangkat, dan ruang lingkup teknis..." 
                                              class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500"></textarea>
                                </div>

                                <div>
                                    <label class="block font-bold text-gray-700 uppercase mb-1">Estimasi Beban Kerja (Mandays Engineer) *</label>
                                    <input type="number" name="mandays" x-model="form.mandays" min="1" required placeholder="Contoh: 10" 
                                           class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                                </div>

                                <div>
                                    <label class="block font-bold text-gray-700 uppercase mb-1">
                                        <span x-text="activeTender.proposal_file ? 'Perbarui Berkas Proposal Teknis (Opsional)' : 'Unggah Berkas Proposal Teknis & SOW (PDF, DOCX, ZIP)'"></span>
                                    </label>
                                    <input type="file" name="proposal_file" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip,.rar,.7z,.png,.jpg,.jpeg,.txt"
                                           class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-[#AF1424] file:text-white hover:file:bg-[#83101D] cursor-pointer">
                                    <p class="text-[10.5px] text-gray-400 mt-1">Format didukung: PDF, Word, Excel, PPT, ZIP/RAR. Maksimal 50MB.</p>
                                </div>

                                <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100">
                                    <button type="button" @click="isUploadModalOpen = false" class="px-4 py-2 text-sm font-semibold text-gray-600 hover:bg-gray-100 rounded-lg transition">Batal</button>
                                    <button type="submit" class="px-5 py-2.5 text-[13px] font-semibold text-white bg-[#C81E2C] hover:brightness-105 active:translate-y-[1px] rounded-lg shadow-[0_8px_20px_rgba(200,30,44,0.24)] transition-all">
                                        Simpan & Kirim ke Sales
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </template>

                {{-- MODAL KONFIRMASI HAPUS BERKAS PROPOSAL (PRESALES ONLY) --}}
                <template x-teleport="body">
                    <div x-show="isDeleteModalOpen" 
                         x-cloak 
                         class="fixed inset-0 z-50 bg-[#0E0D12]/60 flex items-center justify-center p-4">
                        <div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-2xl space-y-4" @click.away="isDeleteModalOpen = false">
                            <div class="w-12 h-12 rounded-2xl bg-red-50 border border-red-100 text-[#AF1424] flex items-center justify-center mx-auto">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </div>
                            <div class="text-center space-y-1">
                                <h3 class="text-base font-bold text-gray-900">Hapus Berkas Proposal?</h3>
                                <p class="text-xs text-gray-500 leading-relaxed">
                                    Berkas proposal untuk proyek <span class="font-bold text-gray-800" x-text="deletingTender.name"></span> akan dihapus dari sistem. Tim Sales tidak akan dapat mengunduh berkas ini sampai berkas baru diunggah.
                                </p>
                            </div>
                            <form :action="'/presales/proposals/' + deletingTender.id + '/file'" method="POST" class="flex items-center gap-2 pt-2">
                                @csrf
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="button" @click="isDeleteModalOpen = false" class="w-1/2 py-2.5 text-xs font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition">
                                    Batal
                                </button>
                                <button type="submit" class="w-1/2 py-2.5 text-xs font-semibold text-white bg-[#C81E2C] hover:brightness-105 active:translate-y-[1px] rounded-lg shadow-sm transition">
                                    Ya, Hapus Berkas
                                </button>
                            </form>
                        </div>
                    </div>
                </template>
            @endif

            {{-- MODAL PREVIEW DETAIL SOW & PROPOSAL (UNTUK SALES / READ-ONLY) --}}
            <template x-teleport="body">
                <div x-show="isViewModalOpen" 
                     x-cloak 
                     class="fixed inset-0 z-50 bg-[#0E0D12]/60 flex items-center justify-center p-4">
                    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4" @click.away="isViewModalOpen = false">
                        <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-lg bg-red-50 border border-red-100 text-[#AF1424] flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </div>
                                <div>
                                    <span class="text-[10.5px] font-bold uppercase tracking-wider text-[#AF1424]">Presales Engineering Solution</span>
                                    <h3 class="text-base font-bold text-gray-900" x-text="viewingTender.name"></h3>
                                </div>
                            </div>
                            <button @click="isViewModalOpen = false" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <div class="space-y-3 text-xs">
                            <div class="p-3 bg-gray-50 rounded-xl border border-gray-200/70 space-y-1.5">
                                <div class="flex justify-between">
                                    <span class="text-gray-400">Klien / Instansi:</span>
                                    <span class="font-bold text-gray-800" x-text="viewingTender.client || '—'"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-400">Estimasi Beban Kerja:</span>
                                    <span class="font-bold text-gray-900" x-text="(viewingTender.mandays || 10) + ' Mandays Engineer'"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-400">Tim Solusi:</span>
                                    <span class="font-bold text-[#AF1424]" x-text="viewingTender.presales_name || 'Tim Presales & SA'"></span>
                                </div>
                            </div>

                            <div>
                                <label class="block font-bold text-gray-700 uppercase mb-1">Ruang Lingkup Teknis / SOW:</label>
                                <div class="p-3 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-700 whitespace-pre-line leading-relaxed max-h-48 overflow-y-auto" 
                                     x-text="viewingTender.proposal_notes || viewingTender.description || 'Tidak ada catatan teknis khusus.'"></div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100">
                            <button type="button" @click="isViewModalOpen = false" class="px-4 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-100 rounded-xl transition">
                                Tutup
                            </button>
                            <template x-if="viewingTender.proposal_file">
                                <a :href="'/presales/proposals/' + viewingTender.id + '/download'" 
                                   class="px-5 py-2 text-xs font-bold text-white bg-[#AF1424] hover:bg-[#83101D] rounded-xl shadow-sm transition inline-flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    Unduh Dokumen Proposal
                                </a>
                            </template>
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
            }
        }
    }
</script>
@endsection
