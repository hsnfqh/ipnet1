@extends('layouts.app')

@section('title', 'Kategori Produk - Warehouse IPNET')

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
<div class="flex h-screen overflow-hidden bg-[#F8FAFC] font-sans" x-data="categoryManager()">
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Kategori Produk Warehouse'])
        
        <div class="p-4 sm:p-6 lg:p-7 space-y-6 max-w-[1600px] mx-auto">
            
            <!-- HEADER -->
            <div class="ipnet-card p-5 sm:p-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#8F0A0D]"></span>
                            <h2 class="text-[20px] font-bold text-[#1E293B]">Master Kategori Produk</h2>
                        </div>
                        <p class="text-[13px] text-[#64748B] mt-0.5">
                            Klasifikasi seluruh jenis perangkat keras, sistem keamanan, infrastruktur optik, dan aksesoris data center.
                        </p>
                    </div>

                    <div class="flex items-center gap-3">
                        <button type="button" 
                                @click="openCreateModal()"
                                class="btn-ipnet-gradient px-4 py-2.5 rounded-xl font-bold text-[13px] shadow-sm flex items-center gap-2 cursor-pointer transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span>Tambah Kategori Baru</span>
                        </button>
                    </div>
                </div>

                <!-- SEARCH & FILTER -->
                <form action="{{ route('warehouse.categories.index') }}" method="GET" class="mt-5 pt-4 border-t border-[#E2E8F0] flex flex-col sm:flex-row items-center gap-3">
                    <div class="flex-1 w-full">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau kode kategori..." class="wms-input text-[13px]">
                    </div>
                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <button type="submit" class="bg-[#1E293B] hover:bg-[#0F172A] text-white px-5 py-2.5 rounded-xl text-[13px] font-semibold transition">
                            Cari
                        </button>
                        @if(request('search'))
                            <a href="{{ route('warehouse.categories.index') }}" class="bg-slate-200 hover:bg-slate-300 text-slate-700 px-3.5 py-2.5 rounded-xl text-[13px] font-semibold transition">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- CATEGORY GRID CARDS -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @forelse($categories as $cat)
                    <div class="ipnet-card p-5 hover:border-[#CBD5E1] transition flex flex-col justify-between">
                        <div>
                            <div class="flex items-start justify-between mb-2">
                                <span class="px-2.5 py-1 text-[11px] font-mono font-bold rounded-md bg-slate-100 text-slate-700 border border-slate-200">
                                    {{ $cat->code }}
                                </span>
                                <span class="px-2.5 py-0.5 text-[10.5px] font-bold rounded-full {{ $cat->status === 'Aktif' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $cat->status }}
                                </span>
                            </div>
                            <h3 class="text-[16px] font-bold text-[#1E293B]">{{ $cat->name }}</h3>
                            <p class="text-[12px] text-[#64748B] mt-1 line-clamp-2 leading-relaxed">
                                {{ $cat->description ?: 'Tidak ada deskripsi khusus.' }}
                            </p>
                        </div>

                        <div class="mt-4 pt-3 border-t border-[#F1F5F9] flex items-center justify-between">
                            <div class="text-[12px]">
                                <span class="text-[#64748B]">Tersimpan:</span>
                                <strong class="text-[#8F0A0D] font-bold">{{ number_format($cat->products_sum_current_stock ?: 0) }} Unit</strong>
                                <span class="text-[11px] text-slate-400">({{ $cat->products_count }} model)</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <button type="button" 
                                        @click="openEditModal({{ json_encode($cat) }})"
                                        class="p-1.5 rounded-lg text-blue-600 hover:bg-blue-50 transition" title="Edit Kategori">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                <form action="{{ route('warehouse.categories.destroy', $cat->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus kategori ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded-lg text-red-600 hover:bg-red-50 transition" title="Hapus Kategori">
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
                        Belum ada kategori yang terdaftar.
                    </div>
                @endforelse
            </div>

            @if($categories->hasPages())
                <div class="ipnet-card p-4">
                    {{ $categories->links() }}
                </div>
            @endif

        </div>
    </div>

    <!-- MODAL CREATE CATEGORY -->
    <div x-show="showCreateModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.away="showCreateModal = false" class="bg-white rounded-2xl shadow-2xl border border-slate-200 max-w-md w-full p-6">
            <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-200">
                <h3 class="text-[17px] font-bold text-[#1E293B]">Tambah Kategori Baru</h3>
                <button type="button" @click="showCreateModal = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
            </div>

            <form action="{{ route('warehouse.categories.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-[12px] font-semibold text-[#1E293B] mb-1">Nama Kategori *</label>
                    <input type="text" name="name" required class="wms-input text-[13px]" placeholder="e.g. Next-Gen Firewall & Security">
                </div>
                <div>
                    <label class="block text-[12px] font-semibold text-[#1E293B] mb-1">Kode Kategori (Opsional, otomatis jika kosong)</label>
                    <input type="text" name="code" class="wms-input text-[13px]" placeholder="e.g. CAT-FW">
                </div>
                <div>
                    <label class="block text-[12px] font-semibold text-[#1E293B] mb-1">Status</label>
                    <select name="status" class="wms-input text-[13px]">
                        <option value="Aktif">Aktif</option>
                        <option value="Nonaktif">Nonaktif</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[12px] font-semibold text-[#1E293B] mb-1">Deskripsi Singkat</label>
                    <textarea name="description" rows="3" class="wms-input text-[13px]" placeholder="Penjelasan jenis perangkat dalam kategori ini..."></textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-200">
                    <button type="button" @click="showCreateModal = false" class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 font-semibold text-[13px]">Batal</button>
                    <button type="submit" class="btn-ipnet-gradient px-5 py-2.5 rounded-xl font-bold text-[13px]">Simpan Kategori</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL EDIT CATEGORY -->
    <div x-show="showEditModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.away="showEditModal = false" class="bg-white rounded-2xl shadow-2xl border border-slate-200 max-w-md w-full p-6">
            <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-200">
                <h3 class="text-[17px] font-bold text-[#1E293B]">Edit Kategori Produk</h3>
                <button type="button" @click="showEditModal = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
            </div>

            <form :action="'/warehouse/categories/' + editData.id" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-[12px] font-semibold text-[#1E293B] mb-1">Nama Kategori *</label>
                    <input type="text" name="name" x-model="editData.name" required class="wms-input text-[13px]">
                </div>
                <div>
                    <label class="block text-[12px] font-semibold text-[#1E293B] mb-1">Kode Kategori *</label>
                    <input type="text" name="code" x-model="editData.code" required class="wms-input text-[13px]">
                </div>
                <div>
                    <label class="block text-[12px] font-semibold text-[#1E293B] mb-1">Status</label>
                    <select name="status" x-model="editData.status" class="wms-input text-[13px]">
                        <option value="Aktif">Aktif</option>
                        <option value="Nonaktif">Nonaktif</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[12px] font-semibold text-[#1E293B] mb-1">Deskripsi</label>
                    <textarea name="description" x-model="editData.description" rows="3" class="wms-input text-[13px]"></textarea>
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
function categoryManager() {
    return {
        showCreateModal: false,
        showEditModal: false,
        editData: {},
        openCreateModal() {
            this.showCreateModal = true;
        },
        openEditModal(cat) {
            this.editData = Object.assign({}, cat);
            this.showEditModal = true;
        }
    };
}
</script>
@endsection
