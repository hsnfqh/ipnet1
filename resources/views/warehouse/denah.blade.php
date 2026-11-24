@extends('layouts.app')

@section('title', 'Denah Gudang & Tata Letak Rak - Warehouse IPNET')

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
    .ipnet-card:hover {
        border-color: #CBD5E1;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
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
    .anim-delay-1 { animation-delay: 0.05s !important; }
    .anim-delay-2 { animation-delay: 0.10s !important; }
    .anim-delay-3 { animation-delay: 0.15s !important; }
</style>
@endpush

@section('content')
<div class="flex h-screen overflow-hidden bg-[#F8FAFC] font-sans" x-data="{
    zoomLevel: 100,
    modalPreview: false,
    selectedRack: null,
    zoomIn() { if (this.zoomLevel < 180) this.zoomLevel += 15; },
    zoomOut() { if (this.zoomLevel > 60) this.zoomLevel -= 15; },
    resetZoom() { this.zoomLevel = 100; }
}">
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Denah Gudang'])
        
        <div class="p-4 sm:p-6 lg:p-7 space-y-6 max-w-[1600px] mx-auto">
            
            <!-- HEADER SECTION -->
            <div class="ipnet-card p-5 sm:p-6 anim-fade-up anim-delay-1">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 pb-4 mb-4 border-b border-[#E2E8F0]">
                    <div>
                        <p class="text-[#8F0A0D] text-[12px] font-semibold inline-flex items-center uppercase tracking-wider">
                            <span class="ipnet-badge-dot"></span> TATA LETAK &amp; STRUKTUR FISIK GUDANG
                        </p>
                        <h2 class="text-[20px] font-bold text-[#1E293B] tracking-tight mt-0.5">
                            Denah &amp; Zonasi Rak Gudang Utama (Warehouse Floor Plan)
                        </h2>
                        <p class="text-[13px] text-[#64748B] mt-0.5">
                            Peta denah resmi penempatan fisik rak (Rack 1 s/d 7), kardus susun, meja admin, alokasi AM/PIC kepemilikan barang, serta jalur lorong operasional (60cm - 90cm).
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                        <button type="button" 
                                @click="modalPreview = true" 
                                class="py-2.5 px-4 rounded-xl bg-white text-[#334155] border border-[#CBD5E1] font-bold text-xs hover:bg-[#F1F5F9] transition cursor-pointer flex items-center gap-2 shadow-xs">
                            <svg class="w-4 h-4 text-[#8F0A0D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 8V4m0 0h4M4 4l5 5m11-5h-4m4 0v4m0-4l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                            </svg>
                            <span>Layar Penuh (Full View)</span>
                        </button>

                        <a href="{{ route('warehouse.products.index') }}" 
                           class="btn-ipnet-gradient px-4 py-2.5 rounded-xl font-semibold text-[13px] shadow-sm flex items-center gap-2 cursor-pointer transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                            <span>Katalog Produk &amp; Perangkat</span>
                        </a>
                    </div>
                </div>

                <!-- 4 KPI SPESIFIKASI GUDANG -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3.5 pt-1">
                    <div class="p-3.5 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0]">
                        <p class="text-[11px] font-medium text-[#64748B] uppercase tracking-wider">Dimensi Ruang</p>
                        <h4 class="text-lg font-bold text-[#1E293B] mt-0.5">7.92 M &times; 2.95 M</h4>
                        <p class="text-[11px] text-[#94A3B8] mt-0.5">Area 2.30m (Depan) + 5.62m (Rak)</p>
                    </div>

                    <div class="p-3.5 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0]">
                        <p class="text-[11px] font-medium text-[#64748B] uppercase tracking-wider">Kapasitas Rak</p>
                        <h4 class="text-lg font-bold text-[#1E293B] mt-0.5">7 Rak Utama</h4>
                        <p class="text-[11px] text-[#94A3B8] mt-0.5">Rack 1 s/d Rack 7 Tersekat</p>
                    </div>

                    <div class="p-3.5 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0]">
                        <p class="text-[11px] font-medium text-[#64748B] uppercase tracking-wider">Zona Kardus Susun</p>
                        <h4 class="text-lg font-bold text-[#1E293B] mt-0.5">3 Zona Kardus</h4>
                        <p class="text-[11px] text-[#94A3B8] mt-0.5">Pintu Masuk, Dinding, &amp; Depan</p>
                    </div>

                    <div class="p-3.5 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0]">
                        <p class="text-[11px] font-medium text-[#64748B] uppercase tracking-wider">Standar Lorong</p>
                        <h4 class="text-lg font-bold text-[#1E293B] mt-0.5">60 cm s/d 90 cm</h4>
                        <p class="text-[11px] text-[#94A3B8] mt-0.5">Akses mobilitas &amp; keselamatan</p>
                    </div>
                </div>
            </div>

            <!-- DENAH BLUEPRINT VIEWER CARD -->
            <div class="ipnet-card p-5 sm:p-6 anim-fade-up anim-delay-2">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-3.5 border-b border-[#E2E8F0] mb-4">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-red-50 text-[#8F0A0D] flex items-center justify-center font-bold text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-[15px] font-bold text-[#1E293B]">Cetak Biru Denah Fisik (Warehouse Floor Map)</h3>
                            <p class="text-[12px] text-[#64748B]">Perhatikan posisi lorong, pintu masuk gudang, dan pembagian warna penanda rak</p>
                        </div>
                    </div>

                    <!-- Zoom Controls -->
                    <div class="flex items-center gap-1.5 self-start sm:self-auto bg-[#F8FAFC] border border-[#CBD5E1] p-1 rounded-xl">
                        <button type="button" @click="zoomOut()" class="p-1.5 hover:bg-white rounded-lg text-slate-600 transition" title="Perkecil">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                        </button>
                        <span class="text-xs font-mono font-bold px-2 text-slate-700 min-w-[50px] text-center" x-text="zoomLevel + '%'"></span>
                        <button type="button" @click="zoomIn()" class="p-1.5 hover:bg-white rounded-lg text-slate-600 transition" title="Perbesar">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        </button>
                        <button type="button" @click="resetZoom()" class="px-2 py-1 text-[11px] font-semibold text-[#8F0A0D] hover:bg-white rounded-lg transition" title="Reset Zoom">
                            Reset
                        </button>
                    </div>
                </div>

                <!-- Gambar Denah Container -->
                <div class="relative w-full overflow-auto bg-[#0F172A]/[0.02] border border-[#E2E8F0] rounded-2xl p-4 sm:p-6 flex items-center justify-center min-h-[420px]">
                    <div class="transition-transform duration-200 ease-out origin-center inline-block max-w-full"
                         :style="'transform: scale(' + (zoomLevel / 100) + ')'">
                        <img src="{{ asset('images/warehouse/denah_warehouse.jpg') }}" 
                             alt="Denah Warehouse IPNET" 
                             class="max-w-full h-auto rounded-xl shadow-md border border-slate-200 select-none object-contain cursor-zoom-in"
                             @click="modalPreview = true">
                    </div>
                </div>

                <!-- Keterangan Cepat Bawah Denah -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 pt-4 mt-2">
                    <div class="flex items-start gap-2.5 p-3 rounded-xl bg-amber-50/70 border border-amber-200/60">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500 mt-1 shrink-0"></span>
                        <div class="text-[12px]">
                            <p class="font-bold text-amber-900">Alokasi AM / Sales Raiza</p>
                            <p class="text-amber-700 text-[11.5px] mt-0.5">Menempati <strong>Rack 3, Rack 4, Rack 7</strong>, dan Kardus Susun Pintu Masuk.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-2.5 p-3 rounded-xl bg-blue-50/70 border border-blue-200/60">
                        <span class="w-2.5 h-2.5 rounded-full bg-blue-500 mt-1 shrink-0"></span>
                        <div class="text-[12px]">
                            <p class="font-bold text-blue-900">Alokasi Telco &amp; Anton</p>
                            <p class="text-blue-700 text-[11.5px] mt-0.5">Menempati <strong>Rack 2, Rack 5</strong>, sebagian Rack 3 &amp; Kardus Susun.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-2.5 p-3 rounded-xl bg-emerald-50/70 border border-emerald-200/60">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 mt-1 shrink-0"></span>
                        <div class="text-[12px]">
                            <p class="font-bold text-emerald-900">Alokasi Anita, Widodo, &amp; IPNET 1-2</p>
                            <p class="text-emerald-700 text-[11.5px] mt-0.5">Menempati <strong>Rack 1</strong> (Anita), <strong>Rack 6</strong> (Widodo), serta Kardus Dinding.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- DETAIL ZONASI RAK (RACK 1 S/D 7) -->
            <div class="ipnet-card p-5 sm:p-6 anim-fade-up anim-delay-3">
                <div class="flex items-center justify-between pb-3.5 border-b border-[#E2E8F0] mb-4">
                    <div>
                        <h3 class="text-[16px] font-bold text-[#1E293B]">Rincian Alokasi Fisik Rak Inventaris (Rack 1 - 7)</h3>
                        <p class="text-[12px] text-[#64748B]">Daftar alokasi PIC penanggung jawab per rak sesuai denah kerja gudang</p>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-[11.5px] font-semibold bg-red-50 text-[#8F0A0D] border border-red-200/60">
                        7 Blok Rak
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                    @foreach($racks as $rack)
                        <div class="p-4 rounded-xl border border-[#E2E8F0] bg-white hover:border-[#8F0A0D]/50 hover:shadow-sm transition flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-[12px] font-bold font-mono px-2 py-0.5 rounded bg-slate-100 text-slate-800 border border-slate-200">
                                        {{ $rack['code'] }}
                                    </span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-red-50 text-[#8F0A0D] border border-red-200/70">
                                        PIC: {{ $rack['pic'] }}
                                    </span>
                                </div>
                                <h4 class="font-bold text-[#1E293B] text-[14px]">{{ $rack['name'] }}</h4>
                                <p class="text-[11.5px] text-blue-600 font-medium mt-0.5">{{ $rack['category'] }}</p>
                                <p class="text-[11px] text-slate-400 mt-1 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                    <span>{{ $rack['position'] }}</span>
                                </p>
                                <p class="text-[12px] text-slate-600 mt-2 line-clamp-3">
                                    {{ $rack['description'] }}
                                </p>
                            </div>

                            <div class="pt-3 mt-3 border-t border-slate-100 flex items-center justify-between">
                                <a href="{{ route('warehouse.products.index', ['search' => explode(' ', $rack['pic'])[0]]) }}" 
                                   class="text-[11.5px] font-semibold text-[#8F0A0D] hover:underline flex items-center gap-1">
                                    <span>Cari Barang di Rak Ini</span>
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </div>
                        </div>
                    @endforeach

                    <!-- CARD ZONA KARDUS SUSUN & MEJA ADMIN -->
                    <div class="p-4 rounded-xl border border-dashed border-[#CBD5E1] bg-[#F8FAFC] flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Zona Kardus</span>
                                <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-slate-200 text-slate-700">3 Area</span>
                            </div>
                            <h4 class="font-bold text-[#1E293B] text-[13.5px]">Kardus Susun &amp; Packaging</h4>
                            <ul class="text-[11.5px] text-slate-600 space-y-1.5 mt-2">
                                <li class="flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-600 shrink-0"></span>
                                    <span>Pintu: <strong>Calista, Raiza, Telco</strong></span>
                                </li>
                                <li class="flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-600 shrink-0"></span>
                                    <span>Dinding: <strong>IPNET 1 &amp; IPNET 2</strong></span>
                                </li>
                                <li class="flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-600 shrink-0"></span>
                                    <span>Depan (2.30m): <strong>Buffer Box</strong></span>
                                </li>
                            </ul>
                        </div>
                        <div class="pt-3 mt-3 border-t border-slate-200/70 text-[11px] text-slate-400">
                            Meja Admin 1, 2, 3 di area depan pintu masuk
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- MODAL PREVIEW FULLSCREEN DENAH -->
    <div x-show="modalPreview" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/80 backdrop-blur-xs"
         @keydown.escape.window="modalPreview = false">
        <div class="relative bg-white rounded-2xl max-w-6xl w-full max-h-[92vh] flex flex-col overflow-hidden shadow-2xl animate-in fade-in zoom-in duration-150"
             @click.away="modalPreview = false">
            <div class="p-4 border-b border-slate-200 flex items-center justify-between bg-slate-50">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-[#8F0A0D]"></span>
                    <h3 class="font-bold text-slate-800 text-sm">Denah Warehouse Resmi IPNET (7.92 M &times; 2.95 M)</h3>
                </div>
                <button type="button" @click="modalPreview = false" class="p-1 text-slate-400 hover:text-slate-700 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-4 overflow-auto flex-1 flex items-center justify-center bg-slate-900/5">
                <img src="{{ asset('images/warehouse/denah_warehouse.jpg') }}" alt="Denah Fullscreen" class="max-w-full max-h-[75vh] object-contain rounded-lg shadow-sm">
            </div>
            <div class="p-3 bg-slate-50 border-t border-slate-200 flex items-center justify-between text-xs text-slate-500">
                <span>Alokasi: Anita (Rack 1), Anton/Telco (Rack 2), Raiza (Rack 3,4,7), Telco (Rack 5), Widodo (Rack 6)</span>
                <button type="button" @click="modalPreview = false" class="px-4 py-1.5 bg-slate-800 hover:bg-slate-900 text-white rounded-lg font-semibold">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection
