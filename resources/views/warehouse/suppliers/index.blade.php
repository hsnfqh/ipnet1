@extends('layouts.app')

@section('title', 'Supplier & Distributor - Warehouse IPNET')

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
<div class="flex h-screen overflow-hidden bg-[#F8FAFC] font-sans" x-data="supplierManager()">
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Master Supplier & Distributor'])
        
        <div class="p-4 sm:p-6 lg:p-7 space-y-6 max-w-[1600px] mx-auto">
            
            <!-- HEADER -->
            <div class="ipnet-card p-5 sm:p-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#8F0A0D]"></span>
                            <h2 class="text-[20px] font-bold text-[#1E293B]">Direktori Supplier &amp; Distributor Resmi</h2>
                        </div>
                        <p class="text-[13px] text-[#64748B] mt-0.5">
                            Mitra vendor principal, distributor tier-1, dan penyedia suku cadang perangkat data center &amp; jaringan.
                        </p>
                    </div>

                    <div class="flex items-center gap-3">
                        <button type="button" 
                                @click="openCreateModal()"
                                class="btn-ipnet-gradient px-4 py-2.5 rounded-xl font-bold text-[13px] shadow-sm flex items-center gap-2 cursor-pointer transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span>Tambah Supplier Baru</span>
                        </button>
                    </div>
                </div>

                <!-- FILTER ROW -->
                <form action="{{ route('warehouse.suppliers.index') }}" method="GET" class="mt-5 pt-4 border-t border-[#E2E8F0] flex flex-col sm:flex-row items-center gap-3">
                    <div class="flex-1 w-full">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama supplier, PIC, email, atau brand..." class="wms-input text-[13px]">
                    </div>
                    <div class="w-full sm:w-48">
                        <select name="status" class="wms-input text-[13px]" onchange="this.form.submit()">
                            <option value="all">Semua Status</option>
                            <option value="Aktif" {{ request('status') === 'Aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="Nonaktif" {{ request('status') === 'Nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                    </div>
                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <button type="submit" class="bg-[#1E293B] hover:bg-[#0F172A] text-white px-5 py-2.5 rounded-xl text-[13px] font-semibold transition">
                            Filter
                        </button>
                        @if(request()->anyFilled(['search', 'status']))
                            <a href="{{ route('warehouse.suppliers.index') }}" class="bg-slate-200 hover:bg-slate-300 text-slate-700 px-3.5 py-2.5 rounded-xl text-[13px] font-semibold transition">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- SUPPLIERS TABLE -->
            <div class="ipnet-card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-[13px] border-collapse">
                        <thead>
                            <tr class="bg-[#F8FAFC] border-b border-[#E2E8F0] text-[#64748B] text-[11.5px] uppercase font-bold tracking-wider">
                                <th class="py-3.5 px-4">Kode &amp; Nama Perusahaan</th>
                                <th class="py-3.5 px-4">Kontak PIC</th>
                                <th class="py-3.5 px-4">No. Telp &amp; Email</th>
                                <th class="py-3.5 px-4">Brand / Produk Suplai</th>
                                <th class="py-3.5 px-4 text-center">Riwayat PO</th>
                                <th class="py-3.5 px-4 text-center">Status</th>
                                <th class="py-3.5 px-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#F1F5F9]">
                            @forelse($suppliers as $supplier)
                                <tr class="hover:bg-[#F8FAFC]/80 transition">
                                    <td class="py-3.5 px-4">
                                        <div class="font-mono text-[11px] font-bold text-[#8F0A0D]">{{ $supplier->code }}</div>
                                        <div class="font-bold text-[#1E293B] text-[14px]">{{ $supplier->name }}</div>
                                        @if($supplier->address)
                                            <div class="text-[11px] text-[#64748B] line-clamp-1 mt-0.5">{{ $supplier->address }}</div>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 font-semibold text-[#1E293B]">
                                        {{ $supplier->contact_person ?: '-' }}
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <div class="font-medium text-[#1E293B]">{{ $supplier->phone ?: '-' }}</div>
                                        <div class="text-[11px] text-[#64748B]">{{ $supplier->email ?: '-' }}</div>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        @if($supplier->products_supplied)
                                            <div class="flex flex-wrap gap-1">
                                                @foreach(explode(',', $supplier->products_supplied) as $tag)
                                                    <span class="px-2 py-0.5 text-[10.5px] rounded bg-slate-100 font-semibold text-slate-700">
                                                        {{ trim($tag) }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-slate-400">-</span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        <span class="px-2.5 py-1 text-[11px] font-bold rounded-lg bg-blue-50 text-blue-700 border border-blue-200">
                                            {{ $supplier->purchase_orders_count }} PO
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        <span class="px-2.5 py-0.5 text-[10.5px] font-bold rounded-full {{ $supplier->status === 'Aktif' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600' }}">
                                            {{ $supplier->status }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        <div class="inline-flex items-center gap-1.5">
                                            <button type="button" 
                                                    @click="viewDetail({{ json_encode($supplier) }})"
                                                    class="p-1.5 rounded-lg text-slate-600 hover:bg-slate-100 transition" title="Detail Supplier">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </button>
                                            <button type="button" 
                                                    @click="openEditModal({{ json_encode($supplier) }})"
                                                    class="p-1.5 rounded-lg text-blue-600 hover:bg-blue-50 transition" title="Edit Supplier">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </button>
                                            <form action="{{ route('warehouse.suppliers.destroy', $supplier->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus supplier ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 rounded-lg text-red-600 hover:bg-red-50 transition" title="Hapus Supplier">
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
                                        Belum ada data supplier yang cocok.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($suppliers->hasPages())
                    <div class="p-4 border-t border-[#E2E8F0]">
                        {{ $suppliers->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>

    <!-- MODAL CREATE SUPPLIER -->
    <div x-show="showCreateModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.away="showCreateModal = false" class="bg-white rounded-2xl shadow-2xl border border-slate-200 max-w-lg w-full p-6">
            <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-200">
                <h3 class="text-[17px] font-bold text-[#1E293B]">Tambah Supplier / Distributor</h3>
                <button type="button" @click="showCreateModal = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
            </div>

            <form action="{{ route('warehouse.suppliers.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-[12px] font-semibold text-[#1E293B] mb-1">Nama Perusahaan / PT *</label>
                    <input type="text" name="name" required class="wms-input text-[13px]" placeholder="e.g. PT Synnex Metrodata Indonesia">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[12px] font-semibold text-[#1E293B] mb-1">Kode Supplier (Otomatis jika kosong)</label>
                        <input type="text" name="code" class="wms-input text-[13px]" placeholder="e.g. SUP-2026-001">
                    </div>
                    <div>
                        <label class="block text-[12px] font-semibold text-[#1E293B] mb-1">Status</label>
                        <select name="status" class="wms-input text-[13px]">
                            <option value="Aktif">Aktif</option>
                            <option value="Nonaktif">Nonaktif</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[12px] font-semibold text-[#1E293B] mb-1">Nama PIC Kontak</label>
                        <input type="text" name="contact_person" class="wms-input text-[13px]" placeholder="e.g. Bambang Sugianto">
                    </div>
                    <div>
                        <label class="block text-[12px] font-semibold text-[#1E293B] mb-1">No. Telepon / WA</label>
                        <input type="text" name="phone" class="wms-input text-[13px]" placeholder="e.g. 021-29345800 / 0812...">
                    </div>
                </div>
                <div>
                    <label class="block text-[12px] font-semibold text-[#1E293B] mb-1">Email Resmi</label>
                    <input type="email" name="email" class="wms-input text-[13px]" placeholder="sales@distributor.co.id">
                </div>
                <div>
                    <label class="block text-[12px] font-semibold text-[#1E293B] mb-1">Brand / Kategori Produk yang Disuplai</label>
                    <input type="text" name="products_supplied" class="wms-input text-[13px]" placeholder="e.g. Cisco, Fortinet, Mikrotik, Kabel Belden">
                </div>
                <div>
                    <label class="block text-[12px] font-semibold text-[#1E293B] mb-1">Alamat Kantor / Gudang Supplier</label>
                    <textarea name="address" rows="2" class="wms-input text-[13px]" placeholder="Alamat lengkap..."></textarea>
                </div>
                <div>
                    <label class="block text-[12px] font-semibold text-[#1E293B] mb-1">Catatan Tambahan</label>
                    <textarea name="notes" rows="2" class="wms-input text-[13px]" placeholder="Ketentuan diskon, SLA pengiriman, kontak emergency..."></textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-200">
                    <button type="button" @click="showCreateModal = false" class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 font-semibold text-[13px]">Batal</button>
                    <button type="submit" class="btn-ipnet-gradient px-5 py-2.5 rounded-xl font-bold text-[13px]">Simpan Supplier</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL EDIT SUPPLIER -->
    <div x-show="showEditModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.away="showEditModal = false" class="bg-white rounded-2xl shadow-2xl border border-slate-200 max-w-lg w-full p-6">
            <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-200">
                <h3 class="text-[17px] font-bold text-[#1E293B]">Edit Data Supplier</h3>
                <button type="button" @click="showEditModal = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
            </div>

            <form :action="'/warehouse/suppliers/' + editData.id" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-[12px] font-semibold text-[#1E293B] mb-1">Nama Perusahaan *</label>
                    <input type="text" name="name" x-model="editData.name" required class="wms-input text-[13px]">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[12px] font-semibold text-[#1E293B] mb-1">Kode Supplier *</label>
                        <input type="text" name="code" x-model="editData.code" required class="wms-input text-[13px]">
                    </div>
                    <div>
                        <label class="block text-[12px] font-semibold text-[#1E293B] mb-1">Status</label>
                        <select name="status" x-model="editData.status" class="wms-input text-[13px]">
                            <option value="Aktif">Aktif</option>
                            <option value="Nonaktif">Nonaktif</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[12px] font-semibold text-[#1E293B] mb-1">PIC</label>
                        <input type="text" name="contact_person" x-model="editData.contact_person" class="wms-input text-[13px]">
                    </div>
                    <div>
                        <label class="block text-[12px] font-semibold text-[#1E293B] mb-1">Telepon</label>
                        <input type="text" name="phone" x-model="editData.phone" class="wms-input text-[13px]">
                    </div>
                </div>
                <div>
                    <label class="block text-[12px] font-semibold text-[#1E293B] mb-1">Email</label>
                    <input type="email" name="email" x-model="editData.email" class="wms-input text-[13px]">
                </div>
                <div>
                    <label class="block text-[12px] font-semibold text-[#1E293B] mb-1">Brand Suplai</label>
                    <input type="text" name="products_supplied" x-model="editData.products_supplied" class="wms-input text-[13px]">
                </div>
                <div>
                    <label class="block text-[12px] font-semibold text-[#1E293B] mb-1">Alamat</label>
                    <textarea name="address" x-model="editData.address" rows="2" class="wms-input text-[13px]"></textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-200">
                    <button type="button" @click="showEditModal = false" class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 font-semibold text-[13px]">Batal</button>
                    <button type="submit" class="btn-ipnet-gradient px-5 py-2.5 rounded-xl font-bold text-[13px]">Perbarui</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL DETAIL SUPPLIER -->
    <div x-show="showDetailModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.away="showDetailModal = false" class="bg-white rounded-2xl shadow-2xl border border-slate-200 max-w-md w-full p-6">
            <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-200">
                <div>
                    <span class="text-[11px] font-mono font-bold text-[#8F0A0D]" x-text="detailData.code"></span>
                    <h3 class="text-[17px] font-bold text-[#1E293B]" x-text="detailData.name"></h3>
                </div>
                <button type="button" @click="showDetailModal = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
            </div>

            <div class="space-y-3 text-[13px]">
                <div class="bg-slate-50 p-3 rounded-xl border border-slate-200 space-y-2">
                    <div>
                        <span class="text-[11px] text-[#64748B]">PIC &amp; Kontak</span>
                        <div class="font-bold text-[#1E293B]" x-text="detailData.contact_person || '-'"></div>
                        <div class="text-[12px] text-slate-700" x-text="(detailData.phone || '') + ' • ' + (detailData.email || '')"></div>
                    </div>
                    <div>
                        <span class="text-[11px] text-[#64748B]">Produk / Brand yang Disuplai</span>
                        <div class="font-bold text-[#8F0A0D]" x-text="detailData.products_supplied || '-'"></div>
                    </div>
                </div>

                <div x-show="detailData.address" class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                    <span class="text-[11px] text-[#64748B] block mb-1">Alamat Kantor</span>
                    <p class="text-[12px] text-[#1E293B] leading-relaxed" x-text="detailData.address"></p>
                </div>

                <div x-show="detailData.notes" class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                    <span class="text-[11px] text-[#64748B] block mb-1">Catatan Khusus</span>
                    <p class="text-[12px] text-[#1E293B] leading-relaxed" x-text="detailData.notes"></p>
                </div>
            </div>

            <div class="mt-5 pt-3 border-t border-slate-200 flex justify-end">
                <button type="button" @click="showDetailModal = false" class="btn-ipnet-gradient px-4 py-2 rounded-xl text-[13px] font-bold">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
function supplierManager() {
    return {
        showCreateModal: false,
        showEditModal: false,
        showDetailModal: false,
        editData: {},
        detailData: {},
        openCreateModal() {
            this.showCreateModal = true;
        },
        openEditModal(sup) {
            this.editData = Object.assign({}, sup);
            this.showEditModal = true;
        },
        viewDetail(sup) {
            this.detailData = sup;
            this.showDetailModal = true;
        }
    };
}
</script>
@endsection
