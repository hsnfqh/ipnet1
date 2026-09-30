@extends('layouts.app')

@section('title', 'Activity Log Engineer - IP Network Solusindo')

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
<div class="flex h-screen overflow-hidden bg-[#F8FAFC] font-sans" x-data="engineerActivityManager()">
    @include('components.sidebar')

    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => $isLead ? 'Activity Log & Monitoring Engineer' : 'Catatan Aktivitas Harian Engineer'])

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
            @if(session('error'))
                <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700 font-bold px-2">✕</button>
                </div>
            @endif

            {{-- Filter & Action Bar --}}
            <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center gap-3 anim-fade-up anim-delay-1">
                <form method="GET" action="{{ route('engineer.activity_log.index') }}" class="flex flex-col sm:flex-row flex-wrap gap-2.5 w-full lg:w-auto">
                    {{-- Search --}}
                    <div class="relative">
                        <svg class="w-4 h-4 absolute left-3.5 top-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               placeholder="Cari kegiatan, lokasi, nama..."
                               class="w-full sm:w-64 px-3.5 py-2 pl-9 rounded-xl border border-gray-200 text-xs font-semibold text-gray-800 bg-white focus:outline-none focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D] transition-all shadow-xs">
                    </div>

                    {{-- Filter Tipe --}}
                    <select name="activity_type" onchange="this.form.submit()"
                            class="w-full sm:w-48 px-3.5 py-2 rounded-xl border border-gray-200 text-xs font-semibold text-gray-800 bg-white focus:outline-none focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D] transition-all cursor-pointer shadow-xs">
                        <option value="">Semua Tipe Aktivitas</option>
                        @foreach($activityTypes as $type)
                            <option value="{{ $type }}" {{ request('activity_type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                        @endforeach
                    </select>

                    {{-- Filter Engineer (Lead only) --}}
                    @if($isLead && $engineers->isNotEmpty())
                    <select name="user_id" onchange="this.form.submit()"
                            class="w-full sm:w-48 px-3.5 py-2 rounded-xl border border-gray-200 text-xs font-semibold text-gray-800 bg-white focus:outline-none focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D] transition-all cursor-pointer shadow-xs truncate">
                        <option value="">Semua Engineer</option>
                        @foreach($engineers as $eng)
                            <option value="{{ $eng->id }}" {{ request('user_id') == $eng->id ? 'selected' : '' }}>{{ $eng->name }}</option>
                        @endforeach
                    </select>
                    @endif

                    {{-- Filter Proyek --}}
                    <select name="project_id" onchange="this.form.submit()"
                            class="w-full sm:w-56 px-3.5 py-2 rounded-xl border border-gray-200 text-xs font-semibold text-gray-800 bg-white focus:outline-none focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D] transition-all cursor-pointer shadow-xs truncate">
                        <option value="">Semua Prospek / Proyek</option>
                        @foreach($projects as $p)
                            <option value="{{ $p->id }}" {{ request('project_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                        @endforeach
                    </select>

                    @if(request('search') || request('user_id') || request('activity_type') || request('status') || request('project_id') || request('date'))
                        <a href="{{ route('engineer.activity_log.index') }}" class="px-3 py-2 text-xs font-bold text-gray-500 hover:text-gray-800 self-center">
                            Reset Filter
                        </a>
                    @endif
                </form>

                {{-- Tombol Input Aktivitas --}}
                <button type="button" @click="$dispatch('open-engineer-activity-modal')"
                        class="btn-ipnet-primary w-full sm:w-auto justify-center shadow-md px-4 py-2.5 rounded-xl font-bold text-xs flex items-center gap-2 transition-all cursor-pointer shrink-0">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Input Aktivitas</span>
                </button>
            </div>

            {{-- Activity Feed Grid — Grouped by Project (exactly like Sales CRM) --}}
            @php
                $grouped = $activities->getCollection()->groupBy(function($act) {
                    return ($act->project_id ?? 'no_project') . '_' . ($act->user_id ?? '0');
                });
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 anim-fade-up anim-delay-2">
                @forelse($grouped as $groupKey => $groupActivities)
                    @php
                        $firstAct    = $groupActivities->first();
                        $project     = $firstAct->project;
                        $engineer    = $firstAct->engineer;
                        $projectName = $project->name ?? 'Tanpa Proyek';
                        $clientName  = $project->client ?? '-';
                        $actType     = $firstAct->activity_type ?? 'Aktivitas';
                        $totalInGroup = $groupActivities->count();

                        // Encode data untuk detail modal
                        $groupJson = json_encode([
                            'project_name' => $projectName,
                            'client_name'  => $clientName,
                            'activity_type'=> $actType,
                            'engineer_name'=> $engineer->name ?? '-',
                            'engineer_pos' => $engineer->position ?? '-',
                            'total'        => $totalInGroup,
                            'items'        => $groupActivities->map(function($a, $idx) {
                                return [
                                    'no'          => $idx + 1,
                                    'date'        => $a->activity_date ? $a->activity_date->format('d M Y') : '-',
                                    'time'        => $a->start_time ? \Carbon\Carbon::parse($a->start_time)->format('H:i') : '-',
                                    'subject'     => $a->description ?? '-',
                                    'client_pic'  => '', // extracted from notes if present
                                    'ipnet_pic'   => $a->engineer->name ?? '-',
                                    'notes'       => $a->notes ?? '-',
                                    'status'      => $a->status ?? '-',
                                    'id'          => $a->id,
                                    'user_id'     => $a->user_id,
                                ];
                            })->values()->toArray(),
                        ]);
                    @endphp

                    <div class="ipnet-card p-5 flex flex-col justify-between space-y-3.5 border-red-200/80 bg-gradient-to-b from-white to-red-50/20">
                        <div class="space-y-2.5">
                            {{-- Top: Tipe + Jumlah badge + Tanggal --}}
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-1.5">
                                    <span class="px-2.5 py-1 rounded-lg text-[10.5px] font-bold bg-red-50 text-[#8F0A0D] border border-red-200">
                                        {{ $actType }}
                                    </span>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                        {{ $totalInGroup }} Rangkaian Agenda
                                    </span>
                                </div>
                                <span class="text-xs font-bold text-gray-400">
                                    {{ $firstAct->activity_date ? $firstAct->activity_date->format('d M Y, H:i') : '-' }}
                                </span>
                            </div>

                            {{-- Nama Proyek --}}
                            <h3 class="text-sm font-bold text-gray-900 leading-snug">
                                {{ $projectName }}
                            </h3>

                            {{-- Engineer & Klien --}}
                            <div class="text-[11px] text-gray-500 flex items-center gap-1.5 flex-wrap">
                                <span class="font-bold text-gray-900">{{ $engineer->name ?? 'Engineer' }}</span>
                                <span>•</span>
                                <span>{{ $clientName }}</span>
                            </div>

                            {{-- Rangkuman Kronologis (preview 3 baris pertama) --}}
                            <div class="bg-[#F8FAFC] p-3.5 rounded-xl border border-gray-200/80 space-y-2.5">
                                <p class="text-[11px] font-bold text-gray-600 uppercase tracking-wider">
                                    Rangkuman Aktivitas:
                                </p>
                                <div class="space-y-1.5 text-xs text-gray-700">
                                    @foreach($groupActivities->take(3) as $item)
                                        <div class="flex items-start gap-2 text-[11.5px] leading-tight">
                                            <span class="font-bold text-[#8F0A0D] shrink-0">
                                                {{ $item->start_time ? \Carbon\Carbon::parse($item->start_time)->format('H:i') : '-' }}
                                            </span>
                                            <span class="text-gray-400">|</span>
                                            <span class="font-semibold text-gray-800 truncate flex-1">{{ $item->description }}</span>
                                            @if($item->notes)
                                                <span class="text-[10.5px] text-gray-500 shrink-0">({{ \Illuminate\Support\Str::limit($item->notes, 20) }})</span>
                                            @endif
                                        </div>
                                    @endforeach
                                    @if($totalInGroup > 3)
                                        <p class="text-[11px] font-semibold text-gray-400 italic pt-1">
                                            + {{ $totalInGroup - 3 }} agenda aktivitas lainnya...
                                        </p>
                                    @endif
                                </div>

                                {{-- Tombol Lihat Detail --}}
                                <button type="button"
                                        @click="openDetailModal({{ $groupJson }})"
                                        class="w-full mt-2 py-2 px-3 bg-white hover:bg-red-50 text-[#8F0A0D] border border-red-200 hover:border-red-300 rounded-xl text-xs font-bold flex items-center justify-center gap-2 transition cursor-pointer shadow-xs">
                                    <svg class="w-4 h-4 text-[#8F0A0D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    <span>Lihat Detail Lengkap ({{ $totalInGroup }} Agenda)</span>
                                </button>
                            </div>
                        </div>

                        {{-- Footer: dicatat oleh + hapus --}}
                        <div class="flex items-center justify-between pt-3 border-t border-gray-100 text-[11px]">
                            <div class="text-gray-400">
                                <span>Dicatat oleh: <strong class="text-gray-800">{{ $engineer->name ?? 'Engineer' }}</strong></span>
                            </div>
                            {{-- Hapus hanya untuk engineer sendiri atau lead --}}
                            @if(auth()->id() === $firstAct->user_id || $isLead)
                                <form method="POST" action="{{ route('engineer.activity_log.destroy', $firstAct) }}"
                                      onsubmit="return confirm('Hapus seluruh aktivitas ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2.5 py-1 bg-white hover:bg-red-50 text-red-600 hover:text-red-700 text-[11px] font-semibold rounded-lg border border-red-200 shadow-xs transition inline-flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        <span>Hapus</span>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-full ipnet-card p-12 text-center text-xs text-gray-400 space-y-2">
                        <p class="font-bold text-gray-900">Belum ada aktivitas yang tercatat.</p>
                        <p>Klik tombol "Input Aktivitas" di atas untuk mencatat aktivitas teknis.</p>
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

    {{-- ══ MODAL DETAIL AKTIVITAS (mirip Sales CRM) ══ --}}
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
                            <span class="text-[#8F0A0D] text-[11px] font-bold uppercase tracking-wider">DETAIL AKTIVITAS ENGINEER</span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200"
                                  x-text="(selectedDetail?.total || 0) + ' Rangkaian Agenda'"></span>
                        </div>
                        <h3 class="text-[17px] font-bold text-[#1E293B]" x-text="selectedDetail?.project_name || 'Detail Aktivitas'"></h3>
                    </div>
                    <button type="button" @click="isDetailModalOpen = false" class="text-[#94A3B8] hover:text-[#1E293B] p-1.5 rounded-lg hover:bg-[#F1F5F9] transition cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Meta Info Bar --}}
                <div class="px-5 sm:px-6 py-3 bg-[#F8FAFC] border-b border-[#E2E8F0] flex flex-wrap items-center justify-between gap-3 text-xs shrink-0">
                    <div class="flex items-center gap-4 flex-wrap">
                        <div>
                            <span class="text-gray-400 font-medium">Proyek:</span>
                            <strong class="text-gray-800 ml-1" x-text="selectedDetail?.project_name || '-'"></strong>
                        </div>
                        <span class="text-gray-300">•</span>
                        <div>
                            <span class="text-gray-400 font-medium">Klien:</span>
                            <strong class="text-gray-800 ml-1" x-text="selectedDetail?.client_name || '-'"></strong>
                        </div>
                        <span class="text-gray-300">•</span>
                        <div>
                            <span class="text-gray-400 font-medium">Tipe:</span>
                            <span class="px-2 py-0.5 rounded-md bg-white border border-gray-200 text-gray-800 font-bold ml-1" x-text="selectedDetail?.activity_type || '-'"></span>
                        </div>
                    </div>
                    <div class="text-gray-500 text-[11px]">
                        Dicatat oleh: <strong class="text-gray-800" x-text="selectedDetail?.engineer_name || '-'"></strong>
                    </div>
                </div>

                {{-- Table Content --}}
                <div class="flex-1 overflow-y-auto overflow-x-auto p-5 sm:p-6 bg-white">
                    <table class="w-full border-collapse rounded-xl border border-[#E2E8F0] text-left text-xs min-w-[750px]">
                        <thead class="bg-[#F8FAFC] text-[#475569] font-bold uppercase text-[10.5px] tracking-wider border-b border-[#E2E8F0]">
                            <tr>
                                <th class="py-3 px-3.5 w-12 text-center">No</th>
                                <th class="py-3 px-3.5 w-36">Waktu & Tanggal</th>
                                <th class="py-3 px-3.5 min-w-[220px]">Aktivitas</th>
                                <th class="py-3 px-3.5 w-36">PIC IPNET</th>
                                <th class="py-3 px-3.5 w-24">Status</th>
                                <th class="py-3 px-3.5 min-w-[180px]">Noted</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#E2E8F0]">
                            <template x-for="(item, idx) in (selectedDetail?.items || [])" :key="idx">
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
                                        <span class="inline-block px-2.5 py-1 bg-red-50 text-[#8F0A0D] border border-red-100 rounded-md font-semibold text-[11px]" x-text="item.ipnet_pic || '-'"></span>
                                    </td>
                                    <td class="py-3 px-3.5">
                                        <span class="inline-block px-2 py-0.5 rounded-full text-[9.5px] font-bold"
                                              :class="{
                                                'bg-emerald-50 text-emerald-700 border border-emerald-200': item.status === 'Selesai',
                                                'bg-sky-50 text-sky-700 border border-sky-200': item.status === 'Sedang Berjalan',
                                                'bg-amber-50 text-amber-700 border border-amber-200': item.status === 'Ditunda',
                                              }"
                                              x-text="item.status || '-'"></span>
                                    </td>
                                    <td class="py-3 px-3.5">
                                        <p class="text-gray-700 whitespace-pre-line leading-relaxed font-medium" x-text="item.notes || '-'"></p>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                {{-- Footer dengan tombol Download PDF & Excel --}}
                <div class="flex items-center justify-between p-4 px-6 border-t border-[#E2E8F0] bg-[#F8FAFC] shrink-0">
                    {{-- Download Buttons --}}
                    <div class="flex items-center gap-2">
                        {{-- Download PDF --}}
                        <a :href="buildExportUrl('pdf')"
                           target="_blank"
                           class="px-4 py-2 text-xs font-bold text-[#8F0A0D] bg-white hover:bg-red-50 border border-red-200 hover:border-red-300 rounded-xl transition cursor-pointer shadow-xs inline-flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                            Download PDF
                        </a>
                        {{-- Download Excel --}}
                        <a :href="buildExportUrl('excel')"
                           class="px-4 py-2 text-xs font-bold text-emerald-700 bg-white hover:bg-emerald-50 border border-emerald-200 hover:border-emerald-300 rounded-xl transition cursor-pointer shadow-xs inline-flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Download Excel
                        </a>
                    </div>

                    <button type="button"
                            @click="isDetailModalOpen = false"
                            class="px-5 py-2.5 text-xs font-bold text-[#1E293B] bg-white hover:bg-gray-100 border border-[#CBD5E1] rounded-xl transition cursor-pointer shadow-xs">
                        Tutup
                    </button>
                </div>

            </div>
        </div>
    </template>

    {{-- MODAL INPUT AKTIVITAS SPREADSHEET --}}
    @include('components.engineer-activity-bulk-modal')
</div>

@push('scripts')
<script>
function engineerActivityManager() {
    return {
        isDetailModalOpen: false,
        selectedDetail: null,

        openDetailModal(groupData) {
            this.selectedDetail = groupData;
            this.isDetailModalOpen = true;
        },

        buildExportUrl(type) {
            const base = type === 'pdf'
                ? '{{ route("engineer.activity_log.export_pdf") }}'
                : '{{ route("engineer.activity_log.export_excel") }}';
            // Pass current page filters
            const params = new URLSearchParams(window.location.search);
            if (Object.keys(params).length > 0) {
                return base + '?' + params.toString();
            }
            return base;
        },
    };
}
</script>
@endpush

@endsection
