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
                            Sistem Informasi Logistik &amp; Inventaris Perangkat
                        </h2>
                        <p class="text-[13px] text-[#64748B] mt-0.5">
                            Monitoring persediaan unit fisik, pergerakan barang masuk (Purchase Order), pengiriman keluar (Delivery Order), dan audit stok rak Gudang Utama IPNET.
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
                        <a href="{{ route('warehouse.stock_opname.index') }}" 
                           class="bg-white hover:bg-[#F8FAFC] text-[#475569] px-3.5 py-2.5 rounded-xl font-semibold text-[13px] border border-[#CBD5E1] flex items-center gap-2 transition">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Stock Opname</span>
                        </a>
                    </div>
                </div>

                <!-- 4 PRIMARY KPI OPERATIONAL METRIC CARDS (NO PRICES / NO RUPIAH) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 pt-1">
                    <!-- Total Model Produk -->
                    <div class="p-4 rounded-xl bg-gradient-to-br from-[#FEF2F2] to-white border border-[#FECACA]/60">
                        <div class="flex items-center justify-between text-[#8F0A0D] text-[12px] font-semibold mb-1">
                            <span>Katalog Model Produk</span>
                            <div class="w-7 h-7 rounded-lg bg-[#FEE2E2] flex items-center justify-center text-[#8F0A0D]">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            </div>
                        </div>
                        <div class="text-[26px] font-extrabold text-[#1E293B] tracking-tight">
                            {{ number_format($metrics['total_products']) }} <span class="text-xs text-slate-500 font-normal">Model</span>
                        </div>
                        <div class="flex items-center gap-1.5 mt-1 text-[11px] text-[#64748B]">
                            @if(($metrics['low_stock_count'] ?? 0) > 0)
                                <span class="inline-flex items-center text-amber-600 font-semibold">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1 animate-pulse"></span>
                                    {{ $metrics['low_stock_count'] }} Perlu Restock
                                </span>
                                <span>•</span>
                            @endif
                            <a href="{{ route('warehouse.products.index') }}" class="text-[#8F0A0D] hover:underline font-medium">Buka Katalog &rarr;</a>
                        </div>
                    </div>

                    <!-- Total Unit Fisik di Rak Gudang (Menggantikan Kartu Harga) -->
                    <div class="p-4 rounded-xl bg-gradient-to-br from-emerald-50/60 to-white border border-emerald-100">
                        <div class="flex items-center justify-between text-emerald-800 text-[12px] font-semibold mb-1">
                            <span>Total Unit Fisik di Gudang</span>
                            <div class="w-7 h-7 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-700">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                                </svg>
                            </div>
                        </div>
                        <div class="text-[26px] font-extrabold text-emerald-700 tracking-tight">
                            {{ number_format($metrics['total_stock_units'] ?? 0) }} <span class="text-xs text-emerald-600 font-normal">Unit Fisik</span>
                        </div>
                        <p class="text-[11px] text-emerald-600 mt-1 font-medium flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Tersedia di rak Gudang Utama IPNET
                        </p>
                    </div>

                    <!-- Purchase Order Aktif (Barang Masuk) -->
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

                    <!-- Delivery Order Berjalan (Barang Keluar) -->
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

                <!-- 4 SECONDARY COUNTERS (REFINED FOR SINGLE WAREHOUSE) -->
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
                            <div class="text-[11px] text-[#64748B] font-medium">Mitra Supplier</div>
                            <div class="text-[15px] font-bold text-[#1E293B]">{{ $metrics['total_suppliers'] }} Distributor</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-700 border border-emerald-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-[11px] text-[#64748B] font-medium">Gudang Logistik</div>
                            <div class="text-[15px] font-bold text-emerald-700">1 Gudang Utama (Tunggal)</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PROFIL GUDANG UTAMA IPNET (FASILITAS TUNGGAL RESMI) -->
            <div class="ipnet-card p-5 sm:p-6 bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 text-white anim-fade-up anim-delay-2 border-slate-700 shadow-lg">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="space-y-1.5">
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded-full text-[10.5px] font-extrabold bg-[#8F0A0D] text-white tracking-wide uppercase">
                                {{ $primaryWarehouse->code ?? 'WH-JKT-01' }}
                            </span>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                Operasional &amp; Aktif
                            </span>
                        </div>
                        <h3 class="text-[18px] font-bold text-white tracking-tight">
                            {{ $primaryWarehouse->name ?? 'Gudang Utama IPNET' }}
                        </h3>
                        <p class="text-[12.5px] text-slate-300 flex items-center gap-2">
                            <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span>{{ $primaryWarehouse->address ?? 'Kantor Pusat PT IP Network Solusindo, Jakarta' }}</span>
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-4 text-[12px] bg-white/5 p-3.5 rounded-xl border border-white/10 shrink-0">
                        <div class="border-r border-white/15 pr-4">
                            <div class="text-slate-400 text-[11px]">PIC Gudang</div>
                            <div class="font-bold text-white text-[13px]">{{ $primaryWarehouse->pic_name ?? 'Ahmad' }}</div>
                        </div>
                        <div class="border-r border-white/15 pr-4">
                            <div class="text-slate-400 text-[11px]">Kapasitas Rak</div>
                            <div class="font-bold text-white text-[13px]">{{ $primaryWarehouse->capacity_desc ?? 'Rak A, B, C' }}</div>
                        </div>
                        <div>
                            <div class="text-slate-400 text-[11px]">Total Fisik Tersimpan</div>
                            <div class="font-bold text-emerald-400 text-[14px]">{{ number_format($metrics['total_stock_units'] ?? 0) }} Unit</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION VISUALISASI & GRAFIK GUDANG (INTERACTIVE CHARTS) -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 anim-fade-up anim-delay-2">
                <!-- Grafik 1: Level Stok Perangkat di Rak (Bar Chart) -->
                <div class="lg:col-span-7 ipnet-card p-5 sm:p-6 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between pb-3 mb-3 border-b border-[#E2E8F0]">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span>
                                <div>
                                    <h3 class="text-[15px] font-bold text-[#1E293B]">Monitoring Stok Fisik Perangkat di Rak</h3>
                                    <p class="text-[11.5px] text-[#64748B]">Perbandingan unit fisik tersedia vs batas minimum stok (safety stock)</p>
                                </div>
                            </div>
                            <a href="{{ route('warehouse.products.index') }}" class="text-[12px] font-semibold text-[#8F0A0D] hover:underline shrink-0">
                                Kelola Stok &rarr;
                            </a>
                        </div>

                        <!-- Canvas Chart Bar -->
                        <div class="relative w-full h-[280px]">
                            <canvas id="topProductsBarChart"></canvas>
                        </div>
                    </div>

                    <div class="mt-3 pt-3 border-t border-[#F1F5F9] flex flex-wrap items-center justify-between text-[11.5px] text-[#64748B]">
                        <span class="inline-flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                            Stok Fisik Tersedia
                        </span>
                        <span class="inline-flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                            Batas Minimum (Safety Threshold)
                        </span>
                        <span class="text-slate-400">Data live rak A, B, C</span>
                    </div>
                </div>

                <!-- Grafik 2: Komposisi Stok per Kategori (Donut Chart) -->
                <div class="lg:col-span-5 ipnet-card p-5 sm:p-6 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between pb-3 mb-3 border-b border-[#E2E8F0]">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-[#8F0A0D]"></span>
                                <div>
                                    <h3 class="text-[15px] font-bold text-[#1E293B]">Komposisi Stok per Kategori</h3>
                                    <p class="text-[11.5px] text-[#64748B]">Sebaran unit fisik berdasarkan kategori barang</p>
                                </div>
                            </div>
                            <a href="{{ route('warehouse.categories.index') }}" class="text-[12px] font-semibold text-[#8F0A0D] hover:underline shrink-0">
                                Kategori &rarr;
                            </a>
                        </div>

                        <!-- Canvas Doughnut Chart -->
                        <div class="relative w-full h-[210px] my-2">
                            <canvas id="categoryStockChart"></canvas>
                        </div>
                    </div>

                    <!-- Category Pills List -->
                    <div class="space-y-2 mt-2 pt-3 border-t border-[#F1F5F9] max-h-[140px] overflow-y-auto pr-1">
                        @php
                            $colors = ['#8F0A0D', '#2563EB', '#059669', '#D97706', '#7C3AED', '#0284C7', '#475569'];
                        @endphp
                        @foreach($categoryBreakdown as $idx => $cat)
                            <div class="flex items-center justify-between text-[12px] py-1 border-b border-slate-50 last:border-0">
                                <div class="flex items-center gap-2 truncate">
                                    <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background-color: {{ $colors[$idx % count($colors)] }};"></span>
                                    <span class="font-medium text-[#1E293B] truncate">{{ $cat->name }}</span>
                                </div>
                                <div class="text-right shrink-0">
                                    <span class="font-bold text-[#1E293B]">{{ number_format($cat->products_sum_current_stock ?: 0) }} Unit</span>
                                    <span class="text-[10.5px] text-[#64748B]">({{ $cat->products_count }} model)</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- ALERT: PERINGATAN STOK MENIPIS (JIKA ADA) -->
            @if($lowStockProducts->isNotEmpty())
                <div class="ipnet-card p-4 sm:p-5 border-amber-200 bg-amber-50/50 anim-fade-up">
                    <div class="flex items-start sm:items-center justify-between flex-col sm:flex-row gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-amber-100 flex items-center justify-center text-amber-700 shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-[14px] font-bold text-amber-900">
                                    Perhatian: Terdapat {{ $lowStockProducts->count() }} Perangkat dengan Stok Menipis
                                </h4>
                                <p class="text-[12px] text-amber-800">
                                    Stok fisik perangkat berikut telah mencapai atau berada di bawah batas minimum safety stock.
                                </p>
                            </div>
                        </div>
                        <a href="{{ route('warehouse.purchase_orders.index') }}" 
                           class="px-3.5 py-1.5 rounded-lg bg-amber-600 hover:bg-amber-700 text-white font-semibold text-[12px] transition shrink-0 flex items-center gap-1.5 shadow-sm">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span>Buat Purchase Order Restock</span>
                        </a>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 mt-3 pt-3 border-t border-amber-200/70">
                        @foreach($lowStockProducts as $low)
                            <div class="p-2.5 rounded-lg bg-white/80 border border-amber-200 flex items-center justify-between text-[12px]">
                                <div class="truncate mr-2">
                                    <div class="font-bold text-[#1E293B] truncate">{{ $low->name }}</div>
                                    <div class="text-[10.5px] text-[#64748B]">{{ $low->sku }} • Rak: {{ $low->rack_position ?: 'Utama' }}</div>
                                </div>
                                <div class="text-right shrink-0">
                                    <span class="inline-block px-2 py-0.5 rounded text-[11px] font-extrabold bg-amber-100 text-amber-800">
                                        {{ $low->current_stock }} / Min {{ $low->min_stock }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- TABLES: RECENT PO & RECENT DO (CLEAN OPERATIONAL LOGISTICS) -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 anim-fade-up anim-delay-3">
                <!-- Recent Purchase Orders (Pengadaan Masuk) -->
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
                                    <th class="pb-2 font-semibold">Tujuan</th>
                                    <th class="pb-2 font-semibold text-right">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#F1F5F9]">
                                @forelse($recentPOs as $po)
                                    <tr class="hover:bg-slate-50/70 transition">
                                        <td class="py-2.5 font-bold text-[#1E293B]">
                                            {{ $po->po_number }}
                                            <div class="text-[10.5px] text-[#64748B] font-normal">{{ $po->po_date ? $po->po_date->format('d M Y') : '-' }}</div>
                                        </td>
                                        <td class="py-2.5 text-[#1E293B] font-medium">{{ $po->supplier_name ?: ($po->supplier->name ?? '-') }}</td>
                                        <td class="py-2.5 text-[#64748B] text-[11.5px]">{{ $po->warehouse_name ?: 'Gudang Utama' }}</td>
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

                <!-- Recent Delivery Orders (Pengiriman Keluar) -->
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
                                    <tr class="hover:bg-slate-50/70 transition">
                                        <td class="py-2.5 font-bold text-[#1E293B]">
                                            {{ $do->do_number }}
                                            <div class="text-[10.5px] text-[#64748B] font-normal">{{ $do->do_date ? $do->do_date->format('d M Y') : '-' }}</div>
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. DOUGHNUT CHART - KOMPOSISI STOK PER KATEGORI
    const categoryCtx = document.getElementById('categoryStockChart');
    if (categoryCtx && typeof Chart !== 'undefined') {
        const catLabels = @json($categoryBreakdown->pluck('name'));
        const catData = @json($categoryBreakdown->pluck('products_sum_current_stock')->map(function($val) { return (int)($val ?? 0); }));
        
        new Chart(categoryCtx, {
            type: 'doughnut',
            data: {
                labels: catLabels,
                datasets: [{
                    data: catData,
                    backgroundColor: [
                        '#8F0A0D', // IPNET Crimson
                        '#2563EB', // Blue
                        '#059669', // Emerald
                        '#D97706', // Amber
                        '#7C3AED', // Purple
                        '#0284C7', // Sky
                        '#475569'  // Slate
                    ],
                    borderWidth: 2,
                    borderColor: '#FFFFFF',
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '72%',
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#1E293B',
                        titleFont: { size: 12, weight: 'bold' },
                        bodyFont: { size: 12 },
                        padding: 10,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                return ' ' + context.label + ': ' + context.parsed + ' Unit';
                            }
                        }
                    }
                }
            }
        });
    }

    // 2. HORIZONTAL BAR CHART - TOP STOK FISIK PERANGKAT DI RAK
    const productCtx = document.getElementById('topProductsBarChart');
    if (productCtx && typeof Chart !== 'undefined') {
        const prodLabels = @json($topStockProducts->pluck('name')->map(function($name) {
            return strlen($name) > 28 ? substr($name, 0, 28) . '...' : $name;
        }));
        const currentStockData = @json($topStockProducts->pluck('current_stock'));
        const minStockData = @json($topStockProducts->pluck('min_stock'));

        new Chart(productCtx, {
            type: 'bar',
            data: {
                labels: prodLabels,
                datasets: [
                    {
                        label: 'Stok Fisik Tersedia',
                        data: currentStockData,
                        backgroundColor: '#059669',
                        borderRadius: 6,
                        maxBarThickness: 18,
                    },
                    {
                        label: 'Batas Minimum Safety',
                        data: minStockData,
                        backgroundColor: '#F59E0B',
                        borderRadius: 6,
                        maxBarThickness: 18,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                scales: {
                    x: {
                        beginAtZero: true,
                        grid: {
                            color: '#F1F5F9',
                        },
                        ticks: {
                            color: '#64748B',
                            font: { size: 11 },
                            stepSize: 5
                        }
                    },
                    y: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#334155',
                            font: { size: 11, weight: '500' }
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top',
                        align: 'end',
                        labels: {
                            boxWidth: 10,
                            boxHeight: 10,
                            font: { size: 11, weight: '600' },
                            color: '#475569',
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    },
                    tooltip: {
                        backgroundColor: '#1E293B',
                        padding: 10,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                return ' ' + context.dataset.label + ': ' + context.parsed.x + ' Unit';
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>
@endpush
