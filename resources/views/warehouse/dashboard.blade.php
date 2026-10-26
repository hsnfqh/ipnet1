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
            
            <!-- ============================================================ -->
            <!-- HEADER SECTION                                               -->
            <!-- ============================================================ -->
            <div class="ipnet-card p-5 sm:p-6 anim-fade-up anim-delay-1">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 pb-4 mb-4 border-b border-[#E2E8F0]">
                    <div>
                        <p class="text-[#8F0A0D] text-[12px] font-semibold inline-flex items-center uppercase tracking-wider">
                            <span class="ipnet-badge-dot"></span> PUSAT KONTROL WAREHOUSE &amp; INVENTARIS
                        </p>
                        <h2 class="text-[20px] font-bold text-[#1E293B] tracking-tight mt-0.5">
                            Ikhtisar Operasional Gudang &amp; Logistik Perangkat
                        </h2>
                        <p class="text-[13px] text-[#64748B] mt-0.5">
                            Monitoring ketersediaan stok fisik perangkat jaringan, alokasi unit proyek, perangkat POC aktif, dan pergerakan aset logistik.
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                        <a href="{{ route('warehouse.items.index') }}" 
                           class="btn-ipnet-gradient px-4 py-2.5 rounded-xl font-semibold text-[13px] shadow-sm flex items-center gap-2 cursor-pointer transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                            <span>Lihat Master Barang</span>
                        </a>
                        <a href="{{ route('warehouse.transactions.in') }}" 
                           class="bg-[#F1F5F9] hover:bg-[#E2E8F0] text-[#1E293B] px-3.5 py-2.5 rounded-xl font-semibold text-[13px] border border-[#CBD5E1] flex items-center gap-2 transition">
                            <svg class="w-4 h-4 text-[#8F0A0D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                            </svg>
                            <span>Barang Masuk</span>
                        </a>
                    </div>
                </div>

                <!-- 5 METRIC KPI CARDS -->
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3.5 pt-1">
                    <!-- Total Items -->
                    <div class="p-4 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0]">
                        <div class="flex items-center justify-between text-[#64748B] text-[11.5px] font-semibold mb-1">
                            <span>Total Unit Aset</span>
                            <div class="w-6 h-6 rounded-md bg-slate-200/70 flex items-center justify-center text-slate-700">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            </div>
                        </div>
                        <div class="text-[22px] font-bold text-[#1E293B] tracking-tight">
                            {{ number_format($metrics['total_items']) }}
                        </div>
                        <p class="text-[11px] text-[#64748B] mt-0.5 font-medium">Semua jenis perangkat</p>
                    </div>

                    <!-- Ready Stock -->
                    <div class="p-4 rounded-xl bg-emerald-50/50 border border-emerald-100">
                        <div class="flex items-center justify-between text-emerald-800 text-[11.5px] font-semibold mb-1">
                            <span>Ready Stock</span>
                            <div class="w-6 h-6 rounded-md bg-emerald-100 flex items-center justify-center text-emerald-700">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                        </div>
                        <div class="text-[22px] font-bold text-emerald-700 tracking-tight">
                            {{ number_format($metrics['ready_stock']) }}
                        </div>
                        <p class="text-[11px] text-emerald-600 mt-0.5 font-medium">Tersedia di rak gudang</p>
                    </div>

                    <!-- Backup / Spare -->
                    <div class="p-4 rounded-xl bg-blue-50/50 border border-blue-100">
                        <div class="flex items-center justify-between text-blue-800 text-[11.5px] font-semibold mb-1">
                            <span>Cadangan (Spare)</span>
                            <div class="w-6 h-6 rounded-md bg-blue-100 flex items-center justify-center text-blue-700">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                            </div>
                        </div>
                        <div class="text-[22px] font-bold text-blue-700 tracking-tight">
                            {{ number_format($metrics['backup_spare']) }}
                        </div>
                        <p class="text-[11px] text-blue-600 mt-0.5 font-medium">Standby insiden darurat</p>
                    </div>

                    <!-- Active POC -->
                    <div class="p-4 rounded-xl bg-purple-50/50 border border-purple-100">
                        <div class="flex items-center justify-between text-purple-800 text-[11.5px] font-semibold mb-1">
                            <span>Sedang POC</span>
                            <div class="w-6 h-6 rounded-md bg-purple-100 flex items-center justify-center text-purple-700">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="text-[22px] font-bold text-purple-700 tracking-tight">
                            {{ number_format($metrics['active_poc']) }}
                        </div>
                        <p class="text-[11px] text-purple-600 mt-0.5 font-medium">Uji coba di pelanggan</p>
                    </div>

                    <!-- Disti Loan -->
                    <div class="p-4 rounded-xl bg-amber-50/50 border border-amber-100">
                        <div class="flex items-center justify-between text-amber-800 text-[11.5px] font-semibold mb-1">
                            <span>Pinjaman Disti</span>
                            <div class="w-6 h-6 rounded-md bg-amber-100 flex items-center justify-center text-amber-700">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/>
                                </svg>
                            </div>
                        </div>
                        <div class="text-[22px] font-bold text-amber-700 tracking-tight">
                            {{ number_format($metrics['disti_loan']) }}
                        </div>
                        <p class="text-[11px] text-amber-600 mt-0.5 font-medium">Dipinjam dari vendor</p>
                    </div>

                    <!-- Customer Deployed -->
                    <div class="p-4 rounded-xl bg-rose-50/50 border border-rose-100">
                        <div class="flex items-center justify-between text-rose-800 text-[11.5px] font-semibold mb-1">
                            <span>Terpasang Klien</span>
                            <div class="w-6 h-6 rounded-md bg-rose-100 flex items-center justify-center text-rose-700">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="text-[22px] font-bold text-rose-700 tracking-tight">
                            {{ number_format($metrics['customer_deployed']) }}
                        </div>
                        <p class="text-[11px] text-rose-600 mt-0.5 font-medium">Lokasi kantor pelanggan</p>
                    </div>
                </div>
            </div>

            <!-- ============================================================ -->
            <!-- BREAKDOWN & RINGKASAN DATA                                   -->
            <!-- ============================================================ -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 anim-fade-up anim-delay-2">
                <!-- 1. Distribusi Jenis Barang -->
                <div class="ipnet-card p-5">
                    <div class="flex items-center justify-between pb-3 mb-3 border-b border-[#E2E8F0]">
                        <h3 class="text-[14.5px] font-bold text-[#1E293B]">Distribusi Jenis Perangkat</h3>
                        <a href="{{ route('warehouse.categories.index') }}" class="text-[12px] text-[#8F0A0D] font-bold hover:underline">Detail</a>
                    </div>
                    <div class="space-y-3">
                        @forelse($byType as $type)
                            @php
                                $percent = $metrics['total_items'] > 0 ? round(($type->total / $metrics['total_items']) * 100) : 0;
                            @endphp
                            <div>
                                <div class="flex items-center justify-between text-[12.5px] font-medium text-[#334155] mb-1">
                                    <span>{{ $type->item_type }}</span>
                                    <span class="font-bold text-[#1E293B]">{{ $type->total }} unit ({{ $percent }}%)</span>
                                </div>
                                <div class="w-full bg-[#E2E8F0] rounded-full h-2 overflow-hidden">
                                    <div class="bg-[#8F0A0D] h-2 rounded-full" style="width: {{ $percent }}%"></div>
                                </div>
                            </div>
                        @empty
                            <p class="text-[12.5px] text-[#94A3B8] text-center py-4">Belum ada data jenis barang</p>
                        @endforelse
                    </div>
                </div>

                <!-- 2. Alokasi Unit Kerja IPNET 1 - 5 -->
                <div class="ipnet-card p-5">
                    <div class="flex items-center justify-between pb-3 mb-3 border-b border-[#E2E8F0]">
                        <h3 class="text-[14.5px] font-bold text-[#1E293B]">Distribusi Tim IPNET 1 - 5</h3>
                        <span class="text-[11px] font-semibold text-[#64748B] bg-[#F1F5F9] px-2 py-0.5 rounded-md">Internal IPNET</span>
                    </div>
                    <div class="space-y-2.5">
                        @foreach(['IPNET 1', 'IPNET 2', 'IPNET 3', 'IPNET 4', 'IPNET 5'] as $unit)
                            @php
                                $found = $byWorkUnit->firstWhere('ipnet_work_unit', $unit);
                                $unitCount = $found ? $found->total : 0;
                            @endphp
                            <div class="flex items-center justify-between p-2.5 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0]">
                                <div class="flex items-center gap-2">
                                    <div class="w-2.5 h-2.5 rounded-full bg-[#8F0A0D]"></div>
                                    <span class="text-[13px] font-bold text-[#1E293B]">{{ $unit }}</span>
                                </div>
                                <span class="text-[12.5px] font-bold text-[#475569]">{{ $unitCount }} unit terhubung</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- 3. Pintasan Cepat Menu Transaksi -->
                <div class="ipnet-card p-5">
                    <div class="pb-3 mb-3 border-b border-[#E2E8F0]">
                        <h3 class="text-[14.5px] font-bold text-[#1E293B]">Aksi &amp; Pintasan Menu</h3>
                        <p class="text-[11.5px] text-[#64748B]">Akses cepat modul pencatatan inventori</p>
                    </div>
                    <div class="grid grid-cols-2 gap-2.5">
                        <a href="{{ route('warehouse.transactions.in') }}" 
                           class="p-3 rounded-xl border border-[#E2E8F0] hover:border-[#8F0A0D] bg-[#F8FAFC] hover:bg-white transition group flex flex-col justify-between">
                            <div class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center mb-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                                </svg>
                            </div>
                            <span class="text-[12.5px] font-bold text-[#1E293B] group-hover:text-[#8F0A0D]">Barang Masuk</span>
                            <span class="text-[10.5px] text-[#64748B]">PO &amp; Penerimaan</span>
                        </a>

                        <a href="{{ route('warehouse.transactions.out') }}" 
                           class="p-3 rounded-xl border border-[#E2E8F0] hover:border-[#8F0A0D] bg-[#F8FAFC] hover:bg-white transition group flex flex-col justify-between">
                            <div class="w-7 h-7 rounded-lg bg-rose-100 text-rose-700 flex items-center justify-center mb-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                                </svg>
                            </div>
                            <span class="text-[12.5px] font-bold text-[#1E293B] group-hover:text-[#8F0A0D]">Barang Keluar</span>
                            <span class="text-[10.5px] text-[#64748B]">Proyek &amp; Dispatch</span>
                        </a>

                        <a href="{{ route('warehouse.transactions.out', ['type' => 'poc']) }}" 
                           class="p-3 rounded-xl border border-[#E2E8F0] hover:border-[#8F0A0D] bg-[#F8FAFC] hover:bg-white transition group flex flex-col justify-between">
                            <div class="w-7 h-7 rounded-lg bg-purple-100 text-purple-700 flex items-center justify-center mb-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                </svg>
                            </div>
                            <span class="text-[12.5px] font-bold text-[#1E293B] group-hover:text-[#8F0A0D]">Peminjaman &amp; POC</span>
                            <span class="text-[10.5px] text-[#64748B]">Kategori Barang Keluar</span>
                        </a>

                        <a href="{{ route('warehouse.stock_opname.index') }}" 
                           class="p-3 rounded-xl border border-[#E2E8F0] hover:border-[#8F0A0D] bg-[#F8FAFC] hover:bg-white transition group flex flex-col justify-between">
                            <div class="w-7 h-7 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center mb-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                </svg>
                            </div>
                            <span class="text-[12.5px] font-bold text-[#1E293B] group-hover:text-[#8F0A0D]">Stock Opname</span>
                            <span class="text-[10.5px] text-[#64748B]">Audit Rak Gudang</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- ============================================================ -->
            <!-- TABEL TERAKHIR MASUK / DIUBAH                                -->
            <!-- ============================================================ -->
            <div class="ipnet-card p-5 sm:p-6 anim-fade-up anim-delay-3">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-4 mb-4 border-b border-[#E2E8F0]">
                    <div>
                        <h3 class="text-[16px] font-bold text-[#1E293B]">Aktivitas Mutasi Perangkat Terkini</h3>
                        <p class="text-[12.5px] text-[#64748B]">Daftar 6 perangkat terbaru yang didaftarkan atau diperbarui ke sistem.</p>
                    </div>
                    <a href="{{ route('warehouse.items.index') }}" class="text-[13px] font-bold text-[#8F0A0D] hover:underline flex items-center gap-1.5">
                        <span>Buka Semua Data Barang</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-[#E2E8F0] bg-[#F8FAFC]">
                                <th class="py-3 px-3 text-[11px] font-bold text-[#475569] uppercase tracking-wider">Kode Item &amp; SN</th>
                                <th class="py-3 px-3 text-[11px] font-bold text-[#475569] uppercase tracking-wider">Nama Perangkat &amp; Brand</th>
                                <th class="py-3 px-3 text-[11px] font-bold text-[#475569] uppercase tracking-wider">Jenis &amp; Komponen</th>
                                <th class="py-3 px-3 text-[11px] font-bold text-[#475569] uppercase tracking-wider">Kategori</th>
                                <th class="py-3 px-3 text-[11px] font-bold text-[#475569] uppercase tracking-wider">Lokasi Fisik</th>
                                <th class="py-3 px-3 text-[11px] font-bold text-[#475569] uppercase tracking-wider">Status</th>
                                <th class="py-3 px-3 text-[11px] font-bold text-[#475569] uppercase tracking-wider text-right">Detail</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#E2E8F0]">
                            @forelse($recentItems as $item)
                                <tr class="hover:bg-[#F8FAFC] transition">
                                    <td class="py-3 px-3">
                                        <div class="font-bold text-[#1E293B] text-[12.5px] font-mono">{{ $item->item_code }}</div>
                                        <div class="text-[11px] text-[#64748B] font-mono">SN: {{ $item->serial_number ?: '-' }}</div>
                                    </td>
                                    <td class="py-3 px-3">
                                        <div class="font-bold text-[#1E293B] text-[12.5px]">{{ $item->name }}</div>
                                        <div class="text-[11px] text-[#64748B]">{{ $item->brand ?: 'Brand N/A' }} &bull; PN: {{ $item->product_number ?: '-' }}</div>
                                    </td>
                                    <td class="py-3 px-3">
                                        <span class="inline-block px-2 py-0.5 rounded text-[11px] font-semibold bg-slate-100 text-slate-700">
                                            {{ $item->item_type }}
                                        </span>
                                        <div class="text-[11px] text-[#64748B] mt-0.5">{{ $item->component_type ?: '-' }}</div>
                                    </td>
                                    <td class="py-3 px-3">
                                        @if($item->category === 'Stock')
                                            <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">Stock</span>
                                        @elseif($item->category === 'Backup / Spare')
                                            <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">Backup / Spare</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">Pinjaman</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-3">
                                        <div class="text-[12px] font-medium text-[#1E293B]">{{ $item->location_type }}</div>
                                        <div class="text-[11px] text-[#64748B]">{{ $item->location_type === 'Warehouse IPNET' ? ($item->warehouse_rack ?: 'Rak N/A') : ($item->poc_customer_name ?: 'Alamat Customer') }}</div>
                                    </td>
                                    <td class="py-3 px-3">
                                        @if($item->status === 'Tersedia')
                                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Tersedia
                                            </span>
                                        @elseif($item->status === 'POC / Pinjaman')
                                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[11px] font-bold bg-purple-100 text-purple-800">
                                                <span class="w-1.5 h-1.5 rounded-full bg-purple-500"></span> POC Aktif
                                            </span>
                                        @elseif($item->status === 'Maintenance')
                                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[11px] font-bold bg-red-100 text-red-800">
                                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Maintenance
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 text-slate-800">
                                                {{ $item->status }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-3 text-right">
                                        <a href="{{ route('warehouse.items.index', ['search' => $item->item_code]) }}" 
                                           class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11.5px] font-semibold text-[#8F0A0D] bg-red-50 hover:bg-red-100 border border-red-200 transition">
                                            <span>Buka</span>
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-8 text-center text-[#94A3B8] text-[13px]">
                                        Belum ada data barang terdaftar di inventaris.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
