@extends('layouts.app')

@section('title', 'Peluang & Pipeline - Sales Portal')

@section('content')
<div class="flex h-screen overflow-hidden" x-data="salesPipelinePage()">
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto bg-[#FAF9F8]">
        @include('components.topbar', ['title' => 'Peluang & Pipeline'])
        
        <div class="p-4 sm:p-5 lg:p-[26px] animate-fade-in space-y-5 max-w-[1600px] mx-auto">
            
            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 font-bold px-2">✕</button>
                </div>
            @endif

            {{-- 3 Summary Metric Cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-[0_1px_3px_rgba(0,0,0,0.03)] flex items-center justify-between">
                    <div>
                        <span class="text-[11.5px] font-medium text-gray-400 block">Total active pipeline</span>
                        <div class="text-xl font-extrabold text-gray-900 tracking-tight mt-1">
                            Rp {{ number_format($totalPipelineValue / 1000000, 1, ',', '.') }} Jt
                        </div>
                        <span class="text-xs text-gray-400 mt-0.5 block">Seluruh peluang belum Won/Lost</span>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-100 text-blue-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-[0_1px_3px_rgba(0,0,0,0.03)] flex items-center justify-between">
                    <div>
                        <span class="text-[11.5px] font-medium text-gray-400 block">Weighted forecast</span>
                        <div class="text-xl font-extrabold text-amber-900 tracking-tight mt-1">
                            Rp {{ number_format($totalWeightedForecast / 1000000, 1, ',', '.') }} Jt
                        </div>
                        <span class="text-xs text-gray-400 mt-0.5 block">Nilai berbobot probabilitas (%)</span>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-amber-50 border border-amber-100 text-amber-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-[0_1px_3px_rgba(0,0,0,0.03)] flex items-center justify-between">
                    <div>
                        <span class="text-[11.5px] font-medium text-gray-400 block">Total won deals</span>
                        <div class="text-xl font-extrabold text-emerald-700 tracking-tight mt-1">
                            Rp {{ number_format($totalWonValue / 1000000, 1, ',', '.') }} Jt
                        </div>
                        <span class="text-xs text-gray-400 mt-0.5 block">Kontrak tereksekusi resmi</span>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                </div>
            </div>

            <!-- Filter & Action Bar -->
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
                <form method="GET" action="{{ route('sales.pipeline.index') }}" class="flex flex-col sm:flex-row flex-wrap gap-2.5 w-full sm:w-auto">
                    <div class="relative">
                        <svg class="w-3.5 h-3.5 absolute left-3 top-3 text-[#948F99]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" 
                               name="search" 
                               value="{{ $search }}"
                               placeholder="Cari nama peluang, klien, quotation..." 
                               class="w-full sm:w-64 px-[11px] py-[9px] pl-8 rounded-lg border border-[#E7E5E3] text-[13.5px] text-[#17151C] outline-none bg-white focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all">
                    </div>

                    <select name="stage" onchange="this.form.submit()" 
                            class="w-full sm:w-52 px-[11px] py-[9px] rounded-lg border border-[#E7E5E3] text-[13.5px] text-[#17151C] outline-none bg-white focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all cursor-pointer">
                        <option value="">Semua Tahapan SOP</option>
                        @foreach($stages as $stageKey => $meta)
                            <option value="{{ $stageKey }}" {{ $filterStage == $stageKey ? 'selected' : '' }}>{{ $meta['label'] }}</option>
                        @endforeach
                    </select>

                    @if($isManagerial)
                    <select name="sales" onchange="this.form.submit()" 
                            class="w-full sm:w-48 px-[11px] py-[9px] rounded-lg border border-[#E7E5E3] text-[13.5px] text-[#17151C] outline-none bg-white focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all cursor-pointer">
                        <option value="">Semua Sales PIC</option>
                        @foreach($salesTeam as $sp)
                            <option value="{{ $sp }}" {{ $filterSales == $sp ? 'selected' : '' }}>{{ $sp }}</option>
                        @endforeach
                    </select>
                    @endif

                    @if($search || $filterStage || ($isManagerial && $filterSales))
                        <a href="{{ route('sales.pipeline.index') }}" 
                           class="px-3 py-2 text-[13px] font-semibold text-gray-500 hover:text-gray-700 self-center">
                            Reset
                        </a>
                    @endif
                </form>

                <div class="flex items-center gap-2">
                    <button type="button" 
                            @click="isNewOppModalOpen = true"
                            class="inline-flex items-center justify-center gap-2 px-4 py-[9px] bg-[#C81E2C] hover:bg-[#AF1424] text-white rounded-lg text-[13.5px] font-bold shadow-[0_4px_12px_rgba(200,30,44,0.2)] active:translate-y-[1px] transition-all cursor-pointer whitespace-nowrap">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Tambah Peluang Baru</span>
                    </button>
                </div>
            </div>

            {{-- Main Data Table --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-[0_1px_3px_rgba(0,0,0,0.03)] overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-gray-50 text-gray-500 uppercase text-[10.5px] font-bold">
                            <tr>
                                <th class="py-2.5 px-4 rounded-l-lg">Peluang & Klien</th>
                                <th class="py-2.5 px-4">Sales & BDM Origin</th>
                                <th class="py-2.5 px-4 text-right">Nilai Nominal & Quo</th>
                                <th class="py-2.5 px-4 text-center">Tahapan & Probabilitas</th>
                                <th class="py-2.5 px-4">Closing Horizon</th>
                                <th class="py-2.5 px-4 rounded-r-lg text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 font-medium text-gray-700">
                            @forelse($projects as $p)
                                @php
                                    $stageMeta = $stages[$p->sales_stage] ?? ['color' => '#6B7280', 'bg' => '#F3F4F6'];
                                    $prob = $p->win_probability ?? ($stages[$p->sales_stage]['default_prob'] ?? 10);
                                    $wtd = ($p->contract_value ?? 0) * ($prob / 100);
                                @endphp
                                <tr class="hover:bg-gray-50/80 transition-colors">
                                    {{-- Proyek & Klien --}}
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-gray-900 max-w-sm">{{ $p->name }}</div>
                                        <div class="text-[11px] text-gray-400 font-medium mt-0.5 flex items-center gap-1.5">
                                            <span>{{ $p->client }}</span>
                                            @if($p->proposal_file)
                                                <span>•</span>
                                                <a href="{{ route('projects.proposal.download', $p->id) }}" class="text-emerald-700 font-bold hover:underline">
                                                    SOW Presales ✓
                                                </a>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- Sales PIC & BDM --}}
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <div class="font-bold text-gray-900">{{ $p->sales_name ?: 'Belum Assign' }}</div>
                                        <div class="text-[11px] text-gray-400">BDM: {{ $p->bdm->name ?? ($p->creator->name ?? 'BDM') }}</div>
                                    </td>

                                    {{-- Nilai & Quotation --}}
                                    <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                        <div class="font-extrabold text-gray-900">
                                            {{ $p->contract_value > 0 ? 'Rp ' . number_format($p->contract_value, 0, ',', '.') : '—' }}
                                        </div>
                                        <div class="text-[11px] text-gray-400">
                                            @if($p->quotation_number)
                                                <span class="font-mono text-gray-700">Quo: {{ $p->quotation_number }}</span>
                                            @else
                                                <span class="italic text-gray-400">Belum ada Quo</span>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- Tahapan & Probabilitas --}}
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        <div class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-bold" 
                                              style="background-color: {{ $stageMeta['bg'] }}; color: {{ $stageMeta['color'] }};">
                                            <span>{{ $p->sales_stage }}</span>
                                            <span class="text-[10px] opacity-90">({{ $prob }}%)</span>
                                        </div>
                                        <div class="text-[10px] text-gray-400 mt-1">
                                            Weighted: Rp {{ number_format($wtd / 1000000, 1, ',', '.') }} Jt
                                        </div>
                                    </td>

                                    {{-- Closing Date --}}
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        @if($p->expected_closing_date)
                                            <div class="font-semibold text-gray-900">
                                                {{ \Carbon\Carbon::parse($p->expected_closing_date)->format('d M Y') }}
                                            </div>
                                            <div class="text-[10.5px] text-gray-400">
                                                {{ \Carbon\Carbon::parse($p->expected_closing_date)->diffForHumans() }}
                                            </div>
                                        @else
                                            <span class="text-gray-400 italic">Belum diset</span>
                                        @endif
                                    </td>

                                    {{-- Aksi --}}
                                    <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <button type="button" 
                                                    @click="openStageModal({{ json_encode($p) }})"
                                                    class="px-2.5 py-1 bg-white hover:bg-gray-50 text-[#C81E2C] text-[11px] font-semibold rounded-lg border border-red-200 shadow-sm transition">
                                                Update
                                            </button>
                                            <a href="{{ route('projects.show', $p->id) }}" 
                                               class="px-2.5 py-1 bg-white hover:bg-gray-50 text-gray-700 text-[11px] font-semibold rounded-lg border border-gray-200 shadow-sm transition">
                                                Detail
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-12 text-center text-gray-400 text-xs">
                                        Tidak ada peluang yang cocok dengan filter.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($projects->hasPages())
                    <div class="p-4 border-t border-gray-100 bg-[#FAF9F8]">
                        {{ $projects->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>

    {{-- MODAL UPDATE SALES LIFECYCLE STAGE & FORECAST (BUSDEV/ENGINEER STANDARD) --}}
    <template x-teleport="body">
        <div x-show="isStageModalOpen" 
             x-cloak 
             class="fixed inset-0 z-50 bg-[#0E0D12]/60 flex items-center justify-center p-3 sm:p-5 backdrop-blur-xs"
             @click.self="isStageModalOpen = false">
            <div class="bg-white rounded-2xl w-[760px] max-w-full max-h-[88vh] flex flex-col overflow-hidden shadow-[0_20px_60px_rgba(14,13,18,0.2)] border border-[#E7E5E3] my-auto animate-fade-in-up">
                
                {{-- Modal Header (Fixed) --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-[#E7E5E3] flex-shrink-0 bg-white">
                    <div>
                        <h3 class="font-display text-[17px] font-bold text-[#17151C]" x-text="'Update Lifecycle: ' + (selectedDeal.name || '')"></h3>
                        <p class="text-[12px] text-[#75727C] mt-0.5">Perbarui progres tahapan sales SOP, probabilitas, dan dokumen penawaran.</p>
                    </div>
                    <button type="button" @click="isStageModalOpen = false" class="rounded-lg p-1.5 text-[#75727C] hover:text-[#17151C] hover:bg-[#F1F0EE] transition-colors cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Modal Body (Scrollable) --}}
                <div class="px-6 py-4 overflow-y-auto flex-1 space-y-3.5 text-xs">
                    <form id="stageUpdateForm"
                          :action="'/sales/pipeline/' + selectedDeal.id + '/stage'" 
                          method="POST" 
                          enctype="multipart/form-data" 
                          class="space-y-3.5">
                        @csrf
                        
                        {{-- Ringkasan Peluang --}}
                        <div class="bg-[#FAF9F8] p-3 rounded-xl border border-[#E7E5E3] flex items-center justify-between text-[12.5px]">
                            <div>
                                <span class="text-[#75727C]">Klien / Instansi: </span>
                                <span class="font-bold text-[#17151C]" x-text="selectedDeal.client || '—'"></span>
                            </div>
                            <div>
                                <span class="text-[#75727C]">Sales PIC: </span>
                                <span class="font-bold text-[#C81E2C]" x-text="selectedDeal.sales_name || 'Belum Ditugaskan'"></span>
                            </div>
                        </div>

                        {{-- Section 1: Stage & Win Probability --}}
                        <div class="bg-[#FAF9F8] p-3.5 rounded-xl border border-[#E7E5E3] space-y-2.5">
                            <div class="font-bold text-[#17151C] text-[12.5px] flex items-center gap-2">
                                <span class="w-4.5 h-4.5 rounded-full bg-[#C81E2C] text-white flex items-center justify-center text-[10px] font-bold">1</span>
                                <span>Tahapan & Probabilitas Closing</span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                <div>
                                    <label class="block text-[11px] font-semibold text-[#17151C] mb-1">
                                        Tahapan Siklus Penjualan (Sales Stage) <span class="text-[#C81E2C]">*</span>
                                    </label>
                                    <select name="sales_stage" x-model="formStage" @change="syncProbability()" required
                                            class="w-full bg-white border border-[#E7E5E3] rounded-lg px-3 py-2 text-[12.5px] text-[#17151C] outline-none focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all font-semibold">
                                        <option value="Qualification">1. Qualification (10%)</option>
                                        <option value="Qualified Opportunity">2. Qualified Opportunity (25%)</option>
                                        <option value="Proposal Request">3. Proposal Request (50%)</option>
                                        <option value="Quotation">4. Quotation Submitted (70%)</option>
                                        <option value="Negotiation">5. Negotiation (85%)</option>
                                        <option value="Approval">6. Internal Approval (95%)</option>
                                        <option value="Contract / PO / SPK">7. Contract / PO / SPK (98%)</option>
                                        <option value="Closed Won">8. Closed Won (100% Handover)</option>
                                        <option value="Closed Lost">Closed Lost / Drop (0%)</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-[11px] font-semibold text-[#17151C] mb-1">
                                        Win Probability (%) <span class="text-[#C81E2C]">*</span>
                                    </label>
                                    <input type="number" name="win_probability" x-model="formProb" min="0" max="100" required
                                           class="w-full bg-white border border-[#E7E5E3] rounded-lg px-3 py-2 text-[12.5px] text-[#17151C] outline-none focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all font-semibold">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                <div>
                                    <label class="block text-[11px] font-semibold text-[#17151C] mb-1">
                                        Nilai Kontrak Estimasi (Rp)
                                    </label>
                                    <input type="number" name="contract_value" x-model="formContractValue" min="0" step="1000"
                                           class="w-full bg-white border border-[#E7E5E3] rounded-lg px-3 py-2 text-[12.5px] text-[#17151C] outline-none focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all font-semibold">
                                </div>

                                <div>
                                    <label class="block text-[11px] font-semibold text-[#17151C] mb-1">
                                        Expected Closing Date
                                    </label>
                                    <input type="date" name="expected_closing_date" x-model="formClosingDate"
                                           class="w-full bg-white border border-[#E7E5E3] rounded-lg px-3 py-2 text-[12.5px] text-[#17151C] outline-none focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all font-medium">
                                </div>
                            </div>
                        </div>

                        {{-- Section 2: Quotation Details --}}
                        <div class="bg-[#FAF9F8] p-3.5 rounded-xl border border-[#E7E5E3] space-y-2.5">
                            <div class="font-bold text-[#17151C] text-[12.5px] flex items-center gap-2">
                                <span class="w-4.5 h-4.5 rounded-full bg-[#C81E2C] text-white flex items-center justify-center text-[10px] font-bold">2</span>
                                <span>Dokumen Penawaran Harga (Quotation)</span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                <div>
                                    <label class="block text-[11px] font-semibold text-[#17151C] mb-1">Nomor Quotation</label>
                                    <input type="text" name="quotation_number" x-model="formQuotationNum" placeholder="Contoh: QUO-IPNET/2026/09/001"
                                           class="w-full bg-white border border-[#E7E5E3] rounded-lg px-3 py-2 text-[12.5px] text-[#17151C] outline-none focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-[#75727C] mb-1">Upload File Quotation Resmi</label>
                                    <input type="file" name="quotation_file" accept=".pdf,.docx,.xlsx,.zip"
                                           class="w-full bg-white border border-[#E7E5E3] rounded-lg px-2.5 py-1.5 text-[12px] text-[#75727C] file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-[11px] file:font-semibold file:bg-[#F1F0EE] file:text-[#17151C] hover:file:bg-[#E7E5E3] cursor-pointer">
                                </div>
                            </div>
                        </div>

                        {{-- Section 3: Lost Reason if Closed Lost --}}
                        <div x-show="formStage === 'Closed Lost'" class="bg-rose-50/60 p-3.5 rounded-xl border border-rose-200 space-y-2.5">
                            <span class="text-[11.5px] font-bold text-rose-800 uppercase tracking-wider block">Alasan Deal Drop / Kalah Tender</span>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                <div>
                                    <label class="block text-[11px] font-semibold text-rose-900 mb-1">Kompetitor Pemenang</label>
                                    <input type="text" name="lost_competitor" placeholder="Contoh: PT Integrator Lain"
                                           class="w-full bg-white border border-rose-300 rounded-lg px-3 py-2 text-[12.5px] text-gray-900 outline-none">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-rose-900 mb-1">Alasan Utama</label>
                                    <input type="text" name="lost_reason" placeholder="Contoh: Harga lebih tinggi, Spek tidak masuk"
                                           class="w-full bg-white border border-rose-300 rounded-lg px-3 py-2 text-[12.5px] text-gray-900 outline-none">
                                </div>
                            </div>
                        </div>

                        {{-- Section 4: Notes --}}
                        <div class="bg-[#FAF9F8] p-3.5 rounded-xl border border-[#E7E5E3] space-y-1.5">
                            <label class="block text-[11px] font-semibold text-[#17151C] mb-1">Catatan Progres Negosiasi / Follow-up</label>
                            <textarea name="sales_notes" rows="2.5" placeholder="Tuliskan ringkasan perkembangan negosiasi dengan klien..."
                                      class="w-full bg-white border border-[#E7E5E3] rounded-lg p-2.5 text-[12.5px] text-[#17151C] outline-none focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all"></textarea>
                        </div>

                    </form>
                </div>

                {{-- Modal Footer (Fixed) --}}
                <div class="flex items-center gap-3 px-6 py-3.5 border-t border-[#E7E5E3] bg-[#FAF9F8] flex-shrink-0">
                    <button type="submit" 
                            form="stageUpdateForm" 
                            class="flex-1 flex items-center justify-center min-h-[40px] bg-[#C81E2C] hover:bg-[#AF1424] text-white shadow-[0_8px_20px_rgba(200,30,44,0.24)] px-4 py-2 rounded-xl font-semibold text-[13px] active:translate-y-[1px] transition-all cursor-pointer">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Simpan Perubahan Stage</span>
                    </button>
                    <button type="button" 
                            @click="isStageModalOpen = false" 
                            class="flex-1 flex items-center justify-center min-h-[40px] bg-white hover:bg-[#F8F7F6] text-[#3D3A44] border border-[#E7E5E3] shadow-[0_1px_2px_rgba(14,13,18,0.05)] px-4 py-2 rounded-xl font-semibold text-[13px] active:translate-y-[1px] transition-all cursor-pointer">
                        Batal
                    </button>
                </div>

            </div>
        </div>
    </template>

    {{-- MODAL TAMBAH PELUANG PENJUALAN BARU (SALES SELF-SOURCED) --}}
    <template x-teleport="body">
        <div x-show="isNewOppModalOpen" 
             x-cloak 
             class="fixed inset-0 z-50 bg-[#0E0D12]/60 flex items-center justify-center p-3 sm:p-5 backdrop-blur-xs"
             @click.self="isNewOppModalOpen = false">
            <div class="bg-white rounded-2xl w-[620px] max-w-full max-h-[90vh] flex flex-col overflow-hidden shadow-[0_20px_60px_rgba(14,13,18,0.2)] border border-[#E7E5E3] my-auto animate-fade-in-up">
                
                {{-- Header (Fixed) --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-[#E7E5E3] flex-shrink-0 bg-white">
                    <div>
                        <h3 class="font-display text-[17px] font-bold text-[#17151C]">Tambah Peluang Penjualan Baru</h3>
                        <p class="text-[12px] text-[#75727C] mt-0.5">Daftarkan prospek mandiri atau proyek baru langsung ke pipeline Sales Anda.</p>
                    </div>
                    <button type="button" @click="isNewOppModalOpen = false" class="rounded-lg p-1.5 text-[#75727C] hover:text-[#17151C] hover:bg-[#F1F0EE] transition-colors cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Body (Scrollable) --}}
                <div class="px-6 py-4 overflow-y-auto flex-1">
                    <form id="newOppForm" action="{{ route('sales.pipeline.store') }}" method="POST" class="space-y-4 text-xs">
                        @csrf

                        {{-- Section 1: Identitas Prospek & Klien --}}
                        <div class="bg-[#FAF9F8] p-3.5 rounded-xl border border-[#E7E5E3] space-y-3">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="w-5 h-5 rounded-full bg-[#17151C] text-white flex items-center justify-center font-bold text-[10px]">1</span>
                                <span class="text-[12px] font-bold text-[#17151C]">Identitas Prospek & Klien</span>
                            </div>

                            <div>
                                <label class="block text-[11.5px] font-semibold text-[#17151C] mb-1">Nama Prospek / Proyek <span class="text-[#C81E2C]">*</span></label>
                                <input type="text" name="name" required class="w-full bg-white border border-[#E7E5E3] rounded-lg px-3 py-2 text-[13px] text-[#17151C] outline-none focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all" placeholder="e.g. Modernisasi Jaringan & WiFi Kampus Utama">
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[11.5px] font-semibold text-[#17151C] mb-1">Nama Klien / Instansi <span class="text-[#C81E2C]">*</span></label>
                                    <input type="text" name="client" required class="w-full bg-white border border-[#E7E5E3] rounded-lg px-3 py-2 text-[13px] text-[#17151C] outline-none focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all" placeholder="e.g. PT Telekomunikasi Indonesia / Instansi">
                                </div>
                                <div>
                                    <label class="block text-[11.5px] font-semibold text-[#17151C] mb-1">Estimasi Nilai Kontrak (Rp) <span class="text-[#C81E2C]">*</span></label>
                                    <input type="number" name="contract_value" required min="0" class="w-full bg-white border border-[#E7E5E3] rounded-lg px-3 py-2 text-[13px] font-bold text-[#C81E2C] outline-none focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all" placeholder="250000000">
                                </div>
                            </div>
                        </div>

                        {{-- Section 2: Tahapan Pipeline & Horizon Closing --}}
                        <div class="bg-[#FAF9F8] p-3.5 rounded-xl border border-[#E7E5E3] space-y-3">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="w-5 h-5 rounded-full bg-[#17151C] text-white flex items-center justify-center font-bold text-[10px]">2</span>
                                <span class="text-[12px] font-bold text-[#17151C]">Tahapan Pipeline & Horizon Closing</span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[11.5px] font-semibold text-[#17151C] mb-1">Tahap Awal Pipeline <span class="text-[#C81E2C]">*</span></label>
                                    <select name="sales_stage" x-model="newOppStage" @change="syncNewOppProb()" class="w-full bg-white border border-[#E7E5E3] rounded-lg px-3 py-2 text-[13px] text-[#17151C] outline-none focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all cursor-pointer">
                                        @foreach($stages as $stageKey => $meta)
                                            <option value="{{ $stageKey }}">{{ $meta['label'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[11.5px] font-semibold text-[#17151C] mb-1">Win Probability (%)</label>
                                    <div class="relative">
                                        <input type="number" name="win_probability" x-model="newOppProb" min="0" max="100" class="w-full bg-white border border-[#E7E5E3] rounded-lg px-3 py-2 text-[13px] font-bold text-blue-600 outline-none focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all">
                                        <span class="absolute right-3 top-2 text-xs font-bold text-gray-400">%</span>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[11.5px] font-semibold text-[#17151C] mb-1">Target Tanggal Closing</label>
                                    <input type="date" name="expected_closing_date" value="{{ now()->addMonths(1)->format('Y-m-d') }}" class="w-full bg-white border border-[#E7E5E3] rounded-lg px-3 py-2 text-[13px] text-[#17151C] outline-none focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all cursor-pointer">
                                </div>
                                <div>
                                    <label class="block text-[11.5px] font-semibold text-[#17151C] mb-1">Sumber Peluang</label>
                                    <select name="opportunity_source" class="w-full bg-white border border-[#E7E5E3] rounded-lg px-3 py-2 text-[13px] text-[#17151C] outline-none focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all cursor-pointer">
                                        <option value="Direct Sales Prospecting" selected>Direct Sales Prospecting</option>
                                        <option value="Client Inbound / Referensi">Client Inbound / Referensi</option>
                                        <option value="Existing Account Upsell">Existing Account Upsell / Renewal</option>
                                        <option value="Tender / Pengadaan Resmi">Tender / Pengadaan Resmi (LPSE)</option>
                                        <option value="Partner Referral">Partner / Prinsipal Referral</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- Section 3: Catatan & Kebutuhan Awal --}}
                        <div class="bg-[#FAF9F8] p-3.5 rounded-xl border border-[#E7E5E3] space-y-1.5">
                            <label class="block text-[11.5px] font-semibold text-[#17151C] mb-1">Catatan Kebutuhan Awal / Action Plan</label>
                            <textarea name="sales_notes" rows="2.5" class="w-full bg-white border border-[#E7E5E3] rounded-lg p-2.5 text-[12.5px] text-[#17151C] outline-none focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all" placeholder="Catat ringkasan kebutuhan awal klien, spesifikasi, atau agenda follow up berikutnya..."></textarea>
                        </div>
                    </form>
                </div>

                {{-- Footer (Fixed) --}}
                <div class="flex items-center gap-3 px-6 py-3.5 border-t border-[#E7E5E3] bg-[#FAF9F8] flex-shrink-0">
                    <button type="submit" 
                            form="newOppForm" 
                            class="flex-1 flex items-center justify-center min-h-[40px] bg-[#C81E2C] hover:bg-[#AF1424] text-white shadow-[0_8px_20px_rgba(200,30,44,0.24)] px-4 py-2 rounded-xl font-semibold text-[13px] active:translate-y-[1px] transition-all cursor-pointer">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Simpan Peluang</span>
                    </button>
                    <button type="button" 
                            @click="isNewOppModalOpen = false" 
                            class="flex-1 flex items-center justify-center min-h-[40px] bg-white hover:bg-[#F8F7F6] text-[#3D3A44] border border-[#E7E5E3] shadow-[0_1px_2px_rgba(14,13,18,0.05)] px-4 py-2 rounded-xl font-semibold text-[13px] active:translate-y-[1px] transition-all cursor-pointer">
                        Batal
                    </button>
                </div>

            </div>
        </div>
    </template>

</div>

<script>
    function salesPipelinePage() {
        return {
            isStageModalOpen: false,
            isNewOppModalOpen: false,
            newOppStage: 'Qualification',
            newOppProb: 10,
            selectedDeal: {},
            formStage: 'Qualification',
            formProb: 10,
            formContractValue: 0,
            formClosingDate: '',
            formQuotationNum: '',

            syncNewOppProb() {
                const stageProbs = {
                    'Qualification': 10,
                    'Qualified Opportunity': 25,
                    'Proposal Request': 50,
                    'Quotation': 70,
                    'Negotiation': 85,
                    'Approval': 95,
                    'Contract / PO / SPK': 98,
                    'Closed Won': 100,
                    'Closed Lost': 0
                };
                if (stageProbs[this.newOppStage] !== undefined) {
                    this.newOppProb = stageProbs[this.newOppStage];
                }
            },

            openStageModal(deal) {
                this.selectedDeal = deal;
                this.formStage = deal.sales_stage || 'Qualification';
                this.formProb = deal.win_probability !== null ? deal.win_probability : 10;
                this.formContractValue = deal.contract_value || 0;
                this.formClosingDate = deal.expected_closing_date ? deal.expected_closing_date.split('T')[0] : '';
                this.formQuotationNum = deal.quotation_number || '';
                this.isStageModalOpen = true;
            },

            syncProbability() {
                const stageProbs = {
                    'Qualification': 10,
                    'Qualified Opportunity': 25,
                    'Proposal Request': 50,
                    'Quotation': 70,
                    'Negotiation': 85,
                    'Approval': 95,
                    'Contract / PO / SPK': 98,
                    'Closed Won': 100,
                    'Closed Lost': 0
                };
                if (stageProbs[this.formStage] !== undefined) {
                    this.formProb = stageProbs[this.formStage];
                }
            }
        }
    }
</script>
@endsection
