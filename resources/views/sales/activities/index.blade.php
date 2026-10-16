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
                'lost_analysis'  => $p->parsed_lost_analysis,
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

    /* ── Activity Card (Identik Persis Engineer) ── */
    .activity-card {
        background: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04), 0 2px 6px rgba(15, 23, 42, 0.02);
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        overflow: hidden;
    }

    .activity-card:hover {
        border-color: #CBD5E1;
        box-shadow: 0 8px 20px rgba(143, 10, 13, 0.06), 0 3px 8px rgba(0, 0, 0, 0.04);
        transform: translateY(-2px);
    }

    .activity-card-header { padding: 14px 16px 10px; }
    .activity-card-body   { padding: 0 16px 12px; flex: 1; }
    .activity-card-footer {
        padding: 9px 16px;
        background: #FAFBFD;
        border-top: 1px solid #F1F5F9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
    }

    .agenda-badge {
        display: inline-flex;
        align-items: center;
        gap: 4.5px;
        padding: 2.5px 8.5px;
        border-radius: 999px;
        font-size: 10.5px;
        font-weight: 700;
        background: #FEF2F2;
        color: #8F0A0D;
        border: 1px solid #FECACA;
    }
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

            {{-- Activity Feed Grid (Identik Persis Layout Engineer) --}}
            @php
                $getInitials = function($name) {
                    $trimmed = trim((string) $name);
                    if (!$trimmed) return 'SL';
                    $words = preg_split('/\s+/', $trimmed);
                    if (count($words) >= 2) {
                        return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
                    }
                    return strtoupper(substr($trimmed, 0, 2));
                };
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 anim-fade-up anim-delay-2">
                @forelse($activities as $act)
                    @php
                        $isBatch = false;
                        $batchData = null;
                        if (!empty($act->notes) && is_string($act->notes) && str_starts_with(trim($act->notes), '{')) {
                            $decoded = json_decode($act->notes, true);
                            if (is_array($decoded)) {
                                $batchData = $decoded;
                                if (isset($decoded['type']) && $decoded['type'] === 'kronologi_batch') {
                                    $isBatch = true;
                                }
                            }
                        }

                        $isLessonLearned = ($act->activity_type === 'Lesson Learned') 
                            || (is_array($batchData) && isset($batchData['type']) && $batchData['type'] === 'lesson_learned')
                            || (is_array($batchData) && isset($batchData['primary_factor']));

                        $lostAnalysis = null;
                        if ($isLessonLearned) {
                            if (is_array($batchData) && isset($batchData['analysis_data'])) {
                                $lostAnalysis = $batchData['analysis_data'];
                            } elseif (is_array($batchData) && isset($batchData['primary_factor'])) {
                                $lostAnalysis = $batchData;
                            }
                        }

                        $salesName = $act->sales->name ?? 'Sales';
                        $salesInitial = $getInitials($salesName);
                        $projectName = $act->project->name ?? 'Prospek';
                        $clientName = $act->project->client ?? '-';
                        $totalInBatch = $isBatch && !empty($batchData['items']) ? count($batchData['items']) : 1;

                        // Siapkan cardItems untuk modal edit identik engineer
                        $cardItems = [];
                        if ($isBatch && !empty($batchData['items'])) {
                            foreach ($batchData['items'] as $bIdx => $bItem) {
                                $cardItems[] = [
                                    'no'         => $bItem['no'] ?? ($bIdx + 1),
                                    'id'         => $act->id,
                                    'subject'    => $bItem['subject'] ?? '',
                                    'date_raw'   => !empty($bItem['date']) ? date('Y-m-d', strtotime($bItem['date'])) : \Carbon\Carbon::parse($act->activity_date)->format('Y-m-d'),
                                    'time_str'   => $bItem['time'] ?? '09:00',
                                    'client_pic' => $bItem['client_pic'] ?? '',
                                    'ipnet_pic'  => $bItem['ipnet_pic'] ?? '',
                                    'notes'      => $bItem['notes'] ?? '',
                                ];
                            }
                        } else {
                            $cardItems[] = [
                                'no'         => 1,
                                'id'         => $act->id,
                                'subject'    => $act->subject,
                                'date_raw'   => \Carbon\Carbon::parse($act->activity_date)->format('Y-m-d'),
                                'time_str'   => \Carbon\Carbon::parse($act->activity_date)->format('H:i'),
                                'client_pic' => '',
                                'ipnet_pic'  => $act->sales->name ?? '',
                                'notes'      => $act->notes ?? '',
                            ];
                        }
                    @endphp

                    <div class="activity-card group">

                        {{-- Header Card --}}
                        <div class="activity-card-header">
                            <div class="flex items-center justify-between gap-2 mb-2">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    @if($isLessonLearned)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10.5px] font-extrabold bg-amber-50 text-amber-900 border border-amber-300">
                                            <svg class="w-3 h-3 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                            Lesson Learned
                                        </span>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-extrabold border bg-rose-50 text-rose-700 border-rose-200">
                                            Closed Lost
                                        </span>
                                    @else
                                        <span class="agenda-badge">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[#8F0A0D]"></span>
                                            {{ $totalInBatch }} Agenda
                                        </span>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-extrabold border bg-red-50 text-[#8F0A0D] border-red-200">
                                            {{ $act->activity_type }}
                                        </span>
                                    @endif
                                </div>
                                <span class="text-[11px] font-semibold text-gray-400 tabular-nums">
                                    {{ \Carbon\Carbon::parse($act->activity_date)->format('d M Y') }}
                                </span>
                            </div>

                            <h3 class="text-[13.5px] font-bold text-gray-900 group-hover:text-[#8F0A0D] transition-colors leading-snug line-clamp-1" title="{{ $act->subject }}">
                                {{ $act->subject }}
                            </h3>

                            <div class="flex items-center flex-wrap gap-1.5 mt-2">
                                {{-- Stacked Circular Avatars --}}
                                <div class="flex items-center -space-x-1.5 shrink-0" title="{{ $salesName }}">
                                    <div class="w-5 h-5 rounded-full bg-[#8F0A0D] text-white text-[9.5px] font-black flex items-center justify-center ring-1.5 ring-white uppercase shrink-0 shadow-xs">
                                        {{ $salesInitial }}
                                    </div>
                                </div>

                                {{-- Sales Name --}}
                                <span class="text-[12px] font-bold text-[#1E293B] truncate max-w-[200px]" title="{{ $salesName }}">
                                    {{ $salesName }}
                                </span>

                                {{-- Separator & Client Name --}}
                                @if($clientName && $clientName !== '-')
                                    <span class="text-gray-300 shrink-0">•</span>
                                    <span class="text-[11px] text-gray-500 font-medium truncate max-w-[120px]" title="{{ $clientName }}">{{ $clientName }}</span>
                                @endif
                            </div>
                        </div>

                        {{-- Body: Mini Rangkuman Agenda / Lesson Learned --}}
                        <div class="activity-card-body">
                            <div class="bg-[#F8FAFC] border border-[#EEF2F6] rounded-xl p-2.5 space-y-1.5">
                                @if($isLessonLearned)
                                    <div class="flex items-start gap-2 text-[11px]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-600 shrink-0 mt-1"></span>
                                        <div class="flex-1 truncate">
                                            <span class="text-slate-500 font-bold">Faktor:</span>
                                            <span class="text-slate-800 font-semibold ml-1">{{ $lostAnalysis['primary_factor'] ?? 'Kalah Harga / Lead Time' }}</span>
                                        </div>
                                    </div>
                                    @if(!empty($lostAnalysis['competitor_winner']) && $lostAnalysis['competitor_winner'] !== '-')
                                        <div class="flex items-center gap-2 text-[11px]">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-600 shrink-0"></span>
                                            <span class="text-slate-500 font-bold shrink-0">Pemenang:</span>
                                            <span class="text-amber-800 font-semibold truncate flex-1">{{ $lostAnalysis['competitor_winner'] }}</span>
                                        </div>
                                    @endif
                                    @if(!empty($lostAnalysis['lessons_learned']))
                                        <div class="text-[10.5px] text-slate-700 bg-amber-50/70 border border-amber-200/60 p-2 rounded-lg">
                                            <span class="font-bold text-amber-900 block mb-0.5">Pembelajaran Utama:</span>
                                            <p class="text-slate-700 font-medium line-clamp-2 leading-relaxed">{{ $lostAnalysis['lessons_learned'] }}</p>
                                        </div>
                                    @endif
                                @elseif($isBatch && !empty($batchData['items']))
                                    @foreach(array_slice($batchData['items'], 0, 3) as $item)
                                        <div class="flex items-center gap-2 text-[11px]">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[#8F0A0D]/70 shrink-0"></span>
                                            <span class="text-gray-400 font-medium text-[10px] shrink-0 tabular-nums">{{ $item['time'] ?: '-' }}</span>
                                            <span class="text-gray-700 font-medium truncate flex-1">{{ $item['subject'] }}</span>
                                        </div>
                                    @endforeach
                                    @if(count($batchData['items']) > 3)
                                        <div class="text-[10.5px] text-[#8F0A0D] font-bold pl-3.5 pt-0.5">
                                            + {{ count($batchData['items']) - 3 }} agenda lainnya...
                                        </div>
                                    @endif
                                @else
                                    <div class="flex items-center gap-2 text-[11px]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#8F0A0D]/70 shrink-0"></span>
                                        <span class="text-gray-400 font-medium text-[10px] shrink-0 tabular-nums">{{ \Carbon\Carbon::parse($act->activity_date)->format('H:i') }}</span>
                                        <span class="text-gray-700 font-medium truncate flex-1">{{ \Illuminate\Support\Str::limit($act->notes ?: $act->subject, 50) }}</span>
                                    </div>
                                    @if($act->next_action)
                                        <div class="flex items-center gap-2 text-[10.5px] text-amber-800">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 shrink-0"></span>
                                            <span class="text-amber-600 font-bold text-[10px] shrink-0">Next:</span>
                                            <span class="text-amber-900 font-medium truncate flex-1">{{ $act->next_action }}</span>
                                        </div>
                                    @endif
                                @endif
                            </div>
                                            {{-- Footer: Tepat 2 Tombol (Edit Form Laporan/Evaluasi & Hapus) --}}
                        <div class="activity-card-footer">
                            @if($isLessonLearned)
                                <button type="button" @click="openEditBulkModal({{ json_encode($act) }}, {{ json_encode($batchData) }})"
                                        class="flex-1 flex items-center justify-center gap-1.5 py-1.5 px-3 rounded-lg text-[11px] font-bold text-amber-900 bg-amber-50 hover:bg-amber-100 border border-amber-300 transition-all cursor-pointer">
                                    <svg class="w-3.5 h-3.5 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    <span>Edit Form Evaluasi</span>
                                </button>

                                @if(!$isExecutiveOrPimpinan)
                                    <button type="button" 
                                            @click="confirmDelete({{ $act->id }}, '{{ addslashes($act->subject) }}')" 
                                            class="inline-flex items-center gap-1 px-3 py-1.5 bg-white hover:bg-red-50 text-red-600 hover:text-red-700 text-[11px] font-bold rounded-lg border border-red-200 transition cursor-pointer"
                                            title="Hapus Aktivitas">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        <span>Hapus</span>
                                    </button>
                                @endif
                            @else
                                <button type="button" @click="openEditBulkModal({{ json_encode($act) }}, {{ json_encode($batchData) }})"
                                        class="flex-1 flex items-center justify-center gap-1.5 py-1.5 px-3 rounded-lg text-[11px] font-bold text-[#8F0A0D] bg-red-50/70 hover:bg-red-100/80 border border-red-200/80 hover:border-red-300 transition-all cursor-pointer">
                                    <svg class="w-3.5 h-3.5 text-[#8F0A0D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    <span>Edit Form Laporan</span>
                                    <span class="px-1.5 py-0.2 rounded-full bg-white text-[#8F0A0D] text-[9.5px] font-black border border-red-200/60">{{ $totalInBatch }}</span>
                                </button>

                                @if(!$isExecutiveOrPimpinan)
                                    <button type="button" 
                                            @click="confirmDelete({{ $act->id }}, '{{ addslashes($act->subject) }}')" 
                                            class="inline-flex items-center gap-1 px-3 py-1.5 bg-white hover:bg-red-50 text-red-600 hover:text-red-700 text-[11px] font-bold rounded-lg border border-red-200 transition cursor-pointer"
                                            title="Hapus Aktivitas">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        <span>Hapus</span>
                                    </button>
                                @endif
                            @endif
                        </div>      </div>

                    </div>
                @empty
                    <div class="col-span-full empty-state p-12 text-center text-xs text-gray-400 space-y-2 bg-white rounded-2xl border-1.5 border-dashed border-[#CBD5E1]">
                        <p class="font-bold text-gray-900">Belum ada aktivitas CRM yang tercatat.</p>
                        @if(!$isExecutiveOrPimpinan)
                        <p>Klik tombol "Input Dokumen Resmi Sales" di atas untuk mencatat interaksi dan runtutan agenda aktivitas.</p>
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
            <div class="bg-white rounded-2xl max-w-5xl w-full shadow-2xl border border-[#CBD5E1] max-h-[94vh] flex flex-col overflow-hidden anim-fade-up">
                
                {{-- Modal Header Bar --}}
                <div class="flex items-center justify-between border-b border-[#E2E8F0] px-5 sm:px-6 py-3.5 shrink-0 bg-white">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-[#8F0A0D] text-[11px] font-bold uppercase tracking-wider"
                              x-text="(selectedDetail?.activity?.activity_type === 'Lesson Learned' || selectedDetail?.batch?.primary_factor) ? 'FORM EVALUASI & LESSON LEARNED PROSPEK' : 'FORM LAPORAN AKTIVITAS SALES'"></span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200"
                              x-text="(selectedDetail?.batch?.items?.length || 1) + ' Agenda'"></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <a :href="'/sales/activities/' + (selectedDetail?.activity?.id || '') + '/pdf'" 
                           target="_blank"
                           class="px-3.5 py-1.5 text-xs font-bold text-[#8F0A0D] bg-white hover:bg-red-50 border border-red-200 rounded-lg transition cursor-pointer shadow-xs inline-flex items-center gap-1.5 no-underline">
                            <svg class="w-3.5 h-3.5 text-[#8F0A0D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span>Download PDF</span>
                        </a>
                        <button type="button" @click="isDetailModalOpen = false" class="text-[#94A3B8] hover:text-[#1E293B] p-1.5 rounded-lg hover:bg-[#F1F5F9] transition cursor-pointer" title="Tutup">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </div>

                {{-- Scrollable Canvas Container (Identik Persis Engineer Paper Form) --}}
                <div class="flex-1 overflow-y-auto px-4 pt-4 pb-8 sm:px-6 sm:pt-6 sm:pb-8 bg-[#F1F5F9]/80">

                    {{-- ── KANVAS LEMBAR KERTAS: FORM LAPORAN AKTIVITAS SALES ── --}}
                    <div x-show="selectedDetail?.activity?.activity_type !== 'Lesson Learned' && !selectedDetail?.batch?.primary_factor"
                         class="bg-white border-2 border-[#CBD5E1] rounded-xl p-5 sm:p-7 shadow-sm space-y-5 text-[#0F172A] font-sans max-w-4xl mx-auto">
                        
                        {{-- Kop Surat Resmi Standar Engineer --}}
                        <div class="border-b-2 border-[#8F0A0D] pb-3 flex items-center justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <img src="/images/ipnet1.png" onerror="this.src='/images/ipnet.png'" alt="Logo IPNET" class="h-10 w-auto object-contain shrink-0">
                                <div>
                                    <h2 class="text-[15px] sm:text-[17px] font-black text-[#0F172A] uppercase tracking-wide leading-tight">
                                        FORM LAPORAN AKTIVITAS SALES
                                    </h2>
                                    <p class="text-[11px] text-[#475569] mt-0.5">
                                        Dokumen ini digunakan sebagai laporan aktivitas sales, notulensi koordinasi dengan klien, dan bukti pelaksanaan agenda bisnis.
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
                                            <td class="w-3/10 py-1.5 px-2.5 border-r border-[#1E293B] font-bold text-[#8F0A0D]" x-text="selectedDetail?.activity?.project?.name || '-'"></td>
                                            <td class="w-1/5 py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">No. Dokumen / Laporan</td>
                                            <td class="w-3/10 py-1.5 px-2.5 font-mono font-bold text-[#8F0A0D]" x-text="'SLS-ACT/' + new Date().getFullYear() + '/' + String(selectedDetail?.activity?.id || 1).padStart(4, '0')"></td>
                                        </tr>
                                        <tr class="border-b border-[#1E293B]">
                                            <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Judul / Topik Pertemuan</td>
                                            <td class="py-1.5 px-2.5 border-r border-[#1E293B] font-semibold text-[#0F172A]" x-text="selectedDetail?.batch?.title || selectedDetail?.activity?.subject || '-'"></td>
                                            <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Tipe Aktivitas / Agenda</td>
                                            <td class="py-1.5 px-2.5 font-semibold text-[#0F172A]" x-text="selectedDetail?.activity?.activity_type || 'Meeting'"></td>
                                        </tr>
                                        <tr class="border-b border-[#1E293B]">
                                            <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Sales Person (AE)</td>
                                            <td class="py-1.5 px-2.5 border-r border-[#1E293B] font-semibold text-[#0F172A]" x-text="selectedDetail?.activity?.sales?.name || 'Sales Team'"></td>
                                            <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Media / Lokasi Pertemuan</td>
                                            <td class="py-1.5 px-2.5" x-text="selectedDetail?.batch?.meeting_media || 'Onsite di Lokasi Klien'"></td>
                                        </tr>
                                        <tr>
                                            <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Tanggal Pelaksanaan</td>
                                            <td class="py-1.5 px-2.5 border-r border-[#1E293B] font-semibold text-slate-700" x-text="selectedDetail?.activity?.activity_date ? new Date(selectedDetail.activity.activity_date).toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }) : '-'"></td>
                                            <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Durasi Pelaksanaan</td>
                                            <td class="py-1.5 px-2.5 font-bold text-[#8F0A0D]" x-text="(selectedDetail?.batch?.duration_days ? selectedDetail.batch.duration_days + ' Hari' : '1 Hari')"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- B. KOMPOSISI PESERTA PERTEMUAN (ATTENDEES) --}}
                        <div class="space-y-1.5">
                            <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">B. KOMPOSISI PESERTA PERTEMUAN (ATTENDEES)</h3>
                            <div class="border border-[#1E293B] rounded-sm overflow-hidden text-xs">
                                <table class="w-full border-collapse border border-[#1E293B]">
                                    <thead class="bg-[#EBF3FB] text-[#0F172A] font-bold uppercase text-[10.5px]">
                                        <tr>
                                            <th class="py-1.5 px-2 border-r border-b border-[#1E293B] w-8 text-center">No</th>
                                            <th class="py-1.5 px-2.5 border-r border-b border-[#1E293B] w-28 text-left">Pihak</th>
                                            <th class="py-1.5 px-2.5 border-r border-b border-[#1E293B] text-left">Nama Peserta</th>
                                            <th class="py-1.5 px-2.5 border-r border-b border-[#1E293B] text-left">Jabatan / Peran</th>
                                            <th class="py-1.5 px-2.5 border-r border-b border-[#1E293B] text-left">Kontak / No. HP</th>
                                            <th class="py-1.5 px-2.5 border-b border-[#1E293B] text-left">Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-[#1E293B]">
                                        <template x-if="selectedDetail?.batch?.attendees && selectedDetail.batch.attendees.length > 0">
                                            <template x-for="(att, aIdx) in selectedDetail.batch.attendees" :key="aIdx">
                                                <tr>
                                                    <td class="py-1.5 px-2 border-r border-[#1E293B] text-center font-bold text-[#0F172A]" x-text="aIdx + 1"></td>
                                                    <td class="py-1.5 px-2.5 border-r border-[#1E293B] font-bold" x-text="att.pihak || 'Klien'"></td>
                                                    <td class="py-1.5 px-2.5 border-r border-[#1E293B] font-bold" x-text="att.nama || '-'"></td>
                                                    <td class="py-1.5 px-2.5 border-r border-[#1E293B]" x-text="att.jabatan || '-'"></td>
                                                    <td class="py-1.5 px-2.5 border-r border-[#1E293B]" x-text="att.kontak || '-'"></td>
                                                    <td class="py-1.5 px-2.5" x-text="att.keterangan || '-'"></td>
                                                </tr>
                                            </template>
                                        </template>
                                        <template x-if="!selectedDetail?.batch?.attendees || selectedDetail.batch.attendees.length === 0">
                                            <tr>
                                                <td class="py-1.5 px-2 border-r border-[#1E293B] text-center font-bold text-[#0F172A]">1</td>
                                                <td class="py-1.5 px-2.5 border-r border-[#1E293B] font-bold">Internal IPNET</td>
                                                <td class="py-1.5 px-2.5 border-r border-[#1E293B] font-bold" x-text="selectedDetail?.activity?.sales?.name || 'Sales In Charge'"></td>
                                                <td class="py-1.5 px-2.5 border-r border-[#1E293B]">Account Executive</td>
                                                <td class="py-1.5 px-2.5 border-r border-[#1E293B]">-</td>
                                                <td class="py-1.5 px-2.5">Sales Person In Charge</td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- C. RUANG LINGKUP KEBUTUHAN & ESTIMASI NILAI (OPPORTUNITY SCOPE) --}}
                        <div x-show="selectedDetail?.batch?.scope_category || selectedDetail?.batch?.scope_description || selectedDetail?.batch?.estimated_budget" class="space-y-1.5">
                            <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">C. RUANG LINGKUP KEBUTUHAN &amp; ESTIMASI NILAI (OPPORTUNITY SCOPE)</h3>
                            <div class="border border-[#1E293B] rounded-sm overflow-hidden text-xs">
                                <table class="w-full border-collapse border border-[#1E293B]">
                                    <tbody>
                                        <tr class="border-b border-[#1E293B]">
                                            <td class="w-1/5 py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Kategori Kebutuhan</td>
                                            <td class="py-1.5 px-2.5 font-bold text-[#0F172A]" colspan="3" x-text="selectedDetail?.batch?.scope_category || '-'"></td>
                                        </tr>
                                        <tr x-show="selectedDetail?.batch?.scope_description" class="border-b border-[#1E293B]">
                                            <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B] align-top">Ringkasan Kebutuhan</td>
                                            <td class="py-1.5 px-2.5 whitespace-pre-line text-[#0F172A]" colspan="3" x-text="selectedDetail?.batch?.scope_description || '-'"></td>
                                        </tr>
                                        <tr>
                                            <td class="w-1/5 py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Estimasi Budget Customer</td>
                                            <td class="w-3/10 py-1.5 px-2.5 border-r border-[#1E293B] font-bold text-[#8F0A0D]" x-text="selectedDetail?.batch?.estimated_budget ? 'Rp ' + Number(selectedDetail.batch.estimated_budget).toLocaleString('id-ID') : 'Dalam Proses Estimasi'"></td>
                                            <td class="w-1/5 py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Target Tender / PO</td>
                                            <td class="w-3/10 py-1.5 px-2.5 font-semibold" x-text="selectedDetail?.batch?.tender_timeline || '-'"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- D. RINCIAN KRONOLOGIS AGENDA & NOTULENSI PEMBAHASAN --}}
                        <div class="space-y-1.5">
                            <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">D. RINCIAN KRONOLOGIS AGENDA &amp; NOTULENSI PEMBAHASAN</h3>
                            <div class="border border-[#1E293B] rounded-sm overflow-hidden text-xs">
                                <table class="w-full border-collapse border border-[#1E293B]">
                                    <thead class="bg-[#EBF3FB] text-[#0F172A] font-bold uppercase text-[10.5px]">
                                        <tr>
                                            <th class="py-1.5 px-2 border-r border-b border-[#1E293B] w-8 text-center">No</th>
                                            <th class="py-1.5 px-2.5 border-r border-b border-[#1E293B] w-28 text-left">Waktu &amp; Tanggal</th>
                                            <th class="py-1.5 px-2.5 border-r border-b border-[#1E293B] text-left">Kronologis Agenda / Aktivitas</th>
                                            <th class="py-1.5 px-2.5 border-r border-b border-[#1E293B] w-28 text-left">PIC Klien</th>
                                            <th class="py-1.5 px-2.5 border-r border-b border-[#1E293B] w-28 text-left">PIC IPNET</th>
                                            <th class="py-1.5 px-2.5 border-b border-[#1E293B] text-left">Catatan Aksi / Detail</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-[#1E293B]">
                                        <template x-if="selectedDetail?.batch?.items && selectedDetail.batch.items.length > 0">
                                            <template x-for="(item, idx) in selectedDetail.batch.items" :key="idx">
                                                <tr>
                                                    <td class="py-2 px-2 border-r border-[#1E293B] text-center font-bold text-[#0F172A]" x-text="item.no || (idx + 1)"></td>
                                                    <td class="py-2 px-2.5 border-r border-[#1E293B]">
                                                        <div class="font-bold text-[#0F172A]" x-text="item.time || '-'"></div>
                                                        <div class="text-[10px] text-gray-500" x-text="item.date || '-'"></div>
                                                    </td>
                                                    <td class="py-2 px-2.5 border-r border-[#1E293B] font-bold text-[#0F172A]" x-text="item.subject || '-'"></td>
                                                    <td class="py-2 px-2.5 border-r border-[#1E293B]" x-text="item.client_pic || '-'"></td>
                                                    <td class="py-2 px-2.5 border-r border-[#1E293B]" x-text="item.ipnet_pic || '-'"></td>
                                                    <td class="py-2 px-2.5 whitespace-pre-line text-slate-700" x-text="item.notes || '-'"></td>
                                                </tr>
                                            </template>
                                        </template>
                                        <template x-if="!selectedDetail?.batch?.items || selectedDetail.batch.items.length === 0">
                                            <tr>
                                                <td class="py-2 px-2 border-r border-[#1E293B] text-center font-bold text-[#0F172A]">1</td>
                                                <td class="py-2 px-2.5 border-r border-[#1E293B]">
                                                    <div class="font-bold text-[#0F172A]" x-text="selectedDetail?.activity?.activity_date ? new Date(selectedDetail.activity.activity_date).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) : '-'"></div>
                                                    <div class="text-[10px] text-gray-500" x-text="selectedDetail?.activity?.activity_date ? new Date(selectedDetail.activity.activity_date).toLocaleDateString('id-ID') : '-'"></div>
                                                </td>
                                                <td class="py-2 px-2.5 border-r border-[#1E293B] font-bold text-[#0F172A]" x-text="selectedDetail?.activity?.subject || '-'"></td>
                                                <td class="py-2 px-2.5 border-r border-[#1E293B]">-</td>
                                                <td class="py-2 px-2.5 border-r border-[#1E293B]" x-text="selectedDetail?.activity?.sales?.name || 'Sales'"></td>
                                                <td class="py-2 px-2.5 whitespace-pre-line text-slate-700" x-text="selectedDetail?.activity?.notes || '-'"></td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- E. IDENTIFIKASI KENDALA, KOMPETITOR & POTENSI RISIKO --}}
                        <div x-show="selectedDetail?.batch?.competitors || selectedDetail?.batch?.challenges || selectedDetail?.batch?.win_probability" class="space-y-1.5">
                            <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">E. IDENTIFIKASI KENDALA, KOMPETITOR &amp; POTENSI RISIKO</h3>
                            <div class="border border-[#1E293B] rounded-sm overflow-hidden text-xs">
                                <table class="w-full border-collapse border border-[#1E293B]">
                                    <tbody>
                                        <tr class="border-b border-[#1E293B]">
                                            <td class="w-1/4 py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Kompetitor / Brand Pesaing</td>
                                            <td class="py-1.5 px-2.5 font-bold text-[#8F0A0D]" colspan="3" x-text="selectedDetail?.batch?.competitors || '-'"></td>
                                        </tr>
                                        <tr x-show="selectedDetail?.batch?.challenges" class="border-b border-[#1E293B]">
                                            <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B] align-top">Kendala / Isu Kritis Customer</td>
                                            <td class="py-1.5 px-2.5 whitespace-pre-line text-[#0F172A]" colspan="3" x-text="selectedDetail?.batch?.challenges || '-'"></td>
                                        </tr>
                                        <tr>
                                            <td class="w-1/4 py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Estimasi Win Probability</td>
                                            <td class="py-1.5 px-2.5 font-bold text-[#0F172A]" colspan="3" x-text="(selectedDetail?.batch?.win_probability || selectedDetail?.activity?.project?.win_probability || 50) + '%'"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- F. RENCANA TINDAK LANJUT BERIKUTNYA --}}
                        <div x-show="selectedDetail?.activity?.next_action || selectedDetail?.batch?.next_action_pic" class="space-y-1.5">
                            <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">F. RENCANA TINDAK LANJUT BERIKUTNYA (NEXT ACTION PLAN &amp; COMMITMENT)</h3>
                            <div class="border border-[#1E293B] rounded-sm overflow-hidden text-xs">
                                <table class="w-full border-collapse border border-[#1E293B]">
                                    <tbody>
                                        <tr class="border-b border-[#1E293B]">
                                            <td class="w-1/4 py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Komitmen / Aksi Berikutnya</td>
                                            <td class="w-1/4 py-1.5 px-2.5 border-r border-[#1E293B] font-bold text-[#0F172A]" x-text="selectedDetail?.activity?.next_action || '-'"></td>
                                            <td class="w-1/4 py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">PIC Pelaksana</td>
                                            <td class="w-1/4 py-1.5 px-2.5 font-bold text-[#0F172A]" x-text="selectedDetail?.batch?.next_action_pic || selectedDetail?.activity?.sales?.name || 'Sales Team'"></td>
                                        </tr>
                                        <tr>
                                            <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Target Due Date</td>
                                            <td class="py-1.5 px-2.5 font-bold text-[#0F172A]" colspan="3" x-text="selectedDetail?.activity?.next_action_date ? new Date(selectedDetail.activity.next_action_date).toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }) : 'Segera'"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- G. LEMBAR PENGESAHAN & VERIFIKASI RESMI (TANDA TANGAN) --}}
                        <div class="space-y-1.5">
                            <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">G. LEMBAR PENGESAHAN &amp; VERIFIKASI (TANDA TANGAN)</h3>
                            <div class="border border-[#1E293B] rounded-sm overflow-hidden text-xs">
                                <table class="w-full border-collapse border border-[#1E293B]">
                                    <thead class="bg-[#EBF3FB] font-bold text-[#0F172A] border-b border-[#1E293B] uppercase text-[10.5px]">
                                        <tr>
                                            <th class="py-2 px-3 w-1/4 border-r border-[#1E293B] text-left">Pihak</th>
                                            <th class="py-2 px-3 w-1/4 border-r border-[#1E293B] text-left">Nama</th>
                                            <th class="py-2 px-3 w-1/6 border-r border-[#1E293B] text-center">Tanggal</th>
                                            <th class="py-2 px-3 w-1/3 text-center">Tanda Tangan</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-[#1E293B]">
                                        <tr>
                                            <td class="py-2.5 px-3 font-bold bg-[#F8FAFC] border-r border-[#1E293B] align-middle">Account Executive (Sales)</td>
                                            <td class="p-2 border-r border-[#1E293B] font-bold text-[#0F172A] align-middle" x-text="selectedDetail?.activity?.sales?.name || '{{ auth()->user()->name }}'"></td>
                                            <td class="p-2 border-r border-[#1E293B] text-center align-middle font-medium" x-text="selectedDetail?.activity?.activity_date ? new Date(selectedDetail.activity.activity_date).toLocaleDateString('id-ID') : '{{ date('d/m/Y') }}'"></td>
                                            <td class="h-14 p-2 text-center align-middle bg-white"></td>
                                        </tr>
                                        <tr>
                                            <td class="py-2.5 px-3 font-bold bg-[#F8FAFC] border-r border-[#1E293B] align-middle">Division Head / Atasan Sales</td>
                                            <td class="p-2 border-r border-[#1E293B] font-bold text-[#0F172A] align-middle">Susanto Djaya</td>
                                            <td class="p-2 border-r border-[#1E293B] text-center align-middle font-medium" x-text="selectedDetail?.activity?.activity_date ? new Date(selectedDetail.activity.activity_date).toLocaleDateString('id-ID') : '{{ date('d/m/Y') }}'"></td>
                                            <td class="h-14 p-2 text-center align-middle bg-white"></td>
                                        </tr>
                                        <tr>
                                            <td class="py-2.5 px-3 font-bold bg-[#F8FAFC] border-r border-[#1E293B] align-middle">Customer / PIC Klien</td>
                                            <td class="p-2 border-r border-[#1E293B] font-semibold text-slate-700 align-middle" x-text="selectedDetail?.activity?.project?.client || 'PIC Customer Representative'"></td>
                                            <td class="p-2 border-r border-[#1E293B] text-center align-middle font-medium" x-text="selectedDetail?.activity?.activity_date ? new Date(selectedDetail.activity.activity_date).toLocaleDateString('id-ID') : '{{ date('d/m/Y') }}'"></td>
                                            <td class="h-14 p-2 text-center align-middle bg-white"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Footer Dokumen Resmi --}}
                        <div class="pt-3 border-t border-dashed border-[#CBD5E1] text-[10.5px] text-[#475569] space-y-1">
                            <p><strong>Catatan Dokumen Resmi:</strong> Dokumen ini diterbitkan secara sah melalui Sistem Manajemen Penjualan PT IP Network Solusindo. Berfungsi sebagai bukti otentik notulensi koordinasi bisnis, berita acara pertemuan komersial, dan rujukan audit operasional sales.</p>
                            <p class="text-[9.5px] text-[#64748B] pt-1 border-t border-gray-200 flex items-center justify-between">
                                <span>Dokumen ini merupakan milik PT.IP Network Solusindo.</span>
                                <span>&copy; PT.IP Network Solusindo, Seluruh Hak Dilindungi.</span>
                            </p>
                        </div>

                    </div>

                    {{-- ── KANVAS LEMBAR KERTAS: FORM EVALUASI & LESSON LEARNED PROSPEK (LOST DEAL) ── --}}
                    <div x-show="selectedDetail?.activity?.activity_type === 'Lesson Learned' || selectedDetail?.batch?.primary_factor"
                         class="bg-white border-2 border-[#CBD5E1] rounded-xl p-5 sm:p-7 shadow-sm space-y-5 text-[#0F172A] font-sans max-w-4xl mx-auto">
                        
                        {{-- Kop Surat Resmi Standar Engineer --}}
                        <div class="border-b-2 border-[#8F0A0D] pb-3 flex items-center justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <img src="/images/ipnet1.png" onerror="this.src='/images/ipnet.png'" alt="Logo IPNET" class="h-10 w-auto object-contain shrink-0">
                                <div>
                                    <h2 class="text-[15px] sm:text-[17px] font-black text-[#0F172A] uppercase tracking-wide leading-tight">
                                        FORM EVALUASI &amp; LESSON LEARNED (LOST DEAL)
                                    </h2>
                                    <p class="text-[11px] text-[#475569] mt-0.5">
                                        Dokumen ini digunakan sebagai evaluasi retrospektif internal dan dasar continuous improvement strategi komersial penjualan.
                                    </p>
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="px-3 py-1 bg-red-50 text-[#8F0A0D] border border-red-200 rounded-lg text-xs font-bold uppercase tracking-wider">
                                    Dokumen Resmi
                                </span>
                            </div>
                        </div>

                        {{-- A. IDENTITAS PROSPEK / TENDER --}}
                        <div class="space-y-1.5">
                            <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">A. IDENTITAS PROSPEK / TENDER</h3>
                            <div class="border border-[#1E293B] rounded-sm overflow-hidden text-xs">
                                <table class="w-full border-collapse border border-[#1E293B]">
                                    <tbody>
                                        <tr class="border-b border-[#1E293B]">
                                            <td class="w-1/4 py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Nama Klien / Perusahaan</td>
                                            <td class="w-1/4 py-1.5 px-2.5 border-r border-[#1E293B] font-bold text-[#0F172A]" x-text="selectedDetail?.activity?.project?.client || '-'"></td>
                                            <td class="w-1/4 py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Nama Prospek / Proyek</td>
                                            <td class="w-1/4 py-1.5 px-2.5 font-bold text-[#0F172A]" x-text="selectedDetail?.activity?.project?.name || '-'"></td>
                                        </tr>
                                        <tr class="border-b border-[#1E293B]">
                                            <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Sales Person (AE)</td>
                                            <td class="py-1.5 px-2.5 border-r border-[#1E293B] font-semibold text-[#0F172A]" x-text="selectedDetail?.activity?.sales?.name || 'Sales Team'"></td>
                                            <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Presales / Arsitek Solusi</td>
                                            <td class="py-1.5 px-2.5 font-semibold text-[#0F172A]">Presales Team</td>
                                        </tr>
                                        <tr>
                                            <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Nilai Penawaran IPNET</td>
                                            <td class="py-1.5 px-2.5 border-r border-[#1E293B] font-bold text-[#0F172A]" x-text="(selectedDetail?.batch?.our_price || selectedDetail?.activity?.project?.contract_value) ? 'Rp ' + Number(selectedDetail?.batch?.our_price || selectedDetail?.activity?.project?.contract_value).toLocaleString('id-ID') : 'Rp -'"></td>
                                            <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Tanggal Evaluasi / Review</td>
                                            <td class="py-1.5 px-2.5 font-semibold" x-text="selectedDetail?.activity?.activity_date ? new Date(selectedDetail.activity.activity_date).toLocaleDateString('id-ID', { year: 'numeric', month: 'short', day: 'numeric' }) : '{{ date('d M Y') }}'"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- B. FAKTOR UTAMA KEKALAHAN --}}
                        <div class="space-y-1.5">
                            <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">B. FAKTOR UTAMA KEKALAHAN (PRIMARY LOSS DRIVER)</h3>
                            <div class="border border-[#CBD5E1] rounded-sm p-3 bg-[#F8FAFC] text-xs">
                                <p class="font-bold text-[#0F172A]" x-text="selectedDetail?.batch?.primary_factor || selectedDetail?.activity?.project?.lost_reason || 'Harga / Komersial'"></p>
                            </div>
                        </div>

                        {{-- C. INFORMASI PEMENANG & PERBANDINGAN KOMERSIAL --}}
                        <div class="space-y-1.5">
                            <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">C. INFORMASI PEMENANG &amp; PERBANDINGAN KOMERSIAL</h3>
                            <div class="border border-[#1E293B] rounded-sm overflow-hidden text-xs">
                                <table class="w-full border-collapse border border-[#1E293B]">
                                    <tbody>
                                        <tr class="border-b border-[#1E293B]">
                                            <td class="w-1/4 py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Vendor / Kompetitor Pemenang</td>
                                            <td class="w-1/4 py-1.5 px-2.5 border-r border-[#1E293B] font-bold text-[#0F172A]" x-text="selectedDetail?.batch?.competitor_winner || selectedDetail?.activity?.project?.lost_competitor || '-'"></td>
                                            <td class="w-1/4 py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Estimasi Harga Pemenang</td>
                                            <td class="w-1/4 py-1.5 px-2.5 font-semibold" x-text="selectedDetail?.batch?.competitor_price ? 'Rp ' + Number(selectedDetail.batch.competitor_price).toLocaleString('id-ID') : '-'"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- D. KRONOLOGI & TITIK BALIK KEKALAHAN (TURNING POINT) --}}
                        <div class="space-y-1.5">
                            <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">D. KRONOLOGI &amp; TITIK BALIK KEKALAHAN (TURNING POINT)</h3>
                            <div class="border border-[#1E293B] rounded-sm p-3 bg-white text-xs whitespace-pre-line text-[#0F172A]" x-text="selectedDetail?.batch?.turning_point || 'Evaluasi perbandingan penawaran komersial dan lead time.'"></div>
                        </div>

                        {{-- E. ANALISIS GAP: KELEMAHAN INTERNAL VS KEUNGGULAN KOMPETITOR (TEKS HITAM) --}}
                        <div class="space-y-1.5">
                            <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">E. ANALISIS GAP: KELEMAHAN INTERNAL VS KEUNGGULAN KOMPETITOR</h3>
                            <div class="border border-[#1E293B] rounded-sm overflow-hidden text-xs">
                                <table class="w-full border-collapse border border-[#1E293B]">
                                    <thead class="bg-[#EBF3FB] font-bold text-[#000000] border-b border-[#1E293B] uppercase text-[10.5px]">
                                        <tr>
                                            <th class="py-2 px-3 w-1/2 border-r border-[#1E293B] text-left text-black">Faktor Kelemahan Penawaran IPNET</th>
                                            <th class="py-2 px-3 w-1/2 text-left text-black">Keunggulan Penawaran Kompetitor Pemenang</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="p-3 border-r border-[#1E293B] whitespace-pre-line align-top text-[#0F172A]" x-text="selectedDetail?.batch?.gap_our_weakness || '-'"></td>
                                            <td class="p-3 whitespace-pre-line align-top text-[#0F172A]" x-text="selectedDetail?.batch?.gap_competitor_strength || '-'"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- F. LESSON LEARNED & KEY TAKEAWAYS (NEUTRAL CARD) --}}
                        <div class="space-y-1.5">
                            <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">F. LESSON LEARNED &amp; KEY TAKEAWAYS</h3>
                            <div class="border border-[#1E293B] rounded-sm p-3 bg-white text-xs whitespace-pre-line text-[#0F172A]" x-text="selectedDetail?.batch?.lessons_learned || '-'"></div>
                        </div>

                        {{-- G. RENCANA PERBAIKAN & TINDAK LANJUT MASA DEPAN (NEUTRAL CARD) --}}
                        <div class="space-y-1.5">
                            <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">G. RENCANA PERBAIKAN &amp; TINDAK LANJUT MASA DEPAN (RE-ENGAGEMENT PLAN)</h3>
                            <div class="border border-[#1E293B] rounded-sm p-3 bg-white text-xs whitespace-pre-line text-[#0F172A]" x-text="selectedDetail?.batch?.future_strategy || 'Tetap menjalin komunikasi berkala dengan PIC klien untuk peluang periode berikutnya.'"></div>
                        </div>

                        {{-- H. LEMBAR PENGESAHAN & VERIFIKASI RESMI (TANDA TANGAN) --}}
                        <div class="space-y-1.5">
                            <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">H. LEMBAR PENGESAHAN &amp; VERIFIKASI (TANDA TANGAN)</h3>
                            <div class="border border-[#1E293B] rounded-sm overflow-hidden text-xs">
                                <table class="w-full border-collapse border border-[#1E293B]">
                                    <thead class="bg-[#EBF3FB] font-bold text-[#0F172A] border-b border-[#1E293B] uppercase text-[10.5px]">
                                        <tr>
                                            <th class="py-2 px-3 w-1/4 border-r border-[#1E293B] text-left">Pihak Evaluator</th>
                                            <th class="py-2 px-3 w-1/4 border-r border-[#1E293B] text-left">Nama Personel</th>
                                            <th class="py-2 px-3 w-1/6 border-r border-[#1E293B] text-center">Tanggal</th>
                                            <th class="py-2 px-3 w-1/3 text-center">Tanda Tangan</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-[#1E293B]">
                                        <tr>
                                            <td class="py-2.5 px-3 font-bold bg-[#F8FAFC] border-r border-[#1E293B] align-middle">Account Executive (Sales)</td>
                                            <td class="p-2 border-r border-[#1E293B] font-bold text-[#0F172A] align-middle" x-text="selectedDetail?.activity?.sales?.name || '{{ auth()->user()->name }}'"></td>
                                            <td class="p-2 border-r border-[#1E293B] text-center align-middle font-medium" x-text="selectedDetail?.activity?.activity_date ? new Date(selectedDetail.activity.activity_date).toLocaleDateString('id-ID') : '{{ date('d/m/Y') }}'"></td>
                                            <td class="h-14 p-2 text-center align-middle bg-white"></td>
                                        </tr>
                                        <tr>
                                            <td class="py-2.5 px-3 font-bold bg-[#F8FAFC] border-r border-[#1E293B] align-middle">Division Head / Atasan Sales</td>
                                            <td class="p-2 border-r border-[#1E293B] font-bold text-[#0F172A] align-middle">Susanto Djaya</td>
                                            <td class="p-2 border-r border-[#1E293B] text-center align-middle font-medium" x-text="selectedDetail?.activity?.activity_date ? new Date(selectedDetail.activity.activity_date).toLocaleDateString('id-ID') : '{{ date('d/m/Y') }}'"></td>
                                            <td class="h-14 p-2 text-center align-middle bg-white"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Footer Dokumen Resmi --}}
                        <div class="pt-3 border-t border-dashed border-[#CBD5E1] text-[10.5px] text-[#475569] space-y-1">
                            <p><strong>Catatan Dokumen Resmi:</strong> Dokumen evaluasi ini diterbitkan secara sah melalui Sistem Terintegrasi CRM &amp; Pipeline PT IP Network Solusindo.</p>
                            <p class="text-[9.5px] text-[#64748B] pt-1 border-t border-gray-200 flex items-center justify-between">
                                <span>Dokumen ini merupakan milik PT.IP Network Solusindo.</span>
                                <span>&copy; PT.IP Network Solusindo, Seluruh Hak Dilindungi.</span>
                            </p>
                        </div>

                    </div>

                </div>

                {{-- Modal Footer --}}
                <div class="flex items-center justify-between p-4 px-6 border-t border-[#E2E8F0] bg-white shrink-0">
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
                            <h3 class="text-[16px] font-bold text-[#0F172A]" x-text="isBulkEditing ? (selectedCategory === 'lesson_learned' ? 'Edit Form Evaluasi & Lesson Learned Prospek' : 'Edit Form Laporan Aktivitas Sales') : 'Input Form Laporan Aktivitas Sales'"></h3>
                            <p class="text-[11px] text-[#64748B]" x-text="isBulkEditing ? 'Perbarui formulir laporan kerja resmi di bawah ini sesuai acuan template PT IP Network Solusindo.' : 'Isi formulir laporan kerja resmi di bawah ini sesuai acuan template PT IP Network Solusindo.'"></p>
                        </div>
                    </div>
                    <button type="button" @click="isBulkModalOpen = false" class="text-[#64748B] hover:text-[#1E293B] p-1.5 rounded-lg hover:bg-[#E2E8F0] transition cursor-pointer" title="Tutup">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form :action="bulkFormUrl" method="POST" @submit="isSubmitting = true" class="flex flex-col flex-1 min-h-0 overflow-hidden">
                    @csrf
                    <template x-if="isBulkEditing">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

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
                                            FORM LAPORAN AKTIVITAS SALES
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
                                                    <select name="activity_type" x-model="bulkActivityType" class="w-full p-1 bg-transparent border-0 focus:ring-1 focus:ring-[#8F0A0D] font-semibold text-xs cursor-pointer">
                                                        @foreach($activityTypes as $typeKey => $typeLabel)
                                                            <option value="{{ $typeKey }}">{{ $typeLabel }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr class="border-b border-[#1E293B]">
                                                <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Sales Person (AE)</td>
                                                <td class="py-1 px-2.5 border-r border-[#1E293B] font-semibold text-xs text-[#0F172A]">
                                                    <span x-text="selectedProjectData?.sales_name || '{{ auth()->user()->name }}'"></span>
                                                </td>
                                                <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Media / Lokasi Pertemuan</td>
                                                <td class="py-1 px-2">
                                                    <select name="meeting_media" x-model="meetingMedia" class="w-full p-1 bg-transparent border-0 focus:ring-1 focus:ring-[#8F0A0D] text-xs font-medium cursor-pointer">
                                                        <option value="Onsite di Lokasi Klien">Onsite di Lokasi Klien / Customer Office</option>
                                                        <option value="Kantor IP Network Solusindo">Kantor IP Network Solusindo</option>
                                                        <option value="Virtual Video Conference (Zoom / Google Meet)">Virtual Video Conference (Zoom / Google Meet)</option>
                                                        <option value="Phone Call / WhatsApp Coordination">Phone Call / WhatsApp Coordination</option>
                                                        <option value="Business Lunch / Dinning">Business Lunch / Dinning</option>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Tanggal Pelaksanaan</td>
                                                <td class="py-1 px-2.5 border-r border-[#1E293B] font-semibold text-xs text-slate-700">
                                                    {{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}
                                                </td>
                                                <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Durasi Pelaksanaan</td>
                                                <td class="py-1 px-2">
                                                    <div class="flex items-center gap-1.5 text-xs">
                                                        <input type="number" min="1" max="365" name="duration_days" x-model="durationDays" placeholder="1" class="w-20 p-1 border border-gray-300 rounded text-xs font-bold text-center text-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]">
                                                        <span class="font-bold text-[#0F172A]">Hari</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- B. KOMPOSISI PESERTA PERTEMUAN (ATTENDEES) --}}
                            <div class="space-y-1.5">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">
                                        B. KOMPOSISI PESERTA PERTEMUAN (ATTENDEES)
                                    </h3>
                                    <div class="flex items-center gap-3">
                                        <button type="button" @click="addAttendeeRow()" class="text-[11px] font-bold text-[#8F0A0D] hover:underline flex items-center gap-1 cursor-pointer">
                                            <span>+ Tambah Peserta</span>
                                        </button>
                                        <button type="button" @click="removeAttendeeRow()" x-show="attendees.length > 1" class="text-[11px] font-bold text-gray-500 hover:text-red-600 hover:underline flex items-center gap-1 cursor-pointer">
                                            <span>- Hapus Peserta</span>
                                        </button>
                                    </div>
                                </div>
                                <div class="border border-[#1E293B] rounded-sm overflow-hidden text-xs">
                                    <table class="w-full border-collapse border border-[#1E293B]">
                                        <thead class="bg-[#EBF3FB] font-bold text-[#0F172A] border-b border-[#1E293B]">
                                            <tr>
                                                <th class="py-1.5 px-2 w-10 text-center border-r border-[#1E293B]">No.</th>
                                                <th class="py-1.5 px-2 w-32 border-r border-[#1E293B] text-left">Pihak</th>
                                                <th class="py-1.5 px-2 w-1/4 border-r border-[#1E293B] text-left">Nama Peserta</th>
                                                <th class="py-1.5 px-2 w-1/4 border-r border-[#1E293B] text-left">Jabatan / Peran</th>
                                                <th class="py-1.5 px-2 w-1/5 border-r border-[#1E293B] text-left">Kontak / No. HP</th>
                                                <th class="py-1.5 px-2 text-left">Keterangan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <template x-for="(att, attIdx) in attendees" :key="attIdx">
                                                <tr class="border-b border-[#1E293B]">
                                                    <td class="text-center font-bold py-1 px-1 border-r border-[#1E293B]" x-text="attIdx + 1"></td>
                                                    <td class="p-1 border-r border-[#1E293B]">
                                                        <select :name="'attendees[' + attIdx + '][pihak]'" x-model="att.pihak" class="w-full p-1 text-xs border-0 bg-transparent font-semibold cursor-pointer">
                                                            <option value="Internal IPNET">Internal IPNET</option>
                                                            <option value="Klien">Klien / Customer</option>
                                                            <option value="Partner / Principal">Partner / Principal</option>
                                                        </select>
                                                    </td>
                                                    <td class="p-1 border-r border-[#1E293B]">
                                                        <input type="text" :name="'attendees[' + attIdx + '][nama]'" x-model="att.nama" placeholder="Nama Peserta" class="w-full p-1 text-xs border-0 bg-transparent font-semibold">
                                                    </td>
                                                    <td class="p-1 border-r border-[#1E293B]">
                                                        <input type="text" :name="'attendees[' + attIdx + '][jabatan]'" x-model="att.jabatan" placeholder="Jabatan / Peran" class="w-full p-1 text-xs border-0 bg-transparent">
                                                    </td>
                                                    <td class="p-1 border-r border-[#1E293B]">
                                                        <input type="text" :name="'attendees[' + attIdx + '][kontak]'" x-model="att.kontak" placeholder="No. HP / Email" class="w-full p-1 text-xs border-0 bg-transparent">
                                                    </td>
                                                    <td class="p-1">
                                                        <input type="text" :name="'attendees[' + attIdx + '][keterangan]'" x-model="att.keterangan" placeholder="Keterangan kehadiran..." class="w-full p-1 text-xs border-0 bg-transparent">
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="text-[11.5px] font-semibold text-[#334155] pt-0.5">
                                    Total peserta pertemuan: <u>&nbsp;&nbsp;<strong x-text="attendees.length"></strong>&nbsp;&nbsp;</u> orang
                                </div>
                            </div>

                            {{-- C. RUANG LINGKUP KEBUTUHAN & ESTIMASI NILAI (OPPORTUNITY SCOPE) --}}
                            <div class="space-y-1.5">
                                <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">
                                    C. RUANG LINGKUP KEBUTUHAN &amp; ESTIMASI NILAI (OPPORTUNITY SCOPE)
                                </h3>
                                <div class="border border-[#1E293B] rounded-sm overflow-hidden text-xs">
                                    <table class="w-full border-collapse border border-[#1E293B]">
                                        <tbody>
                                            <tr class="border-b border-[#1E293B]">
                                                <td class="w-1/4 py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Kategori Kebutuhan Prospek</td>
                                                <td class="w-3/4 p-1">
                                                    <select name="scope_category" x-model="scopeCategory" class="w-full p-1 bg-transparent border-0 focus:ring-1 focus:ring-[#8F0A0D] font-semibold text-xs cursor-pointer">
                                                        <option value="Hardware & Perangkat Jaringan (Switch/Router/Firewall/Server)">Hardware &amp; Perangkat Jaringan (Switch/Router/Firewall/Server)</option>
                                                        <option value="Software, License & Cloud Subscription">Software, License &amp; Cloud Subscription</option>
                                                        <option value="Managed Service & SLA Operasional">Managed Service &amp; SLA Operasional</option>
                                                        <option value="Maintenance & Preventive Support">Maintenance &amp; Preventive Support</option>
                                                        <option value="Infrastruktur Kabel & Passive Network">Infrastruktur Kabel &amp; Passive Network</option>
                                                        <option value="Cyber Security & Perimeter Defense">Cyber Security &amp; Perimeter Defense</option>
                                                        <option value="Custom IT Solution & Integration">Custom IT Solution &amp; Integration</option>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr class="border-b border-[#1E293B]">
                                                <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B] align-top">Ringkasan Kebutuhan &amp; Spesifikasi</td>
                                                <td class="p-1">
                                                    <textarea name="scope_description" x-model="scopeDescription" rows="2" placeholder="Rangkuman spesifikasi teknis atau ruang lingkup pekerjaan yang diminta customer..." class="w-full p-1 text-xs border-0 bg-transparent resize-none focus:ring-1 focus:ring-[#8F0A0D]"></textarea>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Estimasi Budget &amp; Target Timeline</td>
                                                <td class="p-1">
                                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                                        <div class="flex items-center gap-1.5">
                                                             <span class="text-slate-500 font-bold shrink-0">Budget:</span>
                                                            <input type="number" name="estimated_budget" x-model="estimatedBudget" placeholder="Estimasi IDR..." class="w-full p-1 text-xs border border-gray-200 rounded font-semibold text-[#8F0A0D]">
                                                        </div>
                                                        <div class="flex items-center gap-1.5">
                                                            <span class="text-slate-500 font-bold shrink-0">Target Tender/PO:</span>
                                                            <input type="text" name="tender_timeline" x-model="tenderTimeline" placeholder="Contoh: Q4 2026 / Bulan Depan" class="w-full p-1 text-xs border border-gray-200 rounded font-medium">
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- D. RINCIAN KRONOLOGIS AGENDA & NOTULENSI PEMBAHASAN --}}
                            <div class="space-y-1.5">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">
                                        D. RINCIAN KRONOLOGIS AGENDA &amp; NOTULENSI PEMBAHASAN
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
                                                <th class="py-1.5 px-2 min-w-[200px] border-r border-[#1E293B] text-left">Catatan Aksi / Detail Notulensi</th>
                                                <th class="py-1.5 px-1 w-12 text-center">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <template x-for="(row, index) in bulkRows" :key="index">
                                                <tr class="border-b border-[#1E293B] hover:bg-[#F8FAFC]">
                                                    <td class="text-center font-bold py-1 px-1 border-r border-[#1E293B] text-[#0F172A]" x-text="index + 1"></td>
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
                                                                  placeholder="Rangkuman hasil diskusi, durasi, kesepakatan notulensi..."
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

                            {{-- E. IDENTIFIKASI KENDALA, KOMPETITOR & POTENSI RISIKO --}}
                            <div class="space-y-1.5">
                                <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">
                                    E. IDENTIFIKASI KENDALA, KOMPETITOR &amp; POTENSI RISIKO
                                </h3>
                                <div class="border border-[#1E293B] rounded-sm overflow-hidden text-xs">
                                    <table class="w-full border-collapse border border-[#1E293B]">
                                        <tbody>
                                            <tr class="border-b border-[#1E293B]">
                                                <td class="w-1/4 py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Kompetitor / Brand Pesaing</td>
                                                <td class="w-3/4 p-1">
                                                    <input type="text" name="competitors" x-model="competitors" placeholder="Nama kompetitor atau brand alternatif yang dipertimbangkan customer..." class="w-full p-1 text-xs border-0 bg-transparent">
                                                </td>
                                            </tr>
                                            <tr class="border-b border-[#1E293B]">
                                                <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B] align-top">Kendala / Isu Kritis Customer</td>
                                                <td class="p-1">
                                                    <textarea name="challenges" x-model="challenges" rows="2" placeholder="Tantangan procurement, batasan regulasi, atau masalah teknis customer..." class="w-full p-1 text-xs border-0 bg-transparent resize-none"></textarea>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">Estimasi Win Probability Saat Ini</td>
                                                <td class="p-1 flex items-center gap-2">
                                                    <input type="number" min="0" max="100" name="win_probability" x-model="winProbability" class="w-20 p-1 text-xs border border-gray-300 rounded font-bold text-center text-[#8F0A0D]">
                                                    <span class="font-bold text-xs">%</span>
                                                    <span class="text-[11px] text-slate-500 italic">(Otomatis memperbarui probabilitas kemenangan pada CRM Pipeline)</span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- F. RENCANA TINDAK LANJUT BERIKUTNYA --}}
                            <div class="space-y-1.5">
                                <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">
                                    F. RENCANA TINDAK LANJUT BERIKUTNYA (NEXT ACTION PLAN &amp; COMMITMENT)
                                </h3>
                                <div class="border border-[#1E293B] rounded-sm overflow-hidden text-xs">
                                    <table class="w-full border-collapse border border-[#1E293B]">
                                        <tbody>
                                            <tr>
                                                <td class="w-1/5 py-1.5 px-2.5 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">
                                                    Komitmen / Aksi Berikutnya
                                                </td>
                                                <td class="w-2/5 p-1 border-r border-[#1E293B]">
                                                    <input type="text" name="next_action" x-model="nextAction" placeholder="Contoh: Mengirimkan surat penawaran harga (SPH) revisi dan BoQ teknis" 
                                                           class="w-full p-1 bg-transparent border-0 focus:ring-1 focus:ring-[#8F0A0D] text-xs font-semibold text-[#0F172A]">
                                                </td>
                                                <td class="w-1/6 py-1.5 px-2 font-bold bg-[#F8FAFC] border-r border-[#1E293B]">
                                                    PIC Pelaksana
                                                </td>
                                                <td class="w-1/6 p-1 border-r border-[#1E293B]">
                                                    <input type="text" name="next_action_pic" x-model="nextActionPic" placeholder="PIC Aksi" class="w-full p-1 text-xs border-0 bg-transparent font-medium">
                                                </td>
                                                <td class="w-1/12 py-1.5 px-2 font-bold bg-[#F8FAFC] border-r border-[#1E293B] text-center">
                                                    Target
                                                </td>
                                                <td class="w-1/6 p-1">
                                                    <input type="date" name="next_action_date" x-model="nextActionDate"
                                                           class="w-full p-1 bg-transparent border-0 focus:ring-1 focus:ring-[#8F0A0D] text-xs font-medium cursor-pointer">
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- G. LEMBAR PENGESAHAN & VERIFIKASI (TANDA TANGAN) --}}
                            <div class="space-y-1.5">
                                <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">
                                    G. LEMBAR PENGESAHAN &amp; VERIFIKASI (TANDA TANGAN)
                                </h3>
                                <div class="border border-[#1E293B] rounded-sm overflow-hidden text-xs">
                                    <table class="w-full border-collapse border border-[#1E293B]">
                                        <thead class="bg-[#EBF3FB] font-bold text-[#0F172A] border-b border-[#1E293B]">
                                            <tr>
                                                <th class="py-1.5 px-3 w-1/4 border-r border-[#1E293B] text-left">Pihak</th>
                                                <th class="py-1.5 px-3 w-1/4 border-r border-[#1E293B] text-left">Nama</th>
                                                <th class="py-1.5 px-3 w-1/6 border-r border-[#1E293B] text-center">Tanggal</th>
                                                <th class="py-1.5 px-3 w-1/3 text-center">Tanda Tangan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="border-b border-[#1E293B]">
                                                <td class="py-2.5 px-3 font-bold bg-[#F8FAFC] border-r border-[#1E293B] align-middle">Account Executive (Sales)</td>
                                                <td class="p-1 px-2 border-r border-[#1E293B] font-bold text-[#0F172A] align-middle">{{ auth()->user()->name }}</td>
                                                <td class="p-1 px-2 border-r border-[#1E293B] text-center align-middle font-medium">{{ date('d/m/Y') }}</td>
                                                <td class="h-14 py-4 px-3 text-center align-middle bg-white"></td>
                                            </tr>
                                            <tr class="border-b border-[#1E293B]">
                                                <td class="py-2.5 px-3 font-bold bg-[#F8FAFC] border-r border-[#1E293B] align-middle">Division Head / Atasan Sales</td>
                                                <td class="p-1 px-2 border-r border-[#1E293B] font-bold text-[#0F172A] align-middle">Susanto Djaya</td>
                                                <td class="p-1 px-2 border-r border-[#1E293B] text-center align-middle font-medium">{{ date('d/m/Y') }}</td>
                                                <td class="h-14 py-4 px-3 text-center align-middle bg-white"></td>
                                            </tr>
                                            <tr>
                                                <td class="py-2.5 px-3 font-bold bg-[#F8FAFC] border-r border-[#1E293B] align-middle">Customer / PIC Klien</td>
                                                <td class="p-1 px-2 border-r border-[#1E293B] align-middle">
                                                    <input type="text" name="customer_rep_name" placeholder="Nama PIC Customer / Klien..." class="w-full p-1 bg-transparent border-0 focus:ring-1 focus:ring-[#8F0A0D] font-bold text-xs text-[#0F172A]">
                                                </td>
                                                <td class="p-1 px-2 border-r border-[#1E293B] text-center align-middle font-medium">{{ date('d/m/Y') }}</td>
                                                <td class="h-14 py-4 px-3 text-center align-middle bg-white"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- Catatan Footer Resmi Dokumen --}}
                            <div class="pt-2 text-[10.5px] text-[#475569] italic border-t border-dashed border-[#CBD5E1]">
                                <p><strong>Catatan Dokumen Resmi:</strong> Dokumen ini diterbitkan secara sah melalui Sistem Terintegrasi Penjualan &amp; Pipeline PT IP Network Solusindo. Berfungsi sebagai bukti otentik notulensi koordinasi bisnis, berita acara pertemuan komersial, dan rujukan audit operasional sales.</p>
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
                                            FORM EVALUASI &amp; LESSON LEARNED (LOST DEAL)
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

                            {{-- E. LEMBAR PENGESAHAN & REVIEW MANAGEMENT --}}
                            <div class="space-y-1.5">
                                <h3 class="text-xs font-black uppercase text-[#0F172A] tracking-wider">
                                    E. LEMBAR PENGESAHAN &amp; VERIFIKASI (TANDA TANGAN)
                                </h3>
                                <div class="border border-[#1E293B] rounded-sm overflow-hidden text-xs">
                                    <table class="w-full border-collapse border border-[#1E293B]">
                                        <thead class="bg-[#EBF3FB] font-bold text-[#0F172A] border-b border-[#1E293B]">
                                            <tr>
                                                <th class="py-1.5 px-3 w-1/4 border-r border-[#1E293B] text-left">Pihak Evaluator</th>
                                                <th class="py-1.5 px-3 w-1/4 border-r border-[#1E293B] text-left">Nama Personel</th>
                                                <th class="py-1.5 px-3 w-1/6 border-r border-[#1E293B] text-center">Tanggal</th>
                                                <th class="py-1.5 px-3 w-1/3 text-center">Tanda Tangan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="border-b border-[#1E293B]">
                                                <td class="py-2.5 px-3 font-bold bg-[#F8FAFC] border-r border-[#1E293B] align-middle">Account Executive (Sales)</td>
                                                <td class="p-1 px-2 border-r border-[#1E293B] font-bold text-[#0F172A] align-middle">{{ auth()->user()->name }}</td>
                                                <td class="p-1 px-2 border-r border-[#1E293B] text-center align-middle font-medium">{{ date('d/m/Y') }}</td>
                                                <td class="h-14 py-4 px-3 text-center align-middle bg-white"></td>
                                            </tr>
                                            <tr>
                                                <td class="py-2.5 px-3 font-bold bg-[#F8FAFC] border-r border-[#1E293B] align-middle">Division Head / Atasan Sales</td>
                                                <td class="p-1 px-2 border-r border-[#1E293B] font-bold text-[#0F172A] align-middle">Susanto Djaya</td>
                                                <td class="p-1 px-2 border-r border-[#1E293B] text-center align-middle font-medium">{{ date('d/m/Y') }}</td>
                                                <td class="h-14 py-4 px-3 text-center align-middle bg-white"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- Catatan Footer Resmi Dokumen --}}
                            <div class="pt-2 text-[10.5px] text-[#475569] italic border-t border-dashed border-[#CBD5E1]">
                                <p><strong>Catatan Dokumen Resmi:</strong> Dokumen Lesson Learned ini diterbitkan sebagai laporan evaluasi resmi komersial PT IP Network Solusindo untuk keperluan review manajemen, mitigasi risiko deal serupa, dan peningkatan win-rate tim penjualan di masa mendatang.</p>
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
                            <span x-text="isSubmitting ? 'Menyimpan...' : (isBulkEditing ? 'Simpan Perubahan Laporan Aktivitas' : (selectedCategory === 'lesson_learned' ? 'Simpan Laporan Lesson Learned' : 'Simpan Laporan Aktivitas'))"></span>
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

            // Edit via Official Form Laporan (Identik Persis Engineer)
            isBulkEditing: false,
            bulkFormUrl: '{{ route('sales.activities.bulk') }}',
            bulkActivityId: null,
            bulkActivityType: 'Meeting',
            meetingMedia: 'Onsite di Lokasi Klien',
            durationDays: 1,
            scopeCategory: 'Hardware & Perangkat Jaringan (Switch/Router/Firewall/Server)',
            scopeDescription: '',
            estimatedBudget: '',
            tenderTimeline: '',
            competitors: '',
            challenges: '',
            winProbability: 50,
            nextAction: '',
            nextActionPic: '{{ auth()->user()->name }}',
            nextActionDate: '',

            // Spreadsheet Multi-Row Bulk Entry
            isBulkModalOpen: false,
            selectedCategory: 'activity',
            activityTitle: '',
            isSubmitting: false,
            bulkProjectId: '{{ $filterProject ?: ($activeProjects->first()->id ?? '') }}',
            projectsMap: {{ Js::from($projectsLookup) }},
            lostFormData: {
                primary_factor: 'Faktor Harga / Komersial & Lead Time Delivery Hardware',
                competitor_winner: 'PT Mitra Solusi Datatama (Distributor Direct Tier-1)',
                competitor_price: '450000000',
                our_price: '520000000',
                turning_point: 'Pada evaluasi komersial tahap akhir, pihak kompetitor memberikan potongan bundling lisensi 3 tahun sebesar 15% serta jaminan ketersediaan stok ready 2 minggu.',
                gap_our_weakness: 'Struktur harga penawaran belum mendapatkan special project discount dari prinsipal, dan skema pembayaran mewajibkan DP 40%.',
                gap_competitor_strength: 'Kompetitor merupakan partner titanium prinsipal dengan margin harga fleksibel dan opsi pembayaran TOP 60 hari.',
                lessons_learned: 'Untuk peluang deal bernilai di atas 500 juta, tim sales wajib mengunci registrasi deal ke prinsipal lebih awal guna memperoleh harga proteksi khusus.',
                future_strategy: 'Menjaga hubungan komunikasi berkala (courtesy call setiap 2 minggu) dengan PIC klien dan memantau kepuasan implementasi kompetitor untuk persiapan renewal lisensi tahun depan.'
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
            attendees: [
                { pihak: 'Internal IPNET', nama: 'Syaiful', jabatan: 'Presales & Solution Specialist', kontak: '0812-9876-5432', keterangan: 'PIC Teknis IPNET' },
                { pihak: 'Klien', nama: 'Hasan Faqih', jabatan: 'Head of IT Infrastructure', kontak: '0811-2345-6789', keterangan: 'PIC Representatif Klien' }
            ],
            addAttendeeRow() {
                this.attendees.push({ pihak: 'Klien', nama: '', jabatan: '', kontak: '', keterangan: '' });
            },
            removeAttendeeRow(idx) {
                if (this.attendees.length > 1) {
                    if (typeof idx === 'number') {
                        this.attendees.splice(idx, 1);
                    } else {
                        this.attendees.pop();
                    }
                }
            },
            bulkRows: [
                { 
                    subject: 'Kick-off koordinasi teknis kebutuhan bandwidth & arsitektur core switch', 
                    activity_date: '{{ date('Y-m-d') }}', 
                    time_str: '09:00', 
                    client_pic: 'Hasan Faqih', 
                    ipnet_pic: 'Syaiful', 
                    notes: 'Pemaparan arsitektur high availability dan redundancy dual-uplink 10Gbps. Klien menyepakati skema topologi jaringan.' 
                },
                { 
                    subject: 'Walkthrough survey denah data center & kesiapan rack server', 
                    activity_date: '{{ date('Y-m-d') }}', 
                    time_str: '11:00', 
                    client_pic: 'Hasan Faqih', 
                    ipnet_pic: 'Syaiful', 
                    notes: 'Pengecekan ketersediaan slot 4U rack, alokasi daya PDU dual source, dan suhu ruangan server data center.' 
                },
                { 
                    subject: 'Pembahasan BoQ hardware, modul SFP+ & kabel optical fiber', 
                    activity_date: '{{ date('Y-m-d') }}', 
                    time_str: '13:30', 
                    client_pic: 'Hasan Faqih', 
                    ipnet_pic: 'Syaiful', 
                    notes: 'Verifikasi rincian kebutuhan 24 port SFP28 dan 48 port PoE+. Klien meminta rekomendasi switch enterprise tier-1.' 
                },
                { 
                    subject: 'Peninjauan Service Level Agreement (SLA) & skema support teknis', 
                    activity_date: '{{ date('Y-m-d') }}', 
                    time_str: '15:00', 
                    client_pic: 'Hasan Faqih', 
                    ipnet_pic: 'Syaiful', 
                    notes: 'Disepakati SLA uptime 99.8% dengan waktu respons onsite maksimal 4 jam untuk insiden critical severity 24x7.' 
                },
                { 
                    subject: 'Finalisasi timeline pengiriman perangkat & persiapan penawaran resmi', 
                    activity_date: '{{ date('Y-m-d') }}', 
                    time_str: '16:30', 
                    client_pic: 'Hasan Faqih', 
                    ipnet_pic: 'Syaiful', 
                    notes: 'Tim sales IPNET berkomitmen menerbitkan Surat Penawaran Harga (SPH) resmi dan garansi prinsipal maksimal 3 hari kerja.' 
                }
            ],

            get filledRowsCount() {
                return this.bulkRows.filter(r => r.subject && r.subject.trim() !== '').length;
            },

            openBulkModal() {
                this.isBulkEditing = false;
                this.bulkFormUrl = '{{ route('sales.activities.bulk') }}';
                this.bulkActivityId = null;
                this.selectedCategory = 'activity';
                this.activityTitle = '';
                this.bulkActivityType = 'Meeting';
                this.meetingMedia = 'Onsite di Lokasi Klien';
                this.durationDays = 1;
                this.scopeCategory = 'Hardware & Perangkat Jaringan (Switch/Router/Firewall/Server)';
                this.scopeDescription = '';
                this.estimatedBudget = '';
                this.tenderTimeline = '';
                this.competitors = '';
                this.challenges = '';
                this.winProbability = 50;
                this.nextAction = '';
                this.nextActionPic = '{{ auth()->user()->name }}';
                this.nextActionDate = '';
                this.attendees = [
                    { pihak: 'Internal IPNET', nama: '{{ auth()->user()->name }}', jabatan: 'Presales & Solution Specialist', kontak: '0812-9876-5432', keterangan: 'PIC Teknis IPNET' },
                    { pihak: 'Klien', nama: 'Hasan Faqih', jabatan: 'Head of IT Infrastructure', kontak: '0811-2345-6789', keterangan: 'PIC Representatif Klien' }
                ];
                this.bulkRows = [
                    { 
                        subject: 'Kick-off koordinasi teknis kebutuhan bandwidth & arsitektur core switch', 
                        activity_date: '{{ date('Y-m-d') }}', 
                        time_str: '09:00', 
                        client_pic: 'Hasan Faqih', 
                        ipnet_pic: '{{ auth()->user()->name }}', 
                        notes: 'Pemaparan arsitektur high availability dan redundancy dual-uplink 10Gbps. Klien menyepakati skema topologi jaringan.' 
                    }
                ];
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

            openEditBulkModal(act, batchData) {
                if (!act) return;
                this.isBulkEditing = true;
                this.bulkActivityId = act.id;
                this.bulkFormUrl = '/sales/activities/' + act.id;
                this.bulkProjectId = act.project_id || '';
                
                const isLesson = (act.activity_type === 'Lesson Learned' || (batchData && batchData.primary_factor));
                this.selectedCategory = isLesson ? 'lesson_learned' : 'activity';

                this.activityTitle = (batchData && batchData.title) ? batchData.title : (act.subject || '');
                this.bulkActivityType = act.activity_type || 'Meeting';
                this.meetingMedia = (batchData && batchData.meeting_media) ? batchData.meeting_media : 'Onsite di Lokasi Klien';
                this.durationDays = (batchData && batchData.duration_days) ? batchData.duration_days : 1;

                // Attendees
                if (batchData && Array.isArray(batchData.attendees) && batchData.attendees.length > 0) {
                    this.attendees = batchData.attendees.map(a => ({
                        pihak: a.pihak || 'Internal IPNET',
                        nama: a.nama || '',
                        jabatan: a.jabatan || '',
                        kontak: a.kontak || '',
                        keterangan: a.keterangan || ''
                    }));
                } else {
                    this.attendees = [
                        { pihak: 'Internal IPNET', nama: act.sales?.name || '{{ auth()->user()->name }}', jabatan: 'Account Executive', kontak: '-', keterangan: 'Sales Person In Charge' }
                    ];
                }

                // Scope
                this.scopeCategory = (batchData && batchData.scope_category) ? batchData.scope_category : 'Hardware & Perangkat Jaringan (Switch/Router/Firewall/Server)';
                this.scopeDescription = (batchData && batchData.scope_description) ? batchData.scope_description : '';
                this.estimatedBudget = (batchData && batchData.estimated_budget) ? batchData.estimated_budget : '';
                this.tenderTimeline = (batchData && batchData.tender_timeline) ? batchData.tender_timeline : '';

                // Kronologis items
                let items = [];
                if (batchData && Array.isArray(batchData.items) && batchData.items.length > 0) {
                    items = batchData.items.map(it => ({
                        subject: it.subject || '',
                        activity_date: it.date || (act.activity_date ? act.activity_date.substring(0, 10) : '{{ date('Y-m-d') }}'),
                        time_str: it.time || (act.activity_date && act.activity_date.length > 11 ? act.activity_date.substring(11, 16) : '{{ date('H:i') }}'),
                        client_pic: it.client_pic || '',
                        ipnet_pic: it.ipnet_pic || act.sales?.name || '{{ auth()->user()->name }}',
                        notes: it.notes || ''
                    }));
                } else {
                    let parsedNotes = null;
                    if (act.notes) {
                        try {
                            parsedNotes = typeof act.notes === 'string' ? JSON.parse(act.notes) : act.notes;
                        } catch(e) {}
                    }
                    if (parsedNotes && Array.isArray(parsedNotes.items) && parsedNotes.items.length > 0) {
                        items = parsedNotes.items.map(it => ({
                            subject: it.subject || '',
                            activity_date: it.date || (act.activity_date ? act.activity_date.substring(0, 10) : '{{ date('Y-m-d') }}'),
                            time_str: it.time || '{{ date('H:i') }}',
                            client_pic: it.client_pic || '',
                            ipnet_pic: it.ipnet_pic || act.sales?.name || '{{ auth()->user()->name }}',
                            notes: it.notes || ''
                        }));
                    } else {
                        items = [{
                            subject: act.subject || '',
                            activity_date: act.activity_date ? act.activity_date.substring(0, 10) : '{{ date('Y-m-d') }}',
                            time_str: act.activity_date && act.activity_date.length > 11 ? act.activity_date.substring(11, 16) : '{{ date('H:i') }}',
                            client_pic: '',
                            ipnet_pic: act.sales?.name || '{{ auth()->user()->name }}',
                            notes: typeof act.notes === 'string' && !act.notes.startsWith('{') ? act.notes : ''
                        }];
                    }
                }
                this.bulkRows = items;

                // Section E & F
                this.competitors = (batchData && batchData.competitors) ? batchData.competitors : '';
                this.challenges = (batchData && batchData.challenges) ? batchData.challenges : '';
                this.winProbability = (batchData && batchData.win_probability) ? batchData.win_probability : (act.project?.win_probability || 50);
                this.nextAction = act.next_action || (batchData && batchData.next_action) || '';
                this.nextActionPic = (batchData && batchData.next_action_pic) ? batchData.next_action_pic : (act.sales?.name || '{{ auth()->user()->name }}');
                this.nextActionDate = act.next_action_date ? act.next_action_date.substring(0, 10) : ((batchData && batchData.next_action_date) || '');

                // Lost Form Data (if Lesson Learned)
                if (batchData && batchData.primary_factor) {
                    this.lostFormData = {
                        primary_factor: batchData.primary_factor || '',
                        competitor_winner: batchData.competitor_winner || '',
                        competitor_price: batchData.competitor_price || '',
                        our_price: batchData.our_price || '',
                        turning_point: batchData.turning_point || '',
                        gap_our_weakness: batchData.gap_our_weakness || '',
                        gap_competitor_strength: batchData.gap_competitor_strength || '',
                        lessons_learned: batchData.lessons_learned || '',
                        future_strategy: batchData.future_strategy || ''
                    };
                } else if (act.project?.lost_analysis) {
                    try {
                        const la = typeof act.project.lost_analysis === 'string' ? JSON.parse(act.project.lost_analysis) : act.project.lost_analysis;
                        this.lostFormData = {
                            primary_factor: la.primary_factor || '',
                            competitor_winner: la.competitor_winner || act.project.lost_competitor || '',
                            competitor_price: la.competitor_price || '',
                            our_price: la.our_price || act.project.contract_value || '',
                            turning_point: la.turning_point || '',
                            gap_our_weakness: la.gap_our_weakness || '',
                            gap_competitor_strength: la.gap_competitor_strength || '',
                            lessons_learned: la.lessons_learned || act.project.lost_reason || '',
                            future_strategy: la.future_strategy || ''
                        };
                    } catch(e) {}
                }

                this.isBulkModalOpen = true;
            }
        }
    }
</script>
@endpush
