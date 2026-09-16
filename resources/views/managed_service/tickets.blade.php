@extends('layouts.app')

@section('title', 'Tiket Insiden & SLA - Managed Service')

@push('styles')
<style>
    :root {
        --ipnet-primary: #8F0A0D;
        --ipnet-primary-hover: #73080A;
    }
    .ipnet-card {
        background-color: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04), 0 4px 12px rgba(0, 0, 0, 0.02);
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .btn-ipnet-gradient {
        background: linear-gradient(135deg, #8F0A0D 0%, #B81525 100%);
        color: #FFFFFF;
        transition: all 0.2s ease;
    }
    .btn-ipnet-gradient:hover {
        background: linear-gradient(135deg, #7A080B 0%, #9E0E1D 100%);
        box-shadow: 0 6px 16px rgba(143, 10, 13, 0.25);
        transform: translateY(-1px);
    }
</style>
@endpush

@section('content')
<div class="flex h-screen overflow-hidden bg-[#F8FAFC] font-sans" x-data="ticketManager()" x-cloak>
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Tiket Insiden & Layanan SLA'])
        
        <div class="p-4 sm:p-6 lg:p-7 space-y-6 max-w-[1600px] mx-auto animate-fade-in">
            
            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between text-[13px] font-semibold shadow-xs">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-600 flex-shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            {{-- Top Action Bar & Filter --}}
            <div class="ipnet-card p-4 sm:p-5 flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4">
                <form method="GET" action="{{ route('ms.tickets.index') }}" class="flex-1 flex flex-col sm:flex-row items-center gap-3">
                    <div class="relative w-full sm:max-w-xs">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#94A3B8]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari No. Tiket, Judul, Klien, Pelapor..." 
                               class="w-full pl-10 pr-4 py-2 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition">
                    </div>

                    <select name="type" onchange="this.form.submit()" class="w-full sm:w-auto px-3.5 py-2 bg-white border border-[#CBD5E1] rounded-xl text-[12.5px] font-bold text-[#334155] cursor-pointer">
                        <option value="all">Semua Tipe</option>
                        <option value="Incident" {{ request('type') == 'Incident' ? 'selected' : '' }}>Incident</option>
                        <option value="Service Request" {{ request('type') == 'Service Request' ? 'selected' : '' }}>Service Request</option>
                        <option value="Change Request" {{ request('type') == 'Change Request' ? 'selected' : '' }}>Change Request</option>
                    </select>

                    <select name="status" onchange="this.form.submit()" class="w-full sm:w-auto px-3.5 py-2 bg-white border border-[#CBD5E1] rounded-xl text-[12.5px] font-bold text-[#334155] cursor-pointer">
                        <option value="all">Semua Status</option>
                        <option value="Open" {{ request('status') == 'Open' ? 'selected' : '' }}>Open</option>
                        <option value="In Progress" {{ request('status') == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="Resolved" {{ request('status') == 'Resolved' ? 'selected' : '' }}>Resolved</option>
                        <option value="Closed" {{ request('status') == 'Closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                </form>

                <div class="flex items-center gap-3">
                    <button @click="openCreateModal()" class="btn-ipnet-gradient px-4 py-2.5 rounded-xl font-bold text-[12.5px] flex items-center gap-2 cursor-pointer shadow-md">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        <span>Buat Tiket Baru</span>
                    </button>
                </div>
            </div>

            {{-- Tickets Table --}}
            <div class="ipnet-card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-[#E2E8F0] bg-[#F8FAFC] text-[11px] font-extrabold tracking-wider text-[#64748B] uppercase">
                                <th class="py-3.5 px-5">NO. TIKET & KLIEN</th>
                                <th class="py-3.5 px-5">JUDUL & RINGKASAN MASALAH</th>
                                <th class="py-3.5 px-5">PRIORITAS & TIPE</th>
                                <th class="py-3.5 px-5">ENGINEER PIC</th>
                                <th class="py-3.5 px-5">SLA DEADLINE</th>
                                <th class="py-3.5 px-5 text-right">STATUS / AKSI</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#F1F5F9] text-[13px]">
                            @forelse($tickets as $t)
                                <tr class="hover:bg-[#F8FAFC] transition-colors">
                                    <td class="py-4 px-5 whitespace-nowrap">
                                        <div class="font-mono font-extrabold text-[#1E293B]">{{ $t->ticket_number }}</div>
                                        <div class="text-[12px] text-[#64748B] font-semibold">{{ $t->client_name }}</div>
                                    </td>
                                    <td class="py-4 px-5">
                                        <div class="font-bold text-[#1E293B]">{{ $t->title }}</div>
                                        <div class="text-[11.5px] text-[#64748B] line-clamp-1 mt-0.5">{{ $t->description ?: 'Tidak ada deskripsi tambahan.' }}</div>
                                    </td>
                                    <td class="py-4 px-5 whitespace-nowrap space-y-1">
                                        <div>
                                            <span class="px-2 py-0.5 rounded-md text-[10.5px] font-bold 
                                                {{ $t->type === 'Incident' ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-blue-50 text-blue-700 border border-blue-200' }}">
                                                {{ $t->type }}
                                            </span>
                                        </div>
                                        <div>
                                            <span class="px-2 py-0.5 rounded-md text-[10.5px] font-bold 
                                                {{ str_contains($t->priority, 'P1') ? 'bg-rose-100 text-rose-800 border border-rose-200' : (str_contains($t->priority, 'P2') ? 'bg-amber-50 text-amber-800 border border-amber-200' : 'bg-[#F1F5F9] text-[#475569] border border-[#CBD5E1]') }}">
                                                {{ $t->priority }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="py-4 px-5 whitespace-nowrap text-[12px]">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-[#FEF2F2] text-[#8F0A0D] font-bold border border-[#FECACA]">
                                            {{ $t->assignedEngineer?->name ?: 'Belum Ditugaskan' }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-5 whitespace-nowrap text-[12px]">
                                        <div class="font-extrabold {{ $t->sla_deadline && now()->gt($t->sla_deadline) && !in_array($t->status, ['Resolved', 'Closed']) ? 'text-red-600' : 'text-[#1E293B]' }}">
                                            {{ $t->sla_deadline ? $t->sla_deadline->format('d M Y, H:i') : '—' }}
                                        </div>
                                    </td>
                                    <td class="py-4 px-5 text-right whitespace-nowrap">
                                        <div class="flex items-center justify-end gap-2">
                                            <span class="px-2.5 py-1 rounded-full text-[11px] font-bold
                                                {{ $t->status === 'Resolved' || $t->status === 'Closed' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                                                {{ $t->status }}
                                            </span>
                                            <button @click="openUpdateModal({{ $t }})" class="px-3 py-1 bg-white hover:bg-[#F8FAFC] border border-[#CBD5E1] text-[#1E293B] font-bold text-[12px] rounded-lg transition shadow-2xs cursor-pointer">
                                                Update
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-12 text-center text-[#64748B] text-[13px]">
                                        Tidak ada data tiket insiden yang sesuai kriteria.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($tickets instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    <div class="p-4 border-t border-[#E2E8F0] bg-white">
                        {{ $tickets->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>

    {{-- MODAL CREATE TICKET --}}
    <template x-teleport="body">
        <div x-show="isCreateModalOpen" 
             x-cloak 
             class="fixed inset-0 z-50 bg-[#0F172A]/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-[#E2E8F0] space-y-4 max-h-[90vh] overflow-y-auto" @click.away="isCreateModalOpen = false">
                <div class="flex items-center justify-between border-b border-[#E2E8F0] pb-3.5">
                    <div>
                        <p class="text-[#8F0A0D] text-[11px] font-bold uppercase tracking-wider">Tiket Baru</p>
                        <h3 class="text-[16px] font-bold text-[#1E293B]">Buat Tiket Insiden / Request Baru</h3>
                    </div>
                    <button @click="isCreateModalOpen = false" class="text-[#94A3B8] hover:text-[#1E293B] p-1 rounded-lg hover:bg-[#F1F5F9] transition cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form action="{{ route('ms.tickets.store') }}" method="POST" class="space-y-3.5 text-[12.5px]">
                    @csrf
                    <div>
                        <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">Nama Instansi / Klien <span class="text-[#8F0A0D]">*</span></label>
                        <input type="text" name="client_name" required placeholder="Contoh: Bank Mandiri, PT Telkom"
                               class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition">
                    </div>

                    <div>
                        <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">Judul Tiket / Insiden <span class="text-[#8F0A0D]">*</span></label>
                        <input type="text" name="title" required placeholder="Contoh: Flapping Link SFP Port 24"
                               class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div>
                            <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">Tipe Layanan <span class="text-[#8F0A0D]">*</span></label>
                            <select name="type" class="w-full px-3.5 py-2.5 bg-white border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition cursor-pointer">
                                <option value="Incident">Incident (Gangguan)</option>
                                <option value="Service Request">Service Request (Permintaan)</option>
                                <option value="Change Request">Change Request (Perubahan)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">Tingkat Prioritas (SLA) <span class="text-[#8F0A0D]">*</span></label>
                            <select name="priority" class="w-full px-3.5 py-2.5 bg-white border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition cursor-pointer">
                                <option value="P1 - Critical">P1 - Critical (SLA 1 Jam)</option>
                                <option value="P2 - Major" selected>P2 - Major (SLA 4 Jam)</option>
                                <option value="P3 - Minor">P3 - Minor (SLA 8 Jam)</option>
                                <option value="P4 - Low">P4 - Low (SLA 24 Jam)</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">Perangkat CI Terkait (Opsional)</label>
                        <select name="asset_id" class="w-full px-3.5 py-2.5 bg-white border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition cursor-pointer">
                            <option value="">-- Pilih Perangkat (Opsional) --</option>
                            @foreach($assets as $a)
                                <option value="{{ $a->id }}">{{ $a->device_name }} ({{ $a->client_name }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div>
                            <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">Nama Pelapor</label>
                            <input type="text" name="reported_by" placeholder="Bpk. Hendra (IT Ops)"
                                   class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition">
                        </div>
                        <div>
                            <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">No. Kontak / WA</label>
                            <input type="text" name="contact_phone" placeholder="0812-xxxx-xxxx"
                                   class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition">
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">Tugaskan ke Engineer PIC</label>
                        <select name="assigned_to" class="w-full px-3.5 py-2.5 bg-white border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition cursor-pointer">
                            <option value="">-- Pilih Teknisi (Opsional) --</option>
                            @foreach($engineers as $eng)
                                <option value="{{ $eng->id }}">{{ $eng->name }} ({{ $eng->roles->pluck('name')->first() }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">Rincian Deskripsi Masalah</label>
                        <textarea name="description" rows="3" placeholder="Jelaskan kronologi kendala atau permintaan..."
                                  class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition resize-none"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-3.5 border-t border-[#E2E8F0]">
                        <button type="button" @click="isCreateModalOpen = false" class="px-4 py-2.5 text-[12.5px] font-bold text-[#475569] hover:bg-[#F1F5F9] border border-[#CBD5E1] rounded-xl transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="btn-ipnet-gradient px-5 py-2.5 text-[12.5px] font-bold rounded-xl shadow-md cursor-pointer">
                            Terbitkan Tiket
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>

    {{-- MODAL UPDATE STATUS & RESOLUSI TIKET --}}
    <template x-teleport="body">
        <div x-show="isUpdateModalOpen" 
             x-cloak 
             class="fixed inset-0 z-50 bg-[#0F172A]/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-[#E2E8F0] space-y-4 max-h-[90vh] overflow-y-auto" @click.away="isUpdateModalOpen = false">
                <div class="flex items-center justify-between border-b border-[#E2E8F0] pb-3.5">
                    <div>
                        <span class="text-[11px] font-bold uppercase tracking-wider text-[#8F0A0D]">Tiket Service Update</span>
                        <h3 class="text-[16px] font-bold text-[#1E293B]" x-text="activeTicket.ticket_number + ' - ' + activeTicket.title"></h3>
                    </div>
                    <button @click="isUpdateModalOpen = false" class="text-[#94A3B8] hover:text-[#1E293B] p-1 rounded-lg hover:bg-[#F1F5F9] transition cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form :action="'/managed-service/tickets/' + activeTicket.id" method="POST" class="space-y-3.5 text-[12.5px]">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">Status Tiket <span class="text-[#8F0A0D]">*</span></label>
                        <select name="status" x-model="activeTicket.status" class="w-full px-3.5 py-2.5 bg-white border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition cursor-pointer">
                            <option value="Open">Open</option>
                            <option value="In Progress">In Progress</option>
                            <option value="Pending Vendor">Pending Vendor / Sparepart</option>
                            <option value="Resolved">Resolved (Terselesaikan)</option>
                            <option value="Closed">Closed (Ditutup)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">Tugaskan ke Engineer PIC</label>
                        <select name="assigned_to" x-model="activeTicket.assigned_to" class="w-full px-3.5 py-2.5 bg-white border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition cursor-pointer">
                            <option value="">-- Pilih Teknisi --</option>
                            @foreach($engineers as $eng)
                                <option value="{{ $eng->id }}">{{ $eng->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">Akar Masalah (Root Cause Analysis)</label>
                        <input type="text" name="root_cause" x-model="activeTicket.root_cause" placeholder="Contoh: Kabel fiber optic tertekuk, Memory leak modul X"
                               class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition">
                    </div>

                    <div>
                        <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">Catatan Tindakan & Resolusi</label>
                        <textarea name="resolution_notes" x-model="activeTicket.resolution_notes" rows="3" placeholder="Tuliskan tindakan teknis yang telah diambil..."
                                  class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition resize-none"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-3.5 border-t border-[#E2E8F0]">
                        <button type="button" @click="isUpdateModalOpen = false" class="px-4 py-2.5 text-[12.5px] font-bold text-[#475569] hover:bg-[#F1F5F9] border border-[#CBD5E1] rounded-xl transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="btn-ipnet-gradient px-5 py-2.5 text-[12.5px] font-bold rounded-xl shadow-md cursor-pointer">
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
