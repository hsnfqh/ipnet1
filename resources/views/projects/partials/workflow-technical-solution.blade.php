{{-- KOLABORASI TIM SOLUSI TEKNIS (PIC BD, PRE-SALES & SOLUTION ARCHITECT) --}}
@php
    $isAnyApproved = $isAnyApproved ?? (!empty($headApproval['approved']) || !empty($directorApproval['approved']));
    $isBdApproved = $isBdApproved ?? ((($bdVerification['status'] ?? '') === 'Approved'));
    $isBdmAssigned = $isBdmAssigned ?? (!empty($project->bdm_id) || !empty($bdmAssignment['assigned']));
    $bdmName = $bdmName ?? ($project->bdm->name ?? ($bdmAssignment['assigned_to'] ?? null));
    $isPresalesAssigned = $isPresalesAssigned ?? (!empty($presalesAssignment['assigned']));
    $isArchitectAssigned = $isArchitectAssigned ?? (!empty($architectAssignment['assigned']));
    $isPresalesDone = $isPresalesDone ?? (!empty($presalesAssignment['document_path']));
    $isArchitectDone = $isArchitectDone ?? (!empty($architectAssignment['document_path']));
    $canVerifyBD = $canVerifyBD ?? false;
    $canUploadPresales = $canUploadPresales ?? false;
    $canUploadArchitect = $canUploadArchitect ?? false;
