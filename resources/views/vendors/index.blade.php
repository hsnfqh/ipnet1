@extends('layouts.app')

@section('title', 'Vendor - PT IP Network Solusindo')

@push('styles')
<style>
    :root {
        --ipnet-primary: #8F0A0D;
        --ipnet-primary-hover: #73080A;
        --ipnet-card-bg: #FFFFFF;
        --ipnet-card-border: #E2E8F0;
        --ipnet-text-main: #1E293B;
    }

    .btn-ipnet-primary {
        background: #DC2626;
        color: #FFFFFF;
        font-weight: 700;
        transition: all 0.2s ease;
        box-shadow: 0 2px 4px rgba(220, 38, 38, 0.2);
    }

    .btn-ipnet-primary:hover {
        background: #B91C1C;
        box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
        transform: translateY(-1px);
    }

    .btn-outline-details {
        background: #FFFFFF;
        border: 1px solid #CBD5E1;
        color: #334155;
        font-weight: 700;
        letter-spacing: 0.5px;
        transition: all 0.15s ease;
    }

    .btn-outline-details:hover {
        background: #F8FAFC;
        border-color: #94A3B8;
        color: #0F172A;
    }

    .btn-danger-delete {
        background: #DC2626;
        color: #FFFFFF;
        font-weight: 700;
        letter-spacing: 0.5px;
        transition: all 0.15s ease;
    }

    .btn-danger-delete:hover {
        background: #B91C1C;
    }
</style>
@endpush

