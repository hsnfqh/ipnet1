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
    @keyframes fadeUpStagger {
        0% { opacity: 0; transform: translateY(16px); }
        100% { opacity: 1; transform: translateY(0); }
    }
    .anim-fade-up {
        animation: fadeUpStagger 0.5s cubic-bezier(0.16, 1, 0.3, 1) both;
    }
    .anim-delay-1 { animation-delay: 0.06s !important; }
    .anim-delay-2 { animation-delay: 0.12s !important; }
    .anim-delay-3 { animation-delay: 0.18s !important; }
    .anim-delay-4 { animation-delay: 0.24s !important; }
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

            <!-- ========================================================== -->
            <!-- SECTION HEADER & FILTER CONTROLS                           -->
            <!-- ========================================================== -->
            <div class="ipnet-card p-5 sm:p-6 anim-fade-up anim-delay-1">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 pb-4 mb-4 border-b border-[#E2E8F0]">
                    <div>
                        <p class="text-[#8F0A0D] text-[12px] font-bold inline-flex items-center uppercase tracking-wider">
                            <span class="w-2 h-2 rounded-full bg-[#8F0A0D] inline-block mr-2"></span> MANAJEMEN TIKET GANGGUAN
                        </p>
                        <h2 class="text-[20px] font-bold text-[#1E293B] tracking-tight mt-0.5">Daftar Tiket & Respon SLA</h2>
                        <p class="text-[13px] text-[#64748B] mt-0.5">Monitoring status tiket insiden, permohonan layanan teknis, dan SLA penanganan per klien</p>
                    </div>

                    <div class="flex flex-wrap items-center gap-3 shrink-0">
                        <div class="px-3.5 py-1.5 rounded-full bg-[#F8FAFC] border border-[#E2E8F0] text-[12px] font-bold text-[#1E293B] flex items-center gap-1.5 shadow-xs">
                            <span class="text-[#64748B]">Total Tiket:</span>
                            <span class="text-[#8F0A0D] font-extrabold">{{ $tickets->count() }}</span>
                        </div>

                        <button @click="openCreateModal()" class="btn-ipnet-gradient px-4 py-2.5 rounded-xl font-bold text-[13px] flex items-center gap-2 cursor-pointer shadow-md">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                            <span>Buat Tiket Baru</span>
                        </button>
                    </div>
                </div>

                <!-- Filter Controls -->
                <form method="GET" action="{{ route('ms.tickets.index') }}" class="flex flex-col sm:flex-row flex-wrap items-center gap-3">
                    <div class="relative w-full sm:w-72">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#94A3B8]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari No. Tiket, Judul, Klien, Pelapor..." 
                               class="w-full pl-10 pr-3 py-2 rounded-xl border border-[#CBD5E1] text-[12.5px] font-medium text-[#1E293B] bg-white outline-none hover:border-[#94A3B8] focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition-all shadow-xs">
                    </div>

                    <select name="type" onchange="this.form.submit()" class="w-full sm:w-56 px-3 py-2 rounded-xl border border-[#CBD5E1] text-[12.5px] font-semibold text-[#1E293B] bg-white outline-none hover:border-[#94A3B8] focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition-all shadow-xs cursor-pointer">
                        <option value="all">Semua Tipe Layanan</option>
                        <option value="Preventive Maintenance" {{ in_array(request('type'), ['Preventive Maintenance', 'Preventive']) ? 'selected' : '' }}>Preventive Maintenance (PM)</option>
                        <option value="Corrective Maintenance" {{ in_array(request('type'), ['Corrective Maintenance', 'Corrective', 'Incident']) ? 'selected' : '' }}>Corrective Maintenance (CM)</option>
                    </select>
                    
                    <select name="status" onchange="this.form.submit()" class="w-full sm:w-44 px-3 py-2 rounded-xl border border-[#CBD5E1] text-[12.5px] font-semibold text-[#1E293B] bg-white outline-none hover:border-[#94A3B8] focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition-all shadow-xs cursor-pointer">
                        <option value="all">Semua Status</option>
                        <option value="Open" {{ request('status') == 'Open' ? 'selected' : '' }}>Open</option>
                        <option value="In Progress" {{ request('status') == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="Resolved" {{ request('status') == 'Resolved' ? 'selected' : '' }}>Resolved</option>
                        <option value="Closed" {{ request('status') == 'Closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                </form>
            </div>

            {{-- Tickets Table --}}
            <div class="ipnet-card overflow-hidden anim-fade-up anim-delay-2">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-[#E2E8F0] bg-[#F8FAFC] text-[11px] font-extrabold tracking-wider text-[#64748B] uppercase">
                                <th class="py-3.5 px-5">NO. TIKET & KLIEN</th>
                                <th class="py-3.5 px-5">JUDUL & RINGKASAN MASALAH</th>
                                <th class="py-3.5 px-5">TIPE LAYANAN</th>
                                <th class="py-3.5 px-5">TEKNISI PIC</th>
                                <th class="py-3.5 px-5">SLA DEADLINE</th>
                                <th class="py-3.5 px-5 text-center">STATUS</th>
                                <th class="py-3.5 px-5 text-right">AKSI</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#F1F5F9] text-[13px]">
                            @forelse($tickets as $t)
                                @php $tTier = $t->sla_tier_info; @endphp
                                <tr class="hover:bg-[#F8FAFC] transition-colors">
                                    <td class="py-4 px-5 whitespace-nowrap">
                                        <div class="font-mono font-extrabold text-[#1E293B] text-[13px]">{{ $t->ticket_number }}</div>
                                        <div class="text-[12px] text-[#64748B] font-semibold flex items-center gap-1.5 mt-0.5">
                                            <span>{{ $t->client_name }}</span>
                                            @if($tTier)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-[#8F0A0D] text-white shadow-2xs">
                                                    <span>{{ $tTier['tier'] }}</span>
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="py-4 px-5">
                                        <div class="font-bold text-[#1E293B] text-[13.5px]">{{ $t->title }}</div>
                                        <div class="text-[11.5px] text-[#64748B] line-clamp-1 mt-0.5">{{ $t->description ?: 'Tidak ada deskripsi tambahan.' }}</div>
                                    </td>
                                    <td class="py-4 px-5 whitespace-nowrap">
                                        @if(in_array($t->type, ['Preventive Maintenance', 'Preventive']))
                                            <span class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                                Preventive (PM)
                                            </span>
                                        @elseif(in_array($t->type, ['Corrective Maintenance', 'Corrective', 'Incident']))
                                            <span class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-red-50 text-red-700 border border-red-200">
                                                Corrective (CM)
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                                {{ $t->type }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-5 whitespace-nowrap">
                                        @if($t->assignedEngineer)
                                            @php
                                                $nameParts = explode(' ', trim($t->assignedEngineer->name));
                                                $initials = strtoupper(substr($nameParts[0], 0, 1) . (isset($nameParts[1]) ? substr($nameParts[1], 0, 1) : ''));
                                            @endphp
                                            <div class="flex items-center gap-2">
                                                <div class="w-6 h-6 rounded-full bg-[#8F0A0D] text-white flex items-center justify-center text-[10px] font-extrabold shrink-0 shadow-2xs">
                                                    {{ $initials }}
                                                </div>
                                                <span class="font-bold text-[#1E293B] text-[12.5px]">{{ $t->assignedEngineer->name }}</span>
                                            </div>
                                        @else
                                            <div class="flex items-center gap-2 text-[#94A3B8]">
                                                <div class="w-6 h-6 rounded-full bg-[#F1F5F9] border border-[#CBD5E1] text-[#94A3B8] flex items-center justify-center text-[10px] font-bold shrink-0">
                                                    -
                                                </div>
                                                <span class="text-[12px] font-medium italic">Belum Ditugaskan</span>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="py-4 px-5 whitespace-nowrap text-[12px]">
                                        <div class="font-mono font-bold text-[#B91C1C]">
                                            {{ $t->sla_deadline ? $t->sla_deadline->format('d M Y') : '—' }}
                                        </div>
                                    </td>
                                    <td class="py-4 px-5 text-center whitespace-nowrap">
                                        @if($t->status === 'Resolved')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                Resolved
                                            </span>
                                        @elseif($t->status === 'Closed')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-slate-100 text-slate-700 border border-slate-300">
                                                <span class="w-1.5 h-1.5 rounded-full bg-slate-500"></span>
                                                Closed
                                            </span>
                                        @elseif($t->status === 'In Progress')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span>
                                                In Progress
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                Open
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-5 text-right whitespace-nowrap">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <button @click="openUpdateModal({{ json_encode($t) }})" 
                                                    title="Update Status / Resolusi"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-[#CBD5E1] rounded-lg text-[12px] font-bold text-[#1E293B] hover:bg-[#F8FAFC] hover:border-[#8F0A0D] hover:text-[#8F0A0D] transition shadow-xs cursor-pointer">
                                                <svg class="w-3.5 h-3.5 text-[#64748B]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                                <span>Update</span>
                                            </button>

                                            <button type="button" 
                                                    @click="confirmDelete({{ json_encode($t) }})"
                                                    title="Hapus Tiket"
                                                    class="p-1.5 bg-white border border-[#CBD5E1] rounded-lg text-[#64748B] hover:text-red-600 hover:border-red-300 hover:bg-red-50 transition shadow-xs cursor-pointer">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-12 text-center text-[#64748B] text-[13px]">
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
            <div class="bg-white rounded-2xl max-w-lg w-full shadow-2xl border border-[#E2E8F0] max-h-[90vh] flex flex-col overflow-hidden" @click.away="isCreateModalOpen = false">
                <!-- Fixed Header -->
                <div class="flex items-center justify-between border-b border-[#E2E8F0] p-5 sm:p-6 pb-4 shrink-0 bg-white">
                    <div>
                        <p class="text-[#8F0A0D] text-[11px] font-bold uppercase tracking-wider">Tiket Maintenance Baru</p>
                        <h3 class="text-[16px] font-bold text-[#1E293B]">Buat Tiket Maintenance (PM / CM)</h3>
                    </div>
                    <button @click="isCreateModalOpen = false" class="text-[#94A3B8] hover:text-[#1E293B] p-1.5 rounded-lg hover:bg-[#F1F5F9] transition cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Form with Scrollable Body & Fixed Footer -->
                <form action="{{ route('ms.tickets.store') }}" method="POST" class="flex flex-col flex-1 min-h-0 overflow-hidden">
                    @csrf
                    <div class="p-5 sm:p-6 overflow-y-auto space-y-3.5 text-[12.5px] flex-1">
                        <div>
                            <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">Nama Instansi / Klien <span class="text-[#8F0A0D]">*</span></label>
                            <input type="text" name="client_name" required placeholder="Contoh: Bank Mandiri, PT Telkom"
                                   class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition">
                        </div>

                        <div>
                            <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">Judul Tiket / Permasalahan <span class="text-[#8F0A0D]">*</span></label>
                            <input type="text" name="title" required placeholder="Contoh: Flapping Link SFP Port 24 / Jadwal PM Q3"
                                   class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <div>
                                <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">Tanggal Tiket <span class="text-[#8F0A0D]">*</span></label>
                                <input type="date" name="created_at" value="{{ now()->format('Y-m-d') }}" required
                                       class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition cursor-pointer">
                            </div>
                            <div>
                                <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">Tier SLA Klien <span class="text-[#8F0A0D]">*</span></label>
                                <select name="priority" class="w-full px-3.5 py-2.5 bg-white border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition cursor-pointer">
                                    <option value="Platinum">Platinum</option>
                                    <option value="Gold" selected>Gold</option>
                                    <option value="Silver">Silver</option>
                                    <option value="Bronze">Bronze</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <div>
                                <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">Tipe Layanan <span class="text-[#8F0A0D]">*</span></label>
                                <select name="type" class="w-full px-3.5 py-2.5 bg-white border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition cursor-pointer">
                                    <option value="Preventive Maintenance">Preventive Maintenance (PM)</option>
                                    <option value="Corrective Maintenance" selected>Corrective Maintenance (CM)</option>
                                </select>
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
                            <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">Tugaskan ke Teknisi PIC</label>
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
                    </div>

                    <!-- Fixed Footer -->
                    <div class="flex items-center justify-end gap-2.5 p-4 px-6 border-t border-[#E2E8F0] bg-[#F8FAFC] shrink-0">
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
            <div class="bg-white rounded-2xl max-w-lg w-full shadow-2xl border border-[#E2E8F0] max-h-[90vh] flex flex-col overflow-hidden" @click.away="isUpdateModalOpen = false">
                <!-- Fixed Header -->
                <div class="flex items-center justify-between border-b border-[#E2E8F0] p-5 sm:p-6 pb-4 shrink-0 bg-white">
                    <div>
                        <span class="text-[11px] font-bold uppercase tracking-wider text-[#8F0A0D]">Tiket Service Update</span>
                        <h3 class="text-[16px] font-bold text-[#1E293B]" x-text="activeTicket.ticket_number + ' - ' + activeTicket.title"></h3>
                    </div>
                    <button @click="isUpdateModalOpen = false" class="text-[#94A3B8] hover:text-[#1E293B] p-1.5 rounded-lg hover:bg-[#F1F5F9] transition cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Form with Scrollable Body & Fixed Footer -->
                <form :action="'/managed-service/tickets/' + activeTicket.id" method="POST" class="flex flex-col flex-1 min-h-0 overflow-hidden">
                    @csrf
                    @method('PUT')

                    <div class="p-5 sm:p-6 overflow-y-auto space-y-3.5 text-[12.5px] flex-1">
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
                            <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">Tugaskan ke Teknisi PIC</label>
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
                    </div>

                    <!-- Fixed Footer -->
                    <div class="flex items-center justify-end gap-2.5 p-4 px-6 border-t border-[#E2E8F0] bg-[#F8FAFC] shrink-0">
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

    {{-- MODAL KONFIRMASI HAPUS TIKET (POPUP) --}}
    <template x-teleport="body">
        <div x-show="isDeleteModalOpen"
             x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-[#0F172A]/60 z-[99999] flex items-center justify-center p-4 backdrop-blur-xs"
             @click.self="isDeleteModalOpen = false"
             @keydown.escape.window="isDeleteModalOpen = false">

            <div class="bg-white rounded-2xl w-[420px] max-w-full p-6 text-left shadow-[0_20px_60px_rgba(15,23,42,0.25)] border border-[#E2E8F0] animate-fade-in-up">
                <div class="w-12 h-12 rounded-full bg-[#FEF2F2] flex items-center justify-center mx-auto mb-4 text-[#8F0A0D]">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>

                <h3 class="text-center font-display text-[16px] font-bold text-[#1E293B] mb-1.5">Yakin Hapus Tiket?</h3>
                <p class="text-center text-[12.5px] text-[#64748B] mb-6 break-words">
                    Apakah Anda yakin ingin menghapus tiket <strong class="text-[#1E293B]" x-text="ticketToDelete ? ticketToDelete.ticket_number : ''"></strong> (<span x-text="ticketToDelete ? ticketToDelete.title : ''"></span>)? Tindakan ini tidak dapat dibatalkan.
                </p>

                <form :action="'/managed-service/tickets/' + (ticketToDelete ? ticketToDelete.id : '')" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="flex gap-2.5">
                        <button type="button"
                                @click="isDeleteModalOpen = false"
                                class="flex-1 py-2.5 px-4 rounded-xl bg-white text-[#334155] border border-[#CBD5E1] font-bold text-[12.5px] hover:bg-[#F8FAFC] transition cursor-pointer text-center">
                            Batal
                        </button>
                        <button type="submit"
                                class="flex-1 py-2.5 px-4 rounded-xl btn-ipnet-gradient font-bold text-[12.5px] transition cursor-pointer shadow-md text-white text-center">
                            Ya, Hapus
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
            isDeleteModalOpen: false,
            activeTicket: {},
            ticketToDelete: null,
            openCreateModal() {
                this.isCreateModalOpen = true;
            },
            openUpdateModal(ticket) {
                this.activeTicket = { ...ticket };
                this.isUpdateModalOpen = true;
            },
            confirmDelete(ticket) {
                this.ticketToDelete = ticket;
                this.isDeleteModalOpen = true;
            }
        }
    }
</script>
@endsection
