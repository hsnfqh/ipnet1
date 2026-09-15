@extends('layouts.app')

@section('title', 'Vendor - Sales Portal')

@section('content')
<div class="flex h-screen overflow-hidden">
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Vendor'])
        
        <div class="p-4 sm:p-5 lg:p-[26px] animate-fade-in space-y-6" x-data="vendorManager()">
            
            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between text-sm shadow-sm">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            {{-- Search & Action Bar --}}
            <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
                <form method="GET" action="{{ route('vendors.index') }}" class="w-full sm:max-w-md relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="Search vendor name, department, product category..." 
                           class="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-all">
                </form>

                <div class="flex items-center justify-between sm:justify-end gap-4">
                    <div class="text-xs text-gray-400 font-medium whitespace-nowrap">
                        Total: <span class="text-gray-700 font-bold">{{ $vendors->total() }}</span> Vendor
                    </div>

                    <button @click="openAddModal()" 
                            class="inline-flex items-center gap-1.5 px-4 py-2 bg-[#C81E2C] hover:brightness-105 active:translate-y-[1px] text-white text-[13px] font-semibold rounded-lg shadow-[0_8px_20px_rgba(200,30,44,0.24)] transition-all whitespace-nowrap">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>Tambah Vendor</span>
                    </button>
                </div>
            </div>

            {{-- Vendor List Table --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-gray-100 bg-gray-50/50 text-[11px] font-bold tracking-wider text-gray-500 uppercase">
                                <th class="py-4 px-6">NAMA VENDOR <span class="text-gray-400">↑</span></th>
                                <th class="py-4 px-6">DEPARTEMEN</th>
                                <th class="py-4 px-6">CHANNEL MANAGER / KONTAK</th>
                                <th class="py-4 px-6 text-right">AKSI</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @forelse($vendors as $vendor)
                                <tr class="hover:bg-gray-50/60 transition-colors">
                                    <td class="py-4 px-6 font-bold text-gray-900">
                                        {{ $vendor->name }}
                                    </td>
                                    <td class="py-4 px-6 text-gray-600 font-medium">
                                        {{ $vendor->department ?: 'DEPT01' }}
                                    </td>
                                    <td class="py-4 px-6 text-gray-600">
                                        @if($vendor->channel_manager)
                                            <div class="font-medium text-gray-800">{{ $vendor->channel_manager }}</div>
                                        @endif
                                        <div class="text-xs text-gray-400">{{ $vendor->phone ?: ($vendor->email ?: ($vendor->product_category ?: '—')) }}</div>
                                    </td>
                                    <td class="py-4 px-6 text-right whitespace-nowrap">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <button @click="showDetails({{ $vendor->id }})" 
                                                    class="px-3 py-1 bg-white hover:bg-gray-50 text-gray-700 text-xs font-semibold rounded-lg border border-gray-200 shadow-sm transition-all">
                                                Detail
                                            </button>
                                            <button @click="confirmDelete({{ $vendor->id }}, '{{ addslashes($vendor->name) }}')"
                                                    class="px-3 py-1 bg-white hover:bg-red-50 text-red-600 hover:text-red-700 text-xs font-semibold rounded-lg border border-red-200 shadow-sm transition-all">
                                                Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-12 text-center text-gray-400 text-sm">
                                        Belum ada data vendor atau distributor yang ditemukan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($vendors->hasPages())
                    <div class="p-4 border-t border-gray-100">
                        {{ $vendors->links() }}
                    </div>
                @endif
            </div>

            {{-- Modal Add/Edit Vendor --}}
            {{-- Modal Add / Edit Vendor --}}
            <template x-teleport="body">
                <div x-show="isModalOpen" 
                     x-cloak 
                     class="fixed inset-0 z-50 bg-[#0E0D12]/60 flex items-center justify-center p-4">
                    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-[0_20px_50px_rgba(14,13,18,0.25)] space-y-5" @click.away="isModalOpen = false">
                        <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                            <h3 class="text-lg font-bold text-gray-900" x-text="editMode ? 'Edit Vendor' : 'Add New Vendor'"></h3>
                            <button @click="isModalOpen = false" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <form :action="editMode ? '/vendors/' + form.id : '{{ route('vendors.store') }}'" method="POST" class="space-y-4">
                            @csrf
                            <template x-if="editMode">
                                <input type="hidden" name="_method" value="PUT">
                            </template>

                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Vendor / Distributor Name *</label>
                                <input type="text" name="name" x-model="form.name" required placeholder="Contoh: Exclusive Networks" 
                                       class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Department</label>
                                    <input type="text" name="department" x-model="form.department" placeholder="Contoh: DEPT01, Extreme" 
                                       class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Channel Manager / PIC</label>
                                    <input type="text" name="channel_manager" x-model="form.channel_manager" placeholder="Nama Channel Manager" 
                                           class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Phone / WA</label>
                                    <input type="text" name="phone" x-model="form.phone" placeholder="021-xxxx / 0812-xxxx" 
                                           class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Email</label>
                                    <input type="email" name="email" x-model="form.email" placeholder="sales@distributor.com" 
                                           class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Product Category / Brand Focus</label>
                                <input type="text" name="product_category" x-model="form.product_category" placeholder="Contoh: Fortinet, Palo Alto, Cisco" 
                                       class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                            </div>

                            <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100">
                                <button type="button" @click="isModalOpen = false" class="px-4 py-2 text-sm font-semibold text-gray-600 hover:bg-gray-100 rounded-lg transition">Batal</button>
                                <button type="submit" class="px-5 py-2.5 text-[13px] font-semibold text-white bg-[#C81E2C] hover:brightness-105 active:translate-y-[1px] rounded-lg shadow-[0_8px_20px_rgba(200,30,44,0.24)] transition-all">Simpan Vendor</button>
                            </div>
                        </form>
                    </div>
                </div>
            </template>

            {{-- Modal Details Vendor --}}
            <template x-teleport="body">
                <div x-show="isDetailOpen" 
                     x-cloak 
                     class="fixed inset-0 z-50 bg-[#0E0D12]/60 flex items-center justify-center p-4">
                    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-[0_20px_50px_rgba(14,13,18,0.25)] space-y-4" @click.away="isDetailOpen = false">
                        <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                            <h3 class="text-lg font-bold text-gray-900" x-text="detailVendor.name"></h3>
                            <button @click="isDetailOpen = false" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between py-1.5 border-b border-gray-50">
                                <span class="text-gray-500">Department</span>
                                <span class="font-semibold text-gray-800" x-text="detailVendor.department || 'DEPT01'"></span>
                            </div>
                            <div class="flex justify-between py-1.5 border-b border-gray-50">
                                <span class="text-gray-500">Channel Manager</span>
                                <span class="font-semibold text-gray-800" x-text="detailVendor.channel_manager || '—'"></span>
                            </div>
                            <div class="flex justify-between py-1.5 border-b border-gray-50">
                                <span class="text-gray-500">Product / Brand</span>
                                <span class="font-semibold text-gray-800" x-text="detailVendor.product_category || '—'"></span>
                            </div>
                            <div class="flex justify-between py-1.5 border-b border-gray-50">
                                <span class="text-gray-500">Phone</span>
                                <span class="font-semibold text-gray-800" x-text="detailVendor.phone || '—'"></span>
                            </div>
                            <div class="flex justify-between py-1.5 border-b border-gray-50">
                                <span class="text-gray-500">Email</span>
                                <span class="font-semibold text-gray-800" x-text="detailVendor.email || '—'"></span>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100">
                            <button @click="editVendor(detailVendor)" class="px-4 py-2 text-xs font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition">Edit</button>
                            <button @click="isDetailOpen = false" class="px-4 py-2 text-xs font-semibold text-white bg-gray-800 hover:bg-gray-900 rounded-lg transition">Tutup</button>
                        </div>
                    </div>
                </div>
            </template>

            {{-- Hidden Delete Form --}}
            <form id="delete-vendor-form" method="POST" style="display: none;">
                @csrf
                @method('DELETE')
            </form>

        </div>
    </div>
</div>

<script>
    function vendorManager() {
        return {
            isModalOpen: false,
            isDetailOpen: false,
            editMode: false,
            form: { id: null, name: '', department: '', channel_manager: '', phone: '', email: '', product_category: '', notes: '' },
            detailVendor: {},

            openAddModal() {
                this.editMode = false;
                this.form = { id: null, name: '', department: 'DEPT01', channel_manager: '', phone: '', email: '', product_category: '', notes: '' };
                this.isModalOpen = true;
            },

            async showDetails(id) {
                try {
                    const res = await fetch(`/vendors/${id}`);
                    this.detailVendor = await res.json();
                    this.isDetailOpen = true;
                } catch (e) {
                    console.error(e);
                }
            },

            editVendor(vendor) {
                this.isDetailOpen = false;
                this.editMode = true;
                this.form = { ...vendor };
                this.isModalOpen = true;
            },

            confirmDelete(id, name) {
                if (confirm(`Yakin ingin menghapus vendor "${name}"?`)) {
                    const form = document.getElementById('delete-vendor-form');
                    form.action = `/vendors/${id}`;
                    form.submit();
                }
            }
        }
    }
</script>
@endsection
