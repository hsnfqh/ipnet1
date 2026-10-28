@extends('layouts.app')

@section('title', 'Master Produk & Perangkat - Warehouse IPNET')

@push('styles')
<style>
    [x-cloak] { display: none !important; }

    .ipnet-card {
        background-color: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 18px;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.035);
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
    @keyframes fadeUpStagger {
        0% { opacity: 0; transform: translateY(14px); }
        100% { opacity: 1; transform: translateY(0); }
    }
    .anim-fade-up {
        animation: fadeUpStagger 0.45s cubic-bezier(0.16, 1, 0.3, 1) both;
    }
    .anim-delay-1 { animation-delay: 0.05s !important; }
    .anim-delay-2 { animation-delay: 0.10s !important; }
    .anim-delay-3 { animation-delay: 0.15s !important; }
</style>
@endpush

@php
    $itemTypes      = $itemTypes ?? \App\Models\WarehouseItem::getItemTypes();
    $componentTypes = $componentTypes ?? \App\Models\WarehouseItem::getComponentTypes();
    $cableTypes     = $cableTypes ?? \App\Models\WarehouseItem::getCableTypes();
    $workUnits      = $workUnits ?? \App\Models\WarehouseItem::getWorkUnits();
    $categories     = $categories ?? \App\Models\WarehouseItem::getCategories();
    $statuses       = $statuses ?? \App\Models\WarehouseItem::getStatuses();
    $locations      = $locations ?? ['Gudang IPNET', 'Customer'];
    $deviceModels   = $deviceModels ?? \App\Models\WarehouseItem::getDeviceModels();
    $deviceBrands   = $deviceBrands ?? \App\Models\WarehouseItem::getDeviceBrands();
@endphp

