@extends('layouts.app')

@section('title', 'Aset & Alat Kerja Operasional - Admin Support')

@section('content')
<div class="flex h-screen overflow-hidden" x-data="{ isCreateModalOpen: false, isBorrowModalOpen: false, selectedAsset: null, borrowAction: 'borrow', modalUrl: '' }" x-cloak>
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto bg-[#FAF9F8]">
        @include('components.topbar', ['title' => 'Aset Alat Kerja & Testing Tools (OTDR, Splicer, Toolkit)'])
        
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
                <form method="GET" action="{{ route('admin_support.assets.index') }}" class="flex flex-col sm:flex-row flex-wrap gap-2.5 w-full sm:w-auto">
                    <div class="relative">
                        <svg class="w-3.5 h-3.5 absolute left-3 top-3 text-[#948F99]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}"
                               placeholder="Cari Kode Aset / Nama Alat..." 
                               class="w-full sm:w-64 px-[11px] py-[9px] pl-8 rounded-lg border border-[#E7E5E3] text-[13px] text-[#17151C] outline-none bg-white focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all">
                    </div>

                    <select name="category" onchange="this.form.submit()" 
                            class="w-full sm:w-44 px-[11px] py-[9px] rounded-lg border border-[#E7E5E3] text-[13px] text-[#17151C] outline-none bg-white focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all cursor-pointer">
                        <option value="">Semua Kategori Alat</option>
                        <option value="Testing Tool" {{ request('category') == 'Testing Tool' ? 'selected' : '' }}>Testing Tool (OTDR)</option>
                        <option value="Fusion Splicer" {{ request('category') == 'Fusion Splicer' ? 'selected' : '' }}>Fusion Splicer</option>
                        <option value="Power Meter" {{ request('category') == 'Power Meter' ? 'selected' : '' }}>Power Meter (OPM)</option>
                        <option value="Toolkit" {{ request('category') == 'Toolkit' ? 'selected' : '' }}>Toolkit Engineer</option>
                        <option value="Laptop / Device" {{ request('category') == 'Laptop / Device' ? 'selected' : '' }}>Laptop / Device</option>
                    </select>

                    <select name="status" onchange="this.form.submit()" 
                            class="w-full sm:w-48 px-[11px] py-[9px] rounded-lg border border-[#E7E5E3] text-[13px] text-[#17151C] outline-none bg-white focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all cursor-pointer">
                        <option value="">Semua Status Peminjaman</option>
                        <option value="Available in HQ" {{ request('status') == 'Available in HQ' ? 'selected' : '' }}>Tersedia di HQ</option>
                        <option value="Borrowed by Engineer" {{ request('status') == 'Borrowed by Engineer' ? 'selected' : '' }}>Dipinjam Engineer</option>
                        <option value="In Maintenance" {{ request('status') == 'In Maintenance' ? 'selected' : '' }}>Kalibrasi / Servis</option>
                    </select>

                    @if(request('search') || request('category') || request('status'))
                        <a href="{{ route('admin_support.assets.index') }}" 
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
                    <span>Daftarkan Alat / Aset Baru</span>
                </button>
            </div>

            {{-- Table Assets --}}
            <div class="bg-white rounded-xl border border-[#E7E5E3] shadow-[0_1px_2px_rgba(14,13,18,0.05)] overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[1050px] border-collapse text-[13px]">
                        <thead>
                            <tr class="bg-[#F1F0EE] border-b border-[#E7E5E3]">
                                <th class="text-left py-3.5 px-4 text-[11px] font-semibold text-[#75727C] uppercase tracking-[0.3px] w-[18%]">Kode & Kategori</th>
                                <th class="text-left py-3.5 px-4 text-[11px] font-semibold text-[#75727C] uppercase tracking-[0.3px] w-[28%]">Nama Alat & Spesifikasi</th>
                                <th class="text-center py-3.5 px-3 text-[11px] font-semibold text-[#75727C] uppercase tracking-[0.3px] w-[12%] whitespace-nowrap">Kondisi Fisik</th>
                                <th class="text-center py-3.5 px-3 text-[11px] font-semibold text-[#75727C] uppercase tracking-[0.3px] w-[14%] whitespace-nowrap">Status Ketersediaan</th>
                                <th class="text-left py-3.5 px-4 text-[11px] font-semibold text-[#75727C] uppercase tracking-[0.3px] w-[16%]">Peminjam / Lokasi</th>
                                <th class="text-right py-3.5 px-4 text-[11px] font-semibold text-[#75727C] uppercase tracking-[0.3px] w-[12%] whitespace-nowrap">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#EFEDEB]">
                            @forelse($assets as $ast)
                                <tr class="hover:bg-[#F8F7F6] transition-colors">
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-[#17151C] text-[13px] leading-snug">{{ $ast->asset_code }}</div>
                                        <div class="text-[11px] text-gray-500 mt-0.5">{{ $ast->category }}</div>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-[#17151C] text-[13px]">{{ $ast->asset_name }}</div>
                                        <div class="text-[11.5px] text-gray-500 mt-0.5">Model: {{ $ast->brand_model ?: '-' }} &bull; SN: {{ $ast->serial_number ?: '-' }}</div>
                                    </td>
                                    <td class="py-3.5 px-3 text-center whitespace-nowrap">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10.5px] font-bold {{ $ast->condition == 'Excellent' || $ast->condition == 'Good' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                                            {{ $ast->condition }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-3 text-center whitespace-nowrap">
                                        @php $astBadge = $ast->status_badge; @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10.5px] font-bold border {{ $astBadge['bg'] }} {{ $astBadge['text'] }} {{ $astBadge['border'] }}">
                                            {{ $astBadge['label'] }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        @if($ast->status == 'Borrowed by Engineer')
                                            <div class="font-semibold text-gray-900 text-xs">{{ $ast->borrower ? $ast->borrower->name : 'Engineer' }}</div>
                                            <div class="text-[11px] text-gray-400">Kembali: {{ $ast->expected_return_date ? $ast->expected_return_date->format('d M Y') : '-' }}</div>
                                        @else
                                            <div class="text-xs text-gray-600">{{ $ast->storage_location }}</div>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                        @if($ast->status == 'Available in HQ')
                                            <button @click="selectedAsset = {{ $ast->id }}; borrowAction = 'borrow'; modalUrl = '/admin-support/assets/' + {{ $ast->id }} + '/borrow-return'; isBorrowModalOpen = true"
                                                    class="px-2.5 py-1 bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 font-bold rounded-lg text-xs transition">
                                                Pinjamkan
                                            </button>
                                        @else
                                            <form action="{{ route('admin_support.assets.borrow-return', $ast->id) }}" method="POST" class="inline">
                                                @csrf
                                                <input type="hidden" name="action" value="return">
                                                <button type="submit" class="px-2.5 py-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 font-bold rounded-lg text-xs transition">
                                                    Kembalikan
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-12 text-center text-gray-400 text-xs">
                                        Belum ada data aset alat kerja terdaftar.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($assets->hasPages())
                    <div class="p-4 border-t border-[#EFEDEB]">
                        {{ $assets->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>

    {{-- MODAL: Register Alat Baru --}}
    <div x-show="isCreateModalOpen" class="fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 space-y-4 border border-[#E7E5E3] shadow-xl animate-fade-in-up" @click.outside="isCreateModalOpen = false">
            <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                <h3 class="text-base font-bold text-gray-900">Daftarkan Aset Alat Kerja / Testing Tool</h3>
                <button @click="isCreateModalOpen = false" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>

            <form action="{{ route('admin_support.assets.store') }}" method="POST" class="space-y-3.5 text-xs">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Nama Alat *</label>
                        <input type="text" name="asset_name" required class="wms-input" placeholder="e.g. EXFO OTDR Optical Tester">
                    </div>
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Kategori *</label>
                        <select name="category" required class="wms-input font-bold">
                            <option value="Testing Tool">Testing Tool (OTDR)</option>
                            <option value="Fusion Splicer">Fusion Splicer</option>
                            <option value="Power Meter">Optical Power Meter (OPM)</option>
                            <option value="Toolkit">Toolkit Engineer</option>
                            <option value="Laptop / Device">Laptop / Device Operasional</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Brand & Model</label>
                        <input type="text" name="brand_model" class="wms-input" placeholder="e.g. Fujikura 90S+">
                    </div>
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Serial Number Alat</label>
                        <input type="text" name="serial_number" class="wms-input" placeholder="e.g. FJK-8839201">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Kondisi Fisik *</label>
                        <select name="condition" required class="wms-input">
                            <option value="Excellent">Excellent (Sempurna/Baru)</option>
                            <option value="Good" selected>Good (Baik & Terkalibrasi)</option>
                            <option value="Fair">Fair (Perlu Pengecekan)</option>
                            <option value="Need Calibration">Need Calibration</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Lokasi Penyimpanan</label>
                        <input type="text" name="storage_location" class="wms-input" value="HQ Workshop / Rak Alat">
                    </div>
                </div>

                <div class="pt-3 border-t border-gray-100 flex justify-end gap-2">
                    <button type="button" @click="isCreateModalOpen = false" class="px-4 py-2 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 cursor-pointer">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-[#C81E2C] text-white font-bold rounded-xl hover:brightness-105 shadow-sm cursor-pointer">Simpan Aset</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL: Form Peminjaman Alat --}}
    <div x-show="isBorrowModalOpen" class="fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-sm w-full p-5 space-y-4 border border-[#E7E5E3] shadow-xl animate-fade-in-up" @click.outside="isBorrowModalOpen = false">
            <div class="flex items-center justify-between pb-2 border-b border-gray-100">
                <h3 class="text-base font-bold text-gray-900">Pinjamkan Alat ke Engineer</h3>
                <button @click="isBorrowModalOpen = false" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>

            <form :action="modalUrl" method="POST" class="space-y-3.5 text-xs">
                @csrf
                <input type="hidden" name="action" value="borrow">
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Nama Engineer Peminjam *</label>
                    <select name="borrower_id" required class="wms-input font-bold">
                        @foreach($engineers as $eng)
                            <option value="{{ $eng->id }}">{{ $eng->name }} ({{ $eng->position ?: 'Staff' }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Estimasi Tanggal Kembali *</label>
                    <input type="date" name="expected_return_date" required class="wms-input" value="{{ date('Y-m-d', strtotime('+3 days')) }}">
                </div>

                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Keperluan / Proyek</label>
                    <textarea name="notes" rows="2" class="wms-input" placeholder="e.g. Splicing kabel FO proyek Mandiri..."></textarea>
                </div>

                <div class="pt-2 border-t border-gray-100 flex justify-end gap-2">
                    <button type="button" @click="isBorrowModalOpen = false" class="px-3 py-1.5 bg-gray-100 text-gray-700 font-bold rounded-lg">Batal</button>
                    <button type="submit" class="px-3 py-1.5 bg-[#C81E2C] text-white font-bold rounded-lg">Konfirmasi Pinjam</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
