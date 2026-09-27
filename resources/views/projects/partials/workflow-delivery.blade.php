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
            <div class="space-y-3">
                
                {{-- Header --}}
                <div class="flex items-center justify-between gap-1 pb-2 border-b border-slate-100">
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200 uppercase tracking-wider whitespace-nowrap">
                        TIM ENGINEER
                    </span>
                    @if($uniqueEngineers->count() > 0)
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border whitespace-nowrap bg-emerald-50 text-emerald-700 border-emerald-200">
                            ✓ {{ $uniqueEngineers->count() }} Personel Ditugaskan
                        </span>
                    @elseif($project->division_id || $project->handover_status === 'Approved' || in_array($project->stage, ['Deliver', 'Operate']))
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border whitespace-nowrap bg-indigo-50 text-indigo-700 border-indigo-200">
                            ✓ Assigned ke Lead {{ $project->division ? $project->division->name : 'Network & Security' }}
                        </span>
                    @else
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border whitespace-nowrap bg-amber-50 text-amber-800 border-amber-200">
                            Menunggu Disposisi Divisi
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
                            @if($project->division && str_contains(strtolower($project->division->name), 'net'))
                                Nugraha Pratama (Lead Network)
                            @elseif($project->division && str_contains(strtolower($project->division->name), 'sec'))
                                Ignatius Rizky (Lead Security)
                            @elseif($project->division)
                                {{ $project->division->name }}
                            @elseif($project->stage === 'Deliver' || $project->handover_status === 'Approved')
                                Lead Network &amp; Lead Security (Lintas Divisi)
                            @else
                                Belum Didisposisikan ke Divisi
                            @endif
                        </h4>
                        <p class="text-[10.5px] text-slate-500 truncate">
                            {{ $uniqueEngineers->count() > 0 ? $uniqueEngineers->pluck('name')->join(', ') : 'Field Engineers & Pelaksana Lapangan' }}
                        </p>
                    </div>
                </div>

                {{-- Engineers List / Status Box --}}
                <div>
                    @if($uniqueEngineers->count() > 0)
                        <div class="p-3 rounded-lg bg-slate-50 border border-slate-200/80 space-y-1.5">
                            <div class="text-[10.5px] font-bold text-slate-700 mb-1 flex items-center justify-between">
                                <span>Personel Teknisi Terdaftar</span>
                                <span class="text-slate-400 font-mono">{{ $uniqueEngineers->count() }} Orang</span>
                            </div>
                            <div class="space-y-1 max-h-24 overflow-y-auto pr-0.5">
                                @foreach($uniqueEngineers as $eng)
                                    <div class="flex items-center justify-between text-[11px] bg-white p-1.5 rounded border border-slate-200/60 shadow-2xs">
                                        <div class="flex items-center gap-1.5 min-w-0">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            <span class="font-semibold text-slate-800 truncate">{{ $eng->name }}</span>
                                        </div>
                                        <span class="text-[9.5px] text-slate-400 shrink-0 font-medium">{{ $eng->roles->pluck('name')->first() ?? 'Engineer' }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @elseif($project->division_id || $project->handover_status === 'Approved' || in_array($project->stage, ['Deliver', 'Operate']))
                        <div class="p-3 rounded-lg bg-indigo-50/50 border border-indigo-200/70 text-indigo-950 space-y-1">
                            <div class="font-bold text-xs flex items-center gap-1.5 text-indigo-900">
                                <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>Status: Assigned ke Lead Engineer</span>
                            </div>
                            <p class="text-[10.5px] text-indigo-800/80 leading-relaxed">
                                Proyek telah masuk ke antrean kerja. Lead Engineer dapat memilih dan menugaskan teknisi lapangan (Field Engineer) pelaksana di bawah ini.
                            </p>
                        </div>
                    @else
                        <div class="p-3 rounded-lg border border-dashed border-amber-300 bg-amber-50/40 text-amber-900 text-center text-[10.5px] space-y-1">
                            <div class="font-bold text-amber-900">Belum Ada Disposisi Divisi</div>
                            <p class="text-amber-800/80">Silakan tentukan divisi pelaksana (Network, Security, atau Keduanya) untuk menyerahkan tugas ke Lead Engineer.</p>
                        </div>
                    @endif
                </div>

            </div>

            {{-- Actions Footer: Disposisi Divisi & Penugasan Teknisi Lapangan --}}
            <div class="pt-2.5 border-t border-slate-100 flex flex-wrap items-center justify-between gap-2 mt-auto">
                @if(!$project->division_id && $project->stage !== 'Deliver' && $project->handover_status !== 'Approved')
                    {{-- Belum Ada Divisi: Tombol Disposisi Utama --}}
                    <button type="button" 
                            @click="openAssignDivisionModal()" 
                            onclick="window.openModal('modal-assign-division')"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-white bg-[#8F0A0D] hover:bg-[#73080A] transition cursor-pointer shadow-xs">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        <span>Assign ke Divisi Terkait</span>
                    </button>
                @else
                    {{-- Sudah Ada Divisi: Tombol Pilih Teknisi & Board Tugas --}}
                    <div class="flex flex-wrap items-center gap-1.5 w-full justify-between">
                        <div class="flex items-center gap-1.5">
                            <button type="button" 
                                    @click="openAssignEngineerModal()" 
                                    onclick="window.openModal('modal-assign-engineer')"
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg text-xs font-bold text-white bg-[#8F0A0D] hover:bg-[#73080A] transition cursor-pointer shadow-xs">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                                <span>+ Pilih Teknisi</span>
                            </button>

                            <a href="{{ route('tasks.index') }}"
                               class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-200 transition cursor-pointer">
                                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                <span>Board Tugas</span>
                            </a>
                        </div>

                        {{-- Tombol Ubah Divisi --}}
                        <button type="button" 
                                @click="openAssignDivisionModal()" 
                                onclick="window.openModal('modal-assign-division')"
                                title="Ubah Divisi Pelaksana Proyek"
                                class="text-[11px] text-slate-500 hover:text-[#8F0A0D] font-semibold underline underline-offset-2 transition cursor-pointer">
                            Ubah Divisi
                        </button>
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>
