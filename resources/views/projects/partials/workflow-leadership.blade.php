{{-- PERSETUJUAN PIMPINAN (REVIEW & SIGN-OFF) --}}
@php
    $isBothApproved = $isBothApproved ?? (!empty($headApproval['approved']) && !empty($directorApproval['approved']));
    $isHeadAssigned = $isHeadAssigned ?? (!empty($headApproval['assigned']));
    $isDirectorAssigned = $isDirectorAssigned ?? (!empty($directorApproval['assigned']));
    $canApproveHead = $canApproveHead ?? false;
    $canApproveDirector = $canApproveDirector ?? false;
    $susantoUser = $susantoUser ?? null;
    $hariyadiUser = $hariyadiUser ?? null;

    $headName = $headApproval['assigned_to'] ?? ($susantoUser ? $susantoUser->name : 'Susanto Djaya');
    $headInitials = strtoupper(substr($headName, 0, 2));
    if (str_contains(strtolower($headName), 'susanto')) {
        $headInitials = 'SD';
    }

    $directorName = $directorApproval['assigned_to'] ?? ($hariyadiUser ? $hariyadiUser->name : 'Hariyadi');
    $directorInitials = strtoupper(substr($directorName, 0, 2));
    if (str_contains(strtolower($directorName), 'hariyadi')) {
        $directorInitials = 'HY';
    }
