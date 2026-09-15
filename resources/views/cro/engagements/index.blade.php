@extends('layouts.app')

@section('title', 'Log Relasi & Meeting Klien - CRO')

@section('content')
<div class="flex h-screen overflow-hidden" x-data="{ isModalOpen: false }" x-cloak>
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto bg-[#FAF9F8]">
        @include('components.topbar', ['title' => 'Customer Relationship & Meeting Logs'])
        
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
                <form method="GET" action="{{ route('cro.engagements.index') }}" class="flex flex-col sm:flex-row flex-wrap gap-2.5 w-full sm:w-auto">
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
                            class="w-full sm:w-48 px-[11px] py-[9px] rounded-lg border border-[#E7E5E3] text-[13px] text-[#17151C] outline-none bg-white focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all cursor-pointer">
                        <option value="">Semua Tipe Interaksi</option>
                        <option value="Meeting" {{ request('type') == 'Meeting' ? 'selected' : '' }}>Meeting Resmi</option>
                        <option value="QBR Review" {{ request('type') == 'QBR Review' ? 'selected' : '' }}>QBR Review</option>
                        <option value="Courtesy Visit" {{ request('type') == 'Courtesy Visit' ? 'selected' : '' }}>Courtesy Visit</option>
                        <option value="Lunch / Coffee" {{ request('type') == 'Lunch / Coffee' ? 'selected' : '' }}>Lunch / Coffee</option>
                        <option value="Phone Call" {{ request('type') == 'Phone Call' ? 'selected' : '' }}>Phone / Video Call</option>
                        <option value="Site Visit" {{ request('type') == 'Site Visit' ? 'selected' : '' }}>Site Visit</option>
                    </select>
                    <select name="sentiment" onchange="this.form.submit()" 
                            class="w-full sm:w-44 px-[11px] py-[9px] rounded-lg border border-[#E7E5E3] text-[13px] text-[#17151C] outline-none bg-white focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all cursor-pointer">
                        <option value="">Semua Sentimen</option>
                        <option value="Positive" {{ request('sentiment') == 'Positive' ? 'selected' : '' }}>Positif</option>
                        <option value="Neutral" {{ request('sentiment') == 'Neutral' ? 'selected' : '' }}>Netral</option>
                        <option value="Concerned" {{ request('sentiment') == 'Concerned' ? 'selected' : '' }}>Ada Perhatian</option>
                        <option value="Negative" {{ request('sentiment') == 'Negative' ? 'selected' : '' }}>Kritis / Negatif</option>
                    </select>
                    @if(request('client') || request('type') || request('sentiment'))
                        <a href="{{ route('cro.engagements.index') }}" 
                           class="px-3 py-2 text-[13px] font-semibold text-gray-500 hover:text-gray-700 self-center">
                            Reset
                        </a>
                    @endif
                </form>

                <button type="button" @click="isModalOpen = true" 
                        class="w-full sm:w-auto justify-center bg-[#C81E2C] text-white shadow-[0_8px_20px_rgba(200,30,44,0.24)] px-[17px] py-[9px] rounded-lg font-semibold text-[13px] flex items-center gap-1.5 hover:brightness-105 active:translate-y-[1px] transition-all cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Catat Pertemuan Baru</span>
                </button>
            </div>

            {{-- Engagements Table --}}
            <div class="bg-white rounded-xl border border-[#E7E5E3] shadow-[0_1px_2px_rgba(14,13,18,0.05)] overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[1000px] border-collapse text-[13px]">
                        <thead>
                            <tr class="bg-[#F1F0EE] border-b border-[#E7E5E3]">
                                <th class="text-left py-3.5 px-4 text-[11px] font-semibold text-[#75727C] uppercase tracking-[0.3px] w-[14%] whitespace-nowrap">Tanggal</th>
                                <th class="text-left py-3.5 px-4 text-[11px] font-semibold text-[#75727C] uppercase tracking-[0.3px] w-[22%]">Klien & PIC</th>
                                <th class="text-left py-3.5 px-4 text-[11px] font-semibold text-[#75727C] uppercase tracking-[0.3px] w-[32%]">Tipe & Topik Diskusi</th>
                                <th class="text-center py-3.5 px-3 text-[11px] font-semibold text-[#75727C] uppercase tracking-[0.3px] w-[12%] whitespace-nowrap">Sentimen</th>
                                <th class="text-left py-3.5 px-4 text-[11px] font-semibold text-[#75727C] uppercase tracking-[0.3px] w-[15%]">Action Items</th>
                                <th class="text-right py-3.5 px-4 text-[11px] font-semibold text-[#75727C] uppercase tracking-[0.3px] w-[5%] whitespace-nowrap">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#EFEDEB]">
                            @forelse($engagements as $eng)
                                <tr class="hover:bg-[#F8F7F6] transition-colors">
                                    <td class="py-3.5 px-4 whitespace-nowrap text-gray-600 font-medium text-[12.5px]">
                                        {{ $eng->engagement_date ? $eng->engagement_date->format('d M Y') : '-' }}
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-[#17151C] text-[13px] leading-snug">{{ $eng->client_name }}</div>
                                        <div class="text-[11.5px] text-[#75727C] mt-0.5">
                                            {{ $eng->pic_name ?: '-' }} {{ $eng->pic_contact ? "({$eng->pic_contact})" : '' }}
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <div class="flex items-center gap-1.5 mb-1">
                                            <span class="px-1.5 py-0.5 rounded bg-gray-100 text-gray-700 text-[10.5px] font-bold">
                                                {{ $eng->engagement_type }}
                                            </span>
                                            @if($eng->location)
                                                <span class="text-[11px] text-gray-400">📍 {{ $eng->location }}</span>
                                            @endif
                                        </div>
                                        <div class="font-semibold text-gray-900 text-[13px]">{{ $eng->title }}</div>
                                        @if($eng->discussion_summary)
                                            <p class="text-[12px] text-gray-500 mt-1 line-clamp-2">{{ $eng->discussion_summary }}</p>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-3 text-center whitespace-nowrap">
                                        @php $badge = $eng->sentiment_badge; @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold border {{ $badge['bg'] }} {{ $badge['text'] }} {{ $badge['border'] }}">
                                            {{ $badge['label'] }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-[12px] text-gray-700">
                                        <div class="line-clamp-2" title="{{ $eng->action_items ?: '-' }}">
                                            {{ $eng->action_items ?: '-' }}
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                        <form action="{{ route('cro.engagements.destroy', $eng->id) }}" method="POST" onsubmit="return confirm('Hapus log pertemuan ini?')" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-gray-400 hover:text-rose-600 rounded-lg hover:bg-rose-50 transition cursor-pointer" title="Hapus">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-10 text-center text-gray-400 text-xs">
                                        Belum ada catatan interaksi/meeting yang tercatat.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($engagements->hasPages())
                    <div class="p-4 border-t border-[#EFEDEB]">
                        {{ $engagements->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>

    {{-- Modal Tambah Meeting / Touchpoint --}}
    <div x-show="isModalOpen" class="fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 space-y-4 border border-[#E7E5E3] shadow-xl animate-fade-in-up" @click.outside="isModalOpen = false">
            <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                <h3 class="text-base font-bold text-gray-900">Catat Interaksi / Meeting Klien</h3>
                <button @click="isModalOpen = false" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>

            <form action="{{ route('cro.engagements.store') }}" method="POST" class="space-y-3.5 text-xs">
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
                        <label class="block font-semibold text-gray-700 mb-1">Tipe Interaksi *</label>
                        <select name="engagement_type" required class="wms-input">
                            <option value="Meeting">Meeting Resmi</option>
                            <option value="QBR Review">QBR Review</option>
                            <option value="Courtesy Visit">Courtesy Visit</option>
                            <option value="Lunch / Coffee">Lunch / Coffee</option>
                            <option value="Phone Call">Phone / Video Call</option>
                            <option value="Site Visit">Site Visit</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Tanggal *</label>
                        <input type="date" name="engagement_date" required class="wms-input" value="{{ date('Y-m-d') }}">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">PIC Klien yang Ditemui</label>
                        <input type="text" name="pic_name" class="wms-input" placeholder="e.g. Pak Hendra (VP IT)">
                    </div>
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Sentimen Hubungan *</label>
                        <select name="sentiment" required class="wms-input">
                            <option value="Positive">Positif</option>
                            <option value="Neutral">Netral</option>
                            <option value="Concerned">Ada Perhatian</option>
                            <option value="Negative">Negatif</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Judul / Topik Diskusi *</label>
                    <input type="text" name="title" required class="wms-input" placeholder="e.g. Evaluasi SLA Q3 & Perencanaan Kapasitas">
                </div>

                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Ringkasan Diskusi</label>
                    <textarea name="discussion_summary" rows="3" class="wms-input" placeholder="Poin-poin penting yang dibahas..."></textarea>
                </div>

                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Action Items / Kesepakatan Tindak Lanjut</label>
                    <textarea name="action_items" rows="2" class="wms-input" placeholder="Rencana aksi internal maupun ke klien..."></textarea>
                </div>

                <div class="pt-3 border-t border-gray-100 flex justify-end gap-2">
                    <button type="button" @click="isModalOpen = false" class="px-4 py-2 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 cursor-pointer">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-[#C81E2C] text-white font-bold rounded-xl hover:brightness-105 shadow-sm cursor-pointer">Simpan Notulen</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
