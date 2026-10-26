@extends('layouts.app')

@section('title', 'Transaksi Barang Masuk - Warehouse IPNET')

@push('styles')
<style>
    [x-cloak] { display: none !important; }
    .ipnet-card {
        background-color: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 18px;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.035);
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
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
        0% { opacity: 0; transform: translateY(14px); }
        100% { opacity: 1; transform: translateY(0); }
    }
    .anim-fade-up {
        animation: fadeUpStagger 0.45s cubic-bezier(0.16, 1, 0.3, 1) both;
    }
</style>
@endpush

@section('content')
<div class="flex h-screen overflow-hidden bg-[#F8FAFC] font-sans" 
     x-data="{ 
         isDetailModalOpen: false, 
         selectedItem: null,
         openDetailModal(item) {
             this.selectedItem = item;
             this.isDetailModalOpen = true;
         }
     }">
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Barang Masuk'])
        
        <div class="p-4 sm:p-6 lg:p-7 space-y-6 max-w-[1600px] mx-auto">
            
            <!-- HEADER -->
            <div class="ipnet-card p-5 sm:p-6 anim-fade-up">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-[#E2E8F0]">
                    <div>
                        <p class="text-[#8F0A0D] text-[12px] font-semibold inline-flex items-center uppercase tracking-wider">
                            <span class="ipnet-badge-dot"></span> TRANSAKSI INVENTORI
                        </p>
                        <h2 class="text-[20px] font-bold text-[#1E293B] tracking-tight mt-0.5">
                            Penerimaan Barang Masuk (PO, Vendor &amp; Return)
                        </h2>
                        <p class="text-[13px] text-[#64748B] mt-0.5">
                            Pencatatan pengadaan perangkat baru dari Purchase Order (PO), peminjaman distributor, atau pengembalian POC.
                        </p>
                    </div>

                    <a href="{{ route('warehouse.items.index') }}" 
                       class="btn-ipnet-gradient px-4 py-2.5 rounded-xl font-semibold text-[13px] shadow-sm flex items-center gap-2 cursor-pointer transition shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>Input Penerimaan Barang</span>
                    </a>
                </div>

                <!-- FILTER / SEARCH BAR -->
                <form method="GET" action="{{ route('warehouse.transactions.in') }}" class="pt-4 flex flex-col sm:flex-row gap-3">
                    <div class="relative flex-1">
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}" 
                               placeholder="Cari nomor PO, Serial Number, nama barang, atau nama distributor..."
                               class="w-full pl-10 pr-4 py-2.5 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[13px] font-medium text-[#1E293B] placeholder-[#94A3B8] focus:bg-white focus:border-[#8F0A0D] outline-none transition">
                        <svg class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-[#94A3B8]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <button type="submit" class="px-5 py-2.5 bg-[#1E293B] hover:bg-[#0F172A] text-white rounded-xl text-[13px] font-bold transition">
                        Cari Data
                    </button>
                    @if(request('search'))
                        <a href="{{ route('warehouse.transactions.in') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-[13px] font-semibold transition text-center">
                            Reset
                        </a>
                    @endif
                </form>
            </div>

            <!-- TABLE BARANG MASUK -->
            <div class="ipnet-card p-5 sm:p-6 anim-fade-up">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-[#E2E8F0] bg-[#F8FAFC]">
                                <th class="py-3 px-3 text-[11px] font-bold text-[#475569] uppercase tracking-wider">No. PO &amp; Tanggal Masuk</th>
                                <th class="py-3 px-3 text-[11px] font-bold text-[#475569] uppercase tracking-wider">Nama Perangkat &amp; Brand</th>
                                <th class="py-3 px-3 text-[11px] font-bold text-[#475569] uppercase tracking-wider">Serial Number</th>
                                <th class="py-3 px-3 text-[11px] font-bold text-[#475569] uppercase tracking-wider">Sumber Masuk</th>
                                <th class="py-3 px-3 text-[11px] font-bold text-[#475569] uppercase tracking-wider">Rak Gudang</th>
                                <th class="py-3 px-3 text-[11px] font-bold text-[#475569] uppercase tracking-wider">Status</th>
                                <th class="py-3 px-3 text-[11px] font-bold text-[#475569] uppercase tracking-wider text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#E2E8F0]">
                            @forelse($items as $item)
                                <tr class="hover:bg-[#F8FAFC] transition">
                                    <td class="py-3 px-3">
                                        <div class="font-bold text-[#1E293B] text-[12.5px] font-mono">{{ $item->po_number ?: 'Tanpa No PO' }}</div>
                                        <div class="text-[11px] text-[#64748B]">{{ $item->created_at ? $item->created_at->format('d M Y, H:i') : '-' }}</div>
                                    </td>
                                    <td class="py-3 px-3">
                                        <div class="font-bold text-[#1E293B] text-[12.5px]">{{ $item->name }}</div>
                                        <div class="text-[11px] text-[#64748B]">{{ $item->brand }} &bull; {{ $item->item_type }}</div>
                                    </td>
                                    <td class="py-3 px-3">
                                        <span class="font-mono text-[12px] font-bold text-[#334155]">{{ $item->serial_number ?: '-' }}</span>
                                        <div class="text-[10.5px] text-[#64748B] font-mono">{{ $item->item_code }}</div>
                                    </td>
                                    <td class="py-3 px-3">
                                        @if($item->disti_name)
                                            <span class="text-[12px] font-semibold text-purple-700">Distributor: {{ $item->disti_name }}</span>
                                        @elseif($item->ipnet_work_unit)
                                            <span class="text-[12px] font-semibold text-blue-700">Tim Internal {{ $item->ipnet_work_unit }}</span>
                                        @else
                                            <span class="text-[12px] font-semibold text-emerald-700">Pengadaan Resmi IPNET</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-3">
                                        <span class="text-[12px] font-semibold text-[#1E293B]">{{ $item->warehouse_rack ?: 'Gudang IPNET' }}</span>
                                    </td>
                                    <td class="py-3 px-3">
                                        <span class="px-2 py-0.5 rounded text-[11px] font-bold {{ $item->status === 'Tersedia' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-800' }}">
                                            {{ $item->status }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-3 text-right whitespace-nowrap">
                                        <button type="button" 
                                                @click="openDetailModal({{ json_encode($item) }})" 
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-[11.5px] font-bold text-[#8F0A0D] bg-red-50 hover:bg-red-100 border border-red-200 rounded-xl transition cursor-pointer shadow-2xs">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            <span>Detail Barang</span>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-8 text-center text-[#94A3B8] text-[13px]">
                                        Tidak ada catatan barang masuk yang cocok dengan pencarian.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $items->links() }}
                </div>
            </div>

            <!-- MODAL DETAIL BARANG MASUK -->
            <template x-teleport="body">
                <div x-show="isDetailModalOpen" 
                     x-cloak
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-[#0F172A]/60 z-[99999] flex items-center justify-center p-3 sm:p-5 backdrop-blur-xs"
                     @click.self="isDetailModalOpen = false"
                     @keydown.escape.window="isDetailModalOpen = false">
                    
                    <div class="bg-white rounded-2xl w-[720px] max-w-full max-h-[90vh] flex flex-col text-left shadow-[0_20px_60px_rgba(15,23,42,0.25)] border border-[#E2E8F0] overflow-hidden">
                        
                        <!-- Fixed Header -->
                        <div class="px-6 py-4 bg-[#F8FAFC] border-b border-[#E2E8F0] flex items-start justify-between shrink-0">
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 rounded-xl bg-red-50 text-[#8F0A0D] border border-red-100 flex items-center justify-center shrink-0 mt-0.5">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-[11px] font-mono px-2 py-0.5 rounded bg-slate-100 text-slate-700 font-bold border border-slate-200" x-text="selectedItem?.item_code"></span>
                                        <span class="text-[11px] font-bold px-2 py-0.5 rounded-full" 
                                              :class="selectedItem?.status === 'Tersedia' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-800'"
                                              x-text="selectedItem?.status"></span>
                                    </div>
                                    <h3 class="text-base font-bold text-slate-900 leading-snug" x-text="selectedItem?.name"></h3>
                                    <p class="text-xs text-slate-500 mt-0.5" x-text="'Brand: ' + (selectedItem?.brand || '-') + ' • Tipe: ' + (selectedItem?.item_type || '-')"></p>
                                </div>
                            </div>
                            <button type="button" @click="isDetailModalOpen = false" class="text-slate-400 hover:text-slate-600 cursor-pointer p-1.5 rounded-lg hover:bg-slate-100 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <!-- Scrollable Body -->
                        <div class="p-6 overflow-y-auto space-y-4 text-xs flex-1 min-h-0">
                            <!-- Grid Hardware & Identifikasi -->
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                                    <p class="text-[10.5px] font-semibold text-slate-400 uppercase tracking-wider">Serial Number (S/N)</p>
                                    <p class="font-mono font-bold text-slate-800 text-[12px] mt-0.5" x-text="selectedItem?.serial_number || '-'"></p>
                                </div>
                                <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                                    <p class="text-[10.5px] font-semibold text-slate-400 uppercase tracking-wider">Part Number (P/N)</p>
                                    <p class="font-mono font-bold text-slate-800 text-[12px] mt-0.5" x-text="selectedItem?.product_number || '-'"></p>
                                </div>
                                <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                                    <p class="text-[10.5px] font-semibold text-slate-400 uppercase tracking-wider">Komponen / Kabel</p>
                                    <p class="font-bold text-slate-800 text-[12px] mt-0.5" x-text="(selectedItem?.component_type || '-') + (selectedItem?.cable_type ? ' (' + selectedItem?.cable_type + ')' : '')"></p>
                                </div>
                            </div>

                            <!-- Panel Penerimaan & Sumber Masuk -->
                            <div class="p-4 rounded-xl bg-slate-50/80 border border-slate-200/80 space-y-3">
                                <div class="flex items-center gap-2 pb-1 border-b border-slate-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#8F0A0D]"></span>
                                    <h4 class="font-bold text-slate-800 uppercase tracking-wider text-[11px]">Informasi Dokumen &amp; Sumber Masuk</h4>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-[11.5px]">
                                    <div>
                                        <p class="text-slate-500 font-semibold text-[10.5px]">Nomor Purchase Order (PO):</p>
                                        <p class="font-bold font-mono text-slate-900 text-xs mt-0.5" x-text="selectedItem?.po_number || 'Tidak ada No. PO'"></p>
                                    </div>
                                    <div>
                                        <p class="text-slate-500 font-semibold text-[10.5px]">Nomor Kontrak:</p>
                                        <p class="font-bold font-mono text-slate-900 text-xs mt-0.5" x-text="selectedItem?.contract_number || '-'"></p>
                                    </div>
                                    <div>
                                        <p class="text-slate-500 font-semibold text-[10.5px]">Sumber Masuk / Pengadaan:</p>
                                        <p class="font-bold text-slate-800 mt-0.5" 
                                           x-text="selectedItem?.disti_name ? ('Distributor: ' + selectedItem?.disti_name) : (selectedItem?.ipnet_work_unit ? ('Tim Internal ' + selectedItem?.ipnet_work_unit) : 'Pengadaan Resmi IPNET')"></p>
                                    </div>
                                    <div>
                                        <p class="text-slate-500 font-semibold text-[10.5px]">PIC &amp; Kontak Sumber:</p>
                                        <p class="font-medium text-slate-800 mt-0.5" 
                                           x-text="(selectedItem?.disti_pic || (selectedItem?.ipnet_pic_name || '-')) + ' • ' + (selectedItem?.disti_phone || (selectedItem?.ipnet_phone || '-'))"></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Panel Lokasi Penyimpanan -->
                            <div class="p-4 rounded-xl bg-blue-50/60 border border-blue-200/70 space-y-2.5">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                        </svg>
                                        <span class="font-bold text-blue-900 text-xs">Lokasi Rak &amp; Penyimpanan Gudang</span>
                                    </div>
                                    <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-white text-blue-700 border border-blue-200" 
                                          x-text="selectedItem?.location_type || 'Gudang IPNET'"></span>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-[11.5px] text-blue-900 pt-1">
                                    <div>
                                        <p class="text-blue-600 font-semibold text-[10.5px]">Posisi Rak Gudang:</p>
                                        <p class="font-bold text-xs mt-0.5 text-blue-950" x-text="selectedItem?.warehouse_rack || 'Gudang Utama IPNET'"></p>
                                    </div>
                                    <div>
                                        <p class="text-blue-600 font-semibold text-[10.5px]">Kategori Stok:</p>
                                        <p class="font-bold text-xs mt-0.5 text-blue-950" x-text="selectedItem?.category || 'Stock'"></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Catatan Tambahan -->
                            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                                <p class="text-[10.5px] font-semibold text-slate-400 uppercase tracking-wider">Catatan Barang Masuk</p>
                                <p class="text-slate-700 text-xs mt-1 whitespace-pre-line" x-text="selectedItem?.notes || 'Tidak ada catatan khusus pada barang masuk ini.'"></p>
                            </div>
                        </div>

                        <!-- Fixed Footer -->
                        <div class="px-6 py-3.5 bg-[#F8FAFC] border-t border-[#E2E8F0] flex items-center justify-end gap-3 shrink-0">
                            <button type="button" @click="isDetailModalOpen = false" class="py-2.5 px-5 rounded-xl bg-white text-[#334155] border border-[#CBD5E1] font-bold text-xs hover:bg-[#F1F5F9] transition cursor-pointer">
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>
            </template>

        </div>
    </div>
</div>
@endsection
