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

            {{-- Top Action Bar & Filter --}}
            <div class="ipnet-card p-4 sm:p-5 flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4">
                <form method="GET" action="{{ route('ms.assets.index') }}" class="flex-1 flex flex-col sm:flex-row items-center gap-3">
                    <div class="relative w-full sm:max-w-xs">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#94A3B8]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama perangkat, serial, IP, klien..." 
                               class="w-full pl-10 pr-4 py-2 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition">
                    </div>

                    <select name="category" onchange="this.form.submit()" class="w-full sm:w-auto px-3.5 py-2 bg-white border border-[#CBD5E1] rounded-xl text-[12.5px] font-bold text-[#334155] cursor-pointer">
                        <option value="all">Semua Kategori</option>
                        @foreach(['Router', 'Switch', 'Firewall', 'Server', 'Access Point', 'UPS', 'Storage', 'Other'] as $cat)
                            <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>

                    <select name="status" onchange="this.form.submit()" class="w-full sm:w-auto px-3.5 py-2 bg-white border border-[#CBD5E1] rounded-xl text-[12.5px] font-bold text-[#334155] cursor-pointer">
                        <option value="all">Semua Status</option>
                        @foreach(['Online', 'Warning', 'Offline', 'Maintenance'] as $st)
                            <option value="{{ $st }}" {{ request('status') == $st ? 'selected' : '' }}>{{ $st }}</option>
                        @endforeach
                    </select>
                </form>

                <div class="flex items-center gap-3">
                    <button @click="openCreateModal()" class="btn-ipnet-gradient px-4 py-2.5 rounded-xl font-bold text-[12.5px] flex items-center gap-2 cursor-pointer shadow-md">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        <span>Tambah CI Aset</span>
                    </button>
                </div>
            </div>

            {{-- Asset Table --}}
            <div class="ipnet-card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-[#E2E8F0] bg-[#F8FAFC] text-[11px] font-extrabold tracking-wider text-[#64748B] uppercase">
                                <th class="py-3.5 px-5">PERANGKAT & KLIEN</th>
                                <th class="py-3.5 px-5">KATEGORI & BRAND</th>
                                <th class="py-3.5 px-5">SERIAL NUMBER</th>
                                <th class="py-3.5 px-5">IP & LOKASI RACK</th>
                                <th class="py-3.5 px-5">STATUS KESEHATAN</th>
                                <th class="py-3.5 px-5 text-right">AKSI</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#F1F5F9] text-[13px]">
                            @forelse($assets as $asset)
                                <tr class="hover:bg-[#F8FAFC] transition-colors">
                                    <td class="py-4 px-5">
                                        <div class="font-bold text-[#1E293B]">{{ $asset->device_name }}</div>
                                        <div class="text-[12px] text-[#64748B] font-semibold flex items-center gap-1.5 mt-0.5">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[#8F0A0D]"></span>
                                            <span>{{ $asset->client_name }}</span>
                                        </div>
                                    </td>
                                    <td class="py-4 px-5 whitespace-nowrap">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-blue-50 text-blue-700 text-[11.5px] font-bold border border-blue-200">
                                            {{ $asset->category }} &bull; {{ $asset->brand ?: 'Gen' }} {{ $asset->model }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-5 font-mono text-[12px] text-[#475569] font-semibold whitespace-nowrap">
                                        {{ $asset->serial_number ?: '—' }}
                                    </td>
                                    <td class="py-4 px-5 text-[12px] whitespace-nowrap">
                                        <div class="font-mono font-bold text-[#1E293B]">{{ $asset->ip_address ?: '—' }}</div>
                                        <div class="text-[#64748B] text-[11px]">{{ $asset->location_site ?: 'Site Default' }} {{ $asset->rack_position ? '• ' . $asset->rack_position : '' }}</div>
                                    </td>
                                    <td class="py-4 px-5 whitespace-nowrap text-[12px]">
                                        <span class="inline-flex px-2.5 py-1 rounded-full text-[11px] font-bold
                                            {{ $asset->status === 'Online' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : ($asset->status === 'Warning' ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-rose-50 text-rose-700 border border-rose-200') }}">
                                            {{ $asset->status }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-5 text-right whitespace-nowrap space-x-1">
                                        <button @click="openEditModal({{ json_encode($asset) }})" class="p-1.5 text-[#64748B] hover:text-[#1E293B] hover:bg-[#F1F5F9] rounded-lg transition cursor-pointer" title="Edit Aset">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>
                                        <form action="{{ route('ms.assets.destroy', $asset->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus aset CI ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-[#8F0A0D] hover:bg-[#FEF2F2] rounded-lg transition cursor-pointer" title="Hapus Aset">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
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
            <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-[#E2E8F0] space-y-4 max-h-[90vh] overflow-y-auto" @click.away="isModalOpen = false">
                <div class="flex items-center justify-between border-b border-[#E2E8F0] pb-3.5">
                    <div>
                        <p class="text-[#8F0A0D] text-[11px] font-bold uppercase tracking-wider">Aset CI</p>
                        <h3 class="text-[16px] font-bold text-[#1E293B]" x-text="isEdit ? 'Perbarui Aset CI' : 'Tambah Aset CI Baru'"></h3>
                    </div>
                    <button @click="isModalOpen = false" class="text-[#94A3B8] hover:text-[#1E293B] p-1 rounded-lg hover:bg-[#F1F5F9] transition cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form :action="isEdit ? '/managed-service/assets/' + form.id : '{{ route('ms.assets.store') }}'" method="POST" class="space-y-3.5 text-[12.5px]">
                    @csrf
                    <template x-if="isEdit">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

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

                    <div class="flex items-center justify-end gap-2.5 pt-3.5 border-t border-[#E2E8F0]">
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
</div>

<script>
    function assetManager() {
        return {
            isModalOpen: false,
            isEdit: false,
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
            }
        }
    }
</script>
@endsection
