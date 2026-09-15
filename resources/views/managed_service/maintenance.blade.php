@extends('layouts.app')

@section('title', 'Preventive Maintenance - Managed Service')

@section('content')
<div class="flex h-screen overflow-hidden" x-data="maintenanceManager()" x-cloak>
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto bg-[#FAF9F8]">
        @include('components.topbar', ['title' => 'Preventive Maintenance & Checklist'])
        
        <div class="p-4 sm:p-5 lg:p-[26px] animate-fade-in space-y-6">
            
            {{-- Top Action Bar & Filter --}}
            <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4">
                <div>
                    <h3 class="font-bold text-gray-900 text-sm">Jadwal Pemeliharaan Berkala (Preventive Maintenance)</h3>
                    <p class="text-xs text-gray-400">Inspeksi fisik, pembersihan filter, backup konfigurasi, & uji redundansi</p>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('schedules.index') }}" class="px-4 py-2 bg-[#C81E2C] hover:brightness-105 active:translate-y-[1px] text-white text-xs font-bold rounded-xl shadow-[0_4px_12px_rgba(200,30,44,0.2)] transition inline-flex items-center gap-1.5 whitespace-nowrap">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Buat Jadwal Kunjungan
                    </a>
                </div>
            </div>

            {{-- Checklist Standar PM Card & Schedules Grid --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                {{-- Left 2 Cols: Jadwal Kunjungan & History --}}
                <div class="lg:col-span-2 space-y-4">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-5 border-b border-gray-100">
                            <h4 class="font-bold text-gray-900 text-sm">Agenda Kunjungan Maintenance Rutin Terjadwal</h4>
                        </div>

                        <div class="divide-y divide-gray-100">
                            @forelse($schedules as $sch)
                                <div class="p-4 hover:bg-gray-50/60 transition flex items-start justify-between gap-4 text-xs">
                                    <div class="space-y-1">
                                        <div class="font-bold text-gray-900 text-sm">{{ $sch->title }}</div>
                                        <div class="text-gray-500 font-medium flex items-center gap-2">
                                            <span>{{ $sch->project ? $sch->project->name : ($sch->client_name ?: 'Klien Regular') }}</span>
                                            <span>• Lokasi: <strong>{{ $sch->location ?: 'On-Site' }}</strong></span>
                                        </div>
                                        <div class="text-gray-400 text-[11px]">
                                            Teknisi Ditugaskan: <strong class="text-[#AF1424]">{{ $sch->users->pluck('name')->implode(', ') ?: 'Doris / Tim Maintenance' }}</strong>
                                        </div>
                                    </div>

                                    <div class="text-right whitespace-nowrap space-y-1">
                                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700">
                                            {{ $sch->date ? $sch->date->format('d M Y') : 'Terjadwal' }}
                                        </span>
                                        <div class="text-[11px] text-gray-400">{{ $sch->start_time ?: '09:00' }} WIB</div>
                                    </div>
                                </div>
                            @empty
                                <div class="py-12 text-center text-gray-400 text-xs">
                                    Belum ada agenda kunjungan maintenance terjadwal.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Right 1 Col: Checklist SOP Preventive Maintenance --}}
                <div class="space-y-4">
                    <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 space-y-4 text-xs">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 font-bold flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900">SOP Standar Kunjungan PM</h4>
                                <p class="text-[10.5px] text-gray-400">Parameter verifikasi teknisi di lapangan</p>
                            </div>
                        </div>

                        <div class="space-y-2.5">
                            <div class="p-2.5 rounded-xl bg-gray-50 border border-gray-200/70 space-y-1">
                                <div class="font-bold text-gray-800">1. Inspeksi Fisik & Lingkungan</div>
                                <p class="text-[11px] text-gray-500">Pembersihan debu filter rack, pengecekan suhu ruang DC (18–22°C), dan kabel power redundancy.</p>
                            </div>

                            <div class="p-2.5 rounded-xl bg-gray-50 border border-gray-200/70 space-y-1">
                                <div class="font-bold text-gray-800">2. Backup Konfigurasi (Running-Config)</div>
                                <p class="text-[11px] text-gray-500">Ekspor running configuration seluruh switch, firewall, dan router ke server backup internal.</p>
                            </div>

                            <div class="p-2.5 rounded-xl bg-gray-50 border border-gray-200/70 space-y-1">
                                <div class="font-bold text-gray-800">3. Uji Redundansi & Failover</div>
                                <p class="text-[11px] text-gray-500">Verifikasi status HA (High Availability) cluster FortiGate & Cisco StackWise link.</p>
                            </div>

                            <div class="p-2.5 rounded-xl bg-gray-50 border border-gray-200/70 space-y-1">
                                <div class="font-bold text-gray-800">4. Penandatanganan Berita Acara PM</div>
                                <p class="text-[11px] text-gray-500">Laporan hasil inspeksi ditandatangani oleh PIC IT klien sebelum meninggalkan site.</p>
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
