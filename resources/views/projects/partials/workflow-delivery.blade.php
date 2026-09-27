{{-- FASE DELIVERY & SERAH TERIMA PROYEK (PMO / MANAGED SERVICE) --}}
@php
    $currentStatus = $currentStatus ?? ($project->status ?? 'Draft');
    $uniqueEngineers = $uniqueEngineers ?? collect();
    $isMs = ($project->handover_target === 'managed_service' || $project->stage === 'Operate');
    $isBdApproved = $isBdApproved ?? (!empty($bdVerification['status']) && $bdVerification['status'] === 'Approved');
@endphp
<div class="ipnet-card p-6 space-y-5">
    {{-- Header & Status --}}
    <div class="flex items-center justify-between gap-3 border-b border-slate-100 pb-3">
        <div class="min-w-0">
            <p class="text-[#8F0A0D] text-[11px] font-bold inline-flex items-center uppercase tracking-wider mb-0.5">
                <span class="w-1.5 h-1.5 rounded-full bg-[#8F0A0D] inline-block mr-1.5"></span> 
                ALOKASI KATEGORI PROYEK
            </p>
            <h3 class="text-sm sm:text-base font-bold text-slate-900">
                Alokasi Tipe Kategori Proyek
            </h3>
            <p class="text-xs text-slate-500 mt-0.5">
                Penetapan alur eksekusi proyek Implementasi atau Managed Service serta penugasan tim pelaksana.
            </p>
        </div>
        @if($project->pm)
            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center gap-1.5 shadow-2xs shrink-0 whitespace-nowrap">
                <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                <span>{{ $isMs ? 'Managed Service Terkonfirmasi' : 'Implementasi PMO Terkonfirmasi' }}</span>
            </span>
        @elseif(!$isBdApproved)
            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-800 border border-amber-200 flex items-center gap-1.5 shadow-2xs shrink-0 whitespace-nowrap">
                <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                <span>Menunggu Solusi Teknis</span>
            </span>
        @endif
    </div>

    {{-- 2 Standardized Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs items-stretch">
        
        {{-- ══ CARD 1: LEAD PMO / MAINTENANCE ══ --}}
        <div class="p-4 rounded-xl bg-white border border-slate-200 shadow-2xs flex flex-col justify-between space-y-3.5 hover:border-slate-300 transition">
            <div class="space-y-3">
                
                {{-- Header --}}
                <div class="flex items-center justify-between gap-1 pb-2 border-b border-slate-100">
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200 uppercase tracking-wider whitespace-nowrap">
                        KATEGORI PROYEK
                    </span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border whitespace-nowrap {{ $project->pm ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : ($isBdApproved ? 'bg-amber-50 text-amber-800 border-amber-200' : 'bg-slate-100 text-slate-500 border-slate-200') }}">
                        {{ $project->pm ? '✓ Ditugaskan' : ($isBdApproved ? 'Menunggu Kategori' : 'Terkunci') }}
                    </span>
                </div>

                {{-- Person --}}
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-[#8F0A0D] to-[#BA1B1D] text-white font-extrabold text-xs flex items-center justify-center shrink-0 shadow-2xs">
                        {{ $project->pm ? strtoupper(substr($project->pm->name, 0, 2)) : ($isMs ? 'MT' : 'PM') }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <h4 class="font-bold text-xs text-slate-900 truncate" title="{{ $project->pm ? $project->pm->name : '' }}">
                            {{ $project->pm ? $project->pm->name : 'Belum Ditugaskan' }}
                        </h4>
                        <p class="text-[10.5px] text-slate-500 truncate">
                            {{ $isMs ? 'Lead Maintenance (SLA ' . ($project->sla_tier ?: 'Gold') . ')' : 'Lead Project Delivery (PMO)' }}
                        </p>
                    </div>
                </div>

                {{-- Status / Deliverable Box --}}
                <div>
                    @if($project->pm)
                        <div class="p-3 rounded-lg bg-slate-50 border border-slate-200/80 space-y-2">
                            <div class="flex items-center gap-2 min-w-0">
                                <div class="w-7 h-7 rounded-lg bg-red-50 text-[#8F0A0D] flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="font-bold text-slate-900 text-xs truncate">
                                        {{ $isMs ? 'Kategori: Managed Service (Maintenance)' : 'Kategori: Implementasi Proyek (PMO)' }}
                                    </div>
                                    <div class="text-[10.5px] text-slate-500 truncate">{{ $project->pm->email }}</div>
                                </div>
                            </div>
                            @if($isMs)
                                <div class="text-[10px] text-purple-700 font-bold pt-1.5 border-t border-slate-200/80 text-right">
                                    SLA Tier: {{ $project->sla_tier ?: 'Gold (99.5%)' }}
                                </div>
                            @endif
                        </div>
                    @elseif(!$isBdApproved)
                        <div class="p-3 rounded-lg bg-slate-50 border border-dashed border-slate-200 text-slate-500 space-y-1">
                            <div class="font-bold text-slate-700 text-xs flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                <span>Menunggu Solusi Teknis</span>
                            </div>
                            <div class="text-[10.5px] text-slate-400">Menunggu seluruh dokumen &amp; solusi teknis disahkan oleh PIC BD terlebih dahulu.</div>
                        </div>
                    @else
                        <div class="p-3 rounded-lg bg-amber-50/70 border border-amber-200 text-amber-900 space-y-1.5">
                            <div class="flex items-center gap-2 min-w-0">
                                <div class="w-7 h-7 rounded-lg bg-amber-100 text-amber-800 flex items-center justify-center shrink-0">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="font-bold text-amber-900 text-xs">Pilih Kategori Proyek</div>
                                    <div class="text-[10.5px] text-amber-800 truncate">Implementasi ke PMO atau Managed Service ke Maintenance</div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

            </div>

            {{-- Actions Footer --}}
            @if(!$project->pm && empty($isPresalesOrSaOnly) && ($canAssignSales ?? false))
                <div class="pt-2.5 border-t border-slate-100 flex items-center justify-between gap-2 mt-auto">
                    @if(!$isBdApproved)
                        <button type="button" disabled class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-400 bg-slate-100 border border-slate-200 cursor-not-allowed opacity-75 shadow-2xs" title="Terkunci: Menunggu verifikasi solusi teknis disahkan oleh PIC BD">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            <span>Pilih Kategori Proyek</span>
                        </button>
                    @else
                        <button type="button" @click="openHandoverModal('{{ $isMs ? 'managed_service' : 'pmo' }}')"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-[#8F0A0D] bg-red-50 hover:bg-red-100 border border-red-200 transition cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                            <span>Pilih Kategori Proyek</span>
                        </button>
                    @endif
                </div>
            @endif
        </div>

        {{-- ══ CARD 2: TIM ENGINEER PELAKSANA ══ --}}
        <div class="p-4 rounded-xl bg-white border border-slate-200 shadow-2xs flex flex-col justify-between space-y-3.5 hover:border-slate-300 transition">
            <div class="space-y-3">                {{-- Header --}}
                <div class="flex items-center justify-between gap-1 pb-2 border-b border-slate-100">
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200 uppercase tracking-wider whitespace-nowrap">
                        DIVISI PELAKSANA TEKNIS
                    </span>
                    @if(!empty($project->division_id) && !empty($project->division))
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border whitespace-nowrap bg-emerald-50 text-emerald-700 border-emerald-200 flex items-center gap-1">
                            <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            <span>{{ $project->division->name }}</span>
                        </span>
                    @else
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border whitespace-nowrap bg-amber-50 text-amber-800 border-amber-200">
                            Menunggu Delegasi
                        </span>
                    @endif
                </div>

                {{-- Person / Lead --}}
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-[#8F0A0D] to-[#BA1B1D] text-white font-extrabold text-xs flex items-center justify-center shrink-0 shadow-2xs">
                        EN
                    </div>
                    <div class="min-w-0 flex-1">
                        <h4 class="font-bold text-xs text-slate-900 truncate">
                            @if(!empty($project->division_id) && !empty($project->division))
                                @if(str_contains(strtolower($project->division->name), 'net') && !str_contains(strtolower($project->division->name), 'lintas') && !str_contains(strtolower($project->division->name), 'security'))
                                    Nugraha Pratama (Lead Network)
                                @elseif(str_contains(strtolower($project->division->name), 'sec') && !str_contains(strtolower($project->division->name), 'lintas') && !str_contains(strtolower($project->division->name), 'network'))
                                    Ignatius Rizky (Lead Security)
                                @else
                                    Lead Network &amp; Lead Security (Lintas Divisi)
                                @endif
                            @else
                                Belum Didelegasikan ke Divisi
                            @endif
                        </h4>
                        <p class="text-[10.5px] text-slate-500 truncate">
                            Lead Engineering Delivery &amp; Operational
                        </p>
                    </div>
                </div>

                {{-- Status / Information Box --}}
                <div>
                    @if(!empty($project->division_id) && !empty($project->division))
                        <div class="p-3 rounded-lg bg-slate-50 border border-slate-200/80 space-y-1.5">
                            <div class="flex items-center gap-2 min-w-0">
                                <div class="w-7 h-7 rounded-lg bg-red-50 text-[#8F0A0D] flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="font-bold text-slate-900 text-xs truncate">
                                        {{ $project->division->name }}
                                    </div>
                                    <div class="text-[10.5px] text-slate-500 truncate">
                                        Divisi Pelaksana Teknis Proyek
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="p-3 rounded-lg bg-amber-50/70 border border-amber-200 text-amber-900 space-y-1.5">
                            <div class="flex items-center gap-2 min-w-0">
                                <div class="w-7 h-7 rounded-lg bg-amber-100 text-amber-800 flex items-center justify-center shrink-0">
                                    <svg class="w-3.5 h-3.5 text-amber-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="font-bold text-amber-900 text-xs">Pilih Divisi Pelaksana</div>
                                    <div class="text-[10.5px] text-amber-800 truncate">Serahkan wewenang teknis ke Divisi Network, Security, atau Keduanya</div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

            </div>

            {{-- Actions Footer: Hanya dapat diubah oleh PM / PMO / Sales / Executive (Bukan Team Leader) --}}
            @php
                $canChangeDivision = $authUser && (
                    \App\Helpers\ScopeHelper::isPmo($authUser)
                    || ($project->pm_id && $project->pm_id == $authUser->id)
                    || ($canAssignSales ?? false)
                    || ($isExecutive ?? false)
                ) && !\App\Helpers\ScopeHelper::isTeamLeader($authUser);
            @endphp
            @if($canChangeDivision)
                <div class="pt-2.5 border-t border-slate-100 flex items-center justify-between gap-2 mt-auto">
                    <button type="button" 
                            @click="openAssignDivisionModal()" 
                            onclick="window.openModal('modal-assign-division')"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-[#8F0A0D] bg-red-50 hover:bg-red-100 border border-red-200 transition cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        <span>{{ (!empty($project->division_id) && !empty($project->division)) ? 'Ubah Divisi Pelaksana' : 'Pilih Divisi Pelaksana' }}</span>
                    </button>
                </div>
            @endif
        </div>

    </div>
</div>
