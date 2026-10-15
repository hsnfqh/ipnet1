{{-- ═══════════════════════════════════════════════════════════════════════ --}}
{{-- PENGESAHAN SERAH TERIMA PROYEK (OFFICIAL HANDOVER GATEWAY)             --}}
{{-- ═══════════════════════════════════════════════════════════════════════ --}}
@php
    $authUser = auth()->user();
    $userRoles = $authUser && method_exists($authUser, 'roles') ? $authUser->roles->pluck('name')->toArray() : [];
    $currentStatus = $currentStatus ?? ($project->status ?? 'Draft');
    $currentSalesStage = $project->sales_stage ?: 'Qualification';
    $isClosedWon = ($currentSalesStage === 'Closed Won');
    $isClosedLost = ($currentSalesStage === 'Closed Lost');
    $isBoth = ($project->handover_target === 'both');
    $isMs = ($project->handover_target === 'managed_service');
    $isHandoverApproved = ($project->commercial_handover_status === 'Approved');

    // Cek wewenang pengesahan handover
    $canConfirmHandover = $authUser && (
        \App\Helpers\ScopeHelper::isPmo($authUser)
        || \App\Helpers\ScopeHelper::isExecutive($authUser)
        || ($project->sales_id && $project->sales_id == $authUser->id)
        || ($project->created_by == $authUser->id)
        || !empty(array_intersect(['Super Admin', 'Admin', 'Director', 'Direktur', 'HD / Direktur', 'Division Head', 'Head Divisi', 'Group Leader Delivery & Operation', 'PMO', 'Project Manager'], $userRoles))
    );

    $pmoUsersList = $pmoUsers ?? \App\Models\User::whereHas('roles', fn($q) => $q->whereIn('name', ['PMO', 'Project Manager', 'Lead Engineer']))->get();
@endphp

