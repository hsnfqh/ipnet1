@extends('layouts.app')

@section('title', 'Daftar Proyek & Kontrak Managed Service')

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb & Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 mb-1">
                <span>Managed Service</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-[#8F0A0D]">Daftar Proyek</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Daftar Proyek & Integrasi Lapangan</h1>
            <p class="text-xs text-slate-500 mt-0.5">Kelola pelaksanaan proyek klien, jadwal visit berkala, dan monitoring progres instalasi</p>
        </div>
        <div class="flex items-center gap-2.5">
            <a href="{{ route('ms.monitoring.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                <span>Monitoring PM / CM</span>
            </a>
        </div>
    </div>

    <!-- Main Card & Filter Controls -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-[#8F0A0D]"></span>
                        <p class="text-[11px] font-bold tracking-wider text-[#8F0A0D] uppercase">MANAJEMEN PORTOFOLIO</p>
                    </div>
                    <h2 class="text-base font-bold text-slate-900 mt-0.5">Daftar Proyek & Kontrak Service</h2>
                    <p class="text-xs text-slate-500">Pilih proyek untuk melihat detail dokumen (SPK, Kontrak, BOQ, BA) atau monitoring SLA.</p>
                </div>
            </div>

            <!-- Filter Controls -->
            <form method="GET" action="{{ route('ms.projects.index') }}" class="flex flex-col md:flex-row flex-wrap items-center justify-between gap-3 pt-2">
                <div class="flex flex-col sm:flex-row flex-wrap items-center gap-2.5 w-full md:w-auto flex-1">
                    <div class="relative w-full sm:w-72">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama project, client, sales, atau lokasi..." 
                               class="w-full pl-10 pr-3.5 py-2 rounded-xl border border-slate-300 text-xs text-slate-800 placeholder:text-slate-400 bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition shadow-2xs">
                    </div>

                    <select name="status" onchange="this.form.submit()" class="w-full sm:w-44 px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800 bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition shadow-2xs cursor-pointer">
                        <option value="all">Semua Status</option>
                        <option value="Active" {{ request('status') === 'Active' ? 'selected' : '' }}>Active / Operate</option>
                        <option value="In Progress" {{ request('status') === 'In Progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="Planning" {{ request('status') === 'Planning' ? 'selected' : '' }}>Planning</option>
                        <option value="Completed" {{ request('status') === 'Completed' ? 'selected' : '' }}>Completed</option>
                    </select>

                    <select name="type" onchange="this.form.submit()" class="w-full sm:w-48 px-3 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-800 bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition shadow-2xs cursor-pointer">
                        <option value="all">Semua Tipe Proyek</option>
                        <option value="Managed Service SLA" {{ request('type') === 'Managed Service SLA' ? 'selected' : '' }}>Managed Service SLA</option>
                        <option value="Maintenance" {{ request('type') === 'Maintenance' ? 'selected' : '' }}>Maintenance Contract</option>
                        <option value="One-Time" {{ request('type') === 'One-Time' ? 'selected' : '' }}>One-Time Project</option>
                    </select>

                    @if(request('search') || (request('status') && request('status') !== 'all') || (request('type') && request('type') !== 'all') || (request('per_page') && request('per_page') != 10))
                        <a href="{{ route('ms.projects.index') }}" class="px-2.5 py-1.5 text-xs font-semibold text-slate-500 hover:text-slate-900 transition">
                            Reset Filter
                        </a>
                    @endif
                </div>

                <div class="flex items-center gap-2 self-end md:self-center shrink-0">
                    <span class="text-xs font-semibold text-slate-500">Tampilkan:</span>
                    <select name="per_page" onchange="this.form.submit()" class="px-2.5 py-1.5 rounded-xl border border-slate-300 text-xs font-bold text-slate-800 bg-white focus:outline-none focus:border-[#8F0A0D] cursor-pointer shadow-2xs">
                        <option value="5" {{ request('per_page', $perPage ?? 10) == 5 ? 'selected' : '' }}>5 baris</option>
                        <option value="10" {{ request('per_page', $perPage ?? 10) == 10 ? 'selected' : '' }}>10 baris</option>
                        <option value="25" {{ request('per_page', $perPage ?? 10) == 25 ? 'selected' : '' }}>25 baris</option>
                        <option value="50" {{ request('per_page', $perPage ?? 10) == 50 ? 'selected' : '' }}>50 baris</option>
                    </select>
                </div>
            </form>
        </div>

        <!-- Table View -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50/80 text-[11px] font-bold tracking-wider text-slate-600 uppercase">
                        <th class="py-3.5 px-5">Nama Project & Tipe</th>
                        <th class="py-3.5 px-4">Client & Sales</th>
                        <th class="py-3.5 px-4">Lokasi</th>
                        <th class="py-3.5 px-4">Durasi & Deadline</th>
                        <th class="py-3.5 px-4">Progress</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-[12.5px]">
                    @forelse($projects as $p)
                        @php
                            $pmCyclesCount = $p->pmCycles->count() ?: ($p->pm_total_cycles ?: 4);
                            $pmCompletedCount = $p->pmCycles->where('activity_status', 'Completed')->count();
                            $progressPct = (int) ($p->progress ?: ($pmCyclesCount > 0 ? round(($pmCompletedCount / $pmCyclesCount) * 100) : 0));
                            
                            $deadlineDate = $p->service_end_date ?: $p->deadline;
                            $formattedDeadline = $deadlineDate ? \Carbon\Carbon::parse($deadlineDate)->translatedFormat('d M Y') : '—';
                            $startDate = $p->service_start_date ?: $p->start_date;
                            
                            $durationText = '—';
                            if ($startDate && $deadlineDate) {
                                $s = \Carbon\Carbon::parse($startDate);
                                $e = \Carbon\Carbon::parse($deadlineDate);
                                $diffMonths = round($s->floatDiffInMonths($e), 1);
                                if ($diffMonths >= 12) {
                                    $durationText = round($diffMonths / 12, 1) . ' Tahun';
                                } else {
                                    $durationText = $diffMonths . ' Bulan';
                                }
                            }
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            {{-- Nama Project & Tipe --}}
                            <td class="py-4 px-5 max-w-sm">
                                <a href="{{ route('ms.projects.show', $p->id) }}" class="font-bold text-slate-900 hover:text-[#8F0A0D] text-sm block transition-colors leading-snug">
                                    {{ $p->name }}
                                </a>
                                <div class="mt-1 flex items-center gap-2">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10.5px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                        {{ $p->project_type ?: 'Managed Service SLA' }}
                                    </span>
                                    @if($p->sla_tier)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10.5px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                            SLA: {{ $p->sla_tier }}
                                        </span>
                                    @endif
                                </div>
                            </td>

                            {{-- Client & Sales --}}
                            <td class="py-4 px-4 whitespace-nowrap">
                                <div class="font-bold text-slate-900">{{ $p->client }}</div>
                                <div class="text-xs text-slate-500 font-medium mt-0.5">
                                    Sales: <span class="text-slate-700 font-semibold">{{ $p->sales_name ?: ($p->creator?->name ?: 'Raiza') }}</span>
                                </div>
                            </td>

                            {{-- Lokasi --}}
                            <td class="py-4 px-4 text-slate-600 max-w-[160px] truncate" title="{{ $p->location ?: 'On-Site Klien' }}">
                                {{ $p->location ?: '—' }}
                            </td>

                            {{-- Durasi & Deadline --}}
                            <td class="py-4 px-4 whitespace-nowrap">
                                <div class="font-semibold text-slate-800">{{ $formattedDeadline }}</div>
                                <div class="text-[11.5px] text-slate-500 mt-0.5">
                                    Durasi: <span class="font-bold text-slate-700">{{ $durationText }}</span>
                                </div>
                            </td>

                            {{-- Progress --}}
                            <td class="py-4 px-4 whitespace-nowrap">
                                <div class="flex items-center gap-2.5 w-36">
                                    <div class="flex-1 bg-slate-200 h-2 rounded-full overflow-hidden">
                                        <div class="bg-[#8F0A0D] h-2 rounded-full transition-all duration-500" style="width: {{ min(100, $progressPct) }}%"></div>
                                    </div>
                                    <span class="text-xs font-extrabold text-slate-800 w-9 text-right">{{ $progressPct }}%</span>
                                </div>
                                <div class="text-[10.5px] text-slate-400 mt-0.5">Progress Layanan</div>
                            </td>

                            {{-- Status --}}
                            <td class="py-4 px-4 whitespace-nowrap">
                                @if(in_array(strtolower($p->status ?? ''), ['active', 'operate']) || in_array(strtolower($p->stage ?? ''), ['active', 'operate']))
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 animate-pulse"></span>
                                        Active
                                    </span>
                                @elseif(in_array(strtolower($p->status ?? ''), ['completed', 'selesai']))
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                                        Completed
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        {{ $p->status ?: 'In Progress' }}
                                    </span>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="py-4 px-5 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('ms.projects.show', $p->id) }}" class="p-2 text-slate-500 hover:text-slate-900 hover:bg-slate-100 rounded-lg transition" title="Lihat Detail & Dokumen Proyek">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    </a>
                                    <a href="{{ route('ms.monitoring.index', ['project_id' => $p->id]) }}" class="p-2 text-[#8F0A0D] hover:bg-rose-50 rounded-lg transition" title="Buka Monitoring PM & CM">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-500 text-sm">
                                Tidak ada proyek Managed Service yang sesuai dengan kriteria filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($projects instanceof \Illuminate\Pagination\LengthAwarePaginator && $projects->hasPages())
            <div class="p-4 border-t border-slate-100 bg-white flex flex-col sm:flex-row items-center justify-between gap-3">
                <div class="text-xs text-slate-500">
                    Menampilkan {{ $projects->firstItem() ?? 0 }} - {{ $projects->lastItem() ?? 0 }} dari total {{ $projects->total() }} proyek
                </div>
                <div>
                    {{ $projects->links() }}
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
