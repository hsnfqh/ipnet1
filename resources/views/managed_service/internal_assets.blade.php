@extends('layouts.app')

@section('title', 'Internal Asset - Managed Service')

@push('styles')
<style>
    :root {
        --ipnet-primary: #8F0A0D;
        --ipnet-primary-hover: #73080A;
    }
    .ipnet-card {
        background-color: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04), 0 4px 12px rgba(0, 0, 0, 0.02);
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .btn-ipnet-gradient {
        background: linear-gradient(135deg, #8F0A0D 0%, #B81525 100%);
        color: #FFFFFF;
        transition: all 0.2s ease;
    }
    .btn-ipnet-gradient:hover {
        background: linear-gradient(135deg, #7A080B 0%, #9E0E1D 100%);
        box-shadow: 0 6px 16px rgba(143, 10, 13, 0.25);
        transform: translateY(-1px);
    }
    @keyframes fadeUpStagger {
        0% { opacity: 0; transform: translateY(16px); }
        100% { opacity: 1; transform: translateY(0); }
    }
    .anim-fade-up {
        animation: fadeUpStagger 0.5s cubic-bezier(0.16, 1, 0.3, 1) both;
    }
    .anim-delay-1 { animation-delay: 0.06s !important; }
    .anim-delay-2 { animation-delay: 0.12s !important; }
    .anim-delay-3 { animation-delay: 0.18s !important; }
</style>
@endpush

@section('content')
<div class="flex h-screen overflow-hidden bg-[#F8FAFC] font-sans" x-data="{}" x-cloak>
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Internal Asset'])
        
        <div class="p-4 sm:p-6 lg:p-7 space-y-6 max-w-[1600px] mx-auto animate-fade-in">
            
            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between text-[13px] font-semibold shadow-xs">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-600 flex-shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            {{-- Top Action Bar --}}
            <div class="ipnet-card p-5 sm:p-6 flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4 anim-fade-up anim-delay-1">
                <div>
                    <p class="text-[#8F0A0D] text-[12px] font-bold inline-flex items-center uppercase tracking-wider">
                        <span class="w-2 h-2 rounded-full bg-[#8F0A0D] inline-block mr-2"></span> ASET INTERNAL
                    </p>
                    <h3 class="font-bold text-[#1E293B] text-[20px] tracking-tight mt-0.5">Aset Internal Perusahaan</h3>
                    <p class="text-[13px] text-[#64748B] mt-0.5">Inventaris alat kerja, perangkat kantor, dan operasional PT IPNET</p>
                </div>

                <div class="flex items-center gap-2.5 shrink-0">
                    <span class="inline-flex items-center px-3.5 py-1.5 rounded-full text-xs font-bold bg-[#F8FAFC] text-[#1E293B] border border-[#E2E8F0] shadow-xs">
                        Total Aset Internal: <strong class="text-[#8F0A0D] ml-1">{{ $assets->total() }}</strong>
                    </span>
                </div>
            </div>

            {{-- Filter & Per-Page Controls --}}
            <div class="ipnet-card p-4 anim-fade-up anim-delay-2">
                <form method="GET" action="{{ route('ms.assets.internal') }}" class="flex flex-col md:flex-row flex-wrap items-center justify-between gap-3">
                    <div class="flex flex-col sm:flex-row flex-wrap items-center gap-2.5 w-full md:w-auto flex-1">
                        <div class="relative w-full sm:w-64">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#94A3B8]">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                            <input type="text" 
                                   name="search" 
                                   value="{{ request('search') }}"
                                   placeholder="Cari kode aset, nama alat, SN..." 
                                   class="w-full pl-10 pr-3 py-2 rounded-xl border border-[#CBD5E1] text-[12.5px] font-medium text-[#1E293B] bg-white outline-none hover:border-[#94A3B8] focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition-all shadow-xs">
                        </div>

                        <select name="category" onchange="this.form.submit()" 
                                class="w-full sm:w-44 px-3 py-2 rounded-xl border border-[#CBD5E1] text-[12.5px] font-semibold text-[#1E293B] bg-white outline-none hover:border-[#94A3B8] focus:border-[#8F0A0D] cursor-pointer shadow-xs">
                            <option value="all">Semua Tipe</option>
                            <option value="Testing Tool" {{ request('category') == 'Testing Tool' ? 'selected' : '' }}>Testing Tool (OTDR)</option>
                            <option value="Fusion Splicer" {{ request('category') == 'Fusion Splicer' ? 'selected' : '' }}>Fusion Splicer</option>
                            <option value="Power Meter" {{ request('category') == 'Power Meter' ? 'selected' : '' }}>Power Meter (OPM)</option>
                            <option value="Toolkit" {{ request('category') == 'Toolkit' ? 'selected' : '' }}>Toolkit Engineer</option>
                            <option value="Laptop / Device" {{ request('category') == 'Laptop / Device' ? 'selected' : '' }}>Laptop / Device</option>
                        </select>

                        <select name="status" onchange="this.form.submit()" 
                                class="w-full sm:w-48 px-3 py-2 rounded-xl border border-[#CBD5E1] text-[12.5px] font-semibold text-[#1E293B] bg-white outline-none hover:border-[#94A3B8] focus:border-[#8F0A0D] cursor-pointer shadow-xs">
                            <option value="all">Semua Status</option>
                            <option value="Available in HQ" {{ request('status') == 'Available in HQ' ? 'selected' : '' }}>Tersedia di HQ</option>
                            <option value="Borrowed by Engineer" {{ request('status') == 'Borrowed by Engineer' ? 'selected' : '' }}>Dipinjam Engineer</option>
                            <option value="In Maintenance" {{ request('status') == 'In Maintenance' ? 'selected' : '' }}>Kalibrasi / Servis</option>
                        </select>

                        @if(request('search') || (request('category') && request('category') !== 'all') || (request('status') && request('status') !== 'all') || (request('per_page') && request('per_page') != 10))
                            <a href="{{ route('ms.assets.internal') }}" class="px-3 py-2 text-xs font-semibold text-slate-500 hover:text-slate-800 self-center">
                                Reset Filter
                            </a>
                        @endif
                    </div>

                    {{-- Option 5, 10, 25, 50 per page --}}
                    <div class="flex items-center gap-2 self-end md:self-center shrink-0">
                        <span class="text-[12px] font-semibold text-[#64748B]">Tampilkan:</span>
                        <select name="per_page" onchange="this.form.submit()" class="px-3 py-2 rounded-xl border border-[#CBD5E1] text-[12px] font-bold text-[#1E293B] bg-white outline-none hover:border-[#94A3B8] focus:border-[#8F0A0D] cursor-pointer shadow-xs">
                            <option value="5" {{ request('per_page', $perPage ?? 10) == 5 ? 'selected' : '' }}>5 baris</option>
                            <option value="10" {{ request('per_page', $perPage ?? 10) == 10 ? 'selected' : '' }}>10 baris</option>
                            <option value="25" {{ request('per_page', $perPage ?? 10) == 25 ? 'selected' : '' }}>25 baris</option>
                            <option value="50" {{ request('per_page', $perPage ?? 10) == 50 ? 'selected' : '' }}>50 baris</option>
                        </select>
                    </div>
                </form>
            </div>

            {{-- Table with Exact 7 Columns: Tipe Perangkat, Product Name, Product Description, Quantity, Serial Number, Keterangan, Pemilik --}}
            <div class="ipnet-card overflow-hidden anim-fade-up anim-delay-3">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-[#E2E8F0] bg-[#F8FAFC] text-[11px] font-bold tracking-wider text-[#64748B] uppercase">
                                <th class="py-3.5 px-4">Tipe Perangkat</th>
                                <th class="py-3.5 px-4">Product Name</th>
                                <th class="py-3.5 px-4">Product Description</th>
                                <th class="py-3.5 px-3 text-center">Quantity</th>
                                <th class="py-3.5 px-4">Serial Number</th>
                                <th class="py-3.5 px-4">Keterangan</th>
                                <th class="py-3.5 px-4">Pemilik</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#F1F5F9] text-[12.5px]">
                            @forelse($assets as $asset)
                                <tr class="hover:bg-[#F8FAFC]/80 transition-colors">
                                    {{-- 1. Tipe Perangkat --}}
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[11px] font-bold bg-[#F1F5F9] text-[#475569] border border-[#E2E8F0]">
                                            {{ $asset->category ?: 'Peralatan' }}
                                        </span>
                                    </td>

                                    {{-- 2. Product Name --}}
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-[#1E293B] text-[13px]">{{ $asset->asset_name }}</div>
                                        <div class="text-[11.5px] font-mono text-[#8F0A0D] font-semibold">{{ $asset->asset_code ?: '-' }}</div>
                                    </td>

                                    {{-- 3. Product Description --}}
                                    <td class="py-3.5 px-4 max-w-xs">
                                        <div class="text-[#334155] text-[12px]">
                                            {{ $asset->brand_model ?: '—' }}
                                        </div>
                                    </td>

                                    {{-- 4. Quantity --}}
                                    <td class="py-3.5 px-3 text-center whitespace-nowrap">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11.5px] font-extrabold bg-slate-100 text-slate-800 border border-slate-200">
                                            1 Unit
                                        </span>
                                    </td>

                                    {{-- 5. Serial Number --}}
                                    <td class="py-3.5 px-4 font-mono text-[11.5px] text-[#475569] font-medium whitespace-nowrap">
                                        {{ $asset->serial_number ?: '—' }}
                                    </td>

                                    {{-- 6. Keterangan --}}
                                    <td class="py-3.5 px-4 max-w-xs">
                                        <div class="text-[#475569] text-[11.5px]">
                                            {{ $asset->notes ?: ($asset->storage_location ? 'Lokasi: ' . $asset->storage_location : '—') }}
                                        </div>
                                        <div class="mt-1">
                                            @if($asset->status === 'Borrowed by Engineer')
                                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md text-[10.5px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                    Dipinjam: {{ $asset->borrower ? $asset->borrower->name : 'Engineer' }}
                                                </span>
                                            @elseif($asset->status === 'In Maintenance')
                                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md text-[10.5px] font-bold bg-rose-50 text-rose-800 border border-rose-200">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                                    Servis / Kalibrasi
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md text-[10.5px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                    Tersedia di HQ
                                                </span>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- 7. Pemilik --}}
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-bold bg-rose-50 text-[#8F0A0D] border border-rose-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[#8F0A0D]"></span>
                                            PT IPNET (Internal)
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-12 text-center text-[#64748B] text-[13px]">
                                        Belum ada data aset internal perusahaan yang terdaftar.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($assets->hasPages())
                    <div class="p-4 border-t border-[#E2E8F0] bg-white flex flex-col sm:flex-row items-center justify-between gap-3">
                        <div class="text-xs text-slate-500">
                            Menampilkan {{ $assets->firstItem() ?? 0 }} - {{ $assets->lastItem() ?? 0 }} dari total {{ $assets->total() }} data
                        </div>
                        <div>
                            {{ $assets->links() }}
                        </div>
                    </div>
                @endif
            </div>

        </div>
    </div>
</div>
@endsection