<div class="ipnet-card p-6 space-y-5 transition-all">
    
    {{-- Header Bersih & Ringkas (Konsisten dengan Kolaborasi Tim Solusi Teknis) --}}
    <div class="flex items-center justify-between gap-3 border-b border-slate-100 pb-3 flex-wrap">
        <div class="min-w-0">
            <p class="text-[#8F0A0D] text-[11px] font-bold inline-flex items-center uppercase tracking-wider mb-0.5">
                <span class="w-1.5 h-1.5 rounded-full bg-[#8F0A0D] inline-block mr-1.5"></span> SERAH TERIMA &amp; OPERASIONAL
            </p>
            <h3 class="text-sm sm:text-base font-bold text-slate-900">Pengesahan Serah Terima Proyek</h3>
            <p class="text-xs text-slate-500 mt-0.5">Alur verifikasi legalitas kontrak, penetapan ruang lingkup deliverable, dan serah terima ke tim delivery.</p>
        </div>
        
        <div class="shrink-0 flex items-center">
            @if($isHandoverApproved)
                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center gap-1.5 shadow-2xs shrink-0 whitespace-nowrap">
                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    <span>Resmi Diserahterimakan</span>
                </span>
            @elseif($isClosedWon)
                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-200 flex items-center gap-1.5 shadow-2xs shrink-0 whitespace-nowrap">
                    <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span>
                    <span>Siap Serah Terima</span>
                </span>
            @else
                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-800 border border-amber-200 flex items-center gap-1.5 shadow-2xs shrink-0 whitespace-nowrap">
                    <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    <span>Menunggu Closed Won</span>
                </span>
            @endif
        </div>
    </div>

    {{-- 3-Pilar Status Transisi (Konsisten dengan Card Tim Solusi Teknis) --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs items-stretch">
        
        {{-- ══ CARD 1: RUANG LINGKUP TEKNIS ══ --}}
        <div class="p-3.5 sm:p-4 rounded-xl bg-white border border-slate-200 shadow-2xs flex flex-col justify-between space-y-3.5 hover:border-slate-300 transition overflow-hidden">
            <div class="space-y-3">
                <div class="flex flex-col 2xl:flex-row 2xl:items-center 2xl:justify-between items-start gap-1.5 pb-2 border-b border-slate-100 min-h-[50px] 2xl:min-h-0">
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200 uppercase tracking-wider self-start">
                        RUANG LINGKUP TEKNIS
                    </span>
                    @php
                        $scopeBadgeClass = ($isHandoverApproved || ($isClosedWon && $project->handover_target))
                            ? 'bg-emerald-50 text-emerald-700 border-emerald-200'
                            : ($isClosedWon ? 'bg-amber-50 text-amber-800 border-amber-200' : 'bg-slate-100 text-slate-500 border-slate-200');
                        $scopeBadgeLabel = ($isHandoverApproved || ($isClosedWon && $project->handover_target))
                            ? '✓ Siap'
                            : ($isClosedWon ? 'Menunggu Perumusan' : 'Menunggu');
                    @endphp
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border self-start 2xl:self-auto shrink-0 {{ $scopeBadgeClass }}">
                        {{ $scopeBadgeLabel }}
                    </span>
                </div>

                {{-- Entity --}}
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-[#8F0A0D] to-[#BA1B1D] text-white flex items-center justify-center shrink-0 shadow-2xs">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <h4 class="font-bold text-xs {{ $project->handover_target ? 'text-slate-900' : 'text-slate-400 italic' }} truncate">
                            @if($isBoth)
                                Dual Scope: Proyek &amp; Managed Service
                            @elseif($isMs)
                                Kontrak Managed Service (Opex)
                            @elseif($project->handover_target === 'pmo')
                                Proyek Implementasi
                            @else
                                Belum Ditentukan Pre-Sales
                            @endif
                        </h4>
                        <p class="text-[10.5px] text-slate-500 truncate">
                            Divisi: {{ $project->division ? $project->division->name : 'Belum didelegasikan' }}
                        </p>
                    </div>
                </div>

                {{-- Details Box --}}
                <div class="p-3 rounded-lg bg-slate-50 border border-slate-200/80 space-y-1">
                    <div class="font-bold text-slate-900 text-xs">Target Penyerahan Deliverable</div>
                    <div class="text-[10.5px] text-slate-500 line-clamp-2">
                        {{ $project->handover_target ? 'Ruang lingkup proyek dan deliverable teknis telah teridentifikasi.' : 'Menunggu perumusan lingkup kerja teknis dan deliverable.' }}
                    </div>
                </div>
            </div>
        </div>

        {{-- ══ CARD 2: LEGALITAS KONTRAK (PO/SPK) ══ --}}
        <div class="p-3.5 sm:p-4 rounded-xl bg-white border border-slate-200 shadow-2xs flex flex-col justify-between space-y-3.5 hover:border-slate-300 transition overflow-hidden">
            <div class="space-y-3">
                <div class="flex flex-col 2xl:flex-row 2xl:items-center 2xl:justify-between items-start gap-1.5 pb-2 border-b border-slate-100 min-h-[50px] 2xl:min-h-0">
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200 uppercase tracking-wider self-start">
                        LEGALITAS KONTRAK (PO/SPK)
                    </span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border self-start 2xl:self-auto shrink-0 {{ $isClosedWon ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-amber-50 text-amber-800 border-amber-300' }}">
                        {{ $isClosedWon ? '✓ Closed Won' : $currentSalesStage }}
                    </span>
                </div>

                {{-- Entity --}}
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-[#8F0A0D] to-[#BA1B1D] text-white flex items-center justify-center shrink-0 shadow-2xs">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <h4 class="font-bold text-xs {{ $isClosedWon ? 'text-slate-900' : 'text-slate-600' }} truncate">
                            {{ $project->po_spk_number ? 'No. PO: ' . $project->po_spk_number : ($isClosedWon ? 'PO/Kontrak Sah Terbit' : 'Menunggu Penerbitan PO') }}
                        </h4>
                        <p class="text-[10.5px] text-slate-500 truncate">
                            @if(!empty($project->contract_value))
                                Nilai: Rp {{ number_format((float)$project->contract_value, 0, ',', '.') }}
                            @else
                                Peluang: {{ $currentProb }}%
                            @endif
                        </p>
                    </div>
                </div>

                {{-- Details Box --}}
                <div class="p-3 rounded-lg bg-slate-50 border border-slate-200/80 space-y-1">
                    <div class="font-bold text-slate-900 text-xs">Penerbitan Kontrak &amp; PO</div>
                    <div class="text-[10.5px] text-slate-500 line-clamp-2">
                        {{ $isClosedWon ? 'PO/SPK resmi telah diterbitkan oleh pihak klien & terverifikasi.' : 'Menunggu penerbitan PO/SPK resmi atau kontrak bertandatangan.' }}
                    </div>
                </div>
            </div>
        </div>

        {{-- ══ CARD 3: KESIAPAN EKSEKUSI (PMO DELIVERY) ══ --}}
        <div class="p-3.5 sm:p-4 rounded-xl bg-white border border-slate-200 shadow-2xs flex flex-col justify-between space-y-3.5 hover:border-slate-300 transition overflow-hidden">
            <div class="space-y-3">
                <div class="flex flex-col 2xl:flex-row 2xl:items-center 2xl:justify-between items-start gap-1.5 pb-2 border-b border-slate-100 min-h-[50px] 2xl:min-h-0">
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200 uppercase tracking-wider self-start">
                        KESIAPAN EKSEKUSI
                    </span>
                    @php
                        $execBadgeClass = $isHandoverApproved
                            ? 'bg-emerald-50 text-emerald-700 border-emerald-200'
                            : ($isClosedWon ? 'bg-blue-50 text-blue-700 border-blue-200' : 'bg-slate-100 text-slate-500 border-slate-200');
                        $execBadgeLabel = $isHandoverApproved
                            ? '✓ Berjalan'
                            : ($isClosedWon ? 'Siap Sahkan' : 'Menunggu');
                    @endphp
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border self-start 2xl:self-auto shrink-0 {{ $execBadgeClass }}">
                        {{ $execBadgeLabel }}
                    </span>
                </div>

                {{-- Entity --}}
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-[#8F0A0D] to-[#BA1B1D] text-white flex items-center justify-center shrink-0 shadow-2xs">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <h4 class="font-bold text-xs {{ $project->pm ? 'text-slate-900' : 'text-slate-400 italic' }} truncate">
                            Project Manager: {{ $project->pm ? $project->pm->name : 'Ditetapkan Saat Handover' }}
                        </h4>
                        <p class="text-[10.5px] text-slate-500 truncate">
                            Dokumen teknis tersedia di Lampiran
                        </p>
                    </div>
                </div>

                {{-- Details Box --}}
                <div class="p-3 rounded-lg bg-slate-50 border border-slate-200/80 space-y-1">
                    <div class="font-bold text-slate-900 text-xs">Penetapan Tim Lapangan</div>
                    <div class="text-[10.5px] text-slate-500 line-clamp-2">
                        {{ $isHandoverApproved ? 'Handover tervalidasi dan penugasan tim operasional aktif.' : ($isClosedWon ? 'Pilih Project Manager di form bawah untuk mengesahkan.' : 'Handover dapat disahkan setelah status mencapai Closed Won.') }}
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- ═══ STATUS & AKSI SERAH TERIMA ═══ --}}
    @if(!$isClosedWon)
        {{-- Proyek Belum Closed Won --}}
        <div class="p-4 rounded-xl bg-amber-50/80 border border-amber-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
            <div class="flex items-center gap-2 text-amber-900">
                <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Proyek saat ini masih di tahap penawaran ({{ $currentSalesStage }}). Serah terima proyek dibuka setelah status mencapai Closed Won.</span>
            </div>

            @if(empty($isPresalesOrSaOnly) && ($canAssignSales ?? false))
                <button type="button" 
                        onclick="window.openModal('modal-edit-pipeline')"
                        class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-bold text-white bg-[#8F0A0D] hover:bg-[#75080A] transition shadow-xs cursor-pointer whitespace-nowrap shrink-0">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    <span>Ubah Tahap Sales</span>
                </button>
            @endif
        </div>

    @elseif($isClosedWon && !$isHandoverApproved)
        {{-- Proyek Sudah Closed Won, Belum Disahkan --}}
        <div class="p-5 rounded-xl bg-blue-50/60 border border-blue-200 space-y-4">
            <div class="flex items-center justify-between flex-wrap gap-2 border-b border-blue-100 pb-2">
                <div class="font-bold text-blue-950 text-xs flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-blue-600 animate-ping"></span>
                    <span>Proyek Closed Won — Silakan Tetapkan PM &amp; Sahkan Serah Terima</span>
                </div>
            </div>

            {{-- Formulir Pengesahan Handover --}}
            <form action="{{ route('projects.confirm_handover_pmo', $project->id) }}" method="POST" class="space-y-3.5 text-xs">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 text-[11px] mb-1">
                            PENETAPAN PROJECT MANAGER (PMO) <span class="text-[#8F0A0D]">*</span>
                        </label>
                        <select name="pm_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-900 bg-white font-bold hover:border-slate-400 focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 cursor-pointer transition">
                            <option value="">-- Pilih Project Manager --</option>
                            @foreach($pmoUsersList as $pmo)
                                <option value="{{ $pmo->id }}" {{ ($project->pm_id == $pmo->id || (empty($project->pm_id) && str_contains(strtolower($pmo->name), 'rizki'))) ? 'selected' : '' }}>
                                    {{ $pmo->name }}
                                </option>
                            @endforeach
                            @php
                                $otherEngineers = ($allUsers ?? \App\Models\User::orderBy('name')->get())->whereNotIn('id', $pmoUsersList->pluck('id'));
                            @endphp
                            @foreach($otherEngineers as $oe)
                                <option value="{{ $oe->id }}" {{ $project->pm_id == $oe->id ? 'selected' : '' }}>
                                    {{ $oe->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 text-[11px] mb-1">
                            CATATAN SERAH TERIMA (OPSIONAL)
                        </label>
                        <input type="text" name="handover_notes" placeholder="Contoh: Instalasi dimulai H+3 setelah PO."
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-900 bg-white hover:border-slate-400 focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition">
                    </div>
                </div>

                <div class="pt-2 flex items-center justify-end">
                    @if($canConfirmHandover)
                        <button type="submit" 
                                onclick="return confirm('Apakah Anda yakin ingin mengesahkan serah terima proyek ini ke PMO?')"
                                class="inline-flex items-center gap-2 px-5 py-2 rounded-xl text-xs font-bold btn-ipnet-primary transition cursor-pointer shadow-sm active:scale-95">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Sahkan Serah Terima ke PMO</span>
                        </button>
                    @else
                        <span class="text-xs text-slate-400 font-medium italic">
                            Wewenang pengesahan: PMO, Sales terkait, atau Manajemen
                        </span>
                    @endif
                </div>
            </form>
        </div>

    @else
        {{-- Proyek Sudah Resmi Diserahterimakan --}}
        <div class="p-4 sm:p-5 rounded-xl bg-gradient-to-r from-emerald-50/70 via-white to-emerald-50/40 border border-emerald-200 text-xs space-y-3">
            <div class="flex items-center justify-between flex-wrap gap-2 border-b border-emerald-100 pb-2.5">
                <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-lg bg-emerald-600 text-white flex items-center justify-center font-bold text-xs shrink-0">
                        ✓
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-900 text-xs">
                            Serah Terima Proyek Telah Disahkan
                        </h4>
                        <p class="text-[11px] text-slate-500">
                            Disahkan pada {{ $project->commercial_handover_at ? \Carbon\Carbon::parse($project->commercial_handover_at)->format('d M Y, H:i') : '-' }}
                            @if(!empty($project->commercialHandoverBy))
                                oleh <strong>{{ $project->commercialHandoverBy->name }}</strong>
                            @endif
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-1 rounded-md text-[10.5px] font-bold bg-white text-emerald-800 border border-emerald-300 shadow-2xs">
                        Fase: {{ $project->stage === 'Operate' ? 'Operasional (Operate)' : 'Delivery Lapangan (Deliver)' }}
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-slate-700">
                <div class="p-2.5 rounded-lg bg-white border border-emerald-100">
                    <span class="text-[10px] uppercase font-bold text-slate-400 block">PROJECT MANAGER</span>
                    <strong class="text-slate-900 text-xs">{{ $project->pm ? $project->pm->name : 'Tim Delivery Proyek' }}</strong>
                </div>
                <div class="p-2.5 rounded-lg bg-white border border-emerald-100">
                    <span class="text-[10px] uppercase font-bold text-slate-400 block">NOMOR PO / SPK</span>
                    <strong class="text-slate-900 text-xs">{{ $project->po_spk_number ?: '(PO Tercatat)' }}</strong>
                </div>
                <div class="p-2.5 rounded-lg bg-white border border-emerald-100">
                    <span class="text-[10px] uppercase font-bold text-slate-400 block">SKEMA PROYEK</span>
                    <strong class="text-slate-900 text-xs">
                        @if($isBoth)
                            Dual Scope (Implementasi &amp; MS)
                        @elseif($isMs)
                            Managed Service &amp; Maintenance
                        @else
                            Implementasi Teknis Proyek PMO
                        @endif
                    </strong>
                </div>
            </div>
        </div>
    @endif

</div>
