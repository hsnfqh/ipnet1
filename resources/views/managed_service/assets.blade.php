@extends('layouts.app')

@section('title', 'Aset & Configuration Items (CI) - Managed Service')

@section('content')
<div class="flex h-screen overflow-hidden" x-data="assetManager()" x-cloak>
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto bg-[#FAF9F8]">
        @include('components.topbar', ['title' => 'Aset & Configuration Items (CI)'])
        
        <div class="p-4 sm:p-5 lg:p-[26px] animate-fade-in space-y-6">
            
            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between text-sm shadow-sm">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            {{-- Top Action Bar & Filter --}}
            <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4">
                <form method="GET" action="{{ route('ms.assets.index') }}" class="flex-1 flex flex-col sm:flex-row items-center gap-3">
                    <div class="relative w-full sm:max-w-xs">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama perangkat, serial, IP, klien..." 
                               class="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition">
                    </div>

                    <select name="category" onchange="this.form.submit()" class="w-full sm:w-auto px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-semibold text-gray-700">
                        <option value="all">Semua Kategori</option>
                        @foreach(['Router', 'Switch', 'Firewall', 'Server', 'Access Point', 'UPS', 'Storage', 'Other'] as $cat)
                            <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>

                    <select name="status" onchange="this.form.submit()" class="w-full sm:w-auto px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-semibold text-gray-700">
                        <option value="all">Semua Status</option>
                        @foreach(['Online', 'Warning', 'Offline', 'Maintenance'] as $st)
                            <option value="{{ $st }}" {{ request('status') == $st ? 'selected' : '' }}>{{ $st }}</option>
                        @endforeach
                    </select>
                </form>

                <div class="flex items-center gap-3">
                    <button @click="openCreateModal()" class="px-4 py-2 bg-[#C81E2C] hover:brightness-105 active:translate-y-[1px] text-white text-xs font-bold rounded-xl shadow-[0_4px_12px_rgba(200,30,44,0.2)] transition inline-flex items-center gap-1.5 whitespace-nowrap">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Tambah CI Aset
                    </button>
                </div>
            </div>

            {{-- Asset Table --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-gray-100 bg-gray-50/50 text-[11px] font-bold tracking-wider text-gray-500 uppercase">
                                <th class="py-4 px-6">PERANGKAT & KLIEN</th>
                                <th class="py-4 px-6">KATEGORI & BRAND</th>
                                <th class="py-4 px-6">SERIAL NUMBER</th>
                                <th class="py-4 px-6">IP & LOKASI RACK</th>
                                <th class="py-4 px-6">STATUS KESEHATAN</th>
                                <th class="py-4 px-6 text-right">AKSI</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @forelse($assets as $asset)
                                <tr class="hover:bg-gray-50/60 transition-colors">
                                    <td class="py-4 px-6">
                                        <div class="font-bold text-gray-900">{{ $asset->device_name }}</div>
                                        <div class="text-xs text-gray-500 font-medium flex items-center gap-1.5 mt-0.5">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[#C81E2C]"></span>
                                            <span>{{ $asset->client_name }}</span>
                                        </div>
                                    </td>
                                    <td class="py-4 px-6 whitespace-nowrap">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-semibold">
                                            {{ $asset->category }} • {{ $asset->brand ?: 'Gen' }} {{ $asset->model }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 font-mono text-xs text-gray-700 whitespace-nowrap">
                                        {{ $asset->serial_number ?: '—' }}
                                    </td>
                                    <td class="py-4 px-6 text-xs whitespace-nowrap">
                                        <div class="font-mono font-bold text-gray-800">{{ $asset->ip_address ?: '—' }}</div>
                                        <div class="text-gray-400 text-[11px]">{{ $asset->location_site ?: 'Site Default' }} {{ $asset->rack_position ? '• ' . $asset->rack_position : '' }}</div>
                                    </td>
                                    <td class="py-4 px-6 whitespace-nowrap text-xs">
                                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-bold
                                            {{ $asset->status === 'Online' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : ($asset->status === 'Warning' ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-rose-50 text-rose-700 border border-rose-200') }}">
                                            {{ $asset->status }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 text-right whitespace-nowrap space-x-1">
                                        <button @click="openEditModal({{ json_encode($asset) }})" class="p-1.5 text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition" title="Edit Aset">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>
                                        <form action="{{ route('ms.assets.destroy', $asset->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus aset CI ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Hapus Aset">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-12 text-center text-gray-400 text-xs">
                                        Belum ada aset Configuration Item (CI) yang terdaftar.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($assets->hasPages())
                    <div class="p-4 border-t border-gray-100">
                        {{ $assets->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>

    {{-- MODAL CREATE / EDIT ASSET --}}
    <template x-teleport="body">
        <div x-show="isModalOpen" class="fixed inset-0 z-50 bg-[#0E0D12]/60 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4" @click.away="isModalOpen = false">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <h3 class="text-base font-bold text-gray-900" x-text="isEdit ? 'Perbarui Aset CI' : 'Tambah Aset CI Baru'"></h3>
                    <button @click="isModalOpen = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form :action="isEdit ? '/managed-service/assets/' + form.id : '{{ route('ms.assets.store') }}'" method="POST" class="space-y-3 text-xs">
                    @csrf
                    <template x-if="isEdit">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <div>
                        <label class="block font-bold text-gray-700 uppercase mb-1">Nama Instansi / Klien *</label>
                        <input type="text" name="client_name" x-model="form.client_name" required placeholder="Contoh: Bank Mandiri, PT Telkom"
                               class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-red-500/20">
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 uppercase mb-1">Nama Perangkat & Hostname *</label>
                        <input type="text" name="device_name" x-model="form.device_name" required placeholder="Contoh: Core DC Switch FortiGate 600E"
                               class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-red-500/20">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-gray-700 uppercase mb-1">Kategori *</label>
                            <select name="category" x-model="form.category" class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs">
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
                            <label class="block font-bold text-gray-700 uppercase mb-1">Status Kesehatan *</label>
                            <select name="status" x-model="form.status" class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs">
                                <option value="Online">Online</option>
                                <option value="Warning">Warning</option>
                                <option value="Offline">Offline</option>
                                <option value="Maintenance">Maintenance</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-gray-700 uppercase mb-1">Brand / Merek</label>
                            <input type="text" name="brand" x-model="form.brand" placeholder="Cisco, Fortinet, Mikrotik"
                                   class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs">
                        </div>
                        <div>
                            <label class="block font-bold text-gray-700 uppercase mb-1">Model / Tipe</label>
                            <input type="text" name="model" x-model="form.model" placeholder="FG-600E, C9500"
                                   class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-gray-700 uppercase mb-1">Serial Number</label>
                            <input type="text" name="serial_number" x-model="form.serial_number" placeholder="SN-123456"
                                   class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs">
                        </div>
                        <div>
                            <label class="block font-bold text-gray-700 uppercase mb-1">IP Address Management</label>
                            <input type="text" name="ip_address" x-model="form.ip_address" placeholder="10.240.1.1"
                                   class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-gray-700 uppercase mb-1">Lokasi Site</label>
                            <input type="text" name="location_site" x-model="form.location_site" placeholder="Data Center Lt. 8"
                                   class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs">
                        </div>
                        <div>
                            <label class="block font-bold text-gray-700 uppercase mb-1">Posisi Rack</label>
                            <input type="text" name="rack_position" x-model="form.rack_position" placeholder="Rack DC-04 (U18)"
                                   class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs">
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100">
                        <button type="button" @click="isModalOpen = false" class="px-4 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-100 rounded-xl transition">Batal</button>
                        <button type="submit" class="px-5 py-2 text-xs font-bold text-white bg-[#C81E2C] hover:brightness-105 rounded-xl shadow-sm transition">
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
