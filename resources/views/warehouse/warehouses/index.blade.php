@extends('layouts.app')

@section('title', 'Lokasi Gudang Warehouse - PT IP Network Solusindo')

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
<div class="flex h-screen overflow-hidden bg-[#F8FAFC] font-sans" x-data="warehouseLocationManager()">
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Master Lokasi Gudang (Warehouse)'])
        
        <div class="p-4 sm:p-6 lg:p-7 space-y-6 max-w-[1600px] mx-auto">
            
            <!-- HEADER -->
            <div class="ipnet-card p-5 sm:p-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#8F0A0D]"></span>
                            <h2 class="text-[20px] font-bold text-[#1E293B]">Titik Lokasi Gudang &amp; Fasilitas Staging</h2>
                        </div>
                        <p class="text-[13px] text-[#64748B] mt-0.5">
                            Manajemen multi-lokasi gudang penyimpanan, buffer stock cadangan, lab pre-konfigurasi, dan hub logistik regional.
                        </p>
                    </div>

                    <div class="flex items-center gap-3">
                        <button type="button" 
                                @click="openCreateModal()"
                                class="btn-ipnet-gradient px-4 py-2.5 rounded-xl font-bold text-[13px] shadow-sm flex items-center gap-2 cursor-pointer transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span>Tambah Titik Gudang</span>
                        </button>
                    </div>
                </div>

                <!-- SEARCH -->
                <form action="{{ route('warehouse.warehouses.index') }}" method="GET" class="mt-5 pt-4 border-t border-[#E2E8F0] flex flex-col sm:flex-row items-center gap-3">
                    <div class="flex-1 w-full">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama gudang, kode, PIC, atau alamat..." class="wms-input text-[13px]">
                    </div>
                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <button type="submit" class="bg-[#1E293B] hover:bg-[#0F172A] text-white px-5 py-2.5 rounded-xl text-[13px] font-semibold transition">
                            Cari
                        </button>
                        @if(request('search'))
                            <a href="{{ route('warehouse.warehouses.index') }}" class="bg-slate-200 hover:bg-slate-300 text-slate-700 px-3.5 py-2.5 rounded-xl text-[13px] font-semibold transition">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- WAREHOUSE GRID CARDS -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                @forelse($warehouses as $wh)
                    <div class="ipnet-card p-5 sm:p-6 hover:border-[#CBD5E1] transition flex flex-col justify-between">
                        <div>
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-9 h-9 rounded-xl bg-[#FEF2F2] flex items-center justify-center text-[#8F0A0D]">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <span class="px-2 py-0.5 text-[10.5px] font-mono font-bold rounded bg-slate-100 text-slate-700">
                                            {{ $wh->code }}
                                        </span>
                                        <h3 class="text-[16px] font-bold text-[#1E293B] mt-0.5">{{ $wh->name }}</h3>
                                    </div>
                                </div>
                                <span class="px-2.5 py-0.5 text-[10.5px] font-bold rounded-full {{ $wh->status === 'Operasional' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                                    {{ $wh->status }}
                                </span>
                            </div>

                            <p class="text-[12.5px] text-[#64748B] flex items-start gap-1.5 mt-2">
                                <svg class="w-4 h-4 text-slate-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <span>{{ $wh->address ?: 'Alamat belum diisi.' }}</span>
                            </p>

                            <!-- Mini Metrics Box -->
                            <div class="grid grid-cols-2 gap-2 mt-4 p-3 rounded-xl bg-slate-50 border border-slate-200 text-[12px]">
                                <div>
                                    <span class="text-[#64748B] block text-[11px]">PIC Gudang</span>
                                    <strong class="text-[#1E293B]">{{ $wh->pic_name ?: 'Ahmad' }}</strong>
                                    <div class="text-[11px] text-slate-500">{{ $wh->pic_phone ?: '-' }}</div>
                                </div>
                                <div>
                                    <span class="text-[#64748B] block text-[11px]">Kapasitas Fasilitas</span>
                                    <strong class="text-[#1E293B]">{{ $wh->capacity_desc ?: '500 Unit' }}</strong>
                                    <div class="text-[11px] text-[#8F0A0D] font-bold">{{ number_format($wh->products_sum_current_stock ?: 0) }} Unit Terisi</div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-t border-[#F1F5F9] flex items-center justify-between">
                            <div class="text-[11.5px] text-[#64748B]">
                                <span>{{ $wh->products_count }} SKU Produk</span> • 
                                <span>{{ $wh->delivery_orders_count }} DO Keluar</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <button type="button" 
                                        @click="openEditModal({{ json_encode($wh) }})"
                                        class="p-1.5 rounded-lg text-blue-600 hover:bg-blue-50 transition" title="Edit Gudang">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                <form action="{{ route('warehouse.warehouses.destroy', $wh->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus lokasi gudang ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded-lg text-red-600 hover:bg-red-50 transition" title="Hapus Gudang">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full ipnet-card p-12 text-center text-[#94A3B8]">
                        Belum ada lokasi gudang yang tercatat.
                    </div>
                @endforelse
            </div>

            @if($warehouses->hasPages())
                <div class="ipnet-card p-4">
                    {{ $warehouses->links() }}
                </div>
            @endif

        </div>
    </div>

    <!-- MODAL CREATE WAREHOUSE (GAMBAR 2 STYLE) -->
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
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-900">Tambah Titik Lokasi Gudang Baru</h3>
                            <p class="text-xs text-slate-400 mt-0.5">Pencatatan fasilitas penyimpanan fisik, kapasitas rak, dan PIC operasional</p>
                        </div>
                    </div>
                    <button type="button" @click="showCreateModal = false" class="text-slate-400 hover:text-slate-600 cursor-pointer p-1 rounded-lg hover:bg-slate-100 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <form action="{{ route('warehouse.warehouses.store') }}" method="POST" class="flex flex-col flex-1 min-h-0 overflow-hidden m-0">
                    @csrf
                    <div class="p-6 overflow-y-auto space-y-5 text-xs flex-1 min-h-0">
                        <div class="space-y-3">
                            <div class="flex items-center gap-2 pb-1 border-b border-slate-100">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#8F0A0D]"></span>
                                <h4 class="font-bold text-slate-800 uppercase tracking-wider text-[11px]">• 1. FASILITAS &amp; STATUS GUDANG</h4>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                <div class="sm:col-span-2">
                                    <label class="block font-semibold text-slate-700 mb-1">Nama Fasilitas Gudang <span class="text-red-500">*</span></label>
                                    <input type="text" name="name" required placeholder="e.g. Gudang Utama IPNET Jakarta" class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-[#8F0A0D] text-slate-800">
                                </div>
                                <div>
                                    <label class="block font-semibold text-slate-700 mb-1">Kode Gudang (Opsional)</label>
                                    <input type="text" name="code" placeholder="Otomatis jika kosong (WH-...)" class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-[#8F0A0D] text-slate-800 font-mono">
                                </div>
                                <div>
                                    <label class="block font-semibold text-slate-700 mb-1">Status Operasional <span class="text-red-500">*</span></label>
                                    <select name="status" class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-[#8F0A0D] text-slate-800 bg-white cursor-pointer font-medium">
                                        <option value="Operasional">Operasional (Aktif)</option>
                                        <option value="Penuh">Kapasitas Penuh</option>
                                        <option value="Maintenance">Dalam Maintenance</option>
                                    </select>
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block font-semibold text-slate-700 mb-1">Estimasi Kapasitas Rak / Ruang</label>
                                    <input type="text" name="capacity_desc" placeholder="e.g. 600 Unit / 12 Rak Rackmount Server" class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-[#8F0A0D] text-slate-800">
                                </div>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <div class="flex items-center gap-2 pb-1 border-b border-slate-100">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#8F0A0D]"></span>
                                <h4 class="font-bold text-slate-800 uppercase tracking-wider text-[11px]">• 2. PIC OPERASIONAL &amp; ALAMAT FISIK</h4>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                <div>
                                    <label class="block font-semibold text-slate-700 mb-1">Nama PIC Pengelola Gudang</label>
                                    <input type="text" name="pic_name" placeholder="e.g. Ahmad Hasan" class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-[#8F0A0D] text-slate-800">
                                </div>
                                <div>
                                    <label class="block font-semibold text-slate-700 mb-1">No. Kontak PIC</label>
                                    <input type="text" name="pic_phone" placeholder="e.g. 081298765432" class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-[#8F0A0D] text-slate-800 font-mono">
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block font-semibold text-slate-700 mb-1">Alamat Lengkap Pergudangan</label>
                                    <textarea name="address" rows="2" placeholder="Gedung / Jalan / Area loading dock..." class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-[#8F0A0D] text-slate-800"></textarea>
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block font-semibold text-slate-700 mb-1">Catatan Akses &amp; Keamanan</label>
                                    <textarea name="notes" rows="2" placeholder="Ketentuan loading dock, kartu akses security, jam buka..." class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-[#8F0A0D] text-slate-800"></textarea>
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
                            <span>Simpan Gudang</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>

    <!-- MODAL EDIT WAREHOUSE (GAMBAR 2 STYLE) -->
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
                            <h3 class="text-sm font-bold text-slate-900">Perbarui Data Titik Gudang</h3>
                            <p class="text-xs text-slate-400 mt-0.5" x-text="'Kode Fasilitas: ' + (editData.code || '-')"></p>
                        </div>
                    </div>
                    <button type="button" @click="showEditModal = false" class="text-slate-400 hover:text-slate-600 cursor-pointer p-1 rounded-lg hover:bg-slate-100 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <form :action="'/warehouse/warehouses/' + editData.id" method="POST" class="flex flex-col flex-1 min-h-0 overflow-hidden m-0">
                    @csrf
                    @method('PUT')
                    <div class="p-6 overflow-y-auto space-y-5 text-xs flex-1 min-h-0">
                        <div class="space-y-3">
                            <div class="flex items-center gap-2 pb-1 border-b border-slate-100">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#8F0A0D]"></span>
                                <h4 class="font-bold text-slate-800 uppercase tracking-wider text-[11px]">• 1. FASILITAS &amp; STATUS GUDANG</h4>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                <div class="sm:col-span-2">
                                    <label class="block font-semibold text-slate-700 mb-1">Nama Fasilitas Gudang <span class="text-red-500">*</span></label>
                                    <input type="text" name="name" x-model="editData.name" required class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-[#8F0A0D] text-slate-800">
                                </div>
                                <div>
                                    <label class="block font-semibold text-slate-700 mb-1">Kode Gudang <span class="text-red-500">*</span></label>
                                    <input type="text" name="code" x-model="editData.code" required class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-[#8F0A0D] text-slate-800 font-mono">
                                </div>
                                <div>
                                    <label class="block font-semibold text-slate-700 mb-1">Status Operasional <span class="text-red-500">*</span></label>
                                    <select name="status" x-model="editData.status" class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-[#8F0A0D] text-slate-800 bg-white cursor-pointer font-medium">
                                        <option value="Operasional">Operasional (Aktif)</option>
                                        <option value="Penuh">Kapasitas Penuh</option>
                                        <option value="Maintenance">Dalam Maintenance</option>
                                    </select>
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block font-semibold text-slate-700 mb-1">Kapasitas Rak / Ruang</label>
                                    <input type="text" name="capacity_desc" x-model="editData.capacity_desc" class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-[#8F0A0D] text-slate-800">
                                </div>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <div class="flex items-center gap-2 pb-1 border-b border-slate-100">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#8F0A0D]"></span>
                                <h4 class="font-bold text-slate-800 uppercase tracking-wider text-[11px]">• 2. PIC OPERASIONAL &amp; ALAMAT FISIK</h4>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                <div>
                                    <label class="block font-semibold text-slate-700 mb-1">Nama PIC Pengelola Gudang</label>
                                    <input type="text" name="pic_name" x-model="editData.pic_name" class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-[#8F0A0D] text-slate-800">
                                </div>
                                <div>
                                    <label class="block font-semibold text-slate-700 mb-1">No. Kontak PIC</label>
                                    <input type="text" name="pic_phone" x-model="editData.pic_phone" class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-[#8F0A0D] text-slate-800 font-mono">
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block font-semibold text-slate-700 mb-1">Alamat Lengkap Pergudangan</label>
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
                            <span>Perbarui Gudang</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>
</div>

<script>
function warehouseLocationManager() {
    return {
        showCreateModal: false,
        showEditModal: false,
        editData: {},
        openCreateModal() {
            this.showCreateModal = true;
        },
        openEditModal(wh) {
            this.editData = Object.assign({}, wh);
            this.showEditModal = true;
        }
    };
}
</script>
@endsection
