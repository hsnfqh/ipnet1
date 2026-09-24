{{-- FASE DELIVERY & SERAH TERIMA PROYEK (PMO / MANAGED SERVICE) --}}
@php
    $currentStatus = $currentStatus ?? ($project->status ?? 'Draft');
    $uniqueEngineers = $uniqueEngineers ?? collect();
    $isMs = ($project->handover_target === 'managed_service' || $project->stage === 'Operate');
@endphp
<div class="ipnet-card p-6 space-y-5">
    {{-- Header & Status --}}
    <div class="border-b border-slate-100 pb-3 flex items-center justify-between flex-wrap gap-2">
        <div>
            <p class="text-[#8F0A0D] text-[11px] font-bold inline-flex items-center uppercase tracking-wider mb-1">
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
            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center gap-1.5 shadow-2xs">
                <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                <span>{{ $isMs ? 'Managed Service Terkonfirmasi' : 'Implementasi PMO Terkonfirmasi' }}</span>
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
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border whitespace-nowrap {{ $project->pm ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-amber-50 text-amber-800 border-amber-200' }}">
                        {{ $project->pm ? '✓ Ditugaskan' : 'Menunggu Kategori' }}
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
                    @else
                        <div class="p-3 rounded-lg bg-amber-50/70 border border-amber-200 text-amber-900 space-y-1.5">
                            <div class="flex items-center gap-2 min-w-0">
                                <div class="w-7 h-7 rounded-lg bg-amber-100 text-amber-800 flex items-center justify-center shrink-0">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
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
            <div class="pt-2.5 border-t border-slate-100 flex items-center justify-between gap-2 mt-auto">
                <button type="button" @click="openHandoverModal('{{ $isMs ? 'managed_service' : 'pmo' }}')"
                        class="text-xs font-semibold text-slate-600 hover:text-[#8F0A0D] cursor-pointer inline-flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                    <span>{{ $project->pm ? 'Ubah Kategori Proyek' : 'Pilih Kategori Proyek' }}</span>
                </button>
            </div>
        </div>

        {{-- ══ CARD 2: TIM ENGINEER PELAKSANA ══ --}}
        <div class="p-4 rounded-xl bg-white border border-slate-200 shadow-2xs flex flex-col justify-between space-y-3.5 hover:border-slate-300 transition">
            <div class="space-y-3">
                
                {{-- Header --}}
                <div class="flex items-center justify-between gap-1 pb-2 border-b border-slate-100">
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200 uppercase tracking-wider whitespace-nowrap">
                        TIM ENGINEER
                    </span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border whitespace-nowrap {{ $uniqueEngineers->count() > 0 ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-slate-100 text-slate-500 border-slate-200' }}">
                        {{ $uniqueEngineers->count() > 0 ? '✓ ' . $uniqueEngineers->count() . ' Personel' : 'Belum Ditugaskan' }}
                    </span>
                </div>

                {{-- Person / Lead --}}
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-[#8F0A0D] to-[#BA1B1D] text-white font-extrabold text-xs flex items-center justify-center shrink-0 shadow-2xs">
                        EN
                    </div>
                    <div class="min-w-0 flex-1">
                        <h4 class="font-bold text-xs text-slate-900 truncate" title="{{ $uniqueEngineers->count() > 0 ? $uniqueEngineers->pluck('name')->join(', ') : '' }}">
                            {{ $uniqueEngineers->count() > 0 ? $uniqueEngineers->first()->name . ($uniqueEngineers->count() > 1 ? ' (+' . ($uniqueEngineers->count() - 1) . ' tim)' : '') : 'Teknisi Pelaksana' }}
                        </h4>
                        <p class="text-[10.5px] text-slate-500 truncate">
                            {{ $isMs ? 'Teknisi Operasional Maintenance' : 'Field Engineers & Implementasi' }}
                        </p>
                    </div>
                </div>

                {{-- Engineers List Box --}}
                <div>
                    @if($uniqueEngineers->count() > 0)
                        <div class="p-3 rounded-lg bg-slate-50 border border-slate-200/80 space-y-1.5">
                            <div class="text-[10.5px] font-bold text-slate-700 mb-1 flex items-center justify-between">
                                <span>Personel Terdaftar</span>
                                <span class="text-slate-400 font-mono">{{ $uniqueEngineers->count() }} Orang</span>
                            </div>
                            <div class="space-y-1 max-h-20 overflow-y-auto pr-0.5">
                                @foreach($uniqueEngineers->take(3) as $eng)
                                    <div class="flex items-center justify-between text-[11px] bg-white p-1 rounded border border-slate-200/60">
                                        <span class="font-semibold text-slate-800 truncate">{{ $eng->name }}</span>
                                        <span class="text-[9.5px] text-slate-400 shrink-0 font-medium">{{ $eng->roles->pluck('name')->first() ?? 'Engineer' }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="p-3 rounded-lg border border-dashed border-slate-200 text-slate-400 text-center text-[10.5px]">
                            Menunggu alokasi tim teknisi oleh Lead {{ $isMs ? 'Maintenance' : 'PMO' }}.
                        </div>
                    @endif
                </div>

            </div>

        </div>

    </div>
</div>
