@extends('layouts.app')

@section('title', 'Stock Opname & Rekonsiliasi - Warehouse IPNET')

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
<div class="flex h-screen overflow-hidden bg-[#F8FAFC] font-sans" x-data="soManager()">
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Stock Opname & Audit Fisik'])
        
        <div class="p-4 sm:p-6 lg:p-7 space-y-6 max-w-[1600px] mx-auto">
            
            <!-- HEADER -->
            <div class="ipnet-card p-5 sm:p-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#8F0A0D]"></span>
                            <h2 class="text-[20px] font-bold text-[#1E293B]">Stock Opname &amp; Audit Fisik Berkala</h2>
                        </div>
                        <p class="text-[13px] text-[#64748B] mt-0.5">
                            Pencocokan kuantitas perangkat di rak fisik terhadap catatan sistem, pendeteksian selisih, dan sinkronisasi saldo akhir.
                        </p>
                    </div>

                    <div class="flex items-center gap-3">
                        <button type="button" 
                                @click="openCreateModal()"
                                class="btn-ipnet-gradient px-4 py-2.5 rounded-xl font-bold text-[13px] shadow-sm flex items-center gap-2 cursor-pointer transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span>Catat Stock Opname</span>
                        </button>
                    </div>
                </div>

                <!-- FILTER ROW -->
                <form action="{{ route('warehouse.stock_opname.index') }}" method="GET" class="mt-5 pt-4 border-t border-[#E2E8F0] grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    <div>
                        <label class="block text-[11px] font-semibold text-[#64748B] mb-1">Cari No. Dokumen SO</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="SO-2026-..." class="wms-input text-[12.5px]">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-[#64748B] mb-1">Status Audit</label>
                        <select name="status" class="wms-input text-[12.5px]" onchange="this.form.submit()">
                            <option value="all">Semua Status</option>
                            <option value="Draft" {{ request('status') === 'Draft' ? 'selected' : '' }}>Draft</option>
                            <option value="Menunggu Approval" {{ request('status') === 'Menunggu Approval' ? 'selected' : '' }}>Menunggu Approval</option>
                            <option value="Selesai" {{ request('status') === 'Selesai' ? 'selected' : '' }}>Selesai (Disinkronkan)</option>
                        </select>
                    </div>
                    <div class="flex items-end gap-2 col-span-1 sm:col-span-2">
                        <button type="submit" class="bg-[#1E293B] hover:bg-[#0F172A] text-white px-5 py-2.5 rounded-xl text-[12.5px] font-semibold transition">
                            Filter
                        </button>
                        @if(request()->anyFilled(['search', 'status']))
                            <a href="{{ route('warehouse.stock_opname.index') }}" class="bg-slate-200 hover:bg-slate-300 text-slate-700 px-3.5 py-2.5 rounded-xl text-[12.5px] font-semibold transition">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- SO TABLE -->
            <div class="ipnet-card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-[13px] border-collapse">
                        <thead>
                            <tr class="bg-[#F8FAFC] border-b border-[#E2E8F0] text-[#64748B] text-[11.5px] uppercase font-bold tracking-wider">
                                <th class="py-3.5 px-4">No. Dokumen &amp; Tanggal</th>
                                <th class="py-3.5 px-4">Titik Gudang yang Diaudit</th>
                                <th class="py-3.5 px-4">Petugas Auditor (PIC)</th>
                                <th class="py-3.5 px-4 text-center">Item Diperiksa</th>
                                <th class="py-3.5 px-4 text-center">Selisih (+/-)</th>
                                <th class="py-3.5 px-4 text-center">Status</th>
                                <th class="py-3.5 px-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#F1F5F9]">
                            @forelse($stockOpnames as $so)
                                <tr class="hover:bg-[#F8FAFC]/80 transition">
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-[#1E293B] text-[14px]">{{ $so->so_number }}</div>
                                        <div class="text-[11px] text-[#64748B] mt-0.5">{{ $so->so_date->format('d M Y') }}</div>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <div class="font-semibold text-[#1E293B]">{{ $so->warehouse_name ?: '-' }}</div>
                                    </td>
                                    <td class="py-3.5 px-4 font-medium text-slate-700">
                                        {{ $so->auditor_name ?: '-' }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center font-bold text-slate-800">
                                        {{ $so->total_items_checked }} Item
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        @if($so->total_discrepancy == 0)
                                            <span class="px-2 py-0.5 text-[11px] font-bold rounded bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                Klop (0)
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 text-[11px] font-bold rounded bg-amber-50 text-amber-700 border border-amber-200">
                                                Selisih {{ $so->total_discrepancy }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        <span class="px-2.5 py-1 text-[11px] font-bold rounded-md {{ $so->status === 'Selesai' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                                            {{ $so->status }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        <div class="inline-flex items-center gap-1.5">
                                            <button type="button" 
                                                    @click="viewDetail({{ json_encode($so) }})"
                                                    class="p-1.5 rounded-lg text-slate-600 hover:bg-slate-100 transition" title="Lihat & Cetak Hasil SO">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </button>
                                            @if($so->status !== 'Selesai')
                                                <form action="{{ route('warehouse.stock_opname.sync', $so->id) }}" method="POST" class="inline" onsubmit="return confirm('Sinkronkan stok sistem sesuai hasil fisik Stock Opname ini?')">
                                                    @csrf
                                                    <button type="submit" class="p-1.5 rounded-lg text-emerald-600 hover:bg-emerald-50 transition" title="Sinkronkan Stok Fisik ke Master">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                                        </svg>
                                                    </button>
                                                </form>
                                            @endif
                                            <form action="{{ route('warehouse.stock_opname.destroy', $so->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus dokumen Stock Opname ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 rounded-lg text-red-600 hover:bg-red-50 transition" title="Hapus Dokumen">
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
                                        Belum ada dokumen Stock Opname tercatat.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($stockOpnames->hasPages())
                    <div class="p-4 border-t border-[#E2E8F0]">
                        {{ $stockOpnames->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>

    <!-- MODAL CREATE STOCK OPNAME -->
    <div x-show="showCreateModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.away="showCreateModal = false" class="bg-white rounded-2xl shadow-2xl border border-slate-200 max-w-4xl w-full max-h-[92vh] overflow-y-auto p-6">
            <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-200">
                <h3 class="text-[18px] font-bold text-[#1E293B]">Catat Audit Stock Opname</h3>
                <button type="button" @click="showCreateModal = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
            </div>

            <form action="{{ route('warehouse.stock_opname.store') }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    <div>
                        <label class="block text-[11.5px] font-semibold text-[#1E293B] mb-1">Nomor SO (Otomatis jika kosong)</label>
                        <input type="text" name="so_number" class="wms-input text-[12.5px]" placeholder="SO-2026-10-...">
                    </div>
                    <div>
                        <label class="block text-[11.5px] font-semibold text-[#1E293B] mb-1">Tanggal Audit Fisik *</label>
                        <input type="date" name="so_date" value="{{ date('Y-m-d') }}" required class="wms-input text-[12.5px]">
                    </div>
                    <div>
                        <label class="block text-[11.5px] font-semibold text-[#1E293B] mb-1">Gudang yang Diaudit *</label>
                        <select name="warehouse_id" required class="wms-input text-[12.5px]">
                            @foreach($warehouses as $wh)
                                <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11.5px] font-semibold text-[#1E293B] mb-1">Petugas Auditor (PIC) *</label>
                        <input type="text" name="auditor_name" value="Ahmad (Warehouse)" required class="wms-input text-[12.5px]">
                    </div>
                </div>

                <!-- ITEMS TABLE FOR AUDIT -->
                <div class="pt-3 border-t border-slate-200">
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-[13px] font-bold text-[#1E293B]">Hasil Penghitungan Fisik per Barang *</label>
                        <button type="button" @click="addAuditRow()" class="text-[12px] font-bold text-[#8F0A0D] hover:underline">
                            + Tambah Baris Audit
                        </button>
                    </div>

                    <div class="space-y-2">
                        <template x-for="(row, index) in auditRows" :key="index">
                            <div class="grid grid-cols-12 gap-2 items-center bg-slate-50 p-2.5 rounded-xl border border-slate-200">
                                <div class="col-span-4">
                                    <label class="block text-[10px] text-slate-500 font-semibold mb-0.5">Nama Produk</label>
                                    <input type="text" :name="'items[' + index + '][product_name]'" x-model="row.product_name" required class="wms-input text-[12px] py-1.5" placeholder="e.g. Cisco Catalyst 3850">
                                    <input type="hidden" :name="'items[' + index + '][product_id]'" x-model="row.product_id">
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-[10px] text-slate-500 font-semibold mb-0.5">Stok Sistem</label>
                                    <input type="number" :name="'items[' + index + '][system_stock]'" x-model.number="row.system_stock" min="0" required class="wms-input text-[12px] py-1.5">
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-[10px] text-slate-500 font-semibold mb-0.5">Stok Fisik Aktual</label>
                                    <input type="number" :name="'items[' + index + '][physical_stock]'" x-model.number="row.physical_stock" min="0" required class="wms-input text-[12px] py-1.5">
                                </div>
                                <div class="col-span-3">
                                    <label class="block text-[10px] text-slate-500 font-semibold mb-0.5">Kondisi Fisik</label>
                                    <select :name="'items[' + index + '][condition_status]'" x-model="row.condition_status" class="wms-input text-[12px] py-1.5">
                                        <option value="Baik & Lengkap">Baik &amp; Lengkap</option>
                                        <option value="Perlu Servis">Perlu Servis</option>
                                        <option value="Kemasan Rusak">Kemasan Rusak</option>
                                        <option value="Hilang">Hilang</option>
                                    </select>
                                </div>
                                <div class="col-span-1 text-center pt-3">
                                    <button type="button" @click="removeAuditRow(index)" class="text-red-500 hover:text-red-700 text-lg font-bold" x-show="auditRows.length > 1">&times;</button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[11.5px] font-semibold text-[#1E293B] mb-1">Status Dokumen</label>
                        <select name="status" class="wms-input text-[12.5px]">
                            <option value="Draft">Draft</option>
                            <option value="Menunggu Approval" selected>Menunggu Approval</option>
                            <option value="Selesai">Selesai (Langsung Sinkronkan Saldo)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11.5px] font-semibold text-[#1E293B] mb-1">Catatan Audit / Justifikasi Selisih</label>
                        <input type="text" name="notes" class="wms-input text-[12.5px]" placeholder="Penjelasan temuan selisih barang...">
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-200">
                    <button type="button" @click="showCreateModal = false" class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 font-semibold text-[13px]">Batal</button>
                    <button type="submit" class="btn-ipnet-gradient px-5 py-2.5 rounded-xl font-bold text-[13px]">Simpan Berita Acara SO</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL DETAIL & CETAK SO -->
    <div x-show="showDetailModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.away="showDetailModal = false" class="bg-white rounded-2xl shadow-2xl border border-slate-200 max-w-2xl w-full p-6 max-h-[92vh] overflow-y-auto">
            <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-200">
                <div class="flex items-center gap-2">
                    <span class="text-[12px] font-bold px-2 py-0.5 rounded bg-emerald-100 text-emerald-700" x-text="detailData.status"></span>
                    <h3 class="text-[17px] font-bold text-[#1E293B]" x-text="detailData.so_number"></h3>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="window.print()" class="px-3 py-1.5 rounded-lg border border-slate-300 text-slate-700 font-semibold text-[12px] hover:bg-slate-100 flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        <span>Cetak Berita Acara</span>
                    </button>
                    <button type="button" @click="showDetailModal = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
                </div>
            </div>

            <!-- PRINTABLE SO SHEET -->
            <div id="print-area" class="space-y-4 text-[13px]">
                <div class="border-b-2 border-[#8F0A0D] pb-3 flex items-center justify-between">
                    <div>
                        <h2 class="text-[18px] font-extrabold text-[#8F0A0D]">BERITA ACARA STOCK OPNAME</h2>
                        <p class="text-[11px] text-slate-500">PT IP NETWORK SOLUSINDO - WAREHOUSE DIVISION</p>
                    </div>
                    <div class="text-right">
                        <div class="text-[14px] font-mono font-bold text-[#1E293B]" x-text="detailData.so_number"></div>
                        <div class="text-[11px] text-slate-500" x-text="'Audit: ' + (detailData.so_date || '')"></div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 bg-slate-50 p-3 rounded-xl border border-slate-200">
                    <div>
                        <span class="text-[11px] text-[#64748B] block font-semibold">Gudang Fasilitas:</span>
                        <div class="font-bold text-[#1E293B]" x-text="detailData.warehouse_name || '-'"></div>
                    </div>
                    <div>
                        <span class="text-[11px] text-[#64748B] block font-semibold">Auditor PIC:</span>
                        <div class="font-bold text-[#8F0A0D]" x-text="detailData.auditor_name || '-'"></div>
                    </div>
                </div>

                <!-- ITEMS TABLE -->
                <div>
                    <h4 class="font-bold text-[#1E293B] mb-2 text-[13px]">Rincian Hasil Pengecekan Fisik:</h4>
                    <table class="w-full text-left text-[12.5px] border border-slate-200 rounded-lg overflow-hidden">
                        <thead class="bg-slate-100 text-slate-700 font-bold">
                            <tr>
                                <th class="p-2 border-b">Nama Barang</th>
                                <th class="p-2 border-b text-center">Sistem</th>
                                <th class="p-2 border-b text-center">Fisik</th>
                                <th class="p-2 border-b text-center">Selisih</th>
                                <th class="p-2 border-b">Kondisi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            <template x-for="item in (detailData.items || [])" :key="item.id">
                                <tr>
                                    <td class="p-2 font-medium text-slate-800" x-text="item.product_name"></td>
                                    <td class="p-2 text-center" x-text="item.system_stock"></td>
                                    <td class="p-2 text-center font-bold" x-text="item.physical_stock"></td>
                                    <td class="p-2 text-center font-bold" :class="item.difference != 0 ? 'text-amber-600' : 'text-emerald-600'" x-text="item.difference"></td>
                                    <td class="p-2 text-slate-700" x-text="item.condition_status"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <div x-show="detailData.notes" class="p-3 bg-slate-50 rounded-xl border border-slate-200 text-[12px]">
                    <span class="font-semibold text-slate-700 block mb-0.5">Catatan &amp; Keterangan:</span>
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
function soManager() {
    return {
        showCreateModal: false,
        showDetailModal: false,
        detailData: {},
        auditRows: [
            { product_id: '', product_name: 'Cisco Catalyst 3850', system_stock: 12, physical_stock: 12, condition_status: 'Baik & Lengkap' }
        ],
        openCreateModal() {
            this.showCreateModal = true;
        },
        addAuditRow() {
            this.auditRows.push({ product_id: '', product_name: '', system_stock: 0, physical_stock: 0, condition_status: 'Baik & Lengkap' });
        },
        removeAuditRow(index) {
            if (this.auditRows.length > 1) {
                this.auditRows.splice(index, 1);
            }
        },
        viewDetail(so) {
            this.detailData = so;
            this.showDetailModal = true;
        }
    };
}
</script>
@endsection
