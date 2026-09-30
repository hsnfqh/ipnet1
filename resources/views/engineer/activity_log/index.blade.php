@extends('layouts.app')

@section('title', 'Activity Log Engineer - IP Network Solusindo')

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
        box-shadow: 0 1px 3px rgba(0,0,0,0.04), 0 4px 12px rgba(0,0,0,0.02);
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .ipnet-card:hover {
        border-color: #CBD5E1;
        box-shadow: 0 8px 24px rgba(0,0,0,0.06);
    }

    .btn-ipnet-primary {
        background: linear-gradient(135deg, #8F0A0D 0%, #B81525 100%);
        color: #FFFFFF;
        font-weight: 700;
        transition: all 0.2s ease;
        box-shadow: 0 2px 4px rgba(143,10,13,0.15);
    }

    .btn-ipnet-primary:hover {
        background: linear-gradient(135deg, #7A080B 0%, #9E0E1D 100%);
        box-shadow: 0 6px 16px rgba(143,10,13,0.28);
        transform: translateY(-1px);
    }

    @keyframes fadeUpStagger {
        0% { opacity: 0; transform: translateY(16px); }
        100% { opacity: 1; transform: translateY(0); }
    }

    .anim-fade-up { animation: fadeUpStagger 0.5s cubic-bezier(0.16, 1, 0.3, 1) both; }
    .anim-delay-1 { animation-delay: 0.06s !important; }
    .anim-delay-2 { animation-delay: 0.12s !important; }

    /* ── Activity Card ── */
    .activity-card {
        background: #FFFFFF;
        border: 1px solid #E8EDF5;
        border-left: 4px solid #8F0A0D;
        border-radius: 14px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.04), 0 2px 8px rgba(0,0,0,0.02);
        transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1);
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }

    .activity-card:hover {
        border-color: #D1D9E6;
        border-left-color: #8F0A0D;
        box-shadow: 0 6px 20px rgba(0,0,0,0.07), 0 2px 8px rgba(143,10,13,0.06);
        transform: translateY(-2px);
    }

    .activity-card-header { padding: 14px 16px 11px; border-bottom: 1px solid #F1F5F9; }
    .activity-card-body   { padding: 12px 16px 14px; flex: 1; }
    .activity-card-footer {
        padding: 8px 16px;
        background: #F8FAFC;
        border-top: 1px solid #EEF2F7;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
    }

    .agenda-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 2px 9px;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 700;
        background: linear-gradient(135deg, #FEF3C7, #FDE68A);
        color: #92400E;
        border: 1px solid #FCD34D;
    }

    .project-icon {
        width: 34px; height: 34px; min-width: 34px;
        border-radius: 9px;
        background: linear-gradient(135deg, #8F0A0D, #B81525);
        display: flex; align-items: center; justify-content: center;
    }

    /* Timeline */
    .timeline-list  { display: flex; flex-direction: column; }
    .timeline-item  {
        display: flex; align-items: flex-start; gap: 8px;
        padding: 5px 0;
        border-bottom: 1px dashed #EAEEF4;
    }
    .timeline-item:last-of-type { border-bottom: none; }
    .timeline-dot   { width: 5px; height: 5px; min-width: 5px; border-radius: 50%; background: #8F0A0D; margin-top: 5px; }
    .timeline-desc  { font-size: 11.5px; font-weight: 600; color: #334155; line-height: 1.45; flex: 1; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; }

    /* Detail button */
    .detail-btn {
        width: 100%; padding: 7px 14px; border-radius: 10px;
        border: 1.5px solid #E2E8F0; background: white; color: #8F0A0D;
        font-size: 11.5px; font-weight: 700;
        display: flex; align-items: center; justify-content: center; gap: 7px;
        cursor: pointer; transition: all 0.18s ease; margin-top: 11px;
    }
    .detail-btn:hover { background: #FFF1F1; border-color: #F5A0A2; box-shadow: 0 2px 8px rgba(143,10,13,0.1); }

    /* Empty state */
    .empty-state {
        grid-column: 1 / -1; display: flex; flex-direction: column;
        align-items: center; justify-content: center;
        padding: 60px 20px; text-align: center;
        background: white; border-radius: 16px; border: 1.5px dashed #E2E8F0;
    }
</style>
@endpush

@section('content')
<div class="flex h-screen overflow-hidden bg-[#F8FAFC] font-sans" x-data="engineerActivityManager()">
    @include('components.sidebar')

    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => $isLead ? 'Activity Log & Monitoring Engineer' : 'Catatan Aktivitas Harian Engineer'])

        <div class="p-4 sm:p-6 lg:p-7 space-y-5 max-w-[1680px] mx-auto animate-fade-in">

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

            {{-- ═══ Page Banner ═══ --}}
            <div class="bg-white border border-[#E2E8F0] rounded-2xl px-6 py-5 shadow-sm anim-fade-up">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2 mb-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#8F0A0D] inline-block"></span>
                            <span class="text-[10.5px] font-bold text-[#8F0A0D] uppercase tracking-widest">
                                {{ $isLead ? 'Monitoring Engineer' : 'Catatan Aktivitas' }}
                            </span>
                        </div>
                        <h1 class="text-[20px] font-bold text-[#0F172A] leading-snug">
                            {{ $isLead ? 'Activity Log & Monitoring Engineer' : 'Catatan Aktivitas Harian Engineer' }}
                        </h1>
                        <p class="text-[12.5px] text-[#64748B] mt-1">
                            {{ $isLead ? 'Pantau dan kelola seluruh aktivitas harian engineer secara terpusat' : 'Kelola dan catat aktivitas teknis harian Anda di sini' }}
                        </p>
                    </div>
                    <div class="flex items-center gap-3 shrink-0">
                        <div class="text-right hidden sm:block">
                            <p class="text-[10.5px] text-gray-400 font-medium">Total Aktivitas</p>
                            <p class="text-[20px] font-bold text-[#0F172A]">{{ $activities->total() }}</p>
                        </div>
                        <button type="button" @click="$dispatch('open-engineer-activity-modal')"
                                class="btn-ipnet-primary px-5 py-2.5 rounded-xl font-bold text-[12.5px] flex items-center gap-2 transition-all cursor-pointer shadow-md whitespace-nowrap">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                            </svg>
                            Input Aktivitas
                        </button>
                    </div>
                </div>
            </div>

            {{-- Filter Bar --}}
            <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center gap-3 anim-fade-up anim-delay-1">
                <form method="GET" action="{{ route('engineer.activity_log.index') }}" class="flex flex-col sm:flex-row flex-wrap gap-2.5 w-full lg:w-auto">
                    <div class="relative">
                        <svg class="w-4 h-4 absolute left-3.5 top-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kegiatan, lokasi, nama..."
                               class="w-full sm:w-64 px-3.5 py-2 pl-9 rounded-xl border border-gray-200 text-xs font-semibold text-gray-800 bg-white focus:outline-none focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D] transition-all shadow-xs">
                    </div>

                    @if($isLead && $engineers->isNotEmpty())
                    <select name="user_id" onchange="this.form.submit()"
                            class="w-full sm:w-48 px-3.5 py-2 rounded-xl border border-gray-200 text-xs font-semibold text-gray-800 bg-white focus:outline-none focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D] transition-all cursor-pointer shadow-xs truncate">
                        <option value="">Semua Engineer</option>
                        @foreach($engineers as $eng)
                            <option value="{{ $eng->id }}" {{ request('user_id') == $eng->id ? 'selected' : '' }}>{{ $eng->name }}</option>
                        @endforeach
                    </select>
                    @endif

                    <select name="project_id" onchange="this.form.submit()"
                            class="w-full sm:w-56 px-3.5 py-2 rounded-xl border border-gray-200 text-xs font-semibold text-gray-800 bg-white focus:outline-none focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D] transition-all cursor-pointer shadow-xs truncate">
                        <option value="">Semua Prospek / Proyek</option>
                        @foreach($projects as $p)
                            <option value="{{ $p->id }}" {{ request('project_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                        @endforeach
                    </select>

                    @if(request('search') || request('user_id') || request('status') || request('project_id') || request('date'))
                        <a href="{{ route('engineer.activity_log.index') }}" class="px-3 py-2 text-xs font-bold text-gray-500 hover:text-gray-800 self-center">Reset Filter</a>
                    @endif
                </form>

                <div class="flex items-center gap-2.5 w-full sm:w-auto">
                    <div x-data="{ exportOpen: false }" class="relative">
                        <button type="button" @click="exportOpen = !exportOpen"
                                class="px-4 py-2.5 rounded-xl border border-gray-200 bg-white font-bold text-xs text-gray-700 hover:bg-gray-50 flex items-center gap-2 cursor-pointer shadow-xs transition">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            <span>Export</span>
                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="exportOpen" x-cloak @click.outside="exportOpen = false"
                             class="absolute right-0 mt-2 w-52 bg-white border border-[#E2E8F0] rounded-2xl shadow-xl z-30 py-1.5 overflow-hidden anim-fade-up">
                            <a href="{{ route('engineer.activity_log.export_excel', request()->all()) }}"
                               class="flex items-center gap-2.5 px-4 py-2.5 text-[13px] font-semibold text-[#1E293B] hover:bg-[#F0FDF4] hover:text-[#16A34A] transition">
                                <svg class="w-4 h-4 text-[#16A34A] flex-shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zM6 4h7v5h5v11H6V4zm2 8h2.5l1.5 2.5 1.5-2.5H16l-2.25 3.5L16 19h-2.5L12 16.5 10.5 19H8l2.25-3.5L8 12z"/></svg>
                                Export Excel (.xlsx)
                            </a>
                            <a href="{{ route('engineer.activity_log.export_pdf', request()->all()) }}" target="_blank"
                               class="flex items-center gap-2.5 px-4 py-2.5 text-[13px] font-semibold text-[#1E293B] hover:bg-[#FEF2F2] hover:text-[#8F0A0D] transition border-t border-[#F1F5F9]">
                                <svg class="w-4 h-4 text-[#8F0A0D] flex-shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14H9v-2h2v2zm0-4H9V7h2v5zm4 4h-2v-6h2v6z"/></svg>
                                Export PDF (.pdf)
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Activity Grid — Grouped by Project --}}
            @php
                $grouped = $activities->getCollection()->groupBy(function($act) {
                    return ($act->project_id ?? 'no_project') . '_' . ($act->user_id ?? '0');
                });
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 anim-fade-up anim-delay-2">
                @forelse($grouped as $groupKey => $groupActivities)
                    @php
                        $firstAct     = $groupActivities->first();
                        $project      = $firstAct->project;
                        $engineer     = $firstAct->engineer;
                        $projectName  = $project->name ?? 'Tanpa Proyek';
                        $clientName   = $project->client ?? '-';
                        $totalInGroup = $groupActivities->count();

                        // Build clean preview items (strip [prefix] and PIC from notes)
                        $previewItems = $groupActivities->take(3)->map(function($a) {
                            $desc = $a->description ?? '-';
                            if (preg_match('/^\[(.+?)\]\s*(.*)$/s', $desc, $m)) {
                                $desc = $m[2] ?: $m[1];
                            }
                            return [
                                'desc' => $desc,
                                'date' => $a->activity_date ? $a->activity_date->format('d M Y') : '-',
                            ];
                        });

                        // Build full modal data
                        $groupJson = json_encode([
                            'project_name'  => $projectName,
                            'client_name'   => $clientName,
                            'engineer_name' => $engineer->name ?? '-',
                            'total'         => $totalInGroup,
                            'items'         => $groupActivities->map(function($a, $idx) {
                                $rawNotes  = $a->notes ?? '';
                                $clientPic = '';
                                $ipnetPic  = '';
                                $notedOnly = $rawNotes;

                                if ($rawNotes) {
                                    $parts = array_map('trim', explode('|', $rawNotes));
                                    $notedParts = [];
                                    foreach ($parts as $part) {
                                        if (str_starts_with($part, 'PIC Klien:')) {
                                            $clientPic = trim(substr($part, strlen('PIC Klien:')));
                                        } elseif (str_starts_with($part, 'PIC IPNET:')) {
                                            $ipnetPic = trim(substr($part, strlen('PIC IPNET:')));
                                        } else {
                                            $notedParts[] = $part;
                                        }
                                    }
                                    $notedOnly = implode(' | ', array_filter($notedParts));
                                }

                                if (!$ipnetPic) {
                                    $ipnetPic = $a->engineer->name ?? '-';
                                }

                                $description = $a->description ?? '-';
                                if (preg_match('/^\[(.+?)\]\s*(.*)$/s', $description, $m)) {
                                    $description = $m[2] ?: $m[1];
                                }

                                return [
                                    'no'         => $idx + 1,
                                    'id'         => $a->id,
                                    'user_id'    => $a->user_id,
                                    'date'       => $a->activity_date ? $a->activity_date->format('d M Y') : '-',
                                    'date_raw'   => $a->activity_date ? $a->activity_date->format('Y-m-d') : '',
                                    'subject'    => $description,
                                    'client_pic' => $clientPic ?: '-',
                                    'ipnet_pic'  => $ipnetPic,
                                    'notes'      => $notedOnly ?: '-',
                                    'status'     => $a->status ?? '-',
                                    'project_id' => $a->project_id,
                                ];
                            })->values()->toArray(),
                        ]);

                        // Edit data per item (first item for quick edit on card)
                        $editJson = json_encode($groupActivities->map(function($a) use ($projectName) {
                            $rawNotes  = $a->notes ?? '';
                            $clientPic = ''; $ipnetPic = ''; $notedOnly = $rawNotes;
                            if ($rawNotes) {
                                $parts = array_map('trim', explode('|', $rawNotes));
                                $notedParts = [];
                                foreach ($parts as $part) {
                                    if (str_starts_with($part, 'PIC Klien:')) { $clientPic = trim(substr($part, strlen('PIC Klien:'))); }
                                    elseif (str_starts_with($part, 'PIC IPNET:')) { $ipnetPic = trim(substr($part, strlen('PIC IPNET:'))); }
                                    else { $notedParts[] = $part; }
                                }
                                $notedOnly = implode(' | ', array_filter($notedParts));
                            }
                            $desc = $a->description ?? '';
                            $actTitle = '';
                            if (preg_match('/^\[(.+?)\]\s*(.*)$/s', $desc, $m)) {
                                $actTitle = $m[1];
                                $desc = $m[2] ?: $m[1];
                            }
                            return [
                                'id'           => $a->id,
                                'subject'      => $desc,
                                'activity_title' => $actTitle,
                                'date_raw'     => $a->activity_date ? $a->activity_date->format('Y-m-d') : date('Y-m-d'),
                                'client_pic'   => $clientPic,
                                'ipnet_pic'    => $ipnetPic,
                                'notes'        => $notedOnly,
                                'project_id'   => $a->project_id,
                                'project_name' => $projectName,
                                'update_url'   => route('engineer.activity_log.update', $a),
                            ];
                        })->values()->toArray());

                        $canEdit = (auth()->id() === $firstAct->user_id || $isLead);
                    @endphp

                    <div class="activity-card">

                        {{-- Header --}}
                        <div class="activity-card-header">
                            <div class="flex items-start gap-3">
                                <div class="project-icon">
                                    <svg width="17" height="17" fill="none" stroke="white" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between mb-1.5">
                                        <span class="agenda-badge">{{ $totalInGroup }} Agenda</span>
                                        <span class="text-[10px] font-semibold text-gray-400 tabular-nums">
                                            {{ $firstAct->activity_date ? $firstAct->activity_date->format('d M Y') : '-' }}
                                        </span>
                                    </div>
                                    <h3 class="text-[13px] font-bold text-gray-900 leading-snug truncate" title="{{ $projectName }}">
                                        {{ $projectName }}
                                    </h3>
                                    <div class="flex items-center gap-1.5 mt-1 flex-wrap">
                                        <span class="text-[11px] font-semibold text-[#8F0A0D]">{{ $engineer->name ?? 'Engineer' }}</span>
                                        @if($clientName && $clientName !== '-')
                                            <span class="text-gray-300 text-xs">|</span>
                                            <span class="text-[11px] text-gray-500 truncate max-w-[110px]" title="{{ $clientName }}">{{ $clientName }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Body: Timeline Preview --}}
                        <div class="activity-card-body">
                            <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mb-2">Rangkuman Aktivitas</p>
                            <div class="timeline-list">
                                @foreach($previewItems as $item)
                                    <div class="timeline-item">
                                        <div class="timeline-dot"></div>
                                        <span class="text-[10px] font-semibold text-gray-300 min-w-[56px] shrink-0 tabular-nums">{{ $item['date'] }}</span>
                                        <span class="timeline-desc">{{ $item['desc'] }}</span>
                                    </div>
                                @endforeach
                            </div>
                            @if($totalInGroup > 3)
                                <p class="text-[10px] text-gray-400 font-semibold italic pl-3 mt-1.5">+ {{ $totalInGroup - 3 }} aktivitas lainnya...</p>
                            @endif

                            <button type="button" @click="openDetailModal({{ $groupJson }})" class="detail-btn">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <span>Lihat Detail &amp; Semua Agenda</span>
                                <span class="ml-auto px-1.5 py-0.5 rounded-md bg-red-50 text-[#8F0A0D] text-[10px] font-bold">{{ $totalInGroup }}</span>
                            </button>
                        </div>

                        {{-- Footer: Dicatat + Edit + Hapus --}}
                        <div class="activity-card-footer">
                            <span class="text-[10.5px] text-gray-400 font-medium truncate mr-1">
                                Dicatat: <strong class="text-gray-600">{{ $engineer->name ?? 'Engineer' }}</strong>
                            </span>
                            @if($canEdit)
                                <div class="flex items-center gap-1.5 shrink-0">
                                    {{-- Tombol Edit --}}
                                    <button type="button"
                                            @click="openEditModal({{ $editJson }})"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 bg-white hover:bg-blue-50 text-blue-600 hover:text-blue-700 text-[10.5px] font-semibold rounded-lg border border-blue-100 hover:border-blue-200 transition cursor-pointer">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        Edit
                                    </button>
                                    {{-- Tombol Hapus --}}
                                    <form method="POST" action="{{ route('engineer.activity_log.destroy', $firstAct) }}"
                                          onsubmit="return confirm('Hapus seluruh aktivitas dalam grup ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1 bg-white hover:bg-red-50 text-red-500 hover:text-red-700 text-[10.5px] font-semibold rounded-lg border border-red-100 hover:border-red-200 transition cursor-pointer">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>

                @empty
                    <div class="empty-state">
                        <div class="w-14 h-14 rounded-2xl bg-gray-100 flex items-center justify-center mb-4">
                            <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <p class="text-sm font-bold text-gray-600 mb-1">Belum ada aktivitas yang tercatat</p>
                        <p class="text-xs text-gray-400">Klik tombol "Input Aktivitas" di atas untuk mencatat aktivitas teknis.</p>
                    </div>
                @endforelse
            </div>

            @if($activities->hasPages())
                <div class="p-4 ipnet-card">{{ $activities->links() }}</div>
            @endif

        </div>
    </div>

    {{-- ══ MODAL DETAIL AKTIVITAS ══ --}}
    <template x-teleport="body">
        <div x-show="isDetailModalOpen" x-cloak
             class="fixed inset-0 z-50 bg-[#0F172A]/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-6"
             @click.self="isDetailModalOpen = false">
            <div class="bg-white rounded-2xl max-w-5xl w-full shadow-2xl border border-[#E2E8F0] max-h-[92vh] flex flex-col overflow-hidden anim-fade-up">
                <div class="flex items-center justify-between border-b border-[#E2E8F0] p-5 sm:p-6 pb-4 shrink-0 bg-white">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-[#8F0A0D] text-[11px] font-bold uppercase tracking-wider">DETAIL AKTIVITAS ENGINEER</span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200"
                                  x-text="(selectedDetail?.total || 0) + ' Agenda'"></span>
                        </div>
                        <h3 class="text-[17px] font-bold text-[#1E293B]" x-text="selectedDetail?.project_name || 'Detail Aktivitas'"></h3>
                    </div>
                    <button type="button" @click="isDetailModalOpen = false" class="text-[#94A3B8] hover:text-[#1E293B] p-1.5 rounded-lg hover:bg-[#F1F5F9] transition cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="px-5 sm:px-6 py-3 bg-[#F8FAFC] border-b border-[#E2E8F0] flex flex-wrap items-center justify-between gap-3 text-xs shrink-0">
                    <div class="flex items-center gap-3 flex-wrap">
                        <div>
                            <span class="text-gray-400 font-medium">Proyek:</span>
                            <strong class="text-gray-800 ml-1" x-text="selectedDetail?.project_name || '-'"></strong>
                        </div>
                        <span class="text-gray-300">•</span>
                        <div class="text-gray-500 text-[11px]">
                            Dicatat oleh: <strong class="text-gray-800" x-text="selectedDetail?.engineer_name || '-'"></strong>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <a :href="buildExportUrl('pdf')" target="_blank"
                           class="px-4 py-1.5 text-xs font-bold text-[#8F0A0D] bg-white hover:bg-red-50 border border-red-200 hover:border-red-300 rounded-full transition cursor-pointer shadow-xs inline-flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            Download PDF
                        </a>
                        <a :href="buildExportUrl('excel')"
                           class="px-4 py-1.5 text-xs font-bold text-[#0F6B43] bg-white hover:bg-emerald-50 border border-emerald-200 hover:border-emerald-300 rounded-full transition cursor-pointer shadow-xs inline-flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Download Excel
                        </a>
                    </div>
                </div>

                <div class="flex-1 overflow-y-auto overflow-x-auto p-5 sm:p-6 bg-white">
                    <table class="w-full border-collapse rounded-xl border border-[#E2E8F0] text-left text-xs min-w-[860px]">
                        <thead class="bg-[#F8FAFC] text-[#475569] font-bold uppercase text-[10.5px] tracking-wider border-b border-[#E2E8F0]">
                            <tr>
                                <th class="py-3 px-3 w-10 text-center">No</th>
                                <th class="py-3 px-3 min-w-[200px]">Aktivitas</th>
                                <th class="py-3 px-3 w-28">Tanggal</th>
                                <th class="py-3 px-3 w-32">PIC Klien</th>
                                <th class="py-3 px-3 w-32">PIC IPNET</th>
                                <th class="py-3 px-3 min-w-[160px]">Noted</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#E2E8F0]">
                            <template x-for="(item, idx) in (selectedDetail?.items || [])" :key="idx">
                                <tr class="hover:bg-[#F8FAFC]/80 transition-colors">
                                    <td class="py-3 px-3 text-center font-bold text-[#8F0A0D]" x-text="item.no || (idx + 1)"></td>
                                    <td class="py-3 px-3"><p class="font-semibold text-[#1E293B] leading-relaxed" x-text="item.subject || '-'"></p></td>
                                    <td class="py-3 px-3 whitespace-nowrap"><div class="font-bold text-[#1E293B]" x-text="item.date || '-'"></div></td>
                                    <td class="py-3 px-3"><span class="inline-block px-2.5 py-1 bg-[#F1F5F9] text-[#334155] rounded-md font-semibold text-[11px]" x-text="item.client_pic || '-'"></span></td>
                                    <td class="py-3 px-3"><span class="inline-block px-2.5 py-1 bg-red-50 text-[#8F0A0D] border border-red-100 rounded-md font-semibold text-[11px]" x-text="item.ipnet_pic || '-'"></span></td>
                                    <td class="py-3 px-3"><p class="text-gray-700 whitespace-pre-line leading-relaxed font-medium" x-text="item.notes || '-'"></p></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <div class="flex items-center justify-between p-4 px-6 border-t border-[#E2E8F0] bg-[#F8FAFC] shrink-0">
                    <p class="text-xs text-gray-500 font-medium">
                        Total: <strong class="text-gray-800" x-text="(selectedDetail?.items?.length || 0) + ' Agenda'"></strong>
                    </p>
                    <button type="button" @click="isDetailModalOpen = false"
                            class="px-5 py-2 text-xs font-bold text-[#1E293B] bg-white hover:bg-gray-100 border border-[#CBD5E1] rounded-xl transition cursor-pointer shadow-xs">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </template>

    {{-- == MODAL EDIT AKTIVITAS == --}}
    <template x-teleport="body">
        <div x-show="isEditModalOpen" x-cloak
             class="fixed inset-0 z-[60] bg-[#0F172A]/65 backdrop-blur-sm flex items-center justify-center p-3 sm:p-6"
             @click.self="isEditModalOpen = false"
             @keydown.escape.window="isEditModalOpen = false">
            <div class="bg-white rounded-2xl max-w-2xl w-full shadow-2xl border border-[#E2E8F0] max-h-[92vh] flex flex-col overflow-hidden">

                <div class="flex items-center justify-between px-6 py-4 border-b border-[#E2E8F0] shrink-0">
                    <div class="flex items-center gap-3">
                        <div style="width:38px;height:38px;border-radius:11px;background:linear-gradient(135deg,#8F0A0D,#C41E2A);display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 3px 8px rgba(143,10,13,.22);">
                            <svg width="16" height="16" fill="none" stroke="white" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-[#8F0A0D] uppercase tracking-widest mb-0.5">Edit Aktivitas</p>
                            <h3 class="text-[14.5px] font-bold text-[#1E293B] leading-tight truncate max-w-xs" x-text="editGroupName"></h3>
                        </div>
                    </div>
                    <button type="button" @click="isEditModalOpen = false"
                            class="w-8 h-8 flex items-center justify-center rounded-lg text-[#94A3B8] hover:text-[#1E293B] hover:bg-[#F1F5F9] transition cursor-pointer shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="px-6 py-2 border-b border-[#F0F4F8] bg-[#F8FAFC] flex items-center justify-between shrink-0">
                    <p class="text-[11px] text-gray-500">
                        Terdapat <span class="font-bold text-[#1E293B]" x-text="editRows.length"></span> agenda
                    </p>
                    <button type="button" @click="addEditRow()"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-[#8F0A0D]/25 bg-white hover:bg-red-50 text-[#8F0A0D] text-[11px] font-bold transition cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        Tambah Agenda
                    </button>
                </div>

                <div class="flex-1 overflow-y-auto px-5 py-4 space-y-3 bg-[#EEF2F7]">
                    <template x-for="(row, idx) in editRows" :key="idx">
                        <div class="bg-white rounded-xl border border-[#E2E8F0] shadow-sm overflow-hidden">
                            <div class="flex items-center justify-between px-4 py-2.5"
                                 :class="row.id ? 'border-b border-[#F1F5F9] bg-white' : 'border-b border-emerald-100 bg-emerald-50/60'">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-6 h-6 rounded-full flex items-center justify-center text-[10.5px] font-bold shrink-0"
                                         :style="row.id ? 'background:#FEF3C7;border:1.5px solid #FCD34D;color:#92400E;' : 'background:#D1FAE5;border:1.5px solid #6EE7B7;color:#065F46;'"
                                         x-text="idx + 1"></div>
                                    <span class="text-[12.5px] font-bold"
                                          :class="row.id ? 'text-[#1E293B]' : 'text-emerald-800'"
                                          x-text="row.id ? 'Agenda #' + (idx + 1) : 'Agenda Baru'"></span>
                                    <span x-show="!row.id"
                                          class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-700 border border-emerald-200 uppercase tracking-wide">baru</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <input type="date" x-model="row.date_raw"
                                           class="px-2.5 py-1 border border-[#D1D5DB] rounded-lg text-[11px] text-[#374151] bg-[#F9FAFB] focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition cursor-pointer">
                                    <button x-show="!row.id" type="button" @click="editRows.splice(idx, 1)"
                                            class="w-6 h-6 flex items-center justify-center rounded-md text-gray-400 hover:text-red-500 hover:bg-red-50 transition cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                            </div>
                            <div class="px-4 pt-3.5 pb-4 space-y-3">
                                <div>
                                    <label class="block text-[10px] font-bold text-[#64748B] uppercase tracking-widest mb-1.5">Aktivitas <span class="text-red-500">*</span></label>
                                    <textarea x-model="row.subject" rows="2"
                                              class="w-full px-3 py-2.5 bg-[#F8FAFC] border border-[#E2E8F0] rounded-lg text-[12.5px] text-[#1E293B] focus:outline-none focus:bg-white focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/15 transition resize-none placeholder-gray-300"
                                              placeholder="Rincian aktivitas teknis..."></textarea>
                                </div>
                                <div class="grid grid-cols-3 gap-3">
                                    <div>
                                        <label class="block text-[10px] font-bold text-[#64748B] uppercase tracking-widest mb-1.5">PIC Klien</label>
                                        <input type="text" x-model="row.client_pic" placeholder="Nama PIC klien"
                                               class="w-full px-3 py-2.5 bg-[#F8FAFC] border border-[#E2E8F0] rounded-lg text-[12.5px] text-[#1E293B] focus:outline-none focus:bg-white focus:border-[#8F0A0D] transition placeholder-gray-300">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-[#64748B] uppercase tracking-widest mb-1.5">PIC IPNET</label>
                                        <input type="text" x-model="row.ipnet_pic" placeholder="Nama engineer"
                                               class="w-full px-3 py-2.5 bg-[#F8FAFC] border border-[#E2E8F0] rounded-lg text-[12.5px] text-[#1E293B] focus:outline-none focus:bg-white focus:border-[#8F0A0D] transition placeholder-gray-300">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-[#64748B] uppercase tracking-widest mb-1.5">Noted</label>
                                        <input type="text" x-model="row.notes" placeholder="Keterangan"
                                               class="w-full px-3 py-2.5 bg-[#F8FAFC] border border-[#E2E8F0] rounded-lg text-[12.5px] text-[#1E293B] focus:outline-none focus:bg-white focus:border-[#8F0A0D] transition placeholder-gray-300">
                                    </div>
                                </div>
                                <div class="flex justify-end pt-0.5">
                                    <button type="button" @click="saveEditRow(row)" :disabled="editSaving"
                                            class="inline-flex items-center gap-2 px-4 py-2 text-[12px] font-bold text-white rounded-lg transition cursor-pointer disabled:opacity-50 shadow-sm"
                                            style="background:linear-gradient(135deg,#8F0A0D,#C41E2A);">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                        <span x-text="editSaving ? 'Menyimpan...' : (row.id ? 'Simpan Perubahan' : 'Simpan Agenda Baru')"></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>

                    <div x-show="editRows.length === 0"
                         class="bg-white rounded-xl border border-dashed border-[#CBD5E1] py-10 text-center">
                        <p class="text-sm font-semibold text-gray-400">Belum ada agenda.</p>
                        <p class="text-xs text-gray-300 mt-0.5">Klik "Tambah Agenda" di atas.</p>
                    </div>
                </div>

                <div class="px-6 py-3 border-t border-[#E2E8F0] bg-white flex items-center justify-between shrink-0">
                    <p class="text-[11px] text-gray-400">Simpan per agenda, tutup setelah selesai.</p>
                    <button type="button" @click="isEditModalOpen = false"
                            class="px-5 py-2 text-[12px] font-bold text-[#1E293B] bg-[#F1F5F9] hover:bg-[#E2E8F0] border border-[#CBD5E1] rounded-xl transition cursor-pointer">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </template>

    {{-- MODAL INPUT AKTIVITAS --}}
    @include('components.engineer-activity-bulk-modal')
</div>

@push('scripts')
<script>
function engineerActivityManager() {
    return {
        // Detail modal
        isDetailModalOpen: false,
        selectedDetail: null,

        // Edit modal
        isEditModalOpen: false,
        editRows: [],
        editGroupName: '',
        editProjectId: '',
        editStoreUrl: '',
        editActTitle: '',
        editSaving: false,

        openDetailModal(groupData) {
            this.selectedDetail = groupData;
            this.isDetailModalOpen = true;
        },

        openEditModal(rows) {
            this.editRows = rows.map(r => ({ ...r }));
            this.editGroupName = rows[0]?.project_name || 'Edit Aktivitas';
            this.editProjectId = rows[0]?.project_id || '';
            this.editStoreUrl  = '{{ route("engineer.activity_log.store") }}';
            this.editActTitle  = rows[0]?.activity_title || '';
            this.isEditModalOpen = true;
        },

        addEditRow() {
            const today = new Date().toISOString().slice(0, 10);
            this.editRows.push({
                id:           null,
                subject:      '',
                activity_title: this.editActTitle,
                date_raw:     today,
                client_pic:   '',
                ipnet_pic:    '',
                notes:        '',
                project_id:   this.editProjectId,
                project_name: this.editGroupName,
                update_url:   null,
            });
            // Scroll to bottom after adding
            this.$nextTick(() => {
                const container = this.$el.querySelector('.overflow-y-auto');
                if (container) container.scrollTop = container.scrollHeight;
            });
        },

        async saveEditRow(row) {
            if (!row.subject || !row.subject.trim()) {
                alert('Aktivitas tidak boleh kosong.');
                return;
            }
            this.editSaving = true;
            const token = document.querySelector('meta[name="csrf-token"]').content;
            const formData = new FormData();
            formData.append('_token', token);

            try {
                if (row.id) {
                    // UPDATE existing row
                    formData.append('_method', 'PUT');
                    formData.append('subject', row.subject);
                    formData.append('activity_date', row.date_raw);
                    formData.append('client_pic', row.client_pic || '');
                    formData.append('ipnet_pic', row.ipnet_pic || '');
                    formData.append('notes', row.notes || '');
                    formData.append('project_id', row.project_id || '');
                    formData.append('activity_title', row.activity_title || '');
                    const res = await fetch(row.update_url, { method: 'POST', body: formData });
                    if (res.ok || res.redirected) { window.location.reload(); }
                    else { alert('Gagal menyimpan. Coba lagi.'); }
                } else {
                    // CREATE new row via bulk store
                    formData.append('project_id', row.project_id || '');
                    formData.append('activity_title', row.activity_title || '');
                    formData.append('activities[0][subject]', row.subject);
                    formData.append('activities[0][activity_date]', row.date_raw);
                    formData.append('activities[0][time_str]', '');
                    formData.append('activities[0][client_pic]', row.client_pic || '');
                    formData.append('activities[0][ipnet_pic]', row.ipnet_pic || '');
                    formData.append('activities[0][notes]', row.notes || '');
                    const res = await fetch(this.editStoreUrl, { method: 'POST', body: formData });
                    if (res.ok || res.redirected) { window.location.reload(); }
                    else { alert('Gagal menyimpan agenda baru. Coba lagi.'); }
                }
            } catch (e) {
                alert('Terjadi kesalahan: ' + e.message);
            } finally {
                this.editSaving = false;
            }
        },

        buildExportUrl(type) {
            const base = type === 'pdf'
                ? '{{ route("engineer.activity_log.export_pdf") }}'
                : '{{ route("engineer.activity_log.export_excel") }}';
            const params = new URLSearchParams(window.location.search);
            if (this.selectedDetail && this.selectedDetail.items && this.selectedDetail.items.length > 0) {
                const ids = this.selectedDetail.items.map(item => item.id).filter(Boolean);
                if (ids.length > 0) params.set('log_ids', ids.join(','));
                if (this.selectedDetail.project_name) params.set('project_name', this.selectedDetail.project_name);
            }
            const qs = params.toString();
            return qs ? (base + '?' + qs) : base;
        },
    };
}
</script>
@endpush

@endsection