@endphp
<div class="ipnet-card p-6 space-y-5">
    {{-- Header & Status --}}
    <div class="border-b border-slate-100 pb-3 flex items-center justify-between flex-wrap gap-2">
        <div>
            <p class="text-[#8F0A0D] text-[11px] font-bold inline-flex items-center uppercase tracking-wider mb-1">
                <span class="w-1.5 h-1.5 rounded-full bg-[#8F0A0D] inline-block mr-1.5"></span> OTORISASI PIMPINAN
            </p>
            <h3 class="text-sm sm:text-base font-bold text-slate-900">Persetujuan Pimpinan (Review &amp; Sign-Off)</h3>
            <p class="text-xs text-slate-500 mt-0.5">Review kelayakan teknis oleh Head Divisi &amp; otorisasi kontrak oleh Direktur.</p>
        </div>
        @if($isBothApproved)
            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center gap-1.5 shadow-2xs">
                <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                <span>Otorisasi Lengkap Disahkan</span>
            </span>
        @endif
    </div>

    {{-- 2 Leadership Authorization Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs items-stretch">
        
        {{-- ══ CARD 1: HEAD DIVISI ══ --}}
        <div class="p-4 rounded-xl bg-white border border-slate-200 shadow-2xs flex flex-col justify-between space-y-3.5 hover:border-slate-300 transition">
            <div class="space-y-3">
                
                {{-- Header --}}
                <div class="flex items-center justify-between gap-1 pb-2 border-b border-slate-100">
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200 uppercase tracking-wider whitespace-nowrap">
                        HEAD DIVISI
                    </span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border whitespace-nowrap {{ !empty($headApproval['approved']) ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : ($isHeadAssigned ? 'bg-amber-50 text-amber-800 border-amber-200' : 'bg-slate-100 text-slate-500 border-slate-200') }}">
                        {{ !empty($headApproval['approved']) ? '✓ Disetujui' : ($isHeadAssigned ? 'Menunggu Review' : 'Belum Di-assign') }}
                    </span>
                </div>

                {{-- Person --}}
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-[#8F0A0D] to-[#BA1B1D] text-white font-extrabold text-xs flex items-center justify-center shrink-0 shadow-2xs">
                        {{ $headInitials }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <h4 class="font-bold text-xs text-slate-900 truncate" title="{{ $headName }}">
                            {{ $headName }}
                        </h4>
                        <p class="text-[10.5px] text-slate-500 truncate">Review Kelayakan &amp; Alokasi Resource</p>
                    </div>
                </div>

                {{-- Approval Notes Box --}}
                <div>
                    @if(!empty($headApproval['approved']))
                        <div class="p-3 rounded-lg bg-emerald-50/80 border border-emerald-200 text-emerald-800 space-y-1">
                            <div class="font-medium italic text-[11px]">"{{ $headApproval['notes'] ?? 'Kelayakan teknis & alokasi resource disetujui.' }}"</div>
                            <div class="text-[10px] text-emerald-600 mt-1 font-mono">Disetujui: {{ $headApproval['date'] ?? '-' }}</div>
                        </div>
                    @elseif($isHeadAssigned)
                        <div class="p-3 rounded-lg bg-amber-50/70 border border-amber-200 text-amber-900 space-y-1">
                            <div class="font-medium italic text-[11px]">"Menunggu review kelayakan dari {{ $headName }}."</div>
                            @if(!empty($headApproval['assigned_at']))
                                <div class="text-[10px] text-amber-700 font-mono mt-1">Ditugaskan: {{ $headApproval['assigned_at'] }}</div>
                            @endif
                        </div>
                    @else
                        <div class="p-3 rounded-lg border border-dashed border-slate-200 text-slate-400 text-center text-[10.5px]">
                            Belum diajukan ke Head Divisi.
                        </div>
                    @endif
                </div>

            </div>

            {{-- Actions Footer --}}
            <div class="pt-2.5 border-t border-slate-100 flex items-center justify-between gap-2 mt-auto">
                @if(!$isHeadAssigned)
                    <button type="button" @click="openAssignModal('head')" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-[#8F0A0D] bg-red-50 hover:bg-red-100 border border-red-200 transition cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Assign Head Divisi</span>
                    </button>
                @elseif(!$headApproval['approved'])
                    <button type="button" @click="openAssignModal('head')" class="text-xs font-semibold text-slate-500 hover:text-[#8F0A0D] cursor-pointer">
                        Ubah Penugasan
                    </button>
                @endif

                @if($canApproveHead && $isHeadAssigned)
                    <button type="button" @click="openApproveModal('head')" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg text-xs font-bold text-white btn-ipnet-primary shadow-xs cursor-pointer ml-auto">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        <span>{{ empty($headApproval['approved']) ? 'Beri Approval' : 'Ubah Catatan' }}</span>
                    </button>
                @endif
            </div>
        </div>

        {{-- ══ CARD 2: DIREKTUR ══ --}}
        <div class="p-4 rounded-xl bg-white border border-slate-200 shadow-2xs flex flex-col justify-between space-y-3.5 hover:border-slate-300 transition">
            <div class="space-y-3">
                
                {{-- Header --}}
                <div class="flex items-center justify-between gap-1 pb-2 border-b border-slate-100">
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200 uppercase tracking-wider whitespace-nowrap">
                        DIREKTUR
                    </span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border whitespace-nowrap {{ !empty($directorApproval['approved']) ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : ($isDirectorAssigned ? 'bg-amber-50 text-amber-800 border-amber-200' : 'bg-slate-100 text-slate-500 border-slate-200') }}">
                        {{ !empty($directorApproval['approved']) ? '✓ Disahkan' : ($isDirectorAssigned ? 'Menunggu Otorisasi' : 'Belum Di-assign') }}
                    </span>
                </div>

                {{-- Person --}}
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-[#8F0A0D] to-[#BA1B1D] text-white font-extrabold text-xs flex items-center justify-center shrink-0 shadow-2xs">
                        {{ $directorInitials }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <h4 class="font-bold text-xs text-slate-900 truncate" title="{{ $directorName }}">
                            {{ $directorName }}
                        </h4>
                        <p class="text-[10.5px] text-slate-500 truncate">Otorisasi Anggaran &amp; Kontrak</p>
                    </div>
                </div>

                {{-- Approval Notes Box --}}
                <div>
                    @if(!empty($directorApproval['approved']))
                        <div class="p-3 rounded-lg bg-emerald-50/80 border border-emerald-200 text-emerald-800 space-y-1">
                            <div class="font-medium italic text-[11px]">"{{ $directorApproval['notes'] ?? 'Otorisasi anggaran dan persetujuan eksekusi kontrak disahkan.' }}"</div>
                            <div class="text-[10px] text-emerald-600 mt-1 font-mono">Disahkan: {{ $directorApproval['date'] ?? '-' }}</div>
                        </div>
                    @elseif($isDirectorAssigned)
                        <div class="p-3 rounded-lg bg-amber-50/70 border border-amber-200 text-amber-900 space-y-1">
                            <div class="font-medium italic text-[11px]">"Menunggu otorisasi anggaran dari {{ $directorName }}."</div>
                            @if(!empty($directorApproval['assigned_at']))
                                <div class="text-[10px] text-amber-700 font-mono mt-1">Ditugaskan: {{ $directorApproval['assigned_at'] }}</div>
                            @endif
                        </div>
                    @else
                        <div class="p-3 rounded-lg border border-dashed border-slate-200 text-slate-400 text-center text-[10.5px]">
                            Belum diajukan ke Direktur.
                        </div>
                    @endif
                </div>

            </div>

            {{-- Actions Footer --}}
            <div class="pt-2.5 border-t border-slate-100 flex items-center justify-between gap-2 mt-auto">
                @if(!$isDirectorAssigned)
                    <button type="button" @click="openAssignModal('director')" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-[#8F0A0D] bg-red-50 hover:bg-red-100 border border-red-200 transition cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Assign Direktur</span>
                    </button>
                @elseif(!$directorApproval['approved'])
                    <button type="button" @click="openAssignModal('director')" class="text-xs font-semibold text-slate-500 hover:text-[#8F0A0D] cursor-pointer">
                        Ubah Penugasan
                    </button>
                @endif

                @if($canApproveDirector && $isDirectorAssigned)
                    <button type="button" @click="openApproveModal('director')" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg text-xs font-bold text-white btn-ipnet-primary shadow-xs cursor-pointer ml-auto">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        <span>{{ empty($directorApproval['approved']) ? 'Beri Otorisasi' : 'Ubah Catatan' }}</span>
                    </button>
                @endif
            </div>
        </div>

    </div>
</div>
