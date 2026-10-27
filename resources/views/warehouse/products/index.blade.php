@extends('layouts.app')

@section('title', 'Katalog Master Produk - Warehouse IPNET')

@push('styles')
<style>
    [x-cloak] { display: none !important; }
    .ipnet-card {
        background-color: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 16px;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.03);
    }
</style>
@endpush

@section('content')
<div class="flex h-screen overflow-hidden bg-[#F8FAFC] font-sans" x-data="productManager()">
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Master Produk Warehouse'])
        
        <div class="p-4 sm:p-6 lg:p-7 space-y-6 max-w-[1600px] mx-auto">
            
            <!-- HEADER -->
            <div class="ipnet-card p-5 sm:p-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#8F0A0D]"></span>
                            <h2 class="text-[20px] font-bold text-[#1E293B]">Katalog Master Produk &amp; Perangkat</h2>
                        </div>
                        <p class="text-[13px] text-[#64748B] mt-0.5">
                            Kelola seluruh SKU perangkat jaringan, sparepart, modul optik, dan kuantitas stok di setiap gudang.
                        </p>
                    </div>

                    <div class="flex items-center gap-3">
                        <button type="button" 
                                @click="openCreateModal()"
                                class="btn-ipnet-gradient px-4 py-2.5 rounded-xl font-bold text-[13px] shadow-sm flex items-center gap-2 cursor-pointer transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span>Tambah Produk Baru</span>
                        </button>
                    </div>
                </div>

                <!-- FILTER ROW -->
                <form action="{{ route('warehouse.products.index') }}" method="GET" class="mt-5 pt-4 border-t border-[#E2E8F0] grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                    <div>
                        <label class="block text-[11px] font-semibold text-[#64748B] mb-1">Cari Produk / SKU / Barcode</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, SKU, merk..." class="wms-input text-[12.5px]">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-[#64748B] mb-1">Kategori</label>
                        <select name="category_id" class="wms-input text-[12.5px]" onchange="this.form.submit()">
                            <option value="all">Semua Kategori</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-[#64748B] mb-1">Lokasi Gudang</label>
                        <select name="warehouse_id" class="wms-input text-[12.5px]" onchange="this.form.submit()">
                            <option value="all">Semua Titik Gudang</option>
                            @foreach($warehouses as $wh)
                                <option value="{{ $wh->id }}" {{ request('warehouse_id') == $wh->id ? 'selected' : '' }}>
                                    {{ $wh->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-[#64748B] mb-1">Status Ketersediaan</label>
                        <select name="status" class="wms-input text-[12.5px]" onchange="this.form.submit()">
                            <option value="all">Semua Status</option>
                            <option value="Tersedia" {{ request('status') === 'Tersedia' ? 'selected' : '' }}>Tersedia (Ready)</option>
                            <option value="low_stock" {{ request('status') === 'low_stock' ? 'selected' : '' }}>Stok Menipis (Alert)</option>
                            <option value="Habis" {{ request('status') === 'Habis' ? 'selected' : '' }}>Habis (Out of Stock)</option>
                        </select>
                    </div>
                    <div class="flex items-end gap-2">
                        <button type="submit" class="bg-[#1E293B] hover:bg-[#0F172A] text-white px-4 py-2.5 rounded-xl text-[12.5px] font-semibold w-full transition">
                            Filter
                        </button>
                        @if(request()->anyFilled(['search', 'category_id', 'warehouse_id', 'status']))
                            <a href="{{ route('warehouse.products.index') }}" class="bg-slate-200 hover:bg-slate-300 text-slate-700 px-3 py-2.5 rounded-xl text-[12.5px] font-semibold transition">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- PRODUCT TABLE -->
            <div class="ipnet-card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-[13px] border-collapse">
                        <thead>
                            <tr class="bg-[#F8FAFC] border-b border-[#E2E8F0] text-[#64748B] text-[11.5px] uppercase font-bold tracking-wider">
                                <th class="py-3.5 px-4">Produk &amp; SKU</th>
                                <th class="py-3.5 px-4">Kategori &amp; Satuan</th>
                                <th class="py-3.5 px-4">Lokasi Gudang</th>
                                <th class="py-3.5 px-4 text-center">Stok Fisik</th>
                                <th class="py-3.5 px-4 text-right">Estimasi Harga</th>
                                <th class="py-3.5 px-4 text-center">Status</th>
                                <th class="py-3.5 px-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#F1F5F9]">
                            @forelse($products as $product)
                                <tr class="hover:bg-[#F8FAFC]/80 transition">
                                    <!-- Produk & SKU -->
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-[#1E293B]">{{ $product->name }}</div>
                                        <div class="flex items-center gap-2 mt-0.5 text-[11px] text-[#64748B]">
                                            <span class="font-mono bg-slate-100 px-1.5 py-0.5 rounded text-slate-700 font-semibold">{{ $product->sku }}</span>
                                            @if($product->brand)
                                                <span>• Merk: <strong>{{ $product->brand }}</strong></span>
                                            @endif
                                        </div>
                                    </td>

                                    <!-- Kategori & Satuan -->
                                    <td class="py-3.5 px-4">
                                        <div class="font-semibold text-[#1E293B]">{{ $product->category_name ?: ($product->category ? $product->category->name : '-') }}</div>
                                        <div class="text-[11px] text-[#64748B]">Satuan: {{ $product->unit_name ?: 'Unit' }}</div>
                                    </td>

                                    <!-- Lokasi Gudang -->
                                    <td class="py-3.5 px-4">
                                        <div class="font-medium text-[#1E293B]">{{ $product->warehouse_name ?: ($product->warehouseLocation ? $product->warehouseLocation->name : '-') }}</div>
                                        @if($product->rack_position)
                                            <div class="text-[11px] text-[#8F0A0D] font-semibold">{{ $product->rack_position }}</div>
                                        @endif
                                    </td>

                                    <!-- Stok Fisik -->
                                    <td class="py-3.5 px-4 text-center">
                                        <div class="text-[15px] font-extrabold {{ $product->current_stock <= $product->min_stock ? 'text-amber-600' : 'text-[#1E293B]' }}">
                                            {{ $product->current_stock }}
                                        </div>
                                        <div class="text-[10px] text-[#64748B]">Min: {{ $product->min_stock }}</div>
                                    </td>

                                    <!-- Harga -->
                                    <td class="py-3.5 px-4 text-right">
                                        <div class="font-bold text-[#1E293B]">Rp {{ number_format($product->unit_price, 0, ',', '.') }}</div>
                                        <div class="text-[11px] text-[#64748B]">Total: Rp {{ number_format($product->current_stock * $product->unit_price, 0, ',', '.') }}</div>
                                    </td>

                                    <!-- Status -->
                                    <td class="py-3.5 px-4 text-center">
                                        @if($product->current_stock == 0)
                                            <span class="px-2.5 py-1 text-[11px] font-bold rounded-lg bg-red-50 text-red-700 border border-red-200">
                                                Habis
                                            </span>
                                        @elseif($product->current_stock <= $product->min_stock)
                                            <span class="px-2.5 py-1 text-[11px] font-bold rounded-lg bg-amber-50 text-amber-700 border border-amber-200">
                                                Menipis
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 text-[11px] font-bold rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                Tersedia
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Aksi -->
                                    <td class="py-3.5 px-4 text-center">
                                        <div class="inline-flex items-center gap-1.5">
                                            <button type="button" 
                                                    @click="viewDetail({{ json_encode($product) }})"
                                                    class="p-1.5 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition" 
                                                    title="Lihat Detail">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </button>
                                            <button type="button" 
                                                    @click="openEditModal({{ json_encode($product) }})"
                                                    class="p-1.5 rounded-lg text-blue-600 hover:text-blue-800 hover:bg-blue-50 transition" 
                                                    title="Edit Produk">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </button>
                                            <form action="{{ route('warehouse.products.destroy', $product->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus produk ini dari katalog warehouse?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 rounded-lg text-red-600 hover:text-red-800 hover:bg-red-50 transition" title="Hapus Produk">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-12 text-center text-[#94A3B8]">
                                        Tidak ada produk yang cocok dengan pencarian atau filter.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($products->hasPages())
                    <div class="p-4 border-t border-[#E2E8F0]">
                        {{ $products->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>

    <!-- MODAL CREATE PRODUCT -->
    <div x-show="showCreateModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.away="showCreateModal = false" class="bg-white rounded-2xl shadow-2xl border border-slate-200 max-w-2xl w-full max-h-[90vh] overflow-y-auto p-6">
            <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-200">
                <h3 class="text-[17px] font-bold text-[#1E293B]">Tambah Master Produk Baru</h3>
                <button type="button" @click="showCreateModal = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
            </div>

            <form action="{{ route('warehouse.products.store') }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[12px] font-semibold text-[#1E293B] mb-1">Nama Produk / Tipe Perangkat *</label>
                        <input type="text" name="name" required class="wms-input text-[13px]" placeholder="e.g. Cisco Catalyst 3850">
                    </div>
                    <div>
                        <label class="block text-[12px] font-semibold text-[#1E293B] mb-1">Kode SKU (Opsional, otomatis jika kosong)</label>
                        <input type="text" name="sku" class="wms-input text-[13px]" placeholder="e.g. PRD-CS-3850">
                    </div>
                    <div>
                        <label class="block text-[12px] font-semibold text-[#1E293B] mb-1">Merk / Brand</label>
                        <input type="text" name="brand" class="wms-input text-[13px]" placeholder="e.g. Cisco, Fortinet, Mikrotik">
                    </div>
                    <div>
                        <label class="block text-[12px] font-semibold text-[#1E293B] mb-1">Barcode / Serial Number Label</label>
                        <input type="text" name="barcode" class="wms-input text-[13px]" placeholder="e.g. 882658532014">
                    </div>
                    <div>
                        <label class="block text-[12px] font-semibold text-[#1E293B] mb-1">Kategori Produk</label>
                        <select name="category_id" class="wms-input text-[13px]">
                            <option value="">Pilih Kategori...</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[12px] font-semibold text-[#1E293B] mb-1">Satuan Unit</label>
                        <select name="unit_id" class="wms-input text-[13px]">
                            @foreach($units as $u)
                                <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->short_name }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[12px] font-semibold text-[#1E293B] mb-1">Supplier Rekanan</label>
                        <select name="supplier_id" class="wms-input text-[13px]">
                            <option value="">Pilih Supplier...</option>
                            @foreach($suppliers as $sup)
                                <option value="{{ $sup->id }}">{{ $sup->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[12px] font-semibold text-[#1E293B] mb-1">Gudang Penyimpanan</label>
                        <select name="warehouse_id" class="wms-input text-[13px]">
                            <option value="">Pilih Gudang...</option>
                            @foreach($warehouses as $wh)
                                <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[12px] font-semibold text-[#1E293B] mb-1">Posisi Rak / Bin</label>
                        <input type="text" name="rack_position" class="wms-input text-[13px]" placeholder="e.g. Rak A-01 / Slot 2">
                    </div>
                    <div>
                        <label class="block text-[12px] font-semibold text-[#1E293B] mb-1">Harga Satuan (IDR)</label>
                        <input type="number" name="unit_price" value="0" min="0" class="wms-input text-[13px]">
                    </div>
                    <div>
                        <label class="block text-[12px] font-semibold text-[#1E293B] mb-1">Stok Awal Fisik *</label>
                        <input type="number" name="current_stock" value="1" min="0" required class="wms-input text-[13px]">
                    </div>
                    <div>
                        <label class="block text-[12px] font-semibold text-[#1E293B] mb-1">Batas Minimum Stok (Alert) *</label>
                        <input type="number" name="min_stock" value="3" min="0" required class="wms-input text-[13px]">
                    </div>
                </div>

                <div>
                    <label class="block text-[12px] font-semibold text-[#1E293B] mb-1">Spesifikasi Teknis / Catatan Perangkat</label>
                    <textarea name="specifications" rows="2" class="wms-input text-[13px]" placeholder="Port, throughput, modular power supply, lisensi, dll"></textarea>
                </div>

                <div class="hidden">
                    <input type="hidden" name="status" value="Tersedia">
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-200">
                    <button type="button" @click="showCreateModal = false" class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 font-semibold text-[13px]">Batal</button>
                    <button type="submit" class="btn-ipnet-gradient px-5 py-2.5 rounded-xl font-bold text-[13px]">Simpan Produk</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL EDIT PRODUCT -->
    <div x-show="showEditModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.away="showEditModal = false" class="bg-white rounded-2xl shadow-2xl border border-slate-200 max-w-2xl w-full max-h-[90vh] overflow-y-auto p-6">
            <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-200">
                <h3 class="text-[17px] font-bold text-[#1E293B]">Edit Master Produk</h3>
                <button type="button" @click="showEditModal = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
            </div>

            <form :action="'/warehouse/products/' + editData.id" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[12px] font-semibold text-[#1E293B] mb-1">Nama Produk *</label>
                        <input type="text" name="name" x-model="editData.name" required class="wms-input text-[13px]">
                    </div>
                    <div>
                        <label class="block text-[12px] font-semibold text-[#1E293B] mb-1">Kode SKU *</label>
                        <input type="text" name="sku" x-model="editData.sku" required class="wms-input text-[13px]">
                    </div>
                    <div>
                        <label class="block text-[12px] font-semibold text-[#1E293B] mb-1">Merk / Brand</label>
                        <input type="text" name="brand" x-model="editData.brand" class="wms-input text-[13px]">
                    </div>
                    <div>
                        <label class="block text-[12px] font-semibold text-[#1E293B] mb-1">Barcode</label>
                        <input type="text" name="barcode" x-model="editData.barcode" class="wms-input text-[13px]">
                    </div>
                    <div>
                        <label class="block text-[12px] font-semibold text-[#1E293B] mb-1">Kategori</label>
                        <select name="category_id" x-model="editData.category_id" class="wms-input text-[13px]">
                            <option value="">Pilih Kategori...</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[12px] font-semibold text-[#1E293B] mb-1">Satuan</label>
                        <select name="unit_id" x-model="editData.unit_id" class="wms-input text-[13px]">
                            @foreach($units as $u)
                                <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->short_name }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[12px] font-semibold text-[#1E293B] mb-1">Supplier</label>
                        <select name="supplier_id" x-model="editData.supplier_id" class="wms-input text-[13px]">
                            <option value="">Pilih Supplier...</option>
                            @foreach($suppliers as $sup)
                                <option value="{{ $sup->id }}">{{ $sup->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[12px] font-semibold text-[#1E293B] mb-1">Lokasi Gudang</label>
                        <select name="warehouse_id" x-model="editData.warehouse_id" class="wms-input text-[13px]">
                            <option value="">Pilih Gudang...</option>
                            @foreach($warehouses as $wh)
                                <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[12px] font-semibold text-[#1E293B] mb-1">Posisi Rak</label>
                        <input type="text" name="rack_position" x-model="editData.rack_position" class="wms-input text-[13px]">
                    </div>
                    <div>
                        <label class="block text-[12px] font-semibold text-[#1E293B] mb-1">Harga Satuan (IDR)</label>
                        <input type="number" name="unit_price" x-model="editData.unit_price" min="0" class="wms-input text-[13px]">
                    </div>
                    <div>
                        <label class="block text-[12px] font-semibold text-[#1E293B] mb-1">Stok Fisik Saat Ini *</label>
                        <input type="number" name="current_stock" x-model="editData.current_stock" min="0" required class="wms-input text-[13px]">
                    </div>
                    <div>
                        <label class="block text-[12px] font-semibold text-[#1E293B] mb-1">Batas Minimum Stok *</label>
                        <input type="number" name="min_stock" x-model="editData.min_stock" min="0" required class="wms-input text-[13px]">
                    </div>
                </div>

                <div>
                    <label class="block text-[12px] font-semibold text-[#1E293B] mb-1">Status Ketersediaan</label>
                    <select name="status" x-model="editData.status" class="wms-input text-[13px]">
                        <option value="Tersedia">Tersedia</option>
                        <option value="Stok Menipis">Stok Menipis</option>
                        <option value="Habis">Habis</option>
                        <option value="Discontinued">Discontinued</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[12px] font-semibold text-[#1E293B] mb-1">Spesifikasi</label>
                    <textarea name="specifications" x-model="editData.specifications" rows="2" class="wms-input text-[13px]"></textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-200">
                    <button type="button" @click="showEditModal = false" class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 font-semibold text-[13px]">Batal</button>
                    <button type="submit" class="btn-ipnet-gradient px-5 py-2.5 rounded-xl font-bold text-[13px]">Perbarui Produk</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL DETAIL PRODUCT -->
    <div x-show="showDetailModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.away="showDetailModal = false" class="bg-white rounded-2xl shadow-2xl border border-slate-200 max-w-lg w-full p-6">
            <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-200">
                <div>
                    <span class="text-[11px] font-mono font-bold text-[#8F0A0D]" x-text="detailData.sku"></span>
                    <h3 class="text-[18px] font-bold text-[#1E293B]" x-text="detailData.name"></h3>
                </div>
                <button type="button" @click="showDetailModal = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
            </div>

            <div class="space-y-3 text-[13px]">
                <div class="grid grid-cols-2 gap-2 bg-slate-50 p-3 rounded-xl border border-slate-200">
                    <div>
                        <span class="text-[11px] text-[#64748B]">Merk / Brand</span>
                        <div class="font-bold text-[#1E293B]" x-text="detailData.brand || '-'"></div>
                    </div>
                    <div>
                        <span class="text-[11px] text-[#64748B]">Barcode</span>
                        <div class="font-mono text-[#1E293B]" x-text="detailData.barcode || '-'"></div>
                    </div>
                    <div>
                        <span class="text-[11px] text-[#64748B]">Kategori</span>
                        <div class="font-bold text-[#1E293B]" x-text="detailData.category_name || '-'"></div>
                    </div>
                    <div>
                        <span class="text-[11px] text-[#64748B]">Satuan</span>
                        <div class="font-bold text-[#1E293B]" x-text="detailData.unit_name || 'Unit'"></div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2 bg-slate-50 p-3 rounded-xl border border-slate-200">
                    <div>
                        <span class="text-[11px] text-[#64748B]">Lokasi Gudang</span>
                        <div class="font-bold text-[#1E293B]" x-text="detailData.warehouse_name || '-'"></div>
                    </div>
                    <div>
                        <span class="text-[11px] text-[#64748B]">Posisi Rak</span>
                        <div class="font-bold text-[#8F0A0D]" x-text="detailData.rack_position || '-'"></div>
                    </div>
                    <div>
                        <span class="text-[11px] text-[#64748B]">Stok Saat Ini</span>
                        <div class="text-[16px] font-extrabold text-[#1E293B]" x-text="detailData.current_stock + ' ' + (detailData.unit_name || 'Unit')"></div>
                    </div>
                    <div>
                        <span class="text-[11px] text-[#64748B]">Stok Minimum</span>
                        <div class="font-bold text-slate-700" x-text="detailData.min_stock"></div>
                    </div>
                </div>

                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                    <span class="text-[11px] text-[#64748B]">Estimasi Harga Satuan</span>
                    <div class="text-[16px] font-extrabold text-emerald-700" x-text="'Rp ' + (new Intl.NumberFormat('id-ID').format(detailData.unit_price || 0))"></div>
                </div>

                <div x-show="detailData.specifications" class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                    <span class="text-[11px] text-[#64748B] block mb-1">Spesifikasi</span>
                    <p class="text-[12.5px] text-[#1E293B] leading-relaxed" x-text="detailData.specifications"></p>
                </div>
            </div>

            <div class="mt-5 pt-3 border-t border-slate-200 flex justify-end">
                <button type="button" @click="showDetailModal = false" class="btn-ipnet-gradient px-4 py-2 rounded-xl text-[13px] font-bold">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
function productManager() {
    return {
        showCreateModal: false,
        showEditModal: false,
        showDetailModal: false,
        editData: {},
        detailData: {},
        openCreateModal() {
            this.showCreateModal = true;
        },
        openEditModal(product) {
            this.editData = Object.assign({}, product);
            this.showEditModal = true;
        },
        viewDetail(product) {
            this.detailData = product;
            this.showDetailModal = true;
        }
    };
}
</script>
@endsection
