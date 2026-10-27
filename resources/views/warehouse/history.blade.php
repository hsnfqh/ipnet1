@extends('layouts.app')

@section('title', 'Riwayat Pergerakan Stok - Warehouse IPNET')

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
        @include('components.topbar', ['title' => 'Riwayat Pergerakan Stok'])
        
        <div class="p-4 sm:p-6 lg:p-7 space-y-6 max-w-[1600px] mx-auto">
            
            <!-- HEADER -->
            <div class="ipnet-card p-5 sm:p-6 anim-fade-up">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-[#E2E8F0]">
                    <div>
                        <p class="text-[#8F0A0D] text-[12px] font-semibold inline-flex items-center uppercase tracking-wider">
                            <span class="ipnet-badge-dot"></span> MONITORING &amp; AUDIT TRAIL
                        </p>
                        <h2 class="text-[20px] font-bold text-[#1E293B] tracking-tight mt-0.5">
                            Riwayat Mutasi &amp; Perubahan Status Perangkat
                        </h2>
                        <p class="text-[13px] text-[#64748B] mt-0.5">
                            Catatan kronologis waktu perpindahan fisik, alokasi pelanggan, dan update operasional inventaris.
                        </p>
                    </div>

                    <a href="{{ route('warehouse.dashboard') }}" 
                       class="btn-ipnet-gradient px-4 py-2.5 rounded-xl font-semibold text-[13px] shadow-sm flex items-center gap-2 cursor-pointer transition shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                        </svg>
                        <span>Kembali ke Dashboard</span>
                    </a>
                </div>
            </div>

            <!-- LOG TIMELINE TABLE -->
            <div class="ipnet-card p-5 sm:p-6 anim-fade-up">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-[#E2E8F0] bg-[#F8FAFC]">
                                <th class="py-3 px-3 text-[11px] font-bold text-[#475569] uppercase tracking-wider">Waktu Pembaruan</th>
                                <th class="py-3 px-3 text-[11px] font-bold text-[#475569] uppercase tracking-wider">Kode Item &amp; SN</th>
                                <th class="py-3 px-3 text-[11px] font-bold text-[#475569] uppercase tracking-wider">Perangkat</th>
                                <th class="py-3 px-3 text-[11px] font-bold text-[#475569] uppercase tracking-wider">Lokasi Tercatat</th>
                                <th class="py-3 px-3 text-[11px] font-bold text-[#475569] uppercase tracking-wider">Status Terakhir</th>
                                <th class="py-3 px-3 text-[11px] font-bold text-[#475569] uppercase tracking-wider">Petugas Pencatat</th>
                                <th class="py-3 px-3 text-[11px] font-bold text-[#475569] uppercase tracking-wider text-right">Detail</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#E2E8F0]">
                            @forelse($items as $item)
                                <tr class="hover:bg-[#F8FAFC] transition">
                                    <td class="py-3 px-3">
                                        <div class="font-bold text-[#1E293B] text-[12.5px]">{{ $item->updated_at ? $item->updated_at->format('d M Y') : '-' }}</div>
                                        <div class="text-[11px] text-[#64748B]">{{ $item->updated_at ? $item->updated_at->format('H:i:s') . ' WIB' : '-' }}</div>
                                    </td>
                                    <td class="py-3 px-3">
                                        <div class="font-mono font-bold text-[#1E293B] text-[12px]">{{ $item->item_code }}</div>
                                        <div class="font-mono text-[11px] text-[#64748B]">SN: {{ $item->serial_number ?: '-' }}</div>
                                    </td>
                                    <td class="py-3 px-3">
                                        <div class="font-bold text-[#1E293B] text-[12.5px]">{{ $item->name }}</div>
                                        <div class="text-[11px] text-[#64748B]">{{ $item->brand }} &bull; {{ $item->item_type }}</div>
                                    </td>
                                    <td class="py-3 px-3">
                                        <div class="text-[12px] font-semibold text-[#1E293B]">{{ $item->location_type === 'Warehouse IPNET' ? 'Gudang IPNET' : $item->location_type }}</div>
                                        <div class="text-[11px] text-[#64748B]">{{ in_array($item->location_type, ['Gudang IPNET', 'Warehouse IPNET']) ? ($item->warehouse_rack ?: 'Gudang IPNET') : ($item->poc_customer_name ?: 'Alamat Customer') }}</div>
                                    </td>
                                    <td class="py-3 px-3">
                                        <span class="px-2 py-0.5 rounded text-[11px] font-bold {{ $item->status === 'Tersedia' ? 'bg-emerald-100 text-emerald-800' : ($item->status === 'POC / Pinjaman' ? 'bg-purple-100 text-purple-800' : 'bg-slate-100 text-slate-800') }}">
                                            {{ $item->status }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-3">
                                        <div class="text-[12px] font-semibold text-[#1E293B]">{{ $item->creator ? $item->creator->name : 'Ahmad (Warehouse)' }}</div>
                                        <div class="text-[10.5px] text-[#64748B]">Staff Logistik</div>
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
                                        Belum ada riwayat pergerakan stok.
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
