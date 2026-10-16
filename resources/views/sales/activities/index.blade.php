@extends('layouts.app')

@section('title', 'Aktivitas Sales & CRM - Sales Portal')

@php
    $projectsLookup = $activeProjects->mapWithKeys(function($p) {
        return [
            $p->id => [
                'id'             => $p->id,
                'name'           => $p->name,
                'client'         => $p->client,
                'sales_name'     => $p->sales_name,
                'contract_value' => $p->contract_value,
                'sales_stage'    => $p->sales_stage,
                'status'         => $p->status,
                'lost_reason'    => $p->lost_reason,
                'lost_competitor'=> $p->lost_competitor,
                'lost_analysis'  => $p->lost_analysis,
            ]
        ];
    });
@endphp

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
                    <span>Input Dokumen Resmi Sales</span>
                </button>
                @endif
            </div>

            {{-- Activity Feed Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 anim-fade-up anim-delay-2">
                @forelse($activities as $act)
                    @php
                        $isBatch = false;
                        $batchData = null;
                        if (!empty($act->notes) && is_string($act->notes) && str_starts_with(trim($act->notes), '{')) {
                            $decoded = json_decode($act->notes, true);
                            if (is_array($decoded) && isset($decoded['type']) && $decoded['type'] === 'kronologi_batch') {
                                $isBatch = true;
                                $batchData = $decoded;
                            }
                        }
                    @endphp

                    <div class="ipnet-card p-5 flex flex-col justify-between space-y-3.5 {{ $isBatch ? 'border-red-200/80 bg-gradient-to-b from-white to-red-50/20' : '' }}">
                        <div class="space-y-2.5">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-1.5">
                                    <span class="px-2.5 py-1 rounded-lg text-[10.5px] font-bold {{ $isBatch ? 'bg-red-50 text-[#8F0A0D] border border-red-200' : 'bg-[#F8FAFC] border border-gray-200 text-gray-800' }}">
                                        {{ $act->activity_type }}
                                    </span>
                                    @if($isBatch)
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                            {{ count($batchData['items'] ?? []) }} Rangkaian Agenda
                                        </span>
                                    @endif
                                </div>
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

                            {{-- Card Body Preview --}}
                            @if($isBatch && !empty($batchData['items']))
                                <div class="bg-[#F8FAFC] p-3.5 rounded-xl border border-gray-200/80 space-y-2.5">
                                    <p class="text-[11px] font-bold text-gray-600 uppercase tracking-wider">
                                        Rangkuman Kronologis Agenda:
                                    </p>
                                    <div class="space-y-1.5 text-xs text-gray-700">
                                        @foreach(array_slice($batchData['items'], 0, 3) as $item)
                                            <div class="flex items-start gap-2 text-[11.5px] leading-tight">
                                                <span class="font-bold text-[#8F0A0D] shrink-0">{{ $item['time'] ?: '-' }}</span>
                                                <span class="text-gray-400">|</span>
                                                <span class="font-semibold text-gray-800 truncate flex-1">{{ $item['subject'] }}</span>
                                                @if(!empty($item['client_pic']) || !empty($item['ipnet_pic']))
                                                    <span class="text-[10.5px] text-gray-500 shrink-0">({{ $item['client_pic'] ?: '-' }})</span>
                                                @endif
                                            </div>
                                        @endforeach
                                        @if(count($batchData['items']) > 3)
                                            <p class="text-[11px] font-semibold text-gray-400 italic pt-1">
                                                + {{ count($batchData['items']) - 3 }} agenda kronologis lainnya...
                                            </p>
                                        @endif
                                    </div>

                                    <button type="button" 
                                            @click="openDetailModal({{ json_encode($act) }}, {{ json_encode($batchData) }})"
                                            class="w-full mt-2 py-2 px-3 bg-white hover:bg-red-50 text-[#8F0A0D] border border-red-200 hover:border-red-300 rounded-xl text-xs font-bold flex items-center justify-center gap-2 transition cursor-pointer shadow-xs">
                                        <svg class="w-4 h-4 text-[#8F0A0D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        <span>Lihat Detail Lengkap ({{ count($batchData['items']) }} Agenda)</span>
                                    </button>
                                </div>
                            @elseif($act->notes)
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

                            <div class="flex items-center gap-1.5">
                                {{-- Cetak Dokumen Resmi PDF --}}
                                <a href="{{ route('sales.activities.pdf', $act->id) }}" target="_blank"
                                   class="px-2.5 py-1 bg-white hover:bg-red-50 text-[#8F0A0D] text-[11px] font-bold rounded-lg border border-red-200 shadow-xs transition inline-flex items-center gap-1"
                                   title="Cetak Dokumen Resmi PDF">
                                    <svg class="w-3.5 h-3.5 text-[#8F0A0D]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <span>PDF Resmi</span>
                                </a>

                                @if(!$isExecutiveOrPimpinan)
                                {{-- Action buttons: Edit & Hapus --}}
                                @if(!$isBatch)
                                <button type="button" 
                                        @click="openEditModal({{ json_encode($act) }})" 
                                        class="px-2.5 py-1 bg-white hover:bg-gray-50 text-gray-700 text-[11px] font-semibold rounded-lg border border-gray-200 shadow-xs transition inline-flex items-center gap-1">
                                    <svg class="w-3 h-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    <span>Edit</span>
                                </button>
                                @endif
                                <button type="button" 
                                        @click="confirmDelete({{ $act->id }}, '{{ addslashes($act->subject) }}')" 
                                        class="px-2.5 py-1 bg-white hover:bg-red-50 text-red-600 hover:text-red-700 text-[11px] font-semibold rounded-lg border border-red-200 shadow-xs transition inline-flex items-center gap-1">
                                    <svg class="w-3 h-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    <span>Hapus</span>
                                </button>
                                @endif
                            </div>
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

    {{-- MODAL POP-UP DETAIL KRONOLOGIS --}}
    <template x-teleport="body">
        <div x-show="isDetailModalOpen" 
             x-cloak 
             class="fixed inset-0 z-50 bg-[#0F172A]/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-6"
             @click.self="isDetailModalOpen = false">
            <div class="bg-white rounded-2xl max-w-5xl w-full shadow-2xl border border-[#E2E8F0] max-h-[92vh] flex flex-col overflow-hidden anim-fade-up">
                
                {{-- Header --}}
                <div class="flex items-center justify-between border-b border-[#E2E8F0] p-5 sm:p-6 pb-4 shrink-0 bg-white">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-[#8F0A0D] text-[11px] font-bold uppercase tracking-wider">DETAIL KRONOLOGIS CRM</span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200"
                                  x-text="(selectedDetail?.batch?.items?.length || 0) + ' Rangkaian Agenda'"></span>
                        </div>
                        <h3 class="text-[17px] font-bold text-[#1E293B]" x-text="selectedDetail?.activity?.subject || 'Detail Aktivitas'"></h3>
                    </div>
                    <button type="button" @click="isDetailModalOpen = false" class="text-[#94A3B8] hover:text-[#1E293B] p-1.5 rounded-lg hover:bg-[#F1F5F9] transition cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Meta Info Bar --}}
                <div class="px-5 sm:px-6 py-3 bg-[#F8FAFC] border-b border-[#E2E8F0] flex flex-wrap items-center justify-between gap-3 text-xs shrink-0">
                    <div class="flex items-center gap-4 flex-wrap">
                        <div>
                            <span class="text-gray-400 font-medium">Prospek / Proyek:</span>
                            <strong class="text-gray-800 ml-1" x-text="selectedDetail?.activity?.project?.name || '-'"></strong>
                        </div>
                        <span class="text-gray-300">•</span>
                        <div>
                            <span class="text-gray-400 font-medium">Klien:</span>
                            <strong class="text-gray-800 ml-1" x-text="selectedDetail?.activity?.project?.client || '-'"></strong>
                        </div>
                        <span class="text-gray-300">•</span>
                        <div>
                            <span class="text-gray-400 font-medium">Tipe:</span>
                            <span class="px-2 py-0.5 rounded-md bg-white border border-gray-200 text-gray-800 font-bold ml-1" x-text="selectedDetail?.activity?.activity_type || 'Troubleshooting'"></span>
                        </div>
                    </div>
                    <div class="text-gray-500 text-[11px]">
                        Dicatat oleh: <strong class="text-gray-800" x-text="selectedDetail?.activity?.sales?.name || 'Sales'"></strong>
                    </div>
                </div>

                {{-- Table Content Body --}}
                <div class="flex-1 overflow-y-auto overflow-x-auto p-5 sm:p-6 bg-white">
                    <table class="w-full border-collapse rounded-xl border border-[#E2E8F0] text-left text-xs min-w-[750px]">
                        <thead class="bg-[#F8FAFC] text-[#475569] font-bold uppercase text-[10.5px] tracking-wider border-b border-[#E2E8F0]">
                            <tr>
                                <th class="py-3 px-3.5 w-12 text-center">No</th>
                                <th class="py-3 px-3.5 w-36">Waktu &amp; Tanggal</th>
                                <th class="py-3 px-3.5 min-w-[220px]">Kronologis Agenda / Aktivitas</th>
                                <th class="py-3 px-3.5 w-36">PIC Klien</th>
                                <th class="py-3 px-3.5 w-36">PIC IPNET</th>
                                <th class="py-3 px-3.5 min-w-[200px]">Catatan Aksi / Detail</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#E2E8F0]">
                            <template x-for="(item, idx) in (selectedDetail?.batch?.items || [])" :key="idx">
                                <tr class="hover:bg-[#F8FAFC]/80 transition-colors">
                                    <td class="py-3 px-3.5 text-center font-bold text-[#8F0A0D]" x-text="item.no || (idx + 1)"></td>
                                    <td class="py-3 px-3.5 whitespace-nowrap">
                                        <div class="font-bold text-[#1E293B]" x-text="item.time || '-'"></div>
                                        <div class="text-[11px] text-gray-500 font-medium" x-text="item.date || '-'"></div>
                                    </td>
                                    <td class="py-3 px-3.5">
                                        <p class="font-bold text-[#1E293B] leading-relaxed" x-text="item.subject || '-'"></p>
                                    </td>
                                    <td class="py-3 px-3.5">
                                        <span class="inline-block px-2.5 py-1 bg-[#F1F5F9] text-[#334155] rounded-md font-semibold text-[11px]" x-text="item.client_pic || '-'"></span>
                                    </td>
                                    <td class="py-3 px-3.5">
                                        <span class="inline-block px-2.5 py-1 bg-red-50 text-[#8F0A0D] border border-red-100 rounded-md font-semibold text-[11px]" x-text="item.ipnet_pic || '-'"></span>
                                    </td>
                                    <td class="py-3 px-3.5">
                                        <p class="text-gray-700 whitespace-pre-line leading-relaxed font-medium" x-text="item.notes || '-'"></p>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                {{-- Footer --}}
                <div class="flex items-center justify-between p-4 px-6 border-t border-[#E2E8F0] bg-[#F8FAFC] shrink-0">
                    <a :href="'/sales/activities/' + (selectedDetail?.activity?.id || '') + '/pdf'" 
                       target="_blank"
                       class="px-4 py-2.5 text-xs font-bold text-white bg-[#8F0A0D] hover:bg-[#73080A] rounded-xl transition cursor-pointer shadow-xs inline-flex items-center gap-1.5 no-underline">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Cetak Dokumen Resmi (PDF)</span>
                    </a>

                    <button type="button" 
                            @click="isDetailModalOpen = false" 
                            class="px-5 py-2.5 text-xs font-bold text-[#1E293B] bg-white hover:bg-gray-100 border border-[#CBD5E1] rounded-xl transition cursor-pointer shadow-xs">
                        Tutup
                    </button>
                </div>

            </div>
        </div>
    </template>

    {{-- MODAL INPUT FORM LAPORAN RESMI AKTIVITAS SALES (LAYOUT RESMI IDENTIK ENGINEER) --}}
    <template x-teleport="body">
        <div x-show="isBulkModalOpen" 
             x-cloak 
             class="fixed inset-0 z-50 bg-[#0F172A]/70 backdrop-blur-xs flex items-center justify-center p-3 sm:p-6"
             @click.self="isBulkModalOpen = false"
             @keydown.escape.window="isBulkModalOpen = false">
            <div class="bg-white rounded-2xl max-w-7xl w-full shadow-2xl border border-[#CBD5E1] max-h-[94vh] flex flex-col overflow-hidden">
                
                {{-- Header Modal (Identik Persis Engineer) --}}
                <div class="px-6 py-4 border-b border-[#E2E8F0] flex items-center justify-between bg-[#F8FAFC] shrink-0">
                    <div class="flex items-center gap-2.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#8F0A0D]"></span>
                        <div>
                            <h3 class="text-[16px] font-bold text-[#0F172A]">Input Form Laporan Aktivitas Sales</h3>
                            <p class="text-[11px] text-[#64748B]">Isi formulir laporan kerja resmi di bawah ini sesuai acuan template PT IP Network Solusindo.</p>
                        </div>
                    </div>
                    <button type="button" @click="isBulkModalOpen = false" class="text-[#64748B] hover:text-[#1E293B] p-1.5 rounded-lg hover:bg-[#E2E8F0] transition cursor-pointer" title="Tutup">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form action="{{ route('sales.activities.bulk') }}" method="POST" @submit="isSubmitting = true" class="flex flex-col flex-1 min-h-0 overflow-hidden">
                    @csrf

                    {{-- Kategori Bar (Identik Persis Engineer: 2 Kategori Sales & CRM) --}}
                    <div class="px-4 sm:px-6 py-3 bg-white border-b border-[#CBD5E1] flex flex-wrap items-center justify-between gap-3 shrink-0">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-[#475569] uppercase tracking-wider">Kategori Laporan:</span>
                            <div class="inline-flex rounded-xl p-1 bg-[#F1F5F9] border border-[#CBD5E1] shadow-xs">
                                <button type="button" @click="selectedCategory = 'activity'"
                                        :class="selectedCategory === 'activity' ? 'bg-[#8F0A0D] text-white shadow-xs' : 'text-[#334155] hover:text-[#0F172A]'"
                                        class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition cursor-pointer flex items-center gap-1.5">
                                    <span>Aktivitas Sales</span>
                                </button>
                                <button type="button" @click="selectedCategory = 'lesson_learned'"
                                        :class="selectedCategory === 'lesson_learned' ? 'bg-[#8F0A0D] text-white shadow-xs' : 'text-[#334155] hover:text-[#0F172A]'"
                                        class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition cursor-pointer flex items-center gap-1.5">
                                    <span>Lesson Learned</span>
                                </button>
                            </div>
                        </div>
                        <div class="text-xs text-gray-500 font-medium hidden sm:block">
                            Format resmi PT IP Network Solusindo
                        </div>
                    </div>

                    {{-- Prospek & Topik Bar (Identik Persis Engineer) --}}
                    <div class="px-4 py-3 sm:px-6 sm:py-3.5 bg-[#F8FAFC] border-b border-[#CBD5E1] shrink-0">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3.5">
                            <div class="md:col-span-2">
                                <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">
                                    PILIH PROSPEK / PROYEK TUJUAN <span class="text-[#8F0A0D]">*</span>
                                </label>
                                <select name="project_id" x-model="bulkProjectId" @change="onProjectChange()" required
                                        class="w-full px-3.5 py-2.5 bg-white border border-[#CBD5E1] rounded-xl text-[12.5px] font-semibold text-[#1E293B] focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition cursor-pointer shadow-xs">
                                    <option value="">-- Pilih Prospek / Proyek Terkait (Data otomatis terisi ke formulir) --</option>
                                    @foreach($activeProjects as $p)
                                        <option value="{{ $p->id }}" {{ ($filterProject == $p->id || (count($activeProjects) == 1 && $activeProjects[0]->id == $p->id)) ? 'selected' : '' }}>
                                            {{ $p->name }} {{ $p->client ? '('.$p->client.')' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">
                                    JUDUL / TOPIK LAPORAN (OPSIONAL)
                                </label>
                                <input type="text" name="activity_title" x-model="activityTitle"
                                       placeholder="Contoh: Diskusi Kebutuhan Switch &amp; Firewall..."
                                       class="w-full px-3.5 py-2.5 bg-white border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition shadow-xs">
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="category" :value="selectedCategory">
                    <input type="hidden" name="report_category" :value="selectedCategory">

                    {{-- Scrollable Canvas Container (Identik Persis Engineer) --}}
                    <div class="flex-1 overflow-y-auto px-4 pt-3.5 pb-6 sm:px-6 sm:pt-4 sm:pb-6 bg-[#F1F5F9]/70">

                        {{-- Kategori 1: FORM LAPORAN AKTIVITAS SALES & CRM --}}
                        <div x-show="selectedCategory === 'activity'"
                             class="!mt-0 bg-white border-2 border-[#CBD5E1] rounded-xl p-5 sm:p-7 shadow-sm space-y-5 text-[#0F172A] font-sans">
                            
                            {{-- Kop Surat Resmi --}}
                            <div class="border-b-2 border-[#8F0A0D] pb-3 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <img src="/images/ipnet1.png" onerror="this.src='/images/ipnet.png'" alt="Logo IPNET" class="h-10 w-auto object-contain shrink-0">
                                    <div>
                                        <h2 class="text-[15px] sm:text-[17px] font-black text-[#0F172A] uppercase tracking-wide leading-tight">
                                            FORM LAPORAN AKTIVITAS SALES &amp; CRM
                                        </h2>
                                        <p class="text-[11px] text-[#475569] mt-0.5">
                                            Dokumen resmi ini digunakan sebagai laporan aktivitas sales, notulensi koordinasi dengan klien, dan bukti pelaksanaan agenda bisnis lapangan.
                                        </p>
                                    </div>
                                </div>
                                <div class="text-right shrink-0">
                                    <span class="px-3 py-1 bg-red-50 text-[#8F0A0D] border border-red-200 rounded-lg text-xs font-bold uppercase tracking-wider">
                                        Dokumen Resmi
                                    </span>
                                </div>
                            </div>

                            {{-- A. IDENTITAS PROSPEK & PERTEMUAN --}}
                            <div class="space-y-1.5">
                                <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">A. IDENTITAS PROSPEK &amp; PERTEMUAN</h3>
                                <div class="border border-[#1E293B] rounded-sm overflow-hidden text-xs">
                                    <table class="w-full border-collapse border border-[#1E293B]">
                                        <tbody>
                                            <tr class="border-b border-[#1E293B]">
                                                <td class="w-1/5 py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Prospek / Proyek Tujuan</td>
                                                <td class="w-3/10 py-1 px-2 border-r border-[#1E293B]">
                                                    <span class="font-bold text-[#0F172A]" x-text="selectedProjectData?.name || 'Pilih proyek di atas...'"></span>
                                                </td>
                                                <td class="w-1/5 py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">No. Dokumen / Laporan</td>
                                                <td class="w-3/10 py-1 px-2 font-mono font-bold text-[#8F0A0D]">
                                                    SLS-REP/{{ date('Y') }}/DRAFT
                                                </td>
                                            </tr>
                                            <tr class="border-b border-[#1E293B]">
                                                <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Judul / Topik Pertemuan</td>
                                                <td class="py-1 px-2 border-r border-[#1E293B]">
                                                    <span class="font-semibold text-slate-800" x-text="activityTitle || 'Sesuai judul laporan di atas'"></span>
                                                </td>
                                                <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Tipe Aktivitas / Agenda</td>
                                                <td class="py-1 px-2">
                                                    <select name="activity_type" class="w-full p-1 bg-transparent border-0 focus:ring-1 focus:ring-[#8F0A0D] font-semibold text-xs cursor-pointer">
                                                        @foreach($activityTypes as $typeKey => $typeLabel)
                                                            <option value="{{ $typeKey }}" {{ $typeKey === 'Meeting' ? 'selected' : '' }}>{{ $typeLabel }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Sales Person (AE)</td>
                                                <td class="py-1 px-2.5 border-r border-[#1E293B] font-semibold text-xs text-[#0F172A]">
                                                    <span x-text="selectedProjectData?.sales_name || '{{ auth()->user()->name }}'"></span>
                                                </td>
                                                <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Tanggal Dokumen</td>
                                                <td class="py-1 px-2.5 font-semibold text-xs text-slate-700">
                                                    {{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- B. RINCIAN KRONOLOGIS AGENDA & AKTIVITAS --}}
                            <div class="space-y-1.5">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">
                                        B. RINCIAN KRONOLOGIS AGENDA &amp; AKTIVITAS
                                    </h3>
                                    <div class="flex items-center gap-3">
                                        <button type="button" @click="addBulkRow()" class="text-[11px] font-bold text-[#8F0A0D] hover:underline flex items-center gap-1 cursor-pointer">
                                            <span>+ Tambah Baris</span>
                                        </button>
                                        <button type="button" @click="addBulkMultiple(5)" class="text-[11px] font-bold text-[#475569] hover:underline flex items-center gap-1 cursor-pointer">
                                            <span>+ Tambah 5 Baris</span>
                                        </button>
                                    </div>
                                </div>
                                <div class="border border-[#1E293B] rounded-sm overflow-x-auto text-xs">
                                    <table class="w-full border-collapse border border-[#1E293B] min-w-[1050px]">
                                        <thead class="bg-[#EBF3FB] font-bold text-[#0F172A] border-b border-[#1E293B]">
                                            <tr>
                                                <th class="py-1.5 px-2 w-10 text-center border-r border-[#1E293B]">No.</th>
                                                <th class="py-1.5 px-2 w-72 border-r border-[#1E293B] text-left">Kronologis Agenda / Aktivitas <span class="text-[#8F0A0D]">*</span></th>
                                                <th class="py-1.5 px-2 w-32 border-r border-[#1E293B] text-center">Tanggal</th>
                                                <th class="py-1.5 px-2 w-28 border-r border-[#1E293B] text-center">Waktu (Jam)</th>
                                                <th class="py-1.5 px-2 w-36 border-r border-[#1E293B] text-left">PIC Klien</th>
                                                <th class="py-1.5 px-2 w-36 border-r border-[#1E293B] text-left">PIC IPNET</th>
                                                <th class="py-1.5 px-2 min-w-[200px] border-r border-[#1E293B] text-left">Catatan Aksi / Detail</th>
                                                <th class="py-1.5 px-1 w-12 text-center">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <template x-for="(row, index) in bulkRows" :key="index">
                                                <tr class="border-b border-[#1E293B] hover:bg-[#F8FAFC]">
                                                    <td class="text-center font-bold py-1 px-1 border-r border-[#1E293B] text-[#8F0A0D]" x-text="index + 1"></td>
                                                    <td class="p-1 border-r border-[#1E293B]">
                                                        <textarea :name="'activities[' + index + '][subject]'" 
                                                                  x-model="row.subject" 
                                                                  rows="2" 
                                                                  placeholder="Contoh: Pembahasan spesifikasi switch dan SLA..."
                                                                  class="w-full p-1 text-xs border-0 bg-transparent font-semibold focus:ring-1 focus:ring-[#8F0A0D] resize-none"></textarea>
                                                    </td>
                                                    <td class="p-1 border-r border-[#1E293B] text-center">
                                                        <input type="date" 
                                                               :name="'activities[' + index + '][activity_date]'" 
                                                               x-model="row.activity_date" 
                                                               class="w-full p-1 text-center text-xs border-0 bg-transparent font-medium cursor-pointer focus:ring-1 focus:ring-[#8F0A0D]">
                                                    </td>
                                                    <td class="p-1 border-r border-[#1E293B] text-center">
                                                        <input type="time" 
                                                               :name="'activities[' + index + '][time_str]'" 
                                                               x-model="row.time_str" 
                                                               class="w-full p-1 text-center text-xs border-0 bg-transparent font-medium cursor-pointer focus:ring-1 focus:ring-[#8F0A0D]">
                                                    </td>
                                                    <td class="p-1 border-r border-[#1E293B]">
                                                        <input type="text" 
                                                               :name="'activities[' + index + '][client_pic]'" 
                                                               x-model="row.client_pic" 
                                                               placeholder="Nama PIC Klien"
                                                               class="w-full p-1 text-xs border-0 bg-transparent focus:ring-1 focus:ring-[#8F0A0D]">
                                                    </td>
                                                    <td class="p-1 border-r border-[#1E293B]">
                                                        <input type="text" 
                                                               :name="'activities[' + index + '][ipnet_pic]'" 
                                                               x-model="row.ipnet_pic" 
                                                               placeholder="Nama PIC IPNET"
                                                               class="w-full p-1 text-xs border-0 bg-transparent focus:ring-1 focus:ring-[#8F0A0D]">
                                                    </td>
                                                    <td class="p-1 border-r border-[#1E293B]">
                                                        <textarea :name="'activities[' + index + '][notes]'" 
                                                                  x-model="row.notes" 
                                                                  rows="2" 
                                                                  placeholder="Rangkuman hasil diskusi, durasi, dll..."
                                                                  class="w-full p-1 text-xs border-0 bg-transparent resize-none focus:ring-1 focus:ring-[#8F0A0D]"></textarea>
                                                    </td>
                                                    <td class="p-1 text-center">
                                                        <button type="button" 
                                                                @click="removeBulkRow(index)" 
                                                                title="Hapus baris ini"
                                                                class="p-1 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded transition cursor-pointer">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                        </button>
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="text-[11.5px] font-semibold text-[#334155] pt-0.5 flex items-center justify-between">
                                    <span>Total agenda tercatat: <strong class="text-[#8F0A0D]" x-text="filledRowsCount"></strong> dari <strong x-text="bulkRows.length"></strong> baris</span>
                                </div>
                            </div>

                            {{-- C. RENCANA TINDAK LANJUT BERIKUTNYA --}}
                            <div class="space-y-1.5">
                                <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">
                                    C. RENCANA TINDAK LANJUT BERIKUTNYA (NEXT ACTION PLAN)
                                </h3>
                                <div class="border border-[#1E293B] rounded-sm overflow-hidden text-xs">
                                    <table class="w-full border-collapse border border-[#1E293B]">
                                        <tbody>
                                            <tr>
                                                <td class="w-1/5 py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">
                                                    Komitmen / Aksi Berikutnya
                                                </td>
                                                <td class="w-3/5 p-1 border-r border-[#1E293B]">
                                                    <input type="text" name="next_action" placeholder="Contoh: Mengirimkan surat penawaran harga (SPH) revisi dan spesifikasi teknis" 
                                                           class="w-full p-1 bg-transparent border-0 focus:ring-1 focus:ring-[#8F0A0D] text-xs font-semibold text-[#0F172A]">
                                                </td>
                                                <td class="w-1/10 py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B] text-center">
                                                    Target Selesai
                                                </td>
                                                <td class="w-1/10 p-1">
                                                    <input type="date" name="next_action_date" 
                                                           class="w-full p-1 bg-transparent border-0 focus:ring-1 focus:ring-[#8F0A0D] text-xs font-medium cursor-pointer">
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>

                        {{-- Kategori 2: FORM LAPORAN EVALUASI & LESSON LEARNED (LOST DEAL) --}}
                        <div x-show="selectedCategory === 'lesson_learned'"
                             class="!mt-0 bg-white border-2 border-[#CBD5E1] rounded-xl p-5 sm:p-7 shadow-sm space-y-5 text-[#0F172A] font-sans">
                            
                            {{-- Kop Surat Resmi --}}
                            <div class="border-b-2 border-[#8F0A0D] pb-3 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <img src="/images/ipnet1.png" onerror="this.src='/images/ipnet.png'" alt="Logo IPNET" class="h-10 w-auto object-contain shrink-0">
                                    <div>
                                        <h2 class="text-[15px] sm:text-[17px] font-black text-[#0F172A] uppercase tracking-wide leading-tight">
                                            FORM LAPORAN EVALUASI &amp; LESSON LEARNED (LOST DEAL)
                                        </h2>
                                        <p class="text-[11px] text-[#475569] mt-0.5">
                                            Dokumen analisis resmi komersial untuk meninjau penyebab kegagalan peluang (Closed Lost) dan rekomendasi perbaikan strategi tim sales.
                                        </p>
                                    </div>
                                </div>
                                <div class="text-right shrink-0">
                                    <span class="px-3 py-1 bg-red-50 text-[#8F0A0D] border border-red-200 rounded-lg text-xs font-bold uppercase tracking-wider">
                                        Dokumen Resmi
                                    </span>
                                </div>
                            </div>

                            {{-- A. IDENTITAS PROSPEK & PELUANG --}}
                            <div class="space-y-1.5">
                                <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">A. IDENTITAS PROSPEK &amp; PELUANG</h3>
                                <div class="border border-[#1E293B] rounded-sm overflow-hidden text-xs">
                                    <table class="w-full border-collapse border border-[#1E293B]">
                                        <tbody>
                                            <tr class="border-b border-[#1E293B]">
                                                <td class="w-1/5 py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Nama Prospek / Proyek</td>
                                                <td class="w-3/10 py-1 px-2 border-r border-[#1E293B]">
                                                    <span class="font-bold text-[#0F172A]" x-text="selectedProjectData?.name || 'Pilih proyek di atas...'"></span>
                                                </td>
                                                <td class="w-1/5 py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">No. Laporan</td>
                                                <td class="w-3/10 py-1 px-2 font-mono font-bold text-[#8F0A0D]">
                                                    LST-REP/{{ date('Y') }}/DRAFT
                                                </td>
                                            </tr>
                                            <tr class="border-b border-[#1E293B]">
                                                <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Klien / Instansi</td>
                                                <td class="py-1 px-2 border-r border-[#1E293B]">
                                                    <span class="font-semibold text-slate-700" x-text="selectedProjectData?.client || '-'"></span>
                                                </td>
                                                <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Tanggal Evaluasi</td>
                                                <td class="py-1 px-2 font-medium text-slate-700">
                                                    {{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Account Executive / Sales</td>
                                                <td class="py-1 px-2 border-r border-[#1E293B]">
                                                    <span class="font-semibold text-slate-700" x-text="selectedProjectData?.sales_name || '{{ auth()->user()->name }}'"></span>
                                                </td>
                                                <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Nilai Peluang / Estimasi (IDR)</td>
                                                <td class="py-1 px-2 font-bold text-[#8F0A0D]">
                                                    <span x-text="selectedProjectData?.contract_value ? 'Rp ' + Number(selectedProjectData.contract_value).toLocaleString('id-ID') : 'Rp 0'"></span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- B. ANALISIS FAKTOR PENYEBAB & KOMPETITOR --}}
                            <div class="space-y-1.5">
                                <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">B. ANALISIS FAKTOR PENYEBAB &amp; KOMPETITOR</h3>
                                <div class="border border-[#1E293B] rounded-sm overflow-hidden text-xs">
                                    <table class="w-full border-collapse border border-[#1E293B]">
                                        <tbody>
                                            <tr class="border-b border-[#1E293B]">
                                                <td class="w-1/5 py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Faktor Utama Kekalahan <span class="text-[#8F0A0D]">*</span></td>
                                                <td class="w-3/10 py-1 px-2 border-r border-[#1E293B]">
                                                    <select name="primary_factor" x-model="lostFormData.primary_factor" :required="selectedCategory === 'lesson_learned'" class="w-full p-1 bg-transparent border-0 focus:ring-1 focus:ring-[#8F0A0D] font-bold text-xs cursor-pointer">
                                                        <option value="">-- Pilih Faktor Utama --</option>
                                                        <option value="Harga / Budget Kurang Bersaing">Harga / Budget Kurang Bersaing</option>
                                                        <option value="Kalah Spesifikasi Teknis / Brand">Kalah Spesifikasi Teknis / Brand</option>
                                                        <option value="Hubungan / Kedekatan Kompetitor">Hubungan / Kedekatan Kompetitor</option>
                                                        <option value="Waktu Delivery / SLA Tidak Cocok">Waktu Delivery / SLA Tidak Cocok</option>
                                                        <option value="Persyaratan Legal / Kualifikasi">Persyaratan Legal / Kualifikasi</option>
                                                        <option value="Proyek Dibatalkan Internal Klien">Proyek Dibatalkan Internal Klien</option>
                                                        <option value="Lainnya">Lainnya</option>
                                                    </select>
                                                </td>
                                                <td class="w-1/5 py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Kompetitor Pemenang</td>
                                                <td class="w-3/10 py-1 px-2">
                                                    <input type="text" name="competitor_winner" x-model="lostFormData.competitor_winner" placeholder="Contoh: PT Mitra Solusi..." class="w-full p-1 bg-transparent border-0 focus:ring-1 focus:ring-[#8F0A0D] font-semibold text-xs">
                                                </td>
                                            </tr>
                                            <tr class="border-b border-[#1E293B]">
                                                <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Harga Penawaran IPNET (IDR)</td>
                                                <td class="py-1 px-2 border-r border-[#1E293B]">
                                                    <input type="number" name="our_price" x-model="lostFormData.our_price" placeholder="Contoh: 150000000" class="w-full p-1 bg-transparent border-0 focus:ring-1 focus:ring-[#8F0A0D] text-xs font-semibold">
                                                </td>
                                                <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Estimasi Harga Pemenang (IDR)</td>
                                                <td class="py-1 px-2">
                                                    <input type="number" name="competitor_price" x-model="lostFormData.competitor_price" placeholder="Contoh: 135000000" class="w-full p-1 bg-transparent border-0 focus:ring-1 focus:ring-[#8F0A0D] text-xs font-semibold">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Titik Balik Keputusan (Turning Point)</td>
                                                <td colspan="3" class="p-1">
                                                    <input type="text" name="turning_point" x-model="lostFormData.turning_point" placeholder="Momen saat peluang mulai lepas, contoh: Saat klarifikasi spesifikasi atau negosiasi diskon..." class="w-full p-1 bg-transparent border-0 focus:ring-1 focus:ring-[#8F0A0D] text-xs">
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- C. ANALISIS KESENJANGAN (GAP ANALYSIS) --}}
                            <div class="space-y-1.5">
                                <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">C. ANALISIS KESENJANGAN (GAP ANALYSIS)</h3>
                                <div class="border border-[#1E293B] rounded-sm overflow-hidden text-xs">
                                    <table class="w-full border-collapse border border-[#1E293B]">
                                        <thead class="bg-[#EBF3FB] font-bold text-[#0F172A] border-b border-[#1E293B]">
                                            <tr>
                                                <th class="py-1.5 px-2.5 w-1/2 border-r border-[#1E293B] text-left">Kelemahan Penawaran IPNET</th>
                                                <th class="py-1.5 px-2.5 w-1/2 text-left">Keunggulan Utama Kompetitor</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="p-1 border-r border-[#1E293B] align-top">
                                                    <textarea name="gap_our_weakness" x-model="lostFormData.gap_our_weakness" rows="3" placeholder="Faktor kekurangan kita, misal: Lead time delivery terlalu lama, diskon prinsipal kurang..." class="w-full p-1 text-xs border-0 bg-transparent resize-none focus:ring-1 focus:ring-[#8F0A0D]"></textarea>
                                                </td>
                                                <td class="p-1 align-top">
                                                    <textarea name="gap_competitor_strength" x-model="lostFormData.gap_competitor_strength" rows="3" placeholder="Keunggulan lawan, misal: Brand sudah terpasang lama, bonus implementasi free..." class="w-full p-1 text-xs border-0 bg-transparent resize-none focus:ring-1 focus:ring-[#8F0A0D]"></textarea>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- D. LESSON LEARNED & STRATEGI REKOMENDASI --}}
                            <div class="space-y-1.5">
                                <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">D. LESSON LEARNED &amp; STRATEGI REKOMENDASI</h3>
                                <div class="border border-[#1E293B] rounded-sm overflow-hidden text-xs">
                                    <table class="w-full border-collapse border border-[#1E293B]">
                                        <tbody>
                                            <tr class="border-b border-[#1E293B]">
                                                <td class="w-1/4 py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B] align-top">Pembelajaran (Lesson Learned)</td>
                                                <td class="w-3/4 p-1">
                                                    <textarea name="lessons_learned" x-model="lostFormData.lessons_learned" rows="3" placeholder="Apa yang dipelajari tim sales agar kesalahan tidak terulang di prospek serupa..." class="w-full p-1 text-xs border-0 bg-transparent resize-none focus:ring-1 focus:ring-[#8F0A0D]"></textarea>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B] align-top">Rekomendasi Strategi Berikutnya</td>
                                                <td class="p-1">
                                                    <textarea name="future_strategy" x-model="lostFormData.future_strategy" rows="3" placeholder="Langkah mitigasi ke depan: menjaga relasi dengan klien untuk proyek berikutnya, dsb..." class="w-full p-1 text-xs border-0 bg-transparent resize-none focus:ring-1 focus:ring-[#8F0A0D]"></textarea>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>

                    </div>

                    {{-- Footer Modal (Identik Persis Engineer) --}}
                    <div class="px-6 py-4 bg-white border-t border-[#CBD5E1] flex items-center justify-between shrink-0 shadow-sm">
                        <button type="button" @click="isBulkModalOpen = false"
                                class="px-5 py-2.5 bg-white text-[#334155] border border-[#CBD5E1] hover:bg-gray-50 font-bold text-xs rounded-xl transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit"
                                :disabled="isSubmitting"
                                :class="isSubmitting ? 'opacity-75 cursor-not-allowed bg-[#73080A]' : 'bg-[#8F0A0D] hover:bg-[#73080A] cursor-pointer'"
                                class="px-6 py-2.5 text-white font-bold text-xs rounded-xl shadow-md transition flex items-center gap-2">
                            <template x-if="!isSubmitting">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                            </template>
                            <template x-if="isSubmitting">
                                <svg class="w-4 h-4 text-white animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                </svg>
                            </template>
                            <span x-text="isSubmitting ? 'Menyimpan...' : (selectedCategory === 'lesson_learned' ? 'Simpan Laporan Lesson Learned' : 'Simpan Laporan Aktivitas')"></span>
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

    {{-- MODAL KONFIRMASI HAPUS AKTIVITAS (STANDARD MODAL TEMPLATE) --}}
    <template x-teleport="body">
        <div x-show="isDeleteModalOpen" x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-[99999] bg-[#0F172A]/60 backdrop-blur-xs flex items-center justify-center p-4"
             @click.self="isDeleteModalOpen = false"
             @keydown.escape.window="isDeleteModalOpen = false">
            <div class="bg-white rounded-2xl w-[420px] max-w-full p-6 text-left shadow-[0_20px_60px_rgba(15,23,42,0.25)] border border-[#E2E8F0] anim-fade-up">
                <div class="w-12 h-12 rounded-full bg-[#FEF2F2] flex items-center justify-center mx-auto mb-4 text-[#8F0A0D]">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>
                
                <h3 class="text-center font-display text-[16px] font-bold text-[#1E293B] mb-1.5">Yakin Hapus Aktivitas?</h3>
                <p class="text-center text-[12.5px] text-[#64748B] mb-6 break-words" x-text="'Aktivitas &quot;' + deleteActivitySubject + '&quot; akan dihapus secara permanen.'"></p>

                <div class="flex gap-2.5">
                    <button type="button" @click="isDeleteModalOpen = false"
                            class="flex-1 py-2.5 px-4 rounded-xl bg-white text-[#334155] border border-[#CBD5E1] font-bold text-[12.5px] hover:bg-[#F8FAFC] transition cursor-pointer text-center">
                        Batal
                    </button>
                    <button type="button" @click="submitDelete()"
                            class="flex-1 py-2.5 px-4 rounded-xl btn-ipnet-primary font-bold text-[12.5px] transition cursor-pointer shadow-md text-white text-center">
                        Hapus
                    </button>
                </div>
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

            // Detail Modal Pop-up State
            isDetailModalOpen: false,
            selectedDetail: null,

            openDetailModal(act, batchData) {
                this.selectedDetail = {
                    activity: act,
                    batch: batchData
                };
                this.isDetailModalOpen = true;
            },

            // Delete Modal State
            isDeleteModalOpen: false,
            deleteActivityId: null,
            deleteActivitySubject: '',

            confirmDelete(id, subject) {
                this.deleteActivityId = id;
                this.deleteActivitySubject = subject;
                this.isDeleteModalOpen = true;
            },

            submitDelete() {
                if (!this.deleteActivityId) return;
                const form = document.getElementById('delete-activity-form');
                form.action = '/sales/activities/' + this.deleteActivityId;
                form.submit();
            },

            // Spreadsheet Multi-Row Bulk Entry
            isBulkModalOpen: false,
            selectedCategory: 'activity',
            activityTitle: '',
            isSubmitting: false,
            bulkProjectId: '{{ $filterProject ?: ($activeProjects->first()->id ?? '') }}',
            projectsMap: {{ Js::from($projectsLookup) }},
            lostFormData: {
                primary_factor: '',
                competitor_winner: '',
                competitor_price: '',
                our_price: '',
                turning_point: '',
                gap_our_weakness: '',
                gap_competitor_strength: '',
                lessons_learned: '',
                future_strategy: ''
            },
            get selectedProjectData() {
                return this.projectsMap[this.bulkProjectId] || null;
            },
            onProjectChange() {
                const p = this.selectedProjectData;
                if (p) {
                    if (p.lost_analysis) {
                        try {
                            const la = typeof p.lost_analysis === 'string' ? JSON.parse(p.lost_analysis) : p.lost_analysis;
                            this.lostFormData.primary_factor = la.primary_factor || '';
                            this.lostFormData.competitor_winner = la.competitor_winner || p.lost_competitor || '';
                            this.lostFormData.competitor_price = la.competitor_price || '';
                            this.lostFormData.our_price = la.our_price || p.contract_value || '';
                            this.lostFormData.turning_point = la.turning_point || '';
                            this.lostFormData.gap_our_weakness = la.gap_our_weakness || '';
                            this.lostFormData.gap_competitor_strength = la.gap_competitor_strength || '';
                            this.lostFormData.lessons_learned = la.lessons_learned || p.lost_reason || '';
                            this.lostFormData.future_strategy = la.future_strategy || '';
                        } catch (e) {}
                    } else {
                        this.lostFormData.our_price = p.contract_value || '';
                        this.lostFormData.competitor_winner = p.lost_competitor || '';
                        this.lostFormData.lessons_learned = p.lost_reason || '';
                    }
                }
            },
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
                this.onProjectChange();
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
            }
        }
    }
</script>
@endpush
