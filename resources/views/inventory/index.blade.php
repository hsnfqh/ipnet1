@extends('layouts.app')

@section('title', 'Inventory - PT IP Network Solusindo')

@push('styles')
<style>
    :root {
        --ipnet-primary: #8F0A0D;
        --ipnet-primary-hover: #73080A;
        --ipnet-card-bg: #FFFFFF;
        --ipnet-card-border: #E2E8F0;
        --ipnet-text-main: #1E293B;
    }

    .btn-ipnet-primary {
        background: #DC2626;
        color: #FFFFFF;
        font-weight: 700;
        transition: all 0.2s ease;
        box-shadow: 0 2px 4px rgba(220, 38, 38, 0.2);
    }

    .btn-ipnet-primary:hover {
        background: #B91C1C;
        box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
        transform: translateY(-1px);
    }

    .btn-slate-stockout {
        background: #64748B;
        color: #FFFFFF;
        font-weight: 700;
        transition: all 0.2s ease;
        box-shadow: 0 2px 4px rgba(100, 116, 139, 0.2);
    }

    .btn-slate-stockout:hover {
        background: #475569;
        box-shadow: 0 4px 12px rgba(100, 116, 139, 0.3);
        transform: translateY(-1px);
    }
</style>
@endpush

@section('content')
<div class="flex h-screen overflow-hidden bg-[#F8FAFC] font-sans" x-data="inventoryManagerPage()">
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Inventory'])
        
        <div class="p-4 sm:p-6 lg:p-8 space-y-6 max-w-7xl mx-auto">
            
            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 font-bold px-2">✕</button>
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-xs font-semibold flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700 font-bold px-2">✕</button>
                </div>
            @endif

            {{-- HEADER: Title with Icon matching reference --}}
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-red-50 border border-red-200 text-[#8F0A0D] flex items-center justify-center">
                        <svg class="w-5 h-5 text-[#8F0A0D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <h1 class="text-2xl font-bold text-[#1E293B] tracking-tight">Inventory</h1>
                </div>

                <button type="button" @click="openAddProductModal()" 
                        class="text-xs font-bold text-slate-500 hover:text-red-700 underline cursor-pointer">
                    + Tambah Produk Baru
                </button>
            </div>

            {{-- CONTROLS BAR (Matching Reference Image) --}}
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
                {{-- Search Input --}}
                <form method="GET" action="{{ route('inventory.index') }}" class="relative w-full sm:w-80">
                    <svg class="w-4 h-4 absolute left-3.5 top-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="Search" 
                           class="w-full pl-9 pr-3.5 py-2.5 rounded-xl border border-gray-200 text-xs font-semibold text-gray-800 bg-white focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-all shadow-xs">
                </form>

                {{-- Action Buttons: Barang Keluar & Barang Masuk (Matching Reference Image) --}}
                <div class="flex items-center gap-3 shrink-0">
                    <button type="button" 
                            @click="openStockOutModal()"
                            class="btn-slate-stockout px-5 py-2.5 rounded-xl text-xs font-bold cursor-pointer whitespace-nowrap">
                        Barang Keluar
                    </button>

                    <button type="button" 
                            @click="openStockInModal()"
                            class="btn-ipnet-primary px-5 py-2.5 rounded-xl text-xs font-bold cursor-pointer whitespace-nowrap">
                        Barang Masuk
                    </button>
                </div>
            </div>

            {{-- INVENTORY TABLE (Matching Reference Image) --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="border-b border-slate-200 text-slate-500 uppercase text-[10.5px] font-bold">
                                <th class="py-3.5 px-5 font-bold">KODE PRODUK</th>
                                <th class="py-3.5 px-5 font-bold">NAMA PRODUK</th>
                                <th class="py-3.5 px-5 font-bold text-center">QTY</th>
                                <th class="py-3.5 px-5 font-bold">KATEGORI</th>
                                <th class="py-3.5 px-5 font-bold">TANGGAL</th>
                                <th class="py-3.5 px-5 font-bold text-center">STOK AKHIR</th>
                                <th class="py-3.5 px-5 font-bold text-center">STATUS</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium text-slate-800">
                            @forelse($items as $item)
                                @php
                                    $latestTx = $item->transactions->first();
                                    $lastDate = $latestTx ? \Carbon\Carbon::parse($latestTx->transaction_date)->format('d/m/Y') : \Carbon\Carbon::parse($item->updated_at)->format('d/m/Y');
                                    $lastQty = $latestTx ? ($latestTx->type === 'in' ? "+{$latestTx->qty}" : "-{$latestTx->qty}") : "{$item->stock}";
                                @endphp
                                <tr class="hover:bg-slate-50/70 transition">
                                    {{-- Kode Produk --}}
                                    <td class="py-3.5 px-5 font-mono font-bold text-slate-900">
                                        {{ $item->product_code }}
                                    </td>

                                    {{-- Nama Produk --}}
                                    <td class="py-3.5 px-5 font-bold text-slate-900">
                                        {{ $item->product_name }}
                                        @if($item->description)
                                            <div class="text-[11px] text-slate-400 font-normal truncate max-w-xs">{{ $item->description }}</div>
                                        @endif
                                    </td>

                                    {{-- QTY Mutasi Terakhir --}}
                                    <td class="py-3.5 px-5 text-center font-bold {{ $latestTx && $latestTx->type === 'out' ? 'text-amber-600' : 'text-emerald-600' }}">
                                        {{ $lastQty }}
                                    </td>

                                    {{-- Kategori --}}
                                    <td class="py-3.5 px-5 text-slate-600">
                                        {{ $item->category ?: 'General' }}
                                    </td>

                                    {{-- Tanggal --}}
                                    <td class="py-3.5 px-5 text-slate-500 font-mono">
                                        {{ $lastDate }}
                                    </td>

                                    {{-- Stok Akhir --}}
                                    <td class="py-3.5 px-5 text-center font-bold text-slate-900">
                                        {{ $item->stock }} <span class="text-[10px] text-slate-400 font-normal">{{ $item->unit }}</span>
                                    </td>

                                    {{-- Status --}}
                                    <td class="py-3.5 px-5 text-center whitespace-nowrap">
                                        @if($item->stock > 5)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10.5px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                Tersedia
                                            </span>
                                        @elseif($item->stock > 0)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10.5px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                Menipis
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10.5px] font-bold bg-red-50 text-red-700 border border-red-200">
                                                Habis
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-12 text-center text-slate-400 text-xs">
                                        Tidak ada data inventory produk.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination (Matching 0-0 from 0 < > format) --}}
                <div class="p-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                    <div>
                        {{ $items->firstItem() ?? 0 }}-{{ $items->lastItem() ?? 0 }} from {{ $items->total() }}
                    </div>
                    <div>
                        @if($items->hasPages())
                            {{ $items->links() }}
                        @else
                            <div class="flex items-center gap-2">
                                <button disabled class="p-1 rounded border border-slate-200 text-slate-300">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                </button>
                                <button disabled class="p-1 rounded border border-slate-200 text-slate-300">
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
                    <select name="inventory_item_id" required class="w-full px-3.5 py-2 rounded-xl border border-gray-200 text-xs text-gray-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500 cursor-pointer">
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
                               class="w-full px-3.5 py-2 rounded-xl border border-gray-200 text-xs font-normal text-gray-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                    </div>
                    <div>
                        <label class="block text-gray-700 mb-1">Tanggal Transaksi <span class="text-red-500">*</span></label>
                        <input type="date" name="transaction_date" value="{{ date('Y-m-d') }}" required
                               class="w-full px-3.5 py-2 rounded-xl border border-gray-200 text-xs font-normal text-gray-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                    </div>
                </div>

                <div>
                    <label class="block text-gray-700 mb-1">No. Referensi PO / DO / Vendor</label>
                    <input type="text" name="reference_no" placeholder="Contoh: PO-2026/09/001"
                           class="w-full px-3.5 py-2 rounded-xl border border-gray-200 text-xs font-normal text-gray-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                </div>

                <div>
                    <label class="block text-gray-700 mb-1">Keterangan / Supplier</label>
                    <textarea name="notes" rows="2" placeholder="Nama distributor, vendor pengirim, nomor surat jalan..."
                              class="w-full px-3.5 py-2 rounded-xl border border-gray-200 text-xs font-normal text-gray-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t">
                    <button type="button" @click="isStockInModalOpen = false" class="px-4 py-2 rounded-xl border border-gray-200 text-gray-600 font-bold hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="submit" class="btn-ipnet-primary px-5 py-2 rounded-xl font-bold shadow-md">
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
                    <select name="inventory_item_id" required class="w-full px-3.5 py-2 rounded-xl border border-gray-200 text-xs text-gray-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500 cursor-pointer">
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
                               class="w-full px-3.5 py-2 rounded-xl border border-gray-200 text-xs font-normal text-gray-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                    </div>
                    <div>
                        <label class="block text-gray-700 mb-1">Tanggal Keluar <span class="text-red-500">*</span></label>
                        <input type="date" name="transaction_date" value="{{ date('Y-m-d') }}" required
                               class="w-full px-3.5 py-2 rounded-xl border border-gray-200 text-xs font-normal text-gray-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                    </div>
                </div>

                <div>
                    <label class="block text-gray-700 mb-1">No. Project / DO / SPK</label>
                    <input type="text" name="reference_no" placeholder="Contoh: PRJ-TELKOM-2026 / DO-009"
                           class="w-full px-3.5 py-2 rounded-xl border border-gray-200 text-xs font-normal text-gray-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                </div>

                <div>
                    <label class="block text-gray-700 mb-1">Tujuan Pengiriman / Penerima</label>
                    <textarea name="notes" rows="2" placeholder="Nama PIC engineer lapangan, lokasi site instalasi..."
                              class="w-full px-3.5 py-2 rounded-xl border border-gray-200 text-xs font-normal text-gray-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t">
                    <button type="button" @click="isStockOutModalOpen = false" class="px-4 py-2 rounded-xl border border-gray-200 text-gray-600 font-bold hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="submit" class="btn-slate-stockout px-5 py-2 rounded-xl font-bold shadow-md">
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
                               class="w-full px-3.5 py-2 rounded-xl border border-gray-200 text-xs font-normal text-gray-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                    </div>
                    <div>
                        <label class="block text-gray-700 mb-1">Kategori</label>
                        <input type="text" name="category" placeholder="Switch, Router, AP..."
                               class="w-full px-3.5 py-2 rounded-xl border border-gray-200 text-xs font-normal text-gray-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                    </div>
                </div>

                <div>
                    <label class="block text-gray-700 mb-1">Nama Produk / Tipe <span class="text-red-500">*</span></label>
                    <input type="text" name="product_name" required placeholder="Contoh: Cisco Catalyst 2960-X 48 Port Gigabit"
                           class="w-full px-3.5 py-2 rounded-xl border border-gray-200 text-xs font-normal text-gray-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block text-gray-700 mb-1">Stok Awal <span class="text-red-500">*</span></label>
                        <input type="number" name="stock" min="0" required placeholder="0"
                               class="w-full px-3.5 py-2 rounded-xl border border-gray-200 text-xs font-normal text-gray-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                    </div>
                    <div>
                        <label class="block text-gray-700 mb-1">Satuan <span class="text-red-500">*</span></label>
                        <input type="text" name="unit" required placeholder="Unit / Pcs / Box" value="Unit"
                               class="w-full px-3.5 py-2 rounded-xl border border-gray-200 text-xs font-normal text-gray-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                    </div>
                    <div>
                        <label class="block text-gray-700 mb-1">Harga Satuan</label>
                        <input type="number" name="unit_price" min="0" placeholder="0"
                               class="w-full px-3.5 py-2 rounded-xl border border-gray-200 text-xs font-normal text-gray-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                    </div>
                </div>

                <div>
                    <label class="block text-gray-700 mb-1">Deskripsi Spesifikasi</label>
                    <textarea name="description" rows="2" placeholder="Spesifikasi teknis produk..."
                              class="w-full px-3.5 py-2 rounded-xl border border-gray-200 text-xs font-normal text-gray-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t">
                    <button type="button" @click="isAddProductModalOpen = false" class="px-4 py-2 rounded-xl border border-gray-200 text-gray-600 font-bold hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="submit" class="btn-ipnet-primary px-5 py-2 rounded-xl font-bold shadow-md">
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
