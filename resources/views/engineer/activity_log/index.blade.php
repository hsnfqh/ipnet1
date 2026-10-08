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
        @include('components.topbar', ['title' => 'Activity Log & Monitoring Engineer'])

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
            @if($errors->any())
                <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold shadow-xs">
                    <div class="flex items-center gap-2 mb-1.5 font-bold text-rose-900">
                        <svg class="w-4 h-4 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span>Gagal Menyimpan Data Formulir:</span>
                    </div>
                    <ul class="list-disc list-inside space-y-0.5 pl-6 text-rose-700 font-medium">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- ═══ Page Banner ═══ --}}
            <div class="bg-white border border-[#E2E8F0] rounded-2xl px-6 py-5 shadow-sm anim-fade-up">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2 mb-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#8F0A0D] inline-block"></span>
                            <span class="text-[10.5px] font-bold text-[#8F0A0D] uppercase tracking-widest">
                                MONITORING &amp; LOG AKTIVITAS
                            </span>
                        </div>
                        <h1 class="text-[20px] font-bold text-[#0F172A] leading-snug">
                            Activity Log &amp; Monitoring Engineer
                        </h1>
                        <p class="text-[12.5px] text-[#64748B] mt-1">
                            {{ $isLead ? 'Pantau dan kelola seluruh aktivitas harian engineer secara terpusat' : 'Kelola dan pantau aktivitas penugasan proyek Anda dan tim secara terhubung' }}
                        </p>
                    </div>
                    <div class="shrink-0 flex items-center gap-2.5">
                        <a href="{{ route('public.verify.index') }}" target="_blank"
                           class="px-4 py-2.5 rounded-xl font-bold text-[12.5px] flex items-center gap-2 transition-all cursor-pointer bg-white border border-[#CBD5E1] text-[#334155] hover:bg-[#F8FAFC] hover:border-[#94A3B8] shadow-sm whitespace-nowrap">
                            <svg class="w-4 h-4 text-[#8F0A0D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                            Uji Integritas Dokumen
                        </a>
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
            </div>

            {{-- Activity Grid — Grouped by Project (Shared Collaboration) --}}
            @php
                $grouped = $activities->getCollection()->groupBy(function($act) {
                    return $act->project_id ? ('proj_' . $act->project_id) : ('no_proj_' . $act->user_id);
                });

                // Preload status tanda tangan untuk seluruh grup di halaman ini agar instan
                $preloadedSigList = collect();
                try {
                    \App\Models\ActivityDocumentSignature::ensureSchemaReady();
                    $allScopeKeys = $grouped->keys()->toArray();
                    $allProjectIds = $grouped->map(fn($g) => $g->first()->project_id)->filter()->values()->toArray();
                    $preloadedSigList = \App\Models\ActivityDocumentSignature::whereIn('scope_key', $allScopeKeys)
                        ->orWhereIn('project_id', $allProjectIds)
                        ->get();
                } catch (\Throwable $e) {
                    // Abaikan jika belum siap
                }
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 anim-fade-up anim-delay-2">
                @forelse($grouped as $groupKey => $groupActivities)
                    @php
                        $firstAct     = $groupActivities->first();
                        $project      = $firstAct->project;
                        $projectName  = $project->name ?? 'Tanpa Proyek';
                        $clientName   = $project->client ?? '-';
                        $totalInGroup = $groupActivities->count();

                        // Helper untuk inisial 2 huruf kapital
                        $getInitials = function($name) {
                            $trimmed = trim((string) $name);
                            if (!$trimmed) return 'EN';
                            $words = preg_split('/\s+/', $trimmed);
                            if (count($words) >= 2) {
                                return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
                            }
                            return strtoupper(substr($trimmed, 0, 2));
                        };

                        // Kumpulkan seluruh engineer yang berkontribusi secara riil pada grup kegiatan ini
                        $collectedEngineers = collect();

                        foreach ($groupActivities as $act) {
                            if ($act->engineer && !empty($act->engineer->name)) {
                                $cName = trim($act->engineer->name);
                                if (!in_array(strtolower($cName), ['test', 'null', 'none'])) {
                                    $collectedEngineers->push([
                                        'id'       => $act->engineer->id,
                                        'name'     => $cName,
                                        'initials' => $getInitials($cName),
                                    ]);
                                }
                            }
                        }

                        // Fallback jika belum ada relasi engineer tercatat
                        if ($collectedEngineers->isEmpty()) {
                            $fallbackName = auth()->user()->name ?? 'Engineer';
                            $collectedEngineers->push([
                                'id'       => auth()->id(),
                                'name'     => $fallbackName,
                                'initials' => $getInitials($fallbackName),
                            ]);
                        }

                        // Deduplikasi berdasarkan nama atau user_id
                        $uniqueEngineers = $collectedEngineers->unique(function ($item) {
                            return strtolower($item['name']);
                        })->values();

                        // Format tampilan nama engineer (jika 1, 2, atau 3 orang tampilkan langsung namanya tanpa badge)
                        $engineerCount = $uniqueEngineers->count();
                        if ($engineerCount === 1) {
                            $engineersFormattedLabel = $uniqueEngineers[0]['name'];
                            $additionalTeamCount = 0;
                        } elseif ($engineerCount === 2) {
                            $engineersFormattedLabel = $uniqueEngineers[0]['name'] . ' dan ' . $uniqueEngineers[1]['name'];
                            $additionalTeamCount = 0;
                        } elseif ($engineerCount === 3) {
                            $engineersFormattedLabel = $uniqueEngineers[0]['name'] . ', ' . $uniqueEngineers[1]['name'] . ', dan ' . $uniqueEngineers[2]['name'];
                            $additionalTeamCount = 0;
                        } else {
                            $engineersFormattedLabel = $uniqueEngineers[0]['name'] . ' dan ' . $uniqueEngineers[1]['name'];
                            $additionalTeamCount = $engineerCount - 2;
                        }

                        $primaryEngineer = $uniqueEngineers->first();
                        $displayAvatars = $uniqueEngineers->take(2);
                        $allEngineerNames = $uniqueEngineers->pluck('name')->implode(', ');

                        // "Dibuat oleh" menampilkan seluruh pembuat riil langsung
                        $creatorDisplayName = $engineersFormattedLabel;
                        $creatorName = $creatorDisplayName;

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

                        // Match signature status for this group
                        $matchedSig = $preloadedSigList->first(function($s) use ($groupKey, $firstAct) {
                            return $s->scope_key === $groupKey || ($firstAct->project_id && (int)$s->project_id === (int)$firstAct->project_id);
                        });

                        $sigData = $matchedSig ? [
                            'exists'          => true,
                            'document_number' => $matchedSig->document_number,
                            'scope_key'       => $matchedSig->scope_key ?? $groupKey,
                            'status'          => $matchedSig->status,
                            'verification_hash' => $matchedSig->verification_hash,
                            'pic' => [
                                'signed'    => !empty($matchedSig->pic_signature),
                                'name'      => $matchedSig->pic_name,
                                'title'     => $matchedSig->pic_title ?? 'PIC Field Engineer',
                                'signed_at' => $matchedSig->pic_signed_at ? $matchedSig->pic_signed_at->format('d M Y, H:i') . ' WIB' : null,
                            ],
                            'lead' => [
                                'signed'    => !empty($matchedSig->lead_signature),
                                'name'      => $matchedSig->lead_name,
                                'title'     => $matchedSig->lead_title ?? 'Lead Network Engineer',
                                'signed_at' => $matchedSig->lead_signed_at ? $matchedSig->lead_signed_at->format('d M Y, H:i') . ' WIB' : null,
                            ],
                            'head' => [
                                'signed'    => !empty($matchedSig->head_signature),
                                'name'      => $matchedSig->head_name,
                                'title'     => $matchedSig->head_title ?? 'Head of Division',
                                'signed_at' => $matchedSig->head_signed_at ? $matchedSig->head_signed_at->format('d M Y, H:i') . ' WIB' : null,
                            ],
                            'report_data'     => $matchedSig->report_data,
                        ] : null;

                        // Build full modal data
                        $groupJson = json_encode([
                            'scope_key'     => $groupKey,
                            'sig_info'      => $sigData,
                            'project_id'    => $firstAct->project_id,
                            'project_name'  => $projectName,
                            'client_name'   => $clientName,
                            'engineer_name' => $allEngineerNames ?: ($firstAct->engineer->name ?? '-'),
                            'creator_name'  => $creatorName,
                            'total'         => $totalInGroup,
                            'items'         => $groupActivities->map(function($a, $idx) use ($creatorName) {
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

                                $description = $a->description ?? '-';
                                if (preg_match('/^\[(.+?)\]\s*(.*)$/s', $description, $m)) {
                                    $description = $m[2] ?: $m[1];
                                }

                                return [
                                    'no'           => $idx + 1,
                                    'id'           => $a->id,
                                    'user_id'      => $a->user_id,
                                    'creator_name' => $a->engineer->name ?? $creatorName,
                                    'date'         => $a->activity_date ? $a->activity_date->format('d M Y') : '-',
                                    'date_raw'     => $a->activity_date ? $a->activity_date->format('Y-m-d') : '',
                                    'subject'      => $description,
                                    'client_pic'   => $clientPic ?: '-',
                                    'ipnet_pic'    => $ipnetPic ?: '-',
                                    'notes'        => $notedOnly ?: '-',
                                    'status'       => $a->status ?? '-',
                                    'project_id'   => $a->project_id,
                                ];
                            })->values()->toArray(),
                        ]);

                        // Edit data per item
                        $editJson = json_encode($groupActivities->map(function($a) use ($projectName, $creatorName) {
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
                                'id'             => $a->id,
                                'subject'        => $desc,
                                'activity_title' => $actTitle,
                                'date_raw'       => $a->activity_date ? $a->activity_date->format('Y-m-d') : date('Y-m-d'),
                                'client_pic'     => $clientPic,
                                'ipnet_pic'      => $ipnetPic,
                                'notes'          => $notedOnly,
                                'creator_name'   => $a->engineer->name ?? $creatorName,
                                'project_id'     => $a->project_id,
                                'project_name'   => $projectName,
                                'update_url'     => route('engineer.activity_log.update', $a),
                            ];
                        })->values()->toArray());

                        // Hak edit dan hapus adalah hak eksklusif pembuat/kontributor riil kegiatan ini.
                        // Leader/Managerial hanya monitoring (lihat & detail), kecuali mereka ikut mencatat aktivitas di grup ini.
                        $isContributor = $groupActivities->contains('user_id', auth()->id());
                        $canEdit       = $isContributor;
                        $canDelete     = $isContributor;
                    @endphp

                    <div class="activity-card group">

                        {{-- Header Card --}}
                        <div class="activity-card-header">
                            <div class="flex items-center justify-between gap-2 mb-2">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span class="agenda-badge">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#8F0A0D]"></span>
                                        {{ $totalInGroup }} Agenda
                                    </span>
                                </div>
                                <span class="text-[11px] font-semibold text-gray-400 tabular-nums">
                                    {{ $firstAct->activity_date ? $firstAct->activity_date->format('d M Y') : '-' }}
                                </span>
                            </div>

                            <h3 class="text-[13.5px] font-bold text-gray-900 group-hover:text-[#8F0A0D] transition-colors leading-snug line-clamp-1" title="{{ $projectName }}">
                                {{ $projectName }}
                            </h3>

                            <div class="flex items-center flex-wrap gap-1.5 mt-2">
                                {{-- Stacked Circular Avatars --}}
                                <div class="flex items-center -space-x-1.5 shrink-0" title="{{ $allEngineerNames }}">
                                    @foreach($displayAvatars as $idx => $eng)
                                        @php
                                            $bgColor = $idx === 0 ? 'bg-[#8F0A0D]' : ($idx === 1 ? 'bg-[#2563EB]' : 'bg-[#0D9488]');
                                        @endphp
                                        <div class="w-5 h-5 rounded-full {{ $bgColor }} text-white text-[9.5px] font-black flex items-center justify-center ring-1.5 ring-white uppercase shrink-0 shadow-xs"
                                             title="{{ $eng['name'] }}">
                                            {{ $eng['initials'] }}
                                        </div>
                                    @endforeach
                                </div>

                                {{-- Engineer Names --}}
                                <span class="text-[12px] font-bold text-[#1E293B] truncate max-w-[280px]" title="{{ $allEngineerNames }}">
                                    {{ $engineersFormattedLabel }}
                                </span>

                                {{-- Badge tambahan (+X orang) hanya jika lebih dari 3 personel --}}
                                @if($additionalTeamCount > 0)
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[9.5px] font-extrabold bg-[#EFF6FF] text-[#2563EB] border border-[#BFDBFE] shrink-0"
                                          title="{{ $allEngineerNames }}">
                                        +{{ $additionalTeamCount }} orang
                                    </span>
                                @endif

                                {{-- Separator & Client Name --}}
                                @if($clientName && $clientName !== '-')
                                    <span class="text-gray-300 shrink-0">•</span>
                                    <span class="text-[11px] text-gray-500 font-medium truncate max-w-[120px]" title="{{ $clientName }}">{{ $clientName }}</span>
                                @endif
                            </div>
                        </div>

                        {{-- Body: Mini Rangkuman Aktivitas --}}
                        <div class="activity-card-body">
                            <div class="bg-[#F8FAFC] border border-[#EEF2F6] rounded-xl p-2.5 space-y-1.5">
                                @foreach($previewItems as $item)
                                    <div class="flex items-center gap-2 text-[11px]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#8F0A0D]/70 shrink-0"></span>
                                        <span class="text-gray-400 font-medium text-[10px] shrink-0 tabular-nums">{{ $item['date'] }}</span>
                                        <span class="text-gray-700 font-medium truncate flex-1">{{ $item['desc'] }}</span>
                                    </div>
                                @endforeach
                                @if($totalInGroup > 3)
                                    <div class="text-[10.5px] text-[#8F0A0D] font-bold pl-3.5 pt-0.5">
                                        + {{ $totalInGroup - 3 }} agenda lainnya...
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Footer: Detail Button (Full Width & Centered jika Monitoring) + Edit/Hapus Action --}}
                        <div class="activity-card-footer">
                            @if(!$canEdit)
                                <button type="button" @click="openDetailModal({{ $groupJson }})"
                                        class="w-full flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl text-[12px] font-bold text-[#8F0A0D] bg-red-50/70 hover:bg-red-100/90 border border-red-200/80 hover:border-red-300 transition-all duration-150 cursor-pointer shadow-2xs hover:shadow-xs group/btn">
                                    <svg class="w-4 h-4 text-[#8F0A0D] transition-transform duration-150 group-hover/btn:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <span>Form Laporan Resmi &amp; Detail</span>
                                    <span class="ml-1 px-2 py-0.5 rounded-full bg-white text-[#8F0A0D] text-[10px] font-extrabold border border-red-200 shadow-2xs">{{ $totalInGroup }} Agenda</span>
                                </button>
                            @else
                                <button type="button" @click="openDetailModal({{ $groupJson }})"
                                        class="flex-1 flex items-center justify-center gap-1.5 py-1.5 px-3 rounded-lg text-[11px] font-bold text-[#8F0A0D] bg-red-50/70 hover:bg-red-100/80 border border-red-200/80 hover:border-red-300 transition-all cursor-pointer">
                                    <svg class="w-3.5 h-3.5 text-[#8F0A0D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <span>Form Laporan</span>
                                    <span class="px-1.5 py-0.2 rounded-full bg-white text-[#8F0A0D] text-[9.5px] font-black border border-red-200/60">{{ $totalInGroup }}</span>
                                </button>

                                <div class="flex items-center gap-1.5 shrink-0">
                                    <button type="button"
                                            @click="openEditModal({{ $editJson }})"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-white hover:bg-blue-50 text-blue-600 text-[11px] font-bold rounded-lg border border-blue-200 transition cursor-pointer">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        Edit
                                    </button>
                                    @if($canDelete)
                                    <button type="button"
                                            @click="promptDeleteGroup('{{ route('engineer.activity_log.destroy', $firstAct) }}', '{{ addslashes($projectName) }}')"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-white hover:bg-red-50 text-red-600 hover:text-red-700 text-[11px] font-bold rounded-lg border border-red-200 transition cursor-pointer"
                                            title="Hapus Seluruh Aktivitas Proyek Ini">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        <span>Hapus</span>
                                    </button>
                                    @endif
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
            <div class="bg-white rounded-2xl max-w-7xl w-full shadow-2xl border border-[#E2E8F0] max-h-[94vh] flex flex-col overflow-hidden anim-fade-up">
                {{-- Header Modal: Judul Proyek, Info Kreator/Dokumen, Tombol Download PDF & Excel, Tombol Close --}}
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-[#E2E8F0] p-5 sm:px-6 py-4 shrink-0 bg-white">
                    <div>
                        <div class="flex items-center gap-2 mb-1 flex-wrap">
                            <span class="text-[#8F0A0D] text-[11px] font-bold uppercase tracking-wider"
                                  x-text="(reportFormData?.category === 'managed_service' ? 'FORM LAPORAN AKTIVITAS ENGINEER – MANAGED SERVICE' : (reportFormData?.category === 'help_desk' ? 'FORM LAPORAN AKTIVITAS ENGINEER – HELP DESK' : 'FORM LAPORAN AKTIVITAS ENGINEER – PROJECT'))"></span>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-red-50 text-[#8F0A0D] border border-red-200"
                                  x-text="(selectedDetail?.total || 0) + ' Agenda'"></span>
                            <template x-if="sigInfo?.document_number">
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                    <svg class="w-3 h-3 text-[#8F0A0D]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                    <span x-text="sigInfo.document_number"></span>
                                </span>
                            </template>
                        </div>
                        <h3 class="text-[17px] font-bold text-[#1E293B]" x-text="selectedDetail?.project_name || 'Detail Aktivitas'"></h3>
                        <div class="text-gray-500 text-[11px] mt-0.5 flex items-center gap-1.5">
                            <span>Dibuat oleh:</span>
                            <strong class="text-gray-800" x-text="selectedDetail?.creator_name || selectedDetail?.engineer_name || '-'"></strong>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 self-end sm:self-center shrink-0">
                        <button type="button" @click="downloadReportPdf('download')"
                           class="px-3.5 py-1.5 text-xs font-bold text-[#8F0A0D] bg-white hover:bg-red-50 border border-red-200 hover:border-red-300 rounded-lg transition cursor-pointer shadow-xs inline-flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-[#8F0A0D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            <span>Download PDF</span>
                        </button>
                        <a :href="buildExportUrl('excel')"
                           class="px-3.5 py-1.5 text-xs font-bold text-[#0F6B43] bg-white hover:bg-emerald-50 border border-emerald-200 hover:border-emerald-300 rounded-lg transition cursor-pointer shadow-xs inline-flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-[#0F6B43]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span>Download Excel</span>
                        </a>
                        <div class="h-6 w-px bg-slate-200 mx-1 hidden sm:block"></div>
                        <button type="button" @click="isDetailModalOpen = false" class="text-[#94A3B8] hover:text-[#1E293B] p-1.5 rounded-lg hover:bg-[#F1F5F9] transition cursor-pointer" title="Tutup">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </div>

                {{-- Form Laporan Resmi (Sesuai PDF) --}}
                <div class="flex-1 overflow-y-auto p-4 sm:p-6 bg-[#F8FAFC]">
                    @include('components.engineer-activity-report-form')
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
                                         :style="row.id ? 'background:#FEF2F2;border:1.5px solid #FECACA;color:#8F0A0D;' : 'background:#ECFDF5;border:1.5px solid #A7F3D0;color:#065F46;'"
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
                                    <button type="button" @click="promptDeleteRow(row, idx)" :disabled="editSaving"
                                            class="inline-flex items-center gap-1 px-2 py-1 rounded-md text-[11px] font-bold text-red-600 bg-red-50 hover:bg-red-600 hover:text-white border border-red-200 transition cursor-pointer disabled:opacity-50"
                                            title="Hapus Agenda Ini">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        <span>Hapus</span>
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
                                        <label class="block text-[10px] font-bold text-[#64748B] uppercase tracking-widest mb-1.5">Notes</label>
                                        <input type="text" x-model="row.notes" placeholder="Keterangan"
                                               class="w-full px-3 py-2.5 bg-[#F8FAFC] border border-[#E2E8F0] rounded-lg text-[12.5px] text-[#1E293B] focus:outline-none focus:bg-white focus:border-[#8F0A0D] transition placeholder-gray-300">
                                    </div>
                                </div>
                                <div class="flex items-center justify-between pt-1">
                                    <div class="text-[11px] text-gray-400 font-medium">
                                        Dibuat oleh <strong class="text-gray-600 font-semibold" x-text="row.creator_name || '{{ auth()->user()?->name ?? 'Engineer' }}'"></strong>
                                    </div>
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

    {{-- ══ MODAL KONFIRMASI HAPUS (KONSISTEN DENGAN TIMESHEET) ══ --}}
    <template x-teleport="body">
        <div x-show="deleteConfirmOpen" 
             x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-[#0F172A]/60 z-[99999] flex items-center justify-center p-4 backdrop-blur-xs"
             @click.self="deleteConfirmOpen = false"
             @keydown.escape.window="deleteConfirmOpen = false">
            
            <div class="bg-white rounded-2xl w-[420px] max-w-full p-6 text-left shadow-[0_20px_60px_rgba(15,23,42,0.25)] border border-[#E2E8F0]">
                <div class="w-12 h-12 rounded-full bg-[#FEF2F2] flex items-center justify-center mx-auto mb-4 text-[#8F0A0D]">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>
                
                <h3 class="text-center font-display text-[16px] font-bold text-[#1E293B] mb-1.5" x-text="deleteModalTitle || 'Yakin Ingin Menghapus?'"></h3>
                <p class="text-center text-[12.5px] text-[#64748B] mb-6 break-words" x-text="deleteModalMessage"></p>

                <div class="flex gap-2.5">
                    <button type="button" @click="deleteConfirmOpen = false" :disabled="deleteLoading"
                            class="flex-1 py-2.5 px-4 rounded-xl bg-white text-[#334155] border border-[#CBD5E1] font-bold text-[12.5px] hover:bg-[#F8FAFC] transition cursor-pointer disabled:opacity-50">
                        Batal
                    </button>
                    <button type="button" @click="executeConfirmedDelete()" :disabled="deleteLoading"
                            class="flex-1 py-2.5 px-4 rounded-xl text-white font-bold text-[12.5px] transition cursor-pointer shadow-md disabled:opacity-50 flex items-center justify-center gap-2"
                            style="background:linear-gradient(135deg,#8F0A0D,#C41E2A);">
                        <svg x-show="deleteLoading" class="animate-spin h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span x-text="deleteLoading ? 'Menghapus...' : 'Ya, Hapus'"></span>
                    </button>
                </div>
            </div>
        </div>
    </template>

    {{-- ══ MODAL DIGITAL SIGNATURE PAD (INTERAKTIF CANVAS) ══ --}}
    <template x-teleport="body">
        <div x-show="isSignatureModalOpen" 
             x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-[#0F172A]/70 z-[99999] flex items-center justify-center p-4 backdrop-blur-xs"
             @click.self="closeSignaturePad()"
             @keydown.escape.window="closeSignaturePad()">
            
            <div class="bg-white rounded-2xl w-[520px] max-w-full p-6 text-left shadow-2xl border border-[#E2E8F0] anim-fade-up">
                <div class="flex items-center justify-between pb-3 border-b border-[#E2E8F0] mb-4">
                    <div>
                        <div class="flex items-center gap-1.5 mb-1">
                            <span class="w-2 h-2 rounded-full bg-[#8F0A0D]"></span>
                            <span class="text-[10.5px] font-bold text-[#8F0A0D] uppercase tracking-wider">DIGITAL SIGNATURE PAD</span>
                        </div>
                        <h3 class="font-bold text-[16px] text-[#0F172A]" x-text="'Tanda Tangan ' + signatureRoleLabel"></h3>
                    </div>
                    <button type="button" @click="closeSignaturePad()" class="text-gray-400 hover:text-gray-700 p-1.5 rounded-lg hover:bg-gray-100 cursor-pointer">
                        ✕
                    </button>
                </div>

                <div class="mb-4">
                    <p class="text-[12px] text-gray-500 mb-2.5">
                        Bubuhkan tanda tangan Anda di area kanvas bawah ini menggunakan mouse atau sentuhan jari:
                    </p>
                    <div class="relative bg-[#F8FAFC] border-2 border-dashed border-[#CBD5E1] rounded-xl overflow-hidden shadow-inner flex items-center justify-center">
                        <canvas id="signature-canvas" width="460" height="170" class="w-full h-44 cursor-crosshair touch-none bg-transparent"></canvas>
                        
                        <div class="absolute bottom-6 left-8 right-8 border-b border-gray-300 pointer-events-none text-center">
                            <span class="text-[9.5px] text-gray-400 font-semibold tracking-widest uppercase">Garis Tanda Tangan Resmi</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-2">
                    <button type="button" @click="clearSignaturePad()"
                            class="px-3.5 py-2 text-xs font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl transition flex items-center gap-1.5 cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        Hapus Coretan
                    </button>

                    <div class="flex items-center gap-2">
                        <button type="button" @click="closeSignaturePad()"
                                class="px-4 py-2 text-xs font-bold text-gray-600 bg-white border border-gray-200 hover:bg-gray-50 rounded-xl transition cursor-pointer">
                            Batal
                        </button>
                        <button type="button" @click="submitSignature()" :disabled="signatureSaving"
                                class="px-5 py-2 text-xs font-bold text-white rounded-xl shadow-md transition flex items-center gap-2 disabled:opacity-50 cursor-pointer"
                                style="background: linear-gradient(135deg, #8F0A0D, #C41E2A);">
                            <svg x-show="signatureSaving" class="animate-spin h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <span x-text="signatureSaving ? 'Menyimpan...' : 'Simpan & Bubuhkan TTD'"></span>
                        </button>
                    </div>
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
        // Detail modal & Form Laporan
        isDetailModalOpen: false,
        selectedDetail: null,
        activeDetailTab: 'form', // 'form' | 'logs'
        reportFormData: null,
        reportFormSaving: false,
        reportFormSavedSuccess: false,

        // Edit modal
        isEditModalOpen: false,
        editRows: [],
        editGroupName: '',
        editProjectId: '',
        editStoreUrl: '',
        editActTitle: '',
        editSaving: false,

        // Delete Confirmation Modal
        deleteConfirmOpen: false,
        deleteLoading: false,
        deleteActionType: null,
        deleteTargetUrl: null,
        deleteRowTarget: null,
        deleteRowIndex: null,
        deleteModalTitle: '',
        deleteModalMessage: '',

        // Digital Signature State
        sigInfo: null,
        isSignatureModalOpen: false,
        signatureRoleType: '',
        signatureRoleLabel: '',
        signatureSaving: false,
        canvasObj: null,
        ctxObj: null,
        isDrawing: false,

        openDetailModal(groupData) {
            this.selectedDetail = groupData;
            this.sigInfo = groupData?.sig_info || null;
            this.activeDetailTab = 'form';
            this.initDefaultReportData();
            if (this.sigInfo?.report_data) {
                this.loadReportData(this.sigInfo.report_data);
            }
            this.isDetailModalOpen = true;
            if (groupData && groupData.scope_key) {
                this.fetchSignatureStatus(groupData.scope_key);
            }
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
                creator_name: '{{ auth()->user()?->name ?? 'Engineer' }}',
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

        promptDeleteGroup(url, projectName) {
            this.deleteActionType = 'group';
            this.deleteTargetUrl = url;
            this.deleteModalTitle = 'Yakin Hapus Catatan Aktivitas?';
            this.deleteModalMessage = 'Seluruh catatan aktivitas pada proyek "' + projectName + '" akan dihapus secara permanen.';
            this.deleteConfirmOpen = true;
        },

        promptDeleteRow(row, idx) {
            this.deleteActionType = 'row';
            this.deleteRowTarget = row;
            this.deleteRowIndex = idx;
            this.deleteModalTitle = 'Yakin Hapus Agenda Ini?';
            const subjectSnippet = row.subject ? ' ("' + (row.subject.length > 40 ? row.subject.slice(0, 40) + '...' : row.subject) + '")' : '';
            this.deleteModalMessage = 'Agenda #' + (idx + 1) + subjectSnippet + ' akan dihapus secara permanen.';
            this.deleteConfirmOpen = true;
        },

        async executeConfirmedDelete() {
            if (this.deleteActionType === 'group') {
                this.deleteLoading = true;
                const token = document.querySelector('meta[name="csrf-token"]').content;
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = this.deleteTargetUrl;
                form.innerHTML = `
                    <input type="hidden" name="_token" value="${token}">
                    <input type="hidden" name="_method" value="DELETE">
                    <input type="hidden" name="delete_group" value="1">
                `;
                document.body.appendChild(form);
                form.submit();
                return;
            }

            if (this.deleteActionType === 'row') {
                if (!this.deleteRowTarget?.id) {
                    this.editRows.splice(this.deleteRowIndex, 1);
                    this.deleteConfirmOpen = false;
                    return;
                }
                this.deleteLoading = true;
                const token = document.querySelector('meta[name="csrf-token"]').content;
                const formData = new FormData();
                formData.append('_token', token);
                formData.append('_method', 'DELETE');

                try {
                    const res = await fetch(`/engineer/activity-logs/${this.deleteRowTarget.id}`, {
                        method: 'POST',
                        headers: { 'Accept': 'application/json' },
                        body: formData
                    });
                    if (res.ok) {
                        this.editRows.splice(this.deleteRowIndex, 1);
                        this.deleteConfirmOpen = false;
                        if (this.editRows.length === 0) {
                            window.location.reload();
                        }
                    } else {
                        const err = await res.json().catch(() => ({}));
                        alert(err.message || 'Gagal menghapus agenda.');
                    }
                } catch (e) {
                    alert('Terjadi kesalahan: ' + e.message);
                } finally {
                    this.deleteLoading = false;
                }
            }
        },

        async fetchSignatureStatus(scopeKey) {
            try {
                const res = await fetch(`{{ route('engineer.activity_log.signature_status') }}?scope_key=${encodeURIComponent(scopeKey)}`, {
                    headers: { 'Accept': 'application/json' }
                });
                if (res.ok) {
                    const data = await res.json();
                    this.sigInfo = data;
                    if (data?.report_data) {
                        this.loadReportData(data.report_data);
                    }
                }
            } catch (e) {
                console.error('Error fetching signature status:', e);
            }
        },

        initDefaultReportData() {
            const detail = this.selectedDetail;
            const items = detail?.items || [];
            const firstItem = items[0] || {};
            const lastItem = items[items.length - 1] || firstItem;

            const todayDate = new Date();
            const pad = (n) => String(n).padStart(2, '0');
            const todayIso = todayDate.getFullYear() + '-' + pad(todayDate.getMonth() + 1) + '-' + pad(todayDate.getDate());
            const firstDateRaw = firstItem.date_raw || (firstItem.date && firstItem.date.includes('-') ? firstItem.date : todayIso);

            const extractTime = (val, fallback) => {
                if (!val) return fallback;
                const m = val.match(/(\d{1,2}:\d{2})/);
                return m ? m[1].padStart(5, '0') : fallback;
            };

            const jamMulai = extractTime(firstItem.time_str || firstItem.start_time, '');
            const jamSelesai = extractTime(lastItem.time_str || lastItem.end_time || lastItem.start_time, '');

            this.reportFormData = {
                category: 'project',
                identitas: {
                    hari_tanggal: firstDateRaw,
                    no_laporan: this.sigInfo?.document_number || 'IPNET-ACT-' + todayDate.getFullYear() + pad(todayDate.getMonth()+1) + '-0001',
                    nama_project: detail?.project_name || '',
                    no_so_spk: '',
                    lokasi_site: '',
                    work_order: '',
                    nama_engineer: detail?.creator_name || detail?.engineer_name || '{{ auth()->user()?->name ?? 'Nugraha Pratama' }}',
                    nama_leader: this.sigInfo?.lead?.name || 'Nugraha Pratama',
                    customer: detail?.client_name || '',
                    jenis_pekerjaan: '',
                    pic_customer: firstItem.client_pic && firstItem.client_pic !== '-' ? firstItem.client_pic : '',
                    kategori_implementasi: false,
                    kategori_managed_service: false,
                    kategori_pekerjaan: '',
                    jabatan: '{{ auth()->user()?->position ?: (auth()->user()?->getRoleNames()->first() ?: 'Network Engineer') }}',
                    jam_mulai: jamMulai,
                    jam_selesai: jamSelesai,
                },
                manpower: [
                    {
                        nama: detail?.creator_name || detail?.engineer_name || '{{ auth()->user()?->name ?? '' }}',
                        unit_kerja: '',
                        jabatan: '{{ auth()->user()?->position ?: 'Field Engineer' }}',
                        keterangan: ''
                    }
                ],
                total_tenaga_kerja: 1,
                ruang_lingkup: {
                    target_hari_ini: '',
                    durasi_project: '',
                    scope_pekerjaan: '',
                    perangkat_sistem: '',
                    kriteria_selesai: '',
                },
                rincian_aktivitas: items.length > 0 ? items.map((it, idx) => ({
                    no: idx + 1,
                    waktu: extractTime(it.time_str || it.start_time, ''),
                    aktivitas: it.subject || '',
                    perangkat: '',
                    hasil: '',
                    status: it.status || '',
                    kendala: '',
                    tindak_lanjut: it.notes && it.notes !== '-' ? it.notes : '',
                })) : [
                    { no: 1, waktu: '', aktivitas: '', perangkat: '', hasil: '', status: '', kendala: '', tindak_lanjut: '' }
                ],
                materials: [
                    { nama: '', spesifikasi: '', qty: 1, satuan: 'Pcs', kondisi: 'Baik', keterangan: '' }
                ],
                test_results: [
                    { parameter: '', sebelum: 0, sesudah: 0, satuan: 'ms', metode: '', keterangan: '' }
                ],
                incidents: [
                    { waktu: '', kendala: '', dampak: '', tindakan: '', status: 'Closed' }
                ],
                hasil_akhir: {
                    status_pekerjaan: 'selesai',
                    progress_percent: 100,
                    kondisi_sistem: '',
                    outstanding: '',
                    rekomendasi: '',
                    eskalasi_pic: '',
                },
                foto_dokumentasi: {
                    before: { area: '', url: '', caption: '' },
                    progress: { area: '', url: '', caption: '' },
                    after: { area: '', url: '', caption: '' },
                },
                administrasi: {
                    nomor_wo: '',
                    nomor_ba: '',
                    lampiran: '',
                    folder: '',
                },

                // 2. MANAGED SERVICE DEFAULT DATA
                ms_identitas: {
                    tanggal: firstDateRaw,
                    no_report: this.sigInfo?.document_number || 'IPNET-MS-' + todayDate.getFullYear() + pad(todayDate.getMonth()+1) + '-0001',
                    customer: detail?.client_name || '',
                    no_contract: '',
                    site_lokasi: '',
                    ticket_incident_no: '',
                    engineer_pic: detail?.creator_name || detail?.engineer_name || '{{ auth()->user()?->name ?? 'Engineer' }}',
                    shift: 'Pagi / Regular',
                    jenis_aktivitas: {
                        monitoring: true,
                        pm: false,
                        cm: false,
                        incident: false,
                        request: false,
                        visit: false,
                    },
                    service_device: '',
                    jam_mulai: jamMulai || '08:00',
                    jam_selesai: jamSelesai || '17:00',
                },
                ms_sla: [
                    { parameter: 'Ticket Received', waktu: '08:00', target_sla: '< 15 Menit', aktual: '5 Menit', status: 'Met', keterangan: 'Normal' },
                    { parameter: 'Engineer Response', waktu: '08:05', target_sla: '< 30 Menit', aktual: '10 Menit', status: 'Met', keterangan: 'Respon cepat' },
                    { parameter: 'Service Restore', waktu: '09:30', target_sla: '< 4 Jam', aktual: '1.5 Jam', status: 'Met', keterangan: 'Layanan pulih' },
                    { parameter: 'Resolution / Close', waktu: '10:00', target_sla: '< 8 Jam', aktual: '2 Jam', status: 'Met', keterangan: 'Resolved' }
                ],
                ms_kondisi_perangkat: [
                    { no: 1, service_device: '', parameter: '', before: '', after: '', status: 'Good', keterangan: '' },
                    { no: 2, service_device: '', parameter: '', before: '', after: '', status: 'Good', keterangan: '' },
                    { no: 3, service_device: '', parameter: '', before: '', after: '', status: 'Good', keterangan: '' }
                ],
                ms_aktivitas: items.length > 0 ? items.map((it, idx) => ({
                    no: idx + 1,
                    waktu: extractTime(it.time_str || it.start_time, '09:00'),
                    aktivitas: it.subject || '',
                    ticket_alarm: '',
                    hasil: '',
                    status: it.status || 'Done',
                    kendala: '',
                    follow_up: it.notes && it.notes !== '-' ? it.notes : ''
                })) : [
                    { no: 1, waktu: '08:30', aktivitas: '', ticket_alarm: '', hasil: '', status: 'Done', kendala: '', follow_up: '' }
                ],
                ms_incident_escalation: [
                    { no: 1, incident: '', impact: '', root_cause: '', corrective_action: '', escalation: '', status: 'Closed' }
                ],
                ms_pm_checklist: [
                    { no: 1, item_pemeriksaan: '', kondisi: 'Baik', hasil: 'Normal', temuan: '', tindakan: '', status: 'OK' }
                ],
                ms_materials: [
                    { no: 1, item: '', type: '', qty: '', used_replaced: '', old_new: '', keterangan: '' }
                ],
                ms_evidence: [
                    { no: 1, evidence: '', waktu: '', foto_url: '', keterangan: '' }
                ],
                ms_closure: {
                    service_status: { resolved: true, monitoring: false, escalated: false, closed: false },
                    sla: { met: true, breach: false },
                    customer_confirmation: 'Layanan berjalan normal',
                    outstanding: '-'
                },
                ms_verifikasi: {
                    engineer_name: detail?.creator_name || detail?.engineer_name || '{{ auth()->user()?->name ?? 'Engineer' }}',
                    lead_name: this.sigInfo?.lead?.name || 'Nugraha Pratama',
                    customer_name: ''
                },

                // 3. HELP DESK DEFAULT DATA
                hd_identitas: {
                    tanggal: firstDateRaw,
                    shift: { pagi: true, siang: false, malam: false },
                    nama_engineer: detail?.creator_name || detail?.engineer_name || '{{ auth()->user()?->name ?? 'Engineer' }}',
                    team_leader: this.sigInfo?.lead?.name || 'Nugraha Pratama',
                    area_site: '',
                    customer_service: detail?.client_name || '',
                    jam_shift: '08:00 - 16:00 WIB',
                    jumlah_engineer: 1
                },
                hd_kondisi_awal: [
                    { no: 1, item_service: '', kondisi_awal: 'Normal', alarm_issue: 'Clear', status: 'OK', keterangan: '' }
                ],
                hd_aktivitas: items.length > 0 ? items.map((it, idx) => ({
                    no: idx + 1,
                    waktu: extractTime(it.time_str || it.start_time, '09:00'),
                    aktivitas: it.subject || '',
                    ticket_wo: '',
                    lokasi_device: '',
                    hasil: '',
                    status: it.status || 'Done'
                })) : [
                    { no: 1, waktu: '08:30', aktivitas: '', ticket_wo: '', lokasi_device: '', hasil: '', status: 'Done' }
                ],
                hd_ticket_incident: [
                    { no: 1, ticket: '', jenis: '', priority: 'Medium', start: '', restore: '', close_status: 'Closed', keterangan: '' }
                ],
                hd_monitoring_status: [
                    { no: 1, service_device: '', status: 'Up', alarm: 'None', performance: '', action: '', keterangan: '' }
                ],
                hd_pekerjaan_field: [
                    { no: 1, lokasi: '', pekerjaan: '', engineer: detail?.creator_name || detail?.engineer_name || '{{ auth()->user()?->name ?? 'Engineer' }}', hasil: '', foto_evidence: '', status: 'Done' }
                ],
                hd_kendala_escalation: [
                    { no: 1, kendala_incident: '', dampak: '', tindakan: '', escalated_to: '', status: 'Normal', next_action: '' }
                ],
                hd_handover: [
                    { no: 1, outstanding_issue: '', kondisi_terakhir: '', tindakan_berikutnya: '', pic: '', due_time: '', catatan: '' }
                ],
                hd_rekap_shift: {
                    total_ticket_diterima: 0,
                    total_ticket_closed: 0,
                    total_incident: 0,
                    outstanding: 0,
                    service_kritis: '0',
                    handover_diperlukan: false
                },
                hd_verifikasi: {
                    engineer_name: detail?.creator_name || detail?.engineer_name || '{{ auth()->user()?->name ?? 'Engineer' }}',
                    team_leader_name: this.sigInfo?.lead?.name || 'Nugraha Pratama',
                    next_engineer_name: ''
                }
            };
        },

        setDetailDocCategory(cat) {
            if (!this.reportFormData) {
                this.initDefaultReportData();
            }
            this.reportFormData.category = cat;
        },

        loadReportData(data) {
            this.initDefaultReportData();
            if (data && typeof data === 'object') {
                const detectedCategory = data.category || (data.ms_identitas ? 'managed_service' : (data.hd_identitas ? 'help_desk' : 'project'));
                this.reportFormData = {
                    ...this.reportFormData,
                    ...data,
                    category: detectedCategory,
                    identitas: { ...this.reportFormData.identitas, ...(data.identitas || {}) },
                    ruang_lingkup: { ...this.reportFormData.ruang_lingkup, ...(data.ruang_lingkup || {}) },
                    hasil_akhir: { ...this.reportFormData.hasil_akhir, ...(data.hasil_akhir || {}) },
                    foto_dokumentasi: {
                        before: { ...this.reportFormData.foto_dokumentasi.before, ...(data.foto_dokumentasi?.before || {}) },
                        progress: { ...this.reportFormData.foto_dokumentasi.progress, ...(data.foto_dokumentasi?.progress || {}) },
                        after: { ...this.reportFormData.foto_dokumentasi.after, ...(data.foto_dokumentasi?.after || {}) },
                    },
                    administrasi: { ...this.reportFormData.administrasi, ...(data.administrasi || {}) },
                    manpower: Array.isArray(data.manpower) && data.manpower.length > 0 ? data.manpower : this.reportFormData.manpower,
                    rincian_aktivitas: Array.isArray(data.rincian_aktivitas) && data.rincian_aktivitas.length > 0 ? data.rincian_aktivitas : this.reportFormData.rincian_aktivitas,
                    materials: Array.isArray(data.materials) && data.materials.length > 0 ? data.materials : this.reportFormData.materials,
                    test_results: Array.isArray(data.test_results) && data.test_results.length > 0 ? data.test_results : this.reportFormData.test_results,
                    incidents: Array.isArray(data.incidents) && data.incidents.length > 0 ? data.incidents : this.reportFormData.incidents,

                    // Managed Service
                    ms_identitas: {
                        ...this.reportFormData.ms_identitas,
                        ...(data.ms_identitas || {}),
                        jenis_aktivitas: {
                            ...this.reportFormData.ms_identitas.jenis_aktivitas,
                            ...(data.ms_identitas?.jenis_aktivitas || {})
                        }
                    },
                    ms_sla: Array.isArray(data.ms_sla) && data.ms_sla.length > 0 ? data.ms_sla : this.reportFormData.ms_sla,
                    ms_kondisi_perangkat: Array.isArray(data.ms_kondisi_perangkat) && data.ms_kondisi_perangkat.length > 0 ? data.ms_kondisi_perangkat : this.reportFormData.ms_kondisi_perangkat,
                    ms_aktivitas: Array.isArray(data.ms_aktivitas) && data.ms_aktivitas.length > 0 ? data.ms_aktivitas : this.reportFormData.ms_aktivitas,
                    ms_incident_escalation: Array.isArray(data.ms_incident_escalation) && data.ms_incident_escalation.length > 0 ? data.ms_incident_escalation : this.reportFormData.ms_incident_escalation,
                    ms_pm_checklist: Array.isArray(data.ms_pm_checklist) && data.ms_pm_checklist.length > 0 ? data.ms_pm_checklist : this.reportFormData.ms_pm_checklist,
                    ms_materials: Array.isArray(data.ms_materials) && data.ms_materials.length > 0 ? data.ms_materials : this.reportFormData.ms_materials,
                    ms_evidence: Array.isArray(data.ms_evidence) && data.ms_evidence.length > 0 ? data.ms_evidence : this.reportFormData.ms_evidence,
                    ms_closure: {
                        ...this.reportFormData.ms_closure,
                        ...(data.ms_closure || {}),
                        service_status: { ...this.reportFormData.ms_closure.service_status, ...(data.ms_closure?.service_status || {}) },
                        sla: { ...this.reportFormData.ms_closure.sla, ...(data.ms_closure?.sla || {}) }
                    },
                    ms_verifikasi: { ...this.reportFormData.ms_verifikasi, ...(data.ms_verifikasi || {}) },

                    // Help Desk
                    hd_identitas: {
                        ...this.reportFormData.hd_identitas,
                        ...(data.hd_identitas || {}),
                        shift: {
                            ...this.reportFormData.hd_identitas.shift,
                            ...(data.hd_identitas?.shift || {})
                        }
                    },
                    hd_kondisi_awal: Array.isArray(data.hd_kondisi_awal) && data.hd_kondisi_awal.length > 0 ? data.hd_kondisi_awal : this.reportFormData.hd_kondisi_awal,
                    hd_aktivitas: Array.isArray(data.hd_aktivitas) && data.hd_aktivitas.length > 0 ? data.hd_aktivitas : this.reportFormData.hd_aktivitas,
                    hd_ticket_incident: Array.isArray(data.hd_ticket_incident) && data.hd_ticket_incident.length > 0 ? data.hd_ticket_incident : this.reportFormData.hd_ticket_incident,
                    hd_monitoring_status: Array.isArray(data.hd_monitoring_status) && data.hd_monitoring_status.length > 0 ? data.hd_monitoring_status : this.reportFormData.hd_monitoring_status,
                    hd_pekerjaan_field: Array.isArray(data.hd_pekerjaan_field) && data.hd_pekerjaan_field.length > 0 ? data.hd_pekerjaan_field : this.reportFormData.hd_pekerjaan_field,
                    hd_kendala_escalation: Array.isArray(data.hd_kendala_escalation) && data.hd_kendala_escalation.length > 0 ? data.hd_kendala_escalation : this.reportFormData.hd_kendala_escalation,
                    hd_handover: Array.isArray(data.hd_handover) && data.hd_handover.length > 0 ? data.hd_handover : this.reportFormData.hd_handover,
                    hd_rekap_shift: { ...this.reportFormData.hd_rekap_shift, ...(data.hd_rekap_shift || {}) },
                    hd_verifikasi: { ...this.reportFormData.hd_verifikasi, ...(data.hd_verifikasi || {}) },
                };

                // Normalisasi checkbox kategori pekerjaan jika data lampau berupa string radio
                if (this.reportFormData.identitas.kategori_implementasi === undefined) {
                    const kat = String(this.reportFormData.identitas.kategori_pekerjaan || '').toLowerCase();
                    this.reportFormData.identitas.kategori_implementasi = kat.includes('implement');
                }
                if (this.reportFormData.identitas.kategori_managed_service === undefined) {
                    const kat = String(this.reportFormData.identitas.kategori_pekerjaan || '').toLowerCase();
                    this.reportFormData.identitas.kategori_managed_service = kat.includes('managed') || kat.includes('maintenance');
                }
            }
        },

        async saveReportForm() {
            if (!this.selectedDetail?.scope_key) return;
            this.reportFormSaving = true;
            this.reportFormSavedSuccess = false;
            const token = document.querySelector('meta[name="csrf-token"]')?.content || '';

            try {
                const res = await fetch('{{ route("engineer.activity_log.save_report_data") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify({
                        scope_key: this.selectedDetail.scope_key,
                        project_id: this.selectedDetail.project_id,
                        project_name: this.selectedDetail.project_name,
                        report_data: this.reportFormData
                    })
                });

                const data = await res.json();
                if (res.ok && data.success) {
                    this.reportFormSavedSuccess = true;
                    if (data.document_number) {
                        if (!this.sigInfo) this.sigInfo = {};
                        this.sigInfo.document_number = data.document_number;
                        this.reportFormData.identitas.no_laporan = data.document_number;
                    }
                    setTimeout(() => { this.reportFormSavedSuccess = false; }, 3500);
                } else {
                    alert(data.message || data.error || 'Gagal menyimpan formulir laporan.');
                }
            } catch (e) {
                alert('Terjadi kesalahan: ' + e.message);
            } finally {
                this.reportFormSaving = false;
            }
        },

        compressImage(file, maxDimension = 1000, quality = 0.72) {
            return new Promise((resolve) => {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const img = new Image();
                    img.onload = () => {
                        let width = img.width;
                        let height = img.height;
                        if (width > maxDimension || height > maxDimension) {
                            if (width > height) {
                                height = Math.round((height * maxDimension) / width);
                                width = maxDimension;
                            } else {
                                width = Math.round((width * maxDimension) / height);
                                height = maxDimension;
                            }
                        }
                        const canvas = document.createElement('canvas');
                        canvas.width = width;
                        canvas.height = height;
                        const ctx = canvas.getContext('2d');
                        ctx.drawImage(img, 0, 0, width, height);
                        resolve(canvas.toDataURL('image/jpeg', quality));
                    };
                    img.onerror = () => resolve(e.target.result);
                    img.src = e.target.result;
                };
                reader.onerror = () => resolve('');
                reader.readAsDataURL(file);
            });
        },

        async handlePhotoUpload(e, stage) {
            const file = e.target.files[0];
            if (!file) return;
            try {
                const compressed = await this.compressImage(file, 1000, 0.72);
                if (compressed) {
                    this.reportFormData.foto_dokumentasi[stage].url = compressed;
                }
            } catch (err) {
                const reader = new FileReader();
                reader.onload = (ev) => {
                    this.reportFormData.foto_dokumentasi[stage].url = ev.target.result;
                };
                reader.readAsDataURL(file);
            }
        },

        removePhoto(stage) {
            this.reportFormData.foto_dokumentasi[stage].url = '';
        },

        addManpowerRow() {
            this.reportFormData.manpower.push({ nama: '', unit_kerja: '', jabatan: '', keterangan: '' });
            this.reportFormData.total_tenaga_kerja = this.reportFormData.manpower.length;
        },
        removeManpowerRow(idx) {
            if (this.reportFormData.manpower.length > 1) {
                if (typeof idx === 'number') {
                    this.reportFormData.manpower.splice(idx, 1);
                } else {
                    this.reportFormData.manpower.pop();
                }
                this.reportFormData.total_tenaga_kerja = this.reportFormData.manpower.length;
            }
        },

        addActivityRow() {
            const nextNo = (this.reportFormData.rincian_aktivitas?.length || 0) + 1;
            this.reportFormData.rincian_aktivitas.push({
                no: nextNo,
                waktu: '09:00',
                aktivitas: '',
                perangkat: '',
                hasil: '',
                status: 'Selesai',
                kendala: '',
                tindak_lanjut: ''
            });
        },
        removeActivityRow(idx) {
            if (this.reportFormData.rincian_aktivitas.length > 1) {
                if (typeof idx === 'number') {
                    this.reportFormData.rincian_aktivitas.splice(idx, 1);
                } else {
                    this.reportFormData.rincian_aktivitas.pop();
                }
            }
        },

        addMaterialRow() {
            this.reportFormData.materials.push({ nama: '', spesifikasi: '', qty: 1, satuan: 'Pcs', kondisi: 'Baik', keterangan: '' });
        },
        removeMaterialRow(idx) {
            if (this.reportFormData.materials.length > 1) {
                if (typeof idx === 'number') {
                    this.reportFormData.materials.splice(idx, 1);
                } else {
                    this.reportFormData.materials.pop();
                }
            }
        },

        addTestResultRow() {
            this.reportFormData.test_results.push({ parameter: '', sebelum: 0, sesudah: 0, satuan: 'ms', metode: '', keterangan: '' });
        },
        removeTestResultRow(idx) {
            if (this.reportFormData.test_results.length > 1) {
                if (typeof idx === 'number') {
                    this.reportFormData.test_results.splice(idx, 1);
                } else {
                    this.reportFormData.test_results.pop();
                }
            }
        },

        addIncidentRow() {
            this.reportFormData.incidents.push({ waktu: '09:00', kendala: '', dampak: '', tindakan: '', status: '' });
        },
        removeIncidentRow(idx) {
            if (this.reportFormData.incidents.length > 1) {
                if (typeof idx === 'number') {
                    this.reportFormData.incidents.splice(idx, 1);
                } else {
                    this.reportFormData.incidents.pop();
                }
            }
        },

        addActRow() {
            this.addActivityRow();
        },
        removeActRow(idx) {
            this.removeActivityRow(idx);
        },

        // ═══ MANAGED SERVICE ROW HELPERS ═══
        addMsKondisiRow() {
            if (!Array.isArray(this.reportFormData.ms_kondisi_perangkat)) this.reportFormData.ms_kondisi_perangkat = [];
            const nextNo = this.reportFormData.ms_kondisi_perangkat.length + 1;
            this.reportFormData.ms_kondisi_perangkat.push({
                no: nextNo, service_device: '', parameter: '', before: '', after: '', status: 'Good', keterangan: ''
            });
        },
        removeMsKondisiRow(idx) {
            if (this.reportFormData.ms_kondisi_perangkat && this.reportFormData.ms_kondisi_perangkat.length > 1) {
                this.reportFormData.ms_kondisi_perangkat.splice(idx, 1);
            }
        },

        addMsActRow() {
            if (!Array.isArray(this.reportFormData.ms_aktivitas)) this.reportFormData.ms_aktivitas = [];
            const nextNo = this.reportFormData.ms_aktivitas.length + 1;
            this.reportFormData.ms_aktivitas.push({
                no: nextNo, waktu: '09:00', aktivitas: '', ticket_alarm: '', hasil: '', status: 'Done', kendala: '', follow_up: ''
            });
        },
        removeMsActRow(idx) {
            if (this.reportFormData.ms_aktivitas && this.reportFormData.ms_aktivitas.length > 1) {
                this.reportFormData.ms_aktivitas.splice(idx, 1);
            }
        },

        addMsIncidentRow() {
            if (!Array.isArray(this.reportFormData.ms_incident_escalation)) this.reportFormData.ms_incident_escalation = [];
            const nextNo = this.reportFormData.ms_incident_escalation.length + 1;
            this.reportFormData.ms_incident_escalation.push({
                no: nextNo, incident: '', impact: '', root_cause: '', corrective_action: '', escalation: '', status: 'Closed'
            });
        },
        removeMsIncidentRow(idx) {
            if (this.reportFormData.ms_incident_escalation && this.reportFormData.ms_incident_escalation.length > 1) {
                this.reportFormData.ms_incident_escalation.splice(idx, 1);
            }
        },

        addMsPmRow() {
            if (!Array.isArray(this.reportFormData.ms_pm_checklist)) this.reportFormData.ms_pm_checklist = [];
            const nextNo = this.reportFormData.ms_pm_checklist.length + 1;
            this.reportFormData.ms_pm_checklist.push({
                no: nextNo, item_pemeriksaan: '', kondisi: 'Baik', hasil: 'Normal', temuan: '', tindakan: '', status: 'OK'
            });
        },
        removeMsPmRow(idx) {
            if (this.reportFormData.ms_pm_checklist && this.reportFormData.ms_pm_checklist.length > 1) {
                this.reportFormData.ms_pm_checklist.splice(idx, 1);
            }
        },

        addMsMaterialRow() {
            if (!Array.isArray(this.reportFormData.ms_materials)) this.reportFormData.ms_materials = [];
            const nextNo = this.reportFormData.ms_materials.length + 1;
            this.reportFormData.ms_materials.push({
                no: nextNo, item: '', type: '', qty: '', used_replaced: '', old_new: '', keterangan: ''
            });
        },
        removeMsMaterialRow(idx) {
            if (this.reportFormData.ms_materials && this.reportFormData.ms_materials.length > 1) {
                this.reportFormData.ms_materials.splice(idx, 1);
            }
        },

        addMsEvidenceRow() {
            if (!Array.isArray(this.reportFormData.ms_evidence)) this.reportFormData.ms_evidence = [];
            const nextNo = this.reportFormData.ms_evidence.length + 1;
            this.reportFormData.ms_evidence.push({
                no: nextNo, evidence: '', waktu: '09:00', foto_url: '', keterangan: ''
            });
        },
        removeMsEvidenceRow(idx) {
            if (this.reportFormData.ms_evidence && this.reportFormData.ms_evidence.length > 1) {
                this.reportFormData.ms_evidence.splice(idx, 1);
            }
        },

        async handleMsEvidenceUpload(e, idx) {
            const file = e.target.files[0];
            if (!file) return;
            try {
                const compressed = await this.compressImage(file, 1000, 0.72);
                if (compressed && this.reportFormData.ms_evidence[idx]) {
                    this.reportFormData.ms_evidence[idx].foto_url = compressed;
                }
            } catch (err) {
                const reader = new FileReader();
                reader.onload = (ev) => {
                    if (this.reportFormData.ms_evidence[idx]) {
                        this.reportFormData.ms_evidence[idx].foto_url = ev.target.result;
                    }
                };
                reader.readAsDataURL(file);
            }
        },

        // ═══ HELP DESK ROW HELPERS ═══
        addHdKondisiRow() {
            if (!Array.isArray(this.reportFormData.hd_kondisi_awal)) this.reportFormData.hd_kondisi_awal = [];
            const nextNo = this.reportFormData.hd_kondisi_awal.length + 1;
            this.reportFormData.hd_kondisi_awal.push({
                no: nextNo, item_service: '', kondisi_awal: 'Normal', alarm_issue: 'Clear', status: 'OK', keterangan: ''
            });
        },
        removeHdKondisiRow(idx) {
            if (this.reportFormData.hd_kondisi_awal && this.reportFormData.hd_kondisi_awal.length > 1) {
                this.reportFormData.hd_kondisi_awal.splice(idx, 1);
            }
        },

        addHdActRow() {
            if (!Array.isArray(this.reportFormData.hd_aktivitas)) this.reportFormData.hd_aktivitas = [];
            const nextNo = this.reportFormData.hd_aktivitas.length + 1;
            this.reportFormData.hd_aktivitas.push({
                no: nextNo, waktu: '09:00', aktivitas: '', ticket_wo: '', lokasi_device: '', hasil: '', status: 'Done'
            });
        },
        removeHdActRow(idx) {
            if (this.reportFormData.hd_aktivitas && this.reportFormData.hd_aktivitas.length > 1) {
                this.reportFormData.hd_aktivitas.splice(idx, 1);
            }
        },

        addHdTicketRow() {
            if (!Array.isArray(this.reportFormData.hd_ticket_incident)) this.reportFormData.hd_ticket_incident = [];
            const nextNo = this.reportFormData.hd_ticket_incident.length + 1;
            this.reportFormData.hd_ticket_incident.push({
                no: nextNo, ticket: '', jenis: '', priority: 'Medium', start: '', restore: '', close_status: 'Closed', keterangan: ''
            });
        },
        removeHdTicketRow(idx) {
            if (this.reportFormData.hd_ticket_incident && this.reportFormData.hd_ticket_incident.length > 1) {
                this.reportFormData.hd_ticket_incident.splice(idx, 1);
            }
        },

        addHdMonitoringRow() {
            if (!Array.isArray(this.reportFormData.hd_monitoring_status)) this.reportFormData.hd_monitoring_status = [];
            const nextNo = this.reportFormData.hd_monitoring_status.length + 1;
            this.reportFormData.hd_monitoring_status.push({
                no: nextNo, service_device: '', status: 'Up', alarm: 'None', performance: '', action: '', keterangan: ''
            });
        },
        removeHdMonitoringRow(idx) {
            if (this.reportFormData.hd_monitoring_status && this.reportFormData.hd_monitoring_status.length > 1) {
                this.reportFormData.hd_monitoring_status.splice(idx, 1);
            }
        },

        addHdFieldRow() {
            if (!Array.isArray(this.reportFormData.hd_pekerjaan_field)) this.reportFormData.hd_pekerjaan_field = [];
            const nextNo = this.reportFormData.hd_pekerjaan_field.length + 1;
            this.reportFormData.hd_pekerjaan_field.push({
                no: nextNo, lokasi: '', pekerjaan: '', engineer: '{{ auth()->user()?->name ?? 'Engineer' }}', hasil: '', foto_evidence: '', status: 'Done'
            });
        },
        removeHdFieldRow(idx) {
            if (this.reportFormData.hd_pekerjaan_field && this.reportFormData.hd_pekerjaan_field.length > 1) {
                this.reportFormData.hd_pekerjaan_field.splice(idx, 1);
            }
        },

        addHdKendalaRow() {
            if (!Array.isArray(this.reportFormData.hd_kendala_escalation)) this.reportFormData.hd_kendala_escalation = [];
            const nextNo = this.reportFormData.hd_kendala_escalation.length + 1;
            this.reportFormData.hd_kendala_escalation.push({
                no: nextNo, kendala_incident: '', dampak: '', tindakan: '', escalated_to: '', status: 'Normal', next_action: ''
            });
        },
        removeHdKendalaRow(idx) {
            if (this.reportFormData.hd_kendala_escalation && this.reportFormData.hd_kendala_escalation.length > 1) {
                this.reportFormData.hd_kendala_escalation.splice(idx, 1);
            }
        },

        addHdHandoverRow() {
            if (!Array.isArray(this.reportFormData.hd_handover)) this.reportFormData.hd_handover = [];
            const nextNo = this.reportFormData.hd_handover.length + 1;
            this.reportFormData.hd_handover.push({
                no: nextNo, outstanding_issue: '', kondisi_terakhir: '', tindakan_berikutnya: '', pic: '', due_time: '', catatan: ''
            });
        },
        removeHdHandoverRow(idx) {
            if (this.reportFormData.hd_handover && this.reportFormData.hd_handover.length > 1) {
                this.reportFormData.hd_handover.splice(idx, 1);
            }
        },

        openSignaturePad(roleType, roleLabel) {
            this.signatureRoleType = roleType;
            this.signatureRoleLabel = roleLabel;
            this.isSignatureModalOpen = true;
            this.$nextTick(() => {
                this.initSignatureCanvas();
            });
        },

        closeSignaturePad() {
            this.isSignatureModalOpen = false;
            this.signatureRoleType = '';
            this.signatureRoleLabel = '';
        },

        initSignatureCanvas() {
            const canvas = document.getElementById('signature-canvas');
            if (!canvas) return;
            this.canvasObj = canvas;
            this.ctxObj = canvas.getContext('2d');
            this.ctxObj.strokeStyle = '#0F172A';
            this.ctxObj.lineWidth = 2.5;
            this.ctxObj.lineCap = 'round';
            this.ctxObj.lineJoin = 'round';
            this.clearSignaturePad();

            const getPos = (e) => {
                const rect = canvas.getBoundingClientRect();
                const scaleX = canvas.width / rect.width;
                const scaleY = canvas.height / rect.height;
                if (e.touches && e.touches.length > 0) {
                    return {
                        x: (e.touches[0].clientX - rect.left) * scaleX,
                        y: (e.touches[0].clientY - rect.top) * scaleY
                    };
                }
                return {
                    x: (e.clientX - rect.left) * scaleX,
                    y: (e.clientY - rect.top) * scaleY
                };
            };

            const startDraw = (e) => {
                e.preventDefault();
                this.isDrawing = true;
                const pos = getPos(e);
                this.ctxObj.beginPath();
                this.ctxObj.moveTo(pos.x, pos.y);
            };

            const draw = (e) => {
                if (!this.isDrawing) return;
                e.preventDefault();
                const pos = getPos(e);
                this.ctxObj.lineTo(pos.x, pos.y);
                this.ctxObj.stroke();
            };

            const stopDraw = (e) => {
                if (this.isDrawing) {
                    e.preventDefault();
                    this.ctxObj.closePath();
                    this.isDrawing = false;
                }
            };

            canvas.onmousedown = startDraw;
            canvas.onmousemove = draw;
            window.onmouseup = stopDraw;

            canvas.ontouchstart = startDraw;
            canvas.ontouchmove = draw;
            canvas.ontouchend = stopDraw;
        },

        clearSignaturePad() {
            if (this.ctxObj && this.canvasObj) {
                this.ctxObj.clearRect(0, 0, this.canvasObj.width, this.canvasObj.height);
            }
        },

        isCanvasEmpty(canvas) {
            const blank = document.createElement('canvas');
            blank.width = canvas.width;
            blank.height = canvas.height;
            return canvas.toDataURL() === blank.toDataURL();
        },

        async submitSignature() {
            if (!this.canvasObj || this.isCanvasEmpty(this.canvasObj)) {
                alert('Silakan bubuhkan tanda tangan Anda terlebih dahulu pada kanvas.');
                return;
            }

            const signatureData = this.canvasObj.toDataURL('image/png');
            this.signatureSaving = true;

            const token = document.querySelector('meta[name="csrf-token"]')?.content || '';
            const logIds = (this.selectedDetail?.items || []).map(i => i.id).filter(Boolean);

            try {
                const res = await fetch('{{ route("engineer.activity_log.signature_store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify({
                        scope_key: this.selectedDetail?.scope_key,
                        project_id: this.selectedDetail?.project_id,
                        project_name: this.selectedDetail?.project_name,
                        role_type: this.signatureRoleType,
                        signature_data: signatureData,
                        log_ids: logIds
                    })
                });

                const data = await res.json();
                if (res.ok && data.success) {
                    this.closeSignaturePad();
                    if (this.selectedDetail?.scope_key) {
                        await this.fetchSignatureStatus(this.selectedDetail.scope_key);
                    }
                } else {
                    alert(data.message || data.error || 'Gagal menyimpan tanda tangan.');
                }
            } catch (e) {
                alert('Terjadi kesalahan: ' + e.message);
            } finally {
                this.signatureSaving = false;
            }
        },

        buildExportUrl(type, extraParams = {}) {
            const base = type === 'pdf'
                ? '{{ route("engineer.activity_log.export_pdf") }}'
                : '{{ route("engineer.activity_log.export_excel") }}';
            const params = new URLSearchParams();
            if (this.selectedDetail) {
                if (this.selectedDetail.scope_key) params.set('scope_key', this.selectedDetail.scope_key);
                if (this.selectedDetail.project_id) params.set('project_id', this.selectedDetail.project_id);
                if (this.selectedDetail.project_name) params.set('project_name', this.selectedDetail.project_name);
                if (this.selectedDetail.items && this.selectedDetail.items.length > 0) {
                    const ids = this.selectedDetail.items.map(item => item.id).filter(Boolean);
                    if (ids.length > 0) params.set('log_ids', ids.join(','));
                }
            } else {
                const currentParams = new URLSearchParams(window.location.search);
                currentParams.forEach((val, key) => params.set(key, val));
            }
            if (this.reportFormData && this.reportFormData.category) {
                params.set('category', this.reportFormData.category);
            }
            if (extraParams && typeof extraParams === 'object') {
                Object.entries(extraParams).forEach(([k, v]) => params.set(k, v));
            }
            const qs = params.toString();
            return qs ? (base + '?' + qs) : base;
        },

        async saveReportFormSilently() {
            if (!this.selectedDetail?.scope_key || !this.reportFormData) return;
            const token = document.querySelector('meta[name="csrf-token"]')?.content || '';
            try {
                const res = await fetch('{{ route("engineer.activity_log.save_report_data") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify({
                        scope_key: this.selectedDetail.scope_key,
                        project_id: this.selectedDetail.project_id,
                        project_name: this.selectedDetail.project_name,
                        report_data: this.reportFormData
                    })
                });
                const data = await res.json();
                if (res.ok && data.success && data.document_number) {
                    if (!this.sigInfo) this.sigInfo = {};
                    this.sigInfo.document_number = data.document_number;
                    this.reportFormData.identitas.no_laporan = data.document_number;
                }
            } catch (e) {
                console.warn('Silently saving report form:', e);
            }
        },

        async downloadReportPdf(action = 'download') {
            // Simpan isian form terlebih dahulu agar PDF memuat data termutakhir
            if (this.selectedDetail?.scope_key && this.reportFormData) {
                await this.saveReportFormSilently();
            }
            const exportUrl = this.buildExportUrl('pdf', action === 'stream' ? { stream: 1 } : {});
            if (action === 'stream') {
                window.open(exportUrl, '_blank');
            } else {
                window.location.href = exportUrl;
            }
        },
    };
}
</script>
@endpush

@endsection
