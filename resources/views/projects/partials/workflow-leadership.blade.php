{{-- PERSETUJUAN PIMPINAN (REVIEW & SIGN-OFF DRAF SUSANTO & HARIYADI) --}}
@php
    $isBothApproved = $isBothApproved ?? (!empty($headApproval['approved']) && !empty($directorApproval['approved']));
    $isHeadAssigned = $isHeadAssigned ?? (!empty($headApproval['assigned']));
    $isDirectorAssigned = $isDirectorAssigned ?? (!empty($directorApproval['assigned']));
    $canApproveHead = $canApproveHead ?? false;
    $canApproveDirector = $canApproveDirector ?? false;
    $susantoUser = $susantoUser ?? null;
    $hariyadiUser = $hariyadiUser ?? null;
@endphp
<div class="ipnet-card p-6 space-y-5">
    <div class="border-b border-slate-100 pb-3 flex items-center justify-between flex-wrap gap-2">
        <div>
            <p class="text-[#8F0A0D] text-[11px] font-bold inline-flex items-center uppercase tracking-wider mb-1">
                <span class="w-1.5 h-1.5 rounded-full bg-[#8F0A0D] inline-block mr-1.5"></span> OTORISASI PIMPINAN
            </p>
            <h3 class="text-sm sm:text-base font-bold text-slate-900">Persetujuan Pimpinan (Review &amp; Sign-Off)</h3>
            <p class="text-xs text-slate-500 mt-0.5">Review kelayakan teknis oleh Head Divisi (Pak Susanto) &amp; otorisasi kontrak oleh Direktur (Pak Hariyadi).</p>
        </div>
        @if($isBothApproved)
            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center gap-1.5 shadow-2xs">
                <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                <span>Otorisasi Lengkap Disahkan</span>
            </span>
        @endif
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
        {{-- Reviewer 1: Pak Susanto --}}
        <div class="p-4 rounded-xl bg-slate-50/70 border border-slate-200 space-y-3 flex flex-col justify-between">
            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">HEAD DIVISI</div>
                        <div class="font-bold text-slate-900 text-sm mt-0.5">{{ $headApproval['assigned_to'] ?? ($susantoUser ? $susantoUser->name : 'Pak Susanto Djaya') }}</div>
                    </div>
                    <span class="px-2.5 py-0.5 rounded text-[10.5px] font-bold {{ !empty($headApproval['approved']) ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : ($isHeadAssigned ? 'bg-amber-50 text-amber-800 border border-amber-200' : 'bg-slate-100 text-slate-600 border border-slate-200') }}">
                        {{ !empty($headApproval['approved']) ? '✓ Disetujui' : ($isHeadAssigned ? 'Menunggu Review' : 'Belum Di-assign') }}
                    </span>
                </div>

                <div class="text-[11.5px] text-slate-600">
                    @if(!empty($headApproval['approved']))
                        <div class="p-2.5 rounded-lg bg-emerald-50/80 border border-emerald-200 text-emerald-800 font-medium">
                            "{{ $headApproval['notes'] ?? 'Review kelayakan teknis & alokasi resource disetujui.' }}"
                        </div>
                        <div class="text-[10.5px] text-slate-400 mt-1">Disetujui: {{ $headApproval['date'] ?? '-' }}</div>
                    @elseif($isHeadAssigned)
                        <div class="p-2.5 rounded-lg bg-amber-50/60 border border-amber-200 text-amber-800 italic">
                            "Menunggu review dari {{ $headApproval['assigned_to'] ?? 'Pak Susanto Djaya' }}."
                        </div>
                        @if(!empty($headApproval['assigned_at']))
                            <div class="text-[10.5px] text-slate-400 mt-1">Ditugaskan: {{ $headApproval['assigned_at'] }}</div>
                        @endif
                    @else
                        <span class="text-slate-400">Belum diajukan ke Head Divisi.</span>
                    @endif
                </div>
            </div>

            <div class="pt-2 border-t border-slate-200 flex items-center justify-between gap-2">
                @if(!$isHeadAssigned)
                    <button type="button" @click="openAssignModal('head')" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-[#8F0A0D] bg-red-50 hover:bg-red-100 border border-red-200 transition cursor-pointer">
                        <span>+ Assign ke Pak Susanto</span>
                    </button>
                @elseif(!$headApproval['approved'])
                    <button type="button" @click="openAssignModal('head')" class="text-xs font-semibold text-slate-500 hover:text-[#8F0A0D] cursor-pointer">
                        Ubah Penugasan
                    </button>
                @endif

                @if($canApproveHead && $isHeadAssigned)
                    <button type="button" @click="openApproveModal('head')" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg text-xs font-bold text-white btn-ipnet-primary shadow-xs cursor-pointer ml-auto">
                        <span>{{ empty($headApproval['approved']) ? '✓ Beri Approval' : 'Ubah Catatan' }}</span>
                    </button>
                @endif
            </div>
        </div>

        {{-- Reviewer 2: Pak Hariyadi --}}
        <div class="p-4 rounded-xl bg-slate-50/70 border border-slate-200 space-y-3 flex flex-col justify-between">
            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">DIREKTUR</div>
                        <div class="font-bold text-slate-900 text-sm mt-0.5">{{ $directorApproval['assigned_to'] ?? ($hariyadiUser ? $hariyadiUser->name : 'Pak Hariyadi') }}</div>
                    </div>
                    <span class="px-2.5 py-0.5 rounded text-[10.5px] font-bold {{ !empty($directorApproval['approved']) ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : ($isDirectorAssigned ? 'bg-amber-50 text-amber-800 border border-amber-200' : 'bg-slate-100 text-slate-600 border border-slate-200') }}">
                        {{ !empty($directorApproval['approved']) ? '✓ Disahkan' : ($isDirectorAssigned ? 'Menunggu Otorisasi' : 'Belum Di-assign') }}
                    </span>
                </div>

                <div class="text-[11.5px] text-slate-600">
                    @if(!empty($directorApproval['approved']))
                        <div class="p-2.5 rounded-lg bg-emerald-50/80 border border-emerald-200 text-emerald-800 font-medium">
                            "{{ $directorApproval['notes'] ?? 'Otorisasi finansial & validasi kontrak disahkan.' }}"
                        </div>
                        <div class="text-[10.5px] text-slate-400 mt-1">Disahkan: {{ $directorApproval['date'] ?? '-' }}</div>
                    @elseif($isDirectorAssigned)
                        <div class="p-2.5 rounded-lg bg-amber-50/60 border border-amber-200 text-amber-800 italic">
                            "Menunggu otorisasi finansial dari {{ $directorApproval['assigned_to'] ?? 'Pak Hariyadi' }}."
                        </div>
                        @if(!empty($directorApproval['assigned_at']))
                            <div class="text-[10.5px] text-slate-400 mt-1">Ditugaskan: {{ $directorApproval['assigned_at'] }}</div>
                        @endif
                    @else
                        <span class="text-slate-400">Belum diajukan ke Direktur.</span>
                    @endif
                </div>
            </div>

            <div class="pt-2 border-t border-slate-200 flex items-center justify-between gap-2">
                @if(!$isDirectorAssigned)
                    <button type="button" @click="openAssignModal('director')" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-[#8F0A0D] bg-red-50 hover:bg-red-100 border border-red-200 transition cursor-pointer">
                        <span>+ Assign ke Pak Hariyadi</span>
                    </button>
                @elseif(!$directorApproval['approved'])
                    <button type="button" @click="openAssignModal('director')" class="text-xs font-semibold text-slate-500 hover:text-[#8F0A0D] cursor-pointer">
                        Ubah Penugasan
                    </button>
                @endif

                @if($canApproveDirector && $isDirectorAssigned)
                    <button type="button" @click="openApproveModal('director')" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg text-xs font-bold text-white btn-ipnet-primary shadow-xs cursor-pointer ml-auto">
                        <span>{{ empty($directorApproval['approved']) ? '✓ Beri Otorisasi' : 'Ubah Catatan' }}</span>
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>