@section('content')
<div class="flex h-screen overflow-hidden bg-[#F8FAFC] font-sans" x-data="warehouseManager()">
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Master Produk & Perangkat'])
        
        <div class="p-4 sm:p-6 lg:p-7 space-y-6 max-w-[1600px] mx-auto">
            
            <!-- ============================================================ -->
            <!-- HEADER SECTION                                               -->
            <!-- ============================================================ -->
            <div class="ipnet-card p-5 sm:p-6 anim-fade-up anim-delay-1">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 pb-4 mb-4 border-b border-[#E2E8F0]">
                    <div>
                        <p class="text-[#8F0A0D] text-[12px] font-semibold inline-flex items-center uppercase tracking-wider">
                            <span class="ipnet-badge-dot"></span> MENU UTAMA &bull; MASTER PRODUK &amp; INVENTARIS
                        </p>
                        <h2 class="text-[20px] font-bold text-[#1E293B] tracking-tight mt-0.5">
                            Master Data Produk &amp; Katalog Perangkat
                        </h2>
                        <p class="text-[13px] text-[#64748B] mt-0.5">
                            Katalog lengkap seluruh perangkat jaringan, nomor seri (SN), nomor PO/kontrak, dan alokasi tim kerja IPNET 1 - 5.
                        </p>
                    </div>

                    <div class="flex items-center gap-2.5 shrink-0">
                        <button type="button" 
                                @click="openAddModal()" 
                                class="btn-ipnet-gradient px-4 py-2.5 rounded-xl font-semibold text-[13px] shadow-sm flex items-center gap-2 cursor-pointer transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span>Daftarkan Produk Baru</span>
                        </button>
                    </div>
                </div>

                <!-- 5 METRIC CARDS (CONSISTENT NEUTRAL STYLE) -->
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3.5 pt-1">
                    <div class="p-4 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0]">
                        <p class="text-[11px] font-medium text-[#64748B] uppercase tracking-wider">Total Inventaris</p>
                        <h3 class="text-2xl font-bold text-[#1E293B] mt-1">{{ number_format($metrics['total_items'] ?? $items->total()) }}</h3>
                        <p class="text-[11px] text-[#94A3B8] mt-0.5">Seluruh unit terdaftar</p>
                    </div>
                    <div class="p-4 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0]">
                        <p class="text-[11px] font-medium text-[#64748B] uppercase tracking-wider">Stok Tersedia</p>
                        <h3 class="text-2xl font-bold text-[#1E293B] mt-1">{{ number_format($metrics['ready_stock'] ?? 0) }}</h3>
                        <p class="text-[11px] text-[#94A3B8] mt-0.5">Ready di gudang</p>
                    </div>
                    <div class="p-4 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0]">
                        <p class="text-[11px] font-medium text-[#64748B] uppercase tracking-wider">Backup / Spare</p>
                        <h3 class="text-2xl font-bold text-[#1E293B] mt-1">{{ number_format($metrics['backup_spare'] ?? 0) }}</h3>
                        <p class="text-[11px] text-[#94A3B8] mt-0.5">Cadangan maintenance</p>
                    </div>
                    <div class="p-4 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0]">
                        <p class="text-[11px] font-medium text-[#64748B] uppercase tracking-wider">Peminjaman POC</p>
                        <h3 class="text-2xl font-bold text-[#1E293B] mt-1">{{ number_format($metrics['active_poc'] ?? 0) }}</h3>
                        <p class="text-[11px] text-[#94A3B8] mt-0.5">Uji coba di pelanggan</p>
                    </div>
                    <div class="p-4 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0] col-span-2 sm:col-span-1">
                        <p class="text-[11px] font-medium text-[#64748B] uppercase tracking-wider">Pinjaman Disti</p>
                        <h3 class="text-2xl font-bold text-[#1E293B] mt-1">{{ number_format($metrics['disti_loan'] ?? 0) }}</h3>
                        <p class="text-[11px] text-[#94A3B8] mt-0.5">Unit demo vendor</p>
                    </div>
                </div>
            </div>

            <!-- ============================================================ -->
            <!-- FILTER & SEARCH BAR                                          -->
            <!-- ============================================================ -->
            <div class="ipnet-card p-4 sm:p-5 anim-fade-up anim-delay-2">
                <form method="GET" action="{{ route('warehouse.products.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
                    <!-- Search input -->
                    <div class="lg:col-span-2">
                        <label class="block text-[11px] font-semibold text-[#475569] uppercase tracking-wider mb-1">Pencarian Cepat</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-[#94A3B8]">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </span>
                            <input type="text" 
                                   name="search" 
                                   value="{{ request('search') }}" 
                                   placeholder="Cari S/N, P/N, Merk, Model, No PO..."
                                   class="w-full pl-9 pr-3 py-2 text-[12.5px] bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl focus:bg-white focus:outline-none focus:border-[#8F0A0D] text-[#1E293B]">
                        </div>
                    </div>

                    <!-- Jenis Barang -->
                    <div>
                        <label class="block text-[11px] font-semibold text-[#475569] uppercase tracking-wider mb-1">Jenis Perangkat</label>
                        <select name="item_type" class="w-full py-2 px-3 text-[12.5px] bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl focus:bg-white focus:outline-none focus:border-[#8F0A0D] text-[#1E293B]">
                            <option value="Semua">Semua Jenis</option>
                            @foreach($itemTypes as $t)
                                <option value="{{ $t }}" {{ request('item_type') === $t ? 'selected' : '' }}>{{ $t }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Kategori -->
                    <div>
                        <label class="block text-[11px] font-semibold text-[#475569] uppercase tracking-wider mb-1">Kategori</label>
                        <select name="category" class="w-full py-2 px-3 text-[12.5px] bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl focus:bg-white focus:outline-none focus:border-[#8F0A0D] text-[#1E293B]">
                            <option value="Semua">Semua Kategori</option>
                            @foreach($categories as $c)
                                <option value="{{ $c }}" {{ request('category') === $c ? 'selected' : '' }}>{{ $c }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-[11px] font-semibold text-[#475569] uppercase tracking-wider mb-1">Status</label>
                        <select name="status" class="w-full py-2 px-3 text-[12.5px] bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl focus:bg-white focus:outline-none focus:border-[#8F0A0D] text-[#1E293B]">
                            <option value="Semua">Semua Status</option>
                            @foreach($statuses as $s)
                                <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ $s }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Button Actions -->
                    <div class="flex items-end gap-2">
                        <button type="submit" class="flex-1 py-2 px-3 bg-[#1E293B] hover:bg-[#0F172A] text-white text-[12.5px] font-semibold rounded-xl transition cursor-pointer text-center">
                            Filter
                        </button>
                        <a href="{{ route('warehouse.products.index') }}" class="py-2 px-3 bg-white border border-[#CBD5E1] hover:bg-[#F8FAFC] text-slate-600 text-[12.5px] font-semibold rounded-xl transition cursor-pointer text-center">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            <!-- ============================================================ -->
            <!-- TABEL INVENTARIS GUDANG                                      -->
            <!-- ============================================================ -->
            <div class="ipnet-card overflow-hidden anim-fade-up anim-delay-3">
                <div class="p-4 sm:p-5 border-b border-[#E2E8F0] flex items-center justify-between">
                    <div>
                        <h3 class="text-[15px] font-bold text-[#1E293B]">Daftar Master Perangkat &amp; Produk Inventaris</h3>
                        <p class="text-[12px] text-[#64748B]">Menampilkan {{ $items->total() }} unit perangkat di inventaris</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[1000px] text-left border-collapse text-[13px]">
                        <thead>
                            <tr class="bg-[#F8FAFC] border-b border-[#E2E8F0] text-slate-500 uppercase tracking-wider text-[11px] font-semibold">
                                <th class="py-3 px-4">Kode &amp; Nama Perangkat</th>
                                <th class="py-3 px-4">Jenis &amp; Tipe Komponen</th>
                                <th class="py-3 px-4">Serial &amp; Part Number</th>
                                <th class="py-3 px-4">Kategori Kepemilikan</th>
                                <th class="py-3 px-4">Lokasi Fisik</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="py-3 px-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#F1F5F9]">
                            @forelse($items as $item)
                                <tr class="hover:bg-[#F8FAFC]/80 transition">
                                    {{-- Kode & Nama --}}
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
                                            <p class="font-semibold text-slate-800 text-[13px] mt-1 line-clamp-1">
                                                {{ $item->name }}
                                            </p>
                                            @if($item->po_number || $item->contract_number)
                                                <p class="text-[11px] text-slate-400 font-normal mt-0.5">
                                                    {{ $item->po_number ? 'PO: ' . $item->po_number : '' }}
                                                    {{ $item->po_number && $item->contract_number ? ' • ' : '' }}
                                                    {{ $item->contract_number ? 'Kontrak: ' . $item->contract_number : '' }}
                                                </p>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- Jenis & Komponen --}}
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <p class="font-medium text-slate-700 text-xs">{{ $item->item_type }}</p>
                                        <p class="text-[11px] text-slate-400 font-normal mt-0.5">
                                            {{ $item->component_type }}
                                            @if($item->cable_type)
                                                <span class="text-slate-500 font-medium">({{ $item->cable_type }})</span>
                                            @endif
                                        </p>
                                    </td>

                                    {{-- S/N & P/N --}}
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <p class="font-mono text-xs text-slate-700 font-medium">
                                            <span class="text-slate-400 font-sans text-[10.5px]">S/N:</span> {{ $item->serial_number ?: '-' }}
                                        </p>
                                        <p class="font-mono text-[11px] text-slate-500 mt-0.5">
                                            <span class="text-slate-400 font-sans text-[10px]">P/N:</span> {{ $item->product_number ?: '-' }}
                                        </p>
                                    </td>

                                    {{-- Kategori --}}
                                    <td class="py-3.5 px-4">
                                        @if($item->category === 'Stock')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                                Stock
                                            </span>
                                        @elseif($item->category === 'Backup / Spare')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-amber-50 text-amber-700 border border-amber-200/80">
                                                Backup / Spare
                                            </span>
                                        @else
                                            <div class="space-y-0.5">
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-purple-50 text-purple-700 border border-purple-200/80">
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

                                    {{-- Lokasi Fisik --}}
                                    <td class="py-3.5 px-4">
                                        @if($item->location_type === 'Gudang IPNET' || $item->location_type === 'Warehouse IPNET')
                                            <p class="font-medium text-slate-700 text-xs">Gudang IPNET</p>
                                            <p class="text-[11px] text-slate-400 mt-0.5">{{ $item->warehouse_rack ?: 'Gudang Utama' }}</p>
                                        @else
                                            <p class="font-medium text-slate-800 text-xs">Lokasi Customer</p>
                                            <p class="text-[11px] text-slate-500 mt-0.5 line-clamp-1" title="{{ $item->customer_address }}">
                                                {{ $item->customer_address ?: '-' }}
                                            </p>
                                            @if($item->customer_sales_name)
                                                <p class="text-[10px] text-slate-400">AM: {{ $item->customer_sales_name }}</p>
                                            @endif
                                        @endif
                                    </td>

                                    {{-- Status --}}
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        @if($item->status === 'Tersedia')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                Tersedia
                                            </span>
                                        @elseif($item->status === 'POC / Pinjaman')
                                            <div>
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-blue-50 text-blue-700 border border-blue-200">
                                                    Sedang POC
                                                </span>
                                                @if($item->poc_customer_name)
                                                    <p class="text-[11px] text-slate-600 font-medium mt-1">{{ $item->poc_customer_name }}</p>
                                                @endif
                                                @if($item->poc_duration)
                                                    <p class="text-[10px] text-slate-400">{{ $item->poc_duration }}</p>
                                                @endif
                                            </div>
                                        @elseif($item->status === 'Pelanggan')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                                Terpasang di Klien
                                            </span>
                                        @elseif($item->status === 'Pinjaman')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-purple-50 text-purple-700 border border-purple-200">
                                                Pinjaman Vendor
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-red-50 text-red-700 border border-red-200">
                                                {{ $item->status }}
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Aksi --}}
                                    <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <button type="button" 
                                                    @click="openDetailModal({{ json_encode($item) }})" 
                                                    class="p-1.5 rounded-lg border border-slate-200 hover:bg-slate-50 text-slate-500 transition cursor-pointer"
                                                    title="Lihat Detail Lengkap">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            </button>
                                            <button type="button" 
                                                    @click="openEditModal({{ json_encode($item) }})" 
                                                    class="p-1.5 rounded-lg border border-slate-200 hover:bg-slate-50 text-slate-600 transition cursor-pointer"
                                                    title="Edit Data Barang">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </button>
                                            <form action="{{ route('warehouse.products.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus barang {{ $item->item_code }} ini?');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 rounded-lg border border-red-100 hover:bg-red-50 text-[#8F0A0D] transition cursor-pointer" title="Hapus">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-10 text-center text-slate-400">
                                        <div class="max-w-sm mx-auto space-y-2">
                                            <svg class="w-8 h-8 text-slate-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                            <p class="font-medium text-slate-600 text-xs">Belum ada perangkat inventaris yang sesuai filter.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($items->hasPages())
                    <div class="p-4 border-t border-[#E2E8F0]">
                        {{ $items->links() }}
                    </div>
                @endif
            </div>

            <!-- ============================================================ -->
            <!-- MODAL TAMBAH & EDIT PERANGKAT INVENTARIS                     -->
            <!-- ============================================================ -->
            <template x-teleport="body">
                <div x-show="formModalOpen" 
                     x-cloak
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-[#0F172A]/60 z-[99999] flex items-center justify-center p-3 sm:p-5 backdrop-blur-xs"
                     @click.self="formModalOpen = false"
                     @keydown.escape.window="formModalOpen = false">
                    
                    <div class="bg-white rounded-2xl w-[780px] max-w-full max-h-[90vh] flex flex-col text-left shadow-[0_20px_60px_rgba(15,23,42,0.25)] border border-[#E2E8F0] overflow-hidden">
                        
                        <!-- Modal Header (Fixed) -->
                        <div class="flex items-center justify-between px-6 py-4 bg-[#F8FAFC] border-b border-[#E2E8F0] shrink-0">
                            <div class="flex items-center gap-2.5">
                                <div class="w-9 h-9 rounded-xl bg-red-50 text-[#8F0A0D] flex items-center justify-center shrink-0 border border-red-100">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-sm font-bold text-slate-900" x-text="isEditing ? 'Perbarui Data Perangkat' : 'Daftarkan Perangkat Baru ke Gudang'"></h3>
                                    <p class="text-xs text-slate-400 mt-0.5">Kelola data spesifikasi, serial number, kepemilikan, dan lokasi unit</p>
                                </div>
                            </div>
                            <button type="button" @click="formModalOpen = false" class="text-slate-400 hover:text-slate-600 cursor-pointer p-1 rounded-lg hover:bg-slate-100 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <!-- Modal Body Form (Flex container with fixed footer) -->
                        <form @submit.prevent="submitForm()" class="flex flex-col flex-1 min-h-0 overflow-hidden m-0">
                            
                            <!-- Scrollable Inputs Area -->
                            <div class="p-6 overflow-y-auto space-y-5 text-xs flex-1 min-h-0">
                                <!-- SEKSI 1: IDENTITAS PERANGKAT -->
                                <div class="space-y-3">
                                    <div class="flex items-center gap-2 pb-1 border-b border-slate-100">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#8F0A0D]"></span>
                                        <h4 class="font-bold text-slate-800 uppercase tracking-wider text-[11px]">1. Informasi Perangkat</h4>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                        <div class="sm:col-span-2">
                                            <label class="block font-semibold text-slate-700 mb-1">Model <span class="text-red-500">*</span></label>
                                            <select x-model="selectedModel" 
                                                    @change="onModelChange()" 
                                                    required 
                                                    class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-[#8F0A0D] text-slate-800 bg-white font-medium cursor-pointer">
                                                <option value="">-- Pilih Model --</option>
                                                @foreach($deviceModels as $dm)
                                                    <option value="{{ $dm }}">{{ $dm }}</option>
                                                @endforeach
                                            </select>
                                            
                                            <!-- Field Input Tambahan Jika Pilih Others -->
                                            <div x-show="selectedModel === 'Others'" class="mt-2" x-transition>
                                                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Nama Model Kustom <span class="text-red-500">*</span></label>
                                                <input type="text" 
                                                       x-model="customModelName" 
                                                       @input="form.name = customModelName" 
                                                       :required="selectedModel === 'Others'"
                                                       placeholder="Ketik nama model / perangkat kustom..." 
                                                       class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-[#8F0A0D] text-slate-800">
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block font-semibold text-slate-700 mb-1">Merk / Brand <span class="text-red-500">*</span></label>
                                            <select x-model="selectedBrand" 
                                                    @change="onBrandChange()" 
                                                    required
                                                    class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-[#8F0A0D] text-slate-800 bg-white font-medium cursor-pointer">
                                                <option value="">-- Pilih Merk / Brand --</option>
                                                @foreach($deviceBrands as $db)
                                                    <option value="{{ $db }}">{{ $db }}</option>
                                                @endforeach
                                            </select>

                                            <!-- Field Input Tambahan Jika Pilih Others -->
                                            <div x-show="selectedBrand === 'Others'" class="mt-2" x-transition>
                                                <input type="text" 
                                                       x-model="customBrandName" 
                                                       @input="form.brand = customBrandName" 
                                                       :required="selectedBrand === 'Others'"
                                                       placeholder="Ketik merk / brand kustom..." 
                                                       class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-[#8F0A0D] text-slate-800">
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block font-semibold text-slate-700 mb-1">Jenis Perangkat <span class="text-red-500">*</span></label>
                                            <select x-model="form.item_type" required class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-[#8F0A0D] text-slate-800 bg-white">
                                                @foreach($itemTypes as $t)
                                                    <option value="{{ $t }}">{{ $t }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block font-semibold text-slate-700 mb-1">Tipe Komponen / Peripheral</label>
                                            <select x-model="form.component_type" class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-[#8F0A0D] text-slate-800 bg-white">
                                                @foreach($componentTypes as $ct)
                                                    <option value="{{ $ct }}">{{ $ct }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <!-- Conditional Sub-tipe Kabel -->
                                        <div x-show="form.item_type === 'Kabel' || form.component_type === 'Kabel'">
                                            <label class="block font-semibold text-slate-700 mb-1">Tipe / Varian Kabel</label>
                                            <select x-model="form.cable_type" class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-[#8F0A0D] text-slate-800 bg-white">
                                                <option value="">— Pilih Tipe Kabel —</option>
                                                @foreach($cableTypes as $cbt)
                                                    <option value="{{ $cbt }}">{{ $cbt }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- SEKSI 2: IDENTITAS SERIAL & DOKUMEN -->
                                <div class="space-y-3">
                                    <div class="flex items-center gap-2 pb-1 border-b border-slate-100">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#8F0A0D]"></span>
                                        <h4 class="font-bold text-slate-800 uppercase tracking-wider text-[11px]">2. Serial Number &amp; Dokumen Referensi</h4>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                        <div>
                                            <label class="block font-semibold text-slate-700 mb-1">Serial Number (S/N)</label>
                                            <input type="text" x-model="form.serial_number" placeholder="Nomor S/N fisik perangkat" class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-[#8F0A0D] text-slate-800 font-mono">
                                        </div>
                                        <div>
                                            <label class="block font-semibold text-slate-700 mb-1">Part / Product Number (P/N)</label>
                                            <input type="text" x-model="form.product_number" placeholder="Nomor P/N produk" class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-[#8F0A0D] text-slate-800 font-mono">
                                        </div>
                                        <div>
                                            <label class="block font-semibold text-slate-700 mb-1">Nomor PO</label>
                                            <input type="text" x-model="form.po_number" placeholder="PO-IPN-2026-..." class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-[#8F0A0D] text-slate-800">
                                        </div>
                                        <div>
                                            <label class="block font-semibold text-slate-700 mb-1">Nomor Kontrak</label>
                                            <input type="text" x-model="form.contract_number" placeholder="CTR-..." class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-[#8F0A0D] text-slate-800">
                                        </div>
                                    </div>
                                </div>

                                <!-- SEKSI 3: KATEGORI & KEPEMILIKAN -->
                                <div class="space-y-3">
                                    <div class="flex items-center gap-2 pb-1 border-b border-slate-100">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#8F0A0D]"></span>
                                        <h4 class="font-bold text-slate-800 uppercase tracking-wider text-[11px]">3. Kategori Kepemilikan</h4>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                        <div class="sm:col-span-2">
                                            <label class="block font-semibold text-slate-700 mb-1">Kategori Barang <span class="text-red-500">*</span></label>
                                            <select x-model="form.category" required class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-[#8F0A0D] text-slate-800 bg-white font-medium">
                                                @foreach($categories as $c)
                                                    <option value="{{ $c }}">{{ $c }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <!-- DETAIL JIKA KATEGORI = PINJAMAN -->
                                        <template x-if="form.category === 'Pinjaman'">
                                            <div class="sm:col-span-2 p-3.5 rounded-xl bg-purple-50/70 border border-purple-200/80 space-y-3">
                                                <div class="flex items-center justify-between">
                                                    <span class="font-bold text-purple-900 text-xs">Detail Sumber Pinjaman</span>
                                                    <div class="flex items-center gap-3">
                                                        <label class="flex items-center gap-1.5 cursor-pointer">
                                                            <input type="radio" value="disti" x-model="form.loan_source_type" class="text-purple-700">
                                                            <span class="text-xs text-purple-900 font-medium">Distributor (Disti)</span>
                                                        </label>
                                                        <label class="flex items-center gap-1.5 cursor-pointer">
                                                            <input type="radio" value="ipnet" x-model="form.loan_source_type" class="text-purple-700">
                                                            <span class="text-xs text-purple-900 font-medium">Internal IPNET</span>
                                                        </label>
                                                    </div>
                                                </div>

                                                <!-- Opsi Disti -->
                                                <div x-show="form.loan_source_type === 'disti'" class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 pt-1">
                                                    <div>
                                                        <label class="block text-[11px] font-semibold text-purple-900 mb-1">Nama PT Distributor</label>
                                                        <input type="text" x-model="form.disti_name" placeholder="PT Synnex Metrodata..." class="w-full px-2.5 py-1.5 border border-purple-200 rounded-lg text-xs bg-white">
                                                    </div>
                                                    <div>
                                                        <label class="block text-[11px] font-semibold text-purple-900 mb-1">PIC Distributor</label>
                                                        <input type="text" x-model="form.disti_pic" placeholder="Nama PIC Disti" class="w-full px-2.5 py-1.5 border border-purple-200 rounded-lg text-xs bg-white">
                                                    </div>
                                                    <div>
                                                        <label class="block text-[11px] font-semibold text-purple-900 mb-1">No. Telp PIC</label>
                                                        <input type="text" x-model="form.disti_phone" placeholder="08..." class="w-full px-2.5 py-1.5 border border-purple-200 rounded-lg text-xs bg-white">
                                                    </div>
                                                    <div>
                                                        <label class="block text-[11px] font-semibold text-purple-900 mb-1">Durasi Pinjaman</label>
                                                        <input type="text" x-model="form.disti_duration" placeholder="Contoh: 30 Hari, s/d 15 Nov 2026" class="w-full px-2.5 py-1.5 border border-purple-200 rounded-lg text-xs bg-white">
                                                    </div>
                                                    <div class="sm:col-span-2">
                                                        <label class="block text-[11px] font-semibold text-purple-900 mb-1">Alamat Distributor</label>
                                                        <textarea x-model="form.disti_address" rows="1" placeholder="Alamat kantor distributor" class="w-full px-2.5 py-1.5 border border-purple-200 rounded-lg text-xs bg-white"></textarea>
                                                    </div>
                                                </div>

                                                <!-- Opsi IPNET Internal -->
                                                <div x-show="form.loan_source_type === 'ipnet'" class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 pt-1">
                                                    <div>
                                                        <label class="block text-[11px] font-semibold text-purple-900 mb-1">Nama PIC IPNET</label>
                                                        <input type="text" x-model="form.ipnet_pic_name" placeholder="Nama teknisi / lead" class="w-full px-2.5 py-1.5 border border-purple-200 rounded-lg text-xs bg-white">
                                                    </div>
                                                    <div>
                                                        <label class="block text-[11px] font-semibold text-purple-900 mb-1">Unit / Tim Kerja IPNET</label>
                                                        <select x-model="form.ipnet_work_unit" class="w-full px-2.5 py-1.5 border border-purple-200 rounded-lg text-xs bg-white">
                                                            <option value="">— Pilih Tim Kerja —</option>
                                                            @foreach($workUnits as $wu)
                                                                <option value="{{ $wu }}">{{ $wu }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div>
                                                        <label class="block text-[11px] font-semibold text-purple-900 mb-1">No. Telp PIC</label>
                                                        <input type="text" x-model="form.ipnet_phone" placeholder="08..." class="w-full px-2.5 py-1.5 border border-purple-200 rounded-lg text-xs bg-white">
                                                    </div>
                                                    <div>
                                                        <label class="block text-[11px] font-semibold text-purple-900 mb-1">Durasi Pinjaman</label>
                                                        <input type="text" x-model="form.ipnet_duration" placeholder="Contoh: 14 Hari" class="w-full px-2.5 py-1.5 border border-purple-200 rounded-lg text-xs bg-white">
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                <!-- SEKSI 4: LOKASI & STATUS OPERASIONAL -->
                                <div class="space-y-3">
                                    <div class="flex items-center gap-2 pb-1 border-b border-slate-100">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#8F0A0D]"></span>
                                        <h4 class="font-bold text-slate-800 uppercase tracking-wider text-[11px]">4. Lokasi Fisik &amp; Status Distribusi</h4>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                        <div>
                                            <label class="block font-semibold text-slate-700 mb-1">Lokasi Fisik <span class="text-red-500">*</span></label>
                                            <select x-model="form.location_type" required class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-[#8F0A0D] text-slate-800 bg-white">
                                                <option value="Gudang IPNET">Gudang IPNET</option>
                                                <option value="Customer">Customer (Klien Luar)</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block font-semibold text-slate-700 mb-1">Status Operasional <span class="text-red-500">*</span></label>
                                            <select x-model="form.status" required class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-[#8F0A0D] text-slate-800 bg-white font-medium">
                                                @foreach($statuses as $st)
                                                    <option value="{{ $st }}">{{ $st }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <!-- Detail jika Lokasi = Gudang IPNET -->
                                        <div x-show="form.location_type === 'Gudang IPNET' || form.location_type === 'Warehouse IPNET'" class="sm:col-span-2">
                                            <label class="block font-semibold text-slate-700 mb-1">Posisi Rak / Ruang Simpan Gudang</label>
                                            <input type="text" x-model="form.warehouse_rack" placeholder="Contoh: Rak Utama A-01, Bin 03" class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-[#8F0A0D] text-slate-800">
                                        </div>

                                        <!-- Detail jika Lokasi = Customer -->
                                        <template x-if="form.location_type === 'Customer'">
                                            <div class="sm:col-span-2 p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 space-y-2.5">
                                                <span class="font-bold text-slate-800 text-xs">Detail Lokasi Customer</span>
                                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                                                    <div>
                                                        <label class="block text-[11px] font-semibold text-slate-700 mb-1">PIC Customer</label>
                                                        <input type="text" x-model="form.customer_pic" placeholder="Nama PIC" class="w-full px-2.5 py-1.5 border border-slate-200 rounded-lg text-xs bg-white">
                                                    </div>
                                                    <div>
                                                        <label class="block text-[11px] font-semibold text-slate-700 mb-1">No. Telp PIC</label>
                                                        <input type="text" x-model="form.customer_phone" placeholder="08..." class="w-full px-2.5 py-1.5 border border-slate-200 rounded-lg text-xs bg-white">
                                                    </div>
                                                    <div>
                                                        <label class="block text-[11px] font-semibold text-slate-700 mb-1">AM / Sales In-Charge</label>
                                                        <input type="text" x-model="form.customer_sales_name" placeholder="Nama Sales" class="w-full px-2.5 py-1.5 border border-slate-200 rounded-lg text-xs bg-white">
                                                    </div>
                                                    <div class="sm:col-span-3">
                                                        <label class="block text-[11px] font-semibold text-slate-700 mb-1">Alamat Customer (Manual)</label>
                                                        <textarea x-model="form.customer_address" rows="1.5" placeholder="Gedung / Alamat lengkap customer" class="w-full px-2.5 py-1.5 border border-slate-200 rounded-lg text-xs bg-white"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>

                                        <!-- Detail jika Status = POC / Pinjaman -->
                                        <template x-if="form.status === 'POC / Pinjaman'">
                                            <div class="sm:col-span-2 p-3.5 rounded-xl bg-blue-50/70 border border-blue-200/80 space-y-2.5">
                                                <span class="font-bold text-blue-900 text-xs">Detail Peminjaman POC (Proof of Concept)</span>
                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                                    <div>
                                                        <label class="block text-[11px] font-semibold text-blue-900 mb-1">Nama Pelanggan POC</label>
                                                        <input type="text" x-model="form.poc_customer_name" placeholder="PT Bank Mandiri..." class="w-full px-2.5 py-1.5 border border-blue-200 rounded-lg text-xs bg-white">
                                                    </div>
                                                    <div>
                                                        <label class="block text-[11px] font-semibold text-blue-900 mb-1">Durasi POC</label>
                                                        <input type="text" x-model="form.poc_duration" placeholder="Contoh: 14 Hari (20 Okt - 03 Nov 2026)" class="w-full px-2.5 py-1.5 border border-blue-200 rounded-lg text-xs bg-white">
                                                    </div>
                                                    <div>
                                                        <label class="block text-[11px] font-semibold text-blue-900 mb-1">PIC Pelanggan</label>
                                                        <input type="text" x-model="form.poc_pic_name" placeholder="Nama PIC" class="w-full px-2.5 py-1.5 border border-blue-200 rounded-lg text-xs bg-white">
                                                    </div>
                                                    <div>
                                                        <label class="block text-[11px] font-semibold text-blue-900 mb-1">No. Telp Pelanggan</label>
                                                        <input type="text" x-model="form.poc_pic_phone" placeholder="08..." class="w-full px-2.5 py-1.5 border border-blue-200 rounded-lg text-xs bg-white">
                                                    </div>
                                                    <div class="sm:col-span-2">
                                                        <label class="block text-[11px] font-semibold text-blue-900 mb-1">Sales / AM Penanggung Jawab</label>
                                                        <input type="text" x-model="form.poc_sales_name" placeholder="Nama Account Manager / Sales" class="w-full px-2.5 py-1.5 border border-blue-200 rounded-lg text-xs bg-white">
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                <!-- Catatan Tambahan -->
                                <div>
                                    <label class="block font-semibold text-slate-700 mb-1">Catatan Tambahan (Opsional)</label>
                                    <textarea x-model="form.notes" rows="2" placeholder="Catatan kondisi fisik, kelengkapan aksesoris, dll..." class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-[#8F0A0D] text-slate-800"></textarea>
                                </div>
                            </div>

                            <!-- Fixed Modal Footer (Permanently Pinned, Never Scrolls) -->
                            <div class="px-6 py-3.5 bg-[#F8FAFC] border-t border-[#E2E8F0] flex items-center justify-end gap-3 shrink-0">
                                <button type="button" @click="formModalOpen = false" class="py-2.5 px-4 rounded-xl bg-white text-[#334155] border border-[#CBD5E1] font-bold text-xs hover:bg-[#F1F5F9] transition cursor-pointer">
                                    Batal
                                </button>
                                <button type="submit" :disabled="isSubmitting" class="btn-ipnet-gradient py-2.5 px-5 rounded-xl font-bold text-xs text-white shadow-xs transition flex items-center gap-1.5 cursor-pointer disabled:opacity-50">
                                    <span x-text="isSubmitting ? 'Menyimpan...' : (isEditing ? 'Perbarui Perangkat' : 'Simpan ke Inventaris')"></span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </template>

            <!-- ============================================================ -->
            <!-- MODAL DETAIL PERANGKAT                                       -->
            <!-- ============================================================ -->
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
                    
                    <div class="bg-white rounded-2xl w-[640px] max-w-full max-h-[90vh] flex flex-col text-left shadow-[0_20px_60px_rgba(15,23,42,0.25)] border border-[#E2E8F0] overflow-hidden">
                        
                        <!-- Fixed Header -->
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

                        <!-- Scrollable Body Only -->
                        <div class="p-6 overflow-y-auto space-y-4 text-xs flex-1 min-h-0">
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
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
                            </div>

                            <!-- Detail Sumber Pinjaman jika Pinjaman -->
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

                            <!-- Lokasi & Penempatan -->
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

                            <!-- Detail jika sedang POC -->
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

                            <!-- Catatan -->
                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                                <p class="text-[10.5px] font-semibold text-slate-400 uppercase">Catatan Tambahan</p>
                                <p class="text-slate-700 mt-1 whitespace-pre-line" x-text="selectedItem?.notes || 'Tidak ada catatan khusus.'"></p>
                            </div>
                        </div>

                        <!-- Fixed Footer -->
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
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('warehouseManager', () => ({
            formModalOpen: false,
            detailModalOpen: false,
            isEditing: false,
            isSubmitting: false,
            selectedItem: null,
            selectedModel: '',
            customModelName: '',
            selectedBrand: '',
            customBrandName: '',

            onBrandChange() {
                if (this.selectedBrand === 'Others') {
                    this.form.brand = this.customBrandName || '';
                } else {
                    this.form.brand = this.selectedBrand;
                }
            },

            onModelChange() {
                if (this.selectedModel === 'Others') {
                    this.form.name = this.customModelName || '';
                    return;
                }
                this.form.name = this.selectedModel;
                const m = this.selectedModel;

                // Auto-detect brand & item_type sesuai katalog perangkat
                if (['ACX2200','ACX4000','ACX5000','ACX7000','CTP2056','EX2200','EX2300','EX3200','EX3300','EX3400','EX4200','EX4300','EX4500','EX4550','EX4600','EX8200','EX9200','ISG1000','ISG2000','MX80','MX104','MX204','MX240','MX301','MX304','MX480','MX960','MX10K','M10i','M320','NS500','QFX3500','QFX5100','QFX5200','SSG20','SSG140','SRX100','SRX210','SRX240','SRX300','SRX550','SRX650','SRX1400','SRX1500','SRX3400','SRX3500','SRX5600','SRX5800','T1600','T4000','PTX5000'].includes(m)) {
                    this.selectedBrand = 'Juniper Networks';
                    this.form.brand = 'Juniper Networks';
                    if (m.startsWith('SRX') || m.startsWith('ISG') || m.startsWith('SSG') || m.startsWith('NS')) {
                        this.form.item_type = 'Firewall';
                    } else if (m.startsWith('EX') || m.startsWith('QFX')) {
                        this.form.item_type = 'Switch';
                    } else {
                        this.form.item_type = 'Router';
                    }
                } else if (['BD8800','BDX8','X430','X440','X450','X460','X480','X650','X670'].includes(m)) {
                    this.selectedBrand = 'Extreme Networks';
                    this.form.brand = 'Extreme Networks';
                    this.form.item_type = 'Switch';
                } else if (['PA3020','PA5010','PA5020','PA5050'].includes(m)) {
                    this.selectedBrand = 'Palo Alto Networks';
                    this.form.brand = 'Palo Alto Networks';
                    this.form.item_type = 'Firewall';
                } else if (m === 'NUTANIX') {
                    this.selectedBrand = 'Nutanix';
                    this.form.brand = 'Nutanix';
                    this.form.item_type = 'Lainnya / Other';
                } else if (m === 'Newtec') {
                    this.selectedBrand = 'Newtec';
                    this.form.brand = 'Newtec';
                } else if (m === 'Procera') {
                    this.selectedBrand = 'Procera (Sandvine)';
                    this.form.brand = 'Procera (Sandvine)';
                    this.form.item_type = 'Bandwidth Management';
                } else if (m === 'PulseSecure') {
                    this.selectedBrand = 'Pulse Secure';
                    this.form.brand = 'Pulse Secure';
                    this.form.item_type = 'Firewall';
                } else if (m === 'sfp-xfp-qsfp-cfp') {
                    this.selectedBrand = 'Multi-Vendor / Generic';
                    this.form.brand = 'Multi-Vendor / Generic';
                    this.form.item_type = 'Kabel';
                    this.form.component_type = 'SFP';
                }
            },

            form: {
                id: null,
                name: '',
                brand: '',
                item_type: 'Switch',
                component_type: 'Perangkat Utuh',
                cable_type: '',
                serial_number: '',
                product_number: '',
                po_number: '',
                contract_number: '',
                category: 'Stock',
                loan_source_type: 'disti',
                disti_name: '',
                disti_address: '',
                disti_pic: '',
                disti_phone: '',
                disti_duration: '',
                ipnet_pic_name: '',
                ipnet_work_unit: 'IPNET 1',
                ipnet_phone: '',
                ipnet_duration: '',
                location_type: 'Gudang IPNET',
                warehouse_rack: '',
                customer_address: '',
                customer_pic: '',
                customer_phone: '',
                customer_sales_name: '',
                status: 'Tersedia',
                poc_customer_name: '',
                poc_pic_name: '',
                poc_pic_phone: '',
                poc_sales_name: '',
                poc_duration: '',
                notes: ''
            },

            openAddModal() {
                this.isEditing = false;
                this.selectedModel = '';
                this.customModelName = '';
                this.selectedBrand = '';
                this.customBrandName = '';
                this.form = {
                    id: null,
                    name: '',
                    brand: '',
                    item_type: 'Switch',
                    component_type: 'Perangkat Utuh',
                    cable_type: '',
                    serial_number: '',
                    product_number: '',
                    po_number: '',
                    contract_number: '',
                    category: 'Stock',
                    loan_source_type: 'disti',
                    disti_name: '',
                    disti_address: '',
                    disti_pic: '',
                    disti_phone: '',
                    disti_duration: '',
                    ipnet_pic_name: '',
                    ipnet_work_unit: 'IPNET 1',
                    ipnet_phone: '',
                    ipnet_duration: '',
                    location_type: 'Gudang IPNET',
                    warehouse_rack: 'Rak Utama A-01',
                    customer_address: '',
                    customer_pic: '',
                    customer_phone: '',
                    customer_sales_name: '',
                    status: 'Tersedia',
                    poc_customer_name: '',
                    poc_pic_name: '',
                    poc_pic_phone: '',
                    poc_sales_name: '',
                    poc_duration: '',
                    notes: ''
                };
                this.formModalOpen = true;
            },

            openEditModal(item) {
                this.isEditing = true;
                const modelsList = @json($deviceModels);
                if (modelsList.includes(item.name)) {
                    this.selectedModel = item.name;
                    this.customModelName = '';
                } else if (item.name) {
                    this.selectedModel = 'Others';
                    this.customModelName = item.name;
                } else {
                    this.selectedModel = '';
                    this.customModelName = '';
                }

                const brandsList = @json($deviceBrands);
                if (brandsList.includes(item.brand)) {
                    this.selectedBrand = item.brand;
                    this.customBrandName = '';
                } else if (item.brand) {
                    this.selectedBrand = 'Others';
                    this.customBrandName = item.brand;
                } else {
                    this.selectedBrand = '';
                    this.customBrandName = '';
                }
                this.form = {
                    id: item.id,
                    name: item.name || '',
                    brand: item.brand || '',
                    item_type: item.item_type || 'Switch',
                    component_type: item.component_type || 'Perangkat Utuh',
                    cable_type: item.cable_type || '',
                    serial_number: item.serial_number || '',
                    product_number: item.product_number || '',
                    po_number: item.po_number || '',
                    contract_number: item.contract_number || '',
                    category: item.category || 'Stock',
                    loan_source_type: item.loan_source_type || 'disti',
                    disti_name: item.disti_name || '',
                    disti_address: item.disti_address || '',
                    disti_pic: item.disti_pic || '',
                    disti_phone: item.disti_phone || '',
                    disti_duration: item.disti_duration || '',
                    ipnet_pic_name: item.ipnet_pic_name || '',
                    ipnet_work_unit: item.ipnet_work_unit || 'IPNET 1',
                    ipnet_phone: item.ipnet_phone || '',
                    ipnet_duration: item.ipnet_duration || '',
                    location_type: (item.location_type === 'Warehouse IPNET' ? 'Gudang IPNET' : (item.location_type || 'Gudang IPNET')),
                    warehouse_rack: item.warehouse_rack || '',
                    customer_address: item.customer_address || '',
                    customer_pic: item.customer_pic || '',
                    customer_phone: item.customer_phone || '',
                    customer_sales_name: item.customer_sales_name || '',
                    status: item.status || 'Tersedia',
                    poc_customer_name: item.poc_customer_name || '',
                    poc_pic_name: item.poc_pic_name || '',
                    poc_pic_phone: item.poc_pic_phone || '',
                    poc_sales_name: item.poc_sales_name || '',
                    poc_duration: item.poc_duration || '',
                    notes: item.notes || ''
                };
                this.formModalOpen = true;
            },

            openDetailModal(item) {
                this.selectedItem = item;
                this.detailModalOpen = true;
            },

            async submitForm() {
                this.isSubmitting = true;
                try {
                    const url = this.isEditing ? `/warehouse/products/${this.form.id}` : '/warehouse/products';
                    const method = this.isEditing ? 'PUT' : 'POST';

                    const response = await fetch(url, {
                        method: method,
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify(this.form)
                    });

                    const data = await response.json();
                    if (response.ok && data.success) {
                        this.formModalOpen = false;
                        if (typeof Swal !== 'undefined') {
                            await Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: data.message || 'Data inventaris berhasil disimpan.',
                                confirmButtonColor: '#8F0A0D'
                            });
                        }
                        window.location.reload();
                    } else {
                        const errMsg = data.message || 'Terjadi kesalahan saat menyimpan data.';
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({ icon: 'error', title: 'Gagal', text: errMsg, confirmButtonColor: '#8F0A0D' });
                        } else {
                            alert(errMsg);
                        }
                    }
                } catch (err) {
                    console.error('Submit error:', err);
                    alert('Terjadi kesalahan jaringan.');
                } finally {
                    this.isSubmitting = false;
                }
            }
        }));
    });
</script>
@endpush
@endsection
