@extends('layouts.app')

@section('title', 'Project Handover & Digital Archive - Admin Support')

@section('content')
<div class="flex h-screen overflow-hidden" x-data="{ isCreateModalOpen: false }" x-cloak>
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto bg-[#FAF9F8]">
        @include('components.topbar', ['title' => 'Project Document Handover & Digital Archive Repository'])
        
        <div class="p-4 sm:p-5 lg:p-[26px] animate-fade-in space-y-5 max-w-[1600px] mx-auto">
            
            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between shadow-sm">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 font-bold px-2">✕</button>
                </div>
            @endif

            {{-- Filter & Action Bar --}}
            <div class="flex flex-col sm:flex-row sm:flex-wrap sm:justify-between sm:items-center gap-2.5">
                <form method="GET" action="{{ route('admin_support.handovers.index') }}" class="flex flex-col sm:flex-row flex-wrap gap-2.5 w-full sm:w-auto">
                    <div class="relative">
                        <svg class="w-3.5 h-3.5 absolute left-3 top-3 text-[#948F99]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}"
                               placeholder="Cari No Handover / Proyek..." 
                               class="w-full sm:w-64 px-[11px] py-[9px] pl-8 rounded-lg border border-[#E7E5E3] text-[13px] text-[#17151C] outline-none bg-white focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all">
                    </div>

                    @if(request('search'))
                        <a href="{{ route('admin_support.handovers.index') }}" 
                           class="px-3 py-2 text-[13px] font-semibold text-gray-500 hover:text-gray-700 self-center">
                            Reset
                        </a>
                    @endif
                </form>

                <button type="button" @click="isCreateModalOpen = true" 
                        class="w-full sm:w-auto justify-center bg-[#C81E2C] text-white shadow-[0_8px_20px_rgba(200,30,44,0.24)] px-[17px] py-[9px] rounded-lg font-semibold text-[13px] flex items-center gap-1.5 hover:brightness-105 active:translate-y-[1px] transition-all cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Catat Berita Acara Handover</span>
                </button>
            </div>

            {{-- Table Handover --}}
            <div class="bg-white rounded-xl border border-[#E7E5E3] shadow-[0_1px_2px_rgba(14,13,18,0.05)] overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[1050px] border-collapse text-[13px]">
                        <thead>
                            <tr class="bg-[#F1F0EE] border-b border-[#E7E5E3]">
                                <th class="text-left py-3.5 px-4 text-[11px] font-semibold text-[#75727C] uppercase tracking-[0.3px] w-[18%]">No. Handover & Tgl</th>
                                <th class="text-left py-3.5 px-4 text-[11px] font-semibold text-[#75727C] uppercase tracking-[0.3px] w-[26%]">Proyek & Target Serah Terima</th>
                                <th class="text-center py-3.5 px-3 text-[11px] font-semibold text-[#75727C] uppercase tracking-[0.3px] w-[14%] whitespace-nowrap">Kelengkapan Berkas</th>
                                <th class="text-center py-3.5 px-3 text-[11px] font-semibold text-[#75727C] uppercase tracking-[0.3px] w-[14%] whitespace-nowrap">Status Audit Admin</th>
                                <th class="text-left py-3.5 px-4 text-[11px] font-semibold text-[#75727C] uppercase tracking-[0.3px] w-[18%]">Lokasi Box Arsip</th>
                                <th class="text-left py-3.5 px-4 text-[11px] font-semibold text-[#75727C] uppercase tracking-[0.3px] w-[10%]">Auditor</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#EFEDEB]">
                            @forelse($handovers as $hnd)
                                <tr class="hover:bg-[#F8F7F6] transition-colors">
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-[#17151C] text-[13px] leading-snug">{{ $hnd->handover_number }}</div>
                                        <div class="text-[11px] text-gray-500 mt-0.5 font-medium">
                                            {{ $hnd->handover_date ? $hnd->handover_date->format('d M Y') : '-' }}
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-[#17151C] text-[13px]">{{ $hnd->project ? $hnd->project->name : 'N/A' }}</div>
                                        <div class="text-[11.5px] text-gray-600 mt-0.5">
                                            Tujuan: <strong class="text-[#C81E2C]">{{ $hnd->target_division }}</strong>
                                        </div>
                                        @if($hnd->audit_notes)
                                            <div class="text-[11px] text-gray-400 mt-0.5 truncate max-w-sm">{{ $hnd->audit_notes }}</div>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-3 text-center whitespace-nowrap font-extrabold text-[13.5px] text-emerald-600">
                                        {{ $hnd->completeness_score }}%
                                    </td>
                                    <td class="py-3.5 px-3 text-center whitespace-nowrap">
                                        @php $hBadge = $hnd->status_badge; @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10.5px] font-bold border {{ $hBadge['bg'] }} {{ $hBadge['text'] }} {{ $hBadge['border'] }}">
                                            {{ $hBadge['label'] }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <div class="text-xs font-semibold text-gray-800">{{ $hnd->archive_box_code ?: 'Belum Masuk Box' }}</div>
                                    </td>
                                    <td class="py-3.5 px-4 text-xs text-gray-600">
                                        {{ $hnd->auditor ? $hnd->auditor->name : 'Admin' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-12 text-center text-gray-400 text-xs">
                                        Belum ada catatan serah terima berkas proyek.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($handovers->hasPages())
                    <div class="p-4 border-t border-[#EFEDEB]">
                        {{ $handovers->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>

    {{-- MODAL: Catat Handover Baru --}}
    <div x-show="isCreateModalOpen" class="fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 space-y-4 border border-[#E7E5E3] shadow-xl animate-fade-in-up" @click.outside="isCreateModalOpen = false">
            <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                <h3 class="text-base font-bold text-gray-900">Catat Serah Terima Berkas Proyek (Handover)</h3>
                <button @click="isCreateModalOpen = false" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>

            <form action="{{ route('admin_support.handovers.store') }}" method="POST" class="space-y-3.5 text-xs">
                @csrf
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Pilih Proyek *</label>
                    <select name="project_id" required class="wms-input font-bold">
                        @foreach($projects as $p)
                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Tujuan Serah Terima *</label>
                        <select name="target_division" required class="wms-input font-bold">
                            <option value="Client">Client (Klien Eksternal)</option>
                            <option value="Managed Service">Managed Service (Operate)</option>
                            <option value="Finance">Finance (Invoicing & Penagihan)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Tanggal Handover *</label>
                        <input type="date" name="handover_date" required class="wms-input" value="{{ date('Y-m-d') }}">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Kelengkapan Berkas (%)</label>
                        <input type="number" name="completeness_score" min="0" max="100" value="95" class="wms-input font-bold">
                    </div>
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Kode Box Arsip Fisik</label>
                        <input type="text" name="archive_box_code" class="wms-input" placeholder="e.g. BOX-2026-MND-01">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Catatan Audit Kelengkapan</label>
                    <textarea name="audit_notes" rows="3" class="wms-input" placeholder="Rincian berkas yang diserahterimakan (BAST, UAT, ABD, Dokumen Garansi)..."></textarea>
                </div>

                <div class="pt-3 border-t border-gray-100 flex justify-end gap-2">
                    <button type="button" @click="isCreateModalOpen = false" class="px-4 py-2 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 cursor-pointer">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-[#C81E2C] text-white font-bold rounded-xl hover:brightness-105 shadow-sm cursor-pointer">Simpan Handover</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
