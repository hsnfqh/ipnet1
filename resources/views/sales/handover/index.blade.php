@extends('layouts.app')

@section('title', 'Commercial Handover - Sales Portal')

@section('content')
<div class="flex h-screen overflow-hidden" x-data="commercialHandoverPage()">
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto bg-[#FAF9F8]">
        @include('components.topbar', ['title' => 'Commercial Handover'])
        
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

            <!-- Filter & Action Bar -->
            <div class="flex flex-col sm:flex-row sm:flex-wrap sm:justify-between sm:items-center gap-2.5">
                <form method="GET" action="{{ route('sales.handover.index') }}" class="flex flex-col sm:flex-row flex-wrap gap-2.5 w-full sm:w-auto">
                    <div class="relative">
                        <svg class="w-3.5 h-3.5 absolute left-3 top-3 text-[#948F99]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" 
                               name="search" 
                               value="{{ $search }}"
                               placeholder="Cari proyek, klien, nomor PO/SPK..." 
                               class="w-full sm:w-64 px-[11px] py-[9px] pl-8 rounded-lg border border-[#E7E5E3] text-[13.5px] text-[#17151C] outline-none bg-white focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all">
                    </div>

                    <select name="status" onchange="this.form.submit()" 
                            class="w-full sm:w-48 px-[11px] py-[9px] rounded-lg border border-[#E7E5E3] text-[13.5px] text-[#17151C] outline-none bg-white focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all cursor-pointer">
                        <option value="">Semua Status Handover</option>
                        <option value="Draft" {{ $filterStatus == 'Draft' ? 'selected' : '' }}>Draft Handover</option>
                        <option value="Submitted" {{ $filterStatus == 'Submitted' ? 'selected' : '' }}>Submitted ke Delivery</option>
                        <option value="Approved" {{ $filterStatus == 'Approved' ? 'selected' : '' }}>Approved by PMO</option>
                    </select>

                    @if($search || $filterStatus)
                        <a href="{{ route('sales.handover.index') }}" 
                           class="px-3 py-2 text-[13px] font-semibold text-gray-500 hover:text-gray-700 self-center">
                            Reset
                        </a>
                    @endif
                </form>
            </div>

            {{-- Table Deals Handover --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-[0_1px_3px_rgba(0,0,0,0.03)] overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-gray-50 text-gray-500 uppercase text-[10.5px] font-bold">
                            <tr>
                                <th class="py-2.5 px-4 rounded-l-lg">Proyek & Klien</th>
                                <th class="py-2.5 px-4">Nomor & Tanggal PO/SPK</th>
                                <th class="py-2.5 px-4 text-right">Nilai Kontrak</th>
                                <th class="py-2.5 px-4">Billing & SLA Terms</th>
                                <th class="py-2.5 px-4 text-center">Status Handover</th>
                                <th class="py-2.5 px-4 rounded-r-lg text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 font-medium text-gray-700">
                            @forelse($handoverProjects as $hp)
                                @php
                                    $badge = match($hp->commercial_handover_status) {
                                        'Approved'  => ['bg' => '#F0FDF4', 'color' => '#16A34A', 'label' => 'Approved by PMO'],
                                        'Submitted' => ['bg' => '#EFF6FF', 'color' => '#2563EB', 'label' => 'Submitted to Delivery'],
                                        default     => ['bg' => '#FEFCE8', 'color' => '#CA8A04', 'label' => 'Draft Handover'],
                                    };
                                @endphp
                                <tr class="hover:bg-gray-50/80 transition-colors">
                                    {{-- Proyek & Klien --}}
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-gray-900 max-w-sm">{{ $hp->name }}</div>
                                        <div class="text-[11px] text-gray-400 font-medium mt-0.5">{{ $hp->client }} • PIC: {{ $hp->sales_name ?: 'Sales' }}</div>
                                    </td>

                                    {{-- Nomor PO / SPK --}}
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        @if($hp->po_spk_number)
                                            <div class="font-mono font-bold text-gray-900">{{ $hp->po_spk_number }}</div>
                                            <div class="text-[11px] text-gray-400">{{ $hp->po_spk_date ? \Carbon\Carbon::parse($hp->po_spk_date)->format('d M Y') : '—' }}</div>
                                        @else
                                            <span class="text-gray-400 italic">Belum Input PO/SPK</span>
                                        @endif
                                    </td>

                                    {{-- Nilai Kontrak --}}
                                    <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                        <div class="font-extrabold text-gray-900">
                                            {{ $hp->contract_value > 0 ? 'Rp ' . number_format($hp->contract_value, 0, ',', '.') : '—' }}
                                        </div>
                                        <div class="text-[10.5px] text-gray-400">
                                            Stage: {{ $hp->sales_stage }}
                                        </div>
                                    </td>

                                    {{-- Billing & SLA --}}
                                    <td class="py-3.5 px-4 max-w-xs">
                                        <div class="text-[11.5px] font-semibold text-gray-900 truncate" title="{{ $hp->billing_terms }}">
                                            {{ $hp->billing_terms ?: 'Termin belum diset' }}
                                        </div>
                                        <div class="text-[10.5px] text-gray-400 truncate" title="{{ $hp->sla_commitment }}">
                                            SLA: {{ $hp->sla_commitment ?: 'Standar 8x5' }}
                                        </div>
                                    </td>

                                    {{-- Status Handover --}}
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-[10.5px] font-bold" 
                                              style="background-color: {{ $badge['bg'] }}; color: {{ $badge['color'] }};">
                                            <span>{{ $badge['label'] }}</span>
                                        </span>
                                        @if($hp->commercial_handover_at)
                                            <div class="text-[10px] text-gray-400 mt-0.5">
                                                {{ \Carbon\Carbon::parse($hp->commercial_handover_at)->format('d/m/Y H:i') }}
                                            </div>
                                        @endif
                                    </td>

                                    {{-- Aksi --}}
                                    <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <button type="button" 
                                                    @click="openHandoverModal({{ json_encode($hp) }})"
                                                    class="px-2.5 py-1 bg-white hover:bg-gray-50 text-[#C81E2C] text-[11px] font-semibold rounded-lg border border-red-200 shadow-sm transition">
                                                {{ $hp->commercial_handover_status === 'Submitted' || $hp->commercial_handover_status === 'Approved' ? 'Edit' : 'Serah Terima' }}
                                            </button>
                                            <a href="{{ route('projects.show', $hp->id) }}" 
                                               class="px-2.5 py-1 bg-white hover:bg-gray-50 text-gray-700 text-[11px] font-semibold rounded-lg border border-gray-200 shadow-sm transition">
                                                Detail
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-12 text-center text-gray-400 text-xs">
                                        Belum ada kontrak yang siap diserahterimakan ke tim Delivery.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($handoverProjects->hasPages())
                    <div class="p-4 border-t border-gray-100 bg-[#FAF9F8]">
                        {{ $handoverProjects->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>

    {{-- MODAL COMMERCIAL HANDOVER SUBMISSION (BUSDEV/ENGINEER STANDARD) --}}
    <template x-teleport="body">
        <div x-show="isHandoverModalOpen" 
             x-cloak 
             class="fixed inset-0 z-50 bg-[#0E0D12]/60 flex items-center justify-center p-3 sm:p-5 backdrop-blur-xs"
             @click.self="isHandoverModalOpen = false">
            <div class="bg-white rounded-2xl w-[760px] max-w-full max-h-[88vh] flex flex-col overflow-hidden shadow-[0_20px_60px_rgba(14,13,18,0.2)] border border-[#E7E5E3] my-auto animate-fade-in-up">
                
                {{-- Modal Header (Fixed) --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-[#E7E5E3] flex-shrink-0 bg-white">
                    <div>
                        <h3 class="font-display text-[17px] font-bold text-[#17151C]" x-text="'Commercial Handover: ' + (selectedProject.name || '')"></h3>
                        <p class="text-[12px] text-[#75727C] mt-0.5">Serah terima legalitas kontrak, PO/SPK, dan ketentuan komersial ke tim PMO & Delivery.</p>
                    </div>
                    <button type="button" @click="isHandoverModalOpen = false" class="rounded-lg p-1.5 text-[#75727C] hover:text-[#17151C] hover:bg-[#F1F0EE] transition-colors cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Modal Body (Scrollable) --}}
                <div class="px-6 py-4 overflow-y-auto flex-1 space-y-3.5 text-xs">
                    <form id="commercialHandoverForm" 
                          :action="'/sales/handover/' + selectedProject.id + '/submit'" 
                          method="POST" 
                          enctype="multipart/form-data" 
                          class="space-y-3.5">
                        @csrf

                        {{-- Ringkasan Peluang --}}
                        <div class="bg-[#FAF9F8] p-3 rounded-xl border border-[#E7E5E3] flex items-center justify-between text-[12.5px]">
                            <div>
                                <span class="text-[#75727C]">Klien / Instansi: </span>
                                <span class="font-bold text-[#17151C]" x-text="selectedProject.client || '—'"></span>
                            </div>
                            <div>
                                <span class="text-[#75727C]">Sales PIC: </span>
                                <span class="font-bold text-[#C81E2C]">{{ auth()->user()->name }}</span>
                            </div>
                        </div>

                        {{-- Section 1: Legal & Financial Contract Info --}}
                        <div class="bg-[#FAF9F8] p-3.5 rounded-xl border border-[#E7E5E3] space-y-2.5">
                            <div class="font-bold text-[#17151C] text-[12.5px] flex items-center gap-2">
                                <span class="w-4.5 h-4.5 rounded-full bg-[#C81E2C] text-white flex items-center justify-center text-[10px] font-bold">1</span>
                                <span>Legalitas Kontrak & PO / SPK</span>
                            </div>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                <div>
                                    <label class="block text-[11px] font-semibold text-[#17151C] mb-1">
                                        Nomor PO / SPK / Kontrak <span class="text-[#C81E2C]">*</span>
                                    </label>
                                    <input type="text" name="po_spk_number" x-model="formPoNum" placeholder="Contoh: PO-CLI/2026/09/889" required
                                           class="w-full bg-white border border-[#E7E5E3] rounded-lg px-3 py-2 text-[12.5px] text-[#17151C] outline-none focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all font-medium">
                                </div>

                                <div>
                                    <label class="block text-[11px] font-semibold text-[#17151C] mb-1">
                                        Tanggal Terbit PO / SPK <span class="text-[#C81E2C]">*</span>
                                    </label>
                                    <input type="date" name="po_spk_date" x-model="formPoDate" required
                                           class="w-full bg-white border border-[#E7E5E3] rounded-lg px-3 py-2 text-[12.5px] text-[#17151C] outline-none focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all font-medium">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                <div>
                                    <label class="block text-[11px] font-semibold text-[#17151C] mb-1">
                                        Nilai Final Kontrak (Rp) <span class="text-[#C81E2C]">*</span>
                                    </label>
                                    <input type="number" name="contract_value" x-model="formContractValue" min="0" step="1000" required
                                           class="w-full bg-white border border-[#E7E5E3] rounded-lg px-3 py-2 text-[12.5px] text-[#17151C] outline-none focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all font-semibold">
                                </div>

                                <div>
                                    <label class="block text-[11px] font-semibold text-[#75727C] mb-1">
                                        Upload Berkas PO / SPK / Kontrak (PDF/ZIP)
                                    </label>
                                    <input type="file" name="po_spk_file" accept=".pdf,.docx,.xlsx,.zip,.rar"
                                           class="w-full bg-white border border-[#E7E5E3] rounded-lg px-2.5 py-1.5 text-[12px] text-[#75727C] file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-[11px] file:font-semibold file:bg-[#F1F0EE] file:text-[#17151C] hover:file:bg-[#E7E5E3] cursor-pointer">
                                </div>
                            </div>
                        </div>

                        {{-- Section 2: Commercial Terms & Billing --}}
                        <div class="bg-[#FAF9F8] p-3.5 rounded-xl border border-[#E7E5E3] space-y-2.5">
                            <div class="font-bold text-[#17151C] text-[12.5px] flex items-center gap-2">
                                <span class="w-4.5 h-4.5 rounded-full bg-[#C81E2C] text-white flex items-center justify-center text-[10px] font-bold">2</span>
                                <span>Ketentuan Komersial & Termin Pembayaran</span>
                            </div>
                            
                            <div>
                                <label class="block text-[11px] font-semibold text-[#17151C] mb-1">
                                    Billing Terms (Termin Penagihan) <span class="text-[#C81E2C]">*</span>
                                </label>
                                <textarea name="billing_terms" x-model="formBilling" rows="2.5" placeholder="Contoh: DP 30% setelah PO, 50% setelah Delivery Hardware, 20% setelah UAT & BAST selesai (TOP 30 Hari)." required
                                          class="w-full bg-white border border-[#E7E5E3] rounded-lg p-2.5 text-[12.5px] text-[#17151C] outline-none focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all"></textarea>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                <div>
                                    <label class="block text-[11px] font-semibold text-[#75727C] mb-1">SLA & Garansi</label>
                                    <input type="text" name="sla_commitment" x-model="formSla" placeholder="Contoh: Support 8x5 Response 4 Jam, Garansi 1 Tahun"
                                           class="w-full bg-white border border-[#E7E5E3] rounded-lg px-3 py-2 text-[12.5px] text-[#17151C] outline-none focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all">
                                </div>

                                <div>
                                    <label class="block text-[11px] font-semibold text-[#75727C] mb-1">Commercial Terms</label>
                                    <input type="text" name="commercial_terms" x-model="formCommercialTerms" placeholder="Contoh: Franco On-Site Jakarta, Termasuk PPN 11%"
                                           class="w-full bg-white border border-[#E7E5E3] rounded-lg px-3 py-2 text-[12.5px] text-[#17151C] outline-none focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all">
                                </div>
                            </div>
                        </div>

                        {{-- Section 3: Scope & Exclusions --}}
                        <div class="bg-[#FAF9F8] p-3.5 rounded-xl border border-[#E7E5E3] space-y-2.5">
                            <div class="font-bold text-[#17151C] text-[12.5px] flex items-center gap-2">
                                <span class="w-4.5 h-4.5 rounded-full bg-[#C81E2C] text-white flex items-center justify-center text-[10px] font-bold">3</span>
                                <span>Komitmen Khusus & Batasan Luar Scope (Exclusions)</span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                <div>
                                    <label class="block text-[11px] font-semibold text-[#75727C] mb-1">Komitmen Khusus ke Klien</label>
                                    <textarea name="special_commitment" x-model="formSpecialCommitment" rows="2" placeholder="Contoh: Training administrator 2 sesi untuk 5 engineer klien"
                                              class="w-full bg-white border border-[#E7E5E3] rounded-lg p-2.5 text-[12.5px] text-[#17151C] outline-none focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all"></textarea>
                                </div>

                                <div>
                                    <label class="block text-[11px] font-semibold text-[#75727C] mb-1">Exclusions (Luar Scope)</label>
                                    <textarea name="exclusions" x-model="formExclusions" rows="2" placeholder="Contoh: Penarikan FO baru & sipil dikerjakan vendor klien"
                                              class="w-full bg-white border border-[#E7E5E3] rounded-lg p-2.5 text-[12.5px] text-[#17151C] outline-none focus:border-[#C81E2C] focus:shadow-[0_0_0_3px_#FDF1F2] transition-all"></textarea>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>

                {{-- Modal Footer (Fixed) --}}
                <div class="flex items-center gap-3 px-6 py-3.5 border-t border-[#E7E5E3] bg-[#FAF9F8] flex-shrink-0">
                    <button type="submit" 
                            form="commercialHandoverForm" 
                            class="flex-1 flex items-center justify-center min-h-[40px] bg-[#C81E2C] hover:bg-[#AF1424] text-white shadow-[0_8px_20px_rgba(200,30,44,0.24)] px-4 py-2 rounded-xl font-semibold text-[13px] active:translate-y-[1px] transition-all cursor-pointer">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Kirim Commercial Handover ke PMO & Delivery</span>
                    </button>
                    <button type="button" 
                            @click="isHandoverModalOpen = false" 
                            class="flex-1 flex items-center justify-center min-h-[40px] bg-white hover:bg-[#F8F7F6] text-[#3D3A44] border border-[#E7E5E3] shadow-[0_1px_2px_rgba(14,13,18,0.05)] px-4 py-2 rounded-xl font-semibold text-[13px] active:translate-y-[1px] transition-all cursor-pointer">
                        Batal
                    </button>
                </div>

            </div>
        </div>
    </template>

</div>

<script>
    function commercialHandoverPage() {
        return {
            isHandoverModalOpen: false,
            selectedProject: {},
            formPoNum: '',
            formPoDate: '',
            formContractValue: 0,
            formBilling: '',
            formSla: '',
            formCommercialTerms: '',
            formSpecialCommitment: '',
            formExclusions: '',

            openHandoverModal(proj) {
                this.selectedProject = proj;
                this.formPoNum = proj.po_spk_number || '';
                this.formPoDate = proj.po_spk_date ? proj.po_spk_date.split('T')[0] : '{{ date('Y-m-d') }}';
                this.formContractValue = proj.contract_value || 0;
                this.formBilling = proj.billing_terms || 'DP 30% setelah PO terbit, 50% setelah delivery barang on-site, 20% pelunasan setelah BAST selesai.';
                this.formSla = proj.sla_commitment || 'SLA Garansi Resmi Pabrik 1 Tahun, Layanan Support Jam Kerja (8x5) Response Time 4 Jam.';
                this.formCommercialTerms = proj.commercial_terms || 'Harga Franco Jakarta, Pembayaran Net 30 Hari (TOP 30) setelah invoice diterima.';
                this.formSpecialCommitment = proj.special_commitment || 'Termasuk pendampingan User Acceptance Testing (UAT) dan transfer knowledge 1 hari.';
                this.formExclusions = proj.exclusions || 'Pengadaan kabel, rack, atau perangkat tambahan di luar BOM terlampir dikenakan PO terpisah.';
                this.isHandoverModalOpen = true;
            }
        }
    }
</script>
@endsection
