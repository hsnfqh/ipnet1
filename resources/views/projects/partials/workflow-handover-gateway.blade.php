{{-- ═══════════════════════════════════════════════════════════════════════ --}}
{{-- 5. GERBANG SERAH TERIMA RESMI PROYEK (OFFICIAL HANDOVER GATEWAY)       --}}
{{-- Sesuai SOP Enterprise: Transisi Resmi dari Sales/Pre-Sales ke PMO/Ops  --}}
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
    
    {{-- Header & Gateway Status Indicator --}}
    <div class="flex items-start sm:items-center justify-between gap-3 border-b border-slate-100 pb-3">
        <div class="min-w-0 flex-1">
            <p class="text-[#8F0A0D] text-[11px] font-bold inline-flex items-center uppercase tracking-wider mb-0.5">
                <span class="w-1.5 h-1.5 rounded-full bg-[#8F0A0D] inline-block mr-1.5"></span> 
                TAHAP 2: GERBANG SERAH TERIMA RESMI (GATEWAY PMO &amp; OPERASIONAL)
            </p>
            <h3 class="text-sm sm:text-base font-bold text-slate-900">
                Pengesahan Serah Terima Resmi Proyek ke PMO &amp; Operasional
            </h3>
            <p class="text-xs text-slate-500 mt-0.5">
                Protokol pengendalian mutu (Gatekeeper SOP) untuk memvalidasi legalitas kontrak dari Sales dan kesiapan ruang lingkup teknis dari Pre-Sales sebelum proyek dieksekusi.
            </p>
        </div>
        
        <div class="shrink-0 flex items-center">
            @if($isHandoverApproved)
                <span class="px-3 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200 flex items-center gap-1.5 shadow-2xs whitespace-nowrap">
                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    <span>Resmi Berjalan di PMO / Operasional</span>
                </span>
            @elseif($isClosedWon)
                <span class="px-3 py-1 rounded-full text-[11px] font-bold bg-blue-50 text-blue-800 border border-blue-200 flex items-center gap-1.5 shadow-2xs whitespace-nowrap animate-pulse">
                    <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                    <span>Siap Diserahterimakan ke PMO</span>
                </span>
            @else
                <span class="px-3 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-800 border border-amber-200 flex items-center gap-1.5 shadow-2xs whitespace-nowrap">
                    <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    <span>Terkunci (Menunggu Kesepakatan Kontrak / Closed Won)</span>
                </span>
            @endif
        </div>
    </div>

    {{-- 3-Pilar Transisi & Kesiapan Serah Terima Proyek --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3.5 text-xs">
        
        {{-- Pilar 1: Kesiapan Lingkup Teknis (Pre-Sales) --}}
        <div class="p-3.5 rounded-xl border {{ $project->handover_target ? 'bg-emerald-50/40 border-emerald-200' : 'bg-slate-50 border-slate-200' }} space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold uppercase tracking-wider {{ $project->handover_target ? 'text-emerald-800' : 'text-slate-500' }}">
                    1. Ruang Lingkup Teknis
                </span>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $project->handover_target ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-600' }}">
                    {{ $project->handover_target ? '✓ Siap' : 'Belum Ditentukan' }}
                </span>
            </div>
            <div>
                <div class="font-bold text-xs {{ $project->handover_target ? 'text-slate-900' : 'text-slate-500 italic' }}">
                    @if($isBoth)
                        Dual Scope: PMO &amp; Managed Service
                    @elseif($isMs)
                        Kontrak Managed Service (Opex)
                    @elseif($project->handover_target === 'pmo')
                        Proyek Implementasi (PMO Delivery)
                    @else
                        Menunggu Perumusan Pre-Sales
                    @endif
                </div>
                <p class="text-[11px] text-slate-500 mt-0.5 truncate">
                    Divisi: {{ $project->division ? $project->division->name : 'Belum didelegasikan' }}
                </p>
            </div>
        </div>

        {{-- Pilar 2: Legalitas Kontrak & PO Komersial (Sales) --}}
        <div class="p-3.5 rounded-xl border {{ $isClosedWon ? 'bg-emerald-50/40 border-emerald-200' : 'bg-slate-50 border-slate-200' }} space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold uppercase tracking-wider {{ $isClosedWon ? 'text-emerald-800' : 'text-slate-500' }}">
                    2. Legalitas Kontrak (PO/SPK)
                </span>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $isClosedWon ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                    {{ $isClosedWon ? '✓ Closed Won' : $currentSalesStage }}
                </span>
            </div>
            <div>
                <div class="font-bold text-xs {{ $isClosedWon ? 'text-slate-900' : 'text-slate-600' }} truncate">
                    {{ $project->po_spk_number ? 'No. PO: ' . $project->po_spk_number : ($isClosedWon ? 'PO/Kontrak Sah' : 'Menunggu Penerbitan PO Klien') }}
                </div>
                <p class="text-[11px] text-slate-500 mt-0.5 truncate">
                    @if(!empty($project->contract_value))
                        Nilai: Rp {{ number_format((float)$project->contract_value, 0, ',', '.') }}
                    @else
                        Estimasi Peluang: {{ $currentProb }}%
                    @endif
                </p>
            </div>
        </div>

        {{-- Pilar 3: Kelengkapan Dokumen Lampiran (BoQ / SOW) --}}
        <div class="p-3.5 rounded-xl border {{ $isClosedWon && $project->handover_target ? 'bg-emerald-50/40 border-emerald-200' : 'bg-slate-50 border-slate-200' }} space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold uppercase tracking-wider {{ $isClosedWon && $project->handover_target ? 'text-emerald-800' : 'text-slate-500' }}">
                    3. Kesiapan Eksekusi
                </span>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $isHandoverApproved ? 'bg-emerald-100 text-emerald-800' : ($isClosedWon ? 'bg-blue-100 text-blue-800' : 'bg-slate-200 text-slate-600') }}">
                    {{ $isHandoverApproved ? '✓ Berjalan' : ($isClosedWon ? 'Siap Sahkan' : 'Menunggu') }}
                </span>
            </div>
            <div>
                <div class="font-bold text-xs text-slate-900 truncate">
                    Project Manager: {{ $project->pm ? $project->pm->name : 'Ditetapkan Saat Handover' }}
                </div>
                <p class="text-[11px] text-slate-500 mt-0.5 truncate">
                    Dokumen teknis tersedia di Lampiran
                </p>
            </div>
        </div>

    </div>

    {{-- ═══ ACTION & EXECUTION GATEWAY AREA ═══ --}}
    @if(!$isClosedWon)
        {{-- KONDISI 1: Proyek Belum Closed Won (Menunggu Penandatanganan Kontrak) --}}
        <div class="p-4 sm:p-5 rounded-xl bg-gradient-to-r from-amber-50/80 via-slate-50 to-amber-50/40 border border-amber-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4 text-xs">
            <div class="space-y-1.5 flex-1 min-w-0">
                <div class="font-bold text-amber-950 flex items-center gap-2">
                    <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Protokol SOP Enterprise: Proyek Masih Berada di Tahap Penawaran ({{ $currentSalesStage }})</span>
                </div>
                <p class="text-[11.5px] text-amber-900 leading-relaxed">
                    Sesuai standar operasional IPNet, serah terima resmi ke PMO / Tim Operasional hanya dapat dilaksanakan setelah Sales menyelesaikan kesepakatan komersial (<strong>Closed Won</strong>) dan mengunggah dokumen legalitas PO/SPK. Tim Pre-Sales dipersilakan melengkapi perencanaan ruang lingkup teknis dan lampiran dokumen pada kartu di atas.
                </p>
            </div>

            @if(empty($isPresalesOrSaOnly) && ($canAssignSales ?? false))
                <button type="button" 
                        onclick="window.openModal('modal-edit-pipeline')"
                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-white bg-gradient-to-r from-[#8F0A0D] to-[#BA1B1D] hover:from-[#7B080A] hover:to-[#9E1416] transition shadow-sm cursor-pointer whitespace-nowrap shrink-0">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    <span>Perbarui Tahapan Sales</span>
                </button>
            @endif
        </div>

    @elseif($isClosedWon && !$isHandoverApproved)
        {{-- KONDISI 2: Proyek Sudah Closed Won, Menunggu Pengesahan Serah Terima Resmi ke PMO --}}
        <div class="p-5 rounded-xl bg-blue-50/60 border border-blue-200 space-y-4">
            <div class="flex items-center justify-between flex-wrap gap-2 border-b border-blue-100 pb-3">
                <div class="space-y-0.5">
                    <div class="font-bold text-blue-950 text-xs flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-blue-600 animate-ping"></span>
                        <span>Kesepakatan Kontrak Closed Won Terkonfirmasi — Siap Disahkan ke PMO</span>
                    </div>
                    <p class="text-[11.5px] text-blue-800">
                        Dokumen legalitas PO/SPK telah tercatat. Silakan tetapkan atau konfirmasi Project Manager definitif sebelum proyek disahkan untuk eksekusi lapangan.
                    </p>
                </div>
            </div>

            {{-- Formulir Pengesahan Handover --}}
            <form action="{{ route('projects.confirm_handover_pmo', $project->id) }}" method="POST" class="space-y-3.5 text-xs">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Pilihan Project Manager Definitif --}}
                    <div>
                        <label class="block font-bold text-slate-700 text-[11px] mb-1">
                            PENETAPAN PROJECT MANAGER (PMO) <span class="text-[#8F0A0D]">*</span>
                        </label>
                        <select name="pm_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-900 bg-white font-bold hover:border-slate-400 focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 cursor-pointer transition">
                            <option value="">-- Pilih Project Manager yang Bertugas --</option>
                            @foreach($pmoUsersList as $pmo)
                                <option value="{{ $pmo->id }}" {{ ($project->pm_id == $pmo->id || (empty($project->pm_id) && str_contains(strtolower($pmo->name), 'rizki'))) ? 'selected' : '' }}>
                                    {{ $pmo->name }} ({{ $pmo->email }})
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

                    {{-- Catatan Instruksi Khusus Serah Terima --}}
                    <div>
                        <label class="block font-bold text-slate-700 text-[11px] mb-1">
                            CATATAN &amp; INSTRUKSI SERAH TERIMA KE PMO
                        </label>
                        <input type="text" name="handover_notes" placeholder="Contoh: Perhatikan SLA Uptime klien, instalasi dimulai H+3 setelah PO."
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-900 bg-white hover:border-slate-400 focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition">
                    </div>
                </div>

                <div class="pt-2 flex items-center justify-between flex-wrap gap-3">
                    <p class="text-[11px] text-slate-500 italic">
                        Pengesahan ini akan secara resmi mengaktifkan fase Deliver/In Progress dan mengirimkan disposisi tugas ke Project Manager.
                    </p>
                    @if($canConfirmHandover)
                        <button type="submit" 
                                onclick="return confirm('Apakah Anda yakin ingin mengesahkan serah terima proyek ini ke PMO? Proyek akan resmi beralih ke fase eksekusi lapangan.')"
                                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold btn-ipnet-primary transition cursor-pointer shadow-sm active:scale-95">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Sahkan &amp; Serah Terimakan ke PMO</span>
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
        {{-- KONDISI 3: Proyek Sudah Resmi Diserahterimakan ke PMO --}}
        <div class="p-4 sm:p-5 rounded-xl bg-gradient-to-r from-emerald-50/70 via-white to-emerald-50/40 border border-emerald-200 text-xs space-y-3">
            <div class="flex items-center justify-between flex-wrap gap-2 border-b border-emerald-100 pb-2.5">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-emerald-600 text-white flex items-center justify-center font-bold text-xs">
                        ✓
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-900 text-xs">
                            Proyek Resmi Berjalan di PMO &amp; Tim Operasional Lapangan
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
                    <span class="text-[10px] uppercase font-bold text-slate-400 block">PROJECT MANAGER (PMO)</span>
                    <strong class="text-slate-900 text-xs">{{ $project->pm ? $project->pm->name : 'PMO Delivery' }}</strong>
                </div>
                <div class="p-2.5 rounded-lg bg-white border border-emerald-100">
                    <span class="text-[10px] uppercase font-bold text-slate-400 block">NOMOR LEGALITAS PO/SPK</span>
                    <strong class="text-slate-900 text-xs">{{ $project->po_spk_number ?: '(PO Tercatat)' }}</strong>
                </div>
                <div class="p-2.5 rounded-lg bg-white border border-emerald-100">
                    <span class="text-[10px] uppercase font-bold text-slate-400 block">SKEMA EKSEKUSI</span>
                    <strong class="text-slate-900 text-xs">
                        @if($isBoth)
                            Dual Scope (Fase 1 Delivery &rarr; Fase 2 MS)
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
