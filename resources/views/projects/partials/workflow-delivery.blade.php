{{-- FASE DELIVERY & SERAH TERIMA PROYEK (PMO / MANAGED SERVICE) --}}
@php
    $currentStatus = $currentStatus ?? ($project->status ?? 'Draft');
    $uniqueEngineers = $uniqueEngineers ?? collect();
@endphp
<div class="ipnet-card p-6 space-y-4">
    <div class="flex items-center justify-between border-b border-slate-100 pb-3 flex-wrap gap-2">
        <div>
            <p class="text-amber-600 text-[11px] font-bold inline-flex items-center uppercase tracking-wider mb-1">
                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 inline-block mr-1.5"></span> 
                {{ ($project->handover_target === 'managed_service' || $project->stage === 'Operate') ? 'FASE OPERASIONAL & MANAGED SERVICE' : 'FASE DELIVERY & IMPLEMENTASI PMO' }}
            </p>
            <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                <span>Alokasi Tim {{ ($project->handover_target === 'managed_service' || $project->stage === 'Operate') ? 'Managed Service' : 'PMO' }} &amp; Engineer Pelaksana</span>
                @if($project->handover_target === 'managed_service' || $project->stage === 'Operate')
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200 uppercase tracking-wider">
                        Managed Service (SLA {{ $project->sla_tier ?: 'Gold' }})
                    </span>
                @else
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200 uppercase tracking-wider">
                        Project Delivery (PMO)
                    </span>
                @endif
            </h3>
        </div>
        <div class="flex items-center gap-2.5">
            @if($currentStatus !== 'Completed')
                <form action="{{ route('projects.stage_update', $project->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="status" value="Completed">
                    <button type="submit" onclick="return confirm('Tandai proyek {{ addslashes($project->name) }} sebagai Selesai (Completed)?')"
                            class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition shadow-xs cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        <span>✓ Selesaikan Proyek (Completed)</span>
                    </button>
                </form>
            @endif
            <button type="button" 
                    @click="isHandoverModalOpen = true; openAssignModal('both')" 
                    onclick="window.openModal('modal-handover')" 
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold text-[#8F0A0D] bg-red-50 hover:bg-red-100 border border-red-200 transition cursor-pointer shadow-2xs">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                <span>{{ $project->pm ? 'Ubah Handover (' . $project->pm->name . ')' : '+ Handover PMO / Managed Service' }}</span>
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
        {{-- Card PM / Lead MS --}}
        <div class="p-4 rounded-xl bg-slate-50/70 border border-slate-200 space-y-1.5">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                    {{ ($project->handover_target === 'managed_service' || $project->stage === 'Operate') ? 'LEAD MANAGED SERVICE / OPERASIONAL' : 'PROJECT MANAGER (PMO)' }}
                </span>
                @if($project->pm)
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        ✓ Assigned
                    </span>
                @else
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                        Menunggu Assign
                    </span>
                @endif
            </div>
            <div class="font-bold text-slate-900 text-sm mt-0.5">{{ $project->pm ? $project->pm->name : 'Belum Ada PM / Lead' }}</div>
            <div class="text-[11.5px] text-slate-500">{{ $project->pm ? $project->pm->email : 'Sales silakan serahkan proyek ke tim PMO atau Managed Service' }}</div>
            @if($project->handover_target === 'managed_service' || $project->stage === 'Operate')
                <div class="pt-1.5 border-t border-slate-200 flex items-center gap-2 text-[10.5px]">
                    <span class="text-slate-400">SLA Tier:</span>
                    <span class="font-bold text-purple-700 bg-purple-50 px-1.5 py-0.5 rounded border border-purple-200">{{ $project->sla_tier ?: 'Gold (99.5%)' }}</span>
                </div>
            @endif
        </div>

        {{-- Card Tim Engineer --}}
        <div class="p-4 rounded-xl bg-slate-50/70 border border-slate-200 space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">TIM ENGINEER PELAKSANA</span>
                <span class="text-[10.5px] font-bold text-slate-600">{{ $uniqueEngineers->count() }} Personel</span>
            </div>
            @if($uniqueEngineers->count() > 0)
                <div class="space-y-1.5 pt-0.5 max-h-28 overflow-y-auto pr-1">
                    @foreach($uniqueEngineers as $eng)
                        <div class="flex items-center justify-between text-[11.5px] bg-white p-1.5 rounded-lg border border-slate-100">
                            <span class="font-semibold text-slate-800">{{ $eng->name }}</span>
                            <span class="text-[10.5px] text-slate-500 font-medium">{{ $eng->roles->pluck('name')->first() ?? 'Engineer' }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-[11.5px] text-slate-400 italic py-2">
                    Menunggu alokasi tim teknis oleh {{ ($project->handover_target === 'managed_service' || $project->stage === 'Operate') ? 'Lead Managed Service' : 'PMO' }}.
                </div>
            @endif
        </div>
    </div>
</div>
