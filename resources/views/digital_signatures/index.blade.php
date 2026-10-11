@extends('layouts.app')

@section('title', 'Digital Signature - PT IP Network Solusindo')

@push('styles')
<style>
    [x-cloak] { display: none !important; }

    .ipnet-card {
        background-color: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 20px;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.04);
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .ipnet-badge-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background-color: #8F0A0D;
        display: inline-block;
        margin-right: 8px;
    }
    .btn-ipnet-gradient {
        background: linear-gradient(135deg, #B91C1C 0%, #8F0A0D 60%, #750608 100%) !important;
        color: #FFFFFF !important;
        border: 1px solid rgba(255, 255, 255, 0.15) !important;
        box-shadow: 0 4px 12px rgba(143, 10, 13, 0.22) !important;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
    }
    .btn-ipnet-gradient:hover {
        background: linear-gradient(135deg, #C52222 0%, #9C0C0F 60%, #83080A 100%) !important;
        transform: translateY(-1px) !important;
        box-shadow: 0 6px 16px rgba(143, 10, 13, 0.3) !important;
    }
    @keyframes fadeUpStagger {
        0% { opacity: 0; transform: translateY(14px); }
        100% { opacity: 1; transform: translateY(0); }
    }
    .anim-fade-up {
        animation: fadeUpStagger 0.45s cubic-bezier(0.16, 1, 0.3, 1) both;
    }
    .anim-delay-1 { animation-delay: 0.06s !important; }
    .anim-delay-2 { animation-delay: 0.12s !important; }
</style>
@endpush

@section('content')
<div class="flex h-screen overflow-hidden bg-[#F8FAFC] font-sans">
    @include('components.sidebar')

    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Digital Signature'])

        <div x-data="digitalSignatureIndexManager()" class="p-4 sm:p-6 lg:p-7 space-y-5 max-w-[1600px] mx-auto anim-fade-up">

            {{-- ALERT MESSAGES --}}
            @if(session('success'))
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between text-xs font-semibold shadow-xs">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800 font-bold px-2">&times;</button>
                </div>
            @endif
            @if(session('error'))
                <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 flex items-center justify-between text-xs font-semibold shadow-xs">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-800 font-bold px-2">&times;</button>
                </div>
            @endif

            {{-- HEADER SECTION SESUAI STYLE IPNET PROJECT --}}
            <div class="ipnet-card p-5 sm:p-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <p class="text-[#8F0A0D] text-[12px] font-bold inline-flex items-center uppercase tracking-wider mb-1">
                            <span class="ipnet-badge-dot"></span> MANAJEMEN DOKUMEN ELEKTRONIK
                        </p>
                        <h2 class="text-[20px] font-bold text-[#1E293B] tracking-tight">Dokumen & E-Signature</h2>
                        <p class="text-[13px] text-[#64748B] mt-0.5">Pengelolaan dokumen tanda tangan digital internal IPNET dan pengesahan resmi PIC klien.</p>
                    </div>
                    <div class="shrink-0">
                        <a href="{{ route('digital_signatures.create') }}" class="btn-ipnet-gradient px-4 py-2.5 rounded-xl font-bold text-[13px] flex items-center gap-2 cursor-pointer shadow-md">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span>Buat Dokumen Baru</span>
                        </a>
                    </div>
                </div>
            </div>

            {{-- STATS CARDS --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="ipnet-card p-5">
                    <div class="text-[11px] font-semibold text-[#64748B] uppercase tracking-wider">Total Dokumen</div>
                    <div class="text-[26px] font-bold text-[#1E293B] mt-1">{{ number_format($totalDocs) }}</div>
                    <div class="text-[12px] text-[#64748B] mt-0.5">Seluruh berkas dokumen</div>
                </div>
                <div class="ipnet-card p-5 border-amber-200/80 bg-amber-50/20">
                    <div class="text-[11px] font-semibold text-amber-700 uppercase tracking-wider">Proses Internal</div>
                    <div class="text-[26px] font-bold text-amber-700 mt-1">{{ number_format($internalPendingCount) }}</div>
                    <div class="text-[12px] text-amber-700/80 mt-0.5">Menunggu TTD tim IPNET</div>
                </div>
                <div class="ipnet-card p-5 border-blue-200/80 bg-blue-50/20">
                    <div class="text-[11px] font-semibold text-blue-700 uppercase tracking-wider">Siap Kirim ke Klien</div>
                    <div class="text-[26px] font-bold text-blue-700 mt-1">{{ number_format($readyForClientCount) }}</div>
                    <div class="text-[12px] text-blue-700/80 mt-0.5">Internal selesai, link aktif</div>
                </div>
                <div class="ipnet-card p-5 border-emerald-200/80 bg-emerald-50/20">
                    <div class="text-[11px] font-semibold text-emerald-700 uppercase tracking-wider">Selesai & Sah</div>
                    <div class="text-[26px] font-bold text-emerald-700 mt-1">{{ number_format($completedCount) }}</div>
                    <div class="text-[12px] text-emerald-700/80 mt-0.5">Lengkap & QR Code valid</div>
                </div>
            </div>

            {{-- FILTER & SEARCH TOOLBAR --}}
            <div class="ipnet-card p-4 sm:p-5">
                <form method="GET" action="{{ route('digital_signatures.index') }}" class="flex flex-col md:flex-row gap-3 items-center justify-between">
                    {{-- Status Tabs --}}
                    <div class="flex flex-wrap items-center gap-2 w-full md:w-auto">
                        <a href="{{ route('digital_signatures.index', array_merge(request()->except('status', 'page'), [])) }}"
                           class="px-3.5 py-2 rounded-xl text-[12.5px] font-bold transition-all {{ empty($status) ? 'btn-ipnet-gradient shadow-xs text-white' : 'bg-[#F8FAFC] text-[#64748B] border border-[#E2E8F0] hover:text-[#1E293B] hover:bg-[#FEF2F2]' }}">
                           Semua ({{ $totalDocs }})
                        </a>
                        <a href="{{ route('digital_signatures.index', array_merge(request()->except('page'), ['status' => 'internal_in_progress'])) }}"
                           class="px-3.5 py-2 rounded-xl text-[12.5px] font-bold transition-all {{ $status === 'internal_in_progress' ? 'bg-amber-600 text-white shadow-xs' : 'bg-[#F8FAFC] text-[#64748B] border border-[#E2E8F0] hover:text-[#1E293B] hover:bg-[#FEF2F2]' }}">
                           Proses Internal ({{ $internalPendingCount }})
                        </a>
                        <a href="{{ route('digital_signatures.index', array_merge(request()->except('page'), ['status' => 'ready_for_client'])) }}"
                           class="px-3.5 py-2 rounded-xl text-[12.5px] font-bold transition-all {{ $status === 'ready_for_client' ? 'bg-blue-600 text-white shadow-xs' : 'bg-[#F8FAFC] text-[#64748B] border border-[#E2E8F0] hover:text-[#1E293B] hover:bg-[#FEF2F2]' }}">
                           Siap Kirim ke Klien ({{ $readyForClientCount }})
                        </a>
                        <a href="{{ route('digital_signatures.index', array_merge(request()->except('page'), ['status' => 'completed'])) }}"
                           class="px-3.5 py-2 rounded-xl text-[12.5px] font-bold transition-all {{ $status === 'completed' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-[#F8FAFC] text-[#64748B] border border-[#E2E8F0] hover:text-[#1E293B] hover:bg-[#FEF2F2]' }}">
                           Selesai ({{ $completedCount }})
                        </a>
                    </div>

                    {{-- Search Field & Filter Button --}}
                    <div class="flex items-center gap-2.5 w-full md:w-auto">
                        <div class="relative flex-1 md:w-80">
                            <svg class="w-4 h-4 text-[#94A3B8] absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <input type="text" 
                                   name="q" 
                                   value="{{ $q }}" 
                                   placeholder="Cari judul dokumen, nomor, klien..."
                                   class="w-full pl-9 pr-3.5 py-2 rounded-xl border border-[#CBD5E1] text-[12.5px] font-semibold text-[#1E293B] bg-white outline-none hover:border-[#94A3B8] focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition-all shadow-xs placeholder-[#94A3B8]">
                        </div>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-[#1E293B] hover:bg-[#0F172A] text-white text-[12.5px] font-bold transition cursor-pointer shadow-xs">
                            Filter
                        </button>
                        @if($q || $status || $category)
                            <a href="{{ route('digital_signatures.index') }}" class="px-3 py-2 text-[12px] font-bold text-[#64748B] hover:text-[#8F0A0D] bg-[#F8FAFC] hover:bg-[#FEF2F2] border border-[#E2E8F0] hover:border-[#FECACA] rounded-xl transition cursor-pointer">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            {{-- DOCUMENT TABLE CARD --}}
            <div class="ipnet-card overflow-hidden">
                {{-- Header Card Tabel Sesuai Gambar 4 --}}
                <div class="p-4 sm:p-5 border-b border-[#E2E8F0] bg-white flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <h3 class="text-sm font-extrabold text-[#1E293B]">Daftar Dokumen Digital Signature</h3>
                        <p class="text-[12px] text-[#64748B] mt-0.5">Berikut ini daftar berkas dokumen yang memerlukan tanda tangan dan persetujuan resmi.</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[900px] border-collapse text-[13px] text-left">
                        <thead>
                            <tr class="bg-[#F8FAFC] border-b border-[#E2E8F0]">
                                <th class="py-3 px-4 text-[11px] font-semibold text-[#64748B] uppercase tracking-wider">Nomor & Judul Dokumen</th>
                                <th class="py-3 px-4 text-[11px] font-semibold text-[#64748B] uppercase tracking-wider">Proyek / Klien</th>
                                <th class="py-3 px-4 text-[11px] font-semibold text-[#64748B] uppercase tracking-wider">Penandatangan Internal</th>
                                <th class="py-3 px-4 text-[11px] font-semibold text-[#64748B] uppercase tracking-wider">PIC Klien</th>
                                <th class="py-3 px-4 text-[11px] font-semibold text-[#64748B] uppercase tracking-wider text-center">Status</th>
                                <th class="py-3 px-4 text-[11px] font-semibold text-[#64748B] uppercase tracking-wider text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#F1F5F9]">
                            @forelse($documents as $doc)
                                <tr class="hover:bg-[#F8FAFC]/80 transition-colors">
                                    {{-- Nomor & Judul Dokumen --}}
                                    <td class="py-3.5 px-4">
                                        <a href="{{ route('digital_signatures.show', $doc->id) }}" class="font-medium text-[#1E293B] text-[13px] leading-snug hover:text-[#8F0A0D] transition">
                                            {{ $doc->title }}
                                        </a>
                                        <div class="flex items-center gap-2 mt-1 text-[11.5px] text-[#64748B]">
                                            <span class="font-mono text-[#475569] font-semibold">{{ $doc->document_number }}</span>
                                            <span>&bull;</span>
                                            <span class="px-2 py-0.5 rounded-md bg-[#F1F5F9] text-[#475569] border border-[#E2E8F0] text-[10.5px] font-semibold uppercase">{{ $doc->category }}</span>
                                        </div>
                                    </td>

                                    {{-- Proyek / Klien --}}
                                    <td class="py-3.5 px-4">
                                        @if($doc->project_name)
                                            <div class="font-medium text-[#334155] text-[13px]">{{ $doc->project_name }}</div>
                                            <div class="text-[11.5px] text-[#64748B] mt-0.5">{{ $doc->client_company ?: '-' }}</div>
                                        @else
                                            <div class="font-medium text-[#334155] text-[13px]">{{ $doc->client_company ?: '-' }}</div>
                                            <div class="text-[11px] text-[#94A3B8] italic">Dokumen Lepas (Non-Proyek)</div>
                                        @endif
                                    </td>

                                    {{-- Penandatangan Internal --}}
                                    <td class="py-3.5 px-4">
                                        @php
                                            $signers = $doc->internal_signers ?? [];
                                            $signedCount = collect($signers)->where('status', 'signed')->count();
                                            $totalSigners = count($signers);
                                        @endphp
                                        <div class="flex items-center gap-2">
                                            <div class="w-18 bg-[#F1F5F9] border border-[#E2E8F0] rounded-full h-2 overflow-hidden">
                                                <div class="bg-gradient-to-r from-[#8F0A0D] to-[#D62E3C] h-2 rounded-full transition-all" style="width: {{ $totalSigners > 0 ? ($signedCount / $totalSigners) * 100 : 0 }}%"></div>
                                            </div>
                                            <span class="text-[11.5px] font-semibold text-[#1E293B]">{{ $signedCount }}/{{ $totalSigners }} Tanda Tangan</span>
                                        </div>
                                        <div class="text-[11px] text-[#64748B] mt-1">
                                            @foreach(array_slice($signers, 0, 2) as $s)
                                                <span class="{{ ($s['status'] ?? '') === 'signed' ? 'text-[#8F0A0D] font-bold' : 'text-[#64748B]' }}">
                                                    {{ Str::limit($s['name'], 15) }} {{ ($s['status'] ?? '') === 'signed' ? '✓' : '⌛' }}
                                                </span>@if(!$loop->last), @endif
                                            @endforeach
                                            @if(count($signers) > 2)
                                                <span class="text-[#94A3B8] font-medium">+{{ count($signers) - 2 }}</span>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- PIC Klien --}}
                                    <td class="py-3.5 px-4">
                                        <div class="font-medium text-[#334155] text-[13px]">{{ $doc->client_name ?: '-' }}</div>
                                        <div class="text-[11.5px] text-[#64748B] mt-0.5">
                                            {{ $doc->client_position ?: '-' }}
                                        </div>
                                    </td>

                                    {{-- Status --}}
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        @if($doc->status === 'completed')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-red-50 text-[#8F0A0D] border border-red-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-[#8F0A0D]"></span>
                                                Selesai & Sah
                                            </span>
                                        @elseif($doc->status === 'ready_for_client')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                                Siap Kirim PIC
                                            </span>
                                        @elseif($doc->status === 'internal_in_progress')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                TTD Internal
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                                Draft
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Aksi --}}
                                    <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                        @php
                                            $currUser = auth()->user();
                                            $canUserSignDoc = $currUser ? $doc->canUserSign($currUser->id) : false;
                                        @endphp
                                        <div class="inline-flex items-center gap-2 justify-end">
                                            {{-- Tombol Detail (Biru Profesional IPNET) --}}
                                            <a href="{{ route('digital_signatures.show', $doc->id) }}"
                                               title="Detail & Pratinjau Dokumen"
                                               class="px-3 py-1.5 rounded-lg bg-sky-600 hover:bg-sky-700 text-white font-bold text-xs inline-flex items-center gap-1.5 shadow-2xs transition-all active:scale-95">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                                <span>Detail</span>
                                            </a>

                                            {{-- Tombol Tanda Tangani (Merah Gradien IPNET) & Tolak (Rose Border) --}}
                                            @if($canUserSignDoc)
                                                <div class="inline-flex flex-col gap-1 items-stretch">
                                                    <a href="{{ route('digital_signatures.place_signature', $doc->id) }}"
                                                       title="Tempatkan Tanda Tangan Sekarang"
                                                       class="px-2.5 py-1 rounded-md bg-gradient-to-r from-[#B91C1C] via-[#8F0A0D] to-[#750608] hover:from-[#C52222] hover:to-[#83080A] text-white font-bold text-[11px] inline-flex items-center justify-center gap-1 shadow-xs transition-all active:scale-95">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                                        <span>Tanda Tangani</span>
                                                    </a>
                                                    <button type="button"
                                                            onclick="rejectDocumentTable({{ $doc->id }})"
                                                            title="Tolak Dokumen Ini"
                                                            class="px-2.5 py-1 rounded-md bg-white hover:bg-rose-50 text-rose-700 border border-rose-300 font-bold text-[11px] inline-flex items-center justify-center gap-1 shadow-2xs transition-all active:scale-95 cursor-pointer">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                                        <span>Tolak</span>
                                                    </button>
                                                </div>
                                            @endif

                                            @if($doc->status === 'ready_for_client' || $doc->status === 'completed')
                                                <button type="button"
                                                        onclick="copyClientLink('{{ url('/sign/' . $doc->client_signing_token) }}')"
                                                        title="Salin Link Penandatanganan Klien"
                                                        class="p-1.5 rounded-lg bg-blue-50 border border-blue-200 text-blue-600 hover:bg-blue-600 hover:text-white transition-all cursor-pointer">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                                                </button>
                                            @endif
                                            <a href="{{ route('digital_signatures.download_pdf', $doc->id) }}"
                                               title="{{ !empty($doc->signed_file_path) ? 'Unduh Dokumen Ber-TTD (PDF)' : 'Unduh Dokumen PDF' }}"
                                               class="p-1.5 rounded-lg bg-[#F8FAFC] border border-[#E2E8F0] text-[#475569] hover:bg-[#1E293B] hover:text-white hover:border-[#1E293B] transition-all">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                            </a>

                                            {{-- Tombol Hapus Dokumen --}}
                                            @if($doc->created_by === auth()->id() || auth()->user()->hasAnyRole(['Super Admin', 'Superadmin', 'Admin']))
                                                <button type="button"
                                                        @click="openDeleteModal({ id: {{ $doc->id }}, title: '{{ addslashes($doc->title) }}', number: '{{ addslashes($doc->document_number) }}', client: '{{ addslashes($doc->client_company ?: $doc->client_name) }}' })"
                                                        title="Hapus Dokumen"
                                                        class="p-1.5 rounded-lg bg-rose-50 border border-rose-200 text-rose-600 hover:bg-rose-600 hover:text-white hover:border-rose-600 transition-all cursor-pointer">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-14 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="w-14 h-14 rounded-2xl bg-[#F8FAFC] border border-[#E2E8F0] text-[#94A3B8] flex items-center justify-center mb-3">
                                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            </div>
                                            <div class="text-[14px] font-bold text-[#1E293B]">Belum ada dokumen digital signature</div>
                                            <p class="text-[12.5px] text-[#64748B] mt-0.5">Mulai dengan mengklik tombol "Buat Dokumen Baru" di atas.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($documents->hasPages())
                    <div class="p-4 border-t border-[#F1F5F9] bg-[#F8FAFC]/50">
                        {{ $documents->links() }}
                    </div>
                @endif
            </div>

            {{-- MODAL KONFIRMASI HAPUS DOKUMEN (STANDAR POPUP APLIKASI IPNET) --}}
            <template x-teleport="body">
                <div x-show="isDeleteModalOpen" 
                     x-cloak 
                     class="fixed inset-0 z-[99999] bg-[#0F172A]/60 backdrop-blur-xs flex items-center justify-center p-4"
                     @click.self="if(!isDeleting) isDeleteModalOpen = false"
                     @keydown.escape.window="if(!isDeleting) isDeleteModalOpen = false">
                    
                    <div class="bg-white rounded-2xl w-[440px] max-w-full p-6 text-center shadow-2xl border border-[#E2E8F0] my-auto anim-fade-up">
                        
                        <div class="w-14 h-14 rounded-full bg-[#FEF2F2] border border-[#FECACA] flex items-center justify-center mx-auto mb-4 text-[#8F0A0D]">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </div>

                        <h3 class="text-[17px] font-bold text-[#1E293B]">Hapus Dokumen?</h3>
                        <p class="text-[12.5px] text-[#64748B] mt-1.5 leading-relaxed">
                            Apakah Anda yakin ingin menghapus dokumen <strong class="text-[#1E293B] font-bold" x-text="deleteDocData ? deleteDocData.title : ''"></strong>? Dokumen dan riwayat penandatanganan akan dihapus secara permanen.
                        </p>

                        <template x-if="deleteDocData">
                            <div class="mt-4 p-3.5 bg-[#F8FAFC] rounded-xl border border-[#E2E8F0] text-left text-[12px] space-y-1.5 text-[#64748B]">
                                <div class="flex justify-between items-center">
                                    <span class="text-[#64748B] font-medium">Nomor Dokumen:</span>
                                    <span class="font-mono font-bold text-[#1E293B]" x-text="deleteDocData.number"></span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-[#64748B] font-medium">Klien / Instansi:</span>
                                    <span class="font-semibold text-[#1E293B]" x-text="deleteDocData.client || '-'"></span>
                                </div>
                            </div>
                        </template>

                        {{-- Action Buttons with Loading State --}}
                        <div class="flex items-center justify-end gap-3 pt-5">
                            <button type="button" 
                                    @click="isDeleteModalOpen = false" 
                                    :disabled="isDeleting"
                                    class="flex-1 min-h-[40px] bg-white hover:bg-[#F1F5F9] text-[#475569] border border-[#CBD5E1] shadow-2xs px-4 py-2 rounded-xl font-bold text-[12.5px] transition cursor-pointer disabled:opacity-50">
                                Batal
                            </button>

                            <button type="button" 
                                    @click="executeDeleteDoc()" 
                                    class="flex-1 min-h-[40px] bg-[#8F0A0D] hover:bg-[#73080A] text-white shadow-md px-4 py-2 rounded-xl font-bold text-[12.5px] transition cursor-pointer flex items-center justify-center gap-2">
                                <span>Ya, Hapus</span>
                            </button>
                        </div>

                    </div>
                </div>
            </template>

        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('vendor/sweetalert2/sweetalert2.all.min.js') }}" onerror="this.src='https://cdn.jsdelivr.net/npm/sweetalert2@11'"></script>
<script>
function digitalSignatureIndexManager() {
    return {
        isDeleteModalOpen: false,
        isDeleting: false,
        deleteDocData: null,

        openDeleteModal(data) {
            this.deleteDocData = data;
            this.isDeleting = false;
            this.isDeleteModalOpen = true;
        },

        async executeDeleteDoc() {
            if (!this.deleteDocData) return;
            const docId = this.deleteDocData.id;

            // Tutup modal konfirmasi
            this.isDeleteModalOpen = false;

            // Buka popup SweetAlert loading "Menghapus Dokumen..."
            if (window.Swal) {
                Swal.fire({
                    title: 'Menghapus Dokumen...',
                    text: 'Mohon tunggu sebentar, sedang memproses penghapusan.',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            }

            try {
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
                const formData = new FormData();
                formData.append('_token', token);
                formData.append('_method', 'DELETE');

                const res = await fetch(`{{ url('/digital-signatures') }}/${docId}/delete`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: formData
                });

                const data = await res.json().catch(() => ({}));
                if ((res.ok && data.success) || res.status === 404 || (data.message && data.message.includes('No query results'))) {
                    if (window.Swal) {
                        await Swal.fire({
                            icon: 'success',
                            title: 'Berhasil Dihapus',
                            text: 'Dokumen berhasil dihapus dari sistem.',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }
                    window.location.reload();
                } else {
                    if (window.Swal) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Menghapus',
                            text: data.message || 'Terjadi kesalahan saat menghapus dokumen.',
                            confirmButtonColor: '#8F0A0D'
                        });
                    } else {
                        alert(data.message || 'Gagal menghapus dokumen.');
                    }
                }
            } catch (err) {
                if (window.Swal) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Menghapus',
                        text: err.message || 'Terjadi kesalahan koneksi ke server.',
                        confirmButtonColor: '#8F0A0D'
                    });
                } else {
                    alert('Gagal menghapus dokumen: ' + (err.message || 'Error'));
                }
            }
        }
    };
}

