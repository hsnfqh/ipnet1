@extends('layouts.app')

@section('title', 'Inventory - PT IP Network Solusindo')

@push('styles')
<style>
    .ipnet-card {
        background-color: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 20px;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.04);
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .ipnet-card:hover {
        border-color: #CBD5E1;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
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
        0% { opacity: 0; transform: translateY(16px); }
        100% { opacity: 1; transform: translateY(0); }
    }
    .anim-fade-up {
        animation: fadeUpStagger 0.5s cubic-bezier(0.16, 1, 0.3, 1) both;
    }
    .anim-delay-1 { animation-delay: 0.06s !important; }
    .anim-delay-2 { animation-delay: 0.12s !important; }
</style>
@endpush

@section('content')
<div class="flex h-screen overflow-hidden bg-[#F8FAFC] font-sans" x-data="inventoryManagerPage()">
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Inventory'])
        
        <div class="p-4 sm:p-6 lg:p-7 space-y-5 max-w-[1600px] mx-auto">
            
            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between shadow-xs anim-fade-up">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 font-bold px-2">✕</button>
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-xs font-semibold flex items-center justify-between shadow-xs anim-fade-up">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700 font-bold px-2">✕</button>
                </div>
            @endif

            <!-- ========================================================== -->
            <!-- 1. OFFICIAL IPNET SECTION HEADER & ACTION CONTROLS          -->
            <!-- ========================================================== -->
            <div class="ipnet-card p-5 sm:p-6 anim-fade-up anim-delay-1">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 pb-4 mb-4 border-b border-[#E2E8F0]">
                    <div>
                        <p class="text-[#8F0A0D] text-[12px] font-bold inline-flex items-center uppercase tracking-wider">
                            <span class="ipnet-badge-dot"></span> MANAJEMEN STOK &amp; LOGISTIK
                        </p>
                        <h2 class="text-[20px] font-bold text-[#1E293B] tracking-tight">Inventaris Perangkat &amp; Master Stock</h2>
                        <p class="text-[13px] text-[#64748B] mt-0.5">Monitoring stok ketersediaan barang, mutasi barang keluar (DO) dan penerimaan barang masuk</p>
                    </div>

                    <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                        {{-- Hero Action: Tambah Produk (Beda Sendiri & Standout IPNET Gradient) --}}
                        <button type="button" 
                                @click="openAddProductModal()"
                                class="btn-ipnet-gradient h-10 px-4.5 rounded-xl font-bold text-[12.5px] inline-flex items-center gap-2 text-white shadow-md shadow-[#8F0A0D]/25 hover:shadow-lg hover:shadow-[#8F0A0D]/35 hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200 cursor-pointer group">
                            <span class="w-5 h-5 rounded-lg bg-white/20 flex items-center justify-center text-white transition-transform duration-200 group-hover:rotate-90">
                                <svg class="w-3.5 h-3.5 stroke-[2.8]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                                </svg>
                            </span>
                            <span>Tambah Produk</span>
                        </button>

                        {{-- Action Buttons: Barang Keluar & Barang Masuk (Konsisten Dimension & Sizing) --}}
                        <button type="button" 
                                @click="openStockOutModal()"
                                class="h-10 px-4 rounded-xl bg-slate-700 hover:bg-slate-800 text-white font-bold text-[12.5px] inline-flex items-center gap-2 shadow-xs hover:shadow hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200 cursor-pointer">
                            <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 10.5L12 3m0 0l7.5 7.5M12 3v18"/>
                            </svg>
                            <span>Barang Keluar</span>
                        </button>

                        <button type="button" 
                                @click="openStockInModal()"
                                class="h-10 px-4 rounded-xl bg-slate-800 hover:bg-slate-900 text-white font-bold text-[12.5px] inline-flex items-center gap-2 shadow-xs hover:shadow hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200 cursor-pointer">
                            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 13.5L12 21m0 0l-7.5-7.5M12 21V3"/>
                            </svg>
                            <span>Barang Masuk</span>
                        </button>
                    </div>
                </div>

                {{-- Filter & Search Toolbar --}}
                <form method="GET" action="{{ route('inventory.index') }}" class="flex flex-col sm:flex-row items-center gap-3">
                    <div class="relative flex-1 min-w-[260px] w-full">
                        <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-[#94A3B8]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}"
                               placeholder="Search kode produk, nama barang, kategori..." 
                               class="w-full pl-9 pr-3.5 py-2 rounded-xl border border-[#CBD5E1] text-[12.5px] font-semibold text-[#1E293B] bg-white outline-none hover:border-[#94A3B8] focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition-all shadow-xs placeholder-[#94A3B8]">
                    </div>

                    @if(request('search'))
                        <a href="{{ route('inventory.index') }}" 
                           class="px-3 py-2 text-[12px] font-bold text-[#64748B] hover:text-[#8F0A0D] bg-[#F8FAFC] hover:bg-[#FEF2F2] border border-[#E2E8F0] hover:border-[#FCA5A5] rounded-xl transition cursor-pointer">
                            Reset Filter
                        </a>
                    @endif
                </form>
            </div>

            <!-- ========================================================== -->
            <!-- 2. DATA TABLE CARD                                         -->
            <!-- ========================================================== -->
            <div class="ipnet-card overflow-hidden anim-fade-up anim-delay-2">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-[#F8FAFC] border-b border-[#E2E8F0] text-[#64748B] uppercase text-[10.5px] font-bold">
                                <th class="py-3.5 px-5 font-bold">KODE PRODUK</th>
                                <th class="py-3.5 px-5 font-bold">NAMA PRODUK</th>
                                <th class="py-3.5 px-5 font-bold text-center">QTY</th>
                                <th class="py-3.5 px-5 font-bold">KATEGORI</th>
                                <th class="py-3.5 px-5 font-bold">TANGGAL</th>
                                <th class="py-3.5 px-5 font-bold text-center">STOK AKHIR</th>
                                <th class="py-3.5 px-5 font-bold text-center">STATUS</th>
                                <th class="py-3.5 px-5 font-bold text-center">AKSI</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#F1F5F9] font-medium text-[#1E293B]">
                            @forelse($items as $item)
                                @php
                                    $latestTx = $item->transactions->first();
                                    $lastDate = $latestTx ? \Carbon\Carbon::parse($latestTx->transaction_date)->format('d/m/Y') : \Carbon\Carbon::parse($item->updated_at)->format('d/m/Y');
                                    $lastQty = $latestTx ? ($latestTx->type === 'in' ? "+{$latestTx->qty}" : "-{$latestTx->qty}") : "{$item->stock}";
                                @endphp
                                <tr class="hover:bg-[#F8FAFC] transition">
                                    {{-- Kode Produk --}}
                                    <td class="py-3.5 px-5 font-mono font-bold text-[#8F0A0D]">
                                        {{ $item->product_code }}
                                    </td>

                                    {{-- Nama Produk --}}
                                    <td class="py-3.5 px-5 font-bold text-[#1E293B]">
                                        {{ $item->product_name }}
                                        @if($item->description)
                                            <div class="text-[11px] text-[#94A3B8] font-normal truncate max-w-xs">{{ $item->description }}</div>
                                        @endif
                                    </td>

                                    {{-- QTY Mutasi Terakhir --}}
                                    <td class="py-3.5 px-5 text-center font-bold {{ $latestTx && $latestTx->type === 'out' ? 'text-amber-600' : 'text-emerald-600' }}">
                                        {{ $lastQty }}
                                    </td>

                                    {{-- Kategori --}}
                                    <td class="py-3.5 px-5 text-[#64748B]">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded bg-[#F1F5F9] border border-[#E2E8F0] text-[11px] text-[#475569]">
                                            {{ $item->category ?: 'General' }}
                                        </span>
                                    </td>

                                    {{-- Tanggal --}}
                                    <td class="py-3.5 px-5 text-[#64748B] font-mono text-xs">
                                        {{ $lastDate }}
                                    </td>

                                    {{-- Stok Akhir --}}
                                    <td class="py-3.5 px-5 text-center font-bold text-[#1E293B] text-xs">
                                        {{ $item->stock }} <span class="text-[10px] text-[#94A3B8] font-normal">{{ $item->unit }}</span>
                                    </td>

                                    {{-- Status --}}
                                    <td class="py-3.5 px-5 text-center whitespace-nowrap">
                                        @if($item->stock > 5)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10.5px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                Tersedia
                                            </span>
                                        @elseif($item->stock > 0)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10.5px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                Menipis
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10.5px] font-bold bg-red-50 text-red-700 border border-red-200">
                                                Habis
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Aksi: Hapus Produk --}}
                                    <td class="py-3.5 px-5 text-center whitespace-nowrap">
                                        <form action="{{ route('inventory.destroy', $item->id) }}" 
                                              method="POST" 
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk {{ addslashes($item->product_name) }} ({{ $item->product_code }})?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    title="Hapus Produk" 
                                                    class="w-8 h-8 rounded-lg inline-flex items-center justify-center text-slate-400 hover:text-red-600 hover:bg-red-50 border border-slate-200 hover:border-red-200 transition-all cursor-pointer">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="py-12 text-center text-[#94A3B8] text-xs">
                                        Tidak ada data inventory produk.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination (Matching 0-0 from 0 format) --}}
                <div class="p-4 border-t border-[#E2E8F0] flex items-center justify-between text-xs text-[#64748B]">
                    <div>
                        {{ $items->firstItem() ?? 0 }}-{{ $items->lastItem() ?? 0 }} from {{ $items->total() }}
                    </div>
                    <div>
                        @if($items->hasPages())
                            {{ $items->links() }}
                        @else
                            <div class="flex items-center gap-2">
                                <button disabled class="p-1.5 rounded-lg border border-[#E2E8F0] text-slate-300">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                </button>
                                <button disabled class="p-1.5 rounded-lg border border-[#E2E8F0] text-slate-300">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- MODAL: BARANG MASUK (STOCK IN) --}}
    <div x-show="isStockInModalOpen" x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
        <div @click.away="isStockInModalOpen = false" 
             class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-gray-100 space-y-4 max-h-[90vh] overflow-y-auto">
            
            <div class="flex items-center justify-between border-b pb-3">
                <h3 class="text-base font-bold text-gray-900">Catat Barang Masuk (Stock In)</h3>
                <button type="button" @click="isStockInModalOpen = false" class="text-gray-400 hover:text-gray-700 text-lg font-bold">✕</button>
            </div>

            <form action="{{ route('inventory.stock-in') }}" method="POST" class="space-y-4 text-xs font-semibold">
                @csrf
                <div>
                    <label class="block text-gray-700 mb-1">Pilih Produk <span class="text-red-500">*</span></label>
                    <select name="inventory_item_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs text-gray-900 focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D] cursor-pointer">
                        <option value="">Pilih Produk...</option>
                        @foreach($allItems as $prod)
                            <option value="{{ $prod->id }}">{{ $prod->product_code }} - {{ $prod->product_name }} (Stok: {{ $prod->stock }} {{ $prod->unit }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-gray-700 mb-1">Jumlah Masuk (Qty) <span class="text-red-500">*</span></label>
                        <input type="number" name="qty" min="1" required placeholder="1"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs font-normal text-gray-900 focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D]">
                    </div>
                    <div>
                        <label class="block text-gray-700 mb-1">Tanggal Transaksi <span class="text-red-500">*</span></label>
                        <input type="date" name="transaction_date" value="{{ date('Y-m-d') }}" required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs font-normal text-gray-900 focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D]">
                    </div>
                </div>

                <div>
                    <label class="block text-gray-700 mb-1">No. Referensi PO / DO / Vendor</label>
                    <input type="text" name="reference_no" placeholder="Contoh: PO-2026/09/001"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs font-normal text-gray-900 focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D]">
                </div>

                <div>
                    <label class="block text-gray-700 mb-1">Keterangan / Supplier</label>
                    <textarea name="notes" rows="2" placeholder="Nama distributor, vendor pengirim, nomor surat jalan..."
                              class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs font-normal text-gray-900 focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D]"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t">
                    <button type="button" @click="isStockInModalOpen = false" class="px-4 py-2 rounded-xl border border-gray-200 text-gray-600 font-bold hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="submit" class="btn-ipnet-gradient px-5 py-2.5 rounded-xl font-bold shadow-md cursor-pointer">
                        Simpan Barang Masuk
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL: BARANG KELUAR (STOCK OUT) --}}
    <div x-show="isStockOutModalOpen" x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
        <div @click.away="isStockOutModalOpen = false" 
             class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-gray-100 space-y-4 max-h-[90vh] overflow-y-auto">
            
            <div class="flex items-center justify-between border-b pb-3">
                <h3 class="text-base font-bold text-gray-900">Catat Barang Keluar (Stock Out)</h3>
                <button type="button" @click="isStockOutModalOpen = false" class="text-gray-400 hover:text-gray-700 text-lg font-bold">✕</button>
            </div>

            <form action="{{ route('inventory.stock-out') }}" method="POST" class="space-y-4 text-xs font-semibold">
                @csrf
                <div>
                    <label class="block text-gray-700 mb-1">Pilih Produk <span class="text-red-500">*</span></label>
                    <select name="inventory_item_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs text-gray-900 focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D] cursor-pointer">
                        <option value="">Pilih Produk...</option>
                        @foreach($allItems as $prod)
                            <option value="{{ $prod->id }}">{{ $prod->product_code }} - {{ $prod->product_name }} (Tersedia: {{ $prod->stock }} {{ $prod->unit }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-gray-700 mb-1">Jumlah Keluar (Qty) <span class="text-red-500">*</span></label>
                        <input type="number" name="qty" min="1" required placeholder="1"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs font-normal text-gray-900 focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D]">
                    </div>
                    <div>
                        <label class="block text-gray-700 mb-1">Tanggal Keluar <span class="text-red-500">*</span></label>
                        <input type="date" name="transaction_date" value="{{ date('Y-m-d') }}" required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs font-normal text-gray-900 focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D]">
                    </div>
                </div>

                <div>
                    <label class="block text-gray-700 mb-1">No. Project / DO / SPK</label>
                    <input type="text" name="reference_no" placeholder="Contoh: PRJ-TELKOM-2026 / DO-009"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs font-normal text-gray-900 focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D]">
                </div>

                <div>
                    <label class="block text-gray-700 mb-1">Tujuan Pengiriman / Penerima</label>
                    <textarea name="notes" rows="2" placeholder="Nama PIC engineer lapangan, lokasi site instalasi..."
                              class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs font-normal text-gray-900 focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D]"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t">
                    <button type="button" @click="isStockOutModalOpen = false" class="px-4 py-2 rounded-xl border border-gray-200 text-gray-600 font-bold hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl font-bold bg-slate-700 hover:bg-slate-800 text-white shadow-md cursor-pointer">
                        Simpan Barang Keluar
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL: TAMBAH PRODUK BARU --}}
    <div x-show="isAddProductModalOpen" x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
        <div @click.away="isAddProductModalOpen = false" 
             class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-gray-100 space-y-4 max-h-[90vh] overflow-y-auto">
            
            <div class="flex items-center justify-between border-b pb-3">
                <h3 class="text-base font-bold text-gray-900">Tambah Master Produk Baru</h3>
                <button type="button" @click="isAddProductModalOpen = false" class="text-gray-400 hover:text-gray-700 text-lg font-bold">✕</button>
            </div>

            <form action="{{ route('inventory.store') }}" method="POST" class="space-y-4 text-xs font-semibold">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-gray-700 mb-1">Kode Produk <span class="text-red-500">*</span></label>
                        <input type="text" name="product_code" required placeholder="Contoh: HW-SW-48P"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs font-normal text-gray-900 focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D]">
                    </div>
                    <div>
                        <label class="block text-gray-700 mb-1">Kategori</label>
                        <input type="text" name="category" placeholder="Switch, Router, AP..."
                               class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs font-normal text-gray-900 focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D]">
                    </div>
                </div>

                <div>
                    <label class="block text-gray-700 mb-1">Nama Produk / Tipe <span class="text-red-500">*</span></label>
                    <input type="text" name="product_name" required placeholder="Contoh: Cisco Catalyst 2960-X 48 Port Gigabit"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs font-normal text-gray-900 focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D]">
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block text-gray-700 mb-1">Stok Awal <span class="text-red-500">*</span></label>
                        <input type="number" name="stock" min="0" required placeholder="0"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs font-normal text-gray-900 focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D]">
                    </div>
                    <div>
                        <label class="block text-gray-700 mb-1">Satuan <span class="text-red-500">*</span></label>
                        <input type="text" name="unit" required placeholder="Unit / Pcs / Box" value="Unit"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs font-normal text-gray-900 focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D]">
                    </div>
                    <div>
                        <label class="block text-gray-700 mb-1">Harga Satuan</label>
                        <input type="number" name="unit_price" min="0" placeholder="0"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs font-normal text-gray-900 focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D]">
                    </div>
                </div>

                <div>
                    <label class="block text-gray-700 mb-1">Deskripsi Spesifikasi</label>
                    <textarea name="description" rows="2" placeholder="Spesifikasi teknis produk..."
                              class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs font-normal text-gray-900 focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D]"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t">
                    <button type="button" @click="isAddProductModalOpen = false" class="px-4 py-2 rounded-xl border border-gray-200 text-gray-600 font-bold hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="submit" class="btn-ipnet-gradient px-5 py-2.5 rounded-xl font-bold shadow-md cursor-pointer">
                        Simpan Master Produk
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
    function inventoryManagerPage() {
        return {
            isStockInModalOpen: false,
            isStockOutModalOpen: false,
            isAddProductModalOpen: false,

            openStockInModal() {
                this.isStockInModalOpen = true;
            },

            openStockOutModal() {
                this.isStockOutModalOpen = true;
            },

            openAddProductModal() {
                this.isAddProductModalOpen = true;
            }
        };
    }
</script>
@endsection