@section('content')
<div class="flex h-screen overflow-hidden bg-[#F8FAFC] font-sans" x-data="vendorManagerPage()">
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Vendor'])
        
        <div class="p-4 sm:p-6 lg:p-8 space-y-6 max-w-7xl mx-auto">
            
            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 font-bold px-2">✕</button>
                </div>
            @endif

            {{-- HEADER: Title with Icon matching reference --}}
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-red-50 border border-red-200 text-[#8F0A0D] flex items-center justify-center">
                    <svg class="w-5 h-5 text-[#8F0A0D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-[#1E293B] tracking-tight">Vendor</h1>
            </div>

            {{-- CONTROLS BAR (Matching Reference Image) --}}
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
                {{-- Search Input --}}
                <form method="GET" action="{{ route('vendors.index') }}" class="relative w-full sm:w-80">
                    <svg class="w-4 h-4 absolute left-3.5 top-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="Search" 
                           class="w-full pl-9 pr-3.5 py-2.5 rounded-xl border border-gray-200 text-xs font-semibold text-gray-800 bg-white focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-all shadow-xs">
                </form>

                {{-- Add Vendor Button --}}
                <button type="button" 
                        @click="openAddModal()"
                        class="btn-ipnet-primary inline-flex items-center justify-center gap-1.5 px-5 py-2.5 rounded-xl text-xs font-bold shadow-md cursor-pointer whitespace-nowrap">
                    <span class="text-base leading-none font-bold">+</span>
                    <span>Add New Vendor</span>
                </button>
            </div>

            {{-- VENDORS TABLE (Matching Reference Image) --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="border-b border-slate-200 text-slate-500 uppercase text-[11px] font-bold">
                                <th class="py-3.5 px-6 font-bold flex items-center gap-1">
                                    <span>NAME</span>
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7"/></svg>
                                </th>
                                <th class="py-3.5 px-6 font-bold">DEPARTMENT</th>
                                <th class="py-3.5 px-6 font-bold text-right"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium text-slate-800">
                            @forelse($vendors as $vendor)
                                <tr class="hover:bg-slate-50/70 transition">
                                    {{-- Name --}}
                                    <td class="py-4 px-6 font-bold text-slate-900 text-[13px]">
                                        {{ $vendor->name }}
                                    </td>

                                    {{-- Department / Category --}}
                                    <td class="py-4 px-6 text-slate-600 text-xs font-semibold">
                                        {{ $vendor->department ?: ($vendor->product_category ?: 'DEPT01') }}
                                    </td>

                                    {{-- Action Buttons (DETAILS & DELETE) --}}
                                    <td class="py-4 px-6 text-right whitespace-nowrap">
                                        <div class="inline-flex items-center gap-2.5">
                                            <button type="button" 
                                                    @click="openDetailsModal({{ json_encode($vendor) }})"
                                                    class="btn-outline-details px-4 py-1.5 rounded-lg text-[11px] uppercase cursor-pointer">
                                                DETAILS
                                            </button>

                                            <button type="button" 
                                                    @click="confirmDelete({{ $vendor->id }}, '{{ addslashes($vendor->name) }}')"
                                                    class="btn-danger-delete px-4 py-1.5 rounded-lg text-[11px] uppercase cursor-pointer">
                                                DELETE
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="py-12 text-center text-slate-400 text-xs">
                                        Tidak ada data vendor yang ditemukan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($vendors->hasPages())
                    <div class="p-4 border-t border-slate-100 flex items-center justify-between">
                        <div class="text-xs text-slate-500">
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
                           class="w-full px-3.5 py-2 rounded-xl border border-gray-200 text-xs font-normal text-gray-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-gray-700 mb-1">Departemen / Unit</label>
                        <input type="text" name="department" placeholder="Contoh: DEPT01, Extreme"
                               class="w-full px-3.5 py-2 rounded-xl border border-gray-200 text-xs font-normal text-gray-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                    </div>
                    <div>
                        <label class="block text-gray-700 mb-1">Channel Manager / PIC</label>
                        <input type="text" name="channel_manager" placeholder="Nama PIC Channel"
                               class="w-full px-3.5 py-2 rounded-xl border border-gray-200 text-xs font-normal text-gray-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-gray-700 mb-1">Kategori Produk</label>
                        <input type="text" name="product_category" placeholder="Network, Security, Server..."
                               class="w-full px-3.5 py-2 rounded-xl border border-gray-200 text-xs font-normal text-gray-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                    </div>
                    <div>
                        <label class="block text-gray-700 mb-1">Nomor Kontak / Telepon</label>
                        <input type="text" name="phone" placeholder="021-xxxxxxx"
                               class="w-full px-3.5 py-2 rounded-xl border border-gray-200 text-xs font-normal text-gray-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                    </div>
                </div>

                <div>
                    <label class="block text-gray-700 mb-1">Email Resmi Vendor</label>
                    <input type="email" name="email" placeholder="channel@vendor.com"
                           class="w-full px-3.5 py-2 rounded-xl border border-gray-200 text-xs font-normal text-gray-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                </div>

                <div>
                    <label class="block text-gray-700 mb-1">Alamat Kantor Vendor</label>
                    <textarea name="address" rows="2" placeholder="Alamat lengkap distributor..."
                              class="w-full px-3.5 py-2 rounded-xl border border-gray-200 text-xs font-normal text-gray-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t">
                    <button type="button" @click="isAddModalOpen = false" class="px-4 py-2 rounded-xl border border-gray-200 text-gray-600 font-bold hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="submit" class="btn-ipnet-primary px-5 py-2 rounded-xl font-bold shadow-md">
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
                           class="w-full px-3.5 py-2 rounded-xl border border-gray-200 text-xs font-normal text-gray-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-gray-700 mb-1">Departemen / Unit</label>
                        <input type="text" name="department" x-model="selectedVendor.department"
                               class="w-full px-3.5 py-2 rounded-xl border border-gray-200 text-xs font-normal text-gray-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                    </div>
                    <div>
                        <label class="block text-gray-700 mb-1">Channel Manager / PIC</label>
                        <input type="text" name="channel_manager" x-model="selectedVendor.channel_manager"
                               class="w-full px-3.5 py-2 rounded-xl border border-gray-200 text-xs font-normal text-gray-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-gray-700 mb-1">Kategori Produk</label>
                        <input type="text" name="product_category" x-model="selectedVendor.product_category"
                               class="w-full px-3.5 py-2 rounded-xl border border-gray-200 text-xs font-normal text-gray-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                    </div>
                    <div>
                        <label class="block text-gray-700 mb-1">Nomor Telepon</label>
                        <input type="text" name="phone" x-model="selectedVendor.phone"
                               class="w-full px-3.5 py-2 rounded-xl border border-gray-200 text-xs font-normal text-gray-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                    </div>
                </div>

                <div>
                    <label class="block text-gray-700 mb-1">Email Resmi</label>
                    <input type="email" name="email" x-model="selectedVendor.email"
                           class="w-full px-3.5 py-2 rounded-xl border border-gray-200 text-xs font-normal text-gray-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                </div>

                <div>
                    <label class="block text-gray-700 mb-1">Alamat Kantor</label>
                    <textarea name="address" rows="2" x-model="selectedVendor.address"
                              class="w-full px-3.5 py-2 rounded-xl border border-gray-200 text-xs font-normal text-gray-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t">
                    <button type="button" @click="isDetailsModalOpen = false" class="px-4 py-2 rounded-xl border border-gray-200 text-gray-600 font-bold hover:bg-gray-50">
                        Tutup
                    </button>
                    <button type="submit" class="btn-ipnet-primary px-5 py-2 rounded-xl font-bold shadow-md">
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
