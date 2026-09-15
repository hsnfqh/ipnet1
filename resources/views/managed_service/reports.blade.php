@extends('layouts.app')

@section('title', 'Laporan Layanan & SLA - Managed Service')

@section('content')
<div class="flex h-screen overflow-hidden" x-data="reportManager()" x-cloak>
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto bg-[#FAF9F8]">
        @include('components.topbar', ['title' => 'Laporan Layanan & SLA Kinerja'])
        
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
                <div>
                    <h3 class="font-bold text-gray-900 text-sm">Dokumen Laporan Managed Service & Kepatuhan SLA</h3>
                    <p class="text-xs text-gray-400">Laporan Preventive Maintenance, SLA Review, Incident Post-Mortem, & Service Activation</p>
                </div>

                <div class="flex items-center gap-3">
                    <button @click="isCreateModalOpen = true" class="px-4 py-2 bg-[#C81E2C] hover:brightness-105 active:translate-y-[1px] text-white text-xs font-bold rounded-xl shadow-[0_4px_12px_rgba(200,30,44,0.2)] transition inline-flex items-center gap-1.5 whitespace-nowrap">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Terbitkan Laporan Baru
                    </button>
                </div>
            </div>

            {{-- Reports Grid / Table --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-gray-100 bg-gray-50/50 text-[11px] font-bold tracking-wider text-gray-500 uppercase">
                                <th class="py-4 px-6">JUDUL LAPORAN & KLIEN</th>
                                <th class="py-4 px-6">TIPE DOKUMEN</th>
                                <th class="py-4 px-6">PERIODE</th>
                                <th class="py-4 px-6">SKOR SLA</th>
                                <th class="py-4 px-6">STATUS</th>
                                <th class="py-4 px-6 text-right">AKSI</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @forelse($reports as $rep)
                                <tr class="hover:bg-gray-50/60 transition-colors">
                                    <td class="py-4 px-6">
                                        <div class="font-bold text-gray-900">{{ $rep->title }}</div>
                                        <div class="text-xs text-gray-500 font-medium flex items-center gap-1.5 mt-0.5">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[#C81E2C]"></span>
                                            <span>{{ $rep->client_name }}</span>
                                        </div>
                                    </td>
                                    <td class="py-4 px-6 whitespace-nowrap">
                                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700">
                                            {{ $rep->report_type }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 whitespace-nowrap text-xs font-semibold text-gray-700">
                                        {{ $rep->period_month }} {{ $rep->period_year }}
                                    </td>
                                    <td class="py-4 px-6 whitespace-nowrap text-xs font-bold text-emerald-600">
                                        {{ $rep->sla_score }}%
                                    </td>
                                    <td class="py-4 px-6 whitespace-nowrap text-xs">
                                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-bold 
                                            {{ $rep->status === 'Approved by Client' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-gray-100 text-gray-700' }}">
                                            {{ $rep->status }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 text-right whitespace-nowrap">
                                        <button class="px-3 py-1.5 bg-gray-50 hover:bg-gray-100 text-gray-700 text-xs font-bold rounded-lg border border-gray-200 transition inline-flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            Lihat Ringkasan
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-12 text-center text-gray-400 text-xs">
                                        Belum ada dokumen laporan yang diterbitkan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($reports->hasPages())
                    <div class="p-4 border-t border-gray-100">
                        {{ $reports->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>

    {{-- MODAL CREATE REPORT --}}
    <template x-teleport="body">
        <div x-show="isCreateModalOpen" class="fixed inset-0 z-50 bg-[#0E0D12]/60 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4" @click.away="isCreateModalOpen = false">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <h3 class="text-base font-bold text-gray-900">Terbitkan Laporan Managed Service</h3>
                    <button @click="isCreateModalOpen = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form action="{{ route('ms.reports.store') }}" method="POST" class="space-y-3 text-xs">
                    @csrf
                    <div>
                        <label class="block font-bold text-gray-700 uppercase mb-1">Nama Instansi / Klien *</label>
                        <input type="text" name="client_name" required placeholder="Contoh: Bank Mandiri, PT Telkom"
                               class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-red-500/20">
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 uppercase mb-1">Judul Laporan *</label>
                        <input type="text" name="title" required placeholder="Contoh: Laporan Bulanan SLA Availability Q3 2026"
                               class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-red-500/20">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-gray-700 uppercase mb-1">Tipe Laporan *</label>
                            <select name="report_type" class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs">
                                <option value="Preventive Maintenance">Preventive Maintenance</option>
                                <option value="SLA Review">SLA Review</option>
                                <option value="Incident Post-Mortem">Incident Post-Mortem</option>
                                <option value="Service Activation">Service Activation</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-bold text-gray-700 uppercase mb-1">Skor Kepatuhan SLA (%) *</label>
                            <input type="number" step="0.01" name="sla_score" value="99.9" required
                                   class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-gray-700 uppercase mb-1">Bulan Periode *</label>
                            <select name="period_month" class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs">
                                @foreach(['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'] as $m)
                                    <option value="{{ $m }}" {{ $m === 'September' ? 'selected' : '' }}>{{ $m }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block font-bold text-gray-700 uppercase mb-1">Tahun *</label>
                            <input type="number" name="period_year" value="2026" required
                                   class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs">
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 uppercase mb-1">Ringkasan Eksekutif</label>
                        <textarea name="summary" rows="3" placeholder="Tuliskan poin penting hasil pemeliharaan dan rekomendasi perbaikan..."
                                  class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100">
                        <button type="button" @click="isCreateModalOpen = false" class="px-4 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-100 rounded-xl transition">Batal</button>
                        <button type="submit" class="px-5 py-2 text-xs font-bold text-white bg-[#C81E2C] hover:brightness-105 rounded-xl shadow-sm transition">
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
