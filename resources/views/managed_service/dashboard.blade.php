@extends('layouts.app')

@section('title', 'Managed Service - Service Delivery & Operate')

@section('content')
<div class="flex h-screen overflow-hidden" x-data="msDashboard()" x-cloak>
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto bg-[#FAF9F8]">
        @include('components.topbar', ['title' => 'Managed Service (Operate & SLA)'])
        
        <div class="p-4 sm:p-5 lg:p-[26px] animate-fade-in space-y-6">
            
            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between text-sm shadow-sm">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            {{-- Top Action Header (Clean Lead-Engineer Style) --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h2 class="text-sm font-semibold text-gray-400">Ringkasan Operasional & SLA</h2>
                </div>

                <div class="flex items-center gap-2.5">
                    <button @click="isTicketModalOpen = true" 
                            class="px-4 py-2 bg-[#C81E2C] hover:brightness-105 active:translate-y-[1px] text-white text-xs font-bold rounded-xl shadow-[0_4px_12px_rgba(200,30,44,0.2)] transition inline-flex items-center gap-2 whitespace-nowrap">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Buat Tiket Baru</span>
                    </button>
                    <button @click="isAssetModalOpen = true" 
                            class="px-4 py-2 bg-white hover:bg-gray-50 border border-gray-200 text-gray-800 text-xs font-bold rounded-xl shadow-xs transition inline-flex items-center gap-2 whitespace-nowrap">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Tambah CI Aset</span>
                    </button>
                </div>
            </div>

            {{-- 4 Primary Metric Cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                
                {{-- Card 1: SLA Score --}}
                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-[0_1px_3px_rgba(0,0,0,0.03)] flex flex-col justify-between space-y-4">
                    <div class="space-y-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-600 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-[11.5px] font-medium text-gray-400">Pencapaian Kepatuhan SLA</p>
                            <h3 class="text-2xl font-extrabold text-emerald-600 tracking-tight mt-1">
                                {{ $slaScore }}%
                            </h3>
                        </div>
                    </div>
                    <div class="flex items-center justify-between text-xs pt-3 border-t border-gray-50 text-gray-400">
                        <span class="font-bold text-emerald-600">Target Standard 99.5%</span>
                        <span>SLA Terjaga</span>
                    </div>
                </div>

                {{-- Card 2: Tiket Insiden Aktif --}}
                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-[0_1px_3px_rgba(0,0,0,0.03)] flex flex-col justify-between space-y-4">
                    <div class="space-y-3">
                        <div class="w-10 h-10 rounded-xl bg-rose-50 border border-rose-100 text-rose-600 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-[11.5px] font-medium text-gray-400">Tiket Aktif Dalam Penanganan</p>
                            <h3 class="text-2xl font-extrabold text-gray-900 tracking-tight mt-1">
                                {{ $openTickets }} <span class="text-sm font-semibold text-gray-400">Tiket</span>
                            </h3>
                        </div>
                    </div>
                    <div class="flex items-center justify-between text-xs pt-3 border-t border-gray-50 text-gray-400">
                        <span class="font-bold {{ $criticalTickets > 0 ? 'text-red-600' : 'text-emerald-600' }}">
                            {{ $criticalTickets > 0 ? $criticalTickets . ' P1 Critical' : '0 P1 Critical' }}
                        </span>
                        <span>{{ $resolvedTickets }} Selesai Bulan Ini</span>
                    </div>
                </div>

                {{-- Card 3: Status CI Aset Klien --}}
                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-[0_1px_3px_rgba(0,0,0,0.03)] flex flex-col justify-between space-y-4">
                    <div class="space-y-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-100 text-blue-600 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-[11.5px] font-medium text-gray-400">Aset & Configuration Items (CI)</p>
                            <h3 class="text-2xl font-extrabold text-blue-600 tracking-tight mt-1">
                                {{ $onlineAssets }} / {{ $totalAssets }} <span class="text-sm font-semibold text-gray-400">Online</span>
                            </h3>
                        </div>
                    </div>
                    <div class="flex items-center justify-between text-xs pt-3 border-t border-gray-50 text-gray-400">
                        <span class="font-bold {{ $warningAssets > 0 ? 'text-amber-600' : 'text-gray-500' }}">
                            {{ $warningAssets }} Perlu Perhatian
                        </span>
                        <span>Data Center & Site</span>
                    </div>
                </div>

                {{-- Card 4: Kontrak Operasional --}}
                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-[0_1px_3px_rgba(0,0,0,0.03)] flex flex-col justify-between space-y-4">
                    <div class="space-y-3">
                        <div class="w-10 h-10 rounded-xl bg-purple-50 border border-purple-100 text-purple-600 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-[11.5px] font-medium text-gray-400">Kontrak Layanan Berjalan</p>
                            <h3 class="text-2xl font-extrabold text-purple-700 tracking-tight mt-1">
                                {{ $operateProjects->count() }} <span class="text-sm font-semibold text-gray-400">Klien Aktif</span>
                            </h3>
                        </div>
                    </div>
                    <div class="flex items-center justify-between text-xs pt-3 border-t border-gray-50 text-gray-400">
                        <span class="font-bold text-purple-600">Stage 4: Operate</span>
                        <span>SLA Maintenance 24/7</span>
                    </div>
                </div>

            </div>

            {{-- 2 Main Columns: Live Incident Tickets & Active CI Asset Monitoring --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                {{-- Left 2 Cols: Antrean Tiket Insiden & Service Request --}}
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-5 border-b border-gray-100 flex items-center justify-between">
                            <div>
                                <h3 class="font-bold text-gray-900 text-base">Antrean Tiket Insiden & Permintaan Layanan</h3>
                                <p class="text-xs text-gray-400 mt-0.5">Monitoring status respon tiket dan tenggat waktu SLA</p>
                            </div>
                            <a href="{{ route('ms.tickets.index') }}" class="text-xs font-bold text-[#AF1424] hover:underline flex items-center gap-1">
                                Lihat Semua Tiket &rarr;
                            </a>
                        </div>

                        <div class="divide-y divide-gray-100">
                            @forelse($tickets->take(5) as $ticket)
                                <div class="p-4 hover:bg-gray-50/70 transition flex items-start justify-between gap-4">
                                    <div class="space-y-1.5 flex-1 min-w-0">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="font-mono text-xs font-extrabold text-gray-800">{{ $ticket->ticket_number }}</span>
                                            
                                            {{-- Type Badge --}}
                                            <span class="px-2 py-0.5 rounded-md text-[10.5px] font-bold 
                                                {{ $ticket->type === 'Incident' ? 'bg-red-50 text-red-700 border border-red-200' : ($ticket->type === 'Change Request' ? 'bg-purple-50 text-purple-700 border border-purple-200' : 'bg-blue-50 text-blue-700 border border-blue-200') }}">
                                                {{ $ticket->type }}
                                            </span>

                                            {{-- Priority Badge --}}
                                            <span class="px-2 py-0.5 rounded-md text-[10.5px] font-bold 
                                                {{ str_contains($ticket->priority, 'P1') ? 'bg-rose-100 text-rose-800' : (str_contains($ticket->priority, 'P2') ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-700') }}">
                                                {{ $ticket->priority }}
                                            </span>

                                            {{-- Status Badge --}}
                                            <span class="px-2 py-0.5 rounded-md text-[10.5px] font-bold 
                                                {{ $ticket->status === 'Resolved' || $ticket->status === 'Closed' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                                                {{ $ticket->status }}
                                            </span>
                                        </div>

                                        <h4 class="font-bold text-gray-900 text-sm truncate">{{ $ticket->title }}</h4>
                                        
                                        <div class="flex items-center gap-3 text-xs text-gray-500 flex-wrap">
                                            <span class="font-semibold text-gray-700">{{ $ticket->client_name }}</span>
                                            @if($ticket->asset)
                                                <span>• Perangkat: <strong class="text-gray-700">{{ $ticket->asset->device_name }}</strong></span>
                                            @endif
                                            <span>• PIC: <strong class="text-[#AF1424]">{{ $ticket->assignedEngineer?->name ?: 'Belum Ditugaskan' }}</strong></span>
                                        </div>
                                    </div>

                                    <div class="text-right whitespace-nowrap space-y-1 flex-shrink-0">
                                        <div class="text-[11px] font-semibold text-gray-400">SLA Deadline</div>
                                        <div class="text-xs font-bold {{ $ticket->sla_deadline && now()->gt($ticket->sla_deadline) && !in_array($ticket->status, ['Resolved', 'Closed']) ? 'text-red-600' : 'text-gray-800' }}">
                                            {{ $ticket->sla_deadline ? $ticket->sla_deadline->format('d M, H:i') : '—' }}
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="py-12 text-center text-gray-400 text-xs">
                                    Tidak ada tiket insiden yang sedang berjalan saat ini.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Right 1 Col: Live Assets Health & Support Team --}}
                <div class="space-y-6">
                    {{-- Card CI Assets Overview --}}
                    <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="font-bold text-gray-900 text-sm">Status Perangkat Aset (CI)</h3>
                                <p class="text-xs text-gray-400">Kesehatan perangkat klien terpasang</p>
                            </div>
                            <a href="{{ route('ms.assets.index') }}" class="text-xs font-bold text-[#AF1424] hover:underline">Kelola &rarr;</a>
                        </div>

                        <div class="space-y-3 text-xs">
                            @foreach($assets->take(4) as $a)
                                <div class="p-3 rounded-xl bg-gray-50 border border-gray-200/70 flex items-center justify-between">
                                    <div class="space-y-0.5">
                                        <div class="font-bold text-gray-900 truncate max-w-[180px]">{{ $a->device_name }}</div>
                                        <div class="text-[11px] text-gray-500">{{ $a->brand }} {{ $a->model }} • {{ $a->client_name }}</div>
                                    </div>
                                    <span class="px-2 py-0.5 text-[10.5px] font-bold rounded-md 
                                        {{ $a->status === 'Online' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : ($a->status === 'Warning' ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-red-50 text-red-700 border border-red-200') }}">
                                        {{ $a->status }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Card Agenda Preventive Maintenance Terdekat --}}
                    <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="font-bold text-gray-900 text-sm">Agenda Preventive Maintenance</h3>
                                <p class="text-xs text-gray-400">Jadwal inspeksi & pemeliharaan rutin</p>
                            </div>
                            <a href="{{ route('ms.maintenance.index') }}" class="text-xs font-bold text-[#AF1424] hover:underline">Jadwal &rarr;</a>
                        </div>

                        <div class="space-y-3 text-xs">
                            @forelse($upcomingPmSchedules as $sch)
                                <div class="p-3 rounded-xl bg-gray-50 border border-gray-200/70 space-y-1.5">
                                    <div class="flex items-center justify-between">
                                        <span class="font-bold text-gray-900 truncate max-w-[170px]">{{ $sch->title }}</span>
                                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-md bg-blue-50 text-blue-700">
                                            {{ $sch->date ? $sch->date->format('d M') : 'Terjadwal' }}
                                        </span>
                                    </div>
                                    <div class="text-[11px] text-gray-500 truncate">
                                        {{ $sch->project ? $sch->project->name : ($sch->client_name ?: 'Klien Regular') }}
                                    </div>
                                    <div class="text-[10.5px] text-gray-400">
                                        Teknisi: <strong class="text-[#AF1424]">{{ ($sch->engineers && $sch->engineers->isNotEmpty()) ? $sch->engineers->pluck('name')->implode(', ') : ($sch->engineer?->name ?: 'Doris / Tim Maintenance') }}</strong>
                                    </div>
                                </div>
                            @empty
                                <div class="py-6 text-center text-gray-400 text-xs">
                                    Belum ada agenda pemeliharaan terjadwal.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>

    {{-- MODAL CREATE TICKET --}}
    <template x-teleport="body">
        <div x-show="isTicketModalOpen" class="fixed inset-0 z-50 bg-[#0E0D12]/60 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4" @click.away="isTicketModalOpen = false">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <h3 class="text-base font-bold text-gray-900">Buat Tiket Insiden / Request Baru</h3>
                    <button @click="isTicketModalOpen = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form action="{{ route('ms.tickets.store') }}" method="POST" class="space-y-3 text-xs">
                    @csrf
                    <div>
                        <label class="block font-bold text-gray-700 uppercase mb-1">Nama Instansi / Klien *</label>
                        <input type="text" name="client_name" required placeholder="Contoh: Bank Mandiri, PT Telkom"
                               class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-red-500/20">
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 uppercase mb-1">Judul Tiket / Insiden *</label>
                        <input type="text" name="title" required placeholder="Contoh: Flapping Link SFP Port 24"
                               class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-red-500/20">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-gray-700 uppercase mb-1">Tipe Layanan *</label>
                            <select name="type" class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs">
                                <option value="Incident">Incident (Gangguan)</option>
                                <option value="Service Request">Service Request (Permintaan)</option>
                                <option value="Change Request">Change Request (Perubahan)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-bold text-gray-700 uppercase mb-1">Tingkat Prioritas (SLA) *</label>
                            <select name="priority" class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs">
                                <option value="P1 - Critical">P1 - Critical (SLA 1 Jam)</option>
                                <option value="P2 - Major" selected>P2 - Major (SLA 4 Jam)</option>
                                <option value="P3 - Minor">P3 - Minor (SLA 8 Jam)</option>
                                <option value="P4 - Low">P4 - Low (SLA 24 Jam)</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 uppercase mb-1">Perangkat CI Terkait (Opsional)</label>
                        <select name="asset_id" class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs">
                            <option value="">-- Pilih Perangkat (Opsional) --</option>
                            @foreach($assets as $a)
                                <option value="{{ $a->id }}">{{ $a->device_name }} ({{ $a->client_name }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-gray-700 uppercase mb-1">Nama Pelapor</label>
                            <input type="text" name="reported_by" placeholder="Bpk. Hendra (IT Ops)"
                                   class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs">
                        </div>
                        <div>
                            <label class="block font-bold text-gray-700 uppercase mb-1">No. Kontak / WA</label>
                            <input type="text" name="contact_phone" placeholder="0812-xxxx-xxxx"
                                   class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs">
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 uppercase mb-1">Tugaskan ke Engineer PIC</label>
                        <select name="assigned_to" class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs">
                            <option value="">-- Pilih Teknisi (Opsional) --</option>
                            @foreach($maintenanceEngineers as $eng)
                                <option value="{{ $eng->id }}">{{ $eng->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 uppercase mb-1">Rincian Deskripsi Masalah</label>
                        <textarea name="description" rows="3" placeholder="Jelaskan kronologi kendala atau permintaan..."
                                  class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100">
                        <button type="button" @click="isTicketModalOpen = false" class="px-4 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-100 rounded-xl transition">Batal</button>
                        <button type="submit" class="px-5 py-2 text-xs font-bold text-white bg-[#C81E2C] hover:brightness-105 rounded-xl shadow-sm transition">
                            Terbitkan Tiket
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>

    {{-- MODAL CREATE ASSET --}}
    <template x-teleport="body">
        <div x-show="isAssetModalOpen" class="fixed inset-0 z-50 bg-[#0E0D12]/60 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4" @click.away="isAssetModalOpen = false">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <h3 class="text-base font-bold text-gray-900">Tambah Aset Configuration Item (CI) Baru</h3>
                    <button @click="isAssetModalOpen = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form action="{{ route('ms.assets.store') }}" method="POST" class="space-y-3 text-xs">
                    @csrf
                    <div>
                        <label class="block font-bold text-gray-700 uppercase mb-1">Nama Instansi / Klien *</label>
                        <input type="text" name="client_name" required placeholder="Contoh: Bank Mandiri, PT Telkom"
                               class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-red-500/20">
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 uppercase mb-1">Nama Perangkat & Hostname *</label>
                        <input type="text" name="device_name" required placeholder="Contoh: Core DC Switch FortiGate 600E"
                               class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-red-500/20">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-gray-700 uppercase mb-1">Kategori *</label>
                            <select name="category" class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs">
                                <option value="Switch">Switch</option>
                                <option value="Router">Router</option>
                                <option value="Firewall">Firewall</option>
                                <option value="Server">Server</option>
                                <option value="Access Point">Access Point</option>
                                <option value="UPS">UPS</option>
                                <option value="Storage">Storage</option>
                                <option value="Other">Lainnya</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-bold text-gray-700 uppercase mb-1">Status Kesehatan *</label>
                            <select name="status" class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs">
                                <option value="Online">Online</option>
                                <option value="Warning">Warning</option>
                                <option value="Offline">Offline</option>
                                <option value="Maintenance">Maintenance</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-gray-700 uppercase mb-1">Brand / Merek</label>
                            <input type="text" name="brand" placeholder="Cisco, Fortinet, Mikrotik"
                                   class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs">
                        </div>
                        <div>
                            <label class="block font-bold text-gray-700 uppercase mb-1">Model / Tipe</label>
                            <input type="text" name="model" placeholder="FG-600E, C9500"
                                   class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-gray-700 uppercase mb-1">Serial Number</label>
                            <input type="text" name="serial_number" placeholder="SN-123456"
                                   class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs">
                        </div>
                        <div>
                            <label class="block font-bold text-gray-700 uppercase mb-1">IP Address Management</label>
                            <input type="text" name="ip_address" placeholder="10.240.1.1"
                                   class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-gray-700 uppercase mb-1">Lokasi Site</label>
                            <input type="text" name="location_site" placeholder="Data Center Lt. 8"
                                   class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs">
                        </div>
                        <div>
                            <label class="block font-bold text-gray-700 uppercase mb-1">Posisi Rack</label>
                            <input type="text" name="rack_position" placeholder="Rack DC-04 (U18)"
                                   class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs">
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100">
                        <button type="button" @click="isAssetModalOpen = false" class="px-4 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-100 rounded-xl transition">Batal</button>
                        <button type="submit" class="px-5 py-2 text-xs font-bold text-white bg-[#C81E2C] hover:brightness-105 rounded-xl shadow-sm transition">
                            Simpan Aset CI
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>

</div>

<script>
    function msDashboard() {
        return {
            isTicketModalOpen: false,
            isAssetModalOpen: false,
        }
    }
</script>
@endsection
