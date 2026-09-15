@extends('layouts.app')

@section('title', 'Tiket Insiden & SLA - Managed Service')

@section('content')
<div class="flex h-screen overflow-hidden" x-data="ticketManager()" x-cloak>
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto bg-[#FAF9F8]">
        @include('components.topbar', ['title' => 'Tiket Insiden & Layanan SLA'])
        
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

            {{-- Top Action Bar & Filter --}}
            <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4">
                <form method="GET" action="{{ route('ms.tickets.index') }}" class="flex-1 flex flex-col sm:flex-row items-center gap-3">
                    <div class="relative w-full sm:max-w-xs">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari No. Tiket, Judul, Klien, Pelapor..." 
                               class="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition">
                    </div>

                    <select name="type" onchange="this.form.submit()" class="w-full sm:w-auto px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-semibold text-gray-700">
                        <option value="all">Semua Tipe</option>
                        <option value="Incident" {{ request('type') == 'Incident' ? 'selected' : '' }}>Incident</option>
                        <option value="Service Request" {{ request('type') == 'Service Request' ? 'selected' : '' }}>Service Request</option>
                        <option value="Change Request" {{ request('type') == 'Change Request' ? 'selected' : '' }}>Change Request</option>
                    </select>

                    <select name="status" onchange="this.form.submit()" class="w-full sm:w-auto px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs font-semibold text-gray-700">
                        <option value="all">Semua Status</option>
                        <option value="Open" {{ request('status') == 'Open' ? 'selected' : '' }}>Open</option>
                        <option value="In Progress" {{ request('status') == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="Resolved" {{ request('status') == 'Resolved' ? 'selected' : '' }}>Resolved</option>
                        <option value="Closed" {{ request('status') == 'Closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                </form>

                <div class="flex items-center gap-3">
                    <button @click="openCreateModal()" class="px-4 py-2 bg-[#C81E2C] hover:brightness-105 active:translate-y-[1px] text-white text-xs font-bold rounded-xl shadow-[0_4px_12px_rgba(200,30,44,0.2)] transition inline-flex items-center gap-1.5 whitespace-nowrap">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Buat Tiket Baru
                    </button>
                </div>
            </div>

            {{-- Tickets Table --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-gray-100 bg-gray-50/50 text-[11px] font-bold tracking-wider text-gray-500 uppercase">
                                <th class="py-4 px-6">NO. TIKET & KLIEN</th>
                                <th class="py-4 px-6">JUDUL & RINGKASAN MASALAH</th>
                                <th class="py-4 px-6">PRIORITAS & TIPE</th>
                                <th class="py-4 px-6">ENGINEER PIC</th>
                                <th class="py-4 px-6">SLA DEADLINE</th>
                                <th class="py-4 px-6 text-right">STATUS / AKSI</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @forelse($tickets as $t)
                                <tr class="hover:bg-gray-50/60 transition-colors">
                                    <td class="py-4 px-6 whitespace-nowrap">
                                        <div class="font-mono font-extrabold text-gray-900">{{ $t->ticket_number }}</div>
                                        <div class="text-xs text-gray-500 font-medium">{{ $t->client_name }}</div>
                                    </td>
                                    <td class="py-4 px-6">
                                        <div class="font-bold text-gray-900">{{ $t->title }}</div>
                                        <div class="text-xs text-gray-400 line-clamp-1 mt-0.5">{{ $t->description ?: 'Tidak ada deskripsi tambahan.' }}</div>
                                    </td>
                                    <td class="py-4 px-6 whitespace-nowrap space-y-1">
                                        <div>
                                            <span class="px-2 py-0.5 rounded-md text-[10.5px] font-bold 
                                                {{ $t->type === 'Incident' ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-blue-50 text-blue-700 border border-blue-200' }}">
                                                {{ $t->type }}
                                            </span>
                                        </div>
                                        <div>
                                            <span class="px-2 py-0.5 rounded-md text-[10.5px] font-bold 
                                                {{ str_contains($t->priority, 'P1') ? 'bg-rose-100 text-rose-800' : (str_contains($t->priority, 'P2') ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-700') }}">
                                                {{ $t->priority }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="py-4 px-6 whitespace-nowrap text-xs">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-red-50 text-[#AF1424] font-semibold">
                                            {{ $t->assignedEngineer?->name ?: 'Belum Ditugaskan' }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 whitespace-nowrap text-xs">
                                        @if($t->status === 'Resolved' || $t->status === 'Closed')
                                            <div class="text-emerald-700 font-bold flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                <span>{{ $t->resolved_at ? $t->resolved_at->format('d M, H:i') : 'Selesai' }}</span>
                                            </div>
                                            <div class="text-[10.5px] text-gray-400">SLA: {{ $t->sla_met ? 'Tepat Waktu' : 'Terlewati' }}</div>
                                        @else
                                            <div class="font-bold {{ $t->sla_deadline && now()->gt($t->sla_deadline) ? 'text-red-600' : 'text-gray-800' }}">
                                                {{ $t->sla_deadline ? $t->sla_deadline->format('d M, H:i') : '—' }}
                                            </div>
                                            <div class="text-[10.5px] {{ $t->sla_deadline && now()->gt($t->sla_deadline) ? 'text-red-500 font-semibold' : 'text-gray-400' }}">
                                                {{ $t->sla_deadline && now()->gt($t->sla_deadline) ? 'SLA Terlewati' : 'Target SLA' }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6 text-right whitespace-nowrap">
                                        <button @click="openUpdateModal({{ json_encode($t) }})" class="px-3 py-1.5 bg-gray-50 hover:bg-gray-100 text-gray-800 text-xs font-bold rounded-lg border border-gray-200 transition">
                                            {{ $t->status }} &rarr;
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-12 text-center text-gray-400 text-xs">
                                        Tidak ada tiket yang ditemukan pada filter ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($tickets->hasPages())
                    <div class="p-4 border-t border-gray-100">
                        {{ $tickets->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>

    {{-- MODAL CREATE TICKET --}}
    <template x-teleport="body">
        <div x-show="isCreateModalOpen" class="fixed inset-0 z-50 bg-[#0E0D12]/60 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4" @click.away="isCreateModalOpen = false">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <h3 class="text-base font-bold text-gray-900">Buat Tiket Insiden / Request Baru</h3>
                    <button @click="isCreateModalOpen = false" class="text-gray-400 hover:text-gray-600">
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
                            @foreach($engineers as $eng)
                                <option value="{{ $eng->id }}">{{ $eng->name }} ({{ $eng->roles->pluck('name')->first() }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 uppercase mb-1">Rincian Deskripsi Masalah</label>
                        <textarea name="description" rows="3" placeholder="Jelaskan kronologi kendala atau permintaan..."
                                  class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100">
                        <button type="button" @click="isCreateModalOpen = false" class="px-4 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-100 rounded-xl transition">Batal</button>
                        <button type="submit" class="px-5 py-2 text-xs font-bold text-white bg-[#C81E2C] hover:brightness-105 rounded-xl shadow-sm transition">
                            Terbitkan Tiket
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>

    {{-- MODAL UPDATE STATUS & RESOLUSI TIKET --}}
    <template x-teleport="body">
        <div x-show="isUpdateModalOpen" class="fixed inset-0 z-50 bg-[#0E0D12]/60 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4" @click.away="isUpdateModalOpen = false">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <div>
                        <span class="text-[10.5px] font-bold uppercase tracking-wider text-[#AF1424]">Tiket Service Update</span>
                        <h3 class="text-base font-bold text-gray-900" x-text="activeTicket.ticket_number + ' - ' + activeTicket.title"></h3>
                    </div>
                    <button @click="isUpdateModalOpen = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form :action="'/managed-service/tickets/' + activeTicket.id" method="POST" class="space-y-3 text-xs">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block font-bold text-gray-700 uppercase mb-1">Status Tiket *</label>
                        <select name="status" x-model="activeTicket.status" class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs">
                            <option value="Open">Open</option>
                            <option value="In Progress">In Progress</option>
                            <option value="Pending Vendor">Pending Vendor / Sparepart</option>
                            <option value="Resolved">Resolved (Terselesaikan)</option>
                            <option value="Closed">Closed (Ditutup)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 uppercase mb-1">Tugaskan ke Engineer PIC</label>
                        <select name="assigned_to" x-model="activeTicket.assigned_to" class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs">
                            <option value="">-- Pilih Teknisi --</option>
                            @foreach($engineers as $eng)
                                <option value="{{ $eng->id }}">{{ $eng->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 uppercase mb-1">Akar Masalah (Root Cause Analysis)</label>
                        <input type="text" name="root_cause" x-model="activeTicket.root_cause" placeholder="Contoh: Kabel fiber optic tertekuk, Memory leak modul X"
                               class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs">
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 uppercase mb-1">Catatan Tindakan & Resolusi</label>
                        <textarea name="resolution_notes" x-model="activeTicket.resolution_notes" rows="3" placeholder="Tuliskan tindakan teknis yang telah diambil..."
                                  class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100">
                        <button type="button" @click="isUpdateModalOpen = false" class="px-4 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-100 rounded-xl transition">Batal</button>
                        <button type="submit" class="px-5 py-2 text-xs font-bold text-white bg-[#C81E2C] hover:brightness-105 rounded-xl shadow-sm transition">
                            Simpan Pembaruan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>
</div>

<script>
    function ticketManager() {
        return {
            isCreateModalOpen: false,
            isUpdateModalOpen: false,
            activeTicket: {},
            openCreateModal() {
                this.isCreateModalOpen = true;
            },
            openUpdateModal(ticket) {
                this.activeTicket = { ...ticket };
                this.isUpdateModalOpen = true;
            }
        }
    }
</script>
@endsection
