@extends('layouts.app')

@section('title', 'Delivery Order (Surat Jalan) - Warehouse IPNET')

@push('styles')
<style>
    [x-cloak] { display: none !important; }
    .ipnet-card {
        background-color: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 16px;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.03);
    }
    @media print {
        body * { visibility: hidden !important; }
        #print-area, #print-area * { visibility: visible !important; }
        #print-area {
            position: absolute !important;
            left: 0 !important;
            top: 0 !important;
            width: 100% !important;
            margin: 0 !important;
            padding: 28px !important;
            background: #FFFFFF !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
    }
    .tt-header-black {
        background-color: #000000 !important;
        color: #FFFFFF !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
    .tt-bg-gray {
        background-color: #F8FAFC !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
</style>
@endpush

@section('content')
<div class="flex h-screen overflow-hidden bg-[#F8FAFC] font-sans" x-data="doManager()">
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Delivery Order & Surat Jalan'])
        
        <div class="p-4 sm:p-6 lg:p-7 space-y-6 max-w-[1600px] mx-auto">
            
            <!-- HEADER -->
            <div class="ipnet-card p-5 sm:p-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#8F0A0D]"></span>
                            <h2 class="text-[20px] font-bold text-[#1E293B]">Delivery Order (DO) &amp; Surat Jalan Pengiriman</h2>
                        </div>
                        <p class="text-[13px] text-[#64748B] mt-0.5">
                            Penerbitan surat jalan resmi pengeluaran barang untuk implementasi proyek pelanggan, uji coba POC, maupun distribusi antar-gudang.
                        </p>
                    </div>

                    <div class="flex items-center gap-3">
                        <button type="button" 
                                @click="openCreateModal()"
                                class="btn-ipnet-gradient px-4 py-2.5 rounded-xl font-bold text-[13px] shadow-sm flex items-center gap-2 cursor-pointer transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                            </svg>
                            <span>Buat Surat Jalan (DO)</span>
                        </button>
                    </div>
                </div>

                <!-- FILTER ROW -->
                <form action="{{ route('warehouse.delivery_orders.index') }}" method="GET" class="mt-5 pt-4 border-t border-[#E2E8F0] grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    <div>
                        <label class="block text-[11px] font-semibold text-[#64748B] mb-1">Cari No DO / Penerima / Resi</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="DO-IPN-... atau PT ..." class="wms-input text-[12.5px]">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-[#64748B] mb-1">Status Pengiriman</label>
                        <select name="status" class="wms-input text-[12.5px]" onchange="this.form.submit()">
                            <option value="all">Semua Status</option>
                            <option value="Draft" {{ request('status') === 'Draft' ? 'selected' : '' }}>Draft</option>
                            <option value="Siap Dikirim" {{ request('status') === 'Siap Dikirim' ? 'selected' : '' }}>Siap Dikirim</option>
                            <option value="Dalam Pengiriman" {{ request('status') === 'Dalam Pengiriman' ? 'selected' : '' }}>Dalam Pengiriman</option>
                            <option value="Terkirim & Diterima" {{ request('status') === 'Terkirim & Diterima' ? 'selected' : '' }}>Terkirim &amp; Diterima</option>
                            <option value="Batal" {{ request('status') === 'Batal' ? 'selected' : '' }}>Batal</option>
                        </select>
                    </div>
                    <div class="flex items-end gap-2 col-span-1 sm:col-span-2">
                        <button type="submit" class="bg-[#1E293B] hover:bg-[#0F172A] text-white px-5 py-2.5 rounded-xl text-[12.5px] font-semibold transition">
                            Filter
                        </button>
                        @if(request()->anyFilled(['search', 'status']))
                            <a href="{{ route('warehouse.delivery_orders.index') }}" class="bg-slate-200 hover:bg-slate-300 text-slate-700 px-3.5 py-2.5 rounded-xl text-[12.5px] font-semibold transition">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- DO TABLE -->
            <div class="ipnet-card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-[13px] border-collapse">
                        <thead>
                            <tr class="bg-[#F8FAFC] border-b border-[#E2E8F0] text-[#64748B] text-[11.5px] uppercase font-bold tracking-wider">
                                <th class="py-3.5 px-4">Nomor &amp; Tanggal DO</th>
                                <th class="py-3.5 px-4">Penerima &amp; Perusahaan</th>
                                <th class="py-3.5 px-4">Gudang Asal &amp; Referensi</th>
                                <th class="py-3.5 px-4">Kurir / Ekspedisi</th>
                                <th class="py-3.5 px-4 text-center">Unit Fisik</th>
                                <th class="py-3.5 px-4 text-center">Status</th>
                                <th class="py-3.5 px-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#F1F5F9]">
                            @forelse($deliveryOrders as $do)
                                <tr class="hover:bg-[#F8FAFC]/80 transition">
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-[#1E293B] text-[14px]">{{ $do->do_number }}</div>
                                        <div class="text-[11px] text-[#64748B] mt-0.5">
                                            Tanggal: <strong>{{ $do->do_date->format('d M Y') }}</strong>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-[#1E293B]">{{ $do->recipient_name }}</div>
                                        <div class="text-[11px] text-[#8F0A0D] font-medium">{{ $do->recipient_company ?: '-' }}</div>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <div class="font-medium text-[#1E293B]">{{ $do->warehouse_name ?: 'Gudang Utama' }}</div>
                                        <div class="text-[11px] text-[#64748B]">Ref: {{ $do->reference_no ?: '-' }}</div>
                                    </td>
                                    <td class="py-3.5 px-4 text-[12px]">
                                        <div class="font-semibold text-slate-800">{{ $do->courier_name ?: 'Driver Internal' }}</div>
                                        <div class="text-[11px] text-slate-500">
                                            {{ $do->vehicle_no ?: '' }} {{ $do->tracking_no ? '• Resi: ' . $do->tracking_no : '' }}
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4 text-center font-bold text-slate-800">
                                        {{ $do->items->sum('quantity') }} Unit
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        @php
                                            $doBadge = match($do->status) {
                                                'Terkirim & Diterima' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                                'Dalam Pengiriman' => 'bg-purple-50 text-purple-700 border-purple-200',
                                                'Siap Dikirim' => 'bg-blue-50 text-blue-700 border-blue-200',
                                                'Batal' => 'bg-red-50 text-red-700 border-red-200',
                                                default => 'bg-slate-100 text-slate-700 border-slate-200'
                                            };
                                        @endphp
                                        <span class="px-2.5 py-1 text-[11px] font-bold rounded-md border {{ $doBadge }}">
                                            {{ $do->status }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        <div class="inline-flex items-center gap-1.5">
                                            <button type="button" 
                                                    @click="viewDetail({{ json_encode($do) }})"
                                                    class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-[11.5px] flex items-center gap-1.5 transition" title="Lihat & Cetak Format Resmi Tanda Terima">
                                                <svg class="w-3.5 h-3.5 text-[#8F0A0D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                                </svg>
                                                <span>Cetak Tanda Terima</span>
                                            </button>
                                            @if($do->status !== 'Terkirim & Diterima')
                                                <form action="{{ route('warehouse.delivery_orders.status', $do->id) }}" method="POST" class="inline" onsubmit="return confirm('Konfirmasi bahwa barang sudah terkirim & diterima pelanggan?')">
                                                    @csrf
                                                    <input type="hidden" name="status" value="Terkirim & Diterima">
                                                    <button type="submit" class="p-1.5 rounded-lg text-emerald-600 hover:bg-emerald-50 transition" title="Tandai Terkirim">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                                        </svg>
                                                    </button>
                                                </form>
                                            @endif
                                            <form action="{{ route('warehouse.delivery_orders.destroy', $do->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus Surat Jalan ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 rounded-lg text-red-600 hover:bg-red-50 transition" title="Hapus DO">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-12 text-center text-[#94A3B8]">
                                        Belum ada Delivery Order / Surat Jalan tercatat.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($deliveryOrders->hasPages())
                    <div class="p-4 border-t border-[#E2E8F0]">
                        {{ $deliveryOrders->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>

    <!-- MODAL CREATE DELIVERY ORDER (Gambar 2 Style) -->
    <template x-teleport="body">
        <div x-show="showCreateModal" 
             x-cloak 
             class="fixed inset-0 bg-[#0F172A]/60 z-[99999] flex items-center justify-center p-3 sm:p-5 backdrop-blur-xs">
            <div @click.away="showCreateModal = false" 
                 class="bg-white rounded-2xl w-[860px] max-w-full max-h-[92vh] flex flex-col text-left shadow-[0_20px_60px_rgba(15,23,42,0.25)] border border-[#E2E8F0] overflow-hidden">
                
                <!-- Fixed Header -->
                <div class="px-6 py-4 bg-[#F8FAFC] border-b border-[#E2E8F0] shrink-0 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-purple-50 text-purple-700 border border-purple-100 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-800">Terbitkan Surat Jalan (Delivery Order) Baru</h3>
                            <p class="text-[11px] text-slate-500">Pengiriman dan distribusi unit perangkat jaringan ke klien / lokasi instalasi</p>
                        </div>
                    </div>
                    <button type="button" @click="showCreateModal = false" class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-200/60 flex items-center justify-center text-lg transition">
                        &times;
                    </button>
                </div>

                <!-- Scrollable Body -->
                <form id="createDoForm" action="{{ route('warehouse.delivery_orders.store') }}" method="POST" class="flex flex-col flex-1 min-h-0">
                    @csrf
                    <div class="p-6 overflow-y-auto space-y-5 text-xs flex-1 min-h-0">

                        <!-- SECTION 1 -->
                        <div class="space-y-3">
                            <div class="flex items-center gap-2 text-xs font-bold text-slate-800 uppercase tracking-wider">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#8F0A0D]"></span>
                                <span>1. INFORMASI SURAT JALAN &amp; KEPERLUAN ALOKASI</span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Nomor DO (Opsional)</label>
                                    <input type="text" name="do_number" class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-[#8F0A0D] text-slate-800 bg-white" placeholder="Otomatis jika kosong">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Tanggal Pengiriman <span class="text-red-500">*</span></label>
                                    <input type="date" name="do_date" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-[#8F0A0D] text-slate-800 bg-white">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Gudang Asal <span class="text-red-500">*</span></label>
                                    <select name="warehouse_id" required class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-[#8F0A0D] text-slate-800 bg-white">
                                        @foreach($warehouses as $wh)
                                            <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">No. Referensi (PO / Proyek)</label>
                                    <input type="text" name="reference_no" class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-[#8F0A0D] text-slate-800 bg-white" placeholder="CTR-BRI-2026...">
                                </div>
                            </div>

                            <!-- Keperluan Alokasi Pengeluaran -->
                            <div class="pt-2">
                                <label class="block text-[11px] font-semibold text-slate-700 mb-1.5">Kategori / Tujuan Pengeluaran Barang <span class="text-red-500">*</span></label>
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                                    <label class="p-2.5 rounded-xl border cursor-pointer transition flex items-center gap-2"
                                           :class="allocationType === 'proyek' ? 'border-[#8F0A0D] bg-red-50/50 text-[#8F0A0D] font-bold' : 'border-slate-200 bg-white text-slate-700'">
                                        <input type="radio" name="allocation_type" value="proyek" x-model="allocationType" class="text-[#8F0A0D]">
                                        <span class="text-xs">Proyek Klien</span>
                                    </label>
                                    <label class="p-2.5 rounded-xl border cursor-pointer transition flex items-center gap-2"
                                           :class="allocationType === 'poc' ? 'border-[#8F0A0D] bg-red-50/50 text-[#8F0A0D] font-bold' : 'border-slate-200 bg-white text-slate-700'">
                                        <input type="radio" name="allocation_type" value="poc" x-model="allocationType" class="text-[#8F0A0D]">
                                        <span class="text-xs">Peminjaman POC</span>
                                    </label>
                                    <label class="p-2.5 rounded-xl border cursor-pointer transition flex items-center gap-2"
                                           :class="allocationType === 'pinjaman_ipnet' ? 'border-[#8F0A0D] bg-red-50/50 text-[#8F0A0D] font-bold' : 'border-slate-200 bg-white text-slate-700'">
                                        <input type="radio" name="allocation_type" value="pinjaman_ipnet" x-model="allocationType" class="text-[#8F0A0D]">
                                        <span class="text-xs">Pinjaman Internal IPNET</span>
                                    </label>
                                    <label class="p-2.5 rounded-xl border cursor-pointer transition flex items-center gap-2"
                                           :class="allocationType === 'pinjaman_disti' ? 'border-[#8F0A0D] bg-red-50/50 text-[#8F0A0D] font-bold' : 'border-slate-200 bg-white text-slate-700'">
                                        <input type="radio" name="allocation_type" value="pinjaman_disti" x-model="allocationType" class="text-[#8F0A0D]">
                                        <span class="text-xs">Pinjaman Disti</span>
                                    </label>
                                </div>

                                <!-- Sub-Pilihan Spesifik Unit IPNET (Bisa pilih IPNET 1, 2, 3, 4, atau 5) -->
                                <div x-show="allocationType === 'pinjaman_ipnet'" x-transition class="mt-3 p-3.5 rounded-xl bg-red-50/60 border border-red-200/80">
                                    <div class="flex items-center justify-between mb-2">
                                        <label class="block text-[11px] font-bold text-[#8F0A0D] uppercase tracking-wider">
                                            Pilih Unit Kerja IPNET Peminjam <span class="text-red-500">*</span>
                                        </label>
                                        <span class="text-[10.5px] text-slate-500 font-medium">Pilih spesifik unit peminjam (IPNET 1 s/d 5)</span>
                                    </div>
                                    <div class="grid grid-cols-2 sm:grid-cols-5 gap-2">
                                        <template x-for="u in ['IPNET 1', 'IPNET 2', 'IPNET 3', 'IPNET 4', 'IPNET 5']" :key="u">
                                            <label class="p-2 rounded-lg border text-center cursor-pointer transition flex items-center justify-center gap-1.5"
                                                   :class="selectedIpnetUnit === u ? 'bg-[#8F0A0D] text-white font-bold border-[#8F0A0D] shadow-xs' : 'bg-white text-slate-700 border-slate-200 hover:border-slate-300 font-medium'">
                                                <input type="radio" name="ipnet_work_unit" :value="u" x-model="selectedIpnetUnit" @change="onIpnetUnitChange(u)" class="hidden">
                                                <span class="text-xs" x-text="u"></span>
                                            </label>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- SECTION 2 -->
                        <div class="space-y-3 pt-2 border-t border-slate-100">
                            <div class="flex items-center gap-2 text-xs font-bold text-slate-800 uppercase tracking-wider">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#8F0A0D]"></span>
                                <span>2. DATA PENERIMA &amp; ALAMAT TUJUAN</span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">
                                        <span x-text="allocationType === 'pinjaman_ipnet' ? 'Nama PIC / Engineer Peminjam' : 'Nama Penerima (PIC Klien)'"></span>
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="recipient_name" required class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-[#8F0A0D] text-slate-800 bg-white" placeholder="e.g. Budi Hartono">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">
                                        <span x-text="allocationType === 'pinjaman_ipnet' ? 'Unit Kerja IPNET Peminjam' : 'Nama Instansi / Perusahaan / Unit'"></span>
                                    </label>
                                    <input type="text" name="recipient_company" x-model="recipientCompany" class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-[#8F0A0D] text-slate-800 bg-white" placeholder="e.g. PT Bank Rakyat Indonesia Tbk">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Nomor Telp / WA Penerima</label>
                                    <input type="text" name="recipient_phone" class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-[#8F0A0D] text-slate-800 bg-white" placeholder="e.g. 0812-8765-4321">
                                </div>
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-700 mb-1">Alamat Lengkap Pengiriman <span class="text-red-500">*</span></label>
                                <textarea name="delivery_address" rows="2" required class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-[#8F0A0D] text-slate-800 bg-white text-xs" placeholder="Gedung, lantai, nomor ruangan, jalan, kota..."></textarea>
                            </div>
                        </div>

                        <!-- SECTION 3 -->
                        <div class="space-y-3 pt-2 border-t border-slate-100">
                            <div class="flex items-center gap-2 text-xs font-bold text-slate-800 uppercase tracking-wider">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#8F0A0D]"></span>
                                <span>3. DETAIL EKSPEDISI &amp; STATUS PENGIRIMAN</span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Kurir / Driver</label>
                                    <input type="text" name="courier_name" class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-[#8F0A0D] text-slate-800 bg-white" placeholder="e.g. Supriyadi (Internal Driver)">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Nomor Polisi Kendaraan</label>
                                    <input type="text" name="vehicle_no" class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-[#8F0A0D] text-slate-800 bg-white" placeholder="e.g. B 9182 JKL">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Status Pengiriman</label>
                                    <select name="status" class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-[#8F0A0D] text-slate-800 bg-white">
                                        <option value="Draft">Draft</option>
                                        <option value="Siap Dikirim">Siap Dikirim</option>
                                        <option value="Dalam Pengiriman" selected>Dalam Pengiriman (Kurangi Stok)</option>
                                        <option value="Terkirim & Diterima">Terkirim &amp; Diterima</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- SECTION 4 -->
                        <div class="space-y-3 pt-2 border-t border-slate-100">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2 text-xs font-bold text-slate-800 uppercase tracking-wider">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#8F0A0D]"></span>
                                    <span>4. PILIH PERANGKAT DARI RAK GUDANG</span>
                                </div>
                                <button type="button" @click="addItemRow()" class="text-xs font-bold text-[#8F0A0D] hover:underline flex items-center gap-1">
                                    + Tambah Baris Manual
                                </button>
                            </div>

                            <!-- SCANNER BARCODE / SERIAL NUMBER BOX -->
                            <div class="p-3 bg-red-50/50 rounded-xl border border-red-200/80 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-lg bg-red-100 text-[#8F0A0D] flex items-center justify-center shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <span class="text-xs font-bold text-[#8F0A0D] block">Scan Barcode / Serial Number Perangkat</span>
                                        <span class="text-[10.5px] text-slate-500">Tembak barcode S/N di kardus/fisik, perangkat otomatis langsung masuk ke daftar</span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 sm:w-[320px]">
                                    <input type="text" 
                                           x-ref="scanInput"
                                           x-model="scanQuery" 
                                           @keydown.enter.prevent="handleBarcodeScan()"
                                           placeholder="Tembak scanner S/N di sini..." 
                                           class="w-full px-3 py-1.5 border border-red-300 rounded-lg outline-none focus:ring-2 focus:ring-red-400 bg-white text-xs font-mono font-bold text-slate-800">
                                    <button type="button" 
                                            @click="handleBarcodeScan()" 
                                            class="px-3.5 py-1.5 bg-[#8F0A0D] hover:bg-red-800 text-white font-bold text-xs rounded-lg transition shrink-0">
                                        Scan
                                    </button>
                                </div>
                            </div>

                            <div class="space-y-2.5">
                                <template x-for="(row, index) in itemRows" :key="index">
                                    <div class="grid grid-cols-12 gap-2.5 items-center bg-[#F8FAFC] p-3 rounded-xl border border-slate-200">
                                        <!-- Physical Item / Product Selection -->
                                        <div class="col-span-12 sm:col-span-6">
                                            <label class="block text-[10.5px] text-slate-600 font-semibold mb-1">Pilih Perangkat Fisik (Rak Gudang) *</label>
                                            <select :name="'items[' + index + '][warehouse_item_id]'" 
                                                    x-model="row.warehouse_item_id"
                                                    @change="onPhysicalItemSelect(index)"
                                                    class="w-full px-2.5 py-1.5 border border-slate-200 rounded-lg outline-none focus:border-[#8F0A0D] text-slate-800 bg-white text-xs">
                                                <option value="">-- Pilih dari Unit Fisik di Rak Gudang --</option>
                                                <template x-for="item in availableItemsList" :key="item.id">
                                                    <option :value="item.id" x-text="'[' + item.item_code + '] ' + item.name + ' • SN: ' + (item.serial_number || '-') + ' • ' + (item.rack || 'Gudang')"></option>
                                                </template>
                                                <option value="custom">-- Pilih dari Master Katalog / Manual --</option>
                                            </select>
                                            
                                            <!-- Fallback to catalog product if custom -->
                                            <div x-show="row.warehouse_item_id === 'custom'" class="mt-1.5 flex gap-2">
                                                <select :name="'items[' + index + '][product_id]'" 
                                                        x-model="row.product_id"
                                                        @change="onProductSelect(index)"
                                                        class="w-full px-2.5 py-1.5 border border-amber-300 rounded-lg outline-none focus:border-[#8F0A0D] text-slate-800 bg-amber-50/50 text-xs">
                                                    <option value="">-- Pilih dari Master Katalog --</option>
                                                    <template x-for="p in productsList" :key="p.id">
                                                        <option :value="p.id" x-text="p.name + (p.sku ? ' (' + p.sku + ')' : '')"></option>
                                                    </template>
                                                </select>
                                            </div>
                                            <input type="hidden" :name="'items[' + index + '][product_name]'" x-model="row.product_name">
                                        </div>

                                        <!-- Serial Number -->
                                        <div class="col-span-12 sm:col-span-3">
                                            <label class="block text-[10.5px] text-slate-600 font-semibold mb-1">Serial Number (S/N)</label>
                                            <input type="text" 
                                                   :name="'items[' + index + '][serial_number]'" 
                                                   x-model="row.serial_number" 
                                                   placeholder="FCW2143L0G7..." 
                                                   class="w-full px-2.5 py-1.5 border border-slate-200 rounded-lg outline-none focus:border-[#8F0A0D] text-slate-800 bg-white text-xs font-mono">
                                        </div>

                                        <!-- Quantity -->
                                        <div class="col-span-6 sm:col-span-2">
                                            <label class="block text-[10.5px] text-slate-600 font-semibold mb-1">Qty &amp; Satuan</label>
                                            <div class="flex items-center gap-1">
                                                <input type="number" 
                                                       :name="'items[' + index + '][quantity]'" 
                                                       x-model.number="row.quantity" 
                                                       min="1" 
                                                       required 
                                                       class="w-16 px-2 py-1.5 border border-slate-200 rounded-lg outline-none focus:border-[#8F0A0D] text-slate-800 bg-white text-xs font-bold text-center">
                                                <select :name="'items[' + index + '][unit]'" 
                                                        x-model="row.unit" 
                                                        required 
                                                        class="flex-1 px-1.5 py-1.5 border border-slate-200 rounded-lg outline-none focus:border-[#8F0A0D] text-slate-800 bg-white text-xs">
                                                    <option value="Unit">Unit</option>
                                                    <option value="Pcs">Pcs</option>
                                                    <option value="Box">Box</option>
                                                    <option value="Set">Set</option>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Delete row -->
                                        <div class="col-span-1 text-center pt-4">
                                            <button type="button" 
                                                    @click="removeItemRow(index)" 
                                                    class="w-7 h-7 rounded-lg text-red-500 hover:bg-red-50 hover:text-red-700 flex items-center justify-center text-base font-bold transition mx-auto" 
                                                    x-show="itemRows.length > 1">
                                                &times;
                                            </button>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- SECTION 5 -->
                        <div class="space-y-3 pt-2 border-t border-slate-100">
                            <div class="flex items-center gap-2 text-xs font-bold text-slate-800 uppercase tracking-wider">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#8F0A0D]"></span>
                                <span>5. CATATAN PENGIRIMAN &amp; KELENGKAPAN UNIT</span>
                            </div>
                            <div>
                                <textarea name="notes" rows="2" class="w-full px-3 py-2 border border-slate-200 rounded-xl outline-none focus:border-[#8F0A0D] text-slate-800 bg-white text-xs" placeholder="Kelengkapan kabel power, mounting ear, manual book, kondisi segel..."></textarea>
                            </div>
                        </div>

                    </div>

                    <!-- Fixed Footer -->
                    <div class="px-6 py-3.5 bg-[#F8FAFC] border-t border-[#E2E8F0] flex items-center justify-end gap-3 shrink-0">
                        <button type="button" @click="showCreateModal = false" class="px-4 py-2 rounded-xl bg-white border border-[#CBD5E1] text-[#334155] font-bold text-xs hover:bg-[#F1F5F9] transition">
                            Batal
                        </button>
                        <button type="submit" class="btn-ipnet-gradient py-2.5 px-5 rounded-xl font-bold text-xs text-white shadow-sm flex items-center gap-2 cursor-pointer transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                            </svg>
                            <span>Terbitkan Surat Jalan &amp; Alokasi</span>
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </template>

    <!-- MODAL DETAIL & CETAK SURAT JALAN DO (Gambar 2 Style) -->
    <template x-teleport="body">
        <div x-show="showDetailModal" 
             x-cloak 
             class="fixed inset-0 bg-[#0F172A]/60 z-[99999] flex items-center justify-center p-3 sm:p-5 backdrop-blur-xs">
            <div @click.away="showDetailModal = false" 
                 class="bg-white rounded-2xl w-[860px] max-w-[96vw] max-h-[92vh] flex flex-col text-left shadow-[0_20px_60px_rgba(15,23,42,0.25)] border border-[#E2E8F0] overflow-hidden">
                
                <!-- Fixed Header -->
                <div class="px-6 py-4 bg-[#F8FAFC] border-b border-[#E2E8F0] shrink-0 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-[#8F0A0D]/10 text-[#8F0A0D] border border-[#8F0A0D]/20 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                            </svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-sm font-bold text-slate-800" x-text="detailData.do_number"></h3>
                                <span class="text-[10.5px] font-bold px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200" x-text="detailData.status"></span>
                            </div>
                            <p class="text-[11px] text-slate-500">Preview Resmi Dokumen Tanda Terima Fisik (Standar IPNET)</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="window.print()" class="px-3.5 py-1.5 rounded-lg bg-[#8F0A0D] text-white font-bold text-xs hover:bg-[#72080a] flex items-center gap-1.5 shadow-sm transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                            </svg>
                            <span>Cetak Tanda Terima</span>
                        </button>
                        <button type="button" @click="showDetailModal = false" class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-200/60 flex items-center justify-center text-lg transition">
                            &times;
                        </button>
                    </div>
                </div>

                <!-- Printable Body (Format Resmi Tanda Terima PT IP Network Solusindo) -->
                <div class="p-8 overflow-y-auto space-y-4 text-xs flex-1 min-h-0 bg-white" id="print-area">
                    <!-- KOP SURAT RESMI IPNET & TITLE -->
                    <div class="flex items-start justify-between border-b-[1.5px] border-slate-900 pb-4 mb-4">
                        <div class="flex items-start gap-4">
                            <!-- Logo IPNET Lebih Besar & Jelas -->
                            <img src="{{ asset('images/ipnet.png') }}" alt="Logo IPNET" class="w-20 h-20 object-contain shrink-0">
                            <div class="leading-tight text-[11.5px] text-slate-800 space-y-0.5">
                                <h2 class="text-[15px] font-black text-slate-900 uppercase tracking-wide">PT. IP NETWORK SOLUSINDO</h2>
                                <p class="text-slate-700">Golden Centrum Complex</p>
                                <p class="text-slate-700">Jl. Majapahit 26P, Jakarta 10160</p>
                                <p class="text-slate-700">Ph : +6221 3511304, 3501106-07</p>
                                <p class="text-slate-700">Fax: +6221 3511306</p>
                                <p class="text-[#8F0A0D] font-bold text-[11px] pt-0.5">ipnetsolusindo.com</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <h1 class="text-3xl font-black text-slate-900 tracking-wider underline underline-offset-8">TANDA TERIMA</h1>
                            <div class="mt-3 text-[12px] font-mono text-slate-700">
                                No. DO : <span class="font-bold text-slate-900" x-text="detailData.do_number"></span>
                            </div>
                        </div>
                    </div>

                    <!-- META INFO GRID (Satu Box Terpadu dengan Garis Konsisten & Titik Dua Rapi) -->
                    <div class="border-[1.5px] border-slate-900 grid grid-cols-2 divide-x-[1.5px] divide-slate-900 text-[12px] bg-white mb-4">
                        <!-- Kolom Kiri: Tujuan / TO -->
                        <div class="p-3.5 space-y-2">
                            <div class="flex items-start">
                                <span class="w-20 font-bold text-slate-800 shrink-0">TO</span>
                                <span class="w-4 text-center font-bold text-slate-800 shrink-0">:</span>
                                <span class="font-bold text-slate-900 flex-1 leading-snug" x-text="detailData.recipient_company || detailData.recipient_name || '-'"></span>
                            </div>
                            <div class="flex items-start">
                                <span class="w-20 font-semibold text-slate-700 shrink-0">Up</span>
                                <span class="w-4 text-center font-bold text-slate-700 shrink-0">:</span>
                                <span class="text-slate-800 font-medium flex-1 leading-snug" x-text="detailData.recipient_name || '-'"></span>
                            </div>
                            <div class="flex items-start">
                                <span class="w-20 font-semibold text-slate-700 shrink-0">Phone</span>
                                <span class="w-4 text-center font-bold text-slate-700 shrink-0">:</span>
                                <span class="text-slate-900 font-mono font-medium flex-1" x-text="detailData.recipient_phone || '-'"></span>
                            </div>
                            <div class="flex items-start" x-show="detailData.delivery_address">
                                <span class="w-20 font-semibold text-slate-700 shrink-0">Alamat</span>
                                <span class="w-4 text-center font-bold text-slate-700 shrink-0">:</span>
                                <span class="text-slate-800 text-[11.5px] leading-snug flex-1" x-text="detailData.delivery_address"></span>
                            </div>
                        </div>

                        <!-- Kolom Kanan: Detail Pengiriman / Ref -->
                        <div class="p-3.5 space-y-2">
                            <div class="flex items-start">
                                <span class="w-24 font-semibold text-slate-700 shrink-0">Keterangan</span>
                                <span class="w-4 text-center font-bold text-slate-700 shrink-0">:</span>
                                <span class="font-bold text-slate-900 flex-1 leading-snug" x-text="detailData.recipient_company && detailData.recipient_company.startsWith('IPNET ') ? 'Alokasi Internal (' + detailData.recipient_company + ')' : 'Pengiriman Barang'"></span>
                            </div>
                            <div class="flex items-start">
                                <span class="w-24 font-semibold text-slate-700 shrink-0">PO Nomor</span>
                                <span class="w-4 text-center font-bold text-slate-700 shrink-0">:</span>
                                <span class="font-mono font-bold text-slate-900 flex-1" x-text="detailData.reference_no || '-'"></span>
                            </div>
                            <div class="flex items-start">
                                <span class="w-24 font-semibold text-slate-700 shrink-0">Pengiriman</span>
                                <span class="w-4 text-center font-bold text-slate-700 shrink-0">:</span>
                                <span class="font-medium text-slate-900 flex-1" x-text="detailData.do_date ? formatDateIndo(detailData.do_date) : '-'"></span>
                            </div>
                            <div class="flex items-start">
                                <span class="w-24 font-semibold text-slate-700 shrink-0">Perangkat</span>
                                <span class="w-4 text-center font-bold text-slate-700 shrink-0">:</span>
                                <span class="text-slate-800 font-medium flex-1" x-text="detailData.vehicle_no || detailData.courier_name || 'Hardware / Network'"></span>
                            </div>
                            <div class="flex items-start">
                                <span class="w-24 font-semibold text-slate-700 shrink-0">Page</span>
                                <span class="w-4 text-center font-bold text-slate-700 shrink-0">:</span>
                                <span class="font-bold text-slate-900 flex-1">1 OF 1</span>
                            </div>
                        </div>
                    </div>

                    <!-- Items Table Sesuai Template Excel (Border Konsisten & Header Hitam Pekat) -->
                    <div class="border-[1.5px] border-slate-900 overflow-hidden mb-4">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="tt-header-black text-[11px] font-black uppercase tracking-wider">
                                    <th class="py-2.5 px-3 text-center border-r-[1.5px] border-slate-900 w-12 text-white">NO.</th>
                                    <th class="py-2.5 px-3 border-r-[1.5px] border-slate-900 text-white">KODE BARANG</th>
                                    <th class="py-2.5 px-3 border-r-[1.5px] border-slate-900 text-white">URAIAN BARANG</th>
                                    <th class="py-2.5 px-3 text-center border-r-[1.5px] border-slate-900 w-28 text-white">QUANTITY</th>
                                    <th class="py-2.5 px-3 text-white w-48">SERIAL NUMBER</th>
                                </tr>
                            </thead>
                            <tbody class="text-slate-900 text-[12px]">
                                <template x-for="(item, idx) in (detailData.items || [])" :key="item.id || idx">
                                    <tr class="border-t-[1.5px] border-slate-900 hover:bg-slate-50/50">
                                        <td class="py-3 px-3 text-center font-bold border-r-[1.5px] border-slate-900" x-text="idx + 1"></td>
                                        <td class="py-3 px-3 font-bold text-slate-900 border-r-[1.5px] border-slate-900" x-text="item.product_name"></td>
                                        <td class="py-3 px-3 text-slate-700 border-r-[1.5px] border-slate-900" x-text="item.notes || '-'"></td>
                                        <td class="py-3 px-3 text-center font-bold border-r-[1.5px] border-slate-900" x-text="item.quantity + ' ' + (item.unit || 'Unit')"></td>
                                        <td class="py-3 px-3 font-mono font-bold text-slate-900" x-text="item.serial_number || '-'"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>

                    <!-- Kotak Tanda Tangan & Remarks (Lapang untuk Tanda Tangan & Cap Fisik) -->
                    <div class="border-[1.5px] border-slate-900 grid grid-cols-3 divide-x-[1.5px] divide-slate-900 text-xs bg-white">
                        <!-- Remarks Column -->
                        <div class="flex flex-col">
                            <div class="tt-bg-gray border-b-[1.5px] border-slate-900 py-2 px-3 font-bold text-slate-900 text-center uppercase tracking-wide text-[11.5px]">
                                Remarks
                            </div>
                            <div class="p-3.5 text-[11.5px] text-slate-700 leading-relaxed flex-1 min-h-[160px]" x-text="detailData.notes || 'Pengiriman switch core & patch cord untuk peremajaan LAN BRI Pusat.'"></div>
                        </div>

                        <!-- Prepare By Column -->
                        <div class="flex flex-col">
                            <div class="tt-bg-gray border-b-[1.5px] border-slate-900 py-2 px-3 font-bold text-slate-900 text-center uppercase tracking-wide text-[11.5px]">
                                Prepare By
                            </div>
                            <div class="p-3.5 flex-1 flex flex-col justify-end text-center min-h-[160px]">
                                <div class="flex-1 flex items-center justify-center text-slate-300 text-[11px] italic mb-2">
                                    ( Tanda Tangan &amp; Cap Gudang )
                                </div>
                                <div class="border-b-[1.5px] border-slate-900 w-4/5 mx-auto mb-1.5"></div>
                                <div class="font-bold text-[12px] text-slate-900 uppercase">Warehouse IPNET</div>
                                <div class="text-[10px] text-slate-500 mt-0.5">Petugas Gudang Pengirim</div>
                            </div>
                        </div>

                        <!-- Received By Column -->
                        <div class="flex flex-col">
                            <div class="tt-bg-gray border-b-[1.5px] border-slate-900 py-2 px-3 font-bold text-slate-900 text-center uppercase tracking-wide text-[11.5px]">
                                Received By
                            </div>
                            <div class="p-3.5 flex-1 flex flex-col justify-end text-center min-h-[160px]">
                                <div class="flex-1 flex items-center justify-center text-slate-300 text-[11px] italic mb-2">
                                    ( Tanda Tangan &amp; Cap Penerima )
                                </div>
                                <div class="border-b-[1.5px] border-slate-900 w-4/5 mx-auto mb-1.5"></div>
                                <div class="font-bold text-[12px] text-slate-900" x-text="detailData.recipient_name || 'Budi Hartono (IT Infrastructure)'"></div>
                                <div class="text-[10px] text-slate-500 mt-0.5" x-text="detailData.recipient_company || 'Penerima / Klien / Unit IPNET'"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Fixed Footer -->
                <div class="px-6 py-3.5 bg-[#F8FAFC] border-t border-[#E2E8F0] flex items-center justify-end gap-3 shrink-0">
                    <button type="button" @click="showDetailModal = false" class="px-4 py-2 rounded-xl bg-white border border-[#CBD5E1] text-[#334155] font-bold text-xs hover:bg-[#F1F5F9] transition">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>

<script>
function doManager() {
    return {
        showCreateModal: false,
        showDetailModal: false,
        allocationType: 'proyek',
        selectedIpnetUnit: 'IPNET 1',
        recipientCompany: '',
        detailData: {},
        productsList: {!! json_encode($products->map(function($p) { return ['id' => $p->id, 'name' => $p->name, 'sku' => $p->sku, 'unit' => $p->unit]; })) !!},
        availableItemsList: {!! json_encode($availableItems->map(function($i) { return ['id' => $i->id, 'item_code' => $i->item_code, 'name' => $i->name, 'serial_number' => $i->serial_number, 'rack' => $i->warehouse_rack, 'brand' => $i->brand]; })) !!},
        itemRows: [
            { warehouse_item_id: '', product_id: '', product_name: '', serial_number: '', quantity: 1, unit: 'Unit' }
        ],
        init() {
            this.$watch('allocationType', (val) => {
                if (val === 'pinjaman_ipnet') {
                    this.recipientCompany = this.selectedIpnetUnit;
                } else if (this.recipientCompany && this.recipientCompany.startsWith('IPNET ')) {
                    this.recipientCompany = '';
                }
            });
        },
        onIpnetUnitChange(u) {
            this.selectedIpnetUnit = u;
            if (this.allocationType === 'pinjaman_ipnet') {
                this.recipientCompany = u;
            }
        },
        scanQuery: '',
        openCreateModal() {
            this.showCreateModal = true;
            this.$nextTick(() => {
                if (this.$refs.scanInput) this.$refs.scanInput.focus();
            });
        },
        handleBarcodeScan() {
            const query = (this.scanQuery || '').trim();
            if (!query) return;

            const queryLower = query.toLowerCase();
            const matchedItem = this.availableItemsList.find(x => 
                (x.serial_number && x.serial_number.toLowerCase() === queryLower) ||
                (x.item_code && x.item_code.toLowerCase() === queryLower)
            );

            if (matchedItem) {
                const alreadyAdded = this.itemRows.some(row => row.warehouse_item_id == matchedItem.id);
                if (alreadyAdded) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Perangkat Sudah Ada',
                            text: `Unit ${matchedItem.name} (S/N: ${matchedItem.serial_number || '-'}) sudah masuk dalam daftar.`,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                    this.scanQuery = '';
                    return;
                }

                if (this.itemRows.length === 1 && !this.itemRows[0].warehouse_item_id && !this.itemRows[0].product_name) {
                    this.itemRows[0].warehouse_item_id = matchedItem.id;
                    this.itemRows[0].product_name = matchedItem.name;
                    this.itemRows[0].serial_number = matchedItem.serial_number || '';
                    this.itemRows[0].quantity = 1;
                    this.itemRows[0].unit = 'Unit';
                } else {
                    this.itemRows.push({
                        warehouse_item_id: matchedItem.id,
                        product_id: '',
                        product_name: matchedItem.name,
                        serial_number: matchedItem.serial_number || '',
                        quantity: 1,
                        unit: 'Unit'
                    });
                }

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Perangkat Masuk!',
                        html: `<strong>${matchedItem.name}</strong><br><span class="text-xs text-slate-500 font-mono">S/N: ${matchedItem.serial_number || '-'} • Rak: ${matchedItem.rack || 'Gudang'}</span>`,
                        timer: 1600,
                        showConfirmButton: false
                    });
                }

                this.scanQuery = '';
                this.$nextTick(() => {
                    if (this.$refs.scanInput) this.$refs.scanInput.focus();
                });
            } else {
                const matchedProduct = this.productsList.find(p => 
                    p.sku && p.sku.toLowerCase() === queryLower
                );

                if (matchedProduct) {
                    this.itemRows.push({
                        warehouse_item_id: 'custom',
                        product_id: matchedProduct.id,
                        product_name: matchedProduct.name,
                        serial_number: '',
                        quantity: 1,
                        unit: matchedProduct.unit || 'Unit'
                    });

                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Katalog Masuk!',
                            text: `${matchedProduct.name} (${matchedProduct.sku})`,
                            timer: 1600,
                            showConfirmButton: false
                        });
                    }
                    this.scanQuery = '';
                    this.$nextTick(() => {
                        if (this.$refs.scanInput) this.$refs.scanInput.focus();
                    });
                } else {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Tidak Ditemukan',
                            text: `Perangkat dengan S/N atau Barcode "${query}" tidak ditemukan di rak gudang.`,
                        });
                    }
                    this.scanQuery = '';
                    this.$nextTick(() => {
                        if (this.$refs.scanInput) this.$refs.scanInput.focus();
                    });
                }
            }
        },
        addItemRow() {
            this.itemRows.push({ warehouse_item_id: '', product_id: '', product_name: '', serial_number: '', quantity: 1, unit: 'Unit' });
        },
        removeItemRow(index) {
            if (this.itemRows.length > 1) {
                this.itemRows.splice(index, 1);
            }
        },
        onPhysicalItemSelect(index) {
            const row = this.itemRows[index];
            if (row.warehouse_item_id && row.warehouse_item_id !== 'custom') {
                const item = this.availableItemsList.find(x => x.id == row.warehouse_item_id);
                if (item) {
                    row.product_name = item.name;
                    row.serial_number = item.serial_number || '';
                    row.quantity = 1;
                    row.unit = 'Unit';
                }
            } else if (row.warehouse_item_id === 'custom') {
                row.product_name = '';
                row.serial_number = '';
            }
        },
        onProductSelect(index) {
            const row = this.itemRows[index];
            if (row.product_id) {
                const p = this.productsList.find(x => x.id == row.product_id);
                if (p) {
                    row.product_name = p.name;
                    if (p.unit) row.unit = p.unit;
                }
            }
        },
        viewDetail(doData) {
            this.detailData = doData;
            this.showDetailModal = true;
        },
        formatDateIndo(dateStr) {
            if (!dateStr) return '-';
            try {
                const d = new Date(dateStr);
                if (isNaN(d.getTime())) return dateStr;
                return d.toLocaleDateString('en-GB', { day: 'numeric', month: 'long', year: 'numeric' });
            } catch(e) {
                return dateStr;
            }
        }
    };
}
</script>
@endsection
