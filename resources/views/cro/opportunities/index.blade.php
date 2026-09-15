@extends('layouts.app')

@section('title', 'Peluang Ekspansi Akun - CRO')

@section('content')
<div class="flex h-screen overflow-hidden" x-data="croOppHandler()" x-cloak>
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto bg-[#FAF9F8]">
        @include('components.topbar', ['title' => 'Account Development & Opportunity Bridge'])
        
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
                <form method="GET" action="{{ route('cro.opportunities.index') }}" class="flex flex-col sm:flex-row flex-wrap gap-2.5 w-full sm:w-auto">
                    <div class="relative">
                        <svg class="w-3.5 h-3.5 absolute left-3 top-3 text-[#948F99]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" 
                               name="client" 
                               value="{{ request('client') }}"
                               placeholder="Cari Klien / Perusahaan..." 
                               class="w-full sm:w-64 px-[11px] py-[9px] pl-8 rounded-lg border border-[#E7E5E3] text-[13px] text-[#17151C] outline-none bg-white focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all">
                    </div>
                    <select name="type" onchange="this.form.submit()" 
                            class="w-full sm:w-56 px-[11px] py-[9px] rounded-lg border border-[#E7E5E3] text-[13px] text-[#17151C] outline-none bg-white focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all cursor-pointer">
                        <option value="">Semua Tipe Peluang</option>
                        <option value="Renewal" {{ request('type') == 'Renewal' ? 'selected' : '' }}>Renewal (Perpanjangan)</option>
                        <option value="Upsell Bandwidth" {{ request('type') == 'Upsell Bandwidth' ? 'selected' : '' }}>Upsell Bandwidth / Kapasitas</option>
                        <option value="Cross-Sell New Site" {{ request('type') == 'Cross-Sell New Site' ? 'selected' : '' }}>Cross-Sell New Site / Branch</option>
                        <option value="Hardware / License Addon" {{ request('type') == 'Hardware / License Addon' ? 'selected' : '' }}>Hardware / License Addon</option>
                        <option value="New Solution" {{ request('type') == 'New Solution' ? 'selected' : '' }}>New Solution</option>
                    </select>
                    <select name="status" onchange="this.form.submit()" 
                            class="w-full sm:w-44 px-[11px] py-[9px] rounded-lg border border-[#E7E5E3] text-[13px] text-[#17151C] outline-none bg-white focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all cursor-pointer">
                        <option value="">Semua Status</option>
                        <option value="Identified" {{ request('status') == 'Identified' ? 'selected' : '' }}>Teridentifikasi</option>
                        <option value="Handed Over" {{ request('status') == 'Handed Over' ? 'selected' : '' }}>Handover ke Sales</option>
                        <option value="In Sales Pipeline" {{ request('status') == 'In Sales Pipeline' ? 'selected' : '' }}>Di Pipeline Sales</option>
                        <option value="Won" {{ request('status') == 'Won' ? 'selected' : '' }}>Won / Deal Baru</option>
                        <option value="Dropped" {{ request('status') == 'Dropped' ? 'selected' : '' }}>Dropped</option>
                    </select>
                    @if(request('client') || request('type') || request('status'))
                        <a href="{{ route('cro.opportunities.index') }}" 
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
                    <span>Catat Peluang Ekspansi Baru</span>
                </button>
            </div>

            {{-- Table Opportunities --}}
            <div class="bg-white rounded-xl border border-[#E7E5E3] shadow-[0_1px_2px_rgba(14,13,18,0.05)] overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[1050px] border-collapse text-[13px]">
                        <thead>
                            <tr class="bg-[#F1F0EE] border-b border-[#E7E5E3]">
                                <th class="text-left py-3.5 px-4 text-[11px] font-semibold text-[#75727C] uppercase tracking-[0.3px] w-[20%]">Klien / Akun</th>
                                <th class="text-center py-3.5 px-3 text-[11px] font-semibold text-[#75727C] uppercase tracking-[0.3px] w-[14%] whitespace-nowrap">Tipe Peluang</th>
                                <th class="text-left py-3.5 px-4 text-[11px] font-semibold text-[#75727C] uppercase tracking-[0.3px] w-[28%]">Judul & Kebutuhan</th>
                                <th class="text-right py-3.5 px-4 text-[11px] font-semibold text-[#75727C] uppercase tracking-[0.3px] w-[14%] whitespace-nowrap">Estimasi Nilai</th>
                                <th class="text-left py-3.5 px-4 text-[11px] font-semibold text-[#75727C] uppercase tracking-[0.3px] w-[12%]">Sales / AM</th>
                                <th class="text-center py-3.5 px-3 text-[11px] font-semibold text-[#75727C] uppercase tracking-[0.3px] w-[12%] whitespace-nowrap">Status</th>
                                <th class="text-right py-3.5 px-4 text-[11px] font-semibold text-[#75727C] uppercase tracking-[0.3px] w-[10%] whitespace-nowrap">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#EFEDEB]">
                            @forelse($opportunities as $opp)
                                <tr class="hover:bg-[#F8F7F6] transition-colors">
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-[#17151C] text-[13px] leading-snug">{{ $opp->client_name }}</div>
                                        <div class="text-[11px] text-[#75727C] mt-0.5">
                                            Ditemukan: {{ $opp->created_at ? $opp->created_at->format('d M Y') : '-' }}
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-3 text-center whitespace-nowrap">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10.5px] font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                            {{ $opp->opportunity_type }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <div class="font-semibold text-gray-900 text-[13px] leading-snug">{{ $opp->title }}</div>
                                        @if($opp->requirement_notes)
                                            <p class="text-[11.5px] text-[#75727C] mt-1 line-clamp-2" title="{{ $opp->requirement_notes }}">{{ $opp->requirement_notes }}</p>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-right whitespace-nowrap font-bold text-emerald-600 text-[13px]">
                                        Rp {{ number_format($opp->estimated_value, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <div class="text-gray-800 font-semibold text-[12.5px]">
                                            {{ $opp->salesPic ? $opp->salesPic->name : 'Belum Ditunjuk' }}
                                        </div>
                                        @if($opp->handed_over_at)
                                            <div class="text-[10.5px] text-gray-400">Handover: {{ $opp->handed_over_at->format('d M Y') }}</div>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-3 text-center whitespace-nowrap">
                                        @php $badge = $opp->status_badge; @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10.5px] font-bold border {{ $badge['bg'] }} {{ $badge['text'] }} {{ $badge['border'] }}">
                                            {{ $badge['label'] }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-right whitespace-nowrap space-x-1">
                                        @if($opp->status === 'Identified')
                                            <button @click="openHandoverModal({{ $opp->id }}, '{{ addslashes($opp->title) }}')"
                                                    class="px-2.5 py-1 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-lg shadow-2xs transition text-[11px] cursor-pointer">
                                                Handover
                                            </button>
                                        @endif

                                        <form action="{{ route('cro.opportunities.destroy', $opp->id) }}" method="POST" onsubmit="return confirm('Hapus peluang ini?')" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1 text-gray-400 hover:text-rose-600 rounded-lg hover:bg-rose-50 transition cursor-pointer" title="Hapus">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-10 text-center text-gray-400 text-xs">
                                        Belum ada data peluang ekspansi akun yang tercatat.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($opportunities->hasPages())
                    <div class="p-4 border-t border-[#EFEDEB]">
                        {{ $opportunities->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>

    {{-- MODAL 1: Create Opportunity --}}
    <div x-show="isCreateModalOpen" class="fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 space-y-4 border border-[#E7E5E3] shadow-xl animate-fade-in-up" @click.outside="isCreateModalOpen = false">
            <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                <h3 class="text-base font-bold text-gray-900">Catat Peluang Ekspansi Akun (CRO)</h3>
                <button @click="isCreateModalOpen = false" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>

            <form action="{{ route('cro.opportunities.store') }}" method="POST" class="space-y-3.5 text-xs">
                @csrf
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Nama Klien / Perusahaan *</label>
                    <input type="text" name="client_name" list="clientList" required class="wms-input" placeholder="Pilih atau ketik nama klien">
                    <datalist id="clientList">
                        @foreach($clients as $c)
                            <option value="{{ $c->name }}"></option>
                        @endforeach
                    </datalist>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Tipe Peluang *</label>
                        <select name="opportunity_type" required class="wms-input font-bold">
                            <option value="Renewal">Renewal (Perpanjangan)</option>
                            <option value="Upsell Bandwidth">Upsell Bandwidth / Kapasitas</option>
                            <option value="Cross-Sell New Site">Cross-Sell New Site / Branch</option>
                            <option value="Hardware / License Addon">Hardware / License Addon</option>
                            <option value="New Solution">New Solution</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Estimasi Nilai Potensial (Rp)</label>
                        <input type="number" step="1000" name="estimated_value" class="wms-input" placeholder="e.g. 500000000">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Judul Peluang / Scope Singkat *</label>
                    <input type="text" name="title" required class="wms-input" placeholder="e.g. Upgrade Bandwidth Metro 1Gbps untuk DRC Surabaya">
                </div>

                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Catatan Kebutuhan Klien</label>
                    <textarea name="requirement_notes" rows="3" class="wms-input" placeholder="Jelaskan kebutuhan spesifik, timeline yang diminta klien, dsb..."></textarea>
                </div>

                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Assign ke Sales / AM (Opsional)</label>
                    <select name="handed_over_to" class="wms-input">
                        <option value="">-- Nanti Saja / Disimpan Dulu --</option>
                        @foreach($salesUsers as $u)
                            <option value="{{ $u->id }}">{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="pt-3 border-t border-gray-100 flex justify-end gap-2">
                    <button type="button" @click="isCreateModalOpen = false" class="px-4 py-2 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 cursor-pointer">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-[#C81E2C] text-white font-bold rounded-xl hover:brightness-105 shadow-sm cursor-pointer">Simpan Peluang</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL 2: Handover to Sales --}}
    <div x-show="isHandoverModalOpen" class="fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4 border border-[#E7E5E3] shadow-xl animate-fade-in-up" @click.outside="isHandoverModalOpen = false">
            <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                <h3 class="text-base font-bold text-gray-900">Handover Peluang ke Sales</h3>
                <button @click="isHandoverModalOpen = false" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>

            <form :action="'/cro/opportunities/' + handoverData.id + '/handover'" method="POST" class="space-y-3.5 text-xs">
                @csrf
                <p class="text-gray-700 font-semibold mb-1" x-text="handoverData.title"></p>

                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Pilih PIC Sales / Account Manager *</label>
                    <select name="handed_over_to" required class="wms-input font-bold">
                        <option value="">-- Pilih Sales PIC --</option>
                        @foreach($salesUsers as $u)
                            <option value="{{ $u->id }}">{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="p-3 bg-indigo-50 border border-indigo-200 rounded-xl text-indigo-800 text-[11.5px]">
                    ℹ️ Peluang ini akan dialihkan ke tim Sales/BDM untuk ditindaklanjuti menjadi penawaran harga & proposal resmi.
                </div>

                <div class="pt-3 border-t border-gray-100 flex justify-end gap-2">
                    <button type="button" @click="isHandoverModalOpen = false" class="px-4 py-2 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 cursor-pointer">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 shadow-sm cursor-pointer">Kirim ke Sales</button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
function croOppHandler() {
    return {
        isCreateModalOpen: false,
        isHandoverModalOpen: false,
        handoverData: { id: null, title: '' },
        openHandoverModal(id, title) {
            this.handoverData = { id, title };
            this.isHandoverModalOpen = true;
        }
    };
}
</script>
@endsection
