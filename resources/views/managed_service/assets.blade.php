@extends('layouts.app')

@section('title', 'Data Aset & Perangkat - Managed Service')

@push('styles')
<style>
    :root {
        --ipnet-primary: #8F0A0D;
        --ipnet-primary-hover: #73080A;
    }
    .ipnet-card {
        background-color: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04), 0 4px 12px rgba(0, 0, 0, 0.02);
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .btn-ipnet-gradient {
        background: linear-gradient(135deg, #8F0A0D 0%, #B81525 100%);
        color: #FFFFFF;
        transition: all 0.2s ease;
    }
    .btn-ipnet-gradient:hover {
        background: linear-gradient(135deg, #7A080B 0%, #9E0E1D 100%);
        box-shadow: 0 6px 16px rgba(143, 10, 13, 0.25);
        transform: translateY(-1px);
    }
    @keyframes fadeUpStagger {
        0% { opacity: 0; transform: translateY(16px); }
        100% { opacity: 1; transform: translateY(0); }
    }
    .anim-fade-up {
        animation: fadeUpStagger 0.5s cubic-bezier(0.16, 1, 0.3, 1) both;
    }
    .anim-delay-1 { animation-delay: 0.06s !important; }
    .anim-delay-2 { animation-delay: 0.12s !important; }
    .anim-delay-3 { animation-delay: 0.18s !important; }
</style>
@endpush

@section('content')
<div class="flex h-screen overflow-hidden bg-[#F8FAFC] font-sans" x-data="assetManager()" x-cloak>
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Asset Management'])
        
        <div class="p-4 sm:p-6 lg:p-7 space-y-6 max-w-[1600px] mx-auto animate-fade-in">
            
            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between text-[13px] font-semibold shadow-xs">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-600 flex-shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 font-bold px-2">✕</button>
                </div>
            @endif

            @if(session('info'))
                <div class="p-4 rounded-xl bg-blue-50 border border-blue-200 text-blue-800 flex items-center justify-between text-[13px] font-semibold shadow-xs">
                    <div class="flex items-center gap-2.5">
                        <span>{{ session('info') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-blue-500 hover:text-blue-700 font-bold px-2">✕</button>
                </div>
            @endif

            <!-- SECTION HEADER & FILTER CONTROLS -->
            <div class="ipnet-card p-5 sm:p-6 anim-fade-up anim-delay-1">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 pb-4 mb-4 border-b border-[#E2E8F0]">
                    <div>
                        <p class="text-[#8F0A0D] text-[12px] font-bold inline-flex items-center uppercase tracking-wider">
                            <span class="w-2 h-2 rounded-full bg-[#8F0A0D] inline-block mr-2"></span> MANAJEMEN ASET PERANGKAT
                        </p>
                        <h2 class="text-[20px] font-bold text-[#1E293B] tracking-tight mt-0.5">Daftar Aset Eksternal (Perangkat Klien)</h2>
                        <p class="text-[13px] text-[#64748B] mt-0.5">Inventaris perangkat, spesifikasi OS, status garansi, kontrak vendor (PAR-ND/SD/RTF), EOL/EOS, &amp; backup konfigurasi</p>
                    </div>

                    <div class="flex flex-wrap items-center gap-3 shrink-0">
                        {{-- Toggle Button Internal vs External (Like Reference Image) --}}
                        <div class="inline-flex p-1 bg-[#F1F5F9] rounded-xl border border-[#E2E8F0] shadow-2xs">
                            <a href="{{ route('ms.assets.internal') }}" 
                               class="px-4 py-2 rounded-lg text-xs font-semibold transition-all flex items-center gap-1.5 text-[#64748B] hover:text-[#1E293B] hover:bg-white/60">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                <span>Internal Asset</span>
                            </a>
                            <a href="{{ route('ms.assets.external') }}" 
                               class="px-4 py-2 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 bg-[#8F0A0D] text-white shadow-xs">
                                <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/></svg>
                                <span>External Asset</span>
                            </a>
                        </div>

                        <div class="px-3.5 py-2 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0] text-[12px] font-bold text-[#1E293B] flex items-center gap-1.5 shadow-xs">
                            <span class="text-[#64748B]">Total:</span>
                            <span class="text-[#8F0A0D] font-extrabold">{{ $assets->total() }}</span>
                        </div>

                        <button @click="openCreateModal()" class="btn-ipnet-gradient px-4 py-2 rounded-xl font-bold text-[12.5px] flex items-center gap-2 cursor-pointer shadow-md">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                            <span>Tambah Aset</span>
                        </button>
                    </div>
                </div>

                <!-- KPI Summary Cards: 4 Pilar Informasi Perangkat Customer -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 mb-5">
                    {{-- Metric 1: Total Perangkat --}}
                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 flex items-center justify-between">
                        <div>
                            <span class="text-[10.5px] font-bold text-slate-500 uppercase tracking-wider block">Total Perangkat</span>
                            <div class="text-[20px] font-extrabold text-slate-900 mt-0.5">{{ $totalAssetsCount ?? $assets->total() }}</div>
                            <span class="text-[11px] text-slate-500 font-medium">Unit Terdata</span>
                        </div>
                        <div class="w-9 h-9 rounded-lg bg-white text-slate-700 flex items-center justify-center border border-slate-200 shadow-2xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/></svg>
                        </div>
                    </div>

                    {{-- Metric 2: Support Vendor Aktif --}}
                    <div class="p-3.5 rounded-xl bg-emerald-50/60 border border-emerald-200/80 flex items-center justify-between">
                        <div>
                            <span class="text-[10.5px] font-bold text-emerald-800 uppercase tracking-wider block">Support Vendor</span>
                            <div class="text-[20px] font-extrabold text-emerald-700 mt-0.5">{{ $activeSupportCount ?? 0 }}</div>
                            <span class="text-[11px] text-emerald-600 font-semibold">Active (PAR / SLA)</span>
                        </div>
                        <div class="w-9 h-9 rounded-lg bg-emerald-100/80 text-emerald-700 flex items-center justify-center border border-emerald-200 shadow-2xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                    </div>

                    {{-- Metric 3: EOL / EOS Reached --}}
                    <div class="p-3.5 rounded-xl {{ ($eolEosCount ?? 0) > 0 ? 'bg-red-50/60 border-red-200/80' : 'bg-slate-50 border-slate-200/80' }} border flex items-center justify-between">
                        <div>
                            <span class="text-[10.5px] font-bold {{ ($eolEosCount ?? 0) > 0 ? 'text-[#8F0A0D]' : 'text-slate-500' }} uppercase tracking-wider block">Siklus EOL / EOS</span>
                            <div class="text-[20px] font-extrabold {{ ($eolEosCount ?? 0) > 0 ? 'text-[#8F0A0D]' : 'text-slate-900' }} mt-0.5">{{ $eolEosCount ?? 0 }}</div>
                            <span class="text-[11px] {{ ($eolEosCount ?? 0) > 0 ? 'text-[#8F0A0D] font-bold' : 'text-slate-500' }}">
                                {{ ($eolEosCount ?? 0) > 0 ? 'Perlu Peremajaan' : 'Semua Aman' }}
                            </span>
                        </div>
                        <div class="w-9 h-9 rounded-lg {{ ($eolEosCount ?? 0) > 0 ? 'bg-red-100 text-[#8F0A0D] border border-red-200' : 'bg-white text-slate-700 border border-slate-200' }} flex items-center justify-center shadow-2xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                    </div>

                    {{-- Metric 4: Backup Perangkat --}}
                    <div class="p-3.5 rounded-xl bg-blue-50/60 border border-blue-200/80 flex items-center justify-between">
                        <div>
                            <span class="text-[10.5px] font-bold text-blue-900 uppercase tracking-wider block">Backup Config</span>
                            <div class="text-[20px] font-extrabold text-blue-800 mt-0.5">{{ $backedUpCount ?? 0 }}</div>
                            <span class="text-[11px] text-blue-700 font-semibold">Tersimpan</span>
                        </div>
                        <div class="w-9 h-9 rounded-lg bg-blue-100/80 text-blue-800 flex items-center justify-center border border-blue-200 shadow-2xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/></svg>
                        </div>
                    </div>
                </div>

                <!-- Filter Controls with Per Page Selector (5, 10, 25, 50) -->
                <form method="GET" action="{{ route('ms.assets.external') }}" class="flex flex-col md:flex-row flex-wrap items-center justify-between gap-3">
                    <div class="flex flex-col sm:flex-row flex-wrap items-center gap-2.5 w-full md:w-auto flex-1">
                        <div class="relative w-full sm:w-64">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#94A3B8]">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, serial, OS, service..." 
                                   class="w-full pl-10 pr-3 py-2 rounded-xl border border-[#CBD5E1] text-[12.5px] font-medium text-[#1E293B] bg-white outline-none hover:border-[#94A3B8] focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition-all shadow-xs">
                        </div>

                        <select name="category" onchange="this.form.submit()" class="w-full sm:w-40 px-3 py-2 rounded-xl border border-[#CBD5E1] text-[12.5px] font-semibold text-[#1E293B] bg-white outline-none hover:border-[#94A3B8] focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition-all shadow-xs cursor-pointer">
                            <option value="all">Semua Tipe</option>
                            @foreach(['Router', 'Switch', 'Firewall', 'Server', 'Access Point', 'UPS', 'Storage', 'Testing Tool', 'Fusion Splicer', 'Toolkit', 'Laptop / Device', 'Other'] as $cat)
                                <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                            @endforeach
                        </select>

                        <select name="support_status" onchange="this.form.submit()" class="w-full sm:w-40 px-3 py-2 rounded-xl border border-[#CBD5E1] text-[12.5px] font-semibold text-[#1E293B] bg-white outline-none hover:border-[#94A3B8] focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition-all shadow-xs cursor-pointer">
                            <option value="all">Semua Support</option>
                            <option value="Active" {{ request('support_status') == 'Active' ? 'selected' : '' }}>Support Active</option>
                            <option value="Expired" {{ request('support_status') == 'Expired' ? 'selected' : '' }}>Support Expired</option>
                            <option value="Pending Renewal" {{ request('support_status') == 'Pending Renewal' ? 'selected' : '' }}>Pending Renewal</option>
                        </select>

                        <select name="owner" onchange="this.form.submit()" class="w-full sm:w-44 px-3 py-2 rounded-xl border border-[#CBD5E1] text-[12.5px] font-semibold text-[#1E293B] bg-white outline-none hover:border-[#94A3B8] focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition-all shadow-xs cursor-pointer">
                            <option value="">Semua Klien</option>
                            @foreach($clients as $c)
                                <option value="{{ $c->name }}" {{ request('owner') == $c->name ? 'selected' : '' }}>{{ $c->name }}</option>
                            @endforeach
                        </select>

                        @if(request('search') || (request('category') && request('category') !== 'all') || (request('support_status') && request('support_status') !== 'all') || request('owner') || (request('per_page') && request('per_page') != 10))
                            <a href="{{ route('ms.assets.external') }}" class="px-3 py-2 text-xs font-semibold text-slate-500 hover:text-slate-800 self-center">
                                Reset Filter
                            </a>
                        @endif
                    </div>

                    {{-- Option 5, 10, 25, 50 per page --}}
                    <div class="flex items-center gap-2 self-end md:self-center shrink-0">
                        <span class="text-[12px] font-semibold text-[#64748B]">Tampilkan:</span>
                        <select name="per_page" onchange="this.form.submit()" class="px-3 py-2 rounded-xl border border-[#CBD5E1] text-[12px] font-bold text-[#1E293B] bg-white outline-none hover:border-[#94A3B8] focus:border-[#8F0A0D] cursor-pointer shadow-xs">
                            <option value="5" {{ request('per_page', $perPage ?? 10) == 5 ? 'selected' : '' }}>5 baris</option>
                            <option value="10" {{ request('per_page', $perPage ?? 10) == 10 ? 'selected' : '' }}>10 baris</option>
                            <option value="25" {{ request('per_page', $perPage ?? 10) == 25 ? 'selected' : '' }}>25 baris</option>
                            <option value="50" {{ request('per_page', $perPage ?? 10) == 50 ? 'selected' : '' }}>50 baris</option>
                        </select>
                    </div>
                </form>
            </div>

            {{-- Asset Table: Tipe Perangkat, Brand & OS, Serial Number, Service Support, Siklus EOL/EOS, Backup, Pemilik --}}
            <div class="ipnet-card overflow-hidden anim-fade-up anim-delay-2">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-[#E2E8F0] bg-[#F8FAFC] text-[11px] font-bold tracking-wider text-[#64748B] uppercase">
                                <th class="py-3.5 px-4 min-w-[120px]">Tipe Perangkat</th>
                                <th class="py-3.5 px-4 min-w-[200px]">Perangkat &amp; Brand</th>
                                <th class="py-3.5 px-4 min-w-[170px]">Serial Number &amp; OS</th>
                                <th class="py-3.5 px-4 min-w-[180px]">Service Support Vendor</th>
                                <th class="py-3.5 px-4 min-w-[150px]">Siklus EOL / EOS</th>
                                <th class="py-3.5 px-4 min-w-[150px]">Backup Config</th>
                                <th class="py-3.5 px-4 min-w-[150px]">Pemilik &amp; Lokasi</th>
                                <th class="py-3.5 px-4 text-right whitespace-nowrap">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#F1F5F9] text-[12.5px]">
                            @forelse($assets as $asset)
                                @php
                                    $isInternal = ($asset->owner_type === 'internal') || str_contains(strtolower($asset->client_name), 'ipnet') || str_contains(strtolower($asset->client_name), 'internal');
                                    $isSupportExpired = ($asset->support_expiry_date && \Carbon\Carbon::parse($asset->support_expiry_date)->isPast()) || ($asset->support_status === 'Expired');
                                    $isEol = $asset->eol_date && \Carbon\Carbon::parse($asset->eol_date)->isPast();
                                    $isEos = $asset->eos_date && \Carbon\Carbon::parse($asset->eos_date)->isPast();
                                    $isBackedUp = ($asset->backup_status === 'Backed Up');
                                @endphp
                                <tr class="hover:bg-[#F8FAFC]/80 transition-colors">
                                    {{-- 1. Tipe Perangkat --}}
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[11px] font-bold bg-[#F1F5F9] text-[#475569] border border-[#E2E8F0]">
                                            {{ $asset->category ?: 'Perangkat' }}
                                        </span>
                                        <div class="mt-1">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10.5px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                                {{ $asset->quantity ?: 1 }} Unit
                                            </span>
                                        </div>
                                    </td>

                                    {{-- 2. Perangkat & Brand --}}
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-[#1E293B] text-[13px] flex items-center gap-1.5">
                                            <span>{{ $asset->device_name }}</span>
                                            @if($asset->status === 'Online')
                                                <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block shrink-0" title="Online"></span>
                                            @elseif($asset->status === 'Warning')
                                                <span class="w-2 h-2 rounded-full bg-amber-500 inline-block shrink-0" title="Warning"></span>
                                            @else
                                                <span class="w-2 h-2 rounded-full bg-rose-500 inline-block shrink-0" title="{{ $asset->status }}"></span>
                                            @endif
                                        </div>
                                        <div class="text-[11.5px] text-[#64748B] font-medium mt-0.5">
                                            <strong>{{ $asset->brand ?: 'Brand N/A' }}</strong>
                                            @if($asset->model) &bull; {{ $asset->model }} @endif
                                        </div>
                                        @if($asset->ip_address)
                                            <div class="font-mono text-[11px] text-[#8F0A0D] font-semibold mt-0.5">
                                                IP: {{ $asset->ip_address }}
                                            </div>
                                        @endif
                                    </td>

                                    {{-- 3. Serial Number & Version OS --}}
                                    <td class="py-3.5 px-4">
                                        <div class="font-mono text-[12px] font-bold text-slate-800 tracking-tight">
                                            {{ $asset->serial_number ?: '—' }}
                                        </div>
                                        <div class="mt-1">
                                            @if($asset->os_version)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10.5px] font-bold bg-blue-50 text-blue-800 border border-blue-200" title="Version OS">
                                                    <svg class="w-3 h-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                                                    <span>{{ $asset->os_version }}</span>
                                                </span>
                                            @else
                                                <span class="text-[11px] text-slate-400 italic">OS: Belum Diisi</span>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- 4. Service Support Vendor (PAR-ND, PAR-SD, PAR-RTF, etc.) --}}
                                    <td class="py-3.5 px-4">
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-bold {{ $isSupportExpired ? 'bg-red-50 text-[#8F0A0D] border border-red-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200' }}">
                                                {{ $isSupportExpired ? 'Expired' : ($asset->support_status ?: 'Active') }}
                                            </span>
                                            @if($asset->service_support_type)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-bold bg-purple-50 text-purple-700 border border-purple-200" title="Tipe Layanan Vendor">
                                                    {{ $asset->service_support_type }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="text-[11px] text-slate-500 font-medium mt-1">
                                            @if($asset->support_expiry_date || $asset->warranty_expiry)
                                                Exp: {{ \Carbon\Carbon::parse($asset->support_expiry_date ?: $asset->warranty_expiry)->format('d M Y') }}
                                            @else
                                                <span class="text-slate-400">Garansi: N/A</span>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- 5. Siklus Hidup (EOL & EOS) --}}
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <div class="space-y-1">
                                            <div class="flex items-center gap-1 text-[11px]">
                                                <span class="font-bold {{ $isEol ? 'text-[#8F0A0D]' : 'text-slate-600' }}">EOL:</span>
                                                @if($asset->eol_date)
                                                    <span class="{{ $isEol ? 'font-bold text-[#8F0A0D]' : 'text-slate-700' }}">{{ \Carbon\Carbon::parse($asset->eol_date)->format('d M Y') }}</span>
                                                    @if($isEol) <span class="px-1.5 py-0.2 rounded text-[9.5px] font-bold bg-red-100 text-[#8F0A0D]">Passed</span> @endif
                                                @else
                                                    <span class="text-slate-400">-</span>
                                                @endif
                                            </div>
                                            <div class="flex items-center gap-1 text-[11px]">
                                                <span class="font-bold {{ $isEos ? 'text-[#8F0A0D]' : 'text-slate-600' }}">EOS:</span>
                                                @if($asset->eos_date)
                                                    <span class="{{ $isEos ? 'font-bold text-[#8F0A0D]' : 'text-slate-700' }}">{{ \Carbon\Carbon::parse($asset->eos_date)->format('d M Y') }}</span>
                                                    @if($isEos) <span class="px-1.5 py-0.2 rounded text-[9.5px] font-bold bg-red-100 text-[#8F0A0D]">Passed</span> @endif
                                                @else
                                                    <span class="text-slate-400">-</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>

                                    {{-- 6. Backup Perangkat --}}
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold {{ $isBackedUp ? 'bg-blue-50 text-blue-800 border border-blue-200' : 'bg-amber-50 text-amber-800 border border-amber-200' }}">
                                            @if($isBackedUp)
                                                <svg class="w-3 h-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                <span>Backed Up</span>
                                            @else
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                <span>{{ $asset->backup_status ?: 'Not Backed Up' }}</span>
                                            @endif
                                        </span>
                                        <div class="text-[10.5px] text-slate-500 mt-1">
                                            @if($asset->last_backup_date)
                                                Tgl: {{ \Carbon\Carbon::parse($asset->last_backup_date)->format('d M Y') }}
                                            @else
                                                Belum Pernah
                                            @endif
                                        </div>
                                    </td>

                                    {{-- 7. Pemilik & Lokasi --}}
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        @if($isInternal)
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-bold bg-rose-50 text-[#8F0A0D] border border-rose-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-[#8F0A0D]"></span>
                                                PT IPNET (Internal)
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-bold bg-blue-50 text-blue-800 border border-blue-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                                {{ $asset->client_name }}
                                            </span>
                                        @endif
                                        <div class="text-[11px] text-[#64748B] mt-1 truncate max-w-[160px]" title="{{ $asset->location_site }}">
                                            {{ $asset->location_site ?: ($asset->notes ?: '—') }}
                                        </div>
                                    </td>

                                    {{-- 8. Aksi --}}
                                    <td class="py-3.5 px-4 text-right whitespace-nowrap space-x-1">
                                        <button @click="openEditModal({{ json_encode($asset) }})" class="p-1.5 text-[#64748B] hover:text-[#1E293B] hover:bg-[#F1F5F9] rounded-lg transition cursor-pointer" title="Edit Aset">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>
                                        <button type="button" 
                                                @click="confirmDelete({{ json_encode($asset) }})"
                                                class="p-1.5 text-[#8F0A0D] hover:bg-[#FEF2F2] rounded-lg transition cursor-pointer" 
                                                title="Hapus Aset">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="py-16 text-center">
                                        <div class="max-w-sm mx-auto flex flex-col items-center justify-center">
                                            <div class="w-14 h-14 rounded-2xl bg-red-50 text-[#8F0A0D] flex items-center justify-center mb-3 border border-red-100 shadow-2xs">
                                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
                                                </svg>
                                            </div>
                                            <h4 class="text-sm font-bold text-slate-800">Belum Ada Data Aset Perangkat</h4>
                                            <p class="text-xs text-slate-500 mt-1 max-w-xs leading-relaxed">
                                                Data perangkat klien belum ada atau tidak cocok dengan filter pencarian saat ini.
                                            </p>
                                            <button type="button" @click="openCreateModal()" class="mt-4 px-4 py-2 rounded-xl bg-[#8F0A0D] hover:bg-[#73080A] text-white font-bold text-xs shadow-xs transition flex items-center gap-1.5 cursor-pointer">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                                <span>Daftarkan Aset Baru</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($assets instanceof \Illuminate\Pagination\LengthAwarePaginator && $assets->hasPages())
                    <div class="p-4 border-t border-[#E2E8F0] bg-white flex flex-col sm:flex-row items-center justify-between gap-3">
                        <div class="text-xs text-slate-500">
                            Menampilkan {{ $assets->firstItem() ?? 0 }} - {{ $assets->lastItem() ?? 0 }} dari total {{ $assets->total() }} data
                        </div>
                        <div>
                            {{ $assets->links() }}
                        </div>
                    </div>
                @endif
            </div>

        </div>
    </div>

    {{-- MODAL TAMBAH / EDIT EXTERNAL ASSET --}}
    <template x-teleport="body">
        <div x-show="isModalOpen" 
             x-cloak 
             class="fixed inset-0 z-50 bg-[#0F172A]/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-2xl w-full shadow-2xl border border-slate-200 max-h-[92vh] flex flex-col overflow-hidden" @click.away="isModalOpen = false">
                <!-- Header -->
                <div class="flex items-center justify-between border-b border-slate-200 p-5 sm:p-6 pb-4 shrink-0 bg-white">
                    <div>
                        <p class="text-[#8F0A0D] text-[11px] font-bold uppercase tracking-wider inline-flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#8F0A0D]"></span>
                            <span>Informasi Perangkat Customer &bull; Managed Service</span>
                        </p>
                        <h3 class="text-[18px] font-bold text-slate-900" x-text="isEdit ? 'Perbarui Data Aset Perangkat' : 'Tambah Aset Perangkat Baru'"></h3>
                        <p class="text-xs text-slate-500 mt-0.5">Kelola tipe perangkat, OS, status support (PAR-ND/SD/RTF), EOL/EOS, &amp; backup.</p>
                    </div>
                    <button @click="isModalOpen = false" class="text-slate-400 hover:text-slate-700 p-1.5 rounded-lg hover:bg-slate-100 transition cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Form -->
                <form :action="isEdit ? '/managed-service/assets/' + form.id : '{{ route('ms.assets.store') }}'" method="POST" class="flex flex-col flex-1 min-h-0 overflow-hidden">
                    @csrf
                    <template x-if="isEdit">
                        <input type="hidden" name="_method" value="PUT">
                    </template>
                    <input type="hidden" name="owner_type" value="client">

                    <div class="p-5 sm:p-6 overflow-y-auto space-y-5 text-[13px] flex-1">
                        
                        {{-- SECTION 1: IDENTITAS & BRAND PERANGKAT --}}
                        <div class="space-y-3.5">
                            <div class="flex items-center gap-2 pb-1 border-b border-slate-100 text-xs font-bold text-[#8F0A0D] uppercase tracking-wider">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/></svg>
                                <span>1. Identitas &amp; Tipe Perangkat</span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                {{-- Pemilik / Klien --}}
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                        Pemilik (Klien) <span class="text-[#8F0A0D]">*</span>
                                    </label>
                                    <input type="text" name="client_name" x-model="form.client_name" required placeholder="Contoh: Bank Raya, Telkom, PT APS"
                                           class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition shadow-2xs">
                                </div>

                                {{-- Tipe Perangkat --}}
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                        Tipe Perangkat <span class="text-[#8F0A0D]">*</span>
                                    </label>
                                    <select name="category" x-model="form.category" required 
                                            class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-sm font-medium text-slate-800 focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition shadow-2xs cursor-pointer">
                                        <option value="Router">Router</option>
                                        <option value="Switch">Switch</option>
                                        <option value="Firewall">Firewall</option>
                                        <option value="Server">Server</option>
                                        <option value="Access Point">Access Point</option>
                                        <option value="UPS">UPS</option>
                                        <option value="Storage">Storage</option>
                                        <option value="Testing Tool">Testing Tool (OTDR / OPM)</option>
                                        <option value="Fusion Splicer">Fusion Splicer</option>
                                        <option value="Toolkit">Toolkit Engineer</option>
                                        <option value="Laptop / Device">Laptop / Device</option>
                                        <option value="Other">Lainnya</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                                {{-- Brand --}}
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                        Brand
                                    </label>
                                    <input type="text" name="brand" x-model="form.brand" placeholder="Cisco, Fortinet, Ruijie..."
                                           class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition shadow-2xs">
                                </div>

                                {{-- Nama Produk / Device Name --}}
                                <div class="sm:col-span-2">
                                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                        Nama Perangkat / Model <span class="text-[#8F0A0D]">*</span>
                                    </label>
                                    <input type="text" name="device_name" x-model="form.device_name" required placeholder="Contoh: Catalyst 9200L-24P-4G atau FortiGate 100F"
                                           class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition shadow-2xs">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                                {{-- Serial Number --}}
                                <div class="sm:col-span-2">
                                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                        Serial Number
                                    </label>
                                    <input type="text" name="serial_number" x-model="form.serial_number" placeholder="Contoh: FOC223419LK / FGT100F123456"
                                           class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-sm font-mono text-slate-800 placeholder:text-slate-400 focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition shadow-2xs">
                                </div>

                                {{-- Quantity --}}
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                        Quantity
                                    </label>
                                    <input type="number" name="quantity" x-model="form.quantity" min="1" placeholder="1"
                                           class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-sm font-bold text-slate-800 focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition shadow-2xs">
                                </div>
                            </div>
                        </div>

                        {{-- SECTION 2: SYSTEM & JARINGAN (VERSION OS, IP, STATUS) --}}
                        <div class="space-y-3.5 pt-2">
                            <div class="flex items-center gap-2 pb-1 border-b border-slate-100 text-xs font-bold text-[#8F0A0D] uppercase tracking-wider">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                                <span>2. Sistem Operasi &amp; Jaringan</span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                {{-- Version OS --}}
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                        Version OS
                                    </label>
                                    <input type="text" name="os_version" x-model="form.os_version" placeholder="Contoh: Cisco IOS XE 17.6.4, FortiOS 7.2.5"
                                           class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-sm font-medium text-slate-800 placeholder:text-slate-400 focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition shadow-2xs">
                                </div>

                                {{-- IP Address --}}
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                        IP Address Manajemen
                                    </label>
                                    <input type="text" name="ip_address" x-model="form.ip_address" placeholder="Contoh: 192.168.10.1"
                                           class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-sm font-mono text-slate-800 placeholder:text-slate-400 focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition shadow-2xs">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                {{-- Lokasi Site / Rack --}}
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                        Lokasi Site / Rak
                                    </label>
                                    <input type="text" name="location_site" x-model="form.location_site" placeholder="Contoh: DC Cyber Lantai 2, Rack 04 - U18"
                                           class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition shadow-2xs">
                                </div>

                                {{-- Status Operasional --}}
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                        Status Operasional
                                    </label>
                                    <select name="status" x-model="form.status"
                                            class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition shadow-2xs cursor-pointer">
                                        <option value="Online">Online / Normal</option>
                                        <option value="Warning">Warning / Issue</option>
                                        <option value="Offline">Offline</option>
                                        <option value="Maintenance">Maintenance</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- SECTION 3: DUKUNGAN VENDOR & SIKLUS HIDUP (SUPPORT, EOL, EOS) --}}
                        <div class="space-y-3.5 pt-2">
                            <div class="flex items-center gap-2 pb-1 border-b border-slate-100 text-xs font-bold text-[#8F0A0D] uppercase tracking-wider">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                <span>3. Dukungan Vendor &amp; Siklus Hidup (EOL / EOS)</span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                                {{-- Service Support Type (PAR-ND, PAR-SD, PAR-RTF, etc.) --}}
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                        Service Support
                                    </label>
                                    <input type="text" name="service_support_type" x-model="form.service_support_type" list="serviceSupportList" placeholder="PAR-ND, PAR-SD, PAR-RTF..."
                                           class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 placeholder:text-slate-400 focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition shadow-2xs">
                                    <datalist id="serviceSupportList">
                                        <option value="PAR-ND">PAR-ND (Next Day Replacement)</option>
                                        <option value="PAR-SD">PAR-SD (Same Day 4h Onsite)</option>
                                        <option value="PAR-RTF">PAR-RTF (Return to Factory)</option>
                                        <option value="24x7 4-Hour Onsite">24x7 4-Hour Onsite SLA</option>
                                        <option value="8x5 NBD">8x5 Next Business Day</option>
                                        <option value="Vendor Warranty">Standard Vendor Warranty</option>
                                    </datalist>
                                </div>

                                {{-- Status Support --}}
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                        Status Support
                                    </label>
                                    <select name="support_status" x-model="form.support_status"
                                            class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition shadow-2xs cursor-pointer">
                                        <option value="Active">Active (Berlaku)</option>
                                        <option value="Expired">Expired (Habis)</option>
                                        <option value="Pending Renewal">Pending Renewal</option>
                                    </select>
                                </div>

                                {{-- Tanggal Expired Support --}}
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                        Tgl Expired Support
                                    </label>
                                    <input type="date" name="support_expiry_date" x-model="form.support_expiry_date"
                                           class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition shadow-2xs">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                {{-- EOL Date --}}
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                        Tanggal EOL (End of Life)
                                    </label>
                                    <input type="date" name="eol_date" x-model="form.eol_date"
                                           class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition shadow-2xs">
                                </div>

                                {{-- EOS Date --}}
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                        Tanggal EOS (End of Support)
                                    </label>
                                    <input type="date" name="eos_date" x-model="form.eos_date"
                                           class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition shadow-2xs">
                                </div>
                            </div>
                        </div>

                        {{-- SECTION 4: BACKUP KONFIGURASI PERANGKAT --}}
                        <div class="space-y-3.5 pt-2">
                            <div class="flex items-center gap-2 pb-1 border-b border-slate-100 text-xs font-bold text-[#8F0A0D] uppercase tracking-wider">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/></svg>
                                <span>4. Backup Konfigurasi Perangkat</span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                {{-- Status Backup --}}
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                        Status Backup
                                    </label>
                                    <select name="backup_status" x-model="form.backup_status"
                                            class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition shadow-2xs cursor-pointer">
                                        <option value="Backed Up">Backed Up (Sudah Dibackup)</option>
                                        <option value="Not Backed Up">Not Backed Up (Belum)</option>
                                        <option value="Scheduled">Scheduled (Terjadwal)</option>
                                    </select>
                                </div>

                                {{-- Tanggal Terakhir Backup --}}
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                        Tanggal Terakhir Backup
                                    </label>
                                    <input type="date" name="last_backup_date" x-model="form.last_backup_date"
                                           class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition shadow-2xs">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                    Catatan / Lokasi Backup Config
                                </label>
                                <input type="text" name="backup_notes" x-model="form.backup_notes" placeholder="Contoh: TFTP Server HQ / Storage Backup DC-01"
                                       class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition shadow-2xs">
                            </div>
                        </div>

                        {{-- Catatan Umum --}}
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Catatan Tambahan
                            </label>
                            <textarea name="notes" x-model="form.notes" rows="2" placeholder="Catatan kondisi perangkat, port, atau riwayat perbaikan..."
                                      class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition shadow-2xs"></textarea>
                        </div>

                    </div>

                    <!-- Footer Action -->
                    <div class="border-t border-slate-200 p-4 sm:p-5 flex items-center justify-end gap-3 bg-slate-50 shrink-0">
                        <button type="button" @click="isModalOpen = false" class="px-4 py-2.5 rounded-xl border border-slate-300 text-slate-700 hover:text-slate-900 font-semibold text-sm hover:bg-white transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#8F0A0D] hover:bg-[#73080A] text-white font-semibold text-sm transition shadow-sm cursor-pointer">
                            <span x-text="isEdit ? 'Simpan Perubahan' : 'Daftarkan Aset'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>

    {{-- DELETE CONFIRMATION MODAL --}}
    <template x-teleport="body">
        <div x-show="isDeleteModalOpen" 
             x-cloak 
             class="fixed inset-0 z-50 bg-[#0F172A]/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-2xl border border-slate-200 text-center" @click.away="isDeleteModalOpen = false">
                <div class="w-12 h-12 rounded-full bg-rose-50 text-[#8F0A0D] flex items-center justify-center mx-auto mb-4 border border-rose-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <h3 class="text-[16px] font-bold text-slate-900">Hapus Data Aset?</h3>
                <p class="text-[12.5px] text-slate-600 mt-1.5 leading-relaxed">
                    Data perangkat <strong class="text-slate-900" x-text="assetToDelete?.device_name"></strong> akan dihapus permanen.
                </p>
                <div class="mt-6 flex items-center justify-center gap-3">
                    <button type="button" @click="isDeleteModalOpen = false" class="px-4 py-2 rounded-xl border border-slate-300 text-slate-700 font-semibold text-[12.5px] hover:bg-slate-50 transition cursor-pointer">
                        Batal
                    </button>
                    <form :action="'/managed-service/assets/' + assetToDelete?.id" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 rounded-xl bg-[#8F0A0D] hover:bg-[#73080A] text-white font-semibold text-[12.5px] transition cursor-pointer shadow-sm">
                            Ya, Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </template>
</div>
@endsection

@push('scripts')
<script>
    function assetManager() {
        return {
            isModalOpen: false,
            isDeleteModalOpen: false,
            isEdit: false,
            assetToDelete: null,
            form: {
                id: null,
                client_name: '',
                owner_type_choice: 'client',
                device_name: '',
                product_description: '',
                category: 'Switch',
                brand: '',
                model: '',
                os_version: '',
                quantity: 1,
                serial_number: '',
                ip_address: '',
                location_site: '',
                rack_position: '',
                status: 'Online',
                service_support_type: 'PAR-ND',
                support_status: 'Active',
                support_expiry_date: '',
                eol_date: '',
                eos_date: '',
                backup_status: 'Not Backed Up',
                last_backup_date: '',
                backup_notes: '',
                notes: '',
                project_id: null,
            },
            openCreateModal() {
                this.isEdit = false;
                this.form = {
                    id: null,
                    client_name: '',
                    owner_type_choice: 'client',
                    device_name: '',
                    product_description: '',
                    category: 'Switch',
                    brand: '',
                    model: '',
                    os_version: '',
                    quantity: 1,
                    serial_number: '',
                    ip_address: '',
                    location_site: '',
                    rack_position: '',
                    status: 'Online',
                    service_support_type: 'PAR-ND',
                    support_status: 'Active',
                    support_expiry_date: '',
                    eol_date: '',
                    eos_date: '',
                    backup_status: 'Not Backed Up',
                    last_backup_date: '',
                    backup_notes: '',
                    notes: '',
                    project_id: null,
                };
                this.isModalOpen = true;
            },
            openEditModal(asset) {
                this.isEdit = true;
                const isInternal = (asset.owner_type === 'internal') || 
                                   (asset.client_name && (asset.client_name.toLowerCase().includes('ipnet') || asset.client_name.toLowerCase().includes('internal')));
                
                const fmtDate = (d) => {
                    if (!d) return '';
                    if (typeof d === 'string') return d.substring(0, 10);
                    return '';
                };

                this.form = {
                    id: asset.id,
                    client_name: asset.client_name || '',
                    owner_type_choice: isInternal ? 'internal' : 'client',
                    device_name: asset.device_name || '',
                    product_description: asset.product_description || '',
                    category: asset.category || 'Switch',
                    brand: asset.brand || '',
                    model: asset.model || '',
                    os_version: asset.os_version || '',
                    quantity: asset.quantity || 1,
                    serial_number: asset.serial_number || '',
                    ip_address: asset.ip_address || '',
                    location_site: asset.location_site || '',
                    rack_position: asset.rack_position || '',
                    status: asset.status || 'Online',
                    service_support_type: asset.service_support_type || 'PAR-ND',
                    support_status: asset.support_status || 'Active',
                    support_expiry_date: fmtDate(asset.support_expiry_date || asset.warranty_expiry),
                    eol_date: fmtDate(asset.eol_date),
                    eos_date: fmtDate(asset.eos_date),
                    backup_status: asset.backup_status || 'Not Backed Up',
                    last_backup_date: fmtDate(asset.last_backup_date),
                    backup_notes: asset.backup_notes || '',
                    notes: asset.notes || '',
                    project_id: asset.project_id || null,
                };
                this.isModalOpen = true;
            },
            confirmDelete(asset) {
                this.assetToDelete = asset;
                this.isDeleteModalOpen = true;
            }
        };
    }
</script>
@endpush
