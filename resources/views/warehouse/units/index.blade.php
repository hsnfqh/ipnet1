@extends('layouts.app')

@section('title', 'Satuan Unit - Warehouse IPNET')

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
<div class="flex h-screen overflow-hidden bg-[#F8FAFC] font-sans" x-data="unitManager()">
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Master Satuan Produk'])
        
        <div class="p-4 sm:p-6 lg:p-7 space-y-6 max-w-[1600px] mx-auto">
            
            <!-- HEADER -->
            <div class="ipnet-card p-5 sm:p-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#8F0A0D]"></span>
                            <h2 class="text-[20px] font-bold text-[#1E293B]">Master Satuan Unit Barang</h2>
                        </div>
                        <p class="text-[13px] text-[#64748B] mt-0.5">
                            Standarisasi satuan kuantitas logistik inventori (Unit, Pcs, Box, Roll, Meter, Set, Pack).
                        </p>
                    </div>

                    <div class="flex items-center gap-3">
                        <button type="button" 
                                @click="openCreateModal()"
                                class="btn-ipnet-gradient px-4 py-2.5 rounded-xl font-bold text-[13px] shadow-sm flex items-center gap-2 cursor-pointer transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span>Tambah Satuan Baru</span>
                        </button>
                    </div>
                </div>

                <!-- SEARCH -->
                <form action="{{ route('warehouse.units.index') }}" method="GET" class="mt-5 pt-4 border-t border-[#E2E8F0] flex flex-col sm:flex-row items-center gap-3">
                    <div class="flex-1 w-full">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau kode satuan..." class="wms-input text-[13px]">
                    </div>
                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <button type="submit" class="bg-[#1E293B] hover:bg-[#0F172A] text-white px-5 py-2.5 rounded-xl text-[13px] font-semibold transition">
                            Cari
                        </button>
                        @if(request('search'))
                            <a href="{{ route('warehouse.units.index') }}" class="bg-slate-200 hover:bg-slate-300 text-slate-700 px-3.5 py-2.5 rounded-xl text-[13px] font-semibold transition">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- UNITS TABLE -->
            <div class="ipnet-card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-[13px] border-collapse">
                        <thead>
                            <tr class="bg-[#F8FAFC] border-b border-[#E2E8F0] text-[#64748B] text-[11.5px] uppercase font-bold tracking-wider">
                                <th class="py-3.5 px-4">Kode</th>
                                <th class="py-3.5 px-4">Nama Satuan</th>
                                <th class="py-3.5 px-4">Singkatan</th>
                                <th class="py-3.5 px-4">Deskripsi / Peruntukan</th>
                                <th class="py-3.5 px-4 text-center">Penggunaan</th>
                                <th class="py-3.5 px-4 text-center">Status</th>
                                <th class="py-3.5 px-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#F1F5F9]">
                            @forelse($units as $unit)
                                <tr class="hover:bg-[#F8FAFC]/80 transition">
                                    <td class="py-3.5 px-4 font-mono font-bold text-[#8F0A0D]">
                                        {{ $unit->code }}
                                    </td>
                                    <td class="py-3.5 px-4 font-bold text-[#1E293B]">
                                        {{ $unit->name }}
                                    </td>
                                    <td class="py-3.5 px-4 font-semibold text-slate-700">
                                        {{ $unit->short_name }}
                                    </td>
                                    <td class="py-3.5 px-4 text-[#64748B] text-[12px]">
                                        {{ $unit->description ?: '-' }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        <span class="inline-block px-2.5 py-1 text-[11px] font-bold rounded-lg bg-slate-100 text-slate-700">
                                            {{ $unit->products_count }} Produk
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        <span class="px-2.5 py-0.5 text-[10.5px] font-bold rounded-full {{ $unit->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600' }}">
                                            {{ $unit->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        <div class="inline-flex items-center gap-1.5">
                                            <button type="button" 
                                                    @click="openEditModal({{ json_encode($unit) }})"
                                                    class="p-1.5 rounded-lg text-blue-600 hover:bg-blue-50 transition" title="Edit Satuan">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </button>
                                            <form action="{{ route('warehouse.units.destroy', $unit->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus satuan ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 rounded-lg text-red-600 hover:bg-red-50 transition" title="Hapus Satuan">
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
                                        Belum ada satuan yang tercatat.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($units->hasPages())
                    <div class="p-4 border-t border-[#E2E8F0]">
                        {{ $units->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>

    <!-- MODAL CREATE UNIT -->
    <div x-show="showCreateModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.away="showCreateModal = false" class="bg-white rounded-2xl shadow-2xl border border-slate-200 max-w-md w-full p-6">
            <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-200">
                <h3 class="text-[17px] font-bold text-[#1E293B]">Tambah Satuan Unit Baru</h3>
                <button type="button" @click="showCreateModal = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
            </div>

            <form action="{{ route('warehouse.units.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-[12px] font-semibold text-[#1E293B] mb-1">Nama Satuan Lengkap *</label>
                    <input type="text" name="name" required class="wms-input text-[13px]" placeholder="e.g. Pieces, Meter, Roll, Set">
                </div>
                <div>
                    <label class="block text-[12px] font-semibold text-[#1E293B] mb-1">Kode Satuan (Huruf Kapital) *</label>
                    <input type="text" name="code" required class="wms-input text-[13px]" placeholder="e.g. PCS, MTR, ROL, SET">
                </div>
                <div>
                    <label class="block text-[12px] font-semibold text-[#1E293B] mb-1">Singkatan Tampilan *</label>
                    <input type="text" name="short_name" required class="wms-input text-[13px]" placeholder="e.g. Pcs, M, Rol, Set">
                </div>
                <div>
                    <label class="block text-[12px] font-semibold text-[#1E293B] mb-1">Deskripsi Singkat</label>
                    <textarea name="description" rows="2" class="wms-input text-[13px]" placeholder="Penjelasan penggunaan satuan..."></textarea>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="is_active_create" name="is_active" value="1" checked class="rounded border-slate-300 text-[#8F0A0D] focus:ring-[#8F0A0D]">
                    <label for="is_active_create" class="text-[13px] font-semibold text-[#1E293B]">Satuan Aktif &amp; Siap Digunakan</label>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-200">
                    <button type="button" @click="showCreateModal = false" class="px-4 py-2 rounded-xl text-slate-600 hover:bg-slate-100 font-semibold text-[13px]">Batal</button>
                    <button type="submit" class="btn-ipnet-gradient px-5 py-2.5 rounded-xl font-bold text-[13px]">Simpan Satuan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL EDIT UNIT -->
    <div x-show="showEditModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div @click.away="showEditModal = false" class="bg-white rounded-2xl shadow-2xl border border-slate-200 max-w-md w-full p-6">
            <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-200">
                <h3 class="text-[17px] font-bold text-[#1E293B]">Edit Satuan Unit</h3>
                <button type="button" @click="showEditModal = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
            </div>

            <form :action="'/warehouse/units/' + editData.id" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-[12px] font-semibold text-[#1E293B] mb-1">Nama Satuan *</label>
                    <input type="text" name="name" x-model="editData.name" required class="wms-input text-[13px]">
                </div>
                <div>
                    <label class="block text-[12px] font-semibold text-[#1E293B] mb-1">Kode Satuan *</label>
                    <input type="text" name="code" x-model="editData.code" required class="wms-input text-[13px]">
                </div>
                <div>
                    <label class="block text-[12px] font-semibold text-[#1E293B] mb-1">Singkatan *</label>
                    <input type="text" name="short_name" x-model="editData.short_name" required class="wms-input text-[13px]">
                </div>
                <div>
                    <label class="block text-[12px] font-semibold text-[#1E293B] mb-1">Deskripsi</label>
                    <textarea name="description" x-model="editData.description" rows="2" class="wms-input text-[13px]"></textarea>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="is_active_edit" name="is_active" value="1" x-model="editData.is_active" class="rounded border-slate-300 text-[#8F0A0D] focus:ring-[#8F0A0D]">
                    <label for="is_active_edit" class="text-[13px] font-semibold text-[#1E293B]">Satuan Aktif</label>
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
function unitManager() {
    return {
        showCreateModal: false,
        showEditModal: false,
        editData: {},
        openCreateModal() {
            this.showCreateModal = true;
        },
        openEditModal(unit) {
            this.editData = Object.assign({}, unit);
            this.showEditModal = true;
        }
    };
}
</script>
@endsection
