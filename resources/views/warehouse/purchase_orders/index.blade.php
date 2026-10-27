@extends('layouts.app')

@section('title', 'Purchase Order (PO) - Warehouse IPNET')

@push('styles')
<style>
    [x-cloak] { display: none !important; }
    .ipnet-card {
        background-color: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 16px;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.03);
    }
    @media print {
        body * { visibility: hidden; }
        #print-area, #print-area * { visibility: visible; }
        #print-area { position: absolute; left: 0; top: 0; width: 100%; }
    }
</style>
@endpush

@section('content')
<div class="flex h-screen overflow-hidden bg-[#F8FAFC] font-sans" x-data="poManager()">
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Purchase Order (PO) Pengadaan'])
        
        <div class="p-4 sm:p-6 lg:p-7 space-y-6 max-w-[1600px] mx-auto">
            
            <!-- HEADER -->
            <div class="ipnet-card p-5 sm:p-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#8F0A0D]"></span>
                            <h2 class="text-[20px] font-bold text-[#1E293B]">Purchase Order &amp; Pengadaan Barang Masuk</h2>
                        </div>
                        <p class="text-[13px] text-[#64748B] mt-0.5">
                            Penerbitan surat pesanan pembelian ke distributor resmi, tracking estimasi tiba, dan penerimaan stok fisik ke gudang.
                        </p>
                    </div>

                    <div class="flex items-center gap-3">
                        <button type="button" 
                                @click="openCreateModal()"
                                class="btn-ipnet-gradient px-4 py-2.5 rounded-xl font-bold text-[13px] shadow-sm flex items-center gap-2 cursor-pointer transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span>Buat PO Baru</span>
                        </button>
                    </div>
                </div>

                <!-- FILTER ROW -->
                <form action="{{ route('warehouse.purchase_orders.index') }}" method="GET" class="mt-5 pt-4 border-t border-[#E2E8F0] grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    <div>
                        <label class="block text-[11px] font-semibold text-[#64748B] mb-1">Cari No PO / Catatan</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="PO-IPN-..." class="wms-input text-[12.5px]">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-[#64748B] mb-1">Supplier</label>
                        <select name="supplier_id" class="wms-input text-[12.5px]" onchange="this.form.submit()">
                            <option value="all">Semua Supplier</option>
                            @foreach($suppliers as $sup)
                                <option value="{{ $sup->id }}" {{ request('supplier_id') == $sup->id ? 'selected' : '' }}>
                                    {{ $sup->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-[#64748B] mb-1">Status PO</label>
                        <select name="status" class="wms-input text-[12.5px]" onchange="this.form.submit()">
                            <option value="all">Semua Status</option>
                            <option value="Draft" {{ request('status') === 'Draft' ? 'selected' : '' }}>Draft</option>
                            <option value="Diproses" {{ request('status') === 'Diproses' ? 'selected' : '' }}>Diproses</option>
                            <option value="Disetujui" {{ request('status') === 'Disetujui' ? 'selected' : '' }}>Disetujui</option>
                            <option value="Selesai" {{ request('status') === 'Selesai' ? 'selected' : '' }}>Selesai (Diterima)</option>
                            <option value="Dibatalkan" {{ request('status') === 'Dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                        </select>
                    </div>
                    <div class="flex items-end gap-2">
                        <button type="submit" class="bg-[#1E293B] hover:bg-[#0F172A] text-white px-4 py-2.5 rounded-xl text-[12.5px] font-semibold w-full transition">
                            Filter
                        </button>
                        @if(request()->anyFilled(['search', 'supplier_id', 'status']))
                            <a href="{{ route('warehouse.purchase_orders.index') }}" class="bg-slate-200 hover:bg-slate-300 text-slate-700 px-3 py-2.5 rounded-xl text-[12.5px] font-semibold transition">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- PO TABLE -->
            <div class="ipnet-card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-[13px] border-collapse">
                        <thead>
                            <tr class="bg-[#F8FAFC] border-b border-[#E2E8F0] text-[#64748B] text-[11.5px] uppercase font-bold tracking-wider">
                                <th class="py-3.5 px-4">Nomor &amp; Tanggal PO</th>
                                <th class="py-3.5 px-4">Supplier &amp; Gudang Tujuan</th>
                                <th class="py-3.5 px-4 text-center">Item Dipesan</th>
                                <th class="py-3.5 px-4 text-right">Nilai Pembelian</th>
                                <th class="py-3.5 px-4 text-center">Estimasi Tiba</th>
                                <th class="py-3.5 px-4 text-center">Status</th>
                                <th class="py-3.5 px-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#F1F5F9]">
                            @forelse($purchaseOrders as $po)
                                <tr class="hover:bg-[#F8FAFC]/80 transition">
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-[#1E293B] text-[14px]">{{ $po->po_number }}</div>
                                        <div class="text-[11px] text-[#64748B] mt-0.5">
                                            Tanggal: <strong>{{ $po->po_date->format('d M Y') }}</strong>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <div class="font-semibold text-[#1E293B]">{{ $po->supplier_name ?: '-' }}</div>
                                        <div class="text-[11px] text-[#8F0A0D] font-medium">Tujuan: {{ $po->warehouse_name ?: 'Gudang Utama' }}</div>
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        <span class="inline-block px-2.5 py-1 text-[11px] font-bold rounded-lg bg-slate-100 text-slate-700">
                                            {{ $po->items->count() }} Jenis Item
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-right">
                                        <div class="font-extrabold text-[#1E293B]">Rp {{ number_format($po->total_amount, 0, ',', '.') }}</div>
                                    </td>
                                    <td class="py-3.5 px-4 text-center text-[12px] text-[#64748B]">
                                        {{ $po->expected_date ? $po->expected_date->format('d M Y') : '-' }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        @php
                                            $poBadge = match($po->status) {
                                                'Selesai' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                                'Disetujui' => 'bg-blue-50 text-blue-700 border-blue-200',
                                                'Diproses' => 'bg-amber-50 text-amber-700 border-amber-200',
                                                'Dibatalkan' => 'bg-red-50 text-red-700 border-red-200',
                                                default => 'bg-slate-100 text-slate-700 border-slate-200'
                                            };
                                        @endphp
                                        <span class="px-2.5 py-1 text-[11px] font-bold rounded-md border {{ $poBadge }}">
                                            {{ $po->status }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        <div class="inline-flex items-center gap-1.5">
                                            <button type="button" 
                                                    @click="viewDetail({{ json_encode($po) }})"
                                                    class="p-1.5 rounded-lg text-slate-600 hover:bg-slate-100 transition" title="Lihat & Cetak PO">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </button>
                                            @if($po->status !== 'Selesai')
                                                <form action="{{ route('warehouse.purchase_orders.status', $po->id) }}" method="POST" class="inline" onsubmit="return confirm('Konfirmasi penerimaan barang dan selesaikan PO ini? Stok produk akan otomatis bertambah.')">
                                                    @csrf
                                                    <input type="hidden" name="status" value="Selesai">
                                                    <button type="submit" class="p-1.5 rounded-lg text-emerald-600 hover:bg-emerald-50 transition" title="Terima Barang & Selesaikan PO">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                                        </svg>
                                                    </button>
                                                </form>
                                            @endif
                                            <form action="{{ route('warehouse.purchase_orders.destroy', $po->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus Purchase Order ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 rounded-lg text-red-600 hover:bg-red-50 transition" title="Hapus PO">
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
                                        Belum ada Purchase Order yang tercatat.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($purchaseOrders->hasPages())
                    <div class="p-4 border-t border-[#E2E8F0]">
                        {{ $purchaseOrders->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>

    <!-- MODAL CREATE PO (Gambar 2 Style) -->
    <template x-teleport="body">
        <div x-show="showCreateModal" 
             x-cloak 
             class="fixed inset-0 bg-[#0F172A]/60 z-[99999] flex items-center justify-center p-3 sm:p-5 backdrop-blur-xs">
            <div @click.away="showCreateModal = false" 
                 class="bg-white rounded-2xl w-[860px] max-w-full max-h-[92vh] flex flex-col text-left shadow-[0_20px_60px_rgba(15,23,42,0.25)] border border-[#E2E8F0] overflow-hidden">
                
                <!-- Fixed Header -->
                <div class="px-6 py-4 bg-[#F8FAFC] border-b border-[#E2E8F0] shrink-0 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-red-50 text-[#8F0A0D] border border-red-100 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-800">Terbitkan Purchase Order Baru</h3>
                            <p class="text-[11px] text-slate-500">Pemesanan pengadaan perangkat ke distributor resmi &amp; tracking barang masuk</p>
                        </div>
                    </div>
                    <button type="button" @click="showCreateModal = false" class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-200/60 flex items-center justify-center text-lg transition">
                        &times;
                    </button>
                </div>

                <!-- Scrollable Body -->
                <form id="createPoForm" action="{{ route('warehouse.purchase_orders.store') }}" method="POST" class="flex flex-col flex-1 min-h-0">
                    @csrf
                    <div class="p-6 overflow-y-auto space-y-5 text-xs flex-1 min-h-0">

                        <!-- SECTION 1 -->
                        <div class="space-y-3">
                            <div class="flex items-center gap-2 text-xs font-bold text-slate-800 uppercase tracking-wider">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#8F0A0D]"></span>
                                <span>1. INFORMASI PURCHASE ORDER &amp; REKANAN</span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Nomor PO (Opsional)</label>
                                    <input type="text" name="po_number" class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-[#8F0A0D] text-slate-800 bg-white" placeholder="Otomatis sistem jika kosong">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Tanggal Terbit PO <span class="text-red-500">*</span></label>
                                    <input type="date" name="po_date" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-[#8F0A0D] text-slate-800 bg-white">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Supplier / Mitra <span class="text-red-500">*</span></label>
                                    <select name="supplier_id" required class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-[#8F0A0D] text-slate-800 bg-white">
                                        <option value="">-- Pilih Supplier --</option>
                                        @foreach($suppliers as $sup)
                                            <option value="{{ $sup->id }}">{{ $sup->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Gudang Penerima <span class="text-red-500">*</span></label>
                                    <select name="warehouse_id" required class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-[#8F0A0D] text-slate-800 bg-white">
                                        @foreach($warehouses as $wh)
                                            <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- SECTION 2 -->
                        <div class="space-y-3 pt-2 border-t border-slate-100">
                            <div class="flex items-center gap-2 text-xs font-bold text-slate-800 uppercase tracking-wider">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#8F0A0D]"></span>
                                <span>2. JADWAL PENGIRIMAN &amp; STATUS PO</span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Estimasi Tanggal Tiba</label>
                                    <input type="date" name="expected_date" class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-[#8F0A0D] text-slate-800 bg-white">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Status Awal Purchase Order</label>
                                    <select name="status" class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-[#8F0A0D] text-slate-800 bg-white">
                                        <option value="Draft">Draft</option>
                                        <option value="Diproses" selected>Diproses</option>
                                        <option value="Disetujui">Disetujui</option>
                                        <option value="Selesai">Selesai (Langsung Tambah Stok Fisik)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- SECTION 3 -->
                        <div class="space-y-3 pt-2 border-t border-slate-100">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2 text-xs font-bold text-slate-800 uppercase tracking-wider">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#8F0A0D]"></span>
                                    <span>3. DAFTAR ITEM PERANGKAT YANG DIPESAN</span>
                                </div>
                                <button type="button" @click="addItemRow()" class="text-xs font-bold text-[#8F0A0D] hover:underline flex items-center gap-1">
                                    + Tambah Baris Produk
                                </button>
                            </div>

                            <div class="space-y-2.5">
                                <template x-for="(row, index) in itemRows" :key="index">
                                    <div class="grid grid-cols-12 gap-2.5 items-center bg-[#F8FAFC] p-3 rounded-xl border border-slate-200">
                                        <!-- Product Dropdown -->
                                        <div class="col-span-12 sm:col-span-5">
                                            <label class="block text-[10.5px] text-slate-600 font-semibold mb-1">Pilih Produk / Perangkat *</label>
                                            <select :name="'items[' + index + '][product_id]'" 
                                                    x-model="row.product_id"
                                                    @change="onProductSelect(index)"
                                                    class="w-full px-2.5 py-1.5 border border-slate-200 rounded-lg outline-none focus:border-[#8F0A0D] text-slate-800 bg-white text-xs">
                                                <option value="">-- Pilih dari Katalog Produk --</option>
                                                <template x-for="p in productsList" :key="p.id">
                                                    <option :value="p.id" x-text="p.name + (p.sku ? ' (' + p.sku + ')' : '')"></option>
                                                </template>
                                                <option value="custom">-- Input Perangkat Baru Lainnya --</option>
                                            </select>
                                            <input type="text" 
                                                   :name="'items[' + index + '][product_name]'" 
                                                   x-model="row.product_name" 
                                                   x-show="row.product_id === 'custom' || !row.product_id"
                                                   placeholder="Ketik nama perangkat baru..."
                                                   class="w-full px-2.5 py-1.5 mt-1.5 border border-amber-300 rounded-lg outline-none focus:border-[#8F0A0D] text-slate-800 bg-amber-50/50 text-xs">
                                        </div>

                                        <!-- Quantity -->
                                        <div class="col-span-4 sm:col-span-2">
                                            <label class="block text-[10.5px] text-slate-600 font-semibold mb-1">Qty</label>
                                            <input type="number" 
                                                   :name="'items[' + index + '][quantity]'" 
                                                   x-model.number="row.quantity" 
                                                   min="1" 
                                                   required 
                                                   class="w-full px-2.5 py-1.5 border border-slate-200 rounded-lg outline-none focus:border-[#8F0A0D] text-slate-800 bg-white text-xs">
                                        </div>

                                        <!-- Satuan Dropdown -->
                                        <div class="col-span-4 sm:col-span-2">
                                            <label class="block text-[10.5px] text-slate-600 font-semibold mb-1">Satuan</label>
                                            <select :name="'items[' + index + '][unit]'" 
                                                    x-model="row.unit" 
                                                    required 
                                                    class="w-full px-2 py-1.5 border border-slate-200 rounded-lg outline-none focus:border-[#8F0A0D] text-slate-800 bg-white text-xs">
                                                <option value="Unit">Unit</option>
                                                <option value="Pcs">Pcs</option>
                                                <option value="Box">Box</option>
                                                <option value="Roll">Roll</option>
                                                <option value="Meter">Meter</option>
                                                <option value="Set">Set</option>
                                                <option value="Pack">Pack</option>
                                            </select>
                                        </div>

                                        <!-- Harga Satuan -->
                                        <div class="col-span-3 sm:col-span-2">
                                            <label class="block text-[10.5px] text-slate-600 font-semibold mb-1">Harga Satuan (Rp)</label>
                                            <input type="number" 
                                                   :name="'items[' + index + '][unit_price]'" 
                                                   x-model.number="row.unit_price" 
                                                   min="0" 
                                                   required 
                                                   class="w-full px-2.5 py-1.5 border border-slate-200 rounded-lg outline-none focus:border-[#8F0A0D] text-slate-800 bg-white text-xs">
                                        </div>

                                        <!-- Delete row -->
                                        <div class="col-span-1 text-center pt-4">
                                            <button type="button" 
                                                    @click="removeItemRow(index)" 
                                                    class="w-7 h-7 rounded-lg text-red-500 hover:bg-red-50 hover:text-red-700 flex items-center justify-center text-base font-bold transition mx-auto" 
                                                    x-show="itemRows.length > 1">
                                                &times;
                                            </button>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <!-- TOTAL SUMMARY -->
                            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between text-xs">
                                <span class="font-semibold text-slate-700">Estimasi Total Biaya Pengadaan:</span>
                                <strong class="text-base font-extrabold text-[#8F0A0D]" x-text="'Rp ' + (new Intl.NumberFormat('id-ID').format(calculateTotal()))"></strong>
                            </div>
                        </div>

                        <!-- SECTION 4 -->
                        <div class="space-y-3 pt-2 border-t border-slate-100">
                            <div class="flex items-center gap-2 text-xs font-bold text-slate-800 uppercase tracking-wider">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#8F0A0D]"></span>
                                <span>4. SYARAT PEMBAYARAN &amp; CATATAN PENGADAAN</span>
                            </div>
                            <div>
                                <textarea name="notes" rows="2" class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-[#8F0A0D] text-slate-800 bg-white text-xs" placeholder="Syarat serah terima, ketentuan garansi distributor, syarat pembayaran tempo..."></textarea>
                            </div>
                        </div>

                    </div>

                    <!-- Fixed Footer -->
                    <div class="px-6 py-3.5 bg-[#F8FAFC] border-t border-[#E2E8F0] flex items-center justify-end gap-3 shrink-0">
                        <button type="button" @click="showCreateModal = false" class="px-4 py-2 rounded-xl bg-white border border-[#CBD5E1] text-[#334155] font-bold text-xs hover:bg-[#F1F5F9] transition">
                            Batal
                        </button>
                        <button type="submit" class="btn-ipnet-gradient py-2.5 px-5 rounded-xl font-bold text-xs text-white shadow-sm flex items-center gap-2 cursor-pointer transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Terbitkan Purchase Order</span>
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </template>

    <!-- MODAL DETAIL & CETAK PO (Gambar 2 Style) -->
    <template x-teleport="body">
        <div x-show="showDetailModal" 
             x-cloak 
             class="fixed inset-0 bg-[#0F172A]/60 z-[99999] flex items-center justify-center p-3 sm:p-5 backdrop-blur-xs">
            <div @click.away="showDetailModal = false" 
                 class="bg-white rounded-2xl w-[760px] max-w-full max-h-[92vh] flex flex-col text-left shadow-[0_20px_60px_rgba(15,23,42,0.25)] border border-[#E2E8F0] overflow-hidden">
                
                <!-- Fixed Header -->
                <div class="px-6 py-4 bg-[#F8FAFC] border-b border-[#E2E8F0] shrink-0 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-700 border border-blue-100 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-sm font-bold text-slate-800" x-text="detailData.po_number"></h3>
                                <span class="text-[10.5px] font-bold px-2 py-0.5 rounded-md bg-blue-50 text-blue-700 border border-blue-200" x-text="detailData.status"></span>
                            </div>
                            <p class="text-[11px] text-slate-500">Lembar Purchase Order resmi pengadaan perangkat logistik</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="window.print()" class="px-3 py-1.5 rounded-lg border border-slate-300 text-slate-700 font-semibold text-xs hover:bg-slate-100 flex items-center gap-1.5 transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                            </svg>
                            <span>Cetak PO</span>
                        </button>
                        <button type="button" @click="showDetailModal = false" class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-200/60 flex items-center justify-center text-lg transition">
                            &times;
                        </button>
                    </div>
                </div>

                <!-- Printable Sheet Body -->
                <div class="p-6 overflow-y-auto space-y-4 text-xs flex-1 min-h-0" id="print-area">
                    <div class="border-b-2 border-[#8F0A0D] pb-3 flex items-center justify-between">
                        <div>
                            <h2 class="text-base font-extrabold text-[#8F0A0D]">PT IP NETWORK SOLUSINDO</h2>
                            <p class="text-[11px] text-slate-500">Official Purchase Order &amp; Procurement Document</p>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-mono font-bold text-slate-800" x-text="detailData.po_number"></div>
                            <div class="text-[11px] text-slate-500" x-text="'Tanggal: ' + (detailData.po_date ? detailData.po_date.substring(0,10) : '')"></div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 bg-[#F8FAFC] p-3 rounded-xl border border-slate-200">
                        <div>
                            <span class="text-[10.5px] text-slate-500 block font-semibold">Supplier / Mitra:</span>
                            <div class="font-bold text-slate-800 text-xs" x-text="detailData.supplier_name || '-'"></div>
                        </div>
                        <div>
                            <span class="text-[10.5px] text-slate-500 block font-semibold">Gudang Penerima:</span>
                            <div class="font-bold text-[#8F0A0D] text-xs" x-text="detailData.warehouse_name || 'Gudang Utama IPNET'"></div>
                        </div>
                    </div>

                    <div>
                        <h4 class="font-bold text-slate-800 mb-2 text-xs">Rincian Barang yang Dipesan:</h4>
                        <table class="w-full text-left text-xs border border-slate-200 rounded-xl overflow-hidden">
                            <thead class="bg-slate-100 text-slate-700 font-bold">
                                <tr>
                                    <th class="p-2.5 border-b border-slate-200">Nama Barang</th>
                                    <th class="p-2.5 border-b border-slate-200 text-center">Qty</th>
                                    <th class="p-2.5 border-b border-slate-200 text-right">Harga Satuan</th>
                                    <th class="p-2.5 border-b border-slate-200 text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <template x-for="item in (detailData.items || [])" :key="item.id">
                                    <tr>
                                        <td class="p-2.5 font-medium text-slate-800" x-text="item.product_name"></td>
                                        <td class="p-2.5 text-center text-slate-700" x-text="item.quantity + ' ' + item.unit"></td>
                                        <td class="p-2.5 text-right text-slate-700" x-text="'Rp ' + (new Intl.NumberFormat('id-ID').format(item.unit_price))"></td>
                                        <td class="p-2.5 text-right font-bold text-slate-900" x-text="'Rp ' + (new Intl.NumberFormat('id-ID').format(item.subtotal))"></td>
                                    </tr>
                                </template>
                            </tbody>
                            <tfoot class="bg-slate-50 font-bold">
                                <tr>
                                    <td colspan="3" class="p-2.5 text-right text-slate-700">Total Nilai PO:</td>
                                    <td class="p-2.5 text-right text-[#8F0A0D] text-sm" x-text="'Rp ' + (new Intl.NumberFormat('id-ID').format(detailData.total_amount || 0))"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div x-show="detailData.notes" class="p-3 bg-slate-50 rounded-xl border border-slate-200 text-xs">
                        <span class="font-semibold text-slate-700 block mb-0.5">Catatan PO:</span>
                        <p class="text-slate-600" x-text="detailData.notes"></p>
                    </div>
                </div>

                <!-- Fixed Footer -->
                <div class="px-6 py-3.5 bg-[#F8FAFC] border-t border-[#E2E8F0] flex items-center justify-end gap-3 shrink-0">
                    <button type="button" @click="showDetailModal = false" class="px-4 py-2 rounded-xl bg-white border border-[#CBD5E1] text-[#334155] font-bold text-xs hover:bg-[#F1F5F9] transition">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>

<script>
function poManager() {
    return {
        showCreateModal: false,
        showDetailModal: false,
        detailData: {},
        productsList: {!! json_encode($products->map(function($p) { return ['id' => $p->id, 'name' => $p->name, 'sku' => $p->sku, 'unit' => $p->unit, 'price' => $p->purchase_price]; })) !!},
        itemRows: [
            { product_id: '', product_name: '', quantity: 1, unit: 'Unit', unit_price: 0 }
        ],
        openCreateModal() {
            this.showCreateModal = true;
        },
        addItemRow() {
            this.itemRows.push({ product_id: '', product_name: '', quantity: 1, unit: 'Unit', unit_price: 0 });
        },
        removeItemRow(index) {
            if (this.itemRows.length > 1) {
                this.itemRows.splice(index, 1);
            }
        },
        onProductSelect(index) {
            const row = this.itemRows[index];
            if (row.product_id && row.product_id !== 'custom') {
                const p = this.productsList.find(x => x.id == row.product_id);
                if (p) {
                    row.product_name = p.name;
                    if (p.unit) row.unit = p.unit;
                    if (p.price) row.unit_price = Number(p.price);
                }
            } else if (row.product_id === 'custom') {
                row.product_name = '';
            }
        },
        calculateTotal() {
            return this.itemRows.reduce((sum, r) => sum + ((r.quantity || 0) * (r.unit_price || 0)), 0);
        },
        viewDetail(po) {
            this.detailData = po;
            this.showDetailModal = true;
        }
    };
}
</script>
@endsection
