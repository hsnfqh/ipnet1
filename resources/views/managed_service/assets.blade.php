@extends('layouts.app')

@section('title', 'Aset & Configuration Items (CI) - Managed Service')

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
    .anim-delay-4 { animation-delay: 0.24s !important; }
</style>
@endpush

@section('content')
<div class="flex h-screen overflow-hidden bg-[#F8FAFC] font-sans" x-data="assetManager()" x-cloak>
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Aset & Configuration Items (CI)'])
        
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
                </div>
            @endif

            <!-- ========================================================== -->
            <!-- SECTION HEADER & FILTER CONTROLS                           -->
            <!-- ========================================================== -->
            <div class="ipnet-card p-5 sm:p-6 anim-fade-up anim-delay-1">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 pb-4 mb-4 border-b border-[#E2E8F0]">
                    <div>
                        <p class="text-[#8F0A0D] text-[12px] font-bold inline-flex items-center uppercase tracking-wider">
                            <span class="w-2 h-2 rounded-full bg-[#8F0A0D] inline-block mr-2"></span> DATA ASET & CMDB
                        </p>
                        <h2 class="text-[20px] font-bold text-[#1E293B] tracking-tight mt-0.5">Aset Configuration Items (CI)</h2>
                        <p class="text-[13px] text-[#64748B] mt-0.5">Inventaris perangkat keras, switch, firewall, dan status operasional perangkat pada site klien</p>
                    </div>

                    <div class="flex flex-wrap items-center gap-3 shrink-0">
                        <div class="px-3.5 py-1.5 rounded-full bg-[#F8FAFC] border border-[#E2E8F0] text-[12px] font-bold text-[#1E293B] flex items-center gap-1.5 shadow-xs">
                            <span class="text-[#64748B]">Total Aset CI:</span>
                            <span class="text-[#8F0A0D] font-extrabold">{{ $assets->count() }}</span>
                        </div>

                        <button @click="openCreateModal()" class="btn-ipnet-gradient px-4 py-2.5 rounded-xl font-bold text-[13px] flex items-center gap-2 cursor-pointer shadow-md">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                            <span>Tambah CI Aset</span>
                        </button>
                    </div>
                </div>

                <!-- Filter Controls -->
                <form method="GET" action="{{ route('ms.assets.index') }}" class="flex flex-col sm:flex-row flex-wrap items-center gap-3">
                    <div class="relative w-full sm:w-72">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#94A3B8]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama perangkat, serial, IP, klien..." 
                               class="w-full pl-10 pr-3 py-2 rounded-xl border border-[#CBD5E1] text-[12.5px] font-medium text-[#1E293B] bg-white outline-none hover:border-[#94A3B8] focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition-all shadow-xs">
                    </div>

                    <select name="category" onchange="this.form.submit()" class="w-full sm:w-44 px-3 py-2 rounded-xl border border-[#CBD5E1] text-[12.5px] font-semibold text-[#1E293B] bg-white outline-none hover:border-[#94A3B8] focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition-all shadow-xs cursor-pointer">
                        <option value="all">Semua Kategori</option>
                        @foreach(['Router', 'Switch', 'Firewall', 'Server', 'Access Point', 'UPS', 'Storage', 'Other'] as $cat)
                            <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>

                    <select name="status" onchange="this.form.submit()" class="w-full sm:w-44 px-3 py-2 rounded-xl border border-[#CBD5E1] text-[12.5px] font-semibold text-[#1E293B] bg-white outline-none hover:border-[#94A3B8] focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition-all shadow-xs cursor-pointer">
                        <option value="all">Semua Status</option>
                        @foreach(['Online', 'Warning', 'Offline', 'Maintenance'] as $st)
                            <option value="{{ $st }}" {{ request('status') == $st ? 'selected' : '' }}>{{ $st }}</option>
                        @endforeach
                    </select>
                </form>
            </div>

            {{-- Asset Table --}}
            <div class="ipnet-card overflow-hidden anim-fade-up anim-delay-2">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-[#E2E8F0] bg-[#F8FAFC] text-[11px] font-bold tracking-wider text-[#64748B] uppercase">
                                <th class="py-3.5 px-5">Perangkat & Klien</th>
                                <th class="py-3.5 px-5">Kategori & Brand</th>
                                <th class="py-3.5 px-5">Serial Number</th>
                                <th class="py-3.5 px-5">IP & Lokasi Rack</th>
                                <th class="py-3.5 px-5">Status</th>
                                <th class="py-3.5 px-5 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#F1F5F9] text-[13px]">
                            @forelse($assets as $asset)
                                <tr class="hover:bg-[#F8FAFC]/80 transition-colors">
                                    <td class="py-3.5 px-5">
                                        <div class="font-semibold text-[#1E293B] text-[13px]">{{ $asset->device_name }}</div>
                                        <div class="text-[12px] text-[#64748B] mt-0.5">{{ $asset->client_name }}</div>
                                    </td>
                                    <td class="py-3.5 px-5 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-medium bg-[#F1F5F9] text-[#475569] border border-[#E2E8F0]">
                                            {{ $asset->category }} &bull; {{ $asset->brand ?: 'Gen' }} {{ $asset->model }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-5 font-mono text-[12px] text-[#64748B] whitespace-nowrap">
                                        {{ $asset->serial_number ?: '—' }}
                                    </td>
                                    <td class="py-3.5 px-5 text-[12px] whitespace-nowrap">
                                        <div class="font-mono text-[#334155] font-semibold">{{ $asset->ip_address ?: '—' }}</div>
                                        <div class="text-[#64748B] text-[11.5px] mt-0.5">{{ $asset->location_site ?: 'Site Default' }} {{ $asset->rack_position ? '• ' . $asset->rack_position : '' }}</div>
                                    </td>
                                    <td class="py-3.5 px-5 whitespace-nowrap text-[12px]">
                                        <x-status-badge :status="$asset->status" />
                                    </td>
                                    <td class="py-3.5 px-5 text-right whitespace-nowrap space-x-1">
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
                                    <td colspan="6" class="py-12 text-center text-[#64748B] text-[13px]">
                                        Belum ada aset Configuration Item (CI) yang terdaftar.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($assets instanceof \Illuminate\Pagination\LengthAwarePaginator && $assets->hasPages())
                    <div class="p-4 border-t border-[#E2E8F0] bg-white">
                        {{ $assets->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>

    {{-- MODAL CREATE / EDIT ASSET --}}
    <template x-teleport="body">
        <div x-show="isModalOpen" 
             x-cloak 
             class="fixed inset-0 z-50 bg-[#0F172A]/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-lg w-full shadow-2xl border border-[#E2E8F0] max-h-[90vh] flex flex-col overflow-hidden" @click.away="isModalOpen = false">
                <!-- Fixed Header -->
                <div class="flex items-center justify-between border-b border-[#E2E8F0] p-5 sm:p-6 pb-4 shrink-0 bg-white">
                    <div>
                        <p class="text-[#8F0A0D] text-[11px] font-bold uppercase tracking-wider">Aset CI</p>
                        <h3 class="text-[16px] font-bold text-[#1E293B]" x-text="isEdit ? 'Perbarui Aset CI' : 'Tambah Aset CI Baru'"></h3>
                    </div>
                    <button @click="isModalOpen = false" class="text-[#94A3B8] hover:text-[#1E293B] p-1.5 rounded-lg hover:bg-[#F1F5F9] transition cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Form with Scrollable Body & Fixed Footer -->
                <form :action="isEdit ? '/managed-service/assets/' + form.id : '{{ route('ms.assets.store') }}'" method="POST" class="flex flex-col flex-1 min-h-0 overflow-hidden">
                    @csrf
                    <template x-if="isEdit">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <div class="p-5 sm:p-6 overflow-y-auto space-y-3.5 text-[12.5px] flex-1">
                        <div>
                            <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">Nama Instansi / Klien <span class="text-[#8F0A0D]">*</span></label>
                            <input type="text" name="client_name" x-model="form.client_name" required placeholder="Contoh: Bank Mandiri, PT Telkom"
                                   class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition">
                        </div>

                        <div>
                            <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">Nama Perangkat & Hostname <span class="text-[#8F0A0D]">*</span></label>
                            <input type="text" name="device_name" x-model="form.device_name" required placeholder="Contoh: Core DC Switch FortiGate 600E"
                                   class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <div>
                                <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">Kategori <span class="text-[#8F0A0D]">*</span></label>
                                <select name="category" x-model="form.category" class="w-full px-3.5 py-2.5 bg-white border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition cursor-pointer">
                                    <option value="Switch">Switch</option>
                                    <option value="Router">Router</option>
                                    <option value="Firewall">Firewall</option>
                                    <option value="Server">Server</option>
                                    <option value="Access Point">Access Point</option>
                                    <option value="UPS">UPS</option>
                                    <option value="Storage">Storage</option>
                                    <option value="Other">Lainnya</option>
                                </select>
                            </div>
                            <div>
                                <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">Status Kesehatan <span class="text-[#8F0A0D]">*</span></label>
                                <select name="status" x-model="form.status" class="w-full px-3.5 py-2.5 bg-white border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition cursor-pointer">
                                    <option value="Online">Online</option>
                                    <option value="Warning">Warning</option>
                                    <option value="Offline">Offline</option>
                                    <option value="Maintenance">Maintenance</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <div>
                                <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">Brand / Merek</label>
                                <input type="text" name="brand" x-model="form.brand" placeholder="Cisco, Fortinet, Mikrotik"
                                       class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition">
                            </div>
                            <div>
                                <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">Model / Tipe</label>
                                <input type="text" name="model" x-model="form.model" placeholder="FG-600E, C9500"
                                       class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <div>
                                <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">Serial Number</label>
                                <input type="text" name="serial_number" x-model="form.serial_number" placeholder="SN-123456"
                                       class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition">
                            </div>
                            <div>
                                <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">IP Address Management</label>
                                <input type="text" name="ip_address" x-model="form.ip_address" placeholder="10.240.1.1"
                                       class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <div>
                                <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">Lokasi Site</label>
                                <input type="text" name="location_site" x-model="form.location_site" placeholder="Data Center Lt. 8"
                                       class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition">
                            </div>
                            <div>
                                <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">Posisi Rack</label>
                                <input type="text" name="rack_position" x-model="form.rack_position" placeholder="Rack DC-04 (U18)"
                                       class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition">
                            </div>
                        </div>
                    </div>

                    <!-- Fixed Footer -->
                    <div class="flex items-center justify-end gap-2.5 p-4 px-6 border-t border-[#E2E8F0] bg-[#F8FAFC] shrink-0">
                        <button type="button" @click="isModalOpen = false" class="px-4 py-2.5 text-[12.5px] font-bold text-[#475569] hover:bg-[#F1F5F9] border border-[#CBD5E1] rounded-xl transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="btn-ipnet-gradient px-5 py-2.5 text-[12.5px] font-bold rounded-xl shadow-md cursor-pointer">
                            Simpan Aset CI
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>

    {{-- MODAL KONFIRMASI HAPUS ASET CI (POPUP) --}}
    <template x-teleport="body">
        <div x-show="isDeleteModalOpen"
             x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-[#0F172A]/60 z-[99999] flex items-center justify-center p-4 backdrop-blur-xs"
             @click.self="isDeleteModalOpen = false"
             @keydown.escape.window="isDeleteModalOpen = false">

            <div class="bg-white rounded-2xl w-[420px] max-w-full p-6 text-left shadow-[0_20px_60px_rgba(15,23,42,0.25)] border border-[#E2E8F0] animate-fade-in-up">
                <div class="w-12 h-12 rounded-full bg-[#FEF2F2] flex items-center justify-center mx-auto mb-4 text-[#8F0A0D]">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>

                <h3 class="text-center font-display text-[16px] font-bold text-[#1E293B] mb-1.5">Yakin Hapus Aset CI?</h3>
                <p class="text-center text-[12.5px] text-[#64748B] mb-6 break-words">
                    Apakah Anda yakin ingin menghapus aset <strong class="text-[#1E293B]" x-text="assetToDelete ? assetToDelete.device_name : ''"></strong> (<span x-text="assetToDelete ? assetToDelete.client_name : ''"></span>)? Tindakan ini tidak dapat dibatalkan.
                </p>

                <form :action="'/managed-service/assets/' + (assetToDelete ? assetToDelete.id : '')" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="flex gap-2.5">
                        <button type="button"
                                @click="isDeleteModalOpen = false"
                                class="flex-1 py-2.5 px-4 rounded-xl bg-white text-[#334155] border border-[#CBD5E1] font-bold text-[12.5px] hover:bg-[#F8FAFC] transition cursor-pointer text-center">
                            Batal
                        </button>
                        <button type="submit"
                                class="flex-1 py-2.5 px-4 rounded-xl btn-ipnet-gradient font-bold text-[12.5px] transition cursor-pointer shadow-md text-white text-center">
                            Ya, Hapus
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>
</div>

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
                device_name: '',
                category: 'Switch',
                status: 'Online',
                brand: '',
                model: '',
                serial_number: '',
                ip_address: '',
                location_site: '',
                rack_position: '',
            },
            openCreateModal() {
                this.isEdit = false;
                this.form = {
                    id: null,
                    client_name: '',
                    device_name: '',
                    category: 'Switch',
                    status: 'Online',
                    brand: '',
                    model: '',
                    serial_number: '',
                    ip_address: '',
                    location_site: '',
                    rack_position: '',
                };
                this.isModalOpen = true;
            },
            openEditModal(asset) {
                this.isEdit = true;
                this.form = { ...asset };
                this.isModalOpen = true;
            },
            confirmDelete(asset) {
                this.assetToDelete = asset;
                this.isDeleteModalOpen = true;
            }
        }
    }
</script>
@endsection
