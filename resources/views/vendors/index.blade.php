@extends('layouts.app')

@section('title', 'Vendor - PT IP Network Solusindo')

@push('styles')
<style>
    .ipnet-card {
        background-color: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 20px;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.04);
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .ipnet-card:hover {
        border-color: #CBD5E1;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
    }
    .ipnet-badge-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background-color: #8F0A0D;
        display: inline-block;
        margin-right: 8px;
    }
    @keyframes fadeUpStagger {
        0% { opacity: 0; transform: translateY(16px); }
        100% { opacity: 1; transform: translateY(0); }
    }
    .anim-fade-up {
        animation: fadeUpStagger 0.5s cubic-bezier(0.16, 1, 0.3, 1) both;
    }
    .anim-delay-1 { animation-delay: 0.06s !important; }
    .anim-delay-2 { animation-delay: 0.12s !important; }
</style>
@endpush

@section('content')
<div class="flex h-screen overflow-hidden bg-[#F8FAFC] font-sans" x-data="vendorManagerPage()">
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Vendor'])
        
        <div class="p-4 sm:p-6 lg:p-7 space-y-5 max-w-[1600px] mx-auto">
            
            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between shadow-xs anim-fade-up">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 font-bold px-2">✕</button>
                </div>
            @endif

            <!-- ========================================================== -->
            <!-- 1. OFFICIAL IPNET SECTION HEADER & ACTION CONTROLS          -->
            <!-- ========================================================== -->
            <div class="ipnet-card p-5 sm:p-6 anim-fade-up anim-delay-1">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 pb-4 mb-4 border-b border-[#E2E8F0]">
                    <div>
                        <p class="text-[#8F0A0D] text-[12px] font-bold inline-flex items-center uppercase tracking-wider">
                            <span class="ipnet-badge-dot"></span> MITRA &amp; DISTRIBUTOR PRINSIPAL
                        </p>
                        <h2 class="text-[20px] font-bold text-[#1E293B] tracking-tight">Database Vendor &amp; Principal Partner</h2>
                        <p class="text-[13px] text-[#64748B] mt-0.5">Kelola data mitra distributor, prinsipal teknologi, kontak channel manager, dan lini produk resmi</p>
                    </div>

                    <div class="flex flex-wrap items-center gap-3 shrink-0">
                        <div class="px-3.5 py-2 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0] text-[12px] font-bold text-[#1E293B] flex items-center gap-1.5 shadow-xs">
                            <span class="text-[#64748B]">Total Vendor:</span>
                            <span class="text-[#8F0A0D] font-extrabold">{{ $vendors->total() }}</span>
                        </div>

                        <button type="button" 
                                @click="openAddModal()"
                                class="btn-ipnet-gradient px-4 py-2.5 rounded-xl font-bold text-[13px] flex items-center gap-2 cursor-pointer shadow-md hover:shadow-lg active:scale-[0.98] transition-all">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span>Add New Vendor</span>
                        </button>
                    </div>
                </div>

                {{-- Filter & Search Toolbar --}}
                <form method="GET" action="{{ route('vendors.index') }}" class="flex flex-col sm:flex-row items-center gap-3">
                    <div class="relative flex-1 min-w-[260px] w-full">
                        <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-[#94A3B8]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}"
                               placeholder="Search nama vendor, departemen, channel manager, kategori..." 
                               class="w-full pl-9 pr-3.5 py-2 rounded-xl border border-[#CBD5E1] text-[12.5px] font-semibold text-[#1E293B] bg-white outline-none hover:border-[#94A3B8] focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition-all shadow-xs placeholder-[#94A3B8]">
                    </div>

                    @if(request('search'))
                        <a href="{{ route('vendors.index') }}" 
                           class="px-3 py-2 text-[12px] font-bold text-[#64748B] hover:text-[#8F0A0D] bg-[#F8FAFC] hover:bg-[#FEF2F2] border border-[#E2E8F0] hover:border-[#FCA5A5] rounded-xl transition cursor-pointer">
                            Reset Filter
                        </a>
                    @endif
                </form>
            </div>

            <!-- ========================================================== -->
            <!-- 2. DATA TABLE CARD                                         -->
            <!-- ========================================================== -->
            <div class="ipnet-card overflow-hidden anim-fade-up anim-delay-2">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-[#F8FAFC] border-b border-[#E2E8F0] text-[#64748B] uppercase text-[11px] font-bold">
                                <th class="py-3.5 px-6 font-bold flex items-center gap-1">
                                    <span>NAME</span>
                                    <svg class="w-3.5 h-3.5 text-[#94A3B8]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7"/></svg>
                                </th>
                                <th class="py-3.5 px-6 font-bold">DEPARTMENT</th>
                                <th class="py-3.5 px-6 font-bold">CHANNEL &amp; KATEGORI</th>
                                <th class="py-3.5 px-6 font-bold text-right">AKSI</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#F1F5F9] font-medium text-[#1E293B]">
                            @forelse($vendors as $vendor)
                                <tr class="hover:bg-[#F8FAFC] transition">
                                    {{-- Name --}}
                                    <td class="py-4 px-6 font-bold text-[#1E293B] text-[13px]">
                                        {{ $vendor->name }}
                                    </td>

                                    {{-- Department / Category --}}
                                    <td class="py-4 px-6 text-[#64748B] text-xs font-semibold">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md bg-[#F1F5F9] border border-[#E2E8F0] text-[#475569]">
                                            {{ $vendor->department ?: ($vendor->product_category ?: 'DEPT01') }}
                                        </span>
                                    </td>

                                    {{-- Channel Manager & Kategori --}}
                                    <td class="py-4 px-6 text-[#64748B] text-xs">
                                        <div class="font-bold text-[#1E293B]">{{ $vendor->channel_manager ?: '-' }}</div>
                                        <div class="text-[11px] text-[#94A3B8]">{{ $vendor->phone ?: ($vendor->email ?: 'Tidak ada kontak') }}</div>
                                    </td>

                                    {{-- Action Buttons --}}
                                    <td class="py-4 px-6 text-right whitespace-nowrap">
                                        <div class="inline-flex items-center gap-2">
                                            <button type="button" 
                                                    @click="openDetailsModal({{ json_encode($vendor) }})"
                                                    class="px-3.5 py-1.5 rounded-lg border border-[#CBD5E1] text-[#1E293B] hover:bg-[#F8FAFC] hover:border-[#94A3B8] text-[11px] font-bold uppercase transition cursor-pointer">
                                                DETAILS
                                            </button>

                                            <button type="button" 
                                                    @click="confirmDelete({{ $vendor->id }}, '{{ addslashes($vendor->name) }}')"
                                                    class="btn-ipnet-gradient px-3.5 py-1.5 rounded-lg text-white text-[11px] font-bold uppercase shadow-xs hover:shadow-md transition cursor-pointer">
                                                HAPUS
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-12 text-center text-[#94A3B8] text-xs">
                                        Tidak ada data vendor yang ditemukan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($vendors->hasPages())
                    <div class="p-4 border-t border-[#E2E8F0] flex items-center justify-between text-xs text-[#64748B]">
                        <div>
                            Menampilkan {{ $vendors->firstItem() ?? 0 }}-{{ $vendors->lastItem() ?? 0 }} dari {{ $vendors->total() }}
                        </div>
                        <div>
                            {{ $vendors->links() }}
                        </div>
                    </div>
                @endif
            </div>

        </div>
    </div>

    {{-- MODAL: + ADD NEW VENDOR --}}
    <div x-show="isAddModalOpen" x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
        <div @click.away="isAddModalOpen = false" 
             class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-gray-100 space-y-4 max-h-[90vh] overflow-y-auto">
            
            <div class="flex items-center justify-between border-b pb-3">
                <h3 class="text-base font-bold text-gray-900">Tambah Vendor / Distributor Baru</h3>
                <button type="button" @click="isAddModalOpen = false" class="text-gray-400 hover:text-gray-700 text-lg font-bold">✕</button>
            </div>

            <form action="{{ route('vendors.store') }}" method="POST" class="space-y-4 text-xs font-semibold">
                @csrf
                <div>
                    <label class="block text-gray-700 mb-1">Nama Vendor / Distributor <span class="text-red-500">*</span></label>
                    <input type="text" name="name" required placeholder="Contoh: Blue Power Technology / DELL Indonesia"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs font-normal text-gray-900 focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D]">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-gray-700 mb-1">Departemen / Unit</label>
                        <input type="text" name="department" placeholder="Contoh: DEPT01, Extreme"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs font-normal text-gray-900 focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D]">
                    </div>
                    <div>
                        <label class="block text-gray-700 mb-1">Channel Manager / PIC</label>
                        <input type="text" name="channel_manager" placeholder="Nama PIC Channel"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs font-normal text-gray-900 focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D]">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-gray-700 mb-1">Kategori Produk</label>
                        <input type="text" name="product_category" placeholder="Network, Security, Server..."
                               class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs font-normal text-gray-900 focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D]">
                    </div>
                    <div>
                        <label class="block text-gray-700 mb-1">Nomor Kontak / Telepon</label>
                        <input type="text" name="phone" placeholder="021-xxxxxxx"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs font-normal text-gray-900 focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D]">
                    </div>
                </div>

                <div>
                    <label class="block text-gray-700 mb-1">Email Resmi Vendor</label>
                    <input type="email" name="email" placeholder="channel@vendor.com"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs font-normal text-gray-900 focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D]">
                </div>

                <div>
                    <label class="block text-gray-700 mb-1">Alamat Kantor Vendor</label>
                    <textarea name="address" rows="2" placeholder="Alamat lengkap distributor..."
                              class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs font-normal text-gray-900 focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D]"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t">
                    <button type="button" @click="isAddModalOpen = false" class="px-4 py-2 rounded-xl border border-gray-200 text-gray-600 font-bold hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="submit" class="btn-ipnet-gradient px-5 py-2.5 rounded-xl font-bold shadow-md cursor-pointer">
                        Simpan Vendor
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL: DETAILS / EDIT VENDOR --}}
    <div x-show="isDetailsModalOpen" x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
        <div @click.away="isDetailsModalOpen = false" 
             class="bg-white rounded-2xl max-w-xl w-full p-6 shadow-2xl border border-gray-100 space-y-4 max-h-[90vh] overflow-y-auto">
            
            <div class="flex items-center justify-between border-b pb-3">
                <div>
                    <h3 class="text-base font-bold text-gray-900">Detail &amp; Edit Vendor</h3>
                    <p class="text-xs text-gray-400 mt-0.5" x-text="selectedVendor.name"></p>
                </div>
                <button type="button" @click="isDetailsModalOpen = false" class="text-gray-400 hover:text-gray-700 text-lg font-bold">✕</button>
            </div>

            <form :action="'{{ url('vendors') }}/' + selectedVendor.id" method="POST" class="space-y-4 text-xs font-semibold">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-gray-700 mb-1">Nama Vendor / Distributor <span class="text-red-500">*</span></label>
                    <input type="text" name="name" x-model="selectedVendor.name" required
                           class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs font-normal text-gray-900 focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D]">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-gray-700 mb-1">Departemen / Unit</label>
                        <input type="text" name="department" x-model="selectedVendor.department"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs font-normal text-gray-900 focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D]">
                    </div>
                    <div>
                        <label class="block text-gray-700 mb-1">Channel Manager / PIC</label>
                        <input type="text" name="channel_manager" x-model="selectedVendor.channel_manager"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs font-normal text-gray-900 focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D]">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-gray-700 mb-1">Kategori Produk</label>
                        <input type="text" name="product_category" x-model="selectedVendor.product_category"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs font-normal text-gray-900 focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D]">
                    </div>
                    <div>
                        <label class="block text-gray-700 mb-1">Nomor Telepon</label>
                        <input type="text" name="phone" x-model="selectedVendor.phone"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs font-normal text-gray-900 focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D]">
                    </div>
                </div>

                <div>
                    <label class="block text-gray-700 mb-1">Email Resmi</label>
                    <input type="email" name="email" x-model="selectedVendor.email"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs font-normal text-gray-900 focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D]">
                </div>

                <div>
                    <label class="block text-gray-700 mb-1">Alamat Kantor</label>
                    <textarea name="address" rows="2" x-model="selectedVendor.address"
                              class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs font-normal text-gray-900 focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D]"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t">
                    <button type="button" @click="isDetailsModalOpen = false" class="px-4 py-2 rounded-xl border border-gray-200 text-gray-600 font-bold hover:bg-gray-50">
                        Tutup
                    </button>
                    <button type="submit" class="btn-ipnet-gradient px-5 py-2.5 rounded-xl font-bold shadow-md cursor-pointer">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- DELETE CONFIRM FORM --}}
    <form id="deleteVendorForm" method="POST" style="display:none;">
        @csrf
        @method('DELETE')
    </form>

</div>

<script>
    function vendorManagerPage() {
        return {
            isAddModalOpen: false,
            isDetailsModalOpen: false,
            selectedVendor: {},

            openAddModal() {
                this.isAddModalOpen = true;
            },

            openDetailsModal(vendor) {
                this.selectedVendor = Object.assign({}, vendor);
                this.isDetailsModalOpen = true;
            },

            confirmDelete(id, name) {
                if (confirm(`Apakah Anda yakin ingin menghapus data vendor "${name}"?`)) {
                    const form = document.getElementById('deleteVendorForm');
                    form.action = `{{ url('vendors') }}/${id}`;
                    form.submit();
                }
            }
        };
    }
</script>
@endsection
