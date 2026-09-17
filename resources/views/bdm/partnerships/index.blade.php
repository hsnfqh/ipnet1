@extends('layouts.app')

@section('title', 'Mitra Vendor & Prinsipal - BDM Portal PT IP Network Solusindo')

@push('styles')
<style>
    :root {
        --ipnet-primary: #8F0A0D;
        --ipnet-primary-hover: #73080A;
        --ipnet-card-bg: #FFFFFF;
        --ipnet-card-border: #E2E8F0;
        --ipnet-text-main: #1E293B;
    }

    .ipnet-card {
        background-color: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04), 0 4px 12px rgba(0, 0, 0, 0.02);
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .ipnet-card:hover {
        border-color: #CBD5E1;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
    }

    .ipnet-metric-card {
        background-color: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 16px;
        padding: 16px 18px;
        position: relative;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
        transition: all 0.2s ease;
    }

    .ipnet-metric-card:hover {
        transform: translateY(-2px);
        border-color: #CBD5E1;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);
    }

    .btn-ipnet-primary {
        background: linear-gradient(135deg, #8F0A0D 0%, #B81525 100%);
        color: #FFFFFF;
        font-weight: 600;
        transition: all 0.2s ease;
        box-shadow: 0 2px 4px rgba(143, 10, 13, 0.15);
    }

    .btn-ipnet-primary:hover {
        background: linear-gradient(135deg, #7A080B 0%, #9E0E1D 100%);
        box-shadow: 0 6px 16px rgba(143, 10, 13, 0.28);
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
<div class="flex h-screen overflow-hidden bg-[#F8FAFC] font-sans" x-data="{ isPartnerModalOpen: false }">
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Mitra Vendor & Prinsipal'])
        
        <div class="p-4 sm:p-6 lg:p-7 space-y-6 max-w-[1680px] mx-auto animate-fade-in">

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-medium flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 font-bold px-2">✕</button>
                </div>
            @endif

            {{-- 3 SUMMARY METRIC CARDS --}}
            @php
                $principalCount = $partners->where('partner_type', 'Principal')->count();
                $activeCount = $partners->where('status', 'Active')->count();
            @endphp
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 anim-fade-up anim-delay-1">
                <div class="ipnet-metric-card flex items-center justify-between">
                    <div>
                        <span class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider block">Total Mitra Terdaftar</span>
                        <div class="text-xl font-bold text-gray-800 tracking-tight mt-0.5">
                            {{ $partners->total() }} Mitra &amp; Channel
                        </div>
                        <span class="text-[11.5px] text-gray-400 mt-0.5 block">Katalog ekosistem teknologi</span>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                </div>

                <div class="ipnet-metric-card flex items-center justify-between">
                    <div>
                        <span class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider block">Principal &amp; Distributor</span>
                        <div class="text-xl font-bold text-purple-700 tracking-tight mt-0.5">
                            {{ $principalCount }} Prinsipal Resmi
                        </div>
                        <span class="text-[11.5px] text-purple-600 font-medium mt-0.5 block">Direct authorization channel</span>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-purple-50 border border-purple-100 text-purple-600 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                    </div>
                </div>

                <div class="ipnet-metric-card flex items-center justify-between">
                    <div>
                        <span class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider block">Status Kolaborasi</span>
                        <div class="text-xl font-bold text-blue-700 tracking-tight mt-0.5">
                            {{ $activeCount }} Mitra Aktif
                        </div>
                        <span class="text-[11.5px] text-gray-400 mt-0.5 block">Deal registration &amp; support</span>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-100 text-blue-600 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                </div>
            </div>

            <!-- Filter & Action Bar -->
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 anim-fade-up anim-delay-2">
                <form method="GET" action="{{ route('bdm.partnerships.index') }}" class="flex flex-col sm:flex-row flex-wrap gap-2.5 w-full sm:w-auto">
                    <div class="relative">
                        <svg class="w-4 h-4 absolute left-3.5 top-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" 
                               name="search" 
                               value="{{ $search }}"
                               placeholder="Cari nama mitra, PIC, benefit..." 
                               class="w-full sm:w-72 px-3.5 py-2 pl-9 rounded-xl border border-gray-200 text-xs font-medium text-gray-800 bg-white focus:outline-none focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D] transition-all shadow-xs">
                    </div>

                    <select name="type" onchange="this.form.submit()" 
                            class="w-full sm:w-56 px-3.5 py-2 rounded-xl border border-gray-200 text-xs font-medium text-gray-800 bg-white focus:outline-none focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D] transition-all cursor-pointer shadow-xs">
                        <option value="">Semua Tipe Mitra</option>
                        <option value="Principal" {{ $partnerType === 'Principal' ? 'selected' : '' }}>Principal (Vendor)</option>
                        <option value="Distributor" {{ $partnerType === 'Distributor' ? 'selected' : '' }}>Distributor Resmi</option>
                        <option value="Telco / ISP Provider" {{ $partnerType === 'Telco / ISP Provider' ? 'selected' : '' }}>Telco / ISP Provider</option>
                        <option value="Technology Partner" {{ $partnerType === 'Technology Partner' ? 'selected' : '' }}>Technology Partner</option>
                    </select>

                    @if($search || $partnerType)
                        <a href="{{ route('bdm.partnerships.index') }}" 
                           class="px-3 py-2 text-xs font-medium text-gray-500 hover:text-gray-800 self-center">
                            Reset Filter
                        </a>
                    @endif
                </form>

                <div class="flex items-center gap-2">
                    <button type="button" 
                            @click="isPartnerModalOpen = true" 
                            class="btn-ipnet-primary inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs font-semibold shadow-md transition-all cursor-pointer whitespace-nowrap">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>Daftarkan Mitra Baru</span>
                    </button>
                </div>
            </div>

            <!-- Table Container -->
            <div class="ipnet-card overflow-hidden anim-fade-up anim-delay-3">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-gray-50/80 text-gray-500 uppercase text-[10.5px] font-semibold tracking-wider">
                            <tr>
                                <th class="py-3 px-4 rounded-l-lg">Nama Mitra / Prinsipal</th>
                                <th class="py-3 px-4">Tipe &amp; Tier</th>
                                <th class="py-3 px-4">Kontak PIC</th>
                                <th class="py-3 px-4">Ruang Lingkup / Benefit</th>
                                <th class="py-3 px-4 text-center">Status</th>
                                <th class="py-3 px-4 rounded-r-lg text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 font-normal text-gray-600">
                            @forelse($partners as $partner)
                                @php
                                    $tierBadge = match($partner->tier_level) {
                                        'Platinum Partner' => 'bg-purple-50 text-purple-700 border-purple-200',
                                        'Gold Partner' => 'bg-amber-50 text-amber-700 border-amber-200',
                                        'Strategic Distributor' => 'bg-blue-50 text-blue-700 border-blue-200',
                                        default => 'bg-emerald-50 text-emerald-700 border-emerald-200'
                                    };
                                @endphp
                                <tr class="hover:bg-gray-50/80 transition-colors">
                                    <td class="py-3.5 px-4">
                                        <div class="font-semibold text-gray-800 text-xs">{{ $partner->partner_name }}</div>
                                        <div class="text-[11px] text-gray-400 mt-0.5">{{ $partner->partner_type }}</div>
                                    </td>
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[10.5px] font-medium border {{ $tierBadge }}">
                                            {{ $partner->tier_level }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <div class="font-medium text-gray-800">{{ $partner->pic_name ?: '—' }}</div>
                                        <div class="text-[11px] text-gray-400 mt-0.5">{{ $partner->pic_contact ?: ($partner->pic_email ?: '—') }}</div>
                                    </td>
                                    <td class="py-3.5 px-4 text-gray-600 max-w-xs leading-snug">
                                        {{ $partner->collaboration_scope ?: 'Kerjasama distribusi & implementasi perangkat jaringan' }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        <span class="inline-flex items-center gap-1 text-[10.5px] font-medium text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            {{ $partner->status }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                        <form action="{{ route('bdm.partnership.destroy', $partner->id) }}" method="POST" onsubmit="return confirm('Hapus data kemitraan ini?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="p-1.5 bg-white hover:bg-red-50 text-red-600 hover:text-red-700 rounded-lg border border-red-200 shadow-xs transition cursor-pointer"
                                                    title="Hapus Kemitraan">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-12 text-center text-gray-400 text-xs font-medium">
                                        Belum ada data kemitraan yang sesuai filter.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($partners->hasPages())
                    <div class="p-4 bg-gray-50/70 border-t border-gray-100 flex items-center justify-between text-xs">
                        <div class="text-gray-500 font-medium">
                            Menampilkan <span class="font-semibold text-gray-800">{{ $partners->firstItem() }}</span> - <span class="font-semibold text-gray-800">{{ $partners->lastItem() }}</span> dari <span class="font-semibold text-gray-800">{{ $partners->total() }}</span> mitra
                        </div>
                        <div>
                            {{ $partners->appends(request()->query())->links() }}
                        </div>
                    </div>
                @endif
            </div>

        </div>
    </div>

    {{-- MODAL INPUT PARTNERSHIP --}}
    <template x-teleport="body">
        <div x-show="isPartnerModalOpen" 
             x-cloak 
             class="fixed inset-0 z-50 bg-slate-900/60 flex items-center justify-center p-3 sm:p-5 backdrop-blur-sm"
             @click.self="isPartnerModalOpen = false">
            <div class="bg-white rounded-2xl w-[600px] max-w-full max-h-[85vh] flex flex-col overflow-hidden shadow-2xl border border-gray-200 my-auto animate-fade-in-up">
                
                {{-- Modal Header (Fixed) --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 flex-shrink-0 bg-white">
                    <div>
                        <h3 class="text-base font-bold text-gray-800">Daftarkan Kemitraan &amp; Prinsipal</h3>
                        <p class="text-xs text-gray-400 mt-0.5">Kerjasama vendor teknologi, tingkatan tier &amp; proteksi harga.</p>
                    </div>
                    <button type="button" @click="isPartnerModalOpen = false" class="rounded-xl p-2 text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition-colors cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Modal Body (Scrollable) --}}
                <div class="px-6 py-4 overflow-y-auto flex-1">
                    <form id="partnerForm" action="{{ route('bdm.partnership.store') }}" method="POST" class="space-y-3.5 text-xs">
                        @csrf
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Nama Mitra / Prinsipal <span class="text-[#8F0A0D]">*</span></label>
                            <input type="text" name="partner_name" required class="w-full bg-white border border-gray-200 rounded-xl px-3.5 py-2 text-xs font-medium text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D] transition-all" placeholder="e.g. Fortinet Indonesia">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Tipe Mitra <span class="text-[#8F0A0D]">*</span></label>
                                <select name="partner_type" required class="w-full bg-white border border-gray-200 rounded-xl px-3.5 py-2 text-xs font-medium text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D] transition-all cursor-pointer">
                                    <option value="Principal">Principal (Vendor)</option>
                                    <option value="Distributor">Distributor Resmi</option>
                                    <option value="Telco / ISP Provider">Telco / ISP Provider</option>
                                    <option value="Technology Partner">Technology Partner</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Tier / Level Kemitraan <span class="text-[#8F0A0D]">*</span></label>
                                <select name="tier_level" required class="w-full bg-white border border-gray-200 rounded-xl px-3.5 py-2 text-xs font-medium text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D] transition-all cursor-pointer">
                                    <option value="Platinum Partner">Platinum Partner</option>
                                    <option value="Gold Partner" selected>Gold Partner</option>
                                    <option value="Authorized Partner">Authorized Partner</option>
                                    <option value="Strategic Distributor">Strategic Distributor</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-[11px] font-medium text-gray-600 mb-1">Nama PIC</label>
                                <input type="text" name="pic_name" class="w-full bg-white border border-gray-200 rounded-xl px-3 py-2 text-xs text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D] transition-all" placeholder="Nama PIC">
                            </div>
                            <div>
                                <label class="block text-[11px] font-medium text-gray-600 mb-1">No. Kontak</label>
                                <input type="text" name="pic_contact" class="w-full bg-white border border-gray-200 rounded-xl px-3 py-2 text-xs text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D] transition-all" placeholder="0812xxxxxxxx">
                            </div>
                            <div>
                                <label class="block text-[11px] font-medium text-gray-600 mb-1">Email</label>
                                <input type="email" name="pic_email" class="w-full bg-white border border-gray-200 rounded-xl px-3 py-2 text-xs text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D] transition-all" placeholder="pic@vendor.com">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Ruang Lingkup Kerjasama / Benefit</label>
                            <textarea name="collaboration_scope" rows="2.5" class="w-full bg-white border border-gray-200 rounded-xl p-2.5 text-xs text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D] transition-all" placeholder="Hak deal registration, unit demo POC, proteksi harga diskon partner, target tahunan..."></textarea>
                        </div>
                    </form>
                </div>

                {{-- Modal Footer (Fixed) --}}
                <div class="flex items-center gap-3 px-6 py-4 border-t border-gray-100 bg-gray-50 flex-shrink-0">
                    <button type="submit" 
                            form="partnerForm" 
                            class="btn-ipnet-primary flex-1 flex items-center justify-center min-h-[40px] rounded-xl font-semibold text-xs shadow-md transition-all cursor-pointer">
                        Daftarkan Mitra
                    </button>
                    <button type="button" 
                            @click="isPartnerModalOpen = false" 
                            class="flex-1 flex items-center justify-center min-h-[40px] bg-white hover:bg-gray-100 text-gray-600 border border-gray-200 shadow-xs rounded-xl font-medium text-xs transition-all cursor-pointer">
                        Batal
                    </button>
                </div>

            </div>
        </div>
    </template>

</div>
@endsection
