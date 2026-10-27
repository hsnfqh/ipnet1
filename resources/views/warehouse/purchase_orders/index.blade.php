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

    <!-- MODAL CREATE PO -->
    <div x-show="showCreateModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.away="showCreateModal = false" class="bg-white rounded-2xl shadow-2xl border border-slate-200 max-w-4xl w-full max-h-[92vh] overflow-y-auto p-6">
            <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-200">
                <h3 class="text-[18px] font-bold text-[#1E293B]">Terbitkan Purchase Order Baru</h3>
                <button type="button" @click="showCreateModal = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
            </div>

            <form action="{{ route('warehouse.purchase_orders.store') }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    <div>
                        <label class="block text-[11.5px] font-semibold text-[#1E293B] mb-1">Nomor PO (Otomatis jika kosong)</label>
                        <input type="text" name="po_number" class="wms-input text-[12.5px]" placeholder="PO-IPN-2026-...">
                    </div>
                    <div>
                        <label class="block text-[11.5px] font-semibold text-[#1E293B] mb-1">Tanggal Terbit PO *</label>
                        <input type="date" name="po_date" value="{{ date('Y-m-d') }}" required class="wms-input text-[12.5px]">
                    </div>
                    <div>
                        <label class="block text-[11.5px] font-semibold text-[#1E293B] mb-1">Supplier / Mitra *</label>
                        <select name="supplier_id" required class="wms-input text-[12.5px]">
                            <option value="">Pilih Supplier...</option>
                            @foreach($suppliers as $sup)
                                <option value="{{ $sup->id }}">{{ $sup->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11.5px] font-semibold text-[#1E293B] mb-1">Gudang Penerima *</label>
                        <select name="warehouse_id" required class="wms-input text-[12.5px]">
                            @foreach($warehouses as $wh)
                                <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[11.5px] font-semibold text-[#1E293B] mb-1">Estimasi Tanggal Tiba</label>
                        <input type="date" name="expected_date" class="wms-input text-[12.5px]">
                    </div>
                    <div>
                        <label class="block text-[11.5px] font-semibold text-[#1E293B] mb-1">Status Awal</label>
                        <select name="status" class="wms-input text-[12.5px]">
                            <option value="Draft">Draft</option>
                            <option value="Diproses" selected>Diproses</option>
                            <option value="Disetujui">Disetujui</option>
                            <option value="Selesai">Selesai (Langsung Tambah Stok)</option>
                        </select>
                    </div>
                </div>

                <!-- DYNAMIC ITEMS TABLE -->
                <div class="pt-3 border-t border-slate-200">
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-[13px] font-bold text-[#1E293B]">Daftar Item Perangkat yang Dipesan *</label>
                        <button type="button" @click="addItemRow()" class="text-[12px] font-bold text-[#8F0A0D] hover:underline flex items-center gap-1">
                            + Tambah Baris Produk
                        </button>
                    </div>

                    <div class="space-y-2">
                        <template x-for="(row, index) in itemRows" :key="index">
                            <div class="grid grid-cols-12 gap-2 items-center bg-slate-50 p-2.5 rounded-xl border border-slate-200">
                                <div class="col-span-5">
                                    <label class="block text-[10px] text-slate-500 font-semibold mb-0.5">Pilih / Ketik Nama Produk</label>
                                    <input type="text" :name="'items[' + index + '][product_name]'" x-model="row.product_name" required class="wms-input text-[12px] py-1.5" placeholder="e.g. Cisco Catalyst 3850">
                                    <input type="hidden" :name="'items[' + index + '][product_id]'" x-model="row.product_id">
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-[10px] text-slate-500 font-semibold mb-0.5">Qty</label>
                                    <input type="number" :name="'items[' + index + '][quantity]'" x-model.number="row.quantity" min="1" required class="wms-input text-[12px] py-1.5">
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-[10px] text-slate-500 font-semibold mb-0.5">Satuan</label>
                                    <input type="text" :name="'items[' + index + '][unit]'" x-model="row.unit" required class="wms-input text-[12px] py-1.5">
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-[10px] text-slate-500 font-semibold mb-0.5">Harga Satuan (Rp)</label>
                                    <input type="number" :name="'items[' + index + '][unit_price]'" x-model.number="row.unit_price" min="0" required class="wms-input text-[12px] py-1.5">
                                </div>
                                <div class="col-span-1 text-center pt-3">
                                    <button type="button" @click="removeItemRow(index)" class="text-red-500 hover:text-red-700 text-lg font-bold" x-show="itemRows.length > 1">&times;</button>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- TOTAL CALCULATION -->
                    <div class="mt-3 p-3 rounded-xl bg-slate-100 flex items-center justify-between text-[13px]">
                        <span class="font-semibold text-slate-700">Estimasi Total Biaya Pengadaan:</span>
                        <strong class="text-[17px] font-extrabold text-[#8F0A0D]" x-text="'Rp ' + (new Intl.NumberFormat('id-ID').format(calculateTotal()))"></strong>
                    </div>
                </div>

                <div>
                    <label class="block text-[11.5px] font-semibold text-[#1E293B] mb-1">Catatan Tambahan / Syarat Pembayaran</label>
                    <textarea name="notes" rows="2" class="wms-input text-[12.5px]" placeholder="Syarat serah terima, ketentuan garansi, no kontrak rekanan..."></textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-200">
                    <button type="button" @click="showCreateModal = false" class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 font-semibold text-[13px]">Batal</button>
                    <button type="submit" class="btn-ipnet-gradient px-5 py-2.5 rounded-xl font-bold text-[13px]">Terbitkan Purchase Order</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL DETAIL & CETAK PO -->
    <div x-show="showDetailModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.away="showDetailModal = false" class="bg-white rounded-2xl shadow-2xl border border-slate-200 max-w-2xl w-full p-6 max-h-[92vh] overflow-y-auto">
            <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-200">
                <div class="flex items-center gap-2">
                    <span class="text-[12px] font-bold px-2 py-0.5 rounded bg-blue-100 text-blue-700" x-text="detailData.status"></span>
                    <h3 class="text-[17px] font-bold text-[#1E293B]" x-text="detailData.po_number"></h3>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="window.print()" class="px-3 py-1.5 rounded-lg border border-slate-300 text-slate-700 font-semibold text-[12px] hover:bg-slate-100 flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        <span>Cetak PO</span>
                    </button>
                    <button type="button" @click="showDetailModal = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
                </div>
            </div>

            <!-- PRINTABLE PO SHEET -->
            <div id="print-area" class="space-y-4 text-[13px]">
                <div class="border-b-2 border-[#8F0A0D] pb-3 flex items-center justify-between">
                    <div>
                        <h2 class="text-[18px] font-extrabold text-[#8F0A0D]">PT IP NETWORK SOLUSINDO</h2>
                        <p class="text-[11px] text-slate-500">Official Purchase Order &amp; Procurement Document</p>
                    </div>
                    <div class="text-right">
                        <div class="text-[14px] font-mono font-bold text-[#1E293B]" x-text="detailData.po_number"></div>
                        <div class="text-[11px] text-slate-500" x-text="'Tanggal: ' + (detailData.po_date || '')"></div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 bg-slate-50 p-3 rounded-xl border border-slate-200">
                    <div>
                        <span class="text-[11px] text-[#64748B] block font-semibold">Supplier Tujuan:</span>
                        <div class="font-bold text-[#1E293B]" x-text="detailData.supplier_name || '-'"></div>
                    </div>
                    <div>
                        <span class="text-[11px] text-[#64748B] block font-semibold">Gudang Penerima:</span>
                        <div class="font-bold text-[#8F0A0D]" x-text="detailData.warehouse_name || 'Gudang Utama IPNET'"></div>
                    </div>
                </div>

                <!-- ITEMS TABLE -->
                <div>
                    <h4 class="font-bold text-[#1E293B] mb-2 text-[13px]">Rincian Barang yang Dipesan:</h4>
                    <table class="w-full text-left text-[12.5px] border border-slate-200 rounded-lg overflow-hidden">
                        <thead class="bg-slate-100 text-slate-700 font-bold">
                            <tr>
                                <th class="p-2 border-b">Nama Barang</th>
                                <th class="p-2 border-b text-center">Qty</th>
                                <th class="p-2 border-b text-right">Harga Satuan</th>
                                <th class="p-2 border-b text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            <template x-for="item in (detailData.items || [])" :key="item.id">
                                <tr>
                                    <td class="p-2 font-medium text-slate-800" x-text="item.product_name"></td>
                                    <td class="p-2 text-center" x-text="item.quantity + ' ' + item.unit"></td>
                                    <td class="p-2 text-right" x-text="'Rp ' + (new Intl.NumberFormat('id-ID').format(item.unit_price))"></td>
                                    <td class="p-2 text-right font-bold text-slate-900" x-text="'Rp ' + (new Intl.NumberFormat('id-ID').format(item.subtotal))"></td>
                                </tr>
                            </template>
                        </tbody>
                        <tfoot class="bg-slate-50 font-bold">
                            <tr>
                                <td colspan="3" class="p-2 text-right text-slate-700">Total Nilai PO:</td>
                                <td class="p-2 text-right text-[#8F0A0D] text-[14px]" x-text="'Rp ' + (new Intl.NumberFormat('id-ID').format(detailData.total_amount || 0))"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div x-show="detailData.notes" class="p-3 bg-slate-50 rounded-xl border border-slate-200 text-[12px]">
                    <span class="font-semibold text-slate-700 block mb-0.5">Catatan PO:</span>
                    <p class="text-slate-600" x-text="detailData.notes"></p>
                </div>
            </div>

            <div class="mt-5 pt-3 border-t border-slate-200 flex justify-end">
                <button type="button" @click="showDetailModal = false" class="btn-ipnet-gradient px-4 py-2 rounded-xl text-[13px] font-bold">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
function poManager() {
    return {
        showCreateModal: false,
        showDetailModal: false,
        detailData: {},
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
