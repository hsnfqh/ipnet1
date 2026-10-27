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

    <!-- MODAL CREATE WAREHOUSE -->
    <div x-show="showCreateModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.away="showCreateModal = false" class="bg-white rounded-2xl shadow-2xl border border-slate-200 max-w-lg w-full p-6">
            <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-200">
                <h3 class="text-[17px] font-bold text-[#1E293B]">Tambah Titik Lokasi Gudang</h3>
                <button type="button" @click="showCreateModal = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
            </div>

            <form action="{{ route('warehouse.warehouses.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-[12px] font-semibold text-[#1E293B] mb-1">Nama Fasilitas Gudang *</label>
                    <input type="text" name="name" required class="wms-input text-[13px]" placeholder="e.g. Gudang Utama IPNET Jakarta">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[12px] font-semibold text-[#1E293B] mb-1">Kode Gudang (Otomatis jika kosong)</label>
                        <input type="text" name="code" class="wms-input text-[13px]" placeholder="e.g. WH-JKT-01">
                    </div>
                    <div>
                        <label class="block text-[12px] font-semibold text-[#1E293B] mb-1">Status Operasional</label>
                        <select name="status" class="wms-input text-[13px]">
                            <option value="Operasional">Operasional</option>
                            <option value="Penuh">Penuh</option>
                            <option value="Maintenance">Maintenance</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[12px] font-semibold text-[#1E293B] mb-1">Nama PIC Gudang</label>
                        <input type="text" name="pic_name" class="wms-input text-[13px]" placeholder="e.g. Ahmad">
                    </div>
                    <div>
                        <label class="block text-[12px] font-semibold text-[#1E293B] mb-1">No. Kontak PIC</label>
                        <input type="text" name="pic_phone" class="wms-input text-[13px]" placeholder="e.g. 081298765432">
                    </div>
                </div>
                <div>
                    <label class="block text-[12px] font-semibold text-[#1E293B] mb-1">Kapasitas Rak / Ruang</label>
                    <input type="text" name="capacity_desc" class="wms-input text-[13px]" placeholder="e.g. 600 Unit / 12 Rak Rackmount">
                </div>
                <div>
                    <label class="block text-[12px] font-semibold text-[#1E293B] mb-1">Alamat Lengkap</label>
                    <textarea name="address" rows="2" class="wms-input text-[13px]" placeholder="Alamat fisik pergudangan..."></textarea>
                </div>
                <div>
                    <label class="block text-[12px] font-semibold text-[#1E293B] mb-1">Catatan Akses / Keamanan</label>
                    <textarea name="notes" rows="2" class="wms-input text-[13px]" placeholder="Jam operasional loading dock, akses security..."></textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-200">
                    <button type="button" @click="showCreateModal = false" class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 font-semibold text-[13px]">Batal</button>
                    <button type="submit" class="btn-ipnet-gradient px-5 py-2.5 rounded-xl font-bold text-[13px]">Simpan Gudang</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL EDIT WAREHOUSE -->
    <div x-show="showEditModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.away="showEditModal = false" class="bg-white rounded-2xl shadow-2xl border border-slate-200 max-w-lg w-full p-6">
            <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-200">
                <h3 class="text-[17px] font-bold text-[#1E293B]">Edit Data Lokasi Gudang</h3>
                <button type="button" @click="showEditModal = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
            </div>

            <form :action="'/warehouse/warehouses/' + editData.id" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-[12px] font-semibold text-[#1E293B] mb-1">Nama Fasilitas *</label>
                    <input type="text" name="name" x-model="editData.name" required class="wms-input text-[13px]">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[12px] font-semibold text-[#1E293B] mb-1">Kode Gudang *</label>
                        <input type="text" name="code" x-model="editData.code" required class="wms-input text-[13px]">
                    </div>
                    <div>
                        <label class="block text-[12px] font-semibold text-[#1E293B] mb-1">Status</label>
                        <select name="status" x-model="editData.status" class="wms-input text-[13px]">
                            <option value="Operasional">Operasional</option>
                            <option value="Penuh">Penuh</option>
                            <option value="Maintenance">Maintenance</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[12px] font-semibold text-[#1E293B] mb-1">PIC</label>
                        <input type="text" name="pic_name" x-model="editData.pic_name" class="wms-input text-[13px]">
                    </div>
                    <div>
                        <label class="block text-[12px] font-semibold text-[#1E293B] mb-1">Kontak PIC</label>
                        <input type="text" name="pic_phone" x-model="editData.pic_phone" class="wms-input text-[13px]">
                    </div>
                </div>
                <div>
                    <label class="block text-[12px] font-semibold text-[#1E293B] mb-1">Kapasitas</label>
                    <input type="text" name="capacity_desc" x-model="editData.capacity_desc" class="wms-input text-[13px]">
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
