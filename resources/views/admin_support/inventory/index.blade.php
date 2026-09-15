@extends('layouts.app')

@section('title', 'Serial Number & Inventory Tracking - Admin Support')

@section('content')
<div class="flex h-screen overflow-hidden" x-data="{ isCreateModalOpen: false }" x-cloak>
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto bg-[#FAF9F8]">
        @include('components.topbar', ['title' => 'Master Serial Number & Inventory Registry'])
        
        <div class="p-4 sm:p-5 lg:p-[26px] animate-fade-in space-y-5 max-w-[1600px] mx-auto">
            
            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between shadow-sm">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 font-bold px-2">✕</button>
                </div>
            @endif

            {{-- Filter & Action Bar --}}
            <div class="flex flex-col sm:flex-row sm:flex-wrap sm:justify-between sm:items-center gap-2.5">
                <form method="GET" action="{{ route('admin_support.inventory.index') }}" class="flex flex-col sm:flex-row flex-wrap gap-2.5 w-full sm:w-auto">
                    <div class="relative">
                        <svg class="w-3.5 h-3.5 absolute left-3 top-3 text-[#948F99]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}"
                               placeholder="Cari Serial Number / Brand / Model..." 
                               class="w-full sm:w-64 px-[11px] py-[9px] pl-8 rounded-lg border border-[#E7E5E3] text-[13px] text-[#17151C] outline-none bg-white focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all">
                    </div>

                    <select name="category" onchange="this.form.submit()" 
                            class="w-full sm:w-44 px-[11px] py-[9px] rounded-lg border border-[#E7E5E3] text-[13px] text-[#17151C] outline-none bg-white focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all cursor-pointer">
                        <option value="">Semua Kategori</option>
                        <option value="Router" {{ request('category') == 'Router' ? 'selected' : '' }}>Router</option>
                        <option value="Switch" {{ request('category') == 'Switch' ? 'selected' : '' }}>Switch</option>
                        <option value="Firewall" {{ request('category') == 'Firewall' ? 'selected' : '' }}>Firewall / Security</option>
                        <option value="Server" {{ request('category') == 'Server' ? 'selected' : '' }}>Server & Storage</option>
                        <option value="SFP" {{ request('category') == 'SFP' ? 'selected' : '' }}>SFP & Transceiver</option>
                    </select>

                    <select name="status" onchange="this.form.submit()" 
                            class="w-full sm:w-48 px-[11px] py-[9px] rounded-lg border border-[#E7E5E3] text-[13px] text-[#17151C] outline-none bg-white focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all cursor-pointer">
                        <option value="">Semua Status Siklus</option>
                        <option value="In Warehouse" {{ request('status') == 'In Warehouse' ? 'selected' : '' }}>In Warehouse (Gudang HQ)</option>
                        <option value="Allocated to Project" {{ request('status') == 'Allocated to Project' ? 'selected' : '' }}>Allocated to Project</option>
                        <option value="Dispatched / In Transit" {{ request('status') == 'Dispatched / In Transit' ? 'selected' : '' }}>Dispatched (Dikirim)</option>
                        <option value="Installed at Site" {{ request('status') == 'Installed at Site' ? 'selected' : '' }}>Installed at Site</option>
                        <option value="RMA / Maintenance" {{ request('status') == 'RMA / Maintenance' ? 'selected' : '' }}>RMA / Maintenance</option>
                    </select>

                    @if(request('search') || request('category') || request('status'))
                        <a href="{{ route('admin_support.inventory.index') }}" 
                           class="px-3 py-2 text-[13px] font-semibold text-gray-500 hover:text-gray-700 self-center">
                            Reset
                        </a>
                    @endif
                </form>

                <button type="button" @click="isCreateModalOpen = true" 
                        class="w-full sm:w-auto justify-center bg-[#C81E2C] text-white shadow-[0_8px_20px_rgba(200,30,44,0.24)] px-[17px] py-[9px] rounded-lg font-semibold text-[13px] flex items-center gap-1.5 hover:brightness-105 active:translate-y-[1px] transition-all cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Daftarkan Serial Number</span>
                </button>
            </div>

            {{-- Table Serial Numbers --}}
            <div class="bg-white rounded-xl border border-[#E7E5E3] shadow-[0_1px_2px_rgba(14,13,18,0.05)] overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[1100px] border-collapse text-[13px]">
                        <thead>
                            <tr class="bg-[#F1F0EE] border-b border-[#E7E5E3]">
                                <th class="text-left py-3.5 px-4 text-[11px] font-semibold text-[#75727C] uppercase tracking-[0.3px] w-[20%]">Serial Number (SN)</th>
                                <th class="text-left py-3.5 px-4 text-[11px] font-semibold text-[#75727C] uppercase tracking-[0.3px] w-[26%]">Perangkat & Model</th>
                                <th class="text-center py-3.5 px-3 text-[11px] font-semibold text-[#75727C] uppercase tracking-[0.3px] w-[12%] whitespace-nowrap">Kategori</th>
                                <th class="text-center py-3.5 px-3 text-[11px] font-semibold text-[#75727C] uppercase tracking-[0.3px] w-[14%] whitespace-nowrap">Status Siklus</th>
                                <th class="text-left py-3.5 px-4 text-[11px] font-semibold text-[#75727C] uppercase tracking-[0.3px] w-[18%]">Lokasi & Klien</th>
                                <th class="text-center py-3.5 px-3 text-[11px] font-semibold text-[#75727C] uppercase tracking-[0.3px] w-[10%] whitespace-nowrap">Garansi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#EFEDEB]">
                            @forelse($serialNumbers as $sn)
                                <tr class="hover:bg-[#F8F7F6] transition-colors">
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-[#17151C] text-[13px] leading-snug">{{ $sn->serial_number }}</div>
                                        <div class="text-[11px] text-gray-500 mt-0.5">Brand: <strong class="text-gray-800">{{ $sn->brand ?: '-' }}</strong></div>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-[#17151C] text-[13px]">{{ $sn->product_name }}</div>
                                        <div class="text-[11.5px] text-gray-500 mt-0.5">Model: {{ $sn->model ?: '-' }}</div>
                                    </td>
                                    <td class="py-3.5 px-3 text-center whitespace-nowrap">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10.5px] font-bold bg-gray-100 text-gray-700">
                                            {{ $sn->category }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-3 text-center whitespace-nowrap">
                                        @php $snBadge = $sn->status_badge; @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10.5px] font-bold border {{ $snBadge['bg'] }} {{ $snBadge['text'] }} {{ $snBadge['border'] }}">
                                            {{ $snBadge['label'] }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <div class="font-semibold text-gray-800 text-xs">{{ $sn->current_location }}</div>
                                        <div class="text-[11px] text-gray-500 mt-0.5">{{ $sn->client_name ?: ($sn->project ? $sn->project->name : 'Stock Buffer') }}</div>
                                    </td>
                                    <td class="py-3.5 px-3 text-center whitespace-nowrap text-[11.5px] text-gray-600">
                                        {{ $sn->warranty_expiry ? $sn->warranty_expiry->format('d M Y') : '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-12 text-center text-gray-400 text-xs">
                                        Belum ada serial number perangkat yang terdaftar.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($serialNumbers->hasPages())
                    <div class="p-4 border-t border-[#EFEDEB]">
                        {{ $serialNumbers->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>

    {{-- MODAL: Register Serial Number Baru --}}
    <div x-show="isCreateModalOpen" class="fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 space-y-4 border border-[#E7E5E3] shadow-xl animate-fade-in-up" @click.outside="isCreateModalOpen = false">
            <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                <h3 class="text-base font-bold text-gray-900">Daftarkan Serial Number Hardware</h3>
                <button @click="isCreateModalOpen = false" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>

            <form action="{{ route('admin_support.inventory.sn.store') }}" method="POST" class="space-y-3.5 text-xs">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Serial Number (SN) *</label>
                        <input type="text" name="serial_number" required class="wms-input font-bold uppercase" placeholder="e.g. FOC24190ABC">
                    </div>
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Kategori *</label>
                        <select name="category" required class="wms-input font-bold">
                            <option value="Router">Router</option>
                            <option value="Switch">Switch</option>
                            <option value="Firewall">Firewall / Security</option>
                            <option value="Access Point">Access Point</option>
                            <option value="Server">Server</option>
                            <option value="SFP">SFP / Transceiver</option>
                            <option value="Power Supply">Power Supply (PSU)</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Nama Perangkat *</label>
                    <input type="text" name="product_name" required class="wms-input" placeholder="e.g. Cisco ISR 4451 Security Router">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Brand / Pabrikan</label>
                        <input type="text" name="brand" class="wms-input" placeholder="e.g. Cisco / Huawei / Fortinet">
                    </div>
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Model / Part Number</label>
                        <input type="text" name="model" class="wms-input" placeholder="e.g. ISR4451/K9">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Status Siklus *</label>
                        <select name="current_status" required class="wms-input font-bold">
                            <option value="In Warehouse">In Warehouse (Gudang HQ)</option>
                            <option value="Allocated to Project">Allocated to Project</option>
                            <option value="Dispatched / In Transit">Dispatched / In Transit</option>
                            <option value="Installed at Site">Installed at Site</option>
                            <option value="RMA / Maintenance">RMA / Maintenance</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Garansi Hingga</label>
                        <input type="date" name="warranty_expiry" class="wms-input">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Lokasi Fisik Saat Ini</label>
                    <input type="text" name="current_location" class="wms-input" value="HQ Warehouse Jakarta Barat">
                </div>

                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Alokasi Klien (Opsional)</label>
                    <input type="text" name="client_name" list="clientList" class="wms-input" placeholder="Ketik / pilih klien">
                    <datalist id="clientList">
                        @foreach($clients as $c)
                            <option value="{{ $c->name }}"></option>
                        @endforeach
                    </datalist>
                </div>

                <div class="pt-3 border-t border-gray-100 flex justify-end gap-2">
                    <button type="button" @click="isCreateModalOpen = false" class="px-4 py-2 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 cursor-pointer">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-[#C81E2C] text-white font-bold rounded-xl hover:brightness-105 shadow-sm cursor-pointer">Simpan Serial Number</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
