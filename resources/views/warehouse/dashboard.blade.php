@extends('layouts.app')

@section('title', 'Dashboard Warehouse - PT IP Network Solusindo')

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
    .ipnet-card:hover {
        border-color: #CBD5E1;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
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

@section('content')
<div class="flex h-screen overflow-hidden bg-[#F8FAFC] font-sans">
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Dashboard Warehouse'])
        
        <div class="p-4 sm:p-6 lg:p-7 space-y-6 max-w-[1600px] mx-auto">
            
            <!-- HEADER SECTION -->
            <div class="ipnet-card p-5 sm:p-6 anim-fade-up anim-delay-1">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 pb-4 mb-4 border-b border-[#E2E8F0]">
                    <div>
                        <p class="text-[#8F0A0D] text-[12px] font-semibold inline-flex items-center uppercase tracking-wider">
                            <span class="ipnet-badge-dot"></span> PUSAT KONTROL &amp; MANAJEMEN WAREHOUSE
                        </p>
                        <h2 class="text-[20px] font-bold text-[#1E293B] tracking-tight mt-0.5">
                            Sistem Informasi Logistik, Pengadaan &amp; Distribusi Perangkat
                        </h2>
                        <p class="text-[13px] text-[#64748B] mt-0.5">
                            Monitoring menyeluruh katalog produk, pengadaan Purchase Order (PO), pengiriman Delivery Order (DO), audit Stock Opname, dan multi-gudang IPNET.
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                        <a href="{{ route('warehouse.products.index') }}" 
                           class="btn-ipnet-gradient px-4 py-2.5 rounded-xl font-semibold text-[13px] shadow-sm flex items-center gap-2 cursor-pointer transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span>Tambah Produk</span>
                        </a>
                        <a href="{{ route('warehouse.purchase_orders.index') }}" 
                           class="bg-[#F1F5F9] hover:bg-[#E2E8F0] text-[#1E293B] px-3.5 py-2.5 rounded-xl font-semibold text-[13px] border border-[#CBD5E1] flex items-center gap-2 transition">
                            <svg class="w-4 h-4 text-[#8F0A0D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                            <span>Buat PO</span>
                        </a>
                        <a href="{{ route('warehouse.delivery_orders.index') }}" 
                           class="bg-[#F1F5F9] hover:bg-[#E2E8F0] text-[#1E293B] px-3.5 py-2.5 rounded-xl font-semibold text-[13px] border border-[#CBD5E1] flex items-center gap-2 transition">
                            <svg class="w-4 h-4 text-[#8F0A0D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                            </svg>
                            <span>Buat DO</span>
                        </a>
                    </div>
                </div>

                <!-- 4 PRIMARY KPI METRIC CARDS -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 pt-1">
                    <!-- Total Produk -->
                    <div class="p-4 rounded-xl bg-gradient-to-br from-[#FEF2F2] to-white border border-[#FECACA]/60">
                        <div class="flex items-center justify-between text-[#8F0A0D] text-[12px] font-semibold mb-1">
                            <span>Total Produk Terdaftar</span>
                            <div class="w-7 h-7 rounded-lg bg-[#FEE2E2] flex items-center justify-center text-[#8F0A0D]">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            </div>
                        </div>
                        <div class="text-[26px] font-extrabold text-[#1E293B] tracking-tight">
                            {{ number_format($metrics['total_products']) }}
                        </div>
                        <div class="flex items-center gap-1.5 mt-1 text-[11px] text-[#64748B]">
                            <span class="inline-flex items-center text-amber-600 font-semibold">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1"></span>
                                {{ $metrics['low_stock_count'] }} Menipis
                            </span>
                            <span>•</span>
                            <a href="{{ route('warehouse.products.index') }}" class="text-[#8F0A0D] hover:underline font-medium">Buka Katalog &rarr;</a>
                        </div>
                    </div>

                    <!-- Total Valuasi Stok -->
                    <div class="p-4 rounded-xl bg-gradient-to-br from-emerald-50/60 to-white border border-emerald-100">
                        <div class="flex items-center justify-between text-emerald-800 text-[12px] font-semibold mb-1">
                            <span>Valuasi Total Aset Stok</span>
                            <div class="w-7 h-7 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-700">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="text-[22px] font-extrabold text-emerald-700 tracking-tight">
                            Rp {{ number_format($metrics['total_valuation'], 0, ',', '.') }}
                        </div>
                        <p class="text-[11px] text-emerald-600 mt-1 font-medium">
                            Akumulasi nilai seluruh unit fisik di rak
                        </p>
                    </div>

                    <!-- Purchase Order Aktif -->
                    <div class="p-4 rounded-xl bg-gradient-to-br from-blue-50/60 to-white border border-blue-100">
                        <div class="flex items-center justify-between text-blue-800 text-[12px] font-semibold mb-1">
                            <span>Purchase Order Berjalan</span>
                            <div class="w-7 h-7 rounded-lg bg-blue-100 flex items-center justify-center text-blue-700">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                        </div>
                        <div class="text-[26px] font-extrabold text-blue-700 tracking-tight">
                            {{ number_format($metrics['active_po']) }} <span class="text-xs text-blue-500 font-semibold font-normal">PO Aktif</span>
                        </div>
                        <p class="text-[11px] text-blue-600 mt-1 font-medium">
                            <a href="{{ route('warehouse.purchase_orders.index') }}" class="hover:underline">Proses pengadaan &amp; restock &rarr;</a>
                        </p>
                    </div>

                    <!-- Delivery Order Berjalan -->
                    <div class="p-4 rounded-xl bg-gradient-to-br from-purple-50/60 to-white border border-purple-100">
                        <div class="flex items-center justify-between text-purple-800 text-[12px] font-semibold mb-1">
                            <span>Delivery Order / Pengiriman</span>
                            <div class="w-7 h-7 rounded-lg bg-purple-100 flex items-center justify-center text-purple-700">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                </svg>
                            </div>
                        </div>
                        <div class="text-[26px] font-extrabold text-purple-700 tracking-tight">
                            {{ number_format($metrics['pending_do']) }} <span class="text-xs text-purple-500 font-semibold font-normal">Surat Jalan</span>
                        </div>
                        <p class="text-[11px] text-purple-600 mt-1 font-medium">
                            <a href="{{ route('warehouse.delivery_orders.index') }}" class="hover:underline">Sedang disiapkan / dikirim &rarr;</a>
                        </p>
                    </div>
                </div>

                <!-- 4 SECONDARY METRIC COUNTERS -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-4 pt-4 border-t border-[#E2E8F0]">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-slate-100 flex items-center justify-center text-slate-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-[11px] text-[#64748B] font-medium">Kategori Produk</div>
                            <div class="text-[15px] font-bold text-[#1E293B]">{{ $metrics['total_categories'] }} Kategori</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-slate-100 flex items-center justify-center text-slate-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-[11px] text-[#64748B] font-medium">Satuan Terdaftar</div>
                            <div class="text-[15px] font-bold text-[#1E293B]">{{ $metrics['total_units'] }} Satuan</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-slate-100 flex items-center justify-center text-slate-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 17a2 2 0 100 4 2 2 0 000-4zm10 0a2 2 0 100 4 2 2 0 000-4zm-8-3h8m-8 0V6a1 1 0 00-1-1H3a1 1 0 00-1 1v8a1 1 0 001 1h1m7-1V8a1 1 0 011-1h3.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V14a1 1 0 01-1 1h-1"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-[11px] text-[#64748B] font-medium">Supplier &amp; Disti</div>
                            <div class="text-[15px] font-bold text-[#1E293B]">{{ $metrics['total_suppliers'] }} Mitra Supplier</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-slate-100 flex items-center justify-center text-slate-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-[11px] text-[#64748B] font-medium">Titik Lokasi Gudang</div>
                            <div class="text-[15px] font-bold text-[#1E293B]">{{ $metrics['total_warehouses'] }} Warehouse</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- MULTI-WAREHOUSE & CATEGORY DISTRIBUTION -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 anim-fade-up anim-delay-2">
                <!-- Multi-Warehouse Locations Status -->
                <div class="lg:col-span-2 ipnet-card p-5 sm:p-6">
                    <div class="flex items-center justify-between pb-3 mb-4 border-b border-[#E2E8F0]">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-[#8F0A0D]"></span>
                            <h3 class="text-[15px] font-bold text-[#1E293B]">Distribusi Stok di Titik Warehouse</h3>
                        </div>
                        <a href="{{ route('warehouse.warehouses.index') }}" class="text-[12px] font-semibold text-[#8F0A0D] hover:underline">
                            Lihat Semua Gudang &rarr;
                        </a>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($warehouseBreakdown as $wh)
                            <div class="p-4 rounded-xl border border-[#E2E8F0] hover:border-[#CBD5E1] bg-[#F8FAFC]/50 hover:bg-white transition">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <span class="inline-block px-2 py-0.5 text-[10px] font-bold rounded bg-slate-200 text-slate-700 mb-1">
                                            {{ $wh->code }}
                                        </span>
                                        <h4 class="text-[14px] font-bold text-[#1E293B]">{{ $wh->name }}</h4>
                                        <p class="text-[11.5px] text-[#64748B] line-clamp-1 mt-0.5">{{ $wh->address }}</p>
                                    </div>
                                    <span class="px-2 py-1 text-[10.5px] font-semibold rounded-lg {{ $wh->status === 'Operasional' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600' }}">
                                        {{ $wh->status }}
                                    </span>
                                </div>
                                <div class="mt-3 pt-3 border-t border-[#E2E8F0] flex items-center justify-between text-[12px]">
                                    <span class="text-[#64748B]">PIC: <strong>{{ $wh->pic_name ?: 'Ahmad' }}</strong></span>
                                    <span class="text-[#8F0A0D] font-bold">{{ number_format($wh->products_sum_current_stock ?: 0) }} Unit Fisik</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Kategori Produk Breakdown -->
                <div class="ipnet-card p-5 sm:p-6">
                    <div class="flex items-center justify-between pb-3 mb-4 border-b border-[#E2E8F0]">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-[#8F0A0D]"></span>
                            <h3 class="text-[15px] font-bold text-[#1E293B]">Stok per Kategori</h3>
                        </div>
                        <a href="{{ route('warehouse.categories.index') }}" class="text-[12px] font-semibold text-[#8F0A0D] hover:underline">
                            Kategori &rarr;
                        </a>
                    </div>

                    <div class="space-y-3">
                        @foreach($categoryBreakdown as $cat)
                            <div class="flex items-center justify-between text-[12.5px]">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-slate-300"></span>
                                    <span class="font-medium text-[#1E293B]">{{ $cat->name }}</span>
                                </div>
                                <div class="text-right">
                                    <span class="font-bold text-[#1E293B]">{{ number_format($cat->products_sum_current_stock ?: 0) }} Unit</span>
                                    <span class="text-[11px] text-[#64748B]">({{ $cat->products_count }} model)</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- TABLES: RECENT PO & RECENT DO -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 anim-fade-up anim-delay-3">
                <!-- Recent Purchase Orders -->
                <div class="ipnet-card p-5 sm:p-6">
                    <div class="flex items-center justify-between pb-3 mb-4 border-b border-[#E2E8F0]">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                            <h3 class="text-[15px] font-bold text-[#1E293B]">Purchase Order Terkini (Pengadaan Masuk)</h3>
                        </div>
                        <a href="{{ route('warehouse.purchase_orders.index') }}" class="text-[12px] font-semibold text-[#8F0A0D] hover:underline">
                            Semua PO &rarr;
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-[12.5px]">
                            <thead>
                                <tr class="text-[#64748B] border-b border-[#E2E8F0]">
                                    <th class="pb-2 font-semibold">No. PO</th>
                                    <th class="pb-2 font-semibold">Supplier</th>
                                    <th class="pb-2 font-semibold">Gudang Tujuan</th>
                                    <th class="pb-2 font-semibold text-right">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#F1F5F9]">
                                @forelse($recentPOs as $po)
                                    <tr>
                                        <td class="py-2.5 font-bold text-[#1E293B]">
                                            {{ $po->po_number }}
                                            <div class="text-[10.5px] text-[#64748B] font-normal">{{ $po->po_date->format('d M Y') }}</div>
                                        </td>
                                        <td class="py-2.5 text-[#1E293B] font-medium">{{ $po->supplier_name ?: '-' }}</td>
                                        <td class="py-2.5 text-[#64748B] text-[11.5px]">{{ $po->warehouse_name ?: '-' }}</td>
                                        <td class="py-2.5 text-right">
                                            @php
                                                $poBadge = match($po->status) {
                                                    'Selesai' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                                    'Disetujui' => 'bg-blue-50 text-blue-700 border-blue-200',
                                                    'Diproses' => 'bg-amber-50 text-amber-700 border-amber-200',
                                                    default => 'bg-slate-100 text-slate-700 border-slate-200'
                                                };
                                            @endphp
                                            <span class="px-2 py-0.5 text-[10.5px] font-bold rounded-md border {{ $poBadge }}">
                                                {{ $po->status }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-6 text-center text-[#94A3B8]">Belum ada Purchase Order aktif.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Recent Delivery Orders -->
                <div class="ipnet-card p-5 sm:p-6">
                    <div class="flex items-center justify-between pb-3 mb-4 border-b border-[#E2E8F0]">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-purple-600"></span>
                            <h3 class="text-[15px] font-bold text-[#1E293B]">Delivery Order Terkini (Pengiriman Keluar)</h3>
                        </div>
                        <a href="{{ route('warehouse.delivery_orders.index') }}" class="text-[12px] font-semibold text-[#8F0A0D] hover:underline">
                            Semua DO &rarr;
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-[12.5px]">
                            <thead>
                                <tr class="text-[#64748B] border-b border-[#E2E8F0]">
                                    <th class="pb-2 font-semibold">No. DO</th>
                                    <th class="pb-2 font-semibold">Penerima &amp; Klien</th>
                                    <th class="pb-2 font-semibold">Kurir / Resi</th>
                                    <th class="pb-2 font-semibold text-right">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#F1F5F9]">
                                @forelse($recentDOs as $do)
                                    <tr>
                                        <td class="py-2.5 font-bold text-[#1E293B]">
                                            {{ $do->do_number }}
                                            <div class="text-[10.5px] text-[#64748B] font-normal">{{ $do->do_date->format('d M Y') }}</div>
                                        </td>
                                        <td class="py-2.5 text-[#1E293B] font-medium">
                                            {{ $do->recipient_name }}
                                            <div class="text-[10.5px] text-[#64748B] font-normal">{{ $do->recipient_company }}</div>
                                        </td>
                                        <td class="py-2.5 text-[#64748B] text-[11.5px]">
                                            {{ $do->courier_name ?: 'Internal Driver' }}
                                            @if($do->vehicle_no)
                                                <span class="text-slate-400">• {{ $do->vehicle_no }}</span>
                                            @endif
                                        </td>
                                        <td class="py-2.5 text-right">
                                            @php
                                                $doBadge = match($do->status) {
                                                    'Terkirim & Diterima' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                                    'Dalam Pengiriman' => 'bg-purple-50 text-purple-700 border-purple-200',
                                                    'Siap Dikirim' => 'bg-blue-50 text-blue-700 border-blue-200',
                                                    default => 'bg-slate-100 text-slate-700 border-slate-200'
                                                };
                                            @endphp
                                            <span class="px-2 py-0.5 text-[10.5px] font-bold rounded-md border {{ $doBadge }}">
                                                {{ $do->status }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-6 text-center text-[#94A3B8]">Belum ada Surat Jalan pengiriman.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
