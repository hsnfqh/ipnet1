@extends('layouts.app')

@section('title', 'Internal Asset - Managed Service')

@push('styles')
<style>
    [x-cloak] { display: none !important; }

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
<div class="flex h-screen overflow-hidden bg-[#F8FAFC] font-sans" x-data="{ selectedItem: null, detailModalOpen: false }" x-cloak>
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
                </div>
            @endif

            {{-- Top Action Bar with Segmented Button Toggle --}}
            <div class="ipnet-card p-5 sm:p-6 flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4 anim-fade-up anim-delay-1">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-[#8F0A0D] inline-block"></span>
                        <p class="text-[#8F0A0D] text-[12px] font-bold uppercase tracking-wider">
                            MANAJEMEN ASET &bull; INTEGRASI WAREHOUSE
                        </p>
                    </div>
                    <h3 class="font-bold text-[#1E293B] text-[20px] tracking-tight mt-0.5">Aset Internal Perusahaan</h3>
                    <p class="text-[13px] text-[#64748B] mt-0.5">
                        Daftar perangkat jaringan, suku cadang, dan inventaris operasional terhubung langsung dari Warehouse IPNET.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-3 shrink-0">
                    {{-- Toggle Button Internal vs External --}}
                    <div class="inline-flex p-1 bg-[#F1F5F9] rounded-xl border border-[#E2E8F0] shadow-2xs">
                        <a href="{{ route('ms.assets.internal') }}" 
                           class="px-4 py-2 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 bg-[#8F0A0D] text-white shadow-xs">
                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            <span>Internal Asset</span>
                        </a>
                        <a href="{{ route('ms.assets.external') }}" 
                           class="px-4 py-2 rounded-lg text-xs font-semibold transition-all flex items-center gap-1.5 text-[#64748B] hover:text-[#1E293B] hover:bg-white/60">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/></svg>
                            <span>External Asset</span>
                        </a>
                    </div>

                    <a href="{{ route('warehouse.products.index') }}" 
                       class="btn-ipnet-gradient px-4 py-2 rounded-xl text-xs font-bold flex items-center gap-2 shadow-xs transition cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        <span>Kelola di Warehouse</span>
                    </a>

                    <span class="inline-flex items-center px-3.5 py-2 rounded-xl text-xs font-bold bg-[#F8FAFC] text-[#1E293B] border border-[#E2E8F0] shadow-xs">
                        Total Aset: <strong class="text-[#8F0A0D] ml-1">{{ $assets->total() }}</strong>
                    </span>
                </div>
            </div>

            {{-- Filter & Per-Page Controls --}}
            <div class="ipnet-card p-4 anim-fade-up anim-delay-2">
                <form method="GET" action="{{ route('ms.assets.internal') }}" class="flex flex-col md:flex-row flex-wrap items-center justify-between gap-3">
                    <div class="flex flex-col sm:flex-row flex-wrap items-center gap-2.5 w-full md:w-auto flex-1">
                        <div class="relative w-full sm:w-64">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#94A3B8]">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                            <input type="text" 
                                   name="search" 
                                   value="{{ request('search') }}"
                                   placeholder="Cari model, brand, P/N, S/N, AM..." 
                                   class="w-full pl-10 pr-3 py-2 rounded-xl border border-[#CBD5E1] text-[12.5px] font-medium text-[#1E293B] bg-white outline-none hover:border-[#94A3B8] focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition-all shadow-xs">
                        </div>

                        {{-- Filter Jenis Perangkat --}}
                        <select name="item_type" onchange="this.form.submit()" 
                                class="w-full sm:w-44 px-3 py-2 rounded-xl border border-[#CBD5E1] text-[12.5px] font-semibold text-[#1E293B] bg-white outline-none hover:border-[#94A3B8] focus:border-[#8F0A0D] cursor-pointer shadow-xs">
                            <option value="all">Semua Jenis Perangkat</option>
                            @foreach($itemTypes ?? [] as $t)
                                <option value="{{ $t }}" {{ request('item_type') == $t ? 'selected' : '' }}>{{ $t }}</option>
                            @endforeach
                        </select>

                        {{-- Filter Kategori Kepemilikan --}}
                        <select name="category" onchange="this.form.submit()" 
                                class="w-full sm:w-40 px-3 py-2 rounded-xl border border-[#CBD5E1] text-[12.5px] font-semibold text-[#1E293B] bg-white outline-none hover:border-[#94A3B8] focus:border-[#8F0A0D] cursor-pointer shadow-xs">
                            <option value="all">Semua Kategori</option>
                            @foreach($categories ?? [] as $c)
                                <option value="{{ $c }}" {{ request('category') == $c ? 'selected' : '' }}>{{ $c }}</option>
                            @endforeach
                        </select>

                        {{-- Filter Status --}}
                        <select name="status" onchange="this.form.submit()" 
                                class="w-full sm:w-40 px-3 py-2 rounded-xl border border-[#CBD5E1] text-[12.5px] font-semibold text-[#1E293B] bg-white outline-none hover:border-[#94A3B8] focus:border-[#8F0A0D] cursor-pointer shadow-xs">
                            <option value="all">Semua Status</option>
                            @foreach($statuses ?? [] as $st)
                                <option value="{{ $st }}" {{ request('status') == $st ? 'selected' : '' }}>{{ $st }}</option>
                            @endforeach
                        </select>

                        @if(request('search') || (request('category') && request('category') !== 'all') || (request('item_type') && request('item_type') !== 'all') || (request('status') && request('status') !== 'all') || (request('per_page') && request('per_page') != 10))
                            <a href="{{ route('ms.assets.internal') }}" class="px-3 py-2 text-xs font-semibold text-slate-500 hover:text-slate-800 self-center">
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

            {{-- Table Aset Internal Sesuai Kolom Warehouse --}}
            <div class="ipnet-card overflow-hidden anim-fade-up anim-delay-3">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-[#E2E8F0] bg-[#F8FAFC] text-[11px] font-bold tracking-wider text-[#64748B] uppercase">
                                <th class="py-3.5 px-4">Perangkat &amp; Model</th>
                                <th class="py-3.5 px-4">Jenis &amp; Komponen</th>
                                <th class="py-3.5 px-4">P/N &amp; S/N</th>
                                <th class="py-3.5 px-3 text-center">Qty</th>
                                <th class="py-3.5 px-4">Kategori Kepemilikan</th>
                                <th class="py-3.5 px-4">Account Manager (AM)</th>
                                <th class="py-3.5 px-4">Lokasi &amp; Rak</th>
                                <th class="py-3.5 px-4">Status</th>
                                <th class="py-3.5 px-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#F1F5F9] text-[12.5px]">
                            @forelse($assets as $item)
                                <tr class="hover:bg-[#F8FAFC]/80 transition-colors">
                                    {{-- 1. Perangkat & Model --}}
                                    <td class="py-3.5 px-4">
                                        <div class="min-w-0">
                                            <div class="flex items-center gap-1.5 flex-wrap">
                                                <span class="text-[10.5px] font-mono px-1.5 py-0.5 rounded bg-slate-100 text-slate-700 font-semibold border border-slate-200">
                                                    {{ $item->item_code }}
                                                </span>
                                                @if($item->brand)
                                                    <span class="text-[10px] uppercase font-bold text-[#8F0A0D] bg-red-50 border border-red-200/60 px-1.5 py-0.2 rounded">
                                                        {{ $item->brand }}
                                                    </span>
                                                @endif
                                            </div>
                                            <p class="font-bold text-[#1E293B] text-[13px] mt-1 line-clamp-1">
                                                {{ $item->name }}
                                            </p>
                                            @if($item->po_number || $item->contract_number)
                                                <p class="text-[11px] text-slate-400 font-normal mt-0.5 font-mono">
                                                    {{ $item->po_number ? 'PO: ' . $item->po_number : '' }}
                                                    {{ $item->po_number && $item->contract_number ? ' • ' : '' }}
                                                    {{ $item->contract_number ? 'CTR: ' . $item->contract_number : '' }}
                                                </p>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- 2. Jenis & Komponen --}}
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <p class="font-semibold text-slate-700 text-xs">{{ $item->item_type }}</p>
                                        <p class="text-[11px] text-slate-400 font-normal mt-0.5">
                                            {{ $item->component_type }}
                                            @if($item->cable_type)
                                                <span class="text-slate-500 font-medium">({{ $item->cable_type }})</span>
                                            @endif
                                        </p>
                                    </td>

                                    {{-- 3. P/N & S/N --}}
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <p class="font-mono text-xs text-slate-700 font-medium">
                                            <span class="text-slate-400 font-sans text-[10.5px]">S/N:</span> {{ $item->serial_number ?: '-' }}
                                        </p>
                                        <p class="font-mono text-[11px] text-slate-500 mt-0.5">
                                            <span class="text-slate-400 font-sans text-[10px]">P/N:</span> {{ $item->product_number ?: '-' }}
                                        </p>
                                    </td>

                                    {{-- 4. Quantity --}}
                                    <td class="py-3.5 px-3 text-center whitespace-nowrap">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11.5px] font-extrabold bg-slate-100 text-slate-800 border border-slate-200">
                                            1 Unit
                                        </span>
                                        @if(isset($modelStockCounts[$item->name]) && $modelStockCounts[$item->name] > 1)
                                            <div class="text-[10px] text-slate-400 font-medium mt-0.5" title="Total stok fisik model ini di gudang">
                                                Total Model: {{ $modelStockCounts[$item->name] }}
                                            </div>
                                        @endif
                                    </td>

                                    {{-- 5. Kategori Kepemilikan --}}
                                    <td class="py-3.5 px-4">
                                        @if($item->category === 'Stock')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                                Stock
                                            </span>
                                        @elseif($item->category === 'Backup / Spare')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200/80">
                                                Backup / Spare
                                            </span>
                                        @else
                                            <div class="space-y-0.5">
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-purple-50 text-purple-700 border border-purple-200/80">
                                                    Pinjaman: {{ $item->loan_source_type === 'disti' ? 'Distributor' : 'Internal IPNET' }}
                                                </span>
                                                @if($item->loan_source_type === 'disti' && $item->disti_name)
                                                    <p class="text-[11px] text-slate-500 truncate max-w-xs">{{ $item->disti_name }}</p>
                                                @elseif($item->loan_source_type === 'ipnet')
                                                    <p class="text-[11px] text-slate-500">{{ $item->ipnet_work_unit ?: 'IPNET' }} &bull; {{ $item->ipnet_pic_name ?: '-' }}</p>
                                                @endif
                                            </div>
                                        @endif
                                    </td>

                                    {{-- 6. Account Manager (AM) --}}
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        @php
                                            $amName = $item->customer_sales_name ?: ($item->poc_sales_name ?: null);
                                        @endphp
                                        @if($amName)
                                            <div class="flex items-center gap-1.5">
                                                <span class="w-1.5 h-1.5 rounded-full bg-[#8F0A0D]"></span>
                                                <span class="font-bold text-[#1E293B] text-xs">{{ $amName }}</span>
                                            </div>
                                            <span class="text-[10px] text-slate-400 font-medium">Sales / AM</span>
                                        @else
                                            <span class="text-slate-400 text-xs">—</span>
                                        @endif
                                    </td>

                                    {{-- 7. Lokasi & Rak --}}
                                    <td class="py-3.5 px-4">
                                        @if($item->location_type === 'Gudang IPNET' || $item->location_type === 'Warehouse IPNET')
                                            <p class="font-semibold text-slate-700 text-xs">Gudang IPNET</p>
                                            <p class="text-[11px] text-slate-400 mt-0.5">{{ $item->warehouse_rack ?: 'Gudang Utama' }}</p>
                                        @else
                                            <p class="font-semibold text-slate-800 text-xs">Lokasi Customer</p>
                                            <p class="text-[11px] text-slate-500 mt-0.5 line-clamp-1" title="{{ $item->customer_address }}">
                                                {{ $item->customer_address ?: '-' }}
                                            </p>
                                        @endif
                                    </td>

                                    {{-- 8. Status Operasional --}}
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        @if($item->status === 'Tersedia')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                Tersedia
                                            </span>
                                        @elseif($item->status === 'POC / Pinjaman')
                                            <div>
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                                    Sedang POC
                                                </span>
                                                @if($item->poc_customer_name)
                                                    <p class="text-[11px] text-slate-600 font-medium mt-1">{{ $item->poc_customer_name }}</p>
                                                @endif
                                            </div>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                                {{ $item->status }}
                                            </span>
                                        @endif
                                    </td>

                                    {{-- 9. Aksi Detail --}}
                                    <td class="py-3.5 px-3 text-center whitespace-nowrap">
                                        <button type="button" 
                                                @click="selectedItem = {{ json_encode($item) }}; detailModalOpen = true;" 
                                                class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-[#8F0A0D] hover:text-white text-slate-700 text-xs font-bold transition flex items-center gap-1 cursor-pointer shadow-2xs">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            <span>Detail</span>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="py-12 text-center text-[#64748B] text-[13px]">
                                        <div class="max-w-sm mx-auto space-y-2">
                                            <svg class="w-10 h-10 text-slate-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                            <p class="font-bold text-slate-700">Belum Ada Perangkat di Warehouse</p>
                                            <p class="text-xs text-slate-400">Seluruh perangkat yang didaftarkan melalui Warehouse IPNET akan otomatis tampil dan terhubung di sini.</p>
                                            <a href="{{ route('warehouse.products.index') }}" class="inline-block mt-2 btn-ipnet-gradient px-4 py-1.5 rounded-xl text-xs font-bold shadow-xs">
                                                Buka Warehouse
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($assets->hasPages())
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

    {{-- MODAL DETAIL PERANGKAT LENGKAP --}}
    <template x-teleport="body">
        <div x-show="detailModalOpen" 
             x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-[#0F172A]/60 z-[99999] flex items-center justify-center p-3 sm:p-5 backdrop-blur-xs"
             @click.self="detailModalOpen = false"
             @keydown.escape.window="detailModalOpen = false">
            
            <div class="bg-white rounded-2xl w-[660px] max-w-full max-h-[90vh] flex flex-col text-left shadow-[0_20px_60px_rgba(15,23,42,0.25)] border border-[#E2E8F0] overflow-hidden">
                
                {{-- Fixed Header --}}
                <div class="px-6 py-4 bg-[#F8FAFC] border-b border-[#E2E8F0] flex items-start justify-between shrink-0">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-[11px] font-mono px-2 py-0.5 rounded bg-slate-100 text-slate-700 font-bold border border-slate-200" x-text="selectedItem?.item_code"></span>
                            <span class="text-[11px] font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200/60" x-text="selectedItem?.status"></span>
                        </div>
                        <h3 class="text-base font-bold text-slate-900 leading-snug" x-text="selectedItem?.name"></h3>
                        <p class="text-xs text-slate-500 mt-0.5" x-text="'Merk: ' + (selectedItem?.brand || '-') + ' • Jenis: ' + (selectedItem?.item_type || '-')"></p>
                    </div>
                    <button type="button" @click="detailModalOpen = false" class="text-slate-400 hover:text-slate-600 cursor-pointer p-1 rounded-lg hover:bg-slate-100 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Scrollable Body --}}
                <div class="p-6 overflow-y-auto space-y-4 text-xs flex-1 min-h-0">
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                            <p class="text-[10.5px] font-semibold text-slate-400 uppercase">Kuantitas (Qty)</p>
                            <p class="font-bold text-slate-800 text-[12px] mt-0.5">1 Unit Fisik</p>
                        </div>
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                            <p class="text-[10.5px] font-semibold text-slate-400 uppercase">Serial Number (S/N)</p>
                            <p class="font-mono font-bold text-slate-800 text-[12px] mt-0.5" x-text="selectedItem?.serial_number || '-'"></p>
                        </div>
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                            <p class="text-[10.5px] font-semibold text-slate-400 uppercase">Part Number (P/N)</p>
                            <p class="font-mono font-bold text-slate-800 text-[12px] mt-0.5" x-text="selectedItem?.product_number || '-'"></p>
                        </div>
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                            <p class="text-[10.5px] font-semibold text-slate-400 uppercase">Komponen / Kabel</p>
                            <p class="font-bold text-slate-800 mt-0.5" x-text="(selectedItem?.component_type || '-') + (selectedItem?.cable_type ? ' (' + selectedItem.cable_type + ')' : '')"></p>
                        </div>
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                            <p class="text-[10.5px] font-semibold text-slate-400 uppercase">Nomor PO</p>
                            <p class="font-semibold text-slate-800 mt-0.5" x-text="selectedItem?.po_number || '-'"></p>
                        </div>
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                            <p class="text-[10.5px] font-semibold text-slate-400 uppercase">Nomor Kontrak</p>
                            <p class="font-semibold text-slate-800 mt-0.5" x-text="selectedItem?.contract_number || '-'"></p>
                        </div>
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                            <p class="text-[10.5px] font-semibold text-slate-400 uppercase">Kategori</p>
                            <p class="font-semibold text-slate-800 mt-0.5" x-text="selectedItem?.category || '-'"></p>
                        </div>
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                            <p class="text-[10.5px] font-semibold text-slate-400 uppercase">Account Manager (AM)</p>
                            <p class="font-semibold text-[#8F0A0D] mt-0.5" x-text="selectedItem?.customer_sales_name || selectedItem?.poc_sales_name || '-'"></p>
                        </div>
                    </div>

                    {{-- Detail Sumber Pinjaman jika Pinjaman --}}
                    <template x-if="selectedItem?.category === 'Pinjaman'">
                        <div class="p-3.5 rounded-xl bg-purple-50/70 border border-purple-200/80 space-y-2">
                            <div class="flex items-center justify-between">
                                <p class="font-bold text-purple-900 text-[11px] uppercase tracking-wider">
                                    Sumber Pinjaman: <span x-text="selectedItem?.loan_source_type === 'disti' ? 'Distributor Vendor' : 'Internal IPNET'"></span>
                                </p>
                                <span class="text-[11px] font-semibold text-purple-700 bg-purple-100/70 px-2 py-0.5 rounded" x-text="'Durasi: ' + (selectedItem?.loan_source_type === 'disti' ? selectedItem?.disti_duration : selectedItem?.ipnet_duration || '-')"></span>
                            </div>
                            <div class="grid grid-cols-2 gap-2 text-slate-700 pt-1">
                                <template x-if="selectedItem?.loan_source_type === 'disti'">
                                    <div class="col-span-2 space-y-1">
                                        <p><strong class="text-purple-900">Perusahaan:</strong> <span x-text="selectedItem?.disti_name || '-'"></span></p>
                                        <p><strong class="text-purple-900">PIC Distributor:</strong> <span x-text="selectedItem?.disti_pic || '-'"></span> (<span x-text="selectedItem?.disti_phone || '-'"></span>)</p>
                                        <p><strong class="text-purple-900">Alamat:</strong> <span x-text="selectedItem?.disti_address || '-'"></span></p>
                                    </div>
                                </template>
                                <template x-if="selectedItem?.loan_source_type === 'ipnet'">
                                    <div class="col-span-2 space-y-1">
                                        <p><strong class="text-purple-900">Unit Kerja:</strong> <span class="font-bold text-[#8F0A0D]" x-text="selectedItem?.ipnet_work_unit || '-'"></span></p>
                                        <p><strong class="text-purple-900">PIC IPNET:</strong> <span x-text="selectedItem?.ipnet_pic_name || '-'"></span> (<span x-text="selectedItem?.ipnet_phone || '-'"></span>)</p>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>

                    {{-- Lokasi & Penempatan --}}
                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100 space-y-2">
                        <p class="font-bold text-slate-800 text-[11px] uppercase tracking-wider">Posisi Fisik Perangkat</p>
                        <div class="grid grid-cols-2 gap-2 text-slate-700">
                            <div>
                                <p class="text-slate-400 text-[10.5px]">Titik Lokasi:</p>
                                <p class="font-bold text-slate-800 mt-0.5" x-text="selectedItem?.location_type || '-'"></p>
                            </div>
                            <div>
                                <p class="text-slate-400 text-[10.5px]">Posisi Rak Gudang:</p>
                                <p class="font-semibold text-slate-800 mt-0.5" x-text="selectedItem?.warehouse_rack || '-'"></p>
                            </div>
                            <template x-if="selectedItem?.location_type === 'Customer'">
                                <div class="col-span-2 pt-1 border-t border-slate-200/60 space-y-1">
                                    <p><strong class="text-slate-800">PIC Customer:</strong> <span x-text="selectedItem?.customer_pic || '-'"></span> (<span x-text="selectedItem?.customer_phone || '-'"></span>)</p>
                                    <p><strong class="text-slate-800">AM / Sales:</strong> <span x-text="selectedItem?.customer_sales_name || '-'"></span></p>
                                    <p><strong class="text-slate-800">Alamat Klien:</strong> <span x-text="selectedItem?.customer_address || '-'"></span></p>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- Detail jika sedang POC --}}
                    <template x-if="selectedItem?.status === 'POC / Pinjaman'">
                        <div class="p-3.5 rounded-xl bg-blue-50/70 border border-blue-200/80 space-y-2">
                            <p class="font-bold text-blue-900 text-[11px] uppercase tracking-wider">Peminjaman Uji Coba (POC)</p>
                            <div class="space-y-1 text-slate-700">
                                <p><strong class="text-blue-950">Nama Pelanggan POC:</strong> <span class="font-bold text-blue-900" x-text="selectedItem?.poc_customer_name || '-'"></span></p>
                                <p><strong class="text-blue-950">Durasi:</strong> <span x-text="selectedItem?.poc_duration || '-'"></span></p>
                                <p><strong class="text-blue-950">PIC Pelanggan:</strong> <span x-text="selectedItem?.poc_pic_name || '-'"></span> (<span x-text="selectedItem?.poc_pic_phone || '-'"></span>)</p>
                                <p><strong class="text-blue-950">Sales Penanggung Jawab:</strong> <span x-text="selectedItem?.poc_sales_name || '-'"></span></p>
                            </div>
                        </div>
                    </template>

                    {{-- Catatan --}}
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <p class="text-[10.5px] font-semibold text-slate-400 uppercase">Catatan Tambahan</p>
                        <p class="text-slate-700 mt-1 whitespace-pre-line" x-text="selectedItem?.notes || 'Tidak ada catatan khusus.'"></p>
                    </div>
                </div>

                {{-- Fixed Footer --}}
                <div class="px-6 py-3 bg-[#F8FAFC] border-t border-[#E2E8F0] flex items-center justify-between shrink-0">
                    <span class="text-[11px] text-slate-400">Terdaftar di sistem Warehouse IPNET</span>
                    <button type="button" @click="detailModalOpen = false" class="py-2 px-4 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold text-xs transition cursor-pointer">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>
@endsection
