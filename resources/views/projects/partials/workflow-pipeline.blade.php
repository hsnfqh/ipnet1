{{-- PIPELINE SALES & PROSPEK PENJUALAN --}}
@php
    $currentStatus = $currentStatus ?? ($project->status ?? 'Draft');
@endphp
<div class="ipnet-card p-6 space-y-4">
    <div class="flex items-center justify-between flex-wrap gap-2 border-b border-slate-100 pb-3">
        <div>
            <p class="text-[#8F0A0D] text-[11px] font-bold inline-flex items-center uppercase tracking-wider mb-1">
                <span class="w-1.5 h-1.5 rounded-full bg-[#8F0A0D] inline-block mr-1.5"></span> PIPELINE &amp; PROSPEK PENJUALAN
            </p>
            <h3 class="text-sm font-bold text-slate-900">Tahapan Sales &amp; Estimasi Closing</h3>
        </div>
        @if(empty($isPresalesOrSaOnly))
        <button type="button" 
                @click="isEditPipelineModalOpen = true; openEditPipelineModal()" 
                onclick="window.openModal('modal-edit-pipeline')"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold text-slate-700 bg-slate-50 hover:bg-slate-100 border border-slate-200 transition cursor-pointer shadow-2xs">
            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            <span>Edit Stage &amp; Prospek</span>
        </button>
        @endif
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="p-3.5 bg-slate-50/70 rounded-xl border border-slate-200/80 flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">STAGE SAAT INI</span>
                <strong class="text-xs font-bold text-slate-900 mt-0.5 block">{{ $project->sales_stage ?: ($currentStatus === 'In Progress' ? 'Closed Won' : 'Qualification') }}</strong>
            </div>
            @if($project->sales_stage === 'Closed Won' || $currentStatus === 'In Progress')
                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                    Closed Won (Deal)
                </span>
            @else
                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-sky-50 text-sky-700 border border-sky-200">
                    Active Stage
                </span>
            @endif
        </div>

        <div class="p-3.5 bg-slate-50/70 rounded-xl border border-slate-200/80 flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">WIN PROBABILITY</span>
                <div class="text-xs font-extrabold text-[#8F0A0D] mt-0.5">
                    {{ ($project->sales_stage === 'Closed Won' || $currentStatus === 'In Progress') ? 100 : ($project->win_probability ?: 10) }}% Peluang
                </div>
            </div>
            <div class="w-16 bg-slate-200 h-2 rounded-full overflow-hidden">
                <div class="bg-gradient-to-r from-[#DC2626] to-[#8F0A0D] h-full rounded-full" style="width: {{ min(100, max(5, ($project->sales_stage === 'Closed Won' || $currentStatus === 'In Progress') ? 100 : ($project->win_probability ?: 10))) }}%"></div>
            </div>
        </div>

        <div class="p-3.5 bg-slate-50/70 rounded-xl border border-slate-200/80 flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">TARGET CLOSING</span>
                <strong class="text-xs font-bold text-slate-900 mt-0.5 block">{{ $project->expected_closing_date ? \Carbon\Carbon::parse($project->expected_closing_date)->format('d M Y') : 'Belum Ditentukan' }}</strong>
            </div>
            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        </div>
    </div>
</div>
