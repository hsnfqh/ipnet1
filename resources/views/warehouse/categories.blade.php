@extends('layouts.app')

@section('title', 'Kategori & Satuan Perangkat - Warehouse IPNET')

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
</style>
@endpush

@section('content')
<div class="flex h-screen overflow-hidden bg-[#F8FAFC] font-sans">
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Kategori & Satuan'])
        
        <div class="p-4 sm:p-6 lg:p-7 space-y-6 max-w-[1600px] mx-auto">
            
            <!-- HEADER -->
            <div class="ipnet-card p-5 sm:p-6 anim-fade-up">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-[#E2E8F0]">
                    <div>
                        <p class="text-[#8F0A0D] text-[12px] font-semibold inline-flex items-center uppercase tracking-wider">
                            <span class="ipnet-badge-dot"></span> MASTER KLASIFIKASI &amp; KOMPONEN
                        </p>
                        <h2 class="text-[20px] font-bold text-[#1E293B] tracking-tight mt-0.5">
                            Katalog Jenis Barang, Komponen, dan Tipe Kabel
                        </h2>
                        <p class="text-[13px] text-[#64748B] mt-0.5">
                            Daftar klasifikasi standar perangkat keras jaringan dan varian kabel pendukung di gudang IPNET.
                        </p>
                    </div>

                    <a href="{{ route('warehouse.items.index') }}" 
                       class="btn-ipnet-gradient px-4 py-2.5 rounded-xl font-semibold text-[13px] shadow-sm flex items-center gap-2 cursor-pointer transition shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                        </svg>
                        <span>Ke Master Barang</span>
                    </a>
                </div>
            </div>

            <!-- GRID 4 KATALOG -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 anim-fade-up">
                
                <!-- 1. JENIS BARANG -->
                <div class="ipnet-card p-5">
                    <div class="flex items-center justify-between pb-3 mb-4 border-b border-[#E2E8F0]">
                        <div>
                            <h3 class="text-[15px] font-bold text-[#1E293B]">Jenis Barang</h3>
                            <p class="text-[11.5px] text-[#64748B]">Kategori perangkat utama</p>
                        </div>
                        <div class="w-8 h-8 rounded-lg bg-red-50 text-[#8F0A0D] flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                    </div>

                    <div class="space-y-2">
                        @php
                            $standardItemTypes = ['Router', 'Switch', 'Firewall', 'AP (Access Point)', 'Bandwidth Management', 'Kabel', 'Lainnya / Other'];
                        @endphp
                        @foreach($standardItemTypes as $type)
                            @php $count = $itemTypes[$type] ?? 0; @endphp
                            <div class="flex items-center justify-between p-2.5 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0]">
                                <span class="text-[12.5px] font-bold text-[#1E293B]">{{ $type }}</span>
                                <span class="text-[11.5px] font-bold px-2 py-0.5 rounded-full {{ $count > 0 ? 'bg-red-100 text-[#8F0A0D]' : 'bg-slate-100 text-slate-500' }}">
                                    {{ $count }} unit
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- 2. TIPE KOMPONEN -->
                <div class="ipnet-card p-5">
                    <div class="flex items-center justify-between pb-3 mb-4 border-b border-[#E2E8F0]">
                        <div>
                            <h3 class="text-[15px] font-bold text-[#1E293B]">Tipe Komponen</h3>
                            <p class="text-[11.5px] text-[#64748B]">Bentuk fisik / part modul</p>
                        </div>
                        <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-700 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
                            </svg>
                        </div>
                    </div>

                    <div class="space-y-2">
                        @php
                            $standardComponents = ['Perangkat Utuh', 'Filter Udara', 'SFP', 'Fan', 'Modul / Card', 'Chasis / Rack', 'PSU', 'Kabel'];
                        @endphp
                        @foreach($standardComponents as $comp)
                            @php $count = $componentTypes[$comp] ?? 0; @endphp
                            <div class="flex items-center justify-between p-2.5 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0]">
                                <span class="text-[12.5px] font-bold text-[#1E293B]">{{ $comp }}</span>
                                <span class="text-[11.5px] font-bold px-2 py-0.5 rounded-full {{ $count > 0 ? 'bg-blue-100 text-blue-800' : 'bg-slate-100 text-slate-500' }}">
                                    {{ $count }} unit
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- 3. TIPE KABEL KHUSUS -->
                <div class="ipnet-card p-5">
                    <div class="flex items-center justify-between pb-3 mb-4 border-b border-[#E2E8F0]">
                        <div>
                            <h3 class="text-[15px] font-bold text-[#1E293B]">Tipe Kabel</h3>
                            <p class="text-[11.5px] text-[#64748B]">Kabel data &amp; kabel power</p>
                        </div>
                        <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                    </div>

                    <div class="space-y-2">
                        @php
                            $standardCables = ['Fiber Optic (FO)', 'UTP / Ethernet', 'C14 - C13', 'C13 - CEE 7', 'CEE', 'C15 - 2 Pin Japanese', 'C19 - CEE 7', 'Patch Cord', 'Lainnya'];
                        @endphp
                        @foreach($standardCables as $cable)
                            @php $count = $cableTypes[$cable] ?? 0; @endphp
                            <div class="flex items-center justify-between p-2.5 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0]">
                                <span class="text-[12.5px] font-bold text-[#1E293B]">{{ $cable }}</span>
                                <span class="text-[11.5px] font-bold px-2 py-0.5 rounded-full {{ $count > 0 ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-500' }}">
                                    {{ $count }} unit
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- 4. KATEGORI KEPEMILIKAN / ALOKASI -->
                <div class="ipnet-card p-5">
                    <div class="flex items-center justify-between pb-3 mb-4 border-b border-[#E2E8F0]">
                        <div>
                            <h3 class="text-[15px] font-bold text-[#1E293B]">Kategori Aset</h3>
                            <p class="text-[11.5px] text-[#64748B]">Peruntukan operasional</p>
                        </div>
                        <div class="w-8 h-8 rounded-lg bg-purple-50 text-purple-700 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                        </div>
                    </div>

                    <div class="space-y-2">
                        @php
                            $standardCategories = ['Stock', 'Backup / Spare', 'Pinjaman'];
                        @endphp
                        @foreach($standardCategories as $cat)
                            @php $count = $categoryTypes[$cat] ?? 0; @endphp
                            <div class="flex items-center justify-between p-2.5 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0]">
                                <div>
                                    <span class="text-[12.5px] font-bold text-[#1E293B]">{{ $cat }}</span>
                                    <p class="text-[11px] text-[#64748B]">
                                        @if($cat === 'Stock') Stok reguler siap kirim
                                        @elseif($cat === 'Backup / Spare') Unit cadangan darurat
                                        @else Pinjaman disti / IPNET 1-5
                                        @endif
                                    </p>
                                </div>
                                <span class="text-[11.5px] font-bold px-2 py-0.5 rounded-full {{ $count > 0 ? 'bg-purple-100 text-purple-800' : 'bg-slate-100 text-slate-500' }}">
                                    {{ $count }} unit
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>
@endsection
