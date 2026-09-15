@extends('layouts.app')

@section('title', 'Central Document Register & Vault - Admin Support')

@section('content')
<div class="flex h-screen overflow-hidden" x-data="adminDocsHandler()" x-cloak>
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto bg-[#FAF9F8]">
        @include('components.topbar', ['title' => 'Central Document Register & Vault'])
        
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
                <form method="GET" action="{{ route('admin_support.documents.index') }}" class="flex flex-col sm:flex-row flex-wrap gap-2.5 w-full sm:w-auto">
                    <div class="relative">
                        <svg class="w-3.5 h-3.5 absolute left-3 top-3 text-[#948F99]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}"
                               placeholder="Cari No Dokumen / Judul / Klien..." 
                               class="w-full sm:w-64 px-[11px] py-[9px] pl-8 rounded-lg border border-[#E7E5E3] text-[13px] text-[#17151C] outline-none bg-white focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all">
                    </div>

                    <select name="type" onchange="this.form.submit()" 
                            class="w-full sm:w-44 px-[11px] py-[9px] rounded-lg border border-[#E7E5E3] text-[13px] text-[#17151C] outline-none bg-white focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all cursor-pointer">
                        <option value="">Semua Tipe Dokumen</option>
                        <option value="SPK" {{ request('type') == 'SPK' ? 'selected' : '' }}>SPK Klien</option>
                        <option value="PO Client" {{ request('type') == 'PO Client' ? 'selected' : '' }}>PO Klien</option>
                        <option value="PO Vendor" {{ request('type') == 'PO Vendor' ? 'selected' : '' }}>PO Vendor</option>
                        <option value="Contract" {{ request('type') == 'Contract' ? 'selected' : '' }}>Kontrak / PKS</option>
                        <option value="WO" {{ request('type') == 'WO' ? 'selected' : '' }}>Work Order (WO)</option>
                        <option value="BAST" {{ request('type') == 'BAST' ? 'selected' : '' }}>BAST Final</option>
                        <option value="Tender" {{ request('type') == 'Tender' ? 'selected' : '' }}>Tender / Bid</option>
                        <option value="Proposal" {{ request('type') == 'Proposal' ? 'selected' : '' }}>Proposal Final</option>
                    </select>

                    <select name="status" onchange="this.form.submit()" 
                            class="w-full sm:w-44 px-[11px] py-[9px] rounded-lg border border-[#E7E5E3] text-[13px] text-[#17151C] outline-none bg-white focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all cursor-pointer">
                        <option value="">Semua Status Verifikasi</option>
                        <option value="Verified / Complete" {{ request('status') == 'Verified / Complete' ? 'selected' : '' }}>Verified (Lengkap)</option>
                        <option value="Under Review" {{ request('status') == 'Under Review' ? 'selected' : '' }}>Under Review</option>
                        <option value="Clarification Requested" {{ request('status') == 'Clarification Requested' ? 'selected' : '' }}>Minta Revisi / Klarifikasi</option>
                        <option value="Archived" {{ request('status') == 'Archived' ? 'selected' : '' }}>Archived (Arsip)</option>
                    </select>

                    @if(request('search') || request('type') || request('status'))
                        <a href="{{ route('admin_support.documents.index') }}" 
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
                    <span>Daftarkan Dokumen Baru</span>
                </button>
            </div>

            {{-- Table Document Register --}}
            <div class="bg-white rounded-xl border border-[#E7E5E3] shadow-[0_1px_2px_rgba(14,13,18,0.05)] overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[1100px] border-collapse text-[13px]">
                        <thead>
                            <tr class="bg-[#F1F0EE] border-b border-[#E7E5E3]">
                                <th class="text-left py-3.5 px-4 text-[11px] font-semibold text-[#75727C] uppercase tracking-[0.3px] w-[18%]">No. Dokumen & Tipe</th>
                                <th class="text-left py-3.5 px-4 text-[11px] font-semibold text-[#75727C] uppercase tracking-[0.3px] w-[28%]">Judul Dokumen & Klien</th>
                                <th class="text-right py-3.5 px-4 text-[11px] font-semibold text-[#75727C] uppercase tracking-[0.3px] w-[13%] whitespace-nowrap">Nilai Kontrak</th>
                                <th class="text-center py-3.5 px-3 text-[11px] font-semibold text-[#75727C] uppercase tracking-[0.3px] w-[12%] whitespace-nowrap">Masa Berlaku</th>
                                <th class="text-center py-3.5 px-3 text-[11px] font-semibold text-[#75727C] uppercase tracking-[0.3px] w-[14%] whitespace-nowrap">Status Verifikasi</th>
                                <th class="text-right py-3.5 px-4 text-[11px] font-semibold text-[#75727C] uppercase tracking-[0.3px] w-[15%] whitespace-nowrap">Aksi Verifikasi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#EFEDEB]">
                            @forelse($documents as $doc)
                                <tr class="hover:bg-[#F8F7F6] transition-colors">
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-[#17151C] text-[13px] leading-snug">{{ $doc->doc_number }}</div>
                                        <div class="flex items-center gap-1.5 mt-1 flex-wrap">
                                            @php $tBadge = $doc->type_badge; @endphp
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold border {{ $tBadge['bg'] }}">
                                                {{ $tBadge['name'] }}
                                            </span>
                                            <span class="text-[11px] text-gray-400">{{ $doc->version }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-[#17151C] text-[13px]">{{ $doc->title }}</div>
                                        <div class="text-[11.5px] text-[#75727C] mt-0.5 flex items-center gap-2 flex-wrap">
                                            <span>{{ $doc->client_name ?: ($doc->vendor_name ?: ($doc->project ? $doc->project->name : '-')) }}</span>
                                            @if($doc->physical_archive_location)
                                                <span>•</span>
                                                <span class="text-gray-400">Arsip: {{ $doc->physical_archive_location }}</span>
                                            @endif
                                        </div>
                                        @if($doc->rejection_notes)
                                            <div class="mt-1 text-[11px] text-rose-700 bg-rose-50 p-1.5 rounded border border-rose-100">
                                                <strong>Catatan Revisi:</strong> {{ $doc->rejection_notes }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-right whitespace-nowrap font-bold text-[#17151C]">
                                        {{ $doc->value > 0 ? 'Rp ' . number_format($doc->value, 0, ',', '.') : '-' }}
                                    </td>
                                    <td class="py-3.5 px-3 text-center whitespace-nowrap text-[12px] text-gray-600">
                                        {{ $doc->effective_date ? $doc->effective_date->format('d M Y') : '-' }}
                                        @if($doc->expiry_date)
                                            <div class="text-[10.5px] text-gray-400 mt-0.5">s/d {{ $doc->expiry_date->format('d M Y') }}</div>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-3 text-center whitespace-nowrap">
                                        @php $stBadge = $doc->status_badge; @endphp
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold border {{ $stBadge['bg'] }} {{ $stBadge['text'] }} {{ $stBadge['border'] }}">
                                            {{ $stBadge['label'] }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                        <div class="inline-flex items-center gap-1.5">
                                            <button @click="openInspectModal({{ json_encode($doc) }})" 
                                                    class="px-3 py-1.5 bg-[#C81E2C] hover:brightness-105 active:translate-y-[1px] text-white rounded-lg text-xs font-bold shadow-xs transition inline-flex items-center gap-1 cursor-pointer" title="Periksa Berkas & Verifikasi">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                <span>{{ $doc->status == 'Verified / Complete' ? 'Lihat Berkas' : 'Periksa & Verifikasi' }}</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-12 text-center text-gray-400 text-xs">
                                        Belum ada dokumen yang terdaftar sesuai kriteria filter.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($documents->hasPages())
                    <div class="p-4 border-t border-[#EFEDEB]">
                        {{ $documents->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>

    {{-- MODAL: Comprehensive Document Inspection & Verification --}}
    <div x-show="isInspectModalOpen" class="fixed inset-0 z-50 overflow-y-auto bg-black/60 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-2xl w-full p-6 space-y-4 border border-[#E7E5E3] shadow-2xl animate-fade-in-up" @click.outside="isInspectModalOpen = false">
            
            {{-- Modal Header --}}
            <div class="flex items-start justify-between pb-3 border-b border-gray-100">
                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-gray-500 uppercase">Inspeksi & Verifikasi Berkas</span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200" x-text="activeDoc ? activeDoc.status : ''"></span>
                    </div>
                    <h3 class="text-base font-bold text-gray-900" x-text="activeDoc ? activeDoc.doc_number : ''"></h3>
                </div>
                <button @click="isInspectModalOpen = false" class="text-gray-400 hover:text-gray-600 text-lg">&times;</button>
            </div>

            <template x-if="activeDoc">
                <div class="space-y-4 text-xs">
                    
                    {{-- Document Details Summary --}}
                    <div class="p-3.5 rounded-xl bg-gray-50 border border-gray-200 space-y-2">
                        <div class="font-bold text-gray-900 text-sm" x-text="activeDoc.title"></div>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5 text-gray-600 pt-1">
                            <div>
                                <span class="text-gray-400 block text-[11px]">Tipe & Kategori:</span>
                                <strong class="text-gray-800" x-text="(activeDoc.doc_type || '-') + ' (' + (activeDoc.category || 'General') + ')'"></strong>
                            </div>
                            <div>
                                <span class="text-gray-400 block text-[11px]">Klien / Partner:</span>
                                <strong class="text-gray-800" x-text="activeDoc.client_name || activeDoc.vendor_name || '-'"></strong>
                            </div>
                            <div>
                                <span class="text-gray-400 block text-[11px]">Nilai Kontrak / PO:</span>
                                <strong class="text-emerald-700" x-text="activeDoc.value > 0 ? 'Rp ' + Number(activeDoc.value).toLocaleString('id-ID') : '-'"></strong>
                            </div>
                            <div>
                                <span class="text-gray-400 block text-[11px]">Masa Berlaku:</span>
                                <span class="font-medium text-gray-800" x-text="formatReadableDate(activeDoc.effective_date) + (activeDoc.expiry_date ? ' s/d ' + formatReadableDate(activeDoc.expiry_date) : '')"></span>
                            </div>
                            <div>
                                <span class="text-gray-400 block text-[11px]">Versi Berkas:</span>
                                <span class="font-mono font-bold text-gray-700" x-text="activeDoc.version || 'v1.0'"></span>
                            </div>
                            <div>
                                <span class="text-gray-400 block text-[11px]">Lokasi Arsip Fisik:</span>
                                <span class="text-gray-700" x-text="activeDoc.physical_archive_location || 'Lemari Legal A - Box 02'"></span>
                            </div>
                        </div>
                    </div>

                    {{-- Digital Document File Preview Box with Click to View --}}
                    <div class="p-3.5 rounded-xl bg-blue-50/70 border border-blue-200 flex items-center justify-between hover:bg-blue-100/60 transition cursor-pointer"
                         @click="isDocPreviewOpen = true">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-red-100 text-[#C81E2C] border border-red-200 flex items-center justify-center font-black text-xs flex-shrink-0 shadow-xs">
                                PDF
                            </div>
                            <div>
                                <div class="font-bold text-gray-900 text-xs flex items-center gap-1.5">
                                    <span x-text="activeDoc.doc_number + '.pdf'"></span>
                                    <span class="text-[10px] text-blue-600 bg-blue-100 px-1.5 py-0.2 rounded font-semibold">Resmi</span>
                                </div>
                                <div class="text-[11px] text-gray-500">Berkas digital terlampir (Klik untuk buka & periksa lembar PDF)</div>
                            </div>
                        </div>
                        <button type="button" @click.stop="isDocPreviewOpen = true" class="px-3 py-1.5 bg-[#C81E2C] hover:brightness-105 active:translate-y-[1px] text-white font-bold rounded-lg shadow-xs text-xs flex items-center gap-1 cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <span>Buka Dokumen</span>
                        </button>
                    </div>

                    {{-- Verification Checklist --}}
                    <div class="p-3.5 rounded-xl bg-amber-50/50 border border-amber-200 space-y-2">
                        <div class="font-bold text-amber-900 text-xs">Daftar Pemeriksaan Standar Verifikasi Admin:</div>
                        <div class="space-y-1.5 text-gray-700 text-[11.5px]">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" checked class="rounded text-[#C81E2C] focus:ring-0">
                                <span>Keabsahan tanda tangan pejabat berwenang & stempel basah / e-meterai</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" checked class="rounded text-[#C81E2C] focus:ring-0">
                                <span>Kesesuaian nomor referensi, tanggal berlaku, dan nilai komersial</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" checked class="rounded text-[#C81E2C] focus:ring-0">
                                <span>Lampiran teknis (SOW / BoQ / Berita Acara UAT) lengkap dan terbaca jelas</span>
                            </label>
                        </div>
                    </div>

                    {{-- Actions Inside Modal --}}
                    <div class="pt-3 border-t border-gray-100 flex items-center justify-between gap-3">
                        <template x-if="activeDoc.status != 'Verified / Complete'">
                            <button type="button" @click="openRejectFromInspect()" class="px-4 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-bold rounded-xl transition cursor-pointer">
                                Minta Revisi / Kembalikan
                            </button>
                        </template>
                        <template x-if="activeDoc.status == 'Verified / Complete'">
                            <span class="text-xs font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-3 py-1.5 rounded-xl flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Status: Berkas Telah Terverifikasi Sah
                            </span>
                        </template>

                        <div class="flex items-center gap-2">
                            <button type="button" @click="isInspectModalOpen = false" class="px-4 py-2 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 cursor-pointer">Tutup</button>
                            <template x-if="activeDoc.status != 'Verified / Complete'">
                                <form :action="'/admin-support/documents/' + activeDoc.id + '/verify'" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-xs transition cursor-pointer flex items-center gap-1.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        <span>Sahkan & Loloskan Verifikasi</span>
                                    </button>
                                </form>
                            </template>
                        </div>
                    </div>

                </div>
            </template>
        </div>
    </div>

    {{-- MODAL: Document Sheet / PDF Viewer --}}
    @include('admin_support.components.document_viewer_modal')

    {{-- MODAL: Register Dokumen Baru --}}
    <div x-show="isCreateModalOpen" class="fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 space-y-4 border border-[#E7E5E3] shadow-xl animate-fade-in-up" @click.outside="isCreateModalOpen = false">
            <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                <h3 class="text-base font-bold text-gray-900">Daftarkan Dokumen ke Central Register</h3>
                <button @click="isCreateModalOpen = false" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>

            <form action="{{ route('admin_support.documents.store') }}" method="POST" class="space-y-3.5 text-xs">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Tipe Dokumen *</label>
                        <select name="doc_type" required class="wms-input">
                            <option value="SPK">SPK (Surat Perintah Kerja)</option>
                            <option value="PO Client">PO Klien</option>
                            <option value="PO Vendor">PO Vendor / Distributor</option>
                            <option value="Contract">Kontrak Induk / PKS</option>
                            <option value="WO">Work Order (WO)</option>
                            <option value="BAST">BAST (Berita Acara Serah Terima)</option>
                            <option value="Tender">Dokumen Tender</option>
                            <option value="Proposal">Proposal Final</option>
                            <option value="SLA Agreement">SLA Agreement Addendum</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Kategori *</label>
                        <select name="category" required class="wms-input">
                            <option value="Commercial">Commercial / Sales</option>
                            <option value="Legal">Legal & Corporate</option>
                            <option value="Project Delivery">Project Delivery (PMO)</option>
                            <option value="Operations">Operations / Managed Service</option>
                            <option value="Procurement">Procurement & Logistik</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Judul Dokumen *</label>
                    <input type="text" name="title" required class="wms-input" placeholder="e.g. SPK Pengadaan & Implementasi Router SD-WAN Phase 2">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Klien Terkait</label>
                        <input type="text" name="client_name" list="clientList" class="wms-input" placeholder="Ketik / pilih klien">
                        <datalist id="clientList">
                            @foreach($clients as $c)
                                <option value="{{ $c->name }}"></option>
                            @endforeach
                        </datalist>
                    </div>
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Vendor (Jika Dokumen Vendor)</label>
                        <input type="text" name="vendor_name" list="vendorList" class="wms-input" placeholder="Ketik / pilih vendor">
                        <datalist id="vendorList">
                            @foreach($vendors as $v)
                                <option value="{{ $v->name }}"></option>
                            @endforeach
                        </datalist>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Nilai Kontrak / PO (Rp)</label>
                        <input type="number" name="value" class="wms-input" placeholder="e.g. 1500000000">
                    </div>
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Versi Dokumen</label>
                        <input type="text" name="version" class="wms-input" value="v1.0">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Tanggal Berlaku</label>
                        <input type="date" name="effective_date" class="wms-input" value="{{ date('Y-m-d') }}">
                    </div>
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Tanggal Berakhir</label>
                        <input type="date" name="expiry_date" class="wms-input">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Lokasi Arsip Fisik</label>
                    <input type="text" name="physical_archive_location" class="wms-input" placeholder="e.g. Lemari Legal A - Box 02 / Binder PMO 2026">
                </div>

                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Catatan Tambahan</label>
                    <textarea name="notes" rows="2" class="wms-input" placeholder="Keterangan kelengkapan berkas..."></textarea>
                </div>

                <div class="pt-3 border-t border-gray-100 flex justify-end gap-2">
                    <button type="button" @click="isCreateModalOpen = false" class="px-4 py-2 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 cursor-pointer">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-[#C81E2C] text-white font-bold rounded-xl hover:brightness-105 shadow-sm cursor-pointer">Simpan ke Register</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL: Clarification / Reject Document --}}
    <div x-show="isRejectModalOpen" class="fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4 border border-[#E7E5E3] shadow-xl animate-fade-in-up" @click.outside="isRejectModalOpen = false">
            <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                <h3 class="text-base font-bold text-gray-900">Kembalikan Dokumen (Minta Revisi)</h3>
                <button @click="isRejectModalOpen = false" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>

            <form :action="rejectUrl" method="POST" class="space-y-3.5 text-xs">
                @csrf
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Nomor Dokumen</label>
                    <input type="text" x-model="rejectDocNumber" readonly class="wms-input font-bold bg-gray-50">
                </div>

                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Catatan Kekurangan / Klarifikasi *</label>
                    <textarea name="rejection_notes" rows="4" required class="wms-input" placeholder="Jelaskan alasan pengembalian (misal: belum ada paraf legal, lampiran SOW kurang, materai belum ditandatangani)..."></textarea>
                </div>

                <div class="pt-3 border-t border-gray-100 flex justify-end gap-2">
                    <button type="button" @click="isRejectModalOpen = false" class="px-4 py-2 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 cursor-pointer">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-rose-600 text-white font-bold rounded-xl hover:bg-rose-700 shadow-sm cursor-pointer">Kembalikan Dokumen</button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
function adminDocsHandler() {
    return {
        isCreateModalOpen: false,
        isInspectModalOpen: false,
        isDocPreviewOpen: false,
        isRejectModalOpen: false,
        activeDoc: null,
        rejectDocNumber: '',
        rejectUrl: '',
        openInspectModal(doc) {
            this.activeDoc = doc;
            this.isInspectModalOpen = true;
        },
        openRejectFromInspect() {
            if (this.activeDoc) {
                this.isInspectModalOpen = false;
                this.openRejectModal(this.activeDoc.id, this.activeDoc.doc_number);
            }
        },
        openRejectModal(id, docNumber) {
            this.rejectDocNumber = docNumber;
            this.rejectUrl = '/admin-support/documents/' + id + '/reject-clarify';
            this.isRejectModalOpen = true;
        },
        formatReadableDate(dateString) {
            if (!dateString) return '-';
            try {
                const d = new Date(dateString);
                if (isNaN(d.getTime())) return dateString;
                return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
            } catch (e) {
                return dateString;
            }
        }
    };
}
</script>
@endsection
