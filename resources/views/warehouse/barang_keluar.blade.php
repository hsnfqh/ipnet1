@extends('layouts.app')

@section('title', 'Transaksi Barang Keluar - Warehouse IPNET')

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
<div class="flex h-screen overflow-hidden bg-[#F8FAFC] font-sans">
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Barang Keluar'])
        
        <div class="p-4 sm:p-6 lg:p-7 space-y-6 max-w-[1600px] mx-auto">
            
            <!-- HEADER -->
            <div class="ipnet-card p-5 sm:p-6 anim-fade-up">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-[#E2E8F0]">
                    <div>
                        <p class="text-[#8F0A0D] text-[12px] font-semibold inline-flex items-center uppercase tracking-wider">
                            <span class="ipnet-badge-dot"></span> TRANSAKSI INVENTORI
                        </p>
                        <h2 class="text-[20px] font-bold text-[#1E293B] tracking-tight mt-0.5">
                            Pengeluaran Perangkat (Proyek, Dispatch &amp; POC)
                        </h2>
                        <p class="text-[13px] text-[#64748B] mt-0.5">
                            Monitoring alur perangkat yang dikeluarkan dari gudang IPNET untuk implementasi proyek pelanggan, surat jalan (DO), atau POC.
                        </p>
                    </div>

                    <a href="{{ route('warehouse.items.index') }}" 
                       class="btn-ipnet-gradient px-4 py-2.5 rounded-xl font-semibold text-[13px] shadow-sm flex items-center gap-2 cursor-pointer transition shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>Alokasikan Pengeluaran</span>
                    </a>
                </div>

                <!-- FILTER / SEARCH BAR -->
                <form method="GET" action="{{ route('warehouse.transactions.out') }}" class="pt-4 flex flex-col sm:flex-row gap-3">
                    <div class="relative flex-1">
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}" 
                               placeholder="Cari no kontrak, Serial Number, nama pelanggan, PIC, atau sales..."
                               class="w-full pl-10 pr-4 py-2.5 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[13px] font-medium text-[#1E293B] placeholder-[#94A3B8] focus:bg-white focus:border-[#8F0A0D] outline-none transition">
                        <svg class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-[#94A3B8]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <button type="submit" class="px-5 py-2.5 bg-[#1E293B] hover:bg-[#0F172A] text-white rounded-xl text-[13px] font-bold transition">
                        Cari Data
                    </button>
                    @if(request('search'))
                        <a href="{{ route('warehouse.transactions.out') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-[13px] font-semibold transition text-center">
                            Reset
                        </a>
                    @endif
                </form>
            </div>

            <!-- TABLE BARANG KELUAR -->
            <div class="ipnet-card p-5 sm:p-6 anim-fade-up">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-[#E2E8F0] bg-[#F8FAFC]">
                                <th class="py-3 px-3 text-[11px] font-bold text-[#475569] uppercase tracking-wider">No. Kontrak / Proyek</th>
                                <th class="py-3 px-3 text-[11px] font-bold text-[#475569] uppercase tracking-wider">Perangkat &amp; Brand</th>
                                <th class="py-3 px-3 text-[11px] font-bold text-[#475569] uppercase tracking-wider">Serial Number</th>
                                <th class="py-3 px-3 text-[11px] font-bold text-[#475569] uppercase tracking-wider">Tujuan Pengeluaran</th>
                                <th class="py-3 px-3 text-[11px] font-bold text-[#475569] uppercase tracking-wider">PIC &amp; Sales Pengawal</th>
                                <th class="py-3 px-3 text-[11px] font-bold text-[#475569] uppercase tracking-wider">Status</th>
                                <th class="py-3 px-3 text-[11px] font-bold text-[#475569] uppercase tracking-wider text-right">Detail</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#E2E8F0]">
                            @forelse($items as $item)
                                <tr class="hover:bg-[#F8FAFC] transition">
                                    <td class="py-3 px-3">
                                        <div class="font-bold text-[#1E293B] text-[12.5px] font-mono">{{ $item->contract_number ?: ($item->po_number ?: 'Tanpa No Kontrak') }}</div>
                                        <div class="text-[11px] text-[#64748B]">{{ $item->updated_at ? $item->updated_at->format('d M Y') : '-' }}</div>
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
                                        <div class="text-[12px] font-bold text-[#1E293B]">{{ $item->poc_customer_name ?: ($item->customer_address ? 'Pelanggan Terdaftar' : 'Proyek Implementasi') }}</div>
                                        <div class="text-[11px] text-[#64748B] truncate max-w-[200px]">{{ $item->customer_address ?: ($item->warehouse_rack ?: '-') }}</div>
                                    </td>
                                    <td class="py-3 px-3">
                                        <div class="text-[12px] font-semibold text-[#1E293B]">PIC: {{ $item->customer_pic ?: ($item->poc_pic_name ?: '-') }}</div>
                                        <div class="text-[11px] text-[#64748B]">Sales: {{ $item->customer_sales_name ?: ($item->poc_sales_name ?: '-') }}</div>
                                    </td>
                                    <td class="py-3 px-3">
                                        <span class="px-2 py-0.5 rounded text-[11px] font-bold {{ $item->status === 'POC / Pinjaman' ? 'bg-purple-100 text-purple-800' : 'bg-rose-100 text-rose-800' }}">
                                            {{ $item->status }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-3 text-right">
                                        <a href="{{ route('warehouse.items.index', ['search' => $item->item_code]) }}" 
                                           class="px-2.5 py-1 text-[11.5px] font-semibold text-[#8F0A0D] bg-red-50 hover:bg-red-100 border border-red-200 rounded-lg transition">
                                            Buka
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-8 text-center text-[#94A3B8] text-[13px]">
                                        Tidak ada catatan barang keluar yang sesuai.
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

        </div>
    </div>
</div>
@endsection