@endphp
<div class="ipnet-card p-6 space-y-5">
    {{-- Header & Status --}}
    <div class="flex items-center justify-between gap-3 border-b border-slate-100 pb-3">
        <div class="min-w-0">
            <p class="text-[#8F0A0D] text-[11px] font-bold inline-flex items-center uppercase tracking-wider mb-0.5">
                <span class="w-1.5 h-1.5 rounded-full bg-[#8F0A0D] inline-block mr-1.5"></span> TIM SOLUSI TEKNIS
            </p>
            <h3 class="text-sm sm:text-base font-bold text-slate-900">Kolaborasi Tim Solusi Teknis (BD, Pre-Sales &amp; Solution Architect)</h3>
            <p class="text-xs text-slate-500 mt-0.5">Alur verifikasi kelayakan teknis, penyusunan proposal lingkup kerja (SOW), dan perancangan arsitektur solusi.</p>
        </div>
        @if(!$isAnyApproved)
            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-800 border border-amber-200 flex items-center gap-1.5 shadow-2xs shrink-0 whitespace-nowrap">
                <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Menunggu Persetujuan Pimpinan</span>
            </span>
        @elseif(($bdVerification['status'] ?? '') === 'Approved')
            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center gap-1.5 shadow-2xs shrink-0 whitespace-nowrap">
                <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                <span>Solusi Disetujui PIC BD</span>
            </span>
        @endif
    </div>

    {{-- 3 Collaborative Person Workspace Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs items-stretch">
        
        {{-- ══ CARD 1: BUSINESS DEVELOPMENT (PIC / PM) ══ --}}
        <div class="p-4 rounded-xl bg-white border border-slate-200 shadow-2xs flex flex-col justify-between space-y-3.5 hover:border-slate-300 transition">
            <div class="space-y-3">
                
                {{-- Header --}}
                <div class="flex items-center justify-between gap-1 pb-2 border-b border-slate-100">
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200 uppercase tracking-wider whitespace-nowrap">
                        BUSINESS DEVELOPMENT
                    </span>
                    @php
                        $bdBadgeClass = match($bdVerification['status'] ?? '') {
                            'Approved'            => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                            'Revision Needed'     => 'bg-rose-50 text-rose-700 border-rose-200',
                            'Pending Verification'=> 'bg-amber-50 text-amber-800 border-amber-300',
                            'Waiting Uploads'     => 'bg-slate-100 text-slate-700 border-slate-200',
                            default               => 'bg-slate-100 text-slate-500 border-slate-200',
                        };
                        $bdBadgeLabel = match($bdVerification['status'] ?? '') {
                            'Approved'            => '✓ Disetujui BD',
                            'Revision Needed'     => '⚠ Perlu Revisi',
                            'Pending Verification'=> 'Menunggu Verifikasi',
                            'Waiting Uploads'     => 'Menunggu Dokumen',
                            default               => 'Belum Ditugaskan',
                        };
                    @endphp
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border whitespace-nowrap {{ $bdBadgeClass }}">
                        {{ $bdBadgeLabel }}
                    </span>
                </div>

                {{-- Person --}}
                <div class="flex items-center gap-2.5">
                    @if($isBdmAssigned && !empty($bdmName))
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-[#8F0A0D] to-[#BA1B1D] text-white font-extrabold text-xs flex items-center justify-center shrink-0 shadow-2xs">
                            {{ strtoupper(substr($bdmName, 0, 2)) }}
                        </div>
                    @else
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-[#8F0A0D] to-[#BA1B1D] text-white flex items-center justify-center shrink-0 shadow-2xs" title="Belum Ditugaskan">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                    @endif
                    <div class="min-w-0 flex-1">
                        <h4 class="font-bold text-xs {{ $isBdmAssigned ? 'text-slate-900' : 'text-slate-400 italic' }} truncate" title="{{ $isBdmAssigned && !empty($bdmName) ? $bdmName : '' }}">
                            {{ $isBdmAssigned ? $bdmName : 'Belum Ditugaskan' }}
                        </h4>
                        <p class="text-[10.5px] text-slate-500 truncate">Verifikator Kelayakan &amp; Solusi Teknis</p>
                    </div>
                </div>

                {{-- Verification Status Box --}}
                <div>
                    @if(($bdVerification['status'] ?? '') === 'Approved')
                        <div class="p-3 rounded-lg bg-slate-50 border border-slate-200/80 space-y-2">
                            <div class="flex items-center gap-2 min-w-0">
                                <div class="w-7 h-7 rounded-lg bg-red-50 text-[#8F0A0D] flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="font-bold text-slate-900 text-xs truncate">Solusi Disetujui</div>
                                    <div class="text-[10.5px] text-slate-500 truncate">{{ $bdVerification['notes'] ?? 'Verifikasi teknis dinyatakan lengkap & valid' }}</div>
                                </div>
                            </div>
                            @if(!empty($bdVerification['verified_at']))
                                <div class="text-[10px] text-slate-400 pt-1.5 border-t border-slate-200/80 text-right font-mono">
                                    {{ $bdVerification['verified_at'] }}
                                </div>
                            @endif
                        </div>
                    @elseif(($bdVerification['status'] ?? '') === 'Revision Needed')
                        <div class="p-3 rounded-lg bg-rose-50/70 border border-rose-200 text-rose-900 space-y-1.5">
                            <div class="flex items-center gap-2 min-w-0">
                                <div class="w-7 h-7 rounded-lg bg-rose-100 text-rose-700 flex items-center justify-center shrink-0">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="font-bold text-rose-900 text-xs">Catatan Revisi BD</div>
                                    <div class="text-[10.5px] text-rose-800 italic truncate">"{{ $bdVerification['notes'] ?? 'Mohon lakukan perbaikan dokumen teknis.' }}"</div>
                                </div>
                            </div>
                        </div>
                    @elseif(($bdVerification['status'] ?? '') === 'Pending Verification')
                        <div class="p-3 rounded-lg bg-amber-50/70 border border-amber-200 text-amber-900 space-y-1.5">
                            <div class="flex items-center gap-2 min-w-0">
                                <div class="w-7 h-7 rounded-lg bg-amber-100 text-amber-800 flex items-center justify-center shrink-0">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="font-bold text-amber-900 text-xs">Menunggu Verifikasi</div>
                                    <div class="text-[10.5px] text-amber-800 truncate">Dokumen siap ditelaah oleh PIC BD</div>
                                </div>
                            </div>
                        </div>
                    @elseif($isBdmAssigned)
                        <div class="p-3 rounded-lg bg-slate-50 border border-slate-200 text-slate-500 text-[10.5px] text-center">
                            Menunggu kelengkapan dokumen dari Pre-Sales &amp; Solution Architect.
                        </div>
                    @else
                        <div class="p-3 rounded-lg border border-dashed border-slate-200 text-slate-400 text-center text-[10.5px]">
                            Belum ada PIC Business Development yang ditugaskan.
                        </div>
                    @endif
                </div>

            </div>

            {{-- Actions Footer --}}
            @php
                $isBdApproved = (($bdVerification['status'] ?? '') === 'Approved');
                $showBdAssign = empty($isPresalesOrSaOnly) && ($canAssignSales ?? false) && !$isBdmAssigned;
                $showBdVerify = $canVerifyBD && ($isPresalesDone || $isArchitectDone) && !$isBdApproved;
            @endphp
            @if(!$isAnyApproved && !$isBdmAssigned && empty($isPresalesOrSaOnly) && ($canAssignSales ?? false))
                <div class="pt-2.5 border-t border-slate-100 flex items-center justify-between gap-2 mt-auto">
                    <button type="button" disabled class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-semibold text-slate-400 bg-slate-100 border border-slate-200 cursor-not-allowed opacity-80 shadow-2xs" title="Terkunci: Menunggu persetujuan pimpinan (Pak Susanto / Pak Hariyadi)">
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        <span>Tugaskan PIC BD</span>
                    </button>
                </div>
            @elseif($showBdAssign || $showBdVerify)
                <div class="pt-2.5 border-t border-slate-100 flex items-center justify-between gap-2 mt-auto">
                    @if($showBdAssign)
                        <button type="button" @click.stop="openAssignTechnicalModal('bdm')" onclick="event.stopPropagation(); window.openAssignTechnicalModalCustom('bdm')" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold text-white btn-ipnet-primary transition cursor-pointer shadow-xs active:scale-95">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                            <span>Tugaskan PIC BD</span>
                        </button>
                    @endif

                    @if($showBdVerify)
                        <button type="button" @click.stop="openVerifyTechnicalModal()" onclick="event.stopPropagation(); window.openModal('modal-verify-technical')" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold text-white btn-ipnet-primary transition cursor-pointer shadow-xs active:scale-95 ml-auto">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            <span>Verifikasi Dokumen Teknis</span>
                        </button>
                    @endif
                </div>
            @endif
        </div>

        {{-- ══ CARD 2: PRE-SALES SPECIALIST ══ --}}
        <div class="p-4 rounded-xl bg-white border border-slate-200 shadow-2xs flex flex-col justify-between space-y-3.5 hover:border-slate-300 transition">
            <div class="space-y-3">
                
                {{-- Header --}}
                <div class="flex items-center justify-between gap-1 pb-2 border-b border-slate-100">
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200 uppercase tracking-wider whitespace-nowrap">
                        PRE-SALES
                    </span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border whitespace-nowrap {{ $isPresalesDone ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : ($isPresalesAssigned ? 'bg-amber-50 text-amber-800 border-amber-200' : 'bg-slate-100 text-slate-500 border-slate-200') }}">
                        {{ $isPresalesDone ? '✓ Dokumen Diunggah' : ($isPresalesAssigned ? 'Menunggu Dokumen' : 'Belum Ditugaskan') }}
                    </span>
                </div>

                {{-- Person --}}
                <div class="flex items-center gap-2.5">
                    @if($isPresalesAssigned && !empty($presalesAssignment['assigned_to']))
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-[#8F0A0D] to-[#BA1B1D] text-white font-extrabold text-xs flex items-center justify-center shrink-0 shadow-2xs">
                            {{ strtoupper(substr($presalesAssignment['assigned_to'], 0, 2)) }}
                        </div>
                    @else
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-[#8F0A0D] to-[#BA1B1D] text-white flex items-center justify-center shrink-0 shadow-2xs" title="Belum Ditugaskan">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                    @endif
                    <div class="min-w-0 flex-1">
                        <h4 class="font-bold text-xs {{ $isPresalesAssigned && !empty($presalesAssignment['assigned_to']) ? 'text-slate-900' : 'text-slate-400 italic' }} truncate" title="{{ $isPresalesAssigned && !empty($presalesAssignment['assigned_to']) ? $presalesAssignment['assigned_to'] : '' }}">
                            {{ $isPresalesAssigned && !empty($presalesAssignment['assigned_to']) ? $presalesAssignment['assigned_to'] : 'Belum Ditugaskan' }}
                        </h4>
                        <p class="text-[10.5px] text-slate-500 truncate">Penyusunan Proposal Teknis &amp; BoQ</p>
                    </div>
                </div>

                {{-- Document Deliverable Box --}}
                <div>
                    @if($isPresalesDone)
                        <div class="p-3 rounded-lg bg-slate-50 border border-slate-200/80 space-y-2">
                            <div class="flex items-center justify-between gap-2">
                                <div class="flex items-center gap-2 min-w-0 flex-1">
                                    <div class="w-7 h-7 rounded-lg bg-red-50 text-[#8F0A0D] flex items-center justify-center shrink-0">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="font-bold text-slate-900 text-xs truncate" title="{{ $presalesAssignment['document_title'] ?? 'Proposal' }}">
                                             {{ \Illuminate\Support\Str::limit($presalesAssignment['document_title'] ?? 'Proposal', 10, '...') }}
                                        </div>
                                        <div class="text-[10px] text-slate-500 font-mono truncate" title="{{ $presalesAssignment['document_name'] ?? 'jurnal.pdf' }}">
                                             {{ $presalesAssignment['document_name'] ?? 'jurnal.pdf' }}
                                        </div>
                                    </div>
                                </div>
                                @if(!empty($presalesAssignment['document_path']))
                                    <a href="{{ asset('storage/' . $presalesAssignment['document_path']) }}" target="_blank" 
                                       class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-bold text-[#8F0A0D] bg-red-50 hover:bg-red-100 border border-red-200 transition shrink-0 shadow-2xs" title="Unduh Proposal">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                        <span>Unduh</span>
                                    </a>
                                @endif
                            </div>
                            @if(!empty($presalesAssignment['completed_at']))
                                <div class="text-[10px] text-slate-400 pt-1.5 border-t border-slate-200/80 text-right font-mono">
                                    {{ $presalesAssignment['completed_at'] }}
                                </div>
                            @endif
                        </div>
                    @elseif($isPresalesAssigned)
                        <div class="p-3 rounded-lg bg-amber-50/70 border border-amber-200 text-amber-900 space-y-1">
                            <div class="font-medium italic text-[11px]">"{{ !empty($presalesAssignment['sales_notes']) ? $presalesAssignment['sales_notes'] : 'Mohon disusun proposal penawaran teknis dan rincian BoQ proyek.' }}"</div>
                            <div class="text-[10px] text-amber-700 mt-1 font-mono">Ditugaskan: {{ $presalesAssignment['assigned_at'] ?? '-' }}</div>
                        </div>
                    @else
                        <div class="p-3 rounded-lg border border-dashed border-slate-200 text-slate-400 text-center text-[10.5px]">
                            Belum ada personel Pre-Sales yang ditugaskan.
                        </div>
                    @endif
                </div>
            </div>

            {{-- Actions Footer --}}
            @php
                $showPresalesAssign = ($canAssignSales ?? false) && !$isPresalesAssigned;
                $showPresalesUploadRevision = $isPresalesAssigned && ($bdVerification['status'] ?? '') === 'Revision Needed' && $canUploadPresales;
                $showPresalesUpload = $isPresalesAssigned && !$isPresalesDone && $canUploadPresales && !$showPresalesUploadRevision;
                $showPresalesReupload = $isPresalesAssigned && $isPresalesDone && $canUploadPresales && !$showPresalesUploadRevision && !$isBdApproved;
            @endphp
            @if(!$isAnyApproved && !$isPresalesAssigned && ($canAssignSales ?? false))
                <div class="pt-2.5 border-t border-slate-100 flex items-center justify-between gap-2 mt-auto">
                    <button type="button" disabled class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-semibold text-slate-400 bg-slate-100 border border-slate-200 cursor-not-allowed opacity-80 shadow-2xs" title="Terkunci: Menunggu persetujuan pimpinan (Pak Susanto / Pak Hariyadi)">
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        <span>Tugaskan Pre-Sales</span>
                    </button>
                </div>
            @elseif($showPresalesAssign || $showPresalesUploadRevision || $showPresalesUpload || $showPresalesReupload)
                <div class="pt-2.5 border-t border-slate-100 flex items-center justify-between gap-2 mt-auto">
                    @if($showPresalesAssign)
                        <button type="button" @click.stop="openAssignTechnicalModal('presales')" onclick="event.stopPropagation(); window.openAssignTechnicalModalCustom('presales')" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold text-white btn-ipnet-primary transition cursor-pointer shadow-xs active:scale-95">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                            <span>Tugaskan Pre-Sales</span>
                        </button>
                    @endif

                    @if($showPresalesUploadRevision)
                        <button type="button" @click.stop="openUploadTechnicalModal('presales')" onclick="event.stopPropagation(); window.openUploadTechnicalModalCustom('presales')" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold text-white btn-ipnet-primary transition cursor-pointer shadow-xs active:scale-95">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                            <span>Unggah Revisi Dokumen</span>
                        </button>
                    @elseif($showPresalesUpload)
                        <button type="button" @click.stop="openUploadTechnicalModal('presales')" onclick="event.stopPropagation(); window.openUploadTechnicalModalCustom('presales')" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold text-white btn-ipnet-primary transition cursor-pointer shadow-xs active:scale-95">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                            <span>Unggah Dokumen Proposal</span>
                        </button>
                    @elseif($showPresalesReupload)
                        <button type="button" @click.stop="openUploadTechnicalModal('presales')" onclick="event.stopPropagation(); window.openUploadTechnicalModalCustom('presales')" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold text-white btn-ipnet-primary transition cursor-pointer shadow-xs active:scale-95">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                            <span>Unggah Ulang Dokumen</span>
                        </button>
                    @endif
                </div>
            @endif
        </div>

        {{-- ══ CARD 3: SOLUTION ARCHITECT (SA) ══ --}}
        <div class="p-4 rounded-xl bg-white border border-slate-200 shadow-2xs flex flex-col justify-between space-y-3.5 hover:border-slate-300 transition">
            <div class="space-y-3">
                
                {{-- Header --}}
                <div class="flex items-center justify-between gap-1 pb-2 border-b border-slate-100">
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200 uppercase tracking-wider whitespace-nowrap">
                        SOLUTION ARCHITECT
                    </span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border whitespace-nowrap {{ $isArchitectDone ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : ($isArchitectAssigned ? 'bg-amber-50 text-amber-800 border-amber-200' : 'bg-slate-100 text-slate-500 border-slate-200') }}">
                        {{ $isArchitectDone ? '✓ Dokumen Diunggah' : ($isArchitectAssigned ? 'Menunggu Dokumen' : 'Belum Ditugaskan') }}
                    </span>
                </div>

                {{-- Person --}}
                <div class="flex items-center gap-2.5">
                    @if($isArchitectAssigned && !empty($architectAssignment['assigned_to']))
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-[#8F0A0D] to-[#BA1B1D] text-white font-extrabold text-xs flex items-center justify-center shrink-0 shadow-2xs">
                            {{ strtoupper(substr($architectAssignment['assigned_to'], 0, 2)) }}
                        </div>
                    @else
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-[#8F0A0D] to-[#BA1B1D] text-white flex items-center justify-center shrink-0 shadow-2xs" title="Belum Ditugaskan">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                    @endif
                    <div class="min-w-0 flex-1">
                        <h4 class="font-bold text-xs {{ $isArchitectAssigned && !empty($architectAssignment['assigned_to']) ? 'text-slate-900' : 'text-slate-400 italic' }} truncate" title="{{ $isArchitectAssigned && !empty($architectAssignment['assigned_to']) ? $architectAssignment['assigned_to'] : '' }}">
                            {{ $isArchitectAssigned && !empty($architectAssignment['assigned_to']) ? $architectAssignment['assigned_to'] : 'Belum Ditugaskan' }}
                        </h4>
                        <p class="text-[10.5px] text-slate-500 truncate">Perancangan Arsitektur &amp; Topologi Sistem</p>
                    </div>
                </div>

                {{-- Document Deliverable Box --}}
                <div>
                    @if($isArchitectDone)
                        <div class="p-3 rounded-lg bg-slate-50 border border-slate-200/80 space-y-2">
                            <div class="flex items-center justify-between gap-2">
                                <div class="flex items-center gap-2 min-w-0 flex-1">
                                    <div class="w-7 h-7 rounded-lg bg-red-50 text-[#8F0A0D] flex items-center justify-center shrink-0">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="font-bold text-slate-900 text-xs truncate" title="{{ $architectAssignment['document_title'] ?? 'Desain Topologi' }}">
                                             {{ \Illuminate\Support\Str::limit($architectAssignment['document_title'] ?? 'Desain Topologi', 10, '...') }}
                                        </div>
                                        <div class="text-[10px] text-slate-500 font-mono truncate" title="{{ $architectAssignment['document_name'] ?? 'jurnal.pdf' }}">
                                             {{ $architectAssignment['document_name'] ?? 'jurnal.pdf' }}
                                        </div>
                                    </div>
                                </div>
                                @if(!empty($architectAssignment['document_path']))
                                    <a href="{{ asset('storage/' . $architectAssignment['document_path']) }}" target="_blank" 
                                       class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-bold text-[#8F0A0D] bg-red-50 hover:bg-red-100 border border-red-200 transition shrink-0 shadow-2xs" title="Unduh Desain">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                        <span>Unduh</span>
                                    </a>
                                @endif
                            </div>
                            @if(!empty($architectAssignment['completed_at']))
                                <div class="text-[10px] text-slate-400 pt-1.5 border-t border-slate-200/80 text-right font-mono">
                                    {{ $architectAssignment['completed_at'] }}
                                </div>
                            @endif
                        </div>
                    @elseif($isArchitectAssigned)
                        <div class="p-3 rounded-lg bg-amber-50/70 border border-amber-200 text-amber-900 space-y-1">
                            <div class="font-medium italic text-[11px]">"{{ !empty($architectAssignment['sales_notes']) ? $architectAssignment['sales_notes'] : 'Mohon dirancang arsitektur topologi sistem dan spesifikasi teknis.' }}"</div>
                            <div class="text-[10px] text-amber-700 mt-1 font-mono">Ditugaskan: {{ $architectAssignment['assigned_at'] ?? '-' }}</div>
                        </div>
                    @else
                        <div class="p-3 rounded-lg border border-dashed border-slate-200 text-slate-400 text-center text-[10.5px]">
                            Belum ada Solution Architect yang ditugaskan.
                        </div>
                    @endif
                </div>

            </div>

            {{-- Actions Footer --}}
            @php
                $showArchitectAssign = ($canAssignSales ?? false) && !$isArchitectAssigned;
                $showArchitectUploadRevision = $isArchitectAssigned && ($bdVerification['status'] ?? '') === 'Revision Needed' && $canUploadArchitect;
                $showArchitectUpload = $isArchitectAssigned && !$isArchitectDone && $canUploadArchitect && !$showArchitectUploadRevision;
                $showArchitectReupload = $isArchitectAssigned && $isArchitectDone && $canUploadArchitect && !$showArchitectUploadRevision && !$isBdApproved;
            @endphp
            @if(!$isAnyApproved && !$isArchitectAssigned && ($canAssignSales ?? false))
                <div class="pt-2.5 border-t border-slate-100 flex items-center justify-between gap-2 mt-auto">
                    <button type="button" disabled class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-semibold text-slate-400 bg-slate-100 border border-slate-200 cursor-not-allowed opacity-80 shadow-2xs" title="Terkunci: Menunggu persetujuan pimpinan (Pak Susanto / Pak Hariyadi)">
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        <span>Tugaskan Solution Architect</span>
                    </button>
                </div>
            @elseif($showArchitectAssign || $showArchitectUploadRevision || $showArchitectUpload || $showArchitectReupload)
                <div class="pt-2.5 border-t border-slate-100 flex items-center justify-between gap-2 mt-auto">
                    @if($showArchitectAssign)
                        <button type="button" @click.stop="openAssignTechnicalModal('architect')" onclick="event.stopPropagation(); window.openAssignTechnicalModalCustom('architect')" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold text-white btn-ipnet-primary transition cursor-pointer shadow-xs active:scale-95">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                            <span>Tugaskan Solution Architect</span>
                        </button>
                    @endif

                    @if($showArchitectUploadRevision)
                        <button type="button" @click.stop="openUploadTechnicalModal('architect')" onclick="event.stopPropagation(); window.openUploadTechnicalModalCustom('architect')" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold text-white btn-ipnet-primary transition cursor-pointer shadow-xs active:scale-95">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                            <span>Unggah Revisi Dokumen Solusi</span>
                        </button>
                    @elseif($showArchitectUpload)
                        <button type="button" @click.stop="openUploadTechnicalModal('architect')" onclick="event.stopPropagation(); window.openUploadTechnicalModalCustom('architect')" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold text-white btn-ipnet-primary transition cursor-pointer shadow-xs active:scale-95">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                            <span>Unggah Dokumen Solusi</span>
                        </button>
                    @elseif($showArchitectReupload)
                        <button type="button" @click.stop="openUploadTechnicalModal('architect')" onclick="event.stopPropagation(); window.openUploadTechnicalModalCustom('architect')" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold text-white btn-ipnet-primary transition cursor-pointer shadow-xs active:scale-95">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                            <span>Unggah Ulang Dokumen</span>
                        </button>
                    @endif
                </div>
            @endif
        </div>

    </div>
</div>
