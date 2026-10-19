@extends('layouts.app')

@section('title', 'Monitoring PM / CM - ' . ($project ? $project->client : 'Managed Service'))

@section('content')
<div class="space-y-6" x-data="{ 
    isPmModalOpen: false, 
    activeCycle: null,
    isCmModalOpen: false,
    openPmModal(cycle) {
        this.activeCycle = cycle;
        this.isPmModalOpen = true;
    }
}">
    <!-- Top Header & Customer Selector -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 mb-1">
                <a href="{{ route('ms.projects.index') }}" class="hover:text-slate-800 transition">Managed Service</a>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-[#8F0A0D]">Monitoring PM & CM</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Monitoring PM / CM</h1>
            <p class="text-xs text-slate-500 mt-0.5">Pantau realisasi Preventive Maintenance berkala dan utilisasi kuota Corrective Maintenance klien.</p>
        </div>

        <!-- Customer Selector Dropdown (Sesuai Sketsa: Customer [ID]) -->
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
            <form method="GET" action="{{ route('ms.monitoring.index') }}" class="flex items-center gap-2">
                <span class="text-xs font-bold text-slate-700 whitespace-nowrap">Customer:</span>
                <select name="project_id" onchange="this.form.submit()" class="px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-bold text-slate-900 bg-white focus:outline-none focus:border-[#8F0A0D] shadow-2xs cursor-pointer min-w-[260px]">
                    @foreach($allProjects as $proj)
                        <option value="{{ $proj->id }}" {{ ($project && $project->id == $proj->id) ? 'selected' : '' }}>
                            {{ $proj->client }} — {{ \Illuminate\Support\Str::limit($proj->name, 35) }}
                        </option>
                    @endforeach
                </select>
            </form>

            @if($project)
                <a href="{{ route('ms.projects.show', $project->id) }}" class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold transition shadow-2xs">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    <span>Detail Kontrak</span>
                </a>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between">
            <span>{{ session('success') }}</span>
            <button type="button" @click="$el.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900">&times;</button>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold flex items-center justify-between">
            <span>{{ session('error') }}</span>
            <button type="button" @click="$el.parentElement.remove()" class="text-rose-600 hover:text-rose-900">&times;</button>
        </div>
    @endif

    @if($project)
        <!-- Ringkasan Customer & Kontrak Header Banner -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-5 gap-4">
            <div class="space-y-0.5">
                <span class="text-[10.5px] font-bold text-slate-400 uppercase tracking-wider">Customer / Client</span>
                <p class="text-sm font-black text-slate-900 truncate">{{ $project->client }}</p>
                <p class="text-[11px] text-slate-500 truncate">{{ $project->location ?: 'On-Site Klien' }}</p>
            </div>
            <div class="space-y-0.5">
                <span class="text-[10.5px] font-bold text-slate-400 uppercase tracking-wider">Siklus PM</span>
                <p class="text-sm font-black text-slate-900">{{ $pmCompleted }} / {{ $pmTotal }} PM Selesai</p>
                <p class="text-[11px] font-semibold text-emerald-700">{{ $pmProgress }}% Progres</p>
            </div>
            <div class="space-y-0.5">
                <span class="text-[10.5px] font-bold text-slate-400 uppercase tracking-wider">Kuota Tiket CM</span>
                <p class="text-sm font-black text-[#8F0A0D]">{{ $cmUsed }} / {{ $cmQuota >= 999 ? '∞' : $cmQuota }} Tiket</p>
                <p class="text-[11px] font-semibold text-slate-600">Sisa Kuota: {{ $cmRemaining }} Tiket</p>
            </div>
            <div class="space-y-0.5">
                <span class="text-[10.5px] font-bold text-slate-400 uppercase tracking-wider">Durasi Kontrak</span>
                <p class="text-sm font-black text-slate-900">
                    @if($project->service_start_date && $project->service_end_date)
                        {{ \Carbon\Carbon::parse($project->service_start_date)->format('M Y') }} - {{ \Carbon\Carbon::parse($project->service_end_date)->format('M Y') }}
                    @else
                        1 Tahun Kontrak
                    @endif
                </p>
                <p class="text-[11px] text-slate-500">Coverage: {{ $project->sla_coverage_hours ?: '24x7' }}</p>
            </div>
            <div class="col-span-2 sm:col-span-4 lg:col-span-1 flex lg:justify-end items-center">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-rose-50 text-[#8F0A0D] border border-rose-200 text-xs font-black">
                    <span class="w-2 h-2 rounded-full bg-[#8F0A0D]"></span>
                    SLA Target: {{ $project->sla_target_percentage ?: '99.5' }}%
                </span>
            </div>
        </div>

        <!-- DUA KOLOM UTAMA: PREVENTIVE MAINTENANCE (KIRI) vs CORRECTIVE MAINTENANCE (KANAN) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            <!-- ========================================== -->
            <!-- SISI KIRI: PREVENTIVE MAINTENANCE (PM)     -->
            <!-- ========================================== -->
            <div class="lg:col-span-6 space-y-5">
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 space-y-6">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span>
                            <h2 class="text-sm font-black text-slate-900 uppercase tracking-wider">Preventive Maintenance (PM)</h2>
                        </div>
                        <span class="text-xs font-bold px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-200">
                            {{ $pmTotal }}x Visit ({{ str_replace('_', ' ', $project->pm_frequency_type ?: 'per 3 bulan') }})
                        </span>
                    </div>

                    <!-- TIMELINE VISUAL PROGRESS (Sesuai Sketsa User: Start -> PM1 -> PM2 -> PM3 -> PM4 -> End (25% progress)) -->
                    <div class="p-4 rounded-2xl bg-slate-50/80 border border-slate-200/80 space-y-3">
                        <div class="flex items-center justify-between text-xs font-bold text-slate-700">
                            <span>Timeline Kunjungan Berkala</span>
                            <span class="text-[#8F0A0D] font-black text-sm">{{ $pmProgress }}% Progres</span>
                        </div>

                        <!-- Progress Bar & Nodes -->
                        <div class="relative py-4">
                            <!-- Background line -->
                            <div class="absolute top-1/2 left-4 right-4 h-1.5 bg-slate-200 -translate-y-1/2 rounded-full"></div>
                            <!-- Active fill line -->
                            <div class="absolute top-1/2 left-4 h-1.5 bg-[#8F0A0D] -translate-y-1/2 rounded-full transition-all duration-700" style="width: calc({{ min(100, $pmProgress) }}% * 0.85);"></div>

                            <div class="relative flex items-center justify-between px-2">
                                <!-- Start Node -->
                                <div class="flex flex-col items-center">
                                    <div class="w-6 h-6 rounded-full bg-slate-800 text-white flex items-center justify-center text-[10px] font-bold shadow-xs">
                                        S
                                    </div>
                                    <span class="text-[10px] font-bold text-slate-500 mt-1">Start</span>
                                </div>

                                <!-- PM Nodes (1..4) -->
                                @foreach($pmCycles as $c)
                                    @php
                                        $isDone = ($c->activity_status === 'Completed');
                                        $isInProg = ($c->activity_status === 'In Progress');
                                    @endphp
                                    <div class="flex flex-col items-center cursor-pointer group" @click="openPmModal({{ json_encode($c) }})">
                                        <div class="w-7 h-7 rounded-full flex items-center justify-center text-[11px] font-black transition-all shadow-xs {{ $isDone ? 'bg-[#8F0A0D] text-white ring-2 ring-rose-200' : ($isInProg ? 'bg-amber-500 text-white ring-2 ring-amber-200 animate-pulse' : 'bg-white text-slate-500 border border-slate-300 group-hover:border-[#8F0A0D]') }}">
                                            @if($isDone)
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                            @else
                                                {{ $c->cycle_number }}
                                            @endif
                                        </div>
                                        <span class="text-[10.5px] font-bold mt-1 {{ $isDone ? 'text-slate-900 font-black' : 'text-slate-500' }}">PM {{ $c->cycle_number }}</span>
                                    </div>
                                @endforeach

                                <!-- End Node -->
                                <div class="flex flex-col items-center">
                                    <div class="w-6 h-6 rounded-full bg-slate-300 text-slate-600 flex items-center justify-center text-[10px] font-bold shadow-xs">
                                        E
                                    </div>
                                    <span class="text-[10px] font-bold text-slate-500 mt-1">End</span>
                                </div>
                            </div>
                        </div>

                        <p class="text-[11px] text-slate-400 text-center font-medium">Klik pada salah satu node siklus PM di atas untuk mengupdate status / dokumen.</p>
                    </div>

                    <!-- DAFTAR KARTU SIKLUS PM (PM 1, PM 2, PM 3, PM 4) -->
                    <!-- Sesuai Sketsa User: PM 1 -> Activity [status], Laporan [status][dokumen], BA/Penagihan [status][dokumen] -->
                    <div class="space-y-4">
                        @foreach($pmCycles as $cycle)
                            @php
                                $isDone = ($cycle->activity_status === 'Completed');
                                $isInProg = ($cycle->activity_status === 'In Progress');
                            @endphp
                            <div class="p-4 rounded-xl border {{ $isDone ? 'border-emerald-200 bg-emerald-50/20' : 'border-slate-200 bg-white' }} space-y-3 transition hover:shadow-2xs">
                                <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
                                    <div class="flex items-center gap-2">
                                        <span class="w-6 h-6 rounded-lg {{ $isDone ? 'bg-emerald-600 text-white' : 'bg-slate-200 text-slate-700' }} flex items-center justify-center text-xs font-black">
                                            {{ $cycle->cycle_number }}
                                        </span>
                                        <h3 class="text-xs font-black text-slate-900">{{ $cycle->cycle_label }}</h3>
                                    </div>
                                    <button type="button" @click="openPmModal({{ json_encode($cycle) }})" class="px-2.5 py-1 rounded-lg text-[11px] font-bold text-[#8F0A0D] hover:bg-rose-50 border border-rose-200 transition cursor-pointer">
                                        Update Siklus
                                    </button>
                                </div>

                                <div class="space-y-2 text-xs">
                                    {{-- 1. Activity Row --}}
                                    <div class="flex items-center justify-between py-1 border-b border-slate-50">
                                        <span class="text-slate-500 font-medium">Activity (Inspeksi / Visit):</span>
                                        <div class="flex items-center gap-2">
                                            @if($isDone)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10.5px] font-bold bg-emerald-100 text-emerald-800">Selesai</span>
                                                <span class="text-[11px] text-slate-500">{{ $cycle->actual_date ? \Carbon\Carbon::parse($cycle->actual_date)->format('d/m/Y') : '—' }}</span>
                                            @elseif($isInProg)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10.5px] font-bold bg-amber-100 text-amber-800">In Progress</span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10.5px] font-bold bg-slate-100 text-slate-600">Terjadwal</span>
                                                <span class="text-[11px] text-slate-400">{{ $cycle->scheduled_date ? \Carbon\Carbon::parse($cycle->scheduled_date)->format('d/m/Y') : '—' }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- 2. Laporan PM Row --}}
                                    <div class="flex items-center justify-between py-1 border-b border-slate-50">
                                        <span class="text-slate-500 font-medium">Laporan PM:</span>
                                        <div class="flex items-center gap-2">
                                            @if($cycle->report_status === 'Approved')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10.5px] font-bold bg-emerald-100 text-emerald-800">Approved</span>
                                            @elseif($cycle->report_status === 'Submitted')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10.5px] font-bold bg-blue-100 text-blue-800">Submitted</span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10.5px] font-bold bg-slate-100 text-slate-500">Pending</span>
                                            @endif

                                            @if($cycle->report_file)
                                                <a href="{{ asset('storage/' . $cycle->report_file) }}" target="_blank" class="px-2 py-0.5 rounded bg-slate-100 hover:bg-slate-200 text-slate-800 text-[10.5px] font-bold transition flex items-center gap-1">
                                                    <svg class="w-3 h-3 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                    <span>PDF Laporan</span>
                                                </a>
                                            @else
                                                <button type="button" @click="openPmModal({{ json_encode($cycle) }})" class="text-[10.5px] text-[#8F0A0D] font-bold hover:underline cursor-pointer">
                                                    + Upload PDF
                                                </button>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- 3. BA / Penagihan Row --}}
                                    <div class="flex items-center justify-between py-1">
                                        <span class="text-slate-500 font-medium">BA / Penagihan (Milestone):</span>
                                        <div class="flex items-center gap-2">
                                            @if(in_array($cycle->billing_ba_status, ['Signed', 'Invoiced']))
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10.5px] font-bold bg-emerald-100 text-emerald-800">{{ $cycle->billing_ba_status }}</span>
                                            @elseif($cycle->billing_ba_status === 'Submitted')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10.5px] font-bold bg-blue-100 text-blue-800">Submitted</span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10.5px] font-bold bg-slate-100 text-slate-500">Pending</span>
                                            @endif

                                            @if($cycle->billing_ba_file)
                                                <a href="{{ asset('storage/' . $cycle->billing_ba_file) }}" target="_blank" class="px-2 py-0.5 rounded bg-slate-100 hover:bg-slate-200 text-slate-800 text-[10.5px] font-bold transition flex items-center gap-1">
                                                    <svg class="w-3 h-3 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                    <span>Dokumen BA</span>
                                                </a>
                                            @else
                                                <button type="button" @click="openPmModal({{ json_encode($cycle) }})" class="text-[10.5px] text-[#8F0A0D] font-bold hover:underline cursor-pointer">
                                                    + Upload BA
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- SISI KANAN: CORRECTIVE MAINTENANCE (CM)    -->
            <!-- ========================================== -->
            <div class="lg:col-span-6 space-y-5">
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 space-y-5">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#8F0A0D]"></span>
                            <h2 class="text-sm font-black text-slate-900 uppercase tracking-wider">Corrective Maintenance (CM)</h2>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-black px-2.5 py-1 rounded-lg bg-rose-50 text-[#8F0A0D] border border-rose-200">
                                Kuota: {{ $cmQuota >= 999 ? 'Unlimited' : $cmQuota . ' Tiket' }}
                            </span>
                            @if($cmRemaining > 0 || $cmQuota >= 999)
                                <button type="button" @click="isCmModalOpen = true" class="px-3 py-1.5 rounded-lg bg-[#8F0A0D] hover:bg-[#73080A] text-white text-xs font-bold transition shadow-xs cursor-pointer">
                                    + Catat Tiket CM
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Quota Usage Bar -->
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 space-y-2">
                        <div class="flex items-center justify-between text-xs font-bold text-slate-700">
                            <span>Utilisasi Tiket Insiden / Perbaikan</span>
                            <span>{{ $cmUsed }} dari {{ $cmQuota >= 999 ? '∞' : $cmQuota }} Tiket Terpakai</span>
                        </div>
                        <div class="w-full bg-slate-200 h-2.5 rounded-full overflow-hidden">
                            <div class="bg-[#8F0A0D] h-2.5 rounded-full transition-all" style="width: {{ $cmQuota >= 999 ? '15%' : min(100, round(($cmUsed / max(1, $cmQuota)) * 100)) }}%"></div>
                        </div>
                        <div class="flex items-center justify-between text-[11px] text-slate-500">
                            <span>Sisa Tiket: <strong class="text-emerald-700 font-bold">{{ $cmRemaining }} Tiket</strong></span>
                            <span>MTTR Target: <strong>{{ $project->mttr_hours ?: 4 }} Jam</strong></span>
                        </div>
                    </div>

                    <!-- DAFTAR KEGIATAN CM (1, 2, 3, ... sampai 18 tiket) Sesuai Sketsa User -->
                    <!-- Sesuai Sketsa User: 1. Kegiatan upgrade os: [tugas][dokumen][selesai] -->
                    <div class="space-y-3">
                        @forelse($cmTickets as $idx => $ticket)
                            <div class="p-3.5 rounded-xl border border-slate-200 bg-white hover:border-slate-300 transition shadow-2xs space-y-2">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex items-start gap-2.5 min-w-0">
                                        <span class="w-6 h-6 rounded-lg bg-rose-50 text-[#8F0A0D] border border-rose-200 flex items-center justify-center text-xs font-black shrink-0 mt-0.5">
                                            {{ $idx + 1 }}
                                        </span>
                                        <div class="min-w-0">
                                            <h4 class="text-xs font-bold text-slate-900 leading-snug">{{ $ticket->title }}</h4>
                                            <div class="flex flex-wrap items-center gap-2 mt-1">
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-mono font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                                    {{ $ticket->ticket_number }}
                                                </span>
                                                <span class="text-[11px] text-slate-400">
                                                    {{ $ticket->incident_date ? \Carbon\Carbon::parse($ticket->incident_date)->format('d M Y') : $ticket->created_at->format('d M Y') }}
                                                </span>
                                                @if($ticket->assignedEngineer)
                                                    <span class="text-[11px] text-slate-500 font-medium">PIC: {{ $ticket->assignedEngineer->name }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Status Badge (Sesuai Sketsa: [selesai]) -->
                                    <div class="shrink-0">
                                        @if(in_array($ticket->status, ['Resolved', 'Closed']))
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10.5px] font-black bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                Selesai
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10.5px] font-black bg-amber-50 text-amber-700 border border-amber-200">
                                                {{ $ticket->status }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Action Buttons (Sesuai Sketsa: [tugas] [dokumen] [selesai]) -->
                                <div class="flex items-center justify-between pt-2 border-t border-slate-100 text-[11px]">
                                    <div class="text-slate-500 line-clamp-1 max-w-[260px]">
                                        {{ $ticket->description }}
                                    </div>
                                    <div class="flex items-center gap-1.5 shrink-0">
                                        <a href="{{ route('ms.tickets.index', ['search' => $ticket->ticket_number]) }}" class="px-2 py-1 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition flex items-center gap-1 text-[10.5px]">
                                            <svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                            <span>Tugas / Tiket</span>
                                        </a>
                                        @if($ticket->attachment_path)
                                            <a href="{{ asset('storage/' . $ticket->attachment_path) }}" target="_blank" class="px-2 py-1 rounded bg-rose-50 hover:bg-rose-100 text-[#8F0A0D] font-bold transition border border-rose-200 flex items-center gap-1 text-[10.5px]">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                                <span>Dokumen BAP</span>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-8 text-center rounded-xl border border-dashed border-slate-200 text-slate-400 text-xs">
                                Belum ada insiden Corrective Maintenance (CM) yang dicatat pada proyek ini.
                            </div>
                        @endforelse

                        <!-- Placeholder slot kuota berikutnya -->
                        @if($cmRemaining > 0 && $cmTickets->count() < 18)
                            @for($i = $cmTickets->count() + 1; $i <= min(18, $cmQuota); $i++)
                                <div class="p-3 rounded-xl border border-dashed border-slate-200/80 bg-slate-50/40 flex items-center justify-between text-xs text-slate-400">
                                    <div class="flex items-center gap-2">
                                        <span class="w-6 h-6 rounded-lg bg-slate-100 text-slate-400 flex items-center justify-center text-xs font-bold">{{ $i }}</span>
                                        <span>Slot Kuota {{ $i }} — Belum Terpakai</span>
                                    </div>
                                    <button type="button" @click="isCmModalOpen = true" class="text-[11px] font-bold text-slate-500 hover:text-[#8F0A0D] transition cursor-pointer">
                                        + Gunakan Kuota
                                    </button>
                                </div>
                            @endfor
                        @endif
                    </div>
                </div>
            </div>

        </div>
    @else
        <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center text-slate-500">
            Pilih proyek untuk melihat monitoring Preventive & Corrective Maintenance.
        </div>
    @endif

    <!-- MODAL UPDATE SIKLUS PM -->
    <template x-teleport="body">
        <div x-show="isPmModalOpen" x-cloak class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 max-h-[90vh] overflow-y-auto" @click.away="isPmModalOpen = false">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
                    <div>
                        <p class="text-[11px] font-bold text-emerald-700 uppercase tracking-wider">Update Realisasi PM</p>
                        <h3 class="text-base font-bold text-slate-900" x-text="activeCycle ? activeCycle.cycle_label : 'Siklus PM'"></h3>
                    </div>
                    <button @click="isPmModalOpen = false" class="text-slate-400 hover:text-slate-700 p-1 rounded-lg">&times;</button>
                </div>

                <form :action="'/managed-service/monitoring/pm-cycles/' + (activeCycle ? activeCycle.id : '')" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Status Kegiatan (Activity) <span class="text-[#8F0A0D]">*</span></label>
                        <select name="activity_status" :value="activeCycle ? activeCycle.activity_status : 'Scheduled'" required class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-xs font-bold text-slate-800 focus:outline-none focus:border-[#8F0A0D] cursor-pointer">
                            <option value="Scheduled">Scheduled (Terjadwal)</option>
                            <option value="In Progress">In Progress (Sedang Dikerjakan)</option>
                            <option value="Completed">Completed (Selesai Dilaksanakan)</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Realisasi Visit</label>
                            <input type="date" name="actual_date" :value="activeCycle ? activeCycle.actual_date : ''" class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-800 focus:outline-none focus:border-[#8F0A0D]">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Engineer Pelaksana</label>
                            <select name="assigned_engineer_id" class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs font-medium text-slate-800 focus:outline-none focus:border-[#8F0A0D] cursor-pointer">
                                <option value="">Pilih Teknisi</option>
                                @foreach($engineers as $eng)
                                    <option value="{{ $eng->id }}">{{ $eng->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan Kegiatan / Temuan Inspeksi</label>
                        <textarea name="activity_notes" rows="2" placeholder="Catatan kondisi perangkat keras, link kelistrikan, suhu ruang server..." class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-800 focus:outline-none focus:border-[#8F0A0D]" x-text="activeCycle ? activeCycle.activity_notes : ''"></textarea>
                    </div>

                    <!-- Dokumen Laporan PM -->
                    <div class="p-3.5 rounded-xl border border-slate-200 bg-slate-50/50 space-y-2.5">
                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-bold text-slate-800">Laporan Maintenance Berkala</label>
                            <select name="report_status" :value="activeCycle ? activeCycle.report_status : 'Pending'" class="px-2 py-1 bg-white border border-slate-300 rounded-lg text-[11px] font-bold text-slate-700 cursor-pointer">
                                <option value="Pending">Pending</option>
                                <option value="Draft">Draft</option>
                                <option value="Submitted">Submitted</option>
                                <option value="Approved">Approved</option>
                            </select>
                        </div>
                        <input type="file" name="report_file" class="w-full px-3 py-1.5 bg-white border border-slate-300 rounded-xl text-xs text-slate-800 cursor-pointer">
                    </div>

                    <!-- Dokumen BA / Penagihan -->
                    <div class="p-3.5 rounded-xl border border-slate-200 bg-slate-50/50 space-y-2.5">
                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-bold text-slate-800">BA / Penagihan (Milestone)</label>
                            <select name="billing_ba_status" :value="activeCycle ? activeCycle.billing_ba_status : 'Pending'" class="px-2 py-1 bg-white border border-slate-300 rounded-lg text-[11px] font-bold text-slate-700 cursor-pointer">
                                <option value="Pending">Pending</option>
                                <option value="Draft">Draft</option>
                                <option value="Submitted">Submitted</option>
                                <option value="Signed">Signed (Ditandatangani)</option>
                                <option value="Invoiced">Invoiced (Ditagihkan)</option>
                            </select>
                        </div>
                        <input type="file" name="billing_ba_file" class="w-full px-3 py-1.5 bg-white border border-slate-300 rounded-xl text-xs text-slate-800 cursor-pointer">
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                        <button type="button" @click="isPmModalOpen = false" class="px-4 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition cursor-pointer">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold transition cursor-pointer">Simpan Siklus PM</button>
                    </div>
                </form>
            </div>
        </div>
    </template>

    <!-- MODAL CATAT TIKET CM BARU -->
    <template x-teleport="body">
        <div x-show="isCmModalOpen" x-cloak class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200" @click.away="isCmModalOpen = false">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
                    <div>
                        <p class="text-[11px] font-bold text-[#8F0A0D] uppercase tracking-wider">Corrective Maintenance</p>
                        <h3 class="text-base font-bold text-slate-900">Catat Tiket Insiden / CM Baru</h3>
                    </div>
                    <button @click="isCmModalOpen = false" class="text-slate-400 hover:text-slate-700 p-1 rounded-lg">&times;</button>
                </div>

                <form action="{{ route('ms.monitoring.cm.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <input type="hidden" name="project_id" value="{{ $project ? $project->id : '' }}">

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Judul Kegiatan / Insiden <span class="text-[#8F0A0D]">*</span></label>
                        <input type="text" name="title" required placeholder="Contoh: Kegiatan upgrade OS & patching security switch" class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-xs text-slate-800 placeholder:text-slate-400 focus:outline-none focus:border-[#8F0A0D]">
                    </div>

                    <div class="grid grid-cols-2 gap-3.5">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Prioritas / SLA Tier <span class="text-[#8F0A0D]">*</span></label>
                            <select name="priority" required class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs font-bold text-slate-800 focus:outline-none focus:border-[#8F0A0D] cursor-pointer">
                                <option value="Platinum">Platinum (1 Jam)</option>
                                <option value="Gold" selected>Gold (4 Jam)</option>
                                <option value="Silver">Silver (8 Jam)</option>
                                <option value="Bronze">Bronze (24 Jam)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Kejadian</label>
                            <input type="date" name="incident_date" value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-800 focus:outline-none focus:border-[#8F0A0D]">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Teknisi / Engineer Penanggung Jawab</label>
                        <select name="assigned_to" class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs font-medium text-slate-800 focus:outline-none focus:border-[#8F0A0D] cursor-pointer">
                            <option value="">Pilih Teknisi</option>
                            @foreach($engineers as $eng)
                                <option value="{{ $eng->id }}">{{ $eng->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Deskripsi Masalah / Solusi</label>
                        <textarea name="description" rows="2" placeholder="Uraian pekerjaan perbaikan atau upgrade perangkat..." class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-800 focus:outline-none focus:border-[#8F0A0D]"></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Upload Berkas / BAP (Opsional)</label>
                        <input type="file" name="attachment" class="w-full px-3 py-1.5 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800 cursor-pointer">
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                        <button type="button" @click="isCmModalOpen = false" class="px-4 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition cursor-pointer">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-[#8F0A0D] hover:bg-[#73080A] text-white text-xs font-bold transition cursor-pointer">Simpan Tiket CM</button>
                    </div>
                </form>
            </div>
        </div>
    </template>
</div>
@endsection
