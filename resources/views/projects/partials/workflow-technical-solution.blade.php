{{-- KOLABORASI TIM SOLUSI TEKNIS (PIC BD, PRE-SALES & SOLUTION ARCHITECT) --}}
@php
    $isBdApproved = $isBdApproved ?? ((($bdVerification['status'] ?? '') === 'Approved'));
    $isBdmAssigned = $isBdmAssigned ?? (!empty($project->bdm_id) || !empty($bdmAssignment['assigned']));
    $bdmName = $bdmName ?? ($project->bdm->name ?? ($bdmAssignment['assigned_to'] ?? null));
    $isPresalesAssigned = $isPresalesAssigned ?? (!empty($presalesAssignment['assigned']));
    $isArchitectAssigned = $isArchitectAssigned ?? (!empty($architectAssignment['assigned']));
    $isPresalesDone = $isPresalesDone ?? (!empty($presalesAssignment['document_path']) || ($presalesAssignment['status'] ?? '') === 'Completed');
    $isArchitectDone = $isArchitectDone ?? (!empty($architectAssignment['document_path']) || ($architectAssignment['status'] ?? '') === 'Completed');
    $canVerifyBD = $canVerifyBD ?? false;
    $canUploadPresales = $canUploadPresales ?? false;
    $canUploadArchitect = $canUploadArchitect ?? false;
