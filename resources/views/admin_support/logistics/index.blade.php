@extends('layouts.app')

@section('title', 'Surat Jalan & Logistik - Admin Support')

@section('content')
<div class="flex h-screen overflow-hidden" x-data="{ isCreateModalOpen: false, isStatusModalOpen: false, selectedDispatch: null, statusUrl: '' }" x-cloak>
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto bg-[#FAF9F8]">
        @include('components.topbar', ['title' => 'Surat Jalan, Delivery Instructions & Logistics Tracker'])
        
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
                <form method="GET" action="{{ route('admin_support.logistics.index') }}" class="flex flex-col sm:flex-row flex-wrap gap-2.5 w-full sm:w-auto">
                    <div class="relative">
                        <svg class="w-3.5 h-3.5 absolute left-3 top-3 text-[#948F99]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}"
                               placeholder="Cari No. SJ / Penerima / Klien..." 
                               class="w-full sm:w-64 px-[11px] py-[9px] pl-8 rounded-lg border border-[#E7E5E3] text-[13px] text-[#17151C] outline-none bg-white focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all">
                    </div>

                    <select name="status" onchange="this.form.submit()" 
                            class="w-full sm:w-52 px-[11px] py-[9px] rounded-lg border border-[#E7E5E3] text-[13px] text-[#17151C] outline-none bg-white focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all cursor-pointer">
                        <option value="">Semua Status Pengiriman</option>
                        <option value="Ready to Dispatch" {{ request('status') == 'Ready to Dispatch' ? 'selected' : '' }}>Ready to Dispatch (Siap)</option>
                        <option value="In Transit" {{ request('status') == 'In Transit' ? 'selected' : '' }}>In Transit (Di Jalan)</option>
                        <option value="Delivered" {{ request('status') == 'Delivered' ? 'selected' : '' }}>Delivered (Sampai)</option>
                        <option value="Confirmed / Signed" {{ request('status') == 'Confirmed / Signed' ? 'selected' : '' }}>Confirmed (Tertandatangani)</option>
                    </select>

                    @if(request('search') || request('status'))
                        <a href="{{ route('admin_support.logistics.index') }}" 
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
                    <span>Terbitkan Surat Jalan (DO)</span>
                </button>
            </div>

            {{-- Table Surat Jalan --}}
            <div class="bg-white rounded-xl border border-[#E7E5E3] shadow-[0_1px_2px_rgba(14,13,18,0.05)] overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[1100px] border-collapse text-[13px]">
                        <thead>
                            <tr class="bg-[#F1F0EE] border-b border-[#E7E5E3]">
                                <th class="text-left py-3.5 px-4 text-[11px] font-semibold text-[#75727C] uppercase tracking-[0.3px] w-[18%]">No. Surat Jalan & Tgl</th>
                                <th class="text-left py-3.5 px-4 text-[11px] font-semibold text-[#75727C] uppercase tracking-[0.3px] w-[26%]">Tujuan & Penerima</th>
                                <th class="text-left py-3.5 px-4 text-[11px] font-semibold text-[#75727C] uppercase tracking-[0.3px] w-[22%]">Ekspedisi & Kurir</th>
                                <th class="text-left py-3.5 px-4 text-[11px] font-semibold text-[#75727C] uppercase tracking-[0.3px] w-[14%]">Material / Barang</th>
                                <th class="text-center py-3.5 px-3 text-[11px] font-semibold text-[#75727C] uppercase tracking-[0.3px] w-[10%] whitespace-nowrap">Status</th>
                                <th class="text-right py-3.5 px-4 text-[11px] font-semibold text-[#75727C] uppercase tracking-[0.3px] w-[10%] whitespace-nowrap">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#EFEDEB]">
                            @forelse($dispatches as $disp)
                                <tr class="hover:bg-[#F8F7F6] transition-colors">
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-[#17151C] text-[13px] leading-snug">{{ $disp->dispatch_number }}</div>
                                        <div class="text-[11px] text-gray-500 mt-0.5 font-medium">
                                            {{ $disp->dispatch_date ? $disp->dispatch_date->format('d M Y') : '-' }}
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-[#17151C] text-[13px]">{{ $disp->client_name ?: ($disp->project ? $disp->project->name : 'Site Proyek') }}</div>
                                        <div class="text-[11.5px] text-gray-700 mt-0.5">
                                            Penerima: <strong>{{ $disp->recipient_name }}</strong> ({{ $disp->recipient_phone ?: '-' }})
                                        </div>
                                        <div class="text-[11px] text-gray-400 truncate max-w-sm mt-0.5">
                                            {{ $disp->destination_address }}
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <div class="font-semibold text-gray-800 text-[12.5px]">{{ $disp->courier_name ?: $disp->courier_type }}</div>
                                        @if($disp->tracking_ref)
                                            <div class="text-[11px] text-gray-500 mt-0.5">Ref/Resi: <strong>{{ $disp->tracking_ref }}</strong></div>
                                        @endif
                                        <div class="text-[10.5px] text-gray-400">Asal: {{ $disp->origin_warehouse }}</div>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        @if($disp->items->count() > 0)
                                            <div class="text-xs font-semibold text-gray-800">{{ $disp->items->first()->item_name }}</div>
                                            <div class="text-[11px] text-gray-500">Qty: {{ $disp->items->first()->quantity }} {{ $disp->items->first()->unit }}</div>
                                            @if($disp->items->count() > 1)
                                                <div class="text-[10.5px] text-[#C81E2C] font-semibold">+{{ $disp->items->count() - 1 }} item lainnya</div>
                                            @endif
                                        @else
                                            <span class="text-xs text-gray-400">Belum ada rincian item</span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-3 text-center whitespace-nowrap">
                                        @php $sBadge = $disp->status_badge; @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10.5px] font-bold border {{ $sBadge['bg'] }} {{ $sBadge['text'] }} {{ $sBadge['border'] }}">
                                            {{ $sBadge['label'] }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                        <button @click="selectedDispatch = {{ $disp->id }}; statusUrl = '/admin-support/logistics/' + {{ $disp->id }} + '/status'; isStatusModalOpen = true" 
                                                class="px-2.5 py-1 bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold rounded-lg text-xs transition">
                                            Update Status
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-12 text-center text-gray-400 text-xs">
                                        Belum ada surat jalan tercatat.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($dispatches->hasPages())
                    <div class="p-4 border-t border-[#EFEDEB]">
                        {{ $dispatches->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>

    {{-- MODAL: Terbitkan Surat Jalan Baru --}}
    <div x-show="isCreateModalOpen" class="fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 space-y-4 border border-[#E7E5E3] shadow-xl animate-fade-in-up" @click.outside="isCreateModalOpen = false">
            <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                <h3 class="text-base font-bold text-gray-900">Terbitkan Surat Jalan (DO) Pengiriman</h3>
                <button @click="isCreateModalOpen = false" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>

            <form action="{{ route('admin_support.logistics.store') }}" method="POST" class="space-y-3.5 text-xs">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Klien / Tujuan *</label>
                        <input type="text" name="client_name" list="clientList" required class="wms-input" placeholder="Pilih / ketik nama klien">
                        <datalist id="clientList">
                            @foreach($clients as $c)
                                <option value="{{ $c->name }}"></option>
                            @endforeach
                        </datalist>
                    </div>
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Tanggal Pengiriman *</label>
                        <input type="date" name="dispatch_date" required class="wms-input" value="{{ date('Y-m-d') }}">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Metode Pengiriman *</label>
                        <select name="courier_type" required class="wms-input">
                            <option value="Internal Driver">Internal Driver (Mobil Operasional)</option>
                            <option value="Ekspedisi / Kurir">Ekspedisi (JNE Trucking / Dakota / Indah)</option>
                            <option value="Vendor Direct">Vendor Direct to Site</option>
                            <option value="Hand Carry">Hand Carry Engineer</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Nama Driver / Ekspedisi</label>
                        <input type="text" name="courier_name" class="wms-input" placeholder="e.g. Pak Supri / JNE Trucking">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Nama Penerima di Site *</label>
                        <input type="text" name="recipient_name" required class="wms-input" placeholder="e.g. Bapak Hendra (PIC IT)">
                    </div>
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">No. Kontak Penerima</label>
                        <input type="text" name="recipient_phone" class="wms-input" placeholder="e.g. 0812-3344-5566">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Alamat Lengkap Pengiriman *</label>
                    <textarea name="destination_address" rows="2" required class="wms-input" placeholder="Gedung, lantai, nomor ruangan, kota..."></textarea>
                </div>

                {{-- Barang yang Dikirim --}}
                <div class="p-3 rounded-xl bg-gray-50 border border-gray-200 space-y-2.5">
                    <div class="font-bold text-gray-800 text-xs">Rincian Barang Utama:</div>
                    <div class="grid grid-cols-3 gap-2">
                        <div class="col-span-2">
                            <input type="text" name="item_name" class="wms-input" placeholder="Nama perangkat (e.g. Cisco ISR 4451)">
                        </div>
                        <div>
                            <input type="number" name="quantity" min="1" value="1" class="wms-input" placeholder="Qty">
                        </div>
                    </div>
                    <div>
                        <input type="text" name="serial_numbers" class="wms-input" placeholder="Serial Number (e.g. FOC24190ABC, FOC24190ABD)">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Catatan Tambahan</label>
                    <textarea name="delivery_notes" rows="2" class="wms-input" placeholder="Instruksi penanganan barang / jadwal bongkar muat..."></textarea>
                </div>

                <div class="pt-3 border-t border-gray-100 flex justify-end gap-2">
                    <button type="button" @click="isCreateModalOpen = false" class="px-4 py-2 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 cursor-pointer">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-[#C81E2C] text-white font-bold rounded-xl hover:brightness-105 shadow-sm cursor-pointer">Terbitkan & Cetak</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL: Update Status Surat Jalan --}}
    <div x-show="isStatusModalOpen" class="fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-sm w-full p-5 space-y-4 border border-[#E7E5E3] shadow-xl animate-fade-in-up" @click.outside="isStatusModalOpen = false">
            <div class="flex items-center justify-between pb-2 border-b border-gray-100">
                <h3 class="text-base font-bold text-gray-900">Update Status Pengiriman</h3>
                <button @click="isStatusModalOpen = false" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>

            <form :action="statusUrl" method="POST" class="space-y-3.5 text-xs">
                @csrf
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Status Pengiriman *</label>
                    <select name="status" required class="wms-input font-bold">
                        <option value="Ready to Dispatch">Ready to Dispatch (Siap Kirim)</option>
                        <option value="In Transit">In Transit (Sedang Dalam Perjalanan)</option>
                        <option value="Delivered">Delivered (Telah Tiba di Site)</option>
                        <option value="Confirmed / Signed">Confirmed / Signed (Tanda Terima Sah)</option>
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Catatan Pengiriman</label>
                    <textarea name="delivery_notes" rows="2" class="wms-input" placeholder="e.g. Diterima oleh Pak Arif jam 14:30 WIB..."></textarea>
                </div>

                <div class="pt-2 border-t border-gray-100 flex justify-end gap-2">
                    <button type="button" @click="isStatusModalOpen = false" class="px-3 py-1.5 bg-gray-100 text-gray-700 font-bold rounded-lg">Batal</button>
                    <button type="submit" class="px-3 py-1.5 bg-[#C81E2C] text-white font-bold rounded-lg">Update</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