function copyClientLink(url) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(url).then(() => {
            if (window.Swal) {
                Swal.fire({
                    icon: 'success',
                    title: 'Tautan Tersalin!',
                    text: 'Tautan tanda tangan PIC Klien berhasil disalin ke clipboard.',
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                alert('Tautan tanda tangan PIC Klien berhasil disalin ke clipboard:\n' + url);
            }
        });
    } else {
        prompt('Salin tautan ini untuk dikirimkan ke PIC Klien:', url);
    }
}

async function rejectDocumentTable(docId) {
    let reason = '';
    if (window.Swal) {
        const { value: text, isConfirmed } = await Swal.fire({
            title: 'Tolak Dokumen Ini?',
            text: 'Masukkan alasan penolakan dokumen:',
            input: 'textarea',
            inputPlaceholder: 'Tuliskan alasan penolakan di sini...',
            inputAttributes: {
                'aria-label': 'Alasan penolakan'
            },
            showCancelButton: true,
            confirmButtonText: 'Ya, Tolak Dokumen',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#E11D48',
            cancelButtonColor: '#64748B',
            inputValidator: (value) => {
                if (!value || !value.trim()) {
                    return 'Alasan penolakan wajib diisi!';
                }
            }
        });
        if (!isConfirmed) return;
        reason = text;
    } else {
        reason = prompt('Masukkan alasan penolakan dokumen:');
        if (!reason || !reason.trim()) {
            alert('Alasan penolakan wajib diisi!');
            return;
        }
    }

    try {
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
        const res = await fetch(`/digital-signatures/${docId}/reject-internal`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ reason: reason })
        });
        const data = await res.json();
        if (data.success) {
            if (window.Swal) {
                await Swal.fire({
                    icon: 'success',
                    title: 'Dokumen Ditolak',
                    text: 'Penolakan dokumen telah dicatat.',
                    confirmButtonColor: '#8F0A0D'
                });
            } else {
                alert('Dokumen berhasil ditolak.');
            }
            window.location.reload();
        } else {
            if (window.Swal) {
                Swal.fire({ icon: 'error', title: 'Gagal', text: data.message || 'Terjadi kesalahan.', confirmButtonColor: '#8F0A0D' });
            } else {
                alert('Gagal: ' + (data.message || 'Terjadi kesalahan'));
            }
        }
    } catch (e) {
        if (window.Swal) {
            Swal.fire({ icon: 'error', title: 'Koneksi Gagal', text: 'Gagal menghubungi server.', confirmButtonColor: '#8F0A0D' });
        } else {
            alert('Gagal menghubungi server.');
        }
    }
}
</script>
@endpush
@endsection
