@extends('layouts.app')

@section('title', 'Serah Terima Proyek - Sales Portal')

@push('styles')
<style>
    :root {
        --ipnet-primary: #8F0A0D;
        --ipnet-primary-hover: #73080A;
        --ipnet-card-bg: #FFFFFF;
        --ipnet-card-border: #E2E8F0;
        --ipnet-text-main: #1E293B;
    }

    .ipnet-card {
        background-color: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04), 0 4px 12px rgba(0, 0, 0, 0.02);
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .ipnet-card:hover {
        border-color: #CBD5E1;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
    }

    .btn-ipnet-primary {
        background: linear-gradient(135deg, #8F0A0D 0%, #B81525 100%);
        color: #FFFFFF;
        font-weight: 700;
        transition: all 0.2s ease;
        box-shadow: 0 2px 4px rgba(143, 10, 13, 0.15);
    }

    .btn-ipnet-primary:hover {
        background: linear-gradient(135deg, #7A080B 0%, #9E0E1D 100%);
        box-shadow: 0 6px 16px rgba(143, 10, 13, 0.28);
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
<div class="flex h-screen overflow-hidden bg-[#F8FAFC] font-sans" x-data="commercialHandoverPage()">
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Serah Terima Proyek'])
        
        <div class="p-4 sm:p-6 lg:p-7 space-y-6 max-w-[1680px] mx-auto animate-fade-in">
            
            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 font-bold px-2">✕</button>
                </div>
            @endif

            <!-- Filter & Action Bar -->
            <div class="flex flex-col sm:flex-row sm:flex-wrap sm:justify-between sm:items-center gap-3 anim-fade-up anim-delay-1">
                <form method="GET" action="{{ route('sales.handover.index') }}" class="flex flex-col sm:flex-row flex-wrap gap-2.5 w-full sm:w-auto">
                    <div class="relative">
                        <svg class="w-4 h-4 absolute left-3.5 top-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" 
                               name="search" 
                               value="{{ $search }}"
                               placeholder="Cari proyek, klien, nomor PO/SPK..." 
                               class="w-full sm:w-72 px-3.5 py-2 pl-9 rounded-xl border border-gray-200 text-xs font-semibold text-gray-800 bg-white focus:outline-none focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D] transition-all shadow-xs">
                    </div>

                    <select name="status" onchange="this.form.submit()" 
                            class="w-full sm:w-52 px-3.5 py-2 rounded-xl border border-gray-200 text-xs font-semibold text-gray-800 bg-white focus:outline-none focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D] transition-all cursor-pointer shadow-xs">
                        <option value="">Semua Status Handover</option>
                        <option value="Draft" {{ $filterStatus == 'Draft' ? 'selected' : '' }}>Draft Handover</option>
                        <option value="Submitted" {{ $filterStatus == 'Submitted' ? 'selected' : '' }}>Submitted ke Delivery</option>
                        <option value="Approved" {{ $filterStatus == 'Approved' ? 'selected' : '' }}>Approved by PMO</option>
                    </select>

                    @if($search || $filterStatus)
                        <a href="{{ route('sales.handover.index') }}" 
                           class="px-3 py-2 text-xs font-bold text-gray-500 hover:text-gray-800 self-center">
                            Reset Filter
                        </a>
                    @endif
                </form>
            </div>

            {{-- Table Deals Handover --}}
            <div class="ipnet-card overflow-hidden anim-fade-up anim-delay-2">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-gray-50 text-gray-500 uppercase text-[10.5px] font-bold">
                            <tr>
                                <th class="py-3 px-4 rounded-l-lg">Proyek &amp; Klien</th>
                                <th class="py-3 px-4">Nomor &amp; Tanggal PO/SPK</th>
                                <th class="py-3 px-4 text-right">Nilai Kontrak</th>
                                <th class="py-3 px-4">Billing &amp; SLA Terms</th>
                                <th class="py-3 px-4 text-center">Status Handover</th>
                                <th class="py-3 px-4 rounded-r-lg text-right">Aksi</th>
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
                                        <div class="font-black text-gray-900">
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
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10.5px] font-bold" 
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
                                                    class="px-2.5 py-1 bg-white hover:bg-red-50 text-[#8F0A0D] text-[11px] font-bold rounded-lg border border-red-200 shadow-xs hover:border-[#8F0A0D] transition cursor-pointer">
                                                {{ $hp->commercial_handover_status === 'Submitted' || $hp->commercial_handover_status === 'Approved' ? 'Edit' : 'Serah Terima' }}
                                            </button>
                                            <a href="{{ route('projects.show', $hp->id) }}" 
                                               class="px-2.5 py-1 bg-white hover:bg-gray-50 text-gray-700 text-[11px] font-semibold rounded-lg border border-gray-200 shadow-xs transition cursor-pointer">
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
                    <div class="p-4 border-t border-gray-100 bg-[#F8FAFC]">
                        {{ $handoverProjects->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>

    {{-- MODAL COMMERCIAL HANDOVER SUBMISSION --}}
    <template x-teleport="body">
        <div x-show="isHandoverModalOpen" 
             x-cloak 
             class="fixed inset-0 z-50 bg-[#0F172A]/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-5"
             @click.self="isHandoverModalOpen = false">
            <div class="bg-white rounded-2xl w-[780px] max-w-full max-h-[90vh] flex flex-col overflow-hidden shadow-2xl border border-[#E2E8F0] my-auto anim-fade-up">
                
                {{-- Fixed Header --}}
                <div class="flex items-center justify-between border-b border-[#E2E8F0] p-5 sm:p-6 pb-4 shrink-0 bg-white">
                    <div>
                        <p class="text-[#8F0A0D] text-[11px] font-bold uppercase tracking-wider">Commercial Handover</p>
                        <h3 class="text-[16px] font-bold text-[#1E293B]" x-text="'Handover: ' + (selectedProject.name || '')"></h3>
                    </div>
                    <button type="button" @click="isHandoverModalOpen = false" class="text-[#94A3B8] hover:text-[#1E293B] p-1.5 rounded-lg hover:bg-[#F1F5F9] transition cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Form Body with Scrollable Area --}}
                <form id="commercialHandoverForm" 
                      :action="'/sales/handover/' + selectedProject.id + '/submit'" 
                      method="POST" 
                      enctype="multipart/form-data" 
                      class="flex flex-col flex-1 min-h-0 overflow-hidden">
                    @csrf

                    <div class="p-5 sm:p-6 overflow-y-auto space-y-4 text-[12.5px] flex-1">
                        
                        {{-- Ringkasan Peluang --}}
                        <div class="p-3.5 bg-[#F8FAFC] rounded-xl border border-[#E2E8F0] flex flex-wrap items-center justify-between gap-2 text-xs">
                            <div>
                                <span class="text-[#64748B] block font-medium">Klien / Instansi:</span>
                                <span class="font-bold text-[#1E293B] text-[13px]" x-text="selectedProject.client || '—'"></span>
                            </div>
                            <div class="text-right">
                                <span class="text-[#64748B] block font-medium">Sales Commercial PIC:</span>
                                <span class="font-bold text-[#8F0A0D] text-[13px]">{{ auth()->user()->name }}</span>
                            </div>
                        </div>

                        {{-- Dokumen PO & Tanggal --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <div>
                                <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">
                                    Nomor PO / SPK <span class="text-[#8F0A0D]">*</span>
                                </label>
                                <input type="text" name="po_spk_number" x-model="formPoNum" placeholder="Contoh: PO-CLI/2026/09/001" required
                                       class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition">
                            </div>

                            <div>
                                <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">
                                    Tanggal PO / SPK <span class="text-[#8F0A0D]">*</span>
                                </label>
                                <input type="date" name="po_spk_date" x-model="formPoDate" required
                                       class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition cursor-pointer">
                            </div>
                        </div>

                        {{-- Nilai Final & Upload PO --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <div>
                                <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">
                                    Nilai Final Kontrak (Rp) <span class="text-[#8F0A0D]">*</span>
                                </label>
                                <input type="number" name="contract_value" x-model="formContractValue" min="0" step="1000" required
                                       class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-bold text-[#1E293B] focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition">
                            </div>

                            <div>
                                <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">
                                    Upload PO / SPK (PDF/ZIP)
                                </label>
                                <input type="file" name="po_spk_file" accept=".pdf,.docx,.xlsx,.zip,.rar"
                                       class="w-full px-3 py-1.5 bg-white border border-[#CBD5E1] rounded-xl text-[11px] text-[#64748B] file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[11px] file:font-bold file:bg-[#8F0A0D] file:text-white hover:file:bg-[#73080A] cursor-pointer">
                            </div>
                        </div>

                        {{-- Termin Pembayaran --}}
                        <div>
                            <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">
                                Billing Terms (Termin Pembayaran) <span class="text-[#8F0A0D]">*</span>
                            </label>
                            <textarea name="billing_terms" x-model="formBilling" rows="2" placeholder="Contoh: DP 30% setelah PO terbit, 50% setelah delivery hardware, 20% setelah UAT & BAST selesai." required
                                      class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition resize-none"></textarea>
                        </div>

                        {{-- Ketentuan Garansi & Komersial --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <div>
                                <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">
                                    SLA &amp; Garansi
                                </label>
                                <input type="text" name="sla_commitment" x-model="formSla" placeholder="Garansi Resmi Prinsipal 1 Tahun, Support 8x5"
                                       class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition">
                            </div>

                            <div>
                                <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">
                                    Commercial Terms
                                </label>
                                <input type="text" name="commercial_terms" x-model="formCommercialTerms" placeholder="Franco Jakarta, TOP 30 Hari"
                                       class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition">
                            </div>
                        </div>

                        {{-- Komitmen Khusus & Exclusions --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <div>
                                <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">
                                    Komitmen Khusus
                                </label>
                                <textarea name="special_commitment" x-model="formSpecialCommitment" rows="2.5" placeholder="Termasuk pendampingan User Acceptance Testing (UAT) dan transfer knowledge..."
                                          class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition resize-none"></textarea>
                            </div>

                            <div>
                                <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">
                                    Exclusions (Luar Scope)
                                </label>
                                <textarea name="exclusions" x-model="formExclusions" rows="2.5" placeholder="Pengadaan kabel, rack, atau perangkat tambahan di luar BoQ terlampir dikenakan PO terpisah..."
                                          class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition resize-none"></textarea>
                            </div>
                        </div>

                    </div>

                    {{-- Fixed Footer --}}
                    <div class="flex items-center justify-end gap-2.5 p-4 px-6 border-t border-[#E2E8F0] bg-[#F8FAFC] shrink-0">
                        <button type="button" 
                                @click="isHandoverModalOpen = false" 
                                class="px-4 py-2.5 text-[12.5px] font-bold text-[#475569] hover:bg-[#F1F5F9] border border-[#CBD5E1] rounded-xl transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" 
                                class="px-5 py-2.5 text-[12.5px] font-bold text-white bg-[#8F0A0D] hover:bg-[#73080A] rounded-xl shadow-md transition cursor-pointer">
                            Kirim Handover ke PMO
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </template>

</div>
@endsection

@push('scripts')
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
@endpush
