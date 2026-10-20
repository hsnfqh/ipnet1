@extends('layouts.app')

@section('title', 'Data Aset & Perangkat - Managed Service')

@push('styles')
<style>
    :root {
        --ipnet-primary: #8F0A0D;
        --ipnet-primary-hover: #73080A;
    }
    .ipnet-card {
        background-color: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04), 0 4px 12px rgba(0, 0, 0, 0.02);
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .btn-ipnet-gradient {
        background: linear-gradient(135deg, #8F0A0D 0%, #B81525 100%);
        color: #FFFFFF;
        transition: all 0.2s ease;
    }
    .btn-ipnet-gradient:hover {
        background: linear-gradient(135deg, #7A080B 0%, #9E0E1D 100%);
        box-shadow: 0 6px 16px rgba(143, 10, 13, 0.25);
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
    .anim-delay-3 { animation-delay: 0.18s !important; }
</style>
@endpush

@section('content')
<div class="flex h-screen overflow-hidden bg-[#F8FAFC] font-sans" x-data="assetManager()" x-cloak>
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Asset Management'])
        
        <div class="p-4 sm:p-6 lg:p-7 space-y-6 max-w-[1600px] mx-auto animate-fade-in">
            
            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between text-[13px] font-semibold shadow-xs">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-600 flex-shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 font-bold px-2">✕</button>
                </div>
            @endif

            @if(session('info'))
                <div class="p-4 rounded-xl bg-blue-50 border border-blue-200 text-blue-800 flex items-center justify-between text-[13px] font-semibold shadow-xs">
                    <div class="flex items-center gap-2.5">
                        <span>{{ session('info') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-blue-500 hover:text-blue-700 font-bold px-2">✕</button>
                </div>
            @endif

            <!-- SECTION HEADER & FILTER CONTROLS -->
            <div class="ipnet-card p-5 sm:p-6 anim-fade-up anim-delay-1">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 pb-4 mb-4 border-b border-[#E2E8F0]">
                    <div>
                        <p class="text-[#8F0A0D] text-[12px] font-bold inline-flex items-center uppercase tracking-wider">
                            <span class="w-2 h-2 rounded-full bg-[#8F0A0D] inline-block mr-2"></span> MANAJEMEN ASET
                        </p>
                        <h2 class="text-[20px] font-bold text-[#1E293B] tracking-tight mt-0.5">Daftar Aset Eksternal (Perangkat Klien)</h2>
                        <p class="text-[13px] text-[#64748B] mt-0.5">Inventaris perangkat keras, spesifikasi teknis, nomor seri, dan kepemilikan aset klien</p>
                    </div>

                    <div class="flex flex-wrap items-center gap-3 shrink-0">
                        {{-- Toggle Button Internal vs External (Like Reference Image) --}}
                        <div class="inline-flex p-1 bg-[#F1F5F9] rounded-xl border border-[#E2E8F0] shadow-2xs">
                            <a href="{{ route('ms.assets.internal') }}" 
                               class="px-4 py-2 rounded-lg text-xs font-semibold transition-all flex items-center gap-1.5 text-[#64748B] hover:text-[#1E293B] hover:bg-white/60">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                <span>Internal Asset</span>
                            </a>
                            <a href="{{ route('ms.assets.external') }}" 
                               class="px-4 py-2 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 bg-[#8F0A0D] text-white shadow-xs">
                                <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/></svg>
                                <span>External Asset</span>
                            </a>
                        </div>

                        <div class="px-3.5 py-2 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0] text-[12px] font-bold text-[#1E293B] flex items-center gap-1.5 shadow-xs">
                            <span class="text-[#64748B]">Total:</span>
                            <span class="text-[#8F0A0D] font-extrabold">{{ $assets->total() }}</span>
                        </div>

                        <button @click="openCreateModal()" class="btn-ipnet-gradient px-4 py-2 rounded-xl font-bold text-[12.5px] flex items-center gap-2 cursor-pointer shadow-md">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                            <span>Tambah Aset</span>
                        </button>
                    </div>
                </div>

                <!-- Filter Controls with Per Page Selector (5, 10, 25, 50) -->
                <form method="GET" action="{{ route('ms.assets.external') }}" class="flex flex-col md:flex-row flex-wrap items-center justify-between gap-3">
                    <div class="flex flex-col sm:flex-row flex-wrap items-center gap-2.5 w-full md:w-auto flex-1">
                        <div class="relative w-full sm:w-64">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#94A3B8]">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, serial, deskripsi..." 
                                   class="w-full pl-10 pr-3 py-2 rounded-xl border border-[#CBD5E1] text-[12.5px] font-medium text-[#1E293B] bg-white outline-none hover:border-[#94A3B8] focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition-all shadow-xs">
                        </div>

                        <select name="category" onchange="this.form.submit()" class="w-full sm:w-44 px-3 py-2 rounded-xl border border-[#CBD5E1] text-[12.5px] font-semibold text-[#1E293B] bg-white outline-none hover:border-[#94A3B8] focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition-all shadow-xs cursor-pointer">
                            <option value="all">Semua Tipe</option>
                            @foreach(['Router', 'Switch', 'Firewall', 'Server', 'Access Point', 'UPS', 'Storage', 'Testing Tool', 'Fusion Splicer', 'Toolkit', 'Laptop / Device', 'Other'] as $cat)
                                <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                            @endforeach
                        </select>

                        <select name="owner" onchange="this.form.submit()" class="w-full sm:w-48 px-3 py-2 rounded-xl border border-[#CBD5E1] text-[12.5px] font-semibold text-[#1E293B] bg-white outline-none hover:border-[#94A3B8] focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition-all shadow-xs cursor-pointer">
                            <option value="">Semua Pemilik (Klien)</option>
                            @foreach($clients as $c)
                                <option value="{{ $c->name }}" {{ request('owner') == $c->name ? 'selected' : '' }}>{{ $c->name }}</option>
                            @endforeach
                        </select>

                        @if(request('search') || (request('category') && request('category') !== 'all') || request('owner') || (request('per_page') && request('per_page') != 10))
                            <a href="{{ route('ms.assets.external') }}" class="px-3 py-2 text-xs font-semibold text-slate-500 hover:text-slate-800 self-center">
                                Reset Filter
                            </a>
                        @endif
                    </div>

                    {{-- Option 5, 10, 25, 50 per page --}}
                    <div class="flex items-center gap-2 self-end md:self-center shrink-0">
                        <span class="text-[12px] font-semibold text-[#64748B]">Tampilkan:</span>
                        <select name="per_page" onchange="this.form.submit()" class="px-3 py-2 rounded-xl border border-[#CBD5E1] text-[12px] font-bold text-[#1E293B] bg-white outline-none hover:border-[#94A3B8] focus:border-[#8F0A0D] cursor-pointer shadow-xs">
                            <option value="5" {{ request('per_page', $perPage ?? 10) == 5 ? 'selected' : '' }}>5 baris</option>
                            <option value="10" {{ request('per_page', $perPage ?? 10) == 10 ? 'selected' : '' }}>10 baris</option>
                            <option value="25" {{ request('per_page', $perPage ?? 10) == 25 ? 'selected' : '' }}>25 baris</option>
                            <option value="50" {{ request('per_page', $perPage ?? 10) == 50 ? 'selected' : '' }}>50 baris</option>
                        </select>
                    </div>
                </form>
            </div>

            {{-- Asset Table: Tipe Perangkat, Product Name, Product Description, Quantity, Serial Number, Keterangan, Pemilik --}}
            <div class="ipnet-card overflow-hidden anim-fade-up anim-delay-2">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-[#E2E8F0] bg-[#F8FAFC] text-[11px] font-bold tracking-wider text-[#64748B] uppercase">
                                <th class="py-3.5 px-4">Tipe Perangkat</th>
                                <th class="py-3.5 px-4">Product Name</th>
                                <th class="py-3.5 px-4">Product Description</th>
                                <th class="py-3.5 px-3 text-center">Quantity</th>
                                <th class="py-3.5 px-4">Serial Number</th>
                                <th class="py-3.5 px-4">Keterangan</th>
                                <th class="py-3.5 px-4">Pemilik</th>
                                <th class="py-3.5 px-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#F1F5F9] text-[12.5px]">
                            @forelse($assets as $asset)
                                @php
                                    $isInternal = ($asset->owner_type === 'internal') || str_contains(strtolower($asset->client_name), 'ipnet') || str_contains(strtolower($asset->client_name), 'internal');
                                @endphp
                                <tr class="hover:bg-[#F8FAFC]/80 transition-colors">
                                    {{-- 1. Tipe Perangkat --}}
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[11px] font-bold bg-[#F1F5F9] text-[#475569] border border-[#E2E8F0]">
                                            {{ $asset->category ?: 'Perangkat' }}
                                        </span>
                                    </td>

                                    {{-- 2. Product Name --}}
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-[#1E293B] text-[13px]">{{ $asset->device_name }}</div>
                                        @if($asset->brand || $asset->model)
                                            <div class="text-[11px] text-[#64748B] font-medium">{{ $asset->brand }} {{ $asset->model }}</div>
                                        @endif
                                    </td>

                                    {{-- 3. Product Description --}}
                                    <td class="py-3.5 px-4 max-w-xs">
                                        <div class="text-[#334155] text-[12px] line-clamp-2" title="{{ $asset->product_description ?: ($asset->brand . ' ' . $asset->model) }}">
                                            {{ $asset->product_description ?: ($asset->brand ? $asset->brand . ' ' . $asset->model : '—') }}
                                        </div>
                                        @if($asset->ip_address)
                                            <div class="font-mono text-[11px] text-[#8F0A0D] font-semibold mt-0.5">IP: {{ $asset->ip_address }}</div>
                                        @endif
                                    </td>

                                    {{-- 4. Quantity --}}
                                    <td class="py-3.5 px-3 text-center whitespace-nowrap">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11.5px] font-extrabold bg-slate-100 text-slate-800 border border-slate-200">
                                            {{ $asset->quantity ?: 1 }} Unit
                                        </span>
                                    </td>

                                    {{-- 5. Serial Number --}}
                                    <td class="py-3.5 px-4 font-mono text-[11.5px] text-[#475569] font-medium whitespace-nowrap">
                                        {{ $asset->serial_number ?: '—' }}
                                    </td>

                                    {{-- 6. Keterangan --}}
                                    <td class="py-3.5 px-4 max-w-xs">
                                        <div class="text-[#475569] text-[11.5px]">
                                            {{ $asset->notes ?: ($asset->location_site ? 'Lokasi: ' . $asset->location_site : '—') }}
                                        </div>
                                        <div class="mt-1">
                                            <x-status-badge :status="$asset->status" />
                                        </div>
                                    </td>

                                    {{-- 7. Pemilik --}}
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        @if($isInternal)
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-bold bg-rose-50 text-[#8F0A0D] border border-rose-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-[#8F0A0D]"></span>
                                                PT IPNET (Internal)
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-bold bg-blue-50 text-blue-800 border border-blue-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                                {{ $asset->client_name }}
                                            </span>
                                        @endif
                                    </td>

                                    {{-- 8. Aksi --}}
                                    <td class="py-3.5 px-4 text-right whitespace-nowrap space-x-1">
                                        <button @click="openEditModal({{ json_encode($asset) }})" class="p-1.5 text-[#64748B] hover:text-[#1E293B] hover:bg-[#F1F5F9] rounded-lg transition cursor-pointer" title="Edit Aset">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>
                                        <button type="button" 
                                                @click="confirmDelete({{ json_encode($asset) }})"
                                                class="p-1.5 text-[#8F0A0D] hover:bg-[#FEF2F2] rounded-lg transition cursor-pointer" 
                                                title="Hapus Aset">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="py-12 text-center text-[#64748B] text-[13px]">
                                        Belum ada data aset perangkat yang sesuai dengan filter.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($assets instanceof \Illuminate\Pagination\LengthAwarePaginator && $assets->hasPages())
                    <div class="p-4 border-t border-[#E2E8F0] bg-white flex flex-col sm:flex-row items-center justify-between gap-3">
                        <div class="text-xs text-slate-500">
                            Menampilkan {{ $assets->firstItem() ?? 0 }} - {{ $assets->lastItem() ?? 0 }} dari total {{ $assets->total() }} data
                        </div>
                        <div>
                            {{ $assets->links() }}
                        </div>
                    </div>
                @endif
            </div>

        </div>
    </div>

    {{-- MODAL TAMBAH / EDIT EXTERNAL ASSET --}}
    <template x-teleport="body">
        <div x-show="isModalOpen" 
             x-cloak 
             class="fixed inset-0 z-50 bg-[#0F172A]/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-xl w-full shadow-2xl border border-slate-200 max-h-[90vh] flex flex-col overflow-hidden" @click.away="isModalOpen = false">
                <!-- Header -->
                <div class="flex items-center justify-between border-b border-slate-200 p-5 sm:p-6 pb-4 shrink-0 bg-white">
                    <div>
                        <p class="text-[#8F0A0D] text-[11px] font-bold uppercase tracking-wider">External Asset</p>
                        <h3 class="text-[17px] font-bold text-slate-900" x-text="isEdit ? 'Perbarui Aset Eksternal' : 'Tambah Aset Eksternal Baru'"></h3>
                        <p class="text-xs text-slate-500 mt-0.5">Kelola data perangkat dan aset operasional milik klien.</p>
                    </div>
                    <button @click="isModalOpen = false" class="text-slate-400 hover:text-slate-700 p-1.5 rounded-lg hover:bg-slate-100 transition cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Form (7 Kolom Presisi: Tipe Perangkat, Product Name, Product Description, Quantity, Serial Number, Keterangan, Pemilik) -->
                <form :action="isEdit ? '/managed-service/assets/' + form.id : '{{ route('ms.assets.store') }}'" method="POST" class="flex flex-col flex-1 min-h-0 overflow-hidden">
                    @csrf
                    <template x-if="isEdit">
                        <input type="hidden" name="_method" value="PUT">
                    </template>
                    <input type="hidden" name="owner_type" value="client">

                    <div class="p-5 sm:p-6 overflow-y-auto space-y-4 text-[13px] flex-1">
                        
                        {{-- 1. Tipe Perangkat & 2. Product Name --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                    Tipe Perangkat <span class="text-[#8F0A0D]">*</span>
                                </label>
                                <select name="category" x-model="form.category" required 
                                        class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-sm font-medium text-slate-800 focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition shadow-2xs cursor-pointer">
                                    <option value="Router">Router</option>
                                    <option value="Switch">Switch</option>
                                    <option value="Firewall">Firewall</option>
                                    <option value="Server">Server</option>
                                    <option value="Access Point">Access Point</option>
                                    <option value="UPS">UPS</option>
                                    <option value="Storage">Storage</option>
                                    <option value="Testing Tool">Testing Tool (OTDR / OPM)</option>
                                    <option value="Fusion Splicer">Fusion Splicer</option>
                                    <option value="Toolkit">Toolkit Engineer</option>
                                    <option value="Laptop / Device">Laptop / Device</option>
                                    <option value="Other">Lainnya</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                    Product Name <span class="text-[#8F0A0D]">*</span>
                                </label>
                                <input type="text" name="device_name" x-model="form.device_name" required placeholder="Contoh: Cisco Catalyst 9200L"
                                       class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition shadow-2xs">
                            </div>
                        </div>

                        {{-- 3. Product Description --}}
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Product Description
                            </label>
                            <textarea name="product_description" x-model="form.product_description" rows="2" placeholder="Spesifikasi teknis, 24-Port Gigabit PoE+, Layer 3 Switch..."
                                      class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition shadow-2xs"></textarea>
                        </div>

                        {{-- 4. Quantity & 5. Serial Number --}}
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                            <div class="sm:col-span-1">
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                    Quantity <span class="text-[#8F0A0D]">*</span>
                                </label>
                                <input type="number" name="quantity" x-model="form.quantity" min="1" required placeholder="1"
                                       class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-sm font-bold text-slate-800 placeholder:text-slate-400 focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition shadow-2xs">
                            </div>

                            <div class="sm:col-span-2">
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                    Serial Number
                                </label>
                                <input type="text" name="serial_number" x-model="form.serial_number" placeholder="Contoh: FOC223419LK"
                                       class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-sm font-mono text-slate-800 placeholder:text-slate-400 focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition shadow-2xs">
                            </div>
                        </div>

                        {{-- 6. Keterangan --}}
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Keterangan
                            </label>
                            <textarea name="notes" x-model="form.notes" rows="2" placeholder="Catatan kondisi perangkat, lokasi rak DC-01, atau informasi garansi..."
                                      class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition shadow-2xs"></textarea>
                        </div>

                        {{-- 7. Pemilik (Nama Klien Eksternal) --}}
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Pemilik <span class="text-[#8F0A0D]">*</span>
                            </label>
                            <input type="text" name="client_name" x-model="form.client_name" required placeholder="Nama Klien atau Perusahaan Pemilik (contoh: Bank Raya, Telkom)..."
                                   class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition shadow-2xs">
                        </div>

                    </div>

                    <!-- Footer Action -->
                    <div class="border-t border-slate-200 p-4 sm:p-5 flex items-center justify-end gap-3 bg-slate-50 shrink-0">
                        <button type="button" @click="isModalOpen = false" class="px-4 py-2.5 rounded-xl border border-slate-300 text-slate-700 hover:text-slate-900 font-semibold text-sm hover:bg-white transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#8F0A0D] hover:bg-[#73080A] text-white font-semibold text-sm transition shadow-sm cursor-pointer">
                            <span x-text="isEdit ? 'Simpan Perubahan' : 'Daftarkan Aset'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>

    {{-- DELETE CONFIRMATION MODAL --}}
    <template x-teleport="body">
        <div x-show="isDeleteModalOpen" 
             x-cloak 
             class="fixed inset-0 z-50 bg-[#0F172A]/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-2xl border border-slate-200 text-center" @click.away="isDeleteModalOpen = false">
                <div class="w-12 h-12 rounded-full bg-rose-50 text-[#8F0A0D] flex items-center justify-center mx-auto mb-4 border border-rose-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <h3 class="text-[16px] font-bold text-slate-900">Hapus Data Aset?</h3>
                <p class="text-[12.5px] text-slate-600 mt-1.5 leading-relaxed">
                    Data perangkat <strong class="text-slate-900" x-text="assetToDelete?.device_name"></strong> akan dihapus permanen.
                </p>
                <div class="mt-6 flex items-center justify-center gap-3">
                    <button type="button" @click="isDeleteModalOpen = false" class="px-4 py-2 rounded-xl border border-slate-300 text-slate-700 font-semibold text-[12.5px] hover:bg-slate-50 transition cursor-pointer">
                        Batal
                    </button>
                    <form :action="'/managed-service/assets/' + assetToDelete?.id" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 rounded-xl bg-[#8F0A0D] hover:bg-[#73080A] text-white font-semibold text-[12.5px] transition cursor-pointer shadow-sm">
                            Ya, Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </template>
</div>
@endsection

@push('scripts')
<script>
    function assetManager() {
        return {
            isModalOpen: false,
            isDeleteModalOpen: false,
            isEdit: false,
            assetToDelete: null,
            form: {
                id: null,
                client_name: '',
                owner_type_choice: 'client',
                device_name: '',
                product_description: '',
                category: 'Switch',
                quantity: 1,
                serial_number: '',
                notes: '',
                project_id: null,
            },
            openCreateModal() {
                this.isEdit = false;
                this.form = {
                    id: null,
                    client_name: '',
                    owner_type_choice: 'client',
                    device_name: '',
                    product_description: '',
                    category: 'Switch',
                    quantity: 1,
                    serial_number: '',
                    notes: '',
                    project_id: null,
                };
                this.isModalOpen = true;
            },
            openEditModal(asset) {
                this.isEdit = true;
                const isInternal = (asset.owner_type === 'internal') || 
                                   (asset.client_name && (asset.client_name.toLowerCase().includes('ipnet') || asset.client_name.toLowerCase().includes('internal')));
                this.form = {
                    id: asset.id,
                    client_name: asset.client_name || '',
                    owner_type_choice: isInternal ? 'internal' : 'client',
                    device_name: asset.device_name || '',
                    product_description: asset.product_description || (asset.brand ? (asset.brand + ' ' + (asset.model || '')) : ''),
                    category: asset.category || 'Switch',
                    quantity: asset.quantity || 1,
                    serial_number: asset.serial_number || '',
                    notes: asset.notes || '',
                    project_id: asset.project_id || null,
                };
                this.isModalOpen = true;
            },
            confirmDelete(asset) {
                this.assetToDelete = asset;
                this.isDeleteModalOpen = true;
            }
        };
    }
</script>
@endpush