@endphp
<div class="ipnet-card p-6 space-y-5">
    {{-- Header & Status --}}
    <div class="flex items-center justify-between gap-3 border-b border-slate-100 pb-3 flex-wrap">
        <div>
            <p class="text-[#8F0A0D] text-[11px] font-bold inline-flex items-center uppercase tracking-wider mb-1">
                <span class="w-1.5 h-1.5 rounded-full bg-[#8F0A0D] inline-block mr-1.5"></span> TIM SOLUSI TEKNIS
            </p>
            <h3 class="text-sm sm:text-base font-bold text-slate-900">Kolaborasi Tim Solusi (BD, Pre-Sales &amp; Solution Architect)</h3>
            <p class="text-xs text-slate-500 mt-0.5">Workflow verifikasi kelayakan teknis, proposal SOW &amp; desain topologi arsitektur.</p>
        </div>
        @if(($bdVerification['status'] ?? '') === 'Approved')
            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center gap-1.5 shadow-2xs">
                <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                <span>Solusi Disahkan BD</span>
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
                        PIC BD
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
                            'Waiting Uploads'     => 'Menunggu Berkas',
                            default               => 'Belum Di-assign',
                        };
                    @endphp
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border whitespace-nowrap {{ $bdBadgeClass }}">
                        {{ $bdBadgeLabel }}
                    </span>
                </div>

                {{-- Person --}}
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-[#8F0A0D] to-[#BA1B1D] text-white font-extrabold text-xs flex items-center justify-center shrink-0 shadow-2xs">
                        {{ $isBdmAssigned && !empty($bdmName) ? strtoupper(substr($bdmName, 0, 2)) : 'BD' }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <h4 class="font-bold text-xs {{ $isBdmAssigned ? 'text-slate-900' : 'text-slate-400 italic' }} truncate" title="{{ $isBdmAssigned && !empty($bdmName) ? $bdmName : '' }}">
                            {{ $isBdmAssigned ? $bdmName : 'Belum Ditunjuk' }}
                        </h4>
                        <p class="text-[10.5px] text-slate-500 truncate">Verifikator Kelayakan &amp; Scope</p>
                    </div>
                </div>

                {{-- Verification Status Box --}}
                <div>
                    @if(($bdVerification['status'] ?? '') === 'Approved')
                        <div class="p-3 rounded-lg bg-emerald-50/70 border border-emerald-200 text-emerald-900 space-y-1">
                            <div class="font-bold text-[11px] text-emerald-800">✓ Dokumen Disetujui</div>
                            <p class="text-[10.5px] text-emerald-800 leading-relaxed italic">"{{ $bdVerification['notes'] ?? 'Proposal & Desain Arsitektur telah diverifikasi dan disetujui.' }}"</p>
                            @if(!empty($bdVerification['verified_at']))
                                <div class="text-[9.5px] text-emerald-700 font-mono pt-1 border-t border-emerald-200/60">{{ $bdVerification['verified_at'] }}</div>
                            @endif
                        </div>
                    @elseif(($bdVerification['status'] ?? '') === 'Revision Needed')
                        <div class="p-3 rounded-lg bg-rose-50/70 border border-rose-200 text-rose-900 space-y-1">
                            <div class="font-bold text-[11px] text-rose-800">Catatan Revisi:</div>
                            <div class="text-[11px] text-rose-950 bg-white/90 p-2 rounded border border-rose-100 italic">
                                "{{ $bdVerification['notes'] ?? 'Mohon lakukan perbaikan.' }}"
                            </div>
                        </div>
                    @elseif(($bdVerification['status'] ?? '') === 'Pending Verification')
                        <div class="p-3 rounded-lg bg-amber-50/70 border border-amber-200 text-amber-900 space-y-1">
                            <div class="font-bold text-[11px] text-amber-800">Menunggu Verifikasi</div>
                            <p class="text-[10.5px] text-amber-800 leading-relaxed">Berkas telah siap untuk ditinjau oleh PIC BD.</p>
                        </div>
                    @elseif($isBdmAssigned)
                        <div class="p-3 rounded-lg bg-slate-50 border border-slate-200 text-slate-500 text-[10.5px]">
                            Menunggu upload dari Pre-Sales &amp; SA.
                        </div>
                    @else
                        <div class="p-3 rounded-lg border border-dashed border-slate-200 text-slate-400 text-center text-[10.5px]">
                            Belum ada PIC BD yang ditunjuk.
                        </div>
                    @endif
                </div>

            </div>

            {{-- Actions Footer --}}
            @if(!$isBdmAssigned || ($canVerifyBD && ($isPresalesDone || $isArchitectDone)))
                <div class="pt-2.5 border-t border-slate-100 flex items-center justify-between gap-2 mt-auto">
                    @if(!$isBdmAssigned)
                        <button type="button" @click="openAssignTechnicalModal('bdm')" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-[#8F0A0D] bg-red-50 hover:bg-red-100 border border-red-200 transition cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            <span>Tunjuk PIC BD</span>
                        </button>
                    @endif

                    @if($canVerifyBD && ($isPresalesDone || $isArchitectDone))
                        <button type="button" @click="openVerifyTechnicalModal()" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-bold text-white btn-ipnet-primary shadow-xs cursor-pointer ml-auto">
                            <span>Verifikasi Dokumen</span>
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
                        {{ $isPresalesDone ? '✓ Berkas Diunggah' : ($isPresalesAssigned ? 'Menunggu Proposal' : 'Belum Di-assign') }}
                    </span>
                </div>

                {{-- Person --}}
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-[#8F0A0D] to-[#BA1B1D] text-white font-extrabold text-xs flex items-center justify-center shrink-0 shadow-2xs">
                        {{ $isPresalesAssigned && !empty($presalesAssignment['assigned_to']) ? strtoupper(substr($presalesAssignment['assigned_to'], 0, 2)) : 'PS' }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <h4 class="font-bold text-xs {{ $isPresalesAssigned && !empty($presalesAssignment['assigned_to']) ? 'text-slate-900' : 'text-slate-400 italic' }} truncate" title="{{ $isPresalesAssigned && !empty($presalesAssignment['assigned_to']) ? $presalesAssignment['assigned_to'] : '' }}">
                            {{ $isPresalesAssigned && !empty($presalesAssignment['assigned_to']) ? $presalesAssignment['assigned_to'] : 'Belum Ditugaskan' }}
                        </h4>
                        <p class="text-[10.5px] text-slate-500 truncate">Proposal &amp; BoQ Proyek</p>
                    </div>
                </div>

                {{-- Document Deliverable Box --}}
                <div>
                    @if($isPresalesDone)
                        <div class="p-3 rounded-lg bg-slate-50 border border-slate-200 space-y-2">
                            <div class="flex items-center justify-between gap-2">
                                <div class="flex items-center gap-2 min-w-0 flex-1">
                                    <div class="w-7 h-7 rounded-lg bg-red-50 text-[#8F0A0D] flex items-center justify-center shrink-0">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="font-bold text-slate-900 text-xs truncate" title="{{ $presalesAssignment['document_title'] ?? 'Proposal Teknis' }}">{{ $presalesAssignment['document_title'] ?? 'Proposal Teknis' }}</div>
                                        <div class="text-[10px] text-slate-500 font-mono truncate" title="{{ $presalesAssignment['document_name'] ?? 'Berkas terlampir' }}">{{ $presalesAssignment['document_name'] ?? 'Berkas terlampir' }}</div>
                                    </div>
                                </div>
                                @if(!empty($presalesAssignment['document_path']))
                                    <a href="{{ asset('storage/' . $presalesAssignment['document_path']) }}" target="_blank" 
                                       class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-bold text-[#8F0A0D] bg-red-50 hover:bg-red-100 border border-red-200 transition shrink-0 shadow-2xs" title="Unduh Proposal">
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
                            <div class="font-medium italic text-[11px]">"{{ !empty($presalesAssignment['sales_notes']) ? $presalesAssignment['sales_notes'] : 'Mohon disusun proposal penawaran.' }}"</div>
                            <div class="text-[10px] text-amber-700 mt-1">Ditugaskan: {{ $presalesAssignment['assigned_at'] ?? '-' }}</div>
                        </div>
                    @else
                        <div class="p-3 rounded-lg border border-dashed border-slate-200 text-slate-400 text-center text-[10.5px]">
                            Belum ada penugasan Pre-Sales.
                        </div>
                    @endif
                </div>

            </div>

            {{-- Actions Footer --}}
            @if(!$isPresalesAssigned)
                <div class="pt-2.5 border-t border-slate-100 flex items-center justify-between gap-2 mt-auto">
                    <button type="button" @click="openAssignTechnicalModal('presales')" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-[#8F0A0D] bg-red-50 hover:bg-red-100 border border-red-200 transition cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Tugaskan Pre-Sales</span>
                    </button>
                </div>
            @else
                <div class="pt-2.5 border-t border-slate-100 flex items-center justify-between gap-2 mt-auto flex-wrap">
                    @if($canUploadPresales)
                        <button type="button" @click="openUploadTechnicalModal('presales')" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-[#8F0A0D] bg-red-50 hover:bg-red-100 border border-red-200 transition cursor-pointer shadow-2xs">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                            <span>{{ $isPresalesDone ? 'Ubah Proposal' : 'Unggah Proposal' }}</span>
                        </button>
                    @endif
                    <button type="button" @click="openAssignTechnicalModal('presales')" class="text-xs font-semibold text-slate-500 hover:text-[#8F0A0D] cursor-pointer ml-auto">
                        Ubah Penugasan
                    </button>
                </div>
            @endif
        </div>

        {{-- ══ CARD 3: SOLUTION ARCHITECT (SA) ══ --}}
        <div class="p-4 rounded-xl bg-white border border-slate-200 shadow-2xs flex flex-col justify-between space-y-3.5 hover:border-slate-300 transition">
            <div class="space-y-3">
                
                {{-- Header --}}
                <div class="flex items-center justify-between gap-1 pb-2 border-b border-slate-100">
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200 uppercase tracking-wider whitespace-nowrap">
                        SOLUTION ARCHITECT
                    </span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border whitespace-nowrap {{ $isArchitectDone ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : ($isArchitectAssigned ? 'bg-amber-50 text-amber-800 border-amber-200' : 'bg-slate-100 text-slate-500 border-slate-200') }}">
                        {{ $isArchitectDone ? '✓ Desain Diunggah' : ($isArchitectAssigned ? 'Menunggu Topologi' : 'Belum Di-assign') }}
                    </span>
                </div>

                {{-- Person --}}
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-[#8F0A0D] to-[#BA1B1D] text-white font-extrabold text-xs flex items-center justify-center shrink-0 shadow-2xs">
                        {{ $isArchitectAssigned && !empty($architectAssignment['assigned_to']) ? strtoupper(substr($architectAssignment['assigned_to'], 0, 2)) : 'SA' }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <h4 class="font-bold text-xs {{ $isArchitectAssigned && !empty($architectAssignment['assigned_to']) ? 'text-slate-900' : 'text-slate-400 italic' }} truncate" title="{{ $isArchitectAssigned && !empty($architectAssignment['assigned_to']) ? $architectAssignment['assigned_to'] : '' }}">
                            {{ $isArchitectAssigned && !empty($architectAssignment['assigned_to']) ? $architectAssignment['assigned_to'] : 'Belum Ditugaskan' }}
                        </h4>
                        <p class="text-[10.5px] text-slate-500 truncate">Topologi Arsitektur &amp; Spek</p>
                    </div>
                </div>

                {{-- Document Deliverable Box --}}
                <div>
                    @if($isArchitectDone)
                        <div class="p-3 rounded-lg bg-slate-50 border border-slate-200 space-y-2">
                            <div class="flex items-center justify-between gap-2">
                                <div class="flex items-center gap-2 min-w-0 flex-1">
                                    <div class="w-7 h-7 rounded-lg bg-red-50 text-[#8F0A0D] flex items-center justify-center shrink-0">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/></svg>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="font-bold text-slate-900 text-xs truncate" title="{{ $architectAssignment['document_title'] ?? 'Desain Arsitektur' }}">{{ $architectAssignment['document_title'] ?? 'Desain Arsitektur' }}</div>
                                        <div class="text-[10px] text-slate-500 font-mono truncate" title="{{ $architectAssignment['document_name'] ?? 'Berkas terlampir' }}">{{ $architectAssignment['document_name'] ?? 'Berkas terlampir' }}</div>
                                    </div>
                                </div>
                                @if(!empty($architectAssignment['document_path']))
                                    <a href="{{ asset('storage/' . $architectAssignment['document_path']) }}" target="_blank" 
                                       class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-bold text-[#8F0A0D] bg-red-50 hover:bg-red-100 border border-red-200 transition shrink-0 shadow-2xs" title="Unduh Desain">
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
                            <div class="font-medium italic text-[11px]">"{{ !empty($architectAssignment['sales_notes']) ? $architectAssignment['sales_notes'] : 'Mohon dirancang topologi sistem & spesifikasi teknis.' }}"</div>
                            <div class="text-[10px] text-amber-700 mt-1">Ditugaskan: {{ $architectAssignment['assigned_at'] ?? '-' }}</div>
                        </div>
                    @else
                        <div class="p-3 rounded-lg border border-dashed border-slate-200 text-slate-400 text-center text-[10.5px]">
                            Belum ada penugasan Solution Architect.
                        </div>
                    @endif
                </div>

            </div>

            {{-- Actions Footer --}}
            @if(!$isArchitectAssigned)
                <div class="pt-2.5 border-t border-slate-100 flex items-center justify-between gap-2 mt-auto">
                    <button type="button" @click="openAssignTechnicalModal('architect')" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-[#8F0A0D] bg-red-50 hover:bg-red-100 border border-red-200 transition cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Tugaskan SA</span>
                    </button>
                </div>
            @else
                <div class="pt-2.5 border-t border-slate-100 flex items-center justify-between gap-2 mt-auto flex-wrap">
                    @if($canUploadArchitect)
                        <button type="button" @click="openUploadTechnicalModal('architect')" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-[#8F0A0D] bg-red-50 hover:bg-red-100 border border-red-200 transition cursor-pointer shadow-2xs">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                            <span>{{ $isArchitectDone ? 'Ubah Desain SA' : 'Unggah Desain SA' }}</span>
                        </button>
                    @endif
                    <button type="button" @click="openAssignTechnicalModal('architect')" class="text-xs font-semibold text-slate-500 hover:text-[#8F0A0D] cursor-pointer ml-auto">
                        Ubah Penugasan
                    </button>
                </div>
            @endif
        </div>

    </div>
</div>
