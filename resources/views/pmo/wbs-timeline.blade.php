@extends('layouts.app')

@section('title', 'WBS & Project Timeline Gantt Chart - PMO')

@push('styles')
<style>
    :root {
        --ipnet-primary: #8F0A0D;
        --ipnet-primary-hover: #73080A;
    }
</style>
@endpush

@section('content')
<div class="flex h-screen overflow-hidden bg-[#F8FAFC] font-sans" x-data="{ currentProject: '{{ $selectedProject ? $selectedProject->id : '' }}' }" x-cloak>
    @include('components.sidebar')

    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'WBS Project Timeline & Gantt Chart'])

        <div class="p-4 sm:p-6 lg:p-7 space-y-6 max-w-[1680px] mx-auto animate-fade-in">
            
            {{-- Header Card & Project Selector --}}
            <div class="bg-white rounded-2xl border border-slate-200/90 p-5 sm:p-6 shadow-xs">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    
                    <div class="space-y-1 flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-red-50 text-[#8F0A0D] border border-red-200">
                                <span class="w-2 h-2 rounded-full bg-[#8F0A0D]"></span>
                                Work Breakdown Structure (WBS) & Scheduling
                            </span>
                            <span class="text-slate-300">&bull;</span>
                            <span class="text-xs text-slate-500 font-medium">Model Microsoft Project Professional & Gantt Timeline</span>
                        </div>
                        <h1 class="text-[21px] sm:text-[23px] font-extrabold text-[#1E293B] tracking-tight">
                            Manajemen Jadwal & Timeline Proyek (Gantt Chart)
                        </h1>
                        <p class="text-xs text-slate-500">
                            Kelola tahapan inisiasi, desain teknis, implementasi jaringan, dan penutupan proyek dalam tampilan interaktif.
                        </p>
                    </div>

                    {{-- Dropdown Selector Proyek --}}
                    <div class="flex items-center gap-3 shrink-0">
                        <div class="text-right hidden sm:block">
                            <span class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider">Pilih Proyek Aktif:</span>
                            <span class="text-xs font-semibold text-slate-700">Portofolio Implementasi</span>
                        </div>
                        <select x-model="currentProject" 
                                @change="window.location.href = '{{ route('pmo.wbs.index') }}?project_id=' + currentProject"
                                class="px-3.5 py-2 text-xs font-bold rounded-xl border border-slate-300 bg-white text-slate-800 shadow-2xs focus:outline-none focus:border-[#8F0A0D] transition min-w-[240px]">
                            @forelse($projects as $p)
                                @php
                                    $pId = is_array($p) ? $p['id'] : $p->id;
                                    $pName = is_array($p) ? $p['name'] : $p->name;
                                    $pClient = is_array($p) ? ($p['client'] ?? 'Client') : ($p->client ?? 'Client');
                                @endphp
                                <option value="{{ $pId }}" {{ $selectedProject && $selectedProject->id == $pId ? 'selected' : '' }}>
                                    {{ $pName }} ({{ $pClient }})
                                </option>
                            @empty
                                <option value="">Belum ada proyek aktif</option>
                            @endforelse
                        </select>

                        @if($selectedProject)
                        <a href="{{ route('pmo.projects.show', $selectedProject->id) }}" 
                           title="Buka Dashboard Detail & Kurva S Proyek Ini"
                           class="px-3.5 py-2 text-xs font-bold text-[#8F0A0D] bg-red-50 hover:bg-red-100 rounded-xl border border-red-200 transition shrink-0 inline-flex items-center gap-1.5 shadow-2xs">
                            <span>Buka Detail & Kurva S</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </a>
                        @endif
                    </div>

                </div>

                {{-- Selected Project Mini Spec Chips --}}
                @if($selectedProject)
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-4 mt-4 border-t border-slate-100 text-xs">
                    <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200/80">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Klien</span>
                        <span class="font-extrabold text-slate-800 truncate block">{{ $selectedProject->client ?: 'Client IPNET' }}</span>
                    </div>
                    <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200/80">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Periode Pelaksanaan</span>
                        <span class="font-bold text-slate-800 truncate block">
                            {{ $selectedProject->start_date ? $selectedProject->start_date->format('d M Y') : '26 Agu 2026' }} s/d 
                            {{ $selectedProject->deadline ? $selectedProject->deadline->format('d M Y') : '20 Nov 2026' }}
                        </span>
                    </div>
                    <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200/80">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Tim Pelaksana</span>
                        <span class="font-bold text-slate-800 truncate block">
                            {{ $selectedProject->division ? preg_replace('/^Divisi\s+/i', 'Tim ', $selectedProject->division->name) : 'Tim Network & PMO' }}
                        </span>
                    </div>
                    <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200/80">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Project Manager</span>
                        <span class="font-bold text-[#8F0A0D] truncate block">
                            {{ $selectedProject->pm ? $selectedProject->pm->name : 'Rizki (PM)' }}
                        </span>
                    </div>
                </div>
                @endif
            </div>

            {{-- Component: Gantt Chart & WBS Split-View --}}
            <x-pmo-wbs-gantt :project="$selectedProject" :wbsTasks="$wbsTasks" :canEdit="true" :isStandalone="true" />

        </div>
    </div>
</div>
@endsection
