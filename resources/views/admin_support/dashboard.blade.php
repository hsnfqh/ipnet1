@extends('layouts.app')

@section('title', 'Admin Support - Central Governance & Gatekeeper')

@section('content')
<div class="flex h-screen overflow-hidden" x-data="adminDashboard()" x-cloak>
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto bg-[#FAF9F8]">
        @include('components.topbar', ['title' => 'Admin Support & Document Gatekeeper'])
        
        <div class="p-4 sm:p-5 lg:p-[26px] animate-fade-in space-y-6 max-w-[1600px] mx-auto">
            
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



            {{-- 4 Primary Metric Cards --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-3.5">
                <x-metric-card label="Dokumen Terverifikasi" value="{{ $verifiedDocs }} / {{ $totalDocs }}" icon="ShieldCheck" :accent="true" href="{{ route('admin_support.documents.index') }}">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </x-metric-card>

                <x-metric-card label="Perlu Review / Revisi" value="{{ $pendingVerifyDocs + $clarificationDocs }}" icon="FileText" href="{{ route('admin_support.documents.index') }}">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </x-metric-card>

                <x-metric-card label="Surat Jalan & Dispatch Aktif" value="{{ $activeDispatches }}" icon="Truck" href="{{ route('admin_support.logistics.index') }}">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h2m-8 0a2 2 0 100 4 2 2 0 000-4zm10 0a2 2 0 100 4 2 2 0 000-4z"/>
                </x-metric-card>

                <x-metric-card label="Aset Alat Kerja & Toolkit" value="{{ $borrowedAssets }} / {{ $totalEquipmentAssets }} Dipinjam" icon="Tool" href="{{ route('admin_support.assets.index') }}">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                </x-metric-card>
            </div>

            {{-- 2-Column Section: Document Review Queue & Gatekeeper Checklist --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-stretch">
                
                {{-- Left: Dokumen Masuk & Menunggu Verifikasi (6 Cols) --}}
                <div class="lg:col-span-6 bg-white rounded-2xl border border-[#E7E5E3] shadow-xs flex flex-col justify-between overflow-hidden">
                    <div class="p-4 px-5 flex items-center justify-between border-b border-[#EFEDEB]">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse"></span>
                            <h3 class="font-bold text-gray-900 text-sm">Antrean Verifikasi Dokumen Masuk</h3>
                        </div>
                        <a href="{{ route('admin_support.documents.index') }}" class="text-xs font-bold text-[#C81E2C] hover:underline">
                            Lihat Register &rarr;
                        </a>
                    </div>

                    <div class="divide-y divide-gray-100 flex-1">
                        @forelse($pendingDocuments as $doc)
                            <div class="p-4 hover:bg-gray-50/70 transition flex items-start justify-between gap-3">
                                <div class="space-y-1.5 flex-1 min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="text-xs font-bold text-gray-800">{{ $doc->doc_number }}</span>
                                        
                                        @php $tBadge = $doc->type_badge; @endphp
                                        <span class="px-2 py-0.5 rounded-md text-[10.5px] font-bold border {{ $tBadge['bg'] }}">
                                            {{ $tBadge['name'] }}
                                        </span>

                                        @php $stBadge = $doc->status_badge; @endphp
                                        <span class="px-2 py-0.5 rounded-md text-[10.5px] font-bold border {{ $stBadge['bg'] }} {{ $stBadge['text'] }} {{ $stBadge['border'] }}">
                                            {{ $stBadge['label'] }}
                                        </span>
                                    </div>

                                    <h4 class="font-bold text-gray-900 text-sm leading-snug truncate">
                                        {{ $doc->title }}
                                    </h4>

                                    <div class="flex items-center gap-2 text-xs text-gray-500 flex-wrap">
                                        <span class="font-semibold text-gray-700">{{ $doc->client_name ?: ($doc->vendor_name ?: ($doc->project ? $doc->project->name : 'Internal')) }}</span>
                                        @if($doc->value > 0)
                                            <span>•</span>
                                            <span>Nilai: <strong class="text-emerald-700 font-bold">Rp {{ number_format($doc->value, 0, ',', '.') }}</strong></span>
                                        @endif
                                        @if($doc->physical_archive_location)
                                            <span>•</span>
                                            <span class="text-gray-400">Lokasi: {{ $doc->physical_archive_location }}</span>
                                        @endif
                                    </div>

                                    @if($doc->rejection_notes)
                                        <div class="text-[11.5px] text-rose-700 bg-rose-50 p-2 rounded-lg border border-rose-100">
                                            <strong>Catatan Klarifikasi:</strong> {{ $doc->rejection_notes }}
                                        </div>
                                    @endif
                                </div>

                                <div class="flex items-center gap-1.5 flex-shrink-0">
                                    <button @click="openInspectModal({{ json_encode($doc) }})" 
                                            class="px-3 py-1.5 bg-[#C81E2C] hover:brightness-105 active:translate-y-[1px] text-white rounded-lg text-xs font-bold shadow-xs transition inline-flex items-center gap-1 cursor-pointer" title="Periksa Berkas & Verifikasi">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        <span>Periksa</span>
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div class="py-12 text-center text-gray-400 text-xs">
                                <svg class="w-8 h-8 mx-auto mb-2 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Seluruh dokumen telah terverifikasi. Tidak ada antrean pending.
                            </div>
                        @endforelse
                    </div>

                    <div class="p-3 bg-gray-50/50 border-t border-gray-100 text-[11.5px] text-gray-500 flex items-center justify-between px-5">
                        <span>Total Dokumen Tercatat: <strong>{{ $totalDocs }} Berkas</strong></span>
                        <span class="text-emerald-700 font-bold">{{ $verifiedDocs }} Terverifikasi Sah</span>
                    </div>
                </div>

                {{-- Right: Gatekeeper Checklist Alerts (6 Cols) --}}
                <div class="lg:col-span-6 bg-white rounded-2xl border border-[#E7E5E3] shadow-xs flex flex-col justify-between overflow-hidden">
                    <div class="p-4 px-5 flex items-center justify-between border-b border-[#EFEDEB]">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-rose-500 animate-pulse"></span>
                            <h3 class="font-bold text-gray-900 text-sm">Gatekeeper: Dokumen Wajib Proyek yang Belum Lengkap</h3>
                        </div>
                        <a href="{{ route('admin_support.checklists.index') }}" class="text-xs font-bold text-[#C81E2C] hover:underline">
                            Matriks Lengkap &rarr;
                        </a>
                    </div>

                    <div class="divide-y divide-gray-100 flex-1">
                        @forelse($outstandingChecklists as $chk)
                            <div class="p-4 hover:bg-gray-50/70 transition flex items-start justify-between gap-3">
                                <div class="space-y-1.5 flex-1 min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="px-2 py-0.5 rounded-md text-[10.5px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                            Wajib / Mandatory
                                        </span>
                                        <span class="px-2 py-0.5 rounded-md text-[10.5px] font-bold bg-gray-100 text-gray-700">
                                            {{ $chk->milestone }}
                                        </span>
                                    </div>

                                    <h4 class="font-bold text-gray-900 text-sm leading-snug">
                                        {{ $chk->document_name }}
                                    </h4>

                                    <div class="flex items-center gap-2 text-xs text-gray-500">
                                        <span>Proyek: <strong class="text-gray-800">{{ $chk->project ? $chk->project->name : 'N/A' }}</strong></span>
                                        <span>•</span>
                                        <span class="text-rose-600 font-semibold">{{ $chk->is_submitted ? 'Telah Disubmit (Belum Diverifikasi)' : 'Belum Disubmit' }}</span>
                                    </div>
                                </div>

                                <a href="{{ route('admin_support.checklists.index', ['project_id' => $chk->project_id]) }}" class="px-3 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-lg text-xs transition self-center">
                                    Cek
                                </a>
                            </div>
                        @empty
                            <div class="py-12 text-center text-gray-400 text-xs">
                                <svg class="w-8 h-8 mx-auto mb-2 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Seluruh dokumen persyaratan proyek telah lengkap & terverifikasi.
                            </div>
                        @endforelse
                    </div>

                    <div class="p-3 bg-gray-50/50 border-t border-gray-100 text-[11.5px] text-gray-500 flex items-center justify-between px-5">
                        <span>Workflow Guard Active</span>
                        <span class="text-rose-700 font-bold">{{ count($outstandingChecklists) }} Persyaratan Menggantung</span>
                    </div>
                </div>

            </div>

            {{-- Bottom Section: Logistics Dispatches & Serial Number Registry --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 items-stretch">
                
                {{-- Logistics & Surat Jalan Aktif --}}
                <div class="bg-white rounded-2xl border border-[#E7E5E3] shadow-xs flex flex-col justify-between overflow-hidden">
                    <div class="p-4 px-5 flex items-center justify-between border-b border-[#EFEDEB]">
                        <h3 class="font-bold text-gray-900 text-sm">Surat Jalan & Pengiriman Material (Logistik)</h3>
                        <a href="{{ route('admin_support.logistics.index') }}" class="text-xs font-bold text-[#C81E2C] hover:underline">Kelola Surat Jalan &rarr;</a>
                    </div>

                    <div class="divide-y divide-gray-100 flex-1">
                        @forelse($recentDispatches as $disp)
                            <div class="p-4 hover:bg-gray-50/70 transition flex items-start justify-between gap-3">
                                <div class="space-y-1 min-w-0 flex-1">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="font-bold text-gray-900 text-sm">{{ $disp->dispatch_number }}</span>
                                        @php $sBadge = $disp->status_badge; @endphp
                                        <span class="px-2 py-0.5 rounded-md text-[10.5px] font-bold border {{ $sBadge['bg'] }} {{ $sBadge['text'] }} {{ $sBadge['border'] }}">
                                            {{ $sBadge['label'] }}
                                        </span>
                                        <span class="text-xs text-gray-400">• {{ $disp->dispatch_date ? $disp->dispatch_date->format('d M Y') : '' }}</span>
                                    </div>
                                    <p class="text-xs text-gray-700 font-semibold truncate">Tujuan: {{ $disp->client_name ?: ($disp->project ? $disp->project->name : 'Site Proyek') }}</p>
                                    <p class="text-[11.5px] text-gray-400 truncate">Kurir: {{ $disp->courier_name ?: $disp->courier_type }} &bull; Penerima: {{ $disp->recipient_name }}</p>
                                </div>
                            </div>
                        @empty
                            <div class="py-8 text-center text-gray-400 text-xs">Belum ada pengiriman surat jalan aktif.</div>
                        @endforelse
                    </div>

                    <div class="p-3 bg-gray-50/50 border-t border-gray-100 text-[11.5px] text-gray-500 px-5 flex justify-between">
                        <span>Total Surat Jalan Aktif: <strong>{{ $activeDispatches }} Pengiriman</strong></span>
                    </div>
                </div>

                {{-- Master Serial Number (SN Tracking) Terbaru --}}
                <div class="bg-white rounded-2xl border border-[#E7E5E3] shadow-xs flex flex-col justify-between overflow-hidden">
                    <div class="p-4 px-5 flex items-center justify-between border-b border-[#EFEDEB]">
                        <div>
                            <h3 class="font-bold text-gray-900 text-sm">Pelacakan Serial Number (SN Master Registry)</h3>
                        </div>
                        <a href="{{ route('admin_support.inventory.index') }}" class="text-xs font-bold text-[#C81E2C] hover:underline">Database SN &rarr;</a>
                    </div>

                    <div class="divide-y divide-gray-100 flex-1">
                        @forelse($recentSerialNumbers as $sn)
                            <div class="p-4 hover:bg-gray-50/70 transition flex items-start justify-between gap-3">
                                <div class="space-y-1 min-w-0 flex-1">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="text-xs font-bold text-gray-800">{{ $sn->serial_number }}</span>
                                        <span class="px-2 py-0.5 rounded-md text-[10.5px] font-bold bg-gray-100 text-gray-700">{{ $sn->brand }} {{ $sn->category }}</span>
                                        
                                        @php $snBadge = $sn->status_badge; @endphp
                                        <span class="px-2 py-0.5 rounded-md text-[10.5px] font-bold border {{ $snBadge['bg'] }} {{ $snBadge['text'] }} {{ $snBadge['border'] }}">
                                            {{ $snBadge['label'] }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-gray-800 font-semibold truncate">{{ $sn->product_name }}</p>
                                    <p class="text-[11.5px] text-gray-400">Lokasi: {{ $sn->current_location }} &bull; Klien: {{ $sn->client_name ?: '-' }}</p>
                                </div>
                            </div>
                        @empty
                            <div class="py-8 text-center text-gray-400 text-xs">Belum ada serial number tercatat.</div>
                        @endforelse
                    </div>

                    <div class="p-3 bg-gray-50/50 border-t border-gray-100 text-[11.5px] text-gray-500 flex items-center justify-between px-5">
                        <span>Total SN Terdaftar:</span>
                        <span class="font-bold text-gray-800">{{ $totalSerialNumbers }} Unit Hardware</span>
                    </div>
                </div>

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
function adminDashboard() {
    return {
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
