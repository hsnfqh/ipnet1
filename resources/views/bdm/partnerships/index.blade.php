@extends('layouts.app')

@section('title', 'Mitra Vendor - BDM Portal')

@section('content')
<div class="flex h-screen overflow-hidden" x-data="{ isPartnerModalOpen: false }">
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto bg-[#FAF9F8]">
        @include('components.topbar', ['title' => 'Mitra Vendor'])
        
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

            <!-- Filter & Action Bar -->
            <div class="flex flex-col sm:flex-row sm:flex-wrap sm:justify-between sm:items-center gap-2.5">
                <form method="GET" action="{{ route('bdm.partnerships.index') }}" class="flex flex-col sm:flex-row flex-wrap gap-2.5 w-full sm:w-auto">
                    <div class="relative">
                        <svg class="w-3.5 h-3.5 absolute left-3 top-3 text-[#948F99]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" 
                               name="search" 
                               value="{{ $search }}"
                               placeholder="Cari nama mitra, PIC, benefit..." 
                               class="w-full sm:w-64 px-[11px] py-[9px] pl-8 rounded-lg border border-[#E7E5E3] text-[13.5px] text-[#17151C] outline-none bg-white focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all">
                    </div>
                    <select name="type" onchange="this.form.submit()" 
                            class="w-full sm:w-48 px-[11px] py-[9px] rounded-lg border border-[#E7E5E3] text-[13.5px] text-[#17151C] outline-none bg-white focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all cursor-pointer">
                        <option value="">Semua Tipe Mitra</option>
                        <option value="Principal" {{ $partnerType === 'Principal' ? 'selected' : '' }}>Principal (Vendor)</option>
                        <option value="Distributor" {{ $partnerType === 'Distributor' ? 'selected' : '' }}>Distributor Resmi</option>
                        <option value="Telco / ISP Provider" {{ $partnerType === 'Telco / ISP Provider' ? 'selected' : '' }}>Telco / ISP Provider</option>
                        <option value="Technology Partner" {{ $partnerType === 'Technology Partner' ? 'selected' : '' }}>Technology Partner</option>
                    </select>
                    @if($search || $partnerType)
                        <a href="{{ route('bdm.partnerships.index') }}" 
                           class="px-3 py-2 text-[13px] font-semibold text-gray-500 hover:text-gray-700 self-center">
                            Reset
                        </a>
                    @endif
                </form>

                <button type="button" @click="isPartnerModalOpen = true" 
                        class="w-full sm:w-auto justify-center bg-[#C81E2C] text-white shadow-[0_8px_20px_rgba(200,30,44,0.24)] px-[17px] py-[9px] rounded-lg font-semibold text-[13.5px] flex items-center gap-1.5 hover:brightness-105 active:translate-y-[1px] transition-all cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Daftarkan Mitra Baru</span>
                </button>
            </div>

            <!-- Table Container -->
            <div class="bg-white rounded-xl border border-[#E7E5E3] shadow-[0_1px_2px_rgba(14,13,18,0.05)] overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[900px] border-collapse text-[13.5px]">
                        <thead>
                            <tr class="bg-[#F1F0EE] border-b border-[#E7E5E3]">
                                <th class="text-left py-3.5 px-4 text-[11.5px] font-semibold text-[#75727C] uppercase tracking-[0.3px]">Nama Mitra / Prinsipal</th>
                                <th class="text-left py-3.5 px-4 text-[11.5px] font-semibold text-[#75727C] uppercase tracking-[0.3px]">Tipe & Tier</th>
                                <th class="text-left py-3.5 px-4 text-[11.5px] font-semibold text-[#75727C] uppercase tracking-[0.3px]">Kontak PIC</th>
                                <th class="text-left py-3.5 px-4 text-[11.5px] font-semibold text-[#75727C] uppercase tracking-[0.3px]">Ruang Lingkup / Benefit</th>
                                <th class="text-center py-3.5 px-4 text-[11.5px] font-semibold text-[#75727C] uppercase tracking-[0.3px]">Status</th>
                                <th class="text-right py-3.5 px-4 text-[11.5px] font-semibold text-[#75727C] uppercase tracking-[0.3px]">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#EFEDEB]">
                            @forelse($partners as $partner)
                                @php
                                    $tierBadge = match($partner->tier_level) {
                                        'Platinum Partner' => 'bg-purple-50 text-purple-700 border-purple-200',
                                        'Gold Partner' => 'bg-amber-50 text-amber-700 border-amber-200',
                                        'Strategic Distributor' => 'bg-blue-50 text-blue-700 border-blue-200',
                                        default => 'bg-emerald-50 text-emerald-700 border-emerald-200'
                                    };
                                @endphp
                                <tr class="hover:bg-[#F8F7F6] transition-colors">
                                    <td class="py-3.5 px-4 font-bold text-[#17151C]">
                                        <div class="text-[13.5px]">{{ $partner->partner_name }}</div>
                                        <div class="text-[11px] text-[#75727C] mt-0.5">{{ $partner->partner_type }}</div>
                                    </td>
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10.5px] font-bold border {{ $tierBadge }}">
                                            {{ $partner->tier_level }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <div class="font-semibold text-gray-800">{{ $partner->pic_name ?: '—' }}</div>
                                        <div class="text-[11px] text-gray-400 mt-0.5">{{ $partner->pic_contact ?: ($partner->pic_email ?: '—') }}</div>
                                    </td>
                                    <td class="py-3.5 px-4 text-[#3D3A44] text-[12.5px] max-w-xs">
                                        {{ $partner->collaboration_scope ?: 'Kerjasama distribusi & implementasi perangkat jaringan' }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            {{ $partner->status }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                        <form action="{{ route('bdm.partnership.destroy', $partner->id) }}" method="POST" onsubmit="return confirm('Hapus data kemitraan ini?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="px-2.5 py-1 bg-white hover:bg-red-50 text-red-600 hover:text-red-700 text-[11.5px] font-semibold rounded-lg border border-red-200 shadow-xs transition">
                                                Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-10 text-center text-gray-400 text-xs">
                                        Belum ada data kemitraan yang sesuai filter.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($partners->hasPages())
                    <div class="p-3.5 bg-[#FAF9F8] border-t border-[#E7E5E3] flex items-center justify-between text-xs">
                        <div class="text-gray-500 font-medium">
                            Menampilkan <span class="font-bold text-gray-800">{{ $partners->firstItem() }}</span> - <span class="font-bold text-gray-800">{{ $partners->lastItem() }}</span> dari <span class="font-bold text-gray-800">{{ $partners->total() }}</span> mitra
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
             class="fixed inset-0 z-50 bg-[#0E0D12]/60 flex items-center justify-center p-3 sm:p-5 backdrop-blur-xs"
             @click.self="isPartnerModalOpen = false">
            <div class="bg-white rounded-2xl w-[600px] max-w-full max-h-[85vh] flex flex-col overflow-hidden shadow-[0_20px_60px_rgba(14,13,18,0.2)] border border-[#E7E5E3] my-auto animate-fade-in-up">
                
                {{-- Modal Header (Fixed) --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-[#E7E5E3] flex-shrink-0 bg-white">
                    <div>
                        <h3 class="font-display text-[17px] font-bold text-[#17151C]">Daftarkan Kemitraan & Prinsipal</h3>
                        <p class="text-[12px] text-[#75727C] mt-0.5">Kerjasama vendor teknologi, tingkatan tier & proteksi harga.</p>
                    </div>
                    <button type="button" @click="isPartnerModalOpen = false" class="rounded-lg p-1.5 text-[#75727C] hover:text-[#17151C] hover:bg-[#F1F0EE] transition-colors cursor-pointer">
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
                            <label class="block text-[12px] font-semibold text-[#17151C] mb-1">Nama Mitra / Prinsipal <span class="text-[#C81E2C]">*</span></label>
                            <input type="text" name="partner_name" required class="w-full bg-white border border-[#E7E5E3] rounded-lg px-3 py-2 text-[13px] text-[#17151C] outline-none focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all" placeholder="e.g. Fortinet Indonesia">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[12px] font-semibold text-[#17151C] mb-1">Tipe Mitra <span class="text-[#C81E2C]">*</span></label>
                                <select name="partner_type" required class="w-full bg-white border border-[#E7E5E3] rounded-lg px-3 py-2 text-[13px] text-[#17151C] outline-none focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all cursor-pointer">
                                    <option value="Principal">Principal (Vendor)</option>
                                    <option value="Distributor">Distributor Resmi</option>
                                    <option value="Telco / ISP Provider">Telco / ISP Provider</option>
                                    <option value="Technology Partner">Technology Partner</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[12px] font-semibold text-[#17151C] mb-1">Tier / Level Kemitraan <span class="text-[#C81E2C]">*</span></label>
                                <select name="tier_level" required class="w-full bg-white border border-[#E7E5E3] rounded-lg px-3 py-2 text-[13px] text-[#17151C] outline-none focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all cursor-pointer">
                                    <option value="Platinum Partner">Platinum Partner</option>
                                    <option value="Gold Partner" selected>Gold Partner</option>
                                    <option value="Authorized Partner">Authorized Partner</option>
                                    <option value="Strategic Distributor">Strategic Distributor</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-[11px] font-semibold text-[#75727C] mb-1">Nama PIC</label>
                                <input type="text" name="pic_name" class="w-full bg-white border border-[#E7E5E3] rounded-lg px-2.5 py-1.5 text-[12px] text-[#17151C] outline-none focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all" placeholder="Nama PIC">
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-[#75727C] mb-1">No. Kontak</label>
                                <input type="text" name="pic_contact" class="w-full bg-white border border-[#E7E5E3] rounded-lg px-2.5 py-1.5 text-[12px] text-[#17151C] outline-none focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all" placeholder="0812xxxxxxxx">
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-[#75727C] mb-1">Email</label>
                                <input type="email" name="pic_email" class="w-full bg-white border border-[#E7E5E3] rounded-lg px-2.5 py-1.5 text-[12px] text-[#17151C] outline-none focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all" placeholder="pic@vendor.com">
                            </div>
                        </div>

                        <div>
                            <label class="block text-[12px] font-semibold text-[#17151C] mb-1">Ruang Lingkup Kerjasama / Benefit</label>
                            <textarea name="collaboration_scope" rows="2.5" class="w-full bg-white border border-[#E7E5E3] rounded-lg p-2.5 text-[13px] text-[#17151C] outline-none focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all" placeholder="Hak deal registration, unit demo POC, proteksi harga diskon partner, target tahunan..."></textarea>
                        </div>
                    </form>
                </div>

                {{-- Modal Footer (Fixed) --}}
                <div class="flex items-center gap-3 px-6 py-3.5 border-t border-[#E7E5E3] bg-[#FAF9F8] flex-shrink-0">
                    <button type="submit" 
                            form="partnerForm" 
                            class="flex-1 flex items-center justify-center min-h-[40px] bg-[#C81E2C] hover:bg-[#AF1424] text-white shadow-[0_8px_20px_rgba(200,30,44,0.24)] px-4 py-2 rounded-xl font-semibold text-[13px] active:translate-y-[1px] transition-all cursor-pointer">
                        Daftarkan Mitra
                    </button>
                    <button type="button" 
                            @click="isPartnerModalOpen = false" 
                            class="flex-1 flex items-center justify-center min-h-[40px] bg-white hover:bg-[#F8F7F6] text-[#3D3A44] border border-[#E7E5E3] shadow-[0_1px_2px_rgba(14,13,18,0.05)] px-4 py-2 rounded-xl font-semibold text-[13px] active:translate-y-[1px] transition-all cursor-pointer">
                        Batal
                    </button>
                </div>

            </div>
        </div>
    </template>

</div>
@endsection
