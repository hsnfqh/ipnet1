@extends('layouts.app')

@section('title', 'Stock Opname & Audit Gudang - Warehouse IPNET')

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
        @include('components.topbar', ['title' => 'Stock Opname'])
        
        <div class="p-4 sm:p-6 lg:p-7 space-y-6 max-w-[1600px] mx-auto">
            
            <!-- HEADER -->
            <div class="ipnet-card p-5 sm:p-6 anim-fade-up">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-[#E2E8F0]">
                    <div>
                        <p class="text-[#8F0A0D] text-[12px] font-semibold inline-flex items-center uppercase tracking-wider">
                            <span class="ipnet-badge-dot"></span> TRANSAKSI INVENTORI
                        </p>
                        <h2 class="text-[20px] font-bold text-[#1E293B] tracking-tight mt-0.5">
                            Stock Opname &amp; Audit Fisik Rak Gudang
                        </h2>
                        <p class="text-[13px] text-[#64748B] mt-0.5">
                            Verifikasi kesesuaian fisik stok perangkat yang tersimpan di Rak Gudang IPNET dengan data sistem.
                        </p>
                    </div>

                    <div class="flex items-center gap-2.5 shrink-0">
                        <button type="button" 
                                onclick="alert('Audit Stock Opname Selesai. Seluruh fisik rak telah diverifikasi sesuai sistem!')"
                                class="btn-ipnet-gradient px-4 py-2.5 rounded-xl font-semibold text-[13px] shadow-sm flex items-center gap-2 cursor-pointer transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Selesaikan Verifikasi Opname</span>
                        </button>
                    </div>
                </div>

                <!-- RACK SUMMARY PILLS -->
                <div class="pt-4 flex flex-wrap items-center gap-2">
                    <a href="{{ route('warehouse.stock_opname.index', ['rack' => 'all']) }}" 
                       class="px-3 py-1.5 rounded-xl text-[12.5px] font-bold transition {{ (!request('rack') || request('rack') === 'all') ? 'bg-[#8F0A0D] text-white shadow-sm' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}">
                        Semua Rak ({{ $metrics['warehouse_stock'] }} Unit)
                    </a>
                    @foreach($racks as $r)
                        <a href="{{ route('warehouse.stock_opname.index', ['rack' => $r->warehouse_rack]) }}" 
                           class="px-3 py-1.5 rounded-xl text-[12.5px] font-bold transition {{ request('rack') === $r->warehouse_rack ? 'bg-[#8F0A0D] text-white shadow-sm' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}">
                            {{ $r->warehouse_rack }} ({{ $r->count }})
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- OPNAME TABLE -->
            <div class="ipnet-card p-5 sm:p-6 anim-fade-up">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-[#E2E8F0] bg-[#F8FAFC]">
                                <th class="py-3 px-3 text-[11px] font-bold text-[#475569] uppercase tracking-wider">Lokasi Rak Gudang</th>
                                <th class="py-3 px-3 text-[11px] font-bold text-[#475569] uppercase tracking-wider">Kode Item &amp; SN</th>
                                <th class="py-3 px-3 text-[11px] font-bold text-[#475569] uppercase tracking-wider">Perangkat &amp; Model</th>
                                <th class="py-3 px-3 text-[11px] font-bold text-[#475569] uppercase tracking-wider">Jenis &amp; Komponen</th>
                                <th class="py-3 px-3 text-[11px] font-bold text-[#475569] uppercase tracking-wider">Kategori</th>
                                <th class="py-3 px-3 text-[11px] font-bold text-[#475569] uppercase tracking-wider">Status Fisik</th>
                                <th class="py-3 px-3 text-[11px] font-bold text-[#475569] uppercase tracking-wider text-right">Verifikasi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#E2E8F0]">
                            @forelse($items as $item)
                                <tr class="hover:bg-[#F8FAFC] transition">
                                    <td class="py-3 px-3">
                                        <div class="font-bold text-[#1E293B] text-[12.5px]">{{ $item->warehouse_rack ?: 'Gudang Utama' }}</div>
                                        <div class="text-[11px] text-emerald-600 font-semibold">Tersimpan di Gudang</div>
                                    </td>
                                    <td class="py-3 px-3">
                                        <div class="font-mono text-[12px] font-bold text-[#1E293B]">{{ $item->item_code }}</div>
                                        <div class="font-mono text-[11px] text-[#64748B]">SN: {{ $item->serial_number ?: '-' }}</div>
                                    </td>
                                    <td class="py-3 px-3">
                                        <div class="font-bold text-[#1E293B] text-[12.5px]">{{ $item->name }}</div>
                                        <div class="text-[11px] text-[#64748B]">{{ $item->brand }} &bull; PN: {{ $item->product_number ?: '-' }}</div>
                                    </td>
                                    <td class="py-3 px-3">
                                        <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-slate-100 text-slate-700">
                                            {{ $item->item_type }}
                                        </span>
                                        <div class="text-[11px] text-[#64748B] mt-0.5">{{ $item->component_type ?: '-' }}</div>
                                    </td>
                                    <td class="py-3 px-3">
                                        <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-slate-100 text-slate-700">
                                            {{ $item->category }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-3">
                                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Fisik Siap Pakai
                                        </span>
                                    </td>
                                    <td class="py-3 px-3 text-right">
                                        <button type="button" 
                                                onclick="this.innerText = 'Terverifikasi'; this.className = 'px-2.5 py-1 text-[11px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-lg';"
                                                class="px-2.5 py-1 text-[11.5px] font-semibold text-[#8F0A0D] bg-red-50 hover:bg-red-100 border border-red-200 rounded-lg transition cursor-pointer">
                                            Cek Fisik
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-8 text-center text-[#94A3B8] text-[13px]">
                                        Tidak ada data perangkat di rak gudang ini.
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
