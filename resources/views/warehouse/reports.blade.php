@extends('layouts.app')

@section('title', 'Laporan Inventori - Warehouse IPNET')

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
        @include('components.topbar', ['title' => 'Laporan Inventori'])
        
        <div class="p-4 sm:p-6 lg:p-7 space-y-6 max-w-[1600px] mx-auto">
            
            <!-- HEADER -->
            <div class="ipnet-card p-5 sm:p-6 anim-fade-up">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-[#E2E8F0]">
                    <div>
                        <p class="text-[#8F0A0D] text-[12px] font-semibold inline-flex items-center uppercase tracking-wider">
                            <span class="ipnet-badge-dot"></span> MONITORING &amp; LAPORAN
                        </p>
                        <h2 class="text-[20px] font-bold text-[#1E293B] tracking-tight mt-0.5">
                            Laporan Rekapitulasi Inventaris &amp; Aset Logistik
                        </h2>
                        <p class="text-[13px] text-[#64748B] mt-0.5">
                            Ringkasan eksekutif status ketersediaan barang, rasio perangkat cadangan, dan laporan alokasi proyek IPNET 1-5.
                        </p>
                    </div>

                    <div class="flex items-center gap-2.5 shrink-0">
                        <button type="button" 
                                onclick="window.print()"
                                class="px-4 py-2.5 bg-white border border-[#CBD5E1] hover:bg-slate-50 text-[#1E293B] rounded-xl text-[13px] font-bold shadow-sm transition flex items-center gap-2 cursor-pointer">
                            <svg class="w-4 h-4 text-[#8F0A0D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                            </svg>
                            <span>Cetak Laporan</span>
                        </button>
                    </div>
                </div>

                <!-- 4 KPI REKAP -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 pt-4">
                    <div class="p-3.5 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0]">
                        <span class="text-[11.5px] font-semibold text-[#64748B]">Total Populasi Perangkat</span>
                        <div class="text-[20px] font-bold text-[#1E293B] mt-0.5">{{ number_format($metrics['total_items']) }} Unit</div>
                    </div>
                    <div class="p-3.5 rounded-xl bg-emerald-50/60 border border-emerald-100">
                        <span class="text-[11.5px] font-semibold text-emerald-800">Tersedia Siap Kirim</span>
                        <div class="text-[20px] font-bold text-emerald-700 mt-0.5">{{ number_format($metrics['ready_stock']) }} Unit</div>
                    </div>
                    <div class="p-3.5 rounded-xl bg-blue-50/60 border border-blue-100">
                        <span class="text-[11.5px] font-semibold text-blue-800">Cadangan Kritis (Spare)</span>
                        <div class="text-[20px] font-bold text-blue-700 mt-0.5">{{ number_format($metrics['backup_spare']) }} Unit</div>
                    </div>
                    <div class="p-3.5 rounded-xl bg-purple-50/60 border border-purple-100">
                        <span class="text-[11.5px] font-semibold text-purple-800">Sedang Uji Coba (POC)</span>
                        <div class="text-[20px] font-bold text-purple-700 mt-0.5">{{ number_format($metrics['active_poc']) }} Unit</div>
                    </div>
                </div>
            </div>

            <!-- RINGKASAN KATEGORI & STATUS -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 anim-fade-up">
                <!-- Breakdown Kategori -->
                <div class="ipnet-card p-5">
                    <h3 class="text-[14.5px] font-bold text-[#1E293B] pb-3 mb-3 border-b border-[#E2E8F0]">
                        Rekapitulasi Kategori Aset
                    </h3>
                    <div class="space-y-3">
                        @foreach($byCategory as $c)
                            <div class="flex items-center justify-between p-3 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0]">
                                <span class="text-[13px] font-bold text-[#1E293B]">{{ $c->category }}</span>
                                <span class="text-[12.5px] font-bold text-[#8F0A0D]">{{ $c->total }} Unit</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Breakdown Status Operasional -->
                <div class="ipnet-card p-5">
                    <h3 class="text-[14.5px] font-bold text-[#1E293B] pb-3 mb-3 border-b border-[#E2E8F0]">
                        Rekapitulasi Status Operasional
                    </h3>
                    <div class="space-y-3">
                        @foreach($byStatus as $s)
                            <div class="flex items-center justify-between p-3 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0]">
                                <span class="text-[13px] font-bold text-[#1E293B]">{{ $s->status }}</span>
                                <span class="text-[12.5px] font-bold text-[#475569]">{{ $s->total }} Unit</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- TABEL AUDIT 50 DATA TERAKHIR -->
            <div class="ipnet-card p-5 sm:p-6 anim-fade-up">
                <div class="pb-3 mb-3 border-b border-[#E2E8F0]">
                    <h3 class="text-[15px] font-bold text-[#1E293B]">Lampiran Rincian Perangkat (Audit Trail)</h3>
                    <p class="text-[12px] text-[#64748B]">Menampilkan hingga 50 perangkat terdaftar dalam sistem logistik.</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-[12px]">
                        <thead>
                            <tr class="border-b border-[#E2E8F0] bg-[#F8FAFC]">
                                <th class="py-2.5 px-3 font-bold text-[#475569]">Kode</th>
                                <th class="py-2.5 px-3 font-bold text-[#475569]">Nama Perangkat</th>
                                <th class="py-2.5 px-3 font-bold text-[#475569]">Serial Number</th>
                                <th class="py-2.5 px-3 font-bold text-[#475569]">Jenis</th>
                                <th class="py-2.5 px-3 font-bold text-[#475569]">Kategori</th>
                                <th class="py-2.5 px-3 font-bold text-[#475569]">Lokasi Fisik</th>
                                <th class="py-2.5 px-3 font-bold text-[#475569]">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#E2E8F0]">
                            @foreach($items as $i)
                                <tr class="hover:bg-[#F8FAFC]">
                                    <td class="py-2.5 px-3 font-mono font-bold text-[#1E293B]">{{ $i->item_code }}</td>
                                    <td class="py-2.5 px-3 font-semibold text-[#1E293B]">{{ $i->name }}</td>
                                    <td class="py-2.5 px-3 font-mono text-[#475569]">{{ $i->serial_number ?: '-' }}</td>
                                    <td class="py-2.5 px-3">{{ $i->item_type }}</td>
                                    <td class="py-2.5 px-3">{{ $i->category }}</td>
                                    <td class="py-2.5 px-3">{{ $i->location_type }}</td>
                                    <td class="py-2.5 px-3 font-semibold">{{ $i->status }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
