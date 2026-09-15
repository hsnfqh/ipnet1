@extends('layouts.app')

@section('title', 'Aktivitas Sales & CRM - Sales Portal')

@section('content')
<div class="flex h-screen overflow-hidden" x-data="{ isAddModalOpen: false }">
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto bg-[#FAF9F8]">
        @include('components.topbar', ['title' => 'Aktivitas Sales'])
        
        <div class="p-4 sm:p-5 lg:p-[26px] animate-fade-in space-y-5 max-w-[1600px] mx-auto">
            
            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 font-bold px-2">✕</button>
                </div>
            @endif

            <!-- Filter & Action Bar -->
            <div class="flex flex-col sm:flex-row sm:flex-wrap sm:justify-between sm:items-center gap-2.5">
                <form method="GET" action="{{ route('sales.activities.index') }}" class="flex flex-col sm:flex-row flex-wrap gap-2.5 w-full sm:w-auto">
                    <div class="relative">
                        <svg class="w-3.5 h-3.5 absolute left-3 top-3 text-[#948F99]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" 
                               name="search" 
                               value="{{ $search }}"
                               placeholder="Cari subjek, catatan, klien..." 
                               class="w-full sm:w-64 px-[11px] py-[9px] pl-8 rounded-lg border border-[#E7E5E3] text-[13.5px] text-[#17151C] outline-none bg-white focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all">
                    </div>

                    <select name="type" onchange="this.form.submit()" 
                            class="w-full sm:w-48 px-[11px] py-[9px] rounded-lg border border-[#E7E5E3] text-[13.5px] text-[#17151C] outline-none bg-white focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all cursor-pointer">
                        <option value="">Semua Tipe Aktivitas</option>
                        @foreach($activityTypes as $typeKey => $typeLabel)
                            <option value="{{ $typeKey }}" {{ $filterType == $typeKey ? 'selected' : '' }}>{{ $typeLabel }}</option>
                        @endforeach
                    </select>

                    <select name="project_id" onchange="this.form.submit()" 
                            class="w-full sm:w-56 px-[11px] py-[9px] rounded-lg border border-[#E7E5E3] text-[13.5px] text-[#17151C] outline-none bg-white focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all cursor-pointer truncate">
                        <option value="">Semua Prospek</option>
                        @foreach($activeProjects as $p)
                            <option value="{{ $p->id }}" {{ $filterProject == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                        @endforeach
                    </select>

                    @if($search || $filterType || $filterProject)
                        <a href="{{ route('sales.activities.index') }}" 
                           class="px-3 py-2 text-[13px] font-semibold text-gray-500 hover:text-gray-700 self-center">
                            Reset
                        </a>
                    @endif
                </form>

                <button type="button" @click="isAddModalOpen = true" 
                        class="w-full sm:w-auto justify-center bg-[#C81E2C] text-white shadow-[0_8px_20px_rgba(200,30,44,0.24)] px-[17px] py-[9px] rounded-lg font-semibold text-[13.5px] flex items-center gap-1.5 hover:brightness-105 active:translate-y-[1px] transition-all cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Catat Aktivitas CRM</span>
                </button>
            </div>

            {{-- Activity Feed Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @forelse($activities as $act)
                    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-[0_1px_3px_rgba(0,0,0,0.03)] flex flex-col justify-between space-y-3 hover:border-[#C81E2C]/30 transition">
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="px-2.5 py-1 rounded-lg text-[10.5px] font-semibold bg-[#FAF9F8] border border-gray-200 text-gray-700">
                                    {{ $act->activity_type }}
                                </span>
                                <span class="text-xs font-semibold text-gray-400">
                                    {{ \Carbon\Carbon::parse($act->activity_date)->format('d M Y, H:i') }}
                                </span>
                            </div>

                            <h3 class="text-sm font-bold text-gray-900 leading-snug">
                                {{ $act->subject }}
                            </h3>

                            <div class="text-[11.5px] text-gray-500 flex items-center gap-1.5 flex-wrap">
                                <span class="font-bold text-gray-900">{{ $act->project->name ?? 'Proyek' }}</span>
                                <span>•</span>
                                <span>{{ $act->project->client ?? 'Klien' }}</span>
                            </div>

                            @if($act->notes)
                                <p class="text-xs text-gray-700 bg-[#FAF9F8] p-3 rounded-xl border border-gray-100 leading-relaxed whitespace-pre-line">
                                    {{ $act->notes }}
                                </p>
                            @endif

                            @if($act->next_action)
                                <div class="p-2.5 rounded-xl bg-amber-50/70 border border-amber-200 text-xs text-amber-900 space-y-1">
                                    <div class="flex items-center justify-between font-bold">
                                        <span>Next Action:</span>
                                        @if($act->next_action_date)
                                            <span class="text-[11px]">{{ \Carbon\Carbon::parse($act->next_action_date)->format('d M Y') }}</span>
                                        @endif
                                    </div>
                                    <div>{{ $act->next_action }}</div>
                                </div>
                            @endif
                        </div>

                        <div class="flex items-center justify-between pt-2 border-t border-gray-50 text-[11px] text-gray-400">
                            <span>Dicatat oleh: <strong class="text-gray-800">{{ $act->sales->name ?? 'Sales' }}</strong></span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700">
                                {{ $act->status }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full bg-white p-12 rounded-2xl border border-gray-100 text-center text-xs text-gray-400 space-y-2">
                        <p class="font-bold text-gray-900">Belum ada aktivitas CRM yang tercatat.</p>
                        <p>Klik tombol "+ Catat Aktivitas CRM" di atas untuk mencatat interaksi klien pertama.</p>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            @if($activities->hasPages())
                <div class="p-4 bg-white rounded-2xl border border-gray-100">
                    {{ $activities->links() }}
                </div>
            @endif

        </div>
    </div>

    {{-- MODAL ADD SALES CRM ACTIVITY (BUSDEV/ENGINEER STANDARD) --}}
    <template x-teleport="body">
        <div x-show="isAddModalOpen" 
             x-cloak 
             class="fixed inset-0 z-50 bg-[#0E0D12]/60 flex items-center justify-center p-3 sm:p-5 backdrop-blur-xs"
             @click.self="isAddModalOpen = false">
            <div class="bg-white rounded-2xl w-[760px] max-w-full max-h-[88vh] flex flex-col overflow-hidden shadow-[0_20px_60px_rgba(14,13,18,0.2)] border border-[#E7E5E3] my-auto animate-fade-in-up">
                
                {{-- Modal Header (Fixed) --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-[#E7E5E3] flex-shrink-0 bg-white">
                    <div>
                        <h3 class="font-display text-[17px] font-bold text-[#17151C]">Catat Aktivitas Interaksi Prospek</h3>
                        <p class="text-[12px] text-[#75727C] mt-0.5">Dokumentasikan riwayat meeting, telepon, follow up, atau negosiasi dengan klien.</p>
                    </div>
                    <button type="button" @click="isAddModalOpen = false" class="rounded-lg p-1.5 text-[#75727C] hover:text-[#17151C] hover:bg-[#F1F0EE] transition-colors cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Modal Body (Scrollable) --}}
                <div class="px-6 py-4 overflow-y-auto flex-1 space-y-3.5 text-xs">
                    <form id="activityForm" 
                          action="{{ route('sales.activities.store') }}" 
                          method="POST" 
                          class="space-y-3.5">
                        @csrf
                        
                        {{-- Section 1: Prospek & Jenis Interaksi --}}
                        <div class="bg-[#FAF9F8] p-3.5 rounded-xl border border-[#E7E5E3] space-y-2.5">
                            <div class="font-bold text-[#17151C] text-[12.5px] flex items-center gap-2">
                                <span class="w-4.5 h-4.5 rounded-full bg-[#C81E2C] text-white flex items-center justify-center text-[10px] font-bold">1</span>
                                <span>Target Prospek & Waktu Interaksi</span>
                            </div>

                            <div>
                                <label class="block text-[11px] font-semibold text-[#17151C] mb-1">
                                    Pilih Prospek / Proyek <span class="text-[#C81E2C]">*</span>
                                </label>
                                <select name="project_id" required
                                        class="w-full bg-white border border-[#E7E5E3] rounded-lg px-3 py-2 text-[12.5px] text-[#17151C] outline-none focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all font-medium">
                                    <option value="">-- Pilih Prospek --</option>
                                    @foreach($activeProjects as $p)
                                        <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->client }}) - PIC: {{ $p->sales_name ?: 'Sales' }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                <div>
                                    <label class="block text-[11px] font-semibold text-[#17151C] mb-1">
                                        Tipe Aktivitas <span class="text-[#C81E2C]">*</span>
                                    </label>
                                    <select name="activity_type" required
                                            class="w-full bg-white border border-[#E7E5E3] rounded-lg px-3 py-2 text-[12.5px] text-[#17151C] outline-none focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all">
                                        @foreach($activityTypes as $typeKey => $typeLabel)
                                            <option value="{{ $typeKey }}">{{ $typeLabel }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-[11px] font-semibold text-[#17151C] mb-1">
                                        Tanggal & Waktu Aktivitas <span class="text-[#C81E2C]">*</span>
                                    </label>
                                    <input type="datetime-local" name="activity_date" value="{{ date('Y-m-d\TH:i') }}" required
                                           class="w-full bg-white border border-[#E7E5E3] rounded-lg px-3 py-2 text-[12.5px] text-[#17151C] outline-none focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all font-medium">
                                </div>
                            </div>
                        </div>

                        {{-- Section 2: Hasil Pembahasan --}}
                        <div class="bg-[#FAF9F8] p-3.5 rounded-xl border border-[#E7E5E3] space-y-2.5">
                            <div class="font-bold text-[#17151C] text-[12.5px] flex items-center gap-2">
                                <span class="w-4.5 h-4.5 rounded-full bg-[#C81E2C] text-white flex items-center justify-center text-[10px] font-bold">2</span>
                                <span>Subjek & Catatan Pembahasan</span>
                            </div>

                            <div>
                                <label class="block text-[11px] font-semibold text-[#17151C] mb-1">
                                    Subjek / Judul Interaksi <span class="text-[#C81E2C]">*</span>
                                </label>
                                <input type="text" name="subject" placeholder="Contoh: Meeting Klarifikasi BoQ Firewall & Franco Pengiriman" required
                                       class="w-full bg-white border border-[#E7E5E3] rounded-lg px-3 py-2 text-[12.5px] text-[#17151C] outline-none focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all">
                            </div>

                            <div>
                                <label class="block text-[11px] font-semibold text-[#17151C] mb-1">Hasil Pembahasan & Catatan Detail</label>
                                <textarea name="notes" rows="3" placeholder="Tuliskan poin penting hasil meeting, feedback dari user, atau penyesuaian harga..."
                                          class="w-full bg-white border border-[#E7E5E3] rounded-lg p-2.5 text-[12.5px] text-[#17151C] outline-none focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all"></textarea>
                            </div>
                        </div>

                        {{-- Section 3: Next Action --}}
                        <div class="bg-[#FAF9F8] p-3.5 rounded-xl border border-[#E7E5E3] space-y-2.5">
                            <div class="font-bold text-[#17151C] text-[12.5px] flex items-center gap-2">
                                <span class="w-4.5 h-4.5 rounded-full bg-[#C81E2C] text-white flex items-center justify-center text-[10px] font-bold">3</span>
                                <span>Tindak Lanjut Berikutnya (Next Action)</span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                <div>
                                    <label class="block text-[11px] font-semibold text-[#75727C] mb-1">Tindak Lanjut Selanjutnya (Next Action)</label>
                                    <input type="text" name="next_action" placeholder="Contoh: Kirim revisi quotation & draft SPK"
                                           class="w-full bg-white border border-[#E7E5E3] rounded-lg px-3 py-2 text-[12.5px] text-[#17151C] outline-none focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all">
                                </div>

                                <div>
                                    <label class="block text-[11px] font-semibold text-[#75727C] mb-1">Target Tanggal Next Action</label>
                                    <input type="date" name="next_action_date"
                                           class="w-full bg-white border border-[#E7E5E3] rounded-lg px-3 py-2 text-[12.5px] text-[#17151C] outline-none focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all font-medium">
                                </div>
                            </div>
                        </div>

                    </form>
                </div>

                {{-- Modal Footer (Fixed) --}}
                <div class="flex items-center gap-3 px-6 py-3.5 border-t border-[#E7E5E3] bg-[#FAF9F8] flex-shrink-0">
                    <button type="submit" 
                            form="activityForm" 
                            class="flex-1 flex items-center justify-center min-h-[40px] bg-[#C81E2C] hover:bg-[#AF1424] text-white shadow-[0_8px_20px_rgba(200,30,44,0.24)] px-4 py-2 rounded-xl font-semibold text-[13px] active:translate-y-[1px] transition-all cursor-pointer">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Simpan Log Aktivitas</span>
                    </button>
                    <button type="button" 
                            @click="isAddModalOpen = false" 
                            class="flex-1 flex items-center justify-center min-h-[40px] bg-white hover:bg-[#F8F7F6] text-[#3D3A44] border border-[#E7E5E3] shadow-[0_1px_2px_rgba(14,13,18,0.05)] px-4 py-2 rounded-xl font-semibold text-[13px] active:translate-y-[1px] transition-all cursor-pointer">
                        Batal
                    </button>
                </div>

            </div>
        </div>
    </template>

            </div>
        </div>
    </template>

</div>
@endsection
