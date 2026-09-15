@extends('layouts.app')

@section('title', 'Mitra Principal & Distributor - PT IP Network Solusindo')

@push('styles')
<style>
    /* ========================================================
       IPNET Brand Design System
       ======================================================== */
    [x-cloak] { display: none !important; }

    :root {
        --ipnet-primary: #8F0A0D;
        --ipnet-primary-hover: #73080A;
        --ipnet-card-bg: #FFFFFF;
        --ipnet-card-border: #E2E8F0;
        --ipnet-text-main: #1E293B;
    }

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

    .btn-ipnet-gradient {
        background: linear-gradient(135deg, #B91C1C 0%, #8F0A0D 60%, #750608 100%);
        color: #FFFFFF;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .btn-ipnet-gradient:hover {
        background: linear-gradient(135deg, #991B1B 0%, #7F080A 60%, #5E0305 100%);
        box-shadow: 0 6px 18px rgba(143, 10, 13, 0.35);
        transform: translateY(-1px);
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
<div class="flex h-screen overflow-hidden bg-[#F8FAFC] font-sans">
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Mitra Principal & Distributor'])
        
        <div class="p-4 sm:p-6 lg:p-7 space-y-5 max-w-[1600px] mx-auto animate-fade-in" x-data="vendorManager()">
            
            <!-- ========================================================== -->
            <!-- SECTION HEADER & FILTER CONTROLS                           -->
            <!-- ========================================================== -->
            <div class="ipnet-card p-5 sm:p-6 anim-fade-up anim-delay-1">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 pb-4 mb-4 border-b border-[#E2E8F0]">
                    <div>
                        <p class="text-[#8F0A0D] text-[12px] font-bold inline-flex items-center uppercase tracking-wider">
                            <span class="ipnet-badge-dot"></span> MITRA &amp; CHANNEL PRINSIPAL
                        </p>
                        <h2 class="text-[20px] font-bold text-[#1E293B] tracking-tight">Katalog Mitra Principal &amp; Distributor</h2>
                        <p class="text-[13px] text-[#64748B] mt-0.5">Direktori kontak prinsipal teknologi, distributor resmi, dan channel manager untuk dukungan perancangan solusi &amp; BoQ</p>
                    </div>

                    <div class="shrink-0">
                        <button @click="openAddModal()" class="btn-ipnet-gradient px-4 py-2.5 rounded-xl font-bold text-[13px] flex items-center gap-2 cursor-pointer shadow-md">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span>Tambah Principal Baru</span>
                        </button>
                    </div>
                </div>

                <!-- Filter Controls -->
                <form method="GET" action="{{ route('vendors.index') }}" class="flex flex-col sm:flex-row flex-wrap items-center justify-between gap-3">
                    <div class="relative flex-1 min-w-[240px] w-full sm:w-auto">
                        <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-[#94A3B8]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}"
                               placeholder="Cari nama principal, distributor, atau kategori..." 
                               class="w-full pl-9 pr-3.5 py-2 rounded-xl border border-[#CBD5E1] text-[12.5px] font-semibold text-[#1E293B] bg-white outline-none hover:border-[#94A3B8] focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition-all shadow-xs placeholder-[#94A3B8]">
                    </div>

                    <div class="text-[12.5px] text-[#64748B] font-medium px-3.5 py-2 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl whitespace-nowrap self-end sm:self-auto">
                        Total: <span class="text-[#1E293B] font-extrabold">{{ $vendors->total() }}</span> Mitra Terdaftar
                    </div>
                </form>
            </div>

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between text-xs font-semibold shadow-xs">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            {{-- Vendor List Table --}}
            <div class="ipnet-card overflow-hidden anim-fade-up anim-delay-2">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="border-b border-[#E2E8F0] bg-[#F8FAFC] text-[10.5px] font-bold tracking-wider text-[#64748B] uppercase">
                                <th class="py-3 px-4 sm:px-5">Nama Principal / Distributor</th>
                                <th class="py-3 px-3.5">Fokus Brand / Kategori</th>
                                <th class="py-3 px-3.5">Departemen</th>
                                <th class="py-3 px-3.5">Channel Manager &amp; Kontak</th>
                                <th class="py-3 px-4 sm:px-5 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#F1F5F9] font-medium text-[#1E293B]">
                            @forelse($vendors as $vendor)
                                <tr class="hover:bg-[#F8FAFC] transition-colors">
                                    {{-- Nama Vendor --}}
                                    <td class="py-3 px-4 sm:px-5">
                                        <div class="font-semibold text-[#1E293B] text-[12.5px] leading-snug">
                                            {{ $vendor->name }}
                                        </div>
                                    </td>

                                    {{-- Brand / Category Focus --}}
                                    <td class="py-3 px-3.5 whitespace-nowrap">
                                        @if($vendor->product_category)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg bg-red-50 text-[#8F0A0D] text-[11px] font-semibold border border-red-100">
                                                {{ $vendor->product_category }}
                                            </span>
                                        @else
                                            <span class="text-[11px] text-[#94A3B8]">—</span>
                                        @endif
                                    </td>

                                    {{-- Departemen --}}
                                    <td class="py-3 px-3.5 text-[#64748B] text-[11.5px] whitespace-nowrap">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-[#F1F5F9] text-[#1E293B] text-[11px] font-medium border border-[#E2E8F0]">
                                            {{ $vendor->department ?: 'Principal Partner' }}
                                        </span>
                                    </td>

                                    {{-- Channel Manager / Kontak --}}
                                    <td class="py-3 px-3.5">
                                        @if($vendor->channel_manager)
                                            <div class="font-semibold text-[#1E293B] text-[12px]">{{ $vendor->channel_manager }}</div>
                                        @endif
                                        <div class="text-[11px] text-[#64748B] flex items-center gap-2 mt-0.5 flex-wrap">
                                            @if($vendor->phone)
                                                <span class="inline-flex items-center gap-1">
                                                    <svg class="w-3 h-3 text-[#8F0A0D]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                                    <span>{{ $vendor->phone }}</span>
                                                </span>
                                            @endif
                                            @if($vendor->email)
                                                <span class="inline-flex items-center gap-1 text-[#64748B]">
                                                    <svg class="w-3 h-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                                    <span>{{ $vendor->email }}</span>
                                                </span>
                                            @endif
                                            @if(!$vendor->channel_manager && !$vendor->phone && !$vendor->email)
                                                <span class="text-[11px] text-[#94A3B8]">Belum ada kontak</span>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- Aksi --}}
                                    <td class="py-3 px-4 sm:px-5 text-right whitespace-nowrap">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <button @click="showDetails({{ $vendor->id }})" 
                                                    class="px-2.5 py-1 bg-white hover:bg-[#F8FAFC] text-[#1E293B] text-[11px] font-semibold rounded-lg border border-[#CBD5E1] shadow-2xs transition">
                                                Detail
                                            </button>
                                            <button @click="confirmDelete({{ $vendor->id }}, '{{ addslashes($vendor->name) }}')"
                                                    class="px-2.5 py-1 bg-white hover:bg-red-50 text-red-600 hover:text-red-700 text-[11px] font-semibold rounded-lg border border-red-200 shadow-2xs transition">
                                                Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-14 text-center text-[#64748B]">
                                        <svg class="w-8 h-8 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                        <p class="font-semibold text-gray-600">Belum ada data principal atau distributor</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($vendors->hasPages())
                    <div class="p-3.5 border-t border-[#E2E8F0] bg-[#F8FAFC]">
                        {{ $vendors->links() }}
                    </div>
                @endif
            </div>

            {{-- Modal Add / Edit Vendor --}}
            <template x-teleport="body">
                <div x-show="isModalOpen" 
                     x-cloak 
                     class="fixed inset-0 z-50 bg-[#0E0D12]/60 flex items-center justify-center p-4">
                    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4" @click.away="isModalOpen = false">
                        <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-[#8F0A0D]">Data Mitra &amp; Principal</span>
                                <h3 class="text-base font-bold text-[#1E293B]" x-text="editMode ? 'Edit Data Mitra' : 'Tambah Mitra Principal Baru'"></h3>
                            </div>
                            <button @click="isModalOpen = false" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <form :action="editMode ? '/vendors/' + form.id : '{{ route('vendors.store') }}'" method="POST" class="space-y-3.5 text-xs">
                            @csrf
                            <template x-if="editMode">
                                <input type="hidden" name="_method" value="PUT">
                            </template>

                            <div>
                                <label class="block font-bold text-gray-700 uppercase mb-1">Nama Principal / Distributor *</label>
                                <input type="text" name="name" x-model="form.name" required placeholder="Contoh: Fortinet Indonesia, Exclusive Networks" 
                                       class="w-full px-3 py-2 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D]">
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block font-bold text-gray-700 uppercase mb-1">Departemen / Divisi</label>
                                    <input type="text" name="department" x-model="form.department" placeholder="Contoh: Enterprise Sales, Networking" 
                                           class="w-full px-3 py-2 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D]">
                                </div>
                                <div>
                                    <label class="block font-bold text-gray-700 uppercase mb-1">Channel Manager / PIC</label>
                                    <input type="text" name="channel_manager" x-model="form.channel_manager" placeholder="Nama kontak PIC" 
                                           class="w-full px-3 py-2 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D]">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block font-bold text-gray-700 uppercase mb-1">Telepon / WhatsApp</label>
                                    <input type="text" name="phone" x-model="form.phone" placeholder="021-xxxx / 0812-xxxx" 
                                           class="w-full px-3 py-2 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D]">
                                </div>
                                <div>
                                    <label class="block font-bold text-gray-700 uppercase mb-1">Email</label>
                                    <input type="email" name="email" x-model="form.email" placeholder="kontak@distributor.com" 
                                           class="w-full px-3 py-2 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D]">
                                </div>
                            </div>

                            <div>
                                <label class="block font-bold text-gray-700 uppercase mb-1">Fokus Brand / Kategori Produk</label>
                                <input type="text" name="product_category" x-model="form.product_category" placeholder="Contoh: Firewall, Switching, Server, SD-WAN" 
                                       class="w-full px-3 py-2 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D]">
                            </div>

                            <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100">
                                <button type="button" @click="isModalOpen = false" class="px-4 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-100 rounded-xl transition">Batal</button>
                                <button type="submit" class="px-4 py-2 text-xs font-bold text-white btn-ipnet-primary rounded-xl shadow-xs transition">
                                    Simpan Mitra
                                </button>
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
                    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4" @click.away="isDetailOpen = false">
                        <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-[#8F0A0D]">Profil Mitra Principal</span>
                                <h3 class="text-base font-bold text-[#1E293B]" x-text="detailVendor.name"></h3>
                            </div>
                            <button @click="isDetailOpen = false" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <div class="space-y-2.5 text-xs">
                            <div class="flex justify-between py-2 border-b border-gray-100">
                                <span class="text-gray-500">Departemen</span>
                                <span class="font-semibold text-gray-800" x-text="detailVendor.department || '—'"></span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-gray-100">
                                <span class="text-gray-500">Channel Manager</span>
                                <span class="font-semibold text-gray-800" x-text="detailVendor.channel_manager || '—'"></span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-gray-100">
                                <span class="text-gray-500">Kategori / Brand Fokus</span>
                                <span class="font-semibold text-[#8F0A0D]" x-text="detailVendor.product_category || '—'"></span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-gray-100">
                                <span class="text-gray-500">Telepon / WhatsApp</span>
                                <span class="font-semibold text-gray-800" x-text="detailVendor.phone || '—'"></span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-gray-100">
                                <span class="text-gray-500">Email</span>
                                <span class="font-semibold text-gray-800" x-text="detailVendor.email || '—'"></span>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100">
                            <button @click="editVendor(detailVendor)" class="px-3.5 py-1.5 text-xs font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition">
                                Edit Data
                            </button>
                            <button @click="isDetailOpen = false" class="px-4 py-1.5 text-xs font-bold text-white bg-[#1E293B] hover:bg-[#0F172A] rounded-xl transition">
                                Tutup
                            </button>
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
                this.form = { id: null, name: '', department: '', channel_manager: '', phone: '', email: '', product_category: '', notes: '' };
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
