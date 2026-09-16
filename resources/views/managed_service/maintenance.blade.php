@extends('layouts.app')

@section('title', 'Preventive Maintenance - Managed Service')

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
<div class="flex h-screen overflow-hidden bg-[#F8FAFC] font-sans" x-data="maintenanceManager()" x-cloak>
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Preventive Maintenance & Checklist'])
        
        <div class="p-4 sm:p-6 lg:p-7 space-y-6 max-w-[1600px] mx-auto animate-fade-in">
            
            {{-- Top Action Bar & Filter --}}
            <div class="ipnet-card p-5 sm:p-6 flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4">
                <div>
                    <p class="text-[#8F0A0D] text-[11px] font-bold uppercase tracking-wider">Pemeliharaan Berkala</p>
                    <h3 class="font-bold text-[#1E293B] text-[16px] mt-0.5">Jadwal Pemeliharaan Berkala (Preventive Maintenance)</h3>
                    <p class="text-[12.5px] text-[#64748B] mt-0.5">Inspeksi fisik, pembersihan filter, backup konfigurasi, & uji redundansi perangkat</p>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('schedules.index') }}" class="btn-ipnet-gradient px-4 py-2.5 rounded-xl font-bold text-[12.5px] flex items-center gap-2 cursor-pointer shadow-md">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        <span>Buat Jadwal Kunjungan</span>
                    </a>
                </div>
            </div>

            {{-- Checklist Standar PM Card & Schedules Grid --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                {{-- Left 2 Cols: Jadwal Kunjungan & History --}}
                <div class="lg:col-span-2 space-y-4">
                    <div class="ipnet-card overflow-hidden">
                        <div class="p-5 sm:px-6 sm:py-4.5 border-b border-[#E2E8F0]">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-[#8F0A0D]"></span>
                                <h4 class="font-bold text-[#1E293B] text-[15px]">Agenda Kunjungan Maintenance Rutin Terjadwal</h4>
                            </div>
                        </div>

                        <div class="divide-y divide-[#F1F5F9]">
                            @forelse($schedules as $sch)
                                <div class="p-4 sm:p-5 hover:bg-[#F8FAFC] transition flex items-start justify-between gap-4 text-[12.5px]">
                                    <div class="space-y-1.5 flex-1 min-w-0">
                                        <div class="font-bold text-[#1E293B] text-[14px]">{{ $sch->title }}</div>
                                        <div class="text-[#64748B] font-semibold flex items-center gap-2 flex-wrap">
                                            <span class="text-[#334155]">{{ $sch->project ? $sch->project->name : ($sch->client_name ?: 'Klien Regular') }}</span>
                                            <span>&bull; Lokasi: <strong class="text-[#334155]">{{ $sch->location ?: 'On-Site' }}</strong></span>
                                        </div>
                                        <div class="text-[#94A3B8] text-[11.5px]">
                                            Teknisi Ditugaskan: <strong class="text-[#8F0A0D] bg-[#FEF2F2] px-1.5 py-0.5 rounded border border-[#FECACA]">{{ $sch->users->pluck('name')->implode(', ') ?: 'Doris / Tim Maintenance' }}</strong>
                                        </div>
                                    </div>

                                    <div class="text-right whitespace-nowrap space-y-1 flex-shrink-0">
                                        <span class="px-2.5 py-1 rounded-full text-[11.5px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                            {{ $sch->date ? $sch->date->format('d M Y') : 'Terjadwal' }}
                                        </span>
                                        <div class="text-[11px] text-[#64748B] font-semibold">{{ $sch->start_time ?: '09:00' }} WIB</div>
                                    </div>
                                </div>
                            @empty
                                <div class="py-12 text-center text-[#64748B] text-[13px]">
                                    Belum ada agenda kunjungan maintenance terjadwal.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Right 1 Col: Checklist SOP Preventive Maintenance --}}
                <div class="space-y-4">
                    <div class="ipnet-card p-5 sm:p-6 space-y-4 text-[12.5px]">
                        <div class="flex items-center gap-2.5 pb-3 border-b border-[#E2E8F0]">
                            <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 font-bold flex items-center justify-center border border-emerald-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-[#1E293B] text-[14px]">SOP Standar Kunjungan PM</h4>
                                <p class="text-[11px] text-[#64748B]">Parameter verifikasi teknisi di lapangan</p>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <div class="p-3 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0] space-y-1">
                                <div class="font-bold text-[#1E293B]">1. Inspeksi Fisik & Lingkungan</div>
                                <p class="text-[11.5px] text-[#64748B]">Pembersihan debu filter rack, pengecekan suhu ruang DC (18–22°C), dan kabel power redundancy.</p>
                            </div>

                            <div class="p-3 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0] space-y-1">
                                <div class="font-bold text-[#1E293B]">2. Backup Konfigurasi (Running-Config)</div>
                                <p class="text-[11.5px] text-[#64748B]">Ekspor running configuration seluruh switch, firewall, dan router ke server backup internal.</p>
                            </div>

                            <div class="p-3 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0] space-y-1">
                                <div class="font-bold text-[#1E293B]">3. Uji Redundansi & Failover</div>
                                <p class="text-[11.5px] text-[#64748B]">Verifikasi status HA (High Availability) cluster FortiGate & Cisco StackWise link.</p>
                            </div>

                            <div class="p-3 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0] space-y-1">
                                <div class="font-bold text-[#1E293B]">4. Penandatanganan Berita Acara PM</div>
                                <p class="text-[11.5px] text-[#64748B]">Laporan hasil inspeksi ditandatangani oleh PIC IT klien sebelum meninggalkan site.</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>

<script>
    function maintenanceManager() {
        return {}
    }
</script>
@endsection
