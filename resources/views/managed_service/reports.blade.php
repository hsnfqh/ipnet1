@extends('layouts.app')

@section('title', 'Laporan Layanan & SLA - Managed Service')

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
<div class="flex h-screen overflow-hidden bg-[#F8FAFC] font-sans" x-data="reportManager()" x-cloak>
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Laporan Layanan & SLA Kinerja'])
        
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
            <!-- SECTION HEADER & ACTIONS                                   -->
            <!-- ========================================================== -->
            <div class="ipnet-card p-5 sm:p-6 anim-fade-up anim-delay-1">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <div>
                        <p class="text-[#8F0A0D] text-[12px] font-bold inline-flex items-center uppercase tracking-wider">
                            <span class="w-2 h-2 rounded-full bg-[#8F0A0D] inline-block mr-2"></span> DOKUMEN & LAPORAN BULANAN
                        </p>
                        <h2 class="text-[20px] font-bold text-[#1E293B] tracking-tight mt-0.5">Laporan Kinerja Managed Service & SLA</h2>
                        <p class="text-[13px] text-[#64748B] mt-0.5">Laporan Preventive Maintenance, SLA Review, Incident Post-Mortem, & Service Activation</p>
                    </div>

                    <div class="flex items-center gap-3 shrink-0">
                        <div class="px-3.5 py-1.5 rounded-full bg-[#F8FAFC] border border-[#E2E8F0] text-[12px] font-bold text-[#1E293B] flex items-center gap-1.5 shadow-xs">
                            <span class="text-[#64748B]">Total Dokumen:</span>
                            <span class="text-[#8F0A0D] font-extrabold">{{ $reports->count() }}</span>
                        </div>

                        <button @click="isCreateModalOpen = true" class="btn-ipnet-gradient px-4 py-2.5 rounded-xl font-bold text-[13px] flex items-center gap-2 cursor-pointer shadow-md">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                            <span>Terbitkan Laporan Baru</span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Reports Grid / Table --}}
            <div class="ipnet-card overflow-hidden anim-fade-up anim-delay-2">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-[#E2E8F0] bg-[#F8FAFC] text-[11px] font-extrabold tracking-wider text-[#64748B] uppercase">
                                <th class="py-3.5 px-5">JUDUL LAPORAN & KLIEN</th>
                                <th class="py-3.5 px-5">TIPE DOKUMEN</th>
                                <th class="py-3.5 px-5">PERIODE</th>
                                <th class="py-3.5 px-5">SKOR SLA</th>
                                <th class="py-3.5 px-5">STATUS</th>
                                <th class="py-3.5 px-5 text-right">AKSI</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#F1F5F9] text-[13px]">
                            @forelse($reports as $rep)
                                <tr class="hover:bg-[#F8FAFC] transition-colors">
                                    <td class="py-4 px-5">
                                        <div class="font-bold text-[#1E293B]">{{ $rep->title }}</div>
                                        <div class="text-[12px] text-[#64748B] font-semibold flex items-center gap-1.5 mt-0.5">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[#8F0A0D]"></span>
                                            <span>{{ $rep->client_name }}</span>
                                        </div>
                                    </td>
                                    <td class="py-4 px-5 whitespace-nowrap">
                                        <span class="inline-flex px-2.5 py-1 rounded-full text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                            {{ $rep->report_type }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-5 whitespace-nowrap text-[12px] font-bold text-[#475569]">
                                        {{ $rep->period_month }} {{ $rep->period_year }}
                                    </td>
                                    <td class="py-4 px-5 whitespace-nowrap text-[13px] font-extrabold text-emerald-600">
                                        {{ $rep->sla_score }}%
                                    </td>
                                    <td class="py-4 px-5 whitespace-nowrap text-[12px]">
                                        <span class="inline-flex px-2.5 py-1 rounded-full text-[11px] font-bold 
                                            {{ $rep->status === 'Approved by Client' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-[#F1F5F9] text-[#475569] border border-[#CBD5E1]' }}">
                                            {{ $rep->status }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-5 text-right whitespace-nowrap">
                                        <button class="px-3 py-1 bg-white hover:bg-[#F8FAFC] text-[#1E293B] text-[12px] font-bold rounded-lg border border-[#CBD5E1] transition shadow-2xs inline-flex items-center gap-1 cursor-pointer">
                                            <svg class="w-3.5 h-3.5 text-[#64748B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            <span>Lihat</span>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-12 text-center text-[#94A3B8] text-[13px]">
                                        Belum ada dokumen laporan yang diterbitkan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($reports->hasPages())
                    <div class="p-4 border-t border-[#E2E8F0] bg-white">
                        {{ $reports->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>

    {{-- MODAL CREATE REPORT --}}
    <template x-teleport="body">
        <div x-show="isCreateModalOpen" 
             x-cloak
             class="fixed inset-0 z-50 bg-[#0F172A]/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-lg w-full shadow-2xl border border-[#E2E8F0] max-h-[90vh] flex flex-col overflow-hidden" @click.away="isCreateModalOpen = false">
                <!-- Fixed Header -->
                <div class="flex items-center justify-between border-b border-[#E2E8F0] p-5 sm:p-6 pb-4 shrink-0 bg-white">
                    <div>
                        <p class="text-[#8F0A0D] text-[11px] font-bold uppercase tracking-wider">Laporan Baru</p>
                        <h3 class="text-[16px] font-bold text-[#1E293B]">Terbitkan Laporan Managed Service</h3>
                    </div>
                    <button @click="isCreateModalOpen = false" class="text-[#94A3B8] hover:text-[#1E293B] p-1.5 rounded-lg hover:bg-[#F1F5F9] transition cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Form with Scrollable Body & Fixed Footer -->
                <form action="{{ route('ms.reports.store') }}" method="POST" class="flex flex-col flex-1 min-h-0 overflow-hidden">
                    @csrf
                    <div class="p-5 sm:p-6 overflow-y-auto space-y-3.5 text-[12.5px] flex-1">
                        <div>
                            <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">Nama Instansi / Klien <span class="text-[#8F0A0D]">*</span></label>
                            <input type="text" name="client_name" required placeholder="Contoh: Bank Mandiri, PT Telkom"
                                   class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition">
                        </div>

                        <div>
                            <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">Judul Laporan <span class="text-[#8F0A0D]">*</span></label>
                            <input type="text" name="title" required placeholder="Contoh: Laporan Bulanan SLA Availability Q3 2026"
                                   class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <div>
                                <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">Tipe Laporan <span class="text-[#8F0A0D]">*</span></label>
                                <select name="report_type" class="w-full px-3.5 py-2.5 bg-white border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition cursor-pointer">
                                    <option value="Preventive Maintenance">Preventive Maintenance</option>
                                    <option value="SLA Review">SLA Review</option>
                                    <option value="Incident Post-Mortem">Incident Post-Mortem</option>
                                    <option value="Service Activation">Service Activation</option>
                                </select>
                            </div>
                            <div>
                                <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">Skor Kepatuhan SLA (%) <span class="text-[#8F0A0D]">*</span></label>
                                <input type="number" step="0.01" name="sla_score" value="99.9" required
                                       class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <div>
                                <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">Bulan Periode <span class="text-[#8F0A0D]">*</span></label>
                                <select name="period_month" class="w-full px-3.5 py-2.5 bg-white border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition cursor-pointer">
                                    @foreach(['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'] as $m)
                                        <option value="{{ $m }}" {{ $m === 'September' ? 'selected' : '' }}>{{ $m }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">Tahun <span class="text-[#8F0A0D]">*</span></label>
                                <input type="number" name="period_year" value="2026" required
                                       class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition">
                            </div>
                        </div>

                        <div>
                            <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">Ringkasan Eksekutif</label>
                            <textarea name="summary" rows="3" placeholder="Tuliskan poin penting hasil pemeliharaan dan rekomendasi perbaikan..."
                                      class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition resize-none"></textarea>
                        </div>
                    </div>

                    <!-- Fixed Footer -->
                    <div class="flex items-center justify-end gap-2.5 p-4 px-6 border-t border-[#E2E8F0] bg-[#F8FAFC] shrink-0">
                        <button type="button" @click="isCreateModalOpen = false" class="px-4 py-2.5 text-[12.5px] font-bold text-[#475569] hover:bg-[#F1F5F9] border border-[#CBD5E1] rounded-xl transition cursor-pointer">Batal</button>
                        <button type="submit" class="btn-ipnet-gradient px-5 py-2.5 text-[12.5px] font-bold rounded-xl shadow-md cursor-pointer">
                            Terbitkan Laporan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>
</div>

<script>
    function reportManager() {
        return {
            isCreateModalOpen: false,
        }
    }
</script>
@endsection
