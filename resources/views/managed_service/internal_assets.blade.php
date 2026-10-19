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
        @include('components.topbar', ['title' => 'Internal Asset (Aset & Peralatan Perusahaan)'])
        
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

            {{-- Top Action Bar & Filter --}}
            <div class="ipnet-card p-5 sm:p-6 flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4 anim-fade-up anim-delay-1">
                <div>
                    <p class="text-[#8F0A0D] text-[12px] font-bold inline-flex items-center uppercase tracking-wider">
                        <span class="w-2 h-2 rounded-full bg-[#8F0A0D] inline-block mr-2"></span> ASET PERUSAHAAN (INTERNAL)
                    </p>
                    <h3 class="font-bold text-[#1E293B] text-[20px] tracking-tight mt-0.5">Inventaris Alat Kerja & Aset Operasional HQ</h3>
                    <p class="text-[13px] text-[#64748B] mt-0.5">Daftar alat ukur, splicing kit, laptop, dan perlengkapan teknisi milik PT IP Network Solusindo.</p>
                </div>

                <div class="flex items-center gap-2.5 shrink-0">
                    <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200">
                        Total: {{ $assets->total() }} Alat
                    </span>
                </div>
            </div>

            {{-- Filters --}}
            <div class="ipnet-card p-4 anim-fade-up anim-delay-2">
                <form method="GET" action="{{ route('ms.assets.internal') }}" class="flex flex-col sm:flex-row flex-wrap gap-3 items-center justify-between">
                    <div class="flex flex-col sm:flex-row flex-wrap gap-3 w-full sm:w-auto flex-1">
                        <div class="relative flex-1 sm:max-w-xs">
                            <svg class="w-4 h-4 absolute left-3 top-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input type="text" 
                                   name="search" 
                                   value="{{ request('search') }}"
                                   placeholder="Cari Kode Aset / Nama Alat / SN..." 
                                   class="w-full pl-9 pr-3 py-2 rounded-xl border border-slate-200 text-[13px] text-slate-800 outline-none bg-white focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition-all">
                        </div>

                        <select name="category" onchange="this.form.submit()" 
                                class="px-3 py-2 rounded-xl border border-slate-200 text-[13px] text-slate-800 outline-none bg-white focus:border-[#8F0A0D] cursor-pointer">
                            <option value="">Semua Kategori</option>
                            <option value="Testing Tool" {{ request('category') == 'Testing Tool' ? 'selected' : '' }}>Testing Tool (OTDR)</option>
                            <option value="Fusion Splicer" {{ request('category') == 'Fusion Splicer' ? 'selected' : '' }}>Fusion Splicer</option>
                            <option value="Power Meter" {{ request('category') == 'Power Meter' ? 'selected' : '' }}>Power Meter (OPM)</option>
                            <option value="Toolkit" {{ request('category') == 'Toolkit' ? 'selected' : '' }}>Toolkit Engineer</option>
                            <option value="Laptop / Device" {{ request('category') == 'Laptop / Device' ? 'selected' : '' }}>Laptop / Device</option>
                        </select>

                        <select name="status" onchange="this.form.submit()" 
                                class="px-3 py-2 rounded-xl border border-slate-200 text-[13px] text-slate-800 outline-none bg-white focus:border-[#8F0A0D] cursor-pointer">
                            <option value="">Semua Status</option>
                            <option value="Available in HQ" {{ request('status') == 'Available in HQ' ? 'selected' : '' }}>Tersedia di HQ</option>
                            <option value="Borrowed by Engineer" {{ request('status') == 'Borrowed by Engineer' ? 'selected' : '' }}>Dipinjam Engineer</option>
                            <option value="In Maintenance" {{ request('status') == 'In Maintenance' ? 'selected' : '' }}>Kalibrasi / Servis</option>
                        </select>
                    </div>

                    @if(request('search') || request('category') || request('status'))
                        <a href="{{ route('ms.assets.internal') }}" class="px-3 py-1.5 text-xs font-semibold text-slate-500 hover:text-slate-800">
                            Reset Filter
                        </a>
                    @endif
                </form>
            </div>

            {{-- Table --}}
            <div class="ipnet-card overflow-hidden anim-fade-up anim-delay-3">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-[13px]">
                        <thead class="bg-slate-50/80 border-b border-slate-200 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                            <tr>
                                <th class="px-5 py-3.5">Kode & Nama Alat</th>
                                <th class="px-5 py-3.5">Kategori</th>
                                <th class="px-5 py-3.5">Merek / Model</th>
                                <th class="px-5 py-3.5">Serial Number</th>
                                <th class="px-5 py-3.5">Kondisi</th>
                                <th class="px-5 py-3.5">Status Peminjaman</th>
                                <th class="px-5 py-3.5">Lokasi Penyimpanan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @forelse($assets as $asset)
                                <tr class="hover:bg-slate-50/60 transition">
                                    <td class="px-5 py-3.5">
                                        <div class="font-bold text-slate-900">{{ $asset->asset_name }}</div>
                                        <div class="text-[11.5px] font-mono text-[#8F0A0D] font-semibold">{{ $asset->asset_code ?: '-' }}</div>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-slate-100 text-slate-700">
                                            {{ $asset->category ?: 'General' }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5 text-slate-600 font-medium">
                                        {{ $asset->brand_model ?: '-' }}
                                    </td>
                                    <td class="px-5 py-3.5 font-mono text-[12px] text-slate-500">
                                        {{ $asset->serial_number ?: '-' }}
                                    </td>
                                    <td class="px-5 py-3.5">
                                        @if($asset->condition === 'Good' || $asset->condition === 'Baik')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                Baik / Siap Pakai
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                                {{ $asset->condition ?: 'Normal' }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5">
                                        @if($asset->status === 'Borrowed by Engineer')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                Dipinjam: {{ $asset->borrower ? $asset->borrower->name : ($asset->borrower_name ?: 'Engineer') }}
                                            </span>
                                        @elseif($asset->status === 'In Maintenance')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-50 text-rose-800 border border-rose-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                                Servis / Kalibrasi
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                Tersedia di HQ
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5 text-slate-500 text-[12px]">
                                        {{ $asset->storage_location ?: 'HQ Workshop' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-5 py-10 text-center text-slate-400">
                                        Belum ada data aset internal perusahaan yang terdaftar.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($assets->hasPages())
                    <div class="p-4 border-t border-slate-100">
                        {{ $assets->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</div>
@endsection
