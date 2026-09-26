@extends('layouts.app')

@section('title', 'Aktivitas Sales & CRM - Sales Portal')

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

    .btn-ipnet-primary {
        background: linear-gradient(135deg, #8F0A0D 0%, #B81525 100%);
        color: #FFFFFF;
        font-weight: 700;
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
    .anim-delay-4 { animation-delay: 0.24s !important; }
</style>
@endpush

@section('content')
<div class="flex h-screen overflow-hidden bg-[#F8FAFC] font-sans" x-data="salesActivitiesManager()">
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Aktivitas Sales & CRM'])
        
        <div class="p-4 sm:p-6 lg:p-7 space-y-6 max-w-[1680px] mx-auto animate-fade-in">
            
            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 font-bold px-2">✕</button>
                </div>
            @endif

            <!-- Filter & Action Bar -->
            <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center gap-3 anim-fade-up anim-delay-1">
                <form method="GET" action="{{ route('sales.activities.index') }}" class="flex flex-col sm:flex-row flex-wrap gap-2.5 w-full lg:w-auto">
                    <div class="relative">
                        <svg class="w-4 h-4 absolute left-3.5 top-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" 
                               name="search" 
                               value="{{ $search }}"
                               placeholder="Cari subjek, catatan, klien..." 
                               class="w-full sm:w-72 px-3.5 py-2 pl-9 rounded-xl border border-gray-200 text-xs font-semibold text-gray-800 bg-white focus:outline-none focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D] transition-all shadow-xs">
                    </div>

                    <select name="type" onchange="this.form.submit()" 
                            class="w-full sm:w-48 px-3.5 py-2 rounded-xl border border-gray-200 text-xs font-semibold text-gray-800 bg-white focus:outline-none focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D] transition-all cursor-pointer shadow-xs">
                        <option value="">Semua Tipe Aktivitas</option>
                        @foreach($activityTypes as $typeKey => $typeLabel)
                            <option value="{{ $typeKey }}" {{ $filterType == $typeKey ? 'selected' : '' }}>{{ $typeLabel }}</option>
                        @endforeach
                    </select>

                    <select name="project_id" onchange="this.form.submit()" 
                            class="w-full sm:w-56 px-3.5 py-2 rounded-xl border border-gray-200 text-xs font-semibold text-gray-800 bg-white focus:outline-none focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D] transition-all cursor-pointer shadow-xs truncate">
                        <option value="">Semua Prospek / Proyek</option>
                        @foreach($activeProjects as $p)
                            <option value="{{ $p->id }}" {{ $filterProject == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                        @endforeach
                    </select>

                    @if($search || $filterType || $filterProject)
                        <a href="{{ route('sales.activities.index') }}" 
                           class="px-3 py-2 text-xs font-bold text-gray-500 hover:text-gray-800 self-center">
                            Reset Filter
                        </a>
                    @endif
                </form>                @php
                    $authUser = auth()->user();
                    $isExecutiveOrPimpinan = $authUser && (
                        str_contains(strtolower($authUser->name ?? ''), 'hariyadi')
                        || str_contains(strtolower($authUser->name ?? ''), 'susanto')
                        || $authUser->hasAnyRole(['Director', 'Direktur', 'HD / Direktur', 'Division Head', 'Head Divisi', 'Group Leader', 'Group Leader Commercial & Solution', 'Group Leader Delivery & Operation'])
                    );
                @endphp

                @if(!$isExecutiveOrPimpinan)
                <button type="button" @click="openBulkModal()" 
                        class="btn-ipnet-primary w-full sm:w-auto justify-center shadow-md px-4 py-2.5 rounded-xl font-bold text-xs flex items-center gap-2 transition-all cursor-pointer">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Input Kronologi</span>
                </button>
                @endif
            </div>

            {{-- Activity Feed Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 anim-fade-up anim-delay-2">
                @forelse($activities as $act)
                    <div class="ipnet-card p-5 flex flex-col justify-between space-y-3.5">
                        <div class="space-y-2.5">
                            <div class="flex items-center justify-between">
                                <span class="px-2.5 py-1 rounded-lg text-[10.5px] font-bold bg-[#F8FAFC] border border-gray-200 text-gray-800">
                                    {{ $act->activity_type }}
                                </span>
                                <span class="text-xs font-bold text-gray-400">
                                    {{ \Carbon\Carbon::parse($act->activity_date)->format('d M Y, H:i') }}
                                </span>
                            </div>

                            <h3 class="text-sm font-bold text-gray-900 leading-snug">
                                {{ $act->subject }}
                            </h3>

                            <div class="text-[11px] text-gray-500 flex items-center gap-1.5 flex-wrap">
                                <span class="font-bold text-gray-900">{{ $act->project->name ?? 'Proyek' }}</span>
                                <span>•</span>
                                <span>{{ $act->project->client ?? 'Klien' }}</span>
                            </div>

                            @if($act->notes)
                                <p class="text-xs text-gray-700 bg-[#F8FAFC] p-3 rounded-xl border border-gray-100 leading-relaxed whitespace-pre-line font-medium">
                                    {{ $act->notes }}
                                </p>
                            @endif

                            @if($act->next_action)
                                <div class="p-3 rounded-xl bg-amber-50/80 border border-amber-200 text-xs text-amber-900 space-y-1">
                                    <div class="flex items-center justify-between font-bold text-[11px]">
                                        <span>Tindak Lanjut Berikutnya:</span>
                                        @if($act->next_action_date)
                                            <span>{{ \Carbon\Carbon::parse($act->next_action_date)->format('d M Y') }}</span>
                                        @endif
                                    </div>
                                    <div class="text-amber-950 font-medium">{{ $act->next_action }}</div>
                                </div>
                            @endif
                        </div>

                        <div class="flex items-center justify-between pt-3 border-t border-gray-100 text-[11px]">
                            <div class="text-gray-400">
                                <span>Dicatat oleh: <strong class="text-gray-800">{{ $act->sales->name ?? 'Sales' }}</strong></span>
                            </div>

                            @if(!$isExecutiveOrPimpinan)
                            {{-- Action buttons: Edit & Hapus --}}
                            <div class="flex items-center gap-1.5">
                                <button type="button" 
                                        @click="openEditModal({{ json_encode($act) }})" 
                                        class="px-2.5 py-1 bg-white hover:bg-gray-50 text-gray-700 text-[11px] font-semibold rounded-lg border border-gray-200 shadow-xs transition inline-flex items-center gap-1">
                                    <svg class="w-3 h-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    <span>Edit</span>
                                </button>
                                <button type="button" 
                                        @click="confirmDelete({{ $act->id }}, '{{ addslashes($act->subject) }}')" 
                                        class="px-2.5 py-1 bg-white hover:bg-red-50 text-red-600 hover:text-red-700 text-[11px] font-semibold rounded-lg border border-red-200 shadow-xs transition inline-flex items-center gap-1">
                                    <svg class="w-3 h-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    <span>Hapus</span>
                                </button>
                            </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-full ipnet-card p-12 text-center text-xs text-gray-400 space-y-2">
                        <p class="font-bold text-gray-900">Belum ada aktivitas CRM yang tercatat.</p>
                        @if(!$isExecutiveOrPimpinan)
                        <p>Klik tombol "Input Kronologi" di atas untuk mencatat interaksi dan runtutan agenda aktivitas.</p>
                        @endif
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            @if($activities->hasPages())
                <div class="p-4 ipnet-card">
                    {{ $activities->links() }}
                </div>
            @endif

        </div>
    </div>

    {{-- MODAL INPUT KRONOLOGI MASSAL (SPREADSHEET MODE) --}}
    <template x-teleport="body">
        <div x-show="isBulkModalOpen" 
             x-cloak 
             class="fixed inset-0 z-50 bg-[#0F172A]/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-6"
             @click.self="isBulkModalOpen = false">
            <div class="bg-white rounded-2xl max-w-7xl w-full shadow-2xl border border-[#E2E8F0] max-h-[92vh] flex flex-col overflow-hidden anim-fade-up">
                
                {{-- Fixed Standard White Header --}}
                <div class="flex items-center justify-between border-b border-[#E2E8F0] p-5 sm:p-6 pb-4 shrink-0 bg-white">
                    <div>
                        <p class="text-[#8F0A0D] text-[11px] font-bold uppercase tracking-wider">AKTIVITAS BARU</p>
                        <h3 class="text-[16px] font-bold text-[#1E293B]">Input Kronologis Agenda &amp; Catatan Aktivitas</h3>
                    </div>
                    <button type="button" @click="isBulkModalOpen = false" class="text-[#94A3B8] hover:text-[#1E293B] p-1.5 rounded-lg hover:bg-[#F1F5F9] transition cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Form with Scrollable Table Body & Fixed Footer --}}
                <form action="{{ route('sales.activities.bulk') }}" method="POST" class="flex flex-col flex-1 min-h-0 overflow-hidden">
                    @csrf
                    
                    {{-- Global Selection Bar (Single Clean Selector) --}}
                    <div class="p-4 sm:p-5 bg-[#F8FAFC] border-b border-[#E2E8F0] shrink-0">
                        <div>
                            <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">
                                Pilih Prospek / Proyek Tujuan <span class="text-[#8F0A0D]">*</span>
                            </label>
                            <select name="project_id" x-model="bulkProjectId" required
                                    class="w-full px-3.5 py-2.5 bg-white border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition cursor-pointer shadow-xs">
                                <option value="">-- Pilih Prospek / Proyek --</option>
                                @foreach($activeProjects as $p)
                                    <option value="{{ $p->id }}" {{ ($filterProject == $p->id || (count($activeProjects) == 1 && $activeProjects[0]->id == $p->id)) ? 'selected' : '' }}>
                                        {{ $p->name }} ({{ $p->client }}) - PIC: {{ $p->sales_name ?: 'Sales' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Table Spreadsheet Body --}}
                    <div class="flex-1 overflow-x-auto overflow-y-auto p-4 sm:p-5 bg-[#F8FAFC]/50">
                        <table class="w-full border-collapse bg-white rounded-xl shadow-xs border border-[#E2E8F0] text-left text-xs min-w-[1050px]">
                            <thead class="bg-[#F8FAFC] text-[#475569] font-bold uppercase text-[10.5px] tracking-wider border-b border-[#E2E8F0]">
                                <tr>
                                    <th class="py-3 px-3 w-12 text-center">No</th>
                                    <th class="py-3 px-3 w-80">Kronologis Agenda / Aktivitas <span class="text-[#8F0A0D]">*</span></th>
                                    <th class="py-3 px-3 w-36">Tanggal</th>
                                    <th class="py-3 px-3 w-32">Waktu (Jam)</th>
                                    <th class="py-3 px-3 w-36">PIC Klien</th>
                                    <th class="py-3 px-3 w-36">PIC IPNET</th>
                                    <th class="py-3 px-3 min-w-[200px]">Catatan Aksi / Durasi</th>
                                    <th class="py-3 px-2 w-12 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#E2E8F0]">
                                <template x-for="(row, index) in bulkRows" :key="index">
                                    <tr class="hover:bg-[#F8FAFC] transition-colors">
                                        {{-- No --}}
                                        <td class="py-2.5 px-3 text-center font-bold text-[#94A3B8] text-xs" x-text="index + 1"></td>
                                        
                                        {{-- Agenda / Subject --}}
                                        <td class="py-2 px-2.5">
                                            <input type="hidden" :name="'activities[' + index + '][activity_type]'" value="Troubleshooting">
                                            <textarea :name="'activities[' + index + '][subject]'" 
                                                      x-model="row.subject" 
                                                      rows="2" 
                                                      class="w-full p-2 bg-[#F8FAFC] focus:bg-white border border-[#CBD5E1] rounded-lg text-xs text-[#1E293B] focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D] transition resize-none"></textarea>
                                        </td>

                                        {{-- Tanggal --}}
                                        <td class="py-2 px-2.5">
                                            <input type="date" 
                                                   :name="'activities[' + index + '][activity_date]'" 
                                                   x-model="row.activity_date" 
                                                   class="w-full p-2 bg-[#F8FAFC] focus:bg-white border border-[#CBD5E1] rounded-lg text-xs text-[#1E293B] focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D] transition cursor-pointer">
                                        </td>

                                        {{-- Waktu (Time Picker with AM/PM & 24h native support) --}}
                                        <td class="py-2 px-2.5">
                                            <input type="time" 
                                                   :name="'activities[' + index + '][time_str]'" 
                                                   x-model="row.time_str" 
                                                   class="w-full p-2 bg-[#F8FAFC] focus:bg-white border border-[#CBD5E1] rounded-lg text-xs text-[#1E293B] focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D] transition cursor-pointer">
                                        </td>

                                        {{-- PIC Klien --}}
                                        <td class="py-2 px-2.5">
                                            <input type="text" 
                                                   :name="'activities[' + index + '][client_pic]'" 
                                                   x-model="row.client_pic" 
                                                   class="w-full p-2 bg-[#F8FAFC] focus:bg-white border border-[#CBD5E1] rounded-lg text-xs text-[#1E293B] focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D] transition">
                                        </td>

                                        {{-- PIC IPNET --}}
                                        <td class="py-2 px-2.5">
                                            <input type="text" 
                                                   :name="'activities[' + index + '][ipnet_pic]'" 
                                                   x-model="row.ipnet_pic" 
                                                   class="w-full p-2 bg-[#F8FAFC] focus:bg-white border border-[#CBD5E1] rounded-lg text-xs text-[#1E293B] focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D] transition">
                                        </td>

                                        {{-- Catatan Aksi --}}
                                        <td class="py-2 px-2.5">
                                            <textarea :name="'activities[' + index + '][notes]'" 
                                                      x-model="row.notes" 
                                                      rows="2" 
                                                      class="w-full p-2 bg-[#F8FAFC] focus:bg-white border border-[#CBD5E1] rounded-lg text-xs text-[#1E293B] focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D] transition resize-none"></textarea>
                                        </td>

                                        {{-- Hapus Baris --}}
                                        <td class="py-2 px-2 text-center">
                                            <button type="button" 
                                                    @click="removeBulkRow(index)" 
                                                    title="Hapus baris ini"
                                                    class="p-1.5 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-lg transition cursor-pointer">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>

                        {{-- Quick Add Rows Controls --}}
                        <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
                            <div class="flex items-center gap-2">
                                <button type="button" 
                                        @click="addBulkRow()" 
                                        class="px-4 py-2 bg-white hover:bg-red-50 text-[#8F0A0D] border border-red-200 hover:border-red-300 font-bold rounded-xl text-xs flex items-center gap-1.5 shadow-xs transition cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                    <span>+ Tambah 1 Baris</span>
                                </button>
                                <button type="button" 
                                        @click="addBulkMultiple(5)" 
                                        class="px-3.5 py-2 bg-white hover:bg-[#F1F5F9] text-[#475569] border border-[#CBD5E1] font-semibold rounded-xl text-xs transition cursor-pointer">
                                    <span>+ Tambah 5 Baris Sekaligus</span>
                                </button>
                            </div>
                            <div class="text-xs text-[#64748B] font-medium">
                                Total Baris Terisi: <strong class="text-[#1E293B] font-bold" x-text="filledRowsCount"></strong> dari <strong class="text-[#1E293B]" x-text="bulkRows.length"></strong> baris
                            </div>
                        </div>
                    </div>

                    {{-- Fixed Footer --}}
                    <div class="flex items-center justify-between p-4 px-6 border-t border-[#E2E8F0] bg-[#F8FAFC] shrink-0">
                        <button type="button" 
                                @click="isBulkModalOpen = false" 
                                class="px-4 py-2.5 text-xs font-bold text-[#475569] hover:bg-[#F1F5F9] border border-[#CBD5E1] rounded-xl transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" 
                                class="px-6 py-2.5 text-xs font-bold text-white bg-[#8F0A0D] hover:bg-[#73080A] rounded-xl shadow-md transition flex items-center gap-2 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            <span>Simpan Semua Kronologi Aktivitas</span>
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </template>

    {{-- MODAL TAMBAH / EDIT SALES CRM ACTIVITY (SATUAN) --}}
    <template x-teleport="body">
        <div x-show="isModalOpen" 
             x-cloak 
             class="fixed inset-0 z-50 bg-[#0F172A]/60 backdrop-blur-xs flex items-center justify-center p-4"
             @click.self="isModalOpen = false">
            <div class="bg-white rounded-2xl max-w-lg w-full shadow-2xl border border-[#E2E8F0] max-h-[90vh] flex flex-col overflow-hidden anim-fade-up">
                
                {{-- Fixed Header --}}
                <div class="flex items-center justify-between border-b border-[#E2E8F0] p-5 sm:p-6 pb-4 shrink-0 bg-white">
                    <div>
                        <p class="text-[#8F0A0D] text-[11px] font-bold uppercase tracking-wider" x-text="isEditing ? 'Edit Aktivitas' : 'Aktivitas Baru'"></p>
                        <h3 class="text-[16px] font-bold text-[#1E293B]" x-text="isEditing ? 'Edit Riwayat Interaksi CRM' : 'Catat Aktivitas Interaksi Prospek'"></h3>
                    </div>
                    <button type="button" @click="isModalOpen = false" class="text-[#94A3B8] hover:text-[#1E293B] p-1.5 rounded-lg hover:bg-[#F1F5F9] transition cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Form with Scrollable Body & Fixed Footer --}}
                <form :action="formUrl" method="POST" class="flex flex-col flex-1 min-h-0 overflow-hidden">
                    @csrf
                    <template x-if="isEditing">
                        <input type="hidden" name="_method" value="PUT">
                    </template>
                    
                    <div class="p-5 sm:p-6 overflow-y-auto space-y-3.5 text-[12.5px] flex-1">
                        
                        <div>
                            <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">
                                Pilih Prospek / Proyek <span class="text-[#8F0A0D]">*</span>
                            </label>
                            <select name="project_id" x-model="form.project_id" required
                                    class="w-full px-3.5 py-2.5 bg-white border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition cursor-pointer">
                                <option value="">-- Pilih Prospek / Proyek --</option>
                                @foreach($activeProjects as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->client }}) - PIC: {{ $p->sales_name ?: 'Sales' }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <div>
                                <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">
                                    Tipe Aktivitas <span class="text-[#8F0A0D]">*</span>
                                </label>
                                <select name="activity_type" x-model="form.activity_type" required
                                        class="w-full px-3.5 py-2.5 bg-white border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition cursor-pointer">
                                    @foreach($activityTypes as $typeKey => $typeLabel)
                                        <option value="{{ $typeKey }}">{{ $typeLabel }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">
                                    Tanggal &amp; Waktu <span class="text-[#8F0A0D]">*</span>
                                </label>
                                <input type="datetime-local" name="activity_date" x-model="form.activity_date" required
                                       class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition cursor-pointer">
                            </div>
                        </div>

                        <div>
                            <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">
                                Subjek Interaksi <span class="text-[#8F0A0D]">*</span>
                            </label>
                            <input type="text" name="subject" x-model="form.subject" placeholder="Contoh: Meeting Klarifikasi BoQ & Pengiriman" required
                                   class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition">
                        </div>

                        <div>
                            <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">
                                Hasil Pembahasan &amp; Catatan
                            </label>
                            <textarea name="notes" x-model="form.notes" rows="3" placeholder="Poin penting hasil meeting, feedback klien, penyesuaian harga..."
                                      class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition resize-none"></textarea>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <div>
                                <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">
                                    Next Action (Opsional)
                                </label>
                                <input type="text" name="next_action" x-model="form.next_action" placeholder="Contoh: Kirim revisi quotation"
                                       class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition">
                            </div>

                            <div>
                                <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">
                                    Target Tanggal Next Action
                                </label>
                                <input type="date" name="next_action_date" x-model="form.next_action_date"
                                       class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition cursor-pointer">
                            </div>
                        </div>

                    </div>

                    {{-- Fixed Footer --}}
                    <div class="flex items-center justify-end gap-2.5 p-4 px-6 border-t border-[#E2E8F0] bg-[#F8FAFC] shrink-0">
                        <button type="button" 
                                @click="isModalOpen = false" 
                                class="px-4 py-2.5 text-[12.5px] font-bold text-[#475569] hover:bg-[#F1F5F9] border border-[#CBD5E1] rounded-xl transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" 
                                class="px-5 py-2.5 text-[12.5px] font-bold text-white bg-[#8F0A0D] hover:bg-[#73080A] rounded-xl shadow-md transition cursor-pointer"
                                x-text="isEditing ? 'Simpan Perubahan' : 'Simpan Log Aktivitas'">
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </template>

    {{-- Hidden Delete Form --}}
    <form id="delete-activity-form" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

</div>
@endsection

@push('scripts')
<script>
    function salesActivitiesManager() {
        return {
            isModalOpen: false,
            isEditing: false,
            formUrl: '{{ route('sales.activities.store') }}',
            form: {
                id: null,
                project_id: '',
                activity_type: 'Meeting',
                activity_date: '{{ date('Y-m-d\TH:i') }}',
                subject: '',
                notes: '',
                next_action: '',
                next_action_date: ''
            },

            // Spreadsheet Multi-Row Bulk Entry
            isBulkModalOpen: false,
            bulkProjectId: '{{ $filterProject ?: ($activeProjects->first()->id ?? '') }}',
            bulkDefaultType: 'Troubleshooting',
            bulkRows: [
                { subject: '', activity_date: '{{ date('Y-m-d') }}', time_str: '{{ date('H:i') }}', client_pic: '', ipnet_pic: '', notes: '' },
                { subject: '', activity_date: '{{ date('Y-m-d') }}', time_str: '{{ date('H:i') }}', client_pic: '', ipnet_pic: '', notes: '' },
                { subject: '', activity_date: '{{ date('Y-m-d') }}', time_str: '{{ date('H:i') }}', client_pic: '', ipnet_pic: '', notes: '' },
                { subject: '', activity_date: '{{ date('Y-m-d') }}', time_str: '{{ date('H:i') }}', client_pic: '', ipnet_pic: '', notes: '' },
                { subject: '', activity_date: '{{ date('Y-m-d') }}', time_str: '{{ date('H:i') }}', client_pic: '', ipnet_pic: '', notes: '' }
            ],

            get filledRowsCount() {
                return this.bulkRows.filter(r => r.subject && r.subject.trim() !== '').length;
            },

            openBulkModal() {
                this.isBulkModalOpen = true;
            },

            addBulkRow() {
                const lastDate = this.bulkRows.length > 0 ? this.bulkRows[this.bulkRows.length - 1].activity_date : '{{ date('Y-m-d') }}';
                const lastTime = this.bulkRows.length > 0 ? this.bulkRows[this.bulkRows.length - 1].time_str : '{{ date('H:i') }}';
                this.bulkRows.push({
                    subject: '',
                    activity_date: lastDate,
                    time_str: lastTime || '{{ date('H:i') }}',
                    client_pic: '',
                    ipnet_pic: '',
                    notes: ''
                });
            },

            addBulkMultiple(count) {
                const lastDate = this.bulkRows.length > 0 ? this.bulkRows[this.bulkRows.length - 1].activity_date : '{{ date('Y-m-d') }}';
                const lastTime = this.bulkRows.length > 0 ? this.bulkRows[this.bulkRows.length - 1].time_str : '{{ date('H:i') }}';
                for (let i = 0; i < count; i++) {
                    this.bulkRows.push({
                        subject: '',
                        activity_date: lastDate,
                        time_str: lastTime || '{{ date('H:i') }}',
                        client_pic: '',
                        ipnet_pic: '',
                        notes: ''
                    });
                }
            },

            removeBulkRow(index) {
                if (this.bulkRows.length > 1) {
                    this.bulkRows.splice(index, 1);
                } else {
                    this.bulkRows[0] = { subject: '', activity_date: '{{ date('Y-m-d') }}', time_str: '{{ date('H:i') }}', client_pic: '', ipnet_pic: '', notes: '' };
                }
            },

            openAddModal() {
                this.isEditing = false;
                this.formUrl = '{{ route('sales.activities.store') }}';
                this.form = {
                    id: null,
                    project_id: '',
                    activity_type: 'Meeting',
                    activity_date: '{{ date('Y-m-d\TH:i') }}',
                    subject: '',
                    notes: '',
                    next_action: '',
                    next_action_date: ''
                };
                this.isModalOpen = true;
            },

            openEditModal(act) {
                this.isEditing = true;
                this.formUrl = '/sales/activities/' + act.id;
                
                var dateVal = '';
                if (act.activity_date) {
                    dateVal = act.activity_date.replace(' ', 'T').substring(0, 16);
                }

                var nextDateVal = '';
                if (act.next_action_date) {
                    nextDateVal = act.next_action_date.split('T')[0];
                }

                this.form = {
                    id: act.id,
                    project_id: act.project_id || '',
                    activity_type: act.activity_type || 'Meeting',
                    activity_date: dateVal || '{{ date('Y-m-d\TH:i') }}',
                    subject: act.subject || '',
                    notes: act.notes || '',
                    next_action: act.next_action || '',
                    next_action_date: nextDateVal
                };
                this.isModalOpen = true;
            },

            confirmDelete(id, subject) {
                if (confirm(`Yakin ingin menghapus aktivitas "${subject}"?`)) {
                    const form = document.getElementById('delete-activity-form');
                    form.action = `/sales/activities/${id}`;
                    form.submit();
                }
            }
        }
    }
</script>
@endpush
