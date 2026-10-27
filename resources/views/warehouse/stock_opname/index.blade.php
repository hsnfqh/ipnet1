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

    <!-- MODAL CREATE STOCK OPNAME (Gambar 2 Style) -->
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
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-800">Catat Audit Stock Opname</h3>
                            <p class="text-[11px] text-slate-500">Pencocokan kuantitas perangkat rak fisik terhadap catatan pembukuan sistem</p>
                        </div>
                    </div>
                    <button type="button" @click="showCreateModal = false" class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-200/60 flex items-center justify-center text-lg transition">
                        &times;
                    </button>
                </div>

                <!-- Scrollable Body -->
                <form id="createSoForm" action="{{ route('warehouse.stock_opname.store') }}" method="POST" class="flex flex-col flex-1 min-h-0">
                    @csrf
                    <div class="p-6 overflow-y-auto space-y-5 text-xs flex-1 min-h-0">

                        <!-- SECTION 1 -->
                        <div class="space-y-3">
                            <div class="flex items-center gap-2 text-xs font-bold text-slate-800 uppercase tracking-wider">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#8F0A0D]"></span>
                                <span>1. DOKUMEN &amp; LOKASI AUDIT</span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Nomor SO (Opsional)</label>
                                    <input type="text" name="so_number" class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-[#8F0A0D] text-slate-800 bg-white" placeholder="Otomatis jika kosong">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Tanggal Audit Fisik <span class="text-red-500">*</span></label>
                                    <input type="date" name="so_date" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-[#8F0A0D] text-slate-800 bg-white">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Gudang yang Diaudit <span class="text-red-500">*</span></label>
                                    <select name="warehouse_id" required class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-[#8F0A0D] text-slate-800 bg-white">
                                        @foreach($warehouses as $wh)
                                            <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Petugas Auditor (PIC) <span class="text-red-500">*</span></label>
                                    <input type="text" name="auditor_name" value="{{ auth()->user()->name ?? 'Warehouse Officer' }}" required class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-[#8F0A0D] text-slate-800 bg-white">
                                </div>
                            </div>
                        </div>

                        <!-- SECTION 2 -->
                        <div class="space-y-3 pt-2 border-t border-slate-100">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2 text-xs font-bold text-slate-800 uppercase tracking-wider">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#8F0A0D]"></span>
                                    <span>2. HASIL AUDIT FISIK PERANGKAT</span>
                                </div>
                                <button type="button" @click="addAuditRow()" class="text-xs font-bold text-[#8F0A0D] hover:underline flex items-center gap-1">
                                    + Tambah Baris Audit
                                </button>
                            </div>

                            <div class="space-y-2.5">
                                <template x-for="(row, index) in auditRows" :key="index">
                                    <div class="grid grid-cols-12 gap-2.5 items-center bg-[#F8FAFC] p-3 rounded-xl border border-slate-200">
                                        <!-- Product Dropdown -->
                                        <div class="col-span-12 sm:col-span-4">
                                            <label class="block text-[10.5px] text-slate-600 font-semibold mb-1">Pilih Produk *</label>
                                            <select :name="'items[' + index + '][product_id]'" 
                                                    x-model="row.product_id"
                                                    @change="onProductSelect(index)"
                                                    class="w-full px-2.5 py-1.5 border border-slate-200 rounded-lg outline-none focus:border-[#8F0A0D] text-slate-800 bg-white text-xs">
                                                <option value="">-- Pilih dari Katalog --</option>
                                                <template x-for="p in productsList" :key="p.id">
                                                    <option :value="p.id" x-text="p.name + (p.sku ? ' (' + p.sku + ')' : '')"></option>
                                                </template>
                                                <option value="custom">-- Input Perangkat Lainnya --</option>
                                            </select>
                                            <input type="text" 
                                                   :name="'items[' + index + '][product_name]'" 
                                                   x-model="row.product_name" 
                                                   x-show="row.product_id === 'custom' || !row.product_id"
                                                   placeholder="Ketik nama produk..."
                                                   class="w-full px-2.5 py-1.5 mt-1.5 border border-amber-300 rounded-lg outline-none focus:border-[#8F0A0D] text-slate-800 bg-amber-50/50 text-xs">
                                        </div>

                                        <!-- System Stock -->
                                        <div class="col-span-4 sm:col-span-2">
                                            <label class="block text-[10.5px] text-slate-600 font-semibold mb-1">Stok Sistem</label>
                                            <input type="number" 
                                                   :name="'items[' + index + '][system_stock]'" 
                                                   x-model.number="row.system_stock" 
                                                   min="0" 
                                                   required 
                                                   class="w-full px-2.5 py-1.5 border border-slate-200 rounded-lg outline-none focus:border-[#8F0A0D] text-slate-800 bg-slate-100 text-xs font-semibold">
                                        </div>

                                        <!-- Physical Stock -->
                                        <div class="col-span-4 sm:col-span-2">
                                            <label class="block text-[10.5px] text-slate-600 font-semibold mb-1">Stok Fisik Aktual</label>
                                            <input type="number" 
                                                   :name="'items[' + index + '][physical_stock]'" 
                                                   x-model.number="row.physical_stock" 
                                                   min="0" 
                                                   required 
                                                   class="w-full px-2.5 py-1.5 border border-slate-200 rounded-lg outline-none focus:border-[#8F0A0D] text-slate-800 bg-white text-xs font-bold">
                                        </div>

                                        <!-- Condition Dropdown -->
                                        <div class="col-span-3 sm:col-span-3">
                                            <label class="block text-[10.5px] text-slate-600 font-semibold mb-1">Kondisi Fisik</label>
                                            <select :name="'items[' + index + '][condition_status]'" 
                                                    x-model="row.condition_status" 
                                                    class="w-full px-2 py-1.5 border border-slate-200 rounded-lg outline-none focus:border-[#8F0A0D] text-slate-800 bg-white text-xs">
                                                <option value="Baik & Lengkap">Baik &amp; Lengkap</option>
                                                <option value="Perlu Servis">Perlu Servis</option>
                                                <option value="Kemasan Rusak">Kemasan Rusak</option>
                                                <option value="Hilang">Hilang</option>
                                            </select>
                                        </div>

                                        <!-- Delete row -->
                                        <div class="col-span-1 text-center pt-4">
                                            <button type="button" 
                                                    @click="removeAuditRow(index)" 
                                                    class="w-7 h-7 rounded-lg text-red-500 hover:bg-red-50 hover:text-red-700 flex items-center justify-center text-base font-bold transition mx-auto" 
                                                    x-show="auditRows.length > 1">
                                                &times;
                                            </button>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- SECTION 3 -->
                        <div class="space-y-3 pt-2 border-t border-slate-100">
                            <div class="flex items-center gap-2 text-xs font-bold text-slate-800 uppercase tracking-wider">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#8F0A0D]"></span>
                                <span>3. STATUS DOKUMEN &amp; JUSTIFIKASI AUDITOR</span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Status Dokumen</label>
                                    <select name="status" class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-[#8F0A0D] text-slate-800 bg-white">
                                        <option value="Draft">Draft</option>
                                        <option value="Menunggu Approval" selected>Menunggu Approval</option>
                                        <option value="Selesai">Selesai (Langsung Sinkronkan Saldo)</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Catatan Audit / Justifikasi Selisih</label>
                                    <input type="text" name="notes" class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-[#8F0A0D] text-slate-800 bg-white" placeholder="Penjelasan temuan selisih, nomor rak...">
                                </div>
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
                            <span>Simpan Berita Acara SO</span>
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </template>

    <!-- MODAL DETAIL & CETAK SO (Gambar 2 Style) -->
    <template x-teleport="body">
        <div x-show="showDetailModal" 
             x-cloak 
             class="fixed inset-0 bg-[#0F172A]/60 z-[99999] flex items-center justify-center p-3 sm:p-5 backdrop-blur-xs">
            <div @click.away="showDetailModal = false" 
                 class="bg-white rounded-2xl w-[760px] max-w-full max-h-[92vh] flex flex-col text-left shadow-[0_20px_60px_rgba(15,23,42,0.25)] border border-[#E2E8F0] overflow-hidden">
                
                <!-- Fixed Header -->
                <div class="px-6 py-4 bg-[#F8FAFC] border-b border-[#E2E8F0] shrink-0 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-100 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                            </svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-sm font-bold text-slate-800" x-text="detailData.so_number"></h3>
                                <span class="text-[10.5px] font-bold px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200" x-text="detailData.status"></span>
                            </div>
                            <p class="text-[11px] text-slate-500">Berita Acara Rekonsiliasi &amp; Stock Opname Logistik</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="window.print()" class="px-3 py-1.5 rounded-lg border border-slate-300 text-slate-700 font-semibold text-xs hover:bg-slate-100 flex items-center gap-1.5 transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                            </svg>
                            <span>Cetak Berita Acara</span>
                        </button>
                        <button type="button" @click="showDetailModal = false" class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-200/60 flex items-center justify-center text-lg transition">
                            &times;
                        </button>
                    </div>
                </div>

                <!-- Printable Body -->
                <div class="p-6 overflow-y-auto space-y-4 text-xs flex-1 min-h-0" id="print-area">
                    <div class="border-b-2 border-[#8F0A0D] pb-3 flex items-center justify-between">
                        <div>
                            <h2 class="text-base font-extrabold text-[#8F0A0D]">BERITA ACARA STOCK OPNAME</h2>
                            <p class="text-[11px] text-slate-500">PT IP NETWORK SOLUSINDO - WAREHOUSE DIVISION</p>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-mono font-bold text-slate-800" x-text="detailData.so_number"></div>
                            <div class="text-[11px] text-slate-500" x-text="'Audit: ' + (detailData.so_date ? detailData.so_date.substring(0,10) : '')"></div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 bg-[#F8FAFC] p-3 rounded-xl border border-slate-200">
                        <div>
                            <span class="text-[10.5px] text-slate-500 block font-semibold">Gudang Fasilitas:</span>
                            <div class="font-bold text-slate-800 text-xs" x-text="detailData.warehouse_name || '-'"></div>
                        </div>
                        <div>
                            <span class="text-[10.5px] text-slate-500 block font-semibold">Auditor PIC:</span>
                            <div class="font-bold text-[#8F0A0D] text-xs" x-text="detailData.auditor_name || '-'"></div>
                        </div>
                    </div>

                    <div>
                        <h4 class="font-bold text-slate-800 mb-2 text-xs">Rincian Hasil Pengecekan Fisik:</h4>
                        <table class="w-full text-left text-xs border border-slate-200 rounded-xl overflow-hidden">
                            <thead class="bg-slate-100 text-slate-700 font-bold">
                                <tr>
                                    <th class="p-2.5 border-b border-slate-200">Nama Barang</th>
                                    <th class="p-2.5 border-b border-slate-200 text-center">Sistem</th>
                                    <th class="p-2.5 border-b border-slate-200 text-center">Fisik</th>
                                    <th class="p-2.5 border-b border-slate-200 text-center">Selisih</th>
                                    <th class="p-2.5 border-b border-slate-200">Kondisi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <template x-for="item in (detailData.items || [])" :key="item.id">
                                    <tr>
                                        <td class="p-2.5 font-medium text-slate-800" x-text="item.product_name"></td>
                                        <td class="p-2.5 text-center text-slate-700" x-text="item.system_stock"></td>
                                        <td class="p-2.5 text-center font-bold text-slate-800" x-text="item.physical_stock"></td>
                                        <td class="p-2.5 text-center font-bold" :class="item.difference != 0 ? 'text-amber-600' : 'text-emerald-600'" x-text="item.difference"></td>
                                        <td class="p-2.5 text-slate-700" x-text="item.condition_status"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>

                    <div x-show="detailData.notes" class="p-3 bg-slate-50 rounded-xl border border-slate-200 text-xs">
                        <span class="font-semibold text-slate-700 block mb-0.5">Catatan &amp; Keterangan:</span>
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
function soManager() {
    return {
        showCreateModal: false,
        showDetailModal: false,
        detailData: {},
        productsList: {!! json_encode($products->map(function($p) { return ['id' => $p->id, 'name' => $p->name, 'sku' => $p->sku, 'stock' => $p->current_stock]; })) !!},
        auditRows: [
            { product_id: '', product_name: '', system_stock: 0, physical_stock: 0, condition_status: 'Baik & Lengkap' }
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
        onProductSelect(index) {
            const row = this.auditRows[index];
            if (row.product_id && row.product_id !== 'custom') {
                const p = this.productsList.find(x => x.id == row.product_id);
                if (p) {
                    row.product_name = p.name;
                    row.system_stock = Number(p.stock) || 0;
                    row.physical_stock = Number(p.stock) || 0;
                }
            } else if (row.product_id === 'custom') {
                row.product_name = '';
                row.system_stock = 0;
                row.physical_stock = 0;
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
