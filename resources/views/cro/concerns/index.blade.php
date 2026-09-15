@extends('layouts.app')

@section('title', 'Concern & Escalation Tracking - CRO')

@section('content')
<div class="flex h-screen overflow-hidden" x-data="croConcernsHandler()" x-cloak>
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto bg-[#FAF9F8]">
        @include('components.topbar', ['title' => 'Customer Concern, Escalation & Orchestration'])
        
        <div class="p-4 sm:p-5 lg:p-[26px] animate-fade-in space-y-5 max-w-[1600px] mx-auto">
            
            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between shadow-sm">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 font-bold px-2">✕</button>
                </div>
            @endif

            {{-- Orchestration Flow Banner --}}
            <div class="bg-white rounded-xl border border-[#E7E5E3] p-4 shadow-[0_1px_2px_rgba(14,13,18,0.05)]">
                <div class="text-[11px] font-bold text-[#75727C] uppercase tracking-[0.3px] mb-2.5">Alur Koordinasi & Penyelesaian (Orchestration Pipeline):</div>
                <div class="grid grid-cols-2 sm:grid-cols-5 gap-2 text-center text-xs">
                    <div class="p-2.5 rounded-lg bg-rose-50 border border-rose-200 text-rose-800 font-bold flex items-center justify-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                        <span>1. Input (Open)</span>
                    </div>
                    <div class="p-2.5 rounded-lg bg-indigo-50 border border-indigo-200 text-indigo-800 font-bold flex items-center justify-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                        <span>2. Dispatched</span>
                    </div>
                    <div class="p-2.5 rounded-lg bg-blue-50 border border-blue-200 text-blue-800 font-bold flex items-center justify-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                        <span>3. In Progress</span>
                    </div>
                    <div class="p-2.5 rounded-lg bg-amber-50 border border-amber-200 text-amber-800 font-bold flex items-center justify-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        <span>4. Konfirmasi Klien</span>
                    </div>
                    <div class="p-2.5 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 font-bold flex items-center justify-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span>5. Closed</span>
                    </div>
                </div>
            </div>

            {{-- Filter & Action Bar --}}
            <div class="flex flex-col sm:flex-row sm:flex-wrap sm:justify-between sm:items-center gap-2.5">
                <form method="GET" action="{{ route('cro.concerns.index') }}" class="flex flex-col sm:flex-row flex-wrap gap-2.5 w-full sm:w-auto">
                    <div class="relative">
                        <svg class="w-3.5 h-3.5 absolute left-3 top-3 text-[#948F99]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" 
                               name="client" 
                               value="{{ request('client') }}"
                               placeholder="Cari Klien / Tiket..." 
                               class="w-full sm:w-56 px-[11px] py-[9px] pl-8 rounded-lg border border-[#E7E5E3] text-[13px] text-[#17151C] outline-none bg-white focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all">
                    </div>
                    <select name="status" onchange="this.form.submit()" 
                            class="w-full sm:w-44 px-[11px] py-[9px] rounded-lg border border-[#E7E5E3] text-[13px] text-[#17151C] outline-none bg-white focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all cursor-pointer">
                        <option value="">Semua Status</option>
                        <option value="Open" {{ request('status') == 'Open' ? 'selected' : '' }}>Open</option>
                        <option value="Dispatched" {{ request('status') == 'Dispatched' ? 'selected' : '' }}>Dispatched</option>
                        <option value="In Progress" {{ request('status') == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="Pending Confirmation" {{ request('status') == 'Pending Confirmation' ? 'selected' : '' }}>Pending Confirm</option>
                        <option value="Closed" {{ request('status') == 'Closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                    <select name="severity" onchange="this.form.submit()" 
                            class="w-full sm:w-44 px-[11px] py-[9px] rounded-lg border border-[#E7E5E3] text-[13px] text-[#17151C] outline-none bg-white focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all cursor-pointer">
                        <option value="">Semua Severity</option>
                        <option value="P1 - Critical" {{ request('severity') == 'P1 - Critical' ? 'selected' : '' }}>P1 - Critical</option>
                        <option value="P2 - High" {{ request('severity') == 'P2 - High' ? 'selected' : '' }}>P2 - High</option>
                        <option value="P3 - Medium" {{ request('severity') == 'P3 - Medium' ? 'selected' : '' }}>P3 - Medium</option>
                        <option value="P4 - Low" {{ request('severity') == 'P4 - Low' ? 'selected' : '' }}>P4 - Low</option>
                    </select>
                    <select name="dept" onchange="this.form.submit()" 
                            class="w-full sm:w-44 px-[11px] py-[9px] rounded-lg border border-[#E7E5E3] text-[13px] text-[#17151C] outline-none bg-white focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all cursor-pointer">
                        <option value="">Semua Unit Internal</option>
                        <option value="Managed Service" {{ request('dept') == 'Managed Service' ? 'selected' : '' }}>Managed Service</option>
                        <option value="PMO" {{ request('dept') == 'PMO' ? 'selected' : '' }}>PMO</option>
                        <option value="Technical / Network" {{ request('dept') == 'Technical / Network' ? 'selected' : '' }}>Technical / Network</option>
                        <option value="Sales / BDM" {{ request('dept') == 'Sales / BDM' ? 'selected' : '' }}>Sales / BDM</option>
                    </select>
                    @if(request('client') || request('status') || request('severity') || request('dept'))
                        <a href="{{ route('cro.concerns.index') }}" 
                           class="px-3 py-2 text-[13px] font-semibold text-gray-500 hover:text-gray-700 self-center">
                            Reset
                        </a>
                    @endif
                </form>

                <button type="button" @click="isCreateModalOpen = true" 
                        class="w-full sm:w-auto justify-center bg-[#C81E2C] text-white shadow-[0_8px_20px_rgba(200,30,44,0.24)] px-[17px] py-[9px] rounded-lg font-semibold text-[13px] flex items-center gap-1.5 hover:brightness-105 active:translate-y-[1px] transition-all cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Buat Tiket Concern Baru</span>
                </button>
            </div>

            {{-- Table Concerns & Escalation --}}
            <div class="bg-white rounded-xl border border-[#E7E5E3] shadow-[0_1px_2px_rgba(14,13,18,0.05)] overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[1100px] border-collapse text-[13px]">
                        <thead>
                            <tr class="bg-[#F1F0EE] border-b border-[#E7E5E3]">
                                <th class="text-left py-3.5 px-4 text-[11px] font-semibold text-[#75727C] uppercase tracking-[0.3px] w-[16%]">Tiket & Klien</th>
                                <th class="text-center py-3.5 px-3 text-[11px] font-semibold text-[#75727C] uppercase tracking-[0.3px] w-[11%] whitespace-nowrap">Severity</th>
                                <th class="text-left py-3.5 px-4 text-[11px] font-semibold text-[#75727C] uppercase tracking-[0.3px] w-[28%]">Judul Masalah & Tindakan</th>
                                <th class="text-left py-3.5 px-4 text-[11px] font-semibold text-[#75727C] uppercase tracking-[0.3px] w-[15%]">Unit & PIC</th>
                                <th class="text-center py-3.5 px-3 text-[11px] font-semibold text-[#75727C] uppercase tracking-[0.3px] w-[12%] whitespace-nowrap">Status Flow</th>
                                <th class="text-center py-3.5 px-3 text-[11px] font-semibold text-[#75727C] uppercase tracking-[0.3px] w-[10%] whitespace-nowrap">Target SLA</th>
                                <th class="text-right py-3.5 px-4 text-[11px] font-semibold text-[#75727C] uppercase tracking-[0.3px] w-[8%] whitespace-nowrap">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#EFEDEB]">
                            @forelse($concerns as $con)
                                <tr class="hover:bg-[#F8F7F6] transition-colors">
                                    <td class="py-3.5 px-4">
                                        <div class="font-mono text-[11px] font-bold text-[#C81E2C]">{{ $con->ticket_number }}</div>
                                        <div class="font-bold text-[#17151C] text-[13px] leading-snug mt-0.5">{{ $con->client_name }}</div>
                                    </td>
                                    <td class="py-3.5 px-3 text-center whitespace-nowrap">
                                        @php $sBadge = $con->severity_badge; @endphp
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10.5px] font-bold border {{ $sBadge['bg'] }} {{ $sBadge['text'] }} {{ $sBadge['border'] }}">
                                            {{ $sBadge['label'] }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <div class="font-semibold text-gray-900 text-[13px] leading-snug">{{ $con->title }}</div>
                                        <div class="text-[11px] text-[#75727C] mt-0.5">Sumber: {{ $con->source }}</div>
                                        @if($con->action_taken)
                                            <div class="text-[11.5px] text-emerald-800 bg-emerald-50/80 p-2 rounded-lg mt-1.5 border border-emerald-200">
                                                <strong>Tindakan:</strong> {{ $con->action_taken }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10.5px] font-semibold bg-gray-100 text-gray-700">
                                            {{ $con->assigned_dept }}
                                        </span>
                                        <div class="text-[11.5px] text-gray-600 mt-1">
                                            {{ $con->assignedUser ? $con->assignedUser->name : 'Belum Ditugaskan' }}
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-3 text-center whitespace-nowrap">
                                        @php $stBadge = $con->status_badge; @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10.5px] font-bold border {{ $stBadge['bg'] }} {{ $stBadge['text'] }} {{ $stBadge['border'] }}">
                                            {{ $stBadge['label'] }}
                                        </span>
                                        @if($con->customer_satisfaction_rating)
                                            <div class="text-[11px] font-bold text-amber-500 mt-1">
                                                {{ str_repeat('⭐', $con->customer_satisfaction_rating) }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-3 text-center whitespace-nowrap text-gray-600 text-[12px] font-medium">
                                        {{ $con->sla_due_date ? $con->sla_due_date->format('d M Y') : '-' }}
                                    </td>
                                    <td class="py-3.5 px-4 text-right whitespace-nowrap space-x-1">
                                        @if(in_array($con->status, ['Open', 'Dispatched']))
                                            <button @click="openDispatchModal({{ $con->id }}, '{{ $con->ticket_number }}', '{{ $con->assigned_dept }}', '{{ $con->assigned_to }}')"
                                                    class="px-2.5 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold rounded-lg border border-indigo-200 transition text-[11px] cursor-pointer" title="Disposisi ke PIC">
                                                Disposisi
                                            </button>
                                        @endif

                                        @if(in_array($con->status, ['In Progress', 'Dispatched']))
                                            <button @click="openResolveModal({{ $con->id }}, '{{ $con->ticket_number }}', '{{ addslashes($con->root_cause ?? '') }}', '{{ addslashes($con->action_taken ?? '') }}')"
                                                    class="px-2.5 py-1 bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold rounded-lg border border-blue-200 transition text-[11px] cursor-pointer" title="Catat Tindakan Selesai">
                                                Tindakan
                                            </button>
                                        @endif

                                        @if($con->status === 'Pending Confirmation')
                                            <button @click="openConfirmModal({{ $con->id }}, '{{ $con->ticket_number }}')"
                                                    class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg shadow-2xs transition text-[11px] cursor-pointer" title="Validasi Konfirmasi Klien">
                                                Konfirmasi
                                            </button>
                                        @endif

                                        <form action="{{ route('cro.concerns.destroy', $con->id) }}" method="POST" onsubmit="return confirm('Hapus concern ini?')" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1 text-gray-400 hover:text-rose-600 rounded-lg hover:bg-rose-50 transition cursor-pointer" title="Hapus">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-10 text-center text-gray-400 text-xs">
                                        Tidak ada tiket concern atau eskalasi ditemukan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($concerns->hasPages())
                    <div class="p-4 border-t border-[#EFEDEB]">
                        {{ $concerns->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>

    {{-- MODAL 1: Create New Concern --}}
    <div x-show="isCreateModalOpen" class="fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 space-y-4 border border-[#E7E5E3] shadow-xl animate-fade-in-up" @click.outside="isCreateModalOpen = false">
            <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                <h3 class="text-base font-bold text-gray-900">Buat Tiket Concern / Eskalasi Baru</h3>
                <button @click="isCreateModalOpen = false" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>

            <form action="{{ route('cro.concerns.store') }}" method="POST" class="space-y-3.5 text-xs">
                @csrf
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Nama Klien / Perusahaan *</label>
                    <input type="text" name="client_name" list="clientList" required class="wms-input" placeholder="Pilih atau ketik nama klien">
                    <datalist id="clientList">
                        @foreach($clients as $c)
                            <option value="{{ $c->name }}"></option>
                        @endforeach
                    </datalist>
                </div>

                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Judul Isu / Concern *</label>
                    <input type="text" name="title" required class="wms-input" placeholder="e.g. Komplain lambatnya respon sparepart">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Tingkat Keparahan (Severity) *</label>
                        <select name="severity" required class="wms-input">
                            <option value="P1 - Critical">P1 - Critical (Downtime/SLA Breach)</option>
                            <option value="P2 - High">P2 - High (Degradasi Layanan)</option>
                            <option value="P3 - Medium" selected>P3 - Medium (Keluhan Operasional)</option>
                            <option value="P4 - Low">P4 - Low (Pertanyaan/Minor)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Sumber Concern *</label>
                        <select name="source" required class="wms-input">
                            <option value="Client Direct">Client Direct (Email/WA/Call)</option>
                            <option value="SLA Breach">SLA Breach / Alarm</option>
                            <option value="Incident Escalation">Incident Escalation</option>
                            <option value="QBR Meeting">QBR / Meeting Klien</option>
                            <option value="Survey Feedback">Survey Feedback</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Disposisi ke Dept Terkait *</label>
                        <select name="assigned_dept" required class="wms-input">
                            <option value="Managed Service">Managed Service (Operate)</option>
                            <option value="PMO">PMO (Project Delivery)</option>
                            <option value="Technical / Network">Technical / Network</option>
                            <option value="Sales / BDM">Sales / BDM (Commercial)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Assign Internal PIC</label>
                        <select name="assigned_to" class="wms-input">
                            <option value="">-- Pilih PIC Internal --</option>
                            @foreach($internalUsers as $u)
                                <option value="{{ $u->id }}">{{ $u->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Target SLA Selesai</label>
                    <input type="date" name="sla_due_date" class="wms-input" value="{{ date('Y-m-d', strtotime('+2 days')) }}">
                </div>

                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Deskripsi Detail Masalah *</label>
                    <textarea name="description" rows="3" required class="wms-input" placeholder="Kronologi komplain atau kebutuhan..."></textarea>
                </div>

                <div class="pt-3 border-t border-gray-100 flex justify-end gap-2">
                    <button type="button" @click="isCreateModalOpen = false" class="px-4 py-2 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 cursor-pointer">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-[#C81E2C] text-white font-bold rounded-xl hover:brightness-105 shadow-sm cursor-pointer">Simpan Tiket</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL 2: Dispatch Concern --}}
    <div x-show="isDispatchModalOpen" class="fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4 border border-[#E7E5E3] shadow-xl animate-fade-in-up" @click.outside="isDispatchModalOpen = false">
            <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                <h3 class="text-base font-bold text-gray-900">Disposisi Tiket <span x-text="dispatchData.ticketNumber" class="text-[#C81E2C]"></span></h3>
                <button @click="isDispatchModalOpen = false" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>

            <form :action="'/cro/concerns/' + dispatchData.id + '/dispatch'" method="POST" class="space-y-3.5 text-xs">
                @csrf
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Unit Internal *</label>
                    <select name="assigned_dept" x-model="dispatchData.assignedDept" required class="wms-input">
                        <option value="Managed Service">Managed Service</option>
                        <option value="PMO">PMO</option>
                        <option value="Technical / Network">Technical / Network</option>
                        <option value="Sales / BDM">Sales / BDM</option>
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-gray-700 mb-1">PIC Yang Ditugaskan *</label>
                    <select name="assigned_to" x-model="dispatchData.assignedTo" required class="wms-input font-bold">
                        <option value="">-- Pilih PIC Internal --</option>
                        @foreach($internalUsers as $u)
                            <option value="{{ $u->id }}">{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Target SLA Selesai</label>
                    <input type="date" name="sla_due_date" class="wms-input" value="{{ date('Y-m-d', strtotime('+2 days')) }}">
                </div>

                <div class="pt-3 border-t border-gray-100 flex justify-end gap-2">
                    <button type="button" @click="isDispatchModalOpen = false" class="px-4 py-2 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 cursor-pointer">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 shadow-sm cursor-pointer">Simpan Disposisi</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL 3: Resolve & Record Action Taken --}}
    <div x-show="isResolveModalOpen" class="fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 space-y-4 border border-[#E7E5E3] shadow-xl animate-fade-in-up" @click.outside="isResolveModalOpen = false">
            <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                <h3 class="text-base font-bold text-gray-900">Catat Tindakan Selesai (<span x-text="resolveData.ticketNumber" class="text-[#C81E2C]"></span>)</h3>
                <button @click="isResolveModalOpen = false" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>

            <form :action="'/cro/concerns/' + resolveData.id + '/resolve'" method="POST" class="space-y-3.5 text-xs">
                @csrf
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Root Cause (Penyebab Utama Masalah) *</label>
                    <textarea name="root_cause" x-model="resolveData.rootCause" rows="2" required class="wms-input" placeholder="e.g. Masalah buffer stock di gudang / degradasi link kabel FO"></textarea>
                </div>

                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Tindakan Perbaikan yang Telah Dilakukan *</label>
                    <textarea name="action_taken" x-model="resolveData.actionTaken" rows="3" required class="wms-input" placeholder="e.g. Penempatan unit standby router di site klien & pengiriman dokumen RCA"></textarea>
                </div>

                <div class="p-3 bg-blue-50 border border-blue-200 rounded-xl text-blue-800 text-[11.5px]">
                    ℹ️ Setelah disimpan, tiket akan beralih ke status <strong>"Pending Confirm"</strong>. CRO akan memvalidasi kepuasan klien sebelum dinyatakan CLOSED.
                </div>

                <div class="pt-3 border-t border-gray-100 flex justify-end gap-2">
                    <button type="button" @click="isResolveModalOpen = false" class="px-4 py-2 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 cursor-pointer">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 shadow-sm cursor-pointer">Submit Tindakan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL 4: Customer Confirmation & Rating --}}
    <div x-show="isConfirmModalOpen" class="fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4 border border-[#E7E5E3] shadow-xl animate-fade-in-up" @click.outside="isConfirmModalOpen = false">
            <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                <h3 class="text-base font-bold text-gray-900">Validasi Konfirmasi Klien (<span x-text="confirmData.ticketNumber" class="text-[#C81E2C]"></span>)</h3>
                <button @click="isConfirmModalOpen = false" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>

            <form :action="'/cro/concerns/' + confirmData.id + '/confirm'" method="POST" class="space-y-3.5 text-xs">
                @csrf
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Tanggal Konfirmasi Klien *</label>
                    <input type="datetime-local" name="customer_confirmed_at" required class="wms-input" value="{{ date('Y-m-d\TH:i') }}">
                </div>

                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Rating Kepuasan atas Penyelesaian (1 - 5) *</label>
                    <select name="customer_satisfaction_rating" required class="wms-input font-bold text-amber-600">
                        <option value="5" selected>⭐⭐⭐⭐⭐ 5 (Sangat Puas / Tuntas)</option>
                        <option value="4">⭐⭐⭐⭐ 4 (Puas)</option>
                        <option value="3">⭐⭐⭐ 3 (Cukup / Netral)</option>
                        <option value="2">⭐⭐ 2 (Kurang Puas)</option>
                        <option value="1">⭐ 1 (Tidak Puas)</option>
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Catatan / Pernyataan Konfirmasi dari Klien</label>
                    <textarea name="customer_confirmation_notes" rows="2" class="wms-input" placeholder="e.g. Klien telah mengonfirmasi bahwa koneksi sudah stabil dan puas dengan unit standby..."></textarea>
                </div>

                <div class="pt-3 border-t border-gray-100 flex justify-end gap-2">
                    <button type="button" @click="isConfirmModalOpen = false" class="px-4 py-2 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 cursor-pointer">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-emerald-600 text-white font-bold rounded-xl hover:bg-emerald-700 shadow-sm cursor-pointer">Validasi & Close Tiket</button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
function croConcernsHandler() {
    return {
        isCreateModalOpen: false,
        isDispatchModalOpen: false,
        isResolveModalOpen: false,
        isConfirmModalOpen: false,
        dispatchData: { id: null, ticketNumber: '', assignedDept: '', assignedTo: '' },
        resolveData: { id: null, ticketNumber: '', rootCause: '', actionTaken: '' },
        confirmData: { id: null, ticketNumber: '' },
        openDispatchModal(id, num, dept, user) {
            this.dispatchData = { id, ticketNumber: num, assignedDept: dept, assignedTo: user };
            this.isDispatchModalOpen = true;
        },
        openResolveModal(id, num, root, action) {
            this.resolveData = { id, ticketNumber: num, rootCause: root, actionTaken: action };
            this.isResolveModalOpen = true;
        },
        openConfirmModal(id, num) {
            this.confirmData = { id, ticketNumber: num };
            this.isConfirmModalOpen = true;
        }
    };
}
</script>
@endsection
