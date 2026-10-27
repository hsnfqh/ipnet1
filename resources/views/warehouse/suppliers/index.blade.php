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

    <!-- MODAL CREATE SUPPLIER (GAMBAR 2 STYLE) -->
    <template x-teleport="body">
        <div x-show="showCreateModal" 
             x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-[#0F172A]/60 z-[99999] flex items-center justify-center p-3 sm:p-5 backdrop-blur-xs"
             @click.self="showCreateModal = false"
             @keydown.escape.window="showCreateModal = false">
            
            <div class="bg-white rounded-2xl w-[680px] max-w-full max-h-[90vh] flex flex-col text-left shadow-[0_20px_60px_rgba(15,23,42,0.25)] border border-[#E2E8F0] overflow-hidden">
                <!-- Modal Header -->
                <div class="flex items-center justify-between px-6 py-4 bg-[#F8FAFC] border-b border-[#E2E8F0] shrink-0">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-red-50 text-[#8F0A0D] flex items-center justify-center shrink-0 border border-red-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-900">Tambah Supplier &amp; Distributor Rekanan</h3>
                            <p class="text-xs text-slate-400 mt-0.5">Pencatatan profil prinsipal, distributor resmi, dan narahubung PIC</p>
                        </div>
                    </div>
                    <button type="button" @click="showCreateModal = false" class="text-slate-400 hover:text-slate-600 cursor-pointer p-1 rounded-lg hover:bg-slate-100 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <form action="{{ route('warehouse.suppliers.store') }}" method="POST" class="flex flex-col flex-1 min-h-0 overflow-hidden m-0">
                    @csrf
                    <div class="p-6 overflow-y-auto space-y-5 text-xs flex-1 min-h-0">
                        <div class="space-y-3">
                            <div class="flex items-center gap-2 pb-1 border-b border-slate-100">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#8F0A0D]"></span>
                                <h4 class="font-bold text-slate-800 uppercase tracking-wider text-[11px]">• 1. INFORMASI PERUSAHAAN &amp; DISTRIBUTOR</h4>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                <div class="sm:col-span-2">
                                    <label class="block font-semibold text-slate-700 mb-1">Nama Perusahaan / PT Distributor <span class="text-red-500">*</span></label>
                                    <input type="text" name="name" required placeholder="e.g. PT Synnex Metrodata Indonesia" class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-[#8F0A0D] text-slate-800">
                                </div>
                                <div>
                                    <label class="block font-semibold text-slate-700 mb-1">Kode Supplier (Opsional)</label>
                                    <input type="text" name="code" placeholder="Otomatis jika kosong (SUP-...)" class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-[#8F0A0D] text-slate-800 font-mono">
                                </div>
                                <div>
                                    <label class="block font-semibold text-slate-700 mb-1">Status Kemitraan <span class="text-red-500">*</span></label>
                                    <select name="status" class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-[#8F0A0D] text-slate-800 bg-white cursor-pointer font-medium">
                                        <option value="Aktif">Aktif &amp; Terverifikasi</option>
                                        <option value="Nonaktif">Nonaktif</option>
                                    </select>
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block font-semibold text-slate-700 mb-1">Brand / Kategori Produk yang Disuplai</label>
                                    <input type="text" name="products_supplied" placeholder="e.g. Juniper Networks, Cisco, Fortinet, Belden" class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-[#8F0A0D] text-slate-800">
                                </div>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <div class="flex items-center gap-2 pb-1 border-b border-slate-100">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#8F0A0D]"></span>
                                <h4 class="font-bold text-slate-800 uppercase tracking-wider text-[11px]">• 2. KONTAK PIC &amp; ALAMAT KANTOR</h4>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                <div>
                                    <label class="block font-semibold text-slate-700 mb-1">Nama PIC Kontak Sales / Lead</label>
                                    <input type="text" name="contact_person" placeholder="e.g. Budi Santoso" class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-[#8F0A0D] text-slate-800">
                                </div>
                                <div>
                                    <label class="block font-semibold text-slate-700 mb-1">No. Telepon / WA PIC</label>
                                    <input type="text" name="phone" placeholder="e.g. 081299998888" class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-[#8F0A0D] text-slate-800 font-mono">
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block font-semibold text-slate-700 mb-1">Email Resmi Supplier</label>
                                    <input type="email" name="email" placeholder="sales@distributor.co.id" class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-[#8F0A0D] text-slate-800">
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block font-semibold text-slate-700 mb-1">Alamat Kantor / Gudang Distributor</label>
                                    <textarea name="address" rows="2" placeholder="Gedung / Jalan lengkap..." class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-[#8F0A0D] text-slate-800"></textarea>
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block font-semibold text-slate-700 mb-1">Catatan Tambahan (Opsional)</label>
                                    <textarea name="notes" rows="2" placeholder="Ketentuan TOP (Terms of Payment), diskon, SLA..." class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-[#8F0A0D] text-slate-800"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="px-6 py-3.5 bg-[#F8FAFC] border-t border-[#E2E8F0] flex items-center justify-end gap-3 shrink-0">
                        <button type="button" @click="showCreateModal = false" class="py-2.5 px-4 rounded-xl bg-white text-[#334155] border border-[#CBD5E1] font-bold text-xs hover:bg-[#F1F5F9] transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="btn-ipnet-gradient py-2.5 px-5 rounded-xl font-bold text-xs text-white shadow-xs transition flex items-center gap-1.5 cursor-pointer">
                            <span>Simpan Supplier</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>

    <!-- MODAL EDIT SUPPLIER (GAMBAR 2 STYLE) -->
    <template x-teleport="body">
        <div x-show="showEditModal" 
             x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-[#0F172A]/60 z-[99999] flex items-center justify-center p-3 sm:p-5 backdrop-blur-xs"
             @click.self="showEditModal = false"
             @keydown.escape.window="showEditModal = false">
            
            <div class="bg-white rounded-2xl w-[680px] max-w-full max-h-[90vh] flex flex-col text-left shadow-[0_20px_60px_rgba(15,23,42,0.25)] border border-[#E2E8F0] overflow-hidden">
                <!-- Modal Header -->
                <div class="flex items-center justify-between px-6 py-4 bg-[#F8FAFC] border-b border-[#E2E8F0] shrink-0">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center shrink-0 border border-blue-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-900">Perbarui Data Supplier</h3>
                            <p class="text-xs text-slate-400 mt-0.5" x-text="'Kode Supplier: ' + (editData.code || '-')"></p>
                        </div>
                    </div>
                    <button type="button" @click="showEditModal = false" class="text-slate-400 hover:text-slate-600 cursor-pointer p-1 rounded-lg hover:bg-slate-100 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <form :action="'/warehouse/suppliers/' + editData.id" method="POST" class="flex flex-col flex-1 min-h-0 overflow-hidden m-0">
                    @csrf
                    @method('PUT')
                    <div class="p-6 overflow-y-auto space-y-5 text-xs flex-1 min-h-0">
                        <div class="space-y-3">
                            <div class="flex items-center gap-2 pb-1 border-b border-slate-100">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#8F0A0D]"></span>
                                <h4 class="font-bold text-slate-800 uppercase tracking-wider text-[11px]">• 1. INFORMASI PERUSAHAAN &amp; DISTRIBUTOR</h4>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                <div class="sm:col-span-2">
                                    <label class="block font-semibold text-slate-700 mb-1">Nama Perusahaan / PT <span class="text-red-500">*</span></label>
                                    <input type="text" name="name" x-model="editData.name" required class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-[#8F0A0D] text-slate-800">
                                </div>
                                <div>
                                    <label class="block font-semibold text-slate-700 mb-1">Kode Supplier <span class="text-red-500">*</span></label>
                                    <input type="text" name="code" x-model="editData.code" required class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-[#8F0A0D] text-slate-800 font-mono">
                                </div>
                                <div>
                                    <label class="block font-semibold text-slate-700 mb-1">Status Kemitraan <span class="text-red-500">*</span></label>
                                    <select name="status" x-model="editData.status" class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-[#8F0A0D] text-slate-800 bg-white cursor-pointer font-medium">
                                        <option value="Aktif">Aktif &amp; Terverifikasi</option>
                                        <option value="Nonaktif">Nonaktif</option>
                                    </select>
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block font-semibold text-slate-700 mb-1">Brand Suplai</label>
                                    <input type="text" name="products_supplied" x-model="editData.products_supplied" class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-[#8F0A0D] text-slate-800">
                                </div>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <div class="flex items-center gap-2 pb-1 border-b border-slate-100">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#8F0A0D]"></span>
                                <h4 class="font-bold text-slate-800 uppercase tracking-wider text-[11px]">• 2. KONTAK PIC &amp; ALAMAT</h4>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                <div>
                                    <label class="block font-semibold text-slate-700 mb-1">Nama PIC Kontak</label>
                                    <input type="text" name="contact_person" x-model="editData.contact_person" class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-[#8F0A0D] text-slate-800">
                                </div>
                                <div>
                                    <label class="block font-semibold text-slate-700 mb-1">No. Telepon / WA</label>
                                    <input type="text" name="phone" x-model="editData.phone" class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-[#8F0A0D] text-slate-800 font-mono">
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block font-semibold text-slate-700 mb-1">Email Resmi</label>
                                    <input type="email" name="email" x-model="editData.email" class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-[#8F0A0D] text-slate-800">
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block font-semibold text-slate-700 mb-1">Alamat Kantor / Gudang</label>
                                    <textarea name="address" x-model="editData.address" rows="2" class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-[#8F0A0D] text-slate-800"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="px-6 py-3.5 bg-[#F8FAFC] border-t border-[#E2E8F0] flex items-center justify-end gap-3 shrink-0">
                        <button type="button" @click="showEditModal = false" class="py-2.5 px-4 rounded-xl bg-white text-[#334155] border border-[#CBD5E1] font-bold text-xs hover:bg-[#F1F5F9] transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="btn-ipnet-gradient py-2.5 px-5 rounded-xl font-bold text-xs text-white shadow-xs transition flex items-center gap-1.5 cursor-pointer">
                            <span>Perbarui Supplier</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>

    <!-- MODAL DETAIL SUPPLIER (GAMBAR 2 STYLE) -->
    <template x-teleport="body">
        <div x-show="showDetailModal" 
             x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-[#0F172A]/60 z-[99999] flex items-center justify-center p-3 sm:p-5 backdrop-blur-xs"
             @click.self="showDetailModal = false"
             @keydown.escape.window="showDetailModal = false">
            
            <div class="bg-white rounded-2xl w-[640px] max-w-full max-h-[90vh] flex flex-col text-left shadow-[0_20px_60px_rgba(15,23,42,0.25)] border border-[#E2E8F0] overflow-hidden">
                <!-- Modal Header -->
                <div class="px-6 py-4 bg-[#F8FAFC] border-b border-[#E2E8F0] flex items-start justify-between shrink-0">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-[11px] font-mono px-2 py-0.5 rounded bg-slate-100 text-slate-700 font-bold border border-slate-200" x-text="detailData.code"></span>
                            <span class="text-[11px] font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200/60" x-text="detailData.status || 'Aktif'"></span>
                        </div>
                        <h3 class="text-base font-bold text-slate-900 leading-snug" x-text="detailData.name"></h3>
                        <p class="text-xs text-slate-500 mt-0.5" x-text="'Produk Suplai: ' + (detailData.products_supplied || '-')"></p>
                    </div>
                    <button type="button" @click="showDetailModal = false" class="text-slate-400 hover:text-slate-600 cursor-pointer p-1 rounded-lg hover:bg-slate-100 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-6 overflow-y-auto space-y-4 text-xs flex-1 min-h-0">
                    <div class="grid grid-cols-2 gap-3">
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                            <p class="text-[10.5px] font-semibold text-slate-400 uppercase">PIC Kontak</p>
                            <p class="font-bold text-slate-800 text-[12px] mt-0.5" x-text="detailData.contact_person || '-'"></p>
                        </div>
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                            <p class="text-[10.5px] font-semibold text-slate-400 uppercase">No. Telepon / WA</p>
                            <p class="font-mono font-bold text-slate-800 text-[12px] mt-0.5" x-text="detailData.phone || '-'"></p>
                        </div>
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 col-span-2">
                            <p class="text-[10.5px] font-semibold text-slate-400 uppercase">Email Resmi</p>
                            <p class="font-mono font-bold text-slate-800 text-[12px] mt-0.5" x-text="detailData.email || '-'"></p>
                        </div>
                    </div>

                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100 space-y-1">
                        <p class="text-[10.5px] font-semibold text-slate-400 uppercase">Alamat Kantor / Pergudangan</p>
                        <p class="text-slate-700 leading-relaxed mt-1" x-text="detailData.address || 'Alamat belum dilengkapi.'"></p>
                    </div>

                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100 space-y-1">
                        <p class="text-[10.5px] font-semibold text-slate-400 uppercase">Catatan Tambahan &amp; Ketentuan TOP</p>
                        <p class="text-slate-700 leading-relaxed mt-1" x-text="detailData.notes || 'Tidak ada catatan khusus.'"></p>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="px-6 py-3.5 bg-[#F8FAFC] border-t border-[#E2E8F0] flex items-center justify-end shrink-0">
                    <button type="button" @click="showDetailModal = false" class="py-2.5 px-4 rounded-xl bg-white text-[#334155] border border-[#CBD5E1] font-bold text-xs hover:bg-[#F1F5F9] transition cursor-pointer">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </template>
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
