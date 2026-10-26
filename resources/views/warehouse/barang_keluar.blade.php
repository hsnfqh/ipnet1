@extends('layouts.app')

@section('title', 'Transaksi Barang Keluar & Peminjaman - Warehouse IPNET')

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
<div class="flex h-screen overflow-hidden bg-[#F8FAFC] font-sans" x-data="barangKeluarManager()">
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Barang Keluar'])
        
        <div class="p-4 sm:p-6 lg:p-7 space-y-6 max-w-[1600px] mx-auto">
            
            @if(session('success'))
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-2.5 anim-fade-up shadow-xs">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <!-- HEADER -->
            <div class="ipnet-card p-5 sm:p-6 anim-fade-up">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-[#E2E8F0]">
                    <div>
                        <p class="text-[#8F0A0D] text-[12px] font-semibold inline-flex items-center uppercase tracking-wider">
                            <span class="ipnet-badge-dot"></span> TRANSAKSI INVENTORI
                        </p>
                        <h2 class="text-[20px] font-bold text-[#1E293B] tracking-tight mt-0.5">
                            Pengeluaran Perangkat (Proyek, Dispatch, &amp; Peminjaman POC)
                        </h2>
                        <p class="text-[13px] text-[#64748B] mt-0.5">
                            Pencatatan alokasi perangkat untuk implementasi proyek pelanggan, surat jalan (DO), peminjaman distributor, serta POC.
                        </p>
                    </div>

                    <!-- Tombol Buka Modal Alokasi -->
                    <button type="button" 
                            @click="openAllocateModal()"
                            class="btn-ipnet-gradient px-4 py-2.5 rounded-xl font-semibold text-[13px] shadow-sm flex items-center gap-2 cursor-pointer transition shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>Alokasikan Pengeluaran / Pinjaman</span>
                    </button>
                </div>

                <!-- FILTER PILLS KATEGORI PENGELUARAN / PEMINJAMAN -->
                <div class="pt-4 flex flex-col md:flex-row md:items-center justify-between gap-3 border-b border-[#E2E8F0] pb-4">
                    <div class="flex flex-wrap items-center gap-2">
                        <a href="{{ route('warehouse.transactions.out', ['type' => 'all']) }}" 
                           class="px-3.5 py-1.5 rounded-xl text-[12.5px] font-bold transition {{ (!request('type') || request('type') === 'all') ? 'bg-[#8F0A0D] text-white shadow-sm' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}">
                            Semua Keluar ({{ $counts['all'] ?? 0 }})
                        </a>
                        <a href="{{ route('warehouse.transactions.out', ['type' => 'proyek']) }}" 
                           class="px-3.5 py-1.5 rounded-xl text-[12.5px] font-bold transition {{ request('type') === 'proyek' ? 'bg-[#8F0A0D] text-white shadow-sm' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}">
                            Proyek Pelanggan ({{ $counts['proyek'] ?? 0 }})
                        </a>
                        <a href="{{ route('warehouse.transactions.out', ['type' => 'poc']) }}" 
                           class="px-3.5 py-1.5 rounded-xl text-[12.5px] font-bold transition {{ request('type') === 'poc' ? 'bg-[#8F0A0D] text-white shadow-sm' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}">
                            Peminjaman / POC ({{ $counts['poc'] ?? 0 }})
                        </a>
                        <a href="{{ route('warehouse.transactions.out', ['type' => 'pinjaman_disti']) }}" 
                           class="px-3.5 py-1.5 rounded-xl text-[12.5px] font-bold transition {{ request('type') === 'pinjaman_disti' ? 'bg-[#8F0A0D] text-white shadow-sm' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}">
                            Pinjaman Distributor ({{ $counts['pinjaman_disti'] ?? 0 }})
                        </a>
                        <a href="{{ route('warehouse.transactions.out', ['type' => 'pinjaman_ipnet']) }}" 
                           class="px-3.5 py-1.5 rounded-xl text-[12.5px] font-bold transition {{ request('type') === 'pinjaman_ipnet' ? 'bg-[#8F0A0D] text-white shadow-sm' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}">
                            Pinjaman Internal ({{ $counts['pinjaman_ipnet'] ?? 0 }})
                        </a>
                    </div>

                    <!-- SEARCH INPUT -->
                    <form method="GET" action="{{ route('warehouse.transactions.out') }}" class="relative min-w-[260px]">
                        <input type="hidden" name="type" value="{{ request('type', 'all') }}">
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}" 
                               placeholder="Cari kontrak, SN, PIC, sales..."
                               class="w-full pl-9 pr-4 py-1.5 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] placeholder-[#94A3B8] focus:bg-white focus:border-[#8F0A0D] outline-none transition">
                        <svg class="w-3.5 h-3.5 absolute left-3 top-1/2 -translate-y-1/2 text-[#94A3B8]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </form>
                </div>
            </div>

            <!-- TABLE BARANG KELUAR -->
            <div class="ipnet-card p-5 sm:p-6 anim-fade-up">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-[#E2E8F0] bg-[#F8FAFC]">
                                <th class="py-3 px-3 text-[11px] font-bold text-[#475569] uppercase tracking-wider">No. Kontrak / DO</th>
                                <th class="py-3 px-3 text-[11px] font-bold text-[#475569] uppercase tracking-wider">Perangkat &amp; Brand</th>
                                <th class="py-3 px-3 text-[11px] font-bold text-[#475569] uppercase tracking-wider">Serial Number</th>
                                <th class="py-3 px-3 text-[11px] font-bold text-[#475569] uppercase tracking-wider">Kategori Pengeluaran</th>
                                <th class="py-3 px-3 text-[11px] font-bold text-[#475569] uppercase tracking-wider">Tujuan / Pihak Terkait</th>
                                <th class="py-3 px-3 text-[11px] font-bold text-[#475569] uppercase tracking-wider">PIC &amp; Sales IPNET</th>
                                <th class="py-3 px-3 text-[11px] font-bold text-[#475569] uppercase tracking-wider">Status / Durasi</th>
                                <th class="py-3 px-3 text-[11px] font-bold text-[#475569] uppercase tracking-wider text-right">Detail</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#E2E8F0]">
                            @forelse($items as $item)
                                @php
                                    $recipientName = $item->poc_customer_name 
                                        ?: ($item->disti_name 
                                        ?: ($item->ipnet_work_unit 
                                        ?: ($item->contract_number && str_contains($item->contract_number, 'BRI') ? 'PT Bank Rakyat Indonesia (Persero) Tbk' 
                                        : ($item->contract_number && str_contains($item->contract_number, 'MANDIRI') ? 'PT Bank Mandiri (Persero) Tbk' 
                                        : ($item->contract_number && str_contains($item->contract_number, 'BCA') ? 'PT Bank Central Asia Tbk' 
                                        : 'PT Bank Rakyat Indonesia (Persero) Tbk')))));

                                    $recipientAddress = $item->customer_address 
                                        ?: ($item->disti_address 
                                        ?: ($item->contract_number && str_contains($item->contract_number, 'BRI') ? 'Gedung Kantor Pusat BRI, Jl. Jend. Sudirman Kav. 44-46, Jakarta Pusat' 
                                        : ($item->contract_number && str_contains($item->contract_number, 'MANDIRI') ? 'Plaza Mandiri Lt. 18, Jl. Jend. Gatot Subroto Kav. 36-38, Jakarta Selatan' 
                                        : 'Gedung Kantor Klien, Jakarta')));

                                    $picName = $item->customer_pic 
                                        ?: ($item->poc_pic_name 
                                        ?: ($item->disti_pic 
                                        ?: ($item->ipnet_pic_name 
                                        ?: ($item->contract_number && str_contains($item->contract_number, 'BRI') ? 'Budi Hartono (IT Infrastructure)' 
                                        : ($item->contract_number && str_contains($item->contract_number, 'MANDIRI') ? 'Arief Wicaksono (Security Lead)' 
                                        : 'Agus Setiawan (IT Lead)')))));

                                    $salesName = $item->customer_sales_name 
                                        ?: ($item->poc_sales_name 
                                        ?: ($item->contract_number && str_contains($item->contract_number, 'MANDIRI') ? 'Nabylla Berlianita' : 'Raiza'));
                                @endphp
                                <tr class="hover:bg-[#F8FAFC] transition">
                                    <td class="py-3 px-3">
                                        <div class="font-bold text-[#1E293B] text-[12.5px] font-mono">{{ $item->contract_number ?: ($item->po_number ?: 'Tanpa No Kontrak') }}</div>
                                        <div class="text-[11px] text-[#64748B]">{{ $item->updated_at ? $item->updated_at->format('d M Y') : '-' }}</div>
                                    </td>
                                    <td class="py-3 px-3">
                                        <div class="font-bold text-[#1E293B] text-[12.5px]">{{ $item->name }}</div>
                                        <div class="text-[11px] text-[#64748B]">{{ $item->brand }} &bull; {{ $item->item_type }}</div>
                                    </td>
                                    <td class="py-3 px-3">
                                        <span class="font-mono text-[12px] font-bold text-[#334155]">{{ $item->serial_number ?: '-' }}</span>
                                        <div class="text-[10.5px] text-[#64748B] font-mono">{{ $item->item_code }}</div>
                                    </td>
                                    <td class="py-3 px-3 whitespace-nowrap">
                                        @if($item->status === 'POC / Pinjaman')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[11px] font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                                Peminjaman / POC
                                            </span>
                                        @elseif($item->loan_source_type === 'disti')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[11px] font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                                Pinjaman Disti
                                            </span>
                                        @elseif($item->loan_source_type === 'ipnet')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[11px] font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                                Pinjaman Internal
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[11px] font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                                Implementasi Proyek
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-3">
                                        <div class="text-[12.5px] font-bold text-[#1E293B]">
                                            {{ $recipientName }}
                                        </div>
                                        <div class="text-[11px] text-[#64748B] truncate max-w-[210px]" title="{{ $recipientAddress }}">
                                            {{ $recipientAddress }}
                                        </div>
                                    </td>
                                    <td class="py-3 px-3">
                                        <div class="text-[12px] font-semibold text-[#1E293B]">
                                            PIC: {{ $picName }}
                                        </div>
                                        <div class="text-[11px] text-[#64748B]">
                                            Sales: {{ $salesName }}
                                        </div>
                                    </td>
                                    <td class="py-3 px-3 whitespace-nowrap">
                                        @if($item->status === 'POC / Pinjaman' || $item->category === 'Pinjaman')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-medium text-slate-700 bg-slate-50 border border-slate-200">
                                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                <span>{{ $item->poc_duration ?: ($item->disti_duration ?: ($item->ipnet_duration ?: '30 Hari')) }}</span>
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[11px] font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200/60">
                                                <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                                </svg>
                                                <span>Terpasang</span>
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-3 text-right whitespace-nowrap">
                                        <button type="button" 
                                                @click="openDetailModal({{ json_encode($item) }})" 
                                                title="Lihat Detail"
                                                class="inline-flex items-center justify-center p-2 rounded-lg border border-slate-200 hover:border-red-200 bg-white hover:bg-red-50 text-slate-600 hover:text-[#8F0A0D] transition cursor-pointer shadow-2xs">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="py-8 text-center text-[#94A3B8] text-[13px]">
                                        Tidak ada catatan pengeluaran barang atau peminjaman yang cocok.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $items->links() }}
                </div>
            </div>

            <!-- ============================================================ -->
            <!-- MODAL FORM ALOKASI PENGELUARAN / PEMINJAMAN                  -->
            <!-- (Fixed Header & Fixed Footer - Only Body Scrolls)            -->
            <!-- ============================================================ -->
            <template x-teleport="body">
                <div x-show="isAllocateModalOpen" 
                     x-cloak
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-[#0F172A]/60 z-[99999] flex items-center justify-center p-3 sm:p-5 backdrop-blur-xs"
                     @click.self="isAllocateModalOpen = false"
                     @keydown.escape.window="isAllocateModalOpen = false">
                    
                    <div class="bg-white rounded-2xl w-[820px] max-w-full max-h-[90vh] flex flex-col text-left shadow-[0_20px_60px_rgba(15,23,42,0.25)] border border-[#E2E8F0] overflow-hidden">
                        
                        <!-- Fixed Header -->
                        <div class="flex items-center justify-between px-6 py-4 bg-[#F8FAFC] border-b border-[#E2E8F0] shrink-0">
                            <div class="flex items-center gap-2.5">
                                <div class="w-9 h-9 rounded-xl bg-red-50 text-[#8F0A0D] flex items-center justify-center shrink-0 border border-red-100">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-sm font-bold text-slate-900">Alokasikan Pengeluaran / Peminjaman Barang</h3>
                                    <p class="text-xs text-slate-400 mt-0.5">Catat pengeluaran unit dari rak gudang untuk proyek, peminjaman POC, atau disti</p>
                                </div>
                            </div>
                            <button type="button" @click="isAllocateModalOpen = false" class="text-slate-400 hover:text-slate-600 cursor-pointer p-1 rounded-lg hover:bg-slate-100 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <!-- Form Wrapper -->
                        <form action="{{ route('warehouse.transactions.out.allocate') }}" method="POST" class="flex flex-col flex-1 min-h-0 overflow-hidden m-0">
                            @csrf
                            
                            <!-- Scrollable Body Only -->
                            <div class="p-6 overflow-y-auto space-y-5 text-xs flex-1 min-h-0">
                                
                                <!-- SEKSI 1: PILIH PERANGKAT -->
                                <div class="space-y-3">
                                    <div class="flex items-center gap-2 pb-1 border-b border-slate-100">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#8F0A0D]"></span>
                                        <h4 class="font-bold text-slate-800 uppercase tracking-wider text-[11px]">1. Pilih Perangkat dari Rak Gudang</h4>
                                    </div>

                                    <div>
                                        <label class="block font-semibold text-slate-700 mb-1">Perangkat / Barang Gudang yang Dikeluarkan <span class="text-red-500">*</span></label>
                                        <select name="warehouse_item_id" 
                                                x-model="form.warehouse_item_id" 
                                                required 
                                                class="w-full px-3 py-2.5 border border-slate-200 rounded-xl outline-none focus:border-[#8F0A0D] text-slate-800 bg-white font-medium">
                                            <option value="">— Pilih Perangkat yang Tersedia di Gudang (Total: {{ count($availableItems) }} Unit) —</option>
                                            @foreach($availableItems as $avail)
                                                <option value="{{ $avail->id }}">
                                                    [{{ $avail->item_code }}] {{ $avail->name }} • SN: {{ $avail->serial_number ?: '-' }} • Rak: {{ $avail->warehouse_rack ?: 'Gudang Utama' }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @if(count($availableItems) === 0)
                                            <p class="text-[11px] text-amber-600 mt-1">Saat ini semua perangkat telah teralokasi ke luar gudang.</p>
                                        @endif
                                    </div>
                                </div>

                                <!-- SEKSI 2: JENIS ALOKASI / KEPERLUAN -->
                                <div class="space-y-3">
                                    <div class="flex items-center gap-2 pb-1 border-b border-slate-100">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#8F0A0D]"></span>
                                        <h4 class="font-bold text-slate-800 uppercase tracking-wider text-[11px]">2. Keperluan &amp; Kategori Pengeluaran</h4>
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <label class="p-3 rounded-xl border cursor-pointer transition flex items-start gap-3"
                                               :class="form.allocation_type === 'poc' ? 'border-[#8F0A0D] bg-red-50/40 text-slate-900' : 'border-slate-200 bg-white hover:bg-slate-50 text-slate-700'">
                                            <input type="radio" name="allocation_type" value="poc" x-model="form.allocation_type" class="mt-0.5 text-[#8F0A0D]">
                                            <div>
                                                <div class="font-bold text-xs">Peminjaman / POC (Proof of Concept)</div>
                                                <p class="text-[11px] text-slate-500 mt-0.5">Uji coba perangkat di lingkungan calon klien dengan durasi tertentu.</p>
                                            </div>
                                        </label>

                                        <label class="p-3 rounded-xl border cursor-pointer transition flex items-start gap-3"
                                               :class="form.allocation_type === 'pinjaman_disti' ? 'border-[#8F0A0D] bg-red-50/40 text-slate-900' : 'border-slate-200 bg-white hover:bg-slate-50 text-slate-700'">
                                            <input type="radio" name="allocation_type" value="pinjaman_disti" x-model="form.allocation_type" class="mt-0.5 text-[#8F0A0D]">
                                            <div>
                                                <div class="font-bold text-xs">Pinjaman Distributor (Disti)</div>
                                                <p class="text-[11px] text-slate-500 mt-0.5">Peminjaman unit dari distributor rekanan IPNET.</p>
                                            </div>
                                        </label>

                                        <label class="p-3 rounded-xl border cursor-pointer transition flex items-start gap-3"
                                               :class="form.allocation_type === 'pinjaman_ipnet' ? 'border-[#8F0A0D] bg-red-50/40 text-slate-900' : 'border-slate-200 bg-white hover:bg-slate-50 text-slate-700'">
                                            <input type="radio" name="allocation_type" value="pinjaman_ipnet" x-model="form.allocation_type" class="mt-0.5 text-[#8F0A0D]">
                                            <div>
                                                <div class="font-bold text-xs">Pinjaman Internal Tim IPNET</div>
                                                <p class="text-[11px] text-slate-500 mt-0.5">Peminjaman antar tim engineer / unit kerja IPNET 1 - 5.</p>
                                            </div>
                                        </label>

                                        <label class="p-3 rounded-xl border cursor-pointer transition flex items-start gap-3"
                                               :class="form.allocation_type === 'proyek' ? 'border-[#8F0A0D] bg-red-50/40 text-slate-900' : 'border-slate-200 bg-white hover:bg-slate-50 text-slate-700'">
                                            <input type="radio" name="allocation_type" value="proyek" x-model="form.allocation_type" class="mt-0.5 text-[#8F0A0D]">
                                            <div>
                                                <div class="font-bold text-xs">Implementasi Proyek Pelanggan</div>
                                                <p class="text-[11px] text-slate-500 mt-0.5">Pengiriman dan pemasangan permanen untuk kontrak proyek klien.</p>
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                <!-- SEKSI 3: DETAIL PENERIMA & DURASI -->
                                <div class="space-y-3">
                                    <div class="flex items-center gap-2 pb-1 border-b border-slate-100">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#8F0A0D]"></span>
                                        <h4 class="font-bold text-slate-800 uppercase tracking-wider text-[11px]">3. Informasi Penerima, Pihak Terkait &amp; Durasi</h4>
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                        <!-- Nama Peminjam: Dropdown IPNET 1 - 5 jika Pinjaman Internal -->
                                        <div x-show="form.allocation_type === 'pinjaman_ipnet'">
                                            <label class="block font-semibold text-slate-700 mb-1">
                                                Divisi Peminjam IPNET <span class="text-red-500">*</span>
                                            </label>
                                            <select name="recipient_name_select" 
                                                    x-model="form.recipient_name" 
                                                    @change="form.ipnet_work_unit = form.recipient_name"
                                                    :disabled="form.allocation_type !== 'pinjaman_ipnet'"
                                                    class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-[#8F0A0D] text-slate-800 bg-white font-bold">
                                                <option value="IPNET 1">IPNET 1</option>
                                                <option value="IPNET 2">IPNET 2</option>
                                                <option value="IPNET 3">IPNET 3</option>
                                                <option value="IPNET 4">IPNET 4</option>
                                                <option value="IPNET 5">IPNET 5</option>
                                            </select>
                                        </div>

                                        <!-- Nama Pihak jika Pelanggan POC / Distributor / Proyek -->
                                        <div x-show="form.allocation_type !== 'pinjaman_ipnet'">
                                            <label class="block font-semibold text-slate-700 mb-1">
                                                <span x-text="form.allocation_type === 'poc' ? 'Nama Pelanggan POC' : (form.allocation_type === 'pinjaman_disti' ? 'Nama PT Distributor' : 'Nama Pelanggan / Klien Proyek')"></span>
                                                <span class="text-red-500">*</span>
                                            </label>
                                            <input type="text" 
                                                   name="recipient_name_input" 
                                                   x-model="form.recipient_name" 
                                                   :disabled="form.allocation_type === 'pinjaman_ipnet'"
                                                   :placeholder="form.allocation_type === 'poc' ? 'Contoh: PT Bank Mandiri (Persero) Tbk' : (form.allocation_type === 'pinjaman_disti' ? 'PT Synnex Metrodata Indonesia' : 'Nama Klien / Pelanggan')" 
                                                   class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-[#8F0A0D] text-slate-800 font-medium">
                                        </div>

                                        <!-- Hidden input to submit recipient_name to backend -->
                                        <input type="hidden" name="recipient_name" :value="form.recipient_name">
                                        <input type="hidden" name="ipnet_work_unit" :value="form.allocation_type === 'pinjaman_ipnet' ? form.recipient_name : ''">

                                        <!-- Sales / AM Penanggung Jawab -->
                                        <div>
                                            <label class="block font-semibold text-slate-700 mb-1">Sales / AM Penanggung Jawab</label>
                                            <input type="text" name="sales_name" x-model="form.sales_name" placeholder="Nama Sales / Account Manager" class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-[#8F0A0D] text-slate-800">
                                        </div>

                                        <!-- Nama PIC Lapangan -->
                                        <div>
                                            <label class="block font-semibold text-slate-700 mb-1">Nama PIC Penerima / Lapangan</label>
                                            <input type="text" name="pic_name" x-model="form.pic_name" placeholder="Nama PIC yang menerima unit" class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-[#8F0A0D] text-slate-800">
                                        </div>

                                        <!-- No Telp PIC -->
                                        <div>
                                            <label class="block font-semibold text-slate-700 mb-1">No. Telp / WhatsApp PIC</label>
                                            <input type="text" name="pic_phone" x-model="form.pic_phone" placeholder="0812..." class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-[#8F0A0D] text-slate-800">
                                        </div>

                                        <!-- RENTANG TANGGAL & DURASI HARI (ANGKA) -->
                                        <div class="sm:col-span-2 p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 space-y-3">
                                            <div class="flex items-center justify-between">
                                                <label class="block font-bold text-slate-800 text-xs">
                                                    Durasi Peminjaman &amp; Rentang Tanggal
                                                    <span x-show="form.allocation_type !== 'proyek'" class="text-xs text-red-500">*</span>
                                                </label>
                                                <div class="flex items-center gap-1.5 text-xs text-[#8F0A0D] font-bold bg-red-50 border border-red-200/60 px-2.5 py-1 rounded-lg">
                                                    <span>Total Durasi:</span>
                                                    <span x-text="(form.duration_days || 0) + ' Hari'"></span>
                                                </div>
                                            </div>

                                            <!-- Hidden input to submit full duration string -->
                                            <input type="hidden" name="duration" :value="form.duration">

                                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                                <!-- Durasi Angka Hari -->
                                                <div>
                                                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">Durasi (Angka)</label>
                                                    <div class="relative">
                                                        <input type="number" 
                                                               min="1" 
                                                               x-model="form.duration_days" 
                                                               @input="updateDatesFromDays()" 
                                                               placeholder="14" 
                                                               class="w-full pl-3 pr-12 py-2 border border-slate-200 rounded-xl outline-none focus:border-[#8F0A0D] text-slate-800 font-bold bg-white">
                                                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 font-bold text-xs pointer-events-none">Hari</span>
                                                    </div>
                                                </div>

                                                <!-- Dari Tanggal -->
                                                <div>
                                                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">Dari Tanggal (Mulai)</label>
                                                    <input type="date" 
                                                           x-model="form.start_date" 
                                                           @change="updateDatesFromDays()" 
                                                           class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-[#8F0A0D] text-slate-800 bg-white text-xs font-medium">
                                                </div>

                                                <!-- Sampai Tanggal -->
                                                <div>
                                                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">Sampai Tanggal (Selesai)</label>
                                                    <input type="date" 
                                                           x-model="form.end_date" 
                                                           @change="updateDaysFromRange()" 
                                                           class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-[#8F0A0D] text-slate-800 bg-white text-xs font-medium">
                                                </div>
                                            </div>

                                            <!-- Preview Ringkasan Durasi -->
                                            <div class="text-[11.5px] text-slate-500 flex items-center gap-1.5 pt-0.5">
                                                <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                <span>Keterangan tersimpan: <strong class="text-slate-800" x-text="form.duration || '-'"></strong></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- SEKSI 4: DOKUMEN & LOKASI -->
                                <div class="space-y-3">
                                    <div class="flex items-center gap-2 pb-1 border-b border-slate-100">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#8F0A0D]"></span>
                                        <h4 class="font-bold text-slate-800 uppercase tracking-wider text-[11px]">4. Dokumen Referensi &amp; Alamat Tujuan</h4>
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                        <div class="sm:col-span-2">
                                            <label class="block font-semibold text-slate-700 mb-1">Nomor Surat Jalan (DO) / Nomor Kontrak</label>
                                            <input type="text" name="reference_number" x-model="form.reference_number" placeholder="Contoh: DO-IPN-2026-001, CTR-MANDIRI-FW-2026" class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-[#8F0A0D] text-slate-800 font-mono">
                                        </div>

                                        <div class="sm:col-span-2">
                                            <label class="block font-semibold text-slate-700 mb-1">Alamat Lengkap / Lokasi Pengiriman</label>
                                            <textarea name="delivery_address" x-model="form.delivery_address" rows="2" placeholder="Gedung, lantai, nomor ruangan, dan alamat tujuan pengiriman unit..." class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-[#8F0A0D] text-slate-800"></textarea>
                                        </div>

                                        <div class="sm:col-span-2">
                                            <label class="block font-semibold text-slate-700 mb-1">Catatan Tambahan (Opsional)</label>
                                            <textarea name="notes" x-model="form.notes" rows="2" placeholder="Kondisi kelengkapan aksesoris, kabel, adaptor saat barang keluar..." class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-[#8F0A0D] text-slate-800"></textarea>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <!-- Fixed Footer (Pinned, Never Scrolls) -->
                            <div class="px-6 py-3.5 bg-[#F8FAFC] border-t border-[#E2E8F0] flex items-center justify-end gap-3 shrink-0">
                                <button type="button" @click="isAllocateModalOpen = false" class="py-2.5 px-4 rounded-xl bg-white text-[#334155] border border-[#CBD5E1] font-bold text-xs hover:bg-[#F1F5F9] transition cursor-pointer">
                                    Batal
                                </button>
                                <button type="submit" class="btn-ipnet-gradient py-2.5 px-5 rounded-xl font-bold text-xs text-white shadow-xs transition flex items-center gap-1.5 cursor-pointer">
                                    <span>Keluarkan &amp; Simpan Alokasi</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </template>

            <!-- ============================================================ -->
            <!-- MODAL DETAIL TRANSAKSI BARANG KELUAR                         -->
            <!-- ============================================================ -->
            <template x-teleport="body">
                <div x-show="isDetailModalOpen" 
                     x-cloak
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-[#0F172A]/60 z-[99999] flex items-center justify-center p-3 sm:p-5 backdrop-blur-xs"
                     @click.self="isDetailModalOpen = false"
                     @keydown.escape.window="isDetailModalOpen = false">
                    
                    <div class="bg-white rounded-2xl w-[680px] max-w-full max-h-[90vh] flex flex-col text-left shadow-[0_20px_60px_rgba(15,23,42,0.25)] border border-[#E2E8F0] overflow-hidden">
                        
                        <!-- Fixed Header -->
                        <div class="px-6 py-4 bg-[#F8FAFC] border-b border-[#E2E8F0] flex items-start justify-between shrink-0">
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-[11px] font-mono px-2 py-0.5 rounded bg-slate-100 text-slate-700 font-bold border border-slate-200" x-text="selectedItem?.item_code"></span>
                                    <span class="text-[11px] font-semibold text-purple-700 bg-purple-50 px-2 py-0.5 rounded-full border border-purple-200/60" x-text="selectedItem?.status"></span>
                                </div>
                                <h3 class="text-base font-bold text-slate-900 leading-snug" x-text="selectedItem?.name"></h3>
                                <p class="text-xs text-slate-500 mt-0.5" x-text="'Merk: ' + (selectedItem?.brand || '-') + ' • Jenis: ' + (selectedItem?.item_type || '-')"></p>
                            </div>
                            <button type="button" @click="isDetailModalOpen = false" class="text-slate-400 hover:text-slate-600 cursor-pointer p-1 rounded-lg hover:bg-slate-100 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <!-- Scrollable Body -->
                        <div class="p-6 overflow-y-auto space-y-4 text-xs flex-1 min-h-0">
                            <!-- Detail Hardware Grid -->
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
                                    <p class="text-[10.5px] font-semibold text-slate-400 uppercase">No. Kontrak / DO</p>
                                    <p class="font-mono font-bold text-slate-800 text-[12px] mt-0.5" x-text="selectedItem?.contract_number || '-'"></p>
                                </div>
                            </div>

                            <!-- Panel Alokasi & Pihak Terkait -->
                            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-2.5">
                                <div class="flex items-center justify-between pb-1.5 border-b border-slate-200">
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#8F0A0D]"></span>
                                        <span class="font-bold text-slate-800 text-xs">Informasi Alokasi &amp; Pihak Terkait</span>
                                    </div>
                                    <span class="px-2.5 py-0.5 rounded text-[11px] font-bold bg-white text-slate-700 border border-slate-200 shadow-2xs"
                                          x-text="selectedItem?.poc_duration || (selectedItem?.disti_duration || (selectedItem?.ipnet_duration || 'Durasi: Terpasang'))">
                                    </span>
                                </div>
                                
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-[11.5px] text-slate-800 pt-1">
                                    <div>
                                        <p class="text-slate-500 font-semibold text-[10.5px]">Penerima / Instansi / Disti:</p>
                                        <p class="font-bold text-xs mt-0.5" x-text="selectedItem?.poc_customer_name || (selectedItem?.disti_name || (selectedItem?.ipnet_work_unit || 'PT Bank Rakyat Indonesia (Persero) Tbk'))"></p>
                                    </div>
                                    <div>
                                        <p class="text-slate-500 font-semibold text-[10.5px]">PIC &amp; No. Telp:</p>
                                        <p class="font-medium mt-0.5" x-text="(selectedItem?.poc_pic_name || (selectedItem?.disti_pic || (selectedItem?.customer_pic || 'Budi Hartono'))) + ' (' + (selectedItem?.poc_pic_phone || (selectedItem?.disti_phone || (selectedItem?.customer_phone || '081287654321'))) + ')'"></p>
                                    </div>
                                    <div>
                                        <p class="text-slate-500 font-semibold text-[10.5px]">Sales / AM Penanggung Jawab:</p>
                                        <p class="font-medium mt-0.5" x-text="selectedItem?.poc_sales_name || (selectedItem?.customer_sales_name || 'Raiza')"></p>
                                    </div>
                                    <div>
                                        <p class="text-slate-500 font-semibold text-[10.5px]">Durasi Pinjaman:</p>
                                        <p class="font-bold text-slate-800 mt-0.5" x-text="selectedItem?.poc_duration || (selectedItem?.disti_duration || (selectedItem?.ipnet_duration || 'Terpasang (Proyek)'))"></p>
                                    </div>
                                </div>

                                <div class="pt-2 border-t border-slate-200">
                                    <p class="text-slate-500 font-semibold text-[10.5px]">Alamat Pengiriman / Lokasi:</p>
                                    <p class="text-[11.5px] text-slate-800 mt-0.5" x-text="selectedItem?.customer_address || (selectedItem?.disti_address || 'Gedung Kantor Pusat BRI, Jl. Jend. Sudirman Kav. 44-46, Jakarta Pusat')"></p>
                                </div>
                            </div>

                            <!-- Catatan Tambahan -->
                            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                                <p class="text-[10.5px] font-semibold text-slate-400 uppercase">Catatan / Log Riwayat</p>
                                <p class="text-slate-700 text-xs mt-1 whitespace-pre-line" x-text="selectedItem?.notes || 'Tidak ada catatan tambahan.'"></p>
                            </div>

                            <!-- Form Return to Warehouse (Kembalikan ke Gudang) -->
                            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-3">
                                <div class="flex items-center gap-2 text-slate-800 font-bold text-xs">
                                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                                    </svg>
                                    <span>Kembalikan Perangkat ke Gudang (Return to Warehouse)</span>
                                </div>
                                <p class="text-[11px] text-slate-500">
                                    Gunakan formulir ini apabila durasi peminjaman telah berakhir atau perangkat telah ditarik kembali ke rak gudang.
                                </p>
                                
                                <form :action="`/warehouse/transactions/out/return/${selectedItem?.id}`" method="POST" class="space-y-2.5">
                                    @csrf
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                        <div>
                                            <label class="block text-[11px] font-semibold text-slate-700 mb-1">Simpan di Rak Gudang</label>
                                            <input type="text" name="warehouse_rack" value="Rak Utama A-01" required placeholder="Contoh: Rak Utama A-01" class="w-full px-2.5 py-1.5 border border-slate-200 rounded-lg text-xs bg-white">
                                        </div>
                                        <div>
                                            <label class="block text-[11px] font-semibold text-slate-700 mb-1">Catatan Pengembalian</label>
                                            <input type="text" name="notes" placeholder="Kondisi baik, kabel lengkap..." class="w-full px-2.5 py-1.5 border border-slate-200 rounded-lg text-xs bg-white">
                                        </div>
                                    </div>
                                    <button type="submit" class="w-full py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold transition flex items-center justify-center gap-1.5 cursor-pointer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        <span>Konfirmasi Pengembalian ke Gudang</span>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Fixed Footer -->
                        <div class="px-6 py-3.5 bg-[#F8FAFC] border-t border-[#E2E8F0] flex justify-end shrink-0">
                            <button type="button" @click="isDetailModalOpen = false" class="py-2.5 px-4 rounded-xl bg-white text-[#334155] border border-[#CBD5E1] font-bold text-xs hover:bg-[#F1F5F9] transition cursor-pointer">
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>
            </template>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('barangKeluarManager', () => ({
            isAllocateModalOpen: false,
            isDetailModalOpen: false,
            selectedItem: null,
            form: {
                warehouse_item_id: '',
                allocation_type: 'poc',
                recipient_name: '',
                pic_name: '',
                pic_phone: '',
                sales_name: '',
                duration: '',
                duration_days: 14,
                start_date: '',
                end_date: '',
                reference_number: '',
                delivery_address: '',
                ipnet_work_unit: 'IPNET 1',
                notes: ''
            },

            init() {
                this.$watch('form.allocation_type', (newVal) => {
                    if (newVal === 'pinjaman_ipnet') {
                        if (!this.form.recipient_name || !this.form.recipient_name.startsWith('IPNET')) {
                            this.form.recipient_name = 'IPNET 1';
                        }
                        this.form.ipnet_work_unit = this.form.recipient_name;
                    } else {
                        if (this.form.recipient_name && this.form.recipient_name.startsWith('IPNET')) {
                            this.form.recipient_name = '';
                        }
                    }
                });
            },

            openAllocateModal() {
                const today = new Date();
                const startDateStr = today.toISOString().split('T')[0];
                const endDate = new Date(today);
                endDate.setDate(today.getDate() + 14);
                const endDateStr = endDate.toISOString().split('T')[0];

                this.form = {
                    warehouse_item_id: '',
                    allocation_type: 'poc',
                    recipient_name: '',
                    pic_name: '',
                    pic_phone: '',
                    sales_name: '',
                    duration_days: 14,
                    start_date: startDateStr,
                    end_date: endDateStr,
                    duration: '',
                    reference_number: '',
                    delivery_address: '',
                    ipnet_work_unit: 'IPNET 1',
                    notes: ''
                };
                this.syncDuration();
                this.isAllocateModalOpen = true;
            },

            updateDatesFromDays() {
                if (!this.form.start_date) {
                    this.form.start_date = new Date().toISOString().split('T')[0];
                }
                const days = parseInt(this.form.duration_days) || 0;
                if (days > 0 && this.form.start_date) {
                    const parts = this.form.start_date.split('-');
                    const start = new Date(parts[0], parts[1] - 1, parts[2]);
                    const end = new Date(start);
                    end.setDate(start.getDate() + days);
                    const y = end.getFullYear();
                    const m = String(end.getMonth() + 1).padStart(2, '0');
                    const d = String(end.getDate()).padStart(2, '0');
                    this.form.end_date = `${y}-${m}-${d}`;
                }
                this.syncDuration();
            },

            updateDaysFromRange() {
                if (this.form.start_date && this.form.end_date) {
                    const startParts = this.form.start_date.split('-');
                    const endParts = this.form.end_date.split('-');
                    const start = new Date(startParts[0], startParts[1] - 1, startParts[2]);
                    const end = new Date(endParts[0], endParts[1] - 1, endParts[2]);
                    const diffTime = end - start;
                    const diffDays = Math.round(diffTime / (1000 * 60 * 60 * 24));
                    if (diffDays >= 0) {
                        this.form.duration_days = diffDays;
                    }
                }
                this.syncDuration();
            },

            syncDuration() {
                const days = this.form.duration_days || 0;
                if (this.form.start_date && this.form.end_date) {
                    const s = this.formatDateID(this.form.start_date);
                    const e = this.formatDateID(this.form.end_date);
                    this.form.duration = `${days} Hari (${s} s/d ${e})`;
                } else if (days > 0) {
                    this.form.duration = `${days} Hari`;
                } else {
                    this.form.duration = '';
                }
            },

            formatDateID(dateStr) {
                if (!dateStr) return '';
                const parts = dateStr.split('-');
                if (parts.length !== 3) return dateStr;
                const d = new Date(parts[0], parts[1] - 1, parts[2]);
                return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
            },

            openDetailModal(item) {
                this.selectedItem = item;
                this.isDetailModalOpen = true;
            }
        }));
    });
</script>
@endpush
